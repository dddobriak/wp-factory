<?php

class Isrotel implements ICoupon {
	const API_URL      = '###';
	const IMG_PREF_URL = '###';

	private $coupons = [];

	public function __construct() {
		$json = $this->parse();

		foreach ( $json as $obj ) {
			$coupon = $this->convert( $obj );
			$this->add_coupon( $coupon );
		}
	}

	public function parse() {
		$args = array(
			'timeout'    => 10,
			'user-agent' => ICoupon::USER_AGENT,
		);

		$response = response_checker( self::API_URL, $args, 'get' );
		if ( ! $response ) {
			return [];
		}

		$response = $response['body'];
		return json_decode( $response, true );
	}

	public function convert( $data ) {
		$coupon = new Coupon();
		$coupon->set_uid( $this->regex_uid( $data['NavigationUrl'] ) );
		$coupon->set_source( static::class );
		$coupon->set_title( "{$data['Title']} {$data['Description']}" );
		$coupon->set_url( $this->collect_url( $data['NavigationUrl'] ) );
		$coupon->set_image( $this->regex_image( $data['ImageUrl'] ) );
		$coupon->set_start_date( '' );
		$coupon->set_end_date( $this->end_date() );
		$coupon->set_price( $data['Price'] );
		$coupon->set_full_price( $data['Price'] );
		$coupon->set_discount( '' );
		$coupon->set_saving( '' );
		$coupon->set_purchased( '' );
		$coupon->set_currency_code( '' );
		$coupon->set_category( '' );
		$coupon->set_location( '' );
		$coupon->set_hash();

		return $coupon;
	}

	public function add_coupon( $data ) {
		if ( $data instanceof Coupon ) {
			$this->coupons[] = $data;
		}
	}

	public function get_coupons(): array {
		return $this->coupons;
	}

	private function regex_uid( $data ): string {
		preg_match( '/\d+$/', $data, $uid_output );

		return isset( $uid_output[0] ) ? $uid_output[0] : '';
	}

	private function collect_url( $data ): string {
		return $data . '&' . ICoupon::TRACKER_ID;
	}

	private function regex_image( $data ): string {
		return self::IMG_PREF_URL . $data;
	}

	private function end_date() {
		return wp_date( 'Y-m-d', strtotime( '+7 days' ) );
	}
}
