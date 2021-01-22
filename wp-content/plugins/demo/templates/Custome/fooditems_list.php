<?php

$get_all_items = get_product_list_by_category_new();

ob_start();

?>
<?php foreach ($get_all_items as $item){ ?>
<?php if(!empty($item['category_item'])){ ?>
<div id="menu-category-<?php echo $item['cat_id']; ?>" class="rpress-element-title" data-term-id="<?php echo $item['cat_id']; ?>">
    <div class="menu-category-wrap" data-cat-id="<?php echo $item['category_name']; ?>">
        <div class="menu-category-wrap" data-cat-id="<?php echo $item['category_name']; ?>">
            <h5 class="rpress-cat rpress-different-cat red"><?php echo $item['category_name']; ?></h5>
        </div>
    </div>
</div>
<?php } ?>
<?php foreach ($item['category_item'] as $category_item){ ?>
<?php 

$image_url = site_url().'/wp-content/plugins/restropress/assets/svg/plate.png';
if($category_item['photo'] != ''){
    $image_url = 'https://food.mammothecommerce.com/upload/'.$category_item['photo'];
}
?>
<div itemscope="" itemtype="http://schema.org/Product" class="rpress_fooditem" data-term-id="<?php echo $item['category_name']; ?>" id="rpress_fooditem_<?php echo $category_item['item_id']; ?>">
    <div class="row rpress_fooditem_inner">
        <div class="rp-col-md-9">
            <div class="rpress-thumbnail-holder rpress-bg">
                <img src="<?php echo $image_url; ?>" class="attachment-thumbnail size-thumbnail wp-post-image" alt="" style="height: 100%;border-radius:50%;">      
            </div>
            <div class="rpress-title-holder">
                <h3 itemprop="name" class="rpress_fooditem_title">
                    <a class="food-title" itemprop="url" data-search-term="<?php echo $category_item['item_name']; ?>"><?php echo $category_item['item_name']; ?></a>
                </h3>
                <div itemprop="description" class="rpress_fooditem_excerpt">
                    <p><?php echo substr($category_item['item_description'],0,100).'...'; ?></p>
                </div>
            </div>
        </div>
        <div class="rp-col-md-3">
            <div class="rpress-price-holder">
                <span class="price">
                    <span class="rpress_price rpress_price_range_low" id="rpress_price_low_<?php echo $category_item['item_id']; ?>">
                        <?php echo  get_price_detail($category_item['price']); ?>
                    </span>
                </span>

                <div class="rpress_fooditem_buy_button">
                    <form id="rpress_purchase_<?php echo $category_item['item_id']; ?>" class="rpress_fooditem_purchase_form rpress_purchase_<?php echo $category_item['item_id']; ?>" method="post">

                        <span itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
                            <meta itemprop="price" content="<?php echo current($category_item['price']); ?>">
                                <meta itemprop="priceCurrency" content="USD">
                                    </span>

                                    <div class="rpress_purchase_submit_wrapper">
                                        <a href="#" data-title="<?php echo $category_item['item_name']; ?>" class="rpress-add-to-cart-custome button red rpress-submit" style="background: none !important;" data-action="rpress_add_to_cart" data-fooditem-id="<?php echo $category_item['item_id']; ?>" data-variable-price="<?php echo count($category_item['price']) > 1 ? 'yes' : 'no'; ?>" data-price-mode="single" data-price="<?php echo current($category_item['price']); ?>"><span class="rpress-add-to-cart-label">ADD</span> </a>
                                        <span class="rpress-cart-ajax-alert" aria-live="assertive">
                                            <span class="rpress-cart-added-alert" style="display: none;">
                                                <svg class="rpress-icon rpress-icon-check" xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" aria-hidden="true">
                                                    <path d="M26.11 8.844c0 .39-.157.78-.44 1.062L12.234 23.344c-.28.28-.672.438-1.062.438s-.78-.156-1.06-.438l-7.782-7.78c-.28-.282-.438-.673-.438-1.063s.156-.78.438-1.06l2.125-2.126c.28-.28.672-.438 1.062-.438s.78.156 1.062.438l4.594 4.61L21.42 5.656c.282-.28.673-.438 1.063-.438s.78.155 1.062.437l2.125 2.125c.28.28.438.672.438 1.062z"></path>
                                                </svg>
                                                Added to cart						</span>
                                        </span>


                                    </div><!--end .rpress_purchase_submit_wrapper-->

                                    <input type="hidden" name="fooditem_id" value="<?php echo $category_item['item_id']; ?>">
                                        <input type="hidden" class="fooditem_qty" name="fooditem_qty" value="">
                                            <input type="hidden" name="rpress_action" class="rpress_action_input" value="add_to_cart">



                                                </form><!--end #rpress_purchase_291-->


                                                </div>

                                                </div>
                                                </div>


                                                </div>
                                                </div>
<?php  } ?>
<?php } ?>  
<?php
echo ob_get_clean();
