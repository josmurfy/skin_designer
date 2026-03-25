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
        <button type="button" id="import-ebay-order" data-toggle="tooltip" data-token="<?php echo $token; ?>" data-account="<?php echo $account_id; ?>" title="<?php echo $button_import_ebay_order; ?>" class="btn btn-success"><i class="fa fa-download" aria-hidden="true"></i> <?php echo $button_import_ebay_order; ?></button>

        <button type="button" id="import-ebay-single-order" data-toggle="modal" data-target="#import_ebay_single_order" title="<?php echo $help_import_ebay_single_order; ?>" class="btn btn-warning"><i class="fa fa-hand-o-right" aria-hidden="true"></i> <?php echo $button_import_ebay_single_order; ?></button>

        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#import_ebay_order">
          <i class="fa fa-info-circle" aria-hidden="true"></i> <?php echo "Show Result"; ?>
        </button>
        <button id="delete-import-order" type="button" data-toggle="tooltip" data-token="<?php echo $token; ?>" title="<?php echo $button_delete; ?>" data-account="<?php echo $account_id; ?>" class="btn btn-danger" ><i class="fa fa-trash-o"></i></button>
      </div>
    </div>
  </div>
  <!-- Modal-order-result -->
  <div class="modal fade" id="import_ebay_order" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><?php echo $text_syn_result; ?></h4>
        </div>
        <div class="modal-body" id="order_sync_result" style="overflow-y: scroll;max-height: 350px">
          <?php if(isset($order_import_result['success']) && $order_import_result['success']){
            foreach($order_import_result['success'] as $success_import_success){ ?>
              <div class="alert alert-success"> <?php echo $success_import_success['success_message']; ?></div>
            <?php } ?>
          <?php } ?>
          <?php if(isset($order_import_result['error']) && $order_import_result['error']){
            foreach($order_import_result['error'] as $order_import_error){ ?>
              <div class="alert alert-danger"> <?php echo $order_import_error['error_message']; ?></div>
            <?php } ?>
          <?php } ?>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal-single-order-import-->
  <div class="modal fade" id="import_ebay_single_order" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><?php echo $text_import_single_order; ?></h4>
        </div>
        <div class="modal-body" id="single_order_sync_result" style="overflow-y: scroll;max-height: 350px">
            <div class="form-horizontal">
                <div class="form-group required">
                  <label class="col-sm-3 control-label" for="input-ebay-order-id"><span data-toggle="tooltip" title="<?php echo $help_ebay_import_orderId; ?>"><?php echo $entry_ebay_import_orderId; ?></span></label>
                  <div class="col-sm-9">
                    <input type="hidden" name="ebay_account_id" value="<?php echo $account_id; ?>" />
                    <input type="text" class="form-control" name="ebay_import_orderId" id="input-ebay-order-id" value="<?php if(isset($ebay_import_orderId)){ echo $ebay_import_orderId; } ?>" placeholder="<?php echo $placeholder_order_id; ?>" />
                    <div id="error_ebay_import_orderId"></div>
                  </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="import_single_order"><?php echo $button_single_order_import; ?></button>
          <button type="button" class="btn btn-default" id="close_model_single">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="container-fluid" id="order_import_list_section">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading"  style="display:inline-block;width:100%;">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_product_list; ?></h3></div>
          <div class="panel-body">
            <div class="col-sm-12 form-horizontal text-right">
              <div class="col-sm-12 form-group">
                <label class="col-sm-2 control-label"><?php echo $text_processing; ?></label>
                <div class="col-sm-10" style="margin-top:10px">
                  <div class="progress">
                    <div id="progress-bar" class="progress-bar" style="width: 0%;"></div>
                  </div>
                  <div id="progress-text"></div>
                </div>
              </div>
            </div>

            <div class="well">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-oc-order-id"><?php echo $column_oc_order_id; ?></label>
                      <input type="text" name="filter_oc_order_id" value="<?php echo $filter_oc_order_id; ?>" placeholder="<?php echo $column_oc_order_id; ?>" id="input-oc-order-id" class="form-control"/>
                  </div>

                  <div class="form-group">
                    <label class="control-label" for="input-oc-order-total"><?php echo $column_order_total; ?></label>
                    <input type="text" name="filter_order_total" value="<?php echo $filter_order_total; ?>" placeholder="<?php echo $column_order_total; ?>" id="input-oc-order-total" class="form-control"/>
                  </div>
                </div>

                <div class="col-sm-4">
                    <div class="form-group">
                      <label class="control-label" for="input-ebay-order-id"><?php echo $column_ebay_order_id; ?></label>
                        <input type="text" name="filter_ebay_order_id" value="<?php echo $filter_ebay_order_id; ?>" placeholder="<?php echo $column_ebay_order_id; ?>" id="input-ebay-order-id" class="form-control"/>
                    </div>

                    <div class="form-group">
                      <label class="control-label" for="input-date-added"><?php echo $column_created_date; ?></label>
                      <div class="input-group datetime">
                        <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" data-date-format="YYYY-MM-DD HH:mm:ss" placeholder="<?php echo $column_created_date; ?>" id="input-date-added" class="form-control"/>
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
                        </span>
                      </div>
                    </div>
                </div>

                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-order-status"><?php echo $column_ebay_order_status; ?></label>
                      <input type="text" name="filter_order_status" value="<?php echo $filter_order_status; ?>" placeholder="<?php echo $column_ebay_order_status; ?>" id="input-order-status" class="form-control"/>
                  </div>

                  <div style="margin-top:38px;">
                    <button type="button" onclick="filter_map_order();" class="btn btn-primary" style="border-radius:0px;">
                      <i class="fa fa-search"></i><?php echo $button_filter_order; ?></button>
                    <a href="<?php echo $clear_order_filter; ?>" class="btn btn-default pull-right" style="border-radius:0px;"><i class="fa fa-eraser" aria-hidden="true"></i><?php echo $button_clear_order; ?></a>
                  </div>
                </div>

              </div>
            </div>

            <form method="post" enctype="multipart/form-data" id="form-order-delete">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('#order_import_list_section input[name*=\'selected\']').prop('checked', this.checked);" /></td>

                      <td class="text-center"><?php echo $column_ebay_order_id; ?></td>
                      <td class="text-center"><?php echo $column_oc_order_id; ?></td>
                      <td class="text-right"><?php echo $column_ebay_order_status; ?></td>
                      <td class="text-center"><?php echo $column_order_total; ?></td>
                      <td class="text-right"><?php echo $column_created_date; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if ($map_orders) { ?>
                    <?php foreach ($map_orders as $ebay_order) { ?>
                    <tr>
                      <td class="text-center"><?php if (in_array($ebay_order['oc_order_id'], $selected)) { ?>
                        <input type="checkbox" name="selected[]" value="<?php echo $ebay_order['oc_order_id']; ?>" checked="checked" />
                        <?php } else { ?>
                        <input type="checkbox" name="selected[]" value="<?php echo $ebay_order['oc_order_id']; ?>" />
                        <?php } ?></td>

                      <td class="text-center text-info"><?php echo $ebay_order['ebay_order_id']; ?></td>
                      <td class="text-center"><?php echo $ebay_order['oc_order_id']; ?></td>
                      <td class="text-right text-success"><?php echo $ebay_order['ebay_order_status']; ?></td>
                      <td class="text-center text-danger"><?php echo $ebay_order['order_total']; ?></td>
                      <td class="text-right"><?php echo $ebay_order['created_date']; ?></td>
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
function filter_map_order() {
	url = 'index.php?route=ebay_map/ebay_account/edit&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&status=account_order_map';

  var filter_oc_order_id = $('input[name=\'filter_oc_order_id\']').val();

  if (filter_oc_order_id) {
    url += '&filter_oc_order_id=' + encodeURIComponent(filter_oc_order_id);
  }

  var filter_ebay_order_id = $('input[name=\'filter_ebay_order_id\']').val();

  if (filter_ebay_order_id) {
    url += '&filter_ebay_order_id=' + encodeURIComponent(filter_ebay_order_id);
  }

  var filter_order_total = $('input[name=\'filter_order_total\']').val();

  if (filter_order_total) {
    url += '&filter_order_total=' + encodeURIComponent(filter_order_total);
  }

	var filter_date_added = $('input[name=\'filter_date_added\']').val();

	if (filter_date_added) {
		url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
	}

  var filter_order_status = $('input[name=\'filter_order_status\']').val();

  if (filter_order_status) {
    url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
  }

	location = url;

}

$('.datetime').datetimepicker({
  pickDate: true,
  pickTime: true
});
//--></script>
<script type="text/javascript">

    var order_step      = new Array();
    var total_step      = 0;
    var success_notify  = false;
    $('body').on('click','#import-ebay-order',function(e){
      e.preventDefault();
      var get_Token   = $(this).data('token');
      var get_Account = $(this).data('account');
       if (typeof timer != 'undefined') {
            clearInterval(timer);
        }
        timer = setInterval(function() {
          clearInterval(timer);
          // Reset everything
          $('.alert').remove();
          $('#order_import_list_section #progress-bar').css('width', '0%');
          $('#order_import_list_section #progress-text').html('');

          $.ajax({
            url: 'index.php?route=ebay_map/ebay_map_order/import_order&token='+get_Token+'&account_id='+get_Account,
            type: 'post',
            beforeSend: function() {
              $('.alert').remove();
              $('.block_div').css('display','block');
            },
            success: function(json) {
              if (json['error']) {
                $('#order_import_list_section #progress-bar-danger').css('width','100%');
                $('#order_import_list_section #progress-text').html('<div class="text-danger">' + json['error']['error_message'] + '</div>');
                $('#order_sync_result').append('<div class="alert alert-danger">' + json['error']['error_message'] + '</div>');
                $('.block_div').css('display','none');
              }else{
                if (json['step']) {
                  order_step = json['step'];
                  total_step = order_step.length;
                  next_step();
                }
              }
              if (json['redirect']){
                location = json['redirect'];
              }
            },
            error: function(xhr, ajaxOptions, thrownError) {
              alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
          });
      }, 500);
  });

function next_step() {
  data = order_step.shift();
  $('#order_import_list_section #progress-bar').removeClass('progress-bar-danger');
  if (data) {
    $('#order_import_list_section #progress-bar').css('width', (100 - (order_step.length / total_step) * 100) + '%');
    $('#order_import_list_section #progress-text').html('<span class="text-info pull-left">' + data['text'] + '</span>');
    $.ajax({
      url: data.url,
      type: 'post',
      dataType: 'json',
      data: {'order_id':data.process_data,'page_no': data.page_no},
      success: function(json) {
          if (json.error) {
             $('#order_import_list_section #progress-bar').addClass('progress-bar-danger');
            var html = '';
            for (i in json.error) {
              if(json.error[i]['error_status']){
                html += '<div class="alert alert-danger">'+json.error[i]['error_message']+'</div>';
              }
            }
            $('#order_sync_result').append(html);
          }

          if (json.success) {
            success_notify = true;
            html1 = '';
            for (i in json.success) {
              html1 += '<div class="alert alert-success">'+json.success[i]['success_message']+'</div>';
            }
            $('#order_sync_result').append(html1);
          }
          setTimeout(function(){
            next_step();
          },2000)
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }else{
    $('#order_import_list_section #progress-bar').css('width', '100%');
    if(success_notify){
        $('#order_import_list_section #progress-bar').addClass('progress-bar-success');
        $('#order_import_list_section').prepend('<div class="alert alert-success"><?php echo $test_success_sync; ?></div>');
        $('#order_import_list_section #progress-text').html('<span class="text-success"> <?php echo $text_success_order_sync; ?></span>');
        var redirect = '<?php echo $redirect; ?>';
        if(redirect){
          window.location.href = redirect;
        }
    }else{
        $('#progress-text').html('<div class="text-danger"> <?php echo "Warning: no order imported to opencart store from ebay store, please check Show Result!" ?></div>');
        $('#order_import_list_section #progress-bar').addClass('progress-bar-danger');
    }
    $('.block_div').css('display','none');
  }
}

$('body').on('click', '#import_single_order', function() {
var error_check = false;
    $('#order_sync_result').find('.alert, .text-danger').remove();
    var account_id    = $('input[name="ebay_account_id"]').val();
    var order_id      = $('input[name="ebay_import_orderId"]').val();
    if(!account_id){
        error_check   = true;
        $('#order_sync_result').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> Warning: There is some issue related to account info, please try again! <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
    }
    if(!order_id){
        error_check = true;
        $('#error_ebay_import_orderId').addClass('text-danger');
        $('#error_ebay_import_orderId').html('<i class="fa fa-exclamation-circle"></i> Warning: Please provide eBay Order Id! ').show().fadeOut(4000);
    }else if(order_id.length < 10 || order_id.length > 30){
        error_check = true;
        $('#error_ebay_import_orderId').addClass('text-danger');
        $('#error_ebay_import_orderId').html('<i class="fa fa-exclamation-circle"></i> Warning: Order Id must be between 10 and 30 digits! ').show().fadeOut(4000);
    }
    if(!error_check){
        $.ajax({
            url     :   'index.php?route=ebay_map/ebay_map_order/getSingleEbayOrder&token=<?php echo $token; ?>',
            type    :   "POST",
            dataType:   "json",
            data    : {
                        'account_id'    : '<?php echo $account_id; ?>',
                        'ebay_order_id'  : order_id
                      },
            beforeSend: function() {
              $('.block_div').css('display','block');
              $('.container-fluid > .alert').remove();
              $('#single_order_sync_result > .alert').remove();
              $('#order_sync_result > .alert').remove();
              $('#import_single_order').prop('disabled', true);
            },
            complete:function() {
                $('.block_div').css('display','none');
                $('#import_single_order').prop('disabled', false);
            },
            success: function(jsonResponse) {
                if (jsonResponse.error_failed) {
                    $('#single_order_sync_result').prepend('<div class="alert alert-danger" style="border-radius:0;"> <i class="fa fa-exclamation-circle"></i> '+ jsonResponse.error_failed +' </div>');
                }else{
                    if (jsonResponse.success) {
                      var html_result_success = '';
                      for (i in jsonResponse.success) {
                        html_result_success += '<div class="alert alert-success">'+jsonResponse.success[i]['success_message']+'</div>';
                      }
                      $('#single_order_sync_result').prepend(html_result_success);
                    }

                    if (jsonResponse.update) {
                      var html_result_update = '';
                      for (i in jsonResponse.update) {
                        if(jsonResponse.update[i]['error_status']){
                          html_result_update += '<div class="alert alert-warning">'+jsonResponse.update[i]['error_message']+'</div>';
                        }
                      }
                      $('#single_order_sync_result').prepend(html_result_update);
                    }

                    if (jsonResponse.error) {
                      var html_result_error = '';
                      for (i in jsonResponse.error) {
                        if(jsonResponse.error[i]['error_status']){
                          html_result_error += '<div class="alert alert-danger">'+jsonResponse.error[i]['error_message']+'</div>';
                        }
                      }
                      $('#single_order_sync_result').prepend(html_result_error);
                    }
               }
            },
        })
    }
})
$(document).ready(function () {
    var checkOrderDownKey = false;
    $(document).keydown(function(e) {
        if (e.keyCode == 17) checkOrderDownKey = true;
    }).keyup(function(e) {
        if (e.keyCode == 17) checkOrderDownKey = false;
    });

  $('input[name="ebay_import_orderId').keypress(function (e) {
       if (e.which != 8 && e.which != 0 && (!checkOrderDownKey && (e.which != 118)) && (e.which < 48 || (e.which > 57 && e.which != 118) )) {
          $('#error_ebay_import_orderId').addClass('text-danger');
          $('#error_ebay_import_orderId').html('<i class="fa fa-exclamation-circle"></i> Warning: Accepts Only Digits!').show().fadeOut(4000);
             return false;
      }
   });

   $('#close_model_single').on('click', function(e){
      e.preventDefault();
      var getMessage = $('#single_order_sync_result').find('.alert').html();
      if(typeof getMessage !== "undefined"){
          location.reload();
      }else{
          $('body #import_ebay_single_order').modal('toggle');
      }
   })
});
</script>
