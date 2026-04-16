// Original: shopmanager/card/card_listing_tab_cards.js
/**
 * card_listing_tab_cards.js
 * Handles all interactions for the #tab-cards panel:
 *   - Image zoom on hover
 *   - Edit-card button
 *   - Column header sorting
 *   - Drag & drop row reorder
 *   - Inline price / quantity editing via AJAX
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

(function ($) {
    'use strict';

    $(document).ready(function () {
        initImageZoom();
        initCardDetailsModal();
        initColumnSorting();
        initCardsSorting();
        initCardsSelection();
        initQuantityUpdater();
        initInfiniteScroll();
        initMarketPriceFetch();
    });

    // ── Market Price Fetch ────────────────────────────────────────────

    function buildPriceText(price, originalPrice) {
        var content = '$' + parseFloat(price).toFixed(2);

        if (originalPrice !== undefined && originalPrice !== null && originalPrice !== '' && parseFloat(originalPrice) > parseFloat(price)) {
            content += ' <span style="color:#666;text-decoration:line-through;">$' + parseFloat(originalPrice).toFixed(2) + '</span>';
        }

        return content;
    }

    function getGradingConfig() {
        var $table = $('#cards-table');
        var ebayFeeRate = parseFloat($table.data('ebay-fee-rate'));
        var psaCertificationPrice = parseFloat($table.data('psa-certification-price'));

        if (isNaN(ebayFeeRate) || ebayFeeRate < 0) {
            ebayFeeRate = 0.13;
        }

        if (isNaN(psaCertificationPrice) || psaCertificationPrice < 0) {
            psaCertificationPrice = 55;
        }

        return {
            ebayFeeRate: ebayFeeRate,
            psaCertificationPrice: psaCertificationPrice
        };
    }

    function getBestGradedMarketPrice(auctionGraded, listGraded) {
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

        if (prices.length === 0) {
            return null;
        }

        return Math.min.apply(null, prices);
    }

    function evaluateGradingPotential($row, auctionGraded, listGraded) {
        var bestGradedPrice = getBestGradedMarketPrice(auctionGraded, listGraded);
        if (bestGradedPrice === null || isNaN(bestGradedPrice) || bestGradedPrice <= 0) {
            return null;
        }

        var config = getGradingConfig();
        var currentPrice = parseFloat($row.find('.price-input').val() || $row.data('price') || 0);
        if (isNaN(currentPrice) || currentPrice < 0) {
            currentPrice = 0;
        }

        var netAfterFeesAndPsa = bestGradedPrice - (bestGradedPrice * config.ebayFeeRate) - config.psaCertificationPrice;
        var profit = netAfterFeesAndPsa - currentPrice;

        return {
            bestGradedPrice: bestGradedPrice,
            netAfterFeesAndPsa: netAfterFeesAndPsa,
            currentPrice: currentPrice,
            profit: profit,
            isProfitable: profit > 0
        };
    }

    function applyGradingPotentialStyle($row, potential) {
        var $priceInput = $row.find('.price-input');
        var $priceCell = $priceInput.closest('td');
        var $profitNote = $priceCell.find('.grading-profit-note');

        if (!$profitNote.length) {
            $profitNote = $('<div class="grading-profit-note" style="font-size:11px;line-height:1.2;margin-top:4px;"></div>');
            $priceCell.append($profitNote);
        }

        if (!potential || !potential.isProfitable) {
            $priceCell.css('background-color', '');
            $priceInput.attr('title', '');
            $profitNote.text('');
            return;
        }

        $priceCell.css('background-color', '#ffd700');
        $priceInput.attr('title', 'Grading potentiel: profit +' + potential.profit.toFixed(2) + ' CAD');
        $profitNote.html(
            '<span style="font-weight:600;color:#7a5c00;">Profit grading: $' + potential.profit.toFixed(2) + ' CAD</span>'
        );
    }

    function applyGradingPotentialToRow($row, auctionGraded, listGraded) {
        var potential = evaluateGradingPotential($row, auctionGraded, listGraded);
        applyGradingPotentialStyle($row, potential);
    }

    function buildOwnListingNote(ownListing, priceType) {
        if (!ownListing || ownListing.price === null || ownListing.price === undefined || ownListing.price === '') {
            return '';
        }

        var ownListLabel = $('#cards-table').data('own-list-label') || 'Our eBay';
        var variantLabel = $('#cards-table').data('variant-label') || 'Variant';
        var note = '<div style="font-size:11px;color:#666;line-height:1.1;margin-bottom:2px;">' + htmlspecialchars(ownListLabel) + ' ' + buildPriceText(ownListing.price, ownListing.original_price);

        if (priceType === 'auction' && ownListing.bid_count !== undefined && ownListing.bid_count !== null) {
            note += ' • ' + parseInt(ownListing.bid_count, 10) + ' bid' + (parseInt(ownListing.bid_count, 10) === 1 ? '' : 's');
        }

        if (ownListing.is_variant) {
            note += ' • ' + htmlspecialchars(variantLabel);
        }

        note += '</div>';

        return note;
    }

    function getComparisonPrice(cardPrice, priceType, ownListing) {
        if (ownListing && ownListing.price !== null && ownListing.price !== undefined && ownListing.price !== '') {
            return parseFloat(ownListing.price);
        }

        return parseFloat(cardPrice) || 0;
    }

    function getMarketPriceColor(priceValue, cardPrice, priceType, ownListing) {
        var p = parseFloat(priceValue);
        var comparePrice = getComparisonPrice(cardPrice, priceType, ownListing);

        return (p <= comparePrice) ? '#dc3545' : '#28a745';
    }

    function isRateLimitMessage(message) {
        var text = String(message || '').toLowerCase();
        return text.indexOf('call limit') !== -1 ||
               text.indexOf('rate limit') !== -1 ||
               text.indexOf('quota') !== -1 ||
               text.indexOf('too many requests') !== -1 ||
               text.indexOf('http 429') !== -1 ||
               text.indexOf('error 429') !== -1 ||
               text.indexOf('request limit') !== -1;
    }

    function buildManualUrlsFromKeyword(keyword) {
        var rawKeyword = String(keyword || '').trim();
        var base = 'https://www.ebay.ca/sch/i.html';
        return {
            auction_raw: base + '?_nkw=' + encodeURIComponent(rawKeyword) + '&_sacat=261328&_sop=2&LH_Auction=1&Graded=No',
            auction_graded: base + '?_nkw=' + encodeURIComponent(rawKeyword) + '&_sacat=261328&_sop=2&LH_Auction=1&Graded=Yes',
            buy_now_raw: base + '?_nkw=' + encodeURIComponent(rawKeyword) + '&_sacat=261328&_sop=2&LH_BIN=1&Graded=No',
            buy_now_graded: base + '?_nkw=' + encodeURIComponent(rawKeyword) + '&_sacat=261328&_sop=2&LH_BIN=1&Graded=Yes',
            sold_graded: base + '?_nkw=' + encodeURIComponent(rawKeyword) + '&_sacat=261328&LH_Sold=1&Graded=Yes&_sop=3&_udlo=35'
        };
    }

    function buildSoldGradedButton(keyword) {
        var rawKeyword = String(keyword || '').trim();
        var targetName = 'ebay_market_preview';
        var soldLabel = ($('#cards-table').data('market-manual-sold-graded-label') || 'Sold Graded').toString();

        if (!rawKeyword) {
            return '';
        }

        var manualUrls = buildManualUrlsFromKeyword(rawKeyword);
        if (!manualUrls.sold_graded) {
            return '';
        }

        return '<div class="mt-1">'
             + '<a class="btn btn-sm btn-dark w-100" style="font-size:10px;padding:2px 6px;line-height:1.15;" target="' + targetName + '" href="' + htmlspecialchars(manualUrls.sold_graded) + '">' + htmlspecialchars(soldLabel) + '</a>'
             + '</div>';
    }

    function renderManualMarketButtons($row, manualUrls) {
        var urls = manualUrls || {};
        var auctionButtons = '';
        var buyNowButtons = '';
        var targetName = 'ebay_market_preview';

        if (urls.auction_raw) {
            auctionButtons += '<a class="btn btn-sm btn-primary me-1 mb-1" style="font-size:10px;padding:2px 6px;line-height:1.15;" target="' + targetName + '" href="' + htmlspecialchars(urls.auction_raw) + '">Raw</a>';
        }
        if (urls.auction_graded) {
            auctionButtons += '<a class="btn btn-sm btn-dark mb-1" style="font-size:10px;padding:2px 6px;line-height:1.15;" target="' + targetName + '" href="' + htmlspecialchars(urls.auction_graded) + '">Graded</a>';
        }
        if (urls.buy_now_raw) {
            buyNowButtons += '<a class="btn btn-sm btn-primary me-1 mb-1" style="font-size:10px;padding:2px 6px;line-height:1.15;" target="' + targetName + '" href="' + htmlspecialchars(urls.buy_now_raw) + '">Raw</a>';
        }
        if (urls.buy_now_graded) {
            buyNowButtons += '<a class="btn btn-sm btn-dark mb-1" style="font-size:10px;padding:2px 6px;line-height:1.15;" target="' + targetName + '" href="' + htmlspecialchars(urls.buy_now_graded) + '">Graded</a>';
        }

        if (!auctionButtons) {
            auctionButtons = '<span class="text-muted">—</span>';
        }
        if (!buyNowButtons) {
            buyNowButtons = '<span class="text-muted">—</span>';
        }

        $row.find('.market-price-auction').html('<div class="d-flex flex-wrap justify-content-center">' + auctionButtons + '</div>');
        $row.find('.market-price-list').html('<div class="d-flex flex-wrap justify-content-center">' + buyNowButtons + '</div>');
    }

    function refreshRowMarketPrices($row) {
        var cardPrice     = parseFloat($row.find('.price-input').val() || $row.data('price') || 0);
        var auctionGraded = $row.data('price-sold-graded-obj') || null;
        var listGraded    = $row.data('price-list-graded-obj') || null;

        updateMarketCell($row.find('.market-price-auction'), $row.data('price-sold'), cardPrice, $row.data('price-sold-url') || '', $row.data('price-sold-bids'), 'auction', $row.data('own-auction') || null, auctionGraded);
        updateMarketCell($row.find('.market-price-list'), $row.data('price-list'), cardPrice, $row.data('price-list-url') || '', '', 'buy_now', $row.data('own-buy-now') || null, listGraded);
    }

    function refreshAllMarketPrices() {
        $('#cards-tbody tr[data-card-id]').each(function () {
            refreshRowMarketPrices($(this));
        });
    }

    function getMarketProgressText(key, fallback) {
        var $table = $('#cards-table');
        var value = $table.data(key);

        if (typeof value === 'undefined' || value === null || value === '') {
            return fallback;
        }

        return String(value);
    }

    function initMarketPriceFetch() {
        $(document).on('click', '#btn-fetch-market-prices', function () {
            var $btn        = $(this);
            var $table      = $('#cards-table');
            var urlFetch    = $table.data('url-fetch-market-price');
            var $rows       = $('#cards-tbody tr[data-card-id]');
            var $progress   = $('#market-price-progress');
            var progressLabel = getMarketProgressText('market-progress-label', 'Fetching prices...');
            var progressDone = getMarketProgressText('market-progress-done', 'prices updated.');
            var progressLimit = getMarketProgressText('market-progress-limit', 'eBay API limit reached. Stopping checks.');
            var progressFallback = getMarketProgressText('market-progress-fallback', 'Manual buttons displayed for the remaining rows.');

            if (!urlFetch) { alert('URL not configured.'); return; }
            if ($rows.length === 0) { alert('No cards in table.'); return; }

            $btn.prop('disabled', true);
            var total = $rows.length;
            var done  = 0;
            var stoppedByRateLimit = false;

            function stopBecauseRateLimit(triggerIndex, message, $triggerRow, manualUrls) {
                if (stoppedByRateLimit) {
                    return;
                }

                stoppedByRateLimit = true;

                if ($triggerRow && $triggerRow.length) {
                    renderManualMarketButtons($triggerRow, manualUrls || buildManualUrlsFromKeyword(($triggerRow.data('keyword') || '').toString()));
                }

                for (var i = triggerIndex + 1; i < $rows.length; i++) {
                    var $nextRow = $($rows[i]);
                    var nextKeyword = ($nextRow.data('keyword') || '').toString();
                    renderManualMarketButtons($nextRow, buildManualUrlsFromKeyword(nextKeyword));
                }

                $btn.prop('disabled', false);
                $progress.text('⚠ ' + progressLimit + ' ' + progressFallback);
                if (message) {
                    $progress.attr('title', message);
                }
                setTimeout(function () { $progress.text(''); }, 6000);
            }

            function processNext(index) {
                if (stoppedByRateLimit) {
                    return;
                }

                if (index >= $rows.length) {
                    $btn.prop('disabled', false);
                    $progress.text('\u2713 ' + done + '/' + total + ' ' + progressDone);
                    setTimeout(function () { $progress.text(''); }, 5000);
                    return;
                }
                var $row      = $($rows[index]);
                var cardId    = $row.data('card-id');
                var cardPrice = parseFloat($row.data('price') || 0);
                var keyword   = $row.data('keyword') || '';
                $progress.text(progressLabel + ' (' + (index + 1) + '/' + total + ') ' + (keyword || 'card ' + cardId) + '...');

                $.ajax({
                    url: urlFetch,
                    type: 'POST',
                    data: { card_id: cardId, keyword: keyword },
                    dataType: 'json',
                    success: function (r) {
                        var combinedMessage = [r.error, r.api_error, r.php_warning].filter(Boolean).join(' | ');
                        if (r.rate_limited || isRateLimitMessage(combinedMessage)) {
                            stopBecauseRateLimit(index, combinedMessage, $row, r.manual_urls || buildManualUrlsFromKeyword(keyword));
                            return;
                        }

                        if (r.success) {
                            done++;
                            var auctionGraded = r.price_sold_graded ? {price: r.price_sold_graded, url: r.price_sold_graded_url || '', bids: r.price_sold_graded_bids || 0, grade: r.price_sold_graded_grade || ''} : null;
                            var listGraded    = r.price_list_graded  ? {price: r.price_list_graded,  url: r.price_list_graded_url  || '', grade: r.price_list_graded_grade  || ''} : null;
                            updateMarketCell($row.find('.market-price-auction'), r.price_sold, cardPrice, r.price_sold_url || '', r.price_sold_bids, 'auction', r.own_auction || null, auctionGraded);
                            updateMarketCell($row.find('.market-price-list'),  r.price_list,  cardPrice, r.price_list_url || '', '', 'buy_now', r.own_buy_now || null, listGraded);
                            $row.data('price-sold', r.price_sold || '');
                            $row.data('price-sold-url', r.price_sold_url || '');
                            $row.data('price-sold-bids', r.price_sold_bids !== undefined && r.price_sold_bids !== null ? r.price_sold_bids : '');
                            $row.data('price-list', r.price_list  || '');
                            $row.data('price-list-url', r.price_list_url || '');
                            $row.data('own-auction', r.own_auction || null);
                            $row.data('own-buy-now', r.own_buy_now || null);
                            $row.data('price-sold-graded-obj', auctionGraded);
                            $row.data('price-list-graded-obj', listGraded);
                            applyGradingPotentialToRow($row, auctionGraded, listGraded);
                            // Show API-level error (no results, etc.) as tooltip
                            if (r.api_error) {
                                $row.find('.market-price-auction').attr('title', r.api_error);
                                $row.find('.market-price-list').attr('title', r.api_error);
                            }
                        } else {
                            // Show server error on the row
                            var errMsg = r.error || 'unknown error';
                            if (isRateLimitMessage(errMsg)) {
                                stopBecauseRateLimit(index, errMsg, $row, r.manual_urls || buildManualUrlsFromKeyword(keyword));
                                return;
                            }
                            $row.find('.market-price-auction').html('<span class="text-danger" title="' + errMsg + '">ERR</span>');
                            $row.find('.market-price-list').html('<span class="text-danger" title="' + errMsg + '">ERR</span>');
                        }

                        if (!stoppedByRateLimit) {
                            setTimeout(function () { processNext(index + 1); }, 400);
                        }
                    },
                    error: function (xhr) {
                        // Show raw response for diagnosis (first 200 chars)
                        var raw = (xhr.responseText || 'no response').substring(0, 200);
                        if (isRateLimitMessage(raw)) {
                            stopBecauseRateLimit(index, raw, $row, buildManualUrlsFromKeyword(keyword));
                            return;
                        }
                        $row.find('.market-price-auction').html('<span class="text-danger" title="' + raw.replace(/"/g,'&quot;') + '">XHR ERR</span>');
                        $row.find('.market-price-list').html('<span class="text-danger" title="' + raw.replace(/"/g,'&quot;') + '">XHR ERR</span>');
                        if (!stoppedByRateLimit) {
                            setTimeout(function () { processNext(index + 1); }, 400);
                        }
                    }
                });
            }
            processNext(0);
        });
    }

    // Build the graded sub-line HTML (separator + grade price + grade label).
    function buildGradedSubLine(gradedData, cardPrice, priceType, ownListing) {
        var marketTarget = 'ebay_market_preview';
        if (!gradedData || !gradedData.price) { return ''; }
        var gColor   = getMarketPriceColor(parseFloat(gradedData.price), cardPrice, priceType || 'auction', ownListing || null);
        var gContent = '<span style="font-weight:600;color:#fff;background:' + gColor + ';padding:2px 6px;border-radius:4px;display:inline-block;">$' + parseFloat(gradedData.price).toFixed(2) + '</span>';
        if (gradedData.url) {
            gContent = '<a href="' + htmlspecialchars(gradedData.url) + '" target="' + marketTarget + '" style="text-decoration:none;">' + gContent + '</a>';
        }
        if (gradedData.grade) {
            gContent += ' <span style="font-size:11px;color:#555;">' + htmlspecialchars(gradedData.grade) + '</span>';
        }
        if (gradedData.bids) {
            gContent += '<div style="font-size:11px;color:#666;line-height:1.1;">' + parseInt(gradedData.bids, 10) + ' bid' + (parseInt(gradedData.bids, 10) === 1 ? '' : 's') + '</div>';
        }
        return '<div style="margin-top:3px;padding-top:3px;border-top:1px dashed #ddd;font-size:12px;">' + gContent + '</div>';
    }

    function updateMarketCell($cell, price, cardPrice, url, bidCount, priceType, ownListing, gradedData) {
        var marketTarget = 'ebay_market_preview';
        if (price === null || price === undefined || price === '') {
            var emptyContent = buildOwnListingNote(ownListing, priceType || 'auction')
                             + buildGradedSubLine(gradedData || null, cardPrice, priceType, ownListing)
                             + '<span class="text-muted">—</span>';
            $cell.html(emptyContent);
            return;
        }
        var p     = parseFloat(price);
        var color = getMarketPriceColor(p, cardPrice, priceType || 'auction', ownListing || null);
        var content = '<span style="font-weight:700;color:#fff;background:' + color + ';padding:2px 6px;border-radius:4px;display:inline-block;">$' + p.toFixed(2) + '</span>';
        if (url) {
            content = '<a href="' + htmlspecialchars(url) + '" target="' + marketTarget + '" style="text-decoration:none;">' + content + '</a>';
        }
        if (bidCount !== undefined && bidCount !== null && bidCount !== '') {
            content += '<div style="font-size:11px;color:#666;line-height:1.1;">' + parseInt(bidCount, 10) + ' bid' + (parseInt(bidCount, 10) === 1 ? '' : 's') + '</div>';
        }
        content += buildGradedSubLine(gradedData || null, cardPrice, priceType, ownListing);
        $cell.html(buildOwnListingNote(ownListing, priceType || 'auction') + content);
    }

    // ── Image zoom on hover ────────────────────────────────────────────

    function initImageZoom() {
        $(document).on('mouseenter', '.actual-image-container .img-thumbnail', function () {
            var $wrapper = $(this).closest('.actual-image-container').find('.fullsize-actual-image-wrapper');
            if (!$wrapper.length) return;
            var $clone = $wrapper.clone().attr('id', 'temp-fullsize-preview');
            $('body').append($clone);
        });

        $(document).on('mouseleave', '.actual-image-container .img-thumbnail', function () {
            $('#temp-fullsize-preview').remove();
        });
    }

    // ── Edit-card button ───────────────────────────────────────────────

    function initCardDetailsModal() {
        $(document).on('click', '.edit-card', function (e) {
            e.preventDefault();
            var cardId = $(this).data('card-id');
            alert('Card details modal for card ID: ' + cardId + '\nThis feature will be implemented in the next phase.');
        });
    }

    // ── Column header sorting ──────────────────────────────────────────

    function initColumnSorting() {
        $(document).on('click', '#cards-table .sortable', function () {
            var $table   = $('#cards-table');
            var newSort  = $(this).data('sort');
            var curSort  = $table.data('sort');
            var curOrder = $table.data('order');
            var newOrder = (newSort === curSort && curOrder === 'ASC') ? 'DESC' : 'ASC';

            // Update sort icons
            $('#cards-table .sortable i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort');
            $(this).find('i').removeClass('fa-sort').addClass(newOrder === 'ASC' ? 'fa-sort-up' : 'fa-sort-down');

            fetchCards(1, newSort, newOrder, false);
        });
    }

    function updateHeaderCheckboxState() {
        var $rows = $('#cards-tbody tr[data-card-id]');
        var $checkedRows = $rows.find('.card-select-checkbox:checked');
        var $selectAll = $('#cards-select-all');

        if ($rows.length === 0) {
            $selectAll.prop('checked', false).prop('indeterminate', false);
            return;
        }

        if ($checkedRows.length === 0) {
            $selectAll.prop('checked', false).prop('indeterminate', false);
        } else if ($checkedRows.length === $rows.length) {
            $selectAll.prop('checked', true).prop('indeterminate', false);
        } else {
            $selectAll.prop('checked', false).prop('indeterminate', true);
        }
    }

    function showSelectAllPopup(title) {
        if (!$('#loading-popup').length) {
            $('body').append(
                '<div id="loading-popup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.6); z-index:9999;">' +
                '  <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:#fff; padding:20px 30px; border-radius:8px; width:420px; max-width:90%; box-shadow:0 0 15px rgba(0,0,0,0.3); text-align:center;">' +
                '    <div id="loading-spinner" style="margin-bottom:10px;"><img src="https://i.imgur.com/llF5iyg.gif" width="50" alt="Loading..."></div>' +
                '    <h4 id="loading-title" style="color:#0056b3; margin-bottom:15px;">Loading...</h4>' +
                '    <div id="loading-messages" style="max-height:200px; overflow-y:auto; text-align:left; margin-top:10px; font-size:13px; line-height:1.6; padding:10px; background:#f8f9fa; border-radius:5px;"></div>' +
                '    <button type="button" id="close-loading-btn" style="margin-top:15px; display:none; padding:8px 20px; background:#0056b3; color:#fff; border:none; border-radius:5px; cursor:pointer;">Close</button>' +
                '  </div>' +
                '</div>'
            );

            $(document).off('click.cardsSelectPopupClose', '#close-loading-btn').on('click.cardsSelectPopupClose', '#close-loading-btn', function () {
                hideSelectAllPopup();
            });
        }

        if (typeof window.showLoadingPopup === 'function') {
            window.showLoadingPopup(title || 'Loading cards...');
            return;
        }

        var $popup = $('#loading-popup');
        if (!$popup.length) {
            return;
        }

        $('#loading-title').text(title || 'Loading cards...');
        $('#loading-messages').html('');
        $('#close-loading-btn').hide();
        $popup.show();
    }

    function appendSelectAllPopupMessage(message) {
        if (typeof window.appendLoadingMessage === 'function') {
            window.appendLoadingMessage(message, 'info');
            return;
        }

        var $messages = $('#loading-messages');
        if (!$messages.length) {
            return;
        }

        $messages.html('<div><span style="color:#007bff">ℹ️ ' + htmlspecialchars(message) + '</span></div>');
    }

    function hideSelectAllPopup() {
        if (typeof window.hideLoadingPopup === 'function') {
            window.hideLoadingPopup();
            return;
        }

        var $popup = $('#loading-popup');
        if ($popup.length) {
            $popup.hide();
        }
    }

    function loadAllCardsThen(callback) {
        var $table = $('#cards-table');
        var total = parseInt($table.data('total') || 0, 10);
        var loaded = $('#cards-tbody tr[data-card-id]').length;
        var sort = $table.data('sort');
        var order = $table.data('order');
        var isLoadingAll = false;

        if (loaded >= total) {
            if (typeof callback === 'function') callback();
            return;
        }

        showSelectAllPopup('Selecting all cards...');
        appendSelectAllPopupMessage('Loading cards (' + loaded + '/' + total + ')...');

        function loadNext() {
            var currentLoaded = $('#cards-tbody tr[data-card-id]').length;
            if (currentLoaded >= total) {
                hideSelectAllPopup();
                if (typeof callback === 'function') callback();
                return;
            }

            if (isLoadingAll) {
                return;
            }

            isLoadingAll = true;

            var nextPage = parseInt($table.data('page') || 1, 10) + 1;
            appendSelectAllPopupMessage('Loading cards (' + currentLoaded + '/' + total + ')...');
            fetchCards(nextPage, sort, order, true, null, function () {
                isLoadingAll = false;
                var afterLoaded = $('#cards-tbody tr[data-card-id]').length;
                if (afterLoaded <= currentLoaded) {
                    hideSelectAllPopup();
                    if (typeof callback === 'function') callback();
                    return;
                }
                loadNext();
            });
        }

        loadNext();
    }

    function initCardsSelection() {
        $(document).on('change', '.card-select-checkbox', function () {
            updateHeaderCheckboxState();
        });

        $(document).on('change', '#cards-select-all', function () {
            if (!$(this).is(':checked')) {
                $('.card-select-checkbox').prop('checked', false);
                updateHeaderCheckboxState();
                return;
            }

            loadAllCardsThen(function () {
                $('.card-select-checkbox').prop('checked', true);
                updateHeaderCheckboxState();
            });
        });

        $(document).on('click', '#btn-select-all-cards', function () {
            $('#cards-select-all').prop('checked', true).prop('indeterminate', false).trigger('change');
        });

        $(document).on('click', '#btn-deselect-all-cards', function () {
            $('.card-select-checkbox').prop('checked', false);
            $('#cards-select-all').prop('checked', false).prop('indeterminate', false);
            hideSelectAllPopup();
        });

        updateHeaderCheckboxState();
    }

    // ── Drag & drop row reorder ────────────────────────────────────────

    function initCardsSorting() {
        if (typeof $.fn.sortable !== 'function') {
            console.warn('jQuery UI Sortable not loaded. Drag & drop will not work.');
            return;
        }

        $('#cards-tbody').sortable({
            handle: '.drag-handle',
            placeholder: 'ui-state-highlight',
            axis: 'y',
            cursor: 'move',
            update: function (event, ui) {
                var cardIds = [];
                $('#cards-tbody tr').each(function () {
                    var id = $(this).data('card-id');
                    if (id) cardIds.push(id);
                });

                var userToken = new URLSearchParams(window.location.search).get('user_token');
                $.ajax({
                    url: 'index.php?route=shopmanager/card/card_listing.updateSortOrder&user_token=' + userToken,
                    type: 'POST',
                    data: { card_ids: cardIds },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            ui.item.effect('highlight', { color: '#d4edda' }, 1000);
                        } else {
                            alert('Error: ' + (response.error || 'Failed to update sort order'));
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('Error updating sort order: ' + error);
                    }
                });
            }
        });

        if (!$('#sortable-placeholder-style').length) {
            $('<style id="sortable-placeholder-style">')
                .text('.ui-state-highlight { height: 50px; background-color: #f0f0f0; border: 2px dashed #ccc; }')
                .appendTo('head');
        }
    }

    // ── Inline quantity / price editing ───────────────────────────────

    function initQuantityUpdater() {
        $(document).on('blur', '.price-input', function () { updateCardPrice($(this)); });
        $(document).on('keypress', '.price-input', function (e) {
            if (e.which === 13) { e.preventDefault(); $(this).blur(); }
        });

        $(document).on('blur', '.raw-price-input', function () { updateCardRawPrice($(this)); });
        $(document).on('keypress', '.raw-price-input', function (e) {
            if (e.which === 13) { e.preventDefault(); $(this).blur(); }
        });

        $(document).on('blur', '.quantity-input', function () { updateCardQuantity($(this)); });
        $(document).on('keypress', '.quantity-input', function (e) {
            if (e.which === 13) { e.preventDefault(); $(this).blur(); }
        });
    }

    function updateCardPrice($input) {
        var cardId        = $input.data('card-id');
        var newPrice      = parseFloat($input.val());
        var originalValue = $input.attr('data-original-value');

        if (isNaN(newPrice) || newPrice < 0) {
            alert('Invalid price. Please enter a valid number.');
            $input.val(typeof originalValue !== 'undefined' ? originalValue : $input.val());
            return;
        }

        newPrice = newPrice.toFixed(2);
        // Skip only if original is explicitly known and unchanged
        if (typeof originalValue !== 'undefined' && parseFloat(newPrice) === parseFloat(originalValue)) return;

        $input.prop('disabled', true).addClass('bg-light');
        var userToken = new URLSearchParams(window.location.search).get('user_token');

        $.ajax({
            url: 'index.php?route=shopmanager/card/card.updatePrice&user_token=' + userToken,
            type: 'POST',
            data: { card_id: cardId, price: newPrice },
            dataType: 'json',
            success: function (response) {
                $input.prop('disabled', false).removeClass('bg-light');
                if (response.success) {
                    $input.val(newPrice).attr('data-original-value', newPrice);
                    $input.closest('tr').data('price', newPrice);
                    refreshRowMarketPrices($input.closest('tr'));
                    $input.addClass('border-success');
                    setTimeout(function () { $input.removeClass('border-success'); }, 1500);
                } else {
                    alert('Error: ' + (response.error || 'Failed to update price'));
                    if (typeof originalValue !== 'undefined') { $input.val(originalValue); }
                }
            },
            error: function (xhr, status, error) {
                $input.prop('disabled', false).removeClass('bg-light');
                alert('Error updating price: ' + error);
                if (typeof originalValue !== 'undefined') { $input.val(originalValue); }
            }
        });
    }

    function updateCardRawPrice($input) {
        var cardId        = $input.data('card-id');
        var rawVal        = $input.val().trim();
        var originalValue = $input.attr('data-original-value');

        // Allow clearing (empty = NULL)
        var newRawPrice = rawVal === '' ? '' : parseFloat(rawVal);

        if (rawVal !== '' && (isNaN(newRawPrice) || newRawPrice < 0)) {
            alert('Invalid raw price. Please enter a valid number or leave empty.');
            $input.val(typeof originalValue !== 'undefined' ? originalValue : '');
            return;
        }

        var displayVal = rawVal === '' ? '' : newRawPrice.toFixed(2);
        if (rawVal !== '') { newRawPrice = displayVal; }

        // For ref fields (value from oc_card_price), always save even if value matches — user is locking it in
        var isRef = $input.attr('data-is-ref') === '1';
        if (!isRef && typeof originalValue !== 'undefined' && displayVal === originalValue) return;

        $input.prop('disabled', true).addClass('bg-light');
        var userToken = new URLSearchParams(window.location.search).get('user_token');

        $.ajax({
            url: 'index.php?route=shopmanager/card/card.updateRawPrice&user_token=' + userToken,
            type: 'POST',
            data: { card_id: cardId, raw_price: rawVal },
            dataType: 'json',
            success: function (response) {
                $input.prop('disabled', false).removeClass('bg-light');
                if (response.success) {
                    $input.val(displayVal).attr('data-original-value', displayVal);
                    // Retirer le flag "référence" : la valeur est maintenant enregistrée
                    $input.attr('data-is-ref', '0').removeClass('raw-price-ref').removeAttr('title');
                    $input.addClass('border-success');
                    setTimeout(function () { $input.removeClass('border-success'); }, 1500);
                } else {
                    alert('Error: ' + (response.error || 'Failed to update raw price'));
                    if (typeof originalValue !== 'undefined') { $input.val(originalValue); }
                }
            },
            error: function (xhr, status, error) {
                $input.prop('disabled', false).removeClass('bg-light');
                alert('Error updating raw price: ' + error);
                if (typeof originalValue !== 'undefined') { $input.val(originalValue); }
            }
        });
    }

    function updateCardQuantity($input) {
        var cardId        = $input.data('card-id');
        var newQuantity   = parseInt($input.val());
        var originalValue = $input.attr('data-original-value');

        if (isNaN(newQuantity) || newQuantity < 0) {
            alert('Invalid quantity. Please enter a valid number.');
            $input.val(typeof originalValue !== 'undefined' ? originalValue : newQuantity);
            return;
        }

        // Skip only if original is explicitly known and unchanged
        if (typeof originalValue !== 'undefined' && newQuantity == originalValue) {
            return;
        }

        // Indicateur immédiat que le handler a bien capté le blur
        $input.css('outline', '2px solid orange');
        $input.prop('disabled', true).addClass('bg-light');
        var userToken = new URLSearchParams(window.location.search).get('user_token');
        var ajaxUrl = 'index.php?route=shopmanager/card/card.updateQuantity&user_token=' + userToken;

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: { card_id: cardId, quantity: newQuantity },
            dataType: 'json',
            success: function (response) {
                $input.prop('disabled', false).removeClass('bg-light').css('outline', '');
                if (response.success) {
                    $input.attr('data-original-value', newQuantity);
                    $input.addClass('border-success');
                    var $badge = $('<span class="badge bg-success ms-1 qty-saved-badge">✓ Quantité mise à jour</span>');
                    $input.after($badge);
                    setTimeout(function () {
                        $input.removeClass('border-success');
                        $badge.fadeOut(400, function () { $(this).remove(); });
                    }, 2000);
                } else {
                    alert('Error: ' + (response.error || 'Failed to update quantity'));
                    if (typeof originalValue !== 'undefined') { $input.val(originalValue); }
                }
            },
            error: function (xhr, status, error) {
                $input.prop('disabled', false).removeClass('bg-light').css('outline', '');
                alert('Erreur mise à jour quantité: ' + error);
                if (typeof originalValue !== 'undefined') { $input.val(originalValue); }
            }
        });
    }

    // ── Infinite scroll (AJAX) ────────────────────────────────────────

    function initInfiniteScroll() {
        var sentinel = document.getElementById('cards-load-sentinel');
        if (!sentinel) return;
        if (!('IntersectionObserver' in window)) return;

        var observer = new IntersectionObserver(function (entries) {
            if (!entries[0].isIntersecting) return;
            var $table  = $('#cards-table');
            var page    = parseInt($table.data('page') || 1);
            var total   = parseInt($table.data('total') || 0);
            var loaded  = $('#cards-tbody tr').length;

            if (loaded >= total) {
                observer.disconnect();
                $('#cards-load-sentinel').hide();
                return;
            }

            fetchCards(page + 1, $table.data('sort'), $table.data('order'), true, observer);
        }, { rootMargin: '400px' });

        observer.observe(sentinel);
    }
    // ── Build a <tr> from a card object returned by getCards() ────────────────

    function buildCardRow(card) {
        var batchColors = {1:'primary', 2:'success', 3:'warning', 4:'danger', 99:'dark', 100:'dark'};
        var batchNum    = parseInt(card.batch_name || 0);
        var bcolor      = batchColors[batchNum] || 'secondary';

        function imgCell(url) {
            if (!url) {
                return '<div class="text-muted" style="width:90px;height:90px;border:1px dashed #ccc;display:flex;align-items:center;justify-content:center;">'
                     + '<i class="fa fa-image"></i></div>';
            }
            var esc = $('<div>').text(url).html();
            return '<div class="actual-image-container" style="width:90px;height:90px;display:flex;align-items:center;justify-content:center;position:relative;">'
                 + '<img src="' + esc + '" class="img-thumbnail" style="max-width:90px;max-height:90px;width:auto;height:auto;"/>'
                 + '<div class="fullsize-actual-image-wrapper"><img src="' + esc + '" class="fullsize-actual-image"></div>'
                 + '</div>';
        }

        var img0 = (card.images && card.images[0]) ? card.images[0] : '';
        var img1 = (card.images && card.images[1]) ? card.images[1] : '';

        var price    = parseFloat(card.price || 0).toFixed(2);
        var rawPrice = (card.raw_price !== null && card.raw_price !== undefined && card.raw_price !== '') ? parseFloat(card.raw_price).toFixed(2) : '';
        var isRef    = card.raw_price_is_ref ? true : false;
        var rawClass = 'form-control raw-price-input' + (isRef ? ' raw-price-ref' : '');
        var rawTitle = isRef ? 'title="Valeur de r\u00e9f\u00e9rence (oc_card_price) \u2014 modifier pour l\u0027enregistrer"' : '';
        var mergeBadge = (parseInt(card.merge) === 1)
            ? '<span class="badge bg-success">Yes</span>'
            : '<span class="badge bg-secondary">No</span>';

        var skuEsc    = $('<div>').text(card.sku || '').html();
        var playerEsc = $('<div>').text(card.player_name || '').html();
        var numEsc    = $('<div>').text(card.card_number || '').html();

        // Build search keyword (title first, then fallback)
        var keyword = String(card.title || '').trim();
        if (!keyword) {
            if (card.set_name) {
                keyword = String(card.set_name).trim();
            } else if (card.player_name) {
                keyword = String(card.player_name).trim();
            } else if (card.card_number) {
                keyword = '#' + String(card.card_number).trim();
            } else if (card.brand) {
                keyword = String(card.brand).trim();
            } else if (card.year) {
                keyword = String(card.year).trim();
            }
        }
        var soldGradedButton = buildSoldGradedButton(keyword);

        return '<tr data-card-id="'    + card.card_id
             + '" data-sku="'         + skuEsc
             + '" data-player="'      + (card.player_name || '').toLowerCase()
             + '" data-card-number="' + numEsc
             + '" data-price="'       + price
             + '" data-quantity="'    + parseInt(card.quantity || 0)
             + '" data-batch="'       + batchNum
             + '" data-price-sold="'  + (card.price_sold || '')
             + '" data-price-sold-url="' + htmlspecialchars(card.price_sold_url || '')
             + '" data-price-sold-bids="' + htmlspecialchars(card.price_sold_bids || '')
             + '" data-price-list="'  + (card.price_list || '')
             + '" data-price-list-url="' + htmlspecialchars(card.price_list_url || '')
             + '" data-keyword="'     + $('<div>').text(keyword).html()
             + '">'
             + '<td class="text-center"><input type="checkbox" class="form-check-input card-select-checkbox" data-card-id="' + card.card_id + '" /></td>'
             + '<td><div style="display:flex;gap:5px;">' + imgCell(img0) + imgCell(img1) + '</div></td>'
             + '<td>' + playerEsc + '</td>'
             + '<td>' + numEsc + '</td>'
             + '<td class="text-center batch-badge-cell">'
             + '<span class="badge bg-' + bcolor + '" title="eBay Batch ' + batchNum + '">B' + batchNum + '</span>'
             + '</td>'
             + '<td><input type="text" class="form-control price-input" data-card-id="' + card.card_id + '" data-original-value="' + price + '" value="' + price + '" style="width:80px;"/>' + soldGradedButton + '</td>'
             + '<td class="market-price-auction text-center" style="min-width:80px;">'
             + buildMarketPriceSpan(card.price_sold, parseFloat(price), card.price_sold_url || '', card.price_sold_bids, 'auction', null)
             + '</td>'
             + '<td class="market-price-list text-center" style="min-width:80px;">'
             + buildMarketPriceSpan(card.price_list, parseFloat(price), card.price_list_url || '', '', 'buy_now', null)
             + '</td>'
             + '<td><input type="text" class="' + rawClass + '" data-card-id="' + card.card_id + '" data-original-value="' + rawPrice + '" data-is-ref="' + (isRef ? '1' : '0') + '" value="' + rawPrice + '" placeholder="\u2014" ' + rawTitle + ' style="width:80px;"/></td>'
             + '<td><input type="text" class="form-control quantity-input" data-card-id="' + card.card_id + '" data-original-value="' + parseInt(card.quantity || 0) + '" value="' + parseInt(card.quantity || 0) + '" style="width:80px;"/></td>'
             + '<td>' + mergeBadge + '</td>'
             + '<td><button type="button" class="btn btn-sm btn-primary edit-card" data-card-id="' + card.card_id + '"><i class="fa fa-pencil"></i></button></td>'
             + '</tr>';
    }

    // helper — safe integer cast (avoids NaN in HTML attributes)
    function int(v) { return parseInt(v) || 0; }

    function buildMarketPriceSpan(priceVal, cardPrice, url, bidCount, priceType, ownListing, gradedData) {
        var marketTarget = 'ebay_market_preview';
        if (priceVal === null || priceVal === undefined || priceVal === '') {
            return buildOwnListingNote(ownListing, priceType || 'auction')
                 + buildGradedSubLine(gradedData || null, cardPrice, priceType, ownListing)
                 + '<span class="text-muted">—</span>';
        }
        var p     = parseFloat(priceVal);
        var color = getMarketPriceColor(p, cardPrice, priceType || 'auction', ownListing || null);
        var content = '<span style="font-weight:700;color:#fff;background:' + color + ';padding:2px 6px;border-radius:4px;display:inline-block;">$' + p.toFixed(2) + '</span>';
        if (url) {
            content = '<a href="' + htmlspecialchars(url) + '" target="' + marketTarget + '" style="text-decoration:none;">' + content + '</a>';
        }
        if (bidCount !== undefined && bidCount !== null && bidCount !== '') {
            content += '<div style="font-size:11px;color:#666;line-height:1.1;">' + parseInt(bidCount, 10) + ' bid' + (parseInt(bidCount, 10) === 1 ? '' : 's') + '</div>';
        }
        content += buildGradedSubLine(gradedData || null, cardPrice, priceType, ownListing);
        return buildOwnListingNote(ownListing, priceType || 'auction') + content;
    }
    // ── Shared AJAX card fetch ────────────────────────────────────────

    function fetchCards(page, sort, order, append, observer, onDone) {
        var $table      = $('#cards-table');
        var listingId   = $table.data('listing-id');
        var urlGetCards = $table.data('url-get-cards');

        if (!listingId || !urlGetCards) {
            return;
        }

        $('#cards-loading-indicator').show();

        $.ajax({
            url:      urlGetCards,
            type:     'GET',
            dataType: 'json',
            data:     { filter_listing_id: listingId, page: page, sort: sort, order: order, limit: 25 },
            success: function (r) {
                $('#cards-loading-indicator').hide();
                if (!r.success || !r.cards) return;

                var $tbody = $('#cards-tbody');
                if (!append) { $tbody.empty(); }

                $.each(r.cards, function (i, card) { $tbody.append(buildCardRow(card)); });

                $table.data('page',  page);
                $table.data('sort',  sort);
                $table.data('order', order);
                $table.data('total', r.total);

                var loaded = $tbody.find('tr').length;
                if (loaded >= r.total) {
                    $('#cards-load-sentinel').hide();
                    if (observer) { observer.disconnect(); }
                } else {
                    $('#cards-load-sentinel').show();
                }

                updateHeaderCheckboxState();
                if (typeof onDone === 'function') {
                    onDone(r);
                }
            },
            error: function (xhr, status, err) {
                $('#cards-loading-indicator').hide();
                if (typeof onDone === 'function') {
                    onDone(null);
                }
            }
        });
    }

    // ── QR Label Print button ─────────────────────────────────────────────────
    $(document).on('click', '.btn-print-qr', function () {
        var sku       = $(this).data('sku') || '';
        if (!sku) { alert('No SKU assigned to this card.'); return; }
        var userToken = new URLSearchParams(window.location.search).get('user_token');
        var url = 'index.php?route=shopmanager/tools.create_label'
            + '&sku='        + encodeURIComponent(sku)
            + '&upc='
            + '&quantity=1'
            + '&user_token=' + encodeURIComponent(userToken);
        window.open(url, 'printWindow', 'width=400,height=500');
    });

})(window.jQuery || window.$);
