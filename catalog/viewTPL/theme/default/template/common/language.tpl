<?php if (count($languages) > 1) { ?>
<div class="pull-left">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-language">
    <ul class="list-inline">
      <?php foreach ($languages as $language) { ?>
	  <?php if ($language['code'] != $code){?>
      <li class="<?php if ($language['code'] == $code) { echo 'current-lang lang-'.$code ;  } ?>"><button class="btn btn-link btn-block language-select" type="button" name="<?php echo $language['code']; ?>"><?php echo $language['name']; ?></button></li>

	  <?php }} ?>
    </ul>
     
  <input type="hidden" name="code" value="" />
  <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
</form>
</div>
<?php } ?>
