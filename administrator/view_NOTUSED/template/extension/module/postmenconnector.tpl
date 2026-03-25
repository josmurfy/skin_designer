<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-postmenconnector" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
         <a id="orderexport" title="Sync Orders" onclick="$('#postmentmsgbody').html('<strong>Orders Export to Postmen Please wait...</strong>');$('#postmentpopupbutton').click();$('#actionpost').val('exportorders');$('#form-postmenconnector').submit();" class="btn btn-primary"><i class="fa fa-cog"></i></a>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
        
        </div>
       
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if(isset($success)) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-postmenconnector" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-cost"><?php echo $entry_apikey; ?></label>
            <div class="col-sm-10">
              <input type="text" name="postmenconnector_apikey" required="required" value="<?php echo $postment_masking_api_key; ?>" placeholder="<?php echo $entry_apikey; ?>" id="input-apikey" class="form-control" />
              <input type="hidden" name="postment_masking_api_key"  value=""  id="input-apikey" class="form-control" />
              <input type="hidden" name="postment_end_url"  value=""  id="input-apikey" class="form-control" />
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="postmenconnector_status" id="input-status" class="form-control">
                <?php if ($postmenconnector_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
              <input type="hidden" name="action" id="actionpost" value="configration" />
            </div>
          </div>
        </form>
      </div>
    </div>
    <!-- Button trigger modal -->
    <button type="button" class="btn btn-primary btn-lg hidden" id="postmentpopupbutton" data-toggle="modal" data-target="#myModalPostment"> </button>
    
    <!-- Modal -->
    <div class="modal fade" id="myModalPostment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Postment Request and Response</h4>
          </div>
          <div class="modal-body" id="postmentmsgbody"> ... </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <td class="text-right"><?php if ($sort == 'order_id') { ?>
              <a href="<?php echo $sort_order_id; ?>" class="<?php echo strtolower($order); ?>">Order Id</a>
              <?php } else { ?>
              <a href="<?php echo $sort_order_id; ?>">Order Id</a>
              <?php } ?></td>
            <td class="text-left"><?php if ($sort == 'customer') { ?>
              <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>">Customer</a>
              <?php } else { ?>
              <a href="<?php echo $sort_customer; ?>">Customer</a>
              <?php } ?></td>
            <td class="text-right"><?php if ($sort == 'order_status') { ?>
              <a href="<?php echo $sort_order_status; ?>" class="<?php echo strtolower($order); ?>">Status</a>
              <?php } else { ?>
              <a href="<?php echo $sort_order_status; ?>">Status</a>
              <?php } ?></td>
            <td class="text-right"><?php if ($sort == 'total') { ?>
              <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>">Total</a>
              <?php } else { ?>
              <a href="<?php echo $sort_total; ?>">Total</a>
              <?php } ?></td>
            <td class="text-right"><?php if ($sort == 'date_added') { ?>
              <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>">Order Date</a>
              <?php } else { ?>
              <a href="<?php echo $sort_date_added; ?>">Order Date</a>
              <?php } ?></td>
            <td class="text-right"><?php if ($sort == 'status') { ?>
              <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>">Sync Status</a>
              <?php } else { ?>
              <a href="<?php echo $sort_status;?>">Sync Status</a>
              <?php } ?></td>
            <td class="text-right">Action</td>
          </tr>
        </thead>
        <tbody>
          <?php if (isset($Orders)) { ?>
          <?php foreach ($Orders as $order) { ?>
          <tr>
            <td class="text-right"><?php echo $order['order_id']; ?></td>
            <td class="text-left"><?php echo $order['customer']; ?></td>
            <td class="text-right"><?php echo $order['order_status']; ?></td>
            <td class="text-right"><?php echo $order['total']; ?></td>
            <td class="text-right"><?php echo $order['date_added']; ?></td>
            <td class="text-right"><?php if ($order['status'] == 1) { ?>
              Done
              <?php }else{ ?>
              <button type="button" data-toggle="tooltip" title="" class="btn btn-danger"  data-original-title="Error">Error</button>
              <?php }?></td>
            <td class="text-right"><form method="post" id="orderresend">
                <input type="hidden" name="orderid" value="<?php echo $order['order_id']; ?>" />
                <input type="hidden" name="action" value="order_resend" />
                <button type="button" onclick="confirm('Are you sure?') ? $(this).parents('form').submit() : false;" data-toggle="tooltip"  class="btn btn-info" data-original-title="Resend Order"> <i class="fa fa-refresh"></i> </button>
                <div style="display:none;" class="requestresponsediv">
                  <div> <strong>Request Time: </strong> <?php echo ($order['request_datetime']); ?> <br />
                    <strong>Response Time: </strong> <?php echo ($order['response_datetime']); ?> <br />
                    <strong>Total Time: </strong> <?php echo ($order['response_time']); ?> Second<br />
                    <br />
                    <strong>Request: </strong> <spna class="requestspan"><?php echo json_encode(json_decode($order['request']),JSON_PRETTY_PRINT); ?></div> <br />
                    
                    <strong> Response:</strong> <span class="responsespan"><?php echo ($order['response']); ?></span> </div>
                </div>
                <a href="javascript:;" onclick="$('#postmentmsgbody').html($(this).parents('td').find('.requestresponsediv').html());$('#postmentpopupbutton').click();" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="Request and Response"><i class="fa fa-asterisk"></i></a>
              </form></td>
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
    <div class="row">
      <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
      <div class="col-sm-6 text-right"><?php echo $results; ?></div>
    </div>
  </div>
</div>
<?php 
if(isset($exportorder)){?>
<script>
	$(document).ready(function(e) {
        $("#orderexport").click();
    });
</script>
<?php }?>
<?php echo $footer; ?> 