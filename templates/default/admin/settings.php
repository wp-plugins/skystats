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
						<p id="skystats-license-info-message" class="skystats-info-message"><?php printf( __( 'Don\'t have a license key yet? Click <a href="%s">here</a> to purchase a free or premium license key.', SKYSTATS_TEXT_DOMAIN ), 'https://skystats.com/#pricing' ); ?></p>
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

				<!-- Voting Opt-in -->
				<fieldset class="skystats-fieldset">
					<legend><?php _e( 'Vote Opt-in', SKYSTATS_TEXT_DOMAIN ); ?></legend>
					<p><?php _e( 'Select "Yes" below to enable you to vote and allow us to include your email address with your vote(s), otherwise select "No". By default, "No" will always be selected. ', SKYSTATS_TEXT_DOMAIN ); ?></p>
					<div class="skystats-form-group">
						<?php $opted_in = get_option( 'skystats_vote_opted_in_with_email_address' ); ?>
						<select name="vote-opt-in" id="vote-opt-in">
							<option value="false" <?php selected( $opted_in, 'false' ); ?>><?php _e( 'No', SKYSTATS_TEXT_DOMAIN ); ?></option>
							<option value="true" <?php selected( $opted_in, 'true' ); ?>><?php _e( 'Yes', SKYSTATS_TEXT_DOMAIN ); ?></option>
						</select>
					</div>
				</fieldset>

				<!-- Role Access -->
				<fieldset class="skystats-fieldset">
					<legend><?php _e( 'Role Access', SKYSTATS_TEXT_DOMAIN ); ?></legend>
					<div class="skystats-form-group">
						<label class="skystats-form-label" for="role_access"><?php _e( 'Roles', SKYSTATS_TEXT_DOMAIN ); ?></label>
						<?php $role_access = get_option( 'skystats_role_access' ); ?>
						<?php $roles = (array) get_editable_roles(); ?>
						<p><?php _e( 'Select a role which is allowed to access the mashboard, detail, and setting pages. Any role(s) which are above the selected role will also have permission e.g. selecting Editor will allow Editors and Administrators to view the aforementioned pages.', SKYSTATS_TEXT_DOMAIN ); ?>
						</p>
						<select class="skystats-form-control" name="role_access" id="role_access" required="required">
							<?php $selected_role_access = get_option( 'skystats_selected_role_access' ); ?>
							<?php foreach ( $roles as $identifier => $role ): ?>
								<?php if ( isset( $role['name'] ) ): ?>
									<option value="<?php echo esc_attr( $identifier ); ?>" <?php if ( $identifier === $selected_role_access ): ?>selected="selected"<?php endif; ?>><?php echo esc_attr( $role['name'] ); ?></option>
								<?php endif; ?>
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
					<p><?php _e( 'Settings saved successfully. Please click "Reload Page" below to see your changes.', SKYSTATS_TEXT_DOMAIN ); ?></p>
				</div>

				<div class="skystats-form-group">
					<button type="submit" class="skystats-button" id="save_settings" name="save_settings" value="-1"><?php _e( 'Save Settings', SKYSTATS_TEXT_DOMAIN ); ?></button>
					<div id="skystats-settings-save-settings-loading-container" class="skystats-loading-container">
						<img class="skystats-loading-image" src="<?php echo SKYSTATS_TEMPLATE_IMAGES_URL . 'loading-spin.svg'; ?>" width="64" height="64">
					</div>
					<button class="skystats-button" id="settings-reload"><?php _e( 'Reload Page', SKYSTATS_TEXT_DOMAIN ); ?></button>
				</div>
			</form>
		</div>
	</div>
</div>