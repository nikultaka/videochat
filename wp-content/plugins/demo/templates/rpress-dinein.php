<div class="tab-pane fade delivery-settings-wrapper" id="nav-dinein" role="tabpanel" aria-labelledby="nav-delivery-tab">
	<!-- Delivery Time Wrap -->
  <div class="rpress-delivery-time-wrap rpress-time-wrap">

    <?php do_action( 'rpress_before_service_time', 'Dine In' ); ?>

    <?php
      if ( rpress_is_service_enabled( 'delivery' ) ) :

        $store_times = rp_get_store_timings();
        $store_timings = apply_filters( 'rpress_store_delivery_timings', $store_times );

        $store_time_format = rpress_get_option( 'store_time_format' );

        if ( empty( $store_time_format ) ) {
          $store_time_format = '12hrs';
        }

        if ( $store_time_format == '24hrs' ) {
          $time_format = 'H:i';
        }
        else {
          $time_format = 'h:ia';
        }
    ?>
    <div class="delivery-time-text">
      <?php echo apply_filters( 'rpress_delivery_time_string', esc_html_e( 'Select a dine In time', 'restropress' ) ); ?>
    </div>

  		<select class="rpress-delivery rpress-allowed-delivery-hrs rpress-hrs rp-form-control" id="rpress-delivery-hours" name="rpress_allowed_hours">
  		<?php
        if( is_array( $store_timings ) ) :
          foreach( $store_timings as $time ) :
            $loop_time = date( $time_format, $time );
      ?>
            <option value='<?php echo $loop_time; ?>'>
              <?php echo $loop_time; ?>
            </option>
            <?php
          endforeach;
        endif;
  			?>
  		</select>
    <?php endif; ?>
    <?php do_action( 'rpress_after_service_time', 'delivery' ); ?>
	</div>
	<!-- Delivery Time Wrap Ends Here -->
</div>
