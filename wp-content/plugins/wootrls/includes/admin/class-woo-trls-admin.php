<?php
/**
 * Class Woo_TRLS_Admin
 * Woo Tradelines Plus administrative part
 *
 * @since 1.0.0
 */

class Woo_TRLS_Admin {

	/**
	 * Thumb counter
	 *
	 * @since 1.0.0
	 *
	 * @var int
	 */
	private $thumbnail_style_count = 0;

	/**
	 * Construct all admin part
	 *
	 * @since 1.0.0
	 * @since 1.0.1 Added ajax method 'check_license' / added link to 'add_link_to_menu'
	 */
	public function __construct() {

		$this->loadIncludes();

		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );

		add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_data_tab' ) );

		add_action( 'woocommerce_product_data_panels', array( $this, 'product_tab_content' ) );
		add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_settings' ) );

		add_action( 'admin_menu', array( $this, 'add_link_to_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );

		add_action( 'wp_ajax_wootrls_check_license', array( $this, 'check_license' ), 99 );

	}

	/**
	 * Includes all necessary PHP files for admin part
	 *
	 * This function is responsible for including all necessary PHP files.
	 *
	 * @since 2.1.8
	 */
	public function loadIncludes() {

		include WOO_TRLS_INC_PATH . 'admin/class-woo-trls-admin-product-exporter.php';

	}

	/**
	 * Load styles and scripts
	 *
	 * @since  1.0.0
	 */
	public function add_scripts() {

		wp_enqueue_style( 'woo-trls-admin-style', WOO_TRLS_PLUGIN_URL . 'assets/admin.css', array(),
			WOO_TRLS_VERSION );
		wp_enqueue_script( 'woo-trls-admin-script', WOO_TRLS_PLUGIN_URL . 'assets/admin.js',
			array( 'jquery' ), WOO_TRLS_VERSION, true );

	}

	/**
	 * Add tab
	 *
	 * @since 1.0.0
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	function add_product_data_tab( $tabs ) {

		$tabs['Tradelines'] = array(
			'label'  => __( 'Tradelines option', 'woocommerce-simple-plugin' ),
			'target' => 'tradeline_options',
			'class'  => 'show_if_tradeline',
		);

		return $tabs;
	}

	/**
	 * For to make new product with type Tradeline
	 *
	 * @since 1.0.0
	 */
	function product_tab_content() {

		if ( isset( $_GET['post'] ) ) {
			$product_object = wc_get_product( $_GET['post'] );
			if ( ! is_a( $product_object, 'WC_Product_Tradeline' ) ) {
				unset( $product_object );
			}
		}
		?>

        <div id='tradeline_options' class='panel woocommerce_options_panel'>
            <div class='options_group'>
				<?php
				if ( ! Woo_TRLS_License::check_key() ) {
					?>
                    <div class="wootrls-activation-warning">
                        <h3>Warning</h3>
                        For use tradeline product type, you need add license key on
                        <a href="<?php echo esc_url( admin_url( 'class-woo-trls-admin.php?page=woo-trls' ) ); ?>">plugin
                            settings
                            page</a>
                    </div>
					<?php
				} else {

					woocommerce_wp_text_input(
						array(
							'id'          => 'trd_woo_limit',
							'name'        => 'woo_tradeline[limit]',
							'label'       => __( 'Credit limit' ),
							'placeholder' => '',
							'desc_tip'    => 'true',
							'description' => __( 'Enter credit limit for a card' ),
							'type'        => 'number',
							'value'       => isset( $product_object ) ? $product_object->get_limit() : '',
						)
					);

					woocommerce_wp_select(
						array(
							'id'          => 'trd_woo_utilization',
							'name'        => 'woo_tradeline[utilization]',
							'label'       => __( 'Card have less than 15% utilization ?*' ),
							'desc_tip'    => 'true',
							'description' => __( 'Card have less than 15% utilization ?' ),
							'options'     => [
								'yes' => 'Yes',
								'no'  => 'No',
							],
							'value'       => isset( $product_object ) ? $product_object->get_utilization() : 'no',
						)
					);

					woocommerce_wp_select(
						array(
							'id'          => 'trd_woo_typeaccount',
							'name'        => 'woo_tradeline[typeaccount]',
							'label'       => __( 'Card type of account ?*' ),
							'desc_tip'    => 'true',
							'description' => __( 'Card type of account ?' ),
							'options'     => [
								'primary'    => 'Primary',
								'authorized' => 'Authorized',
								'business'   => 'Business',
								'aged-Corps' => 'Aged Corps',
								'personal'   => 'Personal',

							],
							'value'       => isset( $product_object ) ? $product_object->get_typeaccount() : 'personal',
						)
					);
					woocommerce_wp_text_input(
						array(
							'id'          => 'trd_woo_openeddate',
							'name'        => 'woo_tradeline[openeddate]',
							'label'       => __( 'Credit opened date' ),
							'placeholder' => '',
							'desc_tip'    => 'true',
							'description' => __( 'Enter the date of the card' ),
							'value'       => isset( $product_object ) ? $product_object->get_openeddate() : '',
						)
					);

					?>

					<?php
					woocommerce_wp_text_input(
						array(
							'id'          => 'trd_woo_report',
							'name'        => 'woo_tradeline[report]',
							'label'       => __( 'Report period' ),
							'placeholder' => '',
							'desc_tip'    => 'true',
							'description' => __( 'Enter report period for a card' ),
							'type'        => 'number',
							'value'       => isset( $product_object ) ?
								$product_object->get_meta( 'woo_tradeline_report' ) : '',
						)
					);

					woocommerce_wp_select(
						array(
							'id'          => 'trd_woo_softpull',
							'name'        => 'woo_tradeline[softpull]',
							'label'       => __( 'Soft Pull*' ),
							'desc_tip'    => 'true',
							'description' => __( 'Credit check soft pull or hard pull!' ),
							'options'     => [
								'yes' => 'Yes',
								'no'  => 'No',
							],
							'value'       => isset( $product_object ) ? $product_object->get_softpull() : 'no',
						)
					);


				}
				?>

            </div>
        </div>
		<?php

	}

	/**
	 * Save product settings
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id
	 */
	function save_product_settings( $post_id ) {

		$woo_tradeline = $_POST['woo_tradeline'];

		if ( ! empty( $woo_tradeline ) ) {
			$product_object = wc_get_product( $post_id );
			if ( is_a( $product_object, 'WC_Product_Tradeline' ) ) {
				foreach ( $woo_tradeline as $key => $value ) {
					update_post_meta( $post_id, 'woo_tradeline_' . $key, esc_attr( $value ) );
				}
			}
		}

	}

	/**
	 * Add link to admin left menu
	 *
	 * @since 1.0.1
	 * @since 2.1.4 Rename title
	 */
	function add_link_to_menu() {

		add_menu_page( __( 'WooTradelines' ), __( 'WooTradelines' ), 'manage_options', 'woo-trls', array(
			$this,
			'display_settings'
		), '', 56 );

	}

	/**
	 * Show tradelines list
	 *
	 * @since 1.0.1
	 */
	public function display_settings() {

		include WOO_TRLS_INC_PATH . 'admin/class-woo-trls-admin-settings.php';

	}

	/**
	 * Add meta boxes for additional images
	 *
	 * @since 1.0.0
	 */
	function add_meta_boxes() {

		add_meta_box( 'postimagedivstyle1', 'Thumb style 1 | 342x65', array(
			$this,
			'make_meta_box_thumbnail'
		), null, 'side', 'low', array( '__back_compat_meta_box' => true ) );

		add_meta_box( 'postimagedivstyle2', 'Thumb style 2 | 50x50', array(
			$this,
			'make_meta_box_thumbnail'
		), null, 'side', 'low', array( '__back_compat_meta_box' => true ) );

		add_meta_box( 'postimagedivstyle3', 'Thumb style 3 | 300x300', array(
			$this,
			'make_meta_box_thumbnail'
		), null, 'side', 'low', array( '__back_compat_meta_box' => true ) );

	}

	/**
	 * Make thumbnail for styles
	 *
	 * @since 1.0.0
	 */
	function make_meta_box_thumbnail( $post ) {

		$this->thumbnail_style_count ++;
		$product_object = wc_get_product( $post->ID );
		$style_id       = $this->thumbnail_style_count;

		if ( $product_object ) {
			$thumb_id = $product_object->meta_exists( 'woo_tradeline_thumb_' . $style_id ) ?
				$product_object->get_meta( 'woo_tradeline_thumb_' . $style_id ) : '';

			echo '<div class="woo-tradeline-thumb-wrapper">';
			echo '<input type="hidden" value="' . $thumb_id
			     . '" class="woo-tradeline-thumb-id" name="woo_tradeline[thumb_' . $style_id . ']" >';

			if ( $product_object->meta_exists( 'woo_tradeline_thumb_' . $style_id )
			     && is_numeric( $product_object->get_meta( 'woo_tradeline_thumb_' . $style_id ) ) ) {
				echo '<img src="' . wp_get_attachment_url(
						$product_object->get_meta( 'woo_tradeline_thumb_' . $style_id ) ) .
				     '" style="max-width: 100%" >';
			} else {
				echo '<a href="#" class="woo-tradeline-thumb-loader" data-post_id="' . $post->ID . '" >Load style '
				     . $style_id . ' image</a>';
			}
			echo '</div>';
		}

	}

	/**
	 * Check plugin license key and save if it correct
	 *
	 * @since 1.0.1
	 */
	public function check_license() {

		ob_clean();
		parse_str( $_POST['data'], $postdata );

		$key = sanitize_text_field( $postdata['key'] );

		if ( Woo_TRLS_License::check_key( $key ) ) {
			update_option( Woo_TRLS_License::$option_name, $key );

			wp_send_json_success();
		} else {
			wp_send_json_error();
		}

	}

}

/**
 * Run WooTRLSAdmin class
 *
 * @since 1.0.0
 *
 * @return Woo_TRLS_Admin
 */
function woo_trls_admin_runner() {

	return new Woo_TRLS_Admin();
}

//Run just in admin part
if ( is_admin() ) {
	woo_trls_admin_runner();
}