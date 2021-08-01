<?php

class Couponi implements ICoupon {
	const API_URL  = '###';
	const TRACKING = 'tracking=?';

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

		return json_decode( wp_json_encode( $json ), true )['deal'];
	}

	public function convert( $data ): Coupon {
		$coupon = new Coupon();
		$coupon->set_uid( $data['uid'] );
		$coupon->set_source( static::class );
		$coupon->set_title( $data['title'] );
		$coupon->set_url( $this->url_encode( $data['url'] ) );
		$coupon->set_image( $data['image'] );
		$coupon->set_start_date( $data['start-date'] );
		$coupon->set_end_date( $this->end_date( $data['end-date'] ) );
		$coupon->set_price( $data['price'] );
		$coupon->set_full_price( $data['full-price'] );
		$coupon->set_discount( $data['discount-percentage'] );
		$coupon->set_saving( $data['saving'] );
		$coupon->set_purchased( $data['ordered'] );
		$coupon->set_currency_code( '' );
		$coupon->set_category( $data['category'] );
		$coupon->set_location( $data['address'] );
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

	public function url_encode( $full_url ): string {
		$url_path = wp_parse_url( $full_url, PHP_URL_PATH );
		$str      = isset( explode( '/', $url_path )[2] ) ? explode( '/', $url_path )[2] : ' ';

		return str_replace( $str, rawurlencode( $str ), $full_url );
	}

	private function end_date( $data ) {
		$end_date = new DateTime( $data );
		return $end_date->format( 'Y-m-d' );
	}
}
