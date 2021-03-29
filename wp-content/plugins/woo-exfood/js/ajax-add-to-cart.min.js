;(function($){
	'use strict';
	function exfd_flytocart(imgtodrag){
		var cart = jQuery('.exfd-shopping-cart');
		if (cart.length == 0) {return;}
	    if (imgtodrag.length) {
	        var imgclone = imgtodrag.clone().offset({
	            top: imgtodrag.offset().top,
	            left: imgtodrag.offset().left
	        }).css({
	            'opacity': '0.5',
	                'position': 'absolute',
	                'height': '150px',
	                'width': '150px',
	                'z-index': '1001'
	        }).appendTo(jQuery('body'))
	            .animate({
	            'top': cart.offset().top + 10,
	                'left': cart.offset().left,
	                'width': 40,
	                'height': 40
	        }, 800);
	        imgclone.animate({
	            'width': 0,
	                'height': 0
	        }, function () {
	            jQuery(this).detach()
	        });
	    }
	}
	function exf_refresh_cart(){
		var data = {
			action: 'exwoofood_refresh_cart',
		};
		$('.exfd-cart-mini').css('opacity','.6');
		$.ajax({
			type: 'post',
			url: wc_add_to_cart_params.ajax_url,
			data: data,
			success: function(res){
				$('div.exfd-cart-mini').remove();
				$(res.fragments['div.exfd-cart-mini']).insertAfter('.exfd-cart-content .exfd-close-cart');
			}
		});
	}
	$(document).on('submit', '.exwoofood-woocommerce form', function (e) {
		$("#food_modal .exwoofood-woocommerce > div .exfd-out-notice").remove();
		var $button = $(this).find('.single_add_to_cart_button');
		var $form = $(this);
		var product_id = $form.find('input[name=add-to-cart]').val() || $button.val();
		var loc = $('.ex-fdlist > input[name=food_loc]').val();
		if (!product_id){ return;}
		if ($button.is('.disabled')){ return;}
		e.preventDefault();
		var data = {
			action: 'exwoofood_add_to_cart',
			'add-to-cart': product_id,
			'loc': loc,
		};
		$form.serializeArray().forEach(function (element) {
			data[element.name] = element.value;
		});
		$(document.body).trigger('adding_to_cart', [$button, data]);
		if($(this).find(".exrow-group.ex-checkbox").length){
			$(this).find(".exrow-group.ex-checkbox").each(function() {
				var $name = $(this).find('.ex-options').attr('name');
				var dt_cb =[];
				$(this).find('input[name="'+$name+'"]:checked').each(function() {
					dt_cb.push($(this).val());
				});
				data[$name] = dt_cb;
			});
		}
		if($(".wc-pao-addon-checkbox").length){
			$(".wc-pao-addon-checkbox").each(function() {
				var $name = $(this).attr('name');
				var dt_cb =[];
				$('input[name="'+$name+'"]:checked').each(function() {
				  dt_cb.push($(this).val());
				});
				data[$name] = dt_cb;
			});
		}
		if($(".ppom-check-input").length){
			$(".ppom-check-input").each(function() {
				if($(this).attr('type')=='checkbox'){
					var $name = $(this).attr('name');
					var dt_cb =[];
					$('input[name="'+$name+'"]:checked').each(function() {
					  dt_cb.push($(this).val());
					});
					data[$name] = dt_cb;
				}
			});
		}
		if($(".wccf_product_field_checkbox").length){
			$(".wccf_product_field_checkbox").each(function() {
				var $name = $(this).attr('name');
				var dt_cb =[];
				$('input[name="'+$name+'"]:checked').each(function() {
				  dt_cb.push($(this).val());
				});
				data[$name] = dt_cb;
			});
		}
		var old_dtcart = $('.exfd-cart-mini').html();
		$.ajax({
			type: 'post',
			url: wc_add_to_cart_params.ajax_url,
			data: data,
			beforeSend: function (response) {
				$button.removeClass('added').addClass('ex-loading');
			},
			complete: function (response) {
				$button.removeClass('ex-loading');
				var new_dtcart = $('.exfd-cart-mini');
				if(old_dtcart == new_dtcart){ return;}
				if (!response.error) {
					$button.addClass('added');
				}
			},
			success: function (response) {
				if (response.error) {
					$("#food_modal .exwoofood-woocommerce > div").append(response.message);
					return;
				} else {
					$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
					$('.woocommerce-notices-wrapper').empty().append(response.notices);
					if($('.ex-fdlist.ex-food-plug .exfd-choice').length){
						$('.ex-fdlist.ex-food-plug .exfd-choice').removeClass('ex-loading');
					}
					var new_dtcart = $('.exfd-cart-mini').html();
					if(old_dtcart == new_dtcart){ 
						$button.removeClass('added');
						if($("#food_modal .modal-content").data('close-popup') == 'yes'){
		                	$("#food_modal .ex_close").trigger('click');
		                }
						return;
					}
					
	                var imgtodrag;
	                var id_parent =$button.closest(".ex-fdlist ").attr('id');
	                var layout = $('#'+id_parent).hasClass('table-layout') ? 'table' : '';
	                imgtodrag = $button.closest("#food_modal").find("img").eq(0);
	                if (imgtodrag.length == 0) {
	                	if (layout!='table') {
		                	imgtodrag = $button.closest(".item-grid").find("img").eq(0);
		                	if (imgtodrag.length == 0) {
		                		imgtodrag = $button.closest(".item-grid").find(".ex-fly-cart").eq(0);
		                		if (imgtodrag.length == 0) {
		                			imgtodrag = $button.closest(".item-grid");
		                		}
		                	}
		                }else{
		                	imgtodrag = $button.closest("tr").find("img").eq(0);
		                	if (imgtodrag.length == 0) {
		                		imgtodrag = $button.closest("tr").find(".item-grid");
		                	}
		                }
	                }
	                if(imgtodrag.length == 0 && jQuery('.ex_modal.exfd-modal-active').length){
	                	imgtodrag = jQuery('.ex_modal.exfd-modal-active .exmd-no-img');
	                }
	                exfd_flytocart(imgtodrag);
	                exf_refresh_cart();
	                if($("#food_modal .modal-content").data('close-popup') == 'yes'){
	                	$("#food_modal .ex_close").trigger('click');
	                }
	                $(document).trigger('exwfqv_added_tocart');
				}
			},
		});
		return false;
	});
    
}(jQuery));