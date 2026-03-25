<div id="content">
<style>
  #form-export-product-ebay .dropdown-item{
    display: block;
    width: 100%;
    padding: .25rem 1.5rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    white-space: nowrap;
    background: 0 0;
    border: 0;
  }
  #form-export-product-ebay .table-responsive{
    overflow-x: visible;
  }
</style>
  <div class="page-header">
    <div class="container-fluid">
    <h3><?php echo $heading_title; ?></h3>
      <div class="pull-right" style="margin-bottom: 10px;">
        <button type="button" class="btn btn-primary" id="button-export-csv-xls" onclick="$('input[name=\'export_csv_xls\']').click();"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Using XLS/CSV</button>

        <input type="file" name="export_csv_xls" id="export-csv-xls" style="display:none;" accept=".csv,.xls">
        <button type="button" id="export-ebay-product" data-toggle="tooltip" data-token="<?php echo $token; ?>" data-account="<?php echo $account_id; ?>" title="<?php echo $button_export_to_ebay; ?>" class="btn btn-warning"><i class="fa fa-upload" aria-hidden="true"></i> <?php echo $button_export_to_ebay; ?></button>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#export_result">
          <i class="fa fa-info-circle" aria-hidden="true"></i> <?php echo "Show Result"; ?>
        </button>
      </div>
    </div>
  </div>
  <!-- Button trigger modal -->


      <!-- Modal -->
      <div class="modal fade" id="export_result" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel"><?php echo $text_export_result; ?></h4>
            </div>
            <div class="modal-body" id="sync_result" style="overflow-y: scroll;max-height: 350px">
              <?php if(isset($product_export_result['success']) && $product_export_result['success']){
                foreach($product_export_result['success'] as $product_export_success){
                  if(isset($product_export_success['success_message'])){ ?>
                  <div class="alert alert-success"> <?php echo $product_export_success['success_message']; ?></div>
                <?php } } ?>
              <?php } ?>
              <?php if(isset($product_export_result['error']) && $product_export_result['error']){
                foreach($product_export_result['error'] as $product_export_error){
                  if(isset($product_export_error['error_message'])){?>
                  <div class="alert alert-danger"> <?php echo $product_export_error['error_message']; ?></div>
                <?php } } ?>
              <?php } ?>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>

  <div class="container-fluid" id="product_export_list_section">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
      <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
      <div class="panel-heading"  style="display:inline-block;width:100%;">
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $text_oc_product_list; ?></h3>
      </div>
      <div class="panel-body">
        <div class="form-horizontal text-right">
          <div class="col-sm-12 form-group">
            <label class="col-sm-2 control-label"><?php echo $text_processing; ?></label>
            <div class="col-sm-10" style="margin-top:10px">
              <div class="progress">
                <div id="progress-bar" class="progress-bar" style="width: 0%;"></div>
              </div>
              <div id="progress-text"></div>
            </div>
          </div>
        </div>

          <div class="well">
            <div class="row">
              <div class="col-sm-4">
                <div class="form-group">
                  <label class="control-label" for="input-oc-product-id"><?php echo $column_oc_product_id; ?></label>
                    <input type="text" name="filter_oc_prod_id" value="<?php echo $filter_oc_prod_id; ?>" placeholder="<?php echo $column_oc_product_id; ?>" id="input-oc-product-id" class="form-control"/>
                </div>

                <div class="form-group">
                  <label class="control-label" for="input-oc-price"><?php echo $column_price; ?></label>
                    <input type="text" name="filter_price" value="<?php echo $filter_price; ?>" placeholder="<?php echo $column_price; ?>" id="input-oc-price" class="form-control"/>
                </div>
              </div>

              <div class="col-sm-4">
                  <div class="form-group">
                    <label class="control-label" for="input-oc-product-name"><?php echo $column_name; ?></label>
                    <div class='input-group'>
                      <input type="text" name="filter_oc_prod_name" value="<?php echo $filter_oc_prod_name; ?>" placeholder="<?php echo $column_name; ?>" id="input-oc-product-name" class="form-control"/>
                      <span class="input-group-addon">
                        <span class="fa fa-angle-double-down"></span>
                      </span>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="control-label" for="input-oc-quantity"><?php echo $column_quantity; ?></label>
                      <input type="text" name="filter_quantity" value="<?php echo $filter_quantity; ?>" placeholder="<?php echo $column_quantity; ?>" id="input-oc-quantity" class="form-control"/>
                  </div>
              </div>

              <div class="col-sm-4">
                <div class="form-group">
                  <label class="control-label" for="input-oc-category-name"><?php echo $column_category_name; ?></label>
                  <div class='input-group'>
                    <input type="text" name="filter_oc_cat_name" value="<?php echo $filter_oc_cat_name; ?>" placeholder="<?php echo $column_category_name; ?>" id="input-oc-category-name" class="form-control"/>
                    <span class="input-group-addon">
                      <span class="fa fa-angle-double-down"></span>
                    </span>
                  </div>
                </div>

                <div style="margin-top:38px;">
                  <button type="button" onclick="filter_export_product();" class="btn btn-primary" style="border-radius:0px;">
                    <i class="fa fa-search"></i><?php echo $button_filter_product; ?></button>
                  <a href="<?php echo $clear_export_filter; ?>" class="btn btn-default pull-right" style="border-radius:0px;"><i class="fa fa-eraser" aria-hidden="true"></i><?php echo $button_clear_product; ?></a>
                </div>
              </div>

            </div>
          </div>
        <form method="post" enctype="multipart/form-data" id="form-export-product-ebay">
          <div class="table-responsive">
            <table class="table table-bordered table-hover">
              <thead>
                <tr>
                  <td style="width: 1px;" class="text-center"><input type="checkbox" onclick="$('#product_export_list_section input[name*=\'selected\']').prop('checked', this.checked);" /></td>

                  <td class="text-left"><?php echo $column_product_id; ?></td>
                  <td class="text-left"><?php echo $column_name; ?></td>
                  <td class="text-left"><?php echo $column_category_name; ?></td>
                  <td class="text-left"><?php echo $column_price; ?></td>
                  <td class="text-left"><?php echo $column_quantity; ?></td>
                </tr>
              </thead>
              <tbody>
                <?php if ($oc_products) { ?>
                <?php foreach ($oc_products as $product) { ?>
                <tr>
                  <td class="text-center"><?php if (in_array($product['product_id'], $selected)) { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" checked="checked" />
                    <?php } else { ?>
                    <input type="checkbox" name="selected[]" value="<?php echo $product['product_id']; ?>" />
                    <?php } ?></td>

                  <td class="text-left"><?php echo $product['product_id']; ?></td>
                  <td class="text-left"><?php echo $product['name']; ?></td>
                  <td class="text-left">
                    <?php if(isset($product['category']) && $product['category']){ ?>
                      <div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Categories</button>
                        <div class="dropdown-menu">
                          <?php foreach ($product['category'] as $category) { ?>
                            <p class="dropdown-item"><?php echo $category['name']; ?></p>
                          <?php } ?>
                        </div>
                      </div>
                    <?php }else{ ?>
                      N/A
                    <?php } ?>
                  </td>
                  <td class="text-left"><?php echo $product['price']; ?></td>
                  <td class="text-left"><?php echo $product['quantity']; ?></td>
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
function filter_export_product() {
	url = 'index.php?route=ebay_map/ebay_account/edit&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&status=account_import_to_ebay';

  var filter_oc_prod_id = $('input[name=\'filter_oc_prod_id\']').val();

  if (filter_oc_prod_id) {
    url += '&filter_oc_prod_id=' + encodeURIComponent(filter_oc_prod_id);
  }

	var filter_oc_prod_name = $('input[name=\'filter_oc_prod_name\']').val();

	if (filter_oc_prod_name) {
		url += '&filter_oc_prod_name=' + encodeURIComponent(filter_oc_prod_name);
	}

	var filter_oc_cat_name = $('input[name=\'filter_oc_cat_name\']').val();

	if (filter_oc_cat_name) {
		url += '&filter_oc_cat_name=' + encodeURIComponent(filter_oc_cat_name);
	}

  var filter_price = $('input[name=\'filter_price\']').val();

  if (filter_price) {
    url += '&filter_price=' + encodeURIComponent(filter_price);
  }

  var filter_quantity = $('input[name=\'filter_quantity\']').val();

  if (filter_quantity) {
    url += '&filter_quantity=' + encodeURIComponent(filter_quantity);
  }

	location = url;
}

$('input[name=\'filter_oc_cat_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/export_product_to_ebay/autocomplete&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&filter_oc_cat_name=' +  encodeURIComponent(request),
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
    $('input[name=\'filter_oc_cat_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

$('input[name=\'filter_oc_prod_name\']').autocomplete({
  delay: 0,
  source: function(request, response) {
    $.ajax({
      url: 'index.php?route=ebay_map/export_product_to_ebay/autocomplete&token=<?php echo $token; ?>&account_id=<?php echo $account_id; ?>&filter_oc_prod_name=' +  encodeURIComponent(request),
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
    $('input[name=\'filter_oc_prod_name\']').val(item.label);
    return false;
  },
  focus: function(item) {
      return false;
  }
});

//--></script>
<script type="text/javascript">
  document.querySelector('#export-csv-xls').addEventListener('change', function(e) {
    var file = this.files[0];
    if (file.type == 'text/csv' || file.type == 'application/vnd.ms-excel') {
      console.log('File type allowed!');
    } else {
      window.alert('File type not allowed!');
      return false;
    }

    var form_data = new FormData();
    form_data.append(this.name, file);
    // These extra params aren't necessary but show that you can include other data.
    // form_data.append("username", "Groucho");
    // form_data.append("accountnum", 123456);
    var controller_url = 'index.php?route=ebay_map/export_product_to_ebay/start_syncronize&user_token={{ user_token }}&account_id={{ account_id }}';

    var xhr = new XMLHttpRequest();
    xhr.open('POST', controller_url, true);

    xhr.upload.onprogress = function(e) {
      if (e.lengthComputable) {
        var percentComplete = (e.loaded / e.total) * 100;
        var progress_bar = document.querySelector('#product_export_list_section #progress-bar');
        var progress_text = document.querySelector('#product_export_list_section #progress-text')
        progress_bar.style.width = percentComplete + '%';
        progress_text.innerText = percentComplete + '% completed';
        console.log(percentComplete + '% uploaded');
      }
    };
    xhr.onload = function() {
      if (this.status == 200) {
        var resp = JSON.parse(this.response);
        console.log('Server got:', resp);
      };
    };
    xhr.send(form_data);
  }, false);
</script>
<script type="text/javascript">
    var step          = new Array();
    var total         = 0;
    var status        = false;
    var count_success = false;
    $('body').on('click','#export-ebay-product',function(e){
      e.preventDefault();

      $('#form-export-product-ebay input:checkbox').each(function(key, val){
        if($(val).prop('checked')){
          status = true;
        }
      })

      if(status == 'true' || status == true){
        $('#sync_result > .alert').remove();
        var get_Token   = $(this).data('token');
        var get_Account = $(this).data('account');
        var data        = new FormData($('#form-export-product-ebay')[0]);
          if (typeof timer != 'undefined') {
              clearInterval(timer);
          }
          timer = setInterval(function() {
            clearInterval(timer);
            // Reset everything
            $('.alert').remove();
            $('#product_export_list_section  #progress-bar').css('width', '0%');
            $('#product_export_list_section  #progress-bar').removeClass('progress-bar-danger progress-bar-success');
            $('#product_export_list_section  #progress-text').html('');

            $.ajax({
              url: 'index.php?route=ebay_map/export_product_to_ebay/export_product&token='+get_Token+'&account_id='+get_Account,
              type: 'post',
              data: data,
                contentType: false,
                cache: false,
                processData:false,
              beforeSend: function() {
                $('.alert').remove();
                $('.block_div').css('display','block');
              },
              success: function(json) {
                if (json['error']) {
                  $('#product_export_list_section  #progress-bar').addClass('progress-bar-danger');
                  $('#product_export_list_section  #progress-text').html('<div class="text-danger">' + json['error'] + '</div>');
                }
                if (json['step']) {
                  step = json['step'];
                  total = step.length;
                    next();
                }
                if (json['redirect']){
                  location = json['redirect'];
                }
              },
              error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
              }
            });
        }, 500);
      }else{
        alert('Warning: You have to select atleast one record to export!');
      }
  });


function next() {
  data = step.shift();

  if (data) {
    $('#product_export_list_section  #progress-bar').css('width', (100 - (step.length / total) * 100) + '%');
    $('#product_export_list_section  #progress-text').html('<span class="text-info pull-left">' + data['text'] + '</span>');

    $.ajax({
      url: data.url,
      type: 'post',
      dataType: 'json',
      data: 'product_id=' + data.process_data,
      success: function(json) {
          if (json.error) {
            $('#product_export_list_section  #progress-bar').addClass('progress-bar-danger');
            var html = '';
            for (i in json.error) {
              if(json.error[i]['error_status']){
                html += '<div class="alert alert-danger">'+json.error[i]['error_message']+'</div>';
              }
            }
            $('#sync_result').append(html);
          }

          if (json.success) {
            if(!json.error){
              $('#product_export_list_section  #progress-bar').addClass('progress-bar-success');
            }
            html1 = '';
            count_success = true;
            for (i in json.success) {
              if(json.success[i]['success_message']){
                html1 += '<div class="alert alert-success">'+json.success[i]['success_message']+'</div>';
              }
            }
            $('#sync_result').append(html1);
          }

          setTimeout(function(){
            next();
          },2000)
      },
      error: function(xhr, ajaxOptions, thrownError) {
        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
      }
    });
  }else{
    if(count_success){
        $('#product_export_list_section').prepend('<div class="alert alert-success"> <?php echo $text_export_success; ?> </div>');
        $('#product_export_list_section #progress-bar').addClass('progress-bar-success');
        $('#product_export_list_section #progress-text').html('<span class="text-success"><?php echo $text_success_ebay_export; ?></span>');
        var redirect = '<?php echo $redirect; ?>';
        if(redirect){
          window.location.href = redirect;
        }
    }else{
        $('#product_export_list_section').prepend('<div class="alert alert-danger"> <?php echo $text_export_warning; ?> </div>');
        $('#product_export_list_section #progress-bar').addClass('progress-bar-danger');
        $('#product_export_list_section #progress-text').html('');
    }
    $('.block_div').css('display','none');
  }
}
</script>
