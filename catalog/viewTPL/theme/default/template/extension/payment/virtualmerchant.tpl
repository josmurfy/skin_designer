<form id="payment" class="form-horizontal">
    <fieldset>
        <legend><?php echo $text_credit_card; ?></legend><div id="evalon-notification">    <div class="text-center alert alert-info">&nbsp;<?php echo $button_confirm; ?></div></div>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-owner"><?php echo $entry_cc_owner; ?></label>
            <div class="col-sm-10">
                <input type="text" name="cc_owner" value="" placeholder="<?php echo $entry_cc_owner; ?>" id="input-cc-owner" class="form-control" />
            </div>
        </div>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-number"><?php echo $entry_cc_number; ?></label>
            <div class="col-sm-10">
                <input type="text" name="cc_number" value="" placeholder="<?php echo $entry_cc_number; ?>" id="input-cc-number" class="form-control" />
            </div>
        </div>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-expire-date"><?php echo $entry_cc_expire_date; ?></label>
            <div class="col-sm-5">
                <select name="cc_expire_date_month" id="input-cc-expire-date" class="form-control">
                    <?php foreach ($months as $month) { ?>
                    <option value="<?php echo $month['value']; ?>"><?php echo $month['text']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-5">
                <select name="cc_expire_date_year" class="form-control">
                    <?php foreach ($year_expire as $year) { ?>
                    <option value="<?php echo $year['value']; ?>"><?php echo $year['text']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-cc-cvv2"><?php echo $entry_cc_cvv2; ?></label>
            <div class="col-sm-10">
                <input type="text" name="cc_cvv2" value="" placeholder="<?php echo $entry_cc_cvv2; ?>" id="input-cc-cvv2" class="form-control" />
            </div>
        </div>		<div class="form-group">			<div class="col-sm-10 col-sm-offset-2">				<img src="https://phoenixliquidation.ca/image/catalog/cie/evalonsecure.png" />			</div>		</div>
    </fieldset>
</form>
<div class="buttons">
    <div class="pull-right">
        <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
    </div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').on('click', function() {
	$.ajax({
		url: 'index.php?route=extension/payment/virtualmerchant/send',
		type: 'post',
		data: $('#payment :input'),
		dataType: 'json',
		cache: false,		
		beforeSend: function() {
			$('#button-confirm').button('loading').prop('disabled', true);
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},				
		success: function(json) {
			if (json['error']) {				$('#evalon-notification').html('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>&nbsp;' + json['error'] + '</div>');
				$('#button-confirm-order').button('reset');
			}
			if (json['redirect']) {
				location = json['redirect'];
			}
		}
	});
});
//--></script>