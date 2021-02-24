<?php
/*
WPUpdates Plugin Updater Class
http://wp-updates.com
v2.0
*/

if ( ! class_exists( 'WP_Updates_Plugin_Updater_2128' ) ) {

	class WP_Updates_Plugin_Updater_2128 {

		var $api_url;
		var $plugin_id = 2128;
		var $plugin_path;
		var $plugin_slug;
		var $license_key;

		// added for wpls start
		var $product_code = 'Woo_TRLS';
		var $shop_url = 'https://dmwds.com';

		// added for wpls end

		function __construct( $api_url, $plugin_path, $license_key = null ) {

			$this->api_url     = $api_url;
			$this->plugin_path = $plugin_path;
			$this->license_key = $license_key;
			if ( strstr( $plugin_path, '/' ) ) {
				list ( $t1, $t2 ) = explode( '/', $plugin_path );
			} else {
				$t2 = $plugin_path;
			}
			$this->plugin_slug = str_replace( '.php', '', $t2 );

			add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'check_for_update' ) );
			add_filter( 'plugins_api', array( &$this, 'plugin_api_call' ), 10, 3 );

			/**
			 * Edited for WooTRLS
			 */
			add_action( "in_plugin_update_message-{$plugin_path}", array( &$this, 'update_message' ), 10, 2 );
			/**
			 * End edited for WooTRLS
			 */

			// This is for testing only!
			//set_site_transient( 'update_plugins', null );

			// Show which variables are being requested when query plugin API
			//add_filter( 'plugins_api_result', array( &$this, 'debug_result' ), 10, 3 );

		}

		function check_for_update( $transient ) {

			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			$request_args = array(
				'id'      => $this->plugin_id,
				'slug'    => $this->plugin_slug,
				'version' => $transient->checked[ $this->plugin_path ]
			);
			if ( $this->license_key ) {
				$request_args['license'] = $this->license_key;
			}

			$request_string = $this->prepare_request( 'update_check', $request_args );
			$raw_response   = wp_remote_post( $this->api_url, $request_string );

			$response = null;
			if ( ! is_wp_error( $raw_response ) && ( $raw_response['response']['code'] == 200 ) ) {
				$response = unserialize( $raw_response['body'] );
			}

			/**
			 * Edited for WooTRLS
			 */
			if ( ! Woo_TRLS_License::check_key() ) {
				unset( $response->package );
			}
			/**
			 * End edited for WooTRLS
			 */

			if ( is_object( $response ) && ! empty( $response ) ) {
				// Feed the update data into WP updater
                
                $response->icons                           = Array(
					'2x' => esc_url( WOO_TRLS_PLUGIN_URL . '/assets/icon-256x256.png' ),
					'1x' => esc_url( WOO_TRLS_PLUGIN_URL . '/assets/icon-128x128.png' )
				);
				$transient->response[ $this->plugin_path ] = $response;

				return $transient;
			}

			// Check to make sure there is not a similarly named plugin in the wordpress.org repository
			if ( isset( $transient->response[ $this->plugin_path ] ) ) {
				if ( strpos( $transient->response[ $this->plugin_path ]->package, 'wordpress.org' ) !== false ) {
					unset( $transient->response[ $this->plugin_path ] );
				}
			}

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
			 * Edited for WooTRLS
			 */
			if ( ! Woo_TRLS_License::check_key() ) {
				unset( $res->download_link );
			}

			/**
			 * End edited for WooTRLS
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

		function debug_result( $res, $action, $args ) {

			echo '<pre>' . print_r( $res, true ) . '</pre>';

			return $res;
		}

		/**
		 * Edited for WooTRLS
		 * Add mesage about plugin license
		 *
		 * @param $plugin_data
		 * @param $response
		 */
		function update_message( $plugin_data, $response ) {

			$message = '&nbsp;<strong>';
			$message .= sprintf( __( '<br>For update, please provide a valid license at <a href="%1$s">Settings page</a>' ), esc_url( admin_url( 'class-woo-trls-admin.php?page=woo-trls' ) ) );
			$message .= '</strong>';
			if ( empty( $response->package ) ) {
				echo $message;
			}

		}

	}
}