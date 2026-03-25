<?php if($config->get('awesome_social_login_ozxmod_status')) { ?>
<div class="social-login-account">
	<h3><?php echo $text_2_login_with_checkout; ?></h3>
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
        	<a class="btn btn-link-2 twit_link twitter">
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

