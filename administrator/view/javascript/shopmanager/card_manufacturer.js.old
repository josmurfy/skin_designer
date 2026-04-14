/**
 * card_manufacturer.js
 * Autonomous JS for Card Manufacturer CRUD module (ShopManager / PhoenixLiquidation)
 * No external shared dependencies - all utilities duplicated intentionally.
 */

'use strict';

// -----------------------------------------------------------------------
// Utility functions (duplicated intentionally - see copilot-instructions.md)
// -----------------------------------------------------------------------
function htmlspecialchars(str) {
    if (typeof str !== 'string') return str;
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function htmlspecialchars_decode(str) {
    if (typeof str !== 'string') return str;
    return str
        .replace(/&amp;/g, '&')
        .replace(/&lt;/g, '<')
        .replace(/&gt;/g, '>')
        .replace(/&quot;/g, '"')
        .replace(/&#039;/g, "'");
}

function ucwords(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
}

function getQueryParam(name) {
    var match = RegExp('[?&]' + name + '=([^&]*)').exec(window.location.search);
    return match ? decodeURIComponent(match[1].replace(/\+/g, ' ')) : '';
}

function updateQueryParam(url, key, value) {
    var re = new RegExp('([?&])' + key + '=.*?(&|$)', 'i');
    var separator = url.indexOf('?') !== -1 ? '&' : '?';
    if (url.match(re)) {
        return url.replace(re, '$1' + key + '=' + encodeURIComponent(value) + '$2');
    }
    return url + separator + key + '=' + encodeURIComponent(value);
}

// -----------------------------------------------------------------------
// List page – select all checkboxes
// -----------------------------------------------------------------------
function cardManufacturerSelectAll(source) {
    var checkboxes = document.querySelectorAll('#form-card-manufacturer input[name="selected[]"]');
    checkboxes.forEach(function(cb) {
        cb.checked = source.checked;
    });
}

// -----------------------------------------------------------------------
// List page – delete handler
// -----------------------------------------------------------------------
function cardManufacturerDelete(urlDelete, confirmMsg) {
    var selected = document.querySelectorAll('#form-card-manufacturer input[name="selected[]"]:checked');
    if (selected.length === 0) {
        alert(TEXT_SELECT_ONE_MFR);
        return;
    }
    if (confirm(confirmMsg || TEXT_CONFIRM_DELETE_MFR)) {
        var form = document.getElementById('form-card-manufacturer');
        form.method = 'post';
        form.action = urlDelete;
        form.submit();
    }
}

// -----------------------------------------------------------------------
// List page – filter
// -----------------------------------------------------------------------
function cardManufacturerFilter() {
    var name   = document.getElementById('input-filter-name')   ? document.getElementById('input-filter-name').value   : '';
    var status = document.getElementById('input-filter-status') ? document.getElementById('input-filter-status').value : '';
    var url    = window.location.pathname + window.location.search;

    url = updateQueryParam(url, 'filter_name',   name);
    url = updateQueryParam(url, 'filter_status', status);
    url = updateQueryParam(url, 'page',          1);
    window.location.href = url;
}

function cardManufacturerResetFilter(userToken) {
    window.location.href = window.location.pathname + '?route=shopmanager/card_manufacturer&user_token=' + encodeURIComponent(userToken || getQueryParam('user_token'));
}

// -----------------------------------------------------------------------
// Form page – client-side validation
// -----------------------------------------------------------------------
function cardManufacturerValidateForm(errorMsg) {
    var nameInput = document.getElementById('input-name');
    if (!nameInput) return true;

    var name = nameInput.value.trim();
    if (!name || name.length > 100) {
        nameInput.closest('.form-group').classList.add('has-error');
        nameInput.focus();
        alert(errorMsg || TEXT_NAME_LENGTH_ERROR);
        return false;
    }

    nameInput.closest('.form-group').classList.remove('has-error');
    return true;
}

// -----------------------------------------------------------------------
// AJAX search (for use in other forms - e.g. select2 autocomplete)
// Usage:  cardManufacturerSearch(query, userToken, baseUrl, callback)
// -----------------------------------------------------------------------
function cardManufacturerSearch(query, userToken, baseUrl, callback) {
    var url = baseUrl + 'index.php?route=shopmanager/card_manufacturer.search&user_token=' + encodeURIComponent(userToken) + '&filter_name=' + encodeURIComponent(query);

    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var data = JSON.parse(xhr.responseText);
                callback(null, data);
            } catch (e) {
                callback('JSON parse error', []);
            }
        } else {
            callback('HTTP ' + xhr.status, []);
        }
    };
    xhr.onerror = function() {
        callback('Network error', []);
    };
    xhr.send();
}
