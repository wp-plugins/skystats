<?php

/**
 * SkyStats MailChimp API-related functions.
 *
 * @since 0.3.3
 *
 * @package SkyStats\API\MailChimp
 */

defined( 'ABSPATH' ) or exit();

require_once dirname( __FILE__ ) . '/cache.php';

/**
 * Return URL which allows a user to authenticate/authorize with MailChimp.
 *
 * @since 0.3.3
 *
 * @param string $redirect_url URL to redirect to on success/failure.
 *
 * @return string
 */
function skystats_api_mailchimp_get_authorization_url( $redirect_url ) {

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURL'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/mailchimp/authorize/' );

	return $url;
}

/**
 * Return URL which allows a user to deauthenticate/deauthorize with MailChimp.
 *
 * @since 0.3.3
 *
 * @param string $redirect_url URL to redirect to on success/failure.
 *
 * @return string
 */
function skystats_api_mailchimp_get_deauthorization_url( $redirect_url ) {

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURL'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/mailchimp/deauthorize/' );

	return $url;
}

/**
 * Called when a user deauthorizes MailChimp.
 *
 * Removes cached MailChimp data.
 *
 * @since 0.2.8
 */
function skystats_api_mailchimp_deauthorize() {
	skystats_api_cache_delete_name_like( 'skystats_cache_mailchimp' );
	wp_remote_get( add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/mailchimp/deauthorize/' ), array(
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
	) );
}

function skystats_api_mailchimp_get_status() {

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/mailchimp/getStatus/' );

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

	return $body;
}

/**
 * Returns data for the a view (mashboard or detail).
 *
 * @since 0.3.3
 *
 * @param string $view       'mashboard' or 'detail'.
 *
 * @param string $start_date End date as YYYY-MM-DD.
 *
 * @param string $end_date   End date as YYYY-MM-DD.
 *
 * @return array
 */
function skystats_api_mailchimp_get_view_data( $view, $start_date, $end_date) {

	$start_date_time = (string) strtotime( $start_date );
	$end_date_time = (string) strtotime( $end_date );

	if ( 'mashboard' === $view ) {
		$cache_name = "skystats_cache_mailchimp_mashboard_data_{$start_date_time}__{$end_date_time}";
	} else {
		$cache_name = "skystats_cache_mailchimp_detail_data_{$start_date_time}__{$end_date_time}";
	}

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	if ( 'mashboard' === $view ) {
		$api_url = SKYSTATS_API_URL . 'api/mailchimp/getMashboardViewData/';
	} else {
		$api_url = SKYSTATS_API_URL . 'api/mailchimp/getDetailViewData/';
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'startDate'     => $start_date,
		'endDate'       => $end_date,
	), $api_url );

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