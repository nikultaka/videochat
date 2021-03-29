<?php
//Option addon
$disable_exoptions = exwoofood_get_option('exwoofood_disable_exoptions','exwoofood_options');
if($disable_exoptions!='yes'){
	include plugin_dir_path(__FILE__).'product-options-addon/product-options-addon.php';
}
//shortcode
include plugin_dir_path(__FILE__).'shortcodes/woo-food-list.php';
include plugin_dir_path(__FILE__).'shortcodes/woo-food-grid.php';
include plugin_dir_path(__FILE__).'shortcodes/woo-food-table.php';
include plugin_dir_path(__FILE__).'shortcodes/woo-food-carousel.php';
include plugin_dir_path(__FILE__).'shortcodes/woo-food-opcls-time.php';
//widget
include plugin_dir_path(__FILE__).'widgets/woo-food.php';
// woo hook
include plugin_dir_path(__FILE__).'woo-hook.php';
// Menu by date
$all_options = get_option( 'exwoofood_options' );
if(isset($all_options['exwoofood_foodby_date']) && $all_options['exwoofood_foodby_date']=='yes'){
	include plugin_dir_path(__FILE__).'food-by-date.php';
}
// Radius shipping
include plugin_dir_path(__FILE__).'shipping.php';

if(!function_exists('exwoofood_startsWith')){
	function exwoofood_startsWith($haystack, $needle)
	{
		return !strncmp($haystack, $needle, strlen($needle));
	}
} 
if(!function_exists('exwoofood_get_google_fonts_url')){
	function exwoofood_get_google_fonts_url ($font_names) {
	
		$font_url = '';
	
		$font_url = add_query_arg( 'family', urlencode(implode('|', $font_names)) , "//fonts.googleapis.com/css" );
		return $font_url;
	} 
}
if(!function_exists('exwoofood_get_google_font_name')){
	function exwoofood_get_google_font_name($family_name){
		$name = $family_name;
		if(exwoofood_startsWith($family_name, 'http')){
			// $family_name is a full link, so first, we need to cut off the link
			$idx = strpos($name,'=');
			if($idx > -1){
				$name = substr($name, $idx);
			}
		}
		$idx = strpos($name,':');
		if($idx > -1){
			$name = substr($name, 0, $idx);
			$name = str_replace('+',' ', $name);
		}
		return $name;
	}
}
if(!function_exists('exwoofood_template_plugin')){
	function exwoofood_template_plugin($pageName,$shortcode=false){
		if(isset($shortcode) && $shortcode== true){
			if (locate_template('woocommerce-food/content-shortcodes/content-' . $pageName . '.php') != '') {
				get_template_part('woocommerce-food/content-shortcodes/content', $pageName);
			} else {
				include exwoof_get_plugin_url().'templates/content-shortcodes/content-' . $pageName . '.php';
			}
		}else{
			if (locate_template('woocommerce-food/' . $pageName . '.php') != '') {
				get_template_part('woocommerce-food/'.$pageName);
			} else {
				include exwoof_get_plugin_url().'templates/' . $pageName . '.php';
			}
		}
	}
}

if(!function_exists('exwoofood_query')){
    function exwoofood_query($posttype, $count, $order, $orderby, $cat, $tag, $taxonomy, $meta_key, $ids, $meta_value=false,$page=false,$mult=false,$active_filter=false,$feature=false, $sloc = false){
    	if(isset($active_filter) && $active_filter!=''){ $cat = $active_filter;}
		$posttype = 'product';
		if($orderby == 'order_field'){
			$meta_key = 'exwoofood_order';
			$orderby = 'meta_value_num';
		}
		$posttype = explode(",", $posttype);
		
		if($ids!=''){ //specify IDs
			$ids = explode(",", $ids);
			$args = array(
				'post_type' => $posttype,
				'posts_per_page' => $count,
				'post_status' => array( 'publish'),
				'post__in' =>  $ids,
				'order' => $order,
				'orderby' => $orderby,
				'ignore_sticky_posts' => 1,
			);
		}elseif($ids==''){
			$args = array(
				'post_type' => $posttype,
				'posts_per_page' => $count,
				'post_status' => array( 'publish'),
				'order' => $order,
				'orderby' => $orderby,
				'meta_key' => $meta_key,
				'ignore_sticky_posts' => 1,
			);
			if($orderby =='sale'){
				$ids = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
				if(is_array($ids) && !empty($ids)){
					$args['post__in'] = $ids;
				}
			}
		}

		$loc = WC()->session->get( 'ex_userloc' );
		if(isset($sloc) && $sloc!='' && $sloc!='OFF'){ $loc = $sloc;}
		if( $tag!=''){
			if($taxonomy ==''){ $taxonomy = 'product_tag';}
			$tags = explode(",",$tag);
			if(is_numeric($tags[0])){$field_tag = 'term_id'; }
			else{ $field_tag = 'slug'; }
			if(count($tags)>1){
				  $texo = array(
					  'relation' => 'OR',
				  );
				  foreach($tags as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field_tag,
							  'terms' => $iterm,
						  );
				  }
				  if($loc!=''){$texo = array($texo);}
			  }else{
				  $texo = array(
					  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field_tag,
							  'terms' => $tags,
						  )
				  );
			}
		}
		//cats
		if($cat!=''){
			if($taxonomy == '' || ($taxonomy != '' && $tag!='')){$taxonomy = 'product_cat';}
			$cats = explode(",",$cat);
			if(is_numeric($cats[0])){$field = 'term_id'; }
			else{ $field = 'slug'; }
			if(count($cats)>1){
				  $texo = array(
					  'relation' => 'OR',
				  );
				  foreach($cats as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
				  if($loc!=''){$texo = array($texo);}
			  }else{
				  $texo = array(
					  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $cats,
						  )
				  );
			}
		}
		// user select loc
		//check if ( exwoofood_get_option('exwoofood_enable_loc') =='yes' ) {
			
			if($loc!=''){
				$loc = explode(",",$loc);
				//if(is_numeric($loc[0])){$field = 'term_id'; }
				//else{ $field = 'slug'; }
				$field = 'slug';
				if(!isset($texo) || !is_array($texo)){ $texo = array();}
				$texo['relation'] = 'AND';
				if(count($loc)>1){
					  foreach($loc as $iterm) {
						  $texo[] = 
							  array(
								  'taxonomy' => 'exwoofood_loc',
								  'field' => $field,
								  'terms' => $iterm,
							  );
					  }
				  }else{
					  $texo[] = 
						  array(
								  'taxonomy' => 'exwoofood_loc',
								  'field' => $field,
								  'terms' => $loc,
					  );
				}
			}
		// End check }	
		if(isset($texo)){
			$args += array('tax_query' => $texo);
		}
		if(isset($feature) && $feature==1){
			$args['tax_query'][] = array(
				'taxonomy' => 'product_visibility',
				'field'    => 'name',
				'terms'    => 'featured',
			);
		}
		if(isset($meta_value) && $meta_value!='' && $meta_key!=''){
			if(!empty($args['meta_query'])){
				$args['meta_query']['relation'] = 'AND';
			}
			$args['meta_query'][] = array(
				'key'  => $meta_key,
				'value' => $meta_value,
				'compare' => '='
			);
		}
		if(isset($page) && $page!=''){
			$args['paged'] = $page;
		}
		return apply_filters( 'exwoofood_query', $args );
	}
}


if(!function_exists('EX_WPFood_customlink')){
	function EX_WPFood_customlink($id,$dislbox=false){
		if(isset($dislbox) && $dislbox=='yes') {
			return 'javascript:;';
		}
		return get_the_permalink($id);
	}
}


if(!function_exists('exwoofood_page_number_html')){
	if(!function_exists('exwoofood_page_number_html')){
		function exwoofood_page_number_html($the_query,$ID,$atts,$num_pg,$args,$arr_ids){
			if(isset($atts['cat'])){ $atts['cat'] = str_replace(' ', '', $atts['cat']);}
			if(isset($atts['ids'])){ $atts['ids'] = str_replace(' ', '', $atts['ids']);}
			if(function_exists('paginate_links')) {
				echo '<div class="exfd-pagination">';
				echo '
					<input type="hidden"  name="id_grid" value="'.esc_attr($ID).'">
					<input type="hidden"  name="num_page" value="'.esc_attr($num_pg).'">
					<input type="hidden"  name="num_page_uu" value="1">
					<input type="hidden"  name="current_page" value="1">
					<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">
					<input type="hidden"  name="param_query" value='.esc_attr(str_replace('\/', '/', json_encode($args))).'>
					<input type="hidden"  name="param_ids" value='.esc_attr(str_replace('\/', '/', json_encode($arr_ids))).'>
					<input type="hidden" id="param_shortcode" name="param_shortcode" value='.esc_attr(str_replace('\/', '/', json_encode($atts))).'>
				';
				if($num_pg > 1){
					$page_link =  paginate_links( array(
						'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
						'format'       => '?paged=%#%',
						'add_args'     => false,
						'show_all'     => true,
						'current' => max( 1, get_query_var('paged') ),
						'total' => $num_pg,
						'prev_next'    => false,
						'type'         => 'array',
						'end_size'     => 3,
						'mid_size'     => 3
					) );
					$class = '';
					if ( get_query_var('paged')<2) {
						$class = 'disable-click';
					}
					$prev_link = '<a class="prev-ajax '.esc_attr($class).'" href="javascript:;">&larr;</a>';
					$next_link = '<a class="next-ajax" href="javascript:;">&rarr;</a>';
					array_unshift($page_link, $prev_link);
					$page_link[] = $next_link;
					echo '<div class="page-navi">'.wp_kses_post(implode($page_link)).'</div>';
				}
				echo '</div>';
			}
		}
	}
}

if(!function_exists('exwoofood_ajax_navigate_html')){
	function exwoofood_ajax_navigate_html($ID,$atts,$num_pg,$args,$arr_ids){
		echo '
			<div class="ex-loadmore">
				<input type="hidden"  name="id_grid" value="'.esc_attr($ID).'">
				<input type="hidden"  name="num_page" value="'.esc_attr($num_pg).'">
				<input type="hidden"  name="num_page_uu" value="1">
				<input type="hidden"  name="current_page" value="1">
				<input type="hidden"  name="ajax_url" value="'.esc_url(admin_url( 'admin-ajax.php' )).'">
				<input type="hidden"  name="param_query" value="'.esc_attr(str_replace('\/', '/', htmlentities(json_encode($args)))).'">
				<input type="hidden"  name="param_ids" value='.esc_attr(str_replace('\/', '/', json_encode($arr_ids))).'>
				<input type="hidden" id="param_shortcode" name="param_shortcode" value='.esc_attr(str_replace('\/', '/', json_encode($atts))).'>';
				if($num_pg > 1){
					echo '
					<a  href="javascript:void(0)" class="loadmore-exfood" data-id="'.esc_attr($ID).'">
						<span class="load-text">'.esc_html__('Load more','woocommerce-food').'</span><span></span>&nbsp;<span></span>&nbsp;<span></span>
					</a>';
				}
				echo '
		</div>';
	}
}

add_action( 'wp_ajax_exwoofood_loadmore', 'ajax_exwoofood_loadmore' );
add_action( 'wp_ajax_nopriv_exwoofood_loadmore', 'ajax_exwoofood_loadmore' );
function ajax_exwoofood_loadmore(){
	global $columns,$number_excerpt,$show_time,$orderby,$img_size,$ID;
	global $ID,$number_excerpt,$img_size;
	$atts = json_decode( stripslashes( $_POST['param_shortcode'] ), true );
	$ID = isset($atts['ID']) && $atts['ID'] !=''? $atts['ID'] : 'ex-'.rand(10,9999);
	$style = isset($atts['style']) && $atts['style'] !=''? $atts['style'] : '1';
	$column = isset($atts['column']) && $atts['column'] !=''? $atts['column'] : '2';
	$posttype   = isset($atts['posttype']) && $atts['posttype']!='' ? $atts['posttype'] : 'product';
	$ids   = isset($atts['ids']) ? $atts['ids'] : '';
	$taxonomy  = isset($atts['taxonomy']) ? $atts['taxonomy'] : '';
	$cat   = isset($atts['cat']) ? $atts['cat'] : '';
	$tag  = isset($atts['tag']) ? $atts['tag'] : '';
	$count   = isset($atts['count']) &&  $atts['count'] !=''? $atts['count'] : '9';
	$posts_per_page   = isset($atts['posts_per_page']) && $atts['posts_per_page'] !=''? $atts['posts_per_page'] : '3';
	$order  = isset($atts['order']) ? $atts['order'] : '';
	$orderby  = isset($atts['orderby']) ? $atts['orderby'] : '';
	$meta_key  = isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value  = isset($atts['meta_value']) ? $atts['meta_value'] : '';
	$class  = isset($atts['class']) ? $atts['class'] : '';
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$layout = isset($_POST['layout']) ? $_POST['layout'] : '';
	$param_query = json_decode( stripslashes( $_POST['param_query'] ), true );
	$param_ids = '';
	if(isset($_POST['param_ids']) && $_POST['param_ids']!=''){
		$param_ids =  json_decode( stripslashes( $_POST['param_ids'] ), true )!='' ? json_decode( stripslashes( $_POST['param_ids'] ), true ) : explode(",",$_POST['param_ids']);
	}
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	if($orderby =='rand' && is_array($param_ids)){
		$param_query['post__not_in'] = $param_ids;
		$param_query['paged'] = 1;
	}
	if($orderby =='sale'){
		$ids = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		if(is_array($ids) && !empty($ids)){
			$param_query['post__in'] = $ids;
		}
	}
	$param_query = apply_filters('exwf_ajax_query_args',$param_query,$atts,$param_ids);
	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	if($the_query->have_posts()){
		$i =0;
		$arr_ids = array();
		$html_modal = '';
		while($the_query->have_posts()){ $the_query->the_post();
			$i++;
			$arr_ids[] = get_the_ID();
			if($layout=='table'){
				exwoofood_template_plugin('table-'.$style,1);
			}else if($layout=='list'){
				echo '<div class="fditem-list item-grid" data-id="ex_id-'.esc_attr($ID).'-'.esc_attr(get_the_ID()).'" data-id_food="'.esc_attr(get_the_ID()).'" id="ctc-'.esc_attr($ID).'-'.get_the_ID().'"> ';
					exwf_custom_color('list',$style,'ctc-'.esc_attr($ID).'-'.get_the_ID());
						?>
					<div class="exp-arrow" >
						<?php 
						exwoofood_template_plugin('list-'.$style,1);
						?>
					<div class="exfd_clearfix"></div>
					</div>
					<?php
				echo '</div>';
			}else{
				echo '<div class="item-grid" data-id="ex_id-'.esc_attr($ID).'-'.esc_attr(get_the_ID()).'" data-id_food="'.esc_attr(get_the_ID()).'" id="ctc-'.esc_attr($ID).'-'.get_the_ID().'"> ';
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
			if($end_it_nb!='' && $end_it_nb == $i){break;}
		}
		wp_reset_postdata();
		
		if(is_array($param_ids)){
			?>
	        <script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery('#<?php  echo esc_html__($_POST['id_crsc']);?> input[name=param_ids]').val(<?php echo str_replace('\/', '/', json_encode(array_merge($param_ids,$arr_ids)));?>);
			});
	        </script>
	        <?php 
		}?>
        </div>
        <?php
	}
	$html = ob_get_clean();
	$output =  array('html_content'=>$html,'html_modal'=> $html_modal);
	echo str_replace('\/', '/', json_encode($output));
	die;
}
// register sesion
add_action( 'init', 'exwf_wc_session_user' );
function exwf_wc_session_user() {
    if ( is_user_logged_in() || is_admin() )
        return;

    if ( isset(WC()->session) && ! WC()->session->has_session() ) {
        WC()->session->set_customer_session_cookie( true );
    }
}
/*
function register_exwoofood_session(){
	if(is_admin()&& !defined( 'DOING_AJAX' )){ return;}
	if( !session_id() ){
    	session_start();
	}
}
add_action('init', 'register_exwoofood_session');
*/

add_action( 'wp_ajax_exfood_menuegory', 'ajax_exfood_menuegory' );
add_action( 'wp_ajax_nopriv_exfood_menuegory', 'ajax_exfood_menuegory' );
function ajax_exfood_menuegory(){
	global $ID,$number_excerpt,$img_size;
	$atts = json_decode( stripslashes( $_POST['param_shortcode'] ), true );
	$ID = isset($atts['ID']) && $atts['ID'] !=''? $atts['ID'] : 'ex-'.rand(10,9999);
	$ids   = isset($atts['ids']) ? $atts['ids'] : '';
	$count   = isset($atts['count']) &&  $atts['count'] !=''? $atts['count'] : '9';
	$style = isset($atts['style']) && $atts['style'] !=''? $atts['style'] : '1';
	$posts_per_page   = isset($atts['posts_per_page']) && $atts['posts_per_page'] !=''? $atts['posts_per_page'] : '3';
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$cat   = isset($atts['cat']) ? $atts['cat'] : '';
	$orderby   = isset($atts['orderby']) ? $atts['orderby'] : '';
	$page_navi  = isset($atts['page_navi']) ? $atts['page_navi'] : '';
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'';
	$featured =  isset($atts['featured']) ? $atts['featured'] :'';
	$page = isset($_POST['page']) ? $_POST['page'] : '';
	$layout = isset($_POST['layout']) ? $_POST['layout'] : '';
	$param_query = json_decode( stripslashes( $_POST['param_query'] ), true );
	$param_ids = '';
	if(isset($_POST['param_ids']) && $_POST['param_ids']!=''){
		$param_ids =  json_decode( stripslashes( $_POST['param_ids'] ), true )!='' ? json_decode( stripslashes( $_POST['param_ids'] ), true ) : explode(",",$_POST['param_ids']);
	}
	$end_it_nb ='';
	if($page!=''){ 
		$param_query['paged'] = $page;
		$count_check = $page*$posts_per_page;
		if(($count_check > $count) && (($count_check - $count)< $posts_per_page)){$end_it_nb = $count - (($page - 1)*$posts_per_page);}
		else if(($count_check > $count)) {die;}
	}
	$param_query['post__in'] ='';
	$loc = '';
	if ( exwoofood_get_option('exwoofood_enable_loc') =='yes' ) {
		$loc = WC()->session->get( 'ex_userloc' );
	}
	if(isset($_POST['cat']) && $_POST['cat']!=''){
		$texo = array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $_POST['cat'],
			),
		);
	}else{
		$param_query['tax_query'] ='';
		if($cat!=''){
			$taxonomy ='product_cat'; 
			$cats = explode(",",$cat);
			if(is_numeric($cats[0])){$field = 'term_id'; }else{ $field = 'slug'; }
			if(count($cats)>1){
				  $texo = array( 'relation' => 'OR');
				  foreach($cats as $iterm) {
					  $texo[] = array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
				  if($loc!=''){$texo = array($texo);}
			  }else{
				  $texo = array(
					  array(
							  'taxonomy' => $taxonomy,
							  'field' => $field,
							  'terms' => $cats,
						  )
				  );
			}
			
		}
	}
	if ( exwoofood_get_option('exwoofood_enable_loc') =='yes' ) {
		if($loc!=''){
			$loc = explode(",",$loc);
			//if(is_numeric($loc[0])){$field = 'term_id'; }
			//else{ $field = 'slug'; }
			$field = 'slug';
			if(!isset($texo) || !is_array($texo)){ $texo = array();}
			$texo['relation'] = 'AND';
			if(count($loc)>1){
				  foreach($loc as $iterm) {
					  $texo[] = 
						  array(
							  'taxonomy' => 'exwoofood_loc',
							  'field' => $field,
							  'terms' => $iterm,
						  );
				  }
			  }else{
				  $texo[] = 
					  array(
							  'taxonomy' => 'exwoofood_loc',
							  'field' => $field,
							  'terms' => $loc,
				  );
			}
		}
	}
	if(isset($texo)){
		$param_query['tax_query'] = $texo;
	}
	if($ids!=''){
		$ids = explode(",", $ids);
		$param_query['post__in'] = $ids;
	}
	if($orderby =='sale'){
		$ids = array_merge( array( 0 ), wc_get_product_ids_on_sale() );
		if(is_array($ids) && !empty($ids)){
			$param_query['post__in'] = $ids;
		}
	}
	if(isset($featured) && $featured==1){
		$param_query['tax_query'][] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => 'featured',
		);
	}
	if(isset($_POST['key_word']) && $_POST['key_word']!=''){
		$param_query['s'] = $_POST['key_word'];
	}else{
		$param_query['s'] = '';
	}
	$param_query = apply_filters('exwf_ajax_filter_query_args',$param_query,$atts);

	$the_query = new WP_Query( $param_query );
	$it = $the_query->post_count;
	ob_start();
	if($the_query->have_posts()){
		$it = $the_query->found_posts;
		if($it < $count || $count=='-1'){ $count = $it;}
		if($count  > $posts_per_page){
			$num_pg = ceil($count/$posts_per_page);
			$it_ep  = $count%$posts_per_page;
		}else{
			$num_pg = 1;
		}
		$arr_ids = array();
		$html_modal = '';
		$i=0;
		while($the_query->have_posts()){ $the_query->the_post();
			$i++;
			$arr_ids[] = get_the_ID();
			if($layout=='list'){
				echo '<div class="fditem-list item-grid" data-id="ex_id-'.esc_attr($ID).'-'.get_the_ID().'" data-id_food="'.get_the_ID().'" id="ctc-'.esc_attr($ID).'-'.get_the_ID().'"> ';
					exwf_custom_color('list',$style,'ctc-'.esc_attr($ID).'-'.get_the_ID());
						?>
					<div class="exp-arrow" >
						<?php 
						exwoofood_template_plugin('list-'.$style,1);
						?>
					<div class="exfd_clearfix"></div>
					</div>
					<?php
				echo '</div>';
			}elseif($layout=='table'){
				exwoofood_template_plugin('table-'.$style,1);
			}else{
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
			if($end_it_nb!='' && $end_it_nb == $i){break;}
		}
		
		wp_reset_postdata();
		
		?>
        </div>
        <?php
	}

	$html = ob_get_contents();
	ob_end_clean();
	$html_dcat = '';
	if($html==''){
		$html = '<span class="exwf-no-rs">'.esc_html__('No matching records found','woocommerce-food').'</span>';
	}else if(isset($_POST['cat']) && $_POST['cat']!=''){
		$term = get_term_by('slug', $_POST['cat'], 'product_cat');
		if($term->description!=''){
			$html_dcat ='<p class="exwf-dcat" style="display:block;">'.$term->description.'</p>';
		}
	}
	ob_start();
	// global $modal_html;
	// 	if(!isset($modal_html) || $modal_html!='on'){
	// 		$modal_html = 'on';
	// 		echo "<div id='food_modal' class='ex_modal'></div>";
	// 	}
	if($page_navi=='loadmore'){
		exwoofood_ajax_navigate_html($ID,$atts,$num_pg,$param_query,$arr_ids); 
	}else{
		exwoofood_page_number_html($the_query,$ID,$atts,$num_pg,$param_query,$arr_ids);
	}
	$page_navihtml = ob_get_contents();
	ob_end_clean();
	$output =  array('html_content'=>$html,'page_navi'=> $page_navihtml,'html_modal'=>$html_modal,'html_dcat'=>$html_dcat);
	echo str_replace('\/', '/', json_encode($output));
	die;
}
if(!function_exists('exwoofood_search_form_html')){
	function exwoofood_search_form_html($cats, $order_cat, $pos = false,$active_filter=false,$filter_style=false,$hide_ftall=false){
		$args = array(
			'hide_empty'        => true,
			'parent'        => '0',
		);
		if($cats !=''){
		    unset($args['parent']);
		}
		$cats = $cats!=''? explode(",",$cats) : array();
		if (!empty($cats) && !is_numeric($cats[0])) {
			$args['slug'] = $cats;
			$args['orderby'] = 'slug__in';
		}else if (!empty($cats)) {
			$args['include'] = $cats;
			$args['orderby'] = 'include';
		}
		if ($order_cat == 'yes') {
			$args['meta_key'] = 'exwoofood_menu_order';
			$args['orderby'] = 'meta_value_num';
		}
		$loc_selected = exwf_get_loc_selected();
		$exclude = array();
		if($loc_selected!=''){
			$exclude = get_term_meta( $loc_selected, 'exwp_loc_hide_menu', true );
		}
		$count_stop = 5;
		$terms = get_terms('product_cat', $args);
		?>
        <div class="exfd-filter <?php echo isset($filter_style) && $filter_style=='icon' ? 'exwf-fticon-style' :''; ?>">
	    	<div class="exfd-filter-group">
	            <?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){ 
	            	$select_option = $list_item = '';
	            	
	            	?>
	            	<div class="ex-menu-list">
	            		<?php if (isset($pos) && $pos=='left'){
	            			$act_cls = 'ex-active-left';
	            		}else{
	            			$act_cls = 'ex-menu-item-active';
	            		}
	            		$all_atcl = $act_cls;
	            		if(isset($active_filter) && $active_filter != ''){
	            			$all_atcl = '';	
	            		}
	            		if(isset($hide_ftall) && $hide_ftall!='yes'){?>
		            		<a class="ex-menu-item <?php esc_attr_e($all_atcl);?>" href="javascript:;" data-value=""><?php echo esc_html__('All','woocommerce-food'); ?></a><?php
		            	}
	            			foreach ( $terms as $term ) {
	            				if(is_array($exclude) && !empty($exclude) && in_array($term->slug, $exclude)){
	            					// if exclude
	            				}else{
		            				$all_atcl = '';
		            				if(isset($active_filter) && $active_filter == $term->slug){
		            					$all_atcl = $act_cls;
		            				}
		            				global $wp;
									$curent_url = home_url( $wp->request );
							  		echo '<a class="ex-menu-item '.esc_attr($all_atcl).'" href="'.esc_url(add_query_arg( array('menu' => $term->slug), $curent_url )).'" data-value="'. esc_attr($term->slug) .'">';
								  		if(isset($filter_style) && $filter_style=='icon'){
								  			$_iconsc = get_term_meta( $term->term_id, 'exwoofood_menu_iconsc', true );
								  			if($_iconsc!=''){
								  				echo '<span class="exwf-caticon exwf-iconsc">'.$_iconsc.'</span>'; 
								  			}else{
									  			$thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
									  			if($thumbnail_id!=''){
													// get the medium-sized image url
													$image = wp_get_attachment_image_src( $thumbnail_id, 'full' );
													// Output in img tag
													if(isset($image[0]) && $image[0]!=''){
														echo '<span class="exwf-caticon"><img src="' . $image[0] . '" alt="" /></span>'; 
													}
												}
											}
										}
										echo wp_kses_post($term->name);
							  			exfd_show_child_inline($cats,$term,$count_stop,$order_cat,'inline',$exclude);
							  		echo '</a>';
							  	}
						  	}
	            			?>
	            		<div class="exfd_clearfix"></div>
	            	</div>
	            	<div class="ex-menu-select">
	            		<div>
			                <select name="exfood_menu">
			                	<?php if(isset($hide_ftall) && $hide_ftall!='yes'){?>
				                	<option value=""><?php echo esc_html__('All','woocommerce-food'); ?></option>
				                	<?php 
			                	}
		            			foreach ( $terms as $term ) {
		            				if(is_array($exclude) && !empty($exclude) && in_array($term->slug, $exclude)){
	            					// if exclude
	            					}else{
			            				$selected  ='';
			            				if(isset($active_filter) && $active_filter == $term->slug){
			            					$selected  ='selected';
			            				}
								  		echo '<option value="'. esc_attr($term->slug) .'" '.esc_attr($selected).'>'. wp_kses_post($term->name) .'</option>';
								  		echo exfd_show_child_inline($cats,$term,$count_stop,$order_cat,'',$exclude);
								  	}
							  	}
			                	?>
			                </select>
			            </div>
		            </div>
	            <?php } //if have terms ?>
	        </div>
        </div>
        <?php
	}
}

if(!function_exists('exfd_show_child_inline')){
	function exfd_show_child_inline($cats,$term,$count_stop,$order_cat,$inline,$exclude=false){
		if ($count_stop < 2) {
			return;
		}
		$charactor ='';
		if ($count_stop == 5) {
			$charactor ='— ';
		}elseif ($count_stop == 4) {
			$charactor ='—— ';
		}elseif ($count_stop == 3) {
			$charactor ='——— ';
		}elseif ($count_stop == 2) {
			$charactor ='———— ';
		}
		$args_child = array(
				'child_of' => $term->term_id,
				'parent' => $term->term_id,
				'hide_empty'        => false,
		);
		if ($order_cat == 'yes') {
			$args_child['meta_key'] = 'exwoofood_menu_order';
			$args_child['orderby'] = 'meta_value_num';
		}
		$second_level_terms = get_terms('product_cat', $args_child);
		if ($second_level_terms) {
			$count_stop = $count_stop -1;
			if ($inline != 'inline') {
				foreach ($second_level_terms as $second_level_term) {
					if(is_array($exclude) && !empty($exclude) && in_array($second_level_term->slug, $exclude)){
					// if exclude
					}else{
						echo '<option value="'. esc_attr($second_level_term->slug) .'">'.wp_kses_post($charactor. $second_level_term->name) .'</option>';
						exfd_show_child_inline($cats,$second_level_term,$count_stop,$order_cat,'');
					}
				}
			}else{
				echo '<span class="exfd-caret"></span>';
		        echo '<ul class="exfd-ul-child">';
		        global $wp;
				$curent_url = home_url( $wp->request );
		        foreach ($second_level_terms as $second_level_term) {
		        	if(is_array($exclude) && !empty($exclude) && in_array($second_level_term->slug, $exclude)){
					// if exclude
					}else{
			            $second_term_name = $second_level_term->name;
			            echo '<li class="exfd-child-click ex-menu-item" data-value="'.esc_attr($second_level_term->slug).'" data-url="'.esc_url(add_query_arg( array('menu' => $second_level_term->slug), $curent_url )).'">
			            '.wp_kses_post($second_term_name);
			            exfd_show_child_inline($cats,$second_level_term,$count_stop,$order_cat,'inline');
			            echo '</li>';
			        }
		        }

		        echo '</ul>';
		    }
	    }
	}
}

function exwoofood_convert_color($color){
	if ($color == '') {
		return;
	}
	$hex  = str_replace("#", "", $color);
	if(strlen($hex) == 3) {
	  $r = hexdec(substr($hex,0,1).substr($hex,0,1));
	  $g = hexdec(substr($hex,1,1).substr($hex,1,1));
	  $b = hexdec(substr($hex,2,1).substr($hex,2,1));
	} else {
	  $r = hexdec(substr($hex,0,2));
	  $g = hexdec(substr($hex,2,2));
	  $b = hexdec(substr($hex,4,2));
	}
	$rgb = $r.','. $g.','.$b;
	return $rgb;
}

if(!function_exists('exwoofood_sale_badge')){
	function exwoofood_sale_badge(){
		global $product;
		if ( is_object($product) && method_exists($product, 'is_on_sale') && $product->is_on_sale() ) { ?>
			<div class="exfd-ribbon"><span><?php esc_html_e('Sale','woocommerce-food');?></span></div>
			<?php 
		}elseif(exfd_show_reviews('',$product)!=''){

			echo '<div class="exfd-ribbon"><span>'.exfd_show_reviews('',$product).'</span></div>';
		}
	}
}

if(!function_exists('exwoofood_add_to_cart_form_shortcode')){
	function exwoofood_add_to_cart_form_shortcode( $atts ) {
		if (!exwf_check_open_close_time($atts['id'])) {
			return exwfd_open_closing_message();
		}
		$hide_pm = isset( $atts['hide_pm']) ? $atts['hide_pm'] : '';

		if ( empty( $atts ) || !function_exists('woocommerce_template_single_add_to_cart')) { return '';}
		if ( ! isset( $atts['id'] ) && ! isset( $atts['sku'] ) ) { return '';}
		$args = array(
			'posts_per_page'      => 1,
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'no_found_rows'       => 1,
		);
		if ( isset( $atts['sku'] ) ) {
			$args['meta_query'][] = array(
				'key'     => '_sku',
				'value'   => sanitize_text_field( $atts['sku'] ),
				'compare' => '=',
			);
			$args['post_type'] = array( 'product', 'product_variation' );
		}
		if ( isset( $atts['id'] ) ) {
			$args['p'] = absint( $atts['id'] );
		}
		// Change form action to avoid redirect.
		add_filter( 'woocommerce_add_to_cart_form_action', '__return_empty_string' );
		$single_product = new WP_Query( $args );
		$preselected_id = '0';
		global $wp_food;
		$wp_food = 'woo';
		// Check if sku is a variation.
		if ( isset( $atts['sku'] ) && $single_product->have_posts() && 'product_variation' === $single_product->post->post_type ) {
			$variation = new WC_Product_Variation( $single_product->post->ID );
			$attributes = $variation->get_attributes();
			// Set preselected id to be used by JS to provide context.
			$preselected_id = $single_product->post->ID;
			// Get the parent product object.
			$args = array(
				'posts_per_page'      => 1,
				'post_type'           => 'product',
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
				'no_found_rows'       => 1,
				'p'                   => $single_product->post->post_parent,
			);
			$single_product = new WP_Query( $args );
			?>
			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					var $variations_form = $( '[data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>"]' ).find( 'form.variations_form' );
					<?php foreach ( $attributes as $attr => $value ) { ?>
						$variations_form.find( 'select[name="<?php echo esc_attr( $attr ); ?>"]' ).val( '<?php echo esc_js( $value ); ?>' );
					<?php } ?>
				});
			</script>
		<?php
		}
		// For "is_single" to always make load comments_template() for reviews.
		$single_product->is_single = false;
		ob_start();
		global $wp_query;
		// Backup query object so following loops think this is a product page.
		$previous_wp_query = $wp_query;
		$wp_query          = $single_product;
		wp_enqueue_script( 'wc-single-product' );
		while ( $single_product->have_posts() ) {
			$single_product->the_post();?>
			<div class="single-product" data-product-page-preselected-id="<?php echo esc_attr( $preselected_id ); ?>">
				<?php woocommerce_template_single_add_to_cart();
				do_action('exwf_after_atc_form');
				if($hide_pm!='1'){?>
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery( '#food_modal .exwoofood-woocommerce .cart div.quantity:not(.exbuttons_added):not(.hidden)' ).addClass( 'exbuttons_added' ).append( '<input type="button" value="+" id="exadd_ticket" class="explus" />' ).prepend( '<input type="button" value="-" id="exminus_ticket" class="ex-minus" />' );
							jQuery('#food_modal:not(.exf-dis-bt) .exwoofood-woocommerce .exbuttons_added').on('click', '#exminus_ticket',function() {
								var value = parseInt(jQuery(this).closest(".quantity").find('.qty').val()) - 1;
								if(value>0){
									jQuery(this).closest(".quantity").find('.qty').val(value);
								}else if(value == 0 && jQuery( '#food_modal .grouped_form').length){
									jQuery(this).closest(".quantity").find('.qty').val(value);
								}
							});
							jQuery('#food_modal:not(.exf-dis-bt) .exwoofood-woocommerce .exbuttons_added').on('click', '#exadd_ticket',function() {
								var value = jQuery(this).closest(".quantity").find('.qty').val();
								value = value!='' ? parseInt(value) : 0;
								value = value + 1;
								jQuery(this).closest(".quantity").find('.qty').val(value);
							});
						});
						if ( typeof exwf_change_img == 'function' ) {
						}else{
							function exwf_change_img(){
								var defimg = '';
								/*jQuery( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
									if(variation.image.src!=''){
										console.log(variation.image.src);
										jQuery('#food_modal .fd_modal_img').html('');
									}
								} );*/
								jQuery( document ).on( "found_variation.first", function ( e, variation ) {
									if(variation.image.full_src!=''){
										jQuery('#food_modal .fd_modal_img .exwf-vari-img').fadeOut("normal", function() {
									        jQuery(this).remove();
									    });
										jQuery('#food_modal .fd_modal_img').prepend('<div class="exwf-vari-img"><img src="'+variation.image.full_src+'"/></div>').fadeIn('normal');
									}
								} );
								jQuery( ".variations_form" ).on( "woocommerce_variation_select_change", function () {
								    setTimeout(function(){ 
										var $_cr_img = jQuery('#food_modal .exwoofood-woocommerce form.variations_form').attr("current-image");
								    	if($_cr_img==''){
								    		jQuery('#food_modal .fd_modal_img .exwf-vari-img').remove();
								    		jQuery('#food_modal .exfd-modal-carousel:not(.exwp-no-galle)').EX_ex_s_lick('setPosition');
								    	}
									}, 500);
								} );
							}
							exwf_change_img();
						}	
					</script>
				<?php 
				}?>
			</div>
			<?php
		}
		// Restore $previous_wp_query and reset post data.
		$wp_query = $previous_wp_query;
		wp_reset_postdata();
		return '<div class="exwoofood-woocommerce woocommerce">' . ob_get_clean() . '</div>';
	}
}
add_shortcode( 'ex_food_wooform', 'exwoofood_add_to_cart_form_shortcode' );

add_action( 'wp_ajax_exwoofood_booking_info', 'ajax_exwoofood_booking_info' );
add_action( 'wp_ajax_nopriv_exwoofood_booking_info', 'ajax_exwoofood_booking_info' );

function ajax_exwoofood_booking_info(){
	if(isset($_POST['id_food']) && $_POST['id_food']!=''){
		$product_exist = $_POST['id_food'];
		global $atts,$id_food;
		$id_food = $_POST['id_food'];
        if($product_exist!='' && is_numeric($product_exist)){
			$atts['id'] = $product_exist;
		}
		exwoofood_template_plugin('modal',true);
	}else{
		echo 'error';
	}
	exit;	
}

add_action('wp_ajax_exwoofood_add_to_cart', 'exwoofood_ajax_add_to_cart');
add_action('wp_ajax_nopriv_exwoofood_add_to_cart', 'exwoofood_ajax_add_to_cart');
function exwoofood_ajax_add_to_cart() {
	$product_id = apply_filters('woocommerce_add_to_cart_product_id', absint($_POST['add-to-cart']));
    $quantity = empty($_POST['quantity']) ? 1 : wc_stock_amount($_POST['quantity']);
    $variation_id = absint($_POST['variation_id']);
    do_action('exwf_before_ajaxadd_to_cart',$_POST);
    $passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity,$variation_id,);
    $product_status = get_post_status($product_id);
    $un_vali = apply_filters( 'exwfood_remove_atc_valid', false );
    if ($passed_validation || $un_vali == true) {

        do_action('woocommerce_ajax_added_to_cart', $product_id);

        if ('yes' === get_option('woocommerce_cart_redirect_after_add')) {
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }

        WC_AJAX :: get_refreshed_fragments();
    } else {

        $data = array(
            'error' => true,
            'message' => '<p class="exfd-out-notice">'.esc_html__( 'Please re-check all required fields and try again', 'woocommerce-food' ).'</p>'
        );

        echo wp_send_json($data);
    }
    wp_die();
}
add_action('wp_ajax_exwoofood_refresh_cart', 'exwoofood_refresh_cart');
add_action('wp_ajax_nopriv_exwoofood_refresh_cart', 'exwoofood_refresh_cart');
function exwoofood_refresh_cart() {
	WC_AJAX :: get_refreshed_fragments();
	wp_die();
}

/*--- Booking button ---*/
if(!function_exists('exwoofood_booking_button_html')){
	function exwoofood_booking_button_html($style) {
		if (!exwf_check_open_close_time(get_the_ID())) {
			return;
		}
		$html = '<a href="'.get_the_permalink(get_the_ID()).'" class="exstyle-'.esc_attr($style).'-button">'.esc_html__( 'Order', 'woocommerce-food' ).'</a>';
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        $product_exist = get_the_ID();
    	$product = wc_get_product ($product_exist);
    	if($product!==false) {
        	$type = $product->get_type();
        	$disable_addon = apply_filters( 'exwf_disable_default_options', 'no' );
        	if($type =='variable'){

        	}else if(function_exists('exwoo_get_options') && $disable_addon!='yes'){
        		$data_options = exwoo_get_options($product_exist);
        		$ck_buin = 0;
        		if(is_array($data_options) && !empty($data_options)){

        		}else{
	        		if($type =='simple' && is_array($data_options) && empty($data_options) || $type =='simple' && $data_options =='' ){
	        			$html = do_shortcode( '[ex_food_wooform id="'.$product_exist.'" hide_pm="1"]');
	        		}else{$ck_buin = 1;}
	        		if (is_plugin_active( 'woocommerce-tm-extra-product-options/tm-woo-extra-product-options.php' ) ) {
	        			$has_epo = THEMECOMPLETE_EPO_API()->has_options($product_exist );

						if ( THEMECOMPLETE_EPO_API()->is_valid_options( $has_epo ) ) {
							$html = '<a href="'.get_the_permalink($product_exist).'" class="exstyle-'.esc_attr($style).'-button">'.esc_html__( 'Order', 'woocommerce-food' ).'</a>';
	        			}else{
	        				$html = do_shortcode( '[ex_food_wooform id="'.$product_exist.'" hide_pm="1"]');
	        			}
						
					}
					if ($ck_buin!='1' &&  is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
						if(function_exists('get_product_addons')){
							$product_addons = get_product_addons( $product_exist, false );
						}else{
							$product_addons = WC_Product_Addons_Helper::get_product_addons( $product_exist );
							wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
						}
						if ( is_array( $product_addons ) && sizeof( $product_addons ) > 0 ) {
							$html = '<a href="'.get_the_permalink($product_exist).'" class="exstyle-'.esc_attr($style).'-button">'.esc_html__( 'Order', 'woocommerce-food' ).'</a>';
						}else if($type =='simple'){
							$html = do_shortcode( '[ex_food_wooform id="'.$product_exist.'" hide_pm="1"]');
						}
					}
				}
        	}else if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
				if(function_exists('get_product_addons')){
					$product_addons = get_product_addons( $product_exist, false );
				}else{
					$product_addons = WC_Product_Addons_Helper::get_product_addons( $product_exist );
					wp_enqueue_script( 'jquery-tiptip', WC()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js', array( 'jquery' ), WC_VERSION, true );
				}
				if ( is_array( $product_addons ) && sizeof( $product_addons ) > 0 ) {
				}else if($type =='simple'){
					$html = do_shortcode( '[ex_food_wooform id="'.$product_exist.'" hide_pm="1"]');
				}
			}else if (is_plugin_active( 'woocommerce-tm-extra-product-options/tm-woo-extra-product-options.php' ) ) {
				// alway open lightbox
			}else if($type =='simple'){
				$html = do_shortcode( '[ex_food_wooform id="'.$product_exist.'" hide_pm="1"]');
			}
		}
		//inline button
		echo '<div class="exbt-inline">'.$html.'</div>';
		
	}
}

add_filter( 'woocommerce_add_to_cart_fragments', 'exwoofood_woo_cart_count_fragments', 10, 1 );
function exwoofood_woo_cart_count_fragments( $fragments ) {
    $fragments['span.exfd-cart-num'] = '<span class="exfd-cart-num">' . WC()->cart->get_cart_contents_count() . '</span>';
    
    return $fragments;
}

add_filter( 'woocommerce_add_to_cart_fragments', 'exwoofood_woo_cart_content_fragments', 10, 1 );
function exwoofood_woo_cart_content_fragments( $fragments ) {
    ob_start();?>
    <div class="exfd-cart-mini"><?php woocommerce_mini_cart(); ?></div>
    <?php
    $fragments['div.exfd-cart-mini'] = ob_get_contents();
    ob_get_clean();
    return $fragments;
}
// exfood price
function exwoofood_price_with_currency($id_food=false){
	global $product;
	if(isset($id_food) && is_numeric($id_food)){
		$product = wc_get_product ($id_food);
	}
	//$type = $product->get_type();
	$price ='';
	if ( $price_html = $product->get_price_html() ) :
		$price = $price_html; 
	endif; 	
	return $price;
}
function exwoofood_woo_cart_icon_html($show){
	global $cart_icon;
	if(!isset($cart_icon) || $cart_icon!='on' || $show='yes'){
		$cart_icon = 'on';
	}else if($cart_icon =='on'){
		return;
	}
	if(!function_exists('woocommerce_mini_cart')){ return;}
	exwoofood_template_plugin('cart-mini',1);
}

function exwoofood_select_loc_html($atts){
	$locations = isset($atts['locations']) ? $atts['locations'] : '';
	$args = array(
		'hide_empty'        => true,
		'parent'        => '0',
	);
	$locations = $locations!='' ? explode(",",$locations) : array();
	if (!empty($locations) && !is_numeric($locations[0])) {
		$args['slug'] = $locations;
	}else if (!empty($locations)) {
		$args['include'] = $locations;
	}
	$terms = get_terms('exwoofood_loc', $args);
	ob_start();
	$loc_selected = WC()->session->get( 'ex_userloc' );
	?>
	<div class="exwoofood-select-loc">
		<div>
			<select class="ex-loc-select" data-text="<?php esc_html_e('The products added to cart for this location will reset. Are you sure you want to change the location ?','woocommerce-food');?>">
				<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
					global $wp;
					$cr_url =  home_url( $wp->request );
		        	$select_option = '';
		        	echo  '<option disabled selected value>'.esc_html__( '-- Select --', 'woocommerce-food' ) .'</option>';
		        	$count_stop = 5;
		        	foreach ( $terms as $term ) {
		        		$url = add_query_arg(array('loc' => $term->slug), $cr_url);
		        		$selected = $loc_selected == $term->slug ? 'selected' : '';
				  		echo '<option value="'. esc_url($url) .'" '.esc_attr($selected).' >'. wp_kses_post($term->name) .'</option>';
				  		exfd_show_child_location($locations,$term,$count_stop,$loc_selected,'');
				  	}
		        } //if have terms ?>
			</select>
		</div>
	</div>
	<?php
	$cart_content = ob_get_contents();
	ob_end_clean();
	return $cart_content;
}
add_shortcode( 'exwoofood_sllocation', 'exwoofood_select_loc_html' );
if(!function_exists('exfd_show_child_location')){
	function exfd_show_child_location($locations,$term,$count_stop,$loc_selected,$checkout){
		if ($count_stop < 2) {
			return;
		}
		$charactor ='';
		if ($count_stop == 5) {
			$charactor ='— ';
		}elseif ($count_stop == 4) {
			$charactor ='—— ';
		}elseif ($count_stop == 3) {
			$charactor ='——— ';
		}elseif ($count_stop == 2) {
			$charactor ='———— ';
		}
		$args_child = array(
				'child_of' => $term->term_id,
				'parent' => $term->term_id,
				'hide_empty'        => false,
		);
		
		$second_level_terms = get_terms('exwoofood_loc', $args_child);
		$loc_current = '';
		/*if (isset($_SESSION['exfd_data_check']) && $_SESSION['exfd_data_check']!='') {
			$data_order = array();
			$data_order = $_SESSION['exfd_data_check'];
			$loc_current = isset($data_order['_location']) ? $data_order['_location'] : '';
		}*/
		ob_start();
		if ($second_level_terms) {
			$count_stop = $count_stop -1;
			foreach ($second_level_terms as $second_level_term) {
				if ($checkout !='yes') {
					//global $wp;
					$cr_url =  home_url( $wp->request );
					$url = add_query_arg(array('loc' => $second_level_term->slug), $cr_url);
	        		$selected = $loc_selected == $second_level_term->slug ? 'selected' : '';
			  		echo '<option value="'. esc_url($url) .'" '.esc_attr($selected).' >'.$charactor. wp_kses_post($second_level_term->name) .'</option>';
				}else{
					$select_loc = '';
	        		if ($second_level_term->slug !='' && $second_level_term->slug == $loc_selected) {
		                $select_loc = ' selected="selected"';
		              }
					echo '<option value="'. esc_attr($second_level_term->slug) .'" '.$select_loc.'>'.$charactor. wp_kses_post($second_level_term->name) .'</option>';
				}
				
				exfd_show_child_location($locations,$second_level_term,$count_stop,$loc_selected,$checkout);
			}
	    }
	    $output_string = ob_get_contents();
		ob_end_clean();
		if($checkout =='yes'){
			return $output_string;
		}else{
			echo $output_string;
		}
	}
}
function exwoofood_select_location_html($locations){
	if ( exwoofood_get_option('exwoofood_enable_loc') !='yes' ) {
		return;
	}
	global $loc_exits;
	$loc_selected = WC()->session->get( 'ex_userloc' );
	if($loc_selected!=''){
		return;
	}
	if(!isset($loc_exits) || $loc_exits!='on'){
		$loc_exits = 'on';
	}else if($loc_exits =='on'){
		return;
	}
	$atts = array();
	$atts['locations'] = $locations;
	?>
	<div class="ex-popup-location">
		<div class="ex-popup-content">
			<?php
			$icon = exwoofood_get_option('exwoofood_loc_icon');
			if($icon!=''){ ?>
				<div class="ex-pop-icon">
					<img src="<?php echo esc_url($icon);?>" alt="image">
				</div>
			<?php } ?>
			<div class="ex-popup-info">
				<h1><?php esc_html_e('Please choose area you want to order','woocommerce-food');?></h1>
				<?php echo exwoofood_select_loc_html($atts); ?>
			</div>
		</div>
	
	</div>
	<?php
}
add_action( 'init', 'exwoofood_user_select_location',20 );
function exwoofood_user_select_location(){
	if(is_admin()&& !defined( 'DOING_AJAX' ) || !isset(WC()->session) ){ return;}
	if(isset($_GET["loc"])){
		$term = term_exists( $_GET["loc"], 'exwoofood_loc' );
		if ( $term !== 0 && $term !== null ) {
			$ex_userloc = WC()->session->get( 'ex_userloc' );
			if($ex_userloc=='' || $ex_userloc != $_GET["loc"]){
			//if(!isset($_SESSION['ex_userloc']) || $_SESSION['ex_userloc'] != $_GET["loc"]){
				if ( exwoofood_get_option('exwoofood_enable_loc') =='yes' ) {
					global $woocommerce;
					$woocommerce->cart->empty_cart();
				}
				WC()->session->set( 'ex_userloc' , $_GET["loc"]);
				//$_SESSION['ex_userloc'] = $_GET["loc"];
				//session_write_close();
			}
		}else{
			WC()->session->set( 'ex_userloc' , '');
			//$_SESSION['ex_userloc'] = '';
			//session_write_close();
		}
	}
	if ( exwoofood_get_option('exwoofood_enable_loc') !='yes' ) {
		WC()->session->set( 'ex_userloc' , '');
		//$_SESSION['ex_userloc'] = '';
		//session_write_close();
	}
}

function exwoofood_location_field_html(){
	$args = array(
		'hide_empty'        => true,
		'parent'        => '0',
	);
	$terms = get_terms('exwoofood_loc', $args);
	ob_start();
	$loc_selected = WC()->session->get( 'ex_userloc' );
	$loc_current = '';
	/*
	if (isset($_SESSION['exfd_data_check']) && $_SESSION['exfd_data_check']!='') {
		$data_order = array();
		$data_order = $_SESSION['exfd_data_check'];
		$loc_current = isset($data_order['_location']) ? $data_order['_location'] : '';
	}*/
	?>
	<select class="ex-ck-select exfd-choice-locate" name="_location">
		<?php if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
			global $wp;
			if ( exwoofood_get_option('exwoofood_enable_loc') !='yes' ) {
	        	$select_option = '';
	        	$count_stop = 5;
	        	echo '<option disabled selected value>'.esc_html__( '-- Select --', 'woocommerce-food' ) .'</option>';
	        	foreach ( $terms as $term ) {
	        		$select_loc = '';
	        		if ($term->slug !='' && $term->slug == $loc_current) {
		                $select_loc = ' selected="selected"';
		              }
			  		echo '<option value="'. esc_attr($term->slug) .'" '.$select_loc.'>'. wp_kses_post($term->name) .'</option>';
			  		exfd_show_child_location('',$term,$count_stop,$loc_selected,'yes');
			  	}
			}else{
				$term = get_term_by('slug', $loc_selected, 'exwoofood_loc');
				echo '<option selected value="'.esc_attr( $loc_selected ).'">'.wp_kses_post($term->name).'</option>';
			}
        } //if have terms ?>
	</select>
	<?php
	$loca = ob_get_contents();
	ob_end_clean();
	return $loca;
}

add_action( 'wp_ajax_exwoofood_loadstore', 'ajax_exwoofood_loadstore' );
add_action( 'wp_ajax_nopriv_exwoofood_loadstore', 'ajax_exwoofood_loadstore' );
function ajax_exwoofood_loadstore(){
	
	$param_query = json_decode( stripslashes( $_POST['param_query'] ), true );
	$locate_param = '';
	$locate_param = sanitize_text_field($_POST['locate_param']);
	if ($locate_param == '') {
		return;
	}
	ob_start();
	$posts_array = get_posts(
        array(
            'post_status' => array( 'publish'),
            'post_type' => 'exwoofood_store',
            'tax_query' => array(
                array(
                    'taxonomy' => 'exwoofood_loc',
                    'field' => 'slug',
                    'terms' => $locate_param,
                )
            )
        )
    );
    
    $count =sizeof($posts_array);
    if ($count == 0) {
    	echo "0";
    }else{
	    echo '<label class="exfd-label">'.esc_html__("Select store","woocommerce-food").'</label>';
	    $number = 1;
	    $check='';
		foreach ( $posts_array as $it ) {
			if ($number == 1) {
				$check ='checked="checked"';
			}else{$check ='';}
			$number = $number + 1;
			echo '<label class="exfd-container"><p>'.wp_kses_post($it->post_title).'</p>
				<span>'.wpautop($it->post_content).'</span>
				<input class="exfd-choice-order" type="radio" name="_store" '.$check.' value="'.esc_attr($it->ID).'">
				<span class="exfd-checkmark"></span>
	        </label>';
		}
	}
	$html = ob_get_clean();
	$output =  array('html_content'=>$html);
	echo str_replace('\/', '/', json_encode($output));
	die;
}


if(!function_exists('exwoofood_pagenavi_no_ajax')){
	function exwoofood_pagenavi_no_ajax($the_query){
		if(function_exists('paginate_links')) {
			echo '<div class="exwoofood-no-ajax-pagination">';
			echo paginate_links( array(
				'base'         => esc_url_raw( str_replace( 999999999, '%#%', get_pagenum_link( 999999999, false ) ) ),
				'format'       => '',
				'add_args'     => false,
				'current' => max( 1, get_query_var('paged') ),
				'total' => $the_query->max_num_pages,
				'prev_text'    => '&larr;',
				'next_text'    => '&rarr;',
				'type'         => 'list',
				'end_size'     => 3,
				'mid_size'     => 3
			) );
			echo '</div>';
		}
	}
}

if(!function_exists('exwfd_get_current_time')){
	function exwfd_get_current_time(){
		$cure_time =  strtotime("now");
		$gmt_offset = get_option('gmt_offset');
		if($gmt_offset!=''){
			$cure_time = $cure_time + ($gmt_offset*3600);
		}
		return $cure_time;
	}
}
// get location selected
if(!function_exists('exwf_get_loc_selected')){
	function exwf_get_loc_selected(){
		$loc_selected = WC()->session->get( 'ex_userloc' );
		$user_log = '';
		if($loc_selected==''){
			$user_log = WC()->session->get( '_user_deli_log' );
			$loc_selected=  $user_log ;
		}
		if($loc_selected!=''){
			$term = get_term_by('slug', $loc_selected, 'exwoofood_loc');
			if(isset($term->term_id)){
				return $term->term_id;
			}
		}
		return;
	}
}
if(!function_exists('exwf_check_open_close_time')){
	function exwf_check_open_close_time($id_cr=false){
		$enable_time = exwoofood_get_option('exwoofood_open_close','exwoofood_advanced_options');
		if($enable_time=='closed'){
			return false;
		}
		$check_pr = false;
		if(isset($id_cr) && is_numeric($id_cr)){
			$al_products = exwoofood_get_option('exwoofood_ign_op','exwoofood_advanced_options');
			if($al_products!=''){
				$al_products = explode(",",$al_products);
				if(in_array($id_cr, $al_products)){
					$check_pr = true;
				}
			}
		}
		if ($enable_time == '' || $check_pr== true) {
			return true;
		}
		$cure_time =  exwfd_get_current_time();
		date_default_timezone_set('UTC');
		$hours_current= intval(date('H', $cure_time));
		$minutes_current = intval(date('i', $cure_time));
		// $times is time stamp start 00:00:00
		$times = $cure_time - $hours_current*3600 - $minutes_current*60;
		// New advanced open closing time
		$opcl_time = exwoofood_get_option('exwfood_'.date('D',$cure_time).'_opcl_time','exwoofood_advanced_options');
		// for each location
		$log_selected = exwf_get_loc_selected();
		if($log_selected!=''){
			$loc_opcls = exwoofood_get_option('exwoofood_open_close_loc','exwoofood_advanced_options');
			if($loc_opcls=='yes'){
				$opcl_time_log = get_term_meta( $log_selected, 'exwfood_'.date('D',$cure_time).'_opcl_time', true );
				$opcl_time = is_array($opcl_time_log) && !empty($opcl_time_log) ? $opcl_time_log : $opcl_time;
			}
		}
		if(is_array($opcl_time) && !empty($opcl_time)){
			$check= true;
			foreach ($opcl_time as $it_time) {
				$open_hours = isset($it_time['open-time']) ? intval(date('H', strtotime($it_time['open-time'])))*3600 + intval(date('i', strtotime($it_time['open-time'])))*60 : 0 ;
				$close_hours = isset($it_time['close-time']) ? intval(date('H', strtotime($it_time['close-time'])))*3600 + intval(date('i', strtotime($it_time['close-time'])))*60 : 0 ;
				if($close_hours < $open_hours){
					$close_hours = $close_hours + 86400;
				}
				$open_hours_unix = $times + $open_hours;
				$close_hours_unix = $times + $close_hours;
				if ($open_hours_unix > $close_hours_unix || $cure_time < $open_hours_unix || $cure_time > $close_hours_unix) {
					$check= false;
				}else{
					$check= true;
					break;
				}
			}
			return $check;
		}else{
			$open_hours = exwoofood_get_option('exwoofood_ck_open_hour','exwoofood_advanced_options');
			$close_hours = exwoofood_get_option('exwoofood_ck_close_hour','exwoofood_advanced_options');
			if ($open_hours == '' || $close_hours == '') {
				return false;
			}
			$open_hours_unix = $times + intval(date('H', strtotime($open_hours)))*3600 + intval(date('i', strtotime($open_hours)))*60;
			$close_hours_unix = $times + intval(date('H', strtotime($close_hours)))*3600 + intval(date('i', strtotime($close_hours)))*60;
			// echo date_i18n(get_option('time_format'), $times).' '.date_i18n(get_option('time_format'), $open_hours_unix).' '.date_i18n(get_option('time_format'), $close_hours_unix);exit();
			if ($open_hours_unix > $close_hours_unix || $cure_time < $open_hours_unix || $cure_time > $close_hours_unix) {
				return false;
			}
			return true;
		}
		return $check_pr;
	}
}
/*-- Get next open hour--*/
if(!function_exists('exwfd_get_next_open_close_time')){
	function exwfd_get_next_open_close_time(){
		$cure_time =  exwfd_get_current_time();
		date_default_timezone_set('UTC');
		$hours_current= intval(date('H', $cure_time));
		$minutes_current = intval(date('i', $cure_time));
		// $times is time stamp start 00:00:00
		$times = $cure_time - $hours_current*3600 - $minutes_current*60;
		$open_hours = $close_hours = '';
		for ($i=0; $i < 7; $i++) {
			$check= false;
			$timck = $cure_time + ($i * 86400);
			$opcl_time = exwoofood_get_option('exwfood_'.date('D',$timck).'_opcl_time','exwoofood_advanced_options');
			// for each location
			$log_selected = exwf_get_loc_selected();
			if($log_selected!=''){
				$loc_opcls = exwoofood_get_option('exwoofood_open_close_loc','exwoofood_advanced_options');
				if($loc_opcls=='yes'){
					$opcl_time_log = get_term_meta( $log_selected, 'exwfood_'.date('D',$cure_time).'_opcl_time', true );
					$opcl_time = is_array($opcl_time_log) && !empty($opcl_time_log) ? $opcl_time_log : $opcl_time;
				}
			}
			if(is_array($opcl_time) && !empty($opcl_time)){
				
				foreach ($opcl_time as $it_time) {
					$open_hours = $it_time['open-time'];
					$close_hours = $it_time['close-time'];
					if($i == 0){
						$open_hours_unix = $times + intval(date('H', strtotime($open_hours)))*3600 + intval(date('i', strtotime($open_hours)))*60;
						$close_hours_unix = $times + intval(date('H', strtotime($close_hours)))*3600 + intval(date('i', strtotime($close_hours)))*60;
						if ($open_hours_unix >  $cure_time ) {
							$check= true;
							break;
						}
					}else if($open_hours!=$close_hours){

						$check= true;
						break;
					}
				}
			}
			if($check==true){
				break;
			}	
		}
		if($check==true){
			if($i ==0){ $timck = '';}
			return array($open_hours,$close_hours,$timck);
		}
	}
}
/*---- Open closing time message----*/
if(!function_exists('exwfd_open_closing_message')){
	function exwfd_open_closing_message($rhtml=false){
		$enable_time = exwoofood_get_option('exwoofood_open_close','exwoofood_advanced_options');
		ob_start();
		$next_op = exwfd_get_next_open_close_time();
		if($enable_time=='closed'){
			$text = esc_html__( 'Ordering food is now closed','woocommerce-food');
		}else if(array($next_op) && !empty($next_op)){
			$fp = date_i18n(get_option('time_format'),strtotime($next_op[0]));
			$to = date_i18n(get_option('time_format'),strtotime($next_op[1]));
			$nday = isset($next_op[2]) && is_numeric($next_op[2]) ? date_i18n('l',$next_op[2]) : esc_html__( 'Today', 'woocommerce-food' );
			$text = sprintf( esc_html__( 'Ordering food is now closed, please come back from %1$s to %2$s %3$s', 'woocommerce-food' ), $fp, $to, $nday);
		}else{
			$text = esc_html__( 'Ordering food is now closed','woocommerce-food');
		}

		$text = apply_filters( 'exwfood_opcl_text', $text, $next_op );
		if(isset($rhtml) && $rhtml==true ){
			echo $text;
		}else{
			echo '<p class="exfd-out-notice">' .$text.'</p>';
		}
		$html = ob_get_contents();
		ob_end_clean();
		return $html;
	}
}

if(!function_exists('exfd_show_reviews')){
	function exfd_show_reviews($id_food,$product=false){
		if(!isset($product) || $product==''){
	    	$product = wc_get_product( $id_food );
	    }
		if(function_exists('wc_get_rating_html')){
			$rating_html = wc_get_rating_html($product->get_average_rating());
		}else{
			$rating_html = $product->get_rating_html();
		}
		$rating_count = $product->get_rating_count();
		if (  $rating_count > 0 && get_option( 'woocommerce_enable_review_rating' ) != 'no' && $rating_html){
				return  '<div class="exwf-rating woocommerce">'.$rating_html.'</div>';
		}
    }
}


function exwf_custom_color($sc,$style,$id){
	$color = get_post_meta( get_the_ID(), 'exwoofood_custom_color',true );
	if($color==''){ return;}
	?>
	<style type="text/css">
		<?php if($sc=='grid'){
			if($style=='1' || $style=='2'){
				?>
				.ex-fdlist #<?php echo esc_attr($id);?> figcaption .exbt-inline > a,
				#<?php echo esc_attr($id);?> .exwoofood-woocommerce.woocommerce form.cart button[type="submit"]{background:<?php echo esc_attr($color);?>;}
				#<?php echo esc_attr($id);?> figcaption h5{color:<?php echo esc_attr($color);?>;}
				<?php
			}else if( $style=='3'){?>
				#<?php echo esc_attr($id);?> figcaption h5{color:<?php echo esc_attr($color);?>;}
				<?php
			}else if($style=='4'){?>
				.ex-fdlist #<?php echo esc_attr($id);?> .exfd-icon-plus:before,
				.ex-fdlist #<?php echo esc_attr($id);?> .exfd-icon-plus:after,
				#<?php echo esc_attr($id);?> figcaption h5{background:<?php echo esc_attr($color);?>;}
				.ex-fdlist #<?php echo esc_attr($id);?> .exstyle-4-button.exfd-choice{border-color:<?php echo esc_attr($color);?>;}
				<?php
			}
		}else if($sc=='list' || $sc=='table'){ ?>
			.ex-fdlist #<?php echo esc_attr($id);?> .exfd-icon-plus:before,
			.ex-fdlist #<?php echo esc_attr($id);?> .exfd-icon-plus:after,
			#<?php echo esc_attr($id);?> figcaption h5{background:<?php echo esc_attr($color);?>;}
			.ex-fdlist #<?php echo esc_attr($id);?> .exfd-choice{border-color:<?php echo esc_attr($color);?>;}
			<?php
		}?>
	</style>
	<?php
}
// search html
function exwf_search_html($enable_search){
	if($enable_search!='yes'){ return;}
	?>
	<div class="exwf-search">
		<form role="search" method="get" class="exwf-search-form" action="<?php echo home_url(); ?>/">
		
			<input type="hidden" name="post_type" value="product" />
	      	<input type="text" value="<?php the_search_query(); ?>" name="s" id="s" placeholder="<?php echo  esc_html__('Type Keywords','woocommerce-food'); ?>" class="exwf-s-field" />
	      	<button type="submit" class="exwf-s-submit" ><img src="<?php echo EX_WOOFOOD_PATH.'css/img/search-outline.svg';?>" alt="image-cart"></button>
		</form>
	</div>	
	<?php
}
add_filter( 'exwo_accordion_style', 'exwoofood_extra_option_accordion_style', 10, 1 );
function exwoofood_extra_option_accordion_style( $style ) {
	if ( exwoofood_get_option('exwoofood_exoptions_style') =='accordion' ) {
	   	$style = true;
	}
    return $style;
}
//Add info to pdf invoice
if(!function_exists('exwo_add_info_to_invoice')){
	add_action( 'wpo_wcpdf_after_order_data', 'exwo_add_info_to_invoice', 10, 3 );
	function exwo_add_info_to_invoice ( $type, $order) {
		$dv_date = get_post_meta( $order->get_id(), 'exwfood_date_deli', true );
		$dv_time = get_post_meta( $order->get_id(), 'exwfood_time_deli', true );
		$loc_ar = get_post_meta( $order->get_id(), 'exwoofood_location', true );
		
		$text_datedel = exwf_date_time_text('date',$order);
		$text_timedel = exwf_date_time_text('time',$order);
		if($dv_date !=''){?>
		    <tr>
		    	<th><?php echo $text_datedel; ?></th>
		    	<td><?php echo $dv_date; ?></td>
		    </tr>
		<?php }
		if($dv_time !=''){
			?>
		    <tr>
		    	<th><?php echo $text_timedel; ?></th>
		    	<td><?php echo $dv_time; ?></td>
		    </tr>
		<?php }
		$log_name = get_term_by('slug', $loc_ar, 'exwoofood_loc');
		if($log_name->name){
			?>
		    <tr>
		    	<th><?php echo esc_html__( 'Location', 'woocommerce-food' ); ?></th>
		    	<td><?php echo $log_name->name; ?></td>
		    </tr>
		<?php }
	}
}
///
/*
function exwf_addoption_to_order_items( $item, $cart_item_key, $values, $order ) {

	//print_r($values);exit;
	if(isset($values['exoptions']) && !empty($values['exoptions'])){
		$title = get_the_title($values['product_id']);
		$option_name ='';
		foreach ($values['exoptions'] as $option) {
			$option_name .=  ' '.$option['name'].': '.$option['value'].'+'.wc_price($option['price']);
		}
		$title = $title.' -'.$option_name;
		$item->set_name($title);
	}

}
add_action( 'woocommerce_checkout_create_order_line_item', 'exwf_addoption_to_order_items', 10, 4 );
*/
function exwf_date_time_text($text,$order=false){
	$text_datedel = esc_html__('Delivery Date','woocommerce-food');
	$text_timedel = esc_html__('Delivery Time','woocommerce-food');
	if(isset($order) && is_object($order) && method_exists($order,'get_id')){
		$user_odmethod = get_post_meta( $order->get_id(), 'exwfood_order_method', true );
	}else{
		$user_odmethod = WC()->session->get( '_user_order_method' );
	}
	if($user_odmethod=='takeaway'){
		$text_datedel = esc_html__('Pickup Date','woocommerce-food');
		$text_timedel = esc_html__('Pickup Time','woocommerce-food');
	}
	if($user_odmethod=='dinein'){
		$text_datedel = esc_html__('Date','woocommerce-food');
		$text_timedel = esc_html__('Time','woocommerce-food');
	}
	if($text=='date'){
		return apply_filters('exwf_datedeli_text',$text_datedel);
	}else{
		return apply_filters('exwf_timedeli_text',$text_timedel);
	}
}
// change and using jquery to change text like above
//add_action('woocommerce_checkout_update_order_review', 'exwf_change_text_shipping_methods', 10, 1);
function exwf_change_text_shipping_methods( $post_data ) {
    parse_str($post_data, $get_array);
    $method = isset($get_array['shipping_method'][0]) ? $get_array['shipping_method'][0] : '';
    if($method=='flat_rate:3'){
    	WC()->session->set( '_user_order_method' , 'delivery');
    }else{
    	WC()->session->set( '_user_order_method' , 'takeaway');
    }
}

if(!function_exists('exwf_change_datelb_shipping_methods')){
	//add_filter('exwf_datedeli_text', 'exwf_change_datelb_shipping_methods', 10, 1);
	function exwf_change_datelb_shipping_methods( $text ) {
		if(is_admin()){
			global $pagenow;
			if (( $pagenow == 'post.php' ) || (get_post_type() == 'shop_order')) {
				$order = wc_get_order(get_the_ID());
				$method = @array_shift($order->get_shipping_methods());
				$method = isset($method['method_id']) ? $method['method_id'] : '';
			}
		}else{
			$method = WC()->session->get( 'chosen_shipping_methods' );
			$method = isset($method[0]) ? $method[0] : '';
		}
	    if (strpos($method, 'flat_rate') !== false) {
	    	$text = esc_html__('Delivery Date','woocommerce-food');
	    }else{
	    	$text = esc_html__('Pickup Date','woocommerce-food');
	    }
	    return $text;
	}
}

if(!function_exists('exwf_change_timelb_shipping_methods')){
	//add_filter('exwf_timedeli_text', 'exwf_change_timelb_shipping_methods', 10, 1);
	function exwf_change_timelb_shipping_methods( $text ) {
		if(is_admin()){
			global $pagenow;
			if (( $pagenow == 'post.php' ) || (get_post_type() == 'shop_order')) {
				$order = wc_get_order(get_the_ID());
				$method = @array_shift($order->get_shipping_methods());
				$method = isset($method['method_id']) ? $method['method_id'] : '';
			}
		}else{
			$method = WC()->session->get( 'chosen_shipping_methods' );
			$method = isset($method[0]) ? $method[0] : '';
		}
	    if (strpos($method, 'flat_rate') !== false) {
	    	$text = esc_html__('Delivery time','woocommerce-food');
	    }else{
	    	$text = esc_html__('Pickup time','woocommerce-food');
	    }
	    return $text;
	}
}

if(!function_exists('exwf_auto_update_label_script')){
	//add_action( 'wp_footer', 'exwf_auto_update_label_script', 999 );
	function exwf_auto_update_label_script() {
	    if (is_checkout()) :?>
	    <script>
	        jQuery( function( $ ) {
	            // woocommerce_params is required to continue, ensure the object exists
	            if ( typeof woocommerce_params === 'undefined' ) {
	                return false;
	            }
	            // Postkantoor shipping methods
	            //var show = ['flat_rate:3','flat_rate:7','flat_rate:8'];

	            $(document).on( 'change', '#shipping_method input[type="radio"]', function() {
	              // console.log($.inArray($(this).val(), show));
	              //if ($.inArray($(this).val(), show) > -1) {    // >-1 if found in array
	              	var $mth = $(this).val();
	              	var $rq = $rq_time = '';
					if($('#exwfood_date_deli_field').hasClass('validate-required')){
						$rq = '<abbr class="required" title="required">*</abbr>';
					}
					if($('#exwfood_time_deli_field').hasClass('validate-required')){
						$rq_time = '<abbr class="required" title="required">*</abbr>';
					}
					if($mth.indexOf("flat_rate") >= 0){
						$('#exwfood_date_deli_field label').html('<?php esc_html_e('Delivery Date','woocommerce-food') ?> '+$rq);
						$('#exwfood_time_deli_field label').html('<?php esc_html_e('Delivery Time','woocommerce-food') ?> '+$rq_time);
						//$('.billing-dynamic').removeClass('hide');
						// console.log('show');
					} else {
						$('#exwfood_date_deli_field label').html('<?php esc_html_e('Pickup Date','woocommerce-food') ?> '+$rq);
						$('#exwfood_time_deli_field label').html('<?php esc_html_e('Pickup Time','woocommerce-food') ?> '+$rq_time);
						//$('.billing-dynamic').addClass('hide');
						// console.log('hide');
					}
	            });

	        });
	    </script>
	    <?php
	    endif;
	}
}
function exwf_icon_color($id=false){
	if($id==false){
		$id = get_the_ID();
	}
	$icons = get_post_meta( $id, 'exwoofood_cticon_gr', true );
	if(is_array($icons) && !empty($icons)){
		echo '<span class="exwf-lbicons">';
		foreach ($icons as $icon) {
			$bg = isset($icon['bgcolor']) && $icon['bgcolor']!='' ? 'background-color:'.$icon['bgcolor'] : '';
			$img = isset($icon['icon']) && $icon['icon']!='' ? 'background-image:url('.esc_url($icon['icon']).')' : '';
			?>
			<span class="exwf-lbicon <?php echo $img=='' ? 'exwf-ep-ic' : '';?>" style="<?php echo $bg.';'.$img;?>"></span>
			<?php
		}
		echo '</span>';
	}
}

function exwf_if_check_product_notin_shipping(){
	$al_products = exwoofood_get_option('exwoofood_ign_deli','exwoofood_advanced_options');
	$al_cats = exwoofood_get_option('exwoofood_igncat_deli','exwoofood_advanced_options');
	$check_ex = true;
	if($al_products!='' || (is_array($al_cats) && !empty($al_cats))){
		$check_ex = false;
		$al_products = $al_products!='' ? explode(",",$al_products) : array();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$id_cr = $cart_item['product_id'];
			if(is_array($al_cats) && !empty($al_cats)){
				if(!in_array($id_cr, $al_products) && !has_term( $al_cats, 'product_cat', $id_cr ) ){
					$check_ex = true;
					break;
				}
			}else{
				if(!empty($al_products) && !in_array($id_cr, $al_products)){
					$check_ex = true;
					break;
				}
			}
		}
	}
	return $check_ex;
}
function exwf_if_check_product_in_shipping(){
	$al_products = exwoofood_get_option('exwoofood_ign_deli','exwoofood_advanced_options');
	$al_cats = exwoofood_get_option('exwoofood_igncat_deli','exwoofood_advanced_options');
	$check_ex = true;
	if($al_products!='' || (is_array($al_cats) && !empty($al_cats)) ){
		$check_ex = false;
		$al_products = $al_products!='' ? explode(",",$al_products) : array();
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$id_cr = $cart_item['product_id'];
			if(is_array($al_cats) && !empty($al_cats)){
				if(in_array($id_cr, $al_products) || has_term( $al_cats, 'product_cat', $id_cr ) ){
					$check_ex = true;
					break;
				}
			}else{
				if( !empty($al_products) &&  in_array($id_cr, $al_products)){
					$check_ex = true;
					break;
				}
			}
		}
	}else{
		$check_ex = false;
	}
	return $check_ex;
}