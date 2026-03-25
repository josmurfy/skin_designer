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
  <div class="container-fluid">
    <?php if (isset($error_warning) && $error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if (isset($success) && $success) {?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
      </div>
    <div class="panel-body">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-amazon-price-rules" class="form-horizontal">

        <div class="form-group required">
          <label class="col-sm-3 control-label" for="input-price-from"><span data-toggle="tooltip" title="<?php echo $help_price_from; ?>"><?php echo $entry_price_from; ?></span></label>
          <div class="col-sm-9">
            <input type="number" class="form-control" name="price_from" id="input-price_from" value="<?php if(isset($price_from)){ echo $price_from; } ?>" placeholder="<?php echo $placeholder_price_from; ?>" />
            <?php if(isset($err_price_from) &&  $err_price_from){ ?>
              <div class="text-danger"><?php echo $err_price_from; ?></div>
            <?php } ?>
          </div>
        </div>

        <div class="form-group required">
          <label class="col-sm-3 control-label" for="input-price-to"><span data-toggle="tooltip" title="<?php echo $help_price_to; ?>"><?php echo $entry_price_to; ?></span></label>
          <div class="col-sm-9">
            <input type="number" class="form-control" name="price_to" id="input-price-to" value="<?php if(isset($price_to)){ echo $price_to; } ?>" placeholder="<?php echo $placeholder_price_to; ?>" />
            <?php if(isset($err_price_to) && $err_price_to){ ?>
              <div class="text-danger"><?php echo $err_price_to; ?></div>
            <?php } ?>
          </div>
        </div>

        <div class="form-group required">
          <label class="col-sm-3 control-label" for="input-price-value"><span data-toggle="tooltip" title="<?php echo $help_price_value; ?>"><?php echo $entry_price_value; ?></span></label>
          <div class="col-sm-9">
            <input type="number" class="form-control" name="price_value" id="input-price-value" value="<?php if(isset($price_value)){ echo $price_value; } ?>" placeholder="<?php echo $placeholder_price_value; ?>" />
            <?php if(isset($err_price_value) &&  $err_price_value){ ?>
              <div class="text-danger"><?php echo $err_price_value; ?></div>
            <?php } ?>
          </div>
        </div>

        <div class="form-group required">
          <label class="col-sm-3 control-label" for="input-price-type"><span data-toggle="tooltip" title="<?php echo $help_price_opration; ?>"><?php echo $entry_price_type; ?></span></label>
          <div class="col-sm-9">
            <select name="price_type" id="input-price-type" class="form-control">
              <?php if ($price_type) { ?>
              <option value="1" selected="selected"><?php echo $text_price_type_inc; ?></option>
              <option value="0"><?php echo $text_price_type_dec; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_price_type_inc; ?></option>
              <option value="0" selected="selected"><?php echo $text_price_type_dec; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>

        <div class="form-group required">
          <label class="col-sm-3 control-label" for="input-price-opration"><span data-toggle="tooltip" title="<?php echo $help_price_type; ?>"><?php echo $entry_price_opration; ?></span></label>
          <div class="col-sm-9">
            <select name="price_opration" id="input-price-type" class="form-control">
              <?php if (!$price_opration) { ?>
              <option value="0" selected="selected"><?php echo $text_price_type_fixed; ?></option>
              <option value="1"><?php echo $text_price_type_percent; ?></option>
              <?php } else { ?>
              <option value="0"><?php echo $text_price_type_fixed; ?></option>
              <option value="1" selected="selected"><?php echo $text_price_type_percent; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="col-sm-3 control-label" for="input-price-status"><span data-toggle="tooltip" title="<?php echo $help_price_status; ?>"><?php echo $entry_price_status; ?></span></label>
          <div class="col-sm-9">
            <select name="price_status" id="input-price-status" class="form-control">
              <?php if ($price_status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
          <!-- <div class="form-group required">
            <label class="col-sm-3 control-label" for="input-store-name"><span data-toggle="tooltip" title="<?php echo $help_amazon_store_name; ?>"><?php echo $text_store; ?></span></label>
            <div class="col-sm-9">
              <input type="text" name="price_store" class="form-control" id="input-store-name" value="<?php if(isset($price_store) && $price_store) { echo $price_store; } ?>"/>
              <?php if(isset($error_price_store) && $error_price_store){ ?>
                <div class="text-danger"><?php echo $error_price_store; ?></div>
              <?php } ?>
            </div>
          </div> -->
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
