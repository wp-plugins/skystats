/*http://www.jslint.com/*/
/*jslint white: true */
/*global jQuery, window, console, ajaxurl, skystats_facebook*/
jQuery( window ).ready( function( $ ) {
	var $pageLoadingContainer = $( '#skystats-page-loading-container' ),
		$pageErrorContainer = $( '#skystats-page-error-container' ),
		$pageErrorContainerMsg = $pageErrorContainer.find( 'p' ),
		$dateRangeContainer = $( '#skystats-date-range-container' ),
		$dataContainer = $( '#skystats-facebook-data-container' );

	$.get( ajaxurl, {
		action: 'skystats_ajax_licensing_api_query',
		query : 'check_license'
	}, function( response ) {
		response = $.parseJSON( response );
		if ( 'error' === response.responseType ) {
			switch ( response['responseContext'] ) {
				case 'missing_license_key':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['missing_license_key'] );
					break;
				case 'license_expired':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['license_expired'] );
					break;
				case 'malformed_license_key':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['malformed_license_key'] );
					break;
				case 'http_error':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['http_error'] );
					break;
				case 'error_initialising_request':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['error_initializing_request'] );
					break;
				case 'error_executing_request':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['error_executing_request'] );
					break;
				case 'malformed_response':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['malformed_request'] );
					break;
				case 'license_site_inactive':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['license_site_inactive'] );
					break;
				case 'license_site_inactive_no_activations_left':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['license_site_inactive_no_activations_left'] );
					break;
				case 'license_inactive':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['license_inactive'] );
					break;
				case 'license_inactive_no_activations_left':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['license_inactive_no_activations_left'] );
					break;
				case 'license_disabled':
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['license_disabled'] );
					break;
				case 'license_unknown_status':
				default:
					$pageErrorContainerMsg.html( skystats_facebook['trans']['licensing']['license_unknown_status'] );
					break;
			}
			$pageLoadingContainer.fadeOut( 400, function () {
				$pageErrorContainer.fadeIn();
			} );
		} else {
			$pageLoadingContainer.fadeOut( 400, function() {
				$dateRangeContainer.fadeIn();
				$dataContainer.fadeIn();
				$( '#skystats-date-range-notice-container' ).fadeIn();

				var dateOptions = {
					dateFormat: 'mm/dd/yy',
					changeMonth: true,
					changeYear: true,
					minDate: -30,
					maxDate: 'today',
					onSelect: function( d, i ) {
						if ( d !== i.lastVal ) {
							$( this ).change();
						}
					}
				};

				var $startDate = $( '#start_date' ),
					$endDate = $( '#end_date' ),
					minDate = new Date(),
					minDateOffset = ( 24 * 60 * 60 * 1000 ) * 30;

				minDate.setTime(  minDate.getTime() - minDateOffset );
				var minDateStr = minDate.toDateString();

				$startDate.change( function() {
					var startDateStr = new Date( $( this ).val() ).toDateString(),
						endDateStr = new Date( $endDate.val() ).toDateString();
					/* Start date is today and the end date is the maximum allowed date, which exceeds the 30 day limit
					 Decrease the end date by a single day */
					if ( startDateStr === minDateStr && endDateStr === new Date().toDateString() ) {
						$endDate.datepicker( 'setDate', '-1' );
					}
				});
				$endDate.change( function() {
					var endDateStr = new Date( $( this ).val() ).toDateString(),
						now = new Date().toDateString(),
						startDate = new Date( $startDate.val() ).toDateString();
					/* End date is today and start date is the minimum allowed date which exceeds the 30 day limit
					 Increase the start date by a day. */
					if ( endDateStr === now && minDateStr === startDate ) {
						$startDate.datepicker( 'setDate', '-29d' );
					}
				});

				$startDate.datepicker( dateOptions );
				$endDate.datepicker( dateOptions );

				$startDate.datepicker( 'setDate', '-30d' );
				$endDate.datepicker( 'setDate', '-1d' );

				var integration = getIntegration( 'facebook', 'detail' );

				initTooltips();

				// Update integrations
				$( '#date_range' ).click( function() {
					integration.updateDataTabData( false );
				});

				// Resize chart
				$( window ).resize( function() {
					integration.resizeChart();
				});

				// Initially, load and display data or settings
				integration.loadDataTabData();
			});
		}
	});
});