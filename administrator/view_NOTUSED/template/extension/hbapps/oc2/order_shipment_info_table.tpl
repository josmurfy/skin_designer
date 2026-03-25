<div class="table-responsive">
<table class="table table-bordered table-hover">
	<thead>
	<tr>
		<td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'email_selected_product\']').prop('checked', this.checked);" /></td>
	  <td class="text-left">Product</td>
	  <td class="text-left">Model</td>
	  <td class="text-center">Shipped Qty</td>
	  <td class="text-left">Shipping Partner</td>
	  <td class="text-left">Tracking ID / Number</td>
	  <td class="text-left">Tracking Link</td>
	  <td class="text-left">Delivery Date</td>
      <td class="text-left">Action</td>
	</tr>
  </thead>
  <tbody>
	<?php if ($order_shipment_data) { ?>
	<?php foreach ($order_shipment_data as $shipment) { ?>
	<tr>
		<td style="text-align: center;"><input type="checkbox" name="email_selected_product[]" value="<?php echo $shipment['shipment_id']; ?>" /></td>
	  <td class="text-left"><?php echo $shipment['product_name']; ?>
	  				<?php foreach ($shipment['option'] as $option) { ?>
					<br />
					<?php if ($option['type'] != 'file') { ?>
					&nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
					<?php } else { ?>
					&nbsp;<small> - <?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></small>
					<?php } ?>
                	<?php } ?></td>
	  <td class="text-left"><?php echo $shipment['model']; ?></td> 				
	  <td class="text-center"><?php echo $shipment['shipped_qty']; ?></td>
	  <td class="text-left"><?php echo $shipment['partner_name']; ?></td>
	  <td class="text-left"><?php echo $shipment['code']; ?></td>
	  <td class="text-left"><a href="<?php echo $shipment['tracking_link']; ?>" target="_blank"><?php echo $shipment['tracking_link']; ?></a></td>
	  <td class="text-left"><?php echo $shipment['delivery_date']; ?></td>
	  <td class="text-left">
	  	<a class="btn btn-sm btn-default" onclick="editItem(<?php echo $shipment['shipment_id']; ?>);">Edit</a> 
		<a class="btn btn-sm btn-danger" onclick="deleteItem(<?php echo $shipment['shipment_id']; ?>);">Delete</a> 
		<?php if ($shipment['mail'] == 0) { ?> 
			<a class="btn btn-sm btn-primary" onclick="emailPreview(<?php echo $order_id; ?>,<?php echo $shipment['shipment_id']; ?>);"><i class="fa fa-envelope"></i> Send Email</a>
		<?php } else { ?>
			<a class="btn btn-sm btn-success" onclick="emailPreview(<?php echo $order_id; ?>,<?php echo $shipment['shipment_id']; ?>);"><i class="fa fa-check"></i> Send Email Again?</a>
		<?php } ?>
	  </td>
    </tr>
	<?php } ?>

	<tr>
	  <td class="text-center" colspan="9"><a onclick="emailPreview(<?php echo $order_id; ?>, 0);" class="btn pr_info"><i class="fa fa-envelope"></i> SEND SHIPMENT EMAIL TO CUSTOMER</a></td>
	</tr>

	 <?php } else { ?>
	<tr>
	  <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
	</tr>
	<?php } ?>
  </tbody>
</table>
</div>

<div id="val-msg"></div>

<!--MODAL EDIT ROW-->
<div class="modal fade" id="edit-item" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-md" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Edit</h4>
	  </div>
	  <div class="modal-body" id="editform">

	  </div>
	  <div id="edit-result" style="text-align:center;"></div>
	</div>
  </div>
</div>
<!--END - MODAL EDIT ROW-->

<!--MODAL EDIT ROW-->
<div class="modal fade" id="sit-mail" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
	  <div class="modal-header" style="background-color:#c2dde6;">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title"><span class="mail-action-button"></span>
			<div class="col-sm-6">Email Preview </div>
			<div class="col-sm-4">

			</div>
		</h4>
		
	  </div>
	  <div class="modal-body" id="sit-mail-preview">

	  </div>
	  <div class="modal-footer">
	 	 <div class="mail-action-button"></div>
		 <div id="sit-mail-result" style="text-align:center;"></div>
	  </div>
	</div>
  </div>
</div>
<!--END - MODAL EDIT ROW-->


<script type="text/javascript">
//This event is fired after the modal is closed
$("#sit-mail").on('hidden.bs.modal', function(){
  loadShipmentInfoTable();
});

function editItem(id){
	$('#edit-result').html('');
	$('#editform').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#editform').load('index.php?route=<?php echo $base_route; ?>/order_shipment/editform&token=<?php echo $token; ?>&id='+id);
	$('#edit-item').modal('show');
}

function deleteItem(id){
	$('#sit-output-console').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$.ajax({
		type: 'post',
		url: 'index.php?route=<?php echo $base_route; ?>/order_shipment/deleteitem&token=<?php echo $token; ?>',
		data: {id : id},
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				  $('#sit-output-console').html('<div class="alert pr_success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				  loadShipmentInfoTable();
			}
			if (json['error']) {
				  $('#sit-output-console').html('<div class="alert pr_error"><i class="fa fa-check"></i> '+json['error']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
		},			
		error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}

function emailPreview(order_id, shipment_id){
	$('#val-msg').html('');
	$('#sit-mail-result').html('');
	$('#sit-mail-preview').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	
	var product_array = $('input[name="email_selected_product[]"]:checked').map(function(){return this.value;}).get();
	if (shipment_id == 0 && product_array.length > 0) {
		//console.log(product_array.toString());
		shipment_id = product_array.toString();
	}	
	
	$.ajax({
		url: 'index.php?route=<?php echo $base_route; ?>/order_shipment/validate_products&token=<?php echo $token; ?>&order_id='+order_id+'&shipment_id='+shipment_id,
		dataType: 'json',
		success: function(json) {
			if (json['error']) {
				 $('#val-msg').html('<div class="alert alert-danger"><i class="fa fa-times"></i> '+json['error']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}else{
				<?php if ($hb_shipment_preview_enable) { ?>
					$('#sit-mail-preview').load('index.php?route=<?php echo $base_route; ?>/order_shipment/notify_customer&token=<?php echo $token; ?>&preview=true&order_id='+order_id+'&shipment_id='+shipment_id);
					$('.mail-action-button').html('<a onclick="sendShipmentEmail(' + order_id + ',\''+shipment_id+'\');" class="btn btn-sm btn-success">Confirm & Send</a>');
					$('#sit-mail').modal('show');
				<?php } else { ?>
					sendShipmentEmail(order_id, shipment_id);
				<?php } ?>
			}
		},			
		error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });

}

function sendShipmentEmail(order_id, shipment_id){
	var shipped_order_status = $('#shipped_order_status').val();
	<?php if ($hb_shipment_preview_enable) { ?>
		$('#sit-mail-result').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	<?php } else { ?>
		$('#sit-output-console').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	<?php } ?>
	$.ajax({
		url: 'index.php?route=<?php echo $base_route; ?>/order_shipment/notify_customer&token=<?php echo $token; ?>&order_id='+order_id+'&shipment_id='+shipment_id+'&shipped_order_status='+shipped_order_status,
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				<?php if ($hb_shipment_preview_enable) { ?>
				  $('#sit-mail-result').html('<div class="alert pr_success"><i class="fa fa-check"></i> '+json['success']+'</div>');
				  $('.mail-action-button').html('<a class="btn btn-sm btn-success"><i class="fa fa-check"></i> Email Sent</a>');
				<?php } else { ?>
					$('#sit-output-console').html('<div class="alert pr_success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div></div>');
					loadShipmentInfoTable();
				<?php } ?>
				 $('#history').load('index.php?route=sale/order/history&token=<?php echo $token; ?>&order_id='+order_id); 
			}
		},			
		error: function(xhr, ajaxOptions, thrownError) { alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}
</script>