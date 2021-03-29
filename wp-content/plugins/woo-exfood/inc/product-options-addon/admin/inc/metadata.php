<?php
/**
 * Register metadata box
 */

function exwf_hide_if_no_product( $field ) {
	// Don't show this field if not in the cats category.
	if ( get_post_type($field->object_id) == 'exwo_glboptions') {
		return false;
	}
	return true;
}

add_action( 'cmb2_admin_init', 'exwo_register_metabox' );

function exwo_register_metabox() {
	$prefix = 'exwo_';
	$text_domain = exwo_text_domain();
	/**
	 * Food general info
	 */
	$exwo_options = new_cmb2_box( array(
		'id'            => $prefix . 'addition_options',
		'title'         => esc_html__( 'Additional option', $text_domain ),
		'object_types'  => array( 'product','exwo_glboptions' ), // Post type
	) );
	$exwo_options->add_field( array(
		'name' => esc_html__( 'Exclude Global Option', $text_domain ),
		'description' => esc_html__( 'Exclude all Global Options apply this product', $text_domain ),
		'id'   => 'exwo_exclude_options',
		'type' => 'checkbox',
		'default' => '',
		'show_on_cb' => 'exwf_hide_if_no_product',
	) );
	$exwo_options->add_field( array(
		'name'        => esc_html__( 'Include global options',$text_domain  ),
		'id'          => 'exwo_include_options',
		'type'        => 'post_search_text', 
		'desc'       => esc_html__( 'Select Option(s) to apply for this product', $text_domain ),
		'post_type'   => 'exwo_glboptions',
		'select_type' => 'checkbox',
		'select_behavior' => 'add',
		'after_field'  => '',
		'show_on_cb' => 'exwf_hide_if_no_product',
	) );
	$group_option = $exwo_options->add_field( array(
		'id'          => $prefix . 'options',
		'type'        => 'group',
		'description' => esc_html__( 'Add additional product option to allow user can order with this product', $text_domain ),
		// 'repeatable'  => false, // use false if you want non-repeatable group
		'options'     => array(
			'group_title'   => esc_html__( 'Option {#}', $text_domain ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add Option', $text_domain ),
			'remove_button' => esc_html__( 'Remove Option', $text_domain ),
			'sortable'      => true, // beta
			'closed'     => true, // true to have the groups closed by default
		),
		'after_group' => '',
	) );
	// Id's for group's fields only need to be unique for the group. Prefix is not needed.
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Name', $text_domain ),
		'id'   => '_name',
		'type' => 'text',
		'classes' => 'exwo-stgeneral exwo-op-name',
		'before_row'     => 'exwf_option_sttab_html',
		// 'repeatable' => true, // Repeatable fields are supported w/in repeatable groups (for most types)
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Option type', $text_domain ),
		'description' => esc_html__( 'Select type of this option', $text_domain ),
		'id'   => '_type',
		'classes' => 'exwo-stgeneral extype-option exwo-op-type',
		'type' => 'select',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			'' => esc_html__( 'Checkboxes', $text_domain ),
			'radio'   => esc_html__( 'Radio buttons', $text_domain ),
			'select'   => esc_html__( 'Select box', $text_domain ),
			'text'   => esc_html__( 'Textbox', $text_domain ),
			'textarea'   => esc_html__( 'Textarea', $text_domain ),
			'quantity'   => esc_html__( 'Quantity', $text_domain ),
		),
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Required?', $text_domain ),
		'description' => esc_html__( 'Select this option is required or not', $text_domain ),
		'id'   => '_required',
		'type' => 'select',
		'classes' => 'exwo-stgeneral exwo-op-rq',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			'' => esc_html__( 'No', $text_domain ),
			'yes'   => esc_html__( 'Yes', $text_domain ),
		),
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Minimun selection', $text_domain ),
		'classes' => 'exwo-stgeneral exhide-radio exhide-select exhide-quantity exhide-textbox exhide-textarea exwo-op-min',
		'description' => esc_html__( 'Enter number minimum at least option required', $text_domain ),
		'id'   => '_min_op',
		'type' => 'text',
		'default' => '',
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Maximum selection', $text_domain ),
		'classes' => 'exwo-stgeneral exhide-radio exhide-select exhide-quantity exhide-textbox exhide-textarea exwo-op-max',
		'description' => esc_html__( 'Enter number Maximum option can select', $text_domain ),
		'id'   => '_max_op',
		'type' => 'text',
		'default' => '',
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Options', $text_domain ),
		'classes' => 'exwo-stgeneral exhide-textbox exhide-quantity exhide-textarea exwo-op-ops',
		'description' => esc_html__( 'Set name and price for each option', $text_domain ),
		'id'   => '_options',
		'type' => 'price_options',
		'repeatable'     => true,
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Type of price', $text_domain ),
		'description' => '',
		'classes' => 'exwo-stgeneral exshow-textbox exshow-quantity exshow-textarea exwo-hidden exwo-op-tpr',
		'id'   => '_price_type',
		'type' => 'select',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			'' => esc_html__( 'Quantity Based', $text_domain ),
			'fixed'   => esc_html__( 'Fixed Amount', $text_domain ),
		),
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Price', $text_domain ),
		'classes' => 'exwo-stgeneral exshow-textbox exshow-quantity exshow-textarea exwo-hidden exwo-op-pri',
		'description' => '',
		'id'   => '_price',
		'type' => 'text',
		'default' => '',
		'after_row'     => '</div>',
	) );
	
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Enable Conditional Logic', $text_domain ),
		'description' => esc_html__( 'Enable Conditional Logic for this option', $text_domain ),
		'classes' => 'exwo-stcon-logic',
		'id'   => '_enb_logic',
		'type' => 'checkbox',
		'show_option_none' => false,
		'before_row'     => '<div class="exwo-con-logic">',
		'default' => '',
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Conditional Logic', $text_domain ),
		'classes' => 'exwo-stcon-logic',
		'description' => '',
		'id'   => '_con_tlogic',
		'type' => 'select',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			''   => esc_html__( 'Show this option if', $text_domain ),
			'hide' => esc_html__( 'Hide this option if', $text_domain ),
		),
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => ' ',
		'classes' => 'exwo-stcon-logic',
		'description' => '',
		'id'   => '_con_logic',
		'type' => 'conlogic_options',
		'repeatable'     => true,
		'show_option_none' => false,
	) );
	$exwo_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Id of option', $text_domain ),
		'description' => '',
		'classes' => 'exwo-stcon-logic exwo-hidden',
		'id'   => '_id',
		'type' => 'text',
		'show_option_none' => false,
		'default' => '',
		'sanitization_cb' => 'exwo_metadata_save_id_html',
		'after_row'     => '</div>',
	) );

	$exwf_proptions = new_cmb2_box( array(
		'id'            => $prefix . 'products',
		'title'         => esc_html__( 'Products', 'woocommerce-food' ),
		'object_types'  => array( 'exwo_glboptions' ),
		'context' => 'side',
		'priority' => 'low',
	) );
	$exwf_proptions->add_field( array(
		'name'        => '',
		'id'          => $prefix . 'product_ids',
		'type'        => 'post_search_text', 
		'desc'       => esc_html__( 'Select product to apply this options', 'woocommerce-food' ),
		'post_type'   => 'product',
		'select_type' => 'checkbox',
		'select_behavior' => 'add',
		'after_field'  => '',
	) );
	//echo get_post_meta( '5743', 'exwo_product_ids', true );exit;
}
//
function exwo_metadata_save_id_html( $original_value, $args, $cmb2_field ) {
	//print_r(array_filter($_POST['exwo_options']));print_r($_POST);exit;
	if(isset($_POST['exwo_options']) && count($_POST['exwo_options']) == 1){
		if($_POST['exwo_options']['0']['name'] == ''){
			return $original_value;
		}
	}
	if($original_value==''){
		$original_value = 'exwo-id'.rand(10000,10000000000);
	}
    return $original_value; // Unsanitized value.
}
function exwf_option_sttab_html( $field_args, $field ) {
	$text_domain = exwo_text_domain();
	echo '<p class="exwo-gr-option">
		<a href="javascript:;" class="current" data-add=".exwo-general" data-remove=".exwo-con-logic">'.esc_html__('General',$text_domain).'</a>
		<a href="javascript:;" class="exwo-copypre">'.esc_html__('Copy from previous option',$text_domain).'</a>';
		
		$product = wc_get_product(get_the_ID());
		if( is_object($product) && method_exists($product, 'is_type') && $product->is_type( 'variable' ) ) {
			echo '<a href="javascript:;" class="" data-add=".exwo-con-logic" data-remove=".exwo-general">'.esc_html__('Conditional logic',$text_domain).'</a>';
		}
		
		echo '
	</p>
	<div class="exwo-general">';
}
// Metadata repeat field
function exwocmb2_get_price_type_options( $text_domain,$value = false ) {
	$_list = array(
		''   => esc_html__( 'Quantity Based', $text_domain ),
		'fixed' => esc_html__( 'Fixed Amount', $text_domain ),
	);

	$_options = '';
	foreach ( $_list as $abrev => $state ) {
		$_options .= '<option value="'. $abrev .'" '. selected( $value, $abrev, false ) .'>'. $state .'</option>';
	}

	return $_options;
}

function exwocmb2_render_price_options_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
	$text_domain = exwo_text_domain();
	// make sure we specify each part of the value we need.
	$value = wp_parse_args( $value, array(
		'name' => '',
		'type' => '',
		'def' => '',
		'dis' => '',
		'price' => '',
	) );
	?>
	<div class="exwo-options exwo-name-option"><p><label for="<?php echo $field_type->_id( '_name' ); ?>"><?php esc_html_e('Option name',$text_domain)?></label></p>
		<?php echo $field_type->input( array(
			'class' => '',
			'name'  => $field_type->_name( '[name]' ),
			'id'    => $field_type->_id( '_name' ),
			'value' => $value['name'],
			'type'  => 'text',
			'desc'  => '',
		) ); ?>
	</div>
	<div class="exwo-options exwo-def-option">
		<p><label for="<?php echo $field_type->_id( '_def' ); ?>"><?php esc_html_e('Default',$text_domain)?></label></p>
		<input type="checkbox" class="" name="<?php echo esc_attr($field_type->_name( '[def]' ))?>" id="<?php echo $field_type->_id( '_def' ); ?>" value="yes" data-hash="<?php echo $field->hash_id( '_def' ); ?>" <?php checked($value['def'],'yes');?>>
	</div>
	<div class="exwo-options exwo-dis-option">
		<p><label for="<?php echo $field_type->_id( '_dis' ); ?>"><?php esc_html_e('Disable ?',$text_domain)?></label></p>
		<input type="checkbox" class="" name="<?php echo esc_attr($field_type->_name( '[dis]' ))?>" id="<?php echo $field_type->_id( '_dis' ); ?>" value="yes" data-hash="<?php echo $field->hash_id( '_dis' ); ?>" <?php checked($value['dis'],'yes');?>>
	</div>
	<div class="exwo-options exwo-price-option"><p><label for="<?php echo $field_type->_id( '_price' ); ?>'"><?php esc_html_e('Price',$text_domain)?></label></p>
		<?php echo $field_type->input( array(
			'class' => '',		
			'name'  => $field_type->_name( '[price]' ),
			'id'    => $field_type->_id( '_price' ),
			'value' => $value['price'],
			'type'  => 'text',
			'desc'  => '',
		) ); ?>
	</div>
	<div class="exwo-options exwo-type-option"><p><label for="<?php echo $field_type->_id( '_type' ); ?>'"><?php esc_html_e('Type of price',$text_domain)?></label></p>
		<?php echo $field_type->select( array(
			'class' => '',		
			'name'  => $field_type->_name( '[type]' ),
			'id'    => $field_type->_id( '_type' ),
			'value' => $value['type'],
			'options' => exwocmb2_get_price_type_options($text_domain, $value['type'] ),
			'desc'  => '',
		) ); ?>
	</div>
	<br class="clear">
	<?php
	echo $field_type->_desc( true );

}
add_filter( 'cmb2_render_price_options', 'exwocmb2_render_price_options_field_callback', 10, 5 );
function exwocmb2_sanitize_price_options_callback( $override_value, $value ) {
	echo '<pre>';print_r($value);exit;
	return $value;
}
//add_filter( 'cmb2_sanitize_openclose', 'exwocmb2_sanitize_price_options_callback', 10, 2 );
// option select
function exwocmb2_get_select_type_options( $_list,$value = false ) {
	
	$_options = '';
	foreach ( $_list as $abrev => $state ) {
		$_options .= '<option value="'. $abrev .'" '. selected( $value, $abrev, false ) .'>'. $state .'</option>';
	}

	return $_options;
}
// condition logic
function exwocmb2_render_conlogic_options_field_callback( $field, $value, $object_id, $object_type, $field_type ) {
	$text_domain = exwo_text_domain();
	// make sure we specify each part of the value we need.
	$value = wp_parse_args( $value, array(
		'type_rel' => '',
		'type_con' => '',
		'type_op' => '',
		'val' => '',
	) );
	?>
	<div class="exwo-options exwo-type_rel-option">
		<?php 
		$list_rule = array(
			''   => esc_html__( 'Or', $text_domain ),
			//'and' => esc_html__( 'And', $text_domain ),
		);
		echo $field_type->select( array(
			'class' => '',
			'name'  => $field_type->_name( '[type_rel]' ),
			'id'    => $field_type->_id( '_type_rel' ),
			'value' => $value['type_rel'],
			'options' => exwocmb2_get_select_type_options($list_rule, $value['type_rel'] ),
			'desc'  => '',
		) ); ?>
	</div>
	<div class="exwo-options exwo-type_op-option">
		<?php 
		$list_op = array(
			''   => esc_html__( 'Variation', $text_domain ),
		);
		echo $field_type->select( array(
			'class' => '',
			'name'  => $field_type->_name( '[type_op]' ),
			'id'    => $field_type->_id( '_type_op' ),
			'value' => $value['type_op'],
			'options' => exwocmb2_get_select_type_options($list_op, $value['type_op'] ),
			'desc'  => '',
		) ); ?>
	</div>
	<div class="exwo-options exwo-type_con-option">
		<?php 
		$list_con = array(
			''   => esc_html__( 'is', $text_domain ),
			'is_not' => esc_html__( 'is not', $text_domain ),
		);
		echo $field_type->select( array(
			'class' => '',
			'name'  => $field_type->_name( '[type_con]' ),
			'id'    => $field_type->_id( '_type_con' ),
			'value' => $value['type_con'],
			'options' => exwocmb2_get_select_type_options($list_con, $value['type_con'] ),
			'desc'  => '',
		) ); ?>
	</div>
	<div class="exwo-options exwo-val-option">
		<?php 
		$id =  get_the_ID();
		$ar_variations = array();
		$ar_variations[] = '';
		$product = wc_get_product($id);
		if( is_object($product) && method_exists($product, 'is_type') && $product->is_type( 'variable' ) ) {
			$variations = $product->get_children();
			if(is_array($variations)){
				foreach ($variations as $variation) {
					if(count($variations) > 1){
						$ar_variations[$variation] = $variation.' - '.get_the_title($variation);
					}
				}
			}
		}
		echo $field_type->select( array(
			'class' => '',		
			'name'  => $field_type->_name( '[val]' ),
			'id'    => $field_type->_id( '_val' ),
			'value' => $value['val'],
			'options' => exwocmb2_get_select_type_options($ar_variations, $value['val'] ),
			'desc'  => '',
		) ); 
		echo $field_type->input( array(
			'class' => 'exwo-hidden',		
			'name'  => $field_type->_name( '[val]' ),
			'id'    => $field_type->_id( '_val' ),
			'value' => $value['val'],
			'type'  => 'text',
			'desc'  => '',
		) );
		?>
	</div>
	<br class="clear">
	<?php
	echo $field_type->_desc( true );

}
add_filter( 'cmb2_render_conlogic_options', 'exwocmb2_render_conlogic_options_field_callback', 12, 5 );
add_filter( 'cmb2_sanitize_conlogic_options', 'exwosanitize' , 10, 5 );
add_filter( 'cmb2_types_esc_conlogic_options', 'exwoescape' , 10, 4 );

add_filter( 'cmb2_sanitize_price_options', 'exwosanitize' , 10, 5 );
add_filter( 'cmb2_types_esc_price_options', 'exwoescape' , 10, 4 );
function exwosanitize( $check, $meta_value, $object_id, $field_args, $sanitize_object ) {

	// if not repeatable, bail out.
	if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
		return $check;
	}

	foreach ( $meta_value as $key => $val ) {
		$meta_value[ $key ] = array_filter( array_map( 'sanitize_text_field', $val ) );
	}

	return array_filter( $meta_value );
}

function exwoescape( $check, $meta_value, $field_args, $field_object ) {
	// if not repeatable, bail out.
	if ( ! is_array( $meta_value ) || ! $field_args['repeatable'] ) {
		return $check;
	}

	foreach ( $meta_value as $key => $val ) {
		$meta_value[ $key ] = array_filter( array_map( 'esc_attr', $val ) );
	}

	return array_filter( $meta_value );
}
