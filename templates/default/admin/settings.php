<?php

defined( 'ABSPATH' ) or exit();

?>

<div class="wrap">

	<!-- Logo Container & Image -->
	<div id="skystats-logo-container">
		<?php $mashboard_page_url = esc_attr( admin_url() . 'admin.php?page=skystats-mashboard' ); ?>
		<?php $brand_logo_url = esc_attr( get_option( 'skystats_brand_logo_image_url' ) ); ?>
		<a href="<?php echo $mashboard_page_url; ?>"><img src="<?php echo $brand_logo_url; ?>"></a>
	</div>

	<!-- Accordion -->
	<div id="skystats-settings-accordion">

		<!-- License Settings -->
		<h3 class="skystats-setting-header"><?php _e( 'License', SKYSTATS_TEXT_DOMAIN ); ?></h3>
		<div>
			<p id="skystats-successful-license-validation-request" class="skystats-success-message"></p>
			<p id="skystats-unsuccessful-license-validation-request" class="skystats-error-message"></p>
			<form method="POST" role="form">
				<div class="skystats-form-group">
					<label class="skystats-form-label" for="license_key"><?php _e( 'License Key', SKYSTATS_TEXT_DOMAIN ); ?></label>
					<?php $show_license_purchase_notification = get_option( 'skystats_show_license_purchase_notification' );?>
					<?php if ( 'true' === $show_license_purchase_notification ) : ?>
						<p id="skystats-license-info-message" class="skystats-info-message"><?php printf( __( 'Don\'t have a license key yet? Click <a href="%s">here</a> to purchase a free or premium license key.', SKYSTATS_TEXT_DOMAIN ), SKYSTATS_MARKETING_SITE_FREE_SIGNUP_PAGE_URL ); ?></p>
					<?php endif; ?>
					<?php $license_key = get_option( 'skystats_license_key' ); ?>
					<input type="text" class="skystats-form-control" name="license_key" id="license_key" required="required" value="<?php echo esc_attr( $license_key ); ?>">
					<div id="skystats-settings-validate-license-loading-container" class="skystats-loading-container">
						<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
					</div>
				</div>
				<div class="skystats-form-group">
					<button id="validate_license" type="submit" class="skystats-button" name="validate_license" value="-1"><?php _e( 'Validate', SKYSTATS_TEXT_DOMAIN ); ?></button>
				</div>
			</form>
		</div>

		<!-- General Settings -->
		<h3 class="skystats-setting-header"><?php _e( 'Settings', SKYSTATS_TEXT_DOMAIN ); ?></h3>
		<div>
			<p><?php _e( 'Make your changes below then click "Save Settings" at the bottom of the page. You will need to refresh the page to see some of your changes.', SKYSTATS_TEXT_DOMAIN ); ?></p>
			<form method="POST" role="form">

				<fieldset class="skystats-fieldset">
					<legend><?php _e( 'Stats Access', SKYSTATS_TEXT_DOMAIN ); ?></legend>
					<div class="skystats-form-group">
						<label class="skystats-form-label" for="skystats_reports_users_allowed_access"><?php _e( 'Roles', SKYSTATS_TEXT_DOMAIN ); ?></label>
						<?php $skystats_reports_users_allowed_access = get_option( 'skystats_reports_users_allowed_access' ); ?>
						<?php $roles = (array) get_editable_roles(); ?>
						<p><?php _e( 'Select which roles are allowed to see and access the Mashboard and detail pages.', SKYSTATS_TEXT_DOMAIN ); ?></p>
						<select class="skystats-form-control" name="skystats_reports_users_allowed_access" id="skystats_reports_users_allowed_access" required="required" data-placeholder="<?php _e( 'Click here to select a role', SKYSTATS_TEXT_DOMAIN ); ?>" multiple>
							<?php foreach ( $roles as $identifier => $role ): ?>
								<?php if ( isset( $role['name'] ) ): ?>
									<option value="<?php echo esc_attr( $identifier ); ?>" <?php if ( is_array( $skystats_reports_users_allowed_access ) && in_array( $identifier, $skystats_reports_users_allowed_access ) ): ?>selected="selected"<?php endif; ?>><?php echo esc_attr( $role['name'] ); ?></option>
								<?php endif; ?>
							<?php endforeach; ?>
						</select>
					</div>
				</fieldset>

				<fieldset class="skystats-fieldset">
					<legend><?php _e( 'Settings Access', SKYSTATS_TEXT_DOMAIN ); ?></legend>
					<div class="skystats-form-group">
						<label class="skystats-form-label" for="skystats_settings_users_allowed_access"><?php _e( 'Users', SKYSTATS_TEXT_DOMAIN ); ?></label>
						<?php $skystats_settings_users_allowed_access = get_option( 'skystats_settings_users_allowed_access' ); ?>
						<p><?php _e( 'Select which users are allowed to view and edit the Settings.  Administrators have access to both stats and settings by default but when adding users be sure to include your own user so you don\'t accidentally lock yourself out of the settings.', SKYSTATS_TEXT_DOMAIN ); ?></p>
						<?php
						global $wpdb;
						$results = $wpdb->get_results( "SELECT `ID`, `user_login` FROM `{$wpdb->users}`" );
						$users = array();
						if ( is_array( $results ) && ! empty( $results ) ) {
							foreach ( $results as $user ) {
								if ( ! is_object( $user ) ) {
									continue;
								}
								if ( ! isset( $user->ID, $user->user_login ) ) {
									continue;
								}
								$users[ $user->ID ] = $user->user_login;
							}
						}
						?>
						<select class="skystats-form-control" name="skystats_settings_users_allowed_access" id="skystats_settings_users_allowed_access" required="skystats_settings_users_allowed_access" data-placeholder="<?php _e( 'Click here to select a user', SKYSTATS_TEXT_DOMAIN ); ?>" multiple>
							<?php foreach ( $users as $user_id => $user_login_name ) : ?>
								<option value="<?php echo esc_attr( $user_id ); ?>" <?php if ( is_array( $skystats_settings_users_allowed_access ) && in_array( $user_id, $skystats_settings_users_allowed_access ) ): ?>selected="selected"<?php endif; ?>><?php echo $user_login_name; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</fieldset>

				<!-- Default Dashboard -->
				<fieldset class="skystats-fieldset">
					<legend><?php _e( 'Default Dashboard', SKYSTATS_TEXT_DOMAIN ); ?></legend>
					<p><?php _e( 'Select whether users are sent to the SkyStats Mashboard or the WordPress Dashboard when they login.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<div class="form-group">
						<select name="default_dashboard" id="default_dashboard">
							<?php $default_dashboard = get_option( 'skystats_default_dashboard' ); ?>
							<option value="skystats_mashboard" <?php selected( $default_dashboard, 'skystats_mashboard' ); ?>><?php _e( 'SkyStats Mashboard', SKYSTATS_TEXT_DOMAIN ); ?></option>
							<option value="wordpress_dashboard"<?php selected( $default_dashboard, 'wordpress_dashboard' ); ?>><?php _e( 'WordPress Dashboard', SKYSTATS_TEXT_DOMAIN ); ?></option>
						</select>
					</div>
				</fieldset>

				<!-- Caching -->
				<fieldset class="skystats-fieldset">
					<legend><?php _e( 'Caching', SKYSTATS_TEXT_DOMAIN ); ?></legend>
					<p><?php _e( 'Select whether you would like to used a cached version of the data for your integrations if it is available, which dramatically increases the performance of the requests.', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<div class="form-group">
						<select name="cache_mode" id="cache_mode">
							<?php $cache_mode = get_option( 'skystats_cache_mode' ); ?>
							<option value="enabled" <?php selected( $cache_mode, 'enabled' ); ?>>
								<?php _e( 'Enabled', SKYSTATS_TEXT_DOMAIN ); ?>
							</option>
							<option value="disabled" <?php selected( $cache_mode, 'disabled' ); ?>>
								<?php _e( 'Disabled', SKYSTATS_TEXT_DOMAIN ); ?>
							</option>
						</select>
					</div>
				</fieldset>

				<div id="skystats-save-settings-result" class="skystats-form-group skystats-success-message">
					<p><?php _e( 'Settings saved successfully. You will need to reload the page to see some of your changes.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				</div>

				<div class="skystats-form-group">
					<button type="submit" class="skystats-button" id="save_settings" name="save_settings" value="-1"><?php _e( 'Save Settings', SKYSTATS_TEXT_DOMAIN ); ?></button>
					<div id="skystats-settings-save-settings-loading-container" class="skystats-loading-container">
						<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
					</div>
				</div>
			</form>
		</div>
	</div>
</div>