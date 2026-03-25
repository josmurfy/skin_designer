<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="etsy-shipping-temp-add" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
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
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title_main; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="etsy-shipping-temp-add" class="form-horizontal">
                    <input type="hidden" name="shipping_templates" value="shipping_templates"/>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_template_title"><?php echo $text_template_title; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="etsy[template][template_title]" value="<?php if (isset($etsy['template']['template_title'])) echo $etsy['template']['template_title']; ?>" placeholder="" id="etsy_template_title" class="form-control"/>
                            <?php if ($error_etsy_template_title) { ?>
                                <div class="text-danger"><?php echo $error_etsy_template_title; ?></div>
                            <?php } ?>
                            <?php if (isset($id_etsy_shipping_templates)) { ?>
                                <input type="hidden" name="id_etsy_shipping_templates" value="<?php echo $id_etsy_shipping_templates; ?>" placeholder="" id="id_etsy_shipping_templates" class="form-control"/>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $text_origin_country; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[template][origin_country]" id="input-status" class="form-control">
                                <?php foreach ($etsy_countries as $etsy_country) { ?>
                                    <?php if ($etsy_country['country_id'] == $etsy_select_country) { ?>
                                        <option value="<?php echo $etsy_country['country_id']; ?>" selected="selected"><?php echo $etsy_country['country_name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $etsy_country['country_id']; ?>"><?php echo $etsy_country['country_name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php if (isset($id_etsy_shipping_templates) && $id_etsy_shipping_templates != "") { ?>
                        <input type="hidden" name="etsy[template][primary_cost]" value="<?php if (isset($etsy['template']['primary_cost'])) echo $etsy['template']['primary_cost']; ?>" placeholder="" id="etsy_template_primary_cost" class="form-control"/>
                    <?php } else { ?>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="etsy_template_primary_cost"><?php echo $text_primary_cost; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="etsy[template][primary_cost]" value="<?php if (isset($etsy['template']['primary_cost'])) echo $etsy['template']['primary_cost']; ?>" placeholder="" id="etsy_template_primary_cost" class="form-control"/>
                                <?php if ($error_etsy_primary_cost) { ?>
                                    <div class="text-danger"><?php echo $error_etsy_primary_cost; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if (isset($id_etsy_shipping_templates) && $id_etsy_shipping_templates != "") { ?>
                        <input type="hidden" name="etsy[template][secondary_cost]" value="<?php if (isset($etsy['template']['secondary_cost'])) echo $etsy['template']['secondary_cost']; ?>" placeholder="" id="etsy_template_secondary_cost" class="form-control"/>
                    <?php } else { ?>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label" for="etsy_template_secondary_cost"><?php echo $text_secondary_cost; ?></label>
                            <div class="col-sm-10">
                                <input type="text" name="etsy[template][secondary_cost]" value="<?php if (isset($etsy['template']['secondary_cost'])) echo $etsy['template']['secondary_cost']; ?>" placeholder="" id="etsy_template_secondary_cost" class="form-control"/>
                                <?php if ($error_etsy_secondary_cost) { ?>
                                    <div class="text-danger"><?php echo $error_etsy_secondary_cost; ?></div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_template_min_process_days"><?php echo $text_min_process_days; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="etsy[template][min_process_days]" value="<?php if (isset($etsy['template']['min_process_days'])) echo $etsy['template']['min_process_days']; ?>" placeholder="" id="etsy_template_min_process_days" class="form-control"/>
                            <?php if ($error_etsy_min_process_days) { ?>
                                <div class="text-danger"><?php echo $error_etsy_min_process_days; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_template_max_process_days"><?php echo $text_max_process_days; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="etsy[template][max_process_days]" value="<?php if (isset($etsy['template']['max_process_days'])) echo $etsy['template']['max_process_days']; ?>" placeholder="" id="etsy_template_max_process_days" class="form-control"/>
                            <?php if ($error_etsy_max_process_days) { ?>
                                <div class="text-danger"><?php echo $error_etsy_max_process_days; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </form>
                <div class="alert alert-info" style="margin-top: 20px">
                    <?php echo $text_currency_info; ?>
                </div>
                
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>