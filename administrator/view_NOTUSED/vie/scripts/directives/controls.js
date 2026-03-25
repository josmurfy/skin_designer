'use strict';

angular.module('vieDirectivesControls', ['vieTemplatesControls', 'ui.bootstrap', 'summernote'])
.directive('vieColorPicker', function () {
	return {
		restrict: 'EA',
		scope: {
			value: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/color-picker.html',
		link: function ($scope, $element, $attrs) {
            $element.spectrum({
                preferredFormat: "rgba",
                allowEmpty: true,
                showAlpha: true,
                showInput: true,
                showButtons: false,
                change: function(color) {
                	$scope.$apply(function () {
                		if (color) {
                			if (color.getAlpha() == 0) {
                    			color.setAlpha(1);
                    		}
                			
                			$scope.value = color.toRgbString();
                		} else {
                			$scope.value = 'transparent';
                		}
                	});
                },
                move: function(color) {
                    $scope.$apply(function () {
                    	if (color) {
                    		if (color.getAlpha() == 0) {
                    			color.setAlpha(1);
                    		}

                    		$scope.value = color.toRgbString();
                    	} else {
                    		$scope.value = 'transparent';
                    	}
                	});
                }
            });

            var option = JSON.parse($attrs.option);

            $scope.$watch('value', function (val) {
            	$element.spectrum("set", $scope.value);

            	if (val !== undefined) {
            		$scope.$emit('optionChanged', option);
            	}
            });
        }
	};
})
.directive('vieInput', function () {
	return {
		restrict: 'EA',
		scope: {
			value: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/input.html',
		controller: function ($scope, $element, $attrs) {
			var option = JSON.parse($attrs.option);

			var timer;

			$scope.$watch('value', function (val) {
				if (val !== undefined) {
					clearTimeout(timer);

					(function () {
						timer = setTimeout(function () {
							option.value = val;
							$scope.$emit('optionChanged', option);
						}, 500);
					})(val);
				}
			});
		}
	};
})
.directive('vieButtons', function () {
	return {
		restrict: 'EA',
		scope: {
			value: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/buttons.html',
		controller: function ($scope, $element, $attrs) {
			var option = JSON.parse($attrs.option);

			$scope.option_values = option.option_values;

			$scope.buttons = {
				value: $scope.value
			};

			$scope.$watch('buttons.value', function (val) {
				if (val !== undefined) {
					$scope.value = val;
					$scope.$emit('optionChanged', option);
				}
			});
		}
	};
})
.directive('vieEditor', function () {
	return {
		restrict: 'EA',
		scope: {
			value: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/editor.html',
	};
})
.directive('vieMultilingualEditor', function () {
	return {
		restrict: 'EA',
		scope: {
			value: '=',
			languages: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/multilingual-editor.html',
		controller: function ($scope, $element, $attrs) {
			var option = JSON.parse($attrs.option);

			$scope.option_values = option.option_values;

			if (angular.isUndefined($scope.value)) {
				$scope.value = {};
			}

			$scope.blur = function() {
				option.value = $scope.value;
				$scope.$emit('optionChanged', option);
			};
		}
	};
})
.directive('vieMultilingual', function () {
	return {
		scope: {
			value: '=',
			languages: '='
		},
		restrict: 'EA',
		replace: true,
		templateUrl: 'view/vie/templates/controls/multilingual.html',
		controller: function ($scope, $element, $attrs) {
			if (angular.isUndefined($scope.value)) {
				$scope.value = {};
			}

			var option = JSON.parse($attrs.option);

			var timer;

			$scope.$watch('value', function (val) {
				if (val !== undefined) {
					clearTimeout(timer);

					(function () {
						timer = setTimeout(function () {
							option.value = val;
							$scope.$emit('optionChanged', option);
						}, 500);
					})(val);
				}
			}, true);
		}
	};
})
.directive('vieToggle', function () {
	return {
		restrict: 'EA',
		scope: {
			value: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/toggle.html',
		controller: function ($scope, $element, $attrs) {
			var option = JSON.parse($attrs.option);

			$scope.$watch('value', function (val) {
				if (val !== undefined) {
					option.value = val;

					$scope.$emit('optionChanged', option);
				}
			});
		}
	};
})
.directive('vieImage', function () {
	var uniqueId = 1;

	return {
		scope: {
			value: '='
		},
		restrict: 'EA',
		replace: true,
		templateUrl: 'view/vie/templates/controls/image.html',
		controller: function ($scope, $element, $attrs) {
			var option = JSON.parse($attrs.option);

			uniqueId++;

			$scope.id = 'vie-image-' + uniqueId;
			$scope.inputId = 'vie-image-input-' + uniqueId;

			if (angular.isUndefined($scope.value)) {
				$scope.value = '';
			}

			$scope.$watch('value', function (value) {
				if (value) {
					$scope.thumb = Vie.front_base + 'image/' + value;
				} else {
					$scope.thumb = 'view/vie/images/no_image.png';
				}

				option.value = value;

				$scope.$emit('optionChanged', option);
			});
		},
		link: function ($scope, $element) {
			$element.on('click', function(e) {
				e.preventDefault();
			
				$element.popover({
					html: true,
					placement: 'right',
					trigger: 'manual',
					content: function() {
						return '<button type="button" id="button-image" class="btn btn-primary"><i class="fa fa-pencil"></i></button> <button type="button" id="button-clear" class="btn btn-danger"><i class="fa fa-trash-o"></i></button>';
					}
				});
			
				$element.popover('toggle');		
			
				$('#button-image').on('click', function() {
					$('#modal-image').remove();
			
					$.ajax({
						url: 'index.php?route=common/filemanager&token=' + getURLVar('token') + '&target=' + $scope.inputId + '&thumb=' + $scope.id,
						dataType: 'html',
						beforeSend: function() {
							$('#button-image i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
							$('#button-image').prop('disabled', true);
						},
						complete: function() {
							$('#button-image i').replaceWith('<i class="fa fa-upload"></i>');
							$('#button-image').prop('disabled', false);
						},
						success: function(html) {
							$('body').append('<div id="modal-image" class="modal">' + html + '</div>');

							$('#modal-image').modal('show');

							// Update model when image is selected
							$('body')
								.off('click.vie-thumbnail')
								.on('click.vie-thumbnail', 'a.thumbnail', function () {
									$scope.$apply(function () {
										$scope.value = $('#' + $scope.inputId).val();
									});
								});
						}
					});
			
					$element.popover('hide');
				});
			
				$('#button-clear').on('click', function() {
					$('#' + $scope.inputId).val('');

					$element.popover('hide');

					$scope.$apply(function () {
						$scope.value = '';
					});
				});
			});
		}
	};
})
.directive('vieFont', function () {
	return {
		restrict: 'EA',
		scope: {
			fonts: '=',
			value: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/font.html',
		controller: function ($scope, $element, $attrs) {
			var option = JSON.parse($attrs.option);

			$scope.$watch('value', function (val) {
				if (val !== undefined) {
					option.value = val;

					$scope.$emit('optionChanged', option);
				}
			});
		}
	};
})
.directive('vieSelect', function () {
	return {
		restrict: 'EA',
		scope: {
			value: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/select.html',
		controller: function ($scope, $element, $attrs) {
			var option = JSON.parse($attrs.option);

			$scope.option_values = option.option_values;

			$scope.$watch('value', function (val) {
				if (val !== undefined) {
					option.value = val;
					
					$scope.$emit('optionChanged', option);
				}
			});
		}
	};
})
.directive('vieTextarea', function () {
	return {
		restrict: 'EA',
		scope: {
			value: '='
		},
		replace: true,
		templateUrl: 'view/vie/templates/controls/textarea.html',
		controller: function ($scope, $element, $attrs) {
			var option = JSON.parse($attrs.option);

			var timer;

			$scope.$watch('value', function (val) {
				if (val !== undefined) {
					clearTimeout(timer);

					(function () {
						timer = setTimeout(function () {
							option.value = val;
							$scope.$emit('optionChanged', option);
						}, 1000);
					})(val);
				}
			});
		}
	};
})
