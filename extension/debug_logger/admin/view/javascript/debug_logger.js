/**
 * Debug Logger v2.0.0 Pro — Admin JS
 * Modal content built from DL_CONFIG.i18n (multilingual, server-side).
 */
(function () {
  'use strict';

  var cfg  = window.DL_CONFIG || {};
  var i18n = cfg.i18n || {};
  var _logs = [], _netLogs = [], _hasErr = false;

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
        if (!r.ok) { _netLogs.push('[NET] ' + r.status + ' ' + r.url); }
        return r;
      }).catch(function (e) {
        _netLogs.push('[NET-ERR] ' + e.message);
        throw e;
      });
    };
  }

  /* ── Escape helper (used in save restore) ────────────────── */
  function esc(s) {
    return String(s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  /* ── DOM ready ────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {

    var $ov    = $('#dl-overlay');
    var $modal = $('#dl-modal');
    var $btn   = $('#btn-debug-logger');
    var $toast = $('#dl-toast');

    function openModal() {
      $ov.show();
      $modal.show();
      $('#dl-url-display').text(window.location.href);
      var allLogs = _logs.concat(_netLogs);
      $('#dl-console-display').text(allLogs.join('\n'));
      $('#dl-count').text('(' + allLogs.length + ')');
      $('#dl-comment').val('');
      var $sev = $('#dl-severity');
      $sev.val(_hasErr && $sev.find('option[value="bug"]').length ? 'bug' : $sev.find('option:first').val());
      $('body').css('overflow', 'hidden');

      // Screenshot capture (Pro)
      $('#dl-screenshot-data').val('');
      $('#dl-screenshot-preview').empty();
      if (cfg.captureScreenshot && typeof html2canvas === 'function') {
        var $ssField = $('#dl-screenshot-field');
        $ssField.show();
        // Hide modal temporarily for clean screenshot
        $modal.css('visibility', 'hidden');
        $ov.css('visibility', 'hidden');
        setTimeout(function () {
          html2canvas(document.body, { useCORS: true, scale: 0.5, logging: false }).then(function (canvas) {
            var dataUrl = canvas.toDataURL('image/jpeg', 0.6);
            if (dataUrl && dataUrl.length < 2097152) {
              $('#dl-screenshot-data').val(dataUrl);
              $ssField.find('#dl-screenshot-preview').html(
                '<img src="' + dataUrl + '" style="max-width:100%;max-height:120px;border-radius:4px;border:1px solid #334155">'
              );
            }
            $modal.css('visibility', '');
            $ov.css('visibility', '');
          }).catch(function () {
            $modal.css('visibility', '');
            $ov.css('visibility', '');
          });
        }, 100);
      }

      setTimeout(function () { $('#dl-comment').focus(); }, 300);
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

    $(document).on('click', '#dl-modal-close, #dl-btn-cancel', closeModal);
    $ov.on('click', closeModal);
    $(document).on('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    $(document).on('click', '#dl-btn-save', function () {
      if (cfg.requireComment && !$('#dl-comment').val().trim()) {
        $('#dl-comment').focus();
        return;
      }
      var $save = $('#dl-btn-save');
      $save.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i>');

      var fd = new FormData();
      fd.append('url',         window.location.href);
      fd.append('console_log', _logs.join('\n'));
      fd.append('network_log', _netLogs.join('\n'));
      fd.append('comment',     $('#dl-comment').val());
      fd.append('severity',    $('#dl-severity').val());
      var ssData = $('#dl-screenshot-data').val();
      if (ssData) fd.append('screenshot', ssData);

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
          $save.prop('disabled', false)
               .html('<i class="fa-solid fa-save"></i> ' + esc(i18n.btnSave || 'Save'));
        });
    });
  });
})();
