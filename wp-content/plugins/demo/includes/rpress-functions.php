<?php
/**
 * Custom Functions
 *
 * @package     RPRESS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get Cart Items By Key
 *
 * @since       1.0
 * @param       int | key
 * @return      array | cart items array
 */
function rpress_get_cart_items_by_key( $key ) {
  $cart_items_arr = array();
  if( $key !== '' ) {
    $cart_items = rpress_get_cart_contents();
    if( is_array( $cart_items ) && !empty( $cart_items ) ) {
      $items_in_cart = $cart_items[$key];
      if( is_array( $items_in_cart ) ) {
        if( isset( $items_in_cart['addon_items'] ) ) {
          $cart_items_arr = $items_in_cart['addon_items'];
        }
      }
    }
  }
  return $cart_items_arr;
}

/**
 * Get Cart Items Price
 *
 * @since       1.0
 * @param       int | key
 * @return      int | total price for cart
 */
function rpress_get_cart_item_by_price( $key ) {
  $cart_items_price = array();

  if( $key !== '' ) {
    $cart_items = rpress_get_cart_contents();

    if( is_array( $cart_items ) && !empty( $cart_items ) ) {
      $items_in_cart = $cart_items[$key];
      if( is_array( $items_in_cart ) ) {
        $item_price = rpress_get_fooditem_price( $items_in_cart['id'] );

        if( $items_in_cart['quantity'] > 0 ) {
          $item_price = $item_price * $items_in_cart['quantity'];
        }
        array_push( $cart_items_price, $item_price );

        if( isset( $items_in_cart['addon_items'] ) && is_array( $items_in_cart['addon_items'] ) ) {
          foreach( $items_in_cart['addon_items'] as $item_list ) {
            array_push( $cart_items_price, $item_list['price'] );
          }
        }

      }
    }
  }

  $cart_item_total = array_sum($cart_items_price);
  return $cart_item_total;
}

function addon_category_taxonomy_custom_fields($tag) {
  $t_id = $tag->term_id;
  $term_meta = get_option( "taxonomy_term_$t_id" );
  $use_addon_like =  isset($term_meta['use_it_like']) ? $term_meta['use_it_like'] : 'checkbox';
?>
<?php if( $tag->parent != 0 ): ?>
<tr class="form-field">
  <th scope="row" valign="top">
    <label for="price_id"><?php _e('Price'); ?></label>
  </th>
  <td>
    <input type="number" step=".01" name="term_meta[price]" id="term_meta[price]" size="25" style="width:15%;" value="<?php echo $term_meta['price'] ? $term_meta['price'] : ''; ?>"><br />
    <span class="description"><?php _e('Price for this addon item'); ?></span>
  </td>
</tr>
<?php endif; ?>

<?php if( $tag->parent == 0 ): ?>
<tr class="form-field">
  <th scope="row" valign="top">
    <label for="use_it_as">
      <?php _e('Addon item selection type', 'restropress'); ?></label>
  </th>
  <td>
    <div class="use-it-like-wrap">
      <label for="use_like_radio">
        <input id="use_like_radio" type="radio" value="radio" name="term_meta[use_it_like]" <?php checked( $use_addon_like, 'radio'); ?> >
          <?php _e('Single item', 'restropress'); ?>
      </label>
      <br/><br/>
      <label for="use_like_checkbox">
        <input id="use_like_checkbox" type="radio" value="checkbox" name="term_meta[use_it_like]" <?php checked( $use_addon_like, 'checkbox'); ?> >
          <?php _e('Multiple Items', 'restropress'); ?>
      </label>
    </div>
  </td>
</tr>
<?php endif; ?>

<?php
}

/**
 * Update taxonomy meta data
 *
 * @since       1.0
 * @param       int | term_id
 * @return      update meta data
 */
function save_addon_category_custom_fields( $term_id ) {
  if( isset( $_POST['term_meta'] ) ) {
    $t_id = $term_id;
    $term_meta = get_option( "taxonomy_term_$t_id" );
    $cat_keys = array_keys( $_POST['term_meta'] );

    if( is_array( $cat_keys ) && !empty( $cat_keys ) ) {
      foreach ( $cat_keys as $key ){
        if( isset( $_POST['term_meta'][$key] ) ){
          $term_meta[$key] = $_POST['term_meta'][$key];
        }
      }
    }

    //save the option array
    update_option( "taxonomy_term_$t_id", $term_meta );
  }
}

// Add the fields to the "addon_category" taxonomy, using our callback function
add_action( 'addon_category_edit_form_fields', 'addon_category_taxonomy_custom_fields', 10, 2 );

// Save the changes made on the "addon_category" taxonomy, using our callback function
add_action( 'edited_addon_category', 'save_addon_category_custom_fields', 10, 2 );

/**
 * Get food item quantity in the cart by key
 *
 * @since       1.0
 * @param       int | cart_key
 * @return      array | cart items array
 */
function rpress_get_item_qty_by_key( $cart_key ) {
    
  if( $cart_key !== '' ) {
    $cart_items = rpress_get_cart_contents();
    $cart_items = $cart_items[$cart_key];
    return $cart_items['quantity'];
  }
}

add_action( 'wp_footer', 'rpress_popup' );
if( !function_exists('rpress_popup') ) {
  function rpress_popup() {
    rpress_get_template_part( 'rpress', 'popup' );
    rpress_get_template_part('Custome/rpress-popup');
  }
}


add_action( 'rp_get_categories', 'get_fooditems_categories' );

if ( ! function_exists( 'get_fooditems_categories' ) ) {
  function get_fooditems_categories( $params ){
    global $data;
    $data = $params;
    rpress_get_template_part('rpress', 'get-categories');
  }
}

if ( ! function_exists( 'rpress_search_form' ) ) {
  function rpress_search_form() {
    ?>
    <div class="rpress-search-wrap rpress-live-search">
      <input id="rpress-food-search" type="text" placeholder="<?php _e('Search Food Item', 'restropress') ?>">
    </div>
    <?php
  }
}

add_action( 'before_fooditems_list', 'rpress_search_form' );

if ( ! function_exists( 'rpress_product_menu_tab' ) ) {
  /**
   * Output the rpress menu tab content.
   */
  function rpress_product_menu_tab() {
    echo do_shortcode('[rpress_items]');
  }
}

/**
 * Get special instruction for food items
 *
 * @since       1.0
 * @param       array | food items
 * @return      string | Special instruction string
 */
function get_special_instruction( $items ) {
  $instruction = '';

  if( is_array($items) ) {
    if( isset($items['options']) ) {
      $instruction = $items['options']['instruction'];
    } else {
      if( isset($items['instruction']) ) {
        $instruction = $items['instruction'];
      }
    }
  }

  return apply_filters( 'rpress_sepcial_instruction', $instruction );
}

/**
 * Get instruction in the cart by key
 *
 * @since       1.0
 * @param       int | cart_key
 * @return      string | Special instruction string
 */
function rpress_get_instruction_by_key( $cart_key ) {
  $instruction = '';
  if( $cart_key !== '' ) {
    $cart_items = rpress_get_cart_contents();
    $cart_items = $cart_items[$cart_key];
    if( isset($cart_items['instruction']) ) {
      $instruction = !empty($cart_items['instruction']) ? $cart_items['instruction'] : '';
    }
  }
  return $instruction;
}

/**
 * Show delivery options in the cart
 *
 * @since       1.0.2
 * @param       void
 * @return      string | Outputs the html for the delivery options with texts
 */
function get_delivery_options( $changeble ) {
  $color = rpress_get_option( 'checkout_color', 'red' );
  $service_date = isset( $_COOKIE['delivery_date'] ) ? $_COOKIE['delivery_date'] : '';
  ob_start();
  ?>
  <div class="delivery-wrap">
    <div class="delivery-opts">
      <?php if ( !empty( $_COOKIE['service_type'] ) ) : ?>
      <span class="delMethod">
        <?php echo rpress_service_label( $_COOKIE['service_type'] ) . ', ' . $service_date; ?></span>
        <?php if( !empty( $_COOKIE['service_time'] ) ) : ?>
          <span class="delTime">
            <?php echo 'at ' . $_COOKIE['service_time']; ?>
          </span>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <?php if( $changeble && !empty( $_COOKIE['service_type'] ) ) : ?>
      <a href="#" class="delivery-change <?php echo $color; ?>"><?php esc_html_e( 'Change?', 'restropress' ); ?></a>
    <?php endif; ?>
  </div>
  <?php
  $data = ob_get_contents();
  ob_get_clean();
  return $data;
}

/**
 * Stores delivery address meta
 *
 * @since       1.0.3
 * @param       array | Delivery address meta array
 * @return      array | Custom data with delivery address meta array
 */
function rpress_store_custom_fields( $delivery_address_meta ) {
  $delivery_address_meta['address']   = !empty( $_POST['rpress_street_address'] ) ? sanitize_text_field( $_POST['rpress_street_address'] ) : '';
  $delivery_address_meta['flat']      = !empty( $_POST['rpress_apt_suite'] ) ? sanitize_text_field( $_POST['rpress_apt_suite'] ) : '';
  $delivery_address_meta['city']      = !empty( $_POST['rpress_city'] ) ? sanitize_text_field( $_POST['rpress_city'] ) : '';
  $delivery_address_meta['postcode']  = !empty( $_POST['rpress_postcode'] ) ? sanitize_text_field( $_POST['rpress_postcode'] ) : '';
  return $delivery_address_meta;
}
add_filter( 'rpress_delivery_address_meta', 'rpress_store_custom_fields');


/**
* Add order note to the order
*/
add_filter( 'rpress_order_note_meta', 'rpress_order_note_fields' );
function rpress_order_note_fields( $order_note ) {
  $order_note = isset( $_POST['rpress_order_note'] ) ? sanitize_text_field( $_POST['rpress_order_note'] ) : '';
  return $order_note;
}

/**
* Add phone number to payment meta
*/
add_filter( 'rpress_payment_meta', 'rpress_add_phone' );
function rpress_add_phone( $payment_meta ) {
  if( !empty( $_POST['rpress_phone'] ) )
    $payment_meta['phone']  = $_POST['rpress_phone'];
  return $payment_meta;
}

/**
 * Get Service type
 *
 * @since       1.0.4
 * @param       Int | Payment_id
 * @return      string | Service type string
 */
function rpress_get_service_type( $payment_id ) {
  if( $payment_id  ) {
    $service_type = get_post_meta( $payment_id, '_rpress_delivery_type', true );
    return strtolower( $service_type );
  }
}

/* Remove View Link From Food Items */
add_filter('post_row_actions','rpress_remove_view_link', 10, 2);

function rpress_remove_view_link($actions, $post){
  if ($post->post_type =="fooditem"){
    unset($actions['view']);
  }
  return $actions;
}

/* Remove View Link From Food Addon Category */
add_filter('addon_category_row_actions','rpress_remove_tax_view_link', 10, 2);

function rpress_remove_tax_view_link($actions, $taxonomy) {
    if( $taxonomy->taxonomy == 'addon_category' ) {
        unset($actions['view']);
    }
    return $actions;
}

/* Remove View Link From Food Category */
add_filter('food-category_row_actions','rpress_remove_food_cat_view_link', 10, 2);

function rpress_remove_food_cat_view_link($actions, $taxonomy) {
  if( $taxonomy->taxonomy == 'food-category' ) {
    unset($actions['view']);
  }
  return $actions;
}

/**
 * Get store timings for the store
 *
 * @since       1.0.0
 * @return      array | store timings
 */
function rp_get_store_timings( $hide_past_time = true ) {

  $current_time = current_time( 'timestamp' );
  $prep_time = !empty( rpress_get_option( 'prep_time' ) ) ? rpress_get_option( 'prep_time' ) : 0;
  $open_time = !empty( rpress_get_option( 'open_time' ) ) ? rpress_get_option( 'open_time' ) : '9:00am';
  $close_time = !empty( rpress_get_option( 'close_time' ) ) ? rpress_get_option( 'close_time' ) : '11:30pm';

  $time_interval = apply_filters( 'rp_store_time_interval', 30 );
  $time_interval = $time_interval * 60;

  $prep_time  = $prep_time * 60;
  $open_time  = strtotime( date_i18n( 'Y-m-d' ) . ' ' . $open_time );
  $close_time = strtotime( date_i18n( 'Y-m-d' ) . ' ' . $close_time );
  $time_today = apply_filters( 'rpress_timing_for_today', true );

  $store_times = range( $open_time, $close_time, $time_interval );

  //If not today then return normal time
  if( !$time_today ) return $store_times;

  //Add prep time to current time to determine the time to display for the dropdown
  if( $prep_time > 0 ) {
    $current_time = $current_time + $prep_time;
  }
  //Store timings for today.
  $store_timings = [];
  foreach( $store_times as $store_time ) {
    if( $hide_past_time ) {
      if( $store_time > $current_time ) {
        $store_timings[] = $store_time;
      }
    } else {
      $store_timings[] = $store_time;
    }

  }
  return $store_timings;
}

/**
 * Get current time
 *
 * @since       1.0.0
 * @return      string | current time
 */
function rp_get_current_time() {
  $current_time = '';
  $timezone = get_option( 'timezone_string' );
  if( !empty( $timezone ) ) {
    $tz = new DateTimeZone( $timezone );
    $dt = new DateTime( "now", $tz );
    $current_time = $dt->format("H:i:s");
  }
  return $current_time;
}

/**
 * Get current date
 *
 * @since       1.0.0
 * @return      string | current date
 */
function rp_current_date( $format = '' ) {
  $date_format  = empty( $format ) ? get_option( 'date_format' ) : $format;
  $date_i18n = date_i18n( $date_format );
  return apply_filters( 'rpress_current_date', $date_i18n );
}

/**
 * Get local date from date string
 *
 * @since       1.0.0
 * @return      string | localized date based on date string
 */
function rpress_local_date( $date ) {
  $date_format = apply_filters( 'rpress_date_format', get_option( 'date_format', true ) );
  $timestamp  = strtotime( $date );
  $local_date = empty( get_option( 'timezone_string' ) ) ? date_i18n( $date_format, $timestamp ) : wp_date( $date_format, $timestamp );
  return apply_filters( 'rpress_local_date', $local_date, $date );
}

/**
 * Get list of categories
 *
 * @since 2.2.4
 * @return array of categories
 */
function rpress_get_categories( $params = array() ) {

  if( !empty( $params['ids'] ) ) {
    $params['include'] = $params['ids'];
    $params['orderby'] = 'include';
  }

  unset( $params['ids'] );

  $defaults = array(
    'taxonomy'    => 'food-category',
    'hide_empty'  => true,
    'orderby'     => 'name',
    'order'       => 'ASC',
  );
  $term_args = wp_parse_args( $params, $defaults );
  $term_args = apply_filters( 'rpress_get_categories', $term_args );
  $get_all_items = get_terms( $term_args );

  return $get_all_items;
}

function rpress_get_service_types() {
  $service_types = array(
    'delivery'  => __( 'Delivery', 'restropress' ),
    'pickup'    => __( 'Pickup', 'restropress' )
  );
  return apply_filters( 'rpress_service_type', $service_types );
}

/**
* Get Store service hours
* @since 3.0
* @param string $service_type Select service type
* @param bool $current_time_aware if current_time_aware is set true then it would show the next time from now otherwise it would show the default store timings
* @return store time
*/
function rp_get_store_service_hours( $service_type, $current_time_aware = true, $selected_time  ) {

  if ( empty( $service_type ) ) {
    return;
  }

  $time_format = get_option( 'time_format', true );
  $time_format = apply_filters( 'rp_store_time_format', $time_format );

  $current_time = !empty( rp_get_current_time() ) ? rp_get_current_time() : date( $time_format );
  $store_times = rp_get_store_timings( false );

  if ( $service_type == 'delivery' ) {
    $store_timings = apply_filters( 'rpress_store_delivery_timings', $store_times );
  } else {
    $store_timings = apply_filters( 'rpress_store_pickup_timings', $store_times );
  }

  $store_timings_for_today = apply_filters( 'rpress_timing_for_today', true );

  if( is_array( $store_timings ) ) {

    foreach( $store_timings as $time ) {

      // Bring both curent time and Selected time to Admin Time Format
      echo $store_time = date( $time_format, $time );
      $selected_time = date( $time_format, strtotime( $selected_time ) );

      if ( $store_timings_for_today ) {

        // Remove any extra space in Current Time and Selected Time
        $timing_slug = str_replace( ' ', '', $store_time );
        $selected_time = str_replace( ' ', '', $selected_time );

        if( $current_time_aware ) {

          if ( strtotime( $store_time ) > strtotime( $current_time ) ) { ?>

            <option <?php selected( $selected_time, $timing_slug ); ?> value='<?php echo $store_time; ?>'>
              <?php echo $store_time; ?>
            </option>

          <?php }

        } else { ?>

          <option <?php selected( $selected_time, $timing_slug ); ?> value='<?php echo $store_time; ?>'>
            <?php echo $store_time; ?>
          </option>

        <?php }
      }
    }
  }
}

/**
 * Get list of categories/subcategories
 *
 * @since 2.3
 * @return array of Get list of categories/subcategories
 */
function rpress_get_child_cats( $category ) {
  $taxonomy_name = 'food-category';
  $parent_term = $category[0];
  $get_child_terms = get_terms( $taxonomy_name,
      ['child_of'=> $parent_term ] );

  if ( empty( $get_child_terms ) ) {
    $parent_terms = array(
      'taxonomy'    => $taxonomy_name,
      'hide_empty'  => true,
      'include'     => $category,
    );

    $get_child_terms = get_terms( $parent_terms );
  }
  return $get_child_terms;
}

add_filter( 'post_updated_messages', 'rpress_fooditem_update_messages' );
function rpress_fooditem_update_messages( $messages ) {
  global $post, $post_ID;

  $post_types = get_post_types( array( 'show_ui' => true, '_builtin' => false ), 'objects' );

  foreach( $post_types as $post_type => $post_object ) {
    if ( $post_type == 'fooditem' ) {
      $messages[$post_type] = array(
        0  => '', // Unused. Messages start at index 1.
        1  => sprintf( __( '%s updated.' ), $post_object->labels->singular_name ),
        2  => __( 'Custom field updated.' ),
        3  => __( 'Custom field deleted.' ),
        4  => sprintf( __( '%s updated.' ), $post_object->labels->singular_name ),
        5  => isset( $_GET['revision']) ? sprintf( __( '%s restored to revision from %s' ), $post_object->labels->singular_name, wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
        6  => sprintf( __( '%s published.' ), $post_object->labels->singular_name ),
        7  => sprintf( __( '%s saved.' ), $post_object->labels->singular_name ),
        8  => sprintf( __( '%s submitted'), $post_object->labels->singular_name),
        9  => sprintf( __( '%s scheduled for: <strong>%1$s</strong>'), $post_object->labels->singular_name, date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), $post_object->labels->singular_name ),
        10 => sprintf( __( '%s draft updated.'), $post_object->labels->singular_name ),
        );
    }
  }

  return $messages;

}

/**
 * Return the html selected attribute if stringified $value is found in array of stringified $options
 * or if stringified $value is the same as scalar stringified $options.
 *
 * @param string|int       $value   Value to find within options.
 * @param string|int|array $options Options to go through when looking for value.
 * @return string
 */
function rp_selected( $value, $options ) {
  if ( is_array( $options ) ) {
    $options = array_map( 'strval', $options );
    return selected( in_array( (string) $value, $options, true ), true, false );
  }
  return selected( $value, $options, false );
}


/**
 * Return the currently selected service type
 *
 * @since       2.5
 * @param       string | type
 * @return      string | Currently selected service type
 */
function rpress_selected_service( $type = '' ) {
  $service_type = isset( $_COOKIE['service_type'] ) ? $_COOKIE['service_type'] : '';
  //Return service type label when $type is label
  if( $type == 'label' )
    $service_type = rpress_service_label( $service_type );

  return $service_type;
}

/**
 * Return the service type label based on the service slug.
 *
 * @since       2.5
 * @param       string | service type
 * @return      string | Service type label
 */
function rpress_service_label( $service ) {
  $service_types = array(
    'delivery'  => __( 'Delivery', 'restropress' ),
    'pickup'    => __( 'Pickup', 'restropress' ),
  );
  //Allow to filter the service types.
  $service_types = apply_filters( 'rpress_service_types', $service_types );

  //Check for the service key in the service types and return the service type label
  if( array_key_exists( $service, $service_types ) )
    $service = $service_types[$service];

  return $service;
}

/**
 * Save order type in session
 *
 * @since       1.0.4
 * @param       string | Delivery Type
 * @param           string | Delivery Time
 * @return      array  | Session array for delivery type and delivery time
 */
function rpress_checkout_delivery_type( $service_type, $service_time ) {

  $_COOKIE['service_type'] = $service_type;
  $_COOKIE['service_time'] = $service_time;
}

/**
 * Validates the cart before checkout
 *
 * @since       2.5
 * @param       void
 * @return      array | Respose as success/error
 */
function rpress_pre_validate_order(){
  $service_type 	= !empty( $_COOKIE['service_type'] ) ? $_COOKIE['service_type'] : '';
  $service_time 	= !empty( $_COOKIE['service_time'] ) ? $_COOKIE['service_time'] : '';
  $service_date 	= !empty( $_COOKIE['service_date'] ) ? $_COOKIE['service_date'] : current_time( 'Y-m-d' );
  $prep_time 			= rpress_get_option( 'prep_time', 0 );
  $prep_time  		= $prep_time * 60;
  $current_time 	= current_time( 'timestamp' );


  if( $prep_time > 0 ) {
    $current_time = $current_time + $prep_time;
  }

  $service_time = strtotime( $service_date . ' ' . $service_time );

  //Check minimum order
  $enable_minimum_order = rpress_get_option( 'allow_minimum_order' );
  $minimum_order_price_delivery = rpress_get_option('minimum_order_price');
  $minimum_order_price_delivery = floatval( $minimum_order_price_delivery );
  $minimum_order_price_pickup = rpress_get_option( 'minimum_order_price_pickup' );
  $minimum_order_price_pickup = floatval( $minimum_order_price_pickup );


  if ( $enable_minimum_order && $service_type == 'delivery' && rpress_get_cart_subtotal() < $minimum_order_price_delivery ) {
    $minimum_price_error = rpress_get_option('minimum_order_error');
    $minimum_order_formatted = rpress_currency_filter( rpress_format_amount( $minimum_order_price_delivery ) );
    $minimum_price_error = str_replace('{min_order_price}', $minimum_order_formatted, $minimum_price_error);
    $response = array( 'status' => 'error', 'minimum_price' => $minimum_order_price, 'error_msg' =>  $minimum_price_error  );
  }
  else if ( $enable_minimum_order && $service_type == 'pickup' && rpress_get_cart_subtotal() < $minimum_order_price_pickup ) {
    $minimum_price_error_pickup = rpress_get_option('minimum_order_error_pickup');
    $minimum_order_formatted = rpress_currency_filter( rpress_format_amount( $minimum_order_price_pickup ) );
    $minimum_price_error_pickup = str_replace('{min_order_price}', $minimum_order_formatted, $minimum_price_error_pickup);
    $response = array( 'status' => 'error', 'minimum_price' => $minimum_order_price_pickup, 'error_msg' =>  $minimum_price_error_pickup  );
  }
  else if( $current_time > $service_time && !empty( $_COOKIE['service_time'] ) ){
    /*$time_error = __( 'Please select a different time slot.', 'restropress' );
    $response = array(
      'status' => 'error',
      'error_msg' =>  $time_error
    );*/
    $response = array( 'status' => 'success' );
  }
  else {
    $response = array( 'status' => 'success' );
  }
  return $response;
}
