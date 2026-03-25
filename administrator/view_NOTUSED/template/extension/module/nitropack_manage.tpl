<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content" class="nitro">
    <div class="container-fluid bg-light p-4">
        <div class="row">
            <div class="col-12">
                <h2 class="pull-left">
                    <img src="view/image/vendor/nitropackio/logo.png" id="nitropack-logo" alt="<?php echo $heading_title; ?>" />
                    <span class="opacity-0-9"><?php echo $text_nitropack; ?></span>
                    <span class="opacity-0-2"><?php echo $text_io; ?></span>
                    <span class="opacity-0-2">&nbsp;<small><?php echo $version; ?></small></span>
                </h2>
                <div class="pull-right">
                    <a target="_blank" href="<?php echo $external_settings; ?>" data-toggle="tooltip" title="<?php echo $button_external_settings; ?>" class="btn btn-warning d-inline-block"><i class="fa fa-cogs"></i></a>
                    <a target="_blank" href="<?php echo $documentation; ?>" data-toggle="tooltip" title="<?php echo $button_documentation; ?>" class="btn btn-info d-inline-block"><i class="fa fa-question-circle"></i></a>
                    <div class="dropdown d-inline-block">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="store-picker" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <?php echo $store['name']; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="store-picker">
                            <?php foreach ($stores as $store) : ?>
                                <a class="dropdown-item <?php echo $store['current'] ? 'active' : ''; ?>" href="<?php echo $store['admin_href']; ?>"><?php echo $store['name']; ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-light btn-outline-secondary d-inline-block"><i class="fa fa-reply"></i></a>
                </div>
            </div>
            <div class="col-12 mt-3" id="nitropack-flash-container">
                <?php if ($error) : ?>
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
                        <i class="fa fa-exclamation-circle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success) : ?>
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
                        <i class="fa fa-check"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                <?php if ($warning) : ?>
                    <div class="alert alert-warning">
                        <button type="button" class="close" data-dismiss="alert"><i class="fa fa-times"></i></button>
                        <i class="fa fa-exclamation-triangle"></i> <?php echo $warning; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <form method="POST" action="<?php echo $manage; ?>" enctype="multipart/form-data" id="manage-form" data-text-loading="<?php echo $text_loading_form; ?>" data-text-logged-out="<?php echo $text_logged_out; ?>" data-text-connection-lost="<?php echo $text_connection_lost; ?>" data-url-update-check="<?php echo $update_check; ?>" data-text-onbeforeunload="<?php echo $text_onbeforeunload; ?>" data-url-cleanup-stale-cache="<?php echo $cleanup_stale_cache; ?>" data-url-has-stale-cache="<?php echo $has_stale_cache; ?>" data-url-default="<?php echo $url_default; ?>">
            <input type="hidden" name="local_preset" id="nitropack-local-preset" value="<?php echo $local_preset; ?>" />
            <div class="row">
                <div class="col-md-4">
                    <div class="card mt-4">
                        <div class="iframe-container iframe-container-small">
                            <iframe scrolling="no" data-text-loading-invalidate-cache="<?php echo $text_loading_invalidate_cache; ?>" data-text-preset-changed="<?php echo $text_preset_changed; ?>" data-url-invalidate-cache="<?php echo $invalidate; ?>" data-text-loading-purge-cache="<?php echo $text_loading_purge_cache; ?>" data-url-purge-cache="<?php echo $purge; ?>" id="optimizations" data-src="<?php echo $optimizations; ?>" scrolling="no"></iframe>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mt-4">
                        <div class="iframe-container iframe-container-small">
                            <iframe scrolling="no" id="plan" data-src="<?php echo $plan; ?>" scrolling="no"></iframe>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title">
                            <?php echo $text_service_status; ?>
                            </h5>
                            <div class="form-group">
                                <label><?php echo $field_site_id; ?></label>
                                <div>
                                    <span id="site-id"><?php echo $site_id; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo $field_site; ?></label>
                                <div>
                                    <span id="site"><?php echo $site; ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label><?php echo $entry_warmup_status; ?></label>
                                <div>
                                    <small class="pull-left">
                                        <span class="warmup-status"><?php echo $warmup_stats['text_warmup_status']; ?></span>
                                    </small>
                                    <span class="pull-right">
                                        <span id="warmup-buttons" data-warmup-stats-url="<?php echo $warmup_stats_url; ?>" data-warmup-estimate-url="<?php echo $warmup_estimate_url; ?>" data-warmup-autostart="<?php echo $warmup ? '1' : '0'; ?>">
                                            <button <?php if ($warmup_stats['is_warmup_active'] || !$warmup_stats['status'] || !$warmup_stats['is_warmup_enabled']) : ?> style="display: none;" <?php endif; ?> data-warmup-button="start" data-warmup-action="<?php echo $warmup_start; ?>" class="btn btn-sm btn-success" data-toggle="tooltip" data-original-title="<?php echo $text_warmup_start; ?>"><i class="fa fa-play"></i></button>
                                            <button <?php if ($warmup_stats['pending'] == 0 || !$warmup_stats['is_warmup_enabled'] || !$warmup_stats['status']) : ?> style="display: none;" <?php endif; ?> data-warmup-button="pause" data-warmup-action="<?php echo $warmup_pause; ?>" class="btn btn-sm btn-warning" data-toggle="tooltip" data-original-title="<?php echo $text_warmup_pause; ?>"><i class="fa fa-pause"></i></button>
                                            <!--button <?php if (!$warmup || !$warmup_stats['status']) : ?> style="display: none;" <?php endif; ?> data-warmup-button="info" class="btn btn-sm btn-info" data-toggle="tooltip" data-original-title="<?php echo $text_warmup_stats; ?>"><i class="fa fa-info-circle"></i></button-->
                                        </span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="pull-left">
                                <span data-connection="connected" class="text-secondary">
                                    <i class="fa fa-circle text-success"></i> <?php echo $text_active; ?>
                                </span>
                                <span data-connection="disabled" class="text-secondary">
                                    <i class="fa fa-circle text-danger"></i> <?php echo $text_disabled; ?>
                                </span>
                            </small>
                            <small class="pull-right">
                                <a href="<?php echo $disconnect; ?>" id="disconnect" data-loading-text="<?php echo $text_disconnecting; ?>" data-are-you-sure="<?php echo $text_confirm; ?>" class="card-link text-secondary">
                                    <i class="fa fa-power-off"></i> <?php echo $button_disconnect; ?>
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6 p-3">
                    <div class="card">
                        <div class="iframe-container iframe-container-medium">
                            <iframe scrolling="no" id="quicksetup" data-src="<?php echo $quicksetup; ?>" scrolling="no"></iframe>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="iframe-container iframe-container-medium">
                            <iframe scrolling="no" id="beforeafter" data-src="<?php echo $beforeafter; ?>" scrolling="no"></iframe>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 p-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">
                                <?php echo $text_general_settings; ?>
                                <a class="pull-right text-primary" href="<?php echo $help_general_settings; ?>" target="_blank" data-toggle="tooltip" data-original-title="<?php echo $button_help; ?>"><i class="fa fa-question-circle"></i></a>
                            </h5>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label">
                                    <?php echo $entry_extension_status; ?><br />
                                    <small class="text-secondary"><?php echo $help_extension_status; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input id="select-status" type="checkbox" name="<?php echo $field_status; ?>" value="1" <?php echo $status == '1' ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-6 col-md-8 col-form-label" for="select-warmup">
                                    <?php echo $entry_warmup; ?><br />
                                    <small class="text-secondary"><?php echo $help_warmup; ?></small>
                                    <small class="text-secondary"><div id="warmup-details"><?php echo $warmup_details; ?></div></small>
                                    <div id="warmup-data">
                                        <?php foreach ($excluded_warmup_languages as $excluded_warmup_language): ?>
                                            <input type="hidden" name="excluded_warmup_languages[]" value="<?php echo $excluded_warmup_language; ?>" />
                                        <?php endforeach; ?>

                                        <?php foreach ($excluded_warmup_currencies as $excluded_warmup_currency): ?>
                                            <input type="hidden" name="excluded_warmup_currencies[]" value="<?php echo $excluded_warmup_currency; ?>" />
                                        <?php endforeach; ?>

                                        <?php foreach ($included_warmup_routes as $included_warmup_route) : ?>
                                            <input type="hidden" name="included_warmup_routes[]" value="<?php echo $included_warmup_route; ?>" />
                                        <?php endforeach; ?>

                                        <?php if ($product_categories_warmup) : ?>
                                            <input type="hidden" name="product_categories_warmup" value="1" />
                                        <?php endif; ?>
                                    </div>
                                </label>
                                <div class="col-6 col-md-4 text-right">
                                    <button id="button-configure-warmup" data-toggle="tooltip" data-original-title="<?php echo $button_configure_warmup; ?>" class="btn btn-md btn-light btn-outline-secondary"><i class="fa fa-gear"></i></button>

                                    <label class="switch">
                                        <input type="checkbox" name="warmup" value="1" <?php echo $warmup ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label" for="select-allow-cart">
                                    <?php echo $entry_allow_cart; ?><br />
                                    <small class="text-secondary"><?php echo $help_allow_cart; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input type="checkbox" name="allow_cart" value="1" <?php echo $allow_cart ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label" for="select-compression">
                                    <?php echo $entry_compression; ?><br />
                                    <small class="text-secondary"><?php echo $help_compression; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input type="checkbox" name="compression" value="1" <?php echo $compression ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <small class="pull-right">
                                <a href="#" id="open-cron" class="card-link text-secondary">
                                    <i class="fa fa-clock-o"></i> <?php echo $button_cron; ?>
                                </a>
                                <a href="<?php echo $debug_log; ?>" class="card-link text-secondary">
                                    <i class="fa fa-download"></i> <?php echo $button_debug_log; ?>
                                </a>
                                <a href="<?php echo $error_log; ?>" class="card-link text-secondary">
                                    <i class="fa fa-download"></i> <?php echo $button_error_log; ?>
                                </a>
                            </small>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $text_included; ?>
                                <a class="pull-right text-primary" href="<?php echo $help_page_types; ?>" target="_blank" data-toggle="tooltip" data-original-title="<?php echo $button_help; ?>"><i class="fa fa-question-circle"></i></a>
                            </h5>
                            <table class="table table-hover mt-4">
                                <thead>
                                    <tr>
                                        <td colspan="4">
                                            <?php echo $entry_standard_pages; ?>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($standard_pages as $i => $page) : ?>
                                    <tr data-standard-page-route="<?php echo $page['route']; ?>">
                                        <td>
                                            <?php echo $page['name']; ?>
                                        </td>
                                        <td class="text-muted hidden-xs word-break">
                                            <?php echo $page['route']; ?>
                                        </td>
                                        <td class="text-right button-clear-td">
                                            <button class="btn btn-warning btn-sm" data-toggle="tooltip" data-original-title="<?php echo $button_invalidate; ?>" data-button-clear="invalidate" data-button-clear-action="&invalidate_type=route&invalidate_value=<?php echo $page['route']; ?>" data-are-you-sure="<?php echo $text_confirm_invalidate; ?>"><i class="fa fa-recycle"></i></button>

                                            <button class="btn btn-danger btn-sm" data-toggle="tooltip" data-original-title="<?php echo $button_purge; ?>" data-button-clear="purge" data-button-clear-action="&purge_type=route&purge_value=<?php echo $page['route']; ?>" data-are-you-sure="<?php echo $text_confirm_purge; ?>"><i class="fa fa-recycle"></i></button>
                                        </td>
                                        <td class="text-right">
                                            <label class="switch">
                                                <input type="checkbox" name="standard_page[<?php echo $page['key']; ?>]" <?php echo $page['status'] ? 'checked' : ''; ?> />
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <thead>
                                    <tr>
                                        <td colspan="4">
                                            <?php echo $entry_custom_pages; ?>
                                        </td>
                                    </tr>
                                </thead>
                                <tbody id="custom-pages">
                                    <?php foreach ($custom_pages as $i => $page) : ?>
                                    <tr data-custom-page-i="<?php echo $i; ?>">
                                        <td>
                                            <?php echo $page['name']; ?>
                                        </td>
                                        <td class="text-muted hidden-xs word-break">
                                            <?php echo $page['route']; ?>
                                        </td>
                                        <td class="text-right button-clear-td">
                                            <input type="hidden" name="custom_page[<?php echo $i; ?>][name]" value="<?php echo $page['name']; ?>" />
                                            <input type="hidden" name="custom_page[<?php echo $i; ?>][route]" value="<?php echo $page['route']; ?>" />

                                            <button data-toggle="tooltip" data-original-title="<?php echo $button_delete_custom_page; ?>" class="btn btn-light btn-sm delete-custom-page" data-are-you-sure="<?php echo $text_confirm_custom_page; ?>"><i class="fa fa-times"></i></button>

                                            <button class="btn btn-warning btn-sm" data-toggle="tooltip" data-original-title="<?php echo $button_invalidate; ?>" data-button-clear="invalidate" data-button-clear-action="&invalidate_type=route&invalidate_value=<?php echo $page['route']; ?>" data-are-you-sure="<?php echo $text_confirm_invalidate; ?>"><i class="fa fa-recycle"></i></button>

                                            <button class="btn btn-danger btn-sm" data-toggle="tooltip" data-original-title="<?php echo $button_purge; ?>" data-button-clear="purge" data-button-clear-action="&purge_type=route&purge_value=<?php echo $page['route']; ?>" data-are-you-sure="<?php echo $text_confirm_purge; ?>"><i class="fa fa-recycle"></i></button>
                                        </td>
                                        <td class="text-right">
                                            <label class="switch">
                                                <input type="checkbox" name="custom_page[<?php echo $i; ?>][status]" <?php echo $page['status'] ? 'checked' : ''; ?> />
                                                <span class="slider round"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right">
                                        <button id="add-custom-page" class="btn btn-light btn-outline-secondary"><i class="fa fa-plus"></i> <?php echo $button_add_custom_page; ?></button>
                                    </td>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="card mt-4">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?php echo $text_auto_cache_clear; ?>
                                <a class="pull-right text-primary" href="<?php echo $help_auto_cache_clear; ?>" target="_blank" data-toggle="tooltip" data-original-title="<?php echo $button_help; ?>"><i class="fa fa-question-circle"></i></a>
                            </h5>
                            <div class="alert alert-light bg-light">
                                <?php echo $info_auto_cache_clear; ?>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label" for="select-warmup">
                                    <?php echo $entry_auto_cache_clear_admin_product; ?><br />
                                    <small class="text-secondary"><?php echo $help_auto_cache_clear_admin_product; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input type="checkbox" name="auto_cache_clear_admin_product" value="1" <?php echo $auto_cache_clear_admin_product ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label" for="select-warmup">
                                    <?php echo $entry_auto_cache_clear_admin_category; ?><br />
                                    <small class="text-secondary"><?php echo $help_auto_cache_clear_admin_category; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input type="checkbox" name="auto_cache_clear_admin_category" value="1" <?php echo $auto_cache_clear_admin_category ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label" for="select-warmup">
                                    <?php echo $entry_auto_cache_clear_admin_information; ?><br />
                                    <small class="text-secondary"><?php echo $help_auto_cache_clear_admin_information; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input type="checkbox" name="auto_cache_clear_admin_information" value="1" <?php echo $auto_cache_clear_admin_information ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label" for="select-warmup">
                                    <?php echo $entry_auto_cache_clear_admin_manufacturer; ?><br />
                                    <small class="text-secondary"><?php echo $help_auto_cache_clear_admin_manufacturer; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input type="checkbox" name="auto_cache_clear_admin_manufacturer" value="1" <?php echo $auto_cache_clear_admin_manufacturer ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label" for="select-warmup">
                                    <?php echo $entry_auto_cache_clear_admin_review; ?><br />
                                    <small class="text-secondary"><?php echo $help_auto_cache_clear_admin_review; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input type="checkbox" name="auto_cache_clear_admin_review" value="1" <?php echo $auto_cache_clear_admin_review ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-xs-8 col-form-label" for="select-warmup">
                                    <?php echo $entry_auto_cache_clear_order; ?><br />
                                    <small class="text-secondary"><?php echo $help_auto_cache_clear_order; ?></small>
                                </label>
                                <div class="col-xs-4 text-right">
                                    <label class="switch">
                                        <input type="checkbox" name="auto_cache_clear_order" value="1" <?php echo $auto_cache_clear_order ? 'checked' : ''; ?> />
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="fb-chat" data-toggle="tooltip" title="<?php echo $button_chat; ?>">
        <a href="<?php echo $messenger; ?>" target="_blank">
            <svg width="60px" height="60px" viewBox="0 0 60 60">
                <svg x="0" y="0" width="60px" height="60px">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g>
                            <circle fill="#007bff" cx="30" cy="30" r="30"></circle>
                            <svg x="10" y="10">
                                <g transform="translate(0.000000, -10.000000)" fill="#FFFFFF">
                                    <g id="logo" transform="translate(0.000000, 10.000000)">
                                        <path fill="#fff" d="M20,0 C31.2666,0 40,8.2528 40,19.4 C40,30.5472 31.2666,38.8 20,38.8 C17.9763,38.8 16.0348,38.5327 14.2106,38.0311 C13.856,37.9335 13.4789,37.9612 13.1424,38.1098 L9.1727,39.8621 C8.1343,40.3205 6.9621,39.5819 6.9273,38.4474 L6.8184,34.8894 C6.805,34.4513 6.6078,34.0414 6.2811,33.7492 C2.3896,30.2691 0,25.2307 0,19.4 C0,8.2528 8.7334,0 20,0 Z M7.99009,25.07344 C7.42629,25.96794 8.52579,26.97594 9.36809,26.33674 L15.67879,21.54734 C16.10569,21.22334 16.69559,21.22164 17.12429,21.54314 L21.79709,25.04774 C23.19919,26.09944 25.20039,25.73014 26.13499,24.24744 L32.00999,14.92654 C32.57369,14.03204 31.47419,13.02404 30.63189,13.66324 L24.32119,18.45264 C23.89429,18.77664 23.30439,18.77834 22.87569,18.45674 L18.20299,14.95224 C16.80079,13.90064 14.79959,14.26984 13.86509,15.75264 L7.99009,25.07344 Z"></path>
                                    </g>
                                </g>
                            </svg>
                        </g>
                    </g>
                </svg>
            </svg>
        </a>
    </div>
</div>
<script id="template-nitropack-notification-success" type="text/template">
<div id="nitropack-notification" data-type="success">
    <div class="alert alert-success" id="nitropack-notification-message">{message}</div>
</div>
</script>
<script id="template-nitropack-notification-danger" type="text/template">
<div id="nitropack-notification" data-type="danger">
    <div class="alert alert-danger" id="nitropack-notification-message">{message}</div>
</div>
</script>
<script id="template-nitropack-notification-warning" type="text/template">
<div id="nitropack-notification" data-type="warning">
    <div class="alert alert-warning" id="nitropack-notification-message">{message}</div>
</div>
</script>
<script id="template-nitropack-notification-info" type="text/template">
<div id="nitropack-notification" data-type="info">
    <div class="alert alert-info" id="nitropack-notification-message">{message}</div>
</div>
</script>
<script id="template-custom-page" type="text/template">
<tr data-custom-page-i="{i}">
    <td>
        {name}
    </td>
    <td class="text-muted hidden-xs word-break">
        {route}
    </td>
    <td class="text-right button-clear-td">
        <input type="hidden" name="custom_page[{i}][name]" value="{name_escaped}" />
        <input type="hidden" name="custom_page[{i}][route]" value="{route}" />

        <button data-toggle="tooltip" data-original-title="<?php echo $button_delete_custom_page; ?>" class="btn btn-light btn-sm delete-custom-page" data-are-you-sure="<?php echo $text_confirm_custom_page; ?>"><i class="fa fa-times"></i></button>

        <button class="btn btn-warning btn-sm" data-toggle="tooltip" data-original-title="<?php echo $button_invalidate; ?>" data-button-clear="invalidate" data-button-clear-action="&invalidate_type=route&invalidate_value={route}" data-are-you-sure="<?php echo $text_confirm_invalidate; ?>"><i class="fa fa-recycle"></i></button>

        <button class="btn btn-danger btn-sm" data-toggle="tooltip" data-original-title="<?php echo $button_purge; ?>" data-button-clear="purge" data-button-clear-action="&purge_type=route&purge_value={route}" data-are-you-sure="<?php echo $text_confirm_purge; ?>"><i class="fa fa-recycle"></i></button>
    </td>
    <td class="text-right">
        <label class="switch">
            <input type="checkbox" name="custom_page[{i}][status]" value="1" checked />
            <span class="slider round"></span>
        </label>
    </td>
</tr>
</script>
<script id="template-modal-custom-page" type="text/template">
<div class="nitro modal fade modal-removable" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $text_add_custom_page; ?></h5>
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            </div>
            <div class="modal-body">
                <form id="custom-page">
                    <div class="form-group required">
                        <label for="input-custom-page-name"><?php echo $entry_custom_page_name; ?></label>
                        <div>
                            <input type="text" name="custom_page_name" value="" placeholder="<?php echo $placeholder_custom_page_name; ?>" id="input-custom-page-name" class="form-control" autocomplete="new-password" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select-custom-page-route"><?php echo $entry_custom_page_route; ?></label>
                        <div>
                            <select id="select-custom-page-route" name="custom_page_route" class="form-control">
                                <?php foreach ($all_pages as $page) : ?>
                                <option value="<?php echo $page; ?>"><?php echo $page; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outline-secondary" data-dismiss="modal"><?php echo $button_close; ?></button>
                <button disabled type="button" class="btn btn-primary" id="save-custom-page"><?php echo $button_save; ?></button>
            </div>
        </div>
    </div>
</div>
</script>
<script id="template-modal-warmup-disable-confirm" type="text/template">
<div id="modal-warmup-disable-confirm" class="nitro modal fade modal-removable" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $text_configure_warmup; ?></h5>
                <a class="text-primary close" href="<?php echo $help_warmup; ?>" target="_blank"><i class="fa fa-question-circle"></i></a>
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            </div>
            <div class="modal-body">
                <p><i class="fa fa-exclamation-triangle text-warning"></i> <?php echo $text_warmup_disable_confirm; ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outline-secondary" data-dismiss="modal"><?php echo $button_cancel; ?></button>
                <button type="button" class="btn btn-primary" id="open-warmup-settings"><?php echo $button_disable_warmup_continue; ?></button>
            </div>
        </div>
    </div>
</div>
</script>
<script id="template-modal-warmup" type="text/template">
<div id="modal-warmup" class="nitro modal fade modal-removable" tabindex="-1" role="dialog" data-warmup-form="<?php echo $warmup_form; ?>">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $text_configure_warmup; ?></h5>
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info text-center"><i class="fa fa-spinner fa-spin"></i> <?php echo $text_loading_warmup; ?></div>
            </div>
            <div id="warmup-estimate-loading" class="modal-footer modal-footer-left warmup-estimate-modal-container" style="display: none;">
                <div class="alert alert-warning"><i class="fa fa-spinner fa-spin"></i> <?php echo $text_warmup_estimate_loading; ?></div>
            </div>
            <div id="warmup-estimate-error" class="modal-footer modal-footer-left warmup-estimate-modal-container" style="display: none;">
                <div id="warmup-estimate-error-message" class="alert alert-danger"></div>
            </div>
            <div id="warmup-estimate-result" class="modal-footer modal-footer-left warmup-estimate-modal-container" style="display: none;" data-warmup-estimate-result-text="<?php echo $text_warmup_estimate_result; ?>">
                <div id="warmup-estimate-result-message" class="alert alert-warning"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outline-secondary" data-dismiss="modal"><?php echo $button_close; ?></button>
                <button disabled type="button" class="btn btn-primary" id="close-enable-warmup"><?php echo $button_close_enable_warmup; ?></button>
            </div>
        </div>
    </div>
</div>
</script>
<script id="template-modal-warmup-stats" type="text/template">
<div id="modal-warmup-stats" class="nitro modal fade modal-removable" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $text_warmup_stats; ?></h5>
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info text-center"><i class="fa fa-spinner fa-spin"></i> <?php echo $text_loading_warmup_stats; ?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outline-secondary" data-dismiss="modal"><?php echo $button_close; ?></button>
            </div>
        </div>
    </div>
</div>
</script>
<script id="template-modal-warmup-detail" type="text/template">
    <div class="row mb-2">
        <div class="col-xs-6">
            {key}
        </div>
        <div class="col-xs-6 text-right">
            {value}
        </div>
    </div>
</script>
<script id="template-update-check-flash" type="text/template">
    <div class="alert alert-info d-flex justify-content-between lh-2">
        <div>{message}</div>
        <div><button class="btn btn-primary btn-sm" id="button-modal-update"><?php echo $button_update_check_get_it; ?></button></div>
    </div>
</script>
<script id="template-modal-update" type="text/template">
<div id="modal-update" class="nitro modal fade modal-removable" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{title}</h5>
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            </div>
            <?php if ($non_installed_modifications) : ?>
                <div class="modal-body">
                    <div class="alert alert-warning lh-2">
                        <div><?php echo $text_non_installed_modifications; ?></div>
                        <hr />
                        <ul>
                            <?php foreach ($non_installed_modifications as $non_installed_modification) : ?>
                                <li><?php echo $non_installed_modification; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <a id="modifications-refresh" href="<?php echo $modifications_refresh; ?>" class="btn btn-primary btn-sm"><?php echo $button_update_check_refresh_now; ?></a>
                </div>
            <?php else: ?>
                <div class="modal-body">
                    <h3><?php echo $text_release_notes; ?></h3>
                    {body}
                </div>
                <div id="modal-update-steps" class="modal-footer text-left d-none">
                    <h3><?php echo $text_update_progress; ?></h3>
                    <div id="progress-lines" class="well"></div>
                    <div id="modal-update-progress" class="progress w-100 d-block">
                        <div class="progress-bar" role="progressbar"></div>
                    </div>
                </div>
                <div id="modal-update-setup" class="modal-footer text-left d-none">
                    <?php if ($has_modification_permissions) : ?>
                        <div class="w-100 alert alert-info">
                            <?php echo $text_refresh_modifications; ?>
                        </div>
                    <?php else: ?>
                        <div class="w-100 alert alert-warning">
                            <i class="fa fa-exclamation-triangle"></i> <?php echo $text_refresh_modifications_no_permissions; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light d-none" id="modal-update-abort"><?php echo $button_abort; ?></button>
                    <button type="button" class="btn btn-primary" id="modal-update-start"><?php echo $button_update; ?></button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</script>
<script id="template-update-check-progress" type="text/template">
    <div class="w-100 d-flex justify-content-between">
        <div><i class="fa fa-arrow-right"></i> {message}</div>
        <div class="update-progress">
            <small class="text-secondary">
                <i class="fa fa-spin fa-circle-o-notch"></i> <?php echo $text_working; ?>
            </small>
        </div>
    </div>
</script>
<script id="template-update-check-error" type="text/template">
    <div id="update-check-error-container" class="alert alert-danger mt-2">
        <i class="fa fa-exclamation-triangle"></i> {message}
    </div>
</script>
<script id="template-update-check-badge-ok" type="text/template">
    <span class="badge badge-success"><?php echo $text_ok; ?></span>
</script>
<script id="template-update-check-badge-error" type="text/template">
    <span class="badge badge-danger"><?php echo $text_error; ?></span>
</script>
<script id="template-update-check-badge-aborted" type="text/template">
    <span class="badge badge-warning"><?php echo $text_aborted; ?></span>
</script>
<script id="template-modal-cron" type="text/template">
<div id="modal-cron" class="nitro modal fade modal-removable" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo $text_title_cron; ?></h5>
                <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            </div>

            <div class="modal-body">
                <?php if ($cron_warning): ?>
                <div class="alert alert-danger">
                    <?php echo $text_cron_warning; ?>
                </div>
                <?php endif; ?>

                <?php echo $text_cron_info; ?>
            </div>
        </div>
    </div>
</div>
</script>
<?php echo $footer; ?>
