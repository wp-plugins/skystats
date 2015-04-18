<?php

defined( 'ABSPATH' ) or exit();

?>

<div id="facebook_2" class="skystats-card-container">
	<div class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'Facebook', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<span id="skystats-facebook-settings-icon" class="skystats-card-settings skystats-setting-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Configure Settings for Facebook', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
			<span id="skystats-facebook-grid-icon" class="skystats-card-eye skystats-select-tool-tip skystats-tooltip skystats-disabled" data-tooltip="<?php _e( 'Show/Hide Facebook Data Points', SKYSTATS_TEXT_DOMAIN ); ?>">
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
		<div id="skystats-facebook-card-content" class="skystats-card-content">

			<!-- Facebook Specific Errors -->
			<div id="skystats-facebook-error-container" class="skystats-error-container">
				<p></p>
			</div>

			<!-- Facebook Loading Icon -->
			<div id="skystats-facebook-loading-container" class="skystats-loading-container skystats-chart-loading-container">
				<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
			</div>

			<!-- Facebook Settings Content -->
			<div id="skystats-facebook-settings-content" class="skystats-settings-tab-settings-content">
				<?php require_once SKYSTATS_API_FUNCTIONS_PATH . 'facebook.php'; ?>
				<?php $api_authenticate_url = esc_attr( skystats_facebook_get_authentication_url( SKYSTATS_MASHBOARD_PAGE_URL ) ); ?>
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
					<?php $fb_deauthorize_url = esc_attr( skystats_api_facebook_get_deauthorize_url( SKYSTATS_MASHBOARD_PAGE_URL ) ); ?>
					<a id="skystats-facebook-deauthorize" href="<?php echo $fb_deauthorize_url; ?>" class=" skystats-button"><?php _e( 'Deauthorize', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
				<!-- Setup / Authorize / Authenticate -->
				<div id="skystats-facebook-settings-authorize-section" class="skystats-facebook-settings-tab-section skystats-settings-tab-section">
					<h3><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></h3>
					<p><?php _e( 'Click the button below to Login to Facebook and allow the application to access your page(s) insights. You will then be able to see data for your page(s). Please make sure you login with an account that has access to at least one page\'s insights.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<a id="skystats-facebook-authorize" href="<?php echo $api_authenticate_url; ?>" class="skystats-button"><?php _e( 'Setup', SKYSTATS_TEXT_DOMAIN ); ?></a>
				</div>
			</div>

			<!-- Facebook Data Tab Content -->
			<div id="skystats-facebook-data-tab-content">

				<!-- Facebook Chart -->
				<div id="skystats-facebook-chart-container" class="skystats-mashboard-chart-container skystats-chart-container">
					<div id="skystats-facebook-chart" class="skystats-mashboard-chart" height="300"></div>
				</div>

				<!-- Facebook Data Points -->
				<div id="skystats-facebook-data-points-container" class="skystats-data-points-container">

					<!-- Facebook Total Likes -->
					<div id="skystats-facebook-total-likes-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-light-blue-chart-key-icon">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Total Likes', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total number of people who have liked your page during this period. This metric is updated every 24 hours.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-facebook-total-likes-total" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-facebook-total-likes-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-facebook-total-likes-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'total likes at the end of the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-facebook-total-likes-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Facebook Total Reach -->
					<div id="skystats-facebook-total-reach-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-turquoise-chart-key-icon">&nbsp;</span>
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Total Reach', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The number of people who have seen any content associated with your page during this period. This metric is updated every 24 hours.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-facebook-total-reach-total" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-facebook-total-reach-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-facebook-total-reach-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'total reach during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-facebook-total-reach-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Facebook Page Visits -->
					<div id="skystats-facebook-page-visits-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'Page Visits', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'Total amount of page visits by all users during this period. This metric is updated every 24 hours.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-facebook-page-visits-total" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-facebook-page-visits-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-facebook-page-visits-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'page visits during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-facebook-page-visits-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>

					<!-- Facebook People Engaged -->
					<div id="skystats-facebook-people-engaged-data-point" class="skystats-mashboard-data-point-column">
						<div class="skystats-dashboard-data-point-header">
							<span class="skystats-dashboard-data-point-heading"><?php _e( 'People Engaged', SKYSTATS_TEXT_DOMAIN ); ?></span>
							<span class="skystats-dashboard-data-point-heading-info skystats-tooltip" data-tooltip="<?php _e( 'The unique number of people who liked, commented on, shared, or clicked on your posts during this period. This metric is updated every 24 hours.', SKYSTATS_TEXT_DOMAIN ); ?>"></span>
						</div>
						<div class="skystats-dashboard-data-point-content">
							<span id="skystats-facebook-people-engaged-total" class="skystats-data-point-value"></span>
						</div>
						<div class="skystats-dashboard-data-point-footer">
							<span id="skystats-facebook-people-engaged-change-direction" class="skystats-data-point-change-direction"></span>
							<span id="skystats-facebook-people-engaged-change" class="skystats-data-point-change"></span>
							<?php $title = __( 'people were engaged during the previous period.', SKYSTATS_TEXT_DOMAIN ); ?>
							<span id="skystats-facebook-people-engaged-change-info" class="skystats-dashboard-data-point-heading-info skystats-tooltip skystats-service-data-point-change-percentage-info" data-tooltip-backup="<?php echo $title; ?>" data-tooltip="<?php echo $title; ?>"></span>
						</div>
					</div>
				</div>

				<!-- Facebook Details -->
				<div class="skystats-card-details-container">
					<span class="skystats-card-details">
						<a class="skystats-card-details-link skystats-tooltip" href="<?php echo admin_url( 'admin.php?page=skystats-facebook' ); ?>" data-tooltip="<?php _e( 'View detailed information about your Facebook page.', SKYSTATS_TEXT_DOMAIN ); ?>">
							<?php _e( 'View Details', SKYSTATS_TEXT_DOMAIN ); ?>
						</a>
					</span>
				</div>
			</div> <!-- #skystats-mashboard-facebook-data-content -->
		</div> <!-- #skystats-facebook-card-content -->
	</div> <!-- .skystats-card -->
</div> <!-- #facebook_2.skystats-card-container -->