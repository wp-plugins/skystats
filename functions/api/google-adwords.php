<?php

/**
 * SkyStats Google Adwords API-related functions.
 *
 * @since 0.2.9
 *
 * @package SkyStats\API\Google_Adwords
 */

defined( 'ABSPATH' ) or exit();

require_once dirname( __FILE__ ) . '/cache.php';

/**
 * Return URL which allows a user to authenticate/authorize with Google Adwords.
 *
 * @since 0.2.9
 *
 * @param string $redirect_url URL to redirect to on success/failure.
 *
 * @return string
 */
function skystats_api_google_adwords_get_authorization_url( $redirect_url ) {

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURI'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/googleAdwords/authorize/' );

	return $url;
}

/**
 * Return URL which allows a user to deauthenticate/deauthorize with Google Adwords.
 *
 * @since 0.2.9
 *
 * @param string $redirect_url URL to redirect to on success/failure.
 *
 * @return string
 */
function skystats_api_google_adwords_get_deauthorization_url( $redirect_url ) {

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'redirectURI'   => $redirect_url,
	), SKYSTATS_API_URL . 'api/googleAdwords/deauthorize/' );

	return $url;
}

/**
 * Called when a user deauthorizes Google Adwords.
 *
 * Removes cached Google Adwords data.
 *
 * @since 0.2.9
 */
function skystats_api_google_adwords_deauthorize() {
	skystats_api_cache_delete_name_like( 'skystats_cache_google_adwords' );
	delete_option( 'skystats_google_adwords_accounts' );
	delete_option( 'skystats_google_adwords_selected_customer_id' );
	delete_option( 'skystats_google_adwords_selected_campaign_id' );
}

/**
 * Save the customer id.
 *
 * @since 0.2.9
 */
function skystats_api_google_adwords_save_customer_id() {
	$customer_id = isset( $_GET['google_adwords_selected_customer_id'] ) ? (string) $_GET['google_adwords_selected_customer_id'] : null;
	if ( ! ctype_digit( $customer_id ) ) {
		return;
	}
	update_option( 'skystats_google_adwords_selected_customer_id', $customer_id );
}

/**
 * Save the campaign id.
 *
 * @since 0.2.9
 */
function skystats_api_google_adwords_save_campaign_id() {
	$campaign_id = isset( $_GET['google_adwords_selected_campaign_id'] ) ? (string) $_GET['google_adwords_selected_campaign_id'] : null;
	if ( $campaign_id !== 'allData' && ! ctype_digit( $campaign_id ) ) {
		return;
	}
	update_option( 'skystats_google_adwords_selected_campaign_id', $campaign_id );
	if ( isset( $_GET['google_adwords_selected_customer_id'] ) ) {
		$customer_id = (string) $_GET['google_adwords_selected_customer_id'];
		if ( ctype_digit( $customer_id ) ) {
			update_option( 'skystats_google_adwords_selected_customer_id', $customer_id );
		}
	}
}
/**
 * Return accounts data.
 *
 * @since 0.2.9
 *
 * @return array
 */
function skystats_api_google_adwords_get_accounts() {

	$cache_option_name = 'skystats_google_adwords_accounts';

	$cached_campaign_data = get_option( $cache_option_name );
	if ( is_array($cached_campaign_data) && ! empty( $cached_campaign_data ) ) {
		return $cached_campaign_data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
	), SKYSTATS_API_URL . 'api/googleAdwords/getAccounts/' );

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
		update_option( $cache_option_name, $body );
	}

	return $body;
}

/**
 * Returns campaign data.
 *
 * @since 0.2.9
 *
 * @param int|string $customer_id
 *
 * @return array
 */
function skystats_api_google_adwords_get_campaign_data( $customer_id ) {

	$customer_id = (string) $customer_id;

	if ( ! ctype_digit( $customer_id ) ) {
		$customer_id = 0;
	}

	$cache_name = "skystats_cache_google_adwords_campaigns_customer_{$customer_id}";

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		return $data;
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'customerId'    => $customer_id,
	), SKYSTATS_API_URL . 'api/googleAdwords/getCampaigns/' );

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
 * Returns data for the a view (mashboard or detail).
 *
 * @since 0.2.9
 *
 * @param string $view       'mashboard' or 'detail'.
 *
 * @param string $start_date End date as YYYY-MM-DD.
 *
 * @param string $end_date   End date as YYYY-MM-DD.
 *
 * @param string $customer_id Customer/account id.
 *
 * @param string $campaign_id Campaign id.
 *
 * @return array
 */
function skystats_api_google_adwords_get_view_data( $view, $start_date, $end_date, $customer_id, $campaign_id ) {

	$start_date_time = (string) strtotime( $start_date );
	$end_date_time = (string) strtotime( $end_date );
	$customer_id = (string) $customer_id;

	if ( ! ctype_digit( $customer_id ) ) {
		$customer_id = 0;
	}

	$campaign_id = (string) $campaign_id;

	if ( $campaign_id !== 'allData' && ! ctype_digit( $campaign_id ) ) {
		$campaign_id = 0;
	}

	if ( 'mashboard' === $view ) {
		$cache_name = "skystats_cache_google_adwords_mashboard_data_{$start_date_time}__{$end_date_time}_{$customer_id}_{$campaign_id}";
	} else {
		$cache_name = "skystats_cache_google_adwords_detail_data_{$start_date_time}__{$end_date_time}_{$customer_id}_{$campaign_id}";
	}

	if ( false !== $data = skystats_api_cache_get( $cache_name ) ) {
		if ( isset( $data['data']['currency_code'] ) ) {
			$data['data']['currency_symbol_hex_code'] = skystats_google_adwords_get_currency_symbol_hex_code( $data['data']['currency_code'] );
		}
		return $data;
	}

	if ( 'mashboard' === $view ) {
		$api_url = SKYSTATS_API_URL . 'api/googleAdwords/getMashboardViewData/';
	} else {
		$api_url = SKYSTATS_API_URL . 'api/googleAdwords/getDetailViewData/';
	}

	$url = add_query_arg( array(
		'licenseKey'    => get_option( 'skystats_license_key' ),
		'licenseDomain' => home_url(),
		'startDate'     => $start_date,
		'endDate'       => $end_date,
		'customerId'    => $customer_id,
		'campaignId'    => $campaign_id,
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
		if ( isset( $body['data']['currency_code'] ) ) {
			$body['data']['currency_symbol_hex_code'] = skystats_google_adwords_get_currency_symbol_hex_code( $body['data']['currency_code'] );
		}
		skystats_api_cache_set( $cache_name, $body );
	}

	return $body;
}

/**
 * Return the HTML currency symbol hex code for a currency code.
 *
 * Note: Not all values are hex codes.
 *
 * @since 0.2.9
 *
 * @param string $currency_code The 3 digit currency code e.g. USD, EUR.
 *
 * @return string
 */
function skystats_google_adwords_get_currency_symbol_hex_code( $currency_code ) {
	$currency_symbol_hex_codes = array(
		'AED' => '',
		'ARS' => 'AR$',
		'AUD' => '$',
		'BGN' => '',
		'BND' => '$',
		'BOB' => '',
		'BRL' => 'R$',
		'CAD' => '$',
		'CHF' => '',
		'CLP' => 'CL$',
		'CNY' => "&#xA5;",
		'COP' => '',
		'CSD' => '',
		'CZK' => '',
		'DEM' => '',
		'DKK' => '',
		'EEK' => '',
		'EGP' => '',
		'EUR' => "&#x20AC;",
		'FJD' => '$',
		'FRF' => '',
		'GBP' => "&#xA3;",
		'HKD' => '$',
		'HRK' => '',
		'HUF' => '',
		'IDR' => '',
		'ILS' => '&#x20aa;',
		'INR' => '',
		'JPY' => '',
		'KES' => '',
		'KRW' => "&20A9;",
		'LTL' => '',
		'MAD' => '',
		'MTL' => '',
		'MXN' => '',
		'MYR' => '',
		'NOK' => '',
		'NZD' => '$',
		'PEN' => '',
		'PHP' => '',
		'PKR' => '',
		'PLN' => '',
		'ROL' => '',
		'RON' => '',
		'RSD' => '',
		'RUB' => '',
		'SAR' => '',
		'SEK' => '',
		'SGD' => '$',
		'SIT' => '',
		'SKK' => '',
		'THB' => '&#xe3f;',
		'TRL' => '',
		'TRY' => '',
		'TWD' => '$',
		'UAH' => '',
		'USD' => '$',
		'VEB' => '',
		'VEF' => '',
		'VND' => "&20AB;",
		'ZAR' => '',
	);

	return isset( $currency_symbol_hex_codes[ $currency_code ] ) ? $currency_symbol_hex_codes[ $currency_code ] : '$';
}

/**
 * Return translation for a Google Adwords API error that is known, but currently unhandled.
 *
 * @since 0.2.9
 *
 * @param string $error The Google Adwords API "Error.REASON".
 *
 * @return string Translated error.
 */
function skystats_api_google_adwords_get_api_generic_error_translation( $error ) {
	return sprintf( __( 'A unhandled Google Adwords API error occurred: "%s". Please try again.', SKYSTATS_TEXT_DOMAIN ), $error );
}


/**
 * Return translations for Google Adwords API errors.
 *
 * @since 0.2.9
 *
 * @return array
 */
function skystats_api_google_adwords_get_api_error_translations() {
	$tooMuchDataError = __( "Google Adwords API Error: There was too much data to return. If you're trying to view data over a long period of time, please try refining your criteria or trying again.", SKYSTATS_TEXT_DOMAIN );
	$invalidCustomerError = __( "Google Adwords API Error: No account found for the provided customer ID. If you've just created your account, then please wait a few minutes before trying again.", SKYSTATS_TEXT_DOMAIN );
	return array(
		'AdError.INVALID_INPUT'                                        => skystats_api_google_adwords_get_api_generic_error_translation( 'AdError.INVALID_INPUT' ),
		'AdError.LINE_TOO_WIDE'                                        => skystats_api_google_adwords_get_api_generic_error_translation( 'AdError.LINE_TOO_WIDE' ),
		'AdGroupAdError.CANNOT_OPERATE_ON_REMOVED_ADGROUPAD'           => skystats_api_google_adwords_get_api_generic_error_translation( 'AdGroupAdError.CANNOT_OPERATE_ON_REMOVED_ADGROUPAD' ),
		'AdGroupCriterionError.INVALID_KEYWORD_TEXT'                   => skystats_api_google_adwords_get_api_generic_error_translation( 'AdGroupCriterionError.INVALID_KEYWORD_TEXT' ),
		'AdGroupServiceError.DUPLICATE_ADGROUP_NAME'                   => skystats_api_google_adwords_get_api_generic_error_translation( 'AdGroupServiceError.DUPLICATE_ADGROUP_NAME' ),
		'AuthenticationError.CUSTOMER_NOT_FOUND'                       => $invalidCustomerError,
		'AuthenticationError.GOOGLE_ACCOUNT_COOKIE_INVALID'            => __( 'Your access token has expired or is invalid. Please try again.', SKYSTATS_TEXT_DOMAIN ),
		'AuthenticationError.NOT_ADS_USER'                             => sprintf( __( 'Your account is not an Adwords account. Please try again, or if the problem persists, deauthorize, then click <a href="%s">here</a> to signup for a Google Adwords account.', SKYSTATS_TEXT_DOMAIN ), 'https://www.google.co.uk/adwords/' ),
		'AuthorizationError.UNABLE_TO_AUTHORIZE'                       => __( 'There was an error trying to authorize your account. Please try again or wait a few minutes before retrying.', SKYSTATS_TEXT_DOMAIN ),
		'AuthorizationError.USER_PERMISSION_DENIED'                    => __( "Permission denied. You're likely trying to access an account or campaign that you do not have access to. Please try again.", SKYSTATS_TEXT_DOMAIN ),
		'BiddingError.BID_TOO_HIGH_FOR_DAILY_BUDGET'                   => skystats_api_google_adwords_get_api_generic_error_translation( 'BiddingError.BID_TOO_HIGH_FOR_DAILY_BUDGET' ),
		'BiddingError.BID_TOO_MANY_FRACTIONAL_DIGITS'                  => skystats_api_google_adwords_get_api_generic_error_translation( 'BiddingError.BID_TOO_MANY_FRACTIONAL_DIGITS' ),
		'BiddingError.BID_TOO_BIG'                                     => skystats_api_google_adwords_get_api_generic_error_translation( 'BiddingError.BID_TOO_BIG' ),
		'BiddingError.CANNOT_SET_SITE_MAX_CPC'                         => skystats_api_google_adwords_get_api_generic_error_translation( 'BiddingError.CANNOT_SET_SITE_MAX_CPC' ),
		'BulkMutateJobError.PAYLOAD_STORE_UNAVAILABLE'                 => skystats_api_google_adwords_get_api_generic_error_translation( 'BulkMutateJobError.PAYLOAD_STORE_UNAVAILABLE' ),
		'CampaignError.DUPLICATE_CAMPAIGN_NAME'                        => skystats_api_google_adwords_get_api_generic_error_translation( 'CampaignError.DUPLICATE_CAMPAIGN_NAME' ),
		'CriterionError.AD_SCHEDULE_EXCEEDED_INTERVALS_PER_DAY_LIMIT'  => skystats_api_google_adwords_get_api_generic_error_translation( 'CriterionError.AD_SCHEDULE_EXCEEDED_INTERVALS_PER_DAY_LIMIT' ),
		'CustomerSyncError.TOO_MANY_CHANGES'                           => $tooMuchDataError,
		'DatabaseError.CONCURRENT_MODIFICATION'                        => skystats_api_google_adwords_get_api_generic_error_translation( 'DatabaseError.CONCURRENT_MODIFICATION' ),
		'DistinctError.DUPLICATE_ELEMENT'                              => skystats_api_google_adwords_get_api_generic_error_translation( 'DistinctError.DUPLICATE_ELEMENT' ),
		'EntityNotFound.INVALID_ID'                                    => skystats_api_google_adwords_get_api_generic_error_translation( 'EntityNotFound.INVALID_ID'  ),
		'InternalApiError.UNEXPECTED_INTERNAL_API_ERROR'               => __( 'There was an issue processing your request due an error in the Google Adwords API. Please wait at least 30 seconds before trying again.', SKYSTATS_TEXT_DOMAIN ),
		'JobError.TOO_LATE_TO_CANCEL_JOB'                              => skystats_api_google_adwords_get_api_generic_error_translation( 'JobError.TOO_LATE_TO_CANCEL_JOB' ),
		'NotEmptyError.EMPTY_LIST'                                     => skystats_api_google_adwords_get_api_generic_error_translation( 'NotEmptyError.EMPTY_LIST' ),
		'NotWhitelistedError.CUSTOMER_ADS_API_REJECT'                  => skystats_api_google_adwords_get_api_generic_error_translation( 'NotWhitelistedError.CUSTOMER_ADS_API_REJECT' ),
		'OperationAccessDenied.ADD_OPERATION_NOT_PERMITTED'            => skystats_api_google_adwords_get_api_generic_error_translation( 'OperationAccessDenied.ADD_OPERATION_NOT_PERMITTED' ),
		'OperationAccessDenied.MUTATE_ACTION_NOT_PERMITTED_FOR_CLIENT' => skystats_api_google_adwords_get_api_generic_error_translation( 'OperationAccessDenied.MUTATE_ACTION_NOT_PERMITTED_FOR_CLIENT' ),
		'PolicyViolationError'                                         => skystats_api_google_adwords_get_api_generic_error_translation( 'PolicyViolationError' ),
		'QuotaCheckError.ACCOUNT_DELINQUENT'                           => skystats_api_google_adwords_get_api_generic_error_translation( 'QuotaCheckError.ACCOUNT_DELINQUENT' ),
		'QuotaCheckError.ACCOUNT_INACCESSIBLE'                         => skystats_api_google_adwords_get_api_generic_error_translation( 'QuotaCheckError.ACCOUNT_INACCESSIBLE' ),
		'QuotaCheckError.TERMS_AND_CONDITIONS_NOT_SIGNED'              => skystats_api_google_adwords_get_api_generic_error_translation( 'QuotaCheckError.TERMS_AND_CONDITIONS_NOT_SIGNED' ),
		'QuotaCheckError.DEVELOPER_TOKEN_NOT_APPROVED'                 => skystats_api_google_adwords_get_api_generic_error_translation( 'QuotaCheckError.DEVELOPER_TOKEN_NOT_APPROVED' ),
		'QuotaCheckError.INCOMPLETE_SIGNUP'                            => skystats_api_google_adwords_get_api_generic_error_translation( 'QuotaCheckError.INCOMPLETE_SIGNUP' ),
		'QuotaCheckError.INVALID_TOKEN_HEADER'                         => skystats_api_google_adwords_get_api_generic_error_translation( 'QuotaCheckError.INVALID_TOKEN_HEADER' ),
		'QuotaCheckError.MONTHLY_BUDGET_REACHED'                       => skystats_api_google_adwords_get_api_generic_error_translation( 'QuotaCheckError.MONTHLY_BUDGET_REACHED' ),
		'QuotaCheckError.QUOTA_EXCEEDED'                               => skystats_api_google_adwords_get_api_generic_error_translation( 'QuotaCheckError.QUOTA_EXCEEDED' ),
		'RangeError.TOO_LOW'                                           => __( 'The selected date range is too low. Please try again or refine your search.', SKYSTATS_TEXT_DOMAIN ),
		'RateExceededError.RATE_EXCEEDED'                              => __( 'Rate limit exceeded. Please wait for at least 30 seconds, then try again.', SKYSTATS_TEXT_DOMAIN ),
		'ReportDefinitionError.CUSTOMER_SERVING_TYPE_REPORT_MISMATCH'  => skystats_api_google_adwords_get_api_generic_error_translation( 'ReportDefinitionError.CUSTOMER_SERVING_TYPE_REPORT_MISMATCH' ),
		'ReportInfoError.INVALID_USER_ID_IN_HEADER'                    => $invalidCustomerError,
		'RequestError.INVALID_INPUT'                                   => skystats_api_google_adwords_get_api_generic_error_translation( 'RequestError.INVALID_INPUT' ),
		'RequiredError.REQUIRED'                                       => skystats_api_google_adwords_get_api_generic_error_translation( 'RequiredError.REQUIRED' ),
		'SizeLimitError.RESPONSE_SIZE_LIMIT_EXCEEDED'                  => $tooMuchDataError,
		'unknown_google_adwords_api_error'                             => __( 'Google Adwords API Error: An unknown error occurred. Please try again.', SKYSTATS_TEXT_DOMAIN ),
		'no_accounts'                                                  => __( 'No accounts found. Please try again or deauthorize and use a Google Adwords account that has access to an account with at least one active campaign.', SKYSTATS_TEXT_DOMAIN ),
		'no_campaigns'                                                 => __( 'No campaigns found. Please select an account which has access to at least one active campaign.', SKYSTATS_TEXT_DOMAIN ),
		'no_data'                                                      => __( 'No data found for the selected period. Please try again or refine your search.', SKYSTATS_TEXT_DOMAIN ),
		'http_error'                                                   => __( 'A HTTP Error occurred. This was likely caused by a timeout. Please try again.', SKYSTATS_TEXT_DOMAIN ),
	);
}