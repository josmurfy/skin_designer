
<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">        
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-globe"></i>&nbsp;<?php echo $text_configure_geo_zone; ?></h3>
      </div>
      <div class="panel-body">
        <p><?php echo $text_zone_intro_1; ?></p>
        <p><?php echo $text_zone_intro_2; ?></p>
        <p><?php echo $text_zone_intro_3; ?></p>
        <ul>
          <li><?php echo $text_zone_usa; ?></li>
          <li><?php echo $text_zone_canada; ?></li>
          <li><?php echo $text_zone_eu_uk; ?></li>
          <li><?php echo $text_zone_japan; ?></li>
          <?php if ($store_country) { ?>
            <li><?php echo $text_zone_store_country; ?></li>
          <?php } ?>
        </ul>
        <div class="text-right">
          <a href="<?php echo $skip; ?>" class="btn btn-default"><?php echo $button_skip_no_remind; ?></a>
          <a href="<?php echo $confirm; ?>" class="btn btn-primary"><?php echo $button_confirm_geo_zone; ?></a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>