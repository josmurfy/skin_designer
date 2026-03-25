<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <?php if ($squareup_merchant_id) { ?>
                    <button type="button" form="form-square-checkout" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <?php } else { ?>
                    <span data-toggle="tooltip" title="<?php echo $text_please_connect; ?>">
                        <button disabled class="btn btn-primary"><i class="fa fa-save"></i></button>
                    </span>
                <?php } ?>
                <a href="<?php echo $help; ?>" data-toggle="tooltip" title="<?php echo $button_help; ?>" class="btn btn-info" target="_blank"><i class="fa fa-question-circle"></i></a>
                <?php if ($squareup_merchant_id) { ?>
                    <a href="<?php echo $on_demand_cron; ?>" id="on-demand-cron" data-toggle="tooltip" title="<?php echo $button_on_demand_cron; ?>" class="btn btn-info"><i class="fa fa-refresh"></i></a>
                <?php } ?>
                <?php if ($can_modify_geo_zones) { ?>
                    <a href="<?php echo $setup_geo_zones; ?>" data-toggle="tooltip" title="<?php echo $button_geo_zones; ?>" class="btn btn-info"><i class="fa fa-globe"></i></a>
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
        <?php if ($error_warning) { ?>
          <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div>
        <?php } ?>

        <?php foreach ($alerts as $alert) { ?>
            <div class="alert alert-<?php echo $alert['type']; ?>"><i class="fa fa-<?php echo $alert['icon']; ?>"></i>&nbsp;<?php echo $alert['text']; ?>
                <?php if (!empty($alert['non_dismissable'])) { ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i>&nbsp;<?php echo $text_edit_heading; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-square-checkout" class="form-horizontal">
                    <input type="hidden" name="squareup_profile_cancel_status" value="1" />
                    <input type="hidden" name="squareup_card" value="1" />
                    <input type="hidden" name="squareup_admin_url_transaction" value="<?php echo $squareup_admin_url_transaction; ?>" />
                    <input type="hidden" name="squareup_admin_url_settings" value="<?php echo $squareup_admin_url_settings; ?>" />
                    <ul class="nav nav-tabs">
                        <li><a href="#tab-setting" data-toggle="tab"><i class="fa fa-gear"></i>&nbsp;<?php echo $tab_setting; ?></a></li>
                        <li><a href="#tab-transaction" data-toggle="tab"><i class="fa fa-list"></i>&nbsp;<?php echo $tab_transaction; ?></a></li>
                        <li><a href="#tab-recurring" data-toggle="tab"><i class="fa fa-hourglass-half"></i>&nbsp;<?php echo $tab_recurring; ?></a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="tab-setting">
                            <fieldset>
                                <legend>
                                    <span>
                                        <?php if ($squareup_merchant_id) { ?>
                                            <?php echo $text_connection_section; ?> - <?php echo $text_connected; ?>
                                        <?php } else { ?>
                                            <?php echo $text_connection_section; ?> - <?php echo $text_not_connected; ?>
                                        <?php } ?>
                                    </span>
                                    <div class="pull-right">
                                        <a target="_blank" href="<?php echo $url_video_help; ?>" data-toggle="tooltip" title="<?php echo $text_video_help; ?>" class="btn btn-info btn-sm"><i class="fa fa-video-camera"></i>&nbsp;&nbsp;<?php echo $button_video_help; ?></a>
                                        <a target="_blank" href="<?php echo $url_integration_settings_help; ?>" data-toggle="tooltip" title="<?php echo $text_integration_settings_help; ?>" class="btn btn-info btn-sm"><i class="fa fa-question-circle"></i></a>
                                    </div>
                                </legend>

                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input_squareup_client_id">
                                        <span data-toggle="tooltip" title="<?php echo $text_client_id_help; ?>"><?php echo $text_client_id_label; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" value="<?php echo $squareup_client_id; ?>" placeholder="<?php echo $text_client_id_placeholder; ?>" id="input_squareup_client_id" class="form-control"/>
                                        <?php if ($error_client_id) { ?>
                                            <div class="text-danger"><?php echo $error_client_id; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input_squareup_client_secret">
                                        <span data-toggle="tooltip" title="<?php echo $text_client_secret_help; ?>"><?php echo $text_client_secret_label; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" value="<?php echo $squareup_client_secret; ?>" placeholder="<?php echo $text_client_secret_placeholder; ?>" id="input_squareup_client_secret" class="form-control"/>
                                        <?php if ($error_client_secret) { ?>
                                            <div class="text-danger"><?php echo $error_client_secret; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input_squareup_redirect_uri_static">
                                        <span data-toggle="tooltip" title="<?php echo $text_redirect_uri_help; ?>"><?php echo $text_redirect_uri_label; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input readonly type="text" id="input_squareup_redirect_uri_static" name="squareup_redirect_uri_static" class="form-control" value="<?php echo $squareup_redirect_uri; ?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input_squareup_webhook_url_static">
                                        <span data-toggle="tooltip" title="<?php echo $text_webhook_url_help; ?>"><?php echo $text_webhook_url_label; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input readonly type="text" id="input_squareup_webhook_url_static" name="squareup_webhook_url_static" class="form-control" value="<?php echo $squareup_webhook_url; ?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input_squareup_webhook_signature">
                                        <span data-toggle="tooltip" title="<?php echo $text_webhook_signature_help; ?>"><?php echo $text_webhook_signature_label; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" id="input_squareup_webhook_signature" name="squareup_webhook_signature" class="form-control" value="<?php echo $squareup_webhook_signature; ?>" placeholder="<?php echo $text_webhook_signature_label; ?>" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-2"></div>
                                    <div class="col-sm-10">
                                        <?php if ($squareup_merchant_id) { ?>
                                            <span data-toggle="tooltip" title="<?php echo $text_disabled_connect_help_text; ?>">
                                                <a id="reconnect-button" href="javascript:void(0)" class="btn btn-primary btn-lg btn-connect" ><?php echo $button_reconnect; ?></a>
                                            </span>
                                            <span id="connect-error"></span>
                                            <p><?php echo $text_connected_info; ?></p>
                                        <?php } else { ?>
                                            <div class="alert alert-info">
                                                <ul>
                                                    <li><?php echo $text_not_connected_info_1; ?></li>
                                                    <li><?php echo $text_not_connected_info_2; ?></li>
                                                </ul>
                                            </div>
                                            <span data-toggle="tooltip" title="<?php echo $text_disabled_connect_help_text; ?>">
                                                <a id="connect-button" href="javascript:void(0)" class="btn btn-primary btn-lg btn-connect"><?php echo $button_connect; ?></a>
                                            </span>
                                            <span id="connect-error"></span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend><?php echo $text_cron_settings; ?></legend>
                                <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $text_recurring_info; ?></div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-original-title="<?php echo $help_local_cron; ?>"><?php echo $text_local_cron; ?></span></label>
                                    <div class="col-sm-10">
                                        <input readonly type="text" class="form-control" value="<?php echo $squareup_cron_command; ?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span data-toggle="tooltip" data-original-title="<?php echo $help_remote_cron; ?>"><?php echo $text_remote_cron; ?></span></label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <input readonly type="text" name="squareup_cron_url" id="input_squareup_cron_url" class="form-control" value="" />
                                            <div data-toggle="tooltip" data-original-title="<?php echo $text_refresh_token; ?>" class="input-group-addon btn btn-primary" id="refresh-cron-token">
                                                <i class="fa fa-refresh"></i>
                                            </div>
                                        </div>
                                        <input id="input_squareup_cron_token" type="hidden" name="squareup_cron_token" value="<?php echo $squareup_cron_token; ?>" />
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="checkbox_squareup_cron_acknowledge"><?php echo $entry_setup_confirmation; ?></label>
                                    <div class="col-sm-10">
                                        <label class="checkbox-inline">
                                            <input id="checkbox_squareup_cron_acknowledge" type="checkbox" value="1" <?php if ($squareup_cron_acknowledge) { ?> checked <?php } ?> name="squareup_cron_acknowledge" /> <?php echo $text_acknowledge_cron; ?>
                                        </label>

                                        <?php if ($error_cron_acknowledge) { ?>
                                            <div class="text-danger"><?php echo $error_cron_acknowledge; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="dropdown_squareup_cron_email_status"><span data-toggle="tooltip" data-original-title="<?php echo $help_cron_email_status; ?>"><?php echo $text_cron_email_status; ?></span></label>
                                    <div class="col-sm-10">
                                        <select id="dropdown_squareup_cron_email_status" name="squareup_cron_email_status" class="form-control">
                                            <option value="1" <?php if ($squareup_cron_email_status == '1') { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                            <option value="0" <?php if ($squareup_cron_email_status == '0') { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="col-sm-2 control-label" for="input_squareup_cron_email"><span data-toggle="tooltip" data-original-title="<?php echo $help_cron_email; ?>"><?php echo $text_cron_email; ?></span></label>
                                    <div class="col-sm-10">
                                        <input id="input_squareup_cron_email" name="squareup_cron_email" type="text" class="form-control" value="<?php echo $squareup_cron_email; ?>"/>
                                        <?php if ($error_cron_email) { ?>
                                            <div class="text-danger"><?php echo $error_cron_email; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend><?php echo $text_basic_settings; ?></legend>
                                <div id="container_squareup_location_id" class="form-group required">
                                    <label class="col-sm-2 control-label" for="dropdown_squareup_location_id"><span data-toggle="tooltip" title="<?php echo $text_location_help; ?>"><?php echo $text_location_label; ?></span></label>
                                    <div class="col-sm-10">
                                        <select name="squareup_location_id" id="dropdown_squareup_location_id" class="form-control" <?php if (!$squareup_locations) { ?> disabled <?php } ?>>
                                            <?php if (is_array($squareup_locations)) : ?>
                                                <?php foreach ($squareup_locations as $location) { ?>
                                                    <option value="<?php echo $location['id']; ?>" <?php if ($location['id'] == $squareup_location_id) { ?> selected <?php } ?>><?php echo $location['name']; ?></option>
                                                <?php } ?>
                                            <?php endif; ?>
                                        </select>
                                        <?php if ($error_location) { ?>
                                            <div class="text-danger"><?php echo $error_location; ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="dropdown_squareup_delay_capture"><span data-toggle="tooltip" title="<?php echo $text_delay_capture_help; ?>"><?php echo $text_delay_capture_label; ?></span></label>
                                    <div class="col-sm-10">
                                        <select name="squareup_delay_capture" id="dropdown_squareup_delay_capture" class="form-control">
                                            <option value="1" <?php if ($squareup_delay_capture == 1) { ?> selected <?php } ?>><?php echo $text_authorize_label; ?></option>
                                            <option value="0" <?php if ($squareup_delay_capture == 0) { ?> selected <?php } ?>><?php echo $text_sale_label; ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="input_squareup_total">
                                        <span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span>
                                    </label>
                                    <div class="col-sm-10">
                                        <input type="text" name="squareup_total" value="<?php echo $squareup_total; ?>" placeholder="<?php echo $entry_total; ?>" id="squareup_total" class="form-control"/>
                                    </div>
                                </div>

                                <hr />
                                <div class="alert alert-info">
                                    <p><i class="fa fa-info-circle"></i> <?php echo $text_inventory_sync_info; ?></p>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="dropdown_squareup_sync_source"><span data-toggle="tooltip" title="<?php echo $text_sync_source_help; ?>"><?php echo $text_sync_source_label; ?></span></label>
                                    <div class="col-sm-10">
                                        <select name="squareup_sync_source" id="dropdown_squareup_sync_source" class="form-control">
                                            <option value="opencart" <?php if ($squareup_sync_source == 'opencart') { ?> selected <?php } ?>><?php echo $text_opencart_sync_option; ?></option>
                                            <option value="none" <?php if ($squareup_sync_source == 'none') { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="dropdown_squareup_inventory_sync"><span data-toggle="tooltip" title="<?php echo $text_inventory_sync_help; ?>"><?php echo $text_inventory_sync_label; ?></span></label>
                                    <div class="col-sm-10">
                                        <select name="squareup_inventory_sync" id="dropdown_squareup_inventory_sync" class="form-control">
                                            <option value="inventory_single" <?php if ($squareup_inventory_sync == 'inventory_single') { ?> selected <?php } ?>><?php echo $text_inventory_sync_single; ?></option>
                                            <option value="inventory_all" <?php if ($squareup_inventory_sync == 'inventory_all') { ?> selected <?php } ?>><?php echo $text_inventory_sync_all; ?></option>
                                            <option value="none" <?php if ($squareup_inventory_sync == 'none') { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend><?php echo $text_order_status_settings; ?></legend>
                                <div id="order_status_settings_intro" style="display: none;">
                                    <p><?php echo $text_order_status_settings_info; ?></p>
                                    <p><button id="order_status_settings_expand" class="btn btn-default"><i class="fa fa-plus-square-o"></i> <?php echo $button_edit_pairings; ?></button></p>
                                </div>
                                <div id="order_status_settings">
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_status_authorized"><span data-toggle="tooltip" title="<?php echo $squareup_status_comment_authorized; ?>"><?php echo $entry_status_authorized_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_status_authorized" id="dropdown_squareup_status_authorized" class="form-control">
                                                <option value><?php echo $text_select_status; ?></option>
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $squareup_status_authorized) { ?> selected <?php } ?>><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php if ($error_status_authorized) { ?>
                                                <div class="text-danger"><?php echo $error_status_authorized; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_status_captured"><span data-toggle="tooltip" title="<?php echo $squareup_status_comment_captured; ?>"><?php echo $entry_status_captured_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_status_captured" id="dropdown_squareup_status_captured" class="form-control">
                                                <option value><?php echo $text_select_status; ?></option>
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $squareup_status_captured) { ?> selected <?php } ?>><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php if ($error_status_captured) { ?>
                                                <div class="text-danger"><?php echo $error_status_captured; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_status_voided"><span data-toggle="tooltip" title="<?php echo $squareup_status_comment_voided; ?>"><?php echo $entry_status_voided_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_status_voided" id="dropdown_squareup_status_voided" class="form-control">
                                                <option value><?php echo $text_select_status; ?></option>
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $squareup_status_voided) { ?>selected <?php } ?>><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php if ($error_status_voided) { ?>
                                                <div class="text-danger"><?php echo $error_status_voided; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_status_failed"><span data-toggle="tooltip" title="<?php echo $squareup_status_comment_failed; ?>"><?php echo $entry_status_failed_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_status_failed" id="dropdown_squareup_status_failed" class="form-control">
                                                <option value><?php echo $text_select_status; ?></option>
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $squareup_status_failed) { ?> selected <?php } ?>><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php if ($error_status_failed) { ?>
                                                <div class="text-danger"><?php echo $error_status_failed; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_status_partially_refunded"><span data-toggle="tooltip" title="<?php echo $squareup_status_comment_partially_refunded; ?>"><?php echo $entry_status_partially_refunded_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_status_partially_refunded" id="dropdown_squareup_status_partially_refunded" class="form-control">
                                                <option value><?php echo $text_select_status; ?></option>
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $squareup_status_partially_refunded) { ?> selected <?php } ?>><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php if ($error_status_partially_refunded) { ?>
                                                <div class="text-danger"><?php echo $error_status_partially_refunded; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group required">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_status_fully_refunded"><span data-toggle="tooltip" title="<?php echo $squareup_status_comment_fully_refunded; ?>"><?php echo $entry_status_fully_refunded_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_status_fully_refunded" id="dropdown_squareup_status_fully_refunded" class="form-control">
                                                <option value><?php echo $text_select_status; ?></option>
                                                <?php foreach ($order_statuses as $order_status) { ?>
                                                    <option value="<?php echo $order_status['order_status_id']; ?>" <?php if ($order_status['order_status_id'] == $squareup_status_fully_refunded) { ?> selected <?php } ?>><?php echo $order_status['name']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <?php if ($error_status_fully_refunded) { ?>
                                                <div class="text-danger"><?php echo $error_status_fully_refunded; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend><?php echo $text_advanced_settings; ?></legend>
                                <div id="advanced_settings_intro" style="display: none;">
                                    <p><?php echo $text_advanced_settings_info; ?></p>
                                    <p><button id="advanced_settings_expand" class="btn btn-default"><i class="fa fa-plus-square-o"></i> <?php echo $button_edit_advanced; ?></button></p>
                                </div>
                                <div id="advanced_settings">
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label">
                                            <span data-toggle="tooltip" title="<?php echo $text_payment_method_name_help; ?>"><?php echo $text_payment_method_name_label; ?></span>
                                        </label>
                                        <div class="col-sm-10">
                                            <?php foreach ($languages as $language) { ?>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><img src="<?php echo $language['image']; ?>" alt="<?php echo $language['name']; ?>" /></span>
                                                    <input type="text" name="squareup_display_name[<?php echo $language['language_id']; ?>]" value="<?php echo !empty($squareup_display_name[$language['language_id']]) ? $text_payment_method_name_placeholder : ''; ?>" placeholder="<?php echo $text_payment_method_name_placeholder; ?>" class="form-control"/>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                      <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
                                      <div class="col-sm-10">
                                        <select name="squareup_geo_zone_id" id="input-geo-zone" class="form-control">
                                            <option value="0"><?php echo $text_all_zones; ?></option>
                                            <?php foreach ($geo_zones as $geo_zone) { ?>
                                                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" <?php if ($geo_zone['geo_zone_id'] == $squareup_geo_zone_id) { ?> selected <?php } ?>><?php echo $geo_zone['name']; ?></option>
                                            <?php } ?>
                                        </select>
                                      </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input_squareup_sort_order">
                                            <?php echo $entry_sort_order; ?>
                                        </label>
                                        <div class="col-sm-10">
                                            <input type="text" name="squareup_sort_order" value="<?php echo $squareup_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input_squareup_sort_order" class="form-control"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_debug"><span data-toggle="tooltip" title="<?php echo $text_debug_help; ?>"><?php echo $text_debug_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_debug" id="dropdown_squareup_debug" class="form-control">
                                                <option value="1" <?php if ($squareup_debug == 1) { ?> selected <?php } ?>><?php echo $text_debug_enabled; ?></option>
                                                <option value="0" <?php if ($squareup_debug == 0) { ?> selected <?php } ?>><?php echo $text_debug_disabled; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_enable_icon"><span data-toggle="tooltip" title="<?php echo $text_enable_icon_help; ?>"><?php echo $text_enable_icon_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_icon_status" id="dropdown_squareup_enable_icon" class="form-control">
                                                <option value="1" <?php if ($squareup_icon_status == 1) { ?> selected <?php } ?>><?php echo $text_visible_option; ?></option>
                                                <option value="0" <?php if ($squareup_icon_status == 0) { ?> selected <?php } ?>><?php echo $text_hidden_option; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_enable_accepted_cards"><span data-toggle="tooltip" title="<?php echo $text_enable_accepted_cards_help; ?>"><?php echo $text_enable_accepted_cards_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_accepted_cards_status" id="dropdown_squareup_enable_accepted_cards" class="form-control">
                                                <option value="1" <?php if ($squareup_accepted_cards_status == 1) { ?> selected <?php } ?>><?php echo $text_visible_option; ?></option>
                                                <option value="0" <?php if ($squareup_accepted_cards_status == 0) { ?> selected <?php } ?>><?php echo $text_hidden_option; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_guest"><span data-toggle="tooltip" title="<?php echo $text_guest_help; ?>"><?php echo $text_guest_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <select name="squareup_guest" id="dropdown_squareup_guest" class="form-control">
                                                <option value="1" <?php if ($squareup_guest == 1) { ?> selected <?php } ?>><?php echo $text_guest_enabled; ?></option>
                                                <option value="0" <?php if ($squareup_guest == 0) { ?> selected <?php } ?>><?php echo $text_guest_disabled; ?></option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input_merchant_name">
                                            <?php echo $text_merchant_name_label; ?>
                                        </label>
                                        <div class="col-sm-10">
                                            <input type="text" name="merchant_name" value="<?php echo $squareup_merchant_name; ?>" placeholder="<?php echo $text_merchant_name_placeholder; ?>" id="input_merchant_name" class="form-control" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="access_token_expires_time">
                                            <?php echo $text_access_token_expires_label; ?>
                                        </label>
                                        <div class="col-sm-10">
                                            <input type="text" name="access_token_expires" value="<?php echo $access_token_expires_time; ?>" placeholder="<?php echo $text_access_token_expires_placeholder; ?>" id="access_token_expires_time" class="form-control" readonly />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="input_squareup_cron_standard_period">
                                            <?php echo $entry_cron_standard_period; ?>
                                        </label>
                                        <div class="col-sm-10">
                                            <input type="number" min="1" max="<?php echo $max_standard_period; ?>" name="squareup_cron_standard_period" value="<?php echo $squareup_cron_standard_period; ?>" placeholder="<?php echo $entry_cron_standard_period; ?>" id="input_squareup_cron_standard_period" class="form-control"/>
                                            <?php if ($error_cron_standard_period) { ?>
                                                <div class="text-danger"><?php echo $error_cron_standard_period; ?></div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-2 control-label" for="cron_status_text">
                                            <?php echo $text_cron_status_text_label; ?>
                                        </label>
                                        <div class="col-sm-10">
                                            <div class="input-group">
                                                <input type="text" name="cron_status_text" value="" id="cron_status_text" class="form-control" readonly />
                                                <span class="input-group-addon" style="border: none; background-color: transparent; padding: 0 0 0 10px;">
                                                    <a href="<?php echo $url_download_sync_log; ?>" data-toggle="tooltip" title="" class="btn btn-primary" data-original-title="<?php echo $button_download_log; ?>"><i class="fa fa-download"></i></a>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="squareup_ad_hoc_sync" value="1">
                                    <!--div class="form-group">
                                        <label class="col-sm-2 control-label" for="dropdown_squareup_ad_hoc_sync"><span data-toggle="tooltip" title="<?php echo $text_ad_hoc_sync_help; ?>"><?php echo $text_ad_hoc_sync_label; ?></span></label>
                                        <div class="col-sm-10">
                                            <div class="alert alert-info"><?php echo $text_ad_hoc_warning; ?></div>
                                            <select name="squareup_ad_hoc_sync" id="dropdown_squareup_ad_hoc_sync" class="form-control">
                                                <option value="1" <?php if ($squareup_ad_hoc_sync == 1) { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                                <option value="0" <?php if ($squareup_ad_hoc_sync == 0) { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                            </select>
                                        </div>
                                    </div-->
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend><?php echo $text_extension_status_heading; ?></legend>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label" for="dropdown_squareup_status"><span data-toggle="tooltip" title="<?php echo $text_extension_status_help; ?>"><?php echo $text_extension_status; ?></span></label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <select name="squareup_status" id="dropdown_squareup_status" class="form-control">
                                                <option value="1" <?php if ($squareup_status == 1) { ?> selected <?php } ?>><?php echo $text_extension_status_enabled; ?></option>
                                                <option value="0" <?php if ($squareup_status == 0) { ?> selected <?php } ?>><?php echo $text_extension_status_disabled; ?></option>
                                            </select>
                                            <span class="input-group-addon" style="border: none; background-color: transparent; padding: 0 0 0 10px;">
                                                <?php if ($squareup_merchant_id) { ?>
                                                    <button type="button" form="form-square-checkout" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                                                <?php } else { ?>
                                                    <span data-toggle="tooltip" title="<?php echo $text_please_connect; ?>">
                                                        <button disabled class="btn btn-primary"><i class="fa fa-save"></i></button>
                                                    </span>
                                                <?php } ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <div class="tab-pane" id="tab-transaction">
                            <div id="transaction-alert" data-message="<?php echo $text_loading; ?>"></div>
                            <div class="text-right">
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th class="text-left hidden-xs"><?php echo $column_transaction_id; ?></th> 
                                            <th class="text-left"><?php echo $column_customer; ?></th>
                                            <th class="text-left hidden-xs"><?php echo $column_order_id; ?></th>
                                            <th class="text-left hidden-xs"><?php echo $column_type; ?></th>
                                            <th class="text-left hidden-xs"><?php echo $column_amount; ?></th>
                                            <th class="text-left hidden-xs"><?php echo $column_refunds; ?></th>
                                            <th class="text-left hidden-xs hidden-sm"><?php echo $column_ip; ?></th>
                                            <th class="text-left"><?php echo $column_date_created; ?></th>
                                            <th class="text-right"><?php echo $column_action; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="transactions">
                                    </tbody>
                                </table>
                                <div id="transactions_pagination" class="pagination"></div>
                            </div>
                        </div>
                        <div class="tab-pane" id="tab-recurring">
                            <div class="form-group">
                                <label class="control-label col-sm-2" for="dropdown_squareup_recurring_status"><span data-toggle="tooltip" data-original-title="<?php echo $help_recurring_status; ?>"><?php echo $text_recurring_status; ?></span></label>
                                <div class="col-sm-10">
                                    <select id="dropdown_squareup_recurring_status" name="squareup_recurring_status" class="form-control">
                                        <option value="1" <?php if ($squareup_recurring_status == '1') { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                        <option value="0" <?php if ($squareup_recurring_status == '0') { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                    </select>
                                </div>
                            </div>
                            <fieldset>
                                <legend><?php echo $text_customer_notifications; ?></legend>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="dropdown_squareup_notify_recurring_success"><span data-toggle="tooltip" data-original-title="<?php echo $help_notify_recurring_success; ?>"><?php echo $text_notify_recurring_success; ?></span></label>
                                    <div class="col-sm-10">
                                        <select id="dropdown_squareup_notify_recurring_success" name="squareup_notify_recurring_success" class="form-control">
                                            <option value="1" <?php if ($squareup_notify_recurring_success == '1') { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                            <option value="0" <?php if ($squareup_notify_recurring_success == '0') { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2" for="dropdown_squareup_notify_recurring_fail"><span data-toggle="tooltip" data-original-title="<?php echo $help_notify_recurring_fail; ?>"><?php echo $text_notify_recurring_fail; ?></span></label>
                                    <div class="col-sm-10">
                                        <select id="dropdown_squareup_notify_recurring_fail" name="squareup_notify_recurring_fail" class="form-control">
                                            <option value="1" <?php if ($squareup_notify_recurring_fail == '1') { ?> selected <?php } ?>><?php echo $text_enabled; ?></option>
                                            <option value="0" <?php if ($squareup_notify_recurring_fail == '0') { ?> selected <?php } ?>><?php echo $text_disabled; ?></option>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </div>

                    <?php if ($initial_sync_not_performed) { ?>
                        <div class="modal fade" id="squareup-sync-modal" data-backdrop="static" data-keyboard="">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title"><?php echo $text_select_initial_sync_mode; ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="alert alert-info"><i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;<?php echo $text_loading; ?></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" form="form-square-checkout" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="squareup-on-demand-cron-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo $text_on_demand_cron_heading; ?></h4>
                </div>
                <div class="modal-body form-horizontal">
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-circle"></i> <?php echo $text_on_demand_cron_intro; ?>
                    </div>

                    <div class="form-group required">
                        <div class="col-sm-12">
                            <label class="checkbox-inline">
                                <input id="checkbox_on_demand_cron_acknowledge" type="checkbox" value="1" name="payment_on_demand_cron_acknowledge" /> <?php echo $text_acknowledge_on_demand_cron; ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $text_close; ?></button>
                    <button id="squareup-on-demand-cron-ok" type="button" class="btn btn-primary"><?php echo $text_begin_on_demand_sync; ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="squareup-confirm-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo $text_confirm_action; ?></h4>
                </div>
                <div class="modal-body">
                    <h4 id="squareup-confirm-modal-content"></h4>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $text_close; ?></button>
                    <button id="squareup-confirm-ok" type="button" class="btn btn-primary"><?php echo $text_ok; ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="squareup-refund-modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-center">
                        <i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;<?php echo $text_loading; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($has_new_tax_rates) { ?>
        <div class="modal fade" id="squareup-tax-rate-modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title"><?php echo $text_setup_tax_rate; ?></h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal" id="squareup-tax-rate-form">
                            <p class="alert alert-info"><i class="fa fa-exclamation-circle"></i>&nbsp;<?php echo $text_setup_tax_rate_geo_zone_intro; ?></p>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-left"><?php echo $column_tax_rate_name; ?></th>
                                            <th class="text-left"><?php echo $column_tax_rate_percentage; ?></th>
                                            <th class="text-left"><?php echo $column_geo_zone; ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="squareup-tax-rates">
                                        <?php foreach ($new_tax_rates as $tax_rate) { ?>
                                            <tr data-tax-rate-id="<?php echo $tax_rate['tax_rate_id']; ?>">
                                                <td><?php echo $tax_rate['name']; ?></td>
                                                <td><strong><?php echo $tax_rate['percentage']; ?></strong></td>
                                                <td>
                                                    <select class="form-control" name="tax_rate[<?php echo $tax_rate['tax_rate_id']; ?>]">
                                                        <option value=""><?php echo $text_select; ?></option>
                                                        <?php foreach ($geo_zones as $geo_zone) { ?>
                                                            <option value="<?php echo $geo_zone['geo_zone_id']; ?>" <?php if ($geo_zone['geo_zone_id'] == $tax_rate['suggested_geo_zone_id']) { ?> selected <?php } ?><?php echo $geo_zone['name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <div id="squareup-tax-rate-error" class="text-left"></div>
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $text_close; ?></button>
                        <button id="squareup-tax-rate-ok" type="button" class="btn btn-primary"><?php echo $text_ok; ?></button>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<script type="text/javascript">
$(document).ready(function() {
    <?php if (!$old_api) : ?>
        var token = '';

        function apiLogin() {
            $.ajax({
                url: '<?php echo $catalog; ?>index.php?route=api/login',
                type: 'post',
                dataType: 'json',
                data: 'key=<?php echo $api_key; ?>',
                crossDomain: true,
                success: function(json) {
                    $('.alert-login').remove();

                    if (json['error']) {
                        if (json['error']['key']) {
                            $('#content > .container-fluid').prepend('<div class="alert alert-danger alert-login"><i class="fa fa-exclamation-circle"></i> ' + json['error']['key'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                        }

                        if (json['error']['ip']) {
                            $('#content > .container-fluid').prepend('<div class="alert alert-danger alert-login"><i class="fa fa-exclamation-circle"></i> ' + json['error']['ip'] + ' <button type="button" id="button-ip-add" data-loading-text="<?php echo $text_loading_short; ?>" class="btn btn-danger btn-xs pull-right"><i class="fa fa-plus"></i> <?php echo $button_ip_add; ?></button></div>');
                        }
                    }

                    if (json['token']) {
                        token = json['token'];
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        }

        $(document).delegate('#button-ip-add', 'click', function() {
            $.ajax({
                url: 'index.php?route=user/api/addip&token=<?php echo $token; ?>&api_id=<?php echo $api_id; ?>',
                type: 'post',
                data: 'ip=<?php echo $api_ip; ?>',
                dataType: 'json',
                beforeSend: function() {
                    $('#button-ip-add').button('loading');
                },
                complete: function() {
                    $('#button-ip-add').button('reset');
                },
                success: function(json) {
                    $('.alert-login').remove();

                    if (json['error']) {
                        $('#content > .container-fluid').prepend('<div class="alert alert-danger alert-login"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                    }

                    if (json['success']) {
                        apiLogin();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });

        apiLogin();
    <?php else : ?>
        var token = '<?php echo $token; ?>';
    <?php endif; ?>

    var triggerConnectButtons = function() {
        if ($('#input_squareup_client_id').val() != '' && $('#input_squareup_client_secret').val() != '') {
            $('.btn-connect').removeClass('disabled');
        } else {
            $('.btn-connect').addClass('disabled');
        }
    }

    var setCronUrl = function() {
        $('#input_squareup_cron_url').val(
            "<?php echo $squareup_cron_url; ?>".replace('{CRON_TOKEN}', $('#input_squareup_cron_token').val())
        );
    }

    var randomString = function() {
        return (Math.random() * 100).toString(16).replace('.', '');
    }

    var onConnectClick = function(event) {
        event.preventDefault();
        event.stopPropagation();

        $.ajax({
            url: '<?php echo $connect; ?>',
            dataType: 'json',
            type: 'POST',
            data : {
                squareup_client_id: $('#input_squareup_client_id').val(),
                squareup_client_secret: $('#input_squareup_client_secret').val(),
                squareup_webhook_signature: $('#input_squareup_webhook_signature').val()
            },
            beforeSend : function() {
                $('#connect-error').empty();
            },
            success: function(json) {
                if (json.redirect) {
                    document.location = json.redirect;
                }

                if (json.error) {
                    $('#connect-error').html('<span class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="X"><span aria-hidden="true">&times;</span></button><i class="fa fa-exclamation-circle"></i>&nbsp;' + json.error + '</span>');
                }
            }
        });
    }

    var listTransactions = function(page) {
        $.ajax({
          url : '<?php echo $url_list_transactions; ?>'.replace('{PAGE}', page ? page : transactionListPage),
          dataType : 'json',
          beforeSend : function() {
            $('#refresh_transactions').button('loading');
            $('#transactions_pagination').empty();
            $('#transactions').html('<tr><td colspan="9" class="text-center"><i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;<?php echo $text_loading; ?></td></tr>');
          },
          success : function(data) {
            var html = '';

            if (data.transactions.length) {
              for (var i in data.transactions) {
                var row = data.transactions[i];

                if (row.order_history_data) {
                    <?php if ($is_oc15) : ?>
                        $.ajax({
                            url: 'index.php?route=sale/order/history&token=<?php echo $token; ?>&store_id=' + row.store_id + '&order_id=' + row.order_id,
                            type: 'post',
                            dataType: 'html',
                            data: row.order_history_data
                        });
                    <?php else : ?>
                        $.ajax({
                            url: '<?php echo (!$old_api) ? ($catalog . "index.php?route=api/order/history&token=' + token + '") : ("index.php?route=sale/order/api&token=" . $token . "&api=api/order/history"); ?>&store_id=' + row.store_id + '&order_id=' + row.order_id,
                            type: 'post',
                            dataType: 'json',
                            data: row.order_history_data
                        });
                    <?php endif; ?>
                }

                html += '<tr>';
                html += '<td class="text-left hidden-xs">' + (!row.is_merchant_transaction ? '<i class="fa fa-warning text-warning" data-toggle="tooltip" title="' + row.text_different_merchant + '"></i>&nbsp;' : '') + row.transaction_id + '</td>';
                html += '<td class="text-left hidden-xs">' + row.customer + '</td>';
                html += '<td class="text-left"><a target="_blank" href="' + row.url_order + '">' + row.order_id + '</td>';
                html += '<td class="text-left hidden-xs">' + row.status + '</td>';
                html += '<td class="text-left hidden-xs">' + row.amount + '</td>';
                html += '<td class="text-left hidden-xs">' + row.amount_refunded + '</td>';
                html += '<td class="text-left hidden-xs hidden-sm">' + row.ip + '</td>';
                html += '<td class="text-left">' + row.date_created + '</td>';
                html += '<td class="text-right">';

                if (row.is_merchant_transaction) {
                    switch (row.type) {
                        case "AUTHORIZED" : {
                            html += '<a class="btn btn-success" data-url-transaction-capture="' + row.url_capture + '" data-confirm-capture="' + row.confirm_capture + '"><?php echo $text_capture; ?></a> ';
                            html += '<a class="btn btn-warning" data-url-transaction-void="' + row.url_void + '" data-confirm-void="' + row.confirm_void + '"><?php echo $text_void; ?></a> ';
                        } break;

                        case "CAPTURED" : {
                            if (row.is_fully_refunded) {
                                break;
                            }
                            html += '<a class="btn btn-danger" data-url-transaction-refund="' + row.url_refund + '" data-url-transaction-refund-modal="' + row.url_refund_modal + '"><?php echo $text_refund; ?></a> ';
                        } break;
                    }
                }

                html += ' <a class="btn btn-info" href="' + row.url_info + '"><?php echo $text_view; ?></a>';
                html += '</td>';
                html += '</tr>';
              }
            } else {
              html += '<tr>';
              html += '<td class="text-center" colspan="9"><?php echo $text_no_transactions; ?></td>';
              html += '</tr>';
            }

            $('#transactions').html(html);
            
            $('#transactions_pagination').html(data.pagination).find('a[href]').each(function(index,element) {
              $(this).click(function(e) {
                e.preventDefault();

                transactionListPage = isNaN($(this).attr('href')) ? 1 : $(this).attr('href');

                listTransactions();
              })
            });
          },
          complete : function() {
            $('#refresh_transactions').button('reset');
          }
        });
    }

    var transactionLoading = function() {
        var message = $('#transaction-alert').attr('data-message');

        $('#transaction-alert').html('<div class="text-center alert alert-info"><i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;' + message + '</div>');
    }

    var transactionError = function(message) {
        $('#transaction-alert').html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="X"><span aria-hidden="true">&times;</span></button><i class="fa fa-exclamation-circle"></i>&nbsp;' + message + '</div>');
    }

    var transactionSuccess = function(message) {
        $('#transaction-alert').html('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-label="X"><span aria-hidden="true">&times;</span></button><i class="fa fa-exclamation-circle"></i>&nbsp;' + message + '</div>');
    }

    var addOrderHistory = function(data, success_callback) {
        <?php if ($is_oc15) : ?>
            $.ajax({
                url: 'index.php?route=sale/order/history&token=<?php echo $token; ?>&store_id=' + data.store_id + '&order_id=' + data.order_id,
                type: 'post',
                dataType: 'html',
                data: data,
                success: function(json) {
                    success_callback();
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    transactionError(thrownError);
                    enableTransactionButtons();
                }
            });
        <?php else : ?>
            $.ajax({
                url: '<?php echo (!$old_api) ? ($catalog . "index.php?route=api/order/history&token=' + token + '") : ("index.php?route=sale/order/api&token=" . $token . "&api=api/order/history"); ?>&store_id=' + data.store_id + '&order_id=' + data.order_id,
                type: 'post',
                dataType: 'json',
                data: data,
                success: function(json) {
                    if (json['error']) {
                        transactionError(json['error']);
                        enableTransactionButtons();
                    }

                    if (json['success'] && typeof success_callback == 'function') {
                        success_callback();
                    }
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    transactionError(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    enableTransactionButtons();
                }
            });
        <?php endif; ?>
    }

    var transactionRequest = function(type, url, data) {
        $.ajax({
            url : url,
            dataType : 'json',
            type : type,
            data : data,
            beforeSend : transactionLoading,
            success : function(json) {
                if (json.error) {
                    transactionError(json.error);
                    enableTransactionButtons();
                }

                if (json.success && json.order_history_data) {
                    addOrderHistory(json.order_history_data, function() {
                        transactionSuccess(json.success);
                        listTransactions();
                    });
                }
            },
            error : function(xhr, ajaxSettings, thrownError) {
                transactionError(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                enableTransactionButtons();
            }
        });
    }

    var disableTransactionButtons = function() {
        $('*[data-url-transaction-capture], *[data-url-transaction-void], *[data-url-transaction-refund]').attr('disabled', true);
    }

    var enableTransactionButtons = function() {
        $('*[data-url-transaction-capture], *[data-url-transaction-void], *[data-url-transaction-refund]').attr('disabled', false);
    }

    var modalConfirm = function(url, text) {
        var modal = '#squareup-confirm-modal';
        var content = '#squareup-confirm-modal-content';
        var button = '#squareup-confirm-ok';

        $(content).html(text);
        $(button).unbind().click(function() {
            disableTransactionButtons();

            $(modal).modal('hide');

            transactionRequest('GET', url);
        });
        
        $(modal).modal('show');
    }

    var modalRefund = function(url, url_refund_modal) {
        $('#squareup-refund-modal').modal('show');

        var setModalHtml = function(html) {
            $('#squareup-refund-modal .modal-content').html(html);
        }

        $.ajax({
            url : url_refund_modal,
            dataType : 'json',
            success : function(data) {
                if (typeof data.error != 'undefined') {
                    setModalHtml('<div class="modal-body"><div class="alert alert-danger">' + data.error + '</div></div>');
                } else if (typeof data.html != 'undefined') {
                    setModalHtml(data.html);

                    var invalidRefundAmount = function(element) {
                        var value = parseFloat($(element).val().replace(/[^0-9\.\-]/g, ""));
                        var max = parseFloat($(element).attr('data-max-allowed').replace(/[^0-9\.\-]/g, ""));

                        return (value <= 0) || (value > max);
                    };

                    var flow = {
                        itemized : {
                            steps : [
                                "#squareup-refund-step-itemized-refund",
                                "#squareup-refund-step-itemized-restock"
                            ],
                            final : "#squareup-refund-confirm-itemized"
                        },
                        amount : {
                            steps : [],
                            final : "#squareup-refund-confirm-amount"
                        }
                    };

                    var breadcrumb = [];

                    var showScreenById = function(screenId) {
                        $(".squareup-refund-step").hide();
                        $(screenId).show();
                    }

                    $("#squareup-refund-finish").hide();
                    $("#squareup-refund-back").hide();
                    $("#squareup-refund-next").show();
                    showScreenById("#squareup-refund-initial");

                    var showNextScreen = function(type) {
                        var nextScreenIndex = breadcrumb.length;

                        if (typeof flow[type].steps[nextScreenIndex] == 'string') {
                            $("#squareup-refund-finish").hide();
                            $("#squareup-refund-back").show();
                            $("#squareup-refund-next").show();

                            breadcrumb.push(flow[type].steps[nextScreenIndex]);

                            showScreenById(flow[type].steps[nextScreenIndex]);
                        } else if (typeof flow[type].steps[nextScreenIndex] == 'undefined') {
                            $("#squareup-refund-finish").show();
                            $("#squareup-refund-back").show();
                            $("#squareup-refund-next").hide();

                            breadcrumb.push(flow[type].final);

                            showScreenById(flow[type].final);
                        }
                    }

                    $("#squareup-refund-next").click(function(e) {
                        e.preventDefault();

                        if ($(this).attr('disabled')) {
                            return;
                        }

                        var type = $('input[name="refund_type"]:checked').val();

                        showNextScreen(type);

                        if ($('#squareup-refund-step-itemized-restock').is(':visible')) {
                            var amount_input = '#squareup-refund-itemized-insert';

                            if (!$(amount_input)[0].checkValidity() || invalidRefundAmount(amount_input)) {
                                $(amount_input).closest('.form-group').addClass('has-error');
                                showPreviousScreen(type);
                            } else {
                                $(amount_input).closest('.form-group').removeClass('has-error');

                                // No issues here. Restrict the allowed re-stocks according to the quantity selections from the refund screen

                                var text_summary = 
                                    "<?php echo $text_itemized_refund_restock_total; ?>"
                                        .replace(/{price_prefix}/, $(amount_input).attr('data-price-prefix'))
                                        .replace(/{price_suffix}/, $(amount_input).attr('data-price-suffix'))
                                        .replace(/{price}/, $(amount_input).val().replace(/[^0-9\.\-]/g, ""));

                                $('#itemized_refund_restock_total').html(text_summary);
                            }
                        } else if ($('#squareup-refund-confirm-itemized').is(':visible')) {
                            var rows = {};

                            var populateRows = function(index, element) {
                                var order_product_id = $(element).attr('data-order-product-id');
                                var type = $(element).attr('data-type');
                                var quantity = parseInt($(element).val());

                                if (quantity <= 0) {
                                    return;
                                }

                                if (typeof rows[order_product_id] == 'undefined') {
                                    rows[order_product_id] = {
                                        'name' : $(element).closest('tr').find('td.itemized_name').html(),
                                        'model' : $(element).closest('tr').find('td.itemized_model').html(),
                                        'quantity_restock' : 0,
                                        'quantity_refund' : 0
                                    };
                                }

                                rows[order_product_id][type] += quantity;
                            };

                            $('[name^="itemized_restock"]').each(populateRows);
                            $('[name^="itemized_refund"]').each(populateRows);

                            if (Object.keys(rows).length === 0) {
                                $('#itemized_refund_restock_items').html('<div class="alert alert-warning"><?php echo $text_no_items_restock_refund; ?></div>');
                            } else {
                                var html = '';

                                html += '<div class="table-responsive">';
                                html += '<table class="table table-bordered table-hover">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th><?php echo $column_product_name; ?></th>';
                                html += '<th><?php echo $column_product_model; ?></th>';
                                html += '<th><?php echo $column_product_quantity_refund; ?></th>';
                                html += '<th><?php echo $column_product_quantity_restock; ?></th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';

                                $.each(rows, function(index, row) {
                                    html += '<tr>';
                                    html += '<td>' + row.name + '</td>';
                                    html += '<td>' + row.model + '</td>';
                                    html += '<td>' + row.quantity_refund + '</td>';
                                    html += '<td>' + row.quantity_restock + '</td>';
                                    html += '</tr>';
                                });

                                html += '</tbody>';
                                html += '</table>';
                                html += '</div>';

                                $('#itemized_refund_restock_items').html(html);
                            }
                        }
                    });

                    var showPreviousScreen = function(type) {
                        breadcrumb.pop();

                        var candidatePreviousScreen = breadcrumb[breadcrumb.length - 1];

                        if (typeof candidatePreviousScreen == 'undefined') {
                            $("#squareup-refund-finish").hide();
                            $("#squareup-refund-back").hide();
                            $("#squareup-refund-next").show();

                            showScreenById("#squareup-refund-initial");
                        } else if (typeof candidatePreviousScreen == 'string') {
                            $("#squareup-refund-finish").hide();
                            $("#squareup-refund-back").show();
                            $("#squareup-refund-next").show();

                            showScreenById(candidatePreviousScreen);
                        }
                    }

                    $("#squareup-refund-back").click(function(e) {
                        e.preventDefault();

                        showPreviousScreen($('input[name="refund_type"]:checked').val());
                    });

                    var refundInputValidate = function() {
                        var result = true;
                        var reason_input = "#squareup-refund-reason-insert";
                        var amount_input = "#squareup-refund-amount-insert";

                        if (!$(reason_input)[0].checkValidity()) {
                            $(reason_input).closest('.form-group').addClass('has-error');
                            result = false;
                        } else {
                            $(reason_input).closest('.form-group').removeClass('has-error');
                        }

                        if (!$(amount_input)[0].checkValidity() || invalidRefundAmount(amount_input)) {
                            $(amount_input).closest('.form-group').addClass('has-error');
                            result = false;
                        } else {
                            $(amount_input).closest('.form-group').removeClass('has-error');
                        }

                        return result;
                    }

                    var validateNext = function(e) {
                        if (parseInt($(this).val()) > parseInt($(this).attr("max")) || parseInt($(this).val()) < 0) {
                            $(this).css('background-color', '#f5c1bb');
                            $("#squareup-refund-next").attr('disabled', true);
                        } else {
                            $(this).css('background-color', 'white');
                            $("#squareup-refund-next").attr('disabled', false);
                        }
                    }

                    $('[name^="itemized_refund"]').change(function(e) {
                        var element = $('#squareup-refund-itemized-insert').first();
                        var currentValue = 0;
                        var price = parseFloat($(this).attr('data-price').replace(/[^0-9\.\-]/g, ""));

                        $('[name^="itemized_refund"]').each(function(index, element) {
                            currentValue += price * parseInt($(element).val());
                        });
                        
                        var max = parseFloat($(element).attr('data-max-allowed').replace(/[^0-9\.\-]/g, ""));

                        if (currentValue > max) {
                            currentValue = max;
                        } else if (currentValue < 0) {
                            currentValue = 0;
                        }

                        $(element).val(currentValue);
                    });

                    $('[name^="itemized_refund"], [name^="itemized_restock"]').change(validateNext);
                    $('[name^="itemized_refund"], [name^="itemized_restock"]').keyup(validateNext);

                    $("#squareup-refund-finish").click(function(e) {
                        e.preventDefault();

                        if ($('input[name="refund_type"]:checked').val() == 'amount') {
                            // Amount Refund - validate the manually inserted amount and prepare the POST request
                            if (!refundInputValidate()) {
                                return;
                            }

                            disableTransactionButtons();

                            $('#squareup-refund-modal').modal('hide');

                            transactionRequest('POST', url, {
                                reason : $("#squareup-refund-reason-insert").val(),
                                amount : $("#squareup-refund-amount-insert").val()
                            });
                        } else {
                            // Itemized Refund - display refund confirmation and prepare the POST request
                            disableTransactionButtons();

                            $('#squareup-refund-modal').modal('hide');

                            var restock = {};
                            var refund = {};

                            $('[name^="itemized_restock"]').each(function(index, element) {
                                var key = $(element).attr('data-order-product-id');
                                var value = parseInt($(element).val());

                                if (value > 0) {
                                    restock[key] = value;
                                }
                            });

                            $('[name^="itemized_refund"]').each(function(index, element) {
                                var key = $(element).attr('data-order-product-id');
                                var value = parseInt($(element).val());

                                if (value > 0) {
                                    refund[key] = value;
                                }
                            });

                            transactionRequest('POST', url, {
                                reason : "<?php echo $text_itemized_refund_reason; ?>",
                                amount : $("#squareup-refund-itemized-insert").val(),
                                restock : restock,
                                refund : refund
                            });
                        }
                    });
                }
            },
            error : function(xhr, ajaxSettings, thrownError) {
                setModalHtml('<div class="modal-body"><div class="alert alert-danger">' + '(' + xhr.statusText + '): ' + xhr.responseText + '</div></div>');
            }
        });
    }

    var transactionListPage = 1;

    $('.nav-tabs a[href="#<?php echo $tab; ?>"]').tab('show');

    <?php if ($order_status_settings_hidden) { ?>
        $('#order_status_settings_intro').show();
        $('#order_status_settings').hide();

        $('#order_status_settings_expand').click(function(e) {
            e.preventDefault();
            e.stopPropagation();

            $('#order_status_settings_intro').hide();
            $('#order_status_settings').slideDown();
        });
    <?php } ?>

    $('#advanced_settings_intro').show();
    $('#advanced_settings').hide();

    $('#advanced_settings_expand').click(function(e) {
        e.preventDefault();
        e.stopPropagation();

        $('#advanced_settings_intro').hide();
        $('#advanced_settings').slideDown();
    });

    <?php if ($error_cron_standard_period) { ?> 
        $('#advanced_settings_expand').trigger('click');
    <?php } ?>

    $('#input_squareup_client_id, #input_squareup_client_secret')
        .change(triggerConnectButtons)
        .keyup(triggerConnectButtons)
        .trigger('change');

    $('#refresh-cron-token').click(function() {
        $('#input_squareup_cron_token').val(randomString() + randomString());
        setCronUrl();
    });

    $('#connect-button').click(onConnectClick);
    
    $('#reconnect-button').click(onConnectClick);

    $(document).on('click', '*[data-url-transaction-capture]', function() {
        if ($(this).attr('disabled')) return;

        modalConfirm(
            $(this).attr('data-url-transaction-capture'),
            $(this).attr('data-confirm-capture')
        );
    });
        
    $(document).on('click', '*[data-url-transaction-void]', function() {
        if ($(this).attr('disabled')) return;

        modalConfirm(
            $(this).attr('data-url-transaction-void'),
            $(this).attr('data-confirm-void')
        );
    });

    $(document).on('click', '*[data-url-transaction-refund]', function() {
        if ($(this).attr('disabled')) return;

        modalRefund($(this).attr('data-url-transaction-refund'), $(this).attr('data-url-transaction-refund-modal'));
    });
    
    $(document).on('click', '#cron_click', function() { 
        $('.nav-tabs a[href="#tab-setting"]').tab('show');

        $('html, body').animate({
            scrollTop: $("#checkbox_squareup_cron_acknowledge").closest('fieldset').offset().top
        }, 2000);
    });

    $(document).on('click', '.focus_connect', function(e) {
        e.preventDefault();

        $('.nav-tabs a[href="#tab-setting"]').tab('show');

        $('html, body').animate({
            scrollTop: $("#input_squareup_client_id").closest('fieldset').offset().top
        }, 2000);
    });

    <?php if ($has_new_tax_rates) { ?>
        $(document).on('click', '#squareup-tax-rate-ok', function() {
            var taxRateError = function(message) {
                $('#squareup-tax-rate-error').html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="X"><span aria-hidden="true">&times;</span></button><i class="fa fa-exclamation-circle"></i>&nbsp;' + message + '</div>');
            };

            $.ajax({
                url : '<?php echo $url_tax_rate; ?>',
                dataType : 'json',
                type : 'POST',
                data : $('#squareup-tax-rate-form :input'),
                beforeSend : function() {
                    $('#squareup-tax-rate-error').empty();
                    $('[data-tax-rate-id]').removeClass('bg-danger');
                    $('#squareup-tax-rate-ok').button('loading');
                },
                success : function(json) {
                    if (json.error) {
                        taxRateError(json.error);

                        if (json.error_tax_rate_id) {
                            $.each(json.error_tax_rate_id, function(index, tax_rate_id) {
                                $('tr[data-tax-rate-id="' + tax_rate_id + '"]').addClass('bg-danger');
                            });
                        }
                    }

                    if (json.success) {
                        $('#squareup-tax-rate-modal').modal('hide');
                    }
                },
                complete: function() {
                    $('#squareup-tax-rate-ok').button('reset');
                },
                error : function(xhr, ajaxSettings, thrownError) {
                    taxRateError(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
            });
        });

        if ($('#squareup-tax-rate-modal').length > 0) {
            $('#squareup-tax-rate-modal').modal('show');
        }
    <?php } ?>

    <?php if ($initial_sync_not_performed) { ?> 
        var fetchSyncModalOptions = function(modal) {
            var submitButton = $(modal).find('button[type="button"][form="form-square-checkout"]');

            $.ajax({
                url : '<?php echo $url_sync_modal_options; ?>',
                dataType : 'json',
                type : 'POST',
                data : $('#form-square-checkout :input'),
                beforeSend : function() {
                    $(submitButton).button('loading');
                },
                success : function(json) {
                    if (!json.already_synced) {
                        $(modal).find('.modal-body').html(json.html);
                        $(submitButton).button('reset');
                    } else {
                        $(submitButton).trigger('click', [true]);
                    }
                }
            });
        }

        $('#form-square-checkout').on('submit', function(e, forced) {
            if (typeof forced == 'undefined' && $('#dropdown_squareup_sync_source').val() != 'none' && $(event.currentTarget).closest('#squareup-sync-modal').length == 0) {
                var modal = $('#squareup-sync-modal').modal('show');

                fetchSyncModalOptions(modal);

                return false;
            }
        });
    <?php } ?>

    $('button[type="button"][form="form-square-checkout"]').click(function(e, forced) {
        var args = typeof forced != 'undefined' ? [true] : [];

        $('#form-square-checkout').trigger('submit', args);
    });

    $('#on-demand-cron').click(function(e) {
        e.preventDefault();

        if ($(this).attr('disabled')) return;

        $('#squareup-on-demand-cron-modal').modal('show');

        $('#checkbox_on_demand_cron_acknowledge').attr('checked', false).trigger('change');
    });

    $('#checkbox_on_demand_cron_acknowledge').change(function(e) {
        if ($(this).is(':checked')) {
            $('#squareup-on-demand-cron-ok').attr('disabled', false);
        } else {
            $('#squareup-on-demand-cron-ok').attr('disabled', true);
        }
    });

    $('#squareup-on-demand-cron-ok').click(function(e) {
        if ($(this).attr('disabled')) return;

        document.location = $('#on-demand-cron').attr('href');
    });

    var checkCronStatus = function() {
        $.ajax({
            url : '<?php echo $url_check_cron_status; ?>',
            dataType : 'json',
            success : function(data) {
                $('#on-demand-cron').attr('disabled', data.on_demand_status);

                if (data.on_demand_status) {
                    $('#on-demand-cron i').addClass('fa-spin');
                } else {
                    $('#on-demand-cron i').removeClass('fa-spin');
                }

                $('#cron_status_text').val(data.cron_status_text);
            },
            complete: function() {
                setTimeout(checkCronStatus, 5000);
            }
        });
    };

    checkCronStatus();

    setCronUrl();

    listTransactions();
});
</script>
<?php echo $footer; ?>