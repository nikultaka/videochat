<div class="exfd-shopping-cart">
    <div class="exfd-cart-parent">
            <a href="javascript:;">
            <img src="<?php echo EX_WOOFOOD_PATH.'css/img/exfdcart2.svg';?>" alt="image-cart">
            <span class="exfd-cart-num"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
        </a>
    </div>
</div>
<div class="exfd-overlay"></div>
<div class="exfd-cart-content">
    <span class="exfd-close-cart">&times;</span>
    <?php echo '<div class="exfd-cart-mini">';woocommerce_mini_cart();echo '</div>';?>
    <?php do_action('exwf_sidecart_after_content');?>
</div>