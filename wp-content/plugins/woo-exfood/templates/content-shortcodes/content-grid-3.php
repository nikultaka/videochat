<?php
  $customlink = EX_WPFood_customlink(get_the_ID());
  global $number_excerpt,$img_size;
  if($img_size==''){$img_size = 'exwoofood_400x400';}
  
  $custom_price = get_post_meta( get_the_ID(), 'exwoofood_custom_price', true );
  $price = exwoofood_price_with_currency();
  if ($custom_price != '') {
    $price = $custom_price;
  }
  $excerpt = '';
  if(has_excerpt(get_the_ID())){
    if($number_excerpt=='full'){
      $excerpt = get_the_excerpt();
    }else if($number_excerpt!='0'){
      $excerpt = wp_trim_words(get_the_excerpt(),$number_excerpt,'...');
    }
    $excerpt = '<p>'.$excerpt.'</p>';
  }
?>
<figure class="exstyle-3 tppost-<?php the_ID();?> <?php if($number_excerpt !='0'){ echo "exstyle-3-center"; }?>">
  <div class="exstyle-3-image ex-fly-cart" style="background-image: url(<?php echo get_the_post_thumbnail_url(get_the_ID(),$img_size); ?>)">
    <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>"></a>
    <?php exwoofood_sale_badge();
    $prod = wc_get_product(get_the_ID());
    $cls_ost = '';
    $st_stt = esc_html__( 'Order', 'woocommerce-food' );
    if ( is_object($prod) && method_exists( $prod, 'get_stock_status' ) && $prod->get_stock_status()=='outofstock' ) {
      $cls_ost = 'exwf-ofstock';
      $st_stt = esc_html__( 'Sold Out', 'woocommerce-food' );
    }
    exwf_icon_color();
    ?>
      <div class="exbt-inline <?php echo esc_attr($cls_ost); ?>">
        <a href="<?php echo esc_url($customlink); ?>" class="exstyle-3-button"><?php echo $st_stt; ?></a>
      </div>
  </div><figcaption>
    <h3><a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>"><?php the_title(); ?></a></h3>
    <h5>
      <?php echo wp_kses_post($price);?>
    </h5>
    <?php 
    echo wp_kses_post($excerpt);
    ?>
  </figcaption>
</figure>