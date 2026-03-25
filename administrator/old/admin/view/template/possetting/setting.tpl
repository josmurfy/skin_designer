<?php echo $header; ?>	
<script src="view/javascript/colorbox/jquery.minicolors.js"></script>
<link rel="stylesheet" href="view/stylesheet/jquery.minicolors.css">
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-setting" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      
      <h1><?php echo $tmdheading_title; ?></h1>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-setting" class="form-horizontal">
			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-customsetting" data-toggle="tab"><?php echo $tab_customsetting; ?></a></li>
				<li><a href="#tab-customersetting" data-toggle="tab"><?php echo $tab_customersetting; ?></a></li>
				<li class="hide"><a href="#tab-barcode" data-toggle="tab"><?php echo $tab_barcode; ?></a></li>
				<li><a href="#tab-paymentmethod" data-toggle="tab"><?php echo $tab_paymentmethod; ?></a></li>
				<li><a href="#tab-invoice" data-toggle="tab"><?php echo $tab_invoice; ?></a></li>
				<li><a href="#tab-dashboard" data-toggle="tab"><?php echo $tab_dashboard; ?></a></li>
				<li><a href="#tab-customermail" data-toggle="tab"><?php echo $tab_cmail; ?></a></li>
				<li><a href="#tab-ordermail" data-toggle="tab"><?php echo $tab_omail; ?></a></li>
				<li><a href="#tab-paysetting" data-toggle="tab"><?php echo $tab_paysetting; ?></a></li>
			</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="tab-customsetting">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-setting_status"><?php echo $entry_status; ?></label>
				    <div class="col-sm-10">
				          <select name="setting_status" id="input-setting-status" class="form-control">
				          <?php if ($setting_status) { ?>
				            <option value="0"><?php echo $text_disabled; ?></option>
				            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
				          <?php } else { ?>
				            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
				            <option value="1"><?php echo $text_enabled; ?></option>
				          <?php } ?>
				          </select>
					</div>
				</div>
				<div class="table-responsive">
                <table id="register" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_fieldname; ?></td>
                      <td class="text-left"><?php echo $entry_label; ?></td>
                      <td class="text-left"><?php echo $entry_error; ?></td>
                      <td class="text-left"><?php echo $entry_required; ?></td>
					  <td class="text-left"><?php echo $entry_status; ?></td>
                      <td class="text-left hide"><?php echo $entry_sort; ?></td>
                     
                    </tr>
                  </thead>
                  <tbody>
					<tr>
						<td class="text-left"><label><?php echo $entry_firstname; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
						<?php 
						if(!empty($setting_settings['firstnamelabel'][$language['language_id']])){
							$value = $setting_settings['firstnamelabel'][$language['language_id']];
						}else{
							$value = '';
						}
						?>	
						<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
						
						<input name="setting_settings[firstnamelabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_firstname; ?>" value="<?php echo $value; ?>" class="form-control"/>
						</div>
						<?php } ?>
						</td>
						
						<td class="text-left"><?php foreach ($languages as $language) { ?>
						<?php 
						if(!empty($setting_settings['firstnamerror'][$language['language_id']])){
							$value = $setting_settings['firstnamerror'][$language['language_id']];
						}else{
							$value = '';
						}
						?>
						<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
						<input name="setting_settings[firstnamerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_firstname_missing; ?>" class="form-control" value="<?php echo $value; ?>"/>
						</div>
						<?php } ?>
						</td>
					    <td class="text-left">
					     <label class="radio-inline">
						 <?php if ($setting_fnamerequired) { ?>
						   <input type="radio" name="setting_fnamerequired" value="1" checked="checked" />
						   <?php echo $text_yes; ?>
						 <?php } else { ?>
						   <input type="radio" name="setting_fnamerequired" value="1" />
						   <?php echo $text_yes; ?>
					    <?php } ?>
						</label>
						<label class="radio-inline">
						 <?php if (!$setting_fnamerequired) { ?>
						  <input type="radio" name="setting_format" value="0" checked="checked" />
					      <?php echo $text_no; ?>
					    <?php } else { ?>
					     <input type="radio" name="setting_format" value="0" /><?php echo $text_no; ?>
					    <?php } ?>
						</label></td>
					    <td class="text-left">
					     <label class="radio-inline">
						 <?php if ($setting_fnamestatus) { ?>
						   <input type="radio" name="setting_fnamestatus" value="1" checked="checked" />
						   <?php echo $text_yes; ?>
						 <?php } else { ?>
						   <input type="radio" name="setting_fnamestatus" value="1" />
						   <?php echo $text_yes; ?>
					    <?php } ?>
						</label>
						<label class="radio-inline">
						 <?php if (!$setting_fnamestatus) { ?>
						  <input type="radio" name="setting_fnamestatus" value="0" checked="checked" />
					      <?php echo $text_no; ?>
					    <?php } else { ?>
					     <input type="radio" name="setting_fnamestatus" value="0" /><?php echo $text_no; ?>
					    <?php } ?>
						</label></td>
						<td class="text-left hide" style="width:8%;">
							<input type="text" name="setting_fnamesortorder" value="<?php echo $setting_fnamesortorder;?>" placeholder="" class="form-control" />
						</td>
						
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_lastname; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							
						<?php 
						if(!empty($setting_settings['lastnamelabel'][$language['language_id']])){
							$value = $setting_settings['lastnamelabel'][$language['language_id']];
						}else{
							$value = '';
						}
						?>		
						<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
						  <input name="setting_settings[lastnamelabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_lastname; ?>" value="<?php echo $value; ?>" class="form-control"/>
						</div>
						<?php } ?>
						</td>
						
						<td class="text-left"><?php foreach ($languages as $language) { ?>
						<?php 
						if(!empty($setting_settings['lastnamerror'][$language['language_id']])){
							$value = $setting_settings['lastnamerror'][$language['language_id']];
						}else{
							$value = '';
						}
						?>
						<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
						  <input name="setting_settings[lastnamerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_lastname_missing; ?>"  value="<?php echo $value; ?>" class="form-control"/>
						</div>
						<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_lastnamerequired) { ?>
							<input type="radio" name="setting_lastnamerequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_lastnamerequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_lastnamerequired) { ?>
							<input type="radio" name="setting_lastnamerequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_lastnamerequired" value="0" /><?php echo $text_no; ?>
							<?php } ?></label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_lastnamestatus) { ?>
							<input type="radio" name="setting_lastnamestatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_lastnamestatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_lastnamestatus) { ?>
							<input type="radio" name="setting_lastnamestatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_lastnamestatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left hide">
							<input type="text" name="setting_lastnamesortorder" value="<?php echo $setting_lastnamesortorder;?>" placeholder="" class="form-control" />	
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_email; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
						<?php 
						if(!empty($setting_settings['emaillabel'][$language['language_id']])){
							$value = $setting_settings['emaillabel'][$language['language_id']];
						}else{
							$value = '';
						}
						?>
						<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
						  <input name="setting_settings[emaillabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_email; ?>" value="<?php echo $value; ?>" class="form-control"/>
						</div>
						<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
								<?php 
							if(!empty($setting_settings['emailerror'][$language['language_id']])){
								$value = $setting_settings['emailerror'][$language['language_id']];
							}else{
								$value = '';
							}
							?>						
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							  <input name="setting_settings[emailerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_email_missing; ?>" value="<?php echo $value; ?>"class="form-control"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_emailrequired) { ?>
							<input type="radio" name="setting_emailrequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_emailrequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_emailrequired) { ?>
							<input type="radio" name="setting_emailrequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_emailrequired" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_emailstatus) { ?>
							<input type="radio" name="setting_emailstatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_emailstatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_emailstatus) { ?>
							<input type="radio" name="setting_emailstatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_emailstatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
						</label>
						</td>
            			<td class="text-left hide">
							<input type="text" name="setting_emailsortorder" value="<?php echo $setting_emailsortorder;?>" placeholder="" class="form-control" />
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_telephone; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
						<?php 
						if(!empty($setting_settings['phonelabel'][$language['language_id']])){
							$value = $setting_settings['phonelabel'][$language['language_id']];
						}else{
							$value = '';
						}
						?>
						<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
						  <input name="setting_settings[phonelabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_telephone; ?>" value="<?php echo $value; ?>" class="form-control"/>
						</div>
						<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
						<?php 
						if(!empty($setting_settings['phonerror'][$language['language_id']])){
							$value = $setting_settings['phonerror'][$language['language_id']];
						}else{
							$value = '';
						}
						?>
						<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
						  <input name="setting_settings[phonerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>" />
						</div>
						<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_phonerequired) { ?>
							<input type="radio" name="setting_phonerequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_phonerequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_phonerequired) { ?>
							<input type="radio" name="setting_phonerequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_phonerequired" value="0" /><?php echo $text_no; ?>
							<?php } ?></label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_phonestatus) { ?>
							<input type="radio" name="setting_phonestatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_phonestatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_phonestatus) { ?>
							<input type="radio" name="setting_phonestatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_phonestatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left hide">
						<input type="text" name="setting_phonesortorder" value="<?php echo $setting_phonesortorder;?>" placeholder="" class="form-control" />
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_fax; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['faxlabel'][$language['language_id']])){
							$value = $setting_settings['faxlabel'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[faxlabel][<?php echo $language['language_id']; ?>][" placeholder="<?php echo $entry_fax; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
						<?php 
						if(!empty($setting_settings['faxerror'][$language['language_id']])){
							$value = $setting_settings['faxerror'][$language['language_id']];
						}else{
							$value = '';
						}
						?>	
						<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
						  <input name="setting_settings[faxerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
						</div>
						<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_faxrequired) { ?>
							<input type="radio" name="setting_faxrequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_faxrequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_faxrequired) { ?>
							<input type="radio" name="setting_faxrequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_faxrequired" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_faxstatus) { ?>
							<input type="radio" name="setting_faxstatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_faxstatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_faxstatus) { ?>
							<input type="radio" name="setting_faxstatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_faxstatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
                        <td class="text-left hide">
							<input type="text" name="setting_faxsortorder" value="<?php echo $setting_faxsortorder;?>" placeholder="" class="form-control" />	
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_company; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['companylabel'][$language['language_id']])){
							$value = $setting_settings['companylabel'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
								<input name="setting_settings[companylabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_company; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['companyerror'][$language['language_id']])){
							$value = $setting_settings['companyerror'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
								<input name="setting_settings[companyerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
					    <td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_compquired) { ?>
							<input type="radio" name="setting_compquired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_compquired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_compquired) { ?>
							<input type="radio" name="setting_compquired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_compquired" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
					    <td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_compstatus) { ?>
							<input type="radio" name="setting_compstatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_compstatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_compstatus) { ?>
							<input type="radio" name="setting_compstatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_compstatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left hide">
							<input type="text" name="setting_compsortorder" value="<?php echo $setting_compsortorder;?>" placeholder="" class="form-control" />	
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_address1; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['add1label'][$language['language_id']])){
							$value = $setting_settings['add1label'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[add1label][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_address1; ?>" class="form-control" value="<?php echo $value; ?>" />
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['add1error'][$language['language_id']])){
							$value = $setting_settings['add1error'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[add1error][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_add1required) { ?>
							<input type="radio" name="setting_add1required" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_add1required" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_add1required) { ?>
							<input type="radio" name="setting_add1required" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_add1required" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_add1status) { ?>
							<input type="radio" name="setting_add1status" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_add1status" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_add1status) { ?>
							<input type="radio" name="setting_add1status" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_add1status" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left hide">
							<input type="text" name="setting_add1sortorder" value="<?php echo $setting_add1sortorder;?>" placeholder="" class="form-control" />
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_address2; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['add2label'][$language['language_id']])){
							$value = $setting_settings['add2label'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[add2label][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_address2; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['add2error'][$language['language_id']])){
							$value = $setting_settings['add2error'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[add2error][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_add2required) { ?>
							<input type="radio" name="setting_add2required" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_add2required" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_add2required) { ?>
							<input type="radio" name="setting_add2required" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_add2required" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_add2status) { ?>
							<input type="radio" name="setting_add2status" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_add2status" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_add2status) { ?>
							<input type="radio" name="setting_add2status" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_add2status" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>	
						<td class="text-left hide">
							<input type="text" name="setting_add2sortorder" value="<?php echo $setting_add2sortorder;?>" placeholder="" class="form-control" />
						</td>
                    </tr>
					
					<tr>
						<td class="text-left"><label><?php echo $entry_city; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['citylabel'][$language['language_id']])){
							$value = $setting_settings['citylabel'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[citylabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_city; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['cityerror'][$language['language_id']])){
							$value = $setting_settings['cityerror'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[cityerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_cityrequired) { ?>
							<input type="radio" name="setting_cityrequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_cityrequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_cityrequired) { ?>
							<input type="radio" name="setting_cityrequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_cityrequired" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_citystatus) { ?>
							<input type="radio" name="setting_citystatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_citystatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_citystatus) { ?>
							<input type="radio" name="setting_citystatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_citystatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left hide" style="width:8%;">
							<input type="text" name="setting_citysortorder" value="<?php echo $setting_citysortorder;?>" placeholder="" class="form-control" />
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_postcode; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['postcodelabel'][$language['language_id']])){
							$value = $setting_settings['postcodelabel'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[postcodelabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_postcode; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['postcoderror'][$language['language_id']])){
							$value = $setting_settings['postcoderror'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[postcoderror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_postcodrequired) { ?>
							<input type="radio" name="setting_postcodrequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_postcodrequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_postcodrequired) { ?>
							<input type="radio" name="setting_postcodrequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_postcodrequired" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_postcodstatus) { ?>
							<input type="radio" name="setting_postcodstatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_postcodstatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_postcodstatus) { ?>
							<input type="radio" name="setting_postcodstatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_postcodstatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left hide" style="width:8%;">
							<input type="text" name="setting_postcodsortorder" value="<?php echo $setting_postcodsortorder;?>" placeholder="" class="form-control" />
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_country; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['countrylabel'][$language['language_id']])){
							$value = $setting_settings['countrylabel'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[countrylabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_country; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['countryerror'][$language['language_id']])){
							$value = $setting_settings['countryerror'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[countryerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_countryrequired) { ?>
							<input type="radio" name="setting_countryrequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_countryrequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_countryrequired) { ?>
							<input type="radio" name="setting_countryrequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_countryrequired" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_countrystatus) { ?>
							<input type="radio" name="setting_countrystatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_countrystatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_countrystatus) { ?>
							<input type="radio" name="setting_countrystatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_countrystatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
                        <td class="text-left hide" style="width:8%;">
							<input type="text" name="setting_countrysortorder" value="<?php echo $setting_countrysortorder;?>" placeholder="" class="form-control" />
						</td>
						
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_zone; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['zonelabel'][$language['language_id']])){
							$value = $setting_settings['zonelabel'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[zonelabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_zone; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['zonerror'][$language['language_id']])){
							$value = $setting_settings['zonerror'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[zonerror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_zonerequired) { ?>
							<input type="radio" name="setting_zonerequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_zonerequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_zonerequired) { ?>
							<input type="radio" name="setting_zonerequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_zonerequired" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_zonestatus) { ?>
							<input type="radio" name="setting_zonestatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_zonestatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_zonestatus) { ?>
							<input type="radio" name="setting_zonestatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_zonestatus" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left hide" style="width:8%;">
							<input type="text" name="setting_zonesortorder" value="<?php echo $setting_zonesortorder;?>" placeholder="" class="form-control" />
						</td>
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_password; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['pwdlabel'][$language['language_id']])){
							$value = $setting_settings['pwdlabel'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[pwdlabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_password; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['pwderror'][$language['language_id']])){
							$value = $setting_settings['pwderror'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[pwderror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_password_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_pwdrequired) { ?>
							<input type="radio" name="setting_pwdrequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_pwdrequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_pwdrequired) { ?>
							<input type="radio" name="setting_pwdrequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_pwdrequired" value="0" /><?php echo $text_no; ?>
							<?php } ?>
							</label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_pwdstatus) { ?>
							<input type="radio" name="setting_pwdstatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_pwdstatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_pwdstatus) { ?>
							<input type="radio" name="setting_pwdstatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_pwdstatus" value="0" /><?php echo $text_no; ?>
							<?php } ?></label>
						</td>
						<td class="text-left hide" style="width:8%;">
							<input type="text" name="setting_pwdsortorder" value="<?php echo $setting_pwdsortorder;?>" placeholder="" class="form-control" />
						</td>
						
                    </tr>
					<tr>
						<td class="text-left"><label><?php echo $entry_confirm_password; ?></label></td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['cpwdlabel'][$language['language_id']])){
							$value = $setting_settings['cpwdlabel'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[cpwdlabel][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_confirm_password; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left"><?php foreach ($languages as $language) { ?>
							<?php 
							if(!empty($setting_settings['cpwderror'][$language['language_id']])){
							$value = $setting_settings['cpwderror'][$language['language_id']];
							}else{
							$value = '';
							}
							?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							<input name="setting_settings[cpwderror][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_error; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
							<?php } ?>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_cpwdrequired) { ?>
							<input type="radio" name="setting_cpwdrequired" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_cpwdrequired" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_cpwdrequired) { ?>
							<input type="radio" name="setting_cpwdrequired" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_cpwdrequired" value="0" /><?php echo $text_no; ?>
							<?php } ?></label>
						</td>
						<td class="text-left">
							<label class="radio-inline">
							<?php if ($setting_cpwdstatus) { ?>
							<input type="radio" name="setting_cpwdstatus" value="1" checked="checked" />
							<?php echo $text_yes; ?>
							<?php } else { ?>
							<input type="radio" name="setting_cpwdstatus" value="1" />
							<?php echo $text_yes; ?>
							<?php } ?>
							</label>
							<label class="radio-inline">
							<?php if (!$setting_cpwdstatus) { ?>
							<input type="radio" name="setting_cpwdstatus" value="0" checked="checked" />
							<?php echo $text_no; ?>
							<?php } else { ?>
							<input type="radio" name="setting_cpwdstatus" value="0" /><?php echo $text_no; ?>
							<?php } ?></label>
						</td>
						<td class="text-left hide" style="width:8%;">
							<input type="text" name="setting_cpwdsortorder" value="<?php echo $setting_cpwdsortorder;?>" placeholder="" class="form-control" />
						</td>
						
                    </tr>
					
                  </tbody>
                </table>
				</div>
				
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-email-exist"><?php echo $entry_email_warning; ?></label>
					<div class="col-sm-10">
						<?php foreach ($languages as $language) { ?>
						<?php 
						if(!empty($setting_settings['emailexist'][$language['language_id']])){
							$value = $setting_settings['emailexist'][$language['language_id']];
						}else{
							$value = '';
						}
						?>	
							<div class="input-group"><span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
							  <input name="setting_settings[emailexist][<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_title; ?>" class="form-control" value="<?php echo $value; ?>"/>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		  <div class="tab-pane" id="tab-customersetting">
              
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-customer_group"><span data-toggle="tooltip" title="<?php echo $help_customer_group; ?>"><?php echo $entry_customer_group; ?></span></label>
				<div class="col-sm-10">
					<select name="setting_customer_group_id" id="input-customer_group" class="form-control">
						<?php foreach ($customer_groups as $customer_group) { ?>
				        <?php if ($customer_group['customer_group_id'] == $setting_customer_group) { ?>
				        <option value="<?php echo $customer_group['customer_group_id']; ?>" selected="selected"><?php echo $customer_group['name']; ?></option>
				        <?php } else { ?>
				        <option value="<?php echo $customer_group['customer_group_id']; ?>"><?php echo $customer_group['name']; ?></option>
				        <?php } ?>
				        <?php } ?>
				    </select>
				</div>
			</div>

			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-newsletter"><span data-toggle="tooltip" title="<?php echo $help_newsletter; ?>"><?php echo $entry_newsletter; ?></span></label>
				<div class="col-sm-10">
					<select name="setting_newsletter" id="input-status" class="form-control">
						<?php if ($setting_newsletter) { ?>
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
				<label class="col-sm-2 control-label" for="input-default_password"><span data-toggle="tooltip" title="<?php echo $help_default_password; ?>"><?php echo $entry_default_password; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_default_password" value="<?php echo $setting_default_password;?>" placeholder="<?php echo $entry_default_password; ?>" id="input-default_password" class="form-control" />
				</div>
			</div>
			
			<div class="form-group ">
				<label class="col-sm-2 control-label" for="input-firstname"><?php echo $entry_firstname; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_firstname" value="<?php echo $setting_firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
				</div>
			</div>
			<div class="form-group ">
				<label class="col-sm-2 control-label" for="input-lastname"><?php echo $entry_lastname; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_lastname" value="<?php echo $setting_lastname?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
				</div>
			</div>
			<div class="form-group ">
				<label class="col-sm-2 control-label" for="input-email"><?php echo $entry_email; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_email" value="<?php echo $setting_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
				</div>
			</div>
			<div class="form-group ">
				<label class="col-sm-2 control-label" for="input-telephone"><?php echo $entry_telephone; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_telephone" value="<?php echo $setting_telephone?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-fax"><?php echo $entry_fax; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_fax" value="<?php echo $setting_fax;?>" placeholder="<?php echo $entry_fax; ?>" id="input-fax" class="form-control" />
				</div>
			</div>
			<h3>Default Customer Address</h3>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-company"><?php echo $entry_company; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_company" value="<?php echo $setting_company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-address1"><?php echo $entry_address1; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_address1" value="<?php echo $setting_address1; ?>" placeholder="<?php echo $entry_address1; ?>" id="input-address1" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-address2"><?php echo $entry_address2; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_address2" value="<?php echo $setting_address2; ?>" placeholder="<?php echo $entry_address2; ?>" id="input-address2" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-city"><?php echo $entry_city; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_city" value="<?php echo $setting_city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-postcode"><?php echo $entry_postcode; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_postcode" value="<?php echo $setting_postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control" />
				</div>
			</div>
			<div class="form-group required">
				<label class="col-sm-2 control-label" for="input-country"><?php echo $entry_country; ?></label>
				<div class="col-sm-10">
					<select name="setting_country_id" id="input-country" class="form-control">
						<option value=""><?php echo $text_select; ?></option>
						<?php foreach ($countries as $country) { ?>
						<?php if ($country['country_id'] == $setting_country_id) { ?>
						<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group required">
				<label class="col-sm-2 control-label" for="input-zone"><?php echo $entry_zone; ?></label>
				<div class="col-sm-10">
					<select type="text" name="setting_zone_id" placeholder="<?php echo $entry_zone; ?>" id="input-zone" class="form-control" >

					</select>
				</div>
			</div>

		  </div>
		  <div class="tab-pane hide" id="tab-barcode">
		  	<div class="form-group">
				<label class="col-sm-2 control-label" for="input-barcode"><span data-toggle="tooltip" title="<?php echo $help_barcode; ?>"><?php echo $entry_barcode; ?></span></label>
				<div class="col-sm-10">
					<input type="text" name="setting_barcode" value="<?php echo $setting_barcode; ?>" placeholder="<?php echo $entry_barcode; ?>" id="input-barcode" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-type"><?php echo $entry_barcodetype; ?></label>
				<div class="col-sm-10">
					<select name="setting_type" class="form-control">
						<option value=""><?php echo $text_select?></option>
						<?php foreach ($barcodes as $result){ ?>
						<?php if ($setting_type == $result['type']){ ?>
							<option value="<?php echo $result['type']; ?>" selected="selected"><?php echo $result['value']; ?></option> 
						<?php } else { ?>
							<option value="<?php echo $result['type']; ?>"><?php echo $result['value']; ?></option> 
						<?php } ?>
						<?php } ?>
					</select>	
				</div>
			</div>
			<div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_productbarcode; ?></label>
                <div class="col-sm-10">
                  <label class="radio-inline">
                    <?php if ($setting_barcode_product) { ?>
                    <input type="radio" name="setting_barcode_product" value="1" checked="checked" />
                    <?php echo $text_yes; ?>
                    <?php } else { ?>
                    <input type="radio" name="setting_barcode_product" value="1" />
                    <?php echo $text_yes; ?>
                    <?php } ?>
                  </label>
                  <label class="radio-inline">
                    <?php if (!$setting_barcode_product) { ?>
                    <input type="radio" name="setting_barcode_product" value="0" checked="checked" />
                    <?php echo $text_no; ?>
                    <?php } else { ?>
                    <input type="radio" name="setting_barcode_product" value="0" />
                    <?php echo $text_no; ?>
                    <?php } ?>
                  </label>
                </div>
              </div>
			<div class="form-group">
				<label class="col-sm-2 control-label" for="input-barcodeimage"><?php echo $entry_barcodeimage; ?></label>
				<div class="col-sm-10">
                  <label class="radio-inline">
                    <?php if ($setting_barcodeimage) { ?>
                    <input type="radio" name="setting_barcodeimage" value="1" checked="checked" />
                    <?php echo $text_horizontal; ?>
                    <?php } else { ?>
                    <input type="radio" name="setting_barcodeimage" value="1" />
                    <?php echo $text_horizontal; ?>
                    <?php } ?>
                  </label>
                  <label class="radio-inline">
                    <?php if (!$setting_barcodeimage) { ?>
                    <input type="radio" name="setting_barcodeimage" value="0" checked="checked" />
                    <?php echo $text_vertical; ?>
                    <?php } else { ?>
                    <input type="radio" name="setting_barcodeimage" value="0" />
                    <?php echo $text_vertical; ?>
                    <?php } ?>
                  </label>
                </div>
			</div>

		  </div>
		  <div class="tab-pane" id="tab-paymentmethod">
			<div class="table-responsive">
                <table id="payment" class="table table-striped table-bordered table-hover">
                  <thead>
                    <tr>
                      <td class="text-left"><?php echo $entry_name; ?></td>
                      <td class="text-left"><?php echo $entry_order_status; ?></td>
                      <td class="text-left"><?php echo $entry_sortorder; ?></td>
                      <td></td>
                    </tr>
                  </thead>
                  <tbody>
<!-- Payment Method -->
                    <?php $payment_row = 0; ?>
                    <?php foreach ($setting_paymentmethods as $setting_paymentmethod) { ?>
                    
                    <tr id="payment-row<?php echo $payment_row; ?>">
						<td class="text-left"><input type="text" name="setting_paymentmethod[<?php echo $payment_row; ?>][name]" value="<?php echo $setting_paymentmethod['name']; ?>" class="form-control" placeholder="<?php echo $entry_name; ?>"/></td>
						<td class="text-left">
							<select name="setting_paymentmethod[<?php echo $payment_row; ?>][order_status_id]" id="input-order_status" class="form-control">
								<option value=""><?php echo $text_select; ?></option>
								<?php foreach ($order_statuss as $order_status) { ?>
								<?php if ($order_status['order_status_id'] == $setting_paymentmethod['order_status_id']) { ?>
								<option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
								<?php } ?>
								<?php } ?>
							</select>
						</td>
						<td class="text-left"><input type="text" name="setting_paymentmethod[<?php echo $payment_row; ?>][sortorder]" value="<?php echo $setting_paymentmethod['sortorder']; ?>" class="form-control" placeholder="<?php echo $entry_sortorder; ?>"/></td>
						<td class="text-left"><button type="button" onclick="$('#payment-row<?php echo $payment_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                    </tr>
                     <?php $payment_row++; ?>
                    <?php } ?>
<!-- Payment Method -->
                  </tbody>
                  <tfoot>
                    <tr>
                      <td colspan="3"></td>
                      <td class="text-left"><button type="button" onclick="addPayment();" data-toggle="tooltip" title="<?php echo $button_discount_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
		  	</div>
		  	<div class="tab-pane" id="tab-invoice"> 
				<div class="form-group ">
					<label class="col-sm-2 control-label" for="input-setting_format"><?php echo $entry_format; ?></label>
					<div class="col-sm-10">
						<select name="setting_format" id="input-setting_format" class="form-control">
						<?php if ($setting_format) { ?>
						<option value="1" selected="selected"><?php echo $text_3mm; ?></option>
						<option value="0"><?php echo $text_fullsize; ?></option>
						<?php } else { ?>
						<option value="1"><?php echo $text_3mm; ?></option>
						<option value="0" selected="selected"><?php echo $text_fullsize; ?></option>
						<?php } ?>
						</select>
					</div>
				</div> 
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_logo; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_store_logo) { ?>
	                    <input type="radio" name="setting_store_logo" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_store_logo" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_store_logo) { ?>
	                    <input type="radio" name="setting_store_logo" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_store_logo" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_sname; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_store_name) { ?>
	                    <input type="radio" name="setting_store_name" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_store_name" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_store_name) { ?>
	                    <input type="radio" name="setting_store_name" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_store_name" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_saddress; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_store_address) { ?>
	                    <input type="radio" name="setting_store_address" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_store_address" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_store_address) { ?>
	                    <input type="radio" name="setting_store_address" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_store_address" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_stelephone; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_store_telephone) { ?>
	                    <input type="radio" name="setting_store_telephone" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_store_telephone" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_store_telephone) { ?>
	                    <input type="radio" name="setting_store_telephone" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_store_telephone" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_odate; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_order_date) { ?>
	                    <input type="radio" name="setting_order_date" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_order_date" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_order_date) { ?>
	                    <input type="radio" name="setting_order_date" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_order_date" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_otime; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_order_time) { ?>
	                    <input type="radio" name="setting_order_time" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_order_time" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_order_time) { ?>
	                    <input type="radio" name="setting_order_time" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_order_time" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_number; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_invoice_number) { ?>
	                    <input type="radio" name="setting_invoice_number" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_invoice_number" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_invoice_number) { ?>
	                    <input type="radio" name="setting_invoice_number" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_invoice_number" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_cname; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_cashier_name) { ?>
	                    <input type="radio" name="setting_cashier_name" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_cashier_name" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_cashier_name) { ?>
	                    <input type="radio" name="setting_cashier_name" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_cashier_name" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_smode; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_shipping_mode) { ?>
	                    <input type="radio" name="setting_shipping_mode" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_shipping_mode" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_shipping_mode) { ?>
	                    <input type="radio" name="setting_shipping_mode" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_shipping_mode" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_pmode; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_payment_mode) { ?>
	                    <input type="radio" name="setting_payment_mode" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_payment_mode" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_payment_mode) { ?>
	                    <input type="radio" name="setting_payment_mode" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_payment_mode" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_onote; ?></label>
	                <div class="col-sm-10">
	                  <label class="radio-inline">
	                    <?php if ($setting_order_note) { ?>
	                    <input type="radio" name="setting_order_note" value="1" checked="checked" />
	                    <?php echo $text_yes; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_order_note" value="1" />
	                    <?php echo $text_yes; ?>
	                    <?php } ?>
	                  </label>
	                  <label class="radio-inline">
	                    <?php if (!$setting_order_note) { ?>
	                    <input type="radio" name="setting_order_note" value="0" checked="checked" />
	                    <?php echo $text_no; ?>
	                    <?php } else { ?>
	                    <input type="radio" name="setting_order_note" value="0" />
	                    <?php echo $text_no; ?>
	                    <?php } ?>
	                  </label>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label"><?php echo $entry_extra; ?></label>
	                <div class="col-sm-10">
	                  <textarea name="setting_extra" rows="5" placeholder="<?php echo $entry_extra; ?>" id="input-extra" class="form-control"><?php echo $setting_extra; ?></textarea>
	                </div>
	            </div>
	            <div class="form-group">
	                <label class="col-sm-2 control-label" from="input-setting_invoice"><?php echo $entry_invoice; ?></label>
	                <div class="col-sm-10">
	                  <textarea name="setting_invoice" rows="5" placeholder="<?php echo $entry_invoice; ?>" id="input-setting_invoice" class="form-control"><?php echo $setting_invoice; ?></textarea>
	                </div>
	            </div>
				
		  	</div>
		  	<div class="tab-pane" id="tab-dashboard">
		  		<div class="row">
		  			<div class="col-sm-2">
		  				<ul class="nav nav-pills nav-stacked" id="dashboard">
							<?php $order_row = 0; ?>
							<?php foreach($setting_dashboards as $setting_dashboard) { ?>
							
							<li><a href="#tab-dashboard<?php echo $order_row; ?>" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$('#dashboard a:first').tab('show'); $('#dashboard a[href=\'#tab-dashboard<?php echo $order_row; ?>\']').parent().remove(); $('#tab-dashboard<?php echo $order_row; ?>').remove();"></i> <?php echo $setting_dashboard['name']; ?></a></li>

							<?php $order_row++; ?>
							<?php } ?>
							<li id="dashboard-add"><a onclick="addCustome();" class="btn btn-primary"><i class="fa fa-plus-circle"></i> <?php echo $button_add; ?></a></li>
						</ul>
		  			</div>
		  			<div class="col-sm-10">
                  		<div class="tab-content">
                  			<?php $order_row = 0; ?>
                  			<?php if(isset($setting_dashboards)) {?>
                			<?php foreach($setting_dashboards as $setting_dashboard) { ?>
                  			<div class="tab-pane active" id="tab-dashboard<?php echo $order_row; ?>">
                  				<div class="form-group">
									<label class="col-sm-2 control-label" for="input-value<?php echo $order_row;?>"><?php echo $entry_name; ?></label>
									<div class="col-sm-10">
										<input type="text" name="setting_dashboard[<?php echo $order_row;?>][name]" value="<?php echo $setting_dashboard['name'];?>" placeholder="<?php echo $entry_name; ?>" id="input-value" class="form-control" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="input-order_status<?php echo $order_row; ?>"><?php echo $entry_order_status; ?></label>
									<div class="col-sm-10">
										<div class="well well-sm" style="height: 150px; overflow: auto;">
											<?php foreach ($orderstatuss as $order) { ?>
												<div class="checkbox">
													<label>	
     													<?php if (in_array($order['order_status_id'], $setting_dashboard['dashboard_orderstatus'])) { ?>
														<input type="checkbox" name="setting_dashboard[<?php echo $order_row; ?>][dashboard_orderstatus][<?php echo $order['order_status_id'];?>][order_status_id]" value="<?php echo $order['order_status_id']; ?>" checked="checked" />
														<?php echo $order['name']; ?>
														<?php } else { ?>
														<input type="checkbox" name="setting_dashboard[<?php echo $order_row; ?>][dashboard_orderstatus][<?php echo $order['order_status_id'];?>][order_status_id]" value="<?php echo $order['order_status_id']; ?>" />
														<?php echo $order['name']; ?>
														<?php } ?>
													</label>
												</div>
											<?php } ?>
										</div>
									</div>
								</div>
						<!-- Payment Method -->
								<div class="form-group">
				                    <label class="col-sm-2 control-label"><?php echo $entry_method; ?></label>
				                        <div class="col-sm-10">
				                          <div class="well well-sm" style="height: 150px; overflow: auto;">
				                            <?php foreach ($paymentmethods as $payment) { ?>
				                            <div class="checkbox">
				                              	<label>
													<?php if (in_array($payment['name'],$setting_dashboard['dashboard_paymentmethod'])) { ?>
													<input type="checkbox" name="setting_dashboard[<?php echo $order_row; ?>][dashboard_paymentmethod][<?php echo $payment['name']; ?>][method]" value="<?php echo $payment['name']; ?>" checked="checked" />
													<?php echo $payment['name']; ?>
													<?php } else { ?>
													<input type="checkbox" name="setting_dashboard[<?php echo $order_row; ?>][dashboard_paymentmethod][<?php echo $payment['name']; ?>][method]" value="<?php echo $payment['name']; ?>" />
													<?php echo $payment['name']; ?>
													<?php } ?>
				                                </label>
				                            </div>
				                            <?php } ?>
				                        </div>
				                    </div>
				                </div>
				        <!-- Payment Method -->
								<div class="form-group">
									<label class="col-sm-2 control-label" for="input-status<?php echo $order_row;?>"><?php echo $entry_status; ?></label>
									<div class="col-sm-10">
										<select name="setting_dashboard[<?php echo $order_row;?>][dashboard_status]" id="input-status<?php echo $order_row;?>" class="form-control">
											<?php if ($setting_dashboard['dashboard_status']) { ?>
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
									<label class="col-sm-2 control-label" for="input-value<?php echo $order_row;?>"><?php echo $entry_sortorder; ?></label>
									<div class="col-sm-10">
										<input type="text" name="setting_dashboard[<?php echo $order_row;?>][sort_order]" value="<?php echo $setting_dashboard['sort_order'];?>" placeholder="<?php echo $entry_sortorder; ?>" id="input-value<?php echo $order_row; ?>" class="form-control" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label" for="input-daytype"><?php echo $entry_daytype; ?></label>
									<div class="col-sm-10">
										<select name="setting_dashboard[<?php echo $order_row;?>][daytype]" class="form-control">
											<?php foreach ($daytypes as $result){ ?>
											<?php if ($setting_dashboard['daytype'] == $result['daytype']){ ?>
												<option value="<?php echo $result['daytype']; ?>" selected="selected"><?php echo $result['value']; ?></option> 
											<?php } else { ?>
												<option value="<?php echo $result['daytype']; ?>"><?php echo $result['value']; ?></option> 
											<?php } ?>
											<?php } ?>
										</select>	
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label" for="input-color"><?php echo $entry_textcolor; ?></label>
									<div class="col-sm-10">
										<input type="text" name="setting_dashboard[<?php echo $order_row; ?>][text_color]" value="<?php echo $setting_dashboard['text_color']; ?>" placeholder="<?php echo $entry_textcolor; ?>" class="form-control color" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="input-color"><?php echo $entry_bgcolor; ?></label>
									<div class="col-sm-10">
										<input type="text" name="setting_dashboard[<?php echo $order_row; ?>][bg_color]" value="<?php echo $setting_dashboard['bg_color']; ?>" placeholder="<?php echo $entry_bgcolor; ?>" class="form-control color" />
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-2 control-label"><?php echo $entry_icon; ?></label>
									<div class="col-sm-4">
                                     <div class="input-group">
										<input type="text" name="setting_dashboard[<?php echo $order_row;?>][icon]" value="<?php echo $setting_dashboard['icon'];?>" placeholder="<?php echo $entry_icon; ?>" id="input-value<?php echo $order_row; ?>" class="form-control fontbox" />
                                        <span class="input-group-addon"></span>
									</div>
									</div>
								</div>
                  			</div>
                  			<?php $order_row++; ?>
							<?php } ?>	
							<?php } ?>
                  		</div>
                  	</div>

		  		</div>
		  	</div>
		  	<div class="tab-pane" id="tab-customermail">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-subject"><?php echo $entry_subject; ?></label>
					<div class="col-sm-10">
						<input type="text" name="setting_customers_subject" value="<?php echo $setting_customers_subject; ?>" placeholder="<?php echo $entry_subject; ?>" id="input-subject" class="form-control"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-message"><?php echo $entry_message; ?></label>
					<div class="col-sm-10">
						<textarea type="text" name="setting_customers_message" value="" placeholder="<?php echo $entry_message; ?>" id="input-message" class="form-control summernote"><?php echo $setting_customers_message; ?></textarea>
					</div>
				</div>
                <div class="form-group">
					<label class="col-sm-2 control-label" for="input-shortcuts">shortcuts</label>
					<div class="col-sm-10">
					  <ul class="list-unstyled">
                        <li><p>{firstname} </p>        = First Name</li>
                        <li><p>{lastname} </p>         = Last Name</li>
                        <li><p>{email}  </p>           = Email</li>
                        <li><p>{telephone} </p>  	   = Telephone</li>
                        <li><p>{fax}  </p>             = Fax</li>
                        <li><p>{company}</p>       = Company Name</li>
                        <li><p>{address_1} </p>         =  Address1</li>
                        <li><p>{address_2} </p>         =  Address2</li>
                        <li><p>{city}</p>              = City</li>
                        <li><p>{postcode} </p>         = PostCode</li>
                        <li><p>{country} </p>      = Country</li>
                        <li><p>{zone} </p>      = zone</li>
                      </ul>
					</div>
				</div>
		  	</div>
		  	<div class="tab-pane" id="tab-ordermail">
		  		
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-subject"><?php echo $entry_subject; ?></label>
					<div class="col-sm-10">
						<input type="text" name="setting_orders_subject" value="<?php echo $setting_orders_subject; ?>" placeholder="<?php echo $entry_subject; ?>" id="input-subject" class="form-control"/>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-message"><?php echo $entry_message; ?></label>
					<div class="col-sm-10">
						<textarea type="text" name="setting_orders_message" placeholder="<?php echo $entry_message; ?>" id="input-message" class="form-control summernote"><?php echo $setting_orders_message; ?></textarea>
					</div>
				</div>
			</div>

		  	<div class="tab-pane" id="tab-paysetting">
<!-- Payment Method -->
		  		<div class="form-group">
					<label class="col-sm-2 control-label" for="input-setting_default_paymentmethod"><?php echo $entry_paysetting; ?></label>
					<div class="col-sm-10">
						<select name="setting_default_paymentmethod" id="input-setting_default_paymentmethod" class="form-control">
							<?php foreach ($setting_paymentmethods as $result){ ?>
							<?php if ($result['name'] == $setting_default_paymentmethod){ ?>
							<option value="<?php echo $result['name']; ?>" selected="selected"><?php echo $result['name']; ?></option> 
							<?php } else { ?>
							<option value="<?php echo $result['name']; ?>"><?php echo $result['name']; ?></option> 
							<?php } ?>
							<?php } ?>
						</select>
					</div>
	            </div>
<!-- Payment Method -->
	            <div class="form-group">
					<label class="col-sm-2 control-label" for="input-setting_defult_guest"><?php echo $entry_defaultguest; ?></label>
					<div class="col-sm-10">
						<select name="setting_defult_guest" id="input-setting_defult_guest" class="form-control">
						<?php if ($setting_defult_guest) { ?>
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
					<label class="col-sm-2 control-label" for="input-setting_customermail"><?php echo $entry_customermail; ?></label>
					<div class="col-sm-10">
						<select name="setting_customermail" id="input-setting_customermail" class="form-control">
						<?php if ($setting_customermail) { ?>
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
					<label class="col-sm-2 control-label" for="input-setting_ordermail"><?php echo $entry_ordermail; ?></label>
					<div class="col-sm-10">
						<select name="setting_ordermail" id="input-setting_ordermail" class="form-control">
						<?php if ($setting_ordermail) { ?>
						<option value="1" selected="selected"><?php echo $text_yes; ?></option>
						<option value="0"><?php echo $text_no; ?></option>
						<?php } else { ?>
						<option value="1"><?php echo $text_yes; ?></option>
						<option value="0" selected="selected"><?php echo $text_no; ?></option>
						<?php } ?>
						</select>
					</div>
	            </div>
					
		  	</div>

		  	</div>
		</div>
        </form>
      </div>
    </div>
  </div>
</div>
 <script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
  <link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
  <script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<script type="text/javascript"><!--
$('#language a:first').tab('show');
$('#languages a:first').tab('show');
//--></script>


<script type="text/javascript"><!--

var order_row = <?php echo $order_row; ?>;

function addCustome() {
	html  = '<div class="tab-pane active" id="tab-dashboard' + order_row + '">';										 
	html += '	<div class="form-group">';
	html += '	  <label class="col-sm-2 control-label" for="input-value' + order_row + '"><?php echo $entry_name; ?></label>';
	html += '	  <div class="col-sm-10"><input type="text" name="setting_dashboard[' + order_row + '][name]" value="" placeholder="<?php echo $entry_name; ?>" id="input-value' + order_row + '" class="form-control" /></div>';
	html += '	</div>';
	
	html +='<div class="form-group"><label class="col-sm-2 control-label"><?php echo $entry_order_status; ?></label>';
	html +='<div class="col-sm-10"><div class="well well-sm" style="height: 150px; overflow: auto;">';          
			<?php foreach ($orderstatuss as $order) { ?>
	html +='<div class="checkbox">';
	html +='<label><input type="checkbox" name="setting_dashboard[' + order_row + '][dashboard_orderstatus][<?php echo $order['order_status_id'];?>][order_status_id]" value="<?php echo $order['order_status_id']; ?>" />  <?php echo $order['name']; ?> </label>';
	html +='</div>';
			<?php } ?>
	html +='</div></div>';
	html +='</div>';
// Payment Method //
	html +='<div class="form-group"><label class="col-sm-2 control-label"><?php echo $entry_method; ?></label>';
  	html +='<div class="col-sm-10"><div class="well well-sm" style="height: 150px; overflow: auto;">';          
      	<?php foreach ($paymentmethods as $payment) { ?>
  	html +='<div class="checkbox">';
  	html +='<label><input type="checkbox" name="setting_dashboard[' + order_row + '][dashboard_paymentmethod][<?php echo $payment['name']; ?>][method]" value="<?php echo $payment['name']; ?>" />  <?php echo $payment['name']; ?> </label>';
  	html +='</div>';
      	<?php } ?>
  	html +='</div></div>';
  	html +='</div>';
// Payment Method //

	html += '	<div class="form-group">';
	html += '	  <label class="col-sm-2 control-label" for="input-status' + order_row + '"><?php echo $entry_status; ?></label>';
	html += '	  <div class="col-sm-10"><select name="setting_dashboard[' + order_row + '][dashboard_status]" id="input-status' + order_row + '" class="form-control">';
	html += '	      <option value="1"><?php echo $text_enabled; ?></option>';
	html += '	      <option value="0"><?php echo $text_disabled; ?></option>';
	html += '	  </select></div>';
	html += '	</div>';
	html += '	<div class="form-group">';
	html += '	  <label class="col-sm-2 control-label" for="input-value' + order_row + '"><?php echo $entry_sortorder; ?></label>';
	html += '	  <div class="col-sm-10"><input type="text" name="setting_dashboard[' + order_row + '][sort_order]" dort_order="" placeholder="<?php echo $entry_sortorder; ?>" id="input-value' + order_row + '" class="form-control" /></div>';
	html += '	</div>';

	html += '<div class="form-group"><label class="col-sm-2 control-label" for="input-type"><?php echo $entry_daytype; ?></label>';
	html += '<div class="col-sm-10"><select name="setting_dashboard[' + order_row + '][daytype]" class="form-control">';
		<?php foreach ($daytypes as $result){ ?>
	html += '<option value="<?php echo $result['daytype']; ?>"><?php echo $result['value']; ?></option>'; 
		<?php } ?>
	html += '</select></div>';
	html += '</div>';

	html += '	<div class="form-group">';
	html += '		<label class="col-sm-2 control-label" for="input-color' + order_row + '"><?php echo $entry_textcolor; ?></label>';
	html += '		<div class="col-sm-10">';
	html += ' 				<input type="text" name="setting_dashboard[' + order_row + '][text_color]" value="" placeholder="<?php echo $entry_textcolor; ?>" class="form-control color" />';
	html += '		</div>';
	html += '	</div>';
	html += '	<div class="form-group">';
	html += '		<label class="col-sm-2 control-label" for="input-bgcolor"><?php echo $entry_bgcolor; ?></label>';
	html += '		<div class="col-sm-10">';
	html += ' 				<input type="text" name="setting_dashboard[' + order_row + '][bg_color]" value="" placeholder="<?php echo $entry_bgcolor; ?>" class="form-control color" />';
	html += '		</div>';
	html += '	</div>';
	html += '	<div class="form-group">';
	html += '		<label class="col-sm-2 control-label" for="input-icon"><?php echo $entry_icon; ?></label>';
	html += '  		<div class="col-sm-4"><div class="input-group"><input type="text" name="setting_dashboard[' + order_row + '][icon]" dort_order="" placeholder="<?php echo $entry_icon; ?>" id="input-value' + order_row + '" class="form-control fontbox" /><span class="input-group-addon"></span></div></div>';
	html += '	</div>';
	html += '</div>';
	
	
	$('#tab-dashboard .tab-content').append(html);
		
	$('#dashboard-add').before('<li><a href="#tab-dashboard' + order_row + '" data-toggle="tab"><i class="fa fa-minus-circle" onclick="$(\'#dashboard a:first\').tab(\'show\'); $(\'a[href=\\\'#tab-dashboard' + order_row + '\\\']\').parent().remove(); $(\'#tab-dashboard' + order_row + '\').remove();"></i> <?php echo $tab_dashboard; ?> ' + order_row + '</a></li>');

	$('#dashboard a[href=\'#tab-dashboard' + order_row + '\']').tab('show');
		
	/*$('#tab-dashboard' + order_row + ' .form-group[data-sort]').detach().each(function() {
		if ($(this).attr('data-sort') >= 0 && $(this).attr('data-sort') <= $('#tab-dashboard' + order_row + ' .form-group').length) {
			$('#tab-dashboard' + order_row + ' .form-group').eq($(this).attr('data-sort')).before(this);
		}

		if ($(this).attr('data-sort') > $('#tab-dashboard' + order_row + ' .form-group').length) {
			$('#tab-dashboard' + order_row + ' .form-group:last').after(this);
		}

		if ($(this).attr('data-sort') < -$('#tab-dashboard' + order_row + ' .form-group').length) {
			$('#tab-dashboard' + order_row + ' .form-group:first').before(this);
		}
	});*/
		$(document).ready( function() {
			
            $('.color').each( function() {
               		$(this).minicolors({
					control: $(this).attr('data-control') || 'hue',
					defaultValue: $(this).attr('data-defaultValue') || '',
					inline: $(this).attr('data-inline') === 'true',
					letterCase: $(this).attr('data-letterCase') || 'lowercase',
					opacity: $(this).attr('data-opacity'),
					position: $(this).attr('data-position') || 'bottom left',
					change: function(hex, opacity) {
						if( !hex ) return;
						if( opacity ) hex += ', ' + opacity;
						try {
							console.log(hex);
						} catch(e) {}
					},
					theme: 'bootstrap'
				});
                
            });
			
		}); 
	order_row++;
		
}
//--></script>

<script type="text/javascript"><!--
$('#language a:first').tab('show');
$('#dashboard a:first').tab('show');
//--></script>
<script type="text/javascript"><!--
$('select[name=\'setting_country_id\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=localisation/country/country&token=<?php echo $token; ?>&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'setting_country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('.fa-spin').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('input[name=\'setting_postcode\']').parent().parent().addClass('required');
			} else {
				$('input[name=\'setting_postcode\']').parent().parent().removeClass('required');
			}

			html = '<option value=""><?php echo $text_select; ?></option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == '<?php echo $setting_zone_id; ?>') {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}

			$('select[name=\'setting_zone_id\']').html(html);
			$('select[name=\'setting_zone_id\']').trigger('change');
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'setting_country_id\']').trigger('change');
//--></script>

<script type="text/javascript"><!--
var payment_row = <?php echo $payment_row; ?>;

function addPayment() {
// Payment Method //
	html  = '<tr id="payment-row' + payment_row + '">';
	html += '<td class="text-left"><input type="text" name="setting_paymentmethod[' + payment_row + '][name]" class="form-control" placeholder="<?php echo $entry_name; ?>"/></td>';
    html += '<td class="text-left"><select name="setting_paymentmethod[' + payment_row + '][order_status_id]" class="form-control">';
// Payment Method //
    <?php foreach ($order_statuss as $orderstatus) { ?>
    html += '<option value="<?php echo $orderstatus['order_status_id']; ?>"><?php echo addslashes($orderstatus['name']); ?></option>';
    <?php } ?>
    html += '  </select></td>';
    html += '<td class="text-left"><input type="text" name="setting_paymentmethod[' + payment_row + '][sortorder]" class="form-control" placeholder="<?php echo $entry_sortorder; ?>"/></td>';
    html += '<td class="text-left"><button type="button" onclick="$(\'#payment-row' + payment_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
	html += '</tr>';

	$('#payment tbody').append(html);

	payment_row++;
}
//--></script>
<script>
$(document).ready( function() {		
	$('.color').each( function() {
    	$(this).minicolors({
			control: $(this).attr('data-control') || 'hue',
			defaultValue: $(this).attr('data-defaultValue') || '',
			inline: $(this).attr('data-inline') === 'true',
			letterCase: $(this).attr('data-letterCase') || 'lowercase',
			opacity: $(this).attr('data-opacity'),
			position: $(this).attr('data-position') || 'bottom left',
			change: function(hex, opacity) {
				if( !hex ) return;
				if( opacity ) hex += ', ' + opacity;
				try {
					console.log(hex);
				} catch(e) {}
			},
			theme: 'bootstrap'
		});
        
    });
	
});

</script>
<!-- new js code end here -->
<link rel="stylesheet" href="view/javascript/dist/css/fontawesome-iconpicker.min.css">
<script src="view/javascript/dist/js/fontawesome-iconpicker.js"></script>
 <script>
            $(function() {
                $('.fontbox').on('click', function() {
                    $('.fontbox').iconpicker();
                }).trigger('click');
            });
</script>
<?php echo $footer; ?>
<style>
#myModal ul li p{
	width:25%;
	display:inline-block;
}	
#myModal .close {
    color: #000;
    font-size: 25px;
    margin: -25px 0 0 !important;
    opacity: 1;
    position: absolute;
    right: 5px;
    top: auto;
}
.modal-title {
    color: #000;
    font-size: 18px;
    font-weight: bold;
    text-transform: uppercase;
}
ul li.active > a,ul li.active > a:hover,ul li.active > a:focus{
	background: #00a4e4 none repeat scroll 0 0 !important;
	color:#fff;
}
.nav-tabs li a{
	background:#E4E6EA;
}
.nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus{
	color:#fff;
}
#tab-sucess ul li.active > a,#tab-sucess ul li.active > a:hover,#tab-sucess ul li.active > a:focus{
	background: #fff !important;
	color: #333 !important;
}
    #tab-customermail  ul li{
        width:23%;
        display: inline-block;
    }
 #tab-customermail  ul li p{
        width:35%;
     display: inline-block;
    }
</style>
