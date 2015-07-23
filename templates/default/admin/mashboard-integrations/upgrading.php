<?php

defined( 'ABSPATH' ) or exit();

?>

<div id="upgrading_14" class="skystats-card-container">
	<div id="skystats-google-analytics-card" class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'Premium Features', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<span class="skystats-card-settings skystats-disabled"></span>
			<span class="skystats-card-eye skystats-select-tool-tip skystats-disabled"></span>
		</div>
		<div class="skystats-card-content">
			<div class="skystats-centered-block">
				<a href="<?php echo SKYSTATS_MARKETING_SITE_FREE_SIGNUP_PAGE_URL; ?>" target="_blank">
					<img id="skystats-mashboard-upgrade-ad" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'upgrade-ad.gif'; ?>">
				</a>
			</div>
			<p id="skystats-mashboard-upgrade-ad-notice">
				<?php _e( 'Want to remove this?', SKYSTATS_TEXT_DOMAIN ); ?><br>
				<a class="skystats-link" href="<?php echo SKYSTATS_MARKETING_SITE_FREE_SIGNUP_PAGE_URL; ?>" target="_blank"><?php _e( 'Upgrade to SkyStats Premium &raquo;', SKYSTATS_TEXT_DOMAIN ); ?></a>
			</p>
		</div>
	</div>
</div>