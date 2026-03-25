<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-information" id="btnSubmit" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary">
          <i class="fa fa-save">
          </i>
        </button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default">
          <i class="fa fa-reply">
          </i>
        </a>
      </div>
      <h1>
        <?php echo $heading_title; ?>
      </h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li>
          <a href="<?php echo $breadcrumb['href']; ?>">
            <?php echo $breadcrumb['text']; ?>
          </a>
        </li>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_form; ?></h3>
      </div>
    <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-information" class="form-horizontal">
			<div class="form-group">
                <label class="col-sm-2 control-label" for="input-package-title">
                  <?php echo $entry_product;?>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="product" value="<?php echo $product; ?>"    placeholder="<?php echo $entry_product;?>" id="input-package-titl" class="form-control"/>
					<input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-2 control-label" for="input-package-title">
                  <?php echo $entry_store;?>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="store" value="<?php echo $store; ?>"    placeholder="<?php echo $entry_store;?>" id="input-package-titl" class="form-control"/>
					<input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
                </div>
            </div>
			<div class="form-group required">
                <label class="col-sm-2 control-label" for="input-package-title">
                  <?php echo $entry_quantity;?>
                </label>
                <div class="col-sm-10">
                    <input type="text" name="quantity" value="<?php echo $quantity; ?>"    placeholder="<?php echo $entry_quantity;?>" id="input-package-titl" class="form-control"/>
                </div>
            </div>
			
		</form>
    </div>
    </div>
  </div>
  <script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
  <link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
  <script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>  
  <script type="text/javascript"><!--
$('#language a:first').tab('show');
//--></script>
 
<script type="text/javascript">
$('input[name=\'store\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=possetting/store/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				json.unshift({
					store_id: 0,
					name:'<?php echo $text_none; ?>'
				});

				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['store_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'store\']').val(item['label']);
		$('input[name=\'store_id\']').val(item['value']);
	}
});
</script>	

<script type="text/javascript">
$('input[name=\'product\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				json.unshift({
					product_id: 0,
					name:'<?php echo $text_none; ?>'
				});

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
		$('input[name=\'product\']').val(item['label']);
		$('input[name=\'product_id\']').val(item['value']);
	}
});
</script>	
	
<?php echo $footer; ?></div>