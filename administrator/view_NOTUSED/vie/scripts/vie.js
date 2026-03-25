var vieNgDependencies = ['ui.bootstrap', 'vieDirectivesControls'];

angular
	.module('vie', vieNgDependencies, ['$httpProvider', function ($httpProvider) {
		$httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
		$httpProvider.defaults.transformRequest = [function(data) {
			return angular.isObject(data) && String(data) !== '[object File]' ? jQuery.param(data) : data;
		}];
	}])
	.filter('trusted', ['$sce', function ($sce) {
	    return function(url) {
	        return $sce.trustAsResourceUrl(url);
	    };
	}]);

jQuery(document).ready(function () {
	angular.bootstrap(document.body, ['vie']);
});
