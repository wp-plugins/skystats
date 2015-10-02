(function ($) {
	var options = {};


	function processDatapoints( plot, series, datapoints ) {
		setupCategoriesForAxis( series, "xaxis", datapoints );
	}

	function setupCategoriesForAxis( series, axis, datapoints ) {
		if ( 'categories' !== series[axis].options.mode ) {
			return;
		}
		series[ axis ].options.ticks = categoriesTickGenerator;
	}

	function categoriesTickGenerator( axis ) {
		var res = [],
			n = 1,
			catLen = 0;
		for ( var key in axis.categories ) {
			if (axis.categories.hasOwnProperty(key)) {
				++catLen;
			}
		}
		// Each property is the number of markers on a chart, and they each contain an array which corresponds to
		// which index of labels to display, to allow us to display up to 30 markers on the mashboard chart, but a predefined
		// amount of labels so that they fit correctly.
		var markerAmountsLabelIdxs = {
			1:  [1],
			2:  [1,2],
			3:  [1,2,3],
			4:  [1,2,3,4],
			5:  [1,2,3,4,5],
			6:  [1,2,3,4,5,6],
			7:  [1,2,3,4,5,6,7],
			8:  [1,2,3,4,6,7,8],
			9:  [1,2,4,6,8,9],
			10: [1,5,10],
			11: [1,3,6,9,11],
			12: [1,3,6,9,12],
			13: [1,4,8,11,13],
			14: [1,3,6,9,12,14],
			15: [1,5,10,15],
			16: [1,4,7,10,13,16],
			17: [1,3,5,7,9,11,13,15,17],
			18: [1,6,13,18],
			19: [1,3,8,13,18,19],
			20: [1,5,10,15,20],
			21: [1,4,8,16,19,21],
			22: [1,4,10,15,20,22],
			23: [1,5,10,15,21,23],
			24: [1,5,10,15,24],
			25: [1,5,10,15,20,25],
			26: [1,5,10,17,22,26],
			27: [1,7,14,21,27],
			28: [1,9,20,28],
			29: [1,5,10,15,20,25,29],
			30: [1,5,10,15,20,25,30]
		};
		for ( var label in axis.categories ) {
			if ( axis.categories.hasOwnProperty( label ) ) {
				if ( markerAmountsLabelIdxs.hasOwnProperty( catLen.toString() ) ) {
					var nums = markerAmountsLabelIdxs[ catLen ];
					var v = axis.categories[label],
						theLabel = '';
					if (jQuery.inArray(n, nums) !== -1) {
						theLabel = label;
					}
					if (v >= axis.min && v <= axis.max) {
						res.push([v, theLabel]);
					}
				}
			}
			++n;
		}

		res.sort(function ( a, b ) { return a[0] - b[0]; });

		return res;
	}

	function init( plot ) {
		plot.hooks.processDatapoints.push( processDatapoints );
	}

	$.plot.plugins.push({
		init: init,
		options: options,
		name: 'skystats_mashboard_categories',
		version: '1.0'
	});
})(jQuery);