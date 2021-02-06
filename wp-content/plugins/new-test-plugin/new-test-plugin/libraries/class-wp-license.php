<?php
/**
 * Class Woo_Kunaki_License
 * Check license key from https://dmwds.com
 *
 * @since 7.3.0



 * var $product_id;
 * var $api_url;
 * var $api_key;
 * var $api_language;
 * var $current_version;
 * var $verify_type;
 * var $verification_period;
 * var $current_path;
 * var $root_path;
 * var $license_file;
 * 'LB-API-KEY' => $this->api_key, 
 * 	'LB-URL' => $this_url, 
 * 	'LB-IP' => $this_ip, 
 * 	'LB-LANG' => $this->api_language
*/
class Licensebox_License {

	/**
	 * Code from wp-updaters.com
	 *
	 * @since 7.3.0
	 *
	 * @var string
	 */
	public static $product_id = 'FDEAFE19';

	/**
	 * Url to  wp-updaters.com
	 *
	 * @since 7.3.0
	 *
	 * @var string
	 */
	public static $api_url = 'https://wp-updaters.com/';
    public static $api_key = '57748FB6FCACE2B58828';
	/**
	 * Option name to store key in WordPress
	 *
	 * @since 7.3.0
	 *
	 * @var string
	 */
	public static $option_name = 'license_code';
    
	/**
	 * Check plugin license key
	 *
	 * @since 7.3.0
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

		if ( empty( $key ) ) {
			return false;
		}

		$api_params = array(
			'verify_license' => $key,
			'license_code'     => $key,
			'product_id'     => urlencode( self::$product_id ),
			'domain'      => $domain,
			'LB-IP'     => isset( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']
		);

		$response = wp_remote_get( add_query_arg( $api_params, self::$api_url ), array(
			'timeout'   => 15,
			'sslverify' => false
		) );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$authorize_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( empty( $authorize_data ) || $authorize_data === null || $authorize_data === false ) {
			return false;
		}

		if ( $authorize_data->info->status == 'Active' ) {
			return true;
		}

		return false;
	}
}