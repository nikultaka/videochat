<?php
/*
 * Display tradelines shop in style 1
 */

class Woo_TRLS_Style_3 {

	/**
	 * Construct style 3
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
		
		$total_products = wc_get_products( array( 'type' => WOO_TRLS_PRODUCT_TYPE ) );
		$count          = count( $total_products );
		$total          = round( $count / Woo_TRLS_Pagination::get_limit( 3 ) );

		$orderby = isset( $_GET['trls_orderby'] ) ? esc_attr( $_GET['trls_orderby'] ) : 'id';
		$order   = isset( $_GET['trls_order'] ) && 'ASC' == $_GET['trls_order'] ? 'ASC' : 'DESC';

		$page = isset( $_GET['trls_page'] ) ? $_GET['trls_page'] : 1;

		$args = array(
			'limit'   => Woo_TRLS_Pagination::get_limit(3),
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

        <div class="woo-trls-wrapper">
            <div class="woo-trls-orders">
                Order by:
				<?php Woo_TRLS_Order::order_link( 'Bank Name', 'name' ) ?>
				<?php Woo_TRLS_Order::order_link( 'Card ID', 'ID' ) ?>
				<?php Woo_TRLS_Order::order_link( 'Credit Limit', 'woo_tradeline_limit' ); ?>
				<?php Woo_TRLS_Order::order_link( 'Date Opened', 'woo_tradeline_openeddate' ); ?>
				<?php Woo_TRLS_Order::order_link( 'Soft Pull', 'woo_tradeline_softpull' ); ?>
				<?php Woo_TRLS_Order::order_link( 'Reporting Period', 'woo_tradeline_report' ); ?>
				<?php Woo_TRLS_Order::order_link( 'Availability', '_stock' ); ?>
				<?php Woo_TRLS_Order::order_link( 'Price', '_price' ); ?>
            </div>
            <div class="woo-trls-items">
				<?php foreach ( $tradelines as $tradeline ) { ?>
                    <div class="woo-trls-item">
						<?php if ( $tradeline->get_stock_quantity() <= 0 ) { ?>
                            <div class="woo-trls-out-of-stock">Out
                                of stock
                            </div>
						<?php } ?>

                        <div class="woo-trls-thumb">
                            <a href="<?php echo get_permalink( $tradeline->get_id() ); ?>">
								<?php echo $tradeline->get_thumb_style( 3 ); ?>
                            </a>
                        </div>
                        <div class="woo-trls-title">
                            <a href="<?php echo get_permalink( $tradeline->get_id() ); ?>"><?php echo $tradeline->get_title(); ?></a>
                        </div>
                        <div class="woo-trls-item-smalls">
                            <div class="woo-trls-item-small">
                                <div class="trl-title"><span>Credit limit</span></div>
                                <span><?php echo $tradeline->get_limit(); ?></span>
                            </div>
                            <div class="woo-trls-item-small">
                                <div class="trl-title"><span>Date opened</span></div>
                                <span><?php echo $tradeline->get_openeddate(); ?></span>
                            </div>
                            <div class="woo-trls-item-small left-border">
                                <div class="trl-title"><span> Reporting</span></div>
                                <span><?php echo $tradeline->get_reporting_period(); ?></span>
                            </div>
                            <div class="woo-trls-item-small">
                                <div class="trl-title"><span>Availability</span></div>
                                <span>
                                    <?php
                                    if ( $tradeline->get_stock_quantity() > 0 ) {
	                                    echo $tradeline->get_stock_quantity() . ' ';
                                    }
                                    echo $tradeline->get_stock_status();
                                    ?>
                                </span>
                            </div>
                            <div class="woo-trls-item-small">
                                <div class="trl-title"><span>Price</span></div>
								<?php echo $tradeline->get_price_html(); ?>
                            </div>
                            <div class="woo-trls-item-small left-border">
								<?php if ( $tradeline->get_stock_quantity() > 0 ) { ?>
                                    <a href="?add-to-cart=<?php echo $tradeline->get_id(); ?>" data-quantity="1"
                                       class="btn btn-primary success add_to_cart_button ajax_add_to_cart"
                                       data-product_id="<?php echo $tradeline->get_id(); ?>" data-product_sku=""
                                       aria-label="Add to cart" rel="nofollow">BUY NOW</a>
								<?php } else { ?>
                                    <a href="<?php echo get_permalink( $tradeline->get_id() ); ?>" class="out-of-stock">OUT
                                        OF STOCK</a>
								<?php } ?>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
			<?php if ( $count > Woo_TRLS_Pagination::get_limit( 3 ) ) { ?>
                <div class="woo-trls-pagination-wrapper">
					<?php Woo_TRLS_Pagination::display_pagination( $page, $total ); ?>
                </div>
			<?php } ?>
        </div>
		<?php

	}

}

/**
 * Run Woo_TRLS_Style_3 class
 *
 * @since 1.0.0
 *
 * @return Woo_TRLS_Style_3
 */
function woo_trls_style_3_runner(  $atts ) {

	return new Woo_TRLS_Style_3( $atts );
}

woo_trls_style_3_runner( $atts );