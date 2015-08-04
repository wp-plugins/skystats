<?php

/**
 * SkyStats API cache-related functions.
 * 
 * @since 1.1.1
 *
 * @package SkyStats\API\Cache
 */

defined( 'ABSPATH' ) or exit();

/**
 * Return a cache row specified by $name.
 * 
 * @since 1.1.1
 * 
 * @param string $name Cache name.
 * 
 * @return mixed False if the cache does not exist, otherwise the value of the cache.
 */
function skystats_api_cache_get( $name ) {

	if ( 'enabled' !== get_option( 'skystats_cache_mode' ) ) {
		return false;
	}

	global $wpdb;

	$var = $wpdb->get_var( "SELECT `cache_value` FROM `{$wpdb->prefix}skystats_cache` WHERE `cache_name` = '{$name}'");

	if ( ! $var ) {
		return false;
	}

	$var = unserialize( $var );

	if ( ! is_array( $var ) ) {
		return false;
	}

	if ( ! ( array_key_exists( 'data', $var ) && array_key_exists( 'responseType', $var ) && array_key_exists( 'responseContext', $var ) ) ) {
		return false;
	}

	return $var;
}

/**
 * Inserts or updates a cache row.
 * 
 * @since 1.1.1
 * 
 * @param string $name  Cache name.
 * 
 * @param mixed  $value Cache value.
 * 
 * @return bool|null    False if the caching is disabled. Otherwise null.
 */
function skystats_api_cache_set( $name, $value ) {

	if ( ! ( ! empty( $value ) && is_array( $value ) ) ) {
		return;
	}

	if ( ! $value = serialize( $value ) ) {
		return;
	}

	global $wpdb;

	$table = $wpdb->prefix . 'skystats_cache';

	if ( $cache_id = $wpdb->get_var( "SELECT `cache_id` FROM `{$table}` WHERE `cache_name` = '{$name}'" ) ) {
		$wpdb->update( $table, array(
			'cache_value' => $value,
		), array(
			'cache_id'   => $cache_id,
		) );
	} else {
		$wpdb->insert( $wpdb->prefix . 'skystats_cache', array(
			'cache_name'  => $name,
			'cache_value' => $value,
		) );
	}
}

/**
 * Delete a cache row identified by $name.
 * 
 * @since 1.1.1
 * 
 * @param string $name  Cache name.
 * 
 * @return void
 */
function skystats_api_cache_delete( $name ) {
	global $wpdb;
	$table = $wpdb->prefix . 'skystats_cache';
	$wpdb->query( "DELETE FROM `{$table}` WHERE `cache_name` = '{$name}'" );
}

/**
 * Deletes one or more cache rows matching the query.
 * 
 * @since 1.1.1
 * 
 * @param string $name_like
 * 
 * @return void
 */
function skystats_api_cache_delete_name_like( $name_like ) {
	global $wpdb;
	$table = $wpdb->prefix . 'skystats_cache';
	$wpdb->query( "DELETE FROM `{$table}` WHERE `cache_name` LIKE '%{$name_like}%'" );
}