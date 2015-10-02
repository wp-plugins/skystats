<?php

/**
 * SkyStats option-related functions.
 * 
 * @since 1.0.0
 *
 * @package SkyStats
 */

// Prevent direct access
defined( 'ABSPATH' ) or exit();

/**
 * Returns options used for blogs and sites.
 * 
 * @since 1.0.0
 * 
 * @return array
 */
function skystats_get_options() {

	$options = array();

	$options['site']['skystats_version'] = SKYSTATS_VERSION;
	$options['blog']['skystats_version'] = SKYSTATS_VERSION;

	$options['site']['skystats_perform_network_install']    = false;

	$options['blog']['skystats_license_key']                = null;
	$options['blog']['skystats_brand_background_image_url'] = SKYSTATS_DEFAULT_BACKGROUND_IMAGE_URL;
	$options['blog']['skystats_brand_logo_image_url']       = SKYSTATS_DEFAULT_LOGO_IMAGE_URL;

	// (string) Hex color for background color on SkyStats pages.
	$options['blog']['skystats_brand_background_color']     = '#333';

	/* (string) Location user is redirected upon login (SkyStats Mashboard or WordPress dashboard).
	 * Note: If WordPress Dashboard is selected as the default dashboard (default option), we don't do anything
	 * when the user logs in. We only redirect the user to the SkyStats Mashboard if 'skystats_mashboard' is selected.
	 */
	$options['blog']['skystats_default_dashboard']          = 'wordpress_dashboard';

	// (string[]) Contains role identifiers as string-values.
	$options['blog']['skystats_role_access']                = array();

	// (array) Mashboard card positions
	$options['blog']['skystats_mashboard_card_positions']   = array(
		'postbox-container-1' => array(
			'googleanalytics_1',
			'mailchimp_8',
			'youtube_5',
			'wordpress_9',
		),
		'postbox-container-2' => array(
			'facebook_2',
			'googleplus_6',
			'aweber_10',
		),
		'postbox-container-3' => array(
			'twitter_3',
			'linkedin_7',
			'googleadwords_11',
		),
		'postbox-container-4' => array(
			'vote_13',
			'upgrading_14',
			'paypal_4',
			'campaignmonitor_12',
		),
	);

	// (null|string) Selected Google Analytics Profile ID for data retrieval
	$options['blog']['skystats_selected_google_analytics_profile_id'] = null;

	// (null|string) Selected Facebook Page ID for data retrieval
	$options['blog']['skystats_selected_facebook_page_id'] = null;

	// (string) Whether to use the cache when fetching data for any integration (default: enabled).
	$options['blog']['skystats_cache_mode'] = 'enabled';

	// (string) The license type
	$options['blog']['skystats_license_type'] = 'free';

	// (string) Plugin type
	$options['blog']['skystats_plugin_type'] = 'free';

	$options['blog']['skystats_show_license_purchase_notification'] = 'true';

	$options['blog']['skystats_activation_message_shown'] = 'false';

	// (null|string) Adwords Customer/Account Id
	$options['blog']['skystats_google_adwords_selected_customer_id'] = null;

	// (null|string) Adwords Campaign Id
	$options['blog']['skystats_google_adwords_selected_campaign_id'] = null;

	// (array) Adwords accounts
	$options['blog']['skystats_google_adwords_accounts'] = array();

	return $options;
}