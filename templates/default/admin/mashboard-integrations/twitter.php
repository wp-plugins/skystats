<?php

defined( 'ABSPATH' ) or exit();

?>

<div id="twitter_3" class="skystats-card-container">
	<div class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'Twitter', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<!-- Twitter Settings Icon -->
			<span id="skystats-twitter-settings-icon" class="skystats-card-settings skystats-setting-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for Twitter', SKYSTATS_TEXT_DOMAIN ); ?>"></span>

			<!-- Twitter Grid Icon -->
			<span id="skystats-twitter-grid-icon" class="skystats-card-eye skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide Twitter Data Points', SKYSTATS_TEXT_DOMAIN ); ?>"></span>

			<!-- Twitter Grid Icon Content -->
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
			</div>
		</div>
		<div id="skystats-twitter-card-content" class="skystats-card-content">

			<!-- Chart Loading Icon -->
			<div id="skystats-twitter-loading-container" class="skystats-loading-container skystats-chart-loading-container">
				<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
			</div>

			<!-- Twitter Settings Tab Data -->
			<div id="skystats-twitter-settings-content" class="skystats-settings-tab-settings-content">

				<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'twitter.php'; ?>
				<?php $twitter_authorization_url = skystats_api_twitter_get_authorization_url( SKYSTATS_MASHBOARD_PAGE_URL ); ?>

				<!-- Valid Access Token -->
				<div id="skystats-twitter-settings-valid-access-token-section" class="skystats-twitter-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Valid Access Token', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'This means everything is working correctly and you haven\'t revoked the SkyStats Twitter application\'s access to your Twitter account.', SKYSTATS_TEXT_DOMAIN ); ?></p>
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
				<!-- Deauthorize / Deauthenticate -->
				<div id="skystats-twitter-settings-deauthorize-section" class="skystats-twitter-settings-tab-section skystats-settings-tab-section">
					<div class="skystats-card-settings-authorize-container">
						<h3><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
						<p><?php _e( 'Purge all Twitter authentication and cache data from your local install.', SKYSTATS_TEXT_DOMAIN ); ?></p>
						<p><strong><?php _e( 'All data that was collected will also be deleted.', SKYSTATS_TEXT_DOMAIN ); ?></strong></p>
						<?php $twitter_deauthorize_url = esc_attr( skystats_api_twitter_get_deauthorization_url( SKYSTATS_MASHBOARD_PAGE_URL ) ); ?>
						<a href="<?php echo $twitter_deauthorize_url; ?>" id="skystats-twitter-deauthorize" class="skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
					</div>
				</div>
				<!-- Setup / Authorize / Authenticate -->
				<div id="skystats-twitter-settings-authorize-section" class="skystats-twitter-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Click the button below to login to Twitter and authorize the application.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<p><strong><?php _e( 'Note: Full historical data for Twitter is not available. Data will be collected for you each day after you setup the integration and until you deauthorize.', SKYSTATS_TEXT_DOMAIN ); ?></strong></p>
					<a id="skystats-twitter-authorize" href="<?php echo esc_attr( $twitter_authorization_url ); ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
			</div>

			<!-- Twitter Data Tab Data -->
			<div id="skystats-twitter-data-tab-content">

				<!-- Twitter Chart -->
				<div id="skystats-twitter-chart-container" class="skystats-mashboard-chart-container skystats-chart-container">
					<div id="skystats-twitter-chart" class="skystats-mashboard-chart"></div>
				</div>

				<!-- Twitter Data Points -->
				<div id="skystats-twitter-data-points-container" class="skystats-data-points-container">

					<!-- Favourites -->
					<div id="skystats-twitter-favourites-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span id="skystats-twitter-favorites-chart-key-icon">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Favorites', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of your tweets that have been favorited by other users, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-twitter-favourites" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-twitter-favourites-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-twitter-favourites-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'is the amount of times your Tweets have been favorited, by other users, during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-twitter-favourites-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Mentions -->
					<div id="skystats-twitter-mentions-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span id="skystats-twitter-mentions-chart-key-icon">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Mentions', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of times other users have mentioned you in a tweet, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-twitter-mentions" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-twitter-mentions-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-twitter-mentions-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'is the amount of times you were mentioned in a tweet, by another user, in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-twitter-mentions-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Retweets -->
					<div id="skystats-twitter-retweets-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Retweets', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of your tweets that have been retweeted by other users, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-twitter-retweets" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-twitter-retweets-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-twitter-retweets-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'is the amount of times your tweets were retweeted, by other users, during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-twitter-retweets-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- New Followers -->
					<div id="skystats-twitter-followers-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'New Followers', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of users who have followed you during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-twitter-followers" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-twitter-followers-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-twitter-followers-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'people followed you in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-twitter-followers-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Tweets -->
					<div id="skystats-twitter-tweets-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Tweets', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of tweets posted to Twitter, including retweets and replies, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-twitter-tweets" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-twitter-tweets-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-twitter-tweets-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'tweets were posted by you in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-twitter-tweets-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Following -->
					<div id="skystats-twitter-following-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Following', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of users you have followed during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-twitter-following" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-twitter-following-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-twitter-following-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'is the amount of people you followed in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-twitter-following-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>
				</div>

				<!-- Details -->
				<div class="skystats-card-details-container">
					<span class="skystats-card-details">
						<a class="skystats-card-details-link skystats-tooltip" href="<?php echo admin_url( 'admin.php?page=skystats-twitter' ); ?>" data-tooltip="<?php _e( 'View detailed information about your Twitter account.', SKYSTATS_TEXT_DOMAIN ); ?>">
							<?php _e( 'View Details', SKYSTATS_TEXT_DOMAIN ); ?>
						</a>
					</span>
				</div>
			</div> <!-- #skystats-twitter-data-tab-content -->
		</div> <!-- #skystats-twitter-card-content -->
	</div> <!-- .skystats-card -->
</div> <!-- #twitter_3# -->