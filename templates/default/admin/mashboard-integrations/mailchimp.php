<?php

// Prevent direct access
defined( 'ABSPATH' ) or exit();

?>

<div id="mailchimp_8" class="skystats-card-container">
	<div class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'MailChimp', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<span id="skystats-mailchimp-settings-icon" class="skystats-settings-icon skystats-setting-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for MailChimp', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
			<span id="skystats-mailchimp-grid-icon" class="skystats-grid-icon skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide MailChimp Data Points', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
			<div id="skystats-mailchimp-grid-icon-content" class="skystats-grid-content">
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-unique-opens-data-point" checked>
						<?php _e( 'Opens', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-unique-clicks-data-point" checked>
						<?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-unsubscribed-data-point" checked>
						<?php _e( 'Unsubscribed', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-subscribers-data-point" checked>
						<?php _e( 'Subscribers', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-open-rate-data-point">
						<?php _e( 'Open Rate', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
				<p>
					<label>
						<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-click-rate-data-point">
						<?php _e( 'Click Rate', SKYSTATS_TEXT_DOMAIN ); ?>
					</label>
				</p>
			</div>
		</div>
		<div id="skystats-mailchimp-card-content" class="skystats-card-content">

			<!-- Integration specific error container -->
			<div id="skystats-mailchimp-error-container" class="skystats-integration-error-container">
				<p></p>
			</div>

			<!-- Loading Container -->
			<div id="skystats-mailchimp-loading-container" class="skystats-loading-container skystats-chart-loading-container">
				<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" height="64" width="64">
			</div>

			<!-- Settings Content -->
			<div id="skystats-mailchimp-settings-content">
				<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'mailchimp.php'; ?>
				<?php $api_authenticate_url = skystats_api_mailchimp_get_authorization_url( SKYSTATS_MASHBOARD_PAGE_URL ); ?>

				<!-- Valid Access Token -->
				<div id="skystats-mailchimp-settings-valid-access-token-section" class="skystats-mailchimp-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Valid Access Token', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'This means everything is working correctly and you haven\'t revoked the SkyStats MailChimp application\'s access to your MailChimp account.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<p><?php _e( 'If you want to go back to your data, click the Settings icon again. If you want to use a different account, you\'ll need to Deauthorize.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				</div>

				<!-- Invalid Access Token -->
				<div id="skystats-mailchimp-settings-invalid-access-token-section" class="skystats-mailchimp-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Invalid Access Token', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'This means you have either revoked the SkyStats application\'s access to your MailChimp account manually, it was invalidated by MailChimp, or you deauthorized', SKYSTATS_TEXT_DOMAIN ); ?></p>
				</div>

				<!-- Setup / Authorize / Authenticate -->
				<div id="skystats-mailchimp-settings-authorize-section"  class="skystats-mailchimp-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Click the button below to login to your MailChimp account and authorize the SkyStats application to allow us to display data for your campaigns.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<a id="skystats-mailchimp-authorize" href="<?php echo esc_attr( $api_authenticate_url ); ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>

				<!-- Deauthorize / Deauthenticate -->
				<div id="skystats-mailchimp-settings-deauthorize-section" class="skystats-mailchimp-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Purge all MailChimp authentication and cache data from your local install.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<?php $ga_deauthorize_url = esc_attr( skystats_api_mailchimp_get_deauthorization_url( SKYSTATS_MASHBOARD_PAGE_URL ) ); ?>
					<a href="<?php echo $ga_deauthorize_url; ?>" id="skystats-mailchimp-deauthorize" class="skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
			</div>

			<!-- Data Content (chart & data points) -->
			<div id="skystats-mailchimp-data-content">

				<!-- Chart -->
				<div id="skystats-mailchimp-chart-container" class="skystats-mashboard-chart-container skystats-chart-container">
					<div id="skystats-mailchimp-chart" class="skystats-mashboard-chart"></div>
				</div>

				<!-- Data Points -->
				<div id="skystats-mailchimp-data-points-container" class="skystats-data-points-container">

					<!-- Opens (Unique) -->
					<div id="skystats-mailchimp-unique-opens-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-mailchimp-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span id="skystats-mailchimp-unique-opens-chart-key-icon" class="skystats-dashboard-data-point-chart-key">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Unique Opens', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total number of unique subscribers who opened your campaigns during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-mailchimp-unique-opens" class="skystats-data-point-value skystats-mailchimp-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-mailchimp-unique-opens-change-direction"></span>
							<span id="skystats-mailchimp-unique-opens-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'Unique Opens were made during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-mailchimp-unique-opens-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Clicks (Unique) -->
					<div id="skystats-mailchimp-unique-clicks-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-mailchimp-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span id="skystats-mailchimp-unique-clicks-chart-key-icon" class="skystats-dashboard-data-point-chart-key">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Unique Clicks', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total number of unique clicks for links across your campaigns during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-mailchimp-unique-clicks" class="skystats-data-point-value skystats-mailchimp-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-mailchimp-unique-clicks-change-direction"></span>
							<span id="skystats-mailchimp-unique-clicks-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'Unique Clicks were made in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-mailchimp-unique-clicks-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Unsubscribed -->
					<div id="skystats-mailchimp-unsubscribed-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-mailchimp-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Unsubscribed', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total number of users who unsubscribed during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-mailchimp-unsubscribed" class="skystats-data-point-value skystats-mailchimp-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-mailchimp-unsubscribed-change-direction"></span>
							<span id="skystats-mailchimp-unsubscribed-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'users unsubscribed in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-mailchimp-unsubscribed-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Subscribers -->
					<div id="skystats-mailchimp-subscribers-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-mailchimp-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Subscribers', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of subscribers your campaigns were sent to during this period (whether successful or not)', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-mailchimp-subscribers" class="skystats-data-point-value skystats-mailchimp-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-mailchimp-subscribers-change-direction"></span>
							<span id="skystats-mailchimp-subscribers-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'was the total number of Subscribers your campaigns were sent to during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-mailchimp-subscribers-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Open Rate -->
					<div id="skystats-mailchimp-open-rate-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-mailchimp-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Open Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Percentage of successfully delivered campaigns that registered as an open during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-mailchimp-open-rate" class="skystats-data-point-value skystats-mailchimp-data-point-value  skystats-data-point-value-percentage"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-mailchimp-open-rate-change-direction"></span>
							<span id="skystats-mailchimp-open-rate-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'was the Open Rate during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-mailchimp-open-rate-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Click Rate -->
					<div id="skystats-mailchimp-click-rate-data-point" class="skystats-mashboard-data-point-column skystats-mashboard-mailchimp-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Click Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Percentage of successfully delivered campaigns that registered a click during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-mailchimp-click-rate" class="skystats-data-point-value skystats-mailchimp-data-point-value  skystats-data-point-value-percentage"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-mailchimp-click-rate-change-direction"></span>
							<span id="skystats-mailchimp-click-rate-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'was the Click Rate during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-mailchimp-click-rate-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>
				</div>

				<!-- MailChimp Details -->
				<div class="skystats-card-details-container">
					<span class="skystats-card-details">
						<a class="skystats-card-details-link skystats-tooltip" href="<?php echo SKYSTATS_MAILCHIMP_DETAIL_PAGE_URL; ?>" data-tooltip="<?php _e( 'View detailed information about your MailChimp campaigns.', SKYSTATS_TEXT_DOMAIN ); ?>">
							<?php _e( 'View Details', SKYSTATS_TEXT_DOMAIN ); ?>
						</a>
					</span>
				</div>
			</div>
		</div>
	</div>
</div>