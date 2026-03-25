<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
   <div class="page-header">
     <div class="container-fluid">
       <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              </div>
              <div class="form-group">
               <label class="control-label" for="input-template-category-id"><?php echo $entry_category; ?></label>
               <select name="filter_template_category_id" id="input-template-category-id" class="form-control">
                 <option value="*"></option>
                 <?php foreach ($template_categories as $template_category) { ?>
                 <?php if ($template_category['template_category_id'] == $filter_template_category_id) { ?>
                 <option value="<?php echo $template_category['template_category_id']; ?>" selected="selected"><?php echo $template_category['name']; ?></option>
                 <?php } else { ?>
                 <option value="<?php echo $template_category['template_category_id']; ?>"><?php echo $template_category['name']; ?></option>
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
              <div class="form-group">
                <label class="control-label" for="input-default"><?php echo $entry_default; ?></label>
                <select name="filter_default" id="input-default" class="form-control">
                  <option value="*"></option>
                  <?php if ($filter_default) { ?>
                  <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                  <?php } else { ?>
                  <option value="1"><?php echo $text_yes; ?></option>
                  <?php } ?>
                  <?php if (!$filter_default && !is_null($filter_default)) { ?>
                  <option value="0" selected="selected"><?php echo $text_no; ?></option>
                  <?php } else { ?>
                  <option value="0"><?php echo $text_no; ?></option>
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
              <div class="form-group">
               <label class="control-label" for="input-date-modified"><?php echo $entry_date_modified; ?></label>
               <div class="input-group date">
                 <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="<?php echo $entry_date_modified; ?>" data-date-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
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
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_category; ?></td>
                  <td class="text-left"><?php if ($sort == 't.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                    <td class="text-left"><?php if ($sort == 't.default') { ?>
                      <a href="<?php echo $sort_default; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_default; ?></a>
                      <?php } else { ?>
                      <a href="<?php echo $sort_default; ?>"><?php echo $column_default; ?></a>
                      <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 't.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($templates) { ?>
                <?php foreach ($templates as $template) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($template['template_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $template['template_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $template['template_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $template['name']; ?></td>
                  <td class="text-left"><?php echo $template['category_name']; ?></td>
                  <td class="text-left"><?php echo $template['status']; ?></td>
                  <td class="text-left"><?php echo $template['default']; ?></td>
                  <td class="text-left"><?php echo $template['date_added']; ?></td>
                  <td class="text-right"><a href="<?php echo $template['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
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
	url = 'index.php?route=social_autopilot/template&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

   var filter_channel_id = $('select[name=\'filter_channel_id\']').val();

	if (filter_channel_id != '*') {
		url += '&filter_channel_id=' + encodeURIComponent(filter_channel_id);
	}

   var filter_template_category_id = $('select[name=\'filter_template_category_id\']').val();

	if (filter_template_category_id != '*') {
		url += '&filter_template_category_id=' + encodeURIComponent(filter_template_category_id);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

   var filter_default = $('select[name=\'filter_default\']').val();

	if (filter_default != '*') {
		url += '&filter_default=' + encodeURIComponent(filter_default);
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
