<?php
/*
 * Display tradelines shop in style 1
 */

class Woo_TRLS_Style_All {

	/**
	 * Construct style all
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
	
		$category = isset( $atts['id'] )  ? (int)$atts['id'] : false;
		$order_link = '&by=' . ( isset( $_GET['by'] ) || $_GET['by'] == 'DESC' ? 'ASC' : 'DESC' );

		$args = array(
		'limit'   => 0,
			'page'    => 1,
			'type'    => WOO_TRLS_PRODUCT_TYPE,
			'orderby' => isset( $_GET['order'] ) ? esc_attr( $_GET['order'] ) : 'id',
			'order'   => isset( $_GET['by'] ) && in_array( $_GET['by'], [ 'DESC', 'ASC' ] ) ? $_GET['by'] : 'DESC',
		);
		
		// filter by category
		if( $category ){
			$args['category'] = array( get_term( $category )->slug );
		}

		$tradelines = wc_get_products( $args );
		?>

		<select id="tradeline-table-style">
			<option value="style1">Style 1</option>
			<option value="style2">Style 2</option>
			<option value="style3">Style 3</option>
		</select>

        <table class="woo-trls-table">
            <thead>
            <tr>
                <td>
                    Bank Name <!--<span class="woo-trls-tip"></span>-->
                </td>
                <td><a href="?order=id<?php echo $order_link; ?>" class="woo-trls-order">Card ID</a></td>
                <td>Credit Limit</td>
                <td>Date Opened</td>
                <td>Purchase By Date</td>
                <td>Reporting Period</td>
                <td>Availability</td>
                <td>Price</td>
                <td>Add Individual To Cart</td>
            </tr>
            </thead>
            <tbody>
			<?php foreach ( $tradelines as $tradeline ) { ?>
                <tr>
                    <td name="tradeline-thumb-style-1"><?php echo $tradeline->get_thumb_style(1); ?></td>
                    <td name="tradeline-thumb-style-2" class="woo-trls-hidden"><?php echo $tradeline->get_thumb_style(2); ?></td>
                    <td name="tradeline-thumb-style-3" class="woo-trls-hidden"><?php echo $tradeline->get_thumb_style(3); ?></td>
                    <td><?php echo $tradeline->get_id(); ?></td>
                    <td><?php echo $tradeline->get_limit(); ?></td>
                    <td><?php echo $tradeline->get_issue_date(); ?></td>
                    <td><?php echo $tradeline->get_purchase_by_date(); ?></td>
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
        </table>
		<?php

	}

}

/**
 * Run Woo_TRLS_Style_All class
 *
 * @since 1.0.0
 *
 * @return Woo_TRLS_Style_All
 */
function woo_trls_style_all_runner( $atts ) {

	return new Woo_TRLS_Style_All( $atts );
}

woo_trls_style_all_runner( $atts );