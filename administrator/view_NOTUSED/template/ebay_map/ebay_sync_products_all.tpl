<style>
  thead {
      background-color: green;
      color: white;
    }

  .block_div{
    background-color: #000;
    height: 100%;
    left: 0;
    opacity: 0.5;
    position: absolute;
    top: 0;
    width: 100%;
    z-index: 99;
    display: none;
  }
  .block_spinner {
    left: 50%;
    position: relative;
    top: 35%;
  }
  .tabs-left > .li-format{
    margin:12px 0;
    margin-right: -18px;
    border-left: 3px solid #1978ab;
    float: none;
  }
  .tabs-left > .li-format > a{
    border-radius: 0;
    border-top: 1px solid #e8e8e8;
    border-bottom: 1px solid #e8e8e8;
  }
  .tabs-left > li.active{
    border-left: 3px solid #E22C5C;
  }
    .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus{
    border-bottom: 1px solid #e8e8e8;
    border-right: none;
  }
</style>

<form name="myForm" action="<?php echo $action ?>" method="post" enctype="multipart/form-data" id="form-ebay_sync_products_all">
  <div id="tab-all-data">
    <div class="row">
      <div class="col-sm-12" style="margin-bottom: 15px;">
        <div class="form-group">
          <button type="button" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger pull-right" onclick="confirm('<?php echo $text_confirm; ?>') ? deleteMapping() : false;"><i class="fa fa-trash-o"></i></button>
        </div>
      </div>
    </div>
    <!-- start filter data -->
    <div class="well">
      <div class="row">
        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label" for="input-oc-source-product-id"><?php echo $column_ebay_source_product_id ?></label>
              <input type="text" name="filter_ebay_source_product_id" value="<?php echo $filter_ebay_source_product_id ?>" placeholder="<?php echo $column_ebay_source_product_id ?>" id="input-oc-source-product-id" class="form-control"/>
          </div>
        </div>

        <div class="col-sm-4">
          <div class="form-group">
            <label class="control-label" for="input-oc-product-id"><?php echo $column_oc_source_product_id ?></label>
              <input type="text" name="filter_oc_source_product_id" value="<?php echo $filter_oc_product_id ?>" placeholder="<?php echo $column_oc_source_product_id ?>" id="input-oc-product-id" class="form-control"/>
          </div>
        </div>

        <div class="col-sm-4">
            <div class="form-group">
              <label class="control-label" for="input-oc-product-name"><?php echo $column_product_name ?></label>
              <div class='input-group'>
                <input type="text" name="filter_oc_product_name" value="<?php echo $filter_oc_product_name ?>" placeholder="<?php echo $column_product_name ?>" id="input-oc-product-name" class="form-control"/>
                <span class="input-group-addon">
                  <span class="fa fa-angle-double-down"></span>
                </span>
              </div>
            </div>
        </div>

        <div class="col-sm-offset-8 col-sm-4">
          <div style="margin-top:38px;">
            <button type="button" class="btn btn-primary filter_search" id="filter_search" style="border-radius:0px;">
              <i class="fa fa-search"></i><?php echo $button_filter_product ?></button>
            <a href="<?php echo $clear_product_filter ?>" class="btn btn-default pull-right" style="border-radius:0px;"><i class="fa fa-eraser" aria-hidden="true"></i><?php echo $button_clear_product ?></a>
          </div>
        </div>

      </div> <!-- end row -->
    </div>

    <!-- filter data -->
    <div class="table-responsive">
      <table id="stores" class="table table-striped table-bordered table-hover">
        <thead>
          <tr>
            <td>
              <input type="checkbox" onclick="$('input[name*=\'selected\']').prop('checked', this.checked);" >
            </td>
            <td class="text-left"><?php echo $column_source_product ?></td>
            <td class="text-left"><?php echo $column_destination_product ?></td>
            <td><?php echo $column_action ?></td>
          </tr>
        </thead>
        <tbody>
            <?php $attribute_row = 0 ?>
            <?php foreach($product_attributes as $product_attribute) { ?>
            <tr id="store-row<?php echo $attribute_row ?>">
              <td class="text-left" style="width: 40%;">
                <input type="text" name="store_name["' + store_id + '"]" value="<?php echo $store_id ?>" placeholder="<?php echo $store_name ?>" class="form-control" />
              </td>
              <td class="text-left">
                <input type="text" name="store_product[]" value="<?php echo $product_id ?>" data-id="' + product_id + '" placeholder="<?php echo $text_select ?>" class="form-control" />
              </td>
              <td class="text-right"><button type="button" onclick="$('#store-row<?php echo $attribute_row ?>').remove();" data-toggle="tooltip" title="<?php echo $button_remove ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
              </td>
              <td class="text-left" style="width: 40%;">
                <input type="button" name="store_name["' + store_id + '"]" value="<?php echo $store_id ?>" placeholder="<?php echo $store_name ?>" class="form-control" />
              </td>
            </tr>
          <?php } ?>
          </tbody>
        <tfoot>
          <tr>

          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</form>

<script>
  $(document).ready(function (){
    let get_Token = '<?php echo $token ?>';
    let attribute_row = <?php echo $attribute_row ?>;

    let footer = (json) => {
      let html = '<td style="padding: 10px;" colspan="3">' +
        '<div class="col-sm-6 text-left">' +
          json['pagination'] +
        '</div>' +
        '<div class="col-sm-6 text-right">' +
          json['results'] +
        '</div>' +
      '</td>';
      $('#tab-all-data tfoot').html(html);
    }

    function createRow(page, url = '', button_press_id = 'button-save') {
      $.ajax({
        url: 'index.php?route=ebay_map/ebay_sync_products/getAllSyncProductsNow&token='+ get_Token + url,
        type: 'post',
        data: {
          'page': page
        },
        beforeSend: function() {
          if($('.alert-success').length) {
            $('.alert-success').slideUp(5000);
          }
          $('.alert-danger').remove();
          $('.block_div').css('display','block');
          $('#' + button_press_id).button('loading');
        },
        complete: function() {
          $('.block_div').css('display','none');
          $('#' + button_press_id).button('reset');
        },
        success: function(json) {
          addRow(json);
          footer(json);
        }
      });
    }
    if(!page) {
      page = 1;
    }
    createRow(page);

    $(document).on('click', '.editSyncProducts', function() {
      let url = link;

      if($(this).data('id')) {
        url += '&source_product_id=' + $(this).data('id');
      }
      location = url;
    })

    function addRow(json) {
      let html = '';

      let returnTrue = false;
      // console.log(attribute_row , 'attribute_row');
      $.map(json, function (value, key) {
        if(key == 'product_names' | key == 'pagination') {
          returnTrue = true;
        }
        if(returnTrue) {
          return true;
        }

        html += '<tr id="store-row' + attribute_row + '">' +
          '<td class="text-left">' +
            '<input type="checkbox" name="selected[]" value="' + value['source_product'] + '" />' +
          '</td>' +
          '<td class="text-left">' +
            '<span name="source_product" data-id="' + value['source_product'] + '">' + json['product_names'][value['source_product']] + '</span>' +
          '</td>' +
          '<td class="text-left">';
        let destination_products;
        let string = value['destination_products'];
        if (string.indexOf(',') > -1) {
          destination_products = string.split(',');
          $.map(destination_products, function (val){
            console.log(val);
            html += '<span name="destination_product[]" data-id="' + val + '"><i class="fa fa-check"></i> ' + json['product_names'][val] + '</span><br/>';
          })
        } else {
          destination_products = string;
          html += '<span name="destination_product[]" data-id="' + destination_products + '"><i class="fa fa-check"></i> ' + json['product_names'][destination_products] + '</span>';
        }
        html += '</td>' +
            '<td class="text-right"><button type="button" data-id="' + value['source_product'] + '" data-toggle="tooltip" title="<?php echo $button_edit ?>" class="btn btn-primary editSyncProducts"><i class="fa fa-pencil"></i></button>' +
          '</td>' +
        '</tr>';
        attribute_row++;
      })

      $('#tab-all-data tbody').html(html);
    }

    function filter_map_product() {
      url = '';

      var filter_oc_source_product_id = $('input[name=\'filter_oc_source_product_id\']').val();

      if (filter_oc_source_product_id) {
        url += '&filter_oc_source_product_id=' + encodeURIComponent(filter_oc_source_product_id);
      }

      var filter_oc_destination_product_id = $('input[name=\'filter_oc_destination_product_id\']').val();

      if (filter_oc_destination_product_id) {
        url += '&filter_oc_destination_product_id=' + encodeURIComponent(filter_oc_destination_product_id);
      }

      var filter_ebay_source_product_id = $('input[name=\'filter_ebay_source_product_id\']').val();

      if (filter_ebay_source_product_id) {
        url += '&filter_ebay_source_product_id=' + encodeURIComponent(filter_ebay_source_product_id);
      }

      var filter_ebay_destination_product_id = $('input[name=\'filter_ebay_destination_product_id\']').val();

      if (filter_ebay_destination_product_id) {
        url += '&filter_ebay_destination_product_id=' + encodeURIComponent(filter_ebay_destination_product_id);
      }

      var filter_oc_product_name = $('input[name=\'filter_oc_product_name\']').val();

      if (filter_oc_product_name) {
        url += '&filter_oc_product_name=' + encodeURIComponent(filter_oc_product_name);
      }

      createRow(page, url, button_press_id = 'filter_search');
      return;
    }

    $('.filter_search').on('click', function() {
      filter_map_product();
    })

    $('input[name=\'filter_oc_product_name\']').autocomplete({
      delay: 0,
      source: function(request, response) {
        $.ajax({
          url: 'index.php?route=ebay_map/ebay_sync_products/getAllSyncProductsNow&token=<?php echo $token ?>&filter_oc_product_name=' + encodeURIComponent(request),
          dataType: 'json',
          success: function(json) {
            response($.map(json['source_product_names'], function(item) {
              return {
                label: item.name,
                value: item.item_id
              }
            }));
          }
        });
      },
      select: function(item) {
        $('input[name=\'filter_oc_product_name\']').val(item.label);
        return false;
      },
      focus: function(item) {
          return false;
      }
    });

  });

  function deleteMapping() {
    if($('input[name*=\'selected\']:checked').length) {
      let action = 'index.php?route=ebay_map/ebay_sync_products/delete&token=<?php echo $token; ?>';
      $('[name=\'myForm\']').attr('action', action).submit();
    } else {
        alert('<?php echo $text_please_select; ?>')
    }
  }
</script>
