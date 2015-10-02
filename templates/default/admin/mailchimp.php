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

	<!-- MailChimp Container -->
	<div id="skystats-mailchimp-data-container" class="skystats-service-detail-container">

		<!-- Top Container (Header, Icons) -->
		<div class="skystats-detail-top-container">
			<h3 class="skystats-detail-header"><?php _e( 'MailChimp', SKYSTATS_TEXT_DOMAIN ); ?>
				<div class="skystats-detail-icons">
					<span id="skystats-mailchimp-settings-icon" class="skystats-setting-tool-tip skystats-tooltip skystats-settings-icon skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for MailChimp', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					<span id="skystats-mailchimp-grid-icon" class="skystats-grid-icon skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide MailChimp Data Points', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
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
								<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-open-rate-data-point" checked>
								<?php _e( 'Open Rate', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-click-rate-data-point" checked>
								<?php _e( 'Click Rate', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-industry-average-open-rate-data-point" checked>
								<?php _e( 'Industry Average Open Rate', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-mailchimp-bounces-data-point" checked>
								<?php _e( 'Bounced', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
					</div> <!-- #skystats-mailchimp-grid-icon-content -->
				</div> <!-- .skystats-detail-icons -->
			</h3>
			<!-- Integration specific error container -->
			<div id="skystats-mailchimp-error-container" class="skystats-integration-detail-error-container skystats-integration-error-container">
				<p></p>
			</div>
		</div> <!-- .skystats-detail-top-container -->

		<!-- Chart Key / Figure -->
		<div id="skystats-detail-chart-key-container" class="skystats-service-detail-chart-key-container">
			<span id="skystats-mailchimp-unique-opens-chart-key-icon" class="skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Opens', SKYSTATS_TEXT_DOMAIN ); ?></span>
			<span id="skystats-mailchimp-unique-clicks-chart-key-icon" class="skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?></span>
		</div>

		<!-- Loading Image -->
		<div id="skystats-mailchimp-loading-container" class="skystats-loading-container">
			<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64" alt="<?php _e( 'Loading', SKYSTATS_TEXT_DOMAIN ); ?>">
		</div>

		<!-- MailChimp Settings Tab Content -->
		<div id="skystats-mailchimp-settings-content" class="skystats-settings-tab-settings-content">

			<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'mailchimp.php'; ?>

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

			<!-- Setup (Authorize/Authenticate) -->
			<div id="skystats-mailchimp-settings-authorize-section" class="skystats-mailchimp-settings-tab-section skystats-settings-tab-section">
				<div class="skystats-card-settings-authorize-container">
					<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Click the button below to login to your MailChimp account and authorize the SkyStats application to allow us to display data for your campaigns.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<?php $mailchimp_authorization_url = skystats_api_mailchimp_get_authorization_url( SKYSTATS_MAILCHIMP_DETAIL_PAGE_URL ); ?>
					<a id="skystats-mailchimp-authorize" href="<?php echo esc_attr( $mailchimp_authorization_url ); ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
			</div>

			<!-- Deauthorize/Deauthenticate -->
			<div id="skystats-mailchimp-settings-deauthorize-section" class="skystats-mailchimp-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'Purge all MailChimp authentication and cache data from your local install.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<?php $mailchimp_deauthorize_url = esc_attr( skystats_api_mailchimp_get_deauthorization_url( SKYSTATS_MAILCHIMP_DETAIL_PAGE_URL ) ); ?>
				<a href="<?php echo $mailchimp_deauthorize_url; ?>" id="skystats-mailchimp-deauthorize" class="skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
			</div>
		</div>

		<!-- Data Tab Content -->
		<div id="skystats-mailchimp-data-tab-content">

			<!-- Chart -->
			<div id="skystats-mailchimp-chart-container" class="skystats-detail-chart-container skystats-chart-container">
				<div id="skystats-mailchimp-chart" class="skystats-detail-chart"></div>
			</div>

			<!-- Data Points -->
			<div id="skystats-mailchimp-data-points-container" class="skystats-data-points-container">

				<!-- Opens (Unique) -->
				<div id="skystats-mailchimp-unique-opens-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Opens', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total number of unique subscribers who opened your campaigns during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-mailchimp-unique-opens" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-mailchimp-unique-opens-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-mailchimp-unique-opens-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'Opens were made during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-mailchimp-unique-opens-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Clicks (Unique) -->
				<div id="skystats-mailchimp-unique-clicks-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total number of unique clicks for links across your campaigns during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-mailchimp-unique-clicks" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-mailchimp-unique-clicks-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-mailchimp-unique-clicks-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'Clicks were made during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-mailchimp-unique-clicks-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Unsubscribed -->
				<div id="skystats-mailchimp-unsubscribed-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Unsubscribed', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The total number of users who unsubscribed during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-mailchimp-unsubscribed" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-mailchimp-unsubscribed-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-mailchimp-unsubscribed-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'users unsubscribed in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-mailchimp-unsubscribed-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Subscribers -->
				<div id="skystats-mailchimp-subscribers-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Subscribers', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of subscribers your campaigns were sent to during this period (whether successful or not)', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-mailchimp-subscribers" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-mailchimp-subscribers-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-mailchimp-subscribers-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'Subscribers were made during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-mailchimp-subscribers-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Open Rate -->
				<div id="skystats-mailchimp-open-rate-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Open Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Percentage of successfully delivered campaigns that registered as an open during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-mailchimp-open-rate" class="skystats-data-point-value skystats-data-point-value-percentage"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-mailchimp-open-rate-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-mailchimp-open-rate-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the Open Rate during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-mailchimp-open-rate-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Click Rate -->
				<div id="skystats-mailchimp-click-rate-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Click Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Percentage of successfully delivered campaigns that registered a click during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-mailchimp-click-rate" class="skystats-data-point-value skystats-data-point-value-percentage"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-mailchimp-click-rate-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-mailchimp-click-rate-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the Click Rate during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-mailchimp-click-rate-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Industry Average Open Rate -->
				<div id="skystats-mailchimp-industry-average-open-rate-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Industry Avg Open Rate', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Average percentage of opens for campaigns in the selected industry set for your account.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-mailchimp-industry-average-open-rate" class="skystats-data-point-value skystats-data-point-value-percentage"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-mailchimp-industry-average-open-rate-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-mailchimp-industry-average-open-rate-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'was the Industry Average Open Rate in the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-mailchimp-industry-average-open-rate-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Bounces -->
				<div id="skystats-mailchimp-bounces-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Bounced', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of recipients that registered as a hard or soft bounce for your campaigns during this period.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-mailchimp-bounces" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-mailchimp-bounces-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-mailchimp-bounces-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'Bounces were registered during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-mailchimp-bounces-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
					</div>
				</div>
			</div> <!-- #skystats-mailchimp-data-points-container -->

			<!-- Tables -->
			<div class="skystats-service-detail-data-tables-container">
				<div class="skystats-detail-data-table-row">
					<!-- Top Campaigns -->
					<div id="skystats-detail-mailchimp-top-campaigns-data-table-column" class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-mailchimp-top-campaigns-data-table-column-content" class="skystats-data-table-column-content skystats-service-detail-data-table-column-content">
							<h2><?php _e( 'Top 5 Campaigns', SKYSTATS_TEXT_DOMAIN ); ?></h2>
							<div id="skystats-mailchimp-top-campaigns-data-error-container" class="skystats-top-data-error-container">
								<p><?php _e( 'No data available for this period.', SKYSTATS_TEXT_DOMAIN ); ?></p>
							</div>
							<table id="skystats-mailchimp-top-campaigns-table" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
									<tr>
										<th><?php _e( 'Campaign Name', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Opens', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Clicks', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Unsubscribed', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Open Rate', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Click Rate', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Date Sent', SKYSTATS_TEXT_DOMAIN ); ?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
					<!-- Top Lists -->
					<div id="skystats-detail-mailchimp-top-lists-data-table-column" class="skystats-service-detail-data-table-column">
						<div id="skystats-detail-mailchimp-top-lists-data-table-column-content" class="skystats-data-table-column-content skystats-service-detail-data-table-column-content">
							<h2><?php _e( 'Top 5 Lists', SKYSTATS_TEXT_DOMAIN ); ?></h2>
							<div id="skystats-mailchimp-top-lists-data-error-container" class="skystats-top-data-error-container">
								<p><?php _e( 'No data available for this period.', SKYSTATS_TEXT_DOMAIN ); ?></p>
							</div>
							<table id="skystats-mailchimp-top-lists-table" class="skystats-service-detail-data-table wp-list-table widefat">
								<thead>
									<tr>
										<th><?php _e( 'List Name', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Avg. Open Rate', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Avg. Click Rate', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Subscribers', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Avg. Subscribe Rate', SKYSTATS_TEXT_DOMAIN ); ?></th>
										<th><?php _e( 'Avg. Unsubscribe Rate', SKYSTATS_TEXT_DOMAIN ); ?></th>
									</tr>
								</thead>
								<tbody></tbody>
							</table>
						</div>
					</div>
				</div> <!-- .skystats-detail-data-table-row -->
			</div> <!-- .skystats-service-detail-data-tables-container -->
		</div> <!-- #skystats-mailchimp-data-tab-content -->
	</div> <!-- #skystats-mailchimp-data-container -->
</div> <!-- .wrap -->