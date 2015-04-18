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
						$( '#' + card ).appendTo( columnId );
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

					$( '#skystats-date-range-container' ).fadeIn();

					$( '#skystats-date-range-notice-container' ).fadeIn();

					$( '.skystats-card' ).fadeIn();

					var $voteNowButton = $( '.skystats-mashboard-vote-now' );
					$voteNowButton.addClass( 'skystats-disabled' );

					// Fetch number of votes
					$.get( ajaxurl, {
						action: 'skystats_ajax_get_integration_vote_totals'
					}, function( response ) {
						response = $.parseJSON( response );
						if ( null != response.result ) {
							$.each( response.result, function( i, v ) {
								var selector = '#skystats-' + i + '-integration-vote-totals-container',
									$container = $( selector ),
									$strong = $container.find( '>strong' ),
									$span = $container.find( '>span' );
								$strong.text( v );
								if ( parseInt( v ) === 1 ) {
									$span.text( skystats_mashboard['trans']['integration_vote'] );
								} else {
									$span.text( skystats_mashboard['trans']['integration_votes'] );
								}
							});
						}
						$( '.skystats-integration-vote-totals-loading-container' ).fadeOut( 400, function() {
							$( '.skystats-integration-vote-totals-container' ).fadeIn();
							$voteNowButton.removeClass( 'skystats-disabled' );
						});
					});

					// When a user votes for an integration
					$voteNowButton.off( 'click' );
					$voteNowButton.on( 'click', function( e ) {
						e.preventDefault();
						var $img = $( this );
						if ( $img.hasClass( 'skystats-disabled' ) ) {
							return;
						}
						var $container = $img.prev(),
							$strong = $container.find( '>strong' ),
							$span = $container.find( '>span' ),
							$loadingIcon = $( '.skystats-loading-container' ).first().clone();
						$loadingIcon.attr( 'id', '' );
						$loadingIcon.addClass( 'skystats-integration-vote-loading-container' );
						$loadingIcon.insertBefore( $img );
						$loadingIcon.show();
						$img.hide();
						var integration = $img.attr( 'data-integration' );

						$( '#skystats-mashboard-' + integration + '-vote-result-message' )
							.remove()
							.end();
						$( '#skystats-mashboard-' + integration + '-vote-result-sub-message' )
							.remove();

						// User must opt-in to vote.
						if ( 'true' !== skystats_mashboard['voting_opted_in'] ) {
							var $messages = $( '<p id="skystats-mashboard-' + integration + '-vote-result-message" class="skystats-mashboard-vote-result-message">'
							+ skystats_mashboard['trans']['voting_not_opted_in_message'] + '</p>' +
							'<p id="skystats-mashboard-' + integration + '-vote-result-sub-message" class="skystats-mashboard-vote-result-sub-message">'
							+ skystats_mashboard['trans']['voting_not_opted_in_sub_message'] + '</p>' );
							$messages.insertBefore( $img );
							$img.parent().children().first().remove();
							$img.remove();
							$loadingIcon.remove();
							return;
						}

						var $messages = $( '<p id="skystats-mashboard-' + integration + '-vote-result-message" class="skystats-mashboard-vote-result-message">'
						+ skystats_mashboard['trans']['integration_vote_result_message'] + '</p>' +
						'<p id="skystats-mashboard-' + integration + '-vote-result-sub-message" class="skystats-mashboard-vote-result-sub-message">'
						+ skystats_mashboard['trans']['integration_vote_result_sub_message'] + '</p>' );

						$.post( ajaxurl, {
							'action'      : 'skystats_ajax_vote_for_integration',
							'integration' : integration
						}, function() {
							var newVotesTotal = parseInt( $strong.text() ) + 1;
							$strong.text( newVotesTotal );
							if ( newVotesTotal > 1 ) {
								$span.text( skystats_mashboard['trans']['integration_votes'] );
							} else {
								$span.text( skystats_mashboard['trans']['integration_vote'] );
							}
							$messages.insertBefore( $img );
							$img.parent().children().first().remove();
							$img.remove();
							$loadingIcon.remove();
						});
					});

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