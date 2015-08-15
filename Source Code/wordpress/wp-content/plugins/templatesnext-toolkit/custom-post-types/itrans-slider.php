<?php

/* ************************************************ */
/*	Team Post Type Functions  */
/* ************************************************ */	    
	    
	
	add_action('init', 'tx_itrans_slider_register');  
	  
	function tx_itrans_slider_register() {  
	
	    $labels = array(
	        'name' => _x('itrans Slider', 'post type general name', "nx-admin"),
	        'singular_name' => _x('itrans Slide', 'post type singular name', "nx-admin"),
	        'add_new' => _x('Add New', 'itrans Slide', "nx-admin"),
	        'add_new_item' => __('Add New itrans Slide', "nx-admin"),
	        'edit_item' => __('Edit itrans Slide', "nx-admin"),
	        'new_item' => __('New itrans Slide', "nx-admin"),
	        'view_item' => __('View itrans Slide', "nx-admin"),
	        'search_items' => __('Search itrans Slide', "nx-admin"),
	        'not_found' =>  __('No itrans slide have been added yet', "nx-admin"),
	        'not_found_in_trash' => __('Nothing found in Trash', "nx-admin"),
	        'parent_item_colon' => ''
	    );
	
	    $args = array(  
	        'labels' => $labels,  
	        'public' => true,  
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'show_in_nav_menus' => false,
	        'rewrite' => false,
	        'supports' => array('title', 'editor', 'thumbnail'),
	        'has_archive' => true,
	        'taxonomies' => array('itrans-slider-category')
	       );  
	  
	    register_post_type( 'itrans-slider' , $args );
		
	}  
	
	function create_itrans_slider_taxonomy() {
		
		$atts = array(
			"label" 						=> _x('itrans Slider Categories', 'category label', "nx-admin"), 
			"singular_label" 				=> _x('itrans Slider Category', 'category singular label', "nx-admin"), 
			'public'                        => true,
			'hierarchical'                  => true,
			'show_ui'                       => true,
			'show_in_nav_menus'             => false,
			'args'                          => array( 'orderby' => 'term_order' ),
			'rewrite'                       => false,
			'query_var'                     => true
		);
		
		register_taxonomy( 'itrans-slider-category', 'itrans-slider', $atts );		
		
	}
	add_action( 'init', 'create_itrans_slider_taxonomy', 0 );		
	
	
	add_filter("manage_edit-itrans_slider_columns", "itrans_slider_edit_columns");   
	  
	function itrans_slider_edit_columns($columns){  
	        $columns = array(  
	            "cb" => "<input type=\"checkbox\" />",  
	            "thumbnail" => "",
	            "title" => __("Slide Title", "nx-admin"),
	            "description" => __("Description", "nx-admin"),
	            "team-category" => __("Categories", "nx-admin")
	        );  
	  
	        return $columns;  
	}