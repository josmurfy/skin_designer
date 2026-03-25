<div class="slider-wrapper theme-<?php echo $setting['theme'];?>"> 
<div id="slider<?php echo $module; ?>" class="nivoSlider">
  <?php foreach ($banners as $banner) { ?>
    <?php if ($banner['link']) { ?>
    <a href="<?php echo $banner['link']; ?>"><img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" /></a>
    <?php } else { ?>
    <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['title']; ?>" />
    <?php } ?>
  <?php } ?>
</div>
</div>
<script type="text/javascript"><!--
$('#slider<?php echo $module; ?>').nivoSlider({
	effect: '<?php echo $setting['effect']; ?>', 
    <?php if ( $setting['slices']) echo "slices:" . $setting['slices'] . "," ; ?>
    <?php if ( $setting['boxcols']) echo "boxCols:" . $setting['boxcols'] . "," ; ?>
    <?php if ( $setting['boxrows']) echo "boxRows:" . $setting['boxrows'] . "," ; ?>
    <?php if ( $setting['animspeed']) echo "animSpeed:" . $setting['animspeed'] . "," ; ?>
    <?php if ( $setting['pausetime']) echo "pauseTime:" . $setting['pausetime'] . "," ; ?>
    <?php if ( $setting['startslide'])
			if ( $setting['startslide']=="-1")
				echo "randomStart:true,"; 
			else
				echo "";
		  else
				echo "startSlide:" . $setting['startslide'] . ","; 				
	?>
    <?php if ( $setting['directionnav']) echo "directionNav:true," ; ?>
    <?php if ( $setting['controlnav']) echo "controlNav:true," ; ?>
    <?php if ( $setting['usethumbnails']) echo "controlNavThumbs:true," ; ?>
    <?php if ( $setting['pauseonhover']) echo "pauseOnHover:true," ; ?>
    <?php if ( $setting['forcemanualtrans']) echo "manualAdvance:true," ; ?>
    prevText: '<?php echo $setting['prevtext']; ?>',
    nextText: '<?php echo $setting['nexttext']; ?>'
});
--></script>