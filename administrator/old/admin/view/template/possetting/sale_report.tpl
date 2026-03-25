<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="page-header">
<div class="container-fluid">
  <div class="pull-right">
    <button type="submit" id="button-invoice" form="form-order" formaction="<?php echo $print; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $text_print; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>
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
<?php if ($success) { ?>
<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
<button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php } ?>
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
</div>
<div class="panel-body">
  <div class="well">
      <div class="row">
        <div class="col-sm-3">
          <div class="form-group">
            <label class="control-label" for="input-order_id"><?php echo $column_order_id; ?></label>
            <input type="text" name="filter_order_id" value="<?php echo $filter_order_id;?>" placeholder="<?php echo $column_order_id; ?>" id="input-order_id" class="form-control" />
          </div>
        </div>
<!-- Payment Method -->
        <div class="col-sm-3">
          <div class="form-group">
            <label class="control-label" for="input-filter_payment_method"><?php echo $column_payment_method; ?></label>
            <select name="filter_payment_method" class="form-control" id="selectcard">
              <option value="*"><?php echo $text_select;?></option>
              <?php foreach ($setting_paymentmethods as $result){ ?>
              <?php if ($result['name'] == $filter_payment_method){ ?>
              <option value="<?php echo $result['name']; ?>" selected="selected"><?php echo $result['name']; ?></option> 
              <?php } else { ?>
              <option value="<?php echo $result['name']; ?>"><?php echo $result['name']; ?></option> 
              <?php } ?>
              <?php } ?>
            </select>
          </div>
        </div>
<!-- Payment Method -->
        <div class="col-sm-3">
          <div class="form-group">
            <label class="control-label"><?php echo $column_date; ?></label>
            <div class="input-group">
              <span class="input-group-addon">
                <?php echo $column_form;?>
              </span>
              <input type="text" name="filter_date_form" value="<?php echo $filter_date_form;?>" class="form-control datefrom" data-date-format="YYYY-MM-DD" id="input-filter_date_added_form"/>
              <span class="input-group-addon ">
                <?php echo $column_to;?>
              </span>
              <input type="text" name="filter_date_to" value="<?php echo $filter_date_to;?>" class="form-control dateto" data-date-format="YYYY-MM-DD" id="input-filter_date_added_to"/>
            </div>
          </div>
        </div>
        <div class="col-sm-2 text-center">
          <button type="button" style="margin-top:30px;" id="button-filter" class="btn btn-primary"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
        </div>
      </div>
   </div>
  <form action="" method="post" enctype="multipart/form-data" id="form-order">
    <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <td class="text-left"><?php if ($sort == 'date') { ?>
            <a href="<?php echo $sort_date; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_date; ?>"><?php echo $column_date; ?></a>
            <?php } ?>
          </td>

          <td class="text-left"><?php if ($sort == 'order_id') { ?>
            <a href="<?php echo $sort_order_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_order_id; ?>"><?php echo $column_order_id; ?></a>
            <?php } ?>
          </td>

          <td class="text-left"><?php if ($sort == 'payment_method') { ?>
            <a href="<?php echo $sort_payment_method; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_payment_method; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_payment_method; ?>"><?php echo $column_payment_method; ?></a>
            <?php } ?>
          </td>

          <td class="text-left"><?php if ($sort == 'total') { ?>
            <a href="<?php echo $sort_total; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_total; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
            <?php } ?>
          </td>
                    
        </tr>
      </thead>
      <tbody>
        <?php if ($sellreports) { ?>
        <?php foreach ($sellreports as $sellreport) { ?>
        <tr>
          <td class="text-left"><?php echo $sellreport['date_added'];?></td>
          <td class="text-left"><?php echo $sellreport['order_id'];?></td>
          <td class="text-left"><?php echo $sellreport['payment_method'];?></td>
          <td class="text-left"><?php echo $sellreport['total'];?></td>
        </tr>
        <?php } ?> 
        <?php } else { ?>
        <tr>
          <td class="text-center" colspan="5"><?php echo $text_no_results; ?></td>
        </tr>
        <?php } ?>
        <tr>
          <td class="text-center" colspan="3"><strong>Grand Total</strong></td> 
          <td class="text-left" colspan="4"><strong><?php echo $grandtotale; ?></strong></td> 
        </tr>
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

<script type="text/javascript">
$('#button-filter').on('click', function() {
  var url = 'index.php?route=possetting/sale_report&token=<?php echo $token; ?>';
  
  var filter_order_id = $('input[name=\'filter_order_id\']').val();

  if (filter_order_id) {
    url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
  }

  var filter_payment_method = $('select[name=\'filter_payment_method\']').val();

  if (filter_payment_method !='*') {
    url += '&filter_payment_method=' + encodeURIComponent(filter_payment_method);
  }

  var filter_date_form = $('input[name=\'filter_date_form\']').val();

  if (filter_date_form) {
    url += '&filter_date_form=' + encodeURIComponent(filter_date_form);
  }

  var filter_date_to = $('input[name=\'filter_date_to\']').val();

  if (filter_date_to) {
    url += '&filter_date_to=' + encodeURIComponent(filter_date_to);
  }

  location = url;
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

<?php echo $footer; ?>
