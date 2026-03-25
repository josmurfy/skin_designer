<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-webpush" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <?php if ($error_warning) { ?>
            <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i><?php echo $text_edit; ?></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="alert alert-warning text-center">
                          <i class="fa fa-exclamation-circle"></i>&nbsp;&nbsp;<?php echo $text_info; ?>
                        </div>
                    </div>
                </div>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-webpush" class="form-horizontal">
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-appId"><?php echo $entry_appId; ?></label>
                        <div class="col-sm-10">
                            <input name="module_webpush_appId" placeholder="<?php echo $entry_appId; ?>" id="input-appId" class="form-control" value="<?php echo $module_webpush_appId; ?>"></input>
                            <span title="<?php echo $entry_appId; ?>" style="color: #999; font-style: italic"><?php echo $help_appId; ?></span>
                            <?php if($error_appId): ?>
                                <div class="text-danger"><?php echo $error_appId; ?></div>
                            <?php endif ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-bellStatus"><?php echo $entry_bellStatus; ?></label>
                        <div class="col-sm-10">
                            <select name="module_webpush_bellStatus" id="input-bellStatus" class="form-control">
                                <option value="true" <?php echo ($module_webpush_bellStatus == "true") ? 'selected' : ''; ?> ><?php echo $text_yes  ?></option>
                                <option value="false" <?php echo ($module_webpush_bellStatus == "false") ? 'selected' : ''; ?> ><?php echo $text_no  ?></option>
                            </select>
                            <span title="<?php echo $entry_bellStatus; ?>" style="color: #999; font-style: italic"><?php echo $help_bellStatus; ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-position"><?php echo $entry_position; ?></label>
                        <div class="col-sm-10">
                            <select name="module_webpush_position" id="input-position" class="form-control">
                                <option value="bottom-right" <?php echo ($module_webpush_position == "bottom-right") ? 'selected' : ''; ?> ><?php echo $text_bottom_right; ?></option>
                                <option value="bottom-left" <?php echo ($module_webpush_position == "bottom-left") ? 'selected' : ''; ?>><?php echo $text_bottom_left; ?></option>
                            </select>
                            <span title="<?php echo $entry_position; ?>" style="color: #999; font-style: italic"><?php echo $help_position; ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-size"><?php echo $entry_size; ?></label>
                        <div class="col-sm-10">
                            <select name="module_webpush_size" id="input-size" class="form-control">
                                <option value="small" <?php echo ($module_webpush_size == "small") ? 'selected' : ''; ?> ><?php echo $text_small; ?></option>
                                <option value="medium" <?php echo ($module_webpush_size == "medium") ? 'selected' : ''; ?>><?php echo $text_medium  ?></option>
                                <option value="large" <?php echo ($module_webpush_size == "large") ? 'selected' : ''; ?>><?php echo $text_large; ?></option>
                            </select>
                            <span title="<?php echo $entry_size; ?>" style="color: #999; font-style: italic"><?php echo $help_size; ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-autoRegister"><?php echo $entry_autoRegister; ?></label>
                        <div class="col-sm-10">
                            <select name="module_webpush_autoRegister" id="input-autoRegister" class="form-control">
                                <option value="true" <?php echo ($module_webpush_autoRegister == "true") ? 'selected' : ''; ?>><?php echo $text_enabled  ?></option>
                                <option value="false" <?php echo ($module_webpush_autoRegister == "false") ? 'selected' : ''; ?> ><?php echo $text_disabled; ?></option>
                            </select>
                            <span title="<?php echo $entry_autoRegister; ?>" style="color: #999; font-style: italic"><?php echo $help_autoRegister; ?></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
                        <div class="col-sm-10">
                            <select name="module_webpush_status" id="input-status" class="form-control">
                                <?php if($module_webpush_status): ?>
                                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                    <option value="0"><?php echo $text_disabled; ?></option>
                                <?php else: ?>
                                    <option value="1"><?php echo $text_enabled; ?></option>
                                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php endif ?>
                            </select>
                            <span title="<?php echo $entry_status; ?>" style="color: #999; font-style: italic"><?php echo $help_onesignalStatus; ?></span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php echo $footer; ?>
