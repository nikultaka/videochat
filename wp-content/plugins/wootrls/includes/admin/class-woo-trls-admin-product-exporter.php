<?php
/**
 * Class Woo_TRLS_Admin_Product_Exporter
 * Extends default WooCommerce Export/Import for work with tradeline products
 *
 * @since 2.1.8
 */

class Woo_TRLS_Admin_Product_Exporter {

	/**
	 * List of all tradeline options
	 *
	 * @since 2.2.5
	 *
	 * @var array
	 */
	private $columns;

	/**
	 * Woo_TRLS_Admin_Product_Exporter constructor.
	 *
	 * @since 2.1.8
	 */
	public function __construct() {

		$this->columns = array(
			'woo_tradeline_thumb_1'     => array( 'images1', 'image' ),
			'woo_tradeline_thumb_2'     => array( 'images2', 'image' ),
			'woo_tradeline_thumb_3'     => array( 'images3', 'image' ),
			'woo_tradeline_limit'       => array( 'Trdl Credit limit', 'integer' ),
			'woo_tradeline_utilization' => array( 'Trdl Utilization', 'string' ),
			'woo_tradeline_openeddate'  => array( 'Trdl Date Open', 'date' ),
			'woo_tradeline_report'      => array( 'Trdl Report period', 'integer' ),
			'woo_tradeline_typeaccount' => array( 'Trdl Account Type', 'string' ),
			'woo_tradeline_softpull'    => array( 'Trdl Soft Pull', 'string' ),
		);

		add_filter( 'woocommerce_product_export_meta_value', array( $this, 'customize_cf_export' ), 1, 4 );
		add_filter( 'woocommerce_product_export_column_names', array( $this, 'add_export_column' ) );
		add_filter( 'woocommerce_product_export_product_default_columns', array( $this, 'add_export_column' ) );

		foreach ( $this->columns as $key => $column ) {
			add_filter( 'woocommerce_product_export_product_column_' . $key, array(
				$this,
				'add_export_' . $key
			), 10, 2 );
		}

		add_filter( 'woocommerce_csv_product_import_mapping_options', array( $this, 'add_import_columns' ) );
		add_filter( 'woocommerce_csv_product_import_mapping_default_columns', array(
			$this,
			'add_column_to_mapping_screen'
		) );
		add_filter( 'woocommerce_product_import_pre_insert_product_object', array( $this, 'process_import' ), 10, 2 );

	}

	/**
	 * Add normal names for tradelines columns
	 *
	 * @since 2.2.5
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	function add_export_column( $columns ) {

		foreach ( $this->columns as $key => $column ) {
			$columns[ $key ] = $column[0];
		}

		return $columns;
	}

	/**
	 * Data for thumb1
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_thumb_1( $value, $product ) {

		$attachment_id = $product->get_meta( 'woo_tradeline_thumb_1', true, 'edit' );
		$value         = wp_get_attachment_image_url( $attachment_id, 'full' );

		return $value;
	}

	/**
	 * Data for thumb2
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_thumb_2( $value, $product ) {

		$attachment_id = $product->get_meta( 'woo_tradeline_thumb_2', true, 'edit' );
		$value         = wp_get_attachment_image_url( $attachment_id, 'full' );

		return $value;
	}

	/**
	 * Data for thumb3
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_thumb_3( $value, $product ) {

		$attachment_id = $product->get_meta( 'woo_tradeline_thumb_3', true, 'edit' );
		$value         = wp_get_attachment_image_url( $attachment_id, 'full' );

		return $value;
	}

	/**
	 * Data for limit
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_limit( $value, $product ) {

		$value = $product->get_meta( 'woo_tradeline_limit', true, 'edit' );

		return $value;
	}

	/**
	 * Data for utilization
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_utilization( $value, $product ) {

		$value = $product->get_meta( 'woo_tradeline_utilization', true, 'edit' );

		return $value;
	}

	/**
	 * Data for opened date
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_openeddate( $value, $product ) {

		$value = $product->get_meta( 'woo_tradeline_openeddate', true, 'edit' );

		return $value;
	}

	/**
	 * Data for report
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_report( $value, $product ) {

		$value = $product->get_meta( 'woo_tradeline_report', true, 'edit' );

		return $value;
	}

	/**
	 * Data for account type
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_typeaccount( $value, $product ) {

		$value = $product->get_meta( 'woo_tradeline_typeaccount', true, 'edit' );

		return $value;
	}

	/**
	 * Data for soft pull
	 *
	 * @since 2.2.5
	 *
	 * @param mixed $value (default: '')
	 * @param WC_Product $product
	 *
	 * @return mixed $value
	 */
	function add_export_woo_tradeline_softpull( $value, $product ) {

		$value = $product->get_meta( 'woo_tradeline_softpull', true, 'edit' );

		return $value;
	}

	/**
	 * Register the 'Custom Column' column in the importer.
	 *
	 * @param array $options
	 *
	 * @return array $options
	 */
	public function add_import_columns( $options ) {

		foreach ( $this->columns as $key => $column ) {
			$options[ $key ] = $column[0];
		}

		return $options;
	}

	/**
	 * Add automatic mapping support for 'Custom Column'.
	 * This will automatically select the correct mapping for columns named 'Custom Column' or 'custom column'.
	 *
	 * @param array $columns
	 *
	 * @return array $columns
	 */
	public function add_column_to_mapping_screen( $columns ) {

		foreach ( $this->columns as $key => $column ) {
			$columns[ $column[0] ] = $key;
		}

		return $columns;
	}

	/**
	 * Convert all img urls to attachment_id
	 *
	 * @param WC_Product $object - Product being imported or updated.
	 * @param array $data - CSV data read for the product.
	 *
	 * @return WC_Product $object
	 */
	public function process_import( $object, $data ) {

		foreach ( $this->columns as $key => $column ) {

			if ( ! isset( $data[ $key ] ) || empty( $data[ $key ] ) ) {
				continue;
			}

			switch ( $column[1] ) {
				case 'image':
					$attachment_id = $this->import_image( $data[ $key ] );
					if ( 0 < $attachment_id ) {
						$object->update_meta_data( $key, $attachment_id );
					}
					break;
				case 'integer':
					$object->update_meta_data( $key, intval( $data[ $key ] ) );
					break;
				case 'string':
					$object->update_meta_data( $key, sanitize_text_field( $data[ $key ] ) );
					break;
				case 'date':
					$object->update_meta_data( $key, date( 'd.m.Y', strtotime( $data[ $key ] ) ) );
					break;
			}
		}

		return $object;
	}

	/**
	 * Load image from URL to Library
	 *
	 * @since 2.2.5
	 *
	 * @param $url URL to image
	 *
	 * @return int Attachment id
	 */
	private function import_image( $url ) {

		$upload_dir = wp_upload_dir();
		$image_data = file_get_contents( $url );
		$filename   = basename( $url );

		if ( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		file_put_contents( $file, $image_data );

		$wp_filetype = wp_check_filetype( $filename, null );

		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		$attach_id = wp_insert_attachment( $attachment, $file );
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

}

/**
 * Run Woo_TRLS_Admin_Product_Exporter class
 *
 * @since 2.1.8
 *
 * @return Woo_TRLS_Admin_Product_Exporter
 */
function woo_trls_admin_product_exporter_runner() {

	return new Woo_TRLS_Admin_Product_Exporter();
}

woo_trls_admin_product_exporter_runner();