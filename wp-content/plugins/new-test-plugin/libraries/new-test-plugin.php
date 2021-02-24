<?php
/**
 * Plugin Name: New Test Plugin
 * Plugin URI: https://dmwds.com
 * Description: Empty plugin for testing update
 * Version: 1.0.5
 * Author: Daniel
 * Author URI: https://dmwds.com
 * Text Domain: nt-plugin  
 * Domain Path: /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 4.0.1
 */

defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'NTPLugin' ) ) {

	define( 'NTPLUGIN_FILE', plugin_basename( __FILE__ ) );
	include_once dirname( __FILE__ ) . '/libraries/class-ntplugin.php';

}


/**
 * The main function for returning NTPLugin instance
 *
 * @since 1.0.0
 *
 * @return object The one and only true NTPLugin instance.
 */
function ntplugin_runner() {

	return NTPLugin::instance();
}

ntplugin_runner();
