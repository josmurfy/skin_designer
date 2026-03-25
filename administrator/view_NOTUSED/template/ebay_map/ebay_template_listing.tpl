<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">

        <a href="<?php echo $add_template; ?>" type="button" data-toggle="tooltip" title="<?php echo $button_add_template; ?>" class="btn btn-info"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo $button_add_template; ?></a>

        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete_template; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-ebay-specification').submit() : false;"><i class="fa fa-trash-o"></i> <?php echo $button_delete_template; ?></button>
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
                <label class="control-label" for="input-template-title"><?php echo $entry_template_title; ?></label>
                <div class='input-group'>
                    <input type="text" name="filter_template_title" value="<?php echo $filter_template_title; ?>" placeholder="<?php echo $entry_template_title; ?>" id="input-template-title" class="form-control" />
                    <span class="input-group-addon"><i class="fa fa-arrow-down" aria-hidden="true"></i></span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-ebay-category"><?php echo $entry_mapped_ebay_category; ?></label>
                <div class='input-group'>
                    <input type="text" name="filter_mapped_ebay_category" value="<?php echo $filter_mapped_ebay_category; ?>" placeholder="<?php echo $entry_mapped_ebay_category; ?>" id="input-ebay-category" class="form-control" />
                    <span class="input-group-addon"><i class="fa fa-arrow-down" aria-hidden="true"></i></span>
                </div>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-ebay-site-list"><?php echo $entry_ebay_site_list; ?></label>
                <select class="form-control" name="filter_ebay_site_id">
                  <option value="*"><?php echo $text_select_site_list; ?></option>
                  <?php if(isset($ebaySites['ebay_sites']) && $ebaySites['ebay_sites']){ ?>
                     <?php foreach ($ebaySites['ebay_sites'] as $key => $ebay_site) { ?>
                       <option value="<?php echo $key; ?>" <?php if(isset($filter_ebay_site_id) && $key == $filter_ebay_site_id){ echo 'selected'; } ?> ><?php echo $ebay_site; ?></option>
                     <?php } ?>
                  <?php } ?>
                </select>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-modify-date"><?php echo $entry_modify_date; ?></label>
                <div class="input-group datetime">
                  <input type="text" name="filter_modify_date" value="<?php echo $filter_modify_date; ?>" placeholder="<?php echo $entry_modify_date; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-modify-date" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-template-title"><?php echo $entry_created_date; ?></label>
                <div class="input-group datetime">
                  <input type="text" name="filter_created_date" value="<?php echo $filter_created_date; ?>" placeholder="<?php echo $entry_created_date; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" id="input-modify-date" class="form-control" />
                  <span class="input-group-btn">
                  <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                  </span>
                </div>
              </div>

              <a href="<?php echo $clear; ?>" id="button-clear" class="btn btn-default pull-right" style="border-radius:0;"><i class="fa fa-eraser" aria-hidden="true"></i> <?php echo $button_clear_filter; ?></a>
              <button type="button" id="button-filter" onclick="filter();" class="btn btn-warning pull-right" style="border-radius:0;"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-ebay-specification">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'sort_condition_value') { ?>
                    <a href="<?php echo $sort_condition_value; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_template_title; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_condition_value; ?>"><?php echo $entry_template_title; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php if ($sort == 'sort_ebay_category_name') { ?>
                    <a href="<?php echo $sort_ebay_category_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_mapped_ebay_category; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ebay_category_name; ?>"><?php echo $entry_mapped_ebay_category; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php echo $entry_ebay_site_name; ?></td>

                  <td class="text-center"><?php if ($sort == 'sort_condition_name') { ?>
                    <a href="<?php echo $sort_condition_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_created_date; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_condition_name; ?>"><?php echo $entry_created_date; ?></a>
                    <?php } ?></td>

                  <td class="text-center"><?php if ($sort == 'sort_oc_category_name') { ?>
                    <a href="<?php echo $sort_oc_category_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_modify_date; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_oc_category_name; ?>"><?php echo $entry_modify_date; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php echo $entry_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($ebay_templates) { ?>
                <?php foreach ($ebay_templates as $ebay_template) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($ebay_template['row_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ebay_template['row_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ebay_template['row_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $ebay_template['template_title']; ?></td>
                  <td class="text-left"><?php echo $ebay_template['ebay_category_name']; ?></td>
                  <td class="text-left"><?php echo $ebay_template['ebay_site_name']; ?></td>
                  <td class="text-center"><?php echo $ebay_template['create_date']; ?></td>
                  <td class="text-center"><?php echo $ebay_template['modify_date']; ?></td>
                  <td class="text-center">
                    <a href="<?php echo $ebay_template['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit_template; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
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
</div>

<script type="text/javascript"><!--

$('.datetime').datetimepicker({
  pickDate: true,
  pickTime: true
});

function filter() {
	url = 'index.php?route=ebay_map/ebay_template_listing&token=<?php echo $token; ?>';

  var filter_template_title = $('input[name=\'filter_template_title\']').val();

	if (filter_template_title) {
		url += '&filter_template_title=' + encodeURIComponent(filter_template_title);
	}

	var filter_mapped_ebay_category = $('input[name=\'filter_mapped_ebay_category\']').val();

	if (filter_mapped_ebay_category) {
		url += '&filter_mapped_ebay_category=' + encodeURIComponent(filter_mapped_ebay_category);
	}

  var filter_created_date = $('input[name=\'filter_created_date\']').val();

	if (filter_created_date) {
		url += '&filter_created_date=' + encodeURIComponent(filter_created_date);
	}

  var filter_modify_date = $('input[name=\'filter_modify_date\']').val();

	if (filter_modify_date) {
		url += '&filter_modify_date=' + encodeURIComponent(filter_modify_date);
	}

  var filter_ebay_site_id = $('select[name=\'filter_ebay_site_id\']').val();

	if (filter_ebay_site_id != '*') {
		url += '&filter_ebay_site_id=' + encodeURIComponent(filter_ebay_site_id);
	}

	location = url;
}
//--></script>
<script type="text/javascript">

$('input[name=\'filter_mapped_ebay_category\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_condition_list/autocomplete&token=<?php echo $token; ?>&filter_ebay_category_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.ebay_category_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_mapped_ebay_category\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

$('input[name=\'filter_template_title\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_template_listing/autocomplete&token=<?php echo $token; ?>&filter_template_title=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.template_title,
            value: item.template_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_template_title\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});
</script>
<?php echo $footer; ?>
