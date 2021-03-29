<?php
class EXWoofood_Menu_by_date {
	public function __construct(){
        add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'cmb2_admin_init', array($this,'register_metabox') );
    }
	
	function register_post_type(){
		$labels = array(
			'name'               => esc_html__('Menu by date','woocommerce-food'),
			'singular_name'      => esc_html__('Shortcodes','woocommerce-food'),
			'add_new'            => esc_html__('Add New Menu','woocommerce-food'),
			'add_new_item'       => esc_html__('Add New Menu','woocommerce-food'),
			'edit_item'          => esc_html__('Edit Menu','woocommerce-food'),
			'new_item'           => esc_html__('New Menu','woocommerce-food'),
			'all_items'          => esc_html__('Menu by date','woocommerce-food'),
			'view_item'          => esc_html__('View Menu','woocommerce-food'),
			'search_items'       => esc_html__('Search Menu','woocommerce-food'),
			'not_found'          => esc_html__('No Menu found','woocommerce-food'),
			'not_found_in_trash' => esc_html__('No Menu found in Trash','woocommerce-food'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Menu by date','woocommerce-food')
		);
		$rewrite = false;
		$args = array(  
			'labels' => $labels,  
			'menu_position' => 8, 
			'supports' => array('title','custom-fields'),
			'public'             => false,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=product',
			'menu_icon' =>  'dashicons-editor-ul',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'rewrite' => $rewrite,
		);  
		register_post_type('exwf_menubydate',$args);  
	}
	
	function register_metabox() {
		/**
		 * Sample metabox to demonstrate each field type included
		 */
		$prefix = 'exwf_';
		$mnbd = new_cmb2_box( array(
			'id'            => $prefix.'menubydate',
			'title'         => esc_html__( 'Menu', 'woocommerce-food' ),
			'object_types'  => array( 'exwf_menubydate' ), // Post type
		) );
		$mnbd->add_field( array(
			'name'       => esc_html__( 'Date', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select date of this menu', 'woocommerce-food' ),
			'id'         => $prefix.'mndate',
			'type' => 'text_date_timestamp',
			'default'          => '',
			'date_format' => 'Y-m-d',
			'repeatable'     => false,
			'show_option_none' => true,
			
		) );
		$mnbd->add_field( array(
			'name'             => esc_html__( 'Add by special food', 'woocommerce-food' ),
			'desc'             => esc_html__( 'Select food (or category below) and add it into food menu', 'woocommerce-food' ),
			'id'               => 'menu_foods',
			'type'             => 'post_search_text',
			'show_option_none' => false,
			'default'          => '',
			'post_type'   => 'product',
			'select_type' => 'checkbox',
			'select_behavior' => 'add',
			'after_field'  => '',
		) );
		$mnbd->add_field( array(
			'name'           => esc_html__( 'Or Add food by category', 'woocommerce-food' ),
			'desc'           => esc_html__( 'Select category for this date ( If you add food by special food this option will be ignored )', 'woocommerce-food' ),
			'id'             => 'menu_food_cats',
			'taxonomy'       => 'product_cat', //Enter Taxonomy Slug
			'type'           => 'taxonomy_multicheck_inline',
			'select_all_button' => false,
			'remove_default' => 'true', // Removes the default metabox provided by WP core.
			'query_args' => array(
				// 'orderby' => 'slug',
				// 'hide_empty' => true,
			),
			'classes'		 => 'cmb-type-taxonomy-multicheck-inline',
		) );
		// Repeat
		$repeat_option = new_cmb2_box( array(
			'id'            => $prefix.'mnrepeat',
			'title'         => esc_html__( 'Repeat on', 'tv-schedule' ),
			'object_types'  => array( 'exwf_menubydate' ), // Post type
		) );
		$repeat_option->add_field( array(
			'name' => esc_html__( 'Monday', 'tv-schedule' ),
			'id'   => $prefix. 'mnrepeat_Mon',
			'type' => 'checkbox',
			'classes'		 => 'column-7',
		) );
		$repeat_option->add_field( array(
			'name' => esc_html__( 'Tuesday', 'tv-schedule' ),
			'id'   => $prefix. 'mnrepeat_Tue',
			'type' => 'checkbox',
			'classes'		 => 'column-7',
		) );
		$repeat_option->add_field( array(
			'name' => esc_html__( 'Wednesday', 'tv-schedule' ),
			'id'   => $prefix. 'mnrepeat_Wed',
			'type' => 'checkbox',
			'classes'		 => 'column-7',
		) );
		$repeat_option->add_field( array(
			'name' => esc_html__( 'Thursday', 'tv-schedule' ),
			'id'   => $prefix. 'mnrepeat_Thu',
			'type' => 'checkbox',
			'classes'		 => 'column-7',
		) );
		$repeat_option->add_field( array(
			'name' => esc_html__( 'Friday', 'tv-schedule' ),
			'id'   => $prefix. 'mnrepeat_Fri',
			'type' => 'checkbox',
			'classes'		 => 'column-7',
		) );
		$repeat_option->add_field( array(
			'name' => esc_html__( 'Saturday', 'tv-schedule' ),
			'id'   => $prefix. 'mnrepeat_Sat',
			'type' => 'checkbox',
			'classes'		 => 'column-7',
		) );
		$repeat_option->add_field( array(
			'name' => esc_html__( 'Sunday', 'tv-schedule' ),
			'id'   => $prefix. 'mnrepeat_Sun',
			'type' => 'checkbox',
			'classes'		 => 'column-7',
		) );
		
	}
}
$EXWoofood_Menu_by_date = new EXWoofood_Menu_by_date();

function exwf_select_date_html($sdate=false){
	$date_selected = WC()->session->get( '_menudate' );
	if($date_selected!='' || isset($sdate) && $sdate==true){
		if($date_selected!=''){
			global $wp;
			$_fmdate = apply_filters('exwf_tour_by_date_fm', get_option('date_format'));
			$date = date_i18n($_fmdate, strtotime($date_selected));
			$cr_url =  home_url( $wp->request );
			echo '<div class="exwf-menuof-date">
				<a class="mndate-sl" href="'.add_query_arg(array('menu-date' => ''), $cr_url).'">
					<span class="">'.esc_html__('Date: ','woocommerce-food').$date.'</span>
					<span class="mndate-close">&times;</span>
				</a>	
			</div>';
		}
		return;
	}
	global $exwf_menudate;
	if(!isset($exwf_menudate) || $exwf_menudate!='on'){
		$exwf_menudate = 'on';
	}else if($exwf_menudate =='on'){
		return;
	}
	?>
	<div class="exwf-menu-bydate ex-popup-location">
		<div class="ex-popup-content">
			<div class="ex-popup-info">
				<h1><?php esc_html_e('Please choose the date to view menu','woocommerce-food');?></h1>
				<div class="exwoofood-select-loc">
					<div>
					<?php echo exwf_date_selecter(); ?>
					</div>
				</div>
			</div>
		</div>
	
	</div>
	<?php
}
function exwf_date_selecter(){

	$date_before = exwoofood_get_option('exwoofood_ck_beforedate','exwoofood_advanced_options');
	$enb_date = exwoofood_get_option('exwoofood_ck_enadate','exwoofood_advanced_options');
	$dis_day = exwoofood_get_option('exwoofood_ck_disday','exwoofood_advanced_options');

	$cure_time =  strtotime("now");
	$gmt_offset = get_option('gmt_offset');

	if($date_before!='' && is_numeric($date_before)){
		$cure_time =  apply_filters( 'exwt_disable_book_day', strtotime("+$date_before day") );
	}else if($date_before!='' && is_numeric(str_replace("m","",$date_before))){
		$cure_time = apply_filters( 'exwt_disable_book_day', strtotime("+".str_replace("m","",$date_before)." minutes") );
	}
	if($gmt_offset!=''){
		$cure_time = $cure_time + ($gmt_offset*3600);
	}
	$date = strtotime(date('Y-m-d', $cure_time));
	$maxl = apply_filters('exwf_number_date_select',10);
	$deli_date = array();
	$html_ot = '';
	global $wp;
	$cr_url =  home_url( $wp->request );
	$_fmdate = apply_filters('exwf_tour_by_date_fm', get_option('date_format'));
	if(is_array($enb_date) && count($enb_date) > 0){
		$html_ot .= '<option selected="true" value="" disabled>'.esc_html__('-- Select --','woocommerce-food') .'</option>';
		foreach ($enb_date as $enb_date_it) {
			if($enb_date_it > $date){
				$date_fm = date_i18n($_fmdate, $enb_date_it);
				$deli_date[$enb_date_it] = $date_fm;
				$url = add_query_arg(array('menu-date' => date('Y-m-d',$enb_date_it)), $cr_url);
				$html_ot .= '<option value="'. esc_attr($url) .'">'. $date_fm .'</option>';
			}
		}
	}else{
		$html_ot .= '<option selected="true" value="" disabled>'.esc_html__('-- Select --','woocommerce-food') .'</option>';
		for ($i = 0 ; $i<= $maxl; $i ++ ) {
			$date_un = strtotime("+$i day", $date);
			$day_ofdate = date('N',$date_un);
			if((!empty($dis_day) && count($dis_day)==7)){ break;}
			if( (!empty($dis_date) && in_array($date_un, $dis_date )) || (!empty($dis_day) && in_array($day_ofdate, $dis_day ) ) ){
			  $maxl = $maxl +1;
			}else{
			  $date_fm = date_i18n($_fmdate, $date_un);
			  $deli_date[$date_un] = $date_fm;
			  $url = add_query_arg(array('menu-date' => date('Y-m-d',$date_un)), $cr_url);
			  $html_ot .= '<option value="'. esc_attr($url) .'">'. $date_fm .'</option>';
			}
		}
	}
	$html ='<select class="exwf-menu-date ex-loc-select" name="menu-date">'.$html_ot.'</select>';
	return $html;
}

// add user menu date
add_action( 'init', 'exwf_user_select_menudate',20 );
function exwf_user_select_menudate(){
	if(is_admin() || !isset(WC()->session) ){ return;}
	$date_slt = WC()->session->get( '_menudate' );
	$cure_time =  strtotime("now");
	$gmt_offset = get_option('gmt_offset');
	if($gmt_offset!=''){
		$cure_time = $cure_time + ($gmt_offset*3600);
	}
	$date = strtotime(date('Y-m-d', $cure_time));
	if($date_slt!=''){
		if($date > strtotime($date_slt) ){
			WC()->session->set( '_menudate' ,'' );
		}
	}else{
		global $woocommerce;
		$woocommerce->cart->empty_cart();
	}
	if(isset($_GET["menu-date"]) && $date < (strtotime($_GET["menu-date"]) + 86399 ) ){
		if($date_slt=='' || ($date_slt!='' && $date_slt!= $_GET["menu-date"] ) ){
			global $woocommerce;
			$woocommerce->cart->empty_cart();
		}
		WC()->session->set( '_menudate' , $_GET["menu-date"] );
	}else if(isset($_GET["menu-date"])){
		WC()->session->set( '_menudate' ,'' );
	}
}
// get menu by date seleted
function exwf_menuby_date_selected(){
	$date_slt = WC()->session->get( '_menudate' );
	if($date_slt!=''){
		return strtotime($date_slt);
	}
}
// query hook
if(!function_exists('exwf_query_by_menu_date')){
    function exwf_query_by_menu_date($args){
    	$date_slt = WC()->session->get( '_menudate' );
    	if($date_slt!=''){
    		$date_slt = strtotime($date_slt);
    		$wday = date('D', $date_slt);
	    	$args_mn = array(
				'post_type'     => 'exwf_menubydate',
				'post_status'   => array( 'publish' ),
				'numberposts'   => -1,
				'suppress_filters' => true
			);

			$args_mn['meta_query'][] = array(
				'relation' => 'OR',
		        array(
		            'key'     => 'exwf_mndate',
		            'value'   => $date_slt,
		            'compare' => '='
		        ),
		        array(
		            'key'     => 'exwf_mnrepeat_'.$wday,
		            'value'   => 'on',
		            'compare' => '='
		        )
			);
			$menu_f = get_posts( $args_mn );
			$food_ids = array();
			$cat_ids = array();
			if(!empty($menu_f) && count($menu_f) > 0){
				foreach ($menu_f as $f_item) {
					$ids = get_post_meta( $f_item->ID, 'menu_foods', true );
					if($ids!=''){
						$ids = explode(",",$ids);
						$food_ids = array_merge($food_ids,$ids);
					}
					$cats = get_the_terms( $f_item->ID, 'product_cat' );
					if(!empty($cats)){
						$terms_ids = wp_list_pluck($cats, 'term_id');
						$cat_ids = array_merge($cat_ids,$terms_ids);
					}
				}
			}
			if(is_array($food_ids) && !empty($food_ids) || !empty($cat_ids)){
				if(!empty($food_ids)){
					$args['post__in'] = $food_ids;
				}elseif(!empty($cat_ids)){
					$args['tax_query'] = array(
				        'relation' => 'AND',
				        array(
				            'taxonomy' => 'product_cat',
				            'field'    => 'term_id',
				            'terms'    => $cat_ids,
				            'operator' => 'IN',
				        ),
				    );
				}
			}else{
				$args['post__in'] = array('0');
			}
		}
        return $args;
     }
}
add_filter( 'exwoofood_query', 'exwf_query_by_menu_date' );
add_filter( 'exwf_ajax_query_args', 'exwf_query_by_menu_date' );
add_filter( 'exwf_ajax_filter_query_args', 'exwf_query_by_menu_date' );

add_action( 'pre_get_posts','exwf_query_pre_change',101 );
if(!function_exists('exwf_query_pre_change')){
    function exwf_query_pre_change($query){
    	if ( ! is_admin() && (in_array ( $query->get('post_type'), array('product') ) )) {
	    	$date_slt = WC()->session->get( '_menudate' );
	    	if($date_slt!=''){
	    		$date_slt = strtotime($date_slt);
	    		$wday = date('D', $date_slt);
		    	$args_mn = array(
					'post_type'     => 'exwf_menubydate',
					'post_status'   => array( 'publish' ),
					'numberposts'   => -1,
					'suppress_filters' => true
				);

				$args_mn['meta_query'][] = array(
					'relation' => 'OR',
			        array(
			            'key'     => 'exwf_mndate',
			            'value'   => $date_slt,
			            'compare' => '='
			        ),
			        array(
			            'key'     => 'exwf_mnrepeat_'.$wday,
			            'value'   => 'on',
			            'compare' => '='
			        )
				);
				$menu_f = get_posts( $args_mn );
				$food_ids = array();
				if(!empty($menu_f) && count($menu_f) > 0){
					foreach ($menu_f as $f_item) {
						$ids = get_post_meta( $f_item->ID, 'menu_foods', true );
						if($ids!=''){
							$ids = explode(",",$ids);
							$food_ids = array_merge($food_ids,$ids);
						}
					}
				}
				if(is_array($food_ids) && !empty($food_ids)){
					$query->set('post__in', $food_ids);
				}else{
					$query->set('post__in', array(0));
				}
			}
		}
     }
}