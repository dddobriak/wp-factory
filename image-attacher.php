<?php
/**
 * Image attacher
 *
 * @param $local_url
 * @param $post_id
 */

// Required to work with wp_generate_attachment_metadata
require ABSPATH . 'wp-admin/includes/image.php';

function image_attacher( $local_url, $post_id, $image_source ) {
	// Retrieve the file type from the file name.
	$filetype = wp_check_filetype( $local_url );

	// Prepare an array of post data for the attachment.
	$attachment = array(
		'post_mime_type' => $filetype['type'],
		'post_title'     => sanitize_file_name( $local_url ),
		'post_content'   => '',
		'post_status'    => 'inherit',
	);

	// Insert an attachment.
	$attach_id = wp_insert_attachment( $attachment, $local_url, $post_id );

	// Generate attachment meta data and create image sub-sizes for images.
	$attach_data = wp_generate_attachment_metadata( $attach_id, $local_url );

	// Updates metadata for an attachment.
	wp_update_attachment_metadata( $attach_id, $attach_data );

	// Update image source
	update_field( 'image_source', $image_source, $attach_id );

	// Sets the post thumbnail (featured image) for the given post.
	set_post_thumbnail( $post_id, $attach_id );
}
