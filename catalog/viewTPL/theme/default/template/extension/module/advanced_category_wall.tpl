<?php /* === Advanced Category Wall Module v1.1.0 for OpenCart by vytasmk at gmail === */ ?>

<?php if (!empty($heading_title)): ?>
	<h3><?=$heading_title?></h3> 
	<div class="row a_category <?='columns-'.$columns?>">
<?php else: ?>
	<div class="row a_category <?='columns-'.$columns?>" style='margin-top:30px'>
<?php endif ?>

<?php 
	switch ($columns) {
		case 1:
			$class = "col-xs-12";
			break;
		case 2:
			$class = "col-md-6 col-sm-12";
			break;
		case 3:
			$class = "col-md-4 col-sm-6 col-xs-12";
			break;
		case 6:
			$class = "col-lg-2 col-md-3 col-sm-4 col-xs-6";
			break;
		case 4:
		default:
			$class = "col-lg-3 col-md-4 col-sm-6 col-xs-12";
	}
?>
	<?php foreach ($categories as $category): ?>
		<div class="<?=$class?>">
			<div class="product-thumb transition">
				<?php if($show_catname): ?>
				<div class="cat_name_box text-center">
					<h4><a href="<?php echo $category['href']; ?>"><div><?=$category['name']?></div></a></h4><br>
				</div>
				<?php endif ?>
				<div class="image">
					<a href="<?php echo $category['href']; ?>">
						<img src="<?php echo $category['thumb']; ?>" alt="<?php echo $category['name']; ?>" title="<?php echo $category['name']; ?>" class="img-responsive" />
					</a>
				</div>
			</div>
	</div>
	<?php endforeach; ?>
</div>
