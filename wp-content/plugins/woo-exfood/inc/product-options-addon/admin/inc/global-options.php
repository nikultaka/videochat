<?php
class EXWO_Global_Op_Posttype {
	public function __construct()
    {
		add_action( 'init', array( &$this, 'register_post_type' ) );
		//add_action( 'cmb2_admin_init', array( &$this,'register_metabox') );
		add_filter( 'manage_exwo_glboptions_posts_columns', array( &$this,'_edit_columns'),99 );
		add_action( 'manage_exwo_glboptions_posts_custom_column', array( &$this,'_custom_columns_content'),12);
    }
    function register_post_type(){
    	$text_domain = exwo_text_domain();
		$labels = array(
			'name'               => esc_html__('Global Options',$text_domain),
			'singular_name'      => esc_html__('Option',$text_domain),
			'add_new'            => esc_html__('Add New Option',$text_domain),
			'add_new_item'       => esc_html__('Add New Option',$text_domain),
			'edit_item'          => esc_html__('Edit Option',$text_domain),
			'new_item'           => esc_html__('New Option',$text_domain),
			'all_items'          => esc_html__('Global Options',$text_domain),
			'view_item'          => esc_html__('View Option',$text_domain),
			'search_items'       => esc_html__('Search Option',$text_domain),
			'not_found'          => esc_html__('No Option found',$text_domain),
			'not_found_in_trash' => esc_html__('No Option found in Trash',$text_domain),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html__('Global Options',$text_domain)
		);
		$rewrite =  false;
		$args = array(  
			'labels' => $labels,  
			'supports' => array('title'),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => 'edit.php?post_type=product',
			'menu_icon' =>  '',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 1,
			'rewrite' => $rewrite,
			'taxonomies' => array('product_cat')
		); 
		register_post_type('exwo_glboptions',$args);  
	}
	// Register metadata
	function register_metabox() {
		$text_domain = exwo_text_domain();
		$prefix = 'exwo_';

		/**
		 * Food general info
		 */
		$glb_options = new_cmb2_box( array(
			'id'            => $prefix . 'order_meta',
			'title'         => esc_html__( 'Settings', $text_domain ),
			'object_types'  => array( 'exwo_glboptions' ),
		) );

		$glb_options->add_field( array(
			'name'       => esc_html__( 'Test', $text_domain ),
			'desc'       => '',
			'id'         => $prefix . 'fname',
			'type'       => 'text',
			'classes'		 => '',
		) );
		
	}
	function _edit_columns($columns){
		$text_domain = exwo_text_domain();
		global $wpdb;
		$columns['cate'] = esc_html__( 'Categories' , $text_domain );		
		return $columns;
	}
	function _custom_columns_content( $column ) {
		$text_domain = exwo_text_domain();
		global $post;
		switch ( $column ) {
			case 'cate':
				/* Get the genres for the post. */
		        $terms = get_the_terms( $post->ID, 'product_cat' );

		        /* If terms were found. */
		        if ( !empty( $terms ) ) {

		            $out = array();

		            /* Loop through each term, linking to the 'edit posts' page for the specific term. */
		            foreach ( $terms as $term ) {
		                $out[] = sprintf( '<a href="%s">%s</a>',
		                    esc_url( add_query_arg( array( 'post_type' => $post->post_type, 'product_cat' => $term->slug ), 'edit.php' ) ),
		                    esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, 'product_cat', 'display' ) )
		                );
		            }

		            /* Join the terms, separating them with a comma. */
		            echo join( ', ', $out );
		        }

		        /* If no terms were found, output a default message. */
		        else {
		            esc_html_e( 'No Categories',$text_domain );
		        }
				break;
			default :
        		break;	
		}
	}
}
$EXWO_Global_Op_Posttype = new EXWO_Global_Op_Posttype();