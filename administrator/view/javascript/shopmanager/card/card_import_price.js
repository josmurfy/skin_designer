/**
 * card_import_price.js
 * Autonomous JS — no shared dependencies (intentional duplication)
 * Handles: upload, save-to-db, delete selected, truncate, image hover popup, preview row delete
 * List reload / filter / autocomplete are handled inline in the twig (OC4 pattern)
 */

/* ===== Utility (duplicated for autonomy) ===== */
function htmlspecialchars(str) {
    if (typeof str !== 'string') return str === null || str === undefined ? '' : String(str);
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;');
}

/* ===== Modal helpers ===== */
function showAlert(title, message) {
    document.getElementById('alert-modal-title').textContent = title;
    document.getElementById('alert-modal-body').innerHTML = message;
    new bootstrap.Modal(document.getElementById('alertModal')).show();
}

function showConfirm(title, message, onConfirm) {
    document.getElementById('confirm-modal-title').textContent = title;
    document.getElementById('confirm-modal-body').textContent = message;
    var okBtn = document.getElementById('confirm-modal-ok');
    var newOk = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOk, okBtn);
    var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    newOk.addEventListener('click', function () { modal.hide(); onConfirm(); });
    modal.show();
}

function formatMarketValue(value) {
    if (value === null || value === undefined || value === '') {
        return '—';
    }

    var parsed = parseFloat(value);
    if (isNaN(parsed)) {
        return htmlspecialchars(String(value));
    }

    return parsed.toFixed(2);
}

function marketCellHtml(value, url) {
    var formatted = formatMarketValue(value);
    if (formatted === '—') {
        return formatted;
    }

    if (url && String(url).trim() !== '') {
        return '<a href="' + htmlspecialchars(String(url)) + '" target="ebay_market_preview">' + formatted + '</a>';
    }

    return formatted;
}

function getBidLabel(count) {
    return parseInt(count, 10) === 1
        ? (typeof TEXT_BID_SINGULAR !== 'undefined' && TEXT_BID_SINGULAR ? TEXT_BID_SINGULAR : 'bid')
        : (typeof TEXT_BID_PLURAL !== 'undefined' && TEXT_BID_PLURAL ? TEXT_BID_PLURAL : 'bids');
}

function getMarketDisplayColor(priceValue, comparePrice) {
    var p = parseFloat(priceValue);
    var c = parseFloat(comparePrice || 0);

    if (isNaN(p)) {
        return '#6c757d';
    }

    if (!isNaN(c) && c > 0) {
        return p <= c ? '#dc3545' : '#28a745';
    }

    return '#28a745';
}

function buildMarketDisplayHtml(price, url, bidCount, grade, comparePrice) {
    var p;
    var color;
    var content;

    if (price === null || price === undefined || price === '') {
        return '<span class="text-muted">—</span>';
    }

    p = parseFloat(price);
    if (isNaN(p)) {
        return '<span class="text-muted">—</span>';
    }

    color = getMarketDisplayColor(p, comparePrice);
    content = '<span style="font-weight:700;color:#fff;background:' + color + ';padding:2px 6px;border-radius:4px;display:inline-block;">$' + p.toFixed(2) + '</span>';

    if (url) {
        content = '<a href="' + htmlspecialchars(String(url)) + '" target="ebay_market_preview" style="text-decoration:none;">' + content + '</a>';
    }

    if (grade) {
        content += '<div style="font-size:11px;color:#555;line-height:1.1;">' + htmlspecialchars(String(grade)) + '</div>';
    }

    if (bidCount !== undefined && bidCount !== null && bidCount !== '' && parseInt(bidCount, 10) >= 0) {
        content += '<div style="font-size:11px;color:#666;line-height:1.1;">' + parseInt(bidCount, 10) + ' ' + htmlspecialchars(getBidLabel(bidCount)) + '</div>';
    }

    return content;
}

function getDbListReferencePrice($row) {
    var rawText = String($row.find('td').eq(9).text() || '').replace(/[^0-9.\-]/g, '').trim();
    return rawText === '' ? 0 : (parseFloat(rawText) || 0);
}

function getPreviewGradingConfig() {
    return {
        ebayFeeRate: 0.13,
        psaCertificationPrice: 55
    };
}

function getPreviewBestGradedMarketPrice(auctionGraded, listGraded) {
    var prices = [];

    if (
        auctionGraded &&
        auctionGraded.price !== null &&
        auctionGraded.price !== undefined &&
        auctionGraded.price !== '' &&
        parseFloat(auctionGraded.price) > 0 &&
        parseInt(auctionGraded.bids || 0, 10) > 0
    ) {
        prices.push(parseFloat(auctionGraded.price));
    }

    if (
        listGraded &&
        listGraded.price !== null &&
        listGraded.price !== undefined &&
        listGraded.price !== '' &&
        parseFloat(listGraded.price) > 0
    ) {
        prices.push(parseFloat(listGraded.price));
    }

    if (!prices.length) {
        return null;
    }

    return Math.min.apply(null, prices);
}

function getPreviewBestRawMarketPrice(auctionRaw, listRaw) {
    var prices = [];

    if (
        auctionRaw &&
        auctionRaw.price !== null &&
        auctionRaw.price !== undefined &&
        auctionRaw.price !== '' &&
        parseFloat(auctionRaw.price) > 0
    ) {
        prices.push(parseFloat(auctionRaw.price));
    }

    if (
        listRaw &&
        listRaw.price !== null &&
        listRaw.price !== undefined &&
        listRaw.price !== '' &&
        parseFloat(listRaw.price) > 0
    ) {
        prices.push(parseFloat(listRaw.price));
    }

    if (!prices.length) {
        return null;
    }

    return Math.min.apply(null, prices);
}

function getPreviewUngradedValue($row) {
    var rowIndex = parseInt($row.attr('data-index') || '', 10);
    var currentCard = null;
    var $ungradedCell = $row.find('td').eq(4); // col 4 = merged Prices cell
    var rawText = '';

    if (!isNaN(rowIndex) && Array.isArray(currentCards)) {
        currentCard = currentCards.find(function (card) {
            return parseInt(card._index, 10) === rowIndex;
        }) || null;
    }

    if (currentCard && currentCard.ungraded !== null && currentCard.ungraded !== undefined && currentCard.ungraded !== '') {
        return parseFloat(currentCard.ungraded) || 0;
    }

    rawText = $ungradedCell.clone().children().remove().end().text();
    rawText = String(rawText || '').replace(/[^0-9.\-]/g, '').trim();

    if (rawText === '') {
        return 0;
    }

    return parseFloat(rawText) || 0;
}

function evaluatePreviewGradingPotential($row, auctionRaw, listRaw, auctionGraded, listGraded) {
    var bestGradedPrice = getPreviewBestGradedMarketPrice(auctionGraded, listGraded);
    var config = getPreviewGradingConfig();
    var currentPrice = getPreviewUngradedValue($row);
    var bestRawPrice = getPreviewBestRawMarketPrice(auctionRaw, listRaw);
    var netAfterFeesAndPsa;
    var profit;

    if (bestGradedPrice === null || isNaN(bestGradedPrice) || bestGradedPrice <= 0) {
        return null;
    }

    if (bestRawPrice !== null && !isNaN(bestRawPrice) && bestRawPrice > 0) {
        if (isNaN(currentPrice) || currentPrice <= 0 || bestRawPrice < currentPrice) {
            currentPrice = bestRawPrice;
        }
    }

    netAfterFeesAndPsa = bestGradedPrice - (bestGradedPrice * config.ebayFeeRate) - config.psaCertificationPrice;
    profit = netAfterFeesAndPsa - currentPrice;

    return {
        bestGradedPrice: bestGradedPrice,
        netAfterFeesAndPsa: netAfterFeesAndPsa,
        currentPrice: currentPrice,
        profit: profit,
        isProfitable: profit > 0
    };
}

function applyPreviewGradingPotentialStyle($row, potential) {
    var $ungradedCell = $row.find('td').eq(4); // col 4 = merged Prices cell
    var $profitNote = $ungradedCell.find('.preview-grading-profit-note');

    if (!$profitNote.length) {
        $profitNote = $('<div class="preview-grading-profit-note" style="font-size:11px;line-height:1.2;margin-top:4px;"></div>');
        $ungradedCell.append($profitNote);
    }

    if (!potential || !potential.isProfitable) {
        $ungradedCell.css('background-color', '');
        $ungradedCell.attr('title', '');
        $row.removeClass('preview-grading-profitable');
        $row.attr('data-grading-profitable', '0');
        $row.attr('data-grading-profit', '0');
        $profitNote.text('');
        return;
    }

    $ungradedCell.css('background-color', '#ffd700');
    $ungradedCell.attr('title', 'Grading potentiel: profit +' + potential.profit.toFixed(2) + ' CAD');
    $row.addClass('preview-grading-profitable');
    $row.attr('data-grading-profitable', '1');
    $row.attr('data-grading-profit', potential.profit.toFixed(2));
    $profitNote.html('<span style="font-weight:600;color:#7a5c00;">Profit grading: $' + potential.profit.toFixed(2) + ' CAD</span>');
}

function applyPreviewGradingPotentialToRow($row, rowResult) {
    var auctionRaw = rowResult && rowResult.ebay_price_sold_raw ? {
        price: rowResult.ebay_price_sold_raw,
        url: rowResult.ebay_price_sold_raw_url || '',
        bids: rowResult.ebay_price_sold_raw_bids || 0
    } : null;
    var listRaw = rowResult && rowResult.ebay_price_list_raw ? {
        price: rowResult.ebay_price_list_raw,
        url: rowResult.ebay_price_list_raw_url || ''
    } : null;
    var auctionGraded = rowResult && rowResult.ebay_price_sold_graded ? {
        price: rowResult.ebay_price_sold_graded,
        url: rowResult.ebay_price_sold_graded_url || '',
        bids: rowResult.ebay_price_sold_graded_bids || 0
    } : null;
    var listGraded = rowResult && rowResult.ebay_price_list_graded ? {
        price: rowResult.ebay_price_list_graded,
        url: rowResult.ebay_price_list_graded_url || ''
    } : null;
    var potential = evaluatePreviewGradingPotential($row, auctionRaw, listRaw, auctionGraded, listGraded);

    applyPreviewGradingPotentialStyle($row, potential);
}

function renumberPreviewRows() {
    $('#preview-table tbody tr').each(function (i) {
        $(this).find('td').eq(1).text(i + 1);
    });
}

function getPreviewRowCardNumber($row) {
    var rowIndex = parseInt($row.attr('data-index') || '', 10);
    var currentCard = null;

    if (!isNaN(rowIndex) && Array.isArray(currentCards)) {
        currentCard = currentCards.find(function (card) {
            return parseInt(card._index, 10) === rowIndex;
        }) || null;
    }

    if (currentCard && currentCard.card_number !== null && currentCard.card_number !== undefined) {
        return String(currentCard.card_number).replace(/^#\s*/, '').trim();
    }

    return '';
}

function comparePreviewCardNumbers(a, b) {
    var aNum = getPreviewRowCardNumber($(a));
    var bNum = getPreviewRowCardNumber($(b));

    return aNum.localeCompare(bNum, undefined, { numeric: true, sensitivity: 'base' });
}

function sortPreviewGradingPotentials() {
    var $tbody = $('#preview-table tbody');
    var rows = $tbody.find('tr').get();
    var profitableRows = [];
    var otherRows = [];

    if (!rows.length) {
        return 0;
    }

    $.each(rows, function (index, row) {
        var $row = $(row);

        if (!$row.attr('data-original-order')) {
            $row.attr('data-original-order', String(index));
        }

        if ($row.hasClass('preview-grading-profitable') || $row.attr('data-grading-profitable') === '1') {
            profitableRows.push(row);
        } else {
            otherRows.push(row);
        }
    });

    profitableRows.sort(function (a, b) {
        return comparePreviewCardNumbers(a, b);
    });

    otherRows.sort(function (a, b) {
        return parseInt($(a).attr('data-original-order') || '0', 10) - parseInt($(b).attr('data-original-order') || '0', 10);
    });

    fetchDebugLog('[card_import_fetch] sorting preview profitable rows:', profitableRows.length, 'others:', otherRows.length);

    $.each(profitableRows.concat(otherRows), function (_, row) {
        $tbody.append(row);
    });

    renumberPreviewRows();
    return profitableRows.length;
}

function applyMarketRowResult($row, rowResult) {
    if (!$row || !$row.length || !rowResult || !rowResult.success) {
        return;
    }

    var comparePrice = getDbListReferencePrice($row);

    $row.find('.market-sold-raw').html(buildMarketDisplayHtml(rowResult.ebay_price_sold_raw, rowResult.ebay_price_sold_raw_url, rowResult.ebay_price_sold_raw_bids, '', comparePrice));
    $row.find('.market-sold-graded').html(buildMarketDisplayHtml(rowResult.ebay_price_sold_graded, rowResult.ebay_price_sold_graded_url, rowResult.ebay_price_sold_graded_bids, rowResult.ebay_price_sold_graded_grade, comparePrice));
    $row.find('.market-list-raw').html(buildMarketDisplayHtml(rowResult.ebay_price_list_raw, rowResult.ebay_price_list_raw_url, null, '', comparePrice));
    $row.find('.market-list-graded').html(buildMarketDisplayHtml(rowResult.ebay_price_list_graded, rowResult.ebay_price_list_graded_url, null, rowResult.ebay_price_list_graded_grade, comparePrice));
    $row.find('.market-checked-at').text(rowResult.ebay_market_checked_at || '—');

    var soldBtn = $row.find('.btn-market-sold');

    var soldUrl = rowResult.ebay_price_sold_graded_url || '';
    if (!soldUrl && rowResult.manual_urls && rowResult.manual_urls.sold_graded) {
        soldUrl = rowResult.manual_urls.sold_graded;
    }
    if (soldBtn.length && soldUrl) {
        soldBtn.attr('href', soldUrl);
    }
}

function fetchMarketPricesByIds(cardRawIds, forceFetch) {
    if (!Array.isArray(cardRawIds) || !cardRawIds.length) {
        showAlert(TEXT_ERROR, TEXT_NO_DATA || 'No records selected.');
        return;
    }

    var fd = new FormData();
    cardRawIds.forEach(function (id) {
        fd.append('card_raw_ids[]', id);
    });
    if (forceFetch) {
        fd.append('force', '1');
    }

    fetch(URL_FETCH_MARKET_PRICES, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (json) {
            if (json.error) {
                showAlert(TEXT_ERROR, htmlspecialchars(json.error));
                return;
            }

            if (!json.results) {
                showAlert(TEXT_ERROR, TEXT_AJAX_ERROR || 'Invalid response');
                return;
            }

            Object.keys(json.results).forEach(function (key) {
                var rowResult = json.results[key];
                var $row = $('#price-list tr[data-card-raw-id="' + key + '"]');

                if (rowResult && rowResult.success) {
                    applyMarketRowResult($row, rowResult);
                }
            });

            var summary = [];
            summary.push((TEXT_MARKET_FETCH_DONE || 'Fetch done:') + ' ' + (json.processed || 0));
            summary.push((TEXT_MARKET_CACHED || 'cached') + ': ' + (json.cached || 0));
            if ((json.errors || 0) > 0) {
                summary.push('errors: ' + json.errors);
            }
            if (json.rate_limited) {
                summary.push(TEXT_MARKET_RATE_LIMIT || 'Rate limit reached');
            }

            showAlert(TEXT_SUCCESS || 'Success', htmlspecialchars(summary.join(' | ')));
        })
        .catch(function (err) {
            showAlert(TEXT_ERROR, (TEXT_AJAX_ERROR || 'AJAX error') + ' ' + err.message);
        });
}

function updateCurrentCardMarketData(rowIndex, rowResult) {
    if (!currentCards || !Array.isArray(currentCards)) {
        return;
    }

    var idx = parseInt(rowIndex, 10);
    if (isNaN(idx)) {
        return;
    }

    var pos = currentCards.findIndex(function (card) {
        return parseInt(card._index, 10) === idx;
    });

    if (pos === -1) {
        return;
    }

    var fields = [
        'ebay_price_sold_raw',
        'ebay_price_sold_raw_url',
        'ebay_price_sold_raw_bids',
        'ebay_price_sold_graded',
        'ebay_price_sold_graded_url',
        'ebay_price_sold_graded_bids',
        'ebay_price_sold_graded_grade',
        'ebay_price_list_raw',
        'ebay_price_list_raw_url',
        'ebay_price_list_graded',
        'ebay_price_list_graded_url',
        'ebay_price_list_graded_grade',
        'ebay_market_checked_at'
    ];

    fields.forEach(function (field) {
        currentCards[pos][field] = rowResult[field] !== undefined ? rowResult[field] : null;
    });
}

function getPreviewRowLabel(card, fallbackIndex) {
    if (!card) {
        return 'Row ' + fallbackIndex;
    }

    var parts = [];
    if (card.title) {
        parts.push(String(card.title));
    } else {
        if (card.set_name || card.set) {
            parts.push(String(card.set_name || card.set));
        }
        if (card.player) {
            parts.push(String(card.player));
        }
        if (card.card_number) {
            parts.push('#' + String(card.card_number));
        }
    }

    return parts.length ? parts.join(' ') : ('Row ' + fallbackIndex);
}

var DEBUG_CARD_IMPORT_FETCH = false;
var previewFetchState = {
    active: false,
    paused: false,
    pauseRequested: false,
    previewRows: [],
    nextIndex: 0,
    total: 0,
    done: 0,
    processed: 0,
    errors: 0,
    stoppedByRateLimit: false,
    buttonSelector: '#button-fetch-preview-market-prices'
};

function fetchDebugLog() {
    if (!DEBUG_CARD_IMPORT_FETCH || typeof console === 'undefined' || !console.log) {
        return;
    }

    console.log.apply(console, arguments);
}

function fetchDebugWarn() {
    if (!DEBUG_CARD_IMPORT_FETCH || typeof console === 'undefined' || !console.warn) {
        return;
    }

    console.warn.apply(console, arguments);
}

function fetchDebugError() {
    if (!DEBUG_CARD_IMPORT_FETCH || typeof console === 'undefined' || !console.error) {
        return;
    }

    console.error.apply(console, arguments);
}

function appendFetchProgressLog(type, text) {
    var cls = 'secondary';
    if (type === 'success') cls = 'success';
    if (type === 'error') cls = 'danger';
    if (type === 'warning') cls = 'warning';
    if (type === 'info') cls = 'info';

    var html = '<div class="border-bottom py-2"><span class="badge bg-' + cls + ' me-2">' + htmlspecialchars(type.toUpperCase()) + '</span>' + htmlspecialchars(text) + '</div>';
    var $log = $('#fetch-progress-log');
    $log.append(html);
    $log.scrollTop($log[0].scrollHeight);
}

function setFetchProgressSummary(text, type) {
    var cls = 'alert-info';
    if (type === 'success') cls = 'alert-success';
    if (type === 'error') cls = 'alert-danger';
    if (type === 'warning') cls = 'alert-warning';

    $('#fetch-progress-summary').removeClass('alert-info alert-success alert-danger alert-warning').addClass(cls).text(text);
}

function getFetchButton() {
    return $(previewFetchState.buttonSelector);
}

function resetPreviewFetchState() {
    previewFetchState.active = false;
    previewFetchState.paused = false;
    previewFetchState.pauseRequested = false;
    previewFetchState.previewRows = [];
    previewFetchState.nextIndex = 0;
    previewFetchState.total = 0;
    previewFetchState.done = 0;
    previewFetchState.processed = 0;
    previewFetchState.errors = 0;
    previewFetchState.stoppedByRateLimit = false;
}

function updateFetchButtonUi() {
    var $button = getFetchButton();
    if (!$button.length) return;

    if (previewFetchState.active) {
        $button.prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin me-1"></i>' + htmlspecialchars(typeof TEXT_BUTTON_FETCH_EBAY !== 'undefined' && TEXT_BUTTON_FETCH_EBAY ? TEXT_BUTTON_FETCH_EBAY : 'Fetch eBay Prices'));
        return;
    }

    if (previewFetchState.paused) {
        $button.prop('disabled', false).html('<i class="fa-solid fa-play me-1"></i>Resume eBay');
        return;
    }

    $button.prop('disabled', false).html('<i class="fa-solid fa-tags me-1"></i>' + htmlspecialchars(typeof TEXT_BUTTON_FETCH_EBAY !== 'undefined' && TEXT_BUTTON_FETCH_EBAY ? TEXT_BUTTON_FETCH_EBAY : 'Fetch eBay Prices'));
}

function updateFetchStopButtonUi() {
    var $stopBtn = $('#button-stop-fetch-ebay');
    if (!$stopBtn.length) return;

    if (previewFetchState.active && !previewFetchState.pauseRequested) {
        $stopBtn.prop('disabled', false).removeClass('btn-outline-secondary').addClass('btn-outline-danger').text('Arrêter');
    } else if (previewFetchState.pauseRequested) {
        $stopBtn.prop('disabled', true).removeClass('btn-outline-danger').addClass('btn-outline-secondary').text('Arrêt demandé...');
    } else {
        $stopBtn.prop('disabled', true).removeClass('btn-outline-danger').addClass('btn-outline-secondary').text('Arrêter');
    }
}

function finishPreviewFetch(mode) {
    previewFetchState.active = false;
    previewFetchState.pauseRequested = false;
    previewFetchState.paused = (mode === 'paused');

    var profitableCount = sortPreviewGradingPotentials();
    updateFetchButtonUi();
    updateFetchStopButtonUi();

    if (mode === 'paused') {
        setFetchProgressSummary('Pause demandée. Progression: ' + previewFetchState.done + '/' + previewFetchState.total + ', erreurs: ' + previewFetchState.errors + '. Recliquer Fetch pour continuer.', 'warning');
        appendFetchProgressLog('warning', 'Pause effectuée. Recliquer Fetch eBay pour reprendre où le traitement était rendu.');
        return;
    }

    var summaryText;
    var summaryType;
    if (previewFetchState.stoppedByRateLimit) {
        summaryText = 'Stopped: eBay API rate limit reached. Success: ' + previewFetchState.done + '/' + previewFetchState.total + ', errors: ' + previewFetchState.errors;
        summaryType = 'warning';
    } else if (previewFetchState.errors > 0) {
        summaryText = 'Completed. Success: ' + previewFetchState.done + '/' + previewFetchState.total + ', errors: ' + previewFetchState.errors;
        summaryType = 'warning';
    } else {
        summaryText = 'Completed successfully: ' + previewFetchState.done + '/' + previewFetchState.total;
        summaryType = 'success';
    }

    if (profitableCount > 0) {
        summaryText += ' — ' + profitableCount + ' carte(s) à potentiel grading remontée(s) en tête.';
        appendFetchProgressLog('success', profitableCount + ' carte(s) rentables remontées en tête du tableau.');
    }

    setFetchProgressSummary(summaryText, summaryType);

    var snapshotDone = previewFetchState.done;
    var snapshotTotal = previewFetchState.total;
    var snapshotErrors = previewFetchState.errors;
    var snapshotSummaryType = summaryType;

    resetPreviewFetchState();
    updateFetchButtonUi();
    updateFetchStopButtonUi();

    // Auto-fermeture du modal après completion (3s pour lire le message)
    window.setTimeout(function () {
        var modalEl = document.getElementById('fetchProgressModal');
        if (modalEl) {
            var bsModal = bootstrap.Modal.getInstance(modalEl);
            if (bsModal) {
                bsModal.hide();
            }
        }

        // Notification persistante après fermeture du modal
        var notifCls = snapshotSummaryType === 'success' ? 'alert-success' : 'alert-warning';
        var notifText = 'eBay Fetch: ' + snapshotDone + '/' + snapshotTotal + ' OK';
        if (snapshotErrors > 0) {
            notifText += ', ' + snapshotErrors + ' erreur(s)';
        }
        var $notif = $('<div class="alert ' + notifCls + ' alert-dismissible fade show py-2 px-3 mb-2" role="alert" style="font-size:13px;">'
            + '<i class="fa-solid fa-circle-check me-1"></i>' + notifText
            + '<button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button></div>');
        $('#preview-content').prepend($notif);
        setTimeout(function () { $notif.alert('close'); }, 5000);
    }, 3000);
}

function runPreviewMarketFetch(previewRows, $button, startIndex, resumeMode) {
    var total = previewRows.length;
    var modal = new bootstrap.Modal(document.getElementById('fetchProgressModal'));
    var $modalEl = $('#fetchProgressModal');

    previewFetchState.buttonSelector = '#' + ($button.attr('id') || 'button-fetch-ebay');
    previewFetchState.previewRows = previewRows;
    previewFetchState.total = total;
    previewFetchState.nextIndex = startIndex || 0;
    previewFetchState.active = true;
    previewFetchState.paused = false;
    previewFetchState.pauseRequested = false;
    updateFetchButtonUi();
    updateFetchStopButtonUi();

    $modalEl.off('hide.bs.modal.cardimport').on('hide.bs.modal.cardimport', function () {
        // Pause the fetch loop when the modal is closed (X button or Close button)
        if (previewFetchState.active && !previewFetchState.pauseRequested) {
            previewFetchState.pauseRequested = true;
            updateFetchStopButtonUi();
        }

        var active = document.activeElement;
        if (active && this.contains(active) && typeof active.blur === 'function') {
            active.blur();
        }

        if ($button && $button.length) {
            setTimeout(function () {
                $button.trigger('focus');
            }, 0);
        }
    });

    if (!resumeMode) {
        $('#fetch-progress-log').html('');
        previewFetchState.done = 0;
        previewFetchState.processed = 0;
        previewFetchState.errors = 0;
        previewFetchState.stoppedByRateLimit = false;
        setFetchProgressSummary('Starting eBay fetch: 0/' + total, 'info');
    } else {
        setFetchProgressSummary('Resuming eBay fetch: ' + previewFetchState.done + '/' + total, 'info');
        appendFetchProgressLog('info', 'Resume requested. Continuing from item ' + (previewFetchState.nextIndex + 1) + '.');
    }

    modal.show();

    function processNext(index) {
        previewFetchState.nextIndex = index;

        if (previewFetchState.stoppedByRateLimit) {
            finishPreviewFetch('rate_limited');
            return;
        }

        if (previewFetchState.pauseRequested) {
            finishPreviewFetch('paused');
            return;
        }

        if (index >= total) {
            finishPreviewFetch('completed');
            return;
        }

        var card = previewRows[index];
        var rowIndex = parseInt(card._index, 10);
        var rowLabel = getPreviewRowLabel(card, index + 1);
        var $row = $('#preview-table tr[data-index="' + rowIndex + '"]');

        // Spinner sur la ligne en cours
        $row.find('.market-sold-raw').html('<i class="fa-solid fa-circle-notch fa-spin text-muted"></i>');

        setFetchProgressSummary('<i class="fa-solid fa-circle-notch fa-spin me-1"></i> Fetching ' + (index + 1) + '/' + total + ': ' + rowLabel + '…', 'info');
        appendFetchProgressLog('info', 'Fetching: ' + rowLabel);
        if (DEBUG_CARD_IMPORT_FETCH) {
            fetchDebugLog('[card_import_fetch] #' + (index + 1) + ' sending card_index=' + rowIndex + ' to ' + URL_FETCH_PREVIEW_MARKET_PRICES, JSON.parse(JSON.stringify(card)));
        }

        $.ajax({
            url: URL_FETCH_PREVIEW_MARKET_PRICES,
            type: 'POST',
            contentType: 'application/json',
            dataType: 'json',
            timeout: 45000,
            data: JSON.stringify({ cards: [card] }),
            success: function (json) {
                previewFetchState.processed++;
                if (DEBUG_CARD_IMPORT_FETCH) {
                    fetchDebugLog('[card_import_fetch] #' + (index + 1) + ' response json:', JSON.parse(JSON.stringify(json)));
                }

                if (json.error) {
                    previewFetchState.errors++;
                    appendFetchProgressLog('error', rowLabel + ' → ' + json.error);
                    fetchDebugWarn('[card_import_fetch] #' + (index + 1) + ' json.error:', json.error);
                    previewFetchState.nextIndex = index + 1;
                    if (previewFetchState.pauseRequested) {
                        finishPreviewFetch('paused');
                    } else {
                        processNext(index + 1);
                    }
                    return;
                }

                var rowResult = json.results && json.results[rowIndex] ? json.results[rowIndex] : null;
                if (DEBUG_CARD_IMPORT_FETCH) {
                    fetchDebugLog('[card_import_fetch] #' + (index + 1) + ' rowResult (idx=' + rowIndex + '):', rowResult);
                }

                if (!rowResult) {
                    previewFetchState.errors++;
                    appendFetchProgressLog('error', rowLabel + ' → no result returned');
                    previewFetchState.nextIndex = index + 1;
                    if (previewFetchState.pauseRequested) {
                        finishPreviewFetch('paused');
                    } else {
                        processNext(index + 1);
                    }
                    return;
                }

                if (rowResult.rate_limited) {
                    previewFetchState.stoppedByRateLimit = true;
                    previewFetchState.errors++;
                    appendFetchProgressLog('warning', rowLabel + ' → ' + (rowResult.error || 'eBay API rate limit reached'));
                    if (rowResult.manual_urls) { renderManualButtonsToPreviewCells($row, rowResult.manual_urls); }
                    finishPreviewFetch('rate_limited');
                    return;
                }

                if (rowResult.success) {
                    previewFetchState.done++;
                    applyMarketPreviewRowResult($row, rowResult);
                    updateCurrentCardMarketData(rowIndex, rowResult);
                    appendFetchProgressLog('success', rowLabel + ' → updated');
                } else {
                    previewFetchState.errors++;
                    appendFetchProgressLog('error', rowLabel + ' → ' + (rowResult.error || 'unknown error'));
                    if (rowResult.manual_urls) { renderManualButtonsToPreviewCells($row, rowResult.manual_urls); }
                }

                previewFetchState.nextIndex = index + 1;

                if (previewFetchState.pauseRequested) {
                    finishPreviewFetch('paused');
                    return;
                }

                window.setTimeout(function () {
                    processNext(index + 1);
                }, 250);
            },
            error: function (xhr) {
                previewFetchState.processed++;
                previewFetchState.errors++;
                var isTimeout = (xhr.statusText === 'timeout');
                var raw = isTimeout
                    ? 'Timeout (45s) — eBay API trop lent, carte ignorée'
                    : (xhr.responseText || xhr.statusText || 'AJAX error').substring(0, 250);
                if (DEBUG_CARD_IMPORT_FETCH) {
                    fetchDebugError('[card_import_fetch] #' + (index + 1) + ' AJAX error status=' + xhr.status + ':', raw);
                }
                appendFetchProgressLog(isTimeout ? 'warning' : 'error', rowLabel + ' → ' + raw);
                previewFetchState.nextIndex = index + 1;

                if (previewFetchState.pauseRequested) {
                    finishPreviewFetch('paused');
                    return;
                }

                window.setTimeout(function () {
                    processNext(index + 1);
                }, 250);
            }
        });
    }

    processNext(startIndex || 0);
}

function fetchMarketPricesPreview(previewRows, forceFetch) {
    if (!Array.isArray(previewRows) || !previewRows.length) {
        showAlert(TEXT_ERROR, TEXT_NO_DATA || 'No records in preview.');
        return Promise.resolve();
    }

    if (typeof URL_FETCH_PREVIEW_MARKET_PRICES === 'undefined' || !URL_FETCH_PREVIEW_MARKET_PRICES) {
        showAlert(TEXT_ERROR, 'Preview fetch URL is missing.');
        return Promise.resolve();
    }

    return Promise.resolve();
}

function renderManualButtonsToPreviewCells($row, manualUrls) {
    var urls = manualUrls || {};
    var target = 'ebay_market_preview';
    var s = 'font-size:10px;padding:2px 6px;line-height:1.15;';

    function btnHtml(url, label, cls) {
        if (!url) return '—';
        return '<a class="btn btn-sm ' + cls + ' w-100" style="' + s + '" target="' + target + '" href="' + htmlspecialchars(url) + '">' + htmlspecialchars(label) + '</a>';
    }

    // Target sub-spans by class inside the merged Market eBay cell
    $row.find('.market-sold-raw').html(btnHtml(urls.auction_raw || '', typeof TEXT_MARKET_AUCTION_RAW !== 'undefined' ? TEXT_MARKET_AUCTION_RAW : 'Auction Raw', 'btn-primary'));
    $row.find('.market-sold-graded').html(btnHtml(urls.auction_graded || '', typeof TEXT_MARKET_AUCTION_GRADED !== 'undefined' ? TEXT_MARKET_AUCTION_GRADED : 'Auction Graded', 'btn-dark'));
    $row.find('.market-list-raw').html(btnHtml(urls.buy_now_raw || '', typeof TEXT_MARKET_BUY_NOW_RAW !== 'undefined' ? TEXT_MARKET_BUY_NOW_RAW : 'Buy Now Raw', 'btn-primary'));
    $row.find('.market-list-graded').html(btnHtml(urls.buy_now_graded || '', typeof TEXT_MARKET_BUY_NOW_GRADED !== 'undefined' ? TEXT_MARKET_BUY_NOW_GRADED : 'Buy Now Graded', 'btn-dark'));
}

function applyMarketPreviewRowResult($row, rowResult) {
    if (!$row || !$row.length) return;

    var manualUrls = rowResult.manual_urls || null;
    var comparePrice = getPreviewUngradedValue($row);
    var cols = ['ebay_price_sold_raw', 'ebay_price_sold_graded', 'ebay_price_list_raw', 'ebay_price_list_graded', 'ebay_market_checked_at'];
    var urlMapKey = {
        'ebay_price_sold_raw':    'auction_raw',
        'ebay_price_sold_graded': 'auction_graded',
        'ebay_price_list_raw':    'buy_now_raw',
        'ebay_price_list_graded': 'buy_now_graded'
    };
    var btnLabels = {
        'auction_raw':    (typeof TEXT_MARKET_AUCTION_RAW !== 'undefined' ? TEXT_MARKET_AUCTION_RAW : 'Auction Raw'),
        'auction_graded': (typeof TEXT_MARKET_AUCTION_GRADED !== 'undefined' ? TEXT_MARKET_AUCTION_GRADED : 'Auction Graded'),
        'buy_now_raw':    (typeof TEXT_MARKET_BUY_NOW_RAW !== 'undefined' ? TEXT_MARKET_BUY_NOW_RAW : 'Buy Now Raw'),
        'buy_now_graded': (typeof TEXT_MARKET_BUY_NOW_GRADED !== 'undefined' ? TEXT_MARKET_BUY_NOW_GRADED : 'Buy Now Graded')
    };

    // Update market sub-spans by CSS class inside the merged Market eBay cell
    var cssClassMap = {
        'ebay_price_sold_raw':    'market-sold-raw',
        'ebay_price_sold_graded': 'market-sold-graded',
        'ebay_price_list_raw':    'market-list-raw',
        'ebay_price_list_graded': 'market-list-graded'
    };

    cols.forEach(function (col) {
        if (col === 'ebay_market_checked_at') {
            var checkedVal = rowResult[col] || '';
            $row.find('.market-checked-at').text(checkedVal ? ('✓ ' + checkedVal) : '');
        } else {
            var val = rowResult[col];
            var url = rowResult[col + '_url'];
            var cellContent = '';
            var bidCount = null;
            var grade = '';

            if (col === 'ebay_price_sold_raw') {
                bidCount = rowResult.ebay_price_sold_raw_bids;
            } else if (col === 'ebay_price_sold_graded') {
                bidCount = rowResult.ebay_price_sold_graded_bids;
                grade = rowResult.ebay_price_sold_graded_grade || '';
            } else if (col === 'ebay_price_list_graded') {
                grade = rowResult.ebay_price_list_graded_grade || '';
            }

            if (val) {
                cellContent = buildMarketDisplayHtml(val, url, bidCount, grade, comparePrice);
            } else {
                var mapKey = urlMapKey[col];
                var btnUrl = manualUrls && mapKey && manualUrls[mapKey] ? manualUrls[mapKey] : '';
                if (btnUrl) {
                    var cls = (mapKey.indexOf('graded') !== -1) ? 'btn-dark' : 'btn-primary';
                    cellContent = '<a class="btn btn-sm ' + cls + ' w-100" style="font-size:10px;padding:2px 6px;line-height:1.15;" target="ebay_market_preview" href="' + htmlspecialchars(btnUrl) + '">' + (btnLabels[mapKey] || mapKey) + '</a>';
                } else {
                    cellContent = '—';
                }
            }

            var cssClass = cssClassMap[col];
            if (cssClass) {
                $row.find('.' + cssClass).html(cellContent);
            }
        }
    });

    applyPreviewGradingPotentialToRow($row, rowResult);
}

/* ===== State ===== */
var currentCards = null;

/* ===== Image hover popup ===== */
document.addEventListener('mouseover', function (e) {
    var img = e.target.closest('.preview-thumb-img');
    if (!img) return;
    var src = img.getAttribute('data-fullsrc') || img.src;
    if (!src) return;
    var popup = document.createElement('div');
    popup.id = 'preview-img-popup';
    popup.style.cssText = 'position:fixed;z-index:99999;pointer-events:none;background:#fff;border:2px solid #6c757d;border-radius:6px;padding:4px;box-shadow:0 4px 20px rgba(0,0,0,.4);';
    var pi = document.createElement('img');
    pi.src = src;
    pi.style.cssText = 'max-width:280px;max-height:360px;display:block;';
    popup.appendChild(pi);
    document.body.appendChild(popup);
    var x = e.clientX + 16, y = e.clientY + 8;
    if (x + 300 > window.innerWidth) x = e.clientX - 300;
    popup.style.left = x + 'px'; popup.style.top = y + 'px';
});
document.addEventListener('mousemove', function (e) {
    var pop = document.getElementById('preview-img-popup');
    if (!pop) return;
    var x = e.clientX + 16, y = e.clientY + 8;
    if (x + 300 > window.innerWidth) x = e.clientX - 300;
    pop.style.left = x + 'px'; pop.style.top = y + 'px';
});
document.addEventListener('mouseout', function (e) {
    if (!e.target.closest('.preview-thumb-img')) return;
    var pop = document.getElementById('preview-img-popup');
    if (pop) pop.remove();
});

/* ===== DOMContentLoaded ===== */
$(document).ready(function () {

    /* ── File name display + drag-drop ── */
    var inputFile = document.getElementById('input-file');
    if (inputFile) inputFile.addEventListener('change', function () {
        var d = document.getElementById('file-name-display');
        if (d) d.textContent = this.files.length ? this.files[0].name : '';
    });
    var dropZone = document.getElementById('upload-zone');
    if (dropZone) {
        dropZone.addEventListener('dragover', function (e) { e.preventDefault(); this.classList.add('dragover'); });
        dropZone.addEventListener('dragleave', function () { this.classList.remove('dragover'); });
        dropZone.addEventListener('drop', function (e) {
            e.preventDefault(); this.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                inputFile.files = e.dataTransfer.files;
                var d = document.getElementById('file-name-display');
                if (d) d.textContent = e.dataTransfer.files[0].name;
            }
        });
    }

    /* ── DELETE row in preview ── */
    $(document).on('click', '.btn-preview-delete', function () {
        var row = $(this).closest('tr');
        var idx = parseInt(row.attr('data-index'), 10);
        if (currentCards) {
            var pos = currentCards.findIndex(function (c) { return c._index === idx; });
            if (pos !== -1) currentCards.splice(pos, 1);
        }
        row.remove();
        renumberPreviewRows();

        var $checks = $('#preview-table .preview-row-check');
        $('#preview-check-all').prop('checked', $checks.length > 0 && $checks.filter(':checked').length === $checks.length);
    });

    $(document).on('change', '#preview-check-all', function () {
        $('#preview-table .preview-row-check').prop('checked', $(this).is(':checked'));
    });

    $(document).on('change', '#preview-table .preview-row-check', function () {
        var $checks = $('#preview-table .preview-row-check');
        $('#preview-check-all').prop('checked', $checks.length > 0 && $checks.filter(':checked').length === $checks.length);
    });

    /* ── UPLOAD ── */
    $('#form-upload').on('submit', function (e) {
        e.preventDefault();
        var fi = document.getElementById('input-file');
        if (!fi || !fi.files.length) { showAlert(TEXT_ERROR, 'Please select a CSV file first.'); return; }
        var $spinner = $('#upload-spinner');
        var $btn     = $('#button-upload');
        $spinner.show();
        $btn.prop('disabled', true);
        var fd = new FormData();
        fd.append('file', fi.files[0]);

        fetch(URL_UPLOAD, { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                $spinner.hide();
                $btn.prop('disabled', false);
                if (json.error) { showAlert(TEXT_ERROR, htmlspecialchars(json.error)); return; }

                /* Always reset both sections first */
                $('#duplicate-section').hide();
                $('#preview-container').hide();
                $('#button-save-to-db').hide();
                $('#button-fetch-preview-market-prices').hide();

                /* ── DUPLICATE DETECTED — show DB records, block import ── */
                if (json.duplicate_detected) {
                    currentCards = null;
                    $('#duplicate-subtitle').text(
                        json.match_count + '/' + json.sample_total + ' lignes échantillons trouvées en DB'
                    );
                    $('#duplicate-msg').html(
                        '<strong>' + json.match_count + '</strong> ligne(s) sur <strong>' + json.sample_total + '</strong> '
                        + 'choisies aléatoirement dans le CSV existent déjà (même brand / year / category / set). '
                        + 'Ce fichier semble <strong>déjà importé</strong>. '
                        + 'Les <span class="fw-bold">' + json.db_count + '</span> enregistrement(s) actuels de la base sont affichés ci-dessous. '
                        + '<em>Utilisez Truncate pour vider la table avant de ré-importer.</em>'
                    );
                    $('#duplicate-records').html(
                        json.db_records_html || '<p class="p-3 text-muted">Aucun enregistrement trouvé.</p>'
                    );
                    $('#duplicate-section').show();
                    $('#duplicate-section')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });
                    return;
                }

                /* ── NORMAL IMPORT FLOW ── */
                currentCards = json.cards || null;

                /* preview */
                $('#preview-content').html(json.preview_html || '');
                markPreviewDuplicates();
                $('#preview-container').show();
                $('#preview-container')[0].scrollIntoView({ behavior: 'smooth', block: 'start' });

                /* save button */
                $('#button-save-to-db').show();
                $('#button-fetch-preview-market-prices').show();
                $('#button-merge-preview').show();

                /* count info */
                var sc = json.would_skip || 0, ic = json.would_insert || 0;
                var info = $('<p class="text-muted small mt-2 mb-0"></p>').html(
                    '<i class="fa-solid fa-circle-info me-1"></i>'
                    + '<strong>' + json.total_in_file + '</strong> rows &mdash; '
                    + '<span class="text-success fw-bold">' + ic + ' to insert</span>'
                    + (sc > 0 ? ', <span class="text-muted">' + sc + ' ignored (no price)</span>' : '')
                    + '. Click <strong>Save to Database</strong> to confirm.'
                );
                $('#preview-content').prepend(info);
            })
            .catch(function (err) {
                $spinner.hide();
                $btn.prop('disabled', false);
                showAlert(TEXT_ERROR, TEXT_AJAX_ERROR + ' ' + err.message);
            });
    });

    /* ── SAVE TO DATABASE ── */
    $('#button-save-to-db').on('click', function () {
        if (!currentCards || !currentCards.length) { showAlert(TEXT_ERROR, 'No preview loaded. Please upload a CSV first.'); return; }
        var $sp = $('#save-spinner');
        $sp.show();
        $(this).prop('disabled', true);
        var $btn = $(this);

        fetch(URL_SAVE, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ cards: currentCards }) })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                $sp.hide();
                $btn.prop('disabled', false);
                if (json.error) { showAlert(TEXT_ERROR, htmlspecialchars(json.error)); return; }

                $('#stat-total-file').text(json.total_in_file || 0);
                $('#stat-inserted').text(json.inserted      || 0);
                $('#stat-skipped').text(json.skipped        || 0);
                $('#stat-in-db').text(json.total_in_db      || 0);
                new bootstrap.Modal(document.getElementById('importResultsModal')).show();

                currentCards = null;
                $btn.hide();
                $('#button-fetch-preview-market-prices').hide();
                $('#button-merge-preview').hide();
                $('#preview-container').hide();
                $('#already-imported-alert').hide();

                $('#importResultsModal').one('hidden.bs.modal', function () {
                    $('#button-filter').trigger('click');
                });
            })
            .catch(function (err) {
                $sp.hide();
                $btn.prop('disabled', false);
                showAlert(TEXT_ERROR, TEXT_AJAX_ERROR + ' ' + err.message);
            });
    });

    /* ── MERGE PREVIEW ROWS (client-side) ── */
    var _mergeLock = false;

    function doMergeSelectedRows() {
        if (_mergeLock) return;
        if (!currentCards || !currentCards.length) return;

        var selectedIdxs = [];
        $('#preview-table tbody tr[data-index]').filter(function () {
            return $(this).find('.preview-row-check').is(':checked');
        }).each(function () {
            var idx = parseInt($(this).attr('data-index'), 10);
            if (!isNaN(idx)) selectedIdxs.push(idx);
        });

        if (selectedIdxs.length < 2) return; // silencieux — jamais via bouton inline (pré-sélectionné)

        // Collect cards matching selected rows
        var cards = selectedIdxs.map(function (idx) {
            return currentCards.find(function (c) { return parseInt(c._index, 10) === idx; });
        }).filter(Boolean);

        // Validate: all must share the same card_number
        var numbers = cards.map(function (c) { return String(c.card_number || '').trim().toLowerCase(); });
        var uniqueNums = numbers.filter(function (v, i, a) { return a.indexOf(v) === i; });
        if (uniqueNums.length > 1) {
            alert((typeof TEXT_MERGE_PREVIEW_DIFF_NUM !== 'undefined' ? TEXT_MERGE_PREVIEW_DIFF_NUM : 'Numéros de carte différents:')
                + ' ' + uniqueNums.join(', '));
            return;
        }

        var msg = (typeof TEXT_MERGE_PREVIEW_CONFIRM !== 'undefined' ? TEXT_MERGE_PREVIEW_CONFIRM
                    : 'Fusionner les lignes sélectionnées?')
                + '\n\n(' + cards.length + ' lignes)';
        if (!confirm(msg)) return;

        _mergeLock = true;

        // Keeper = longest player; tie = smallest _index
        cards.sort(function (a, b) {
            var la = String(a.player || '').length, lb = String(b.player || '').length;
            if (lb !== la) return lb - la;
            return parseInt(a._index, 10) - parseInt(b._index, 10);
        });
        var keeper = cards[0];

        // Merge: keeper takes MAX prices from all rows
        var priceFields = ['ungraded', 'grade_9', 'grade_10'];
        cards.forEach(function (c) {
            priceFields.forEach(function (f) {
                var v = parseFloat(c[f]) || 0;
                if (v > (parseFloat(keeper[f]) || 0)) keeper[f] = v;
            });
        });

        // Update keeper in currentCards
        var keeperPos = currentCards.findIndex(function (c) { return parseInt(c._index, 10) === parseInt(keeper._index, 10); });
        if (keeperPos !== -1) currentCards[keeperPos] = keeper;

        // Remove non-keeper rows from currentCards and DOM
        var toRemove = cards.slice(1);
        toRemove.forEach(function (c) {
            var pos = currentCards.findIndex(function (cc) { return parseInt(cc._index, 10) === parseInt(c._index, 10); });
            if (pos !== -1) currentCards.splice(pos, 1);
            $('#preview-table tr[data-index="' + c._index + '"]').remove();
        });

        // Update the keeper row display: badges in col 4
        var $keeperRow = $('#preview-table tr[data-index="' + keeper._index + '"]');
        $keeperRow.find('.badge.bg-primary').text(keeper.player || '');
        var $pricesCell = $keeperRow.find('td').eq(4);
        var pHtml = '';
        [['Raw', 'ungraded', 'bg-secondary'], ['G9', 'grade_9', 'bg-success'], ['G10', 'grade_10', 'bg-primary']].forEach(function (p) {
            var v = parseFloat(keeper[p[1]]) || 0;
            if (v > 0) {
                pHtml += '<div style="margin-bottom:2px;"><span class="badge ' + p[2] + '" style="font-size:10px;min-width:30px;">' + p[0] + '</span> <span style="font-size:12px;font-weight:600;">$' + v.toFixed(2) + '</span></div>';
            } else {
                pHtml += '<div style="margin-bottom:2px;opacity:0.35;"><span class="badge bg-light text-dark border" style="font-size:10px;min-width:30px;">' + p[0] + '</span> <span style="font-size:11px;">—</span></div>';
            }
        });
        $pricesCell.find('div[style*="margin-bottom"]').remove();
        $pricesCell.prepend(pHtml);

        renumberPreviewRows();
        $('#preview-table .preview-row-check').prop('checked', false);
        $('#preview-check-all').prop('checked', false);
        $('#button-merge-preview').hide();
        _mergeLock = false;
        markPreviewDuplicates();

        var done = typeof TEXT_MERGE_PREVIEW_DONE !== 'undefined' ? TEXT_MERGE_PREVIEW_DONE : 'Lignes fusionnées.';
        var $info = $('<div class="alert alert-success alert-dismissible fade show py-1 px-2 mt-1" role="alert" style="font-size:11px;">'
            + done + ' (' + toRemove.length + ' supprimée(s), keeper: #' + keeper._index + ')'
            + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>');
        $('#preview-content').prepend($info);
        setTimeout(function () { $info.alert('close'); }, 3000);
    }

    $('#button-merge-preview').on('click', function () {
        if (!$(this).is(':visible')) return; // guard double-clic
        doMergeSelectedRows();
    });

    /* ── DELETE SELECTED ── */
    $('#button-delete-selected').on('click', function () {
        var $checked = $('#price-list input[name="selected[]"]:checked');
        if (!$checked.length) { showAlert(TEXT_ERROR, TEXT_NO_DATA); return; }
        showConfirm('Confirm', TEXT_DELETE_CONFIRM + ' (' + $checked.length + ')', function () {
            var fd = new FormData();
            $checked.each(function () { fd.append('selected[]', this.value); });
            fetch(URL_DELETE, { method: 'POST', body: fd })
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    if (json.error) { showAlert(TEXT_ERROR, htmlspecialchars(json.error)); return; }
                    $('#button-filter').trigger('click');
                })
                .catch(function () { showAlert(TEXT_ERROR, TEXT_AJAX_ERROR); });
        });
    });

    /* ── TRUNCATE ── */
    $('#button-truncate').on('click', function () {
        showConfirm('Confirm', TEXT_TRUNCATE_CONFIRM, function () {
            fetch(URL_TRUNCATE, { method: 'POST' })
                .then(function (r) { return r.json(); })
                .then(function (json) {
                    if (json.error) { showAlert(TEXT_ERROR, htmlspecialchars(json.error)); return; }
                    loadList('');
                })
                .catch(function () { showAlert(TEXT_ERROR, TEXT_AJAX_ERROR); });
        });
    });

    /* ── FETCH EBAY MARKET PRICES (ROW) ── */
    $(document).on('click', '.btn-fetch-market-row', function () {
        var id = parseInt($(this).attr('data-card-raw-id'), 10);
        if (isNaN(id) || id <= 0) {
            showAlert(TEXT_ERROR, TEXT_NO_DATA || 'Invalid row id');
            return;
        }

        fetchMarketPricesByIds([id], false);
    });

    /* ── FETCH EBAY MARKET PRICES (PREVIEW) ── */
    $('#button-fetch-preview-market-prices').on('click', function () {
        var $btn = $(this);
        var previewRows = [];

        if (!currentCards || !Array.isArray(currentCards) || !currentCards.length) {
            showAlert(TEXT_ERROR, TEXT_NO_DATA || 'No preview loaded.');
            return;
        }

        var $rows = $('#preview-table tbody tr[data-index]');
        if (!$rows.length) {
            showAlert(TEXT_ERROR, TEXT_NO_DATA || 'No records in preview.');
            return;
        }

        var $selectedRows = $rows.filter(function () {
            return $(this).find('.preview-row-check').is(':checked');
        });

        if (!$selectedRows.length) {
            $selectedRows = $rows;
        }

        $selectedRows.each(function () {
            var idx = parseInt($(this).attr('data-index'), 10);
            if (isNaN(idx)) {
                return;
            }

            var card = currentCards.find(function (c) {
                return parseInt(c._index, 10) === idx;
            });

            if (card) {
                previewRows.push(card);
            }
        });

        if (!previewRows.length) {
            showAlert(TEXT_ERROR, TEXT_NO_DATA || 'No records in preview.');
            return;
        }

        if (previewFetchState.active) {
            var activeModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('fetchProgressModal'));
            activeModal.show();
            return;
        }

        if (previewFetchState.paused && previewFetchState.previewRows && previewFetchState.previewRows.length) {
            runPreviewMarketFetch(previewFetchState.previewRows, $btn, previewFetchState.nextIndex, true);
            return;
        }

        runPreviewMarketFetch(previewRows, $btn, 0, false);
    });

    $('#button-stop-fetch-ebay').on('click', function () {
        if (!previewFetchState.active || previewFetchState.pauseRequested) {
            return;
        }

        previewFetchState.pauseRequested = true;
        updateFetchStopButtonUi();
        appendFetchProgressLog('warning', 'Pause demandée. Le fetch courant va se terminer puis le traitement va s\'arrêter.');
        setFetchProgressSummary('Pause demandée… fin de la requête en cours puis arrêt.', 'warning');
    });

    /* ── FIND DUPLICATES IN DB ── */
    $('#button-find-duplicates').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa-solid fa-circle-notch fa-spin me-1"></i>Analyse...');

        fetch(URL_FIND_DUPLICATES, { method: 'GET' })
            .then(function (r) { return r.json(); })
            .then(function (json) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-clone me-1"></i>Doublons DB');
                if (json.error) { showAlert('Erreur', htmlspecialchars(json.error)); return; }

                if (!json.duplicate_count) {
                    showAlert('Aucun doublon', '<i class="fa-solid fa-circle-check text-success me-2"></i>Aucun doublon trouvé dans la base de données.');
                    $('#db-duplicates-section').hide();
                    return;
                }

                $('#dup-summary').html(
                    '<span class="badge bg-danger me-1">' + json.duplicate_count + ' doublons</span>'
                    + 'dans <span class="badge bg-secondary me-1">' + json.group_count + ' groupes</span>'
                );
                $('#dup-results-body').html(json.html || '');
                $('#db-duplicates-section').show();
                document.getElementById('db-duplicates-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
            })
            .catch(function (err) {
                $btn.prop('disabled', false).html('<i class="fa-solid fa-clone me-1"></i>Doublons DB');
                showAlert('Erreur', TEXT_AJAX_ERROR + ' ' + err.message);
            });
    });

    /* ── DUPLICATES: check-all toggle ── */
    $(document).on('change', '#dup-check-all', function () {
        $('#duplicates-table .dup-checkbox').prop('checked', $(this).prop('checked'));
    });

    /* ── DUPLICATES: delete checked ── */
    $('#button-delete-duplicates').on('click', function () {
        var ids = [];
        $('#duplicates-table .dup-checkbox:checked').each(function () {
            ids.push(parseInt(this.value, 10));
        });
        if (!ids.length) { showAlert('Info', 'Aucun doublon coché à supprimer.'); return; }

        showConfirm('Confirmer la suppression',
            'Supprimer ' + ids.length + ' doublon(s) sélectionné(s) ?\nLes Keepers (vert) seront conservés.',
            function () {
                var fd = new FormData();
                ids.forEach(function (id) { fd.append('selected[]', id); });

                fetch(URL_DELETE, { method: 'POST', body: fd })
                    .then(function (r) { return r.json(); })
                    .then(function (json) {
                        if (json.error) { showAlert('Erreur', htmlspecialchars(json.error)); return; }
                        $('#db-duplicates-section').hide();
                        loadList('');
                    })
                    .catch(function () { showAlert('Erreur', TEXT_AJAX_ERROR); });
            }
        );
    });

    /* ── DUPLICATES: close section ── */
    $('#button-close-duplicates').on('click', function () {
        $('#db-duplicates-section').hide();
    });

    /* ── PREVIEW DUPLICATE DETECTION ── */
    function markPreviewDuplicates() {
        if (!currentCards || !currentCards.length) return;

        // Count occurrences per card_number (ignore empty)
        var counts = {};
        currentCards.forEach(function (c) {
            var cn = String(c.card_number || '').trim();
            if (!cn) return;
            counts[cn] = (counts[cn] || 0) + 1;
        });

        // Reset all rows first + remove banner
        $('#dup-alert-banner').remove();
        $('#preview-table tbody tr[data-index]').each(function () {
            $(this).find('.dup-badge').remove();
            $(this).find('.inline-merge-btn').remove();
            $(this).find('.preview-merge-col').html('');
            $(this).css({'outline': '', 'outline-offset': '', 'background-color': ''});
        });

        // For each card with a duplicate card_number, add badge
        currentCards.forEach(function (c) {
            var cn = String(c.card_number || '').trim();
            if (!cn || counts[cn] < 2) return;
            var $row = $('#preview-table tr[data-index="' + c._index + '"]');
            if (!$row.length) return;
            $row.css({'outline': '3px solid #fd7e14', 'outline-offset': '-3px', 'background-color': '#fff3e0'});
            // Find the card_number badge in col 4 (4th td = index 3)
            var $infoCell = $row.find('td').eq(3);
            if ($infoCell.find('.dup-badge').length) return; // already added
            var $badge = $('<span class="dup-badge badge" style="font-size:10px;vertical-align:middle;margin-left:4px;background:#fd7e14;color:#fff;cursor:pointer;" title="Clic sur Fusionner pour regrouper ces cartes">'
                + '<i class="fa-solid fa-clone me-1"></i>'
                + counts[cn] + ' doublons'
                + '</span>');
            // Insert after the card_number badge (bg-light text-dark border containing #cn)
            var $cnBadge = $infoCell.find('.badge.bg-light').filter(function () {
                return $(this).text().trim().replace(/^#/, '') === cn;
            });
            if ($cnBadge.length) {
                $cnBadge.after($badge);
            } else {
                $infoCell.find('div').eq(1).append($badge);
            }
        });

        // --- Boutons inline merge par groupe de doublons ---
        var dupGroupsMap = {};
        currentCards.forEach(function (c) {
            var cn = String(c.card_number || '').trim();
            if (!cn || counts[cn] < 2) return;
            if (!dupGroupsMap[cn]) dupGroupsMap[cn] = [];
            dupGroupsMap[cn].push(c._index);
        });
        Object.keys(dupGroupsMap).forEach(function (cn) {
            var idxs = dupGroupsMap[cn];
            // Trier par position DOM
            var rowsWithPos = idxs.map(function (idx) {
                var $r = $('#preview-table tr[data-index="' + idx + '"]');
                return {idx: idx, $row: $r, pos: $r.length ? $r.index() : 9999};
            }).filter(function (x) { return x.$row.length > 0; });
            rowsWithPos.sort(function (a, b) { return a.pos - b.pos; });
            if (!rowsWithPos.length) return;

            var $btn = $('<button type="button" class="btn btn-warning btn-sm inline-merge-btn" '
                + 'style="font-size:10px;padding:3px 4px;display:flex;flex-direction:column;align-items:center;gap:1px;min-width:38px;" '
                + 'title="Fusionner ' + idxs.length + ' lignes (card #' + cn + ')">' 
                + '<i class="fa-solid fa-code-merge"></i>'
                + '<span style="font-size:9px;line-height:1.1;">' + idxs.length + ' ⇌</span>'
                + '</button>');
            $btn.on('click', function (e) {
                e.stopPropagation();
                // Pré-sélectionner toutes les lignes du groupe
                $('#preview-table .preview-row-check').prop('checked', false);
                idxs.forEach(function (idx) {
                    $('#preview-table tr[data-index="' + idx + '"]').find('.preview-row-check').prop('checked', true);
                });
                doMergeSelectedRows();
            });
            rowsWithPos[0].$row.find('.preview-merge-col').append($btn);
        });

        // --- Banner d'avertissement doublons en haut du preview ---
        var dupGroups = Object.keys(counts).filter(function (cn) { return counts[cn] >= 2; });
        if (dupGroups.length > 0) {
            var totalDupCards = dupGroups.reduce(function (sum, cn) { return sum + counts[cn]; }, 0);
            var $banner = $('<div id="dup-alert-banner" class="alert alert-warning d-flex align-items-center gap-2 mb-2 py-2 px-3" style="cursor:pointer;border:2px solid #fd7e14;font-size:13px;" role="alert">'
                + '<i class="fa-solid fa-clone fa-lg"></i>'
                + '<span><strong>' + dupGroups.length + ' numéro' + (dupGroups.length > 1 ? 's' : '') + ' en doublon</strong>'
                + ' — ' + totalDupCards + ' cartes concernées.'
                + ' <u>Cliquez pour aller au premier doublon.</u></span>'
                + '</div>');
            $banner.on('click', function () {
                // Trouver la première rangée en doublon
                var $firstDup = $('#preview-table tbody tr[data-index]').filter(function () {
                    return $(this).find('.dup-badge').length > 0;
                }).first();
                if (!$firstDup.length) return;

                // Récupérer le card_number du premier groupe
                var firstIdx = parseInt($firstDup.attr('data-index'), 10);
                var firstCard = currentCards.find(function (c) { return parseInt(c._index, 10) === firstIdx; });
                var targetCn = firstCard ? String(firstCard.card_number || '').trim().toLowerCase() : '';

                // Décocher tout d'abord
                $('#preview-table .preview-row-check').prop('checked', false);

                // Cocher TOUTES les rangées ayant le même card_number
                $('#preview-table tbody tr[data-index]').each(function () {
                    var idx = parseInt($(this).attr('data-index'), 10);
                    var card = currentCards.find(function (c) { return parseInt(c._index, 10) === idx; });
                    if (card && String(card.card_number || '').trim().toLowerCase() === targetCn) {
                        $(this).find('.preview-row-check').prop('checked', true);
                    }
                });
                $('#preview-check-all').prop('checked', false); // pas "tout" coché

                // Scroll + flash sur la première rangée
                $('html, body').animate({scrollTop: $firstDup.offset().top - 100}, 300);
                $firstDup.css('outline', '4px solid #fd7e14');
                setTimeout(function () { $firstDup.css('outline', '3px solid #fd7e14'); }, 800);
            });
            // Insérer avant le tableau de preview
            var $table = $('#preview-table');
            if ($table.length) {
                $table.before($banner);
            } else {
                $('#preview-content').prepend($banner);
            }
        }
    }

}); // end $(document).ready
