<?php

/**
 * SkyStats AJAX-related functions.
 * 
 * Each `add_action` call below corresponds to the below function.
 * 
 * @since 0.0.1
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
require_once $dir . '/ajax/twitter.php';
require_once $dir . '/ajax/google-adwords.php';
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
		'postbox-container-1',
		'postbox-container-2',
		'postbox-container-3',
		'postbox-container-4',
	);

	/**
	 * Mashboard cards related functions.
	 */
	require_once SKYSTATS_FUNCTIONS_PATH . 'mashboard_cards.php';

	$allowed_card_names = skystats_get_mashboard_card_identifiers();

	$card_positions = array(
		'postbox-container-1' => array(),
		'postbox-container-2' => array(),
		'postbox-container-3' => array(),
		'postbox-container-4' => array(),
	);

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
 * @since 0.0.1
 */
function skystats_ajax_get_mashboard_card_positions() {

	$card_positions = get_option( 'skystats_mashboard_card_positions' );

	if (!isset($card_positions['postbox-container-1'], $card_positions['postbox-container-2'], $card_positions['postbox-container-3'], $card_positions['postbox-container-4'])) {
		$card_positions['postbox-container-1'] = isset($card_positions['column_1']) ? $card_positions['column_1'] : array();
		$card_positions['postbox-container-2'] = isset($card_positions['column_2']) ? $card_positions['column_2'] : array();
		$card_positions['postbox-container-3'] = isset($card_positions['column_3']) ? $card_positions['column_3'] : array();
		$card_positions['postbox-container-4'] = isset($card_positions['column_4']) ? $card_positions['column_4'] : array();
		unset($card_positions['column_1'], $card_positions['column_2'], $card_positions['column_3'], $card_positions['column_4']);
	}

	echo json_encode( $card_positions );

	exit();
}