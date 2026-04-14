/**
 * card_listing_tab_descriptions.js
 * Handles the #tab-descriptions panel: Summernote WYSIWYG initialization
 */

(function ($) {
    'use strict';

    $(document).ready(function () {
        initSummernote();
        initBatchSpecifics();
    });

    /* ── Batch Specifics — Add / Remove / Save ──────────────────────── */
    function initBatchSpecifics() {

        // Add empty row
        $(document).on('click', '.btn-add-specific-row', function () {
            var $tbody = $(this).closest('table').find('tbody');
            $tbody.find('.specifics-empty-row').remove();
            $tbody.append(
                '<tr>' +
                '<td><input type="text" class="form-control form-control-sm specific-name" placeholder="Name"/></td>' +
                '<td><input type="text" class="form-control form-control-sm specific-value" placeholder="Value(s)"/></td>' +
                '<td><button type="button" class="btn btn-sm btn-outline-danger btn-remove-specific" title="Remove"><i class="fa fa-times"></i></button></td>' +
                '</tr>'
            );
        });

        // Remove row
        $(document).on('click', '.btn-remove-specific', function () {
            var $tbody = $(this).closest('tbody');
            $(this).closest('tr').remove();
            if ($tbody.find('tr').length === 0) {
                $tbody.append(
                    '<tr class="specifics-empty-row">' +
                    '<td colspan="3" class="text-muted fst-italic text-center small py-2">No specifics.</td>' +
                    '</tr>'
                );
            }
        });

        // Save specifics for a batch
        $(document).on('click', '.btn-save-batch-specifics', function () {
            var $btn       = $(this);
            var $table     = $btn.closest('table');
            var listingId  = $btn.data('listing-id');
            var batchId    = $btn.data('batch-id');
            var $feedback  = $table.find('.specifics-save-feedback');
            var specifics  = [];

            $table.find('tbody tr:not(.specifics-empty-row)').each(function () {
                var name  = $(this).find('.specific-name').val().trim();
                var value = $(this).find('.specific-value').val().trim();
                if (name) {
                    specifics.push({ name: name, value: value });
                }
            });

            $btn.prop('disabled', true);
            $.ajax({
                url:      URL_SAVE_BATCH_SPECIFICS,
                type:     'POST',
                dataType: 'json',
                data:     { listing_id: listingId, batch_id: batchId, specifics: specifics },
                success: function (r) {
                    $btn.prop('disabled', false);
                    if (r && r.success) {
                        $feedback.removeClass('d-none')
                            .delay(2500)
                            .fadeOut(400, function () { $(this).addClass('d-none').show(); });
                    } else {
                        alert('Error: ' + (r && r.error ? r.error : 'Unknown error'));
                    }
                },
                error: function () {
                    $btn.prop('disabled', false);
                    alert('AJAX error while saving specifics.');
                }
            });
        });
    }

    function initSummernote() {
        if (typeof $.fn.summernote !== 'function') {
            console.warn('Summernote not loaded. Description fields will remain as plain textareas.');
            return;
        }

        $('.summernote').summernote({
            height: 300,
            toolbar: [
                ['style',    ['style']],
                ['font',     ['bold', 'italic', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color',    ['color']],
                ['para',     ['ul', 'ol', 'paragraph']],
                ['table',    ['table']],
                ['insert',   ['link', 'picture', 'video']],
                ['view',     ['fullscreen', 'codeview', 'help']]
            ]
        });
    }

})(window.jQuery || window.$);
