<?php
$customlink = EX_WPFood_customlink(get_the_ID());
global $number_excerpt,$img_size;

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
if($img_size==''){$img_size = 'exwoofood_80x80';}
?>
<figure class="fdstyle-list-2">
  <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>">
    <?php if(has_post_thumbnail(get_the_ID())){ ?>
      <div class="exf-img">
        <?php the_post_thumbnail($img_size); ?>
        <?php exwf_icon_color(get_the_ID()); ?>
      </div>
    <?php }?>
  </a>
  <div class="fdlist_2_detail">
    <div class="fdlist_2_title">
      <div class="fdlist_2_name exfd-list-name">
        <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>">
            <?php the_title(); ?>
        </a>
        <?php 
        $prod = wc_get_product(get_the_ID());
        $cls_ost = '';
        if ( is_object($prod) && method_exists( $prod, 'get_stock_status' ) && $prod->get_stock_status()=='outofstock' ) {
          $cls_ost = 'exwf-ofstock';
          echo '<span>'.esc_html__( ' - Sold Out', 'woocommerce-food' ).'</span>';
        }
        ?>    
      </div>
      <div class="fdlist_2_price <?php echo esc_attr($cls_ost); ?>">
        <span>
          <?php echo wp_kses_post($price);?>
        </span>
        <?php 
        $id = get_the_ID();
        if (exwf_check_open_close_time($id) && exwoofood_get_option('exwoofood_booking') !='disable') { ?>
          <span class="exwoofood-addicon">
            <?php echo '<div class="ex-hidden">'; exwoofood_booking_button_html(1); echo '</div>';?>
            <button class="exfd_modal_click exfd-choice" data="food_id=<?php echo get_the_ID(); ?>&food_qty=1"><div class="exfd-icon-plus"></div></button>
          </span>
        <?php }?>
      </div>
    </div>
  </div>
  <?php
    echo wp_kses_post($excerpt);
  ?>
</figure>