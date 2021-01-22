<?php
/**
 * Front-end Actions
 *
 * @package     RPRESS
 * @subpackage  Functions
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Hooks RPRESS actions, when present in the $_GET superglobal. Every rpress_action
 * present in $_GET is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.0.0
 * @return void
*/
function rpress_get_actions() {
	$key = ! empty( $_GET['rpress_action'] ) ? sanitize_key( $_GET['rpress_action'] ) : false;
	if ( ! empty( $key ) ) {
		do_action( "rpress_{$key}" , $_GET );
	}
}
add_action( 'init', 'rpress_get_actions' );

/**
 * Hooks RPRESS actions, when present in the $_POST superglobal. Every rpress_action
 * present in $_POST is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.0.0
 * @return void
*/
function rpress_post_actions() {
	$key = ! empty( $_POST['rpress_action'] ) ? sanitize_key( $_POST['rpress_action'] ) : false;
	if ( ! empty( $key ) ) {
		do_action( "rpress_{$key}", $_POST );
	}
}
add_action( 'init', 'rpress_post_actions' );

/**
 * This sets the tax rate to fallback tax rate
 *
 * @since 2.6
 * @return mixed
*/
add_action( 'upgrader_process_complete', 'rpress_upgrade_data', 10, 2 );



/*** start new Function ***/
function rpress_upgrade_data( $upgrader_object, $options ) {

  $rpress_plugin_path_name = plugin_basename( __FILE__ );

  if ( $options['action'] == 'update' && $options['type'] == 'plugin' ) {

    if( is_array( $options['plugins'] ) ) {

      foreach ( $options['plugins'] as $plugin ) {

        if ( $plugin == $rpress_plugin_path_name ){

          $default_tax  = '';
          $tax_rates    = get_option( 'rpress_tax_rates', array() );

          if ( is_array( $tax_rates ) && !empty( $tax_rates ) ) {
            $default_tax = isset( $tax_rates[0]['rate'] ) ? $tax_rates[0]['rate'] : '';
          }

          if ( !empty( $default_tax ) ) {
            rpress_update_option( 'tax_rate', $default_tax );
          }
        }
      }
    }
  }
}

function rpress_after_category_list(){
    rpress_get_template_part( 'Custome/rpress-get-categories');
}
add_action( 'rpress_after_category_list', 'rpress_after_category_list', 10, 2 );

function get_custome_cat(){
        $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/menu",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    
    $result = json_decode($response);
    //    if ($err) {
//      echo "cURL Error #:" . $err;
//    } else {
//      $result = json_decode($response);
//    }

    return $result;
}
function get_product_details_cutome($id) {

      
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://food.mammothecommerce.com/api/Itemdetails/".$id,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          
          CURLOPT_HTTPHEADER => array(
            "cache-control: no-cache",
          ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
         if ($err) {
             return array();
         }else{
             $d = json_decode($response);
        
        $temp = array();
    
        $temp['item_id'] = $d->item_id;
        $temp['item_name'] = $d->item_name;
        $temp['item_description'] = $d->item_description;
        $temp['category'] = json_decode($d->category);
        $temp['price'] = json_decode($d->price);
        $temp['addon_item'] = $d->addon_item;
        $temp['discount'] = $d->discount;
        $temp['multi_option'] = json_decode($d->multi_option);
        $temp['multi_option_value'] = json_decode($d->multi_option_value);
        $temp['photo'] = $d->photo;
        $temp['two_flavors'] = $d->two_flavors;
        $temp['two_flavors_position'] = json_decode($d->two_flavors_position);
        $temp['gallery_photo'] = json_decode($d->gallery_photo);
        
        
    
        return $temp;
         }
        
    }
function get_product_list_by_category(){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/Items",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      return array();
    } else {
        $data = json_decode($response);
        $temp = array();
        foreach ($data as $key=>$value){
            $array['item_id'] = $value->item_id;
            $array['item_name'] = $value->item_name;
            $array['item_description'] = $value->item_description;
            $array['category'] = json_decode($value->category);
            $array['price'] = json_decode($value->price);
            $array['addon_item'] = $value->addon_item;
            $array['discount'] = $value->discount;
            $array['multi_option'] = json_decode($value->multi_option);
            $array['multi_option_value'] = json_decode($value->multi_option_value);

            $temp[] = $array;
        }
        $cat = get_custome_cat();
        $final = array();
        foreach ($temp as $temp_key=>$temp_value){
            foreach ($temp_value['category'] as $temp_keys=>$temp_values){
               $key = searchForId($temp_values,get_custome_cat());
               $temp_final['cat_id'] = $key->cat_id;
               $temp_final['category_name'] = $key->category_name;
               $temp_final['category_item'] = array(
                   'item_id'=>$temp_value['item_id'],
                   'item_name'=>$temp_value['item_name'],
                   'item_description'=>$temp_value['item_description'],
                   'category'=>$temp_value['category'][$temp_keys],
                   'price'=>reset($temp_value['price']),
                   'addon_item'=>$temp_value['addon_item'],
                   'discount'=>$temp_value['discount'],
                   'multi_option'=>$temp_value['multi_option'],
                   'multi_option_value'=>$temp_value['multi_option_value'],
               );
               $final[] = $temp_final;
            }
        }

        return $final;
    }

}
function searchForId($id, $array) {
    
   foreach ($array as $key => $val) {
       
       if ($val->cat_id === $id) {
           return $val;
       }
   }
   return null;
}

function rpress_fooditems_list_after_custome(){
    rpress_get_template_part( 'Custome/fooditems_list');
}
add_action( 'rpress_fooditems_list_after_custome', 'rpress_fooditems_list_after_custome', 10, 2 );


/*** End new Function ***/