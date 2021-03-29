<?php
/**
 * Include and setup custom metaboxes and fields. (make sure you copy this file to outside the CMB2 directory)
 *
 * Be sure to replace all instances of 'yourprefix_' with your project's prefix.
 * http://nacin.com/2010/05/11/in-wordpress-prefix-everything/
 *
 * @category YourThemeOrPlugin
 * @package  Demo_CMB2
 * @license  http://www.opensource.org/licenses/gpl-license.php GPL v2.0 (or later)
 * @link     https://github.com/CMB2/CMB2
 */

/**
 * Get the bootstrap! If using the plugin from wordpress.org, REMOVE THIS!
 */

if ( file_exists( dirname( __FILE__ ) . '/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/cmb2/init.php';
} elseif ( file_exists( dirname( __FILE__ ) . '/CMB2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/CMB2/init.php';
}

require_once dirname( __FILE__ ) . '/Post-Search-field/cmb2_post_search_field.php';
require_once dirname( __FILE__ ) . '/open-close-field.php';

function exwoofood_get_option( $key = '', $tab=false, $default = false ) {
	if(isset($tab) && $tab!=''){
		$option_key = $tab;
	}else{
		$option_key = 'exwoofood_options';
	}
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		$val = cmb2_get_option( $option_key, $key, $default );
		return apply_filters('exwf_get_option', $val, $option_key, $key);
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( $option_key, $default );
	$val = $default;
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return apply_filters('exwf_get_option', $val, $option_key, $key);
}

add_action( 'cmb2_admin_init', 'exwoofood_register_metabox' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function exwoofood_register_metabox() {
	$prefix = 'exwoofood_';

	/**
	 * Food general info
	 */
	$food_info = new_cmb2_box( array(
		'id'            => $prefix . 'metabox',
		'title'         => esc_html__( 'Food info', 'woocommerce-food' ),
		'object_types'  => array( 'product' ), // Post type
	) );

	$food_info->add_field( array(
		'name'       => esc_html__( 'Protein', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'woocommerce-food' ),
		'id'         => $prefix . 'protein',
		'type'       => 'text',
		'classes'		 => 'column-4',
	) );
	$food_info->add_field( array(
		'name'       => esc_html__( 'Calories', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'woocommerce-food' ),
		'id'         => $prefix . 'calo',
		'type'       => 'text',
		'classes'		 => 'column-4',
	) );
	$food_info->add_field( array(
		'name'       => esc_html__( 'Cholesterol', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'woocommerce-food' ),
		'id'         => $prefix . 'choles',
		'type'       => 'text',
		'classes'		 => 'column-4',
	) );
	$food_info->add_field( array(
		'name'       => esc_html__( 'Dietary fibre', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'woocommerce-food' ),
		'id'         => $prefix . 'fibel',
		'type'       => 'text',
		'classes'		 => 'column-4',
	) );
	$food_info->add_field( array(
		'name'       => esc_html__( 'Sodium', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'woocommerce-food' ),
		'id'         => $prefix . 'sodium',
		'type'       => 'text',
		'classes'		 => 'column-3',
	) );
	$food_info->add_field( array(
		'name'       => esc_html__( 'Carbohydrates', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'woocommerce-food' ),
		'id'         => $prefix . 'carbo',
		'type'       => 'text',
		'classes'		 => 'column-3',
	) );
	$food_info->add_field( array(
		'name'       => esc_html__( 'Fat total', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Example: 50mg', 'woocommerce-food' ),
		'id'         => $prefix . 'fat',
		'type'       => 'text',
		'classes'		 => 'column-3',
	) );
	$food_info->add_field( array(
		'name'       => esc_html__( 'Custom Price', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Enter anything to replace with price', 'woocommerce-food' ),
		'id'         => $prefix . 'custom_price',
		'type'       => 'text',
		'classes'		 => 'column-2',
	) );
	$food_info->add_field( array(
		'name'       => esc_html__( 'Custom Color', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Set custom color for this food', 'woocommerce-food' ),
		'id'         => $prefix . 'custom_color',
		'type'       => 'colorpicker',
		'classes'		 => 'column-2',
	) );
	/**
	 * Build-in ordering system
	 */
	if(exwoofood_get_option('exfood_addon') =='yes'){
		$addition_option = new_cmb2_box( array(
			'id'            => $prefix . 'addition_options',
			'title'         => esc_html__( 'Additional option', 'woocommerce-food' ),
			'object_types'  => array( 'product' ), // Post type
		) );
		$group_option = $addition_option->add_field( array(
			'id'          => $prefix . 'addition_data',
			'type'        => 'group',
			'description' => esc_html__( 'Add additional food option to allow user can order with this food', 'woocommerce-food' ),
			 // use false if you want non-repeatable group: 'repeatable'  => false,
			'options'     => array(
				'group_title'   => esc_html__( 'Option {#}', 'woocommerce-food' ), // since version 1.1.4, {#} gets replaced by row number
				'add_button'    => esc_html__( 'Add Option', 'woocommerce-food' ),
				'remove_button' => esc_html__( 'Remove Option', 'woocommerce-food' ),
				'sortable'      => true, // beta
				// true to have the groups closed by default: 'closed'     => true,
			),
			'after_group' => 'exwoofood_repeatable_titles_for_options',
		) );
		// Id's for group's fields only need to be unique for the group. Prefix is not needed.
		$addition_option->add_group_field( $group_option, array(
			'name' => esc_html__( 'Name', 'woocommerce-food' ),
			'id'   => '_name',
			'type' => 'text',
			// Repeatable fields are supported w/in repeatable groups (for most types): 'repeatable' => true,
		) );
		$addition_option->add_group_field( $group_option, array(
			'name' => esc_html__( 'Option type', 'woocommerce-food' ),
			'description' => esc_html__( 'Select type of this option', 'woocommerce-food' ),
			'id'   => '_type',
			'type' => 'select',
			'show_option_none' => false,
			'default' => '',
			'options'          => array(
				'' => esc_html__( 'Checkboxes', 'woocommerce-food' ),
				'radio'   => esc_html__( 'Radio buttons', 'woocommerce-food' ),
				'select'   => esc_html__( 'Select box', 'woocommerce-food' ),
			),
		) );
		$addition_option->add_group_field( $group_option, array(
			'name' => esc_html__( 'Required?', 'woocommerce-food' ),
			'description' => esc_html__( 'Select this option is required or not', 'woocommerce-food' ),
			'id'   => '_required',
			'type' => 'select',
			'show_option_none' => false,
			'default' => '',
			'options'          => array(
				'' => esc_html__( 'No', 'woocommerce-food' ),
				'radio'   => esc_html__( 'Yes', 'woocommerce-food' ),
			),
		) );
		$addition_option->add_group_field( $group_option, array(
			'name' => esc_html__( 'Options', 'woocommerce-food' ),
			'description' => esc_html__( 'Enter name of option and price separator by | Example: Option 1 | 100', 'woocommerce-food' ),
			'id'   => '_value',
			'type' => 'text',
			'repeatable'     => true,
			'attributes'  => array(
				'placeholder' => esc_html__( 'Name | Price', 'woocommerce-food' ),
			),
		) );
	}

	$custom_data = new_cmb2_box( array(
		'id'            => $prefix . 'custom_data',
		'title'         => esc_html__( 'Food Custom Info', 'woocommerce-food' ),
		'object_types'  => array( 'product' ),
	) );
	$group_data = $custom_data->add_field( array(
		'id'          => $prefix . 'custom_data_gr',
		'type'        => 'group',
		'description' => esc_html__( 'Add food info, example: Fat saturated... Or anything you want to show', 'woocommerce-food' ),
		// use false if you want non-repeatable group: 'repeatable'  => false,
		'options'     => array(
			'group_title'   => esc_html__( 'Food Info {#}', 'woocommerce-food' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add Another Food info', 'woocommerce-food' ),
			'remove_button' => esc_html__( 'Remove Custom Food info', 'woocommerce-food' ),
			'sortable'      => true, // beta
			// true to have the groups closed by default: 'closed'     => true,
		),
		'after_group' => 'exwoofood_add_js_for_repeatable_titles',
	) );
	// Id's for group's fields only need to be unique for the group. Prefix is not needed.
	$custom_data->add_group_field( $group_data, array(
		'name' => esc_html__( 'Name', 'woocommerce-food' ),
		'id'   => '_name',
		'type' => 'text',
		// Repeatable fields are supported w/in repeatable groups (for most types): 'repeatable' => true,
	) );
	$custom_data->add_group_field( $group_data, array(
		'name' => esc_html__( 'Info', 'woocommerce-food' ),
		'description' => '',
		'id'   => '_value',
		'type' => 'text',
	) );


	$exwf_cticon = new_cmb2_box( array(
		'id'            => $prefix . 'cticon',
		'title'         => esc_html__( 'Custom Label Icon', 'woocommerce-food' ),
		'object_types'  => array( 'product' ),
		'context' => 'side',
		'priority' => 'low',
	) );
	$group_data = $exwf_cticon->add_field( array(
		'id'          => $prefix . 'cticon_gr',
		'type'        => 'group',
		'description' => esc_html__( 'Add label icon like spicy ...', 'woocommerce-food' ),
		// use false if you want non-repeatable group: 'repeatable'  => false,
		'options'     => array(
			'group_title'   => esc_html__( 'Label Icon {#}', 'woocommerce-food' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add Another Label Icon', 'woocommerce-food' ),
			'remove_button' => esc_html__( 'Remove Label Icon', 'woocommerce-food' ),
			'sortable'      => true, // beta
			// true to have the groups closed by default: 'closed'     => true,
		),
		'after_group' => 'exwoofood_add_js_for_repeatable_titles',
	) );
	// Id's for group's fields only need to be unique for the group. Prefix is not needed.
	$exwf_cticon->add_group_field( $group_data, array(
		'name' => esc_html__( 'Icon', 'woocommerce-food' ),
		'id'   => 'icon',
		'type'             => 'file',
		'default' 		   => '',
		'show_option_none' => false,
		'query_args' => array(
			'type' => array(
				'image/gif',
				'image/jpeg',
				'image/png',
			),
		),
		'text'    => array(
			'add_upload_file_text' => esc_html__( 'Add File', 'woocommerce-food' ), // Change upload button text. Default: "Add or Upload File"
		),
		'preview_size' => array( 30, 30 ),
		// Repeatable fields are supported w/in repeatable groups (for most types): 'repeatable' => true,
	) );
	$exwf_cticon->add_group_field( $group_data, array(
		'name' => esc_html__( 'Background color', 'woocommerce-food' ),
		'description' => '',
		'id'   => 'bgcolor',
		'type' => 'colorpicker',
	) );

}
// Regiter metadata fo menu
add_action( 'cmb2_admin_init', 'exwoofood_register_taxonomy_metabox' );
function exwoofood_register_taxonomy_metabox() {
	$prefix = 'exwoofood_menu_';
	/**
	 * Metabox to add fields to categories and tags
	 */
	$cmb_term = new_cmb2_box( array(
		'id'               => $prefix . 'data',
		'title'            => esc_html__( 'Category Metabox', 'woocommerce-food' ), // Doesn't output for term boxes
		'object_types'     => array( 'term' ), // Tells CMB2 to use term_meta vs post_meta
		'taxonomies'       => array( 'product_cat'), // Tells CMB2 which taxonomies should have these fields
		'new_term_section' => true, // Will display in the "Add New Category" section
	) );
	
	$cmb_term->add_field( array(
		'name' => esc_html__( 'Order Menu', 'woocommerce-food' ),
		'id'   => $prefix .'order',
		'type' => 'text',
			'attributes' => array(
			'type' => 'number',
			'pattern' => '\d*',
		),
		'sanitization_cb' => 'absint',
	        'escape_cb'       => 'absint',
	) );
	$cmb_term->add_field( array(
		'name' => esc_html__( 'Icon shortcode', 'woocommerce-food' ),
		'id'   => $prefix .'iconsc',
		'description' => esc_html__( 'Add your icon shortcode to replace with Icon image', 'woocommerce-food' ),
		'type' => 'text',
		'sanitization_cb' => 'exwo_allow_metadata_save_html',
	) );
}




function exwoofood_allow_metadata_save_html( $original_value, $args, $cmb2_field ) {
    return $original_value; // Unsanitized value.
}
function exwoofood_add_js_for_repeatable_titles() {
	add_action( is_admin() ? 'admin_footer' : 'wp_footer', 'exwoofood_js_repeatable_titles_custom_data' );
}
function exwoofood_js_repeatable_titles_custom_data() {
	exwoofood_js_for_repeatable_titles('exwoofood_custom_data');
}
function exwoofood_repeatable_titles_for_options() {
	add_action( is_admin() ? 'admin_footer' : 'wp_footer', 'exwoofood_js_repeatable_titles_options' );
}
function exwoofood_js_repeatable_titles_options() {
	exwoofood_js_for_repeatable_titles('exwoofood_addition_options');
}
function exwoofood_js_for_repeatable_titles($id) {
	
}
/**
 * Callback to define the optionss-saved message.
 *
 * @param CMB2  $cmb The CMB2 object.
 * @param array $args {
 *     An array of message arguments
 *
 *     @type bool   $is_options_page Whether current page is this options page.
 *     @type bool   $should_notify   Whether options were saved and we should be notified.
 *     @type bool   $is_updated      Whether options were updated with save (or stayed the same).
 *     @type string $setting         For add_settings_error(), Slug title of the setting to which
 *                                   this error applies.
 *     @type string $code            For add_settings_error(), Slug-name to identify the error.
 *                                   Used as part of 'id' attribute in HTML output.
 *     @type string $message         For add_settings_error(), The formatted message text to display
 *                                   to the user (will be shown inside styled `<div>` and `<p>` tags).
 *                                   Will be 'Settings updated.' if $is_updated is true, else 'Nothing to update.'
 *     @type string $type            For add_settings_error(), Message type, controls HTML class.
 *                                   Accepts 'error', 'updated', '', 'notice-warning', etc.
 *                                   Will be 'updated' if $is_updated is true, else 'notice-warning'.
 * }
 */
function exwoofood_options_page_message_( $cmb, $args ) {
	if ( ! empty( $args['should_notify'] ) ) {

		if ( $args['is_updated'] ) {

			// Modify the updated message.
			$args['message'] = sprintf( esc_html__( '%s &mdash; Updated!', 'woocommerce-food' ), $cmb->prop( 'title' ) );
		}

		add_settings_error( $args['setting'], $args['code'], $args['message'], $args['type'] );
	}
}


function exwoofood_register_setting_options() {
	/**
	 * Registers main options page menu item and form.
	 */
	$args = array(
		'id'           => 'exwoofood_options_page',
		'title'        => esc_html__('Food Settings','woocommerce-food'),
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('General','woocommerce-food'),
		'message_cb'      => 'exwoofood_options_page_message_',
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$main_options = new_cmb2_box( $args );
	/**
	 * Options fields ids only need
	 * to be unique within this box.
	 * Prefix is not needed.
	 */
	$main_options->add_field( array(
		'name'    => esc_html__('Main Color','woocommerce-food'),
		'desc'    => esc_html__('Choose Main Color for plugin','woocommerce-food'),
		'id'      => 'exwoofood_color',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Content Font Family', 'woocommerce-food' ),
		'desc'       => esc_html__('Enter Google font-family name . For example, if you choose "Source Sans Pro" Google Font, enter Source Sans Pro','woocommerce-food'),
		'id'         => 'exwoofood_font_family',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Content Font Size', 'woocommerce-food' ),
		'desc'       => esc_html__('Enter size of main font, default:13px, Ex: 14px','woocommerce-food'),
		'id'         => 'exwoofood_font_size',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'    => esc_html__('Content Font Color','woocommerce-food'),
		'desc'    => esc_html__('Choose Content Font Color for plugin','woocommerce-food'),
		'id'      => 'exwoofood_ctcolor',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Heading Font Family', 'woocommerce-food' ),
		'desc'       => esc_html__('Enter Google font-family name. For example, if you choose "Oswald" Google Font, enter Oswald','woocommerce-food'),
		'id'         => 'exwoofood_headingfont_family',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Heading Font Size', 'woocommerce-food' ),
		'desc'       => esc_html__('Enter size of heading font, default: 20px, Ex: 22px','woocommerce-food'),
		'id'         => 'exwoofood_headingfont_size',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'    => esc_html__('Heading Font Color','woocommerce-food'),
		'desc'    => esc_html__('Choose Heading Font Color for plugin','woocommerce-food'),
		'id'      => 'exwoofood_hdcolor',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Price Font Family', 'woocommerce-food' ),
		'desc'       => esc_html__('Enter Google font-family name. For example, if you choose "Oswald" Google Font, enter Oswald','woocommerce-food'),
		'id'         => 'exwoofood_pricefont_family',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Price Font Size', 'woocommerce-food' ),
		'desc'       => esc_html__('Enter size of Price font, default: 20px, Ex: 22px','woocommerce-food'),
		'id'         => 'exwoofood_pricefont_size',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'    => esc_html__('Price Font Color','woocommerce-food'),
		'desc'    => esc_html__('Choose Price Font Color for plugin','woocommerce-food'),
		'id'      => 'exwoofood_pricecolor',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Meta Font Family', 'woocommerce-food' ),
		'desc'       => esc_html__('Enter Google font-family name. For example, if you choose "Ubuntu" Google Font, enter Ubuntu','woocommerce-food'),
		'id'         => 'exwoofood_metafont_family',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'       => esc_html__( 'Meta Font Size', 'woocommerce-food' ),
		'desc'       => esc_html__('Enter size of metadata font, default:13px, Ex: 12px','woocommerce-food'),
		'id'         => 'exwoofood_metafont_size',
		'type'       => 'text',
		'default' => '',
	) );
	$main_options->add_field( array(
		'name'    => esc_html__('Meta Font Color','woocommerce-food'),
		'desc'    => esc_html__('Choose Meta Font Color for plugin','woocommerce-food'),
		'id'      => 'exwoofood_mtcolor',
		'type'    => 'colorpicker',
		'default' => '',
	) );
	
	$main_options->add_field( array(
		'name'             => esc_html__( 'Disable Extra Options', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select yes to disable default Extra Options', 'woocommerce-food' ),
		'id'               => 'exwoofood_disable_exoptions',
		'type'             => 'select',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			'' => esc_html__( 'No', 'woocommerce-food' ),
			'yes'   => esc_html__( 'Yes', 'woocommerce-food' ),
		),
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'Extra Options Style', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select style of Extra Options', 'woocommerce-food' ),
		'id'               => 'exwoofood_exoptions_style',
		'type'             => 'select',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			'' => esc_html__( 'Default', 'woocommerce-food' ),
			'accordion'   => esc_html__( 'Accordion', 'woocommerce-food' ),
		),
	) );
	
	$main_options->add_field( array(
		'name'             => esc_html__( 'RTL mode', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Enable RTL mode for RTL language', 'woocommerce-food' ),
		'id'               => 'exwoofood_enable_rtl',
		'type'             => 'select',
		'show_option_none' => false,
		'default' => '',
		'options'          => array(
			'' => esc_html__( 'No', 'woocommerce-food' ),
			'yes'   => esc_html__( 'Yes', 'woocommerce-food' ),
		),
	) );
	
	$main_options->add_field( array(
		'name'             => esc_html__( 'Enable Food by location', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select yes to enable Food by location ( You need to set each food for each location)', 'woocommerce-food' ),
		'id'               => 'exwoofood_enable_loc',
		'type'             => 'select',
		'default' 		   => '',
		'show_option_none' => false,
		'options'          => array(
			'' => esc_html__( 'No', 'woocommerce-food' ),
			'yes'   => esc_html__( 'Yes', 'woocommerce-food' ),
		),
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'Auto close popup after add item to cart', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select yes close popup after add item to cart', 'woocommerce-food' ),
		'id'               => 'exwoofood_clsose_pop',
		'type'             => 'select',
		'default' 		   => '',
		'show_option_none' => false,
		'options'          => array(
			'' => esc_html__( 'No', 'woocommerce-food' ),
			'yes'   => esc_html__( 'Yes', 'woocommerce-food' ),
		),
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'Popup location icon', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select Icon for location popup, only work when enable popup location', 'woocommerce-food' ),
		'id'               => 'exwoofood_loc_icon',
		'type'             => 'file',
		'default' 		   => '',
		'show_option_none' => false,
		'query_args' => array(
			'type' => array(
				'image/gif',
				'image/jpeg',
				'image/png',
			),
		),
		'preview_size' => array( 50, 50 ),
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'Food menu by date', 'woocommerce-food' ),
		'desc'             => esc_html__( 'This feature allow you can create food menu by date and  user only can order food of current date', 'woocommerce-food' ),
		'id'               => 'exwoofood_foodby_date',
		'type'             => 'select',
		'default' 		   => '',
		'show_option_none' => false,
		'options'          => array(
			'' => esc_html__( 'No', 'woocommerce-food' ),
			'yes'   => esc_html__( 'Yes', 'woocommerce-food' ),
		),
	) );
	$main_options->add_field( array(
		'name'             => esc_html__( 'Live total price', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select yes to enable live update total price', 'woocommerce-food' ),
		'id'               => 'exwoofood_enable_livetotal',
		'type'             => 'select',
		'default' 		   => '',
		'show_option_none' => false,
		'options'          => array(
			'' => esc_html__( 'No', 'woocommerce-food' ),
			'yes'   => esc_html__( 'Yes', 'woocommerce-food' ),
		),
	) );
	
}
add_action( 'cmb2_admin_init', 'exwoofood_register_setting_options' );
// register advanced
function exwf_ot_add_adv_time_html( $field_args, $field ) {
	$tab = isset($_GET['page']) && $_GET['page']!='' ? $_GET['page'] : '' ;
	$method_ship = exwoofood_get_option('exwoofood_enable_method','exwoofood_shpping_options');
	echo '<p class="exwf-sub-option">
		<a href="?page=exwoofood_advanced_options" class="'.($tab=='exwoofood_advanced_options' ? 'current' : '').'">'.esc_html__('General','woocommerce-food').'</a>';
	if($method_ship !='' && $method_ship !='delivery' ){	
		echo ' | <a href="?page=exwoofood_adv_takeaway_options" class="'.($tab=='exwoofood_adv_takeaway_options' ? 'current' : '').'">'.esc_html__('Takeaway','woocommerce-food').'</a>';
	}
	$dine_in = exwoofood_get_option('exwoofood_enable_dinein','exwoofood_shpping_options');
	if($dine_in=='yes' ){	
		echo ' | <a href="?page=exwoofood_adv_dinein_options" class="'.($tab=='exwoofood_adv_dinein_options' ? 'current' : '').'">'.esc_html__('Dine-In','woocommerce-food').'</a>';
	}

		echo ' | <a href="?page=exwoofood_adv_timesl_options" class="'.($tab=='exwoofood_adv_timesl_options' ? 'current' : '').'">'.esc_html__('Advanced Time slots','woocommerce-food').'</a>
	</p>';
}

function exwf_hide_if_disable_loc( $field ) {
	$loca_field = exwoofood_get_option('exwoofood_ck_loca','exwoofood_advanced_options');
	if($loca_field==''){
		return false;
	}
	return true;
}
add_action( 'cmb2_admin_init', 'exwoofood_register_setting_advanced' );
function exwoofood_register_setting_advanced(){	
	$args = array(
		'id'           => 'exwoofood_advanced',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_advanced_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('Advanced','woocommerce-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$adv_options = new_cmb2_box( $args );
	$tab = isset($_GET['section']) && $_GET['section']!='' ? $_GET['section'] : '' ;
	
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Date field', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select Date field is Required or Optional or Disable', 'woocommerce-food' ),
			'id'         => 'exwoofood_ck_date',
			'type'             => 'select',
			'before_row'     => 'exwf_ot_add_adv_time_html',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				''   => __( 'Required', 'woocommerce-food' ),
				'no' => __( 'Optional', 'woocommerce-food' ),
				'disable' => __( 'Disable', 'woocommerce-food' ),
			),
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Minimum time to order food before', 'woocommerce-food' ),
			'desc'       => esc_html__( 'This feature allow user only can order food before X day or X minutes, Enter number for day or enter number + m for minutes, Example: 1 for 1 day or: 30m for 30 minutes', 'woocommerce-food' ),
			'id'         => 'exwoofood_ck_beforedate',
			'type' => 'text',
			'show_option_none' => true,
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Disable dates', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Disable special Delivery Date', 'woocommerce-food' ),
			'id'         => 'exwoofood_ck_disdate',
			'type' => 'text_date_timestamp',
			'default'          => '',
			'date_format' => 'Y-m-d',
			'repeatable'     => true,
			'show_option_none' => true,
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Enable Special delivery dates', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Add dates to allow user only can select these special Delivery Dates (only support display delivery date in Select box)', 'woocommerce-food' ),
			'id'         => 'exwoofood_ck_enadate',
			'type' => 'text_date_timestamp',
			'default'          => '',
			'date_format' => 'Y-m-d',
			'repeatable'     => true,
			'show_option_none' => true,
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Disable days', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Disable special Day Delivery', 'woocommerce-food' ),
			'id'         => 'exwoofood_ck_disday',
			'type' => 'multicheck_inline',
			'options' => array(
				'1' => esc_html__( 'Monday', 'woocommerce-food' ),
				'2' => esc_html__( 'Tuesday', 'woocommerce-food' ),
				'3' => esc_html__( 'Wednesday', 'woocommerce-food' ),
				'4' => esc_html__( 'Thursday', 'woocommerce-food' ),
				'5' => esc_html__( 'Friday', 'woocommerce-food' ),
				'6' => esc_html__( 'Saturday', 'woocommerce-food' ),
				'7' => esc_html__( 'Sunday', 'woocommerce-food' ),
			),
			
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Display Delivery date in', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Set style of Delivery date', 'woocommerce-food' ),
			'id'         => 'exwoofood_dd_display',
			'type' => 'select',
			'options' => array(
				'select' => esc_html__( 'Select box', 'woocommerce-food' ),
				'picker' => esc_html__( 'Calendar Pikcer', 'woocommerce-food' ),
			),
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Calendar picker format', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Set format for calendart date picker, default: mm/dd/yyyy', 'woocommerce-food' ),
			'id'         => 'exwoofood_datepk_fm',
			'type' => 'select',
			'options' => array(
				'mm/dd/yyyy' => esc_html__( 'mm/dd/yyyy', 'woocommerce-food' ),
				'dd-mm-yyyy' => esc_html__( 'dd-mm-yyyy', 'woocommerce-food' ),
			),
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Time field', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select time field is Required or Optional or Disable', 'woocommerce-food' ),
			'id'         => 'exwoofood_ck_time',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				''   => __( 'Required', 'woocommerce-food' ),
				'no' => __( 'Optional', 'woocommerce-food' ),
				'disable' => __( 'Disable', 'woocommerce-food' ),
			),
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Time slots', 'woocommerce-food' ),
			'id'         => 'exwfood_deli_time',
			'type' => 'timedelivery',
			'time_format' => 'H:i',
			'repeatable'     => true,
			'attributes' => array(
				'data-timepicker' => json_encode( array(
					'stepMinute' => 1,
		            'timeFormat' => 'HH:mm'
				) ),
			),
		) );
		$adv_options->add_field( array(
			'name' => __( 'Minimum Order Amount required', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Set minimum amount required for each Order', 'woocommerce-food' ),
			'id'   => 'exwoofood_ck_mini_amount',
			'type' => 'text',
			'sanitization_cb' => '',
			'escape_cb'       => '',
			'after_field'  => '',
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Location field', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enable location field in Checkout to allow user can choose area they want to order', 'woocommerce-food' ),
			'id'         => 'exwoofood_ck_loca',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				''   => __( 'Disable', 'woocommerce-food' ),
				'req' => __( 'Required', 'woocommerce-food' ),
				'op' => __( 'Optional', 'woocommerce-food' ),
			),
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Advanced Time slot By location', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Enable Advanced Time slot By location', 'woocommerce-food' ),
			'id'         => 'exwoofood_adv_loca',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				''   => __( 'Disable', 'woocommerce-food' ),
				'enable' => __( 'Enable', 'woocommerce-food' ),
			),
			'show_on_cb' => 'exwf_hide_if_disable_loc',
		) );

		$adv_options->add_field( array(
			'name'        => esc_html__( 'Disable shipping in products','woocommerce-food'  ),
			'id'          => 'exwoofood_ign_deli',
			'type'        => 'post_search_text', 
			'desc'       => esc_html__( 'Select product (or by category below) to disable shipping feature/fields of WooCommerce Food when checkout', 'woocommerce-food' ),
			'post_type'   => 'product',
			'select_type' => 'checkbox',
			'select_behavior' => 'add',
			'after_field'  => '',
		) );
		$adv_options->add_field( array(
			'name'           => esc_html__( 'Disable shipping in category', 'woocommerce-food' ),
			'desc'           => esc_html__( 'Select category to disable shipping feature/fields of WooCommerce Food when checkout', 'woocommerce-food' ),
			'id'             => 'exwoofood_igncat_deli',
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
		// Open close time
		$adv_options->add_field( array(
			'name' => esc_html__('Opening and Closing time','woocommerce-food'),
			'desc' => '',
			'id'   => 'exwfood_op_cl',
			'type'        => 'title', 
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Opening and Closing time', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select yes to enable Opening and Closing time', 'woocommerce-food' ),
			'id'         => 'exwoofood_open_close',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => __( 'Disable', 'woocommerce-food' ),
				'enable' => __( 'Enable', 'woocommerce-food' ),
				'closed' => __( 'Closed', 'woocommerce-food' ),
			),
		) );
		$adv_options->add_field( array(
			'name'       => esc_html__( 'Enable Opening and Closing time for each location', 'woocommerce-food' ),
			'desc'       => esc_html__( 'Select yes to enable Opening and Closing time settings for each location', 'woocommerce-food' ),
			'id'         => 'exwoofood_open_close_loc',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => '',
			'options'          => array(
				'' => __( 'No', 'woocommerce-food' ),
				'yes' => __( 'Yes', 'woocommerce-food' ),
			),
		) );
		$adv_options->add_field( array(
			'name'        => esc_html__( 'Allow products','woocommerce-food'  ),
			'id'          => 'exwoofood_ign_op',
			'type'        => 'post_search_text', 
			'desc'       => esc_html__( 'Allow user can purchase products when your shop is closed.', 'woocommerce-food' ),
			'post_type'   => 'product',
			'select_type' => 'checkbox',
			'select_behavior' => 'add',
			'after_field'  => '',
		) );
		$adv_options->add_field( array(
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
		$adv_options->add_field( array(
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
		$adv_options->add_field( array(
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
		$adv_options->add_field( array(
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
		$adv_options->add_field( array(
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
		$adv_options->add_field( array(
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
		$adv_options->add_field( array(
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
add_action( 'cmb2_admin_init', 'exwoofood_register_setting_advanced_timessl' );
function exwoofood_register_setting_advanced_timessl(){	
	$args = array(
		'id'           => 'exwoofood_advanced_timesl',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_adv_timesl_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('Advanced','woocommerce-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$advsl_options = new_cmb2_box( $args );
	// Advanced time delivery
	$advsl_options->add_field( array(
		'name' => esc_html__('Advanced time slots','woocommerce-food'),
		'desc' => '',
		'id'   => 'exwfood_adv_tdel',
		'type'        => 'title', 
		'before_row'     => 'exwf_ot_add_adv_time_html',
	) );
	$group_option = $advsl_options->add_field( array(
		'id'          => 'exwfood_adv_timedeli',
		'type'        => 'group',
		'description' => esc_html__( 'Set time slots for each day of week (leave blank to use General setting)', 'woocommerce-food' ),
		// 'repeatable'  => false, // use false if you want non-repeatable group
		'options'     => array(
			'group_title'   => esc_html__( 'Time Delivery {#}', 'woocommerce-food' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add new', 'woocommerce-food' ),
			'remove_button' => esc_html__( 'Remove', 'woocommerce-food' ),
			'sortable'      => true, // beta
			'closed'     => false, // true to have the groups closed by default
		),
		'after_group' => '',
	) );
	$advsl_options->add_group_field( $group_option, array(
		'name'             => esc_html__( 'Shipping method', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select Shipping method for this time delivery', 'woocommerce-food' ),
		'id'               => 'deli_method',
		'type'             => 'select',
		'show_option_none' => false,
		'options'          => array(
			''   => esc_html__( 'Default', 'woocommerce-food' ),
			'takeaway'   => esc_html__( 'Only Takeaway', 'woocommerce-food' ),
			'delivery'   => esc_html__( 'Only Delivery', 'woocommerce-food' ),
			'dinein'   => esc_html__( 'Only Dine-in', 'woocommerce-food' ),
		),
	) );
	$enable_adv_tl = exwoofood_get_option('exwoofood_adv_loca','exwoofood_advanced_options');
	if($enable_adv_tl=='enable'){
		$advsl_options->add_group_field( $group_option, array(
			'name'           => esc_html__( 'Locations', 'woocommerce-food' ),
			'desc'           => esc_html__( 'Select Locations for this time delivery, leave blank to apply for all locations', 'woocommerce-food' ),
			'id'             => 'times_loc',
			'taxonomy'       => 'exwoofood_loc', //Enter Taxonomy Slug
			'type'           => 'taxonomy_multicheck_inline',
			'remove_default' => 'true', // Removes the default metabox provided by WP core.
			'query_args' => array(
				// 'orderby' => 'slug',
				// 'hide_empty' => true,
			),
			'classes'		 => 'cmb-type-taxonomy-multicheck-inline',
		) );
	}
	
	$advsl_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Monday', 'woocommerce-food' ),
		'id'   => 'repeat_Mon',
		'type' => 'checkbox',
		'classes'		 => 'column-7',
	) );
	$advsl_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Tuesday', 'woocommerce-food' ),
		'id'   => 'repeat_Tue',
		'type' => 'checkbox',
		'classes'		 => 'column-7',
	) );
	$advsl_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Wednesday', 'woocommerce-food' ),
		'id'   => 'repeat_Wed',
		'type' => 'checkbox',
		'classes'		 => 'column-7',
	) );
	$advsl_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Thursday', 'woocommerce-food' ),
		'id'   => 'repeat_Thu',
		'type' => 'checkbox',
		'classes'		 => 'column-7',
	) );
	$advsl_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Friday', 'woocommerce-food' ),
		'id'   => 'repeat_Fri',
		'type' => 'checkbox',
		'classes'		 => 'column-7',
	) );
	$advsl_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Saturday', 'woocommerce-food' ),
		'id'   => 'repeat_Sat',
		'type' => 'checkbox',
		'classes'		 => 'column-7',
	) );
	$advsl_options->add_group_field( $group_option, array(
		'name' => esc_html__( 'Sunday', 'woocommerce-food' ),
		'id'   => 'repeat_Sun',
		'type' => 'checkbox',
		'classes'		 => 'column-7',
	) );

	$advsl_options->add_group_field( $group_option, array(
		'name'       => esc_html__( 'Time slots', 'woocommerce-food' ),
		'id'         => 'exwfood_deli_time',
		'type' => 'timedelivery',
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
// Take away setting
add_action( 'cmb2_admin_init', 'exwoofood_register_setting_takeway' );
function exwoofood_register_setting_takeway(){	
	$args = array(
		'id'           => 'exwoofood_takeway',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_adv_takeaway_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('Takeaway','woocommerce-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$takeaway_options = new_cmb2_box( $args );
	$takeaway_options->add_field( array(
		'name'       => esc_html__( 'User need order Pickup Date food before', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Enter number, This feature allow user only can select Pickup Date food before X day or X minutes (enter number + m) from now', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_beforedate',
		'type' => 'text',
		'before_row'     => 'exwf_ot_add_adv_time_html',
		'show_option_none' => true,
	) );
	$takeaway_options->add_field( array(
		'name'       => esc_html__( 'Disable dates', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Disable special Pickup Date', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_disdate',
		'type' => 'text_date_timestamp',
		'default'          => '',
		'date_format' => 'Y-m-d',
		'repeatable'     => true,
		'show_option_none' => true,
	) );
	$takeaway_options->add_field( array(
		'name'       => esc_html__( 'Enable Special Pickup dates', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Add dates to allow user only can select these special Pickup Dates (only support display delivery date in Select box)', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_enadate',
		'type' => 'text_date_timestamp',
		'default'          => '',
		'date_format' => 'Y-m-d',
		'repeatable'     => true,
		'show_option_none' => true,
	) );
	$takeaway_options->add_field( array(
		'name'       => esc_html__( 'Disable days', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Disable special Pickup Day', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_disday',
		'type' => 'multicheck_inline',
		'options' => array(
			'1' => esc_html__( 'Monday', 'woocommerce-food' ),
			'2' => esc_html__( 'Tuesday', 'woocommerce-food' ),
			'3' => esc_html__( 'Wednesday', 'woocommerce-food' ),
			'4' => esc_html__( 'Thursday', 'woocommerce-food' ),
			'5' => esc_html__( 'Friday', 'woocommerce-food' ),
			'6' => esc_html__( 'Saturday', 'woocommerce-food' ),
			'7' => esc_html__( 'Sunday', 'woocommerce-food' ),
		),
		
	) );
	$takeaway_options->add_field( array(
		'name'       => esc_html__( 'Disable address fields', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Disable address fields when user select order method is Takeaway', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_disaddr',
		'type' => 'select',
		'options' => array(
			'no' => esc_html__( 'No', 'woocommerce-food' ),
			'yes' => esc_html__( 'Yes', 'woocommerce-food' ),
		),
		
	) );
	
}

// Dinein  setting
$dine_in = exwoofood_get_option('exwoofood_enable_dinein','exwoofood_shpping_options');
if($dine_in=='yes' ){
	add_action( 'cmb2_admin_init', 'exwoofood_register_setting_dinein' );
}
function exwoofood_register_setting_dinein(){	
	$args = array(
		'id'           => 'exwoofood_dinein',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_adv_dinein_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('Dine-In','woocommerce-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$dinein_options = new_cmb2_box( $args );
	$dinein_options->add_field( array(
		'name'       => esc_html__( 'User need order table before', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Enter number, This feature allow user only can select date before X day or X minutes (enter number + m) from now', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_beforedate',
		'type' => 'text',
		'before_row'     => 'exwf_ot_add_adv_time_html',
		'show_option_none' => true,
	) );
	$dinein_options->add_field( array(
		'name'       => esc_html__( 'Disable dates', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Disable special Date', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_disdate',
		'type' => 'text_date_timestamp',
		'default'          => '',
		'date_format' => 'Y-m-d',
		'repeatable'     => true,
		'show_option_none' => true,
	) );
	$dinein_options->add_field( array(
		'name'       => esc_html__( 'Enable Special dates', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Add dates to allow user only can select these special Dates (only support display date in Select box)', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_enadate',
		'type' => 'text_date_timestamp',
		'default'          => '',
		'date_format' => 'Y-m-d',
		'repeatable'     => true,
		'show_option_none' => true,
	) );
	$dinein_options->add_field( array(
		'name'       => esc_html__( 'Disable days', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Disable special Day', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_disday',
		'type' => 'multicheck_inline',
		'options' => array(
			'1' => esc_html__( 'Monday', 'woocommerce-food' ),
			'2' => esc_html__( 'Tuesday', 'woocommerce-food' ),
			'3' => esc_html__( 'Wednesday', 'woocommerce-food' ),
			'4' => esc_html__( 'Thursday', 'woocommerce-food' ),
			'5' => esc_html__( 'Friday', 'woocommerce-food' ),
			'6' => esc_html__( 'Saturday', 'woocommerce-food' ),
			'7' => esc_html__( 'Sunday', 'woocommerce-food' ),
		),
		
	) );
	$dinein_options->add_field( array(
		'name'       => esc_html__( 'Number of persons', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Select yes to enable number of persons field', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_nbperson',
		'type' => 'select',
		'options' => array(
			'' => esc_html__( 'Disable', 'woocommerce-food' ),
			'req' => esc_html__( 'Required', 'woocommerce-food' ),
			'op' => esc_html__( 'Optional', 'woocommerce-food' ),
		),
		
	) );
	$dinein_options->add_field( array(
		'name'       => esc_html__( 'Maxinum number of person user can select', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Enter number, defaul is 6', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_maxperson',
		'type' => 'text',
		'before_row'     => '',
		'show_option_none' => true,
	) );
	$dinein_options->add_field( array(
		'name'       => esc_html__( 'Disable address fields', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Disable address fields when user select order method is Dine-in', 'woocommerce-food' ),
		'id'         => 'exwoofood_ck_disaddr',
		'type' => 'select',
		'options' => array(
			'no' => esc_html__( 'No', 'woocommerce-food' ),
			'yes' => esc_html__( 'Yes', 'woocommerce-food' ),
		),
		
	) );
	
}
// register shipping
add_action( 'cmb2_admin_init', 'exwoofood_register_setting_shipping' );
function exwoofood_register_setting_shipping(){
	// Shipping
	$args = array(
		'id'           => 'exwoofood_shipping',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_shpping_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('Shipping','woocommerce-food'),
	);
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$shpping_options = new_cmb2_box( $args );
	$shpping_options->add_field( array(
		'name'             => esc_html__( 'Shipping method', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select disable to use default shipping method feature of WooCommerce', 'woocommerce-food' ),
		'id'               => 'exwoofood_enable_method',
		'type'             => 'select',
		'show_option_none' => false,
		'options'          => array(
			'' => esc_html__( 'Disable', 'woocommerce-food' ),
			'both'   => esc_html__( 'Delivery and Takeaway', 'woocommerce-food' ),
			'takeaway'   => esc_html__( 'Only Takeaway', 'woocommerce-food' ),
			'delivery'   => esc_html__( 'Only Delivery', 'woocommerce-food' ),
		),
	) );
	$shpping_options->add_field( array(
		'name'             => esc_html__( 'Enable Dine-in option', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Select yes to enable Dine-in option', 'woocommerce-food' ),
		'id'               => 'exwoofood_enable_dinein',
		'type'             => 'select',
		'show_option_none' => false,
		'options'          => array(
			'no'   => esc_html__( 'No', 'woocommerce-food' ),
			'yes'   => esc_html__( 'Yes', 'woocommerce-food' ),
		),
	) );

	$shpping_options->add_field( array(
		'name'       => esc_html__( 'Show close button', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Show close button on popup select shipping method (only work if you do not set different food for each location)', 'woocommerce-food' ),
		'id'         => 'exwoofood_cls_method',
		'type' => 'select',
		'options' => array(
			'no' => esc_html__( 'No', 'woocommerce-food' ),
			'yes' => esc_html__( 'Yes', 'woocommerce-food' ),
		),
		
	) );
	/*
	$shpping_options->add_field( array(
		'name'       => esc_html__( 'Limit shipping by', 'woocommerce-food' ),
		'desc'       => esc_html__( 'Select shipping by Radius with Google Map API or by Postcodes feature of WooCommerce', 'woocommerce-food' ),
		'id'         => 'exwoofood_ship_mode',
		'type' => 'select',
		'options' => array(
			'' => esc_html__( 'Radius with Google Map API', 'woocommerce-food' ),
			'post_code' => esc_html__( 'Postcodes feature of WooCommerce', 'woocommerce-food' ),
		),
		
	) );
	*/
	$shpping_options->add_field( array(
		'name' => esc_html__('Shipping fee','woocommerce-food'),
		'desc' => esc_html__('Set Shipping fee for delivery, enter number','woocommerce-food'),
		'id'   => 'exwoofood_ship_fee',
		'type' => 'text',
		'sanitization_cb' => '',
	) );
	$shpping_options->add_field( array(
		'name' => esc_html__('Minimum order amount to free shipping','woocommerce-food'),
		'desc' => esc_html__('Enter number','woocommerce-food'),
		'id'   => 'exwoofood_ship_free',
		'type' => 'text',
		'sanitization_cb' => '',
	) );
	$shpping_options->add_field( array(
		'name' => esc_html__('Google API','woocommerce-food'),
		'desc' => esc_html__('The API key is required to calculate Distance, please follow this guide to create API: https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key','woocommerce-food'),
		'id'   => 'exwoofood_gg_api',
		'type' => 'text',
		'sanitization_cb' => 'exwo_allow_metadata_save_html',
	) );
	$shpping_options->add_field( array(
		'name' => esc_html__('Distance Matrix API','woocommerce-food'),
		'desc' => esc_html__('If you want to restrict Google API by HTTP referrers (web sites) you will need to create seperate API for Distance Matrix API, Because you only can restrict Distance Matrix API by IP addresses (web servers, cron jobs, etc.)','woocommerce-food'),
		'id'   => 'exwoofood_gg_distance_api',
		'type' => 'text',
		'sanitization_cb' => 'exwo_allow_metadata_save_html',
	) );
	$shpping_options->add_field( array(
		'name' => esc_html__('Distance restrict (km)','woocommerce-food'),
		'desc' => esc_html__('Enter number of kilometer to restrict delivery','woocommerce-food'),
		'id'   => 'exwoofood_restrict_km',
		'type' => 'text',
		'sanitization_cb' => 'exwo_allow_metadata_save_html',
	) );
	$shpping_options->add_field( array(
		'name' => esc_html__('Distance calculation using','woocommerce-food'),
		'desc' => esc_html__('Select transportation mode for the calculation of distances','woocommerce-food'),
		'id'   => 'exwoofood_calcu_mode',
		'type' => 'select',
		'options' => array(
			'' => esc_html__( 'Driving ', 'woocommerce-food' ),
			'walking ' => esc_html__( 'Walking ', 'woocommerce-food' ),
			'bicycling ' => esc_html__( 'Bicycling ', 'woocommerce-food' ),
		),
		'sanitization_cb' => '',
	) );
	$shpping_options->add_field( array(
		'name' => esc_html__('Limit auto address by country','woocommerce-food'),
		'desc' => esc_html__('Enter country code to limit auto address complete by country, you can find your country code here: https://en.wikipedia.org/wiki/List_of_ISO_3166_country_codes, Example:US','woocommerce-food'),
		'id'   => 'exwoofood_autocomplete_limit',
		'type' => 'text',
		'sanitization_cb' => 'exwo_allow_metadata_save_html',
	) );
	
	$shpping_options->add_field( array(
		'name' => esc_html__('Shipping fee by km','woocommerce-food'),
		'desc' => '',
		'id'   => 'exwfood_op_cl',
		'type'        => 'title', 
	) );
	$feebykm_option = $shpping_options->add_field( array(
		'id'          => 'exwfood_adv_feekm',
		'type'        => 'group',
		'description' => esc_html__( 'Set shipping fee by km, leave blank to use default shipping fee above', 'woocommerce-food' ),
		// 'repeatable'  => false, // use false if you want non-repeatable group
		'options'     => array(
			'group_title'   => esc_html__( 'Shipping fee by km {#}', 'woocommerce-food' ), // since version 1.1.4, {#} gets replaced by row number
			'add_button'    => esc_html__( 'Add new', 'woocommerce-food' ),
			'remove_button' => esc_html__( 'Remove', 'woocommerce-food' ),
			'sortable'      => true, // beta
			'closed'     => false, // true to have the groups closed by default
		),
		'after_group' => '',
	) );
	$shpping_options->add_group_field( $feebykm_option, array(
		'name' => esc_html__( 'Max number of km', 'tv-schedule' ),
		'id'   => 'km',
		'type' => 'text',
			
		'classes'		 => 'column-4',
	) );
	$shpping_options->add_group_field( $feebykm_option, array(
		'name' => esc_html__( 'Fee', 'tv-schedule' ),
		'id'   => 'fee',
		'type' => 'text',
			
		'classes'		 => 'column-4',
	) );
	$shpping_options->add_group_field( $feebykm_option, array(
		'name' => esc_html__( 'Free if total amount reach', 'tv-schedule' ),
		'id'   => 'free',
		'type' => 'text',
			
		'classes'		 => 'column-4',
	) );
	$shpping_options->add_group_field( $feebykm_option, array(
		'name' => esc_html__( 'Minimum amount required', 'tv-schedule' ),
		'id'   => 'min_amount',
		'type' => 'text',
			
		'classes'		 => 'column-4',
	) );
	
}
// register custom code
add_action( 'cmb2_admin_init', 'exwoofood_register_setting_custom_code' );
function exwoofood_register_setting_custom_code(){
	// custom code
	$args = array(
		'id'           => 'exwoofood_custom_code',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_custom_code_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('Custom Code','woocommerce-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$customcode_options = new_cmb2_box( $args );
	$customcode_options->add_field( array(
		'name' => esc_html__('Custom Css','woocommerce-food'),
		'desc' => esc_html__('Paste your custom Css code','woocommerce-food'),
		'id'   => 'exwoofood_custom_css',
		'type' => 'textarea_code',
		'attributes' => array(
			'data-codeeditor' => json_encode( array(
				'codemirror' => array(
					'mode' => 'css'
				),
			) ),
		),
		'sanitization_cb' => 'exwo_allow_metadata_save_html',
	) );
	$customcode_options->add_field( array(
		'name' => esc_html__('Custom Js','woocommerce-food'),
		'desc' => esc_html__('Paste your custom Js code','woocommerce-food'),
		'id'   => 'exwoofood_custom_js',
		'type' => 'textarea_code',
		'attributes' => array(
			'data-codeeditor' => json_encode( array(
				'codemirror' => array(
					'mode' => 'javascript'
				),
			) ),
		),
		'sanitization_cb' => 'exwo_allow_metadata_save_html',
	) );
	/**
	 * Registers custom code
	 */
	$args = array(
		'id'           => 'exwoofood_js_css_file',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_js_css_file_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('Js + Css file','woocommerce-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$file_options = new_cmb2_box( $args );
	$file_options->add_field( array(
		'name'             => esc_html__( 'Turn off Google Font', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Turn off loading Google Font', 'woocommerce-food' ),
		'id'               => 'exwoofood_disable_ggfont',
		'type'             => 'select',
		'show_option_none' => false,
		'options'          => array(
			'' => esc_html__( 'No', 'woocommerce-food' ),
			'yes'   => esc_html__( 'Yes', 'woocommerce-food' ),
		),
	) );
	/**
	 * Registers purchase code
	 */
	$args = array(
		'id'           => 'exwoofood_verify_purchase',
		'menu_title'   => '',
		'object_types' => array( 'options-page' ),
		'option_key'   => 'exwoofood_verify_options',
		//'parent_slug'  => 'edit.php?post_type=product',
		'tab_group'    => 'exwoofood_options',
		'capability'    => 'manage_woocommerce',
		'tab_title'    => esc_html__('Plugin License','woocommerce-food'),
	);
	// 'tab_group' property is supported in > 2.4.0.
	if ( version_compare( CMB2_VERSION, '2.4.0' ) ) {
		$args['display_cb'] = 'exwoofood_options_display_with_tabs';
	}
	$vrf_purc_options = new_cmb2_box( $args );
	$vrf_purc_options->add_field( array(
		'name'             => esc_html__( 'Envato Username', 'woocommerce-food' ),
		'desc'             => esc_html__( 'Enter Envato username which you have purchased this plugin', 'woocommerce-food' ),
		'id'               => 'exwoofood_evt_name',
		'type'             => 'text',
	) );
	$vrf_purc_options->add_field( array(
		'name'             => esc_html__( 'Purchase Code', 'woocommerce-food' ),
		'desc'             => sprintf(esc_html__( 'Enter your %s purcahse code %s of this plugin', 'woocommerce-food' ), '<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-">', '</a>'),
		'id'               => 'exwoofood_evt_pcode',
		'type'             => 'text',
	) );
}
function exwo_allow_metadata_save_html( $original_value, $args, $cmb2_field ) {
    return $original_value; // Unsanitized value.
}
/**
 * A CMB2 options-page display callback override which adds tab navigation among
 * CMB2 options pages which share this same display callback.
 *
 * @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
 */
function exwoofood_options_display_with_tabs( $cmb_options ) {
	$tabs = exwoofood_options_page_tabs( $cmb_options );
	?>
	<div class="wrap cmb2-options-page option-<?php echo esc_attr($cmb_options->option_key); ?>">
		<?php if ( get_admin_page_title() ) : ?>
			<h2><?php echo wp_kses_post( get_admin_page_title() ); ?></h2>
		<?php endif; ?>
		<h2 class="nav-tab-wrapper">
			<?php foreach ( $tabs as $option_key => $tab_title ) : ?>
				<a class="nav-tab<?php if ( isset( $_GET['page'] ) && $option_key === $_GET['page'] ) : ?> nav-tab-active<?php endif; ?>" href="<?php menu_page_url( $option_key ); ?>"><?php echo wp_kses_post( $tab_title ); ?></a>
			<?php endforeach; ?>
		</h2>
		<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" id="<?php echo esc_attr($cmb_options->cmb->cmb_id); ?>" enctype="multipart/form-data" encoding="multipart/form-data">
			<input type="hidden" name="action" value="<?php echo esc_attr( $cmb_options->option_key ); ?>">
			<?php $cmb_options->options_page_metabox(); ?>
			<?php submit_button( esc_attr( $cmb_options->cmb->prop( 'save_button' ) ), 'primary', 'submit-cmb' ); ?>
		</form>
	</div>
	<?php
}
/**
 * Gets navigation tabs array for CMB2 options pages which share the given
 * display_cb param.
 *
 * @param CMB2_Options_Hookup $cmb_options The CMB2_Options_Hookup object.
 *
 * @return array Array of tab information.
 */
function exwoofood_options_page_tabs( $cmb_options ) {
	$tab_group = $cmb_options->cmb->prop( 'tab_group' );
	$tabs      = array();
	foreach ( CMB2_Boxes::get_all() as $cmb_id => $cmb ) {
		if ( $tab_group === $cmb->prop( 'tab_group' ) ) {
			$tabs[ $cmb->options_page_keys()[0] ] = $cmb->prop( 'tab_title' )
				? $cmb->prop( 'tab_title' )
				: $cmb->prop( 'title' );
		}
	}
	return $tabs;
}