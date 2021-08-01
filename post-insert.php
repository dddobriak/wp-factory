<?php
/**
 * Poist insert
 *
 * @param $object
 *
 * @return false
 */

function post_insert( $object ) {
	global $latest_coupons, $default_category;

	if ( ! date_checker( $object->get_end_date() ) ) {
		return false;
	}

	$post_checker = post_checker( $object->get_uid(), $object->get_hash() );

	// If uid exists - do nothing
	if ( isset( $post_checker['uid'] ) ) {
		return false;
	}

	$array = array(
		'post_title'   => $object->get_title(),
		'post_content' => '',
		'post_date'    => $object->get_start_date(),
		'post_author'  => 1,
		'post_type'    => 'coupon',
		'post_status'  => 'publish',
	);

	// If update_post exists - get post_id from it and update
	if ( isset( $post_checker['update_post'] ) ) {
		$array['ID'] = (int) $post_checker['update_post'];
		remove_action( 'save_post', 'daylid_save_post' );
		$post_id = wp_update_post( $array );
		add_action( 'save_post', 'daylid_save_post' );
	} else {
		$post_id = wp_insert_post( $array );
		wp_set_object_terms( $post_id, $default_category, 'category' );

		// Count the latest new coupouns and miss updated
		$latest_coupons[ $object->get_source() ][] = $post_id;
	}

	update_field( 'uid', $object->get_uid(), $post_id );
	update_field( 'source', $object->get_source(), $post_id );
	update_field( 'url', $object->get_url(), $post_id );
	update_field( 'image', $object->get_image(), $post_id );
	update_field( 'start_date', $object->get_start_date(), $post_id );
	update_field( 'end_date', $object->get_end_date(), $post_id );
	update_field( 'price', $object->get_price(), $post_id );
	update_field( 'full_price', $object->get_full_price(), $post_id );
	update_field( 'discount', $object->get_discount(), $post_id );
	update_field( 'saving', $object->get_saving(), $post_id );
	update_field( 'purchased', $object->get_purchased(), $post_id );
	update_field( 'currency_code', $object->get_currency_code(), $post_id );
	update_field( 'location', $object->get_location(), $post_id );
	update_field( 'category', $object->get_category(), $post_id );
	update_field( 'rating', '', $post_id );
	update_field( 'hash', $object->get_hash(), $post_id );

	// Upload image or set relationship if it already exists
	if ( $object->get_image() ) {
		$attach_id = image_checker( $object->get_image() );
		if ( $attach_id ) {
			set_post_thumbnail( $post_id, $attach_id );
		} else {
			image_loader( $object->get_image(), $post_id );
		}
	}
}

