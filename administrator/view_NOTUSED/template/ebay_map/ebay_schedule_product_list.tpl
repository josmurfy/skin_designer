<div id="content">
<link href="view/stylesheet/csspin.css" rel="stylesheet" type="text/css"/>
<style type="text/css">
  .cp-round::before, .cp-round::after{
    width: 35px;
    left:8px;
    height: 35px;
    /*top: 25px;*/
    margin-top: 25px;
  }
  .btn-success{
    background-color:#6ABD6A;
    color:#FFF;
    border-style: solid;
    border-width: 1px;
    border-color: #6ABD6A;
    border-bottom-width: 3px;
  }
  .btn-success:hover{
    background-color:#e6e6e6;
    color:#333;
    border-style: solid;
    border-width: 1px;
    border-color: #adadad;
    border-bottom-width: 3px;
  }
</style>
<div class="page-header">
  <div class="container-fluid">
    <h3><?php echo $heading_title; ?></h3>
    <div class="pull-right" style="margin-bottom: 10px;">
  <div class="btn btn-success" data-toggle="modal" data-target="#re_schedule">
    <?php echo $text_re_schedule_product; ?>
      </div>
  <div class="btn btn-warning cancel">
    <?php echo $text_cancel; ?>
      </div>
      <div class="btn btn-warning result" data-toggle="modal" data-target="#show_schedule_result">
        <?php echo $text_result; ?>
          </div>

      </div>
  </div>
</div>

  <!-- Modal for Schedule product -->
  <div class="modal fade" id="re_schedule" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel"><?php echo $text_re_schedule_product; ?></h4>
        </div>
        <div class="modal-body" style="min-height:120px;" >
          <div class="scheduling_items" style="display: block;">
            <div class="col-sm-3">
        <label><?php echo $text_schedule_date_time; ?></label>
    </div>
                                <div class="col-sm-3">
                      <input type="text" name="scheduling_date" placeholder="Choose date" value="" class="form-control date_ss" autocomplete="off">
                                </div>
                                <div class="col-sm-3">
                   <input type="text" name="scheduling_time" placeholder="Choose time" value="" class="form-control time" autocomplete="off">
                                </div>
                                    </div>

    <div class="btn btn-success submit" >
    <?php echo $text_submit; ?>
      </div>
    <span class="error" style="float:left; margin-top:20px;"></span>
        </div>
        <div class="modal-footer">

          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $text_close; ?></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal-for show Re-schedule model -->
  <div class="modal fade" id="show_schedule_result" tabindex="-1" role="dialog" aria-labelledby="myModalLabel_schedule">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel_schedule"><?php echo $text_schedule_result_show; ?></h4>
        </div>
        <div class="modal-body" style="overflow-y: scroll;max-height: 350px"  >
          <div id="show_final_schedule_result">

         </div>

      </div>


        <div class="modal-footer">

          <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $text_close; ?></button>
        </div>
          </div>
      </div>

  </div>

  <div class="container-fluid" id="product_import_list_section">

    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"> <i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading"  style="display:inline-block;width:100%;">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_product_list; ?></h3>

      </div>
      <div class="panel-body">
        <div class="col-sm-12 form-horizontal text-right">
         <div class="col-sm-12 form-group">


         </div>
       </div>

       <div class="well">
         <div class="row">
           <div class="col-sm-4">
             <div class="form-group">
               <label class="control-label" for="input-oc-product-id"><?php echo $column_oc_product_id; ?></label>
                 <input type="text" name="filter_oc_product_id_schedule" value="<?php echo $filter_oc_product_id_schedule; ?>" placeholder="<?php echo $column_oc_product_id; ?>" id="input-oc-product-id" class="form-control"/>
             </div>

             <div class="form-group">
               <label class="control-label" for="input-ebay-product-id"><?php echo $column_ebay_product_id; ?></label>
                 <input type="text" name="filter_ebay_product_id_schedule" value="<?php echo $filter_ebay_product_id_schedule; ?>" placeholder="<?php echo $column_ebay_product_id; ?>" id="input-ebay-product-id" class="form-control"/>
             </div>
           </div>

           <div class="col-sm-4">
               <div class="form-group">
                 <label class="control-label" for="input-oc-product-name"><?php echo $column_product_name; ?></label>
                 <div class='input-group'>
                   <input type="text" name="filter_oc_product_name_schedule" value="<?php echo $filter_oc_product_name_schedule; ?>" placeholder="<?php echo $column_product_name; ?>" id="input-oc-product-name" class="form-control"/>
                   <span class="input-group-addon">
                     <span class="fa fa-angle-double-down"></span>
                   </span>
                 </div>
               </div>

               <div class="form-group">
                 <label class="control-label" for="input-oc-category-name"><?php echo $column_category_name; ?></label>
                 <div class='input-group'>
                   <input type="text" name="filter_category_name_schedule" value="<?php echo $filter_category_name_schedule; ?>" placeholder="<?php echo $column_category_name; ?>" id="input-oc-category-name" class="form-control"/>
                   <span class="input-group-addon">
                     <span class="fa fa-angle-double-down"></span>
                   </span>
                 </div>
               </div>
           </div>

           <div class="col-sm-4">
             <div class="form-group">
               <label class="control-label" for="input-sync-source"><?php echo $column_ebay_source; ?></label>

               <select name="filter_source_sync_schedule" class="form-control">
                 <option value="*"><?php echo $entry_sync_source; ?></option>
                   <option value="Ebay Item" <?php if(isset($filter_source_sync_schedule) && $filter_source_sync_schedule == 'Ebay Item'){ echo 'selected'; } ?> >Ebay Item</option>
                   <option value="Opencart Product" <?php if(isset($filter_source_sync_schedule) && $filter_source_sync_schedule == 'Opencart Product'){ echo 'selected'; } ?> >Opencart Product</option>
               </select>
             </div>

             <div style="margin-top:38px;">
               <button type="button" onclick="filter_map_product_shedule();" class="btn btn-primary" style="border-radius:0px;">
                 <i class="fa fa-search"></i><?php echo $button_filter_product; ?></button>
               <a href="<?php echo $clear_product_filter; ?>" class="btn btn-default pull-right" style="border-radius:0px;"><i class="fa fa-eraser" aria-hidden="true"></i><?php echo $button_clear_product; ?></a>
             </div>
           </div>

         </div>
       </div>

        <form method="post" enctype="multipart/form-data" id="product_schedule" style="clear:both;">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>

                  <td class="text-left"><?php echo $column_oc_product_id; ?></td>
                  <td class="text-left"><?php echo $column_product_name; ?></td>
                  <td class="text-left"><?php echo $column_ebay_product_id; ?></td>
                  <td class="text-left"><?php echo $column_category_name; ?></td>
                  <td class="text-left"><?php echo $column_ebay_source; ?></td>
                  <td class="text-left" style="width: 85px;"><?php echo $text_column_schedule; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($import_products) { ?>
                <?php foreach ($import_products as $ebay_product) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($ebay_product['map_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ebay_product['oc_product_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $ebay_product['oc_product_id']; ?>" />
                    <?php } ?></td>


                  <td class="text-left"><?php echo $ebay_product['oc_product_id']; ?></td>
                  <td class="text-left"><?php echo $ebay_product['product_name']; ?></td>
                  <td class="text-left text-info"><?php echo $ebay_product['ebay_product_id']; ?></td>
                  <td class="text-left"><?php echo $ebay_product['category_name']; ?></td>
                  <td class="text-left"><?php echo $ebay_product['sync_source']; ?></td>
                  <td class="text-left"><?php echo $ebay_product['schedule_date_time']; ?></td>
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

<script type="text/javascript"><!--

$('.date_ss').datetimepicker({
  pickTime: false,
  minDate: new Date()
});
$('.time').datetimepicker({
  pickDate: false,


});
function filter_map_product_shedule() {
	url = 'index.php?route=ebay_map/ebay_account/edit&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&status=product_scheduling';

  var filter_oc_product_id = $('input[name=\'filter_oc_product_id_schedule\']').val();

  if (filter_oc_product_id) {
    url += '&filter_oc_product_id_schedule=' + encodeURIComponent(filter_oc_product_id);
  }

	var filter_oc_product_name = $('input[name=\'filter_oc_product_name_schedule\']').val();

	if (filter_oc_product_name) {
		url += '&filter_oc_product_name_schedule=' + encodeURIComponent(filter_oc_product_name);
	}

  var filter_ebay_product_id = $('input[name=\'filter_ebay_product_id_schedule\']').val();

  if (filter_ebay_product_id) {
    url += '&filter_ebay_product_id_schedule=' + encodeURIComponent(filter_ebay_product_id);
  }

	var filter_category_name = $('input[name=\'filter_category_name_schedule\']').val();

	if (filter_category_name) {
		url += '&filter_category_name_schedule=' + encodeURIComponent(filter_category_name);
	}

  var filter_source_sync = $('select[name=\'filter_source_sync_schedule\']').val();

	if (filter_source_sync != '*') {
		url += '&filter_source_sync_schedule=' + encodeURIComponent(filter_source_sync);
	}

	location = url;
}

$('input[name=\'filter_category_name_schedule\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_schedule_product/autocomplete&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&filter_category_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.item_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_category_name_schedule\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

$('input[name=\'filter_oc_product_name_schedule\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_schedule_product/autocomplete&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&filter_oc_product_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.item_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_oc_product_name_schedule\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

//--></script>
<script type="text/javascript">


$(document).on('click','.cancel', function(){
  if(confirm('Are you sure, if you cancel product thent product will cancel from ebay list.')){

  cancel_status=false; var product_id=[]; var i=0;
  $("#product_schedule input[type=checkbox]:checked").each(function(key, val){
    if($(val).val()){
      cancel_status = true;
      product_id[i++]=$(val).val();
    }
  });
  if(cancel_status){
      $('#show_final_schedule_result').html('');
    $.ajax({
        url     : 'index.php?route=ebay_map/ebay_schedule_product/cancelScheduleProduct&token=<?php echo $token; ?>',
        type    :   "POST",
        dataType:   "json",
        data    : {
              product_ids :product_id },
                  beforeSend: function() {
                    $('.block_div').css('display','block');
                  },
                  complete:function() {
                      $('.block_div').css('display','none');
                  },
        success: function(responseText) {
          $('#show_final_schedule_result').html(responseText);
          $('#show_schedule_result').modal('show');
        }
      });
  }else{
      alert('Warning: You have to select atleast one record to cancel!');
  }

}
});
$(document).on('click','.submit', function(){
  //return false;
  $('.error').removeClass('alert alert-danger');
    $('.error').html(' ');
  cancel_status=false;
  var i=0; var product_id=[];
  $("#product_schedule input[type=checkbox]:checked").each(function(key, val){
    if($(val).val()){
      cancel_status = true;
   product_id[i++]=$(val).val();
    }
  });

  if(cancel_status){
      var scheduling_date = $('input[name=scheduling_date]').val();
      var scheduling_time = $('input[name=scheduling_time]').val();
      if(scheduling_time!='' && scheduling_date!=''){
        $('#re_schedule').modal('hide');
          $('#show_final_schedule_result').html('');
        $.ajax({
            url     : 'index.php?route=ebay_map/ebay_schedule_product/reScheduleProduct&token=<?php echo $token; ?>',
            type    :   "POST",
            dataType:   "json",
            data    : {
                  product_ids :product_id,
                  scheduling_date :scheduling_date,
                  scheduling_time :scheduling_time
                      },
                      beforeSend: function() {
                        $('.block_div').css('display','block');
                      },
                      complete:function() {
                          $('.block_div').css('display','none');
                      },
            success: function(responseText) {

              $('#show_final_schedule_result').html(responseText);
              $('#show_schedule_result').modal('show');
            }
          });
      }else{
        $('.error').addClass('alert alert-danger');
 $('.error').html('Please choose schedule date and time!!');
      }
  }else{
      alert('Warning: You have to select atleast one record to Re-Scheduled!');
  }


});



</script>
