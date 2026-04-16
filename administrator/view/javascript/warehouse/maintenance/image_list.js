// Original: warehouse/maintenance/image_list.js
/**
 * ShopManager - Maintenance Image List
 * Page liste produits (opérations bulk eBay, fix, orphelins, suppression)
 * Variables globales requises (déclarées dans image_list.twig) :
 *   MAINT_IMAGE_TOKEN          — user_token OpenCart
 *   TEXT_IMPORT_EBAY_CONFIRM   — et autres TEXT_*
 *   MAINT_BTN_CHECK_EBAY       — libellé bouton check eBay
 *   MAINT_BTN_IMPORT_EBAY      — libellé bouton import eBay
 *   MAINT_TEXT_CHECK_EBAY_TITLE  — titre modal check eBay
 *   MAINT_TEXT_IMPORT_EBAY_TITLE — titre modal import eBay
 */

/**
 * Affiche une notification Bootstrap toast (remplace alert)
 * @param {string} message
 * @param {string} type  success | danger | warning | info
 */
function showToast(message, type) {
    type = type || 'info';
    var toast = $(
        '<div class="toast align-items-center text-white bg-' + type + ' border-0 position-fixed" role="alert"' +
        ' style="top:20px;right:20px;z-index:10999;min-width:260px;">'
        + '<div class="d-flex">'
        + '<div class="toast-body">' + message + '</div>'
        + '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>'
        + '</div></div>'
    );
    $('body').append(toast);
    var bsToast = new bootstrap.Toast(toast[0], { autohide: true, delay: 3500 });
    bsToast.show();
    toast.on('hidden.bs.toast', function() { $(this).remove(); });
}

// Handle checkbox changes
$(document).off('change.maintenanceImageList', '.remove-missing-image').on('change.maintenanceImageList', '.remove-missing-image', function() {
    const checkedCount = $('.remove-missing-image:checked').length;
    if (checkedCount > 0) {
        $('#remove-selected-images').show();
    } else {
        $('#remove-selected-images').hide();
    }
});

// Handle remove selected images button
$(document).off('click.maintenanceImageList', '#remove-selected-images').on('click.maintenanceImageList', '#remove-selected-images', function() {
    const checkedBoxes = $('.remove-missing-image:checked');

    if (checkedBoxes.length === 0) {
        return;
    }

    if (!confirm('Are you sure you want to remove ' + checkedBoxes.length + ' missing image(s) from the database?')) {
        return;
    }

    showModal(document.getElementById('removeProgressModal'));
    $('#remove-progress-bar').css('width', '0%').text('0%');
    $('#remove-current-status').text('Preparing to remove images...');
    $('#remove-details').html('');
    $('#remove-close-btn').hide();

    const imagesToRemove = [];
    checkedBoxes.each(function() {
        imagesToRemove.push({
            product_id: $(this).data('product-id'),
            image_path: $(this).data('image-path'),
            type: $(this).data('type')
        });
    });

    $('#remove-current-status').text('Removing ' + imagesToRemove.length + ' image reference(s)...');
    $('#remove-details').append('<div class="text-info"><i class="fa-solid fa-hourglass-start"></i> Sending request...</div>');

    $.ajax({
        url: 'index.php?route=warehouse/maintenance/image.removeMissingImages&user_token=' + MAINT_IMAGE_TOKEN,
        type: 'post',
        data: { images: imagesToRemove },
        dataType: 'json',
        beforeSend: function() {
            $('#remove-selected-images').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');
            $('#remove-progress-bar').css('width', '50%').text('50%');
        },
        success: function(json) {
            $('#remove-progress-bar').css('width', '100%').text('100%').removeClass('progress-bar-animated');

            if (json['success']) {
                $('#remove-current-status').text('Completed successfully!');
                $('#remove-details').append('<div class="text-success"><i class="fa-solid fa-check-circle"></i> ' + json['success'] + '</div>');

                setTimeout(function() {
                    var url = window.location.search.replace('?route=warehouse/maintenance/image', '?route=warehouse/maintenance/image.list');
                    if (url === '') {
                        url = '?route=warehouse/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN;
                    }
                    $('#report').load(url);
                    hideModal(document.getElementById('removeProgressModal'));
                }, 1500);
            }

            if (json['error']) {
                $('#remove-current-status').text('Error occurred!');
                $('#remove-details').append('<div class="text-danger"><i class="fa-solid fa-exclamation-circle"></i> ' + json['error'] + '</div>');
                $('#remove-close-btn').show();
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            $('#remove-progress-bar').css('width', '100%').removeClass('progress-bar-animated').addClass('bg-danger');
            $('#remove-current-status').text('Request failed!');
            $('#remove-details').append('<div class="text-danger"><i class="fa-solid fa-times-circle"></i> ' + thrownError + '</div>');
            $('#remove-close-btn').show();
        },
        complete: function() {
            $('#remove-selected-images').prop('disabled', false).html('<i class="fa-solid fa-trash"></i> Remove Selected Missing Images');
        }
    });
});

// Handle orphan image checkbox changes
$(document).off('change.maintenanceImageList', '.add-orphan-main, .add-orphan-secondary').on('change.maintenanceImageList', '.add-orphan-main, .add-orphan-secondary', function() {
    const checkedCount = $('.add-orphan-main:checked, .add-orphan-secondary:checked').length;
    if (checkedCount > 0) {
        $('#add-orphan-images-btn').show();
    } else {
        $('#add-orphan-images-btn').hide();
    }

    if ($(this).hasClass('add-orphan-main') && $(this).is(':checked')) {
        $(this).closest('div').find('.add-orphan-secondary').prop('checked', false);
    } else if ($(this).hasClass('add-orphan-secondary') && $(this).is(':checked')) {
        $(this).closest('div').find('.add-orphan-main').prop('checked', false);
    }
});

// Handle add orphan images button
$(document).off('click.maintenanceImageList', '#add-orphan-images-btn').on('click.maintenanceImageList', '#add-orphan-images-btn', function() {
    const mainChecked = $('.add-orphan-main:checked');
    const secondaryChecked = $('.add-orphan-secondary:checked');

    if (mainChecked.length === 0 && secondaryChecked.length === 0) {
        return;
    }

    const productImages = {};

    mainChecked.each(function() {
        const productId = $(this).data('product-id');
        const imagePath = $(this).data('image-path');
        if (!productImages[productId]) { productImages[productId] = []; }
        productImages[productId].push({ image: imagePath, type: 'main' });
    });

    secondaryChecked.each(function() {
        const productId = $(this).data('product-id');
        const imagePath = $(this).data('image-path');
        if (!productImages[productId]) { productImages[productId] = []; }
        productImages[productId].push({ image: imagePath, type: 'secondary' });
    });

    if (!confirm('Are you sure you want to add ' + (mainChecked.length + secondaryChecked.length) + ' orphan image(s) to their products?')) {
        return;
    }

    let completed = 0;
    const totalProducts = Object.keys(productImages).length;

    for (const productId in productImages) {
        $.ajax({
            url: 'index.php?route=warehouse/maintenance/image.addOrphanImages&user_token=' + MAINT_IMAGE_TOKEN,
            type: 'post',
            data: {
                product_id: productId,
                images: productImages[productId]
            },
            dataType: 'json',
            success: function(json) {
                completed++;
                if (json['error']) { console.error('Product ' + productId + ': ' + json['error']); }
                if (completed === totalProducts) {
                    showToast('Orphan images have been added.', 'success');
                    var url = window.location.search.replace('?route=warehouse/maintenance/image', '?route=warehouse/maintenance/image.list');
                    if (url === '') { url = '?route=warehouse/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN; }
                    $('#report').load(url);
                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                completed++;
                console.error('Error for product ' + productId + ': ' + thrownError);
                if (completed === totalProducts) {
                    showToast('Orphan images have been added with some errors. Check console.', 'warning');
                    var url = window.location.search.replace('?route=warehouse/maintenance/image', '?route=warehouse/maintenance/image.list');
                    if (url === '') { url = '?route=warehouse/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN; }
                    $('#report').load(url);
                }
            }
        });
    }
});

// Handle checkbox changes for fix button
$(document).off('change.maintenanceImageList', 'input[name*=\'selected\']').on('change.maintenanceImageList', 'input[name*=\'selected\']', function() {
    const checkedCount = $('input[name*=\'selected\']:checked').length;
    if (checkedCount > 0) {
        $('#btn-check-ebay-images-selected').show();
        $('#btn-import-ebay-selected').show();
        $('#btn-fix-selected').show();
    } else {
        $('#btn-check-ebay-images-selected').hide();
        $('#btn-import-ebay-selected').hide();
        $('#btn-fix-selected').hide();
    }
});

// Handle select all checkbox
$('#select-all').on('change', function() {
    const checkedCount = $('input[name*=\'selected\']:checked').length;
    if (checkedCount > 0) {
        $('#btn-check-ebay-images-selected').show();
        $('#btn-import-ebay-selected').show();
        $('#btn-fix-selected').show();
    } else {
        $('#btn-check-ebay-images-selected').hide();
        $('#btn-import-ebay-selected').hide();
        $('#btn-fix-selected').hide();
    }
});

// Check eBay images count/name vs DB for selected products
$(document).off('click.maintenanceImageList', '#btn-check-ebay-images-selected').on('click.maintenanceImageList', '#btn-check-ebay-images-selected', function() {
    const checkedBoxes = $('input[name*=\'selected\']:checked');

    if (checkedBoxes.length === 0) {
        showToast(TEXT_CHECK_EBAY_NO_SELECTION, 'warning');
        return;
    }

    const confirmText = TEXT_CHECK_EBAY_CONFIRM.replace('%d', checkedBoxes.length);
    if (!confirm(confirmText)) { return; }

    showModal(document.getElementById('checkEbayImagesModal'));
    $('#check-ebay-progress-bar').css('width', '0%').text('0%').removeClass('bg-danger').addClass('progress-bar-animated bg-primary');
    $('#check-ebay-current-status').text(TEXT_CHECK_EBAY_PREPARING);
    $('#check-ebay-details').html('');
    $('#check-ebay-close-btn').hide();

    const productIds = [];
    checkedBoxes.each(function() { productIds.push($(this).val()); });

    let currentIndex = 0;
    const totalProducts = productIds.length;
    let notifyCount = 0;
    let errorCount = 0;
    const notifyProductIds = [];

    function showOnlyProblemRows(problemIds) {
        const problemMap = {};
        for (let i = 0; i < problemIds.length; i++) { problemMap[String(problemIds[i])] = true; }
        let visibleProblems = 0;

        $('table tbody tr').each(function() {
            const row = $(this);
            const rowProductId = String(row.data('product-id') || '');
            const rowCheckbox = row.find('input[name*=\'selected\']');
            if (!rowProductId || rowCheckbox.length === 0) { return; }
            const isProblem = !!problemMap[rowProductId];
            row.toggle(isProblem);
            rowCheckbox.prop('checked', isProblem);
            if (isProblem) { visibleProblems++; }
        });

        $('#select-all').prop('checked', visibleProblems > 0);
        if (visibleProblems > 0) {
            $('#btn-check-ebay-images-selected').show();
            $('#btn-import-ebay-selected').show();
            $('#btn-fix-selected').show();
            $('#check-ebay-details').append('<div class="alert alert-warning mt-2 p-2"><i class="fa-solid fa-filter"></i> Showing only products in problem (eBay has more images than DB). Click Import eBay Images to import them.</div>');
        } else {
            $('#btn-import-ebay-selected').hide();
            $('#btn-fix-selected').hide();
            $('#check-ebay-details').append('<div class="alert alert-success mt-2 p-2"><i class="fa-solid fa-circle-check"></i> No products in problem were found.</div>');
        }
    }

    function processNextCheck() {
        if (currentIndex >= totalProducts) {
            $('#check-ebay-progress-bar').css('width', '100%').text('100%').removeClass('progress-bar-animated').addClass('bg-success');
            $('#check-ebay-current-status').text(TEXT_CHECK_EBAY_COMPLETE + ' - notify: ' + notifyCount + ', errors: ' + errorCount);
            $('#btn-check-ebay-images-selected').prop('disabled', false).html('<i class="fa-brands fa-ebay"></i> ' + MAINT_BTN_CHECK_EBAY);
            showOnlyProblemRows(notifyProductIds);
            $('#check-ebay-close-btn').show();
            return;
        }

        const productId = productIds[currentIndex];
        const progressPercent = Math.round((currentIndex / totalProducts) * 100);
        $('#check-ebay-progress-bar').css('width', progressPercent + '%').text(progressPercent + '%');
        $('#check-ebay-current-status').text(TEXT_CHECK_EBAY_PROCESSING.replace('%1$d', currentIndex + 1).replace('%2$d', totalProducts).replace('%3$s', productId));

        $.ajax({
            url: 'index.php?route=warehouse/maintenance/image.checkEbayImageComparison&user_token=' + MAINT_IMAGE_TOKEN,
            type: 'post',
            data: { product_id: productId },
            dataType: 'json',
            success: function(json) {
                if (json.success) {
                    const badge = json.notify
                        ? '<span class="badge bg-danger">ALERT</span>'
                        : '<span class="badge bg-success">OK</span>';
                    const headerText = json.notify ? TEXT_CHECK_EBAY_NOTIFY_MORE : TEXT_CHECK_EBAY_OK;
                    if (json.notify) { notifyCount++; notifyProductIds.push(String(json.product_id)); }

                    let html = '';
                    html += '<div class="card mb-2" style="border-left: 3px solid ' + (json.notify ? '#dc3545' : '#28a745') + ';">';
                    html += '<div class="card-body p-2">';
                    html += '<div><strong>#' + json.product_id + '</strong> ' + badge + ' - ' + headerText + '</div>';
                    html += '<div class="small text-muted">eBay item: ' + (json.marketplace_item_id || '-') + '</div>';
                    html += '<div class="small">eBay: <strong>' + json.ebay_count + '</strong> | DB(main+sec): <strong>' + json.db_count + '</strong> (main=' + json.db_main_count + ', sec=' + json.db_secondary_count + ')</div>';
                    html += '</div></div>';
                    $('#check-ebay-details').append(html);
                } else {
                    errorCount++;
                    const err = json.error || 'Unknown error';
                    $('#check-ebay-details').append('<div class="alert alert-danger mb-2 p-2"><strong>#' + productId + '</strong> - ' + err + '</div>');
                }
                $('#check-ebay-details').scrollTop($('#check-ebay-details')[0].scrollHeight);
                currentIndex++;
                processNextCheck();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                errorCount++;
                $('#check-ebay-details').append('<div class="alert alert-danger mb-2 p-2"><strong>#' + productId + '</strong> - Error: ' + thrownError + '</div>');
                currentIndex++;
                processNextCheck();
            }
        });
    }

    $('#btn-check-ebay-images-selected').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> ' + MAINT_TEXT_CHECK_EBAY_TITLE);
    processNextCheck();
});

// Handle import eBay images for selected products
$(document).off('click.maintenanceImageList', '#btn-import-ebay-selected').on('click.maintenanceImageList', '#btn-import-ebay-selected', function() {
    const checkedBoxes = $('input[name*=\'selected\']:checked');

    if (checkedBoxes.length === 0) {
        showToast(TEXT_IMPORT_EBAY_NO_SELECTION, 'warning');
        return;
    }

    const confirmText = TEXT_IMPORT_EBAY_CONFIRM.replace('%d', checkedBoxes.length);
    if (!confirm(confirmText)) { return; }

    showModal(document.getElementById('importEbayProgressModal'));
    $('#import-ebay-progress-bar').css('width', '0%').text('0%').removeClass('bg-danger').addClass('progress-bar-animated bg-success');
    $('#import-ebay-current-status').text(TEXT_IMPORT_EBAY_PREPARING);
    $('#import-ebay-products-list').html('');
    $('#import-ebay-close-btn').hide();

    const productIds = [];
    checkedBoxes.each(function() { productIds.push($(this).val()); });

    let currentIndex = 0;
    const totalProducts = productIds.length;
    let successCount = 0;
    let skippedCount = 0;
    let errorCount = 0;

    function processNextImport() {
        if (currentIndex >= totalProducts) {
            $('#import-ebay-progress-bar').css('width', '100%').text('100%').removeClass('progress-bar-animated');
            $('#import-ebay-current-status').text(TEXT_IMPORT_EBAY_COMPLETE + ' (' + successCount + ' ok, ' + skippedCount + ' skipped, ' + errorCount + ' errors)');
            $('#btn-import-ebay-selected').prop('disabled', false).html('<i class="fa-brands fa-ebay"></i> ' + MAINT_BTN_IMPORT_EBAY);

            setTimeout(function() {
                $('#import-ebay-products-list').append('<div class="alert alert-info mt-2 p-2"><i class="fa-solid fa-sync"></i> ' + TEXT_IMPORT_EBAY_REFRESHING + '</div>');
                var url = window.location.search.replace('?route=warehouse/maintenance/image', '?route=warehouse/maintenance/image.list');
                if (url === '') { url = '?route=warehouse/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN; }
                $('#report').load(url, function() { hideModal(document.getElementById('importEbayProgressModal')); });
            }, 1200);
            return;
        }

        const productId = productIds[currentIndex];
        const progressPercent = Math.round((currentIndex / totalProducts) * 100);
        $('#import-ebay-progress-bar').css('width', progressPercent + '%').text(progressPercent + '%');
        $('#import-ebay-current-status').text(TEXT_IMPORT_EBAY_PROCESSING.replace('%1$d', currentIndex + 1).replace('%2$d', totalProducts).replace('%3$s', productId));

        const productCard = $('<div class="card mb-2" id="import-product-' + productId + '" style="border-left: 3px solid #0d6efd;"></div>');
        const productBody = $('<div class="card-body p-2"></div>');
        productBody.html('<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2" role="status"></div><strong>Product #' + productId + '</strong> <span class="ms-2 text-muted">Processing...</span></div>');
        productCard.append(productBody);
        $('#import-ebay-products-list').append(productCard);
        $('#import-ebay-products-list').scrollTop($('#import-ebay-products-list')[0].scrollHeight);

        $.ajax({
            url: 'index.php?route=warehouse/maintenance/image.syncImagesForProductWitheBay&user_token=' + MAINT_IMAGE_TOKEN,
            type: 'post',
            data: { product_id: productId },
            dataType: 'json',
            success: function(json) {
                if (json.success) {
                    successCount++;
                    $('#import-product-' + productId).css('border-left-color', '#28a745');
                    $('#import-product-' + productId + ' .card-body').html('<div class="d-flex align-items-center"><i class="fa-solid fa-check-circle text-success me-2"></i><strong>Product #' + productId + '</strong> <span class="ms-2 text-success">Success</span></div><div class="small text-muted mt-1">' + json.message + '</div>');
                } else if (json.skipped) {
                    skippedCount++;
                    $('#import-product-' + productId).css('border-left-color', '#ffc107');
                    $('#import-product-' + productId + ' .card-body').html('<div class="d-flex align-items-center"><i class="fa-solid fa-forward text-warning me-2"></i><strong>Product #' + productId + '</strong> <span class="ms-2 text-warning">Skipped</span></div><div class="small text-muted mt-1">' + json.message + '</div>');
                } else {
                    errorCount++;
                    $('#import-product-' + productId).css('border-left-color', '#dc3545');
                    $('#import-product-' + productId + ' .card-body').html('<div class="d-flex align-items-center"><i class="fa-solid fa-times-circle text-danger me-2"></i><strong>Product #' + productId + '</strong> <span class="ms-2 text-danger">Error</span></div><div class="small text-danger mt-1">' + (json.error || 'Unknown error') + '</div>');
                }
                currentIndex++;
                processNextImport();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                errorCount++;
                $('#import-product-' + productId).css('border-left-color', '#dc3545');
                $('#import-product-' + productId + ' .card-body').html('<div class="d-flex align-items-center"><i class="fa-solid fa-times-circle text-danger me-2"></i><strong>Product #' + productId + '</strong> <span class="ms-2 text-danger">Failed</span></div><div class="small text-danger mt-1">Error: ' + thrownError + '</div>');
                currentIndex++;
                processNextImport();
            }
        });
    }

    $('#btn-import-ebay-selected').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> ' + MAINT_TEXT_IMPORT_EBAY_TITLE);
    processNextImport();
});

// Handle fix selected images button
$(document).off('click.maintenanceImageList', '#btn-fix-selected').on('click.maintenanceImageList', '#btn-fix-selected', function() {
    const checkedBoxes = $('input[name*=\'selected\']:checked');

    if (checkedBoxes.length === 0) { return; }

    if (!confirm('This will attempt to fix images for ' + checkedBoxes.length + ' selected product(s).\n\nActions:\n• Move images to correct directory\n• Convert to WebP format\n• Update database references\n• Clean up old directories\n\nContinue?')) {
        return;
    }

    showModal(document.getElementById('fixProgressModal'));
    $('#fix-progress-bar').css('width', '0%').text('0%').addClass('progress-bar-animated');
    $('#fix-current-status').text('Preparing to fix images...');
    $('#fix-products-list').html('');
    $('#fix-close-btn').hide();

    const productIds = [];
    checkedBoxes.each(function() { productIds.push($(this).val()); });

    const orphansToAdd = [];
    $('.add-orphan-secondary:checked').each(function() {
        orphansToAdd.push({
            product_id: $(this).data('product-id'),
            image_path: $(this).data('image-path')
        });
    });

    let currentIndex = 0;
    const totalProducts = productIds.length;
    let allRemainingFiles = {};

    $('#fix-current-status').html('<i class="fa-solid fa-spinner fa-spin"></i> Processing ' + totalProducts + ' product(s)...');
    if (orphansToAdd.length > 0) {
        $('#fix-products-list').append('<div class="alert alert-info mb-2 p-2"><i class="fa-solid fa-plus-circle"></i> <strong>' + orphansToAdd.length + ' orphan image(s) will be added</strong></div>');
    }

    function processNextProduct() {
        if (currentIndex >= totalProducts) {
            $('#fix-progress-bar').css('width', '100%').text('100%').removeClass('progress-bar-animated').addClass('bg-success');
            $('#fix-current-status').html('<i class="fa-solid fa-check-circle text-success"></i> All products processed successfully!');
            $('#btn-fix-selected').prop('disabled', false).html('<i class="fa-solid fa-wrench"></i> Fix Selected Images');

            if (Object.keys(allRemainingFiles).length > 0) {
                $('#fix-products-list').append('<div class="alert alert-warning mt-2 p-2"><i class="fa-solid fa-folder-open"></i> <strong>Old directories contain remaining files</strong></div>');
                handleRemainingFiles(allRemainingFiles, 'fixProgressModal');
            } else {
                setTimeout(function() {
                    $('#fix-products-list').append('<div class="alert alert-success mt-2 p-2"><i class="fa-solid fa-sync"></i> Refreshing product list...</div>');
                    var url = window.location.search.replace('?route=warehouse/maintenance/image', '?route=warehouse/maintenance/image.list');
                    if (url === '') { url = '?route=warehouse/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN; }
                    $('#report').load(url, function() { hideModal(document.getElementById('fixProgressModal')); });
                }, 1500);
            }
            return;
        }

        const productId = productIds[currentIndex];
        const progressPercent = Math.round((currentIndex / totalProducts) * 100);

        $('#fix-progress-bar').css('width', progressPercent + '%').text(progressPercent + '%');
        $('#fix-current-status').html('<i class="fa-solid fa-spinner fa-spin"></i> Processing product #' + productId + ' (' + (currentIndex + 1) + ' of ' + totalProducts + ')');

        const productCard = $('<div class="card mb-2" id="product-' + productId + '" style="border-left: 3px solid #0d6efd;"></div>');
        const productBody = $('<div class="card-body p-2"></div>');
        productBody.html('<div class="d-flex align-items-center"><div class="spinner-border spinner-border-sm me-2" role="status"></div><strong>Product #' + productId + '</strong> <span class="ms-2 text-muted">Processing...</span></div>');
        productCard.append(productBody);
        $('#fix-products-list').append(productCard);
        $('#fix-products-list').scrollTop($('#fix-products-list')[0].scrollHeight);

        const productOrphans = orphansToAdd.filter(o => o.product_id == productId);

        $.ajax({
            url: 'index.php?route=warehouse/maintenance/image.fixImages&user_token=' + MAINT_IMAGE_TOKEN,
            type: 'post',
            data: {
                product_ids: [productId],
                orphans: productOrphans
            },
            dataType: 'json',
            success: function(json) {
                if (json['success']) {
                    $('#product-' + productId).css('border-left-color', '#28a745');
                    $('#product-' + productId + ' .card-body').html('<div class="d-flex align-items-center"><i class="fa-solid fa-check-circle text-success me-2"></i><strong>Product #' + productId + '</strong> <span class="ms-2 text-success">Success</span></div><div class="small text-muted mt-1">' + json['success'] + '</div>');

                    if (json['remaining_files']) {
                        for (let pid in json['remaining_files']) { allRemainingFiles[pid] = json['remaining_files'][pid]; }
                        $('#product-' + productId + ' .card-body').append('<div class="small text-warning mt-1"><i class="fa-solid fa-exclamation-triangle"></i> Old directory has remaining files</div>');
                    }
                } else if (json['error']) {
                    $('#product-' + productId).css('border-left-color', '#dc3545');
                    $('#product-' + productId + ' .card-body').html('<div class="d-flex align-items-center"><i class="fa-solid fa-times-circle text-danger me-2"></i><strong>Product #' + productId + '</strong> <span class="ms-2 text-danger">Error</span></div><div class="small text-danger mt-1">' + json['error'] + '</div>');
                }
                currentIndex++;
                processNextProduct();
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $('#product-' + productId).css('border-left-color', '#dc3545');
                $('#product-' + productId + ' .card-body').html('<div class="d-flex align-items-center"><i class="fa-solid fa-times-circle text-danger me-2"></i><strong>Product #' + productId + '</strong> <span class="ms-2 text-danger">Failed</span></div><div class="small text-danger mt-1">Error: ' + thrownError + '</div>');
                currentIndex++;
                processNextProduct();
            }
        });
    }

    $('#btn-fix-selected').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Processing...');
    processNextProduct();
});

// Handle remaining files in old directories
function handleRemainingFiles(remainingFiles, modalId) {
    let message = 'The following old directories still contain files:\n\n';
    let directories = [];
    let fileCount = 0;

    for (let productId in remainingFiles) {
        let data = remainingFiles[productId];
        message += 'Product ' + productId + ' - Directory: ' + data.directory + '\n';
        message += 'Files: ' + data.files.join(', ') + '\n\n';
        directories.push(data);
        fileCount += data.files.length;

        $('#fix-products-list').append('<div class="alert alert-warning mb-2 p-2"><i class="fa-solid fa-folder"></i> <strong>Product ' + productId + ':</strong> ' + data.files.length + ' file(s) in ' + data.directory + '<br><small class="text-muted">' + data.files.join(', ') + '</small></div>');
    }

    message += 'Do you want to delete these ' + fileCount + ' remaining file(s)?';

    if (confirm(message)) {
        $('#fix-current-status').html('<i class="fa-solid fa-trash-alt"></i> Deleting remaining files...');
        $('#fix-products-list').append('<div class="alert alert-info mb-2 p-2"><i class="fa-solid fa-trash-alt"></i> Deleting ' + fileCount + ' file(s)...</div>');

        $.ajax({
            url: 'index.php?route=warehouse/maintenance/image.deleteRemainingFiles&user_token=' + MAINT_IMAGE_TOKEN,
            type: 'post',
            data: { directories: directories },
            dataType: 'json',
            success: function(json) {
                if (json['success']) {
                    $('#fix-current-status').html('<i class="fa-solid fa-check-circle text-success"></i> Cleanup completed!');
                    $('#fix-products-list').append('<div class="alert alert-success mb-2 p-2"><i class="fa-solid fa-check-circle"></i> ' + json['success'] + '</div>');
                }
                if (json['error']) {
                    $('#fix-products-list').append('<div class="alert alert-danger mb-2 p-2"><i class="fa-solid fa-exclamation-circle"></i> ' + json['error'] + '</div>');
                }
                setTimeout(function() {
                    $('#fix-products-list').append('<div class="alert alert-success mt-2 p-2"><i class="fa-solid fa-sync"></i> Refreshing product list...</div>');
                    var url = window.location.search.replace('?route=warehouse/maintenance/image', '?route=warehouse/maintenance/image.list');
                    if (url === '') { url = '?route=warehouse/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN; }
                    $('#report').load(url, function() { hideModal(document.getElementById(modalId)); });
                }, 1500);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                $('#fix-products-list').append('<div class="alert alert-danger mb-2 p-2"><i class="fa-solid fa-times-circle"></i> Error deleting files: ' + thrownError + '</div>');
                $('#fix-close-btn').show();
            }
        });
    } else {
        $('#fix-progress-bar').css('width', '100%').text('100%').removeClass('progress-bar-animated');
        $('#fix-current-status').text('Completed (files kept)');
        $('#fix-details').append('<div class="text-info"><i class="fa-solid fa-info-circle"></i> Old files were kept in place</div>');

        setTimeout(function() {
            var url = window.location.search.replace('?route=warehouse/maintenance/image', '?route=warehouse/maintenance/image.list');
            if (url === '') { url = '?route=warehouse/maintenance/image.list&user_token=' + MAINT_IMAGE_TOKEN; }
            $('#report').load(url);
            hideModal(document.getElementById(modalId));
        }, 1500);
    }
}

if (typeof window.initImageResolutionCheck !== 'function') {
    window.initImageResolutionCheck = function() {
        document.querySelectorAll('.actual-image-container').forEach(function(container) {
            const fullsizeImg = container.querySelector('.fullsize-actual-image, .actual-image-preview');
            const resolutionOverlay = container.querySelector('.fullsize-resolution-overlay');
            if (!fullsizeImg || !resolutionOverlay) { return; }
            const resolution = fullsizeImg.getAttribute('data-resolution');
            if (!resolution) { return; }
            const parts = resolution.split('x');
            const width = parseInt(parts[0], 10);
            const height = parseInt(parts[1], 10);
            resolutionOverlay.textContent = resolution;
            resolutionOverlay.classList.remove('low-res', 'good-res');
            if (!isNaN(width) && !isNaN(height) && (width < 400 || height < 600)) {
                resolutionOverlay.classList.add('low-res');
            } else {
                resolutionOverlay.classList.add('good-res');
            }
        });
    };
}

if (typeof window.initImagePreview !== 'function') {
    window.initImagePreview = function() {
        document.querySelectorAll('.actual-image-container').forEach(function(container) {
            if (container.dataset.previewInitialized === 'true') { return; }
            container.dataset.previewInitialized = 'true';
            const thumbnail = container.querySelector('.img-thumbnail, .thumbnail-actual-image');
            const wrapper = container.querySelector('.fullsize-actual-image-wrapper');
            if (!thumbnail || !wrapper) { return; }

            thumbnail.addEventListener('mouseenter', function() {
                const oldClone = document.getElementById('temp-fullsize-preview');
                if (oldClone) { oldClone.remove(); }
                const clone = wrapper.cloneNode(true);
                clone.id = 'temp-fullsize-preview';
                clone.style.display = 'block';
                clone.addEventListener('mouseleave', function() {
                    const self = document.getElementById('temp-fullsize-preview');
                    if (self) { self.remove(); }
                });
                document.body.appendChild(clone);
            });

            thumbnail.addEventListener('mouseleave', function() {
                setTimeout(function() {
                    const hovered = document.querySelector('#temp-fullsize-preview:hover');
                    if (!hovered) {
                        const clone = document.getElementById('temp-fullsize-preview');
                        if (clone) { clone.remove(); }
                    }
                }, 80);
            });

            wrapper.addEventListener('mouseenter', function() { wrapper.style.display = 'block'; });
            wrapper.addEventListener('mouseleave', function() { wrapper.style.display = 'none'; });
        });

        document.removeEventListener('mouseleave', window.__maintenanceImagePreviewBodyLeave);
        window.__maintenanceImagePreviewBodyLeave = function() {
            const clone = document.getElementById('temp-fullsize-preview');
            if (clone) { clone.remove(); }
        };
        document.addEventListener('mouseleave', window.__maintenanceImagePreviewBodyLeave);
    };
}

// Initialize image resolution checking and preview for orphan images
if (typeof initImageResolutionCheck === 'function') { initImageResolutionCheck(); }
if (typeof initImagePreview === 'function') { initImagePreview(); }
