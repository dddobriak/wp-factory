<?php

class CouponLoader {
	public function __construct() {
		add_action( 'wp_ajax_couponLoader', [ $this, 'ajax_jequest' ] );
		add_action( 'coupon_load_all', [ $this, 'coupon_functions' ] );
		add_action( 'switch_theme', [ $this, 'coupon_load_all_false' ] );
	}

	public function coupon_load_all_true() {
		update_option( 'coupon_load_all', true );
	}

	public function coupon_load_all_false() {
		update_option( 'coupon_load_all', false );
	}

	/**
	 * Current time + 5 minutes
	 */
	public function get_current_time_up() {
		$n = gmdate( 'i' );
		$x = 5;

		$minutes = round( ( (int) $n + $x / 2 ) / $x ) * $x;
		if ( $n[0] === '0' && $minutes < 10 ) {
			$minutes = 0 . $minutes;
		}

		$current_time_up = gmdate( 'H' ) . ':' . (string) $minutes;

		return DateTime::createFromFormat( 'H:i', $current_time_up );
	}

	/**
	 * Set schedule and create single event
	 */
	public function load() {
		global $cron;

		// Delete schedule before full upload
		$cron->delete_schedule();
		$this->coupon_load_all_true();

		// Create event once
		wp_schedule_single_event( $this->get_current_time_up()->getTimestamp(), 'coupon_load_all' );
	}

	/**
	 * Loop all active apis
	 */
	public function coupon_functions() {
		global $cron, $api_control, $coupon_cleaner;

		foreach ( $api_control->active_api() as $api ) {
			start_insert( $api );
		}

		// Create schedule after full upload
		$cron->create_schedule();
		$coupon_cleaner->cleaner_function();
		$this->coupon_load_all_false();
	}

	public function ajax_jequest() {
		check_ajax_referer( 'apiLoader-nonce', 'nonce_code' );
		$this->load();
		echo wp_kses( '<p>It will be uploaded shortly!</p>', 'post' );
		wp_die();
	}
}
