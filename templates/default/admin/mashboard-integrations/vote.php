<?php

defined( 'ABSPATH' ) or exit();

?>

<div id="vote_13" class="skystats-card-container">
	<div id="skystats-google-analytics-card" class="skystats-card">
		<div class="skystats-card-header">
			<span class="skystats-card-drag-icon"></span>
			<h4 class="skystats-card-heading"><?php _e( 'Vote', SKYSTATS_TEXT_DOMAIN ); ?></h4>
			<span class="skystats-card-settings skystats-disabled"></span>
			<span class="skystats-card-eye skystats-select-tool-tip skystats-disabled"></span>
		</div>
		<div class="skystats-card-content">
			<div class="skystats-centered-block">
				<p class="skystats-placeholder-card-header"><?php _e( "Don't see an integration you want?", SKYSTATS_TEXT_DOMAIN ); ?></p>
				<a href="https://skystats.com/vote/" class="skystats-shadowless" target="_blank">
					<img src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'vote-now-button.png'; ?>" alt="<?php _e( 'Click to vote for an integration.', SKYSTATS_TEXT_DOMAIN ); ?>">
				</a>
				<p><a id="skystats-vote-integration-content-leave-review" href="https://skystats.com/feedback/" target="_blank"><?php _e( "or Leave A Review.", SKYSTATS_TEXT_DOMAIN ); ?></a></p>
			</div>
		</div>
	</div>
</div>