<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <?php if ($etsycountires > 0) { ?>
            <div class="pull-right">
                <a target="_blank" href="<?php echo $sync_template_url; ?>" data-toggle="tooltip" title="<?php echo $text_sync_shipping_profile; ?>" class="btn btn-primary"><i class="fa fa-refresh"></i></a>
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            </div>
            <?php } ?>
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
                <h3 class="panel-title"><i class="fa fa-plane"></i><?php echo $text_edit_shipping; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <?php if ($etsycountires > 0) { ?>
                    <div class="well">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="shipping_name"><?php echo $text_filter_shipping_name; ?></label>
                                    <input type="text" name="filter_shipping_name" value="<?php echo $filter_shipping_name; ?>" placeholder="<?php echo $text_filter_shipping_name; ?>" id="shipping_name" class="form-control" />

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="control-label" for="shipping_name"><?php echo $text_filter_shipping_country; ?></label>
                                    <input type="text" name="filter_shipping_country" value="<?php echo $filter_shipping_country; ?>" placeholder="<?php echo $text_filter_shipping_country; ?>" id="shipping_country" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="control-label" style="display: block">&nbsp;</label>
                                    <input type="hidden" name="filter_min_proc_days" value="<?php echo $filter_min_proc_days; ?>" placeholder="<?php echo $text_filter_min_proc_days; ?>" id="min_proc_days" class="form-control" />
                                    <input type="hidden" name="filter_max_proc_days" value="<?php echo $filter_max_proc_days; ?>" placeholder="<?php echo $text_filter_max_proc_days; ?>" id="max_proc_days" class="form-control" />
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
                                        <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                                        <td class="text-left">
                                            <?php if ($sort == 'id_etsy_shipping_profiles') { ?>
                                                <a href="<?php echo $sort_id_etsy_shipping_profiles; ?>" class="<?php echo $order; ?>"><?php echo $column_template_id; ?></a>
                                            <?php } else { ?>
                                                <a href="<?php echo $sort_id_etsy_shipping_profiles; ?>"><?php echo $column_template_id; ?></a>
                                            <?php } ?>
                                        </td>
                                        <td class="text-left">
                                            <?php if ($sort == 'shipping_profile_title') { ?>
                                                <a href="<?php echo $sort_shipping_profile_title; ?>" class="<?php echo $order; ?>"><?php echo $column_shipping_title; ?></a>
                                            <?php } else { ?>
                                                <a href="<?php echo $sort_shipping_profile_title; ?>"><?php echo $column_shipping_title; ?></a>
                                            <?php } ?>
                                        </td>
                                        <td class="text-left">
                                            <?php if ($sort == 'shipping_origin_country') { ?>
                                                <a href="<?php echo $sort_shipping_origin_country; ?>" class="<?php echo $order; ?>"><?php echo $column_shipping_origin; ?></a>
                                            <?php } else { ?>
                                                <a href="<?php echo $sort_shipping_origin_country; ?>"><?php echo $column_shipping_origin; ?></a>
                                            <?php } ?></td>
                                        <td class="text-left">
                                            <?php if ($sort == 'shipping_min_process_days') { ?>
                                                <a href="<?php echo $sort_shipping_min_process_days; ?>" class="<?php echo $order; ?>"><?php echo $column_min_processing; ?></a>
                                            <?php } else { ?>
                                                <a href="<?php echo $sort_shipping_min_process_days; ?>"><?php echo $column_min_processing; ?></a>
                                            <?php } ?></td>
                                        <td class="text-left">
                                            <?php if ($sort == 'shipping_max_process_days') { ?>
                                                <a href="<?php echo $sort_shipping_max_process_days; ?>" class="<?php echo $order; ?>"><?php echo $column_max_processing; ?></a>
                                            <?php } else { ?>
                                                <a href="<?php echo $sort_shipping_max_process_days; ?>"><?php echo $column_max_processing; ?></a>
                                            <?php } ?></td>
                                        <td class="text-right"><?php echo $column_action; ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($shipping_profiles) { ?>
                                        <?php foreach ($shipping_profiles as $templates) { ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php if (in_array($templates['id_etsy_shipping_profiles'], $selected)) { ?>
                                                        <input type="checkbox" name="selected[]" value="<?php echo $templates['id_etsy_shipping_profiles']; ?>" checked="checked" />
                                                    <?php } else { ?>
                                                        <input type="checkbox" name="selected[]" value="<?php echo $templates['id_etsy_shipping_profiles']; ?>" />
                                                    <?php } ?>
                                                </td>
                                                <td class="text-left"><?php echo $templates['id_etsy_shipping_profiles']; ?></td>
                                                <td class="text-left"><?php echo $templates['shipping_profile_title']; ?></td>
                                                <td class="text-left"><?php echo $templates['shipping_origin_country']; ?></td>
                                                <td class="text-left"><?php echo $templates['shipping_min_process_days']; ?></td>
                                                <td class="text-left"><?php echo $templates['shipping_max_process_days']; ?></td>
                                                <td class="text-right">
                                                    <a href="<?php echo $templates['add']; ?>" data-toggle="tooltip" title="<?php echo $button_add_entry; ?>" class="btn btn-warning"><i class="fa fa-plus"></i></a>
                                                    <a href="<?php echo $templates['view']; ?>" data-toggle="tooltip" title="<?php echo $button_view_entries; ?>" class="btn btn-default"><i class="fa fa-eye"></i></a>
                                                    <a href="<?php echo $templates['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                    <a href="<?php echo $templates['delete']; ?>" onclick="return window.confirm('<?php echo $text_confirm_delete_etsy; ?>');" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td class="text-left" colspan="9">
                                                <div class="alert alert-warning">
                                                    <i class="fa fa-check-circle"></i> <?php echo $text_no_shipping_profile_error; ?>
                                                </div>
                                            </td>
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
                <?php } else { ?>
                    <div class="">
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div>
                                    <?php echo $country_missing_error; ?>
                                </div>
                                <div style="margin-top:20px">
                                    <a target="_blank" href="<?php echo $etsy_country_sync_url; ?>" class="btn btn-primary"><?php echo $text_sync_now; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript"><!--
$('#button-filter').on('click', function () {
        var url = 'index.php?route=<?php echo $module_path; ?>/shippingProfiles&<?php echo $session_token_key; ?>=<?php echo $token; ?>';

        var filter_shipping_name = $('input[name=\'filter_shipping_name\']').val();
        if (filter_shipping_name) {
            url += '&filter_shipping_name=' + encodeURIComponent(filter_shipping_name);
        }

        var filter_shipping_country = $('input[name=\'filter_shipping_country\']').val();
        if (filter_shipping_country) {
            url += '&filter_shipping_country=' + encodeURIComponent(filter_shipping_country);
        }

        var filter_min_proc_days = $('input[name=\'filter_min_proc_days\']').val();
        if (filter_min_proc_days) {
            url += '&filter_min_proc_days=' + encodeURIComponent(filter_min_proc_days);
        }

        var filter_max_proc_days = $('input[name=\'filter_max_proc_days\']').val();
        if (filter_max_proc_days) {
            url += '&filter_max_proc_days=' + encodeURIComponent(filter_max_proc_days);
        }
        location = url;
    });

    $('#button-refresh').click(function (e) {
        var url = 'index.php?route=<?php echo $module_path; ?>/shippingProfiles&<?php echo $session_token_key; ?>=<?php echo $token; ?>';
        location = url;
    });

//-->
</script>
<?php echo $footer; ?>