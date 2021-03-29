<?php
function exwoofood_shortcode_opcls_time( $atts ) {
	if(phpversion()>=7){
		$atts = (array)$atts;
	}
	$img_url   = isset($atts['img_url']) &&  $atts['img_url'] !=''? $atts['img_url'] : '';
	if(is_admin() || (defined('REST_REQUEST') && REST_REQUEST)){ return;}
	$loc_opcls = exwoofood_get_option('exwoofood_open_close_loc','exwoofood_advanced_options');
	ob_start();
	if (!exwf_check_open_close_time()) {?>
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('body').on('click','.exwf-opcls-info .ex_close',function(event) {
					jQuery(this).closest('.exwf-opcls-info').remove();
					sessionStorage.setItem("exwf_cls_ops", '1');
				});
				jQuery('body').on('click', '.exwf-opcls-info', function (event) {
					if (event.target.className == 'exwf-opcls-info ex-popup-active') {
						jQuery('.exwf-opcls-info').remove();
						sessionStorage.setItem("exwf_cls_ops", '1');
					}
				});
				var exwf_at_opcls = sessionStorage.getItem("exwf_cls_ops");
				<?php if($loc_opcls=='yes'){?>
				if(exwf_at_opcls !== '1' && !jQuery('.exwf-order-method .exwf-opcls-info.exwf-odtype').length){
				<?php }else{?>
				if(exwf_at_opcls !== '1'){	
				<?php }?>		
					jQuery('.exwf-opcls-info').addClass('ex-popup-active');
				}
			});
		</script>
		<style type="text/css">
			
		</style>
		<div class="exwf-opcls-info">
			<div class="exwf-opcls-content">
				<span class="ex_close">×</span>
				<?php if($img_url!=''){?>
					<div class="opcls-img"><img src="<?php echo esc_url($img_url); ?>"></div>
				<?php }?>
				<div class="opcls-ct"><?php echo exwfd_open_closing_message(true);?></div>	
			</div>
		</div>
		<?php
	}
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode( 'ex_wf_opcls', 'exwoofood_shortcode_opcls_time' );
add_action( 'after_setup_theme', 'ex_reg_wf_opcls_vc' );
function ex_reg_wf_opcls_vc(){
    if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("Opening and Closing info", "woocommerce-food"),
	   "base" => "ex_wf_opcls",
	   "class" => "",
	   "icon" => "icon-grid",
	   "controls" => "full",
	   "category" => esc_html__('Woocommerce Food','woocommerce-food'),
	   "params" => array(
	   		array(
			  	"admin_label" => true,
				"type" => "textfield",
				"heading" => esc_html__("Image url", "woocommerce-food"),
				"param_name" => "img_url",
				"value" => "",
				"description" => esc_html__("Set url of image", 'woocommerce-food'),
			),
	   )
	));
	}
}
