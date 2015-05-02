<?php

/**
 * SkyStats AJAX-related functions.
 * 
 * Each `add_action` call below corresponds to the below function.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\Ajax
 */

defined( 'ABSPATH' ) or exit();

$dir = dirname( __FILE__ );

add_action( 'wp_ajax_skystats_ajax_upload_image', 'skystats_ajax_upload_image' );

/**
 * Output JSON data of image upload result.
 *
 * @since 0.0.1
 */
function skystats_ajax_upload_image() {
	$result = false;

	if ( isset( $_FILES ) && isset( $_FILES['files']['name'] ) ) {

		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$file = array(
			'tmp_name' => $_FILES['files']['tmp_name'][0],
			'name'     => $_FILES['files']['name'][0],
			'type'     => $_FILES['files']['type'][0],
			'size'     => $_FILES['files']['size'][0],
		);

		// WP will handle uploading our file and placing it in the uploads folder
		$file = wp_handle_upload( $file, array( 'test_form' => false ) );

		if ( isset( $file['url'] ) ) {
			$result = $file['url'];
		}

	}

	echo json_encode( $result );

	exit();
}

require_once $dir . '/ajax/google-analytics.php';
require_once $dir . '/ajax/facebook.php';
require_once $dir . '/ajax/licensing.php';
require_once $dir . '/ajax/settings.php';

add_action( 'wp_ajax_skystats_ajax_validate_date_range', 'skystats_ajax_validate_date_range' );

/**
 * Validates and returns a date range, using default dates if they weren't valid.
 * 
 * @since 0.0.1
 */
function skystats_ajax_validate_date_range( ) {

	require_once SKYSTATS_FUNCTIONS_PATH . 'sanitization.php';

	if ( isset( $_GET['frequency'] ) && 'monthly' === $_GET['frequency'] ) {
		$frequency = 'monthly';
		$tick_interval = '1 month';
	} else {
		$frequency = 'daily';
		$tick_interval = '1 day';
	}

	list( $start_date, $end_date ) = skystats_validate_date_range( $_GET['start_date'], $_GET['end_date'] );

	$arr['start_date']    = $start_date;
	$arr['end_date']      = $end_date;
	$arr['frequency']     = $frequency;
	$arr['tick_interval'] = $tick_interval;

	echo json_encode( $arr );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_save_mashboard_card_positions', 'skystats_ajax_save_mashboard_card_positions' );

/**
 * Save Mashboard card positions.
 * 
 * @since 0.0.1
 */
function skystats_ajax_save_mashboard_card_positions() {

	$data = $_POST['data'];

	if ( ! ( is_array( $data ) && ! empty( $data ) ) ) {
		return;
	}

	// Default column order.
	$allowed_column_names = array( 
		'column_1', 
		'column_2', 
		'column_3',
		'column_4',
	);

	// Default card order (3 per column).
	$allowed_card_names = array(
		'googleanalytics_1',
		'facebook_2',
		'twitter_3',
		'paypal_4',
		'youtube_5',
		'googleplus_6',
		'linkedin_7',
		'mailchimp_8',
		'wordpress_9',
		'aweber_10',
		'googleadwords_11',
		'campaignmonitor_12',
	);

	$card_positions = array();

	foreach ( $data as $column_name => $card_names ) {
		if ( ! in_array( $column_name, $allowed_column_names, true ) ) {
			return;
		}
		foreach ( $card_names as $card_name ) {
			if ( ! in_array( $card_name, $allowed_card_names, true ) ) {
				return;
			}
			$card_positions[ $column_name ][] = $card_name;
		}
	}

	update_option( 'skystats_mashboard_card_positions', $card_positions );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_get_mashboard_card_positions', 'skystats_ajax_get_mashboard_card_positions' );

/**
 * Return Mashboard Card Positions.
 * 
 * @since 1.0.0
 */
function skystats_ajax_get_mashboard_card_positions() {

	$card_positions = get_option( 'skystats_mashboard_card_positions' );

	echo json_encode( $card_positions );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_vote_for_integration', 'skystats_ajax_vote_for_integration' );
/**
 * Vote for a integration.
 *
 * @since 1.0.0
 */
function skystats_ajax_vote_for_integration() {

	$integration = isset( $_POST['integration'] ) ? $_POST['integration'] : '';

	$current_user = wp_get_current_user();

	wp_remote_post( SKYSTATS_STORE_URL, array(
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS,
		'body' => array(
			'skst_integration_voting_action' => 'add_vote',
			'integration'                    => $integration,
			'email'                          => $current_user->user_email,
			'client'                         => 'true',
		),
	) );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_get_integration_vote_totals', 'skystats_ajax_get_integration_vote_totals' );
/**
 * Return total number of votes for each integration.
 *
 * @since 0.1.5
 */
function skystats_ajax_get_integration_vote_totals() {
	$url = add_query_arg( array(
		'skst_integration_voting_action' => 'get_integration_vote_totals',
	), SKYSTATS_STORE_URL );

	$response = wp_remote_get( $url, array(
		'timeout'     => SKYSTATS_API_REQUEST_TIMEOUT,
		'sslverify'   => SKYSTATS_API_REQUEST_VERIFY_SSL,
		'compress'    => SKYSTATS_API_REQUEST_COMPRESS
	) );

	$default_return_value = array( 'result' => null );

	if ( is_wp_error( $response ) ) {
		echo json_encode( $default_return_value );
		exit();
	}

	if ( '' === $body = wp_remote_retrieve_body( $response ) ) {
		echo json_encode( $default_return_value );
		exit();
	}

	$body = json_decode( $body, true );

	if ( ! array_key_exists( 'result', $body ) ) {
		echo json_encode( $default_return_value );
		exit();
	}

	echo json_encode( $body );
	exit();
}