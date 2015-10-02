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
		<p id="skystats-free-data-range-notice" class="skystats-success-message"><?php printf( __( 'Currently displaying the past 30 days. Upgrade to <a href="%s" target="_blank">SkyStats Pro</a> to access more than 30 days of data.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_MARKETING_SITE_FREE_SIGNUP_PAGE_URL ); ?></p>
	</div>

	<div id="skystats-page-error-container" class="skystats-page-error-container skystats-error-container">
		<p></p>
	</div>

	<!-- Loading Image Container -->
	<div id="skystats-cards-loading-container">
		<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
	</div>

	<div id="dashboard-widgets" class="metabox-holder">

		<?php $mashboard_integrations_path = dirname( __FILE__ ) . '/mashboard-integrations/'; ?>

		<!-- 1st Column -->
		<div id="postbox-container-1" class="postbox-container skystats-cards-column">

			<?php require_once $mashboard_integrations_path . 'google-analytics.php'; ?>

		</div>

		<!-- 2nd Column -->
		<div id="postbox-container-2" class="postbox-container skystats-cards-column">

			<?php require_once $mashboard_integrations_path . 'facebook.php'; ?>

		</div>

		<!-- 3rd Column -->
		<div id="postbox-container-3" class="postbox-container skystats-cards-column">

			<?php require_once $mashboard_integrations_path . 'twitter.php'; ?>

		</div>

		<!-- 4th Column -->
		<div id="postbox-container-4" class="postbox-container skystats-cards-column">

			<?php

			require_once $mashboard_integrations_path . 'google-adwords.php';

			require_once $mashboard_integrations_path . 'mailchimp.php';

			require_once $mashboard_integrations_path . 'vote.php';

			require_once $mashboard_integrations_path . 'upgrading.php';

			?>

		</div>
	</div>
</div> <!-- .wrap -->