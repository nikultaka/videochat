<?php
/**
 * Class Woo_TRLS_Pagination
 * Class for display pagination template
 *
 * @since 2.1.8
 */

class Woo_TRLS_Pagination {

	/**
	 * Limit elements per page
	 *
	 * @since 2.1.8
	 *
	 * @var integer
	 */
	const ELEMENTS_PER_PAGE = 8;

	/**
	 * Show pagination template
	 *
	 * @since 2.1.8
	 *
	 * @param $page Current page number
	 * @param $total Total pages
	 * @param int $length Length from left and right
	 */
	public static function display_pagination( $page, $total, $length = 5 ) {

		$html = '<ul class="woo-trls-pagination">';

		if ( $page > 1 ) {
			$link = esc_url( add_query_arg( array( 'trls_page' => $page - 1 ), $_SERVER['REQUEST_URI'] ) );
			$html .= '<li class="first" ><a href="' . $link . '" ><span>&lt;</span></a></li>';
		}

		$from = $page - $length;
		if ( $from < 1 ) {
			$from = 1;
		}

		$to = $page + $length;
		if ( $to > $total ) {
			$to = $total;
		}

		for ( $i = $from; $i <= $to; $i ++ ) {
			$link = esc_url( add_query_arg( array( 'trls_page' => $i ), $_SERVER['REQUEST_URI'] ) );
			if ( $page == $i ) {
				$html .= '<li><a href="' . $link . '" class="active" ><span>' . $i . '</span></a></li>';
			} else {
				$html .= '<li><a href="' . $link . '" ><span>' . $i . '</span></a></li>';
			}
		}

		if ( $page < $total ) {
			$link = esc_url( add_query_arg( array( 'trls_page' => $page + 1 ), $_SERVER['REQUEST_URI'] ) );
			$html .= '<li class="last" ><a href="' . $link . '" ><span>&gt;</span></a></li>';
		}

		$html .= '</ul>';

		echo $html;

	}


	/**
	 * Get limits from settings
	 *
	 * @since 2.2.5
	 *
	 * @param $value
	 *
	 * @return int
	 */
	public static function get_limit( $value ) {

		$settings = get_option( 'trls_settings_block' );

		if ( $settings[ 'limit_output_' . $value ] ) {
			return $settings[ 'limit_output_' . $value ];
		}

		return self::ELEMENTS_PER_PAGE;
	}

}