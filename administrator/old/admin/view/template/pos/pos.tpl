<div class="pospage">
<?php echo $header; ?>
<script type='text/javascript' src="view/javascript/jquery/escroll.js"></script>
<link type="text/css" href="view/stylesheet/pos.css" rel="stylesheet" media="screen" />
<?php echo $column_left; ?>
<div id="content">
<?php echo $dashboard; ?>
<!-- sidebar start here -->
<div class="sidebar">
	<ul class="list-unstyled">
		<li>
			<button type="button" class="orderbtn"><i class="fa fa-shopping-cart"></i></button>
			<div class="orderbox">
				<a class="btn orderlist"><?php echo $button_orderlist; ?></a><i class="fa fa-shopping-cart"></i>
			</div>
		</li>
		<li>
			<button type="button" class="custbtn"><i class="fa fa-user"></i></button>
			<div class="custbox">
				<a class="btn customerlist"><?php echo $button_customerlist; ?></a><i class="fa fa-user"></i>
			</div>
		</li>
		<li>
			<button type="button" class="printbtn" ><i class="fa fa-print"></i></button>
			<div class="printbox">
				<a class="btn" data-toggle="modal" data-target="#printModal"><?php echo $button_print; ?></a><i class="fa fa-print"></i>
			</div>
		</li>
        <li class="clickexpend" rel="1"><a href="javascript:;"  ><i class="fa fa-expand" aria-hidden="true"></i></a></li>
	</ul>
</div>	
<!-- sidebar end here -->

  
  <div class="container-fluid"> 
	  <div id="dash">
		<div class="row">
			<div class="col-md-12 col-sm-12 col-xs-12 padd0 dashbox">		
				<div class="col-md-6 col-sm-6 col-xs-12">
					<div class="leftside1">
						<?php echo $categorysearch; ?>
					</div>					
					<div class="leftside" id="maincontainer">						
						<div class="icon">
							<a href="javascript:;" class="showsubcate" rel="0"><i class="fa fa-home" aria-hidden="true"></i></a>
							<span class="breadcrumbsload"> </span>
							
							<div class="pull-right">
							<a class="btn posproduct btn-primary"><?php echo $button_product; ?></a>
							</div>
							
						</div>	
						<div class="scrollbox3">						
							<div class="allcate">
								<div  id="wait" class="loader hide">
									<img src="view/image/loader.gif" 	alt="loading" title="loading"/>
								</div>
								<?php echo $allcategory; ?>
							</div>									
						</div>
					</div>
				</div>			
				
				<div class="col-md-6 col-sm-6 col-xs-12 ">
					
					<div class="rightside">
						<div class="tablebox">
							<table>
								<tr>
									<th width="15%"><?php echo $text_item; ?></th>
									<th width="20%"><?php echo $text_name; ?></th>
									<th width="15%"><?php echo $text_price; ?></th>
									<th width="15%"><?php echo $text_qty; ?></th>
									<th width="15%"><?php echo $text_total; ?></th>
									<th width="20%"><?php echo $text_action; ?></th>
								</tr>
							</table>
							
							<div class="scrollbox4">
								
							<div class="loadcartclass">
								
								
							</div>
							</div>
							
							<div class="value col-sm-12 sub-totals">
								<div class="extra-options">
									<div class="input-group extraoptions padd col-sm-12" id="manualdiscount">
										<input placeholder="Discount" name="discount" id="discount" type="text" class="form-control">
										<select name="discount_type" id="discount-type" class="form-control">
											<option value="F"><?php echo $text_fixed; ?></option>
											<option value="P"><?php echo $text_percent; ?></option>
										</select>
										<input type="button" value="<?php echo $button_discount; ?>" data-loading-text="<?php echo $text_loading; ?>"  class="btn cartcoupon manualdiscount" />
									</div>
									
									<div class="col-sm-12 padd">
										<div class="input-group" id="coupondiscount">
											<input type="text" name="coupon" value="<?php echo $coupon; ?>" placeholder="<?php echo $entry_coupon; ?>" id="input-coupon" class="form-control" />
											<span class="input-group-btn">
											<input type="button" value="<?php echo $button_coupon; ?>"   data-loading-text="<?php echo $text_loading; ?>"  class="btn cartcoupon  coupondiscount" />
											</span>
										</div>				
										<div class="input-group"id="voucherdiscount">
											<input type="text" name="voucher" value="<?php echo $voucher; ?>" placeholder="<?php echo $entry_voucher; ?>" id="input-voucher" class="form-control" />
											<span class="input-group-btn">
											<input type="submit" value="<?php echo $button_voucher; ?>"   data-loading-text="<?php echo $text_loading; ?>"  class="btn cartcoupon voucherdiscount"/>
											</span> 
										</div>
									</div>				
								</div>
								
								<div class="col-md-6 col-sm-6 col-xs-12">
									
									<ul class="list-unstyled totalload">
										
									
									</ul>
								</div>
								<div class="col-md-6 col-sm-6 col-xs-12 padd0">
									<a class="btn btn-primary paynow"><?php echo $button_paynow; ?></a>
									<!-- <a class="btn btn-primary paynow" <?php if(!empty($order_id)) { ?>style="display:none" <?php }?>><?php echo $button_paynow; ?></a> -->
									<!-- <a class="btn btn-primary editorder"  <?php if(empty($order_id)) { ?>style="display:none" <?php }?>><?php echo $button_paynow; ?></a> -->
								</div>
							</div>	
						</div>
					</div>					 
				</div>					
			</div>   
		</div>
	  </div>
  </div>
</div>
</div>
<input type="hidden" value="2" id="page" />
<script type="text/javascript">
var oldpath='';
$(document).on('click', '.showsubcate',function(){
	rel=$(this).attr('rel');
	path=$(this).attr('path');
	$.ajax({
		url: 'index.php?route=pos/pos/ajaxloaddata&token=<?php echo $token?>&path='+path+'&category_id='+rel,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('.loader').removeClass('hide');
			$('.categories').addClass('hide');
			$('.products').html('');
		},
		complete: function() {
			
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.loader').addClass('hide');
			$('.categories').removeClass('hide');			
			$('.categories').html();
			$('.products').html('');
			if (json['categories']) {
				html='';
				for (i in json['categories']) {
				html +='<li class="col-md-4 col-sm-4 col-xs-12 showsubcate" rel="'+json['categories'][i]['category_id']+'" path="'+json['categories'][i]['path']+'">';
				html +='<i class="fa fa-folder-open" aria-hidden="true"></i>';
				html +='<br>'+json['categories'][i]['name']+'</li>';	
				}
				$('.categories').html(html);
			}
			if (json['products']) {
				$('.loader').addClass('hide');	
				html='';
				for (i in json['products']) {
				html +='<div class="col-md-4 col-sm-4 col-xs-12">';
				html +='<div class="box4">';
				stockclass="off";
				if(json['products'][i]['stock']<10)
				{
				stockclass='off1';
				}
				html +='<div class="'+stockclass+'">';
				html +='<span>'+json['products'][i]['stock']+'</span>';
				html +='</div>';
				html +='<div class="image">';
				html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
				html +='</div>';
				html +='<div class="buttons">';
				html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
				html +='<i class="fa fa-info-circle" aria-hidden="true"></i>Info</a>';
				html +='<a class="addtocartquick" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Cart</a>';
				html +='</div>';
				
				html +='<div class="caption"><h1>'+json['products'][i]['name']+'</h1>';
				if(json['products'][i]['price'])
				{
					html +='<p class="price">';
					if(!json['products'][i]['special'])
					{
					html +=json['products'][i]['price']
					} else {
					html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
					}
                  if(json['products'][i]['tax'])
					{
						html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
					}
					html +='</p>';
                }
				html +='<b>'+json['products'][i]['options']+'</b>';
				html +='</div></div></div>';
				}
				
				if(json['loadmore'])
				{
					if(rel==0 || path!=oldpath)
					{
					$('#page').val('2');
					}
					html +="<div class='loadmoreproduct btn btn-primary' rel='"+rel+"' path='"+path+"' >Load more</div>";
					oldpath=path;
					
				}
				 $('.products').html(html);
				
			}
			  $('.breadcrumbs').html();
				  breadcrumbs='';
				  if(json['breadcrumbs'])
				{
					for (z in json['breadcrumbs']) {
					breadcrumbs +='<span class="showsubcate" rel="'+json['breadcrumbs'][z]['category_id']+'" path="'+json['breadcrumbs'][z]['path']+'">'+json['breadcrumbs'][z]['path']+'</span>';
					}
				}
				
				 $('.breadcrumbsload').html(breadcrumbs);
			
		},
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
	});
})

// Loadmore product

$(document).on('click', '.loadmoreproduct',function(){
	rel=$(this).attr('rel');
	path=$(this).attr('path');
	page=$('#page').val();
	
	$.ajax({
		url: 'index.php?route=pos/pos/ajaxloaddata&token=<?php echo $token?>&path='+path+'&category_id='+rel+'&page='+page,
		type: 'post',
		dataType: 'json',
		beforeSend: function() {
			$('.loader').removeClass('hide');
		},
		complete: function() {
			
		},
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.loader').addClass('hide');
		if (json['products']) {
				$('.loader').addClass('hide');	
				$('.loadmoreproduct').remove();	
				html='';
				for (i in json['products']) {
				html +='<div class="col-md-4 col-sm-4 col-xs-12">';
				html +='<div class="box4">';
				stockclass="off";
				if(json['products'][i]['stock']<10)
				{
				stockclass='off1';
				}
				html +='<div class="'+stockclass+'">';
				html +='<span>'+json['products'][i]['stock']+'</span>';
				html +='</div>';
				html +='<div class="image">';
				html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
				html +='</div>';
				html +='<div class="buttons">';
				html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
				html +='<i class="fa fa-info-circle" aria-hidden="true"></i>Info</a>';
				html +='<a class="addtocartquick" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Cart</a>';
				html +='</div>';
				
				html +='<div class="caption"><h1>'+json['products'][i]['name']+'</h1>';
				if(json['products'][i]['price'])
				{
					html +='<p class="price">';
					if(!json['products'][i]['special'])
					{
					html +=json['products'][i]['price']
					} else {
					html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
					}
                  if(json['products'][i]['tax'])
					{
						html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
					}
					html +='</p>';
                }
				html +='<b>'+json['products'][i]['options']+'</b>';
				html +='</div></div></div>';
				}
				
				if(json['loadmore'])
				{
					$('#page').val(parseInt(page)+1);
					html +="<div class='loadmoreproduct btn btn-primary' rel='"+rel+"' path='"+path+"' >Load more</div>";
					oldpath=path;
					
				}
				 $('.products').append(html);
				
			}
		}
	});
});
			
			

$(document).on('click', '.addtocartquick',function(){
	product_id=$(this).attr('rel');
	var data={}
	data['product_id']=product_id;
	
	$.ajax({
		url: 'index.php?route=pos/cart/ajaxloadaddtocart&token=<?php echo $token?>',
		type: 'post',
		data:data,
		dataType: 'json',	
		beforeSend: function() {

		},
		complete: function() {
			$('#help-modal12').modal('hide');
			$('.loader1').removeClass('hide');
		},
			 
		success: function(json) {
			$('.loader1').addClass('hide');
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
		
			if (json['error']) {
				if (json['error']['option']) {
					$('.productinfodata-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw" style="font-size:42x"></i></div>');
					setTimeout(function(){ $("#help-modal12").modal("show"); }, 3000);
					$('.productinfodata-body').load("index.php?route=pos/productinfo&product_id=" + product_id + "&token=<?php echo $token?>");					
				}				
				
			}
			else
			{
			
			if (json['success']) {
			
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
				loadtotal();
			}
			}			
		},	
	});	
})	

//Add To Cart
$(document).on('click', '.addtocart',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/ajaxloadaddtocart&token=<?php echo $token?>',
		type: 'post',
		data: $('#product input[type=\'text\'], #product input[type=\'hidden\'], #product input[type=\'radio\']:checked, #product input[type=\'checkbox\']:checked, #product select, #product textarea'),
		dataType: 'json',	
		beforeSend: function() {
			$('#button-cart').button('loading');
		},
		complete: function() {
			$('#button-cart').button('reset');
			$('.loader1').removeClass('hide');
		},
			 
		success: function(json) {
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			$('.loader1').addClass('hide');
		
			if (json['error']) {
				if (json['error']['option']) {
					for (i in json['error']['option']) {
						var element = $('#input-option' + i.replace('_', '-'));

						if (element.parent().hasClass('input-group')) {
							element.parent().after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						} else {
							element.after('<div class="text-danger">' + json['error']['option'][i] + '</div>');
						}
					}
				}				
				// Highlight any found errors
				$('.text-danger').parent().addClass('has-error');
			}
			
			if (json['success']) {
				$('#help-modal12').modal('hide');
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
				loadtotal();
			}			
		},	
	});	
})	
		
//Update
$(document).on('click', '.update',function() {
	key=$(this).attr('rel');
	rel1=$(this).attr('rel1');
	quantity=$('.quantity'+rel1).val();
	price=$('.price'+rel1).val();
		
		$.ajax({
		url: 'index.php?route=pos/cart/edit&token=<?php echo $token?>',
		type: 'post',
		data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1)+'&price=' +(typeof(price) != 'undefined' ? price :''),
		dataType: 'json',
		beforeSend: function() {
			$('.loader1').removeClass('hide');
		},
		complete: function() {
	
		},			
		success: function(json) {
			$('.loader1').addClass('hide');
			$('.alert, .text-danger').remove();
			
			$('.breadcrumb').after('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			$('html, body').animate({ scrollTop: 0 }, 'slow');

			
			// Need to set timeout otherwise it wont update the total
			setTimeout(function () {
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
				loadtotal();
			}, 100);				
		}
	});	
	
});

//Remove				
$(document).on('click', '.remove',function() {
	key=$(this).attr('rel');
	
	$.ajax({
		url: 'index.php?route=pos/cart/remove&token=<?php echo $token?>',
		type: 'post',
		data: 'key=' + key,
		dataType: 'json',
		beforeSend: function() {
			$('.loader1').removeClass('hide');
		},
		complete: function() {
			
		},				
		
		success: function(json) {
			$('.loader1').addClass('hide');
			$('.alert, .text-danger').remove();
			
			$('.breadcrumb').after('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			$('html, body').animate({ scrollTop: 0 }, 'slow');

			
			// Need to set timeout otherwise it wont update the total
			setTimeout(function () {
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
				loadtotal();
			}, 100);				
		}			
	});
});

//OrderNow
$(document).on('click', '.ordernow',function(){
	$.ajax({
		url: 'index.php?route=pos/paynow/addorder&token=<?php echo $token?>',
		type: 'post',
		data: $('.order-nowdata input[type=\'text\'], .order-nowdata input[type=\'hidden\'], .order-nowdata input[type=\'checkbox\']:checked, .order-nowdata select, .order-nowdata textarea'),
		dataType: 'json',	
		beforeSend: function() {
			$('#pay-now').button('loading');
		},
		complete: function() {
			$('#pay-now').button('reset');
			
		},			 
		success: function(json) {			
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
		
			if (json['error']) {
				$('.modal-header').after('<div class="alert alert-danger col-sm-12"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			
			if (json['success']) {			
				$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
				loadtotal();
				link=json['link'];
				link=link.replace('amp;', '');
				link=link.replace('amp;', '');
				//$('.paynowfinal').load(link);
				$('.paynowfinal').html('<div class="success alert-success" style="margin:10px;padding:10px;font-size:25px"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <a href="'+json['link']+'" target="_blank"><i class="fa fa-print"></i> </a> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
				//$("#help-modal13").modal("show");
				$('#dash').load('index.php?route=pos/dashboardload&token=<?php echo $token?>');
				
			}			
		},	
	});	
})	

// Manual Discount
$(document).on('click', '.manualdiscount',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/manualdiscount&token=<?php echo $token?>',
		type: 'post',
		data: $('#manualdiscount input[type=\'text\'],#manualdiscount select'),
		dataType: 'json',	
		beforeSend: function() {
			$('.manualdiscount').button('loading');
		},
		complete: function() {
			$('.manualdiscount').button('reset');
			
		},			 
		success: function(json) {	
		$('.alert,.text-danger').remove();
			if (json['error']) {
				$('.sub-totals').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			if (json['success']) {
				
				$('.sub-totals').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			
			loadtotal();
		}
	});
});

// Coupon 
$(document).on('click', '.coupondiscount',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/applydiscount&token=<?php echo $token?>',
		type: 'post',
		data: $('#coupondiscount input[type=\'text\']'),
		dataType: 'json',	
		beforeSend: function() {
			$('.coupondiscount').button('loading');
		},
		complete: function() {
			$('.coupondiscount').button('reset');
			
		},	
		
		success: function(json) {
			$('.alert, .text-danger').remove();
			if (json['error']) {
				$('.sub-totals').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			if (json['success']) {
				$('.sub-totals').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}		
			loadtotal();		
		}
	});
});

// Voucher
$(document).on('click', '.voucherdiscount',function(){
	$.ajax({
		url: 'index.php?route=pos/cart/applyvoucher&token=<?php echo $token?>',
		type: 'post',
		data: $('#voucherdiscount input[type=\'text\']'),
		dataType: 'json',	
		beforeSend: function() {
			$('.voucherdiscount').button('loading');
		},
		complete: function() {
			$('.voucherdiscount').button('reset');
			
		},			 
		success: function(json) {
			if (json['error']) {
							$('.sub-totals').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			if (json['success']) {
							$('.sub-totals').before('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}		
			loadtotal();
		}
	});
});


// Order Edit

$(document).on('click', '.orderedit',function(){
	rel=$(this).attr('rel');
	$.ajax({
		url: 'index.php?route=pos/orderlist/edit&token=<?php echo $token?>&order_id='+rel,
		type: 'post',
		dataType: 'json',	
		beforeSend: function() {
			$('.loader1').removeClass('hide');
			
		},
		complete: function() {
			$('#ordermodal').modal('hide');
			$('#help-modal16').modal('hide');
			$('.loader1').addClass('hide');
			
		},			 
		success: function(json) {
			if (json['success']) {
		//	$('.paynow').hide();
			$('.editorder').show();
			$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
			}		
			loadtotal();
		}
	});
});

// Order edit Action

$(document).on('click', '.editorder',function(){
	rel=$(this).attr('rel');
	$.ajax({
		url: 'index.php?route=pos/orderlist/editsave&token=<?php echo $token?>',
		type: 'post',
		data: $('.order-editdata input[type=\'text\'], .order-editdata input[type=\'hidden\'], .order-editdata input[type=\'checkbox\']:checked, .order-editdata select, .order-editdata textarea'),
		dataType: 'json',	
		beforeSend: function() {
			$('.loader1').removeClass('hide');
			$('.editorder').button('loading');
			
		},
		complete: function() {
			$('.loader1').addClass('hide');
			$('.editorder').button('reset');
			
		},			 
		success: function(json) {
			
			$('.alert, .text-danger').remove();
			$('.form-group').removeClass('has-error');
			
			if (json['error']) {
				$('.modal-header').after('<div class="alert alert-danger col-sm-12"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			
			
			if (json['success']) {
			
			$('.paynow').show();
			$('.editorder').hide();
			$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
					
			loadtotal();
				/* link=json['link'];
				link=link.replace('amp;', '');
				link=link.replace('amp;', '');
				$('.paynowfinal').load(link);
				$('.paynowfinal').html('<div class="success alert-success" style="margin:10px;padding:10px;font-size:25px"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <a href="'+json['link']+'" target="_blank"><i class="fa fa-print"></i> </a> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>'); */
				
				$('.paynow-body').html('<div class="success alert-success" style="margin:10px;padding:10px;font-size:25px"><i class="fa fa-exclamation-circle"></i> ' + json['success'] + ' <a href="'+json['link']+'" target="_blank"><i class="fa fa-print"></i> </a> <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
				$("#help-modal13").modal("show");
			}
		}
	});
});


function loadtotal()
{
	$.ajax({
		url: 'index.php?route=pos/cart/loadtotal&token=<?php echo $token?>',
		type: 'post',
		data: '',
		dataType: 'json',	
		beforeSend: function() {
			
		},
		complete: function() {			
		},			 
		success: function(json) {	
			$('.totalload').html()
			html='';
			if(json['totals']) {
				for (z in json['totals']) {
						html +='<li>'+json['totals'][z]['title']+' : '+ json['totals'][z]['text'] +'</li>';
				}
			}
			$('.totalload').html(html);
		}
	});
}
 
//Search Product start
$(document).on('click', '.serchproduct',function(){
	
	$.ajax({
		url: 'index.php?route=pos/pos/ajaxloaddata&token=<?php echo $token?>',
		type: 'post',
		data: $('.search input[type=\'text\']'),
		dataType: 'json',			
		beforeSend: function() {
			$('.loader').removeClass('hide');
			$('.categories').addClass('hide');
			$('.products').html('');
		},
		complete: function() {
		$('.leftside1 input[name=\'filter_product\']').val('');	
		},			 
		success: function(json) {
			
			if (json['products']) {
				$('.loader').addClass('hide');	
				html='';
				for (i in json['products']) {
				html +='<div class="col-md-4 col-sm-4 col-xs-12">';
				html +='<div class="box4">';
				stockclass="off";
				if(json['products'][i]['stock']<10)
				{
				stockclass='off1';
				}
				html +='<div class="'+stockclass+'">';
				html +='<span>'+json['products'][i]['stock']+'</span>';
				html +='</div>';
				html +='<div class="image">';
				html +='<img src="'+json['products'][i]['thumb']+'" alt="'+json['products'][i]['name']+'" title="'+json['products'][i]['name']+'" class="img-responsive" /></a>';
				html +='</div>';
				html +='<div class="buttons">';
				html +='<a name="modal_trigger" class="productinfodata"  rel="'+json['products'][i]['product_id']+'">';
				html +='<i class="fa fa-info-circle" aria-hidden="true"></i>Info</a>';
				html +='<a class="addtocartquick addtocartquick'+json['products'][i]['product_id']+'" rel="'+json['products'][i]['product_id']+'"><i class="fa fa-shopping-cart" aria-hidden="true"></i>Cart</a>';
				html +='</div>';
				
				html +='<div class="caption"><h1>'+json['products'][i]['name']+'</h1>';
				if(json['products'][i]['price'])
				{
					
					html +='<p class="price">';
					if(json['products'][i]['special'])
					{
						html +=' <span class="price-new">'+json['products'][i]['special']+'</span> <span class="price-old">'+json['products'][i]['price']+'</span>';
					
					}else{
					html +=json['products'][i]['price'];
					}
                  if(json['products'][i]['tax']!=false)
					{
						html +='<span class="price-tax">'+json['products'][i]['tax']+'</span>';
					}
					html +='</p>';
                }
				html +='</div></div></div>';
				}
				
				
				
				 $('.products').html(html);		
				$('.addtocartquick'+json['products'][i]['product_id']).trigger('click');
			}				
		},	
	});	
})

$('.search input[name=\'filter_product\']').on('keydown', function(e) {
		if (e.keyCode == 13) {
			$('.leftside1 input[name=\'filter_product\']').parent().find('button').trigger('click');
		}
	});

	//DateTime
	$('.date').datetimepicker({
		pickTime: false
	});

	$('.time').datetimepicker({
		pickDate: false
	});

	$('.datetime').datetimepicker({
		pickDate: true,
		pickTime: true
	});
</script>
<script type="text/javascript">
$(document).on('click', '.productinfodata',function(){
		$('.productinfodata-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw" style="font-size:42x"></i></div>');
		var product_id = $(this).attr("rel");
		
		$('.productinfodata-body').load("index.php?route=pos/productinfo&product_id=" + product_id + "&token=<?php echo $token?>");
		$("#help-modal12").modal("show");
	});	
	
$(document).on('click', '.paynow',function(){
		$('.paynow-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
		
		$('.paynow-body').load("index.php?route=pos/paynow&token=<?php echo $token?>");
		$("#help-modal13").modal("show");
	});		
	
	$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
				loadtotal();
				
// Customer List
$(document).on('click', '.customerlist',function(){
	$('.customerlist-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.customerlist-body').load("index.php?route=pos/customerlist&token=<?php echo $token?>");
	$("#help-modal14").modal("show");
});	





// Customer Form
$(document).on('click', '.customerform',function(){
	$('.customerform-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	
	$('.customerform-body').load("index.php?route=pos/customerform&token=<?php echo $token?>");
	$('#help-modal14').modal('hide');
	$("#help-modal15").modal("hide");
});	

// Pos Product
$(document).on('click', '.posproduct',function(){
	$('.posproduct-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	
	$('.posproduct-body').load("index.php?route=pos/posproduct&token=<?php echo $token?>");
	$("#help-modal17").modal("show");
});	

	
// Order List
$(document).on('click', '.orderlist',function(){
	$('.orderlist-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	
	$('.orderlist-body').load("index.php?route=pos/orderlist&token=<?php echo $token?>");
	$("#help-modal16").modal("show");
});	
		
// Add Customer on Paynow
 $(document).on('click', '.paynowcustomeradd',function(){	
	$('.loadcustomerfrom').load("index.php?route=pos/paynowcustomer&token=<?php echo $token?>");
});	 
	
// Order Print
	$(document).on('click', '.order-id',function(){
		orderid=$('.orderid').val();
		if(orderid=='') {
			$('#form-print').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Order ID Missing<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			return false;
		} else {			
			window.open('index.php?route=pos/printinvoice&token=<?php echo $token?>&order_id='+orderid);
			return false;
			$("#printModal .order-id" ).click(function() {
               $('#printModal').remove();
               $('.modal-backdrop').remove();
            });			
		}
	});			

//	Add Customer
$(document).on('click', '.addcustomer',function(){
	$.ajax({
		url: 'index.php?route=pos/customerform/addcustomer&token=<?php echo $token?>',
		type: 'post',
		data: $('.add-customer input[type=\'text\'], .add-customer input[type=\'hidden\'], .add-customer input[type=\'password\'], .add-customer textarea, .add-customer select'),
		dataType: 'json',	
		beforeSend: function() {
			$('.addcustomer').button('loading');
		},
		complete: function() {
			$('.addcustomer').button('reset');
			
		},			 
		success: function(json) {			
			$('.alert, .alert-danger').remove();
			$('.form-group').removeClass('has-error');
			if (json['error']) {
				for (i = 0; i < json['error'].length; i++) {
					$('.add-customer').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'][i] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				} 
			} 

			/*if (json['error']) {
				$('.add-customer').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}*/
			
			if (json['success']) {
                $('.addcustomer').button('reset');
				$('.customer-Modal').modal('hide');	
				$('#help-modal15').modal('hide');	
				$('#addModal').modal('hide');	
				//$('.loadcustomerfrom').hide();
				$('.customer_id').val(json['customer']['customer_id']);
				$('.customer_name').val(json['customer']['name']);
				$('input[name=firstname]').val('');	
				$('input[name=lastname]').val('');	
				$('input[name=telephone]').val('');	
				$('input[name=address_1]').val('');	
				
				 
			}			
		},	
	});	
})

//	Add Paynow Customer
$(document).on('click', '.addpaynowcustomer',function(){
	$.ajax({
		url: 'index.php?route=pos/paynowcustomer/addpaycustomer&token=<?php echo $token?>',
		type: 'post',
		data: $('.add-nowcustomer input[type=\'text\'], .add-nowcustomer input[type=\'hidden\'], .add-nowcustomer input[type=\'password\'], .add-nowcustomer textarea, .add-nowcustomer select'),
		dataType: 'json',	
		beforeSend: function() {
			$('.addpaynowcustomer').button('loading');
		},
		complete: function() {
			$('.addpaynowcustomer').button('reset');
			
		},			 
		success: function(json) {			
			$('.alert, .alert-danger').remove();
			$('.form-group').removeClass('has-error');
			if (json['error']) {
				for (i = 0; i < json['error'].length; i++) {
					$('.add-nowcustomer').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'][i] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
				} 
			} 

			
			
			if (json['success']) {
				$('.loadcustomerfrom').hide();
				$('.customer_id').val(json['customer']['customer_id']);
				$('.customer_name').val(json['customer']['name']);
			}			
		},	
	});	
})


//	Add Pos Product
$(document).on('click', '.addposproduct',function(){
	$.ajax({
		url: 'index.php?route=pos/posproduct/addposproduct&token=<?php echo $token?>',
		type: 'post',
		data: $('.add-posproduct input[type=\'text\'], .add-posproduct input[type=\'hidden\'], .add-posproduct textarea'),
		dataType: 'json',	
		beforeSend: function() {
			$('.addposproduct').button('loading');
		},
		complete: function() {
			$('.addposproduct').button('reset');
			
		},			 
		success: function(json) {			
			$('.alert, .alert-danger').remove();
			$('.form-group').removeClass('has-error');
		
			if (json['error']) {
				$('.modal-header').after('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
			
			if (json['success']) {
				$('#help-modal17').modal('hide');			
				cproduct_id=json['cproduct_id'];
				var data={}
				data['cproduct_id']=cproduct_id;
				data['quantity']=json['quantity'];
				
				$.ajax({
					url: 'index.php?route=pos/cart/ajaxloadaddtocart&token=<?php echo $token?>',
					type: 'post',
					data:data,
					dataType: 'json',	
					beforeSend: function() {
						$('.loader1').removeClass('hide');
					},
					complete: function() {
						$('.loader1').removeClass('hide');						
					},
						 
					success: function(json) {
						$('.loader1').addClass('hide');
						$('.alert, .text-danger').remove();
						$('.form-group').removeClass('has-error');
						
						if (json['success']) {
						
							$('.loadcartclass').load('index.php?route=pos/cart/loadcart&token=<?php echo $token?>');
							loadtotal();
						}
									
					},	
				});
				
				 
			}			
		},	
	});	
})

// Expend
$('.clickexpend').click(function(){
	rel=$(this).attr('rel');
	if(rel==1)
	{
        $('#column-left').addClass('leftopen');
		$('#header, #footer,.navbar-header,#column-left,#column-left').css({'display':'block'});
		$(this).attr('rel','2');
	}
	else
	{
		$(this).attr('rel','1');
        $('#column-left').removeClass('leftopen');
		$('#header, #footer,.navbar-header,#column-left,#column-left.active').css({'display':'none'});
	}
})
			
</script>
<script type="text/javascript">
	$('.scrollbox3').enscroll({
    showOnHover: false,
    verticalTrackClass: 'track3',
    verticalHandleClass: 'handle3'
});  
	$('.scrollbox4').enscroll({
    showOnHover: false,
    verticalTrackClass: 'track3',
    verticalHandleClass: 'handle3'
});  
</script>
<!--ProductInfo Popup-->
<div class="modal fade" id="help-modal12" role="dialog">
	<div class="productinfodata-body modal-dialog"> </div>
</div>
<!--ProductInfo Popup-->

<!--PayNow Popup-->
<div class="modal fade" id="help-modal13" role="dialog">
	<div class="paynow-body modal-dialog"> </div>
</div>
<!--PayNow Popup-->


<!--Customer List Popup Start-->
<div class="modal fade" id="help-modal14" role="dialog">
	<div class="customerlist-body modal-dialog modal-lg"> </div>
</div>
<!--Customer List Popup-->

<!--Customer Form Popup Start-->
<div class="modal fade" id="help-modal15" role="dialog">
	<div class="customerform-body modal-dialog modal-lg"> </div>
</div>
<!--Customer List Popup-->

<!--Order List Popup Start-->
<div class="modal fade" id="help-modal16" role="dialog">
	<div class="orderlist-body modal-dialog modal-lg"> </div>
</div>
<!--Order List Popup-->

<!--Pos Product Form Popup Start-->
<div class="modal fade" id="help-modal17" role="dialog">
	<div class="posproduct-body modal-dialog modal-sm"> </div>
</div>
<!--Pos Product Form Popup End-->

<!--PrintOrder Popup End-->
<div id="printModal" class="modalCart modal fade" role="dialog">
  <div class="modal-dialog modal-sm">
		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
			  <div class="row">
				<form action="" method="post" enctype="multipart/form-data" id="form-print">
					<div class="form-group">
						 <label class="col-sm-12 control-label" for="input-orderid"><?php echo $entry_orderid; ?></label>
						 <div class="col-sm-12">
							 <input type="text" name="orderid" value="" class="orderid form-control" placeholder="" id="input-orderid"/>
							 <button type="button" class="btn btn-primary order-id"><?php echo $button_print; ?></button>					
						 </div>
					</div>
				 </form>
			  </div>
		  </div>
		</div>
	</div>
</div>
<!--PrintOrder Popup-->


<!--Product Popup start-->
<div id="productModal" class="modalCart modal fade" role="dialog">
  <div class="modal-dialog modal-md">
		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
			  <div class="row">
				<form action="" method="post" enctype="multipart/form-data" id="form-product">
					<div class="form-group row">
						<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
						<div class="col-sm-10">
							<input type="text" name="name" value="" class="form-control" placeholder="" id="input-name"/>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 control-label" for="input-price"><?php echo $entry_price; ?></label>
						<div class="col-sm-10">
							<input type="text" name="price" value="" class="form-control" placeholder="" id="input-price"/>
						</div>
					</div>
					<div class="text-center">
						<button type="button" class="btn btn-primary"><?php echo $button_submit; ?></button>
					</div>
				 </form>
			  </div>
		  </div>
		</div>
	</div>
</div>
<!--Product Popup end-->
<div id="addModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-md">
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
		  </div>
		  <div class="modal-body">
              <?php echo $customerform; ?>
          </div>
        </div>
    </div>
</div>

<script>
$('.orderbox').hide();
	$('.orderbtn').on('click', function(){
		$('.orderbox').show();
		$('.custbox').hide();
		$('.printbox').hide();
	});
	$('.custbox').hide();
	$('.custbtn').on('click', function(){
		$('.custbox').show();
		$('.printbox').hide();
		$('.orderbox').hide();
	});
	$('.printbox').hide();
	$('.printbtn').on('click', function(){
		$('.printbox').show();
		$('.orderbox').hide();
		$('.custbox').hide();
	});
	$('.orderbox .fa-shopping-cart').on('click', function(){
		$('.orderbox').hide();
		$('.custbox').hide();
		$('.printbox').hide();
	});
	$('.custbox .fa-user').on('click', function(){
		$('.orderbox').hide();
		$('.custbox').hide();
		$('.printbox').hide();
	});
	$('.printbox .fa-print').on('click', function(){
		$('.orderbox').hide();
		$('.custbox').hide();
		$('.printbox').hide();
	});	
</script>
<style>
    .pospage #column-left + #content{
        margin-left: 0px;
    }
    .pospage .leftopen + #content{
        margin-left: 50px !important;
    }
    
</style>
<?php echo $footer; ?>

