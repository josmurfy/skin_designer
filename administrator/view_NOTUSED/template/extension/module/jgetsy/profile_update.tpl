<?php echo $header; ?><?php echo $column_left; ?>

<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <?php if($shipping_profile == true) { ?>
                    <button type="button" form="etsy-profile-add" id="etsy-profile-add-button" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <?php } ?>    
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
        <?php if (isset($error['error_warning'])) { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error['error_warning']; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <?php if (isset($success) && $success != '') { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title_main; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <?php if($shipping_profile == true) { ?>
                <?php if(!empty($etsy_categories)) { ?>
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="etsy-profile-add" class="form-horizontal">
                    <input type="hidden" name="etsy[profile][id_etsy_category_final]" value="<?php if(isset($id_etsy_category_final)) { echo $id_etsy_category_final; } ?>" id="id_etsy_category_final" class="form-control"/>
                    <input type="hidden" name="etsy[profile][etsy_category_text]" value="<?php if(isset($etsy_category_text)) { echo $etsy_category_text; } ?>" id="etsy_category_text" class="form-control"/>

                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_profile_title"><?php echo $text_profile_title; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="etsy[profile][profile_title]" value="<?php if (isset($profile_title)) echo $profile_title; ?>" placeholder="" id="etsy_profile_title" class="form-control"/>
                            <?php if ($error_etsy_profile_title) { ?>
                                <div class="text-danger"><?php echo $error_etsy_profile_title; ?></div>
                            <?php } ?>
                            <?php if (isset($id_etsy_profiles)) { ?>
                                <input type="hidden" name="id_etsy_profiles" value="<?php echo $id_etsy_profiles; ?>" placeholder="" id="id_etsy_profiles" class="form-control"/>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy-category"><?php echo $text_etsy_category; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][etsy_category]" id="etsy-category" class="form-control etsy_root" <?php if($id_etsy_category_final !="") { echo 'style="display:none"'; } ?>>
                                <option value=""><?php echo $entry_select_etsy_category; ?></option>
                                <?php foreach ($etsy_categories as $categories) { ?>
                                    <?php if ($categories['category_code'] == $etsy_category) { ?>
                                        <option value="<?php echo $categories['category_code']; ?>" selected="selected"><?php echo $categories['category_name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $categories['category_code']; ?>"><?php echo $categories['category_name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php if ($error_etsy_category) { ?>
                                <div class="text-danger"><?php echo $error_etsy_category; ?></div>
                            <?php } ?>
                        </div>
                        <?php 
                        if($id_etsy_category_final != "") { ?>
                            <div class="col-sm-10">
                                <p id="etsy_cat_text_div"><?php echo $etsy_category_text; ?></p>
                            </div>
                            <div class="col-sm-2" style="clear:both"></div>
                            <div class="col-sm-10">
                                <input type="button" id="change_category" value="<?php echo $text_change_category; ?>">
                            </div>
                        <?php } ?>
                    </div>
                    <div class="etsy_category_class">
                        
                    </div>
                    
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="input-category"><?php echo $text_store_category; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="category" value="" placeholder="<?php echo $text_store_category; ?>" id="input-category" class="form-control" />
                            <div id="product-category" class="well well-sm" style="height: 150px; overflow: auto; margin-bottom: 0px">
                                <?php foreach ($store_categories as $product_category) { ?>
                                    <div id="product-category<?php echo $product_category['category_id']; ?>"><i class="fa fa-minus-circle"></i> <?php echo $product_category['name']; ?>
                                        <input type="hidden" name="product_category[]" value="<?php echo $product_category['category_id']; ?>" />
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if ($error_category) { ?>
                                <div class="text-danger"><?php echo $error_category; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="etsy_shop_section"><?php echo $text_shop_section_select; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][shop_section_id]" id="shop_section" class="form-control">
                                <option value=""><?php echo $text_shop_section_select_option; ?></option>
                                <?php foreach ($etsy_shop_sections as $shop_section) { ?>
                                    <?php if ($shop_section['etsy_shop_section_id'] == $shop_section_id) { ?>
                                        <option value="<?php echo $shop_section['etsy_shop_section_id']; ?>" selected="selected"><?php echo $shop_section['title']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $shop_section['etsy_shop_section_id']; ?>"><?php echo $shop_section['title']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_templates"><?php echo $text_shipping_profile; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][etsy_templates]" id="etsy-category" class="form-control">
                                <option value=""><?php echo $entry_select_template; ?></option>
                                <?php foreach ($etsy_ship_templates as $templates) { ?>
                                    <?php if ($templates['id_etsy_shipping_profiles'] == $etsy_templates) { ?>
                                        <option value="<?php echo $templates['id_etsy_shipping_profiles']; ?>" selected="selected"><?php echo $templates['shipping_profile_title']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $templates['id_etsy_shipping_profiles']; ?>"><?php echo $templates['shipping_profile_title']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php if ($error_etsy_templates) { ?>
                                <div class="text-danger"><?php echo $error_etsy_templates; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_is_customize; ?></label>
                        <div class="col-sm-10">
                            <label class="radio-inline">
                                <?php if ($is_customizable) { ?>
                                    <input type="radio" name="etsy[profile][is_customizable]" value="1" checked="checked" />
                                    <?php echo $text_yes; ?>
                                <?php } else { ?>
                                    <input type="radio" name="etsy[profile][is_customizable]" value="1" />
                                    <?php echo $text_yes; ?>
                                <?php } ?>
                            </label>
                            <label class="radio-inline">
                                <?php if (!$is_customizable) { ?>
                                    <input type="radio" name="etsy[profile][is_customizable]" value="0" checked="checked" />
                                    <?php echo $text_no; ?>
                                <?php } else { ?>
                                    <input type="radio" name="etsy[profile][is_customizable]" value="0" />
                                    <?php echo $text_no; ?>
                                <?php } ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_auto_renew; ?></label>
                        <div class="col-sm-10">
                            <label class="radio-inline">
                                <?php if ($auto_renew) { ?>
                                    <input type="radio" name="etsy[profile][auto_renew]" value="1" checked="checked" />
                                    <?php echo $text_yes; ?>
                                <?php } else { ?>
                                    <input type="radio" name="etsy[profile][auto_renew]" value="1" />
                                    <?php echo $text_yes; ?>
                                <?php } ?>
                            </label>
                            <label class="radio-inline">
                                <?php if (!$auto_renew) { ?>
                                    <input type="radio" name="etsy[profile][auto_renew]" value="0" checked="checked" />
                                    <?php echo $text_no; ?>
                                <?php } else { ?>
                                    <input type="radio" name="etsy[profile][auto_renew]" value="0" />
                                    <?php echo $text_no; ?>
                                <?php } ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_template_max_process_days"><?php echo $text_who_made; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][who_made]" id="etsy-who-made" class="form-control">
                                <option value=""><?php echo $entry_select_who_made; ?></option>
                                <?php foreach ($who_made_list as $list) { ?>
                                    <?php if ($list['id_option'] == $who_made) { ?>
                                        <option value="<?php echo $list['id_option']; ?>" selected="selected"><?php echo $list['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $list['id_option']; ?>"><?php echo $list['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php if ($error_who_made) { ?>
                                <div class="text-danger"><?php echo $error_who_made; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_template_max_process_days"><?php echo $text_when_made; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][when_made]" id="etsy-when-made" class="form-control">
                                <option value=""><?php echo $entry_select_when_made; ?></option>
                                <?php foreach ($when_made_list as $list) { ?>
                                    <?php if ($list['id_option'] == $when_made) { ?>
                                        <option value="<?php echo $list['id_option']; ?>" selected="selected"><?php echo $list['name']; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $list['id_option']; ?>"><?php echo $list['name']; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php if ($error_when_made) { ?>
                                <div class="text-danger"><?php echo $error_when_made; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"><?php echo $entry_is_supply; ?></label>
                        <div class="col-sm-10">
                            <label class="radio-inline">
                                <?php if (!$is_supply) { ?>
                                    <input type="radio" name="etsy[profile][is_supply]" value="0" checked="checked" />
                                    <?php echo $a_finised_product; ?>
                                <?php } else { ?>
                                    <input type="radio" name="etsy[profile][is_supply]" value="0" />
                                    <?php echo $a_finised_product; ?>
                                <?php } ?>
                            </label>
                            <label class="radio-inline">
                                <?php if ($is_supply) { ?>
                                    <input type="radio" name="etsy[profile][is_supply]" value="1" checked="checked" />
                                    <?php echo $tool_to_make_things; ?>
                                <?php } else { ?>
                                    <input type="radio" name="etsy[profile][is_supply]" value="1" />
                                    <?php echo $tool_to_make_things; ?>
                                <?php } ?>
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="etsy-etsy_recipient"><?php echo $entry_recepient; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][etsy_recipient]" id="etsy-etsy_recipient" class="form-control">
                                <?php foreach ($recipients_list as $key => $list) { ?>
                                    <?php if ($key == $etsy_recipient) { ?>
                                        <option value="<?php echo $key; ?>" selected="selected"><?php echo $list; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $key; ?>"><?php echo $list; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php if ($error_etsy_recipient) { ?>
                                <div class="text-danger"><?php echo $error_etsy_recipient; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="etsy-occasion"><?php echo $entry_occasion; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][etsy_occasion]" id="etsy-occasion" class="form-control">
                                <?php foreach ($occasions_list as $key => $occasion) { ?>
                                    <?php if ($key == $etsy_occasion) { ?>
                                        <option value="<?php echo $key; ?>" selected="selected"><?php echo $occasion; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $key; ?>"><?php echo $occasion; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                            <?php if ($error_etsy_occasion) { ?>
                                <div class="text-danger"><?php echo $error_etsy_occasion; ?></div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <div class="form-group required">
                        <label class="col-sm-2 control-label">
                            <span data-toggle="tooltip" title="" data-original-title="<?php echo $text_price_type_hint; ?>">
                                <?php echo $text_price_type; ?>
                            </span>
                        </label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][price_type]" id="etsy_price_type" class="form-control">
                                <?php if ($price_type) { ?>
                                <option value="1" selected="selected"><?php echo $text_special_price; ?></option>
                                <option value="0"><?php echo $text_actual_price; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_special_price; ?></option>
                                <option value="0" selected="selected"><?php echo $text_actual_price; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group required">
                        <label class="col-sm-2 control-label" for="etsy_price_management"><?php echo $text_price_management; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][price_management]" id="etsy_price_management" class="form-control">
                                <?php if ($price_management) { ?>
                                <option value="1" selected="selected"><?php echo $text_yes; ?></option>
                                <option value="0"><?php echo $text_no; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_yes; ?></option>
                                <option value="0" selected="selected"><?php echo $text_no; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group required" <?php if (isset($price_management) && $price_management == 0) { echo "style = 'display:none;'";} ?>>
                        <label class="col-sm-2 control-label" for="etsy_price_management"><?php echo $text_increase_decrese_price; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][increase_decrease]" id="etsy_increase_decrease" class="form-control">
                                <?php if ($increase_decrease) { ?>
                                <option value="1" selected="selected"><?php echo $text_increase_price; ?></option>
                                <option value="0"><?php echo $text_decrease_price; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_increase_price; ?></option>
                                <option value="0" selected="selected"><?php echo $text_decrease_price; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group required" <?php if (isset($price_management) && $price_management == 0) { echo "style = 'display:none;'";} ?>>
                        <label class="col-sm-2 control-label" for="etsy_price_management"><?php echo $text_price_value; ?></label>
                        <div class="col-sm-10">
                            <input type="text" name="etsy[profile][product_price]" value="<?php if(isset($product_price)) echo $product_price; ?>" placeholder="" id="etsy_product_price" class="form-control"/>
                             <?php if ($error_etsy_product_price) { ?>
                                <div class="text-danger"><?php echo $error_etsy_product_price; ?></div>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="form-group required" <?php if (isset($price_management) && $price_management == 0) { echo "style = 'display:none;'";} ?>>
                        <label class="col-sm-2 control-label" for="etsy_price_management"><?php echo $text_price_percentage_fixed; ?></label>
                        <div class="col-sm-10">
                            <select name="etsy[profile][percentage_fixed]" id="etsy_percentage_fixed" class="form-control">
                                <?php if ($percentage_fixed) { ?>
                                <option value="1" selected="selected"><?php echo $text_price_percentage; ?></option>
                                <option value="0"><?php echo $text_price_fixed; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_price_percentage; ?></option>
                                <option value="0" selected="selected"><?php echo $text_price_fixed; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    
                </form>
                <?php } else { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
                    <?php echo $category_not_avaliable_error; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php } ?>
                <?php } else { ?>
                <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>
                    <?php echo $shipping_profile_not_avaliable_error; ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php if(isset($id_etsy_category_final) && $id_etsy_category_final) { ?>
    $.ajax({
        url: 'index.php?route=<?php echo $module_path; ?>/ajaxGetPropertiesList&<?php echo $session_token_key; ?>=<?php echo $token; ?>&id_etsy_profiles=<?php echo $id_etsy_profiles; ?>&category_id='+<?php echo $id_etsy_category_final; ?>,
        dataType: 'html',
        success: function(html) {
            $(".etsy_category_class").html(html);
        }
    }); 
    <?php } ?>
    $('input[name=\'category\']').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: 'index.php?route=catalog/category/autocomplete&<?php echo $session_token_key; ?>=<?php echo $token; ?>&filter_name=' + encodeURIComponent(request),
                dataType: 'json',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {
                            label: item['name'],
                            value: item['category_id']
                        }
                    }));
                }
            });
        },
        'select': function (item) {
            $('input[name=\'category\']').val('');
            $('#product-category' + item['value']).remove();
            $('#product-category').append('<div id="product-category' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="product_category[]" value="' + item['value'] + '" /></div>');
        }
    });

    $('#product-category').delegate('.fa-minus-circle', 'click', function () {
        $(this).parent().remove();
    });
    
    $(document).on('change', '.etsy_root', function() {
        $(this).parent().nextAll('.col-sm-2').remove();
        $(this).parent().nextAll('.col-sm-10').remove();
        var category_id = $(this).val();
        if(category_id != "") {
            $.ajax({
                url: 'index.php?route=<?php echo $module_path; ?>/ajaxGetPropertiesList&<?php echo $session_token_key; ?>=<?php echo $token; ?>&category_id='+category_id,
                dataType: 'html',
                success: function(html) {
                    $(".etsy_category_class").html(html);
                }
            });    
            $.ajax({
                url: 'index.php?route=<?php echo $module_path; ?>/getSubcategories&<?php echo $session_token_key; ?>=<?php echo $token; ?>&category_id='+category_id,
                dataType: 'json',
                success: function(json) {
                    if(json) {
                        if(json.scales != undefined) {
                            var scales = $.map(json.scales, function(item) { return item });
                            if(scales.length > 0) {
                            }
                            html = '<div class="col-sm-2"></div><div class="col-sm-10" style=""><input type="button" id="confirm_category" value="<?php echo $text_confirm_category; ?>"></div>';
                            $("[name='etsy[profile][etsy_category]']").closest('.col-sm-10').parent().append(html);
                        } else {
                            var arr = $.map(json, function(item) { return item });
                            if(arr.length > 0) {
                                var html = '<div class="col-sm-2"></div><div class="col-sm-10">';
                                html+= '<select class="form-control etsy_root subcat removeall">';
                                html += '<option value=""><?php echo $entry_select_etsy_category; ?></option>';
                                for(i = 0; i < arr.length; i++) {
                                    html += '<option value="' + arr[i]['category_code'] + '">' + arr[i]['category_name'] + '</option>';
                                }
                                html += '</div>';
                                $("[name='etsy[profile][etsy_category]']").closest('.col-sm-10').parent().append(html);
                            } else {
                                html = '<div class="col-sm-2"></div><div class="col-sm-10" style=""><input type="button" id="confirm_category" value="<?php echo $text_confirm_category; ?>"></div>';
                                $("[name='etsy[profile][etsy_category]']").closest('.col-sm-10').parent().append(html);
                            }
                        }
                    } else {
                        html = '<div class="col-sm-2"></div><div class="col-sm-10" style=""><input type="button" id="confirm_category" value="<?php echo $text_confirm_category; ?>"></div>';
                        $("[name='etsy[profile][etsy_category]']").closest('.col-sm-10').parent().append(html);
                    }
                }
            });
        }
    });

    $(document).on('click', '#confirm_category', function() {
        var checkLastLeaf;
        $(".etsy_root").each(function() {
            checkLastLeaf = ($(this).val());
            if (checkLastLeaf == 0) {
                alert('<?php echo $select_leaf_category; ?>');
                return false;
            }
        });
        
        var final_category_text = [];
      
        $('.etsy_root option:selected').each(function() {
            final_category_text.push($(this).text());
        });
    
        var category_text = (final_category_text.join('>>'));
        
        $('#etsy_category_text').val(category_text);
        $('#id_etsy_category_final').val(checkLastLeaf);
        
        $('<p id="etsy_cat_text_div">' + category_text + '</p>').appendTo(document.getElementById("etsy-category").closest('.col-sm-10'));
        $(".etsy_root").hide();
        $(".etsy_root_text").hide();
        $("#confirm_category").hide();
        html = '<div class="col-sm-2" style="clear:both"></div><div class="col-sm-10"><input type="button" id="change_category" value="<?php echo $text_change_category; ?>"></div>';
        $("[name='etsy[profile][etsy_category]']").closest('.col-sm-10').parent().append(html);
    });

    $(document).on('click', '#change_category', function() {
        $("#etsy_cat_text_div").remove();
        $("#change_category").remove();
        $(".etsy_root").show();
        $(".etsy_root_text").show();
        $("#confirm_category").show();
        $('#etsy_category_text').val("");
        $('#id_etsy_category_final').val("");
    });
    
    $(document).on('change', '#etsy_price_management', function() {
        var status = $(this).val();
        if(status == 1){
            $('#etsy_increase_decrease').closest('.form-group').css('display','block');
            $('#etsy_product_price').closest('.form-group').css('display','block');
            $('#etsy_percentage_fixed').closest('.form-group').css('display','block');
            $('#etsy_product_threshold_price').closest('.form-group').css('display','block');
        } else {
            $('#etsy_increase_decrease').closest('.form-group').css('display','none');
            $('#etsy_product_price').closest('.form-group').css('display','none');
            $('#etsy_percentage_fixed').closest('.form-group').css('display','none');
            $('#etsy_product_threshold_price').closest('.form-group').css('display','none');
        }
    });
    $(document).ready(function() {
        $("#etsy-profile-add-button").click(function() {
            if($("#id_etsy_category_final").val() == "") {
                alert('<?php echo $select_leaf_category; ?>');
                return false;
            }
            $("#etsy-profile-add").submit();
        });
    });
    
    
    $(document).on('change', '#etsy_price_management', function() {
        var status = $(this).val();
        if(status == 1){
            $('#etsy_increase_decrease').closest('.form-group').css('display','block');
            $('#etsy_product_price').closest('.form-group').css('display','block');
            $('#etsy_percentage_fixed').closest('.form-group').css('display','block');
        } else {
            $('#etsy_increase_decrease').closest('.form-group').css('display','none');
            $('#etsy_product_price').closest('.form-group').css('display','none');
            $('#etsy_percentage_fixed').closest('.form-group').css('display','none');
        }
    });
</script>
<?php echo $footer; ?>
