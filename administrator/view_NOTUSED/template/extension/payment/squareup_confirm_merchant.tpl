<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">        
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) : ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid">
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-exchange"></i>&nbsp;<?php echo $text_confirm_merchant; ?></h3>
      </div>
      <div class="panel-body">
        <p><?php echo $text_confirm_merchant_intro_1; ?></p>
        <p><?php echo $text_confirm_merchant_intro_2; ?></p>
        <ul>
          <?php if ($has_catalog_sync) : ?>
            <li><?php echo $text_confirm_merchant_delete_catalog_sync; ?></li>
          <?php endif; ?>
          <li><?php echo $text_confirm_merchant_delete_customers_cards; ?></li>
        </ul>
        <div class="text-right">
          <a href="<?php echo $reject; ?>" class="btn btn-default"><?php echo $button_reject_merchant_change; ?></a>
          <a href="<?php echo $confirm; ?>" class="btn btn-primary"><?php echo $button_accept_merchant_change; ?></a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>
