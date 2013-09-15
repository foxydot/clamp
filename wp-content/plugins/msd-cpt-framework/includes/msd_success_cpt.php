<?php
/**
 * @package MSD Publication CPT
 * @version 0.1
 */

class MSDSuccessCPT {

	public function MSDSuccessCPT(){}
	
	function register_cpt_success() {
	
	    $labels = array( 
	        'name' => _x( 'Success Stories', 'success' ),
	        'singular_name' => _x( 'Success Story', 'success' ),
	        'add_new' => _x( 'Add New', 'success' ),
	        'add_new_item' => _x( 'Add New Success Story', 'success' ),
	        'edit_item' => _x( 'Edit Success Story', 'success' ),
	        'new_item' => _x( 'New Success Story', 'success' ),
	        'view_item' => _x( 'View Success Story', 'success' ),
	        'search_items' => _x( 'Search Success Stories', 'success' ),
	        'not_found' => _x( 'No success stories found', 'success' ),
	        'not_found_in_trash' => _x( 'No success stories found in Trash', 'success' ),
	        'parent_item_colon' => _x( 'Parent Success Story:', 'success' ),
	        'menu_name' => _x( 'Success Stories', 'success' ),
	    );
	
	    $args = array( 
	        'labels' => $labels,
	        'hierarchical' => false,
	        'description' => 'Customer Success Stories',
	        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail'),
	        'taxonomies' => array( 'genre' ),
	        'public' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'menu_position' => 20,
	        
	        'show_in_nav_menus' => true,
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'has_archive' => true,
	        'query_var' => true,
	        'can_export' => true,
	        'rewrite' => array('slug'=>'success-stories','with_front'=>false),
	        'capability_type' => 'post'
	    );
	
	    register_post_type( 'msd_success', $args );
	}
		
	function list_success_stories( $atts ) {
		global $success;
		extract( shortcode_atts( array(
		), $atts ) );
		
		$args = array( 'post_type' => 'msd_success', 'numberposts' => 0, );

		$items = get_posts($args);
	    foreach($items AS $item){ 
	    	$success->the_meta($item->ID);
	    	$excerpt = $item->post_excerpt?$item->post_excerpt:msd_trim_headline($item->post_content);
	     	$publication_list .= '
	     	<li>
				<h3><a href="'.get_permalink($item->ID).'">'.$item->post_title.'</a></h3>
				<h4><a href="'.get_permalink($item->ID).'">'.$success->get_the_value('subtitle').'</a></h4>
				<p>'.$excerpt.' <a href="'.get_permalink($item->ID).'">More ></a></p>
				<div class="clear"></div>
			</li>';
	
	     }
		
		return '<ul class="publication-list success-stories">'.$publication_list.'</ul><div class="clear"></div>';
	}	
}

	

	add_action( 'init', array('MSDSuccessCPT','register_cpt_success') );
	add_shortcode( 'success-stories', array('MSDSuccessCPT','list_success_stories') );