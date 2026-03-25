<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-module" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
       <?php foreach($breadcrumbs as $breadcrumb){ ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
   <?php if($error_warning) { ?>
    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="google_lookup_status" id="input-status" class="form-control">
                <?php if($google_lookup_status) { ?>
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
            <label class="col-sm-2 control-label" for="input-limit"><?php echo $entry_apikey; ?></label>
            <div class="col-sm-10">
              <input type="text" name="google_lookup_apikey" value="<?php echo $google_lookup_apikey; ?>" placeholder="<?php echo $entry_apikey; ?>" id="input-limit" class="form-control" />
            </div>
          </div>
		  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_showmap; ?></label>
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-primary btn-sm <?php if($google_lookup_map  == '1') { ?> active <?php } ?>">
							<input type="radio" <?php if($google_lookup_map) { ?> checked <?php } ?> value="1" name="google_lookup_map"><?php echo $text_yes; ?>
						</label>
						<label class="btn btn-primary btn-sm <?php if(!$google_lookup_map) { ?> active <?php } ?>">
							<input type="radio" <?php if(!$google_lookup_map) { ?> checked <?php } ?> value="0" name="google_lookup_map"><?php echo $text_no; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-limit"><?php echo $entry_default; ?></label>
				<div class="col-sm-3">
				<label><?php echo $entry_lat; ?></label>
				  <input type="text" name="google_lookup_lat" value="<?php echo $google_lookup_lat; ?>" placeholder="<?php echo $entry_lat; ?>" id="input-lat" class="form-control" />
				</div>
				<div class="col-sm-3">
				<label><?php echo $entry_lng; ?></label>
				  <input type="text" name="google_lookup_lng" value="<?php echo $google_lookup_lng; ?>" placeholder="<?php echo $entry_lng; ?>" id="input-lng" class="form-control" />
				</div>
				<div class="col-sm-3">
				<label><?php echo $entry_zoom; ?></label>
				  <input type="text" name="google_lookup_zoom" value="<?php echo $google_lookup_zoom; ?>" placeholder="<?php echo $entry_zoom; ?>" id="input-zoom" class="form-control" />
				</div>
          </div>
		  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_showaregister; ?></label>
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-primary btn-sm <?php if($google_lookup_showaregister  == '1') { ?> active <?php } ?>">
							<input type="radio" <?php if($google_lookup_showaregister) { ?> checked <?php } ?> value="1" name="google_lookup_showaregister"><?php echo $text_yes; ?>
						</label>
						<label class="btn btn-primary btn-sm <?php if(!$google_lookup_showaregister) { ?> active <?php } ?>">
							<input type="radio" <?php if(!$google_lookup_showaregister) { ?> checked <?php } ?> value="0" name="google_lookup_showaregister"><?php echo $text_no; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_showaffiliateregister; ?></label>
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-primary btn-sm <?php if($google_lookup_showaffiliateregister  == '1') { ?> active <?php } ?>">
							<input type="radio" <?php if($google_lookup_showaffiliateregister) { ?> checked <?php } ?> value="1" name="google_lookup_showaffiliateregister"><?php echo $text_yes; ?>
						</label>
						<label class="btn btn-primary btn-sm <?php if(!$google_lookup_showaffiliateregister) { ?> active <?php } ?>">
							<input type="radio" <?php if(!$google_lookup_showaffiliateregister) { ?> checked <?php } ?> value="0" name="google_lookup_showaffiliateregister"><?php echo $text_no; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_showcregister; ?></label>
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-primary btn-sm <?php if($google_lookup_showcregister  == '1') { ?> active <?php } ?>">
							<input type="radio" <?php if($google_lookup_showcregister) { ?> checked <?php } ?> value="1" name="google_lookup_showcregister"><?php echo $text_yes; ?>
						</label>
						<label class="btn btn-primary btn-sm <?php if(!$google_lookup_showcregister) { ?> active <?php } ?>">
							<input type="radio" <?php if(!$google_lookup_showcregister) { ?> checked <?php } ?> value="0" name="google_lookup_showcregister"><?php echo $text_no; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_showcguest; ?></label>
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-primary btn-sm <?php if($google_lookup_showcguest  == '1') { ?> active <?php } ?>">
							<input type="radio" <?php if($google_lookup_showcguest) { ?> checked <?php } ?> value="1" name="google_lookup_showcguest"><?php echo $text_yes; ?>
						</label>
						<label class="btn btn-primary btn-sm <?php if(!$google_lookup_showcguest) { ?> active <?php } ?>">
							<input type="radio" <?php if(!$google_lookup_showcguest) { ?> checked <?php } ?> value="0" name="google_lookup_showcguest"><?php echo $text_no; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_showeditaddress; ?></label>
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-primary btn-sm <?php if($google_lookup_editaddress  == '1') { ?> active <?php } ?>">
							<input type="radio" <?php if($google_lookup_editaddress) { ?> checked <?php } ?> value="1" name="google_lookup_editaddress"><?php echo $text_yes; ?>
						</label>
						<label class="btn btn-primary btn-sm <?php if(!$google_lookup_editaddress) { ?> active <?php } ?>">
							<input type="radio" <?php if(!$google_lookup_editaddress) { ?> checked <?php } ?> value="0" name="google_lookup_editaddress"><?php echo $text_no; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_showpayment_add; ?></label>
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-primary btn-sm <?php if($google_lookup_payment_add  == '1') { ?> active <?php } ?>">
							<input type="radio" <?php if($google_lookup_payment_add) { ?> checked <?php } ?> value="1" name="google_lookup_payment_add"><?php echo $text_yes; ?>
						</label>
						<label class="btn btn-primary btn-sm <?php if(!$google_lookup_payment_add) { ?> active <?php } ?>">
							<input type="radio" <?php if(!$google_lookup_payment_add) { ?> checked <?php } ?> value="0" name="google_lookup_payment_add"><?php echo $text_no; ?>
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_showshipping_add; ?></label>
				<div class="col-sm-6">
					<div class="btn-group" data-toggle="buttons">
						<label class="btn btn-primary btn-sm <?php if($google_lookup_shipping_add  == '1') { ?> active <?php } ?>">
							<input type="radio" <?php if($google_lookup_shipping_add) { ?> checked <?php } ?> value="1" name="google_lookup_shipping_add"><?php echo $text_yes; ?>
						</label>
						<label class="btn btn-primary btn-sm <?php if(!$google_lookup_shipping_add) { ?> active <?php } ?>">
							<input type="radio" <?php if(!$google_lookup_shipping_add) { ?> checked <?php } ?> value="0" name="google_lookup_shipping_add"><?php echo $text_no; ?>
						</label>
					</div>
				</div>
			</div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>