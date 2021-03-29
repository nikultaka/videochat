<?php
/**
 * Plugin Name: Custom options for WooCommerce
 * Description: Add custom options to WooCommerce products
 * Version: 1.5.0
 * Author: Ex-Themes
 * Author URI: https://exthemes.net/
 * Text Domain: product-options-addon
 * WC requires at least: 3.4.0
 * WC tested up to: 4.1.0
 * License: Envato Split Licence
 * Domain Path: /languages/
*/
if(!function_exists('exwo_get_plugin_url')){
	function exwo_get_plugin_url(){
		return plugin_dir_path(__FILE__);
	}
}else{ return;}
define( 'EX_WOO_OPTION_PATH', plugin_dir_url( __FILE__ ) );
// Make sure we don't expose any info if called directly
if ( !defined('EX_WOO_OPTION_PATH') ){
	die('-1');
}
class EX_Woo_Custom_Option{
	public $template_url;
	public $plugin_path;
	public function __construct(){
		$this->includes();
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts') );
		add_action('wp_enqueue_scripts', array( $this, 'frontend_style'),99 );
		add_action('plugins_loaded',array( $this, 'load_textdomain'));
    }
    // load text domain
    function load_textdomain() {
		$textdomain = 'product-options-addon';
		$locale = '';
		if ( empty( $locale ) ) {
			if ( is_textdomain_loaded( $textdomain ) ) {
				return true;
			} else {
				return load_plugin_textdomain( $textdomain, false, plugin_basename( dirname( __FILE__ ) ) . '/language' );
			}
		} else {
			return load_textdomain( $textdomain, plugin_basename( dirname( __FILE__ ) ) . '/' . $textdomain . '-' . $locale . '.mo' );
		}
	}
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;
		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	function includes(){
		include_once exwo_get_plugin_url().'admin/functions.php';
		include_once exwo_get_plugin_url().'inc/functions.php';
	}
	// Load js and css
	function frontend_scripts(){
		wp_enqueue_script( 'ex-woo-options',plugins_url('/js/options-addon.js', __FILE__) , array( 'jquery' ),'1.5' );
	}
	function frontend_style(){
		wp_enqueue_style('ex-woo-options', EX_WOO_OPTION_PATH.'css/style.css','1.5');
		if(is_rtl()){
			wp_enqueue_style('ex-woo-options-rtl', EX_WOOFOOD_PATH.'css/rtl.css');
		}
	}
	
}
$EX_Woo_Custom_Option = new EX_Woo_Custom_Option();