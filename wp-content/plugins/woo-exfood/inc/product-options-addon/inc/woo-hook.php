<?php
/**
 * Get options
 */
function exwoo_get_options($id){
	if($id ==''){ $id = get_the_ID();}
	$global_op = array();
	//Check global option
	$exclude_options = get_post_meta( $id, 'exwo_exclude_options', true );
	if($exclude_options!='on'){
		$cate = wp_get_post_terms($id,'product_cat',array( 'fields' => 'slugs' ));
		$args = array(
			'post_type'     => 'exwo_glboptions',
			'post_status'   => array( 'publish' ),
			'numberposts'   => -1,
			'suppress_filters' => true
		);
		$args['meta_query'] = array(
			array(
	            'key' => 'exwo_product_ids',
	            'value' => $id,
	            'compare' => 'LIKE'
	        )
		);
		$args = apply_filters('exwo_option_by_cr_ids',$args);
		$glb_oids = array();
		if(isset($args['meta_query'])){
			$glb_oids = get_posts( $args );
			$glb_oids = wp_list_pluck( $glb_oids, 'ID' );
			unset($args['meta_query']);
		}
		if(!empty($cate) && count($cate) > 0){
			$args['tax_query'] = array(
					array(
					'taxonomy'         => 'product_cat',
					'field'            => 'slug',
					'terms'            => $cate,
					'operator' => 'IN',
					'include_children'=>false,
				)
			);
		}
		//print_r($args);exit;
		$glb_otqr = get_posts( $args );
		$glb_otqr = wp_list_pluck( $glb_otqr, 'ID' );
		$glb_otqr = array_merge($glb_otqr,$glb_oids);
		if(!empty($glb_otqr) && count($glb_otqr) > 0){
			foreach ($glb_otqr as $op_item) {
				$goptions = get_post_meta( $op_item, 'exwo_options', true );
				$global_op = array_merge($global_op,$goptions);
			}
			wp_reset_postdata();
		}
	}
	// include option
	$include_options = get_post_meta( $id, 'exwo_include_options', true );
	if($include_options!=''){
		$include_options = explode(",",$include_options);
		foreach ($include_options as $in_item) {
			$goptions = get_post_meta( $in_item, 'exwo_options', true );
			$global_op = array_merge($global_op,$goptions);
		}
	}
	if(is_array($global_op)){$global_op = array_unique($global_op,SORT_REGULAR);}
	//$glb_options = 
	$data_options = get_post_meta( $id, 'exwo_options', true );
	if(!empty($global_op)){
		if($data_options==''){$data_options=array();}
		$pos_glbop = apply_filters('expoa_pos_global','');
		if($pos_glbop=='before'){
			$data_options = array_merge($global_op,$data_options);
		}else{
			$data_options = array_merge($data_options,$global_op);
		}
	}
	return $data_options;
}
/**
 * Add the field to add to cart form
 */
function exwoo_display_custom_field() {
	global $post;
	$text_domain = exwo_text_domain();
	// Check for the custom field value
	$data_options = exwoo_get_options($post->ID);//echo '<pre>'; print_r($data_options);exit;
	if(is_array($data_options) && !empty($data_options)){
		$i = 0;
		$show_more = apply_filters( 'exwo_show_more_option_button', 0 );
		$cls = $show_more=='1' ? 'exwo-hide-options' : '';
		$accordion_style = apply_filters( 'exwo_accordion_style', 0 );
		$cls = $accordion_style=='1' ? 'exwo-accordion-style' : '';
		echo '<div class="exwo-product-options '.esc_attr($cls).'">';
		$j=0;
		$logic_js = '';
		foreach ($data_options as $item) {
			$j++;
			$el_id = isset($item['_id']) && $item['_id']!='' ? $item['_id'] : 'exwo-id'.rand(10000,10000000000);
			$el_id = $el_id.'-'.$j;
			$type = isset($item['_type']) && $item['_type']!='' ? $item['_type'] : 'checkbox';
			$required = isset($item['_required']) && $item['_required']!='' ? 'ex-required' : '';
			$min_req = $max_req = $required_m = '';
			if($type=='checkbox'){
				$min_req = isset($item['_min_op']) && $item['_min_op']!='' ? $item['_min_op'] : '';
				if(is_numeric($min_req) && $min_req > 0){
					$required_m =' ex-required-min';
				}
				$max_req = isset($item['_max_op']) && $item['_max_op']!='' ? $item['_max_op'] : '';
				if(is_numeric($max_req) && $max_req > 0){
					$required_m .=' ex-required-max';
				}
			}
			$enb_logic = isset($item['_enb_logic']) ? $item['_enb_logic'] : '';
			$plus_sign = apply_filters('exwo_plus_sign_char','+');
			if($enb_logic=='on'){
				$con_logic = isset($item['_con_logic']) ? $item['_con_logic'] : '';
				$logic_rule = isset($item['_con_tlogic']) && $item['_con_tlogic']=='hide' ? 'fadeOut()' : 'fadeIn()';
				if(is_array($con_logic) && !empty($con_logic)){
					$log_option = '';
					$lg = 0;
					foreach ($con_logic as $key => $item_logic) {
						$lg ++;
						$cttype_rel = isset($item_logic['type_rel']) && $item_logic['type_rel']=='and' ? '&&' : '||';
						$ctype_con = isset($item_logic['type_con']) && $item_logic['type_con']=='is_not' ? '!=' : '==';
						$ctype_op = isset($item_logic['type_op']) && $item_logic['type_op']!='' ? $el_id : '$ex_variation';
						$con_val = isset($item_logic['val']) ? $item_logic['val'] : '';
						if($cttype_rel!='' && $ctype_con!='' && $ctype_op!=''){
							if($ctype_op=='$ex_variation'){
								if(count($con_logic) > 1){
									if($lg==1){
										$log_option .= '$ex_variation '.$ctype_con.' "'.$con_val.'" ';
										//$log_option .= ' if($ex_variation '.$ctype_con.' "'.$con_val.'"){ jQuery("#'.$el_id.'").'.$ctype_rule.';}';
									}else{
										$log_option .= $cttype_rel.' $ex_variation '.$ctype_con.' "'.$con_val.'" ';
										//$log_option .= ' else if($ex_variation '.$ctype_con.' "'.$con_val.'"){ jQuery("#'.$el_id.'").'.$ctype_rule.';}';
									}

									if($lg== count($con_logic)){
										$log_option = 'if('.$log_option.'){ jQuery("#'.$el_id.'")'.($logic_rule == 'fadeOut()' ? '.addClass("exwf-offrq").css("display","none")' : '.removeClass("exwf-offrq").css("display","block")').';}
										else{ jQuery("#'.$el_id.'")'.($logic_rule == 'fadeOut()' ? '.removeClass("exwf-offrq").css("display","block")' : '.addClass("exwf-offrq").css("display","none")').';}';
									}
								}else{
									$log_option = ' if($ex_variation '.$ctype_con.' "'.$con_val.'"){ jQuery("#'.$el_id.'")'.($logic_rule == 'fadeOut()' ? '.addClass("exwf-offrq").css("display","none")' : '.removeClass("exwf-offrq").css("display","block")').';}
										else{ jQuery("#'.$el_id.'")'.($logic_rule == 'fadeOut()' ? '.removeClass("exwf-offrq").css("display","block")' : '.addClass("exwf-offrq").css("display","none")').';}';
								}
							}
						}
					}
					$logic_js .= $log_option;
				}
			}
			echo '<div class="exrow-group ex-'.esc_attr($type).' '.esc_attr($required).' '.esc_attr($required_m).' ex-logic-'.esc_attr($enb_logic).'" data-minsl="'.$min_req.'"  data-maxsl="'.$max_req.'" id="'.$el_id.'">';
				if(isset($item['_name']) && $item['_name']){
					$price_tt = '';
					if($type =='text' || $type =='textarea' || $type =='quantity'){
						$price_tt = isset($item['_price']) && $item['_price']!='' ? wc_price(exwo_convert_number_decimal_comma($item['_price'])) :'';
						$price_tt = $price_tt !='' ? '<span> '.$plus_sign.' '.wp_strip_all_tags($price_tt).'</span>' : '';
					}
					echo  '<span class="exfood-label"><span class="exwo-otitle">'.$item['_name'].'</span> '.$price_tt.'</span>' ;
				}
				echo '<div class="exwo-container">';
					$options = isset($item['_options']) ? $item['_options'] : '';
					if($type =='radio' && !empty($options)){
						foreach ($options as $key => $value) {
							$op_name = isset($value['name'])? $value['name'] : '';
							$dis_ck = isset($value['dis'])? $value['dis'] : '';
							$def_ck = isset($value['def']) && $dis_ck!='yes'? $value['def'] : '';
							$op_val = isset($value['price'])? exwo_convert_number_decimal_comma($value['price']) : '';
							$op_typ = isset($value['type'])? $value['type'] : '';
							$op_name = $op_val !='' ? $op_name .' '.$plus_sign.' '.wc_price($op_val) : $op_name;
							echo '<span><label><input class="ex-options" type="radio" name="ex_options_'.esc_attr($i).'[]" value="'.esc_attr($key).'" data-price="'.esc_attr($op_val).'" data-type="'.esc_attr($op_typ).'" '.checked($def_ck,'yes',false).' '.disabled($dis_ck,'yes',false).'>'.wp_kses_post($op_name).'</label></span>';
						}
					}else if($type =='select' && !empty($options)){
						echo '<select class="ex-options" name="ex_options_'.esc_attr($i).'[]">';
						echo '<option value="" data-price="">'.esc_html__( 'Select', $text_domain ).'</option>';
						foreach ($options as $key => $value) {
							$op_name = isset($value['name'])? $value['name'] : '';
							$dis_ck = isset($value['dis'])? $value['dis'] : '';
							$def_ck = isset($value['def']) && $dis_ck!='yes'? $value['def'] : '';
							$op_val = isset($value['price'])? exwo_convert_number_decimal_comma($value['price']) : '';
							$op_typ = isset($value['type'])? $value['type'] : '';
							$op_name = $op_val !='' ? $op_name .' '.$plus_sign.' '.wc_price($op_val) : $op_name;
							echo '<option value="'.esc_attr($key).'" data-price="'.esc_attr($op_val).'" data-type="'.esc_attr($op_typ).'" '. selected( $def_ck, 'yes',false ) .' '.disabled($dis_ck,'yes',false).'>'.wp_kses_post($op_name).'</option>';
						}
						echo '<select>';
					}else if($type =='text'){
						$price_ta = isset($item['_price']) && $item['_price']!='' ? exwo_convert_number_decimal_comma($item['_price']) :'';
						$price_typ = isset($item['_price_type']) && $item['_price_type']!='' ? $item['_price_type'] :'';
						echo '<input class="ex-options" type="text" name="ex_options_'.esc_attr($i).'" data-price="'.esc_attr($price_ta).'" data-type="'.esc_attr($price_typ).'"/>';
					}else if($type =='quantity'){
						$price_ta = isset($item['_price']) && $item['_price']!='' ? exwo_convert_number_decimal_comma($item['_price']) :'';
						$price_typ = isset($item['_price_type']) && $item['_price_type']!='' ? $item['_price_type'] :'';
						echo '<input class="ex-options" type="number" min="0" name="ex_options_'.esc_attr($i).'" data-price="'.esc_attr($price_ta).'" data-type="'.esc_attr($price_typ).'" placeholder="0" />';
					}else if($type =='textarea'){
						$price_ta = isset($item['_price']) && $item['_price']!='' ? exwo_convert_number_decimal_comma($item['_price']) :'';
						$price_typ = isset($item['_price_type']) && $item['_price_type']!='' ? $item['_price_type'] :'';
						echo '<textarea class="ex-options" name="ex_options_'.esc_attr($i).'" data-price="'.esc_attr($price_ta).'" data-type="'.esc_attr($price_typ).'"/></textarea>';
					}else if(!empty($options)){
						foreach ($options as $key => $value) {
							$op_name = isset($value['name'])? $value['name'] : '';
							$dis_ck = isset($value['dis'])? $value['dis'] : '';
							$def_ck = isset($value['def']) && $dis_ck!='yes'? $value['def'] : '';
							$op_val = isset($value['price'])? exwo_convert_number_decimal_comma($value['price']) : '';
							$op_typ = isset($value['type'])? $value['type'] : '';
							$op_name = $op_val !='' ? $op_name .' '.$plus_sign.' '.wc_price($op_val) : $op_name;
							echo '<span><label><input class="ex-options" type="checkbox" name="ex_options_'.esc_attr($i).'[]" value="'.esc_attr($key).'" data-price="'.esc_attr($op_val).'" data-type="'.esc_attr($op_typ).'" '.checked($def_ck,'yes',false).' '.disabled($dis_ck,'yes',false).'>'.wp_kses_post($op_name).'</label></span>';
						}
					}
					if($required!=''){
						echo '<p class="ex-required-message">'.esc_html__('This option is required', $text_domain ).'</p>';
					}
					if($type=='checkbox' && is_numeric($min_req) && $min_req > 0){
						echo '<p class="ex-required-min-message">'.sprintf( esc_html__('Please choose at least %s options.','woocommerce-food' ) , $min_req).'</p>';
					}
					if($type=='checkbox' && is_numeric($max_req) && $max_req > 0){
						echo '<p class="ex-required-max-message">'.sprintf( esc_html__('You only can select max %s options.','woocommerce-food' ) , $max_req).'</p>';
					}
				echo '</div>
			</div>';
			$i ++;
		}
		if($logic_js !=''){
			echo '<script type="text/javascript">
				jQuery(document).ready(function() {
					var $ex_variation = jQuery("input.variation_id").val();
					if($ex_variation!="" && $ex_variation!=0){
						'.$logic_js.'
					}
				});
				jQuery( document ).on( "found_variation.first", function ( e, variation ) {
				});
				jQuery( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
					setTimeout(function(){ 
						var $ex_variation = jQuery("input.variation_id").val();
						if($ex_variation=="" ){
							jQuery(".ex-logic-on").fadeOut();
						}
					}, 100);
				});	
				jQuery( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
					var $ex_variation = variation.variation_id;
					'.$logic_js.'
				});	


			</script>';
		}
		do_action( 'exwo_after_product_options');
		echo '</div>';
		if($show_more =='1'){
			echo '<div class="exwo-showmore"><span>'.esc_html__( 'Show extra options', $text_domain ).'<span></div>';
		}

	}
}
add_action( 'woocommerce_before_add_to_cart_button', 'exwoo_display_custom_field' );
/**
 * Validate the text field
 */
function exwo_validate_custom_field( $passed, $product_id, $quantity, $variation_id=false ) {
	$vari_pro = false;
	if(is_numeric($variation_id) && $variation_id > 0){
		$variation = wc_get_product($variation_id);
		$product_id = $variation->get_parent_id();
		$vari_pro = true;
	} else if(get_post_type($product_id) == 'product_variation') {
		$variation = wc_get_product($product_id);
		$variation_id = $product_id = $variation->get_parent_id();
		$vari_pro = true;
	}
	$data_options = exwoo_get_options($product_id);
	$text_domain = exwo_text_domain();
	$msg = '';
	if(is_array($data_options) && !empty($data_options)){
		foreach ( $data_options as $key=> $options ) {
			$rq = isset($options['_required']) ? $options['_required'] : ''; 
			$data_exts = isset($_POST['ex_options_'.$key]) ? $_POST['ex_options_'.$key] :'';
			$type = isset($options['_type']) && $options['_type']!='' ? $options['_type'] : 'checkbox';
			if( ($type=='checkbox' || $type=='select' || $type=='radio' ) && !empty($data_exts)){
				foreach ($data_exts as $k => $opc) {
					if( isset($options['_options'][$opc]['dis']) && ($options['_options'][$opc]['dis']=='yes')){
						unset($data_exts[$k]);
					}
				}
				$data_exts = array_values($data_exts);
			}
			$min_req = $type=='checkbox' && isset($options['_min_op']) && $options['_min_op']!='' ? $options['_min_op'] : 0;
			$max_req = $type=='checkbox' && isset($options['_max_op']) && $options['_max_op']!='' ? $options['_max_op'] : 0;

			$enb_logic = isset($options['_enb_logic']) ? $options['_enb_logic'] : '';
			if($enb_logic == 'on' && $vari_pro == true){
				$tlogic = isset($options['_con_tlogic']) ? $options['_con_tlogic'] : '';
				$c_logic = isset($options['_con_logic']) ? $options['_con_logic'] : '';
				if(is_array($c_logic) && !empty($c_logic)){
					$c_or = $c_and = array();
					$vali_con = false;
					foreach ($c_logic as $key_lg => $c_lg_val) {
						$c_val = isset($c_lg_val['val']) ? $c_lg_val['val'] : '';
						$c_type_con = isset($c_lg_val['type_con']) ? $c_lg_val['type_con'] : '';
						if($c_type_con=='is_not'){
							if($tlogic=='hide' && $c_val != $variation_id){
								$rq ='no'; $min_req = $max_req = 0;
								unset($_POST['ex_options_'.$key]);
							}else if($tlogic=='' && $c_val == $variation_id){
								$rq ='no'; $min_req = $max_req = 0;
								unset($_POST['ex_options_'.$key]);
							}
						}else{
							if($tlogic=='hide' && $c_val == $variation_id){
								$rq ='no'; $min_req = $max_req = 0;
								unset($_POST['ex_options_'.$key]);
							}else if($tlogic=='' && $c_val != $variation_id){
								$rq ='no'; $min_req = $max_req = 0;
								unset($_POST['ex_options_'.$key]);
							}
						}
						/*
						if(isset($c_lg_val['type_rel']) && $c_lg_val['type_rel'] == 'or'){
							$c_or[] = isset($c_lg_val['val']) ? $c_lg_val['val'] : '';
						}else{
							$c_and[] = isset($c_lg_val['val']) ? $c_lg_val['val'] : '';
						}*/
					}
				}
			}
			if(is_array($data_exts) && count($data_exts) ==1 && $data_exts[0]==''){
				$data_exts = '';
			}
			$c_item = !empty($data_exts) && is_array($data_exts) ? count($data_exts) : 0;
			if( ($rq =='yes' && ($data_exts=='' || empty($data_exts))) || ( $min_req > 0 &&  $min_req > $c_item) || ( $max_req > 0 &&  $max_req < $c_item) ){
				$passed = false;
				wc_add_notice( __( 'Please re-check all required fields and try again', $text_domain ), 'error' );
				break;
			}
			//print_r($options);
		}//return false;
	}
	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'exwo_validate_custom_field', 10, 4 );


/**
 * Add the text field as item data to the cart object
 */
function exwo_add_custom_field_item_data( $cart_item_data, $product_id ) {

	$data_options = exwoo_get_options($product_id);
	$c_options = array();
	//$price = '';
	if(is_array($data_options) && !empty($data_options)){
		foreach ( $data_options as $key=> $options ) {
			$data_exts = isset($_POST['ex_options_'.$key]) ? $_POST['ex_options_'.$key] :'';
			if(isset($options['_type']) &&($options['_type']=='text' || $options['_type']=='textarea' || $options['_type']=='quantity')){
				$price_op = isset($options['_price']) ? $options['_price'] : '';
				if($data_exts!=''){
					$type_price = isset($options['_price_type']) ? $options['_price_type'] : '';
					if($options['_type']=='quantity'){
						$price_op = floatval($price_op)*$data_exts;
					}
					$c_options[] = array(
						'name'       => sanitize_text_field( $options['_name'] ),
						'value'      => $data_exts,
						'type_of_price'      => $type_price,
						'price'      => floatval($price_op),
						'_type'      => $options['_type'],
					);
					//$price += (float) floatval($price_op);
				}
			}else{
				if(is_array($data_exts) && !empty($data_exts)){
					foreach ($data_exts as $value) {
						if($value!=''){
							$price_op = isset($options['_options'][$value]['price']) ? exwo_convert_number_decimal_comma($options['_options'][$value]['price']) : '';
							$type_price = isset($options['_options'][$value]['type']) ? $options['_options'][$value]['type'] : '';
							$c_options[] = array(
								'name'       => sanitize_text_field( $options['_name'] ),
								'value'      => $options['_options'][$value]['name'],
								'type_of_price'      => $type_price,
								'price'      => floatval($price_op),
								'_type'      => isset($options['_type']) ? $options['_type'] : '',
							);
							//$price += (float) floatval($price_op);
						}
					}
				}
			}
		}
		$cart_item_data['exoptions'] = $c_options;
	}
	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'exwo_add_custom_field_item_data', 10, 2 );
function exwo_add_custom_field_item_data_again($cart_item_data, $product, $order){
	remove_filter( 'woocommerce_add_to_cart_validation', 'exwo_validate_custom_field', 10);
	if(isset($product['item_meta']['_exoptions']) && is_array($product['item_meta']['_exoptions'])){
		$cart_item_data['exoptions'] =  $product['item_meta']['_exoptions'];
		//echo '<pre>';print_r($cart_item_data);exit;
	}
	return $cart_item_data;
}
add_filter( 'woocommerce_order_again_cart_item_data', 'exwo_add_custom_field_item_data_again', 11, 3 );
/**
 * Update price
 */
add_filter( 'woocommerce_add_cart_item',  'exwf_update_total_price_item', 30, 1 );
function exwf_update_total_price_item($cart_item){
	if(isset($cart_item['exoptions']) && is_array($cart_item['exoptions'])){
		$price = (float) $cart_item['data']->get_price( 'edit' );
		$qty = $cart_item['quantity'];
		if(isset($_POST['action']) && isset($_POST['key']) && $_POST['action'] == 'exwf_update_quantity' && $_POST['key'] == $cart_item['key']){
			$qty = $_POST['quantity'];
		}
		foreach ( $cart_item['exoptions'] as $option ) {
			if ( $option['price'] ) {
				if($option['type_of_price'] == 'fixed'){
					$price += (float) $option['price']/$qty;
				}else{
					$price += (float) $option['price'];
				}
			}
		}
		$cart_item['data']->set_price( $price );
	}
	return $cart_item;
}
add_filter( 'woocommerce_get_cart_item_from_session', 'exwf_update_total_from_session', 20, 2 );
function exwf_update_total_from_session($cart_item, $values){
	if(isset($cart_item['exoptions']) && is_array($cart_item['exoptions'])){
		$cart_item = exwf_update_total_price_item($cart_item);
	}
	return $cart_item;
}
/**
 * Display in cart
 */
add_filter('woocommerce_get_item_data','exwf_show_option_in_cart',11,2);
function exwf_show_option_in_cart( $other_data, $cart_item ) {
	if(isset($cart_item['exoptions']) && is_array($cart_item['exoptions'])){
		$show_sgline = apply_filters( 'exwf_show_options_single_line', 'no' );
		if($show_sgline!='yes'){
			foreach ( $cart_item['exoptions'] as $option ) {
				$char_j = ' + ';
				if(isset ($option['_type']) && $option['_type']=='quantity'){ $char_j = ' x ';}
				$char_j = apply_filters('exwo_plus_sign_char',$char_j);
				if(isset ($option['_type']) && $option['_type']=='quantity'){
					$price_s = isset($option['price']) && $option['price']!='' ? $option['value'] .$char_j.wc_price($option['price']/$option['value']) : $option['value'];
				}else{
					$price_s = isset($option['price']) && $option['price']!='' ? $option['value'] .$char_j.wc_price($option['price']) : $option['value'];
				}
				$price_s = apply_filters( 'exwo_price_show_inorder', $price_s, $option );
				$other_data[] = array(
					'name'  => $option['name'],
					'value' => $price_s
				);
			}
		}else{
			$grouped_types = array();
			foreach($cart_item['exoptions'] as $type){
			    $grouped_types[$type['name']][] = $type;
			}
			foreach ($grouped_types as $key => $option_tp) {
				if (is_array($option_tp)){
					$price_a = '';
					$i = 0;
					foreach ($option_tp as $option_it) {
						$i ++;
						$name = $option_it['name'];
						$char_j = ' + ';
						if(isset ($option_it['_type']) && $option_it['_type']=='quantity'){ $char_j = ' x ';}
						$char_j = apply_filters('exwo_plus_sign_char',$char_j);
						$price_s = isset($option_it['price']) && $option_it['price']!='' ? $option_it['value'] .$char_j.wc_price($option_it['price']) : $option_it['value'];
						$price_s = apply_filters( 'exwo_price_show_inorder', $price_s, $option_it );
						$price_a .= $price_s;
						if($i > 0 && $i < count($option_tp)){$price_a .=', '; }
					}
					$other_data[] = array(
						'name'  => $option_it['name'],
						'value' => $price_a
					);
				}
			}
		}
	}
	return $other_data;
}
/**
 * Add option to order object
 */
function exwf_add_options_to_order( $item, $cart_item_key, $values, $order ) {
	if(isset($values['exoptions']) && is_array($values['exoptions'])){
		$show_sgline = apply_filters( 'exwf_show_options_single_line', 'no' );
		if($show_sgline!='yes'){
			foreach ( $values['exoptions'] as $option ) {
				$char_j = '+';
				if(isset ($option['_type']) &&  $option['_type']=='quantity'){ $char_j = 'x';}
				$char_j = apply_filters('exwo_plus_sign_char',$char_j);
				//$value = isset($option['price']) && $option['price']!='' ? strip_tags($option['value'] .$char_j.wc_price($option['price'])) : $option['value'];
				$name = isset($option['price']) && $option['price']!='' ? strip_tags($option['name'] .' ('.$char_j.wc_price($option['price']).')') : $option['name'];
				$name = apply_filters( 'exwo_name_show_inorder', $name, $option );
				$item->add_meta_data( $name,$option['value']);
			}
		}else{
			$grouped_types = array();
			foreach($values['exoptions'] as $type){
			    $grouped_types[$type['name']][] = $type;
			}
			foreach ($grouped_types as $key => $option_tp) {
				if (is_array($option_tp)){
					$price_a = '';
					$i = 0;
					foreach ($option_tp as $option_it) {
						$i ++;
						$name = $option_it['name'];
						$char_j = ' + ';
						if(isset ($option_it['_type']) && $option_it['_type']=='quantity'){ $char_j = ' x ';}
						$char_j = apply_filters('exwo_plus_sign_char',$char_j);
						$price_s = isset($option_it['price']) && $option_it['price']!='' ? $option_it['value'] .$char_j.wc_price($option_it['price']) : $option_it['value'];
						$price_s = apply_filters( 'exwo_price_show_inorder', $price_s, $option_it );
						$price_a .= $price_s;
						if($i > 0 && $i < count($option_tp)){$price_a .=', '; }
					}
					$item->add_meta_data( $option_it['name'], $price_a );
				}
			}
		}
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'exwf_add_options_to_order', 10, 4 );

add_action('woocommerce_new_order_item','exwf_add_options_order_item_meta',10,2);

function exwf_add_options_order_item_meta($item_id, $item){
	if ( is_object( $item ) && isset($item->legacy_values) ) {
		$values = $item->legacy_values;
		if(isset($values['exoptions']) && !empty($values['exoptions'])){
			wc_add_order_item_meta($item_id,'_exoptions',$values['exoptions']);
		}
	}
}