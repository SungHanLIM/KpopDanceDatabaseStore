<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Main WF Class
 *
 * @class WF
 * @version	6.0.0
 * @since 6.0.0
 * @package	WF
 * @author Matty
 */
final class WF {
	/**
	 * WF The single instance of WF.
	 * @var 	object
	 * @access  private
	 * @since 	6.0.0
	 */
	private static $_instance = null;

	/**
	 * The token.
	 * @var     string
	 * @access  private
	 * @since   6.0.0
	 */
	private $_token;

	/**
	 * The settings.
	 * @var     object
	 * @access  public
	 * @since   6.0.0
	 */
	public $settings;

	/**
	 * The meta boxes.
	 * @var     object
	 * @access  public
	 * @since   6.0.0
	 */
	public $meta_boxes;

	/**
	 * The admin screens.
	 * @var     object
	 * @access  public
	 * @since   6.0.0
	 */
	public $screens;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->_token = 'wf';
		$this->screens = array();

		// Set up the settings, on init.
		add_action( 'init', array( $this, 'setup_settings' ) );

		// Set up the meta boxes, on init.
		add_action( 'init', array( $this, 'setup_meta_boxes' ) );
	} // End __construct()

	/**
	 * Set up an object to handle basic settings interactions.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function setup_settings () {
		$this->settings = new WF_Settings();
		$GLOBALS['woo_options'] = $this->settings->get_all();
	} // End setup_settings()

	/**
	 * Set up an object to handle basic meta box interactions.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function setup_meta_boxes () {
		$this->meta_boxes = new WF_Meta();
	} // End setup_meta_boxes()

	/**
	 * Return the URL to the WooFramework directory.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function get_url () {
		return esc_url( apply_filters( 'wf_url', get_template_directory_uri() . '/functions/' ) );
	} // End get_url()

	/**
	 * Return the path to the WooFramework directory.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function get_path () {
		return (string)apply_filters( 'wf_path', get_template_directory() . '/functions/' );
	} // End get_path()

	/**
	 * Return the path to the WooFramework assets directory.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function get_assets_path () {
		return (string)apply_filters( 'wf_assets_path', $this->get_path() . 'assets/' );
	} // End get_assets_path()

	/**
	 * Return the URL to the WooFramework assets directory.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function get_assets_url () {
		return esc_url( apply_filters( 'wf_assets_url', $this->get_url() . 'assets/' ) );
	} // End get_assets_url()

	/**
	 * Return the URL to the placeholder image.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function get_placeholder_image_url () {
		return esc_url( apply_filters( 'wf_placeholder_image_url', '' ) );
	} // End get_placeholder_image_url()

	/**
	 * Return the directory path to the placeholder image.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function get_placeholder_image_path () {
		return apply_filters( 'wf_placeholder_image_path', '' );
	} // End get_placeholder_image_path()

	/**
	 * Main WF Instance
	 *
	 * Ensures only one instance of WF is loaded or can be loaded.
	 *
	 * @since 6.0.0
	 * @static
	 * @see WF()
	 * @return Main WF instance
	 */
	public static function instance () {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 6.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '6.0.0' );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 6.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '6.0.0' );
	} // End __wakeup()
} // End Class
?>