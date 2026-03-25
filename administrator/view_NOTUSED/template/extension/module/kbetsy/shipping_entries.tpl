<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add_template_entry; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
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
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-paper-plane"></i><?php echo $heading_title_shipping_entries; ?></h3>
            </div>
            <div class="panel-body">
               <?php echo $tabs; ?>        
                <div class="well" style="display: none">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="shipping_name"><?php echo $text_filter_shipping_name; ?></label>
                                <input type="text" name="filter_shipping_name" value="<?php echo $filter_shipping_name; ?>" placeholder="<?php echo $text_filter_shipping_name; ?>" id="shipping_name" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="shipping_name"><?php echo $text_filter_shipping_country; ?></label>
                                <input type="text" name="filter_shipping_country" value="<?php echo $filter_shipping_country; ?>" placeholder="<?php echo $text_filter_shipping_country; ?>" id="shipping_country" class="form-control" />
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="destination_country"><?php echo $column_destination_country; ?></label>
                                <input type="text" name="filter_destination_country" value="<?php echo $filter_destination_country; ?>" placeholder="<?php echo $column_destination_country; ?>" id="destination_country" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="destination_region"><?php echo $column_destination_region; ?></label>
                                <input type="text" name="filter_destination_region" value="<?php echo $filter_destination_region; ?>" placeholder="<?php echo $column_destination_region; ?>" id="destination_region" class="form-control" />
                            </div>
                        </div>
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <br>
                                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                                <button type="button" id="button-refresh" class="btn btn-default pull-right" style="margin-right: 2px;"><i class="fa fa-refresh"></i> <?php echo $button_reset; ?></button>&nbsp;
                            </div>
                        </div>
                    </div>
                </div>
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td class="text-left"><?php if ($sort == 'shipping_origin_country') { ?>
                                            <a href="<?php echo $sort_shipping_origin_country; ?>" class="<?php echo $order; ?>"><?php echo $column_shipping_origin; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_shipping_origin_country; ?>"><?php echo $column_shipping_origin; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'shipping_entry_destination_country') { ?>
                                            <a href="<?php echo $sort_shipping_entry_destination_country; ?>" class="<?php echo $order; ?>"><?php echo $column_destination_country; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_shipping_entry_destination_country; ?>"><?php echo $column_destination_country; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'shipping_entry_destination_region') { ?>
                                            <a href="<?php echo $sort_shipping_entry_destination_region; ?>" class="<?php echo $order; ?>"><?php echo $column_destination_region; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_shipping_entry_destination_region; ?>"><?php echo $column_destination_region; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'shipping_entry_primary_cost') { ?>
                                            <a href="<?php echo $sort_shipping_entry_primary_cost; ?>" class="<?php echo $order; ?>"><?php echo $column_primary_cost; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_shipping_entry_primary_cost; ?>"><?php echo $column_primary_cost; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'shipping_entry_secondary_cost') { ?>
                                            <a href="<?php echo $sort_shipping_entry_secondary_cost; ?>" class="<?php echo $order; ?>"><?php echo $column_secondary_cost; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_shipping_entry_secondary_cost; ?>"><?php echo $column_secondary_cost; ?></a>
                                        <?php } ?>
                                    </td>

                                    <td class="text-right"><?php echo $column_action; ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($shipping_template_entries) { ?>
                                    <?php foreach ($shipping_template_entries as $templates) { ?>
                                        <tr>
                                            <td class="text-left"><?php echo $templates['shipping_origin_country']; ?></td>
                                            <td class="text-left"><?php if($templates['shipping_entry_destination_region'] == "" && $templates['shipping_entry_destination_country'] == "") { echo $text_everywhere_else; } else if($templates['shipping_entry_destination_region'] == "") { echo $templates['shipping_entry_destination_country']; } ?></td>
                                            <td class="text-left"><?php echo $templates['shipping_entry_destination_region']; ?></td>
                                            <td class="text-left"><?php echo $templates['shipping_entry_primary_cost']; ?></td>
                                            <td class="text-left"><?php echo $templates['shipping_entry_secondary_cost']; ?></td>
                                            <td class="text-right">
                                                <a href="<?php echo $templates['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                <a href="<?php echo $templates['delete']; ?>" onclick="return window.confirm('<?php echo $text_confirm_delete_etsy; ?>');" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
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
                <div class="row">
                    <div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
                    <div class="col-sm-6 text-right"><?php echo $results; ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
$('#button-filter').on('click', function () {
        var url = 'index.php?route=<?php echo $module_path; ?>/shippingTemplateEntries&<?php echo $session_token_key; ?>=<?php echo $token; ?>';

        var filter_shipping_name = $('input[name=\'filter_shipping_name\']').val();
        if (filter_shipping_name) {
            url += '&filter_shipping_name=' + encodeURIComponent(filter_shipping_name);
        }

        var filter_shipping_country = $('input[name=\'filter_shipping_country\']').val();
        if (filter_shipping_country) {
            url += '&filter_shipping_country=' + encodeURIComponent(filter_shipping_country);
        }

        var filter_destination_country = $('input[name=\'filter_destination_country\']').val();
        if (filter_destination_country) {
            url += '&filter_destination_country=' + encodeURIComponent(filter_destination_country);
        }

        var filter_destination_region = $('input[name=\'filter_destination_region\']').val();
        if (filter_destination_region) {
            url += '&filter_max_proc_days=' + encodeURIComponent(filter_destination_region);
        }

        location = url;
    });
    $('#button-refresh').click(function (e) {
        var url = 'index.php?route=<?php echo $module_path; ?>/shippingTemplateEntries&<?php echo $session_token_key; ?>=<?php echo $token; ?>';
        location = url;
    });
//--></script>
<?php echo $footer; ?>