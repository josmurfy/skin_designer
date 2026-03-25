<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if (isset($error) && $error != "") { ?>
            <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
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
                <h3 class="panel-title"><i class="fa fa-tasks"></i><?php echo $text_product_listing; ?></h3>
            </div>
            <div class="panel-body">
                <?php echo $tabs; ?>
                <div class="well">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-name"><?php echo $entry_name; ?></label>
                                <input type="text" name="filter_name" value="<?php echo $filter_name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="input-model"><?php echo $entry_model; ?></label>
                                <input type="text" name="filter_model" value="<?php echo $filter_model; ?>" placeholder="<?php echo $entry_model; ?>" id="input-model" class="form-control" />
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-listing_status"><?php echo $column_listing_status; ?></label>
                                <select name="filter_listing_status" id="input-listing_status" class="form-control">

                                    <option value="*"></option>
                                    <?php foreach ($listing_statuses as $key => $listing_status) { ?>
                                        <?php if ($filter_listing_status == $key) { ?>
                                            <option value="<?php echo $key; ?>" selected="selected"><?php echo $listing_status; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $listing_status; ?></option>
                                        <?php } ?>
                                    <?php } ?>

                                </select>
                            </div>
                            <!--
                            <div class="form-group">
                                <label class="control-label" for="input-listed-on"><?php echo $column_listed_on; ?></label>
                                <div class="input-group date">
                                    <input type="text" name="filter_listed_on" value="<?php echo $filter_listed_on; ?>" placeholder="<?php echo $column_listed_on; ?>" data-format="YYYY-MM-DD" id="input-listed-on" class="form-control" />
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><i class="fa fa-calendar"></i></button>
                                    </span>
                                </div>
                            </div>
                            -->
                            <div class="form-group">
                                <label class="control-label" for="input-listed-on"><?php echo $column_profile; ?></label>
                                <select name="filter_id_etsy_profiles" id="input-id_etsy_profiles" class="form-control">
                                    <option value=""></option>
                                    <?php foreach ($etsy_profiles as $etsy_profile) { ?>
                                        <?php if ($filter_id_etsy_profiles == $etsy_profile['id_etsy_profiles']) { ?>
                                            <option value="<?php echo $etsy_profile['id_etsy_profiles']; ?>" selected="selected"><?php echo $etsy_profile['profile_title']; ?></option>
                                        <?php } else { ?>
                                            <option value="<?php echo $etsy_profile['id_etsy_profiles']; ?>"><?php echo $etsy_profile['profile_title']; ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            </div>
                            
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="input-listing-id"><?php echo $column_listing_id; ?></label>
                                <input type="text" name="filter_listing_id" value="<?php echo $filter_listing_id; ?>" placeholder="<?php echo $column_listing_id; ?>" id="input-listing-id" class="form-control" />
                            </div>
                            <div class="form-group">
                                <label class="control-label" style="display: block">&nbsp;</label>
                                <button type="button" id="button-filter" class="btn btn-primary pull-right"><i class="fa fa-search"></i> <?php echo $button_filter; ?></button>
                                <button type="button" id="button-refresh" class="btn btn-default pull-right" style="margin-right: 2px;"><i class="fa fa-refresh"></i> <?php echo $button_reset; ?></button>&nbsp;
                            </div>
                        </div>
                    </div>
                </div>
                <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form-product">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <td class="text-left"><?php if ($sort == 'p.product_id') { ?>
                                            <a href="<?php echo $sort_id; ?>" class="<?php echo $order; ?>"><?php echo $column_product_id; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_id; ?>"><?php echo $column_product_id; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'pd.name') { ?>
                                            <a href="<?php echo $sort_name; ?>" class="<?php echo $order; ?>"><?php echo $column_name; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'p.quantity') { ?>
                                            <a href="<?php echo $sort_quantity; ?>" class="<?php echo $order; ?>"><?php echo $column_quantity; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_quantity; ?>"><?php echo $column_quantity; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'ep.id_etsy_profiles') { ?>
                                            <a href="<?php echo $sort_profile; ?>" class="<?php echo $order; ?>"><?php echo $column_profile; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_profile; ?>"><?php echo $column_profile; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'epl.listing_status') { ?>
                                            <a href="<?php echo $sort_listing_status; ?>" class="<?php echo $order; ?>"><?php echo $column_listing_status; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_listing_status; ?>"><?php echo $column_listing_status; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'epl.listing_id') { ?>
                                            <a href="<?php echo $sort_listing_id; ?>" class="<?php echo $order; ?>"><?php echo $column_listing_id; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_listing_id; ?>"><?php echo $column_listing_id; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-left">
                                        <!--<?php if ($sort == 'epl.update_flag') { ?>
                                            <a href="<?php echo $sort_update_status; ?>" class="<?php echo $order; ?>"><?php echo $column_relisting_status; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_update_status; ?>"><?php echo $column_relisting_status; ?></a>
                                        <?php } ?>
                                        -->
                                        <a href="javascript://"><?php echo $column_relisting_status; ?></a>
                                    </td>
                                    <td class="text-left"><?php if ($sort == 'epl.date_listed') { ?>
                                            <a href="<?php echo $sort_listed_on; ?>" class="<?php echo $order; ?>"><?php echo $column_listed_on; ?></a>
                                        <?php } else { ?>
                                            <a href="<?php echo $sort_listed_on; ?>"><?php echo $column_listed_on; ?></a>
                                        <?php } ?>
                                    </td>
                                    <td class="text-right"><?php echo $column_action; ?></td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($etsy_products) { ?>
                                    <?php foreach ($etsy_products as $product) { ?>
                                        <tr>
                                            <td class="text-left"><a href="<?php echo $product['admin_edit_link']; ?>" target="_blank"><?php echo $product['product_id']; ?></a></td>
                                            <td class="text-left">
                                                <a href="<?php echo $product['catalog_link']; ?>" target="_blank"><?php echo $product['name']; ?></a>
                                            </td>
                                            <td class="text-left"><?php echo $product['quantity']; ?></td>
                                            <td class="text-left"><?php echo $product['profile_title']; ?></td>
                                            <?php if($product['is_disabled'] == 1) { ?>
                                            <td class="text-left"><?php echo $text_disabled; ?></td>
                                            <?php } else { ?>
                                            <td class="text-left"><?php echo $product['listing_status_text']; ?></td>
                                            <?php } ?>
                                            <td class="text-left">
                                                <?php if($product['listing_id'] == NULL) { ?>
                                                <?php echo $product['listing_id']; ?>
                                                <?php } else { ?>
                                                <a href="https://www.etsy.com/listing/<?php echo $product['listing_id']; ?>" target="_blank"><?php echo $product['listing_id']; ?></a>
                                                <?php } ?>
                                            </td>
                                            
                                            <td class="text-left">
                                                <?php 
                                                if($product['update_flag'] == 1) {
                                                    echo $update_flag_text; 
                                                } else if($product['delete_flag'] == 1) {
                                                    echo $delete_flag_text; 
                                                } else if($product['renew_flag'] == 1) {
                                                    echo $renew_flag_text; 
                                                }
                                                ?>
                                            </td>
                                            <td class="text-left"><?php echo $product['listed_on']; ?></td>

                                            <td class="text-right">
                                                <?php if($product['listing_status'] == 'Pending' && $product['is_disabled'] == 0) { ?>
                                                        <a target="_blank" href="<?php echo HTTPS_CATALOG . 'index.php?route=kbetsy/product&secure_key='.$secure_key; ?>&etsy_product_id=<?php echo $product['product_id']; ?>" data-toggle="tooltip" title="<?php echo $sync_product_to_etsy; ?>" class="btn btn-success"><i class="fa fa-refresh"></i></a>
                                                <?php } ?>
                                                        
                                                <?php if(($product['listing_status'] == 'Listed' || $product['listing_status'] == 'Inactive'  || $product['listing_status'] == 'Expired') && $product['is_disabled'] == 0 && ($product['update_flag'] == '1' || $product['renew_flag'] == '1')) { ?>
                                                        <a target="_blank" href="<?php echo HTTPS_CATALOG . 'index.php?route=kbetsy/product&secure_key='.$secure_key; ?>&update=1&etsy_product_id=<?php echo $product['product_id']; ?>" data-toggle="tooltip" title="<?php echo $sync_product_to_etsy; ?>" class="btn btn-success"><i class="fa fa-refresh"></i></a>
                                                <?php } ?>
                                                
                                                <?php if($product['is_disabled'] == 1) { ?>
                                                     <a href="<?php echo $product['activate']; ?>" data-toggle="tooltip" title="<?php echo $button_reactivate; ?>" class="btn btn-default"><i class="fa fa-refresh"></i></a>
                                                <?php } else { ?>
                                                <?php if($product["message"] != "") { ?>    
                                                    <a data-message='<?php echo $product["message"]; ?>' data-toggle="modal" data-target=".bd-example-modal-sm" data-toggle="tooltip" title="<?php echo $button_error; ?>" class="btn btn-info"><i class="fa fa-exclamation-circle"></i></a>
                                                <?php } ?>                                                     
                                                <!-- If Status is Listed & Update/Delete/Renew flag is not set then we can only mark the products to update -->
                                                <?php if($product['listing_status'] == 'Listed') { ?>
                                                    <?php if($product['update_flag'] != '1' && $product['delete_flag'] != '1' && $product['renew_flag'] != '1') { ?>
                                                        <a href="<?php echo $product['revise']; ?>" data-toggle="tooltip" title="<?php echo $button_relist; ?>" class="btn btn-default"><i class="fa fa-retweet"></i></a>
                                                    <?php } ?>
                                                <?php } ?>
                                                
                                                <!-- If Status is Inactive then we can only mark the products to activate -->
                                                <?php if($product['listing_status'] == 'Inactive') { ?>
                                                    <?php if($product['update_flag'] != '1' && $product['delete_flag'] != '1' && $product['renew_flag'] != '1') { ?>
                                                        <!-- <a href="<?php echo $product['revise']; ?>" data-toggle="tooltip" title="<?php echo $button_reactivate; ?>" class="btn btn-default"><i class="fa fa-retweet"></i></a> -->
                                                    <?php } else if($product['delete_flag'] != '1') { ?>
                                                        <!-- <a href="<?php echo $product['halt']; ?>" data-toggle="tooltip" title="<?php echo $button_halt_activation; ?>" class="btn btn-primary"><i class="fa fa-stop"></i></a> -->
                                                    <?php } ?> 
                                                <?php } ?>
                                                        
                                                <?php if ($product['delete_flag'] == '1') { ?>
                                                    <a href="<?php echo $product['halt']; ?>" data-toggle="tooltip" title="<?php echo $button_halt_deletion; ?>" class="btn btn-primary"><i class="fa fa-stop"></i></a>
                                                <?php } ?>
                                                    
                                                <?php if ($product['renew_flag'] == '1') { ?>
                                                    <a href="<?php echo $product['halt']; ?>" data-toggle="tooltip" title="<?php echo $button_halt; ?>" class="btn btn-primary"><i class="fa fa-stop"></i></a>
                                                <?php } else if($product['listing_status'] == 'Expired' || $product['listing_status'] == 'Inactive' ) { ?>
                                                    <a href="<?php echo $product['renew']; ?>" data-toggle="tooltip" title="<?php echo $button_renew; ?>" class="btn btn-primary"><i class="fa fa-refresh"></i></a>
                                                <?php } ?>
                                                    
                                                <?php if (($product['listing_status'] == 'Listed' || $product['listing_status'] == 'Inactive') && $product['delete_flag'] == 0) { ?>
                                                    <!-- <a href="<?php echo $product['delete']; ?>" data-toggle="tooltip" title="<?php echo $button_delete; ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i></a> -->
                                                <?php } ?>
                                                <a href="<?php echo $product['disable']; ?>" data-toggle="tooltip" title="<?php echo $text_disabled_hint; ?>" class="btn btn-danger"><i class="fa fa-minus"></i></a>
                                                    
                                                
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td class="text-center" colspan="9"><?php echo $text_no_results; ?></td>
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

<div class="modal fade bd-example-modal-sm" id="errorMessageModal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo $entry_error_message; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"><?php echo $entry_no_error; ?></div>
        </div>
    </div>
</div>

<script src="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<link href="view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />

<script type="text/javascript">
    $('#button-filter').on('click', function () {
        var url = 'index.php?route=<?php echo $module_path; ?>/productListing&<?php echo $session_token_key; ?>=<?php echo $token; ?>';

        var filter_name = $('input[name=\'filter_name\']').val();
        if (filter_name) {
            url += '&filter_name=' + encodeURIComponent(filter_name);
        }

        var filter_model = $('input[name=\'filter_model\']').val();
        if (filter_model) {
            url += '&filter_model=' + encodeURIComponent(filter_model);
        }

        var filter_listing_id = $('input[name=\'filter_listing_id\']').val();
        if (filter_listing_id) {
            url += '&filter_listing_id=' + encodeURIComponent(filter_listing_id);
        }

        //var filter_listed_on = $('input[name=\'filter_listed_on\']').val();
        //if (filter_listed_on) {
        //    url += '&filter_listed_on=' + encodeURIComponent(filter_listed_on);
        //}
        
        var filter_id_etsy_profiles = $('select[name=\'filter_id_etsy_profiles\']').val();
        if (filter_id_etsy_profiles) {
            url += '&filter_id_etsy_profiles=' + encodeURIComponent(filter_id_etsy_profiles);
        }

        var filter_listing_status = $('select[name=\'filter_listing_status\']').val();
        if (filter_listing_status != '*') {
            url += '&filter_listing_status=' + encodeURIComponent(filter_listing_status);
        }
        location = url;
    });

    $('.date').datetimepicker({
        pickTime: false
    });

    $('#errorMessageModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var message = button.data('message') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this);
        if (message != 'undefined') {
            modal.find('.modal-body').text(message)
        }
    });

    $('#button-refresh').click(function (e) {
        var url = 'index.php?route=<?php echo $module_path; ?>/productListing&<?php echo $session_token_key; ?>=<?php echo $token; ?>';
        location = url;
    });
</script>

<?php echo $footer; ?>

