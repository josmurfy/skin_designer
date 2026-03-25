<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-module" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
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
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-module" class="form-horizontal">
		  <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-general" data-toggle="tab"><?php echo $tab_general; ?></a></li>
            <li><a href="#tab-related" data-toggle="tab"><?php echo $tab_related; ?></a></li>
            <li><a href="#tab-all-seo" data-toggle="tab"><?php echo $tab_all_seo; ?></a></li>
            <?php echo $about; ?>
          </ul>
		  <div class="tab-content">
            <div class="tab-pane active" id="tab-general">
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_clear_product; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $clear_product; ?>" class="btn btn-danger"><?php echo $button_clear_product; ?></a>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_clear_category; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $clear_category; ?>" class="btn btn-danger"><?php echo $button_clear_category; ?></a>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_clear_manufacturer; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $clear_manufacturer; ?>" class="btn btn-danger"><?php echo $button_clear_manufacturer; ?></a>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_clear_information; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $clear_information; ?>" class="btn btn-danger"><?php echo $button_clear_information; ?></a>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_generate_product; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $generate_product; ?>" class="btn btn-success"><?php echo $button_generate_product; ?></a>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_generate_category; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $generate_category; ?>" class="btn btn-success"><?php echo $button_generate_category; ?></a>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_generate_manufacturer; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $generate_manufacturer; ?>" class="btn btn-success"><?php echo $button_generate_manufacturer; ?></a>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_generate_information; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $generate_information; ?>" class="btn btn-success"><?php echo $button_generate_information; ?></a>
				</div>
			  </div>
			</div>
            <div class="tab-pane" id="tab-related">
			  <div class="form-group required">
                <label class="col-sm-2 control-label" for="input-limit"><?php echo $entry_related_limit; ?></label>
                <div class="col-sm-10">
                  <input type="text" name="seo_related_limit" value="<?php echo $seo_related_limit; ?>" placeholder="<?php echo $entry_related_limit; ?>" id="input-limit" class="form-control" />
                </div>
              </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label" for="input-random"><?php echo $entry_related_random; ?></label>
				<div class="col-sm-10">
				  <select name="seo_related_random" id="input-random" class="form-control">
					<option value="1"<?php echo $seo_related_random ? ' selected="selected"' : ''; ?>><?php echo $text_enabled; ?></option>
					<option value="0"<?php echo $seo_related_random ? '' : ' selected="selected"'; ?>><?php echo $text_disabled; ?></option>
				  </select>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_clear_related; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $clear_related; ?>" class="btn btn-danger"><?php echo $button_clear_related; ?></a>
				</div>
			  </div>
			  <div class="form-group">
				<label class="col-sm-2 control-label"><?php echo $entry_clear_related; ?></label>
				<div class="col-sm-10">
				  <a href="<?php echo $clear_related; ?>" id="button-generate-related" class="btn btn-primary"><?php echo $button_generate_related; ?></a>
				</div>
			  </div>
			</div>
			<div class="tab-pane" id="tab-all-seo">
			  <table class="table table-bordered table-hover" id="table-all-seo">
				<thead>
				  <tr>
					<td class="text-left"><?php echo $column_query; ?></td>
					<td class="text-left"><?php echo $column_keyword; ?></td>
					<td class="text-right"><?php echo $column_action; ?></td>
				  </tr>
				</thead>
				<tbody>
				</tbody>
			  </table>
			  <div class="pagination"></div>
			</div>
			<?php echo $tab; ?>
		  </div>
		</form>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript"><!--
$('a[href=\'#tab-all-seo\']').click(function() {
	allseo('index.php?route=extension/module/seo/allseo&token=<?php echo $token; ?>&page=1')
});

$(document).on('click', '.pagination a', function() {
	allseo(this.href);
	
	return false;
});

function allseo(url) {
	$.ajax({
		url: url,
		dataType: 'json',
		beforeSend: function() {
			$('#table-all-seo > tbody').html('<tr><td colspan="3" class="text-center"><i class="fa fa-spinner"></i></td></tr>');
		},
		complete: function() {
			
		},
		success: function(json) {
			html = '';
			
			if (json['urls'] != '') {
				for (i = 0; i < json['urls'].length; i++) {
					html += '<tr>';
					html += '<td class="text-left">' + json['urls'][i]['query'] + '</td>';
					html += '<td class="text-left">' + json['urls'][i]['keyword'] + '</td>';
					html += '<td class="text-right">';
					html += '[ <a target="_blank" href="' + json['urls'][i]['url'] + '"><?php echo $text_edit_keyword; ?></a> ]&nbsp;&nbsp;';
					html += '[ <a href="index.php?route=extension/module/seo/deleteseo&token=<?php echo $token; ?>&url_alias_id=' + json['urls'][i]['url_alias_id'] + '"><?php echo $text_delete; ?></a> ]';
					html += '</td>';
					html += '</tr>';
				}
			}
			
			$('#table-all-seo > tbody').html(html);
			$('.pagination').html(json['pagination']);
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
}

$('#button-generate-related').click(function() {
	relate('index.php?route=extension/module/seo/generaterelated&token=<?php echo $token; ?>');
	
	return false;
});

function relate(url) {
	$.ajax({
		url: url,
		dataType: 'json',
		beforeSend: function() {
			$('#button-generate-related').after('<i class="fa fa-spinner"></i>');
		},
		success: function(json) {
			$('.fa-spinner, .error, .alert-success').remove();
			
			if (json['error']) {
				
			} else {
				if (json['next']) {
					relate('index.php?route=extension/module/seo/generaterelated&token=<?php echo $token; ?>&page=' + json['next']);
				}

				$('.breadcrumb').after('<div class="alert alert-success"><i class="fa fa-check-circle"></i> ' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
			}
		},
		error: function(xhr, ajaxOptions, thrownError) {
			alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
		}
	});	
}
//--></script>
<?php echo $footer; ?>