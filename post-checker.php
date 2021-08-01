<?php
function get_posts_by_api( $api ) {
	global $wpdb;

	$post_checker = $wpdb->get_results(
		$wpdb->prepare(
			"select post_id, group_concat(meta_value order by meta_key separator ',') as meta_value from $wpdb->postmeta where post_id in (select post_id from $wpdb->postmeta where meta_key = 'source' and meta_value = %s) and meta_key in('uid','hash') group by post_id",
			$api
		)
	);

	// convert meta_value string to array
	$post_checker = array_map(
		function ( $a ) {
			$a->meta_value = explode( ',', $a->meta_value );

			return $a;
		},
		$post_checker
	);

	/**
	 * get_posts_cache_by_api
	 * element
	 * -- post_id
	 * -- meta_value[0] (hash)
	 * -- meta_value[1] (uid)
	 */
	return $post_checker;
}

function post_checker( $uid, $hash ): ?array {
	$posts_by_api = get_option( 'posts_by_api' );
	$posts_by_api = $posts_by_api ? json_decode( $posts_by_api ) : [];
	$result       = [];

	foreach ( $posts_by_api as $post ) {
		if ( (int) $post->meta_value[1] === (int) $uid ) {
			if ( $post->meta_value[0] !== $hash ) {

				// return post_id to update
				$result['update_post'] = $post->post_id;
				return $result;
			}

			// return uid, if coupon exists
			$result['uid'] = $uid;
			return $result;
		}
	}

	return $result;
}

function date_checker( $date ) {
	$coupon_date  = new DateTime( $date );
	$current_date = new DateTime( wp_date( 'Y-m-d' ) );
	if ( $current_date <= $coupon_date ) {
		return true;
	}

	return false;
}
