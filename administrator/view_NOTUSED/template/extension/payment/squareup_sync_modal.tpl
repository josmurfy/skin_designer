<p><?php echo $text_sync_configure_intro; ?></p>
<hr />
<div class="form-control-static">
    <input type="radio" name="squareup_initial_sync_type" value="1" /> <?php echo $text_sync_configure_option_1; ?>
</div>
<div class="form-control-static">
    <input type="radio" name="squareup_initial_sync_type" value="2"  <?php if($selected == '2') { ?> checked <?php } ?> /> <?php echo $text_sync_configure_option_2; ?>
</div>
<div class="form-control-static">
    <input type="radio" name="squareup_initial_sync_type" value="3" /> <?php echo $text_sync_configure_option_3; ?>
</div>
<div class="form-control-static">
    <input type="radio" name="squareup_initial_sync_type" value="4" <?php if($selected == '4') { ?> checked <?php } ?> /> <?php echo $text_sync_configure_option_4; ?>
</div>