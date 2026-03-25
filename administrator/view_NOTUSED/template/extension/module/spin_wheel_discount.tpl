<!--/***********************
// @category  : OpenCart
// @module    : Spin Wheel Popup
// @author    : OpencartMarketplace <support@opencartmarketplace.com>
***********************-->	
<form action="#" mathod="POST" class="form-horizontal" id="spin_wheel_discount">
	<input type="hidden" name="offer_id" value="<?php echo $offer_id; ?>">
	<div class="form-group required spin-label">
		<label class="col-sm-2 control-label" for="input-label"><?php echo $entry_label; ?></label>
		<div class="col-sm-6">
			<input type="text" name="label" value="<?php echo $label;?>" class="form-control" />
		</div>	
	</div>
	<div class="form-group required ocmp_type">
		<label class="col-sm-2 control-label" for="input-label"><?php echo $entry_type; ?></label>
		<div class="col-sm-6">
			<select name="type" class="form-control">
				<option value="1" <?php echo ($type == 1) ? 'selected="selected"' : ''; ?>><?php echo $text_fixed; ?></option>
				<option value="2" <?php echo ($type == 2) ? 'selected="selected"' : ''; ?>><?php echo $text_precentage; ?></option>
				<option value="3" <?php echo ($type == 3) ? 'selected="selected"' : ''; ?>><?php echo $text_shipping; ?></option>
			</select>	
		</div>	
	</div>
	<div class="form-group required ocmp_discount" style="display:<?php echo ($type == 3) ? 'none;' : 'block;'; ?>">
		<label class="col-sm-2 control-label" for="input-label"><?php echo $entry_discount; ?></label>
		<div class="col-sm-6">
			<input type="text" name="discount" value="<?php echo $discount;?>" class="form-control" />
		</div>	
	</div>
	<div class="form-group">
		<label class="col-sm-2 control-label" for="input-label"><span data-toggle="tooltip" data-original-title="The total amount that must be reached before the coupon is valid."><?php echo $entry_total; ?></span></label>
		<div class="col-sm-6">
			<input type="text" name="total" value="<?php echo $total;?>" class="form-control" />
		</div>	
	</div>	
	<div class="form-group">
		<label class="col-sm-2 control-label" for="input-label"><span data-toggle="tooltip" data-original-title="0 is not change,1 more higher chance."><?php echo $entry_gravity; ?></span></label>
		<div class="col-sm-6">
			<input type="text" name="gravity" value="<?php echo $gravity;?>" class="form-control" />
		</div>	
	</div>	
</form>	