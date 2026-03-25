<li class="dropdown" id="me_admin_search"><a href="#" class="search-toggle btn btn-default"><i class="fa fa-search"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $text_quick_search; ?></span></a>
	<ul class="me_dropdown_menu">
		<div class="row">
			<?php if(!empty($me_admin_search_filter['pname']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-pname"><?php echo $entry_pname; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_pname" value="<?php echo $filter_pname; ?>" placeholder="<?php echo $entry_pname; ?>" id="input-pname" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('catalog/product','filter_name','filter_pname')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['pmodel']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-pmodel"><?php echo $entry_pmodel; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_pmodel" value="<?php echo $filter_pmodel; ?>" placeholder="<?php echo $entry_pmodel; ?>" id="input-pmodel" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('catalog/product','filter_model','filter_pmodel')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['psku']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-psku"><?php echo $entry_psku; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_psku" value="<?php echo $filter_psku; ?>" placeholder="<?php echo $entry_psku; ?>" id="input-psku" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('catalog/product','filter_sku','filter_psku')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['category']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-cname"><?php echo $entry_cname; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_cname" value="<?php echo $filter_cname; ?>" placeholder="<?php echo $entry_cname; ?>" id="input-cname" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('catalog/category','filter_name','filter_cname')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['manufacturer']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-manufacturer"><?php echo $entry_manufacturer; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_pmanufacturer" value="<?php echo $filter_pmanufacturer; ?>" placeholder="<?php echo $entry_manufacturer; ?>" id="input-manufacturer" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('catalog/manufacturer','filter_name','filter_pmanufacturer')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['option']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-option"><?php echo $entry_option; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_poption" value="<?php echo $filter_poption; ?>" placeholder="<?php echo $entry_option; ?>" id="input-option" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('catalog/option','filter_name','filter_poption')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['customer']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-customer_name"><?php echo $entry_customer; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_customer_name" value="<?php echo $filter_customer_name; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer_name" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('customer/customer','filter_name','filter_customer_name')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['customer_email']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-customer_email"><?php echo $entry_customer_email; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_customer_email" value="<?php echo $filter_customer_email; ?>" placeholder="<?php echo $entry_customer_email; ?>" id="input-customer_email" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('customer/customer','filter_email','filter_customer_email')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['customer_telephone']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-customer_telephone"><?php echo $entry_customer_telephone; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_customer_telephone" value="<?php echo $filter_customer_telephone; ?>" placeholder="<?php echo $entry_customer_telephone; ?>" id="input-customer_telephone" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('customer/customer','filter_telephone','filter_customer_telephone')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['orderid']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-orderid"><?php echo $entry_orderid; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_orderid" value="<?php echo $filter_orderid; ?>" placeholder="<?php echo $entry_orderid; ?>" id="input-orderid" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('sale/order','filter_order_id','filter_orderid')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['orderbycustomer']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-orderbycustomer"><?php echo $entry_orderbycustomer; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_orderbycustomer" value="<?php echo $filter_orderbycustomer; ?>" placeholder="<?php echo $entry_orderbycustomer; ?>" id="input-orderbycustomer" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('sale/order','filter_customer','filter_orderbycustomer')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['orderbycustomertel']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-orderbycustomertel"><?php echo $entry_orderbycustomertel; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_orderbycustomertel" value="<?php echo $filter_orderbycustomertel; ?>" placeholder="<?php echo $entry_orderbycustomertel; ?>" id="input-orderbycustomertel" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('sale/order','filter_telephone','filter_orderbycustomertel')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['orderbyproduct']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-orderbyproduct"><?php echo $entry_orderbyproduct; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_orderbyproduct" value="<?php echo $filter_orderbyproduct; ?>" placeholder="<?php echo $entry_orderbyproduct; ?>" id="input-orderbyproduct" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('sale/order','filter_product','filter_orderbyproduct')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['orderstatus']['status'])) { ?>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="control-label" for="input-orderstatus"><?php echo $entry_orderstatus; ?></label>
					<div class="input-group input-group-sm">
						<select class="form-control" name="filter_orderstatusid">
							<option value=""><?php echo $text_select; ?></option>
							<option value="0" <?php if($filter_orderstatusid == '0') { ?>selected="selected"<?php } ?>><?php echo $text_missing; ?></option>
							<?php foreach($order_statuses as $order_status) { ?>
							<option value="<?php echo $order_status['order_status_id']; ?>" <?php if($filter_orderstatusid == $order_status['order_status_id']) { ?>selected="selected"<?php } ?>><?php echo $order_status['name']; ?></option>
							<?php } ?>
						</select>
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('sale/order','filter_order_status_id','filter_orderstatusid')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['ordertotal']['status'])) { ?>
			<div class="col-sm-4">	
				<div class="form-group">
					<label class="control-label" for="input-ototal"><?php echo $entry_ototal; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_ototal" value="<?php echo $filter_ototal; ?>" placeholder="<?php echo $entry_ototal; ?>" id="input-ototal" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('sale/order','filter_total','filter_ototal')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['orderdate']['status'])) { ?>
			<div class="col-sm-8">
				<div class="form-group">
					<div class="row">
						<div class="col-sm-6">
							<label class="control-label" for="input-date-from"><?php echo $entry_filter_from_date; ?></label>
							<div class="input-group date input-group-sm">
								<input type="text" name="filter_from_date" value="<?php echo $filter_from_date; ?>" placeholder="<?php echo $entry_filter_from_date; ?>" data-date-format="YYYY-MM-DD" id="input-date-from" class="form-control" />
								<span class="input-group-btn">
									<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
								</span> 
							</div>
						</div>
						<div class="col-sm-6">
							<label class="control-label" for="input-date-to"><?php echo $entry_filter_to_date; ?></label>
							<div class="d-flex">
								<div class="input-group date input-group-sm">
									<input type="text" name="filter_to_date" value="<?php echo $filter_to_date; ?>" placeholder="<?php echo $entry_filter_to_date; ?>" data-date-format="YYYY-MM-DD" id="input-date-to" class="form-control" />
									<span class="input-group-btn">
										<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
									</span>
								</div>
								<button type="button" class="btn btn-primary btn-sm" id="button-searchdate"><i class="fa fa-search"></i></button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php } ?>
			<?php if(!empty($me_admin_search_filter['coupon']['status'])) { ?>
			<div class="col-sm-4">	
				<div class="form-group">
					<label class="control-label" for="input-coupon"><?php echo $entry_coupon; ?></label>
					<div class="input-group input-group-sm">
						<input type="text" name="filter_mcoupon" value="<?php echo $filter_mcoupon; ?>" placeholder="<?php echo $entry_coupon; ?>" id="input-coupon" class="form-control" />
						<span class="input-group-btn"><button class="btn btn-primary" onclick="meadminsearch('marketing/coupon','filter_name','filter_mcoupon')"><i class="fa fa-search"></i></button></span>
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
	</ul>
</li>
<style>
.d-flex{
	display:flex;
}
.search-toggle{
	border-radius: 0;
	border: none;
}
.me_dropdown_menu{
	position: absolute;
    top: 100%;
    right: 0;
    left: auto;
    z-index: 1000;
    display: none;
    float: left;
    min-width:650px;
    padding:0 15px;
    list-style: none;
    font-size: 13px;
    text-align: left;
    background-color: #fff;
    border: 1px solid #ccc;
    border: 1px solid rgba(0, 0, 0, .15);
    border-radius: 0;
    -webkit-box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
    box-shadow: 0 6px 12px rgba(0, 0, 0, .175);
    background-clip: padding-box;
}
.search-toggle:hover .me_dropdown_menu{
	display:block;
}
#me_admin_search.open .me_dropdown_menu{
	display:block;
}
#me_admin_search .dropdown-menu > li > a{
	white-space: inherit;
	overflow-wrap: break-word;
    padding: 5px 15px;
}
</style>
<script type="text/javascript"><!--
$('.search-toggle').on('click', function(event){
    event.preventDefault();
	$('.me_dropdown_menu').toggle();
});
$(document).on("click", function(event){
	var $trigger = $("#me_admin_search");
	if($trigger !== event.target && !$trigger.has(event.target).length){
		$('.me_dropdown_menu').css('display','none');
	}            
});

$('#button-searchdate').on('click', function() {
	var url = '';
	
	var filter_from_date = $('input[name=\'filter_from_date\']').val();

	if (filter_from_date) {
		url += '&filter_from_date=' + encodeURIComponent(filter_from_date);
	}
	
	var filter_to_date = $('input[name=\'filter_to_date\']').val();

	if (filter_to_date) {
		url += '&filter_to_date=' + encodeURIComponent(filter_to_date);
	}
	
	location = 'index.php?route=sale/order&token=<?php echo $token; ?>' + url;
});

//Category
$('input[name=\'filter_cname\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['category_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_cname\']').val(item['label']);
	}
});

//Product Name
$('input[name=\'filter_pname\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_pname\']').val(item['label']);
	}
});

//Product SKU
$('input[name=\'filter_psku\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=extension/module/me_admin_search/pautocomplete&token=<?php echo $token; ?>&filter_sku=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['sku'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_psku\']').val(item['label']);
	}
});

$('input[name=\'filter_orderbyproduct\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_orderbyproduct\']').val(item['label']);
	}
});

//Product Model
  $('input[name=\'filter_pmodel\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['model'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_pmodel\']').val(item['label']);
	}
});

//Manufacturer
$('input[name=\'filter_pmanufacturer\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/manufacturer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['manufacturer_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_pmanufacturer\']').val(item['label']);
	}
});

//Option
$('input[name=\'filter_poption\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/option/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['option_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_poption\']').val(item['label']);
	}
});

//Customer
$('input[name=\'filter_customer_name\']').autocomplete({
    'source': function(request, response) {
      $.ajax({
        url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
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
      $('input[name=\'filter_customer_name\']').val(item['label']);
    }
});

$('input[name=\'filter_orderbycustomer\']').autocomplete({
    'source': function(request, response) {
      $.ajax({
        url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
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
      $('input[name=\'filter_orderbycustomer\']').val(item['label']);
    }
});

$('input[name=\'filter_customer_email\']').autocomplete({
    'source': function(request, response) {
      $.ajax({
        url: 'index.php?route=customer/customer/autocomplete&token=<?php echo $token; ?>&filter_email=' +  encodeURIComponent(request),
        dataType: 'json',
        success: function(json) {
          response($.map(json, function(item) {
            return {
              label: item['email'],
              value: item['customer_id']
            }
          }));
        }
      });
    },
    'select': function(item) {
      $('input[name=\'filter_customer_email\']').val(item['label']);
    }
});

$('input[name=\'filter_mcoupon\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=extension/module/me_admin_search/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['coupon_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_mcoupon\']').val(item['label']);
	}
});

$('#me_admin_search input').on('keydown', function(e) {
	if (e.keyCode == 13) {
		$(this).parent().find('button').trigger('click');
	}
});

function meadminsearch(route,key,filter_key){
	var url = '';
	
	if(key == 'filter_order_status_id'){
		var filter_key_value = $('select[name=\''+ filter_key +'\']').val();
	}else{
		var filter_key_value = $('input[name=\''+ filter_key +'\']').val();
	}
	
	if (filter_key_value) {
		url += '&'+ key +'=' + encodeURIComponent(filter_key_value);
	}
	
	location = 'index.php?route='+ route +'&token=<?php echo $token; ?>' + url;
}
//--></script>
<script type="text/javascript"><!--
$('.date').datetimepicker({
	pickTime: false
});
//--></script>