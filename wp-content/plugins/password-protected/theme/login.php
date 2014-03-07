<?php

/**
 * Based roughly on wp-login.php @revision 19414
 * http://core.trac.wordpress.org/browser/trunk/wp-login.php?rev=19414
 */

global $Password_Protected, $error, $is_iphone;

/**
 * WP Shake JS
 */
if ( ! function_exists( 'wp_shake_js' ) ) {
	function wp_shake_js() {
		global $is_iphone;
		if ( $is_iphone )
			return;
		?>
		<script type="text/javascript">
		addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
		function s(id,pos){g(id).left=pos+'px';}
		function g(id){return document.getElementById(id).style;}
		function shake(id,a,d){c=a.shift();s(id,c);if(a.length>0){setTimeout(function(){shake(id,a,d);},d);}else{try{g(id).position='static';wp_attempt_focus();}catch(e){}}}
		addLoadEvent(function(){ var p=new Array(15,30,15,0,-15,-30,-15,0);p=p.concat(p.concat(p));var i=document.forms[0].id;g(i).position='relative';shake(i,p,20);});
		</script>
		<?php
	}
}

nocache_headers();
header( 'Content-Type: ' . get_bloginfo( 'html_type' ) . '; charset=' . get_bloginfo( 'charset' ) );

// Set a cookie now to see if they are supported by the browser.
setcookie( TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN );
if ( SITECOOKIEPATH != COOKIEPATH )
	setcookie( TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN );

// If cookies are disabled we can't log in even with a valid password.
if ( isset( $_POST['testcookie'] ) && empty( $_COOKIE[TEST_COOKIE] ) )
	$Password_Protected->errors->add( 'test_cookie', __( "<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress.", 'password-protected' ) );

// Shake it!
$shake_error_codes = array( 'empty_password', 'incorrect_password' );
if ( $Password_Protected->errors->get_error_code() && in_array( $Password_Protected->errors->get_error_code(), $shake_error_codes ) )
	add_action( 'password_protected_login_head', 'wp_shake_js', 12 );

// Obey privacy setting
add_action( 'password_protected_login_head', 'noindex' );

/**
 * Support 3rd party plugins
 */
if ( class_exists( 'CWS_Login_Logo_Plugin' ) ) {
	// Add support for Mark Jaquith's Login Logo plugin
	// http://wordpress.org/extend/plugins/login-logo/
	add_action( 'password_protected_login_head', array( new CWS_Login_Logo_Plugin, 'login_head' ) );
} elseif ( class_exists( 'UberLoginLogo' ) ) {
	// Add support for Uber Login Logo plugin
	// http://wordpress.org/plugins/uber-login-logo/
	 add_action( 'password_protected_login_head', array( 'UberLoginLogo', 'replaceLoginLogo' ) );
}

?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
<meta http-equiv="Content-Type" content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>" />
<title><?php echo apply_filters( 'password_protected_wp_title', get_bloginfo( 'name' ) ); ?></title>
<meta name="description" content="We Are the People! We are the Power! We are the Change!" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/><meta name='robots' content='noindex,follow' />
<link rel="canonical" href="http://bfcclancamp.org/" />
<link rel='stylesheet' id='msdlab-genesis-child-theme-css'  href='http://bfcclancamp.org/wp-content/themes/heartstone/style.css?ver=2.0.1' type='text/css' media='all' />
<link rel='stylesheet' id='bootstrap-style-css'  href='//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.no-icons.min.css?ver=3.8.1' type='text/css' media='all' />
<link rel='stylesheet' id='font-awesome-style-css'  href='//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css?ver=3.8.1' type='text/css' media='all' />
<link rel='stylesheet' id='msd-style-css'  href='http://bfcclancamp.org/wp-content/themes/heartstone/lib/css/style.css?ver=3.8.1' type='text/css' media='all' />
<link rel='stylesheet' id='msd-homepage-style-css'  href='http://bfcclancamp.org/wp-content/themes/heartstone/lib/css/homepage.css?ver=3.8.1' type='text/css' media='all' />
<script type='text/javascript' src='http://bfcclancamp.org/wp-includes/js/jquery/jquery.js?ver=1.10.2'></script>
<script type='text/javascript' src='http://bfcclancamp.org/wp-includes/js/jquery/jquery-migrate.min.js?ver=1.2.1'></script>
<script type='text/javascript' src='//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js?ver=3.8.1'></script>
<script type='text/javascript' src='http://bfcclancamp.org/wp-content/themes/heartstone/lib/js/theme-jquery.js?ver=3.8.1'></script>
<script type='text/javascript' src='http://bfcclancamp.org/wp-content/themes/heartstone/lib/js/jquery.equal-height-columns.js?ver=3.8.1'></script>
<script type='text/javascript' src='http://bfcclancamp.org/wp-content/themes/heartstone/lib/js/homepage-jquery.js?ver=3.8.1'></script>

<!-- Bad Behavior 2.2.15 run time: 3.476 ms -->
<script type="text/javascript">
<!--
function bb2_addLoadEvent(func) {
    var oldonload = window.onload;
    if (typeof window.onload != 'function') {
        window.onload = func;
    } else {
        window.onload = function() {
            oldonload();
            func();
        }
    }
}

bb2_addLoadEvent(function() {
    for ( i=0; i < document.forms.length; i++ ) {
        if (document.forms[i].method == 'post') {
            var myElement = document.createElement('input');
            myElement.setAttribute('type', 'hidden');
            myElement.name = 'bb2_screener_';
            myElement.value = '1393734735 75.186.37.174';
            document.forms[i].appendChild(myElement);
        }
    }
});
// --></script>
        <link rel="Shortcut Icon" href="http://bfcclancamp.org/wp-content/themes/genesis/images/favicon.ico" type="image/x-icon" />
<!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<style type="text/css" id="custom-background-css">
body.custom-background { background-color: #000000; background-image: url('http://bfcclancamp.org/wp-content/uploads/2014/03/foggy_forest_2_2592.jpg'); background-repeat: no-repeat; background-position: top center; background-attachment: fixed; }
</style>
<?php
if ( $is_iphone ) {
    ?>
    <meta name="viewport" content="width=320; initial-scale=0.9; maximum-scale=1.0; user-scalable=0;" />
    <style type="text/css" media="screen">
    .login form, .login .message, #login_error { margin-left: 0px; }
    .login #nav, .login #backtoblog { margin-left: 8px; }
    .login h1 a { width: auto; }
    #login { padding: 20px 0; }
    </style>
    <?php
}

do_action( 'login_enqueue_scripts' );
do_action( 'password_protected_login_head' );
?>
</head>
<body class="login login-password-protected login-action-password-protected-login wp-core-ui page-template-default custom-background header-full-width full-width-content chrome home section-home" itemscope="itemscope" itemtype="http://schema.org/WebPage">
    <div class="site-container">
        <header class="site-header" role="banner" itemscope="itemscope" itemtype="http://schema.org/WPHeader">
            <div class="wrap"></div>
        </header>
        <div class="site-inner">
            <div class="content-sidebar-wrap">
                <main class="content" role="main" itemprop="mainContentOfPage">
                    <article class="post-1 page type-page status-publish entry" itemscope="itemscope" itemtype="http://schema.org/CreativeWork">
                        <div class="entry-content" itemprop="text">
                               <h1 style="color:#FFF;text-align: center"><?php bloginfo( 'name' ); ?></h1>
                           <p style="text-align: center">
<img width="300" height="291" src="http://bfcclancamp.org/wp-content/uploads/2014/03/Black-Forest-Clan-Design-Layout-with-all-layers-copy1-300x291.jpg" alt="Black Forest Clan Design Layout with all layers copy" class="size-medium wp-image-115 aligncenter">                           </p>

<p style="text-align: center"><div>
        <div id="login">
    <?php

    // Add message
    $message = apply_filters( 'password_protected_login_message', '' );
    if ( ! empty( $message ) ) echo $message . "\n";

    if ( $Password_Protected->errors->get_error_code() ) {
        $errors = '';
        $messages = '';
        foreach ( $Password_Protected->errors->get_error_codes() as $code ) {
            $severity = $Password_Protected->errors->get_error_data( $code );
            foreach ( $Password_Protected->errors->get_error_messages( $code ) as $error ) {
                if ( 'message' == $severity )
                    $messages .= '  ' . $error . "<br />\n";
                else
                    $errors .= '    ' . $error . "<br />\n";
            }
        }
        if ( ! empty( $errors ) )
            echo '<div id="login_error">' . apply_filters( 'password_protected_login_errors', $errors ) . "</div>\n";
        if ( ! empty( $messages ) )
            echo '<p class="message">' . apply_filters( 'password_protected_login_messages', $messages ) . "</p>\n";
    }
    ?>

    <?php do_action( 'password_protected_before_login_form' ); ?>

    <form name="loginform" id="loginform" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="post">
        <p style="width: 50%;margin:0 auto;">
            <input type="password" name="password_protected_pwd" id="password_protected_pass" class="input" value="" size="20" tabindex="20" placeholder="password" style="width: 76%;margin-right: 3%;float: left;" />
        
            <input type="submit" name="wp-submit" id="wp-submit" class="button button-primary button-large" value="<?php esc_attr_e( 'Log In', 'password-protected' ); ?>" tabindex="100"  style="width: 20%;float: left;" />
            <input type="hidden" name="testcookie" value="1" />
            <input type="hidden" name="password-protected" value="login" />
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $_REQUEST['redirect_to'] ); ?>" />
        </p>
    </form>

    <?php do_action( 'password_protected_after_login_form' ); ?>

</div>

<script type="text/javascript">
try{document.getElementById('password_protected_pass').focus();}catch(e){}
if(typeof wpOnload=='function')wpOnload();
</script>

<?php do_action( 'login_footer' ); ?>

<div class="clear"></div>
</div></p>
</div></article></main></div></div><footer class="site-footer" role="contentinfo" itemscope="itemscope" itemtype="http://schema.org/WPFooter"><div class="wrap"></div></footer></div>
</body>
</html>