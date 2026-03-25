'use strict';

vieNgDependencies.push('vieDirectivesCp');

angular.module('vieDirectivesCp', [])
.directive('vieIframePreview', function () {
	return {
		restrict: 'A',
		link: function ($scope, $element, $attrs) {
			var frame = $element[0];

			$element.on('load', function () {
				$scope.pushOptions({});

				frame.contentWindow.postMessage({
					url: true
				}, '*');
			});

			$scope.$on('beforeSave', function () {
				frame.contentWindow.postMessage({
					skin_css: true
				}, '*');
			});

			window.addEventListener("message", function (event) {
				if (event.data) {
					if (event.data.skin_css) {
						$scope.saveDeferred.resolve({
							skin_css: event.data.skin_css
						});
					} else if (event.data.url) {
						$scope.$emit('previewUrlChanged', event.data.url);
					}
				}
			}, false);
		}
	};
});
