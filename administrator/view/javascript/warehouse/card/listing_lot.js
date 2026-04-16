// Original: warehouse/card/listing_lot.js
/**
 * card_listing_lot.js
 * Autonomous JS module for Lot eBay listing actions.
 * Depends on globals: LISTING_ID, URL_PUBLISH_LOT, URL_END_LOT, URL_LOT_PREVIEW,
 *                     LOT_CALCULATED_PRICE (all injected by card_listing_form.twig)
 *
 * Pattern: STANDALONE – utility functions duplicated intentionally for isolation.
 */

/* ── Utility (autonomous duplication – do not refactor to shared file) ──────── */
function lotNumberFormat(n, dec) {
    dec = dec === undefined ? 2 : dec;
    return parseFloat(n || 0).toFixed(dec);
}

/* ── Lot panel bootstrap ─────────────────────────────────────────────────────── */
$(function () {
    if (!$('#lot-ebay-panel').length) return; // Panel not on page

    // ── Refresh stats ──────────────────────────────────────────────────────────
    $('#btn-lot-refresh').on('click', function () {
        loadLotPreview();
    });

    // ── Reset price to auto-calculated ────────────────────────────────────────
    $('#btn-reset-lot-price').on('click', function () {
        var calcPrice = typeof LOT_CALCULATED_PRICE !== 'undefined' ? LOT_CALCULATED_PRICE : 0;
        $('#lot-price-input').val(lotNumberFormat(calcPrice));
    });

    // ── Publish lot ───────────────────────────────────────────────────────────
    $('#btn-publish-lot').on('click', function () {
        if (!confirm(TEXT_LOT_CONFIRM_PUBLISH)) return;
        publishLot();
    });

    // ── End lot ───────────────────────────────────────────────────────────
    $(document).on('click', '#btn-end-lot', function () {
        if (!confirm(TEXT_LOT_CONFIRM_END)) return;
        endLot();
    });
});

/**
 * Charge le preview du lot (prix calculé, stats) depuis le serveur.
 */
function loadLotPreview() {
    var $refresh = $('#btn-lot-refresh');
    $refresh.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
        url: URL_LOT_PREVIEW,
        type: 'GET',
        data: { listing_id: LISTING_ID },
        dataType: 'json',
        success: function (json) {
            if (json.success) {
                $('#lot-stat-cards').text(json.card_count);
                $('#lot-stat-qty').text(json.total_qty);
                $('#lot-stat-price').text('$ ' + lotNumberFormat(json.calculated_price));
                $('#lot-stat-floored').text(json.floored_count);

                // Update global and reset price field if empty
                LOT_CALCULATED_PRICE = json.calculated_price;
                if (!$('#lot-price-input').val()) {
                    $('#lot-price-input').val(lotNumberFormat(json.calculated_price));
                }
            } else {
                console.warn('[lot] getLotPreview error:', json.error);
            }
        },
        error: function () {
            console.error('[lot] getLotPreview AJAX error');
        },
        complete: function () {
            $refresh.prop('disabled', false).html('<i class="fa-solid fa-rotate me-1"></i>' + BUTTON_LOT_REFRESH);
        }
    });
}

/**
 * Publie le lot sur eBay via AJAX POST.
 */
function publishLot() {
    var $btn     = $('#btn-publish-lot');
    var $spinner = $('#lot-publish-spinner');
    var lotPrice = parseFloat($('#lot-price-input').val()) || 0;

    if (lotPrice <= 0) {
        alert(TEXT_LOT_PRICE_ERROR);
        return;
    }

    $btn.prop('disabled', true);
    $spinner.removeClass('d-none');

    $.ajax({
        url: URL_PUBLISH_LOT,
        type: 'POST',
        data: {
            listing_id:             LISTING_ID,
            lot_price:              lotPrice,
            lot_weight:             parseFloat($('#lot-weight-input').val()) || 0,
            lot_weight_class_id:    parseInt($('#lot-weight-class-input').val()) || 5,
            lot_length:             parseFloat($('#lot-length-input').val()) || 0,
            lot_width:              parseFloat($('#lot-width-input').val()) || 0,
            lot_height:             parseFloat($('#lot-height-input').val()) || 0,
            lot_length_class_id:    parseInt($('#lot-length-class-input').val()) || 3
        },
        dataType: 'json',
        success: function (json) {
            if (json.success) {
                var itemId  = json.ebay_item_id || '';
                var ebayUrl = json.ebay_url || ('https://www.ebay.com/itm/' + itemId);

                // Update status banner
                $('#lot-status-banner').html(
                    '<div class="alert alert-success d-flex align-items-center justify-content-between mb-3">' +
                    '<span><i class="fa-solid fa-check-circle me-2"></i><strong>' + TEXT_LOT_PUBLISHED_LIVE + '</strong> — ' +
                    '<a href="' + ebayUrl + '" target="_blank" class="alert-link">' + itemId + '</a></span>' +
                    '<button type="button" id="btn-end-lot" class="btn btn-danger btn-sm ms-3">' +
                    '<i class="fa-solid fa-xmark me-1"></i>' + BUTTON_END_LOT + '</button></div>'
                );

                // Hide publish button
                $btn.closest('div.mt-2').hide();
                alert(json.message || TEXT_LOT_PUBLISHED_OK + ': ' + itemId);
            } else {
                alert(json.error || 'Error');
                $btn.prop('disabled', false);
            }
        },
        error: function () {
            alert('AJAX error');
            $btn.prop('disabled', false);
        },
        complete: function () {
            $spinner.addClass('d-none');
        }
    });
}

/**
 * Termine l'annonce lot eBay via AJAX POST.
 */
function endLot() {
    var $btn = $('#btn-end-lot');
    $btn.prop('disabled', true).text('…');

    $.ajax({
        url: URL_END_LOT,
        type: 'POST',
        data: { listing_id: LISTING_ID },
        dataType: 'json',
        success: function (json) {
            if (json.success) {
                // Reset status banner
                $('#lot-status-banner').html(
                    '<div class="alert alert-secondary mb-3">' +
                    '<i class="fa-solid fa-circle-info me-2"></i>' + TEXT_LOT_ENDED_OK + '</div>'
                );

                // Show publish button again
                if ($('#btn-publish-lot').length) {
                    $('#btn-publish-lot').closest('div.mt-2').show();
                    $('#btn-publish-lot').prop('disabled', false);
                } else {
                    // Re-inject publish button
                    $('.card-body', '#lot-ebay-panel').append(
                        '<div class="mt-2">' +
                        '<button type="button" id="btn-publish-lot" class="btn btn-success">' +
                        '<i class="fa-brands fa-ebay me-2"></i>' + BUTTON_PUBLISH_LOT + '</button>' +
                        '<span id="lot-publish-spinner" class="ms-2 d-none">' +
                        '<span class="spinner-border spinner-border-sm text-success"></span> ' + TEXT_LOT_PUBLISHING + '</span>' +
                        '</div>'
                    );
                    // Re-bind publish click
                    $('#btn-publish-lot').on('click', function () {
                        if (!confirm(TEXT_LOT_CONFIRM_PUBLISH)) return;
                        publishLot();
                    });
                }

                alert(json.message || TEXT_LOT_ENDED_OK);
            } else {
                alert(json.error || 'Error');
                $btn.prop('disabled', false).text(BUTTON_END_LOT);
            }
        },
        error: function () {
            alert('AJAX error');
            $btn.prop('disabled', false).text(BUTTON_END_LOT);
        }
    });
}
