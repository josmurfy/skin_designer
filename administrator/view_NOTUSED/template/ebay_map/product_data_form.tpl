<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="product-data-form" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <?php if (isset($error_warning) && $error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="product-data-form" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="select-rule-for"><?php echo $entry_product; ?></label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="product_name" value="<?php echo $product_name; ?>">
              <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_price; ?></label>
            <div class="col-sm-10">
              <div class="input-group">
                <?php if ($symbol_left) { ?>
                  <span class="input-group-addon"><?php echo $symbol_left; ?></span>
                <?php } ?>
                <input type="text" id="input-price" class="form-control" value="<?php echo $product_price; ?>" readonly="readonly">
                <?php if ($symbol_right) { ?>
                  <span class="input-group-addon"><?php echo $symbol_right; ?></span>
                <?php } ?>
              </div>
            </div>
          </div>
          <ul class="nav nav-tabs">
            <?php if (count($ebay_specifications) > 0) { ?>
              <li class="active"><a href="#tab-specification" data-toggle="tab"><?php echo $tab_specification; ?></a></li>
            <?php } ?>
            <?php if (count($ebay_conditions) > 0) { ?>
              <li<?php if (count($ebay_specifications) < 1) { ?><?php echo ' class="active"'; ?><?php } ?>><a href="#tab-condition" data-toggle="tab"><?php echo $tab_condition; ?></a></li>
            <?php } ?>
            <?php if (isset($ebay_variations) && count($ebay_variations['option_values']) > 0) { ?>
              <li<?php if (count($ebay_specifications) < 1) { ?><?php echo ' class="active"'; ?><?php } ?>><a href="#tab-variation" data-toggle="tab"><?php echo $tab_variation; ?></a></li>
            <?php } ?>
            <?php if (count($ebay_templates) > 0) { ?>
              <li<?php if (count($ebay_specifications) < 1 && count($ebay_variations) < 1) { ?><?php echo ' class="active"'; ?><?php } ?>><a href="#tab-template" data-toggle="tab"><?php echo $tab_template; ?></a></li>
            <?php } ?>
            <?php if ((isset($ebay_specifications) && $ebay_specifications) || (isset($ebay_conditions) && $ebay_conditions)) { ?>
              <li><a href="#tab-scheduling" data-toggle="tab"> <?php echo $tab_scheduling; ?></a></li>
            <?php } ?>

            <li><a href="#tab-auction" data-toggle="tab"><?php echo $tab_auction; ?></a></li>
          </ul>
          <div class="tab-content">
            <?php if (count($ebay_specifications) > 0) { ?>
              <div class="tab-pane active" id="tab-specification">
                <?php foreach ($ebay_specifications as $key => $specification) { ?>
                  <div class="form-group">
                    <label class="col-sm-4 control-label" for="input-<?php echo $specification.ebay_specification_code; ?>"><?php echo $specification['name'] . ' ( ' . $specification['ebay_category_name'] . ' ) '; ?></label>
                    <div class="col-sm-6">
                      <select class="form-control" name="product_specification[<?php echo $specification['attribute_group_id']; ?>]">
                        <option value=""><?php echo $text_select; ?></option>
                        <?php if ($specification.attributes && is_array($specification['attributes'])) { ?>
                          <?php foreach ($specification['attributes'] as $key => $attribute) { ?>
                            <?php if ($product_specification && is_array($product_specification) && in_array($attribute['attribute_id'], $product_specification)) { ?>
                              <option value="<?php echo $attribute['attribute_id']; ?>" selected="selected"><?php echo $attribute['name']; ?></option>
                            <?php } else { ?>
                              <option value="<?php echo $attribute['attribute_id']; ?>"><?php echo $attribute['name']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                <?php } ?>
              </div>
            <?php } ?>
            <?php if (count($ebay_conditions) > 0) { ?>
              <div class="tab-pane<?php if (count($ebay_specifications) < 1) { ?><?php echo ' active'; ?><?php } ?>" id="tab-condition">
                <?php foreach ($ebay_conditions as $key => $ebay_condition) { ?>
                  <div class="form-group">
                    <label class="col-sm-4 control-label" for="input-<?php echo $ebay_condition['condition_attr_code']; ?>"><?php echo $ebay_condition['name']; ?></label>
                    <div class="col-sm-6">
                      <select class="form-control" name="product_condition[<?php echo $ebay_condition['id']; ?>]" id="input-<?php echo $ebay_condition['condition_attr_code']; ?>">
                        <option value=""><?php echo $text_select; ?></option>
                        <?php if ($ebay_condition['condition_values'] && is_array($ebay_condition['condition_values'])) { ?>
                          <?php foreach ($ebay_condition['condition_values'] as $key => $condition_value) { ?>
                            <?php if ($product_condition && is_array($product_condition) && $ebay_condition['id'] . '_' . in_array($condition_value['condition_id'], $product_condition)) { ?>
                              <option value="<?php echo $ebay_condition['id'] . '_' . $condition_value['condition_id']; ?>" selected="selected"><?php echo $condition_value['value']; ?></option>
                            <?php } else { ?>
                              <option value="<?php echo $ebay_condition['id'] . '_' . $condition_value['condition_id']; ?>"><?php echo $condition_value['value']; ?></option>
                            <?php } ?>
                          <?php } ?>
                        <?php } ?>
                      </select>
                    </div>
                  </div>
                <?php } ?>
              </div>
            <?php } ?>
            <?php if ($ebay_variations && count($ebay_variations['option_values']) > 0) { ?>
              <div class="tab-pane<?php if (count($ebay_specifications) < 1 && count($ebay_specifications) < 1) { ?><?php echo ' active'; ?><?php } ?>" id="tab-variation">
                <div class="form-group">
                  <div class="col-sm-12">
                    <div class="col-sm-4">
                      <h3><?php echo $ebay_variations['option_name']; ?></h3>
                      <div class="well well-sm" style="height:250px;overflow:auto" >
                        <?php foreach ($ebay_variations['option_values'] as $key => $option_value) { ?>
                          <div class="checkbox variation_value">
                            <label for="option_value_<?php echo $option_value['option_value_id']; ?>">
                              <input type="checkbox" name="product_variation[]" value="<?php echo $option_value['option_value_id']; ?>" id="option_value_<?php echo $option_value['option_value_id']; ?>" data-variation-id = "<?php echo $option_value['option_id']; ?>"
                              <?php if ($product_variation && in_array($option_value['option_value_id'], $product_variation)) { ?><?php echo "checked"; ?><?php } ?>/>
                              <?php echo $option_value['name']; ?>
                            </label>
                          </div>
                        <?php } ?>
                      </div>
                    </div>
                    <div class="col-sm-8">
                      <h3><?php echo "Variation Value List"; ?></h3>
                      <div class="well well-sm" style="height:350px;overflow:auto">
                        <ul class="nav nav-pills nav-stacked" id="product_variation_list">
                          <?php if ($product_variation_value) { ?>
                            <?php foreach ($product_variation_value as $key => $option_value) { ?>
                              <?php foreach ($option_value['option_value'] as $key_option => $product_option_value) { ?>
                                  <li id="<?php echo 'product_variation_' . $key_option; ?>">
                                    <div class="form-group">
                                      <div class="col-sm-3"><input class="form-control" type="hidden"  name="product_variation_value[<?php echo $key; ?>][option_id]" value="<?php echo $key; ?>" />
                                        Variation Name<input class="form-control" type="text" readonly name="product_variation_value[<?php echo $key; ?>][option_value][<?php echo $key_option; ?>][name]" value="<?php echo $product_option_value['name']; ?>" /> </div>
                                      <div class="col-sm-3">
                                        Quantity<input type="text" class="form-control" name="product_variation_value[<?php echo $key; ?>][option_value][<?php echo $key_option; ?>][quantity]" value="<?php echo $product_option_value['quantity']; ?>" placeholder="Quantity" /></div>
                                        <div class="col-sm-3">
                                          Price<input type="text" class="form-control" name="product_variation_value[<?php echo $key; ?>][option_value][<?php echo $key_option; ?>][price]" value="<?php echo $product_option_value['price']; ?>" placeholder="Price" /></div>
                                        <div class="col-sm-3">
                                          Price Prefix<select class="form-control" name="product_variation_value[<?php echo $key; ?>][option_value][<?php echo $key_option; ?>][price_prefix]">
                                            <?php if ($product_option_value['price_prefix'] == '+') { ?>
                                              <option value="+" selected="selected">+</option>
                                            <?php } else { ?>
                                              <option value="+">+</option>
                                            <?php } ?>
                                            <?php if ($product_option_value['price_prefix'] == '-') { ?>
                                              <option value="-" selected="selected">-</option>
                                            <?php } else { ?>
                                              <option value="-">-</option>
                                            <?php } ?>
                                          </select>
                                        </div>
                                    </div>
                                  </li>
                                <?php } ?>
                              <?php } ?>
                            <?php } ?>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <script type="text/javascript">
                $('.variation_value > label > input[type="checkbox"]').on('click', function(){
                  option_value_id = $(this).val();
                  option_value_name = $.trim($(this).parent('label').text());
                  option_id = $(this).data('variation-id');
                  if($(this).is(':checked')) {
                    html = '';
                    html += '<li id="product_variation_'+option_value_id+'"><div class="form-group">';
                    html += '<div class="col-sm-3"><input class="form-control" type="hidden"  name="product_variation_value['+option_id+'][option_id]" value="'+option_id+'" /> Variation Name<input class="form-control" type="text" readonly name="product_variation_value['+option_id+'][option_value]['+option_value_id+'][name]" value="'+option_value_name+'" /> </div>';
                    html += '<div class="col-sm-3">Quantity<input type="text" class="form-control" name="product_variation_value['+option_id+'][option_value]['+option_value_id+'][quantity]" value="" placeholder="Quantity" /></div>';
                    html += '<div class="col-sm-3">Price<input type="text" class="form-control" name="product_variation_value['+option_id+'][option_value]['+option_value_id+'][price]" value="" placeholder="Price" /></div>';
                    html += '<div class="col-sm-3">Price Prefix<select class="form-control" name="product_variation_value['+option_id+'][option_value]['+option_value_id+'][price_prefix]"><option value="+" >+</option><option value="-" >-</option></select></div>';
                    html += '</div></li>';

                    $('#product_variation_list').append(html);
                  } else {
                    $('#product_variation_'+option_value_id).remove();
                  }
                });
              </script>
            <?php } ?>
            <?php if (count($ebay_templates) > 0) { ?>
              <div class="tab-pane<?php if (count($ebay_specifications) < 1 && count($ebay_specifications) < 1 && count($ebay_variations) < 1) { ?><?php echo ' active'; ?><?php } ?>" id="tab-template">
                <div class="alert alert-info"><b><?php echo $info_ebay_template; ?></b></div>
                <div class="form-group">
                  <label class="col-sm-4 control-label" for="input-ebay-template"><?php echo $entry_template; ?></label>
                  <div class="col-sm-6">
                    <select class="form-control" name="product_ebay_template" id="input-ebay-template">
                      <option value=""><?php echo $text_select; ?></option>
                      <?php foreach ($ebay_templates as $key => $ebay_template) { ?>
                        <option value="<?php echo $ebay_template['id']; ?>" <?php if ($product_ebay_template && $product_ebay_template == $ebay_template['id']) { ?><?php echo ' selected'; ?><?php } ?>><?php echo $ebay_template['title']; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
            <?php } ?>
            <?php if ((isset($ebay_specifications) && $ebay_specifications) || (isset($ebay_conditions) && $ebay_conditions)) { ?>
              <div class="tab-pane" id="tab-scheduling">
                <div class="form-group">
                  <label class="col-sm-3 control-label" for="input-scheduling_type"><?php echo $text_ebay_listing_option; ?></span> </label>
                  <div class="col-sm-6">
                    <select name="scheduling_type" class="form-control">
                      <option value="fix" <?php if(isset($scheduling_type) && $scheduling_type!='schedule'){ echo 'selected';  } ?>> <?php echo $text_list_on_export; ?></option>
                      <option value="schedule" <?php if(isset($scheduling_type) && $scheduling_type=='schedule'){ echo 'selected';  } ?>><?php echo $text_schedule_listing; ?></option>
                     </select>
                  </div>
                </div>

                <div class="scheduling_items">
                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-scheduling_type"  ><?php echo $text_schedule_detail; ?>: </span> </label>
                    <div class="col-sm-3">
                      <div class="input-group date">
                        <input type="text" name="scheduling_date" placeholder="<?php echo $schedule_date_placeholder; ?>" value="<?php if (isset($scheduling_date)) { echo $scheduling_date; } ?>" autocomplete="off" class="form-control" data-date-format="YYYY-MM-DD" readonly>
                        <span class="input-group-btn"><button type="button" class="btn btn-default">
                          <i class="fa fa-calendar"></i>
                        </button></span>
                      </div>
                  </div>
                  <div class="col-sm-3">
                    <div class="input-group time">
                      <input type="text" name="scheduling_time" placeholder="<?php echo $schedule_time_placeholder; ?>" value="<?php if (isset($scheduling_time)) { echo $scheduling_time; } ?>" autocomplete="off" class="form-control" data-date-format="HH:MM:SS" readonly>
                      <span class="input-group-btn"><button type="button" class="btn btn-default">
                        <i class="fa fa-clock-o"></i>
                      </button></span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <?php } ?>

            <div class="tab-pane" id="tab-auction">
              <div class="form-group">
                <label class="col-sm-4 control-label" for="input-auction-status"><?php echo $entry_auction_status; ?></label>
              <div class="col-sm-6">
                <select class="form-control" name="auction_status">
                  <?php if ($auction_status) { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                  <?php } else { ?>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <option value="1"><?php echo $text_enabled; ?></option>
                  <?php } ?>
                </select>
              </div>
              </div>
              <div class="form-group">
                  <label class="col-sm-4 control-label" for="input-buy-it-now-price"><span data-toggle="tooltip" title="<?php echo $help_buy_it_now_price; ?>"><?php echo $entry_buy_it_now_price; ?></span></label>
                  <div class="col-sm-6">
                    <input type="text" name="buy_it_now_price" id="input-buy-it-now-price" class="form-control" value="<?php echo $buy_it_now_price; ?>" placeholder="<?php echo $entry_buy_it_now_price; ?>" onkeypress="return validate(event, this);">
                  </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label"><span data-toggle="tooltip" title="<?php echo $help_price_rule_status; ?>"><?php echo $entry_price_rule_status; ?></span></label>
                <div class="col-sm-6">
                  <select class="form-control" name="price_rule_status">
                    <?php if ($price_rule_status && $price_rule_status == 'disabled') { ?>
                      <option value="disabled"><?php echo $text_disabled; ?></option>
                      <option value="enabled"><?php echo $text_enabled; ?></option>
                    <?php } else { ?>
                      <option value="enabled"><?php echo $text_enabled; ?></option>
                      <option value="disabled"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $('input[name=\'product_name\']').autocomplete({
    'source': function(request, response) {
  		$.ajax({
  			url: 'index.php?route=ebay_map/product_data/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
  			dataType: 'json',
  			success: function(json) {
  				response($.map(json, function(item) {
  					return {
  						label: item['name'],
  						value: item['product_id'],
              price: item['price']
  					}
  				}));
  			}
  		});
  	},
  	'select': function(item) {
  		$('input[name=\'product_name\']').val(item['label']);
      $('input[name=\'product_id\']').val(item['value']);
      $('#input-price').val(item['price']);
  	}
  });

  //Function to allow only numbers to textbox
  function validate(key, thisthis, nodot) {
    //getting key code of pressed key
    var keycode = (key.which) ? key.which : key.keyCode;

    if (keycode == 46) {
      if (nodot) {
        return false;
      }

      var val = $(thisthis).val();
      if (val == val.replace('.', '')) {
        return true;
      } else {
        return false;
      }
    }

    //comparing pressed keycodes
    if (!(keycode == 8 || keycode == 9 || keycode == 46 || keycode == 116) && (keycode < 48 || keycode > 57)) {
      return false;
    } else {
      return true;
    }
  }
  $('.date').datetimepicker({
  	pickTime: false
  });

  $('.time').datetimepicker({
  	pickDate: false,
    icons: {
      up: "fa fa-arrow-up",
     down: "fa fa-arrow-down"
    }
  });

  $(document).on('ready', function() {
    if ($('[name=\'scheduling_type\'] option:selected').val()=='fix') {
     $('.scheduling_items').hide();
    } else {
      $('.scheduling_items').show();
    }
  });

  $(document).on('change','[name=\'scheduling_type\']', function(){
    if(this.value=='schedule'){
      $('.scheduling_items').show();
    }else{
      $('.scheduling_items').hide();
    }
  });

</script>
<?php echo $footer; ?>
