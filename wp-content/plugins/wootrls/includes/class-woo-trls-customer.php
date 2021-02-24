<?php
/*
 * Woo Tradelines functionaliy for customer
 */

class Woo_TRLS_Customer {

	/**
	 * Construct all admin part
	 *
	 * @since  1.0.0
	 */
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_shortcode( 'wootrl-style-1', array( $this, 'shop_style_1' ) );
		add_shortcode( 'wootrl-style-2', array( $this, 'shop_style_2' ) );
		add_shortcode( 'wootrl-style-3', array( $this, 'shop_style_3' ) );
		add_shortcode( 'wootrl-style-all', array( $this, 'shop_style_all' ) );
		//Remove decimals from woocommerce price
		add_filter( 'woocommerce_price_trim_zeros', '__return_true' );

	}

	/**
	 * Load styles and scripts
	 *
	 * @since  1.0.0
	 */
	public function add_scripts() {

		wp_enqueue_style( 'woo-trls-style-1', WOO_TRLS_PLUGIN_URL . 'assets/style-1.css', array(), WOO_TRLS_VERSION );
        wp_enqueue_style( 'woo-trls-style-2', WOO_TRLS_PLUGIN_URL . 'assets/style-2.css', array(), WOO_TRLS_VERSION );
		wp_enqueue_style( 'woo-trls-style-3', WOO_TRLS_PLUGIN_URL . 'assets/style-3.css', array(), WOO_TRLS_VERSION );
		wp_enqueue_script( 'woo-trls-style-all', WOO_TRLS_PLUGIN_URL . 'assets/style-all.js', array( 'jquery' ), WOO_TRLS_VERSION, true );

	}

	/**
	 * Load class with shop style 1
	 *
	 * @since 1.0.0
	 */
	public function shop_style_1( $atts ) {

		include_once WOO_TRLS_INC_PATH . 'class-woo-trls-style-1.php';

	}

	public function shop_style_2( $atts ) {

		include_once WOO_TRLS_INC_PATH . 'class-woo-trls-style-2.php';

	}

	public function shop_style_3( $atts ) {

		include_once WOO_TRLS_INC_PATH . 'class-woo-trls-style-3.php';

	}

	public function shop_style_all( $atts ) {

		include_once WOO_TRLS_INC_PATH . 'class-woo-trls-style-all.php';

	}

}

/**
 * Run WooTRLSCustomer class
 *
 * @since 1.0.0
 *
 * @return Woo_TRLS_Customer
 */
function woo_trls_customer_runner() {

	return new Woo_TRLS_Customer();
}

//Run just in not admin part
if ( ! is_admin() ) {
	woo_trls_customer_runner();
}