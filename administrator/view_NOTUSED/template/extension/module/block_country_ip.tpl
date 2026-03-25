<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-banner" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
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
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
			</div>
			<div class="panel-body">
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-banner" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
						<div class="col-sm-10">
							<select name="module_block_country_ip_status" id="input-status" class="form-control">
								<?php if ($module_block_country_ip_status) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_country; ?>"><?php echo $entry_country; ?></span></label>
						<div class="col-sm-10">
							<div class="well well-sm" style="height: 150px; overflow: auto;">
								<?php foreach ($countries as $country) { ?>
								<div class="checkbox">
									<label>
										<?php if (in_array($country['country_id'], $module_block_country_ip_country)) { ?>
										<input type="checkbox" name="module_block_country_ip_country[]" value="<?php echo $country['country_id']; ?>" checked="checked" />
										<?php echo $country['name']; ?>
										<?php } else { ?>
										<input type="checkbox" name="module_block_country_ip_country[]" value="<?php echo $country['country_id']; ?>" />
										<?php echo $country['name']; ?>
										<?php } ?>
									</label>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_redirect; ?>"><?php echo $entry_redirect; ?></span></label>
						<div class="col-sm-10">
							<label class="radio-inline">
								<?php if ($module_block_country_ip_redirect) { ?>
								<input type="radio" name="module_block_country_ip_redirect" value="1" checked="checked" onclick="showInputSite();" />
								<?php echo $text_yes; ?>
								<?php } else { ?>
								<input type="radio" name="module_block_country_ip_redirect" value="1" onclick="showInputSite();" />
								<?php echo $text_yes; ?>
								<?php } ?>
							</label>
							<label class="radio-inline">
								<?php if (!$module_block_country_ip_redirect) { ?>
								<input type="radio" name="module_block_country_ip_redirect" value="0" checked="checked" onclick="showInputMessage();" />
								<?php echo $text_no; ?>
								<?php } else { ?>
								<input type="radio" name="module_block_country_ip_redirect" value="0" onclick="showInputMessage();" />
								<?php echo $text_no; ?>
								<?php } ?>
							</label>
						</div>
					</div>
					<div class="form-group required" id="form-field-message">
						<label class="col-sm-2 control-label" for="input-message"><span data-toggle="tooltip" title="<?php echo $help_message; ?>"><?php echo $entry_message; ?></span></label>
						<div class="col-sm-10">
							<input type="text" name="module_block_country_ip_message" value="<?php echo $module_block_country_ip_message; ?>" placeholder="<?php echo $entry_message; ?>" id="input-msg" class="form-control" />
							<?php if ($error_message) { ?>
							<div class="text-danger"><?php echo $error_message; ?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group required" id="form-field-site">
						<label class="col-sm-2 control-label" for="input-site"><span data-toggle="tooltip" title="<?php echo $help_site; ?>"><?php echo $entry_site; ?></span></label>
						<div class="col-sm-10">
							<input type="text" name="module_block_country_ip_site" value="<?php echo $module_block_country_ip_site; ?>" placeholder="<?php echo $entry_site; ?>" id="input-msg" class="form-control" />
							<?php if ($error_site) { ?>
							<div class="text-danger"><?php echo $error_site; ?></div>
							<?php } ?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
<?php echo $footer; ?>
<script type="text/javascript">

var redirect = <?php echo $module_block_country_ip_redirect; ?>;

if(redirect == '1')
{
	showInputSite();
}
else
{
	showInputMessage();
}

function showInputMessage()
{
	$("#form-field-message").show();
	$("#form-field-site").hide();
}

function showInputSite()
{
	$("#form-field-message").hide();
	$("#form-field-site").show();
}
</script>
