<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-user" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-user">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td class="text-left"><?php if ($sort == 'order_id') { ?>
                    <a href="<?php echo $sort_order_id; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_order_id; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_order_id; ?>"><?php echo $entry_order_id; ?></a>
                    <?php } ?></td>
                    <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $entry_name; ?></a>
                    <?php } ?></td>
                    <td class="text-left"><?php if ($sort == 'barcode') { ?>
                    <a href="<?php echo $sort_barcode; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_barcode; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_barcode; ?>"><?php echo $entry_barcode; ?></a>
                    <?php } ?></td>
                    <td class="text-left"><?php if ($sort == 'quantity') { ?>
                    <a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $entry_quantity; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_quantity; ?>"><?php echo $entry_quantity; ?></a>
                    <?php } ?></td>
                </tr>
              </thead>
              <tr>
                <td class="text-left"></td>
                <td class="text-left"></td>
                <td class="text-left"></td>
                <td class="text-left"></td>
              </tr>
            </table>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php echo $footer; ?> 