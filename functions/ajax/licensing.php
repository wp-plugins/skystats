<?php

/**
 * SkyStats Licensing AJAX-related functions.
 *
 * @since 1.0.0
 *
 * @package SkyStats\Ajax\Licensing
 */

defined( 'ABSPATH' ) or exit();

/**
 * Licensing API related functions.
 *
 * @since 1.0.0
 */
require_once SKYSTATS_API_FUNCTIONS_PATH . 'licensing.php';

add_action( 'wp_ajax_skystats_ajax_licensing_validate_license', 'skystats_ajax_licensing_validate_license' );

function skystats_ajax_licensing_validate_license() {

	update_option( 'skystats_show_license_purchase_notification', 'false' );

	$license_key = isset( $_GET['license_key'] ) ? $_GET['license_key'] : '';

	$result = skystats_api_licensing_validate_license( $license_key );

	if ( ctype_alnum( $license_key ) ) {
		update_option( 'skystats_license_key', $license_key );
	}

	echo json_encode( $result );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_licensing_is_license_valid', 'skystats_ajax_licensing_is_license_valid' );

function skystats_ajax_licensing_is_license_valid() {

	$is_valid = get_option( 'skystats_is_license_key_valid' );

	echo json_encode( $is_valid );

	exit();
}

add_action( 'wp_ajax_skystats_ajax_licensing_api_query', 'skystats_ajax_licensing_api_query' );

/**
 * All licensing API AJAX calls are sent through this function.
 *
 * @since 0.1.4
 */
function skystats_ajax_licensing_api_query() {

	if ( ! isset( $_GET['query'] ) ) {
		exit();
	}

	$query = wp_strip_all_tags( $_GET['query'] );

	switch ( $query ) {
		case 'validate_license':
			$license_key = ( isset( $_GET['license_key'] ) ) ? $_GET['license_key'] : null;
			$response = skystats_api_licensing_validate_license( $license_key );
			echo json_encode( $response );
			break;
		case 'check_license':
			$response = skystats_api_licensing_check_license();
			echo json_encode( $response );
			break;
		default:
			break;
	}

	exit();
}

add_action( 'wp_ajax_skystats_ajax_licensing_save_license_type', 'skystats_ajax_licensing_save_license_type' );
/**
 * Save the license type.
 *
 * @since 0.1.8
 */
function skystats_ajax_licensing_save_license_type() {
	if ( empty( $_POST['license_type'] ) ) {
		exit();
	}
	if ( ! in_array( $_POST['license_type'], array( 'free', 'personal', 'business', 'developer' ) ) ) {
		exit();
	}

	update_option( 'skystats_license_type', $_POST['license_type'] );

	exit();
}