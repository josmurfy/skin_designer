<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_filter; ?>" onclick="$('#filter-rule').toggleClass('hidden-sm hidden-xs');" class="btn btn-default hidden-md hidden-lg"><i class="fa fa-filter"></i></button>
        <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" form="form-product" formaction="<?php echo $delete; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
          <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid"> <?php if ($error_warning) { ?>
    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
    <div class="row">
      <div id="filter-rule" class="col-md-3 col-md-push-9 col-sm-12 hidden-sm hidden-xs">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-filter"></i> <?php echo $text_filter; ?></h3>
          </div>
          <div class="panel-body">
            <div class="form-group">
              <label class="control-label" for="input-rule-for"><?php echo $entry_rule_for; ?></label>
              <select class="form-control" name="filter_rule_for">
                <option value=""></option>
                <?php if ($filter_rule_for == 'price') { ?>
                  <option selected value="price"><?php echo $text_price; ?></option>
                <?php } else { ?>
                  <option value="price"><?php echo $text_price; ?></option>
                <?php } ?>
                <?php if ($filter_rule_for == 'qty') { ?>
                  <option selected value="qty"><?php echo $text_quantity; ?></option>
                <?php } else { ?>
                  <option value="qty"><?php echo $text_quantity; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label class="control-label" for="input-min"><?php echo $entry_rule_min; ?></label>
              <input type="text" name="filter_min" value="<?php echo $filter_min; ?>" placeholder="<?php echo $entry_rule_min; ?>" id="input-min" class="form-control" onkeypress="return validate(event, this)"/>
            </div>
            <div class="form-group">
              <label class="control-label" for="input-max"><?php echo $entry_rule_max; ?></label>
              <input type="text" name="filter_max" value="<?php echo $filter_max; ?>" placeholder="<?php echo $entry_rule_max; ?>" id="input-max" class="form-control" onkeypress="return validate(event, this)"/>
            </div>
            <div class="form-group">
              <label class="control-label" for="input-value"><?php echo $entry_value; ?></label>
              <input type="text" name="filter_value" value="<?php echo $filter_value; ?>" placeholder="<?php echo $entry_value; ?>" id="input-value" class="form-control" onkeypress="return validate(event, this)"/>
            </div>
            <div class="form-group">
              <label class="control-label" for="input-status"><?php echo $entry_status; ?></label>
              <select name="filter_status" id="input-status" class="form-control">
                <option value=""></option>
                <?php if ($filter_status == '1') { ?>
                  <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                <?php } ?>
                <?php if ($filter_status == '0') { ?>
                  <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
              <button type="button" id="button-clear" class="btn btn-default"><i class="fa fa-refresh"></i> <?php echo $button_clear; ?></button>
              <button type="button" id="button-filter" class="btn btn-default pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
          </div>
        </div>
      </div>
      <div class="col-md-9 col-md-pull-3 col-sm-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
          </div>
          <div class="panel-body">
            <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td style="width: 1px;" class="text-center">
                        <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'min') { ?>
                          <a href="<?php echo $sort_min; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_rule_min; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_min; ?>"><?php echo $column_rule_min; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'max') { ?>
                          <a href="<?php echo $sort_max; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_rule_max; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_max; ?>"><?php echo $column_rule_max; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'rule_for') { ?>
                          <a href="<?php echo $sort_rule_for; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_rule_for; ?></a>
                        <?php } else { ?>
                         <a href="<?php echo $sort_rule_for; ?>"><?php echo $column_rule_for; ?></a>
                       <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'value') { ?>
                          <a href="<?php echo $sort_value; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_value; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_value; ?>"><?php echo $column_value; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'operation_type') { ?>
                          <a href="<?php echo $sort_operation_type; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_operation_type; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_operation_type; ?>"><?php echo $column_operation_type; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'operation') { ?>
                          <a href="<?php echo $sort_operation; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_operation; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_operation; ?>"><?php echo $column_operation; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'status') { ?>
                          <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-right"><?php echo $column_action; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if ($rules) { ?>
                  <?php foreach ($rules as $rule) { ?>
                  <tr>
                    <td class="text-center"><?php if (in_array($rule['rule_id'], $selected)) { ?>
                      <input type="checkbox" name="selected[]" value="<?php echo $rule['rule_id']; ?>" checked="checked" />
                    <?php } else { ?>
                      <input type="checkbox" name="selected[]" value="<?php echo $rule['rule_id']; ?>" />
                    <?php } ?>
                    </td>
                    <td class="text-left"><?php echo $rule['min']; ?></td>
                    <td class="text-left"><?php echo $rule['max']; ?></td>
                    <td class="text-center"><?php echo $rule['rule_for']; ?></td>
                    <td class="text-left"><?php echo $rule['value']; ?></td>
                    <td class="text-right"><?php echo $rule['operation_type']; ?></td>
                    <td class="text-left"><?php echo $rule['operation']; ?></td>
                    <td class="text-left"><?php echo $rule['status']; ?></td>
                    <td class="text-right"><a href="<?php echo $rule['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                  </tr>
                <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
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
  </div>
  <script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	var url = '';

	var filter_rule_for = $('select[name=\'filter_rule_for\']').val();

	if (filter_rule_for) {
		url += '&filter_rule_for=' + encodeURIComponent(filter_rule_for);
	}

	var filter_min = $('input[name=\'filter_min\']').val();

	if (filter_min) {
		url += '&filter_min=' + encodeURIComponent(filter_min);
	}

	var filter_max = $('input[name=\'filter_max\']').val();

	if (filter_max) {
		url += '&filter_max=' + encodeURIComponent(filter_max);
	}

	var filter_value = $('input[name=\'filter_value\']').val();

	if (filter_value) {
		url += '&filter_value=' + encodeURIComponent(filter_value);
	}

	var filter_status = $('select[name=\'filter_status\']').val();

	if (filter_status !== '') {
		url += '&filter_status=' + encodeURIComponent(filter_status);
	}

	location = 'index.php?route=ebay_map/price_qty_rule&token=<?php echo $token; ?>' + url;
});
$(document).on('click', '#button-clear', function() {
  location = 'index.php?route=ebay_map/price_qty_rule&token=<?php echo $token; ?>';
});

function validate(key, thisthis, nodot) {
  //getting key code of pressed key
  var keycode = (key.which) ? key.which : key.keyCode;

  if (keycode == 46) {
    if (nodot) {
      return false;
    }

    var val = $(thisthis).val();
    if (val == val.replace('.', '')) {
      return true;
    } else {
      return false;
    }
  }

  //comparing pressed keycodes
  if (!(keycode == 8 || keycode == 9 || keycode == 46 || keycode == 116) && (keycode < 48 || keycode > 57)) {
    return false;
  } else {
    return true;
  }
}

//--></script>
</div>
<?php echo $footer; ?>
