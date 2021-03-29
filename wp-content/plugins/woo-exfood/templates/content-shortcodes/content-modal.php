<?php
global $atts,$id_food,$inline_bt;  
$customlink = EX_WPFood_customlink($id_food);
global $number_excerpt;

$custom_price = get_post_meta( $id_food, 'exwoofood_custom_price', true );
$price = exwoofood_price_with_currency($id_food);
if ($custom_price != '') {
	$price = $custom_price;
}

$protein = get_post_meta( $id_food, 'exwoofood_protein', true );
$calo = get_post_meta( $id_food, 'exwoofood_calo', true );
$choles = get_post_meta( $id_food, 'exwoofood_choles', true );
$fibel = get_post_meta( $id_food, 'exwoofood_fibel', true );
$sodium = get_post_meta( $id_food, 'exwoofood_sodium', true );
$carbo = get_post_meta( $id_food, 'exwoofood_carbo', true );
$fat = get_post_meta( $id_food, 'exwoofood_fat', true );
$gallery = get_post_meta( $id_food, '_product_image_gallery', true );

$custom_data = get_post_meta( $id_food, 'exwoofood_custom_data_gr', true );
$exwoofood_enable_rtl = exwoofood_get_option('exwoofood_enable_rtl');
$rtl_modal_mode = ($exwoofood_enable_rtl == 'yes') ? 'yes' : 'no';
if(class_exists('WPBMap') && method_exists('WPBMap', 'addAllMappedShortcodes')) { 
	WPBMap::addAllMappedShortcodes();
}
$content = apply_filters('the_content', get_post_field('post_content', $id_food));
$cls_sli = '';
if ($gallery == '') {
	$cls_sli = 'ex_s_lick-initialized exwp-no-galle';
}
$close_popup = exwoofood_get_option('exwoofood_clsose_pop');
?>
<!-- The Modal -->
<div class="modal-content <?php echo $gallery =='' && !has_post_thumbnail($id_food) ? ' exmd-no-img' : ''?>" <?php echo class_exists( 'WPCleverWoosb' ) ? 'id="woosq-popup"' : ''; ?> data-close-popup="<?php echo esc_attr($close_popup); ?>">
	<div class="ex-modal-big" id="product-<?php echo esc_attr($id_food); ?>">
	    <span class="ex_close">&times;</span>
	    <div class="fd_modal_img">
	    	<div class="exfd-modal-carousel <?php echo esc_attr($cls_sli);?>" rtl_mode="<?php echo esc_attr($rtl_modal_mode); ?>">
				<div><?php echo get_the_post_thumbnail($id_food,'full'); ?></div>
				<?php 
				if ($gallery != '') {
					$gallery = explode(",",$gallery);
					foreach ($gallery as $item ) {
						$item = wp_get_attachment_image_url($item,'full');
						echo '<div><img src="'.$item.'" alt="'.esc_attr(get_the_title( $id_food )).'"/></div>';
					}
				}
				?>
			</div>
			<?php exwf_icon_color($id_food); ?>
	    </div>
	    <div class="fd_modal_des">
			<h3><?php echo get_the_title( $id_food ); ?></h3>
		    <div class="exfd_nutrition">
		    	<ul>
	    			<?php if($protein!=''){ ?>
		    			<li>
		    				<span><?php esc_html_e('Protein','woocommerce-food'); ?></span><?php echo wp_kses_post($protein);?>
		    			</li>
		    		<?php }if($calo!=''){ ?>
	    				<li><span><?php esc_html_e('Calories','woocommerce-food'); ?></span><?php echo wp_kses_post($calo);?></li>
	    			<?php }if($choles!=''){ ?>
	    				<li><span><?php esc_html_e('Cholesterol','woocommerce-food'); ?></span><?php echo wp_kses_post($choles);?></li>
	    			<?php }if($fibel!=''){ ?>
	    				<li><span><?php esc_html_e('Dietary fibre','woocommerce-food'); ?></span><?php echo wp_kses_post($fibel);?></li>
	    			<?php }if($sodium!=''){ ?>
	    				<li><span><?php esc_html_e('Sodium','woocommerce-food'); ?></span><?php echo wp_kses_post($sodium);?></li>
	    			<?php }if($carbo!=''){ ?>
	    				<li><span><?php esc_html_e('Carbohydrates','woocommerce-food'); ?></span><?php echo wp_kses_post($carbo);?></li>
	    			<?php }if($fat!=''){ ?>
	    				<li><span><?php esc_html_e('Fat total','woocommerce-food'); ?></span><?php echo wp_kses_post($fat);?></li>
	    			<?php }
	    			if ($custom_data != '') {
	    				foreach ($custom_data as $data_it) {?>
			    			<li><span><?php echo wp_kses_post($data_it['_name']); ?></span><?php echo wp_kses_post($data_it['_value']);?></li>
			    			<?php
	    				}
	    			}
	    			?>
	    			<div class="exfd_clearfix"></div>
			    </ul>
			    <?php echo exfd_show_reviews($id_food); ?>
		    </div>
		    <h5>
				<?php echo wp_kses_post($price);?>
		    </h5>
		    <?php
		    if($content!=''){?>
			    <div class="exwoofood-ct"><?php echo wp_kses_post($content);?></div>
			<?php }
			$inline_bt ='yes';
			echo exwoofood_add_to_cart_form_shortcode( $atts );?>
	    </div>
	</div>
</div>