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
        <?php if (isset($error['error_warning'])) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
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
                <h3 class="panel-title"><i class="fa fa-arrow-down"></i><?php echo $text_order_listing; ?></h3>
            </div>            
            <div class="panel-body">
               <?php echo $tabs; ?>
                <div class="well">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-order-id"><?php echo $entry_order_id; ?></label>
                                <input type="text" name="filter_order_id" value="<?php echo $filter_order_id; ?>" placeholder="<?php echo $entry_order_id; ?>" id="input-order-id" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-customer"><?php echo $entry_customer; ?></label>
                                <input type="text" name="filter_customer" value="<?php echo $filter_customer; ?>" placeholder="<?php echo $entry_customer; ?>" id="input-customer" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-order-status"><?php echo $entry_order_status; ?></label>
                                <select name="filter_order_status" id="input-order-status" class="form-control">
                                    <option value="*"></option>
                                    <?php if ($filter_order_status == '0') { ?>
                                        <option value="0" selected="selected"><?php echo $text_missing; ?></option>
                                    <?php } else { ?>
                                        <option value="0"><?php echo $text_missing; ?></option>
                                    <?php } ?>
                                    <?php foreach ($order_statuses as $order_status) { ?>
                                        <?php if ($order_status['order_status_id'] == $filter_order_status) { ?>
                                            <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-total"><?php echo $entry_total; ?></label>
                                <input type="text" name="filter_total" value="<?php echo $filter_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-date-added"><?php echo $entry_date_added; ?></label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_added" value="<?php echo $filter_date_added; ?>" placeholder="<?php echo $entry_date_added; ?>" data-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-date-modified"><?php echo $entry_date_modified; ?></label>
                                <div class="input-group date">
                                    <input type="text" name="filter_date_modified" value="<?php echo $filter_date_modified; ?>" placeholder="<?php echo $entry_date_modified; ?>" data-format="YYYY-MM-DD" id="input-date-modified" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                            <button type="button" id="button-refresh" class="btn btn-default pull-right" style="margin-right: 2px;"><i class="fa fa-refresh"></i> <?php echo $button_reset; ?></button>&nbsp;
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
                                            <td class="text-left"><?php if ($sort == 'o.order_id') { ?>
                                                    <a href="<?php echo $sort_order; ?>" class="<?php echo $order; ?>"><?php echo $column_order_id; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_order; ?>"><?php echo $column_order_id; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php if ($sort == 'eol.id_etsy_order') { ?>
                                                    <a href="<?php echo $sort_etsy_order; ?>" class="<?php echo $order; ?>"><?php echo $column_id_etsy_order; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_etsy_order; ?>"><?php echo $column_id_etsy_order; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php if ($sort == 'customer') { ?>
                                                    <a href="<?php echo $sort_customer; ?>" class="<?php echo $order; ?>"><?php echo $column_customer; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_customer; ?>"><?php echo $column_customer; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php if ($sort == 'status') { ?>
                                                    <a href="<?php echo $sort_status; ?>" class="<?php echo $order; ?>"><?php echo $column_status; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php if ($sort == 'o.total') { ?>
                                                    <a href="<?php echo $sort_total; ?>" class="<?php echo $order; ?>"><?php echo $column_total; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_total; ?>"><?php echo $column_total; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-left"><?php if ($sort == 'o.date_added') { ?>
                                                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo $order; ?>"><?php echo $column_date_added; ?></a>
                                                <?php } else { ?>
                                                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                                                <?php } ?>
                                            </td>
                                            <td class="text-right"><?php echo $column_action; ?></td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($etsy_orders) { ?>
                                            <?php foreach ($etsy_orders as $etsy_order) { ?>
                                                <tr>
                                                    <td class="text-left"><?php echo $etsy_order['order_id']; ?></td>
                                                    <td class="text-left"><?php echo $etsy_order['id_etsy_order']; ?></td>
                                                    <td class="text-left"><?php echo $etsy_order['customer']; ?></td>
                                                    <td class="text-left"><?php echo $etsy_order['status']; ?></td>
                                                    <td class="text-right"><?php echo $etsy_order['total']; ?></td>
                                                    <td class="text-left"><?php echo $etsy_order['date_added']; ?></td>
                                                    <td class="text-right"><a href="<?php echo $etsy_order['view']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-eye"></i></a></td>
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
    var url = 'index.php?route=<?php echo $module_path; ?>/orderListing&<?php echo $session_token_key; ?>=<?php echo $token; ?>';

    var filter_order_id = $('input[name=\'filter_order_id\']').val();
    if (filter_order_id) {
        url += '&filter_order_id=' + encodeURIComponent(filter_order_id);
    }

    var filter_customer = $('input[name=\'filter_customer\']').val();
    if (filter_customer) {
        url += '&filter_customer=' + encodeURIComponent(filter_customer);
    }

    var filter_order_status = $('select[name=\'filter_order_status\']').val();
    if (filter_order_status != '*') {
        url += '&filter_order_status=' + encodeURIComponent(filter_order_status);
    }

    var filter_total = $('input[name=\'filter_total\']').val();
    if (filter_total) {
        url += '&filter_total=' + encodeURIComponent(filter_total);
    }

    var filter_date_added = $('input[name=\'filter_date_added\']').val();
    if (filter_date_added) {
        url += '&filter_date_added=' + encodeURIComponent(filter_date_added);
    }

    var filter_date_modified = $('input[name=\'filter_date_modified\']').val();
    if (filter_date_modified) {
        url += '&filter_date_modified=' + encodeURIComponent(filter_date_modified);
    }
    location = url;
});

$('#button-refresh').click(function (e) {
    var url = 'index.php?route=<?php echo $module_path; ?>/orderListing&<?php echo $session_token_key; ?>=<?php echo $token; ?>';
    location = url;
});
    //-->
</script>
<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
<script type="text/javascript"><!--
$('.date').datetimepicker({
   pickTime: false
});
//-->
</script>
<?php echo $footer; ?>