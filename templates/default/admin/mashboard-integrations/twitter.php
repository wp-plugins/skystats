<?php

defined( 'ABSPATH' ) or exit();

?>

<div id="twitter_3" class="skystats-card-container">
	<div class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'Twitter', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<span id="skystats-twitter-card-settings" class="skystats-card-settings skystats-disabled" title="<?php _e( 'Configure Settings for Twitter', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
			<span id="skystats-twitter-grid" class="skystats-card-eye skystats-select-tool-tip skystats-tooltip skystats-disabled" title="<?php _e( 'Show/Hide Twitter Data Points', SKYSTATS_TEXT_DOMAIN ); ?>">
			</span>
		</div>
		<div id="skystats-twitter-card-content" class="skystats-card-content">

			<div class="skystats-card-image-container">
				<a href="https://skystats.com/#integrations"><img src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'twitter.png'; ?>"></a>
			</div>
			<div class="skystats-vote-now-container">
				<img src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'votenow1.png'; ?>">
				<div class="skystats-integration-vote-totals-loading-container">
					<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin-32.svg'; ?>" width="32" height="32">
				</div>
				<div id="skystats-twitter-integration-vote-totals-container" class="skystats-integration-vote-totals-container">
					<strong>0</strong>
					<span><?php _e( 'votes', SKYSTATS_TEXT_DOMAIN ); ?></span>
				</div>
				<a href="" class="skystats-mashboard-vote-now skystats-shadowless" data-integration="twitter">
					<img src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'vote-now-button.png'; ?>" alt="<?php _e( 'Click to Vote for Twitter', SKYSTATS_TEXT_DOMAIN ); ?>">
				</a>
			</div>
		</div>
	</div>
</div>