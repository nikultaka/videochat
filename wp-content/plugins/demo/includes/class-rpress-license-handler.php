<?php
/**
 * License handler for RestroPress
 *
 * This class should simplify the process of adding license information
 * to new and existing RestroPress extensions.
 *
 * @version 1.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'RP_License' ) ) :

/**
 * RP_License Class
 */
class RP_License {

	private $api_url = 'https://www.restropress.com';

	/**
	 * Class constructor
	 */
	function __construct() {

		// Setup hooks
		$this->includes();
		$this->hooks();
	}

	/**
	 * Include the updater class
	 *
	 * @access  private
	 * @return  void
	 */
	private function includes() {
		if ( ! class_exists( 'RP_Addon_Updater' ) )  {
			require_once 'class-rpress-addon-updater.php';
		}
	}

	/**
	 * Setup hooks
	 *
	 * @access  private
	 * @return  void
	 */
	private function hooks() {

		// Check that license is valid once per week
		// if ( rpress_doing_cron() ) {
		// 	add_action( 'rpress_weekly_scheduled_events', array( $this, 'weekly_license_check' ) );
		// }

		// For testing license notices, uncomment this line to force checks on every page load
		//add_action( 'admin_init', array( $this, 'weekly_license_check' ) );

		// AJAX Activate License
		add_action( 'wp_ajax_activate_addon_license', array( $this, 'activate_license' ) );

		// AJAX Deactivate License
		add_action( 'wp_ajax_deactivate_addon_license', array( $this, 'deactivate_license') );

		// Display notices to admins
		add_action( 'admin_notices', array( $this, 'notices' ) );

		// Updater
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );
	}

	/**
	 * Activate addon license with ajax call
	 *
	 * @since 2.5
	 * @author RestrPress
	 */
	public function activate_license() {
		// listen for our activate button to be clicked
		if( isset($_POST['license_key']) ) {

			// Get the license from the user
			// Item ID (Normally a 2 or 3 digit code)
			$item_id = isset( $_POST['item_id'] ) ? absint( $_POST['item_id'] ) : '';
			// The actual license code
			$license = isset( $_POST['license'] ) ? trim( $_POST['license'] ) : '';

			// Name of the addon (Print Receipts)
			$name = isset( $_POST['product_name'] ) ? $_POST['product_name'] : '';

			// Key to be saved in to DB
			$license_key = isset( $_POST['license_key'] ) ? $_POST['license_key'] : '';

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'activate_license',
				'item_id' 	 => $item_id,
				'item_name'  => urlencode( $name ),
				'license'    => $license,
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response )
				|| 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				}
				else {
					$message = __( 'An error occurred, please try again.' );
				}

			}
			else {

				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				if ( false === $license_data->success ) {

					switch( $license_data->error ) {

							case 'expired' :

								$message = sprintf(
									__( 'Your license key expired on %s.' ),
									date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
								);
								break;

							case 'revoked' :

								$message = __( 'Your license key has been disabled.' );
								break;

							case 'missing' :

								$message = __( 'Invalid license.' );
								break;

							case 'invalid' :
							case 'site_inactive' :

								$message = __( 'Your license is not active for this URL.' );
								break;

							case 'item_name_mismatch' :

								$message = sprintf( __( 'This appears to be an invalid license key for %s.' ), $name );
								break;

							case 'no_activations_left':

								$message = __( 'Your license key has reached its activation limit.' );
								break;

							default :

								$message = __( 'An error occurred, please try again.' );
								break;
					}
				}
			}

			// Check if anything passed on a message constituting a failure
			if ( ! empty( $message ) )
				$return = array( 'status' => 'error', 'message' => $message );
			else {
				//Save the license key in database
				update_option( $license_key, $license );

				// $license_data->license will be either "valid" or "invalid"
				update_option( $license_key . '_status', $license_data->license );
				$return = array( 'status' => 'updated', 'message' => 'Your license is successfully activated.' );
			}
			echo json_encode( $return );
			exit();
		}
	}

	/**
	 * Deactivate the license of plugin with AJAX call
	 *
	 * @since 2.5
	 * @author RestroPress
	 * @return void
	 */
	public function deactivate_license() {

		if( isset($_POST['license_key']) ) {

			$license_key = isset( $_POST['license_key'] ) ? $_POST['license_key'] : '';
			// retrieve the license from the database
			$license = trim( get_option( $license_key ) );

			$item_name = isset( $_POST['product_name'] ) ? $_POST['product_name'] : '';

			// data to send in our API request
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $license,
				'item_name'  => urlencode( $item_name ), // the name of our product in EDD
				'url'        => home_url()
			);

			// Call the custom API.
			$response = wp_remote_post( $this->api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

				if ( is_wp_error( $response ) ) {
					$message = $response->get_error_message();
				} else {
					$message = __( 'An error occurred, please try again.' );
				}
				$return = array( 'status' => 'error', 'message' => $message );
			}
			else{
				// decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				// $license_data->license will be either "deactivated" or "failed"
				if( $license_data->license == 'deactivated' ) {
					delete_option( $license_key . '_status' );
					delete_option( $license_key );
				}
				$return = array( 'status' => 'updated', 'message' => 'License successfully deactivated.' );
			}
			echo json_encode( $return );
			exit();
		}
	}

	/**
	 * Admin notices for errors
	 *
	 * @return  void
	 */
	public function notices() {

		$items = get_transient( 'restropress_add_ons_feed' );
		if( ! $items ) {
			$items = rpress_fetch_items();
		}

		$statuses = array();

		if( is_array($items) && !empty($items) ) {

			foreach( $items as $key => $item ) {

				$class_name = trim($item->class_name);

				if( class_exists($class_name) ) {

					if( !get_option($item->license_string.'_status') ) {
						array_push( $statuses, 'empty' );
					} else {
						$status = get_option($item->license_string.'_status');
						array_push( $statuses, $status );
					}
				}
			}
		}

		if( !empty( $statuses ) && ( in_array( 'empty', $statuses) || in_array( 'invalid', $statuses) ) ) {

			$class = 'notice notice-error';
			$message = __( 'You have invalid or expired license keys for one or more addons of RestroPress. Please go to the <a href="%2$s">Extensions</a> page to update your licenses.', 'restropress' );
			$url = admin_url( 'admin.php?page=rpress-extensions' );

			printf( '<div class="%1$s"><p>' . $message . '</p></div>', esc_attr( $class ), $url );
		}
	}

	/**
	 * Auto updater
	 *
	 * @access  private
	 * @return  void
	 */
	public function auto_updater() {

		// Get all plugins installed currently
		$all_plugins = get_plugins();

		// Get RestroPress extensions
		$items = get_transient( 'restropress_add_ons_feed' );
		if( ! $items ) {
			$items = rpress_fetch_items();
		}

		$installed_addons = array();

		// Let's check which RP extensions are activated currently
		if( is_array($items) && !empty($items) ) {

			foreach( $items as $key => $item ) {

				if( empty( $item->plugin_name ) )
					continue;

				$class_name = trim($item->class_name);
				if( class_exists($class_name) ) {

					$match_array = $this->rp_search_addon_in_wp_plugins( $item->plugin_name, $all_plugins );
					if( $match_array ) {
						$match_array['item_id'] = $item->id;
						$match_array['slug'] = $item->license_string;
						$match_array['title'] = $item->title;
						array_push( $installed_addons, $match_array );
					}
				}
			}
		}

		if( !empty( $installed_addons ) ) :

			foreach ( $installed_addons as $addon ) {

				# Arguments needed to check addon update
				$args = array(
					'item_id'	=> $addon['item_id'],
					'title'		=> $addon['title'],
					'version'	=> $addon['version'],
					'slug'   	=> $addon['slug'],
				);

				# Setup the updater
				$edd_updater = new RP_Addon_Updater(
					$this->api_url,
					$addon['key'],
					$args
				);
			}

		endif;
	}

	/**
	 * Do a multi dimentiontional array serach to compare
	 * the plugins and available extensions of RestroPress
	 *
	 * @since 2.5
	 * @author RestroPress
	 * @param str Value to be searched
	 * @param arr The array of installed plugins
	 */
	public function rp_search_addon_in_wp_plugins( $value, $array ) {

		foreach ($array as $key => $val) {

			if ($val['Name'] === $value) {

				$resultSet['key'] = $key;
				$resultSet['name'] = $val['Name'];
				$resultSet['version'] = $val['Version'];

				return $resultSet;
           }
       }
       return null;
    }

	/**
	 * Check if license key is valid once per week
	 *
	 * @since   2.5
	 * @return  void
	 */
	public function weekly_license_check() {

		if( ! empty( $_POST['edd_settings'] ) ) {
			return; // Don't fire when saving settings
		}

		if( empty( $this->license ) ) {
			return;
		}

		// data to send in our API request
		$api_params = array(
			'edd_action'=> 'check_license',
			'license' 	=> $this->license,
			'item_name' => urlencode( $this->item_name ),
			'url'       => home_url()
		);

		if ( ! empty( $this->item_id ) ) {
			$api_params['item_id'] = $this->item_id;
		}

		// Call the API
		$response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params
			)
		);

		// make sure the response came back okay
		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( $this->item_shortname . '_license_active', $license_data );

	}
}

new RP_License();

endif; // end class_exists check