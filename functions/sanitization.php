<?php

/**
 * SkyStats sanitization-related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\Sanitization
 */

defined( 'ABSPATH' ) or exit();

/**
 * Sanitizes a hex color.
 * 
 * @since 1.0.0
 * 
 * @param string $color Hex color.
 *
 * @return null|string Null is color is invalid otherwise the color.
 */
function skystats_sanitize_hex_color( $color ) {
	if ( ! is_string( $color ) ) {
		return null;
	}

	// #000, #000000, 000, or 000000 are allowed
	if ( ! preg_match( '/^(#?[A-Za-z0-9]{3}){1,2}$/', $color ) ) {
		return null;
	}

	return $color;
}

/**
 * Validates and returns date range, uses default dates if not.
 *
 * @since 1.0.0
 * 
 * @todo relocate
 * 
 * @param string $start_date
 * 
 * @param string $end_date
 * 
 * @return string[] Date range
 */
function skystats_validate_date_range( $start_date, $end_date ) {
	$start_date_time = strtotime( $start_date );
	if ( false === $start_date_time ) {
		$start_date = date( 'Y-m-d', strtotime( '-7days' ) );
	} else {
		$start_date = date( 'Y-m-d', $start_date_time );
	}

	$end_date_time = strtotime( $end_date );
	if ( false === $end_date_time ) {
		$end_date = date( 'Y-m-d', strtotime( 'now' ) );
	} else {
		$end_date = date( 'Y-m-d', $end_date_time );
	}

	return array( $start_date, $end_date );
}