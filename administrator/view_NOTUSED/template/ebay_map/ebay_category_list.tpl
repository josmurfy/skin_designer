<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/css/bootstrap-select.min.css" />
  <link href="view/stylesheet/csspin.css" rel="stylesheet" type="text/css"/>
  <style type="text/css">
  .manage-progress {
    display: none;
    z-index: 1;
  }
  .cp-round::before, .cp-round::after{
    width: 35px;
    left:8px;
    height: 35px;
    /*top: 25px;*/
    margin-top: 25px;
  }

  #new_category_map_section{
    display: none;
  }
  .alert-success{
    background-color: #8cc152;
    border-color: #8cc152;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
  }
  .alert-danger{
    background-color: #ea5744;
    border-color: #ea5744;
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
  }
  .block_div{
    overflow-x: hidden;
    background-color: #000;
    height: 100%;
    left: 0;
    opacity: 0.5;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 99;
    display: none;
  }
  .block_spinner {
    left: 50%;
    position: relative;
    top: 18%;
  }
  </style>
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <div class="row-fluid pull-left">
          <select class="selectpicker" id="input-account" data-show-subtext="true" data-live-search="true">
            <?php foreach ($ebay_accounts as $ebay_account) { ?>
              <option value="<?php echo $ebay_account['id']; ?>" site-id="<?php echo $ebay_account['ebay_connector_ebay_sites'] ?>"><?php echo $ebay_account['ebay_connector_store_name']; ?></option>
            <?php } ?>
          </select>
          <button type="button" class="btn btn-primary" onclick="importStoreCategories();" data-toggle="tooltip" title="<?php echo $button_import_category; ?>"><i class="fa fa-download"></i></button>&nbsp;
        </div>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-ebay-category').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
      <div class="col-sm-12 form-group manage_progress hide">
        <label class="col-sm-2 control-label"><?php echo "Processing..."; ?></label>
        <div class="col-sm-10" style="margin-top:10px">
          <div class="progress">
            <div id="progress-bar" class="progress-bar" style="width: 0%;"></div>
          </div>
          <div id="progress-text"></div>
        </div>
      </div>
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
                <label class="control-label" for="input-ebay-category-id"><?php echo $entry_ebay_category_id; ?></label>
                <input type="text" name="filter_category_id" value="<?php echo $filter_category_id; ?>" placeholder="<?php echo $entry_ebay_category_id; ?>" id="input-ebay-category-id" class="form-control" />
              </div>
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
            </div>

            <div class="col-sm-4">
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
                <label class="control-label" for="input-ebay-category-level"><?php echo $entry_ebay_category_level; ?></label>
                <select class="form-control" name="filter_category_level">
                  <option value="*"><?php echo $text_select_level; ?></option>
                  <?php for ($i=1; $i <= 5; $i++) { ?>
                  	 <option value="<?php echo $i; ?>" <?php if(isset($filter_category_level) && $i == $filter_category_level){ echo 'selected'; } ?>><?php echo 'Level '.$i; ?></option>
                  <?php } ?>
                </select>
              </div>
              <a href="<?php echo $clear; ?>" id="button-clear" class="btn btn-default pull-right" style="border-radius:0;"><i class="fa fa-eraser" aria-hidden="true"></i> <?php echo $button_clear_filter; ?></a>
              <button type="button" id="button-filter" onclick="filter();" class="btn btn-warning pull-right" style="border-radius:0;"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>
          </div>
        </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-ebay-category">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'sort_ebay_category_id') { ?>
                    <a href="<?php echo $sort_ebay_category_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_category_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ebay_category_id; ?>"><?php echo $column_category_id; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'sort_ebay_category_name') { ?>
                    <a href="<?php echo $sort_ebay_category_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_category_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ebay_category_name; ?>"><?php echo $column_category_name; ?></a>
                    <?php } ?></td>
                  <td class="text-center"><?php if ($sort == 'sort_ebay_category_level') { ?>
                    <a href="<?php echo $sort_ebay_category_level; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_category_level; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ebay_category_level; ?>"><?php echo $column_category_level; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php echo $column_site_name; ?></td>
                  <!-- <td class="text-right"><?php echo $column_action; ?></td> -->
                </tr>
              </thead>
              <tbody>
                <?php if ($ebay_categories) { ?>
                <?php foreach ($ebay_categories as $category) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($category['id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $category['id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $category['id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $category['ebay_category_id']; ?></td>
                  <td class="text-left"><?php echo $category['ebay_category_name']; ?></td>
                  <td class="text-center"><?php echo $category['ebay_category_level']; ?></td>
                  <td class="text-left"><?php echo $category['ebay_site_name']; ?></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
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
<div class="block_div">
  <div class="block_spinner">
    <div class="cp-spinner cp-balls"></div>
  </div>
</div>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.3/js/bootstrap-select.min.js"></script>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=ebay_map/ebay_category_list&token=<?php echo $token; ?>';

	var filter_ebay_category_name = $('input[name=\'filter_ebay_category_name\']').val();

	if (filter_ebay_category_name) {
		url += '&filter_ebay_category_name=' + encodeURIComponent(filter_ebay_category_name);
	}

  var filter_category_id = $('input[name=\'filter_category_id\']').val();

  if (filter_category_id) {
    url += '&filter_category_id=' + encodeURIComponent(filter_category_id);
  }

	var filter_category_level = $('select[name=\'filter_category_level\']').val();

	if (filter_category_level != '*') {
		url += '&filter_category_level=' + encodeURIComponent(filter_category_level);
	}

  var filter_ebay_site_id = $('select[name=\'filter_ebay_site_id\']').val();

	if (filter_ebay_site_id != '*') {
		url += '&filter_ebay_site_id=' + encodeURIComponent(filter_ebay_site_id);
	}

	location = url;
}
//--></script>
<script type="text/javascript">

$('input[name=\'filter_ebay_category_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_category_list/autocomplete&token=<?php echo $token; ?>&filter_ebay_category_name=' +  encodeURIComponent(request),
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

</script>
<style media="screen">
.btn-secondary {
  color: #fff;
  background-color: #6c757d;
  border-color: #6c757d;
}

.btn-secondary:hover {
  color: #fff;
  background-color: #5a6268;
  border-color: #545b62;
}

.btn-secondary:focus, .btn-secondary.focus {
  box-shadow: 0 0 0 0.2rem rgba(130, 138, 145, 0.5);
}
</style>
<script type="text/javascript">
var requests    = []; var totalImportedcategory = 0; var total = 0;
var start_page  = 1;
function importStoreCategories() {
  $('.alert').remove();
  var account_id = $('#input-account option:selected').val();
  if (account_id === undefined) {
    $('.breadcrumb').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_account; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>');
    return;
  }
  var ebay_site_id = $('#input-account option:selected').attr('site-id');

  if (typeof timer != 'undefined') {
    clearInterval(timer);
  }
  timer = setInterval(function() {
    clearInterval(timer);
    // Reset everything
    $('.alert').remove();
    $('.manage-progress').css('display','block');
    $('#progress-bar').css('width', '0%');
    $('#progress-bar').removeClass('progress-bar-danger progress-bar-success');
    $('#progress-text').html('<div class="text-info text-left"><?php echo $text_sync_process_category; ?></div>');

    $.ajax({
        url: 'index.php?route=extension/module/ebay_connector/_importEbayCategories&token=<?php echo $token; ?>&account_id='+ account_id,
        dataType: 'json',
        beforeSend: function() {
          $('.block_div').css('display','block');
          $('.container-fluid > .alert').remove();
        },
        complete:function() {
            NextStep();
        },
        success: function(json) {
          $('.manage_progress').removeClass('hide');
          if (json.error) {
            $('#progress-bar').addClass('progress-bar-danger');
            $('#progress-text').html('<div class="text-danger">' + json.error + '</div>');
          }

          total = json.totalcategory;
          for (var start_page = 1; start_page <= json.totalcategory; start_page++) {
            requests.push({
              url     : 'index.php?route=extension/module/ebay_connector/start_syncronize&token=<?php echo $token; ?>',
              type: 'post',
              dataType: 'json',
              async:   true,
              data: {account_id: account_id, page: start_page, ebay_site_id: ebay_site_id},
              success: function(json_response) {
                if (json_response.error) {
                  $('#progress-text').html('<div class="text-danger"> ' + json_response.error_count + ' </div>');
                }
                if (json_response.success_count) {
                  $('#progress-text').html('<div class="text-success text-right">' + json_response.success_msg + '</div>');
                  totalImportedcategory = totalImportedcategory + json_response.success_count;
                }
                if (json_response.success_already) {
                  $('#progress-text').html('<div class="text-success text-right">' + json_response.success_already_msg + '</div>');
                }
              }
            });
          }
          if (json['error'] != undefined && json['error']) {
            $('.breadcrumb').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            return false;
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
   }, 500);
}

var NextStep = function() {
  if (requests.length) {
    $('#progress-bar').css('width', (100 - (requests.length / total) * 100) + '%');
    $.ajax(requests.shift()).then(NextStep);
  } else {
    $('#progress-bar').css('width', '100%');
    $('#progress-text').html('<div class="text-success">Total ' + totalImportedcategory + ' category imported in opencart store from ebay store!"</div>');
    $('#progress-bar').addClass('progress-bar-success');
    $('.block_div').css('display','none');
    setTimeout(function() {
      $('.manage-progress').fadeOut();
      location.reload();
    }, 4000);
  }
};
</script>
<?php echo $footer; ?>
