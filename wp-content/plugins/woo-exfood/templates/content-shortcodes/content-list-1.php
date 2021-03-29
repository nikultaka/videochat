<?php
$customlink = EX_WPFood_customlink(get_the_ID());
global $number_excerpt,$img_size;
$custom_price = get_post_meta( get_the_ID(), 'exwoofood_custom_price', true );
$price = exwoofood_price_with_currency();
if ($custom_price != '') {
  $price = $custom_price;
}
$class_add = '';
if(!has_excerpt(get_the_ID())){
    $class_add = " ex-no-description";
}
if($img_size==''){$img_size = 'exwoofood_80x80';}
$id = get_the_ID();
$excerpt = '';
  if(has_excerpt($id)){
    if($number_excerpt=='full'){
      $excerpt = get_the_excerpt();
    }else if($number_excerpt!='0'){
      $excerpt = wp_trim_words(get_the_excerpt(),$number_excerpt,'...');
    }
    $excerpt = '<p>'.$excerpt.'</p>';
  }
?>
<figure class="fdstyle-list-1 <?php echo esc_attr($class_add);?>">
  <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>">
    <?php if(has_post_thumbnail(get_the_ID())){ ?>
      <div class="exf-img">
        <?php the_post_thumbnail($img_size); ?>
        <?php exwf_icon_color(get_the_ID()); ?>
      </div>
    <?php }?>
  </a>
  <div class="fdlist_1_detail">
    <div class="fdlist_1_title">
      <div class="fdlist_1_name exfd-list-name">
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
      <div class="fdlist_1_price">
        <span>
        <?php echo wp_kses_post($price);?>
        </span>
        
      </div>
    </div>
  </div>
  <div class="fdlist_1_des <?php echo esc_attr($cls_ost); ?>">
    <?php 
    if(has_excerpt($id)){
      ?>
      <p><?php echo wp_kses_post($excerpt); ?></p>
    <?php }
    echo '<div class="ex-hidden">'; exwoofood_booking_button_html(1); echo '</div>';
    ?>
    <?php if (exwf_check_open_close_time($id) && exwoofood_get_option('exwoofood_booking') !='disable') { ?>
     <button class="exfd_modal_click exfd-choice"><div class="exfd-icon-plus"></div></button>
    <?php }?>
    
  </div>
</figure>