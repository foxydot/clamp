<?php
/*
Plugin Name: MSDLAB IPN Fixit Kit
*/
function this_plugin_first() {
    // ensure path to this file is via main wp plugin path
    $wp_path_to_this_file = preg_replace('/(.*)plugins\/(.*)$/', WP_PLUGIN_DIR."/$2", __FILE__);
    $this_plugin = plugin_basename(trim($wp_path_to_this_file));
    $active_plugins = get_option('active_plugins');
    $this_plugin_key = array_search($this_plugin, $active_plugins);
    if ($this_plugin_key) { // if it's 0 it's the first plugin already, no need to continue
        array_splice($active_plugins, $this_plugin_key, 1);
        array_unshift($active_plugins, $this_plugin);
        update_option('active_plugins', $active_plugins);
    }
}
add_action("activated_plugin", "this_plugin_first");
add_shortcode('ipnfixit','msdlab_ipn_fixit');
function msdlab_fixit_process_ipn($fakepost){
        global $fakepost;

        //Ignore requests that are not IPN
        if(RGForms::get("page") != "fixit_paypal_ipn")
            return;

        ts_data("IPN fixit request received. Starting to process...");
        //ts_data($fakepost);

        //Send request to paypal and verify it has not been spoofed
        //clearly these are all spoofed
        //if(!GFPayPal::verify_paypal_ipn()){
            //ts_data("IPN request could not be verified by PayPal. Aborting.");
            //return;
        //}
        //ts_data("IPN message successfully verified by PayPal");

        //Valid IPN requests must have a custom field
        $custom = mypost("custom");
        if(empty($custom)){
            ts_data("IPN request does not have a custom field, so it was not created by Gravity Forms. Aborting.");
            return;
        }

        //Getting entry associated with this IPN message (entry id is sent in the "custom" field)
        list($entry_id, $hash) = explode("|", $custom);
        $hash_matches = wp_hash($entry_id) == $hash;
        //Validates that Entry Id wasn't tampered with
        if(!mypost("test_ipn") && !$hash_matches){
            ts_data("Entry Id verification failed. Hash does not match. Custom field: {$custom}. Aborting.");
            return;
        }

        //ts_data("IPN message has a valid custom field: {$custom}");

        //$entry_id = mypost("custom");
        $entry = RGFormsModel::get_lead($entry_id);

        //Ignore orphan IPN messages (ones without an entry)
        if(!$entry){
            ts_data("Entry could not be found. Entry ID: {$entry_id}. Aborting.");
            return;
        }
        //ts_data("Entry has been found.");

        // config ID is stored in entry via send_to_paypal() function
        $config = GFPayPal::get_config_by_entry($entry);

        //Ignore IPN messages from forms that are no longer configured with the PayPal add-on
        if(!$config){
            ts_data("Form no longer is configured with PayPal Addon. Form ID: {$entry["form_id"]}. Aborting.");
            return;
        }
        //ts_data("Form {$entry["form_id"]} is properly configured.");

        //Only process test messages coming fron SandBox and only process production messages coming from production PayPal
        if( ($config["meta"]["mode"] == "test" && !mypost("test_ipn")) || ($config["meta"]["mode"] == "production" && mypost("test_ipn"))){
            ts_data("Invalid test/production mode. IPN message mode (test/production) does not match mode configured in the PayPal feed. Configured Mode: {$config["meta"]["mode"]}. IPN test mode: " . mypost("test_ipn"));
            return;
        }

        //Check business email to make sure it matches
        $recipient_email = mypost("receiver_email");
        if(strtolower(trim($recipient_email)) != strtolower(trim($config["meta"]["email"]))){
            ts_data("PayPal email does not match. Email entered on PayPal feed:" . strtolower(trim($config["meta"]["email"])) . " - Email from IPN message: " . $recipient_email);
            return;
        }

        //Pre IPN processing filter. Allows users to cancel IPN processing
        $cancel = apply_filters("gform_paypal_pre_ipn", false, $fakepost, $entry, $config);

        if(!$cancel) {
            ts_data("Setting payment status...");
            GFPayPal::set_payment_status($config, $entry, mypost("payment_status"), mypost("txn_type"), mypost("txn_id"), mypost("parent_txn_id"), mypost("subscr_id"), mypost("mc_gross"), mypost("pending_reason"), mypost("reason_code") );
        }
        else{
            ts_data("IPN processing cancelled by the gform_paypal_pre_ipn filter. Aborting.");
        }
        
        $updatedentry = RGFormsModel::get_lead($entry_id);
        ts_data($updatedentry);
        //ts_data("Before gform_paypal_post_ipn.");
        //Post IPN processing action
        do_action("gform_paypal_post_ipn", $fakepost, $entry, $config, $cancel);

        ts_data("IPN processing complete.");
    }
    
function parse_csvfile($file,$delimiter=",",$indexrow=TRUE){
    global $csv_index;
    if($csv_handle = fopen($file, "rb")){
        while (($data = fgetcsv($csv_handle, 1000, $delimiter)) !== FALSE) {
            $csv_array[] = $data;
        }
        fclose($csv_handle);
    } 
    if($indexrow){
        //remove the title level from the csv
        $csv_index = array_shift($csv_array);
    }
    $i = 0;
    //get the categories and reorganize the array
    foreach($csv_array AS $csv_item){
        $j = 0;
        foreach($csv_item AS $csv_datum){
            if($indexrow){
                $list[$i][$csv_index[$j]] = $csv_datum;
            } else {
                $list[$i][$j] = $csv_datum;
            }
            $j++;
        }
        $i++;
    }
    return $list;
}

function msdlab_ipn_fixit(){
    global $fakepost;
    $csvfile = plugin_dir_path(__FILE__).'/BFCShoppingCarts.csv';
    $items = parse_csvfile($csvfile);
    foreach($items AS $fakepost){
        msdlab_fixit_process_ipn($fakepost);
    }
}

function mypost($name){
    global $fakepost;
        if(isset($fakepost[$name]))
            return $fakepost[$name];

        return "";
}
if(!function_exists('rgpost')){
function rgpost($name, $do_stripslashes=true){
    global $fakepost;
    if(isset($fakepost[$name]))
        return $do_stripslashes ? stripslashes_deep($fakepost[$name]) : $fakepost[$name];

    return "";
}
}