jQuery(document).ready(function () {
    let $ = jQuery;

    //for Price tab
    $('.product_data_tabs .general_tab').addClass('show_if_tradeline').show();
    $('.product_data_tabs .wpas-schedule-sale-tab_tab').addClass('show_if_tradeline').show();
    $('#general_product_data .pricing').addClass('show_if_tradeline').show();
    $('.shipping_options').addClass('hide_if_tradeline');

    if ($('#product-type').val() === 'tradeline') {
        $('.shipping_options').hide();
    }

    //for Inventory tab
    $('.inventory_options').addClass('show_if_tradeline').show();
    $('#inventory_product_data ._manage_stock_field').addClass('show_if_tradeline').show();
    $('#inventory_product_data ._sold_individually_field').parent().addClass('show_if_tradeline').show();
    $('#inventory_product_data ._sold_individually_field').addClass('show_if_tradeline').show();

    display_post_image();
    $('#product-type').change(function () {
        display_post_image();
    });

    if (wp.media) {
        const wp_media_post_id = wp.media.model.settings.post.id;
        $('body').on('click', '.woo-tradeline-thumb-loader, .woo-tradeline-thumb-wrapper img', function (event) {
            let that = $(this),
                set_to_post_id = that.data('post_id'),
                file_frame;

            event.preventDefault();

            if (file_frame) {
                file_frame.uploader.uploader.param('post_id', set_to_post_id);
                file_frame.open();
                return;
            } else {
                wp.media.model.settings.post.id = set_to_post_id;
            }

            file_frame = wp.media.frames.file_frame = wp.media({
                title: 'Select a image to upload',
                button: {
                    text: 'Use this image',
                },
                multiple: false
            });

            file_frame.on('select', function () {
                attachment = file_frame.state().get('selection').first().toJSON();

                that.before('<img src="' + attachment.url + '" style="max-width: 100%" >');
                that.parent().find('.woo-tradeline-thumb-id').first().val(attachment.id);
                that.hide();
                wp.media.model.settings.post.id = wp_media_post_id;
            });

            file_frame.open();
        });

        $('a.add_media').on('click', function () {
            wp.media.model.settings.post.id = wp_media_post_id;
        });
    }

    $('body').on('click', '.woo-tradeline-nav-tabs .nav-tab', function () {
        if ($(this).hasClass('nav-tab-active')) {
            return;
        }

        $('.nav-tab-active').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        $('.woo-tradeline-nav-tab').each(function () {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
            }
        });

        $('#' + $(this).data('target')).addClass('active');
    });

    function display_post_image() {
        if ($('#product-type').val() === 'tradeline') {
            if ($('.wootrls-activation-warning').length === 0) {
                $('#postimagedivstyle1').show();
                $('#postimagedivstyle2').show();
                $('#postimagedivstyle3').show();
            }

            $('#_virtual').parent().show();
            $('#_virtual').prop('checked', true);
        } else {
            $('#postimagedivstyle1').hide();
            $('#postimagedivstyle2').hide();
            $('#postimagedivstyle3').hide();
        }
    }

    $('body').on('change', '#_virtual', function () {
        if ($('#product-type').val()) {
            $(this).parent().show();
        }
    });

    $('body').on('click', '#wootrls_license button', function () {
        let buttonObject = $(this);

        if (buttonObject.hasClass('preload')) {
            return;
        }

        let data = {
            action: 'wootrls_check_license',
            data: 'key=' + $('#license_inp').val(),
        };

        buttonObject.attr('class', '');
        buttonObject.addClass('preload');

        $.post(ajaxurl, data, function (resp) {
            buttonObject.removeClass('preload');
            if (resp.success) {
                buttonObject.addClass('success');
            } else {
                buttonObject.addClass('error');
            }
        });
    });
});