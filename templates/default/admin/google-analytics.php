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
		<p id="skystats-free-data-range-notice" class="skystats-success-message"><?php printf( __( 'Currently displaying the past 30 days. Upgrade to <a href="%s">SkyStats Pro</a> to access more than 30 days of data.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_MARKETING_SITE_PRICING_SECTION_URL ); ?></p>
	</div>

	<!-- Page Loading Container -->
	<div id="skystats-page-loading-container" class="skystats-loading-container">
		<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
	</div>

	<!-- Page Error Container -->
	<div id="skystats-page-error-container" class="skystats-page-error-container skystats-error-container">
		<p></p>
	</div>

	<!-- Page Data -->
	<div id="skystats-google-analytics-data-container" class="skystats-service-detail-container">

		<!-- Top Container (Header, Icons) -->
		<div class="skystats-detail-top-container">
			<h3 class="skystats-detail-header"><?php _e( 'Google Analytics', SKYSTATS_TEXT_DOMAIN ); ?>
				<div class="skystats-detail-icons">
					<span id="skystats-google-analytics-settings-icon" class="skystats-setting-tool-tip skystats-tooltip skystats-disabled skystats-settings-icon skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for Google Analytics', SKYSTATS_TEXT_DOMAIN ); ?>">
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
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-analytics-pages-per-visit-data-point" checked>
								<?php _e( 'Pages Per Visit', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-analytics-average-visit-duration-data-point" checked>
								<?php _e( 'Average Visit Duration', SKYSTATS_TEXT_DOMAIN ); ?>
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
			</h3>
		</div>

		<!-- Data Loading Icon Container -->
		<div id="skystats-google-analytics-loading-container" class="skystats-loading-container">
			<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
		</div>

		<!-- Settings Tab & Content -->
		<div id="skystats-google-analytics-settings-content" class="skystats-settings-tab-settings-content">
			<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'google-analytics.php'; ?>
			<?php $api_authenticate_url = skystats_api_google_analytics_get_authorization_url( SKYSTATS_GOOGLE_ANALYTICS_DETAIL_PAGE_URL ); ?>
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
				<?php $ga_deauthorize_url = esc_attr( skystats_api_google_analytics_get_deauthorization_url( SKYSTATS_GOOGLE_ANALYTICS_DETAIL_PAGE_URL ) ); ?>
				<a href="<?php echo $ga_deauthorize_url; ?>" id="skystats-google-analytics-deauthorize" class="skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
			</div>
		</div>

		<!-- Chart Key / Figure -->
		<div id="skystats-detail-chart-key-container" class="skystats-service-detail-chart-key-container">
			<span class="skystats-yellow-chart-key-icon skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Users', SKYSTATS_TEXT_DOMAIN ); ?></span>
			<span class="skystats-orange-chart-key-icon skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Page Views', SKYSTATS_TEXT_DOMAIN ); ?></span>
		</div>

		<!-- Data Tab & Content -->
		<div id="skystats-google-analytics-data-content">

			<!-- Chart -->
			<div id="skystats-google-analytics-chart-container" class="skystats-detail-chart-container skystats-chart-container">
				<div id="skystats-google-analytics-chart" class="skystats-detail-chart"></div>
			</div>

			<!-- Google Analytics Data Points -->
			<div id="skystats-google-analytics-data-points-container" class="skystats-data-points-container">

				<!-- Users / Unique Visitors -->
				<div id="skystats-google-analytics-users-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Users', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-tooltip skystats-data-point-info-icon" data-tooltip="<?php _e( 'Total number of users who visited your website during this period. There is currently a bug in Google Analytics that shows an incorrect value for Users. The value shown here is correct.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-analytics-users-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-analytics-users-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-analytics-users-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'users visited your website during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-analytics-users-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Page Views -->
				<div id="skystats-google-analytics-page-views-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Page Views', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-tooltip skystats-data-point-info-icon" data-tooltip="<?php _e( 'Total number of page views during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-analytics-page-views-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-analytics-page-views-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-analytics-page-views-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'page views on your website during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-analytics-page-views-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Pages Per Visit -->
				<div id="skystats-google-analytics-pages-per-visit-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Pages Per Visit', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-tooltip skystats-data-point-info-icon" data-tooltip="<?php _e( 'The average number of pages viewed per session. Repeated page views are also included.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-analytics-pages-per-visit-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-analytics-pages-per-visit-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-analytics-pages-per-visit-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the average of page visits during a session in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-analytics-pages-per-visit-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Average Visit Duration (Minutes: Seconds) -->
				<div id="skystats-google-analytics-average-visit-duration-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Average Visit Duration (min:sec)', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-tooltip skystats-data-point-info-icon" data-tooltip="<?php _e( 'The average duration your website users were actively engaged with your site, during the selected period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-analytics-average-visit-duration-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-analytics-average-visit-duration-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-analytics-average-visit-duration-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the average visit duration during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-analytics-average-visit-duration-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Bounce Rate -->
				<div id="skystats-google-analytics-bounce-rate-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Bounce Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-tooltip skystats-data-point-info-icon" data-tooltip="<?php _e( 'Bounce Rate is the percentage of single-page visits (i.e. visits in which the person left your site from the entrance page without interacting with the page).', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-analytics-bounce-rate-total" class="skystats-data-point-value skystats-data-point-value-percentage"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-analytics-bounce-rate-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-analytics-bounce-rate-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'single-page visits during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-analytics-bounce-rate-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Search Engine Visits -->
				<div id="skystats-google-analytics-search-engine-visits-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Search Engine Visits', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-tooltip skystats-data-point-info-icon" data-tooltip="<?php _e( 'Total number of visits to your website from any search engine.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-analytics-search-engine-visits-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-analytics-search-engine-visits-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-analytics-search-engine-visits-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'search engine visits to your website during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-analytics-search-engine-visits-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>
			</div>

			<!-- Google Analytics Data Tables -->
			<div class="skystats-service-detail-data-tables-container">

				<div class="skystats-detail-data-table-row">

					<!-- Top Keywords -->
					<div class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-google-analytics-top-keywords-data-table-column-content" class="skystats-data-table-column-content">
							<h2><?php _e( 'Top 5 Keywords', SKYSTATS_TEXT_DOMAIN ); ?></h2>
							<table id="skystats-google-analytics-top-keywords-data-table" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'Keyword', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Sessions', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th>%</th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

					<!-- Top Search Engine Referrals -->
					<div class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-google-analytics-top-search-engine-referrals-data-table-column-content" class="skystats-data-table-column-content">
							<h2><?php _e( 'Top 5 Search Engine Referrals', SKYSTATS_TEXT_DOMAIN ); ?></h2>
							<table id="skystats-google-analytics-top-search-engine-referrals-data-table" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'Search Engine', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Sessions', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th>%</th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div> <!-- .skystats-detail-data-table-row -->

				<div class="skystats-detail-data-table-row">
					<!-- Top Landing Pages -->
					<div class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-google-analytics-top-landing-pages-data-table-column-content" class="skystats-data-table-column-content">
							<h2><?php _e( 'Top 5 Landing Pages', SKYSTATS_TEXT_DOMAIN ); ?></h2>
							<table id="skystats-google-analytics-top-landing-pages-data-table" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'URL', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Sessions', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th>%</th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

					<!-- Visitor Locations -->
					<div class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-google-analytics-top-visitor-locations-data-table-column-content" class="skystats-data-table-column-content">
							<h2><?php _e( 'Top 5 Visitor Locations', SKYSTATS_TEXT_DOMAIN ); ?></h2>
							<table id="skystats-google-analytics-visitor-locations-data-table" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'Country', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Sessions', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th>%</th>
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
</div> <!-- .wrap -->