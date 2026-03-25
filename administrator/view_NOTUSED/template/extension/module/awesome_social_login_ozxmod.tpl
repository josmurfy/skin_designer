<?php 
/* This module is copywrite to ozxmod
 * Author: ozxmod(ozxmod@gmail.com)
 * It is illegal to remove this comment without prior notice to ozxmod(ozxmod@gmail.com)
*/ 
?>
<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-featured" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-featured" class="form-horizontal">   
          <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-popup" data-toggle="tab"><?php echo $tab_popup; ?></a></li>
            <li><a href="#tab-api" data-toggle="tab"><?php echo $tab_api; ?></a></li>
            <li><a href="#tab-support" data-toggle="tab"><?php echo $tab_support; ?></a></li>
          </ul>	
          <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
              <div class="form-group">
                <label class="col-sm-4 control-label"><?php echo $entry_display_at_login; ?></label>
                <div class="col-sm-6">
                	<label class="radio-inline">
                 		<input type="radio" name="awesome_social_login_ozxmod_display_at_login" <?php if($awesome_social_login_ozxmod_display_at_login == "yes") echo 'checked'; ?> value="yes"><?php echo $entry_yes; ?>
                 	</label>
                 	<label class="radio-inline">
			 	  		<input type="radio" name="awesome_social_login_ozxmod_display_at_login" <?php if($awesome_social_login_ozxmod_display_at_login == "no") echo 'checked'; ?> value="no"><?php echo $entry_no; ?>
			 	  	</label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label"><?php echo $entry_display_at_checkout; ?></label>
                <div class="col-sm-6">
                	<label class="radio-inline">
                 		<input type="radio" name="awesome_social_login_ozxmod_display_at_checkout" <?php if($awesome_social_login_ozxmod_display_at_checkout == "yes") echo 'checked'; ?> value="yes"><?php echo $entry_yes; ?>
                 	</label>
                 	<label class="radio-inline">
			 	  		<input type="radio" name="awesome_social_login_ozxmod_display_at_checkout" <?php if($awesome_social_login_ozxmod_display_at_checkout == "no") echo 'checked'; ?> value="no"><?php echo $entry_no; ?>
			 	  	</label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label"><?php echo $entry_display; ?></label>
                <div class="col-sm-6">
                	<label class="checkbox-inline">
                 		<input type="checkbox" name="awesome_social_login_ozxmod_display_fb" <?php echo ($awesome_social_login_ozxmod_display_fb)? 'checked':''; ?> value="1"><?php echo $entry_fb; ?>
                 	</label>
                 	<label class="checkbox-inline">
			 	  		<input type="checkbox" name="awesome_social_login_ozxmod_display_google" <?php echo ($awesome_social_login_ozxmod_display_google)? 'checked':''; ?> value="1"><?php echo $entry_google; ?>
			 	  	</label>
			 	  	<label class="checkbox-inline">
                 		<input type="checkbox" name="awesome_social_login_ozxmod_display_twitter" <?php echo ($awesome_social_login_ozxmod_display_twitter)? 'checked':''; ?> value="1"><?php echo $entry_twitter; ?>
                 	</label>
                 	<label class="checkbox-inline">
			 	  		<input type="checkbox" name="awesome_social_login_ozxmod_display_linkedin" <?php echo ($awesome_social_login_ozxmod_display_linkedin)? 'checked':''; ?> value="1"><?php echo $entry_linkedin; ?>
			 	  	</label>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo htmlspecialchars($text_social_status); ?>"><?php echo $entry_social_status; ?></span></label>
                <div class="col-sm-6">
                	<select name="awesome_social_login_ozxmod_social_status" class="form-control">
				 	  <option value="1" <?php echo ($awesome_social_login_ozxmod_social_status)? 'selected="selected"':''; ?> ><?php echo $text_enable; ?></option>
				 	  <option value="0" <?php echo ($awesome_social_login_ozxmod_social_status)? '' : 'selected="selected"'; ?>><?php echo $text_disable; ?></option>
				 	</select>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo htmlspecialchars($text_status); ?>"><?php echo $entry_status; ?></span></label>
                <div class="col-sm-6">
                	<select name="awesome_social_login_ozxmod_status" class="form-control">
				 	  <option value="1" <?php echo ($awesome_social_login_ozxmod_status)? 'selected="selected"':''; ?> ><?php echo $text_enable; ?></option>
				 	  <option value="0" <?php echo ($awesome_social_login_ozxmod_status)? '' : 'selected="selected"'; ?>><?php echo $text_disable; ?></option>
				 	</select>
                </div>
              </div>
            </div>
            <div class="tab-pane" id="tab-popup">
            	<div class="form-group">
	                <label class="col-sm-4 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo htmlspecialchars($text_name_signup); ?>"><?php echo $entry_name_signup; ?></span></label>
	                <div class="col-sm-6">
	                	<select name="awesome_social_login_ozxmod_name_signup" class="form-control">
					 	  <option value="1" <?php echo ($awesome_social_login_ozxmod_name_signup)? 'selected="selected"':''; ?> ><?php echo $text_enable; ?></option>
					 	  <option value="0" <?php echo ($awesome_social_login_ozxmod_name_signup)? '' : 'selected="selected"'; ?>><?php echo $text_disable; ?></option>
					 	</select>
	                </div>
	              </div>
	             <div class="form-group">
	                <label class="col-sm-4 control-label"><span data-toggle="tooltip" data-html="true" title="<?php echo htmlspecialchars($text_telephone_signup); ?>"><?php echo $entry_telephone_signup; ?></span></label>
	                <div class="col-sm-6">
	                	<select name="awesome_social_login_ozxmod_telephone_signup" class="form-control">
					 	  <option value="1" <?php echo ($awesome_social_login_ozxmod_telephone_signup)? 'selected="selected"':''; ?> ><?php echo $text_enable; ?></option>
					 	  <option value="0" <?php echo ($awesome_social_login_ozxmod_telephone_signup)? '' : 'selected="selected"'; ?>><?php echo $text_disable; ?></option>
					 	</select>
	                </div>
	              </div>
	              <div class="form-group">
	                <label class="col-sm-4 control-label" for="input-theme"><?php echo $entry_theme; ?></label>
	                <div class="col-sm-6">
	                  <select name="awesome_social_login_ozxmod_template_id" id="input-theme" class="form-control">
	                    <?php foreach ($themes as $theme) { ?>
	                    <?php if ($theme['template_id'] == $awesome_social_login_ozxmod_template_id) { ?>
	                    <option key="<?php echo $theme['image']; ?>" value="<?php echo $theme['template_id']; ?>" selected="selected"><?php echo $theme['text']; ?></option>
	                    <?php } else { ?>
	                    <option key="<?php echo $theme['image']; ?>" value="<?php echo $theme['template_id']; ?>"><?php echo $theme['text']; ?></option>
	                    <?php } ?>
	                    <?php } ?>
	                  </select>
	                  <br />
	                  <img src="" alt="" id="theme" class="img-thumbnail" />
	                 </div>
	              </div> 
            </div>
            <div class="tab-pane" id="tab-api">
            	<div class="form-group required">
	                <label class="col-sm-4 control-label"><?php echo $entry_apikey; ?></label>
	                <div class="col-sm-6">
	                	<input type="text" name="awesome_social_login_ozxmod_apikey" id="awesome_social_login_ozxmod_apikey" class="form-control" value="<?php echo $awesome_social_login_ozxmod_apikey; ?>" /><br/>
	                	<?php // echo $text_newfbapp; ?>
	                </div>
	              </div>
	              <div class="form-group required">
	                <label class="col-sm-4 control-label"><?php echo $entry_apisecret; ?></label>
	                <div class="col-sm-6">
	                	<input type="text" name="awesome_social_login_ozxmod_apisecret" id="awesome_social_login_ozxmod_apisecret" class="form-control" value="<?php echo $awesome_social_login_ozxmod_apisecret; ?>" />
	                </div>
	              </div>
	             <div class="form-group required">
	                <label class="col-sm-4 control-label"><?php echo $entry_googleapikey; ?></label>
	                <div class="col-sm-6">
	                	<input type="text" name="awesome_social_login_ozxmod_googleapikey" id="awesome_social_login_ozxmod_googleapikey" class="form-control" value="<?php echo $awesome_social_login_ozxmod_googleapikey; ?>" /><br/>
	                	<?php  echo $text_google_redirect; ?>
	                </div>
	              </div>
	              <div class="form-group required">
	                <label class="col-sm-4 control-label"><?php echo $entry_googleapisecret; ?></label>
	                <div class="col-sm-6">
	                	<input type="text" name="awesome_social_login_ozxmod_googleapisecret" id="awesome_social_login_ozxmod_googleapisecret" class="form-control" value="<?php echo $awesome_social_login_ozxmod_googleapisecret; ?>" />
	                </div>
	              </div>
	             <div class="form-group required">
	                <label class="col-sm-4 control-label"><?php echo $entry_twitterapikey; ?></label>
	                <div class="col-sm-6">
	                	<input type="text" name="awesome_social_login_ozxmod_twitterapikey" id="awesome_social_login_ozxmod_twitterapikey" class="form-control" value="<?php echo $awesome_social_login_ozxmod_twitterapikey; ?>" /><br/>
	                	<?php  echo $text_twitter_redirect; ?>
	                </div>
	              </div>
	              <div class="form-group required">
	                <label class="col-sm-4 control-label"><?php echo $entry_twitterapisecret; ?></label>
	                <div class="col-sm-6">
	                	<input type="text" name="awesome_social_login_ozxmod_twitterapisecret" id="awesome_social_login_ozxmod_twitterapisecret" class="form-control" value="<?php echo $awesome_social_login_ozxmod_twitterapisecret; ?>" />
	                </div>
	              </div> 
	             <div class="form-group required">
	                <label class="col-sm-4 control-label"><?php echo $entry_linkedinapikey; ?></label>
	                <div class="col-sm-6">
	                	<input type="text" name="awesome_social_login_ozxmod_linkedinapikey" id="awesome_social_login_ozxmod_linkedinapikey" class="form-control" value="<?php echo $awesome_social_login_ozxmod_linkedinapikey; ?>" /><br/>
	                	<?php  echo $text_linkedin_redirect; ?>
	                </div>
	              </div>
	              <div class="form-group required">
	                <label class="col-sm-4 control-label"><?php echo $entry_linkedinapisecret; ?></label>
	                <div class="col-sm-6">
	                	<input type="text" name="awesome_social_login_ozxmod_linkedinapisecret" id="awesome_social_login_ozxmod_linkedinapisecret" class="form-control" value="<?php echo $awesome_social_login_ozxmod_linkedinapisecret; ?>" />
	                	<?php  //echo $text_linkedin_redirect; ?>
	                </div>
	              </div>
            </div>
            <div class="tab-pane" id="tab-support">
            	<div class="form-group">
            		<div class="col-sm-2"></div>
	                <div class="col-sm-10">
	                	<?php  echo $text_support; ?>
	                </div>
	            </div>
	            <div class="form-group">
	            	<div class="col-sm-2"></div>
	                <div class="col-sm-10">
	                	<?php  echo $text_visit; ?>
	                </div>
	            </div>
	            <div class="form-group">
	            	<div class="col-sm-2"></div>
	                <div class="col-sm-10">
	                	<?php  echo $text_mail; ?>
	                </div>
	            </div>
            </div>
          </div>  
        </form>
      </div>
    </div>
  </div>
  </div>
<script>
$('#input-theme').on('change', function(){
	$("#theme").attr("src", $("#input-theme option:selected").attr("key"));
});
$(document).ready(function(){
	$('#input-theme').trigger('change');
})

</script>
<?php echo $footer; ?>
<?php 
/* This module is copywrite to ozxmod
 * Author: ozxmod(ozxmod@gmail.com)
 * It is illegal to remove this comment without prior notice to ozxmod(ozxmod@gmail.com)
*/ 
?>