<div id="sap-channels-modal" class="modal modal-ocx-sap fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="<?php echo $button_close; ?>"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-uppercase"><?php echo $text_add_channel_permission; ?></h4>
      </div>
      <div class="modal-body">
         <div class="alert alert-help"><?php echo $help_add_channel_permission; ?></div>
         <div class="channels-list">
            <?php if ($channels) { ?>
            <?php foreach($channels as $channel) { ?>
               <a href="<?php echo $channel['href']; ?>" class="channel-item <?php echo $channel['code']; ?>"><span class="name"><?php echo $channel['name']; ?></span></a>
            <?php } ?>
            <?php } ?>
         </div>
      </div>
      <div class="modal-footer">
        <a class="btn-u btn-block" data-dismiss="modal"><?php echo $button_close; ?></a>
      </div>
    </div>
  </div>
</div>
