/**
 * ShopManager - Maintenance Image
 * Page principale (validation + filtre)
 * Variables globales requises (déclarées dans image.twig) :
 *   MAINT_IMAGE_TOKEN  — user_token OpenCart
 */

// Check validation status on page load
$(document).ready(function() {
    checkValidationStatus();
});

function checkValidationStatus() {
    $.ajax({
        url: 'index.php?route=shopmanager/maintenance/image.checkValidationStatus&user_token=' + MAINT_IMAGE_TOKEN,
        dataType: 'json',
        success: function(json) {
            if (json.needs_validation) {
                var message = '';

                if (json.validated === 0) {
                    message = '<div class="alert alert-warning"><i class="fa-solid fa-exclamation-triangle"></i> <strong>No products have been validated yet.</strong><br>';
                    message += 'Total products: <strong>' + json.total_products + '</strong><br>';
                    message += 'It is recommended to run validation before using filters.</div>';
                } else {
                    message = '<div class="alert alert-info"><i class="fa-solid fa-info-circle"></i> <strong>Validation incomplete</strong><br>';
                    message += 'Validated: <strong>' + json.validated + '</strong> / <strong>' + json.total_products + '</strong> (' + json.percentage + '%)<br>';
                    message += 'Some products need validation or have outdated validation data.</div>';
                }

                $('#validation-info').html(message);
                showModal(document.getElementById('validation-modal'));
            }
        },
        error: function() {
            console.log('Failed to check validation status');
        }
    });
}

// Start validation button
var validationStats = {
    start_time: null,
    total_products: 0,
    validated: 0,
    errors: 0,
    with_issues: 0
};

$('#btn-start-validation').on('click', function() {
    $(this).prop('disabled', true);
    $('.btn-close, .btn-secondary').prop('disabled', true);
    $('#validation-progress').show();
    validationStats.start_time = new Date();

    // Check if table needs to be created first
    $.ajax({
        url: 'index.php?route=shopmanager/maintenance/image.checkValidationStatus&user_token=' + MAINT_IMAGE_TOKEN,
        dataType: 'json',
        success: function(json) {
            validationStats.total_products = json.total_products;

            if (json.message && json.message.includes('créée')) {
                // Need to create table
                $('#validation-status').html('<i class="fa-solid fa-cog fa-spin"></i> Creating maintenance table...');
                $.ajax({
                    url: 'index.php?route=shopmanager/maintenance/image.createValidationTable&user_token=' + MAINT_IMAGE_TOKEN,
                    type: 'post',
                    dataType: 'json',
                    success: function(result) {
                        if (result.error) {
                            alert('Error: ' + result.error);
                        } else {
                            validateBatch(0, json.total_products);
                        }
                    }
                });
            } else {
                validateBatch(0, json.total_products);
            }
        }
    });
});

function validateBatch(offset, total) {
    var validated = validationStats.validated;
    var percentage = Math.round((validated / total) * 100);
    var elapsed = ((new Date() - validationStats.start_time) / 1000).toFixed(1);
    var speed = validated > 0 ? (validated / elapsed).toFixed(1) : 0;
    var remaining = total - validated;
    var eta = speed > 0 ? (remaining / speed).toFixed(0) : '?';

    $('#progress-bar').css('width', percentage + '%').text(percentage + '%');
    $('#validation-status').html(
        '<i class="fa-solid fa-spinner fa-spin"></i> <strong>Validating...</strong><br>' +
        'Progress: <strong>' + validated + '</strong> / <strong>' + total + '</strong> products<br>' +
        '<small>Speed: ' + speed + ' products/sec | ETA: ' + eta + 's | Elapsed: ' + elapsed + 's</small>'
    );

    $.ajax({
        url: 'index.php?route=shopmanager/maintenance/image.validateBatch&user_token=' + MAINT_IMAGE_TOKEN,
        type: 'post',
        data: {offset: offset},
        dataType: 'json',
        timeout: 120000,
        success: function(json) {
            console.log('Batch response:', json);

            if (json.error) {
                alert('Error: ' + json.error);
                return;
            }

            if (json.warnings && json.warnings.length > 0) {
                console.warn('Validation warnings:', json.warnings);
            }

            validationStats.validated += json.validated;

            console.log('Progress:', validationStats.validated, '/', total, 'Completed:', json.completed, 'Next offset:', json.next_offset);

            if (json.completed) {
                var total_time = ((new Date() - validationStats.start_time) / 1000).toFixed(1);
                var avg_speed = (validationStats.validated / total_time).toFixed(1);

                var summary = '<div class="alert alert-success"><i class="fa-solid fa-check-circle"></i> <strong>Validation Complete!</strong></div>';
                summary += '<div class="card"><div class="card-body">';
                summary += '<h6 class="card-title">Summary</h6>';
                summary += '<ul class="list-unstyled mb-0">';
                summary += '<li><i class="fa-solid fa-check text-success"></i> Total validated: <strong>' + validationStats.validated + '</strong> products</li>';
                summary += '<li><i class="fa-solid fa-clock text-info"></i> Total time: <strong>' + total_time + '</strong> seconds</li>';
                summary += '<li><i class="fa-solid fa-gauge text-primary"></i> Average speed: <strong>' + avg_speed + '</strong> products/sec</li>';
                summary += '</ul>';
                summary += '</div></div>';

                $('#validation-info').html(summary);
                $('#validation-progress').hide();
                $('#btn-start-validation').hide();
                $('.btn-secondary').hide();

                // Add reload button
                var reloadBtn = $('<button type="button" class="btn btn-primary">Reload List</button>');
                reloadBtn.on('click', function() {
                    $('#report').html('<div class="text-center py-5"><i class="fa-solid fa-circle-notch fa-spin fa-3x"></i><br><br>Loading...</div>');
                    $('#report').load('index.php?route=shopmanager/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN);
                    hideModal(document.getElementById('validation-modal'));
                });
                $('.modal-footer').html(reloadBtn);
            } else {
                validateBatch(json.next_offset, total);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', {
                status: status,
                error: error,
                responseText: xhr.responseText,
                statusCode: xhr.status,
                offset: offset,
                validated_so_far: validationStats.validated
            });

            var errorMsg = 'Validation stopped at ' + validationStats.validated + ' products';
            if (status === 'timeout') {
                errorMsg += '\n\nTimeout: The server took too long to respond.';
            } else if (xhr.responseText) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMsg += '\n\nError: ' + response.error;
                    }
                } catch(e) {
                    errorMsg += '\n\nResponse: ' + xhr.responseText.substring(0, 500);
                }
            }
            errorMsg += '\n\nCheck console (F12) for details.';

            $('#validation-status').html(
                '<div class="alert alert-danger">' +
                '<i class="fa-solid fa-exclamation-triangle"></i> <strong>Error!</strong><br>' +
                errorMsg.replace(/\n/g, '<br>') +
                '</div>'
            );
        }
    });
}

// Handle pagination and sorting clicks
$('#report').on('click', 'thead a, .pagination a', function(e) {
    e.preventDefault();
    $('#report').html('<div class="text-center py-5"><i class="fa-solid fa-circle-notch fa-spin fa-3x"></i><br><br>Loading...</div>');
    $('#report').load(this.href);
});

$('#button-filter').on('click', function() {
    var url = '';

    var filter_product_id = $('input[name=\'filter_product_id\']').val();
    if (filter_product_id) {
        url += '&filter_product_id=' + encodeURIComponent(filter_product_id);
    }

    var filter_name = $('input[name=\'filter_name\']').val();
    if (filter_name) {
        url += '&filter_name=' + encodeURIComponent(filter_name);
    }

    var filter_model = $('input[name=\'filter_model\']').val();
    if (filter_model) {
        url += '&filter_model=' + encodeURIComponent(filter_model);
    }

    url += '&filter_image_issue=' + ($('input[name=\'filter_image_issue\']').is(':checked') ? '1' : '0');
    url += '&filter_low_resolution=' + ($('input[name=\'filter_low_resolution\']').is(':checked') ? '1' : '0');
    url += '&filter_wrong_path=' + ($('input[name=\'filter_wrong_path\']').is(':checked') ? '1' : '0');
    url += '&filter_old_nomenclature=' + ($('input[name=\'filter_old_nomenclature\']').is(':checked') ? '1' : '0');
    url += '&filter_orphan_images=' + ($('input[name=\'filter_orphan_images\']').is(':checked') ? '1' : '0');
    url += '&filter_zero_quantity=' + ($('input[name=\'filter_zero_quantity\']').is(':checked') ? '1' : '0');

    window.history.pushState({}, null, 'index.php?route=shopmanager/maintenance/image&user_token=' + MAINT_IMAGE_TOKEN + url);

    $('#report').html('<div class="text-center py-5"><i class="fa-solid fa-circle-notch fa-spin fa-3x text-primary"></i><br><br><strong>Loading products...</strong></div>');
    $('#report').load('index.php?route=shopmanager/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN + url);
});
