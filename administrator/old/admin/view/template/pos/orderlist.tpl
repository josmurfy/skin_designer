<div class="modal-content">
 <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button></div>	  
   <div class="modal-body">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="well">
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label" for="input-filter_order_id"><?php echo $column_order_id; ?> </label>
            <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="<?php echo $column_order_id; ?>" id="input-filter_order_id" class="form-control" />
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label" for="input-filter_customer"><?php echo $column_customer;?> </label>
            <input type="text" name="filter_customer" value="" placeholder="<?php echo $column_customer; ?>" id="input-filter_customer" class="form-control" />
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label" for="input-filter_order_status"><?php echo $column_status;?> </label>
            <select name="filter_order_status" id="input-filter_order_status" class="form-control">
              <option value=""><?php echo $text_select?></option>
              <?php foreach ($order_statuses as $order_status) { ?>
              <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label"><?php echo $column_date; ?></label>
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              <input type="text" class="form-control datefrom" placeholder="<?php echo $entry_from; ?>" data-date-format="YYYY-MM-DD" name="filter_date_from" value="<?php echo $filter_date_from; ?>" />
              <span class="input-group-addon ">
                <i class="fa fa-calendar"></i>
              </span>
              <input type="text" class="form-control dateto" placeholder="<?php echo $entry_to; ?>" data-date-format="YYYY-MM-DD" name="filter_date_to" value="<?php echo $filter_date_to; ?>" />
            </div>
          </div>
         </div>
         <div class="col-sm-8">
          <div class="pull-right" style="margin-top: 6%;">
             <button type="button" id="button-filter" class="btn btn-primary"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
          </div>
        </div>
      </div>
    </div>
        <form method="post" action="" enctype="multipart/form-data" id="form-order">
          <div class="table-responsive">
            
            <table class="table table-bordered table-hover">
              <thead class="sortorder">
                <tr>
                 
                  <td class="text-right"><?php if ($sort == 'o.order_id') { ?>
                    <a href="<?php echo $sort_order; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'customer') { ?>
                    <a href="<?php echo $sort_customer; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_customer; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'order_status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php if ($sort == 'o.total') { ?>
                    <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'o.date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'o.date_modified') { ?>
                    <a href="<?php echo $sort_date_modified; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_modified; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_modified; ?>"><?php echo $column_date_modified; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($orders) { ?>
                <?php foreach ($orders as $order) { ?>
                <tr>
                
                  <td class="text-right"><?php echo $order['order_id']; ?></td>
                  <td class="text-left"><?php echo $order['customer']; ?></td>
                  <td class="text-left"><?php echo $order['order_status']; ?></td>
                  <td class="text-right"><?php echo $order['total']; ?></td>
                  <td class="text-left"><?php echo $order['date_added']; ?></td>
                  <td class="text-left"><?php echo $order['date_modified']; ?></td>
                  <td class="text-right"><a href="javascript:;" rel="<?php echo $order['order_id']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary orderedit"><i class="fa fa-pencil"></i></a></td>
                </tr>
				
                <?php } ?>
				<tr>
				<td colspan="7">
			
					<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
				  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
				
				</td>
				</tr>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="8"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
           </table>
         </div>
     </form>
  </div>
</div>
<script>

// Customer pagination
$('#help-modal16').delegate('.pagination a, .sortorder a', 'click', function(e) {
    e.preventDefault();
	$('.orderlist-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.orderlist-body').load(this.href);
	
	return false;
});
</script>

<script type="text/javascript">
$(document).on('click', '#button-filter',function(){
  var url = 'index.php?route=pos/orderlist&token=<?php echo $token; ?>';
  
  var filter_order_id = $('.orderlist-body input[name=\'filter_order_id\']').val();

  if (filter_order_id) {
    url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
  }

  var filter_customer = $('.orderlist-body  input[name=\'filter_customer\']').val();

  if (filter_customer) {
    url += '&filter_customer=' + encodeURIComponent(filter_customer);
  }

  var filter_order_status = $('.orderlist-body  select[name=\'filter_order_status\']').val();
	
  if (filter_order_status) {
    url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
  }

  var filter_date_from = $('.orderlist-body  input[name=\'filter_date_from\']').val();

  if (filter_date_from) {
    url += '&filter_date_from=' + encodeURIComponent(filter_date_from);
  }

  var filter_date_to = $('.orderlist-body  input[name=\'filter_date_to\']').val();

  if (filter_date_to) {
    url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
  }

  $('.orderlist-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
  $('.orderlist-body').load(url);
});
</script>

 <script type="text/javascript"><!--
$('.datefrom').datetimepicker({
  pickTime: false
});
$('.dateto').datetimepicker({
  pickTime: false
}); 
//--></script>