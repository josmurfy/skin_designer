<?php 
if(!empty($warning)) {
?>
<div id="content" class="col-sm-12 bg alert text-danger">
	<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $warning?><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
</div>
<?php } else { ?>

<div id="content" class="col-sm-12 bg paynowfinal">
 <div class="modal-header">
	<h3><?php echo $text_place; ?></h3>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="col-sm-12 form-horizontal">
	<div class="order-nowdata">	   
		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo $text_customer; ?></label>
			<div class="col-sm-8">
			  	<div class="checkbox">
					<label class="guescust">					
						<?php if ($default_guest) { ?>
	                      <input type="checkbox" name="guestcustomer" value="1" checked="checked" id="input-guestcustomer" />
	                      <?php } else { ?>
	                      <input type="checkbox" name="guestcustomer" value="1" id="input-top" />
	                      <?php } ?>					  
							<?php echo $text_gcustomer; ?>
						<br/><br/>
						<?php echo $text_or; ?>				
					</label>
					<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" class="form-control customer_id"/>
					<input type="text" name="customer_name" value="<?php echo $customer_id; ?>" placeholder="Type and Select Customer" class="form-control customer_name"/>
					<br/>
					<a class="paynowcustomeradd" data-toggle="tooltip" title="Add Customer" onClick="$('.loadcustomerfrom').show();" ><i class="fa fa-plus"></i> Add Customer </a>
				</div> 
			</div>
	    </div>
		<div class="loadcustomerfrom" style="display:none">
			 
			  <div class="form-group hide">
			  <div class="col-sm-12">
			  <button type="button" data-toggle="tooltip" title="Save" class="btn btn-primary save-customer pull-right addcustomer"><i class="fa fa-save" style="color:#fff!important;"></i></button>
			  </div>
			  </div>
		</div>
<!-- Payment Method -->
		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo $text_pay_method; ?></label>
			<div class="col-sm-8">			
			  	<select name="payment_method" class="form-control" id="selectcard">
					<?php foreach ($setting_paymentmethods as $result){ ?>
					<?php if ($result['name'] == $payment_method){ ?>
					<option value="<?php echo $result['name']; ?>" selected="selected"><?php echo $result['name']; ?></option> 
					<?php } else { ?>
					<option value="<?php echo $result['name']; ?>"><?php echo $result['name']; ?></option> 
					<?php } ?>
					<?php } ?>
				</select>
			</div>
		</div>
<!-- Payment Method -->
		
			<!--sTART cODE-->
		<div id="cardinput" style="display:none" class="colors">
			<div class="form-group required">
			  <label class="col-sm-4 control-label" for="input-cc-owner"><?php echo $entry_cc_owner; ?></label>
			  <div class="col-sm-8">
				<input type="text" name="cc_owner" value="" placeholder="<?php echo $entry_cc_owner; ?>" id="input-cc-owner" class="form-control cc_owner" />
			  </div>
			</div>
			<div class="form-group required">
			  <label class="col-sm-4 control-label" for="input-cc-number"><?php echo $entry_cc_number; ?></label>
			  <div class="col-sm-8">
				<input type="text" name="cc_number" value="" placeholder="<?php echo $entry_cc_number; ?>" id="input-cc-number" class="form-control ccnumbercss" autocomplete="off"  />
			  </div>
			</div>
			<div class="form-group required">
			  <label class="col-sm-4 control-label" for="input-cc-expire-date"><?php echo $entry_cc_expire_date; ?></label>
			  <div class="col-sm-4">
				<select name="cc_expire_date_month" id="input-cc-expire-month" class="form-control">
				  <?php foreach ($months as $month) { ?>
				  <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
				  <?php } ?>
				</select>
			   </div>
			   <div class="col-sm-4">
				<select name="cc_expire_date_year" class="form-control" id="input-cc-expire-year">
				  <?php foreach ($year_expire as $year) { ?>
				  <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
				  <?php } ?>
				</select>
			  </div>
			</div>
			<div class="form-group required">
			  <label class="col-sm-4 control-label" for="input-cc-cvv2"><?php echo $entry_cc_cvv2; ?></label>
			  <div class="col-sm-8">
				<input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="input-cc-cvv2" class="form-control ccnumbercss" autocomplete="off"/>
			  </div>
			</div>
			
		
		</div>
		<!--eND cODE-->
	   		
		<div class="form-group colors1">
		  <label class="col-sm-4 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
		  <div class="col-sm-8">
			<select name="order_status_id" id="input-order-status" class="form-control">
			  <option value=""><?php echo $text_select?></option>
			  <?php foreach ($order_statuses as $order_status) { ?>
			  <?php if ($order_status['order_status_id'] == $order_status_id) { ?>
			  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
			  <?php } else { ?>
			  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
			  <?php } ?>
			  <?php } ?>
			</select>
			<input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
		  </div>
		</div>				
		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo $text_comment; ?></label>
			<div class="col-sm-8">			
			 <textarea name="comment" class="form-control"><?php echo $comment; ?></textarea>		  
		    </div> 
	    </div>		
		<div class="form-group">
			<label class="col-sm-4 control-label"></label>
			<div class="col-sm-8">			
			 <button class="btn btn-primary ordernow" id="pay-now"><?php echo $text_order; ?></button>
		    </div> 
	    </div>
	 </div>
	</div>
</div>

<script>

$('#selectcard').on('change', function () {
    if(this.value === "Card"){
        $("#cardinput").show();
    } else {
        $("#cardinput").hide();
    }
});

</script>

<script type="text/javascript">
//Autocomplete
$('input[name=\'customer_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=pos/paynow/autocomplete&token=<?php echo $token; ?>&customer_name=' +  encodeURIComponent(request),
			dataType: 'json',			
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['customer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'customer_name\']').val(item['label']);
		$('input[name=\'customer_id\']').val(item['value']);
	}	
});
$('.cc_owner').change(function(){
	var code=$(this).val();
	var code1=code.split('^');
	var cardnumber=code1[0].replace('%B','');
	var name=code1[1].split('/');
	var year=code1[2].substring(0,2);
	var month=code1[2].substring(2,4);
	$('#input-cc-owner').val(name[1]+' '+name[0]);
	$('#input-cc-number').val(cardnumber);
	$('#input-cc-expire-month').val(month);
	$('#input-cc-expire-year').val(20+year);
});
</script>

<script type="text/javascript"><!--
$('select[name=\'payment_method\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=possetting/setting/method&token=<?php echo $token; ?>&payment_method=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'payment_method\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('.fa-spin').remove();
		},
		success: function(json) {
			$('.fa-spin').remove();
			$('select[name=\'order_status_id\']').val(json['order_status_id']);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'payment_method\']').trigger('change');
//--></script>


<?php } ?>