<?php
/**
 * Cart Template
 *
 * @package     RPRESS
 * @subpackage  Cart
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Builds the Cart by providing hooks and calling all the hooks for the Cart
 *
 * @since 1.0
 * @return void
 */
function rpress_checkout_cart() {

	// Check if the Update cart button should be shown
	if( rpress_item_quantities_enabled() ) {
		add_action( 'rpress_cart_footer_buttons', 'rpress_update_cart_button' );
	}

	// Check if the Save Cart button should be shown
	if( ! rpress_is_cart_saving_disabled() ) {
		add_action( 'rpress_cart_footer_buttons', 'rpress_save_cart_button' );
	}


	do_action( 'rpress_before_checkout_cart' );


	
        
	echo '<form id="rpress_checkout_cart_form" class="rp-col-lg-4 rp-col-md-4 rp-col-sm-12 rp-col-xs-12 pull-right sticky-sidebar" method="post">';
		echo '<div id="rpress_checkout_cart_wrap">';
			do_action( 'rpress_checkout_cart_top' );
			rpress_get_template_part( 'checkout_cart' );
			do_action( 'rpress_checkout_cart_bottom' );
		echo '</div>';
	echo '</form>';
	do_action( 'rpress_after_checkout_cart' );
	
}

/**
 * Renders the Shopping Cart
 *
 * @since 1.0
 *
 * @param bool $echo
 * @return string Fully formatted cart
 */
function rpress_shopping_cart( $echo = false ) {
	rpress_get_template_part( 'cart/cart' );
}

/**
 * Get Cart Item Template
 *
 * @since 1.0
 * @param int $cart_key Cart key
 * @param array $item Cart item
 * @param bool $ajax AJAX?
 * @return string Cart item
*/
function rpress_get_cart_item_template( $cart_key, $item, $ajax = false, $data_key ) {

	
		$product_details = get_product_details_cutome( $item['id'] );
	if( empty($item['id']) )
		return;

	$id 			= is_array( $item ) ? $item['id'] : $item;
	$price_id 		= rpress_get_cart_item_price_id( $item );
        $edit_item_url 	= rpress_edit_cart_item( $cart_key, $item );
	$remove_url 	= rpress_remove_item_url( $cart_key );

	$title      	= $product_details['item_name'];
	$options    	= !empty( $item['options'] ) ? $item['options'] : array();
        $quantity = rpress_get_cart_item_quantity($cart_key,$options);
       
	//$quantity   	= $item['options']['quantity'];

	$price      	= rpress_get_cart_item_price( $id, $item, $price_id );
        
	$addon_itm  	= get_addon_item_formatted($item);
        
	$instruction 	= get_special_instruction($item);
	$delivery_options = get_delivery_options( true );
        
	$item_qty   	= rpress_get_item_qty_by_key( $cart_key );
	$color 			= rpress_get_option( 'checkout_color', 'red' );
        
        

	ob_start();

	
	rpress_get_template_part( 'cart/item' );
	

	$item = ob_get_clean();

	// $item_options = isset( $item['options'] ) ? $item['options'] : array();
	// $price = rpress_cart_item_price( $id, $item_options );

	$item = str_replace( '{item_qty}', absint( $quantity ), $item );
	$item = str_replace( '{item_title}', $title, $item );
	$item = str_replace( '{item_amount}', $price, $item );
 	$item = str_replace( '{addon_items}', $addon_itm, $item );

	$item = str_replace( '{cart_item_id}', absint( $cart_key ), $item );
	$item = str_replace( '{item_id}', absint( $id ), $item );
	$item = str_replace( '{item_quantity}', absint( $quantity ), $item );
	$item = str_replace( '{remove_url}', $remove_url, $item );
	$item = str_replace( '{edit_food_item}', $edit_item_url, $item );
	$item = str_replace( '{special_instruction}', $instruction, $item );
	$item = str_replace( '{color}', $color, $item );
        
  	$subtotal = '';
  	if ( $ajax ){
   	 $subtotal = rpress_currency_filter( rpress_format_amount( rpress_get_cart_subtotal() ) ) ;
        }
 	$item = str_replace( '{subtotal}', $subtotal, $item );
 	$item = str_replace( '{deliveryOptions}', $delivery_options, $item );


	return apply_filters( 'rpress_cart_item', $item, $id );
}

function rpress_edit_cart_item( $cart_key, $item ) {
	if( is_array($item) && !empty($item) ) {
		return $cart_key;
	}
}

function get_addon_item_formatted( $addon_items ) {

	$html = '';

	$addon_data_items = isset( $addon_items['options']['addon_items'] ) ? $addon_items['options']['addon_items'] : '';

	if ( empty( $addon_data_items) ) {
		$addon_data_items = isset( $addon_items['addon_items'] ) ? $addon_items['addon_items'] : '';
	}

  	if( is_array( $addon_data_items ) && !empty( $addon_data_items ) ) :

    	$html.= '<ul class="addon-item-wrap">';

    	foreach( $addon_data_items as $addon_item ) :

      		if( is_array( $addon_item ) ) :

        		$addon_id = !empty( $addon_item['addon_id'] ) ? $addon_item['addon_id'] : '';

        		if( !empty( $addon_id ) ) :

          			$addon_price = !empty( $addon_item['price'] ) ? rpress_currency_filter( rpress_format_amount( $addon_item['price'] ) ) : '';

          			

            			$addon_item_name = $addon_item['addon_item_name'];

            			$html.= '<li class="rpress-cart-item">
			              <span class="rpress-cart-item-title">'.$addon_item_name.'</span>
			              <span class="addon-item-price cart-item-quantity-wrap">
			                <span class="rpress-cart-item-price qty-class">'.$addon_price.'</span>
			              </span>
			            </li>';
			        
			    endif;
			endif;
		endforeach;

		$html.= '</ul>';

	endif;

  	return $html;
}


/**
 * Returns the Empty Cart Message
 *
 * @since 1.0
 * @return string Cart is empty message
 */
function rpress_empty_cart_message() {
	return apply_filters( 'rpress_empty_cart_message', '<span class="rpress_empty_cart">' . __( 'CHOOSE AN ITEM FROM THE MENU TO GET STARTED.', 'restropress' ) . '</span>' );
}

/**
 * Echoes the Empty Cart Message
 *
 * @since 1.0
 * @return void
 */
function rpress_empty_checkout_cart() {
	echo rpress_empty_cart_message();
}
add_action( 'rpress_cart_empty', 'rpress_empty_checkout_cart' );

/*
 * Calculate the number of columns in the cart table dynamically.
 *
 * @since 1.0
 * @return int The number of columns
 */
function rpress_checkout_cart_columns() {
	global $wp_filter, $wp_version;

	$columns_count = 3;

	if ( ! empty( $wp_filter['rpress_checkout_table_header_first'] ) ) {
		$header_first_count = 0;
		$callbacks = version_compare( $wp_version, '4.7', '>=' ) ? $wp_filter['rpress_checkout_table_header_first']->callbacks : $wp_filter['rpress_checkout_table_header_first'] ;

		foreach ( $callbacks as $callback ) {
			$header_first_count += count( $callback );
		}
		$columns_count += $header_first_count;
	}

	if ( ! empty( $wp_filter['rpress_checkout_table_header_last'] ) ) {
		$header_last_count = 0;
		$callbacks = version_compare( $wp_version, '4.7', '>=' ) ? $wp_filter['rpress_checkout_table_header_last']->callbacks : $wp_filter['rpress_checkout_table_header_last'] ;

		foreach ( $callbacks as $callback ) {
			$header_last_count += count( $callback );
		}
		$columns_count += $header_last_count;
	}

	return apply_filters( 'rpress_checkout_cart_columns', $columns_count );
}

/**
 * Display the "Save Cart" button on the checkout
 *
 * @since 1.0
 * @return void
 */
function rpress_save_cart_button() {
	if ( rpress_is_cart_saving_disabled() )
		return;

	$color = rpress_get_option( 'checkout_color', 'red' );
	$color = ( $color == 'inherit' ) ? '' : $color;

	if ( rpress_is_cart_saved() ) : ?>
		<a class="rpress-cart-saving-button rpress-submit button<?php echo ' ' . $color; ?>" id="rpress-restore-cart-button" href="<?php echo esc_url( add_query_arg( array( 'rpress_action' => 'restore_cart', 'rpress_cart_token' => rpress_get_cart_token() ) ) ); ?>"><?php _e( 'Restore Previous Cart', 'restropress' ); ?></a>
	<?php endif; ?>
	<a class="rpress-cart-saving-button rpress-submit button<?php echo ' ' . $color; ?>" id="rpress-save-cart-button" href="<?php echo esc_url( add_query_arg( 'rpress_action', 'save_cart' ) ); ?>"><?php _e( 'Save Cart', 'restropress' ); ?></a>
	<?php
}

/**
 * Displays the restore cart link on the empty cart page, if a cart is saved
 *
 * @since 1.0
 * @return void
 */
function rpress_empty_cart_restore_cart_link() {

	if( rpress_is_cart_saving_disabled() )
		return;

	if( rpress_is_cart_saved() ) {
		echo ' <a class="rpress-cart-saving-link" id="rpress-restore-cart-link" href="' . esc_url( add_query_arg( array( 'rpress_action' => 'restore_cart', 'rpress_cart_token' => rpress_get_cart_token() ) ) ) . '">' . __( 'Restore Previous Cart.', 'restropress' ) . '</a>';
	}
}
add_action( 'rpress_cart_empty', 'rpress_empty_cart_restore_cart_link' );

/**
 * Display the "Save Cart" button on the checkout
 *
 * @since 1.0
 * @return void
 */
function rpress_update_cart_button() {

	if ( ! rpress_item_quantities_enabled() )
		return;

	$color = rpress_get_option( 'checkout_color', 'red' );
	$color = ( $color == 'inherit' ) ? '' : $color;

	?>
	<input type="submit" name="rpress_update_cart_submit" class="rpress-submit rpress-no-js button<?php echo ' ' . $color; ?>" value="<?php _e( 'Update Cart', 'restropress' ); ?>"/>
	<input type="hidden" name="rpress_action" value="update_cart"/>
	<?php
}

/**
 * Display the messages that are related to cart saving
 *
 * @since 1.0
 * @return void
 */
function rpress_display_cart_messages() {
	$messages = RPRESS()->session->get( 'rpress_cart_messages' );

	if ( $messages ) {
		foreach ( $messages as $message_id => $message ) {

			// Try and detect what type of message this is
			if ( strpos( strtolower( $message ), 'error' ) ) {
				$type = 'error';
			} elseif ( strpos( strtolower( $message ), 'success' ) ) {
				$type = 'success';
			} else {
				$type = 'info';
			}

			$classes = apply_filters( 'rpress_' . $type . '_class', array(
				'rpress_errors', 'rpress-alert', 'rpress-alert-' . $type
			) );

			echo '<div class="' . implode( ' ', $classes ) . '">';
			// Loop message codes and display messages
			echo '<p class="rpress_error" id="rpress_msg_' . $message_id . '">' . $message . '</p>';
			echo '</div>';
		}

		// Remove all of the cart saving messages
		RPRESS()->session->set( 'rpress_cart_messages', null );
	}
}
add_action( 'rpress_before_checkout_cart', 'rpress_display_cart_messages' );

/**
 * Show Added To Cart Messages
 *
 * @since 1.0
 * @param int $fooditem_id Download (Post) ID
 * @return void
 */
function rpress_show_added_to_cart_messages( $fooditem_id ) {
	if ( isset( $_POST['rpress_action'] ) && $_POST['rpress_action'] == 'add_to_cart' ) {
		if ( $fooditem_id != absint( $_POST['fooditem_id'] ) )
			$fooditem_id = absint( $_POST['fooditem_id'] );

		$alert = '<div class="rpress_added_to_cart_alert">'
		. sprintf( __('You have successfully added %s to your shopping cart.','restropress' ), get_the_title( $fooditem_id ) )
		. ' <a href="' . rpress_get_checkout_uri() . '" class="rpress_alert_checkout_link">' . __('Checkout.','restropress' ) . '</a>'
		. '</div>';

		echo apply_filters( 'rpress_show_added_to_cart_messages', $alert );
	}
}
add_action('rpress_after_fooditem_content', 'rpress_show_added_to_cart_messages');