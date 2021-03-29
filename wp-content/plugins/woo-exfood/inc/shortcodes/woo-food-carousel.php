<?php
function exwoofood_shortcode_carousel( $atts ) {
	if(phpversion()>=7){
		$atts = (array)$atts;
	}
	if(is_admin() || (defined('REST_REQUEST') && REST_REQUEST)){ return;}
	global $ID,$number_excerpt,$img_size,$location;
	$ID = isset($atts['ID']) && $atts['ID'] !=''? $atts['ID'] : 'ex-'.rand(10,9999);
	if(!isset($atts['ID'])){
		$atts['ID']= $ID;
	}
	$style = isset($atts['style']) && $atts['style'] !=''? $atts['style'] : '1';
	$column =  '2';
	$posttype   = 'ex_food';
	$ids   = isset($atts['ids']) ? str_replace(' ', '', $atts['ids']) : '';
	$taxonomy  = isset($atts['taxonomy']) ? $atts['taxonomy'] : '';
	$cat   = isset($atts['cat']) ? $atts['cat'] : '';
	$tag  = isset($atts['tag']) ? $atts['tag'] : '';
	$count   = isset($atts['count']) &&  $atts['count'] !=''? $atts['count'] : '9';
	$order  = isset($atts['order']) ? $atts['order'] : '';
	$orderby  = isset($atts['orderby']) ? $atts['orderby'] : '';
	$meta_key  = isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value  = isset($atts['meta_value']) ? $atts['meta_value'] : '';
	$class  = isset($atts['class']) ? $atts['class'] : '';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$slidesshow = isset($atts['slidesshow'])&& $atts['slidesshow']!='' ? $atts['slidesshow'] : '3';
	$slidesscroll =  isset($atts['slidesscroll'])&& $atts['slidesscroll']!='' ? $atts['slidesscroll'] : '';
	$autoplay 		= isset($atts['autoplay']) && $atts['autoplay'] == 1 ? 1 : 0;
	$autoplayspeed 		= isset($atts['autoplayspeed']) && is_numeric($atts['autoplayspeed']) ? $atts['autoplayspeed'] : '';
	$start_on 		= isset($atts['start_on']) ? $atts['start_on'] : '';
	$loading_effect 		= isset($atts['loading_effect']) ? $atts['loading_effect'] : '';
	$infinite 		= isset($atts['infinite']) ? $atts['infinite'] : '';
	$cart_enable  = isset($atts['cart_enable']) ? $atts['cart_enable'] : '';
	$enable_modal = isset($atts['enable_modal']) ? $atts['enable_modal'] : '';
	$featured =  isset($atts['featured']) ? $atts['featured'] :'';
	$location =  isset($atts['location']) ? $atts['location'] :'';
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'';
	// remove space
	$cat = preg_replace('/\s+/', '', $cat);
	$ids = preg_replace('/\s+/', '', $ids);
	
	$args = exwoofood_query($posttype, $count, $order, $orderby, $cat, $tag, $taxonomy, $meta_key, $ids, $meta_value,'','','',$featured,$location);
	$the_query = new WP_Query( $args );
	ob_start();
	$class = $class." style-".$style;
	if($style == 1 || $style == 3 || $style == 13 || $style == 14 || $style == 15 || $style == 16){
		$class = $class." style-classic";
	}
	$class = $class." ex-food-plug ";
	if($loading_effect == 1){
		$class = $class.' ld-screen';
	}
	if (!exwf_check_open_close_time()) {
		//$class = $class." exfd-out-open-time";
	}
	if($enable_modal=='no'){
		$class = $class." exfdisable-modal";
	}
	if ($slidesscroll == '') {
		$slidesscroll = $slidesshow;
	}
	$html_modal ='';
	wp_enqueue_script( 'wc-add-to-cart-variation' );
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
		$GLOBALS['Product_Addon_Display']->addon_scripts();
	}
	wp_enqueue_style( 'ionicon' );
	wp_enqueue_style( 'wpex-ex_s_lick', EX_WOOFOOD_PATH .'js/ex_s_lick/ex_s_lick.css');
	wp_enqueue_style( 'wpex-ex_s_lick-theme', EX_WOOFOOD_PATH .'js/ex_s_lick/ex_s_lick-theme.css');
	wp_enqueue_script( 'wpex-ex_s_lick', EX_WOOFOOD_PATH.'js/ex_s_lick/ex_s_lick.js', array( 'jquery' ) );
	$exwoofood_enable_rtl = exwoofood_get_option('exwoofood_enable_rtl');
	if(is_rtl()){ $exwoofood_enable_rtl = 'yes';}
	//$locations ='';
	do_action( 'exwoofood_before_shortcode');
	?>
	<div <?php if($exwoofood_enable_rtl=='yes'){ echo 'dir="rtl"';} ?> class="ex-fdlist ex-fdcarousel <?php echo esc_attr($class);?>" id="<?php echo esc_attr($ID);?>" data-autoplay="<?php echo esc_attr($autoplay)?>" data-speed="<?php echo esc_attr($autoplayspeed)?>" data-rtl="<?php echo esc_attr($exwoofood_enable_rtl)?>" data-slidesshow="<?php echo esc_attr($slidesshow)?>" data-slidesscroll="<?php echo esc_attr($slidesscroll)?>"  data-start_on="<?php echo esc_attr($start_on)?>" data-infinite="<?php echo esc_attr($infinite);?>" data-mobile_item="<?php echo esc_attr(apply_filters( 'exwwf_mobile_nbitem', 1 ))?>">
		<?php 
		do_action('exwf_before_shortcode_content',$atts);
		if ( exwoofood_get_option('exwoofood_enable_loc') =='yes' ) {
			$loc_selected = WC()->session->get( 'ex_userloc' );
			if($location!='' && $loc_selected != $location){
				WC()->session->set( 'ex_userloc', $location);
			}
			echo "<input type='hidden' name='food_loc' value='".esc_attr($location)."'/>";
		}
		if($location!='OFF'){
			exwoofood_select_location_html($location);
		}?>
		<?php 
	    if($cart_enable !='no') {
	        global $excart_html;
        	if($excart_html != 'on' || $cart_enable =='yes'){
        		$excart_html = 'on';
	        	exwoofood_woo_cart_icon_html($cart_enable);
	        }
		}
        ?>
    	<?php 
    	echo '<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">';
    	if($loading_effect==1){?>
            <div class="exfd-loadcont"><div class="exfd-loadicon"></div></div>
        <?php } 
        if(function_exists('exwf_select_date_html')){exwf_select_date_html();}   ?>
		<div class="parent_grid">
        <div class="ctgrid">
		<?php
		if ($the_query->have_posts()){
			while ($the_query->have_posts()) { $the_query->the_post();
				echo '<div class="item-grid" data-id="ex_id-'.esc_attr($ID).'-'.get_the_ID().'" data-id_food="'.get_the_ID().'" id="ctc-'.esc_attr($ID).'-'.get_the_ID().'"> ';
					exwf_custom_color('grid',$style,'ctc-'.esc_attr($ID).'-'.get_the_ID());
					?>
					<div class="exp-arrow">
						<?php 
						exwoofood_template_plugin('grid-'.$style,1);
						?>
					<div class="exfd_clearfix"></div>
					</div>
					<?php
				echo '</div>';
			}
		} ?>
		</div>
		</div>
		<!-- Modal ajax -->
		<?php global $modal_html;
		if(!isset($modal_html) || $modal_html!='on' || $enable_modal=='yes'){
			$modal_html = 'on';
			echo "<div id='food_modal' class='ex_modal'></div>";
		}?>
	</div>
	<?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode( 'ex_wf_carousel', 'exwoofood_shortcode_carousel' );
add_action( 'after_setup_theme', 'ex_reg_wf_carousel_vc' );
function ex_reg_wf_carousel_vc(){
    if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("Food Carousel", "woocommerce-food"),
	   "base" => "ex_wf_carousel",
	   "class" => "",
	   "icon" => "icon-grid",
	   "controls" => "full",
	   "category" => esc_html__('Woocommerce Food','woocommerce-food'),
	   "params" => array(
		   array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Style", 'woocommerce-food'),
			 "param_name" => "style",
			 "value" => array(
				esc_html__('1', 'woocommerce-food') => '1',
				esc_html__('2', 'woocommerce-food') => '2',
				esc_html__('3', 'woocommerce-food') => '3',
				esc_html__('4', 'woocommerce-food') => '4',
			 ),
			 "description" => esc_html__('Select style of carousel', 'woocommerce-food')
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Count", "woocommerce-food"),
			"param_name" => "count",
			"value" => "",
			"description" => esc_html__("Enter number of foods to show", 'woocommerce-food'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Number item visible", "woocommerce-food"),
			"param_name" => "slidesshow",
			"value" => "",
			"description" => esc_html__("Number of slides to show at a time", 'woocommerce-food'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Number slides to scroll", "woocommerce-food"),
			"param_name" => "slidesscroll",
			"value" => "",
			"description" => esc_html__("Number of slides to scroll at a time", 'woocommerce-food'),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("IDs", "woocommerce-food"),
			"param_name" => "ids",
			"value" => "",
			"description" => esc_html__("Specify food IDs to retrieve", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Menu", "woocommerce-food"),
			"param_name" => "cat",
			"value" => "",
			"description" => esc_html__("List of cat ID (or slug), separated by a comma", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Order", 'woocommerce-food'),
			 "param_name" => "order",
			 "value" => array(
			 	esc_html__('DESC', 'woocommerce-food') => 'DESC',
				esc_html__('ASC', 'woocommerce-food') => 'ASC',
			 ),
			 "description" => ''
		  ),
		  array(
		  	 "admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Order by", 'woocommerce-food'),
			 "param_name" => "orderby",
			 "value" => array(
			 	esc_html__('Date', 'woocommerce-food') => 'date',
			 	esc_html__('Custom order field', 'woocommerce-food') => 'order_field',
				esc_html__('ID', 'woocommerce-food') => 'ID',
				esc_html__('Author', 'woocommerce-food') => 'author',
			 	esc_html__('Title', 'woocommerce-food') => 'title',
				esc_html__('Name', 'woocommerce-food') => 'name',
				esc_html__('Modified', 'woocommerce-food') => 'modified',
			 	esc_html__('Parent', 'woocommerce-food') => 'parent',
				esc_html__('Random', 'woocommerce-food') => 'rand',
				esc_html__('Menu order', 'woocommerce-food') => 'menu_order',
				esc_html__('Meta value', 'woocommerce-food') => 'meta_value',
				esc_html__('Meta value num', 'woocommerce-food') => 'meta_value_num',
				esc_html__('Post__in', 'woocommerce-food') => 'post__in',
				esc_html__('None', 'woocommerce-food') => 'none',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Meta key", "woocommerce-food"),
			"param_name" => "meta_key",
			"value" => "",
			"description" => esc_html__("Enter meta key to query", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Meta value", "woocommerce-food"),
			"param_name" => "meta_value",
			"value" => "",
			"description" => esc_html__("Enter meta value to query", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Number of Excerpt ( short description)", "woocommerce-food"),
			"param_name" => "number_excerpt",
			"value" => "",
			"description" => esc_html__("Enter number of Excerpt, enter:0 to disable excerpt", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Autoplay", 'woocommerce-food'),
			 "param_name" => "autoplay",
			 "value" => array(
			 	esc_html__('No', 'woocommerce-food') => '',
				esc_html__('Yes', 'woocommerce-food') => '1',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "textfield",
			 "class" => "",
			 "heading" => esc_html__("Autoplay Speed", "woocommerce-food"),
			 "param_name" => "autoplayspeed",
			 "value" => "",
			 "dependency" 	=> array(
				'element' => 'autoplay',
				'value'   => array('1'),
			 ),
			 "description" => esc_html__("Autoplay Speed in milliseconds. Default:3000", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Enable Loading effect", "woocommerce-food"),
			 "param_name" => "loading_effect",
			 "value" => array(
			 	esc_html__('No', 'woocommerce-food') => '',
			 	esc_html__('Yes', 'woocommerce-food') => '1',
			 ),
			 "description" => ""
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Infinite", "woocommerce-food"),
			 "param_name" => "infinite",
			 "value" => array(
			 	esc_html__('No', 'woocommerce-food') => '',
			 	esc_html__('Yes', 'woocommerce-food') => 'yes',
			 ),
			 "description" => esc_html__("Infinite loop sliding ( go to first item when end loop)", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Enable cart", 'woocommerce-food'),
			 "param_name" => "cart_enable",
			 "value" => array(
			 	esc_html__('Default', 'woocommerce-food') => '',
			 	esc_html__('Yes', 'woocommerce-food') => 'yes',
			 	esc_html__('No', 'woocommerce-food') => 'no',
			 ),
			 "description" => esc_html__("Enable side cart icon", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Show only Featured food", 'woocommerce-food'),
			 "param_name" => "featured",
			 "value" => array(
			 	esc_html__('No', 'woocommerce-food') => '',
				esc_html__('Yes', 'woocommerce-food') => '1',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Enable modal", 'woocommerce-food'),
			 "param_name" => "enable_modal",
			 "value" => array(
			 	esc_html__('Default', 'woocommerce-food') => '',
				esc_html__('Yes', 'woocommerce-food') => 'yes',
				esc_html__('No', 'woocommerce-food') => 'no',
			 ),
			 "description" => ''
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Class name", "woocommerce-food"),
			"param_name" => "class",
			"value" => "",
			"description" => esc_html__("add a class name and refer to it in custom CSS", "woocommerce-food"),
		  ),
	   )
	));
	}
}