<div class="row">
	<div class="col-md-12 mar">
		<?php foreach ($products as $product) { ?>
		
		<div class="col-md-4 col-sm-4 col-xs-12">
			<div class="box4">
				<div class="off">
					<span>52</span>
				</div>
				<div class="image">
					<a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /></a>
				</div>
				<div class="buttons">
					<a href="#infoModal" data-toggle="modal" data-target="#infoModal" rel="<?php echo $product['product_id']; ?>">
						<i class="fa fa-info-circle" aria-hidden="true"></i><?php echo $text_info; ?>
					</a>
					<a href="#">
						<i class="fa fa-shopping-cart" aria-hidden="true"></i><?php echo $text_cart; ?>
					</a>
				</div>
				<div class="caption">
					<h1><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h1>
					 <?php if ($product['price']) { ?>
                <p class="price">
                  <?php if (!$product['special']) { ?>
                  <?php echo $product['price']; ?>
                  <?php } else { ?>
                  <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
                  <?php } ?>
                  <?php if ($product['tax']) { ?>
                  <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
                  <?php } ?>
                </p>
                <?php } ?>
				</div>
			</div>
		</div>
		<?php } ?>
		
	</div>
</div>
<!-- Modal code start here-->
<div id="infoModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Modal Header</h4>
      </div>
      <div class="modal-body">
        <p>Some text in the modal.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
<!-- Modal code end here-->