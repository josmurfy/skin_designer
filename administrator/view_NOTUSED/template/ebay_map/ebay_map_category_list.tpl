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

  #new_category_map_section{
    display: none;
  }
  .alert-success{
    background-color: #8cc152;
    border-color: #8cc152;
    color: #fff;
    font-size: 16px;
    font-weight: 600;
  }
  .alert-danger{
    background-color: #ea5744;
    border-color: #ea5744;
    color: #ffffff;
    font-size: 16px;
    font-weight: 600;
  }
</style>
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="button" id="show_new_map_category" data-toggle="tooltip" title="<?php echo $button_map_new_category; ?>" class="btn btn-warning"><i class="fa fa-compress" aria-hidden="true"></i> <?php echo $button_map_new_category; ?></button>
        <button id="map_category_delete" type="button" data-toggle="tooltip" data-token="<?php echo $token; ?>" title="<?php echo $button_delete; ?>" data-account-id="<?php echo $account_id; ?>" class="btn btn-danger" ><i class="fa fa-trash-o"></i></button>
      </div>
      <h3><?php echo $heading_title; ?></h3>
      <hr>
    </div>
  </div>

  <div class="container-fluid" id="new_category_map_section">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_category_form; ?></h3>
      </div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data" id="form-category-save" class="form-horizontal">
          <div class="container-fluid">
            <div class="pull-right">
              <button type="button" id="wk_cat_map_save" data-toggle="tooltip" data-token="<?php echo $token; ?>" data-account-id="<?php echo $account_id; ?>" title="<?php echo $button_map_category; ?>" class="btn btn-warning"><i class="fa fa-save"></i> <?php echo $button_map_category; ?></button>
            </div>
          </div>
          <input type="hidden" name="account_id" value="<?php if(isset($account_id) && $account_id){ echo $account_id; } ?>">

          <div class="form-group required">
            <label class="col-sm-2 control-label" style="margin-top: 20px;"><?php echo $entry_choose_opencart_entry; ?></label>
            <div class="col-sm-10" id="map_opencart_category" style="display: inline;">

              <select id="opencart_category_map" class="form-control opencart_category" required name="opencart_category[]" style="width: auto;display: inline-block;margin-top: 25px;">
                  <option value=""><?php echo $text_select_opencart_category; ?></option>
                  <?php if(isset($opencart_categories) && $opencart_categories){ ?>
                    <?php foreach ($opencart_categories as $key => $opencart_category) { ?>
                      <option value="<?php echo $opencart_category['category_id']; ?>"><?php echo $opencart_category['name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
                <?php if($error_opencart_category){ ?>
                    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_opencart_category; ?></div>
                <?php } ?>
            </div>
          </div>

          <div class="form-group required">
            <label class="col-sm-2 control-label" style="margin-top: 20px;"><?php echo $entry_choose_ebay_entry; ?></label>
            <div class="col-sm-10" id="map_ebay_category" required style="display: inline;">

                <select class="form-control ebay_category" name="ebay_category[]" style="width: auto;display: inline-block;margin-top: 25px;">
                  <option value=""><?php echo $text_select_ebay_category; ?></option>
                  <?php if(isset($ebay_categories) && $ebay_categories){ ?>
                    <?php foreach ($ebay_categories as $key => $ebay_category) { ?>
                      <option value="<?php echo $ebay_category['ebay_category_id']; ?>" data-store-id="<?php echo $ebay_category['ebay_site_id']; ?>"><?php echo $ebay_category['ebay_category_name']; ?></option>
                    <?php } ?>
                  <?php } ?>
                </select>
                <?php if($error_ebay_category){ ?>
                    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_ebay_category; ?></div>
                <?php } ?>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>


  <div class="container-fluid" id="category_map_list_section">
    <div class="panel panel-default">
      <div class="panel-heading"  style="display:inline-block;width:100%;">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_category_list; ?></h3>
      </div>
      <div class="panel-body">

        <div class="well">
          <div class="row">
            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-oc-category-id"><?php echo $column_opencart_category_id; ?></label>
                  <input type="text" name="filter_oc_category_id" value="<?php echo $filter_oc_category_id; ?>" placeholder="<?php echo $column_opencart_category_id; ?>" id="input-oc-category-id" class="form-control"/>
              </div>

              <div class="form-group">
                <label class="control-label" for="input-ebay-category-id"><?php echo $column_ebay_category_id; ?></label>
                  <input type="text" name="filter_ebay_category_id" value="<?php echo $filter_ebay_category_id; ?>" placeholder="<?php echo $column_ebay_category_id; ?>" id="input-ebay-category-id" class="form-control"/>
              </div>
            </div>

            <div class="col-sm-4">
                <div class="form-group">
                  <label class="control-label" for="input-oc-category-name"><?php echo $column_opencart_category_name; ?></label>
                  <div class='input-group'>
                    <input type="text" name="filter_oc_category_name" value="<?php echo $filter_oc_category_name; ?>" placeholder="<?php echo $column_opencart_category_name; ?>" id="input-oc-category-name" class="form-control"/>
                    <span class="input-group-addon">
                      <span class="fa fa-angle-double-down"></span>
                    </span>
                  </div>
                </div>

                <div class="form-group">
                  <label class="control-label" for="input-ebay-category-name"><?php echo $column_ebay_category_name; ?></label>
                  <div class='input-group'>
                    <input type="text" name="filter_ebay_category_name" value="<?php echo $filter_ebay_category_name; ?>" placeholder="<?php echo $column_ebay_category_name; ?>" id="input-ebay-category-name" class="form-control"/>
                    <span class="input-group-addon">
                      <span class="fa fa-angle-double-down"></span>
                    </span>
                  </div>
                </div>
            </div>

            <div class="col-sm-4">
              <div class="form-group">
                <label class="control-label" for="input-quantity"><?php echo $column_variation; ?></label>

                <select name="filter_variation_type" class="form-control">
                  <option value="*"><?php echo $entry_variation_type; ?></option>
                  <?php if($filter_variation_type) { ?>
                    <option value="1" selected="selected">1</option>
                  <?php }else{ ?>
                    <option value="1">1</option>
                  <?php } ?>
                  <?php if(!$filter_variation_type){ ?>
                    <option value="0" selected="selected">0</option>
                  <?php }else{ ?>
                    <option value="0">0</option>
                  <?php } ?>
                </select>
              </div>

              <div style="margin-top:38px;">
                <button type="button" onclick="filter_map_category();" class="btn btn-primary" style="border-radius:0px;">
                  <i class="fa fa-search"></i><?php echo $button_filter_category; ?></button>
                <a href="<?php echo $clear_category_filter; ?>" class="btn btn-default pull-right" style="border-radius:0px;"><i class="fa fa-eraser" aria-hidden="true"></i><?php echo $button_clear_category; ?></a>
              </div>
            </div>

          </div>
        </div>

        <form method="post" enctype="multipart/form-data" id="form-category-delete">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" /></td>

                  <td class="text-left"><?php echo $column_map_id; ?></td>
                  <td class="text-left"><?php echo $column_opencart_category_name; ?></td>
                  <td class="text-left"><?php echo $column_ebay_category_id; ?></td>
                  <td class="text-right"><?php echo $column_ebay_category_name; ?></td>
                  <td class="text-right"><?php echo $column_condition_attribute; ?></td>
                  <td class="text-right"><?php echo $column_variation; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($map_categories) { ?>
                <?php foreach ($map_categories as $map_category) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($map_category['map_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $map_category['map_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $map_category['map_id']; ?>" />
                    <?php } ?></td>
                  <td class="text-left"><?php echo $map_category['map_id']; ?></td>
                  <td class="text-left"><?php echo $map_category['oc_cat_name']; ?></td>
                  <td class="text-left text-info"><?php echo $map_category['ebay_cat_id']; ?></td>
                  <td class="text-right"><?php echo $map_category['ebay_cat_name']; ?></td>
                  <td class="text-right"><?php echo $map_category['ebay_condition_attr']; ?></td>
                  <td class="text-right"><?php echo $map_category['ebay_varitions']; ?></td>
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
function filter_map_category() {
	url = 'index.php?route=ebay_map/ebay_account/edit&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&status=account_category_map';

  var filter_oc_category_id = $('input[name=\'filter_oc_category_id\']').val();

  if (filter_oc_category_id) {
    url += '&filter_oc_category_id=' + encodeURIComponent(filter_oc_category_id);
  }

	var filter_oc_category_name = $('input[name=\'filter_oc_category_name\']').val();

	if (filter_oc_category_name) {
		url += '&filter_oc_category_name=' + encodeURIComponent(filter_oc_category_name);
	}

  var filter_ebay_category_id = $('input[name=\'filter_ebay_category_id\']').val();

  if (filter_ebay_category_id) {
    url += '&filter_ebay_category_id=' + encodeURIComponent(filter_ebay_category_id);
  }

	var filter_ebay_category_name = $('input[name=\'filter_ebay_category_name\']').val();

	if (filter_ebay_category_name) {
		url += '&filter_ebay_category_name=' + encodeURIComponent(filter_ebay_category_name);
	}

  var filter_variation_type = $('select[name=\'filter_variation_type\']').val();

	if (filter_variation_type != '*') {
		url += '&filter_variation_type=' + encodeURIComponent(filter_variation_type);
	}

	location = url;
}

$('input[name=\'filter_oc_category_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_map_category/autocomplete&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&filter_oc_category_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.category_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_oc_category_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

$('input[name=\'filter_ebay_category_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/ebay_map_category/autocomplete&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&filter_ebay_category_name=' +  encodeURIComponent(request),
      dataType: 'json',
      success: function(json) {
        response($.map(json, function(item) {
          return {
            label: item.name,
            value: item.category_id
          }
        }));
      }
    });
  },
  select: function(item) {
    $('input[name=\'filter_ebay_category_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

//--></script>
<script type="text/javascript">
  var count = 1;
  var ebay_count = 1;
  $('body').on('change', '.opencart_category', function(){
    var select_list_element = $(this);
    var get_CategoryId      = $(this).val();

      if(get_CategoryId != ''){
        $.ajax({
          url: 'index.php?route=ebay_map/ebay_map_category/getOcChildCategories&token=<?php echo $token; ?>',
          data: 'parent_category_id=' + get_CategoryId,
          type: 'post',
          dataType: 'json',
          beforeSend: function() {
            $('#form-category-save > .alert').remove();
            $(select_list_element).nextAll().remove();
            $(select_list_element).after('<div class="cp-spinner cp-round"></div>');
          },
          success: function(json) {
            if(json.length != 0){
              $(select_list_element).css('border-color','#66afe9');
              html = '<select id="opencart_category_map_'+count+'" required name="opencart_category[]" class="form-control opencart_category" style="width: auto;display: inline-block;margin-top: 25px;">';
              html += '<option value=""><?php echo $entry_select_sub_category; ?></option>';
              for (var i = 0; i < json.length; i++) {
                html += '<option value="'+json[i].category_id+'">'+json[i].name+'</option>';
              }
              html += '</select>';
              setTimeout(function(){
                $('#map_opencart_category').append(html);
                $('#map_opencart_category').find('.cp-spinner').remove();
                count = count+1;
              },1500);
            }else{
              $(select_list_element).css('border-color','green');
              $('#map_opencart_category').find('.cp-spinner').remove();
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
      }
  })

  $('body').on('change', '.ebay_category', function(){
    var select_list_element = $(this);
    var ebay_site_id        = $('.ebay_category option:selected').data('store-id');
    var get_CategoryId      = $(this).val();

      if(get_CategoryId != ''){
        $.ajax({
          url: 'index.php?route=ebay_map/ebay_map_category/geteBayChildCategories&token=<?php echo $token; ?>&ebay_site_id='+ebay_site_id,
          data: 'parent_category_id=' + get_CategoryId,
          type: 'post',
          dataType: 'json',
          beforeSend: function() {
            $(select_list_element).nextAll().remove();
            $(select_list_element).after('<div class="cp-spinner cp-round"></div>');
          },
          success: function(json) {
            if(json.length != 0){
              $(select_list_element).css('border-color','#66afe9');
              html = '<select id="opencart_category_map_'+ebay_count+'" required name="ebay_category[]" class="form-control ebay_category" style="width: auto;display: inline-block;margin-top: 25px;">';
              html += '<option value=""><?php echo $entry_select_sub_category; ?></option>';
              for (var i = 0; i < json.length; i++) {
                html += '<option value="'+json[i].category_id+'">'+json[i].name+'</option>';
              }
              html += '</select>';
              setTimeout(function(){
                $('#map_ebay_category').append(html);
                $('#map_ebay_category').find('.cp-spinner').remove();
                ebay_count = ebay_count+1;
              },1500);
            }else{
              $(select_list_element).css('border-color','green');
              $('#map_ebay_category').find('.cp-spinner').remove();
            }
          },
          error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
          }
        });
      }
  })
</script>
