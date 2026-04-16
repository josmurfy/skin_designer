// Original: warehouse/card/import/sold.js
/**
 * card_import_sold.js
 * Autonomous JS — no shared dependencies (intentional duplication)
 * Handles: CSV upload → preview → save-to-DB, delete selected, truncate
 */

/* ===== Utility (duplicated for autonomy) ===== */
function htmlspecialchars(str) {
    if (typeof str !== 'string') return (str === null || str === undefined) ? '' : String(str);
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
    newOk.addEventListener('click', function() { modal.hide(); onConfirm(); });
    modal.show();
}

/* ===== State ===== */
var currentCards   = [];   // All rows from CSV (inc. deleted=true)
var previewVisible = false;

/* ===== Upload zone (drag & drop) ===== */
$(function() {

    var zone = document.getElementById('upload-zone');
    if (zone) {
        zone.addEventListener('dragover',  function(e) { e.preventDefault(); zone.classList.add('dragover'); });
        zone.addEventListener('dragleave', function()    { zone.classList.remove('dragover'); });
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            zone.classList.remove('dragover');
            var files = e.dataTransfer.files;
            if (files.length) {
                document.getElementById('input-file').files = files;
                document.getElementById('file-name-display').textContent = files[0].name;
            }
        });
    }

    $('#input-file').on('change', function() {
        var name = this.files.length ? this.files[0].name : '';
        document.getElementById('file-name-display').textContent = name;
    });

    /* ===== Upload form submit ===== */
    $('#form-upload').on('submit', function(e) {
        e.preventDefault();

        var fileInput = document.getElementById('input-file');
        if (!fileInput.files.length) {
            showAlert(TEXT_ERROR || 'Error', TEXT_NO_DATA || 'No file selected.');
            return;
        }

        var formData = new FormData();
        formData.append('file', fileInput.files[0]);

        $('#button-upload').prop('disabled', true);
        $('#upload-spinner').show();

        $.ajax({
            url:         URL_UPLOAD,
            type:        'POST',
            data:        formData,
            processData: false,
            contentType: false,
            success: function(json) {
                $('#button-upload').prop('disabled', false);
                $('#upload-spinner').hide();

                if (json.error) {
                    showAlert(TEXT_ERROR || 'Error', htmlspecialchars(json.error));
                    return;
                }

                // Store cards
                currentCards = json.cards || [];
                renderPreview(json.preview_html, json.total);
            },
            error: function(xhr) {
                $('#button-upload').prop('disabled', false);
                $('#upload-spinner').hide();
                showAlert(TEXT_ERROR || 'Error', (TEXT_AJAX_ERROR || 'AJAX error') + ' (' + xhr.status + ')');
            }
        });
    });

    /* ===== Render preview ===== */
    function renderPreview(html, total) {
        $('#preview-content').html(html);
        $('#preview-container').show();
        $('#button-save-to-db').show();
        previewVisible = true;

        // Count badge
        var $badge = $('#preview-count-badge');
        $badge.text(total + ' rows').show();

        // Count missing card numbers
        var missing = currentCards.filter(function(c) { return !c.card_number || c.card_number.trim() === ''; }).length;
        if (missing > 0) {
            $('#preview-missing-badge').text('⚠ ' + missing + ' missing card #').show();
            $('#preview-warning-missing').show();
        } else {
            $('#preview-missing-badge').hide();
            $('#preview-warning-missing').hide();
        }

        // Scroll to preview
        $('html, body').animate({ scrollTop: $('#preview-container').offset().top - 80 }, 300);

        // Bind editable field changes → sync to currentCards
        bindPreviewEdits();
    }

    /* ===== Sync editable inputs to currentCards ===== */
    function bindPreviewEdits() {
        // Non-card_number fields: sync on input
        var otherFields = ['grader','grade','price','currency','type_listing','bids','total_sold','status','date_sold'];
        otherFields.forEach(function(field) {
            $(document).on('change input', '.field-' + field, function() {
                var idx = parseInt($(this).data('index'), 10);
                var card = currentCards.find(function(c) { return c._index === idx; });
                if (card) card[field] = $(this).val();
            });
        });

        // card_number: smart propagation on change (blur/enter)
        $(document).on('change', '.field-card_number', function() {
            var idx     = parseInt($(this).data('index'), 10);
            var newNum  = $(this).val().trim();
            var card    = currentCards.find(function(c) { return c._index === idx; });
            if (!card) return;

            var wasEmpty = (!card.card_number || card.card_number.trim() === '');
            card.card_number = newNum;

            if (newNum !== '' && wasEmpty) {
                // --- Propagate card_number to all rows with same player AND missing card_number ---
                var playerName = (card.player || '').trim();
                // Remove trailing " #xxx" if player was already modified
                var baseName = playerName.replace(/\s+#\S+$/, '');

                currentCards.forEach(function(c) {
                    if (c._deleted) return;
                    var cBase = (c.player || '').trim().replace(/\s+#\S+$/, '');
                    if (cBase === baseName && (!c.card_number || c.card_number.trim() === '')) {
                        c.card_number = newNum;
                    }
                });

                // --- Append #number to title for all rows that got this card_number AND share baseName ---
                if (baseName !== '') {
                    currentCards.forEach(function(c) {
                        if (c._deleted) return;
                        var cBase = (c.player || '').trim().replace(/\s+#\S+$/, '');
                        if (cBase === baseName && (c.card_number || '').trim() === newNum) {
                            var titleBase = (c.title || '').trim().replace(/\s+#\S+$/, '');
                            c.title = titleBase + ' #' + newNum;
                        }
                    });
                }

                // --- Rebuild + reorder preview table ---
                rebuildPreviewTable();
            } else {
                // Simple visual feedback only
                var isEmpty = (newNum === '');
                var $row = $(this).closest('tr');
                if (isEmpty) {
                    $row.addClass('table-danger');
                    $(this).css({ 'border-color': '#dc3545', 'background': '#fff3cd' });
                } else {
                    $row.removeClass('table-danger');
                    $(this).css({ 'border-color': '', 'background': '' });
                }
                updateMissingBadge();
            }
        });
    }

    /* ===== Re-sort and rebuild preview table after card_number changes ===== */
    function rebuildPreviewTable() {
        var $tbody = $('#preview-table tbody');

        // Map index → existing TR
        var rowMap = {};
        $tbody.find('tr[data-index]').each(function() {
            rowMap[parseInt($(this).data('index'), 10)] = this;
        });

        // Sort active cards by card_number (natural, empty last)
        var activeCards = currentCards.filter(function(c) { return !c._deleted; });
        activeCards.sort(function(a, b) {
            var na = (a.card_number || '').trim();
            var nb = (b.card_number || '').trim();
            if (na === '' && nb === '') return 0;
            if (na === '') return 1;
            if (nb === '') return -1;
            return na.localeCompare(nb, undefined, { numeric: true, sensitivity: 'base' });
        });

        var TEXT_GROUP   = typeof TEXT_GROUP_LABEL   !== 'undefined' ? TEXT_GROUP_LABEL   : 'Group';
        var TEXT_MISSING = typeof TEXT_MISSING_CARD_NR !== 'undefined' ? TEXT_MISSING_CARD_NR : 'Missing card #';

        $tbody.empty();

        var lastCardNumber = '~~NONE~~';
        var groupNum = 0;

        activeCards.forEach(function(card) {
            var cardNum = (card.card_number || '').trim();

            if (cardNum !== lastCardNumber) {
                lastCardNumber = cardNum;
                groupNum++;
                var groupLabel = cardNum === ''
                    ? '<span class="text-danger fw-bold">⚠ ' + htmlspecialchars(TEXT_MISSING) + '</span>'
                    : '<span class="badge bg-secondary me-1"># ' + htmlspecialchars(cardNum) + '</span>';
                $tbody.append(
                    '<tr class="table-secondary" style="font-size:11px;">'
                    + '<td colspan="8" class="py-1 px-2">'
                    + '<i class="fa-solid fa-layer-group me-1 text-muted"></i>'
                    + htmlspecialchars(TEXT_GROUP) + ' ' + groupNum + ' — ' + groupLabel
                    + '</td></tr>'
                );
            }

            var $tr = $(rowMap[card._index]);
            if (!$tr.length) return;

            // Update card_number input
            var missing = (!cardNum);
            $tr.toggleClass('table-danger', missing);
            var $cnInput = $tr.find('.field-card_number');
            $cnInput.val(card.card_number || '');
            if (missing) {
                $cnInput.css({ 'border-color': '#dc3545', 'background': '#fff3cd' });
            } else {
                $cnInput.css({ 'border-color': '', 'background': '' });
            }

            // Update title cell
            $tr.find('.cell-title').text(card.title || '');

            $tbody.append($tr);
        });

        updateMissingBadge();
    }

    function updateMissingBadge() {
        var checked = getCheckedCards();
        var missing = checked.filter(function(c) { return !c.card_number || c.card_number.trim() === ''; }).length;
        if (missing > 0) {
            $('#preview-missing-badge').text('⚠ ' + missing + ' missing card #').show();
            $('#preview-warning-missing').show();
        } else {
            $('#preview-missing-badge').hide();
            $('#preview-warning-missing').hide();
        }
    }

    /* ===== Preview row: select-all ===== */
    $(document).on('change', '#preview-check-all', function() {
        $('.preview-row-check').prop('checked', $(this).is(':checked'));
    });

    /* ===== Preview row: delete ===== */
    /* ===== Search in preview eBay titles ===== */
    $('#button-preview-search').on('click', function() {
        var term = $('#preview-search-input').val().trim().toLowerCase();
        if (!term) {
            $('#preview-search-banner').hide();
            $('#button-preview-delete-found').hide();
            return;
        }
        var found = 0;
        // uncheck all first
        $('#preview-table thead #preview-check-all').prop('checked', false);
        $('#preview-table tbody tr[data-index]').each(function() {
            var $tr = $(this);
            var text = $tr.find('td:nth-child(4)').text().toLowerCase();
            var $cb = $tr.find('.preview-row-check');
            if (text.indexOf(term) !== -1) {
                $cb.prop('checked', true);
                $tr.addClass('table-info');
                found++;
            } else {
                $cb.prop('checked', false);
                $tr.removeClass('table-info');
            }
        });
        var banner = '#preview-search-banner';
        if (found > 0) {
            var msg = (TEXT_SEARCH_FOUND || '%d rows found').replace('%d', found)
                    + ' — "' + term + '"';
            $(banner).text(msg).show();
            $('#preview-delete-found-label').text(found + ' ×  ' + (typeof button_delete_found !== 'undefined' ? button_delete_found : 'Delete'));
            $('#button-preview-delete-found').show();
        } else {
            var noMsg = (TEXT_SEARCH_NONE || 'No rows found for "%s"').replace('%s', term);
            $(banner).text(noMsg).show();
            $('#button-preview-delete-found').hide();
        }
    });

    // Clear banner + highlights when input is cleared
    $('#preview-search-input').on('input', function() {
        if (!$(this).val()) {
            $('#preview-search-banner').hide();
            $('#button-preview-delete-found').hide();
            $('#preview-table tbody tr[data-index]').removeClass('table-info');
        }
    });

    // Also trigger search on Enter key
    $('#preview-search-input').on('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#button-preview-search').trigger('click'); }
    });

    // Bulk delete found rows in preview
    $('#button-preview-delete-found').on('click', function() {
        var term = $('#preview-search-input').val().trim();
        showConfirm(TEXT_ERROR || 'Confirm', (TEXT_DELETE_CONFIRM || 'Delete selected?'), function() {
            $('#preview-table tbody tr[data-index]').each(function() {
                var $tr = $(this);
                if ($tr.find('.preview-row-check').is(':checked')) {
                    var idx = parseInt($tr.data('index'), 10);
                    var card = currentCards.find(function(c) { return c._index === idx; });
                    if (card) card._deleted = true;
                    $tr.remove();
                }
            });
            // Clean orphan group headers
            $('#preview-table tbody tr.table-secondary').each(function() {
                var hasVisible = false;
                $(this).nextAll('tr').each(function() {
                    if ($(this).hasClass('table-secondary')) return false;
                    if (!$(this).hasClass('d-none')) { hasVisible = true; return false; }
                });
                if (!hasVisible) $(this).remove();
            });
            updateMissingBadge();
            $('#preview-search-banner').hide();
            $('#preview-search-input').val('');
            $('#button-preview-delete-found').hide();
        });
    });

    $(document).on('click', '.btn-preview-delete', function() {
        var $tr  = $(this).closest('tr');
        var idx  = parseInt($tr.data('index'), 10);

        // Mark deleted in currentCards
        var card = currentCards.find(function(c) { return c._index === idx; });
        if (card) card._deleted = true;

        // Check if separator row above is now orphan (no visible rows in group after this)
        var $prev = $tr.prev('tr.table-secondary');
        $tr.remove();

        // Hide orphan group headers (no data row siblings after them)
        $('#preview-table tbody tr.table-secondary').each(function() {
            var hasVisible = false;
            var $next = $(this).nextAll('tr');
            $next.each(function() {
                if ($(this).hasClass('table-secondary')) return false; // stop at next group header
                if (!$(this).hasClass('d-none')) { hasVisible = true; return false; }
            });
            if (!hasVisible) $(this).remove();
        });

        updateMissingBadge();
    });

    /* ===== Save checked preview rows to DB ===== */
    $('#button-save-to-db').on('click', function() {
        var rowsToSave = getCheckedCards();

        if (rowsToSave.length === 0) {
            showAlert(TEXT_ERROR || 'Error', TEXT_NO_DATA || 'No rows selected.');
            return;
        }

        $('#button-save-to-db').prop('disabled', true);
        $('#save-spinner').show();

        $.ajax({
            url:         URL_SAVE,
            type:        'POST',
            contentType: 'application/json',
            data:        JSON.stringify({ rows: rowsToSave }),
            success: function(json) {
                $('#button-save-to-db').prop('disabled', false);
                $('#save-spinner').hide();

                if (json.error) {
                    showAlert(TEXT_ERROR || 'Error', htmlspecialchars(json.error));
                    return;
                }

                // Show results modal
                document.getElementById('stat-total-file').textContent = json.total   || 0;
                document.getElementById('stat-inserted').textContent   = json.inserted || 0;
                document.getElementById('stat-skipped').textContent    = json.skipped  || 0;
                document.getElementById('stat-in-db').textContent      = json.inserted || 0;
                new bootstrap.Modal(document.getElementById('importResultsModal')).show();

                // Reload list
                if (typeof loadList === 'function') loadList('');

                // Reset preview
                $('#preview-container').hide();
                previewVisible = false;
                currentCards   = [];
                $('#preview-content').empty();
                $('#preview-count-badge').hide();
                $('#preview-missing-badge').hide();
                $('#preview-warning-missing').hide();
                $('#button-save-to-db').hide();
                document.getElementById('file-name-display').textContent = '';
            },
            error: function(xhr) {
                $('#button-save-to-db').prop('disabled', false);
                $('#save-spinner').hide();
                showAlert(TEXT_ERROR || 'Error', (TEXT_AJAX_ERROR || 'AJAX error') + ' (' + xhr.status + ')');
            }
        });
    });

    /* ===== Search in DB list titles ===== */
    $('#button-search-title').on('click', function() {
        var term = $('#search-title-input').val().trim().toLowerCase();
        if (!term) {
            $('#search-title-banner').hide();
            $('#button-delete-found').hide();
            return;
        }
        var found = 0;
        $('#sold-list input[name="selected[]"]').prop('checked', false);
        $('#sold-list tbody tr').each(function() {
            var $tr = $(this);
            var text = $tr.find('td').eq(3).text().toLowerCase();  // col 4 = title
            if (text.indexOf(term) !== -1) {
                $tr.find('input[name="selected[]"]').prop('checked', true);
                found++;
            }
        });
        if (found > 0) {
            var msg = (TEXT_SEARCH_FOUND || '%d rows found').replace('%d', found) + ' — "' + term + '"';
            $('#search-title-banner').text(msg).show();
            $('#button-delete-found').show();
        } else {
            var noMsg = (TEXT_SEARCH_NONE || 'No rows found.').replace('%s', term).replace('%d', 0);
            $('#search-title-banner').text(noMsg).show();
            $('#button-delete-found').hide();
        }
    });

    $('#search-title-input').on('keydown', function(e) {
        if (e.key === 'Enter') { e.preventDefault(); $('#button-search-title').trigger('click'); }
    });

    $('#search-title-input').on('input', function() {
        if (!$(this).val()) {
            $('#search-title-banner').hide();
            $('#button-delete-found').hide();
            $('#sold-list input[name="selected[]"]').prop('checked', false);
        }
    });

    $('#button-delete-found').on('click', function() {
        var selected = [];
        $('#sold-list input[name="selected[]"]:checked').each(function() {
            selected.push($(this).val());
        });
        if (!selected.length) return;
        showConfirm(TEXT_ERROR || 'Confirm', (TEXT_DELETE_CONFIRM || 'Delete selected?'), function() {
            $.ajax({
                url: URL_DELETE, type: 'POST',
                data: { selected: selected },
                success: function(json) {
                    if (json.error) { showAlert(TEXT_ERROR || 'Error', htmlspecialchars(json.error)); return; }
                    $('#search-title-banner').hide();
                    $('#search-title-input').val('');
                    $('#button-delete-found').hide();
                    if (typeof loadList === 'function') loadList('');
                },
                error: function(xhr) {
                    showAlert(TEXT_ERROR || 'Error', (TEXT_AJAX_ERROR || 'AJAX error') + ' (' + xhr.status + ')');
                }
            });
        });
    });

    /* ===== Delete selected (DB list) ===== */
    $('#button-delete-selected').on('click', function() {
        var selected = [];
        $('#sold-list input[name="selected[]"]:checked').each(function() {
            selected.push($(this).val());
        });

        if (selected.length === 0) {
            showAlert(TEXT_ERROR || 'Error', TEXT_NO_DATA || 'No records selected.');
            return;
        }

        showConfirm(TEXT_ERROR || 'Confirm', (TEXT_DELETE_CONFIRM || 'Delete selected?'), function() {
            $.ajax({
                url:  URL_DELETE,
                type: 'POST',
                data: { selected: selected },
                success: function(json) {
                    if (json.error) { showAlert(TEXT_ERROR || 'Error', htmlspecialchars(json.error)); return; }
                    if (typeof loadList === 'function') loadList('');
                },
                error: function(xhr) {
                    showAlert(TEXT_ERROR || 'Error', (TEXT_AJAX_ERROR || 'AJAX error') + ' (' + xhr.status + ')');
                }
            });
        });
    });

    /* ===== Truncate ===== */
    $('#button-truncate').on('click', function() {
        showConfirm('⚠ Warning', TEXT_TRUNCATE_CONFIRM || 'Delete ALL records?', function() {
            $.ajax({
                url:  URL_TRUNCATE,
                type: 'POST',
                success: function(json) {
                    if (json.error) { showAlert(TEXT_ERROR || 'Error', htmlspecialchars(json.error)); return; }
                    if (typeof loadList === 'function') loadList('');
                },
                error: function(xhr) {
                    showAlert(TEXT_ERROR || 'Error', (TEXT_AJAX_ERROR || 'AJAX error') + ' (' + xhr.status + ')');
                }
            });
        });
    });

});

/* ===== Collect checked preview rows (with latest field values) ===== */
function getCheckedCards() {
    var checkedIndexes = [];
    $('#preview-table tbody tr[data-index]').each(function() {
        var $tr = $(this);
        if ($tr.hasClass('table-secondary')) return; // skip group headers
        var $cb = $tr.find('.preview-row-check');
        if ($cb.is(':checked')) {
            checkedIndexes.push(parseInt($tr.data('index'), 10));
        }
    });

    // Build data from currentCards (already synced via bindPreviewEdits)
    return currentCards.filter(function(c) {
        return !c._deleted && checkedIndexes.indexOf(c._index) !== -1;
    });
}
