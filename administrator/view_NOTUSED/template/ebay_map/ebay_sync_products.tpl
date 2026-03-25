<form name="myForm" action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ebay_sync_products">
  <div class="form-group">
    <label class="control-label" for="input-name"><?php echo $entry_source_product; ?></label>
    <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" name="input-name" id="input-name" class="form-control" />
    <input type="hidden" name="product_id" value="" data-price="" data-quantity="" data-account-id=""/>
  </div>

  <div id="tab-stores">
    <div class="table-responsive">
      <table id="stores" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <td class="text-left"><?php echo $entry_mappedAccountId; ?></td>
            <td class="text-left"><?php echo $entry_destination_product; ?></td>
            <td class="text-left"><span data-original-title="<?php echo $entry_price_quantity_help; ?>"><?php echo $entry_price_quantity; ?></span></td>
          </tr>
        </thead>
        <tbody>
            <?php $attribute_row = 0 ?>
            <?php foreach($product_attributes as $product_attribute) { ?>
            <tr id="store-row<?php echo $attribute_row; ?>">
              <td class="text-left" style="width: 40%;">
                <input type="text" name="store_name["' + store_id + '"]" value="<?php echo $store_id; ?>" placeholder="<?php echo $store_name; ?>" class="form-control" />
              </td>
              <td class="text-left">
                <input type="text" name="store_product[]" value="<?php echo $product_id; ?>" data-id="' + product_id + '" placeholder="<?php echo $text_select; ?>" class="form-control" />
              </td>
              <td class="text-right"><button type="button" onclick="$('#store-row<?php echo $attribute_row; ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
              </td>
            </tr>
            <?php $attribute_row = $attribute_row + 1 ?>
          <?php } ?>
          </tbody>
        <tfoot>
          <tr>
            <td colspan="2"><?php echo $price_quantity_source; ?></td>
            <td class="text-center">
              <input type="radio" name="price_quantity" value="0" class="form-control" checked="checked"/>
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</form>
<script type="text/javascript"><!--


$(document).ready(function (e){
    let get_Token = '<?php echo $token ?>';
    let attribute_row = <?php echo $attribute_row ?>;

    let footer = (json) => {
      let html = '<td style="padding: 10px;" colspan="3">' +
        '<div class="col-sm-6 text-left">' +
          json['pagination'] +
        '</div>' +
        '<div class="col-sm-6 text-right">' +
          json['results'] +
        '</div>' +
      '</td>';
      $('#tab-all-data tfoot').html(html);
    }

    function createRow(page, url = '', button_press_id = 'button-save') {
      $.ajax({
        url: 'index.php?route=ebay_map/ebay_sync_products/getAllSyncProductsNow&token='+ get_Token + url,
        type: 'post',
        data: {
          'page': page
        },
        beforeSend: function() {
          if($('.alert-success').length) {
            $('.alert-success').slideUp(5000);
          }
          $('.alert-danger').remove();
          $('.block_div').css('display','block');
          $('#' + button_press_id).button('loading');
        },
        complete: function() {
          $('.block_div').css('display','none');
          $('#' + button_press_id).button('reset');
        },
        success: function(json) {
          addRow(json);
          footer(json);
        }
      });
    }
    if(!page) {
      page = 1;
    }
    createRow(page);

    $(document).on('click', '.editSyncProducts', function() {
      let url = link;

      if($(this).data('id')) {
        url += '&source_product_id=' + $(this).data('id');
      }
      location = url;
    })

    function addRow(json) {
      let html = '';

      let returnTrue = false;
      // console.log(attribute_row , 'attribute_row');
      $.map(json, function (value, key) {
        if(key == 'product_names' | key == 'pagination') {
          returnTrue = true;
        }
        if(returnTrue) {
          return true;
        }

        html += '<tr id="store-row' + attribute_row + '">' +
          '<td class="text-left">' +
            '<input type="checkbox" name="selected[]" value="' + value['source_product'] + '" />' +
          '</td>' +
          '<td class="text-left">' +
            '<span name="source_product" data-id="' + value['source_product'] + '">' + json['product_names'][value['source_product']] + '</span>' +
          '</td>' +
          '<td class="text-left">';
        let destination_products;
        let string = value['destination_products'];
        if (string.indexOf(',') > -1) {
          destination_products = string.split(',');
          $.map(destination_products, function (val){
            console.log(val);
            html += '<span name="destination_product[]" data-id="' + val + '"><i class="fa fa-check"></i> ' + json['product_names'][val] + '</span><br/>';
          })
        } else {
          destination_products = string;
          html += '<span name="destination_product[]" data-id="' + destination_products + '"><i class="fa fa-check"></i> ' + json['product_names'][destination_products] + '</span>';
        }
        html += '</td>' +
            '<td class="text-right"><button type="button" data-id="' + value['source_product'] + '" data-toggle="tooltip" title="<?php echo $button_edit ?>" class="btn btn-primary editSyncProducts"><i class="fa fa-pencil"></i></button>' +
          '</td>' +
        '</tr>';
        attribute_row++;
      })

      $('#tab-all-data tbody').html(html);
    }

    function filter_map_product() {
      url = '';

      var filter_oc_source_product_id = $('input[name=\'filter_oc_source_product_id\']').val();

      if (filter_oc_source_product_id) {
        url += '&filter_oc_source_product_id=' + encodeURIComponent(filter_oc_source_product_id);
      }

      var filter_oc_destination_product_id = $('input[name=\'filter_oc_destination_product_id\']').val();

      if (filter_oc_destination_product_id) {
        url += '&filter_oc_destination_product_id=' + encodeURIComponent(filter_oc_destination_product_id);
      }

      var filter_ebay_source_product_id = $('input[name=\'filter_ebay_source_product_id\']').val();

      if (filter_ebay_source_product_id) {
        url += '&filter_ebay_source_product_id=' + encodeURIComponent(filter_ebay_source_product_id);
      }

      var filter_ebay_destination_product_id = $('input[name=\'filter_ebay_destination_product_id\']').val();

      if (filter_ebay_destination_product_id) {
        url += '&filter_ebay_destination_product_id=' + encodeURIComponent(filter_ebay_destination_product_id);
      }

      var filter_oc_product_name = $('input[name=\'filter_oc_product_name\']').val();

      if (filter_oc_product_name) {
        url += '&filter_oc_product_name=' + encodeURIComponent(filter_oc_product_name);
      }

      createRow(page, url, button_press_id = 'filter_search');
      return;
    }

    $('.filter_search').on('click', function() {
      filter_map_product();
    })

    $('input[name=\'filter_oc_product_name\']').autocomplete({
      delay: 0,
      source: function(request, response) {
        $.ajax({
          url: 'index.php?route=ebay_map/ebay_map_product/autocomplete&token=<?php echo $token ?>&filter_oc_product_name=' + encodeURIComponent(request),
          dataType: 'json',
          success: function(json) {
            response($.map(json, function(item) {
              return {
                label: item.name,
                value: item.item_id
              }
            }));
          }
        });
      },
      select: function(item) {
        $('input[name=\'filter_oc_product_name\']').val(item.label);
        return false;
      },
      focus: function(item) {
          return false;
      }
    });

  $("#button-save").on('click', function (){
    let get_Token = '<?php echo $token; ?>';
    let formElement = document.querySelector("#form-ebay_sync_products");
    var data = new FormData(formElement);
    let error = validate(data);
    if( error.length ){
      let html = '';
      $.map(error, function (val){
        html += '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>  '+ val +'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
      })
      $('#form-ebay_sync_products').before(html);
      return;
    }

    $.ajax({
      url: 'index.php?route=ebay_map/ebay_sync_products/setMap&token='+get_Token,
      type: 'post',
      data: data,
        contentType: false,
        cache: false,
        processData:false,
      beforeSend: function() {
        $('.alert').remove();
        $('.block_div').css('display','block');
        $('#button-save').button('loading');
      },
      complete: function() {
        $('.block_div').css('display','none');
        $('#button-save').button('reset');
      },
      success: function(json) {

        createRow(1);

        let html = '',
        error = [],
        success = [],
        successBool = false,
        product_name = '';
        if( json['error'] ){
          error.push(json['error']);
        } else {
          $.map(json['variations'], (val, i)=> {
            if(typeof(val.ebayApiResponse) == 'undefined') {
              if(val.error) {
                product_name = val.product_name;
                error.push(' Product Name: ' + product_name + ' ' + ' <br /> Error: ' + val.message);
              }
            } else {
              if( val['ebayApiResponse']['error']) {
                $.map(json['store_product'],(value,i) => {
                  if( val['product_id'] == value ) {
                    product_name = $('[name="store_product[' + i + ']"]').data('name');
                    error.push(product_name + ' ' + val['ebayApiResponse']['message']);
                  }
                });
              } else {
                $.map(json['store_product'],(value,i) => {
                  if( val['product_id'] == value ) {
                    product_name = $('[name="store_product[' + i + ']"]').data('name');
                    success.push(product_name + ' ' + val['ebayApiResponse']['message']);
                  }
                });
              }
            }    
          });
        }
  
        if( error.length ) {
          json['success'] = false;
          $.map(error, (val) => {
            html += '<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> '+ val +'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
          });
        } else {
           $.map(json['variations'], (val, i)=> {
             if(val && val['oc_product']) {
              success.push(json.filter_name + ' Product changed successfully');
             }           
           })
        }

        if( success.length ) {
          $.map(success, (val) => {
            html += '<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i>  '+ val +'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
          });
        }

        if(json['success'] && successBool) {
          html = '<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i>  '+ json['ebayApiResponse']['message'] +'<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
        }
    
        $('#form-ebay_sync_products').before(html);
        if(json['redirect']){
          window.location.href = json['redirect'];
        }
        
      },
    })
  });

  let globals = {
    bool: false,
    ids: []
  };

  if( source_product_id ) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_sync_products/getSingleSyncProduct&token=<?php echo $token; ?>&source_product_id=' +  encodeURIComponent(source_product_id),
      dataType: 'json',
      success: function(json) {
        console.log(json, 'json');
        let item = [];
        item['label'] = json['product']['name'];
        item['value'] = json['product']['product_id'];
        item['price'] = json['product']['price'];
        item['quantity'] = json['product']['quantity'];
        selectFilterName(item);
        let stringIds = json[0]['destination_products'];
        let ids = [];
        let item2 = [];
        globals.stringIds = json[0]['destination_products'];
        globals.json = json;

        if (stringIds.indexOf(',') > -1) {
          ids = stringIds.split(',');

        } else {
          ids.push(json[0]['destination_products']);
        }
        globals.ids = ids;
        globals.bool = true;
      }
    });
  }

  $('input[name=\'filter_name\']').autocomplete({
  	'source': function(request, response) {
  		$.ajax({
  			url: 'index.php?route=catalog/product/autocomplete&ebaySyncStatus=1&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
  			dataType: 'json',
  			success: function(json) {
  				response($.map(json, function(item) {
  					return {
  						label: item['name'],
  						value: item['product_id'],
  						price: item['price'],
  						quantity: item['quantity']
  					}
  				}));
  			}
  		});
  	},
  	'select': function(item) {
      selectFilterName(item);
  	}
  });

  let selectFilterName = function(item) {
    $('.alert-info').remove();
    $('input[name=\'filter_name\']').val(item['label']);
    $('[name="product_id"]').val(item['value']).data("price", item['price']).attr("data-price", item['price']).data("quantity", item['quantity']).attr("data-quantity", item['quantity']);
    checkIsMap(item['value']);
  }

  $('body').on('focus', 'input[id^=\'store_product\']', function(){
    let account_id = $(this).data('store-id');
    $(this).autocomplete(storeProductAutocomplete($(this), account_id)).on('blur', function(){});
  });


  let storeProductAutocomplete = (that, account_id) => {
    $(that).autocomplete({
      delay: 0,
      source: function(request, response) {
        let str = $(that)['context']['id'];

        if( !parseInt(account_id) ) {
          return;
        }

        $.ajax({
          url: 'index.php?route=ebay_map/ebay_sync_products/autocomplete&token=<?php echo $token; ?>&account_id=' + account_id + '&filter_oc_product_name=' +  encodeURIComponent(request),
          dataType: 'json',
          success: function(json) {
            response($.map(json, function(item) {
              return {
                label: item.name,
                value: item.item_id
              }
            }));
          }
        });
      },
      select: function(item) {
        selectStoreProductAutocomplete(that, item);
        return false;
      },
      focus: function(item) {
          return false;
      }
    });
  }

  function selectStoreProductAutocomplete(that, item) {
    console.log(that, 'that', item);
    $(that).data('id', item['value']).attr('data-id', item['value']).val(item['label']);
    $(that).parent().children("[type='hidden']").val(item['value']).data('name', item['label']).attr('data-name', item['label']);
    $('[name="to_map_product_id"]').val(item['value']);
  }

  $('[name="filter_name"]').on('change', function (){
    let html = "<option value=\"0\"><?php echo $text_select; ?></option>";

    $(this).html(html);
    $('.alert-info').remove();
    $('#stores tfoot tr').hide();
    $('[name=\'product_id\']').val('').data('quantity', '').data('price', '').attr('data-quantity', '').attr('data-price', '');
    $('[name=\'filter_oc_product_name\']').val('');
    $('#account_ebay_sync_products #stores > tbody').empty();
  });

  $('[name="filter_mappedAccountId"]').on('change', function (e){
    e.preventDefault();

    $('[name="selected_filter_mappedAccountId"]').val($(this).val());
    $('[name=\'filter_oc_product_name\']').val('');
    $('input[name=\'to_map_product_id\']').val('');
  });

  let checkIsMap = function (product_id) {
  		let get_Token = '<?php echo $token; ?>';
      let get_account_id = 0;
       $.ajax({
          url: 'index.php?route=ebay_map/ebay_sync_products/getMap&token='+get_Token,
          type: 'post',
          dataType: 'json',
        	data: {'account_id': get_account_id, 'product_id': product_id},
          beforeSend: function() {

          },
          complete: function() {

          },
        	success: function(json) {
            console.log(json, 'map');
            if(json['success']){
              $('.alert-info').remove();
              let html = '<div class="alert alert-info" style="margin-top:5px"><i class="fa fa-info-circle"></i> '+  'This is a product of '+ json['store_name'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>';
              $('[name="product_id"]').before(html);
              $('[name="product_id"]').data('account-id', json['account_id']).attr('data-account-id', json['account_id']);
              setStores(json);
            }

            if(json['redirect']){
              window.location.href = json['redirect'];
            }
        	},
        }); //ajax end
  }

  let setStores = (json) => {
    let html = '';
    $.map(json['mappedAccountIds'], function (val){
      html += '<tr>' +
        '<td class="text-left" style="width: 40%;">' +
          '<input type="text" name="store_name[' + val['id'] + ']" value="' + val['ebay_connector_store_name'] + '" placeholder="' + val['ebay_connector_store_name'] + '" class="form-control" readonly/>' +
       '</td>' +
       '<td class="text-left">' +
        '<input type="text" value="" data-store-id=' + val['id'] + ' data-id="" placeholder="<?php echo $text_select; ?>" class="form-control" id="store_product[' + val['id'] + ']" />' +
        '<input type="hidden" name="store_product[' + val['id'] + ']" value="" data-name=""/>' +
       '</td>' +
       '<td class="text-center">' +
          '<input type="radio" name="price_quantity" value=' + val['id'] + ' class="form-control" />' +
       '</td>' +
      '</tr>';
    });

    $('#account_ebay_sync_products #tab-stores tbody').html(html);
    $('#account_ebay_sync_products #stores tfoot tr').show();

    // only for editing part
    if(globals.bool) {
      let item2 = [];
      if(globals.ids) {

      }
      $.map(globals.ids, function(val, key) {
        k = key + 1;
        let that = $("#tab-stores tbody tr:nth-child(" + k + ") td:nth-child(2) input");

        item2['label'] = globals.json['product_names'][val];
        item2['value'] = val;

        selectStoreProductAutocomplete(that, item2);
      });
    }
  }

  let validate = (data) => {
    let error = [];

    $('.alert-danger').remove();
    $('input[name^=\'store_product\']').parent().removeClass('has-error');
    $('[name="product_id"]').parent().removeClass('has-error');

    if( !$('[name="product_id"]').val() ){
      $('[name="product_id"]').parent().addClass('has-error');
      error.push("Please Select a valid Source Product");
    }

    $.map($('input[name^=\'store_product\']'), (val) => {
      if( !val['value'] ) {
        $(val).parent().addClass('has-error');
        error.push("Please Select the Destination Product");
      }
    });

    return error;
  }
});
//--></script>
