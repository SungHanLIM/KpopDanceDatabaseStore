<?php
// File Security Check.
if ( ! defined( 'ABSPATH' ) ) exit;

class WF_Fields {
	protected $_token;
	protected $_settings;
	protected $_sections;
	protected $_fields;

	protected $_assets_url;

	protected $_has_range;
	protected $_has_imageselector;
	protected $_has_colourpicker;
	protected $_has_calendar;
	protected $_has_masked_input;
	protected $_has_typography;
	protected $_has_upload;
	protected $_has_select;

	protected $_processed_sections;

	protected $_extra_hidden_fields;

	protected $_render_submit_button;

	protected $_tabs;

	protected $_wrappers;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->_token = 'woothemes';
		$this->_sections = array();
		$this->_fields = array();

		$this->_has_range = false;
		$this->_has_imageselector = false;
		$this->_has_colourpicker = false;
		$this->_has_calendar = false;
		$this->_has_masked_input = false;
		$this->_has_typography = false;
		$this->_has_upload = false;
		$this->_has_select = false;

		$this->_processed_sections = array();

		$this->_extra_hidden_fields = array();

		$this->_render_submit_button = true;

		$this->_assets_url = WF()->get_assets_url();

		$this->_wrappers = array();

		// Set default field wrappers.
		$this->__set( 'wrapper_start', '<table class="form-table">' );
		$this->__set( 'wrapper_end', '</table>' );
	} // End __construct()

	/**
	 * Initialise the settings sections and fields.
	 * @access  public
	 * @since   6.0.0
	 * @param   array  			$data Array of settings data to be parsed.
	 * @return  boolean/object 	true if successful, WP_Error object if failed.
	 */
	public function init ( $data = array() ) {
		if ( 0 >= count( $data ) ) return new WP_Error( 'bad_settings_data', __( 'The settings data provided is malformed. Please try again.', 'woothemes' ) );

		$this->init_sections( $data );
		$this->init_fields( $data );
	} // End init()

	/**
	 * Generic setter for protected properties.
	 * @access  public
	 * @since   6.0.0
	 * @param   string $key   The key to denote which property is to be set.
	 * @param   mixed  $value The value to which to set the property (casting should happen on a per-case basis).
	 * @return  mixed
	 */
	public function __set ( $key, $value ) {
		switch ( $key ) {
			case 'has_tabs':
				$this->_has_tabs = (bool)$value;
			break;
			case 'token':
				$this->_token = $value;
			break;
			case 'wrapper_start':
				$this->_wrappers['wrapper_start'] = $value;
			break;
			case 'wrapper_end':
				$this->_wrappers['wrapper_end'] = $value;
			break;
			case 'assets_url':
				$this->_assets_url = esc_url( $value );
			break;
			case 'extra_hidden_fields':
				$this->_extra_hidden_fields = (array)$value;
			break;
			case 'fields':
				$this->_fields = (array)$value;
			break;
			case 'sections':
				$this->_sections = (array)$value;
			break;
			case 'render_submit_button':
				$this->_render_submit_button = (bool)$value;
			break;
			default:
			break;
		}
	} // End __set()

	/**
	 * Generic getter for protected properties.
	 * @access  public
	 * @since   6.0.0
	 * @param   string $key   The key to denote which property is to be set.
	 * @return  mixed
	 */
	public function __get ( $key ) {
		switch ( $key ) {
			case 'has_tabs':
				$value = (bool)$this->_has_tabs;
			break;
			case 'token':
				$value = $this->_token;
			break;
			case 'wrapper_start':
				$value = $this->_wrappers['wrapper_start'];
			break;
			case 'wrapper_end':
				$value = $this->_wrappers['wrapper_end'];
			break;
			case 'assets_url':
				$value = $this->_assets_url;
			break;
			case 'extra_hidden_fields':
				$value = (array)$this->_extra_hidden_fields;
			break;
			case 'fields':
				$value = (array)$this->_fields;
			break;
			case 'sections':
				$value = (array)$this->_sections;
			break;
			case 'render_submit_button':
				$value = (bool)$this->_render_submit_button;
			break;
			default:
			break;
		}
		return $value;
	} // End __get()

	/**
	 * Prepare the given data to be validated.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function prepare_data_for_validation ( $data ) {
		$fields = $this->_fields;

		$prepared_data = array();

		// Bring the fields in a "multi_field" up to the top of the array, for validation.
		$fields = $this->maybe_bubble_up_multi_fields( $fields );

		$accepted_keys = array_keys( $fields );

		if ( 0 < count( $accepted_keys ) ) {
			foreach ( $accepted_keys as $k ) {
				// Last minute preservation of the *-id keys for upload fields.
				if ( in_array( $fields[$k]['type'], array( 'upload', 'upload_min' ) ) ) {
					$accepted_keys[] = $k . '-id';
					$field_data = $fields[$k];
					$field_data['type'] = 'upload_field_id';
					$fields[$k . '-id'] = $field_data;
					$this->_fields[$k . '-id'] = $field_data;
				}
			}
		}

		if ( is_array( $data ) && 0 < count( $data ) ) {
			foreach ( $data as $k => $v ) {
				// Remove any keys which aren't meant to be validated and stored.
				if ( ! isset( $fields[$k] ) ) {
					continue;
				}
				// If the current field type isn't supported, don't validate or store it.
				if ( ! in_array( $fields[$k]['type'], $this->get_supported_fields() ) ) {
					continue;
				}

				if ( in_array( $k, $accepted_keys ) ) {
					$prepared_data[$k] = $v;
				}
			}
		}

		return $prepared_data;
	} // End prepare_data_for_validation()

	/**
	 * Move multi_field fields in the given array, into the top index, to prepare for validation.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function maybe_bubble_up_multi_fields ( $fields ) {
		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				if ( isset( $v['type'] ) && 'multi_field' == $v['type'] && isset( $v['multi_fields'] ) ) {
					foreach ( $v['multi_fields'] as $i => $j ) {
						if ( ! isset( $fields[$i] ) ) {
							$fields[$i] = $j;
							unset( $fields[$k] );
						}
					}
				}
			}
		}
		return $fields;
	} // End maybe_bubble_up_multi_fields()

	/**
	 * Validate the given field data.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function validate_fields ( $data, $section = '' ) {
		if ( ! is_array( $data ) || 0 >= count( $data ) ) return new WP_Error( 'bad_field_data', __( 'The provided field data is invalid and cannot be validated.', 'woothemes' ) );

		$sections_to_scan = array();

		// No section has been applied. Assume it's the first.
		if ( '' == $section && 'all_fields' != $section ) {
			$all_sections = $this->_sections;
			if ( is_array( $all_sections ) && 0 < count( $all_sections ) ) {
				foreach ( $all_sections as $k => $v ) {
					$section = $k;
					break;
				}
			}
		}

		// Store the current top section.
		$sections_to_scan[] = $section;
		// Check if we have sub-sections.
		if ( isset( $this->_sections[$section]['children'] ) && 0 < count( (array)$this->_sections[$section]['children'] ) ) {
			foreach ( $this->_sections[$section]['children'] as $k => $v ) {
				$sections_to_scan[] = $v['token'];
			}
		}

		// Retrieve all fields in this current screen (main and sub-sections).
		$fields_by_section = array();

		foreach ( $sections_to_scan as $k => $v ) {
			$field_data = $this->_get_fields_by_section( $v );
			$fields_by_section = array_merge( $fields_by_section, $field_data );
		}

		// Make sure checkboxes are taken care of.
		// As well as multicheck fields.
		if ( 0 < count( $fields_by_section ) ) {
			foreach ( $fields_by_section as $k => $v ) {
				if ( ! in_array( $v['type'], array( 'checkbox', 'multicheck', 'multicheck2' ) ) ) {
					unset( $fields_by_section[$k] );
				}
			}
		}

		// If we have fields left, merge them in.
		if ( 0 < count( $fields_by_section ) ) {
			foreach ( $fields_by_section as $k => $v ) {
				if ( ! isset( $data[$k] ) ) {
					$data[$k] = '';
				}
			}
		}

		$data = $this->prepare_data_for_validation( $data );

		$fields = $this->_fields;

		// Bring the fields in a "multi_field" up to the top of the array, for validation.
		$fields = $this->maybe_bubble_up_multi_fields( $fields );

		if ( 0 < count( $data ) ) {
			foreach ( $data as $k => $v ) {
				if ( ! isset( $fields[$k] ) ) continue;

				// Determine if a method is available for validating this field.
				$method = 'validate_field_' . $fields[$k]['type'];
				if ( ! method_exists( $this, $method ) ) {
					if ( true == (bool)apply_filters( 'wf_validate_field_' . $fields[$k]['type'] . '_use_default', true ) ) {
						$method = 'validate_field_text';
					} else {
						$method = '';
					}
				}

				// If we have an internal method for validation, filter and apply it.
				if ( '' != $method ) {
					add_filter( 'wf_validate_field_' . $fields[$k]['type'], array( $this, $method ), 10, 2 );
				}

				$method_output = apply_filters( 'wf_validate_field_' . $fields[$k]['type'], $v, $fields[$k] );
				// $method_output = apply_filters( 'wf_validate_field_' . $k, $v, $fields[$k] );

				if ( is_wp_error( $method_output ) ) {
					// if ( defined( 'WP_DEBUG' ) || true == constant( 'WP_DEBUG' ) ) print_r( $method_output ); // Add better error display.
				} else {
					$data[$k] = $method_output;
				}
			}
		}

		return $data;
	} // End validate_fields()

	/**
	 * Validate the given data, assuming it is from a text input field.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function validate_field_text ( $v ) {
		return (string)wp_kses_post( $v );
	} // End validate_field_text()

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

		return wp_kses( $v, $allowed );
	} // End validate_field_textarea()

	/**
	 * Validate the given data, assuming it is from a checkbox input field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_checkbox ( $v ) {
		if ( 'true' != $v ) {
			return 'false';
		} else {
			return 'true';
		}
	} // End validate_field_checkbox()

	/**
	 * Validate the given data, assuming it is from a multicheck field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_multicheck ( $v ) {
		$v = (array) $v;

		$v = array_map( 'esc_attr', $v );

		return $v;
	} // End validate_field_multicheck()

	/**
	 * Validate the given data, assuming it is from a multicheck2 field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_multicheck2 ( $v ) {
		$v = (array) $v;

		$v = array_map( 'esc_attr', $v );

		return $v;
	} // End validate_field_multicheck2()

	/**
	 * Validate the given data, assuming it is from a slider field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_slider ( $v ) {
		$v = floatval( $v );

		return $v;
	} // End validate_field_slider()

	/**
	 * Validate the given data, assuming it is from a URL field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_url ( $v ) {
		return trim( esc_url( $v ) );
	} // End validate_field_url()

	/**
	 * Validate the given data, assuming it is from a upload field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_upload ( $v ) {
		return trim( esc_url( $v ) );
	} // End validate_field_upload()

	/**
	 * Validate the given data, assuming it is from a upload_min field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_upload_min ( $v ) {
		return trim( esc_url( $v ) );
	} // End validate_field_upload()

	/**
	 * Validate the given data, assuming it is from a upload_field_id field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_upload_field_id ( $v ) {
		return intval( $v );
	} // End validate_field_upload_field_id()

	/**
	 * Validate the given data, assuming it is from a typography field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_typography ( $v ) {
		$defaults = array( 'size' => '', 'unit' => '', 'face' => '', 'style' => '', 'color' => '' );
		$v = wp_parse_args( $v, $defaults );

		if ( isset( $v['size_' . $v['unit']] ) ) {
			$v['size'] = $v['size_' . $v['unit']];
		}

		foreach ( $v as $i => $j ) {
			if ( ! in_array( $i, array_keys( $defaults ) ) ) {
				unset( $v[$i] );
			}
		}

		$v = array_map( 'strip_tags', $v );
		$v = array_map( 'stripslashes', $v );

		return $v;
	} // End validate_field_typography()

	/**
	 * Validate the given data, assuming it is from a border field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_border ( $v ) {
		$defaults = array( 'width' => '', 'style' => '', 'color' => '' );
		$v = wp_parse_args( $v, $defaults );

		foreach ( $v as $i => $j ) {
			if ( ! in_array( $i, array_keys( $defaults ) ) ) {
				unset( $v[$i] );
			}
		}

		$v = array_map( 'esc_html', $v );

		return $v;
	} // End validate_field_border()

	/**
	 * Validate the given data, assuming it is from a timestamp field.
	 * @access public
	 * @since  6.0.0
	 * @param  string $v
	 * @return string
	 */
	public function validate_field_timestamp ( $v ) {
		$defaults = array( 'date' => '', 'hour' => '', 'minute' => '' );
		$v = wp_parse_args( $v, $defaults );

		foreach ( $v as $i => $j ) {
			if ( ! in_array( $i, array_keys( $defaults ) ) ) {
				unset( $v[$i] );
			}
		}

		$date = $v['date'];

		$hour = $v['hour'];
		$minute = $v['minute'];
		// $second = $output[$option_array['id']]['second'];
		$second = '00';

		$day = substr( $date, 3, 2 );
		$month = substr( $date, 0, 2 );
		$year = substr( $date, 6, 4 );

		$timestamp = mktime( $hour, $minute, $second, $month, $day, $year );

		return esc_attr( $timestamp );
	} // End validate_field_timestamp()

	/**
	 * Render the various sections and their corresponding fields.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function render_sections () {
		if ( 0 >= count( $this->_sections ) ) return;
		$html = '';
		$current_section = '';
		if ( isset( $_GET['tab'] ) && '' != $_GET['tab'] ) {
			$current_section = sanitize_title_with_dashes( $_GET['tab'] );
		} else {
			// Grab the key for the first section, using a short loop.
			if ( 0 < count( $this->_sections ) ) {
				foreach ( $this->_sections as $k => $v ) {
					$current_section = $k;
					break;
				}
			}
		}

		if ( isset( $this->_sections[$current_section] ) ) {
			$html .= $this->render_single_section( $current_section, $this->_sections[$current_section] );
		}

		echo $html;
	} // End render_sections()

	/**
	 * Render a single section if fields.
	 * @access  public
	 * @since   6.0.0
	 * @param   string $key  The key/token.
	 * @param   array  $args Arguments pertaining to this section.
	 * @return  string       Rendered HTML markup for the section.
	 */
	public function render_single_section ( $key, $args, $heading_level = 2 ) {
		if ( in_array( $key, $this->_processed_sections ) ) return; // Don't process a section more than once.
		if ( 6 < intval( $heading_level ) ) $heading_level = 2; // Set a default heading level.

		$fields = $this->_get_fields_by_section( $key );
		$html = '';
		$html .= '<div id="' . esc_attr( $key ) . '" class="settings-section">' . "\n";
		if ( isset( $args['name'] ) ) {
			$html .= '<h' . intval( $heading_level ) . ' class="section-title">' . $args['name'] . '</h' . intval( $heading_level ) . '>' . "\n";
		}
		$html .= $this->__get( 'wrapper_start' );
		if ( 0 < count( $fields ) ) {
			$html .= $this->render_fields( $fields );
		}
		$html .= $this->__get( 'wrapper_end' );

		// Cater for child sections.
		if ( isset( $args['children'] ) && is_array( $args['children'] ) && 0 < count( $args['children'] ) ) {
			foreach ( $args['children'] as $k => $v ) {
				$html .= $this->render_single_section( $k, $v, 3 );
			}
		}
		$html .= '</div><!--/#' . esc_attr( $key ) . ' .settings-section-->' . "\n";
		return $html;
	} // End render_single_section()

	/**
	 * Render the various fields based on the given field data.
	 * @access  public
	 * @since   6.0.0
	 * @param   array $data Provided field data.
	 * @return  void
	 */
	public function render_fields ( $data, $mode = 'table' ) {
		if ( 0 >= count( $data ) ) return;
		$html = '';
		$no_header = $this->get_no_label_field_types();
		$this->enqueue_media_setup(); // Make sure wp_enqueue_media() is being loaded well before the footer, so our underscore.js templates get loaded.
		foreach ( $data as $k => $v ) {
			$colspan = '';
			if ( in_array( $v['type'], $no_header ) ) $colspan = ' colspan="2"'; // If we're not displaying a header, span the table cell by two columns.
			$field = $this->render_single_field( $k, $v );
			$html .= '<tr>' . "\n";
			if ( '' == $colspan ) $html .= '<th>' . $v['name'] . '</th>' . "\n";
			$html .= '<td' . $colspan . '><span class="wf-field wf-field-' . esc_attr( $v['type'] ) . '">' . $field . '</span></td>' . "\n";
			$html .= '</tr>' . "\n";
		}
		return $html;
	} // End render_fields()

	/**
	 * Render the HTML markup for a single field.
	 * @access  public
	 * @since   6.0.0
	 * @param   string $key  The key to be used for the field.
	 * @param   array  $args Arguments pertaining to the field.
	 * @return  string
	 */
	public function render_single_field ( $key, $args ) {
		$html = '';
		if ( ! in_array( $args['type'], $this->get_supported_fields() ) ) return ''; // Supported field type sanity check.

		// Make sure we have some kind of default, if the key isn't set.
		if ( ! isset( $args['std'] ) ) $args['std'] = '';

		$method = 'render_field_' . $args['type'];
		if ( ! method_exists( $this, $method ) ) $method = 'render_field_text';

		$method_output = $this->$method( $key, $args );
		if ( is_wp_error( $method_output ) ) {
			// if ( defined( 'WP_DEBUG' ) || true == constant( 'WP_DEBUG' ) ) print_r( $method_output ); // Add better error display.
		} else {
			$html .= $method_output;
		}

		// Output the description, if the current field allows it.
		if ( isset( $args['type'] ) && ! in_array( $args['type'], (array)apply_filters( 'wf_no_description_fields', array( 'checkbox', 'info' ) ) ) ) {
			if ( isset( $args['desc'] ) ) {
				$description = '<p class="description">' . wp_kses_post( $args['desc'] ) . '</p>' . "\n";
				if ( in_array( $args['type'], (array)apply_filters( 'wf_newline_description_fields', array( 'textarea', 'select', 'select2', 'slider', 'images', 'info', 'border', 'typography', 'color', 'upload', 'calendar', 'timestamp', 'select_taxonomy', 'multi_field' ) ) ) ) {
					$description = wpautop( $description );
				}
				$html .= $description;
			}
		}

		return $html;
	} // End render_single_field()

	/**
	 * Render HTML markup for the "text" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_text ( $key, $args ) {
		$html = '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" value="' . esc_attr( $this->get_value( $key, $args['std'] ) ) . '" />' . "\n";
		return $html;
	} // End render_field_text()

	/**
	 * Render HTML markup for the "multi_field" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_multi_field ( $key, $args ) {
		$html = '';
		if ( isset( $args['multi_fields'] ) && 0 < count( $args['multi_fields'] ) ) {
			foreach ( $args['multi_fields'] as $k => $v ) {
				$html .= '<label for="' . esc_attr( $k ) . '">' . esc_html( $v['name'] ) . '</label>' . "\n";
				$html .= $this->render_single_field( $k, $v );
			}
		}
		return $html;
	} // End render_field_multi_field()

	/**
	 * Render HTML markup for the "typography" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_typography ( $key, $args ) {
		$this->_has_colourpicker = true;
		$this->_has_typography = true;
		$this->_has_select = true;

		$html = '';

		$defaults = array(
			'size' => get_option( $key . '_size', '' ),
			'unit' => get_option( $key . '_unit', '' ),
			'face' => get_option( $key . '_face', '' ),
			'style' => get_option( $key . '_style', '' ),
			'color' => get_option( $key . '_color', '' )
			);

		if ( 0 < count( $defaults ) && isset( $args['std'] ) && is_array( $args['std'] ) ) {
			foreach ( $defaults as $k => $v ) {
				if ( '' == $v && isset( $args['std'][$k] ) ) {
					$defaults[$k] = $args['std'][$k];
				}
			}
		}

		$value = $this->get_value( $key, $defaults );

		// Make sure we place our default values in if the key is empty. wp_parse_args() didn't seem to work for this.
		foreach ( $defaults as $k => $v ) {
			if ( ! isset( $value[$k] ) ) {
				$value[$k] = $defaults[$k];
			} else {
				if ( '' == $value[$k] ) {
					$value[$k] = $defaults[$k];
				}
			}
		}

		// Make sure the size fields are set correctly.
		if ( ! isset( $value['size'] ) ) {
			$value['size'] = $value['size_' . $value['unit']];
		}

		$unit = $value['unit'];

		$html .= '<span class="unit-container ' . esc_attr( 'unit-' . sanitize_title_with_dashes( $unit ) ) . '">' . "\n";

		/* Size in Pixels */
		$html .= '<select class="woo-typography woo-typography-size woo-typography-size-px hide-if-em" name="'. esc_attr( $key . '[size_px]' ) . '" id="'. esc_attr( $key . '_size' ) . '">' . "\n";
		for ( $i = 9; $i < floatval( apply_filters( 'wf_fields_typography_font_size_px_upper_limit', 71 ) ); $i++ ) {
			$html .= '<option value="'. esc_attr( $i ) .'" ' . selected( floatval( $value['size'] ), $i, false ) . '>'. esc_html( $i ) . '</option>' . "\n";
		}
		$html .= '</select>' . "\n";

		/* Size in EMs */
		$html .= '<select class="woo-typography woo-typography-size woo-typography-size-em hide-if-px" name="'. esc_attr( $key . '[size_em]' ) . '" id="'. esc_attr( $key . '_size' ) . '">' . "\n";
		$em = 0;
		for ( $i = 0; $i < 39; $i++ ) {
			if ( $i <= 24 )   // up to 2.0em in 0.1 increments
				$em = $em + 0.1;
			elseif ( $i >= 14 && $i <= 24 )  // Above 2.0em to 3.0em in 0.2 increments
				$em = $em + 0.2;
			elseif ( $i >= 24 )  // Above 3.0em in 0.5 increments
				$em = $em + 0.5;

			$active = '';
			if( strval( $em ) == $value['size'] ) {
				$active = 'selected="selected"';
			}
			$html .= '<option value="' . esc_attr( floatval( $em ) ) . '" ' . $active . '>' . esc_html( $em ) . '</option>';
		}
		$html .= '</select>' . "\n";

		/* Font Unit */
		$unit = $value['unit'];
		$em = ''; $px = '';
		if ( 'em' == $unit ) { $em = 'selected="selected"'; }
		if ( 'px' == $unit ) { $px = 'selected="selected"'; }
		$html .= '<select class="woo-typography woo-typography-unit" name="'. esc_attr( $key .'[unit]' ) . '" id="'. esc_attr( $key . '_unit' ) . '">' . "\n";
		$html .= '<option value="px" ' . $px . '">px</option>' . "\n";
		$html .= '<option value="em" ' . $em . '>em</option>' . "\n";
		$html .= '</select>' . "\n";

		/* Weights */
		$font_weights = (array) apply_filters( 'wf_fields_typography_font_weights', array( '300' => __( 'Thin', 'woothemes' ), '300 italic' => __( 'Thin Italic', 'woothemes' ), 'normal' => __( 'Normal', 'woothemes' ), 'italic' => __( 'Italic', 'woothemes' ), 'bold' => __( 'Bold', 'woothemes' ), 'bold italic' => __( 'Bold/Italic', 'woothemes' ) ) );

		if ( 0 < count( $font_weights ) ) {
			$html .= '<select class="woo-typography woo-typography-font-weight woo-typography-style" name="'. esc_attr( $key . '[style]' ) . '" id="'. esc_attr( $key . '_style' ) . '">' . "\n";
			foreach ( $font_weights as $k => $v ) {
				$html .= '<option value="' . esc_attr( $k ) . '" ' . selected( $value['style'], $k, false ) . '>' . esc_html( $v ) . '</option>' . "\n";
			}
			$html .= '</select>' . "\n";
		}

		/* Font Face */
		$font_faces = wf_get_system_fonts();
		$google_fonts = wf_get_google_fonts();
		if ( 0 < count( $google_fonts ) ) {
			$font_faces[''] = __( '-- Google WebFonts --', 'woothemes' );
			$google_fonts_array = array();
			foreach ( $google_fonts as $k => $v ) {
				$google_fonts_array[$v['name']] = $v['name'];
			}
			asort( $google_fonts_array );
			$font_faces = array_merge( $font_faces, $google_fonts_array );
		}

		if ( 0 < count( $font_faces ) ) {
			$test_cases = wf_get_system_fonts_test_cases();

			$html .= '<select class="woo-typography woo-typography-font-face woo-typography-face" name="'. esc_attr( $key . '[face]' ) . '" id="'. esc_attr( $key . '_face' ) . '">' . "\n";
			foreach ( $font_faces as $k => $v ) {
				$selected = '';
				// If one of the fonts requires a test case, use that value. Otherwise, use the key as the test case.
				if ( in_array( $k, array_keys( $test_cases ) ) ) {
					$value_to_test = $test_cases[$k];
				} else {
					$value_to_test = $k;
				}
				if ( $this->_test_typeface_against_test_case( $value['face'], $value_to_test ) ) $selected = ' selected="selected"';
				$html .= '<option value="' . esc_attr( $k ) . '" ' . $selected . '>' . esc_html( $v ) . '</option>' . "\n";
			}
			$html .= '</select>' . "\n";
		}

		/* Border Color */
		$html .= '<input id="' . esc_attr( $key . '_color' ) . '" name="' . esc_attr( $key . '[color]' ) . '" size="40" type="text" class="woo-typography-color colour" value="' . esc_attr( $value['color'] ) . '" />' . "\n";

		$html .= '</span>' . "\n";
		return $html;
	} // End render_field_typography()

	/**
	 * Test whether or not a typeface has been selected for a "typography" field.
	 * @access  protected
	 * @since   6.0.2
	 * @param   string $face      The noble warrior (typeface) to be tested.
	 * @param   string $test_case The test case. Does the warrior pass the ultimate test and reep eternal glory?
	 * @return  bool       		  Whether or not eternal glory shall be achieved by the warrior.
	 */
	protected function _test_typeface_against_test_case ( $face, $test_case ) {
		$response = false;

		$face = stripslashes( str_replace( '"', '', str_replace( '&quot;', '', $face ) ) );

		$parts = explode( ',', $face );

		if ( $test_case == $parts[0] ) {
			$response = true;
		}

		return $response;
	} // End _test_typeface_against_test_case()

	/**
	 * Render HTML markup for the "border" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_border ( $key, $args ) {
		$this->_has_colourpicker = true;

		$html = '';
		$defaults = array(
			'width' => get_option( $key . '_width', '' ),
			'style' => get_option( $key . '_style', '' ),
			'color' => get_option( $key . '_color', '' )
			);

		if ( 0 < count( $defaults ) && isset( $args['std'] ) && is_array( $args['std'] ) ) {
			foreach ( $defaults as $k => $v ) {
				if ( '' == $v && isset( $args['std'][$k] ) ) {
					$defaults[$k] = $args['std'][$k];
				}
			}
		}

		$value = $this->get_value( $key, $defaults );

		// Make sure we place our default values in if the key is empty. wp_parse_args() didn't seem to work for this.
		foreach ( $defaults as $k => $v ) {
			if ( ! isset( $value[$k] ) ) {
				$value[$k] = $defaults[$k];
			} else {
				if ( '' == $value[$k] ) {
					$value[$k] = $defaults[$k];
				}
			}
		}

		/* Border Width */
		$html .= '<select class="woo-border woo-border-width" name="'. esc_attr( $key . '[width]' ) . '" id="'. esc_attr( $key . '_width' ) . '">' . "\n";
		for ( $i = 0; $i < intval( apply_filters( 'wf_fields_border_width_upper_limit', 21 ) ); $i++ ) {
			$html .= '<option value="' . esc_attr( $i ) . '" ' . selected( intval( $value['width'] ), $i, false ) . '>'. esc_html( $i ) . 'px</option>' . "\n";
		}
		$html .= '</select>' . "\n";

		/* Border Style */
		$border_styles = (array) apply_filters( 'wf_fields_border_styles', array( 'solid' => __( 'Solid', 'woothemes' ), 'dashed' => __( 'Dashed', 'woothemes' ), 'dotted' => __( 'Dotted', 'woothemes' ) ) );

		if ( 0 < count( $border_styles ) ) {
			$html .= '<select class="woo-border woo-border-style" name="'. esc_attr( $key . '[style]' ) . '" id="'. esc_attr( $key . '_style' ) . '">' . "\n";
			foreach ( $border_styles as $k => $v ) {
				$html .= '<option value="' . esc_attr( $k ) . '" ' . selected( $value['style'], $k, false ) . '>' . esc_html( $v ) . '</option>' . "\n";
			}
			$html .= '</select>' . "\n";
		}

		/* Border Color */
		$html .= '<input id="' . esc_attr( $key .'_color' ) . '" name="' . esc_attr( $key . '[color]' ) . '" size="40" type="text" class="colour" value="' . esc_attr( $value['color'] ) . '" />' . "\n";
		return $html;
	} // End render_field_border()

	/**
	 * Render HTML markup for the "radio" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_radio ( $key, $args ) {
		$html = '';
		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			$html = '';
			foreach ( $args['options'] as $k => $v ) {
				$html .= '<input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( $k ) . '"' . checked( esc_attr( $this->get_value( $key, $args['std'] ) ), $k, false ) . ' /> ' . esc_html( $v ) . '<br />' . "\n";
			}
		}
		return $html;
	} // End render_field_radio()

	/**
	 * Render HTML markup for the "textarea" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_textarea ( $key, $args ) {
		// Explore how best to escape this data, as esc_textarea() strips HTML tags, it seems.
		$html = '<textarea id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" cols="42" rows="5">' . stripslashes( $this->get_value( $key, $args['std'] ) ) . '</textarea>' . "\n";
		return $html;
	} // End render_field_textarea()

	/**
	 * Render HTML markup for the "multicheck" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_multicheck ( $key, $args ) {
		$html = '';
		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			// Attempt to preserve legacy "multicheck" field data, which was stored in an unorthodox manner. Retrieve it in our new format.
			$multicheck_legacy_defaults = $this->maybe_create_multicheck_legacy_defaults( $key, $args['options'] );

			$value = $this->get_value( $key, $multicheck_legacy_defaults );

			$html = '<div class="multicheck-container" style="height: 100px; overflow-y: auto;">' . "\n";
			foreach ( $args['options'] as $k => $v ) {
				$checked = '';

				if ( in_array( $v, (array)$value ) ) { $checked = ' checked="checked"'; }
				$html .= '<input type="checkbox" name="' . esc_attr( $key ) . '[]" class="multicheck multicheck-' . esc_attr( $key ) . '" value="' . esc_attr( $v ) . '"' . $checked . ' /> ' . esc_html( $v ) . '<br />' . "\n";
			}
			$html .= '</div>' . "\n";
		}
		return $html;
	} // End render_field_multicheck()

	/**
	 * Render HTML markup for the "multicheck2" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_multicheck2 ( $key, $args ) {
		$html = '';
		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			$value = $this->get_value( $key, array() );

			$html = '<div class="multicheck-container" style="height: 100px; overflow-y: auto;">' . "\n";
			foreach ( $args['options'] as $k => $v ) {
				$checked = '';

				if ( in_array( $k, (array)$value ) ) { $checked = ' checked="checked"'; }
				$html .= '<input type="checkbox" name="' . esc_attr( $key ) . '[]" class="multicheck multicheck-' . esc_attr( $key ) . '" value="' . esc_attr( $k ) . '"' . $checked . ' /> ' . esc_html( $v ) . '<br />' . "\n";
			}
			$html .= '</div>' . "\n";
		}
		return $html;
	} // End render_field_multicheck2()

	/**
	 * Render HTML markup for the "checkbox" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_checkbox ( $key, $args ) {
		$has_description = false;
		$html = '';
		if ( isset( $args['desc'] ) ) {
			$has_description = true;
			$html .= '<label for="' . esc_attr( $key ) . '">' . "\n";
		}
		$html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="checkbox" value="true"' . checked( esc_attr( $this->get_value( $key, $args['std'] ) ), 'true', false ) . ' />' . "\n";
		if ( $has_description ) {
			$html .= wp_kses_post( $args['desc'] ) . '</label>' . "\n";
		}
		return $html;
	} // End render_field_checkbox()

	/**
	 * Render HTML markup for the "info" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_info ( $key, $args ) {
		$html = '<div id="' . esc_attr( $key ) . '" class="woo-notice">' . "\n";
		if ( '' != $args['desc'] ) $html .= '<p>' . wp_kses_post( $args['desc'] ) . '</p>' . "\n";
		$html .= '</div>' . "\n";
		return $html;
	} // End render_field_info()

	/**
	 * Render HTML markup for the "select" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_select ( $key, $args ) {
		$this->_has_select = true;

		$html = '';
		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			$html .= '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . "\n";
				foreach ( $args['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $v ) . '"' . selected( esc_attr( $this->get_value( $key, $args['std'] ) ), $v, false ) . '>' . esc_html( $v ) . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
		}
		return $html;
	} // End render_field_select()

	/**
	 * Render HTML markup for the "select2" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_select2 ( $key, $args ) {
		$this->_has_select = true;

		$html = '';
		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			$html .= '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '">' . "\n";
				foreach ( $args['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $this->get_value( $key, $args['std'] ) ), $k, false ) . '>' . esc_html( $v ) . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
		}
		return $html;
	} // End render_field_select2()

	/**
	 * Render HTML markup for the "select_taxonomy" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_select_taxonomy ( $key, $args ) {
		$this->_has_select = true;

		$defaults = array(
			'show_option_all'    => '',
			'show_option_none'   => '',
			'orderby'            => 'ID',
			'order'              => 'ASC',
			'show_count'         => 0,
			'hide_empty'         => 1,
			'child_of'           => 0,
			'exclude'            => '',
			'selected'           => $this->get_value( $key, $args['std'] ),
			'hierarchical'       => 1,
			'class'              => 'postform',
			'depth'              => 0,
			'tab_index'          => 0,
			'taxonomy'           => 'category',
			'hide_if_empty'      => false,
			'walker'             => ''
        );

		if ( ! isset( $args['options'] ) ) {
			$args['options'] = array();
		}

		$args['options'] = wp_parse_args( $args['options'], $defaults );

		$args['options']['echo'] = false;
		$args['options']['name'] = esc_attr( $key );
		$args['options']['id'] = esc_attr( $key );

		$html = '';
		$html .= wp_dropdown_categories( $args['options'] );
		return $html;
	} // End render_field_select_taxonomy()

	/**
	 * Render HTML markup for the "slider" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_slider ( $key, $args ) {
		$this->_has_range = true;

		$html = '';
		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			$html .= '<select id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" class="range-input">' . "\n";
				foreach ( $args['options'] as $k => $v ) {
					$html .= '<option value="' . esc_attr( $k ) . '"' . selected( esc_attr( $this->get_value( $key, $args['std'] ) ), $k, false ) . '>' . esc_html( $v ) . '</option>' . "\n";
				}
			$html .= '</select>' . "\n";
		}
		return $html;
	} // End render_field_slider()

	/**
	 * Render HTML markup for the "masked_input" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_masked_input ( $key, $args ) {
		$this->_has_masked_input = true;

		$placeholder = '99:99';
		if ( isset( $args['options']['placeholder'] ) ) $placeholder = $args['options']['placeholder'];

		$html = '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" value="' . esc_attr( $this->get_value( $key, $args['std'] ) ) . '" class="woo-input-masked" data-placeholder="' . esc_attr( $placeholder ) . '" />' . "\n";
		return $html;
	} // End render_field_masked_input()

	/**
	 * Render HTML markup for the "time_masked" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_time_masked ( $key, $args ) {
		$this->_has_masked_input = true;

		$placeholder = '99:99';
		if ( isset( $args['options']['placeholder'] ) ) $placeholder = $args['options']['placeholder'];

		$html = '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" value="' . esc_attr( $this->get_value( $key, $args['std'] ) ) . '" class="woo-input-masked" data-placeholder="' . esc_attr( $placeholder ) . '" />' . "\n";
		return $html;
	} // End render_field_time_masked()

	/**
	 * Render HTML markup for the "time" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_time ( $key, $args ) {
		$this->_has_masked_input = true;

		$placeholder = '99:99';
		if ( isset( $args['options']['placeholder'] ) ) $placeholder = $args['options']['placeholder'];

		$html = '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" value="' . esc_attr( $this->get_value( $key, $args['std'] ) ) . '" class="woo-input-masked" data-placeholder="' . esc_attr( $placeholder ) . '" />' . "\n";
		return $html;
	} // End render_field_time()

	/**
	 * Render HTML markup for the "calendar" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_calendar ( $key, $args ) {
		$this->_has_calendar = true;

		$val = $this->get_value( $key );
		if ( '' == $val ) $val = time();

		$html = '<input type="hidden" name="datepicker-image" value="' . esc_url( admin_url( 'images/date-button.gif' ) ) . '" />' . "\n";
		$html .= '<input class="woo-input-calendar" type="text" name="' . esc_attr( $key ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( date( 'm/d/Y', $val ) ) . '">';
		return $html;
	} // End render_field_calendar()

	/**
	 * Render HTML markup for the "color" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_color ( $key, $args ) {
		$this->_has_colourpicker = true;
		$value = $this->get_value( $key, $args['std'] );
		$html = '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="40" type="text" class="colour" value="' . esc_attr( $value ) . '" />' . "\n";
		return $html;
	} // End render_field_color()

	/**
	 * Render HTML markup for the "images" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_images ( $key, $args ) {
		$this->_has_imageselector = true;

		if ( isset( $args['options'] ) && ( 0 < count( (array)$args['options'] ) ) ) {
			$html = '';
			foreach ( $args['options'] as $k => $v ) {
				$image = '<img src="' . esc_url( $v ) . '" alt="' . esc_attr( $k ) . '" title="' . esc_attr( $k ) . '" class="radio-image-thumb" />';
				$html .= '<input type="radio" name="' . esc_attr( $key ) . '" value="' . esc_attr( $k ) . '" class="radio-images"' . checked( esc_attr( $this->get_value( $key, $args['std'] ) ), $k, false ) . ' /> ' . $image . "\n";
			}
		}
		return $html;
	} // End render_field_images()

	/**
	 * Render HTML markup for the "upload" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_upload ( $key, $args ) {
		$this->_has_upload = true;

		$url = $this->get_value( $key, $args['std'] );
		$id = $this->get_value( $key . '-id', 0 );
		$placeholder = apply_filters( 'wf_placeholder_image_url', get_template_directory_uri() . '/functions/assets/images/placeholder.png' );
		$class = ' no-image';
		if ( '' != $url || 0 < intval( $id ) ) $class = ' has-image';

		$html = '<span class="upload-field">' . "\n";
		$html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" size="34" type="text" class="input-upload" value="' . esc_attr( $url ) . '" /> <a href="#" class="button" data-uploader-title="' . esc_attr( sprintf( __( 'Select %s', 'woothemes' ), $args['name'] ) ) . '" data-uploader-button-text="' . esc_attr( sprintf( __( 'Use image as %s', 'woothemes' ), $args['name'] ) ) . '">' . __( 'Upload', 'woothemes' ) . '</a>' . "\n";
		$html .= '<input id="' . esc_attr( $key ) . '-id" name="' . esc_attr( $key ) . '-id" type="hidden" class="input-upload-id" value="' . esc_attr( $id ) . '" /> ' . "\n";
		$html .= '</span>' . "\n";

		$html .= '<div class="image-preview' . esc_attr( $class ) . '">' . "\n";
		$html .= '<img src="' . esc_url( $url ) . '" data-placeholder="' . esc_url( $placeholder ) . '" />' . "\n";
		$html .= '<a href="#" class="remove">' . sprintf( __( 'Remove %s', 'woothemes' ), $args['name'] ) . '</a>' . "\n";
		$html .= '</div><!--/.image-preview-->' . "\n";

		return $html;
	} // End render_field_upload()

	/**
	 * Render HTML markup for the "upload_min" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_upload_min ( $key, $args ) {
		$this->_has_upload = true;

		$url = $this->get_value( $key, $args['std'] );
		$id = $this->get_value( $key . '-id', 0 );
		$placeholder = WF()->get_placeholder_image_url();
		$class = ' no-image';
		if ( '' != $url || 0 < intval( $id ) ) $class = ' has-image';

		$html = '<span class="upload-field">' . "\n";
		$html .= '<input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="hidden" class="input-upload" value="' . esc_attr( $url ) . '" /> <a href="#" class="button" data-uploader-title="' . esc_attr( sprintf( __( 'Select %s', 'woothemes' ), $args['name'] ) ) . '" data-uploader-button-text="' . esc_attr( sprintf( __( 'Use image as %s', 'woothemes' ), $args['name'] ) ) . '">' . __( 'Upload', 'woothemes' ) . '</a>' . "\n";
		$html .= '<input id="' . esc_attr( $key ) . '-id" name="' . esc_attr( $key ) . '-id" type="hidden" class="input-upload-id" value="' . esc_attr( $id ) . '" /> ' . "\n";
		$html .= '</span>' . "\n";

		$html .= '<div class="image-preview' . esc_attr( $class ) . '">' . "\n";
		$html .= '<img src="' . esc_url( $url ) . '" data-placeholder="' . esc_url( $placeholder ) . '" />' . "\n";
		$html .= '<a href="#" class="remove">' . sprintf( __( 'Remove %s', 'woothemes' ), $args['name'] ) . '</a>' . "\n";
		$html .= '</div><!--/.image-preview-->' . "\n";

		return $html;
	} // End render_field_upload_min()

	/**
	 * Render HTML markup for the "timestamp" field type.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $key  The unique ID of this field.
	 * @param   array $args  Arguments used to construct this field.
	 * @return  string       HTML markup for the field.
	 */
	protected function render_field_timestamp ( $key, $args ) {
		$this->_has_calendar = true;

		$val = $this->get_value( $key );
		if ( '' == $val ) $val = time();

		$html = '<input type="hidden" name="datepicker-image" value="' . esc_url( admin_url( 'images/date-button.gif' ) ) . '" />' . "\n";
		$html .= '<input class="woo-input-calendar" type="text" name="' . esc_attr( $key . '[date]' ) . '" id="' . esc_attr( $key ) . '" value="' . esc_attr( date( 'm/d/Y', $val ) ) . '">';
		$html .= '<span class="time-selectors">' . "\n";
		$html .= ' <span class="woo-timestamp-at">' . __( '@', 'woothemes' ) . '</span> ';

		$html .= '<select name="' . esc_attr( $key . '[hour]' ) . '" class="woo-select-timestamp">' . "\n";
			for ( $i = 0; $i <= 23; $i++ ) {

				$j = $i;
				if ( $i < 10 ) {
					$j = '0' . $i;
				}

				$html .= '<option value="' . esc_attr( $i ) . '"' . selected( date( 'H', $val ), $j, false ) . '>' . esc_html( $j ) . '</option>' . "\n";
			}
		$html .= '</select>' . "\n";

		$html .= '<select name="' . $key . '[minute]" class="woo-select-timestamp">' . "\n";
			for ( $i = 0; $i <= 59; $i++ ) {

				$j = $i;
				if ( $i < 10 ) {
					$j = '0' . $i;
				}

				$html .= '<option value="' . esc_attr( $i ) . '"' . selected( date( 'i', $val ), $j, false ) .'>' . esc_html( $j ) . '</option>' . "\n";
			}
		$html .= '</select>' . "\n";
		$html .= '</span><!--/.time-selectors-->' . "\n";
		return $html;
	} // End render_field_timestamp()

	/**
	 * Render the current fields state. This is the main function used for HTML output.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function render () {
		echo '<form name="' . esc_attr( $this->_token ) . '-form" method="post" enctype="multipart/form-data">' . "\n";
		wp_nonce_field( $this->_token . '_nonce', $this->_token . '_nonce' );
		$this->maybe_render_extra_hidden_fields();
		$this->render_sections();
		if ( true == $this->__get( 'render_submit_button' ) ) {
			submit_button();
		}
		echo '</form>' . "\n";
	} // End render()

	/**
	 * Render any extra hidden fields, if any are specified.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function maybe_render_extra_hidden_fields () {
		$fields = $this->__get( 'extra_hidden_fields' );

		$html = '';
		if ( is_array( $fields ) && 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				$html .= '<input type="hidden" name="' . esc_attr( $k ) . '" value="' . esc_attr( $v ) . '" />' . "\n";
			}
		}
		echo $html;
	} // End maybe_render_extra_hidden_fields()

	/**
	 * Attempt to create an array of the selected data from a legacy "multicheck" field.
	 * Previously, each field was saved in a separate entry in the database, as either "true" or "false". Clearly, that is no longer the case. :)
	 * @access public
	 * @since  6.0.0
	 * @return void
	 */
	public function maybe_create_multicheck_legacy_defaults ( $key, $options ) {
		$response = array();
		if ( is_array( $options ) && 0 < count( $options ) ) {
			foreach ( $options as $k => $v ) {
				$check = get_option( $key . '_' . $k, 'false' );
				if ( 'true' == $check ) {
					$response[$k] = $v;
				}
			}
		}
		return $response;
	} // End maybe_create_multicheck_legacy_defaults()

	/**
	 * Load in CSS where necessary.
	 * @access public
	 * @since  6.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		// General fields-related CSS.
		wp_enqueue_style( $this->_token . '-fields', esc_url( $this->_assets_url . 'css/fields.css' ) );

		// Stylesheet for "Chosen".
		wp_register_style( $this->_token . '-chosen', esc_url( $this->_assets_url . 'css/lib/chosen.css' ) );

		if ( $this->_has_colourpicker ) {
			wp_enqueue_style( 'wp-color-picker' );
		}
		if ( $this->_has_select ) {
			wp_enqueue_style( $this->_token . '-chosen' );
		}
	} // End enqueue_styles()

	/**
	 * Load in JavaScripts where necessary.
	 * @access public
	 * @since  6.0.0
	 * @return void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-upload', esc_url( $this->_assets_url . 'js/uploaders.js' ), array( 'jquery' ) );
		wp_register_script( $this->_token . '-datepicker', esc_url( $this->_assets_url . 'js/datepickers.js' ), array( 'jquery', 'jquery-ui-datepicker' ) );
		wp_register_script( $this->_token . '-colourpicker', esc_url( $this->_assets_url . 'js/colourpickers.js' ), array( 'jquery', 'wp-color-picker' ) );
		wp_register_script( $this->_token . '-typography', esc_url( $this->_assets_url . 'js/typography.js' ), array( 'jquery' ) );

		wp_register_script( 'jquery-masked-input', esc_url( $this->_assets_url . 'js/lib/jquery-masked-input.js' ), array( 'jquery' ) );
		wp_register_script( $this->_token . '-masked-input', esc_url( $this->_assets_url . 'js/masked-inputs.js' ), array( 'jquery', 'jquery-masked-input' ) );

		wp_register_script( $this->_token . '-chosen', esc_url( $this->_assets_url . 'js/lib/jquery-chosen.js' ), array( 'jquery' ) );
		wp_register_script( $this->_token . '-chosen-loader', esc_url( $this->_assets_url . 'js/chosen-selectors.js' ), array( 'jquery', $this->_token . '-chosen' ) );

		wp_register_script( $this->_token . '-image-selector', esc_url( $this->_assets_url . 'js/image-selectors.js' ), array( 'jquery' ) );
		wp_register_script( $this->_token . '-range-selector', esc_url( $this->_assets_url . 'js/range-selectors.js' ), array( 'jquery' ) );

		if ( $this->_has_upload ) {
			wp_enqueue_script( $this->_token . '-upload' );
		}
		if ( $this->_has_colourpicker ) {
			wp_enqueue_script( $this->_token . '-colourpicker' );
		}
		if ( $this->_has_typography ) {
			wp_enqueue_script( $this->_token . '-typography' );
		}
		if ( $this->_has_masked_input ) {
			wp_enqueue_script( $this->_token . '-masked-input' );
		}
		if ( $this->_has_calendar ) {
			wp_enqueue_script( $this->_token . '-datepicker' );
		}
		if ( $this->_has_imageselector ) {
			wp_enqueue_script( $this->_token . '-image-selector' );
		}
		if ( $this->_has_range ) {
			wp_enqueue_script( $this->_token . '-range-selector' );
		}
		if ( $this->_has_select ) {
			wp_enqueue_script( $this->_token . '-chosen-loader' );
		}
	} // End enqueue_scripts()

	/**
	 * Load wp_enqueue_media() separately from the JavaScripts, to ensure the underscore.js templates are correctly loaded.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function enqueue_media_setup () {
		wp_enqueue_media();
	} // End enqueue_media_setup()

	/**
	 * Enqueue the styles and scripts for use with the fields.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function maybe_enqueue_field_assets () {
		$this->enqueue_styles();
		$this->enqueue_scripts();
	} // End maybe_enqueue_field_assets()

	/**
	 * Retrieve the fields for a specified section.
	 * @access  protected
	 * @since   6.0.0
	 * @param   string $section The section to search for fields in.
	 * @return  array           An array of the detected fields.
	 */
	protected function _get_fields_by_section ( $section ) {
		$fields = array();
		foreach ( $this->_fields as $k => $v ) {
			if ( $section == $v['section'] ) $fields[$k] = $v;
		}
		return $fields;
	} // End _get_fields_by_section()

	/**
	 * Detect the various sections of the provided data.
	 * @access  public
	 * @since   6.0.0
	 * @param   array $data Data array of various sections and fields.
	 * @return  array       Detected sections.
	 */
	public function init_sections ( $data ) {
		if ( 0 >= count( $data ) ) return new WP_Error( 'bad_settings_data', __( 'The settings data provided is malformed. Please try again.', 'woothemes' ) );

		// Create an array of menu items - multi-dimensional, to accommodate sub-headings.
		$sections = array();
		$headings = array();

		foreach ( $data as $k => $v ) {
			if ( $v['type'] == 'heading' || $v['type'] == 'subheading' ) {
				$headings[] = $v;
			}
		}

		$prev_heading_key = 0;

		foreach ( $headings as $k => $v ) {
			$token = $this->_generate_section_token( $v['name'] );
			// Capture the token.
			$v['token'] = $token;

			if ( 'heading' == $v['type'] ) {
				$sections[$token] = $v;
				$prev_heading_key = $token;
			}

			if ( 'subheading' == $v['type'] ) {
				$sections[$prev_heading_key]['children'][$v['token']] = $v;
			}
		}

		$this->_sections = $sections;

		return $this->_sections;
	} // End init_sections()

	/**
	 * Detect the various fields within the provided data.
	 * @access  public
	 * @since   6.0.0
	 * @param   array $data Data array of various sections and fields.
	 * @return  array       Detected fields.
	 */
	public function init_fields ( $data ) {
		if ( 0 >= count( $data ) ) return new WP_Error( 'bad_settings_data', __( 'The settings data provided is malformed. Please try again.', 'woothemes' ) );

		$current_section = '';
		foreach ( $data as $k => $v ) {
			$field_counter = 0;
			$field_counter++;

			if ( in_array( $v['type'], array( 'heading', 'subheading' ) ) ) {
				$current_section = $this->_generate_section_token( $v['name'] );
				continue; // Ignore headings and sub-headings.
			}

			// Cater for the "std" field in "info" field types. We prefer to use "desc" as it is more logical.
			if ( 'info' == $v['type'] && '' == $v['desc'] ) {
				$v['desc'] = $v['std'];
			}

			// Process fields with an array as the type.
			if ( is_array( $v['type'] ) ) {
				foreach ( $v['type'] as $i => $j ) {
					$v['multi_fields'] = $v['type'];
				}
				foreach ( $v['multi_fields'] as $i => $j ) {
					unset( $v['multi_fields'][$i] );
					// Change "meta" to "name".
					if ( isset( $j['meta'] ) ) {
						$j['name'] = $j['meta'];
						unset( $j['meta'] );
					}
					$v['multi_fields'][$j['id']] = $j;
				}
				$v['type'] = 'multi_field';
			}

			// Add the field to the fields property.
			$v['section'] = $current_section;

			$key = '';
			if ( isset( $v['id'] ) ) {
				$key = $v['id'];
			} else {
				if ( isset( $v['name'] ) ) {
					$key = sanitize_title_with_dashes( $v['name'] );
				}
			}

			// Make sure we always have a key.
			if ( '' == $key ) {
				$key = 'field-' . $field_counter;
			}

			// Avoid duplicate keys by creating an adjusted key.
			if ( isset( $this->_fields[$key] ) ) {
				$counter = 0;
				$new_key = '';

				do {
					$counter++;
					$new_key = $key . '-' . $counter;
				} while ( isset( $this->_fields[$key . '-' . $counter] ) );

				$key = $new_key;
			}

			// Cater for slider fields and create the necessary options, if none are present.
			if ( 'slider' == $v['type'] && ! isset( $v['options'] ) ) {
				if ( isset( $v['min'] ) && isset( $v['max'] ) ) {
					$increment = 1;
					$min = intval( $v['min'] );
					$max = intval( $v['max'] );
					if ( isset( $v['increment'] ) ) {
						$increment = intval( $v['increment'] );
					}

					if ( $max > $min ) {
						$options = array();
						for ( $i = $min; $i <= $max; $i+=$increment ) {
							$options[$i] = $i;
						}
						$v['options'] = $options;
					}
				}
			}

			$this->_fields[$key] = $v;
		}

		return $this->_fields;
	} // End init_fields()

	/**
	 * Generate a section token based on a specified key.
	 * @access  public
	 * @since   6.0.0
	 * @param   string $key 	Specified key for the section.
	 * @return  string      	Generated token for the section.
	 */
	public function _generate_section_token ( $key ) {
		return sanitize_title_with_dashes( $key );
	} // End _generate_section_token()

	/**
	 * Return an array of field types expecting an array value returned.
	 * @access public
	 * @since  6.0.0
	 * @return array
	 */
	public function get_array_field_types () {
		return array( 'multicheck', 'multicheck2', 'typography', 'border', 'timestamp' );
	} // End get_array_field_types()

	/**
	 * Return an array of field types where no label/header is to be displayed.
	 * @access protected
	 * @since  6.0.0
	 * @return array
	 */
	protected function get_no_label_field_types () {
		return array( 'info' );
	} // End get_no_label_field_types()

	/**
	 * Return a filtered array of supported field types.
	 * @access  public
	 * @since   6.0.0
	 * @return  array Supported field type keys.
	 */
	public function get_supported_fields () {
		return (array)apply_filters( 'wf_fields_supported_fields', array( 'text', 'checkbox', 'radio', 'textarea', 'multicheck', 'multicheck2', 'select', 'select2', 'upload', 'upload_min', 'upload_field_id', 'calendar', 'time', 'time_masked', 'timestamp', 'color', 'typography', 'border', 'images', 'info', 'slider', 'masked_input' , 'select_taxonomy', 'multi_field' ) );
	} // End get_supported_fields()

	/**
	 * Return a value, using a desired retrieval method.
	 * @access  public
	 * @since   6.0.0
	 * @return  mixed Returned value.
	 */
	public function get_value ( $key, $default ) {
		$response = false;

		if ( true == apply_filters( 'wf_use_theme_mods', false ) ) {
			$response = get_theme_mod( esc_attr( $key ), $default );
		} else {
			$response = get_option( esc_attr( $key ), $default );
		}

		return $response;
	} // End get_value()
} // End Class
?>