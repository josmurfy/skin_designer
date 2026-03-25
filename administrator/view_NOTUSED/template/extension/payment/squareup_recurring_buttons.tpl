<div id="squareup_buttons" class="form-control-static buttons clearfix">
  <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?> <button type="button" class="close" data-dismiss="alert">&times;</button></div>
  <?php } else if ($order_recurring_id) { ?>
  <div class="pull-right">
    <button type="button" id="button-cancel" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger"><?php echo $button_text; ?></button>
  </div>
  <?php } ?>
</div>
<script type="text/javascript"><!--
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
                    $('.alert').remove();

                    if (json['error']) {
                        if (json['error']['key']) {
                            $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['key'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                        }

                        if (json['error']['ip']) {
                            $('#content > .container-fluid').prepend('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error']['ip'] + ' <button type="button" id="button-ip-add" data-loading-text="<?php echo $text_loading; ?>" class="btn btn-danger btn-xs pull-right"><i class="fa fa-plus"></i> <?php echo $button_ip_add; ?></button></div>');
                        }

                        $('#button-cancel').hide();
                    }

                    if (json['token']) {
                        token = json['token'];

                        $('#button-cancel').show();
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

    var addOrderHistory = function(success_callback) {
        $.ajax({
            url: '<?php echo (!$old_api) ? ($catalog . "index.php?route=api/order/history&token=' + token + '") : ("index.php?route=sale/order/api&token=" . $token . "&api=api/order/history"); ?>&store_id=<?php echo $store_id; ?>' + '&order_id=<?php echo $order_id; ?>',
            type: 'post',
            dataType: 'json',
            data: 'order_status_id=<?php echo $order_status_id; ?>&notify=<?php echo $notify; ?>&override=0&append=0&comment=' + encodeURIComponent("<?php echo $comment; ?>"),
            success: function(json) {
                if (json['error']) {
                    $('#squareup_buttons').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + json['error'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                }

                if (json['success']) {
                    success_callback();
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $('#squareup_buttons').before('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> ' + thrownError + "<br />" + xhr.statusText + "<br />" + xhr.responseText + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
            }
        });
    }

    $(document).delegate('#button-cancel', 'click', function() {
        if (!confirm("<?php echo $text_confirm_cancel; ?>")) {
            return false;
        }

        $.ajax({
            url: '<?php echo $cancel; ?>',
            dataType: 'json',
            beforeSend: function() {
                $('#button-cancel').button('loading');
            },
            success: function(json) {
                $('.alert').remove();
                
                if (json['success']) {
                    addOrderHistory(function() {
                        $('#squareup_buttons').before('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                        $('#button-cancel').hide();
                    });
                }
                
                if (json['error']) {
                    $('#squareup_buttons').before('<div class="alert alert-danger">' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                    $('#button-cancel').button('reset');
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    });
});
//--></script>