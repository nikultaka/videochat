<?php
/**
 * AJAX Functions
 *
 * Process the front-end AJAX actions.
 *
 * @package     RPRESS
 * @subpackage  Functions/AJAX
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Checks whether AJAX is enabled.
 *
 * This will be deprecated soon in favor of rpress_is_ajax_disabled()
 *
 * @since 1.0
 * @return bool True when RPRESS AJAX is enabled (for the cart), false otherwise.
 */
function rpress_is_ajax_enabled() {
	$retval = ! rpress_is_ajax_disabled();
	return apply_filters( 'rpress_is_ajax_enabled', $retval );
}

/**
 * Checks whether AJAX is disabled.
 *
 * @since  1.0.0
 * @since 1.0 Setting to disable AJAX was removed
 * @return bool True when RPRESS AJAX is disabled (for the cart), false otherwise.
 */
function rpress_is_ajax_disabled() {
	return apply_filters( 'rpress_is_ajax_disabled', false );
}

/**
 * Check if AJAX works as expected
 *
 * @since  1.0.0
 * @return bool True if AJAX works, false otherwise
 */
function rpress_test_ajax_works() {

	// Check if the Airplane Mode plugin is installed
	if ( class_exists( 'Airplane_Mode_Core' ) ) {

		$airplane = Airplane_Mode_Core::getInstance();

		if ( method_exists( $airplane, 'enabled' ) ) {

			if ( $airplane->enabled() ) {
				return true;
			}

		} else {

			if ( $airplane->check_status() == 'on' ) {
				return true;
			}
		}
	}

	add_filter( 'block_local_requests', '__return_false' );

	if ( get_transient( '_rpress_ajax_works' ) ) {
		return true;
	}

	$params = array(
		'sslverify'  => false,
		'timeout'    => 30,
		'body'       => array(
			'action' => 'rpress_test_ajax'
		)
	);

	$ajax  = wp_remote_post( rpress_get_ajax_url(), $params );
	$works = true;

	if ( is_wp_error( $ajax ) ) {

		$works = false;

	} else {

		if( empty( $ajax['response'] ) ) {
			$works = false;
		}

		if( empty( $ajax['response']['code'] ) || 200 !== (int) $ajax['response']['code'] ) {
			$works = false;
		}

		if( empty( $ajax['response']['message'] ) || 'OK' !== $ajax['response']['message'] ) {
			$works = false;
		}

		if( ! isset( $ajax['body'] ) || 0 !== (int) $ajax['body'] ) {
			$works = false;
		}

	}

	if ( $works ) {
		set_transient( '_rpress_ajax_works', '1', DAY_IN_SECONDS );
	}

	return $works;
}

/**
 * Get AJAX URL
 *
 * @since 1.0
 * @return string URL to the AJAX file to call during AJAX requests.
*/
function rpress_get_ajax_url() {
	$scheme = defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ? 'https' : 'admin';

	$current_url = rpress_get_current_page_url();
	$ajax_url    = admin_url( 'admin-ajax.php', $scheme );

	if ( preg_match( '/^https/', $current_url ) && ! preg_match( '/^https/', $ajax_url ) ) {
		$ajax_url = preg_replace( '/^http/', 'https', $ajax_url );
	}

	return apply_filters( 'rpress_ajax_url', $ajax_url );
}

/**
 * Validates the supplied discount sent via AJAX.
 *
 * @since 1.0
 * @return void
 */
function rpress_ajax_update_cart_item_quantity() {
	if ( ! empty( $_POST['quantity'] ) && ! empty( $_POST['fooditem_id'] ) ) {

		$fooditem_id = absint( $_POST['fooditem_id'] );
		$quantity    = absint( $_POST['quantity'] );
		$options     = json_decode( stripslashes( $_POST['options'] ), true );

		RPRESS()->cart->set_item_quantity( $fooditem_id, $quantity, $options );

		$return = array(
			'fooditem_id' => $fooditem_id,
			'quantity'    => RPRESS()->cart->get_item_quantity( $fooditem_id, $options, $quantity ),
			'subtotal'    => html_entity_decode( rpress_currency_filter( rpress_format_amount( RPRESS()->cart->get_subtotal() ) ), ENT_COMPAT, 'UTF-8' ),
			'taxes'       => html_entity_decode( rpress_currency_filter( rpress_format_amount( RPRESS()->cart->get_tax() ) ), ENT_COMPAT, 'UTF-8' ),
			'total'       => html_entity_decode( rpress_currency_filter( rpress_format_amount( RPRESS()->cart->get_total() ) ), ENT_COMPAT, 'UTF-8' )
		);

		// Allow for custom cart item quantity handling
		$return = apply_filters( 'rpress_ajax_cart_item_quantity_response', $return );

		echo json_encode($return);
	}
	rpress_die();
}
add_action( 'wp_ajax_rpress_update_quantity', 'rpress_ajax_update_cart_item_quantity' );
add_action( 'wp_ajax_nopriv_rpress_update_quantity', 'rpress_ajax_update_cart_item_quantity' );


/**
 * Check for Download Price Variations via AJAX (this function can only be used
 * in WordPress Admin). This function is used for the Edit Payment screen when fooditems
 * are added to the purchase. When each fooditem is chosen, an AJAX call is fired
 * to this function which will check if variable prices exist for that fooditem.
 * If they do, it will output a dropdown of all the variable prices available for
 * that fooditem.
 *
 * @author RestroPress
 * @since 1.0
 * @return void
 */
function rpress_check_for_fooditem_price_variations() {
	if( ! current_user_can( 'edit_products' ) ) {
		die( '-1' );
	}

	$fooditem_id = intval( $_POST['fooditem_id'] );
	$fooditem    = get_post( $fooditem_id );


	if( 'fooditem' != $fooditem->post_type ) {
		die( '-2' );
	}

	echo rpress_get_fooditem_price($fooditem_id);

	rpress_die();
}
add_action( 'wp_ajax_rpress_check_for_fooditem_price_variations', 'rpress_check_for_fooditem_price_variations' );



/**
 * Get food items list .
 *
 * @since 1.0
 * @return void
 */
function get_fooditem_lists( $fooditem_id, $cart_key = '' ) {

  $addons = wp_get_post_terms( $fooditem_id, 'addon_category' );
  $chosen_addons = array();
  $price_id = 0;

  if ( $addons ) {

    $addon_ids = $child_ids = array();

    foreach( $addons as $addon ) {
      if( $addon->parent != 0 ) {
        $child_ids[] = $addon->term_id;
        if ( !in_array( $addon->parent, $addon_ids ) ) {
          $addon_ids[] = $addon->parent;
        }
      }
    }
  }

  // if( $cart_key !== '' ) {         // Showed Ajax Error as per Nirmal
  // if( !empty( $cart_key ) ) {      // Did work but had issue somewhere else
  // if( !is_null( $cart_key ) ) {    // Did work but had issue somewhere else
  // if( is_int( $cart_key ) ) {      // Did work but had issue somewhere else

  if( $cart_key !== '' ) {

    $cart_contents = rpress_get_cart_contents();
    $cart_contents = $cart_contents[$cart_key];
    $price_id      = isset($cart_contents['price_id']) ? $cart_contents['price_id'] : 0;

    if( !empty( $cart_contents['addon_items'] ) ) {
      foreach( $cart_contents['addon_items'] as $key => $val ) {
        array_push( $chosen_addons, $val['addon_id'] );
      }
    }
  }

  ob_start();

  if ( !empty( $fooditem_id ) && rpress_has_variable_prices( $fooditem_id ) ) {

    $prices = rpress_get_variable_prices( $fooditem_id );

    if ( is_array( $prices ) && !empty( $prices ) ) {

      $variable_price_label = get_post_meta( $fooditem_id, 'rpress_variable_price_label', true );
      $variable_price_label = !empty( $variable_price_label ) ? $variable_price_label : esc_html( 'Price Options', 'restropress' );
      $variable_price_heading = apply_filters( 'rp_variable_price_heading', $variable_price_label ); ?>

      <h6><?php echo $variable_price_heading; ?></h6>

      <div class="rp-variable-price-wrapper">

      <?php
      foreach( $prices as $k => $price ) {

        $price_option = $price['name'];
        $is_first = ( $k == $price_id ) ? 'checked' : '';
        $price_option_slug = sanitize_title( $price['name'] );
        $price_option_amount = rpress_currency_filter( rpress_format_amount( $price['amount'] ) ); ?>

        <div class="food-item-list">
          <label for="<?php echo $price_option_slug; ?>" class="radio-container">
            <input type="radio" name="price_options" id="<?php echo $price_option_slug; ?>" data-value="<?php echo $price_option_slug . '|1|' . $price['amount'] . '|radio'; ?>" value="<?php echo $k; ?>" <?php echo $is_first; ?> class="rp-variable-price-option" ><?php echo $price_option; ?>
            <span class="control__indicator"></span>
          </label>

          <span class="cat_price"><?php echo $price_option_amount; ?></span>
        </div>
      <?php } ?>
      </div>
    <?php }
  }

  if( isset($addon_ids) && is_array( $addon_ids ) && !empty( $addon_ids ) ) {
    foreach( $addon_ids as $parent ) {
      $addon_items = get_term_by( 'id', $parent, 'addon_category' );
      $addon_name = $addon_items->name;
      $addon_slug = $addon_items->slug;
  ?>

  <h6 class="rpress-addon-category">
    <?php echo $addon_name; ?>
  </h6>

  <?php
  $addon_category_args = array( 'taxonomy' => 'addon_category', 'parent' => $addon_items->term_id, 'include' => $child_ids );
  $child_addons = get_terms( apply_filters( 'rp_addon_category', $addon_category_args ) );

  if ( $child_addons ) {
    $child_addons = wp_list_pluck( $child_addons, 'term_id' );
  }

  if( is_array( $child_addons ) && !empty( $child_addons ) ) {

    foreach( $child_addons as $child_addon ) {

      $child_data = get_term_by( 'id', $child_addon, 'addon_category' );
      $child_addon_slug = $child_data->slug;
      $child_addon_name = $child_data->name;
      $child_addon_id   = $child_data->term_id;
      $child_addon_price = rpress_get_addon_data( $child_data->term_id, 'price' );
      $term_meta = get_option( "taxonomy_term_$parent" );
      $use_addon_like =  isset($term_meta['use_it_like']) ? $term_meta['use_it_like'] : 'checkbox';
      $child_addon_type_name = ( $use_addon_like == 'radio' ) ? $addon_name : $child_addon_name; ?>

      <?php if ( is_array( $chosen_addons ) ) : ?>
      <div class="food-item-list">
        <label for="<?php echo $child_addon_slug; ?>" class="<?php echo $use_addon_like; ?>-container">
          <?php $is_selected = in_array( $child_addon_id, $chosen_addons ) ?  'checked' : ''; ?>
          <input data-type="<?php echo $use_addon_like;?>" type="<?php echo $use_addon_like; ?>" name="<?php echo $child_addon_type_name; ?>" id="<?php echo $child_addon_slug; ?>" value="<?php echo $child_addon . '|1|' . $child_addon_price . '|' . $use_addon_like; ?>" <?php echo $is_selected; ?> >
          <span><?php echo $child_addon_name; ?></span>
          <span class="control__indicator"></span>
        </label>

        <?php if( $child_addon_price > 0 ) : ?>
          <span class="cat_price">&nbsp;+&nbsp;<?php echo rpress_currency_filter( rpress_format_amount( $child_addon_price ) ); ?>
          </span>
				<?php endif; ?>
      </div>

    <?php endif;
        }
      }
    }
  }
  return ob_get_clean();
}

function get_addon_items_by_fooditem( $fooditem_id ) {

  if ( empty( $fooditem_id ) ) {
    return;
  }

  $addons  = wp_get_post_terms( $fooditem_id, 'addon_category' );

  $addon_ids = $child_ids = array();

  foreach( $addons as $addon ) {
    if( $addon->parent == 0 ) {
      $addon_ids[] = $addon->term_id;
    }
    else {
      $child_ids[] = $addon->term_id;
    }
  }

  if( is_array( $addon_ids ) && !empty( $addon_ids ) ) {

    foreach( $addon_ids as $parent ) {
      $addon_items = get_term_by( 'id', $parent, 'addon_category' );

      $addon_category_args = array( 'taxonomy' => 'addon_category', 'parent' => $addon_items->term_id );

      $child_addons = get_terms( apply_filters( 'rp_addon_category', $addon_category_args ) );

      if ( $child_addons ) {
        $child_addons = wp_list_pluck( $child_addons, 'term_id' );
      }

      if( is_array( $child_addons )
        && !empty( $child_addons ) ) {

        foreach( $child_addons as $child_addon ) {
          $child_data = get_term_by( 'id', $child_addon, 'addon_category' );
          $child_addon_slug = $child_data->slug;
          $child_addon_name = $child_data->name;
          $child_addon_price = rpress_get_addon_data( $child_data->term_id, 'price' );
          $addon_price = html_entity_decode( rpress_currency_filter( rpress_format_amount( $child_addon_price ) ) );
          ?>
          <option data-price="" data-id="" value=" <?php echo $child_addon_name .' | '. $child_addon_price; ?> "><?php echo $child_addon_name . ' (' . $addon_price . ') '; ?> </option>
          <?php
        }
      }
    }
  }
}
