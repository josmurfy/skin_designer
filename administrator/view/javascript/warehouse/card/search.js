// Original: shopmanager/card/search.js
/**
 * Card Value Search — search.js
 *
 * Autonomous JS (décentralisé) pour shopmanager/card/search
 * Gère: cascading select filters, autoload search, pagination AJAX, tri colonnes
 */

// ─── Duplicated utilities (production safety over DRY) ──────────────
function htmlspecialchars(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}

function htmlspecialchars_decode(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/&amp;/g, '&').replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&quot;/g, '"').replace(/&#039;/g, "'");
}

// ─── Autoload (debounced) search ────────────────────────────────────
var _searchTimer = null;
var _searchDelay = 400; // ms
var _cascadeInProgress = false;

function triggerSearch() {
    clearTimeout(_searchTimer);
    _searchTimer = setTimeout(function () {
        doSearch();
    }, _searchDelay);
}

function doSearch() {
    var url = buildFilterUrl();
    var $list = $('#search-list');

    $list.html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i><br>' + (typeof TEXT_LOADING !== 'undefined' ? TEXT_LOADING : 'Loading...') + '</div>');

    $list.load(url, function () {
        $('html, body').animate({ scrollTop: $list.offset().top - 100 }, 200);
    });
}

function buildFilterUrl() {
    var url = (typeof URL_SEARCH_LIST !== 'undefined') ? URL_SEARCH_LIST : '';
    var params = {};

    var category = $('#input-category').val();
    if (category) params['filter_category'] = category;

    var year = $('#input-year').val();
    if (year) params['filter_year'] = year;

    var brand = $('#input-brand').val();
    if (brand) params['filter_brand'] = brand;

    var setName = $('#input-set').val();
    if (setName) params['filter_set'] = setName;

    var player = $('#input-player').val();
    if (player) params['filter_player'] = player;

    var minPrice = $('#input-min-price').val();
    if (minPrice) params['filter_min_price'] = minPrice;

    var maxPrice = $('#input-max-price').val();
    if (maxPrice) params['filter_max_price'] = maxPrice;

    var limit = $('#input-limit').val();
    if (limit) params['limit'] = limit;

    for (var key in params) {
        if (params.hasOwnProperty(key)) {
            url += '&' + key + '=' + encodeURIComponent(params[key]);
        }
    }

    return url;
}

// ─── Cascading dropdown refresh ─────────────────────────────────────
function getFilterValues() {
    return {
        filter_category: $('#input-category').val() || '',
        filter_year:     $('#input-year').val()     || '',
        filter_brand:    $('#input-brand').val()     || '',
        filter_set:      $('#input-set').val()       || '',
        filter_player:   $('#input-player').val()    || ''
    };
}

function refreshCascadeDropdowns() {
    if (_cascadeInProgress) return;
    _cascadeInProgress = true;

    var filters = getFilterValues();
    var url = (typeof URL_FILTER_OPTIONS !== 'undefined') ? URL_FILTER_OPTIONS : '';
    var textAll = (typeof TEXT_ALL !== 'undefined') ? TEXT_ALL : '-- All --';

    $.getJSON(url, filters, function (json) {
        var selectMap = {
            'category': '#input-category',
            'year':     '#input-year',
            'brand':    '#input-brand',
            'set_name': '#input-set',
            'player':   '#input-player'
        };

        for (var field in selectMap) {
            if (!selectMap.hasOwnProperty(field) || !json[field]) continue;

            var $sel = $(selectMap[field]);
            var currentVal = $sel.val() || '';

            $sel.empty();
            $sel.append('<option value="">' + htmlspecialchars(textAll) + '</option>');

            for (var i = 0; i < json[field].length; i++) {
                var val = json[field][i];
                var selected = (val == currentVal) ? ' selected' : '';
                $sel.append('<option value="' + htmlspecialchars(val) + '"' + selected + '>' + htmlspecialchars(val) + '</option>');
            }

            // If current value no longer exists in new options, reset it
            if (currentVal && $sel.val() !== currentVal) {
                $sel.val('');
            }
        }
    }).always(function () {
        _cascadeInProgress = false;
    });
}

// ─── Document Ready ─────────────────────────────────────────────────
$(document).ready(function () {

    // ── Cascade filters: on any select change, refresh all dropdowns + search ──
    $(document).on('change', '.cascade-filter', function () {
        refreshCascadeDropdowns();
        triggerSearch();
    });

    // Per-page limit: search immediately
    $('#input-limit').on('change', function () {
        triggerSearch();
    });

    // Price inputs: debounced search
    $('#input-min-price, #input-max-price').on('input', function () {
        triggerSearch();
    });

    // Filter button (explicit)
    $('#button-filter').on('click', function () {
        doSearch();
    });

    // Reset all filters
    $('#button-reset-filters').on('click', function () {
        $('#input-category').val('');
        $('#input-year').val('');
        $('#input-brand').val('');
        $('#input-set').val('');
        $('#input-player').val('');
        $('#input-min-price').val('');
        $('#input-max-price').val('');
        $('#input-limit').val('50');

        // Refresh dropdown options with no filters
        refreshCascadeDropdowns();

        // Clear results
        $('#search-list').html('<div class="text-center text-muted py-5"><i class="fa-solid fa-magnifying-glass fa-3x mb-3"></i></div>');
    });

    // ── AJAX navigation: sort headers + pagination inside #search-list ──
    $('#search-list').on('click', 'thead a, .pagination a', function (e) {
        e.preventDefault();
        var url = this.href;
        var $list = $('#search-list');

        $list.html('<div class="text-center py-4"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');

        $list.load(url, function () {
            $('html, body').animate({ scrollTop: $list.offset().top - 100 }, 200);
        });
    });

    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (el) {
        return new bootstrap.Tooltip(el);
    });
});
