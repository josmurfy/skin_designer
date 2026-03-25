<div id="sap-share-modal" class="modal modal-ocx-sap fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $button_close; ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-uppercase"><?php echo $text_publish_title; ?></h4>
      </div>
      <div class="modal-body">
         <div id="post-message">
            <div class="form-group">
               <div class="sap-heading hidden"><h4><span><?php echo $entry_message; ?></span></h4></div>
                  <textarea name="message" class="form-control" rows="2"></textarea>
                  <div id="post-message-options">
                     <a href="javascript:void(0);" id="sap-schedule-switch" class="sap-btn-option"><i class="fa fa-clock-o"></i></a>
                  </div>
            </div>
         </div>
         <div id="post-link" class="hidden">
            <div class="form-group">
               <div class="input-group">
                  <span class="input-group-addon input-group-addon-square"><i class="fa fa-external-link"></i></span>
                  <input type="text" name="link" class="form-control" />
               </div>
            </div>
         </div>
         <div id="post-image" class="hidden">
            <div class="form-group">
               <div class="input-group">
                  <span class="input-group-addon input-group-addon-square"><i class="fa fa-picture-o"></i></span>
                  <input type="text" name="image" class="form-control" />
               </div>
            </div>
         </div>
         <div id="post-autocomplete">
            <div class="form-group">
               <div class="input-group">
                  <span class="input-group-addon input-group-addon-square medium-size dropdown">
                     <a class="dropdown-toggle" data-toggle="dropdown"><span id="sap-autocomplete-item-type" class="label label-custom"><span id="autocomplete-item-type-description"><?php echo $text_custom_link; ?></span> <span class="caret"></span></span></a>
                     <ul class="dropdown-menu dropdown-menu-custom">
                        <?php if ($template_categories) { ?>
                        <?php foreach($template_categories as $template_category) { ?>
                        <li><a href="javascript:void(0);" data-sap-item-type="<?php echo $template_category['code']; ?>" class="autocomplete-item-type"><?php echo $template_category['name']; ?></a></li>
                        <?php } ?>
                        <li role="separator" class="divider"></li>
                        <?php } ?>
                        <li><a href="javascript:void(0);" class="autocomplete-item-type"><?php echo $text_custom_link; ?></a></li>
                     </ul>
                  </span>
                  <input type="text" name="autocomplete" id="sap-autocomplete-item-id" placeholder="<?php echo $help_custom_link; ?>" data-placeholder-autocomplete="<?php echo $help_autocomplete; ?>" data-placeholder-custom-link="<?php echo $help_custom_link; ?>" class="form-control" />
                  <span class="input-group-addon input-group-addon-square autocomplete-reset"><i class="fa fa-trash"></i></span>
               </div>
            </div>
         </div>
         <div id="post-schedule">
            <div class="form-group">
               <div class="input-group sap-datetime">
                  <span class="input-group-addon input-group-addon-square medium-size"><?php echo $entry_schedule_date; ?></span>
                  <input type="text" name="schedule_datetime" value="" placeholder="<?php echo $help_scheduled_date; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" class="form-control" readonly />
                  <span class="input-group-btn"><button type="button" class="btn btn-default"><i class="fa fa-clock-o"></i></button></span>
               </div>
            </div>
            <div class="form-group hidden">
               <input type="text" name="scheduled_post" value="0" class="hidden" />
            </div>
         </div>
         <div id="post-preview">
            <div class="sap-heading"><h4><span><?php echo $text_post_preview; ?></span></h4></div>
            <div class="post-item">
               <div class="post-image"><a class="image-link" target="_blank"></a></div>
               <div class="post-caption">
                  <div class="post-title"><a class="title-link" target="_blank"></a></div>
                  <div class="post-short-description"><a class="short-description-link" target="_blank"></a></div>
               </div>
               <div class="post-meta">
                  <input type="text" name="item_type" value="" />
                  <input type="text" name="item_id" value="" />
                  <input type="text" name="scheduled_post_id" value="0" />
               </div>
            </div>
         </div>
         <div id="post-channel-permissions-list">
            <div class="sap-heading"><h4><span><?php echo $text_post_where; ?></span></h4></div>
            <div class="row">
               <?php if ($channel_permissions) { ?>
               <?php foreach($channel_permissions as $channel_permission) { ?>
               <div class="col-sm-4">
                  <div class="channel-permission-item <?php echo $channel_permission['channel_code']; ?> <?php echo ($channel_permission['selected']) ? 'active' : ''; ?>">
                     <div class="name"><?php echo $channel_permission['name']; ?></div>
                     <input type="hidden" name="permission[]" value="<?php echo $channel_permission['permission_id']; ?>" />
                  </div>
               </div>
               <?php } ?>
               <?php } ?>
            </div>
         </div>
      </div>
      <div class="modal-footer">
        <a id="btn-post-now" class="btn-u btn-block" data-text-post-now="<?php echo $button_post; ?>" data-text-post-schedule="<?php echo $button_post_schedule; ?>" data-text-update-post-schedule="<?php echo $button_update_post_schedule; ?>"><?php echo $button_post; ?></a>
      </div>
      <div id="sap-secondary-loading" class="loading-mask-overlay"><div class="loading-mask-loading"><div class="uil-ripple-css"><div></div><div></div></div></div></div>
    </div>
  </div>
</div>
