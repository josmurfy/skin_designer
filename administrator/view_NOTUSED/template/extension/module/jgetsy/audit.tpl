<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
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
        <div class="tab-content">
            <div class="tab-pane active" id="tab-general"> 
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-book"></i><?php echo $text_edit_audit; ?></h3>
                    </div>
                    <div class="panel-body">
                        <?php echo $tabs; ?>
                        <div class="well">
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="log_entry"><?php echo $text_filter_log_entry; ?></label>
                                        <input type="text" name="filter_log_entry" value="<?php echo $filter_log_entry; ?>" placeholder="<?php echo $text_filter_log_entry; ?>" id="log_entry" class="form-control" />

                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="control-label" for="log_class_method"><?php echo $text_filter_log_class_method; ?></label>
                                        <input type="text" name="filter_log_class_method" value="<?php echo $filter_log_class_method; ?>" placeholder="<?php echo $text_filter_log_class_method; ?>" id="log_class_method" class="form-control" />

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
                                            <td class="text-left"><?php if ($sort == 'id_etsy_audit_log') { ?>
                                                    <a href="<?php echo $sort_id_etsy_audit_log; ?>" class="<?php echo $order; ?>"><?php echo $column_log_id; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_id_etsy_audit_log; ?>"><?php echo $column_log_id; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php if ($sort == 'log_entry') { ?>
                                                    <a href="<?php echo $sort_log_entry; ?>" class="<?php echo $order; ?>"><?php echo $column_log_description; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_log_entry; ?>"><?php echo $column_log_description; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php if ($sort == 'log_class_method') { ?>
                                                    <a href="<?php echo $sort_log_class_method; ?>" class="<?php echo $order; ?>"><?php echo $column_action_called; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_log_class_method; ?>"><?php echo $column_action_called; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php if ($sort == 'log_time') { ?>
                                                    <a href="<?php echo $sort_log_time; ?>" class="<?php echo $order; ?>"><?php echo $column_time_action; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_log_time; ?>"><?php echo $column_time_action; ?></a>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($audit_log) { ?>
                                            <?php foreach ($audit_log as $log) { ?>
                                                <tr>
                                                    <td class="text-left"><?php echo $log['id_etsy_audit_log']; ?></td>
                                                    <td class="text-left"><?php echo $log['log_entry']; ?></td>
                                                    <td class="text-left"><?php echo $log['log_class_method']; ?></td>
                                                    <td class="text-left"><?php echo $log['log_time']; ?></td>
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
    </div>
</div>

<script type="text/javascript"><!--
$('#button-filter').on('click', function () {
    var url = 'index.php?route=<?php echo $module_path; ?>/auditLog&<?php echo $session_token_key; ?>=<?php echo $token; ?>';

    var filter_log_entry = $('input[name=\'filter_log_entry\']').val();
    if (filter_log_entry) {
        url += '&filter_log_entry=' + encodeURIComponent(filter_log_entry);
    }

    var filter_log_class_method = $('input[name=\'filter_log_class_method\']').val();
    if (filter_log_class_method) {
        url += '&filter_log_class_method=' + encodeURIComponent(filter_log_class_method);
    }
    location = url;
});

$('#button-refresh').click(function (e) {
    var url = 'index.php?route=<?php echo $module_path; ?>/auditLog&<?php echo $session_token_key; ?>=<?php echo $token; ?>';
    location = url;
});
//-->
</script>
<?php echo $footer; ?>