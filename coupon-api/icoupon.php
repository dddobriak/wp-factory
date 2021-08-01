<?php

interface ICoupon {
	const USER_AGENT = '###';
	const TRACKER_ID = '###';

	/**
	 * Parse url
	 *
	 * @return mixed
	 */
	public function parse();

	/**
	 * Convert array element to object
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function convert( $data );

	/**
	 * Add coupon object to array
	 *
	 * @param $data
	 *
	 * @return mixed
	 */
	public function add_coupon( $data );

	/**
	 * Get coupons list
	 *
	 * @return mixed
	 */
	public function get_coupons();
}
