<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
   <div class="page-header">
     <div class="container-fluid">
       <div class="pull-right">
         <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-task').submit() : false;"><i class="fa fa-trash-o"></i></button>
       </div>
       <h1><?php echo $heading_title; ?></h1>
       <ul class="breadcrumb">
         <?php foreach ($breadcrumbs as $breadcrumb) { ?>
         <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
         <?php } ?>
       </ul>
     </div>
   </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="well">
          <div class="row">
             <div class="col-sm-4">
                <div class="form-group">
                 <label class="control-label" for="input-channel-id"><?php echo $entry_channel; ?></label>
                 <select name="filter_channel_id" id="input-channel-id" class="form-control">
                   <option value="*"></option>
                   <?php foreach ($channels as $channel) { ?>
                   <?php if ($channel['channel_id'] == $filter_channel_id) { ?>
                   <option value="<?php echo $channel['channel_id']; ?>" selected="selected"><?php echo $channel['name']; ?></option>
                   <?php } else { ?>
                   <option value="<?php echo $channel['channel_id']; ?>"><?php echo $channel['name']; ?></option>
                   <?php } ?>
                   <?php } ?>
                 </select>
               </div>
            </div>
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status == 'in-progress') { ?>
                  <option value="in-progress" selected="selected"><?php echo $text_task_progress; ?></option>
                  <?php } else { ?>
                  <option value="in-progress"><?php echo $text_task_progress; ?></option>
                  <?php } ?>

                  <?php if ($filter_status == 'partial-success') { ?>
                  <option value="partial-success" selected="selected"><?php echo $text_task_partial_success; ?></option>
                  <?php } else { ?>
                  <option value="partial-success"><?php echo $text_task_partial_success; ?></option>
                  <?php } ?>

                  <?php if ($filter_status == 'success') { ?>
                  <option value="success" selected="selected"><?php echo $text_task_success; ?></option>
                  <?php } else { ?>
                  <option value="success"><?php echo $text_task_success; ?></option>
                  <?php } ?>

                  <?php if ($filter_status == 'failed') { ?>
                  <option value="failed" selected="selected"><?php echo $text_task_fail; ?></option>
                  <?php } else { ?>
                  <option value="failed"><?php echo $text_task_fail; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
               <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
               <div class="input-group date">
                 <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                 <span class="input-group-btn">
                 <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                 </span></div>
              </div>
               <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-task">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php echo $column_message; ?></td>
                  <td class="text-center"><?php echo $column_link; ?></td>
                  <td style="width: 1px;" class="text-center"><?php if ($sort == 'st.channel') { ?>
                    <a href="<?php echo $sort_channel; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_channel; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_channel; ?>"><?php echo $column_channel; ?></a>
                    <?php } ?>
                  </td>
                  <td class="text-center"><?php echo $column_channel_page; ?></td>
                  <td class="text-center"><?php if ($sort == 'st.success_rate') { ?>
                     <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                     <?php } else { ?>
                     <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                     <?php } ?>
                  </td>
                  <td class="text-center"><?php if ($sort == 'st.date_added') { ?>
                     <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                     <?php } else { ?>
                     <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                     <?php } ?>
                  </td>
                </tr>
              </thead>
              <tbody>
                <?php if ($tasks) { ?>
                <?php foreach ($tasks as $task) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($task['task_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $task['task_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $task['task_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $task['message']; ?></td>
                  <td class="text-center"><a href="<?php echo $task['link']; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $task['link']; ?>"><i class="fa fa-external-link"></i></a></td>
                  <td class="text-center"><i class="fa fa-<?php echo $task['channel_code']; ?>" data-toggle="tooltip" data-html="true" title="<?php echo $task['channel_name']; ?>"></i></td>
                  <td class="text-center">
                     <?php if ($task['channel_pages']) { ?>
                     <?php foreach ($task['channel_pages'] as $channel_page) { ?>
                        <a href="<?php echo $channel_page['href']; ?>" target="_blank" data-toggle="tooltip" data-html="true" title="<?php echo $channel_page['name']; ?>" class="sap-task-channel-page"><i class="fa fa-link"></i></a>
                     <?php } ?>
                     <?php } ?>
                  </td>
                  <td class="text-center">
                     <?php if (!$task['processed']) { ?>
                     <i class="fa fa-hourglass-start sap-status-icon sap-progress" data-toggle="tooltip" data-html="true" title="<?php echo $help_progress; ?>"></i>
                     <?php } else { ?>
                        <?php if ($task['success_rate'] == 0) { ?>
                           <i class="fa fa-exclamation-triangle sap-status-icon sap-fail" data-toggle="tooltip" data-html="true" title="<?php echo $help_fail; ?>"></i>
                        <?php } elseif ($task['success_rate'] < 100) { ?>
                           <i class="fa fa-pie-chart sap-status-icon sap-partial-success" data-toggle="tooltip" data-html="true" title="<?php echo $help_success_partial; ?>"></i>
                        <?php } else { ?>
                           <i class="fa fa-check-circle sap-status-icon sap-success" data-toggle="tooltip" data-html="true" title="<?php echo $help_success; ?>"></i>
                        <?php } ?>

                        <?php if ($task['show_log']) { ?>
                        <br /><a href="javascript:void(0);" class="btn-sap-task-log sap-task-view-log" data-task-id="<?php echo $task['task_id']; ?>"><?php echo $button_view_log; ?></a>
                        <?php } ?>
                     <?php }?>
                  </td>
                  <td class="text-center"><?php echo $task['date_added']; ?></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
<script type="text/javascript"><!--
$('.btn-sap-status-switch').on('click', function(){
   var scheduled_post_id = $(this).attr('data-sap-scheduled-post-id');
   var status = $(this).attr('data-sap-new-status');

   $.ajax({
      type: 'POST',
      url: 'index.php?route=social_autopilot/scheduled_post/setStatus&token=<?php echo $token; ?>',
      data: 'scheduled_post_id=' + scheduled_post_id + '&status=' + status,
      dataType: 'json',
      success: function(json){
         if (json['success']) {
            location.reload();
         }
      }
   })

});

$('#button-filter').on('click', function() {
	url = 'index.php?route=social_autopilot/task&token=<?php echo $token; ?>';

   var filter_channel_id = $('select[name=\'filter_channel_id\']').val();

	if (filter_channel_id != '*') {
		url += '&filter_channel_id=' + encodeURIComponent(filter_channel_id);
	}

   var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

	var filter_date_added = $('input[name=\'filter_date_added\']').val();

	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

	location = url;
});
//--></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script></div>
<?php echo $footer; ?>
