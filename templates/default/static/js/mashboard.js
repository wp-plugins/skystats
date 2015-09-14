/*http://www.jslint.com/*/
/*jslint white: true */
/*global jQuery, window, console, ajaxurl, skystats_mashboard*/
jQuery( window ).ready( function( $ ) {
	'use strict';
	var $cardsLoadingContainer = $( '#skystats-cards-loading-container' );
	$.get( ajaxurl, {
		action: 'skystats_ajax_licensing_api_query',
		query : 'check_license'
	}, function( response ) {
		response = $.parseJSON( response );
		if ( 'error' === response.responseType ) {
			var $pageErrorContainer = $( '#skystats-page-error-container' ),
				$pageErrorContainerMsg = $pageErrorContainer.find( 'p' );
			switch ( response['responseContext'] ) {
				case 'missing_license_key':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['missing_license_key'] );
					break;
				case 'license_expired':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['license_expired'] );
					break;
				case 'malformed_license_key':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['malformed_license_key'] );
					break;
				case 'http_error':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['http_error'] );
					break;
				case 'error_initialising_request':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['error_initializing_request'] );
					break;
				case 'error_executing_request':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['error_executing_request'] );
					break;
				case 'malformed_response':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['malformed_request'] );
					break;
				case 'license_site_inactive':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['license_site_inactive'] );
					break;
				case 'license_site_inactive_no_activations_left':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['license_site_inactive_no_activations_left'] );
					break;
				case 'license_inactive':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['license_inactive'] );
					break;
				case 'license_inactive_no_activations_left':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['license_inactive_no_activations_left'] );
					break;
				case 'license_disabled':
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['license_disabled'] );
					break;
				case 'license_unknown_status':
				default:
					$pageErrorContainerMsg.html( skystats_mashboard['trans']['licensing']['license_unknown_status'] );
					break;
			}
			$cardsLoadingContainer.fadeOut( 400, function () {
				$pageErrorContainer.fadeIn();
			} );
		} else {
			$.get( ajaxurl, {
				'action': 'skystats_ajax_get_mashboard_card_positions'
			}, function ( response ) {

				response = $.parseJSON( response );

				// Re-position cards
				$.each( response, function ( column, cards ) {
					var columnId = '#' + column;
					$.each( cards, function ( unused, card ) {
						var $card = $( '#' + card );
						$card.appendTo( columnId );
					} );
				} );

				$cardsLoadingContainer.fadeOut( 400, function () {
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
						$endDate = $( '#end_date' );

					$startDate.datepicker( dateOptions );
					$endDate.datepicker( dateOptions );

					$startDate.datepicker( 'setDate', '-29d' );
					$endDate.datepicker( 'setDate', 'today' );

					$( '#skystats-date-range-container' ).fadeIn();

					$( '#skystats-date-range-notice-container' ).fadeIn();

					$( '.skystats-card' ).fadeIn();

					// Init tooltips
					initTooltips();

					var integrations = getIntegrations( 'mashboard' ),
						integrationsLen = integrations.length;

					makeCardsSortable( integrations, integrationsLen );

					// Update integrations
					$( '#date_range' ).click( function () {
						var checkedDates = false;
						for ( var i = 0; i < integrationsLen; ++i ) {
							integrations[ i ].updateDataTabData( checkedDates );
							checkedDates = true;
						}
					} );

					$( window ).resize( function () {
						for ( var i = 0; i < integrationsLen; ++i ) {
							integrations[ i ].resizeChart();
						}
					} );

					for ( var i = 0; i < integrationsLen; ++i ) {
						integrations[ i ].loadDataTabData();
					}
				} );
			} );
		}
	});
});