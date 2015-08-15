<?php
// File Security Check.
if ( ! defined( 'ABSPATH' ) ) exit;

class WF_Fields_Meta extends WF_Fields {
	/**
	 * Constructor function.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function __construct () {
		parent::__construct();

		// This must be present if using fields that require Javascript or styling.
		add_action( 'admin_footer', array( $this, 'maybe_enqueue_field_assets' ) );
	} // End __construct()

	/**
	 * Return a value, using a desired retrieval method.
	 * @access  public
	 * @since   6.0.0
	 * @return  mixed Returned value.
	 */
	public function get_value ( $key, $default ) {
		global $post_id;

		$id = $post_id; // Avoid overwriting the global variable, just in case.

		if ( 0 >= $post_id && isset( $_GET['post'] ) ) {
			$id = intval( $_GET['post'] );
		}

		$response = false;

		if ( true == (bool)apply_filters( 'wf_meta_use_underscore_prefix', false ) ) {
			$key = '_' . $key;
		}

		$response = get_post_meta( $id, $key, true );

		return $response;
	} // End get_value()
} // End Class
?>