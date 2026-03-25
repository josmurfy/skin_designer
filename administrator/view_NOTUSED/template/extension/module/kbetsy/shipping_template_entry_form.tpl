<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="etsy-shipping-temp-add" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
                    <input type="hidden" name="shipping_templates_entries" value="shipping_templates_entries"/>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_template_title"><?php echo $text_template_title; ?></label>
                        <div class="col-sm-10">
                            <input disabled type="text" name="etsy[template][template_title]" value="<?php if (isset($etsy['template']['template_title'])) echo $etsy['template']['template_title']; ?>" placeholder="" id="etsy_template_title" class="form-control"/>
                            <?php if (isset($id_etsy_shipping_templates_entries)) { ?>
                                <input type="hidden" name="id_etsy_shipping_templates_entries" value="<?php echo $id_etsy_shipping_templates_entries; ?>" placeholder="" id="id_etsy_shipping_templates_entries" class="form-control"/>
                            <?php } ?>
                            <?php if (isset($id_etsy_shipping_templates)) { ?>
                                <input type="hidden" name="id_etsy_shipping_templates" value="<?php echo $id_etsy_shipping_templates; ?>" placeholder="" id="id_etsy_shipping_templates" class="form-control"/>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $text_origin_country; ?></label>
                        <div class="col-sm-10">
                            <select disabled name="etsy[template][origin_country]" id="input-status" class="form-control">
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
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $text_destination_type; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[template][destination_type]" id="destination_type" class="form-control">
                                <?php if ($destination_type == 'region') { ?>
                                    <option value="country" ><?php echo $text_country; ?></option>
                                    <option value="region" selected="selected"><?php echo $text_region; ?></option>
                                <?php } else { ?>
                                    <option value="country" selected="selected"><?php echo $text_country; ?></option>
                                    <option value="region" ><?php echo $text_region; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group required country">
                        <label class="col-sm-2 control-label" for="shipping_entry_destination_country_id"><?php echo $entry_destination_country; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[template][shipping_entry_destination_country_id]" id="input-status" class="form-control">
                                <option value="" selected="selected"><?php echo $entry_select_country; ?></option>
                                <option value="0" <?php if($etsy['template']['shipping_entry_destination_country_id'] == 0 && $etsy['template']['shipping_entry_destination_country_id'] != "") { echo 'selected'; } ?>><?php echo $text_everywhere_else; ?></option>
                                <optgroup label="<?php echo $entry_destination_country; ?>">
                                <?php foreach ($etsy_countries as $etsy_country) { ?>
                                    <?php if ($etsy_country['country_id'] == $etsy['template']['shipping_entry_destination_country_id']) { ?>
                                        <option value="<?php echo $etsy_country['country_id']; ?>" selected="selected"><?php echo $etsy_country['country_name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $etsy_country['country_id']; ?>"><?php echo $etsy_country['country_name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                                </optgroup>
                            </select>
                            <?php if ($error_shipping_entry_destination_country_id) { ?>
                                <div class="text-danger"><?php echo $error_shipping_entry_destination_country_id; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required region">
                        <label class="col-sm-2 control-label" for="shipping_entry_destination_region_id"><?php echo $entry_destination_region; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[template][shipping_entry_destination_region_id]" id="input-status" class="form-control">
                                <option value="" selected="selected"><?php echo $entry_select_region; ?></option>
                                <?php foreach ($etsy_regions as $etsy_region) { ?>
                                    <?php if ($etsy_region['region_id'] == $etsy['template']['shipping_entry_destination_region_id']) { ?>
                                        <option value="<?php echo $etsy_region['region_id']; ?>" selected="selected"><?php echo $etsy_region['region_name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $etsy_region['region_id']; ?>"><?php echo $etsy_region['region_name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php if ($error_shipping_entry_destination_region_id) { ?>
                                <div class="text-danger"><?php echo $error_shipping_entry_destination_region_id; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_template_primary_cost"><?php echo $text_primary_cost; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="etsy[template][primary_cost]" value="<?php if (isset($etsy['template']['primary_cost'])) echo$etsy['template']['primary_cost']; ?>" placeholder="" id="etsy_template_primary_cost" class="form-control"/>
                            <?php if ($error_etsy_primary_cost) { ?>
                                <div class="text-danger"><?php echo $error_etsy_primary_cost; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_template_secondary_cost"><?php echo $text_secondary_cost; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="etsy[template][secondary_cost]" value="<?php if (isset($etsy['template']['secondary_cost'])) echo $etsy['template']['secondary_cost']; ?>" placeholder="" id="etsy_template_secondary_cost" class="form-control"/>
                            <?php if ($error_etsy_secondary_cost) { ?>
                                <div class="text-danger"><?php echo $error_etsy_secondary_cost; ?></div>
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

<style type="text/css">
<?php if (isset($destination_type) && $destination_type == 'country') { ?>
    .country {
        display:block;
    }
    .region {
        display:none;
    }                                                     
<?php } else { ?>
    .country {
        display:none;
    }
    .region {
        display:block;
    } 
<?php } ?>
</style>

<script type="text/javascript">
    $('#destination_type').change(function () {
        if (this.value == 'country') {
            $('.country').show('slow');
            $('.region').hide('slow');
        } else if (this.value == 'region') {
            $('.country').hide('slow');
            $('.region').show('slow');
        }
    });
</script>
<?php echo $footer; ?>