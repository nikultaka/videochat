<?php
/**
 * The default template for displaying content
 *
 * Used for both singular and index.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty
 * @since Twenty Twenty 1.0
 */

?>


<div class="post-inner">
	<div class="entry-content">
		<?php
			the_content( __( 'Continue reading', 'twentytwenty' ) );
		?>
	</div>
</div>

