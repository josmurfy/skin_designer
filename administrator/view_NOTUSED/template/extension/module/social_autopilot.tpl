<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-sm-settings" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>

    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">

		<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-sap-settings" class="form-sap-settings form-horizontal">
			<ul class="nav nav-tabs" id="tabs">
				<li class="active"><a href="#tab-general" data-toggle="tab"><i class="fa fa-fw fa-cogs"></i> <?php echo $tab_general; ?></a></li>
				<li><a href="#tab-help" data-toggle="tab"><i class="fa fa-fw fa-question-circle"></i> <?php echo $tab_help; ?></a></li>
			</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="tab-general">
					<fieldset>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status;?></label>
							<div class="col-sm-10">
								<select name="social_autopilot_status" id="input-status" class="form-control">
									<?php if ($social_autopilot_status) { ?>
									<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
									<option value="0"><?php echo $text_disabled; ?></option>
									<?php } else { ?>
									<option value="1"><?php echo $text_enabled; ?></option>
									<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
									<?php } ?>
								</select>
								<div class="help"><i class="fa fa-fw fa-info-circle"></i> <?php echo $help_status; ?></div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label" for="input-api-key"><?php echo $entry_api_key;?></label>
							<div class="col-sm-10">
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-key"></i></span>
									<input type="text" name="social_autopilot_api_key" value="<?php echo $social_autopilot_api_key; ?>" class="form-control" />
								</div>
								<?php if ($error_api_key) { ?>
								<div class="text-danger"><?php echo $error_api_key; ?></div>
								<?php } ?>
								<div class="help"><i class="fa fa-fw fa-info-circle"></i> <?php echo $help_api_key; ?></div>
							</div>
						</div>
               </fieldset>

               <fieldset>
                  <legend class="small text-center"><?php echo $legend_language; ?></legend>
						<div class="form-group required">
							<label class="col-sm-2 control-label" for="input-language-id"><?php echo $entry_language;?></label>
							<div class="col-sm-10">
								<select name="social_autopilot_language_id" id="input-language-id" class="form-control">
								<?php foreach ($languages as $language) { ?>
								<?php if ($language['language_id'] == $social_autopilot_language_id) { ?>
								<option value="<?php echo $language['language_id']; ?>" selected="selected"><?php echo $language['name']; ?></option>
								<?php } else { ?>
								<option value="<?php echo $language['language_id']; ?>"><?php echo $language['name']; ?></option>
								<?php } ?>
								<?php } ?>
								</select>
							</div>
						</div>
					</fieldset>

               <fieldset>
                  <legend class="small text-center"><?php echo $legend_autopost; ?></legend>
                  <div class="form-group">
							<label class="col-sm-2 control-label" for="input-autopost"><?php echo $entry_autopost;?></label>
							<div class="col-sm-10">
								<select name="social_autopilot_autopost" id="input-autopost" class="form-control">
									<?php if ($social_autopilot_autopost == 'manual') { ?>
									<option value="manual" selected="selected"><?php echo $text_mode_manual; ?></option>
									<option value="auto"><?php echo $text_mode_auto; ?></option>
									<option value="ask"><?php echo $text_mode_ask; ?></option>
                        <?php } elseif ($social_autopilot_autopost == 'auto') { ?>
                           <option value="manual"><?php echo $text_mode_manual; ?></option>
   								<option value="auto" selected="selected"><?php echo $text_mode_auto; ?></option>
   								<option value="ask"><?php echo $text_mode_ask; ?></option>
                           <?php } else { ?>
                           <option value="manual"><?php echo $text_mode_manual; ?></option>
   								<option value="auto"><?php echo $text_mode_auto; ?></option>
   								<option value="ask" selected="selected"><?php echo $text_mode_ask; ?></option>
                           <?php } ?>
								</select>
							</div>
						</div>
					</fieldset>

               <fieldset>
                  <legend class="small text-center"><?php echo $legend_rating_star_code; ?></legend>
                  <div class="form-group">
                     <label class="col-sm-2 control-label" for="input-rating-star-code"><?php echo $entry_rating_star_code; ?></label>
                     <div class="col-sm-10">
                        <div class="input-group">
                           <input type="text" name="social_autopilot_rating_star_code" value="<?php echo $social_autopilot_rating_star_code; ?>" class="form-control" />
                           <span class="input-group-addon"><i id="rating-star-preview"></i></span>
                        </div>
                        <?php if ($error_rating_star_code) { ?>
                        <div class="text-danger"><?php echo $error_rating_star_code; ?></div>
                        <?php } ?>

                        <div class="help"><i class="fa fa-fw fa-info-circle"></i> <?php echo $help_rating_star_code; ?></div>
                     </div>
                  </div>
               </fieldset>

               <fieldset>
                  <legend class="small text-center"><?php echo $legend_timezone; ?></legend>
                  <div class="form-group">
                     <label class="col-sm-2 control-label" for="input-timezone-difference"><?php echo $entry_timezone_difference; ?></label>
                     <div class="col-sm-10">
                        <div class="input-group">
                           <input type="text" name="social_autopilot_timezone_difference" value="<?php echo $social_autopilot_timezone_difference; ?>" class="form-control" />
                           <span class="input-group-addon"><?php echo $text_minute; ?></span>
                        </div>
                        <?php if ($error_timezone_difference) { ?>
                        <div class="text-danger"><?php echo $error_timezone_difference; ?></div>
                        <?php } ?>
                        <div class="help"><i class="fa fa-fw fa-info-circle"></i> <?php echo $help_timezone_difference; ?></div>

                        <div class="bs-callout"><?php echo $help_mysql_time; ?></div>
                     </div>
                  </div>
               </fieldset>

               <fieldset class="hidden">
                  <legend class="small text-center"></legend>
                  <div class="form-group">
                     <label class="col-sm-2 control-label" for="input-image-width"><?php echo $entry_image_width; ?></label>
                     <div class="col-sm-10">
                        <div class="input-group">
                           <input type="text" name="social_autopilot_image_width" value="<?php echo $social_autopilot_image_width; ?>" class="form-control" />
                           <span class="input-group-addon"><?php echo $text_pixel; ?></span>
                        </div>
                        <?php if ($error_image_width) { ?>
                        <div class="text-danger"><?php echo $error_image_width; ?></div>
                        <?php } ?>
                        <div class="help"><i class="fa fa-fw fa-info-circle"></i> <?php echo $help_image_width; ?></div>
                     </div>
                  </div>
                  <div class="form-group">
                     <label class="col-sm-2 control-label" for="input-image-height"><?php echo $entry_image_height; ?></label>
                     <div class="col-sm-10">
                        <div class="input-group">
                           <input type="text" name="social_autopilot_image_height" value="<?php echo $social_autopilot_image_height; ?>" class="form-control" />
                           <span class="input-group-addon"><?php echo $text_pixel; ?></span>
                        </div>
                        <?php if ($error_image_height) { ?>
                        <div class="text-danger"><?php echo $error_image_height; ?></div>
                        <?php } ?>
                        <div class="help"><i class="fa fa-fw fa-info-circle"></i> <?php echo $help_image_height; ?></div>
                     </div>
                  </div>

               </fieldset>
				</div>

				<div class="tab-pane" id="tab-help">
					<div class="tab-content">
						HELP Guide is available : <a href="http://www.oc-extensions.com/OpenCart-Social-AutoPilot-Opencart-2.x-Help" target="blank">HERE</a><br /><br />
						If you need support, email us at <strong>support@oc-extensions.com</strong>
					</div>
				</div>
			</div>
		</form>
    </div>
  </div>
<script type="text/javascript"><!--
$(document).ajaxComplete(function(event, request, settings) {
	var license_activation_regex = /.*api\/license\/activate/;

	if (settings.url.match(license_activation_regex)) {
		if (request.responseJSON['license_key']) {
			setTimeout(function(){
				if (sessionStorage.getItem('ocx_module_social_autopilot')) {
					$('input[name=\'social_autopilot_api_key\']').val(sessionStorage.getItem('ocx_module_social_autopilot').replace(/-/g, '').toLowerCase());
				}
            }, 500);
		}
	}
});

$('input[name=\'social_autopilot_rating_star_code\']').on('change', function() {
   $('#rating-star-preview').html($(this).val());
});

$('input[name=\'social_autopilot_rating_star_code\']').trigger('change');
//--></script>
</div>
<?php echo $footer; ?>
