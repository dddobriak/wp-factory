<?php
/**
 * Init coupon post type
 */

function coupon() {
	register_post_type(
		'coupon',
		array(
			'labels'             => array(
				'name'               => 'Coupons',
				'singular_name'      => 'Coupon',
				'add_new'            => 'Add coupon',
				'add_new_item'       => 'Add new coupon',
				'edit_item'          => 'Edit coupon',
				'new_item'           => 'New coupon',
				'view_item'          => 'View coupon',
				'search_items'       => 'Search coupon',
				'not_found'          => 'Coupon not found',
				'not_found_in_trash' => 'Not found in trash',
				'parent_item_colon'  => '',
				'menu_name'          => 'Coupons',
			),

			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'taxonomies'         => array( 'category' ),
			'supports'           => array(
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'comments',
				'custom-fields',
			),
		)
	);

	//Registering location taxonomy for coupon
	$labels = array(
		'name'              => 'Locations',
		'singular_name'     => 'Location',
		'search_items'      => 'Search locations',
		'all_items'         => 'All locations',
		'parent_item'       => 'Parent location',
		'parent_item_colon' => 'Parent location:',
		'edit_item'         => 'Edit location',
		'update_item'       => 'Update location',
		'add_new_item'      => 'Add new location',
		'new_item_name'     => 'New location name',
		'menu_name'         => 'Location',
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'location' ),
	);

	register_taxonomy( 'location', array( 'coupon' ), $args );
}

add_action( 'init', 'coupon' );
