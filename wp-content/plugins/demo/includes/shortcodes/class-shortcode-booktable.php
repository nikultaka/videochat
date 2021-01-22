<?php
/**
 * Food Items Shortcode
 *
 * @package RestroPress/Shortcodes/BookTable
 * @version 2.6
 */

defined( 'ABSPATH' ) || exit;

/**
 * Shortcode Food Items Class.
 */
class RP_Shortcode_Booktable {

	/**
	 * Food Items Attributes Shortcode
	 *
	 * @var array
	 * @since 1.0
	 */
	public static $atts = array();

	

	/**
	 * Output the Food Items shortcode.
	 *
	 * @param array $atts Shortcode attributes.
	 */
	public static function output( $atts ) {
            rpress_get_template_part( 'fooditem/booktable' );
	}
        public function repress_book_table(){
            $post = $_POST;
            $result['insert_id'] = 0;
            $result['message'] = "Please try again.!";
            if(!empty($post)){
              $user_data = get_user_details_auth();
              $temp_user = json_decode($user_data); 
              $data['merchant_id'] = isset($temp_user->merchant_id) ? $temp_user->merchant_id : 2;
              $data['number_guest'] = isset($post['NumberOfGuest']) ? $post['NumberOfGuest'] : '';
              $data['date_booking'] = isset($post['DateOfBooking']) ? date('Y-m-d', strtotime($post['DateOfBooking'])) : '';
              $data['booking_time'] = isset($post['TimeOfBooking']) ? date('H:i', strtotime($post['TimeOfBooking'])): 0;
              $data['booking_name'] = isset($post['NameOfGuest']) ? $post['NameOfGuest'] : '';
              $data['email'] = isset($post['EmailOfGuest']) ? $post['EmailOfGuest'] : '';
              $data['mobile'] = isset($post['MobileOfGuest']) ? $post['MobileOfGuest'] : '';
              $data['booking_notes'] = isset($post['Instructions']) ? $post['Instructions'] : '';
              
              $response = insert_book_a_table($data);
              echo $response;
              die;
            }
            echo json_encode($response);
            die;
        }
}
$RP_Shortcode_Booktable = new RP_Shortcode_Booktable();

add_action('wp_ajax_RP_Shortcode_Booktable::repress_book_table', Array('RP_Shortcode_Booktable', 'repress_book_table'));
add_action('wp_ajax_nopriv_RP_Shortcode_Booktable::repress_book_table', array('RP_Shortcode_Booktable', 'repress_book_table'));
