
$(document).ready(function() {
	/**
	 * [toggle the new category map]
	 * @param  {[type]} ){		var getDisplay    [description]
	 * @return {[type]}          [description]
	 */
	$('body').on('click', '#show_new_map_category', function(){
    $('.alert').remove();
		var getDisplay = $('#new_category_map_section').toggle('slow');
	})

	/**
	 * [validate and save the both category form]
	 * @param  {[type]} ){	} [click event]
	 * @return {[type]}        [new categoty map form selector]
	 */
	$('body').on('click', '#wk_cat_map_save', function(e){
		e.preventDefault();
		var get_Token = $(this).data('token');
    var get_account_id = $(this).data('account-id');
    var data = new FormData($('#form-category-save')[0]);

     $.ajax({
            url: 'index.php?route=ebay_map/ebay_map_category/validateCategoryForm&token='+get_Token+'&account_id='+get_account_id,
          	type: 'post',
          	data: data,
            	contentType: false,
            	cache: false,
            	processData:false,
            beforeSend: function() {
            	$('.alert').remove();
              $('.block_div').css('display','block');
            },
            complete: function() {
              $('.block_div').css('display','none');
            },
          	success: function(json) {
          		if(json['warning']){
          			html = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  '+ json['warning']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
          			$('#new_category_map_section').before(html);
          			
          			if(json['error_select_opencart_category']){
          				html_oc = '<div class="alert alert-danger" style="margin-top:3px;"><i class="fa fa-exclamation-circle"></i>  '+ json['error_select_opencart_category']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                  $('#form-category-save #map_opencart_category > select:last').css('border-color','#EA5744');
          				$('#form-category-save #map_opencart_category').append(html_oc);
          			}
          			if(json['error_select_ebay_category']){
          				html_ebay = '<div class="alert alert-danger" style="margin-top:3px;"><i class="fa fa-exclamation-circle"></i>  '+ json['error_select_ebay_category']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                  $('#form-category-save #map_ebay_category > select:last').css('border-color','#EA5744');
          				$('#form-category-save #map_ebay_category').append(html_ebay);
          			}
          		}
              if(json['success']){
                $('#show_new_map_category').trigger('click');
                html = '<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i>  '+ json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                // $('#category_map_list_section').prepend(html);
              }

              if(json['redirect']){
                window.location.href = json['redirect'];
              }
          	},
          });//ajax end
	})


    $('body').on('click', '#map_category_delete', function(e){
      e.preventDefault();
      var get_Token     = $(this).data('token');
      var get_account_id= $(this).data('account-id');
      var data          = new FormData($('#form-category-delete')[0]);
      var status_category_delete = false;
        $("#form-category-delete input[type=checkbox]:checked").each(function(key, val){
          if($(val).val()){
            status_category_delete = true;
          }
        })
        
        if(status_category_delete == true){
          $.ajax({
            url: 'index.php?route=ebay_map/ebay_map_category/deleteMapCategory&token='+get_Token+'&account_id='+get_account_id,
            type: 'post',
            data: data,
              contentType: false,
              cache: false,
              processData:false,
            beforeSend: function() {
              $('.alert').remove();
              $('.block_div').css('display','block');
            },
            complete: function() {
              $('.block_div').css('display','none');
            },
            success: function(json) {
              if(json['error_permission']){
                html = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  '+ json['error_permission']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                $('#new_category_map_section').before(html);
              }
              
              if(json['success']){
                // $('#show_new_map_category').trigger('click');
                html = '<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i>  '+ json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                // $('#category_map_list_section').prepend(html);
              }
              if(json['redirect']){
                window.location.href = json['redirect'];
              }

            },
          })
        }else{
          alert('Warning: You have to select atleast one record to delete!');
        }
    })


    /**
     * product import from ebay to opencart
     */
    

    $('body').on('click', '#delete-import-product', function(e){
      e.preventDefault();
      var get_Token       = $(this).data('token');
      var get_account_id  = $(this).data('account-id');
      var data            = new FormData($('#form-product-delete')[0]);
      var status_product_delete = false;
        $("#form-product-delete input[type=checkbox]:checked").each(function(key, val){
          if($(val).val()){
            status_product_delete = true;
          }
        })
        if(status_product_delete == true){
          $.ajax({
            url: 'index.php?route=ebay_map/ebay_map_product/deleteMapProduct&token='+get_Token+'&account_id='+get_account_id,
            type: 'post',
            data: data,
              contentType: false,
              cache: false,
              processData:false,
            beforeSend: function() {
              $('.alert').remove();
              $('.block_div').css('display','block');
            },
            complete: function() {
              $('.block_div').css('display','none');
            },
            success: function(json) {
              if(json['error_permission']){
                html = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  '+ json['error_permission']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                $('#product_import_list_section').prepend(html);
              }
              
              if(json['success']){
                html = '<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i>  '+ json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                // $('#product_import_list_section').prepend(html);
              }
              if(json['redirect']){
                window.location.href = json['redirect'];
              }
            },
          })
        }else{
          alert('Warning: You have to select atleast one record to delete!');
        }
    })

     $('body').on('click', '#delete-import-order', function(e){
      e.preventDefault();
      var get_Token = $(this).data('token');
      var get_account_id  = $(this).data('account');
      var data      = new FormData($('#form-order-delete')[0]);
      var status_delete = false;
        $("#form-order-delete input[type=checkbox]:checked").each(function(key, val){
          if($(val).val()){
            status_delete = true;
          }
        })
        if(status_delete == true){
          $.ajax({
            url: 'index.php?route=ebay_map/ebay_map_order/deleteMapOrder&token='+get_Token+'&account_id='+get_account_id,
            type: 'post',
            data: data,
              contentType: false,
              cache: false,
              processData:false,
            beforeSend: function() {
              $('.alert').remove();
              $('.block_div').css('display','block');
            },
            complete: function() {
              $('.block_div').css('display','none');
            },
            success: function(json) {
              if(json['error_permission']){
                html = '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  '+ json['error_permission']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                $('#order_import_list_section').prepend(html);
              }
              
              if(json['success']){
                html = '<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i>  '+ json['success']+'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
                // $('#order_import_list_section').prepend(html);
              }
              if(json['redirect']){
                window.location.href = json['redirect'];
              }
            },
          })
        }else{
          alert('Warning: You have to select atleast one record to delete!');
        }
    })
  

  /**
   * [getURL to open current filter tab]
   * @type {[type]}
   */
  var getURL = window.location.search.substring(1);
  var getARGU = getURL.split("&");
  
  for (var i=0;i<getARGU.length;i++) {
    var getSTATUS = getARGU[i].split("=");

    if(getSTATUS[0] && getSTATUS[0] == 'status'){
      $('#accordion_ebay li').removeClass('active');
      $('#ebay_right_link .tab-pane').removeClass('active');
      $('#accordion_ebay li > a').each(function(key, val){
        var getHRF = $(val).attr('href');
        if(getHRF == '#'+getSTATUS[1]){
          $(val).parent().addClass('active');
        }
      })
      $('#ebay_right_link .tab-pane').each(function(key, val){
        var getID = $(val).attr('id');
        if(getID == getSTATUS[1]){
          $(val).addClass('active');
        }
      })
    }
  } 
  
});