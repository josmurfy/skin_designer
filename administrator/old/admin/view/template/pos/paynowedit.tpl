<?php 
if(!empty($warning)) {
?>
<div id="content" class="col-sm-12 bg alert text-danger">
	<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $warning?><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>
</div>
<?php } else { ?>

<div id="content" class="col-sm-12 bg paynowfinal">
 <div class="modal-header">
	<h3><?php echo $text_editplace; ?></h3>
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="col-sm-12 form-horizontal">
	<div class="order-editdata">	   
		<?php if (empty($guestcustomer)) { ?>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?php echo $text_customer; ?></label>
			<div class="col-sm-8">
			  <div class="checkbox">
				  <label class="guescust">					
                      <input type="checkbox" name="guestcustomer" value="1" checked="checked" id="input-guestcustomer" disabled="disabled" /> Guest
				</div> 
			</div>
	    </div>
		<?php } ?>		
		<div class="loadcustomerfrom add-customer" <?php if (empty($guestcustomer)) { ?> style="display:none" <?php } ?>>
			<input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" class="form-control customer_id"/>
			 <div class="form-group required">
				<label class="col-sm-4 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
				<div class="col-sm-8">
				  <input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
				 </div>
			  </div>
			  
			  <div class="form-group required">
				<label class="col-sm-4 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
				<div class="col-sm-8">
				  <input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
				 </div>
			  </div>
			  
			  <div class="form-group required">
				<label class="col-sm-4 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
				<div class="col-sm-8">
				  <input type="text" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
				  </div>
			  </div>
			  
			   <div class="form-group required">
				<label class="col-sm-4 control-label" for="input-address-1"><?php echo $entry_address_1; ?></label>
				<div class="col-sm-8">
				  <input type="text" name="address_1" value="<?php echo $address_1; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1" class="form-control" />
				 </div>
			  </div>
		</div>
<!-- Payment Method -->
		<div class="editorder_div">
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
					<input type="text" name="cc_number" value="" placeholder="<?php echo $entry_cc_number; ?>" id="input-cc-number" class="form-control ccnumbercss"  autocomplete="off"/>
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
					<input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="input-cc-cvv2" class="form-control ccnumbercss" autocomplete="off" />
				  </div>
				</div>
			</div>
		</div>
		
		
		<div class="form-group colors1">
		  <label class="col-sm-4 control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
		  <div class="col-sm-8">
			<select name="order_status_id" id="selectorder_status" class="form-control">
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
			 <button class="btn btn-primary editorder"><?php echo $text_clearcart; ?></button>
		    </div> 
	    </div>
	 </div>
	</div>
</div>
<?php } ?>

<script type="text/javascript">
  $('#selectcard').on('change', function () {
    if(this.value === "Card"){
        $("#cardinput").show();
    } else {
        $("#cardinput").hide();
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