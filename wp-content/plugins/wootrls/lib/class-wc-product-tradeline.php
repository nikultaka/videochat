<?php

/**
 * Make new produc type for tradelines
 *
 * @since 1.0.0
 */

class WC_Product_Tradeline extends WC_Product {

	/**
	 * Adding tradeline type
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_type() {

		return WOO_TRLS_PRODUCT_TYPE;
	}

	/**
	 * Card limit
	 *
	 * @since 1.0.0
	 *
	 * @return int|mixed
	 */
	public function get_limit() {

		return $this->meta_exists( 'woo_tradeline_limit' ) ? $this->get_meta( 'woo_tradeline_limit' ) : 0;
	}

	/**
	 * Card opened
	 *
	 * @since 1.0.0
	 *
	 * @return int|mixed
	 */
	public function get_openeddate() {

		return $this->meta_exists( 'woo_tradeline_openeddate' ) ? $this->get_meta( 'woo_tradeline_openeddate' ) : '';
	}

	/**
	 * Bank image for that card
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_bank_image() {

		$categories = $this->get_category_ids();
		if ( count( $categories ) <= 0 ) {
			return '';
		}
		$thumbnail_id = get_term_meta( $categories[0], 'thumbnail_id', true );
		$image        = wp_get_attachment_url( $thumbnail_id );

		return '<img src="' . $image . '" alt="' . get_the_category_by_ID( $categories[0] ) . '" />';
	}

	/**
	 * Get styles for thumb
	 *
	 * @since 1.0.0
	 *
	 * @param $style_id
	 *
	 * @return string
	 */
	public function get_thumb_style( $style_id ) {

		if ( ! $this->meta_exists( 'woo_tradeline_thumb_' . $style_id ) ) {
			$image = WOO_TRLS_PLUGIN_URL . 'assets/img/noimage' . $style_id . '.png';
		} else {
			$thumbnail_id = $this->get_meta( 'woo_tradeline_thumb_' . $style_id );
			$image        = wp_get_attachment_url( $thumbnail_id );
		}

		return '<img src="' . $image . '" />';
	}

	/**
	 * Reporting period
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|string
	 */
	public function get_reporting_period() {

		$period = '';
		if ( $this->meta_exists( 'woo_tradeline_report' ) ) {
			$period .= date( 'M jS - ' );
			$period .= date( 'M jS', strtotime( '+ ' . $this->get_meta( 'woo_tradeline_report' ) . ' days' ) );
		}

		return $period;
	}

	/**
	 * Product utilization Yes or No
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|string
	 */
	public function get_utilization() {

		return $this->meta_exists( 'woo_tradeline_utilization' ) ? $this->get_meta( 'woo_tradeline_utilization' ) : 'No';
	}

	/**
	 * Product typeaccount
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|string
	 */
	public function get_typeaccount() {

		return $this->meta_exists( 'woo_tradeline_typeaccount' ) ? $this->get_meta( 'woo_tradeline_typeaccount' ) : 'personal';
	}

	/**
	 * Product softpull Yes or No
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|string
	 */
	public function get_softpull() {

		return $this->meta_exists( 'woo_tradeline_softpull' ) ? $this->get_meta( 'woo_tradeline_softpull' ) : 'No';
	}

	/**
	 * Get purchase date of product
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_purchase_by_date() {

		if ( $this->meta_exists( 'wpas_schedule_sale_end_time' ) ) {
			return date( 'M jS', strtotime( '+ ' . $this->get_meta( 'wpas_schedule_sale_end_time' ) . ' days' ) );
		}

		return '';
	}
}