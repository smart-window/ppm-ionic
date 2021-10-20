(function () {
	'use strict';

	angular.module('awlert')
	.directive('awLert', AwesomeAlert);

	AwesomeAlert.$inject = ['$timeout'];

	function AwesomeAlert($timeout){
		var transformDuration = 600;

		function _link(scope, element){
			scope.$parent.remove = _remove;			
			
			init();

			function init(){
				element.addClass(scope.type);

				element.bind('click', _clickHandler);

				$timeout(function(){
					element.addClass('enter');

					if(scope.duration == -1) 
						return;
					else
						_remove();

				}, 30);				
			}

			function _clickHandler(){
				scope.$emit('awlert:click', scope);
			}

			function _remove(){
				$timeout(function(){

					element.removeClass('enter');									
					
					$timeout(function(){

						element.remove();
						scope.$destroy();

					}, transformDuration);							

				}, scope.duration || 3000);			
			}

		}

		return {
			template: '<div class="awlert">'+
									'<div class="space"></div>'+
									'<div class="content">'+
									'{{message}}'+
									'</div>'+
								'</div>',
			replace: 'true',
			restrict: 'E',
			scope: true, 
			link: _link
		};
	}

})();