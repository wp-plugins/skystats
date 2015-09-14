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
			update_option( 'skystats_selected_role_access', $post_role_access );
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

	// Role identifiers allowed to view and access the reports (mashboard & detail pages)
	if ( is_array( $_POST['skystats_reports_users_allowed_access'] ) && ! empty( $_POST['skystats_reports_users_allowed_access'] ) ) {
		$skystats_reports_users_allowed_access = $_POST['skystats_reports_users_allowed_access'];
		$registered_roles = array_keys( (array) get_editable_roles() );
		$role_identifiers = array();
		foreach ( $skystats_reports_users_allowed_access as $role_identifier ) {
			if ( in_array( $role_identifier, $registered_roles ) ) {
				$role_identifiers[] = $role_identifier;
			}
		}
		if ( ! empty( $role_identifiers ) ) {
			update_option( 'skystats_reports_users_allowed_access', $role_identifiers );
		}
	}

	// User ids allowed access to view and edit the Settings
	if ( is_array( $_POST['skystats_settings_users_allowed_access'] ) && ! empty( $_POST['skystats_settings_users_allowed_access'] ) ) {
		$settings_user_ids_allowed_access = $_POST['skystats_settings_users_allowed_access'];
		$user_ids = array();
		foreach ( $settings_user_ids_allowed_access as &$user_id ) {
			$user_id = (string) $user_id;
			if ( ctype_digit( $user_id ) && ! in_array( $user_id, $user_ids ) ) {
				$user_ids[] = $user_id;
			}
		}
		if ( ! empty( $user_ids ) ) {
			update_option( 'skystats_settings_users_allowed_access', $user_ids );
		}
	}

	exit();
}