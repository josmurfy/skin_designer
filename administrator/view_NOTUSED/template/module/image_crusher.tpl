<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-image_crusher" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-image_crusher" class="form-horizontal">
          
            <!--START OF IMAGE CRUSHER-->
            <p><?php echo $image_crusher_intro; ?></p>
            
            <!--start of new image Crusher Block-->
            <div class="section-wrap">
                <h1><?php echo $image_crusher_title; ?></h1>
                <p><?php echo $image_crusher_image_manager_text; ?></p>
             

                <p><?php echo $image_crusher_excluded_filetypes; ?></p>

                <h4 class="title"><?php echo $image_crusher_switch_title; ?></h4>
                <div><?php echo $image_crusher_switch_text; ?></div>
                <div>        
                    <input type = "radio"
                           name = "image_crusher_image_optimise_on"
                           id = "image_crusher_image_optimise_on"
                           value = "on"
                           <?php echo $image_crusher_switch_setting_on; ?>
                           onclick = "deselectOff()"
                           />
                    <label for = "image_crusher_image_optimise_on"><?php echo $image_crusher_switch_on; ?></label>
                    
                    <input type = "radio"
                           name = "image-optimise-off"
                           id = "image-optimise-off"
                           value = "off"
                           <?php echo $image_crusher_switch_setting_off; ?>
                           onclick = "deselectOn()" 
                           />
                    <label for = "image-optimise-off"><?php echo $image_crusher_switch_off; ?></label>         
                </div>

                <div>
                <h4 class="title"><?php echo $image_crusher_compression_level_title; ?></h4>
                <div><?php echo $image_crusher_compression_level_text; ?></div>
                  <input type="range" id="image_crusher_compression_level" class="image_crusher_compression_level" name="image_crusher_compression_level" min="1" max="10" value="<?php echo $image_crusher_default_compression_level; ?>" step="1" onchange="showValue(this.value, 'new-image-range')">
                </div>

                <div class="level">
                <?php echo $image_crusher_compression_level_label; ?> <span id="new-image-range"><?php echo $image_crusher_default_compression_level; ?></span>
                </div>
            </div>
            <!--End of new image Crusher Block-->

            <!--Existing Image Crusher Block-->
            <div class="section-wrap">
                <h1><?php echo $image_crusher_existing_images_title; ?></h1>
                <p><?php echo $image_crusher_existing_images_text; ?></p>

                <div class="warning">
                  <p><strong><?php echo $image_crusher_existing_images_warning_title; ?></strong></p>
                  <p><?php echo $image_crusher_existing_images_warning_text; ?></p>
                </div>
               
                <!--Level slider for existing image crusher-->
                <div>
                <h4 class="title"><?php echo $image_crusher_compression_level_title; ?></h4>
                <div><?php echo $image_crusher_compression_level_text; ?></div>
                  <input type="range" class="image_crusher_compression_level" id="image_crusher_existing_image_compression_level" name="image_crusher_existing_image_compression_level" min="1" max="10" value="<?php echo $image_crusher_existing_image_compression_level; ?>" step="1" onchange="showValue(this.value, 'existing-image-range')">
                </div> 

                <div class="level">
                <?php echo $image_crusher_compression_level_label; ?> <span id="existing-image-range"><?php echo $image_crusher_existing_image_compression_level; ?></span>
                </div>

                <!--Form-->
                <div class="existing-images-input-box">
                  <p><?php echo $image_crusher_existing_images_image_folder_text; ?> </p>
                  <input type="text" name="image_crusher_existing_images_folder" id="image_crusher_existing_images_folder" placeholder="<?php echo $image_crusher_existing_images_image_folder_placeholder_text; ?> " value="">
                </div>
                <div id="existing-images-submit"><?php echo $image_crusher_existing_images_submit_button; ?></div>

                <div id="image-crush-results"></div>

                <div id="existing-images-dialog-confirm" title="Crush Existing Images?">
                  <p><?php echo $image_crusher_existing_images_popup_text_1; ?></p>
                  <p><?php echo $image_crusher_existing_images_popup_text_2; ?></p>
                </div>
            </div>
          
            

            <script type="text/javascript">

                //post the form values to the crusher script when a user presses submit.       
                $("#existing-images-submit").click(function() {

                    //get the value of the form for the image folder
                    var imageFolder = $("input[name=image_crusher_existing_images_folder]").val();

                    if (imageFolder === '') {
                        $( "#image-crush-results" ).empty().append('<span class="error">Please enter a folder</span>');
                        return;
                    }

                    //show a popup to the user for one last check
                    $("#existing-images-dialog-confirm").dialog({
                      resizable: false,
                      height:200,
                      modal: true,
                      buttons: {
                        "Crush My Images": function() {
                          $( this ).dialog( "close" );
                        
                              //get the compression level.
                              var existingImageSlider = $("#image_crusher_existing_image_compression_level").val();
                            
                              //post the value to the image crusher script and then display results in #image-crush-results.
                              $.ajax({ url: 'index.php?route=module/image_crusher/imageCrushreceive&token=<?php echo $user_token; ?>', 
                                  data: {
                                    function: "compressImages", 
                                    imageFolder: imageFolder, 
                                    existingImageSlider: existingImageSlider
                                  },
                                  type: 'post',
                                  beforeSend: function() {
                                    $('#image-crush-results').html('<img src="../image/catalog/processing.gif" />');
                                  },
                                  success: function(output) {
                                      var content = output;
                                      $( "#image-crush-results" ).empty().append( content );
                                  },
                                  error: function(output) {
                                    $( "#image-crush-results" ).empty().append('<span class="error">The specified image folder does not exist!</span>');
                                  }
                              });
                        },
                        Cancel: function() {
                          $( this ).dialog( "close" );
                        }
                      }
                    });
                });

            </script>

            <!--END OF IMAGE CRUSHER-->

        </form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>