<?php
/**
 * Image loader
 *
 * @param $url
 * @param $post_id
 */

// Gives us access to the download_url() and wp_handle_sideload() functions
require ABSPATH . 'wp-admin/includes/file.php';

function image_loader( $url, $post_id = false ) {
	$timeout_seconds = 60;

	// Download file to temp dir
	$temp_file = image_response_checker( $url, $timeout_seconds );

	if ( $temp_file ) {


		// Array based on $_FILE as seen in PHP file uploads
		$file = array(
			'name'     => basename( $url ), // ex: wp-header-logo.png
			'type'     => wp_check_filetype( $url )['ext'],
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		$overrides = array(
			// Tells WordPress to not look for the POST form
			// fields that would normally be present as
			// we downloaded the file from a remote server, so there
			// will be no form fields
			// Default is true
			'test_form' => false,

			// Setting this to false lets WordPress allow empty files, not recommended
			// Default is true
			'test_size' => true,
		);

		// Move the temporary file into the uploads directory
		$results = wp_handle_sideload( $file, $overrides );

		// I don't know what should I do here
		if ( empty( $results['error'] ) ) {
			$filename  = $results['file']; // Full path to the file
			$local_url = $results['url'];  // URL to the file in the uploads dir
			$type      = $results['type']; // MIME type of the file

			if ( ! empty( $post_id ) ) {
				$image_source = $url;
				image_attacher( $filename, $post_id, $image_source );
			}
		}
	}
}
