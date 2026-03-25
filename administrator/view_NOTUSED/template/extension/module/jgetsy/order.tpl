<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-etsy-order" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <?php if (isset($error['error_warning'])) { ?>
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
                <h3 class="panel-title"><i class="fa fa-shopping-cart"></i><?php echo $text_edit_order; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-etsy-order" class="form-horizontal">
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-general">             
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="etsy-order-default-status"><?php echo $text_status_order_default; ?></label>
                                <div class="col-sm-10">
                                    <select name="etsy[order][defauls_status]" id="etsy-order-default-status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $etsy_order_default_status_id) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="etsy-order-paid-status"><?php echo $text_status_order_paid; ?></label>
                                <div class="col-sm-10">
                                    <select name="etsy[order][paid_status]" id="etsy-order-paid-status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $etsy_order_paid_status_id) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="etsy-order-shipped-status"><?php echo $text_status_order_shipped; ?></label>
                                <div class="col-sm-10">
                                    <select name="etsy[order][shipped_status]" id="etsy-order-shipped-status" class="form-control">
                                        <?php foreach ($order_statuses as $order_status) { ?>
                                            <?php if ($order_status['order_status_id'] == $etsy_order_shipped_status_id) { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                                            <?php } else { ?>
                                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                            <?php } ?>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>