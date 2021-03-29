<?php
function exwoofood_custom_css(){
    ob_start();
    $exwoofood_color = exwoofood_get_option('exwoofood_color');

    $hex  = str_replace("#", "", $exwoofood_color);
    if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
    } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
    }
    $rgb = $r.','. $g.','.$b;
    if($exwoofood_color!=''){
    	?>

        .ex-fdlist .exstyle-1 figcaption .exstyle-1-button,
        .ex-fdlist[id^=ex] .exwoofood-woocommerce.woocommerce form.cart button[type="submit"],
        .exwoofood-woocommerce.woocommerce form.cart button[type="submit"],
        .exwoofood-woocommerce.woocommerce .cart:not(.grouped_form) .quantity input[type=button],
        .ex-fdlist .exstyle-2 figcaption .exstyle-2-button,
        .ex-fdlist .exstyle-3 figcaption .exstyle-3-button,
        .ex-fdlist .exstyle-4 figcaption h5,
        .ex-fdlist .exstyle-4 .exfd-icon-plus:before,
        .ex-fdlist .exstyle-4 .exfd-icon-plus:after,
        .exfd-table-1 .ex-fd-table-order .exfd-icon-plus:before,
        .exfd-table-1 .ex-fd-table-order .exfd-icon-plus:after,
        .ex-loadmore .loadmore-exfood:hover,
        .exfd-cart-content .exfd-close-cart,
        .exfd-cart-content .woocommerce-mini-cart__buttons a,
        .ex-fdlist .exstyle-4 figcaption .exbt-inline .exstyle-4-button,
        .ex_close,
        .exwf-lbicon,
        .ex-fdlist.category_left .exfd-filter .ex-menu-list .ex-active-left:after{background:<?php echo esc_attr($exwoofood_color);?>;}
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list a ul li:hover,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list .ex-menu-item-active{
            background:<?php echo esc_attr($exwoofood_color);?>;
            border-color:<?php echo esc_attr($exwoofood_color);?>;
        }
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list .ex-menu-item-active:not(.exfd-child-click):after,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list .ex-menu-item-active:after,
        .ex-fdlist .exstyle-4 figcaption{
            border-top-color: <?php echo esc_attr($exwoofood_color);?>;
        }
        .fdstyle-list-1 .fdlist_1_des button,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_price button,
        .fdstyle-list-3 .fdlist_3_order button,
        .ex-fdlist .exstyle-4 figcaption .exstyle-4-button.exfd-choice,
        .exwf-method-ct .exwf-method-title .at-method,
        .exwf-search .exwf-s-field,
        .exfd-cart-mini .exwf-quantity .exwf-con-quantity,
        .exfd-table-1 .ex-fd-table-order button{
            border-color: <?php echo esc_attr($exwoofood_color);?>;
        }
        .ex-fdlist.style-4 .item-grid{
            border-bottom-color: <?php echo esc_attr($exwoofood_color);?>;
        }
        .exwoofood-mulit-steps >div.active:after {
            border-left-color: <?php echo esc_attr($exwoofood_color);?>;
        }
        .ex-fdlist .exstyle-1 figcaption h5,
        .ex-fdlist .exstyle-2 figcaption h5,
        .ex-fdlist .exstyle-3 figcaption h5,
        .exfd-table-1 td.ex-fd-name h3 a,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_price,
        .ex-fdlist .ex-popup-location .ex-popup-content .ex-popup-info h1,
        .ex-fdlist.category_left .exfd-filter .ex-menu-list a:hover,
        .ex-fdlist.category_left .exfd-filter .ex-menu-list .ex-active-left,
        .ex-fdlist.ex-fdcarousel .ex_s_lick-dots li.ex_s_lick-active button:before,
        .exfd-admin-review > span > i.icon,
        .ex_modal .modal-content .ex_s_lick-dots li button:before,
        .ex-fdlist .exfd-filter.exwf-fticon-style .exfd-filter-group .ex-menu-list .ex-menu-item-active:not(li),
        .exwf-method-ct .exwf-method-title .at-method,
        .ex-fdlist.ex-fdcarousel .ex_s_lick-dots li button:before{
            color: <?php echo esc_attr($exwoofood_color);?>;
        }
        .exwf-cksp-method.exwf-method-ct .exwf-method-title .at-method,
        .exfd-pagination .page-navi .page-numbers.current {
            background-color: <?php echo esc_attr($exwoofood_color);?>;
            border-color: <?php echo esc_attr($exwoofood_color);?>;
        }
        .ex-loadmore .loadmore-exfood{
            border-color: <?php echo esc_attr($exwoofood_color);?>;
            color: <?php echo esc_attr($exwoofood_color);?>;
        }
        .exwf-button,
        .ex-loadmore .loadmore-exfood span:not(.load-text),
        .ex-fdlist .exfd-shopping-cart,
        .fdstyle-list-1 .exfd-icon-plus:before,
        .fdstyle-list-1 .exfd-icon-plus:after,
        .fdstyle-list-2 .exfd-icon-plus:before,
        .fdstyle-list-3 .exfd-icon-plus:before,
        .fdstyle-list-2 .exfd-icon-plus:after,
        .fdstyle-list-3 .exfd-icon-plus:after,
        .exfd-cart-mini .exwf-quantity .exwf-con-quantity > input,
        .exfd-table-1 th{
            background-color: <?php echo esc_attr($exwoofood_color);?>;
        }
        @media screen and (max-width: 768px){

        }
        @media screen and (max-width: 992px) and (min-width: 769px){

        }
        <?php
    }
    $exwoofood_font_family = exwoofood_get_option('exwoofood_font_family');
    $main_font_family = explode(":", $exwoofood_font_family);
    $main_font_family = $main_font_family[0];
    if($exwoofood_font_family!=''){?>
        .ex-fdlist{font-family: "<?php echo esc_html($main_font_family);?>", sans-serif;}
        <?php
    }
    $exwoofood_font_size = exwoofood_get_option('exwoofood_font_size');
    if($exwoofood_font_size!=''){?>
        .ex-fdlist{font-size: <?php echo esc_html($exwoofood_font_size);?>;}
        <?php
    }
    $exwoofood_ctcolor = exwoofood_get_option('exwoofood_ctcolor');
    if($exwoofood_ctcolor!=''){?>
    	.ex-fdlist,
        .exfd-table-1 td{color: <?php echo esc_html($exwoofood_ctcolor);?>;}
        <?php
    }

    $exwoofood_headingfont_family = exwoofood_get_option('exwoofood_headingfont_family');
    $h_font_family = explode(":", $exwoofood_headingfont_family);
    $h_font_family = $h_font_family[0];
    if($h_font_family!=''){?>
    	.ex-fdlist .exstyle-1 h3 a,
        .ex-fdlist .exstyle-2 h3 a,
        .ex-fdlist .exstyle-3 h3 a,
        .ex-fdlist .exstyle-4 h3 a,
        .ex-popup-location .ex-popup-content .ex-popup-info h1,
        .exfd-table-1 td.ex-fd-name h3 a,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_name,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_name,
        .fdstyle-list-3 .fdlist_3_title h3,
        .ex_modal .modal-content .fd_modal_des h3,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list a,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-select,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-select select{
            font-family: "<?php echo esc_html($h_font_family);?>", sans-serif;
        }
    	<?php 
    }
    $exwoofood_headingfont_size = exwoofood_get_option('exwoofood_headingfont_size');
    if($exwoofood_headingfont_size!=''){?>
    	.ex-fdlist .exstyle-1 h3 a,
        .ex-fdlist .exstyle-2 h3 a,
        .ex-fdlist .exstyle-3 h3 a,
        .ex-fdlist .exstyle-4 h3 a,
        .exwoofood-thankyou h3,
        .ex-popup-location .ex-popup-content .ex-popup-info h1,
        .exfd-table-1 td.ex-fd-name h3 a,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_name,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_name,
        .fdstyle-list-3 .fdlist_3_title h3,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list a,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-select select{font-size: <?php echo esc_html($exwoofood_headingfont_size);?>;}
        <?php
    }
    $exwoofood_hdcolor = exwoofood_get_option('exwoofood_hdcolor');
    if($exwoofood_hdcolor!=''){?>
    	.ex-fdlist .exstyle-1 h3 a,
        .ex-fdlist .exstyle-2 h3 a,
        .ex-fdlist .exstyle-4 h3 a,
        .ex-popup-location .ex-popup-content .ex-popup-info h1,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-list a,
        .ex_modal .modal-content .fd_modal_des h3,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_name,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_name,
        .fdstyle-list-3 .fdlist_3_title h3,
        .exfd-table-1 td.ex-fd-name h3 a,
        .ex-fdlist .exfd-filter .exfd-filter-group .ex-menu-select select{color: <?php echo esc_html($exwoofood_hdcolor);?>;}
        <?php
    }
    // price font
    $exwoofood_pricefont_family = exwoofood_get_option('exwoofood_pricefont_family');
    $price_font_family = explode(":", $exwoofood_pricefont_family);
    $price_font_family = $price_font_family[0];
    if($price_font_family!=''){?>
        .ex-fdlist .exstyle-1 figcaption h5,
        .ex-fdlist .exstyle-2 figcaption h5,
        .ex-fdlist .exstyle-3 figcaption h5,
        .ex-fdlist .exstyle-4 figcaption h5,
        .exfd-table-1 td .exfd-price-detail,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_price,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_price,
        .ex_modal .modal-content .fd_modal_des h5{
            font-family: "<?php echo esc_html($price_font_family);?>", sans-serif;
        }
        <?php 
    }
    $exwoofood_pricefont_size = exwoofood_get_option('exwoofood_pricefont_size');
    if($exwoofood_pricefont_size!=''){?>
        .ex-fdlist .exstyle-1 figcaption h5,
        .ex-fdlist .exstyle-2 figcaption h5,
        .ex-fdlist .exstyle-3 figcaption h5,
        .ex-fdlist .exstyle-4 figcaption h5,
        .exfd-table-1 td .exfd-price-detail,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_price,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_price,
        .ex_modal .modal-content .fd_modal_des h5{font-size: <?php echo esc_html($exwoofood_pricefont_size);?>;}
        <?php
    }
    $exwoofood_pricecolor = exwoofood_get_option('exwoofood_pricecolor');
    if($exwoofood_pricecolor!=''){?>
        .ex-fdlist .exstyle-1 figcaption h5,
        .ex-fdlist .exstyle-2 figcaption h5,
        .ex-fdlist .exstyle-3 figcaption h5,
        .ex-fdlist .exstyle-4 figcaption h5,
        .exfd-table-1 td .exfd-price-detail,
        .fdstyle-list-1 .fdlist_1_title .fdlist_1_price,
        .fdstyle-list-2 .fdlist_2_title .fdlist_2_price,
        .ex_modal .modal-content .fd_modal_des h5{color: <?php echo esc_html($exwoofood_pricecolor);?>;}
        <?php
    }
    // end price font


    $exwoofood_metafont_family = exwoofood_get_option('exwoofood_metafont_family');
    $m_font_family = explode(":", $exwoofood_metafont_family);
    $m_font_family = $m_font_family[0];
    if($m_font_family!=''){?>
    	.ex_modal .modal-content .fd_modal_des .exfd_nutrition li{
            font-family: "<?php echo esc_html($m_font_family);?>", sans-serif;
        }
    	<?php 
    }
    $exwoofood_metafont_size = exwoofood_get_option('exwoofood_metafont_size');
    if($exwoofood_metafont_size!=''){?>
    	.ex_modal .modal-content .fd_modal_des .exfd_nutrition li{font-size: <?php echo esc_html($exwoofood_metafont_size);?>;}
        <?php
    }
    $exwoofood_mtcolor = exwoofood_get_option('exwoofood_mtcolor');
    if($exwoofood_mtcolor!=''){?>
    	.ex_modal .modal-content .fd_modal_des .exfd_nutrition li{color: <?php echo esc_html($exwoofood_mtcolor);?>;}
        <?php
    }

    $exwoofood_custom_css = exwoofood_get_option('exwoofood_custom_css','exwoofood_custom_code_options');
    if($exwoofood_custom_css!=''){
    	echo ($exwoofood_custom_css);
    }
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}