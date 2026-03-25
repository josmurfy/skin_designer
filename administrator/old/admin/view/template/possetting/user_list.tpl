<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
        <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger" onclick="confirm('<?php echo $text_confirm; ?>') ? $('#form-user').submit() : false;"><i class="fa fa-trash-o"></i></button>
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
							<label class="control-label" for="input-package-title"><?php echo $entry_store;?> </label>
							<input type="text" name="filter_store" value="" placeholder="<?php echo $entry_store; ?>" id="input-name" class="form-control" />
							<input type="hidden" name="store_id" value="">
						</div>
					</div>
					<div class="col-sm-2 text-center">
						<button style="margin-top:21%;" type="button" id="button-filter" class="btn btn-primary"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
					</div>
				</div>
			</div>	
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-user">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'username') { ?>
                    <a href="<?php echo $sort_username; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_username; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_username; ?>"><?php echo $column_username; ?></a>
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
                    <td class="text-left"><?php if ($sort == 'commission') { ?>
                    <a href="<?php echo $sort_commission; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_commission; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_commission; ?>"><?php echo $column_commission; ?></a>
                    <?php } ?></td>
                  	<td class="text-left"><?php if ($sort == 'date_added') { ?>
                    <a href="<?php echo $sort_date_added; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_date_added; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_date_added; ?>"><?php echo $column_date_added; ?></a>
                    <?php } ?></td>
                  <td class="text-right"><?php echo $column_action; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($users) { ?>
                <?php foreach ($users as $user) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($user['user_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $user['user_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $user['user_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $user['username']; ?></td>
                  <td class="text-left"><?php echo $user['store']; ?></td>
                  <td class="text-left"><?php echo $user['status']; ?></td>
                  <td class="text-left"><?php echo $user['commission']; ?> = <?php echo $user['commission_value']; ?></td>
                  <td class="text-left"><?php echo $user['date_added']; ?></td>
                  <td class="text-right"><a href="<?php echo $user['edit']; ?>" data-toggle="tooltip" title="<?php echo $button_edit; ?>" class="btn btn-primary"><i class="fa fa-pencil"></i></a></td>
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
<script type="text/javascript">
$('#button-filter').on('click', function() {
	var url = 'index.php?route=possetting/user&token=<?php echo $token; ?>';
	
	var filter_store = $('input[name=\'store_id\']').val();

	if (filter_store) {
		url += '&filter_store=' + encodeURIComponent(filter_store);
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
