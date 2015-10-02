<?php

/**
 * SkyStats Twitter API-related functions.
 * 
 * @since 0.2.5
 *
 * @package SkyStats\API\Twitter
 */

defined( 'ABSPATH' ) or exit();

/**
 * API cache related functions.
 * 
 * @since 0.2.5
 */
require_once dirname( __FILE__ ) . '/cache.php';

/**
 * Return the status of the Twitter API / current authenticated user to determine what to do next.
 * 
 * @since 0.2.5
 * 
 * @return array Key => value pair with a result (if any) and a result context
 *               to provide context for the result.
 */
function skystats_api_twitter_get_status() {

	$cache_name = 'skystats_cache_twitter_status';

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/twitter/getStatus/' );

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
 * Return data for the Mashboard view.
 * 
 * @since 0.2.5
 *
 * @param string $start_date Start date as 'YYYY-MM-DD'.
 *
 * @param string $end_date   End date as 'YYYY-MM-DD'.
 * 
 * @return array Key => value pair with a result (if any) and a result context to
 *               provide context for the result.
 */
function skystats_api_twitter_get_mashboard_view_data( $start_date, $end_date ) {

	$start_date_time = (string) strtotime( $start_date );
	$end_date_time = (string) strtotime( $end_date );

	$cache_name = "skystats_cache_twitter_mashboard_data_{$start_date_time}__{$end_date_time}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'startDate'     => $start_date,
		'endDate'       => $end_date,
	), SKYSTATS_API_URL . 'api/twitter/getMashboardViewData/' );

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
 * Return data for the Detail view.
 * 
 * @since 0.2.5
 *
 * @param string $start_date Start date as 'YYYY-MM-DD'.
 *
 * @param string $end_date   End date as 'YYYY-MM-DD'.
 * 
 * @return array Key => value pair with a result (if any) and a result context
 *               to provide context for the result.
 */
function skystats_api_twitter_get_detail_view_data( $start_date, $end_date ) {

	$start_date_time = (string) strtotime( $start_date );
	$end_date_time = (string) strtotime( $end_date );

	$cache_name = "skystats_cache_twitter_detail_data_{$start_date_time}__{$end_date_time}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'startDate'     => $start_date,
		'endDate'       => $end_date,
	), SKYSTATS_API_URL . 'api/twitter/getDetailViewData/' );

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
 * Return authorization/authentication URL.
 * 
 * @since 0.2.5
 *
 * @param string $redirect_url Absolute URL to redirect to after authorizing.
 * 
 * @return string
 */
function skystats_api_twitter_get_authorization_url( $redirect_url ) {
	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURI'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/twitter/authenticate/' );

	return $url;
}

/**
 * Return deauthorization/deauthentication URL.
 * 
 * @since 0.2.5
 *
 * @param string $redirect_url Absolute URL to redirect to after deauthorizing.
 * 
 * @return string
 */
function skystats_api_twitter_get_deauthorization_url( $redirect_url ) {
	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURI'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/twitter/deauthorize/' );

	return $url;
}

/**
 * When user deauthorizes the Twitter integration.
 *
 * Delete all local cached data.
 *
 * @since 0.2.5
 */
function skystats_api_twitter_deauthorize() {
	skystats_api_cache_delete_name_like( 'skystats_cache_twitter' );

	wp_remote_get( add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/twitter/deauthorize/' ), array(
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
	) );

	exit();
}