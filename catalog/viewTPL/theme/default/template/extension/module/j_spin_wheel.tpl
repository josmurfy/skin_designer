<script>
 var html = '<div id="ocmp_spin_wheel">';
	 html += '<div class="ocmp-<?php echo $spin_wheel['display']; ?>-container" style="<?php if( $spin_wheel['display'] == 'full') { ?> height: 100%; position: fixed; left: 0px; bottom: 0px; top: 0px; z-index: 100000; <?php } ?> display: none;">';
	 html += '		<div class="<?php echo $spin_wheel['display']; ?>-background"></div>';
	 html += '		<div class="ocmp_spin_content">';
	 html += '			<div class="ocmp_form_info">';
	 html += '				<div class="ocmp_offer_description" style="color:<?php echo $spin_wheel['text_color'] ? $spin_wheel['text_color'] : '#ffffff'; ?>">';
	 html += '					<div class="spin-description">';
	 html += '						<div class="ocmp-title"><?php echo isset($spin_wheel[$language_id]) ? $spin_wheel[$language_id]['title'] : ''; ?></div>';
	 html += '						<div class="ocmp-sub-title">';
	 html += '							<span><?php echo isset($spin_wheel[$language_id]) ? $spin_wheel[$language_id]['sub_title'] : ''; ?></span>';
	 html += '						</div>';
						<?php if($notes){ ?>
	 html += '							<ul class="ocmp-note">';	
							<?php foreach($notes as $note){ ?>
	 html += '								<li><?php echo $note; ?></li>';
							<?php } ?>	
	 html += '							</ul>';	
						<?php } ?>	
	 html += '					</div>';	
	 html += '					<div class="success-description" style="display:none;">';
	 html += '					</div>';		
	 html += '					<div class="spin-form">';
	 html += '						<div class="ocmp-input">';	
	 html += '							<div class="ocmp-col-2 left">';
	 html += '								<input type="text" name="firstname" data-type="" placeholder="Firstname" class="form-control">';
	 html += '							</div>';
	 html += '							<div class="ocmp-col-2 right">';		
	 html += '								<input type="text" name="lastname" data-type="" placeholder="Lastname" class="form-control">';
	 html += '							</div>';	
	 html += '						</div>';
	 html += '						<div class="ocmp-email">';		
	 html += '							<input type="text" name="email" placeholder="Please enter email" class="form-control"><br>';
	 html += '						</div>';
	 html += '						<div class="ocmp-button">';	
	 html += '							<button class="btn btn-block btn-lg" id="btn-spin-wheel"><?php echo $spin_wheel[$language_id]['btn_label']; ?></button>';
	 html += '						</div>';
	 html += '						<div class="ocmp-text-noluck text-right">';	
	 html += '							<span class="close_popup"><?php echo $spin_wheel[$language_id]['text_no_luck']; ?></span>';
	 html += '						</div>';	
	 html += '					</div>';
	 html += '				</div>';
	 html += '			</div>';
	 html += '			<div class="ocmp_spinner_preview">';
	 html += '				<div class="ocmp_spinners">';
	 html += '					<div class="ocmp_spinner">';
						<?php $q = 1; $rotate = 0; ?>
						<?php foreach($wheel_offers as $wheel_offer){    ?>
	 html += '							<span class="wheel-offers offer-<?php echo $q; ?>" style="transform: rotate(-<?php echo $rotate; ?>deg) translate(10px, -50%);"><?php echo $wheel_offer['label']; ?></span>';
							<?php $rotate = $q * 30; ?>
						<?php $q++; } ?>	
	 html += '					</div>';
	 html += '					<img class="spin-arrow" src="catalog/view/javascript/jquery/ocmp_spin_wheel/image/spin_arrow.svg" />';
	 html += '				</div>';	
	 html += '			</div>';
	 html += '		</div>';		
	 html += '	</div>';
	 html += '</div>';
	 $("body").append(html);
</script>
<script type="text/javascript">
	var data_status = true;
	var ocmp_wheel_color = "<?php echo isset($spin_wheel['wheel_color']) ? $spin_wheel['wheel_color'] : '#ff0000'; ?>";
	var onRotateURL = "<?php echo $onRotate; ?>";
	var emailExist = "<?php echo $emailCheckUrl; ?>";
	var emailSend = "<?php echo $emailSend; ?>";
	
    var disable_popup = "<?php echo $spin_wheel['hide_after'] * 10; ?>";
    var WheelHexCode = "<?php echo $spin_wheel['wheel_color']; ?>";
	var close_icon = "<?php echo ($spin_wheel['close']) ? 1 : 0; ?>";
	
	var when_to_display = "<?php echo ($spin_wheel['when_to_display']) ? $spin_wheel['when_to_display'] : ''; ?>";
	var spin_display_popup = "<?php echo ($spin_wheel['spin_popup_time']) ? $spin_wheel['spin_popup_time'] : ''; ?>";
	var spin_scroll_popup = "<?php echo ($spin_wheel['spin_scroll_time']) ? $spin_wheel['spin_scroll_time'] : ''; ?>";
	var display_interval = "<?php echo ($spin_wheel['display_interval']) ? $spin_wheel['display_interval'] : 0; ?>";	
	var fireworks_status = "<?php echo ($spin_wheel['firework']) ? $spin_wheel['firework'] : 0; ?>";
	var sound_status = "<?php echo ($spin_wheel['sound']) ? $spin_wheel['sound'] : 0; ?>";
	var display_coupon = "<?php echo ($spin_wheel['display_coupon']) ? $spin_wheel['display_coupon'] : ''; ?>";
    var spinwheel_base_url = "catalog/view/javascript/jquery/ocmp_spin_wheel/image/wheel_preview";
	var send_mail = "<?php echo $send_mail; ?>";
	var btn_continue = "Continue";
	//Error
	var errors = <?php echo $errors; ?>;

	<?php	
		if($spin_wheel['js']){ 
			echo $spin_wheel['js'];
		}
	?>
</script>
<style>
#ocmp_spin_wheel .ocmp_spin_content{
	<?php if($spin_wheel['bg_type'] == 1){ ?>
		background: url('<?php echo $bg_image; ?>');
		background-size:cover;
	<?php }else{ ?>
		background: <?php echo isset($spin_wheel['background_color']) ? $spin_wheel['background_color'] : '#ff0000'; ?>;
	<?php } ?>	
}
#btn-spin-wheel, #btn-continue{
	color: <?php echo isset($spin_wheel['btn_text_color']) ? $spin_wheel['btn_text_color'] : '#ffffff'; ?>;
	background: <?php echo isset($spin_wheel['button_bg_color']) ? $spin_wheel['button_bg_color'] : '#000'; ?>;
}
.ocmp_spinner .wheel-offers{ color : <?php echo isset($spin_wheel['wheel_font_color']) ? $spin_wheel['wheel_font_color'] : '#ffffff'; ?>;
}
.ocmp-text-noluck span{ color : <?php echo isset($spin_wheel['text_noluck_color']) ? $spin_wheel['text_noluck_color'] : '#ffffff'; ?>;}
	<?php	
		if($spin_wheel['css']){ 
			echo $spin_wheel['css'];
		}
	?>
</style>