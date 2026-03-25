<?php if($config->get('awesome_social_login_ozxmod_status')) { ?>
	
<!-- CSS -->
<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/ozxmod/assets/1/css/style.css">
	
	
<div  id="ozxmod_login_head">
<div class="modal fade" id="ozxmod_login_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog form-box" role="document">
	    <div class="modal-content form-top">
	      <div class="modal-header">
	      	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      	<div id="tabs-login" class="my-htabs">
				<a id="ajaxlogintab" href="#tab-login"><?php echo $tab_login; ?></a>
				<a id="ajaxsignuptab" href="#tab-register"><?php echo $tab_signup; ?></a>
			</div>
	      </div>
	      <div class="modal-body">
			<div class="row">
				<?php
					$col = 12; 
					if($config->get('awesome_social_login_ozxmod_social_status')){
						$col = 7;
					} 
				 ?>
				<div class="col-sm-<?php echo $col; ?>" left-part">
					<div id="tab-login" class="my-tab-content">
						<div id="login_details">
							<div class="form-group">
				                <input type="text" name="ajax_email" value="" placeholder="<?php echo $text_email; ?>" class="form-control" />
			              	</div>
			              	<div class="form-group">
				                <input type="password" name="ajax_password" value="" placeholder="<?php echo $text_password; ?>" class="form-control" />
			              	</div>
			              	<div class="form-group">
				                <input type="button" value="<?php echo $button_social_login; ?>" id="button-login-pop" class="btn btn-primary" />
			              	</div>
			              	<div class="form-group">
			              		<a id="forgot_password"><?php echo $text_forgot; ?></a>
			              	</div>
						</div>
						<div id="forgot_password_div" style="display:none;">
							<div class="form-group">
				                <label class="control-label"><?php echo $text_forgot_desc; ?></label>
			              	</div>
			              	<div class="form-group">
				                <input type="text" name="ajax_forgot_email" value="" placeholder="<?php echo $text_email; ?>" class="form-control" />
			              	</div>
			              	<div class="form-group">
				                <input type="button" value="<?php echo $button_send; ?>" id="button-forgot-password" class="btn btn-primary" />
			              	</div>
						</div>
					</div>
					<div id="tab-register" class="my-tab-content">
						<div id="signup_details">
							<?php if($config->get('awesome_social_login_ozxmod_name_signup')) { ?>
								<div class="form-group required">
					                <input type="text" name="ajax_register_name" value="" placeholder="<?php echo $text_name; ?>" class="form-control" />
				              	</div>
			              	<?php } ?>
							<div class="form-group required">
				                <input type="text" name="ajax_register_email" value="" placeholder="<?php echo $text_email; ?>" class="form-control" />
			              	</div>
			              	<?php if($config->get('awesome_social_login_ozxmod_telephone_signup')) { ?>
				              	<div class="form-group required">
					                <input type="text" name="ajax_register_telephone" value="" placeholder="<?php echo $text_telephone; ?>" class="form-control" />
				              	</div>
			              	<?php } ?>
			              	<div class="form-group required">
				                <input type="password" name="ajax_register_password" value="" placeholder="<?php echo $text_password; ?>" class="form-control" />
			              	</div>
			              	<div class="form-group required">
				                <input type="password" name="re_ajax_register_password" value="" placeholder="<?php echo $text_repeat; ?>" class="form-control" />
			              	</div>
			              	<div class="form-group">
				                <input type="button" value="<?php echo $button_signup; ?>" id="button-register-pop" class="btn btn-primary" />
			              	</div>
						</div>
					</div>
				</div>
				<?php if($config->get('awesome_social_login_ozxmod_social_status')){ ?>
					<div class="col-sm-5">
						<div id="signin_with_div">
							<div class="signin-div"><?php echo $text_sign_with; ?></div>
							
					         	<div class="social-login">
							    	<div class="social-login-buttons">
							    		<?php if($config->get('awesome_social_login_ozxmod_display_fb')) { ?>
								        	<a class="btn btn-link-2 facebook" href="<?php echo $fb_login; ?>">
								        		<i class="fa fa-facebook"></i> <?php echo $text_fb_title; ?>
								        	</a>
							        	<?php } ?>
							        	<?php if($config->get('awesome_social_login_ozxmod_display_google')) { ?>
								        	<a class="btn btn-link-2 gplus" href="<?php echo $g_login; ?>">
								        		<i class="fa fa-google-plus"></i> <?php echo $text_google_title; ?>
								        	</a>
								        <?php } ?>
								        <?php if($config->get('awesome_social_login_ozxmod_display_twitter')) { ?>
								        	<a class="btn btn-link-2 twit_link twitter" >
								        		<i class="fa fa-twitter"></i> <?php echo $text_twitter_title; ?>
								        	</a>
								        <?php } ?>
								        <?php if($config->get('awesome_social_login_ozxmod_display_linkedin')) { ?>
								        	<a class="btn btn-link-2 linkedin" href="<?php echo $linkedin_login; ?>">
								        		<i class="fa fa-linkedin"></i> <?php echo $text_linkedin_title; ?>
								        	</a>
								        <?php } ?>
							    	</div>
						    	</div>
						</div>
					  </div>
				   <?php } ?>
			</div>
			</div>
			<div class="login_footer">
				<span class="footer_close" id="footer_close"><?php echo $text_close_window; ?> <b>X</b></span>
			</div>
		</div>
		</div>
		</div>
</div>
<div class="modal fade" id="ozxmod_twitter_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog modal-sm" role="document">
	    <div class="modal-content form-top">
	      <div class="modal-header">
	        <div id="tabs-twitter-login">
				<a><?php echo $tab_twitter; ?></a>
			</div>
	      </div>
	      <div class="modal-body">
	        <div class="form-bottom" id="twitter_login_details">
            	<div class="form-group">
                	<input type="text" name="ajax_twit_email" value="" placeholder="<?php echo $text_twit_email; ?>" class="form-control" />
                </div>
                <button id="button-twit-login" class="btn btn-primary"><?php echo $button_social_login; ?></button>
	        </div>
	      </div>
	      <div class="login_footer">
			<span class="footer_close" id="footer_twitter_close"><?php echo $text_close_window; ?> <b>X</b></span>
		  </div>
	    </div>
	  </div>
	</div>

<!-- Javascript -->
<script>
	var ozxmod_twit_error = "<?php echo $ozxmod_twit_error; ?>";
</script>
<script src="catalog/view/theme/default/stylesheet/ozxmod/assets/1/js/scripts.js"></script>
<script type="text/javascript" src="catalog/view/theme/default/stylesheet/ozxmod/assets/1/js/tabs.js"></script>
<?php } ?>
