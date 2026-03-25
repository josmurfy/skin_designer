<div id="content">
<link href="view/stylesheet/csspin.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .cp-round::before, .cp-round::after{
    width: 35px;
    left:8px;
    height: 35px;
    /*top: 25px;*/
    margin-top: 25px;
  }
  .btn-success{
    background-color:#6ABD6A;
    color:#FFF;
    border-style: solid;
    border-width: 1px;
    border-color: #6ABD6A;
    border-bottom-width: 3px;
  }
  .btn-success:hover{
    background-color:#e6e6e6;
    color:#333;
    border-style: solid;
    border-width: 1px;
    border-color: #adadad;
    border-bottom-width: 3px;
  }
</style>
<div class="page-header">
  <div class="container-fluid">
    <h3><?php echo $heading_title; ?></h3>
    <div class="pull-right" style="margin-bottom: 10px;">
      <button type="button" id="import-ebay-product" data-toggle="tooltip" title="<?php echo $help_import_ebay_product; ?>" class="btn btn-success"><i class="fa fa-download" aria-hidden="true"></i> <?php echo $button_import_ebay_product; ?></button>

      <button type="button" id="import-ebay-single-product" data-toggle="modal" data-target="#import_ebay_single_product" title="<?php echo $help_import_ebay_single_product; ?>" class="btn btn-warning"><i class="fa fa-hand-o-right" aria-hidden="true"></i> <?php echo $button_import_ebay_single_product; ?></button>

      <button type="button" class="btn btn-info" title="<?php echo $help_show_import_product_result; ?>" data-toggle="modal" data-target="#import_ebay_product">
        <i class="fa fa-info-circle" aria-hidden="true"></i> <?php echo "Show Result"; ?>
      </button>

      <button id="delete-import-product" type="button" data-toggle="tooltip" data-token="<?php echo $token; ?>" data-account-id="<?php echo $account_id; ?>" title="<?php echo $button_delete; ?>" class="btn btn-danger" ><i class="fa fa-trash-o"></i></button>
    </div>
  </div>
</div>

  <!-- Modal-show-result -->
  <div class="modal fade" id="import_ebay_product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><?php echo $text_syn_result; ?></h4>
        </div>
        <div class="modal-body" id="product_sync_result" style="overflow-y: scroll;max-height: 350px">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal-single-product-import-->
  <div class="modal fade" id="import_ebay_single_product" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><?php echo $text_import_single; ?></h4>
        </div>
        <div class="modal-body" id="single_product_sync_result" style="overflow-y: scroll;max-height: 350px">
              <div class="form-horizontal">
                <div class="form-group">
                  <label class="col-sm-3 control-label" for="input-ebay-item-separator"><span data-toggle="tooltip" title="<?php echo $help_ebay_import_item_separator; ?>"><?php echo $entry_ebay_import_item_separator; ?></span></label>
                  <div class="col-sm-9">
                    <select class="form-control" name="ebay_import_item_separator" id="input-ebay-item-separator">
                      <option value=""> -- Select Separator -- </option>
                      <option value=";">Semicolon ( ; )</option>
                      <option value=":">Colon ( : )</option>
                      <option value=",">Comma ( , )</option>
                      <option value="|">Vertical Bar ( | )</option>
                    </select>
                  </div>
                </div>
                <div class="form-group required">
                  <label class="col-sm-3 control-label" for="input-ebay-item-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_import_itemId; ?>"><?php echo $entry_ebay_import_itemId; ?></span></label>
                  <div class="col-sm-9">
                    <input type="hidden" name="ebay_account_id" value="<?php echo $account_id; ?>" />
                    <input type="text" class="form-control" name="ebay_import_itemId" id="input-ebay-item-id" value="<?php if(isset($ebay_import_itemId)){ echo $ebay_import_itemId; } ?>" placeholder="<?php echo $placeholder_item_id; ?>" />
                    <div id="error_ebay_import_itemId"></div>
                  </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="import_single_item"><?php echo $button_single_product_import; ?></button>
          <button type="button" class="btn btn-default" id="close_model_single_product">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid" id="product_import_list_section">

    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"> <i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading"  style="display:inline-block;width:100%;">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_product_list; ?></h3>

      </div>
      <div class="panel-body">
        <div class="col-sm-12 form-horizontal text-right">
         <div class="col-sm-12 form-group">
           <label class="col-sm-2 control-label"><?php echo $text_processing; ?></label>
           <div class="col-sm-10" style="margin-top:10px">
             <div class="progress">
               <div id="progress-bar-product" class="progress-bar" style="width: 0%;"></div>
             </div>
             <div id="progress-text-product"></div>
           </div>
         </div>
       </div>

       <div class="well">
         <div class="row">
           <div class="col-sm-4">
             <div class="form-group">
               <label class="control-label" for="input-oc-product-id"><?php echo $column_oc_product_id; ?></label>
                 <input type="text" name="filter_oc_product_id" value="<?php echo $filter_oc_product_id; ?>" placeholder="<?php echo $column_oc_product_id; ?>" id="input-oc-product-id" class="form-control"/>
             </div>

             <div class="form-group">
               <label class="control-label" for="input-ebay-product-id"><?php echo $column_ebay_product_id; ?></label>
                 <input type="text" name="filter_ebay_product_id" value="<?php echo $filter_ebay_product_id; ?>" placeholder="<?php echo $column_ebay_product_id; ?>" id="input-ebay-product-id" class="form-control"/>
             </div>
           </div>

           <div class="col-sm-4">
               <div class="form-group">
                 <label class="control-label" for="input-oc-product-name"><?php echo $column_product_name; ?></label>
                 <div class='input-group'>
                   <input type="text" name="filter_oc_product_name" value="<?php echo $filter_oc_product_name; ?>" placeholder="<?php echo $column_product_name; ?>" id="input-oc-product-name" class="form-control"/>
                   <span class="input-group-addon">
                     <span class="fa fa-angle-double-down"></span>
                   </span>
                 </div>
               </div>

               <div class="form-group">
                 <label class="control-label" for="input-oc-category-name"><?php echo $column_category_name; ?></label>
                 <div class='input-group'>
                   <input type="text" name="filter_category_name" value="<?php echo $filter_category_name; ?>" placeholder="<?php echo $column_category_name; ?>" id="input-oc-category-name" class="form-control"/>
                   <span class="input-group-addon">
                     <span class="fa fa-angle-double-down"></span>
                   </span>
                 </div>
               </div>
           </div>

           <div class="col-sm-4">
             <div class="form-group">
               <label class="control-label" for="input-sync-source"><?php echo $column_ebay_source; ?></label>

               <select name="filter_source_sync" class="form-control">
                 <option value="*"><?php echo $entry_sync_source; ?></option>
                   <option value="Ebay Item" <?php if(isset($filter_source_sync) && $filter_source_sync == 'Ebay Item'){ echo 'selected'; } ?> >Ebay Item</option>
                   <option value="Opencart Product" <?php if(isset($filter_source_sync) && $filter_source_sync == 'Opencart Product'){ echo 'selected'; } ?> >Opencart Product</option>
               </select>
             </div>

             <div style="margin-top:38px;">
               <button type="button" onclick="filter_map_product();" class="btn btn-primary" style="border-radius:0px;">
                 <i class="fa fa-search"></i><?php echo $button_filter_product; ?></button>
               <a href="<?php echo $clear_product_filter; ?>" class="btn btn-default pull-right" style="border-radius:0px;"><i class="fa fa-eraser" aria-hidden="true"></i><?php echo $button_clear_product; ?></a>
             </div>
           </div>

         </div>
       </div>

        <form method="post" enctype="multipart/form-data" id="form-product-delete" style="clear:both;">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php echo $column_map_id; ?></td>
                  <td class="text-left"><?php echo $column_oc_product_id; ?></td>
                  <td class="text-left"><?php echo $column_product_name; ?></td>
                  <td class="text-left"><?php echo $column_ebay_product_id; ?></td>
                  <td class="text-left"><?php echo $column_category_name; ?></td>
                  <td class="text-left"><?php echo $column_ebay_source; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($import_products) { ?>
                <?php foreach ($import_products as $ebay_product) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($ebay_product['map_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ebay_product['map_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ebay_product['map_id']; ?>" />
                    <?php } ?></td>

                  <td class="text-left"><?php echo $ebay_product['map_id']; ?></td>
                  <td class="text-left"><?php echo $ebay_product['oc_product_id']; ?></td>
                  <td class="text-left"><?php echo $ebay_product['product_name']; ?></td>
                  <td class="text-left text-info"><?php echo $ebay_product['ebay_product_id']; ?></td>
                  <td class="text-left"><?php echo $ebay_product['category_name']; ?></td>
                  <td class="text-left"><?php echo $ebay_product['sync_source']; ?></td>
                </tr>
                <?php } ?>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </form>
        <div class="row">
          <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
          <div class="col-sm-6 text-right"><?php echo $results; ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript"><!--
function filter_map_product() {
	url = 'index.php?route=ebay_map/ebay_account/edit&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&status=account_product_map';

  var filter_oc_product_id = $('input[name=\'filter_oc_product_id\']').val();

  if (filter_oc_product_id) {
    url += '&filter_oc_product_id=' + encodeURIComponent(filter_oc_product_id);
  }

	var filter_oc_product_name = $('input[name=\'filter_oc_product_name\']').val();

	if (filter_oc_product_name) {
		url += '&filter_oc_product_name=' + encodeURIComponent(filter_oc_product_name);
	}

  var filter_ebay_product_id = $('input[name=\'filter_ebay_product_id\']').val();

  if (filter_ebay_product_id) {
    url += '&filter_ebay_product_id=' + encodeURIComponent(filter_ebay_product_id);
  }

	var filter_category_name = $('input[name=\'filter_category_name\']').val();

	if (filter_category_name) {
		url += '&filter_category_name=' + encodeURIComponent(filter_category_name);
	}

  var filter_source_sync = $('select[name=\'filter_source_sync\']').val();

	if (filter_source_sync != '*') {
		url += '&filter_source_sync=' + encodeURIComponent(filter_source_sync);
	}

	location = url;
}

$('input[name=\'filter_category_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_map_product/autocomplete&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&filter_category_name=' +  encodeURIComponent(request),
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
    $('input[name=\'filter_category_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

$('input[name=\'filter_oc_product_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_map_product/autocomplete&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&filter_oc_product_name=' +  encodeURIComponent(request),
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

//--></script>
<script type="text/javascript">
var requests    = []; var totalImportedProduct = 0; var total = 0;
var start_page  = 1;
var success_alert_check = false;
$('#import-ebay-product').on('click', function(e){
    e.preventDefault();
    if (typeof timer != 'undefined') {
      clearInterval(timer);
    }
    timer = setInterval(function() {
            clearInterval(timer);
    // Reset everything
    $('.alert').remove();
    $('#progress-bar-product').css('width', '0%');
    $('#progress-bar-product').removeClass('progress-bar-danger progress-bar-success');
    $('#progress-text-product').html('<div class="text-info text-left"><?php echo $text_currently_sync; ?></div>');

        $.ajax({
            url     : 'index.php?route=ebay_map/ebay_map_product/getTotalProductPages&token=<?php echo $token; ?>',
            type    :   "POST",
            dataType:   "json",
            data    : {
                        'account_id' : '<?php echo $account_id; ?>',
                        'page' : start_page
                      },
            beforeSend: function() {
              $('.block_div').css('display','block');
              $('.container-fluid > .alert').remove();
              $('#single_product_sync_result > .alert').remove();
            },
            complete:function() {
                NextStep();
            },
            success: function(jsonEbayPro) {
                if (jsonEbayPro.error_failed) {
                    $('#progress-bar-product').addClass('progress-bar-danger');
                    $('#progress-text-product').html('<div class="text-danger">' + jsonEbayPro.error_failed + '</div>');
                    $('#product_sync_result').append('<div class="alert alert-danger"> <i class="fa fa-exclamation-circle"></i> '+jsonEbayPro.error_failed+' </div>');
                }else{
                    if(jsonEbayPro.data.success || jsonEbayPro.data.update){
                        success_alert_check = true;
                        if(jsonEbayPro.data.success){
                            html1 = '';
                            for (i in jsonEbayPro.data.success) {
                              html1 += '<div class="alert alert-success">'+jsonEbayPro.data.success[i]+'</div>';
                            }
                        }
                        if(jsonEbayPro.data.update){
                            html1 = '';
                            for (i in jsonEbayPro.data.update) {
                              html1 += '<div class="alert alert-warning">'+jsonEbayPro.data.update[i]+'</div>';
                            }
                        }
                        $('#product_sync_result').append(html1);
                        if(jsonEbayPro.totalPage == 1) {
                            totalImportedProduct = totalImportedProduct + jsonEbayPro.data.success_count;
                        } else {
                            totalImportedProduct = totalImportedProduct + jsonEbayPro.data.success_count;
                        }
                        if(jsonEbayPro.data.success_count){
                            $('#progress-text-product').html('<div class="text-success"> '+jsonEbayPro.data.success_count+' eBay products imported successfully!</div>');
                        }
                    }
                    if(jsonEbayPro.data.error){
                        var html = '';
                        for (i in jsonEbayPro.data.error) {
                            html += '<div class="alert alert-danger"> <i class="fa fa-exclamation-circle"></i> '+jsonEbayPro.data.error[i]+'</div>';
                        }
                        $('#product_sync_result').append(html);
                        if(jsonEbayPro.data.error_count){
                            $('#progress-text-product').html('<div class="text-danger">'+jsonEbayPro.data.error_count+' </div>');
                        }
                    }
                    total = jsonEbayPro.totalPage;
                    for(start_page = 2; start_page < jsonEbayPro.totalPage; start_page++) {
                        requests.push({
                            url     : 'index.php?route=ebay_map/ebay_map_product/getTotalProductPages&token=<?php echo $token; ?>',
                            type    :   "POST",
                            dataType:   "json",
                            async   :   true,
                            data: {
                                'account_id' : '<?php echo $account_id; ?>',
                                'page' : start_page
                            },
                            success :   function(json_response){
                                if(json_response.data.success || json_response.data.update){
                                    success_alert_check = true;
                                    if(json_response.data.success){
                                        html1 = '';
                                        for (i in json_response.data.success) {
                                          html1 += '<div class="alert alert-success">'+json_response.data.success[i]+'</div>';
                                        }
                                    }
                                    if(json_response.data.update){
                                        html1 = '';
                                        for (i in json_response.data.update) {
                                          html1 += '<div class="alert alert-warning">'+json_response.data.update[i]+'</div>';
                                        }
                                    }
                                    $('#product_sync_result').append(html1);
                                    totalImportedProduct = totalImportedProduct + json_response.data.success_count;
                                    if(json_response.data.success_count){
                                        $('#progress-text-product').html('<div class="text-success"> '+json_response.data.success_count+' eBay products imported successfully!</div>');
                                    }
                                }
                                if(json_response.data.error){
                                    var html = '';
                                    for (i in json_response.data.error) {
                                        html += '<div class="alert alert-danger"> <i class="fa fa-exclamation-circle"></i> '+json_response.data.error[i]+'</div>';
                                    }
                                    $('#product_sync_result').append(html);
                                    if(json_response.data.error_count){
                                        $('#progress-text-product').html('<div class="text-danger">'+json_response.data.error_count+' </div>');
                                    }
                                }
                            }
                        });
                    }
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }, 500);
});

var NextStep = function(){
    if (requests.length) {
        $('#progress-bar-product').css('width', (100 - (requests.length / total) * 100) + '%');
        $.ajax(requests.shift()).then(NextStep);
    } else {
      $('#progress-bar-product').css('width', '100%');
      $('.block_div').css('display','none');
      if(success_alert_check){
          $('#progress-text-product').html('<div class="text-success"><?php echo "Total '+totalImportedProduct+' products imported in opencart store from ebay store (check the Show Result)!" ?></div>');
          $('#progress-bar-product').addClass('progress-bar-success');
          var redirect = '<?php echo $redirect; ?>';
          if(redirect){
            // window.location.href = redirect;
          }
      }else{
          $('#progress-text-product').html('<div class="text-danger"><?php echo "Warning: no product imported to opencart store from ebay store, please check Show Result!" ?></div>');
          $('#progress-bar-product').addClass('progress-bar-danger');
      }
    }
};

$('body').on('click', '#import_single_item', function() {
var error_check = false;
    $('#product_sync_result').find('.alert, .text-danger').remove();
    var account_id  = $('input[name="ebay_account_id"]').val();
    var separator   = $('select[name="ebay_import_item_separator"]').val();
    var item_id     = $('input[name="ebay_import_itemId"]').val();
    if(!account_id){
        error_check = true;
        $('#product_sync_result').prepend('<div class="alert alert-danger"> <i class="fa fa-exclamation-circle"></i> Warning: Some account related error occurred, try again! <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
    }
    if(!item_id){
        error_check = true;
        $('#error_ebay_import_itemId').addClass('text-danger');
        $('#error_ebay_import_itemId').html('<i class="fa fa-exclamation-circle"></i> Warning: Provide eBay Item Id! ').show().fadeOut(4000);
    }

    if(!error_check){
        $.ajax({
            url     :   'index.php?route=ebay_map/ebay_map_product/getSingleEbayProduct&token=<?php echo $token; ?>',
            type    :   "POST",
            dataType:   "json",
            data    : {
                        'account_id'    : '<?php echo $account_id; ?>',
                        'separator'     : separator,
                        'ebay_item_id'  : item_id
                      },
            beforeSend: function() {
              $('.block_div').css('display','block');
              $('.container-fluid > .alert').remove();
              $('#single_product_sync_result > .alert').remove();
              $('#product_sync_result > .alert').remove();
              $('#import_single_item').prop('disabled', true);
            },
            complete:function() {
                $('.block_div').css('display','none');
                $('#import_single_item').prop('disabled', false);
            },
            success: function(jsonResponse) {
                if (jsonResponse.error_failed) {
                    $('#single_product_sync_result').prepend('<div class="alert alert-danger" style="border-radius:0;"> <i class="fa fa-exclamation-circle"></i> '+ jsonResponse.error_failed +' </div>');
                }else{
                    var html_result_success = '';
                    var html_result_error = '';
                    if(jsonResponse.data.success || jsonResponse.data.update){
                        if(jsonResponse.data.success){
                            for (i in jsonResponse.data.success) {
                              html_result_success += '<div class="alert alert-success">'+jsonResponse.data.success[i]+'</div>';
                            }
                        }
                        if(jsonResponse.data.update){
                            for (i in jsonResponse.data.update) {
                              html_result_success += '<div class="alert alert-warning">'+jsonResponse.data.update[i]+'</div>';
                            }
                        }
                        $('#single_product_sync_result').prepend(html_result_success);
                    }
                    if(jsonResponse.data.error){
                        for (i in jsonResponse.data.error) {
                            html_result_error += '<div class="alert alert-danger"> <i class="fa fa-exclamation-circle"></i> '+jsonResponse.data.error[i]+'</div>';
                        }
                        $('#single_product_sync_result').prepend(html_result_error);
                    }
               }
            },
        })
    }
})
$(document).ready(function () {
    var checkDownKey = false;
    $(document).keydown(function(e) {
        if (e.keyCode == 17) checkDownKey = true;
    }).keyup(function(e) {
        if (e.keyCode == 17) checkDownKey = false;
    });

  $('input[name="ebay_import_itemId').keypress(function (e) {
       if (e.which != 8 && e.which != 0 && (!checkDownKey && (e.which != 118)) && (e.which < 48 || (e.which > 57 && e.which != 118) ) && e.which != 59 && e.which != 124 && e.which != 58 && e.which != 44) {
          $('#error_ebay_import_itemId').addClass('text-danger');
          $('#error_ebay_import_itemId').html('<i class="fa fa-exclamation-circle"></i> Warning: Accepts Only Digits!').show().fadeOut(4000);
             return false;
      }
   });

  $('#close_model_single_product').on('click', function(e){
     e.preventDefault();
     var getMessage = $('#single_product_sync_result').find('.alert').html();
     if(typeof getMessage !== "undefined"){
         location.reload();
     }else{
         $('body #import_ebay_single_product').modal('toggle');
     }
  })
});
</script>
