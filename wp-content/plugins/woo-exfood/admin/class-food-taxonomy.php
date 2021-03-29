<?php
include 'inc/metadata-functions.php';
class EX_Food_Taxonomy {
	public function __construct()
    {
		add_action( 'init', array( $this, 'register_category_taxonomies' ) );
		add_action( 'init', array( $this, 'register_location_taxonomies' ) );
		add_action( 'cmb2_admin_init', array( $this,'register_taxonomy_category_metabox') );
		add_filter( 'manage_edit-product_cat_columns', array( $this,'_edit_columns_exfood_menu'));
		add_action( 'manage_product_cat_custom_column', array( $this,'_custom_columns_content_exfood_menu'),10,3);
    }
	function register_category_taxonomies(){
		$labels = array(
			'name'              => esc_html__( 'Food Menu', 'woocommerce-food' ),
			'singular_name'     => esc_html__( 'Food Menu', 'woocommerce-food' ),
			'search_items'      => esc_html__( 'Food Menu','woocommerce-food' ),
			'all_items'         => esc_html__( 'All Menu','woocommerce-food' ),
			'parent_item'       => esc_html__( 'Parent Menu' ,'woocommerce-food'),
			'parent_item_colon' => esc_html__( 'Parent Menu:','woocommerce-food' ),
			'edit_item'         => esc_html__( 'Edit Menu' ,'woocommerce-food'),
			'update_item'       => esc_html__( 'Update Menu','woocommerce-food' ),
			'add_new_item'      => esc_html__( 'Add New Menu' ,'woocommerce-food'),
			'menu_name'         => esc_html__( 'Food Menus','woocommerce-food' ),
		);			
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'food-menu' ),
		);
		
	}
	function register_location_taxonomies(){
		$labels = array(
			'name'              => esc_html__( 'Location', 'woocommerce-food' ),
			'singular_name'     => esc_html__( 'Location', 'woocommerce-food' ),
			'search_items'      => esc_html__( 'Location','woocommerce-food' ),
			'all_items'         => esc_html__( 'All Location','woocommerce-food' ),
			'parent_item'       => esc_html__( 'Parent Location' ,'woocommerce-food'),
			'parent_item_colon' => esc_html__( 'Parent Location:','woocommerce-food' ),
			'edit_item'         => esc_html__( 'Edit Location' ,'woocommerce-food'),
			'update_item'       => esc_html__( 'Update Location','woocommerce-food' ),
			'add_new_item'      => esc_html__( 'Add New Location' ,'woocommerce-food'),
			'menu_name'         => esc_html__( 'Food Locations','woocommerce-food' ),
		);			
		$args = array(
			'hierarchical'      => true,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true,
			'rewrite'           => array( 'slug' => 'food-menu' ),
		);
		$store = exwoofood_get_option('exwoofood_ck_store','exwoofood_advanced_options');
		if ($store !='disable') {
			register_taxonomy('exwoofood_loc', array( 'exwoofood_store','product' ), $args);
		}else{
			register_taxonomy('exwoofood_loc','product', $args);
		}	
	}
	// Register email field in location
	function register_taxonomy_category_metabox() {
		$prefix = 'exwp_loc_';
		/**
		 * Metabox to add fields to categories and tags
		 */
		$exwf_log_meta = new_cmb2_box( array(
			'id'               => $prefix . 'data',
			'title'            => esc_html__( 'Category Metabox', 'woocommerce-food' ), // Doesn't output for term boxes
			'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
			'taxonomies'       => array( 'exwoofood_loc'), // Tells CMB2 which taxonomies should have these fields
			'new_term_section' => true, // Will display in the "Add New Category" section
		) );
		$exwf_log_meta->add_field( array(
			'name' => esc_html__( 'Address', 'woocommerce-food' ),
			'id'   => $prefix .'address',
			'desc' => esc_html__( 'Add full address of this location to calculate radius shipping', 'woocommerce-food' ),
			'type' => 'text',
		) );
		$exwf_log_meta->add_field( array(
			'name' => esc_html__( 'Distance restrict (km)', 'woocommerce-food' ),
			'id'   => $prefix .'diskm',
			'desc' => esc_html__( 'Enter number of kilometer to restrict delivery for this location, leave blank to use value from setting page', 'woocommerce-food' ),
			'type' => 'text',
		) );
		$exwf_log_meta->add_field( array(
			'name' => esc_html__( 'Minimum Order Amount required', 'woocommerce-food' ),
			'id'   => $prefix .'min_amount',
			'desc' => esc_html__( 'Set minimum amount required for this location', 'woocommerce-food' ),
			'type' => 'text',
		) );
		$exwf_log_meta->add_field( array(
			'name' => esc_html__('Shipping fee','woocommerce-food'),
			'desc' => esc_html__('Set Shipping fee for delivery, enter number','woocommerce-food'),
			'id'   => $prefix .'ship_fee',
			'type' => 'text',
			'sanitization_cb' => '',
		) );
		$exwf_log_meta->add_field( array(
			'name' => esc_html__('Minimum order amount to free shipping','woocommerce-food'),
			'desc' => esc_html__('Enter number','woocommerce-food'),
			'id'   => $prefix .'ship_free',
			'type' => 'text',
			'sanitization_cb' => '',
		) );
		$exwf_log_meta->add_field( array(
			'name' => esc_html__( 'Email recipients', 'woocommerce-food' ),
			'id'   => $prefix .'email',
			'desc' => esc_html__( 'Set email to get notification when user order food from this location', 'woocommerce-food' ),
			'type' => 'text',
		) );
		$exwf_log_meta->add_field( array(
			'name' => esc_html__( 'Hide menu/category filter', 'woocommerce-food' ),
			'id'   => $prefix .'hide_menu',
			'desc' => esc_html__( 'Select menu/category filter to hide from this location', 'woocommerce-food' ),
			'taxonomy'       => 'product_cat',
			'type'           => 'taxonomy_multicheck_inline',
			'remove_default' => 'true', // Removes the default metabox provided by WP core.
			'select_all_button' => false,
			'query_args' => array(
				// 'orderby' => 'slug',
				// 'hide_empty' => true,
			),
			'classes'		 => 'cmb-type-taxonomy-multicheck-inline',
		) );
		// Open close time
		$loc_opcls = exwoofood_get_option('exwoofood_open_close_loc','exwoofood_advanced_options');
		if($loc_opcls=='yes'){
			$exwf_log_meta->add_field( array(
				'name' => esc_html__('Opening and Closing time','woocommerce-food'),
				'desc' => esc_html__('Leave blank to use value from setting','woocommerce-food'),
				'id'   => 'exwfood_op_cl',
				'type'        => 'title', 
			) );
			$exwf_log_meta->add_field( array(
				'name'       => esc_html__( 'Monday', 'woocommerce-food' ),
				'id'         => 'exwfood_Mon_opcl_time',
				'type' => 'openclose',
				'time_format' => 'H:i',
				'repeatable'     => true,
				'attributes' => array(
					'data-timepicker' => json_encode( array(
						'stepMinute' => 1,
			            'timeFormat' => 'HH:mm'
					) ),
				),
			) );
			$exwf_log_meta->add_field( array(
				'name'       => esc_html__( 'Tuesday', 'woocommerce-food' ),
				'id'         => 'exwfood_Tue_opcl_time',
				'type' => 'openclose',
				'time_format' => 'H:i',
				'repeatable'     => true,
				'attributes' => array(
					'data-timepicker' => json_encode( array(
						'stepMinute' => 1,
			            'timeFormat' => 'HH:mm'
					) ),
				),
			) );
			$exwf_log_meta->add_field( array(
				'name'       => esc_html__( 'Wednesday', 'woocommerce-food' ),
				'id'         => 'exwfood_Wed_opcl_time',
				'type' => 'openclose',
				'time_format' => 'H:i',
				'repeatable'     => true,
				'attributes' => array(
					'data-timepicker' => json_encode( array(
						'stepMinute' => 1,
			            'timeFormat' => 'HH:mm'
					) ),
				),
			) );
			$exwf_log_meta->add_field( array(
				'name'       => esc_html__( 'Thursday', 'woocommerce-food' ),
				'id'         => 'exwfood_Thu_opcl_time',
				'type' => 'openclose',
				'time_format' => 'H:i',
				'repeatable'     => true,
				'attributes' => array(
					'data-timepicker' => json_encode( array(
						'stepMinute' => 1,
			            'timeFormat' => 'HH:mm'
					) ),
				),
			) );
			$exwf_log_meta->add_field( array(
				'name'       => esc_html__( 'Friday', 'woocommerce-food' ),
				'id'         => 'exwfood_Fri_opcl_time',
				'type' => 'openclose',
				'time_format' => 'H:i',
				'repeatable'     => true,
				'attributes' => array(
					'data-timepicker' => json_encode( array(
						'stepMinute' => 1,
			            'timeFormat' => 'HH:mm'
					) ),
				),
				
			) );
			$exwf_log_meta->add_field( array(
				'name'       => esc_html__( 'Saturday', 'woocommerce-food' ),
				'id'         => 'exwfood_Sat_opcl_time',
				'type' => 'openclose',
				'time_format' => 'H:i',
				'repeatable'     => true,
				'attributes' => array(
					'data-timepicker' => json_encode( array(
						'stepMinute' => 1,
			            'timeFormat' => 'HH:mm'
					) ),
				),
				
			) );
			$exwf_log_meta->add_field( array(
				'name'       => esc_html__( 'Sunday', 'woocommerce-food' ),
				'id'         => 'exwfood_Sun_opcl_time',
				'type' => 'openclose',
				'time_format' => 'H:i',
				'repeatable'     => true,
				'attributes' => array(
					'data-timepicker' => json_encode( array(
						'stepMinute' => 1,
			            'timeFormat' => 'HH:mm'
					) ),
				),
				
			) );
		}

	}
	function _edit_columns_exfood_menu($columns){
		$columns['_order'] = esc_html__( 'Order Menu' , 'woocommerce-food' );	
		return $columns;
	}
	function _custom_columns_content_exfood_menu( $content,$column_name,$term_id) {
		switch ( $column_name ) {
			case '_order':
				$term_order = get_term_meta($term_id, 'exwoofood_menu_order', true);
				echo '<input type="number" class="exfd-sort-menu" data-id="' . esc_attr($term_id) . '" name="exfd_sort_menu" value="'.esc_attr($term_order).'">';
				break;	
		}
	}	
}
$EX_Food_Taxonomy = new EX_Food_Taxonomy();