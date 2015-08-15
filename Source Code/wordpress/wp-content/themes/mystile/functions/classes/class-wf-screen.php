<?php
// File Security Check.
if ( ! defined( 'ABSPATH' ) ) exit;

class WF_Screen {
	/**
	 * Generate header HTML.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public static function get_header ( $token = 'woothemes', $screen_icon = 'themes' ) {
		do_action( 'wf_screen_before', $token, $screen_icon );
		do_action( 'wf_screen_before_' . esc_attr( $token ), $token, $screen_icon );
		$html = '<div class="wf-wrap wrap">' . "\n";
		$html .= get_screen_icon( $screen_icon );
		$html .= '<h2 class="nav-tab-wrapper">' . "\n";
		$html .= self::get_navigation_tabs();
		$html .= self::get_admin_branding();
		$html .= '</h2>' . "\n";
		echo $html;
		do_action( 'wf_screen_header_before_content', $token, $screen_icon );
		do_action( 'wf_screen_header_before_content_' . esc_attr( $token ), $token, $screen_icon );
	} // End get_header()

	/**
	 * Generate footer HTML.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public static function get_footer ( $token = 'woothemes', $screen_icon = 'themes' ) {
		do_action( 'wf_screen_footer_after_content_' . esc_attr( $token ), $token, $screen_icon );
		do_action( 'wf_screen_footer_after_content', $token, $screen_icon );
		$html = '</div><!--/.wrap-->' . "\n";
		echo $html;
		do_action( 'wf_screen_after_' . esc_attr( $token ), $token, $screen_icon );
		do_action( 'wf_screen_after', $token, $screen_icon );
	} // End get_footer()

	/**
	 * Generate navigation tabs HTML, based on a specific admin menu.
	 * @access  public
	 * @since   6.0.0
	 * @return  string/WP_Error
	 */
	public static function get_navigation_tabs ( $menu_key = 'woothemes' ) {
		global $submenu;
		if ( ! isset( $submenu[$menu_key] ) ) return new WP_Error( 'invalid_menu_key', __( 'No menu found for the specified menu key.', 'woothemes' ) );
		$html = '';

		$current_tab = '';
		if ( isset( $_GET['page'] ) ) $current_tab = $_GET['page'];

		if ( 0 < count( $submenu[$menu_key] ) ) {
			foreach ( $submenu[$menu_key] as $k => $v ) {
				$class = 'nav-tab';
				if ( $current_tab == $v[2] ) {
					$class .= ' nav-tab-active';
				}

				$url = add_query_arg( 'page', $v[2], admin_url( 'admin.php' ) );
				$html .= '<a href="' . esc_url( $url ) . '" class="' . esc_attr( $class ) . '">' . esc_html( $v[0] ) . '</a>';
			}
		}

		return $html;
	} // End get_navigation_tabs()

	/**
	 * Generate and retrieve HTML for the admin logo branding.
	 * @access public
	 * @since  6.0.0
	 * @return string Generate HTML for the admin logo branding.
	 */
	public static function get_admin_branding () {
		$html = '<span class="logo alignright">' . "\n";
		$html .= '<img src="' . esc_url( apply_filters( 'wf_branding_logo', get_template_directory_uri() . '/functions/assets/images/logo.png' ) ) . '" />' . "\n";
		$html .= '</span><!--/.logo-->' . "\n";
		return $html;
	} // End get_admin_branding()
} // End Class
?>