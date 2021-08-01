<?php

class Idgu implements ICoupon {
	const API_URL  = '###';
	const TRACKING = '###';

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
		$dom      = new DOMDocument();
		$dom->loadXML( $response );
		$json = simplexml_import_dom( $dom );

		return json_decode( wp_json_encode( $json ), true )['products']['product'];
	}

	public function convert( $data ): Coupon {
		$coupon = new Coupon();
		$coupon->set_uid( $this->regex_uid( $data['PRODUCT_URL'] ) );
		$coupon->set_source( static::class );
		$coupon->set_title( $data['product_name'] );
		$coupon->set_url( $this->collect_url( $data['PRODUCT_URL'] ) );
		$coupon->set_image( $data['IMAGE'] );
		$coupon->set_start_date( '' );
		$coupon->set_end_date( $this->end_date() );
		$coupon->set_price( $data['PRICE'] );
		$coupon->set_full_price( $data['PRICE'] );
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
		return $data . '&' . self::TRACKING;
	}

	private function end_date() {
		return wp_date( 'Y-m-d', strtotime( '+7 days' ) );
	}
}
