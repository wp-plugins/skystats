<?php

// Prevent direct access
defined( 'ABSPATH' ) or exit();

/**
 * SkyStats mashboard cards related functions.
 *
 * @since 0.2.9
 *
 * @package SkyStats\Mashboard
 */

/**
 * Returns array of mashboard cards element IDs.
 *
 * @since 0.2.9
 *
 * @return array
 */
function skystats_get_mashboard_card_identifiers() {
	return array(
		'googleanalytics_1',
		'facebook_2',
		'twitter_3',
		'paypal_4',
		'youtube_5',
		'googleplus_6',
		'linkedin_7',
		'mailchimp_8',
		'wordpress_9',
		'aweber_10',
		'googleadwords_11',
		'campaignmonitor_12',
		'vote_13',
		'upgrading_14',
	);
}