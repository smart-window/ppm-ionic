(function () {
	'use strict';

	angular.module('awlert')
	.constant('AWLERT_TYPES', {
		"ERROR": 'assertive-bg',
		"SUCCESS": 'balanced-bg',
		"NEUTRAL": 'positive-bg',
		"CUSTOM":  ''
	});
})();