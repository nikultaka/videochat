<?php
/*
WPUpdates Plugin Updater Class
http://wp-updates.com
v2.0
*/



// wp-updater old method method If user has a valid license and new updates are available show the update notification in plugins page.
if ( ! class_exists( 'WP_Updates_Plugin_lb_Updater' ) ) {
	/**
	 * Class WP_Updates_Plugin_lb_Updater
	 *
	 * @property LicenseBoxAPI $license_box_api;
	 */
	class WP_Updates_Plugin_lb_Updater {

	

		function __construct() {

			$this->plugin_path     = plugin_dir_path( __FILE__ );
			$this->plugin_slug     =  plugin_basename( __FILE__ );
			//$this->plugin_url      = ''; plugin url is via api response code
			$this->license_box_api = new LicenseBoxAPI();
			$this->license_key     = ntplugin_license_data( 'license_code' );
			$this->client_name     = ntplugin_license_data( 'client_name' );

			add_filter( 'site_transient_update_plugins', array( &$this, 'check_for_update' ) );
//			add_filter( 'plugins_api', array( &$this, 'plugin_api_call' ), 10, 3 );
		}

		function check_for_update( $transient ) {

			if ( empty( $transient->checked ) || ! isset( $transient->checked[ $this->plugin_path ] ) ) {
				return $transient;
			}

			$plugin_args = array(
				'id'          => $this->plugin_id,
				'slug'        => $this->plugin_slug,
				'plugin'      => $this->plugin_path,
				'url'         => $this->plugin_url,
				'license_key' => $this->license_key,
			);

			$response       = $this->license_box_api->check_update();
			$license_verify = $this->license_box_api->verify_license( false, $this->license_key, $this->client_name );

			// Has update
			if ( isset( $response['status'] ) && $response['status'] && isset( $license_verify['status'] ) && $license_verify['status'] ) {

				$plugin_args['version']     = isset( $response['version'] ) ? $response['version'] : $this->license_box_api->get_current_version();
				$plugin_args['new_version'] = isset( $response['version'] ) ? $response['version'] : $this->license_box_api->get_latest_version();
				$plugin_args['update_id']   = isset( $response['update_id'] ) ? $response['update_id'] : '';

				// Here the actual download link will be added
				$plugin_args['package'] = 'https://wp-updaters.com/api/download_update/main/8dd322dc5f3e76867865';

				$transient->response[ $this->plugin_path ] = (object) $plugin_args;
				$transient->checked[ $this->plugin_path ]  = $this->license_box_api->get_current_version();
			}

//			$destination = $this->license_box_api->root_path."/update_main_".$version.".zip";
//			$this->license_box_api->download_update( $plugin_args['update_id'], true, $this->license_key, $this->client_name );

			return $transient;
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