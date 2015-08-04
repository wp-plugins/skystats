<?php

// Prevent direct access
defined( 'ABSPATH' ) or exit();

/**
 * SkyStats page access related functions.
 *
 * @since 1.0.0
 *
 * @package SkyStats\Access
 */

/**
 * Shows the unauthorized error when trying to access a SkyStats page the user doesn't have access to.
 */
function skystats_show_unauthorized_error() {
	$brand_name = get_option( 'skystats_brand_name', __( 'SkyStats', SKYSTATS_TEXT_DOMAIN ) );
	wp_die(
		sprintf( __( 'Sorry! You do not have access this page. If you think this is wrong, please contact the person who has access to the %s Settings.', SKYSTATS_TEXT_DOMAIN ), $brand_name ),
		__( 'Access Denied', SKYSTATS_TEXT_DOMAIN ),
		array( 'back_link' => true )
	);
}

/**
 * Check whether the current user can access the reports (mashboard & detail pages).
 *
 * @return bool True
 */
function skystats_can_current_user_access_reports() {

	// Logged in?
	if ( true !== is_user_logged_in() ) {
		return false;
	}

	$reports_roles_allowed_access = get_option( 'skystats_reports_users_allowed_access' );

	/*
	 * If no roles have been selected, this could be because SkyStats has just been installed,
	 * then only allow users with the 'manage_options' capability.
	 */
	if ( empty( $reports_roles_allowed_access ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		return false;
	}

	$user = wp_get_current_user();

	if ( ! $user instanceof WP_User ) {
		return false;
	}

	foreach ( $user->roles as $identifier ) {
		if ( in_array( $identifier, $reports_roles_allowed_access, true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Check whether the current user is allowed to see/access the Settings.
 *
 * @since 0.2.8
 *
 * @return bool True if the user can see/change the settings otherwise false.
 */
function skystats_can_current_user_access_settings() {

	// Logged in?
	if ( true !== is_user_logged_in() ) {
		return false;
	}

	$settings_users_allowed_access = get_option( 'skystats_settings_users_allowed_access' );

	/*
	 * If no users have been selected, this could be because SkyStats has just been installed,
	 * then only allow users with the 'manage_options' capability.
	 */
	if ( empty( $settings_users_allowed_access ) ) {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		return false;
	}

	$user = wp_get_current_user();

	if ( ! $user instanceof WP_User ) {
		return false;
	}

	if ( isset( $user->ID ) && in_array( $user->ID, $settings_users_allowed_access  ) ) {
		return true;
	}

	return false;
}