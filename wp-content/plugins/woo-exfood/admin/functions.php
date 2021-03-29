<?php
include 'class-food-taxonomy.php';
include 'shortcode-builder.php';

add_action( 'admin_enqueue_scripts', 'exwoofood_admin_scripts' );
function exwoofood_admin_scripts(){
	$js_params = array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) );
	wp_localize_script( 'jquery', 'exwoofood_ajax', $js_params  );
	wp_enqueue_style('ex-woo-food', EX_WOOFOOD_PATH . 'admin/css/style.css','','2.0');
	wp_enqueue_script('ex-woo-food', EX_WOOFOOD_PATH . 'admin/js/admin.js', array( 'jquery' ),'2.0' );
}



add_filter( 'manage_exwoofood_scbd_posts_columns', 'exwoofood_edit_scbd_columns',99 );
function exwoofood_edit_scbd_columns( $columns ) {
	unset($columns['date']);
	$columns['layout'] = esc_html__( 'Type' , 'woocommerce-food' );
	$columns['shortcode'] = esc_html__( 'Shortcode' , 'woocommerce-food' );
	$columns['date'] = esc_html__( 'Publish date' , 'woocommerce-food' );		
	return $columns;
}
add_action( 'manage_exwoofood_scbd_posts_custom_column', 'exwoofood_scbd_custom_columns',12);
function exwoofood_scbd_custom_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'layout':
			$sc_type = get_post_meta($post->ID, 'sc_type', true);
			$exwoofood_id = $post->ID;
			echo '<span class="layout">'.wp_kses_post($sc_type).'</span>';
			break;
		case 'shortcode':
			$_shortcode = get_post_meta($post->ID, '_shortcode', true);
			echo '<input type="text" readonly name="_shortcode" value="'.esc_attr($_shortcode).'">';
			break;	
	}
}

function exwoofood_id_taxonomy_columns( $columns ){
	$columns['cat_id'] = esc_html__('ID','woocommerce-food');

	return $columns;
}
add_filter('manage_edit-product_cat_columns' , 'exwoofood_id_taxonomy_columns');
function exwoofood_taxonomy_columns_content( $content, $column_name, $term_id ){
    if ( 'cat_id' == $column_name ) {
        $content = $term_id;
    }
	return $content;
}
add_filter( 'manage_product_cat_custom_column', 'exwoofood_taxonomy_columns_content', 10, 3 );

add_action('wp_ajax_exfd_change_order_menu', 'wp_ajax_exfd_change_order_menu' );
function wp_ajax_exfd_change_order_menu(){
	$post_id = $_POST['post_id'];
	$value = $_POST['value'];
	if ($value == '') {
		$value = 0;
	}
	if(isset($post_id) && $post_id != 0)
	{
		update_term_meta($post_id, 'exwoofood_menu_order', esc_attr($value));
	}
	die;
}
// Order column
add_filter( 'manage_product_posts_columns', 'exwf_edit_columns',99 );
function exwf_edit_columns( $columns ) {
	$columns['exwoofood_order'] = esc_html__( 'CT Order' , 'woocommerce-food' );	
	return $columns;
}
add_action( 'manage_product_posts_custom_column', 'exwf_custom_columns',12);
function exwf_custom_columns( $column ) {
	global $post;
	switch ( $column ) {	
		case 'exwoofood_order':
			$exwf_order = get_post_meta($post->ID, 'exwoofood_order', true);
			echo '<input type="number" style="max-width:50px" data-id="' . $post->ID . '" name="exwoofood_order" value="'.esc_attr($exwf_order).'">';
			break;
	}
}

add_action( 'wp_ajax_exwoofood_change_sort_food', 'exwf_change_sort' );
function exwf_change_sort(){
	$post_id = $_POST['post_id'];
	$value = $_POST['value'];
	if(isset($post_id) && $post_id != 0)
	{
		update_post_meta($post_id, 'exwoofood_order', esc_attr(str_replace(' ', '', $value)));
	}
	die;
}
// upgrade data of delivery time from 1.1.2 to 1.2
add_action( 'init', 'exwf_update_option' );
if(!function_exists('exwf_update_option')){
	function exwf_update_option() {
		if (get_option('_exwp_udoption')!='updated' && is_user_logged_in() && current_user_can( 'manage_options' ) && function_exists('exwoofood_get_option')){
			$_timesl = exwoofood_get_option('exwoofood_ck_times','exwoofood_advanced_options');
			if(is_array($_timesl) && !empty($_timesl)){
				$_newtsl= array();
				foreach ($_timesl as $value) {
					$_newtsl[] = array(
						'name-ts'=> $value
					);
				}
				if(!empty($_newtsl)){
					$all_options = get_option( 'exwoofood_advanced_options' );
					$all_options['exwoofood_ck_times'] = '';
					$all_options['exwfood_deli_time'] = $_newtsl;
					update_option( 'exwoofood_advanced_options', $all_options );
				}
			}	
			update_option( '_exwp_udoption', 'updated' );
		}else if(is_user_logged_in() && current_user_can( 'manage_options' )){
			if(isset($_GET['exot_reset']) && $_GET['exot_reset']=='yes' && isset($_GET['page']) && strpos($_GET['page'], 'exwoofood') !== false ){
				update_option( $_GET['page'], '' );
			}
		}
	}
}
// active into
if(!function_exists('exwf_check_purchase_code') && is_admin()){
	function exwf_check_purchase_code() {
		$class = 'notice notice-error';
		$message =  'You are using an unregistered version of WooCommerce Food, please <a href="'.esc_url(admin_url('admin.php?page=exwoofood_verify_options')).'">active your license</a> of WooCoommerce Food';
	
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
	function exwf_invalid_pr_code() {
		$class = 'notice notice-error';
		$message =  'Invalid purchase code for WooCommerce Food plugin, please find check how to find your purchase code <a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">here </a>';
	
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message ); 
	}
	$scd_ck = get_option( 'exwf_ckforupdate');
	$crt = strtotime('now');
	if($scd_ck=='' || $crt > $scd_ck ){
		$check_version = '';
		global $pagenow;
		if((isset($_GET['page']) && ($_GET['page'] =='exwoofood_options' || $_GET['page'] =='exwoofood_verify_options' )) || (isset($_GET['post_type']) && $_GET['post_type']=='product') || $pagenow == 'plugins.php' ){
			$_name = exwoofood_get_option('exwoofood_evt_name','exwoofood_verify_options');
			$_pcode = exwoofood_get_option('exwoofood_evt_pcode','exwoofood_verify_options');
			if ($_name =='' || $_pcode=='' ) {
				add_action( 'admin_notices', 'exwf_check_purchase_code' );
				return;
			}else{
				$site = get_site_url();
				$url = 'https://exthemes.net/verify-purchase-code/';
				$myvars = 'buyer=' . $_name . '&code=' . $_pcode. '&site='.$site.'&item_id=25457330';
				$res = '';
				if(function_exists('stream_context_create')){
					$data = array('buyer' => $_name, 'code' => $_pcode, 'item_id' =>'25457330', 'site' => $site);
					$options = array(
					        'http' => array(
					        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					        'method'  => 'POST',
					        'content' => http_build_query($data),
					    )
					);

					$context  = stream_context_create($options);
					$res = file_get_contents($url, false, $context);
				}
				if($res!=''){
					$res = json_decode($res);
				}else{
					$ch = curl_init( $url );
					curl_setopt( $ch, CURLOPT_POST, 1);
					curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
					curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
					curl_setopt( $ch, CURLOPT_HEADER, 0);
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
					curl_setopt($ch, CURLOPT_TIMEOUT, 2);
					$res=json_decode(curl_exec($ch),true);
					curl_close($ch);
				}
				//print_r( $res) ;exit;
				if(isset($res[0]) && $res[0] == 'error'){
					add_action( 'admin_notices', 'exwf_invalid_pr_code' );
					update_option( 'exwf_ckforupdate', '' );
					return;
				}else if(isset($res[0]) && $res[0] == 'success'){
					$check_version = isset($res[5]) ? $res[5] : '';
					update_option( 'exwf_ckforupdate', strtotime('+10 day') );
				}else{
					update_option( 'exwf_ckforupdate', strtotime('+5 day') );
				}
			}
		}
		if( ! function_exists('get_plugin_data') ){
	        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	    }
	    if (file_exists( WP_PLUGIN_DIR.'/woocommerce-food/woo-food.php' ) ) {
	    	$plugin_data = get_plugin_data( WP_PLUGIN_DIR  . '/woocommerce-food/woo-food.php' );
	    }else{
		    $plugin_data = get_plugin_data( WP_PLUGIN_DIR  . '/woo-exfood/woo-food.php' );
		}
	    $plugin_version = str_replace('.', '',$plugin_data['Version']);
	    $check_version = $check_version !='' ? str_replace('.', '',$check_version) : '';
	    if(strlen($check_version) > strlen($plugin_version)){
	    	$plugin_version = is_numeric($plugin_version) ?  $plugin_version *10 : '';
	    }else if(strlen($check_version) < strlen($plugin_version)){
	    	$check_version = is_numeric($check_version) ?  $check_version *10 : '';
	    }
	 	if($check_version!='' && $check_version > $plugin_version){
	 		if (file_exists( WP_PLUGIN_DIR.'/woocommerce-food/woo-food.php' ) ) {
	 			add_action( 'after_plugin_row_woocommerce-food/woo-food.php', 'show_purchase_notice_under_plugin', 10 );
	 		}else{
				add_action( 'after_plugin_row_woo-exfood/woo-food.php', 'show_purchase_notice_under_plugin', 10 );
			}
			function show_purchase_notice_under_plugin(){
				$text = sprintf(
					esc_html__( 'There is a new version of WooComemrce Food available. %1$s View details %2$s and please check how to update plugin %3$s here%4$s.', 'woocommerce-food' ),
						'<a href="https://codecanyon.net/item/woocommerce-food-restaurant-menu-food-ordering/25457330#item-description__changelog" target="_blank">',
						'</a>', 
						'<a href="https://exthemes.net/woocommerce-food/doc/#!/install-file" target="_blank">',
						'</a>'
					);
				echo '
				<style>[data-slug="woo-exfood"].active td,[data-slug="woo-exfood"].active th { box-shadow: none;}</style>
				<tr class="plugin-update-tr active">
					<td colspan="3" class="plugin-update">
						<div class="update-message notice inline notice-alt"><p>'.$text.'</p></div>
					</td>
				</tr>';
			}
		}
	}
}
//print_r(exwf_license_infomation());exit;
function exwf_license_infomation(){
	$scd_ck = get_option( 'exwf_ckforupdate');
	$crt = strtotime('now');
	$res = '';
	if($scd_ck=='' || $crt > $scd_ck ){
		$_name = exwoofood_get_option('exwoofood_evt_name','exwoofood_verify_options');
		$_pcode = exwoofood_get_option('exwoofood_evt_pcode','exwoofood_verify_options');

		$site = get_site_url();
		$url = 'https://exthemes.net/verify-purchase-code/';
		$myvars = 'buyer=' . $_name . '&code=' . $_pcode. '&site='.$site.'&item_id=25457330';
		$res = '';
		if(function_exists('stream_context_create')){
			$data = array('buyer' => $_name, 'code' => $_pcode, 'item_id' =>'25457330', 'site' => $site);
			$options = array(
			        'http' => array(
			        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
			        'method'  => 'POST',
			        'content' => http_build_query($data),
			    )
			);

			$context  = stream_context_create($options);
			$res = file_get_contents($url, false, $context);
		}
		if($res!=''){
			$res = json_decode($res);
		}else{
			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_POST, 1);
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
			curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
			curl_setopt( $ch, CURLOPT_HEADER, 0);
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			$res=json_decode(curl_exec($ch),true);
			curl_close($ch);
		}
		//print_r( $res) ;exit;
		if(isset($res[0]) && $res[0] == 'error'){
			update_option( 'exwf_ckforupdate', '' );
		}else if(isset($res[0]) && $res[0] == 'success'){
			update_option( 'exwf_ckforupdate', strtotime('+10 day') );
		}else{
			update_option( 'exwf_ckforupdate', strtotime('+5 day') );
		}
	}
	return $res;
}

// Show delivery date column
add_filter( 'manage_shop_order_posts_columns', 'exwf_edit_order_columns',99 );
function exwf_edit_order_columns( $columns ) {
	$method_ship = exwoofood_get_option('exwoofood_enable_method','exwoofood_shpping_options');
	if($method_ship!=''){
		$columns['order-method'] = esc_html__( 'Order method' , 'woocommerce-food' );
	}
	$columns['date-delivery'] = esc_html__( 'Delivery time' , 'woocommerce-food' );	
	return $columns;
}
add_action( 'manage_shop_order_posts_custom_column', 'exwf_admin_order_delivery_columns',12);
function exwf_admin_order_delivery_columns( $column ) {
	global $post;
	switch ( $column ) {
		case 'order-method':
			$exfood_id = $post->ID;
			$order_method = get_post_meta( $exfood_id, 'exwfood_order_method', true );
			$order_method = $order_method=='takeaway' ? esc_html__('Takeaway','woocommerce-food') : ( $order_method=='dinein' ? esc_html__('Dine-in','woocommerce-food') : esc_html__('Delivery','woocommerce-food'));
			echo '<span class="order-method">'.$order_method.'</span>';
			break;
		case 'date-delivery':
			$exfood_id = $post->ID;
			echo '<span class="exfood_id">'.get_post_meta( $exfood_id, 'exwfood_date_deli', true ).' '.get_post_meta( $exfood_id, 'exwfood_time_deli', true ).'</span>';
			break;	
	}
}

/***** add filter order by delivery date *****/
if(!function_exists('exwf_admin_filter_order_delivery')){
	function exwf_admin_filter_order_delivery( $post_type, $which ) {
		if ( $post_type == 'shop_order' ) {	
			wp_enqueue_script('jquery-ui-core');
			wp_enqueue_script('jquery-ui-datepicker');
			wp_enqueue_script('jquery-ui-datetimepicker');
			// Display filter HTML
			echo '<input type="text" class="date-picker" name="date_delivery" placeholder="'.esc_html__( 'Select delivery date', 'woocommerce-food' ).'" value="'.(isset( $_GET['date_delivery'] ) ? $_GET['date_delivery'] : '' ).'">';

			$method_ship = exwoofood_get_option('exwoofood_enable_method','exwoofood_shpping_options');
			$dine_in = exwoofood_get_option('exwoofood_enable_dinein','exwoofood_shpping_options');
			if($dine_in=='yes' && $method_ship!='' || $method_ship=='both'){
				echo "<select name='method' id='method' class='postform'>";
				echo '<option value="">' . esc_html__( 'All Shipping methods', 'exthemes' ) . '</option>';
				if($method_ship!='takeaway'){
					echo '<option value="delivery" '.(( isset( $_GET['method'] ) && ( $_GET['method'] == 'delivery' ) ) ? ' selected="selected"' : '' ).'>'.esc_html__( 'Deilvery', 'woocommerce-food' ).'</option>';
				}
				if($method_ship!='delivery'){
					echo '<option value="takeaway" '.(( isset( $_GET['method'] ) && ( $_GET['method'] == 'takwaway' ) ) ? ' selected="selected"' : '' ).'>'.esc_html__( 'Takeaway', 'woocommerce-food' ).'</option>';
				}
				if($dine_in=='yes'){
					echo '<option value="dinein" '.(( isset( $_GET['method'] ) && ( $_GET['method'] == 'dinein' ) ) ? ' selected="selected"' : '' ).'>'.esc_html__( 'Dine-in', 'woocommerce-food' ).'</option>';
				}
				echo '</select>';
			}
			$args = array(
				'hide_empty'        => true,
				'parent'        => '0',
			);
			$loc_selected = isset( $_GET['floc'] ) ? ( $_GET['floc'] ) : '';
			$terms = get_terms('exwoofood_loc', $args);
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){?>
				<select class="postform" name="floc">
					<?php 
			    	$count_stop = 5;
			    	echo '<option value="">'.esc_html__( '-- Select --', 'woocommerce-food' ) .'</option>';
			    	foreach ( $terms as $term ) {
			    		$select_loc = '';
			    		if ($term->slug !='' && $term->slug == $loc_selected) {
			                $select_loc = ' selected="selected"';
			              }
				  		echo '<option value="'. esc_attr($term->slug) .'" '.$select_loc.'>'. wp_kses_post($term->name) .'</option>';
				  		echo exfd_show_child_location('',$term,$count_stop,$loc_selected,'yes');
				  	}
			        ?>
				</select>
				<?php
			}

		}
	
	}
	add_action( 'restrict_manage_posts', 'exwf_admin_filter_order_delivery' , 10, 2);
}
add_action( 'pre_get_posts','exwf_admin_filter_delivery_qr',101 );
if (!function_exists('exwf_admin_filter_delivery_qr')) {
	function exwf_admin_filter_delivery_qr($query) {
		if ( isset($_GET['post_type']) && $_GET['post_type']=='shop_order' && is_admin()) {
			if( isset($_GET['date_delivery']) && $_GET['date_delivery']!='' ){
				$unix_tdl = strtotime($_GET['date_delivery']);
				$query->set('meta_key', 'exwfood_date_deli_unix');
				//$query->set('orderby', 'meta_value_num');
				$query->set('meta_value', $unix_tdl);
				$query->set('meta_compare', '=');
				//$query->set('order', 'ASC');
			}
			$meta_query_args = array();
			$method = isset($_GET['method']) ? $_GET['method'] : '';
			if( $method!='' ){
				$meta_query_args['relation'] = 'AND';
				if($method!='delivery'){
					$meta_query_args[]= array(
						'key' => 'exwfood_order_method',
						'value' => $method,
						'compare' => '=',
					);
				}else{
					$meta_query_args[] = array(
						'relation' => 'OR',
						array(
							'key' => 'exwfood_order_method',
							'value' => $method,
							'compare' => '=',
						),
						array(
							'key' => 'exwfood_order_method',
							'value' => '',
							'compare' => 'NOT EXISTS',
						)
					);
				}
			}
			$loc = isset($_GET['floc']) ? $_GET['floc'] : '';
			if( $loc!='' ){
				$meta_query_args['relation'] = 'AND';
				$meta_query_args[] = array(
					'key' => 'exwoofood_location',
					'value' => $loc,
					'compare' => '=',
				);
			}
			//echo '<pre>';print_r($meta_query_args);exit;
			if(!empty($meta_query_args)){
				$query->set('meta_query', $meta_query_args);
			}
		}
	}
}