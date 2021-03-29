;(function($){
	'use strict';
	$(document).ready(function() {
		$("body").on("submit", ".exwoofood-woocommerce form.cart, .product form.cart", function(e){
			if(!$(this).find('.exrow-group.ex-required').length && !$(this).find('.exrow-group.ex-required-min').length ){ return;}
			var $validate = true;
			$('.ex-required-message, .ex-required-min-message, .ex-required-max-message').fadeOut();
			$(this).find('.exrow-group.ex-required:not(.ex-required-min)').each(function(){
				var $this_sl = $(this);
				if($this_sl.hasClass('exwf-offrq')){
				}else{
					if($this_sl.hasClass('ex-radio') || $this_sl.hasClass('ex-checkbox')){
						if(!$this_sl.find('.ex-options').is(":checked")){
							$this_sl.find('.ex-required-message').fadeIn();
							$this_sl.find('.exfood-label:not(.exwo-active)').trigger('click');
							$validate = false;
						}
					}else{
						if($this_sl.find('.ex-options').val() == ''){
							$this_sl.find('.exfood-label:not(.exwo-active)').trigger('click');
							$this_sl.find('.ex-required-message').fadeIn();
							$validate = false;
						}
					}
				}
			});
			$(this).find('.exrow-group.ex-checkbox.ex-required-min').each(function(){
				var $this_sl = $(this);
				if($this_sl.hasClass('exwf-offrq')){
				}else{
					var $minsl = $this_sl.data('minsl');
					var $nbsl = $this_sl.find('.ex-options:checked').length;
					if( $nbsl < $minsl ){
						$this_sl.find('.exfood-label:not(.exwo-active)').trigger('click');
						$this_sl.find('.ex-required-min-message').fadeIn();
						$validate = false;
					}
				}
			});	
			if($validate != true){
				e.preventDefault();
				e.stopPropagation();
				return;
			}
			return true;	
		});
		$('body').on('change', '.ex-checkbox.ex-required-max .ex-options', function() {
	    	var $this_sl = $(this);
	    	if($this_sl.hasClass('exwf-offrq')){
			}else{
		    	var $maxsl = $this_sl.closest(".ex-checkbox.ex-required-max").data('maxsl');
		    	var $nbsl = $this_sl.closest(".ex-checkbox.ex-required-max").find('.ex-options:checked').length;
		    	if( $nbsl > $maxsl ){
		    		$this_sl.closest(".ex-checkbox.ex-required-max").find('.ex-required-max-message').fadeIn();
			    	this.checked = false;
			    	event.preventDefault();
			    }
			}
	    });
		$("body").on("click",".exwo-accordion-style .exrow-group .exfood-label" ,function(e){
			var $this = $(this);
			$($this).next(".exwo-container").slideToggle(200);
			if($this.hasClass('exwo-active')){ 
				$this.removeClass('exwo-active');
			}else{
				$this.addClass('exwo-active');
			}
		});
    });
}(jQuery));