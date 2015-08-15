<?php
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * WooThemes Theme Options Backup
 *
 * Backup your "Theme Options" to a downloadable text file.
 *
 * @version 6.0.0
 * @author  Matty
 * @since   4.5.0
 *
 * @package WooFramework
 * @subpackage Module
 *
 * TABLE OF CONTENTS
 *
 * - private $_admin_page
 * - private $_token
 *
 * - function __construct ()
 * - function init ()
 * - function register_admin_screen ()
 * - function admin_screen ()
 * - function admin_screen_help ()
 * - function admin_screen_logic ()
 * - function move_admin_menu ()
 * - function import ()
 * - function export ()
 * - function add_to_export_query ()
 * - function add_single_to_export_query ()
 * - function construct_database_query ()
 */
class WF_Backup {
	private $_admin_page;
	private $_token;

	public function __construct () {
		if ( ! defined( 'ABSPATH' ) ) exit;
		$this->_admin_page = '';
		$this->_token = 'woothemes-backup';

		add_action( 'admin_menu', array( $this, 'register_admin_screen' ), 50 );
	} // End __construct()

	/**
	 * Register the admin screen within WordPress.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function register_admin_screen () {
		$this->_admin_page = add_submenu_page( 'woothemes', __( 'WooThemes Settings Backup', 'woothemes' ), __( 'Backup Settings', 'woothemes' ), 'manage_options', $this->_token, array( $this, 'admin_screen' ) );
		// Admin screen logic.
		add_action( 'load-' . $this->_admin_page, array( $this, 'admin_screen_logic' ) );
		// Add admin notices to the backups screen.
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 10 );
	} // End register_admin_screen()

	/**
	 * Load the admin screen.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function admin_screen () {
		$export_type = 'all';

		if ( isset( $_POST['export-type'] ) && in_array( $_POST['export-type'], array( 'theme', 'framework' ) ) ) {
			$export_type = esc_attr( $_POST['export-type'] );
		}
?>
<?php
	do_action( 'wf_screen_get_header', 'woothemes', 'themes' );
?>
	<h2><?php _e( 'Backup Settings', 'woothemes' ); ?></h2>
	<h3><?php _e( 'Import Theme Settings', 'woothemes' ); ?></h3>

	<p><?php _e( 'Import your theme settings from a previously exported backup.', 'woothemes' ); ?></p>
	<div class="form-wrap">
		<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin.php?page=' . $this->_token ); ?>">
			<table class="form-table">
				<tbody>
					<tr>
						<th>
						<?php wp_nonce_field( 'woothemes-backup-import' ); ?>
						<?php _e( 'Choose a file', 'woothemes' ); ?>
						</th>
						<td>
							<input type="file" id="woothemes-import-file" name="woothemes-import-file" size="25" />
							<p class="description"><?php printf( __( 'Maximum Size: %s', 'woothemes' ), ini_get( 'post_max_size' ) ); ?></p>
							<input type="hidden" name="woothemes-backup-import" value="1" />
						</td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
			<input type="submit" class="button" value="<?php _e( 'Upload and Import', 'woothemes' ); ?>" />
			</p>
		</form>
	</div><!--/.form-wrap-->

	<h3><?php _e( 'Export Theme Settings', 'woothemes' ); ?></h3>

	<p><?php _e( 'Export your settings and download them to your computer as an importable text file.', 'woothemes' ); ?></p>

	<form method="post" action="<?php echo admin_url( 'admin.php?page=' . $this->_token ); ?>">

		<table class="form-table">
			<tbody>
				<tr>
					<th><?php _e( 'Export', 'woothemes' ); ?></th>
					<td>
						<?php wp_nonce_field( 'woothemes-backup-export' ); ?>
						<fieldset>
							<label><input type="radio" name="export-type" value="all"<?php checked( 'all', $export_type ); ?>> <?php _e( 'All Settings', 'woothemes' ); ?></label>
							<p class="description"><?php _e( 'This will contain all of the options listed below.', 'woothemes' ); ?></p><br />

							<label for="content"><input type="radio" name="export-type" value="theme"<?php checked( 'theme', $export_type ); ?>> <?php _e( 'Theme Options', 'woothemes' ); ?></label><br />
							<label for="content"><input type="radio" name="export-type" value="framework"<?php checked( 'framework', $export_type ); ?>> <?php _e( 'Framework Settings', 'woothemes' ); ?></label><br />
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="hidden" name="woothemes-backup-export" value="1" />
			<input type="submit" class="button" value="<?php _e( 'Download Export File', 'woothemes' ); ?>" />
		</p>

	</form>
<?php
	do_action( 'wf_screen_get_footer', 'woothemes', 'themes' );
?>
<?php
	} // End admin_screen()

	/**
	 * Display admin notices when performing backup/restore.
	 * @access  public
	 * @since   6.0.0
	 * @return  void
	 */
	public function admin_notices () {
		if ( ! isset( $_GET['page'] ) || ( $_GET['page'] != $this->_token ) ) { return; }

		echo '<div id="import-notice" class="updated"><p>' . sprintf( __( 'Please note that this backup manager backs up only your settings and not your content. To backup your content, please use the %sWordPress Export Tool%s.', 'woothemes' ), '<a href="' . admin_url( 'export.php' ) . '">', '</a>' ) . '</p></div><!--/#import-notice .message-->' . "\n";

		if ( isset( $_GET['error'] ) && $_GET['error'] == 'true' ) {
			echo '<div id="message" class="error"><p>' . __( 'There was a problem importing your settings. Please try again.', 'woothemes' ) . '</p></div>';
		} else if ( isset( $_GET['error-export'] ) && $_GET['error-export'] == 'true' ) {
			echo '<div id="message" class="error"><p>' . __( 'There was a problem exporting your settings. Please try again.', 'woothemes' ) . '</p></div>';
		} else if ( isset( $_GET['invalid'] ) && $_GET['invalid'] == 'true' ) {
			echo '<div id="message" class="error"><p>' . __( 'The import file you\'ve provided is invalid. Please try again.', 'woothemes' ) . '</p></div>';
		} else if ( isset( $_GET['imported'] ) && $_GET['imported'] == 'true' ) {
			echo '<div id="message" class="updated"><p>' . sprintf( __( 'Settings successfully imported. | Return to %sTheme Options%s', 'woothemes' ), '<a href="' . admin_url( 'admin.php?page=woothemes' ) . '">', '</a>' ) . '</p></div>';
		}
	} // End admin_notices()

	/**
	 * The processing code to generate the backup or restore from a previous backup.
	 * @access public
	 * @since  6.0.0
	 * @return void
	 */
	public function admin_screen_logic () {
		if ( ! isset( $_POST['woothemes-backup-export'] ) && isset( $_POST['woothemes-backup-import'] ) && ( $_POST['woothemes-backup-import'] == true ) ) {
			$this->import();
		}

		if ( ! isset( $_POST['woothemes-backup-import'] ) && isset( $_POST['woothemes-backup-export'] ) && ( $_POST['woothemes-backup-export'] == true ) ) {
			$this->export();
		}
	} // End admin_screen_logic()

	/**
	 * Import settings from a backup file.
	 * @access private
	 * @since  6.0.0
	 * @return void
	 */
	private function import() {
		check_admin_referer( 'woothemes-backup-import' ); // Security check.

		if ( ! isset( $_FILES['woothemes-import-file'] ) ) { return; } // We can't import the settings without a settings file.

		// Extract file contents
		$upload = file_get_contents( $_FILES['woothemes-import-file']['tmp_name'] );

		// Decode the JSON from the uploaded file
		$options = json_decode( $upload, true );

		// Check for errors
		if ( ! $options || $_FILES['woothemes-import-file']['error'] ) {
			wp_redirect( admin_url( 'admin.php?page=' . $this->_token . '&error=true' ) );
			exit;
		}

		// Make sure this is a valid backup file.
		if ( ! isset( $options['woothemes-backup-validator'] ) ) {
			wp_redirect( admin_url( 'admin.php?page=' . $this->_token . '&invalid=true' ) );
			exit;
		} else {
			unset( $options['woothemes-backup-validator'] ); // Now that we've checked it, we don't need the field anymore.
		}

		// Make sure the options are saved to the global options collection as well.
		$woo_options = get_option( 'woo_options' );

		$has_updated = false; // If this is set to true at any stage, we update the main options collection.

		// Cycle through data, import settings
		foreach ( (array)$options as $key => $settings ) {
			$settings = maybe_unserialize( $settings ); // Unserialize serialized data before inserting it back into the database.

			// We can run checks using get_option(), as the options are all cached. See wp-includes/functions.php for more information.
			if ( get_option( $key ) != $settings ) {
				update_option( $key, $settings );
			}

			if ( is_array( $woo_options ) ) {
				if ( isset( $woo_options[$key] ) && $woo_options[$key] != $settings ) {
					$woo_options[$key] = $settings;
					$has_updated = true;
				}
			}
		}

		if ( $has_updated == true ) {
			update_option( 'woo_options', $woo_options );
		}

		// Redirect, add success flag to the URI
		wp_redirect( admin_url( 'admin.php?page=' . $this->_token . '&imported=true' ) );
		exit;
	} // End import()

	/**
	 * Export settings to a backup file.
	 * @access  private
	 * @since   6.0.0
	 * @uses    $wpdb WordPress database object.
	 * @return  void
	 */
	private function export() {
		global $wpdb;
		check_admin_referer( 'woothemes-backup-export' ); // Security check.

		$export_options = array( 'all', 'theme', 'framework' );

		if ( ! in_array( strip_tags( $_POST['export-type'] ), $export_options ) ) { return; } // No invalid exports, please.

		$export_type = esc_attr( strip_tags( $_POST['export-type'] ) );

		$settings = array();

		$query = $this->construct_database_query( $export_type );

		// Error trapping for the export.
		if ( $query == '' ) {
			wp_redirect( admin_url( 'admin.php?page=' . $this->_token . '&error-export=true' ) );
			return;
		}

		// If we get to this stage, all is safe so run the query.
		$results = $wpdb->get_results( $query );

		foreach ( $results as $result ) {
		     $settings[$result->option_name] = $result->option_value;
		}

		// Remove the "blogname" and "blogdescription" fields
		unset( $settings['blogname'] );
		unset( $settings['blogdescription'] );

		if ( ! $settings ) { return; }

		// Add our custom marker, to ensure only valid files are imported successfully.
		$settings['woothemes-backup-validator'] = date( 'Y-m-d h:i:s' );

		// Generate the export file.
	    $output = json_encode( (array)$settings );

	    header( 'Content-Description: File Transfer' );
	    header( 'Cache-Control: public, must-revalidate' );
	    header( 'Pragma: hack' );
	    header( 'Content-Type: text/plain' );
	    header( 'Content-Disposition: attachment; filename="' . $this->_token . '-' . date( 'Ymd-His' ) . '.json"' );
	    header( 'Content-Length: ' . strlen( $output ) );
	    echo $output;
	    exit;
	} // End export()

	/**
	 * Loop through an array of options and add them to the MySQL SELECT query string.
	 * @access  private
	 * @since   6.0.0
	 * @param   $options array
	 * @param   $count int
	 * @return  $query array ( string, count )
	 */
	private function add_to_export_query ( $options, $count ) {
		$query = array();
		$query_inner = '';

		foreach( $options as $option ) {
			if( isset( $option['id'] ) ) {
				$count++;
				$option_id = $option['id'];

				$option_id = esc_attr( $option_id );
				$option_id = sanitize_title( $option_id );

				if( $count > 1 ) { $query_inner .= ' OR '; }
				$query_inner .= "option_name = '$option_id'";

				// Width/Height-type fields
				if ( is_array( $option['type'] ) ) {
					foreach ( $option['type'] as $o ) {
						if( $count > 1 ){ $query_inner .= ' OR '; }
						if ( isset( $o['id'] ) ) {
							$option_id = $o['id'];

							$option_id = esc_attr( $option_id );
							$option_id = sanitize_title( $option_id );

							$query_inner .= "option_name = '$option_id'";
						}
					}
				}

				// Multicheck fields
				if ( ! is_array( $option['type'] ) && $option['type'] == 'multicheck' ) {
					foreach ( $option['options'] as $k => $v ) {
						if( $count > 1 ){ $query_inner .= ' OR '; }
						if ( ! is_numeric( $k ) ) {
							$option_id = $option['id'] . '_' . $k;

							$option_id = esc_attr( $option_id );
							$option_id = sanitize_title( $option_id );

							$query_inner .= "option_name = '$option_id'";
						}
					}
				}
			}
		}

		$query['string'] = $query_inner;
		$query['count'] = $count;

		return $query;
	} // End add_to_export_query()

	/**
	 * Add a single item to the MySQL SELECT query string.
	 * @access  private
	 * @since   6.0.0
	 * @param   $option_id string
	 * @param   $count int
	 * @return  $query array ( string, count )
	 */
	private function add_single_to_export_query ( $option_id, $count ) {
		$query = array();
		$query_inner = '';

		$option_id = esc_attr( $option_id );
		$option_id = sanitize_title( $option_id );

		if( $count > 1 ) { $query_inner .= ' OR '; }
		$query_inner .= "option_name = '$option_id'";

		$query['string'] = $query_inner;
		$query['count'] = $count;

		return $query;
	} // End add_single_to_export_query()

	/**
	 * Constructs the database query based on the export type.
	 * @access  private
	 * @since   6.0.0
	 * @param   $export_type string
	 * @uses    global $wpdb
	 * @return  string Constructed query.
	 */
	public function construct_database_query ( $export_type ) {
		global $wpdb;

		$query = '';
		$query_inner = '';
		$count = 0;

		// Begin populating settings to be exported.
		switch ( $export_type ) {
			// All Settings
			case 'all':
				// Theme Options
				$options = get_option( 'woo_template' );

				if ( is_array( $options ) ) {
					$query = $this->add_to_export_query( $options, $count );

					$query_inner .= $query['string'];
					$count = $query['count'];
				}

				// Framework Settings
				$options = get_option( 'woo_framework_template' );

				if ( is_array( $options ) ) {
					// Remove the "framework_woo_export_options" and "framework_woo_import_options" items before constructing the query.
					foreach ( (array) $options as $k => $v ) {
						if ( isset( $options[$k]['id'] ) && in_array( $options[$k]['id'], array( 'framework_woo_import_options', 'framework_woo_export_options' ) ) ) {
							unset( $options[$k] );
						}
					}

					$query = $this->add_to_export_query( $options, $count );

					$query_inner .= $query['string'];
					$count = $query['count'];
				}
			break;

			// Theme Options
			case 'theme':
				$options = get_option( 'woo_template' );

				if ( is_array( $options ) ) {
					$query = $this->add_to_export_query( $options, $count );

					$query_inner .= $query['string'];
					$count = $query['count'];
				}
			break;

			// Framework Settings
			case 'framework':
				$options = get_option( 'woo_framework_template' );

				if ( is_array( $options ) ) {
					// Remove the "framework_woo_export_options" and "framework_woo_import_options" items before constructing the query.
					foreach ( (array) $options as $k => $v ) {
						if ( isset( $options[$k]['id'] ) && in_array( $options[$k]['id'], array( 'framework_woo_import_options', 'framework_woo_export_options' ) ) ) {
							unset( $options[$k] );
						}
					}

					$query = $this->add_to_export_query( $options, $count );

					$query_inner .= $query['string'];
					$count = $query['count'];
				}
			break;
		}

		// Allow child themes/plugins to add their own data to the exporter.
		$query_inner = (string)apply_filters( 'wooframework_export_query_inner', $query_inner );

		if ( $query_inner != '' ) {
			$query = 'SELECT option_name, option_value FROM ' . $wpdb->options . ' WHERE ' . $query_inner;
		}

		return $query;
	} // End construct_database_query()
} // End Class
?>