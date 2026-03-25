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
			</div>
		</div>
	<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-information">
		<div class="table-responsive">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
					<td class="text-left"><?php if ($sort == 'name') { ?>
						<a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
						<?php } ?></td>
					
						<td class="text-left"><?php if ($sort == 'location') { ?>
						<a href="<?php echo $sort_location; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_location; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_location; ?>"><?php echo $column_location; ?></a>
						<?php } ?></td>
					
					  <td class="text-left"><?php if ($sort == 'phone') { ?>
						<a href="<?php echo $sort_phone; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_phone; ?></a>
						<?php } else { ?>
						<a href="<?php echo $sort_phone; ?>"><?php echo $column_phone; ?></a>
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
				<?php if ($stores) { ?>
				<?php foreach ($stores as $result) { ?>
				<tr>
				 <td class="text-center">
			    	<input type="checkbox" name="selected[]" value="<?php echo $result['store_id']; ?>"  />
				</td>
					<td class="text-left"><?php echo $result['name']; ?></td>
					<td class="text-left"><?php echo $result['location']; ?></td>						
					<td class="text-left"><?php echo $result['phone']; ?></td>						
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
	
	var url = 'index.php?route=job/employ&token=<?php echo $token; ?>';

	var filter_name = $('input[name=\'filter_name\']').val();

	if (filter_name) {
		url += '&filter_name=' + encodeURIComponent(filter_name);
	}
	var filter_fullname = $('input[name=\'filter_fullname\']').val();

	if (filter_fullname) {
		url += '&filter_fullname=' + encodeURIComponent(filter_fullname);
	}
	var filter_email = $('input[name=\'filter_email\']').val();

	if (filter_email) {
		url += '&filter_email=' + encodeURIComponent(filter_email);
	}
	location = url;
});
</script>
<script type="text/javascript">
$('input[name=\'filter_name\']').autocompletec({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=localisation/country/autocompletec&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				json.unshift({
					country_id: 0,
					name:'<?php echo $text_none; ?>'
				});

				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['country_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_name\']').val(item['label']);
		$('input[name=\'country_id\']').val(item['value']);
	}
});
</script>
<script type="text/javascript">
$('input[name=\'filter_name\']').autocomplete({
	'source': function(request, response) {
		$.ajax({
			url: 'index.php?route=localisation/country/autocomplete&token=<?php echo $token; ?>&filter_name=' +  encodeURIComponent(request),
			dataType: 'json',
			success: function(json) {
				json.unshift({
					country_id: 0,
					name:'<?php echo $text_none; ?>'
				});

				response($.map(json, function(item) {
					return {
						label: item['name'],
						value: item['country_id']
					}
				}));
			}
		});
	},
	'select': function(item) {
		$('input[name=\'filter_name\']').val(item['label']);
		$('input[name=\'country_id\']').val(item['value']);
	}
});
</script>
<?php echo $footer; ?>