<?php

/**
 * Installs SkyStats.
 * 
 * Nothing is actually installed unless it has to be.
 * 
 * In a multisite environment:
 * 
 * 1. If the network administrator wishes to install SkyStats globally, installation is run for the site and each blog contained within the site.
 * 2. Otherwise, SkyStats is simply installed for the current blog and its site.
 * 
 * Otherwise, simply installs SkyStats for the current blog.
 * 
 * @since 0.0.1
 *
 * @package SkyStats
 */

// Prevent direct access
defined( 'ABSPATH' ) or exit();

add_action( 'skystats_check_option_skystats_mashboard_card_positions', 'skystats_check_card_positions', 10, 3 );
/**
 * Check we are using the new card positions when installing/upgrading.
 *
 * @param array $current_card_positions Array of card positions.
 *
 * @param array $default_card_positions Array of the original card positions.
 *
 * @param array $option_functions Array of option names under the following keys: get, update, add. The option names
 *                                are automatically generated for you so they will work whether the current site is a site
 *                                or a blog.
 */
function skystats_check_card_positions( $current_card_positions, $default_card_positions, $option_functions ) {
	$option_name = 'skystats_mashboard_card_positions';
	if ( ! isset( $current_card_positions['postbox-container-1'] ) ) {
		$new_option_val = array();
		foreach ( $current_card_positions as $column_name => $cards ) {
			if ( 'column_1' === $column_name ) {
				$new_option_val['postbox-container-1'] = $cards;
			}
			if ( 'column_2' === $column_name ) {
				$new_option_val['postbox-container-2'] = $cards;
			}
			if ( 'column_3' === $column_name ) {
				$new_option_val['postbox-container-3'] = $cards;
			}
			if ( 'column_4' === $column_name ) {
				$new_option_val['postbox-container-4'] = $cards;
			}
		}
		$update_option_function = $option_functions[ 'update' ];
		$update_option_function( $option_name, $new_option_val );
	}
}

add_action( 'skystats_check_option_skystats_version', 'skystats_change_mailchimp_card_position_on_upgrade', 10, 3 );
/**
 * If we are upgrading to 0.3.3 (when MailChimp was released for the free version), we need to check if the MailChimp
 * card is in the 1st column or not. If it is not, we move it to the 1st column.
 *
 * @param string $current_version The version of SkyStats currently installed.
 *
 * @param string $release_version The version of this release.
 *
 * @param array $option_functions Array of option names under the following keys: get, update, add. The option names
 *                                are automatically generated for you so they will work whether the current site is a site
 *                                or a blog.
 */
function skystats_change_mailchimp_card_position_on_upgrade( $current_version, $release_version, $option_functions ) {

	if ( $current_version < '0.3.3' ) {

		$card_positions_option_name = 'skystats_mashboard_card_positions';

		$get_option_function = $option_functions['get'];

		$card_positions = $get_option_function( $card_positions_option_name );

		if ( ! is_array( $card_positions ) ) {
			return;
		}

		if ( ! array_key_exists( 'postbox-container-1', $card_positions ) ) {
			return;
		}

		$changed = false;

		if ( array_key_exists( 'postbox-container-4', $card_positions ) ) {
			if ( ( $key = array_search( 'mailchimp_8', $card_positions['postbox-container-4'] ) ) !== false ) {
				$changed = true;
				unset( $card_positions['postbox-container-1'][ $key ] );
			}
		}

		if ( ! in_array( 'mailchimp_8', $card_positions['postbox-container-1'] ) ) {
			$changed = true;
			$card_positions['postbox-container-1'][] = 'mailchimp_8';
		}

		if ( $changed ) {
			$update_option_function = $option_functions['update'];
			$update_option_function( $card_positions_option_name, $card_positions );
		}
	}
}

add_action( 'skystats_check_option_skystats_version', 'skystats_install_update_version', 10, 3 );
/**
 * Check if the installed version needs to be updated.
 *
 * @param string $current_version The version of SkyStats currently installed.
 *
 * @param string $release_version The version of this release.
 *
 * @param array $option_functions Array of option names under the following keys: get, update, add. The option names
 *                                are automatically generated for you so they will work whether the current site is a site
 *                                or a blog.
 */
function skystats_install_update_version( $current_version, $release_version, $option_functions ) {
	if ( $current_version !== $release_version ) {
		$update_option_function = $option_functions[ 'update' ];
		$update_option_function( 'skystats_version', $release_version );
	}
}

skystats_install();

/**
 * Installs Skystats.
 * 
 * @since 0.0.1
 */
function skystats_install() {

	// Multisite is disabled.
	if ( ! is_multisite() ) {

		skystats_install_options( 'blog' );

		skystats_install_tables( 'blog' );

	// Multisite is enabled.
	} else {

		// Only install for the current blog and site.
		if ( true !== get_site_option( 'skystats_perform_network_install' ) ) {

			skystats_install_options( 'site' );
			skystats_install_tables( 'site' );

			skystats_install_options( 'blog' );
			skystats_install_tables( 'blog' );

		// Otherwise, network administrator wants to install for whole network.
		} else {

			update_site_option( 'skystats_perform_network_install', false );

			/**
			 * Multisite specific functions.
			 */
			require_once SKYSTATS_FUNCTIONS_PATH . 'multisite.php';

			foreach ( skystats_get_site_ids() as $site_id ) {

				skystats_install_options( 'site' );

				skystats_install_tables( 'site' );

				foreach ( skystats_get_blog_ids_of_site_id( $site_id ) as $blog_id ) {

					switch_to_blog( $blog_id );

					skystats_install_options( 'blog' );

					skystats_install_tables( 'blog' );

					restore_current_blog();
				}
			}
		}
	}
}

/**
 * Installs options for a site or blog.
 * 
 * Options are only added if they do not exist.
 * 
 * @since 0.0.1
 * 
 * @param string $site_type 'site' or 'blog'.
 */
function skystats_install_options( $site_type ) {

	require_once SKYSTATS_FUNCTIONS_PATH . 'options.php';

	$options = skystats_get_options();

	$get_option_function = ( 'site' === $site_type ) ? 'get_site_option' : 'get_option';

	$add_option_function = ( 'site' === $site_type ) ? 'add_site_option' : 'add_option';

	$update_option_function = ( 'site' === $site_type ) ? 'update_site_option' : 'update_option';

	$option_functions = array(
		'get' => $get_option_function,
		'add' => $add_option_function,
		'update' => $update_option_function,
	);

	foreach ( $options[ $site_type ] as $name => $values ) {

		if ( false !== $current_option_val = $get_option_function( $name ) ) {

			do_action( "skystats_check_option_{$name}", $current_option_val, $values, $option_functions );

			continue;
		}

		$add_option_function( $name, $values );
	}
}

/**
 * Installs tables for a site or blog.
 * 
 * Tables are only added if they do not exist.
 * 
 * @since 0.0.1
 * 
 * @param string     $site_type    'site' or 'blog'.
 */
function skystats_install_tables( $site_type ) {

	require_once SKYSTATS_API_FUNCTIONS_PATH . 'tables.php';

	// Each table has its own function responsible for setting it up.
	$table_handlers = skystats_get_table_handlers( $site_type );

	global $wpdb;

	foreach ( $table_handlers as $handler ) {
		call_user_func( $handler, $wpdb, 'install' );
	}
}