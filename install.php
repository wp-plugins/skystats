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

					skystats_install_options( 'blog', $blog_id );

					skystats_install_tables( 'blog', $blog_id );

					restore_current_blog( $blog_id );
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

	static $options;

	require_once SKYSTATS_FUNCTIONS_PATH . 'options.php';

	$options = skystats_get_options();

	$get_option_function = ( 'site' === $site_type ) ? 'get_site_option' : 'get_option';

	$add_option_function = ( 'site' === $site_type ) ? 'add_site_option' : 'add_option';

	foreach ( $options[ $site_type ] as $name => $values ) {

		if ( false !== $get_option_function( $name ) ) {
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

	static $tables;

	require_once SKYSTATS_API_FUNCTIONS_PATH . 'tables.php';

	// Each table has its own function responsible for setting it up.
	$table_handlers = skystats_get_table_handlers( $site_type );

	global $wpdb;

	foreach ( $table_handlers as $handler ) {
		call_user_func( $handler, $wpdb, 'install' );
	}
}