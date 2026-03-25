<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <style>
    #product_specification tfoot, #custom_description{
      display: none;
    }
    .alert.alert-default{
      border-radius: 0;
      font-size: 13px;
      line-height: 2.1;
      padding: 10px;
      border-color: #1d8449;
      color: #1d8449;
      background-color: #ecf3e6;
    }
    .alert.alert-default > span{
      font-size: 14px;
      font-weight: 600;
      text-decoration: underline;
    }
  </style>
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-ebay-template-info" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i> <?php echo $button_save; ?></button>
        <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a>
      </div>
      <h1><?php echo $text_add; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_add; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-ebay-template-info" class="form-horizontal">
            <ul class="nav nav-tabs tabs-left"><!-- 'tabs-right' for right tabs -->
              <li class="active li-format"><a href="#add-ebay-info" data-toggle="tab"><?php echo $text_template_info; ?></a></li>
              <li class="li-format"><a href="#add-ebay-general" data-toggle="tab"><?php echo $text_template_general; ?></a></li>
              <li class="li-format"><a href="#add-ebay-description" data-toggle="tab"><?php echo $text_template_description; ?></a></li>
            </ul>
            <div class="tab-content">

                <div class="tab-pane active" id="add-ebay-info">
                    <div class="alert alert-default">
                        <?php echo $inforamtion_tab_content; ?>
                    </div>

                    <div class="table-responsive" id="product_specification_info">
                      <div class="alert alert-info"><b>NOTE:-</b> The below mentioned keywords are not fixed we just provide them as example, you can use the keyword according to you need under the General Tab.</div>
                      <table class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <td class="text-left alert-danger" style="background-color: #e7393f;color: #fff;"><?php echo $entry_product_fields; ?></td>
                            <td class="text-left alert-danger" style="background-color: #e7393f;color: #fff;"><?php echo $entry_keyword_suggestion; ?></td>
                          </tr>
                        </thead>
                        <tbody>
                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_ebay_specification; ?></td>
                            <td class="text-left">These specification will based on the mapped categories of ebay sites. You can use multiple specification for single description. <br><i>You can use them like:  {product_specification_keyword},  #brand#,  --theme--,  $color$,  (material) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_ebay_condition; ?></td>
                            <td class="text-left">The ebay conditions are based on ebay category. If you assign any category to the opencart product, then you must have to choose the ebay condition for that product to export that product on ebay store. <br><i>You can use keyword like : {product_condition_keyword},  #prod_condition#,  --new with tags--,  $used$,  (new without tags) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_product_title; ?></td>
                            <td class="text-left">There are the opencart product's basic fields, by using these you can use them on ebay product description. You can add the product title in the product description by using the keyword.<br><i>{product_name},  #product_title#,  --title--,  $product_heading$,  (product_main_title) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_meta_title; ?></td>
                            <td class="text-left">You can also add the opencart product meta title to the ebay product description by createing the keyword for product meta title. <br><i>{product_meta_title},  #meta_title#,  --meta_title--,  $meta_title$,  (product_meta_title) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_model; ?></td>
                            <td class="text-left">Used opencart product model number to the ebay product description by createing the keyword for product model. <br><i>{product_model},  #product_model_number#,  --model_number--,  $product_model_number$,  (model_number) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_sku; ?></td>
                            <td class="text-left">If you have any sku with the opencart product, then you can use that to the product description by createing the keyword for product sku. <br><i>{product_sku},  #product_sku_number#,  --sku_code--,  $product_sku_number$,  (product_sku) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_location; ?></td>
                            <td class="text-left">If opencart product have any value for the location field, then by creating the keyword you can add that to the product description. <br><i>{product_location},  #location#,  --product_location--,  $product_location$,  (product_location) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_price; ?></td>
                            <td class="text-left">You can display the product price to the ebay product description according to the ebay store currency, by using the keyword in product description. <br><i>{product_price},  #price#,  --Amount--,  $product_price$,  (product_price) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_qty; ?></td>
                            <td class="text-left">You can display the product total available quantity in the ebay product description, by using the created keyword for product quantity in product description. <br><i>{product_qty},  #product_quantity#,  --Quantity--,  $product_inventory$,  (product_qty) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_date_available; ?></td>
                            <td class="text-left">To display the product available date on ebay store product, use the created keyword in product description.<br><i>{product_date_available},  #date_available#,  --date_available--,  $product_date_available$,  (available_date) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_weight; ?></td>
                            <td class="text-left">You can display the product weight with the converted unit. Product weight will convert in to the default config weight class id option.<br><i>{product_weight},  #weight#,  --product_weight--,  $product_weight$,  (product_weight) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_main_image; ?></td>
                            <td class="text-left">To display the opencart product main image under ebay product description, you have to use the keyword in opencart product description. The product main image's width and height will work according to the <b>Product Image Thumb Size (W x H)</b> image size option under the Extension->Themes Menu.<br><i>{main_image},  #product_main_image#,  --product_gallery_image--,  $gallery_image$,  (product_main_image) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_thumb_image; ?></td>
                            <td class="text-left">To display the opencart product related/thumbnails images under ebay product description, you have to use the keyword in opencart product description. The related/thumbnails image width and height will work according to the <b>Additional Product Image Size (W x H)</b> image size option under the Extension->Themes Menu.<br><i>{related_images},  #product_thumbnail_images#,  --thumbnail_images--,  $related_images$,  (product_other_images) etc...</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_thumb_image_number; ?></td>
                            <td class="text-left">You can use this option to restrict the product related/thumbnail images. Define the number of related image will display in ebay product description. <i>You don't have to mention this value in product description, this will work only for the Thumbnail Images option.<br> Only define the number for Thumbnail Images</i></td></tr>

                          <tr><td class="text-left alert-warning col-sm-4"><?php echo $entry_product_description; ?></td>
                            <td class="text-left">You can use either Product Description option or can create the custom Description for the product.</td></tr>
                        </tbody>
                      </table>
                    </div>
                </div>
                <div class="tab-pane" id="add-ebay-general">
                  <input type="hidden" name="template_id" value="<?php echo $template_id; ?>" />
                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-template-title"><?php echo $entry_template_title; ?></label>
                    <div class="col-sm-7">
                      <input type="text" name="template_title" id="input-template-title" value="<?php if(isset($template_title) && $template_title) { echo $template_title; } ?>" class="form-control" />
                      <?php if($error_template_title){ ?>
                        <div class="text-danger"><?php echo $error_template_title; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-sites"><?php echo $entry_ebay_sites; ?></label>
                    <div class="col-sm-7">
                      <select name="template_ebay_site" class="form-control" id="input-ebay-sites">
                        <?php foreach($ebaySites['ebay_sites'] as $site_id => $ebaySite){ ?>
                            <option value="<?php echo $site_id; ?>" <?php if(isset($template_ebay_site) && $template_ebay_site == $site_id){ echo 'selected'; } ?>><?php echo $ebaySite; ?></option>
                        <?php } ?>
                      </select>
                      <?php if($error_template_ebay_site){ ?>
                        <div class="text-danger"><?php echo $error_template_ebay_site; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group required">
                    <label class="col-sm-3 control-label" for="input-ebay-mapped-category"><span data-toggle="tooltip" data-original-title="<?php echo $info_mapped_category; ?>"><?php echo $entry_ebay_mapped_category; ?></span></label>
                    <div class="col-sm-7">
                      <select name="template_mapped_category" class="form-control" id="input-ebay-mapped-category">
                        <option value="0"><?php echo $text_select_category; ?></option>
                        <?php if(isset($ebay_categories)){ ?>
                        <?php foreach($ebay_categories as $key => $ebayCategory){ ?>
                            <option value="<?php echo $ebayCategory['ebay_category_id']; ?>" <?php if(isset($template_mapped_category) && $template_mapped_category == $ebayCategory['ebay_category_id']){ echo 'selected'; } ?> class="mapped_category"><?php echo $ebayCategory['ebay_category_name']; ?></option>
                        <?php } ?>
                        <?php } ?>
                      </select>
                      <?php if($error_template_mapped_category){ ?>
                        <div class="text-danger"><?php echo $error_template_mapped_category; ?></div>
                      <?php } ?>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-product-specification"><span data-toggle="tooltip" data-original-title="<?php echo $info_product_specification; ?>"><?php echo $entry_ebay_specification; ?></span></label>
                    <div class="col-sm-9">
                      <div class="table-responsive" id="product_specification">
                        <table class="table table-bordered table-hover">
                          <thead>
                            <tr>
                              <td class="text-center alert-info"><?php echo $entry_product_attribute_group; ?></td>
                              <td class="text-center alert-info"><?php echo $entry_create_placeholder; ?></td>
                              <td class="text-left alert-info"><?php echo $entry_action; ?></td>
                            </tr>
                          </thead>
                          <tbody>
                            <?php $specification_row = 0; ?>
                            <?php if(isset($template['specification'])){ ?>
                              <?php foreach ($template['specification'] as $key => $temp_specification) { ?>
                                <tr id="specification-row<?php echo $specification_row; ?>">
                                  <td class="text-center"><select name="template[specification][<?php echo $specification_row; ?>]" class="form-control">';
                                    <?php if(isset($ebay_specifications)){ ?>
                                      <?php foreach ($ebay_specifications as $specification) { ?>
                                        <option value="<?php echo $specification['attribute_group_id']; ?>" class="mapped_category" <?php if($temp_specification == $specification['attribute_group_id']){ echo 'selected'; } ?>><?php echo $specification['attr_group_name']; ?></option>
                                      <?php } ?>
                                    <?php } ?>
                                    </select>
                                    <?php if(isset($error_template_specification[$key]) && $error_template_specification[$key]){ ?>
                                      <div class="text-danger text-left"><?php echo $error_template_specification[$key]; ?></div>
                                    <?php } ?>
                                  </td>
                                  <td class="text-center">
                                    <input class="form-control" name="template[keyword][<?php echo $specification_row; ?>]" value="<?php if(isset($template['keyword'][$key])){ echo $template['keyword'][$key]; } ?>" />
                                    <?php if(isset($error_template_keyword[$key]) && $error_template_keyword[$key]){ ?>
                                      <div class="text-danger text-left"><?php echo $error_template_keyword[$key]; ?></div>
                                    <?php } ?>
                                  </td>
                                  <td class="text-left">
                                    <button type="button" onclick="$(\'#specification-row<?php echo $specification_row; ?>\').remove();" data-toggle="tooltip" title="<?php echo $button_remove_row; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button>
                                  </td>
                                </tr>
                                <?php $specification_row++; ?>
                              <?php } ?>
                            <?php }else{ ?>
                              <tr><td class="text-center alert-warning" colspan="3"><?php echo $text_no_record; ?></td></tr>
                            <?php } ?>
                          </tbody>
                          <tfoot style="display:<?php if(isset($template['specification']) && $template['specification']){echo 'table-footer-group';} ?>">
                            <tr>
                              <td colspan="2"></td>
                              <td class="text-left"><button type="button" onclick="addSpecification();" data-toggle="tooltip" title="<?php echo $button_specification_add; ?>" class="btn btn-primary"><i class="fa fa-plus-circle"></i></button></td>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-ebay-codition"><span data-toggle="tooltip" data-original-title="<?php echo $info_condition; ?>"><?php echo $entry_ebay_condition; ?></span></label>
                    <div class="col-sm-7">
                        <input name="template_condition" class="form-control" id="input-ebay-codition" value="<?php if(isset($template_condition) && $template_condition){echo $template_condition; } ?>" placeholder="<?php echo $placeholder_condition; ?>" />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-ebay-basic-detail"><span data-toggle="tooltip" data-original-title="<?php echo $info_basic_details; ?>"><?php echo $entry_basic_details; ?></span></label>
                    <div class="col-sm-8">
                      <div class="table-responsive" id="product_basic_detail">
                        <table class="table table-bordered table-hover">
                          <tbody>
                            <?php foreach($basicDetailsArray as $key_index => $language_text){ ?>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><?php echo $language_text; ?></th>
                                <td class="text-center"><input type="text" name="template_basicDetails[<?php echo $key_index; ?>]" placeholder="<?php echo $placeholder_keyword; ?>" value="<?php if(isset($template_basicDetails[$key_index]) && $template_basicDetails[$key_index]){echo $template_basicDetails[$key_index]; } ?>" class="form-control" /></td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-ebay-images"><span data-toggle="tooltip" data-original-title="<?php echo $info_images; ?>"><?php echo $entry_images; ?></span></label>
                    <div class="col-sm-8">
                      <div class="table-responsive" id="product_images">
                        <table class="table table-bordered table-hover">
                          <tbody>
                            <?php foreach($tempImages as $key_index => $language_text){ ?>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><?php echo $language_text; ?></th>
                                <td class="text-center">
                                  <?php if($key_index == 'thumb_number'){ ?>
                                      <input type="text" name="template_images[<?php echo $key_index; ?>]" placeholder="<?php echo $placeholder_thumb_number; ?>" value="<?php if(isset($template_images[$key_index]) && $template_images[$key_index]){echo $template_images[$key_index]; } ?>" class="form-control" />
                                  <?php }else{ ?>
                                      <input type="text" name="template_images[<?php echo $key_index; ?>]" placeholder="<?php echo $placeholder_keyword; ?>" value="<?php if(isset($template_images[$key_index]) && $template_images[$key_index]){echo $template_images[$key_index]; } ?>" class="form-control" />
                                  <?php } ?>
                                </td>
                              </tr>
                            <?php } ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-ebay-shipping"><span data-toggle="tooltip" data-original-title="<?php echo $info_shipping; ?>"><?php echo $entry_shipping_details; ?></span></label>
                    <div class="col-sm-8">
                      <div class="table-responsive" id="product_shipping">
                        <table class="table table-bordered table-hover">
                          <tbody>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_shipping_block_status; ?>"><?php echo $entry_shipping_block_status; ?></span></th>
                                <td class="text-center">
                                    <select class="form-control" name="shipping[status]">
                                      <option value="1" <?php if(isset($shipping['status']) && $shipping['status']){echo 'selected'; } ?>><?php echo $entry_enabled; ?></option>
                                      <option value="0" <?php if(isset($shipping['status']) && !$shipping['status']){echo 'selected'; } ?>><?php echo $entry_disabled; ?></option>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_shipping_keyword; ?>"><?php echo $entry_shipping_keyword; ?></span></th>
                                <td class="text-center">
                                    <input class="form-control" name="shipping[keyword]" value="<?php if(isset($shipping['keyword']) && $shipping['keyword']){ echo $shipping['keyword']; } ?>" placeholder="<?php echo $placeholder_keyword; ?>" id="shipping_keyword" />
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_shipping_title; ?>"><?php echo $entry_shipping_title; ?></span></th>
                                <td class="text-center">
                                    <input class="form-control" name="shipping[title]" value="<?php if(isset($shipping['title']) && $shipping['title']){ echo $shipping['title']; } ?>" placeholder="<?php echo ""; ?>" id="shipping_title" />
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_shipping_description; ?>"><?php echo $entry_shipping_description; ?></span></th>
                                <td class="text-center">
                                    <textarea name="shipping[details]" class="form-control" cols="7" rows="5"><?php if(isset($shipping['details']) && $shipping['details']){ echo $shipping['details']; } ?></textarea>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_shipping_icon; ?>"><?php echo $entry_shipping_icon; ?></span></th>
                                <td class="text-left">
                                  <div class="col-sm-3 checkbox">
                                    <label>
                                      <input type="checkbox" name="shipping[icon_status]" value="1" <?php if(isset($shipping['icon_status']) && $shipping['icon_status']){ echo 'checked'; } ?> />
                                      <b><?php echo $entry_icon_status; ?></b>
                                    </label>
                                  </div>
                                  <div class="col-sm-5">
                                    <a href="" id="thumb-shipping-icon" data-toggle="image" class="img-thumbnail"><img src="<?php echo $shipping_icon; ?>" alt="" title="" data-placeholder="<?php echo ''; ?>" width="50" height="50" /></a>
                                    <input type="hidden" name="shipping[icon]" value="<?php echo isset($shipping['icon']) ? $shipping['icon'] : ''; ?>" id="input-image" />
                                  </div>
                                  <div class="col-sm-4">
                                    <b><?php echo $entry_shipping_icon_size; ?></b>
                                    <select name="shipping[icon_size]" class="form-control">
                                      <option value="50" <?php if(isset($shipping['icon_size']) && $shipping['icon_size'] == '50'){ echo 'selected'; } ?>>S</option>
                                      <option value="75" <?php if(isset($shipping['icon_size']) && $shipping['icon_size'] == '75'){ echo 'selected'; } ?>>M</option>
                                      <option value="100" <?php if(isset($shipping['icon_size']) && $shipping['icon_size'] == '100'){ echo 'selected'; } ?>>L</option>
                                      <option value="250" <?php if(isset($shipping['icon_size']) && $shipping['icon_size'] == '250'){ echo 'selected'; } ?>>Full Size</option>
                                    </select>
                                  </div>
                                  <?php if(isset($error_shipping_icon_type) && $error_shipping_icon_type){ ?>
                                    <div class="text-danger"><?php echo $error_shipping_icon_type; ?></div>
                                  <?php } ?>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_shipping_service; ?>"><?php echo $entry_shipping_service; ?></span></th>
                                <td class="text-center">
                                  <select class="form-control" name="shipping[service]" id="shipping_service">
                                    <?php foreach($shipping_services as $key => $value){ ?>
                                        <option value="<?php echo $value['value']; ?>" <?php if(isset($shipping['service']) && $shipping['service'] == $value['value']){ echo 'selected'; } ?>><?php echo $value['name']; ?></option>
                                    <?php } ?>
                                  </select>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_shipping_free; ?>"><?php echo $entry_shipping_free; ?></span></th>
                                <td class="text-center">
                                    <select name="shipping[free]" id="shipping_free" class="form-control">
                                      <option value="1" <?php if(isset($shipping['free']) && $shipping['free']){ echo 'selected'; } ?>><?php echo $text_enabled; ?></option>
                                      <option value="0" <?php if(isset($shipping['free']) && !$shipping['free']){ echo 'selected'; } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_shipping_cost; ?>"><?php echo $entry_shipping_cost; ?></span></th>
                                <td class="text-center">
                                    <input class="form-control" name="shipping[cost]" value="<?php if(isset($shipping['cost']) && $shipping['cost']){ echo $shipping['cost']; } ?>" placeholder="<?php echo ""; ?>" id="shipping_cost" />
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_additional_cost; ?>"><?php echo $entry_additional_cost; ?></span></th>
                                <td class="text-center">
                                    <input class="form-control" name="shipping[add_cost]" value="<?php if(isset($shipping['add_cost']) && $shipping['add_cost']){ echo $shipping['add_cost']; } ?>" placeholder="<?php echo ""; ?>" id="shipping_add_cost" />
                                </td>
                              </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-return-policy"><span data-toggle="tooltip" data-original-title="<?php echo $info_return_policy; ?>"><?php echo $entry_return_policy_details; ?></span></label>
                    <div class="col-sm-8">
                      <div class="table-responsive" id="product_return_policy">
                        <table class="table table-bordered table-hover">
                          <tbody>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $info_return_policy_status; ?>"><?php echo $entry_return_policy_status; ?></span></th>
                                <td class="text-center">
                                    <select class="form-control" name="return_policy[status]">
                                      <option value="1" <?php if(isset($return_policy['status']) && $return_policy['status']){echo 'selected'; } ?>><?php echo $entry_enabled; ?></option>
                                      <option value="0" <?php if(isset($return_policy['status']) && !$return_policy['status']){echo 'selected'; } ?>><?php echo $entry_disabled; ?></option>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $info_return_policy_keyword; ?>"><?php echo $entry_return_policy_keyword; ?></span></th>
                                <td class="text-center">
                                    <input class="form-control" name="return_policy[keyword]" value="<?php if(isset($return_policy['keyword']) && $return_policy['keyword']){echo $return_policy['keyword']; } ?>" placeholder="<?php echo $placeholder_keyword; ?>" id="return_policy_keyword" />
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $info_return_policy_title; ?>"><?php echo $entry_return_policy_title; ?></span></th>
                                <td class="text-center">
                                    <input class="form-control" name="return_policy[title]" value="<?php if(isset($return_policy['title']) && $return_policy['title']){echo $return_policy['title']; } ?>" placeholder="<?php echo ""; ?>" id="shipping_title" />
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $info_return_policy_list; ?>"><?php echo $entry_return_policy; ?></span></th>
                                <td class="text-center">
                                    <select id="input-return-policy" name="return_policy[policy_type]" class="form-control">
                                      <option value="ReturnsAccepted" <?php if(isset($return_policy['policy_type']) && $return_policy['policy_type'] == 'ReturnsAccepted'){ echo 'selected'; } ?> ><?php echo $text_return_accepted; ?></option>
                                      <option value="ReturnsNotAccepted" <?php if(isset($return_policy['policy_type']) && $return_policy['policy_type'] == 'ReturnsNotAccepted'){ echo 'selected'; } ?>><?php echo $text_return_not_accepted; ?></option>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $info_return_days; ?>"><?php echo $entry_return_days; ?></span></th>
                                <td class="text-center">
                                    <select id="input-return-days" name="return_policy[days]" class="form-control">
                                      <?php foreach($return_days as $key => $value){ ?>
                                        <?php if(isset($return_policy['days']) && $return_policy['days'] == $value['value']){ ?>
                                          <option value="<?php echo $value['value']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                                        <?php }else{ ?>
                                          <option value="<?php echo $value['value']; ?>" ><?php echo $value['name']; ?></option>
                                        <?php } ?>
                                      <?php } ?>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_pay_by; ?>"><?php echo $entry_pay_by; ?></span></th>
                                <td class="text-center">
                                    <select id="input-pay-by" name="return_policy[pay_by]" class="form-control">
                                      <?php foreach($pay_by as $key => $value){ ?>
                                        <?php if(isset($return_policy['pay_by']) && $return_policy['pay_by'] == $value['value']){ ?>
                                          <option value="<?php echo $value['value']; ?>" selected="selected"><?php echo $value['name']; ?></option>
                                        <?php }else{ ?>
                                          <option value="<?php echo $value['value']; ?>" ><?php echo $value['name']; ?></option>
                                        <?php } ?>
                                      <?php } ?>
                                    </select>
                                </td>
                              </tr>
                              <tr>
                                <th class="text-left alert-info" style="width: 30%;"><span data-toggle="tooltip" data-original-title="<?php echo $help_return_policy_details; ?>"><?php echo $entry_return_policy_details; ?></span></th>
                                <td class="text-center">
                                    <textarea name="return_policy[other_info]" class="form-control" cols="7" rows="5"><?php if(isset($return_policy['other_info']) && $return_policy['other_info']){echo $return_policy['other_info']; } ?></textarea>
                                </td>
                              </tr>

                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="col-sm-3 control-label" for="input-template-status"><span data-toggle="tooltip" data-original-title="<?php echo $info_status; ?>"><?php echo $entry_ebay_template_status; ?></span></label>
                    <div class="col-sm-7">
                      <select name="status" class="form-control" id="input-template-status">
                        <option value="1" <?php if(isset($status) && $status){ echo 'selected'; } ?>><?php echo $entry_enabled; ?></option>
                        <option value="0" <?php if(isset($status) && !$status){ echo 'selected'; } ?>><?php echo $entry_disabled; ?></option>
                      </select>
                    </div>
                  </div>

                </div>

                <div class="tab-pane" id="add-ebay-description">

                    <div class="form-group required">
                      <label class="col-sm-3 control-label" for="input-product-description"><?php echo $entry_product_description; ?></label>
                      <div class="col-sm-5">
                        <select name="template_description" class="form-control" id="input-product-description">
                          <option value=""><?php echo $text_select_description; ?></option>
                          <option value="product" <?php if(isset($template_description) && $template_description == 'product'){ echo 'selected'; } ?>><?php echo $text_product_description; ?></option>
                          <option value="custom" <?php if(isset($template_description) && $template_description == 'custom'){ echo 'selected'; } ?>><?php echo $text_custom_description; ?></option>
                        </select>
                        <?php if($error_template_description){ ?>
                          <div class="text-danger"><?php echo $error_template_description; ?></div>
                        <?php } ?>
                      </div>
                    </div>

                    <div class="form-group" id="custom_description" style="display:<?php if(isset($template_description) && $template_description == 'custom'){ echo 'block'; } ?>">
                      <label class="col-sm-3 control-label" for="input-custom-description"><?php echo $entry_custom_description; ?></label>
                      <div class="col-sm-8" id="description_content">
                        <textarea name="custom_description" class="form-control summernote" id="input-custom-description" placeholder="Create your custom product description"><?php if(isset($custom_description) && $custom_description){ echo $custom_description; } ?></textarea>
                        <?php if($error_custom_description){ ?>
                          <div class="text-danger text-left"><?php echo $error_custom_description; ?></div>
                        <?php } ?>
                      </div>
                    </div>
                </div>

            </div>
        </form>

      </div>
    </div>
  </div>

</div>
<script type="text/javascript" src="view/javascript/summernote/summernote.js"></script>
<link href="view/javascript/summernote/summernote.css" rel="stylesheet" />
<script type="text/javascript" src="view/javascript/summernote/opencart.js"></script>
<script>
var specification_row = <?php echo $specification_row; ?>;
var specificationArray = [];

$('select[name=\'template_ebay_site\']').on('change', function(){
  var ebaySiteId = $(this).val();
  $('#product_specification tfoot').css("display","none");
  $('#input-ebay-mapped-category .mapped_category').remove();
  $('#product_specification tbody > tr').remove();
  $('#product_specification tbody').append('<tr><td class="text-center alert-warning" colspan="3"><?php echo $text_no_record; ?></td></tr>');
    if(ebaySiteId.length >= 1){
      $.ajax({
          url     :   'index.php?route=ebay_map/ebay_template_listing/getMappedCategory&token=<?php echo $token; ?>',
          type    :   "POST",
          dataType:   "json",
          data    : {
                      'ebay_site_id'     : ebaySiteId,
                    },
          beforeSend: function() {
            // $('.container-fluid > .alert').remove();
          },
          success: function(jsonResponse) {
              var html = '';
              if(jsonResponse.ebay_categories){
                  for (i in jsonResponse.ebay_categories) {
                    html += '<option value="'+jsonResponse.ebay_categories[i]['ebay_category_id']+'" class="mapped_category">'+jsonResponse.ebay_categories[i]['ebay_category_name']+'</option>';
                  }
              }
              $('select[name=\'template_mapped_category\']').append(html);
          },
        })
    }
})

$('select[name=\'template_mapped_category\']').on('change', function(){
  var ebayMappedCategoryId = $(this).val();
      specificationArray = [];
    if(ebayMappedCategoryId.length >= 3){
      $.ajax({
          url     :   'index.php?route=ebay_map/ebay_template_listing/getCategoryBasedSpecification&token=<?php echo $token; ?>',
          type    :   "POST",
          dataType:   "json",
          data    : {
                      'ebay_category_id'     : ebayMappedCategoryId,
                    },
          beforeSend: function() {
            $('.container-fluid > .alert').remove();
            $('#product_specification tbody > tr').remove();
            $('#product_specification tbody').append('<tr><td class="text-center alert-warning" colspan="3"><?php echo $text_no_record; ?></td></tr>');
          },
          success: function(jsonResponse) {
              var html = '';
              if(jsonResponse.ebay_specifications){
                $('#product_specification tbody > tr').remove();
                html += '<tr id="specification-row' + specification_row + '"><td class="text-center"><select name="template[specification]['+specification_row+']" class="form-control">';
                  for (i in jsonResponse.ebay_specifications) {
                      specificationArray.push(jsonResponse.ebay_specifications[i]);
                      html += '<option value="'+jsonResponse.ebay_specifications[i]['attribute_group_id']+'" class="mapped_category">'+jsonResponse.ebay_specifications[i]['attr_group_name']+'</option>';
                  }
                html += '</select></td>';
                html += '<td class="text-center"><input class="form-control" name="template[keyword]['+specification_row+']" value="" /></td>';
                html += '  <td class="text-left"><button type="button" onclick="$(\'#specification-row' + specification_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove_row; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
                $('#product_specification tbody').append(html);
                $('#product_specification tfoot').css("display","table-footer-group");
                specification_row++;
              }
          },
        })
    }else{
      $('#product_specification tbody > tr').remove();
      $('#product_specification tfoot').remove();
      $('#product_specification tbody').append('<tr><td class="text-center alert-warning" colspan="3"><?php echo $text_no_record; ?></td></tr>');
    }
})

function addSpecification() {
  if(specificationArray.length === 0) {
    <?php if(isset($ebay_specifications)){ ?>
      <?php foreach ($ebay_specifications as $specification) { ?>
          var attrArray = [];
          attrArray['attribute_group_id']  =  '<?php echo $specification['attribute_group_id']; ?>';
          attrArray['attr_group_name']     =  "<?php echo $specification['attr_group_name']; ?>".replace(/'/g, "\\'");
          specificationArray.push(attrArray);
      <?php } ?>
    <?php } ?>
  }
  html  = '<tr id="specification-row' + specification_row + '"><td class="text-center"><select name="template[specification]['+specification_row+']" class="form-control">';
  for (i in specificationArray) {
    html += '<option value="'+specificationArray[i]['attribute_group_id']+'" class="mapped_category">'+specificationArray[i]['attr_group_name'].replace(/\\'/g, '\'')+'</option>';
  }
	html += '</select></td><td class="text-center"><input class="form-control" name="template[keyword]['+specification_row+']" value="" /></td>';
	html += '  <td class="text-left"><button type="button" onclick="$(\'#specification-row' + specification_row + '\').remove();" data-toggle="tooltip" title="<?php echo $button_remove_row; ?>" class="btn btn-danger"><i class="fa fa-minus-circle"></i></button></td>';
  html += '</tr>';

	$('#product_specification tbody').append(html);
	specification_row++;
}

$('select[name=\'template_description\']').on('change', function(){
  var ebayDescription = $(this).val();
  $('#custom_description').css("display", "none");
  if(ebayDescription == 'custom'){
    $('#custom_description').css("display", "block");
  }
})

$(document).ready(function(){
    <?php if(empty($ebay_categories)){ ?>
      $('select[name=\'template_ebay_site\']').trigger('change');
    <?php } ?>
})
</script>
<?php echo $footer; ?>
