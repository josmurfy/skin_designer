<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="page-header">
<div class="container-fluid">
<div class="pull-right">
  <button type="submit" id="button-invoice" form="form-commission" formaction="<?php echo $print; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $text_print; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>
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
        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label" for="input-username"><?php echo $column_username; ?></label>
            <input type="text" name="filter_username" value="<?php echo $filter_username; ?>" placeholder="<?php echo $column_username; ?>" id="input-username" class="form-control" />
            <input type="hidden" name="user_id" value="" />
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label" for="input-order_id"><?php echo $column_order_id; ?></label>
            <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="<?php echo $column_order_id; ?>" id="input-order_id" class="form-control" />
          </div>
        </div>
        <div class="col-sm-2 text-center">
          <button type="button" id="button-filter" class="btn btn-primary"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
        </div>
      </div>
   </div>
  <form action="" method="post" enctype="multipart/form-data" id="form-commission">
    <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <td class="text-left"><?php if ($sort == 'order_id') { ?>
            <a href="<?php echo $sort_order_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_order_id; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_order_id; ?>"><?php echo $column_order_id; ?></a>
            <?php } ?>
          </td>
            
          <td class="text-left"><?php if ($sort == 'username') { ?>
            <a href="<?php echo $sort_username; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_username; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_username; ?>"><?php echo $column_username; ?></a>
            <?php } ?>
          </td>

          <td class="text-left"><?php if ($sort == 'commission') { ?>
            <a href="<?php echo $sort_commission; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_commission; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_commission; ?>"><?php echo $column_commission; ?></a>
            <?php } ?>
          </td>
          
          <td class="text-left"><?php if ($sort == 'amount') { ?>
            <a href="<?php echo $sort_amount; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_amount; ?></a>
            <?php } else { ?>
            <a href="<?php echo $sort_amount; ?>"><?php echo $column_amount; ?></a>
            <?php } ?>
          </td>
          
        </tr>
      </thead>
      <tbody>
        <?php if ($users) { ?>
        <?php foreach ($users as $result) { ?>
        <tr>
          <td class="text-left"><?php echo $result['order_id'];?></td>
          <td class="text-left"><?php echo $result['username'];?></td>
          <td class="text-left"><?php echo $result['commission'];?></td>
          <td class="text-left"><?php echo $result['amount'];?></td>
        </tr>
        <?php } ?> 
        <?php } else { ?>
        <tr>
          <td class="text-center" colspan="4"><?php echo $text_no_results; ?></td>
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
  var url = 'index.php?route=possetting/commission_report&token=<?php echo $token; ?>';
  
  var filter_username = $('input[name=\'user_id\']').val();

  if (filter_username) {
    url += '&filter_username=' + encodeURIComponent(filter_username);
  }

  var filter_order_id = $('input[name=\'filter_order_id\']').val();

  if (filter_order_id) {
    url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
  }

  var filter_amount = $('input[name=\'filter_amount\']').val();

  if (filter_amount) {
    url += '&filter_amount=' + encodeURIComponent(filter_amount);
  }

  location = url;
});
</script>


<script type="text/javascript"><!--
    
// Seller
$('input[name=\'filter_username\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=possetting/commission_report/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        json.unshift({
          user_id: 0,
          username: '<?php echo $text_none; ?>'
        });

        response($.map(json, function(item) {
          return {
            label: item['username'],
            value: item['user_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'filter_username\']').val(item['label']);
    $('input[name=\'user_id\']').val(item['value']);
  }
});
</script>

<?php echo $footer; ?>
