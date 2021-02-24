<?php
/**
 * Class WooTRLSLicense
 * Check license key from https://dmwds.com
 *
 * @since 1.0.1
 */

class Woo_TRLS_License {

	/**
	 * Code from dmwds.com
	 *
	 * @since 1.0.1
	 *
	 * @var string
	 */
	public static $product_code = 'wootrls19';

	/**
	 * Url to dmwds.com
	 *
	 * @since 1.0.1
	 *
	 * @var string
	 */
	public static $shop_url = 'https://dmwds.com';

	/**
	 * Activator for debug mode
	 *
	 * @since 1.0.1
	 *
	 * @var bool
	 */
	public static $debug = false;

	/**
	 * Option name to store key in WordPress
	 *
	 * @since 1.0.1
	 *
	 * @var string
	 */
	public static $option_name = 'wootrls_license_key';

	/**
	 * Check plugin license key
	 *
	 * @since 1.0.1
	 *
	 * @param $key
	 *
	 * @return bool
	 */
	public static function check_key( $key = '' ) {

		$domain = $_SERVER['SERVER_NAME'];
		if ( substr( $domain, 0, 4 ) == "www." ) {
			$domain = substr( $domain, 4 );
		}

		if ( empty( $key ) ) {
			$key = get_option( self::$option_name, null );
		}

		$api_params = array(
			'wpls-verify' => $key,
			'license'     => $key,
			'product'     => urlencode( self::$product_code ),
			'domain'      => $domain,
			'validip'     => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']
		);

		$response = wp_remote_get( add_query_arg( $api_params, self::$shop_url ), array(
			'timeout'   => 15,
			'sslverify' => false
		) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$authorize_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( self::$debug ) {
			self::log( print_r( $authorize_data, 1 ) );
		}

		if ( empty( $authorize_data ) || $authorize_data === null || $authorize_data === false ) {
			return false;
		}

		if ( $authorize_data->info->status == 'Active' ) {
			return true;
		}

		return false;
	}

	/**
	 * Log all answers
	 *
	 * @since 1.0.1
	 *
	 * @param $message
	 * @param bool $success
	 * @param bool $end
	 */
	public static function log( $message, $success = true, $end = false ) {

		if ( ! file_exists( WP_CONTENT_DIR . '/uploads/log.txt' ) ) {
			file_put_contents( WP_CONTENT_DIR . '/uploads/log.txt', 'Shipping Logs' . "\r\n" );
		}

		$text = "[" . date( "m/d/Y g:i A" ) . "] - " . ( $success ? "SUCCESS :" : "FAILURE :" ) . $message . "\n";

		if ( $end ) {
			$text .= "\n------------------------------------------------------------------\n\n";
		}

		$debug_log_file_name = WP_CONTENT_DIR . '/uploads/log.txt';
		$fp                  = fopen( $debug_log_file_name, "a" );
		fwrite( $fp, $text );
		fclose( $fp );

	}
}