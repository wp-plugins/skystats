/*http://www.jslint.com/*/
/*jslint white: true */
/*global ajaxurl, skystats_integrations, skystats_mashboard, skystats_google_analytics, skystats_facebook, skystats_twitter, skystats_google_adwords, console, window*/
function createObject( proto ) {
	function Ctor() { }
	Ctor.prototype = proto;
	return new Ctor();
}

// Integration - parent
function Integration( viewName ) {
	this.viewName = viewName;
	this.startDate = jQuery( '#start_date' ).val();
	this.endDate = jQuery( '#end_date' ).val();
	this.defaultTooltipContentFormatter = function ( label, x, y ) {
		return '<strong>' + y.toString() + '</strong><br>' + x;
	};
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
				radius: 1.75, // Use smaller markers for Mashboard
				show: true
			}
		},
		shadowSize: 0,
		tooltip: true,
		tooltipOpts: {
			content: this.defaultTooltipContentFormatter
		},
		xaxis: {
			mode: 'categories',
			tickLength: 0 // Don't show vertical lines for Mashboard charts
		},
		yaxes: [
			{
				tickDecimals: 0,
				min: 0
			},
			{
				min: 0,
				alignTicksWithAxis: 1,
				position: 'right'
			}
		]
	};
	if ( 'detail' === this.viewName ) {
		// Use large markers
		this.chartOptions.series.points.radius = 3.5;
		// Show vertical lines
		this.chartOptions.xaxis.tickLength = null;
		this.$chartFigure = jQuery( '#skystats-detail-chart-key-container' );
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

	var $pageErrorContainer = jQuery( '#skystats-page-error-container' );

	if ( ! checkedDates ) {
		$pageErrorContainer.fadeOut();
	}

	if ( 'Facebook' === this.integrationName ) {
		var $facebookErrorContainer = jQuery( '#skystats-facebook-error-container' );
		$facebookErrorContainer.fadeOut();
	} else if ( 'Google Analytics' === this.integrationName ) {
		var $googleAnalyticsErrorContainer = jQuery( '#skystats-google-analytics-error-container' );
		$googleAnalyticsErrorContainer .fadeOut();
	}

	// Ensure we update the dates used, for all integrations, so the correct date is used when the integration returns
	// to the data tab.
	this.startDate = jQuery( '#start_date' ).val();
	this.endDate = jQuery( '#end_date' ).val();

	if ( 'Google Analytics' === this.integrationName && 'detail' === this.viewName ) {
		this.frequency = jQuery( '#frequency' ).val();
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

	if ( currentRangeInDays <= 1 && this.startDate !== this.endDate ) {
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
	this.gridIconChildrenContent = jQuery( this.gridIconContentSelector ).html();
};

Integration.prototype.enableGridIcon = function() {
	if ( true === this.gridIconEnabled ) {
		return;
	}
	this.$gridIcon.removeClass( 'skystats-disabled' );
	this.$gridIcon.tooltip({
		content: this.$gridIcon.attr( 'data-tooltip' ),
		items  : '[data-tooltip]'
	});
	var curInst = this;
	this.gridIconEnabled = true;
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
			var $inputs = jQuery( curInst.gridIconContentSelector ).find( 'input' );
			$inputs.each( function ( i, v ) {
				var $this = jQuery( v );
				if ( jQuery( '#' + $this.val() ).is( ':visible' ) ) {
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
			jQuery( '.skystats-show-data-point' ).change( function () {
				var $this = jQuery( this );
				if ( this.checked ) {
					jQuery('#' + $this.val() ).show();
					$this.attr( 'checked', true );
				} else {
					jQuery( '#' + $this.val() ).hide();
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
	if ( false === this.gridIconEnabled ) {
		return;
	}
	this.$gridIcon.addClass( 'skystats-disabled' );
	this.$gridIcon.tooltip( 'destroy' );
	this.$gridIcon.off( this.gridClickEventName + ' ' + this.gridMouseoverEventName + ' ' + this.gridMouseleaveEventName + ' ' + this.gridMouseoutEventName );
	// Ensures grid does the right thing when clicked after being disabled whilst open.
	this.gridDisplayed = false;
	this.gridIconEnabled = false;
};

Integration.prototype.enableSettingsIcon = function() {
	if ( true === this.settingsIconEnabled ) {
		return;
	}
	var curInst = this;
	this.$settingsIcon.removeClass( 'skystats-disabled' );
	this.$settingsIcon.tooltip({
		content: this.$settingsIcon.attr( 'data-tooltip' ),
		items  : '[data-tooltip]'
	});
	this.settingsIconEnabled = true;
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
	if ( false === this.settingsIconEnabled ) {
		return;
	}
	if ( this.$settingsIcon.hasClass( 'skystats-disabled' ) ) {
		return;
	}
	this.$settingsIcon.addClass( 'skystats-disabled' );

	this.$settingsIcon.tooltip( 'destroy' );

	this.$settingsIcon.off( this.settingsClickEventName );

	this.settingsIconEnabled = false;
};

/**
 * Changes the content of any tooltip element or object.
 *
 * @param {jQuery|string} tooltip
 *
 * @param {string} content
 */
Integration.prototype.changeTooltipContent = function( tooltip, content ) {
	if ( tooltip instanceof jQuery ) {
		tooltip.tooltip('option', 'content', content );
	} else {
		jQuery( tooltip ).tooltip('option', 'content', content );
	}
};


/**
 * Create a popup window which allows the user to authorize with the integration.
 *
 * @param {String} url            URL to be loaded.
 * @param {String} name           Name for the window.
 * @param {Number} width          Width of window.
 * @param {Number} height         Height of window.
 * @param {String|Null} features (Optional) Features of window (size, position), can be used to override features.
 */
Integration.prototype.createPopupWindow = function( url, name, width, height, features ) {
	var left = ( screen.width / 2 ) - ( width / 2 ),
		top = ( screen.height / 2 ) - ( height / 2 );
	if ( null == features ) {
		features = 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left;
	}
	this.popupWindow = window.open( url, name, features );
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
	this.$loadingContainer = jQuery( '#skystats-google-analytics-loading-container' );
	this.$dataPointsContainer = jQuery( '#skystats-google-analytics-data-points-container' );
	this.$dataTableColumnsContent = jQuery( '.skystats-data-table-column-content' );
	this.loadingDataTabData = false;
	this.viewingDataTab = false;
	this.loadedAllData = false; // Detail specific (true if the chart data, data point and data table data was successfully retrieved, or was at least attempted (but one or more may have failed)

	this.topKeywordsData = null;
	this.topSearchEngineReferralsData = null;
	this.topLandingPagesData = null;
	this.topVisitorLocationsData = null;

	/* Chart */
	this.chartID = 'skystats-google-analytics-chart';
	this.$chart = jQuery( '#' + this.chartID );
	this.$chartContainer = jQuery( '#skystats-google-analytics-chart-container' ); // Chart parent
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
	this.$gridIcon = jQuery( this.gridIconSelector );
	this.gridIconContentSelector = '#skystats-google-analytics-grid-icon-content';
	this.gridIconChildrenContent = jQuery( '#skystats-google-analytics-grid-icon-content' ).html();
	this.gridDisplayed = false;
	this.gridClickEventName = 'click.ga_grid';
	this.gridMouseoverEventName = 'mouseover.ga_grid';
	this.gridMouseleaveEventName = 'mouseleave.ga_grid';
	this.gridMouseoutEventName = 'mouseout.ga_grid';
	this.gridIconEnabled = false;

	/* Settings */
	this.$settingsIcon = jQuery( '#skystats-google-analytics-settings-icon' );
	this.loadingSettingsTabData = false;
	this.viewingSettingsTab = false;
	this.$settingsTabSections = jQuery( '.skystats-google-analytics-settings-tab-section' );
	this.$settingsTabSetupSection = jQuery( '#skystats-google-analytics-settings-setup-section' );
	this.$settingsTabReauthorizeSection = jQuery( '#skystats-google-analytics-settings-reauthorize-section' );
	this.$settingsTabDeauthorizeSection = jQuery( '#skystats-google-analytics-settings-deauthorize-section' );
	this.$settingsTabNoProfilesSection = jQuery( '#skystats-google-analytics-settings-no-profiles-section' );
	this.$settingsTabProfilesSection = jQuery( '#skystats-google-analytics-settings-profiles-section' );
	this.settingsClickEventName = 'click.ga_settings';
	this.settingsIconEnabled = false;

	/* Detail Page Top Data Tables Errors */
	this.$topDataErrorContainers = jQuery( '.skystats-top-data-error-container' );
	this.$topKeywordsDataErrorContainer = jQuery( '#skystats-google-analytics-top-keywords-data-error-container' );
	this.$topSearchEngineReferralsDataErrorContainer = jQuery( '#skystats-google-analytics-top-search-engine-referrals-data-error-container' );
	this.$topLandingPagesDataErrorContainer = jQuery( '#skystats-google-analytics-top-landing-pages-data-error-container' );
	this.$topVisitorLocationsDataErrorContainer = jQuery( '#skystats-google-analytics-top-visitor-locations-data-error-container' );

	this.authPopupWindowIntervalId = null;
	// URL popup window first directs to authorize the user.
	this.authPopupWindowURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['google_analytics']['auth_popup_window_url'] :
		skystats_google_analytics['auth_popup_window_url'];
	// URL popup window directs to when authorization is complete.
	this.authPopupWindowCompleteURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['auth_popup_window_complete_url'] :
		skystats_google_analytics['auth_popup_window_complete_url'];
}
GoogleAnalytics.prototype = createObject( Integration.prototype );
GoogleAnalytics.prototype.constructor = GoogleAnalytics;

/**
 * Called whenever the browser window resizes, to try to resize the chart and any other tasks that need to be performed.
 */
GoogleAnalytics.prototype.resizeChart = function() {
	if ( null != this.data ) {
		setGlobalPossibleMarkers(null);
		this.chartInstance.shutdown();
		this.chartData[0].data = getChartData(this.data['chart_data']['users'], this.$chart, this, this.frequency);
		this.chartData[1].data = getChartData(this.data['chart_data']['page_views'], this.$chart, this, this.frequency);
		this.chartInstance = this.$chart.plot(this.chartData, this.chartOptions).data('plot');
	}
};

/**
 * Load the data for the data tab.
 */
GoogleAnalytics.prototype.loadDataTabData = function() {
	var $frequency = jQuery( '#frequency' );
	if ( $frequency.length ) {
		this.frequency = $frequency.val();
	}
	if ( 'detail' === this.viewName ) {
		this.$topDataErrorContainers.fadeOut();
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
				jQuery.get( ajaxurl, {
					action: 'skystats_ajax_google_analytics_api_query',
					query: 'get_mashboard_view_data',
					start_date: curInst.startDate,
					end_date: curInst.endDate
				}, function ( response ) {
					response = jQuery.parseJSON( response );
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
					jQuery.when( detailViewDataDeferredObject, topKeywordsDeferredObject, topSearchEngineReferralsDeferredObject, topLandingPagesDeferredObject, topVisitorLocationsDeferredObject )
						.done( function( detailViewData, topKeywordsData, topSearchEngineReferralsData, topLandingPagesData, topVisitorLocationsData ) {

							var detailViewDataObj = jQuery.parseJSON( detailViewData[ 0 ] );

							if ( 'error' === detailViewDataObj['responseType'] ) {
								curInst.loadSettingsTabData( 'fresh' );
							} else {
								// Signal data was retrieved
								curInst.loadedAllData = true;
								// Store data
								curInst.data = jQuery.parseJSON( detailViewData[0] ).data;
								curInst.topKeywordsData = jQuery.parseJSON( topKeywordsData[0] ).data;
								curInst.topSearchEngineReferralsData = jQuery.parseJSON( topSearchEngineReferralsData[0] ).data;
								curInst.topLandingPagesData = jQuery.parseJSON( topLandingPagesData[0] ).data;
								curInst.topVisitorLocationsData = jQuery.parseJSON( topVisitorLocationsData[0] ).data;
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
	return jQuery.get( ajaxurl, {
		action: 'skystats_ajax_google_analytics_api_query',
		query: 'get_detail_view_data',
		start_date: this.startDate,
		end_date: this.endDate,
		frequency: this.frequency
	});
};
GoogleAnalytics.prototype.getTopKeywordsDeferredObject = function() {
	return jQuery.get( ajaxurl, {
		action: 'skystats_ajax_get_google_analytics_top_data',
		data_type: 'keywords',
		start_date: this.startDate,
		end_date: this.endDate
	});
};
GoogleAnalytics.prototype.getTopSearchEngineReferralsDeferredObject = function() {
	return jQuery.get( ajaxurl, {
		action: 'skystats_ajax_get_google_analytics_top_data',
		data_type: 'search_engine_referrals',
		start_date: this.startDate,
		end_date: this.endDate
	});
};
GoogleAnalytics.prototype.getTopLandingPagesDeferredObject = function() {
	return jQuery.get( ajaxurl, {
		action: 'skystats_ajax_get_google_analytics_top_data',
		data_type: 'landing_pages',
		start_date: this.startDate,
		end_date: this.endDate
	});
};
GoogleAnalytics.prototype.getTopVisitorLocationsDeferredObject = function() {
	return jQuery.get( ajaxurl, {
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
	if ( 'detail' === this.viewName ) {
		this.$topDataErrorContainers.fadeOut();
	}
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
		// Make sure we update the tooltip formatter for this integration
		curInst.chartOptions.tooltipOpts.content = curInst.defaultTooltipContentFormatter;
		curInst.chartInstance = curInst.$chart.plot( curInst.chartData, curInst.chartOptions ).data( 'plot' );

		// Users
		jQuery('#skystats-google-analytics-users-total').html( curInst.data['users'] );
		jQuery('#skystats-google-analytics-users-change').html( curInst.data['users_change'] );
		var $usersChangeInfo = jQuery( '#skystats-google-analytics-users-change-info' );
		curInst.changeTooltipContent( $usersChangeInfo, curInst.data['previous_users'] + ' ' + $usersChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass(
			'skystats-google-analytics-users-change-direction',
			curInst.data['users_change_direction'],
			false
		);
		setDataPointChangeClass(
			'skystats-google-analytics-users-change',
			curInst.data['users_change_direction'],
			false
		);

		// Page Views
		jQuery('#skystats-google-analytics-page-views-total').html( curInst.data['page_views'] );
		jQuery('#skystats-google-analytics-page-views-change').html( curInst.data['page_views_change'] );
		var $pageViewsChangeInfo = jQuery( '#skystats-google-analytics-page-views-change-info' );
		curInst.changeTooltipContent( $pageViewsChangeInfo, curInst.data['previous_page_views'] + ' ' + $pageViewsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass(
			'skystats-google-analytics-page-views-change-direction',
			curInst.data['page_views_change_direction'],
			false
		);
		setDataPointChangeClass(
			'skystats-google-analytics-page-views-change',
			curInst.data['page_views_change_direction'],
			false
		);

		// Donut for bounce rate is currently only used on the mashboard
		if ( 'mashboard' === curInst.viewName ) {


			var $bounceRateDonut = jQuery( '#skystats-google-analytics-bounce-rate' );

			$bounceRateDonut.html( '' );

			// If the bounce rate data point was hidden on the data tab and then reopened,
			// we need it to be visible for our calculations, then we'll hide it and only display it if this wasn't
			// the case.
			var $bounceRateDataPoint = jQuery( '#skystats-google-analytics-bounce-rate-data-point' ),
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
				curInst.data['bounce_rate_change_direction'],
				false
			);

			$bounceRateChange
				.attr( 'fill', $bounceRateChange.css( 'color' ) )
				.css( 'font-size', '19px' )
				.attr( 'dy', '6' );

			var $bounceRateChangePercentSymbol = jQuery( '<tspan dy="-4" dx="2" fill="' + $bounceRateChange.css( 'color' ) + '" style="font-size: 12px;">%</tspan>' );
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
			jQuery( '#skystats-google-analytics-pages-per-visit-total' ).html( curInst.data['pages_per_visit'] );
			jQuery( '#skystats-google-analytics-pages-per-visit-change' ).html( curInst.data['pages_per_visit_change'] );
			var $pagesPerVisitChangeInfo = jQuery( '#skystats-google-analytics-pages-per-visit-change-info' );
			curInst.changeTooltipContent( $pagesPerVisitChangeInfo, curInst.data['previous_pages_per_visit'] + ' ' + $pagesPerVisitChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-google-analytics-pages-per-visit-change-direction', curInst.data['pages_per_visit_change_direction'], false );
			setDataPointChangeClass( 'skystats-google-analytics-pages-per-visit-change', curInst.data['pages_per_visit_change_direction'], false );

			// Average Visit Duration (min:sec)
			jQuery( '#skystats-google-analytics-average-visit-duration-total' ).html( curInst.data['average_visit_duration'] );
			jQuery( '#skystats-google-analytics-average-visit-duration-change' ).html( curInst.data['average_visit_duration_change'] );
			var $averageVisitDurationChangeInfo = jQuery( '#skystats-google-analytics-average-visit-duration-change-info' );
			curInst.changeTooltipContent( $averageVisitDurationChangeInfo, curInst.data['previous_average_visit_duration'] + ' ' + $averageVisitDurationChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-google-analytics-average-visit-duration-change-direction', curInst.data['average_visit_duration_change_direction'], false );
			setDataPointChangeClass( 'skystats-google-analytics-average-visit-duration-change', curInst.data['average_visit_duration_change_direction'], false );

			// Bounce Rate
			var $bounceRateTotal = jQuery( '#skystats-google-analytics-bounce-rate-total' );
			$bounceRateTotal.html( curInst.data['bounce_rate'] );
			$bounceRateChange = jQuery( '#skystats-google-analytics-bounce-rate-change' );
			$bounceRateChange.html( curInst.data['bounce_rate_change'] );
			var $bounceRateChangeInfo = jQuery( '#skystats-google-analytics-bounce-rate-change-info' );
			curInst.changeTooltipContent( $bounceRateChangeInfo, curInst.data['previous_bounce_rate'] + ' ' + $bounceRateChangeInfo.attr( 'data-tooltip-backup' ) );
			var direction = 'neutral';
			if ( 'neutral' === curInst.data['bounce_rate_change_direction'] ) {
				setDataPointChangeDirectionClass('skystats-google-analytics-bounce-rate-change-direction', 'neutral', false );
				setDataPointChangeClass('skystats-google-analytics-bounce-rate-change', 'neutral', false );
			} else {
				var $bounceRateChangeDirection = jQuery( '#skystats-google-analytics-bounce-rate-change-direction' );

				if ( 'positive' === curInst.data['bounce_rate_change_direction'] ) {

					$bounceRateChange.attr( 'class','skystats-data-point-change skystats-data-point-change-increase-negative' );
					$bounceRateChangeDirection.attr( 'class', 'skystats-data-point-change-direction-increase-negative' );

				} else if ( 'negative' === curInst.data['bounce_rate_change_direction'] ) {

					$bounceRateChange.attr( 'class', 'skystats-data-point-change skystats-data-point-change-decrease-positive' );
					$bounceRateChangeDirection.attr( 'class', 'skystats-data-point-change-direction-decrease-positive' );
				}
			}

			// Top Keywords Data
			(function() {
				var $table = jQuery( '#skystats-google-analytics-top-keywords-data-table' ),
					$tbody = $table.find( '> tbody' );
				if ( null == curInst.topKeywordsData || ! curInst.topKeywordsData.length ) {
					$table.hide();
					curInst.$topKeywordsDataErrorContainer.show();
				} else {
					$tbody.html( '' );
					var rows = '';
					jQuery.each( curInst.topKeywordsData, function ( unused, data ) {
						rows += '<tr><td>' + data['keyword'] + '</td><td>' + data['visits'] + '</td><td>' + data['percent'] + '</td></tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				jQuery( '#skystats-detail-google-analytics-top-keywords-data-table-column-content' ).fadeIn();
			})();

			// Top Search Engine Referrals
			(function() {
				var $table = jQuery( '#skystats-google-analytics-top-search-engine-referrals-data-table' ),
					$tbody = $table.find( '> tbody' );
				if ( null == curInst.topSearchEngineReferralsData || ! curInst.topSearchEngineReferralsData.length ) {
					$table.hide();
					curInst.$topSearchEngineReferralsDataErrorContainer.show();
				} else {
					$tbody.html( '' );
					var rows = '';
					jQuery.each( curInst.topSearchEngineReferralsData, function ( unused, data ) {
						rows += '<tr><td>' + data['search_engine'] + '</td><td>' + data['visits'] + '</td><td>' + data['percent'] + '</td></tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				jQuery( '#skystats-detail-google-analytics-top-search-engine-referrals-data-table-column-content' ).fadeIn();
			})();

			// Top Landing Pages
			(function() {
				var $table = jQuery( '#skystats-google-analytics-top-landing-pages-data-table' ),
					$tbody = $table.find( '> tbody' );
				if ( null == curInst.topLandingPagesData || ! curInst.topLandingPagesData.length ) {
					$table.hide();
					curInst.$topLandingPagesDataErrorContainer.show();
				} else {
					$tbody.html( '' );
					var rows = '';
					jQuery.each( curInst.topLandingPagesData, function ( unused, data ) {
						rows += '<tr><td>' + data['page'] + '</td><td>' + data['visits'] + '</td><td>' + data['percent'] + '</td></tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				jQuery( '#skystats-detail-google-analytics-top-landing-pages-data-table-column-content' ).fadeIn();
			})();

			// Top Visitor Locations
			(function() {
				var $table = jQuery( '#skystats-google-analytics-visitor-locations-data-table' ),
					$tbody = $table.find( '> tbody' );
				if ( null == curInst.topVisitorLocationsData || ! curInst.topVisitorLocationsData.length ) {
					$table.hide();
					curInst.$topVisitorLocationsDataErrorContainer.show();
				} else {
					$tbody.html( '' );
					var rows = '';
					jQuery.each( curInst.topVisitorLocationsData, function ( unused, data ) {
						rows += '<tr><td>' + data['country'] + '</td><td>' + data['visits'] + '</td><td>' + data['percent'] + '</td></tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				jQuery('#skystats-detail-google-analytics-top-visitor-locations-data-table-column-content').fadeIn();
			})();
		}

		/*
		 Search Engine Visits
		 */
		jQuery( '#skystats-google-analytics-search-engine-visits-total' ).html( curInst.data['search_engine_visits'] );
		jQuery( '#skystats-google-analytics-search-engine-visits-change' ).html( curInst.data['search_engine_visits_change'] );
		var $searchEngineVisitsChangeInfo = jQuery( '#skystats-google-analytics-search-engine-visits-change-info' );
		curInst.changeTooltipContent( $searchEngineVisitsChangeInfo, curInst.data['previous_search_engine_visits'] + ' ' + $searchEngineVisitsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass(
			'skystats-google-analytics-search-engine-visits-change-direction',
			curInst.data['search_engine_visits_change_direction'],
			false
		);
		setDataPointChangeClass(
			'skystats-google-analytics-search-engine-visits-change',
			curInst.data['search_engine_visits_change_direction'],
			false
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
	if ( 'detail' === this.viewName ) {
		this.$topDataErrorContainers.fadeOut();
	}
	var curInst = this,
		$fadeOutObjects = ( 'mashboard' === this.viewName ) ?
			this.$dataPointsContainer.add( this.$chartContainer ) :
			this.$dataPointsContainer.add( this.$chartContainer ).add( this.$dataTableColumnsContent );
	$fadeOutObjects.fadeOut( 400 ).promise().done( function() {
		curInst.$loadingContainer.fadeIn( 400, function() {
			jQuery.get( ajaxurl, {
				action: 'skystats_ajax_google_analytics_api_query',
				query : 'get_profiles',
				request_type: requestType
			}, function ( response ) {
				response = jQuery.parseJSON( response );
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
				$profiles = jQuery( '#ga-profiles' );

			jQuery.each( response.data, function( propertyId, propertyData ) {
				jQuery.each( propertyData, function( propertyName, views ) {
					selectHTML += '<optgroup label="' + propertyName + '">';
					jQuery.each( views, function( idx, data ) {
						var selected = ( curInst.selectedProfileID == data.id ) ? 'selected="selected"' : '';
						selectHTML += '<option value="' + data.id.toString() + '" ' + selected + '>' + data.name.toString() + '</option>';
					});
					selectHTML += '</optgroup>';
				});
			});
			$profiles.html( selectHTML );

			curInst.$settingsTabProfilesSection.fadeIn();
			$profiles.chosen({
				max_selected_options: 1,
				width: '100%'
			});

			// When a profile is selected and 'Save' is clicked.
			var $saveGAProfileID = jQuery( '#save_ga_profile' );
			$saveGAProfileID.off( 'click' );
			$saveGAProfileID.one( 'click', function( e ) {
				e.preventDefault();
				curInst.disableSettingsIcon();
				var profileID = $profiles.val();
				curInst.selectedProfileID = profileID;
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						jQuery.post( ajaxurl, {
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
			var $authorizeReauthorize = jQuery( '#skystats-google-analytics-authorize, #skystats-google-analytics-reauthorize' );
			$authorizeReauthorize.off( 'click' )
			$authorizeReauthorize.on('click', function( event ) {
				event.preventDefault();
				if ( null != curInst.authPopupWindowIntervalId ) {
					clearInterval( curInst.authPopupWindowIntervalId );
				}
				curInst.createPopupWindow( curInst.authPopupWindowURL, 'GoogleAnalyticsAuthPopup', 900, 500, null );
				curInst.authPopupWindowIntervalId = setInterval( function() {
					try {
						if ( null == curInst.popupWindow || curInst.popupWindow.closed ) {
							clearInterval( curInst.authPopupWindowIntervalId );
						}
						if ( curInst.authPopupWindowCompleteURL === curInst.popupWindow.location.href || curInst.authPopupWindowCompleteURL + '#' === curInst.popupWindow.location.href )  {
							curInst.popupWindow.close();
							clearInterval( curInst.authPopupWindowIntervalId );
							curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
								curInst.loadSettingsTabData( 'fresh' );
							});
						}
					} catch (e) {
					}
				}, 100 );
			});
		}

		if ( curInst.$settingsTabDeauthorizeSection.is( ':visible' ) ) {
			// When 'Deauthorize' is clicked.
			var $deauthorize = jQuery( '#skystats-google-analytics-deauthorize' );
			$deauthorize.off( 'click' );
			$deauthorize.one( 'click', function( event ) {
				event.preventDefault();
				curInst.disableSettingsIcon();
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						jQuery.post( ajaxurl, {
							action: 'skystats_ajax_google_analytics_deauthorize'
						}, function() {
							curInst.data = null;
							curInst.viewingSettingsTab = false;
							curInst.loadingSettingsTabData = true;
							curInst.loadSettingsTabData( 'fresh' );
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

	this.$loadingContainer = jQuery( '#skystats-facebook-loading-container' );

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
	this.$dataTableColumnsContent = jQuery( '.skystats-data-table-column-content'  );

	/* Data Points */
	this.$dataTabContent = jQuery( '#skystats-facebook-data-tab-content' );
	this.$dataPointsContainer = jQuery( '#skystats-facebook-data-points-container' );

	/* Chart */
	this.chartInstance = null;
	this.chartID = 'skystats-facebook-chart';
	this.$chart = jQuery( '#' + this.chartID );
	this.$chartContainer = jQuery( '#skystats-facebook-chart-container' );
	this.chartData = [
		{
			color: '#3b5999',
			data: [],
			label: '' /* Total Likes */
		},
		{
			color: '#5890ff',
			data: [],
			label: '' /* Total Reach */
		}
	];

	/* Grid */
	this.gridIconSelector = '#skystats-facebook-grid-icon';
	this.$gridIcon = jQuery( this.gridIconSelector );
	this.gridIconContentSelector = '#skystats-facebook-grid-icon-content';
	this.gridIconChildrenContent = jQuery( '#skystats-facebook-grid-icon-content' ).html();
	this.gridDisplayed = false;
	this.gridClickEventName = 'click.fb_grid';
	this.gridMouseoverEventName = 'mouseover.fb_grid';
	this.gridMouseleaveEventName = 'mouseleave.fb_grid';
	this.gridMouseoutEventName = 'mouseout.fb_grid';
	this.gridIconEnabled = false;

	/* Settings */
	this.loadingSettingsTabData = false;
	this.viewingSettingsTab = false;
	this.$settingsIcon = jQuery( '#skystats-facebook-settings-icon');
	this.$settingsTabSections = jQuery( '.skystats-facebook-settings-tab-section' );
	this.$settingsTabNoPagesSection = jQuery( '#skystats-facebook-settings-no-pages-section' );
	this.$settingsTabPageSelectionSection = jQuery( '#skystats-facebook-settings-page-selection-section' );
	this.$settingsTabReauthorizeSection = jQuery( '#skystats-facebook-settings-reauthorize-section' );
	this.$settingsTabDeauthorizeSection = jQuery( '#skystats-facebook-settings-deauthorize-section' );
	this.$settingsTabAuthorizeSection = jQuery( '#skystats-facebook-settings-authorize-section' );
	this.settingsClickEventName = 'click.fb_settings';
	this.settingsIconEnabled = false;

	this.$topDataErrorContainers = jQuery( '.skystats-top-data-error-container' );
	this.$topPostsDataErrorContainer = jQuery( '#skystats-facebook-top-posts-data-error-container' );

	this.authPopupWindowIntervalId = null;
	// URL popup window first directs to authorize the user.
	this.authPopupWindowURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['facebook']['auth_popup_window_url'] :
		skystats_facebook['auth_popup_window_url'];
	// URL popup window directs to when authorization is complete.
	this.authPopupWindowCompleteURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['auth_popup_window_complete_url'] :
		skystats_facebook['auth_popup_window_complete_url'];
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
				jQuery.get( ajaxurl, {
					action: 'skystats_ajax_facebook_get_mashboard_view_data',
					start_date: curInst.startDate,
					end_date: curInst.endDate
				}, function ( response ) {
					response = jQuery.parseJSON( response );
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
					jQuery.when( detailViewDataDeferredObject, topPostsDataDeferredObject).done( function ( detailViewData, topPostsData ) {
						var detailViewDataObj = jQuery.parseJSON( detailViewData[0] );
						if ( 'error' === detailViewDataObj['responseType'] ) {
							curInst.loadSettingsTabData( 'fresh' );
						} else {
							curInst.loadedAllData = true;
							curInst.data = jQuery.parseJSON( detailViewData[0] ).data;
							curInst.topPostsData = jQuery.parseJSON( topPostsData[0] ).data;
							curInst.displayDataTabData();
						}
					});
				}
			}
		});
	});
};

Facebook.prototype.getDetailViewDataDeferredObject = function() {
	return jQuery.get( ajaxurl, {
		action: 'skystats_ajax_facebook_get_detail_view_data',
		start_date: this.startDate,
		end_date: this.endDate
	});
};
Facebook.prototype.getTopPostsDeferredObject = function() {
	return jQuery.get( ajaxurl, {
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
	if ( 'detail' === this.viewName ) {
		this.$topDataErrorContainers.fadeOut();
	}
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
		jQuery( '#skystats-facebook-total-likes-total' ).html( curInst.data['total_likes'] );
		jQuery( '#skystats-facebook-total-likes-change' ).html( curInst.data['total_likes_change'] );
		var $totalLikesChangeInfo = jQuery( '#skystats-facebook-total-likes-change-info' );
		curInst.changeTooltipContent( $totalLikesChangeInfo, curInst.data['previous_total_likes']  + ' ' + $totalLikesChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-facebook-total-likes-change-direction', curInst.data['total_likes_change_direction'], false );
		setDataPointChangeClass( 'skystats-facebook-total-likes-change', curInst.data['total_likes_change_direction'], false );

		// Total Reach
		jQuery( '#skystats-facebook-total-reach-total' ).html( curInst.data['total_reach'] );
		jQuery( '#skystats-facebook-total-reach-change' ).html( curInst.data['total_reach_change'] );
		var $totalReachChangeInfo = jQuery( '#skystats-facebook-total-reach-change-info' );
		curInst.changeTooltipContent( $totalReachChangeInfo, curInst.data['previous_total_reach'] + ' ' + $totalReachChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-facebook-total-reach-change-direction', curInst.data['total_reach_change_direction'], false );
		setDataPointChangeClass( 'skystats-facebook-total-reach-change', curInst.data['total_reach_change_direction'], false );

		// Page Views
		jQuery( '#skystats-facebook-page-visits-total' ).html( curInst.data['page_views'] );
		jQuery( '#skystats-facebook-page-visits-change' ).html( curInst.data['page_views_change'] );
		var $pageViewsChangeInfo = jQuery( '#skystats-facebook-page-visits-change-info' );
		curInst.changeTooltipContent( $pageViewsChangeInfo, curInst.data['previous_page_views'] + ' ' + $pageViewsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass('skystats-facebook-page-visits-change-direction', curInst.data['page_views_change_direction'], false );
		setDataPointChangeClass('skystats-facebook-page-visits-change', curInst.data['page_views_change_direction'], false );

		// People Engaged
		jQuery( '#skystats-facebook-people-engaged-total' ).html( curInst.data['people_engaged'] );
		jQuery( '#skystats-facebook-people-engaged-change' ).html( curInst.data['people_engaged_change'] );
		var $peopleEngagedChangeInfo = jQuery( '#skystats-facebook-people-engaged-change-info' );
		curInst.changeTooltipContent( $peopleEngagedChangeInfo, curInst.data['previous_people_engaged'] + ' ' + $peopleEngagedChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-facebook-people-engaged-change-direction', curInst.data['people_engaged_change_direction'], false );
		setDataPointChangeClass( 'skystats-facebook-people-engaged-change', curInst.data['people_engaged_change_direction'], false );

		if ( 'detail' === curInst.viewName ) {
			var $topPostsDataTableTbody = jQuery( '#skystats-facebook-top-posts-data-table-tbody' ),
				rows = '',
				$topPostsDataTable = $topPostsDataTableTbody.parent();
			if ( null == curInst.topPostsData || ! curInst.topPostsData.length ) {
				$topPostsDataTable.hide();
				curInst.$topPostsDataErrorContainer.show();
			} else {
				$topPostsDataTableTbody.html( '' );
				jQuery.each( curInst.topPostsData, function ( unused, data ) {
					rows += '<tr>' +
					'<td>' + data['name'] + '</td>' +
					'<td>' + data['likes'] + '</td>' +
					'<td>' + data['reach'] + '</td>' +
					'<td>' + data['comments'] + '</td>' +
					'<td>' + data['date'] + '</td>' +
					'</tr>';
				} );
				$topPostsDataTable.show();
				jQuery( rows ).appendTo( $topPostsDataTableTbody );
			}
			jQuery( '#skystats-facebook-top-posts-data-table-column-content' ).fadeIn();
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
	if ( 'detail' === this.viewName ) {
		this.$topDataErrorContainers.fadeOut();
	}
	var curInst = this,
	    $fadeOutObjects = ( 'mashboard' === this.viewName ) ?
		    this.$dataPointsContainer.add( this.$chartContainer ) :
		    this.$dataPointsContainer.add( this.$chartContainer ).add( this.$dataTableColumnsContent );
	$fadeOutObjects.fadeOut( 400 ).promise().done( function () {
		curInst.$loadingContainer.fadeIn( 400, function () {
			jQuery.get( ajaxurl, {
				action: 'skystats_ajax_facebook_get_page_list',
				request_type: requestType
			}, function ( response ) {
				response = jQuery.parseJSON( response );
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
				$pages = jQuery( '#skystats-facebook-page-list' );
			jQuery.each( response.data, function ( i, page ) {
				var selected = ( curInst.selectedPageID === page.id ) ? 'selected="selected"' : '';
				selectHTML += '<option value="' + page.id + '" ' + selected + '>' + page.name + '</option>';
			});
			$pages.html( selectHTML );
			curInst.$settingsTabPageSelectionSection.fadeIn();
			$pages.chosen({
				max_selected_options: 1,
				width: '100%'
			});
			// When a profile is selected and 'Save' is clicked.
			var $saveFacebookPageID = jQuery( '#skystats-facebook-save-page-id' );
			$saveFacebookPageID.off( 'click' );
			$saveFacebookPageID.one( 'click', function( e ) {
				e.preventDefault();
				curInst.disableSettingsIcon();
				var pageID = $pages.val();
				curInst.selectedPageID = pageID;
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						jQuery.post( ajaxurl, {
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
			var $authorizeReauthorize = jQuery( '#skystats-facebook-authorize, #skystats-facebook-reauthorize' );
			$authorizeReauthorize.off( 'click' );
			$authorizeReauthorize.on( 'click', function( event ) {
				event.preventDefault();
				if ( null != curInst.authPopupWindowIntervalId ) {
					clearInterval( curInst.authPopupWindowIntervalId );
				}
				curInst.createPopupWindow( curInst.authPopupWindowURL, 'FacebookAuthPopup', 900, 500, null );
				curInst.authPopupWindowIntervalId = setInterval( function() {
					try {
						if ( null == curInst.popupWindow || curInst.popupWindow.closed ) {
							clearInterval( curInst.authPopupWindowIntervalId );
						}
						if ( curInst.popupWindow.location.hasOwnProperty( 'href' ) &&
							( curInst.authPopupWindowCompleteURL === curInst.popupWindow.location.href
							|| curInst.authPopupWindowCompleteURL + '#' === curInst.popupWindow.location.href
							|| curInst.authPopupWindowCompleteURL + '#_=_' === curInst.popupWindow.location.href ) ) {
							curInst.popupWindow.close();
							clearInterval( curInst.authPopupWindowIntervalId );
							curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
								curInst.loadSettingsTabData( 'fresh' );
							});
						}
					} catch (e) {
					}
				}, 100 );
			});
		}

		if ( curInst.$settingsTabDeauthorizeSection.is( ':visible' ) ) {
			var $deauthorize = jQuery( '#skystats-facebook-deauthorize' );
			$deauthorize.off( 'click' );
			$deauthorize.on( 'click', function( event ) {
				event.preventDefault();
				curInst.disableSettingsIcon();
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						jQuery.post( ajaxurl, {
							action: 'skystats_ajax_facebook_deauthorize'
						}, function() {
							curInst.data = null;
							curInst.viewingSettingsTab = false;
							curInst.loadingSettingsTabData = true;
							curInst.loadSettingsTabData( 'fresh' );
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
		setGlobalPossibleMarkers(null);
		this.chartInstance.shutdown();
		this.chartData[0].data = getChartData(this.data['chart_data']['likes'], this.$chart, this, 'daily');
		this.chartData[1].data = getChartData(this.data['chart_data']['reach'], this.$chart, this, 'daily');
		this.chartInstance = this.$chart.plot(this.chartData, this.chartOptions).data('plot');
	}
};

/*
 * Twitter
 */
function Twitter( viewName ) {

	Integration.call( this, viewName );

	this.integrationName = 'Twitter';

	this.$loadingContainer = jQuery( '#skystats-twitter-loading-container' );

	this.translations = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['trans']:
		skystats_twitter['trans'];

	/* Data */
	this.data = null; // Contains data for the chart and data points.
	this.loadingDataTabData = false;
	this.viewingDataTab = false;
	this.loadedAllData = false; // Detail page specific. Whether all data was successfully loaded or at least attempted.
	this.$dataTableColumnsContent = jQuery( '.skystats-data-table-column-content'  );

	/* Data Points */
	this.$dataTabContent = jQuery( '#skystats-twitter-data-tab-content' );
	this.$dataPointsContainer = jQuery( '#skystats-twitter-data-points-container' );

	/* Chart */
	this.chartInstance = null;
	this.chartID = 'skystats-twitter-chart';
	this.$chart = jQuery( '#' + this.chartID );
	this.$chartContainer = jQuery( '#skystats-twitter-chart-container' );
	this.chartData = [
		{
			color: '#66ccff',
			data: [],
			label: ''
		},
		{
			color: '#35d1ae',
			data: [],
			label: ''
		}
	];

	/* Grid */
	this.gridIconSelector = '#skystats-twitter-grid-icon';
	this.$gridIcon = jQuery( this.gridIconSelector );
	this.gridIconContentSelector = '#skystats-twitter-grid-icon-content';
	this.gridIconChildrenContent = jQuery( '#skystats-twitter-grid-icon-content' ).html();
	this.gridDisplayed = false;
	this.gridClickEventName = 'click.twitter_grid';
	this.gridMouseoverEventName = 'mouseover.twitter_grid';
	this.gridMouseleaveEventName = 'mouseleave.twitter_grid';
	this.gridMouseoutEventName = 'mouseout.twitter_grid';

	/* Settings */
	this.loadingSettingsTabData = false;
	this.viewingSettingsTab = false;
	this.$settingsIcon = jQuery( '#skystats-twitter-settings-icon');
	this.$settingsTabSections = jQuery( '.skystats-twitter-settings-tab-section' );
	this.$settingsTabDeauthorizeSection = jQuery( '#skystats-twitter-settings-deauthorize-section' );
	this.$settingsTabAuthorizeSection = jQuery( '#skystats-twitter-settings-authorize-section' );
	this.$settingsTabValidAccessTokenSection = jQuery( '#skystats-twitter-settings-valid-access-token-section' );
	this.$settingsTabInvalidAcccessTokenSection = jQuery( '#skystats-twitter-settings-invalid-access-token-section' );
	this.$settingsTabRateLimitReachedSection = jQuery( '#skystats-twitter-settings-rate-limit-reached-section' );
	this.settingsClickEventName = 'click.twitter_settings';

	this.$topDataErrorContainers = jQuery( '.skystats-top-data-error-container' );
	this.$topTweetsDataErrorContainer = jQuery( '#skystats-twitter-top-tweets-data-error-container' );
	this.$topRetweetsDataErrorContainer = jQuery( '#skystats-twitter-top-retweets-data-error-container' );
	this.$topMentionsDataErrorContainer = jQuery( '#skystats-twitter-top-mentions-data-error-container' );
	this.$topFavouritesDataErrorContainer = jQuery( '#skystats-twitter-top-favourites-data-error-container' );

	this.authPopupWindowIntervalId = null;
	// URL popup window first directs to authorize the user.
	this.authPopupWindowURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['twitter']['auth_popup_window_url'] :
		skystats_twitter['auth_popup_window_url'];
	// URL popup window directs to when authorization is complete.
	this.authPopupWindowCompleteURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['auth_popup_window_complete_url'] :
		skystats_twitter['auth_popup_window_complete_url'];
}
Twitter.prototype = createObject( Integration.prototype );
Twitter.prototype.constructor = Twitter;

/**
 * Load data for the data tab.
 */
Twitter.prototype.loadDataTabData = function() {
	this.hideChartFigure();
	this.$topDataErrorContainers.fadeOut();
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
					jQuery.get( ajaxurl, {
						action: 'skystats_ajax_twitter_api_query',
						query: 'get_mashboard_view_data',
						start_date: curInst.startDate,
						end_date: curInst.endDate
					}, function ( response ) {
						response = jQuery.parseJSON( response );
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
						jQuery.get( ajaxurl, {
							action: 'skystats_ajax_twitter_api_query',
							query: 'get_detail_view_data',
							start_date: curInst.startDate,
							end_date: curInst.endDate
						}, function ( response ) {
							response = jQuery.parseJSON( response );
							if ( null != response.data ) {
								curInst.data = response.data;
								curInst.displayDataTabData();
							} else {
								curInst.loadSettingsTabData( 'fresh' );
							}
						} );
					}
				}
			});
		});
};

/**
 * Display the data for the data tab.
 */
Twitter.prototype.displayDataTabData = function() {
	var curInst = this;
	this.$topDataErrorContainers.fadeOut();
	this.$loadingContainer.fadeOut( 400, function() {

		curInst.showChartFigure();
		curInst.$dataTabContent.fadeIn();
		curInst.$chartContainer.show();

		// Do we need to destroy the chart?
		if ( null !== curInst.chartInstance ) {
			curInst.chartInstance.shutdown();
		}
		// Create chart
		curInst.chartData[ 0 ].data = getChartData( curInst.data['chart_data']['favourites'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartData[ 1 ].data = getChartData( curInst.data['chart_data']['mentions'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartInstance = curInst.$chart.plot( curInst.chartData, curInst.chartOptions ).data( 'plot' );
		curInst.$dataPointsContainer.show();

		/*
		 * Period data
		 */

		// Tweets
		jQuery( '#skystats-twitter-tweets' ).html( curInst.data['period']['tweets'] );
		jQuery( '#skystats-twitter-tweets-change' ).html( curInst.data['period']['tweets_change'] );
		var $tweetsChangeInfo = jQuery( '#skystats-twitter-tweets-change-info' );
		curInst.changeTooltipContent( $tweetsChangeInfo, curInst.data['period']['previous_tweets'] + ' ' + $tweetsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-twitter-tweets-change-direction', curInst.data['period']['tweets_change_direction'], false );
		setDataPointChangeClass( 'skystats-twitter-tweets-change', curInst.data['period']['tweets_change_direction'], false );

		// Following
		jQuery( '#skystats-twitter-following' ).html( curInst.data['period']['following'] );
		jQuery( '#skystats-twitter-following-change' ).html( curInst.data['period']['following_change'] );
		var $followingChangeInfo = jQuery( '#skystats-twitter-following-change-info' );
		curInst.changeTooltipContent( $followingChangeInfo, curInst.data['period']['previous_following'] + ' ' + $followingChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-twitter-following-change-direction', curInst.data['period']['following_change_direction'], false );
		setDataPointChangeClass( 'skystats-twitter-following-change', curInst.data['period']['following_change_direction'], false );

		// Followers
		jQuery( '#skystats-twitter-followers' ).html( curInst.data['period']['followers'] );
		jQuery( '#skystats-twitter-followers-change' ).html( curInst.data['period']['followers_change'] );
		var $followersChangeInfo = jQuery( '#skystats-twitter-followers-change-info' );
		curInst.changeTooltipContent( $followersChangeInfo, curInst.data['period']['previous_followers'] + ' ' + $followersChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-twitter-followers-change-direction', curInst.data['period']['followers_change_direction'], false );
		setDataPointChangeClass( 'skystats-twitter-followers-change', curInst.data['period']['followers_change_direction'], false );

		// Retweets
		jQuery( '#skystats-twitter-retweets' ).html( curInst.data['period']['retweets'] );
		jQuery( '#skystats-twitter-retweets-change' ).html( curInst.data['period']['retweets_change'] );
		var $retweetsChangeInfo = jQuery( '#skystats-twitter-retweets-change-info' );
		curInst.changeTooltipContent( $retweetsChangeInfo, curInst.data['period']['previous_retweets'] + ' ' + $retweetsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-twitter-retweets-change-direction', curInst.data['period']['retweets_change_direction'], false );
		setDataPointChangeClass( 'skystats-twitter-retweets-change', curInst.data['period']['retweets_change_direction'], false );

		// Favourites
		jQuery( '#skystats-twitter-favourites' ).html( curInst.data['period']['favourites'] );
		jQuery( '#skystats-twitter-favourites-change' ).html( curInst.data['period']['favourites_change'] );
		var $favouritesChangeInfo = jQuery( '#skystats-twitter-favourites-change-info' );
		curInst.changeTooltipContent( $favouritesChangeInfo, curInst.data['period']['previous_favourites'] + ' ' + $favouritesChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-twitter-favourites-change-direction', curInst.data['period']['favourites_change_direction'], false );
		setDataPointChangeClass( 'skystats-twitter-favourites-change', curInst.data['period']['favourites_change_direction'], false );

		// Mentions
		jQuery( '#skystats-twitter-mentions' ).html( curInst.data['period']['mentions'] );
		jQuery( '#skystats-twitter-mentions-change' ).html( curInst.data['period']['mentions_change'] );
		var $mentionsChangeInfo = jQuery( '#skystats-twitter-mentions-change-info' );
		curInst.changeTooltipContent( $mentionsChangeInfo, curInst.data['period']['previous_mentions'] + ' ' + $mentionsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-twitter-mentions-change-direction', curInst.data['period']['mentions_change_direction'], false );
		setDataPointChangeClass( 'skystats-twitter-mentions-change', curInst.data['period']['mentions_change_direction'], false );

		if ( 'detail' === curInst.viewName ) {

			var $currentTweets = jQuery( '#skystats-twitter-current-tweets' );
			$currentTweets.html( curInst.data['current']['tweets'] );

			var $currentRetweets = jQuery( '#skystats-twitter-current-retweets' );
			$currentRetweets.html( curInst.data['current']['retweets'] );

			var $currentFollowing = jQuery( '#skystats-twitter-current-following' );
			$currentFollowing.html( curInst.data['current']['following'] );

			var $currentFollowers = jQuery( '#skystats-twitter-current-followers' );
			$currentFollowers.html( curInst.data['current']['followers'] );

			var $currentMentions = jQuery( '#skystats-twitter-current-mentions' );
			$currentMentions.html( curInst.data['current']['mentions'] );

			// Top Latest Tweets
			(function () {
				var $table = jQuery( '#skystats-twitter-top-latest-tweets' ),
					$column = $table.parent(),
					$tbody = $table.find( 'tbody' ),
					topLatestTweets = curInst.data['period']['top_tweets'],
					rows = '';

				if ( ! topLatestTweets.length ) {
					$table.hide();
					curInst.$topTweetsDataErrorContainer.show();
				} else {
					$tbody.html( '' );
					jQuery.each( topLatestTweets, function ( i, v ) {
						rows += '<tr>' +
						'<td><p>' + v['text'] + '</p><p>' + parseTopTableDate( v['date'] ) + '</p></td>' +
						'<td>' + v['retweets'] + '</td>' +
						'<td>' + v['favourites'] + '</td>' +
						'</tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				$column.show();
			})();

			// Top Latest Retweets
			(function() {
				var $table = jQuery( '#skystats-twitter-top-latest-retweets' ),
					$column = $table.parent(),
					$tbody = $table.find( 'tbody' ),
					topLatestRetweets = curInst.data['period']['top_retweets'],
					rows = '';

				if ( ! topLatestRetweets.length ) {
					$table.hide();
					curInst.$topRetweetsDataErrorContainer.show();
				} else {
					$tbody.html( '' );
					jQuery.each( topLatestRetweets, function ( i, v ) {
						rows += '<tr>' +
						'<td><p>' + v['text'] + '</p><p>' + parseTopTableDate( v['date'] ) + '</p></td>' +
						'<td>' + v['retweets'] + '</td>' +
						'</tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				$column.show();
			})();

			// Top Latest Mentions
			(function() {
				var $table = jQuery( '#skystats-twitter-top-latest-mentions' ),
					$column = $table.parent(),
					$tbody = $table.find( 'tbody' ),
					topLatestMentions = curInst.data['period']['top_mentions'],
					rows = '';

				if ( ! topLatestMentions.length ) {
					$table.hide();
					curInst.$topMentionsDataErrorContainer.show();
				} else {
					$tbody.html( '' );
					jQuery.each( topLatestMentions, function ( i, v ) {
						rows += '<tr>' +
						'<td>' + v['user_screen_name'] + '</td>' +
						'<td><p>' + v['text'] + '</p><p>' + parseTopTableDate( v['date'] ) + '</p></td>' +
						'<td>' + v['retweets'] + '</td>' +
						'<td>' + v['favourites'] + '</td>' +
						'</tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				$column.show();
			})();

			// Top Latest Favourites
			(function() {
				var $table = jQuery( '#skystats-twitter-top-latest-favourites' ),
					$column = $table.parent(),
					$tbody = $table.find( 'tbody' ),
					topLatestFavourites = curInst.data['period']['top_favourites'],
					rows = '';

				if ( ! topLatestFavourites.length ) {
					$table.hide();
					curInst.$topFavouritesDataErrorContainer.show();
				} else {
					$tbody.html( '' );
					jQuery.each( topLatestFavourites, function ( i, v ) {
						rows += '<tr>' +
						'<td><p>' + v['text'] + '</p><p>' + parseTopTableDate( v['date'] ) + '</p></td>' +
						'<td>' + v['favourites'] + '</td>' +
						'</tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				$column.show();
			})();
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
Twitter.prototype.loadSettingsTabData = function( requestType ) {
	this.hideChartFigure();
	var curInst = this,
		$fadeOutObjects = ( 'mashboard' === this.viewName ) ?
			this.$dataPointsContainer.add( this.$chartContainer ).add( this.$topDataErrorContainers ) :
			this.$dataPointsContainer.add( this.$chartContainer ).add( this.$dataTableColumnsContent ).add( this.$topDataErrorContainers );

	$fadeOutObjects.fadeOut( 400 ).promise().done( function () {
		curInst.$loadingContainer.fadeIn( 400, function () {
			jQuery.get( ajaxurl, {
				action: 'skystats_ajax_twitter_api_query',
				query: 'get_status',
				request_type: requestType
			}, function ( response ) {
				response = jQuery.parseJSON( response );
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
Twitter.prototype.displaySettingsTabData = function( response ) {
	var curInst = this;

	curInst.$loadingContainer.fadeOut( 400, function () {

		if ( 'success' === response['responseType'] ) {
			switch ( response['responseContext'] ) {
				case 'valid_access_token':
					curInst.$settingsTabValidAccessTokenSection.fadeIn();
					curInst.$settingsTabDeauthorizeSection.fadeIn();
					break;
			}
		} else {
			switch ( response['responseContext'] ) {
				case 'authorization_required':
					curInst.$settingsTabAuthorizeSection.fadeIn();
					break;
				case 'invalid_access_token':
					curInst.$settingsTabInvalidAcccessTokenSection.fadeIn();
					curInst.$settingsTabDeauthorizeSection.fadeIn();
					break;
				case 'rate_limit_reached':
					curInst.$settingsTabRateLimitReachedSection.fadeIn();
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

		if ( curInst.$settingsTabAuthorizeSection.is( ':visible' ) ) {
			var $authorize = jQuery( '#skystats-twitter-authorize' );
			$authorize.off( 'click' );
			$authorize.on( 'click', function( event ) {
				event.preventDefault();
				if ( null != curInst.authPopupWindowIntervalId ) {
					clearInterval( curInst.authPopupWindowIntervalId );
				}
				curInst.createPopupWindow( curInst.authPopupWindowURL, 'TwitterAuthPopup', 900, 500, null );
				curInst.authPopupWindowIntervalId = setInterval( function() {
					try {
						if ( null == curInst.popupWindow || curInst.popupWindow.closed ) {
							clearInterval( curInst.authPopupWindowIntervalId );
						}
						if ( curInst.popupWindow.location.hasOwnProperty( 'href' ) && ( curInst.authPopupWindowCompleteURL === curInst.popupWindow.location.href || curInst.authPopupWindowCompleteURL + '#' === curInst.popupWindow.location.href ) ) {
							curInst.popupWindow.close();
							clearInterval( curInst.authPopupWindowIntervalId );
							curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
								curInst.loadDataTabData();
							});
						}
					} catch (e) {
					}
				}, 100 );
			});
		}

		if ( curInst.$settingsTabDeauthorizeSection.is( ':visible' ) ) {
			var $deauthorize = jQuery( '#skystats-twitter-deauthorize' );
			$deauthorize.off( 'click' );
			$deauthorize.on( 'click', function( event ) {
				event.preventDefault();
				curInst.disableSettingsIcon();
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						jQuery.get( ajaxurl, {
							action: 'skystats_ajax_twitter_api_query',
							query: 'deauthorize'
						}, function() {
							curInst.data = null;
							curInst.viewingSettingsTab = false;
							curInst.loadingSettingsTabData = true;
							curInst.loadSettingsTabData( 'fresh' );
						} );
					} );
				});
			});
		}
	});
};

Twitter.prototype.resizeChart = function() {
	if ( null != this.data ) {
		setGlobalPossibleMarkers(null);
		this.chartInstance.shutdown();
		this.chartData[0].data = getChartData(this.data['chart_data']['favourites'], this.$chart, this, 'daily');
		this.chartData[1].data = getChartData(this.data['chart_data']['mentions'], this.$chart, this, 'daily');
		this.chartInstance = this.$chart.plot(this.chartData, this.chartOptions).data('plot');
	}
};

/*
 * Google Adwords
 */
function GoogleAdwords( viewName ) {

	Integration.call( this, viewName );

	this.integrationName = 'GoogleAdwords';

	this.$loadingContainer = jQuery( '#skystats-google-adwords-loading-container' );

	this.translations = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['trans']:
		skystats_google_adwords['trans'];

	this.selectedCustomerId = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['google_adwords']['selected_customer_id'] :
		skystats_google_adwords['selected_customer_id'];

	this.selectedCampaignId = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['google_adwords']['selected_campaign_id'] :
		skystats_google_adwords['selected_campaign_id'];

	this.$integrationErrorContainer = jQuery( '#skystats-google-adwords-error-container' );

	this.currencySymbol = null;

	/* Data */
	this.data = null; // Contains data for the chart and data points.
	this.loadingDataTabData = false;
	this.viewingDataTab = false;
	this.loadedAllData = false; // Detail page specific. Whether all data was successfully loaded or at least attempted.
	this.$dataTableColumnsContent = jQuery( '.skystats-data-table-column-content'  );

	/* Data Points */
	this.$dataTabContent = jQuery( '#skystats-google-adwords-data-tab-content' );
	this.$dataPointsContainer = jQuery( '#skystats-google-adwords-data-points-container' );

	/* Chart */
	this.chartInstance = null;
	this.chartID = 'skystats-google-adwords-chart';
	this.$chart = jQuery( '#' + this.chartID );
	this.$chartContainer = jQuery( '#skystats-google-adwords-chart-container' );
	this.chartData = [
		{
			color: '#4285F4',
			data: [],
			label: 'Cost'
		},
		{
			color: '#0F9D58',
			data: [],
			label: 'Avg. CPC'
		}
	];

	/* Grid */
	this.gridIconSelector = '#skystats-google-adwords-grid-icon';
	this.$gridIcon = jQuery( this.gridIconSelector );
	this.gridIconContentSelector = '#skystats-google-adwords-grid-icon-content';
	this.gridIconChildrenContent = jQuery( '#skystats-google-adwords-grid-icon-content' ).html();
	this.gridDisplayed = false;
	this.gridClickEventName = 'click.google_adwords_grid';
	this.gridMouseoverEventName = 'mouseover.google_adwords_grid';
	this.gridMouseleaveEventName = 'mouseleave.google_adwords_grid';
	this.gridMouseoutEventName = 'mouseout.google_adwords_grid';

	/* Settings */
	this.loadingSettingsTabData = false;
	this.viewingSettingsTab = false;
	this.$settingsIcon = jQuery( '#skystats-google-adwords-settings-icon');
	this.$settingsTabSections = jQuery( '.skystats-google-adwords-settings-tab-section' );
	this.$settingsTabDeauthorizeSection = jQuery( '#skystats-google-adwords-settings-deauthorize-section' );
	this.$settingsTabAuthorizeSection = jQuery( '#skystats-google-adwords-settings-authorize-section' );
	this.$settingsTabCampaignSelectionSection = jQuery( '#skystats-google-adwords-campaign-selection-section' );
	this.$settingsTabAccountSelectionSection = jQuery( '#skystats-google-adwords-account-selection-section' );

	this.settingsClickEventName = 'click.google_adwords_settings';

	this.$topDataErrorContainers = jQuery( '.skystats-top-data-error-container' );
	if ( 'detail' === this.viewName ) {
		this.$accountLevelCampaignsTableErrorContainer = jQuery( '#skystats-google-adwords-account-level-campaigns-table' );
		this.$accountLevelTopKeywordPerformanceTableErrorContainer = jQuery( '#skystats-google-adwords-account-level-top-keyword-performance' );

		this.$dataTableColumns = jQuery( '.skystats-service-detail-data-table-column' );
	}

	this.$campaignSectionDescription = jQuery( '#skystats-google-adwords-select-campaign-description' );

	this.$campaignSectionLoadingIcon = this.$loadingContainer.clone();
	this.$campaignSectionLoadingIcon.attr( 'id', 'skystats-google-adwords-campaign-selection-loading-icon' );
	this.$campaignSectionLoadingIcon.insertBefore( this.$settingsTabCampaignSelectionSection );
	this.$campaignSectionLoadingIcon.hide();

	this.authPopupWindowIntervalId = null;
	// URL popup window first directs to authorize the user.
	this.authPopupWindowURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['google_adwords']['auth_popup_window_url'] :
		skystats_google_adwords['auth_popup_window_url'];
	// URL popup window directs to when authorization is complete.
	this.authPopupWindowCompleteURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['auth_popup_window_complete_url'] :
		skystats_google_adwords['auth_popup_window_complete_url'];
}
GoogleAdwords.prototype = createObject( Integration.prototype );
GoogleAdwords.prototype.constructor = GoogleAdwords;

/**
 * Load data for the data tab.
 */
GoogleAdwords.prototype.loadDataTabData = function() {
	this.hideChartFigure();
	this.$topDataErrorContainers.fadeOut();
	this.$integrationErrorContainer.fadeOut();
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
					jQuery.get( ajaxurl, {
						action: 'skystats_ajax_google_adwords_api_query',
						query: 'get_mashboard_view_data',
						start_date: curInst.startDate,
						end_date: curInst.endDate,
						customer_id: curInst.selectedCustomerId,
						campaign_id: curInst.selectedCampaignId
					}, function ( response ) {
						response = jQuery.parseJSON( response );
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
						jQuery.get( ajaxurl, {
							action: 'skystats_ajax_google_adwords_api_query',
							query: 'get_detail_view_data',
							start_date: curInst.startDate,
							end_date: curInst.endDate,
							customer_id: curInst.selectedCustomerId,
							campaign_id: curInst.selectedCampaignId
						}, function ( response ) {
							response = jQuery.parseJSON( response );
							if ( null != response.data ) {
								curInst.data = response.data;
								curInst.displayDataTabData();
							} else {
								curInst.loadSettingsTabData( 'fresh' );
							}
						} );
					}
				}
			});
		});
};

/**
 * Enable the grid tooltip and data point show/hide toggle ability.
 */
GoogleAdwords.prototype.enableGridIcon = function() {
	Integration.prototype.enableGridIcon.call( this );
};

/**
 * Disable the grid tooltip and data point show/hide toggle ability.
 */
GoogleAdwords.prototype.disableGridIcon = function() {
	Integration.prototype.disableGridIcon.call( this );
};

/**
 * Enable the settings icon tooltip and tab switchability.
 */
GoogleAdwords.prototype.enableSettingsIcon = function() {
	Integration.prototype.enableSettingsIcon.call( this );
};

/**
 * Disable the settings icon tooltip and tab switchability.
 */
GoogleAdwords.prototype.disableSettingsIcon = function() {
	Integration.prototype.disableSettingsIcon.call( this );
};

/**
 * Display the data for the data tab.
 */
GoogleAdwords.prototype.displayDataTabData = function() {
	var curInst = this;
	this.$topDataErrorContainers.fadeOut();
	this.$integrationErrorContainer.fadeOut();
	this.$loadingContainer.fadeOut( 400, function() {

		curInst.showChartFigure();
		curInst.$dataTabContent.fadeIn();
		curInst.$chartContainer.show();

		var currencySymbol = curInst.data['currency_symbol_hex_code'];

		curInst.currencySymbol = currencySymbol;

		// Do we need to destroy the chart?
		if ( null !== curInst.chartInstance ) {
			curInst.chartInstance.shutdown();
		}
		// Create chart
		curInst.chartData[0].data = getChartData( curInst.data['chart_data']['clicks'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartData[1].data = getChartData( curInst.data['chart_data']['avg_cost_per_click'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartData[1].yaxis = 2;
		curInst.chartOptions.yaxes[1].tickFormatter = function( v, axis ) {
			return currencySymbol + parseFloat(v).toFixed( 1 );
		};
		curInst.chartOptions.tooltipOpts.content = function( label, x, y, item ) {
			if ( 'GoogleAdwords' === curInst.integrationName && 'Avg. CPC' === label && item.seriesIndex === 1 ) {
				return '<strong>' + curInst.currencySymbol.toString() + y.toString() + '</strong><br>' + x;
			}
			return '<strong>' + y.toString() + '</strong><br>' + x;
		};
		curInst.chartInstance = curInst.$chart.plot( curInst.chartData, curInst.chartOptions ).data( 'plot' );
		curInst.$dataPointsContainer.show();

		// Cost
		jQuery( '#skystats-google-adwords-cost' ).html( curInst.data['period']['cost'] );
		jQuery( '#skystats-google-adwords-cost-currency').html( currencySymbol );
		jQuery( '#skystats-google-adwords-cost-change' ).html( curInst.data['period']['cost_change'] );
		var $costChangeInfo = jQuery( '#skystats-google-adwords-cost-change-info' );
		curInst.changeTooltipContent( $costChangeInfo, currencySymbol + curInst.data['period']['previous_cost'] + ' ' + $costChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-google-adwords-cost-change-direction', curInst.data['period']['cost_change_direction'], true );
		setDataPointChangeClass( 'skystats-google-adwords-cost-change', curInst.data['period']['cost_change_direction'], true );

		// Clicks
		jQuery( '#skystats-google-adwords-clicks' ).html( curInst.data['period']['clicks'] );
		jQuery( '#skystats-google-adwords-clicks-change' ).html( curInst.data['period']['clicks_change'] );
		var $clicksChangeInfo = jQuery( '#skystats-google-adwords-clicks-change-info' );
		curInst.changeTooltipContent( $clicksChangeInfo, curInst.data['period']['previous_clicks'] + ' ' + $clicksChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-google-adwords-clicks-change-direction', curInst.data['period']['clicks_change_direction'], false );
		setDataPointChangeClass( 'skystats-google-adwords-clicks-change', curInst.data['period']['clicks_change_direction'], false );

		// Avg Cost Per Click
		jQuery( '#skystats-google-adwords-avg-cost-per-click' ).html( curInst.data['period']['avg_cost_per_click'] );
		jQuery( '#skystats-google-adwords-avg-cost-per-click-currency').html( currencySymbol );
		jQuery( '#skystats-google-adwords-avg-cost-per-click-change' ).html( curInst.data['period']['avg_cost_per_click_change'] );
		var $averageCostPerClickChangeInfo = jQuery( '#skystats-google-adwords-avg-cost-per-click-change-info' );
		curInst.changeTooltipContent( $averageCostPerClickChangeInfo, currencySymbol + curInst.data['period']['previous_avg_cost_per_click'] + ' ' + $averageCostPerClickChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-google-adwords-avg-cost-per-click-change-direction', curInst.data['period']['avg_cost_per_click_change_direction'], true );
		setDataPointChangeClass( 'skystats-google-adwords-avg-cost-per-click-change', curInst.data['period']['avg_cost_per_click_change_direction'], true );

		// Conversions
		jQuery( '#skystats-google-adwords-conversions' ).html( curInst.data['period']['conversions'] );
		jQuery( '#skystats-google-adwords-conversions-change' ).html( curInst.data['period']['conversions_change'] );
		var $conversionsChangeInfo = jQuery( '#skystats-google-adwords-conversions-change-info' );
		curInst.changeTooltipContent( $conversionsChangeInfo, curInst.data['period']['previous_conversions'] + ' ' + $conversionsChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-google-adwords-conversions-change-direction', curInst.data['period']['conversions_change_direction'], false );
		setDataPointChangeClass( 'skystats-google-adwords-conversions-change', curInst.data['period']['conversions_change_direction'], false );

		if ( 'detail' === curInst.viewName ) {
			// Impressions
			jQuery( '#skystats-google-adwords-impressions' ).html( curInst.data['period']['impressions'] );
			jQuery( '#skystats-google-adwords-impressions-change' ).html( curInst.data['period']['impressions_change'] );
			var $impressionsChangeInfo = jQuery( '#skystats-google-adwords-impressions-change-info' );
			curInst.changeTooltipContent( $impressionsChangeInfo, curInst.data['period']['previous_impressions'] + ' ' + $impressionsChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-google-adwords-impressions-change-direction', curInst.data['period']['impressions_change_direction'], false );
			setDataPointChangeClass( 'skystats-google-adwords-impressions-change', curInst.data['period']['impressions_change_direction'], false );

			// Click Through Rate
			jQuery( '#skystats-google-adwords-click-through-rate' ).html( curInst.data['period']['click_through_rate'] );
			jQuery( '#skystats-google-adwords-click-through-rate-change' ).html( curInst.data['period']['click_through_rate_change'] );
			var $clickThroughRateChangeInfo = jQuery( '#skystats-google-adwords-click-through-rate-change-info' );
			curInst.changeTooltipContent( $clickThroughRateChangeInfo, curInst.data['period']['previous_click_through_rate'] + '% ' + $clickThroughRateChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-google-adwords-click-through-rate-change-direction', curInst.data['period']['click_through_rate_change_direction'], false );
			setDataPointChangeClass( 'skystats-google-adwords-click-through-rate-change', curInst.data['period']['click_through_rate_change_direction'], false );

			// Average Cost Per Conversion
			jQuery( '#skystats-google-adwords-avg-cost-per-conversion' ).html( curInst.data['period']['avg_cost_per_conversion'] );
			jQuery( '#skystats-google-adwords-avg-cost-per-conversion-currency').html( currencySymbol );
			jQuery( '#skystats-google-adwords-avg-cost-per-conversion-change' ).html( curInst.data['period']['avg_cost_per_conversion_change'] );
			var $avgCostPerConversionChangeInfo = jQuery( '#skystats-google-adwords-avg-cost-per-conversion-change-info' );
			curInst.changeTooltipContent( $avgCostPerConversionChangeInfo, currencySymbol + curInst.data['period']['previous_average_cost_per_conversion'] + ' ' + $avgCostPerConversionChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-google-adwords-avg-cost-per-conversion-change-direction', curInst.data['period']['avg_cost_per_conversion_change_direction'], true );
			setDataPointChangeClass( 'skystats-google-adwords-avg-cost-per-conversion-change', curInst.data['period']['avg_cost_per_conversion_change_direction'], true );

			// Bounce Rate
			jQuery( '#skystats-google-adwords-bounce-rate' ).html( curInst.data['period']['bounce_rate'] );
			jQuery( '#skystats-google-adwords-bounce-rate-change' ).html( curInst.data['period']['bounce_rate_change'] );
			var $bounceRateChangeInfo = jQuery( '#skystats-google-adwords-bounce-rate-change-info' );
			curInst.changeTooltipContent( $bounceRateChangeInfo, curInst.data['period']['previous_bounce_rate'] + '% ' + $bounceRateChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-google-adwords-bounce-rate-change-direction', curInst.data['period']['bounce_rate_change_direction'], true );
			setDataPointChangeClass( 'skystats-google-adwords-bounce-rate-change', curInst.data['period']['bounce_rate_change_direction'], true );

			// Account-level campaigns
			(function() {
				if ( null == curInst.data['period']['account_level_campaigns'] ) {
					return;
				}
				var $table = jQuery( '#skystats-google-adwords-account-level-campaigns-table' ),
					$columnContent = $table.parent(),
					$column = $columnContent.parent(),
					$tbody = $table.find( 'tbody' ),
					data = curInst.data['period']['account_level_campaigns'],
					rows = '';

				if ( ! Object.keys( data ).length ) {
					$table.hide();
					curInst.$accountLevelCampaignsTableErrorContainer.show();
				} else {
					$tbody.html( '' );
					jQuery.each( data, function ( campaignName, campaignData ) {
						rows += '<tr>' +
							'<td>' + campaignName + '</td>' +
							'<td>' + campaignData['clicks'] + '</td>' +
							'<td>' + campaignData['impressions'] + '</td>' +
							'<td>' + campaignData['click_through_rate'] + '%</td>' +
							'<td>' + currencySymbol + campaignData['average_cost_per_click'] + '</td>' +
							'<td>' + currencySymbol + campaignData['cost'] + '</td>' +
							'<td>' + campaignData['conversions'] + '</td>' +
							'</tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				$columnContent.show();
				$column.show();
			})();

			// Account-level top keyword performance
			(function() {
				if ( null == curInst.data['period']['account_level_top_keyword_performance'] ) {
					return;
				}
				var $table = jQuery( '#skystats-google-adwords-account-level-top-keyword-performance-table' ),
					$columnContent = $table.parent(),
					$column = $columnContent.parent(),
					$tbody = $table.find( 'tbody' ),
					data = curInst.data['period']['account_level_top_keyword_performance'],
					rows = '';

				if ( ! data.length ) {
					$table.hide();
					curInst.$accountLevelTopKeywordPerformanceTableErrorContainer.show();
				} else {
					$tbody.html( '' );
					jQuery.each( data, function ( i, v ) {
						rows += '<tr>' +
							'<td>' + v['keyword'] + '</td>' +
							'<td>' + v['campaign_name'] + '</td>' +
							'<td>' + v['clicks'] + '</td>' +
							'<td>' + v['impressions'] + '</td>' +
							'<td>' + v['click_through_rate'] + '%</td>' +
							'<td>' + currencySymbol + v['average_cost_per_click'] + '</td>' +
							'<td>' + currencySymbol + v['cost'] + '</td>' +
							'<td>' + v['conversions'] + '</td>' +
							'</tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				$columnContent.show();
				$column.show();
			})();
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
GoogleAdwords.prototype.loadSettingsTabData = function( requestType ) {
	this.$integrationErrorContainer.fadeOut();
	this.hideChartFigure();
	var curInst = this,
		$fadeOutObjects = ( 'mashboard' === this.viewName ) ?
			this.$dataPointsContainer.add( this.$chartContainer ).add( this.$topDataErrorContainers ) :
			this.$dataPointsContainer.add( this.$chartContainer ).add( this.$dataTableColumnsContent ).add( this.$topDataErrorContainers );

	$fadeOutObjects.fadeOut( 400 ).promise().done( function () {
		curInst.$loadingContainer.fadeIn( 400, function () {

			var accountsDeferredObject = curInst.getAccountsDeferredObject();

			if ( null != curInst.selectedCustomerId ) {
				jQuery.when( accountsDeferredObject, curInst.getCampaignsDeferredObject(curInst.selectedCustomerId) ).done( function( accountResponseJSON, campaignResponseJSON ) {
					var accountResponse = jQuery.parseJSON( accountResponseJSON[0] ),
						campaignResponse = jQuery.parseJSON( campaignResponseJSON[0] );
					curInst.displaySettingsTabData( accountResponse, campaignResponse );
				});
			} else {
				jQuery.when( accountsDeferredObject ).done( function( accountResponseJSON ) {
					var accountResponse = jQuery.parseJSON( accountResponseJSON[0] );
					curInst.displaySettingsTabData( accountResponse, {} );
				});
			}
		});
	});
};

GoogleAdwords.prototype.getAccountsDeferredObject = function() {
	return jQuery.get( ajaxurl, {
		action: 'skystats_ajax_google_adwords_api_query',
		query: 'get_accounts'
	});
};
GoogleAdwords.prototype.getCampaignsDeferredObject = function(customerId) {
	return jQuery.get( ajaxurl, {
		action: 'skystats_ajax_google_adwords_api_query',
		query: 'get_campaigns',
		customer_id: customerId
	});
};

/**
 * Display settings tab data.
 *
 * @param {Object} accountResponse Settings tab response data.
 * @param {Object} campaignResponse Settings tab response data.
 */
GoogleAdwords.prototype.displaySettingsTabData = function( accountResponse, campaignResponse ) {
	var curInst = this;
	this.$integrationErrorContainer.fadeOut();
	curInst.$loadingContainer.fadeOut( 400, function () {

		// Accounts
		if ( 'error' === accountResponse['responseType'] ) {
			var context = accountResponse['responseContext'];
			if ( 'authorization_required' === context ) {
				curInst.$settingsTabAuthorizeSection.fadeIn();
			} else {
				if ( curInst.translations['google_adwords_api_errors'].hasOwnProperty( context ) ) {
					curInst.$integrationErrorContainer.find('>p').html(curInst.translations['google_adwords_api_errors'][context]);
					curInst.$integrationErrorContainer.fadeIn();
				}
				curInst.$settingsTabDeauthorizeSection.fadeIn();
			}
		} else {

			if ('success' === accountResponse['responseType']) {

				var selectHTML = '',
					$accountSelect = jQuery('#skystats-google-adwords-account-selection'),
					accountsArr = [];
				if ( jQuery.isArray( accountResponse['data'] ) ) {
					jQuery.each(accountResponse['data'], function (i, v) {
						accountsArr.push(v);
					});
					accountsArr.sort( function ( a, b ) {
						var aName = a.name.toLowerCase(),
							bName = b.name.toLowerCase();
						return ( ( aName < bName ) ? -1 : ( ( aName > bName ) ? 1 : 0 ) );
					});
				} else {
					accountsArr = [
						{ id: accountResponse['data']['id'], name: accountResponse['data']['name'] }
					];
				}
				var accountsLen = accountsArr.length;
				selectHTML += '<option value="select">Select Account</option>';
				for (var i = 0; i < accountsLen; ++i) {
					var customerSelected = ( curInst.selectedCustomerId == accountsArr[i]['id'] ) ? 'selected="selected"' : '',
						customerId = accountsArr[i]['id'].toString(),
						customerIdWithDash = customerId.slice( 0, 3 ) + '-' + customerId.slice( 3, 6 ) + '-' + customerId.slice( 6 );
					selectHTML += '<option value="' + customerId + '" ' + customerSelected + '>' + accountsArr[i]['name'] + ' (' + customerIdWithDash + ')</option>';
				}
				$accountSelect.html(selectHTML);
				curInst.$settingsTabAccountSelectionSection.fadeIn();
				$accountSelect.chosen({
					max_selected_options: 1,
					width: '100%'
				});

				$accountSelect.off('change');
				$accountSelect.on( 'change', function() {
					var customerId = jQuery( this).val();
					if ( 'select' == customerId || customerId == curInst.selectedCustomerId ) {
						return;
					}
					curInst.$settingsTabCampaignSelectionSection.fadeOut( 400, function() {
						curInst.$campaignSectionLoadingIcon.fadeIn( 400, function() {
							curInst.selectedCustomerId = customerId;
							jQuery.get(ajaxurl, {
								action: 'skystats_ajax_google_adwords_api_query',
								query: 'save_customer_id',
								google_adwords_selected_customer_id: customerId
							}, function () {
								jQuery.when(curInst.getCampaignsDeferredObject(customerId)).done(function (acampaignResponseJSON) {
									var acampaignResponse = jQuery.parseJSON(acampaignResponseJSON);
									curInst.displaySettingsTabData(accountResponse, acampaignResponse);
								});
							});
						});
					});
				});
			}

			// Campaigns
			if ( campaignResponse.hasOwnProperty('responseType') && 'error' === campaignResponse['responseType'] ) {
				curInst.$campaignSectionLoadingIcon.fadeOut();
				curInst.$settingsTabCampaignSelectionSection.fadeOut();
				var context = campaignResponse['responseContext'];
				if ( curInst.translations['google_adwords_api_errors'].hasOwnProperty( context ) ) {
					curInst.$integrationErrorContainer.find( '>p').html( curInst.translations['google_adwords_api_errors'][ context ] );
					curInst.$integrationErrorContainer.fadeIn();
				}
			} else if (campaignResponse.hasOwnProperty('responseType') && 'success' === campaignResponse['responseType']) {

				curInst.$campaignSectionLoadingIcon.fadeOut();

				var $campaignSelect = jQuery('#skystats-google-adwords-campaign-selection'),
					$saveCampaignId = jQuery('#skystats-google-adwords-save-campaign');

				var campaignSelectHTML = '';

				var campaignSelected = ( curInst.selectedCampaignId == 'allData' ) ? 'selected="selected"' : '';
				campaignSelectHTML += '<option value="allData" ' + campaignSelected + '>All Data</option>';

				$campaignSelect.html(campaignSelectHTML);

				curInst.$campaignSectionLoadingIcon.fadeOut( 400, function() {
					$saveCampaignId.show();
					curInst.$campaignSectionDescription.show();
					// When a campaign is saved
					$saveCampaignId.off('click');
					$saveCampaignId.one('click', function (e) {
						e.preventDefault();
						curInst.$integrationErrorContainer.fadeOut();
						curInst.disableSettingsIcon();
						var campaignId = $campaignSelect.val();
						curInst.selectedCampaignId = campaignId;
						var customerId = $accountSelect.val();
						// Customer ids the same (load & display data)
						if (customerId == curInst.selectedCustomerId) {
							curInst.$settingsTabSections.fadeOut(400).promise().done(function () {
								curInst.$loadingContainer.fadeIn(400, function () {
									jQuery.get(ajaxurl, {
										action: 'skystats_ajax_google_adwords_api_query',
										query: 'save_campaign_id',
										google_adwords_selected_campaign_id: campaignId
									}, function () {
										curInst.data = null;
										if ('detail' === curInst.viewName) {
											curInst.loadedAllData = false;
										}
										curInst.loadDataTabData();
									});
								});
							});
						} else { // customer ids not the same, load new campaigns
							curInst.selectedCustomerId = customerId;
							var fadeOutObjects = $campaignSelect.add($saveCampaignId).add(curInst.$campaignSectionDescription);
							fadeOutObjects.fadeOut(400).promise().done(function () {
								curInst.$campaignSectionLoadingIcon.fadeIn(400, function () {
									jQuery.get(ajaxurl, {
										action: 'skystats_ajax_google_adwords_api_query',
										query: 'save_campaign_id',
										google_adwords_selected_campaign_id: campaignId,
										google_adwords_selected_customer_id: customerId
									}, function () {
										jQuery.when(curInst.getAccountsDeferredObject(), curInst.getCampaignsDeferredObject(curInst.selectedCustomerId)).done(function (accountResponseJSON, campaignResponseJSON) {
											var accountResponse = jQuery.parseJSON(accountResponseJSON[0]),
												campaignResponse = jQuery.parseJSON(campaignResponseJSON[0]);
											curInst.displaySettingsTabData(accountResponse, campaignResponse);
										});
									});
								});
							});
						}
					});
					curInst.$settingsTabCampaignSelectionSection.fadeIn();
					$campaignSelect.chosen({
						max_selected_options: 1,
						width: '100%'
					});
					$campaignSelect.trigger('chosen:updated');
				});
			}
			curInst.$settingsTabDeauthorizeSection.fadeIn();
		}

		curInst.viewingSettingsTab = true;
		curInst.loadingSettingsTabData = false;

		// Enable settings icon if there is data
		if ( null != curInst.data ) {
			curInst.enableSettingsIcon();
		}

		if ( curInst.$settingsTabAuthorizeSection.is( ':visible' ) ) {
			var $authorize = jQuery( '#skystats-google-adwords-authorize' );
			$authorize.off( 'click' );
			$authorize.on( 'click', function( event ) {
				event.preventDefault();
				if ( null != curInst.authPopupWindowIntervalId ) {
					clearInterval( curInst.authPopupWindowIntervalId );
				}
				curInst.createPopupWindow( curInst.authPopupWindowURL, 'GoogleAdwordsAuthPopup', 900, 500, null );
				curInst.authPopupWindowIntervalId = setInterval( function() {
					try {
						if ( null == curInst.popupWindow || curInst.popupWindow.closed ) {
							clearInterval( curInst.authPopupWindowIntervalId );
						}
						if ( curInst.popupWindow.location.hasOwnProperty( 'href' ) && ( curInst.authPopupWindowCompleteURL === curInst.popupWindow.location.href || curInst.authPopupWindowCompleteURL + '#' === curInst.popupWindow.location.href ) ) {
							curInst.popupWindow.close();
							clearInterval( curInst.authPopupWindowIntervalId );
							curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
								curInst.loadSettingsTabData( 'fresh' );
							});
						}
					} catch (e) {
					}
				}, 100 );
			});
		}

		if ( curInst.$settingsTabDeauthorizeSection.is( ':visible' ) ) {
			var $deauthorize = jQuery( '#skystats-google-adwords-deauthorize' );
			$deauthorize.off( 'click' );
			$deauthorize.on( 'click', function( event ) {
				event.preventDefault();
				curInst.$integrationErrorContainer.fadeOut();
				curInst.disableSettingsIcon();
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						jQuery.get( ajaxurl, {
							action: 'skystats_ajax_google_adwords_api_query',
							query: 'deauthorize'
						}, function() {
							curInst.data = null;
							curInst.viewingSettingsTab = false;
							curInst.loadingSettingsTabData = true;
							curInst.loadSettingsTabData( 'fresh' );
						});
					} );
				});
			});
		}
	});
};

GoogleAdwords.prototype.resizeChart = function() {
	if ( null != this.data ) {
		setGlobalPossibleMarkers( null );
		this.chartInstance.shutdown();
		this.chartData[0].data = getChartData(this.data['chart_data']['clicks'], this.$chart, this, 'daily');
		this.chartData[1].data = getChartData(this.data['chart_data']['avg_cost_per_click'], this.$chart, this, 'daily');
		this.chartData[1].yaxis = 2;
		this.chartInstance = this.$chart.plot(this.chartData, this.chartOptions).data('plot');
	}
};

/*
 * MailChimp
 */
function MailChimp( viewName ) {

	Integration.call(this, viewName);

	this.integrationName = 'MailChimp';

	this.$loadingContainer = jQuery('#skystats-mailchimp-loading-container');

	this.translations = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['trans'] :
		skystats_mailchimp['trans'];

	this.$integrationErrorContainer = jQuery('#skystats-mailchimp-error-container');

	/* Data */
	this.data = null; // Contains data for the chart and data points.
	this.loadingDataTabData = false;
	this.viewingDataTab = false;
	this.loadedAllData = false; // Detail page specific. Whether all data was successfully loaded or at least attempted.
	this.$dataTableColumnsContent = jQuery('.skystats-data-table-column-content');

	/* Data Points */
	this.$dataTabContent = jQuery('#skystats-mailchimp-data-tab-content');
	this.$dataPointsContainer = jQuery('#skystats-mailchimp-data-points-container');

	/* Chart */
	this.chartInstance = null;
	this.chartID = 'skystats-mailchimp-chart';
	this.$chart = jQuery('#' + this.chartID);
	this.$chartContainer = jQuery('#skystats-mailchimp-chart-container');
	this.chartData = [
		{
			color: '#FEBE12',
			data: [],
			label: 'Unique Opens'
		},
		{
			color: '#4f9ad8',
			data: [],
			label: 'Unique Clicks'
		}
	];

	/* Grid */
	this.gridIconSelector = '#skystats-mailchimp-grid-icon';
	this.$gridIcon = jQuery(this.gridIconSelector);
	this.gridIconContentSelector = '#skystats-mailchimp-grid-icon-content';
	this.gridIconChildrenContent = jQuery('#skystats-mailchimp-grid-icon-content').html();
	this.gridDisplayed = false;
	this.gridClickEventName = 'click.mailchimp_grid';
	this.gridMouseoverEventName = 'mouseover.mailchimp_grid';
	this.gridMouseleaveEventName = 'mouseleave.mailchimp_grid';
	this.gridMouseoutEventName = 'mouseout.mailchimp_grid';

	/* Settings */
	this.loadingSettingsTabData = false;
	this.viewingSettingsTab = false;
	this.$settingsIcon = jQuery('#skystats-mailchimp-settings-icon');
	this.$settingsTabSections = jQuery('.skystats-mailchimp-settings-tab-section');
	this.$settingsTabAuthorizeSection = jQuery('#skystats-mailchimp-settings-authorize-section');
	this.$settingsTabDeauthorizeSection = jQuery('#skystats-mailchimp-settings-deauthorize-section');
	this.$settingsTabValidAccessTokenSection = jQuery( '#skystats-mailchimp-settings-valid-access-token-section' );
	this.$settingsTabInvalidAccessTokenSection = jQuery( '#skystats-mailchimp-settings-invalid-access-token-section' );

	this.settingsClickEventName = 'click.mailchimp_settings';

	if ('detail' === this.viewName) {
		this.$dataTableColumns = jQuery( '.skystats-service-detail-data-table-column' );
		this.$topCampaignsTableErrorContainer = jQuery( '#skystats-mailchimp-top-campaigns-data-error-container' );
		this.$topListsTableErrorContainer = jQuery( '#skystats-mailchimp-top-lists-data-error-container' );
	}

	this.authPopupWindowIntervalId = null;
	// URL popup window first directs to authorize the user.
	this.authPopupWindowURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['mailchimp']['auth_popup_window_url'] :
		skystats_mailchimp['auth_popup_window_url'];
	// URL popup window directs to when authorization is complete.
	this.authPopupWindowCompleteURL = ( 'mashboard' === this.viewName ) ?
		skystats_mashboard['auth_popup_window_complete_url'] :
		skystats_mailchimp['auth_popup_window_complete_url'];
}
MailChimp.prototype = createObject( Integration.prototype );
MailChimp.prototype.constructor = MailChimp;

/**
 * Load data for the data tab.
 */
MailChimp.prototype.loadDataTabData = function() {
	this.hideChartFigure();
	this.$integrationErrorContainer.fadeOut();
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
					jQuery.get( ajaxurl, {
						action: 'skystats_ajax_mailchimp_api_query',
						query: 'get_mashboard_view_data',
						start_date: curInst.startDate,
						end_date: curInst.endDate
					}, function ( response ) {
						response = jQuery.parseJSON( response );
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
						jQuery.get( ajaxurl, {
							action: 'skystats_ajax_mailchimp_api_query',
							query: 'get_detail_view_data',
							start_date: curInst.startDate,
							end_date: curInst.endDate
						}, function ( response ) {
							response = jQuery.parseJSON( response );
							if ( null != response.data ) {
								curInst.data = response.data;
								curInst.displayDataTabData();
							} else {
								curInst.loadSettingsTabData( 'fresh' );
							}
						} );
					}
				}
			});
		});
};

/**
 * Display the data for the data tab.
 */
MailChimp.prototype.displayDataTabData = function() {
	var curInst = this;
	this.$integrationErrorContainer.fadeOut();
	this.$loadingContainer.fadeOut( 400, function() {

		curInst.showChartFigure();
		curInst.$dataTabContent.fadeIn();
		curInst.$chartContainer.show();

		// Do we need to destroy the chart?
		if ( null !== curInst.chartInstance ) {
			curInst.chartInstance.shutdown();
		}
		// Create chart
		curInst.chartData[0].data = getChartData( curInst.data['chart_data']['unique_opens'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartData[1].data = getChartData( curInst.data['chart_data']['unique_clicks'], curInst.$chart, curInst, curInst.frequency );
		curInst.chartOptions.tooltipOpts.content = function( label, x, y, item ) {
			var value = y.toString();
			if ( 'MailChimp' === curInst.integrationName  ) {
				if ( value > 0 ) {
					var tooltipContent = '<strong>' + value + '</strong><br>' + x;
					jQuery.each( curInst.data['period']['date_campaigns'], function( campaignDate, campaignData ) {
						campaignDate = getMonthDayFormatted( campaignDate );
						var campaignsCount = 0;
						if ( campaignDate === x )  {
							jQuery.each ( campaignData, function( campaignId, campaignName ) {
								if ( campaignsCount >= 3 ) {
									tooltipContent += '...';
								} else {
									tooltipContent += '<br>' + campaignName + ' (' + campaignId + ')';
									campaignsCount++;
								}
							});
						}
					});
					return tooltipContent;
				}
			}
			return '<strong>' + value + '</strong><br>' + x;
		};
		curInst.chartInstance = curInst.$chart.plot( curInst.chartData, curInst.chartOptions ).data( 'plot' );
		curInst.$dataPointsContainer.show();

		// Opens (Unique)
		jQuery( '#skystats-mailchimp-unique-opens' ).html( curInst.data['period']['unique_opens'] );
		jQuery( '#skystats-mailchimp-unique-opens-change' ).html( curInst.data['period']['unique_opens_change'] );
		var $opensChangeInfo = jQuery( '#skystats-mailchimp-unique-opens-change-info' );
		curInst.changeTooltipContent( $opensChangeInfo, curInst.data['period']['previous_unique_opens'] + ' ' + $opensChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-mailchimp-unique-opens-change-direction', curInst.data['period']['unique_opens_change_direction'], false );
		setDataPointChangeClass( 'skystats-mailchimp-unique-opens-change', curInst.data['period']['unique_opens_change_direction'], false );

		// Clicks (Unique)
		jQuery( '#skystats-mailchimp-unique-clicks' ).html( curInst.data['period']['unique_clicks'] );
		jQuery( '#skystats-mailchimp-unique-clicks-change' ).html( curInst.data['period']['unique_clicks_change'] );
		var $clicksChangeInfo = jQuery( '#skystats-mailchimp-unique-clicks-change-info' );
		curInst.changeTooltipContent( $clicksChangeInfo, curInst.data['period']['previous_unique_clicks'] + ' ' + $clicksChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-mailchimp-unique-clicks-change-direction', curInst.data['period']['unique_clicks_change_direction'], false );
		setDataPointChangeClass( 'skystats-mailchimp-unique-clicks-change', curInst.data['period']['unique_clicks_change_direction'], false );

		// Unsubscribed
		jQuery( '#skystats-mailchimp-unsubscribed' ).html( curInst.data['period']['unsubscribed'] );
		jQuery( '#skystats-mailchimp-unsubscribed-change' ).html( curInst.data['period']['unsubscribed_change'] );
		var $unsubscribedChangeInfo = jQuery( '#skystats-mailchimp-unsubscribed-change-info' );
		curInst.changeTooltipContent( $unsubscribedChangeInfo, curInst.data['period']['previous_unsubscribed'] + ' ' + $unsubscribedChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-mailchimp-unsubscribed-change-direction', curInst.data['period']['unsubscribed_change_direction'], true );
		setDataPointChangeClass( 'skystats-mailchimp-unsubscribed-change', curInst.data['period']['unsubscribed_change_direction'], true );

		// Subscribers
		jQuery( '#skystats-mailchimp-subscribers' ).html( curInst.data['period']['subscribers'] );
		jQuery( '#skystats-mailchimp-subscribers-change' ).html( curInst.data['period']['subscribers_change'] );
		var $subscribersChangeInfo = jQuery( '#skystats-mailchimp-subscribers-change-info' );
		curInst.changeTooltipContent( $subscribersChangeInfo, curInst.data['period']['previous_subscribers'] + ' ' + $subscribersChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-mailchimp-subscribers-change-direction', curInst.data['period']['subscribers_change_direction'], false );
		setDataPointChangeClass( 'skystats-mailchimp-subscribers-change', curInst.data['period']['subscribers_change_direction'], false );

		// Open Rate
		jQuery( '#skystats-mailchimp-open-rate' ).html( curInst.data['period']['open_rate'] );
		jQuery( '#skystats-mailchimp-open-rate-change' ).html( curInst.data['period']['open_rate_change'] );
		var $openRateChangeInfo = jQuery( '#skystats-mailchimp-open-rate-change-info' );
		curInst.changeTooltipContent( $openRateChangeInfo, curInst.data['period']['previous_open_rate'] + '% ' + $openRateChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-mailchimp-open-rate-change-direction', curInst.data['period']['open_rate_change_direction'], false );
		setDataPointChangeClass( 'skystats-mailchimp-open-rate-change', curInst.data['period']['open_rate_change_direction'], false );

		// Click Rate
		jQuery( '#skystats-mailchimp-click-rate' ).html( curInst.data['period']['click_rate'] );
		jQuery( '#skystats-mailchimp-click-rate-change' ).html( curInst.data['period']['click_rate_change'] );
		var $clickRateChangeInfo = jQuery( '#skystats-mailchimp-click-rate-change-info' );
		curInst.changeTooltipContent( $clickRateChangeInfo, curInst.data['period']['previous_click_rate'] + '% ' + $clickRateChangeInfo.attr( 'data-tooltip-backup' ) );
		setDataPointChangeDirectionClass( 'skystats-mailchimp-click-rate-change-direction', curInst.data['period']['click_rate_change_direction'], false );
		setDataPointChangeClass( 'skystats-mailchimp-click-rate-change', curInst.data['period']['click_rate_change_direction'], false );

		if ( 'detail' === curInst.viewName ) {

			// Industry Average Open Rate
			jQuery( '#skystats-mailchimp-industry-average-open-rate' ).html( curInst.data['period']['industry_average_open_rate'] );
			jQuery( '#skystats-mailchimp-industry-average-open-rate-change' ).html( curInst.data['period']['industry_average_open_rate_change'] );
			var $industryAvgOpenRateChangeInfo = jQuery( '#skystats-mailchimp-industry-average-open-rate-change-info' );
			curInst.changeTooltipContent( $industryAvgOpenRateChangeInfo, curInst.data['period']['previous_industry_average_open_rate'] + '% ' + $industryAvgOpenRateChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-mailchimp-industry-average-open-rate-change-direction', curInst.data['period']['industry_average_open_rate_change_direction'], false );
			setDataPointChangeClass( 'skystats-mailchimp-industry-average-open-rate-change', curInst.data['period']['industry_average_open_rate_change_direction'], false );

			// Bounces
			jQuery( '#skystats-mailchimp-bounces' ).html( curInst.data['period']['bounces'] );
			jQuery( '#skystats-mailchimp-bounces-change' ).html( curInst.data['period']['bounces_change'] );
			var $bouncesChangeInfo = jQuery( '#skystats-mailchimp-bounces-change-info' );
			curInst.changeTooltipContent( $bouncesChangeInfo, curInst.data['period']['previous_bounces'] + ' ' + $bouncesChangeInfo.attr( 'data-tooltip-backup' ) );
			setDataPointChangeDirectionClass( 'skystats-mailchimp-bounces-change-direction', curInst.data['period']['bounces_change_direction'], true );
			setDataPointChangeClass( 'skystats-mailchimp-bounces-change', curInst.data['period']['bounces_change_direction'], true );

			// Top Campaigns
			(function() {
				if ( null == curInst.data['period']['top_campaigns'] ) {
					return;
				}
				var $table = jQuery( '#skystats-mailchimp-top-campaigns-table' ),
					$columnContent = $table.parent(),
					$column = $columnContent.parent(),
					$tbody = $table.find( 'tbody' ),
					data = curInst.data['period']['top_campaigns'],
					rows = '';

				if ( ! Object.keys( data ).length ) {
					$table.hide();
					curInst.$topCampaignsTableErrorContainer.show();
				} else {
					$tbody.html( '' );
					jQuery.each( data, function ( index, campaignData ) {
						rows += '<tr>' +
							'<td>' + campaignData['name'] + ' (' + campaignData['id'] + ')</td>' +
							'<td>' + campaignData['unique_opens'] + '</td>' +
							'<td>' + campaignData['unique_clicks'] + '</td>' +
							'<td>' + campaignData['unsubscribed'] + '</td>' +
							'<td>' + campaignData['open_rate'] + '%</td>' +
							'<td>' + campaignData['click_rate'] + '%</td>' +
							'<td>' + campaignData['send_time'] + '</td>' +
							'</tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				$columnContent.show();
				$column.show();
			})();

			// Top Lists
			(function() {
				if ( null == curInst.data['period']['top_lists'] ) {
					return;
				}
				var $table = jQuery( '#skystats-mailchimp-top-lists-table' ),
					$columnContent = $table.parent(),
					$column = $columnContent.parent(),
					$tbody = $table.find( 'tbody' ),
					data = curInst.data['period']['top_lists'],
					rows = '';

				if ( ! data.length ) {
					$table.hide();
					curInst.$topListsTableErrorContainer.show();
				} else {
					$tbody.html( '' );
					jQuery.each( data, function ( i, v ) {
						rows += '<tr>' +
							'<td>' + v['name'] + ' (' + v['id'] + ')</td>' +
							'<td>' + v['open_rate'] + '%</td>' +
							'<td>' + v['click_rate'] + '%</td>' +
							'<td>' + v['subscribers'] + '</td>' +
							'<td>' + v['average_subscribe_rate'] + '%</td>' +
							'<td>' + v['average_unsubscribe_rate'] + '%</td>' +
							'</tr>';
					} );
					jQuery( rows ).appendTo( $tbody );
					$table.show();
				}
				$columnContent.show();
				$column.show();
			})();
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
MailChimp.prototype.loadSettingsTabData = function( requestType ) {
	if ( 'detail' === this.viewName ) {
		this.$dataTableColumns.fadeOut();
	}
	this.$integrationErrorContainer.fadeOut();
	this.hideChartFigure();
	var curInst = this,
		$fadeOutObjects = ( 'mashboard' === this.viewName ) ?
			this.$dataPointsContainer.add( this.$chartContainer ) :
			this.$dataPointsContainer.add( this.$chartContainer ).add( this.$dataTableColumnsContent );

	$fadeOutObjects.fadeOut( 400 ).promise().done( function () {
		curInst.$loadingContainer.fadeIn( 400, function () {
			jQuery.get( ajaxurl, {
				action: 'skystats_ajax_mailchimp_api_query',
				query: 'get_status',
				request_type: requestType
			}, function ( response ) {
				response = jQuery.parseJSON( response );
				curInst.displaySettingsTabData( response );
			});
		});
	});
};

/**
 * Display settings tab data.
 */
MailChimp.prototype.displaySettingsTabData = function( response ) {
	var curInst = this;
	this.$integrationErrorContainer.fadeOut();
	curInst.$loadingContainer.fadeOut( 400, function () {

		if ( 'success' === response['responseType'] ) {
			switch ( response['responseContext'] ) {
				case 'valid_access_token':
					curInst.$settingsTabValidAccessTokenSection.fadeIn();
					curInst.$settingsTabDeauthorizeSection.fadeIn();
					break;
			}
		} else {
			switch ( response['responseContext'] ) {
				case 'authorization_required':
					curInst.$settingsTabAuthorizeSection.fadeIn();
					break;
				case 'invalid_access_token':
					curInst.$settingsTabInvalidAcccessTokenSection.fadeIn();
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

		// Setup is visible and clicked
		if ( curInst.$settingsTabAuthorizeSection.is( ':visible' ) ) {
			var $authorize = jQuery( '#skystats-mailchimp-authorize' );
			$authorize.off( 'click' );
			$authorize.on( 'click', function( event ) {
				event.preventDefault();
				if ( null != curInst.authPopupWindowIntervalId ) {
					clearInterval( curInst.authPopupWindowIntervalId );
				}
				curInst.createPopupWindow( curInst.authPopupWindowURL, 'MailChimpAuthPopup', 900, 500, null );
				curInst.authPopupWindowIntervalId = setInterval( function() {
					try {
						if ( null == curInst.popupWindow || curInst.popupWindow.closed ) {
							clearInterval( curInst.authPopupWindowIntervalId );
						}
						if ( curInst.popupWindow.location.hasOwnProperty( 'href' ) && ( curInst.authPopupWindowCompleteURL === curInst.popupWindow.location.href || curInst.authPopupWindowCompleteURL + '#' === curInst.popupWindow.location.href ) ) {
							curInst.popupWindow.close();
							clearInterval( curInst.authPopupWindowIntervalId );
							curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
								curInst.loadDataTabData();
							});
						}
					} catch (e) {
					}
				}, 100 );
			});
		}

		// Deauthorize is visible & clicked
		if ( curInst.$settingsTabDeauthorizeSection.is( ':visible' ) ) {
			var $deauthorize = jQuery( '#skystats-mailchimp-deauthorize' );
			$deauthorize.off( 'click' );
			$deauthorize.on( 'click', function( event ) {
				event.preventDefault();
				curInst.$integrationErrorContainer.fadeOut();
				curInst.disableSettingsIcon();
				curInst.$settingsTabSections.fadeOut( 400 ).promise().done( function() {
					curInst.$loadingContainer.fadeIn( 400, function () {
						jQuery.get( ajaxurl, {
							action: 'skystats_ajax_mailchimp_api_query',
							query: 'deauthorize'
						}, function() {
							curInst.data = null;
							curInst.viewingSettingsTab = false;
							curInst.loadingSettingsTabData = true;
							curInst.loadSettingsTabData( 'fresh' );
						});
					} );
				});
			});
		}
	});
};

MailChimp.prototype.resizeChart = function() {
	if ( null != this.data ) {
		this.chartInstance.shutdown();
		this.chartData[0].data = getChartData(this.data['chart_data']['unique_opens'], this.$chart, this, 'daily');
		this.chartData[1].data = getChartData(this.data['chart_data']['unique_clicks'], this.$chart, this, 'daily');
		this.chartInstance = this.$chart.plot(this.chartData, this.chartOptions).data('plot');
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
		case 'twitter':
			return new Twitter( viewName );
			break;
		case 'googleAdwords':
			return new GoogleAdwords( viewName );
			break;
		case 'mailchimp':
			return new MailChimp( viewName );
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
 * @param {string} viewName                  The type of view this integration will be used for (mashboard or detail currently supported).
 *
 * @returns {Object[]}
 */
function getIntegrations( viewName ) {
	var integrations = [
		new GoogleAnalytics( viewName ),
		new Facebook( viewName ),
		new Twitter( viewName ),
		new GoogleAdwords( viewName ),
		new MailChimp( viewName )
	];

	return integrations;
}

function initTooltips() {
	jQuery( '.skystats-tooltip' ).each( function() {
		var $tooltip = jQuery( this );
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

	var sortableElementSelector = '.skystats-cards-column',
		$sortableElement = jQuery( sortableElementSelector );

	$sortableElement.sortable({
		connectWith: sortableElementSelector,
		cursor: 'move',
		forcePlaceholderSize: true,
		handle: '.skystats-card-drag-icon',
		placeholder: 'skystats-card-placeholder',
		revert: 400,
		/* Triggered when sorting stops */
		stop: function () {
			var data = {};
			$sortableElement.each( function  (i, obj ) {
				var id = jQuery( obj ).attr( 'id' );
				data[ id ] = jQuery( obj ).sortable( 'toArray' );
			});
			for ( var i = 0; i < integrationsLen; ++i ) {
				integrations[ i ].resizeChart();
			}
			jQuery.post( ajaxurl, {
				'action': 'skystats_ajax_save_mashboard_card_positions',
				'data'  : data
			});
		}
	});
}