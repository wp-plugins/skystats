<?php

// Prevent direct access
defined( 'ABSPATH' ) or exit();

?>

<div id="googleadwords_11" class="skystats-card-container">
	<div class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'Google Adwords', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<span id="skystats-google-adwords-settings-icon" class="skystats-settings-icon skystats-setting-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for Google Adwords', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
			<span id="skystats-google-adwords-grid-icon" class="skystats-grid-icon skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide Google Adwords Data Points', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
			<div id="skystats-google-adwords-grid-icon-content" class="skystats-grid-content">
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-cost-data-point" checked>
						<?php _e( 'Cost', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
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
						<input type="checkbox" class="skystats-show-data-point" value="skystats-google-adwords-conversions-data-point" checked>
						<?php _e( 'Conversions', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
			</div>
		</div>
		<div id="skystats-google-adwords-card-content" class="skystats-card-content">

			<!-- Integration specific error container -->
			<div id="skystats-google-adwords-error-container" class="skystats-integration-error-container">
				<p></p>
			</div>

			<!-- Loading Container -->
			<div id="skystats-google-adwords-loading-container" class="skystats-loading-container skystats-chart-loading-container">
				<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" height="64" width="64">
			</div>

			<!-- Settings Content -->
			<div id="skystats-google-adwords-settings-content">
				<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'google-adwords.php'; ?>
				<?php $api_authenticate_url = skystats_api_google_adwords_get_authorization_url( SKYSTATS_MASHBOARD_PAGE_URL ); ?>

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

				<!-- Setup / Authorize / Authenticate -->
				<div id="skystats-google-adwords-settings-authorize-section"  class="skystats-google-adwords-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Click the button below to login to Google and allow the application to access your account(s) and campaign(s). You will then be able to select a campaign to display data for. Please make sure you login with an account that has setup at least one campaign.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<a id="skystats-google-adwords-authorize" href="<?php echo esc_attr( $api_authenticate_url ); ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>

				<!-- Deauthorize / Deauthenticate -->
				<div id="skystats-google-adwords-settings-deauthorize-section" class="skystats-google-adwords-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Purge all Google Adwords authentication and cache data from your local install.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<?php $ga_deauthorize_url = esc_attr( skystats_api_google_adwords_get_deauthorization_url( SKYSTATS_MASHBOARD_PAGE_URL ) ); ?>
					<a href="<?php echo $ga_deauthorize_url; ?>" id="skystats-google-adwords-deauthorize" class="skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
			</div>

			<!-- Data Content (chart & data points) -->
			<div id="skystats-google-adwords-data-content">

				<!-- Chart -->
				<div id="skystats-google-adwords-chart-container" class="skystats-mashboard-chart-container skystats-chart-container">
					<div id="skystats-google-adwords-chart" class="skystats-mashboard-chart"></div>
				</div>

				<!-- Data Points -->
				<div id="skystats-google-adwords-data-points-container" class="skystats-data-points-container">

					<!-- Clicks -->
					<div id="skystats-google-adwords-clicks-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-google-adwords-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span id="skystats-google-adwords-clicks-chart-key-icon" class="skystats-dashboard-data-point-chart-key">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of times this campaign ad\'s have been clicked during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-google-adwords-clicks" class="skystats-data-point-value skystats-google-adwords-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-google-adwords-clicks-change-direction"></span>
							<span id="skystats-google-adwords-clicks-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'Clicks were made during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-google-adwords-clicks-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Avg Cost Per Click -->
					<div id="skystats-google-adwords-avg-cost-per-click-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-google-adwords-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span id="skystats-google-adwords-avg-cost-per-click-chart-key-icon" class="skystats-dashboard-data-point-chart-key">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Avg. CPC', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The Average Cost Per Click (cost ÷ clicks) during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-google-adwords-avg-cost-per-click-currency" class="skystats-currency-data-point"></span>
							<span id="skystats-google-adwords-avg-cost-per-click" class="skystats-data-point-value skystats-google-adwords-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-google-adwords-avg-cost-per-click-change-direction"></span>
							<span id="skystats-google-adwords-avg-cost-per-click-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'was the Average Cost Per Click in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-google-adwords-avg-cost-per-click-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Cost -->
					<div id="skystats-google-adwords-cost-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-google-adwords-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Cost', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total cost of this campaign during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-google-adwords-cost-currency" class="skystats-currency-data-point"></span>
							<span id="skystats-google-adwords-cost" class="skystats-data-point-value skystats-google-adwords-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-google-adwords-cost-change-direction"></span>
							<span id="skystats-google-adwords-cost-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'was the Cost during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-google-adwords-cost-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Conversions -->
					<div id="skystats-google-adwords-conversions-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-google-adwords-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Conversions', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of clicks that have resulted in some action you have defined (e.g. a sale) to your website during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-google-adwords-conversions" class="skystats-data-point-value skystats-google-adwords-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-google-adwords-conversions-change-direction"></span>
							<span id="skystats-google-adwords-conversions-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'Conversions were made in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-google-adwords-conversions-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>
				</div>

				<!-- Google Adwords Details -->
				<div class="skystats-card-details-container">
					<span class="skystats-card-details">
						<a class="skystats-card-details-link skystats-tooltip" href="<?php echo admin_url( 'admin.php?page=skystats-google-adwords' ); ?>" data-tooltip="<?php _e( 'View detailed information about your Google Adwords campaign.', SKYSTATS_TEXT_DOMAIN ); ?>">
							<?php _e( 'View Details', SKYSTATS_TEXT_DOMAIN ); ?>
						</a>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>