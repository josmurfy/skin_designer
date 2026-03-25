<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<link href="view/stylesheet/csspin.css" rel="stylesheet" type="text/css"/>
<link href="view/stylesheet/style.css" rel="stylesheet" type="text/css"/>
<link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet" type="text/css">
<style type="text/css">
  .block_div{
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
    top: 35%;
  }
  .tabs-left > .li-format{
    margin:12px 0;
    margin-right: -18px;
    border-left: 3px solid #1978ab;
    float: none;
  }
  .tabs-left > .li-format > a{
    border-radius: 0;
    border-top: 1px solid #e8e8e8;
    border-bottom: 1px solid #e8e8e8;
  }
  .tabs-left > li.active{
    border-left: 3px solid #E22C5C;
  }
    .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus{
    border-bottom: 1px solid #e8e8e8;
    border-right: none;
  }
</style>
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-ebay-account" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i><?php echo $button_save; ?></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_add; ?></h3>
      </div>
      <div class="panel-body">

          <div class="col-sm-3" id="ebay_left_link">
              <div class="panel-group panel-primary" id="accordion_ebay" role="tablist" aria-multiselectable="true">
              <div class="panel">
                <div class="panel-heading" role="tab" id="headingOne">
                  <h4 class="panel-title">
                    <center><b><a role="button" data-toggle="collapse" data-parent="#accordion_ebay" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                      <?php echo strtoupper($entry_ebay_account_info); ?>
                    </a></b></center>
                  </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                  <div class="panel-body">
                  <!-- Nav tabs -->
                  <ul class="nav nav-tabs tabs-left"><!-- 'tabs-right' for right tabs -->
                    <li class="active li-format"><a href="#add-ebay-account" data-toggle="tab"><?php echo $text_account_tab; ?></a></li>
                    <?php if ($account_id) { ?>
                      <li class="li-format"><a href="#account_category_map" data-toggle="tab"><?php echo $text_category_tab; ?></a></li>
                      <li class="li-format"><a href="#account_product_map" data-toggle="tab"><?php echo $text_product_tab; ?></a></li>
                      <li class="li-format"><a href="#account_order_map" data-toggle="tab"><?php echo $text_order_tab; ?></a></li>
                      <li class="li-format"><a href="#account_import_to_ebay" data-toggle="tab"><?php echo $text_import_tab; ?></a></li>
                        <li class="li-format" style="float: none;"><a href="#product_scheduling" data-toggle="tab"><?php echo "Scheduled Product"; ?></a></li>
                    <?php } ?>
                  </ul>
                  </div>
                </div>
              </div>
            </div>
          </div><!--Col-sm-3-->

          <div class="col-sm-9">
            <!-- Tab panes -->
              <div class="tab-content" id="ebay_right_link">
                <div class="tab-pane active" id="add-ebay-account">
                  <h3><?php echo $text_ebay_account; ?>  </h3>
                  <hr>
                  <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ebay-account" class="form-horizontal">
                  <input type="hidden" name="account_id" value="<?php echo $account_id; ?>" />
                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-store-name"><?php echo $entry_store_name; ?></label>
                      <div class="col-sm-9">
                        <input type="text" name="ebay_connector_store_name" id="input-store-name" value="<?php if ($ebay_connector_store_name) { ?><?php echo $ebay_connector_store_name; ?><?php } ?>" <?php if ($account_id) { ?><?php echo 'readonly'; ?><?php } ?> class="form-control" />
                        <?php if ($error_ebay_connector_store_exist) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_store_exist; ?></div>
                        <?php } ?>
                        <?php if ($error_ebay_connector_store_name) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_store_name; ?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-ebay-sites"><?php echo $entry_ebay_sites; ?></label>
                      <div class="col-sm-9">
                        <select name="ebay_connector_ebay_sites" id="input-ebay-sites" class="form-control">
                          <?php foreach ($ebay_sites as $key => $site) { ?>
                            <option value="<?php echo $key; ?>" <?php if ($ebay_connector_ebay_sites && $ebay_connector_ebay_sites == $key) { ?><?php echo 'selected'; ?><?php } ?>><?php echo $site; ?></option>
                          <?php } ?>
                        </select>
                        <?php if ($error_ebay_connector_ebay_sites) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_ebay_sites; ?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-ebay-user-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_user_id; ?>"><?php echo $entry_ebay_user_id; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="ebay_connector_ebay_user_id" id="input-ebay-user-id" value="<?php if ($ebay_connector_ebay_user_id) { ?><?php echo $ebay_connector_ebay_user_id; ?><?php } ?>" placeholder="<?php echo $placeholder_user_id; ?>" />
                        <?php if ($error_ebay_connector_ebay_user_id) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_ebay_user_id; ?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-ebay-auth-token"><span data-toggle="tooltip" title="<?php echo $help_ebay_auth_token; ?>"><?php echo $entry_ebay_auth_token; ?></span></label>
                      <div class="col-sm-9">
                        <textarea class="form-control" name="ebay_connector_ebay_auth_token" id="input-ebay-auth-token"  rows="5" placeholder="<?php echo $placeholder_auth_token; ?>"><?php if ($ebay_connector_ebay_auth_token) { ?><?php echo $ebay_connector_ebay_auth_token; ?><?php } ?></textarea>
                        <?php if ($error_ebay_connector_ebay_auth_token) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_ebay_auth_token; ?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-ebay-app-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_app_id; ?>"><?php echo $entry_ebay_app_id; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="ebay_connector_ebay_application_id" id="input-ebay-app-id" value="<?php if ($ebay_connector_ebay_application_id) { ?><?php echo $ebay_connector_ebay_application_id; ?><?php } ?>" placeholder="<?php echo $placeholder_app_id; ?>" />
                        <?php if ($error_ebay_connector_ebay_application_id) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_ebay_application_id; ?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-ebay-dev-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_dev_id; ?>"><?php echo $entry_ebay_dev_id; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="ebay_connector_ebay_developer_id" id="input-ebay-dev-id" value="<?php if ($ebay_connector_ebay_developer_id) { ?><?php echo $ebay_connector_ebay_developer_id; ?><?php } ?>" placeholder="<?php echo $placeholder_dev_id; ?>" />
                        <?php if ($error_ebay_connector_ebay_developer_id) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_ebay_developer_id; ?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-ebay-cert-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_cert_id; ?>"><?php echo $entry_ebay_cert_id; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="ebay_connector_ebay_certification_id" id="input-ebay-cert-id" value="<?php if ($ebay_connector_ebay_certification_id) { ?><?php echo $ebay_connector_ebay_certification_id; ?><?php } ?>" placeholder="<?php echo $placeholder_cert_id; ?>" />
                        <?php if ($error_ebay_connector_ebay_certification_id) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_ebay_certification_id; ?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-ebay-currency"><?php echo $entry_ebay_supported_currency; ?></label>
                      <div class="col-sm-9">
                        <select name="ebay_connector_ebay_currency" id="input-ebay-currency" class="form-control">
                          <?php foreach ($ebaySiteCurrency as $key => $currency) { ?>
                            <option value="<?php echo $key; ?>" <?php if ($ebay_connector_ebay_currency && $ebay_connector_ebay_currency == $key) { ?><?php echo 'selected'; ?><?php } ?>><?php echo $currency; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-shop-postal-code"><span data-toggle="tooltip" title="<?php echo $help_shop_postal_code; ?>"><?php echo $entry_shop_postal_code; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="ebay_connector_ebay_shop_postal_code" id="input-shop-postal-code" value="<?php if ($ebay_connector_ebay_shop_postal_code) { ?><?php echo $ebay_connector_ebay_shop_postal_code; ?><?php } ?>" placeholder="<?php echo $placeholder_post_code; ?>" />
                        <?php if ($error_ebay_connector_ebay_shop_postal_code) { ?>
                          <div class="text-danger"><?php echo $error_ebay_connector_ebay_shop_postal_code; ?></div>
                        <?php } ?>
                      </div>
                    </div>
                    <hr>
                    <h3><?php echo $text_shipping; ?></h3>
                    <hr>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-priority"><span data-toggle="tooltip" title="<?php echo $help_shipping_priority; ?>"><?php echo $entry_shipping_priority; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="input-priority" name="shipping_priority" value="<?php echo $shipping_priority; ?>" placeholder="<?php echo $entry_shipping_priority; ?>" onkeypress="return validate(event, this, true);">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-service"><span data-toggle="tooltip" title="<?php echo $help_shipping_service; ?>"><?php echo $entry_shipping_service; ?></span></label>
                      <div class="col-sm-9">
                        <select class="form-control" name="shipping_service">
                          <?php foreach ($text_shipping_services as $key => $text_shipping_service) { ?>
                            <option<?php if ($shipping_service == $key) { ?><?php echo ' selected'; ?><?php } ?> value="<?php echo $key; ?>"><?php echo $text_shipping_service; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-shipping-cost"><span data-toggle="tooltip" title="<?php echo $help_shipping_cost; ?>"><?php echo $entry_shipping_cost; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="input-shipping-cost" name="shipping_cost" value="<?php echo $shipping_cost; ?>" placeholder="<?php echo $entry_shipping_cost; ?>" onkeypress="return validate(event, this);">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-additional"><span data-toggle="tooltip" title="<?php echo $help_shipping_add_cost; ?>"><?php echo $entry_shipping_add_cost; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="input-additional" name="shipping_additional_cost" value="<?php echo $shipping_additional_cost; ?>" placeholder="<?php echo $entry_shipping_add_cost; ?>" onkeypress="return validate(event, this);">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-min-time"><span data-toggle="tooltip" title="<?php echo $help_shipping_min_time; ?>"><?php echo $entry_shipping_min_time; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" name="shipping_min_time" value="<?php echo $shipping_min_time; ?>" placeholder="<?php echo $entry_shipping_min_time; ?>" onkeypress="return validate(event, this, true);">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-max-time"><span data-toggle="tooltip" title="<?php echo $help_shipping_max_time; ?>"><?php echo $entry_shipping_max_time; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="input-max-time" name="shipping_max_time" value="<?php echo $shipping_max_time; ?>" placeholder="<?php echo $entry_shipping_max_time; ?>" onkeypress="return validate(event, this, true);">
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-free-shipping"><span data-toggle="tooltip" title="<?php echo $help_shipping_free_shipping; ?>"><?php echo $entry_shipping_free_shipping; ?></span></label>
                      <div class="col-sm-9">
                        <select class="form-control" name="free_shipping_status" id="input-free-shipping">
                          <?php if ($free_shipping_status) { ?>
                          <option value="1"><?php echo $text_enabled; ?></option>
                          <option value="0"><?php echo $text_disabled; ?></option>
                        <?php } else { ?>
                          <option value="0"><?php echo $text_disabled; ?></option>
                          <option value="1"><?php echo $text_enabled; ?></option>
                        <?php } ?>
                        </select>
                      </div>
                    </div>
                  </form>
                </div><!--add-ebay-account-->

                <div class="tab-pane" id="account_category_map">
                  <?php echo $category_map; ?>
                </div><!--account_category_map-->

                <div class="tab-pane" id="account_product_map">
                  <?php echo $product_map; ?>
                </div><!--account_product_map-->

                <div class="tab-pane" id="account_order_map">
                  <?php echo $order_map; ?>
                </div><!--account_order_map-->

                <div class="tab-pane" id="account_import_to_ebay">
                  <?php echo $export_product; ?>
                </div><!--account_import_to_ebay-->
                <div class="tab-pane" id="product_scheduling">
                  <?php echo $schedule_product; ?>
                 </div>
              </div><!--tab-content-col-sm-9-->
          </div><!--Col-sm-9-->


      </div>
    </div>
  </div>
  <div class="block_div">
    <div class="block_spinner">
      <div class="cp-spinner cp-balls"></div>
    </div>
  </div>
</div>
<script type="text/javascript">
//Function to allow only numbers to textbox
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
