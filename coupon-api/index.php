<?php
/**
 * Path to coupon api
 */
define( 'API_PATH', get_template_directory() . '/inc/coupon-api/' );
define( 'API_ARRAY', [ 'Baligam', 'BigDeal', 'Couponi', 'Groo', 'Idgu', 'Isrotel', 'KSP' ] );

/**
 * Require interface
 */
require API_PATH . '/icoupon.php';

/**
 * Autoload classes with "class" prefix
 *
 * @param $class_name
 *
 * @return mixed
 */
function coupon_autoloader( $class_name ) {
	$class_file = API_PATH . 'class-' . strtolower( $class_name ) . '.php';
	if ( file_exists( $class_file ) ) {
		include $class_file;
	}

	return $class_name;
}

spl_autoload_register( 'coupon_autoloader' );

/**
 * Global api variables
 */
$api_control      = new ApiControl();
$coupon_loader    = new CouponLoader();
$model            = new Model();
$cron             = new Cron();
$coupon_cleaner   = new CouponCleaner();
$latest_coupons   = [];
$default_category = (int) get_option( 'default_category' );

function start_insert( $api ) {
	global $model;

	// Set time limit and memory limit
	set_time_limit( 0 );
	ini_set( 'memory_limit', '512M' );

	update_option( 'posts_by_api', wp_json_encode( get_posts_by_api( $api ) ), false );
	wp_suspend_cache_addition( true );

	$api = Factory::create( $api );

	if ( $api ) {
		$coupons = $api->get_coupons();
		$model->insert_coupons( $coupons );
	}

	update_option( 'posts_by_api', '', false );
	wp_suspend_cache_addition();
}

if ( isset( $_GET['runUpdate2'] ) ) {
	start_insert( 'Groo' );
}

if ( isset( $_GET['show'] ) ) {
	$api = Factory::create( 'Groo' );
	if ( $api ) {
		$coupons = $api->get_coupons();
	var_dump($coupons);
	}

}

if ( isset( $_GET['byID'] ) ) {
	update_option( 'posts_by_api', wp_json_encode( get_posts_by_api( 'Groo' ) ), false );
	var_dump( post_checker( 1033880, 'b50c9ac9d7b8245d49b74b3c2afd5ae1' ) );
	update_option( 'posts_by_api', '', false );
}
if ( isset( $_GET['cron_array'] ) ) {
	var_dump( _get_cron_array() );
}
if ( isset( $_GET['api_control'] ) ) {
	var_dump( $api_control->active_api() );
}
