;(function($){
	'use strict';
	$(document).ready(function() {
		$("#toplevel_page_exwoofood_options").appendTo("#menu-posts-product > ul");
		if($("#toplevel_page_exwoofood_options > a").hasClass('current') || $("#toplevel_page_exwoofood_advanced_options > a").hasClass('current') || $("#toplevel_page_exwoofood_shpping_options > a").hasClass('current') || $("#toplevel_page_exwoofood_custom_code_options > a").hasClass('current') || $("#toplevel_page_exwoofood_js_css_file_options > a").hasClass('current') ){
			$("#adminmenu > li#menu-posts-product").addClass('wp-has-current-submenu');
			$("#toplevel_page_exwoofood_options").addClass('current');
		}
		/*-ready-*/
		if(jQuery('.post-type-exwoofood_scbd .cmb2-metabox select[name="sc_type"]').length>0){
			var $val = jQuery('.post-type-exwoofood_scbd .cmb2-metabox select[name="sc_type"]').val();
			if($val==''){
				$val ='grid';
			}
			if($val =='list'){
				jQuery('.post-type-exwoofood_scbd select#style option').attr('disabled','disabled');
				jQuery('.post-type-exwoofood_scbd select#style option[value="1"], .post-type-exwoofood_scbd select#style option[value="2"], .post-type-exwoofood_scbd select#style option[value="3"]').removeAttr("disabled");
			}else if($val =='table'){
				jQuery('.post-type-exwoofood_scbd select#style option').attr('disabled','disabled');
				jQuery('.post-type-exwoofood_scbd select#style option[value="1"]').removeAttr("disabled");
			}else{
				jQuery('.post-type-exwoofood_scbd select#style option').removeAttr('disabled','disabled');
			}
			$('body').removeClass (function (index, className) {
				return (className.match (/(^|\s)ex-layout\S+/g) || []).join(' ');
			});
			$('body').addClass('ex-layout-'+$val);
		}
		/*-on change-*/
		jQuery('.post-type-exwoofood_scbd .cmb2-metabox select[name="sc_type"]').on('change',function() {
			var $this = $(this);
			var $val = $this.val();
			if($val==''){
				$val ='grid';
			}
			if($val =='list'){
				jQuery('.post-type-exwoofood_scbd select#style option').attr('disabled','disabled');
				jQuery('.post-type-exwoofood_scbd select#style option[value="1"], .post-type-exwoofood_scbd select#style option[value="2"], .post-type-exwoofood_scbd select#style option[value="3"]').removeAttr("disabled");
			}else if($val =='table'){
				jQuery('.post-type-exwoofood_scbd select#style option').attr('disabled','disabled');
				jQuery('.post-type-exwoofood_scbd select#style option[value="1"]').removeAttr("disabled");
			}else{
				jQuery('.post-type-exwoofood_scbd select#style option').removeAttr('disabled','disabled');
			}
			$('body').removeClass (function (index, className) {
				return (className.match (/(^|\s)ex-layout\S+/g) || []).join(' ');
			});
			$('body').addClass('ex-layout-'+$val);
			
		});
		/*-ajax save meta-*/
		jQuery('input[name="exwoofood_order"]').on('change',function() {
			var $this = $(this);
			var post_id = $this.attr('data-id');
			var valu = $this.val();
           	var param = {
	   			action: 'exwoofood_change_sort_food',
	   			post_id: post_id,
				value: valu
	   		};
	   		$.ajax({
	   			type: "post",
	   			url: exwoofood_ajax.ajaxurl,
	   			dataType: 'html',
	   			data: (param),
	   			success: function(data){
	   				return true;
	   			}	
	   		});
		});
		

		function ex_add_title($box){
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
		function ex_replace_title(evt){
			var $this = $( evt.target );
			var id = 'name';
			if ( evt.target.id.indexOf(id, evt.target.id.length - id.length) !== -1 ) {
				$this.parents( '.cmb-row.cmb-repeatable-grouping' ).find( '.cmb-group-title' ).text( $this.val() );
			}
		}
		jQuery('#exwoofood_addition_options,#exwoofood_custom_data').on( 'cmb2_add_row cmb2_shift_rows_complete', ex_add_title )
				.on( 'keyup', ex_replace_title );
		ex_add_title(jQuery('#exwoofood_addition_options,#exwoofood_custom_data'));

		jQuery('.cmb2-id-exorder-store input[name="exorder_store"]').on('change paste keyup',function(e) {
			e.preventDefault();
			var $this = $(this);
			var store_id = $this.val();
			var param = {
	   			action: 'exwoofood_admin_show_store',
	   			store_id: store_id,
	   		};
	   		$.ajax({
	   			type: "post",
	   			url: exwoofood_ajax.ajaxurl,
	   			dataType: 'json',
	   			data: (param),
	   			success: function(data){
	   				if(data!=0){
		   				$('.cmb2-id-exorder-store .cmb2-metabox-description').empty();
		   				$('.cmb2-id-exorder-store .cmb2-metabox-description').append(data.store_name);
		   			}
	   			}	
	   		});
		});

		// change sort menu
		jQuery('input[name="exfd_sort_menu"]').on('change',function() {
			var $this = $(this);
			var post_id = $this.attr('data-id');
			var value = $this.val();
           	var param = {
	   			action: 'exfd_change_order_menu',
	   			post_id: post_id,
				value: value
	   		};
	   		$.ajax({
	   			type: "post",
	   			url: exwoofood_ajax.ajaxurl,
	   			dataType: 'html',
	   			data: (param),
	   			success: function(data){
	   				return true;
	   			}	
	   		});
		});

	});
}(jQuery));