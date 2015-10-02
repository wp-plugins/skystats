<?php

defined( 'ABSPATH' ) or exit();

?>

<div class="wrap">

	<div id="skystats-logo-container">
		<?php $mashboard_page_url = esc_attr( admin_url() . 'admin.php?page=skystats-mashboard' ); ?>
		<?php $brand_logo_image_url = esc_attr( get_option( 'skystats_brand_logo_image_url' ) ); ?>
		<a class="skystats-shadowless" href="<?php echo $mashboard_page_url; ?>"><img src="<?php echo $brand_logo_image_url; ?>"></a>
	</div>

	<!-- Free Version Date Range Notice -->
	<div id="skystats-date-range-notice-container">
		<input class="skystats-date" type="hidden" id="start_date" name="start_date" required="required" value="">
		<input class="skystats-date" type="hidden" id="end_date" name="end_date" required="required" value="">
		<p id="skystats-free-data-range-notice" class="skystats-success-message"><?php printf( __( 'Currently displaying the past 30 days. Upgrade to <a href="%s" target="_blank">SkyStats Pro</a> to access more than 30 days of data.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_MARKETING_SITE_FREE_SIGNUP_PAGE_URL ); ?></p>
	</div>

	<!-- Page Loading Container -->
	<div id="skystats-page-loading-container" class="skystats-loading-container">
		<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
	</div>

	<!-- Page Error Container -->
	<div id="skystats-page-error-container" class="skystats-page-error-container skystats-error-container">
		<p></p>
	</div>

	<!-- Twitter Container -->
	<div id="skystats-twitter-data-container" class="skystats-service-detail-container">

		<!-- Top Container (Header, Icons) -->
		<div class="skystats-detail-top-container">
			<h3 class="skystats-detail-header"><?php _e( 'Twitter', SKYSTATS_TEXT_DOMAIN ); ?>
				<div class="skystats-detail-icons">
					<span id="skystats-twitter-settings-icon" class="skystats-setting-tool-tip skystats-tooltip skystats-settings-icon skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for Twitter', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					<span id="skystats-twitter-grid-icon" class="skystats-grid-icon skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide Twitter Data Points', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					<div id="skystats-twitter-grid-icon-content" class="skystats-grid-content">
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-tweets-data-point" checked>
								<?php _e( 'Tweets', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-following-data-point" checked>
								<?php _e( 'Following', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-followers-data-point" checked>
								<?php _e( 'Followers', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-favourites-data-point" checked>
								<?php _e( 'Favorites', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-retweets-data-point" checked>
								<?php _e( 'Retweets', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-mentions-data-point" checked>
								<?php _e( 'Mentions', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-current-tweets-data-point">
								<?php _e( 'Current Tweets', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-current-retweets-data-point">
								<?php _e( 'Current Retweets', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-current-following-data-point">
								<?php _e( 'Current Following', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-current-followers-data-point">
								<?php _e( 'Current Followers', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-twitter-current-mentions-data-point">
								<?php _e( 'Current Mentions', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
					</div>
				</div>
			</h3>
		</div>

		<!-- Chart Key / Figure -->
		<div id="skystats-detail-chart-key-container" class="skystats-service-detail-chart-key-container">
			<span id="skystats-twitter-favorites-chart-key-icon" class="skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Favorites', SKYSTATS_TEXT_DOMAIN ); ?></span>
			<span id="skystats-twitter-mentions-chart-key-icon" class="skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Mentions', SKYSTATS_TEXT_DOMAIN ); ?></span>
		</div>

		<!-- Loading Image -->
		<div id="skystats-twitter-loading-container" class="skystats-loading-container">
			<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64" alt="<?php _e( 'Loading', SKYSTATS_TEXT_DOMAIN ); ?>">
		</div>

		<!-- Twitter Settings Tab Content -->
		<div id="skystats-twitter-settings-content" class="skystats-settings-tab-settings-content">

			<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'twitter.php'; ?>

			<!-- Valid Access Token -->
			<div id="skystats-twitter-settings-valid-access-token-section" class="skystats-twitter-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Valid Access Token', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'This means everything is working successfully and you haven\'t revoked the SkyStats Twitter application\'s access to your Twitter account.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<p><?php _e( 'You don\'t need to deauthorize. Data will continue to be collected for you each day.', SKYSTATS_TEXT_DOMAIN ); ?></p>
			</div>
			<!-- Invalid Access Token -->
			<div id="skystats-twitter-settings-invalid-access-token-section" class="skystats-twitter-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Invalid Access Token', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'This means you have either revoked the SkyStats Twitter application\'s access to your Twitter account, or your access token has expired. You will need to deauthorize below, to remove all cached data.', SKYSTATS_TEXT_DOMAIN ); ?></p>
			</div>
			<!-- Rate Limit Reached -->
			<div id="skystats-twitter-settings-rate-limit-reached-section" class="skystats-twitter-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Rate Limit Reached', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'This means everything is working correctly, but you\'ll need to wait before querying for new results. Please try again. If the problem continues, you\'ll need to wait at least 15 minutes before querying for new results.', SKYSTATS_TEXT_DOMAIN ); ?></p>
			</div>
			<!-- Setup (Authorize/Authenticate) -->
			<div id="skystats-twitter-settings-authorize-section" class="skystats-twitter-settings-tab-section skystats-settings-tab-section">
				<div class="skystats-card-settings-authorize-container">
					<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Click the button below to login to Twitter and authorize the application.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<p><strong><?php _e( 'Note: Full historical data for Twitter is not available. Data will be collected for you each day after you setup the integration and until you deauthorize.', SKYSTATS_TEXT_DOMAIN ); ?></strong></p>
					<?php $twitter_authorization_url = skystats_api_twitter_get_authorization_url( SKYSTATS_TWITTER_DETAIL_PAGE_URL ); ?>
					<a id="skystats-twitter-authorize" href="<?php echo esc_attr( $twitter_authorization_url ); ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
			</div>
			<!-- Deauthorize/Deauthenticate -->
			<div id="skystats-twitter-settings-deauthorize-section" class="skystats-twitter-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'Purge all Twitter authentication and cache data from your local install.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<p><strong><?php _e( 'All data that was collected will also be deleted.', SKYSTATS_TEXT_DOMAIN ); ?></strong></p>
				<?php $twitter_deauthorize_url = esc_attr( skystats_api_twitter_get_deauthorization_url( SKYSTATS_TWITTER_DETAIL_PAGE_URL ) ); ?>
				<a href="<?php echo $twitter_deauthorize_url; ?>" id="skystats-twitter-deauthorize" class="skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
			</div>
		</div>

		<!-- Twitter Data Tab Content -->
		<div id="skystats-twitter-data-tab-content">

			<!-- Twitter Chart -->
			<div id="skystats-twitter-chart-container" class="skystats-detail-chart-container skystats-chart-container">
				<div id="skystats-twitter-chart" class="skystats-detail-chart"></div>
			</div>

			<!-- Twitter Data Points -->
			<div id="skystats-twitter-data-points-container" class="skystats-data-points-container">

				<!-- Favourites -->
				<div id="skystats-twitter-favourites-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Favorites', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of your tweets that have been favorited by other users, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-favourites" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-twitter-favourites-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-twitter-favourites-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'is the amount of times your Tweets have been favorited, by other users, during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-twitter-favourites-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Mentions -->
				<div id="skystats-twitter-mentions-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Mentions', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of times other users have mentioned you in a tweet, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-mentions" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-twitter-mentions-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-twitter-mentions-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'is the amount of times you were mentioned in a tweet, by another user, in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-twitter-mentions-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Retweets -->
				<div id="skystats-twitter-retweets-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Retweets', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of your tweets that have been retweeted by other users, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-retweets" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-twitter-retweets-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-twitter-retweets-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'is the amount of times your tweets were retweeted, by other users, during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-twitter-retweets-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- New Followers -->
				<div id="skystats-twitter-followers-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'New Followers', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of users who have followed you during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-followers" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-twitter-followers-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-twitter-followers-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'people followed you in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-twitter-followers-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Tweets-->
				<div id="skystats-twitter-tweets-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Tweets', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of tweets posted to Twitter, including retweets and replies, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-tweets" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-twitter-tweets-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-twitter-tweets-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'tweets were posted by you in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-twitter-tweets-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Following -->
				<div id="skystats-twitter-following-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Following', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of users you have followed during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-following" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-twitter-following-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-twitter-following-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'is the amount of people you followed in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-twitter-following-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Current Tweets -->
				<div id="skystats-twitter-current-tweets-data-point" class="skystats-service-detail-data-point-column skystats-mashboard-data-point-column">
					<div class="skystats-dashboard-data-point-header">
						<span class="skystats-dashboard-data-point-heading"><?php _e( 'Current Tweets', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total amount of tweets you have posted.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-current-tweets" class="skystats-data-point-value"></span>
					</div>
				</div>

				<!-- Current Retweets -->
				<div id="skystats-twitter-current-retweets-data-point" class="skystats-service-detail-data-point-column skystats-mashboard-data-point-column">
					<div class="skystats-dashboard-data-point-header">
						<span class="skystats-dashboard-data-point-heading"><?php _e( 'Current Retweets', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total amount of retweets you have made.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-current-retweets" class="skystats-data-point-value"></span>
					</div>
				</div>

				<!-- Current Following -->
				<div id="skystats-twitter-current-following-data-point" class="skystats-service-detail-data-point-column skystats-mashboard-data-point-column">
					<div class="skystats-dashboard-data-point-header">
						<span class="skystats-dashboard-data-point-heading"><?php _e( 'Current Following', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total amount of users you are following.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-current-following" class="skystats-data-point-value"></span>
					</div>
				</div>

				<!-- Current Followers -->
				<div id="skystats-twitter-current-followers-data-point" class="skystats-service-detail-data-point-column skystats-mashboard-data-point-column">
					<div class="skystats-dashboard-data-point-header">
						<span class="skystats-dashboard-data-point-heading"><?php _e( 'Current Followers', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total amount of users who are following you.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-current-followers" class="skystats-data-point-value"></span>
					</div>
				</div>

				<!-- Current Mentions -->
				<div id="skystats-twitter-current-mentions-data-point" class="skystats-service-detail-data-point-column skystats-mashboard-data-point-column">
					<div class="skystats-dashboard-data-point-header">
						<span class="skystats-dashboard-data-point-heading"><?php _e( 'Current Mentions', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total amount of times you have been mentioned in a tweet by another user.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-twitter-current-mentions" class="skystats-data-point-value"></span>
					</div>
				</div>
			</div>

			<!-- Twitter Data Tables -->
			<div class="skystats-service-detail-data-tables-container">

				<div class="skystats-detail-data-table-row">

					<!-- Top 5 Latest Tweets -->
					<div class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-twitter-top-posts-data-table-column-content" class="skystats-data-table-column-content skystats-service-detail-data-table-column-content">
							<h2>
								<?php _e( 'Top 5 Tweets', SKYSTATS_TEXT_DOMAIN ); ?>
								<span class="skystats-data-point-heading-info skystats-data-table-heading-info skystats-tooltip" title="<?php _e( 'Top 5 latest tweets out of your 200 most recent tweets.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
							</h2>
							<div id="skystats-twitter-top-tweets-data-error-container" class="skystats-top-data-error-container">
								<p><?php _e( 'No data available for this period.', SKYSTATS_TEXT_DOMAIN ); ?></p>
							</div>
							<table id="skystats-twitter-top-latest-tweets" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'Text', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Retweets', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Favorites', SKYSTATS_TEXT_DOMAIN ); ?></th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

					<!-- Top 5 Latest Retweets -->
					<div class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-twitter-top-posts-data-table-column-content" class="skystats-data-table-column-content skystats-service-detail-data-table-column-content">
							<h2>
								<?php _e( 'Top 5 Retweets', SKYSTATS_TEXT_DOMAIN ); ?>
								<span class="skystats-data-point-heading-info skystats-data-table-heading-info skystats-tooltip" title="<?php _e( 'Top 5 latest retweets out of the 100 most recent retweets of your tweets.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
							</h2>
							<div id="skystats-twitter-top-retweets-data-error-container" class="skystats-top-data-error-container">
								<p><?php _e( 'No data available for this period.', SKYSTATS_TEXT_DOMAIN ); ?></p>
							</div>
							<table id="skystats-twitter-top-latest-retweets" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'Text', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Retweets', SKYSTATS_TEXT_DOMAIN ); ?></th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>

				<div class="skystats-detail-data-table-row">

					<!-- Top 5 Latest Mentions -->
					<div class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-twitter-top-posts-data-table-column-content" class="skystats-data-table-column-content skystats-service-detail-data-table-column-content">
							<h2>
								<?php _e( 'Top 5 Mentions', SKYSTATS_TEXT_DOMAIN ); ?>
								<span class="skystats-data-point-heading-info skystats-data-table-heading-info skystats-tooltip" title="<?php _e( 'Top 5 latest mentions out of the 200 most recent mentions of your screen name.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
							</h2>
							<div id="skystats-twitter-top-mentions-data-error-container" class="skystats-top-data-error-container">
								<p><?php _e( 'No data available for this period.', SKYSTATS_TEXT_DOMAIN ); ?></p>
							</div>
							<table id="skystats-twitter-top-latest-mentions" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'User', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Text', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Retweets', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Favorites', SKYSTATS_TEXT_DOMAIN ); ?></th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

					<!-- Top 5 Latest Favourites -->
					<div class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-twitter-top-posts-data-table-column-content" class="skystats-data-table-column-content skystats-service-detail-data-table-column-content">
							<h2>
								<?php _e( 'Top 5 Favorites', SKYSTATS_TEXT_DOMAIN ); ?>
								<span class="skystats-data-point-heading-info skystats-data-table-heading-info skystats-tooltip" title="<?php _e( 'Your top 5 latest favourites out of your 200 most recent favourites.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
							</h2>
							<div id="skystats-twitter-top-favourites-data-error-container" class="skystats-top-data-error-container">
								<p><?php _e( 'No data available for this period.', SKYSTATS_TEXT_DOMAIN ); ?></p>
							</div>
							<table id="skystats-twitter-top-latest-favourites" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'Text', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Favorites', SKYSTATS_TEXT_DOMAIN ); ?></th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>