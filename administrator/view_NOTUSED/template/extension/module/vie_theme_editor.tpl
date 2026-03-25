<?php echo $header; ?>
<div id="content" ng-controller="ThemeEditorCtrl" class="clearfix">
  <form target="preview" form-controls action="{{previewUrl | trusted}}" method="post" id="form-controls">
    <input type="hidden" name="vie_preview_data" id="preview-data" value="{{previewData}}" />
    <div id="vie-cp-container">
      <div id="vie-cp-panel-controls">
        <div class="vie-cp-header">
          <h4 class="vie-cp-header-logo"><a href="<?php echo $module_url; ?>"><?php echo Vie::_('heading_vie_module'); ?> v<?php echo $version; ?></a></h4>
          <a href="<?php echo $modules_url; ?>" class="vie-cp-cancel" tooltip="<?php echo Vie::_('button_cancel', 'Cancel'); ?>"><i class="fa fa-close"></i></a>
        </div>
        <div class="vie-cp-content">
          <div class="form-group">
            <label><?php echo Vie::_('text_store', 'Store'); ?></label>
            <select class="form-control vie-cp-store" ng-model="store_id" ng-change="changeStore(store_id)" tooltip="<?php echo Vie::_('text_select_store', 'Select Store'); ?>">
              <?php foreach ($stores as $store_id => $store_name) { ?>
              <option value="<?php echo $store_id; ?>"><?php echo $store_name; ?></option>
              <?php } ?>
            </select>
          </div>

          <fieldset id="fieldset-theme">
            <?php if (count($skins) > 1) { ?>
            <div class="form-group">
              <label><?php echo Vie::_('text_skin', 'Skin'); ?></label>
              <select class="form-control" ng-model="skin_id" ng-change="loadSkin()">
                <?php foreach ($skins as $key_skin_id => $skin_name) { ?>
                <option value="<?php echo $key_skin_id; ?>"><?php echo $skin_name; ?></option>
                <?php } ?>
              </select>
            </div>
            <?php } ?>

            <fieldset>
              <tabset>
                <?php if (!empty($option_section['store'])) { ?>
                <tab heading="<?php echo Vie::_('text_store', 'Store'); ?>">
                  <accordion>
                    <?php foreach ($option_section['store'] as $option) { ?>
                    <?php echo VieAdminView::control($option); ?>
                    <?php } ?>
                  </accordion>
                </tab>
                <?php } ?>
                <?php if (!empty($option_section['style'])) { ?>
                <tab heading="<?php echo Vie::_('text_style_and_colors', 'Style & Colors'); ?>">
                  <accordion>
                    <?php foreach ($option_section['style'] as $option) { ?>
                    <?php echo VieAdminView::control($option); ?>
                    <?php } ?>
                  </accordion>
                </tab>
                <?php } ?>
                <?php if (!empty($option_section['backgrounds'])) { ?>
                <tab heading="<?php echo Vie::_('text_backgrounds', 'Backgrounds'); ?>">
                  <accordion>
                    <?php foreach ($option_section['backgrounds'] as $option) { ?>
                    <?php echo VieAdminView::control($option); ?>
                    <?php } ?>
                  </accordion>            
                </tab>
                <?php } ?>
                <?php if (!empty($option_section['fonts'])) { ?>
                <tab heading="<?php echo Vie::_('text_fonts', 'Fonts'); ?>">
                  <accordion>
                    <?php foreach ($option_section['fonts'] as $option) { ?>
                    <?php echo VieAdminView::control($option); ?>
                    <?php } ?>
                  </accordion>
                </tab>
                <?php } ?>
                <?php if (!empty($option_section['custom_code'])) { ?>
                <tab heading="<?php echo Vie::_('text_custom_code', 'Custom Code'); ?>">
                  <accordion>
                    <?php foreach ($option_section['custom_code'] as $option) { ?>
                    <?php echo VieAdminView::control($option); ?>
                    <?php } ?>
                  </accordion>
                </tab>
                <?php } ?>
              </tabset>
            </fieldset>
          </fieldset>
        </div>
        <div class="vie-cp-footer">
          <a href="http://www.viethemes.com/contact" target="_blank" class="btn btn-default pull-left"><?php echo Vie::_('text_support', 'Support'); ?></a>
          <div class="btn-group" role="group" aria-label="...">
            <button class="btn btn-default" ng-file-select ng-file-change="import($files, $event)" type="button"><?php echo Vie::_('button_import', 'Import'); ?></button>
            <button class="btn btn-default" ng-click="export()" type="button"><?php echo Vie::_('button_export', 'Export'); ?></button>
          </div>
          <button class="btn btn-primary vie-cp-btn-save" ng-click="save()" type="button"><?php echo Vie::_('button_save', 'Save'); ?></button>
        </div>
        <div class="vie-loader" ng-if="loading"><i class="fa fa-circle-o-notch fa-spin"></i></div>
      </div>
      <div id="vie-cp-panel-preview">
        <iframe name="preview" id="preview" vie-iframe-preview frameborder="0" width="100%" height="100%"></iframe>
      </div>
    </div>
  </form>
  <iframe ng-src="{{download_url}}" id="download-frame"></iframe>
</div>
<script>
  var Vie = {
    front_base: <?php echo json_encode($front_base) ?>,
    languages: <?php echo json_encode($languages) ?>,
    store_id: <?php echo json_encode($selected_store_id); ?>,
    skin_id: <?php echo json_encode($skin_id) ?>,
    skin_url: <?php echo json_encode($skin_url) ?>,
    save_url: <?php echo json_encode($save_url) ?>,
    preview_url: <?php echo json_encode($preview_url); ?>,
    export_url: <?php echo json_encode($export_url) ?>,
    import_url: <?php echo json_encode($import_url); ?>,
    fonts: <?php echo json_encode($fonts) ?>
  };
</script>
<?php echo $footer; ?>
