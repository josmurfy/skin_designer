<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" data-toggle="tooltip" title="<?php echo $button_filter; ?>" onclick="$('#filter-rule').toggleClass('hidden-sm hidden-xs');" class="btn btn-default hidden-md hidden-lg"><i class="fa fa-filter"></i></button>
        <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" form="form-product" formaction="<?php echo $delete; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-product').submit() : false;"><i class="fa fa-trash-o"></i></button>
      </div>
      <h1><?php echo $heading_title; ?></h1>
      <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
      </ul>
    </div>
  </div>
  <div class="container-fluid"><?php if ($error_warning) { ?>
    <div class="alert alert-danger alert-dismissible"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success alert-dismissible"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
  <?php } ?>
    <div class="row">
      <div id="filter-product-data" class="col-md-3 col-md-push-9 col-sm-12 hidden-sm hidden-xs">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-filter"></i> <?php echo $text_filter; ?></h3>
          </div>
          <div class="panel-body">
            <div class="form-group form-left">
              <label class="control-label" for="input-product"><?php echo $entry_product_name; ?></label>
              <input type="text" name="filter_product" id="input-product" class="form-control" value="<?php echo $filter_product; ?>" placeholder="<?php echo $entry_product_name; ?>">
            </div>
            <div class="form-group form-left">
              <label class="control-label" for="input-model"><?php echo $entry_model; ?></label>
              <input type="text" name="filter_model" value="<?php echo $filter_model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
            </div>
            <div class="form-group form-left">
              <label class="control-label" for="input-category"><?php echo $entry_category; ?></label>
              <input type="text" name="filter_category" value="<?php echo $filter_category; ?>" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" placeholder="<?php echo $entry_category; ?>"/>
              <input type="hidden" name="filter_category_id" value="<?php echo $filter_category_id; ?>" placeholder="<?php echo $entry_category; ?>" id="input-category-id"/>
            </div>

              <button type="button" id="button-clear" class="btn btn-default"><i class="fa fa-refresh"></i> <?php echo $button_clear; ?></button>
              <button type="button" id="button-filter" class="btn btn-default pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
          </div>
        </div>
      </div>
      <div class="col-md-9 col-md-pull-3 col-sm-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_list; ?></h3>
          </div>
          <div class="panel-body">
            <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
              <div class="table-responsive">
                <table class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <td style="width: 1px;" class="text-center">
                        <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" />
                      </td>
                      <td><?php echo $column_image; ?></td>
                      <td class="text-left">
                        <?php if ($sort == 'pd.name') { ?>
                          <a href="<?php echo $sort_product; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_product_name; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_product; ?>"><?php echo $column_product_name; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'p.model') { ?>
                          <a href="<?php echo $sort_model; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_model; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_model; ?>"><?php echo $column_model; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'p.quantity') { ?>
                          <a href="<?php echo $sort_quantity; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_quantity; ?></a>
                        <?php } else { ?>
                         <a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
                       <?php } ?>
                      </td>
                      <td class="text-left">
                        <?php if ($sort == 'p.price') { ?>
                          <a href="<?php echo $sort_price; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_price; ?></a>
                        <?php } else { ?>
                          <a href="<?php echo $sort_price; ?>"><?php echo $column_price; ?></a>
                        <?php } ?>
                      </td>
                      <td class="text-left"><?php echo $column_category; ?></td>
                      <td class="text-left"><?php echo $column_auction; ?></td>
                      <td class="text-right"><?php echo $column_action; ?></td>
                    </tr>
                  </thead>
                  <tbody>
                  <?php if ($products) { ?>
                  <?php foreach ($products as $product) { ?>
                  <tr>
                    <td class="text-center"><?php if (in_array($product['product_id'], $selected)) { ?>
                      <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
                    <?php } else { ?>
                      <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
                    <?php } ?>
                    </td>
                    <td class="text-left"><img src="<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>"/></td>
                    <td class="text-left"><?php echo $product['name']; ?></td>
                    <td class="text-center"><?php echo $product['model']; ?></td>
                    <td class="text-left"><?php echo $product['quantity']; ?></td>
                    <td class="text-right"><?php echo $product['price']; ?></td>
                    <td class="text-left">
                      <?php if ($product['categories']) { ?>
                        <?php echo $product['categories']; ?>
                      <?php } else { ?>
                        <?php echo $text_no_category; ?>
                      <?php } ?>
                    </td>
                    <td class="text-left"><?php echo $product['auction_status']; ?></td>
                    <td class="text-right"><a href="<?php echo $product['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
                  </tr>
                <?php } ?>
                <?php } else { ?>
                  <tr>
                    <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
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
  </div>
  <style media="screen">
    .btn-category.tooltip..tooltip-inner {
      background-color: #fff;
      color: #000;
      border:0.1em solid #000;
    }
    .form-left > .dropdown-menu {
      right: 31px;
      left: unset !important;
    }
  </style>
  <script type="text/javascript"><!--
  $('input[name=\'filter_product\']').autocomplete({
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
  		$('input[name=\'filter_product\']').val(item['label']);
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

  $('input[name=\'filter_category\']').autocomplete({
  	'source': function(request, response) {
  		$.ajax({
  			url: 'index.php?route=catalog/category/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
  			dataType: 'json',
  			success: function(json) {
  				response($.map(json, function(item) {
  					return {
  						label: item['name'],
  						value: item['category_id']
  					}
  				}));
  			}
  		});
  	},
  	'select': function(item) {
  		$('input[name=\'filter_category\']').val(item['label']);
      $('input[name=\'filter_category_id\']').val(item['value']);
  	}
  });

  $('#button-filter').on('click', function() {
  	var url = '';

  	var filter_product = $('input[name=\'filter_product\']').val();

  	if (filter_product) {
  		url += '&filter_product=' + encodeURIComponent(filter_product);
  	}

  	var filter_model = $('input[name=\'filter_model\']').val();

  	if (filter_model) {
  		url += '&filter_model=' + encodeURIComponent(filter_model);
  	}

  	var filter_category = $('input[name=\'filter_category\']').val();

  	if (filter_category) {
  		url += '&filter_category=' + encodeURIComponent(filter_category);
  	}

    var filter_category_id = $('input[name=\'filter_category_id\']').val();

  	if (filter_category_id) {
  		url += '&filter_category_id=' + encodeURIComponent(filter_category_id);
  	}

  	location = 'index.php?route=ebay_map/product_data&token=<?php echo $token; ?>' + url;
  });

  $(document).on('click', '#button-clear', function() {
    location = 'index.php?route=ebay_map/product_data&token=<?php echo $token; ?>';
  });

//--></script>
</div>
<?php echo $footer; ?>
