<?php
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );

remove_action( 'wp_head', 'wp_shortlink_wp_head' ); //removes shortlink.
remove_action( 'wp_head', 'feed_links', 2 ); //removes feed links.
remove_action( 'wp_head', 'feed_links_extra', 3 );  //removes comments feed.
remove_action( 'wp_head', 'profile_link' );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );

remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'wp_resource_hints', 2 );

/**
 * WordPress REST
 */
//add_filter( 'rest_enabled', '__return_false' );
//remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
//remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
//remove_action( 'template_redirect', 'rest_output_link_header', 11 );
//remove_action( 'auth_cookie_malformed', 'rest_cookie_collect_status' );
//remove_action( 'auth_cookie_expired', 'rest_cookie_collect_status' );
//remove_action( 'auth_cookie_bad_username', 'rest_cookie_collect_status' );
//remove_action( 'auth_cookie_bad_hash', 'rest_cookie_collect_status' );
//remove_action( 'auth_cookie_valid', 'rest_cookie_collect_status' );
//remove_filter( 'rest_authentication_errors', 'rest_cookie_check_errors', 100 );
//remove_action( 'init', 'rest_api_init' );
//remove_action( 'rest_api_init', 'rest_api_default_filters', 10 );
//remove_action( 'parse_request', 'rest_api_loaded' );
//remove_action( 'rest_api_init', 'wp_oembed_register_route' );
//remove_filter( 'rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10 );
//remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
//remove_action( 'wp_head', 'wp_oembed_add_host_js' );
