<?php

class Model {
	private function set_latest_insert_data() {
		global $latest_coupons;

		if ( empty( $latest_coupons ) ) {
			return false;
		}

		$insert_data = json_decode( get_option( 'latest_insert_data' ), true );

		// Add updated value to array
		$insert_data[ key( $latest_coupons ) ] = [
			'count' => count( current( $latest_coupons ), 1 ),
			'date'  => wp_date( 'Y-m-d H:i:s' ),
		];

		// Order array by date
		uasort(
			$insert_data,
			function ( $a, $b ) {
				return strtotime( $b['date'] ) - strtotime( $a['date'] );
			}
		);

		update_option( 'latest_insert_data', wp_json_encode( $insert_data ) );
		$latest_coupons = [];

		return $latest_coupons;
	}

	public function get_latest_insert_data() {
		return json_decode( get_option( 'latest_insert_data' ), true );
	}

	public function insert_coupons( $coupons ) {

		foreach ( $coupons as $coupon ) {
			if ( $coupon instanceof Coupon ) {
				post_insert( $coupon );
			}
		}

		$this->set_latest_insert_data();
	}
}
