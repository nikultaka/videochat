;(function($){
	'use strict';
	$(document).ready(function() {
		function exwo_add_title($box){
			if(!$box.length){ return;}
			$box.find( '.cmb-group-title' ).each( function() {
				var $this = $( this );
				var txt = $this.next().find( '[id$="_name"]' ).val();
				var rowindex;
				if ( ! txt ) {
					txt = $box.find( '[data-grouptitle]' ).data( 'grouptitle' );
					if ( txt ) {
						rowindex = $this.parents( '[data-iterator]' ).data( 'iterator' );
						txt = txt.replace( '{#}', ( rowindex + 1 ) );
					}
				}
				if ( txt ) {
					$this.text( txt );
				}
			});
		}
		function exwo_replace_title(evt){
			var $this = $( evt.target );
			var id = 'name';
			if ( evt.target.id.indexOf(id, evt.target.id.length - id.length) !== -1 ) {
				$this.parents( '.cmb-row.cmb-repeatable-grouping' ).find( '.cmb-group-title' ).text( $this.val() );
			}
		}
		jQuery('#exwo_addition_options').on( 'cmb2_add_row cmb2_shift_rows_complete', exwo_add_title )
				.on( 'keyup', exwo_replace_title );
		exwo_add_title(jQuery('#exwo_addition_options'));
		jQuery('body').on('click', function() {
			exwo_add_title(jQuery('#exwo_addition_options'));
		});

		// show hide option by type of option
		if(jQuery('#cmb2-metabox-exwo_addition_options.cmb2-metabox .extype-option select').length>0){
			jQuery('#cmb2-metabox-exwo_addition_options.cmb2-metabox .extype-option select').each(function(){
				var $this = $(this);
				var $val = $this.val();
				if($val!=''){
					$this.closest('.postbox.cmb-repeatable-grouping').addClass('ex-otype-'+$val);
				}
			});
			jQuery('body').on('change', '#cmb2-metabox-exwo_addition_options.cmb2-metabox .extype-option select', function() {
				var $this = $(this);
				var $val = $this.val();
				$this.closest(".postbox").find('.exwo-options input[type="checkbox"]').prop('checked', false);
				$this.closest('.postbox.cmb-repeatable-grouping').removeClass (function (index, className) {
					return (className.match (/(^|\s)ex-otype\S+/g) || []).join(' ');
				});
				if($val!=''){
					$this.closest('.postbox.cmb-repeatable-grouping').addClass('ex-otype-'+$val);
				}
			});
		}
		// set default value
		$('body').on('change', '.cmb-type-price-options .exwo-options input[type="checkbox"]', function() {
	    	var $this_sl = $(this);
	    	var $nbsl = $this_sl.closest(".cmb-type-price-options").find('.exwo-options.exwo-def-option input[type="checkbox"]:checked').length;
	    	if( ($this_sl.closest(".postbox.ex-otype-radio").length || $this_sl.closest(".postbox.ex-otype-select").length) &&  $nbsl > 1 ){
	    		$this_sl.closest(".cmb-type-price-options").find('.exwo-options.exwo-def-option input[type="checkbox"]').prop('checked', false);
		    	this.checked = true;
		    	event.preventDefault();
		    }
	    });
	    // change settings tab
	    $('body').on('click', '.exwo-gr-option a:not(.exwo-copypre)', function() {
	    	var $this_sl = $(this);
	    	$this_sl.closest('.cmb-field-list').find('.exwo-gr-option a').removeClass('current');
	    	$this_sl.addClass('current');
	    	var _remove = $this_sl.attr('data-remove');
	    	var _add = $this_sl.attr('data-add');
	    	$this_sl.closest('.cmb-field-list').find(_remove).fadeOut();
	    	$this_sl.closest('.cmb-field-list').find(_add).fadeIn();
	    });
	    jQuery( "body" ).on( "change", ".exwo-options.exwo-val-option select", function () {
			jQuery(this).next().val( (jQuery(this).val()) );
		});
		//copy option
		jQuery('#exwo_addition_options').on('click', '.exwo-copypre',function() {
	    	var $crr_info = $(this).closest('.cmb-repeatable-grouping');
	    	var $pre_info = $crr_info.prev();
    		$crr_info.find('.exwo-op-name .cmb-td input').val($pre_info.find('.exwo-op-name .cmb-td input').val());
    		$crr_info.find('.exwo-op-type .cmb-td select').val($pre_info.find('.exwo-op-type .cmb-td select').val()).trigger('change');
    		$crr_info.find('.exwo-op-rq .cmb-td select').val($pre_info.find('.exwo-op-rq .cmb-td select').val());
    		$crr_info.find('.exwo-op-min .cmb-td input').val($pre_info.find('.exwo-op-min .cmb-td input').val());
    		$crr_info.find('.exwo-op-max .cmb-td input').val($pre_info.find('.exwo-op-max .cmb-td input').val());
    		var _name = $crr_info.find('.exwo-op-name .cmb-td input').attr('name');
			var _res =  _name.split("][");
			var _iterator = _res[0].match(/\d+/);
			//console.log(_iterator);
    		var _mt_op = $pre_info.find('.exwo-op-ops .cmb-td .cmb-field-list').html();
    		//console.log(_mt_op);
    		$crr_info.find('.exwo-op-ops .cmb-td .cmb-field-list').html(_mt_op);
    		$crr_info.find( '.exwo-op-ops .cmb-td .cmb-field-list input' ).each( function(){
				var $_name = $(this).attr('name');
				var res =  $_name.split("][");
				$_name = $_name.replace( res[0], 'exwo_options['+(_iterator) );
				$(this).attr('name',$_name);
			});

    		$crr_info.find('.exwo-op-tpr .cmb-td select').val($pre_info.find('.exwo-op-tpr .cmb-td select').val());
    		$crr_info.find('.exwo-op-pri .cmb-td input').val($pre_info.find('.exwo-op-pri .cmb-td input').val());
	    });
    });
}(jQuery));