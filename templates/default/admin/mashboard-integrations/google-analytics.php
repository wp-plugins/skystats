<?php

// Prevent direct access
defined( 'ABSPATH' ) or exit();

?>

<div id="googleanalytics_1" class="skystats-card-container">
	<div class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'Google Analytics', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<span id="skystats-google-analytics-settings-icon" class="skystats-settings-icon skystats-setting-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for Google Analytics', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
			<span id="skystats-google-analytics-grid-icon" class="skystats-grid-icon skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide Google Analytics Data Points', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
			<div id="skystats-google-analytics-grid-icon-content" class="skystats-grid-content">
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-google-analytics-users-data-point" checked>
						<?php _e( 'Users', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-google-analytics-page-views-data-point" checked>
						<?php _e( 'Page Views', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-google-analytics-bounce-rate-data-point" checked>
						<?php _e( 'Bounce Rate', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-google-analytics-search-engine-visits-data-point" checked>
						<?php _e( 'Search Engine Visits', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
			</div>
		</div>
		<div id="skystats-google-analytics-card-content" class="skystats-card-content">

			<!-- Google Analytics Specific Errors -->
			<div id="skystats-google-analytics-error-container" class="skystats-error-container">
				<p></p>
			</div>

			<!-- Google Analytics Loading Container -->
			<div id="skystats-google-analytics-loading-container" class="skystats-loading-container skystats-chart-loading-container">
				<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" height="64" width="64">
			</div>

			<!-- Google Analytics Settings Content -->
			<div id="skystats-google-analytics-settings-content">
				<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'google-analytics.php'; ?>
				<?php $api_authenticate_url = skystats_api_google_analytics_get_authorization_url( SKYSTATS_MASHBOARD_PAGE_URL ); ?>
				<!-- No profiles -->
				<div id="skystats-google-analytics-settings-no-profiles-section" class="skystats-google-analytics-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'No Profiles Found', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'No profiles found. Please try refreshing the page or deauthorizing below, then reauthorize with a Google Analytics account which has access to at least one profile.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				</div>
				<!-- Select a profile -->
				<div id="skystats-google-analytics-settings-profiles-section" class="skystats-google-analytics-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Select a Profile', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Select a profile from the list below to display data for. If you see any profile named "All Website Data", you can change this from within Google Analytics to make it easier to select a profile.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<select id="ga-profiles" class="skystats-card-settings-profiles" style="transition:none;">
						<option value="select"><?php _e( 'Select Profile', SKYSTATS_TEXT_DOMAIN ); ?></option>
					</select>
					<button id="save_ga_profile" class="skystats-settings-tab-save-data-button skystats-button"><?php _e( 'Save', SKYSTATS_TEXT_DOMAIN ); ?></button>
				</div>
				<!-- Setup / Authorize / Authenticate -->
				<div id="skystats-google-analytics-settings-setup-section"  class="skystats-google-analytics-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Click the button below to login to Google and allow the application to access your profile(s) analytics. You will then be able to see data for your profile(s). Please make sure you login with an account that has access to at least one profile\'s analytics.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<a id="skystats-google-analytics-authorize" href="<?php echo esc_attr( $api_authenticate_url ); ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
				<!-- Reauthorize / Reauthenticate -->
				<div id="skystats-google-analytics-settings-reauthorize-section"  class="skystats-google-analytics-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Reauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Sorry, an error occurred that requires you to reauthorize/reauthenticate with Google. This is likely due to either an expired access token, you removed the application from your account, or you logged out of your account and your session expired. Please click the button below to continue.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<a id="skystats-google-analytics-reauthorize" href="<?php echo esc_attr( $api_authenticate_url ); ?>" class="skystats-button"><?php _e( 'Reauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
				<!-- Deauthorize / Deauthenticate -->
				<div id="skystats-google-analytics-settings-deauthorize-section" class="skystats-google-analytics-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Purge all Google Analytics authentication and cache data from your local install.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<?php $ga_deauthorize_url = esc_attr( skystats_api_google_analytics_get_deauthorization_url( SKYSTATS_MASHBOARD_PAGE_URL ) ); ?>
					<a href="<?php echo $ga_deauthorize_url; ?>" id="skystats-google-analytics-deauthorize" class="skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
			</div>

			<!-- Google Analytics Data Content (chart & data points) -->
			<div id="skystats-google-analytics-data-content">
				<!-- Google Analytics Chart -->
				<div id="skystats-google-analytics-chart-container" class="skystats-mashboard-chart-container skystats-chart-container">
					<div id="skystats-google-analytics-chart" class="skystats-mashboard-chart"></div>
				</div>

				<!-- Google Analytics Data Points -->
				<div id="skystats-google-analytics-data-points-container" class="skystats-data-points-container">

					<!-- Google Analytics Users -->
					<div id="skystats-google-analytics-users-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span id="skystats-google-analytics-users-chart-key" class="skystats-dashboard-data-point-chart-key">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Users', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of users who visited your website during this period. There is currently a bug in Google Analytics that shows an incorrect value for Users. The value shown here is correct.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-google-analytics-users-total" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-google-analytics-users-change-direction"></span>
							<span id="skystats-google-analytics-users-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'users visited your website during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-google-analytics-users-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Google Analytics Page Views -->
					<div id="skystats-google-analytics-page-views-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span id="skystats-google-analytics-page-views-chart-key" class="skystats-dashboard-data-point-chart-key">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Page Views', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of page views during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-google-analytics-page-views-total" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-google-analytics-page-views-change-direction"></span>
							<span id="skystats-google-analytics-page-views-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'page views on your website during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-google-analytics-page-views-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Bounce Rate -->
					<div id="skystats-google-analytics-bounce-rate-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Bounce Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Bounce Rate is the percentage of single-page visits (i.e. visits in which the person left your site from the entrance page without interacting with the page). The values shown here are rounded to save space. Take a look at the Google Analytics detail page for the precise values.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div id="skystats-mashboard-google-analytics-bounce-rate-container">
							<div id="skystats-donut-content">
								<div id="skystats-google-analytics-bounce-rate" class="skystats-donut" style="height: 150px;"></div>
							</div>
						</div>
					</div>

					<!-- Search Engine Visits -->
					<div id="skystats-google-analytics-search-engine-visits-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Search Engine Visits', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of visits to your website from any search engine.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-google-analytics-search-engine-visits-total" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-google-analytics-search-engine-visits-change-direction"></span>
							<span id="skystats-google-analytics-search-engine-visits-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'search engine visits to your website during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-google-analytics-search-engine-visits-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>
				</div>

				<!-- Google Analytics Details -->
				<div class="skystats-card-details-container">
					<span class="skystats-card-details">
						<a class="skystats-card-details-link skystats-tooltip" href="<?php echo admin_url( 'admin.php?page=skystats-google-analytics' ); ?>" data-tooltip="<?php _e( 'View detailed information about your Google Analytics profile.', SKYSTATS_TEXT_DOMAIN ); ?>">
							<?php _e( 'View Details', SKYSTATS_TEXT_DOMAIN ); ?>
						</a>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>