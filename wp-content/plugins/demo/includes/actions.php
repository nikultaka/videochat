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

function get_user_details_auth() {
    $merchant_user_name = get_option( 'merchant_username');
    if(trim($merchant_user_name)!='') {
      $user_name = $merchant_user_name;
    } else {
      $current_user_data = wp_get_current_user();
      $user_name = isset($current_user_data->data->user_login) ? $current_user_data->data->user_login : 'davesdiner';
    } 
    
    
    $userdata['username'] = $user_name;
    $data = json_encode($userdata);
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/getmerchant",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      $user_data['merchant_id'] = 2;
        return json_encode($user_data);
    } else {
        $data =  json_decode($response);
        $user_data['merchant_id'] = isset($data->merchant_id) ? $data->merchant_id : 2;
        $user_data['latitude'] = isset($data->latitude) ? $data->latitude : '';
        $user_data['lontitude'] = isset($data->lontitude) ? $data->lontitude : '';
        return json_encode($user_data);
    }
}
function get_sizes_custome(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/sizes",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
    
    
}
function get_all_sizes_custome(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/all_sizes",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
    
    
}
function get_custome_cat(){
        $data = get_user_details_auth();
        $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/menu",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
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
// //    }

//    echo '<pre>';
//    print_r($result);
//    die;

    return $result;
}
function get_all_custome_cat(){
        $data = get_user_details_auth();
        $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_all_custome_cat",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),

    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    
    $result = json_decode($response);

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
        $temp['addon_item'] = json_decode($d->addon_item);
        $temp['discount'] = $d->discount;
        $temp['multi_option'] = json_decode($d->multi_option);
        $temp['multi_option_value'] = json_decode($d->multi_option_value);
        $temp['photo'] = $d->photo;
        $temp['two_flavors'] = $d->two_flavors;
        $temp['two_flavors_position'] = json_decode($d->two_flavors_position);
        $temp['gallery_photo'] = json_decode($d->gallery_photo);
        $temp['status'] = $d->status;
        
        return $temp;
         }
        
    }
function get_food_itemproduct_list(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/items",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      return array();
    } else {
        $data = json_decode($response);
        $cat = get_custome_cat();
        $price_size = get_sizes_custome();
        $temp = array();
        foreach ($data as $key=>$value){
            $array['item_id'] = $value->item_id;
            $array['item_name'] = $value->item_name;
            $array['item_description'] = $value->item_description;
            $categorys = json_decode($value->category);
            $category_data =array();
            foreach ($categorys as $category){
                $category_data[] = searchForId($category,$cat)->category_name;

            }

            $prices = json_decode($value->price);
            $price_data = '';
            foreach ($prices as $price_key=>$price){

                if($price_key == 0){
                    $price_data .= implode(', ', $prices);
                }else{
                    $size = searchForSizeId($price_key,$price_size)->size_name;
                    $price_data .= $price.'-'.$size;
                    $price_data .= '<br />';
                }

            }

            $array['category'] = implode(', ', $category_data);
            $array['price'] = $price_data;
            $array['date'] = date('Y-m-d', strtotime($value->date_created));
            $temp[] = $array;
        }
        return $temp;
    }
}
function get_product_list_by_category(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/items",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
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
               $key = searchForId($temp_values,$cat);
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
function get_product_list_by_category_new(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/items",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
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
            $array['photo'] = $value->photo;

            $temp[] = $array;
        }
        
        
        $cat = get_custome_cat();
        $final = array();
        
        foreach ($cat as $key=>$category){
            $temp_final['cat_id'] = $category->cat_id;
            $temp_final['category_name'] = $category->category_name;
            $fooditems = searchForFoodByCategoryId($temp_final['cat_id'],$temp);
            $temp_final['category_item'] = $fooditems;
            $final[] = $temp_final;
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
function searchForForSubId($id, $array) {
    $temp = array();
   foreach ($array as $key => $val) {
       
       if (in_array($id, $val['category'])) {
           $temp[] = $val;
       }
   }
   
   return $temp;
}
function insert_book_a_table($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/booktable",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return $response;
    }
}
function insert_size($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/insert_size",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return $response;
    }
}
function get_size_details($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_size_details/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function delete_size_record($id){
    $customer['size_id'] =  $id; 
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/delete_size_record",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($customer),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
         
      return "cURL Error #:" . $err;
    } else {
      return json_decode($response);
    }
}
function insert_food_category($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/addoncategory",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return $response;
    }
}
function get_food_category_details($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/Singlefeedcategorydetails/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function delete_foodcategory_record($id){
    $customer['cat_id'] =  $id; 
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/delete_foodcategory_record",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($customer),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
         
      return "cURL Error #:" . $err;
    } else {
      return json_decode($response);
    }
}
function insert_addon_category($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/add_addoncategory",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return $response;
    }
}
function get_addoncategory_details($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/Singleaddoncategorydetails/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function delete_addoncategory_record($id){
    $customer['subcat_id'] =  $id; 
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/delete_addoncategory_record",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($customer),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
         
      return "cURL Error #:" . $err;
    } else {
      return json_decode($response);
    }
}
function insert_addon_item($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/add_addonitem",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return $response;
    }
}
function get_addonitems_details($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/Singleaddonitemsdetails/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function delete_addonitems_record($id){
    $customer['sub_item_id'] =  $id; 
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/delete_addonitems_record",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($customer),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
         
      return "cURL Error #:" . $err;
    } else {
      return json_decode($response);
    }
}
function get_price_detail($item_price){
    $string = '';
    
   $first =  current($item_price);
   $last =  end($item_price);
    $string .= rpress_currency_filter( rpress_format_amount( $first));;
    if($last != $first){
        $string .= ' - '.rpress_currency_filter( rpress_format_amount($last));
    }
    
    return $string;
}
function searchForFoodByCategoryId($id,$array){
    $array_final= array();
    foreach ($array as $key => $val) {
       
        if (in_array($id, $val['category'])) {
            $array_final[] = $val;
        }
       
   }
   return $array_final;
}
function searchForSizeId($id, $array) {
    
   foreach ($array as $key => $val) {
       
       if ($val->size_id === $id) {
           return $val;
       }
   }
   return null;
}
function insert_food_item_api($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/additem",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return "cURL Error #:" . $err;
    } else {
      return $response;
    }
}
function delete_food_item($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/deleteitem",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
         
      return "cURL Error #:" . $err;
    } else {
      return $response;
    }
}
function get_order_details(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/orderdetails",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);
    
    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function get_order_details_by_id($id){
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/Singleorderdetails/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function get_client_info_custome($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/Clientdetailinfo/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function get_order_cart_details($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/OrderCartDetails/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}

function get_fooditem_details_for_price_size($item_id){
    $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://food.mammothecommerce.com/api/Itemdetails/".$item_id,
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
        $temp['status'] = $d->status;
        $price = array();
        $price_size = get_sizes_custome();
        foreach ($temp['price'] as $price_key=>$price_value){
            if($price_key > 0){
                $tempprice['name'] = searchForSizeId($price_key,$price_size)->size_name;
                $tempprice['key'] = $price_key;
                $tempprice['amount'] = $price_value;
                $price[] = $tempprice;
            }
            
        }
    
        return $price;
    }
}
function get_price_option_amount_size_id($item_id,$size_id){
    $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://food.mammothecommerce.com/api/Itemdetails/".$item_id,
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
    
        $temp['price'] = json_decode($d->price);
        
        $price = array();
        foreach ($temp['price'] as $price_key=>$price_value){
            if($price_key == $size_id){
                return $price_value;
            }
            
        }
    
        return 0;
    }
}
function get_item_price_for_food($fooditem_id = 0, $options, $price_id = 0, $remove_tax_from_inclusive = false) {

    $price = 0;
    $addon_price = 0;

    $sum = array();

    if (!$addon_price || false === $price) {

        //Check if Product is variable
        if (rpress_has_variable_prices($fooditem_id)) {

            $price_id = !empty($price_id) ? $price_id : 0;
            $price = rpress_get_price_option_amount($fooditem_id, $price_id);
        } else {
            $price = rpress_get_fooditem_price($fooditem_id);
        }
    }

    if ($remove_tax_from_inclusive && rpress_prices_include_tax()) {
        $price -= $this->get_item_tax($fooditem_id, $options, $price);
    }

    return apply_filters('rpress_cart_item_price', $price, $fooditem_id, $options);
}

function get_fooditem_price_size($fooditem_id,$cart_key = '',$product_details=''){
    $chosen_addons = array();
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

  if ( !empty( $fooditem_id )) {

    $prices = get_fooditem_details_for_price_size($fooditem_id);
    
    if ( is_array( $prices ) && !empty( $prices ) ) {

      
      ?>

      <h6>Sizes</h6>

      <div class="rp-variable-price-wrapper">

      <?php
      
      foreach( $prices as $k => $price ) {

        $price_option = $price['name'];
        $is_first = ( $price['key'] == $price_id || $k == $price_id ) ? 'checked' : '';
        $price_option_slug = sanitize_title( $price['name'] );
        $price_option_amount = rpress_currency_filter( rpress_format_amount( $price['amount'] ) ); ?>

        <div class="food-item-list">
          <label for="<?php echo $price_option_slug; ?>" class="radio-container">
            <input type="radio" name="price_options" id="<?php echo $price_option_slug; ?>" data-value="<?php echo $price_option_slug . '|1|' . $price['amount'] . '|radio'; ?>" value="<?php echo $price['key']; ?>" <?php echo $is_first; ?> class="rp-variable-price-option" ><?php echo $price_option; ?>
            <span class="control__indicator"></span>
          </label>

          <span class="cat_price"><?php echo $price_option_amount; ?></span>
        </div>
      <?php } ?>
      </div>
    <?php }
  }
  if(!empty($product_details['addon_item'])){
      foreach ($product_details['addon_item'] as $sub_cat=>$sub_value){
          $parameter['cat_id'] = $sub_cat;
          $parameter['addon_id'] = $sub_value;
          
          $category_details = get_addon_category_details($parameter);
          
          if(!empty($category_details)){
              $addon_name = $category_details->category->subcategory_name;
              $child_addons = $category_details->addon;
          }
          ?>
      <h6 class="rpress-addon-category">
        <?php echo $addon_name; ?>
      </h6>
      <?php
      
      if( is_array( $child_addons ) && !empty( $child_addons ) ) {
            
          foreach( $child_addons as $child_addon ) { ?>
      <?php 
            $child_addon_slug = 'slug_'.$child_addon->sub_item_id;
            $child_addon_name = $child_addon->sub_item_name;
            $child_addon_id   = $child_addon->sub_item_id;
            
            $child_addon_price = $child_addon->price;
            $term_mete = $product_details['multi_option']->$sub_cat;
            $use_addon_like =  isset($term_mete[0]) && $term_mete[0] == 'multiple' ? 'checkbox' : 'radio';
            $child_addon_type_name = ( $use_addon_like == 'radio' ) ? $addon_name : $child_addon_slug; 
            

      ?>
      
      <div class="food-item-list">
        <label for="<?php echo $child_addon_slug; ?>" class="<?php echo $use_addon_like; ?>-container">
          <?php $is_selected = in_array( $child_addon_id, $chosen_addons ) ?  'checked' : ''; ?>
          <input data-type="<?php echo $use_addon_like;?>" type="<?php echo $use_addon_like; ?>" name="<?php echo $child_addon_type_name; ?>" id="<?php echo $child_addon_slug; ?>" value="<?php echo $child_addon->sub_item_id . '|1|' . $child_addon_price . '|' . $use_addon_like . '|'.$child_addon_name . '|' . $addon_name .'|'.reset(json_decode($child_addon->category)); ?>" <?php echo $is_selected; ?> >
          <span><?php echo $child_addon_name; ?></span>
          <span class="control__indicator"></span>
        </label>

        <?php if( $child_addon_price > 0 ) : ?>
          <span class="cat_price">&nbsp;+&nbsp;<?php echo rpress_currency_filter( rpress_format_amount( $child_addon_price ) ); ?>
          </span>
				<?php endif; ?>
      </div>

                
              <?php
          }
      }
          
      }
  }
  return ob_get_clean();
}
function rpress_fooditems_list_after_custome(){
    rpress_get_template_part( 'Custome/fooditems_list');
}
function get_addon_category_details($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/getaddoncategorydetails",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function get_client_info_for_customer_custome($parameter){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/clientdetail",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function insert_new_order_custome($customer){
    $data = get_user_details_auth();
    $data = json_decode($data);
    $merchant_username = get_option( 'merchant_username');
    $customer['merchant_id'] =  $merchant_username; //isset($data->merchant_id) ? $data->merchant_id : 2;
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/addneworder",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($customer),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    } 
}
function payment_count_details($filter){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/paymentstatuscount",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function get_order_details_by_clients_id($id){
    $data_temp = get_user_details_auth();
    $data_temp = json_decode($data_temp);
    $data['merchant_id'] = isset($data_temp->merchant_id) ? $data_temp->merchant_id : 2;
    $data['client_id'] = $id;
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/orderdetailsclientid/",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
        return json_decode($response);
    }
}
add_action( 'rpress_fooditems_list_after_custome', 'rpress_fooditems_list_after_custome', 10, 2 );



function signup_user_yii($user_data) {

  $data = array('social_strategy'=>'web','first_name'=>$user_data['rpress_first'],'last_name'=>$user_data['rpress_last'],'email_address'=>$user_data['rpress_email'],'password'=>$user_data['rpress_user_pass']);

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://food.mammothecommerce.com/api/addclient/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => array(
      "cache-control: no-cache",
      "content-type: application/json",
    ),
  ));
  
  
  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    return false;
  } else {
    $response = json_decode($response);

    if($response->status == "1") {
      $client_id = $response->insert_id;
      $_SESSION['client_id'] = $client_id;
      return true;
    } else {
      return false;
    }
  }
}



function login_user_yii($email,$password) {

  $data = array('username'=>$email,'password'=>$password);

  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://food.mammothecommerce.com/api/merchantlogin/",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS => json_encode($data),
    CURLOPT_HTTPHEADER => array(
      "cache-control: no-cache",
      "content-type: application/json",
    ),
  ));
  
  
  $response = curl_exec($curl);
  $err = curl_error($curl);

  curl_close($curl);

  if ($err) {
    return false;
  } else {
    $response = json_decode($response);

    if($response->status == "1") {
      $_SESSION['user_client_data'] = $response->data;
      $_SESSION['address_data'] = $response->address_data;
      return true;
    } else {
      return false;
    }
  }
}

function get_addon_subcategory_item_details_by_merchant($item = FALSE){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_addon_subcategory_item",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
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
            $array['sub_item_id'] = $value->sub_item_id;
            $array['sub_item_name'] = $value->sub_item_name;
            $array['item_description'] = $value->item_description;
            $array['category'] = json_decode($value->category);
            $array['price'] = $value->price;
            
            $temp[] = $array;
        }
        if($item == true){
            return $temp;
        }
        $cat = get_addon_subcategory_by_merchant();
        $final = array();
        foreach ($cat as $keys=>$values){
            $temp_final['cat_id'] = $values->subcat_id;
            $temp_final['category_name'] = $values->subcategory_name;
            $data = searchForForSubId($values->subcat_id,$temp);
            $temp_final['category_item'] = $data;
             $final[] = $temp_final;
        }
        return $final;
    }

}

function get_all_addon_subcategory_item_details_by_merchant($item = FALSE){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_all_addon_subcategory_item",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
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
            $array['sub_item_id'] = $value->sub_item_id;
            $array['sub_item_name'] = $value->sub_item_name;
            $array['item_description'] = $value->item_description;
            $array['category'] = json_decode($value->category);
            $array['price'] = $value->price;
            $array['status'] = $value->status;
            
            $temp[] = $array;
        }
        if($item == true){
            return $temp;
        }
        $cat = get_addon_subcategory_by_merchant();
        $final = array();
        foreach ($cat as $keys=>$values){
            $temp_final['cat_id'] = $values->subcat_id;
            $temp_final['category_name'] = $values->subcategory_name;
            $data = searchForForSubId($values->subcat_id,$temp);
            $temp_final['category_item'] = $data;
             $final[] = $temp_final;
        }
        return $final;
    }

}
function get_addon_subcategory_by_merchant(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_addon_subcategory",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));


    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      return array();
    } else {
        $data = json_decode($response);
        
        return $data;
    }
    

}
function get_all_addon_subcategory_by_merchant(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_all_addon_subcategory",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));


    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      return array();
    } else {
        $data = json_decode($response);
        
        return $data;
    }
    

}
function get_food_itemproduct_list_new($request){
    $data_temp = get_user_details_auth();
    $data_temp = json_decode($data_temp);
    $data['merchant_id'] = isset($data_temp->merchant_id) ? $data_temp->merchant_id : 2;
    $data['start'] = $request['start'];
    $data['length'] = $request['length'];
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/items_new",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      return array();
    } else {
        $result = json_decode($response);
        $data = $result->data;
        $cat = get_custome_cat();
        $price = get_sizes_custome();
        $temp = array();
        foreach ($data as $key=>$value){
            $array['item_id'] = $value->item_id;
            $array['item_name'] = $value->item_name;
            $array['item_description'] = $value->item_description;
            $categorys = json_decode($value->category);
            $category_data =array();
            foreach ($categorys as $category){
                $category_data[] = searchForId($category,$cat)->category_name;

            }

            $prices = json_decode($value->price);
            $price_data = '';
            foreach ($prices as $price_key=>$price){

                if($price_key == 0){
                    $price_data .= implode(', ', $prices);
                }else{
                    $size = searchForSizeId($price_key,$price)->size_name;
                    $price_data .= $price.'-'.$size;
                    $price_data .= '<br />';
                }

            }

            $array['category'] = implode(', ', $category_data);
            $array['price'] = $price_data;
            $array['date'] = date('Y-m-d', strtotime($value->date_created));
            $temp[] = $array;
        }
        return $temp;
    }
}
function get_lat_long_of_address($address){
    
    $key = "AIzaSyD2Xuf4uv6eVFiKiHSecESrkkwidCkcR1Q";
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($address).'&key='. urlencode($key);

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => $url,
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
        $latitude = $data->results[0]->geometry->location->lat;
        $longitude = $data->results[0]->geometry->location->lng;
        $result['lat'] = $latitude;
        $result['long'] = $longitude;
        return $result;
    }
   
}
function get_shipng_charge_by_merchant($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/getshipping_charge",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
      
    }
}
function get_offers_custome(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/offers",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
    
    
}
function insert_offer($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/insert_offer",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return $response;
    }
}
function get_offer_details($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_offer_details/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function delete_offer_record($id){
    $customer['offers_id'] =  $id; 
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/delete_offer_record",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($customer),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
         
      return "cURL Error #:" . $err;
    } else {
      return json_decode($response);
    }
}
function get_offer_details_by_data($ammount){
    $data_temp = get_user_details_auth();
    $data_temp = json_decode($data_temp);
    $data['merchant_id'] = isset($data_temp->merchant_id) ? $data_temp->merchant_id : 2;
    $data['ammount'] = $ammount;
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_offer_details_by_data",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      return array();
    }else {
      return json_decode($response);
    }
}




function get_voucher_custome(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/voucher",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
    
    
}
function get_all_voucher_custome(){
    $data = get_user_details_auth();
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/all_voucher",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
    
    
}
function insert_voucher($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/insert_voucher",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return $response;
    }
}
function get_voucher_details($id){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_voucher_details/".$id,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "GET",
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
function delete_voucher_record($id){
    $customer['voucher_id'] =  $id; 
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/delete_voucher_record",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($customer),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
         
      return "cURL Error #:" . $err;
    } else {
      return json_decode($response);
    }
}

function get_discount_by_code($code){
    $data_temp = get_user_details_auth();
    $data_temp = json_decode($data_temp);
    $data['merchant_id'] = isset($data_temp->merchant_id) ? $data_temp->merchant_id : 2;
    $data['voucher_name'] = $code;
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_discount_by_code",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);
    if ($err) {
      return array();
    }else {
      return json_decode($response);
    }
}
function get_discount_value( $discount_details, $calculated_discount , $symbol = false ) {

    if ( empty( $discount_details ) ) {
        return;
    }

    $discount  			= $discount_details;
    if($discount_details->voucher_type == 'percentage'){
            $discount_type = 'percent';
    }elseif ($discount_details->voucher_type == 'fixed amount') {
            $discount_type = 'flat';
    }

    $get_subtotal       = rpress_get_cart_subtotal();
    $discount_amount 	= $discount_details->amount;

    if ( $discount_type == 'percent' ) {
        $discount_value = ( $discount_amount / 100 ) * $get_subtotal;
    } else {
        $discount_value = $get_subtotal - $calculated_discount;
    }

    $discount_value = apply_filters( 'rpress_get_discount_price_value', $discount_value );
    
    if($symbol == true){
        $discount_price_value = $discount_value;
    }else{
        $discount_price_value = rpress_currency_filter( rpress_format_amount( $discount_value ) );    
    }
    return $discount_price_value;
}


function insert_new_address($data){
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/insert_new_address",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return $response;
    }
}
function get_address_details($client_id){
    $data['client_id'] = $client_id;
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://food.mammothecommerce.com/api/get_address_list",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
        "cache-control: no-cache",
        "content-type: application/json",
      ),
    ));
    
    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      return array();
    } else {
      return json_decode($response);
    }
}
/*** End new Function ***/