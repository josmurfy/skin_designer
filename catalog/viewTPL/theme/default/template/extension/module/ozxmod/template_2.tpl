<?php if($config->get('awesome_social_login_ozxmod_status')) { ?>
<!-- CSS -->        
<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/ozxmod/assets/2/css/form-elements.css">
<link rel="stylesheet" href="catalog/view/theme/default/stylesheet/ozxmod/assets/2/css/style.css">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->	

<!-- Modal -->
<div id="ozxmod_login_head">
	<div class="modal fade" id="ozxmod_login_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog form-box" role="document">
	    <div class="modal-content form-top">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        	<div class="form-top-left">
    			<h3><?php echo $text_2_login_head; ?></h3>
        		<p><?php echo $text_2_login_summary; ?></p>
    		</div>
    		<div class="form-top-right">
    			<i class="fa fa-lock"></i>
    		</div>
	      </div>
	      <div class="modal-body">
	        <div class="form-bottom" id="login_details">
	            	<div class="form-group">
	                	<input type="text" name="ajax_email" value="" placeholder="<?php echo $text_email; ?>" class="form-control" />
	                </div>
	                <div class="form-group">
	                	<input type="password" name="ajax_password" value="" placeholder="<?php echo $text_password; ?>" class="form-control" />
	                </div>
	                <button id="button-login-pop" class="btn"><?php echo $button_social_login; ?></button>
	            	
	        </div>
	        <div class="forgot-div">
	            	<div class="form-group">
	                	<span class=""><?php echo $text_2_login_already; ?></span>
	                </div>
	        </div>
	        <div class="form-bottom" id="forgot_password_div" style="display:none;">
				<div class="form-group">
	                <label class="control-label"><?php echo $text_forgot_desc; ?></label>
              	</div>
              	<div class="form-group">
	                <input type="text" name="ajax_forgot_email" value="" placeholder="<?php echo $text_email; ?>" class="form-control" />
              	</div>
              	<button id="button-forgot-password" class="btn"><?php echo $button_send; ?></button>
              	
			</div>
	        <?php if($config->get('awesome_social_login_ozxmod_social_status')){ ?>
		        <div class="social-login">
			    	<h3><?php echo $text_2_login_with; ?></h3>
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
			 <?php } ?>   
	      </div>
	    </div>
	  </div>
	</div>
	
	<div class="modal fade" id="ozxmod_signup_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog form-box" role="document">
	    <div class="modal-content form-top">
	      <div class="modal-header">
	      		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<div class="form-top-left">
	    			<h3><?php echo $text_2_signup_head; ?></h3>
	        		<p><?php echo $text_2_signup_summary; ?></p>
	    		</div>
	    		<div class="form-top-right">
	    			<i class="fa fa-pencil"></i>
	    		</div>
	      </div>
	      <div class="modal-body">
	        <div class="form-bottom" id="signup_details">
	        	<?php if($config->get('awesome_social_login_ozxmod_name_signup')) { ?>
            	<div class="form-group">
            		<input type="text" name="ajax_register_name" value="" placeholder="<?php echo $text_name; ?>" class="form-control" />
                </div>
                <?php } ?>
                <div class="form-group">
                	<input type="text" name="ajax_register_email" value="" placeholder="<?php echo $text_email; ?>" class="form-control" />
                </div>
                <?php if($config->get('awesome_social_login_ozxmod_telephone_signup')) { ?>
                <div class="form-group">
                	<input type="text" name="ajax_register_telephone" value="" placeholder="<?php echo $text_telephone; ?>" class="form-control" />
                </div>
                <?php } ?>
                <div class="form-group">
                	<input type="password" name="ajax_register_password" value="" placeholder="<?php echo $text_password; ?>" class="form-control" />
                </div>
                <div class="form-group">
                	<input type="password" name="re_ajax_register_password" value="" placeholder="<?php echo $text_repeat; ?>" class="form-control" />
                </div>
                <button class="btn" id="button-register-pop"><?php echo $button_signup; ?></button>
	         </div>
	         <div class="forgot-div">
            	<div class="form-group">
                	<span class=""><?php echo $text_2_signup_already; ?></span>
                </div>
	        </div>
	        <?php if($config->get('awesome_social_login_ozxmod_social_status')){ ?>
	         	<div class="social-login">
			    	<h3><?php echo $text_2_login_with; ?></h3>
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
		    <?php } ?>
	      </div>
	    </div>
	  </div>
	</div>
	<div class="modal fade" id="ozxmod_twitter_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog form-box" role="document">
	    <div class="modal-content form-top">
	      <div class="modal-header">
	        	<button type="button" id="twitter_close" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        	<div class="form-top-left">
	    			<h3><?php echo $tab_twitter; ?></h3>
	        		<p><?php echo $text_2_twitter_summary; ?></p>
        		</div>
        		<div class="form-top-right">
	    			<i class="fa fa-lock"></i>
	    		</div>
	      </div>
	      <div class="modal-body">
	        <div class="form-bottom" id="twitter_login_details">
            	<div class="form-group">
                	<input type="text" name="ajax_twit_email" value="" placeholder="<?php echo $text_twit_email; ?>" class="form-control" />
                </div>
                <button id="button-twit-login" class="btn"><?php echo $button_social_login; ?></button>
	        </div>
	      </div>
	    </div>
	  </div>
	</div>
</div>

<!-- Javascript -->
<script>
	var ozxmod_twit_error = "<?php echo $ozxmod_twit_error; ?>";
</script>
<script src="catalog/view/theme/default/stylesheet/ozxmod/assets/2/js/scripts.js"></script>
<?php } ?>

