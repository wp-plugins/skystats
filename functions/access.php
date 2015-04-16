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
 * Checks if current user has access to somewhere.
 * 
 * @since 1.0.0
 * 
 * @return bool True if the user has access, otherwise false.
 */
function skystats_is_current_user_allowed_access() {

	// Logged in?
	if ( true !== is_user_logged_in() ) {
		return false;
	}

	// Prevents first time access from being denied
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	$user = wp_get_current_user();

	if ( ! $user instanceof WP_User ) {
		return false;
	}

	if ( false === $role_access = get_option( 'skystats_role_access' ) ) {
		return false;
	}

	foreach ( $user->roles as $identifier ) {
		if ( in_array( $identifier, $role_access, true ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Displays a HTML message then halts execution if a user doesn't have access to somewhere.
 * 
 * @since 1.0.0
 */
function skystats_show_error_if_unauthorized() {
	if ( true !== skystats_is_current_user_allowed_access() ) {
		$brand_name = get_option( 'skystats_brand_name' );
		wp_die( 
			sprintf( __( 'Sorry! You do not have access this page. If you think this is wrong, please contact the person who has access to the %s Settings.', SKYSTATS_TEXT_DOMAIN ), $brand_name ), 
			__( 'Access Denied', SKYSTATS_TEXT_DOMAIN ),
			array( 'back_link' => true )
		);
	}
}