<div id="content" class="col-sm-12 bg">
 <div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	</div>
	<div class="row">
		<div class="col-sm-6">
		 <?php if ($thumb) { ?>
		  <div class="thumbnails">
			<?php if ($thumb) { ?>
			<img src="<?php echo $thumb; ?>" title="<?php echo $heading_title; ?>" alt="<?php echo $heading_title; ?>" class="img-responsive" />
			<?php } ?>
			</div>
		<?php } ?>
		</div>
		
		<div class="col-sm-6">
		<h1><a href=""><?php echo $name;?></a></h1>
		<div class="col-sm-12">
			 <?php if ($price) { ?>
	  <ul class="list-unstyled">
		<?php if (!$special) { ?>
		<li>
		  <h2><?php echo $price; ?></h2>
		  
		</li>
		<?php } else { ?>
		<li><span style="text-decoration: line-through;"><?php echo $price; ?></span></li>
		<li>
		  <h2><?php echo $special; ?></h2>
		</li>
		<?php } ?>
	   
	  </ul>
	  <?php } ?>
	  
		</div>
		<p><?php echo $description; ?></p>
		<div id="product">
			<?php if ($options) { ?>
		<hr>
		<h3><?php echo $text_option; ?></h3>
		<?php foreach ($options as $option) { ?>
		<?php if ($option['type'] == 'select') { ?>
		<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
		  <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
		  <select name="option[<?php echo $option['product_option_id']; ?>]" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control">
			<option value=""><?php echo $text_select; ?></option>
			<?php foreach ($option['product_option_value'] as $option_value) { ?>
			<option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
			<?php if ($option_value['price']) { ?>
			(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
			<?php } ?>
			</option>
			<?php } ?>
		  </select>
		</div>
		<?php } ?>
		<?php if ($option['type'] == 'radio') { ?>
		<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
		  <label class="control-label"><?php echo $option['name']; ?></label>
		  <div id="input-option<?php echo $option['product_option_id']; ?>">
			<?php foreach ($option['product_option_value'] as $option_value) { ?>
			<div class="radio">
			  <label>
				<input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" />
				<?php if ($option_value['image']) { ?>
				<img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> 
				<?php } ?>                    
				<?php echo $option_value['name']; ?>
				<?php if ($option_value['price']) { ?>
				(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
				<?php } ?>
			  </label>
			</div>
			<?php } ?>
		  </div>
		</div>
		<?php } ?>
		<?php if ($option['type'] == 'checkbox') { ?>
		<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
		  <label class="control-label"><?php echo $option['name']; ?></label>
		  <div id="input-option<?php echo $option['product_option_id']; ?>">
			<?php foreach ($option['product_option_value'] as $option_value) { ?>
			<div class="checkbox">
			  <label>
				<input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" />
				<?php if ($option_value['image']) { ?>
				<img src="<?php echo $option_value['image']; ?>" alt="<?php echo $option_value['name'] . ($option_value['price'] ? ' ' . $option_value['price_prefix'] . $option_value['price'] : ''); ?>" class="img-thumbnail" /> 
				<?php } ?>
				<?php echo $option_value['name']; ?>
				<?php if ($option_value['price']) { ?>
				(<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
				<?php } ?>
			  </label>
			</div>
			<?php } ?>
		  </div>
		</div>
		<?php } ?>
		<?php if ($option['type'] == 'text') { ?>
		<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
		  <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
		  <input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
		</div>
		<?php } ?>
		<?php if ($option['type'] == 'textarea') { ?>
		<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
		  <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
		  <textarea name="option[<?php echo $option['product_option_id']; ?>]" rows="5" placeholder="<?php echo $option['name']; ?>" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control"><?php echo $option['value']; ?></textarea>
		</div>
		<?php } ?>
		<?php if ($option['type'] == 'date') { ?>
		<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
		  <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
		  <div class="input-group date">
			<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
			<span class="input-group-btn">
			<button class="btn btn-default" type="button"><i class="fa fa-calendar"></i></button>
			</span></div>
		</div>
		<?php } ?>
		<?php if ($option['type'] == 'datetime') { ?>
		<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
		  <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
		  <div class="input-group datetime">
			<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="YYYY-MM-DD HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
			<span class="input-group-btn">
			<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
			</span></div>
		</div>
		<?php } ?>
		<?php if ($option['type'] == 'time') { ?>
		<div class="form-group<?php echo ($option['required'] ? ' required' : ''); ?>">
		  <label class="control-label" for="input-option<?php echo $option['product_option_id']; ?>"><?php echo $option['name']; ?></label>
		  <div class="input-group time">
			<input type="text" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option['value']; ?>" data-date-format="HH:mm" id="input-option<?php echo $option['product_option_id']; ?>" class="form-control" />
			<span class="input-group-btn">
			<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
			</span></div>
		</div>
		<?php } ?>
		<?php } ?>
		<?php } ?>		
		<div class="form-group">
			  <label class="control-label" for="input-quantity"><?php echo $entry_qty; ?></label>
			   <input type="text" name="quantity" value="<?php echo $minimum; ?>" size="2" id="input-quantity" class="form-control" />
				<input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
				<br/>
			<button type="button" id="button-cart" data-toggle="tooltip" title="Add to Cart" class="btn btn-primary btn-lg addtocart"><i aria-hidden="true" class="fa fa-shopping-basket"></i> <?php echo $button_cart; ?> </button>
		</div>
	 </div>
	</div>
 </div>
</div>