<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-canadapost" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
			

 
    
<div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-canadapost" class="form-horizontal">
          
<!-- STATUS: ENABLED OR DISABLED -->					
<div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status ?></label>
            <div class="col-sm-10">
              <select name="canadapost_status" id="input-status" class="form-control">
                <?php if ($canadapost_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>											
<!-- STATUS: ENABLED OR DISABLED -->	



<!-- RESPONSE LANGUAGE: ENGLISH OR FRENCH -->	
<div class="form-group">
            <label class="col-sm-2 control-label" for="input-language"><?php echo $entry_language ?></label>
            <div class="col-sm-10">
              <select name="canadapost_language" id="input-language" class="form-control">
                <?php if ($canadapost_language) { ?>
                <option value="1" selected="selected"><?php echo $text_french; ?></option>
                <option value="0"><?php echo $text_eng; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_french; ?></option>
                <option value="0" selected="selected"><?php echo $text_eng; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>						
<!-- RESPONSE LANGUAGE: ENGLISH OR FRENCH -->	

<!-- CANADA POST SERVER -->
<div class="form-group required">
            <label class="col-sm-2 control-label" for="input-server"><?php echo $entry_server; ?></label>
            <div class="col-sm-10">
              <input type="text" name="canadapost_server" value="<?php echo $canadapost_server; ?>" placeholder="<?php echo $entry_sell_online_server; ?>" id="input-server" class="form-control" />
            </div>
</div>
<!-- CANADA POST SERVER -->



<!-- CANADA POST PORT -->
<div class="form-group required">
            <label class="col-sm-2 control-label" for="input-port"><?php echo $entry_port; ?></label>
            <div class="col-sm-10">
              <input type="text" name="canadapost_port" value="<?php echo $canadapost_port; ?>" placeholder="<?php echo $entry_port_default; ?>" id="input-port" class="form-control" />
            </div>
</div>
<!-- CANADA POST PORT -->


<!-- CANADA POST MERCHANT ID -->
<div class="form-group required">
            <label class="col-sm-2 control-label" for="input-merchantId"><?php echo $entry_merchantId; ?></label>
            <div class="col-sm-10">
              <input type="text" name="canadapost_merchantId" value="<?php echo $canadapost_merchantId; ?>" placeholder="<?php echo $entry_merchantId_Sample; ?>" id="input-merchantId" class="form-control" />
            </div>
          </div>
<!-- CANADA POST MERCHANT ID -->

					
<!-- ORIGIN POSTAL CODE -->						
					<div class="form-group required">
            <label class="col-sm-2 control-label" for="input-postcode"><?php echo $entry_origin; ?></label>
            <div class="col-sm-10">
              <input type="text" name="canadapost_origin" value="<?php echo $canadapost_origin; ?>" placeholder="<?php echo $entry_postcode; ?>" id="input-postcode" class="form-control" maxlength="6" />
              <?php if ($error_postcode) { ?>
              <div class="text-danger"><?php echo $error_postcode; ?></div>
              <?php } ?>
            </div>
          </div>
<!-- ORIGIN POSTAL CODE -->	


<!-- HANDLING FEE -->
<div class="form-group">
            <label class="col-sm-2 control-label" for="input-handling"><?php echo $entry_handling; ?></label>
            <div class="col-sm-10">
              <input type="text" name="canadapost_handling" value="<?php echo $canadapost_handling; ?>" placeholder="<?php echo $entry_handling_fee; ?>" id="input-handling" class="form-control" />
            </div>
          </div>
<!-- HANDLING FEE -->

<!-- TURN AROUND TIME -->
<div class="form-group">
            <label class="col-sm-2 control-label" for="input-turnAround"><?php echo $entry_turnAround; ?></label>
            <div class="col-sm-10">
              <input type="text" name="canadapost_turnAround" value="<?php echo $canadapost_turnAround; ?>" placeholder="<?php echo $entry_turnAround_time; ?>" id="input-turnAround" class="form-control" maxlength="2"/>
            </div>
          </div>
<!-- TURN AROUND TIME -->					

<!-- ORIGINAL PACKAGING -->
<div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_originalPackaging; ?></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <?php if ($canadapost_originalPackaging) { ?>
                <input type="radio" name="canadapost_originalPackaging" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <?php } else { ?>
                <input type="radio" name="canadapost_originalPackaging" value="1" />
                <?php echo $text_yes; ?>
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if (!$canadapost_originalPackaging) { ?>
                <input type="radio" name="canadapost_originalPackaging" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="canadapost_originalPackaging" value="0" />
                <?php echo $text_no; ?>
                <?php } ?>
              </label>
            </div>
          </div>	
<!-- ORIGINAL PACKAGING -->

<!-- TAX CLASS -->
 <div class="form-group">
            <label class="col-sm-2 control-label" for="input-tax-class"><?php echo $entry_tax_class; ?></label>
            <div class="col-sm-10">
              <select name="canadapost_tax_class_id" id="input-tax-class" class="form-control">
                <option value="0"><?php echo $text_none; ?></option>
                <?php foreach ($tax_classes as $tax_class) { ?>
                <?php if ($tax_class['tax_class_id'] == $canadapost_tax_class_id) { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>				
<!-- TAX CLASS -->

<!-- GEO ZONE -->
<div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
            <div class="col-sm-10">
              <select name="canadapost_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $canadapost_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
<!-- GEO ZONE -->

<!-- SORT ORDER -->
<div class="form-group">
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="canadapost_sort_order" value="<?php echo $canadapost_sort_order; ?>" placeholder="<?php echo $entry_sort_order_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>
<!-- SORT ORDER -->
					

</form>

</div><!-- div class panel-body -->

	
<!-- MODIFY SELLONLINE SETTINGS -->
<div class="panel-heading">
        <h3 class="panel-title" style="color:#1e91cf;"><?php echo '<a href="'.$entry_link_sellonline.'" target="_blank">'.$entry_sellonline.'</a>'; ?></h3>
      </div>
<!-- MODIFY SELLONLINE SETTINGS -->

<!-- VERSION STATUS -->
<div class="panel-heading">
        <p class="panel-title"><?php echo $entry_version_status_author . '<a href="'.$entry_version_status_link.'" target="_blank" style="color:#1e91cf;">'.$entry_version_status_company.'</a>'; ?></p>
      </div>
<!-- VERSION STATUS -->

<!-- AUTHORS AND CONTRIBUTORS -->
<div class="panel-heading">
        <h3 class="panel-title"><?php echo '<strong>'. "Authors and Contributors:".'</strong>'; ?></h3>
      </div>
<!-- AUTHORS AND CONTRIBUTORS -->

<!-- ORIGINAL AUTHORS -->
<div class="panel-heading">
        <p class="panel-title"><?php echo "Jason Mitchell (Version 1.0 to 1.1) " . '<font color="#1e91cf">' . "http://attemptone.com " . '</font>' . "(Dead Link)"; ?></p>
      </div>
<div class="panel-heading">
        <p class="panel-title"><?php echo "Olivier Labb&eacute; (Version 1.2 to 1.7(OC Version 1.4)) " . '<a href="http://www.votreespace.net" target="_blank" style="color:#1e91cf;">' . "http://www.votreespace.net" . '</a>'; ?></p>
      </div>
<div class="panel-heading">
        <p class="panel-title"><?php echo "Jeremy Langdon (OC Version 1.5.X) " . '<a href="http://www.aylingsbaby.com" target="_blank" style="color:#1e91cf;">' . "http://www.aylingsbaby.com" . '</a>'; ?></p>
      </div>									
<!-- ORIGINAL AUTHORS -->

<!-- CONTRIBUTORS -->
<div class="panel-heading">
        <p class="panel-title"><?php echo "Kevin Davidson Contribution: Fixed weight issue; Multiple items send the rates through the roof. " . '<font color="#1e91cf">' . "(Dead Link)Hosting, Design &amp; SEO Services. AVG Authorized Reseller" . '</font>'; ?></p>
      </div>	
<div class="panel-heading">
        <p class="panel-title"><?php echo "Tweaked by fmaz008, with help from Qphoria (our OpenCart Guru) Contribution: Shiping methods and &quot;Undefined index: code&quot; problem, in 1.5.x, &quot;id&quot; was changed to &quot;code&quot; in the returning data array. " . '<a href="http://opencartguru.com/" target="_blank" style="color:#1e91cf;">' . "Open Cart Guru" . '</a>'; ?></p>
      </div>		
<div class="panel-heading">
        <p class="panel-title"><?php echo "adamata Contribution: Formatted the totals to two decimal places." ;?></p>
      </div>	
<div class="panel-heading">
        <p class="panel-title"><?php echo "Stinn Contribution: Added code to actually add the handling fee." ;?></p>
      </div>				
<div class="panel-heading">
        <p class="panel-title"><?php echo "SuperJuice	Contribution: Canada Post Module modeled after AusPost Module for OpenCart 1.4.5." . '<a href="http://www.pixeldrift.net" target="_blank" style="color:#1e91cf;">' . " pixeldrift " . '</a>' . "and" . '<a href="http://addons.oscommerce.com/info/391" target="_blank" style="color:#1e91cf;">'. " Canada Post Shipping Module" . '</a>' . " for osCommerce by: Kelvin Zhang" ;?></p>
      </div>			
<!-- CONTRIBUTORS -->

</div><!-- div class panel panel-default -->
</div><!-- div class container-fluid -->
</div><!-- div id content -->
<?php echo $footer; ?>

