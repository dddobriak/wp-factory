<?php

class CouponCleaner {
	const CLEANER_TIME = [ '05:05' ];

	public function __construct() {
		add_action( 'after_switch_theme', [ $this, 'create_cleaner_event' ] );
		add_action( 'switch_theme', [ $this, 'delete_cleaner_event' ] );
		add_action( 'coupon_cleaner_event', [ $this, 'cleaner_function' ] );
	}

	/**
	 * Schedule cleaner event
	 */
	public function create_cleaner_event() {
		// convert array times to object
		$array = array_map(
			function ( $a ) {
				return DateTime::createFromFormat( 'H:i', $a );
			},
			self::CLEANER_TIME
		);

		// Create event
		wp_schedule_event( current( $array )->modify( dailyd_offset( false ) )->getTimestamp(), 'daily', 'coupon_cleaner_event' );
	}

	/**
	 *  Unschedule cleaner event
	 */
	public function delete_cleaner_event() {
		// Delete event
		wp_unschedule_hook( 'coupon_cleaner_event' );
	}

	/**
	 * Cleaner function
	 */
	public function cleaner_function() {
		$expired_coupons = $this->get_expired_coupons();
		foreach ( $expired_coupons as $coupon ) {
			wp_delete_post( $coupon, true );
		}
	}

	/**
	 * Get expired coupons
	 */
	public function get_expired_coupons() {
		global $wpdb;
		$results = $wpdb->get_results( "select post_id, date(meta_value) as end_date from $wpdb->postmeta where meta_key = 'end_date' having end_date < CURDATE()" );

		if ( $results ) {
			return array_map( 'intval', wp_list_pluck( $results, 'post_id' ) );
		}

		return [];
	}
}
