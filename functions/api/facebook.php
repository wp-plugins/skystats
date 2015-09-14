<?php

/**
 * SkyStats Facebook API-related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\API\Facebook
 */

defined( 'ABSPATH' ) or exit();

require_once dirname( __FILE__ ) . '/cache.php';

/**
 * Delete cached Facebook pages.
 *
 * @since 0.0.1
 */
function skystats_api_facebook_delete_pages() {

	skystats_api_cache_delete( 'skystats_cache_facebook_pages' );
}

/**
 * Delete cached Facebook page Id.
 * 
 * @since 1.0.0
 */
function skystats_api_facebook_delete_cached_page_id() {

	delete_option( 'skystats_selected_facebook_page_id' );
}

/**
 * Delete local Facebook cache.
 * 
 * @since 1.0.0
 */
function skystats_api_facebook_delete_local_cache() {

	skystats_api_cache_delete_name_like( 'skystats_cache_facebook' );
}


/**
 * Return absolute URL to deauthorize with Facebook.
 * 
 * @since 1.0.0
 *
 * @param string $redirect_url URL to redirect to after success/failure.
 *
 * @return string
 */
function skystats_api_facebook_get_deauthorize_url( $redirect_url ) {
	return add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURI'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/fb/deauthorize/' );
}

/**
 * Return absolute URL to Facebook (re)authentication/(re)authorization.
 * 
 * @since 1.0.0
 *
 * @param string $redirect_url URL to redirect to after success/failure.
 *
 * @return string
 */
function skystats_facebook_get_authentication_url( $redirect_url ) {
	return add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURI'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/fb/authenticate/' );
}

/**
 * Return list of pages attached to clients account for selection.
 * 
 * @since 1.0.0
 * 
 * @param string $request_type 'fresh' or 'cached'.
 * 
 * @return array Page list request response data
 */
function skystats_facebook_get_page_list( $request_type ) {

	$cache_name = "skystats_cache_facebook_pages";

	if ( ( 'cached' === $request_type ) && ( false !== ( $data = skystats_api_cache_get( $cache_name ) ) ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/fb/getPageList/' );

	$response = wp_remote_get( $url, array( 
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
	) );

	$default_return_value = array( 'data' => null, 'responseType' => 'error', 'responseContext' => 'http_error' );

	if ( is_wp_error( $response ) ) {
		return $default_return_value;
	}

	if ( '' === $body = wp_remote_retrieve_body( $response ) ) {
		return $default_return_value;
	}

	$body = json_decode( $body, true );

	if ( ! is_array( $body ) ) {
		return $default_return_value;
	}

	if ( ! ( array_key_exists( 'data', $body ) && array_key_exists( 'responseType', $body ) && array_key_exists( 'responseContext', $body ) ) ) {
		return $default_return_value;
	}

	if ( isset( $body['responseType'] ) && 'success' === $body['responseType'] ) {
		skystats_api_cache_set( $cache_name, $body );
	}

	return $body;
}

/**
 * Return data for Mashboard view.
 * 
 * @since 1.0.0
 * 
 * @param string $start_date Beginning of period to collect data for in format YYYY-MM-DD.
 * 
 * @param string $end_date   End of period to collect data for in format YYYY-MM-DD.
 * 
 * @param bool   $use_cache  Whether to use the cache or not.
 * 
 * @return bool|array False on error, otherwise array of data.
 */
function skystats_facebook_get_mashboard_view_data( $start_date, $end_date, $use_cache = false ) {

	$page_id         = get_option( 'skystats_selected_facebook_page_id' );
	$start_date_time = strtotime( $start_date );
	$end_date_time   = strtotime( $end_date );
	$cache_name      = "skystats_cache_facebook_mashboard_data_{$page_id}_{$start_date_time}_{$end_date_time}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'pageID'        => $page_id,
		'startDate'     => $start_date,
		'endDate'       => $end_date,
		'useCache'      => $use_cache,
	), SKYSTATS_API_URL . 'api/fb/getMashboardViewData/' );

	$response = wp_remote_get( $url, array( 
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
	) );

	$default_return_value = array( 'data' => null, 'responseType' => 'error', 'responseContext' => 'http_error' );

	if ( is_wp_error( $response ) ) {
		return $default_return_value;
	}

	if ( '' === $body = wp_remote_retrieve_body( $response ) ) {
		return $default_return_value;
	}

	$body = json_decode( $body, true );

	if ( ! is_array( $body ) ) {
		return $default_return_value;
	}

	if ( ! ( array_key_exists( 'data', $body ) && array_key_exists( 'responseType', $body ) && array_key_exists( 'responseContext', $body ) ) ) {
		return $default_return_value;
	}

	if ( isset( $body['responseType'] ) && 'success' === $body['responseType'] ) {
		skystats_api_cache_set( $cache_name, $body );
	}

	return $body;
}

/**
 * Return data for detail view.
 * 
 * @since 1.0.0
 *
 * @param string $start_date Beginning of period to collect data for in format YYYY-MM-DD.
 * 
 * @param string $end_date   End of period to collect data for in format YYYY-MM-DD.
 * 
 * @param bool   $use_cache  Whether to use the cache or not.
 * 
 * @return bool|array False on error, otherwise array of data.
 */
function skystats_facebook_get_detail_view_data( $start_date, $end_date, $use_cache = false ) {

	$page_id         = get_option( 'skystats_selected_facebook_page_id' );
	if ( ! ctype_digit( $page_id ) ) {
		$page_id = null;
	}
	$start_date_time = strtotime( $start_date );
	$end_date_time   = strtotime( $end_date );
	$cache_name      = "skystats_cache_facebook_detail_data_{$page_id}_{$start_date_time}_{$end_date_time}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'pageID'        => $page_id,
		'startDate'     => $start_date,
		'endDate'       => $end_date,
		'useCache'      => $use_cache,
	), SKYSTATS_API_URL . 'api/fb/getDetailViewData/' );

	$response = wp_remote_get( $url, array( 
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
	) );

	$default_return_value = array( 'data' => null, 'responseType' => 'error', 'responseContext' => 'http_error' );

	if ( is_wp_error( $response ) ) {
		return $default_return_value;
	}

	if ( '' === $body = wp_remote_retrieve_body( $response ) ) {
		return $default_return_value;
	}

	$body = json_decode( $body, true );

	if ( ! is_array( $body ) ) {
		return $default_return_value;
	}

	if ( ! ( array_key_exists( 'data', $body ) && array_key_exists( 'responseType', $body ) && array_key_exists( 'responseContext', $body ) ) ) {
		return $default_return_value;
	}

	if ( isset( $body['responseType'] ) && 'success' === $body['responseType'] ) {
		skystats_api_cache_set( $cache_name, $body );
	}

	return $body;
}

/**
 * Return page's top post(s).
 * 
 * @since 1.0.0
 * 
 * @param string $start_date Beginning of period to collect data for in format YYYY-MM-DD.
 * 
 * @param string $end_date   End of period to collect data for in format YYYY-MM-DD.
 * 
 * @param bool   $use_cache  Whether to use the cache or not.
 * 
 * @return bool|array False on error, otherwise array of data.
 */
function skystats_facebook_get_page_top_posts( $start_date, $end_date, $use_cache = false ) {

	$page_id = get_option( 'skystats_facebook_page_id' );
	if ( ! ctype_digit( $page_id ) ) {
		$page_id = null;
	}
	$start_date_time = strtotime( $start_date );
	$end_date_time   = strtotime( $end_date );
	$cache_name      = "skystats_cache_facebook_top_posts_data_{$page_id}_{$start_date_time}_{$end_date_time}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'pageID'        => get_option( 'skystats_selected_facebook_page_id' ),
		'startDate'     => $start_date,
		'endDate'       => $end_date,
		'useCache'      => $use_cache,
	), SKYSTATS_API_URL . 'api/fb/getPageTopPosts/' );

	$response = wp_remote_get( $url, array( 
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
	) );

	$default_return_value = array( 'data' => null, 'responseType' => 'error', 'responseContext' => 'http_error' );

	if ( is_wp_error( $response ) ) {
		return $default_return_value;
	}

	if ( '' === $body = wp_remote_retrieve_body( $response ) ) {
		return $default_return_value;
	}

	$body = json_decode( $body, true );

	if ( ! is_array( $body ) ) {
		return $default_return_value;
	}

	if ( ! ( array_key_exists( 'data', $body ) && array_key_exists( 'responseType', $body ) && array_key_exists( 'responseContext', $body ) ) ) {
		return $default_return_value;
	}

	if ( isset( $body['responseType'] ) && 'success' === $body['responseType'] ) {
		skystats_api_cache_set( $cache_name, $body );
	}

	return $body;
}