<?php

class BigDeal implements ICoupon {
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
		$coupon->set_uid( $this->regex_uid( $data['urlToSite'] ) );
		$coupon->set_source( static::class );
		$coupon->set_title( $data['title'] );
		$coupon->set_url( $data['urlToSite'] );
		$coupon->set_image( $data['imageURL'] );
		$coupon->set_start_date( '' );
		$coupon->set_end_date( $this->end_date( $data['endTime'] ) );
		$coupon->set_price( $data['priceAfterDiscount'] );
		$coupon->set_full_price( $data['regPrice'] );
		$coupon->set_discount( '' );
		$coupon->set_saving( '' );
		$coupon->set_purchased( $data['orderAmount'] );
		$coupon->set_currency_code( '' );
		$coupon->set_category( $data['category'] );
		$coupon->set_location( $data['area'] );
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
		preg_match( '/CampaignId=(\d+)/', $data, $uid_output );

		return isset( $uid_output[1] ) ? $uid_output[1] : '';
	}

	private function end_date( $data ): string {
		$data      = strtok( $data, ' ' );
		$timestamp = strtotime( str_replace( '/', '-', $data ) );

		return wp_date( 'Y-m-d', $timestamp );
	}
}
