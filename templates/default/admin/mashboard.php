<?php

defined( 'ABSPATH' ) or exit();

?>

<div class="wrap">
	<div id="skystats-logo-container">
		<?php $current_page_url = esc_attr( admin_url() . 'admin.php?page=skystats-mashboard' ); ?>
		<?php $brand_logo_image_url = esc_attr( get_option( 'skystats_brand_logo_image_url' ) ); ?>
		<a class="skystats-shadowless" href="<?php echo $current_page_url; ?>">
			<?php $id = ( SKYSTATS_DEFAULT_LOGO_IMAGE_URL === $brand_logo_image_url ) ?
				'skystats-default-logo' :
				'';
			?>
			<img id="<?php echo $id; ?>" src="<?php echo $brand_logo_image_url; ?>">
		</a>
	</div>

	<!-- Free Version Date Range Notice -->
	<div id="skystats-date-range-notice-container">
		<input class="skystats-date" type="hidden" id="start_date" name="start_date" required="required" value="">
		<input class="skystats-date" type="hidden" id="end_date" name="end_date" required="required" value="">
		<p id="skystats-free-data-range-notice" class="skystats-success-message"><?php printf( __( 'Currently displaying the past 30 days. Upgrade to <a href="%s">SkyStats Pro</a> to access more than 30 days of data.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_MARKETING_SITE_PRICING_SECTION_URL ); ?></p>
	</div>

	<div id="skystats-page-error-container" class="skystats-page-error-container skystats-error-container">
		<p></p>
	</div>

	<!-- Loading Image Container -->
	<div id="skystats-cards-loading-container">
		<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
	</div>

	<div class="skystats-card-columns-container">

		<?php $mashboard_integrations_path = dirname( __FILE__ ) . '/mashboard-integrations/'; ?>

		<!-- 1st Column -->
		<div id="column_1" class="skystats-cards-col">

			<!-- Google Analytics -->
			<?php require_once $mashboard_integrations_path . 'google-analytics.php'; ?>

			<!-- Youtube -->
			<?php require_once $mashboard_integrations_path . 'youtube.php'; ?>

			<!-- WordPress -->
			<?php require_once $mashboard_integrations_path . 'wordpress.php'; ?>

		</div>

		<!-- 2nd Column -->
		<div id="column_2" class="skystats-cards-col">

			<!-- Facebook -->
			<?php require_once $mashboard_integrations_path . 'facebook.php'; ?>

			<!-- Google Plus -->
			<?php require_once $mashboard_integrations_path . 'google-plus.php'; ?>

			<!-- AWeber -->
			<?php require_once $mashboard_integrations_path . 'aweber.php'; ?>

		</div>

		<!-- 3rd Column -->
		<div id="column_3" class="skystats-cards-col">

			<!-- Twitter -->
			<?php require_once $mashboard_integrations_path . 'twitter.php'; ?>

			<!-- LinkedIn -->
			<?php require_once $mashboard_integrations_path . 'linkedin.php'; ?>

			<!-- GoogleAdwords -->
			<?php require_once $mashboard_integrations_path . 'google-adwords.php'; ?>

		</div>

		<!-- 4th Column -->
		<div id="column_4" class="skystats-cards-col">

			<!-- PayPal -->
			<?php require_once $mashboard_integrations_path . 'paypal.php'; ?>

			<!-- MailChimp -->
			<?php require_once $mashboard_integrations_path . 'mailchimp.php'; ?>

			<!-- CampaignMonitor -->
			<?php require_once $mashboard_integrations_path . 'campaign-monitor.php'; ?>

		</div>
	</div> <!-- .skystats-card-columns-container -->
</div> <!-- .wrap -->