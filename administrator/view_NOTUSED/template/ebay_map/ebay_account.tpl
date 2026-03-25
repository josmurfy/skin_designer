<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">

      	<a href="<?php echo $add_account; ?>" data-toggle="tooltip" title="<?php echo $button_add_account; ?>" class="btn btn-success"><i class="fa fa-plus" aria-hidden="true"></i> <?php echo $button_add_account; ?></a>
        <button type="button" data-toggle="tooltip" id="account-delete" title="<?php echo $button_delete; ?>" class="btn btn-danger" ><i class="fa fa-trash-o"></i></button>

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
      <div class="panel-heading"  style="display:inline-block;width:100%;">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_ebay_account_list; ?></h3>
      </div>
      <div class="panel-body">
          <div class="well">
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label class="control-label" for="input-account"><?php echo $column_account_id; ?></label>
                  <input type="text" name="filter_account_id" value="<?php echo $filter_account_id; ?>" placeholder="<?php echo $column_account_id; ?>" id="input-account" class="form-control" />
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label class="control-label" for="input-store-name"><?php echo $column_ebay_store_name; ?></label>
                  <input type="text" name="filter_store_name" value="<?php echo $filter_store_name; ?>" placeholder="<?php echo $column_ebay_store_name; ?>" id="input-store-name" class="form-control" />
                </div>
              </div>
              <div class="col-sm-4">
                <div class="form-group">
                  <label class="control-label" for="input-ebay-user-id"><?php echo $column_ebay_user_id; ?></label>
                  <input type="text" name="filter_ebay_user_id" value="<?php echo $filter_ebay_user_id; ?>" placeholder="<?php echo $column_ebay_user_id; ?>" id="input-ebay-user-id" class="form-control" />
                </div>
                <a href="<?php echo $clear_filter; ?>" type="button" id="button-filter-clear" class="btn btn-danger pull-right" style="margin-left:5px; "><i class="fa fa-eraser" aria-hidden="true"></i> <?php echo "Clear"; ?></a>
                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
              </div>
            </div>
          </div>
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-ebay-account">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'id') { ?>
                    <a href="<?php echo $sort_account_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_account_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_account_id; ?>"><?php echo $column_account_id; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'ebay_connector_store_name') { ?>
                    <a href="<?php echo $sort_ebay_store_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ebay_store_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ebay_store_name; ?>"><?php echo $column_ebay_store_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'ebay_connector_ebay_user_id') { ?>
                    <a href="<?php echo $sort_ebay_user_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_ebay_user_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_ebay_user_id; ?>"><?php echo $column_ebay_user_id; ?></a>
                    <?php } ?></td>
                  <td class="text-center"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($ebay_accounts) { ?>
                <?php foreach ($ebay_accounts as $account) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($account['account_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $account['account_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $account['account_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $account['account_id']; ?></td>
                  <td class="text-left"><?php echo $account['store_name']; ?></td>
                  <td class="text-left"><?php echo $account['ebay_user_id']; ?></td>
                  <td class="text-center">
                    <a href="<?php echo $account['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_manage; ?>" class="btn btn-warning"><i class="fa fa-pencil" aria-hidden="true"></i> <?php echo $button_manage; ?></a>
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
$('#button-filter').on('click', function() {
  var url = 'index.php?route=ebay_map/ebay_account&token=<?php echo $token; ?>';

  var filter_account_id = $('input[name=\'filter_account_id\']').val();

  if (filter_account_id) {
    url += '&filter_account_id=' + encodeURIComponent(filter_account_id);
  }

  var filter_store_name = $('input[name=\'filter_store_name\']').val();

  if (filter_store_name) {
    url += '&filter_store_name=' + encodeURIComponent(filter_store_name);
  }

  var filter_ebay_user_id = $('input[name=\'filter_ebay_user_id\']').val();

  if (filter_ebay_user_id) {
    url += '&filter_ebay_user_id=' + encodeURIComponent(filter_ebay_user_id);
  }

  location = url;
});

$('#account-delete').on('click', function(){
  var status = false;
    $("#form-ebay-account input[type=checkbox]:checked").each(function(key, val){
      if($(val).val()){
        status = true;
      }
    })
    if(status == true){
      confirm('<?php echo $text_confirm; ?>') ? $('#form-ebay-account').submit() : false;
    }else{
       alert('<?php echo $error_select_record; ?>');
    }
})
//--></script>
<?php echo $footer; ?>
