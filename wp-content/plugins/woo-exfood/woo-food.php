<?php
/*
Plugin Name: WooCommerce Food
Plugin URI: https://exthemes.net/woocommerce-food/
Description: Restaurant Menu & Food ordering
Version: 2.4.1
Author: Ex-Themes
Author URI: https://exthemes.net
Text Domain: woocommerce-food
WC tested up to: 4.1.1
License: Envato Split Licence
Domain Path: /languages/
*/
define( 'EX_WOOFOOD_PATH', plugin_dir_url( __FILE__ ) );
// Make sure we don't expose any info if called directly
if ( !defined('EX_WOOFOOD_PATH') ){
	die('-1');
}
if(!function_exists('exwoof_get_plugin_url')){
	function exwoof_get_plugin_url(){
		return plugin_dir_path(__FILE__);
	}
}
if(!function_exists('exwf_check_woo_exists')){
	function exwf_check_woo_exists() {
		$class = 'notice notice-error';
		$message = esc_html__( 'WooCommerce is Required to WooCommerce Food plugin work, please install or activate WooCommerce plugin', 'exthemes' );
	
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (!is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		add_action( 'admin_notices', 'exwf_check_woo_exists' );
		return;
	}
}
class EX_WOOFood{
	public $template_url;
	public $plugin_path;
	public function __construct(){
		$this->includes();
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts') );
		add_filter( 'template_include', array( $this, 'template_loader' ),99 );
		add_action('wp_enqueue_scripts', array( $this, 'frontend_style'),99 );
		add_action('plugins_loaded',array( $this, 'load_textdomain'));
		add_action( 'after_setup_theme', array( $this, 'calthumb_register') );
    }
    // load text domain
    function load_textdomain() {
		$textdomain = 'woocommerce-food';
		$locale = '';
		if ( empty( $locale ) ) {
			if ( is_textdomain_loaded( $textdomain ) ) {
				return true;
			} else {
				return load_plugin_textdomain( $textdomain, false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
			}
		} else {
			return load_textdomain( $textdomain, plugin_basename( dirname( __FILE__ ) ) . '/woocommerce-food/' . $textdomain . '-' . $locale . '.mo' );
		}
	}
	//thumbnails register
	function calthumb_register(){
		add_image_size('exwoofood_80x80',120,120, true);
		add_image_size('exwoofood_400x400',400,400, true);
	}
	public function plugin_path() {
		if ( $this->plugin_path ) return $this->plugin_path;
		return $this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	function template_loader($template){		
		if(is_tax('exwoofood_loc')){
			wp_redirect( get_template_part( '404' ) ); exit;
		}
		return $template;		
	}

	function includes(){
		include_once exwoof_get_plugin_url().'admin/functions.php';
		include_once exwoof_get_plugin_url().'inc/functions.php';
	}
	// Load js and css
	function frontend_scripts(){
		$main_font_default='Source Sans Pro';
		$g_fonts = array($main_font_default);
		$exwoofood_font_family = exwoofood_get_option('exwoofood_font_family');
		if($exwoofood_font_family!=''){
			$exwoofood_font_family = exwoofood_get_google_font_name($exwoofood_font_family);
			array_push($g_fonts, $exwoofood_font_family);
		}
		$exwoofood_headingfont_family = exwoofood_get_option('exwoofood_headingfont_family');
		if($exwoofood_headingfont_family!=''){
			$exwoofood_headingfont_family = exwoofood_get_google_font_name($exwoofood_headingfont_family);
			array_push($g_fonts, $exwoofood_headingfont_family);
		}
		$wt_googlefont_js = exwoofood_get_option('exwoofood_disable_ggfont','exwoofood_js_css_file_options');
		if($wt_googlefont_js!='yes'){
			wp_enqueue_style( 'ex-google-fonts', exwoofood_get_google_fonts_url($g_fonts), array(), '1.0.0' );
		}
	}
	function frontend_style(){
		$js_params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
		wp_localize_script( 'jquery', 'exwf_jspr', $js_params  );
		$api_map = exwoofood_get_option('exwoofood_gg_api','exwoofood_shpping_options');
		if($api_map!=''){
			$map_lang = urlencode(apply_filters('exwf_map_lang','en'));
			wp_enqueue_script( 'exwf-auto-address', '//maps.googleapis.com/maps/api/js?key='.esc_attr($api_map).'&language='.$map_lang.'&libraries=places');
		}
		wp_enqueue_script( 'ex-woo-food',plugins_url('/js/food.min.js', __FILE__) , array( 'jquery' ),'2.4' );
		$exwoofood_custom_js = exwoofood_get_option('exwoofood_custom_js','exwoofood_custom_code_options');
   		wp_add_inline_script( 'ex-woo-food', $exwoofood_custom_js );
		wp_enqueue_script( 'ex-woo-food-ajax-cart',plugins_url('/js/ajax-add-to-cart.min.js', __FILE__) , array( 'jquery','wc-add-to-cart' ),'2.0' );
		wp_enqueue_style('ex-woo-food', EX_WOOFOOD_PATH.'css/style.css','2.4');
		wp_enqueue_style('ionicon', EX_WOOFOOD_PATH.'css/ionicon/css/ionicons.min.css','1.0');
		wp_enqueue_style('ex-woo-food-list', EX_WOOFOOD_PATH.'css/style-list.css','1.0');
		wp_enqueue_style('ex-woo-food-table', EX_WOOFOOD_PATH.'css/style-table.css','1.0');
		wp_enqueue_style('ex-woo-food-modal', EX_WOOFOOD_PATH.'css/modal.css','1.5.2');
		wp_enqueue_style( 'ex-wp-s_lick', EX_WOOFOOD_PATH.'js/ex_s_lick/ex_s_lick.css');
		wp_enqueue_style( 'ex_wp_s_lick-theme', EX_WOOFOOD_PATH.'js/ex_s_lick/ex_s_lick-theme.css');
		wp_enqueue_script( 'ex_wp_s_lick', EX_WOOFOOD_PATH.'js/ex_s_lick/ex_s_lick.js', array( 'jquery' ),'1.0' );
		$exwoofood_enable_rtl = exwoofood_get_option('exwoofood_enable_rtl');
		wp_enqueue_style(
	        'exwoofood-custom-css',
	        EX_WOOFOOD_PATH.'js/ex_s_lick/ex_s_lick.css'
	    );
		if($exwoofood_enable_rtl=='yes' || is_rtl()){
			wp_enqueue_style('ex-woo-food-rtl', EX_WOOFOOD_PATH.'css/rtl.css');
			wp_enqueue_style(
		        'exwoofood-custom-css',
		        EX_WOOFOOD_PATH.'css/rtl.css'
		    );
		}
		require exwoof_get_plugin_url(). 'css/custom.css.php';
		$ctcss = exwoofood_custom_css();
		wp_add_inline_style( 'exwoofood-custom-css', $ctcss );
	}
	
}
$EX_WOOFood = new EX_WOOFood();