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

	<!-- Google Adwords Container -->
	<div id="skystats-google-adwords-data-container" class="skystats-service-detail-container">

		<!-- Top Container (Header, Icons) -->
		<div class="skystats-detail-top-container">
			<h3 class="skystats-detail-header"><?php _e( 'Google Adwords', SKYSTATS_TEXT_DOMAIN ); ?>
				<div class="skystats-detail-icons">
					<span id="skystats-google-adwords-settings-icon" class="skystats-setting-tool-tip skystats-tooltip skystats-settings-icon skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for Google Adwords', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					<span id="skystats-google-adwords-grid-icon" class="skystats-grid-icon skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide Google Adwords Data Points', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					<div id="skystats-google-adwords-grid-icon-content" class="skystats-grid-content">
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-clicks-data-point" checked>
								<?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-avg-cost-per-click-data-point" checked>
								<?php _e( 'Avg. CPC', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-cost-data-point" checked>
								<?php _e( 'Cost', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-impressions-data-point" checked>
								<?php _e( 'Impressions', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-click-through-rate-data-point" checked>
								<?php _e( 'Click Through Rate', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-conversions-data-point" checked>
								<?php _e( 'Conversions', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-avg-cost-per-conversion-data-point" checked>
								<?php _e( 'Cost / Conv', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
					</div>
				</div>
			</h3>
			<!-- Integration specific error container -->
			<div id="skystats-google-adwords-error-container" class="skystats-integration-detail-error-container skystats-integration-error-container">
				<p></p>
			</div>
		</div>

		<!-- Chart Key / Figure -->
		<div id="skystats-detail-chart-key-container" class="skystats-service-detail-chart-key-container">
			<span id="skystats-google-adwords-clicks-chart-key-icon" class="skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?></span>
			<span id="skystats-google-adwords-avg-cost-per-click-chart-key-icon" class="skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Avg. CPC', SKYSTATS_TEXT_DOMAIN ); ?></span>
		</div>

		<!-- Loading Image -->
		<div id="skystats-google-adwords-loading-container" class="skystats-loading-container">
			<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64" alt="<?php _e( 'Loading', SKYSTATS_TEXT_DOMAIN ); ?>">
		</div>

		<!-- Google Adwords Settings Tab Content -->
		<div id="skystats-google-adwords-settings-content" class="skystats-settings-tab-settings-content">

			<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'google-adwords.php'; ?>

			<!-- Account Selection -->
			<div id="skystats-google-adwords-account-selection-section" class="skystats-google-adwords-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Select an Account', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( "Select an account from the list below. The account's campaigns will then be loaded for you.", SKYSTATS_TEXT_DOMAIN ); ?></p>
				<select id="skystats-google-adwords-account-selection" class="skystats-card-settings-profiles" style="transition:none;"></select>
			</div>

			<!-- Campaign Selection -->
			<div id="skystats-google-adwords-campaign-selection-section" class="skystats-google-adwords-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Select a Campaign', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'Select "All Data" to see data for all your account\'s active campaigns.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<p><?php printf( __( '<a href="%s">Upgrade to Premium</a> to be able to see data for individual campaigns.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_MARKETING_SITE_FREE_SIGNUP_PAGE_URL ); ?></p>
				<select id="skystats-google-adwords-campaign-selection" class="skystats-card-settings-profiles" style="transition:none;"></select>
				<button id="skystats-google-adwords-save-campaign" class="skystats-settings-tab-save-data-button skystats-button"><?php _e( 'Save', SKYSTATS_TEXT_DOMAIN ); ?></button>
			</div>

			<!-- Setup (Authorize/Authenticate) -->
			<div id="skystats-google-adwords-settings-authorize-section" class="skystats-google-adwords-settings-tab-section skystats-settings-tab-section">
				<div class="skystats-card-settings-authorize-container">
					<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Click the button below to login to Google and allow the application to access your account(s) and campaign(s). You will then be able to select a campaign to display data for. Please make sure you login with an account that has setup at least one campaign.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<?php $google_adwords_authorization_url = skystats_api_google_adwords_get_authorization_url( SKYSTATS_GOOGLE_ADWORDS_DETAIL_PAGE_URL ); ?>
					<a id="skystats-google-adwords-authorize" href="<?php echo esc_attr( $google_adwords_authorization_url ); ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
			</div>

			<!-- Deauthorize/Deauthenticate -->
			<div id="skystats-google-adwords-settings-deauthorize-section" class="skystats-google-adwords-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'Purge all Google Adwords authentication and cache data from your local install.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<?php $google_adwords_deauthorize_url = esc_attr( skystats_api_google_adwords_get_deauthorization_url( SKYSTATS_GOOGLE_ADWORDS_DETAIL_PAGE_URL ) ); ?>
				<a href="<?php echo $google_adwords_deauthorize_url; ?>" id="skystats-google-adwords-deauthorize" class="skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
			</div>
		</div>

		<!-- Data Tab Content -->
		<div id="skystats-google-adwords-data-tab-content">

			<!-- Chart -->
			<div id="skystats-google-adwords-chart-container" class="skystats-detail-chart-container skystats-chart-container">
				<div id="skystats-google-adwords-chart" class="skystats-detail-chart"></div>
			</div>

			<!-- Data Points -->
			<div id="skystats-google-adwords-data-points-container" class="skystats-data-points-container">

				<!-- Clicks -->
				<div id="skystats-google-adwords-clicks-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of times this campaign ad\'s have been clicked during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-adwords-clicks" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-adwords-clicks-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-adwords-clicks-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'Clicks were made during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-adwords-clicks-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Avg Cost Per Click -->
				<div id="skystats-google-adwords-avg-cost-per-click-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Avg. CPC', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The Average Cost Per Click (cost ÷ clicks) during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-adwords-avg-cost-per-click-currency" class="skystats-currency-data-point"></span>
						<span id="skystats-google-adwords-avg-cost-per-click" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-adwords-avg-cost-per-click-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-adwords-avg-cost-per-click-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the Average Cost Per Click in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-adwords-avg-cost-per-click-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Cost -->
				<div id="skystats-google-adwords-cost-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Cost', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total cost of this campaign during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-adwords-cost-currency" class="skystats-currency-data-point"></span>
						<span id="skystats-google-adwords-cost" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-adwords-cost-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-adwords-cost-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the Cost during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-adwords-cost-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Impressions -->
				<div id="skystats-google-adwords-impressions-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Impressions', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total number of times this campaign\'s ads have been displayed during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-adwords-impressions" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-adwords-impressions-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-adwords-impressions-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'Impressions were made in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-adwords-impressions-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>
	
				<!-- Click Through Rate -->
				<div id="skystats-google-adwords-click-through-rate-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Click Through Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'A ratio showing how often people who see your ad(s) end up clicking them (clicks ÷ impressions).', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-adwords-click-through-rate" class="skystats-data-point-value skystats-data-point-value-percentage"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-adwords-click-through-rate-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-adwords-click-through-rate-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the Click Through Rate in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-adwords-click-through-rate-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Conversions -->
				<div id="skystats-google-adwords-conversions-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Conversions', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of clicks that have resulted in some action you have defined (e.g. a sale) to your website during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-adwords-conversions" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-adwords-conversions-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-adwords-conversions-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'Conversions were made in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-adwords-conversions-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Avg Cost Per Conversion -->
				<div id="skystats-google-adwords-avg-cost-per-conversion-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Cost / Conv', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The Average Cost Per Conversion during this period (total cost ÷ total conversions).', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-adwords-avg-cost-per-conversion-currency" class="skystats-currency-data-point"></span>
						<span id="skystats-google-adwords-avg-cost-per-conversion" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-adwords-avg-cost-per-conversion-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-adwords-avg-cost-per-conversion-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the Average Cost Per Conversion during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-adwords-avg-cost-per-conversion-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Bounce Rate -->
				<div id="skystats-google-adwords-bounce-rate-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Bounce Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Bounce Rate is the percentage of single-page sessions (i.e. sessions in which the person left your site from the entrance page without interacting with the page).', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-google-adwords-bounce-rate" class="skystats-data-point-value  skystats-data-point-value-percentage"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-google-adwords-bounce-rate-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-google-adwords-bounce-rate-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the Bounce Rate during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-google-adwords-bounce-rate-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>
			</div>
			<!-- Tables -->
			<div class="skystats-service-detail-data-tables-container">
				<div class="skystats-detail-data-table-row">

					<!-- Campaigns (account-level) -->
					<div id="skystats-detail-google-adwords-account-level-campaigns-data-table-column" class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-google-adwords-account-level-campaigns-data-table-column-content" class="skystats-data-table-column-content skystats-service-detail-data-table-column-content">
							<h2>
								<?php _e( 'Campaigns', SKYSTATS_TEXT_DOMAIN ); ?>
								<span class="skystats-data-point-heading-info skystats-data-table-heading-info skystats-tooltip" title="<?php _e( 'The Bounce Rate over the current period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
							</h2>
							<div id="skystats-google-adwords-account-level-campaigns-data-error-container" class="skystats-top-data-error-container">
								<p><?php _e( 'No data available for this period.', SKYSTATS_TEXT_DOMAIN ); ?></p>
							</div>
							<table id="skystats-google-adwords-account-level-campaigns-table" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'Campaign Name', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Impressions', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'CTR', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Avg. CPC', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Cost', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Conversions', SKYSTATS_TEXT_DOMAIN ); ?></th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>

					<!-- Top Keyword Performance (account-level) -->
					<div id="skystats-detail-google-adwords-account-level-top-keyword-performance-data-table-column" class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-google-adwords-account-level-top-keyword-performance-data-table-column-content" class="skystats-data-table-column-content skystats-service-detail-data-table-column-content">
							<h2>
								<?php _e( 'Top Keyword Performance', SKYSTATS_TEXT_DOMAIN ); ?>
								<span class="skystats-data-point-heading-info skystats-data-table-heading-info skystats-tooltip" title="<?php _e( 'The Bounce Rate over the current period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
							</h2>
							<div id="skystats-google-adwords-account-level-top-keyword-performance-data-error-container" class="skystats-top-data-error-container">
								<p><?php _e( 'No data available for this period.', SKYSTATS_TEXT_DOMAIN ); ?></p>
							</div>
							<table id="skystats-google-adwords-account-level-top-keyword-performance-table" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
								<tr>
									<th><?php _e( 'Keyword', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Campaign Name', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Impressions', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'CTR', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Avg. CPC', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Cost', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Conversions', SKYSTATS_TEXT_DOMAIN ); ?></th>
								</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div> <!-- .skystats-detail-data-table-row -->
			</div> <!-- .skystats-service-detail-data-tables-container -->
		</div>
	</div>
</div>