<?php
/**
 * Checkout Template
 *
 * @package     RPRESS
 * @subpackage  Checkout
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Checkout Form
 *
 * @since 1.0
 * @return string
 */
function rpress_checkout_form() {
    
	$payment_mode = rpress_get_chosen_gateway();
        if(is_user_logged_in()){
            if(isset($_GET['confirm']) || $_COOKIE['service_type'] != 'delivery'){
                $form_action  = esc_url( rpress_get_checkout_uri( 'payment-mode=' . $payment_mode ) );
            }else{
                $form_action  = esc_url( rpress_get_checkout_uri( 'confirm=' . 1 ) );
            }
        }
	
	ob_start();
		echo '<div id="rpress_checkout_wrap" class="rpress-section">';
		if ( rpress_get_cart_contents() || rpress_cart_has_fees()  ) : //rpress_get_cart_contents() || rpress_cart_has_fees()

			rpress_checkout_cart();
			$login_method = rpress_get_option( 'login_method', 'login_guest' );
			$login_class = is_user_logged_in() || $login_method == 'guest_only' ? 'rpress-logged-in' : 'rpress-logged-out';
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
			<div id="rpress_checkout_form_wrap" class="rp-col-lg-8 rp-col-md-8 rp-col-sm-12 rp-col-xs-12 <?php echo $login_class; ?>">
				<?php do_action( 'rpress_before_purchase_form' ); ?>
				<form id="rpress_purchase_form" class="rpress_form" action="<?php echo $form_action; ?>" method="POST">
					<?php
					/**
					 * Hooks in at the top of the checkout form
					 *
					 * @since 1.0
					 */
					do_action( 'rpress_checkout_form_top' );

					do_action( 'rpress_purchase_form' );

					do_action( 'rpress_payment_mode_select'  );

					/**
					 * Hooks in at the bottom of the checkout form
					 *
					 * @since 1.0
					 */
					do_action( 'rpress_checkout_form_bottom' )
					?>
				</form>
				<?php do_action( 'rpress_after_purchase_form' ); ?>
			</div><!--end #rpress_checkout_form_wrap-->
		<?php
		else:
			/**
			 * Fires off when there is nothing in the cart
			 *
			 * @since 1.0
			 */
			do_action( 'rpress_cart_empty' );
		endif;
		echo '</div><!--end #rpress_checkout_wrap-->';
	return ob_get_clean();
}


/**
 * Renders the user account link
 *
 * @since  2.5
 * @return string
 */
function rpress_before_purchase_form_address(){
    if(isset($_GET['confirm'])){ ?>
                        <fieldset id="rpress_address_list">
                            <p class="rp-col-md-12 rp-col-sm-12">
                            <h4 class="rpress-apt-suite" style="color: green" for="rpress-apt-suite">
                                    Please confirm your order details 
                            </h4>
                                
                            </p>
                        </fieldset>
        <?php
    }else{
        if(rpress_selected_service() == 'delivery' && isset($_SESSION['user_client_data']) && is_user_logged_in()){ 
        
        $address = get_address_details($_SESSION['user_client_data']->client_id);
        
        ?>

            <fieldset id="rpress_address_list">
                   <p  class="rp-col-md-6 rp-col-sm-12">
                            <label class="rpress-apt-suite" for="rpress-apt-suite">
                                    Address 
                            </label>
                       <select class="form-control" id="rpress-address-changes">
                           <option>Select address from your default</option>
                           <?php if(!empty($address)){ 
                                foreach ($address as $address_value){ ?>
                                    <option data-address1="<?php echo $address_value->street; ?>" data-address2="<?php echo $address_value->city; ?>" data-address3="<?php echo $address_value->state; ?>" data-address_postcode="<?php echo $address_value->zipcode; ?>"><?php echo $address_value->street.' '.$address_value->city.' '.$address_value->state.' '.$address_value->zipcode; ?></option>
                                    <?php 
                                }
                               ?>
                                    
                               <?php
                           }?>
                       </select>
                       
                            <!--<input class="rpress-input" type="text" name="rpress_apt_suite" id="rpress-apt-suite" placeholder="Apartment, suite, unit etc. (optional)" value="8 Loyola Park">-->
                    </p>
                    <p  class="rp-col-md-6 rp-col-sm-12">
  
                        <input type="button" class="btn btn-info add-address-btn" value="Add address" style="float: right;" />
                        <!--<button type="button" class="btn btn-info" data-toggle="modal" style="float: right;" data-target="#myModal">Add address</button>-->
                    </p>
            </fieldset>
                        <!-- Modal -->
  <!-- Modal -->
  
        
        <?php 
    }
    }
}
add_action('rpress_purchase_login_options', 'rpress_checkout_user_account');
add_action('rpress_before_purchase_form', 'rpress_before_purchase_form_address');
function rpress_checkout_user_account() {
	$color = rpress_get_option( 'checkout_color', 'red' );
	$color = ( $color == 'inherit' ) ? '' : $color;
	?>
		<fieldset id="rpress_checkout_login_register" class="rpress-checkout-account-wrap rpress-checkout-block">
			<legend><?php _e('Account', 'restropress'); ?></legend>
			<p><?php _e('To place your order now, log into your existing account or signup now!', 'restropress'); ?></p>
			<div class="clear"></div>
			<div class="rpress-checkout-button-actions">
				<div class="rp-col-md-4 rp-col-lg-4 rp-col-sm-6 rp-col-xs-12">
					<span><?php _e('Have an account?', 'restropress'); ?></span>
					<a href="<?php echo esc_url( add_query_arg( 'login', 1 ) ); ?>" class="rpress_checkout_register_login rpress-submit button <?php echo $color; ?> rp-col-sm-12" data-action="rpress_checkout_login"><?php _e( 'Login', 'restropress' ); ?></a>
				</div>
				<div class="rp-col-md-8 rp-col-sm-6 rp-col-xs-12">
					<span><?php echo sprintf( __( 'New to %s?', 'restropress' ), get_bloginfo( 'name' ) ); ?></span>
					<a href="<?php echo esc_url( remove_query_arg('login') ); ?>" class="rpress_checkout_register_login rpress-submit button <?php echo $color; ?>" data-action="rpress_checkout_register">
						<?php _e( 'Register', 'restropress' ); if(!rpress_no_guest_checkout()) { echo ' ' . __( 'or checkout as a guest', 'restropress' ); } ?>
					</a>
				</div>
			</div>
		</fieldset>
	<?php
}

/**
 * Renders the Purchase Form, hooks are provided to add to the purchase form.
 * The default Purchase Form rendered displays a list of the enabled payment
 * gateways, a user registration form (if enable) and a credit card info form
 * if credit cards are enabled
 *
 * @since  1.0.0
 * @return string
 */
function rpress_show_purchase_form() {

	/**
	 * Hooks in at the top of the purchase form
	 *
	 * @since  1.0.0
	 */
	do_action( 'rpress_purchase_form_top' );

	if ( rpress_can_checkout() ) {
            
		$login_method = rpress_get_option( 'login_method', 'login_guest' );

		if( ! is_user_logged_in() && $login_method != 'guest_only' ){
			do_action( 'rpress_purchase_form_before_register_login' );
			do_action( 'rpress_purchase_login_options' );
		}
		else{
                    if($_COOKIE['service_type'] == 'dinein'){
                        do_action( 'rpress_checkout_dinein_form' );
                    }
			do_action( 'rpress_purchase_form_after_user_info' );
		}

	} else {
		// Can't checkout
		do_action( 'rpress_purchase_form_no_access' );
	}

	/**
	 * Hooks in at the bottom of the purchase form
	 *
	 * @since  1.0.0
	 */
	do_action( 'rpress_purchase_form_bottom' );
}
add_action( 'rpress_purchase_form', 'rpress_show_purchase_form' );
function rpress_checkout_dinein_form(){
    
    ?>
        <fieldset id="rpress_checkout_user_info">
                <legend><?php echo apply_filters('rpress_checkout_personal_info_text', esc_html__('Table Information', 'restropress')); ?></legend>
                <p id="rpress-number-guest-wrap" class="rp-col-md-6 rp-col-sm-12">
                    <label class="rpress-label" for="rpress-guest">
                        <?php esc_html_e('Number of guest', 'restropress'); ?>
                        <?php if (rpress_field_is_required('rpress_number_of_guest')) { ?>
                            <span class="rpress-required-indicator">*</span>
                        <?php } ?>
                    </label>
                    <input class="rpress-input required" type="text" name="rpress_number_of_guest" placeholder="<?php esc_html_e('Number of guest', 'restropress'); ?>" id="rpress_number_of_guest" value=""<?php if (rpress_field_is_required('rpress_number_of_guest')) {
                        echo ' required ';
                    } ?> aria-describedby="rpress_number_of_guest-description" />
                </p>
                <p id="rpress-table-number-wrap" class="rp-col-md-6 rp-col-sm-12">
                    <label class="rpress-label" for="rpress-table-number">
                        <?php esc_html_e('Table Number', 'restropress'); ?>
                        <?php if (rpress_field_is_required('rpress_table_number')) { ?>
                            <span class="rpress-required-indicator">*</span>
        <?php } ?>
                    </label>
                    <input class="rpress-input<?php if (rpress_field_is_required('rpress_table_number')) {
        echo ' required';
        } ?>" type="text" name="rpress_table_number" id="rpress_table_number" placeholder="<?php esc_html_e('Table Number', 'restropress'); ?>" value=""<?php if (rpress_field_is_required('rpress_table_number')) {
        echo ' required ';
        } ?> aria-describedby="rpress-last-description"/>
                </p>
                
            </fieldset>
                        <?php
}
add_action('rpress_checkout_dinein_form','rpress_checkout_dinein_form');
function rpress_show_cc_form() {

	$payment_mode = rpress_get_chosen_gateway();

	/**
	 * Hooks in before Credit Card Form
	 *
	 * @since  1.0.0
	 */
	
	do_action( 'rpress_purchase_form_before_cc_form' );

	if( 1==1 ) { //rpress_get_cart_total() > 0 
		// Load the credit card form and allow gateways to load their own if they wish
		if ( has_action( 'rpress_' . $payment_mode . '_cc_form' ) ) {
			do_action( 'rpress_' . $payment_mode . '_cc_form' );
		} else {
			do_action( 'rpress_cc_form' );
		}
	}

	/**
	 * Hooks in after Credit Card Form
	 *
	 * @since  1.0.0
	 */
	do_action( 'rpress_purchase_form_after_cc_form' );

}

/**
 * Shows the User Info fields in the Personal Info box, more fields can be added
 * via the hooks provided.
 *
 * @since 1.0.0
 * @return void
 */
function rpress_user_info_fields() {
	$customer = RPRESS()->session->get( 'customer' );
	$customer = wp_parse_args( $customer, array( 'first_name' => '', 'last_name' => '', 'email' => '', 'phone'	=> '' ) );

	if( is_user_logged_in() ) {
		$user_data = get_userdata( get_current_user_id() );
		foreach( $customer as $key => $field ) {

			if ( 'email' == $key && empty( $field ) ) {
				$customer[ $key ] = $user_data->user_email;
			} elseif ( empty( $field ) ) {
				$customer[ $key ] = $user_data->$key;
			}

		}
		$customer['phone']	= get_user_meta( get_current_user_id(), '_rpress_phone', true );
	}
	$customer = array_map( 'sanitize_text_field', $customer );
	?>
	<fieldset id="rpress_checkout_user_info">
		<legend><?php echo apply_filters( 'rpress_checkout_personal_info_text', esc_html__( 'Personal Info', 'restropress' ) ); ?></legend>
		<p id="rpress-first-name-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label class="rpress-label" for="rpress-first">
				<?php esc_html_e( 'First Name', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'rpress_first' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<input class="rpress-input required" type="text" name="rpress_first" placeholder="<?php esc_html_e( 'First Name', 'restropress' ); ?>" id="rpress-first" value="<?php echo esc_attr( $customer['first_name'] ); ?>"<?php if( rpress_field_is_required( 'rpress_first' ) ) {  echo ' required '; } ?> aria-describedby="rpress-first-description" />
		</p>
		<p id="rpress-last-name-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label class="rpress-label" for="rpress-last">
				<?php esc_html_e( 'Last Name', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'rpress_last' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<input class="rpress-input<?php if( rpress_field_is_required( 'rpress_last' ) ) { echo ' required'; } ?>" type="text" name="rpress_last" id="rpress-last" placeholder="<?php esc_html_e( 'Last Name', 'restropress' ); ?>" value="<?php echo esc_attr( $customer['last_name'] ); ?>"<?php if( rpress_field_is_required( 'rpress_last' ) ) {  echo ' required '; } ?> aria-describedby="rpress-last-description"/>
		</p>
		<?php do_action( 'rpress_purchase_form_before_email' ); ?>
		<p id="rpress-email-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label class="rpress-label" for="rpress-email">
				<?php esc_html_e( 'Email Address', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'rpress_email' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<input class="rpress-input required" type="email" name="rpress_email" placeholder="<?php esc_html_e( 'Email address', 'restropress' ); ?>" id="rpress-email" value="<?php echo esc_attr( $customer['email'] ); ?>" aria-describedby="rpress-email-description"<?php if( rpress_field_is_required( 'rpress_email' ) ) {  echo ' required '; } ?>/>
		</p>
		<?php do_action( 'rpress_purchase_form_after_email' ); ?>
		<p id="rpress-phone-wrap" class="rp-col-md-6 rp-col-sm-12">
      <label class="rpress-label" for="rpress-phone"><?php esc_html_e('Phone Number', 'restropress'); ?><span class="rpress-required-indicator">*</span></label>
      <input class="rpress-input required" type="text" name="rpress_phone" id="rpress-phone" value="<?php echo esc_attr( $customer['phone'] ); ?>" placeholder="<?php esc_html_e('Phone Number', 'restropress'); ?>" maxlength="16" required />
    </p>
		<?php do_action( 'rpress_purchase_form_user_info' ); ?>
		<?php do_action( 'rpress_purchase_form_user_info_fields' ); ?>
	</fieldset>
	<?php
}
add_action( 'rpress_purchase_form_after_user_info', 'rpress_user_info_fields', 10 );
add_action( 'rpress_register_fields_before', 'rpress_user_info_fields' );

function rpress_order_details_fields(){
?>
<!-- Order details fields -->
<fieldset id="rpress_checkout_order_details" style="<?php echo !is_user_logged_in() ? 'display:none;' : '' ?>" >
	<legend><?php echo apply_filters( 'rpress_checkout_order_details_text', esc_html__( 'Order Details', 'restropress' ) ); ?></legend>
	<?php do_action( 'rpress_purchase_form_before_order_details' ); ?>
	<?php
		if( rpress_selected_service() == 'delivery' ) :
			$customer  = RPRESS()->session->get( 'customer' );
			$customer  = wp_parse_args( $customer, array( 'delivery_address' => array(
				'address'		=> '',
				'flat'			=> '',
				'city'    	=> '',
				'postcode'	=> '',
			) ) );

			$customer['delivery_address'] = array_map( 'sanitize_text_field', $customer['delivery_address'] );

			if( is_user_logged_in() ) {

				$user_address = get_user_meta( get_current_user_id(), '_rpress_user_delivery_address', true );

				foreach( $customer['delivery_address'] as $key => $field ) {

					if ( empty( $field ) && ! empty( $user_address[ $key ] ) ) {
						$customer['delivery_address'][ $key ] = $user_address[ $key ];
					} else {
						$customer['delivery_address'][ $key ] = '';
					}
				}
			}
			$customer['delivery_address'] = apply_filters( 'rpress_delivery_address', $customer['delivery_address'] );
                        
	?>
        <p id="rpress-apt-suite" class="rp-col-md-6 rp-col-sm-12">
			<label class="rpress-apt-suite" for="rpress-apt-suite">
				<?php esc_html_e('Address 1', 'restropress'); ?>
                            <span class="rpress-required-indicator">*</span>
			</label>
			<input class="rpress-input" type="text" name="rpress_apt_suite" id="rpress-apt-suite" placeholder="<?php esc_html_e('Apartment, suite, unit etc. (optional)', 'restropress'); ?>" value="<?php echo $customer['delivery_address']['flat']; ?>" <?php  echo isset($_GET['confirm']) ? 'readonly' : ''; ?> />
		</p>
		<p id="rpress-street-address" class="rp-col-md-6 rp-col-sm-12">
			<label class="rpress-street-address" for="rpress-street-address">
				<?php esc_html_e('Address 2', 'restropress') ?>
				<span class="rpress-required-indicator">*</span>
			</label>
                    <input class="rpress-input" type="text" name="rpress_street_address" id="rpress-street-address" placeholder="<?php esc_html_e('Street Address', 'restropress'); ?>" value="<?php echo $customer['delivery_address']['address']; ?>" <?php  echo isset($_GET['confirm']) ? 'readonly' : ''; ?> />
		</p>
		
		<p id="rpress-city" class="rp-col-md-6 rp-col-sm-12">
			<label class="rpress-city" for="rpress-city">
				<?php _e('Address 3', 'restropress') ?>
				<span class="rpress-required-indicator">*</span>
			</label>
			<input class="rpress-input" type="text" name="rpress_city" id="rpress-city" placeholder="<?php _e('Town / City', 'restropress') ?>" value="<?php echo $customer['delivery_address']['city']; ?>" <?php  echo isset($_GET['confirm']) ? 'readonly' : ''; ?> />
		</p>
		<p id="rpress-postcode" class="rp-col-md-6 rp-col-sm-12">
			<label class="rpress-postcode" for="rpress-postcode">
				<?php _e('Postcode', 'restropress') ?>
				<span class="rpress-required-indicator">*</span>
			</label>
			<input class="rpress-input" type="text" name="rpress_postcode" id="rpress-postcode" placeholder="<?php _e('Postcode / ZIP', 'restropress') ?>" value="<?php echo $customer['delivery_address']['postcode']; ?>" <?php  echo isset($_GET['confirm']) ? 'readonly' : ''; ?> />
		</p>
                <?php do_action('rpress_get_shipping'); ?>
	<?php endif; ?>
	<p id="rpress-order-note" class="rp-col-sm-12">
    <label class="rpress-order-note" for="rpress-order-note"><?php echo sprintf( __('%s Instructions', 'restropress'), rpress_selected_service( 'label' ) ); ?></label>
    <textarea name="rpress_order_note" class="rpress-input" rows="5" cols="8" placeholder="<?php echo sprintf( __('Add %s instructions (optional)', 'restropress'), strtolower( rpress_selected_service( 'label' ) ) ); ?>"></textarea>
  </p>
	<?php do_action( 'rpress_purchase_form_order_details' ); ?>
	<?php do_action( 'rpress_purchase_form_order_details_fields' ); ?>
</fieldset>

<?php
}
function rpress_get_shipping($distance_result = false){
    $customer  = RPRESS()->session->get( 'customer' );
    
    $customer  = wp_parse_args( $customer, array( 'delivery_address' => array(
            'address'		=> '',
            'flat'			=> '',
            'city'    	=> '',
            'postcode'	=> '',
    ) ) );

    $customer['delivery_address'] = array_map( 'sanitize_text_field', $customer['delivery_address'] );
    

        if( is_user_logged_in() ) {

                $user_address = get_user_meta( get_current_user_id(), '_rpress_user_delivery_address', true );

                foreach( $customer['delivery_address'] as $key => $field ) {

                        if ( empty( $field ) && ! empty( $user_address[ $key ] ) ) {
                                $customer['delivery_address'][ $key ] = $user_address[ $key ];
                        } else {
                                $customer['delivery_address'][ $key ] = '';
                        }
                }
        }
    $customer['delivery_address'] = apply_filters( 'rpress_delivery_address', $customer['delivery_address'] );

    $address = $customer['delivery_address']['flat'].','. $customer['delivery_address']['address'].','.$customer['delivery_address']['city'].','.$customer['delivery_address']['postcode'];
    
    $lat_long = get_lat_long_of_address($address);
    
    $merchant_details  = get_user_details_auth();
    $client_details = json_decode($merchant_details);

    $latitudeFrom    = $lat_long['lat'];
    $longitudeFrom    = $lat_long['long'];
    $latitudeTo        = $client_details->latitude;
    $longitudeTo    = $client_details->lontitude;
     // Calculate distance between latitude and longitude
    $theta    = $longitudeFrom - $longitudeTo;
    $dist    = sin(deg2rad($latitudeFrom)) * sin(deg2rad($latitudeTo)) +  cos(deg2rad($latitudeFrom)) * cos(deg2rad($latitudeTo)) * cos(deg2rad($theta));
    $dist    = acos($dist);
    $dist    = rad2deg($dist);
    $miles    = $dist * 60 * 1.1515;

    // Convert unit and return distance
    $unit = strtoupper($unit);

        $distance['km'] =  round($miles * 1.609344, 2).' km';

        $distance['mi'] =  round($miles, 2).' miles';
        $distance['km_number'] =  round($miles * 1.609344, 2);

        $distance['mi_number'] =  round($miles, 2);
        $distance['merchant_id'] = isset($client_details->merchant_id) ? $client_details->merchant_id : 2;
        $shiping_charge = get_shipng_charge_by_merchant($distance);
        $allow = false;
        
        if($distance_result == true){
            return $distance;
        }

        foreach ($shiping_charge as $shiping){
            
            if($shiping->shipping_units == 'km'){
                if($distance['km_number'] >= $shiping->distance_from  &&  $distance['km_number'] <= $shiping->distance_to){
                    $distance_price = $shiping->distance_price;
                    $allow = true;
                }
            }elseif($shiping->shipping_units == 'mi'){
                if($shiping->distance_from <= $distance['mi_number'] && $shiping->distance_to >= $distance['mi_number']){
                    $distance_price = $shiping->distance_price;
                    $allow = true;
                }
            }
        }
        if($allow == true){
            
            return $distance_price;
            
        }else{
            return 'null';
        }
}
add_action( 'rpress_purchase_form_after_user_info', 'rpress_order_details_fields', 11 );
add_action( 'rpress_register_fields_after', 'rpress_order_details_fields' );
add_action( 'rpress_get_shipping', 'rpress_get_shipping' );
/**
 * Renders the credit card info form.
 *
 * @since 1.0
 * @return void
 */
function rpress_get_cc_form() {
	ob_start(); ?>

	<?php do_action( 'rpress_before_cc_fields' ); ?>

	<fieldset id="rpress_cc_fields" class="rpress-do-validate">
		<legend><?php _e( 'Credit Card Info', 'restropress' ); ?></legend>
		<?php if( is_ssl() ) : ?>
			<div id="rpress_secure_site_wrapper">
				<span class="padlock">
					<svg class="rpress-icon rpress-icon-lock" xmlns="http://www.w3.org/2000/svg" width="18" height="28" viewBox="0 0 18 28" aria-hidden="true">
						<path d="M5 12h8V9c0-2.203-1.797-4-4-4S5 6.797 5 9v3zm13 1.5v9c0 .828-.672 1.5-1.5 1.5h-15C.672 24 0 23.328 0 22.5v-9c0-.828.672-1.5 1.5-1.5H2V9c0-3.844 3.156-7 7-7s7 3.156 7 7v3h.5c.828 0 1.5.672 1.5 1.5z"/>
					</svg>
				</span>
				<span><?php _e( 'This is a secure SSL encrypted payment.', 'restropress' ); ?></span>
			</div>
		<?php endif; ?>
		<p id="rpress-card-number-wrap rp-col-sm-12">
			<label for="card_number" class="rpress-label">
				<?php _e( 'Card Number', 'restropress' ); ?>
				<span class="rpress-required-indicator">*</span>
				<span class="card-type"></span>
			</label>
			<span class="rpress-description"><?php _e( 'The (typically) 16 digits on the front of your credit card.', 'restropress' ); ?></span>
			<input type="tel" pattern="^[0-9!@#$%^&* ]*$" autocomplete="off" name="card_number" id="card_number" class="card-number rpress-input required" placeholder="<?php _e( 'Card number', 'restropress' ); ?>" />
		</p>
		<p id="rpress-card-cvc-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label for="card_cvc" class="rpress-label">
				<?php _e( 'CVC', 'restropress' ); ?>
				<span class="rpress-required-indicator">*</span>
			</label>
			<span class="rpress-description"><?php _e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'restropress' ); ?></span>
			<input type="tel" pattern="[0-9]{3,4}" size="4" maxlength="4" autocomplete="off" name="card_cvc" id="card_cvc" class="card-cvc rpress-input required" placeholder="<?php _e( 'Security code', 'restropress' ); ?>" />
		</p>
		<p id="rpress-card-name-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label for="card_name" class="rpress-label">
				<?php _e( 'Name on the Card', 'restropress' ); ?>
				<span class="rpress-required-indicator">*</span>
			</label>
			<span class="rpress-description"><?php _e( 'The name printed on the front of your credit card.', 'restropress' ); ?></span>
			<input type="text" autocomplete="off" name="card_name" id="card_name" class="card-name rpress-input required" placeholder="<?php _e( 'Card name', 'restropress' ); ?>" />
		</p>
		<?php do_action( 'rpress_before_cc_expiration' ); ?>
		<p class="card-expiration rp-col-sm-12">
			<label for="card_exp_month" class="rpress-label">
				<?php _e( 'Expiration (MM/YY)', 'restropress' ); ?>
				<span class="rpress-required-indicator">*</span>
			</label>
			<span class="rpress-description"><?php _e( 'The date your credit card expires, typically on the front of the card.', 'restropress' ); ?></span>
			<select id="card_exp_month" name="card_exp_month" class="card-expiry-month rpress-select rpress-select-small required rp-form-control">
				<?php for( $i = 1; $i <= 12; $i++ ) { echo '<option value="' . $i . '">' . sprintf ('%02d', $i ) . '</option>'; } ?>
			</select>
			<span class="exp-divider"> / </span>
			<select id="card_exp_year" name="card_exp_year" class="card-expiry-year rpress-select rpress-select-small required rp-form-control">
				<?php for( $i = date('Y'); $i <= date('Y') + 30; $i++ ) { echo '<option value="' . $i . '">' . substr( $i, 2 ) . '</option>'; } ?>
			</select>
		</p>
		<?php do_action( 'rpress_after_cc_expiration' ); ?>

	</fieldset>
	<?php
	do_action( 'rpress_after_cc_fields' );

	echo ob_get_clean();
}
add_action( 'rpress_cc_form', 'rpress_get_cc_form' );

/**
 * Outputs the default credit card address fields
 *
 * @since 1.0
 * @return void
 */
function rpress_default_cc_address_fields() {

	$logged_in = is_user_logged_in();
	$customer  = RPRESS()->session->get( 'customer' );
	$customer  = wp_parse_args( $customer, array( 'address' => array(
		'line1'   => '',
		'line2'   => '',
		'city'    => '',
		'zip'     => '',
		'state'   => '',
		'country' => ''
	) ) );

	$customer['address'] = array_map( 'sanitize_text_field', $customer['address'] );

	if( $logged_in ) {

		$user_address = get_user_meta( get_current_user_id(), '_rpress_user_address', true );

		foreach( $customer['address'] as $key => $field ) {

			if ( empty( $field ) && ! empty( $user_address[ $key ] ) ) {
				$customer['address'][ $key ] = $user_address[ $key ];
			} else {
				$customer['address'][ $key ] = '';
			}

		}

	}

	/**
	 * Billing Address Details.
	 *
	 * Allows filtering the customer address details that will be pre-populated on the checkout form.
	 *
	 * @since 1.0.0
	 *
	 * @param array $address The customer address.
	 * @param array $customer The customer data from the session
	 */
	$customer['address'] = apply_filters( 'rpress_checkout_billing_details_address', $customer['address'], $customer );

	ob_start(); ?>
	<fieldset id="rpress_cc_address" class="cc-address">
		<legend><?php _e( 'Billing Details', 'restropress' ); ?></legend>
		<?php do_action( 'rpress_cc_billing_top' ); ?>
		<p id="rpress-card-address-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label for="card_address" class="rpress-label">
				<?php _e( 'Billing Address', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'card_address' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="rpress-description"><?php _e( 'The primary billing address for your credit card.', 'restropress' ); ?></span>
			<input type="text" id="card_address" name="card_address" class="card-address rpress-input<?php if( rpress_field_is_required( 'card_address' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Address line 1', 'restropress' ); ?>" value="<?php echo $customer['address']['line1']; ?>"<?php if( rpress_field_is_required( 'card_address' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="rpress-card-address-2-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label for="card_address_2" class="rpress-label">
				<?php _e( 'Billing Address Line 2 (optional)', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'card_address_2' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="rpress-description"><?php _e( 'The suite, apt no, etc, associated with your billing address.', 'restropress' ); ?></span>
			<input type="text" id="card_address_2" name="card_address_2" class="card-address-2 rpress-input<?php if( rpress_field_is_required( 'card_address_2' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Address line 2', 'restropress' ); ?>" value="<?php echo $customer['address']['line2']; ?>"<?php if( rpress_field_is_required( 'card_address_2' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="rpress-card-city-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label for="card_city" class="rpress-label">
				<?php _e( 'Billing City', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'card_city' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="rpress-description"><?php _e( 'The city for your billing address.', 'restropress' ); ?></span>
			<input type="text" id="card_city" name="card_city" class="card-city rpress-input<?php if( rpress_field_is_required( 'card_city' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'City', 'restropress' ); ?>" value="<?php echo $customer['address']['city']; ?>"<?php if( rpress_field_is_required( 'card_city' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="rpress-card-zip-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label for="card_zip" class="rpress-label">
				<?php _e( 'Billing Zip / Postal Code', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'card_zip' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="rpress-description"><?php _e( 'The zip or postal code for your billing address.', 'restropress' ); ?></span>
			<input type="text" size="4" id="card_zip" name="card_zip" class="card-zip rpress-input<?php if( rpress_field_is_required( 'card_zip' ) ) { echo ' required'; } ?>" placeholder="<?php _e( 'Zip / Postal Code', 'restropress' ); ?>" value="<?php echo $customer['address']['zip']; ?>"<?php if( rpress_field_is_required( 'card_zip' ) ) {  echo ' required '; } ?>/>
		</p>
		<p id="rpress-card-country-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label for="billing_country" class="rpress-label">
				<?php _e( 'Billing Country', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'billing_country' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="rpress-description"><?php _e( 'The country for your billing address.', 'restropress' ); ?></span>
			<select name="billing_country" id="billing_country" class="billing_country rp-form-control <?php if( rpress_field_is_required( 'billing_country' ) ) { echo ' required'; } ?>"<?php if( rpress_field_is_required( 'billing_country' ) ) {  echo ' required '; } ?>>
				<?php

				$selected_country = rpress_get_shop_country();

				if( ! empty( $customer['address']['country'] ) && '*' !== $customer['address']['country'] ) {
					$selected_country = $customer['address']['country'];
				}

				$countries = rpress_get_country_list();
				foreach( $countries as $country_code => $country ) {
				  echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
				}
				?>
			</select>
		</p>
		<p id="rpress-card-state-wrap" class="rp-col-md-6 rp-col-sm-12">
			<label for="card_state" class="rpress-label">
				<?php _e( 'Billing State / Province', 'restropress' ); ?>
				<?php if( rpress_field_is_required( 'card_state' ) ) { ?>
					<span class="rpress-required-indicator">*</span>
				<?php } ?>
			</label>
			<span class="rpress-description"><?php _e( 'The state or province for your billing address.', 'restropress' ); ?></span>
			<?php
			$selected_state = rpress_get_shop_state();
			$states         = rpress_get_states( $selected_country );

			if( ! empty( $customer['address']['state'] ) ) {
				$selected_state = $customer['address']['state'];
			}

			if( ! empty( $states ) ) : ?>
			<select name="card_state" id="card_state" class="card_state rp-form-control <?php if( rpress_field_is_required( 'card_state' ) ) { echo ' required'; } ?>">
				<?php
					foreach( $states as $state_code => $state ) {
						echo '<option value="' . $state_code . '"' . selected( $state_code, $selected_state, false ) . '>' . $state . '</option>';
					}
				?>
			</select>
			<?php else : ?>
			<?php $customer_state = ! empty( $customer['address']['state'] ) ? $customer['address']['state'] : ''; ?>
			<input type="text" size="6" name="card_state" id="card_state" class="card_state rpress-input" value="<?php echo esc_attr( $customer_state ); ?>" placeholder="<?php _e( 'State / Province', 'restropress' ); ?>"/>
			<?php endif; ?>
		</p>
		<?php do_action( 'rpress_cc_billing_bottom' ); ?>
	</fieldset>
	<?php
	echo ob_get_clean();
}
add_action( 'rpress_after_cc_fields', 'rpress_default_cc_address_fields' );


/**
 * Renders the billing address fields for cart taxation
 *
 * @since  1.0.0
 * @return void
 */
function rpress_checkout_tax_fields() {
	//echo "sdsd"; die;
	if( rpress_cart_needs_tax_address_fields() && rpress_get_cart_total() && rpress_show_billing_fields() )
		rpress_default_cc_address_fields();
}
add_action( 'rpress_purchase_form_after_cc_form', 'rpress_checkout_tax_fields', 999 );


/**
 * Renders the user registration fields. If the user is logged in, a login
 * form is displayed other a registration form is provided for the user to
 * create an account.
 *
 * @since 1.0
 * @return string
 */
function rpress_get_register_fields() {
	ob_start(); ?>
	<div id="rpress_register_fields">

		<p id="rpress-login-account-wrap"><?php _e( 'Already have an account?', 'restropress' ); ?> <a href="<?php echo esc_url( add_query_arg( 'login', 1 ) ); ?>" class="rpress_checkout_register_login" data-action="rpress_checkout_login"><?php _e( 'Login', 'restropress' ); ?></a></p>

		<?php do_action('rpress_register_fields_before'); ?>

		<fieldset id="rpress_register_account_fields">
			<legend><?php _e( 'Create an account', 'restropress' ); if( !rpress_no_guest_checkout() ) {  } //echo ' ' . __( '(optional)', 'restropress' ); ?></legend>
			<?php do_action('rpress_register_account_fields_before'); ?>
			<p id="rpress-user-login-wrap" class="rp-col-md-6 rp-col-sm-12">
				<label for="rpress_user_login">
					<?php _e( 'Username', 'restropress' ); ?>
					<?php if( rpress_no_guest_checkout() ) { ?>
					<span class="rpress-required-indicator">*</span>
					<?php } ?>
				</label>
				<span class="rpress-description"><?php _e( 'The username you will use to log into your account.', 'restropress' ); ?></span>
				<input name="rpress_user_login" id="rpress_user_login" required="" class="<?php if(rpress_no_guest_checkout()) { echo 'required '; } ?>rpress-input" type="text" placeholder="<?php _e( 'Username', 'restropress' ); ?>"/>
			</p>
			<p id="rpress-user-pass-wrap" class="rp-col-md-6 rp-col-sm-12">
				<label for="rpress_user_pass">
					<?php _e( 'Password', 'restropress' ); ?>
					<?php if( rpress_no_guest_checkout() ) { ?>
					<span class="rpress-required-indicator">*</span>
					<?php } ?>
				</label>
				<span class="rpress-description"><?php _e( 'The password used to access your account.', 'restropress' ); ?></span>
				<input name="rpress_user_pass" id="rpress_user_pass" required="" class="<?php if(rpress_no_guest_checkout()) { echo 'required '; } ?>rpress-input" placeholder="<?php _e( 'Password', 'restropress' ); ?>" type="password"/>
			</p>
			<?php do_action( 'rpress_register_account_fields_after' ); ?>
		</fieldset>

		<?php do_action('rpress_register_fields_after'); ?>

		<input type="hidden" name="rpress-purchase-var" value="needs-to-register"/>
          
		<?php do_action( 'rpress_purchase_form_user_info' ); ?>
		<?php do_action( 'rpress_purchase_form_user_register_fields' ); ?>

	</div>
	<?php
	echo ob_get_clean();
}
add_action( 'rpress_purchase_form_register_fields', 'rpress_get_register_fields' );

/**
 * Gets the login fields for the login form on the checkout. This function hooks
 * on the rpress_purchase_form_login_fields to display the login form if a user already
 * had an account.
 *
 * @since 1.0
 * @return string
 */
function rpress_get_login_fields() {
	$color = rpress_get_option( 'checkout_color', 'red' );
	$color = ( $color == 'inherit' ) ? '' : $color;
	$style = rpress_get_option( 'button_style', 'button' );

	ob_start(); ?>
		<fieldset id="rpress_login_fields">
				<p id="rpress-new-account-wrap">
					<?php _e( 'Need to create an account?', 'restropress' ); ?>
					<a href="<?php echo esc_url( remove_query_arg('login') ); ?>" class="rpress_checkout_register_login" data-action="rpress_checkout_register">
						<?php _e( 'Register', 'restropress' ); if(!rpress_no_guest_checkout()) { echo ' ' . __( 'or checkout as a guest', 'restropress' ); } ?>
					</a>
				</p>
			<?php do_action('rpress_checkout_login_fields_before'); ?>
			<p id="rpress-user-login-wrap" class="rp-col-md-6 rp-col-sm-12">
				<label class="rpress-label" for="rpress-username">
					<?php _e( 'Email', 'restropress' ); ?>
					<?php if( rpress_no_guest_checkout() ) { ?>
					<span class="rpress-required-indicator">*</span>
					<?php } ?>
				</label>
				<input class="<?php if(rpress_no_guest_checkout()) { echo 'required '; } ?>rpress-input" type="text" name="rpress_user_login" id="rpress_user_login" value="" placeholder="<?php _e( 'Your email address', 'restropress' ); ?>"/>
			</p>
			<p id="rpress-user-pass-wrap" class="rp-col-md-6 rp-col-sm-12 rpress_login_password">
				<label class="rpress-label" for="rpress-password">
					<?php _e( 'Password', 'restropress' ); ?>
					<?php if( rpress_no_guest_checkout() ) { ?>
					<span class="rpress-required-indicator">*</span>
					<?php } ?>
				</label>
				<input class="<?php if( rpress_no_guest_checkout() ) { echo 'required '; } ?>rpress-input" type="password" name="rpress_user_pass" id="rpress_user_pass" placeholder="<?php _e( 'Your password', 'restropress' ); ?>"/>
				<?php if( rpress_no_guest_checkout() ) : ?>
					<input type="hidden" name="rpress-purchase-var" value="needs-to-login"/>
				<?php endif; ?>
			</p>
			<p id="rpress-user-login-submit">
				<input type="submit" class="rpress-submit button <?php echo $color; ?>" name="rpress_login_submit" value="<?php _e( 'Login', 'restropress' ); ?>"/>
			</p>
			<?php do_action('rpress_checkout_login_fields_after'); ?>
		</fieldset><!--end #rpress_login_fields-->
	<?php
	echo ob_get_clean();
}
add_action( 'rpress_purchase_form_login_fields', 'rpress_get_login_fields' );

/**
 * Renders the payment mode form by getting all the enabled payment gateways and
 * outputting them as radio buttons for the user to choose the payment gateway. If
 * a default payment gateway has been chosen from the RPRESS Settings, it will be
 * automatically selected.
 *
 * @since  1.0.0
 * @return void
 */
function rpress_payment_mode_select() {
    if(isset($_GET['confirm']) || $_COOKIE['service_type'] != 'delivery' || !is_user_logged_in()){
	$gateways = rpress_get_enabled_payment_gateways( true );
	$page_URL = rpress_get_current_page_url();
	$chosen_gateway = rpress_get_chosen_gateway();
	?>
                <div  style="<?php echo !is_user_logged_in() ? 'display: none' : '' ?>">
                    <div id="rpress_payment_mode_select_wrap">
		<?php do_action('rpress_payment_mode_top'); ?>
		<?php if( rpress_is_ajax_disabled() ) { ?>
		<form id="rpress_payment_mode" action="<?php echo $page_URL; ?>" method="GET">
		<?php } ?>
			<fieldset id="rpress_payment_mode_select">
				<legend><?php _e( 'Select Payment Method', 'restropress' ); ?></legend>
				<?php do_action( 'rpress_payment_mode_before_gateways_wrap' ); ?>
				<div id="rpress-payment-mode-wrap">
					<?php

					do_action( 'rpress_payment_mode_before_gateways' );

					foreach ( $gateways as $gateway_id => $gateway ) :

						$label         = apply_filters( 'rpress_gateway_checkout_label_' . $gateway_id, $gateway['checkout_label'] );
						$checked       = checked( $gateway_id, $chosen_gateway, false );
						$checked_class = $checked ? ' rpress-gateway-option-selected' : '';

						echo '<label for="rpress-gateway-' . esc_attr( $gateway_id ) . '" class="rpress-gateway-option' . $checked_class . '" id="rpress-gateway-option-' . esc_attr( $gateway_id ) . '">';
							echo '<input type="radio" name="payment-mode" class="rpress-gateway" id="rpress-gateway-' . esc_attr( $gateway_id ) . '" value="' . esc_attr( $gateway_id ) . '"' . $checked . '>' . esc_html( $label );
							echo '<div class="control__indicator">';
							echo '</div>';
						echo '</label>';

					endforeach;

					do_action( 'rpress_payment_mode_after_gateways' );

					?>
				</div>
				<?php do_action( 'rpress_payment_mode_after_gateways_wrap' ); ?>
			</fieldset>
			<fieldset id="rpress_payment_mode_submit" class="rpress-no-js">
				<p id="rpress-next-submit-wrap">
					<?php echo rpress_checkout_button_next(); ?>
				</p>
			</fieldset>
		<?php if( rpress_is_ajax_disabled() ) { ?>
		</form>
		<?php } ?>
	</div>
                </div>
                
	<?php do_action('rpress_after_payment_gateways'); ?>
	<div id="rpress_purchase_form_wrap"></div><!-- the checkout fields are loaded into this-->

	<?php do_action('rpress_payment_mode_bottom');
    }else if( is_user_logged_in()){
         do_action('rpress_confirm_button_next'); 
    }
}
add_action( 'rpress_payment_mode_select', 'rpress_payment_mode_select' );


/**
 * Show Payment Icons by getting all the accepted icons from the RPRESS Settings
 * then outputting the icons.
 *
 * @since 1.0
 * @return void
*/
function rpress_show_payment_icons() {

	$payment_methods = rpress_get_option( 'accepted_cards', array() );

	if( empty( $payment_methods ) ) {
		return;
	}

	echo '<fieldset id="rpress_payment_icons">';
	echo '<legend>'.__('Accepted Cards', 'restropress').'</legend>';
	echo '<div class="rpress-payment-icons">';

	foreach( $payment_methods as $key => $card ) {

		if( rpress_string_is_image_url( $key ) ) {

			echo '<img class="payment-icon" src="' . esc_url( $key ) . '"/>';

		} else {

			$card = strtolower( str_replace( ' ', '', $card ) );

			if( has_filter( 'rpress_accepted_payment_' . $card . '_image' ) ) {

				$image = apply_filters( 'rpress_accepted_payment_' . $card . '_image', '' );

			} else {

				$image = rpress_locate_template( 'images' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $card . '.png', false );

				// Replaces backslashes with forward slashes for Windows systems
				$plugin_dir  = wp_normalize_path( WP_PLUGIN_DIR );
				$content_dir = wp_normalize_path( WP_CONTENT_DIR );
				$image       = wp_normalize_path( $image );

				$image = str_replace( $plugin_dir, WP_PLUGIN_URL, $image );
				$image = str_replace( $content_dir, WP_CONTENT_URL, $image );

			}

			if( rpress_is_ssl_enforced() || is_ssl() ) {

				$image = rpress_enforced_ssl_asset_filter( $image );

			}

			echo '<img class="payment-icon" src="' . esc_url( $image ) . '"/>';
		}

	}

	echo '</div>';
	echo '</fieldset>';

}
add_action( 'rpress_after_payment_gateways', 'rpress_show_payment_icons' );


/**
 * Renders the Discount Code field which allows users to enter a discount code.
 * This field is only displayed if there are any active discounts on the site else
 * it's not displayed.
 *
 * @since  1.0.0
 * @return void
*/
function rpress_discount_field() {

	if( isset( $_GET['payment-mode'] ) && rpress_is_ajax_disabled() ) {
		return; // Only show before a payment method has been selected if ajax is disabled
	}

	if( ! rpress_is_checkout() ) {
		return;
	}

	if ( rpress_has_active_discounts() && rpress_get_cart_total() ) :

		$color = rpress_get_option( 'checkout_color', 'red' );
		$color = ( $color == 'inherit' ) ? '' : $color;
		$style = rpress_get_option( 'button_style', 'button' );
?>
		<fieldset id="rpress_discount_code">
			<p id="rpress_show_discount" style="display:none;">
				<?php _e( 'Have a discount code?', 'restropress' ); ?> <a href="#" class="rpress_discount_link"><?php echo _x( 'Click to enter it', 'Entering a discount code', 'restropress' ); ?></a>
			</p>
			<p id="rpress-discount-code-wrap" class="rpress-cart-adjustment">
				<label class="rpress-label" for="rpress-discount">
					<?php _e( 'Discount', 'restropress' ); ?>
				</label>
				<span class="rpress-description"><?php _e( 'Enter a voucher code if you have one.', 'restropress' ); ?></span>
				<span class="rpress-discount-code-field-wrap">
					<input class="rpress-input" type="text" id="rpress-discount" name="rpress-discount" placeholder="<?php _e( 'Enter voucher code', 'restropress' ); ?>"/>
					<input type="submit" class="rpress-apply-discount rpress-submit <?php echo $color . ' ' . $style; ?>" value="<?php echo _x( 'Apply', 'Apply discount at checkout', 'restropress' ); ?>"/>
				</span>

				<span id="rpress-discount-error-wrap" class="rpress_error rpress-alert rpress-alert-error" aria-hidden="true" style="display:none;"></span>
			</p>
		</fieldset>
<?php
	endif;
}
add_action( 'rpress_checkout_form_top', 'rpress_discount_field', -1 );

/**
 * Renders the Checkout Agree to Terms, this displays a checkbox for users to
 * agree the T&Cs set in the RPRESS Settings. This is only displayed if T&Cs are
 * set in the RPRESS Settings.
 *
 * @since 1.0
 * @return void
 */
function rpress_terms_agreement() {
	if ( rpress_get_option( 'show_agree_to_terms' ) ) {
		$agree_text  = rpress_get_option( 'agree_text' );
		$agree_label = rpress_get_option( 'agree_label', __( 'Agree to Terms?', 'restropress' ) );

		ob_start();
	?>
		<fieldset id="rpress_terms_agreement">
			<div id="rpress_terms" class="rpress-terms" style="display:none;">
				<?php
					do_action( 'rpress_before_terms' );
					echo wpautop( stripslashes( $agree_text ) );
					do_action( 'rpress_after_terms' );
				?>
			</div>
			<div id="rpress_show_terms" class="rpress-show-terms">
				<a href="#" class="rpress_terms_links"><?php _e( 'Show Terms', 'restropress' ); ?></a>
				<a href="#" class="rpress_terms_links" style="display:none;"><?php _e( 'Hide Terms', 'restropress' ); ?></a>
			</div>

			<div class="rpress-terms-agreement">
				<input name="rpress_agree_to_terms" class="required" type="checkbox" id="rpress_agree_to_terms" value="1"/>
				<label for="rpress_agree_to_terms"><?php echo stripslashes( $agree_label ); ?></label>
			</div>
		</fieldset>
<?php
		$html_output = ob_get_clean();

		echo apply_filters( 'rpress_checkout_terms_agreement_html', $html_output );
	}
}
add_action( 'rpress_purchase_form_before_submit', 'rpress_terms_agreement' );


/**
 * Shows the final purchase total at the bottom of the checkout page
 *
 * @since 1.0
 * @return void
 */
function rpress_checkout_final_total() {
	//$subtotal = rpress_get_cart_subtotal();
	//$total = rpress_get_cart_total();
	//rpress_cart_total()
?>
<p id="rpress_final_total_wrap" style="display:none;">
	<strong><?php _e( 'Order Total:', 'restropress' ); ?></strong>
	<span class="rpress_cart_amount" data-subtotal="<?php echo $subtotal; ?>" data-total="<?php echo $subtotal; ?>"><?php $subtotal; ?></span>
</p>
<?php
}
add_action( 'rpress_purchase_form_before_submit', 'rpress_checkout_final_total', 999 );


/**
 * Renders the Checkout Submit section
 *
 * @since 1.0.0
 * @return void
 */
function rpress_checkout_submit() {
?>
	<fieldset id="rpress_purchase_submit">
		<?php do_action( 'rpress_purchase_form_before_submit' ); ?>

		<?php rpress_checkout_hidden_fields(); ?>

		<?php echo rpress_checkout_button_purchase(); ?>

		<?php do_action( 'rpress_purchase_form_after_submit' ); ?>

		<?php if ( rpress_is_ajax_disabled() ) { ?>
			<p class="rpress-cancel"><a href="<?php echo rpress_get_checkout_uri(); ?>"><?php _e( 'Go back', 'restropress' ); ?></a></p>
		<?php } ?>
	</fieldset>
<?php
}
add_action( 'rpress_purchase_form_after_cc_form', 'rpress_checkout_submit', 9999 );

/**
 * Renders the Next button on the Checkout
 *
 * @since 1.0.0
 * @return string
 */
function rpress_checkout_button_next() {
	$color = rpress_get_option( 'checkout_color', 'red' );
	$color = ( $color == 'inherit' ) ? '' : $color;
	$style = rpress_get_option( 'button_style', 'button' );
	$purchase_page = rpress_get_option( 'purchase_page', '0' );

	ob_start();
?>
	<input type="hidden" name="rpress_action" value="gateway_select" />
	<input type="hidden" name="page_id" value="<?php echo absint( $purchase_page ); ?>"/>
	<input type="submit" name="gateway_submit" id="rpress_next_button" class="rpress-submit <?php echo $color; ?> <?php echo $style; ?>" value="<?php _e( 'Next', 'restropress' ); ?>"/>
<?php
	return apply_filters( 'rpress_checkout_button_next', ob_get_clean() );
}
function rpress_confirm_button_next(){ 
    $color = rpress_get_option( 'checkout_color', 'red' );
    $style = rpress_get_option( 'button_style', 'button' );
    if(rpress_get_shipping() == 'null' && rpress_selected_service() == 'delivery' && is_user_logged_in() ){ ?>
        <h4 style="color: red;">We are not Deliver on this address.!</h4>
        <input type="button" name="confirm_submit" id="rpress_next_button" class="rpress-submit <?php echo $color; ?> <?php echo $style; ?>" value="<?php _e( 'Next', 'restropress' ); ?>"/>
    <?php }else{ ?>
        <input type="button" name="confirm_submit" id="rpress_next_button" class="rpress-submit <?php echo $color; ?> <?php echo $style; ?>" value="<?php _e( 'Next', 'restropress' ); ?>"/>
    <?php } ?>
        
    <?php
}
add_action('rpress_confirm_button_next','rpress_confirm_button_next');
/**
 * Renders the Purchase button on the Checkout
 *
 * @since 1.0.0
 * @return string
 */
function rpress_checkout_button_purchase() {
	$color = rpress_get_option( 'checkout_color', 'red' );
	$color = ( $color == 'inherit' ) ? '' : $color;
	$style = rpress_get_option( 'button_style', 'button' );
	$label = rpress_get_checkout_button_purchase_label();

	ob_start();
        
        if(rpress_get_shipping() == 'null' && rpress_selected_service() == 'delivery' && is_user_logged_in() ){ ?>
        <h4 style="color: red;">We are not Deliver on this address.!</h4>
        <input type="button" class="rpress-submit <?php echo $color; ?> <?php echo $style; ?>" id="rpress-update-address" name="rpress-update-address" value="Update Address"/>
            <?php
        }else if(!is_user_logged_in()){ ?>
            <input type="submit" class="rpress-submit <?php echo $color; ?> <?php echo $style; ?>" id="rpress-purchase-button" name="rpress-purchase" value="SignUp"/>
            <?php
        }else{
?>
	<input type="submit" class="rpress-submit <?php echo $color; ?> <?php echo $style; ?>" id="rpress-purchase-button" name="rpress-purchase" value="<?php echo $label; ?>"/>
<?php }
	return apply_filters( 'rpress_checkout_button_purchase', ob_get_clean() );
}

/**
 * Retrieves the label for the place order button
 *
 * @since 1.0.0
 * @return string
 */
function rpress_get_checkout_button_purchase_label() {

	$label             = rpress_get_option( 'checkout_label', '' );
	$complete_purchase = '';
	if ( rpress_get_cart_total() ) {
		$complete_purchase = ! empty( $label ) ? $label : __( 'Place Order', 'restropress' );
	}

	return apply_filters( 'rpress_get_checkout_button_purchase_label', $complete_purchase, $label );
}

/**
 * Outputs the JavaScript code for the Agree to Terms section to toggle
 * the T&Cs text
 *
 * @since 1.0
 * @return void
 */
function rpress_agree_to_terms_js() {
	if ( rpress_get_option( 'show_agree_to_terms', false ) || rpress_get_option( 'show_agree_to_privacy_policy', false ) ) {
?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$( document.body ).on('click', '.rpress_terms_links', function(e) {
				//e.preventDefault();
				$(this).parent().prev('.rpress-terms').slideToggle();
				$(this).parent().find('.rpress_terms_links').toggle();
				return false;
			});
		});
	</script>
<?php
	}
}
add_action( 'rpress_checkout_form_top', 'rpress_agree_to_terms_js' );

/**
 * Renders the hidden Checkout fields
 *
 * @since 1.0
 * @return void
 */
function rpress_checkout_hidden_fields() {
?>
	<?php if ( is_user_logged_in() ) { ?>
	<input type="hidden" name="rpress-user-id" value="<?php echo get_current_user_id(); ?>"/>
	<?php } ?>
	<input type="hidden" name="rpress_action" value="purchase"/>
	<input type="hidden" name="rpress-gateway" value="<?php echo rpress_get_chosen_gateway(); ?>" />
<?php
}

/**
 * Filter Success Page Content
 *
 * Applies filters to the success page content.
 *
 * @since 1.0
 * @param string $content Content before filters
 * @return string $content Filtered content
 */
function rpress_filter_success_page_content( $content ) {
	if ( isset( $_GET['payment-confirmation'] ) && rpress_is_success_page() ) {
		if ( has_filter( 'rpress_payment_confirm_' . $_GET['payment-confirmation'] ) ) {
			$content = apply_filters( 'rpress_payment_confirm_' . $_GET['payment-confirmation'], $content );
		}
	}

	return $content;
}
add_filter( 'the_content', 'rpress_filter_success_page_content', 99999 );
