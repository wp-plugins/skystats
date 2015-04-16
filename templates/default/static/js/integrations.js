/*http://www.jslint.com/*/
/*jslint white: true */
/*global ajaxurl, skystats_integrations, skystats_mashboard, skystats_google_analytics, skystats_facebook, console, window*/
$ = jQuery;
function createObject( proto ) {
	function Ctor() { }
	Ctor.prototype = proto;
	return new Ctor();
}

// Integration - parent
function Integration( viewName ) {
	this.viewName = viewName;
	this.startDate = $( '#start_date' ).val();
	this.endDate = $( '#end_date' ).val();
	this.chartOptions = {
		grid: {
			backgroundColor: null, /* Transparent, automatically handles transparency for IE */
			borderColor: {
				top: '#c8c8c8',
				left: '#c8c8c8',
				right: '#c8c8c8',
				bottom: '#c8c8c8'
			},
			borderWidth: 1,
			hoverable: true
		},
		legend: {
			show: false
		},
		series: {
			lines: {
				show: true
			},
			points: {
				fillColor: null, /* Use color of each data series */
				fill: 1, /* Opaque */
				radius: 3.5,
				show: true
			}
		},
		shadowSize: 0,
		tooltip: true,
		tooltipOpts: {
			content: function ( label, x, y ) {
				return y.toString();
			}
		},
		xaxis: {
			mode: 'categories'
		}
	};
	if ( 'detail' === this.viewName ) {
		this.$chartFigure = $( '#skystats-detail-chart-key-container' );
	}
}

/**
 * Show the chart key/figure/legend (currently only present on the detail pages).
 */
Integration.prototype.showChartFigure = function() {
	if ( 'detail' !== this.viewName ) {
		return;
	}
	this.$chartFigure.show();
};
/**
 * Hide the chart key/figure/legend.
 */
Integration.prototype.hideChartFigure = function() {
	if ( 'detail' !== this.viewName ) {
		return;
	}
	this.$chartFigure.hide();
};

/**
 * @param checkedDates True if dates were checked otherwise false. Prevents error messages from fading in our out more than once per update (occurs on mashboard only).
 */
Integration.prototype.updateDataTabData = function( checkedDates ) {

	var $pageErrorContainer = $( '#skystats-page-error-container' );

	if ( ! checkedDates ) {
		$pageErrorContainer.fadeOut();
	}

	if ( 'Facebook' === this.integrationName ) {
		var $facebookErrorContainer = $( '#skystats-facebook-error-container' );
		$facebookErrorContainer.fadeOut();
	} else if ( 'Google Analytics' === this.integrationName ) {
		var $googleAnalyticsErrorContainer = $( '#skystats-google-analytics-error-container' );
		$googleAnalyticsErrorContainer .fadeOut();
	}

	// Ensure we update the dates used, for all integrations, so the correct date is used when the integration returns
	// to the data tab.
	this.startDate = $( '#start_date' ).val();
	this.endDate = $( '#end_date' ).val();

	if ( 'Google Analytics' === this.integrationName && 'detail' === this.viewName ) {
		this.frequency = $( '#frequency' ).val();
	}

	// If we're viewing or loading the settings tab / setting tab data, do nothing.
	// Otherwise, fetch new data and display it.
	if ( this.viewingSettingsTab ) {
		return;
	}
	if ( this.loadingSettingsTabData ) {
		return;
	}

	var startDateObj = new Date( this.startDate ),
		startDateInSecs = startDateObj.getTime() / 1000,
		endDateObj = new Date( this.endDate ),
		endDateInSecs = endDateObj.getTime() / 1000,
		minDateOffset = ( 24 * 60 * 60 * 1000 ) * 31,
		minDate = new Date(),
		currentRangeInSecs = ( endDateObj - startDateObj ) / 1000 + 86400,
		currentRangeInDays = currentRangeInSecs / 60 / 60 / 24,
		$pageErrorContainerMsg = $pageErrorContainer.find( '> p' ),
		error = false;

	minDate.setTime( minDate.getTime() - minDateOffset );
	var minDateSecs = minDate.getTime() / 1000;

	if ( currentRangeInDays <= 1 ) {
		$pageErrorContainerMsg.html( this.translations['date_range_same']);
		error = true;
	} else if ( currentRangeInSecs > Date.now() ) {
		$pageErrorContainerMsg.html( this.translations['date_range_exceeds_today']);
		error = true;
	} else if ( startDateInSecs < minDateSecs || endDateInSecs < minDateSecs ) {
		if ( 'detail' === this.viewName ) {
			$pageErrorContainerMsg.html( this.translations['date_range_below_min'] );
			$pageErrorContainer.fadeIn();
		} else {
			if ( 'Facebook' === this.integrationName ) {
				$facebookErrorContainer.find( '> p' ).html( this.translations['date_range_below_min'] );
				$facebookErrorContainer.fadeIn();
			} else if ( 'Google Analytics' === this.integrationName ) {
				$googleAnalyticsErrorContainer.find( '> p' ).html( this.translations['date_range_below_min'] );
				$googleAnalyticsErrorContainer.fadeIn();
			}
		}
	}

	if ( error ) {
		if ( ! checkedDates ) {
			$pageErrorContainer.fadeIn();
		}
		return;
	}

	// Reset data
	this.data = null;
	if ( 'detail' === this.viewName ) {
		this.loadedAllData = false;
	}

	setGlobalPossibleMarkers( null );

	this.disableGridIcon();
	this.disableSettingsIcon();
	this.hideChartFigure();

	// Hide/show required elements and fetch and display new data
	this.loadDataTabData();
};

Integration.prototype.resizeChart = function() {
};

Integration.prototype.reinitGridChildrenContent = function() {
	this.gridIconChildrenContent = $( this.gridIconContentSelector ).html();
};

Integration.prototype.enableGridIcon = function() {
	this.$gridIcon.removeClass( 'skystats-disabled' );
	this.$gridIcon.tooltip({
		content: this.$gridIcon.attr( 'data-tooltip' ),
		items  : '[data-tooltip]'
	});
	var curInst = this;
	this.$gridIcon.on( this.gridClickEventName, function() {
		var content = '';
		if ( true === curInst.gridDisplayed ) {
			content = curInst.$gridIcon.attr( 'data-tooltip' );
			curInst.$gridIcon.tooltip( 'option', 'content', content );
			curInst.gridDisplayed = false;
		} else {
			curInst.$gridIcon.removeClass( 'skystats-tooltip' );
			curInst.gridDisplayed = true;

			// Initially check the boxes of any inputs that correspond to a visible data point
			var $inputs = $( curInst.gridIconContentSelector ).find( 'input' );
			$inputs.each( function ( i, v ) {
				var $this = $( v );
				if ( $( '#' + $this.val() ).is( ':visible' ) ) {
					$this.attr( 'checked', true );
				} else {
					$this.attr( 'checked', false );
				}
			});

			// Now re add the grid content again so our changes take effect (correct boxes ticked).
			curInst.reinitGridChildrenContent();

			content = curInst.gridIconChildrenContent;
			curInst.$gridIcon.tooltip( 'destroy' );
			curInst.$gridIcon.tooltip({
				content : content,
				items: curInst.gridIconSelector
			});
			curInst.$gridIcon.tooltip( 'open' );

			// Allow the boxes to be checked and unchecked making the corresponding data point show or hide.
			$( '.skystats-show-data-point' ).change( function () {
				var $this = $( this );
				if ( this.checked ) {
					$('#' + $this.val() ).show();
					$this.attr( 'checked', true );
				} else {
					$( '#' + $this.val() ).hide();
					$this.attr( 'checked', false );
				}
			});
		}
	});

	this.$gridIcon.on( this.gridMouseoverEventName + ' ' + this.gridMouseleaveEventName + ' ' + this.gridMouseoutEventName, function ( e ) {
		if ( true === curInst.gridDisplayed ) {
			e.stopImmediatePropagation();
		}
	});
};
Integration.prototype.disableGridIcon = function() {
	this.$gridIcon.addClass( 'skystats-disabled' );
	var instance = this.$gridIcon.tooltip( 'instance' );
	if ( null != instance ) {
		instance.destroy();
	}
	this.$gridIcon.off( this.gridClickEventName + ' ' + this.gridMouseoverEventName + ' ' + this.gridMouseleaveEventName + ' ' + this.gridMouseoutEventName );
	// Ensures grid does the right thing when clicked after being disabled whilst open.
	this.gridDisplayed = false;
};

Integration.prototype.enableSettingsIcon = function() {
	var curInst = this;
	this.$settingsIcon.removeClass( 'skystats-disabled' );
	this.$settingsIcon.tooltip({
		content: this.$settingsIcon.attr( 'data-tooltip' ),
		items  : '[data-tooltip]'
	});
	this.$settingsIcon.on( this.settingsClickEventName, function() {

		// load/show data tab
		if ( true === curInst.viewingSettingsTab ) {
			if ( true === curInst.loadingDataTabData ) {
				return;
			}
			curInst.viewingSettingsTab = false;
			curInst.loadingDataTabData = true;
			curInst.disableSettingsIcon();
			curInst.loadDataTabData();
			// load/show settings tab
		} else if ( true === curInst.viewingDataTab ) {
			if ( true === curInst.loadingSettingsTabData ) {
				return;
			}
			curInst.viewingDataTab = false;
			curInst.loadingSettingsTabData = true;
			curInst.disableGridIcon();
			curInst.disableSettingsIcon();
			curInst.loadSettingsTabData( 'cached' );
		}
	});
};

/**
 * Disable the settings icon tooltip and tab switchability.
 */
Integration.prototype.disableSettingsIcon = function() {
	this.$settingsIcon.addClass( 'skystats-disabled' );

	var instance = this.$settingsIcon.tooltip( 'instance' );

	if ( null != instance ) {
		instance.destroy();
	}

	this.$settingsIcon.off( this.settingsClickEventName );
};

/**
 * Changes the content of any tooltip element or object.
 *
 * @param {jQuery|string} tooltip
 *
 * @param {string} content
 */
Integration.prototype.changeTooltipContent = function( tooltip, content ) {
	var instance = null;

	if ( tooltip instanceof jQuery ) {
		instance = tooltip.tooltip( 'instance' );
	} else {
		instance = $( tooltip ).tooltip( 'instance' );
	}

	if ( null != instance ) {
		instance.option( 'content', content );
	}
};

// Google Analytics - child
function GoogleAnalytics( viewName ) {
	Integration.call( this, viewName );
	this.integrationName = 'Google Analytics';
	this.translations = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['trans']:
		skystats_google_analytics['trans'];

	this.selectedProfileID = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['google_analytics']['selected_profile_id'] :
		skystats_google_analytics['selected_profile_id'];
	this.data = null;
	this.$loadingContainer = $( '#skystats-google-analytics-loading-container' );
	this.$dataPointsContainer = $( '#skystats-google-analytics-data-points-container' );
	this.$dataTableColumnsContent = $( '.skystats-data-table-column-content' );
	this.loadingDataTabData = false;
	this.viewingDataTab = false;
	this.loadedAllData = false; // Detail specific (true if the chart data, data point and data table data was successfully retrieved, or was at least attempted (but one or more may have failed)

	this.topKeywordsData = null;
	this.topSearchEngineReferralsData = null;
	this.topLandingPagesData = null;
	this.topVisitorLocationsData = null;

	/* Chart */
	this.chartID = 'skystats-google-analytics-chart';
	this.$chart = $( '#' + this.chartID );
	this.$chartContainer = $( '#skystats-google-analytics-chart-container' ); // Chart parent
	this.chartInstance = null;
	this.frequency = 'daily';
	this.chartData = [
		{
			color: '#FFC951',
			data: [],
			label: '' /* Users */
		},
		{
			color: '#FF956C',
			data: [],
			label: '' /* Page Views */
		}
	];

	/* Grid */
	this.gridIconSelector = '#skystats-google-analytics-grid-icon';
	this.$gridIcon = $( this.gridIconSelector );
	this.gridIconContentSelector = '#skystats-google-analytics-grid-icon-content';
	this.gridIconChildrenContent = $( '#skystats-google-analytics-grid-icon-content' ).html();
	this.gridDisplayed = false;
	this.gridClickEventName = 'click.ga_grid';
	this.gridMouseoverEventName = 'mouseover.ga_grid';
	this.gridMouseleaveEventName = 'mouseleave.ga_grid';
	this.gridMouseoutEventName = 'mouseout.ga_grid';

	/* Settings */
	this.$settingsIcon = $( '#skystats-google-analytics-settings-icon' );
	this.loadingSettingsTabData = false;
	this.viewingSettingsTab = false;

	// All sections
	this.$settingsTabSections = $( '.skystats-google-analytics-settings-tab-section' );

	this.$settingsTabSetupSection = $( '#skystats-google-analytics-settings-setup-section' );
	this.$settingsTabReauthorizeSection = $( '#skystats-google-analytics-settings-reauthorize-section' );
	this.$settingsTabDeauthorizeSection = $( '#skystats-google-analytics-settings-deauthorize-section' );
	this.$settingsTabNoProfilesSection = $( '#skystats-google-analytics-settings-no-profiles-section' );
	this.$settingsTabProfilesSection = $( '#skystats-google-analytics-settings-profiles-section' );

	this.settingsClickEventName = 'click.ga_settings';
}
GoogleAnalytics.prototype = createObject( Integration.prototype );
GoogleAnalytics.prototype.constructor = GoogleAnalytics;

/**
 * Called whenever the browser window resizes, to try to resize the chart and any other tasks that need to be performed.
 */
GoogleAnalytics.prototype.resizeChart = function() {
	if ( null != this.data ) {
		setGlobalPossibleMarkers( null );
		this.chartInstance.shutdown();
		this.chartData[ 0 ].data = getChartData( this.data['chart_data']['users'], this.$chart, this, this.frequency );
		this.chartData[ 1 ].data = getChartData( this.data['chart_data']['page_views'], this.$chart, this, this.frequency );
		this.chartInstance = this.$chart.plot( this.chartData, this.chartOptions ).data( 'plot' );
	}
};

/**
 * Load the data for the data tab.
 */
GoogleAnalytics.prototype.loadDataTabData = function() {
	var $frequency = $( '#frequency' );
	if ( $frequency.length ) {
		this.frequency = $frequency.val();
	}
	this.viewingSettingsTab = false;
	this.loadingDataTabData = true;
	var curInst = this;
	this.$settingsTabSections
		.add( this.$chartContainer )
		.add( this.$dataPointsContainer )
		.add( this.$dataTableColumnsContent )
		.fadeOut( 400 ).promise().done( function() {
		curInst.$loadingContainer.fadeIn( 400, function() {
			if ( 'mashboard' === curInst.viewName ) {
				if ( curInst.data ) {
					curInst.displayDataTabData();
					return;
				}
				$.get( ajaxurl, {
					action: 'skystats_ajax_google_analytics_api_query',
					query: 'get_mashboard_view_data',
					start_date: curInst.startDate,
					end_date: curInst.endDate
				}, function ( response ) {
					response = $.parseJSON( response );
					if ( 'success' === response['responseType'] ) {
						curInst.data = response.data;
						curInst.displayDataTabData( response );
					} else {
						curInst.loadSettingsTabData( 'fresh' );
					}
				});
			} else if ( 'detail' === curInst.viewName ) {
				if ( curInst.loadedAllData ) {
					curInst.displayDataTabData();
				} else {
					var detailViewDataDeferredObject = curInst.getDetailViewDataDeferredObject(),
						topKeywordsDeferredObject = curInst.getTopKeywordsDeferredObject(),
						topSearchEngineReferralsDeferredObject = curInst.getTopSearchEngineReferralsDeferredObject(),
						topLandingPagesDeferredObject = curInst.getTopLandingPagesDeferredObject(),
						topVisitorLocationsDeferredObject = curInst.getTopVisitorLocationsDeferredObject();
					$.when( detailViewDataDeferredObject, topKeywordsDeferredObject, topSearchEngineReferralsDeferredObject, topLandingPagesDeferredObject, topVisitorLocationsDeferredObject )
						.done( function( detailViewData, topKeywordsData, topSearchEngineReferralsData, topLandingPagesData, topVisitorLocationsData ) {

							var detailViewDataObj = $.parseJSON( detailViewData[ 0 ] );

							if ( 'error' === detailViewDataObj['responseType'] ) {
								curInst.loadSettingsTabData( 'fresh' );
							} else {
								// Signal data was retrieved
								curInst.loadedAllData = true;
								// Store data
								curInst.data = $.parseJSON( detailViewData[0] ).data;
								curInst.topKeywordsData = $.parseJSON( topKeywordsData[0] ).data;
								curInst.topSearchEngineReferralsData = $.parseJSON( topSearchEngineReferralsData[0] ).data;
								curInst.topLandingPagesData = $.parseJSON( topLandingPagesData[0] ).data;
								curInst.topVisitorLocationsData = $.parseJSON( topVisitorLocationsData[0] ).data;
								// Display data
								curInst.displayDataTabData();
							}
						});
				}
			}
		});
	});
};
GoogleAnalytics.prototype.getDetailViewDataDeferredObject = function() {
	return $.get( ajaxurl, {
		action: 'skystats_ajax_google_analytics_api_query',
		query: 'get_detail_view_data',
		start_date: this.startDate,
		end_date: this.endDate,
		frequency: this.frequency
	});
};
GoogleAnalytics.prototype.getTopKeywordsDeferredObject = function() {
	return $.get( ajaxurl, {
		action: 'skystats_ajax_get_google_analytics_top_data',
		data_type: 'keywords',
		start_date: this.startDate,
		end_date: this.endDate
	});
};
GoogleAnalytics.prototype.getTopSearchEngineReferralsDeferredObject = function() {
	return $.get( ajaxurl, {
		action: 'skystats_ajax_get_google_analytics_top_data',
		data_type: 'search_engine_referrals',
		start_date: this.startDate,
		end_date: this.endDate
	});
};
GoogleAnalytics.prototype.getTopLandingPagesDeferredObject = function() {
	return $.get( ajaxurl, {
		action: 'skystats_ajax_get_google_analytics_top_data',
		data_type: 'landing_pages',
		start_date: this.startDate,
		end_date: this.endDate
	});
};
GoogleAnalytics.prototype.getTopVisitorLocationsDeferredObject = function() {
	return $.get( ajaxurl, {
		action: 'skystats_ajax_get_google_analytics_top_data',
		data_type: 'visitor_locations',
		start_date: this.startDate,
		end_date: this.endDate
	});
};

/**
 * Display the data tab data.
 */
GoogleAnalytics.prototype.displayDataTabData = function() {
	var curInst = this;

	this.$loadingContainer.fadeOut( 400, function() {
		curInst.showChartFigure();
		curInst.$chartContainer.show();
		curInst.$dataPointsContainer.show();

		// Do we need to destroy the chart?
		if ( null !== curInst.chartInstance ) {
			curInst.chartInstance.shutdown();
		}
		// Create chart
		curInst.chartData[ 0 ].data = getChartData( curInst.data['chart_data']['users'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartData[ 1 ].data = getChartData( curInst.data['chart_data']['page_views'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartInstance = curInst.$chart.plot( curInst.chartData, curInst.chartOptions ).data( 'plot' );

		// Users
		$('#skystats-google-analytics-users-total').html( curInst.data['users'] );
		$('#skystats-google-analytics-users-change').html( curInst.data['users_change'] );
		var $usersChangeInfo = $( '#skystats-google-analytics-users-change-info' );
		curInst.changeTooltipContent( $usersChangeInfo, curInst.data['previous_users'] + ' ' + $usersChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass(
			'skystats-google-analytics-users-change-direction',
			curInst.data['users_change_direction']
		);
		setDataPointChangeClass(
			'skystats-google-analytics-users-change',
			curInst.data['users_change_direction']
		);

		// Page Views
		$('#skystats-google-analytics-page-views-total').html( curInst.data['page_views'] );
		$('#skystats-google-analytics-page-views-change').html( curInst.data['page_views_change'] );
		var $pageViewsChangeInfo = $( '#skystats-google-analytics-page-views-change-info' );
		curInst.changeTooltipContent( $pageViewsChangeInfo, curInst.data['previous_page_views'] + ' ' + $pageViewsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass(
			'skystats-google-analytics-page-views-change-direction',
			curInst.data['page_views_change_direction']
		);
		setDataPointChangeClass(
			'skystats-google-analytics-page-views-change',
			curInst.data['page_views_change_direction']
		);

		// Donut for bounce rate is currently only used on the mashboard
		if ( 'mashboard' === curInst.viewName ) {


			var $bounceRateDonut = $( '#skystats-google-analytics-bounce-rate' );

			$bounceRateDonut.html( '' );

			// If the bounce rate data point was hidden on the data tab and then reopened,
			// we need it to be visible for our calculations, then we'll hide it and only display it if this wasn't
			// the case.
			var $bounceRateDataPoint = $( '#skystats-google-analytics-bounce-rate-data-point' ),
				wasBounceRateHidden = false;

			if ( ! $bounceRateDataPoint.is( ':visible' ) ) {
				wasBounceRateHidden = true;
				$bounceRateDataPoint.show();
			}

			/*
			 Bounce Rate Donut
			 */

			var donut = Morris.Donut( {
				data: [
					{ label: curInst.data['bounce_rate'] + '%', value: curInst.data['bounce_rate'] },
					{ label: curInst.data['bounce_rate_change'] + '%', value: 100 - curInst.data['bounce_rate'] }
				],
				element: 'skystats-google-analytics-bounce-rate',
				colors: ['#72aa3f', '#ffffff'],
				resize: false,
				formatter: function () {
					if ( 'negative' === curInst.data['bounce_rate_change_direction'] ) {
						return '▽' + curInst.data['bounce_rate_change'];
					} else if ( 'positive' === curInst.data['bounce_rate_change_direction'] ) {
						return 'Δ+' + curInst.data['bounce_rate_change'];
					} else {
						return curInst.data['bounce_rate_change'];
					}
				}
			} );
			donut.select( 0 );

			var $donutSvg = $bounceRateDonut.find( 'svg' );
			var $donutSvgChildren = $donutSvg.children();

			// Remove outer/inner colored path stroke, inner colored path stroke, and outer/inner white path stroke
			$donutSvgChildren.eq( 2 ).css( 'stroke', 'none' ).end()
				.eq( 3 ).css( 'stroke', 'none' ).end()
				.eq( 4 ).css( 'stroke', 'none' ).end()
				.eq( 5 ).css( 'stroke', 'none' ).css( 'fill', '#eeeeee' );
			var $bounceRate = $donutSvgChildren.eq( 6 );
			$bounceRate.html( '<tspan>' + curInst.data['bounce_rate'] + '</tspan><tspan dy="-7" dx="0" style="font-size:16px;">%</tspan>' );
			$bounceRate.attr( 'class', 'skystats-donut-value' );
			$bounceRate.attr( 'font-size', '25px' );
			$bounceRate.attr( 'y', '70' );
			$donutSvg.css( 'left', '-10%' );

			var $bounceRateChange = $donutSvgChildren.eq( 7 ).find( 'tspan' );

			switch ( curInst.data['bounce_rate_change_direction'] ) {
				case 'positive':
					$bounceRateChange.attr( 'class', 'skystats-data-point-change skystats-data-point-change-negative' );
					break;
				case 'negative':
					$bounceRateChange.attr( 'class', 'skystats-data-point-change skystats-data-point-change-positive' );
					break;
				default:
					$bounceRateChange.attr( 'class', 'skystats-data-point-change' );
					break;
			}

			setDataPointChangeDirectionClass(
				'skystats-google-analytics-bounce-rate-change-direction',
				curInst.data['bounce_rate_change_direction']
			);

			$bounceRateChange
				.attr( 'fill', $bounceRateChange.css( 'color' ) )
				.css( 'font-size', '19px' )
				.attr( 'dy', '6' );

			var $bounceRateChangePercentSymbol = $( '<tspan dy="-4" dx="2" fill="' + $bounceRateChange.css( 'color' ) + '" style="font-size: 12px;">%</tspan>' );
			$bounceRateChangePercentSymbol.appendTo( $bounceRateChange );

			$bounceRateDonut.html( $bounceRateDonut.html() );

			$bounceRateChange = null;

			if ( wasBounceRateHidden && $bounceRateDataPoint.is( ':visible' ) ) {
				$bounceRateDataPoint.hide();
			}
		} // endif view is mashboard

		// These data points are unique to the detail page
		if ( 'detail' === curInst.viewName ) {

			// Pages Per Visit
			$( '#skystats-google-analytics-pages-per-visit-total' ).html( curInst.data['pages_per_visit'] );
			$( '#skystats-google-analytics-pages-per-visit-change' ).html( curInst.data['pages_per_visit_change'] );
			var $pagesPerVisitChangeInfo = $( '#skystats-google-analytics-pages-per-visit-change-info' );
			curInst.changeTooltipContent( $pagesPerVisitChangeInfo, curInst.data['previous_pages_per_visit'] + ' ' + $pagesPerVisitChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-google-analytics-pages-per-visit-change-direction', curInst.data['pages_per_visit_change_direction'] );
			setDataPointChangeClass( 'skystats-google-analytics-pages-per-visit-change', curInst.data['pages_per_visit_change_direction'] );

			// Average Visit Duration (min:sec)
			$( '#skystats-google-analytics-average-visit-duration-total' ).html( curInst.data['average_visit_duration'] );
			$( '#skystats-google-analytics-average-visit-duration-change' ).html( curInst.data['average_visit_duration_change'] );
			var $averageVisitDurationChangeInfo = $( '#skystats-google-analytics-average-visit-duration-change-info' );
			curInst.changeTooltipContent( $averageVisitDurationChangeInfo, curInst.data['previous_average_visit_duration'] + ' ' + $averageVisitDurationChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-google-analytics-average-visit-duration-change-direction', curInst.data['average_visit_duration_change_direction'] );
			setDataPointChangeClass( 'skystats-google-analytics-average-visit-duration-change', curInst.data['average_visit_duration_change_direction'] );

			// Bounce Rate
			var $bounceRateTotal = $( '#skystats-google-analytics-bounce-rate-total' );
			$bounceRateTotal.html( curInst.data['bounce_rate'] );
			$bounceRateChange = $( '#skystats-google-analytics-bounce-rate-change' );
			$bounceRateChange.html( curInst.data['bounce_rate_change'] );
			var $bounceRateChangeInfo = $( '#skystats-google-analytics-bounce-rate-change-info' );
			curInst.changeTooltipContent( $bounceRateChangeInfo, curInst.data['previous_bounce_rate'] + ' ' + $bounceRateChangeInfo.attr( 'data-tooltip-backup' ) );
			var direction = 'neutral';
			if ( 'neutral' === curInst.data['bounce_rate_change_direction'] ) {
				setDataPointChangeDirectionClass('skystats-google-analytics-bounce-rate-change-direction', 'neutral' );
				setDataPointChangeClass('skystats-google-analytics-bounce-rate-change', 'neutral' );
			} else {
				var $bounceRateChangeDirection = $( '#skystats-google-analytics-bounce-rate-change-direction' );

				if ( 'positive' === curInst.data['bounce_rate_change_direction'] ) {

					$bounceRateChange.attr( 'class','skystats-data-point-change skystats-data-point-change-increase-negative' );
					$bounceRateChangeDirection.attr( 'class', 'skystats-data-point-change-direction-increase-negative' );

				} else if ( 'negative' === curInst.data['bounce_rate_change_direction'] ) {

					$bounceRateChange.attr( 'class', 'skystats-data-point-change skystats-data-point-change-decrease-positive' );
					$bounceRateChangeDirection.attr( 'class', 'skystats-data-point-change-direction-decrease-positive' );
				}
			}

			// Top Keywords Data
			if ( null != curInst.topKeywordsData ) {
				var $topKeywordsDataTable = $( '#skystats-google-analytics-top-keywords-data-table' ),
					$topKeywordsDataTableTbody = $topKeywordsDataTable.find( '> tbody' );
				$topKeywordsDataTableTbody.html( '' );
				var topKeywordsRows = '';
				$.each( curInst.topKeywordsData, function ( unused, data ) {
					topKeywordsRows += '<tr><td>' + data['keyword'] + '</td><td>' + data['visits'] + '</td><td>' + data['percent'] + '</td></tr>';
				});
				$( topKeywordsRows ).appendTo( $topKeywordsDataTableTbody );
				$( '#skystats-detail-google-analytics-top-keywords-data-table-column-content' ).fadeIn();
			}

			// Top Search Engine Referrals
			if ( null != curInst.topSearchEngineReferralsData ) {
				var $topSearchEngineReferralsTable = $( '#skystats-google-analytics-top-search-engine-referrals-data-table' ),
					$topSearchEngineReferralsTableTbody = $topSearchEngineReferralsTable.find( '> tbody' );
				$topSearchEngineReferralsTableTbody.html( '' );
				var topSearchEngineReferralsRows = '';
				$.each( curInst.topSearchEngineReferralsData, function ( unused, data ) {
					topSearchEngineReferralsRows += '<tr><td>' + data['search_engine'] + '</td><td>' + data['visits'] + '</td><td>' + data['percent'] + '</td></tr>';
				});
				$( topSearchEngineReferralsRows ).appendTo( $topSearchEngineReferralsTableTbody );
				$( '#skystats-detail-google-analytics-top-search-engine-referrals-data-table-column-content' ).fadeIn();
			}

			// Top Landing Pages
			if ( null != curInst.topLandingPagesData ) {
				var $topLandingPagesDataTable = $( '#skystats-google-analytics-top-landing-pages-data-table' ),
					$topLandingPagesDataTableTbody = $topLandingPagesDataTable.find( '> tbody' );
				$topLandingPagesDataTableTbody.html( '' );
				var topLandingPagesRows = '';
				$.each( curInst.topLandingPagesData, function ( unused, data ) {
					topLandingPagesRows += '<tr><td>' + data['page'] + '</td><td>' + data['visits'] + '</td><td>' + data['percent'] + '</td></tr>';
				});
				$( topLandingPagesRows ).appendTo( $topLandingPagesDataTableTbody );
				$( '#skystats-detail-google-analytics-top-landing-pages-data-table-column-content' ).fadeIn();
			}

			// Top Visitor Locations
			if ( null != curInst.topVisitorLocationsData ) {
				var $topVisitorLocationsDataTable = $( '#skystats-google-analytics-visitor-locations-data-table' ),
					$topVisitorLocationsDataTableTbody = $topVisitorLocationsDataTable.find( '> tbody' );
				$topVisitorLocationsDataTableTbody.html( '' );
				var topVisitorLocationsRows = '';
				$.each( curInst.topVisitorLocationsData, function ( unused, data ) {
					topVisitorLocationsRows += '<tr><td>' + data['country'] + '</td><td>' + data['visits'] + '</td><td>' + data['percent'] + '</td></tr>';
				});
				$( topVisitorLocationsRows ).appendTo( $topVisitorLocationsDataTableTbody );
				$('#skystats-detail-google-analytics-top-visitor-locations-data-table-column-content').fadeIn();
			}
		}

		/*
		 Search Engine Visits
		 */
		$( '#skystats-google-analytics-search-engine-visits-total' ).html( curInst.data['search_engine_visits'] );
		$( '#skystats-google-analytics-search-engine-visits-change' ).html( curInst.data['search_engine_visits_change'] );
		var $searchEngineVisitsChangeInfo = $( '#skystats-google-analytics-search-engine-visits-change-info' );
		curInst.changeTooltipContent( $searchEngineVisitsChangeInfo, curInst.data['previous_search_engine_visits'] + ' ' + $searchEngineVisitsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass(
			'skystats-google-analytics-search-engine-visits-change-direction',
			curInst.data['search_engine_visits_change_direction']
		);
		setDataPointChangeClass(
			'skystats-google-analytics-search-engine-visits-change',
			curInst.data['search_engine_visits_change_direction']
		);

		curInst.enableGridIcon();
		curInst.enableSettingsIcon();

		curInst.loadingDataTabData = false;
		curInst.viewingDataTab = true;
	});
};

/**
 * Enable the grid icons tooltip and data point show/hide toggle ability.
 */
GoogleAnalytics.prototype.enableGridIcon = function() {
	Integration.prototype.enableGridIcon.call( this );
};

/**
 * Disable the grid icons tooltip and data point show/hide toggle ability.
 */
GoogleAnalytics.prototype.disableGridIcon = function() {
	Integration.prototype.disableGridIcon.call( this );
};

/**
 * Enable the settings icon tooltip and tab switchability.
 */
GoogleAnalytics.prototype.enableSettingsIcon = function() {
	Integration.prototype.enableSettingsIcon.call( this );
};

/**
 * Disable the settings icon tooltip and tab switchability.
 */
GoogleAnalytics.prototype.disableSettingsIcon = function() {
	Integration.prototype.disableSettingsIcon.call( this );
};

/**
 * Load the data for the settings tab.
 *
 * @param {string} requestType 'fresh' to retrieve results directly from API, otherwise 'cached' to try to return cached results first, then fresh second.
 */
GoogleAnalytics.prototype.loadSettingsTabData = function( requestType ) {
	this.hideChartFigure();
	var curInst = this,
		$fadeOutObjects = ( 'mashboard' === this.viewName ) ?
			this.$dataPointsContainer.add( this.$chartContainer ) :
			this.$dataPointsContainer.add( this.$chartContainer ).add( this.$dataTableColumnsContent );
	$fadeOutObjects.fadeOut( 400 ).promise().done( function() {
		curInst.$loadingContainer.fadeIn( 400, function() {
			$.get( ajaxurl, {
				action: 'skystats_ajax_google_analytics_api_query',
				query : 'get_profiles',
				request_type: requestType
			}, function ( response ) {
				response = $.parseJSON( response );
				curInst.displaySettingsTabData( response );
			});
		});
	});
};

/**
 * Display the settings tab with supplied data.
 *
 * @param response JSON decoded profile data for the settings tab.
 */
GoogleAnalytics.prototype.displaySettingsTabData = function( response ) {

	var curInst = this;

	curInst.$loadingContainer.fadeOut( 400, function () {
		if ( 'success' === response['responseType'] ) {
			var selectHTML = '',
				$profiles = $( '#ga-profiles' );
			$.each( response.data, function ( i, profile ) {
				var selected = ( curInst.selectedProfileID === profile.id ) ? 'selected="selected"' : '';
				selectHTML += '<option value="' + profile.id + '" ' + selected + '>' + profile.name + '</option>';
			} );
			$profiles.html( selectHTML );

			curInst.$settingsTabProfilesSection.fadeIn();

			// When a profile is selected and 'Save' is clicked.
			var $saveGAProfileID = $( '#save_ga_profile' );
			$saveGAProfileID.off( 'click' );
			$saveGAProfileID.one( 'click', function( e ) {
				e.preventDefault();
				curInst.disableSettingsIcon();
				var profileID = $profiles.val();
				curInst.selectedProfileID = profileID;
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						$.post( ajaxurl, {
							action: 'skystats_ajax_google_analytics_save_profile_id',
							profile_id: profileID
						}, function () {
							// Reset data - important!
							curInst.data = null;
							if ( 'detail' === curInst.viewName ) {
								curInst.loadedAllData = false;
							}
							curInst.loadDataTabData();
						});
					});
				});
			});
			curInst.$settingsTabDeauthorizeSection.fadeIn();

		} else {
			switch ( response['responseContext'] ) {
				case 'authorization_required':
					curInst.$settingsTabSetupSection.fadeIn();
					break;
				case 'reauthorization_required':
					curInst.$settingsTabReauthorizeSection.fadeIn();
					break;
				case 'no_profiles':
					curInst.$settingsTabNoProfilesSection.fadeIn();
					curInst.$settingsTabDeauthorizeSection.fadeIn();
					break;
				default: // deauthorize
					curInst.$settingsTabDeauthorizeSection.fadeIn();
					break;
			}
		}

		curInst.viewingSettingsTab = true;
		curInst.loadingSettingsTabData = false;

		// Enable settings icon if there is data
		if ( null != curInst.data ) {
			curInst.enableSettingsIcon();
		}

		if ( curInst.$settingsTabSetupSection.is( ':visible' ) || curInst.$settingsTabReauthorizeSection.is( ':visible' ) ) {
			var $authorizeReauthorize = $( '#skystats-google-analytics-authorize, #skystats-google-analytics-reauthorize' );
			$authorizeReauthorize.off( 'click' )
				.on( 'click', function() {
					curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
						curInst.$loadingContainer.fadeIn( 400, function () {
							$.post( ajaxurl, {
								action: 'skystats_ajax_google_analytics_authorize'
							});
						} );
					});
				});
		}

		if ( curInst.$settingsTabDeauthorizeSection.is( ':visible' ) ) {
			// When 'Deauthorize' is clicked.
			var $deauthorize = $( '#skystats-google-analytics-deauthorize' );
			$deauthorize.off( 'click' );
			$deauthorize.one( 'click', function() {
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						$.post( ajaxurl, {
							action: 'skystats_ajax_google_analytics_deauthorize'
						});
					});
				});
			});
		}
	});
};

/**
 * Update the data tab, or if on the settings tab (or loading it), do nothing.
 */
GoogleAnalytics.prototype.updateDataTabData = function( checkedDates ) {
	Integration.prototype.updateDataTabData.call( this, checkedDates );
};

// Facebook - child
function Facebook( viewName ) {

	Integration.call( this, viewName );

	this.integrationName = 'Facebook';

	this.$loadingContainer = $( '#skystats-facebook-loading-container' );

	this.translations = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['trans']:
		skystats_facebook['trans'];

	/* Data */
	this.selectedPageID = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['facebook']['selected_page_id'] :
		skystats_facebook['selected_page_id'];
	this.data = null; // Contains data for the chart and data points.
	this.loadingDataTabData = false;
	this.viewingDataTab = false;
	this.loadedAllData = false; // Detail page specific. Whether all data was successfully loaded or at least attempted.
	this.topPostsData = null; // Detail specific
	this.$dataTableColumnsContent = $( '.skystats-data-table-column-content'  );

	/* Data Points */
	this.$dataTabContent = $( '#skystats-facebook-data-tab-content' );
	this.$dataPointsContainer = $( '#skystats-facebook-data-points-container' );

	/* Chart */
	this.chartInstance = null;
	this.chartID = 'skystats-facebook-chart';
	this.$chart = $( '#' + this.chartID );
	this.$chartContainer = $( '#skystats-facebook-chart-container' );
	this.chartData = [
		{
			color: '#66ccff',
			data: [],
			label: '' /* Total Likes */
		},
		{
			color: '#35d1ae',
			data: [],
			label: '' /* Total Reach */
		}
	];

	/* Grid */
	this.gridIconSelector = '#skystats-facebook-grid-icon';
	this.$gridIcon = $( this.gridIconSelector );
	this.gridIconContentSelector = '#skystats-facebook-grid-icon-content';
	this.gridIconChildrenContent = $( '#skystats-facebook-grid-icon-content' ).html();
	this.gridDisplayed = false;
	this.gridClickEventName = 'click.fb_grid';
	this.gridMouseoverEventName = 'mouseover.fb_grid';
	this.gridMouseleaveEventName = 'mouseleave.fb_grid';
	this.gridMouseoutEventName = 'mouseout.fb_grid';

	/* Settings */
	this.loadingSettingsTabData = false;
	this.viewingSettingsTab = false;
	this.$settingsIcon = $( '#skystats-facebook-settings-icon');
	this.$settingsTabSections = $( '.skystats-facebook-settings-tab-section' );
	this.$settingsTabNoPagesSection = $( '#skystats-facebook-settings-no-pages-section' );
	this.$settingsTabPageSelectionSection = $( '#skystats-facebook-settings-page-selection-section' );
	this.$settingsTabReauthorizeSection = $( '#skystats-facebook-settings-reauthorize-section' );
	this.$settingsTabDeauthorizeSection = $( '#skystats-facebook-settings-deauthorize-section' );
	this.$settingsTabAuthorizeSection = $( '#skystats-facebook-settings-authorize-section' );
	this.settingsClickEventName = 'click.fb_settings';

}
Facebook.prototype = createObject( Integration.prototype );
Facebook.prototype.constructor = Facebook;

/**
 * Load data for the data tab.
 */
Facebook.prototype.loadDataTabData = function() {
	this.hideChartFigure();
	this.viewingSettingsTab = false;
	this.loadingDataTabData = true;
	var curInst = this;
	this.$settingsTabSections
		.add( this.$chartContainer )
		.add( this.$dataPointsContainer )
		.add( this.$dataTableColumnsContent )
		.fadeOut( 400 ).promise().done( function() {
		curInst.$loadingContainer.fadeIn( 400, function() {
			if ( 'mashboard' === curInst.viewName ) {
				// If data exists and the settings icon was clicked, just display the data.
				if ( null != curInst.data ) {
					curInst.displayDataTabData();
					return;
				}
				$.get( ajaxurl, {
					action: 'skystats_ajax_facebook_get_mashboard_view_data',
					start_date: curInst.startDate,
					end_date: curInst.endDate
				}, function ( response ) {
					response = $.parseJSON( response );
					if ( null != response.data ) {
						curInst.data = response.data;
						curInst.displayDataTabData();
					} else {
						curInst.loadSettingsTabData( 'fresh' );
					}
				} );
			} else if ( 'detail' === curInst.viewName ) {
				if ( true === curInst.loadedAllData ) {
					curInst.displayDataTabData();
				} else {
					var detailViewDataDeferredObject = curInst.getDetailViewDataDeferredObject(),
						topPostsDataDeferredObject = curInst.getTopPostsDeferredObject();
					$.when( detailViewDataDeferredObject, topPostsDataDeferredObject).done( function ( detailViewData, topPostsData ) {
						var detailViewDataObj = $.parseJSON( detailViewData[0] );
						if ( 'error' === detailViewDataObj['responseType'] ) {
							curInst.loadSettingsTabData( 'fresh' );
						} else {
							curInst.loadedAllData = true;
							curInst.data = $.parseJSON( detailViewData[0] ).data;
							curInst.topPostsData = $.parseJSON( topPostsData[0] ).data;
							curInst.displayDataTabData();
						}
					});
				}
			}
		});
	});
};

Facebook.prototype.getDetailViewDataDeferredObject = function() {
	return $.get( ajaxurl, {
		action: 'skystats_ajax_facebook_get_detail_view_data',
		start_date: this.startDate,
		end_date: this.endDate
	});
};
Facebook.prototype.getTopPostsDeferredObject = function() {
	return $.get( ajaxurl, {
		action: 'skystats_ajax_facebook_get_page_top_posts',
		start_date: this.startDate,
		end_date: this.endDate
	});
};

/**
 * Display the data for the data tab.
 */
Facebook.prototype.displayDataTabData = function() {
	var curInst = this;
	this.$loadingContainer.fadeOut( 400, function() {
		curInst.showChartFigure();
		curInst.$dataTabContent.fadeIn();
		curInst.$chartContainer.show();

		// Do we need to destroy the chart?
		if ( null !== curInst.chartInstance ) {
			curInst.chartInstance.shutdown();
		}
		// Create chart
		curInst.chartData[ 0 ].data = getChartData( curInst.data['chart_data']['likes'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartData[ 1 ].data = getChartData( curInst.data['chart_data']['reach'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartInstance = curInst.$chart.plot( curInst.chartData, curInst.chartOptions ).data( 'plot' );

		curInst.$dataPointsContainer.show();

		// Total Likes
		$( '#skystats-facebook-total-likes-total' ).html( curInst.data['total_likes'] );
		$( '#skystats-facebook-total-likes-change' ).html( curInst.data['total_likes_change'] );
		var $totalLikesChangeInfo = $( '#skystats-facebook-total-likes-change-info' );
		curInst.changeTooltipContent( $totalLikesChangeInfo, curInst.data['previous_total_likes']  + ' ' + $totalLikesChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-facebook-total-likes-change-direction', curInst.data['total_likes_change_direction'] );
		setDataPointChangeClass( 'skystats-facebook-total-likes-change', curInst.data['total_likes_change_direction'] );

		// Total Reach
		$( '#skystats-facebook-total-reach-total' ).html( curInst.data['total_reach'] );
		$( '#skystats-facebook-total-reach-change' ).html( curInst.data['total_reach_change'] );
		var $totalReachChangeInfo = $( '#skystats-facebook-total-reach-change-info' );
		curInst.changeTooltipContent( $totalReachChangeInfo, curInst.data['previous_total_reach'] + ' ' + $totalReachChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-facebook-total-reach-change-direction', curInst.data['total_reach_change_direction'] );
		setDataPointChangeClass( 'skystats-facebook-total-reach-change', curInst.data['total_reach_change_direction'] );

		// Page Views
		$( '#skystats-facebook-page-visits-total' ).html( curInst.data['page_views'] );
		$( '#skystats-facebook-page-visits-change' ).html( curInst.data['page_views_change'] );
		var $pageViewsChangeInfo = $( '#skystats-facebook-page-visits-change-info' );
		curInst.changeTooltipContent( $pageViewsChangeInfo, curInst.data['previous_page_views'] + ' ' + $pageViewsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass('skystats-facebook-page-visits-change-direction', curInst.data['page_views_change_direction'] );
		setDataPointChangeClass('skystats-facebook-page-visits-change', curInst.data['page_views_change_direction'] );

		// People Engaged
		$( '#skystats-facebook-people-engaged-total' ).html( curInst.data['people_engaged'] );
		$( '#skystats-facebook-people-engaged-change' ).html( curInst.data['people_engaged_change'] );
		var $peopleEngagedChangeInfo = $( '#skystats-facebook-people-engaged-change-info' );
		curInst.changeTooltipContent( $peopleEngagedChangeInfo, curInst.data['previous_people_engaged'] + ' ' + $peopleEngagedChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-facebook-people-engaged-change-direction', curInst.data['people_engaged_change_direction'] );
		setDataPointChangeClass( 'skystats-facebook-people-engaged-change', curInst.data['people_engaged_change_direction'] );

		if ( 'detail' === curInst.viewName ) {
			if ( null != curInst.topPostsData ) {
				var $topPostsDataTableTbody = $( '#skystats-facebook-top-posts-data-table-tbody' ),
					rows = '';
				$topPostsDataTableTbody.html( '' );
				$.each( curInst.topPostsData, function ( unused, data ) {
					rows += '<tr>' +
					'<td>' + data['name'] + '</td>' +
					'<td>' + data['likes'] + '</td>' +
					'<td>' + data['reach'] + '</td>' +
					'<td>' + data['comments'] + '</td>' +
					'<td>' + data['date'] + '</td>' +
					'</tr>';
				} );
				$( rows ).appendTo( $topPostsDataTableTbody );
				$( '#skystats-facebook-top-posts-data-table-column-content' ).fadeIn();
			}
		}

		curInst.enableGridIcon();
		curInst.enableSettingsIcon();

		curInst.loadingDataTabData = false;
		curInst.viewingDataTab = true;
	});
};

/**
 * Load settings tab data.
 *
 * @param {string} requestType Whether to request fresh or cached results. Important when there is an error, and we must request fresh results.
 */
Facebook.prototype.loadSettingsTabData = function( requestType ) {
	this.hideChartFigure();
	var curInst = this,
	    $fadeOutObjects = ( 'mashboard' === this.viewName ) ?
		    this.$dataPointsContainer.add( this.$chartContainer ) :
		    this.$dataPointsContainer.add( this.$chartContainer ).add( this.$dataTableColumnsContent );
	$fadeOutObjects.fadeOut( 400 ).promise().done( function () {
		curInst.$loadingContainer.fadeIn( 400, function () {
			$.get( ajaxurl, {
				action: 'skystats_ajax_facebook_get_page_list',
				request_type: requestType
			}, function ( response ) {
				response = $.parseJSON( response );
				curInst.displaySettingsTabData( response );
			});
		});
	});
};

/**
 * Display settings tab data.
 *
 * @param {Object} response Settings tab response data.
 */
Facebook.prototype.displaySettingsTabData = function( response ) {
	var curInst = this;

	curInst.$loadingContainer.fadeOut( 400, function () {

		if ( 'success' === response['responseType'] ) {
			// Populate page selection
			var selectHTML = '',
				$pages = $( '#skystats-facebook-page-list' );
			$.each( response.data, function ( i, page ) {
				var selected = ( curInst.selectedPageID === page.id ) ? 'selected="selected"' : '';
				selectHTML += '<option value="' + page.id + '" ' + selected + '>' + page.name + '</option>';
			});
			$pages.html( selectHTML );
			curInst.$settingsTabPageSelectionSection.fadeIn();
			// When a profile is selected and 'Save' is clicked.
			var $saveFacebookPageID = $( '#skystats-facebook-save-page-id' );
			$saveFacebookPageID.off( 'click' );
			$saveFacebookPageID.one( 'click', function( e ) {
				e.preventDefault();
				curInst.disableSettingsIcon();
				var pageID = $pages.val();
				curInst.selectedPageID = pageID;
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						$.post( ajaxurl, {
							action: 'skystats_ajax_facebook_save_page_id',
							page_id: pageID
						}, function () {
							// Reset data - important!
							curInst.data = null;
							if ( 'detail' === curInst.viewName ) {
								curInst.loadedAllData = false;
							}
							curInst.loadDataTabData();
						});
					});
				});
			});
			curInst.$settingsTabDeauthorizeSection.fadeIn();
		} else {
			switch ( response['responseContext'] ) {
				case 'authorization_required':
					curInst.$settingsTabAuthorizeSection.fadeIn();
					break;
				case 'reauthorization_required':
					curInst.$settingsTabReauthorizeSection.fadeIn();
					break;
				case 'no_pages':
					curInst.$settingsTabNoPagesSection.fadeIn();
					curInst.$settingsTabDeauthorizeSection.fadeIn();
					break;
				default:
					curInst.$settingsTabDeauthorizeSection.fadeIn();
					break;
			}
		}

		curInst.viewingSettingsTab = true;
		curInst.loadingSettingsTabData = false;

		// Enable settings icon if there is data
		if ( null != curInst.data ) {
			curInst.enableSettingsIcon();
		}

		if ( curInst.$settingsTabAuthorizeSection.is( ':visible' ) || curInst.$settingsTabReauthorizeSection.is( ':visible' ) ) {
			var $authorizeReauthorize = $( '#skystats-facebook-authorize, #skystats-facebook-reauthorize' );
			$authorizeReauthorize.off( 'click' );
			$authorizeReauthorize.on( 'click', function() {
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function () {
					curInst.$loadingContainer.fadeIn( 400, function() {
						$.post( ajaxurl, {
							action: 'skystats_ajax_facebook_authorize'
						});
					});
				});
			});
		}

		if ( curInst.$settingsTabDeauthorizeSection.is( ':visible' ) ) {
			var $deauthorize = $( '#skystats-facebook-deauthorize' );
			$deauthorize.off( 'click' );
			$deauthorize.on( 'click', function() {
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						$.post( ajaxurl, {
							action: 'skystats_ajax_facebook_deauthorize'
						} );
					} );
				});
			});
		}
	});
};

/**
 * Enable the grid tooltip and data point show/hide toggle ability.
 */
Facebook.prototype.enableGridIcon = function() {
	Integration.prototype.enableGridIcon.call( this );
};

/**
 * Disable the grid tooltip and data point show/hide toggle ability.
 */
Facebook.prototype.disableGridIcon = function() {
	Integration.prototype.disableGridIcon.call( this );
};

/**
 * Enable the settings icon tooltip and tab switchability.
 */
Facebook.prototype.enableSettingsIcon = function() {
	Integration.prototype.enableSettingsIcon.call( this );
};

/**
 * Disable the settings icon tooltip and tab switchability.
 */
Facebook.prototype.disableSettingsIcon = function() {
	Integration.prototype.disableSettingsIcon.call( this );
};

Facebook.prototype.updateDataTabData = function( checkedDates ) {
	Integration.prototype.updateDataTabData.call( this, checkedDates );
};
Facebook.prototype.resizeChart = function() {
	if ( null != this.data ) {
		setGlobalPossibleMarkers( null );
		this.chartInstance.shutdown();
		this.chartData[ 0 ].data = getChartData( this.data['chart_data']['likes'], this.$chart, this, 'daily' );
		this.chartData[ 1 ].data = getChartData( this.data['chart_data']['reach'], this.$chart, this, 'daily' );
		this.chartInstance = this.$chart.plot( this.chartData, this.chartOptions ).data( 'plot' );
	}
};

/**
 * Return a specific integration object.
 *
 * @since 0.1.4
 *
 * @param {string} integrationName The name of the integration to return.
 *
 * @param {string} viewName        The type of view this integration will be used for (mashboard or detail currently supported).
 *
 * @returns {null|Object} The integration object or null if an invalid integration name was supplied.
 */
function getIntegration( integrationName, viewName ) {
	switch ( integrationName ) {
		case 'googleAnalytics':
			return new GoogleAnalytics( viewName );
			break;
		case 'facebook':
			return new Facebook( viewName );
			break;
		default:
			break;
	}
}

/**
 * Return all integration objects.
 *
 * @since 0.1.4
 *
 * @param {string} viewName The type of view this integration will be used for (mashboard or detail currently supported).
 *
 * @returns {Object[]}
 */
function getIntegrations( viewName ) {
	var google = new GoogleAnalytics( viewName ),
		fb = new Facebook( viewName );

	return [ google, fb ];
}

function initTooltips() {
	$( '.skystats-tooltip' ).each( function() {
		var $tooltip = $( this );
		if ( $tooltip .hasClass( 'skystats-disabled' ) ) {
			return true;
		}
		$tooltip .tooltip({
			content: $tooltip.attr( 'data-tooltip' ),
			items  : '[data-tooltip]'
		});
	});
}

/**
 * Allows each integration's card to be sorted, and automatically saves the positions after each one has been dropped.
 *
 * @param {Object[]} integrations
 *
 * @param {int} integrationsLen
 */
function makeCardsSortable( integrations, integrationsLen ) {

	var sortableElementSelector = '.skystats-cards-col',
		$sortableElement = $( sortableElementSelector );

	$sortableElement.sortable({
		connectWith: sortableElementSelector,
		cursor: 'move',
		forcePlaceholderSize: true,
		handle: '.skystats-card-drag-icon',
		placeholder: 'skystats-card-placeholder',
		revert: 400,
		/* Triggered when sorting has stopped */
		stop: function () {
			var data = {};
			$sortableElement.each( function  (i, obj ) {
				var id = $( obj ).attr( 'id' );
				data[ id ] = $( obj ).sortable( 'toArray' );
			});
			for ( var i = 0; i < integrationsLen; ++i ) {
				integrations[ i ].resizeChart();
			}
			$.post( ajaxurl, {
				'action': 'skystats_ajax_save_mashboard_card_positions',
				'data'  : data
			});
		}
	});
	$sortableElement.disableSelection();
}