<?php

/* ************************************************ */	
/*	Portfolio Post Type Functions  */
/* ************************************************ */	
	//$portfolio_permalinks = get_option( 'nx_portfolio_permalinks' );
	
	    
	add_action('init', 'tx_portfolio_register');  
	  
	function tx_portfolio_register() {
		
		//$portfolio_permalinks = get_option( 'nx_portfolio_permalinks' );
		//$portfolio_permalink = empty( $portfolio_permalinks['portfolio_base'] ) ? _x( 'portfolio', 'slug', 'nx-admin' ) : $portfolio_permalinks['portfolio_base'];
		$portfolio_permalink = _x( 'portfolio', 'slug', 'nx-admin' );
		
	    $labels = array(
	        'name' => _x('Portfolio', 'post type general name', "nx-admin"),
	        'singular_name' => _x('Portfolio Item', 'post type singular name', "nx-admin"),
	        'add_new' => _x('Add New', 'portfolio item', "nx-admin"),
	        'add_new_item' => __('Add New Portfolio Item', "nx-admin"),
	        'edit_item' => __('Edit Portfolio Item', "nx-admin"),
	        'new_item' => __('New Portfolio Item', "nx-admin"),
	        'view_item' => __('View Portfolio Item', "nx-admin"),
	        'search_items' => __('Search Portfolio', "nx-admin"),
	        'not_found' =>  __('No portfolio items have been added yet', "nx-admin"),
	        'not_found_in_trash' => __('Nothing found in Trash', "nx-admin"),
	        'parent_item_colon' => ''
	    );
			
	    $args = array(  
	        'labels' => $labels,  
	        'public' => true,  
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'show_in_nav_menus' => false,
	        'hierarchical' => false,
	        'rewrite' => $portfolio_permalink != "portfolio" ? array(
	        				'slug' => untrailingslashit( $portfolio_permalink ),
	        				'with_front' => false,
	        				'feeds' => true )
	        			: false,
	        'supports' => array('title', 'editor', 'thumbnail'),
	        'has_archive' => true,
			'menu_icon' => 'dashicons-art',

	        'taxonomies' => array('portfolio-category')
	       );  

		register_post_type( 'portfolio' , $args ); 
			
	} 
	
	
	function tx_create_portfolio_taxonomy() {
		
		$atts = array(
			"label" 						=> _x('Portfolio Categories', 'category label', "nx-admin"), 
			"singular_label" 				=> _x('Portfolio Category', 'category singular label', "nx-admin"), 
			'public'                        => true,
			'hierarchical'                  => true,
			'show_ui'                       => true,
			'show_in_nav_menus'             => false,
			'args'                          => array( 'orderby' => 'term_order' ),
			'rewrite' 						=> array(
												//'slug'         => empty( $portfolio_permalinks['category_base'] ) ? _x( 'portfolio-category', 'slug', 'nx-admin' ) : $portfolio_permalinks['category_base'],
												'slug'         => _x( 'portfolio-category', 'slug', 'nx-admin' ),
												'with_front'   => false,
												'hierarchical' => true,
											),
			'query_var'                     => true
		);
		
		register_taxonomy( 'portfolio-category', 'portfolio', $atts );
	}
	
	add_action( 'init', 'tx_create_portfolio_taxonomy', 0 );
	 
		
	
	add_filter("manage_edit-portfolio_columns", "portfolio_edit_columns");   
	  
	function portfolio_edit_columns($columns){  
	        $columns = array(  
	            "cb" => "<input type=\"checkbox\" />",  
	            "thumbnail" => "",
	            "title" => __("Portfolio Item", "nx-admin"),
	            "description" => __("Description", "nx-admin"),
	            "portfolio-category" => __("Categories", "nx-admin") 
	        );  
	  
	        return $columns;  
	}