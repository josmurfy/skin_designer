<fieldset>
<legend><?php echo $tab_courier; ?> #<?php echo $order_id; ?></legend>
<form class="form-horizontal" id="form-shipment">
<div class="form-horizontal">
  <div class="form-group">
    <label class="col-sm-2 control-label"><?php echo $label_courier_product; ?></label>
    <div class="col-sm-10">
		<div class="well well-sm" style="height: 200px; overflow: auto;">
			<table class="table table-bordered table-hover">
			<thead>
			<tr>
				<td>
				<div class="checkbox">
				  <label><input type="checkbox" onclick="$('input[name*=\'courier_select_product\']').prop('checked', this.checked);" />&nbsp;Select All</label>
				</div>
				</td>
				<td><b>Quantity</b></td>
	
			</tr>
			</thead>
			<tbody>
			<?php foreach ($order_products as $product) { ?>
				<tr>
				<td>
				<div class="checkbox">
				  <label><input type="checkbox" name="courier_select_product[]" id="cbx<?php echo $product['order_product_id']; ?>"  value="<?php echo $product['order_product_id']; ?>" />&nbsp;<?php echo $product['name']; ?>&nbsp;-&nbsp;<?php echo $product['model']; ?>   
				   <?php foreach ($product['option'] as $option) { ?>
					<?php if ($option['type'] != 'file') { ?>
					&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
					<?php } else { ?>
					&nbsp;<small> - <?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></small>
					<?php } ?>
                	<?php } ?>
				  </label>
				</div>
				</td>
				<td> 
				<select name="qty[<?php echo $product['order_product_id']; ?>]" onChange="auto_select_checkbox(<?php echo $product['order_product_id']; ?>, this.value);" class="form-control">
					<?php for ($i = 0; $i <= $product['remaining_qty']; $i++) { ?>
						<option><?php echo $i; ?></option>
					<?php } ?>
				</select>
				</td>
				</tr>
			<?php } ?>
			</tbody>
			</table>
		</div>
				  
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label"><?php echo $label_courier; ?></label>
    <div class="col-sm-10">
      <div id="courier_dd">
        <select name="courier_select" id="courier_select" class="form-control">
          <option value="0"><?php echo $select_courier; ?></option>
          <?php foreach ($partners as $partner){ ?>
          	<option value="<?php echo $partner['id']; ?>" <?php echo ($shipment_partner_id == $partner['id'])?'selected="selected"':'' ?> ><?php echo $partner['name']; ?></option>						
          <?php } ?>
        </select>
      </div>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label"><?php echo $label_courier_track; ?></label>
    <div class="col-sm-10">
      <div id="courier_track">
        <input class="form-control" name="courier_tracking_id" id="courier_tracking_id" value="">
      </div>
    </div>
  </div>
  
  <div class="form-group">
    <label class="col-sm-2 control-label">Shipment Order Status</label>
	 <div class="col-sm-10">
		<select class="form-control" name="shipped_order_status" id="shipped_order_status">
			<option value="0">Do not add history</option>
			<?php foreach ($order_statuses as $order_status) { ?>
			<?php if (in_array($order_status['order_status_id'], $hb_shipment_eligible_status)) { ?>
				<option value="<?php echo $order_status['order_status_id']; ?>" <?php echo ($order_status['order_status_id'] == $hb_shipment_shipped_status)? 'selected="selected"': ''; ?> ><?php echo $order_status['name']; ?></option>
				<?php } ?>
			<?php } ?>
		</select>
	</div>
	</div>
  
  <div class="form-group">
		<label class="control-label col-sm-2"><?php echo $label_courier_date; ?></label>
		<div class="col-sm-3">
			<div class="input-group date">
				<input type="text" id="delivery_date" name="delivery_date" data-date-format="YYYY-MM-DD" value="" class="form-control" />
				<span class="input-group-btn">
				<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
				</span>
			</div>
		</div>
	</div>
	
	<div class="form-group">
		<label class="control-label col-sm-2"></label>
		<div class="col-sm-10">
			<a id="hbs_button_save" onClick="save_send(0);" class="btn btn-primary"><i class="fa fa-save"></i>&nbsp;<?php echo $button_save; ?></a>
			<?php if ($hb_shipment_quick_send) { ?>
			<a id="hbs_button_save_send" onClick="save_send(1);" class="btn btn-warning"><i class="fa fa-save"></i>&nbsp;SAVE & SEND EMAIL</a>
			<?php } ?>
      		<div id="msgsavecourier"></div>
		</div>
	</div>
</div>
</form>
<!-- horizonatal form class -->
<br>
<div id="shipment-info-table"></div>
<div id="sit-output-console"></div>
</fieldset>

<style type="text/css">
.pr_error,.pr_info,.pr_infos,.pr_success,.pr_warning{margin:10px 0;}.pr_info{color:#00529B;background-color:#BDE5F8}.pr_success{color:#4F8A10;background-color:#DFF2BF}.pr_warning{color:#9F6000;background-color:#FEEFB3}.pr_error{color:#D8000C;background-color:#FFBABA}.pr_error i,.pr_info i,.pr_success i,.pr_warning i{margin:2px 0;vertical-align:middle}
</style>
				
<script type="text/javascript">
loadShipmentInfoTable();

function loadShipmentInfoTable(){
	$('#shipment-info-table').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#shipment-info-table').load('index.php?route=<?php echo $base_route; ?>/order_shipment/order_shipment_info_table&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&store_id=<?php echo $store_id; ?>');
}
				
$('.date').datetimepicker({
	pickTime: false
});

function auto_select_checkbox(id, value) {
	if (value > 0) {
		$('#cbx'+id).prop('checked', true);
	}else{
		$('#cbx'+id).prop('checked', false);
	}
}
function save_send(mail_flag){
	var id     			=  $('#courier_select').val();
	var tracking_id   	=  $('#courier_tracking_id').val();
	var delivery_date   =  $('#delivery_date').val();

	var product_array = $('input[name="courier_select_product[]"]:checked').map(function(){return this.value;}).get();

	if (product_array.length == 0){
		alert ('Please Select Products!');
		return false;
	}
	if (id == '0'){
		alert ('Please Select a Courier Service!');
		return false;
	}
	
	$.ajax({
	  url: 'index.php?route=<?php echo $base_route; ?>/order_shipment/savecourier&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&store_id=<?php echo $store_id; ?>&mail='+mail_flag,
	  dataType: 'json',
	  type: 'post',
	  data: $('#form-shipment').serialize(),
	  success: function(json) {
		if (json['success']) {
			  loadShipmentInfoTable();
			  $('#msgsavecourier').html('<div class="alert pr_success"><i class="fa fa-check"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			  $('#history').load('index.php?route=sale/order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>');
			  $('#shipment-form').load('index.php?route=<?php echo $base_route; ?>/order_shipment/shipment_form&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&store_id=<?php echo $store_id; ?>');
		}
	  }
	});
}

</script>
