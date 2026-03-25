<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="page-header">
<div class="container-fluid">
<div class="pull-right">
	<button type="submit" form="form-barcode" data-toggle="tooltip" title="<?php echo $button_save?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
</div>
<h1><?php echo $heading_title; ?></h1>
<ul class="breadcrumb">
<?php foreach ($breadcrumbs as $breadcrumb) { ?>
<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
<?php } ?>
</ul>
</div>
</div>
<div class="container-fluid">
<?php if ($error_warning) { ?>
<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
<button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php } ?>
<?php if ($success) { ?>
<div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
<button type="button" class="close" data-dismiss="alert">&times;</button>
</div>
<?php } ?>
<div class="panel panel-default">
<div class="panel-heading">
<h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
</div>
<div class="panel-body">
	<div class="well">
        <div class="row">
        	<div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-name"><?php echo $column_product_name; ?></label>
                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $column_product_name; ?>" id="input-name" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-model"><?php echo $column_model; ?></label>
                <input type="text" name="filter_model" value="<?php echo $filter_model; ?>" placeholder="<?php echo $column_model; ?>" id="input-model" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3">
              <div class="form-group">
                <label class="control-label" for="input-quantity"><?php echo $column_quantity; ?></label>
                <input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>" placeholder="<?php echo $column_quantity; ?>" id="input-quantity" class="form-control" />
              </div>
            </div>
            <div class="col-sm-3 text-center">
            	<button type="button" style="margin-top:15%;" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
            </div>

        </div>
    </div>
	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-barcode">
		<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
						
						<td class="text-left"><?php if ($sort == 'image') { ?>
						<a href="<?php echo $sort_image; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_image; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_image; ?>"><?php echo $column_image; ?></a>
						<?php } ?></td>

						<td class="text-left"><?php if ($sort == 'product_name') { ?>
						<a href="<?php echo $sort_product_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_product_name; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_product_name; ?>"><?php echo $column_product_name; ?></a>
						<?php } ?></td>
					
						<td class="text-left"><?php if ($sort == 'model') { ?>
						<a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
						<?php } ?></td>
						
						<td class="text-left"><?php if ($sort == 'product_option') { ?>
						<a href="<?php echo $sort_product_option; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_product_option; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_product_option; ?>"><?php echo $column_product_option; ?></a>
						<?php } ?></td>

						<td class="text-left"><?php echo $column_images; ?></td>	

						<td class="text-left"><?php if ($sort == 'barcode') { ?>
						<a href="<?php echo $sort_barcode; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_barcode; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_barcode; ?>"><?php echo $column_barcode; ?></a>
						<?php } ?></td>					
										
				</tr>
			</thead>
			<tbody>
				<?php if ($products) { ?>
				<?php foreach ($products as $result) { ?>
				<tr>
				 <td class="text-center">
			    	<input type="checkbox" name="selected[]" value="<?php echo $result['product_id']; ?>"  />
				</td>
					<td class="text-center"><?php if ($result['image']) { ?>
						<img src="<?php echo $result['image']; ?>" alt="<?php echo $result['image']; ?>" class="img-thumbnail" />
						<?php } else { ?>
						<span class="img-thumbnail list"><i class="fa fa-camera fa-2x"></i></span>
						<?php } ?></td>
					<td class="text-left"><?php echo $result['name']; ?></td>
					<td class="text-left"><?php echo $result['model']; ?></td>

					<td class="text-left"> <?php if($result['option_data']) { ?>
					  	<?php foreach($result['option_data'] as $option_data ) { ?>
						  <?php if ($option_data['type'] == 'select' || $option_data['type'] == 'radio' || $option_data['type'] == 'checkbox') { ?>
						  	<?php echo $option_data['name'] .'-'. $option_data['type']; ?>
						  	<br>----------------------</br>
						   
						  	<?php if(isset($option_data)) { ?>
							<table> 
								<?php foreach($option_data['product_option_value'] as $option_data_value) { ?>
									<?php if(!empty($option_data_value['name'])) { ?>
													
									<tr><td height="28" width="50%"><?php echo $option_data_value['name'];?></td><td><div class="col-sm-10"><lable class="col-sm-2"></lable><input type="text" name="product[<?php echo $option_data['product_id']; ?>][option][<?php echo $option_data_value['product_option_value_id']?>][quantity]" value="<?php echo $option_data_value['quantity']?>" placeholder="QTY" class="form-control"/></div> </td><td width="20%"><div class="col-sm-12"><lable class="col-sm-2"></lable><input type="text" name="product[<?php echo $option_data['product_id']?>][option][<?php echo $option_data_value['product_option_value_id']?>][upc]" value="<?php echo $option_data_value['upc']; ?>" placeholder="UPC" class="form-control"/></div></td></tr>
							
									<?php } ?>
								<?php } ?>
						  	</table>
						  
							<?php } ?>
						   <br>----------------------</br>
						   <?php } ?>
					  	<?php } ?>
					  
						<?php } else { ?>
						
						
						<div class="col-sm-5"><lable class="col-sm-2"></lable><input type="text" name="product[<?php echo $result['product_id']?>][quantity]" value="<?php echo $result['quantity']?>" placeholder="QTY" class="form-control"/></div> 

						&nbsp &nbsp &nbsp &nbsp

						<div class="col-sm-5"><lable class="col-sm-2"></lable><input type="text" name="product[<?php echo $result['product_id']?>][upc]" value="<?php echo $result['upc']?>" placeholder="UPC" class="form-control"/></div>


					<?php }	 ?></td>

					
					<td class="text-left">
						<?php foreach($result['barcodes'] as $barcode) { ?>
						<?php if(isset($barcode['url'])) { ?>
						<div style="margin-bottom:5px;">
						<img src="<?php echo $barcode['url']; ?>" class="img-responsive"/> <?php echo $barcode['upc']; ?>
						</div>
						<?php } ?>
						<?php } ?>
					</td>
					

					<td class="text-left"><a target="new" href="<?php echo $result['generate']; ?>" data-toggle="tooltip" title="<?php echo $button_generate; ?>" class="btn btn-primary"><?php echo $button_generate; ?></a></td>
					
				</tr>
				<?php } ?> 
				<?php } else { ?>
				<tr>
					<td class="text-center" colspan="7"><?php echo $text_no_results; ?></td>
				</tr>
				<?php } ?>
			</tbody>
		</table>
	</div>
</form>
<div class="row">
	<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
	<div class="col-sm-6 text-right"><?php echo $results; ?></div>
</div>
</div>
</div>
</div>
</div>

<script type="text/javascript"><!--
$('#button-filter').on('click', function() {
	var url = 'index.php?route=possetting/barcode&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}

	var filter_model = $('input[name=\'filter_model\']').val();

	if (filter_model) {
		url += '&filter_model=' + encodeURIComponent(filter_model);
	}
		
	var filter_quantity = $('input[name=\'filter_quantity\']').val();

	if (filter_quantity) {
		url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
	}

	location = url;
});
//--></script>

 <script type="text/javascript"><!--
$('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_name\']').val(item['label']);
	}
});

$('input[name=\'filter_model\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_model=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				response($.map(json, function(item) {
					return {
						label: item['model'],
						value: item['product_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_model\']').val(item['label']);
	}
});
//--></script>

<?php echo $footer; ?>
