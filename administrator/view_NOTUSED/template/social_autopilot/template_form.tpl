<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-template" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-template" class="form-horizontal">
          <fieldset>
            <div class="form-group required">
              <label class="col-sm-2 control-label required" for="input-template-category-id"><?php echo $entry_category; ?></label>
              <div class="col-sm-10">
                 <select name="template_category_id" id="input-template-category-id" class="form-control">
                    <option value=""><?php echo $text_select; ?></option>
                    <?php foreach ($template_categories as $template_category) { ?>
                    <?php if ($template_category['template_category_id'] == $template_category_id) { ?>
                    <option value="<?php echo $template_category['template_category_id']; ?>" selected="selected"><?php echo $template_category['name']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $template_category['template_category_id']; ?>"><?php echo $template_category['name']; ?></option>
                    <?php } ?>
                    <?php } ?>
                </select>

                <?php if ($error_template_category_id) { ?>
                <div class="text-danger"><?php echo $error_template_category_id; ?></div>
                <?php } ?>

                <div class="help"><i class="fa fa-fw fa-info"></i> <?php echo $help_category; ?></div>
              </div>
            </div>
          </fieldset>

          <fieldset>
             <legend class="small text-center"></legend>
             <div class="form-group required">
               <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
               <div class="col-sm-10">
                 <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                 <?php if ($error_name) { ?>
                 <div class="text-danger"><?php echo $error_name; ?></div>
                 <?php } ?>

                 <div class="help"><i class="fa fa-fw fa-info"></i> <?php echo $help_name; ?></div>
               </div>
             </div>
             <div class="form-group required">
               <label class="col-sm-2 control-label" for="input-message"><?php echo $entry_message; ?></label>
               <div class="col-sm-10">
                 <textarea name="message" placeholder="<?php echo $entry_message; ?>" id="input-message" rows="5" class="form-control"><?php echo $message; ?></textarea>
                 <?php if ($error_message) { ?>
                 <div class="text-danger"><?php echo $error_message; ?></div>
                 <?php } ?>

                 <?php if ($template_categories) { ?>
                 <div class="special-keywords">
                    <?php foreach ($template_categories as $template_category) { ?>
                    <?php if (isset(${'help_' . $template_category['code'] . '_special_keyword'})) { ?>
                    <div class="bs-callout sk-template-category-<?php echo $template_category['template_category_id']; ?>">
                       <div class="bs-callout-title"><?php echo $help_special_keyword; ?></div>
                       <div class="bs-callout-content"><?php echo ${'help_' . $template_category['code'] . '_special_keyword'} ?></div>
                    </div>
                    <?php } ?>
                    <?php } ?>
                 </div>
                 <?php } ?>
               </div>
             </div>
          </fieldset>

          <fieldset>
             <legend class="small text-center"></legend>
             <div class="form-group">
               <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
               <div class="col-sm-10">
                 <select name="status" id="input-status" class="form-control">
                   <?php if ($status) { ?>
                   <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                   <option value="0"><?php echo $text_disabled; ?></option>
                   <?php } else { ?>
                   <option value="1"><?php echo $text_enabled; ?></option>
                   <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                   <?php } ?>
                 </select>

                 <?php if ($error_status) { ?>
                 <div class="text-danger"><?php echo $error_status; ?></div>
                 <?php } ?>
               </div>
             </div>
             <div class="form-group">
               <label class="col-sm-2 control-label" for="input-default"><?php echo $entry_default; ?></label>
               <div class="col-sm-10">
                 <select name="default" id="input-default" class="form-control">
                   <?php if ($default) { ?>
                   <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                   <option value="0"><?php echo $text_no; ?></option>
                   <?php } else { ?>
                   <option value="1"><?php echo $text_yes; ?></option>
                   <option value="0" selected="selected"><?php echo $text_no; ?></option>
                   <?php } ?>
                 </select>

                 <?php if ($error_default) { ?>
                 <div class="text-danger"><?php echo $error_default; ?></div>
                 <?php } ?>
               </div>
             </div>
          </fieldset>

        </form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('select[name=\'template_category_id\']').on('change', function() {
   $('.special-keywords .bs-callout').hide();
   $('.special-keywords .sk-template-category-' + $(this).val()).show();
});

$('select[name=\'template_category_id\']').trigger('change');
//--></script>
<?php echo $footer; ?>
