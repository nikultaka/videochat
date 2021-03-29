<?php
  $customlink = EX_WPFood_customlink(get_the_ID());
  global $number_excerpt,$img_size;
  if($img_size==''){$img_size = 'exwoofood_80x80';}
?>
<figure class="fdstyle-list-3">
  
    <a class="exfd_modal_click" href="<?php echo esc_url($customlink); ?>">
      <?php if(has_post_thumbnail(get_the_ID())){ ?>
      <div class="exf-img">
        <?php the_post_thumbnail($img_size); ?>
        <?php exwf_icon_color(get_the_ID()); ?>  
      </div>
      <?php }?>
    </a>
    <div class="fdlist_3_title">
      <div class="fdlist_3_name exfd-list-name"><h3>
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
        ?></h3></div>
    </div>
    <div class="fdlist_3_des">
      <?php 
          if(has_excerpt(get_the_ID())){
            if($number_excerpt=='full'){
              $excerpt = get_the_excerpt();
              ?><p><?php echo wp_kses_post($excerpt); ?></p><?php
            }else if($number_excerpt!='0'){
              $excerpt = wp_trim_words(get_the_excerpt(),$number_excerpt,'...');
              ?><p><?php echo wp_kses_post($excerpt); ?></p><?php
            }
          }?>
    </div>
    <?php 
    $id = get_the_ID();
    if (exwf_check_open_close_time($id) && exwoofood_get_option('exwoofood_booking') !='disable') { ?>
    <div class="fdlist_3_order <?php echo esc_attr($cls_ost); ?>">
      <?php echo '<div class="ex-hidden">'; exwoofood_booking_button_html(1); echo '</div>';?>
      <button class="exfd_modal_click exfd-choice" data="food_id=<?php echo get_the_ID(); ?>&food_qty=1"><div class="exfd-icon-plus"></div></button>
    </div>
    <?php }?>
</figure>