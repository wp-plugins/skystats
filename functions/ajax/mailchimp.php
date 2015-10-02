<?php

/**
 * SkyStats MailChimp AJAX-related functions.
 *
 * @since 0.3.3
 *
 * @package SkyStats\Ajax\MailChimp
 */

defined( 'ABSPATH' ) or exit();

/**
 * MailChimp API related functions.
 *
 * @since 0.3.3
 */
require_once SKYSTATS_API_FUNCTIONS_PATH . 'mailchimp.php';

add_action( 'wp_ajax_skystats_ajax_mailchimp_api_query', 'skystats_ajax_mailchimp_api_query' );

/**
 * All MailChimp AJAX requests are sent through this function.
 *
 * @since 0.3.3
 */
function skystats_ajax_mailchimp_api_query() {

	if ( empty( $_GET['query'] ) ) {
		exit();
	}

	$query = wp_strip_all_tags( $_GET['query'] );

	if ( 'get_status' === $query ) {
		$status = skystats_api_mailchimp_get_status();
		echo json_encode( $status );
		exit();
	}

	if ( 'deauthorize' === $query ) {
		skystats_api_mailchimp_deauthorize();
		exit();
	}

	if ( ! empty( $_GET['end_date'] ) && ( $end_date = strtotime( $_GET['end_date'] ) ) ) {
		$end_date = new DateTime( date( 'Y-m-d', $end_date ) );
	} else {
		$end_date = new DateTime( 'yesterday' );
	}

	if ( ! empty( $_GET['start_date'] ) && ( $start_date = strtotime( $_GET['start_date'] ) ) ) {
		$start_date = new DateTime( date( 'Y-m-d', $start_date ) );
	} else {
		$start_date = clone $end_date;
		$start_date->modify('-30 days');
	}

	$start_date = $start_date->format( 'Y-m-d' );
	$end_date = $end_date->format( 'Y-m-d' );

	if ( 'get_mashboard_view_data' === $query ) {
		$mashboard_view_data = skystats_api_mailchimp_get_view_data( 'mashboard', $start_date, $end_date );
		echo json_encode( $mashboard_view_data );
		exit();
	}

	if ( 'get_detail_view_data' === $query ) {
		$detail_view_data = skystats_api_mailchimp_get_view_data( 'detail', $start_date, $end_date );
		echo json_encode( $detail_view_data );
		exit();
	}

	exit();
}