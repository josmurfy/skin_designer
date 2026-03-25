<h3><?php echo $heading_title; ?></h3>
<div class="row">
  <?php foreach ($products as $product) { ?>
  <div class="product-layout col-lg-3 col-md-3 col-sm-6 col-xs-12">
    <div class="product-thumb transition">
      <div class="image"><a href="<?php echo $product['href']; ?>"><?php if ($product['quantity'] <= 0) { ?> <div class="wrapsoldout"> <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /> <span class="soldout"> <img src="<?php echo $product['soldout']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /> </span> </div> <?php } else { ?> <img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-responsive" /> <?php } ?></a></div>
      <div class="caption">
        <h4><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a></h4>
        <p><?php echo $product['description']; ?></p>
        <?php if ($product['rating']) { ?>
        <div class="rating">
          <?php for ($i = 1; $i <= 5; $i++) { ?>
          <?php if ($product['rating'] < $i) { ?>
          <span class="fa fa-stack"><i class="fa fa-star-o fa-stack-2x"></i></span>
          <?php } else { ?>
          <span class="fa fa-stack"><i class="fa fa-star fa-stack-2x"></i><i class="fa fa-star-o fa-stack-2x"></i></span>
          <?php } ?>
          <?php } ?>
        </div>
        <?php } ?>
        <?php if ($product['price']) { ?>
        <p class="price">
          <?php if (!$product['special']) { ?>
          <?php echo $product['price']; ?>
		  <?php }elseif ($product['special']>=$product['price']) { ?>
          <?php echo $product['special']; ?>
          <?php } else { ?>
          <span class="price-new"><?php echo $product['special']; ?></span> <span class="price-old"><?php echo $product['price']; ?></span>
          <?php } ?>
          <?php if ($product['tax']) { ?>
          <span class="price-tax"><?php echo $text_tax; ?> <?php echo $product['tax']; ?></span>
          <?php } ?>
        </p>
        <?php } ?>
      </div>
      <div class="button-group">
		<?php if ($product['quantity'] <= 0) { ?> <button type="button" style="cursor: default !important; background: gray !important; color: lightgray !important;"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $product['stock_status']; ?></span></button> <?php } else { ?> <button type="button" onclick="cart.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-shopping-cart"></i> <span class="hidden-xs hidden-sm hidden-md"><?php echo $button_cart; ?></span></button> <?php } ?>
        <button type="button" data-bs-toggle="tooltip" title="<?php echo $button_wishlist; ?>" onclick="wishlist.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-heart"></i></button>
        <button type="button" data-bs-toggle="tooltip" title="<?php echo $button_compare; ?>" onclick="compare.add('<?php echo $product['product_id']; ?>');"><i class="fa fa-exchange"></i></button>
      </div>
    </div>
  </div>
  <?php } ?>
</div>
