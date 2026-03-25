/**
 * Card Listing List JavaScript
 * Handles autocomplete and list functionality
 */

// ============================================
// LOADING POPUP UTILITY
// ============================================
function showLoadingPopup(title = TEXT_LOADING_IN_PROGRESS) {
    var loadingTitle = document.getElementById("loading-title");
    var loadingMessages = document.getElementById("loading-messages");
    var loadingPopup = document.getElementById("loading-popup");
    var closeLoadingBtn = document.getElementById("close-loading-btn");
    
    if (!loadingTitle || !loadingMessages || !loadingPopup) {
        console.warn('Loading popup elements not found in DOM');
        return;
    }
    
    loadingTitle.textContent = title;
    loadingMessages.innerHTML = '';
    loadingPopup.style.display = 'block';
    if (closeLoadingBtn) {
        closeLoadingBtn.style.display = 'none';
    }
}

function appendLoadingMessage(message, type = 'info') {
    const container = document.getElementById("loading-messages");
    
    if (!container) {
        console.warn('Loading messages container not found in DOM');
        return;
    }
    
    const color = {
        info: '#007bff',
        success: '#28a745',
        warning: '#ffc107',
        error: '#dc3545'
    }[type] || '#000';

    const icon = {
        info: 'ℹ️',
        success: '✅',
        warning: '⚠️',
        error: '❌'
    }[type] || '';

    const line = document.createElement('div');
    line.innerHTML = `<span style="color:${color}">${icon} ${message}</span>`;
    container.appendChild(line);
    container.scrollTop = container.scrollHeight;
}

function finishLoadingPopup(message = TEXT_COMPLETED) {
    appendLoadingMessage(message, 'success');
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = 'none';
    }
    const closeBtn = document.getElementById('close-loading-btn');
    if (closeBtn) {
        closeBtn.style.display = 'inline-block';
    }
}

function hideLoadingPopup() {
    const popup = document.getElementById("loading-popup");
    if (popup) {
        popup.style.display = 'none';
    }
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.style.display = 'block';
    }
}

// Autocomplete for Set Name
$('input[name=\'filter_set_name\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=shopmanager/card/card_listing.autocomplete&user_token=' + getURLVar('user_token'),
            data: {    
                filter_set_name: request
            },
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['set_name'] + ' (' + item['sport'] + ' ' + item['year'] + ')',
                        value: item['set_name'],
                        listing_id: item['listing_id']
                    };
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'filter_set_name\']').val(item['value']);
    }
});

// Autocomplete for Sport
$('input[name=\'filter_sport\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=shopmanager/card/card_listing.autocomplete&user_token=' + getURLVar('user_token'),
            type: 'post',
            data: {    
                filter_sport: request
            },
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['sport'],
                        value: item['sport']
                    };
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'filter_sport\']').val(item['value']);
    }
});

// Autocomplete for Manufacturer
$('input[name=\'filter_manufacturer\']').autocomplete({
    'source': function(request, response) {
        $.ajax({
            url: 'index.php?route=shopmanager/card/card_listing.autocomplete&user_token=' + getURLVar('user_token'),
            type: 'post',
            data: {    
                filter_manufacturer: request
            },
            dataType: 'json',
            success: function(json) {
                response($.map(json, function(item) {
                    return {
                        label: item['manufacturer'],
                        value: item['manufacturer']
                    };
                }));
            }
        });
    },
    'select': function(item) {
        $('input[name=\'filter_manufacturer\']').val(item['value']);
    }
});

// Helper function to get URL variable
function getURLVar(key) {
    var value = [];
    var query = String(document.location).split('?');
    
    if (query[1]) {
        var part = query[1].split('&');
        
        for (i = 0; i < part.length; i++) {
            var data = part[i].split('=');
            
            if (data[0] && data[1]) {
                value[data[0]] = data[1];
            }
        }
        
        if (value[key]) {
            return value[key];
        } else {
            return '';
        }
    }
    
    return '';
}

$(document).on('click', '.js-open-ebay-listing', function(e) {
    var ebayUrl = $(this).data('ebay-url') || $(this).attr('href') || '';
    if (!ebayUrl) {
        return;
    }

    e.preventDefault();
    e.stopPropagation();
    window.open(ebayUrl, 'ebaylisting');
});

/**
 * Publish card listing to eBay
 */
$(document).on('click', '.ebay-publish-icon', function() {
    var listingId = $(this).data('listing-id');
    var ebayUrl = $(this).data('ebay-url') || '';
    var $icon = $(this);

    if (ebayUrl) {
        window.open(ebayUrl, 'ebaylisting');
        return;
    }

    console.log('[eBay Publish] CLICK detected');
    console.log('[eBay Publish] listingId:', listingId);
    console.log('[eBay Publish] icon element:', this);
    console.log('[eBay Publish] icon src:', $(this).attr('src'));
    console.log('[eBay Publish] td data:', $(this).closest('td').data());
    
    if (!confirm(TEXT_CONFIRM_PUBLISH_SINGLE)) {
        console.log('[eBay Publish] Cancelled by user');
        return;
    }

    console.log('[eBay Publish] Confirmed — starting AJAX');
    
    // Display loading popup
    showLoadingPopup(TEXT_PUBLISHING_TO_EBAY);
    appendLoadingMessage(TEXT_INITIALIZING_PUBLICATION, 'info');
    appendLoadingMessage('Listing ID: ' + listingId, 'info');
    appendLoadingMessage(TEXT_PREPARING_CARD_SET_DATA, 'info');
    appendLoadingMessage(TEXT_RETRIEVING_DESCRIPTIONS, 'info');
    
    // Disable icon during processing
    $icon.css('opacity', '0.3').css('cursor', 'wait');
    
    $.ajax({
        url: 'index.php?route=shopmanager/card/card_listing.publishToEbay&user_token=' + getURLVar('user_token'),
        type: 'post',
        data: {
            listing_id: listingId
        },
        dataType: 'json',
        beforeSend: function() {
            appendLoadingMessage(TEXT_CONNECTING_EBAY_ACCOUNTS, 'info');
            appendLoadingMessage(TEXT_SENDING_DATA_TO_EBAY, 'info');
            appendLoadingMessage(TEXT_CREATING_CARD_VARIATIONS, 'info');
            appendLoadingMessage(TEXT_UPLOADING_IMAGES, 'info');
        },
        success: function(json) {
            console.log('[eBay Publish] AJAX success, raw response:', json);
            if (json.error) {
                appendLoadingMessage(TEXT_ERROR_PREFIX.replace('%s', json.error), 'error');
                if (json.warnings) {
                    json.warnings.forEach(function(warning) {
                        appendLoadingMessage('⚠️ ' + warning, 'warning');
                    });
                }
                finishLoadingPopup(TEXT_PUBLICATION_FAILED);
                $icon.css('opacity', '0.5').css('cursor', 'pointer');
            } else if (json.success) {
                appendLoadingMessage(TEXT_CARD_SET_PUBLISHED_SUCCESSFULLY, 'success');
                appendLoadingMessage(TEXT_NUMBER_OF_LISTINGS.replace('%s', (json.published_items ? json.published_items.length : 0)), 'success');
                
                if (json.published_items) {
                    json.published_items.forEach(function(item) {
                        var langText = item.language_id == 1 ? 'English' : (item.language_id == 3 ? 'Français' : 'Language ' + item.language_id);
                        appendLoadingMessage('✅ ' + langText + ': ' + item.ebay_item_id, 'success');
                    });
                }
                
                if (json.warnings) {
                    json.warnings.forEach(function(warning) {
                        appendLoadingMessage('⚠️ ' + warning, 'warning');
                    });
                }
                
                finishLoadingPopup(TEXT_PUBLICATION_COMPLETED_SUCCESSFULLY);

                // ── Update eBay cell in the row ───────────────────────────
                var $td           = $icon.closest('td');                console.log('[eBay Publish] $icon:', $icon[0]);
                console.log('[eBay Publish] $td found:', $td.length, $td[0]);
                console.log('[eBay Publish] td data-total-batches:', $td.data('total-batches'));                var totalBatches  = parseInt($td.data('total-batches') || 0);
                if (!totalBatches && json.published_items) totalBatches = json.published_items.length;
                if (!totalBatches) totalBatches = 1;
                var publishedCount = json.published_items ? json.published_items.length : 0;
                var hasWarnings   = json.warnings && json.warnings.length > 0;
                var allPub        = (publishedCount >= totalBatches);
                var somePub       = (publishedCount > 0);

                var imgFile;
                if (allPub && !hasWarnings) {
                    imgFile = 'catalog/marketplace/ebay_ca_green.png';
                } else if (hasWarnings && somePub) {
                    imgFile = 'catalog/marketplace/ebay_ca_grey.png'; // partial — keep grey
                } else if (hasWarnings) {
                    imgFile = 'catalog/marketplace/ebay_ca_red.png';
                } else {
                    imgFile = 'catalog/marketplace/ebay_ca_green.png';
                }
                var opacity = allPub ? '1' : (somePub ? '0.7' : '0.3');
                var color   = allPub && !hasWarnings ? '#28a745' : (somePub ? '#fd7e14' : '#6c757d');
                var fraction = publishedCount + '/' + totalBatches;

                $icon.attr('src', '../image/' + imgFile)
                     .css('opacity', opacity)
                     .attr('title', fraction + ' batches publiés — cliquer pour republier');
                $td.find('.ebay-fraction').text(fraction).css('color', color);
                $td.data('published-batches', publishedCount);
                var $tr = $icon.closest('tr');
                $tr.find('.listing-tag-icon').css('color', '#28a745');
                $tr.find('.listing-tag-count').css('color', '#28a745');
            }
        },
        error: function(xhr, ajaxOptions, thrownError) {
            console.log('[eBay Publish] AJAX error:', thrownError, xhr.status, xhr.responseText);
            appendLoadingMessage(TEXT_AJAX_ERROR_WITH_DETAILS.replace('%s', thrownError), 'error');
            if (xhr.responseText) {
                appendLoadingMessage(TEXT_DETAILS + ': ' + xhr.responseText.substring(0, 200), 'error');
            }
            finishLoadingPopup(TEXT_COMMUNICATION_ERROR);
            $icon.attr('src', '../image/catalog/marketplace/ebay_ca_red.png')
                 .css('opacity', '0.7')
                 .css('cursor', 'pointer');
        }
    });
});

// ============================================
// BATCH ACTION BUTTONS
// ============================================

function updateBatchButtons() {
    var checkboxes = $('.listing-checkbox:checked');
    var totalSelected = checkboxes.length;

    var publishedCount = 0;
    var notPublishedCount = 0;
    var partialCount = 0;

    checkboxes.each(function() {
        var status = $(this).data('ebay-status');
        if ($(this).data('partial') == 1) {
            partialCount++;
        }
        if (status === 'published') {
            publishedCount++;
        } else {
            notPublishedCount++;
        }
    });

    var container = $('#batch-action-container');
    var publishBtn = $('#batch-publish-btn');
    var endBtn = $('#batch-end-btn');
    var syncBtn = $('#batch-ebay-sync-btn');
    var assignBtn = $('#batch-assign-btn');

    // None selected
    if (totalSelected === 0) {
        container.hide();
        publishBtn.hide();
        endBtn.hide();
        syncBtn.hide();
        assignBtn.hide();
        $('#batch-health-check-btn').hide();
        return;
    }

    // Sync + assign always visible when ≥1 selected
    container.show();
    syncBtn.show();
    assignBtn.show();
    $('#batch-health-check-btn').show();
    $('#batch-count-sync').text(totalSelected);
    $('#batch-count-assign').text(totalSelected);
    $('#batch-count-health').text(totalSelected);

    // Any partial listing selected → show both Publish (remaining batches) and End (published batches)
    if (partialCount > 0) {
        publishBtn.show();
        endBtn.show();
        $('#batch-count').text(totalSelected);
        $('#batch-count-end').text(totalSelected);
        return;
    }

    // Mixed (some fully published, some not published at all) → only sync
    if (publishedCount > 0 && notPublishedCount > 0) {
        publishBtn.hide();
        endBtn.hide();
        return;
    }

    // All not published
    if (notPublishedCount === totalSelected) {
        publishBtn.show();
        endBtn.hide();
        $('#batch-count').text(totalSelected);
        return;
    }

    // All fully published
    if (publishedCount === totalSelected) {
        publishBtn.hide();
        endBtn.show();
        $('#batch-count-end').text(totalSelected);
        return;
    }
}


// Listen for checkbox changes
$(document).on('change', '.listing-checkbox', function() {
    updateBatchButtons();
});

// Select all checkbox - use a more robust approach
$(document).on('change', 'input[type="checkbox"][onclick*="selected"]', function() {
    setTimeout(updateBatchButtons, 100);
});

// Also listen on container just in case
$(document).on('change', '#form-listing input[type="checkbox"]', function() {
    setTimeout(updateBatchButtons, 50);
});

// ============================================
// SEQUENTIAL BATCH HELPER
// Traite les listing_ids un par un et affiche
// chaque résultat dans le popup au fur et à mesure.
// options: { url, dataFn, labelFn, onResult, totalLabel, doneLabel, failLabel }
// ============================================
function processSequentially(listingIds, options) {
    var total    = listingIds.length;
    var done     = 0;
    var errors   = 0;
    var ids      = listingIds.slice(); // copie

    function next() {
        if (ids.length === 0) {
            var summary = '✅ ' + (done - errors) + '/' + total
                        + (errors > 0 ? ' — ❌ ' + errors + ' erreur(s)' : '');
            finishLoadingPopup(options.doneLabel ? options.doneLabel.replace('%ok', done - errors).replace('%err', errors) : summary);
            return;
        }
        var lid = ids.shift();
        appendLoadingMessage('─── Listing #' + lid + ' (' + (total - ids.length) + '/' + total + ') ───', 'info');

        $.ajax({
            url:      options.url,
            type:     'POST',
            dataType: 'json',
            timeout:  120000,
            data:     options.dataFn ? options.dataFn(lid) : { listing_id: lid },
            success: function(json) {
                done++;
                if (options.onResult) {
                    options.onResult(lid, json);
                } else if (json.error) {
                    errors++;
                    appendLoadingMessage('❌ ' + json.error, 'error');
                } else {
                    appendLoadingMessage('✅ OK', 'success');
                }
                if (json.error && !options.onResult) errors++;
                next();
            },
            error: function(xhr, status, thrownError) {
                done++;
                errors++;
                appendLoadingMessage('❌ AJAX error: ' + thrownError, 'error');
                next();
            }
        });
    }

    next();
}

// Batch Publish Button
$(document).on('click', '#batch-publish-btn', function() {
    var listingIds = [];
    $('.listing-checkbox:checked').each(function() {
        listingIds.push($(this).val());
    });

    if (listingIds.length === 0) { alert(TEXT_NO_LISTINGS_SELECTED_JS); return; }
    if (!confirm(TEXT_CONFIRM_PUBLISH_MULTIPLE.replace('%s', listingIds.length))) return;

    showLoadingPopup(TEXT_PUBLISHING_MULTIPLE_LISTINGS.replace('%s', listingIds.length));
    appendLoadingMessage(TEXT_LISTINGS_TO_PUBLISH.replace('%s', listingIds.join(', ')), 'info');

    processSequentially(listingIds, {
        url: 'index.php?route=shopmanager/card/card_listing.publishToEbay&user_token=' + getURLVar('user_token'),
        onResult: function(lid, json) {
            if (json.error) {
                appendLoadingMessage('❌ ' + json.error, 'error');
                if (json.warnings) json.warnings.forEach(function(w) { appendLoadingMessage('⚠️ ' + w, 'warning'); });
                // icône rouge
                $('.listing-checkbox[value="' + lid + '"]').closest('tr')
                    .find('.ebay-publish-icon').attr('src', '../image/catalog/marketplace/ebay_ca_red.png').css('opacity', '0.7');
            } else if (json.success) {
                var count = json.published_items ? json.published_items.length : 0;
                appendLoadingMessage('✅ ' + count + ' batch(es) publié(s)', 'success');
                if (json.published_items) {
                    json.published_items.forEach(function(item) {
                        var lang = item.language_id == 1 ? 'EN' : 'FR';
                        appendLoadingMessage('  → ' + lang + ': ' + item.ebay_item_id, 'info');
                    });
                }
                if (json.warnings) json.warnings.forEach(function(w) { appendLoadingMessage('⚠️ ' + w, 'warning'); });
                // mise à jour icône
                var $row = $('.listing-checkbox[value="' + lid + '"]').closest('tr');
                var $td  = $row.find('td[data-total-batches]');
                var total = parseInt($td.data('total-batches') || count || 1);
                var hasWarn = json.warnings && json.warnings.length > 0;
                var allPub  = count >= total;
                var imgFile = (allPub && !hasWarn) ? 'catalog/marketplace/ebay_ca_green.png'
                            : (hasWarn ? 'catalog/marketplace/ebay_ca_grey.png' : 'catalog/marketplace/ebay_ca_green.png');
                $td.find('.ebay-publish-icon').attr('src', '../image/' + imgFile).css('opacity', allPub ? '1' : '0.7')
                   .attr('title', count + '/' + total + ' batches publiés');
                $td.find('.ebay-fraction').text(count + '/' + total).css('color', allPub && !hasWarn ? '#28a745' : '#fd7e14');
                $td.data('published-batches', count);
                $row.find('.listing-tag-icon, .listing-tag-count').css('color', '#28a745');
            }
        },
        doneLabel: TEXT_PUBLICATION_COMPLETED
    });
});

// Batch End Listings Button
$(document).on('click', '#batch-end-btn', function() {
    var listingIds = [];
    $('.listing-checkbox:checked').each(function() {
        listingIds.push($(this).val());
    });

    if (listingIds.length === 0) { alert(TEXT_NO_LISTINGS_SELECTED_JS); return; }
    if (!confirm(TEXT_CONFIRM_END_LISTINGS.replace('%s', listingIds.length))) return;

    showLoadingPopup(TEXT_ENDING_LISTINGS.replace('%s', listingIds.length));
    appendLoadingMessage(TEXT_LISTINGS_TO_END.replace('%s', listingIds.join(', ')), 'info');

    processSequentially(listingIds, {
        url: 'index.php?route=shopmanager/card/card_listing.endMultiple&user_token=' + getURLVar('user_token'),
        dataFn: function(lid) { return { listing_ids: [lid] }; },
        onResult: function(lid, json) {
            if (json.error) {
                appendLoadingMessage('❌ ' + json.error, 'error');
            } else if (json.success) {
                if (json.results) {
                    json.results.forEach(function(r) {
                        if (r.success) {
                            appendLoadingMessage('✅ ' + TEXT_LISTING_ENDED.replace('%s', r.listing_id), 'success');
                        } else {
                            appendLoadingMessage('❌ #' + r.listing_id + ': ' + r.error, 'error');
                        }
                    });
                } else {
                    appendLoadingMessage('✅ Terminé', 'success');
                }
            }
        },
        doneLabel: TEXT_ENDING_COMPLETED
    });
});

// ============================================
// BATCH EBAY SYNC (migrate + sync + republish)
// ============================================
$(document).on('click', '#batch-ebay-sync-btn', function() {
    var listingIds = [];
    $('.listing-checkbox:checked').each(function() {
        listingIds.push($(this).val());
    });

    if (listingIds.length === 0) {
        alert(typeof TEXT_NO_LISTINGS_SELECTED_JS !== 'undefined' ? TEXT_NO_LISTINGS_SELECTED_JS : 'No listings selected');
        return;
    }

    var confirmMsg = typeof TEXT_CONFIRM_BATCH_SYNC !== 'undefined'
        ? TEXT_CONFIRM_BATCH_SYNC.replace('%s', listingIds.length)
        : 'Run eBay sync on ' + listingIds.length + ' listing(s)?';
    if (!confirm(confirmMsg)) return;

    var runningMsg = typeof TEXT_BATCH_SYNC_RUNNING !== 'undefined'
        ? TEXT_BATCH_SYNC_RUNNING.replace('%s', listingIds.length)
        : 'eBay sync: ' + listingIds.length + ' listing(s)...';
    showLoadingPopup(runningMsg);
    appendLoadingMessage('Listings: ' + listingIds.join(', '), 'info');

    processSequentially(listingIds, {
        url: 'index.php?route=shopmanager/card/card_listing.batchEbaySync&user_token=' + getURLVar('user_token'),
        dataFn: function(lid) { return { listing_ids: [lid], sync_mode: 'missing' }; },
        onResult: function(lid, json) {
            if (json.error) {
                appendLoadingMessage('❌ ' + json.error, 'error');
                return;
            }
            if (json.results) {
                json.results.forEach(function(row) {
                    var prefix = row.status === 'ok' ? '✅' : '⚠️';
                    appendLoadingMessage(prefix + ' Listing #' + row.listing_id, row.status === 'ok' ? 'success' : 'warning');
                    if (row.refresh) {
                        appendLoadingMessage('  📋 Refresh: ' + (row.refresh.error ? '❌ ' + row.refresh.error
                            : (row.refresh.items_updated || 0) + ' items'), row.refresh.error ? 'error' : 'info');
                    }
                    if (row.migrate) {
                        appendLoadingMessage('  🖼️ Migrate: ' + (row.migrate.error ? '❌ ' + row.migrate.error
                            : (row.migrate.uploaded || 0) + ' uploaded, ' + (row.migrate.failed || 0) + ' failed'), row.migrate.error ? 'error' : 'info');
                    }
                    if (row.sync) {
                        appendLoadingMessage('  🔄 Sync: ' + (row.sync.error ? '❌ ' + row.sync.error
                            : (row.sync.synced || 0) + ' synced, ' + (row.sync.not_found || 0) + ' not found'), row.sync.error ? 'error' : 'info');
                    }
                    if (row.republish) {
                        appendLoadingMessage('  🚀 Republish: ' + (row.republish.error ? '❌ ' + row.republish.error
                            : (row.republish.published || 0) + ' published, ' + (row.republish.failed || 0) + ' failed'), row.republish.error ? 'error' : 'info');
                    }
                });
            }
        },
        doneLabel: typeof TEXT_BATCH_SYNC_COMPLETED !== 'undefined'
            ? TEXT_BATCH_SYNC_COMPLETED.replace('%s', '?').replace('%s', '?')
            : '✅ Sync terminé'
    });
});

// ============================================
// BULK ASSIGN BATCHES
// ============================================
$(document).on('click', '#batch-assign-btn', function() {
    var listingIds = [];
    $('.listing-checkbox:checked').each(function() {
        listingIds.push($(this).val());
    });

    if (listingIds.length === 0) {
        alert(typeof TEXT_NO_LISTINGS_SELECTED !== 'undefined' ? TEXT_NO_LISTINGS_SELECTED : 'Aucun listing sélectionné.');
        return;
    }

    showLoadingPopup('⚙️ Recalcul des batches...');
    appendLoadingMessage(listingIds.length + ' listing(s) à traiter', 'info');

    var index = 0;
    var successCount = 0;
    var errorCount = 0;

    function processNext() {
        if (index >= listingIds.length) {
            finishLoadingPopup('✅ Terminé — ' + successCount + ' OK, ' + errorCount + ' erreurs');
            return;
        }

        var lid = listingIds[index++];
        appendLoadingMessage('⚙️ Listing #' + lid + '…', 'info');

        $.ajax({
            url: 'index.php?route=shopmanager/card/card_listing.assignBatches&user_token=' + getURLVar('user_token'),
            type: 'POST',
            data: { listing_id: lid },
            dataType: 'json',
            timeout: 120000,
            success: function(json) {
                if (json.error) {
                    appendLoadingMessage('  ❌ #' + lid + ': ' + json.error, 'error');
                    errorCount++;
                } else {
                    var bCount = json.batches ? json.batches.length : '?';
                    appendLoadingMessage('  ✅ #' + lid + ' — ' + bCount + ' batch(es)', 'success');
                    successCount++;
                }
                processNext();
            },
            error: function(xhr, status, err) {
                appendLoadingMessage('  ❌ #' + lid + ' AJAX error: ' + err, 'error');
                errorCount++;
                processNext();
            }
        });
    }

    processNext();
});

// ============================================
// LOCATION INLINE EDIT (span → click → input → Enter → AJAX → span)
// Same pattern as product_list.js .pedit-location
// ============================================
$(document).on('click', '.pedit-clocation', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();

    var rel  = $(this).attr('rel');   // listing_id
    var rel1 = $(this).attr('rel1');  // current value

    // Replace span entirely with input (no nested-inside-span ambiguity)
    $(this).replaceWith(
        '<input type="text" id="cloc-input-' + rel + '"' +
        ' data-rel="' + rel + '" data-orig="' + (rel1 || '') + '"' +
        ' class="form-control form-control-sm cloc-input"' +
        ' value="' + (rel1 || '') + '"' +
        ' style="width:70px; text-align:center; font-size:0.8em; padding:2px 4px;"' +
        ' maxlength="20" placeholder="LOC" />'
    );

    var $input = $('#cloc-input-' + rel);
    $input.focus().select();

    $input.on('input', function () {
        this.value = this.value.toUpperCase();
    });

    $input.on('keydown', function (ke) {
        var orig = $(this).data('orig') || '';
        if (ke.key === 'Escape') {
            var origClass = orig ? 'pedit-clocation btn btn-sm btn-outline-secondary' : 'pedit-clocation';
            $(this).replaceWith(
                '<span id="cloc-' + rel + '" class="' + origClass + '" rel="' + rel + '" rel1="' + orig + '" style="cursor:pointer; font-size:0.78em; font-weight:600; letter-spacing:0.04em; padding:2px 6px;">' + (orig || '—') + '</span>'
            );
            return;
        }
        if (ke.key !== 'Enter') return;
        ke.preventDefault();
        ke.stopImmediatePropagation();
        var newLocation = $(this).val().trim().toUpperCase();
        confirmCardListingLocation(rel, newLocation, orig);
    });
});

function confirmCardListingLocation(listingId, newLocation, origLocation) {
    var userToken = getURLVar('user_token');
    origLocation = origLocation || '';
    $.ajax({
        url:      URL_UPDATE_LOCATION + '&user_token=' + userToken,
        type:     'POST',
        dataType: 'json',
        data:     { listing_id: listingId, location: newLocation },
        success: function (r) {
            var display = newLocation || '—';
            var btnClass = newLocation ? 'pedit-clocation btn btn-sm btn-outline-secondary' : 'pedit-clocation';
            var origBtnClass = origLocation ? 'pedit-clocation btn btn-sm btn-outline-secondary' : 'pedit-clocation';
            if (r.success) {
                $('#cloc-input-' + listingId).replaceWith(
                    '<span id="cloc-' + listingId + '" class="' + btnClass + '" rel="' + listingId + '" rel1="' + newLocation + '" style="cursor:pointer; font-size:0.78em; font-weight:600; letter-spacing:0.04em; padding:2px 6px; color:#28a745; border-color:#28a745;">' + display + '</span>'
                );
                // Update or add the QR print button
                var $td = $('#cloc-' + listingId).closest('td');
                var $printBtn = $td.find('.btn-print-clocation');
                if (newLocation) {
                    var printBtnHtml = '<button type="button" class="btn btn-sm btn-outline-secondary btn-print-clocation ms-1"'
                        + ' data-location="' + newLocation + '"'
                        + ' title="Imprimer étiquette QR"'
                        + ' style="padding:2px 5px; font-size:0.75em;">'
                        + '<i class="fa-solid fa-qrcode"></i></button>';
                    if ($printBtn.length) {
                        $printBtn.data('location', newLocation).attr('data-location', newLocation);
                    } else {
                        $td.append(printBtnHtml);
                    }
                } else {
                    $printBtn.remove();
                }
                setTimeout(function () {
                    $('#cloc-' + listingId).css({ color: '', borderColor: '' });
                }, 2000);
            } else {
                alert('Erreur: ' + (r.error || 'Inconnue'));
                $('#cloc-input-' + listingId).replaceWith(
                    '<span id="cloc-' + listingId + '" class="' + origBtnClass + '" rel="' + listingId + '" rel1="' + origLocation + '" style="cursor:pointer; font-size:0.78em; font-weight:600; letter-spacing:0.04em; padding:2px 6px;">' + (origLocation || '—') + '</span>'
                );
            }
        },
        error: function () {
            alert('AJAX error — location non sauvegardée');
            var origBtnClass = origLocation ? 'pedit-clocation btn btn-sm btn-outline-secondary' : 'pedit-clocation';
            $('#cloc-input-' + listingId).replaceWith(
                '<span id="cloc-' + listingId + '" class="' + origBtnClass + '" rel="' + listingId + '" rel1="' + origLocation + '" style="cursor:pointer; font-size:0.78em; font-weight:600; letter-spacing:0.04em; padding:2px 6px;">' + (origLocation || '—') + '</span>'
            );
        }
    });
}

// ============================================
// LOCATION QR PRINT BUTTON
// ============================================
$(document).on('click', '.btn-print-clocation', function (e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    var location = $(this).data('location') || '';
    if (!location) { alert('Aucune location pour cette annonce.'); return; }
    openPrintLabel(null, '', 1, location, 'yes');
});

// ============================================
// IMAGE THUMBNAIL HOVER POPUP
// ============================================
$(document).on('mouseenter', '.listing-thumb', function () {
    var src = $(this).attr('src');
    if (!src) return;
    $('#listing-img-preview img').attr('src', src);
    $('#listing-img-preview').stop(true, true).fadeIn(150);
});
$(document).on('mouseleave', '.listing-thumb', function () {
    $('#listing-img-preview').stop(true, true).fadeOut(100);
});
var printLabelButton = document.getElementById('btn-print-label');
if (printLabelButton) {
    // Impression à partir de la location
    printLabelButton.addEventListener('click', function () {
        var location = locationInputLabel ? locationInputLabel.value.trim() : '';
        if (location) {
            openPrintLabel(null, null, 1, location, 'yes');
        } else {
            alert(TEXT_ALERT_ENTER_LOCATION_PRINT);
        }
    });
}

// ============================================
// INITIALIZATION
// ============================================
$(document).ready(function() {
    // Initialize batch buttons on page load
    updateBatchButtons();

    // Initialize Bootstrap tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});

// Call also immediately in case DOM is already ready
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setTimeout(updateBatchButtons, 100);
}

// ============================================
// EBAY HEALTH CHECK
// ============================================
$(document).on('click', '#batch-health-check-btn', function () {
    var listingIds = [];
    $('.listing-checkbox:checked').each(function () {
        listingIds.push($(this).val());
    });
    if (listingIds.length === 0) return;

    var btn       = $(this);
    var userToken = getURLVar('user_token');
    var total     = listingIds.length;
    var okCount   = 0;
    var errCount  = 0;

    btn.prop('disabled', true);
    showLoadingPopup(
        (typeof TEXT_HEALTH_CHECK_RUNNING !== 'undefined' ? TEXT_HEALTH_CHECK_RUNNING : 'Vérification eBay')
        + ' — ' + total + ' listing' + (total > 1 ? 's' : '')
    );

    var ids = listingIds.slice();

    function checkNext() {
        if (ids.length === 0) {
            // ── Résumé final ──
            var summaryType = errCount > 0 ? 'error' : 'success';
            var summaryMsg  = (typeof TEXT_HEALTH_CHECK_COMPLETED !== 'undefined' ? TEXT_HEALTH_CHECK_COMPLETED : 'Vérification terminée')
                + ' — ✅ OK: ' + okCount
                + '  ❌ Erreurs: ' + errCount
                + '  📋 Total: ' + (okCount + errCount);
            finishLoadingPopup(summaryMsg);
            btn.prop('disabled', false);
            return;
        }

        var lid  = ids.shift();
        var done = total - ids.length;
        var $row = $('input.listing-checkbox[value="' + lid + '"]').closest('tr');
        var listingName = $row.find('td').eq(3).text().trim() || ('Listing #' + lid);

        appendLoadingMessage(
            '─── (' + done + '/' + total + ') <strong>#' + lid + '</strong> — ' + listingName + ' ───',
            'info'
        );

        $.ajax({
            url:      (typeof URL_CHECK_EBAY_HEALTH !== 'undefined' ? URL_CHECK_EBAY_HEALTH : ''),
            type:     'POST',
            dataType: 'json',
            timeout:  120000,
            data:     { 'selected[]': [lid], user_token: userToken },
            success: function (json) {
                if (json.success && json.results && json.results.length) {
                    var listingResult = json.results[0];
                    var aggStatus     = listingResult.health_status;

                    // ── Lignes détail par batch ──
                    if (listingResult.batches && listingResult.batches.length) {
                        listingResult.batches.forEach(function (b) {
                            var bName = b.batch_name ? b.batch_name : ('Batch #' + b.batch_id);
                            var bMsg, bType;
                            if (b.skipped) {
                                bMsg  = '&nbsp;&nbsp;&nbsp;&nbsp;⏭️ <em>' + bName + '</em> <span style="color:#888;">— non publié (pas de ebay_item_id)</span>';
                                bType = 'info';
                            } else if (b.status === 1) {
                                bMsg  = '&nbsp;&nbsp;&nbsp;&nbsp;✅ <em>' + bName + '</em> <span style="color:#28a745;">— OK</span>';
                                bType = 'success';
                            } else {
                                bMsg  = '&nbsp;&nbsp;&nbsp;&nbsp;❌ <em>' + bName + '</em>' + (b.error ? ' <span style="color:#dc3545;">— ' + b.error + '</span>' : '');
                                bType = 'error';
                            }
                            appendLoadingMessage(bMsg, bType);
                        });
                    } else {
                        appendLoadingMessage('&nbsp;&nbsp;&nbsp;&nbsp;⚠️ Aucun batch trouvé', 'warning');
                    }

                    // ── Mise à jour icône dans la table ──
                    var colour    = aggStatus === 1 ? '#28a745' : (aggStatus >= 2 ? '#dc3545' : '#aaaaaa');
                    var errors    = [];
                    if (listingResult.batches) {
                        listingResult.batches.forEach(function (b) { if (b.error) errors.push(b.error); });
                    }
                    var titleText = aggStatus === 1
                        ? (typeof TEXT_HEALTH_OK !== 'undefined' ? TEXT_HEALTH_OK : 'eBay OK')
                        : (errors.length ? errors.join(' | ') : (typeof TEXT_HEALTH_ERROR !== 'undefined' ? TEXT_HEALTH_ERROR : 'Erreur eBay'));
                    var $icon = $('.health-status-icon[data-listing-id="' + lid + '"]');
                    if (!$icon.length) $icon = $row.find('.health-status-icon');
                    $icon.find('i').css('color', colour);
                    $icon.attr('title', titleText);

                    // Ne comptabiliser que les batches non skippés
                    var hasNonSkipped = listingResult.batches && listingResult.batches.some(function(b) { return !b.skipped; });
                    if (hasNonSkipped) {
                        if (aggStatus === 1) okCount++; else errCount++;
                    }

                } else if (json.error) {
                    appendLoadingMessage('&nbsp;&nbsp;&nbsp;&nbsp;❌ ' + json.error, 'error');
                    errCount++;
                }
                checkNext();
            },
            error: function (xhr) {
                appendLoadingMessage(
                    '&nbsp;&nbsp;&nbsp;&nbsp;❌ HTTP ' + xhr.status + ' — ' +
                    (typeof TEXT_HEALTH_CHECK_FAILED !== 'undefined' ? TEXT_HEALTH_CHECK_FAILED : 'Échec'),
                    'error'
                );
                errCount++;
                checkNext();
            }
        });
    }

    checkNext();
});

function openPrintLabel(sku = '', upc = '', quantity = 1, location = '', force = 'no') {
    // user_token : depuis un input caché si présent (form), sinon depuis l'URL
    var userToken  = new URLSearchParams(window.location.search).get('user_token');

    // condition_id : seulement disponible dans le formulaire de détail
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
