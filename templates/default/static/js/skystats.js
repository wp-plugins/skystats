/*http://www.jslint.com/*/
/*jslint white: true */
/*global jQuery, skystats*/
jQuery( window ).ready( function( $ ) {

	// Check SVG support and use .gif fallbacks for our .svg loading images
	function supportsSVG() {
		return !!document.createElementNS && !! document.createElementNS( 'http://www.w3.org/2000/svg', 'svg' ).createSVGRect;
	}
	if ( ! supportsSVG() ) {
		var $loadingImg = $( '.skystats-loading-image' );
		$loadingImg.each( function() {
			this.src = this.src.replace( '.svg', '.gif' );
		});
	}

	if ( null != skystats.brand_background_image_url && skystats.brand_background_image_url ) {

		// The width at which a background color will be used in place of an image
		var widthShowColor = 500;

		backstretchAction( 'init' );

		// Remove background image on smaller screens
		$( window ).resize( function () {
			if ( $( this ).width() <= widthShowColor ) {
				backstretchAction( 'destroy' );
			} else {
				backstretchAction( 'init' );
			}
		} );
	}

	// Perform an action on Backstretch
	function backstretchAction( type ) {

		var instance = $( 'body' ).data( 'backstretch' );

		// Destroy Backstretch and use background color instead
		if ( 'destroy' === type && instance ) {
			$.backstretch( 'destroy', false );
			$( '#wpcontent' ).css( 'background-color', skystats.brand_background_color );

			// Initialiase Backstretch
		} else if ( 'init' === type && !instance ) {

			/*
			 * If screen is already at width we want to target to destroy Backstretch,
			 * don't initialise.
			 */
			if ( $( this ).width() <= widthShowColor ) {
				return;
			}

			$.backstretch( skystats.brand_background_image_url );
			$( '#wpcontent' ).css( 'background-color', 'transparent' );
		}
	}
});