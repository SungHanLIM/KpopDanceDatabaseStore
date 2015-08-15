<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

/*-----------------------------------------------------------------------------------*/
/* WooThemes Framework Version & Theme Version */
/*-----------------------------------------------------------------------------------*/
/**
 * Return the version number of the WooFramework.
 * @since  6.0.0
 * @return string
 */
function wf_get_version () {
    return '6.0.4';
} // End wf_get_version()

function woo_version_init () {
    $woo_framework_version = wf_get_version();
    if ( get_option( 'woo_framework_version' ) != $woo_framework_version ) {
    	update_option( 'woo_framework_version', $woo_framework_version );
    }
} // End woo_version_init()

add_action( 'init', 'woo_version_init', 10 );

function woo_version () {
    $data = wooframework_get_theme_version_data();
	echo "\n<!-- Theme version -->\n";
    if ( isset( $data['is_child'] ) && true == $data['is_child'] ) echo '<meta name="generator" content="'. esc_attr( $data['child_theme_name'] . ' ' . $data['child_theme_version'] ) . '" />' ."\n";
    echo '<meta name="generator" content="'. esc_attr( $data['theme_name'] . ' ' . $data['theme_version'] ) . '" />' ."\n";
    echo '<meta name="generator" content="WooFramework '. esc_attr( $data['framework_version'] ) .'" />' ."\n";
} // End woo_version()

/*-----------------------------------------------------------------------------------*/
/* Load the required Framework Files */
/*-----------------------------------------------------------------------------------*/

$functions_path = get_template_directory() . '/functions/';
$classes_path = $functions_path . 'classes/';

if ( true == (bool)apply_filters( 'wf_load_deprecated_functions', true ) ) {
    require_once( $functions_path . 'deprecated.php' );                         // Load deprecated functionality. Can be disabled via a filter if the user doesn't wish to load these functions.
    require_once( $functions_path . 'admin-medialibrary-uploader.php' );       // Framework Media Library Uploader Functions // 2010-11-05.
}
// Load core classes for the WooFramework.
require_once( $classes_path . 'class-wf.php' );                             // WF core class.
require_once( $classes_path . 'class-wf-fields.php' );                      // Form fields generator class.
require_once( $classes_path . 'class-wf-fields-settings.php' );             // Theme settings class. Extends WF_Fields.
require_once( $classes_path . 'class-wf-fields-meta.php' );                 // Post meta fields class. Extends WF_Fields.
require_once( $classes_path . 'class-wf-settings.php' );                    // A class to handle all basic settings interactions.
require_once( $classes_path . 'class-wf-meta.php' );                        // Meta box generator class.

/**
 * Returns the main instance of WF to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WF
 */
function WF() {
    return WF::instance();
} // End WF()

// Run the WF() function to generate the initial instance.
WF();

// Load the other WooFramework files.
require_once( $functions_path . 'admin-functions.php' );					// Functions used in the WooFramework and in the theme files.
require_once( $functions_path . 'admin-setup.php' );						// Set up the WooFramework.
require_once( $functions_path . 'admin-interface.php' );					// Administration interfaces.
require_once( $functions_path . 'admin-seo.php' );							// SEO functions.
require_once( $functions_path . 'admin-sbm.php' ); 						    // Widget Area functions.
require_once( $functions_path . 'admin-hooks.php' );						// Contextual hooks.

if ( true == (bool)apply_filters( 'wf_enable_custom_nav', false ) ) {
	require_once( $functions_path . 'admin-custom-nav.php' );				// Woo Custom Navigation
}

require_once ( $functions_path . 'admin-shortcodes.php' );					// Woo Shortcodes

// Load certain files only in the WordPress admin.
if ( is_admin() ) {
    require_once( $classes_path . 'class-wf-screen-admin-base.php' );       // Base class for common functionality used on more technical admin screens.
    require_once( $classes_path . 'class-wf-screen.php' );                  // Admin screen class.
    require_once( $classes_path . 'class-wf-screen-welcome.php' );          // Welcome screen class.
    require_once( $classes_path . 'class-wf-screen-framework.php' );        // Framework screen class.

    require_once( $classes_path . 'class-wf-backup.php' );                  // WF_Backup Class.
    require_once( $functions_path . 'admin-backup.php' );                   // Theme Options Backup // 2011-08-26.
    require_once( $functions_path . 'admin-shortcode-generator.php' ); 	    // Framework Shortcode generator // 2011-01-21.
} else {
    // Add or remove Generator meta tags
    if ( true == apply_filters( 'wf_disable_generator_tags', false ) ) {
        remove_action( 'wp_head',  'wp_generator' );
    } else {
        add_action( 'wp_head', 'woo_version', 10 );
    }
}
?>