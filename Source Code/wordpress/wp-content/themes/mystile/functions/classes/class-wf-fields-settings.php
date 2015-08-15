<?php
// File Security Check.
if ( ! defined( 'ABSPATH' ) ) exit;

class WF_Fields_Settings extends WF_Fields {
	protected $_has_tabs;

	protected $_tabs;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function __construct () {
		parent::__construct();

		$this->_has_tabs = false;

		$this->_tabs = array();

		// This must be present if using fields that require Javascript or styling.
		add_action( 'admin_footer', array( $this, 'maybe_enqueue_field_assets' ) );
	} // End __construct()

	/**
	 * Validate the given data, assuming it is from a textarea field.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function validate_field_textarea ( $v, $k ) {
		// Allow iframe, object and embed tags in textarea fields.
		$allowed = wp_kses_allowed_html( 'post' );
		$allowed['iframe'] = array( 'src' => true, 'width' => true, 'height' => true, 'id' => true, 'class' => true, 'name' => true );
		$allowed['object'] = array( 'src' => true, 'width' => true, 'height' => true, 'id' => true, 'class' => true, 'name' => true );
		$allowed['embed'] = array( 'src' => true, 'width' => true, 'height' => true, 'id' => true, 'class' => true, 'name' => true );

		// Allow script tags in the Google Analytics field.
		if ( is_array( $k ) && isset( $k['id'] ) && in_array( $k['id'], $this->get_script_supported_fields() ) ) {
			$allowed['script'] = array( 'type' => true, 'id' => true, 'class' => true );
		}

		return wp_kses( $v, $allowed );
	} // End validate_field_textarea()

	/**
	 * Return an array of fields which are allowed to support <script> tags.
	 * @access  public
	 * @since   6.0.4
	 * @return  void
	 */
	public function get_script_supported_fields () {
		return (array)apply_filters( 'wf_get_script_supported_fields', array( 'woo_ad_top_adsense', 'woo_google_analytics' ) );
	} // End get_script_supported_fields()

	/**
	 * Initialise the tabs.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function init_tabs () {
		if ( true == $this->_has_tabs ) {
			$this->_create_tabs();
		}
	} // End init_tabs()

	/**
	 * Construct and output HTML markup for the settings tabs.
	 * @access public
	 * @since  1.1.0
	 * @return void
	 */
	public function render_tabs () {
		if ( ! $this->_has_tabs || 0 >= count( $this->_tabs ) ) { return; }

		$html = '';

		$html .= '<ul id="settings-sections" class="subsubsub">' . "\n";

		$sections = array();

		$current_tab = '';
		if ( isset( $_GET['tab'] ) && '' != $_GET['tab'] ) $current_tab = sanitize_title_with_dashes( $_GET['tab'] );

		$count = 0;
		foreach ( $this->_tabs as $k => $v ) {
			$count++;
			$class = 'tab';
			if ( ( '' == $current_tab && 1 == $count ) || $current_tab == $k ) $class .= ' current'; // If no current tab is set, highlight the first one. Otherwise, highlight the current tab.
			$tab = $k;
			$tab = $this->_generate_section_token( $tab, $count );

			$sections[$k] = array( 'href' => remove_query_arg( 'updated', add_query_arg( 'tab', urlencode( $tab ) ) ), 'name' => esc_attr( $v['name'] ), 'class' => $class, 'id' => esc_attr( $k ) );
		}

		$count = 1;
		foreach ( $sections as $k => $v ) {
			$count++;
			$html .= '<li><a href="' . $v['href'] . '"';
			if ( isset( $v['id'] ) && ( $v['id'] != '' ) ) { $html .= ' id="' . esc_attr( $v['id'] ) . '"'; }

			if ( isset( $v['class'] ) && ( $v['class'] != '' ) ) {
				$html .= ' class="' . esc_attr( $v['class'] ) . '"';
			}
			$html .= '>' . esc_attr( $v['name'] ) . '</a>';
			if ( $count <= count( $sections ) ) { $html .= ' | '; }
				$html .= '</li>' . "\n";
		}

		$html .= '</ul><div class="clear"></div>' . "\n";

		echo $html;
	} // End render_tabs()

	/**
	 * Create tabbed navigation based on the sections.
	 * @access private
	 * @since  6.0.0
	 * @return void
	 */
	private function _create_tabs () {
		if ( 0 >= count( $this->_sections ) ) return;
		$tabs = array();
		foreach ( $this->_sections as $k => $v ) {
			$tabs[$k] = $v;
		}
		$this->_tabs = $tabs;
	} // End _create_tabs()
} // End Class
?>