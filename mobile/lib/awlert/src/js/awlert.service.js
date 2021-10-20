(function (){
	'use strict';

	angular.module('awlert')
	.factory('awlert', awlert);

	awlert.$inject = ['$compile', '$document', '$rootScope', 'AWLERT_TYPES'];
	
	function awlert($compile, $document, $rootScope, AWLERT_TYPES){

		function _neutral(message, duration){
			return _show(message, duration, AWLERT_TYPES.NEUTRAL);
		}		

		function _error(message, duration){
			return _show(message, duration, AWLERT_TYPES.ERROR);
		}

		function _success(message, duration){
			return _show(message, duration, AWLERT_TYPES.SUCCESS);			
		}

		function _show(message, duration, type){
			var scope = $rootScope.$new(true);

			angular.extend(scope, {
				message: message,
				duration: duration,
				type: type
			});

			_appendElement(scope);

			return scope;
		}
	
		function _appendElement(scope){
			var element = $compile('<aw-lert></aw-lert>')(scope);
			$document[0].body.appendChild(element[0]);				
		}

		return {
			error: _error,
			success: _success,
			neutral: _neutral
		};
	}

})();