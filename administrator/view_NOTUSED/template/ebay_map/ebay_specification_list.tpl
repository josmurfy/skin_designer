<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-ebay-specification').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                <label class="control-label" for="input-ebay-category-id"><?php echo $entry_specification_id; ?></label>
                <input type="text" name="filter_specification_id" value="<?php echo $filter_specification_id; ?>" placeholder="<?php echo $entry_specification_id; ?>" id="input-specification-id" class="form-control" />
              </div>
              <div class="form-group">
                <label class="control-label" for="input-ebay-category-name"><?php echo $entry_ebay_category_name; ?></label>
                <div class='input-group'>
                    <input type="text" name="filter_ebay_category_name" value="<?php echo $filter_ebay_category_name; ?>" placeholder="<?php echo $entry_ebay_category_name; ?>" id="input-ebay-category-name" class="form-control" />
                    <span class="input-group-addon"><i class="fa fa-arrow-down" aria-hidden="true"></i></span>
                </div>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-specification-name"><?php echo $entry_specification_name; ?></label>
                <div class='input-group'>
                    <input type="text" name="filter_specification_name" value="<?php echo $filter_specification_name; ?>" placeholder="<?php echo $entry_specification_name; ?>" id="input-specification-name" class="form-control" />
                    <span class="input-group-addon"><i class="fa fa-arrow-down" aria-hidden="true"></i></span>
                </div>
              </div>
              <div class="form-group">
                <label class="control-label" for="input-oc-category-name"><?php echo $entry_oc_category_name; ?></label>
                <div class='input-group'>
                    <input type="text" name="filter_oc_category_name" value="<?php echo $filter_oc_category_name; ?>" placeholder="<?php echo $entry_oc_category_name; ?>" id="input-oc-category-name" class="form-control" />
                    <span class="input-group-addon"><i class="fa fa-arrow-down" aria-hidden="true"></i></span>
                </div>
              </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-group-name"><?php echo $entry_group_name; ?></label>
                <div class='input-group'>
                    <input type="text" name="filter_specification_group_name" value="<?php echo $filter_specification_group_name; ?>" placeholder="<?php echo $entry_group_name; ?>" id="input-group-name" class="form-control" />
                    <span class="input-group-addon"><i class="fa fa-arrow-down" aria-hidden="true"></i></span>
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
                  <td class="text-center"><?php if ($sort == 'sort_attribute_id') { ?>
                    <a href="<?php echo $sort_attribute_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_row_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_attribute_id; ?>"><?php echo $column_row_id; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php if ($sort == 'sort_attribute_name') { ?>
                    <a href="<?php echo $sort_attribute_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_specification_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_attribute_name; ?>"><?php echo $column_specification_name; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php if ($sort == 'sort_attribute_group_name') { ?>
                    <a href="<?php echo $sort_attribute_group_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_group_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_attribute_group_name; ?>"><?php echo $column_group_name; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php if ($sort == 'sort_ebay_category_name') { ?>
                    <a href="<?php echo $sort_ebay_category_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ebay_category_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ebay_category_name; ?>"><?php echo $column_ebay_category_name; ?></a>
                    <?php } ?></td>

                  <td class="text-left"><?php if ($sort == 'sort_oc_category_name') { ?>
                    <a href="<?php echo $sort_oc_category_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_oc_category_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_oc_category_name; ?>"><?php echo $column_oc_category_name; ?></a>
                    <?php } ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($ebay_specifications) { ?>
                <?php foreach ($ebay_specifications as $specification) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($specification['specification_row_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $specification['specification_row_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $specification['specification_row_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-center"><?php echo $specification['attribute_id']; ?></td>
                  <td class="text-left"><?php echo $specification['attribute_name']; ?></td>
                  <td class="text-left"><?php echo $specification['attribute_group_name']; ?></td>
                  <td class="text-left"><?php echo $specification['ebay_category_name']; ?></td>
                  <td class="text-left"><?php echo $specification['oc_category_name']; ?></td>
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
</div>

<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=ebay_map/ebay_specification_list&token=<?php echo $token; ?>';


  var filter_specification_id = $('input[name=\'filter_specification_id\']').val();

  if (filter_specification_id) {
    url += '&filter_specification_id=' + encodeURIComponent(filter_specification_id);
  }

  var filter_specification_name = $('input[name=\'filter_specification_name\']').val();

	if (filter_specification_name) {
		url += '&filter_specification_name=' + encodeURIComponent(filter_specification_name);
	}

  var filter_specification_group_name = $('input[name=\'filter_specification_group_name\']').val();

  if (filter_specification_group_name) {
    url += '&filter_specification_group_name=' + encodeURIComponent(filter_specification_group_name);
  }

	var filter_ebay_category_name = $('input[name=\'filter_ebay_category_name\']').val();

	if (filter_ebay_category_name) {
		url += '&filter_ebay_category_name=' + encodeURIComponent(filter_ebay_category_name);
	}

  var filter_oc_category_name = $('input[name=\'filter_oc_category_name\']').val();

	if (filter_oc_category_name) {
		url += '&filter_oc_category_name=' + encodeURIComponent(filter_oc_category_name);
	}

	location = url;
}
//--></script>
<script type="text/javascript">

$('input[name=\'filter_ebay_category_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_specification_list/autocomplete&token=<?php echo $token; ?>&filter_ebay_category_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.category_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_ebay_category_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});


$('input[name=\'filter_specification_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_specification_list/autocomplete&token=<?php echo $token; ?>&filter_specification_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.category_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_specification_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

$('input[name=\'filter_specification_group_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_specification_list/autocomplete&token=<?php echo $token; ?>&filter_specification_group_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.category_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_specification_group_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});



$('input[name=\'filter_oc_category_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_specification_list/autocomplete&token=<?php echo $token; ?>&filter_oc_category_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.category_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_oc_category_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

</script>
<?php echo $footer; ?>
