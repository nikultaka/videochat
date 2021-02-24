<?php
/**
 * Plugin Name: Woo Tradelines
 * Plugin URI: https://dmwds.com/shop/woo-commerce-trade-lines/
 * Description: Wootradelines adds tradelines product type to your WooCommerce shop enabling you to sell tradelines.
 * Version: 1
 * Author: Dmwds
 * Author URI: https://dmwds.com
 * Text Domain: wootrls
 * Domain Path: /languages
 * WC requires at least: 3.5.0
 * WC tested up to: 3.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * The main function for returning Woo_Kunaki_Light instance
 *
 * @since 1.0.1
 * @since 2.2.5 WooCommerce activation check
 * @return bool|object Woo_TRLS instance
 */
function woo_trls_runner() {

	$active_plugins = apply_filters( 'active_plugins', get_option( 'active_plugins' ) );
	$woo_active     = in_array( 'woocommerce/woocommerce.php', $active_plugins );

	if ( $woo_active ) {
		if ( ! class_exists( 'Woo_TRLS' ) ) {
			include_once dirname( __FILE__ ) . '/includes/class-woo-trls.php';
		}

		//License checker
		require_once( 'lib/class-wp-updates-plugin.php' );
		new WP_Updates_Plugin_Updater_2128( 'http://wp-updates.com/api/2/plugin', plugin_basename( __FILE__ ) );

		return Woo_TRLS::instance();
	}

	return false;
}

add_action( 'init', 'woo_trls_runner', 4 );