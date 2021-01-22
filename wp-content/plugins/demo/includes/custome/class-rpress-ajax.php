<?php
/**
 * RestroPress RP_AJAX. AJAX Event Handlers.
 *
 * @class   RP_AJAX
 * @package RestroPress/Classes
 * @since  3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * RP_Ajax class.
 */
class RP_CUSTOME_AJAX {

  /**
   * Hook in ajax handlers.
   */
  public static function init() {

    add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
    add_action( 'template_redirect', array( __CLASS__, 'do_rp_ajax' ), 0 );
    self::add_ajax_events();
  }


  /**
   * Get RP Ajax Endpoint.
   *
   * @param string $request Optional.
   *
   * @return string
   */
  public static function get_endpoint( $request = '' ) {
    return esc_url_raw( apply_filters( 'rp_ajax_get_endpoint', add_query_arg( 'rp-ajax', $request, home_url( '/', 'relative' ) ), $request ) );
  }

  /**
   * Set RP AJAX constant and headers.
   */
  public static function define_ajax() {
      $merchant_user_name = get_option( 'merchant_username');
      
      if($merchant_user_name == ''){
          if($_GET['page'] != 'rpress-add_merchant' && $_POST['action'] != 'rpress_save_merchant'){
              wp_redirect('admin.php?page=rpress-add_merchant');
          } 
           
      }
    // phpcs:disable
    if ( ! empty( $_GET['rp-ajax'] ) ) {
      rp_maybe_define_constant( 'DOING_AJAX', true );
      rp_maybe_define_constant( 'RP_DOING_AJAX', true );
      if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
        @ini_set( 'display_errors', 0 ); // Turn off display_errors during AJAX events to prevent malformed JSON.
      }
      $GLOBALS['wpdb']->hide_errors();
    }
  }

  /**
   * Send headers for RP Ajax Requests.
   *
   */
  private static function rp_ajax_headers() {

    if ( ! headers_sent() ) {
      send_origin_headers();
      send_nosniff_header();
      rp_nocache_headers();
      header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
      header( 'X-Robots-Tag: noindex' );
      status_header( 200 );
    } elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
      headers_sent( $file, $line );
      trigger_error( "rp_ajax_headers cannot set headers - headers already sent by {$file} on line {$line}", E_USER_NOTICE ); // @codingStandardsIgnoreLine
    }
  }

  /**
   * Check for RP Ajax request and fire action.
   */
  public static function do_rp_ajax() {

    global $wp_query;

    if ( ! empty( $_GET['rp-ajax'] ) ) {
      $wp_query->set( 'rp-ajax', sanitize_text_field( wp_unslash( $_GET['rp-ajax'] ) ) );
    }

    $action = $wp_query->get( 'rp-ajax' );

    if ( $action ) {
      self::rp_ajax_headers();
      $action = sanitize_text_field( $action );
      do_action( 'rp_ajax_' . $action );
      wp_die();
    } // phpcs:enable
  }

  /**
   * Hook in methods - uses WordPress ajax handlers (admin-ajax).
   */

  public static function add_ajax_events() {
    $ajax_events_nopriv = array(
      'show_products_cutome',
        'show_address_popup',
      'add_to_cart_cutome',
      'edit_cart_fooditem_custome',
        'update_cart_items_custome',
        'check_service_slot_custome',
        'add_fooditem',
        'add_merchant',
        'fooditem_list',
        'insert_fooditem',
        'get_fooditem',
        'delete_fooditems_record',
        'save_merchant',
        'add_addon_category',
        'add_food_category',
        'add_addon_items',
        'insert_foodcategory',
        'insert_addoncategory',
        'insert_addonitem',
        'get_foodcategory',
        'delete_foodcategory_record',
        'get_addoncategory',
        'delete_addoncategory_record',
        'get_addonitems',
        'delete_addonitems_record',
        'process_update_address_custome',
        'book_table',
        'list_sizes',
        'get_size_details',
        'insert_size',
        'delete_size_record',
        'list_offers',
        'insert_offer',
        'get_offer_details',
        'delete_offer_record',
        'list_voucher',
        'insert_voucher',
        'get_voucher_details',
        'delete_voucher_record',
        'apply_discount_custome',
        'create_new_address',
    );

    foreach ( $ajax_events_nopriv as $ajax_event ) {
      add_action( 'wp_ajax_rpress_' . $ajax_event, array( __CLASS__, $ajax_event ) );
      add_action( 'wp_ajax_nopriv_rpress_' . $ajax_event, array( __CLASS__, $ajax_event ) );

      // RP AJAX can be used for frontend ajax requests.
      add_action( 'rp_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
    }

    $ajax_events = array(
      'add_addon',
        'show_address_popup',
        'create_new_address',
      'load_addon_child',
      'add_price',
      'add_category',
      'get_order_details',
      'update_order_status',
      'check_for_fooditem_price_variations',
      'admin_order_addon_items',
      'customer_search',
      'user_search',
      'search_users',
      'check_new_orders',
      'insert_fooditem',
      'save_merchant',
        'add_addon_category',
        'add_food_category',
        'add_addon_items',
        'insert_foodcategory',
        'insert_addoncategory',
        'insert_addonitem',
        'get_foodcategory',
        'delete_foodcategory_record',
        'get_addoncategory',
        'delete_addoncategory_record',
        'get_addonitems',
        'delete_addonitems_record',
        'get_fooditem',
        'book_table',
        'process_update_address_custome',
        'list_sizes',
        'get_size_details',
        'insert_size',
        'delete_size_record',
        'list_offers',
        'insert_offer',
        'get_offer_details',
        'delete_offer_record',
        'list_voucher',
        'insert_voucher',
        'get_voucher_details',
        'delete_voucher_record',
        'apply_discount_custome'
    );

    foreach ( $ajax_events as $ajax_event ) {
      add_action( 'wp_ajax_rpress_' . $ajax_event, array( __CLASS__, $ajax_event ) );
    }
  }

  /**
   * Add an variable price row.
   */
  
  public function show_address_popup(){
      if(isset($_SESSION['user_client_data']->client_id) && $_SESSION['user_client_data']->client_id > 0){
          $get_addons = '
          <style>
          .has-error{
                border : 1px solid red !important;
            }
          </style>
          <form id="address_form" name="address_form">
                <p id="rpress-apt-suite" class="rp-col-md-6 rp-col-sm-12">
                              <label class="rpress-apt-suite" for="rpress-apt-suite">
                                      Address 1
                                  <span class="rpress-required-indicator">*</span>
                              </label>
                              <input class="rpress-input" type="text" name="address_1" id="address_1" value=""  />
                      </p>
                      <p id="rpress-street-address" class="rp-col-md-6 rp-col-sm-12">
                              <label class="rpress-street-address" for="rpress-street-address">
                                      Address 2
                                      <span class="rpress-required-indicator">*</span>
                              </label>
                          <input class="rpress-input" type="text" name="address_2" id="address_2" value=""  />
                      </p>

                      <p id="rpress-city" class="rp-col-md-6 rp-col-sm-12">
                              <label class="rpress-city" for="rpress-city">
                                      Address 3
                                      <span class="rpress-required-indicator">*</span>
                              </label>
                              <input class="rpress-input" type="text" name="address_3" id="address_3" value=""  />
                      </p>
                      <p id="rpress-postcode" class="rp-col-md-6 rp-col-sm-12">
                              <label class="rpress-postcode" for="rpress-postcode">

                                          Postcode
                                      <span class="rpress-required-indicator">*</span>
                              </label>
                              <input class="rpress-input" type="text" name="address_postcode" id="address_postcode" value=""  />
                      </p>
                      </form>
                      <p>
      <button type="button" class="btn btn-primary btn-block rpress-add-address red ">
                                          Add</button>                
      </p>';
      }else{
          $get_addons = '<p id="rpress-msg" class="rp-col-md-12 rp-col-sm-12">
                              <label class="rpress-postcode" for="rpress-postcode">
                              Please Make login or register first.!
                              </label>
                      </p>';
      }
      

    $response = array(
      'html'        => $get_addons,
      'html_title'  => 'Create new address',
    );

    wp_send_json_success( $response );
    rpress_die();
  }
  public function process_update_address_custome(){
    
    $user['delivery_address'] = array();
    $user['delivery_address']['address']   	= ! empty( $_POST['rpress_street_address'] ) ? sanitize_text_field( $_POST['rpress_street_address'] ) : '';
    $user['delivery_address']['flat']   		= ! empty( $_POST['rpress_apt_suite'] ) ? sanitize_text_field( $_POST['rpress_apt_suite'] ) : '';
    $user['delivery_address']['city']    		= ! empty( $_POST['rpress_city'] ) ? sanitize_text_field( $_POST['rpress_city'] ) : '';
    $user['delivery_address']['postcode']   = ! empty( $_POST['rpress_postcode'] ) ? sanitize_text_field( $_POST['rpress_postcode'] ) : '';
    
    
    // Store the address in the user's meta so the cart can be pre-populated with it on return purchases
    update_user_meta( get_current_user_id(), '_rpress_user_delivery_address', $user['delivery_address'] ,false );
    
    echo true;
}
public function create_new_address(){
    $post = $_POST;
      $response['insert_id'] = 0;
      if(!empty($post)){
        
        $data['client_id'] = isset($_SESSION['user_client_data']->client_id) ? $_SESSION['user_client_data']->client_id : 298;
        $data['street'] = isset($post['address_1']) ? $post['address_1'] : '';
        $data['city'] = isset($post['address_2']) ? $post['address_2'] : '';
        $data['state'] = isset($post['address_3']) ? $post['address_3'] : '';
        $data['zipcode'] = isset($post['address_postcode']) ? $post['address_postcode'] : '';
        $data['location_name'] = isset($post['location_name']) ? $post['location_name'] : '1';
        $data['country_code'] = isset($post['country_code']) ? $post['country_code'] : 'IE';
        $data['as_default'] = isset($post['as_default']) ? $post['as_default'] : 1;
        $address = $data['street'].','.$data['city'].','.$data['state'].','.$data['zipcode'];
        $lat_long = get_lat_long_of_address($address);
        $data['latitude'] = isset($lat_long['lat']) ? $lat_long['lat'] : '0';
        $data['longitude'] = isset($lat_long['long']) ? $lat_long['long'] : '0';
        
        $response = insert_new_address($data);
        
      }
      

    wp_send_json_success( $response );
    rpress_die();
}

public function delete_fooditems_record(){
      $post = $_POST;
      if(isset($post['id'])){
          $response = delete_food_item($post);
          echo $response;
          exit(0);
      }
  }
  
  public function insert_addoncategory(){
      $post = $_POST;
      $response['insert_id'] = 0;
      if(!empty($post)){
        $user_data = get_user_details_auth();
        $temp_user = json_decode($user_data); 
        $data['merchant_id'] = isset($temp_user->merchant_id) ? $temp_user->merchant_id : 2;
        $data['subcategory_name'] = isset($post['category_name']) ? $post['category_name'] : '';
        $data['subcategory_description'] = isset($post['description']) ? $post['description'] : '';
        $data['sequence'] = isset($post['sequence']) ? $post['sequence'] : 0;
        $data['status'] = isset($post['status']) ? $post['status'] : '';
        $data['subcat_id'] = isset($post['id']) ? $post['id'] : '';
        $response = insert_addon_category($data);
        echo $response;
        die;
      }
      echo json_encode($response);
      die;
  }
  public function get_addoncategory(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $categoryDetail = get_addoncategory_details($id);
        
        $result['status'] = 1;
        $result['category'] = $categoryDetail;
          
    }
      
    echo json_encode($result);
    exit;
  }
  public function delete_addoncategory_record(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $data = delete_addoncategory_record($id);
        $result['status'] = $data->status;

    }

    echo json_encode($result);
    exit;
  }

  public function insert_addonitem(){
      $post = $_POST;
      $response['insert_id'] = 0;
      if(!empty($post)){
          
        $user_data = get_user_details_auth();
        $temp_user = json_decode($user_data); 
        $data['merchant_id'] = isset($temp_user->merchant_id) ? $temp_user->merchant_id : 2;
        $data['sub_item_name'] = isset($post['item_name']) ? $post['item_name'] : '';
        $data['item_description'] = isset($post['description']) ? $post['description'] : '';
        $data['category'] = isset($post['category']) ? json_encode($post['category']) : '';
        $data['price'] = isset($post['price']) ? ($post['price']) : '';
        $data['image_url'] = isset($post['image_url2']) ? ($post['image_url2']) : '';
        $data['status'] = isset($post['status']) ? $post['status'] : '';
        $data['sequence'] = isset($post['sequence']) ? $post['sequence'] : 0;
        $data['sub_item_id'] = isset($post['id']) ? $post['id'] : '';
        $response = insert_addon_item($data);
        echo $response;
        die;
      }
      echo json_encode($response);
      die;
  }
  public function get_addonitems(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $categoryDetail = get_addonitems_details($id);
        $data['sub_item_id'] = $categoryDetail->sub_item_id;
        $data['sub_item_name'] = $categoryDetail->sub_item_name;
        $data['item_description'] = $categoryDetail->item_description;
        $data['category'] = json_decode($categoryDetail->category);
        $data['price'] = $categoryDetail->price;
        $data['photo'] = $categoryDetail->photo;
        $data['status'] = $categoryDetail->status;
        $result['status'] = 1;
        $result['category'] = $data;
    }
      
    echo json_encode($result);
    exit;
  }
  public function delete_addonitems_record(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $data = delete_addonitems_record($id);
        $result['status'] = $data->status;
    }

    echo json_encode($result);
    exit;
  }

  public function add_fooditem(){
        ob_start();
        global $wpdb;
        if(isset($_GET['item_id'])){
            $product_details = get_product_details_cutome($_GET['item_id']);
        }
        //include(dirname(__FILE__) . "/admin/fooditems/quiz_list.php");
        include 'admin/fooditems/views/add_fooditem.php';
        $s = ob_get_contents();
        ob_end_clean();
        print $s;
  }

  public function save_merchant() {
      global $wpdb;
      $merchant_username = '';


      if(isset($_POST['merchant_username']) && $_POST['merchant_username']!='') {
          $merchant_username = $_POST['merchant_username'];
          
          $data = $wpdb->query("SELECT * FROM ". $wpdb->prefix. "options WHERE option_name ='merchant_username' LIMIT 1");
          if($data) {
            update_option('merchant_username',$merchant_username);
          } else {
            add_option('merchant_username',$merchant_username);
          }
          echo json_encode(array("status"=>1));
          die;
      }
      
  }

  public function add_merchant() {
    ob_start();
    global $wpdb;
    include 'admin/fooditems/views/add_merchant.php';
    $s = ob_get_contents();
    ob_end_clean();
    print $s;
  }
  
  public function fooditem_list(){
      include 'admin/fooditems/views/fooditems_list.php';
      $s = ob_get_contents();
        ob_end_clean();
        print $s;
  }
  public function insert_fooditem(){
      $post = $_POST;
      if(!empty($post)){
          $user_data = get_user_details_auth();
          $temp_user = json_decode($user_data); 
          $data['merchant_id'] = isset($temp_user->merchant_id) ? $temp_user->merchant_id : 2;
          $data['item_id'] = isset($post['item_id']) ? $post['item_id'] : '';
          $data['item_name'] = isset($post['item_name']) ? $post['item_name'] : '';
          $data['item_description'] = isset($post['description']) ? $post['description'] : '';
          $data['status'] = isset($post['status']) ? $post['status'] : '';
          $category = $post['category'];
          $size = $post['size'];
          $price = $post['price'];
          foreach ($size as $key=>$value){
              $temp[$value] = $price[$key]; 
          }
          
          $data['category'] = json_encode($category);
          $data['price'] = json_encode($temp);
          $data['multi_option'] = json_encode($post['multi_option']);
          $data['addon_item'] = json_encode($post['sub_item_id']);
          $data['image_url'] = $post['image_url2'];
          $response = insert_food_item_api($data);
          echo $response;
          die;
      }
  }
  public function list_sizes(){
    ob_start();
    global $wpdb;
    include 'admin/fooditems/views/list_sizes.php';
    $s = ob_get_contents();
    ob_end_clean();
    print $s;
  }
  public function get_size_details(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $sizeDetail = get_size_details($id);

        $result['status'] = 1;
        $result['size'] = $sizeDetail;

    }

    echo json_encode($result);
    exit;
  }
  public function insert_size(){
      $post = $_POST;
      $response['insert_id'] = 0;
      if(!empty($post)){
        $user_data = get_user_details_auth();
        $temp_user = json_decode($user_data); 
        $data['merchant_id'] = isset($temp_user->merchant_id) ? $temp_user->merchant_id : 2;
        $data['size_name'] = isset($post['size_name']) ? $post['size_name'] : '';
        $data['status'] = isset($post['status']) ? $post['status'] : '';
        $data['sequence'] = isset($post['sequence']) ? $post['sequence'] : 0;
        $data['size_id'] = isset($post['id']) ? $post['id'] : '';
        $response = insert_size($data);
        echo $response;
        die;
      }
      echo json_encode($response);
      die;
  }
  public function delete_size_record(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $data = delete_size_record($id);
        $result['status'] = $data->status;
          
    }
      
    echo json_encode($result);
    exit;
  }
  public function list_offers(){
    ob_start();
    global $wpdb;
    include 'admin/fooditems/views/list_offers.php';
    $s = ob_get_contents();
    ob_end_clean();
    print $s;
  }
  public function get_offer_details(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $offerDetail = get_offer_details($id);
        $data['offer_percentage'] = $offerDetail->offer_percentage;
        $data['offer_price'] = $offerDetail->offer_price;
        $data['valid_from'] = $offerDetail->valid_from;
        $data['valid_to'] = $offerDetail->valid_to;
        $data['status'] = $offerDetail->status;
        $data['applicable_to'] = json_decode($offerDetail->applicable_to);
        $data['offers_id'] = $offerDetail->offers_id;
        $result['status'] = 1;
        $result['offer'] = $data;

    }

    echo json_encode($result);
    exit;
  }
  public function insert_offer(){
      $post = $_POST;
      $response['insert_id'] = 0;
      if(!empty($post)){
        $user_data = get_user_details_auth();
        $temp_user = json_decode($user_data); 
        $data['merchant_id'] = isset($temp_user->merchant_id) ? $temp_user->merchant_id : 2;
        $data['offer_percentage'] = isset($post['offer_percentage']) ? $post['offer_percentage'] : '';
        $data['offer_price'] = isset($post['offer_price']) ? $post['offer_price'] : '';
        $data['valid_from'] = isset($post['valid_from']) ? $post['valid_from'] : '';
        $data['valid_to'] = isset($post['valid_to']) ? $post['valid_to'] : '';
        $data['applicable_to'] = isset($post['applicable_to']) ? json_encode($post['applicable_to']) : '';
        $data['status'] = isset($post['status']) ? $post['status'] : '';
        $data['offers_id'] = isset($post['id']) ? $post['id'] : '';
        
        $response = insert_offer($data);
        echo $response;
        die;
      }
      echo json_encode($response);
      die;
  }
  public function delete_offer_record(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $data = delete_offer_record($id);
        $result['status'] = $data->status;
          
    }
      
    echo json_encode($result);
    exit;
  }
  
  
  
  public function list_voucher(){
    ob_start();
    global $wpdb;
    include 'admin/fooditems/views/list_voucher.php';
    $s = ob_get_contents();
    ob_end_clean();
    print $s;
  }
  public function get_voucher_details(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $voucherDetail = get_voucher_details($id);
        $data['voucher_name'] = $voucherDetail->voucher_name;
        $data['voucher_type'] = $voucherDetail->voucher_type;
        $data['amount'] = $voucherDetail->amount;
        $data['expiration'] = $voucherDetail->expiration;
        $data['status'] = $voucherDetail->status;
        $data['voucher_id'] = $voucherDetail->voucher_id;
        $result['status'] = 1;
        $result['voucher'] = $data;

    }

    echo json_encode($result);
    exit;
  }
  public function insert_voucher(){
      $post = $_POST;
      $response['insert_id'] = 0;
      if(!empty($post)){
        $user_data = get_user_details_auth();
        $temp_user = json_decode($user_data); 
        $data['merchant_id'] = isset($temp_user->merchant_id) ? $temp_user->merchant_id : 2;
        $data['voucher_owner'] = 'merchant';
        $data['voucher_name'] = isset($post['voucher_name']) ? $post['voucher_name'] : '';
        $data['voucher_type'] = isset($post['voucher_type']) ? $post['voucher_type'] : '';
        $data['amount'] = isset($post['amount']) ? $post['amount'] : '';
        $data['expiration'] = isset($post['expiration']) ? $post['expiration'] : '';
        $data['used_once'] = isset($post['used_once']) ? $post['used_once'] : 0;
        $data['status'] = isset($post['status']) ? $post['status'] : '';
        $data['voucher_id'] = isset($post['id']) ? $post['id'] : '';
        $response = insert_voucher($data);
        echo $response;
        die;
      }
      echo json_encode($response);
      die;
  }
  public function delete_voucher_record(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $data = delete_voucher_record($id);
        $result['status'] = $data->status;
          
    }
      
    echo json_encode($result);
    exit;
  }
  
  public function add_food_category(){
    ob_start();
    global $wpdb;
    include 'admin/fooditems/views/add_food_category.php';
    $s = ob_get_contents();
    ob_end_clean();
    print $s;
  }
  public function insert_foodcategory(){
      $post = $_POST;
      $response['insert_id'] = 0;
      if(!empty($post)){
        $user_data = get_user_details_auth();
        $temp_user = json_decode($user_data); 
        $data['merchant_id'] = isset($temp_user->merchant_id) ? $temp_user->merchant_id : 2;
        $data['category_name'] = isset($post['category_name']) ? $post['category_name'] : '';
        $data['category_description'] = isset($post['description']) ? $post['description'] : '';
        $data['sequence'] = isset($post['sequence']) ? $post['sequence'] : 0;
        $data['image_url'] = isset($post['image_url2']) ? $post['image_url2'] : '';
        $data['status'] = isset($post['status']) ? $post['status'] : '';
        $data['cat_id'] = isset($post['id']) ? $post['id'] : '';
        $data['parent_cat_id'] = isset($post['parent_cat_id']) ? $post['parent_cat_id'] : 0;
        $response = insert_food_category($data);
        echo $response;
        die;
      }
      echo json_encode($response);
      die;
  }
  public function get_foodcategory(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $categoryDetail = get_food_category_details($id);
        
        $result['status'] = 1;
        $result['category'] = $categoryDetail;
          
    }
      
    echo json_encode($result);
    exit;
  }
  public function delete_foodcategory_record(){
    $post = $_POST;
    $result = array();
    $result['status'] = 0;
    if(!empty($post)){
        $id = esc_sql($post['id']);
        $data = delete_foodcategory_record($id);
        $result['status'] = $data->status;
          
    }
      
    echo json_encode($result);
    exit;
  }

  public function add_addon_items(){
    ob_start();
    global $wpdb;
    include 'admin/fooditems/views/add_addon_items.php';
    $s = ob_get_contents();
    ob_end_clean();
    print $s;
  }
  public function add_addon_category(){
    ob_start();
    global $wpdb;
    include 'admin/fooditems/views/add_addon_category.php';
    $s = ob_get_contents();
    ob_end_clean();
    print $s;
  }
  public static function add_price() {

    ob_start();

    check_ajax_referer( 'add-price', 'security' );

    $current = $_POST['i'];

    include 'admin/fooditems/views/html-fooditem-variable-price.php';
    wp_die();
  }
    private static function output_tabs() {
       // global $post, $thepostid, $fooditem_object;

        include 'admin/fooditems/views/html-fooditem-data-general.php';
        include 'admin/fooditems/views/html-fooditem-data-category.php';
        include 'admin/fooditems/views/html-fooditem-data-addons.php';
    }
    private static function get_fooditem_data_tabs() {
    $tabs = apply_filters(
      'rpress_fooditem_data_tabs',
      array(
        'general'        => array(
          'label'    => __( 'General', 'restropress' ),
          'target'   => 'general_fooditem_data',
          'class'    => array(),
          'icon'     => 'icon-general',
          'priority' => 10,
        ),
        'category'      => array(
          'label'    => __( 'Category', 'restropress' ),
          'target'   => 'category_fooditem_data',
          'class'    => array(),
          'icon'     => 'icon-category',
          'priority' => 20,
        ),
        'addons'       => array(
          'label'    => __( 'Addons', 'restropress' ),
          'target'   => 'addons_fooditem_data',
          'class'    => array(),
          'icon'     => 'icon-addon',
          'priority' => 30,
        )
      )
    );

    // Sort tabs based on priority.
    uasort( $tabs, array( __CLASS__, 'fooditem_data_tabs_sort' ) );

    return $tabs;
  }
private static function fooditem_data_tabs_sort( $a, $b ) {
    if ( ! isset( $a['priority'], $b['priority'] ) ) {
      return -1;
    }

    if ( $a['priority'] === $b['priority'] ) {
      return 0;
    }

    return $a['priority'] < $b['priority'] ? -1 : 1;
  }

  /**
   * Add an addon row.
   */
  public static function add_addon() {

    ob_start();

    check_ajax_referer( 'add-addon', 'security' );

    $current = $_POST['i'];

    if( $_POST['iscreate'] == 'true' ){
      $addon_types  = rpress_get_addon_types();
      include 'admin/fooditems/views/html-fooditem-new-addon-category.php';
    }
    else {
      $addon_categories = rpress_get_addons();
      include 'admin/fooditems/views/html-fooditem-addon.php';
    }

    wp_die();
  }


  /**
   * Add Category to fooditem
   */
  public static function add_category() {

    check_ajax_referer( 'add-category', 'security' );

    $parent = $_POST['parent'];
    $name   = $_POST['name'];
    $args   = apply_filters( 'rpress_add_category_args', array( 'parent' => $parent ) );

    $category = wp_insert_term( $name, 'food-category', $args );

    wp_send_json( $category );
  }


  /**
  *
  * Change order status from order history
  *
  * @since 3.0
  * @return mixed
  */
  public static function update_order_status() {

    if ( isset( $_GET['status'] ) && isset( $_GET['payment_id'] ) ) {

      $payment_id = $_GET['payment_id'];
      $new_status = $_GET['status'];

      $status = sanitize_text_field( wp_unslash( $new_status ) );
      $statuses = rpress_get_order_statuses();

      if ( array_key_exists( $status, $statuses ) ) {
        rpress_update_order_status( $payment_id, $status );
      }
    }

    wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url( 'admin.php?page=rpress-payment-history' ) );
    exit;
  }

  /**
   * Load addon child items when after selecting parent addon
   */
  public static function load_addon_child() {

    check_ajax_referer( 'load-addon', 'security' );

    $parent = $_POST['parent'];

    $addon_items = rpress_get_addons( $parent );

    $current = $_POST['i'];

    $output = '<ul class="rp-addon-items">';
    foreach( $addon_items as $addon_item ){

      $addon_price = rpress_get_addon_data( $addon_item->term_id, 'price' );
      $addon_price = !empty( $addon_price ) ? rpress_currency_filter( rpress_format_amount( $addon_price ) ) : '0.00';
      $parent_class = ( $addon_item->parent == 0 ) ? 'rp-parent-addon' : 'rp-child-addon';

      $output .= '<li class="  ' . $parent_class . ' ">';
      $output .= '<input type="checkbox" value="' . $addon_item->term_id . '" id="' . $addon_item->slug .'" name="addons[' . $current . '][items][]" class="rp-checkbox">';
      $output .= '<label for="' . $addon_item->slug .'">' . $addon_item->name .'</label>';
      $output .= '<span class="rp-addon-price">('.$addon_price .')</span>';
      $output .= '</li>';
    }
    $output .= '</ul>';

    echo $output;
    wp_die();
  }

  /**
   * Load Fooditems List in the popup
   */
  public function show_products_cutome() {

    if ( empty( $_POST['fooditem_id'] ) )
      return;

    $fooditem_id = $_POST['fooditem_id'];

    $price = '';

    if ( !empty( $fooditem_id ) ) {
      //Check item is variable or simple
      $product_details = get_product_details_cutome( $fooditem_id );
      $price = reset($product_details['price']);
    }

    

    if ( !empty( $price ) ) {
      $formatted_price = rpress_currency_filter( rpress_format_amount( $price ) );
    }

    $food_title     = $product_details['item_name'];
    $fooditem_desc  = $product_details['item_description'];
    $item_addons    = get_fooditem_price_size( $fooditem_id,'',$product_details);

    ob_start();
    rpress_get_template_part( 'rpress', 'show-products' );
    $data = ob_get_clean();

    $data = str_replace( '{fooditemslist}', $item_addons, $data );
    $data = str_replace( '{itemdescription}', $fooditem_desc, $data );

    $response = array(
      'price'       => $formatted_price,
      'price_raw'   => $price,
      'html'        => $data,
      'html_title'  => apply_filters( 'rpress_modal_title' , $food_title ),
    );

    
    wp_send_json_success($response);
    rpress_die();
  }

  /**
   * Show Service Options in the popup
   */
  public static function show_delivery_options() {

    check_ajax_referer( 'service-type', 'security', false );

    $fooditem_id = isset( $_POST['fooditem_id'] ) ? $_POST['fooditem_id'] : '';

    $get_addons = rpress_get_delivery_steps( $fooditem_id );

    $response = array(
      'html'        => $get_addons,
      'html_title'  => apply_filters('rpress_delivery_options_title', __( 'Your Order Settings', 'restropress' ) ),
    );

    wp_send_json_success( $response );
    rpress_die();
  }

  /**
   * Check Service Options availibility
   */
  public static function check_service_slot_custome() {
    $response = apply_filters( 'rpress_check_service_slot', $_POST );
    $response = apply_filters( 'rpress_validate_slot', $response );
    wp_send_json( $response );
    wp_die();
  }

  /**
   * Edit fooditem in the popup
   */
  public static function edit_cart_fooditem_custome() {

    check_ajax_referer( 'edit-cart-fooditem', 'security', false );

    $cart_key = ! empty( $_POST['cartitem_id'] ) ? $_POST['cartitem_id'] : 0 ;
    $cart_key = absint( $cart_key );
    $fooditem_id = ! empty( $_POST['fooditem_id'] ) ? $_POST['fooditem_id'] : '' ;
    $food_title = ! empty( $_POST['fooditem_name'] ) ? $_POST['fooditem_name'] : get_the_title( $fooditem_id );
    $fooditem_desc  = get_post_field( 'post_content', $fooditem_id );

    if ( !empty( $fooditem_id)  ) {

      $price = '';

      if ( !empty( $fooditem_id ) ) {
        //Check item is variable or simple
          $product_details = get_product_details_cutome( $fooditem_id );
          
          $fooditem_desc  = $product_details['item_description'];
          $parent_addons = get_fooditem_price_size( $fooditem_id, $cart_key,$product_details );
          
        $cart_contents = rpress_get_cart_contents();
        
        $cart_contents = $cart_contents[$cart_key];
        $price_id      = isset($cart_contents['price_id']) ? $cart_contents['price_id'] : 0;
        
        $sub_price = 0;
        if(is_array($cart_contents['addon_items']) && count($cart_contents['addon_items']) > 0 ){
            foreach ($cart_contents['addon_items'] as $addon_price){
                $sub_price += $addon_price['price'];
            }
        }
        
        $price = isset($product_details['price']->$price_id) ? $product_details['price']->$price_id + $sub_price : reset($product_details['price']) + $sub_price;
        
//        if ( rpress_has_variable_prices( $fooditem_id ) ) {
//          $price = rpress_get_lowest_price_option( $fooditem_id );
//        } else {
//          $price = rpress_get_fooditem_price( $fooditem_id );
//        }
        
      }

      if ( !empty( $price ) ) {
        $formatted_price = rpress_currency_filter( rpress_format_amount( $price ) );
      }

      
      $special_instruction = rpress_get_instruction_by_key( $cart_key );

      ob_start();
      rpress_get_template_part( 'rpress', 'edit-product' );
      $data = ob_get_clean();

      $data = str_replace( '{itemdescription}', $fooditem_desc, $data );
      $data = str_replace( '{fooditemslist}', $parent_addons, $data );
      $data = str_replace( '{cartinstructions}', $special_instruction, $data );
    }

    $response = array(
      'price'       => $formatted_price,
      'price_raw'   => $price,
      'html'        => $data,
      'html_title'  => apply_filters( 'rpress_modal_title' , $food_title),
    );

    wp_send_json_success( $response );
    rpress_die();
  }

  /**
   * Add To Cart in the popup
   */
  public static function add_to_cart_cutome() {
    if ( empty( $_POST['fooditem_id'] ) && empty( $_POST['fooditem_qty'] ) ) {
      return;
    }

    $fooditem_id  = $_POST['fooditem_id'];
    $quantity     = $_POST['fooditem_qty'];
    $instructions = ! empty($_POST['special_instruction']) ? $_POST['special_instruction'] : '';
    $addon_items  = ! empty( $_POST['post_data'] ) ? $_POST['post_data'] : '';

    $items   = '';
    $options = array();

    //Check whether the fooditem has variable pricing
    $product_details = get_product_details_cutome( $fooditem_id );
    
    $price_id = !empty($addon_items[0]['value']) ? $addon_items[0]['value'] : 0;
    if($price_id > 0){
        $options['price_id'] = $price_id;
        $options['price'] = get_price_option_amount_size_id($fooditem_id, $price_id);
    }else{
        $options['price_id'] = 0;
        $options['price'] = reset($product_details['price']);
    }
    
//    if ( rpress_has_variable_prices( $fooditem_id ) ) {
//      $price_id = !empty( $addon_items[0]['value'] ) ? $addon_items[0]['value'] : 0;
//      $options['price_id'] = $price_id;
//      $options['price']   = rpress_get_price_option_amount( $fooditem_id, $price_id );
//    } else {
//      $options['price'] = rpress_get_fooditem_price( $fooditem_id );
//    }

    
    $options['id'] = $fooditem_id;
    $options['quantity'] = $quantity;
    $options['instruction'] = $instructions;
    $options['category'] = reset($product_details['category']);

    if ( is_array( $addon_items ) && !empty( $addon_items ) ) {

      foreach( $addon_items as $key => $get_items ) {

        $addon_data = explode( '|', $get_items[ 'value' ] );

        if ( is_array( $addon_data ) && !empty( $addon_data ) && count($addon_data) > 2 ) {
            
          $addon_item_like = isset( $addon_data[3] ) ? $addon_data[3] : 'checkbox';

          $addon_id     = ! empty( $addon_data[0] ) ? $addon_data[0] : '';
          $addon_qty    = ! empty( $addon_data[1] ) ? $addon_data[1] : '';
          $addon_price  = ! empty( $addon_data[2] ) ? $addon_data[2] : '';
          $addon_item_name  = ! empty( $addon_data[4] ) ? $addon_data[4] : '';
          $sub_cat_name  = ! empty( $addon_data[5] ) ? $addon_data[5] : '';
          $sub_cat_id  = ! empty( $addon_data[6] ) ? $addon_data[6] : '';

            $options['addon_items'][$key]['addon_item_name'] = $addon_item_name;
            $options['addon_items'][$key]['addon_id'] = $addon_id;
            $options['addon_items'][$key]['price'] = $addon_price;
            $options['addon_items'][$key]['quantity'] = $addon_qty;
            $options['addon_items'][$key]['cat_name'] = $sub_cat_name;
            $options['addon_items'][$key]['cat_id'] = $sub_cat_id;
          
        }
      }
    }

    $key = rpress_add_to_cart( $fooditem_id, $options );
    
    $item = array(
      'id'      => $fooditem_id,
      'options' => $options
    );

    $item   = apply_filters( 'rpress_ajax_pre_cart_item_template', $item );
    
    
    
    
    $items .= rpress_get_cart_item_template( $key, $item, true, $data_key = $key );
    //echo "sdsd123"; die;
    

    $return = array(
     'subtotal'      => html_entity_decode( rpress_currency_filter( rpress_format_amount( rpress_get_cart_subtotal() ) ), ENT_COMPAT, 'UTF-8' ),
     //   'subtotal'      => html_entity_decode( rpress_currency_filter( rpress_format_amount( 100 ) ), ENT_COMPAT, 'UTF-8' ),
     'total'         => html_entity_decode( rpress_currency_filter( rpress_format_amount( rpress_get_cart_total() ) ), ENT_COMPAT, 'UTF-8' ),
     //   'total'         => html_entity_decode( rpress_currency_filter( rpress_format_amount( 100 ) ), ENT_COMPAT, 'UTF-8' ),
     'cart_item'     => $items,
     'cart_key'      => $key,
     'cart_quantity' => html_entity_decode( rpress_get_cart_quantity() )
    );

    if ( rpress_use_taxes() ) {
      $cart_tax = (float) rpress_get_cart_tax();
      $return['taxes'] = html_entity_decode( rpress_currency_filter( rpress_format_amount( $cart_tax ) ), ENT_COMPAT, 'UTF-8' );
    }

    $return = apply_filters( 'rpress_cart_data', $return );

    wp_send_json( $return );
    rpress_die();
  }

  /**
   * Update Cart Items
   */
  public static function update_cart_items_custome() {

    check_ajax_referer( 'update-cart-item', 'security', false );

    $cart_key     = isset( $_POST['fooditem_cartkey'] ) ? $_POST['fooditem_cartkey'] : '';
    $fooditem_id  = isset( $_POST['fooditem_id'] ) ? $_POST['fooditem_id'] : '';
    $item_qty     = isset( $_POST['fooditem_qty'] ) ? $_POST['fooditem_qty'] : 1;

    if ( empty( $cart_key ) && empty( $fooditem_id ) ) {
      return;
    }

    $special_instruction = isset( $_POST['special_instruction'] ) ? sanitize_text_field( $_POST['special_instruction'] ) : '';
    $addon_items = isset( $_POST['post_data'] ) ?  $_POST['post_data'] : '';

    $options = array();
    $options['id'] = $fooditem_id;
    $options['quantity'] = $item_qty;
    $options['instruction'] = $special_instruction;

    $price_id = '';
    $items    = '';
        
   
    if ( isset( $addon_items[0]['name'] ) && $addon_items[0]['name'] == 'price_options' ) {
        $price_id = $addon_items[0]['value'];
      }

   

    $options['price_id'] = $price_id;
    
    $options['price'] = get_price_option_amount_size_id($fooditem_id, $price_id);
    
    RPRESS()->cart->rpress_update_to_cart( $cart_key, $fooditem_id, $options );

    $item = array(
      'id'      => $fooditem_id,
      'options' => $options
    );

    $item   = apply_filters( 'rpress_ajax_pre_cart_item_template', $item );
    $items = rpress_get_cart_item_template( $cart_key, $item, true, $data_key = '' );

    $return = array(
     'subtotal'      => html_entity_decode( rpress_currency_filter( rpress_format_amount( rpress_get_cart_subtotal() ) ), ENT_COMPAT, 'UTF-8' ),
     'total'         => html_entity_decode( rpress_currency_filter( rpress_format_amount( rpress_get_cart_total() ) ), ENT_COMPAT, 'UTF-8' ),
     'cart_item'     => $items,
     'cart_key'      => $cart_key,
     'cart_quantity' => html_entity_decode( rpress_get_cart_quantity() )
    );

    if ( rpress_use_taxes() ) {
      $cart_tax = (float) rpress_get_cart_tax();
      $return['tax'] = html_entity_decode( rpress_currency_filter( rpress_format_amount( $cart_tax ) ), ENT_COMPAT, 'UTF-8' );
    }

    $return = apply_filters( 'rpress_cart_data', $return );
    echo json_encode( $return );
    rpress_die();
  }

  /**
   * Remove an item from Cart
   */
  public static function remove_from_cart() {

    if ( isset( $_POST['cart_item'] ) ) {

      rpress_remove_from_cart( $_POST['cart_item'] );

      $return = array(
        'removed'       => 1,
        'subtotal'      => html_entity_decode( rpress_currency_filter( rpress_format_amount( rpress_get_cart_subtotal() ) ), ENT_COMPAT, 'UTF-8' ),
        'total'         => html_entity_decode( rpress_currency_filter( rpress_format_amount( rpress_get_cart_total() ) ), ENT_COMPAT, 'UTF-8' ),
        'cart_quantity' => html_entity_decode( rpress_get_cart_quantity() ),
      );

      if ( rpress_use_taxes() ) {
        $cart_tax = (float) rpress_get_cart_tax();
        $return['tax'] = html_entity_decode( rpress_currency_filter( rpress_format_amount( $cart_tax ) ), ENT_COMPAT, 'UTF-8' );
      }
      $return = apply_filters( 'rpress_cart_data', $return );
      wp_send_json( $return );

    }
    rpress_die();
  }

  /**
  * Clear cart
  */
  public static function clear_cart() {

    //check_ajax_referer( 'clear-cart', 'security' );

    rpress_empty_cart();

    // Removing Service Time Cookie
    if ( isset( $_COOKIE['service_time'] ) ) {
      unset( $_COOKIE['service_time'] );
      setcookie( "service_time", "", time() - 300,"/" );
    }

    // Removing Service Type Cookie
    if ( isset( $_COOKIE['service_type'] ) ) {
      unset( $_COOKIE['service_type'] );
      setcookie( "service_type", "", time() - 300,"/" );
    }

    // Removing Delivery Date Cookie
    if ( isset( $_COOKIE['delivery_date'] ) ) :
      unset( $_COOKIE['delivery_date'] );
      setcookie( "delivery_date", "", time() - 300,"/" );
    endif;

    $return['status']   = 'success';
    $return['response'] = '<li class="cart_item empty"><span class="rpress_empty_cart">'.apply_filters( 'rpress_empty_cart_message', '<span class="rpress_empty_cart">' . __( 'CHOOSE AN ITEM FROM THE MENU TO GET STARTED.', 'restropress' ) . '</span>' ).'</span></li>';
    echo json_encode( $return );

    rpress_die();
  }

  /**
  * Proceed Checkout
  */
  public static function proceed_checkout() {
    //check_ajax_referer( 'proceed-checkout', 'security' );
    $response = rpress_pre_validate_order();
    $response = apply_filters( 'rpress_proceed_checkout', $response );
    wp_send_json( $response );
    rpress_die();
  }

  /**
   * Get Order Details
   */
  public static function get_order_details() {

    check_admin_referer( 'rpress-preview-order', 'security' );

    $order = rpress_get_payment( absint( $_GET['order_id'] ) );

    if ( $order ) {
      include_once 'admin/payments/class-payments-table.php';

      wp_send_json_success( RPRESS_Payment_History_Table::order_preview_get_order_details( $order ) );
    }
    rpress_die();
  }

  /**
  * Get Fooditem Variations
  */
  public static function check_for_fooditem_price_variations() {

    if ( ! current_user_can( 'edit_products' ) ) {
      die( '-1' );
    }

    $fooditem_id = isset( $_POST['fooditem_id'] ) ? $_POST['fooditem_id'] : '';

    //Check fooditem has any variable pricing
    if ( empty( $fooditem_id ) )
      return;

    ob_start();

    if ( rpress_has_variable_prices( $fooditem_id ) ) :
      $get_lowest_price_id = rpress_get_lowest_price_id( $fooditem_id );
      $get_lowest_price = rpress_get_lowest_price_option( $fooditem_id );
      ?>
      <div class="rpress-get-variable-prices">
        <input type="hidden" class="rpress_selected_price" name="rpress_selected_price" value="<?php echo $get_lowest_price; ?>">
      <?php
      foreach ( rpress_get_variable_prices( $fooditem_id ) as $key => $options ) :
        $option_price = $options['amount'];
        $price = rpress_currency_filter( rpress_format_amount( $option_price ) );
        $option_name = $options['name'];
        $option_name_slug = sanitize_title( $option_name );
      ?>
        <label for="<?php echo $option_name_slug; ?>">
          <input id="<?php echo $option_name_slug; ?>" <?php checked( $get_lowest_price_id, $key, true ); ?> type="radio" name="rpres_price_name" value="<?php echo $option_price; ?>">
          <?php echo $option_name; ?>
          <?php echo sprintf( __( '( %1$s )', 'restropress' ), $price );  ?>
        </label>
      <?php
      endforeach;
    ?>
    </div>
    <?php
    else :
      $normal_price = rpress_get_fooditem_price( $fooditem_id );
      $price = rpress_currency_filter( rpress_format_amount( $normal_price  ) );
      ?>
      <span class="rpress-price-name"><?php echo $price; ?></span>
      <input type="hidden" class="rpress_selected_price" name="rpress_selected_price" value="<?php echo $normal_price; ?>">
      <?php
      //$output .= '<span class="rpress-price">'.$price.'</span>';
    endif;
    $output = ob_get_contents();
    ob_end_clean();
    echo $output;
    rpress_die();
  }

  /**
  * Get addon items in the admin order screen
  */
  public static function admin_order_addon_items() {

    check_ajax_referer( 'load-admin-addon', 'security' );

    $fooditem_id = isset( $_POST['fooditem_id' ] ) ? $_POST['fooditem_id'] : '';
    $get_addon_items = '';

    ob_start();

    if( !empty( $fooditem_id ) ) {
      $get_addon_items = get_addon_items_by_fooditem( $fooditem_id );
    }

    $output = ob_get_contents();
    ob_end_clean();

    echo $output;
    rpress_die();
  }

  /**
   * Gets the cart's subtotal via AJAX.
   *
   * @since 1.0
   * @return void
   */
  public static function get_subtotal() {

    echo rpress_currency_filter( rpress_get_cart_subtotal() );
    rpress_die();
  }

  /**
   * Validates the supplied discount sent via AJAX.
   *
   * @since 1.0
   * @return void
   */
  public static function apply_discount_custome() {

    if ( isset( $_POST['code'] ) ) {

      $discount_code = sanitize_text_field( $_POST['code'] );

      $return = array(
        'msg'  => '',
        'code' => $discount_code
      );

      $user = '';

      if ( is_user_logged_in() ) {
        $user = get_current_user_id();
      } else {
        parse_str( $_POST['form'], $form );
        if ( ! empty( $form['rpress_email'] ) ) {
          $user = urldecode( $form['rpress_email'] );
        }
      }
    
      $discount_details  = get_discount_by_code( $discount_code );
      if(!empty($discount_details)){
          if($discount_details->voucher_type == 'percentage'){
              $discout_type = 'percent';
        }elseif ($discount_details->voucher_type == 'fixed amount') {
              $discout_type = 'flat';
        }
        $amount    = rpress_format_discount_rate( $discout_type, $discount_details->amount );    
        $discounts = rpress_set_cart_discount( $discount_code );
        
        $total     = rpress_get_cart_total( $discounts );
        
         $discount_value = get_discount_value( $discount_details, $total );
         
         $return = array(
          'msg'         => 'valid',
          'discount_value' => $discount_value,
          'amount'      => $amount,
          'total_plain' => $total,
          'total'       => html_entity_decode( rpress_currency_filter( rpress_format_amount( $total ) ), ENT_COMPAT, 'UTF-8' ),
          'code'        => $discount_code,
          'html'        => get_cart_discounts_html( $discount_details )
        );
      }else {

        
        $return['msg']  = "voucher code not found";
        rpress_unset_error( 'rpress-discount-error' );
      }
  

      // Allow for custom discount code handling
      $return = apply_filters( 'rpress_ajax_discount_response', $return );

      echo json_encode($return);
    }
    rpress_die();
  }

  /**
   * Removes a discount code from the cart via ajax
   *
   * @since  1.0.0
   * @return void
   */
  public static function remove_discount() {

    if ( isset( $_POST['code'] ) ) {

      rpress_unset_cart_discount( urldecode( $_POST['code'] ) );

      $total = rpress_get_cart_total();

      $return = array(
        'total'     => html_entity_decode( rpress_currency_filter( rpress_format_amount( $total ) ), ENT_COMPAT, 'UTF-8' ),
        'code'      => $_POST['code'],
        'discounts' => rpress_get_cart_discounts(),
        'html'      => rpress_get_cart_discounts_html()
      );

      echo json_encode( $return );
    }
    rpress_die();
  }

  /**
   * Loads Checkout Login Fields the via AJAX
   *
   * @since 1.0
   * @return void
   */
  public static function checkout_login() {

    do_action( 'rpress_purchase_form_login_fields' );
    rpress_die();
  }
  /**
   * Load Checkout Register Fields via AJAX
   *
   * @since 1.0
   * @return void
  */
  public static function checkout_register() {

    do_action( 'rpress_purchase_form_register_fields' );
    rpress_die();
  }

  /**
   * Recalculate cart taxes
   *
   * @since  1.0.0
   * @return void
   */
  public static function recalculate_taxes() {

    if ( ! rpress_get_cart_contents() ) {
      return false;
    }

    if ( empty( $_POST['billing_country'] ) ) {
      $_POST['billing_country'] = rpress_get_shop_country();
    }

    ob_start();
    rpress_checkout_cart();
    $cart     = ob_get_clean();
    $response = array(
      'html'         => $cart,
      'tax_raw'      => rpress_get_cart_tax(),
      'tax'          => html_entity_decode( rpress_cart_tax( false ), ENT_COMPAT, 'UTF-8' ),
      'tax_rate_raw' => rpress_get_tax_rate(),
      'tax_rate'     => html_entity_decode( rpress_get_formatted_tax_rate(), ENT_COMPAT, 'UTF-8' ),
      'total'        => html_entity_decode( rpress_cart_total( false ), ENT_COMPAT, 'UTF-8' ),
      'total_raw'    => rpress_get_cart_total(),
    );

    echo json_encode( $response );

    rpress_die();
  }

  /**
   * Retrieve a states drop down
   *
   * @since  1.0.0
   * @return void
   */
  public static function get_states() {

    if( empty( $_POST['country'] ) ) {
      $_POST['country'] = rpress_get_shop_country();
    }

    $states = rpress_get_states( $_POST['country'] );

    if( ! empty( $states ) ) {

      $args = array(
        'name'    => $_POST['field_name'],
        'id'      => $_POST['field_name'],
        'class'   => $_POST['field_name'] . '  rpress-select',
        'options' => $states,
        'show_option_all'  => false,
        'show_option_none' => false
      );

      $response = RPRESS()->html->select( $args );

    } else {

      $response = 'nostates';
    }

    echo $response;

    rpress_die();
  }

  /**
   * Search food items
   *
   * @since  1.0.0
   * @return void
   */
  public static function fooditem_search() {

    global $wpdb;

    $search   = esc_sql( sanitize_text_field( $_GET['s'] ) );
    $excludes = ( isset( $_GET['current_id'] ) ? (array) $_GET['current_id'] : array() );

    $no_bundles = isset( $_GET['no_bundles'] ) ? filter_var( $_GET['no_bundles'], FILTER_VALIDATE_BOOLEAN ) : false;
    if( true === $no_bundles ) {
      $bundles  = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_rpress_product_type' AND meta_value = 'bundle';", ARRAY_A );
      $bundles  = wp_list_pluck( $bundles, 'post_id' );
      $excludes = array_merge( $excludes, $bundles );
    }

    $variations = isset( $_GET['variations'] ) ? filter_var( $_GET['variations'], FILTER_VALIDATE_BOOLEAN ) : false;

    $excludes = array_unique( array_map( 'absint', $excludes ) );
    $exclude  = implode( ",", $excludes );

    $results = array();

    // Setup the SELECT statement
    $select = "SELECT ID,post_title FROM $wpdb->posts ";

    // Setup the WHERE clause
    $where = "WHERE `post_type` = 'fooditem' and `post_title` LIKE '%s' ";

    // If we have items to exclude, exclude them
    if( ! empty( $exclude ) ) {
      $where .= "AND `ID` NOT IN (" . $exclude . ") ";
    }

    if ( ! current_user_can( 'edit_products' ) ) {
      $status = apply_filters( 'rpress_product_dropdown_status_nopriv', array( 'publish' ) );
    } else {
      $status = apply_filters( 'rpress_product_dropdown_status', array( 'publish', 'draft', 'private', 'future' ) );
    }

    if ( is_array( $status ) && ! empty( $status ) ) {

      $status     = array_map( 'sanitize_text_field', $status );
      $status_in  = "'" . join( "', '", $status ) . "'";
      $where     .= "AND `post_status` IN ({$status_in}) ";

    } else {

      $where .= "AND `post_status` = `publish` ";

    }

    // Limit the result sets
    $limit = "LIMIT 50";

    $sql = $select . $where . $limit;

    $prepared_statement = $wpdb->prepare( $sql, '%' . $search . '%' );

    $items = $wpdb->get_results( $prepared_statement );

    if( $items ) {

      foreach( $items as $item ) {

        $results[] = array(
          'id'   => $item->ID,
          'name' => $item->post_title
        );

        if ( $variations && rpress_has_variable_prices( $item->ID ) ) {
          $prices = rpress_get_variable_prices( $item->ID );

          foreach ( $prices as $key => $value ) {
            $name   = ! empty( $value['name'] )   ? $value['name']   : '';
            $amount = ! empty( $value['amount'] ) ? $value['amount'] : '';
            $index  = ! empty( $value['index'] )  ? $value['index']  : $key;

            if ( $name && $index ) {
              $results[] = array(
                'id'   => $item->ID . '_' . $key,
                'name' => esc_html( $item->post_title . ': ' . $name ),
              );
            }
          }
        }
      }

    } else {

      $results[] = array(
        'id'   => 0,
        'name' => __( 'No results found', 'restropress' )
      );

    }

    echo json_encode( $results );

    rpress_die();
  }

  /**
   * Search the customers database via AJAX
   *
   * @since  1.0.0
   * @return void
   */
  public static function customer_search() {

    global $wpdb;

    $search  = esc_sql( sanitize_text_field( $_GET['s'] ) );
    $results = array();
    $customer_view_role = apply_filters( 'rpress_view_customers_role', 'view_shop_reports' );
    if ( ! current_user_can( $customer_view_role ) ) {
      $customers = array();
    } else {
      $select = "SELECT id, name, email FROM {$wpdb->prefix}rpress_customers ";
      if ( is_numeric( $search ) ) {
        $where = "WHERE `id` LIKE '%$search%' OR `user_id` LIKE '%$search%' ";
      } else {
        $where = "WHERE `name` LIKE '%$search%' OR `email` LIKE '%$search%' ";
      }
      $limit = "LIMIT 50";

      $customers = $wpdb->get_results( $select . $where . $limit );
    }

    if( $customers ) {

      foreach( $customers as $customer ) {

        $results[] = array(
          'id'   => $customer->id,
          'name' => $customer->name . '(' .  $customer->email . ')'
        );
      }

    } else {

      $customers[] = array(
        'id'   => 0,
        'name' => __( 'No results found', 'restropress' )
      );

    }

    echo json_encode( $results );

    rpress_die();
  }

  /**
   * Search the users database via AJAX
   *
   * @since 1.0.0
   * @return void
   */
  public static function user_search() {

    global $wpdb;

    $search         = esc_sql( sanitize_text_field( $_GET['s'] ) );
    $results        = array();
    $user_view_role = apply_filters( 'rpress_view_users_role', 'view_shop_reports' );

    if ( ! current_user_can( $user_view_role ) ) {
      $results = array();
    } else {
      $user_args = array(
        'search' => '*' . esc_attr( $search ) . '*',
        'number' => 50,
      );

      $users = get_users( $user_args );
    }

    if ( $users ) {

      foreach( $users as $user ) {

        $results[] = array(
          'id'   => $user->ID,
          'name' => $user->display_name,
        );
      }

    } else {

      $results[] = array(
        'id'   => 0,
        'name' => __( 'No users found', 'restropress' )
      );

    }

    echo json_encode( $results );

    rpress_die();
  }

  /**
   * Searches for users via ajax and returns a list of results
   *
   * @since  1.0.0
   * @return void
   */
  public static function search_users() {

    if( current_user_can( 'manage_shop_settings' ) ) {

      $search_query = trim( $_POST['user_name'] );
      $exclude      = trim( $_POST['exclude'] );

      $get_users_args = array(
        'number' => 9999,
        'search' => $search_query . '*'
      );

      if ( ! empty( $exclude ) ) {
        $exclude_array = explode( ',', $exclude );
        $get_users_args['exclude'] = $exclude_array;
      }

      $get_users_args = apply_filters( 'rpress_search_users_args', $get_users_args );

      $found_users = apply_filters( 'rpress_ajax_found_users', get_users( $get_users_args ), $search_query );

      $user_list = '<ul>';
      if( $found_users ) {
        foreach( $found_users as $user ) {
          $user_list .= '<li><a href="#" data-userid="' . esc_attr( $user->ID ) . '" data-login="' . esc_attr( $user->user_login ) . '">' . esc_html( $user->user_login ) . '</a></li>';
        }
      } else {
        $user_list .= '<li>' . __( 'No users found', 'restropress' ) . '</li>';
      }
      $user_list .= '</ul>';

      echo json_encode( array( 'results' => $user_list ) );

    }
    die();
  }

  /**
   * Check for new orders and send notification
   *
   * @since       2.0.1
   * @param       void
   * @return      json | user notification json object
   */
  public static function check_new_orders() {
    $last_order = get_option( 'rp_last_order_id' );
    $order      = rpress_get_payments( array( 'number' => 1 ) );

    if( is_array( $order ) && $order[0]->ID != $last_order ) {

      $payment_id = $order[0]->ID;

      $payment  = new RPRESS_Payment( $payment_id );

      $placeholder = array( '{order_id}' => $payment_id );

      $service_type = get_post_meta( $payment_id, '_rpress_delivery_type', true );

      if ( !empty( $service_type ) ) {
        $service_type = ucfirst( $service_type );
      }

      $service_date = get_post_meta( $payment_id, '_rpress_delivery_date', true );

      if ( !empty( $service_date ) ) {
        $service_date = rpress_local_date( $service_date );
      }

      $payment_status = $payment->status;

      if ( $payment_status == 'publish' ) {
        $payment_status = 'Paid';
      }

      $payment_status = ucfirst( $payment_status );

      $search = array( '{order_id}', '{service_type}', '{payment_status}', '{service_date}' );

      $replace = array( $payment_id, $service_type, $payment_status, $service_date );

      $body = rpress_get_option( 'notification_body' );
      $body = str_replace( $search, $replace, $body );

      $notification = array(
        'title' => rpress_get_option( 'notification_title' ),
        'body'  => $body,
        'icon'  => rpress_get_option( 'notification_icon' ),
        'sound' => rpress_get_option( 'notification_sound' ),
        'url'   => admin_url( 'admin.php?page=rpress-payment-history&view=view-order-details&id=' . $payment_id )
      );
      update_option( 'rp_last_order_id', $payment_id  );
      wp_send_json( $notification );
    }
    wp_die();
  }
  
}

RP_CUSTOME_AJAX::init();
