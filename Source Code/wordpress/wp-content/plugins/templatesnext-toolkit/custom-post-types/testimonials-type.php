<?php

/* ************************************************ */
/*	Testimonials Post Type Functions  */
/* ************************************************ */	    
	    
	
	add_action('init', 'tx_testimonials_register');  
	  
	function tx_testimonials_register() {  
	
	    $labels = array(
	        'name' => _x('Testimonials', 'post type general name', "nx-admin"),
	        'singular_name' => _x('Testimonial', 'post type singular name', "nx-admin"),
	        'add_new' => _x('Add New', 'Testimonial', "nx-admin"),
	        'add_new_item' => __('Add New Testimonial', "nx-admin"),
	        'edit_item' => __('Edit Testimonial', "nx-admin"),
	        'new_item' => __('New Testimonial', "nx-admin"),
	        'view_item' => __('View Testimonial', "nx-admin"),
	        'search_items' => __('Search Testimonials', "nx-admin"),
	        'not_found' =>  __('No testimonials have been added yet', "nx-admin"),
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
			'menu_icon' => 'dashicons-editor-quote',
	        'taxonomies' => array('testimonials-category')
	       );  
	  
	    register_post_type( 'testimonials' , $args );  
	}  
	
	
	function tx_create_testimonials_taxonomy() {
		
		$atts = array(
			"label" 						=> _x('Testimonial Categories', 'category label', "nx-admin"), 
			"singular_label" 				=> _x('Testimonial Category', 'category singular label', "nx-admin"), 
			'public'                        => true,
			'hierarchical'                  => true,
			'show_ui'                       => true,
			'show_in_nav_menus'             => false,
			'args'                          => array( 'orderby' => 'term_order' ),
			'rewrite'                       => false,
			'query_var'                     => true
		);
		
		register_taxonomy( 'testimonials-category', 'testimonials', $atts );		
		
	}
	add_action( 'init', 'tx_create_testimonials_taxonomy', 0 );	
	
	
	add_filter("manage_edit-testimonials_columns", "testimonials_edit_columns");   
	
	function testimonials_edit_columns($columns){  
	        $columns = array(  
	            "cb" => "<input type=\"checkbox\" />",
	            "thumbnail" => "",				
	            "title" => __("Testimonial", "nx-admin"),
	            "description" => __("Description", "nx-admin"),				
	            "testimonials-category" => __("Categories", "nx-admin")
	        );  
	  
	        return $columns;  
	}