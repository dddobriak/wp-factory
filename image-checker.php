<?php
/**
 * Image Checker
 *
 * @param $image_source
 *
 * @return int
 */

function image_checker( $image_source ): int {
	global $wpdb;

	$image = $wpdb->get_results(
		$wpdb->prepare(
			"select post_id from $wpdb->postmeta where meta_key = 'image_source' and meta_value = %s",
			$image_source
		)
	);

	return ! empty( $image ) ? $image[0]->post_id : 0;
}
