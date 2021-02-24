jQuery(document).ready(function($){
	// widget functionality
	$('body').on('change', 'select[name="style_variant"]', function(){
		$('#vc_layout_preview').html('<img src="'+$('#review_image_url').val()+'style'+$(this).val()+'.png" />');
	})
	setInterval(function(){
		console.log('11');
		$('#vc_layout_preview').html('<img src="'+$('#review_image_url').val()+'style'+$('select[name="style_variant"]').val()+'.png" class="style_preview" />');
	}, 1000)
});