<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
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

  .animationload {
    background-color: #000;
    height: 100%;
    left: 0;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 1000;
  }
  .osahanloading {
    animation: 1.5s linear 0s normal none infinite running osahanloading;
    background: red none repeat scroll 0 0;
    border-radius: 50px;
    height: 50px;
    left: 50%;
    margin-left: -25px;
    margin-top: 250px;
    position: absolute;
    top: 10%;
    width: 50px;
  }
  .osahanloading::after {
    animation: 1.5s linear 0s normal none infinite running osahanloading_after;
    border-color: #85d6de transparent;
    border-radius: 80px;
    border-style: solid;
    border-width: 10px;
    content: "";
    height: 80px;
    left: -15px;
    position: absolute;
    top: -15px;
    width: 80px;
  }
  @keyframes osahanloading {
  0% {
    transform: rotate(0deg);
  }
  50% {
    background: #85d6de none repeat scroll 0 0;
    transform: rotate(180deg);
  }
  100% {
    transform: rotate(360deg);
  }
  }
</style>
  <div class="page-header">
    <div class="container-fluid">
      <?php if ($is_installed_composer) { ?>
      <div class="alert alert-warning"><i class="fa fa-exclamation-circle"></i> <?php echo $is_installed_composer; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
      </div>
      <?php } ?>
      <div class="pull-right">
        <button type="submit" form="form-account" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>

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
    <?php if ($invalid_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $invalid_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-account" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-3 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-9">
              <select name="ebay_connector_status" id="input-status" class="form-control">
                <?php if ($ebay_connector_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div id="accordion" role="tablist" aria-multiselectable="true">
            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                  <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_general" aria-expanded="true" aria-controls="collapse_general">
                    <?php echo $panel_general; ?>
                  </a>
                </h4>
              </div>
              <div id="collapse_general" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-sites"><?php echo $entry_ebay_sites; ?></label>
                    <div class="col-sm-9">
                      <select name="ebay_connector_ebay_sites" id="input-ebay-sites" class="form-control">
                        <?php foreach ($ebay_sites as $key => $site) { ?>
                          <option value="<?php echo $key; ?>" <?php if(isset($ebay_connector_ebay_sites) && $ebay_connector_ebay_sites == $key){ echo 'selected'; } ?>><?php echo $site; ?></option>
                        <?php } ?>
                      </select>

                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-mode"><?php echo $entry_ebay_mode; ?></label>
                    <div class="col-sm-9">
                      <select name="ebay_connector_ebay_mode" id="input-ebay-mode" class="form-control">
                        <option value="sandbox" <?php if(isset($ebay_connector_ebay_mode) && $ebay_connector_ebay_mode == 'sandbox'){ echo 'selected'; } ?> ><?php echo $text_sandbox; ?></option>
                        <option value="production" <?php if(isset($ebay_connector_ebay_mode) && $ebay_connector_ebay_mode == 'production'){ echo 'selected'; } ?>><?php echo $text_production; ?></option>
                      </select>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-user-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_user_id; ?>"><?php echo $entry_ebay_user_id; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="ebay_connector_ebay_user_id" id="input-ebay-user-id" value="<?php if(isset($ebay_connector_ebay_user_id)){ echo $ebay_connector_ebay_user_id; } ?>" placeholder="<?php echo $placeholder_user_id; ?>" />
                      <?php if($error_ebay_connector_ebay_user_id){ ?>
                        <div class="text-danger"><?php echo $error_ebay_connector_ebay_user_id; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-auth-token"><span data-toggle="tooltip" title="<?php echo $help_ebay_auth_token; ?>"><?php echo $entry_ebay_auth_token; ?></span></label>
                    <div class="col-sm-9">
                      <textarea class="form-control" name="ebay_connector_ebay_auth_token" id="input-ebay-auth-token"  rows="5" placeholder="<?php echo $placeholder_auth_token; ?>"><?php if(isset($ebay_connector_ebay_auth_token)){ echo $ebay_connector_ebay_auth_token; } ?></textarea>
                      <?php if($error_ebay_connector_ebay_auth_token){ ?>
                        <div class="text-danger"><?php echo $error_ebay_connector_ebay_auth_token; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-app-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_app_id; ?>"><?php echo $entry_ebay_app_id; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="ebay_connector_ebay_application_id" id="input-ebay-app-id" value="<?php if(isset($ebay_connector_ebay_application_id)){ echo $ebay_connector_ebay_application_id; } ?>" placeholder="<?php echo $placeholder_app_id; ?>" />
                      <?php if($error_ebay_connector_ebay_application_id){ ?>
                        <div class="text-danger"><?php echo $error_ebay_connector_ebay_application_id; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-dev-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_dev_id; ?>"><?php echo $entry_ebay_dev_id; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="ebay_connector_ebay_developer_id" id="input-ebay-dev-id" value="<?php if(isset($ebay_connector_ebay_developer_id)){ echo $ebay_connector_ebay_developer_id; } ?>" placeholder="<?php echo $placeholder_dev_id; ?>" />
                      <?php if($error_ebay_connector_ebay_developer_id){ ?>
                        <div class="text-danger"><?php echo $error_ebay_connector_ebay_developer_id; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-cert-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_cert_id; ?>"><?php echo $entry_ebay_cert_id; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="ebay_connector_ebay_certification_id" id="input-ebay-cert-id" value="<?php if(isset($ebay_connector_ebay_certification_id)){ echo $ebay_connector_ebay_certification_id; } ?>" placeholder="<?php echo $placeholder_cert_id; ?>" />
                      <?php if($error_ebay_connector_ebay_certification_id){ ?>
                        <div class="text-danger"><?php echo $error_ebay_connector_ebay_certification_id; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-shop-postal-code"><span data-toggle="tooltip" title="<?php echo $help_shop_postal_code; ?>"><?php echo $entry_shop_postal_code; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" name="ebay_connector_ebay_shop_postal_code" id="input-shop-postal-code" value="<?php if(isset($ebay_connector_ebay_shop_postal_code)){ echo $ebay_connector_ebay_shop_postal_code; } ?>" placeholder="<?php echo $placeholder_post_code; ?>" />
                      <?php if($error_ebay_connector_ebay_shop_postal_code){ ?>
                        <div class="text-danger"><?php echo $error_ebay_connector_ebay_shop_postal_code; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                </div>
              </div>
            </div>
            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="headingTwo">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <?php echo $panel_import_category; ?>
                  </a>
                </h4>
              </div>
              <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
                <div class="panel-body">
                  <div class="alert alert-danger"><?php echo $text_config_save; ?></div>
                   <div class="form-horizontal text-right">
                    <div class="col-sm-12 form-group">
                      <label class="col-sm-2 control-label"><?php echo "Processing..."; ?></label>
                      <div class="col-sm-10" style="margin-top:10px">
                        <div class="progress">
                          <div id="progress-bar" class="progress-bar" style="width: 0%;"></div>
                        </div>
                        <div id="progress-text"></div>
                      </div>
                    </div>
                  </div>
                  <div class=""><div class="animationload block_div"><div class="osahanloading"></div></div></div>

                  <!-- <div class="block_div">
                    <div class="block_spinner">
                      <div class="cp-spinner cp-balls"></div>
                    </div>
                  </div> -->

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-category-row"><span data-toggle="tooltip" title="<?php echo 'Number of row fetch for ebay category'; ?>"><?php echo "Number of row fetch for ebay category"; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" class="form-control" value="<?php if(isset($ebay_connector_category_row) && $ebay_connector_category_row){ echo $ebay_connector_category_row; }else{ echo 100;} ?>" name="ebay_connector_category_row" id="input-category-row" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-import-category"><span data-toggle="tooltip" title="<?php echo $help_import_category; ?>"><?php echo $entry_import_category; ?></span></label>
                    <div class="col-sm-9">
                      <?php if ($ebay_connector_status) { ?>
                          <button type="button" class="btn btn-warning" id="import_category" style="border-radius: 0;"><?php echo $entry_import_category; ?></button>
                      <?php } else { ?>

                          <button type="button" class="btn btn-info" style="border-radius: 0;" data-toggle="modal" data-target="#exampleModal">
                          <?php echo $entry_import_category; ?>
                          </button>

                          <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                              <div class="modal-content">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="exampleModalLabel"><?php echo $text_warn_entry; ?></h5>
                                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                  </button>
                                </div>
                                <div class="modal-body">
                                   <?php echo $entry_import_category_info; ?>
                                </div>
                                <div class="modal-footer">
                                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                              </div>
                            </div>
                          </div>
                      <?php } ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="headingThree">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    <?php echo $panel_product_setting; ?>
                  </a>
                </h4>
              </div>
              <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
                <div class="panel-body">
                    <!-- <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-revise-ebayItem"><span data-toggle="tooltip" title="<?php echo $help_revise_ebayitem; ?>"><?php echo $entry_revise_ebayitem; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-revise-ebayItem" name="ebay_connector_revise_ebayitem" class="form-control">
                          <option value="yes" <?php if(isset($ebay_connector_revise_ebayitem) && $ebay_connector_revise_ebayitem == 'yes'){ echo 'selected'; } ?> ><?php echo $text_yes; ?></option>
                          <option value="no" <?php if(isset($ebay_connector_revise_ebayitem) && $ebay_connector_revise_ebayitem == 'no'){ echo 'selected'; } ?>><?php echo $text_no; ?></option>
                        </select>
                      </div>
                    </div> -->

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-default-quantity"><span data-toggle="tooltip" title="<?php echo $help_default_quantity; ?>"><?php echo $entry_default_quantity; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" name="ebay_connector_default_item_quantity" class="form-control" id="input-default-quantity" value="<?php if(isset($ebay_connector_default_item_quantity) && $ebay_connector_default_item_quantity) { echo $ebay_connector_default_item_quantity; } ?>" />
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-product-tax"><span data-toggle="tooltip" title="<?php echo $help_product_tax; ?>"><?php echo $entry_product_tax_status; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-product-tax" name="ebay_connector_product_tax" class="form-control">
                          <?php if ($ebay_connector_product_tax) { ?>
                          <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                          <option value="0"><?php echo $text_disabled; ?></option>
                          <?php } else { ?>
                          <option value="1"><?php echo $text_enabled; ?></option>
                          <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-default-category"><span data-toggle="tooltip" title="<?php echo $help_default_category; ?>"><?php echo $entry_default_category; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-default-category" name="ebay_connector_default_category" class="form-control">
                         <?php foreach($getOcParentCategory as $key => $value){ ?>
                            <?php if(isset($ebay_connector_default_category) && $ebay_connector_default_category == $value['category_id']){ ?>
                              <option value="<?php echo $value['category_id']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                            <?php }else{ ?>
                              <option value="<?php echo $value['category_id']; ?>" ><?php echo $value['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-account-delete"><span data-toggle="tooltip" title="<?php echo $help_account_delete; ?>"><?php echo $entry_account_delete; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-account-delete" name="ebay_connector_account_delete" class="form-control">
                          <?php if(isset($ebay_connector_account_delete)){ ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                          <?php }else{ ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-account-delete"><span data-toggle="tooltip" title="<?php echo $help_delete_product; ?>"><?php echo $entry_delete_product; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-account-delete" name="ebay_connector_product_delete" class="form-control">
                          <?php if(isset($ebay_connector_product_delete) && $ebay_connector_product_delete){ ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                          <?php }else{ ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                </div>
              </div>
            </div>

            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="headingFour">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                    <?php echo $panel_return_policy; ?>
                  </a>
                </h4>
              </div>
              <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
                <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-return-policy"><span data-toggle="tooltip" title="<?php echo $help_return_policy; ?>"><?php echo $entry_return_policy; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-return-policy" name="ebay_connector_return_policy" class="form-control">
                          <option value="ReturnsAccepted" <?php if(isset($ebay_connector_return_policy) && $ebay_connector_return_policy == 'ReturnsAccepted'){ echo 'selected'; } ?> ><?php echo $text_return_accepted; ?></option>
                          <option value="ReturnsNotAccepted" <?php if(isset($ebay_connector_return_policy) && $ebay_connector_return_policy == 'ReturnsNotAccepted'){ echo 'selected'; } ?>><?php echo $text_return_not_accepted; ?></option>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-return-days"><span data-toggle="tooltip" title="<?php echo $help_return_days; ?>"><?php echo $entry_return_days; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-return-days" name="ebay_connector_return_days" class="form-control">
                          <?php foreach($return_days as $key => $value){ ?>
                            <?php if(isset($ebay_connector_return_days) && $ebay_connector_return_days == $value['value']){ ?>
                              <option value="<?php echo $value['value']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                            <?php }else{ ?>
                              <option value="<?php echo $value['value']; ?>" ><?php echo $value['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-pay-by"><span data-toggle="tooltip" title="<?php echo $help_pay_by; ?>"><?php echo $entry_pay_by; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-pay-by" name="ebay_connector_pay_by" class="form-control">
                          <?php foreach($pay_by as $key => $value){ ?>
                            <?php if(isset($ebay_connector_pay_by) && $ebay_connector_pay_by == $value['value']){ ?>
                              <option value="<?php echo $value['value']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                            <?php }else{ ?>
                              <option value="<?php echo $value['value']; ?>" ><?php echo $value['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-other-info"><span data-toggle="tooltip" title="<?php echo $help_other_info; ?>"><?php echo $entry_other_info; ?></span></label>
                      <div class="col-sm-9">
                        <textarea name="ebay_connector_other_info" id="input-other-info" class="form-control"><?php if(isset($ebay_connector_other_info) && $ebay_connector_other_info){echo $ebay_connector_other_info; } ?></textarea>
                      </div>
                    </div>

                </div>
              </div>
            </div>

            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="headingFive">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                    <?php echo $panel_listing_option; ?>
                  </a>
                </h4>
              </div>
              <div id="collapseFive" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFive">
                <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-listing-duration"><span data-toggle="tooltip" title="<?php echo $help_listing_duration; ?>"><?php echo $entry_listing_duration; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-listing-duration" name="ebay_connector_listing_duration" class="form-control">
                         <?php foreach($listing_duration as $key => $value){ ?>
                            <?php if(isset($ebay_connector_listing_duration) && $ebay_connector_listing_duration == $value['value']){ ?>
                              <option value="<?php echo $value['value']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                            <?php }else{ ?>
                              <option value="<?php echo $value['value']; ?>" ><?php echo $value['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-item-delete"><span data-toggle="tooltip" title="<?php echo $help_item_delete; ?>"><?php echo $entry_item_delete; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-item-delete" name="ebay_connector_ebay_item_delete" class="form-control">
                        <?php if ($ebay_connector_ebay_item_delete) { ?>
                          <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                          <option value="0"><?php echo $text_disabled; ?></option>
                          <?php } else { ?>
                          <option value="1"><?php echo $text_enabled; ?></option>
                          <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-item-delete"><span data-toggle="tooltip" title="<?php echo $help_item_delete_quantity; ?>"><?php echo $entry_item_delete_quantity; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-item-delete" name="ebay_connector_ebay_item_delete_quantity" class="form-control">
                          <?php if($ebay_connector_ebay_item_delete_quantity){ ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                        <?php }else{ ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-product-delete"><span data-toggle="tooltip" title="<?php echo $help_product_delete; ?>"><?php echo $entry_product_delete; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-product-delete" name="ebay_connector_oc_product_delete" class="form-control">
                        <?php if ($ebay_connector_oc_product_delete) { ?>
                          <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                          <option value="0"><?php echo $text_disabled; ?></option>
                          <?php } else { ?>
                          <option value="1"><?php echo $text_enabled; ?></option>
                          <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                        </select>
                      </div>
                    </div>

                </div>
              </div>
            </div>

            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="headingSix">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                    <?php echo $panel_dispatch_options; ?>
                  </a>
                </h4>
              </div>
              <div id="collapseSix" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingSix">
                <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-dispatch-time"><span data-toggle="tooltip" title="<?php echo $help_dispatch_time; ?>"><?php echo $entry_dispatch_time; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-dispatch-time" name="ebay_connector_dispatch_time" class="form-control">
                         <?php foreach($dispatch_time as $key => $value){ ?>
                            <?php if(isset($ebay_connector_dispatch_time) && $ebay_connector_dispatch_time == $value['value']){ ?>
                              <option value="<?php echo $value['value']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                            <?php }else{ ?>
                              <option value="<?php echo $value['value']; ?>" ><?php echo $value['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                </div>
              </div>
            </div>

            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="headingseven">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseseven" aria-expanded="false" aria-controls="collapse7">
                    <?php echo $panel_payment_options; ?>
                  </a>
                </h4>
              </div>
              <div id="collapseseven" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingseven">
                <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-paypal-email"><span data-toggle="tooltip" title="<?php echo $help_paypal_email; ?>"><?php echo $entry_paypal_email; ?></span></label>
                      <div class="col-sm-9">
                        <input type="text" name="ebay_connector_paypal_email" class="form-control" id="input-paypal-email" value="<?php if(isset($ebay_connector_paypal_email) && $ebay_connector_paypal_email){ echo $ebay_connector_paypal_email; } ?>" />
                      </div>
                    </div>

                </div>
              </div>
            </div>

            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="heading_8">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_8" aria-expanded="false" aria-controls="collapse8">
                    <?php echo $panel_default_shipping; ?>
                  </a>
                </h4>
              </div>
              <div id="collapse_8" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_8">
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-shipping-priority"><span data-toggle="tooltip" title="<?php echo $help_shipping_priority; ?>"><?php echo $entry_shipping_priority; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" name="ebay_connector_shipping_priority" class="form-control" id="input-shipping-priority" value="<?php if(isset($ebay_connector_shipping_priority) && $ebay_connector_shipping_priority) { echo $ebay_connector_shipping_priority; } ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-shipping-service"><span data-toggle="tooltip" title="<?php echo $help_shipping_service; ?>"><?php echo $entry_shipping_service; ?></span></label>
                    <div class="col-sm-9">
                      <select id="input-shipping-service" name="ebay_connector_shipping_service" class="form-control">
                        <?php foreach($shipping_services as $key => $value){ ?>
                          <?php if(isset($ebay_connector_shipping_service) && $ebay_connector_shipping_service == $value['value']){ ?>
                            <option value="<?php echo $value['value']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                          <?php }else{ ?>
                            <option value="<?php echo $value['value']; ?>" ><?php echo $value['name']; ?></option>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>

                   <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-shipping-service-cost"><span data-toggle="tooltip" title="<?php echo $help_shipping_service_cost; ?>"><?php echo $entry_shipping_service_cost; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" name="ebay_connector_shipping_service_cost" class="form-control" id="input-shipping-service-cost" value="<?php if(isset($ebay_connector_shipping_service_cost) && $ebay_connector_shipping_service_cost){ echo $ebay_connector_shipping_service_cost; } ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-shipping-service-add-cost"><span data-toggle="tooltip" title="<?php echo $help_shipping_service_addit_cost; ?>"><?php echo $entry_shipping_service_add_cost; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" name="ebay_connector_shipping_service_add_cost" class="form-control" id="input-shipping-service-add-cost" value="<?php if(isset($ebay_connector_shipping_service_add_cost) && $ebay_connector_shipping_service_add_cost){ echo $ebay_connector_shipping_service_add_cost; } ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-shipping-min-time"><span data-toggle="tooltip" title="<?php echo $help_shipping_min_time; ?>"><?php echo $entry_shipping_min_time; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" name="ebay_connector_shipping_min_time" class="form-control" id="input-shipping-min-time" value="<?php if(isset($ebay_connector_shipping_min_time) && $ebay_connector_shipping_min_time){ echo $ebay_connector_shipping_min_time; } ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-shipping-max-time"><span data-toggle="tooltip" title="<?php echo $help_shipping_max_time; ?>"><?php echo $entry_shipping_max_time; ?></span></label>
                    <div class="col-sm-9">
                      <input type="text" name="ebay_connector_shipping_max_time" class="form-control" id="input-shipping-max-time" value="<?php if(isset($ebay_connector_shipping_max_time) && $ebay_connector_shipping_max_time){ echo $ebay_connector_shipping_max_time; } ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-shipping-free-status"><span data-toggle="tooltip" title="<?php echo $help_shipping_free_status; ?>"><?php echo $entry_shipping_free_status; ?></span></label>
                    <div class="col-sm-9">
                      <select name="ebay_connector_shipping_free_status" id="input-shipping-free-status" class="form-control">
                        <?php if ($ebay_connector_shipping_free_status) { ?>
                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                        <option value="0"><?php echo $text_disabled; ?></option>
                        <?php } else { ?>
                        <option value="1"><?php echo $text_enabled; ?></option>
                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
              </div>
            </div>
          </div>

            <div class="panel panel-primary">
              <div class="panel-heading" role="tab" id="heading_9">
                <h4 class="panel-title">
                  <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_9" aria-expanded="false" aria-controls="collapse9">
                    <?php echo $panel_order_sync_option; ?>
                  </a>
                </h4>
              </div>
              <div id="collapse_9" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_9">
                <div class="panel-body">
                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-order-sync-store"><span data-toggle="tooltip" title="<?php echo $help_order_sync_store; ?>"><?php echo $entry_order_sync_store; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-order-sync-store" name="ebay_connector_ordersync_store" class="form-control">
                          <option value="0"><?php echo $text_default; ?></option>
                          <?php if(isset($stores) && $stores){ ?>
                            <?php foreach($stores as $key => $store){ ?>
                              <?php if(isset($ebay_connector_ordersync_store) && $ebay_connector_ordersync_store == $store['store_id']){ ?>
                                <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                              <?php }else{ ?>
                                <option value="<?php echo $store['store_id']; ?>" ><?php echo $store['name']; ?></option>
                              <?php } ?>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-order-status"><span data-toggle="tooltip" title="<?php echo $help_order_status; ?>"><?php echo $entry_order_status; ?></span></label>
                      <div class="col-sm-9">
                        <select id="input-order-status" name="ebay_connector_order_status" class="form-control">
                         <?php foreach($order_status as $key => $value){ ?>
                            <?php if(isset($ebay_connector_order_status) && $ebay_connector_order_status == $value['order_status_id']){ ?>
                              <option value="<?php echo $value['order_status_id']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                            <?php }else{ ?>
                              <option value="<?php echo $value['order_status_id']; ?>" ><?php echo $value['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-3 control-label" for="input-sync-slot"><span data-toggle="tooltip" title="<?php echo $help_sync_slot; ?>"><?php echo $entry_sync_slot; ?></span></label>
                      <div class="col-sm-9">
                      <div class="col-sm-3">
                        <div class="input-group">
                          <span class="input-group-btn">
                            <button class="btn btn-danger minusCustomer" type="button">
                              <i class="fa fa-minus"></i>
                            </button>
                          </span>
                           <input type="text" readonly class="form-control" name="ebay_connector_sync_record" value="<?php if(isset($ebay_connector_sync_record) && ($ebay_connector_sync_record) || ($ebay_connector_sync_record == 3)){ echo $ebay_connector_sync_record; }else{ echo "5"; } ?>" />
                          <span class="input-group-btn">
                            <button class="btn btn-success plusCustomer" type="button">
                              <i class="fa fa-plus"></i>
                            </button>
                          </span>
                        </div>
                      </div>
                      </div>
                    </div>

                </div>
              </div>
            </div>

             <!--  Price rule tab starts-->
              <div class="panel panel-primary">
                <div class="panel-heading collapsed" id="heading_price_rules" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_price_rules" aria-expanded="false" aria-controls="collapse_update">
                  <h4 class="panel-title">
                    <i class="text-info fa fa-money fa-fw"></i> <?php echo $panel_price_rules; ?>
                  </h4>
                </div>
              <div id="collapse_price_rules" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_price_rules">
                <div class="panel-body">
                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-update-imported"><span data-toggle="tooltip" title="<?php echo $help_price_rules; ?>"><?php echo $entry_price_rules; ?></span></label>
                    <div class="col-sm-9">
                      <input id="input-update-imported" type="checkbox" data-toggle="toggle" data-width="75" <?php if (isset($ebay_connector_price_rules) && $ebay_connector_price_rules === 'on') { echo "checked"; } ?>  data-on="Export" data-off="Import" data-onstyle="success" data-offstyle="danger" name="ebay_connector_price_rules">
                      <div class="text-warning"><i><?php echo $info_price_rules; ?></i></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
           <!--  Price rule tab ends-->

           <!--  Real Time Sync Tab starts-->
            <div class="panel panel-primary">
              <div class="panel-heading collapsed">
                <h4 class="panel-title">
                  <a id="heading_realtime_sync" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_realtime_sync" aria-expanded="false" aria-controls="collapse_update"><i class="far fa-calendar-alt"></i> <?php echo $panel_realtime_sync; ?></a>
                </h4>
              </div>
            <div id="collapse_realtime_sync" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_realtime_sync">
              <div class="panel-body">
                <div class="form-group">
                  <label class="col-sm-3 control-label" for="input-realtimesync"><span data-toggle="tooltip" title="<?php echo $help_realtime_sync; ?>"><?php echo $entry_realtime_sync; ?></span></label>
                  <div class="col-sm-9">
                    <input id="input-realtimesync" type="checkbox"  data-toggle="toggle" data-width="100"  <?php if (isset($ebay_connector_realtime_sync) && $ebay_connector_realtime_sync === 'on') { echo "checked"; } ?>  data-on="Enabled" data-off="Disabled" data-onstyle="success" data-offstyle="danger" name="ebay_connector_realtime_sync">
                    <div class="text-warning"><i><?php echo $info_realtime_sync; ?></i></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
         <!--  end Real Time Sync Tab -->

         <!--  ebay syncproducts to multiple store -->
         <div class="panel panel-primary">
           <div class="panel-heading" role="tab" id="heading_syncproduct_status">
             <h4 class="panel-title">
               <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_syncproduct_status" aria-expanded="false" aria-controls="collapse_syncproduct_status">
                 <?php echo $entry_syncproduct_multiple_store; ?>
               </a>
             </h4>
           </div>

           <div id="collapse_syncproduct_status" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading_syncproduct_status">
             <div class="panel-body">
               <div class="form-group">
                 <label class="col-sm-3 control-label" for="input-syncproduct_multiple_store"><span data-toggle="tooltip" title="<?php echo $help_syncproduct_status; ?>"><?php echo $text_syncproduct_status; ?></span></label>
                 <div class="col-sm-9">
                   <input id="input-syncproduct_multiple_store" type="checkbox" data-toggle="toggle" data-width="100" <?php if (isset($ebay_connector_syncproduct_status) && $ebay_connector_syncproduct_status === 'on') { echo "checked"; } ?>  data-on="Enabled" data-off="Disabled" data-onstyle="success" data-offstyle="danger" name="ebay_connector_syncproduct_status">
                   <div class="text-warning"><i><?php echo $info_syncproduct_status; ?></i></div>
                 </div>
               </div>
             </div>
           </div>
         </div>
         <!-- end ebay syncproducts to multiple store -->
          </div>

        </form>
      </div>
    </div>
  </div>

</div>

<script type="text/javascript">
  var requests    = []; var totalImportedcategory = 0; var total = 0;
  var start_page  = 1;
  $('#import_category').on('click', function(e){
    e.preventDefault();
      if (typeof timer != 'undefined') {
        clearInterval(timer);
      }
        timer = setInterval(function() {
          clearInterval(timer);
          // Reset everything
          $('.alert').remove();
          $('#progress-bar').css('width', '0%');
          $('#progress-bar').removeClass('progress-bar-danger progress-bar-success');
          $('#collapseTwo #progress-text').html('<div class="text-info text-left"><?php echo $text_sync_process_category; ?></div>');

          $.ajax({
              url: 'index.php?route=extension/module/ebay_connector/_importEbayCategories&token=<?php echo $token; ?>&ebay_AccountId=false',
              dataType: 'json',
              beforeSend: function() {
                $('.block_div').css('display','block');
                $('.container-fluid > .alert').remove();
              },
              complete:function() {
                  NextStep();
              },
              success: function(json) {
                if (json.redirect){
                  location = json.redirect;
                }
                if (json.error) {
                  $('#progress-bar').addClass('progress-bar-danger');
                  $('#collapseTwo #progress-text').html('<div class="text-danger">' + json.error + '</div>');
                }else{
                  if(json.success){
                      total = json.totalcategory;
                      for(var start_page = 1; start_page <= json.totalcategory; start_page++) {
                          requests.push({
                              url     : 'index.php?route=extension/module/ebay_connector/start_syncronize&token=<?php echo $token; ?>',
                              type    :   "POST",
                              dataType:   "json",
                              async   :   true,
                              data: {
                                      'account_id' : false,
                                      'page' : start_page
                              },
                              success :   function(json_response){
                                  if(json_response.error){
                                    $('#collapseTwo #progress-text').html('<div class="text-danger"> '+json_response.error_count+' </div>');
                                  }
                                  if(json_response.success_count){
                                      $('#collapseTwo #progress-text').html('<div class="text-success text-right">'+json_response.success_msg+'</div>');
                                      totalImportedcategory = totalImportedcategory + json_response.success_count;
                                  }
                                  if(json_response.success_already){
                                      $('#collapseTwo #progress-text').html('<div class="text-success text-right">'+json_response.success_already_msg+'</div>');
                                  }
                              }
                          });
                      }
                  }else{
                    location = json['redirect'];
                  }
                }
              },
              error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
            });
         }, 500);
  });


  var NextStep = function(){
      if (requests.length) {
          $('#progress-bar').css('width', (100 - (requests.length / total) * 100) + '%');
          $.ajax(requests.shift()).then(NextStep);
      } else {
          $('#progress-bar').css('width', '100%');
          $('#collapseTwo #progress-text').html('<div class="text-success">Total '+totalImportedcategory+' category imported in opencart store from ebay store!"</div>');
          $('#progress-bar').addClass('progress-bar-success');
          $('.block_div').css('display','none');
      }
  };


  /*record sync slot set by admin or default value as 5*/
  var assign_recordnumber = '<?php if($ebay_connector_sync_record || $ebay_connector_sync_record == 3) echo $ebay_connector_sync_record; else echo "5"; ?>';

  /*Setting Customer sync number value on loading*/
  $('input[name="ebay_connector_sync_record"]').val(assign_recordnumber)
  /**
   * [To increment the value of sync slot by 1]
   */
  $('.plusCustomer').on('click', function(){
    $('.alert-danger').remove();
    assign_recordnumber++;
    if(assign_recordnumber > 50) {
      assign_recordnumber--;
      $('input[name="ebay_connector_sync_record"]').parents('.form-group').append('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  <?php echo $error_warning_greater; ?><button class="close" data-dismiss="alert">&times;</button></div>');
      return;
    }
    $('input[name="ebay_connector_sync_record"]').val(assign_recordnumber);
  });

  /**
   * [To decrement the value of zoom level by 1]
   * @param  {String} ){                 assign_recordnumber--;    $('input[name [description]
   * @return {[type]}     [description]
   */
  $('.minusCustomer').on('click', function(){
    assign_recordnumber--;
    $('.alert-danger').remove();
    if(assign_recordnumber < 3) {
      assign_recordnumber++;
      $('input[name="ebay_connector_sync_record"]').parents('.form-group').append('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  <?php echo $error_warning_less; ?><button class="close" data-dismiss="alert">&times;</button></div>');
      return;
    }
  $('input[name="ebay_connector_sync_record"]').val(assign_recordnumber);
  });
</script>
<?php echo $footer; ?>
