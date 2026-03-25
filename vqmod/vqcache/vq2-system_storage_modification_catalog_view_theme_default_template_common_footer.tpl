
        <?php $registry = Vie_Front::$instance->registry;; $vie_module_groups = $registry->get('vie_module_groups'); ?>

        <?php if (!empty($vie_module_groups['vie_c_b_o'])) { ?>
          <div class="container">

        <?php if (!empty($vie_module_groups['vie_fct'])) { ?>
        <?php echo implode('', $vie_module_groups['vie_fct']); ?>
        <?php } ?>      
      
            <div class="row">
              <div id="content-bottom-outer" class="col-sm-12">
                <?php echo implode('', $vie_module_groups['vie_c_b_o']); ?>
              </div>
            </div>
          </div>
        <?php } ?>

        <?php if (!empty($vie_module_groups['vie_ft'])) { ?>
          <div id="footer-top">
            <?php echo implode('', $vie_module_groups['vie_ft']); ?>
          </div>
        <?php } ?>      
      
<footer>
  <div class="container">

        <?php if (!empty($vie_module_groups['vie_fct'])) { ?>
        <?php echo implode('', $vie_module_groups['vie_fct']); ?>
        <?php } ?>      
      
    <div class="row">
      <?php if ($informations) { ?>
      <div class="col-sm-3">
        <h5><?php echo $text_information; ?></h5>
        <ul class="list-unstyled">
          <?php foreach ($informations as $information) { ?>
          <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>
      <div class="col-sm-3">
        <h5><?php echo $text_service; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
          <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
          <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
        </ul>
      </div>
      <div class="col-sm-3">
        <h5><?php echo $text_extra; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
          <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
          <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
          <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
        </ul>
      </div>
      <div class="col-sm-3">
        <h5><?php echo $text_account; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
          <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
          <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
          <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
        </ul>
      </div>
    </div>
    <hr>
    

        <?php if (!empty($vie_module_groups['vie_fcb'])) { ?>
        <?php echo implode('', $vie_module_groups['vie_fcb']); ?>
        <?php } ?>      
      
  </div>

      <div id="bottom">
        <?php $vie_front = Vie_Front::$instance; ?>
        <?php if ($vie_front->getSkinOption('footer_show_copyright')) { ?>
        <div class="container">
          <div class="row">
            <?php echo $vie_front->translate($vie_front->getSkinOption('footer_copyright_content')); ?>
          </div>
        </div>
        <?php } else { ?>
        <div class="container"><?php echo $powered; ?></div>
        <?php } ?>
      </div>
      
</footer>

        <?php if (!empty($vie_module_groups['vie_fb'])) { ?>
        <?php echo implode('', $vie_module_groups['vie_fb']); ?>
        <?php } ?>      
      

<!--
OpenCart is open source software and you are free to remove the powered by OpenCart if you want, but its generally accepted practise to make a small donation.
Please donate via PayPal to donate@opencart.com
//-->

<!-- Theme created by Welford Media for OpenCart 2.0 www.welfordmedia.co.uk -->
<style type="text/css"> .wrapsoldout { position:relative; clear:none; overflow:hidden; } .wrapsoldout img { position:relative; z-index:1; max-width: 100%; } .wrapsoldout .soldout { display:block; position:absolute; width:100%; top:0; left:0; z-index:2; text-align:center; margin:0px; } </style>

        <?php $vie_front = Vie_Front::$instance; ?>

        <?php echo $vie_front->renderResources('body_end_scripts'); ?>
        <?php if ($vie_front->getSkinOption('enable_custom_javascript') && $vie_front->getSkinOption('custom_javascript')) { ?>
        <script><?php echo $vie_front->getSkinOption('custom_javascript'); ?></script>
        <?php } ?>
      

        </div>
      
<style type="text/css">
.wrapsoldout {
  position:relative;
  clear:none;
  overflow:hidden;
}
.wrapsoldout img {
  position:relative;
  z-index:1;
  max-width: 100%;
}
.wrapsoldout .soldout {
  display:block;
  position:absolute;
  width:100%;
  top:0;
  left:0;
  z-index:2;
  text-align:center;
  margin:0px;
}
</style>
</body></html>