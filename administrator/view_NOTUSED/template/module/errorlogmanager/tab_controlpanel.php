<div class="text-center hidden" id="initBox" style="font-size: 32px">
    Initializing...<i class="fa fa-spinner fa-spin"></i>
</div>
<div class="text-center hidden" id="updateBox" style="font-size: 32px">
    Catching up with the latest errors :) Please wait...&nbsp;(<span id="updatePercent">0</span>%)
</div>
<div class="text-center hidden" id="loadingBox" style="font-size: 32px">
    <i class="fa fa-spinner fa-spin"></i>
</div>
<div class="hidden" id="listBox">
</div>
<script>
var hash_for_quote = '';
var em_filters = {};
var em_page = 1;
var em_file = $('#option-file').val();
var em_extension = '';
em_filters.file = em_file;
em_filters.sort_order = $('#option-sort').val();

//Step 2/3
function get_errors(filters, page) {
    filters = filters || {};
    page = page || 1;

    $.ajax({
        url: '<?php echo html_entity_decode($getURL); ?>',
        type: 'GET',
        dataType: 'html',
        data: {
            filters: JSON.stringify(filters),
            page: page
        },
        beforeSend: function() {
            $('#listBox').addClass('hidden');
            $('#loadingBox').removeClass('hidden');
        },
        success: function(resp) {
            $('#listBox').html(resp).removeClass('hidden');
        },
        complete: function() {
            $('#loadingBox').addClass('hidden');
        }
    });
}

//Step 2
function update_errors(startByte) {
    startByte = startByte || 0;

    $.ajax({
        url: '<?php echo html_entity_decode($updateURL); ?>',
        type: 'POST',
        data: {start_byte: startByte, file: em_file},
        dataType: 'json',
        beforeSend: function() {
            $('#updateBox').removeClass('hidden');
        },
        success: function(resp) {
            if (!resp.ready) {
                update_errors(resp.cur_pos);
            } else {
                $('#updateBox').addClass('hidden');
                get_errors(em_filters, em_page);
            }
            $('#updatePercent').text(parseInt((resp.cur_pos/resp.total)*100));
        }
    });
}

function clear_error(msg_hash, startByte) {
    msg_hash = msg_hash || 0;
    startByte = startByte || 0;

    $.ajax({
        url: '<?php echo html_entity_decode($clearErrorURL); ?>',
        type: 'POST',
        data: {
            file: em_file,
            msg_hash: (msg_hash.constructor == Array) ? msg_hash[0] : msg_hash
        },
        dataType: 'json',
        success: function(resp) {
            if (!resp.ready) {
                clear_error(msg_hash, resp.cur_pos);
            } else {
                if (msg_hash.constructor == Array) {
                    msg_hash.shift();
                    if (msg_hash.length) {
                        clear_error(msg_hash, resp.cur_pos);
                    } else {
                        get_errors(em_filters, em_page);
                    }
                } else {
                    get_errors(em_filters, em_page);
                }
            }
        }
    });
}

function refresh_db_entries() {
    $.ajax({
        url: '<?php echo html_entity_decode($refreshURL); ?>',
        type: 'POST',
        data: {file: em_file},
        success: function() {
            $('#listBox').html('');
            update_errors();
        }
    });
}

function init() {
    $.ajax({
        url: '<?php echo html_entity_decode($initURL); ?>',
        type: 'GET',
        dataType: 'json',
        data: {file: em_file},
        beforeSend: function() {
            $('#listBox').addClass('hidden');
            $('#initBox').removeClass('hidden');
        },
        success: function(resp) {
            if (!resp.isUpdated) {
                update_errors();
            } else {
                get_errors(em_filters, em_page);
            }
        },
        complete: function() {
            $('#initBox').addClass('hidden');
        }
    });
}

//Step 1
init();

$('#listBox').on('click', 'button.code-preview', function(e) {
    e.preventDefault();
    var hash = $(this).attr('data-message-hash');
    $('#code-preview-' + hash).toggleClass('hidden');
    return false;
});

$('#listBox').on('click', 'button.request-quote', function(e) {
    e.preventDefault();
    hash_for_quote = $(this).attr('data-message-hash');
    $('.modal-main-button').removeAttr('disabled');
    $('#modal-body').removeClass('hidden');
    $('.modal-main-button').removeClass('hidden');
    $('#requestQuoteModal .init-hidden').addClass('hidden');
    $('#requestQuoteModal').modal('show');
    return false;
});

$('#listBox').on('click', 'button.clear-error', function(e) {
    e.preventDefault();
    if (!$(this).hasClass("disabled") && confirm('Are you sure you want to permanently delete this error message?')) {
        $(this).addClass("disabled").find('i').removeClass('fa-trash').addClass('fa-spinner fa-spin');
        var hash = $(this).attr('data-message-hash');
        $(this).find('i').removeClass('fa-trash').addClass('fa-spinner fa-spin');
        clear_error(hash);
    }
    return false;
});

function requestQuote(btn) {
    if ($(btn).attr('disabled')) return;
    $(btn).attr('disabled', true);

    if (!document.getElementById('quoteForm').checkValidity()) {
        $('#modal-alert-danger').text('Please fill in the contact information fields below').removeClass('hidden');
        return;
    }

    $('#modal-alert-success').addClass('hidden');
    $('#modal-alert-danger').addClass('hidden');

    var admin_mail = $('#requestQuoteModal').find('input[name="admin_mail"]').val();
    var admin_name = $('#requestQuoteModal').find('input[name="admin_name"]').val();
    var msg = '';

    $.ajax({
        url: '<?php echo html_entity_decode($getMsgURL); ?>',
        type: 'GET',
        dataType: 'text',
        data: {file: em_file, hash: hash_for_quote},
        success: function(resp) {
            msg = resp;
            getQuote(admin_mail, admin_name, msg);
        }
    });
}

function getQuote(admin_mail, admin_name, msg) {
    $.ajax({
        url: '//isenselabs.com/apis/request_quote',
        type: 'GET',
        dataType: 'json',
        data: {from: admin_mail, name: admin_name, msg: msg},
        beforeSend: function() {
            $('.modalSpinner').removeClass('hidden');
        },
        success: function(resp) {
            $('#btn-modal-get-quote').addClass('hidden');
            $('#modal-body').addClass('hidden');

            if (!resp.fail) {
                $('#btn-modal-purchase').removeClass('hidden');
                $('.modal-main-button').addClass('hidden');
                $('#modal-on-quote').html('The price for fixing this error is <b>$' + resp.amount + '</b> with a turnaround time of ' + resp.turnaround_time + ' business days').removeClass('hidden');
                $('#btn-modal-purchase').attr('data-href', resp.link);
            } else {
                $('#modal-on-fail').text(resp.message).removeClass('hidden');
                $('#btn-modal-mail').attr('data-mail', admin_mail).attr('data-name', admin_name).removeClass('hidden');
            }
        },
        complete: function() {
            $('.modalSpinner').addClass('hidden');
        }
    });
}

function goToPage(btn) {
    var url = $(btn).attr('data-href');
    window.open(url, '_blank');
}

function requestQuoteMail(btn) {
    if ($(btn).attr('disabled')) return;
    $(btn).attr('disabled', true);

    var admin_mail = $(btn).attr('data-mail');
    var admin_name = $(btn).attr('data-name');

    $.ajax({
        url: '<?php echo html_entity_decode($requestQuoteURL); ?>',
        type: 'POST',
        dataType: 'json',
        data: {file: em_file, hash: hash_for_quote, from: admin_mail, name: admin_name},
        beforeSend: function() {
            $('.modalSpinner').removeClass('hidden');
        },
        success: function(resp) {
            if (resp.success) {
                $('#modal-body').addClass('hidden');
                $('#modal-on-fail').addClass('hidden');
                $('.modal-main-button').addClass('hidden');
                $('#btn-modal-thanks').removeClass('hidden');
                $('#modal-alert-success').text(resp.message).removeClass('hidden');
                $('#btn-modal-mail').addClass('hidden');
            } else {
                $('#modal-alert-danger').text(resp.message).removeClass('hidden');
            }
        },
        complete: function() {
            $('.modalSpinner').addClass('hidden');
        }
    });
}

function search() {
    applyFilters();
}

function applyFilters() {
    em_filters.from = $('#filter-from').val();
    em_filters.to = $('#filter-to').val();
    em_filters.file = em_file;
    em_filters.extension = em_extension;
    em_filters.search = $('#searchInput').val();
    em_filters.sort_order = $('#option-sort').val();
    em_page = 1;
    init();
}

function gotoPage(page) {
    page = page||1;
    if (!parseInt(page)) page = 1;//this is to handle the link to page 1 where the page variable comes in as the string "{page}"

    em_page = parseInt(page);
    get_errors(em_filters, em_page);
}

$('#listBox').on('click', 'button.recently-changed', function(e) {
    e.preventDefault();
    var hash = $(this).attr('data-message-hash');
    var div = $('#recently-changed-' + hash);

    if (div.hasClass('hidden')) {
      $.ajax({
        url: '<?php echo html_entity_decode($recentlyChangedURL); ?>',
        type: 'GET',
        dataType: 'html',
        data: {file: em_file, hash: hash},
        beforeSend: function() {
          div.html('<div class="text-center" style="font-size: 32px;"><i class="fa fa-spinner fa-spin"></i>');
          div.removeClass('hidden');
        },
        success: function(resp) {
          div.html(resp);
        }
      });
    } else {
      div.addClass('hidden');
    }

    return false;
});

$('#listBox').on('click', '.btn-clear-selected', function(e) {
    e.preventDefault();
    if (!$(this).hasClass("disabled") && confirm('Are you sure you want to permanently delete the selected error messages?')) {
        var selected = $('.error-checkbox:checked');
        if (selected.size()) {
            $(this).addClass("disabled").find('i').removeClass('fa-trash').addClass('fa-spinner fa-spin');
            var hashes = [];
            for(var i = 0; i < selected.size(); i++) {
                hashes.push($(selected[i]).val());
            }
            clear_error(hashes);
        }
    }
});

$('#listBox').on('dblclick', '.error-checkbox', function(e) {
    if ($('.error-checkbox:not(:checked)').size()) {
        $('.error-checkbox:not(:checked)').prop("checked", true);
    } else {
        $('.error-checkbox:checked').prop("checked", false);
    }
});

$('#listBox').on('change', '#pagination-page', function(e) {
    e.preventDefault();
    gotoPage(parseInt($(this).val()));
    return false;
});

$('#option-file').on('change', function() {
    em_file = this.value;
    applyFilters();
});

$('#option-extension').on('change', function() {
    em_extension = this.value;
});

$('#btnFiltersApply').on('click', function(e) {
    e.preventDefault();
    applyFilters();
});

$('#btnSearch').on('click', search);
$('#searchInput').on('keyup', function(e) {
    if(e.keyCode == 13) {
        search();
    }
});
</script>
