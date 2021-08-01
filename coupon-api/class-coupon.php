<?php

class Coupon {
	private $uid;
	private $source;
	private $title;
	private $url;
	private $image;
	private $start_date;
	private $end_date;
	private $price;
	private $full_price;
	private $discount;
	private $saving;
	private $purchased;
	private $currency_code;
	private $category;
	private $location;
	private $hash;

	/**
	 * @return mixed
	 */
	public function get_uid() {
		return $this->uid;
	}

	/**
	 * @param mixed $uid
	 */
	public function set_uid( $uid ): void {
		$this->uid = $uid;
	}

	/**
	 * @return mixed
	 */
	public function get_source() {
		return $this->source;
	}

	/**
	 * @param mixed $source
	 */
	public function set_source( $source ): void {
		$this->source = $source;
	}

	/**
	 * @return mixed
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @param mixed $title
	 */
	public function set_title( $title ): void {
		$this->title = $title;
	}

	/**
	 * @return mixed
	 */
	public function get_url() {
		return $this->url;
	}

	/**
	 * @param mixed $url
	 */
	public function set_url( $url ): void {
		$this->url = $url;
	}

	/**
	 * @return mixed
	 */
	public function get_image() {
		return $this->image;
	}

	/**
	 * @param mixed $image
	 */
	public function set_image( $image ): void {
		$this->image = $image;
	}

	/**
	 * @return mixed
	 */
	public function get_start_date() {
		return $this->start_date;
	}

	/**
	 * @param mixed $start_date
	 */
	public function set_start_date( $start_date ): void {
		$this->start_date = empty( $start_date ) ? wp_date( 'Y-m-d H:i:s' ) : $start_date;
	}

	/**
	 * @return mixed
	 */
	public function get_end_date() {
		return $this->end_date;
	}

	/**
	 * @param mixed $end_date
	 */
	public function set_end_date( $end_date ): void {
		$this->end_date = $end_date;
	}

	/**
	 * @return mixed
	 */
	public function get_price() {
		return $this->price;
	}

	/**
	 * @param mixed $price
	 */
	public function set_price( $price ): void {
		$this->price = $price;
	}

	/**
	 * @return mixed
	 */
	public function get_full_price() {
		return $this->full_price;
	}

	/**
	 * @param mixed $full_price
	 */
	public function set_full_price( $full_price ): void {
		$this->full_price = $full_price;
	}

	/**
	 * @return mixed
	 */
	public function get_discount() {
		return $this->discount;
	}

	/**
	 * @param mixed $discount
	 */
	public function set_discount( $discount ): void {
		$this->discount = $discount;
	}

	/**
	 * @return mixed
	 */
	public function get_saving() {
		return $this->saving;
	}

	/**
	 * @param mixed $saving
	 */
	public function set_saving( $saving ): void {
		$this->saving = $saving;
	}


	/**
	 * @return mixed
	 */
	public function get_purchased() {
		return $this->purchased;
	}

	/**
	 * @param mixed $purchased
	 */
	public function set_purchased( $purchased ): void {
		$this->purchased = $purchased;
	}

	/**
	 * @return mixed
	 */
	public function get_currency_code() {
		return $this->currency_code;
	}

	/**
	 * @param mixed $currency_code
	 */
	public function set_currency_code( $currency_code ): void {
		$this->currency_code = $currency_code;
	}

	/**
	 * @return mixed
	 */
	public function get_category() {
		return $this->category;
	}

	/**
	 * @param mixed $category
	 */
	public function set_category( $category ): void {
		$this->category = replace_spaces( $category );
	}

	/**
	 * @return mixed
	 */
	public function get_location() {
		return $this->location;
	}

	/**
	 * @param mixed $location
	 */
	public function set_location( $location ): void {
		$this->location = replace_spaces( $location );
	}

	/**
	 * @return mixed
	 */
	public function get_hash() {
		return $this->hash;
	}

	/**
	 * @param mixed
	 */
	public function set_hash(): void {
		$array = get_object_vars( $this );
		unset( $array['start_date'] );

		$array = array_map(
			function ( $e ) {
				return empty( $e ) ? '' : $e;
			},
			$array
		);

		$this->hash = md5(
			array_reduce(
				$array,
				function ( $a, $b ) {
					$a .= $b;

					return $a;
				}
			)
		);
	}
}
