// Check if is something added in post queue from events
$(document).ready(function() {
   sapWatchDog();
});

// Add new Channel Permissions handler - show modal
$(document).delegate('.btn-show-channels', 'click', function() {
   $.ajax({
      url: 'index.php?route=social_autopilot/channel/getChannels&token=' + getURLVar('token'),
      dataType: 'json',
      beforeSend: function(){
      },
      complete: function(){
      },
      success: function(json){
         $('#sap-channels-modal').remove();
         $('body').prepend(json['output']);

         $('#sap-channels-modal').modal('show');
      }
   });
});

// Share dialog
$(document).delegate('.btn-sap-share', 'click', function() {
   var sap_item_type = (typeof($(this).attr('data-sap-item-type')) != 'undefined' ? $(this).attr('data-sap-item-type') : '');
   var sap_item_id = (typeof($(this).attr('data-sap-item-id')) != 'undefined' ? $(this).attr('data-sap-item-id') : 0);

   var sap_options = {
      scheduled_post_id : (typeof($(this).attr('data-sap-scheduled-post-id')) != 'undefined' ? $(this).attr('data-sap-scheduled-post-id') : 0),
      auto_fill_message: true,
      auto_post: (typeof($(this).attr('data-sap-auto-post')) != 'undefined' ? ($(this).attr('data-sap-auto-post') === 'true') : false)
   };

   $.ajax({
      url: 'index.php?route=social_autopilot/share&token=' + getURLVar('token') + ((sap_options['scheduled_post_id'] > 0) ? '&scheduled_post_id=' + sap_options['scheduled_post_id'] : ''),
      dataType: 'json',
      beforeSend: function() {
         // remove modal if is already added from other product share
         $('#sap-share-modal').remove();

         // add loading effect in body
         $('body').prepend('<div id="sap-main-loading" class="loading-mask-overlay loading-mask-sap-background"><div class="loading-mask-loading"><div class="uil-ripple-css"><div></div><div></div></div></div></div>');

         // show loading effect
         $('#sap-main-loading').show();
      },
      complete: function() {
         // remove loading effect (!REMOVE - not hide)
         $('#sap-main-loading').remove();
      },
      success: function(json) {
         // add share modal in page
         $('body').prepend(json['output']);

         // show share modal
         $('#sap-share-modal').modal('show');

         if ((sap_item_type && sap_item_id) || (sap_options.hasOwnProperty('scheduled_post_id') && sap_options['scheduled_post_id'])) {
            sapPreviewGenerator(sap_item_type, sap_item_id, sap_options);

            // case custom scheduled post
            if (!sap_item_type && !sap_item_id) {
               sapInitCustom();
            }
         } else {
            sapInitCustom();
         }

         sapInitCustomDateTime();
      }
   });
});

$(document).delegate('#sap-share-modal #post-autocomplete .autocomplete-item-type', 'click', function() {
   // first clear both
   $('#sap-autocomplete-item-type').attr('data-sap-item-type', '');
   $('#sap-autocomplete-item-id').val('').attr('data-sap-item-id', '').removeClass('autocomplete');
   $('#sap-share-modal #post-preview').hide();

   // check new selection
   var sap_item_type = (typeof($(this).attr('data-sap-item-type')) != 'undefined' ? $(this).attr('data-sap-item-type') : '');

   if (sap_item_type) {
      $('#sap-autocomplete-item-type').attr('data-sap-item-type', sap_item_type);
      $('#sap-autocomplete-item-id').addClass('autocomplete');
      $('#sap-autocomplete-item-id').attr('placeholder', $('#sap-autocomplete-item-id').attr('data-placeholder-autocomplete'));
   } else {
      $('#sap-autocomplete-item-id').attr('placeholder', $('#sap-autocomplete-item-id').attr('data-placeholder-custom-link'));
   }

   // set selection text
   $('#autocomplete-item-type-description').text($(this).text());
});

// case custom post with autocomplete enabled
$(document).delegate('#sap-share-modal #post-autocomplete input[name=\'autocomplete\'].autocomplete', 'focus', function() {
   $(this).autocomplete({
   	'source': function(request, response) {
   		$.ajax({
   			url: 'index.php?route=social_autopilot/autocomplete&token=' + getURLVar('token') + '&sap_item_type=' + encodeURIComponent($('#sap-autocomplete-item-type').attr('data-sap-item-type')) + '&sap_search=' +  encodeURIComponent(request),
   			dataType: 'json',
   			success: function(json) {
   				response($.map(json, function(item) {
   					return {
   						label: item['name'],
   						value: item['id']
   					}
   				}));
   			}
   		});
   	},
   	'select': function(item) {
         $(this).val(item['label']);
         $(this).attr('data-sap-item-id', item['value']);

         sapAutocompletePreviewTrigger();
   	}
   });
});

// case custom post with custom link (NO Autocomplete)
$(document).delegate('#sap-share-modal #post-autocomplete input[name=\'autocomplete\']:not(.autocomplete)', 'change', function() {
   $('#sap-share-modal #post-link input[name=\'link\']').val(encodeURIComponent($(this).val()));
});

// clear autocomplete or custom link
$(document).delegate('#sap-share-modal #post-autocomplete .autocomplete-reset', 'click', function() {
   $('#sap-autocomplete-item-id').val('').attr('data-sap-item-id', '');
   $('#sap-share-modal #post-link input[name=\'link\']').val('');
   $('#sap-share-modal #post-preview').hide();
});

// SHARE
$(document).delegate('#sap-share-modal .channel-permission-item', 'click', function() {
   if ($(this).hasClass('active')) {
      $(this).removeClass('active');
   } else {
      $(this).addClass('active');
   }
});

// Schedule options
$(document).delegate('#sap-schedule-switch', 'click', function() {
   if ($(this).hasClass('active')) {
      $(this).removeClass('active');
      $('#sap-share-modal #post-schedule').hide();
      $('#sap-share-modal #post-schedule input[name=\'scheduled_post\']').val('0');
      $('#sap-share-modal #btn-post-now').text($('#sap-share-modal #btn-post-now').attr('data-text-post-now'));
   } else {
      $(this).addClass('active');
      $('#sap-share-modal #post-schedule').show();
      $('#sap-share-modal #post-schedule input[name=\'scheduled_post\']').val('1');

      var is_update = ($('#sap-share-modal #post-preview .post-item .post-meta input[name=\'scheduled_post_id\']').val() > 0) ? true : false;

      if (is_update) {
         $('#sap-share-modal #btn-post-now').text($('#sap-share-modal #btn-post-now').attr('data-text-update-post-schedule'));
      } else {
         $('#sap-share-modal #btn-post-now').text($('#sap-share-modal #btn-post-now').attr('data-text-post-schedule'));
      }
   }
});

$(document).delegate('#sap-share-modal #btn-post-now', 'click', function() {
   $.ajax({
      type: 'POST',
      url: 'index.php?route=social_autopilot/share&token=' + getURLVar('token'),
      data: $('#sap-share-modal textarea[name=\'message\'], #sap-share-modal input[name=\'link\'], #sap-share-modal input[name=\'image\'], #sap-share-modal input[name=\'scheduled_post\'], #sap-share-modal input[name=\'item_type\'], #sap-share-modal input[name=\'item_id\'], #sap-share-modal input[name=\'scheduled_post_id\'], #sap-share-modal input[name=\'schedule_datetime\'], #sap-share-modal .channel-permission-item.active input[name=\'permission[]\']'),
      dataType: 'json',
      beforeSend: function() {
         $('#sap-secondary-loading').show();
		   $('#sap-share-modal .modal-body .alert').remove();
      },
      complete: function() {
         $('#sap-secondary-loading').hide();
      },
      success: function(json) {
   		if (json['error']) {
   			$('#sap-share-modal .modal-body').prepend('<div class="alert alert-danger">' + json['error'] + '</div>');
   		}

   		if (json['success']) {
   			$('#sap-share-modal .modal-body').prepend('<div class="alert alert-success">' + json['success'] + '</div>');

   			setTimeout(function () {
   				$('#sap-share-modal').modal('hide');

               if (getURLVar('route') == 'social_autopilot/scheduled_post') {
                  location.reload();
               }
   		    }, 2500);
   		}
      }
   });
});

// View Task Log
$(document).delegate('.btn-sap-task-log', 'click', function() {
   var task_id = $(this).attr('data-task-id');

   $.ajax({
      type: 'POST',
      url: 'index.php?route=social_autopilot/task/getLog&token=' + getURLVar('token'),
      data: 'task_id=' + task_id,
      dataType: 'json',
      success: function(json){
         $('#sap-task-log-modal').remove();

         if (json['output']) {
            $('body').prepend(json['output']);

            $('#sap-task-log-modal').modal('show');
         }
      }
   });
});

// FUNCTIONS -----------------------------------------------------
function sapPreviewGenerator(item_type, item_id, options) {
   $.ajax({
      type: 'POST',
      url: 'index.php?route=social_autopilot/share/preview&token=' + getURLVar('token'),
      data: 'item_type=' + encodeURIComponent(item_type) + '&item_id=' + item_id + ((options.hasOwnProperty('scheduled_post_id') && options['scheduled_post_id']) ? '&scheduled_post_id=' + options['scheduled_post_id'] : ''),
      dataType: 'json',
      beforeSend: function() {
         // show loading effect
         $('#sap-share-modal #sap-secondary-loading').show();
      },
      complete: function() {
         $('#sap-share-modal #sap-secondary-loading').hide();
      },
      success: function(json) {
         if (json['share_info']) {
            if (options.hasOwnProperty('auto_fill_message') && options['auto_fill_message']) {
               if (json['share_info']['message']) {
                  $('#sap-share-modal #post-message textarea[name=\'message\']').val(json['share_info']['message']);

                  // autoresize message textarea
                  setTimeout(function () {
                     sapAutoResize($('#sap-share-modal #post-message textarea[name=\'message\']'));
                  }, 500);

               }
            }

            // add preview elements
            if (json['share_info']['image']) {
               $('#sap-share-modal #post-image input[name=\'image\']').val(json['share_info']['image']);
               $('#sap-share-modal #post-preview .post-item .post-image').css('background-image', 'url(\'' + decodeURIComponent(json['share_info']['image']) + '\')');
            }

            if (json['share_info']['link']) {
               $('#sap-share-modal #post-preview .post-item .post-image .image-link, #sap-share-modal #post-preview .post-item .post-caption .title-link, #sap-share-modal #post-preview .post-item .post-caption .short-description-link').attr('href', decodeURIComponent(json['share_info']['link']));
               $('#sap-share-modal #post-link input[name=\'link\']').val(json['share_info']['link']);

               // case loaded custom scheduled post
               if (options.hasOwnProperty('scheduled_post_id') && options['scheduled_post_id'] && (!item_type && !item_id)) {
                  $('#sap-share-modal #post-autocomplete input[name=\'autocomplete\']').val(decodeURIComponent(json['share_info']['link']));
               }
            }

            if (json['share_info']['title']) {
               $('#sap-share-modal #post-preview .post-item .post-caption .title-link').text(json['share_info']['title']);
            }

            if (json['share_info']['short_description']) {
               $('#sap-share-modal #post-preview .post-item .post-caption .short-description-link').text(json['share_info']['short_description']);
            }

            if (json['share_info']['image'] || json['share_info']['link']) {
               $('#sap-share-modal #post-preview').show();

               if (json['share_info']['image']) {
                  $('#sap-share-modal #post-preview .post-image').show();
               } else {
                  $('#sap-share-modal #post-preview .post-image').hide();
               }
            }

            if (json['share_info']['title'] || json['share_info']['short-description']) {
               $('#sap-share-modal #post-preview').show();
               $('#sap-share-modal #post-preview .post-caption').show();
            }

            if (json['share_info']['schedule_datetime']) {
               $('#sap-share-modal #post-schedule input[name=\'schedule_datetime\']').val(json['share_info']['schedule_datetime']);
            }

            // SET POST META FOR SCHEDULED OR FOR LATER EDIT
            $('#sap-share-modal #post-preview .post-item .post-meta input[name=\'item_type\']').val(item_type);
            $('#sap-share-modal #post-preview .post-item .post-meta input[name=\'item_id\']').val(item_id);

            if (options.hasOwnProperty('scheduled_post_id') && options['scheduled_post_id']) {
               $('#sap-share-modal #post-preview .post-item .post-meta input[name=\'scheduled_post_id\']').val(options['scheduled_post_id']);

               $('#sap-share-modal #sap-schedule-switch').trigger('click');
            }

            // NOT RECOMMENDED TO ENABLE THIS OPTION
            if (options.hasOwnProperty('auto_post') && options['auto_post']) {
               $('#btn-post-now').trigger('click');
            }
         }
      }
   });
}

function sapWatchDog() {
   $.ajax({
      url: 'index.php?route=social_autopilot/share/check&token=' + getURLVar('token'),
      dataType: 'json',
      success: function(json){
         if (json['success']) {
            if (!$('.btn-sap-share[data-sap-item-type=\'' + json['sap_item_type'] + '\'][data-sap-item-id=\'' + json['sap_item_id'] + '\']').length) {
               $('.btn-sap-share-dynamic').remove();

               $('body').prepend('<a class="btn-sap-share btn-spa-share-dynamic hidden" data-sap-item-type="' + json['sap_item_type'] + '" data-sap-item-id="' + json['sap_item_id'] + '" data-sap-auto-post="' + json['sap_auto_post'] + '"></a>');
            }

            $('.btn-sap-share[data-sap-item-type=\'' + json['sap_item_type'] + '\'][data-sap-item-id=\'' + json['sap_item_id'] + '\']').trigger('click');
         }
      }
   });
}

function sapInitCustom() {
   $('#sap-share-modal #post-message').addClass('sap-custom');
   $('#sap-share-modal #post-autocomplete').addClass('sap-custom');
   $('#sap-share-modal #post-schedule').addClass('sap-custom');
   $('#sap-share-modal #post-channel-permissions-list').addClass('sap-custom');
}

function sapAutocompletePreviewTrigger() {
   var sap_item_type = (typeof($('#sap-autocomplete-item-type').attr('data-sap-item-type')) != 'undefined' ? $('#sap-autocomplete-item-type').attr('data-sap-item-type') : '');
   var sap_item_id = (typeof($('#sap-autocomplete-item-id').attr('data-sap-item-id')) != 'undefined' ? $('#sap-autocomplete-item-id').attr('data-sap-item-id') : 0);

   var sap_options = {
      auto_fill_message: false,
      auto_post: false
   };

   if (sap_item_type && sap_item_id) {
      sapPreviewGenerator(sap_item_type, sap_item_id, sap_options);
   }
}

function sapAutoResize(element) {
   var scroll_height = element.prop('scrollHeight');
   var inner_height = element.innerHeight();
   var max_height = 170;

   if (scroll_height && scroll_height > inner_height) {
      if (scroll_height > max_height) {
         scroll_height = max_height;
      }

      element.css('height', scroll_height + 'px');
   }
}

function sapInitCustomDateTime() {
   $('.sap-datetime').datetimepicker({
   	pickDate: true,
   	pickTime: true,
      sideBySide: true,
      icons: {
         up:   'fa fa-angle-up',
         down: 'fa fa-angle-down',
      }
   });
}
