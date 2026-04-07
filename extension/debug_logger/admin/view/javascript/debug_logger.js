/**
 * Debug Logger v1.4.0 — Admin JS
 * Reads config from window.DL_CONFIG set by inline Twig script block.
 */
(function () {
  'use strict';

  var cfg = window.DL_CONFIG || {};
  var _logs = [], _hasErr = false;

  /* ── Console capture ──────────────────────────────────────── */
  if (cfg.captureConsole) {
    var _oe = console.error.bind(console);
    var _ow = console.warn.bind(console);
    console.error = function () {
      _hasErr = true;
      try { _logs.push('[ERR] ' + Array.from(arguments).map(String).join(' ')); } catch (e) {}
      _oe.apply(console, arguments);
    };
    console.warn = function () {
      try { _logs.push('[WARN] ' + Array.from(arguments).map(String).join(' ')); } catch (e) {}
      _ow.apply(console, arguments);
    };
    window.addEventListener('error', function (e) {
      _hasErr = true;
      _logs.push('[JS] ' + e.message + ' (' + e.filename + ':' + e.lineno + ')');
    });
    window.addEventListener('unhandledrejection', function (e) {
      _logs.push('[PRO] ' + (e.reason && e.reason.message || String(e.reason)));
    });
  }

  /* ── Network capture ──────────────────────────────────────── */
  if (cfg.captureNetwork) {
    var _oFetch = window.fetch;
    window.fetch = function () {
      return _oFetch.apply(this, arguments).then(function (r) {
        if (!r.ok) _logs.push('[NET] ' + r.status + ' ' + r.url);
        return r;
      }).catch(function (e) {
        _logs.push('[NET] ' + e.message);
        throw e;
      });
    };
  }

  /* ── DOM ready ────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    var $ov     = $('#dl-overlay');
    var $modal  = $('#dl-modal');
    var $btn    = $('#btn-debug-logger');
    var $close  = $('#dl-modal-close');
    var $cancel = $('#dl-btn-cancel');
    var $save   = $('#dl-btn-save');
    var $toast  = $('#dl-toast');

    /* Show/hide severity options based on config */
    if (!cfg.severityBug)     $('#dl-severity option[value="bug"]').remove();
    if (!cfg.severityWarning) $('#dl-severity option[value="warning"]').remove();
    if (!cfg.severityInfo)    $('#dl-severity option[value="info"]').remove();

    if (cfg.requireComment) {
      $('#dl-comment').attr('placeholder', 'Comment required…');
      $('<span>', { text: ' *', style: 'color:#ef4444' }).appendTo($('#dl-comment').prev('label'));
    }

    function openModal() {
      $ov.show();
      $modal.show();
      $('#dl-url-display').text(window.location.href);
      $('#dl-console-display').text(_logs.join('\n'));
      $('#dl-count').text('(' + _logs.length + ')');
      $('#dl-comment').val('');
      $('#dl-severity').val(_hasErr ? 'bug' : $('#dl-severity option:first').val());
      $('body').css('overflow', 'hidden');
      setTimeout(function () { $('#dl-comment').focus(); }, 100);
    }

    function closeModal() {
      $ov.hide();
      $modal.hide();
      $('body').css('overflow', '');
    }

    function showToast(msg, isError) {
      $toast.text(msg).removeClass('error');
      if (isError) $toast.addClass('error');
      $toast.show();
      clearTimeout($toast.data('dl-t'));
      $toast.data('dl-t', setTimeout(function () { $toast.hide(); }, 3800));
    }

    $btn.on('click', function (e) { e.preventDefault(); openModal(); });
    $close.on('click', closeModal);
    $cancel.on('click', closeModal);
    $ov.on('click', closeModal);
    $(document).on('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    $save.on('click', function () {
      if (cfg.requireComment && !$('#dl-comment').val().trim()) {
        $('#dl-comment').focus();
        return;
      }
      $save.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

      var fd = new FormData();
      fd.append('url', window.location.href);
      fd.append('console_log', _logs.join('\n'));
      fd.append('comment', $('#dl-comment').val());
      fd.append('severity', $('#dl-severity').val());

      fetch(cfg.saveUrl, { method: 'POST', body: fd })
        .then(function (r) { return r.json(); })
        .then(function (j) {
          closeModal();
          if (j.success) showToast('Report #' + j.id + ' saved');
          else showToast(j.error || 'Error', true);
        })
        .catch(function (e) {
          closeModal();
          showToast(e.message, true);
        })
        .finally(function () {
          $save.prop('disabled', false).html('<i class="fa-solid fa-save"></i> Save');
        });
    });
  });
})();
