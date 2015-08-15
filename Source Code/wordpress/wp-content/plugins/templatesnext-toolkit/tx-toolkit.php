<?php

/*
Plugin Name: TemplatesNext ToolKit
Description: Custom Portfolio and Shortcode functionality for TemplatesNext Wordpress Themes
Version: 1.1.3
Author: TemplatesNext
Author URI: http://templatesnext.org/
License: GPLv2 or later
*/
function tx_toolkit_activation() {
}
register_activation_hook(__FILE__, 'tx_toolkit_activation');

function tx_toolkit_deactivation() {
}
register_deactivation_hook(__FILE__, 'tx_toolkit_deactivation');


add_action( 'admin_enqueue_scripts', 'tx_toolkit_admin_style' );
function tx_toolkit_admin_style() {
	wp_enqueue_style( 'colorbox', plugins_url( 'css/colorbox.css', __FILE__ ), false, '1.5.14', 'all' );		
    wp_enqueue_style( 'tx-toolkit-admin-style', plugins_url('css/tx-admin-style.css', __FILE__) );
	wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', false, '4.1.0', 'all' );
	
	wp_enqueue_script( 'colorbox', plugins_url( 'js/jquery.colorbox-min.js', __FILE__ ), array( 'jquery' ), '1.5.14', true );	
	wp_enqueue_script('tx-main', plugins_url('/js/tx_main.js', __FILE__));
}


function tx_scripts_styles() {
	
	wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css', false, '4.1.0', 'all' );	
	wp_enqueue_style( 'owl-carousel', plugins_url( 'css/owl.carousel.css', __FILE__ ), false, '1.3.2', 'all' );
	wp_enqueue_style( 'owl-carousel-transitions', plugins_url( 'css/owl.transitions.css', __FILE__ ), false, '1.3.2', 'all' );	
	wp_enqueue_style( 'colorbox', plugins_url( 'css/colorbox.css', __FILE__ ), false, '1.5.14', 'all' );		
	wp_enqueue_style( 'tx-style', plugins_url('css/tx-style.css', __FILE__), array(), '1.01' );	

	wp_enqueue_script( 'jquery-masonry' );
	wp_enqueue_script( 'owl-carousel', plugins_url( 'js/owl.carousel.min.js', __FILE__ ), array( 'jquery' ), '1.3.2', true );
	wp_enqueue_script( 'colorbox', plugins_url( 'js/jquery.colorbox-min.js', __FILE__ ), array( 'jquery' ), '1.5.14', true );	
	wp_enqueue_script( 'tx-script', plugins_url('js/tx-script.js', __FILE__), array(), '2013-07-18', true );
}
add_action( 'wp_enqueue_scripts', 'tx_scripts_styles' );

add_action( 'admin_enqueue_scripts', 'tx_enqueue_color_picker' );
function tx_enqueue_color_picker( $hook_suffix ) {
    // first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}


// multiple featured image
add_theme_support( 'post-thumbnails' );
require_once('custom-post-types/multi-post-thumbnails.php'); /* Must be located directly under lib folder */
    // Define additional "post thumbnails". Relies on MultiPostThumbnails to work
if (class_exists('MultiPostThumbnails')) { 

    $types = array('portfolio' ); /* 'landing_pages' adds support for landing pages CPT,  'post' adds support for blog single pages */
    foreach($types as $type) {
		new MultiPostThumbnails(array('label' => '2nd Feature Image', 'id' => 'feature-image-2', 'post_type' => $type)); 
		new MultiPostThumbnails(array('label' => '3rd Feature Image', 'id' => 'feature-image-3', 'post_type' => $type));
		//new MultiPostThumbnails(array('label' => '4th Feature Image', 'id' => 'feature-image-4', 'post_type' => $type));
		//new MultiPostThumbnails(array('label' => '5th Feature Image', 'id' => 'feature-image-5', 'post_type' => $type));
    }

};

// for thumb retrive https://github.com/voceconnect/multi-post-thumbnails/wiki


//require_once('post-types.php');
require_once('tx-functions.php');
require_once('shortcodes.php');
require_once('custom-post-types/testimonials-type.php');
require_once('custom-post-types/portfolio-type.php');
require_once('custom-post-types/itrans-slider.php');
require_once('inc/aq_resizer.php');


/*-----------------------------------------------------------------------------------*/
/*	Loading Widgets  */
/*-----------------------------------------------------------------------------------*/ 

require_once('inc/widgets/widget-posts.php');
//require_once('inc/widgets/widget-portfolio.php');
require_once('inc/widgets/widget-portfolio-grid.php');
require_once('inc/widgets/widget-advertgrid.php');
require_once('inc/widgets/widget-comments.php');
require_once('inc/widgets/widget-image.php');


/*-----------------------------------------------------------------------------------*/
/*	Metabox
/*-----------------------------------------------------------------------------------*/ 

require_once('inc/tx-meta.php');
require_once('inc/meta-box/meta-box.php');



if(!defined('TX_TOOLKIT_PATH')){
	define('TX_TOOLKIT_PATH', realpath(plugin_dir_path(__FILE__)) . DIRECTORY_SEPARATOR );
}
if(!defined('TX_TOOLKIT_URL')){
	define('TX_TOOLKIT_URL', plugin_dir_url(__FILE__) );
}
