<?php

/**
 * SkyStats Facebook AJAX-related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\Ajax\Facebook
 */

defined( 'ABSPATH' ) or exit();

/**
 * Include Facebook API-related functions for AJAX calls.
 * 
 * @since 1.0.0
 */
require_once SKYSTATS_API_FUNCTIONS_PATH . 'facebook.php';

add_action( 'wp_ajax_skystats_ajax_facebook_authenticate', 'skystats_ajax_facebook_authenticate' );

/**
 * Redirect client to Facebook to (re)authorize/(re)authenticate.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_authenticate() {

	skystats_api_facebook_delete_pages();

	skystats_api_facebook_delete_cached_page_id();

	exit();
}

add_action( 'wp_ajax_skystats_ajax_facebook_authorize', 'skystats_ajax_facebook_authorize' );
/**
 * Authorize with Facebook.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_authorize() {

	skystats_api_facebook_delete_pages();

	skystats_api_facebook_delete_cached_page_id();

	exit();
}

add_action( 'wp_ajax_skystats_ajax_facebook_reauthorize', 'skystats_ajax_facebook_reauthorize' );
/**
 * Reauthorize with Facebook.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_reauthorize() {

	skystats_api_facebook_delete_pages();

	skystats_api_facebook_delete_cached_page_id();

	exit();
}

add_action( 'wp_ajax_skystats_ajax_facebook_deauthorize', 'skystats_ajax_facebook_deauthorize' );
/**
 * Deauthorize with Facebook.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_deauthorize() {

	skystats_api_facebook_delete_pages();

	skystats_api_facebook_delete_cached_page_id();

	skystats_api_facebook_delete_local_cache();

	exit();
}


add_action( 'wp_ajax_skystats_ajax_facebook_get_page_id', 'skystats_ajax_facebook_get_page_id' );

/**
 * Output JSON page ID used for collecting Facebook data.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_get_page_id() {

	$page_id = get_option( 'skystats_selected_facebook_page_id' );

	echo json_encode( $page_id );
	exit();
}

add_action( 'wp_ajax_skystats_ajax_facebook_save_page_id', 'skystats_ajax_facebook_save_page_id' );

/**
 * Save page ID used for collecting Facebook data.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_save_page_id() {

	if ( ! isset( $_POST['page_id'] ) ) {
		exit();
	}

	$page_id = $_POST['page_id'];

	if ( ! ctype_digit( $page_id ) ) {
		exit;
	}

	update_option( 'skystats_selected_facebook_page_id', $page_id );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_facebook_get_page_list', 'skystats_ajax_facebook_get_page_list' );

/**
 * Output JSON page list.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_get_page_list() {

	$request_type = isset( $_GET['request_type'] ) && 'cached' === $_GET['request_type'] ? 'cached' : 'fresh';

	$data = skystats_facebook_get_page_list( $request_type );
		
	echo json_encode( $data );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_facebook_get_mashboard_view_data', 'skystats_ajax_facebook_get_mashboard_view_data' );

/**
 * Output JSON Mashboard View data.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_get_mashboard_view_data() {

	$start_date = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
	$end_date   = isset( $_GET['end_date'] )  ? $_GET['end_date'] : '';
	$use_cache  = isset( $_GET['use_cache'] ) && true === $_GET['use_cache'] ? true : false;

	$data = skystats_facebook_get_mashboard_view_data( $start_date, $end_date, $use_cache );

	echo json_encode( $data );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_facebook_get_detail_view_data', 'skystats_ajax_facebook_get_detail_view_data' );
/**
 * Output JSON detail view data.
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_get_detail_view_data() {
	
	$start_date = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
	$end_date   = isset( $_GET['end_date'] )  ? $_GET['end_date'] : '';
	$use_cache  = isset( $_GET['use_cache'] ) && true === $_GET['use_cache'] ? true : false;

	$data = skystats_facebook_get_detail_view_data( $start_date, $end_date, $use_cache );

	echo json_encode( $data );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_facebook_get_page_top_posts', 'skystats_ajax_facebook_get_page_top_posts' );
/**
 * Output JSON data of page's top post(s).
 * 
 * @since 1.0.0
 */
function skystats_ajax_facebook_get_page_top_posts() {

	$start_date = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
	$end_date   = isset( $_GET['end_date'] )  ? $_GET['end_date'] : '';
	$use_cache  = isset( $_GET['use_cache'] ) ? $_GET['use_cache'] : false;

	$data = skystats_facebook_get_page_top_posts( $start_date, $end_date, $use_cache );

	echo json_encode( $data );

	exit();
}