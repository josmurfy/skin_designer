/**
 * Debug Logger v1.4.0 — Catalog JS
 * Reads config from window.DL_CONFIG set by inline Twig script block.
 */
(function () {
  'use strict';

  var cfg = window.DL_CONFIG || {};
  var i18n = cfg.i18n || {};
  var _logs = [], _hasErr = false;

  /* ── Console capture ──────────────────────────────────────── */
  if (cfg.captureConsole) {
    var _oe = console.error.bind(console);
    console.error = function () {
      _hasErr = true;
      try { _logs.push('[ERR] ' + Array.from(arguments).map(String).join(' ')); } catch (e) {}
      _oe.apply(console, arguments);
    };
    window.addEventListener('error', function (e) {
      _hasErr = true;
      _logs.push('[JS] ' + e.message + ' (' + e.filename + ':' + e.lineno + ')');
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
  /* ── Collect all files loaded on the page ──────────────── */
  function collectLoadedFiles() {
    var files = [];
    if (cfg.serverFiles && cfg.serverFiles.length) {
      cfg.serverFiles.forEach(function(f) { files.push(f); });
    }
    document.querySelectorAll('script[src]').forEach(function(s) {
      var src = s.getAttribute('src');
      if (src && src.indexOf('debug_logger') === -1) files.push(src);
    });
    document.querySelectorAll('link[rel="stylesheet"][href]').forEach(function(l) {
      var href = l.getAttribute('href');
      if (href && href.indexOf('debug_logger') === -1) files.push(href);
    });
    return files;
  }
  /* ── DOM ready ────────────────────────────────────────────── */
  document.addEventListener('DOMContentLoaded', function () {
    var $btn    = document.getElementById('dl-btn-trigger');
    var $ov     = document.getElementById('dl-overlay');
    var $modal  = document.getElementById('dl-modal');
    var $close  = document.getElementById('dl-modal-close');
    var $cancel = document.getElementById('dl-btn-cancel');
    var $save   = document.getElementById('dl-btn-save');
    var $toast  = document.getElementById('dl-toast');

    if (!cfg.severityBug) {
      var o = document.querySelector('#dl-severity option[value="bug"]');
      if (o) o.parentNode.removeChild(o);
    }
    if (!cfg.severityWarning) {
      var o2 = document.querySelector('#dl-severity option[value="warning"]');
      if (o2) o2.parentNode.removeChild(o2);
    }
    if (!cfg.severityInfo) {
      var o3 = document.querySelector('#dl-severity option[value="info"]');
      if (o3) o3.parentNode.removeChild(o3);
    }

    function openModal() {
      $ov.style.display = 'block';
      $modal.style.display = 'block';
      document.getElementById('dl-url-display').textContent = window.location.href;
      var routeEl = document.getElementById('dl-route-display');
      if (routeEl) routeEl.textContent = cfg.currentRoute || '';
      // Populate loaded files
      var loadedFiles = collectLoadedFiles();
      var filesEl = document.getElementById('dl-files-display');
      if (filesEl) filesEl.textContent = loadedFiles.join('\n');
      var countEl = document.getElementById('dl-files-count');
      if (countEl) countEl.textContent = '(' + loadedFiles.length + ')';
      document.getElementById('dl-comment').value = '';
      var sel = document.getElementById('dl-severity');
      if (sel && sel.options.length) sel.value = _hasErr ? 'bug' : sel.options[0].value;
      document.body.style.overflow = 'hidden';
    }

    function closeModal() {
      $ov.style.display = 'none';
      $modal.style.display = 'none';
      document.body.style.overflow = '';
    }

    var _toastTimer;
    function showToast(msg, isError) {
      $toast.textContent = msg;
      $toast.className = isError ? 'error' : '';
      $toast.style.display = 'block';
      clearTimeout(_toastTimer);
      _toastTimer = setTimeout(function () { $toast.style.display = 'none'; }, 3500);
    }

    if ($btn)    $btn.addEventListener('click', openModal);
    if ($close)  $close.addEventListener('click', closeModal);
    if ($cancel) $cancel.addEventListener('click', closeModal);
    if ($ov)     $ov.addEventListener('click', closeModal);
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    if ($save) {
      $save.addEventListener('click', function () {
        if (cfg.requireComment && !document.getElementById('dl-comment').value.trim()) {
          document.getElementById('dl-comment').focus();
          return;
        }
        $save.disabled = true;
        var fd = new FormData();
        fd.append('url', window.location.href);
        fd.append('route', cfg.currentRoute || '');
        fd.append('console_log', _logs.join('\n'));
        fd.append('comment', document.getElementById('dl-comment').value);
        fd.append('severity', document.getElementById('dl-severity').value);
        fd.append('loaded_files', JSON.stringify(collectLoadedFiles()));
        fetch(cfg.saveUrl, { method: 'POST', body: fd })
          .then(function (r) { return r.json(); })
          .then(function (j) {
            closeModal();
            if (j.success) showToast((i18n.toastSaved || 'Report #%s saved').replace('%s', j.id), false);
            else showToast(j.error || i18n.toastError || 'Error', true);
          })
          .catch(function (e) {
            closeModal();
            showToast(e.message, true);
          })
          .finally(function () { $save.disabled = false; });
      });
    }
  });
})();
