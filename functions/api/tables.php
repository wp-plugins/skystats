<?php

/**
 * SkyStats table related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\Tables
 */

// Prevent direct access
defined( 'ABSPATH' ) or exit();

/**
 * Returns array of functions responsible for setting up a table.
 * 
 * @since 1.0.0
 *
 * @param string $site_type 'site' or 'blog', to return handles which handle those site types tables as
 *                          certain tables may not be intended for all site types for example.
 * 
 * @return string[]
 */
function skystats_get_table_handlers( $site_type ) {
	$handlers = array();

	if ( 'blog' === $site_type ) {
		$handlers = array(
			'skystats_cache_table_handler',
		);
	}

	return $handlers;
}

/**
 * Responsible for setting up the cache table.
 * 
 * @since 1.0.0
 * 
 * @param wpdb $wpdb
 *
 * @param string $handle_type What the handler is being requested to handle e.g. a install or uninstall.
 */
function skystats_cache_table_handler( wpdb $wpdb, $handle_type = 'install' ) {
	$prefixed_table_name = $wpdb->prefix . 'skystats_cache';
	if ( 'install' === $handle_type ) {
		// Table doesn't exist, create...
		if ( ! ( $wpdb->query( 'SHOW TABLES LIKE "' . $prefixed_table_name. '"' ) > 0 ) ) {
			$wpdb->query( 'CREATE TABLE `' . $prefixed_table_name . '` (
				`cache_id` bigint(1) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`cache_name` varchar(250) NOT NULL UNIQUE,
				`cache_value` text NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1' );
		}
	} else if ( 'uninstall' === $handle_type ) {
		// Table exists, drop...
		if ( $wpdb->query( 'SHOW TABLES LIKE "' . $prefixed_table_name . '"' ) > 0 ) {
			$wpdb->query( "DROP TABLE `" . $prefixed_table_name . "`" );
		}
	}
}