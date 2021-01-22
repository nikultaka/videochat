<style>
#rpress-email-food-list, #rpress-email-food-list td, #rpress-email-food-list th {
  border: 1px solid #ddd;
  text-align: left;
}
#rpress-email-food-list  {
  border-collapse: collapse;
  width: 100%;
}
#rpress-email-food-list th, #rpress-email-food-list td {
  padding: 15px;
}
</style>

<table id="rpress-email-food-list" class="rpress-table">
  <thead>
    <th><?php _e( 'Name', 'restropress' ); ?></th>
    <th><?php _e( 'Price', 'restropress' ); ?></th>
  </thead>
  <tbody>
      <?php $product_details = get_product_details_cutome($item['id']); ?>
  <?php if( is_array( $rpress_email_fooditems ) ) : ?>
    <?php
    foreach ( $rpress_email_fooditems as $key => $item ) : ?>
      <?php $row_price = array(); ?>
      <tr>
        <td>
          <div class="rpress_email_receipt_product_name">
            <?php echo $item['quantity']; ?> X <?php echo isset($product_details['item_name']) ? $product_details['item_name'] : get_the_title( $item['id'] ); ?> (<?php echo isset($product_details['price'][0]) ? rpress_currency_filter(rpress_format_amount($product_details['price'][0])) : rpress_price( $item['id'] ); ?>)
            <?php
              if( !empty( $item['options'] ) ) {
                foreach( $item['options'] as $k => $v ) {
                  if( is_array( $v ) ) {
                    array_push( $row_price, $v['price'] );
                    if( !empty( $v['addon_item_name'] ) ) {
                      ?>
                      <br/>&nbsp;&nbsp;<small class="rpress-receipt-addon-item"><?php echo $v['addon_item_name']; ?> (<?php echo rpress_currency_filter(rpress_format_amount($v['price'])); ?>)</small>
                      <?php
                    }
                  }
                }
              }
            ?>
          </div>
        </td>
        <td>
          <?php
          $addon_price = array_sum( $row_price );
          $total_price = $addon_price + rpress_get_fooditem_price( $item['id'] );
          ?>
          <?php echo rpress_currency_filter( rpress_format_amount( $total_price ) ); ?>
        </td>
      </tr>
    <?php endforeach; ?>
  <?php endif; ?>
  </tbody>
</table>