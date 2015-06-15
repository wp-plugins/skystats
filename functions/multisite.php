<?php

/**
 * SkyStats multisite functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats
 */

defined( 'ABSPATH' ) or exit();

/**
 * Returns all site ids.
 * 
 * @since 1.0.0
 * 
 * @return string[] Site ids.
 */
function skystats_get_site_ids() {
	global $wpdb;
	return $wpdb->get_col( "SELECT `id` FROM `{$wpdb->site}`" );
}

/**
 * Returns all blog ids in a site, given its id.
 * 
 * @since 1.0.0
 * 
 * @param string|int $site_id Site id.
 * 
 * @return string[] Blog ids.
 */
function skystats_get_blog_ids_of_site_id( $site_id ) {
	global $wpdb;
	return $wpdb->get_col( "SELECT `blog_id` FROM `{$wpdb->blogs}` WHERE `site_id` = '{$site_id}'" );
}