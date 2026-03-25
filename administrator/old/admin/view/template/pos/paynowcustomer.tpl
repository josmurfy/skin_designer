		
				
		
		<div class="form-horizontal add-nowcustomer">
			<div class="form-group  <?php if($firstnamerequiredstatus) { echo 'required';  } ?> <?php if(!$firstnamestatus) { echo "hide"; }?>" data-sort="<?php echo $firstnamesort_order?>">
				<label class="col-sm-4 control-label" for="input-firstname"><?php echo $entry_firstname; ?></label>
				<div class="col-sm-8">
					<input type="text" name="firstname" value="<?php echo $firstname; ?>" placeholder="<?php echo $entry_firstname; ?>" id="input-firstname" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($lastnamerequiredstatus) { echo 'required';  } ?> <?php if(!$lastnamestatus) { echo "hide"; }?>" data-sort="<?php echo $lastnamesort_order?>">
				<label class="col-sm-4 control-label" for="input-lastname"><?php echo $entry_lastname; ?></label>
				<div class="col-sm-8">
					<input type="text" name="lastname" value="<?php echo $lastname; ?>" placeholder="<?php echo $entry_lastname; ?>" id="input-lastname" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($emailrequiredstatus) { echo 'required';  } ?> <?php if(!$emailstatus) { echo "hide"; }?>" data-sort="<?php echo $emailsort_order?>">
				<label class="col-sm-4 control-label" for="input-email"><?php echo $entry_email; ?></label>
				<div class="col-sm-8">
					<input type="text" name="email" value="<?php echo $email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($phonerequiredstatus) { echo 'required';  } ?> <?php if(!$phonestatus) { echo "hide"; }?>" data-sort="<?php echo $phonesort_order?>">
				<label class="col-sm-4 control-label" for="input-telephone"><?php echo $entry_telephone; ?></label>
				<div class="col-sm-8">
					<input type="text" name="telephone" value="<?php echo $telephone; ?>" placeholder="<?php echo $entry_telephone; ?>" id="input-telephone" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($faxrequiredstatus) { echo 'required';  } ?>  <?php if(!$faxstatus) { echo "hide"; }?>" data-sort="<?php echo $faxsort_order?>">
				<label class="col-sm-4 control-label" for="input-fax"><?php echo $entry_fax; ?></label>
				<div class="col-sm-8">
					<input type="text" name="fax" value="<?php echo $fax; ?>" placeholder="<?php echo $entry_fax; ?>" id="input-fax" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($companyrequiredstatus) { echo 'required';  } ?> <?php if(!$companystatus) { echo "hide"; }?>">
				<label class="col-sm-4 control-label" for="input-company" ><?php echo $entry_company; ?></label>
				<div class="col-sm-8">
					<input type="text" name="company" value="<?php echo $company; ?>" placeholder="<?php echo $entry_company; ?>" id="input-company" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($add1requiredstatus) { echo 'required';  } ?> <?php if(!$add1status) { echo "hide"; }?>" data-sort="<?php echo $add1sort_order?>">
				<label class="col-sm-4 control-label" for="input-address-1"><?php echo $entry_address_1; ?></label>
				<div class="col-sm-8">
					<input type="text" name="address_1" value="<?php echo $address_1; ?>" placeholder="<?php echo $entry_address_1; ?>" id="input-address-1" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($add2requiredstatus) { echo 'required';  } ?> <?php if(!$add2status) { echo "hide"; }?>" data-sort="<?php echo $add2sort_order?>">
				<label class="col-sm-4 control-label" for="input-address-2"><?php echo $entry_address_2; ?></label>
				<div class="col-sm-8">
					<input type="text" name="address_2" value="<?php echo $address_2; ?>" placeholder="<?php echo $entry_address_2; ?>" id="input-address-2" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($cityrequiredstatus) { echo 'required';  } ?> <?php if(!$citystatus) { echo "hide"; }?>" data-sort="<?php echo $citysort_order?>">
				<label class="col-sm-4 control-label" for="input-city"><?php echo $entry_city; ?></label>
				<div class="col-sm-8">
					<input type="text" name="city" value="<?php echo $city; ?>" placeholder="<?php echo $entry_city; ?>" id="input-city" class="form-control" />
				</div>
			</div>

			<div class="form-group <?php if($postcoderequiredstatus) { echo 'required';  } ?> <?php if(!$postcodestatus) { echo "hide"; }?>" data-sort="<?php echo $postcodesort_order?>">
				<label class="col-sm-4 control-label" for="input-postcode"><?php echo $entry_postcode; ?></label>
				<div class="col-sm-8">
					<input type="text" name="postcode" value="<?php echo $postcode; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($countryrequiredstatus) { echo 'required';  } ?> <?php if(!$countrystatus) { echo "hide"; }?>" data-sort="<?php echo $countrysort_order?>">
				<label class="col-sm-4 control-label" for="input-country"><?php echo $entry_country; ?></label>
				<div class="col-sm-8">
					<select name="country_id" id="input-country" class="form-control">
						<option value=""><?php echo $text_select; ?></option>
						<?php foreach ($countries as $country) { ?>
						<?php if ($country['country_id'] == $country_id) { ?>
						<option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
						<?php } else { ?>
						<option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
						<?php } ?>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group <?php if($zonerequiredstatus) { echo 'required';  } ?> <?php if(!$zonestatus) { echo "hide"; }?>" data-sort="<?php echo $zonesort_order?>">
				<label class="col-sm-4 control-label" for="input-zone"><?php echo $entry_zone; ?></label>
				<div class="col-sm-8">
					<select name="zone_id" id="input-zone" class="form-control">
					</select>
				</div>
			</div>
			<div class="form-group <?php if($pwdrequiredstatus) { echo 'required';  } ?> <?php if(!$pwdstatus) { echo "hide"; }?>" data-sort="<?php echo $pwdsort_order?>">
				<label class="col-sm-4 control-label" for="input-password"><?php echo $entry_password; ?></label>
				<div class="col-sm-8">
					<input type="password" name="password" value="" placeholder="<?php echo $entry_password; ?>" id="input-password" class="form-control" />
				</div>
			</div>
			<div class="form-group <?php if($cpwdrequiredstatus) { echo 'required';  } ?> <?php if(!$cpwdstatus) { echo "hide"; }?>" data-sort="<?php echo $cpwdsort_order?>">
				<label class="col-sm-4 control-label" for="input-confirm"><?php echo $entry_confirm; ?></label>
				<div class="col-sm-8">
					<input type="password" name="confirm" value="" placeholder="<?php echo $entry_confirm; ?>" id="input-confirm" class="form-control" />
				</div>
			</div>
			<div class="text-right">
			<button type="button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary addpaynowcustomer"><i class="fa fa-save" style="color:#fff!important;"></i></button>
		</div>
		</div>		
<script type="text/javascript"><!--
$('select[name=\'country_id\']').on('change', function() {
	$.ajax({
		url: 'index.php?route=pos/customerform/country&token=<?php echo $token; ?>&country_id=' + this.value,
		dataType: 'json',
		beforeSend: function() {
			$('select[name=\'country_id\']').after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
		},
		complete: function() {
			$('.fa-spin').remove();
		},
		success: function(json) {
			if (json['postcode_required'] == '1') {
				$('input[name=\'postcode\']').parent().parent().addClass('required');
			} else {
				$('input[name=\'postcode\']').parent().parent().removeClass('required');
			}

			html = '<option value=""><?php echo $text_select; ?></option>';

			if (json['zone'] && json['zone'] != '') {
				for (i = 0; i < json['zone'].length; i++) {
					html += '<option value="' + json['zone'][i]['zone_id'] + '"';

					if (json['zone'][i]['zone_id'] == '<?php echo $zone_id; ?>') {
						html += ' selected="selected"';
					}

					html += '>' + json['zone'][i]['name'] + '</option>';
				}
			} else {
				html += '<option value="0" selected="selected"><?php echo $text_none; ?></option>';
			}

			$('select[name=\'zone_id\']').html(html);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});
});

$('select[name=\'country_id\']').trigger('change');
//--></script>
