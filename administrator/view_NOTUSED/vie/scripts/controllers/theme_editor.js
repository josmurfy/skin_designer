vieNgDependencies.push('angularFileUpload');

angular.module('vie')
.controller('ThemeEditorCtrl', ['$scope', '$http', '$timeout', '$q', '$upload', '$window', function ($scope, $http, $timeout, $q, $upload, $window) {
	$scope.languages = Vie.languages;

	$scope.previewUrl = Vie.preview_url;
	$scope.store_id = Vie.store_id;
	$scope.skin_id = Vie.skin_id;
	$scope.fonts = Vie.fonts;
	$scope.skin_options = {};
	$scope.initial_options = null;

	$scope.loading = false;

	$scope.changeStore = function (store_id) {
		$window.location.href = $window.location.href.replace(/&?store_id=\d+/i, '') + '&store_id=' + store_id;
	};

	$scope.loadSkin = function (loadedCallback) {
		$scope.loading = true;

		$http.get(Vie.skin_url, {
			params: {
				store_id: $scope.store_id,
				skin_id: $scope.skin_id
			}
		})
		.success(function (data) {
			if (angular.isObject(data.skin_options)) {
				if (!$scope.initial_options) {
					$scope.initial_options = angular.copy($scope.skin_options, $scope.initial_options);
				}

				$scope.skin_options = angular.extend({}, $scope.initial_options, data.skin_options);

				$scope.$emit('skinLoaded');
			}

			$scope.loading = false;
		})
		.error(function (data) {
			$scope.loading = false;
		});
	};

	$scope.save = function () {
		$scope.loading = true;

		var deferred = $q.defer();

		$scope.saveDeferred = deferred;

		$scope.$broadcast('beforeSave');

		deferred.promise.then(function(data) {
			$http.post(Vie.save_url, $.param({
				store_id: $scope.store_id,
				skin_id: $scope.skin_id,
				skin_options: $scope.skin_options,
				skin_css: data.skin_css
			}))
			.success(function (data) {
				$scope.loading = false;
			})
			.error(function (data) {
				$scope.loading = false;
			});		  
		});
	};

	$scope.export = function () {
		$scope.download_url = Vie.export_url;
	};

	$scope.import = function ($files) {
		$scope.loading = true;

		for (var i = 0; i < $files.length; i++) {
			var file = $files[i];

			$scope.upload = $upload.upload({
				url: Vie.import_url,
				file: file,
				store_id: $scope.store_id
			}).progress(function(evt) {
			}).success(function(data, status, headers, config) {
				if (data.status) {
					alert(data.message);
					location.reload();
				} else {
					alert(data.error);
					$scope.loading = false;
				}
			});
		}
	}

	$scope.pushOptions = function (option) {
		var preview = document.getElementById('preview');

		preview.contentWindow.postMessage({
			skin_id: $scope.skin_id,
			skin_options: $scope.skin_options,
			option: option
		}, '*');
	};

	$scope.postOptions = function (option) {
		$('#preview-data').val(JSON.stringify({
			skin_id: $scope.skin_id,
			skin_options: $scope.skin_options
		}));

		$('#form-controls').trigger('submit');
	};

	$scope.$on('skinCss', function (event, data) {
		$scope.skin_css = data.skin_css;
	});

	$scope.$on('optionChanged', function (event, option) {
		if (option.transport == 'live') {
			$scope.pushOptions(option);
		} else {
			$scope.postOptions(option);
		}
	});

	$scope.$on('previewUrlChanged', function (event, previewUrl) {
		$scope.previewUrl = previewUrl;
	});

	$scope.$on('skinLoaded', function () {
		$scope.postOptions({});
	});

	$scope.loadSkin();
}]);
