<?php
class exwoofood_SC_Builder {
	public function __construct(){
        add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'cmb2_admin_init', array($this,'register_metabox') );
		add_action( 'save_post', array($this,'save_shortcode'),1 );
		add_shortcode( 'exwfsc', array($this,'run_extpsc') );
    }
	function run_extpsc($atts, $content){
		$id = isset($atts['id']) ? $atts['id'] : '';
		$sc = get_post_meta( $id, '_tpsc', true );
		if($id=='' || $sc==''){ return;}
		return do_shortcode($sc);
	}
	function save_shortcode($post_id){
		if('exwoofood_scbd' != get_post_type()){ return;}
		if(isset($_POST['sc_type'])){
			$style = isset($_POST['style']) ? $_POST['style'] : 1;
			$column = isset($_POST['column']) ? $_POST['column'] : 3;
			$count = isset($_POST['count']) && $_POST['count'] !=''? $_POST['count'] : '9';
			$posts_per_page = isset($_POST['posts_per_page']) ? $_POST['posts_per_page'] : '';
			$slidesshow = isset($_POST['slidesshow']) ? $_POST['slidesshow'] : '';
			$ids = isset($_POST['ids']) ? $_POST['ids'] : '';
			$cat = isset($_POST['cat']) ? $_POST['cat'] : '';
			$order_cat = isset($_POST['order_cat']) ? $_POST['order_cat'] : '';
			$order = isset($_POST['order']) ? $_POST['order'] : '';
			$orderby = isset($_POST['orderby']) ? $_POST['orderby'] : '';
			$meta_key = isset($_POST['meta_key']) ? $_POST['meta_key'] : '';
			$meta_value = isset($_POST['meta_value']) ? $_POST['meta_value'] : '';
			$number_excerpt = isset($_POST['number_excerpt']) ? $_POST['number_excerpt'] : '';
			$page_navi = isset($_POST['page_navi']) ? $_POST['page_navi'] : '';
			$cart_enable = isset($_POST['cart_enable']) ? $_POST['cart_enable'] : '';
			$enable_search = isset($_POST['enable_search']) ? $_POST['enable_search'] : '';
			$menu_filter = isset($_POST['menu_filter']) ? $_POST['menu_filter'] : '';
			$active_filter = isset($_POST['active_filter']) ? $_POST['active_filter'] : '';
			$menu_pos = isset($_POST['menu_pos']) ? $_POST['menu_pos'] : '';
			$enable_modal = isset($_POST['enable_modal']) ? $_POST['enable_modal'] : '';
			$featured = isset($_POST['featured']) ? $_POST['featured'] : '';
			$live_sort = isset($_POST['live_sort']) ? $_POST['live_sort'] : '';
			$autoplay = isset($_POST['autoplay']) ? $_POST['autoplay'] : '';
			$autoplayspeed = isset($_POST['autoplayspeed']) ? $_POST['autoplayspeed'] : '';
			$loading_effect = isset($_POST['loading_effect']) ? $_POST['loading_effect'] : '';
			$infinite = isset($_POST['infinite']) ? $_POST['infinite'] : '';
			$filter_style = isset($_POST['filter_style']) ? $_POST['filter_style'] : '';
			$hide_ftall = isset($_POST['hide_ftall']) ? $_POST['hide_ftall'] : '';
			$img_size = isset($_POST['img_size']) ? $_POST['img_size'] : '';
			$class = isset($_POST['class']) ? $_POST['class'] : '';

			if($_POST['sc_type'] == 'grid'){
				
				$sc = '[ex_wf_grid style="'.esc_attr($style).'" column="'.esc_attr($column).'" count="'.esc_attr($count).'" posts_per_page="'.esc_attr($posts_per_page).'" ids="'.esc_attr($ids).'" cat="'.esc_attr($cat).'" order="'.esc_attr($order).'" orderby="'.esc_attr($orderby).'" meta_key="'.esc_attr($meta_key).'" meta_value="'.esc_attr($meta_value).'" number_excerpt="'.esc_attr($number_excerpt).'" cart_enable="'.esc_attr($cart_enable).'" enable_search="'.esc_attr($enable_search).'" enable_modal="'.esc_attr($enable_modal).'" menu_filter="'.esc_attr($menu_filter).'" filter_style="'.esc_attr($filter_style).'" hide_ftall="'.esc_attr($hide_ftall).'" active_filter="'.esc_attr($active_filter).'" order_cat="'.esc_attr($order_cat).'" page_navi="'.esc_attr($page_navi).'" featured="'.esc_attr($featured).'" img_size="'.esc_attr($img_size).'" class="'.esc_attr($class).'"]';
				
			}elseif($_POST['sc_type'] == 'list'){
				$sc = '[ex_wf_list style="'.esc_attr($style).'" count="'.esc_attr($count).'" posts_per_page="'.esc_attr($posts_per_page).'" ids="'.esc_attr($ids).'" cat="'.esc_attr($cat).'" order="'.esc_attr($order).'" orderby="'.esc_attr($orderby).'" meta_key="'.esc_attr($meta_key).'" meta_value="'.esc_attr($meta_value).'" number_excerpt="'.esc_attr($number_excerpt).'" cart_enable="'.esc_attr($cart_enable).'" enable_search="'.esc_attr($enable_search).'" enable_modal="'.esc_attr($enable_modal).'" menu_filter="'.esc_attr($menu_filter).'" filter_style="'.esc_attr($filter_style).'" hide_ftall="'.esc_attr($hide_ftall).'" active_filter="'.esc_attr($active_filter).'" order_cat="'.esc_attr($order_cat).'" menu_pos="'.esc_attr($menu_pos).'"  page_navi="'.esc_attr($page_navi).'" featured="'.esc_attr($featured).'" img_size="'.esc_attr($img_size).'" class="'.esc_attr($class).'"]';
				
			}elseif($_POST['sc_type'] == 'table'){
				
				$sc = '[ex_wf_table style="'.esc_attr($style).'" count="'.esc_attr($count).'" posts_per_page="'.esc_attr($posts_per_page).'" ids="'.esc_attr($ids).'" cat="'.esc_attr($cat).'" order="'.esc_attr($order).'" orderby="'.esc_attr($orderby).'" meta_key="'.esc_attr($meta_key).'" meta_value="'.esc_attr($meta_value).'" number_excerpt="'.esc_attr($number_excerpt).'" cart_enable="'.esc_attr($cart_enable).'" enable_search="'.esc_attr($enable_search).'" enable_modal="'.esc_attr($enable_modal).'" menu_filter="'.esc_attr($menu_filter).'" filter_style="'.esc_attr($filter_style).'" hide_ftall="'.esc_attr($hide_ftall).'" active_filter="'.esc_attr($active_filter).'" order_cat="'.esc_attr($order_cat).'" live_sort="'.esc_attr($live_sort).'"  page_navi="'.esc_attr($page_navi).'" featured="'.esc_attr($featured).'" img_size="'.esc_attr($img_size).'" class="'.esc_attr($class).'"]';
				
			}else{
				
				$sc = '[ex_wf_carousel style="'.esc_attr($style).'" count="'.esc_attr($count).'" slidesshow="'.esc_attr($slidesshow).'" ids="'.esc_attr($ids).'" cat="'.esc_attr($cat).'" order="'.esc_attr($order).'" orderby="'.esc_attr($orderby).'" meta_key="'.esc_attr($meta_key).'" meta_value="'.esc_attr($meta_value).'" number_excerpt="'.esc_attr($number_excerpt).'"  autoplay="'.esc_attr($autoplay).'" cart_enable="'.esc_attr($cart_enable).'" enable_modal="'.esc_attr($enable_modal).'" autoplayspeed="'.esc_attr($autoplayspeed).'" loading_effect="'.esc_attr($loading_effect).'" infinite="'.esc_attr($infinite).'" featured="'.esc_attr($featured).'" img_size="'.esc_attr($img_size).'" class="'.esc_attr($class).'"]';
				
			}
			if($sc!=''){
				update_post_meta( $post_id, '_tpsc', $sc );
			}
			update_post_meta( $post_id, '_shortcode', '[exwfsc id="'.$post_id.'"]' );
		}
	}
	function register_post_type(){
		$labels = array(
			'name'               => esc_html__('Shortcodes','woocommerce-food'),
			'singular_name'      => esc_html__('Shortcodes','woocommerce-food'),
			'add_new'            => esc_html__('Add New Shortcodes','woocommerce-food'),
			'add_new_item'       => esc_html__('Add New Shortcodes','woocommerce-food'),
			'edit_item'          => esc_html__('Edit Shortcodes','woocommerce-food'),
			'new_item'           => esc_html__('New Shortcode','woocommerce-food'),
			'all_items'          => esc_html__('Shortcodes builder','woocommerce-food'),
			'view_item'          => esc_html__('View Shortcodes','woocommerce-food'),
			'search_items'       => esc_html__('Search Shortcodes','woocommerce-food'),
			'not_found'          => esc_html__('No Shortcode found','woocommerce-food'),
			'not_found_in_trash' => esc_html__('No Shortcode found in Trash','woocommerce-food'),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Shortcodes','woocommerce-food')
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
		register_post_type('exwoofood_scbd',$args);  
	}
	
	function register_metabox() {
		/**
		 * Sample metabox to demonstrate each field type included
		 */
		$layout = new_cmb2_box( array(
			'id'            => 'exwf_sc',
			'title'         => esc_html__( 'Shortcode type', 'woocommerce-food' ),
			'object_types'  => array( 'exwoofood_scbd' ), // Post type
		) );
	
		$layout->add_field( array(
			'name'             => esc_html__( 'Type', 'woocommerce-food' ),
			'desc'             => esc_html__( 'Select type of shortcode', 'woocommerce-food' ),
			'id'               => 'sc_type',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'grid',
			'options'          => array(
				'grid' => esc_html__( 'Grid', 'woocommerce-food' ),
				'table'   => esc_html__( 'Table', 'woocommerce-food' ),
				'list'   => esc_html__( 'List', 'woocommerce-food' ),
				'carousel'     => esc_html__( 'Carousel', 'woocommerce-food' ),
			),
		) );
		if(isset($_GET['post']) && is_numeric($_GET['post'])){
			$layout->add_field( array(
				'name'       => esc_html__( 'Shortcode', 'woocommerce-food' ),
				'desc'       => esc_html__( 'Copy this shortcode and paste it into your post, page, or text widget content:', 'woocommerce-food' ),
				'id'         => '_shortcode',
				'type'       => 'text',
				'classes'             => '',
				'attributes'  => array(
					'readonly' => 'readonly',
				),
			) );
		}
		$sc_option = new_cmb2_box( array(
			'id'            => 'scwf_option',
			'title'         => esc_html__( 'Shortcode Option', 'woocommerce-food' ),
			'object_types'  => array( 'exwoofood_scbd' ),
		) );
		
		$sc_option->add_field( array(
			'name'             => esc_html__( 'Style', 'woocommerce-food' ),
			'desc'             => esc_html__( 'Select style of shortcode', 'woocommerce-food' ),
			'id'               => 'style',
			'type'             => 'select',
			'classes'             => 'column-2',
			'show_option_none' => false,
			'default'          => '1',
			'options'          => array(
				'1' => esc_html__('1', 'woocommerce-food'),
				'2' => esc_html__('2', 'woocommerce-food'),
				'3' => esc_html__('3', 'woocommerce-food'),
				'4' => esc_html__('4', 'woocommerce-food'),
			),
		) );
		
		$sc_option->add_field( array(
			'name'             => esc_html__( 'Columns', 'woocommerce-food' ),
			'desc'             => esc_html__( 'Select Columns of shortcode', 'woocommerce-food' ),
			'id'               => 'column',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel hide-intable hide-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '3',
			'options'          => array(
				'2' => esc_html__( '2 columns', 'woocommerce-food' ),
				'3'   => esc_html__( '3 columns', 'woocommerce-food' ),
				'4'   => esc_html__( '4 columns', 'woocommerce-food' ),
				'5'     => esc_html__( '5 columns', 'woocommerce-food' ),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Count', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Number of posts', 'woocommerce-food' ),
			'id'         => 'count',
			'type'       => 'text',
			'classes'             => 'column-2',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Posts per page', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Number items per page', 'woocommerce-food' ),
			'id'         => 'posts_per_page',
			'type'       => 'text',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Number items visible', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enter number', 'woocommerce-food' ),
			'id'         => 'slidesshow',
			'type'       => 'text',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'IDs', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Specify post IDs to retrieve', 'woocommerce-food' ),
			'id'         => 'ids',
			'type'       => 'text',
			'classes'             => 'column-2',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Menu', 'woocommerce-food' ),
			'desc'       => esc_html__( 'List of cat ID (or slug), separated by a comma', 'woocommerce-food' ),
			'id'         => 'cat',
			'type'       => 'text',
		) );

		$sc_option->add_field( array(
			'name'       => esc_html__( 'Order', 'woocommerce-food' ),
			'desc'       => '',
			'id'         => 'order',
			'type'             => 'select',
			'classes'             => 'column-2',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'DESC' => esc_html__('DESC', 'woocommerce-food'),
				'ASC'   => esc_html__('ASC', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Order by', 'woocommerce-food' ),
			'desc'       => '',
			'id'         => 'orderby',
			'type'             => 'select',
			'classes'             => 'column-2',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'date' => esc_html__('Date', 'woocommerce-food'),
				'order_field' => esc_html__('Custom order field', 'woocommerce-food'),
				'sale' => esc_html__('Sale', 'woocommerce-food'),
				'ID'   => esc_html__('ID', 'woocommerce-food'),
				'author' => esc_html__('Author', 'woocommerce-food'),
				'title'   => esc_html__('Title', 'woocommerce-food'),
				'name' => esc_html__('Name', 'woocommerce-food'),
				'modified'   => esc_html__('Modified', 'woocommerce-food'),
				'parent' => esc_html__('Parent', 'woocommerce-food'),
				'rand'   => esc_html__('Rand', 'woocommerce-food'),
				'menu_order' => esc_html__('Menu order', 'woocommerce-food'),
				'meta_value'   => esc_html__('Meta value', 'woocommerce-food'),
				'meta_value_num' => esc_html__('Meta value num', 'woocommerce-food'),
				'post__in'   => esc_html__('Post__in', 'woocommerce-food'),
				'None'   => esc_html__('None', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Meta key', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enter meta key to query', 'woocommerce-food' ),
			'id'         => 'meta_key',
			'type'       => 'text',
			'classes'             => 'column-2',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Meta value', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enter meta value to query', 'woocommerce-food' ),
			'id'         => 'meta_value',
			'type'       => 'text',
			'classes'             => 'column-2',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Number of Excerpt', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enter number', 'woocommerce-food' ),
			'id'         => 'number_excerpt',
			'type'       => 'text',
			'classes'             => 'column-2',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Page navi', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select type of page navigation', 'woocommerce-food' ),
			'id'         => 'page_navi',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('Number', 'woocommerce-food'),
				'loadmore'   => esc_html__('Load more', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Menu filter', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select show or hide menu filter bar', 'woocommerce-food' ),
			'id'         => 'menu_filter',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'hide' => esc_html__('Hide', 'woocommerce-food'),
				'show'   => esc_html__('Show', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Menu filter style', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select Menu filter style', 'woocommerce-food' ),
			'id'         => 'filter_style',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('Default', 'woocommerce-food'),
				'icon'   => esc_html__('Icon', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Active filter', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enter slug of menu to active', 'woocommerce-food' ),
			'id'         => 'active_filter',
			'type'       => 'text',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Order Menu Filter', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Order Menu Filter with custom order', 'woocommerce-food' ),
			'id'         => 'order_cat',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'woocommerce-food'),
				'yes'   => esc_html__('Yes', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( "Hide 'All' Filter", 'woocommerce-food' ),
			'desc'       => esc_html__( "Select 'yes' to disalbe 'All' filter", 'woocommerce-food' ),
			'id'         => 'hide_ftall',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel show-intable show-inlist show-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'woocommerce-food'),
				'yes'   => esc_html__('Yes', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Menu filter Position', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select posstion of menu filter', 'woocommerce-food' ),
			'id'         => 'menu_pos',
			'type'             => 'select',
			'classes'             => 'column-2 hide-incarousel hide-intable show-inlist hide-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'top' => esc_html__('Top', 'woocommerce-food'),
				'left'   => esc_html__('Left', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Show Side cart', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select show or hide side cart', 'woocommerce-food' ),
			'id'         => 'cart_enable',
			'type'             => 'select',
			'classes'             => 'column-3',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('Default', 'woocommerce-food'),
				'yes' => esc_html__('Show', 'woocommerce-food'),
				'no'   => esc_html__('Hide', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Enable Ajax Search', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select yes to enable ajax search feature', 'woocommerce-food' ),
			'id'         => 'enable_search',
			'type'             => 'select',
			'classes'             => 'column-3 hide-incarousel',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'woocommerce-food'),
				'yes'   => esc_html__('Yes', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Live Sort', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enable Live Sort', 'woocommerce-food' ),
			'id'         => 'live_sort',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'woocommerce-food'),
				'1'   => esc_html__('Yes', 'woocommerce-food'),
			),
			'classes'             => 'column-3 hide-incarousel show-intable hide-inlist hide-ingrid',
		) );
		
		
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Autoplay', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enable Autoplay', 'woocommerce-food' ),
			'id'         => 'autoplay',
			'type'             => 'select',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'woocommerce-food'),
				'1'   => esc_html__('Yes', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Autoplay Speed', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Autoplay Speed in milliseconds. Default:3000', 'woocommerce-food' ),
			'id'         => 'autoplayspeed',
			'type'             => 'text',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Loading effect', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enable Loading effect', 'woocommerce-food' ),
			'id'         => 'loading_effect',
			'type'             => 'select',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'woocommerce-food'),
				'1'   => esc_html__('Yes', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Infinite', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Infinite loop sliding ( go to first item when end loop)', 'woocommerce-food' ),
			'id'         => 'infinite',
			'type'             => 'select',
			'classes'             => 'column-2 show-incarousel hide-intable hide-inlist hide-ingrid',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'woocommerce-food'),
				'yes'   => esc_html__('Yes', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Enable modal', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enable modal details food info', 'woocommerce-food' ),
			'id'         => 'enable_modal',
			'type'             => 'select',
			'classes'             => 'column-3',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('Default', 'woocommerce-food'),
				'yes'   => esc_html__('Yes', 'woocommerce-food'),
				'no'   => esc_html__('No', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Featured food', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Show only Featured food', 'woocommerce-food' ),
			'id'         => 'featured',
			'type'             => 'select',
			'classes'             => 'column-3',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => esc_html__('No', 'woocommerce-food'),
				'1'   => esc_html__('Yes', 'woocommerce-food'),
			),
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Image Size', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Leave blank to use default image size', 'woocommerce-food' ),
			'id'         => 'img_size',
			'type'       => 'text',
			'classes'             => 'column-3',
		) );
		$sc_option->add_field( array(
			'name'       => esc_html__( 'Class name', 'woocommerce-food' ),
			'desc'       => esc_html__( 'add a class name and refer to it in custom CSS', 'woocommerce-food' ),
			'id'         => 'class',
			'type'       => 'text',
			'classes'             => 'column-3',
		) );
	
	}
}
$exwoofood_SC_Builder = new exwoofood_SC_Builder();