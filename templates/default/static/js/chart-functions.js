var gPossibleMarkers = null;

/**
 * Set the class of a data point change direction element.
 *
 * @param {string} elementId The element Id.
 * @param {string} direction The change direction (positive or negative).
 * @param {bool}   opposite  Whether the change should be displayed in opposite direction e.g. if true, positive changes
 *                           will be displayed as a negative change and vice versa. This only applies to positve and negative
 *                           changes.
 */
function setDataPointChangeDirectionClass( elementId, direction, opposite ) {
	var classVal = '';
	switch ( direction ) {
		case 'positive':
			if ( opposite ) {
				classVal = 'skystats-data-point-change-direction-increase-negative';
			} else {
				classVal = 'skystats-data-point-change-direction skystats-data-point-change-direction-positive';
			}
			break;
		case 'negative':
			if ( opposite ) {
				classVal = 'skystats-data-point-change-direction-decrease-positive';
			} else {
				classVal = 'skystats-data-point-change-direction skystats-data-point-change-direction-negative';
			}
			break;
		default:
			break;
	}
	jQuery( '#' + elementId ).attr( 'class', classVal );
}

/**
 * Set the class of a data point change element.
 * @param {string} elementId The element Id.
 * @param {string} direction The change direction (positive, negative, or neutral).
 * @param {bool}   opposite  Whether the change should be displayed in opposite direction e.g. if true, positive changes
 *                           will be displayed as a negative change and vice versa. This only applies to positve and negative
 *                           changes.
 */
function setDataPointChangeClass( elementId, direction, opposite ) {
	var classVal = '';
	switch ( direction ) {
		case 'positive':
			if ( opposite ) {
				classVal = 'skystats-data-point-change skystats-data-point-change-increase-negative';
			} else {
				classVal = 'skystats-data-point-change skystats-data-point-change-positive';
			}
			break;
		case 'negative':
			if ( opposite ) {
				classVal = 'skystats-data-point-change skystats-data-point-change-decrease-positive';
			} else {
				classVal = 'skystats-data-point-change skystats-data-point-change-negative';
			}
			break;
		case 'neutral':
			classVal = 'skystats-data-point-change';
			break;
		default:
			break;
	}
	jQuery( '#' + elementId ).attr( 'class', classVal );
}

/**
 * Returns a formatted date given a date string and other required data.
 *
 * @param {string} dateString
 *
 * @param {string} frequency 'daily' or 'monthly'.
 *
 * @return {string}
 */
function getDateFormatted( dateString, frequency ) {
	if ( 'monthly' === frequency ) {
		return getYearMonthFormatted( dateString );
	}
	return getMonthDayFormatted( dateString );
}

/**
 * Given a date string, converts it to a date and returns the year and month formatted as "yy/mm".
 *
 * @param {string} dateString
 *
 * @return {string}
 */
function getYearMonthFormatted( dateString ) {
	var dateStringYear = dateString.substr( 0, 4 ),
		dateStringMonth = dateString.substr( 5, 2 ),
		dateStringDay = dateString.substr( 8, 2 ),
		date = new Date( dateStringYear, dateStringMonth - 1, dateStringDay, 0, 0, 0 ),
		year = ( date.getFullYear() ).toString(),
		month = ( date.getMonth() + 1 ).toString();

	if ( month < 10 ) {
		month = '0' + month;
	}

	return year + '/' + month;
}

/**
 * Given a date string, converts it to a date and returns the day and month formatted as "mm/dd".
 *
 * @param {string} dateString
 *
 * @returns {string}
 */
function getMonthDayFormatted( dateString ) {
	var dateStringYear = dateString.substr( 0, 4 ),
		dateStringMonth = dateString.substr( 5, 2 ),
		dateStringDay = dateString.substr( 8, 2 ),
		date = new Date( dateStringYear, dateStringMonth - 1, dateStringDay, 0, 0, 0 ),
		month = ( date.getMonth() + 1 ).toString(),
		day = ( date.getDate() ).toString();

	if ( month < 10 ) {
		month = '0' + month;
	}

	if ( day < 10 ) {
		day = '0' + day;
	}

	return month + '/' + day;
}

/**
 * Return data for a chart with the supplied data.
 *
 * @param {Array} data
 *
 * @param {jQuery} $chart
 *
 * @param {object} integrationInst
 *
 * @param {string} frequency 'daily' or 'monthly'.
 *
 * @return {Array}
 */
function getChartData( data, $chart, integrationInst, frequency ) {
	var chartData = [],
		dataLen = data.length,
		possibleMarkersNum = getPossibleMarkersNum( data, $chart, integrationInst );

	if ( possibleMarkersNum === dataLen ) {
		for ( var a = 0; a < dataLen; ++a ) {
			var dateI = getDateFormatted( data[ a ][ 0 ], frequency );
			chartData[ a ] = [ dateI, data[ a ][ 1 ] ];
		}
		return chartData;
	}

	chartData[ 0 ] = [ data[ 0 ][ 1], data[ 0 ][ 1 ] ];

	var factor = Math.abs( Math.ceil( dataLen / possibleMarkersNum ) );

	for ( var i = 0, j = 0; i < dataLen, ( j + factor ) < dataLen; ++i, j += factor ) {
		var dateB = getDateFormatted( data[ j ][ 0 ], frequency );
		chartData[ i ] = [ dateB, data[ j ][ 1 ] ];
	}

	var lastIdx = dataLen - 1,
		dateC = getDateFormatted( data[ lastIdx ][ 0 ], frequency );
	chartData[ chartData.length ] = [ dateC, data[ lastIdx ][ 1 ] ];

	return chartData;
}

/**
 * Return how many markers can be displayed on the chart.
 *
 * @param {Array} data
 *
 * @param {jQuery} $chart
 *
 * @param {object} integrationInst An Integration instance for setting the total amount of chart markers that can be displayed.
 *
 * @returns {number}
 */
function getPossibleMarkersNum( data, $chart, integrationInst ) {
	var dataLen = data.length,
		factor = 50,
		chartWidth = $chart.width(),
		totalMarkers = Math.round( chartWidth / factor - 1 ),
		possibleMarkersNum = totalMarkers;

	// Always try to show up to 30 markers for the charts on the Mashboard if these conditions are met.
	if ( 'mashboard' === integrationInst.viewName && chartWidth > 300 && dataLen <= 30 ) {
		return dataLen;
	}

	if ( dataLen <= totalMarkers ) {
		possibleMarkersNum = dataLen;
	}

	if ( 'detail' === integrationInst.viewName ) {
		return possibleMarkersNum;
	}

	if ( null != gPossibleMarkers ) {
		return gPossibleMarkers;
	}
	gPossibleMarkers = possibleMarkersNum;
	return possibleMarkersNum;
}

function setGlobalPossibleMarkers( total ) {
	gPossibleMarkers = total;
}

function parseTopTableDate( dateString ) {
	var dateStringYear = dateString.substr( 0, 4 ),
		dateStringMonth = dateString.substr( 5, 2 ),
		dateStringDay = dateString.substr( 8, 2 ),
		date = new Date( dateStringYear, dateStringMonth - 1, dateStringDay, 0, 0, 0 ),
		year = ( date.getFullYear() ).toString(),
		month = ( date.getMonth() + 1 ).toString(),
		day = ( date.getDate() ).toString();

	if ( month < 10 ) {
		month = '0' + month;
	}

	if ( day < 10 ) {
		day = '0' + day;
	}

	return month + '-' + day + '-' + year;
}