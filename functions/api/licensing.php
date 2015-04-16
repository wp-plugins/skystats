<?php

/**
 * SkyStats Licensing API-related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\API\Licensing
 */

 defined( 'ABSPATH' ) or exit();

/**
 * Validate the currently used license key and activate it if it is inactive.
 * 
 * @since 1.0.0
 * 
 * @param null|string $license_key (Optional) License key to validate otherwise retrieves license key from database.
 * 
 * @return bool                    False if the license key could not be validated or isn't valid, otherwise true.
 */
function skystats_api_licensing_validate_license( $license_key = null ) {

	$license_key = ( null !== $license_key ) ? $license_key : get_option( 'skystats_license_key' );

	$url = add_query_arg( array( 
		'licenseKey'    => $license_key,
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/license/validate/' );

	$response = wp_remote_get( $url, array(
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
	) );

	$default_return_value = array( 'data' => null, 'resultType' => 'error', 'resultContext' => 'http_error' );

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

	return $body;
}

/**
 * Check the current license key and license domain.
 *
 * @since 0.1.4
 *
 * @return array The response containing the result (if any), the result type (success/error) and a context for the
 *               result, such as a useful identifier for uniquely identifying the type of response.
 */
function skystats_api_licensing_check_license() {

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/license/check/' );

	$response = wp_remote_get( $url, array(
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
	) );

	$default_return_value = array( 'data' => null, 'resultType' => 'error', 'resultContext' => 'http_error' );

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

	return $body;
}

/**
 * Return license status translations for a license check request.
 *
 * @since 0.1.4
 *
 * @param string $current_url The current URL used in a link to refresh the page.
 *
 * @return string[] License status translations
 */
function skystats_api_licensing_get_license_status_translations( $current_url ) {

	$errorOccurred = sprintf( __( 'Sorry, an error occurred. Please click <a href="%s">here</a> to refresh the page. Alternatively, click <a href="%s">here</a> to check you\'ve entered your license key correctly.', SKYSTATS_TEXT_DOMAIN ), $current_url, SKYSTATS_SETTINGS_PAGE_URL );

	$translations = array(
		'missing_license_key'                       => sprintf( __( 'A license key has not been entered yet. Please click <a href="%s">here</a> to enter your license key or check you\'ve entered your license key correctly.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_SETTINGS_PAGE_URL ),
		'malformed_license_key'                     => sprintf( __( 'License key format is invalid. Please click <a href="%s">here</a> to check you\'ve entered your license key correctly or to activate one.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_SETTINGS_PAGE_URL ),
		'http_error'                                => sprintf( __( 'Sorry, an error occurred. This was likely caused by a timeout. Please click <a href="%s">here</a> to refresh the page. Alternatively, click <a href="%s">here</a> to check you\'ve entered your license key correctly', SKYSTATS_TEXT_DOMAIN ), $current_url, SKYSTATS_SETTINGS_PAGE_URL ),
		'error_initializing_request'                => $errorOccurred,
		'error_executing_request'                   => $errorOccurred,
		'malformed_response'                        => $errorOccurred,
		'license_site_inactive'                     => sprintf( __( 'This license key has not been activated for this site yet. Please click <a href="%s">here</a> to go to your settings to activate it.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_SETTINGS_PAGE_URL ),
		'license_site_inactive_no_activations_left' => sprintf( __( 'Sorry, this license key hasn\'t been activated for this site yet, however you have no remaining activations left. Please click <a href="%s">here</a> to check you\'ve entered your license key correctly or to enter a new one.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_SETTINGS_PAGE_URL ),
		'license_inactive'                          => sprintf( __( 'This license key has not been activated yet. Please click <a href="%s">here</a> to go to your settings to activate it.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_SETTINGS_PAGE_URL ),
		'license_inactive_no_activations_left'      => sprintf( __( 'Sorry, this license key hasn\'t been activated yet, however you have no remaining activations left. Please click <a href="%s">here</a> to check you\'ve entered your license key correctly or to enter a new one.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_SETTINGS_PAGE_URL ),
		'license_disabled'                          => sprintf( __( 'Sorry, this license key has been disabled. Please click <a href="%s">here</a> to check you\'ve entered your license key correctly.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_SETTINGS_PAGE_URL ),
		'license_unknown_status'                    => sprintf( __( 'Sorry, an unknown error occurred. Please click <a href="%s">here</a> to refresh the page. Alternatively, click <a href="%s">here</a> to check you\'ve entered your license key correctly or to enter a new one.', SKYSTATS_TEXT_DOMAIN ), $current_url, SKYSTATS_SETTINGS_PAGE_URL ),
	);

	$licenseRenewalURL = str_replace( '{LICENSE_KEY}', get_option( 'skystats_license_key' ), SKYSTATS_RENEW_LICENSE_KEY_URL );
	$translations['license_expired'] = sprintf( __( 'Sorry, this license key has expired. Please click <a href="%s">here</a> to renew your license, or click <a href="%s">here</a> to enter a different one.', SKYSTATS_TEXT_DOMAIN ), $licenseRenewalURL, SKYSTATS_SETTINGS_PAGE_URL );

	return $translations;
}

/**
 * Return translations for a license validation request.
 *
 * @since 0.1.4
 *
 * @return string[]
 */
function skystats_api_licensing_get_license_validation_request_translations() {

	$genericError = __( 'Sorry, an error occurred. Please try again.', SKYSTATS_TEXT_DOMAIN );

	$translations = array(
		'missing_license_key'                       => __( 'Please enter a license key.', SKYSTATS_TEXT_DOMAIN ),
		'malformed_license_key'                     => __( 'Please enter a valid license key.', SKYSTATS_TEXT_DOMAIN ),
		'http_error'                                => __( 'Sorry, an error occurred. This was likely caused by a timeout. Please try again.', SKYSTATS_TEXT_DOMAIN ),
		'error_initializing_request'                => $genericError,
		'error_executing_request'                   => $genericError,
		'malformed_response'                        => $genericError,
		'license_valid'                             => sprintf( __( 'License key validated successfully. Please click <a href="%s">here</a> to go setup your integrations.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_MASHBOARD_PAGE_URL ),
		'license_activated'                         => sprintf( __( 'License key activated successfully. Please click <a href="%s">here</a> to go setup your integrations.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_MASHBOARD_PAGE_URL ),
		'license_site_inactive_no_activations_left' => __( 'Sorry, this license key hasn\'t been used for this site yet, however you have no remaining activations left. Please enter a different license key to continue.', SKYSTATS_TEXT_DOMAIN ),
		'license_site_inactive_activation_error'    => __( 'Sorry, this license key hasn\'t been activated for this site yet, however there was an error when trying to activate it. Please try again.', SKYSTATS_TEXT_DOMAIN ),
		'license_inactive_no_activations_left'      => __( 'Sorry, this license key hasn\'t been activated for any sites yet, however you have no remaining activations left. Please enter a different license key to continue.', SKYSTATS_TEXT_DOMAIN ),
		'license_inactive_activation_error'         => __( 'Sorry, this license key hasn\'t been activated for any sites yet, however there was an error when trying to activate it. Please try again.', SKYSTATS_TEXT_DOMAIN ),
		// We replace the {LICENSE_KEY} placeholder in the license validation handler, since fetching it here would mean using a possibly outdated license key.
		'license_expired'                           => sprintf( __( 'Sorry, this license key has expired. Please click <a href="%s">here</a> to renew your license, or enter a different one below.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_RENEW_LICENSE_KEY_URL ),
		'license_disabled'                          => __( 'Sorry, this license key has been disabled. Please enter a different license key to continue.', SKYSTATS_TEXT_DOMAIN ),
		'license_unknown_status'                    => __( 'Sorry, an unknown error occurred. Please try again.', SKYSTATS_TEXT_DOMAIN ),
	);

	return $translations;
}