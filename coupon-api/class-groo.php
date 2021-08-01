<?php

class Groo implements ICoupon {
	const API_LOGIN_URL     = '###';
	const API_PRODUCTS_URL  = '###';
	const API_LOGIN         = '###';
	const API_PASSWORD      = '###';
	const COUPONS_CATEGORY  = 1;
	const TRAVELS_CATEGORY  = 3;
	const PRODUCTS_CATEGORY = 4;

	private $coupons = [];

	public function __construct() {
		$json = $this->parse();

		foreach ( $json as $obj ) {
			$coupon = $this->convert( $obj );
			$this->add_coupon( $coupon );
		}
	}

	public function parse() {
		$result     = [];
		$categories = [ self::COUPONS_CATEGORY, self::TRAVELS_CATEGORY, self::PRODUCTS_CATEGORY ];

		$login_args = array(
			'timeout'    => 50,
			'user-agent' => ICoupon::USER_AGENT,
			'body'       => array(
				'userName' => self::API_LOGIN,
				'password' => self::API_PASSWORD,
			),
		);

		$login_url = response_checker( self::API_LOGIN_URL, $login_args, 'post' );

		if ( ! $login_url ) {
			return [];
		}

		$token = json_decode( $login_url['body'], true )['data']['token'];

		foreach ( $categories as $category ) {
			$products_args = response_setter( ICoupon::USER_AGENT, $token, $category );
			$response      = response_checker( self::API_PRODUCTS_URL, $products_args, 'post' );

			if ( $response ) {
				$data = json_decode( $response['body'], true )['data'];
				if ( $data !== false ) {
					$result[] = $data;
				}
			}
		}

		if ( ! empty( $result ) ) {
			return array_merge( ...$result );
		}

		return [];
	}

	public function convert( $data ): Coupon {
		$coupon = new Coupon();
		$coupon->set_uid( $data['id'] );
		$coupon->set_source( static::class );
		$coupon->set_title( $data['newsletter_title'] );
		$coupon->set_url( $this->collect_url( $data['product_url'] ) );
		$coupon->set_image( $this->regex_image( $data['image'] ) );
		$coupon->set_start_date( '' );
		$coupon->set_end_date( $this->end_date() );
		$coupon->set_price( $data['price'] );
		$coupon->set_full_price( $data['old_price'] );
		$coupon->set_discount( '' );
		$coupon->set_saving( '' );
		$coupon->set_purchased( $this->regex_purchased( $data['purchase_txt'] ) );
		$coupon->set_currency_code( strtolower( $data['currency']['iso'] ) );
		$coupon->set_category( $data['category_seo'] );
		$coupon->set_location( $data['location_text'] );
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

	private function regex_image( $data ): string {
		return strtok( $data, '?' );
	}

	private function collect_url( $data ): string {
		return $data . '&' . ICoupon::TRACKER_ID;
	}

	private function end_date() {
		return wp_date( 'Y-m-d', strtotime( '+7 days' ) );
	}

	private function regex_purchased( $data ): string {
		preg_match( '/\d+/', $data, $uid_output );

		return isset( $uid_output[0] ) ? $uid_output[0] : '';
	}
}
