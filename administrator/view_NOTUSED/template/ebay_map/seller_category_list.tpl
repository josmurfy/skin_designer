<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_filter; ?>" onclick="$('#filter-rule').toggleClass('hidden-sm hidden-xs');" class="btn btn-default hidden-md hidden-lg"><i class="fa fa-filter"></i></button>
        <button data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary" id="button-add"><i class="fa fa-plus"></i></button>
        <button type="button" form="form-product" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
    <?php if (isset($error_warning) && $error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if (isset($success) &&  $success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
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
              <label class="control-label" for="input-ebay-category-id"><?php echo $entry_ebay_category_id; ?></label>
              <input type="text" class="form-control" name="filter_ebay_category_id" id="input-ebay-category-id" value="<?php echo $filter_ebay_category_id; ?>">
            </div>
            <div class="form-group">
              <label class="control-label" for="input-account"><?php echo $entry_ebay_store; ?></label>
              <select class="form-control" name="filter_account_id" id="input-account">
                <option value=""></option>
                <?php foreach ($accounts as $account) { ?>
                  <option value="<?php echo $account['id']; ?>"><?php echo $account['ebay_connector_store_name']; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label class="control-label" for="input-site"><?php echo $entry_ebay_site; ?></label>
              <select class="form-control" name="filter_ebay_site_id" id="input-site">
                <option value=""></option>
                <?php foreach ($ebay_sites as $key => $site) { ?>
                  <option<?php if ($filter_ebay_site_id == $key && $filter_ebay_site_id != '') { ?><?php echo ' selected'; ?><?php } ?> value="<?php echo $key; ?>"><?php echo $site; ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="form-group">
              <label class="control-label" for="input-category-name"><?php echo $entry_ebay_category_name; ?></label>
              <input type="text" name="filter_ebay_category_name" value="<?php echo $filter_ebay_category_name; ?>" id="input-category-name" class="form-control" placeholder="<?php echo $entry_ebay_category_name; ?>"/>
            </div>
            <div class="form-group">
              <label class="control-label" for="input-level"><?php echo $entry_ebay_category_level; ?></label>
              <select name="filter_category_level" id="input-level" class="form-control">
                <option value=""></option>
                <?php for ($i = 1; $i <=5; $i ++) { ?>
                   <option value="<?php echo $i; ?>"<?php if ($filter_category_level == $i) { ?><?php echo ' selected'; ?><?php } ?>><?php echo 'Level ' . $i; ?></option>
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
            <form action="" method="post" enctype="multipart/form-data" id="form-product">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td style="width: 1px;" class="text-center">
                        <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'wec.ebay_category_id') { ?>
                          <a href="<?php echo $sort_category_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ebay_category_id; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_category_id; ?>"><?php echo $column_ebay_category_id; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'ebay_category_name') { ?>
                          <a href="<?php echo $sort_category_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ebay_category_name; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_category_name; ?>"><?php echo $column_ebay_category_name; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'ebay_category_level') { ?>
                          <a href="<?php echo $sort_category_level; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ebay_category_level; ?></a>
                        <?php } else { ?>
                         <a href="<?php echo $sort_category_level; ?>"><?php echo $column_ebay_category_level; ?></a>
                       <?php } ?>
                      </td>
                      <td class="text-left"><?php echo $column_ebay_site; ?></td>
                      <td class="text-left"><?php echo $column_ebay_store; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if ($categories) { ?>
                  <?php foreach ($categories as $category) { ?>
                  <tr>
                    <td class="text-center"><?php if (in_array($category['id'], $selected)) { ?>
                      <input type="checkbox" name="selected[]" value="<?php echo $category['id']; ?>" checked="checked" />
                    <?php } else { ?>
                      <input type="checkbox" name="selected[]" value="<?php echo $category['id']; ?>" />
                    <?php } ?>
                    </td>
                    <td class="text-left"><?php echo $category['ebay_category_id']; ?></td>
                    <td class="text-left"><?php echo $category['ebay_category_name']; ?></td>
                    <td class="text-center"><?php echo $category['ebay_category_level']; ?></td>
                    <td class="text-left"><?php echo $category['ebay_site_name']; ?></td>
                    <td class="text-right"><?php echo $category['ebay_connector_store_name']; ?></td>
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
  </div>
  <div class="modal-category" id="category-modal">
    <div class="category-modal-content">
      <span class="category-modal-close">&times;</span>
      <div class="category-modal-header">
        <h3><?php echo $heading_add_category; ?></h3>
      </div>
      <div class="modal-body">
        <form class="form-horizontal" method="post" id="form-category">
          <div class="form-group">
            <label class="col-sm-3 control-label" for="select-store"><?php echo $entry_ebay_store; ?></label>
            <div class="col-sm-9">
              <select class="form-control" name="account">
                <?php foreach ($accounts as $account) { ?>
                  <option value="<?php echo $account['id']; ?>"><?php echo $account['ebay_connector_store_name']; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="pull-right">
            <button id="button-import" type="button" class="btn btn-primary"><?php echo $button_import; ?></button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <style media="screen">
  .modal-category {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 999; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    background: -webkit-gradient(linear, left top, left 25, from(#FFFFFF), color-stop(4%, #A4FFA4), to(rgba(0, 0, 0, 0.61)));
  }

  /* Modal Content */
  .category-modal-content {
    background-color: #f1f1f1;
    margin: auto;
    padding: 20px;
    border: 1px solid #099a314a;
    width: 60%;
    min-height: 250px;
    overflow-y: auto;
    margin-bottom: 12px;
  }

  /* The Close Button */
  .category-modal-close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    position: relative;
    right: -18px;
    top: -30px;
  }
  .category-modal-close:hover,
  .category-modal-close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
  }
  .category-modal-header {
    text-align: center;
  }
  .no-event {
    pointer-events: none;
  }
  #button-import {
    margin-right: 15px;
  }
  </style>
  <script type="text/javascript">
  $(document).on('click', '#button-add', function() {
    $('#category-modal').fadeIn();
  });

  $(document).on('click', '.category-modal-close', function() {
    $('#category-modal').fadeOut();
  });

  var total_category = 0;
  var row = <?php echo $row; ?>;
  if (row == '' || row == 0) {
    row = 20;
  }
  $(document).on('click', '#button-import', function() {
    $('#progress-div').remove();
    $('#form-category .alert').remove();

    var account_id = $('select[name=\'account\'] option:selected').val();

    $.ajax({
      url: 'index.php?route=ebay_map/seller_category/fetchEbayCategories&token=<?php echo $token; ?>',
      type: 'post',
      dataType: 'json',
      data: {account_id : account_id},
      beforeSend: function() {
        $('#button-import').button('loading');
        $('select[name="account"], #button-import, .category-modal-close, button[data-dismiss="alert"]').addClass('no-event');
      },

      success: function(json) {

        if (json['success'] != undefined) {

          $('#form-category').prepend('<div class="alert alert-success alert-dismissible text-left"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

          total_category = json['total_category'];
          var start = 0;

          var html = '';
          html += '<div class="form-group" id="progress-div">';
          html += ' <label class="col-sm-3 control-label" for="select-store">Processing</label>';
          html += ' <div class="col-sm-9">';
          html += '   <div class="progress" style="margin-top: 11px;">';
          html += '     <div class="progress-bar" role="progressbar" aria-valuenow="" aria-valuemin="0" aria-valuemax="100" >';
          html += '       <span class="text-center progress-bar-success"></span>';
          html += '     </div>';
          html += '   </div>';
          html += ' </div>';
          html += '</div>';

          $('#form-category .form-group:first').after(html);

          importCategories(start, account_id);

        }

        if (json['error'] != undefined) {

          $('#form-category').prepend('<div class="alert alert-danger alert-dismissible text-left"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

        }
      },
      complete: function() {
        $('#button-import').button('reset');
        $('select[name="account"], #button-import, .category-modal-close, button[data-dismiss="alert"]').removeClass('no-event');
      }
    });
  });

  function importCategories(start, account_id) {
    $.ajax({
      url: 'index.php?route=ebay_map/seller_category/importCategories&token=<?php echo $token; ?>',
      type: 'post',
      dataType: 'json',
      beforeSend: function() {
        $('select[name="account"]').addClass('no-event');
        $('#button-import').addClass('no-event');
        $('.category-modal-close').addClass('no-event');
      },
      data: {start: start, account_id: account_id},
      success: function(json) {

        if (json['success']) {
          $('#form-category .alert').remove();
          $('#form-category').prepend('<div class="alert alert-success alert-dismissible text-left"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

          $('.progress-bar-success').text(((start + row)/total_category) * 100 + '% Imported');
          $('.progress-bar').attr('aria-valuenow', ((start + row)/total_category) * 100);
          $('.progress-bar').css('width', ((start + row)/total_category) * 100 + '%');

          if ((start + row) < total_category) {
            start = start + row;
            importCategories(start, account_id);
          } else {
            $('.progress-bar-success').text('100% Imported');
            $('.progress-bar').attr('aria-valuenow', 100);
            $('.progress-bar').css('width', 100 + '%').addClass('progress-bar-success');
            $('select[name="account"], #button-import, .category-modal-close, button[data-dismiss="alert"]').removeClass('no-event');
          }
        }
      },
      complete: function() {
        $('select[name="account"]').removeClass('no-event');
        $('#button-import').removeClass('no-event');
        $('.category-modal-close').removeClass('no-event');
      }
    });
  }

  $('#button-filter').on('click', function() {
  	var url = '';

  	var filter_ebay_category_id = $('input[name=\'filter_ebay_category_id\']').val();

  	if (filter_ebay_category_id) {
  		url += '&filter_ebay_category_id=' + encodeURIComponent(filter_ebay_category_id);
  	}

  	var filter_account_id = $('select[name=\'filter_account_id\'] option:selected').val();

  	if (filter_account_id) {
  		url += '&filter_account_id=' + encodeURIComponent(filter_account_id);
  	}

  	var filter_ebay_site_id = $('select[name=\'filter_ebay_site_id\'] option:selected').val();

  	if (filter_ebay_site_id) {
  		url += '&filter_ebay_site_id=' + encodeURIComponent(filter_ebay_site_id);
  	}

  	var filter_ebay_category_name = $('input[name=\'filter_ebay_category_name\']').val();

  	if (filter_ebay_category_name) {
  		url += '&filter_ebay_category_name=' + encodeURIComponent(filter_ebay_category_name);
  	}

  	var filter_category_level = $('select[name=\'filter_category_level\']').val();

  	if (filter_category_level !== '') {
  		url += '&filter_category_level=' + encodeURIComponent(filter_category_level);
  	}

  	location = 'index.php?route=ebay_map/seller_category&token=<?php echo $token; ?>' + url;
  });

  $(document).on('click', '#button-clear', function() {
    location = 'index.php?route=ebay_map/seller_category&token=<?php echo $token; ?>';
  });
</script>
</div>
<?php echo $footer; ?>
