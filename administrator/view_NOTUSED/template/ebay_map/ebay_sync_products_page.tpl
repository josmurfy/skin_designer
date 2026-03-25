<?php echo $header ?><?php echo $column_left ?>
<div id="content">
<link href="view/stylesheet/csspin.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  #stores tfoot tr {
    display: none;
  }
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
  .cp-round::before, .cp-round::after{
    width: 35px;
    left:8px;
    height: 35px;
    /*top: 25px;*/
    margin-top: 25px;
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
</style>
<script>
  let link = '<?php echo $link ?>',
  source_product_id = '<?php echo $source_product_id ?>',
  page = parseInt('<?php echo $page ?>');
</script>
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <a href="<?php echo $cancel ?>" data-toggle="tooltip" title="<?php echo $button_cancel ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h3><?php echo $heading_title ?></h3>
      <hr />
    </div>
  </div>
  <div class="container-fluid">
    <?php if($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i><?php echo $error_warning ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i><?php echo $success ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_select_product ?></h3>
      </div>
      <div class="panel-body">
        <div class="col-sm-3" id="ebay_left_link">
            <div class="panel-group panel-primary" id="accordion_ebay" role="tablist" aria-multiselectable="true">
            <div class="panel">
              <div class="panel-heading" role="tab" id="headingOne">
                <h4 class="panel-title">
                  <center><b><a role="button" data-toggle="collapse" data-parent="#accordion_ebay" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <?php echo strtoupper($entry_ebay_account_info) ?>
                  </a></b></center>
                </h4>
              </div>
              <div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
                <div class="panel-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs tabs-left"><!-- 'tabs-right' for right tabs -->
                  <!-- ebay_sync_products -->
                  <?php if($source_product_id) { ?>
                    <li class="li-format active"><a href="#account_ebay_sync_products" data-toggle="tab"><?php echo $text_ebay_sync_products ?></a></li>
                    <li class="li-format"><a href="#account_ebay_sync_products_all" data-toggle="tab">All Synced Products</a></li>
                  <?php } else { ?>
                    <li class="li-format"><a href="#account_ebay_sync_products" data-toggle="tab"><?php echo $text_ebay_sync_products ?></a></li>
                    <li class="li-format active"><a href="#account_ebay_sync_products_all" data-toggle="tab">All Synced Products</a></li>
                  <?php } ?>
                </ul>
                </div>
              </div>
            </div>
          </div>
        </div><!--Col-sm-3-->
        <div class="col-sm-9">
          <div class="tab-content" id="">
            <?php if($source_product_id) { ?>
              <div class="tab-pane active" id="account_ebay_sync_products">
                  <div class="form-group">
                    <button type="button" id="button-save" data-account-id="<?php echo $account_id ?>" data-toggle="tooltip" data-token="<?php echo $token ?>" title="<?php echo $button_sync_save ?>" class="pull-right btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_sync_save ?></button>
                    <div class="clearfix"></div>
                  </div>
                  <?php echo $account_ebay_sync_products ?>
              </div>
              <div class="tab-pane" id="account_ebay_sync_products_all"><?php echo $account_ebay_sync_products_all ?></div>
            <?php } else { ?>
              <div class="tab-pane" id="account_ebay_sync_products">
                  <div class="form-group">
                    <button type="button" id="button-save" data-account-id="<?php echo $account_id ?>" data-toggle="tooltip" data-token="<?php echo $token ?>" title="<?php echo $button_sync_save ?>" class="pull-right btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_sync_save ?></button>
                    <div class="clearfix"></div>
                  </div>
                  <?php echo $account_ebay_sync_products ?>
              </div>
              <div class="tab-pane active" id="account_ebay_sync_products_all"><?php echo $account_ebay_sync_products_all ?></div>
            <?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php echo $footer ?>
