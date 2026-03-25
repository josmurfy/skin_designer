<div id="dash">
	<div class="">
		<div class="col-md-12 col-sm-12 col-xs-12 padd0 dashbox">
			<ul class="list-inline box">
				<?php foreach($setting_dashboards as $settings) { ?>
				<li class="active" style="background:<?php echo $settings['bg_color']; ?>">
					<span style="color:<?php echo $settings['text_color']; ?>">
						<i class="fa <?php echo $settings['icon']; ?>" aria-hidden="true"></i> <?php echo $settings['name']; ?>
					</span>
					<p><?php echo $settings['totalamount']; ?></p>
				</li>
				<?php } ?>
			</ul>	
		</div>
	</div>
</div>