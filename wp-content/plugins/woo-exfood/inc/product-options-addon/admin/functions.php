<?php
// admin functions
include 'inc/metadata.php';
include 'inc/global-options.php';

add_action( 'admin_enqueue_scripts', 'exwooop_admin_scripts' );
function exwooop_admin_scripts(){
	$js_params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
	wp_localize_script( 'jquery', 'exwoofood_ajax', $js_params  );
	wp_enqueue_style('exwoo-options', EX_WOO_OPTION_PATH . 'admin/css/style.css','','1.0');
	wp_enqueue_script('exwoo-options', EX_WOO_OPTION_PATH . 'admin/js/admin.js', array( 'jquery' ),'1.0' );
}