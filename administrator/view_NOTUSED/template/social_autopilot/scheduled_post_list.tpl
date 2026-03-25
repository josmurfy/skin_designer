<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
   <div class="page-header">
     <div class="container-fluid">
       <div class="pull-right"><a href="javascript:void(0);" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary btn-sap-share"><i class="fa fa-plus"></i></a>
         <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-template').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
                <select name="filter_status" id="input-status" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_status) { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                  <?php if (!$filter_status && !is_null($filter_status)) { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-sm-4">
               <div class="form-group">
               <label class="control-label" for="input-date-schedule"><?php echo $entry_date_schedule; ?></label>
               <div class="input-group date">
                  <input type="text" name="filter_date_schedule" value="<?php echo $filter_date_schedule; ?>" placeholder="<?php echo $entry_date_schedule; ?>" data-date-format="YYYY-MM-DD" id="input-date-schedule" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span></div>
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

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-template">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php echo $column_message; ?></td>
                  <td class="text-center"><?php echo $column_link; ?></td>
                  <td class="text-center"><?php if ($sort == 'sp.date_schedule') { ?>
                    <a href="<?php echo $sort_date_schedule; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_schedule; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_schedule; ?>"><?php echo $column_date_schedule; ?></a>
                    <?php } ?></td>
                  <td class="text-center"><?php echo $column_status; ?></td>
                  <td class="text-center"><?php if ($sort == 'sp.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($scheduled_posts) { ?>
                <?php foreach ($scheduled_posts as $scheduled_post) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($scheduled_post['scheduled_post_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $scheduled_post['scheduled_post_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $scheduled_post['scheduled_post_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $scheduled_post['message']; ?></td>
                  <td class="text-center"><a href="<?php echo $scheduled_post['link']; ?>" target="_blank" data-toggle="tooltip" title="<?php echo $scheduled_post['link']; ?>"><i class="fa fa-external-link"></i></a></td>
                  <td class="text-center"><?php echo $scheduled_post['date_schedule']; ?></td>
                  <td class="text-center">
                     <?php if ($scheduled_post['enabled']) { ?>
                     <i class="fa fa-hourglass-start sap-status-icon sap-progress" data-toggle="tooltip" data-html="true" title="<?php echo $help_progress; ?>"></i>
                     <?php } else { ?>
                     <i class="fa fa-exclamation-triangle sap-status-icon sap-fail" data-toggle="tooltip" data-html="true" title="<?php echo $help_fail; ?>"></i>
                     <?php } ?>
                  </td>
                  <td class="text-center"><?php echo $scheduled_post['date_added']; ?></td>
                  <td class="text-right">
                     <?php if ($scheduled_post['item_type'] && $scheduled_post['item_id']) { ?>
                     <a href="javascript:void(0);" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-default btn-sap-share" data-sap-item-type="<?php echo $scheduled_post['item_type']; ?>" data-sap-item-id="<?php echo $scheduled_post['item_id']; ?>" data-sap-scheduled-post-id="<?php echo $scheduled_post['scheduled_post_id']; ?>"><i class="fa fa-eye"></i></a>
                     <?php } else { ?>
                     <a href="javascript:void(0);" data-toggle="tooltip" title="<?php echo $button_view; ?>" class="btn btn-default btn-sap-share" data-sap-scheduled-post-id="<?php echo $scheduled_post['scheduled_post_id']; ?>"><i class="fa fa-eye"></i></a>
                     <?php } ?>

                     <?php if ($scheduled_post['enabled']) { ?>
                     <a href="javascript:void(0);" data-toggle="tooltip" title="<?php echo $button_disable; ?>" class="btn btn-danger btn-sap-status-switch" data-sap-scheduled-post-id="<?php echo $scheduled_post['scheduled_post_id']; ?>" data-sap-new-status="0"><i class="fa fa-pause"></i></a>
                     <?php } else { ?>
                     <a href="javascript:void(0);" data-toggle="tooltip" title="<?php echo $button_enable; ?>" class="btn btn-success btn-sap-status-switch" data-sap-scheduled-post-id="<?php echo $scheduled_post['scheduled_post_id']; ?>" data-sap-new-status="1"><i class="fa fa-play"></i></a>
                     <?php } ?>
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
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
	url = 'index.php?route=social_autopilot/scheduled_post&token=<?php echo $token; ?>';

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

   var filter_date_schedule = $('input[name=\'filter_date_schedule\']').val();

	if (filter_date_schedule) {
		url += '&filter_date_schedule=' + encodeURIComponent(filter_date_schedule);
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
