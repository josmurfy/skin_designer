// Original: warehouse/card/import/set.js
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
function cleanModalBackdrops() {
    document.querySelectorAll('.modal-backdrop').forEach(function (el) { el.remove(); });
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('padding-right');
    document.body.style.removeProperty('overflow');
}

function showAlert(title, message) {
    cleanModalBackdrops();
    document.getElementById('alert-modal-title').textContent = title;
    document.getElementById('alert-modal-body').innerHTML = message;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('alertModal')).show();
}

function showConfirm(title, message, onConfirm) {
    cleanModalBackdrops();
    document.getElementById('confirm-modal-title').textContent = title;
    document.getElementById('confirm-modal-body').textContent = message;
    var okBtn = document.getElementById('confirm-modal-ok');
    var newOk = okBtn.cloneNode(true);
    okBtn.parentNode.replaceChild(newOk, okBtn);
    var modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('confirmModal'));
    newOk.addEventListener('click', function () { modal.hide(); onConfirm(); });
    modal.show();
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
                bootstrap.Modal.getOrCreateInstance(document.getElementById('importResultsModal')).show();

                currentCards = null;
                $btn.hide();
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
