<!--/***********************
// @category  : OpenCart
// @module    : Spin Wheel Popup
// @author    : OpencartMarketplace <support@opencartmarketplace.com>
***********************-->	
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
		<?php if($module_id){ ?>
			<button type="submit" form="form-spin-wheel" name="save_stay" data-toggle="tooltip" value="1" title="<?php echo $button_save_stay; ?>" class="btn btn-success"><i class="fa fa-save"></i> &nbsp;<?php echo $button_save_stay; ?></button>
		<?php } ?>	
        <button type="submit" form="form-spin-wheel" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
	<?php if(isset($success) && $success){ ?>
		<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		</div>
	<?php } ?>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-spin-wheel" class="form-horizontal">
			<div class="form-group">
				<label class="col-sm-2 control-label">Name</label>
				<div class="col-sm-10">
					<input type="text" name="name" value="<?php echo $name; ?>" class="form-control" />
					<?php if ($error_name){ ?>
						<div class="text-danger"><?php echo $error_name ?></div>
					<?php } ?>					
				</div>
			</div>
			<div class="col-md-2">
			  <ul class="nav nav-tabs nav-pills nav-stacked ocmp-nav-tabs">
				<li class="active"><a href="#general-setting" data-toggle="tab"><i class="fa fa-gear fa-2x"></i>&nbsp;General Setting</a></li>
				<li><a href="#display-setting" data-toggle="tab"><i class="fa fa-tv fa-2x"></i>&nbsp;Display Setting</a></li>
				<li><a href="#look-setting" data-toggle="tab"><i class="fa fa-eye fa-2x"></i>&nbsp;Look & Fell Setting</a></li>
				<li><a href="#text-setting" data-toggle="tab"><i class="fa fa-file-text fa-2x"></i>&nbsp;Text Setting</a></li>
				<li><a href="#coupon-setting" data-toggle="tab"><i class="fa fa-gift fa-2x"></i>&nbsp;Coupon Setting</a></li>
				<li><a href="#email-setting" data-toggle="tab"><i class="fa fa-envelope fa-2x"></i>&nbsp;Email Setting</a></li>
				<li><a href="#statistics" data-toggle="tab"><i class="fa fa-info fa-2x"></i>&nbsp;Statistics</a></li>
				<li><a href="#about-us" data-toggle="tab"><i class="fa fa-info fa-2x"></i>&nbsp;About Us</a></li>
			  </ul>
			</div>		
			<div class="col-md-10">
				<div class="tab-content">
					<div id="general-setting" class="tab-pane fade in active">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
							<div class="col-sm-5 switch ocmp-switch">
								<?php if ($status){ ?>
									<input type="radio" value="1" id="input-status-1" name="status"checked="checked" /><label for="input-status-1"><?php echo $text_enabled; ?></label>
									<input type="radio" value="0" id="input-status-0" name="status" /><label for="input-status-0"><?php echo $text_disabled; ?></label>
								<?php } else { ?>
									<input type="radio" value="1" id="input-status-1" name="status" /><label for="input-status-1"><?php echo $text_enabled; ?></label>
									<input type="radio" value="0" id="input-status-0"name="status" checked="checked" /><label for="input-status-0"><?php echo $text_disabled; ?></label>
								<?php } ?>
								<a class="slide-button btn"></a>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Store</label>
							<div class="col-sm-8">
								<div class="ocmp-well">
									<?php foreach($stores as $store){ ?>
										<div class="ocmp-checkbox">
											<label for="stores_<?php echo $store['store_id']; ?>">
											<?php $checked = (isset($spin_wheel['stores']) && in_array($store['store_id'], $spin_wheel['stores'])) ? 'checked="checked"' : ''; ?>
												<input type="checkbox" name="spin_wheel[stores][<?php echo $store['store_id']?>]" id="stores_<?php echo $store['store_id']; ?>" value="<?php echo $store['store_id']; ?>" <?php echo $checked; ?>>
											&nbsp;&nbsp;<?php echo $store['name']; ?></label>
										</div>	
									<?php } ?>
								</div>	
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">Customer Group</label>
							<div class="col-sm-8">
								<div class="ocmp-well">
									<?php foreach($customer_groups as $customer_group){ ?>
										<div class="ocmp-checkbox">
											<label for="customer_group_<?php echo $customer_group['customer_group_id']; ?>">
											
											<?php $checked = (isset($spin_wheel['customer_group'])  && in_array($customer_group['customer_group_id'], $spin_wheel['customer_group']))  ? 'checked="checked"' : ''; ?>
												<input type="checkbox" name="spin_wheel[customer_group][<?php echo $customer_group['customer_group_id']; ?>]" id="customer_group_<?php echo $customer_group['customer_group_id']; ?>" value="<?php echo $customer_group['customer_group_id']; ?>"  <?php echo $checked; ?>>
											
											&nbsp;&nbsp;<?php echo $customer_group['name']; ?></label>
										</div>	
									<?php } ?>
								</div>	
							</div>
						</div>						
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-close"><span data-toggle="tooltip" data-original-title="<?php echo $help_close; ?>"><?php echo $entry_close; ?></span></label>
							<div class="col-sm-3 switch ocmp-switch">
								<?php if ($spin_wheel['close']){ ?>
									<input type="radio" value="1" id="input-close-1" name="<?php echo $prefix;?>[close]"checked="checked" /><label for="input-close-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-close-0" name="<?php echo $prefix;?>[close]" /><label for="input-close-0"><?php echo $text_no; ?></label>
								<?php } else { ?>
									<input type="radio" value="1" id="input-close-1" name="<?php echo $prefix;?>[close]" /><label for="input-close-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-close-0"name="<?php echo $prefix;?>[close]" checked="checked" /><label for="input-close-0"><?php echo $text_no; ?></label>
								<?php } ?>
								<a class="slide-button btn"></a>
							</div>
						</div>		
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-display-interval"><span data-toggle="tooltip" data-original-title="<?php echo $help_dis_intervel; ?>"><?php echo $entry_display_itrvl; ?></span></label>
							<div class="col-sm-3">
								<input type="text" name="<?php echo $prefix;?>[display_interval]" value="<?php echo $spin_wheel['display_interval']; ?>" class="form-control" />
							</div>	
						</div>						
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-email-recheck"><span data-toggle="tooltip" data-original-title="<?php echo $help_email_recheck; ?>"><?php echo $entry_email_recheck; ?></span></label>
							<div class="col-sm-3 switch ocmp-switch">
								<?php if ($spin_wheel['email_recheck']){ ?>
									<input type="radio" value="1" id="input-email-recheck-1" name="<?php echo $prefix;?>[email_recheck]"checked="checked" /><label for="input-email-recheck-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-email-recheck-0" name="<?php echo $prefix;?>[email_recheck]" /><label for="input-email-recheck-0"><?php echo $text_no; ?></label>
								<?php } else { ?>
									<input type="radio" value="1" id="input-email-recheck-1" name="<?php echo $prefix;?>[email_recheck]" /><label for="input-email-recheck-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-email-recheck-0"name="<?php echo $prefix;?>[email_recheck]" checked="checked" /><label for="input-email-recheck-0"><?php echo $text_no; ?></label>
								<?php } ?>
								<a class="slide-button btn"></a>
							</div>	
						</div>							
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-sound"><span data-toggle="tooltip" data-original-title="<?php echo $help_sound; ?>"><?php echo $entry_sound; ?></span></label>
							<div class="col-sm-3 switch ocmp-switch">
								<?php if ($spin_wheel['sound']){ ?>
									<input type="radio" value="1" id="input-sound-1" name="<?php echo $prefix;?>[sound]"checked="checked" /><label for="input-sound-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-sound-0" name="<?php echo $prefix;?>[sound]" /><label for="input-sound-0"><?php echo $text_no; ?></label>
								<?php } else { ?>
									<input type="radio" value="1" id="input-sound-1" name="<?php echo $prefix;?>[sound]" /><label for="input-sound-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-sound-0"name="<?php echo $prefix;?>[sound]" checked="checked" /><label for="input-sound-0"><?php echo $text_no; ?></label>
								<?php } ?>
								<a class="slide-button btn"></a>
							</div>	
						</div>							
			
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-firework"><span data-toggle="tooltip" data-original-title="<?php echo $help_firework; ?>"><?php echo $entry_firework; ?></span></label>
							<div class="col-sm-3 switch ocmp-switch">
								<?php if ($spin_wheel['firework']){ ?>
									<input type="radio" value="1" id="input-firework-1" name="<?php echo $prefix;?>[firework]"checked="checked" /><label for="input-firework-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-firework-0" name="<?php echo $prefix;?>[firework]" /><label for="input-firework-0"><?php echo $text_no; ?></label>
								<?php } else { ?>
									<input type="radio" value="1" id="input-firework-1" name="<?php echo $prefix;?>[firework]" /><label for="input-firework-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-firework-0"name="<?php echo $prefix;?>[firework]" checked="checked" /><label for="input-firework-0"><?php echo $text_no; ?></label>
								<?php } ?>
								<a class="slide-button btn"></a>
							</div>
						</div>							
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-css"><span data-toggle="tooltip" data-original-title="<?php echo $help_css; ?>"><?php echo $entry_css; ?></span></label>
							<div class="col-sm-8">
								<textarea type="text" name="<?php echo $prefix; ?>[css]" class="form-control" rows="5"><?php echo $spin_wheel['css']; ?></textarea>
							</div>	
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-js"><span data-toggle="tooltip" data-original-title="<?php echo $help_js; ?>"><?php echo $entry_js; ?></span></label>
							<div class="col-sm-8">
								<textarea type="text" name="<?php echo $prefix; ?>[js]" class="form-control" rows="5"><?php echo $spin_wheel['js']; ?></textarea>
							</div>	
						</div>						
					</div> 
					<div id="display-setting" class="tab-pane fade">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-hide-after"><span data-toggle="tooltip" data-original-title="<?php echo $help_hide_after; ?>"><?php echo $entry_hide_after; ?></span></label>
							<div class="col-sm-4">
								<select name="<?php echo $prefix;?>[hide_after]" class="form-control">
									<?php foreach($hide_after as $key => $hdat){ ?>
										<?php if($key == $spin_wheel['hide_after']){ ?>
											<option value="<?php echo $key; ?>" selected="selected"><?php echo $hdat; ?></option>
										<?php }else{ ?>	
											<option value="<?php echo $key; ?>"><?php echo $hdat; ?></option>
										<?php } ?>	
									<?php } ?>
								</select>
							</div>	
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-fix_time"><span data-toggle="tooltip" data-original-title="<?php echo $help_fix_time; ?>"><?php echo $entry_fix_time; ?></span></label>
							<div class="col-sm-3 switch ocmp-switch fix-date">
								<?php if ($spin_wheel['fix_time']){ ?>
									<input type="radio" value="1" id="input-fix_time-1" name="<?php echo $prefix;?>[fix_time]"checked="checked" /><label for="input-fix_time-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-fix_time-0" name="<?php echo $prefix;?>[fix_time]" /><label for="input-fix_time-0"><?php echo $text_no; ?></label>
								<?php } else { ?>
									<input type="radio" value="1" id="input-fix_time-1" name="<?php echo $prefix;?>[fix_time]" /><label for="input-fix_time-1"><?php echo $text_yes; ?></label>
									<input type="radio" value="0" id="input-fix_time-0"name="<?php echo $prefix;?>[fix_time]" checked="checked" /><label for="input-fix_time-0"><?php echo $text_no; ?></label>
								<?php } ?>
								<a class="slide-button btn"></a>
							</div>	
						</div>	
						<div class="fixdate" style="display:<?php echo ($spin_wheel['fix_time']) ? 'block' : 'none;' ?>">
							<div class="form-group active-date">
								<label class="col-sm-2 control-label" for="input-active-date"><?php echo $entry_active_date; ?></label>
								<div class="col-sm-4">
									<div class="input-group date">
										<input type="text" name="<?php echo $prefix;?>[active_date]" value="<?php echo $spin_wheel['active_date']; ?>" class="form-control date-time" />
										<span class="input-group-btn"><button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button></span>
									</div>										
								</div>	
							</div>
							<div class="form-group expire-date">
								<label class="col-sm-2 control-label" for="input-expire-date"><?php echo $entry_expire_date; ?></label>
								<div class="col-sm-4">
									<div class="input-group date">
										<input type="text" name="<?php echo $prefix;?>[expire_date]" value="<?php echo $spin_wheel['expire_date']; ?>" class="form-control" />
										<span class="input-group-btn"><button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button></span>
									</div>	
								</div>	
							</div>
						</div>	
						
						<div class="form-group">
							<label class="control-label col-sm-2"><span class="label-tooltip" data-toggle="tooltip"  data-original-title="">When to Display</span></label>
							<div class="col-sm-4 WhenToDisplay">
								<select name="<?php echo $prefix;?>[when_to_display]" class="form-control" id="spin_wheel[where_to_display]">
									<?php foreach($when_to_display as $key => $vaule){ ?>
										<?php if($spin_wheel['when_to_display'] == $key){ ?>
											<option value="<?php echo $key; ?>" selected="selected"><?php echo $vaule; ?></option>
										<?php }else{ ?>	
											<option value="<?php echo $key; ?>"><?php echo $vaule; ?></option>
										<?php } ?>	
									<?php } ?>
								</select>
							</div>
						</div>	
						<div class="form-group spin_popup_time" style="display:<?php echo ($spin_wheel['when_to_display'] == 2) ? 'block;' : 'none;'; ?>">
							<label class="control-label col-sm-2">Display Time</label>
							<div class="col-sm-4">
								<input type="text" name="<?php echo $prefix;?>[spin_popup_time]" class="form-control" value="<?php echo $spin_wheel['spin_popup_time']; ?>" />
							</div>
						</div>
						<div class="form-group spin_scroll_time" style="display:<?php echo ($spin_wheel['when_to_display'] == 3) ? 'block;' : 'none;'; ?>">
							<label class="control-label col-sm-2">Enter Scroll</label>
							<div class="col-sm-4">
								<input type="text" name="<?php echo $prefix;?>[spin_scroll_time]" class="form-control" value="<?php echo $spin_wheel['spin_scroll_time']; ?>"  />
							</div>
						</div>						
					</div> 
					<div id="look-setting" class="tab-pane fade">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-size"><span data-toggle="tooltip" data-original-title="<?php echo $help_display; ?>"><?php echo $entry_display; ?></span></label>
							<div class="col-sm-4">
								<select name="<?php echo $prefix;?>[display]" data-class="" data-type="" class="form-control">
									<?php if($spin_wheel['display'] == 'full'){ ?>
										<option value="full" selected="selected">Full Width</option>
										<option value="default">Default</option>
									<?php }else{ ?>	
										<option value="full">Full Width</option>
										<option value="default" selected="selected">Default</option>
									<?php } ?>											
								</select>
							</div>	
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-theme"><?php echo $entry_wheel_pview; ?></label>
							<div class="col-sm-10 wheel_preview" style="background:<?php echo ($spin_wheel['bg_type'] == 1) ? 'url(' . $bg_url . $spin_wheel['bg_image'] . '); background-size:cover;' : $spin_wheel['background_color']; ?>">&nbsp;&nbsp;
								<div class="wheel-text-preview">
									<img class="wheel-prew-img" src="view/javascript/jquery/ocmp_spin_wheel/image/wheel_preview.png">
									<div class="sample-text">
										<span class="wheel-coupon text-1" style="transform: rotate(-0deg) translate(10px, -50%);">25% OFF</span>
										<span class="wheel-coupon text-2" style="transform: rotate(-30deg) translate(10px, -50%);">Opps,Sorry</span>
										<span class="wheel-coupon text-3" style="transform: rotate(-60deg) translate(10px, -50%);">Free Shipping</span>
										<span class="wheel-coupon text-4" style="transform: rotate(-90deg) translate(10px, -50%);">No Offer Today</span>
										<span class="wheel-coupon text-5" style="transform: rotate(-120deg) translate(10px, -50%);">35% OFF</span>
										<span class="wheel-coupon text-6" style="transform: rotate(-150deg) translate(10px, -50%);">Win $10</span>

										<span class="wheel-coupon text-7" style="transform: rotate(-180deg) translate(10px, -50%);">No OFFER</span>
										<span class="wheel-coupon text-8" style="transform: rotate(-210deg) translate(10px, -50%);">50% OFF</span>
										<span class="wheel-coupon text-9" style="transform: rotate(-240deg) translate(10px, -50%);">Opps, Sorry!</span>
										<span class="wheel-coupon text-10" style="transform: rotate(-270deg) translate(10px, -50%);">75% OFF</span>
										<span class="wheel-coupon text-11" style="transform: rotate(-300deg) translate(10px, -50%);">0% OFF</span>
										<span class="wheel-coupon text-12" style="transform: rotate(-330deg) translate(10px, -50%);">100% OFF</span>										
									</div>
								</div>	
								<div class="spin-desc-preview">
									<div class="spin-title">Special bounus unlocked</div>
									<div class="spin-description">
										<p>You have a chance to win a nice big discount. Are you ready?</p>
										<p>You can spin the wheel only once. If you win, you can claim your coupon for 1 day only!</p>
									</div>
									<div class="spin-form">
										<input type="text" data-class="" data-type="" placeholder="Please enter email" disabled="disabled" class="form-control"><br>
										<button class="btn btn-block btn_color" disabled="disabled">Please Try Luck</button>
										<span class="no_luck">No, I do not feel lucky  X</span>
									</div>
								</div>
							</div>	
						</div>
						<div class="form-group bg-type">
							<label class="col-sm-2 control-label" for="input-bg-type">Background</label>
							<div class="col-sm-4 switch ocmp-switch">
								<?php if ($spin_wheel['bg_type'] == 1){ ?>
									<input type="radio" value="1" id="input-bg_type-1" name="<?php echo $prefix;?>[bg_type]"checked="checked" data-class="" data-type="" /><label for="input-bg_type-1">Image</label>
									<input type="radio" value="2" id="input-bg_type-2" name="<?php echo $prefix;?>[bg_type]" data-class="" data-type="" /><label for="input-bg_type-2">Color</label>
								<?php } else { ?>
									<input type="radio" value="1" id="input-bg_type-1" name="<?php echo $prefix;?>[bg_type]" data-class="" data-type="" /><label for="input-bg_type-1">Image</label>
									<input type="radio" value="2" id="input-bg_type-2" data-class="" data-type="" name="<?php echo $prefix;?>[bg_type]" checked="checked" /><label for="input-bg_type-2">Color</label>
								<?php } ?>
								<a class="slide-button btn"></a>
							</div>
						</div>
						<div class="form-group bg-image" style="display:<?php echo ($spin_wheel['bg_type'] == 1) ? 'block;' : 'none;';?>">
							<label class="col-sm-2 control-label" for="input-bg-mage">Background Image</label>
							<div class="col-sm-8">
								<a href="" id="thumb-image" data-toggle="image" class="img-thumbnail"><img src="<?php echo $thumb; ?>" alt="" title="" data-placeholder=""></a>
								<input type="hidden" name="<?php echo $prefix;?>[bg_image]" value="<?php echo $spin_wheel['bg_image']; ?>" data-url="<?php echo $bg_url; ?>" id="input-image">
								<a id="update-bg-color" data-toggle="tooltip" data-original-title=
								"Please upload image and click on refresh button to see wheel background" ><i class="fa fa-refresh"></i></a>
							</div>	
						</div>						

						<div class="form-group">
							<div class="col-sm-4">
								<label class="control-label" for="input-wheel-color"><?php echo $entry_wheel_color; ?></label>
								<div class="col-sm-13 wheel_color">
									<input type="text" name="<?php echo $prefix;?>[wheel_color]" data-class="wheel_color" data-type="" value="<?php echo $spin_wheel['wheel_color']; ?>" class="wheel-color" />
								</div>	
							</div>
							<div class="col-sm-4">
								<label class="control-label" for="input-wheel-font-color"><?php echo $entry_wheel_font_color; ?></label>
								<div class="col-sm-13 colorpicker">
									<input type="text" data-class="wheel-coupon" data-type="color" name="<?php echo $prefix;?>[wheel_font_color]" value="<?php echo $spin_wheel['wheel_font_color']; ?>" />
								</div>	
							</div>	
							<div class="col-sm-4 bg-color"  style="display:<?php echo ($spin_wheel['bg_type'] == 2) ? 'block;' : 'none;';?>">
								<label class="col-sm-21 control-label" for="input-bg-color"><?php echo $entry_background_color; ?></label>
								<div class="col-sm-13 colorpicker">
									<input type="text" data-class="wheel_preview" data-type="background" name="<?php echo $prefix;?>[background_color]" value="<?php echo $spin_wheel['background_color']; ?>" />
								</div>								
							</div>
						</div>	
						<div class="form-group">
							<div class="col-sm-4">
								<label class="col-sm-21 control-label" for="input-text-color"><?php echo $entry_text_color; ?></label>
								<div class="col-sm-13 colorpicker">
									<input type="text" data-class="spin-description p" data-type="color" name="<?php echo $prefix;?>[text_color]" value="<?php echo $spin_wheel['text_color']; ?>" />
								</div>									
							</div>
							<div class="col-sm-4">
								<label class="col-sm-21 control-label" for="input-button-bg-color"><?php echo $entry_btn_bg_color; ?></label>
								<div class="col-sm-13 colorpicker">
									<input type="text" data-class="btn_color" data-type="background" name="<?php echo $prefix;?>[button_bg_color]" value="<?php echo $spin_wheel['button_bg_color']; ?>" />
								</div>	
							</div>
							<div class="col-sm-4">
								<label class="col-sm-21 control-label" for="input-btn-text-color"><?php echo $entry_btn_text_color; ?></label>
								<div class="col-sm-13 colorpicker">
									<input type="text" data-class="btn_color" data-type="color" name="<?php echo $prefix;?>[btn_text_color]" value="<?php echo $spin_wheel['btn_text_color']; ?>" />
								</div>								
							</div>
						</div>	
						<div class="form-group">
							<div class="col-sm-4">
								<label class="col-sm-21 control-label" for="input-noluck-color"><?php echo $entry_noluck_color; ?></label>
								<div class="col-sm-13 colorpicker">
									<input type="text" data-class="no_luck" data-type="color" name="<?php echo $prefix;?>[text_noluck_color]" value="<?php echo $spin_wheel['text_noluck_color']; ?>" />
								</div>								
							</div>						
						</div>		
					</div>
					<div id="text-setting" class="tab-pane fade">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-title"><?php echo $entry_title; ?></label>
							<div class="col-sm-6">
								<?php foreach($languages as $language){ ?>
									<div class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" data-original-title="<?php echo $language['name']; ?>"><img src="<?php echo $language['image']; ?>"/></span>
										<input type="text" name="<?php echo $prefix;?>[<?php echo $language['language_id']; ?>][title]" value="<?php echo isset($spin_wheel[$language['language_id']]) ? $spin_wheel[$language['language_id']]['title'] : ''; ?>" class="form-control" />
									</div>	<br>
								<?php } ?>	
							</div>	
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-sub-title"><?php echo $entry_sub_title; ?></label>
							<div class="col-sm-5">
								<?php foreach($languages as $language){ ?>
									<div class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" data-original-title="<?php echo $language['name']; ?>"><img src="<?php echo $language['image']; ?>"/></span>
										<input type="text" name="<?php echo $prefix;?>[<?php echo $language['language_id']; ?>][sub_title]" value="<?php echo isset($spin_wheel[$language['language_id']]) ? $spin_wheel[$language['language_id']]['sub_title'] : ''; ?>" class="form-control" />
									</div><br>	
								<?php } ?>	
							</div>	
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-note"><?php echo $entry_note; ?></label>
							<div class="col-sm-6">
								<?php foreach($languages as $language){ ?>
									<div class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" data-original-title="<?php echo $language['name']; ?>"><img src="<?php echo $language['image']; ?>"/></span>
										<textarea name="<?php echo $prefix;?>[<?php echo $language['language_id']; ?>][note]" class="form-control"><?php echo isset($spin_wheel[$language['language_id']]) ? $spin_wheel[$language['language_id']]['note'] : ''; ?></textarea>
									</div><br>	
								<?php } ?>	
							</div>	
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-btn-label"><?php echo $entry_btn_label; ?></label>
							<div class="col-sm-6">
								<?php foreach($languages as $language){ ?>
									<div class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" data-original-title="<?php echo $language['name']; ?>"><img src="<?php echo $language['image']; ?>"/></span>
										<input type="text" name="<?php echo $prefix;?>[<?php echo $language['language_id']; ?>][btn_label]" class="form-control" value="<?php echo isset($spin_wheel[$language['language_id']]) ? $spin_wheel[$language['language_id']]['btn_label'] : ''; ?>" />
									</div><br>	
								<?php } ?>	
							</div>	
						</div>	
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-no-luck"><?php echo $entry_text_no_luck; ?></label>
							<div class="col-sm-6">
								<?php foreach($languages as $language){ ?>
									<div class="input-group">
										<span class="input-group-addon" data-toggle="tooltip" data-original-title="<?php echo $language['name']; ?>"><img src="<?php echo $language['image']; ?>"/></span>
										<input type="text" name="<?php echo $prefix;?>[<?php echo $language['language_id']; ?>][text_no_luck]" class="form-control" value="<?php echo isset($spin_wheel[$language['language_id']]) ? $spin_wheel[$language['language_id']]['text_no_luck'] : ''; ?>" />
									</div><br>	
								<?php } ?>	
							</div>	
						</div>						
					</div>
					<div id="coupon-setting" class="tab-pane fade">
						<div class="panel panel-default">
							<div class="panel-heading">
								<h3 class="panel-title">Offer Setting</h3>
							</div>
							<div class="panel-body">	
								<div class="table-responsive">
									<table class="table table-bordered" id="wheel-discount">
										<thead>
											<tr>
												<td>S. No.</td>
												<td>Coupon Type</td>
												<td>Label</td>
												<td>Coupon Value</td>
												<td>Gravity</td>
												<td>Action</td>
											</tr>
										</thead>
										<tbody>
											<?php foreach($WheelCoupons as $WheelCoupon){ ?>
											<tr class="discount-row-<?php echo $WheelCoupon['offer_id']; ?>">
												<td class="sr-no"><?php echo $WheelCoupon['offer_id']; ?></td>
												<td class="type"><?php echo $WheelCoupon['type']; ?></td>
												<td class="Dlabel"><?php echo $WheelCoupon['label']; ?></td>
												<td class="discount"><?php echo $WheelCoupon['discount']; ?></td>
												<td class="gravity"><?php echo $WheelCoupon['gravity']; ?></td>
												<td><a href="<?php echo $WheelCoupon['href']; ?>" class="btn btn-sm btn-info BtnWheelEdit" data-toggle="modal"  data-target="WheelModalForm">Edit</a></td>
											</tr>
										<?php } ?>
										</tbody>
											
									</table>	
								</div>	
							</div>	
						</div>	
					</div>					
					<div id="email-setting" class="tab-pane fade">
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-coupon"><?php echo $entry_display_coupon; ?></label>
							<div class="col-sm-5">
								<select name="<?php echo $prefix;?>[display_coupon]" class="form-control">
									<?php foreach($display_coupon as $key => $value){ ?>
										<?php if($key == $spin_wheel['display_coupon']){ ?>
											<option value="<?php echo $key; ?>" selected="selected"><?php echo $value; ?></option>
										<?php }else{ ?>
											<option value="<?php echo $key; ?>"><?php echo $value; ?></option>
										<?php } ?>										
									<?php } ?>	
								</select>								
							</div>	
						</div>		
						<div id="EmailSettings">	
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-email-subject"><?php echo $entry_email_subject; ?></label>
								<div class="col-sm-5">
									<?php foreach($languages as $language){ ?>
										<div class="input-group">
											<span class="input-group-addon"><img src="<?php echo $language['image']; ?>" /></span>
											<input name="<?php echo $prefix;?>[<?php echo $language['language_id']; ?>][email_subject]" value="<?php echo isset($spin_wheel[$language['language_id']]) ? $spin_wheel[$language['language_id']]['email_subject'] : ''; ?>" class="form-control" />
										</div><br>
									<?php } ?>
								</div>	
							</div>								
							<div class="form-group">
								<label class="col-sm-2 control-label" for="input-email-content"><?php echo $entry_email_content; ?>
									<div class="info">
										You can use below code to send email :<br>
										<span class="label btn-info">{firstname}</span> = For showing Firstname<br>
										<span class="label btn-info">{lastname}</span>  = For showing Lastname<br>
										<span class="label btn-info">{discount}</span>  = For showing Discount<br>
										<span class="label btn-info">{total}</span>  = For showing Total must be reached then coupon is valid!<br>
										<span class="label btn-info">{coupon_code}</span>  = For showing Coupon code<br>
									</div>
								</label>
								<div class="col-sm-10">
									<?php foreach($languages as $language){ ?>
											<img src="<?php echo $language['image']; ?>" />
											<textarea name="<?php echo $prefix;?>[<?php echo $language['language_id']; ?>][email_content]" class="form-control summernote" data-toggle="summernote"><?php echo isset($spin_wheel[$language['language_id']]) ? $spin_wheel[$language['language_id']]['email_content'] : ''; ?></textarea>
										<br>
									<?php } ?>
								</div>	
							</div>								

						</div>
					</div>					
					<div id="statistics" class="tab-pane fade">
						
						<div class="tabe-responsive">
							<table id="table-statistics" class="table table-bordered" style="width:100%;">
								<thead>
									<tr class="rows" style="width:100%">
										<td>ID</td>
										<td>Coupon Code</td>
										<td>Firstname</td>
										<td>Lastname</td>
										<td>Email</td>
										<td>Country</td>
										<td>Ip</td>
										<td>Date Added</td>
									</tr>		
								</thead>
								<tbody>
								<?php foreach($statistics as $statistic){ ?>
									<tr>
										<td><?php echo $statistic['spin_form_id']; ?></td>
										<td><?php echo $statistic['code']; ?></td>
										<td><?php echo $statistic['firstname']; ?></td>
										<td><?php echo $statistic['lastname']; ?></td>
										<td><?php echo $statistic['email']; ?></td>
										<td><?php echo $statistic['country']; ?></td>
										<td><?php echo $statistic['ip']; ?></td>
										<td><?php echo $statistic['date_added']; ?></td>
									</tr>	
								<?php } ?>	
								</tbody>								
							</table>
						</div>

					</div>					
					<div id="about-us" class="tab-pane fade">
						<div class="panel panel-default" style="overflow:hidden;">
							<div class="panel-heading"><b>Extension information</b></div>
							<table class="table">
								<tr>
									<td>Extension name:</td>
									<td><i class="fa fa-external-link"></i> &nbsp;<a  target="_blank" href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=35175">Spin Wheel Popup</a></td>
								</tr>
								<tr>
									<td>Version Compatibility:</td>
									<td>2.0.x, 2.1.x, 2.2.x, 2.3.x, 3.x.x</td>
								</tr>					
								<tr>
									<td>Documentation:</td>
									<td>Include in ZIP file</td>
								</tr>
								<tr>
									<td>Our More Products:</td>
									<td><a  target="_blank" href="https://www.opencart.com/index.php?route=marketplace/extension&filter_member=OpencartMarketplace">OpencartMarketplace</a></td>
								</tr>						
							</table>
						</div>
						<div class="panel panel-default" style="overflow:hidden;">
							<div class="panel-heading"><b>Support & Suggesion</b></div>
							<table class="table">
								<tr>
									<td>Email:</td>
									<td><a href="mailto:support@opencartmarketplace.com">support@opencartmarketplace.com</a></td>
								</tr>
								<tr>
									<td>Skype:</td>
									<td>scf8127</td>
								</tr>
								<tr>
									<td>Website:</td>
									<td><a target="_blank" href="http://opencartmarketplace.com">http://opencartmarketplace.com</a></td>
								</tr>												
							</table>
						</div>
						<div class="panel panel-default" style="overflow:hidden;">
							<div class="panel-heading"><b>Premium service</b></div>
							<div class="panel-body">
								<b>Additional work:</b><br>
								<p>This service includes: implement some additional functionality, resolving conflicts with other extensions/themes, editing of existing module functionality.
								<span class="badge">18.00 USD / hour</span>
								</p>
								<b>Integration module on custom template:</b><br>
								<p>This service includes: installation of the product by our professional on one non-standard template of your store.<br>
								<span class="badge">20.00 USD / 1 Template</span>
								</p>
							</div>
						</div>		
					</div>
				</div>	
			</div>
       </form>
      </div>
    </div>
  </div>
	<!-- MODEL POPUP FOR DISCOUNT -->
	<div class="modal fade ocmp-model-popup" id="WheelModalForm" role="dialog" aria-hidden="true" style="display: none;">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h4 class="modal-title">Discount Settings</h4>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" id="btn-discount-save">Save</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
		</div>
	</div>	
  <script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
  <link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
  <script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
  
  <script type="text/javascript">
	var token = "<?php echo $token; ?>";
  </script>	
</div>
<?php echo $footer; ?>