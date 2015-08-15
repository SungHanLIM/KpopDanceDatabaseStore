<?php
/**
 * Welcome Screen
 *
 * Shows a welcome screen on initial theme activation.
 *
 * @author 		WooThemes
 * @category 	Admin
 * @package 	WooFramework/Admin
 * @version     6.0.0
 * @since 		6.0.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WF_Screen_Welcome extends WF_Screen_Admin_Base {
	/**
	 * Class constructor.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function __construct () {
		parent::__construct();
		add_action( 'admin_menu', array( $this, 'admin_menus' ) );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
	} // End __construct()

	/**
	 * Add admin menus/screens.
	 * @access public
	 * @since  6.0.0
	 * @return void
	 */
	public function admin_menus () {
		// About
		$about = add_submenu_page( 'woothemes', $this->_theme_data['theme_name'], $this->_theme_data['theme_name'], 'manage_options', 'wf-about', array( $this, 'about_screen' ) );

		add_action( 'admin_print_styles-'. $about, array( $this, 'admin_css' ) );

		add_action( 'load-' . $about, array( $this, 'maybe_remove_existing_plugin_install_overrides' ) );
	} // End admin_menus()

	/**
	 * Run functions and load inline styles into the admin head area.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function admin_head () {
		remove_submenu_page( 'woothemes', 'wf-about' );
	} // End admin_head()

	/**
	 * The "About" screen.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function about_screen () {
		?>
		<div class="wrap about-wrap wf-wrap">
		<?php
				$this->_intro();
				$this->_get_started();
				if ( is_multisite() ) {
					$this->_has_supported_plugins_multisite();
				} else {
					$this->_has_supported_plugins();
				}
				$this->_maybe_display_woothemes_helper_notice();
		?>
		</div><!--/.wrap .about-wrap wf-wrap-->
		<?php
	} // End about_screen()

	/**
	 * Generic intro header for each of the screens.
	 * @access  private
	 * @since   6.0.0
	 * @return  void
	 */
	private function _intro () {
		$ct 		= $this->_theme_obj;
		$screenshot = $ct->get_screenshot();
		$class 		= $screenshot ? 'has-screenshot' : '';
		?>
		<div id="current-theme" class="<?php echo esc_attr( $class ); ?>">
		<?php if ( $screenshot ) : ?>
			<img class="theme-screenshot" src="<?php echo esc_url( $screenshot ); ?>" alt="<?php esc_attr_e( 'Current theme preview', 'woothemes' ); ?>" />
		<?php endif; ?>
		<h1><?php echo sprintf( __( 'Welcome to %s', 'woothemes' ), '<span class="theme-name">' . $ct->display( 'Name' ) . '</span>' ); ?></h1>
		<div>
			<?php
				$this->_theme_meta();
			?>
			<p class="theme-description"><?php echo $ct->display( 'Description' ); ?></p>
			<?php if ( $ct->parent() ) {
				printf( ' <p class="howto">' . __( 'This <a href="%1$s">child theme</a> loads its parent theme, %2$s.', 'woothemes' ) . '</p>',
					__( 'http://codex.wordpress.org/Child_Themes' ),
					$ct->parent()->display( 'Name' ) );
			} ?>

		<p class="getting-started-buttons">
			<a href="<?php echo esc_url( add_query_arg( 'page', 'woothemes', admin_url( 'admin.php' ) ) ); ?>" class="button button-primary"><?php printf( __( 'Configure %s', 'woothemes' ), $ct->display( 'Name' ) ); ?></a>
			<a href="<?php echo esc_url( get_option( 'woo_manual', 'http://docs.woothemes.com/' ) ); ?>"><?php printf( __( 'View Documentation &rarr;', 'woothemes' ) ); ?></a>
		</p>
		</div>
		</div><!--/#current-theme-->
		<?php
	} // End _intro()

	/**
	 * Theme options for the "Getting Started" screen.
	 * @access  private
	 * @since   6.0.0
	 * @return  void
	 */
	private function _get_started () {
		?>
		<hr class="spacer" />

		<?php
	} // End _get_started()

	/**
	 * Display meta data about the theme.
	 * @access  private
	 * @since   6.0.0
	 * @return  void
	 */
	private function _theme_meta () {
		$ct = $this->_theme_obj;
		?>
		<ul class="theme-info wf-theme-info">
			<li><?php printf( __( 'Version %s', 'woothemes' ), '<strong>' . $ct->__get( 'Version' ) . '</strong>' ); ?></li>
			<?php
			if ( $ct->parent() ) {
			?>
			<li><?php printf( __( '%s Version %s', 'woothemes' ), $ct->parent()->__get( 'Name' ), '<strong>' . $ct->parent()->__get( 'Version' ) . '</strong>' ); ?></li>
			<?php
			}
			?>
			<li><?php printf( __( 'WooFramework %s - %s', 'woothemes' ), '<strong>' . $this->_theme_data['framework_version'] . '</strong>', sprintf( __( '%sUpdate%s', 'woothemes' ) . ' <span class="dashicons dashicons-update"></span>', '<a href="' . esc_url( add_query_arg( 'page', 'woothemes_framework_update', admin_url( 'admin.php' ) ) ) . '">', '</a>' ) ); ?></li>
		</ul>

		<hr />
		<?php
	} // End _theme_meta()
} // End Class

WF()->screens['welcome'] = new WF_Screen_Welcome();