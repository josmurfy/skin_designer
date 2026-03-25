<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
<div class="page-header">
<div class="container-fluid">
<div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-information').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
			<div class="col-sm-4">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-package-title"><?php echo $entry_name;?> </label>
					<input type="text" name="filter_name" value="" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="col-sm-2 control-label" for="input-package-title"><?php echo $entry_store;?> </label>
					<input type="text" name="filter_store" value="" placeholder="<?php echo $entry_store; ?>" id="input-name" class="form-control" />
					<input type="hidden" name="store_id" value="">
				</div>
			</div>
			<div class="col-sm-2 text-center">
				<button style="margin-top:42%;" type="button" id="button-filter" class="btn btn-primary">
				<i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
			</div>
		</div>
	</div>
	<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-information">
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
					
						<td class="text-left"><?php if ($sort == 'name') { ?>
						<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
						<?php } ?></td>
					
						<td class="text-left"><?php if ($sort == 'store') { ?>
						<a href="<?php echo $sort_store; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_store; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_store; ?>"><?php echo $column_store; ?></a>
						<?php } ?></td>
					
										
					  <td class="text-left"><?php if ($sort == 'status') { ?>
						<a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
						<?php } ?></td>
					  <td class="text-right"><?php echo $column_action; ?></td>
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
					<td class="text-left"><?php echo $result['store']; ?></td>
					<td class="text-left"><?php echo $result['status']; ?></td>
					<td class="text-right"><a href="<?php echo $result['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
				</tr>
				<?php } ?> 
				<?php } else { ?>
				<tr>
					<td class="text-center" colspan="6"><?php echo $text_no_results; ?></td>
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
<script type="text/javascript">
$('#button-filter').on('click', function() {
	var url = 'index.php?route=possetting/product&token=<?php echo $token; ?>';
	
	var filter_store = $('input[name=\'store_id\']').val();

	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
	}
	
	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
		
  location = url;
});
</script>
<script type="text/javascript">
$('input[name=\'filter_store\']').autocomplete({
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
		$('input[name=\'filter_store\']').val(item['label']);
		$('input[name=\'store_id\']').val(item['value']);
	}
});
</script>
<?php echo $footer; ?>