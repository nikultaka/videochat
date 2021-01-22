<?php
/**
 * Gateway Functions
 *
 * @package     RPRESS
 * @subpackage  Gateways
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Returns a list of all available gateways.
 *
 * @since 1.0
 * @return array $gateways All the available gateways
 */
function rpress_get_payment_gateways() {
	// Default, built-in gateways
	$gateways = array(
		'paypal' => array(
			'admin_label'    => __( 'PayPal Standard', 'restropress' ),
			'checkout_label' => __( 'PayPal', 'restropress' ),
			'supports'       => array( 'buy_now' )
		),
		'manual' => array(
			'admin_label'    => __( 'Test Payment', 'restropress' ),
			'checkout_label' => __( 'Test Payment', 'restropress' )
		),
		'cash_on_delivery' => array(
			'admin_label'    => __( 'Pay by cash', 'restropress' ),
			'checkout_label' => __( 'Pay by cash', 'restropress' )
		),
	);

	return apply_filters( 'rpress_payment_gateways', $gateways );
}

/**
 * Returns a list of all enabled gateways.
 *
 * @since 1.0
 * @param  bool $sort If true, the default gateway will be first
 * @return array $gateway_list All the available gateways
*/
function rpress_get_enabled_payment_gateways( $sort = false ) {
	$gateways = rpress_get_payment_gateways();
	$enabled  = (array) rpress_get_option( 'gateways', false );

	$gateway_list = array();

	foreach ( $gateways as $key => $gateway ) {
		if ( isset( $enabled[ $key ] ) && $enabled[ $key ] == 1 ) {
			$gateway_list[ $key ] = $gateway;
		}
	}


	if ( true === $sort ) {
		// Reorder our gateways so the default is first
		$default_gateway_id = rpress_get_default_gateway();

		if( rpress_is_gateway_active( $default_gateway_id ) ) {

			$default_gateway    = array( $default_gateway_id => $gateway_list[ $default_gateway_id ] );
			unset( $gateway_list[ $default_gateway_id ] );

			$gateway_list = array_merge( $default_gateway, $gateway_list );

		}

	}

	return apply_filters( 'rpress_enabled_payment_gateways', $gateway_list );
}

/**
 * Checks whether a specified gateway is activated.
 *
 * @since 1.0
 * @param string $gateway Name of the gateway to check for
 * @return boolean true if enabled, false otherwise
*/
function rpress_is_gateway_active( $gateway ) {
	$gateways = rpress_get_enabled_payment_gateways();
	$ret = array_key_exists( $gateway, $gateways );
	return apply_filters( 'rpress_is_gateway_active', $ret, $gateway, $gateways );
}

/**
 * Gets the default payment gateway selected from the RPRESS Settings
 *
 * @since 1.0
 * @return string Gateway ID
 */
function rpress_get_default_gateway() {
	$default = rpress_get_option( 'default_gateway', 'paypal' );

	if( ! rpress_is_gateway_active( $default ) ) {
		$gateways = rpress_get_enabled_payment_gateways();
		$gateways = array_keys( $gateways );
		$default  = reset( $gateways );
	}

	return apply_filters( 'rpress_default_gateway', $default );
}

/**
 * Returns the admin label for the specified gateway
 *
 * @since 1.0
 * @param string $gateway Name of the gateway to retrieve a label for
 * @return string Gateway admin label
 */
function rpress_get_gateway_admin_label( $gateway ) {
	$gateways = rpress_get_payment_gateways();
	$label    = isset( $gateways[ $gateway ] ) ? $gateways[ $gateway ]['admin_label'] : $gateway;
	$payment  = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : false;

	if( $gateway == 'manual' && $payment ) {
		if( rpress_get_payment_amount( $payment ) == 0 ) {
			$label = __( 'Test Payment', 'restropress' );
		}
	}

	return apply_filters( 'rpress_gateway_admin_label', $label, $gateway );
}

/**
 * Returns the checkout label for the specified gateway
 *
 * @since 1.0
 * @param string $gateway Name of the gateway to retrieve a label for
 * @return string Checkout label for the gateway
 */
function rpress_get_gateway_checkout_label( $gateway ) {
	$gateways = rpress_get_payment_gateways();
	$label    = isset( $gateways[ $gateway ] ) ? $gateways[ $gateway ]['checkout_label'] : $gateway;

	if( $gateway == 'manual' ) {
		$label = __( 'Test Payment', 'restropress' );
	}

	return apply_filters( 'rpress_gateway_checkout_label', $label, $gateway );
}

/**
 * Returns the options a gateway supports
 *
 * @since 1.0
 * @param string $gateway ID of the gateway to retrieve a label for
 * @return array Options the gateway supports
 */
function rpress_get_gateway_supports( $gateway ) {
	$gateways = rpress_get_enabled_payment_gateways();
	$supports = isset( $gateways[ $gateway ]['supports'] ) ? $gateways[ $gateway ]['supports'] : array();
	return apply_filters( 'rpress_gateway_supports', $supports, $gateway );
}

/**
 * Checks if a gateway supports buy now
 *
 * @since 1.0
 * @param string $gateway ID of the gateway to retrieve a label for
 * @return bool
 */
function rpress_gateway_supports_buy_now( $gateway ) {
	$supports = rpress_get_gateway_supports( $gateway );
	$ret = in_array( 'buy_now', $supports );
	return apply_filters( 'rpress_gateway_supports_buy_now', $ret, $gateway );
}

/**
 * Checks if an enabled gateway supports buy now
 *
 * @since 1.0
 * @return bool
 */
function rpress_shop_supports_buy_now() {
	$gateways = rpress_get_enabled_payment_gateways();
	$ret      = false;

	if( ! rpress_use_taxes()  && $gateways && 1 === count( $gateways ) ) {
		foreach( $gateways as $gateway_id => $gateway ) {
			if( rpress_gateway_supports_buy_now( $gateway_id ) ) {
				$ret = true;
				break;
			}
		}
	}

	return apply_filters( 'rpress_shop_supports_buy_now', $ret );
}

/**
 * Build the purchase data for a straight-to-gateway purchase button
 *
 * @since  1.0.0
 *
 * @param int   $fooditem_id
 * @param array $options
 * @param int   $quantity
 * @return mixed|void
 */
function rpress_build_straight_to_gateway_data( $fooditem_id = 0, $options = array(), $quantity = 1 ) {

	$price_options = array();

	if( empty( $options ) || ! rpress_has_variable_prices( $fooditem_id ) ) {
		$price = rpress_get_fooditem_price( $fooditem_id );
	} else {

		if( is_array( $options['price_id'] ) ) {
			$price_id = $options['price_id'][0];
		} else {
			$price_id = $options['price_id'];
		}

		$prices = rpress_get_variable_prices( $fooditem_id );

		// Make sure a valid price ID was supplied
		if( ! isset( $prices[ $price_id ] ) ) {
			wp_die( __( 'The requested price ID does not exist.', 'restropress' ), __( 'Error', 'restropress' ), array( 'response' => 404 ) );
		}

		$price_options = array(
			'price_id' => $price_id,
			'amount'   => $prices[ $price_id ]['amount']
		);
		$price  = $prices[ $price_id ]['amount'];
	}

	// Set up RestroPress array
	$fooditems = array(
		array(
			'id'      => $fooditem_id,
			'options' => $price_options
		)
	);

	// Set up Cart Details array
	$cart_details = array(
		array(
			'name'        => get_the_title( $fooditem_id ),
			'id'          => $fooditem_id,
			'item_number' => array(
				'id'      => $fooditem_id,
				'options' => $price_options
			),
			'tax'         => 0,
			'discount'    => 0,
			'item_price'  => $price,
			'subtotal'    => ( $price * $quantity ),
			'price'       => ( $price * $quantity ),
			'quantity'    => $quantity,
		)
	);

	if( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
	}


	// Setup user information
	$user_info = array(
		'id'         => is_user_logged_in() ? get_current_user_id()         : -1,
		'email'      => is_user_logged_in() ? $current_user->user_email     : '',
		'first_name' => is_user_logged_in() ? $current_user->user_firstname : '',
		'last_name'  => is_user_logged_in() ? $current_user->user_lastname  : '',
		'discount'   => 'none',
		'address'    => array()
	);

	// Setup purchase information
	$purchase_data = array(
		'fooditems'    => $fooditems,
		'fees'         => rpress_get_cart_fees(),
		'subtotal'     => $price * $quantity,
		'discount'     => 0,
		'tax'          => 0,
		'price'        => $price * $quantity,
		'purchase_key' => strtolower( md5( uniqid() ) ),
		'user_email'   => $user_info['email'],
		'date'         => date( 'Y-m-d H:i:s', current_time( 'timestamp' ) ),
		'user_info'    => $user_info,
		'post_data'    => array(),
		'cart_details' => $cart_details,
		'gateway'      => 'paypal',
		'buy_now'      => true,
		'card_info'    => array()
	);

	return apply_filters( 'rpress_straight_to_gateway_purchase_data', $purchase_data );

}

/**
 * Sends all the payment data to the specified gateway
 *
 * @since 1.0
 * @param string $gateway Name of the gateway
 * @param array $payment_data All the payment data to be sent to the gateway
 * @return void
*/
function rpress_send_to_gateway( $gateway, $payment_data ) {

	$payment_data['gateway_nonce'] = wp_create_nonce( 'rpress-gateway' );

	// $gateway must match the ID used when registering the gateway
	do_action( 'rpress_gateway_' . $gateway, $payment_data );
}

/**
 * Determines if the gateway menu should be shown
 *
 * If the cart amount is zero, no option is shown and the cart uses the manual gateway
 * to emulate a no-gateway-setup for a free fooditem
 *
 * @since 1.0
 * @return bool $show_gateways Whether or not to show the gateways
 */
function rpress_show_gateways() {
	$gateways = rpress_get_enabled_payment_gateways();
	$show_gateways = false;

	if ( count( $gateways ) > 1 ) {
		$show_gateways = true;
		if ( rpress_get_cart_total() <= 0 ) {
			$show_gateways = false;
		}
	}

	return apply_filters( 'rpress_show_gateways', $show_gateways );
}

/**
 * Determines what the currently selected gateway is
 *
 * If the cart amount is zero, no option is shown and the cart uses the manual
 * gateway to emulate a no-gateway-setup for a free fooditem
 *
 * @since 1.0
 * @return string $chosen_gateway The slug of the gateway
 */
function rpress_get_chosen_gateway() {
	$gateways = rpress_get_enabled_payment_gateways();
	$chosen   = isset( $_REQUEST['payment-mode'] ) ? $_REQUEST['payment-mode'] : false;

	if ( false !== $chosen ) {
		$chosen = preg_replace('/[^a-zA-Z0-9-_]+/', '', $chosen );
	}

	if ( ! empty ( $chosen ) ) {

		$chosen_gateway = urldecode( $chosen );

		if( ! rpress_is_gateway_active( $chosen_gateway ) ) {
			$chosen_gateway = rpress_get_default_gateway();
		}

	} else {
		$chosen_gateway = rpress_get_default_gateway();
	}

	if ( rpress_get_cart_subtotal() <= 0 ) {
		$chosen_gateway = 'manual';
	}

	return apply_filters( 'rpress_chosen_gateway', $chosen_gateway );
}

/**
 * Record a gateway error
 *
 * A simple wrapper function for rpress_record_log()
 *
 * @since 1.0.0
 * @param string $title Title of the log entry (default: empty)
 * @param string $message  Message to store in the log entry (default: empty)
 * @param int $parent Parent log entry (default: 0)
 * @return int ID of the new log entry
 */
function rpress_record_gateway_error( $title = '', $message = '', $parent = 0 ) {
	return rpress_record_log( $title, $message, $parent, 'gateway_error' );
}

/**
 * Counts the number of purchases made with a gateway
 *
 * @since  1.0.0
 *
 * @param string $gateway_id
 * @param string $status
 * @return int
 */
function rpress_count_sales_by_gateway( $gateway_id = 'paypal', $status = 'publish' ) {

	$ret  = 0;
	$args = array(
		'meta_key'    => '_rpress_payment_gateway',
		'meta_value'  => $gateway_id,
		'nopaging'    => true,
		'post_type'   => 'rpress_payment',
		'post_status' => $status,
		'fields'      => 'ids'
	);

	$payments = new WP_Query( $args );

	if( $payments )
		$ret = $payments->post_count;
	return $ret;
}

/**
 * Processes the purchase data and uses the Cash On Delivery to record
 * the transaction in the Order History
 *
 * @since 1.0
 * @param array $purchase_data Purchase Data
 * @return void
*/
function rpress_cash_on_delivery_payment( $purchase_data ) {
    	if( ! wp_verify_nonce( $purchase_data['gateway_nonce'], 'rpress-gateway' ) ) {
		wp_die( __( 'Nonce verification has failed', 'restropress' ), __( 'Error', 'restropress' ), array( 'response' => 403 ) );
	}

	$client_id = 262;
	$first_name = '';
	$last_name = '';
	$email = '';
        
	if(isset($_SESSION['user_client_data']->client_id)) {
		$client_id = $_SESSION['user_client_data']->client_id;
	}
	if(isset($_SESSION['user_client_data']->first_name)) {
		$first_name = $_SESSION['user_client_data']->first_name;
	}
	if(isset($_SESSION['user_client_data']->last_name)) {
		$last_name = $_SESSION['user_client_data']->last_name;
	}
	if(isset($_SESSION['user_client_data']->email_address)) {
		$email = $_SESSION['user_client_data']->email_address;
	}

	global $current_user;
	$current_user_data = get_currentuserinfo();

	$json_array = array();
        $data_merchant = get_user_details_auth();
        $data_merchant = json_decode($data_merchant);
        
         foreach ($purchase_data['cart_details'] as $cart_details){
             
             $json['item_id'] = $cart_details['id'];
             $json['row'] = "";
             $json['merchant_id'] = isset($data_merchant->merchant_id) ? $data_merchant->merchant_id : 2;
             $json['discount'] = "";
             $json['currentController'] = "";
             $json['category_id'] = $cart_details['item_number']['category'];
             $json['with_size'] = 2;
             
             $data_temp = $cart_details['item_number']['price_id'] > 0 ? '|'.$cart_details['item_number']['price_id'].'|'.$cart_details['item_number']['price_id'] : '';
             $json['price'] = $cart_details['item_price'].$data_temp;
             $json['qty'] = $cart_details['quantity'];
             $json['notes'] = $cart_details['item_number']['instruction'];
             foreach ($cart_details['item_number']['addon_items'] as $value_addon){
                  
                 $temp_addon[$value_addon['cat_id']][] = $value_addon['addon_id'].'|'.$value_addon['price']."|".$value_addon['addon_item_name'];
             }
             
             $json['sub_item'] = $temp_addon;
             $json['two_flavors'] = "";
             $json['non_taxable'] = "1";
             foreach ($cart_details['item_number']['addon_items'] as $value_addon_ids){
                 $temp_ids[] = $value_addon_ids['addon_id'];
             }
             $json['addon_ids'] = $temp_ids;
             $json_array[] = $json;
         }
         
	$payment_data = array(
		'price' 		=> $purchase_data['price'],
		'date' 			=> $purchase_data['date'],
		'user_email' 	=> $purchase_data['user_email'],
		'purchase_key' 	=> $purchase_data['purchase_key'],
		'currency' 		=> rpress_get_currency(),
		'fooditems' 	=> $purchase_data['fooditems'],
		'user_info' 	=> $purchase_data['user_info'],
		'cart_details' 	=> $purchase_data['cart_details'],
		'status' 		=> 'pending'
	);
        
        $order_array = array();
        foreach ($purchase_data['cart_details'] as $cart_details){
            $prodeuct_details = get_product_details_cutome($cart_details['id']);
            $temp['client_id'] = $client_id;
            $temp['item_id'] = $cart_details['id'];
            $temp['item_name'] = $prodeuct_details['item_name'];
            $temp['order_notes'] = $cart_details['item_number']['instruction'];
            $temp['normal_price'] = $cart_details['item_price'];
            $temp['qty'] = $cart_details['quantity'];
            $temp['size_id'] = $cart_details['item_number']['price_id'];
            $temp['cat_id'] = $prodeuct_details['category'][0];
            $temp['addon'] = $purchase_data['fooditems'][0]['addon_items'];
            $order_array[] = $temp;
        }
		$custome_data['order_details'] = $order_array;
		
		$street_address = '';
		$city = '';
		$rpress_postcode = '';
		$rpress_apt_suite = '';
		$rpress_phone = '';
		$rpress_number_of_guest = '';
		$rpress_table_number = '';
		$rpress_order_note = '';
		if(isset($_POST['rpress_street_address']) && $_POST['rpress_street_address']!='') {
			$street_address = $_POST['rpress_street_address'];
		}
		if(isset($_POST['rpress_city']) && $_POST['rpress_city']!='') {
			$city = $_POST['rpress_city'];
		}
		if(isset($_POST['rpress_postcode']) && $_POST['rpress_postcode']!='') {
			$rpress_postcode = $_POST['rpress_postcode'];
		}
		if(isset($_POST['rpress_apt_suite']) && $_POST['rpress_apt_suite']!='') {
			$rpress_apt_suite = $_POST['rpress_apt_suite'];
		}
		if(isset($_POST['rpress_phone']) && $_POST['rpress_phone']!='') {
			$rpress_phone = $_POST['rpress_phone'];
		}
                if(isset($_POST['rpress_number_of_guest']) && $_POST['rpress_number_of_guest']!='') {
			$rpress_number_of_guest = $_POST['rpress_number_of_guest'];
			$rpress_order_note = $_POST['rpress_order_note'];
		}
                if(isset($_POST['rpress_table_number']) && $_POST['rpress_table_number']!='') {
			$rpress_table_number = $_POST['rpress_table_number'];
		}
		

        $custome_data['order_delivery_address'] = array(
            'client_id' => $client_id,
            'street' => $street_address,
            'city' => $city,
            'state' => $city,
            'zipcode' => $rpress_postcode,
            'location_name' => $rpress_apt_suite,
            'country' => 'IE',
            'date_created' => date('Y-m-d H:i:s'),
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'contact_phone' => '+'.$rpress_phone,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'contact_email' => $email,
            'dinein_number_of_guest' => $rpress_number_of_guest,
            'dinein_special_instruction' => $rpress_order_note,
            'dinein_table_number' => $rpress_table_number
            
        );
        
        $custome_data['order'] = array(
            'client_id' => $client_id,
            'json_details' => json_encode($json_array),
            'trans_type' => $_COOKIE['service_type'],
            'payment_type' => 'cod',
            'sub_total' => $purchase_data['subtotal'],
            'voucher_code' => $purchase_data['user_info']['discount'],
            'voucher_amount' => $purchase_data['discount'],
            'discounted_amount' => $purchase_data['offer_amount'],
            'discount_percentage' => $purchase_data['offer_data']->offer_percentage ? $purchase_data['offer_data']->offer_percentage : 0,
            'tax' => $purchase_data['tax'],
            'taxable_total' => $purchase_data['tax'],
            'total_w_tax' => $purchase_data['price'],
            'status' => 'pending',
            'stats_id' => 0,
            'delivery_charge' => $purchase_data['shipping'],
            'distance' => $purchase_data['distance']['km'],
            'delivery_date' => date('Y-m-d', strtotime($_COOKIE['delivery_date'])),
            'date_created' => date('Y-m-d H:i:s'),
            'date_modified' => date('Y-m-d H:i:s'),
            'ip_address' => '1.22.140.221',
            'merchant_earnings' => $payment_data['price'],
            'request_from' => 'web',
            'order_id_token' => $payment_data['purchase_key'],
            'request_cancel_status' => 'pending',
            'dinein_number_of_guest' => $rpress_number_of_guest,
            'dinein_special_instruction' => $rpress_order_note,
            'dinein_table_number' => $rpress_table_number 
            
        );
        $time = $_COOKIE['service_time'];
        if($time == 'asap'){
            $custome_data['order']['delivery_asap'] = '1';    
        }else{
            $custome_data['order']['delivery_time'] = date('H:i:s', strtotime($_COOKIE['service_time']));
        }
          
      $response_custome =   insert_new_order_custome($custome_data);
      if($response_custome->insert_id > 0){
          $payment = rpress_insert_payment( $payment_data);

	if ( $payment ) {
		rpress_update_payment_status( $payment, 'processing' );
		rpress_update_order_status( $payment, 'pending' );
		// Empty the shopping cart
		rpress_empty_cart();
		rpress_send_to_success_page();
	} else {
		rpress_record_gateway_error( __( 'Payment Error', 'restropress' ), sprintf( __( 'Payment creation failed while processing with Cash On delivery order. Payment data: %s', 'restropress' ), json_encode( $payment_data ) ), $payment );
		// If errors are present, send the user back to the purchase page so they can be corrected
		rpress_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['rpress-gateway'] );
	}
      }else{
          	rpress_record_gateway_error( __( 'Payment Error', 'restropress' ), sprintf( __( 'Payment creation failed while processing with Cash On delivery order. Payment data: %s', 'restropress' ), json_encode( $payment_data ) ), $payment );
      }
	
}
add_action( 'rpress_gateway_cash_on_delivery', 'rpress_cash_on_delivery_payment' );

/**
 * Cash On Delivery Remove CC Form
 *
 * Cash On Delivery does not need a CC form, so remove it.
 *
 * @access private
 * @since 1.0
 */
add_action( 'rpress_cash_on_delivery_cc_form', '__return_false' );
