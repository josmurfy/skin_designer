<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><?php if($licensed_md5=='e9dc924f238fa6cc29465942875fe8f0'){ ?>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary gensave"><i class="fa fa-save"></i></button><?php } ?>
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
      <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
    </div>
    <div class="panel-body">
        <?php if ($error_warning || $error_duplicate) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo ($error_warning?$error_warning:$error_duplicate); ?>
          <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
      <?php if($licensed_md5=='e9dc924f238fa6cc29465942875fe8f0'){ ?>
    <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-uksb-google-merchant" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-google-settings" class="general2" data-toggle="tab"><?php echo $tab_google_settings; ?></a></li>
            <li><a href="#tab-google-feeds" class="general4" data-toggle="tab"><?php echo $tab_google_feeds; ?></a></li>
            <li><a href="javascript:void(0);" class="show_bulk"><?php echo $tab_bulk_update; ?></a></li>
            <li><a href="#tab-log" class="general3" data-toggle="tab"><?php echo $tab_log; ?></a></li>
            <li><a href="#tab-videos" class="general5" data-toggle="tab"><?php echo $tab_videos; ?></a></li>
          </ul>
      <div class="tab-content">    
    <div class="tab-pane active" id="tab-google-settings">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="order_details"><?php echo $entry_free_support; ?></label>
            <div class="col-sm-10">
              <?php if($support_status == 'enabled'){ ?><div class="table-responsive"> 
                <table id="order_details" class="table table-bordered table-striped">
                  <thead>
                    <tr>
                      <th class="text-center">Order ID</th><?php if($support_order_id < 10000000){ ?>
                      <th>Extension</th><?php } ?>
                      <th>Registered To</th>
                      <th class="text-center">Registered</th>
                      <th class="text-center">Support Ends</th>
                      <th class="text-center">Remove License</th>
                    </tr>
                  </thead>
                  <tbody>
                  <tr>
                    <td class="text-center"><?php echo $support_order_id; ?></td><?php if($support_order_id < 10000000){ ?>
                    <td><a href="https://www.opencart.com/index.php?route=marketplace/extension/info&extension_id=<?php echo $extension_id; ?>" target="_blank"><?php echo $support_extension_name; ?></a></td><?php } ?>
                    <td><?php echo $support_email; ?></td>
                    <td class="text-center"><?php echo $support_registered_date; ?></td>
                    <td class="text-center"><?php echo $support_order_date; ?></td>
                    <td class="text-center"><a href="<?php echo $link_deregister; ?>" onclick="return confirm('<?php echo $help_deregister; ?>');"><?php echo $text_deregister; ?></a></td>
                  </tr>
                  </tbody>
                  <tfooter>
                    <tr>
                      <td colspan="<?php echo ($support_order_id < 10000000 ? 6 : 5); ?>" class="text-center"><?php echo $text_free_support_remaining; ?></td>
                    </tr>
                  </tfooter>
                </table>
              </div><?php }else{ ?>
              <span id="order_details"><?php echo $text_free_support_remaining; ?></span>
              <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status1"><?php echo $entry_status; ?></label>
            <div class="col-sm-10"><input type="hidden" name="uksb_google_merchant_secure_code" value="<?php echo $uksb_google_merchant_secure_code; ?>">
              <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary<?php if ($uksb_google_merchant_status) { ?> active<?php } ?>">
                        <input type="radio" name="uksb_google_merchant_status" id="input-status1" value="1"<?php if ($uksb_google_merchant_status) { ?> checked<?php } ?>><?php echo $text_enabled; ?>
                    </label>
                    <label class="btn btn-primary<?php if (!$uksb_google_merchant_status) { ?> active<?php } ?>">
                        <input type="radio" name="uksb_google_merchant_status" id="input-status2" value="0"<?php if (!$uksb_google_merchant_status) { ?> checked<?php } ?>><?php echo $text_disabled; ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="input-characters1"><span data-toggle="tooltip" title="<?php echo $help_characters; ?>"><?php echo $entry_characters; ?></span></label>
            <div class="col-sm-10">
              <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary<?php if ($uksb_google_merchant_characters) { ?> active<?php } ?>">
                        <input type="radio" name="uksb_google_merchant_characters" id="input-characters1" value="1"<?php if ($uksb_google_merchant_characters) { ?> checked<?php } ?>><?php echo $text_enabled; ?>
                    </label>
                    <label class="btn btn-primary<?php if (!$uksb_google_merchant_characters) { ?> active<?php } ?>">
                        <input type="radio" name="uksb_google_merchant_characters" id="input-characters2" value="0"<?php if (!$uksb_google_merchant_characters) { ?> checked<?php } ?>><?php echo $text_disabled; ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="input-cron1"><span data-toggle="tooltip" title="<?php echo $help_cron; ?>"><?php echo $entry_cron; ?></span></label>
            <div class="col-sm-10"><input type="hidden" name="savecontinue" id="savecontinue" value="0">
              <div class="btn-group advanced" data-toggle="buttons">
                    <label class="btn btn-primary<?php if ($uksb_google_merchant_cron) { ?> active<?php } ?>">
                        <input type="radio" name="uksb_google_merchant_cron" id="input-cron1" value="1"<?php if ($uksb_google_merchant_cron) { ?> checked<?php } ?>><?php echo $text_enabled; ?>
                    </label>
                    <label class="btn btn-primary<?php if (!$uksb_google_merchant_cron) { ?> active<?php } ?>">
                        <input type="radio" name="uksb_google_merchant_cron" id="input-cron2" value="0"<?php if (!$uksb_google_merchant_cron) { ?> checked<?php } ?>><?php echo $text_disabled; ?>
                    </label>
                </div>
            </div>
        </div>
       <div class="form-group">
            <label class="col-sm-2 control-label" for="product-type"><span data-toggle="tooltip" title="<?php echo $help_product_type; ?>"><?php echo $entry_product_type; ?></span></label>
            <div class="col-sm-8"><input id="product-type" type="text" name="uksb_google_merchant_product_type" placeholder="" value="<?php echo $uksb_google_merchant_product_type; ?>" class="form-control" /></div>
       </div>
       <div class="form-group">
            <label class="col-sm-2 control-label" for="select-gpc"><span data-toggle="tooltip" title="<?php echo $help_google_category; ?><br><br><?php echo $entry_choose_google_category_xml; ?>"><?php echo $entry_google_category; ?></span></label>
            <div class="col-sm-10">

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_gb; ?>"><img src="view/image/flags/gb.png" title="<?php echo $country_gb; ?>" /></span></span><input id="select-gpc" type="text" name="uksb_google_merchant_google_category_gb" placeholder="<?php echo $text_gpc_gb; ?>" value="<?php echo $uksb_google_merchant_google_category_gb; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_us; ?>"><img src="view/image/flags/us.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_ca; ?>"><img src="view/image/flags/ca.png" /></span></span> <span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_zz; ?>"><img src="view/image/flags/zz.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_us" placeholder="<?php echo $text_gpc_us; ?>" value="<?php echo $uksb_google_merchant_google_category_us; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=en-US','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_au; ?>"><img src="view/image/flags/au.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_au" placeholder="<?php echo $text_gpc_au; ?>" value="<?php echo $uksb_google_merchant_google_category_au; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=en-AU','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_fr; ?>"><img src="view/image/flags/fr.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_be; ?>"><img src="view/image/flags/be.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_ca; ?>"><img src="view/image/flags/ca.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_ch; ?>"><img src="view/image/flags/ch.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_fr" placeholder="<?php echo $text_gpc_fr; ?>" value="<?php echo $uksb_google_merchant_google_category_fr; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=fr-FR','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_de; ?>"><img src="view/image/flags/de.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_at; ?>"><img src="view/image/flags/at.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_ch; ?>"><img src="view/image/flags/ch.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_de" placeholder="<?php echo $text_gpc_de; ?>" value="<?php echo $uksb_google_merchant_google_category_de; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=de-DE','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_it; ?>"><img src="view/image/flags/it.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_ch; ?>"><img src="view/image/flags/ch.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_it" placeholder="<?php echo $text_gpc_it; ?>" value="<?php echo $uksb_google_merchant_google_category_it; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=it-IT','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_nl; ?>"><img src="view/image/flags/nl.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_be; ?>"><img src="view/image/flags/be.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_nl" placeholder="<?php echo $text_gpc_nl; ?>" value="<?php echo $uksb_google_merchant_google_category_nl; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=nl-NL','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_es; ?>"><img src="view/image/flags/es.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_mx; ?>"><img src="view/image/flags/mx.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_ar; ?>"><img src="view/image/flags/ar.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_cl; ?>"><img src="view/image/flags/cl.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_co; ?>"><img src="view/image/flags/co.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_es" placeholder="<?php echo $text_gpc_es; ?>" value="<?php echo $uksb_google_merchant_google_category_es; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=es-ES','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_pt; ?>"><img src="view/image/flags/pt.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_br; ?>"><img src="view/image/flags/br.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_pt" placeholder="<?php echo $text_gpc_pt; ?>" value="<?php echo $uksb_google_merchant_google_category_pt; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=pt-BR','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_cz; ?>"><img src="view/image/flags/cz.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_cz" placeholder="<?php echo $text_gpc_cz; ?>" value="<?php echo $uksb_google_merchant_google_category_cz; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=cs-CZ','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_jp; ?>"><img src="view/image/flags/jp.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_jp" placeholder="<?php echo $text_gpc_jp; ?>" value="<?php echo $uksb_google_merchant_google_category_jp; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=ja-JP','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_dk; ?>"><img src="view/image/flags/dk.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_dk" placeholder="<?php echo $text_gpc_da; ?>" value="<?php echo $uksb_google_merchant_google_category_dk; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=da-DK','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_no; ?>"><img src="view/image/flags/no.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_no" placeholder="<?php echo $text_gpc_no; ?>" value="<?php echo $uksb_google_merchant_google_category_no; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=no-NO','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_pl; ?>"><img src="view/image/flags/pl.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_pl" placeholder="<?php echo $text_gpc_pl; ?>" value="<?php echo $uksb_google_merchant_google_category_pl; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=pl-PL','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_ru; ?>"><img src="view/image/flags/ru.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_ru" placeholder="<?php echo $text_gpc_ru; ?>" value="<?php echo $uksb_google_merchant_google_category_ru; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=ru-RU','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_se; ?>"><img src="view/image/flags/se.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_sv" placeholder="<?php echo $text_gpc_sv; ?>" value="<?php echo $uksb_google_merchant_google_category_sv; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=sv-SE','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_tr; ?>"><img src="view/image/flags/tr.png" /></span></span><input type="text" name="uksb_google_merchant_google_category_tr" placeholder="<?php echo $text_gpc_tr; ?>" value="<?php echo $uksb_google_merchant_google_category_tr; ?>" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=tr-TR','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div>

            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="select-mpn"><span data-toggle="tooltip" title="<?php echo $help_mpn; ?>"><?php echo $entry_mpn; ?></span></label>
            <div class="col-sm-10">
              <select name="uksb_google_merchant_mpn" id="select-mpn" class="form-control">
                <option value="sku"<?php if ($uksb_google_merchant_mpn=='sku') { ?> selected="selected"<?php } ?>><?php echo $text_sku; ?></option>
                <option value="model"<?php if (!$uksb_google_merchant_mpn||$uksb_google_merchant_mpn=='model') { ?> selected="selected"<?php } ?>><?php echo $text_model; ?></option>
                <option value="mpn"<?php if ($uksb_google_merchant_mpn=='mpn') { ?> selected="selected"<?php } ?>><?php echo $text_mpn; ?></option>
                <option value="location"<?php if ($uksb_google_merchant_mpn=='location') { ?> selected="selected"<?php } ?>><?php echo $text_location; ?></option>
              </select></div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="select-gtin"><span data-toggle="tooltip" title="<?php echo $help_gtin; ?>"><?php echo $entry_gtin; ?></span></label>
            <div class="col-sm-10">
              <select name="uksb_google_merchant_g_gtin" id="select-gtin" class="form-control">
                <option value="default"<?php if (!$uksb_google_merchant_g_gtin||$uksb_google_merchant_g_gtin=='default') { ?> selected="selected"<?php } ?>><?php echo $text_default; ?></option>
                <option value="sku"<?php if ($uksb_google_merchant_g_gtin=='sku') { ?> selected="selected"<?php } ?>><?php echo $text_sku; ?></option>
                <option value="gtin"<?php if ($uksb_google_merchant_g_gtin=='gtin') { ?> selected="selected"<?php } ?>><?php echo $text_gtin; ?></option>
                <option value="location"<?php if ($uksb_google_merchant_g_gtin=='location') { ?> selected="selected"<?php } ?>><?php echo $text_location; ?></option>
              </select></div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="select-stock_checkout"><span data-toggle="tooltip" title="<?php echo $help_stock_checkout; ?>"><?php echo $entry_stock_checkout; ?></span></label>
            <div class="col-sm-10">
              <select name="uksb_google_merchant_stock_checkout" id="select-stock_checkout" class="form-control">
                <option value="out of stock"<?php if ($uksb_google_merchant_stock_checkout == 'out of stock' ){ ?> selected="selected"<?php } ?>><?php echo $text_out_of_stock; ?></option>
                <option value="in stock"<?php if ($uksb_google_merchant_stock_checkout == 'in stock' ){ ?> selected="selected"<?php } ?>><?php echo $text_in_stock; ?></option>
              </select></div>
          </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="input-micro1"><span data-toggle="tooltip" title="<?php echo $help_micro_data; ?>"><?php echo $entry_micro_data; ?></span></label>
            <div class="col-sm-10">
              <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-primary<?php if ($uksb_google_merchant_micro_data) { ?> active<?php } ?>">
                        <input type="radio" name="uksb_google_merchant_micro_data" id="input-micro1" value="1"<?php if ($uksb_google_merchant_micro_data) { ?> checked<?php } ?>><?php echo $text_enabled; ?>
                    </label>
                    <label class="btn btn-primary<?php if (!$uksb_google_merchant_micro_data) { ?> active<?php } ?>">
                        <input type="radio" name="uksb_google_merchant_micro_data" id="input-micro2" value="0"<?php if (!$uksb_google_merchant_micro_data) { ?> checked<?php } ?>><?php echo $text_disabled; ?>
                    </label>
                </div>
            </div>
        </div>        
        <div class="form-group">
          <div class="col-sm-2"></div>
          <div class="col-sm-10">
            <h3 class="panel-heading"><?php echo $new_products_title; ?></h3>
            <p><?php echo $help_new_products; ?></p>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-uksb_google_merchant_defaults_on_google1"><span data-toggle="tooltip" title="<?php echo $help_on_google; ?>"><?php echo $entry_p_on_google; ?></span></label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary<?php if (($uksb_google_merchant_defaults_on_google) || ((!$uksb_google_merchant_defaults_on_google) && $uksb_google_merchant_defaults_on_google != '0')){ ?> active<?php } ?>">
                <input type="radio" name="uksb_google_merchant_defaults_on_google" id="input-uksb_google_merchant_defaults_on_google1" value="1"<?php if (($uksb_google_merchant_defaults_on_google) || ((!$uksb_google_merchant_defaults_on_google) && $uksb_google_merchant_defaults_on_google != '0')){ ?> checked<?php } ?>><?php echo $text_yes; ?>
              </label>
              <label class="btn btn-primary<?php if ($uksb_google_merchant_defaults_on_google == '0'){ ?> active<?php } ?>">
                <input type="radio" name="uksb_google_merchant_defaults_on_google" id="input-uksb_google_merchant_defaults_on_google2" value="0"<?php if ($uksb_google_merchant_defaults_on_google == '0'){ ?> checked<?php } ?>><?php echo $text_no; ?>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-uksb_google_merchant_defaults_shipping_label"><span data-toggle="tooltip" title="<?php echo $help_shipping_label; ?>"><?php echo $entry_p_shipping_label; ?></span></label>
          <div class="col-sm-10">
            <input type="text" name="uksb_google_merchant_defaults_shipping_label" value="<?php echo $uksb_google_merchant_defaults_shipping_label; ?>" placeholder="<?php echo $entry_p_shipping_label; ?>" id="input-uksb_google_merchant_defaults_shipping_label" class="form-control" />
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-uksb_google_merchant_defaults_identifier_exists1"><span data-toggle="tooltip" title="<?php echo $help_identifier_exists; ?>"><?php echo $entry_p_identifier_exists; ?></span></label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary<?php if (($uksb_google_merchant_defaults_identifier_exists) || ((!$uksb_google_merchant_defaults_identifier_exists) && $uksb_google_merchant_defaults_identifier_exists != '0')){ ?> active<?php } ?>">
                <input type="radio" name="uksb_google_merchant_defaults_identifier_exists" id="input-uksb_google_merchant_defaults_identifier_exists1" value="1"<?php if (($uksb_google_merchant_defaults_identifier_exists) || ((!$uksb_google_merchant_defaults_identifier_exists) && $uksb_google_merchant_defaults_identifier_exists != '0')){ ?> checked<?php } ?>><?php echo $text_yes; ?>
              </label>
              <label class="btn btn-primary<?php if ($uksb_google_merchant_defaults_identifier_exists == '0'){ ?> active<?php } ?>">
                <input type="radio" name="uksb_google_merchant_defaults_identifier_exists" id="input-uksb_google_merchant_defaults_identifier_exists2" value="0"<?php if ($uksb_google_merchant_defaults_identifier_exists == '0'){ ?> checked<?php } ?>><?php echo $text_no; ?>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-uksb_google_merchant_defaults_condition1"><span data-toggle="tooltip" title="<?php echo $help_condition; ?>"><?php echo $entry_p_condition; ?></span></label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary<?php if (!$uksb_google_merchant_defaults_condition || $uksb_google_merchant_defaults_condition == 'new'){ ?> active<?php } ?>">
                <input type="radio" name="uksb_google_merchant_defaults_condition" id="input-uksb_google_merchant_defaults_condition1" value="new"<?php if (!$uksb_google_merchant_defaults_condition ||$uksb_google_merchant_defaults_condition == 'new'){ ?> checked<?php } ?>><?php echo $text_condition_new; ?>
              </label>
              <label class="btn btn-primary<?php if ($uksb_google_merchant_defaults_condition == 'used'){ ?> active<?php } ?>">
                <input type="radio" name="uksb_google_merchant_defaults_condition" id="input-uksb_google_merchant_defaults_condition2" value="used"<?php if ($uksb_google_merchant_defaults_condition == 'used'){ ?> checked<?php } ?>><?php echo $text_condition_used; ?>
              </label>
              <label class="btn btn-primary<?php if ($uksb_google_merchant_defaults_condition == 'refurbished'){ ?> active<?php } ?>">
                <input type="radio" name="uksb_google_merchant_defaults_condition" id="input-uksb_google_merchant_defaults_condition3" value="refurbished"<?php if ($uksb_google_merchant_defaults_condition == 'refurbished'){ ?> checked<?php } ?>><?php echo $text_condition_ref; ?>
              </label>
            </div>
          </div>
        </div>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_info; ?></label>
            <div class="col-sm-10"><?php echo $help_google_help . '<br><br>' . $help_info; ?></div>
          </div>
        </div>
         <div class="tab-pane" id="tab-google-feeds">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="google-site"><span data-toggle="tooltip" title="<?php echo $help_site; ?>"><?php echo $entry_site; ?></span></label>
            <div class="col-sm-10">
              <select name="uksb_google_merchant_site" id="google_site" class="form-control">
                <option value="default" selected="selected"><?php echo $text_choose_google_site; ?></option>
                <option value="gb">(en-GBP) United Kingdom</option>
                <option value="us">(en-USD) United States of America</option>
                <option value="ca">(en-CAD) Canada (English)</option>
                <option value="ca_fr">(fr-CAD) Canada (Français)</option>
                <option value="mx">(es-MXN) México</option>
                <option value="au">(en-AUD) Australia</option>
                <option value="fr">(fr-EUR) France</option>
                <option value="de">(de-EUR) Deutschland</option>
                <option value="it">(it-EUR) Italia</option>
                <option value="nl">(nl-EUR) Nederlands</option>
                <option value="ar">(es-ARS) Argentina (Español)</option>
                <option value="cl">(es-CLP) Chile (Español)</option>
                <option value="co">(es-COP) Colombia (Español)</option>
                <option value="es">(es-EUR) España</option>
                <option value="be_nl">(nl-EUR) België (Nederlands)</option>
                <option value="be_fr">(fr-EUR) Belgique (Français)</option>
                <option value="at_de">(de-EUR) Österreich</option>
                <option value="dk">(da-DKK) Danmark</option>
                <option value="no">(no-NOK) Norge</option>
                <option value="sv">(sv-SEK) Sverige</option>
                <option value="pl">(pl-PLN) Polska</option>
                <option value="cs">(cs-CZK) Československo</option>
                <option value="ch_fr">(fr-CHF) Suisse (Français)</option>
                <option value="ch_de">(de-CHF) Schweiz (Deutsch)</option>
                <option value="ch_it">(it-CHF) Svizzera (Italiano)</option>
                <option value="ru">(ru-RUB) Россия</option>
                <option value="tr">(tr-TRY) Türkiye</option>
                <option value="pt">(pt-EUR) Portugal</option>
                <option value="pt_br">(pt-BRL) Brasil</option>
                <option value="ja">(ja-JPY) 日本</option>
                <option value="zz">All Other Countries</option>
              </select></div>
          </div>
          <div id="other_language_currency" class="form-group" style="display:none;">
                <div class="row">
                  <div class="col-sm-2">
                  </div>
                  <div class="col-sm-10">
                    <p><?php echo $help_all_other_countries; ?></p>
                  </div>
                </div>
                <div class="row">
                  <label class="col-sm-2 control-label"></label>
                  <div class="col-sm-5">
                    <select name="uksb_google_merchant_other_language" id="other_language" class="form-control">
                      <option value="" selected="selected"><?php echo $text_choose_language; ?></option>
                      <?php foreach ($other_languages as $language ){ ?>
                      <option value="<?php echo $language['code']; ?>">(<?php echo $language['code']; ?>) <?php echo $language['name']; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                  <div class="col-sm-5">
                    <select name="uksb_google_merchant_other_currency" id="other_currency" class="form-control">
                      <option value="" selected="selected"><?php echo $text_choose_currency; ?></option>
                      <?php foreach ($other_currencies as $currency){ ?>
                      <option value="<?php echo $currency['code']; ?>">(<?php echo $currency['code']; ?>) <?php echo $currency['title']; ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="notax"><span data-toggle="tooltip" title="<?php echo $help_notax; ?>"><?php echo $entry_notax; ?></span></label>
            <div class="col-sm-10">              
              <div class="btn-group" data-toggle="buttons">
                      <label class="btn btn-primary">
                          <input type="radio" name="notax" id="notax" value="1"><?php echo $text_enabled; ?>
                      </label>
                      <label class="btn btn-primary active">
                          <input type="radio" name="notax" value="0" checked><?php echo $text_disabled; ?>
                      </label>
                  </div>
              </div>
          </div>
          <?php if($config_cron){ ?>
          <div class="form-group">
            <div class="col-sm-2">&nbsp;</div>
            <div class="col-sm-10"><?php echo $help_cron_code; ?></div>
          </div>
          <?php } ?>
          <?php
          $feeds = explode("^", $data_feed);
          $crons = ($config_cron?explode("^", $data_cron_path):'');
          $i=0;
          foreach (array_keys($feeds) as $key) {
            if($config_cron){ ?>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="cron_code_<?php echo $i; ?>"><?php echo $entry_cron_code; ?></label>
          <div class="col-sm-10"><textarea id="cron_code_<?php echo $i; ?>" rows="2" class="form-control" readonly onclick="$(this).select()"></textarea></div>
          </div>
            <?php } ?>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="feed_url_<?php echo $i; ?>"><?php echo $entry_data_feed; ?></label>
          <div class="col-sm-10"><textarea id="feed_url_<?php echo $i; ?>" rows="2" class="form-control" readonly onclick="$(this).select()"></textarea></div>
          </div>
          <?php
          $i++;
          } ?>
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_info; ?></label>
            <div class="col-sm-10"><?php echo $help_google_help . '<br><br>' . $help_info; ?></div>
          </div>
        </div>
        <div class="tab-pane" id="tab-log">
          <div class="form-group">
              <label class="col-sm-2 control-label" for="input-logging1"><span data-toggle="tooltip" title="<?php echo $help_logging; ?>"><?php echo $entry_logging; ?></span></label>
              <div class="col-sm-10">
                <div class="btn-group" data-toggle="buttons">
                      <label class="btn btn-primary<?php if($uksb_google_merchant_logging){ ?> active<?php } ?>">
                          <input type="radio" name="uksb_google_merchant_logging" id="input-logging1" value="1"<?php if($uksb_google_merchant_logging){ ?> checked<?php } ?>><?php echo $text_enabled; ?>
                      </label>
                      <label class="btn btn-primary<?php if(!$uksb_google_merchant_logging){ ?> active<?php } ?>">
                          <input type="radio" name="uksb_google_merchant_logging" id="input-logging2" value="0"<?php if(!$uksb_google_merchant_logging){ ?> checked<?php } ?>><?php echo $text_disabled; ?>
                      </label>
                </div>
              </div>
          </div>
          <p>
            <textarea wrap="off" rows="15" readonly class="form-control"><?php echo $log; ?></textarea>
          </p>
          <div class="text-center"><a href="<?php echo $clear_log; ?>" class="btn btn-danger"><i class="fa fa-eraser"></i> <?php echo $button_clear; ?></a></div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_info; ?></label>
            <div class="col-sm-10"><?php echo $help_google_help . '<br><br>' . $help_info; ?></div>
          </div>
        </div>
        <div class="tab-pane" id="tab-videos">
          <div class="form-group">
              <div class="col-xs-12 col-sm-12 col-md-12 col-lg-8 embed-responsive embed-responsive-16by9"><iframe src="https://vimeo.com/showcase/6518578/embed" frameborder="0" allowfullscreen></iframe></div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_info; ?></label>
            <div class="col-sm-10"><?php echo $help_info; ?></div>
          </div>
        </div>
      </div>        
    </form>
    <form style="display:none;" action="<?php echo $action_bulk_update; ?>" method="post" enctype="multipart/form-data" id="form-bulk-update" class="form-horizontal">
          <ul class="nav nav-tabs">
            <li><a href="javascript:void(0);" class="show_general2"><?php echo $tab_google_settings; ?></a></li>
            <li><a href="javascript:void(0);" class="show_general4"><?php echo $tab_google_feeds; ?></a></li>
            <li class="active"><a href="#tab-bulk_update" data-toggle="tab"><?php echo $tab_bulk_update; ?></a></li>
            <li><a href="javascript:void(0);" class="show_general3"><?php echo $tab_log; ?></a></li>
            <li><a href="javascript:void(0);" class="show_general5"><?php echo $tab_videos; ?></a></li>
          </ul>
      <div class="tab-content">    
        <?php echo $help_bulk_update_info; ?>
        <div class="tab-pane active" id="tab-bulk_update">
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_on_google1"><span data-toggle="tooltip" title="<?php echo $help_p_on_google; ?>"><?php echo $entry_p_on_google; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_on_google" value="0" checked></label></label>
          
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="radio" name="g_on_google" id="input-g_on_google1" value="1" checked><?php echo $text_yes; ?>
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="g_on_google" id="input-g_on_google2" value="0"><?php echo $text_no; ?>
              </label>
            </div>
          </div>
        </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_shipping_label"><span data-toggle="tooltip" title="<?php echo $help_p_shipping_label; ?>"><?php echo $entry_p_shipping_label; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_shipping_label" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_shipping_label" value="" placeholder="<?php echo $entry_p_shipping_label; ?>" id="input-g_shipping_label" class="form-control" />
          </div>
          </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_promotion_id"><span data-toggle="tooltip" title="<?php echo $help_p_promotion_id; ?>"><?php echo $entry_p_promotion_id; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_promotion_id" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_promotion_id" value="" placeholder="<?php echo $entry_p_promotion_id; ?>" id="input-g_promotion_id" class="form-control" />
          </div>
          </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-p_expiry_date"><span data-toggle="tooltip" title="<?php echo $help_p_expiry_date; ?>"><?php echo $entry_p_expiry_date; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_expiry_date" value="0" checked></label></label>
          <div class="col-sm-3">
            <div class="input-group expiry-date">
            <input type="text" name="g_expiry_date" value="" placeholder="<?php echo $entry_p_expiry_date; ?>" data-date-format="YYYY-MM-DD" id="input-p_expiry_date" class="form-control" />
            <span class="input-group-btn">
            <button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
            </span></div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_identifier_exists1"><span data-toggle="tooltip" title="<?php echo $help_p_identifier_exists; ?>"><?php echo $entry_p_identifier_exists; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_identifier_exists" value="0" checked></label></label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="radio" name="g_identifier_exists" id="input-g_identifier_exists1" value="1" checked><?php echo $text_yes; ?>
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="g_identifier_exists" id="input-g_identifier_exists2" value="0"><?php echo $text_no; ?>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_condition1"><span data-toggle="tooltip" title="<?php echo $help_p_condition; ?>"><?php echo $entry_p_condition; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_condition" value="0" checked></label></label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="radio" name="g_condition" id="input-g_condition1" value="new" checked><?php echo $text_condition_new; ?>
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="g_condition" id="input-g_condition2" value="used"><?php echo $text_condition_used; ?>
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="g_condition" id="input-g_condition3" value="refurbished"><?php echo $text_condition_ref; ?>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_brand"><span data-toggle="tooltip" title="<?php echo $help_p_brand; ?>"><?php echo $entry_p_brand; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_brand" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_brand" value="" placeholder="<?php echo $entry_p_brand; ?>" id="input-g_brand" class="form-control" />
          </div>
        </div>
       <div class="form-group">
            <label class="col-sm-2 control-label" for="input-g_product_type"><span data-toggle="tooltip" title="<?php echo $help_p_product_type; ?>"><?php echo $entry_p_product_type; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_product_type" value="0" checked></label></label>
            <div class="col-sm-10"><input id="input-g_product_type" type="text" name="g_product_type" placeholder="<?php echo $entry_p_product_type; ?>" class="form-control" /></div>
       </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="select-gpc"><span data-toggle="tooltip" title="<?php echo $help_p_google_category; ?><br><br><?php echo $entry_choose_google_category_xml; ?>"><?php echo $entry_p_google_category; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_google_categories" value="0" checked></label></label>
          <div class="col-sm-8">
              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_gb; ?>"><img src="view/image/flags/gb.png" title="Great Britain" /></span></span><input id="select-gpc" type="text" name="google_category_gb" placeholder="<?php echo $text_gpc_gb; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_us; ?>"><img src="view/image/flags/us.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_ca; ?>"><img src="view/image/flags/ca.png" /></span></span> <span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_zz; ?>"><img src="view/image/flags/zz.png" /></span></span><input type="text" name="google_category_us" placeholder="<?php echo $text_gpc_us; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=en-US','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_au; ?>"><img src="view/image/flags/au.png" /></span></span><input type="text" name="google_category_au" placeholder="<?php echo $text_gpc_au; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=en-AU','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_fr; ?>"><img src="view/image/flags/fr.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_be; ?>"><img src="view/image/flags/be.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_ca; ?>"><img src="view/image/flags/ca.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_ch; ?>"><img src="view/image/flags/ch.png" /></span></span><input type="text" name="google_category_fr" placeholder="<?php echo $text_gpc_fr; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=fr-FR','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_de; ?>"><img src="view/image/flags/de.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_at; ?>"><img src="view/image/flags/at.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_ch; ?>"><img src="view/image/flags/ch.png" /></span></span><input type="text" name="google_category_de" placeholder="<?php echo $text_gpc_de; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=de-DE','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_it; ?>"><img src="view/image/flags/it.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_ch; ?>"><img src="view/image/flags/ch.png" /></span></span><input type="text" name="google_category_it" placeholder="<?php echo $text_gpc_it; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=it-IT','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_nl; ?>"><img src="view/image/flags/nl.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_be; ?>"><img src="view/image/flags/be.png" /></span></span><input type="text" name="google_category_nl" placeholder="<?php echo $text_gpc_nl; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=nl-NL','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_es; ?>"><img src="view/image/flags/es.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_mx; ?>"><img src="view/image/flags/mx.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_ar; ?>"><img src="view/image/flags/ar.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_cl; ?>"><img src="view/image/flags/cl.png" /></span> <span data-toggle="tooltip" title="<?php echo $country_co; ?>"><img src="view/image/flags/co.png" /></span></span><input type="text" name="google_category_es" placeholder="<?php echo $text_gpc_es; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=es-ES','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_pt; ?>"><img src="view/image/flags/pt.png" /></span></span><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_br; ?>"><img src="view/image/flags/br.png" /></span></span><input type="text" name="google_category_pt" placeholder="<?php echo $text_gpc_pt; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=pt-BR','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_cz; ?>"><img src="view/image/flags/cz.png" /></span></span><input type="text" name="google_category_cz" placeholder="<?php echo $text_gpc_cz; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=cs-CZ','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_jp; ?>"><img src="view/image/flags/jp.png" /></span></span><input type="text" name="google_category_jp" placeholder="<?php echo $text_gpc_jp; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=ja-JP','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_dk; ?>"><img src="view/image/flags/dk.png" /></span></span><input type="text" name="google_category_dk" placeholder="<?php echo $text_gpc_da; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=da-DK','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_no; ?>"><img src="view/image/flags/no.png" /></span></span><input type="text" name="google_category_no" placeholder="<?php echo $text_gpc_no; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=no-NO','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_pl; ?>"><img src="view/image/flags/pl.png" /></span></span><input type="text" name="google_category_pl" placeholder="<?php echo $text_gpc_pl; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=pl-PL','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_ru; ?>"><img src="view/image/flags/ru.png" /></span></span><input type="text" name="google_category_ru" placeholder="<?php echo $text_gpc_ru; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=ru-RU','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_se; ?>"><img src="view/image/flags/se.png" /></span></span><input type="text" name="google_category_sv" placeholder="<?php echo $text_gpc_sv; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=sv-SE','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div><br>

              <div class="input-group"><span class="input-group-addon"><span data-toggle="tooltip" title="<?php echo $country_tr; ?>"><img src="view/image/flags/tr.png" /></span></span><input type="text" name="google_category_tr" placeholder="<?php echo $text_gpc_tr; ?>" value="" class="form-control" /><span class="input-group-addon"><a onclick="window.open('<?php echo $home; ?>taxonomy.php?lang=tr-TR','google');"><i data-toggle="tooltip" class="fa fa-plus-circle" style="cursor:pointer;" title="<?php echo $entry_choose_google_category; ?>"></i></a></span></div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_multipack"><span data-toggle="tooltip" title="<?php echo $help_p_multipack; ?>"><?php echo $entry_p_multipack; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_multipack" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_multipack" value="" placeholder="<?php echo $entry_p_multipack; ?>" id="input-g_multipack" class="form-control" />
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_is_bundle1"><span data-toggle="tooltip" title="<?php echo $help_p_is_bundle; ?>"><?php echo $entry_p_is_bundle; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_is_bundle" value="0" checked></label></label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary">
                <input type="radio" name="g_is_bundle" id="input-g_is_bundle1" value="1"><?php echo $text_yes; ?>
              </label>
              <label class="btn btn-primary active">
                <input type="radio" name="g_is_bundle" id="input-g_is_bundle2" value="0" checked><?php echo $text_no; ?>
              </label>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_adult1"><span data-toggle="tooltip" title="<?php echo $help_p_adult; ?>"><?php echo $entry_p_adult; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_adult" value="0" checked></label></label>
          <div class="col-sm-10">
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary">
                <input type="radio" name="g_adult" id="input-g_adult1" value="1"><?php echo $text_yes; ?>
              </label>
              <label class="btn btn-primary active">
                <input type="radio" name="g_adult" id="input-g_adult2" value="0" checked><?php echo $text_no; ?>
              </label>
            </div>
          </div>
        </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="select-g_energy_efficiency_class"><span data-toggle="tooltip" title="<?php echo $help_p_energy_efficiency_class; ?>"><?php echo $entry_p_energy_efficiency_class; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_energy_efficiency_class" value="0" checked></label></label>
                <div class="col-sm-10">
                  <select name="g_energy_efficiency_class" id="select-g_energy_efficiency_class" class="form-control">
                      <option value="0" selected="selected"><?php echo $text_none; ?></option>
                      <option value="A+++">A+++</option>
                      <option value="A++">A++</option>
                      <option value="A+">A+</option>
                      <option value="A">A</option>
                      <option value="B">B</option>
                      <option value="C">C</option>
                      <option value="D">D</option>
                      <option value="E">E</option>
                      <option value="F">F</option>
                      <option value="G">G</option>
                  </select></div>
              </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_unit_pricing_measure"><span data-toggle="tooltip" title="<?php echo $help_p_unit_pricing_measure; ?>"><?php echo $entry_p_unit_pricing_measure; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_unit_pricing_measure" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_unit_pricing_measure" value="" placeholder="<?php echo $entry_p_unit_pricing_measure; ?>" id="input-g_unit_pricing_measure" class="form-control" />
          </div>
          </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_unit_pricing_base_measure"><span data-toggle="tooltip" title="<?php echo $help_p_unit_pricing_base_measure; ?>"><?php echo $entry_p_unit_pricing_base_measure; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_unit_pricing_base_measure" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_unit_pricing_base_measure" value="" placeholder="<?php echo $entry_p_unit_pricing_base_measure; ?>" id="input-g_unit_pricing_base_measure" class="form-control" />
          </div>
          </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="select-g_gender"><?php echo $entry_p_gender; ?><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_gender" value="0" checked></label></label>
                <div class="col-sm-10">
                  <select name="g_gender" id="select-g_gender" class="form-control">
                      <option value="0" selected="selected"><?php echo $text_none; ?></option>
                      <option value="male"><?php echo $text_male; ?></option>
                      <option value="female"><?php echo $text_female; ?></option>
                      <option value="unisex"><?php echo $text_unisex; ?></option>
                  </select></div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="select-g_age_group"><?php echo $entry_p_age_group; ?><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_age_group" value="0" checked></label></label>
                <div class="col-sm-10">
                  <select name="g_age_group" id="select-g_age_group" class="form-control">
                      <option value="0" selected="selected"><?php echo $text_none; ?></option>
                      <option value="newborn"><?php echo $text_newborn; ?></option>
                      <option value="infant"><?php echo $text_infant; ?></option>
                      <option value="toddler"><?php echo $text_toddler; ?></option>
                      <option value="kids"><?php echo $text_kids; ?></option>
                      <option value="adult"><?php echo $text_adult; ?></option>
                  </select></div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="select-size_type"><span data-toggle="tooltip" title="<?php echo $help_p_size_type; ?>"><?php echo $entry_p_size_type; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_size_type" value="0" checked></label></label>
                <div class="col-sm-10">
                  <select name="g_size_type" id="select-g_size_type" class="form-control">
                      <option value="0" selected="selected"><?php echo $text_none; ?></option>
                      <option value="regular"><?php echo $text_regular; ?></option>
                      <option value="petite"><?php echo $text_petite; ?></option>
                      <option value="plus"><?php echo $text_plus; ?></option>
                      <option value="big and tall"><?php echo $text_big_and_tall; ?></option>
                      <option value="maternity"><?php echo $text_maternity; ?></option>
                  </select></div>
              </div>
              <div class="form-group">
                <label class="col-sm-2 control-label" for="select-size_system"><span data-toggle="tooltip" title="<?php echo $help_p_size_system; ?>"><?php echo $entry_p_size_system; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_size_system" value="0" checked></label></label>
                <div class="col-sm-10">
                  <select name="g_size_system" id="select-g_size_system" class="form-control">
                      <option value="0" selected="selected"><?php echo $text_none; ?></option>
                      <option value="US">US</option>
                      <option value="UK">UK</option>
                      <option value="EU">EU</option>
                      <option value="DE">DE</option>
                      <option value="FR">FR</option>
                      <option value="JP">JP</option>
                      <option value="CN (China)">CN (China)</option>
                      <option value="IT">IT</option>
                      <option value="BR">BR</option>
                      <option value="MEX">MEX</option>
                      <option value="AU">AU</option>
                  </select></div>
              </div>
              <div class="form-group">
                <div class="col-sm-2">&nbsp;</div>
                <div class="col-sm-10"><h2><?php echo $entry_variant_section; ?></h2><?php echo $help_variant_bulk; ?><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_variants" value="0" checked></label></div>
          </div>
                  <div class="table-responsive">
                    <table id="variants" class="table table-striped table-bordered table-hover">
                      <tbody>
                        <tr id="variant-row0">
                          <td class="col-sm-3 text-left">
                <label class="control-label" for="variant[0][g_size]"><?php echo $entry_p_size; ?></label><br>
                <input type="text" id="variant[0][g_size]" name="variant[0][g_size]" value="" placeholder="<?php echo $entry_p_size; ?>" class="form-control" /><br><br>
                <label class="control-label" for="variant[0][g_material]"><?php echo $entry_p_material; ?></label><br>
                <input type="text" id="variant[0][g_material]" name="variant[0][g_material]" value="" placeholder="<?php echo $entry_p_material; ?>" class="form-control" /></td>
                          <td class="col-sm-3 text-left">
                <label class="control-label" for="variant[0][g_colour]"><?php echo $entry_p_colour; ?></label><br>
                <input type="text" id="variant[0][g_colour]" name="variant[0][g_colour]" value="" placeholder="<?php echo $entry_p_colour; ?>" class="form-control" /><br><br>
                <label class="control-label" for="variant[0][g_pattern]"><?php echo $entry_p_pattern; ?></label><br>
                <input type="text" id="variant[0][g_pattern]" name="variant[0][g_pattern]" value="" placeholder="<?php echo $entry_p_pattern; ?>" class="form-control" /></td>
                          <td class="col-sm-3 text-left">
                <label class="control-label" for="variant[0][v_prices]"><?php echo $entry_v_prices; ?></label><br><br><?php echo $help_v_prices; ?><br><br>
                <input type="text" id="variant[0][v_prices]" name="variant[0][v_prices]" value="" placeholder="<?php echo $entry_v_prices; ?>" class="form-control" /></td>
                          <td class="col-sm-2 text-left"><a href="" id="thumb-image0" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="variant[0][v_images]" value="" id="v_images0" /></td>
                          <td class="text-left"><button type="button" onclick="$('#variant-row0').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>
                        </tr>
                      </tbody>
                      <tfoot>
                        <tr>
                          <td colspan="4"></td>
                          <td class="col-sm-1 text-left"><button type="button" onclick="addVariant();" data-toggle="tooltip" title="<?php echo $button_add_variant; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
              <div class="form-group">
                <div class="col-sm-2">&nbsp;</div>
                <div class="col-sm-10"><h2><?php echo $entry_adwords_section; ?></h2></div>
          </div>
              <div class="form-group">
                <div class="col-sm-2">&nbsp;</div>
                <div class="col-sm-10"><?php echo $help_p_custom_label; ?></div>
          </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_custom_label_0"><?php echo $entry_p_custom_label_0; ?><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_custom_label_0" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_custom_label_0" value="" placeholder="<?php echo $entry_p_custom_label_0; ?>" id="input-g_custom_label_0" class="form-control" />
          </div>
          </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_custom_label_1"><?php echo $entry_p_custom_label_1; ?><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_custom_label_1" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_custom_label_1" value="" placeholder="<?php echo $entry_p_custom_label_1; ?>" id="input-g_custom_label_1" class="form-control" />
          </div>
          </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_custom_label_2"><?php echo $entry_p_custom_label_2; ?><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_custom_label_2" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_custom_label_2" value="" placeholder="<?php echo $entry_p_custom_label_2; ?>" id="input-g_custom_label_2" class="form-control" />
          </div>
          </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_custom_label_3"><?php echo $entry_p_custom_label_3; ?><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_custom_label_3" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_custom_label_3" value="" placeholder="<?php echo $entry_p_custom_label_3; ?>" id="input-g_custom_label_3" class="form-control" />
          </div>
          </div>
          <div class="form-group">
          <label class="col-sm-2 control-label" for="input-g_custom_label_4"><?php echo $entry_p_custom_label_4; ?><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_custom_label_4" value="0" checked></label></label>
          <div class="col-sm-10">
            <input type="text" name="g_custom_label_4" value="" placeholder="<?php echo $entry_p_custom_label_4; ?>" id="input-g_custom_label_4" class="form-control" />
          </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-g_adwords_redirect"><span data-toggle="tooltip" title="<?php echo $help_p_adwords_redirect; ?>"><?php echo $entry_p_adwords_redirect; ?></span><br><label><?php echo $entry_ignore; ?><input type="checkbox" name="x_g_adwords_redirect" value="0" checked></label></label>
            <div class="col-sm-10">
              <input type="text" name="g_adwords_redirect" value="" placeholder="<?php echo $entry_p_adwords_redirect; ?>" id="input-g_adwords_redirect" class="form-control" /></div>
          </div>
          <div class="form-group" style="background:#E9E9E9;">
                <div class="col-sm-2">&nbsp;</div>
                <div class="col-sm-10"><h2><?php echo $entry_products_to_update; ?></h2><p><?php echo $help_products_to_update; ?></p></div>
          </div>
          <div class="form-group" style="background:#E9E9E9;">
            <label class="col-sm-2 control-label" for="input-manufacturers"><span data-toggle="tooltip" title="<?php echo $help_manufacturers; ?>"><?php echo $entry_manufacturers; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="manufacturers" value="" placeholder="<?php echo $entry_manufacturers; ?>" id="input-manufacturers" class="form-control" />
              <div id="product-manufacturers" class="well well-sm" style="height: 150px; overflow: auto;"></div>
            </div>
          </div>
          <div class="form-group" style="background:#E9E9E9;">
            <label class="col-sm-2 control-label" for="input-categories"><span data-toggle="tooltip" title="<?php echo $help_categories; ?>"><?php echo $entry_categories; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="categories" value="" placeholder="<?php echo $entry_categories; ?>" id="input-categories" class="form-control" />
              <div id="product-categories" class="well well-sm" style="height: 150px; overflow: auto;"></div>
            </div>
          </div>
          <div class="form-group" style="background:#E9E9E9;">
            <label class="col-sm-2 control-label" for="input-products"><span data-toggle="tooltip" title="<?php echo $help_products; ?>"><?php echo $entry_products; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="products" value="" placeholder="<?php echo $entry_products; ?>" id="input-products" class="form-control" />
              <div id="product-products" class="well well-sm" style="height: 150px; overflow: auto;"></div>
            </div>
          </div>
          <div class="form-group">
             <div class="col-sm-2">&nbsp;</div>
          <div class="col-sm-10 bu"><button type="button" data-toggle="tooltip" title="<?php echo $button_bulk_update; ?>" class="btn btn-primary bulksave"><?php echo $button_bulk_update; ?></button> <button type="button" data-toggle="tooltip" title="<?php echo $button_bulk_reset; ?>" class="btn btn-danger bulkreset"><?php echo $button_bulk_reset; ?></button></div>
          </div>      
          <div class="form-group">
            <label class="col-sm-2 control-label"><?php echo $entry_info; ?></label>
            <div class="col-sm-10"><?php echo $help_google_help . '<br><br>' . $help_info; ?></div>
          </div>        
        </div>
      </div>        
    </form>


    <?php } ?>
    <?php if($licensed=='none'){ ?>
    <?php echo $license_purchase_thanks; ?>
    <?php if(isset($regerror)){ echo $regerror_quote_msg; } ?>
    <?php if(isset($regerror)){ ?><p style="color:red;">error msg: <?php echo $regerror; ?></p><?php } ?>
    <h2><?php echo $license_registration; ?></h2>
    <form name="reg" method="post" action="<?php echo $home; ?>register.php" id="reg" class="form-horizontal">
        <div class="form-group">
            <label class="col-sm-2 control-label" for="opencart_email"><?php echo $license_opencart_email; ?></label>
            <div class="col-sm-10">
              <input name="opencart_email" type="text" autofocus required id="opencart_email" form="reg" class="form-control"></div>
          </div>
	<?php if(isset($emailmal)&&$regerror=='emailmal'){ ?><p style="color:red;"><?php echo $check_email; ?></p><?php } ?>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="order_id"><?php echo $license_opencart_orderid; ?></label>
            <div class="col-sm-10">
              <input name="order_id" type="text" autofocus required id="order_id" form="reg" class="form-control"><input type="hidden" name="aurl" value="<?php echo $aurl; ?>" form="reg"><input type="hidden" name="auri" value="<?php echo $auri; ?>"></div>
          </div>
	<?php if(isset($regerror)&&$regerror=='orderid'){ ?><p style="color:red;"><?php echo $check_orderid; ?></p><?php } ?>
        <div class="form-group">
            <div class="col-sm-12">
              <button type="submit" form="reg" data-toggle="tooltip" title="<?php echo $license_registration; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button><input name="extension_id" type="hidden" id="extension_id" form="reg" value="<?php echo $extension_id; ?>"></div>
          </div>
    </form>
    <?php } ?>
    <?php if($licensed=='curl'){ ?>
    <?php echo $server_error_curl; ?>
    <?php } ?>
     
    </div>
  </div>
  </div>
</div>
<?php echo $footer; ?>

<?php if($licensed_md5=='e9dc924f238fa6cc29465942875fe8f0'){ ?>
<script type="text/javascript">
<!--
$(document).ready(function(){ 
  $(".btn.active").removeClass("btn-primary").addClass("btn-success");
  $(document).on("click", function(){
    $(".btn").removeClass("btn-success").addClass("btn-primary");
    $(".btn.active").removeClass("btn-primary").addClass("btn-success");
  });

<?php if ($viewlog){ ?>
  $(".general3").trigger("click");
<?php }

$i = 0;
reset($feeds);
if($config_cron){
  reset($crons);
}
foreach (array_keys($feeds) as $key) {
?>
    
  $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $text_choose_google_site; ?>');
    
    <?php if($config_cron){ ?>$("textarea#cron_code_<?php echo $i; ?>").text('<?php echo $text_choose_google_site; ?>');<?php } ?>
<?php $i++; } ?>
  
  $('input[name=notax]').change(function(){
    if ($("#other_language_currency").css('display') == 'none' || $("#other_language_currency").css("visibility") == "hidden") {
        $("#google_site").trigger('change');
    } else {
        $("#other_language").trigger('change');
    }
  });

  $("#google_site").change(function(){
    var site = $("#google_site").val();
    var cron_lang_curr;
    var feed_lang_curr;
    var store;
    var store_id;
    var notax = '';
    var securitycode = "&code=<?php echo $uksb_google_merchant_secure_code; ?>";

    if($('input[name=notax]:checked', '#form-uksb-google-merchant').val() == 1){
      notax = '&notax=1';
    }
    
    if( site != 'zz'){
      $("#other_language, #other_currency").val('').change();
    }

    switch (site) {
      case 'gb':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'en-GBP';
        feed_lang_curr = '&language=en&currency=GBP';
        break;
      case 'us':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'en-USD';
        feed_lang_curr = '&language=en&currency=USD';
        break;
      case 'ca':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'en-CAD';
        feed_lang_curr = '&language=en&currency=CAD';
        break;
      case 'ca_fr':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'fr-CAD';
        feed_lang_curr = '&language=fr&currency=CAD';
        break;
      case 'au':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'en-AUD';
        feed_lang_curr = '&language=en&currency=AUD';
        break;
      case 'fr':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'fr-EUR';
        feed_lang_curr = '&language=fr&currency=EUR';
        break;
      case 'be_fr':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'be_fr-EUR';
        feed_lang_curr = '&language=fr&currency=EUR&country=be';
        break;
      case 'de':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'de-EUR';
        feed_lang_curr = '&language=de&currency=EUR';
        break;
      case 'at_de':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'at_de-EUR';
        feed_lang_curr = '&language=de&currency=EUR&country=at';
        break;
      case 'it':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'it-EUR';
        feed_lang_curr = '&language=it&currency=EUR';
        break;
      case 'nl':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'nl-EUR';
        feed_lang_curr = '&language=nl&currency=EUR';
        break;
      case 'be_nl':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'be_nl-EUR';
        feed_lang_curr = '&language=nl&currency=EUR&country=be';
        break;
      case 'ar':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'es-ARS';
        feed_lang_curr = '&language=es&currency=ARS';
        break;
      case 'cl':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'es-CLP';
        feed_lang_curr = '&language=es&currency=CLP';
        break;
      case 'co':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'es-COP';
        feed_lang_curr = '&language=es&currency=COP';
        break;
      case 'es':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'es-EUR';
        feed_lang_curr = '&language=es&currency=EUR';
        break;
      case 'dk':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'da-DKK';
        feed_lang_curr = '&language=da&currency=DKK';
        break;
      case 'no':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'no-NOK';
        feed_lang_curr = '&language=no&currency=NOK';
        break;
      case 'sv':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'sv-SEK';
        feed_lang_curr = '&language=sv&currency=SEK';
        break;
      case 'pl':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'pl-PLN';
        feed_lang_curr = '&language=pl&currency=PLN';
        break;
      case 'cs':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'cs-CZK';
        feed_lang_curr = '&language=cs&currency=CZK';
        break;
      case 'ru':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'ru-RUB';
        feed_lang_curr = '&language=ru&currency=RUB';
        break;
      case 'tr':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'tr-TRY';
        feed_lang_curr = '&language=tr&currency=TRY';
        break;
      case 'ja':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'ja-JPY';
        feed_lang_curr = '&language=ja&currency=JPY';
        break;
      case 'pt':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'pt-EUR';
        feed_lang_curr = '&language=pt&currency=EUR';
        break;
      case 'pt_br':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'pt-BRL';
        feed_lang_curr = '&language=pt&currency=BRL';
        break;
      case 'mx':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'es-MXN';
        feed_lang_curr = '&language=es&currency=MXN';
        break;
      case 'ch_fr':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'ch_fr-CHF';
        feed_lang_curr = '&language=fr&currency=CHF';
        break;
      case 'ch_de':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'ch_de-CHF';
        feed_lang_curr = '&language=de&currency=CHF';
        break;
      case 'ch_it':
        $("#other_language_currency").slideUp();
        cron_lang_curr = 'ch_it-CHF';
        feed_lang_curr = '&language=it&currency=CHF';
        break;
      case 'zz':
        $("#other_language_currency").slideDown();
        cron_lang_curr = '';
        feed_lang_curr = '';
        break;
      case 'default':
      default:
        $("#other_language_currency").slideUp();
        cron_lang_curr = '';
        feed_lang_curr = '';
    }
    
      <?php $i = 0; reset($feeds); if($config_cron){reset($crons);}
    
      foreach (array_keys($feeds) as $key) {
      
        if ($i > 0){ ?>
        store = '&store=<?php echo $i; ?>';
        store_id = '_<?php echo $i; ?>';
        <?php } else { ?>
        store = '';
        store_id = '';
        <?php } ?>

        <?php if($config_cron){ ?>
          if(feed_lang_curr == ''){
            $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $text_choose_google_site; ?>');
            $("textarea#cron_code_<?php echo $i; ?>").text('<?php echo $text_choose_google_site; ?>');
          }else{
          $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $feeds[$key]; ?>' + 'google' + store_id + '_' + cron_lang_curr + '.xml');
          $("textarea#cron_code_<?php echo $i; ?>").text('curl -L -s "<?php echo $crons[$key]; ?>' + store + feed_lang_curr + securitycode + notax + '" > /dev/null 2>&1');
          }
        <?php }else{ ?>
           if(feed_lang_curr == ''){
             $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $text_choose_google_site; ?>');
           }else{
             $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $feeds[$key]; ?>' + feed_lang_curr + securitycode + notax);
           }
        <?php } ?>
      <?php $i++; } ?>
  });

  $("#other_language, #other_currency").change(function(){
    var cron_lang_curr = '';
    var feed_lang_curr = '';
    var notax = '';
    var securitycode = "&code=<?php echo $uksb_google_merchant_secure_code; ?>";

    if($('input[name=notax]:checked', '#form-uksb-google-merchant').val() == 1){
      notax = '&notax=1';
    }

    if($("#other_language").val() != '' && $("#other_currency").val() != ''){
      cron_lang_curr = $("#other_language").val() + '-' + $("#other_currency").val();
      feed_lang_curr = '&language=' + $("#other_language").val() + '&currency=' + $("#other_currency").val();
    }

    <?php $i = 0; reset($feeds); if($config_cron){reset($crons);}
    
      foreach (array_keys($feeds) as $key) {
    
      if ($i > 0){ ?>
        store = '&store=<?php echo $i; ?>';
        store_id = '_<?php echo $i; ?>';
      <?php } else { ?>
        store = '';
        store_id = '';
      <?php } ?>
    
      <?php if($config_cron){ ?>
        if(feed_lang_curr == ''){
          $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $text_choose_google_site; ?>');
          $("textarea#cron_code_<?php echo $i; ?>").text('<?php echo $text_choose_google_site; ?>');
        }else{
          $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $feeds[$key]; ?>' + 'google' + store_id + '_' + cron_lang_curr + '.xml');
          $("textarea#cron_code_<?php echo $i; ?>").text('curl -L -s "<?php echo $crons[$key]; ?>' + store + feed_lang_curr + securitycode + notax + '" > /dev/null 2>&1');
        }
      <?php } else { ?>
         if(feed_lang_curr == ''){
           $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $text_choose_google_site; ?>');
         }else{
           $("textarea#feed_url_<?php echo $i; ?>").text('<?php echo $feeds[$key]; ?>' + feed_lang_curr + securitycode + notax);
         }
      <?php } ?>
      <?php $i++; } ?>
  });

<?php if(isset($clearform)){
    echo '$("#form-uksb-google-merchant").hide();
    $(".gensave").hide();
    $("#form-bulk-update").show();';
} ?>
  $(".show_bulk").on("click", function(){
    $("#form-uksb-google-merchant").hide();
    $(".gensave").hide();
    $("#form-bulk-update").show();
  });
  $(".show_general2").on("click", function(){
    $("#form-bulk-update").hide();
    $("#form-uksb-google-merchant").show();
    $(".gensave").show();
    $(".general2").trigger("click");
  });
  $(".show_general3").on("click", function(){
    $("#form-bulk-update").hide();
    $("#form-uksb-google-merchant").show();
    $(".gensave").show();
    $(".general3").trigger("click");
  });
  $(".show_general4").on("click", function(){
    $("#form-bulk-update").hide();
    $("#form-uksb-google-merchant").show();
    $(".gensave").show();
    $(".general4").trigger("click");
  });  
  $(".show_general5").on("click", function(){
    $("#form-bulk-update").hide();
    $("#form-uksb-google-merchant").show();
    $(".gensave").show();
    $(".general5").trigger("click");
  });
});

$('input[name=\'manufacturers\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/manufacturer/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['manufacturer_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'manufacturers\']').val('');

    $('#product-manufacturers' + item['value']).remove();

    $('#product-manufacturers').append('<div id="product-manufacturers' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_manufacturers[]" value="' + item['value'] + '" /></div>');
  }
});

$('#product-manufacturers').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

$('input[name=\'categories\']').autocomplete({
  'source': function(request, response) {
    $.ajax({
      url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item['name'],
            value: item['category_id']
          }
        }));
      }
    });
  },
  'select': function(item) {
    $('input[name=\'categories\']').val('');

    $('#product-categories' + item['value']).remove();

    $('#product-categories').append('<div id="product-categories' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_categories[]" value="' + item['value'] + '" /></div>');
  }
});

$('#product-categories').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

$('input[name=\'products\']').autocomplete({
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
    $('input[name=\'products\']').val('');

    $('#product-products' + item['value']).remove();

    $('#product-products').append('<div id="product-products' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_products[]" value="' + item['value'] + '" /></div>');
  }
});

$('#product-products').delegate('.fa-minus-circle', 'click', function() {
  $(this).parent().remove();
});

var variant_row = 1;

function addVariant() {
  html  = '  <tr id="variant-row' + variant_row + '">';
  html += '    <td class="col-sm-3 text-left">';
  html += '    <label class="control-label" for="variant[' + variant_row + '][g_size]"><?php echo $entry_p_size; ?></label><br>';
  html += '    <input type="text" name="variant[' + variant_row + '][g_size]" value="" placeholder="<?php echo $entry_p_size; ?>" class="form-control" /><br><br>';
  html += '    <label class="control-label" for="variant[' + variant_row + '][g_material]"><?php echo $entry_p_material; ?></label><br>';
  html += '    <input type="text" name="variant[' + variant_row + '][g_material]" value="" placeholder="<?php echo $entry_p_material; ?>" class="form-control" /></td>';
  html += '    <td class="col-sm-3 text-left">';
  html += '    <label class="control-label" for="variant[' + variant_row + '][g_colour]"><?php echo $entry_p_colour; ?></label><br>';
  html += '    <input type="text" name="variant[' + variant_row + '][g_colour]" value="" placeholder="<?php echo $entry_p_colour; ?>" class="form-control" /><br><br>';
  html += '    <label class="control-label" for="variant[' + variant_row + '][g_pattern]"><?php echo $entry_p_pattern; ?></label><br>';
  html += '    <input type="text" name="variant[' + variant_row + '][g_pattern]" value="" placeholder="<?php echo $entry_p_pattern; ?>" class="form-control" /></td>';
  html += '    <td class="col-sm-3 text-left">';
  html += '    <label class="control-label" for="variant[' + variant_row + '][v_prices]"><?php echo $entry_v_prices; ?></label><br><br><?php echo $help_v_prices; ?><br><br>';
  html += '    <input type="text" name="variant[' + variant_row + '][v_prices]" value="" placeholder="<?php echo $entry_v_prices; ?>" class="form-control" /></td>';
  html += '    <td class="col-sm-2 text-left"><a href="" id="thumb-image' + variant_row + '" data-toggle="image" class="img-thumbnail"><img src="<?php echo $placeholder; ?>" alt="" title="" data-placeholder="<?php echo $placeholder; ?>" /></a><input type="hidden" name="variant[' + variant_row + '][v_images]" value="" id="v_images' + variant_row + '" /></td>';
  html += '    <td class="col-sm-1 text-left"><button type="button" onclick="$(\'#variant-row' + variant_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '  </tr>';
  
  $('#variants tbody').append(html);
  
  variant_row++;
}

$('.expiry-date').datetimepicker({
  pickTime: false
});

$.wait = function( callback, seconds){
   return window.setTimeout( callback, seconds * 1000 );
}

$( ".gensave" ).on( "click", function() {
  $( "#form-uksb-google-merchant" ).submit();
});

$( ".advanced" ).on( "click", function() {
  $("#savecontinue").attr('value', '1');
  $.wait( function(){ $( ".gensave" ).trigger("click"); }, 0.5);
});

$( ".bulksave" ).on( "click", function() {
      $(".alert").remove();
      $.ajax({
        url: 'index.php?route=extension/feed/uksb_google_merchant/bulk_update&token=<?php echo $token; ?>',
        type: 'post',
        dataType: 'json',
        data: new FormData($('#form-bulk-update')[0]),
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function() {
          $('.bulksave').button('loading');
        },
        complete: function() {
          $('.bulksave').button('reset');
        },
        success: function(json) {
          if (json['error']) {
            $('.bu').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>')
          }
          else if(json['updated']){
            $('.bu').prepend('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['updated'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>')
          }
        },
        error: function(xhr, ajaxOptions, thrownError) {
          alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
      });
});

$( ".bulkreset" ).on( "click", function() {
  location.href = 'index.php?route=extension/feed/uksb_google_merchant&clear=1&token=<?php echo $token; ?>';
});
//-->
</script>
<?php } ?>
<script type="text/javascript">
<!--
if (window.location.search != '?route=extension/feed/uksb_google_merchant&token=<?php echo $token; ?>') {
     top.location.href = window.location.pathname + '?route=extension/feed/uksb_google_merchant&token=<?php echo $token; ?>' ;
}
//-->
</script>