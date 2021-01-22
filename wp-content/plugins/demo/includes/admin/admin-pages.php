<?php
/**
 * Admin Pages
 *
 * @package     RPRESS
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2018, Magnigenie
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( class_exists( 'RP_Admin_Menus', false ) ) {
	return new RP_Admin_Menus();
}


/**
 * RP_Admin_Menus Class.
 */
class RP_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus.
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		
		//Custom menu ordering
		add_filter( 'custom_menu_order', '__return_true' );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
	}

	/**
	 * Add menu items.
	 */
	public function admin_menu() {
		global $menu;

		$menu[] = array( '', 'read', 'separator-restropress', '', 'wp-menu-separator restropress' );

		$rpress_payment 	= get_post_type_object( 'rpress_payment' );
		$customer_view_role = apply_filters( 'rpress_view_customers_role', 'view_shop_reports' );

		add_menu_page( __( 'Mammoth Food', 'restropress' ), __( 'Mammoth Food', 'restropress' ), 'manage_shop_settings', 'restropress', null, null, '55.5' );
		

		add_submenu_page( 'restropress', $rpress_payment->labels->name, $rpress_payment->labels->menu_name, 'edit_shop_payments', 'rpress-payment-history', 'rpress_payment_history_page', null , null );
		//add_submenu_page( 'restropress', 'Order', 'Order', 'edit_shop_payments', 'rpress-payment-history-custome', 'rpress_payment_history_page_custome', null , null );
                

		add_submenu_page( 'restropress', __( 'Customers', 'restropress' ), __( 'Customers', 'restropress' ), $customer_view_role, 'rpress-customers', 'rpress_customers_page', null, null );
		//add_submenu_page( 'restropress', __( 'Customer', 'restropress' ), __( 'Customer', 'restropress' ), $customer_view_role, 'rpress-customers-custome', 'rpress_customers_page_custome', null, null );
                
		//add_submenu_page( 'restropress', __( 'Discount Codes', 'restropress' ), __( 'Discount Codes', 'restropress' ), 'manage_shop_discounts', 'rpress-discounts', 'rpress_discounts_page' );

		add_submenu_page( 'restropress', __( 'Earnings and Sales Reports', 'restropress' ), __( 'Reports', 'restropress' ), 'view_shop_reports', 'rpress-reports', 'rpress_reports_page' );

		add_submenu_page( 'restropress', __( 'RestroPress Settings', 'restropress' ), __( 'Settings', 'restropress' ), 'manage_shop_settings', 'rpress-settings', 'rpress_options_page' );

		add_submenu_page( 'restropress', __( 'RestroPress Info and Tools', 'restropress' ), __( 'Tools', 'restropress' ), 'manage_shop_settings', 'rpress-tools', 'rpress_tools_page' );

		add_submenu_page( 'restropress', __( 'RestroPress Extensions', 'restropress' ), '<span style="color:#f39c12;">' . __( 'Extensions', 'restropress' ) . '</span>', 'manage_shop_settings', 'rpress-extensions', 'rpress_extensions_page' );
                add_menu_page( 'Food Items', 'Food Items', 'manage_options', 'fooditem', Array("RP_CUSTOME_AJAX", "fooditem_list"), null, '55.5' );
				add_submenu_page( 'fooditem', "Add Fooditem", "Add Fooditem", 'manage_options', 'rpress-add_fooditem', Array("RP_CUSTOME_AJAX", "add_fooditem") );
				add_submenu_page( 'fooditem', "Add Merchant", "Add Merchant", 'manage_options', 'rpress-add_merchant', Array("RP_CUSTOME_AJAX", "add_merchant") );
                                add_submenu_page( 'fooditem', "Food Category", "Food Category", 'manage_options', 'rpress-add_food_category', Array("RP_CUSTOME_AJAX", "add_food_category") );
                                add_submenu_page( 'fooditem', "Addon Category", "Addon Category", 'manage_options', 'rpress-add_addon_category', Array("RP_CUSTOME_AJAX", "add_addon_category") );
                                add_submenu_page( 'fooditem', "Addon Items", "Addon Items", 'manage_options', 'rpress-add_addon_items', Array("RP_CUSTOME_AJAX", "add_addon_items") );
                                add_submenu_page( 'fooditem', "Sizes", "Sizes", 'manage_options', 'rpress-manage_sizes', Array("RP_CUSTOME_AJAX", "list_sizes") );
                                add_submenu_page( 'fooditem', "Offers", "Offers", 'manage_options', 'rpress-manage_offer', Array("RP_CUSTOME_AJAX", "list_offers") );
                                add_submenu_page( 'fooditem', "Voucher", "Voucher", 'manage_options', 'rpress-manage_voucher', Array("RP_CUSTOME_AJAX", "list_voucher") );
		// Remove the additional restropress menu
		remove_submenu_page( 'restropress', 'restropress' );

	}

	/**
	 * Reorder the WC menu items in admin.
	 *
	 * @param int $menu_order Menu order.
	 * @return array
	 */
	public function menu_order( $menu_order ) {

		// Initialize our custom order array.
		$rpress_menu_order = array();

		// Get the index of our custom separator.
		$rpress_separator = array_search( 'separator-restropress', $menu_order, true );

		// Get index of fooditem menu.
		$rpress_fooditems = array_search( 'edit.php?post_type=fooditem', $menu_order, true );

		//Remove the custom separator and fooditems menu so that we can re-order them
		unset( $menu_order[ $rpress_separator ] );
		unset( $menu_order[ $rpress_fooditems ] );

		// Loop through menu order and do some rearranging.
		foreach ( $menu_order as $index => $item ) {

			if ( 'restropress' === $item ) {
				$rpress_menu_order[] = 'separator-restropress';
				$rpress_menu_order[] = $item;
				$rpress_menu_order[] = 'edit.php?post_type=fooditem';
			} elseif ( ! in_array( $item, array( 'separator-restropress' ), true ) ) {
				$rpress_menu_order[] = $item;
			}
		}
		// Return order.
		return $rpress_menu_order;
	}
}

return new RP_Admin_Menus();
