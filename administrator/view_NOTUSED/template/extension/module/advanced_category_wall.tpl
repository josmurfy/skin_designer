<?php 
/* === Advanced Category Wall Module v1.1.0 for OpenCart by vytasmk at gmail === */

echo $header; 
echo $column_left; 
?>
<div id="content">
	<div class="page-header">
		<div class="container-fluid">
			<div class="pull-right">
				<button type="submit" form="form-featured" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
				<a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
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
				<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-featured" class="form-horizontal">
					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
						<div class="col-sm-10">
							<input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
							<?php if ($error_name) { ?>
							<div class="text-danger"><?php echo $error_name; ?></div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-title<?php echo reset($languages)['language_id']; ?>"><span data-toggle="tooltip" title="<?=$help_title?>"><?=$entry_title?></span></label>
						<div class="col-sm-10">
							<?php foreach ($languages as $language) { ?>
							<div class="input-group">
								<span class="input-group-addon"><img src="language/<?php echo $language['code']; ?>/<?php echo $language['code']; ?>.png" title="<?php echo $language['name']; ?>" alt="<?php echo $language['code']; ?>" /></span>
								<input type="text" name="title_lang[<?php echo $language['language_id']; ?>]" placeholder="<?php echo $entry_title .' ('.$language['name'].')'; ?>" id="input-title<?php echo $language['language_id']; ?>" value="<?php echo isset($title_lang[$language['language_id']]) ? $title_lang[$language['language_id']] : $title; ?>" class="form-control" />
							</div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-filter"><?=$entry_filter?></label>
						<div class="col-sm-10">
							<select name="filter" id="input-filter" class="form-control">
								<?php foreach ($filters as $filter_id => $filter_name) { ?>
								<option value="<?=$filter_id?>" <?=($filter_id == $filter) ? 'selected="selected"':''?>><?=$filter_name?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div id="categories-selected" class="form-group" <?=($filter!="filter_selected")?'style="display:none"':''?>>
						<label class="col-sm-2 control-label" for="input-category"><span data-toggle="tooltip" title="<?php echo $help_category; ?>"><?=$entry_category?></span></label>
						<div class="col-sm-10">
							<input type="text" name="category" value="" placeholder="<?php echo $entry_category; ?>" id="input-category" class="form-control" />
							<div id="advanced-category" class="well well-sm" style="height: 150px; overflow: auto;">
								<?php foreach ($categories as $category) { ?>
								<div id="advanced-category<?php echo $category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?=$category['name']?>
									<input type="hidden" name="category[]" value="<?php echo $category['category_id']; ?>" />
								</div>
								<?php } ?>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-empty"><span data-toggle="tooltip" title="<?php echo $help_empty; ?>"><?=$entry_empty?></span></label>
						<div class="col-sm-10">
							<select name="show_empty" id="input-empty" class="form-control">
								<option value="1" <?=($show_empty)?'selected="selected"':''?>><?php echo $text_show; ?></option>
								<option value="0" <?=(!$show_empty)?'selected="selected"':''?>><?php echo $text_hide; ?></option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-limit"><span data-toggle="tooltip" title="<?=$help_limit?>"><?=$entry_limit?></span></label>
						<div class="col-sm-10">
							<input type="text" name="limit" value="<?php echo $limit; ?>" placeholder="<?php echo $entry_limit; ?>" id="input-limit" class="form-control" />
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-columns"><?=$entry_columns?></label>
						<div class="col-sm-10">
							<select name="columns" id="input-columns" class="form-control">
								<?php foreach (array(1,2,3,4,6) as $col_count): ?>
								<option value="<?=$col_count?>" <?=($col_count==$columns)?'selected="selected"':''?>><?=$col_count?></option>
								<?php endforeach ?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-empty"><?=$entry_show_catname?></span></label>
						<div class="col-sm-10">
							<select name="show_catname" id="input-empty" class="form-control">
								<option value="1" <?=($show_catname)?'selected="selected"':''?>><?php echo $text_show; ?></option>
								<option value="0" <?=(!$show_catname)?'selected="selected"':''?>><?php echo $text_hide; ?></option>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-width"><?=$entry_width?></label>
						<div class="col-sm-10">
							<input type="text" name="width" value="<?php echo $width; ?>" placeholder="<?php echo $entry_width; ?>" id="input-width" class="form-control" />
							<?php if ($error_width) { ?>
							<div class="text-danger"><?php echo $error_width; ?></div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-height"><?=$entry_height?></label>
						<div class="col-sm-10">
							<input type="text" name="height" value="<?php echo $height; ?>" placeholder="<?php echo $entry_height; ?>" id="input-height" class="form-control" />
							<?php if ($error_height) { ?>
							<div class="text-danger"><?=$error_height?></div>
							<?php } ?>
						</div>
					</div>

					<div class="form-group">
						<label class="col-sm-2 control-label" for="input-status"><?=$entry_status?></label>
						<div class="col-sm-10">
							<select name="status" id="input-status" class="form-control">
								<?php if ($status) { ?>
								<option value="1" selected="selected"><?php echo $text_enabled; ?></option>
								<option value="0"><?php echo $text_disabled; ?></option>
								<?php } else { ?>
								<option value="1"><?php echo $text_enabled; ?></option>
								<option value="0" selected="selected"><?php echo $text_disabled; ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript"><!--
		$('input[name=\'category\']').autocomplete({
			source: function(request, response) {
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
			select: function(item) {
				$('input[name=\'category\']').val('');
				
				$('#advanced-category' + item['value']).remove();
				
				$('#advanced-category').append('<div id="advanced-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category[]" value="' + item['value'] + '" /></div>');	
			}
		});
			
		$('#advanced-category').delegate('.fa-minus-circle', 'click', function() {
			$(this).parent().remove();
		});

		$( ".target" ).change(function() {
			alert( "Handler for .change() called." );
		});

		// Show or hide some elements		
		$("#input-filter").change(function() {
			if (this.value == 'filter_all')
  				$('#categories-selected').hide();
  			else
				$('#categories-selected').show();
		});
//--></script></div>
<?php echo $footer; ?>