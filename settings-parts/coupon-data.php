<?php
global $model;
$latest_insert_data = $model->get_latest_insert_data();
?>
<div class="coupon-panel-row">
	<div class="coupon-panel-col">
		<h3><span class="dashicons dashicons-database-import"></span> Recently uploaded</h3>
		<?php
		if ( $latest_insert_data ) {
			foreach ( $latest_insert_data as $name => $datum ) {
				?>
				<p>
					<strong><?php echo esc_html( $name ); ?></strong>:
					<?php echo esc_html( $datum['count'] ); ?> / <?php echo esc_html( $datum['date'] ); ?>
				</p>
				<?php
			}
		}
		?>
	</div>
	<div class="coupon-panel-col">
		<h3><span class="dashicons dashicons-calendar"></span> Upcoming events</h3>
		<div class="loaded-upcoming-events">
			<?php upcoming_events_html(); ?>
		</div>
	</div>
</div>

