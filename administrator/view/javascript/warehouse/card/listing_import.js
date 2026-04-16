// Original: warehouse/card/listing_import.js
/**
 * card_listing_import.js
 * Handles the Import CSV and Regenerate tab in card_listing_form
 * Autonomous file - no shared dependencies
 */

// ── Utility helpers (duplicated intentionally — autonomous file) ───────────
function htmlspecialchars(str) {
    if (typeof str !== 'string') return str;
    return str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
}
/** Simple sprintf: replaces first %d or %s with the value */
function sprintf(fmt, val) {
    return (fmt || '').replace(/%[ds]/, val);
}
// ───────────────────────────────────────────────────────────────────────────
$(document).ready(function () {

    var pendingGroups = null; // Store groups from preview for confirm

    // ── Make Bootstrap tab work for the tab-import panel ───────────────
    $('a[href="#tab-import"]').on('click', function () {
        $('.tab-pane').removeClass('active show');
        $('#tab-import').addClass('active show');
    });
    // Also handle other tabs to hide import panel
    $('a[data-bs-toggle="tab"]').not('[href="#tab-import"]').on('click', function () {
        $('#tab-import').removeClass('active show');
    });

    // ── IMPORT: Preview ────────────────────────────────────────────────
    $('#btn-import-preview').on('click', function () {
        var file = $('#import-csv-file')[0].files[0];
        if (!file) {
            alert('Please select a CSV file first.');
            return;
        }

        pendingGroups = null;
        $('#btn-import-confirm').hide();
        $('#btn-import-cancel').hide();
        $('#import-preview-panel').hide();
        showImportStatus('Parsing CSV...');

        var formData = new FormData();
        formData.append('file', file);
        formData.append('listing_id', LISTING_ID);

        $.ajax({
            url: URL_IMPORT_CSV,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function (json) {
                hideImportStatus();
                if (json.error) {
                    alert('Error: ' + json.error);
                    return;
                }

                pendingGroups = json.groups;

                var stats = json.total_cards + ' cards in ' + json.total_groups + ' groups';
                $('#import-preview-stats').text(stats);
                $('#import-preview-html').html(json.html);
                $('#import-preview-panel').show();
                $('#btn-import-confirm').show();
                $('#btn-import-cancel').show();
            },
            error: function (xhr) {
                hideImportStatus();
                alert('AJAX error: ' + xhr.status + ' ' + xhr.statusText);
            }
        });
    });

    // ── IMPORT: Cancel ─────────────────────────────────────────────────
    $('#btn-import-cancel').on('click', function () {
        pendingGroups = null;
        $('#import-preview-panel').hide();
        $('#btn-import-confirm').hide();
        $('#btn-import-cancel').hide();
        $('#import-csv-file').val('');
    });

    // ── IMPORT: Confirm ────────────────────────────────────────────────
    $('#btn-import-confirm').on('click', function () {
        if (!pendingGroups) {
            alert('No pending import. Please preview first.');
            return;
        }

        if (!confirm('Add all these cards to listing #' + LISTING_ID + '?')) {
            return;
        }

        showImportStatus('Saving cards to database...');
        $('#btn-import-confirm').prop('disabled', true);

        $.ajax({
            url: URL_CONFIRM_IMPORT,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ listing_id: LISTING_ID, groups: pendingGroups }),
            dataType: 'json',
            success: function (json) {
                hideImportStatus();
                $('#btn-import-confirm').prop('disabled', false);
                if (json.error) {
                    alert('Error: ' + json.error);
                    return;
                }
                alert(json.message + '\nPage will reload to show updated cards.');
                location.reload();
            },
            error: function (xhr) {
                hideImportStatus();
                $('#btn-import-confirm').prop('disabled', false);
                alert('AJAX error: ' + xhr.status + ' ' + xhr.statusText);
            }
        });
    });

    // ── REGENERATE: State & helpers ────────────────────────────────────
    var MERGE_PRICE_THRESHOLD  = 3.00;
    var MERGE_SPREAD_THRESHOLD = 0.50;
    var regenPlan = null; // { p1: {key->group}, p2: {key->group} } — null until preview

    /**
     * Populate regenPlan from a server-side JSON response (getRegenPreview endpoint).
     * All cards are analysed in SQL — not limited by the page's 150-card DOM limit.
     */
    function applyServerRegenPlan(json) {
        regenPlan = { p1: {}, p2: {}, p3: {}, p4: {} };

        $.each(json.p1, function (i, g) {
            var key = (g.card_number + '|' + g.player.toLowerCase()).trim();
            g.accepted = true;
            regenPlan.p1[key] = g;
        });

        $.each(json.p2, function (i, g) {
            var key = g.base + '|' + g.player.toLowerCase().replace(/[\s,]+[a-z]{2,5}(,[\s]*[a-z]{2,5})*\s*$/i, '').trim();
            g.accepted = !!g.eligible;  // green = auto-accepted; orange = off by default
            regenPlan.p2[key] = g;
        });

        $.each(json.p3, function (i, g) {
            var key = g.base + '|' + g.player.toLowerCase().replace(/[\s,]+[a-z]{2,5}(,[\s]*[a-z]{2,5})*\s*$/i, '').trim();
            g.accepted = true;
            regenPlan.p3[key] = g;
        });

        $.each(json.p4 || [], function (i, g) {
            var key = g.card_number.toLowerCase();
            g.accepted    = true;
            g.survivor_id = g.candidates[0].card_id;  // default: first candidate
            regenPlan.p4[key] = g;
        });
    }

    /** Count how many groups / cards are currently accepted in the plan */
    function countAccepted() {
        var merges = 0, mergeCards = 0, renames = 0;
        $.each(regenPlan.p1, function (k, g) {
            if (g.count > 1 && g.accepted) { merges++; mergeCards += g.count; }
        });
        $.each(regenPlan.p2, function (k, g) {
            if (g.card_numbers.length > 1 && g.accepted) { merges++; mergeCards += g.card_numbers.length; }
        });
        $.each(regenPlan.p3, function (k, g) {
            if (g.accepted) renames++;
        });
        $.each(regenPlan.p4, function (k, g) {
            if (g.accepted && g.candidates.length > 1) { merges++; mergeCards += g.candidates.length; }
        });
        return { merges: merges, mergeCards: mergeCards, renames: renames };
    }

    /**
     * Render the interactive preview inside #regen-preview-panel.
     * Called on first preview AND every time a group is added/removed.
     */
    function renderRegenPreview() {
        if (!regenPlan) { return; }

        var $panel   = $('#regen-preview-panel');
        var dupeKeys = Object.keys(regenPlan.p1).filter(function (k) { return regenPlan.p1[k].count > 1; });
        var p2Keys   = Object.keys(regenPlan.p2).filter(function (k) { return regenPlan.p2[k].card_numbers.length > 1 && regenPlan.p2[k].hasLetter; });
        var p3Keys   = Object.keys(regenPlan.p3);

        var html = '';

        // ── Pass 1 ────────────────────────────────────────────────────
        html += '<h6 class="mt-2 mb-1">Pass 1 — Exact duplicates <span class="badge bg-secondary">' + dupeKeys.length + '</span></h6>';
        if (dupeKeys.length === 0) {
            html += '<div class="text-muted small mb-2">No exact duplicates found.</div>';
        } else {
            html += '<table class="table table-sm table-bordered mb-2">';
            html += '<thead><tr><th>#</th><th>Player</th><th>Qty</th><th>Min $</th><th>Max $</th><th>Dupes</th><th></th></tr></thead><tbody>';
            $.each(regenPlan.p1, function (key, g) {
                if (g.count < 2) return;
                var rowClass = g.accepted ? 'table-warning' : 'table-light';
                var btn = g.accepted
                    ? '<button class="btn btn-sm btn-danger py-0 px-1 btn-remove-p1" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem" title="Remove from plan">\u274C</button>'
                    : '<button class="btn btn-sm btn-outline-success py-0 px-1 btn-restore-p1" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem" title="Add back">+ Add</button>';
                var minP = (g.min_price !== undefined ? g.min_price : g.price);
                html += '<tr class="' + rowClass + '">'
                      + '<td>' + htmlspecialchars(g.card_number) + '</td>'
                      + '<td>' + htmlspecialchars(g.player) + '</td>'
                      + '<td>' + g.qty + '</td>'
                      + '<td>$' + minP.toFixed(2) + '</td>'
                      + '<td>$' + g.price.toFixed(2) + '</td>'
                      + '<td><span class="badge bg-warning text-dark">' + g.count + ' \u2192 1</span></td>'
                      + '<td>' + btn + '</td></tr>';
            });
            html += '</tbody></table>';
        }

        // ── Pass 2 ────────────────────────────────────────────────────
        if (p2Keys.length > 0) {
            html += '<h6 class="mt-2 mb-1">Pass 2 — Letter variants <span class="badge bg-secondary">' + p2Keys.length + '</span></h6>';
            html += '<div class="d-flex gap-3 mb-1 small">'
                  + '<span class="badge bg-success">&nbsp;</span> ' + LEGEND_REGEN_GREEN + ' &nbsp;&nbsp;'
                  + '<span class="badge bg-warning text-dark">&nbsp;</span> ' + LEGEND_REGEN_ORANGE
                  + '</div>';
            html += '<table class="table table-sm table-bordered mb-2">';
            html += '<thead><tr><th>Base</th><th>Player</th><th>Variants</th><th>Min $</th><th>Max $</th><th>Spread</th><th></th></tr></thead><tbody>';
            $.each(regenPlan.p2, function (key, g) {
                if (g.card_numbers.length < 2 || !g.hasLetter) return;
                var rowClass = g.eligible ? (g.accepted ? 'table-success' : 'table-light') : (g.accepted ? 'table-warning' : 'table-light');
                var btn;
                if (g.accepted) {
                    btn = '<button class="btn btn-sm btn-danger py-0 px-1 btn-remove-p2" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem" title="Remove from plan">\u274C</button>';
                } else if (!g.eligible) {
                    btn = '<button class="btn btn-sm btn-outline-warning py-0 px-1 btn-restore-p2" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem" title="Force merge despite price/spread">' + BTN_MERGE_ANYWAY + '</button>';
                } else {
                    btn = '<button class="btn btn-sm btn-outline-success py-0 px-1 btn-restore-p2" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem" title="Add back to plan">+ Add</button>';
                }
                html += '<tr class="' + rowClass + '">'
                      + '<td>' + htmlspecialchars(g.base) + '</td>'
                      + '<td>' + htmlspecialchars(g.player) + '</td>'
                      + '<td>' + g.card_numbers.map(function (n) { return htmlspecialchars(n); }).join(', ') + '</td>'
                      + '<td>$' + g.minPrice.toFixed(2) + '</td>'
                      + '<td>$' + g.maxPrice.toFixed(2) + '</td>'
                      + '<td>' + Math.round(g.spread * 100) + '%</td>'
                      + '<td>' + btn + '</td></tr>';
            });
            html += '</tbody></table>';
        }

        // ── Pass 3: Orphan letter variants ────────────────────────────
        if (p3Keys.length > 0) {
            html += '<h6 class="mt-2 mb-1">Pass 3 \u2014 Orphan letter variants <span class="badge bg-info text-dark">' + p3Keys.length + '</span></h6>';
            html += '<div class="small text-muted mb-1">Cartes sans jumeau \u2014 la lettre sera supprim\u00e9e du num\u00e9ro.</div>';
            html += '<table class="table table-sm table-bordered mb-2">';
            html += '<thead><tr><th>#</th><th>\u2192</th><th>Player</th><th>Price</th><th></th></tr></thead><tbody>';
            $.each(regenPlan.p3, function (key, g) {
                var rowClass = g.accepted ? 'table-info' : 'table-light';
                var btn = g.accepted
                    ? '<button class="btn btn-sm btn-danger py-0 px-1 btn-remove-p3" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem" title="Ne pas renommer">\u274C</button>'
                    : '<button class="btn btn-sm btn-outline-info py-0 px-1 btn-restore-p3" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem" title="Ajouter au plan">+ Add</button>';
                html += '<tr class="' + rowClass + '">'
                      + '<td><s>' + htmlspecialchars(g.card_number) + '</s></td>'
                      + '<td><strong>' + htmlspecialchars(g.base) + '</strong></td>'
                      + '<td>' + htmlspecialchars(g.player) + '</td>'
                      + '<td>$' + g.price.toFixed(2) + '</td>'
                      + '<td>' + btn + '</td></tr>';
            });
            html += '</tbody></table>';
        }

        // ── Pass 4: Same card_number, different player name ────────────
        var p4Keys = Object.keys(regenPlan.p4);
        if (p4Keys.length > 0) {
            html += '<h6 class="mt-2 mb-1">Pass 4 \u2014 M\u00eame num\u00e9ro, nom diff\u00e9rent <span class="badge bg-primary">' + p4Keys.length + '</span></h6>';
            html += '<div class="small text-muted mb-1">Cartes avec le m\u00eame # mais player diff\u00e9rent \u2014 choisissez le titre \u00e0 garder.</div>';
            html += '<table class="table table-sm table-bordered mb-2">';
            html += '<thead><tr><th>#</th><th>Candidats (choisir le titre)</th><th></th></tr></thead><tbody>';
            $.each(regenPlan.p4, function (key, g) {
                var rowClass = g.accepted ? 'table-primary' : 'table-light';
                var candidateHtml = '';
                $.each(g.candidates, function (ci, c) {
                    var checked = (g.survivor_id === c.card_id) ? 'checked' : '';
                    candidateHtml += '<label class="me-3 d-block">'
                        + '<input type="radio" name="p4-title-' + htmlspecialchars(key) + '" '
                        + 'value="' + c.card_id + '" ' + checked + ' '
                        + 'class="btn-p4-pick me-1" data-key="' + htmlspecialchars(key) + '"> '
                        + htmlspecialchars(c.player) + ' <span class="text-muted small">$' + c.price.toFixed(2) + '</span>'
                        + '</label>';
                });
                var btn = g.accepted
                    ? '<button class="btn btn-sm btn-danger py-0 px-1 btn-remove-p4" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem" title="Ignorer">\u274C</button>'
                    : '<button class="btn btn-sm btn-outline-primary py-0 px-1 btn-restore-p4" data-key="' + htmlspecialchars(key) + '" style="font-size:0.73rem">+ Add</button>';
                html += '<tr class="' + rowClass + '">'
                      + '<td><strong>' + htmlspecialchars(g.card_number) + '</strong></td>'
                      + '<td>' + candidateHtml + '</td>'
                      + '<td>' + btn + '</td></tr>';
            });
            html += '</tbody></table>';
        }

        // ── Counter + Process Merge button ────────────────────────────
        var counts     = countAccepted();
        var totalActs  = counts.merges + counts.renames;
        var alertClass = totalActs > 0 ? 'alert-success' : 'alert-secondary';
        var summary    = [];
        if (counts.merges)  summary.push('<strong>' + counts.merges + ' merge(s)</strong> (' + (counts.mergeCards - counts.merges) + ' supprim\u00e9es)');
        if (counts.renames) summary.push('<strong>' + counts.renames + ' rename(s)</strong> (lettre retir\u00e9e)');
        html += '<div class="alert ' + alertClass + ' d-flex align-items-center gap-3 mt-2 mb-0">';
        html += '<span>' + (summary.length ? summary.join(' &mdash; ') : 'Rien de s\u00e9lectionn\u00e9') + '</span>';
        if (totalActs > 0) {
            html += ' <button type="button" id="btn-process-merge" class="btn btn-success btn-sm ms-auto">'
                  + '\u26A1 Process (' + totalActs + ' action' + (totalActs > 1 ? 's' : '') + ')</button>';
        }
        html += '</div>';

        $panel.html(html).show();

        // ── Bind toggle buttons ───────────────────────────────────────
        $panel.find('.btn-remove-p1').on('click', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p1[key]) { regenPlan.p1[key].accepted = false; renderRegenPreview(); }
        });
        $panel.find('.btn-restore-p1').on('click', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p1[key]) { regenPlan.p1[key].accepted = true; renderRegenPreview(); }
        });
        $panel.find('.btn-remove-p2').on('click', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p2[key]) { regenPlan.p2[key].accepted = false; renderRegenPreview(); }
        });
        $panel.find('.btn-restore-p2').on('click', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p2[key]) { regenPlan.p2[key].accepted = true; renderRegenPreview(); }
        });
        $panel.find('.btn-remove-p3').on('click', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p3[key]) { regenPlan.p3[key].accepted = false; renderRegenPreview(); }
        });
        $panel.find('.btn-restore-p3').on('click', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p3[key]) { regenPlan.p3[key].accepted = true; renderRegenPreview(); }
        });
        $panel.find('.btn-remove-p4').on('click', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p4[key]) { regenPlan.p4[key].accepted = false; renderRegenPreview(); }
        });
        $panel.find('.btn-restore-p4').on('click', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p4[key]) { regenPlan.p4[key].accepted = true; renderRegenPreview(); }
        });
        $panel.find('.btn-p4-pick').on('change', function () {
            var key = String($(this).data('key'));
            if (regenPlan.p4[key]) { regenPlan.p4[key].survivor_id = parseInt($(this).val(), 10); }
        });

        // ── Process Merge click ───────────────────────────────────────
        $panel.find('#btn-process-merge').on('click', function () {
            var groups = [], orphan_ids = [], p4_groups = [];
            $.each(regenPlan.p1, function (k, g) {
                if (g.count > 1 && g.accepted) groups.push(g.card_ids);
            });
            $.each(regenPlan.p2, function (k, g) {
                if (g.card_numbers.length > 1 && g.accepted) groups.push(g.card_ids);
            });
            $.each(regenPlan.p3, function (k, g) {
                if (g.accepted) orphan_ids.push(g.card_id);
            });
            $.each(regenPlan.p4, function (k, g) {
                if (g.accepted && g.candidates.length > 1) {
                    p4_groups.push({
                        card_ids:    g.candidates.map(function (c) { return c.card_id; }),
                        survivor_id: g.survivor_id
                    });
                }
            });
            if (!groups.length && !orphan_ids.length && !p4_groups.length) { alert('Rien de s\u00e9lectionn\u00e9.'); return; }
            var desc = [];
            if (groups.length)     desc.push(groups.length + ' merge(s)');
            if (orphan_ids.length) desc.push(orphan_ids.length + ' rename(s)');
            if (p4_groups.length)  desc.push(p4_groups.length + ' merge(s) nom diff\u00e9rent');
            if (!confirm(desc.join(' + ') + ' ? Cette action est irr\u00e9versible.')) return;

            showRegenStatus('Traitement en cours...');
            $('#btn-process-merge').prop('disabled', true);

            $.ajax({
                url:         URL_PROCESS_MERGE_GROUPS,
                type:        'POST',
                contentType: 'application/json',
                data:        JSON.stringify({ listing_id: LISTING_ID, groups: groups, orphan_ids: orphan_ids, p4_groups: p4_groups }),
                dataType:    'json',
                success: function (json) {
                    hideRegenStatus();
                    if (json.error) {
                        alert('Error: ' + json.error);
                        $('#btn-process-merge').prop('disabled', false);
                        return;
                    }
                    alert('Done! ' + json.message + '\n\nPage will reload.');
                    location.reload();
                },
                error: function (xhr) {
                    hideRegenStatus();
                    $('#btn-process-merge').prop('disabled', false);
                    alert('AJAX error: ' + xhr.status + ' ' + xhr.statusText);
                }
            });
        });
    }

    // ── REGENERATE: Preview (interactive) — server-side AJAX fetch ──────────
    $('#btn-regen-preview').on('click', function () {
        if (!LISTING_ID) {
            $('#regen-preview-panel').html('<div class="alert alert-warning">No listing selected.</div>').show();
            return;
        }
        showRegenStatus('Analyse en cours\u2026');
        $('#regen-preview-panel').hide();
        $.ajax({
            url:      URL_REGEN_PREVIEW,
            type:     'GET',
            data:     { listing_id: LISTING_ID },
            dataType: 'json',
            success: function (json) {
                hideRegenStatus();
                if (json.error) {
                    $('#regen-preview-panel').html('<div class="alert alert-danger">' + htmlspecialchars(json.error) + '</div>').show();
                    return;
                }
                applyServerRegenPlan(json);
                renderRegenPreview();
            },
            error: function (xhr) {
                hideRegenStatus();
                $('#regen-preview-panel').html('<div class="alert alert-danger">AJAX error ' + xhr.status + ': ' + htmlspecialchars(xhr.statusText) + '</div>').show();
            }
        });
    });

    // ── REGENERATE: Same as Preview (btn-regenerate = alias de btn-regen-preview)
    $('#btn-regenerate').on('click', function () {
        $('#btn-regen-preview').trigger('click');
    });

    // ── Helpers ────────────────────────────────────────────────────────
    function showImportStatus(msg) {
        $('#import-status-text').text(msg);
        $('#import-status').show();
    }
    function hideImportStatus() {
        $('#import-status').hide();
    }
    function showRegenStatus(msg) {
        $('#regen-status-text').text(msg);
        $('#regen-status').show();
    }
    function hideRegenStatus() {
        $('#regen-status').hide();
    }
});
