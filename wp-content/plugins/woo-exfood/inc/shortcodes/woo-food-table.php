<?php
function exwoofood_shortcode_table( $atts ) {
	if(phpversion()>=7){
		$atts = (array)$atts;
	}
	if(is_admin() || (defined('REST_REQUEST') && REST_REQUEST)){ return;}
	global $ID, $count, $posts_per_page,$number_excerpt,$img_size;
	$ID = isset($atts['ID']) ? $atts['ID'] : rand(10,9999);
	if(!isset($atts['ID'])){
		$atts['ID']= $ID;
	}
	$style = isset($atts['style']) ? $atts['style'] : '1';
	$posttype   =  'ex_food';
	$ids   = isset($atts['ids']) ? str_replace(' ', '', $atts['ids']) : '';
	$taxonomy  = isset($atts['taxonomy']) ? $atts['taxonomy'] : '';
	$cat   = isset($atts['cat']) ? str_replace(' ', '', $atts['cat']) : '';
	$order_cat   = isset($atts['order_cat']) ? $atts['order_cat'] : '';
	$tag  = isset($atts['tag']) ? $atts['tag'] : '';
	$count   = isset($atts['count']) &&  $atts['count'] !=''? $atts['count'] : '9';
	$posts_per_page   = isset($atts['posts_per_page']) && $atts['posts_per_page'] !=''? $atts['posts_per_page'] : '3';
	$order  = isset($atts['order']) ? $atts['order'] : '';
	$orderby  = isset($atts['orderby']) ? $atts['orderby'] : '';
	$meta_key  = isset($atts['meta_key']) ? $atts['meta_key'] : '';
	$meta_value  = isset($atts['meta_value']) ? $atts['meta_value'] : '';
	$class  = isset($atts['class']) ? $atts['class'] : '';
	$page_navi  = isset($atts['page_navi']) ? $atts['page_navi'] : '';
	$live_sort =  isset($atts['live_sort']) ? $atts['live_sort'] :'';
	$menu_filter   = isset($atts['menu_filter']) ? $atts['menu_filter'] : 'hide';
	$active_filter   = isset($atts['active_filter']) ? $atts['active_filter'] : '';
	$cart_enable  = isset($atts['cart_enable']) ? $atts['cart_enable'] : '';
	$enable_modal = isset($atts['enable_modal']) ? $atts['enable_modal'] : '';
	$featured =  isset($atts['featured']) ? $atts['featured'] :'';
	$paged = get_query_var('paged')?get_query_var('paged'):(get_query_var('page')?get_query_var('page'):1);
	$number_excerpt =  isset($atts['number_excerpt'])&& $atts['number_excerpt']!='' ? $atts['number_excerpt'] : '10';
	$filter_style =  isset($atts['filter_style']) ? $atts['filter_style'] :'';
	$hide_ftall =  isset($atts['hide_ftall']) ? $atts['hide_ftall'] :'';
	$enable_search =  isset($atts['enable_search']) ? $atts['enable_search'] :'';
	$locations =  isset($atts['locations']) ? $atts['locations'] :'';
	$img_size =  isset($atts['img_size']) ? $atts['img_size'] :'';
	if($hide_ftall=='yes' && $active_filter ==''){
		if($cat!=''){
			$cats = explode(",",$cat);
			$active_filter = $cats[0];
		}else{
			$args_ca = array('hide_empty'=> true,'parent'=> '0');
			if ($order_cat == 'yes') {
				$args_ca['meta_key'] = 'exwoofood_menu_order';
				$args_ca['orderby'] = 'meta_value_num';
			}
			$terms = get_terms('product_cat', $args_ca);
			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
				foreach ( $terms as $term ) {
					$active_filter = $term->slug;
					break;
				}
			}
		}
	}
	// remove space
	$cat = preg_replace('/\s+/', '', $cat);
	$ids = preg_replace('/\s+/', '', $ids);
	if(isset($_GET['menu']) && $_GET['menu']!=''){
		$active_filter = $_GET['menu'];
	}
	
	$args = exwoofood_query($posttype, $posts_per_page, $order, $orderby, $cat, $tag, $taxonomy, $meta_key, $ids, $meta_value,'','',$active_filter,$featured);
	global $the_query;
	$the_query = new WP_Query( $args );
	ob_start();
	global $html_modal;
	$html_modal ='';
	$ID = 'table-'.$ID;
	$class = $class." ex-food-plug ";
	if (!exwf_check_open_close_time()) {
		//$class = $class." exfd-out-open-time";
	}
	if($enable_modal=='no'){
		$class = $class." exfdisable-modal";
	}
	wp_enqueue_style( 'ionicon' );
	//$locations ='';
	do_action( 'exwoofood_before_shortcode');
	wp_enqueue_script( 'wc-add-to-cart-variation' );
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if (is_plugin_active( 'woocommerce-product-addons/woocommerce-product-addons.php' ) ) {
		$GLOBALS['Product_Addon_Display']->addon_scripts();
	}
	?>
	<div class="ex-fdlist table-layout <?php echo esc_attr($class); if($live_sort=='1'){ echo ' table-lv-sort';}?>" id ="<?php echo esc_attr($ID);?>">
        <?php 
        do_action('exwf_before_shortcode_content',$atts);
        exwf_search_html($enable_search); 
        exwoofood_select_location_html($locations);
        if($menu_filter=="show") {exwoofood_search_form_html($cat,$order_cat,'',$active_filter,$filter_style,$hide_ftall);}?>
        <?php if($cart_enable !='no') {
	        global $excart_html;
        	if($excart_html != 'on' || $cart_enable =='yes'){
        		$excart_html = 'on';
	        	exwoofood_woo_cart_icon_html($cart_enable);
	        }
		}; 
        if($active_filter!=''){
        	
			$term = get_term_by('slug', $active_filter, 'product_cat');
			if($term->description!=''){
				echo '<p class="exwf-dcat" style="display:block;">'.$term->description.'</p>';
			}
		}
		if(function_exists('exwf_select_date_html')){exwf_select_date_html();}   
        ?>
		<div class="ctlist">
		<?php if($live_sort=='1'){
			?>
			<script type="text/javascript">
				jQuery(document).ready(function ($) {
					if(!jQuery.fn.sortElements){
						jQuery.fn.sortElements = (function(){
							var sort = [].sort;
							return function(comparator, getSortable) {
								getSortable = getSortable || function(){return this;};
								var placements = this.map(function(){
									var sortElement = getSortable.call(this),
										parentNode = sortElement.parentNode,
										nextSibling = parentNode.insertBefore(
											document.createTextNode(''),
											sortElement.nextSibling
										);
									return function() {
										if (parentNode === this) {
											throw new Error(
												"You can't sort elements if any one is a descendant of another."
											);
										}
										parentNode.insertBefore(this, nextSibling);
										parentNode.removeChild(nextSibling);
									};
								});
								return sort.call(this, comparator).each(function(i){
									placements[i].call(getSortable.call(this));
								});
								
							};
						})();							
					}
					var table = $('#<?php echo esc_attr($ID);?>');
					$('#<?php echo esc_attr($ID);?> .exp-sort')
						.each(function(){
							var th = $(this),
								thIndex = th.index(),
								inverse = false;
							th.on('click', function(){
								$('#<?php echo esc_attr($ID);?> th').removeClass('s-descending');
								$('#<?php echo esc_attr($ID);?> th').removeClass('s-ascending');
								if(inverse == true){
									$(this).addClass('s-descending');
									$(this).removeClass('s-ascending');
								}else{
									$(this).removeClass('s-descending');
									$(this).addClass('s-ascending');
								}
								table.find('td').filter(function(){
									return $(this).index() === thIndex;
								}).sortElements(function(a, b){
									return $(a).data('sort') > $(b).data('sort') ?
										inverse ? -1 : 1
										: inverse ? 1 : -1;
								}, function(){
									// parentNode is the element we want to move
									return this.parentNode; 
								});
								inverse = !inverse;
							});
					});
				});
			</script>
		<?php }?>
		
        <table class="exfd-table-<?php echo esc_attr($style); ?> <?php if($number_excerpt != '0') echo "exfd-non-showdes"?>">
            <?php if($style==1){?>
            <thead>
                <tr>
                    <th><?php echo esc_html__('Image','woocommerce-food');?></th>
                    <th class="exp-sort ex-fd-name">
                    	<span class ="exfd-hide-screen"><?php echo esc_html__('Name','woocommerce-food');?></span>
                    	<span class="exfd-hide-mb  ex-detail"><?php echo esc_html__('Detail','woocommerce-food');?>
                    </th>
                    <?php if($number_excerpt != '0'){?>
                    <th class="exfd-hide-screen ex-fd-table-des"><?php echo esc_html__('Description','woocommerce-food');?></th>
                    <?php }?>
                    <th class="exp-sort exfd-hide-screen exfd-hide-tablet ex-fd-category"><?php echo esc_html__('Menu','woocommerce-food');?></th>
                    
                    <th class="exp-sort exfd-hide-screen exfd-price"><?php echo esc_html__('Price','woocommerce-food');?></th>
                    <th class="ex-fd-table-order"></th>
                </tr>
            </thead>
            <?php }?>
            <tbody>
                <?php
                $num_pg = '';
				$arr_ids = array();
                if ($the_query->have_posts()){ 
				$i=0;
				$it = $the_query->found_posts;
				if($it < $count || $count=='-1'){ $count = $it;}
				if($count  > $posts_per_page){
					$num_pg = ceil($count/$posts_per_page);
					$it_ep  = $count%$posts_per_page;
				}else{
					$num_pg = 1;
				}
				$arr_ids = array();
                while ($the_query->have_posts()) { $the_query->the_post();
					$arr_ids[] = get_the_ID();
					$i++;
					if(($num_pg == $paged) && $num_pg!='1'){
						if($i > $it_ep){ break;}
					}
                    exwoofood_template_plugin('table-'.$style,1);?>
                    <?php
                  }
                } ?>
            </tbody>
        </table>
           
		</div>
		<?php global $modal_html;
		if(!isset($modal_html) || $modal_html!='on' || $enable_modal=='yes'){
			$modal_html = 'on';
			echo "<div id='food_modal' class='ex_modal'></div>";
		}?>
        <?php
		if($page_navi=='loadmore'){
			exwoofood_ajax_navigate_html($ID,$atts,$num_pg,$args,$arr_ids); 
		}else{ ?>
			<div class="exfd-pagination-parent">
			<?php exwoofood_page_number_html($the_query,$ID,$atts,$num_pg,$args,$arr_ids);?>
			</div>
		<?php }
		?>
	</div>
	
	<?php
	wp_reset_postdata();
	$output_string = ob_get_contents();
	ob_end_clean();
	return $output_string;
}
add_shortcode( 'ex_wf_table', 'exwoofood_shortcode_table' );
add_action( 'after_setup_theme', 'ex_reg_wf_table_vc' );
function ex_reg_wf_table_vc(){
    if(function_exists('vc_map')){
	vc_map( array(
	   "name" => esc_html__("Food Table", "woocommerce-food"),
	   "base" => "ex_wf_table",
	   "class" => "",
	   "icon" => "icon-grid",
	   "controls" => "full",
	   "category" => esc_html__('Woocommerce Food','woocommerce-food'),
	   "params" => array(
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
			"heading" => esc_html__("Food per page", "woocommerce-food"),
			"param_name" => "posts_per_page",
			"value" => "",
			"description" => esc_html__("Number food per page", 'woocommerce-food'),
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
			 "heading" => esc_html__("Page navi", 'woocommerce-food'),
			 "param_name" => "page_navi",
			 "value" => array(
			 	esc_html__('Number', 'woocommerce-food') => '',
				esc_html__('Load more', 'woocommerce-food') => 'loadmore',
			 ),
			 "description" => esc_html__("Select type of page navigation", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Live Sort", 'woocommerce-food'),
			 "param_name" => "live_sort",
			 "value" => array(
			 	esc_html__('No', 'woocommerce-food') => '',
				esc_html__('Yes', 'woocommerce-food') => '1',
			 ),
			 "description" => esc_html__("Enable Live Sort", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Menu filter", 'woocommerce-food'),
			 "param_name" => "menu_filter",
			 "value" => array(
			 	esc_html__('Hide', 'woocommerce-food') => 'hide',
			 	esc_html__('Show', 'woocommerce-food') => 'show',
			 ),
			 "description" => esc_html__("Select show or hide Menu filter", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			 "type" => "dropdown",
			 "class" => "",
			 "heading" => esc_html__("Menu filter style", 'woocommerce-food'),
			 "param_name" => "filter_style",
			 "value" => array(
			 	esc_html__('Default', 'woocommerce-food') => '',
			 	esc_html__('Icon', 'woocommerce-food') => 'icon',
			 ),
			 "description" => esc_html__("Select Menu filter style", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "textfield",
			"heading" => esc_html__("Active filter", "woocommerce-food"),
			"param_name" => "active_filter",
			"value" => "",
			"description" => esc_html__("Enter slug of menu to active", "woocommerce-food"),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "dropdown",
			"heading" => esc_html__("Order Menu Filter", "woocommerce-food"),
			"param_name" => "order_cat",
			"description" => esc_html__("Order Menu with custom order", "woocommerce-food"),
			"value" => array(
			 	esc_html__('No', 'woocommerce-food') => '',
				esc_html__('Yes', 'woocommerce-food') => 'yes',
			 ),
		  ),
		  array(
		  	"admin_label" => true,
			"type" => "dropdown",
			"heading" => esc_html__("Hide 'All' Filter", "woocommerce-food"),
			"param_name" => "hide_ftall",
			"description" => esc_html__("Select 'yes' to disalbe 'All' filter", "woocommerce-food"),
			"value" => array(
			 	esc_html__('No', 'woocommerce-food') => '',
				esc_html__('Yes', 'woocommerce-food') => 'yes',
			 ),
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
			 "heading" => esc_html__("Enable Live search", 'woocommerce-food'),
			 "param_name" => "enable_search",
			 "value" => array(
			 	esc_html__('No', 'woocommerce-food') => '',
			 	esc_html__('Yes', 'woocommerce-food') => 'yes',
			 ),
			 "description" => esc_html__("Enable ajax live search", "woocommerce-food"),
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
