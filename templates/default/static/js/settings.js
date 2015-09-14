/*http://www.jslint.com/*/
/*jslint white: true */
/*global jQuery, window, ajaxurl, console, skystats_settings: true*/
jQuery( window ).ready( function( $ ) {

	var $accordion = $( '#skystats-settings-accordion' );
	$accordion.accordion({
		animate: 'swing',
		collapsible: true,
		header: 'h3',
		heightStyle: 'content',
		icons: {
			'header': 'skystats-accordion-header-icon',
			'activeHeader': 'skystats-accordion-header-active-icon'
		},
		create: function( event, ui ) {
			$( '#skystats_reports_users_allowed_access' ).chosen({
				width: '100%'
			});
			$( '#skystats_settings_users_allowed_access').chosen({
				width: '100%'
			});
		}
	});

	if ( 'settings' === skystats_settings['active_accordion_section'] ) {
		$accordion.accordion( 'option', 'active' , 1 );
	}

	/* Validate license automatically with no need to refresh page */
	$( '#validate_license' ).click( function ( e ) {
		e.preventDefault();
		var $loadingIcon = $( '#skystats-settings-validate-license-loading-container' ),
			$successTarget = $( '#skystats-successful-license-validation-request' ),
			$errorTarget = $( '#skystats-unsuccessful-license-validation-request' ),
			$validateTarget = $( this ),
			licenseKey = $( '#license_key' ).val();

		$( '#skystats-license-info-message' ).fadeOut();

		$successTarget.add( $errorTarget ).add( $validateTarget ).fadeOut( 400 ).promise().done( function() {
			$loadingIcon.fadeIn( 400, function() {
				$.get( ajaxurl, {
					action: 'skystats_ajax_licensing_validate_license',
					license_key: licenseKey
				}, function ( response ) {
					response = $.parseJSON( response );

					if ( 'success' === response['responseType'] ) {

						// Save license type
						$.post( ajaxurl, {
							action: 'skystats_ajax_licensing_save_license_type',
							license_type: ( null != response['data'] ) ? response['data']['licenseType'] : null
						});

						switch ( response['responseContext'] ) {
							case 'license_activated':
								$successTarget.html( skystats_settings['trans']['licensing']['license_activated'] );
								break;
							case 'license_valid':
							default:
								$successTarget.html( skystats_settings['trans']['licensing']['license_valid'] );
								break;
						}
						$loadingIcon.fadeOut( 400, function () {
							$successTarget.fadeIn();
							$validateTarget.fadeIn();
						});
					} else if ( 'error' === response['responseType'] ) {
						switch ( response['responseContext'] ) {
							case 'license_invalid':
								$errorTarget.html( skystats_settings['trans']['licensing']['license_invalid'] );
								break;
							case 'missing_license_key':
								$errorTarget.html( skystats_settings['trans']['licensing']['missing_license_key'] );
								break;
							case 'error_initializing_request':
								$errorTarget.html( skystats_settings['trans']['licensing']['error_initializing_request'] );
								break;
							case 'error_executing_request':
								$errorTarget.html( skystats_settings['trans']['licensing']['error_executing_request'] );
								break;
							case 'malformed_response':
								$errorTarget.html( skystats_settings['trans']['licensing']['malformed_response'] );
								break;
							case 'license_site_inactive_no_activations_left':
								$errorTarget.html( skystats_settings['trans']['licensing']['license_site_inactive_no_activations_left'] );
								break;
							case 'license_site_inactive_activation_error':
								$errorTarget.html( skystats_settings['trans']['licensing']['license_site_inactive_activation_error'] );
								break;
							case 'license_inactive_no_activations_left':
								$errorTarget.html( skystats_settings['trans']['licensing']['license_inactive_no_activations_left'] );
								break;
							case 'license_inactive_activation_error':
								$errorTarget.html( skystats_settings['trans']['licensing']['license_inactive_activation_error'] );
								break;
							case 'license_expired':
								var licenseExpiredMsg = skystats_settings['trans']['licensing']['license_expired'];
								licenseExpiredMsg = licenseExpiredMsg.replace( '{LICENSE_KEY}', licenseKey );
								$errorTarget.html( licenseExpiredMsg );
								break;
							case 'license_disabled':
								$errorTarget.html( skystats_settings['trans']['licensing']['license_disabled'] );
								break;
							case 'http_error':
								$errorTarget.html( skystats_settings['trans']['licensing']['http_error'] );
								break;
							case 'license_unknown_status':
							default:
								$errorTarget.html( skystats_settings['trans']['licensing']['license_unknown_status'] );
								break;
						}
						$loadingIcon.fadeOut( 400, function () {
							$errorTarget.fadeIn();
							$validateTarget.fadeIn();
						});
					}
				});
			});
		});
	});

	/* Allow images to be uploaded and set automatically */
	var uploadImageInputs = [],
		uploadImageButtons = [],
		uploadImageURLPlaceholders = [];

	$( '.skystats-upload-image' ).each( function ( i, v ) {

		var $this = $( v ),
			$uploadImgButton = $this.prev(),
			$loadingImg = $( '.skystats-loading-container' ).first().clone();

		$loadingImg.attr( 'id', '' );
		$loadingImg.attr( 'style', 'margin-top: 10px;' );

		uploadImageInputs[i] = $this;
		uploadImageButtons[i] = $uploadImgButton;
		uploadImageURLPlaceholders[i] = $uploadImgButton.parent().prev().children().first();

		$this.fileupload( {
			dataType: 'json',
			formData: { action: 'skystats_ajax_upload_image' },
			url: ajaxurl,
			add: function ( e, data ) {
				uploadImageButtons[i].hide();
				$loadingImg.insertAfter( uploadImageButtons[i] );
				$loadingImg.show();
				data.submit();
			},
			done: function ( e, data ) {
				var newImageURL = data.result;
				$loadingImg.remove();
				if ( newImageURL ) {
					uploadImageURLPlaceholders[i].val( newImageURL );
				}
				uploadImageButtons[i].show();
			}
		} );
	} );

	/* Save settings without need for refresh */
	$( '#save_settings' ).click( function( e ) {
		e.preventDefault();
		var $loadingImg = $( '#skystats-settings-save-settings-loading-container' ),
			$saveSettingsButton = $( this ),
			$saveSettingsResult = $( '#skystats-save-settings-result' ),
			$reloadSettingsButton = $( '#settings-reload' );
		$loadingImg.show();
		$saveSettingsButton.add( $reloadSettingsButton ).add( $saveSettingsResult ).fadeOut( 400 ).promise().done( function() {
			$loadingImg.fadeIn( 400, function() {
				$.post( ajaxurl, {
					action: 'skystats_ajax_settings_save_settings',
					role_access: $( '#role_access' ).val(),
					default_dashboard: $( '#default_dashboard' ).val(),
					cache_mode: $( '#cache_mode' ).val(),
					skystats_reports_users_allowed_access: $( '#skystats_reports_users_allowed_access').val(),
					skystats_settings_users_allowed_access : $( '#skystats_settings_users_allowed_access' ).val()
				}, function () {
					$loadingImg.fadeOut( 400, function() {
						$saveSettingsResult.add( $saveSettingsButton ).add( $reloadSettingsButton ).fadeIn( 400 ).promise().done( function() {} );
					});
				});
			});
		});
	});

	// Use WP native colorpicker on versions 3.5+
	if ( skystats_settings.wp_version >= '3.5' ) {
		$( '#brand_background_color' ).wpColorPicker();
	// Fallback to different colorpicker for versions < 3.5.
	} else {
		$( '#brand_background_color' ).ColorPicker( {
			onChange: function ( hsb, hex, rgb, el ) {
				var $el = $( el );
				$( '#brand_background_color' ).val( '#' + hex );
			},
			onSubmit: function ( hsb, hex, rgb, el ) {
				var $el = $( el );
				$el.val( '#' + hex );
				$el.ColorPickerHide();
			}
		} );
	}
});