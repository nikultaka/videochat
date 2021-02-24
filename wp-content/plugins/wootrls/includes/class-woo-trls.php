<?php
/**
 * Class Woo_TRLS
 * Main class for Woo Tradelines
 *
 * @since 2.1.8
 */

class Woo_TRLS {

	/**
	 * The one and only true WooTRLS instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance
	 */
	private static $instance;

	/**
	 * License key for wp-updates.com
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $license;

	/**
	 * Plugin version
	 *
	 * @since 2.1.5
	 * @var string
	 */
	private $version = '2.2.2';

	/**
	 * Instantiate the main class
	 *
	 * This function instantiates the class, initialize all functions and return the object.
	 *
	 * @since 1.0.0
	 * @return object The one and only true WooTRLS instance.
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ( ! self::$instance instanceof Woo_TRLS ) ) {

			self::$instance = new Woo_TRLS;
			self::$instance->setup_constants();
			self::$instance->includes();
			self::$instance->inits();

		}

		return self::$instance;
	}

	/**
	 * Function for setting up constants
	 *
	 * This function is used to set up constants used throughout the plugin.
	 *
	 * @since 1.0.0
	 * @since 2.1.5 Get version from private variable
	 */
	public function setup_constants() {

		if ( ! defined( 'WOO_TRLS_VERSION' ) ) {
			define( 'WOO_TRLS_VERSION', $this->version );
		}

		if ( ! defined( 'WOO_TRLS_INC_PATH' ) ) {
			define( 'WOO_TRLS_INC_PATH', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'WOO_TRLS_PLUGIN_URL' ) ) {
			define( 'WOO_TRLS_PLUGIN_URL', plugin_dir_url( __FILE__ ) . '../' );
		}

		if ( ! defined( 'WOO_TRLS_PRODUCT_TYPE' ) ) {
			define( 'WOO_TRLS_PRODUCT_TYPE', 'tradeline' );
		}

	}

	/**
	 * Includes all necessary PHP files
	 *
	 * This function is responsible for including all necessary PHP files.
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Include class-woo-trls-license.php (WooTRLSLicense class)
	 */
	public function includes() {

		include WOO_TRLS_INC_PATH . '../lib/class-woo-trls-pagination.php';
		include WOO_TRLS_INC_PATH . 'admin/class-woo-trls-admin.php';
		include WOO_TRLS_INC_PATH . 'class-woo-trls-customer.php';
		include WOO_TRLS_INC_PATH . 'class-woo-trls-license.php';
		include WOO_TRLS_INC_PATH . '../lib/class-woo-trls-order.php';
		include WOO_TRLS_INC_PATH . '../vc_extension/vc_widget.php';

	}

	/**
	 * Initialize hooks
	 *
	 * @since 1.0.0
	 */
	public function inits() {

		add_action( 'init', array( $this, 'register_product_type' ), 5 );
		add_filter( 'woocommerce_product_class', array( $this, 'add_product_class' ), 10, 2 );
		add_filter( 'product_type_selector', array( $this, 'add_product_type_title' ) );

	}


	/**
	 * Register new class with new product type
	 *
	 * @since 1.0.0
	 */
	public function register_product_type() {

		include WOO_TRLS_INC_PATH . '../lib/class-wc-product-tradeline.php';

	}

	/**
	 * Add new Class with type for product
	 *
	 * @since 1.0.0
	 *
	 * @param $classname
	 * @param $product_type
	 *
	 * @return string
	 */
	function add_product_class( $classname, $product_type ) {

		if ( $product_type == WOO_TRLS_PRODUCT_TYPE ) {
			$classname = 'WC_Product_Tradeline';
		}

		return $classname;
	}

	/**
	 * Add title to new product type
	 * @since 1.0.0
	 *
	 * @param $types
	 *
	 * @return mixed
	 */
	function add_product_type_title( $types ) {

		$types[ WOO_TRLS_PRODUCT_TYPE ] = 'Tradeline';

		return $types;
	}

}