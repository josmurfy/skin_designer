<form class="form-horizontal" id="form-edit">
<div class="form-group">
	<label class="control-label col-sm-3">Product</label>
	<div class="col-sm-9">
		<input type="text" disabled="disabled" value="<?php echo $product_name; ?>" class="form-control" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" class="form-control" />
	</div>
</div>

<div class="form-group">
	<label class="control-label col-sm-3">Shipped Qty</label>
	<div class="col-sm-9">
		<input type="number" name="shipped_qty" value="<?php echo $shipped_qty; ?>" class="form-control" />
	</div>
</div>

<div class="form-group">
	<label class="control-label col-sm-3">Shipping Partner</label>
	<div class="col-sm-9">
		<select class="form-control" name="courier_id">
			<?php foreach ($partners as $partner) { ?>
			<option value="<?php echo $partner['id']; ?>" <?php echo ($partner['id'] == $courier_id)? 'selected':''; ?> ><?php echo $partner['name']; ?></option>
			<?php } ?>
		</select>
	</div>
</div>

<div class="form-group emu">
	<label class="control-label col-sm-3">Shipment Tracking ID / Number</label>
	<div class="col-sm-9">
		<input type="text" name="tracking_id" value="<?php echo $tracking_id; ?>" class="form-control" />
	</div>
</div>

<div class="form-group emu">
	<label class="control-label col-sm-3">Expected Delivery Date</label>
	<div class="col-sm-9">
		<div class="input-group date">
			<input type="text" name="delivery_date" data-date-format="YYYY-MM-DD" value="<?php echo $delivery_date; ?>" class="form-control" />
			<span class="input-group-btn">
			<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
			</span>
		</div>
	</div>
</div>



<div class="form-group">
	<label class="control-label col-sm-3"></label>
	<div class="col-sm-9">
		<a class="btn btn-warning" id="edit-menu">UPDATE</a>
	</div>
</div>
</form>

<script type="text/javascript">
//This event is fired after the modal is closed
$("#edit-item").on('hidden.bs.modal', function(){
  loadShipmentInfoTable();
});

$('.date').datetimepicker({
	pickTime: false
});

$('#edit-menu').on('click', function() {
	$('#edit-result').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$.post('index.php?route=<?php echo $base_route; ?>/order_shipment/updateitem&token=<?php echo $token; ?>', $('#form-edit').serialize(),function(json) {
	  if (json['success']) {
			$('#edit-result').html('<div class="alert pr_success"><i class="fa fa-check"></i> '+json['success']+'</div>');
		}
		if (json['error']) {
			$('#edit-result').html('<div class="alert pr_warning"><i class="fa fa-exclamation"></i> '+json['error']+'</div>');
		}
	},'json');
}); 
</script>