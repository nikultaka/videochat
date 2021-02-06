jQuery(function () {

    jQuery('input[name="kunaki_product_check"]', '.inline-edit-row').click(function () {
        if (jQuery(this).prop("checked") == true) {
            jQuery('input[name="kunaki_product"]', '.inline-edit-row').val('yes');
        }

        if (jQuery(this).prop("checked") == false) {
            jQuery('input[name="kunaki_product"]', '.inline-edit-row').val('no');
        }

    });

    jQuery('#the-list').on('click', '.editinline', function () {
        //inlineEditPost.revert();

        let post_id = jQuery(this).closest('tr').attr('id');
        post_id = post_id.replace("post-", "");

        let inline_data = jQuery('#custom_field_demo_inline_' + post_id);
        let woocommerce_inline_data = jQuery('#woocommerce_inline_' + post_id);
        let check = inline_data.find("#kunaki_product").text();
        jQuery('input[name="kunaki_product"]', '.inline-edit-row').val(check);
        if (check == 'yes') {
            jQuery('input[name="kunaki_product_check"]', '.inline-edit-row').prop("checked", true);
        } else {
            jQuery('input[name="kunaki_product_check"]', '.inline-edit-row').prop("checked", false);
        }

        let product_type = woocommerce_inline_data.find('.product_type').text();

        if (product_type == 'simple' || product_type == 'external') {
            jQuery('.custom_field_demo', '.inline-edit-row').show();
        } else {
            jQuery('.custom_field_demo', '.inline-edit-row').hide();
        }

    });

    jQuery('body').on('click', '#kunaki-check-license', function () {
        let buttonObject = jQuery(this);

        if (buttonObject.hasClass('kunaki-inp__button-preload')) {
            return;
        }

        let data = {
            action: 'kunaki_check_license',
            data: 'key=' + jQuery('#woo_kunaki_premium_license_key').val(),
        };

        buttonObject.attr('class', 'kunaki-inp__button');
        buttonObject.addClass('kunaki-inp__button-preload');

        console.log(data);
        jQuery.post(ajaxurl, data, function (resp) {
            buttonObject.removeClass('kunaki-inp__button-preload');
            if (resp.success) {
                buttonObject.addClass('kunaki-inp__button-cussess');
            } else {
                buttonObject.addClass('kunaki-inp__button-error');
            }
        });
    });
});