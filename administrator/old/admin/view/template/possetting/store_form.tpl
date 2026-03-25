<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-information" id="btnSubmit" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary">
          <i class="fa fa-save">
          </i>
        </button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default">
          <i class="fa fa-reply">
          </i>
        </a>
      </div>
      <h1>
        <?php echo $heading_title; ?>
      </h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li>
          <a href="<?php echo $breadcrumb['href']; ?>">
            <?php echo $breadcrumb['text']; ?>
          </a>
        </li>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-information" class="form-horizontal">
			<div class="form-group required">
                <label class="col-sm-2 control-label" for="input-package-title">
                  <?php echo $entry_name;?>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="name" value="<?php echo $name;?>"    placeholder="<?php echo $entry_name;?>" id="input-package-titl" class="form-control"/>
					<?php if ($error_name) { ?>
                    <div class="text-danger"><?php echo $error_name; ?></div>
				    <?php } ?>
                </div>
            </div>
			<div class="form-group required">
                <label class="col-sm-2 control-label" for="input-package-title">
                  <?php echo $entry_location;?>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="location" value="<?php echo $location;?>"    placeholder="<?php echo $entry_location;?>" id="input-package-titl" class="form-control"/>
					<?php if ($error_location) { ?>
                    <div class="text-danger"><?php echo $error_location; ?></div>
				    <?php } ?>
                </div>
            </div>
			<div class="form-group required">
                <label class="col-sm-2 control-label" for="input-package-title">
                  <?php echo $entry_phone;?>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="phone" value="<?php echo $phone;?>"    placeholder="<?php echo $entry_phone;?>" id="input-package-titl" class="form-control"/>
					<?php if ($error_phone) { ?>
                    <div class="text-danger"><?php echo $error_phone; ?></div>
				    <?php } ?>
                </div>
            </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enable; ?></option>
                <option value="0"><?php echo $text_disable; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enable; ?></option>
                <option value="0" selected="selected"><?php echo $text_disable; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>


		</form>
    </div>
    </div>
  </div>
  <script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
  <link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
  <script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>  
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
//--></script>
 
<?php echo $footer; ?></div>