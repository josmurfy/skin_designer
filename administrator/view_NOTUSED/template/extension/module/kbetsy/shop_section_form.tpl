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
                    <input type="hidden" name="shop_section" value="shop_section"/>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_shop_id"><?php echo $text_select_shop; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[shop_section][shop_id]" id="input-status" class="form-control">
                                <?php foreach ($etsy_shops as $etsy_shop) { ?>
                                    <?php if ($etsy_shop['shop_id'] == $shop_id) { ?>
                                        <option value="<?php echo $etsy_shop['shop_id']; ?>" selected="selected"><?php echo $etsy_shop['title']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $etsy_shop['shop_id']; ?>"><?php echo $etsy_shop['title']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_shop_section_title"><?php echo $text_shop_section_title; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="etsy[shop_section][title]" value="<?php echo $shop_section_title; ?>" placeholder="" id="etsy_shop_section_title" class="form-control"/>
                            <?php if ($error_shop_section_title) { ?>
                                <div class="text-danger"><?php echo $error_shop_section_title; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if (isset($shop_section_id) && $shop_section_id != "") { ?>
                        <input type="hidden" name="shop_section_id" value="<?php if (isset($shop_section_id)) echo $shop_section_id; ?>" placeholder="" id="shop_section_id" class="form-control"/>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>