<?php
/*
WPUpdates Plugin Updater Class
http://wp-updates.com
v2.0
*/

error_reporting(0);

// wp-updater old method method If user has a valid license and new updates are available show the update notification in plugins page.
if ( ! class_exists( 'WP_Updates_Plugin_lb_Updater' ) ) {
	/**
	 * Class WP_Updates_Plugin_lb_Updater
	 *
	 * @property LicenseBoxAPI $license_box_api;
	 */
	class WP_Updates_Plugin_lb_Updater {

		/**
		 * WP_Updates_Plugin_lb_Updater constructor.
		 *
		 * @since 1.0.0
		 */
		function __construct() {

			$this->plugin_path     = NTPLUGIN_FILE;
			$this->plugin_slug     = plugin_basename( __FILE__ );
			$this->license_box_api = new LicenseBoxAPI();
			$this->license_key     = ntplugin_license_data( 'license_code' );
			$this->client_name     = ntplugin_license_data( 'client_name' );

			add_filter( 'site_transient_update_plugins', array( &$this, 'check_for_update' ) );
			add_filter( 'transient_update_plugins', array( &$this, 'check_for_update' ) );
			add_action( 'upgrader_process_complete', array( $this, 'download_and_update' ), 10, 2 );

		}

		public function check_for_update( $transient ) {
			$data = get_plugin_data(ABSPATH.'wp-content/plugins/new-test-plugin/new-test-plugin.php');
			$plugin_version = $data['Version'];
			$licence_version = new LicenseBoxAPI();
			$license_data = $licence_version->get_latest_version();
			$updater_license = $license_data['latest_version'];
			
			// echo $plugin_version;
			// echo '<br>';
			// echo $updater_license;
			// die;
			
			/*if ( ! is_object( $transient )	 || empty( $transient->checked )  || ! isset( $transient->checked[ $this->plugin_path ] ) ) */
			if(trim($plugin_version) != trim($updater_license))
			{
				// Feed the update data into WP updater
                $response->icons                           = Array(
					'2x' => esc_url( NTPLUGIN_PLUGIN_URL . '/assets/icon-256x256.png' ),
					'1x' => esc_url( NTPLUGIN_PLUGIN_URL . '/assets/icon-128x128.png' )
				);
				$transient->response[ $this->plugin_path ] = $response;

				return $transient;
			}

			if ( ! isset( $transient->response ) || ! is_array( $transient->response ) ) {

				$transient->response = array();
			}

			$license_box_transient = get_transient( 'license_box_response' );

			if ( false === $license_box_transient ) {
				$response              = $this->license_box_api->check_update();
				$license_verify        = $this->license_box_api->verify_license( false, $this->license_key, $this->client_name );
				$latest_version        = $this->license_box_api->get_latest_version();
				$license_box_transient = array(
					'response'       => $response,
					'license_verify' => $license_verify,
					'latest_version' => $latest_version,
				);

				set_transient( 'license_box_response', $license_box_transient, 3 * HOUR_IN_SECONDS );

			} else {
				$response       = $license_box_transient['response'];
				$license_verify = $license_box_transient['license_verify'];
				$latest_version = $license_box_transient['latest_version'];
			}

			if ( isset( $response['status'] ) && $response['status'] && isset( $license_verify['status'] ) && $license_verify['status'] && trim($plugin_version) != trim($updater_license) ) {

				$plugin_args = array(
					'id'          => $this->plugin_id,
					'slug'        => $this->plugin_slug,
					'plugin'      => $this->plugin_path,
					'url'         => $this->plugin_url,
					'license_key' => $this->license_key,
				);

				$plugin_args['new_version'] = isset( $response['version'] ) ? $response['version'] : $latest_version;
				$plugin_args['update_id']   = isset( $response['update_id'] ) ? $response['update_id'] : '';
				$plugin_args['package']     = 'https://wp-updaters.com/api/download_update/main/' . $plugin_args['update_id'];

				$transient->response[ $this->plugin_path ] = (object) $plugin_args;
			}

			return $transient;
		}

		/**
		 * Replace main update function to license box download
		 *
		 * @since 1.0.0
		 *
		 * @param $upgrader_object
		 * @param $options
		 */
		public function download_and_update( $upgrader_object, $options ) {

			if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {
				foreach ( $options['plugins'] as $each_plugin ) {
					if ( $each_plugin == NTPLUGIN_FILE ) {
						$response       = $this->license_box_api->check_update();
						$license_verify = $this->license_box_api->verify_license( false, $this->license_key, $this->client_name );
						$latest_version = $this->license_box_api->get_latest_version();

						if ( isset( $response['status'] ) && $response['status'] && isset( $license_verify['status'] ) && $license_verify['status'] ) {
							remove_action( 'upgrader_process_complete', 'action_upgrader_process_complete', 10, 2 );
							ob_start();
							$this->license_box_api->download_update( $response['update_id'], false, $latest_version, $this->license_key, $this->client_name );
							$update_log = ob_end_clean();

							$args = array(
								'update'     => 'plugin',
								'slug'       => explode( '/', NTPLUGIN_FILE )[0],
								'oldVersion' => 'Version ' . NTPLUGIN_VERSION,
								'newVersion' => 'Version ' . $response['version'],
								'plugin'     => $this->plugin_path,
								'pluginName' => NTPLUGIN_NAME,
							);
							wp_send_json_success( $args );
							wp_die();
						}
					}
				}
			}

		}

		function plugin_api_call( $def, $action, $args ) {

			if ( ! isset( $args->slug ) || $args->slug != $this->plugin_slug ) {
				return $def;
			}

			$plugin_info  = get_site_transient( 'update_plugins' );
			$request_args = array(
				'id'      => $this->plugin_id,
				'slug'    => $this->plugin_slug,
				'version' => ( isset( $plugin_info->checked ) ) ? $plugin_info->checked[ $this->plugin_path ] : 0
				// Current version
			);

			if ( $this->license_key ) {
				$request_args['license'] = $this->license_key;
			}

			$request_string = $this->prepare_request( $action, $request_args );
			$raw_response   = wp_remote_post( $this->api_url, $request_string );

			if ( is_wp_error( $raw_response ) ) {
				$res = new WP_Error( 'plugins_api_failed', __( 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>' ), $raw_response->get_error_message() );
			} else {
				$res = unserialize( $raw_response['body'] );
				if ( $res === false ) {
					$res = new WP_Error( 'plugins_api_failed', __( 'An unknown error occurred' ), $raw_response['body'] );
				}
			}

			/**
			 * Edited for licensebox
			 */
			if ( ! Licensebox_License::check_key() ) {
				unset( $res->download_link );
			}

			/**
			 * End edited for licensebox
			 */

			return $res;
		}

		function prepare_request( $action, $args ) {

			global $wp_version;

			return array(
				'body'       => array(
					'action'  => $action,
					'request' => serialize( $args ),
					'api-key' => md5( home_url() )
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
			);
		}
	}
}

new WP_Updates_Plugin_lb_Updater();