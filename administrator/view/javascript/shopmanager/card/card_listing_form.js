/**
 * card_listing_form.js
 * Handles general form interactions (tab-general, tab-ebay specifics).
 * Tab-specific logic lives in:
 *   card_listing_tab_cards.js
 *   card_listing_tab_descriptions.js
 *   card_listing_import.js
 */

// Utility functions (duplicated for autonomy — see project conventions)
function htmlspecialchars(str) {
    if (typeof str === 'undefined' || str === null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function htmlspecialchars_decode(str) {
    if (typeof str === 'undefined' || str === null) return '';
    return String(str)
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'")
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&amp;/g, '&');
}

(function($) {
    'use strict';

    $(document).ready(function() {
        initSpecificsTable();
        initMigrateImages();
        initSyncOffers();
        initRepublishOffers();
        initLocationHandler();
    });

    /**
     * Migrate Google images to eBay
     */
    function initMigrateImages() {
        $('#btn-migrate-images').on('click', function() {
            var $modal = $('#modal-migrate-images');
            var $body  = $('#migrate-modal-body');

            // Reset modal body to spinner
            $body.html(
                '<div class="text-center py-4">' +
                '<div class="spinner-border text-warning" role="status"></div>' +
                '<p class="mt-2 text-muted">Uploading images to eBay\u2026</p>' +
                '</div>'
            );
            $('#btn-reload-after-migrate').hide();

            $modal.modal('show');

            $.ajax({
                url: URL_MIGRATE_IMAGES + '&listing_id=' + LISTING_ID,
                type: 'GET',
                dataType: 'json',
                success: function(json) {
                    if (json.error) {
                        $body.html(
                            '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-1"></i>' +
                            htmlspecialchars(json.error) + '</div>'
                        );
                        return;
                    }

                    var r = json.result;
                    var statusClass = r.status === 'migrated'   ? 'success' :
                                      r.status === 'no_google_images' ? 'info' : 'warning';
                    var statusIcon  = r.status === 'migrated'   ? 'fa-circle-check' :
                                      r.status === 'no_google_images' ? 'fa-circle-info' : 'fa-triangle-exclamation';

                    var html =
                        '<div class="alert alert-' + statusClass + ' mb-3">' +
                        '<i class="fa-solid ' + statusIcon + ' me-1"></i> Status: <strong>' + htmlspecialchars(r.status) + '</strong>' +
                        '</div>' +
                        '<table class="table table-sm table-bordered">' +
                        '<tr><th>Uploaded to eBay</th><td><span class="badge bg-success">' + (r.uploaded || 0) + '</span></td></tr>' +
                        '<tr><th>Already on eBay</th><td><span class="badge bg-secondary">' + (r.already_on_ebay || 0) + '</span></td></tr>' +
                        '<tr><th>Failed</th><td><span class="badge bg-' + (r.failed > 0 ? 'danger' : 'secondary') + '">' + (r.failed || 0) + '</span></td></tr>' +
                        '</table>';

                    if (r.failed_details && r.failed_details.length > 0) {
                        html += '<h6 class="text-danger mt-3">Failed uploads:</h6><ul class="small text-danger">';
                        $.each(r.failed_details, function(i, f) {
                            html += '<li><strong>#' + f.image_id + '</strong> ' + htmlspecialchars(f.error) + '<br><small class="text-muted">' + htmlspecialchars(f.url) + '</small></li>';
                        });
                        html += '</ul>';
                    }

                    $body.html(html);

                    if (r.uploaded > 0) {
                        $('#btn-reload-after-migrate').show();
                    }
                },
                error: function() {
                    $body.html('<div class="alert alert-danger">AJAX error — check server logs.</div>');
                }
            });
        });
    }

    /**
     * Sync eBay offer_id, published status and errors for cards missing an offer.
     */
    function initSyncOffers() {
        if (typeof URL_SYNC_OFFERS === 'undefined') return;

        $('#btn-sync-offers').on('click', function() {
            var $modal = $('#modal-sync-offers');
            var $body  = $('#sync-offers-modal-body');

            $body.html(
                '<div class="text-center py-4">' +
                '<div class="spinner-border text-info" role="status"></div>' +
                '<p class="mt-2 text-muted">Querying eBay for offer status\u2026</p>' +
                '</div>'
            );
            $('#btn-reload-after-sync').hide();
            $modal.modal('show');

            $.ajax({
                url: URL_SYNC_OFFERS + '&listing_id=' + LISTING_ID,
                type: 'GET',
                dataType: 'json',
                success: function(json) {
                    if (json.error) {
                        $body.html(
                            '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-1"></i>' +
                            htmlspecialchars(json.error) + '</div>'
                        );
                        return;
                    }

                    var r = json.result;
                    var html =
                        '<div class="alert alert-secondary mb-3">' +
                        '<strong>Total cards checked:</strong> ' + (r.total || 0) +
                        ' &nbsp;|&nbsp; <span class="text-success"><strong>Synced:</strong> ' + (r.synced || 0) + '</span>' +
                        ' &nbsp;|&nbsp; <span class="text-warning"><strong>Not found on eBay:</strong> ' + (r.not_found || 0) + '</span>' +
                        ' &nbsp;|&nbsp; <span class="text-danger"><strong>Failed:</strong> ' + (r.failed || 0) + '</span>' +
                        '</div>';

                    if (r.total === 0) {
                        html += '<div class="alert alert-success">All cards already have an offer_id — nothing to sync.</div>';
                        $body.html(html);
                        return;
                    }

                    html += '<table class="table table-sm table-bordered small">' +
                        '<thead><tr>' +
                        '<th>SKU</th><th>Offer ID</th><th>Status</th><th>Published</th><th>Error</th>' +
                        '</tr></thead><tbody>';

                    $.each(r.details, function(i, d) {
                        var resultClass = d.result === 'synced'    ? 'table-success' :
                                          d.result === 'not_found' ? 'table-warning' : 'table-danger';
                        var statusBadge = d.result === 'synced' && d.published
                            ? '<span class="badge bg-success">PUBLISHED</span>'
                            : d.result === 'synced' && !d.published
                                ? '<span class="badge bg-warning text-dark">UNPUBLISHED</span>'
                                : '<span class="badge bg-secondary">' + htmlspecialchars(d.status || d.result) + '</span>';

                        html += '<tr class="' + resultClass + '">' +
                            '<td><small>' + htmlspecialchars(d.sku) + '</small></td>' +
                            '<td><small>' + htmlspecialchars(d.offer_id || '—') + '</small></td>' +
                            '<td>' + statusBadge + '</td>' +
                            '<td class="text-center">' + (d.published ? '<i class="fa-solid fa-check text-success"></i>' : '<i class="fa-solid fa-xmark text-danger"></i>') + '</td>' +
                            '<td class="text-danger small">' + htmlspecialchars(d.error || '') + '</td>' +
                            '</tr>';
                    });

                    html += '</tbody></table>';
                    $body.html(html);

                    if (r.synced > 0) {
                        $('#btn-reload-after-sync').show();
                    }
                },
                error: function() {
                    $body.html('<div class="alert alert-danger">AJAX error — check server logs.</div>');
                }
            });
        });
    }

    /**
     * eBay Specifics table — add / remove rows
     */
    function initSpecificsTable() {
        var specificRowIndex = $('#specifics-table tbody tr').length;

        // Add new specific row
        $('#add-specific').on('click', function() {
            var html = '<tr>' +
                '<td><input type="text" name="specifics[' + specificRowIndex + '][name]" value="" class="form-control" placeholder="Name"/></td>' +
                '<td><input type="text" name="specifics[' + specificRowIndex + '][value]" value="" class="form-control" placeholder="Value"/></td>' +
                '<td><button type="button" class="btn btn-sm btn-danger remove-specific"><i class="fa fa-trash"></i></button></td>' +
                '</tr>';
            $('#specifics-table tbody').append(html);
            specificRowIndex++;
        });

        // Remove specific row
        $('#specifics-table').on('click', '.remove-specific', function() {
            $(this).closest('tr').remove();
        });
    }

    /**
     * Re-publish cards that have an offer_id but are not yet published.
     */
    function initRepublishOffers() {
        if (typeof URL_REPUBLISH_OFFERS === 'undefined') return;

        $('#btn-republish-offers').on('click', function() {
            var $modal = $('#modal-republish-offers');
            var $body  = $('#republish-offers-modal-body');

            $body.html(
                '<div class="text-center py-4">' +
                '<div class="spinner-border text-danger" role="status"></div>' +
                '<p class="mt-2 text-muted">Publishing offers on eBay\u2026</p>' +
                '</div>'
            );
            $('#btn-reload-after-republish').hide();
            $modal.modal('show');

            $.ajax({
                url: URL_REPUBLISH_OFFERS + '&listing_id=' + LISTING_ID,
                type: 'GET',
                dataType: 'json',
                success: function(json) {
                    if (json.error) {
                        $body.html(
                            '<div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation me-1"></i>' +
                            htmlspecialchars(json.error) + '</div>'
                        );
                        return;
                    }

                    var r = json.result;

                    if (r.total === 0) {
                        $body.html('<div class="alert alert-success">No unpublished offers found — nothing to do.</div>');
                        return;
                    }

                    var html =
                        '<div class="alert alert-secondary mb-3">' +
                        '<strong>Total attempted:</strong> ' + (r.total || 0) +
                        ' &nbsp;|&nbsp; <span class="text-success"><strong>Published:</strong> ' + (r.published || 0) + '</span>' +
                        ' &nbsp;|&nbsp; <span class="text-danger"><strong>Failed:</strong> ' + (r.failed || 0) + '</span>' +
                        '</div>';

                    html += '<table class="table table-sm table-bordered small">' +
                        '<thead><tr>' +
                        '<th>Card ID</th><th>Offer ID</th><th>Result</th><th>Error / Listing ID</th>' +
                        '</tr></thead><tbody>';

                    $.each(r.details, function(i, d) {
                        var rowClass = d.result === 'published' ? 'table-success' : 'table-danger';
                        var badge    = d.result === 'published'
                            ? '<span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>PUBLISHED</span>'
                            : '<span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i>FAILED</span>';
                        var detail   = d.result === 'published'
                            ? (d.listing_id ? 'Listing: ' + htmlspecialchars(d.listing_id) : '')
                            : '<span class="text-danger">' + htmlspecialchars(d.error || '') + '</span>';

                        html += '<tr class="' + rowClass + '">' +
                            '<td>' + d.card_id + '</td>' +
                            '<td><small>' + htmlspecialchars(d.offer_id) + '</small></td>' +
                            '<td>' + badge + '</td>' +
                            '<td class="small">' + detail + '</td>' +
                            '</tr>';
                    });

                    html += '</tbody></table>';
                    $body.html(html);

                    if (r.published > 0) {
                        $('#btn-reload-after-republish').show();
                    }
                },
                error: function() {
                    $body.html('<div class="alert alert-danger">AJAX error — check server logs.</div>');
                }
            });
        });
    }

    // ── Recalculate Batches ─────────────────────────────────────────────────────
    // Handles both the card-header button (#btn-assign-batches)
    // and the warning-banner button (.btn-assign-batches-trigger)
    function doAssignBatches($btn) {
        var url        = $btn.data('url');
        var listing_id = $btn.data('listing-id');
        var origHtml   = $btn.html();
        var i18n       = (typeof BATCH_I18N !== 'undefined') ? BATCH_I18N : {};

        $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin me-1"></i>' + (i18n.calculating || 'Calculating…'));

        $.ajax({
            url: url,
            type: 'POST',
            dataType: 'json',
            data: { listing_id: listing_id },
            success: function (r) {
                $btn.prop('disabled', false).html(origHtml);

                if (!r.success) {
                    alert('Error: ' + (r.error || 'Unknown error'));
                    return;
                }

                // Show warnings (migration notice, multi-batch info)
                if (r.warnings && r.warnings.length > 0) {
                    var warnHtml = '<div class="alert alert-info alert-dismissible mt-2" id="batch-assign-warnings">' +
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                        '<ul class="mb-0">';
                    $.each(r.warnings, function (i, w) { warnHtml += '<li>' + w + '</li>'; });
                    warnHtml += '</ul></div>';
                    $('#batch-assign-warnings').remove();
                    $('#ebay-batches-wrap').before(warnHtml);
                }

                // Hide the "needs_batch_warning" banner if present
                $('#batch-warning-banner').fadeOut(400, function () { $(this).remove(); });

                    var statusLabel = {0: '<span class="badge bg-warning text-dark">' + (i18n.status_draft      || 'Draft')      + '</span>',
                                       1: '<span class="badge bg-success">'            + (i18n.status_published || 'Published')  + '</span>',
                                       2: '<span class="badge bg-secondary">'          + (i18n.status_ended    || 'Ended')      + '</span>'};
                    var batchColors = {1:'primary', 2:'success', 3:'warning', 4:'danger'};

                    // Build description and totals lookup by batch_name
                    var descMap   = {};
                    var totalsMap = {};
                    if (r.batch_descriptions) {
                        $.each(r.batch_descriptions, function (bid, d) { descMap[bid] = d; });
                    }
                    if (r.batch_totals) {
                        $.each(r.batch_totals, function (bid, t) { totalsMap[bid] = t; });
                    }

                    if (r.batches && r.batches.length > 0) {
                        var html = '<table class="table table-sm table-bordered mb-0" id="ebay-batches-table">' +
                            '<thead class="table-light"><tr>' +
                            '<th style="width:60px">'  + (i18n.column_batch      || 'Batch')      + '</th>' +
                            '<th style="width:100px">Cards</th>' +
                            '<th style="width:140px">eBay Item ID</th>' +
                            '<th style="width:80px">'  + (i18n.column_variations || 'Variations') + '</th>' +
                            '<th style="width:110px">Total Value</th>' +
                            '<th style="width:90px">Status</th>' +
                            '<th style="width:160px">' + (i18n.col_published     || 'Published')  + '</th>' +
                            '</tr></thead><tbody>';

                        var grandTotal = 0;
                        $.each(r.batches, function (i, b) {
                            var bn    = parseInt(b.batch_name);
                            var color = bn >= 99 ? 'dark' : (batchColors[bn] || 'secondary');
                            var desc  = descMap[bn]  || {};
                            var tot   = totalsMap[bn] || {};

                            var itemId = desc.ebay_item_id
                                ? '<a href="https://www.ebay.ca/itm/' + htmlspecialchars(desc.ebay_item_id) + '" target="_blank">' + htmlspecialchars(desc.ebay_item_id) + '</a>'
                                : '<span class="text-muted">—</span>';

                            var totalValue = parseFloat(tot.total_value || 0);
                            grandTotal += totalValue;
                            var totalCell = totalValue > 0
                                ? '<strong>$ ' + totalValue.toFixed(2) + '</strong>'
                                : '<span class="text-muted">—</span>';

                            html += '<tr>' +
                                '<td class="text-center"><span class="badge bg-' + color + '">B' + bn + '</span></td>' +
                                '<td class="text-center small text-muted">#' + (b.card_sort_start || '—') + '&ndash;#' + (b.card_sort_end || '—') + '</td>' +
                                '<td>' + itemId + '</td>' +
                                '<td class="text-center">' + (b.variation_count || 0) + '</td>' +
                                '<td class="text-end">' + totalCell + '</td>' +
                                '<td class="text-center">' + (statusLabel[parseInt(desc.status)] || statusLabel[0]) + '</td>' +
                                '<td class="text-muted small">' + (desc.date_published || '—') + '</td>' +
                                '</tr>';
                        });

                        html += '</tbody>' +
                            '<tfoot class="table-light fw-bold"><tr>' +
                            '<td colspan="4" class="text-end text-muted small pe-2">' + (i18n.grand_total || 'Grand Total') + '</td>' +
                            '<td class="text-end text-success">$ ' + grandTotal.toFixed(2) + '</td>' +
                            '<td colspan="2"></td>' +
                            '</tr></tfoot>' +
                            '</table>';
                        $('#ebay-batches-wrap').html(html);
                    } else {
                        $('#ebay-batches-wrap').html('<div class="p-3 text-muted">' + (i18n.no_batches || 'No batches assigned.') + '</div>');
                    }

                    // Update badge on each card row in the cards table
                    if (r.summary) {
                        $.each(r.summary, function (i, bInfo) {
                            var bn      = bInfo.batch;
                            var color   = bn >= 99 ? 'dark' : (batchColors[bn] || 'secondary');
                            var badge   = '<span class="badge bg-' + color + '" title="eBay Batch ' + bn + '">B' + bn + '</span>';
                            $.each(bInfo.card_ids, function (j, cid) {
                                $('tr[data-card-id="' + cid + '"] .batch-badge-cell').html(badge);
                            });
                        });
                    }
                },
                error: function () {
                    $btn.prop('disabled', false).html(origHtml);
                    alert(i18n.ajax_error || 'AJAX error — check server logs.');
                }
            });
        }

    var $btnAssign = $('#btn-assign-batches');
    if ($btnAssign.length) {
        $btnAssign.on('click', function () { doAssignBatches($(this)); });
    }
    // Warning-banner button (uses class, shown only when needs_batch_warning)
    $(document).on('click', '.btn-assign-batches-trigger', function () {
        doAssignBatches($(this));
    });

    function initLocationHandler() {

        // ── Bouton QR manuel ──────────────────────────────────────────────────
    var locationInputLabel = document.getElementById('input-location');
    var printLabelButton = document.getElementById('btn-print-label');
 // Impression à partir de la location
    printLabelButton.addEventListener('click', function () {
        var location = locationInputLabel.value.trim();
        if (location) {
            openPrintLabel(location, '', 1, '', 'yes');
        } else {
            alert(TEXT_ALERT_ENTER_LOCATION_PRINT);
        }
    });

        // ── Uppercase en temps réel ───────────────────────────────────────────
        $(document).on('input', '#input-location', function () {
            var pos = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(pos, pos);
        });

        // ── Enter = save location + ouvre QR ─────────────────────────────────
        $(document).on('keydown', '#input-location', function (e) {
            if (e.key !== 'Enter') return;
            e.preventDefault();

            var $inp       = $(this);
            var url        = $inp.data('url');
            var listing_id = $inp.data('listing-id');
            var userToken  = $inp.data('user-token') || new URLSearchParams(window.location.search).get('user_token');
            var location   = $inp.val().trim().toUpperCase();
            $inp.val(location);
            var $feedback  = $('#location-save-feedback');

            // Indicateur immédiat que le JS a bien capté l'événement
            $inp.css('border-color', 'orange');

            if (!url || !listing_id) {
                $feedback.removeClass('text-success text-danger').addClass('text-warning')
                          .html('<i class="fa-solid fa-triangle-exclamation me-1"></i>Sauvegarde le listing d\'abord.')
                          .show();
                setTimeout(function () { $inp.css('border-color', ''); $feedback.fadeOut(400); }, 3000);
                return;
            }

            $inp.addClass('bg-light').css('border-color', '');
            $feedback.hide();

            $.ajax({
                url:      url,
                type:     'POST',
                dataType: 'json',
                data:     { listing_id: listing_id, location: location },
                success: function (r) {
                    $inp.removeClass('bg-light');
                    if (r.success) {
                        $inp.addClass('border-success');
                        $feedback.removeClass('text-danger').addClass('text-success')
                                  .html('<i class="fa-solid fa-circle-check me-1"></i>Location sauvegardée !')
                                  .show();
                        setTimeout(function () {
                            $inp.removeClass('border-success');
                            $feedback.fadeOut(400);
                        }, 2500);
                        if (location) {
                            openPrintLabel(location, '', 1, '', 'yes');
                        }
                    } else {
                        $inp.addClass('border-danger');
                        $feedback.removeClass('text-success').addClass('text-danger')
                                  .html('<i class="fa-solid fa-triangle-exclamation me-1"></i>' + (r.error || 'Erreur sauvegarde.'))
                                  .show();
                        setTimeout(function () { $inp.removeClass('border-danger'); }, 2500);
                    }
                },
                error: function (xhr, status, error) {
                    $inp.removeClass('bg-light').addClass('border-danger');
                    $feedback.removeClass('text-success').addClass('text-danger')
                              .html('<i class="fa-solid fa-triangle-exclamation me-1"></i>Erreur AJAX — voir logs serveur.')
                              .show();
                    setTimeout(function () { $inp.removeClass('border-danger'); }, 2500);
                }
            });
        });
    }

})(window.jQuery || window.$);



function openPrintLabel(sku = '', upc = '', quantity = 1, location = '', force = 'no') {
    var userToken = new URLSearchParams(window.location.search).get('user_token');

    // condition_id : null-safe
    var conditionEl  = document.querySelector('input[name="condition_id"]');
    var condition_id = conditionEl ? conditionEl.value : '';

    // Si le SKU est identique au UPC, on ignore le UPC
    if (sku === upc) {
        //upc = '';
    }

    const url = 'index.php?route=shopmanager/tools.create_label' +
        '&sku='      + encodeURIComponent(sku      || '') +
        '&upc='      + encodeURIComponent(upc      || '') +
        '&quantity=' + encodeURIComponent(quantity) +
        '&location=' + encodeURIComponent(location || '') +
        '&user_token=' + encodeURIComponent(userToken);

    if ((condition_id && condition_id !== '1000') || upc == '' || force == 'yes') {
        window.open(url, 'printWindow', 'width=288,height=96');
    }
}

