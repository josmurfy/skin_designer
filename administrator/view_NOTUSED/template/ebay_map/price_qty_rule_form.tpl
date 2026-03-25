<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-product" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-product" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-3 control-label" for="select-rule-for"><span data-toggle="tooltip" title="<?php echo $help_rule_for; ?>"><?php echo $entry_rule_for; ?></span></label>
            <div class="col-sm-9">
              <select id="select-rule-for" name="rule_for" class="form-control">
                <?php if ($rule_for == 'price') { ?>
                  <option selected="selected" value="price"><?php echo $text_price; ?></option>
                <?php } else { ?>
                    <option value="price"><?php echo $text_price; ?></option>
                <?php } ?>
                <?php if ($rule_for == 'qty') { ?>
                  <option selected="selected" value="qty"><?php echo $text_quantity; ?></option>
                <?php } else { ?>
                  <option value="qty"><?php echo $text_quantity; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="input-portation" class="col-sm-3 control-label"><span data-toggle="tooltip" title="<?php echo $help_portation; ?>"><?php echo $entry_portation; ?></span></label>
            <div class="col-sm-9">
              <select class="form-control" name="portation">
                <?php if ($portation == 'import') { ?>
                  <option selected="selected" value="import"><?php echo $text_import; ?></option>
                <?php } else { ?>
                  <option value="import"><?php echo $text_import; ?></option>
                <?php } ?>

                <?php if ($portation == 'export') { ?>
                  <option selected="selected" value="export"><?php echo $text_export; ?></option>
                <?php } else { ?>
                  <option value="export"><?php echo $text_export; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label for="input-min" class="col-sm-3 control-label"><span data-toggle="tooltip" id="tooltip-min"><?php echo $entry_rule_min; ?></span></label>
            <div class="col-sm-9">
              <input type="text"  class="form-control" data-input="number" id="input-min" name="min" value="<?php echo $min; ?>" onkeypress="return validate(event, this)">
              <?php if ($error_min) { ?>
                <div class="text-danger">
                  <?php echo $error_min; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label for="input-max" class="col-sm-3 control-label"><span id="tooltip-max" data-toggle="tooltip"><?php echo $entry_rule_max; ?></span></label>
            <div class="col-sm-9">
              <input type="text"  class="form-control" data-input="number" id="input-max" name="max" value="<?php echo $max; ?>" onkeypress="return validate(event, this)">
              <?php if ($error_max) { ?>
                <div class="text-danger">
                  <?php echo $error_max; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label for="input-value" class="col-sm-3 control-label"></span data-toggle="tooltip" title="<?php echo $help_value; ?>"><?php echo $entry_value; ?></span></label>
            <div class="col-sm-9">
              <input type="text"  class="form-control" data-input="number" id="input-value" name="value" value="<?php echo $value; ?>" onkeypress="return validate(event, this)">
              <?php if ($error_value) { ?>
                <div class="text-danger">
                  <?php echo $error_value; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label for="input-operation-type" class="col-sm-3 control-label"><?php echo $entry_operation_type; ?></label>
            <div class="col-sm-9">
              <select class="form-control" name="operation_type" id="input-operation-type">
                <?php if ($operation_type == '+') { ?>
                  <option selected="selected" value="+"><?php echo $text_increment; ?></option>
                <?php } else { ?>
                  <option value="+"><?php echo $text_increment; ?></option>
                <?php } ?>
                <?php if ($operation_type == '-') { ?>
                  <option selected="selected" value="-"><?php echo $text_decrement; ?></option>
                <?php } else { ?>
                  <option value="-"><?php echo $text_decrement; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label" for="input-operation"><?php echo $entry_operation; ?></label>
            <div class="col-sm-9">
              <select class="form-control" name="operation">
                <?php if ($operation == 'fixed') { ?>
                  <option selected="selected" value="fixed"><?php echo $text_fixed; ?></option>
                <?php } else { ?>
                  <option value="fixed"><?php echo $text_fixed; ?></option>
                <?php } ?>
                <?php if ($operation == 'percentage') { ?>
                  <option selected="selected" value="percentage"><?php echo $text_percentage; ?></option>
                <?php } else { ?>
                  <option value="percentage"><?php echo $text_percentage; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-3 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-9">
              <input type="text" name="sort_order" id="input-sort-order" class="form-control" value="<?php echo $sort_order; ?>" onkeypress="return validate(event, this, true)">
              <?php if ($error_sort_order) { ?>
                <div class="text-danger">
                  <?php echo $error_sort_order; ?>
                </div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-3 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-9">
              <select class="form-control" name="status">
                <?php if ($status == '1') { ?>
                  <option selected="selected" value="1"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                  <option value="1"><?php echo $text_enabled; ?></option>
                <?php } ?>
                <?php if ($status == '0') { ?>
                  <option selected="selected" value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                  <option value="0"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  var min_content = '';
  var max_content = '';
  if ($('#select-rule-for').val() == 'price') {
    min_content = '<?php echo $help_min_price; ?>';
    max_content = '<?php echo $help_max_price; ?>';
  } else {
    min_content = '<?php echo $help_min_quantity; ?>';
    max_content = '<?php echo $help_max_quantity; ?>';
  }
  $(document).on('change', '#select-rule-for', function() {
    if ($(this).val() == 'price') {
      min_content = '<?php echo $help_min_price; ?>';
      max_content = '<?php echo $help_max_price; ?>';
    } else {
      min_content = '<?php echo $help_min_quantity; ?>';
      max_content = '<?php echo $help_max_quantity; ?>';
    }

    $('#tooltip-min').attr({
      'data-original-title': min_content
    });
    $('#tooltip-max').attr({
      'data-original-title': max_content
    });
  });
  $('#tooltip-min').attr({
    title: min_content
  });
  $('#tooltip-max').attr({
    title: max_content
  });
  $(document).on('ready', function() {
    $('.text-danger:first').parent().find('input:first').focus();
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
</script>
<?php echo $footer; ?>
