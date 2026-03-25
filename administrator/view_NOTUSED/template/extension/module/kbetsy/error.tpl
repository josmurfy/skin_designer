<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <a href="<?php echo $general_settings; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
        <div class="tab-content">
            <div class="tab-pane active" id="tab-general"> 
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-book"></i><?php echo $text_edit_general; ?></h3>
                    </div>
                    <div class="panel-body">
                        <?php echo $tabs; ?>
                        <div class="row" style="margin: 0 10px">
                            <?php if (isset($error) && $error != "") { ?>
                                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>