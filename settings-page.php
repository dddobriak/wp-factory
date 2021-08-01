<?php

/**
 * Register coupon settings page
 */
add_action( 'admin_menu', 'coupons_settings' );

function coupons_settings() {
	add_submenu_page(
		'edit.php?post_type=coupon',
		'Coupon settings page',
		'Coupon settings',
		'manage_options',
		'coupons-settings',
		'coupon_settings_page_html'
	);
}

function coupon_settings_page_html() { ?>
	<div class="wrap">
		<div class="coupon-panel">
			<h1>Coupon settings</h1>
			<?php get_template_part( '/inc/settings-parts/api-control' ); ?>
			<hr>
			<?php get_template_part( '/inc/settings-parts/coupon-data' ); ?>
			<?php get_template_part( '/inc/settings-parts/coupon-loader' ); ?>
			<hr>
			<p><span class="dashicons dashicons-clock"></span> <strong>Current server time</strong>: <?php echo esc_html( wp_date( 'H:i:s' ) ); ?></p>
		</div>
	</div>
	<?php
}
