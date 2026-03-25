<div class="modal-content">
<div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button></div>
      <div class="modal-body">  
	   <div class="text-right">
		<a style="margin-bottom:10px;" href="<?php echo $add; ?>" class="btn-add btn btn-primary customerform" data-toggle="modal" data-target="#addModal"><i class="fa fa-plus"></i></a>
	   </div>
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <?php if ($success) { ?>
    <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
      
          <div class="table-responsive">
     
            <div class="well">
              <div class="row">
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-filter_name"><?php echo $column_name;?> </label>
                    <input type="text" name="filter_name" value="<?php echo $filter_name?>" placeholder="<?php echo $column_name; ?>" id="input-filter_name" class="form-control" />
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-filter_email"><?php echo $column_email;?> </label>
                    <input type="text" name="filter_email" value="<?php echo $filter_email; ?>" placeholder="<?php echo $column_email; ?>" id="input-filter_email" class="form-control" />
                  </div>
                </div>
                <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-filter_telephone"><?php echo $column_telephone;?> </label>
                    <input type="text" name="filter_telephone" value="<?php echo $filter_telephone; ?>" placeholder="<?php echo $column_telephone; ?>" id="input-filter_telephone" class="form-control" />
                  </div>
                  <div class="pull-right">  
                  <button type="button" id="button-filter" class="btn btn-primary"><i class="fa fa-filter"></i> <?php echo $button_filter; ?></button>
                      </div>
                </div>
              </div>
            </div>
       
            <table class="table table-bordered table-hover">
              <thead class="sortorder">
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>
                  <td class="text-left"><?php if ($sort == 'name') { ?>
                    <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'c.email') { ?>
                    <a href="<?php echo $sort_email; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_email; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_email; ?>"><?php echo $column_email; ?></a>
                    <?php } ?></td>
              
                  <td class="text-left"><?php if ($sort == 'address') { ?>
                    <a href="<?php echo $sort_address; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_address; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_address; ?>"><?php echo $column_address; ?></a>
                    <?php } ?></td>
                  <td class="text-left"><?php if ($sort == 'telephone') { ?>
                    <a href="<?php echo $sort_telephone; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_telephone; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_telephone; ?>"><?php echo $column_telephone; ?></a>
                    <?php } ?></td>
              
                  <td class="text-left"><?php if ($sort == 'c.status') { ?>
                    <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
                    <?php } else { ?>
                    <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
                    <?php } ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($customers) { ?>
                <?php foreach ($customers as $customer) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($customer['customer_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $customer['customer_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $customer['customer_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $customer['name']; ?></td>
                  <td class="text-left"><?php echo $customer['email']; ?></td>
                  <td class="text-left"><?php echo $customer['addressname']; ?>,<?php echo $customer['address']; ?></td>
                  <td class="text-left"><?php echo $customer['telephone']; ?></td>
                  <td class="text-left"><?php echo $customer['status']; ?></td>
                </tr>
				
                <?php } ?>
				<tr>
				<td colspan="9">
				
					<div class="col-sm-6 text-left"><?php echo $pagination; ?></div>
				  <div class="col-sm-6 text-right"><?php echo $results; ?></div>
				
				</td>
				</tr>
                <?php } else { ?>
                <tr>
                  <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        
     </div>   
</div>
<script>

// Customer pagination
$('#help-modal14').delegate('.pagination a,.sortorder a', 'click', function(e) {
    e.preventDefault();
	
	$('.customerlist-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
	$('.customerlist-body').load(this.href);
	
	return false;
});
</script>

<script type="text/javascript">
$(document).on('click', '#button-filter',function(){
	
  var url = 'index.php?route=pos/customerlist&token=<?php echo $token; ?>';
  
  var filter_name = $('.customerlist-body input[name=\'filter_name\']').val();

  if (filter_name) {
    url += '&filter_name=' + encodeURIComponent(filter_name);
  }

  var filter_email = $('.customerlist-body input[name=\'filter_email\']').val();

  if (filter_email) {
    url += '&filter_email=' + encodeURIComponent(filter_email);
  }

  var filter_telephone = $('.customerlist-body input[name=\'filter_telephone\']').val();

  if (filter_telephone) {
    url += '&filter_telephone=' + encodeURIComponent(filter_telephone);
  }
  $('.customerlist-body').html('<div class="loadingpoup"><i class="fa fa-spinner fa-spin fa-3x fa-fw"></i></div>');
  $('.customerlist-body').load(url);
 return false;	
});
</script>