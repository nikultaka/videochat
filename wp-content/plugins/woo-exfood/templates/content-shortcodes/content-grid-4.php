<?php
  $customlink = EX_WPFood_customlink(get_the_ID());
  global $number_excerpt,$img_size;
  if($img_size==''){$img_size = 'exwoofood_400x400';}
  $custom_price = get_post_meta( get_the_ID(), 'exwoofood_custom_price', true );
  $price = exwoofood_price_with_currency();
  if ($custom_price != '') {
    $price = $custom_price;
  }
?>
<figure class="exstyle-4 tppost-<?php the_ID();?>">
  
  <div class="exstyle-4-image">
    <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>">
      <?php echo get_the_post_thumbnail(get_the_ID(),$img_size); 
      exwf_icon_color();?>
    </a>
    <?php exwoofood_sale_badge(); 
    $prod = wc_get_product(get_the_ID());
    $cls_ost = $st_stt = '';
    if ( is_object($prod) && method_exists( $prod, 'get_stock_status' ) && $prod->get_stock_status()=='outofstock' ) {
      $cls_ost = 'exwf-ofstock';
      $st_stt = '<span>'.esc_html__( ' - Sold Out', 'woocommerce-food' ).'</span>';
    }
    ?>
  </div><figcaption class="<?php echo esc_attr($cls_ost); ?>">
    <h3><a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>"><?php the_title(); echo $st_stt; ?></a></h3>
    <?php 
    $id = get_the_ID();
    if(has_excerpt($id)){
        if($number_excerpt=='full'){
          $excerpt = get_the_excerpt();
          ?><p><?php echo wp_kses_post($excerpt); ?></p><?php
        }else if($number_excerpt!='0'){
          $excerpt = wp_trim_words(get_the_excerpt(),$number_excerpt,'...');
          ?><p><?php echo wp_kses_post($excerpt); ?></p><?php
        }
    }
    ?>
    <h5>
      <?php echo wp_kses_post($price);?>
    </h5>
    <?php echo '<div class="ex-hidden">'; exwoofood_booking_button_html(1); echo '</div>';?>
    <?php if (exwf_check_open_close_time($id) && exwoofood_get_option('exwoofood_booking') !='disable') { ?>
      <button class="exstyle-4-button exfd-choice"><div class="exfd-icon-plus"></div></button>
    <?php }?>
  </figcaption>
</figure>