/*http://www.jslint.com/*/
/*jslint white: true */
/*global jQuery, window, console, ajaxurl, skystats_google_adwords*/
jQuery( window ).ready( function( $ ) {
	var $pageLoadingContainer = $( '#skystats-page-loading-container' ),
		$pageErrorContainer = $( '#skystats-page-error-container' ),
		$pageErrorContainerMsg = $pageErrorContainer.find( 'p' ),
		$dateRangeContainer = $( '#skystats-date-range-container' ),
		$dataContainer = $( '#skystats-google-adwords-data-container' );

	$.get( ajaxurl, {
		action: 'skystats_ajax_licensing_api_query',
		query : 'check_license'
	}, function( response ) {
		response = $.parseJSON( response );
		if ( 'error' === response.responseType ) {
			switch ( response['responseContext'] ) {
				case 'missing_license_key':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['missing_license_key'] );
					break;
				case 'license_expired':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['license_expired'] );
					break;
				case 'malformed_license_key':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['malformed_license_key'] );
					break;
				case 'http_error':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['http_error'] );
					break;
				case 'error_initialising_request':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['error_initializing_request'] );
					break;
				case 'error_executing_request':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['error_executing_request'] );
					break;
				case 'malformed_response':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['malformed_request'] );
					break;
				case 'license_site_inactive':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['license_site_inactive'] );
					break;
				case 'license_site_inactive_no_activations_left':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['license_site_inactive_no_activations_left'] );
					break;
				case 'license_inactive':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['license_inactive'] );
					break;
				case 'license_inactive_no_activations_left':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['license_inactive_no_activations_left'] );
					break;
				case 'license_disabled':
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['license_disabled'] );
					break;
				case 'license_unknown_status':
				default:
					$pageErrorContainerMsg.html( skystats_google_adwords['trans']['licensing']['license_unknown_status'] );
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
					minDate: '01/01/2005',
					maxDate: 'today',
					onChangeMonthYear: function( year, month, inst ) {
						if ( month < 10 ) {
							month = '0' + month;
						}
						var day = inst.selectedDay;
						if ( day < 10 ) {
							day = '0' + day;
						}
						$( this ).val( month + '/' + day + '/' + year );
					}
				};

				var $startDate = $( '#start_date' ),
					$endDate = $( '#end_date' );

				$startDate.datepicker( dateOptions );
				$endDate.datepicker( dateOptions );

				$startDate.datepicker( 'setDate', '-29d' );
				$endDate.datepicker( 'setDate', 'today' );

				var integration = getIntegration( 'googleAdwords', 'detail' );

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