<?php

/**
 * SkyStats script-related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats\Scripts
 */

defined( 'ABSPATH' ) or exit();

/**
 * Load a style using either uncompressed or minified versions depending on configuration.
 * 
 * You must enqueue the uncompressed version, the function will use the minified version if it's supposed to.
 * Also, you must ensure uncompressed versions are in the main CSS folder and minified versions are in 
 * `min/` in the same folder.
 * 
 * @since 1.0.0
 * 
 * @param string         $handle   The name of the style
 * 
 * @param string         $filename The name of the CSS file with extension e.g. 'admin.css';
 * 
 * @param string[]|array $deps     (Optional) Array of style dependencies.
 * 
 * @param string         $version  (Optional) The version to use for caching the style.
 * 
 * @param string         $media    (Optional) The media type this stylesheet supports.
 */
function skystats_enqueue_style( $handle, $filename, array $deps = array(), $version = SKYSTATS_VERSION, $media = 'all' ) {
	if ( ! SKYSTATS_USE_MINIFIED_SCRIPTS ) {
		$src = SKYSTATS_TEMPLATE_CSS_URL . $filename;
	} else {
		$filename = str_replace( '.css', '.min.css', $filename );
		$src = SKYSTATS_TEMPLATE_CSS_MIN_URL . $filename;
	}

	wp_enqueue_style( $handle, $src, $deps, $version, $media );
}

/**
 * Enqueue a minified style which is stored in a predefined directory.
 * 
 * @since 1.0.0
 * 
 * @param string         $handle   The name of the style
 * 
 * @param string         $filename The name of the CSS file with extension e.g. 'admin.css';
 * 
 * @param string[]|array $deps     (Optional) Array of style dependencies.
 * 
 * @param string         $version  (Optional) The version to use for caching the style.
 * 
 * @param string         $media    (Optional) The media type this stylesheet supports.
 */
function skystats_enqueue_minified_style( $handle, $filename, array $deps = array(), $version = SKYSTATS_VERSION, $media = 'all' ) {
	wp_enqueue_style( $handle, SKYSTATS_TEMPLATE_CSS_MIN_URL . $filename, $deps, $version, $media );
}

/**
 * Loads a script using compressed or uncompressed versions depending on configuration.
 * 
 * @since 1.0.0
 * 
 * @param string         $handle   The name of the script.
 * 
 * @param string         $filename The name of the JS file with extension e.g. 'admin.js';
 * 
 * @param string[]|array $deps     (Optional) Array of script dependencies.
 * 
 * @param string         $version  (Optional) The version to use for caching the script.
 */
function skystats_enqueue_script( $handle, $filename, $deps = array(), $version = SKYSTATS_VERSION ) {
	if ( ! SKYSTATS_USE_MINIFIED_SCRIPTS ) {
		$src = SKYSTATS_TEMPLATE_JS_URL . $filename;
	} else {
		$filename = str_replace( '.js', '.min.js', $filename );
		$src = SKYSTATS_TEMPLATE_JS_MIN_URL . $filename;
	}

	wp_enqueue_script( $handle, $src, $deps, $version );
}

/**
 * Loads a minified/compressed JavaScript file from a predefined directory.
 * 
 * @since 1.0.0
 * 
 * @param string         $handle   The name of the script.
 * 
 * @param string         $filename The name of the JS file with extension e.g. 'admin.js';
 * 
 * @param string[]|array $deps     (Optional) Array of script dependencies.
 * 
 * @param string         $version  (Optional) The version to use for caching the script.
 */
function skystats_enqueue_minified_script( $handle, $filename, $deps = array(), $version = SKYSTATS_VERSION ) {
	wp_enqueue_script( $handle, SKYSTATS_TEMPLATE_JS_MIN_URL . $filename, $deps, $version );
}

/**
 * Loads a uncompressed JavaScript file from a predefined directory.
 * 
 * @since 1.0.0
 * 
 * @param string         $handle   The name of the script.
 * 
 * @param string         $filename The name of the JS file with extension e.g. 'admin.js';
 * 
 * @param string[]|array $deps     (Optional) Array of script dependencies.
 * 
 * @param string         $version  (Optional) The version to use for caching the script.
 */
function skystats_enqueue_uncompressed_script( $handle, $filename, $deps = array(), $version = SKYSTATS_VERSION ) {
	wp_enqueue_script( $handle, SKYSTATS_TEMPLATE_JS_URL . $filename, $deps, $version );
}