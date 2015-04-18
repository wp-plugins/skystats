<?php

defined( 'ABSPATH' ) or exit();

?>

<div id="wordpress_9" class="skystats-card-container">
	<div id="skystats-google-analytics-card" class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'WordPress', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<span class="skystats-card-settings skystats-disabled"></span>
			<span class="skystats-card-eye skystats-select-tool-tip skystats-disabled"></span>
		</div>
		<div id="skystats-wordpress-card-content" class="skystats-card-content">
			<div class="skystats-card-image-container">
				<a href="https://skystats.com/#integrations"><img src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'wordpress.png'; ?>"></a>
			</div>
			<div class="skystats-vote-now-container">
				<img src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'votenow1.png'; ?>">
				<div class="skystats-integration-vote-totals-loading-container">
					<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin-32.svg'; ?>" width="32" height="32">
				</div>
				<div id="skystats-wordpress-integration-vote-totals-container" class="skystats-integration-vote-totals-container">
					<strong>0</strong>
					<span><?php _e( 'votes', SKYSTATS_TEXT_DOMAIN ); ?></span>
				</div>
				<a href="" class="skystats-mashboard-vote-now skystats-shadowless" data-integration="wordpress">
					<img src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'vote-now-button.png'; ?>" alt="<?php _e( 'Click to Vote for WordPress', SKYSTATS_TEXT_DOMAIN ); ?>">
				</a>
			</div>
		</div>
	</div>
</div>