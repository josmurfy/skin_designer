<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <?php if ($is_merchant_transaction) : ?>
            <?php if ($is_authorized) : ?>
                <a id="transaction_capture" data-url-transaction-capture="<?php echo $url_capture; ?>" data-confirm-capture="<?php echo $confirm_capture; ?>" class="btn btn-success"><?php echo $button_capture; ?></a>
                <a id="transaction_void" data-url-transaction-void="<?php echo $url_void; ?>" data-confirm-void="<?php echo $confirm_void; ?>" class="btn btn-warning"><?php echo $button_void; ?></a>
            <?php endif; ?>
            
            <?php if ($is_captured && !$is_fully_refunded) : ?>
                <a id="transaction_refund" data-url-transaction-refund="<?php echo $url_refund; ?>" data-url-transaction-refund-modal="<?php echo $url_refund_modal; ?>" class="btn btn-danger"><?php echo $button_refund; ?></a>
            <?php endif; ?>
        <?php endif; ?>
        
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

    <div id="transaction-alert" data-message="<?php echo $text_loading; ?>">
        <?php foreach ($alerts as $alert) { ?>
            <div class="alert alert-<?php echo $alert['type']; ?>"><i class="fa fa-<?php echo $alert['icon']; ?>"></i>&nbsp;<?php echo $alert['text']; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
    </div>
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-info-circle"></i>&nbsp;<?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_transaction_id; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static">
                        <?php if ($is_merchant_transaction) : ?>
                            <a href="<?php echo $url_transaction; ?>" target="_blank"><?php echo $transaction_id; ?></a>
                        <?php else: ?>
                            <?php echo $transaction_id; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_merchant; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $merchant; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_order_id; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><a href="<?php echo $url_order; ?>" target="_blank"><?php echo $order_id; ?></a></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_transaction_status; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $status; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_amount; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $amount; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_billing_address_company; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $billing_address_company; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_billing_address_street; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $billing_address_street; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_billing_address_city; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $billing_address_city; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_billing_address_postcode; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $billing_address_postcode; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_billing_address_province; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $billing_address_province; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_billing_address_country; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $billing_address_country; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_browser; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $browser; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_ip; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $ip; ?></div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"><?php echo $entry_date_created; ?></label>
                <div class="col-sm-10">
                    <div class="form-control-static"><?php echo $date_created; ?></div>
                </div>
            </div>
            <?php if ($has_refunds) { ?>
                <hr />
                <h3><?php echo $text_refunds; ?></h3>
                <table class="table table-bordered table-striped">
                    <thead>
                        <th><?php echo $column_date_created; ?></th>
                        <th><?php echo $column_reason; ?></th>
                        <th><?php echo $column_status; ?></th>
                        <th><?php echo $column_amount; ?></th>
                        <th><?php echo $column_fee; ?></th>
                    </thead>
                    <tbody>
                        <?php foreach ($refunds as $refund) { ?>
                            <tr>
                                <td><?php echo $refund['date_created']; ?></td>
                                <td><?php echo $refund['reason']; ?></td>
                                <td><?php echo $refund['status']; ?></td>
                                <td><?php echo $refund['amount']; ?></td>
                                <td><?php echo $refund['fee']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
        </form>
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
                    $('.alert').remove();

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
    
    var transactionLoading = function() {
        var message = $('#transaction-alert').attr('data-message');

        $('#transaction-alert').html('<div class="text-center alert alert-info"><i class="fa fa-circle-o-notch fa-spin"></i>&nbsp;' + message + '</div>');
    }

    var refreshPage = function() {
        document.location = document.location;
    }

    var transactionError = function(message) {
        $('#transaction-alert').html('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-label="X"><span aria-hidden="true">&times;</span></button><i class="fa fa-exclamation-circle"></i>&nbsp;' + message + '</div>');
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
                        refreshPage();
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
                    refreshPage();
                }

                if (json.success && json.order_history_data) {
                    addOrderHistory(json.order_history_data, function() {
                        refreshPage();
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

    var refundInputValidate = function(reason_input, amount_input) {
        var result = true;

        if (!$(reason_input)[0].checkValidity()) {
            $(reason_input).closest('.form-group').addClass('has-error');
            result = false;
        } else {
            $(reason_input).closest('.form-group').removeClass('has-error');
        }

        if (!$(amount_input)[0].checkValidity()) {
            $(amount_input).closest('.form-group').addClass('has-error');
            result = false;
        } else {
            $(amount_input).closest('.form-group').removeClass('has-error');
        }

        return result;
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

    var order_history_data = <?php echo $order_history_data; ?>;

    if (order_history_data) {
        <?php if ($is_oc15) : ?>
            $.ajax({
                url: 'index.php?route=sale/order/history&token=<?php echo $token; ?>&store_id=' + order_history_data.store_id + '&order_id=' + order_history_data.order_id,
                type: 'post',
                dataType: 'html',
                data: order_history_data
            });
        <?php else : ?>
            $.ajax({
                url: '<?php echo (!$old_api) ? ($catalog . "index.php?route=api/order/history&token=' + token + '") : ("index.php?route=sale/order/api&token=" . $token . "&api=api/order/history"); ?>&store_id=' + order_history_data.store_id + '&order_id=' + order_history_data.order_id,
                type: 'post',
                dataType: 'json',
                data: order_history_data
            });
        <?php endif; ?>
    }

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
});
</script>
<?php echo $footer; ?>