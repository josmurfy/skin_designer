<ul class="list-inline categories">
	<?php foreach($categories as $category){?>
	<li class="col-md-4 col-sm-4 col-xs-12 showsubcate" rel="<?php echo $category['category_id']?>" path="<?php echo $category['path']?>">
	<i class="fa fa-folder-open" aria-hidden="true"></i>
	<br><?php echo $category['name']?>		
	</li>
	<?php } ?>	
</ul>
<div class="row">
	<div class="col-md-12 products"></div>
</div>




