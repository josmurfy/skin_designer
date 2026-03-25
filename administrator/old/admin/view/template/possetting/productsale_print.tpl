<script type="text/javascript" src="view/javascript/bootstrap/js/bootstrap.min.js"></script>
<link href="view/stylesheet/bootstrap.css" type="text/css" rel="stylesheet" />
<link href="view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="screen" />
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
      </div>
      <h1><?php echo $heading_title; ?></h1>
      
    </div>
  </div>
  <div class="container-fluid">
    
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
      </div>
      <div class="panel-body">    
    <form action="" method="post" enctype="multipart/form-data" id="form-order">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
        <thead>
          <tr>
            
            <td class="text-left"><?php echo $column_productid; ?></td>
            <td class="text-left"><?php echo $column_name; ?></td>
            <td class="text-left"><?php echo $column_model; ?></td>
            <td class="text-left"><?php echo $column_totalsell; ?></td>
            <td class="text-left hide"><?php echo $column_totalamount; ?></td>
            <td class="text-left hide"><?php echo $column_date; ?></td>
              
          </tr>
        </thead>
          <?php if ($productsells) { ?>
          <?php foreach ($productsells as $report) { ?>
          <tr>          
            <td class="text-left"><?php echo $report['product_id']; ?></td>
            <td class="text-left"><?php echo $report['name']; ?></td>
            <td class="text-left"><?php echo $report['model']; ?></td>
            <td class="text-right"><?php echo $report['totalsale']; ?></td>
            <td class="text-left hide"><?php echo $report['price']; ?></td>
            <td class="text-left hide"><?php echo $report['date_added']; ?></td>
          </tr>
          <?php } ?> 
          <?php }?>
            </table>
        </div>
    </form>
       
    </div>
    </div>
  </div>
</div>

