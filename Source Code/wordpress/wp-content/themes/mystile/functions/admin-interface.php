<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;


function wf_setup_screen_header_footer () {
	/**
	 * Setup the default WooFramework admin screen header.
	 * @since  6.0.0
	 */
	add_action( 'wf_screen_get_header', array( 'WF_Screen', 'get_header' ), 10, 2 );

	/**
	 * Setup the default WooFramework admin screen footer.
	 * @since  6.0.0
	 */
	add_action( 'wf_screen_get_footer', array( 'WF_Screen', 'get_footer' ), 10, 2 );
} // End wf_setup_screen_header_footer()

add_action( 'admin_init', 'wf_setup_screen_header_footer' );

/**
 * Set the default placeholder image URL to the default image provided within the WooFramework.
 * @since  6.0.1
 * @param  string $url The current empty placeholder image URL.
 * @return string      The default placeholder image URL.
 */
function wf_set_default_placeholder_image_url ( $url ) {
	if ( '' == $url ) {
		return WF()->get_assets_url() . 'images/placeholder.png';
	} else {
		return $url;
	}
} // End wf_set_default_placeholder_image_url()

/**
 * Set the default placeholder image path to the default image provided within the WooFramework.
 * @since  6.0.1
 * @param  string $path The current empty placeholder image path.
 * @return string       The default placeholder image path.
 */
function wf_set_default_placeholder_image_path ( $path ) {
	if ( '' == $path ) {
		return WF()->get_assets_path() . 'images/placeholder.png';
	} else {
		return $path;
	}
} // End wf_set_default_placeholder_image_path()

if ( true == (bool)apply_filters( 'wf_use_default_placeholder_image', false ) ) {
	add_filter( 'wf_placeholder_image_url', 'wf_set_default_placeholder_image_url' );
	add_filter( 'wf_placeholder_image_path', 'wf_set_default_placeholder_image_path' );
}

/**
 * Enqueue menu.css.
 * Used to control the display of WooFramework menu items across the dashboard
 * @since  6.0.0
 * @return void
 */
function wf_menu_styles() {
	$token = 'woo';
	$wf_version = wf_get_version();

	wp_register_style( $token . '-menu', esc_url( WF()->get_assets_url() . 'css/menu.css' ), array(), $wf_version );
	wp_enqueue_style( $token . '-menu' );
}

add_action( 'admin_enqueue_scripts', 'wf_menu_styles' );

/**
 * Display a list of useful links within the WordPress admin.
 * @since  6.0.0
 * @return void
 */
function wf_useful_links () {
	$theme_data = wooframework_get_theme_version_data();
	do_action( 'wf_useful_links_before' );
	$theme_name =  strtolower( $theme_data['theme_name'] )  ;
	$docs_url =  get_option( 'woo_manual', 'http://docs.woothemes.com/document/' .  urlencode( sanitize_title( $theme_name )  )  );
	$html = '<ul class="useful-links">' . "\n";
	do_action( 'wf_useful_links_list_start' );
	$html .= '<li class="documentation"><a href="' . esc_url( $docs_url ) . '" target="_blank">' . __( 'Documentation', 'woothemes' ) . '</a></li>' . "\n";
	$html .= '<li class="changelog"><a href="' . esc_url( get_template_directory_uri() . '/changelog.txt' ) . '" target="_blank">' . __( 'Changelog', 'woothemes' ) . '</a></li>' . "\n";
	$html .= '<li class="support"><a href="' . esc_url( 'http://support.woothemes.com/' ) . '" target="_blank">' . __( 'Help', 'woothemes' ) . '</a></li>' . "\n";
	do_action( 'wf_useful_links_list_end' );
	$html .= '</ul>' . "\n";
	echo $html;
	do_action( 'wf_useful_links_after' );
} // End wf_useful_links()

add_action( 'wf_screen_header_before_content_woothemes', 'wf_useful_links' );
add_action( 'wf_screen_header_before_content_wf-framework', 'wf_useful_links' );

if ( ! function_exists( 'woo_update_options_filter' ) ) {
	function woo_update_options_filter( $new_value, $old_value ) {
		if ( !current_user_can( 'unfiltered_html' ) ) {
			// Options that get KSES'd
			foreach( woo_ksesed_option_keys() as $option ) {
				$new_value[$option] = wp_kses_post( $new_value[$option] );
			}
			// Options that cannot be set without unfiltered HTML
			foreach( woo_disabled_if_not_unfiltered_html_option_keys() as $option ) {
				$new_value[$option] = $old_value[$option];
			}
		}
		return $new_value;
	}
}

if ( ! function_exists( 'woo_prevent_option_update' ) ) {
	function woo_prevent_option_update( $new_value, $old_value ) {
		return $old_value;
	}
}

/**
 * This is the list of options that are run through KSES on save for users without
 * the unfiltered_html capability
 */
if ( ! function_exists( 'woo_ksesed_option_keys' ) ) {
	function woo_ksesed_option_keys() {
		return array();
	}
}

/**
 * This is the list of standalone options that are run through KSES on save for users without
 * the unfiltered_html capability
 */
if ( ! function_exists( 'woo_ksesed_standalone_options' ) ) {
	function woo_ksesed_standalone_options() {
		return array( 'woo_footer_left_text', 'woo_footer_right_text', 'woo_connect_content' );
	}
}

/**
 * This is the list of options that users without the unfiltered_html capability
 * are not able to update
 */
if ( ! function_exists( 'woo_disabled_if_not_unfiltered_html_option_keys' ) ) {
	function woo_disabled_if_not_unfiltered_html_option_keys() {
		return array( 'woo_google_analytics', 'woo_custom_css' );
	}
}

add_filter( 'pre_update_option_woo_options', 'woo_update_options_filter', 10, 2 );
foreach( woo_ksesed_standalone_options() as $o ) {
	add_filter( 'pre_update_option_' . $o, 'wp_kses_post' );
}
unset( $o );

if ( ! function_exists( 'woothemes_admin_menu_after' ) ) {
/**
 * Load WooFramework menu items that should always appear last.
 * @since  6.0.0
 * @return void
 */
function woothemes_admin_menu_after () {
	global $current_user;
	$current_user_id = $current_user->user_login;
	$super_user = apply_filters( 'wf_super_user', '' );

	do_action( 'wf_admin_menu_after_before_defaults' );

	// Update Framework Menu Item
	if( $super_user == $current_user_id || empty( $super_user ) ) {
		$framework_update_page = add_submenu_page( 'woothemes', 'WooFramework Update', 'Update Framework', 'manage_options', 'woothemes_framework_update', 'woothemes_framework_update_page' );
	}

	do_action( 'wf_admin_menu_after' );
} // End woothemes_admin_menu_after()
}

add_action( 'admin_menu', 'woothemes_admin_menu_after', 50 );

// If this is the Listings theme, add the Content Builder admin menu item.
if ( function_exists( 'woothemes_content_builder_menu' ) ) {
	add_action( 'wf_admin_menu_after_before_defaults', 'woothemes_content_builder_menu' );
}

/**
 * Unset the interal WooFramework admin menu items, and preserve the screens themselves (linked to elsewhere).
 * @since  6.0.0
 * @return void
 */
function wf_unset_internal_framework_menu_items () {
	remove_submenu_page( 'woothemes', 'woothemes-backup' );
	remove_submenu_page( 'woothemes', 'woothemes_framework_update' );
} // End wf_unset_internal_framework_menu_items()

add_action( 'admin_head', 'wf_unset_internal_framework_menu_items' );

/**
 * Load admin CSS on specific screens.
 * @since  6.0.0
 * @return void
 */
function wf_load_admin_css () {
	$load_on = (array)apply_filters( 'wf_load_admin_css', array( 'woothemes', 'wf-framework', 'woothemes-backup' ) );
	wp_register_style( 'wf-admin', esc_url( WF()->get_assets_url() . 'css/admin.css' ), array(), '1.0.0', 'all' );

	if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $load_on ) )
		wp_enqueue_style( 'wf-admin' );
} // End wf_load_admin_css()

add_action( 'admin_enqueue_scripts', 'wf_load_admin_css' );

/**
 * Make sure to flush the rewrite rules when saving on the settings screen.
 * @since 6.0.0
 */
add_action( 'wf_settings_save_before', 'woo_flush_rewriterules' );

function woo_thumb_admin_notice() {

	if ( get_user_setting( 'wooframeworkhidebannerwootimthumb', '0' ) == '1' ) { return; }
	global $current_user;
	$current_user_id = $current_user->user_login;
	$super_user = get_option( 'framework_woo_super_user' );
	if( $super_user == $current_user_id || empty( $super_user ) ) {
		// Test for old timthumb scripts
		$thumb_php_test = file_exists(  get_template_directory() . '/thumb.php' );
		$timthumb_php_test = file_exists(  get_template_directory() . '/timthumb.php' );

		if ( ( $thumb_php_test || $timthumb_php_test ) && ! is_child_theme() ) {
			echo '<div class="error fade">
    			   <p><strong>' . __( 'ATTENTION: A possible old version of the TimThumb script was detected in your theme folder. Please remove the following files from your theme as a security precaution.', 'woothemes' ) . ':</strong></p>' . "\n";
    		if ( $thumb_php_test ) { echo '<p><strong>- thumb.php</strong></p>'; }
    		if ( $timthumb_php_test ) { echo '<p><strong>- timthumb.php</strong></p>'; }
    		echo '<p>' . __( 'If you\'ve added "thumb.php" to your child theme manually please ensure the file is kept up to date. You can then safely hide this notice.', 'woothemes' ) . '</p>' . "\n";
    		echo '</div>';

		}
	} // End If Statement
} // End woo_thumb_admin_notice()

add_action( 'admin_notices', 'woo_thumb_admin_notice' );

global $pagenow;
if ( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'woothemes' ) {
	if ( get_option( 'framework_woo_framework_version_checker' ) == 'true' ) { add_action( 'admin_notices', 'woo_framework_update_notice', 10 ); }

	add_action( 'admin_notices', 'woo_framework_critical_update_notice', 8 ); // Periodically check for critical WooFramework updates.
}

/**
 * woo_framework_update_notice function.
 *
 * @description Notify users of framework updates, if necessary.
 * @since 4.8.0
 * @access public
 * @return void
 */
if ( ! function_exists( 'woo_framework_update_notice' ) ) {
	function woo_framework_update_notice () {
		$local_version = get_option( 'woo_framework_version' );
		if ( $local_version == '' ) { return; }
		$update_data = woo_framework_version_checker( $local_version );

		$html = '';

		if ( is_array( $update_data ) && $update_data['is_update'] == true ) {
			$html = '<div id="wooframework_update" class="updated fade"><p>' . sprintf( __( 'WooFramework update is available (v%s). %sDownload new version%s (%sSee Changelog%s)', 'woothemes' ), $update_data['version'], '<a href="' . admin_url( 'admin.php?page=woothemes_framework_update' ) . '">', '</a>', '<a href="http://www.woothemes.com/updates/functions-changelog.txt" target="_blank" title="Changelog">', '</a>' ) . '</p></div>';
		}

		if ( $html != '' ) { echo $html; }
	} // End woo_framework_update_notice()
}

/**
 * woo_framework_critical_update_notice function.
 *
 * @description Notify users of critical framework updates, if necessary.
 * @since 4.8.0
 * @access public
 * @return void
 */
if ( ! function_exists( 'woo_framework_critical_update_notice' ) ) {
	function woo_framework_critical_update_notice () {
		// Determine if the check has happened.
		$critical_update = get_transient( 'woo_framework_critical_update' );
		$critical_update_data = get_transient( 'woo_framework_critical_update_data' );

		if ( ! $critical_update || ! is_array( $critical_update_data ) ) {

			$local_version = get_option( 'woo_framework_version' );
			if ( $local_version == '' ) { return; }

			$update_data = woo_framework_version_checker( $local_version, true );

			// Set this to "has been checked" for 2 weeks.
			set_transient( 'woo_framework_critical_update', true, 60*60*336 );

			// Cache the data as well.
			set_transient( 'woo_framework_critical_update_data', $update_data, 60*60*336 );
		} else {
			$update_data = $critical_update_data;
		}

		$html = '';

		// Generate output based on returned/stored data.
		if ( is_array( $update_data ) && $update_data['is_update'] == true && $update_data['is_critical'] == true ) {

			// Remove the generic update notice.
			remove_action( 'admin_notices', 'woo_framework_update_notice', 10 );

			$html = '<div id="wooframework_important_update" class="error fade"><p>' . sprintf( __( 'An important WooFramework update is available (v%s). %sDownload new version%s (%sSee Changelog%s)', 'woothemes' ), $update_data['version'], '<a href="' . admin_url( 'admin.php?page=woothemes_framework_update' ) . '">', '</a>', '<a href="http://www.woothemes.com/updates/functions-changelog.txt" target="_blank" title="Changelog">', '</a>' ) . '</p></div>';
		}

		if ( $html != '' ) { echo $html; }
	} // End woo_framework_critical_update_notice()
}
?>