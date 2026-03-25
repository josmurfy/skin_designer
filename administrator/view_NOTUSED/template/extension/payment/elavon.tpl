<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-elavon" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-elavon" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-vmid"><?php echo $entry_vmid; ?></label>
            <div class="col-sm-10">
              <input type="text" name="elavon_vmid" value="<?php echo $elavon_vmid; ?>" placeholder="<?php echo $entry_vmid; ?>" id="input-vmid" class="form-control" />
              <?php if ($error_vmid) { ?>
              <div class="text-danger"><?php echo $error_vmid; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-userid"><?php echo $entry_userid; ?></label>
            <div class="col-sm-10">
              <input type="text" name="elavon_userid" value="<?php echo $elavon_userid; ?>" placeholder="<?php echo $entry_userid; ?>" id="input-userid" class="form-control" />
              <?php if ($error_userid) { ?>
              <div class="text-danger"><?php echo $error_userid; ?></div>
              <?php } ?>
            </div>
          </div>        
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-pin"><?php echo $entry_pin; ?></label>
            <div class="col-sm-10">
              <input type="text" name="elavon_pin" value="<?php echo $elavon_pin; ?>" placeholder="<?php echo $entry_pin; ?>" id="input-pin" class="form-control" />
            </div>
          </div>
            <div class="form-group">
            <label class="col-sm-2 control-label" for="input-cardsaccepted"><?php echo $entry_cardsaccepted; ?></label>
            <div class="col-sm-10">
              <input type="text" name="elavon_cardsaccepted" value="<?php echo $elavon_cardsaccepted; ?>" placeholder="<?php echo $entry_cardsaccepted; ?>" id="input-cardsaccepted" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-refererurl"><?php echo $entry_refererurl; ?></label>
            <div class="col-sm-10">
              <input type="text" name="elavon_refererurl" value="<?php echo $elavon_refererurl; ?>" placeholder="<?php echo $entry_refererurl; ?>" id="input-refererurl" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-mode"><?php echo $entry_mode; ?></label>
            <div class="col-sm-10">
              <select name="elavon_mode" id="input-mode" class="form-control">
                <?php if ($elavon_mode == 'live') { ?>
                <option value="live" selected="selected"><?php echo $text_live; ?></option>
                <?php } else { ?>
                <option value="live"><?php echo $text_live; ?></option>
                <?php } ?>
                <?php if ($elavon_mode == 'test') { ?>
                <option value="test" selected="selected"><?php echo $text_test; ?></option>
                <?php } else { ?>
                <option value="test"><?php echo $text_test; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-method"><?php echo $entry_method; ?></label>
            <div class="col-sm-10">
              <select name="elavon_method" id="input-method" class="form-control">
                <?php if ($elavon_method == 'CCSALE') { ?>
                <option value="CCSALE" selected="selected"><?php echo $text_ccsale; ?></option>
                <?php } else { ?>
                <option value="CCSALE"><?php echo $text_ccsale; ?></option>
                <?php } ?>
                <?php if ($elavon_method == 'CCAUTHONLY') { ?>
                <option value="CCAUTHONLY" selected="selected"><?php echo $text_ccauthonly; ?></option>
                <?php } else { ?>
                <option value="CCAUTHONLY"><?php echo $text_ccauthonly; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>         
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-store_number"><?php echo $entry_store_number; ?></label>
            <div class="col-sm-10">
              <select name="elavon_store_number" id="input-store_number" class="form-control">
                <?php if ($elavon_store_number == 'true') { ?>
                <option value="true" selected="selected"><?php echo $text_true; ?></option>
                <?php } else { ?>
                <option value="true"><?php echo $text_true; ?></option>
                <?php } ?>
                <?php if ($elavon_store_number == 'false') { ?>
                <option value="false" selected="selected"><?php echo $text_false; ?></option>
                <?php } else { ?>
                <option value="false"><?php echo $text_false; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-compatability"><?php echo $entry_compatability; ?></label>
            <div class="col-sm-10">
              <select name="elavon_compatability" id="input-compatability" class="form-control">
                <?php if ($elavon_compatability == 'true') { ?>
                <option value="true" selected="selected"><?php echo $text_true; ?></option>
                <?php } else { ?>
                <option value="true"><?php echo $text_true; ?></option>
                <?php } ?>
                <?php if ($elavon_compatability == 'false') { ?>
                <option value="false" selected="selected"><?php echo $text_false; ?></option>
                <?php } else { ?>
                <option value="false"><?php echo $text_false; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="elavon_total" value="<?php echo $elavon_total; ?>" id="input-total" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
            <div class="col-sm-10">
              <select name="elavon_order_status_id" id="input-order-status" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $elavon_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
            <div class="col-sm-10">
              <select name="elavon_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $elavon_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="elavon_status" id="input-status" class="form-control">
                <?php if ($elavon_status) { ?>
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
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="elavon_sort_order" value="<?php echo $elavon_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?> 