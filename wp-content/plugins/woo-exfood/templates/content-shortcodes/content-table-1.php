<?php
$customlink = EX_WPFood_customlink(get_the_ID());
global $number_excerpt,$product,$img_size;
$order_price = $product->get_price();
$category = get_the_terms(get_the_ID(),'product_cat');
$menu ='';
if(!empty($category)){
  foreach($category as $cd){
    $cat = get_category( $cd );
    $menu .= '<p>'.$cat->name.'</p>';
  }
}
$custom_price = get_post_meta( get_the_ID(), 'exwoofood_custom_price', true );
$price = exwoofood_price_with_currency();
if ($custom_price != '') {
  $price = $custom_price;
}
$id = 'ctc-'.rand(1,10000).'-'.get_the_ID();
if($img_size==''){$img_size = 'exwoofood_80x80';}
?>
<tr data-id_food="<?php echo get_the_ID()?>" id="<?php echo esc_attr($id);?>">
  <?php exwf_custom_color('table','',$id);?>
  <td><a href="<?php echo esc_url($customlink); ?>"><?php the_post_thumbnail($img_size); ?><?php exwf_icon_color(get_the_ID()); ?></a></td>
  <td id="extd-<?php echo get_the_ID()?>" class="ex-fd-name" data-sort="<?php echo esc_attr(get_the_title());?>">
    <?php echo '<div class="item-grid tppost-'.get_the_ID().'" ';?>
      <div class="exp-arrow">
        <h3><a href="<?php echo esc_url($customlink); ?>"><?php the_title(); ?></a></h3>
        <span class="exfd-show-tablet">
          <?php echo esc_html_e( 'Category:', 'woocommerce-food' ).wp_kses_post($menu); ?>
        </span>
        <div class="exfd-hide-mb">
          <div class="exfd-price-detail">
            <?php 
              echo wp_kses_post($price);
            ?>
          </div>
          <?php if($number_excerpt != '0'){?>
            <?php if(has_excerpt(get_the_ID())){
              if($number_excerpt=='full'){
                $excerpt = get_the_excerpt();
                ?><p><?php echo wp_kses_post($excerpt); ?></p><?php
              }else if($number_excerpt!='0'){
                $excerpt = wp_trim_words(get_the_excerpt(),$number_excerpt,'...');
                ?><p><?php echo wp_kses_post($excerpt); ?></p><?php
              }
            } ?>  
          <?php }?>
        </div>
      </div>
    </div>
  </td>
  <?php if($number_excerpt != '0'){?>
  <td class="exfd-hide-screen ex-fd-table-des">
      <?php if(has_excerpt(get_the_ID())){?>
            <p><?php echo wp_kses_post($excerpt); ?></p>
      <?php } ?>
  </td>
  <?php }?>
  
  <td class="exfd-hide-screen exfd-hide-tablet ex-fd-category" data-sort="<?php echo esc_attr($menu);?>">
    <?php echo wp_kses_post($menu); ?>
  </td>

  <td class="exfd-hide-screen exfd-price" data-sort="<?php echo esc_attr($order_price);?>">
    <div class="exfd-price-detail">
    <?php echo wp_kses_post($price);?>
    </div>
  </td>
  <td class="ex-fd-table-order">
    <?php 
    if(exwf_check_open_close_time(get_the_ID())){
      echo '<div class="ex-hidden">'; exwoofood_booking_button_html(1); echo '</div>'; 
    }else{
      echo '<div class="ex-hidden"><a href="'.get_the_permalink(get_the_ID()).'" class="exstyle-1-button"></a></div>'; 
    }
    $prod = wc_get_product(get_the_ID());
    if ( is_object($prod) && method_exists( $prod, 'get_stock_status' ) && $prod->get_stock_status()=='outofstock' ) {
      echo '<span>'.esc_html__( 'Sold Out', 'woocommerce-food' ).'</span>';
    }else{
    ?>  
      <button class="exfd_modal_click exfd-choice" data="food_id=<?php echo esc_attr(get_the_ID()); ?>&food_qty=1"><div class="exfd-icon-plus"></div></button>
    <?php }?>  
  </td>
</tr>
