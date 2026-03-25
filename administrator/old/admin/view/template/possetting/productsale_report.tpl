<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" id="button-invoice" form="form-order" formaction="<?php echo $print; ?>" formtarget="_blank" data-toggle="tooltip" title="<?php echo $text_invoice; ?>" class="btn btn-info"><i class="fa fa-print"></i></button>
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
            <label class="control-label" for="input-productid"><?php echo $entry_productid; ?></label>
            <input type="text" name="filter_productid" value="<?php echo $filter_productid ?>" placeholder="<?php echo $entry_productid; ?>" id="input-productid" class="form-control" />
          </div>
          <div class="form-group">
            <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
            <input type="text" name="filter_name" value="<?php echo $filter_name ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
          </div>
        </div>
        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label" for="input-model"><?php echo $entry_model; ?></label>
            <input type="text" name="filter_model" value="<?php echo $filter_model ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
          </div>
          <div class="form-group hide">
            <label class="control-label" for="input-date"><?php echo $entry_date; ?></label>
            <input type="text" name="filter_date_added" data-date-format="YYYY-MM-DD" value="<?php echo $filter_date_added ?>" placeholder="YYYY-MM-DD" id="input-date" class="form-control date" />
          </div>
        </div>
        <div class="col-sm-3 text-center">
          <button type="button" style="margin-top:28%;" id="button-filter" style="margin-top:7%;" class="btn btn-primary"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
        </div>

      </div>
    </div>    
    <form action="<?php echo $print; ?>" method="post" enctype="multipart/form-data" id="form-order">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
        <thead>
          <tr>
            
            <td class="text-left"><?php if ($sort == 'productid') { ?>
              <a href="<?php echo $sort_productid; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_productid; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_productid; ?>"><?php echo $column_productid; ?></a>
              <?php } ?>
            </td>
            <td class="text-left"><?php if ($sort == 'name') { ?>
              <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
              <?php } ?>
            </td>
            <td class="text-left"><?php if ($sort == 'model') { ?>
              <a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
              <?php } ?>
            </td>
            
            <td class="text-left"><?php if ($sort == 'totalsell') { ?>
              <a href="<?php echo $sort_totalsell; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_totalsell; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_totalsell; ?>"><?php echo $column_totalsell; ?></a>
              <?php } ?>
            </td>
            
            <td class="text-left hide"><?php if ($sort == 'totalamount') { ?>
              <a href="<?php echo $sort_totalamount; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_totalamount; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_totalamount; ?>"><?php echo $column_totalamount; ?></a>
              <?php } ?>
            </td>
            
            <td class="text-left hide"><?php if ($sort == 'date_added') { ?>
              <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date; ?></a>
              <?php } ?>
            </td>
              
          </tr>
        </thead>
          <?php if ($productsells) { ?>
          <?php foreach ($productsells as $report) { ?>
          <tr>
                        
            <td class="text-left"><?php echo $report['product_id']; ?></td>
            <td class="text-left"><?php echo $report['name']; ?></td>
            <td class="text-left"><?php echo $report['model']; ?></td>
            <td class="text-left"><?php if ($report['totalsale'] <=0) { ?>
              <?php } else { ?>

                <?php echo $report['totalsale']; ?>
              <?php } ?>
            </td>
            <td class="text-left hide"><?php echo $report['price']; ?></td>
            <td class="text-left hide"><?php echo $report['date_added']; ?></td>
        </tr>
            <?php } ?> 
            <?php } else { ?>
          <tr>
            <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
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
  var url = 'index.php?route=possetting/productsale_report&token=<?php echo $token; ?>';
  
  var filter_productid = $('input[name=\'filter_productid\']').val();

  if (filter_productid) {
    url += '&filter_productid=' + encodeURIComponent(filter_productid);
  }
  
  var filter_name = $('input[name=\'filter_name\']').val();

  if (filter_name) {
    url += '&filter_name=' + encodeURIComponent(filter_name);
  }

  var filter_model = $('input[name=\'filter_model\']').val();

  if (filter_model) {
    url += '&filter_model=' + encodeURIComponent(filter_model);
  }
  
  var filter_date_added = $('input[name=\'filter_date_added\']').val();

  if (filter_date_added) {
    url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
  }
  
    
  location = url;
});
</script>
  <script type="text/javascript"><!--
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
//--></script>

<?php echo $footer; ?>

<script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
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
    $('input[name=\'filter_name\']').val(item['label']);
  }
});

$('input[name=\'filter_model\']').autocomplete({
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
    $('input[name=\'filter_model\']').val(item['label']);
  }
});
//--></script>
