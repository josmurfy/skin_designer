<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
                <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
                <h3 class="panel-title"><i class="fa fa-user"></i><?php echo $text_edit_profile; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <div class="well">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="profile_name"><?php echo $text_filter_profile_name; ?></label>
                                <input type="text" name="filter_profile_name" value="<?php echo $filter_profile_name; ?>" placeholder="<?php echo $text_filter_profile_name; ?>" id="profile_name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="shipping_name"><?php echo $text_filter_shipping_name; ?></label>
                                <input type="text" name="filter_shipping_name" value="<?php echo $filter_shipping_name; ?>" placeholder="<?php echo $text_filter_shipping_name; ?>" id="shipping_name" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <br/>
                                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                                <button type="button" id="button-refresh" class="btn btn-default pull-right" style="margin-right: 2px;"><i class="fa fa-refresh"></i> <?php echo $button_reset; ?></button>&nbsp;
                            </div>
                        </div>
                    </div>
                </div>
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general"> 
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>

                                            <td class="text-left"><?php if ($sort == 'p.id_etsy_profiles') { ?>
                                                <a href="<?php echo $sort_id_etsy_profiles; ?>" class="<?php echo $order; ?>"><?php echo $column_profile_id; ?></a>
                                                <?php } else { ?>
                                                <a href="<?php echo $sort_id_etsy_profiles; ?>"><?php echo $column_profile_id; ?></a>
                                                <?php } ?></td>
                                            <td class="text-left"><?php if ($sort == 'p.profile_title') { ?>
                                                <a href="<?php echo $sort_profile_title; ?>" class="<?php echo $order; ?>"><?php echo $column_profile_title; ?></a>
                                                <?php } else { ?>
                                                <a href="<?php echo $sort_profile_title; ?>"><?php echo $column_profile_title; ?></a>
                                                <?php } ?></td>
                                            <td class="text-left"><?php if ($sort == 'p.etsy_category_code') { ?>
                                                <a href="<?php echo $sort_etsy_category; ?>" class="<?php echo $order; ?>"><?php echo $column_profile_category; ?></a>
                                                <?php } else { ?>
                                                <a href="<?php echo $sort_etsy_category; ?>"><?php echo $column_profile_category; ?></a>
                                                <?php } ?></td>
                                            <td class="text-left"><?php if ($sort == 'st.shipping_profile_title') { ?>
                                                <a href="<?php echo $sort_shipping_profile_title; ?>" class="<?php echo $order; ?>"><?php echo $column_profile_shipping; ?></a>
                                                <?php } else { ?>
                                                <a href="<?php echo $sort_shipping_profile_title; ?>"><?php echo $column_profile_shipping; ?></a>
                                                <?php } ?></td>
                                            <td class="text-left"><?php if ($sort == 'p.active') { ?>
                                                <a href="<?php echo $sort_active; ?>" class="<?php echo $order; ?>"><?php echo $column_profile_status; ?></a>
                                                <?php } else { ?>
                                                <a href="<?php echo $sort_active; ?>"><?php echo $column_profile_status; ?></a>
                                                <?php } ?></td>
                                            <td class="text-left"><?php if ($sort == 'p.date_added') { ?>
                                                <a href="<?php echo $sort_date_added; ?>" class="<?php echo $order; ?>"><?php echo $column_profile_added; ?></a>
                                                <?php } else { ?>
                                                <a href="<?php echo $sort_date_added; ?>"><?php echo $column_profile_added; ?></a>
                                                <?php } ?></td>
                                            <td class="text-left"><?php if ($sort == 'p.date_updated') { ?>
                                                <a href="<?php echo $sort_date_updated; ?>" class="<?php echo $order; ?>"><?php echo $column_profile_updated; ?></a>
                                                <?php } else { ?>
                                                <a href="<?php echo $sort_date_updated; ?>"><?php echo $column_profile_updated; ?></a>
                                                <?php } ?></td>
                                            <td class="text-right"><?php echo $column_action; ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($profiles) { ?>
                                        <?php foreach ($profiles as $profile) { ?>
                                        <tr>
                                            <td class="text-center">
                                                <?php if (in_array($profile['id_etsy_profiles'], $selected)) { ?>
                                                <input type="checkbox" name="selected[]" value="<?php echo $profile['id_etsy_profiles']; ?>" checked="checked" />
                                                <?php } else { ?>
                                                <input type="checkbox" name="selected[]" value="<?php echo $profile['id_etsy_profiles']; ?>" />
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php echo $profile['id_etsy_profiles']; ?></td>
                                            <td class="text-left"><?php echo $profile['profile_title']; ?></td>
                                            <td class="text-left"><?php echo $profile['category_name']; ?></td>
                                            <td class="text-left"><?php echo $profile['shipping_profile_title']; ?></td>
                                            <td class="text-left"><?php echo $profile['active']; ?></td>
                                            <td class="text-left"><?php echo $profile['date_added']; ?></td>
                                            <td class="text-left"><?php echo $profile['date_updated']; ?></td>
                                            <td class="text-right">
                                                <a href="<?php echo $profile['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a>
                                                <a href="<?php echo $profile['delete']; ?>" onclick="return window.confirm('<?php echo $text_profile_confirm_delete_etsy; ?>');" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>
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
                        </div>
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
        var url = 'index.php?route=<?php echo $module_path; ?>/profileManagement&<?php echo $session_token_key; ?>=<?php echo $token; ?>';

        var filter_profile_name = $('input[name=\'filter_profile_name\']').val();

        if (filter_profile_name) {
            url += '&filter_profile_name=' + encodeURIComponent(filter_profile_name);
        }

        var filter_shipping_name = $('input[name=\'filter_shipping_name\']').val();

        if (filter_shipping_name) {
            url += '&filter_shipping_name=' + encodeURIComponent(filter_shipping_name);
        }

        location = url;
    });
    $('#button-refresh').click(function (e) {
        var url = 'index.php?route=<?php echo $module_path; ?>/profileManagement&<?php echo $session_token_key; ?>=<?php echo $token; ?>';
        location = url;
    });
//--></script>
<?php echo $footer; ?>