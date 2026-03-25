<div class="modal-content">
	<div class="modal-header">
	
		<div class="noncate">Non-catalog</div>
		<div class="addnewnoncate">Add Non-catalog Products</div>
		<button type="button" class="close posclose" data-dismiss="modal">&times;</button></div>
		
	<div class="modal-body">		
		<?php if ($error_warning) { ?>
				<div class="alert alert-danger"><?php echo $error_warning; ?>
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				</div>
			  <?php } ?>  
		
		
			<div class="form-horizontal add-posproduct row">	
			  
				<div class="form-group required">
					<label class="col-sm-4 control-label" for="input-name"><?php echo $entry_name; ?></label>
					<div class="col-sm-8">
					  <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
					 
					</div>
			    </div>
				
			    <div class="form-group required">
				<label class="col-sm-4 control-label" for="input-model"><?php echo $entry_model; ?></label>
				<div class="col-sm-8">
				  <input type="text" name="model" value="<?php echo $model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
				 </div>
			    </div>
				
			    <div class="form-group hide">
                <label class="col-sm-4 control-label" for="input-top"></label>
                <div class="col-sm-8">
                  <div class="checkbox">
                    <label>
                      <?php if ($rshipping) { ?>
                      <input type="checkbox" name="rshipping" value="1" checked="checked" id="input-top" />
                      <?php } else { ?>
                      <input type="checkbox" name="rshipping" value="1" id="input-top" />
                      <?php } ?>
                      &nbsp; <?php echo $entry_reqshipping; ?></label>
                  </div>
                </div>
              </div>
			  
			    <div class="form-group required">
				<label class="col-sm-4 control-label" for="input-price"><?php echo $entry_price; ?></label>
				<div class="col-sm-8">
				  <input type="text" name="price" value="<?php echo $price; ?>" placeholder="<?php echo $entry_price; ?>" id="input-price" class="form-control" />
				 </div>
			    </div>
			  
			    <div class="form-group required">
				<label class="col-sm-4 control-label" for="input-quantity"><?php echo $entry_quantity; ?></label>
				<div class="col-sm-8">
				  <input type="text" name="quantity" value="<?php echo $quantity; ?>" placeholder="<?php echo $entry_quantity; ?>" id="input-quantity" class="form-control" />
				 </div>
			    </div>	
			    <div class="text-right col-sm-12">
			    <button type="button" class="btn btn-primary addposproduct"><?php echo $button_addproduct; ?></button>
		
		</div>	  			  
		</div>
	</div>
</div>
