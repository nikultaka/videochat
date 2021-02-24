<?php
/*
 * Display tradelines shop in style 1
 */

class Woo_TRLS_Style_1 {

	/**
	 * Construct style 1
	 *
	 * @since  1.0.0
	 */
	public function __construct( $atts ) {
		 
		$this->render( $atts );
	}

	/**
	 * Display table with tradelines
	 *
	 * @since 1.0.0
	 */
	public function render( $atts ) {
	
		$category = isset( $atts['id'] )  ? $atts['id'] : false;
	
		if( $category ){
			$total_products = wc_get_products( array( 'type' => WOO_TRLS_PRODUCT_TYPE, 'category' => $category ) );
		}else{
			$total_products = wc_get_products( array( 'type' => WOO_TRLS_PRODUCT_TYPE ) );
		}
		$count          = count( $total_products );
		$total          = round( $count / Woo_TRLS_Pagination::get_limit( 1 ) );

		$orderby = isset( $_GET['trls_orderby'] ) ? esc_attr( $_GET['trls_orderby'] ) : 'id';
		$order   = isset( $_GET['trls_order'] ) && 'ASC' == $_GET['trls_order'] ? 'ASC' : 'DESC';
		
		

		$page = isset( $_GET['trls_page'] ) ? $_GET['trls_page'] : 1;

		$args = array(
			'limit'   => Woo_TRLS_Pagination::get_limit(1),
			'page'    => $page,
			'type'    => WOO_TRLS_PRODUCT_TYPE,
			'orderby' => $orderby,
			'order'   => $order,
		);

		// filter by category
		if( $category ){
			if( is_numeric ( $category  ) ){
				$args['category'] = array( get_term( $category )->slug );
			}else{
				$args['category'] = array( $category );
			}			
		}

		if ( in_array( $orderby, Woo_TRLS_Order::$meta_fields ) ) {
			$args['orderby']  = 'meta_value';
			$args['meta_key'] = $orderby;
		}
 
 
		$tradelines = wc_get_products( $args );
		?>

        <table class="woo-trls-table s1">
            <thead>
            <tr>
                <td><?php Woo_TRLS_Order::order_link( 'Bank Name', 'name' ); ?>
                    <!--<span class="woo-trls-tip"></span>--> </td>
                <td><?php Woo_TRLS_Order::order_link( 'Card ID', 'ID' ); ?></td>
                <td><?php Woo_TRLS_Order::order_link( 'Credit Limit', 'woo_tradeline_limit' ); ?></td>
                <td><?php Woo_TRLS_Order::order_link( 'Credit Account Type', 'woo_tradeline_typeaccount' ); ?></td>
                <td><?php Woo_TRLS_Order::order_link( 'Date Opened', 'woo_tradeline_openeddate' ); ?></td>
                <td><?php Woo_TRLS_Order::order_link( 'Soft Pull', 'woo_tradeline_softpull' ); ?></td>
                <td><?php Woo_TRLS_Order::order_link( 'Reporting Period', 'woo_tradeline_report' ); ?></td>
                <td><?php Woo_TRLS_Order::order_link( 'Availability', '_stock' ); ?></td>
                <td><?php Woo_TRLS_Order::order_link( 'Price', '_price' ); ?></td>
                <td>Add Individual To Cart</td>
            </tr>
            </thead>
            <tbody>
			<?php foreach ( $tradelines as $tradeline ) { ?>
                <tr>
                    <td><?php echo $tradeline->get_thumb_style( 1 ); ?></td>
                    <td><?php echo $tradeline->get_id(); ?></td>
                    <td><?php echo $tradeline->get_limit(); ?></td>
                    <td><?php echo $tradeline->get_typeaccount(); ?></td>
                    <td><?php echo $tradeline->get_openeddate(); ?></td>
                    <td><?php echo $tradeline->get_softpull(); ?></td>
                    <td><?php echo $tradeline->get_reporting_period(); ?></td>
                    <td class="<?php if ( $tradeline->get_stock_quantity() > 0 ) { ?>in-stock<?php } else { ?>out-of-stock<?php } ?>">
						<?php
						if ( $tradeline->get_stock_quantity() > 0 ) {
							echo $tradeline->get_stock_quantity() . ' ';
						}
						echo $tradeline->get_stock_status();
						?>
                    </td>
                    <td><?php echo $tradeline->get_price_html(); ?></td>
                    <td>
						<?php if ( $tradeline->get_stock_quantity() < 1 ) { ?>
                            <a href="<?php echo get_permalink( $tradeline->get_id() ); ?>" class="btn btn-danger">Out of
                                stock</a>
						<?php } else { ?>
                            <a href="?add-to-cart=<?php echo $tradeline->get_id(); ?>" data-quantity="1"
                               class="btn btn-primary success add_to_cart_button ajax_add_to_cart"
                               data-product_id="<?php echo $tradeline->get_id(); ?>" data-product_sku=""
                               aria-label="Add to cart" rel="nofollow">Add to cart</a>
						<?php } ?>
                    </td>
                </tr>
			<?php } ?>
            </tbody>
			<?php if ( $count > Woo_TRLS_Pagination::get_limit( 1 ) ) { ?>
                <tfoot>
                <tr>
                    <td colspan="9">
						<?php Woo_TRLS_Pagination::display_pagination( $page, $total ); ?>
                    </td>
                </tr>
                </tfoot>
			<?php } ?>
        </table>
		<?php

	}

}

/**
 * Run Woo_TRLS_Style_1 class
 *
 * @since 1.0.0
 *
 * @return Woo_TRLS_Style_1
 */
function woo_trls_style_1_runner( $atts ) {

	return new Woo_TRLS_Style_1( $atts );
}

woo_trls_style_1_runner( $atts );