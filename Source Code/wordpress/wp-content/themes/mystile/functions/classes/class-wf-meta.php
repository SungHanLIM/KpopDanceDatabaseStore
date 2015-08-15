<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WF Meta Class
 *
 * @class WF_Meta
 * @version	6.0.0
 * @since 6.0.0
 * @package	WF
 * @author Matty
 */
class WF_Meta {
	/**
	 * The token.
	 * @var     string
	 * @access  private
	 * @since   6.0.0
	 */
	private $_token;

	/**
	 * The settings sections.
	 * @var     object
	 * @access  public
	 * @since   6.0.0
	 */
	private $_sections;

	/**
	 * The settings fields.
	 * @var     object
	 * @access  public
	 * @since   6.0.0
	 */
	private $_fields;

	/**
	 * The field generator.
	 * @var     object
	 * @access  private
	 * @since   6.0.0
	 */
	private $_field_obj;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function __construct () {
		$this->_token = 'wf';

		$this->_field_obj = new WF_Fields_Meta();

		$this->_field_obj->__set( 'token', 'woo' );
		$this->_field_obj->__set( 'render_submit_button', false );

		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'register_meta_boxes' ) );
			add_action( 'admin_footer', array( $this->_field_obj, 'maybe_enqueue_field_assets' ) );
			add_action( 'save_post', array( $this, 'meta_box_save' ) );
		}
	} // End __construct()

	/**
	 * Add the various registered meta boxes.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function register_meta_boxes () {
		$defaults = array(
						'id' => 'woothemes-settings',
						'title' => sprintf( __( '%s Settings', 'woothemes' ), wp_get_theme()->__get( 'Name' ) ),
						'callback' => array( $this, 'meta_box_content' ),
						'page' => 'all',
						'context' => 'normal',
						'priority' => 'default',
						'callback_args' => ''
					);

		$settings = array(
		                'id' => 'woothemes-settings',
		                'title' => sprintf( __( '%s Settings', 'woothemes' ), wp_get_theme()->__get( 'Name' ) ),
		                'callback' => array( $this, 'meta_box_content' ),
		                'page' => 'all',
		                'context' => 'normal',
		                'priority' => 'default',
		                'callback_args' => ''
		            );

		// Allow child themes/plugins to filter these settings.
		$settings = apply_filters( 'woothemes_metabox_settings', $settings, $settings['page'], $settings['id'] );
		$meta_boxes = array( 'woothemes-settings' => $settings );

		$meta_boxes = (array)apply_filters( 'wf_meta_boxes', $meta_boxes );

		// Loop through and set up the meta boxes.
		if ( 0 < count( $meta_boxes ) ) {
			$global_boxes = array();
			foreach ( $meta_boxes as $k => $v ) {
				if ( ! isset( $v['page'] ) ) {
					$meta_boxes[$k]['page'] = 'all';
				}

				// If we want this box to apply to all post types, store it for later to avoid doing a loop within a loop (nasty).
				if ( 'all' == $v['page'] ) {
					$global_boxes[$k] = $v;
				} else {
					$v = wp_parse_args( $v, $defaults );
					add_meta_box( $v['id'], $v['title'], array( $this, 'meta_box_content' ), $v['page'], $v['context'], $v['priority'], $v['callback_args'] );
				}
			}

			// Maybe process global boxes.
			if ( 0 < count( $global_boxes ) ) {
				foreach ( $global_boxes as $k => $v ) {
					$v = wp_parse_args( $v, $defaults );
					foreach ( get_post_types() as $i => $j ) {
						add_meta_box( $v['id'], $v['title'], array( $this, 'meta_box_content' ), $j, $v['context'], $v['priority'], $v['callback_args'] );
					}
				}
			}
		}
	} // End register_meta_boxes()

	/**
	 * Save meta box fields.
	 *
	 * @access public
	 * @since  1.1.0
	 * @param int $post_id
	 * @return void
	 */
	public function meta_box_save ( $post_id ) {
		global $post, $messages;
		if ( empty( $_POST ) ) {
			return;
		}

		// Verify
		if ( ! wp_verify_nonce( $_POST[$this->_field_obj->__get( 'token' ) . '_nonce'], $this->_field_obj->__get( 'token' ) . '_nonce' ) ) {
			return $post_id;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		$this->_field_obj->init_fields( $this->get_settings_template() );
		$field_data = $this->get_fields();
		$field_data = $this->setup_fields( $field_data );

		$key_value_pairs = array();

		if ( is_array( $field_data ) && 0 < count( $field_data ) ) {
			foreach ( $field_data as $k => $v ) {
				$value = '';
				if ( isset( $_POST[$k] ) ) {
					$value = $_POST[$k];
				}
				$key_value_pairs[$k] = $value;
			}
		}

		$key_value_pairs = $this->_field_obj->validate_fields( $key_value_pairs );

		foreach ( $key_value_pairs as $k => $v ) {
			// Escape the URLs.
			if ( 'url' == $field_data[$k]['type'] ) {
				$v = esc_url( $v );
			}

			$field_key = $k;
			if ( true == (bool)apply_filters( 'wf_meta_use_underscore_prefix', false ) ) {
				$field_key = '_' . $k;
			}

			if ( '' == get_post_meta( $post_id, $field_key, true ) ) {
				// add_post_meta( $post_id, $field_key, $v, true );
				// We need to use update_post_meta(), in case there are legacy keys in the database which are empty.
				update_post_meta( $post_id, $field_key, $v );
			} elseif ( ! empty( $v ) && $v != get_post_meta( $post_id, $field_key, true ) ) {
				update_post_meta( $post_id, $field_key, $v );
			} elseif ( '' == $v ) {
				delete_post_meta( $post_id, $field_key, get_post_meta( $post_id, $field_key, true ) );
			}
		}
	} // End meta_box_save()

	/**
	 * Output markup for the meta box content.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function meta_box_content ( $post, $args ) {
		$field_data = $this->get_settings_template();
		$field_data = $this->setup_fields( $field_data );
		$this->_field_obj->__set( 'sections', array( $args['id'] => '' ) ); // Make sure our ID is an array key in the sections array.
		$this->_field_obj->__set( 'fields', $field_data );
		$this->_field_obj->render();
	} // End settings_screen()

	/**
	 * Make sure each field is in a section.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function maybe_assign_default_section ( $fields, $section = 'woothemes-settings' ) {
		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				if ( ! isset( $v['section'] ) ) {
					$fields[$k]['section'] = '';
				}

				if ( ! isset( $v['section'] ) || '' == $v['section'] ) {
					$fields[$k]['section'] = esc_attr( $section );
				}
			}
		}
		return $fields;
	} // End maybe_assign_default_section()

	/**
	 * Make sure each field has a key.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function maybe_assign_field_key ( $fields ) {
		$data = array();
		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				$data[$v['name']] = $v;
			}
		}
		return $data;
	} // End maybe_assign_field_key()

	/**
	 * Transform data to make sure it matches what WF_Fields is expecting.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function maybe_transform_field_data ( $fields ) {
		$data = array();
		if ( 0 < count( $fields ) ) {
			foreach ( $fields as $k => $v ) {
				if ( isset( $v['label'] ) ) {
					$data[$k] = $v;
					$data[$k]['name'] = $v['label'];
				}
			}
		}
		return $data;
	} // End maybe_transform_field_data()

	/**
	 * Return an array of the settings scafolding. The field types, names, etc.
	 * @access  public
	 * @since   6.0.0
	 * @return  array
	 */
	public function get_settings_template () {
		return get_option( 'woo_custom_template', array() );
	} // End get_settings_template()

	/**
	 * Process a single field, when running get_all().
	 * @access  private
	 * @since   6.0.0
	 * @param   string 		 $k The field key.
	 * @param   string/array $v The stored value.
	 * @return  string/array    The stored value, sanitized.
	 */
	private function _process_single_field ( $k, $v ) {
		$value = $this->_field_obj->get_value( esc_attr( $k ), $v['std'] );

		if ( in_array( $v['type'], $this->_field_obj->get_array_field_types() ) ) {
			// Treat this as an array
			$value = array_map( 'esc_attr', $value );
		}
		return $value;
	} // End _process_single_field()

	/**
	 * Retrieve the fields.
	 * @access  public
	 * @since   6.0.0
	 * @param   string $section The section to search for fields in (optional).
	 * @return  array           An array of the detected fields.
	 */
	public function get_fields ( $section = '' ) {
		$field_data = $this->_field_obj->__get( 'fields' );
		$fields = array();

		foreach ( $field_data as $k => $v ) {
			if ( '' != $section ) {
				if ( $section == $v['section'] ) {
					$fields[$k] = $v;
				}
			} else {
				$fields[$k] = $v;
			}
		}

		return $fields;
	} // End get_fields()

	/**
	 * Setup the fields.
	 * @access  public
	 * @since   6.0.0
	 * @return  array An array of the detected fields.
	 */
	public function setup_fields ( $field_data ) {
		$field_data = $this->maybe_assign_field_key( $field_data );
		$field_data = $this->maybe_assign_default_section( $field_data );
		$field_data = $this->maybe_transform_field_data( $field_data );
		$this->_field_obj->__set( 'fields', $field_data );

		return $field_data;
	} // End setup_fields()
} // End Class
?>