<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <!-- <a href="javascript://" data-toggle="tooltip" title="<?php echo $button_add_custom_attribute; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a> -->
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
        <div class="message-container"></div>
        <?php if ($error) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> 
                <?php echo $error; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if ($success) { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> 
                <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-plane"></i><?php echo $text_attribute_mapping; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <?php if ($options == true) { ?>
                    <form action="" method="post" enctype="multipart/form-data" id="form-product">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <td class="text-left"><?php echo $option_name; ?></td>
                                        <td class="text-left"><?php echo $etsy_option; ?></td>
                                        <td class="text-right"><?php echo $column_action; ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($optionMappings) { ?>
                                        <?php foreach ($optionMappings as $optionMapping) { ?>
                                            <tr>
                                                <td class="text-left"><?php echo $optionMapping['name']; ?></td>
                                                <td class="text-left"><?php echo $optionMapping['property_title']; ?></td>
                                                <td class="text-right">
                                                    <a data-target="#editMapping-<?php echo $optionMapping['oc_option_id']; ?> " data-toggle="modal" title="<?php echo $button_edit; ?>" class="btn btn-primary" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <div class="row"></div>
                <?php } else { ?>
                    <div class="">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="alert alert-info">
                                    <?php echo $mapping_not_required; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>


    <?php if ($optionMappings) { ?>
        <?php foreach ($optionMappings as $optionMapping) { ?>
            <div class="modal fade saveToken" id="editMapping-<?php echo $optionMapping['oc_option_id']; ?>"  tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="gridSystemModalLabel"><?php echo $text_attribute_mapping; ?></h4>
                        </div>
                        <form action="" method="post" enctype="multipart/form-data" id="save-token_<?php echo $optionMapping['oc_option_id']; ?>" class='form-horizontal'>
                            <div class="modal-body">
                                <div style="font-size: 14px;">
                                    <div id="layout-message_<?php echo $optionMapping['oc_option_id']; ?>"></div>
                                    <div class="form-group required" style="margin-bottom: 10px;">
                                        <label class="col-sm-3 control-label" for="input-title"><?php echo $select_etsy_option; ?></label>
                                        <div class="col-sm-9">
                                            <select name="etsy_option" id="etsy_option-<?php echo $optionMapping['oc_option_id']; ?>" class="form-control">
                                                <?php foreach ($etsy_options as $etsy_option) { ?>
                                                    <?php if ($etsy_option["id"] == $optionMapping['property_id']) { ?>
                                                        <option value="<?php echo $etsy_option["id"]; ?>" selected="selected"><?php echo $etsy_option["name"]; ?></option>
                                                    <?php } else { ?>
                                                        <option value="<?php echo $etsy_option["id"]; ?>"><?php echo $etsy_option["name"]; ?></option>
                                                    <?php } ?>
                                                <?php } ?>
                                            </select>
                                            <div id="layout-title-error"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <img src="view/image/loader.gif" style="display: none" id="img-loader-<?php echo $optionMapping['oc_option_id']; ?>"/>
                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $button_cancel; ?></button>
                                <button type="button" class="btn btn-primary savetoken" id='save-token-<?php echo $optionMapping['oc_option_id']; ?>'><?php echo $button_save; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</div>

<script type="text/javascript">
    var token = '<?php echo $token; ?>';
    <?php foreach ($optionMappings as $optionMapping) { ?>
    $('#save-token-<?php echo $optionMapping['oc_option_id'] ?>').click(function () {
        etsy_option_id = $('#etsy_option-<?php echo $optionMapping['oc_option_id'] ?>').val();
        $('#layout-message_<?php echo $optionMapping['oc_option_id'] ?>').html("");
        $('#img-loader-<?php echo $optionMapping['oc_option_id'] ?>').show();
        $.ajax({
            url: "index.php?route=<?php echo $module_path; ?>/saveAttributeMapping&<?php echo $session_token_key; ?>=" + token,
            data: 'etsy_option_id=' + etsy_option_id + '&opencart_option_id=<?php echo $optionMapping['oc_option_id'] ?>',
            type: "post",
            dataType: "json",
            success: function (data) {
                $('#img-loader-<?php echo $optionMapping['oc_option_id'] ?>').hide();
                if (data.type == "error") {
                    $('#layout-message_<?php echo $optionMapping['oc_option_id'] ?>').html('<div class="alert alert-warning"><i class="fa fa-check-circle"></i> ' + data.message + '</div>');
                } else {
                    window.location.href = "index.php?route=<?php echo $module_path; ?>/attributeMapping&<?php echo $session_token_key; ?>=" + token;
                }
            }
        });
    });
    <?php } ?>
</script>
<?php echo $footer; ?>