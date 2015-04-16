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

	<!-- Facebook Page Data Container -->
	<div id="skystats-facebook-data-container" class="skystats-service-detail-container">

		<!-- Top Container (Header, Icons) -->
		<div class="skystats-detail-top-container">
			<h3 class="skystats-detail-header"><?php _e( 'Facebook', SKYSTATS_TEXT_DOMAIN ); ?>
				<div class="skystats-detail-icons">
					<span id="skystats-facebook-settings-icon" class="skystats-setting-tool-tip skystats-tooltip skystats-settings-icon skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for Facebook', SKYSTATS_TEXT_DOMAIN ); ?>">
					</span>
					<span id="skystats-facebook-grid-icon" class="skystats-grid-icon skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide Facebook Data Points', SKYSTATS_TEXT_DOMAIN ); ?>">
					</span>
					<div id="skystats-facebook-grid-icon-content" class="skystats-grid-content">
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-facebook-total-likes-data-point" checked>
								<?php _e( 'Total Likes', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-facebook-total-reach-data-point" checked>
								<?php _e( 'Total Reach', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-facebook-page-visits-data-point" checked>
								<?php _e( 'Page Visits', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
						<p>
							<label>
								<input type="checkbox" class="skystats-show-data-point" value="skystats-facebook-people-engaged-data-point" checked>
								<?php _e( 'People Engaged', SKYSTATS_TEXT_DOMAIN ); ?>
							</label>
						</p>
					</div>
				</div>
			</h3>
		</div>

		<!-- Chart Key / Figure -->
		<div id="skystats-detail-chart-key-container" class="skystats-service-detail-chart-key-container">
			<span class="skystats-light-blue-chart-key-icon skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Total Likes', SKYSTATS_TEXT_DOMAIN ); ?></span>
			<span class="skystats-turquoise-chart-key-icon skystats-detail-chart-key-icon">&nbsp;</span>
			<span><?php _e( 'Total Reach', SKYSTATS_TEXT_DOMAIN ); ?></span>
		</div>

		<!-- Loading Image -->
		<div id="skystats-facebook-loading-container" class="skystats-loading-container">
			<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
		</div>

		<!-- Settings Tab Content (Select Page, Setup, Deauthorize) -->
		<div id="skystats-facebook-settings-content" class="skystats-settings-tab-settings-content">
			<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'facebook.php'; ?>
			<?php $api_authenticate_url = esc_attr( skystats_facebook_get_authentication_url( SKYSTATS_FACEBOOK_DETAIL_PAGE_URL ) ); ?>
			<!-- No Pages -->
			<div id="skystats-facebook-settings-no-pages-section" class="skystats-facebook-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'No Pages Found', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'No pages found. Please try refreshing the page or deauthorize below then reauthorize with a Facebook account that has access to at least one page\'s insights.', SKYSTATS_TEXT_DOMAIN ); ?></p>
			</div>
			<!-- Page Selection -->
			<div id="skystats-facebook-settings-page-selection-section" class="skystats-facebook-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Select Page', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'Select a page from the list below to display data for:', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<select id="skystats-facebook-page-list" class="skystats-card-settings-profiles">
				</select>
				<button id="skystats-facebook-save-page-id" class="skystats-settings-tab-save-data-button skystats-button"><?php _e( 'Save', SKYSTATS_TEXT_DOMAIN ); ?></button>
			</div>
			<!-- Reauthorize / Reauthenticate -->
			<div id="skystats-facebook-settings-reauthorize-section" class="skystats-facebook-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Reauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'Sorry, an error occurred that requires you to reauthorize/reauthenticate with Google. This is likely due to either an expired access token, you removed the application from your account, or you logged out of your account and your session expired. Please click the button below to continue.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<a id="skystats-facebook-reauthorize" href="<?php echo $api_authenticate_url; ?>" class="skystats-button"><?php _e( 'Reauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
			</div>
			<!-- Deauthorize / Deauthenticate -->
			<div id="skystats-facebook-settings-deauthorize-section" class="skystats-facebook-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'Purge all Facebook authentication and cache data from your local install.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<?php $fb_deauthorize_url = esc_attr( skystats_api_facebook_get_deauthorize_url( SKYSTATS_FACEBOOK_DETAIL_PAGE_URL ) ); ?>
				<a id="skystats-facebook-deauthorize" href="<?php echo $fb_deauthorize_url; ?>" class=" skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
			</div>
			<!-- Setup / Authorize / Authenticate -->
			<div id="skystats-facebook-settings-authorize-section" class="skystats-facebook-settings-tab-section skystats-settings-tab-section">
				<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
				<p><?php _e( 'Click the button below to Login to Facebook and allow the application to access your page(s) insights. You will then be able to see data for your page(s). Please make sure you login with an account that has access to at least one page\'s insights.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				<a id="skystats-facebook-authorize" href="<?php echo $api_authenticate_url; ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
			</div>
		</div>

		<!-- Data Tab Content (Chart, Data Points, Data Tables) -->
		<div id="skystats-facebook-data-content">

			<!-- Chart -->
			<div id="skystats-facebook-chart-container" class="skystats-detail-chart-container skystats-chart-container">
				<div id="skystats-facebook-chart" class="skystats-detail-chart"></div>
			</div>

			<!-- Data Points -->
			<div id="skystats-facebook-data-points-container" class="skystats-data-points-container">

				<!-- Total Likes -->
				<div id="skystats-facebook-total-likes-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Total Likes', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of people who have liked your page during this period. This metric is updated every 24 hours.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-facebook-total-likes-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-facebook-total-likes-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-facebook-total-likes-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'total likes at the end of the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-facebook-total-likes-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Total Reach -->
				<div id="skystats-facebook-total-reach-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Total Reach', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The number of people who have seen any content associated with your page during this period. This metric is updated every 24 hours.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-facebook-total-reach-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-facebook-total-reach-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-facebook-total-reach-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'total reach during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-facebook-total-reach-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- Page Visits -->
				<div id="skystats-facebook-page-visits-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'Page Visits', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total amount of page visits by all users during this period. This metric is updated every 24 hours.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-facebook-page-visits-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-facebook-page-visits-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-facebook-page-visits-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'page visits during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-facebook-page-visits-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>

				<!-- People Engaged -->
				<div id="skystats-facebook-people-engaged-data-point" class="skystats-service-detail-data-point-column skystats-data-point-column">
					<div class="skystats-service-detail-data-point-column-header">
						<span class="skystats-service-detail-data-point-column-header-label"><?php _e( 'People Engaged', SKYSTATS_TEXT_DOMAIN ); ?></span>
						<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The unique number of people who liked, commented on, shared, or clicked on your posts during this period. This metric is updated every 24 hours.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-content">
						<span id="skystats-facebook-people-engaged-total" class="skystats-data-point-value"></span>
					</div>
					<div class="skystats-service-detail-data-point-column-footer">
						<span id="skystats-facebook-people-engaged-change-direction" class="skystats-data-point-change-direction"></span>
						<span id="skystats-facebook-people-engaged-change" class="skystats-data-point-change"></span>
						<?php $title = __( 'people were engaged during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
						<span id="skystats-facebook-people-engaged-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip="<?php echo $title; ?>" data-tooltip-backup="<?php echo $title; ?>"></span>
					</div>
				</div>
			</div> <!-- End Data Points -->

			<!-- Facebook Data Tables -->
			<div class="skystats-service-detail-data-tables-container">

				<!-- Top Posts -->
				<div class="skystats-service-detail-data-table-column skystats-service-detail-full-width-data-table-column">
					<div id="skystats-facebook-top-posts-data-table-column-content" class="skystats-data-table-column-content">
						<h2><?php _e( 'Top 5 Posts', SKYSTATS_TEXT_DOMAIN ); ?></h2>
						<table id="skystats-facebook-top-posts-data-table" class="skystats-service-detail-data-table wp-list-table widefat">
							<thead>
								<tr>
									<th><?php _e( 'Name', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Likes', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Reach', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Comments', SKYSTATS_TEXT_DOMAIN ); ?></th>
									<th><?php _e( 'Date', SKYSTATS_TEXT_DOMAIN ); ?></th>
								</tr>
							</thead>
							<tbody id="skystats-facebook-top-posts-data-table-tbody"></tbody>
						</table>
					</div>
				</div>
			</div> <!-- End Data Tables -->
		</div> <!-- End Data Tab Content -->
	</div> <!-- Page Data Content -->
</div> <!-- .wrap -->