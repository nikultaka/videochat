<?php
// adding VC widget
add_action( 'vc_before_init', 'wab_before_init_actions' );
function wab_before_init_actions() {
 
	if( class_exists ( 'WPBakeryShortCode') ){
	 // Element Class 
		class single_wtr_element extends WPBakeryShortCode {
			 
			private $item_data;
			// Element Init
			function __construct() {
				//prepare  cat selector
				$out_cats = array();
				$cats = get_terms( 'product_cat' );
				
				$out_cats['Select Category'] =  '';
				foreach( $cats as $single_cat ){
					$out_cats[$single_cat->name] =  $single_cat->term_id;
				}
			 
				
				// Stop all if VC is not enabled
			 
				if ( !defined( 'WPB_VC_VERSION' ) ) {
						return;
				}
					 
				// Map the block with vc_map()
				vc_map( 
			  
					array(
						'name' => __('WooTradelines', 'wab'),
						'base' => 'single_wtr_element',
						'description' => __('WooTradelines', 'wab'), 
						'category' => __('WooTradelines', 'wab'),   
						'icon' => plugins_url('/vc/element-icon-text-block.svg', __FILE__ ), 
				 				 
						'params' => array(
							/*
							 array(
								'type' => 'textfield',
								'holder' => 'h4',
								'class' => 'title-class',
								'id' => 'block_preview_text',
								'heading' => __( 'Title', 'text-domain' ),
								'param_name' => 'title',
								'value' => __( '', 'text-domain' ),
						
								'admin_label' => false,
								'weight' => 0,
								'group' => 'Parameters',
							), 
							*/
							array(
								'type' => 'dropdown',						 
								'heading' => __( 'Style Variant', 'wab' ),
								'param_name' => 'style_variant',
							 
								'value' => array(
									//__( 'Select Layout',  "wab"  ) => '',
									__( 'Style 1',  "wab"  ) => '1',
									__( 'Style 2',  "wab"  ) => '2',
									__( 'Style 3',  "wab"  ) => '3',
									
								  ),
								//'std'  => '1',
								'save_always' => true,
								'description' => __( 'Please, select block styling', 'wab' ),
								'group' => 'Parameters',
								'dependency' => array(
									'element' => 'source',
									'is_empty' => true,
								),
							),
							array(
								'type' => 'woo_layout_preview',						 
								'heading' => __( 'Preview', 'wab' ),
								"param_name" => "data",
								"group" => "Parameters",							),
							
							
							array(
								'type' => 'dropdown',	
								"heading" => __("Category to use", 'wab'),
								"param_name" => "category_to_use",
								'description' => __( 'Leave empty if you want to show all items', 'wab' ),
								'value' => $out_cats,
								"id" => "category_to_use",
								"group" => "Parameters",
								 
							),
							
							 
							
						)
					)
				);   
				add_action( 'init', array( $this, 'vc_infobox_mapping' ) );
			 
				add_shortcode( 'single_wtr_element', array( $this, 'vc_infobox_html' ) );
			}
			 
			// Element Mapping
			public function vc_infobox_mapping() {
			 
				                             
					
			}
			 
			 
			// Element HTML
			public function vc_infobox_html( $atts, $content ) {
				global $post; 
			
			
			
				// Params extraction
				extract(
					shortcode_atts(
						array(
							'style_variant'   => '',
							'category_to_use'   => '',
						 
						), 
						$atts
					)
				);
 
				if( $style_variant == 1 ){
					if( $category_to_use != '' ){
						$html = do_shortcode( '[wootrl-style-1 id="'.$category_to_use.'" ]' );
					}else{
						$html = do_shortcode( '[wootrl-style-1]' );
					}
				}
				if( $style_variant == 2 ){
					if( $category_to_use != '' ){
						$html = do_shortcode( '[wootrl-style-2 id="'.$category_to_use.'" ]' );
					}else{
						$html = do_shortcode( '[wootrl-style-2]' );
					}
				}
		
				if( $style_variant == 3 ){
					if( $category_to_use != '' ){
						$html = do_shortcode( '[wootrl-style-3 id="'.$category_to_use.'" ]' );
					}else{
						$html = do_shortcode( '[wootrl-style-3]' );
					}
				}
				
			 
 
				return $html;
				 
			}
			 
		} // End Element Class
		 
		// Element Class Init
		new single_wtr_element(); 
	 
	 
	} 
}

if( function_Exists('vc_add_shortcode_param') ){
	vc_add_shortcode_param( 'woo_layout_preview', 'wab_woo_layout_preview' );
}
function wab_woo_layout_preview( $settings, $value ) {
	
	
	return  '
	
	<input type="hidden" id="review_image_url" value="'.get_option('home') .''. WOO_TRLS_PLUGIN_URL .'assets/img/'.'" />
	<div class=""    id="vc_layout_preview">
		
	</div>
	';
}
 
 
add_action('wp_print_scripts', 'wep_add_vc_script_fn');
function wep_add_vc_script_fn(){

	if(is_admin()){	
 
		wp_enqueue_script('wep_admi11n_js', plugins_url('/admin.js', __FILE__ ), array('jquery'  ), '1.0' ) ;
		wp_enqueue_style('wep_admin_css', plugins_url('/admin.css', __FILE__ ) ) ;	
	  }else{
 	
		
	  }
}
 