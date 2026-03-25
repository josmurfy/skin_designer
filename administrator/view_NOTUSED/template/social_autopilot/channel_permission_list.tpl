<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="javascript:void(0);" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary btn-show-channels"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-channel-permission').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
            <div class="col-sm-3">
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
           <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
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
           <div class="col-sm-3">
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

        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-channel-permission">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php echo $column_channel; ?></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left hidden"><?php if ($sort == 'cp.id') { ?>
                      <a href="<?php echo $sort_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_id; ?></a>
                      <?php } else { ?>
                      <a href="<?php echo $sort_id; ?>"><?php echo $column_id; ?></a>
                      <?php } ?></td>
                  <td class="text-left hidden"><?php if ($sort == 'cp.access_token') { ?>
                      <a href="<?php echo $sort_access_token; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_access_token; ?></a>
                      <?php } else { ?>
                      <a href="<?php echo $sort_access_token; ?>"><?php echo $column_access_token; ?></a>
                      <?php } ?></td>
                  <td class="text-center"><?php if ($sort == 'c.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-center"><?php if ($sort == 'c.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-center"><?php if ($sort == 'c.date_expire') { ?>
                     <a href="<?php echo $sort_date_expire; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_expire; ?></a>
                     <?php } else { ?>
                     <a href="<?php echo $sort_date_expire; ?>"><?php echo $column_date_expire; ?></a>
                     <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($channel_permissions) { ?>
                <?php foreach ($channel_permissions as $channel_permission) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($channel_permission['permission_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $channel_permission['permission_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $channel_permission['permission_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $channel_permission['channel_name']; ?></td>
                  <td class="text-left"><?php echo $channel_permission['name']; ?></td>
                  <td class="text-left hidden"><?php echo $channel_permission['page_id']; ?></td>
                  <td class="text-left hidden"><?php echo $channel_permission['access_token']; ?></td>
                  <td class="text-center"><?php echo $channel_permission['status']; ?></td>
                  <td class="text-center"><?php echo $channel_permission['date_added']; ?></td>
                  <td class="text-center">
                     <?php echo $channel_permission['date_expire']; ?>

                     <?php if ($channel_permission['date_expire'] != '-') { ?>
                     <i class="fa fa-fw fa-question-circle sap-help-icon" data-toggle="tooltip" data-html="true" title="<?php echo $help_date_expire; ?>"> </i>
                     <?php } ?>
                  </td>
                  <td class="text-right">
                     <?php if ($channel_permission['disable']) { ?>
                     <a href="<?php echo $channel_permission['disable']; ?>" data-toggle="tooltip" title="<?php echo $button_disable; ?>" class="btn btn-danger"><i class="fa fa-pause"></i></a>
                     <?php } ?>
                     <?php if ($channel_permission['enable']) { ?>
                     <a href="<?php echo $channel_permission['enable']; ?>" data-toggle="tooltip" title="<?php echo $button_enable; ?>" class="btn btn-success"><i class="fa fa-play"></i></a>
                     <?php } ?>
                  </td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="7">
                     <div class="sap-no-results"><?php echo $text_no_results; ?></div>
                     <a href="javascript:void(0);" class="btn btn-success btn-show-channels"><?php echo $button_add; ?></a>
                  </td>
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
$('#button-filter').on('click', function() {
	url = 'index.php?route=social_autopilot/channel_permission&token=<?php echo $token; ?>';

   var filter_channel_id = $('select[name=\'filter_channel_id\']').val();

	if (filter_channel_id != '*') {
		url += '&filter_channel_id=' + encodeURIComponent(filter_channel_id);
	}

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
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
