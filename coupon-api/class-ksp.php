<?php

class KSP implements ICoupon {
	const API_URL   = '###';
	const API_KEY   = '###';
	const API_TOKEN = '###';

	private $coupons = [];

	public function __construct() {
		$json = $this->parse();
		foreach ( $json as $obj ) {
			$coupon = $this->convert( $obj );
			$this->add_coupon( $coupon );
		}
	}

	public function parse(): array {
		$full_array = [];
		$api_url    = self::API_URL . '?appKey=' . self::API_KEY . '&token=' . self::API_TOKEN . '&find=';
		$args_json  = get_template_directory_uri() . '/inc/coupon-api/ksp-json/args.json';
		$file       = wp_remote_get( $args_json );

		if ( 200 === wp_remote_retrieve_response_code( $file ) ) {
			$file = json_decode( $file['body'], true );
		} else {
			return [];
		}

		$args = array(
			'timeout'    => 10,
			'user-agent' => ICoupon::USER_AGENT,
		);

		foreach ( $file as $key => $arg ) {
			$api      = $api_url . $arg;
			$response = response_checker( $api, $args, 'get' );

			if ( ! $response ) {
				return [];
			}

			$jsons_data = json_decode( $response['body'], true );

			foreach ( $jsons_data as $url_key => $json_array ) {
				$category                 = $json_array['category'];
				$brand                    = $json_array['brand'];
				$json_array['category']   = $brand . ' - ' . $category;
				$full_array[][ $url_key ] = $json_array;
			}
		}

		return $full_array;
	}

	public function file_cats(): array {
		$cats = [];
		foreach ( $this->json_files as $json_key => $json ) {
			preg_match( '/\/(\w+).json$/', $json, $output_array );
			$cats[] = $output_array[1];
		}

		return $cats;
	}

	public function convert( $data ): Coupon {
		$coupon = new Coupon();
		$coupon->set_uid( key( $data ) );
		$data = current( $data );
		$coupon->set_source( static::class );
		$coupon->set_title( $data['name'] );
		$coupon->set_url( $data['url'] );
		$coupon->set_image( $data['img'] );
		$coupon->set_start_date( '' );
		$coupon->set_end_date( $this->end_date() );
		$coupon->set_price( $data['price'] );
		$coupon->set_full_price( $data['price'] );
		$coupon->set_discount( '' );
		$coupon->set_saving( '' );
		$coupon->set_purchased( '' );
		$coupon->set_currency_code( '' );
		$coupon->set_category( $data['category'] );
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

	private function end_date() {
		return wp_date( 'Y-m-d', strtotime( '+7 days' ) );
	}
}
