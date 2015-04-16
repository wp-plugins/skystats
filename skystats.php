<?php

/*
	Plugin Name: SkyStats Lite
	Plugin URI: https://skystats.com
	Description: A Better WordPress Dashboard.
	Version: 0.2.3
	Author: SkyStats
	Author URI: https://skystats.com
	License: GPLv2 or later
	License URI: http://www.gnu.org/licenses/gpl-2.0.html
	Text Domain: skystats
*/

/**
 * SkyStats plugin entry point.
 * 
 * @since 0.0.1
 *
 * @package SkyStats
 */

// Prevent direct access
defined( 'ABSPATH' ) or exit();

/**
 * Load definitions, configuration, etc.
 */
require_once dirname( __FILE__ ) . '/bootstrap.php';

add_action( 'plugins_loaded', 'skystats_load_translations' );
/**
 * Loads the translation file for SkyStats.
 *
 * Uses a custom translation provided by a user from (defaults to `/wp-content/languages/skystats/`), and using any
 * translation shipped with the plugin as a fallback.
 *
 * @since 0.1.4
 */
function skystats_load_translations() {
	$locale = apply_filters( 'plugin_locale', get_locale(), SKYSTATS_TEXT_DOMAIN );
	$wp_languages_mo_file = WP_LANG_DIR . '/skystats/' . SKYSTATS_TEXT_DOMAIN . '-' . $locale . '.mo';
	$plugin_mo_file = SKYSTATS_ROOT_PATH . 'languages/' . SKYSTATS_TEXT_DOMAIN . '-' . $locale . '.mo';
	$mo_file = ( file_exists( $wp_languages_mo_file ) ) ? $wp_languages_mo_file : $plugin_mo_file;
	load_textdomain( SKYSTATS_TEXT_DOMAIN, $mo_file );
}

/**
 * Install SkyStats.
 */
require_once SKYSTATS_ROOT_PATH . 'install.php';

/**
 * AJAX-related functions.
 */
require_once SKYSTATS_FUNCTIONS_PATH . 'ajax.php';

add_action( 'wp_login', 'skystats_wp_login', 10, 0 );

/**
 * Run when user logs in.
 * 
 * @since 0.0.1
 */
function skystats_wp_login() {
	// Redirect user to SkyStats Mashboard
	if ( 'skystats_mashboard' === get_option( 'skystats_default_dashboard' ) ) {
		wp_redirect( SKYSTATS_MASHBOARD_PAGE_URL );
		exit();
	}
}

add_action( 'admin_notices', 'skystats_admin_notices', 20 );
/**
 * Display notice on activation.
 *
 * @since 0.0.5
 */
function skystats_admin_notices() {
	$msg_shown = get_option( 'skystats_activation_message_shown' );
	if ( false === $msg_shown || 'false' === $msg_shown ) {
		update_option( 'skystats_activation_message_shown', 'true' );
		$html = '<div class="updated">';
		$html .= '<p>';
		$html .= sprintf( __('Thanks for activating SkyStats! Go to the <a href="%s">settings</a> page to enter your license key.', SKYSTATS_TEXT_DOMAIN), SKYSTATS_SETTINGS_PAGE_URL );
		$html .= '</p>';
		$html .= '</div>';
		echo $html;
	}
}

add_filter( 'plugin_action_links', 'skystats_add_plugin_detail_setting_link', 10, 4 );
/**
 * Add a settings link to the plugins links on the Plugins page.
 * 
 * Supports WP installation versions 2.5.0 and upward.
 * 
 * @since 0.1.1
 *
 * @param array $actions
 *
 * @param string $plugin_file
 *
 * @param array $plugin_data
 *
 * @param string $context
 *
 * @return array Actions
 */
function skystats_add_plugin_detail_setting_link( $actions, $plugin_file, $plugin_data, $context  ) {

	if ( 'skystats/skystats.php' !== $plugin_file ) {
		return $actions;
	}

	if ( 'all' !== $context && 'active' !== $context && 'recently_activated' !== $context && 'upgrade' !== $context ) {
		return $actions;
	}

	$filtered_actions = array();

	$settings_url = admin_url( 'admin.php?page=skystats-settings' );

	foreach ( $actions as $action => $link ) {
		$filtered_actions[ $action ] = $link;
		if ( 'edit' === $action ) {
			$filtered_actions['settings'] = '<a href="' . $settings_url . '">' . __( 'Settings', SKYSTATS_TEXT_DOMAIN ) . '</a>';
		}
	}

	return $filtered_actions;
}

/**
 * Run on deactivation.
 *
 * @since 0.0.5
 */
function skystats_deactivate() {
	update_option( 'skystats_activation_message_shown', 'false' );
}
register_deactivation_hook( __FILE__, 'skystats_deactivate' );

add_action( 'admin_menu', 'skystats_admin_menu' );
/**
 * Run when the `admin_menu` action fires.
 * 
 * @since 1.0.0
 * 
 * @global $wp_version
 */
function skystats_admin_menu() {
	global $wp_version;
	/*
	 * We use a color version of the menu icon for WordPress versions < 3.8
	 */
	$icon_url = ( '3.8' <= $wp_version ) ? SKYSTATS_TEMPLATE_IMAGES_URL . 'menu-icon.png' : SKYSTATS_TEMPLATE_IMAGES_URL . 'menu-icon-lt-38.png';

	/*
	 * Capability required to access mashboard, detail, and setting pages.
	 * We check after if the user has the required role(s) to access the pages.
	 * as this can be configured from the settings.
	 */
	$required_access_capability = 'read';

	// Mashboard Page
	$mashboard_title = __( 'SkyStats Mashboard', SKYSTATS_TEXT_DOMAIN );
	add_menu_page( $mashboard_title, __( 'SkyStats', SKYSTATS_TEXT_DOMAIN ), $required_access_capability, 'skystats-mashboard', 'skystats_mashboard', $icon_url, '0.9' );
	add_submenu_page( 'skystats-mashboard', $mashboard_title, __( 'Mashboard', SKYSTATS_TEXT_DOMAIN ), $required_access_capability, 'skystats-mashboard' );

	// Google Analytics Detail Page
	add_submenu_page( 'skystats-mashboard', __( 'SkyStats Google Analytics', SKYSTATS_TEXT_DOMAIN ), __( 'Google Analytics', SKYSTATS_TEXT_DOMAIN ), $required_access_capability, 'skystats-google-analytics', 'skystats_google_analytics' );

	// Facebook Detail Page
	add_submenu_page( 'skystats-mashboard', __( 'SkyStats Facebook', SKYSTATS_TEXT_DOMAIN ), __( 'Facebook', SKYSTATS_TEXT_DOMAIN ), $required_access_capability, 'skystats-facebook', 'skystats_facebook' );

	// Settings Page
	add_submenu_page( 'skystats-mashboard', __( 'SkyStats Settings', SKYSTATS_TEXT_DOMAIN ), __( 'Settings', SKYSTATS_TEXT_DOMAIN ), $required_access_capability, 'skystats-settings', 'skystats_settings' );
}

/**
 * Displays content for the SkyStats mashboard menu page.
 * 
 * @since 1.0.0
 */
function skystats_mashboard() {
	/**
	 * See function description.
	 */
	require_once SKYSTATS_TEMPLATE_ADMIN_PATH . 'mashboard.php';
}

/**
 * Displays content for the SkyStats Google Analytics menu page.
 * 
 * @since 1.0.0
 */
function skystats_google_analytics() {
	/**
	 * See function description.
	 */
	require_once SKYSTATS_TEMPLATE_ADMIN_PATH . 'google-analytics.php';
}

/**
 * Displays content for the SkyStats Facebook menu page.
 * 
 * @since 1.0.0
 */
function skystats_facebook() {
	/**
	 * See function description.
	 */
	require_once SKYSTATS_TEMPLATE_ADMIN_PATH . 'facebook.php';
}

// Page load hooks
add_action( 'load-toplevel_page_skystats-mashboard', 'skystats_load_integration_admin_page' );
add_action( 'load-skystats_page_skystats-google-analytics', 'skystats_load_integration_admin_page' );
add_action( 'load-skystats_page_skystats-facebook', 'skystats_load_integration_admin_page' );

/**
 * Called when the an integration admin page is loaded but before any content is parsed.
 * 
 * An integration page refers to the Mashboard and detail pages.
 * 
 * @since 1.0.0
 */
function skystats_load_integration_admin_page() {

	/**
	 * Access-related functions.
	 */
	require_once SKYSTATS_FUNCTIONS_PATH . 'access.php';

	skystats_show_error_if_unauthorized();
}

add_action( 'load-skystats_page_skystats-settings', 'skystats_load_admin_settings_page' );

/**
 * Called when the SkyStats Settings page is accessed.
 * 
 * @since 1.0.0
 */
function skystats_load_admin_settings_page() {
	/**
	 * Access-related functions.
	 */
	require_once SKYSTATS_FUNCTIONS_PATH . 'access.php';

	skystats_show_error_if_unauthorized();

	require_once SKYSTATS_API_FUNCTIONS_PATH . 'licensing.php';
}

/**
 * Displays content for the SkyStats settings menu page.
 * 
 * @since 1.0.0
 */
function skystats_settings() {

	/**
	 * See function description.
	 */
	require_once SKYSTATS_TEMPLATE_ADMIN_PATH . 'settings.php';
}

add_action( 'admin_enqueue_scripts', 'skystats_admin_enqueue_scripts' );

/**
 * Run when the `admin_enqueue_scripts` action fires.
 * 
 * @since 1.0.0
 */
function skystats_admin_enqueue_scripts() {

	$page = ( isset( $_GET['page'] ) ) ? $_GET['page'] : '';

	require_once SKYSTATS_FUNCTIONS_PATH . 'scripts.php';

	// Any scripts used on any WP dashboard page
	skystats_enqueue_style( 'skystats-backend', 'backend.css' );

	if ( ! $page ) {
		return;
	}

	$pages = array(
		'skystats-mashboard',
		'skystats-google-analytics',
		'skystats-facebook',
		'skystats-settings',
	);

	if ( ! in_array( $page, $pages, true ) ) {
		return;
	}
	// Scripts & styles for all SkyStats pages
	skystats_admin_enqueue_global_scripts();

	if ( 'skystats-mashboard' === $page ) {

		skystats_admin_enqueue_service_scripts();
		skystats_admin_enqueue_mashboard_scripts();

	} else if ( 'skystats-google-analytics' === $page ) {

		skystats_admin_enqueue_service_scripts();
		skystats_admin_enqueue_detail_pages_scripts();
		skystats_admin_enqueue_google_analytics_scripts();

	} else if ( 'skystats-facebook' === $page ) {

		skystats_admin_enqueue_service_scripts();
		skystats_admin_enqueue_detail_pages_scripts();
		skystats_admin_enqueue_facebook_scripts();

	} else {

		skystats_admin_enqueue_settings_scripts();
	}
}

/**
 * Scripts designed to be used for all SkyStats pages.
 * 
 * @since 1.0.0
 * 
 * @global string $wp_version Version of WordPress installed.
 */
function skystats_admin_enqueue_global_scripts() {

	global $wp_version;

	// jQuery UI 1.11.2
	skystats_enqueue_minified_style( 'skystats-jquery-ui', 'jquery-ui-1.11.2.min.css' );

	// Global CSS
	skystats_enqueue_style( 'skystats', 'admin.css' );

	if ( defined( 'SKYSTATS_DEFAULT_BRAND_BACKGROUND_COLOR' ) ) {
		?>
		<style type="text/css">
			#wpcontent {
				background: <?php echo SKYSTATS_DEFAULT_BRAND_BACKGROUND_COLOR; ?>;
				min-height: 1000px;
			}
		</style>
	<?php
	}

	// Backstretch v2.0.4 (Minified)
	skystats_enqueue_minified_script( 'skystats-backstretch', 'backstretch.min.js' );

	// Global jQuery file
	skystats_enqueue_script( 'skystats', 'skystats.js' );

	// Global jQuery file data
	wp_localize_script( 'skystats', 'skystats', array( 
		'brand_background_image_url' => SKYSTATS_DEFAULT_BACKGROUND_IMAGE_URL,
		'brand_background_color'     => SKYSTATS_DEFAULT_BRAND_BACKGROUND_COLOR,
		'wp_version'                 => $wp_version,
	) );
}

/**
 * Scripts used for the Mashboard and Detail pages.
 *
 * @global bool $is_IE True if the user is using Internet Explorer
 * 
 * @since 1.0.0
 */
function skystats_admin_enqueue_service_scripts() {

	global $is_IE;

	?>
	<script type="text/javascript">
	WebFontConfig = {
		google: { families: [ 'Montserrat::latin', 'Open+Sans::latin' ] }
	};
	(function() {
		var wf = document.createElement('script' );
		wf.src = ('https:' == document.location.protocol ? 'https' : 'http') + '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
		wf.type = 'text/javascript';
		wf.async = 'true';
		var s = document.getElementsByTagName('script')[0];
		s.parentNode.insertBefore(wf, s);
	})(); </script>
	<?php

	// SkyStats Services CSS
	skystats_enqueue_style( 'skystats-services', 'services.css' );

	// Excanvas - Adds support for the HTML5 canvas tag on Internet Explorer
	?>
	<!--[if lte IE 8]>
		<script language="javascript" type="text/javascript" src="<?php echo SKYSTATS_TEMPLATE_JS_URL . 'excanvas.min.js'; ?>"></script>
	<![endif]-->
	<?php

	// Flot v0.8.3
	skystats_enqueue_script( 'skystats-flot', 'jquery.flot.js' );

	// Flot categories v1
	skystats_enqueue_script( 'skystats-flot-categories', 'jquery.flot.categories.js' );

	// Flot tooltip v0.8.4
	skystats_enqueue_uncompressed_script( 'skystats-flot-tooltip', 'jquery.flot.tooltip.js' );

	// SkyStats Chart Functions
	skystats_enqueue_script( 'skystats-chart-functions', 'chart-functions.js', array( 'jquery' ) );

	skystats_enqueue_script( 'skystats-integrations', 'integrations.js', array( 'jquery', 'jquery-ui-tooltip', 'jquery-ui-sortable', 'skystats-chart-functions' ) );
}

/**
 * Scripts used for the Detail Pages only.
 * 
 * @since 1.0.0
 */
function skystats_admin_enqueue_detail_pages_scripts() {
	skystats_enqueue_style( 'skystats-detail-pages', 'detail-pages.css' );
}

/**
 * Scripts used for the Google Analytics Detail Page.
 *
 * @global string $wp_version Installed version of WordPress.
 * 
 * @since 1.0.0
 */
function skystats_admin_enqueue_google_analytics_scripts() {

	global $wp_version;

	skystats_enqueue_style( 'skystats-google-analytics', 'google-analytics.css' );

	skystats_enqueue_script( 'skystats-google-analytics', 'google-analytics-detail-page.js', array( 'jquery', 'jquery-ui-datepicker' ) );

	$google_analytics_data = array(
		'selected_profile_id' => get_option( 'skystats_selected_google_analytics_profile_id' ),
	);

	$translations = array(
		'licensing'                => skystats_api_licensing_get_license_status_translations( SKYSTATS_GOOGLE_ANALYTICS_DETAIL_PAGE_URL ),
		'date_range_below_min'     => __( 'Sorry, you can only view data within the current 30 days. The current 30 days worth of data are being loaded for you automatically.', SKYSTATS_TEXT_DOMAIN ),
		'date_range_same'          => __( 'Start date cannot be the same as, or after, the end date.', SKYSTATS_TEXT_DOMAIN ),
	);

	// These versions don't handle multidimensional arrays
	if ( $wp_version < '3.4' ) {
		$google_analytics_data = array_merge_recursive( $google_analytics_data, array(
			'l10n_print_after' => 'skystats_google_analytics.trans = ' . json_encode( $translations ) . ';',
		) );
	} else {
		$google_analytics_data = array_merge_recursive( $google_analytics_data, array(
			'trans' => $translations,
		) );
	}

	wp_localize_script( 'skystats-google-analytics', 'skystats_google_analytics', $google_analytics_data );
}

/**
 * Scripts used for the Facebook Detail Page.
 *
 * @global string $wp_version Installed version of WordPress.
 * 
 * @since 0.0.1
 */
function skystats_admin_enqueue_facebook_scripts() {

	global $wp_version;

	skystats_enqueue_script( 'skystats-facebook', 'facebook-detail-page.js', array( 'jquery', 'jquery-ui-datepicker' ) );

	$facebook_data = array(
		'selected_page_id' => get_option( 'skystats_selected_facebook_page_id' ),
	);

	$translations = array(
		'licensing'                => skystats_api_licensing_get_license_status_translations( SKYSTATS_FACEBOOK_DETAIL_PAGE_URL ),
		'date_range_same'          => __( 'Start date cannot be the same as, or after, the end date.', SKYSTATS_TEXT_DOMAIN ),
		'date_range_below_min'     => __( 'Sorry, you can only view data within the current 30 days. The current 30 days worth of data are being loaded for you automatically.', SKYSTATS_TEXT_DOMAIN ),
		'date_range_exceeds_today' => __( 'Start and end date cannot be after today.', SKYSTATS_TEXT_DOMAIN ),
	);

	// These versions don't handle multidimensional arrays
	if ( $wp_version < '3.4' ) {
		$facebook_data = array_merge_recursive( $facebook_data, array(
			'l10n_print_after' => 'skystats_facebook.trans = ' . json_encode( $translations ) . ';',
		) );
	} else {
		$facebook_data = array_merge_recursive( $facebook_data, array(
			'trans' => $translations,
		) );
	}

	wp_localize_script( 'skystats-facebook', 'skystats_facebook', $facebook_data );
}

/**
 * Scripts used on the mashboard page.
 *
 * @global string $wp_version Installed version of WordPress.
 * 
 * @since 0.0.1
 */
function skystats_admin_enqueue_mashboard_scripts() {

	global $wp_version;

	// SkyStats Mashboard CSS
	skystats_enqueue_style( 'skystats-mashboard', 'mashboard.css' );

	// Raphael v2.1.0
	skystats_enqueue_minified_script( 'skystats-raphael', 'raphael.min.js' );

	// Morris v0.5.0
	skystats_enqueue_minified_script( 'skystats-morris', 'morris.min.js' );

	// SkyStats Mashboard JS
	skystats_enqueue_script( 'skystats-mashboard', 'mashboard.js', array( 'jquery', 'jquery-ui-datepicker', 'skystats-integrations' ) );

	$mashboard_data = array(
		'google_analytics' => array(
			'selected_profile_id' => get_option( 'skystats_selected_google_analytics_profile_id' ),
		),
		'facebook' => array(
			'selected_page_id' => get_option( 'skystats_selected_facebook_page_id' ),
		),
		'template_images_url' => SKYSTATS_TEMPLATE_IMAGES_URL,
		'voting_opted_in' => get_option( 'skystats_vote_opted_in_with_email_address' ),
	);

	$translations = array(
		'voting_not_opted_in_message' => __( 'Please opt-in to vote.', SKYSTATS_TEXT_DOMAIN ),
		'voting_not_opted_in_sub_message' => vsprintf( __( 'Sorry, in order to vote you must opt-in to allow us to include your email address in your vote(s). Please click <a href="%s">here</a> to opt-in.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_SETTINGS_PAGE_URL ),
		'integration_vote' => __( 'vote', SKYSTATS_TEXT_DOMAIN ),
		'integration_votes' => __( 'votes', SKYSTATS_TEXT_DOMAIN ),
		'integration_vote_result_message' => __( 'Thank you for voting!', SKYSTATS_TEXT_DOMAIN ),
		'integration_vote_result_sub_message' => __( 'We value your feedback and will make our decision soon. Please refresh the page to vote again.', SKYSTATS_TEXT_DOMAIN ),
		'licensing' => skystats_api_licensing_get_license_status_translations( SKYSTATS_MASHBOARD_PAGE_URL ),
		'date_range_same' => __( 'Start date cannot be the same as, or after, the end date.', SKYSTATS_TEXT_DOMAIN ),
		'date_range_below_min' => __( 'Sorry, you can only view data within the current 30 days. The current 30 days worth of data are being loaded for you automatically.', SKYSTATS_TEXT_DOMAIN ),
		'date_range_exceeds_today' => __( 'Start and end date cannot be after today.', SKYSTATS_TEXT_DOMAIN ),
	);

	// These versions don't handle multidimensional arrays
	if ( $wp_version < '3.4' ) {
		$mashboard_data = array_merge_recursive( $mashboard_data, array(
			'l10n_print_after' => 'skystats_mashboard.trans = ' . json_encode( $translations ) . ';',
		) );
	} else {
		$mashboard_data = array_merge_recursive( $mashboard_data, array(
			'trans' => $translations,
		) );
	}

	wp_localize_script( 'skystats-mashboard', 'skystats_mashboard', $mashboard_data );
}

/**
 * Loads any scripts used on the settings page.
 * 
 * @since 0.0.1
 *
 * @global string $wp_version The installed version of WP.
 */
function skystats_admin_enqueue_settings_scripts()
{
	global $wp_version;

	// Settings CSS
	skystats_enqueue_style( 'skystats-settings', 'settings.css' );

	?>
	<style>
		.ui-accordion-header-icon {
			background-image: url( " <?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'admin-sprite.png'; ?>" ) !important;
		}
	</style>
	<?php

	skystats_enqueue_minified_script( 'skystats-fileupload', 'jquery.fileupload.min.js', array( 'jquery' ) );

	skystats_enqueue_minified_script( 'skystats-fileupload-ui', 'jquery.fileupload-ui.min.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-widget' ) );

	if ( $wp_version >= '3.5' ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'wp-color-picker' );
	} else {
		skystats_enqueue_minified_style( 'skystats-colorpicker', 'colorpicker.min.css' );
		skystats_enqueue_minified_script( 'skystats-colorpicker', 'colorpicker.min.js');
	}

	skystats_enqueue_script( 'skystats-settings', 'settings.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-accordion' ) );

	$settings_data = array(
		'wp_version' => $wp_version,
	);

	$translations = array(
		'licensing' => skystats_api_licensing_get_license_validation_request_translations(),
	);

	// These versions don't handle multidimensional arrays
	if ( $wp_version < '3.4' ) {
		$settings_data = array_merge_recursive( $settings_data, array(
			'l10n_print_after' => 'skystats_settings.trans = ' . json_encode( $translations ) . ';',
		) );
	} else {
		$settings_data = array_merge_recursive( $settings_data, array(
			'trans' => $translations,
		) );
	}

	wp_localize_script( 'skystats-settings', 'skystats_settings', $settings_data );
}