<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-module" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach($breadcrumbs as $breadcrumb) { ?>
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
				  <select name="me_admin_search_status" id="input-status" class="form-control">
					<?php if($me_admin_search_status) { ?>
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
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaypname; ?>"><?php echo $entry_displaypname; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[pname][status]" value="1" <?php if(!empty($me_admin_search_filter['pname']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[pname][status]" value="0" <?php if(empty($me_admin_search_filter['pname']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaypmodel; ?>"><?php echo $entry_displaypmodel; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[pmodel][status]" value="1" <?php if(!empty($me_admin_search_filter['pmodel']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[pmodel][status]" value="0" <?php if(empty($me_admin_search_filter['pmodel']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaypsku; ?>"><?php echo $entry_displaypsku; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[psku][status]" value="1" <?php if(!empty($me_admin_search_filter['psku']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[psku][status]" value="0" <?php if(empty($me_admin_search_filter['psku']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaycategory; ?>"><?php echo $entry_displaycategory; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[category][status]" value="1" <?php if(!empty($me_admin_search_filter['category']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[category][status]" value="0" <?php if(empty($me_admin_search_filter['category']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaymanufacturer; ?>"><?php echo $entry_displaymanufacturer; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[manufacturer][status]" value="1" <?php if(!empty($me_admin_search_filter['manufacturer']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[manufacturer][status]" value="0" <?php if(empty($me_admin_search_filter['manufacturer']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displayoption; ?>"><?php echo $entry_displayoption; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[option][status]" value="1" <?php if(!empty($me_admin_search_filter['option']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[option][status]" value="0" <?php if(empty($me_admin_search_filter['option']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaycustomer; ?>"><?php echo $entry_displaycustomer; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[customer][status]" value="1" <?php if(!empty($me_admin_search_filter['customer']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[customer][status]" value="0" <?php if(empty($me_admin_search_filter['customer']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaycustomeremail; ?>"><?php echo $entry_displaycustomeremail; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[customer_email][status]" value="1" <?php if(!empty($me_admin_search_filter['customer_email']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[customer_email][status]" value="0" <?php if(empty($me_admin_search_filter['customer_email']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaycustomertelephone; ?>"><?php echo $entry_displaycustomertelephone; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[customer_telephone][status]" value="1" <?php if(!empty($me_admin_search_filter['customer_telephone']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[customer_telephone][status]" value="0" <?php if(empty($me_admin_search_filter['customer_telephone']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displayorderid; ?>"><?php echo $entry_displayorderid; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderid][status]" value="1" <?php if(!empty($me_admin_search_filter['orderid']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderid][status]" value="0" <?php if(empty($me_admin_search_filter['orderid']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displayorderbycustomer; ?>"><?php echo $entry_displayorderbycustomer; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderbycustomer][status]" value="1" <?php if(!empty($me_admin_search_filter['orderbycustomer']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderbycustomer][status]" value="0" <?php if(empty($me_admin_search_filter['orderbycustomer']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displayorderbycustomertel; ?>"><?php echo $entry_displayorderbycustomertel; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderbycustomertel][status]" value="1" <?php if(!empty($me_admin_search_filter['orderbycustomertel']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderbycustomertel][status]" value="0" <?php if(empty($me_admin_search_filter['orderbycustomertel']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displayorderbyproduct; ?>"><?php echo $entry_displayorderbyproduct; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderbyproduct][status]" value="1" <?php if(!empty($me_admin_search_filter['orderbyproduct']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderbyproduct][status]" value="0" <?php if(empty($me_admin_search_filter['orderbyproduct']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displayorderstatus; ?>"><?php echo $entry_displayorderstatus; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderstatus][status]" value="1" <?php if(!empty($me_admin_search_filter['orderstatus']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderstatus][status]" value="0" <?php if(empty($me_admin_search_filter['orderstatus']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displayordertotal; ?>"><?php echo $entry_displayordertotal; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[ordertotal][status]" value="1" <?php if(!empty($me_admin_search_filter['ordertotal']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[ordertotal][status]" value="0" <?php if(empty($me_admin_search_filter['ordertotal']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displayorderdate; ?>"><?php echo $entry_displayorderdate; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderdate][status]" value="1" <?php if(!empty($me_admin_search_filter['orderdate']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[orderdate][status]" value="0" <?php if(empty($me_admin_search_filter['orderdate']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_displaycoupon; ?>"><?php echo $entry_displaycoupon; ?></span></label>
				<div class="col-sm-4">
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[coupon][status]" value="1" <?php if(!empty($me_admin_search_filter['coupon']['status'])) { ?>checked="checked"<?php } ?> />
					<?php echo $text_yes; ?>
					</label>
					<label class="radio-inline"> 
					<input type="radio" name="me_admin_search_filter[coupon][status]" value="0" <?php if(empty($me_admin_search_filter['coupon']['status'])) { ?>checked="checked" <?php } ?>/>
					<?php echo $text_no; ?>
					</label>
				</div>
			</div>
        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>