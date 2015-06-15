<?php

/**
 * SkyStats Google Analytics AJAX-related functions.
 *
 * @since 1.0.0
 *
 * @package SkyStats\Ajax\Google_Analytics
 */

defined( 'ABSPATH' ) or exit();

/**
 * Google Analytics API related functions.
 *
 * @since 1.0.0
 */
require_once SKYSTATS_API_FUNCTIONS_PATH . 'google-analytics.php';

add_action( 'wp_ajax_skystats_ajax_google_analytics_authorize', 'skystats_ajax_google_analytics_authorize' );
/**
 * When a user authorizes with Google Analytics.
 *
 * @since 1.0.0
 */
function skystats_ajax_google_analytics_authorize() {

	skystats_api_google_analytics_delete_profile();

	skystats_api_google_analytics_delete_profiles();

	exit();
}

add_action( 'wp_ajax_skystats_ajax_google_analytics_reauthorize', 'skystats_ajax_google_analytics_reauthorize' );
/**
 * When a user reauthorizes with Google Analytics.
 *
 * @since 1.0.0
 */
function skystats_ajax_google_analytics_reauthorize() {

	skystats_api_google_analytics_delete_profile();

	skystats_api_google_analytics_delete_profiles();

	exit();
}

add_action( 'wp_ajax_skystats_ajax_google_analytics_deauthorize', 'skystats_ajax_google_analytics_deauthorize' );
/**
 * When a user deauthorizes with Google Analytics.
 *
 * @since 1.0.0
 */
function skystats_ajax_google_analytics_deauthorize() {

	skystats_api_google_analytics_delete_profile();

	skystats_api_google_analytics_delete_profiles();

	skystats_api_google_analytics_delete_local_cache();

	exit();
}

add_action( 'wp_ajax_skystats_ajax_google_analytics_save_profile_id', 'skystats_ajax_google_analytics_save_profile_id' );
/**
 * Save Google Analytics Profile ID.
 *
 * @since 1.0.0
 */
function skystats_ajax_google_analytics_save_profile_id() {

	if ( ! isset( $_POST['profile_id'] ) ) {
		exit();
	}

	$profile_id = $_POST['profile_id'];

	if ( ! ctype_digit( $profile_id ) ) {
		exit();
	}

	update_option( 'skystats_selected_google_analytics_profile_id', $profile_id );
	exit();
}

add_action( 'wp_ajax_skystats_ajax_get_google_analytics_mashboard_data', 'skystats_ajax_get_google_analytics_mashboard_data' );
/**
 * Output Google Analytics JSON data for Mashboard view.
 *
 * @since 1.0.0
 */
function skystats_ajax_get_google_analytics_mashboard_data() {

	$start_date = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
	$end_date   = isset( $_GET['end_date'] ) ? $_GET['end_date'] : '';

	$data = skystats_get_google_analytics_mashboard_data( $start_date, $end_date );

	echo json_encode( $data );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_get_google_analytics_detail_view_data', 'skystats_ajax_get_google_analytics_detail_view_data' );
/**
 * Output Google Analytics JSON data for Detail view chart and data points.
 *
 * @since 1.0.0
 */
function skystats_ajax_get_google_analytics_detail_view_data() {

	$start_date = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
	$end_date   = isset( $_GET['end_date'] ) ? $_GET['end_date'] : '';
	$frequency  = isset( $_GET['frequency'] ) && 'monthly' === $_GET['frequency'] ? 'monthly' : 'daily';

	$data = skystats_get_google_analytics_detail_view_data( $start_date, $end_date, $frequency );

	echo json_encode( $data );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_get_google_analytics_top_data', 'skystats_ajax_get_google_analytics_top_data' );
/**
 * Output Google Analytics top data for a specific data type.
 *
 * @since 1.0.0
 */
function skystats_ajax_get_google_analytics_top_data() {

	$data_type  = isset( $_GET['data_type'] ) ? $_GET['data_type'] : '';
	$start_date = isset( $_GET['start_date'] ) ? $_GET['start_date'] : '';
	$end_date   = isset( $_GET['end_date'] ) ? $_GET['end_date'] : '';

	$data = skystats_get_google_analytics_top_data( $data_type, $start_date, $end_date );

	echo json_encode( $data );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_google_analytics_api_query', 'skystats_ajax_google_analytics_api_query' );
/**
 * All Google Analytics AJAX requests are sent through this function.
 *
 * @since 0.1.4
 */
function skystats_ajax_google_analytics_api_query() {

	if ( ! isset( $_GET['query'] ) ) {
		exit();
	}

	$query = wp_strip_all_tags( $_GET['query'] );

	switch ( $query ) {
		case 'get_mashboard_view_data':
			$mashboard_data = skystats_api_google_analytics_get_mashboard_view_data();
			echo json_encode( $mashboard_data );
			break;
		case 'get_profiles':
			$request_type = isset( $_GET['request_type'] ) && 'cached' === $_GET['request_type'] ? 'cached' : 'fresh';
			$profiles = skystats_api_google_analytics_get_profiles( $request_type );
			echo json_encode( $profiles );
			break;
		case 'get_detail_view_data':
			$detail_data = skystats_api_google_analytics_get_detail_view_data();
			echo json_encode( $detail_data );
			break;
		default:
			break;
	}

	exit();
}