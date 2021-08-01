<?php

class Factory {
	public static function create( $name ): ?ICoupon {
		if ( class_exists( $name ) ) {
			$api = new $name();
			if ( ! empty( $api->get_coupons() ) ) {
				return $api;
			}
		}
		return null;
	}
}
