<ul class="nav nav-tabs">
    <li <?php if($active_tab == 1) { echo "class='active'";} ?>><a href="<?php echo $general_settings; ?>"><?php echo $text_gs; ?></a></li>
    <li <?php if($active_tab == 2) { echo "class='active'";} ?>><a href="<?php echo $profile_management; ?>"><?php echo $text_pm; ?></a></li>
    <li <?php if($active_tab == 5) { echo "class='active'";} ?>><a href="<?php echo $attribute_mapping; ?>"><?php echo $text_am; ?></a></li>
    <li <?php if($active_tab == 9) { echo "class='active'";} ?>><a href="<?php echo $shop_section; ?>"><?php echo $text_ss; ?></a></li>
    <li <?php if($active_tab == 3) { echo "class='active'";} ?>><a href="<?php echo $shipping_templates; ?>"><?php echo $text_st; ?></a></li>
    <li <?php if($active_tab == 4) { echo "class='active'";} ?>><a href="<?php echo $product_listing; ?>"><?php echo $text_pl; ?></a></li>
    <li <?php if($active_tab == 6) { echo "class='active'";} ?>><a href="<?php echo $order_listing; ?>"><?php echo $text_ol; ?></a></li>
    <li <?php if($active_tab == 7) { echo "class='active'";} ?>><a href="<?php echo $synchronization; ?>"><?php echo $text_sy; ?></a></li>
    <li <?php if($active_tab == 8) { echo "class='active'";} ?>><a href="<?php echo $audit_log; ?>"><?php echo $text_al; ?></a></li>
    <li <?php if($active_tab == 10) { echo "class='active'";} ?>><a href="<?php echo $support; ?>"><?php echo $text_support; ?></a></li>
</ul>