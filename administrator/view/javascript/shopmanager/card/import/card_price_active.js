/**
 * card_price_active.js - Active Card Prices module
 * Standalone (no shared dependency)
 */

// --- Category -> eBay search term mapping ---------------------------------- //

var CPA_CATEGORY_EBAY_TERM = {
    'HOCKEY':     'ice hockey',
    'BASKETBALL': 'basketball',
    'BASEBALL':   'baseball',
    'FOOTBALL':   'football',
    'SOCCER':     'soccer',
    'TENNIS':     'tennis',
    'GOLF':       'golf',
};

// --- Sport filter: show only sets of the selected category ---------------- //

function cpaFilterSetsByCategory(category) {
    var sel = document.getElementById('fetch-card-select');
    var opts = sel.querySelectorAll('option[data-category]');
    opts.forEach(function(opt) {
        if (!category || opt.getAttribute('data-category') === category) {
            opt.style.display = '';
        } else {
            opt.style.display = 'none';
        }
    });
    sel.value = '';
    document.getElementById('fetch-keyword').value = '';
}

// --- Fill keyword from card SET select ------------------------------------ //

function cpaFillKeywordFromSet(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;

    var year     = opt.getAttribute('data-year')     || '';
    var brand    = opt.getAttribute('data-brand')    || '';
    var set      = opt.getAttribute('data-set')      || '';
    var category = opt.getAttribute('data-category') || '';

    // Build keyword: "year brand set_name ice hockey"
    // Skip set_name if it already contains both year and brand (avoids "1991 O-Pee-Chee 1991 O-Pee-Chee")
    var parts = [];
    var setLower   = set.toLowerCase();
    var yearLower  = year.toLowerCase();
    var brandLower = brand.toLowerCase();
    var setRedundant = set === brand
        || (yearLower && setLower.indexOf(yearLower) !== -1 && brandLower && setLower.indexOf(brandLower) !== -1);

    if (year)  parts.push(year);
    if (brand) parts.push(brand);
    if (set && !setRedundant) parts.push(set);

    // Append eBay-specific sport term if mapped
    var ebayTerm = CPA_CATEGORY_EBAY_TERM[category.toUpperCase()] || '';
    if (ebayTerm) parts.push(ebayTerm);

    document.getElementById('fetch-keyword').value = parts.join(' ');

    // Also sync the sport select if not already set
    var sportSel = document.getElementById('fetch-sport-select');
    if (sportSel && category && !sportSel.value) {
        sportSel.value = category;
    }
}

// --- Fetch from eBay (paginated with progress popup) ---------------------- //

function cpaFetchFromEbay() {
    var keyword = document.getElementById('fetch-keyword').value.trim();
    if (!keyword) {
        cpaShowAlert('warning', 'Please enter a keyword.');
        return;
    }

    var site_id   = document.getElementById('fetch-site').value;
    var condition = document.getElementById('fetch-condition').value;
    var sportSel  = document.getElementById('fetch-sport-select');
    var sport     = sportSel ? sportSel.value : '';
    var btn       = document.getElementById('btn-fetch');

    btn.disabled = true;
    cpaHideAlert();

    // condition='all' → deux buckets (graded puis raw) = 2x10k résultats
    var buckets      = (condition === 'all') ? ['graded', 'raw'] : [condition];
    var bucketIndex  = 0;

    var totalFetched  = 0;
    var totalInserted = 0;
    var ebayTotal     = 0; // total du bucket courant

    // Show progress modal
    var modal = document.getElementById('cpa-fetch-modal');
    var bsModal = new bootstrap.Modal(modal, {backdrop: 'static', keyboard: false});
    bsModal.show();

    function setBar(pct, cls) {
        var bar = document.getElementById('cpa-fetch-bar');
        bar.style.width = pct + '%';
        bar.textContent = pct + '%';
        if (cls) {
            bar.className = 'progress-bar progress-bar-striped progress-bar-animated ' + cls;
        }
    }

    function setStatus(line1, line2) {
        document.getElementById('cpa-fetch-status').textContent = line1;
        if (line2 !== undefined) document.getElementById('cpa-fetch-detail').textContent = line2;
    }

    function setStep(html) {
        document.getElementById('cpa-fetch-step').innerHTML = html;
    }

    // ---- Fetch une page d'un bucket ------------------------------------
    function fetchPage(bucket, page, bucketTotal, bucketPages) {
        var bucketLabel = bucket === 'graded' ? 'Graded' : 'Raw';
        var stepLabel   = 'Step ' + (bucketIndex + 1) + '/' + (buckets.length + 1);
        var pct = bucketPages > 1 ? Math.round(((page - 1) / bucketPages) * 100) : 0;
        setBar(pct, 'bg-primary');
        setStatus(
            stepLabel + ' — ' + bucketLabel + ' page ' + page + (bucketPages > 1 ? ' / ' + bucketPages : '') + ' — ' + totalFetched + ' items fetched...'
        );
        setStep('<i class="fa-solid fa-circle-notch fa-spin me-1"></i>' + stepLabel + ': Fetching ' + bucketLabel + ' listings...');

        $.ajax({
            url:  ROUTE_FETCH,
            type: 'POST',
            data: {
                keyword:          keyword,
                site_id:          site_id,
                condition_type:   condition,
                condition_bucket: bucket,
                sport:            sport,
                page:             page
            },
            dataType: 'json',
            success: function(json) {
                if (!json.success) {
                    if (json.debug_raw) { console.warn('[fetchPage] debug_raw:', json.debug_raw); }
                    finishWithError(json.message || TEXT_NO_RESULTS);
                    return;
                }

                totalFetched  += json.fetched   || 0;
                totalInserted += json.inserted  || 0;
                var curTotal   = json.total      || 0;
                var curPages   = json.total_pages || 1;

                var apiMax = curPages * 200;
                var detail = totalFetched.toLocaleString() + ' fetched';
                if (curTotal > apiMax) {
                    detail += ' (' + bucketLabel + ' eBay total: ' + curTotal.toLocaleString() + ', API max: ' + apiMax.toLocaleString() + ')';
                } else {
                    detail += ' / ' + curTotal.toLocaleString() + ' ' + bucketLabel;
                }
                document.getElementById('cpa-fetch-detail').textContent = detail;

                if (json.completed) {
                    // Bucket terminé → passe au suivant ou au match
                    bucketIndex++;
                    if (bucketIndex < buckets.length) {
                        // Lance le bucket suivant
                        fetchPage(buckets[bucketIndex], 1, 0, 1);
                    } else {
                        // Tous les buckets OK → match
                        startMatch();
                    }
                } else {
                    fetchPage(bucket, page + 1, curTotal, curPages);
                }
            },
            error: function(xhr) {
                finishWithError('Server error: ' + xhr.statusText);
            }
        });
    }

    // ---- Step final : match -------------------------------------------
    function startMatch() {
        var stepLabel = 'Step ' + (buckets.length + 1) + '/' + (buckets.length + 1);
        setBar(100, 'bg-warning');
        setStatus('Matching cards against card set...', totalFetched.toLocaleString() + ' items in buffer');
        setStep('<i class="fa-solid fa-circle-notch fa-spin me-1"></i>' + stepLabel + ': Matching against card set...');

        $.ajax({
            url:      ROUTE_PROCESS,
            type:     'POST',
            dataType: 'json',
            success: function(json) {
                bsModal.hide();
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-download me-1"></i>Fetch';

                if (json.success) {
                    cpaShowAlert('success',
                        totalFetched.toLocaleString() + ' fetched (' + buckets.join('+') + ') — ' +
                        '<strong class="text-success">' + json.matched + '</strong> matched, ' +
                        '<strong class="text-danger">' + json.deleted + '</strong> deleted from buffer.'
                    );
                    cpaLoadRawStats();
                    if (json.matched > 0) {
                        setTimeout(function() { location.reload(); }, 1500);
                    }
                } else {
                    cpaShowAlert('warning', json.warning || 'Match step failed.');
                }
            },
            error: function(xhr) {
                bsModal.hide();
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-download me-1"></i>Fetch';
                cpaShowAlert('danger', 'Match error: ' + xhr.statusText);
            }
        });
    }

    function finishWithError(msg) {
        bsModal.hide();
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-download me-1"></i>Fetch';
        cpaShowAlert('warning', msg);
    }

    // Reset et démarrage
    setBar(0, 'bg-primary');
    setStatus('Connecting to eBay...', '');
    setStep('<i class="fa-solid fa-circle-notch fa-spin me-1"></i>Step 1/' + (buckets.length + 1) + ': Fetching ' + (buckets[0] === 'graded' ? 'Graded' : 'Raw') + ' listings...');
    fetchPage(buckets[0], 1, 0, 1);
}

// cpaProcessRaw() removed — matching is triggered automatically after fetch

// --- Search in eBay titles + bulk delete ---------------------------------- //

$(function() {

    // Check-all toggle
    $(document).on('change', '#cpa-check-all', function() {
        $('.cpa-row-check').prop('checked', $(this).is(':checked'));
    });

    // Search button
    $('#cpa-btn-search').on('click', function() {
        var term = $('#cpa-search-input').val().trim().toLowerCase();
        if (!term) {
            $('#cpa-search-banner').hide();
            $('#cpa-btn-delete-found').hide();
            return;
        }
        var found = 0;
        $('.cpa-row-check').prop('checked', false);
        $('#cpa-check-all').prop('checked', false);
        $('table tbody tr[id^="row-"]').each(function() {
            var $tr = $(this);
            var text = $tr.find('td').eq(2).text().toLowerCase(); // col 3 = title
            if (text.indexOf(term) !== -1) {
                $tr.find('.cpa-row-check').prop('checked', true);
                found++;
            }
        });
        var banner = '#cpa-search-banner';
        if (found > 0) {
            var msg = (TEXT_SEARCH_FOUND || '%d rows matched').replace('%d', found) + ' — "' + term + '"';
            $(banner).text(msg).show();
            $('#cpa-btn-delete-found').show();
        } else {
            $(banner).text(TEXT_SEARCH_NONE || 'No title contains that word.').show();
            $('#cpa-btn-delete-found').hide();
        }
    });

    // Enter key triggers search
    $('#cpa-search-input').on('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#cpa-btn-search').trigger('click'); }
    });

    // Clear banner when input emptied
    $('#cpa-search-input').on('input', function() {
        if (!$(this).val()) {
            $('#cpa-search-banner').hide();
            $('#cpa-btn-delete-found').hide();
            $('.cpa-row-check').prop('checked', false);
        }
    });

    // Bulk delete found
    $('#cpa-btn-delete-found').on('click', function() {
        var selected = [];
        $('.cpa-row-check:checked').each(function() {
            selected.push($(this).val());
        });
        if (!selected.length) return;
        if (!confirm(TEXT_CONFIRM_DELETE || 'Delete selected?')) return;
        $.ajax({
            url:      ROUTE_DELETE,
            type:     'POST',
            data:     { selected: selected },
            dataType: 'json',
            success: function(json) {
                if (json.error) { alert(json.error); return; }
                selected.forEach(function(id) {
                    var row = document.getElementById('row-' + id);
                    if (row) row.remove();
                });
                $('#cpa-search-banner').hide();
                $('#cpa-search-input').val('');
                $('#cpa-btn-delete-found').hide();
            }
        });
    });

});

// --- Delete active record ------------------------------------------------- //

function cpaDeleteRecord(activeId) {
    if (!confirm(TEXT_CONFIRM_DELETE)) return;

    $.ajax({
        url:      ROUTE_DELETE,
        type:     'POST',
        data:     { active_id: activeId },
        dataType: 'json',
        success: function(json) {
            if (json.success) {
                var row = document.getElementById('row-' + activeId);
                if (row) row.remove();
            }
        }
    });
}

// --- Load raw stats via fetch then update bar ----------------------------- //

function cpaLoadRawStats() {
    $.ajax({
        url:      ROUTE_PROCESS.replace('processRaw', 'rawStats') + '&only_stats=1',
        type:     'GET',
        dataType: 'json',
        success: function(json) {
            if (json.stats) cpaUpdateStatsBar(json.stats);
        }
    });
    // Always show bar after fetch
    document.getElementById('raw-stats-bar').style.display = '';
}

function cpaUpdateStatsBar(stats) {
    if (!stats) return;
    var bar = document.getElementById('raw-stats-bar');
    var txt = document.getElementById('raw-stats-text');
    bar.style.display = '';
    txt.textContent = 'Raw buffer: ' + (stats.total || 0) + ' total / ' +
        (stats.matched || 0) + ' matched / ' +
        (stats.rejected || 0) + ' rejected / ' +
        (stats.pending || 0) + ' pending';
}

// --- Image zoom ----------------------------------------------------------- //

function cpaZoomImage(src) {
    var popup = document.getElementById('cpa-img-popup');
    document.getElementById('cpa-img-popup-src').src = src;
    popup.style.display = 'flex';
}

// --- Alert helpers --------------------------------------------------------- //

function cpaShowAlert(type, msg) {
    var el = document.getElementById('fetch-alert');
    el.innerHTML = '<div class="alert alert-' + type + ' mb-0">' + msg + '</div>';
    el.style.display = '';
}

function cpaHideAlert() {
    var el = document.getElementById('fetch-alert');
    el.style.display = 'none';
    el.innerHTML = '';
}
