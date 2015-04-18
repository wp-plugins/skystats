<?php

/**
 * SkyStats Google Analytics API-related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\API\Google_Analytics
 */

defined( 'ABSPATH' ) or exit();

require_once dirname( __FILE__ ) . '/cache.php';

/**
 * Delete local GA cache.
 * 
 * @since 1.0.0
 */
function skystats_api_google_analytics_delete_local_cache() {
	skystats_api_cache_delete_name_like( 'skystats_cache_google_analytics' );
}

/**
 * Delete cached GA profiles.
 *
 * @since 0.0.1
 */
function skystats_api_google_analytics_delete_profiles() {
	skystats_api_cache_delete( 'skystats_cache_google_analytics_profiles' );
}

/**
 * Delete cached GA profile.
 * 
 * @since 1.0.0
 */
function skystats_api_google_analytics_delete_profile() {
	
	delete_option( 'skystats_selected_google_analytics_profile_id' );
}

/**
 * Return URL which allows a user to authenticate/authorize with Google Analytics.
 *
 * @since 0.1.4
 *
 * @param string $redirect_url URL to redirect to on success/failure.
 *
 * @return string
 */
function skystats_api_google_analytics_get_authorization_url( $redirect_url ) {

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURI'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/ga/authenticate/' );

	return $url;
}

/**
 * Return URL which allows a user to deauthenticate/deauthorize with Google Analytics.
 *
 * @since 0.1.4
 *
 * @param string $redirect_url URL to redirect to on success/failure.
 *
 * @return string
 */
function skystats_api_google_analytics_get_deauthorization_url( $redirect_url ) {

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURI'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/ga/deauthorize/' );

	return $url;
}

/**
 * Return absolute URL used to fetch Google Analytics Profiles.
 * 
 * @since 1.0.0
 * 
 * @param string|null $license_key (Optional) License key. Defaults to license key persisted in the database.
 * 
 * @return string
 */
function skystats_get_google_analytics_profiles_url( $license_key = null ) {
	
	$license_key = ( null !== $license_key ) ? $license_key : get_option( 'skystats_license_key' );

	$url = add_query_arg( array(
		'license_key'    => $license_key,
		'license_domain' => home_url(),
	), SKYSTATS_API_URL . 'api/ga/profiles/' );

	return $url;
}

/**
 * Return Google Analytics top data for a specific type.
 * 
 * @since 1.0.0
 * 
 * @param string $data_type 'keywords', 'search_engine_referrals', 'landing_pages', or 'visitor_locations'.
 * 
 * @param string $start_date
 * 
 * @param string $end_date
 * 
 * @return bool|array Boolean false on error, otherwise array of results.
 */
function skystats_get_google_analytics_top_data( $data_type, $start_date, $end_date ) {

	if ( ! in_array( $data_type, array( 'keywords', 'search_engine_referrals', 'landing_pages', 'visitor_locations', true ) ) ) {
		return false;
	}

	switch ( $data_type ) {
		case 'keywords':
			$path = 'api/ga/getTopKeywords';
			break;
		case 'search_engine_referrals':
			$path = 'api/ga/getTopSearchEngineReferrals';
			break;
		case 'landing_pages':
			$path = 'api/ga/getTopLandingPages';
			break;
		case 'visitor_locations':
			$path = 'api/ga/getTopVisitorLocations';
			break;
		default:
			return false;
	}

	require_once SKYSTATS_FUNCTIONS_PATH . 'sanitization.php';

	$profile_id = get_option( 'skystats_selected_google_analytics_profile_id' );
	if ( ! ctype_digit( $profile_id ) ) {
		$profile_id = null;
	}

	$start_date_time = (string) strtotime($start_date);
	$end_date_time = (string) strtotime($end_date);

	$cache_name = "skystats_cache_google_analytics_top_data_{$start_date_time}__{$end_date_time}_{$data_type}_{$profile_id}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'profileID'     => $profile_id,
		'startDate'     => $start_date,
		'endDate'       => $end_date,
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . $path );

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
 * Return JSON encoded data of a profiles request.
 *
 * @since 0.1.4
 * 
 * @param string $request_type 'fresh' or 'cached'.
 *
 * @return array JSON encoded data.
 */
function skystats_api_google_analytics_get_profiles( $request_type ) {

	$cache_name = 'skystats_cache_google_analytics_profiles';

	if ( ( 'cached' === $request_type ) && ( false !== ( $data = skystats_api_cache_get( $cache_name ) ) ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/ga/profiles/' );

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
 * Return JSON encoded data for the mashboard view.
 *
 * @since 0.1.4
 *
 * @return array Mashboard view request data response.
 */
function skystats_api_google_analytics_get_mashboard_view_data() {

	$profile_id = get_option( 'skystats_selected_google_analytics_profile_id' );

	$start_date_time = (string) strtotime( $_GET['start_date'] );
	$end_date_time = (string) strtotime( $_GET['end_date'] );

	$cache_name = "skystats_cache_google_analytics_mashboard_data_{$start_date_time}__{$end_date_time}_{$profile_id}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'profileID'     => $profile_id,
		'startDate'     => date( 'Y-m-d', $start_date_time ),
		'endDate'       => date( 'Y-m-d', $end_date_time ),
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/ga/getMashboardViewData/' );

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
 * Return JSON encoded data for the detail view.
 *
 * @since 0.1.4
 *
 * @return array Detail view request data response.
 */
function skystats_api_google_analytics_get_detail_view_data() {

	$profile_id = get_option( 'skystats_selected_google_analytics_profile_id' );

	$start_date_time = (string) strtotime( $_GET['start_date'] );
	$end_date_time = (string) strtotime( $_GET['end_date'] );
	$frequency = isset( $_GET['frequency'] ) && 'monthly' === $_GET['frequency'] ? 'monthly' : 'daily';

	$cache_name = "skystats_cache_google_analytics_detail_data_{$start_date_time}__{$end_date_time}_{$profile_id}_{$frequency}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'profileID'     => $profile_id,
		'startDate'     => date( 'Y-m-d', $start_date_time ),
		'endDate'       => date( 'Y-m-d', $end_date_time ),
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'frequency'     => $frequency,
	), SKYSTATS_API_URL . 'api/ga/getDetailViewData/' );

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