<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="etsy-general-settings" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <?php if($connect_status == 'yes'){ ?>
                <a href="<?php echo $disconnect; ?>" data-toggle="tooltip" title="<?php echo $button_disconnect; ?>" class="btn btn-danger"><i class="fa fa-stop"></i></a>
                <?php } else { ?>
                <?php if($connect != "") { ?>
                <a href="<?php echo $connect; ?>" data-toggle="tooltip" title="<?php echo $button_connect; ?>" class="btn btn-primary"><i class="fa fa-refresh"></i></a>
                <?php } ?>
                <?php } ?>
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
        <?php if (isset($error) && $error != "") { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <?php if (isset($success) && $success != '') { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-cog"></i><?php echo $text_edit_general; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="etsy-general-settings" class="form-horizontal">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">  
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-status"><?php echo $text_general_enable; ?></label>
                                <div class="col-sm-10">
                                    <select name="etsy[general][enable]" id="input-status" class="form-control">
                                        <?php if (isset($etsy['general']['enable']) && $etsy['general']['enable'] == 1) { ?>
                                        <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } else if (isset($etsy['general']['enable']) && $etsy['general']['enable'] == 0) { ?>
                                        <option value="1"><?php echo $text_enabled; ?></option>
                                        <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                        <?php } else { ?>
                                        <option value="1" selected><?php echo $text_enabled; ?></option>
                                        <option value="0"><?php echo $text_disabled; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="etsy_api_key">
                                    <?php echo $text_etsy_api_key; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="etsy[general][etsy_api_key]" value="<?php if(isset($etsy['general']['etsy_api_key'])){ echo $etsy['general']['etsy_api_key'];} ?>" placeholder="" id="etsy_api_key" class="form-control" <?php if($connect_status == 'yes') { echo "readonly"; }?>/>
                                    <div><?php echo $text_etsy_api_hint; ?></div>
                                    <?php if ($error_etsy_api_key) { ?>
                                    <div class="text-danger"><?php echo $error_etsy_api_key; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="etsy_api_secret">
                                        <?php echo $text_etsy_api_secret; ?>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" name="etsy[general][etsy_api_secret]" value="<?php if(isset($etsy['general']['etsy_api_secret'])){ echo $etsy['general']['etsy_api_secret'];} ?>" placeholder="" id="etsy_api_secret" class="form-control" <?php if($connect_status == 'yes') { echo "readonly"; }?>/>
                                    <div><?php echo $text_etsy_api_hint; ?></div>
                                    <?php if ($error_etsy_api_secret) { ?>
                                    <div class="text-danger"><?php echo $error_etsy_api_secret; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group required" style="display: none">
                                <label class="col-sm-2 control-label" for="etsy_api_host"><?php echo $text_etsy_api_host; ?></label>
                                <div class="col-sm-10">
                                    <input disabled type="text" name="etsy[general][etsy_api_host]" value="<?php if(isset($etsy['general']['etsy_api_host'])){ echo $etsy['general']['etsy_api_host'];} ?>" placeholder="" id="etsy_api_host" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group required"  style="display: none">
                                <label class="col-sm-2 control-label" for="etsy_api_version"><?php echo $text_etsy_api_version; ?></label>
                                <div class="col-sm-10">
                                    <input disabled type="text" name="etsy[general][etsy_api_version]" value="<?php if(isset($etsy['general']['etsy_api_version'])){ echo $etsy['general']['etsy_api_version'];} ?>" placeholder="" id="etsy_api_version" class="form-control"/>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="etsy-currency">
                                    <span data-toggle="tooltip" title="" data-original-title="<?php echo $text_etsy_currency_hint; ?>">
                                        <?php echo $text_etsy_currency; ?>
                                    </span>
                                </label>
                                <div class="col-sm-10">
                                    <select name="etsy[general][currency]" id="etsy-currency" class="form-control">
                                        <option value=""><?php echo $entry_select_currency; ?></option>
                                        <?php foreach ($allcurrencies as $currency) { ?>
                                            <?php if ($currency['code'] == $etsy['general']['currency']) { ?>
                                                <option value="<?php echo $currency['code']; ?>" selected="selected"><?php echo $currency['title']; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $currency['code']; ?>"><?php echo $currency['title']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                    <?php if ($error_currency) { ?>
                                        <div class="text-danger"><?php echo $error_currency; ?></div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for=""><?php echo $text_etsy_default_lang; ?></label>
                                <div class="col-sm-10">
                                    <select name="etsy[general][etsy_default_language]" id="etsy_default_language" class="form-control">
                                        <?php foreach ($languages as $language) { ?>
                                        <?php if ($language['language_id'] == $etsy['general']['etsy_default_language']) { ?>
                                        <option value="<?php echo $language['language_id']; ?>" selected="selected"><?php echo $language['name']; ?></option>
                                        <?php } else { ?>
                                        <option value="<?php echo $language['language_id']; ?>"><?php echo $language['name']; ?></option>
                                        <?php }} ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group required">
                                <label class="col-sm-2 control-label" for="etsy_api_version"><?php echo $text_etsy_lang_sync; ?></label>
                                <div class="col-sm-10">
                                    <div class="well well-sm" style="height: 150px; overflow: auto; margin-bottom: 5px">
                                        <?php foreach ($languages as $language) { ?>
                                        <div class="checkbox">
                                            <label>
                                                <?php if (isset($etsy['general']['etsy_languages_to_sync']) && (in_array($language['language_id'], $etsy['general']['etsy_languages_to_sync']))) { ?>
                                                <input type="checkbox" name="etsy[general][etsy_languages_to_sync][]" value="<?php echo $language['language_id']; ?>" checked="checked" />
                                                <?php echo $language['name']; ?>
                                                <?php } else { ?>
                                                <input type="checkbox" name="etsy[general][etsy_languages_to_sync][]" value="<?php echo $language['language_id']; ?>" />
                                                <?php echo $language['name']; ?>
                                                <?php } ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                    </div>
                                    <div style="font-style: italic; font-size: 11px"><?php echo $text_supported_text_language_hint; ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-shopping-cart"></i><?php echo $text_edit_order; ?></h3>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="etsy-order-default-status">
                            <span data-toggle="tooltip" title="" data-original-title="<?php echo $text_status_order_default_hint; ?>">
                                <?php echo $text_status_order_default; ?>
                            </span>
                        </label>
                        <div class="col-sm-10">
                            <select name="etsy[order][default_status]" id="etsy-order-default-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $etsy_order_default_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                            <div style="font-style: italic; font-size: 11px"><?php echo $text_status_order_default_hint; ?></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="etsy-order-default-status">
                            <span data-toggle="tooltip" title="" data-original-title="<?php echo $text_status_order_paid_oc_hint; ?>">
                                <?php echo $text_status_order_paid_etsy; ?>
                            </span>
                        </label>
                        <div class="col-sm-10">
                            <select name="etsy[order][default_paid_status]" id="etsy-order-default-status" class="form-control">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $order_paid_status_id) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                            <div style="font-style: italic; font-size: 11px"><?php echo $text_status_order_paid_oc_hint; ?></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="etsy-order-paid-status">
                            <span data-toggle="tooltip" title="" data-original-title="<?php echo $text_status_order_paid_hint; ?>">
                                <?php echo $text_status_order_paid; ?>
                            </span>
                        </label>
                        <div class="col-sm-10">
                            <select name="etsy[order][paid_status][]" id="etsy-order-paid-status" class="form-control" multiple="5">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if (in_array($order_status['order_status_id'], $etsy_order_paid_status_id)) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                            <div style="font-style: italic; font-size: 11px"><?php echo $text_status_order_paid_hint; ?></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="etsy-order-shipped-status">
                            <span data-toggle="tooltip" title="" data-original-title="<?php echo $text_status_order_shipped_hint; ?>">
                                <?php echo $text_status_order_shipped; ?>
                            </span>
                        </label>
                        <div class="col-sm-10">
                            <select name="etsy[order][shipped_status][]" id="etsy-order-shipped-status" class="form-control" multiple="5">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if (in_array($order_status['order_status_id'], $etsy_order_shipped_status_id)) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                            <div style="font-style: italic; font-size: 11px"><?php echo $text_status_order_shipped_hint; ?></div>
                        </div>
                    </div>                    
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>