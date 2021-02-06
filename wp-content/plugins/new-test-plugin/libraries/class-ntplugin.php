<?php

/**
 * Main class for NTPlugin plugin
 *
 * @since 1.0.0
 */

defined( 'ABSPATH' ) || exit;

class NTPLugin {

	/**
	 * The one and only true NTPLugin instance
	 *
	 * @since 1.0.0
	 * @access private
	 * @var object $instance
	 */
	private static $instance;

	/**
	 * Plugin version
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $version = '1.0.0';

	/**
	 * Instantiate the main class
	 *
	 * This function instantiates the class, initialize all functions and return the object.
	 *
	 * @since 1.0.0
	 * @return object The one and only true NTPLugin instance.
	 */
	public static function instance() {

		if ( ! isset( self::$instance ) && ( ! self::$instance instanceof NTPLugin ) ) {

			self::$instance = new NTPLugin;
			self::$instance->set_up_constants();
			self::$instance->includes();

		}

		return self::$instance;
	}

	/**
	 * Function for setting up constants
	 *
	 * This function is used to set up constants used throughout the plugin.
	 *
	 * @since 1.0.0
	 */
	public function set_up_constants() {

		self::set_up_constant( 'NTPLUGIN_VERSION', $this->version );
		self::set_up_constant( 'NTPLUGIN_PLUGIN_PATH', plugin_dir_path( __FILE__ ) . '../' );
		self::set_up_constant( 'NTPLUGIN_PLUGIN_URL', plugin_dir_url( __FILE__ ) . '../' );
		self::set_up_constant( 'NTPLUGIN_LIBRARIES_PATH', plugin_dir_path( __FILE__ ) );
		self::set_up_constant( 'NTPLUGIN_DEBUG', true );

	}

	/**
	 * Make new constants
	 *
	 * @param string $name
	 * @param mixed $val
	 */
	public static function set_up_constant( $name, $val = false ) {

		if ( ! defined( $name ) ) {
			define( $name, $val );
		}

	}

	/**
	 * Includes all necessary PHP files
	 *
	 * This function is responsible for including all necessary PHP files.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		if ( defined( 'NTPLUGIN_LIBRARIES_PATH' ) ) {
			require NTPLUGIN_LIBRARIES_PATH . 'lb_helper.php';
			require NTPLUGIN_LIBRARIES_PATH . 'class-setting.php';
			require NTPLUGIN_LIBRARIES_PATH . 'class-wp-updates.php';
		}

	}

	/**
	 * Save message to log file in plugin path
	 *
	 * @since 1.0.0
	 *
	 * @param $message
	 */
	public static function save_log( $message ) {

		if ( defined( 'NTPLUGIN_PLUGIN_PATH' ) ) {
			$log_name        = date( 'd-m-Y' );
			$parser_log_file = NTPLUGIN_PLUGIN_PATH . '/temp/' . $log_name . '.log';
			$log_message     = date( 'd.m.Y H:i:s' ) . ' : ' . $message . PHP_EOL;

			file_put_contents( $parser_log_file, $log_message, FILE_APPEND | LOCK_EX );

			if ( defined( 'DISPLAY_LOG' ) && DISPLAY_LOG == true ) {
				echo '<br>';
				esc_html_e( $log_message );
			}
		}

	}

}