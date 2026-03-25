<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-block-ip').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label" for="input-user-ip"><?php echo $entry_user_ip; ?></label>
								<input type="text" name="filter_user_ip" value="<?php echo $filter_user_ip; ?>" placeholder="<?php echo $entry_user_ip; ?>" id="input-product" class="form-control" />
							</div>
							<div class="form-group">
								<label class="control-label" for="input-country-iso-code"><?php echo $entry_country_iso_code; ?></label>
								<input type="text" name="filter_country_iso_code" value="<?php echo $filter_country_iso_code; ?>" placeholder="<?php echo $entry_country_iso_code; ?>" id="input-country-iso-code" class="form-control" />
							</div>
							<div class="form-group">
								<label class="control-label" for="input-country-iso-code"><?php echo $entry_subdivision_iso_code; ?></label>
								<input type="text" name="filter_subdivision_iso_code" value="<?php echo $filter_subdivision_iso_code; ?>" placeholder="<?php echo $entry_subdivision_iso_code; ?>" id="input-country-iso-code" class="form-control" />
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label class="control-label" for="input-country-name"><?php echo $entry_country_name; ?></label>
								<input type="text" name="filter_country_name" value="<?php echo $filter_country_name; ?>" placeholder="<?php echo $entry_country_name; ?>" id="input-country-name" class="form-control" />
							</div>
							<div class="form-group">
								<label class="control-label" for="input-subdivision-name"><?php echo $entry_subdivision_name; ?></label>
								<input type="text" name="filter_subdivision_name" value="<?php echo $filter_subdivision_name; ?>" placeholder="<?php echo $entry_subdivision_name; ?>" id="input-subdivision-name" class="form-control" />
							</div>
							<div class="form-group">
								<label class="control-label" for="input-date-added"><?php echo $entry_access_date; ?></label>
								<div class="input-group date">
									<input type="text" name="filter_access_date" value="<?php echo $filter_access_date; ?>" placeholder="<?php echo $entry_access_date; ?>" data-date-format="YYYY-MM-DD" id="input-date-added" class="form-control" />
									<span class="input-group-btn">
										<button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
									</span>
								</div>
							</div>
							<button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
						</div>
					</div>
				</div>
				<form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-block-ip">
					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<thead>
								<tr>
									<td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
									<td class="text-left">
										<?php if ($sort == 'user_ip') { ?>
										<a href="<?php echo $sort_user_ip; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_user_ip; ?></a>
										<?php } else { ?>
										<a href="<?php echo $sort_user_ip; ?>"><?php echo $column_user_ip; ?></a>
										<?php } ?>
									</td>
									<td class="text-left">
										<?php if ($sort == 'country_iso_code') { ?>
										<a href="<?php echo $sort_country_iso_code; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_country_iso_code; ?></a>
										<?php } else { ?>
										<a href="<?php echo $sort_country_iso_code; ?>"><?php echo $column_country_iso_code; ?></a>
										<?php } ?>
									</td>
									<td class="text-right">
										<?php if ($sort == 'country_name') { ?>
										<a href="<?php echo $sort_country_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_country_name; ?></a>
										<?php } else { ?>
										<a href="<?php echo $sort_country_name; ?>"><?php echo $column_country_name; ?></a>
										<?php } ?>
									</td>
									<td class="text-left">
										<?php if ($sort == 'subdivision_name') { ?>
										<a href="<?php echo $sort_subdivision_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_subdivision_name; ?></a>
										<?php } else { ?>
										<a href="<?php echo $sort_subdivision_name; ?>"><?php echo $column_subdivision_name; ?></a>
										<?php } ?>
									</td>
									<td class="text-left">
										<?php if ($sort == 'subdivision_iso_code') { ?>
										<a href="<?php echo $sort_subdivision_iso_code; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_subdivision_iso_code; ?></a>
										<?php } else { ?>
										<a href="<?php echo $sort_subdivision_iso_code; ?>"><?php echo $column_subdivision_iso_code; ?></a>
										<?php } ?>
									</td>
									<td class="text-left">
										<?php if ($sort == 'access_date') { ?>
										<a href="<?php echo $sort_access_date; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_access_date; ?></a>
										<?php } else { ?>
										<a href="<?php echo $sort_access_date; ?>"><?php echo $column_access_date; ?></a>
										<?php } ?>
									</td>
									<td class="text-right"><?php echo $column_action; ?></td>
								</tr>
							</thead>
							<tbody>
								<?php if ($blocked_ips) { ?>
								<?php foreach ($blocked_ips as $blocked_ip) { ?>
								<tr>
									<td class="text-center">
										<?php if (in_array($blocked_ip['id'], $selected)) { ?>
										<input type="checkbox" name="selected[]" value="<?php echo $blocked_ip['id']; ?>" checked="checked" />
										<?php } else { ?>
										<input type="checkbox" name="selected[]" value="<?php echo $blocked_ip['id']; ?>" />
										<?php } ?>
									</td>
									<td class="text-left"><?php echo $blocked_ip['user_ip']; ?></td>
									<td class="text-left"><?php echo $blocked_ip['country_iso_code']; ?></td>
									<td class="text-right"><?php echo $blocked_ip['country_name']; ?></td>
									<td class="text-left"><?php echo $blocked_ip['subdivision_name']; ?></td>
									<td class="text-left"><?php echo $blocked_ip['subdivision_iso_code']; ?></td>
									<td class="text-left"><?php echo $blocked_ip['access_date']; ?></td>
									<td class="text-right"><a href="<?php echo $blocked_ip['view']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-eye"></i></a></td>
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
	<script type="text/javascript"><!--
	$('#button-filter').on('click', function() {
	url = 'index.php?route=cfgeoip/block_ip&token=<?php echo $token; ?>';

	var filter_user_ip = $('input[name=\'filter_user_ip\']').val();

	if (filter_user_ip) {
	url += '&filter_user_ip=' + encodeURIComponent(filter_user_ip);
	}

	var filter_country_iso_code = $('input[name=\'filter_country_iso_code\']').val();

	if (filter_country_iso_code) {
	url += '&filter_country_iso_code=' + encodeURIComponent(filter_country_iso_code);
	}

	var filter_country_name = $('input[name=\'filter_country_name\']').val();

	if (filter_country_name) {
	url += '&filter_country_name=' + encodeURIComponent(filter_country_name); 
	}		

	var filter_subdivision_name = $('input[name=\'filter_subdivision_name\']').val();

	if (filter_subdivision_name) {
	url += '&filter_subdivision_name=' + encodeURIComponent(filter_subdivision_name); 
	}		

	var filter_subdivision_iso_code = $('input[name=\'filter_subdivision_iso_code\']').val();

	if (filter_subdivision_iso_code) {
	url += '&filter_subdivision_iso_code=' + encodeURIComponent(filter_subdivision_iso_code); 
	}		

	var filter_access_date = $('input[name=\'filter_access_date\']').val();

	if (filter_access_date) {
	url += '&filter_access_date=' + encodeURIComponent(filter_access_date);
	}

	location = url;
	});
	//--></script> 
	<script type="text/javascript"><!--
	$('.date').datetimepicker({
	pickTime: false
	});
	//--></script>
</div>
<?php echo $footer; ?>
