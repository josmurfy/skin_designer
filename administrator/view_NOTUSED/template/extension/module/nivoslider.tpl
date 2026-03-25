<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-nivoslider" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-nivoslider" class="form-horizontal">
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-banner"><?php echo $entry_banner; ?></label>
            <div class="col-sm-10">
              <select name="banner_id" id="input-banner" class="form-control">
                <?php foreach ($banners as $banner) { ?>
                <?php if ($banner['banner_id'] == $banner_id) { ?>
                <option value="<?php echo $banner['banner_id']; ?>" selected="selected"><?php echo $banner['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $banner['banner_id']; ?>"><?php echo $banner['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-width"><?php echo $entry_width; ?></label>
            <div class="col-sm-10">
              <input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-width" class="form-control" />
              <?php if ($error_width) { ?>
              <div class="text-danger"><?php echo $error_width; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-height"><?php echo $entry_height; ?></label>
            <div class="col-sm-10">
              <input type="text" name="height" value="<?php echo $height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height" class="form-control" />
              <?php if ($error_height) { ?>
              <div class="text-danger"><?php echo $error_height; ?></div>
              <?php } ?>
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-theme"><?php echo $entry_theme; ?></label>
            <div class="col-sm-10">
              <select name="theme" id="input-effect" class="form-control">
                <option value="default" <?php if ($theme=="default") echo "selected"; ?> >default</option>
                <option value="bar" <?php if ($theme=="bar") echo "selected"; ?> >bar</option>
				<option value="dark" <?php if ($theme=="dark") echo "selected"; ?> >dark</option>
				<option value="light" <?php if ($theme=="light") echo "selected"; ?> >light</option>
              </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-effect"><?php echo $entry_effect; ?></label>
            <div class="col-sm-10">
              <select name="effect" id="input-effect" class="form-control">
                <option value="sliceDown" <?php if ($effect=="sliceDown") echo "selected"; ?> >Slice Down</option>
                <option value="sliceDownLeft" <?php if ($effect=="sliceDownLeft") echo "selected"; ?> >Slice Down Left</option>
				<option value="sliceUp" <?php if ($effect=="sliceUp") echo "selected"; ?> >Slice Up</option>
				<option value="sliceUpLeft" <?php if ($effect=="sliceUpLeft") echo "selected"; ?> >Slice Up Left</option>
				<option value="sliceUpDown" <?php if ($effect=="sliceUpDown") echo "selected"; ?> >Slice Down</option>
				<option value="sliceUpDownLeft" <?php if ($effect=="sliceUpDownLeft") echo "selected"; ?> >Slice Down Left</option>
				<option value="fold" <?php if ($effect=="fold") echo "selected"; ?> >Fold</option>
				<option value="fade" <?php if ($effect=="fade") echo "selected"; ?> >Fade</option>
				<option value="random" <?php if ($effect=="random") echo "selected"; ?> >Random</option>
				<option value="slideInRight" <?php if ($effect=="slideInRight") echo "selected"; ?> >Slide In Right</option>
				<option value="slideInLeft" <?php if ($effect=="slideInLeft") echo "selected"; ?> >Slide In Left</option>
				<option value="boxRandom" <?php if ($effect=="boxRandom") echo "selected"; ?> >Random Box</option>
				<option value="boxRain" <?php if ($effect=="boxRain") echo "selected"; ?> >Rain Box</option>
				<option value="boxRainReverse" <?php if ($effect=="boxRainReverse") echo "selected"; ?> >Reverse Rain Box</option>
				<option value="boxRainGrow" <?php if ($effect=="boxRainGrow") echo "selected"; ?> >Grow Rain Box</option>
				<option value="boxRainGrowReverse" <?php if ($effect=="boxRainGrowReverse") echo "selected"; ?> >Reverse Grow Rain Box</option>
              </select>
            </div>
          </div>
		 
		  <div id="slice_section" style="display:none">
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-slices"><?php echo $entry_slices; ?></label>
				<div class="col-sm-10">
					<input type="text" name="slices" value="<?php echo $slices; ?>" placeholder="<?php echo $entry_slices; ?>" id="input-slices" class="form-control" />
				</div>
			  </div>
		  </div>
		  
		  <div id="box_section" style="display:none">
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-boxcols"><?php echo $entry_boxcols; ?></label>
				<div class="col-sm-10">
					<input type="text" name="boxcols" value="<?php echo $boxcols; ?>" placeholder="<?php echo $entry_boxcols; ?>" id="input-boxcols" class="form-control" />
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-boxrows"><?php echo $entry_boxrows; ?></label>
				<div class="col-sm-10">
					<input type="text" name="boxrows" value="<?php echo $boxrows; ?>" placeholder="<?php echo $entry_boxrows; ?>" id="input-boxrows" class="form-control" />
				</div>
			  </div>
		  </div>
		  
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-animspeed"><span data-original-title="<?php echo $help_animspeed; ?>" data-toggle="tooltip" title=""><?php echo $entry_animspeed; ?></span></label>
            <div class="col-sm-10">
				<input type="text" name="animspeed" value="<?php echo $animspeed; ?>" placeholder="<?php echo $entry_animspeed; ?>" id="input-animspeed" class="form-control" />
            </div>
          </div>	
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-pausetime"><span data-original-title="<?php echo $help_pausetime; ?>" data-toggle="tooltip" title=""><?php echo $entry_pausetime; ?></span></label>
            <div class="col-sm-10">
				<input type="text" name="pausetime" value="<?php echo $pausetime; ?>" placeholder="<?php echo $entry_pausetime; ?>" id="input-pausetime" class="form-control" />
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-startslide"><span data-original-title="<?php echo $help_startslide; ?>" data-toggle="tooltip" title=""><?php echo $entry_startslide; ?></span></label>
            <div class="col-sm-10">
				<input type="text" name="startslide" value="<?php echo $startslide; ?>" placeholder="<?php echo $entry_startslide; ?>" id="input-startslide" class="form-control" />
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-directionnav"><span data-original-title="<?php echo $help_directionnav; ?>" data-toggle="tooltip" title=""><?php echo $entry_directionnav; ?></span></label>
            <div class="col-sm-10">
				<select name="directionnav" id="input-directionnav" class="form-control">
                <?php if ($directionnav) { ?>
                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                <option value="0"><?php echo $text_no; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_yes; ?></option>
                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-controlnav"><span data-original-title="<?php echo $help_controlnav; ?>" data-toggle="tooltip" title=""><?php echo $entry_controlnav; ?></span></label>
            <div class="col-sm-10">
				<select name="controlnav" id="input-controlnav" class="form-control">
                <?php if ($controlnav) { ?>
                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                <option value="0"><?php echo $text_no; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_yes; ?></option>
                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-usethumbnails"><span data-original-title="<?php echo $help_usethumbnails; ?>" data-toggle="tooltip" title=""><?php echo $entry_usethumbnails; ?></span></label>
            <div class="col-sm-10">
				<select name="usethumbnails" id="input-usethumbnails" class="form-control">
                <?php if ($usethumbnails) { ?>
                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                <option value="0"><?php echo $text_no; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_yes; ?></option>
                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-pauseonhover"><span data-original-title="<?php echo $help_pauseonhover; ?>" data-toggle="tooltip" title=""><?php echo $entry_pauseonhover; ?></span></label>
            <div class="col-sm-10">
				<select name="pauseonhover" id="input-pauseonhover" class="form-control">
                <?php if ($pauseonhover) { ?>
                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                <option value="0"><?php echo $text_no; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_yes; ?></option>
                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-forcemanualtrans"><?php echo $entry_forcemanualtrans; ?></label>
            <div class="col-sm-10">
				<select name="forcemanualtrans" id="input-forcemanualtrans" class="form-control">
                <?php if ($forcemanualtrans) { ?>
                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                <option value="0"><?php echo $text_no; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_yes; ?></option>
                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-prevtext"><?php echo $entry_prevtext; ?></label>
            <div class="col-sm-10">
				<input type="text" name="prevtext" value="<?php echo $prevtext; ?>" placeholder="<?php echo $entry_prevtext; ?>" id="input-prevtext" class="form-control" />
            </div>
          </div>
		  <div class="form-group">
            <label class="col-sm-2 control-label" for="input-nexttext"><?php echo $entry_nexttext; ?></label>
            <div class="col-sm-10">
				<input type="text" name="nexttext" value="<?php echo $nexttext; ?>" placeholder="<?php echo $entry_nexttext; ?>" id="input-nexttext" class="form-control" />
            </div>
          </div>
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
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
$('#input-effect').on('change', function() {	
	switch (this.value)
	{
		case "sliceDown": 
		case "sliceDownLeft":
		case "sliceUp":
		case "sliceUpLeft":
		case "sliceUpDown":
		case "sliceUpDownLeft":	   
			$('#slice_section').show();
			$('#box_section').hide();
			break;
		case "boxRandom": 
		case "boxRain":
		case "boxRainReverse":
		case "boxRainGrow":
		case "boxRainGrowReverse":
			$('#slice_section').hide();
			$('#box_section').show();
			break;
	   default: 
			$('#slice_section').hide();
			$('#box_section').hide();
			break;
	}
});
</script>
<?php echo $footer; ?>