<?php

/**
 * SkyStats Settings AJAX-related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\Ajax\Settings
 */

defined( 'ABSPATH' ) or exit();

add_action( 'wp_ajax_skystats_ajax_settings_save_settings', 'skystats_ajax_settings_save_settings' );

function skystats_ajax_settings_save_settings() {

	// Vote Opt-in
	$vote_opt_in = ( isset( $_POST['vote_opt_in'] ) && in_array( $_POST['vote_opt_in'], array( 'true', 'false' ) ) ) ?
		$_POST['vote_opt_in'] :
		'false';
	update_option( 'skystats_vote_opted_in_with_email_address', $vote_opt_in );


	// Role Access
	$registered_roles = (array) get_editable_roles();
	$role_access = array();
	if ( ! empty( $_POST['role_access'] ) ) {

		$post_role_access = $_POST['role_access'];

		if ( isset( $registered_roles[ $post_role_access ] ) ) {

			foreach ( $registered_roles as $identifier => $role_data ) {
				$role_access[] = $identifier;
				if ( $post_role_access === $identifier ) {
					break;
				}
			}
		}
	}
	update_option( 'skystats_role_access', $role_access );

	// Default Dashboard
	$default_dashboard = ( ! empty( $_POST['default_dashboard'] ) && in_array( $_POST['default_dashboard'], array(
			'skystats_mashboard',
			'wordpress_dashboard',
		)
	) ) ?
		$_POST['default_dashboard'] :
		'skystats_mashboard';
	update_option( 'skystats_default_dashboard', $default_dashboard );

	// Caching
	$cache_mode = ( ! empty( $_POST['cache_mode'] ) && in_array( $_POST['cache_mode'], array(
			'enabled',
			'disabled',
		)
	) ) ?
		$_POST['cache_mode'] :
		'enabled';
	update_option( 'skystats_cache_mode', $cache_mode );

	exit();
}