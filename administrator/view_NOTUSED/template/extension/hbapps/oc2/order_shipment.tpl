<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <!--Header Start-->
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">	
	  	<a type="submit" onClick="document.getElementById('form-shipment').submit();" form="form-latest" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"> Save</i></a>  	
	  	<button type="button" class="btn btn-success" data-toggle="modal" data-target="#add-partner"><i class="fa fa-plus" aria-hidden="true"></i> Add Partner</button>
		<a class="btn btn-danger" id="delete-partner-button"><i class="fa fa-trash"></i> Delete Partner</a>
		<a href="https://www.huntbee.com/documentation/docs/order-shipment-tracking-info/" title="Documentation" class="btn btn-default" target="_blank"><i class="fa fa-book"></i> Docs</a>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a> </div>
      <h1><?php echo $heading_title_shipment; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <!--Header End-->
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title_shipment; ?></h3>
			<?php if ($stores) { ?>
			<div class="pull-right">
			<select id="store">
				<option value="0" <?php echo ($store_id == 0)?'selected':''; ?>>Default Store</option>
				<?php foreach ($stores as $store) { ?>
					<option value="<?php echo $store['store_id']; ?>" <?php echo ($store_id == $store['store_id'])?'selected':''; ?>><?php echo $store['name']; ?></option>
				<?php } ?>
			</select>
			</div>
			<?php } ?>
      </div>
      <div class="panel-body">
	  	<div id="output-console"></div>
				
        <form  action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-shipment" class="form-horizontal">
			<!--Tabs UL Starts-->
			<ul class="nav nav-tabs" id="tabs">
				<li class="active"><a href="#tab-list" data-toggle="tab"><i class="fa fa-truck"></i> Shipment Service Partners</a></li>
				<li><a href="#tab-orders" data-toggle="tab"><i class="fa fa-database"></i> Order Shipment Information</a></li>
				<li><a href="#tab-email" data-toggle="tab"><i class="fa fa-envelope"></i> Email template</a></li>
				<li><a href="#tab-sms" data-toggle="tab"><i class="fa fa-mobile"></i> SMS</a></li>
				<li><a href="#tab-settings" data-toggle="tab"><i class="fa fa-gears"></i> Settings</a></li>
			 </ul>
			 <!--Tabs UL Ends-->
			 
			 <div class="tab-content"><!--UL TAB MAIN CONTAINER-->
				<!--LIST CONTAINER-->
				<div class="tab-pane active" id="tab-list">
					<div id="list-block"></div>					
				</div>
				<!--ORDERS-->
				<div class="tab-pane" id="tab-orders">
					<div class="input-group" style="margin-bottom:10px;">
						<input type="text" id="search-order-value" onkeyup="searchOrder();" class="form-control" placeholder="Search by order ID or tracking code">
						<span class="input-group-addon btn" id="search-button" onclick="searchOrder();"><i class="fa fa-search"></i></span>
					</div>
					<div id="orders-block"></div>
				</div>
				
				<div class="tab-pane" id="tab-email">
					<div class="form-group">
						<label class="control-label col-sm-2">Select email template</label>
						<div class="col-sm-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-file" aria-hidden="true"></i></span>
								<select name="hb_shipment_template" id="hb_shipment_template" class="form-control">
									<?php foreach ($template_lists as $list) { ?>
										<option value="<?php echo $list['value']; ?>" <?php echo ($hb_shipment_template == $list['value'])? 'selected="selected"':''; ?>><?php echo $list['label']; ?></option>
									<?php } ?>
								</select>
								<a class="btn btn-primary input-group-addon" id="load-template-sample-btn" onclick="loadSampleBlock($('#hb_shipment_template').val());"><i class="fa fa-refresh"></i>  Load/Reset Sample</a>
							</div>		
						</div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-2 control-label">Product Image Dimension</label>
						<div class="col-sm-3">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-arrows-h" aria-hidden="true"></i> Width</span>
								<input type="text" name="hb_shipment_img_w" value="<?php echo $hb_shipment_img_w; ?>" class="form-control">
								<span class="input-group-addon"> PX </span>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-arrows-v" aria-hidden="true"></i> Height</span>
								<input type="text" name="hb_shipment_img_h" value="<?php echo $hb_shipment_img_h; ?>" class="form-control">
								<span class="input-group-addon"> PX </span>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-2">Sender Email Address</label>
						
						<div class="col-sm-4">
							<input type="text" name="hb_shipment_admin_email" value="<?php echo $hb_shipment_admin_email; ?>" class="form-control">	
						</div>
						
						<label class="control-label col-sm-2">Sender Name</label>
						
						<div class="col-sm-4">
							<input type="text" name="hb_shipment_sender" value="<?php echo $hb_shipment_sender; ?>" class="form-control">	
						</div>
					</div>
							
					<ul class="nav nav-tabs" id="template_languages">
						<?php foreach ($languages as $language) { ?>
						<li><a href="#template-language<?php echo $language['language_id']; ?>" data-toggle="tab"><?php echo $language['name']; ?></a></li>
						<?php } ?>
					</ul>
					<div class="tab-content">
					<?php foreach ($languages as $language) { ?>
						<div class="tab-pane" id="template-language<?php echo $language['language_id']; ?>">
							
							<div class="form-group">
								<label class="control-label col-sm-2">Email Subject [Single Item]</label>
								<div class="col-sm-10">
									<input type="text" name="hb_shipment_subject_single<?php echo $language['language_id']; ?>" value="<?php echo $hb_shipment_subject_single[$language['language_id']]; ?>" class="form-control">	
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-2">Email Subject [Multiple Items]</label>
								<div class="col-sm-10">
									<input type="text" name="hb_shipment_subject_multiple<?php echo $language['language_id']; ?>" value="<?php echo $hb_shipment_subject_multiple[$language['language_id']]; ?>" class="form-control">	
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-2">Block 1</label>
								<div class="col-sm-10">
									<textarea id= "block1<?php echo $language['language_id']; ?>" name="hb_shipment_tblock_1<?php echo $language['language_id']; ?>" class="form-control summernote"><?php echo $hb_shipment_tblock_1[$language['language_id']]; ?></textarea>	
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-2">Block 2</label>
								<div class="col-sm-10">
									<textarea id= "block2<?php echo $language['language_id']; ?>" name="hb_shipment_tblock_2<?php echo $language['language_id']; ?>" class="form-control summernote"><?php echo $hb_shipment_tblock_2[$language['language_id']]; ?></textarea>	
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-2">Block 3</label>
								<div class="col-sm-10">
									<textarea id= "block3<?php echo $language['language_id']; ?>" name="hb_shipment_tblock_3<?php echo $language['language_id']; ?>" class="form-control summernote"><?php echo $hb_shipment_tblock_3[$language['language_id']]; ?></textarea>	
								</div>
							</div>
							
							<div class="form-group">
								<label class="control-label col-sm-2">Block 4</label>
								<div class="col-sm-10">
									<textarea id= "block4<?php echo $language['language_id']; ?>" name="hb_shipment_tblock_4<?php echo $language['language_id']; ?>" class="form-control summernote"><?php echo $hb_shipment_tblock_4[$language['language_id']]; ?></textarea>	
								</div>
							</div>
							
						</div>
					<?php } ?>
					</div>			
					
				</div>
				
				<!-- SMS -->
				<div class="tab-pane" id="tab-sms">
					<?php foreach ($languages as $language) { ?>
				   	<div class="form-group">
						<label class="col-sm-4 control-label">SMS Template <img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></label>
						<div class="col-sm-8">
							<textarea name="hb_shipment_sms<?php echo $language['language_id']; ?>" class="form-control"><?php echo $hb_shipment_sms[$language['language_id']]; ?></textarea>
						</div>
					</div>
					<?php } ?>
					
					<?php if ($check_opencart_sms_installation) { ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Send SMS</label>
						<div class="col-sm-8">
							<input type="checkbox" data-on="Yes" data-off="No" data-toggle="toggle" data-onstyle="success" data-offstyle="default" name="hb_shipment_send_sms" class="form-control" value="1" <?php echo ($hb_shipment_send_sms == 1)? 'checked':''; ?> />
						</div>
				   </div>
				   <?php } else { ?>
				   	<div class="form-group">
						<div class="col-sm-12">
							<div class="alert pr_warning"><center>To use the SMS feature, you need the extension "<a href="https://www.huntbee.com/opencart-sms" target="blank">OpenCart SMS System</a>" installed.</center></div>
						</div>
				   	</div>
				   <?php } ?>
				   
				</div>
				
				<!--SETTINGS-->
				<div class="tab-pane" id="tab-settings">
					<div class="form-group">
						<label class="col-sm-4 control-label">Shipment Form Admin Template</label>
						<div class="col-sm-8">
							<select name="hb_shipment_form_template" class="form-control">
							<?php foreach ($shipment_form_templates as $template) { ?>
								<option value="<?php echo $template; ?>" <?php echo ($hb_shipment_form_template ==  $template)? 'selected':''; ?>><?php echo $template; ?></option>
							<?php } ?>
							</select>
						</div>
				   </div>
				   
					 <div class="form-group">
					  <label class="col-sm-4 control-label">Eligible order status for order shipments</label>
					  <div class="col-sm-8">
						<div class="well well-sm" style="height: 150px; overflow: auto;">
						  <?php foreach ($order_statuses as $order_status) { ?>
						  <div class="checkbox">
							<label>
							  <?php if (in_array($order_status['order_status_id'], $hb_shipment_eligible_status)) { ?>
							  <input type="checkbox" name="hb_shipment_eligible_status[]" value="<?php echo $order_status['order_status_id']; ?>" checked="checked" />
							  <?php echo $order_status['name']; ?>
							  <?php } else { ?>
							  <input type="checkbox" name="hb_shipment_eligible_status[]" value="<?php echo $order_status['order_status_id']; ?>" />
							  <?php echo $order_status['name']; ?>
							  <?php } ?>
							</label>
						  </div>
						  <?php } ?>
						</div>
					  </div>
					</div>
					
					<div class="form-group">
						<label class="col-sm-4 control-label">Shipped Order Status</label>
						<div class="col-sm-8">
							<select name="hb_shipment_shipped_status" class="form-control">
							<?php foreach ($order_statuses as $order_status) { ?>
								<option value="<?php echo $order_status['order_status_id']; ?>" <?php echo ($hb_shipment_shipped_status ==  $order_status['order_status_id'])? 'selected':''; ?>><?php echo $order_status['name']; ?></option>
							<?php } ?>
							</select>
						</div>
				   </div>
				   
				   <div class="form-group">
						<label class="col-sm-4 control-label">Shipped History Comment Template</label>
						<div class="col-sm-8">
							<div class="row">
							<?php foreach ($languages as $language) { ?>
								<div class="col-sm-12">
									<div class="input-group" style="margin-bottom:5px;">
										<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" /></span>
										<textarea name="hb_shipment_shipped_comment<?php echo $language['language_id']; ?>" class="form-control"><?php echo $hb_shipment_shipped_comment[$language['language_id']]; ?></textarea>
									</div>
								</div>
							<?php } ?>
							</div>
						</div>
				   </div>		
				   
				   <div class="form-group">
						<label class="col-sm-4 control-label">Enable Preview Mail</label>
						<div class="col-sm-8">
							<input type="checkbox" data-on="Yes" data-off="No" data-toggle="toggle" data-onstyle="success" data-offstyle="default" name="hb_shipment_preview_enable" class="form-control" value="1" <?php echo ($hb_shipment_preview_enable == 1)? 'checked':''; ?> />
						</div>
				   </div>
				   
				   <div class="form-group">
						<label class="col-sm-4 control-label">Enable Quick Save & Send Button</label>
						<div class="col-sm-8">
							<input type="checkbox" data-on="Yes" data-off="No" data-toggle="toggle" data-onstyle="success" data-offstyle="default" name="hb_shipment_quick_send" class="form-control" value="1" <?php echo ($hb_shipment_quick_send == 1)? 'checked':''; ?> />
						</div>
				   </div>
				   			
				</div>
				
			</div><!--END UL TAB MAIN CONTAINER-->
        </form>		
		<!--MODAL CREATE TEMPLATE-->
		<div class="modal fade" id="add-partner" tabindex="-1" role="dialog">
		  <div class="modal-dialog modal-md" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Add Shipping Partner</h4>
			  </div>
			  <div class="modal-body">
				  <div class="form-group">
					<label class="control-label">Enter Shipping Partner Name</label>
					<input type="text" class="form-control" id="partner-name">
					<div class="pr_info">Example: FEDEX</div>
				  </div>
				  <div class="form-group">
					<label class="control-label">Tracking URL</label>
					<input type="text" class="form-control" id="tracking-url">
					<div class="pr_info">Use shortcode {tracking_id} in place where tracking number or consignment number will be placed in the link</div>
					<input type="hidden" class="form-control" id="partner-id">
				  </div>
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="add-partner-button" onclick="addPartner();">ADD</button>
				<button type="button" class="btn btn-warning" id="edit-partner-button" onclick="editPartner();">UPDATE</button>
			  </div>
			  <div id="addpartner-result" style="text-align:center;"></div>
			</div>
		  </div>
		</div>
		<!--END - MODAL CREATE TEMPLATE-->
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <!--Huntbee copyrights-->
    <center>
      <span class="help"><?php echo $heading_title_shipment; ?> - <?php echo $extension_version; ?> &copy; <a href="https://www.huntbee.com/">WWW.HUNTBEE.COM</a> | <a href="https://www.huntbee.com/documentation/docs/order-shipment-tracking-info/" target="_blank">DOCUMENTATION</a> | <a href="https://www.huntbee.com/get-support" target="_blank">SUPPORT</a></span>
    </center>
  </div>
  <!--Huntbee copyrights end-->
</div>

<style type="text/css">
.pr_error,.pr_info,.pr_infos,.pr_success,.pr_warning{margin:10px 0;padding:12px}.pr_info{color:#00529B;background-color:#BDE5F8}.pr_success{color:#4F8A10;background-color:#DFF2BF}.pr_warning{color:#9F6000;background-color:#FEEFB3}.pr_error{color:#D8000C;background-color:#FFBABA}.pr_error i,.pr_info i,.pr_success i,.pr_warning i{margin:10px 0;vertical-align:middle}
</style>

<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>

<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
  
<script type="text/javascript"><!--
$('#template_languages a:first').tab('show');
//--></script>
  
<script type="text/javascript">
$(document).ready(function() {
	loadreports();
	$('#edit-partner-button').hide();
});
</script>
<script type="text/javascript">
function loadreports(){
	$('#list-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#list-block').load('index.php?route=<?php echo $base_route; ?>/order_shipment/partner_list&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
	$('#orders-block').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	$('#orders-block').load('index.php?route=<?php echo $base_route; ?>/order_shipment/order_list&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>');
}

$('#list-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();
	$('#list-block').load(this.href);
});

$('#orders-block').delegate('.pagination a', 'click', function(e) {
	e.preventDefault();
	$('#orders-block').load(this.href);
});
</script>

<script type="text/javascript">
function addPartner(){
	$('#addpartner-result').html('<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>');
	$.ajax({
		type: 'post',
		url: 'index.php?route=<?php echo $base_route; ?>/order_shipment/addpartner&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		data: {name : $('#partner-name').val(), tracking_url : $('#tracking-url').val()},
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				  $('#addpartner-result').html('<div class="alert pr_success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				  loadreports();
				  $('#add-partner').modal('hide');
			}
			if (json['warning']) {
				  $('#addpartner-result').html('<div class="alert pr_error"><i class="fa fa-exclamation"></i> '+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
		},			
		error: function(xhr, ajaxOptions, thrownError) { $('#addpartner-result').html(''); alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}

function editPartner(){
	$('#addpartner-result').html('<i class="fa fa-refresh fa-spin fa-3x fa-fw"></i>');
	$.ajax({
		type: 'post',
		url: 'index.php?route=<?php echo $base_route; ?>/order_shipment/editpartner&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		data: {name : $('#partner-name').val(), tracking_url : $('#tracking-url').val(), id : $('#partner-id').val()},
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				  $('#addpartner-result').html('<div class="alert pr_success"><i class="fa fa-check"></i> '+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				  loadreports();
				  $('#edit-partner-button').hide();
				  $('#add-partner-button').show();
				  $('#partner-name').val('');
				  $('#tracking-url').val('');
				  $('#partner-id').val('');
				  $('#addpartner-result').html('');
				  $('#add-partner').modal('hide');
			}
			if (json['warning']) {
				  $('#addpartner-result').html('<div class="alert pr_error"><i class="fa fa-exclamation"></i> '+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
		},			
		error: function(xhr, ajaxOptions, thrownError) { $('#addpartner-result').html(''); alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });
}

$('#delete-partner-button').on('click', function() {
	$('#output-console').html('<center><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></center>');
	var arraydata = $('input[name="selected[]"]:checked').map(function(){
        return this.value;
    }).get()
	
	$.ajax({
		type: 'post',
		url: 'index.php?route=<?php echo $base_route; ?>/order_shipment/deletepartner&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>',
		data: {selected : arraydata},
		dataType: 'json',
		success: function(json) {
			if (json['success']) {
				  $('#output-console').html('<div class="alert pr_success">'+json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				  loadreports();
			}
			if (json['warning']) {
				  $('#output-console').html('<div class="alert pr_error">'+json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
		},			
		error: function(xhr, ajaxOptions, thrownError) { $('#output-console').html(''); alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText); }
	 });	
});

function searchOrder() {
	var searchvalue = $('#search-order-value').val();
	$('#orders-block').load('index.php?route=<?php echo $base_route; ?>/order_shipment/order_list&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&search='+searchvalue);
};

function openEditModal(name, url, id) {
	$('#partner-name').val(name);
	$('#tracking-url').val(url);
	$('#partner-id').val(id);
	$('#add-partner-button').hide();
	$('#edit-partner-button').show();
	$('#add-partner').modal('show');
}

function loadSampleBlock(template){	
	$('#load-template-sample-btn').html('<center><i class="fa fa-refresh fa-spin fa-fw"></i></center>');
	$.ajax({
		url: 'index.php?route=<?php echo $base_route; ?>/order_shipment/loadSampleBlock&token=<?php echo $token; ?>&store_id=<?php echo $store_id; ?>&template='+template,
		dataType: 'json',
		success: function(json) {
			<?php foreach ($languages as $language) { ?>
			if (json['block1']) {
				  $('#block1<?php echo $language['language_id']; ?>').summernote('code',json['block1']);
			}
			if (json['block2']) {
				  $('#block2<?php echo $language['language_id']; ?>').summernote('code',json['block2']);
			}
			if (json['block3']) {
				  $('#block3<?php echo $language['language_id']; ?>').summernote('code',json['block3']);
			}
			if (json['block4']) {
				  $('#block4<?php echo $language['language_id']; ?>').summernote('code',json['block4']);
			}
			<?php } ?>
			$('#load-template-sample-btn').html('<i class="fa fa-refresh"></i>  Load/Reset Sample');
		}		
	 });
}
</script>
<script type="text/javascript">
$('#store').on('change', function() {
	window.location.href = 'index.php?route=<?php echo $base_route; ?>/order_shipment&token=<?php echo $token; ?>&store_id='+$('#store').val();
});
</script>
<?php echo $footer; ?>