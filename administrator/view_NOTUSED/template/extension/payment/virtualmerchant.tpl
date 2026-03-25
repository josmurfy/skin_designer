<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-virtualmerchant" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-virtualmerchant" class="form-horizontal">
		  <fieldset>
		  	<legend><?php echo $text_account; ?></legend>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-login"><?php echo $entry_login; ?></label>
				<div class="col-sm-10">
				  <input type="text" name="virtualmerchant_login" value="<?php echo $virtualmerchant_login; ?>" placeholder="<?php echo $entry_login; ?>" id="input-login" class="form-control" />
				  <?php if ($error_login) { ?>
				  <div class="text-danger"><?php echo $error_login; ?></div>
				  <?php } ?>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-user"><span data-toggle="tooltip" title="<?php echo $help_user; ?>"><?php echo $entry_user; ?></span></label>
				<div class="col-sm-10">
				  <input type="text" name="virtualmerchant_user" value="<?php echo $virtualmerchant_user; ?>" placeholder="<?php echo $entry_user; ?>" id="input-user" class="form-control" />
				  <?php if ($error_user) { ?>
				  <div class="text-danger"><?php echo $error_user; ?></div>
				  <?php } ?>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-pin"><?php echo $entry_pin; ?></label>
				<div class="col-sm-10">
				  <input type="text" name="virtualmerchant_pin" value="<?php echo $virtualmerchant_pin; ?>" placeholder="<?php echo $entry_pin; ?>" id="input-pin" class="form-control" />
				  <?php if ($error_pin) { ?>
				  <div class="text-danger"><?php echo $error_pin; ?></div>
				  <?php } ?>
				</div>
			  </div>
			  <div class="form-group required">
				<label class="col-sm-2 control-label" for="input-currency"><?php echo $entry_currency; ?></label>
				<div class="col-sm-10">
				  <input type="text" name="virtualmerchant_currency" value="<?php echo $virtualmerchant_currency; ?>" placeholder="<?php echo $entry_currency; ?>" id="input-currency" class="form-control" />
				  <?php if ($error_currency) { ?>
				  <div class="text-danger"><?php echo $error_currency; ?></div>
				  <?php } ?>
				</div>
			  </div>
		  </fieldset>
		  <fieldset>
		  	<legend><?php echo $text_settings; ?></legend>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-server"><?php echo $entry_server; ?></label>
				<div class="col-sm-10">
				  <select name="virtualmerchant_server" id="input-server" class="form-control">
					<?php if ($virtualmerchant_server == 'live') { ?>
					<option value="live" selected="selected"><?php echo $text_live; ?></option>
					<option value="test"><?php echo $text_test; ?></option>
					<?php } else { ?>
					<option value="live"><?php echo $text_live; ?></option>
					<option value="test" selected="selected"><?php echo $text_test; ?></option>
					<?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-mode"><?php echo $entry_mode; ?></label>
				<div class="col-sm-10">
				  <select name="virtualmerchant_mode" id="input-mode" class="form-control">
					<?php if ($virtualmerchant_mode == 'live') { ?>
					<option value="live" selected="selected"><?php echo $text_live; ?></option>
					<option value="test"><?php echo $text_test; ?></option>
					<?php } else { ?>
					<option value="live"><?php echo $text_live; ?></option>
					<option value="test" selected="selected"><?php echo $text_test; ?></option>
					<?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-method"><?php echo $entry_method; ?></label>
				<div class="col-sm-10">
				  <select name="virtualmerchant_method" id="input-method" class="form-control">
					<?php if ($virtualmerchant_method == 'authorization') { ?>
					<option value="authorization" selected="selected"><?php echo $text_authorization; ?></option>
					<option value="capture"><?php echo $text_capture; ?></option>
					<?php } else { ?>
					<option value="authorization"><?php echo $text_authorization; ?></option>
					<option value="capture" selected="selected"><?php echo $text_capture; ?></option>
					<?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-debug"><?php echo $entry_debug; ?></label>
				<div class="col-sm-10">
				  <select name="virtualmerchant_debug" id="input-debug" class="form-control">
					<?php if ($virtualmerchant_debug) { ?>
					<option value="1" selected="selected"><?php echo $text_yes; ?></option>
					<option value="0"><?php echo $text_no; ?></option>
					<?php } else { ?>
					<option value="1"><?php echo $text_yes; ?></option>
					<option value="0" selected="selected"><?php echo $text_no; ?></option>
					<?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
				<div class="col-sm-10">
				  <input type="text" name="virtualmerchant_total" value="<?php echo $virtualmerchant_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
				<div class="col-sm-10">
				  <select name="virtualmerchant_order_status_id" id="input-order-status" class="form-control">
					<?php foreach ($order_statuses as $order_status) { ?>
					<?php if ($order_status['order_status_id'] == $virtualmerchant_order_status_id) { ?>
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
				  <select name="virtualmerchant_geo_zone_id" id="input-geo-zone" class="form-control">
					<option value="0"><?php echo $text_all_zones; ?></option>
					<?php foreach ($geo_zones as $geo_zone) { ?>
					<?php if ($geo_zone['geo_zone_id'] == $virtualmerchant_geo_zone_id) { ?>
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
				  <select name="virtualmerchant_status" id="input-status" class="form-control">
					<?php if ($virtualmerchant_status) { ?>
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
				  <input type="text" name="virtualmerchant_sort_order" value="<?php echo $virtualmerchant_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
				</div>
			  </div>
		  </fieldset>
		  <fieldset>
		  	<legend><?php echo $text_recurring; ?></legend>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-skip-cycle"><span data-toggle="tooltip" title="<?php echo $help_skip_cycle; ?>"><?php echo $entry_skip_cycle; ?></span></label>
				<div class="col-sm-10">
				  <select name="virtualmerchant_skip_cycle" id="input-skip-cycle" class="form-control">
					<?php if ($virtualmerchant_skip_cycle) { ?>
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
				<label class="col-sm-2 control-label" for="input-bill-on-half"><?php echo $entry_bill_on_half; ?></label>
				<div class="col-sm-10">
				  <select name="virtualmerchant_bill_on_half" id="input-bill-on-half" class="form-control">
					<?php if ($virtualmerchant_bill_on_half != 2) { ?>
					<option value="1" selected="selected"><?php echo $text_first_fifteen; ?></option>
					<option value="2"><?php echo $text_fifteen_last; ?></option>
					<?php } else { ?>
					<option value="1"><?php echo $text_first_fifteen; ?></option>
					<option value="2" selected="selected"><?php echo $text_fifteen_last; ?></option>
					<?php } ?>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-end-of-month"><span data-toggle="tooltip" title="<?php echo $help_end_of_month; ?>"><?php echo $entry_end_of_month; ?></span></label>
				<div class="col-sm-10">
				  <select name="virtualmerchant_end_of_month" id="input-end-of-month" class="form-control">
					<?php if ($virtualmerchant_end_of_month == 'Y') { ?>
					<option value="Y" selected="selected"><?php echo $text_yes; ?></option>
					<option value="N"><?php echo $text_no; ?></option>
					<?php } else { ?>
					<option value="Y"><?php echo $text_yes; ?></option>
					<option value="N" selected="selected"><?php echo $text_no; ?></option>
					<?php } ?>
				  </select>
				</div>
			  </div>
		  </fieldset>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>