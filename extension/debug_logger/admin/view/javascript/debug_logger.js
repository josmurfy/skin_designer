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

  /* ── Screenshot annotator (fullscreen overlay editor) ──── */
  var _annot = { drawing: false, ctx: null, canvas: null, tool: 'pen', color: '#ef4444', history: [] };
  var _ssOriginal = '';

  function dlShowThumb($container, dataUrl) {
    _ssOriginal = dataUrl;
    $container.html(
      '<div style="display:flex;align-items:center;gap:10px;margin-top:6px">'
      + '<img src="' + dataUrl + '" style="max-width:180px;max-height:80px;border-radius:4px;border:1px solid #334155;cursor:pointer" id="dl-ss-thumb">'
      + '<button type="button" id="dl-btn-edit-ss" style="background:#1e3a5f;color:#93c5fd;border:1px solid #3b82f6;border-radius:6px;padding:5px 12px;font-size:.78rem;cursor:pointer;white-space:nowrap">'
      + '<i class="fa-solid fa-pen"></i> Edit Screenshot</button>'
      + '</div>'
    );
  }

  function dlOpenEditor() {
    var src = $('#dl-screenshot-data').val() || _ssOriginal;
    if (!src) return;

    var $ed = $('#dl-editor-overlay');
    if (!$ed.length) {
      $('body').append(
        '<div id="dl-editor-overlay"><div id="dl-editor-inner">'
        + '<div id="dl-editor-toolbar"></div>'
        + '<canvas id="dl-editor-canvas" style="cursor:crosshair;border-radius:6px;border:1px solid #475569;max-width:100%"></canvas>'
        + '<div style="margin-top:.75rem">'
        +   '<button type="button" id="dl-ed-done" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border:none;border-radius:8px;padding:8px 28px;font-size:.9rem;font-weight:700;cursor:pointer;margin-right:10px">✓ Done</button>'
        +   '<button type="button" id="dl-ed-cancel" style="background:#374151;color:#d1d5db;border:none;border-radius:8px;padding:8px 20px;font-size:.9rem;cursor:pointer">Cancel</button>'
        + '</div>'
        + '</div></div>'
      );
      $ed = $('#dl-editor-overlay');
    }

    var toolbar = '<button type="button" class="dl-et dl-et-active" data-tool="pen">✏️ Draw</button>'
      + '<button type="button" class="dl-et" data-tool="arrow">➡️ Arrow</button>'
      + '<button type="button" class="dl-et" data-tool="rect">⬜ Rect</button>'
      + '<button type="button" class="dl-et" data-tool="text">T Text</button>'
      + '<span style="width:1px;height:22px;background:#475569;display:inline-block"></span>'
      + '<input type="color" id="dl-ed-color" value="' + _annot.color + '" style="width:32px;height:28px;border:none;padding:0;cursor:pointer;background:transparent">'
      + '<select id="dl-ed-size" style="background:#1e293b;color:#e2e8f0;border:1px solid #475569;border-radius:6px;font-size:13px;padding:2px 6px">'
      +   '<option value="2">Thin</option><option value="4" selected>Normal</option><option value="8">Thick</option>'
      + '</select>'
      + '<span style="width:1px;height:22px;background:#475569;display:inline-block"></span>'
      + '<button type="button" id="dl-ed-undo" class="dl-et">↩️ Undo</button>'
      + '<button type="button" id="dl-ed-reset" class="dl-et">🗑️ Reset</button>';
    $('#dl-editor-toolbar').html(toolbar);

    _annot.tool = 'pen';
    _annot.history = [];

    var img = new Image();
    img.onload = function () {
      var maxW = Math.min(window.innerWidth - 40, 1400);
      var maxH = window.innerHeight - 180;
      var ratio = Math.min(maxW / img.width, maxH / img.height, 1);
      var cw = Math.round(img.width * ratio);
      var ch = Math.round(img.height * ratio);

      var cvs = document.getElementById('dl-editor-canvas');
      cvs.width = cw;
      cvs.height = ch;
      var ctx = cvs.getContext('2d');
      _annot.canvas = cvs;
      _annot.ctx = ctx;

      ctx.drawImage(img, 0, 0, cw, ch);
      _annot.history.push(ctx.getImageData(0, 0, cw, ch));

      var startX, startY, snapshot;

      function getPos(e) {
        var r = cvs.getBoundingClientRect();
        var t = e.touches ? e.touches[0] : e;
        return { x: t.clientX - r.left, y: t.clientY - r.top };
      }

      function beginDraw(e) {
        e.preventDefault();
        var p = getPos(e);
        startX = p.x; startY = p.y;
        _annot.drawing = true;
        snapshot = ctx.getImageData(0, 0, cw, ch);
        if (_annot.tool === 'pen') { ctx.beginPath(); ctx.moveTo(p.x, p.y); }
        if (_annot.tool === 'text') {
          _annot.drawing = false;
          var txt = prompt('Text:');
          if (txt) {
            var sz = parseInt($('#dl-ed-size').val()) * 5 + 10;
            ctx.font = 'bold ' + sz + 'px sans-serif';
            ctx.fillStyle = _annot.color;
            ctx.fillText(txt, p.x, p.y);
            _annot.history.push(ctx.getImageData(0, 0, cw, ch));
          }
        }
      }

      function moveDraw(e) {
        if (!_annot.drawing) return;
        e.preventDefault();
        var p = getPos(e);
        ctx.strokeStyle = _annot.color;
        ctx.lineWidth = parseInt($('#dl-ed-size').val());
        ctx.lineCap = 'round'; ctx.lineJoin = 'round';
        if (_annot.tool === 'pen') {
          ctx.lineTo(p.x, p.y); ctx.stroke();
        } else {
          ctx.putImageData(snapshot, 0, 0);
          if (_annot.tool === 'rect') {
            ctx.strokeRect(startX, startY, p.x - startX, p.y - startY);
          } else if (_annot.tool === 'arrow') {
            ctx.beginPath(); ctx.moveTo(startX, startY); ctx.lineTo(p.x, p.y); ctx.stroke();
            var angle = Math.atan2(p.y - startY, p.x - startX), hl = 14;
            ctx.beginPath(); ctx.moveTo(p.x, p.y);
            ctx.lineTo(p.x - hl * Math.cos(angle - Math.PI / 6), p.y - hl * Math.sin(angle - Math.PI / 6));
            ctx.moveTo(p.x, p.y);
            ctx.lineTo(p.x - hl * Math.cos(angle + Math.PI / 6), p.y - hl * Math.sin(angle + Math.PI / 6));
            ctx.stroke();
          }
        }
      }

      function endDraw() {
        if (!_annot.drawing) return;
        _annot.drawing = false;
        _annot.history.push(ctx.getImageData(0, 0, cw, ch));
      }

      cvs.onmousedown = beginDraw;
      cvs.onmousemove = moveDraw;
      cvs.onmouseup = endDraw;
      cvs.onmouseleave = endDraw;
      cvs.addEventListener('touchstart', beginDraw, { passive: false });
      cvs.addEventListener('touchmove', moveDraw, { passive: false });
      cvs.addEventListener('touchend', endDraw);
    };
    img.src = src;

    $ed.show();

    // Toolbar events (use .off to prevent stacking)
    $ed.off('click', '.dl-et[data-tool]').on('click', '.dl-et[data-tool]', function () {
      $ed.find('.dl-et').removeClass('dl-et-active');
      $(this).addClass('dl-et-active');
      _annot.tool = $(this).data('tool');
    });
    $ed.off('input', '#dl-ed-color').on('input', '#dl-ed-color', function () { _annot.color = this.value; });
    $ed.off('click', '#dl-ed-undo').on('click', '#dl-ed-undo', function () {
      if (_annot.history.length > 1) {
        _annot.history.pop();
        _annot.ctx.putImageData(_annot.history[_annot.history.length - 1], 0, 0);
      }
    });
    $ed.off('click', '#dl-ed-reset').on('click', '#dl-ed-reset', function () {
      if (_annot.history.length > 0) {
        _annot.history = [_annot.history[0]];
        _annot.ctx.putImageData(_annot.history[0], 0, 0);
      }
    });
    $ed.off('click', '#dl-ed-done').on('click', '#dl-ed-done', function () {
      var d = _annot.canvas.toDataURL('image/jpeg', 0.92);
      if (d.length < 4194304) {
        $('#dl-screenshot-data').val(d);
        $('#dl-ss-thumb').attr('src', d);
      }
      $ed.hide();
    });
    $ed.off('click', '#dl-ed-cancel').on('click', '#dl-ed-cancel', function () { $ed.hide(); });
    $(document).off('keydown.dleditor').on('keydown.dleditor', function (e) {
      if (e.key === 'Escape' && $ed.is(':visible')) { $ed.hide(); }
    });
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
        console.log('[DL] Screenshot capture: starting html2canvas...');
        $modal.css('visibility', 'hidden');
        $ov.css('visibility', 'hidden');
        setTimeout(function () {
          html2canvas(document.body, {
            useCORS: true,
            scale: Math.min(window.devicePixelRatio || 1, 2),
            logging: false,
            windowWidth: document.documentElement.clientWidth,
            windowHeight: window.innerHeight,
            width: document.documentElement.clientWidth,
            height: window.innerHeight,
            y: window.scrollY
          }).then(function (canvas) {
            var dataUrl = canvas.toDataURL('image/jpeg', 0.92);
            console.log('[DL] Screenshot captured, size=' + dataUrl.length);
            if (dataUrl && dataUrl.length < 4194304) {
              $('#dl-screenshot-data').val(dataUrl);
              dlShowThumb($ssField.find('#dl-screenshot-preview'), dataUrl);
            } else {
              console.warn('[DL] Screenshot too large: ' + dataUrl.length);
            }
            $modal.css('visibility', '');
            $ov.css('visibility', '');
          }).catch(function (err) {
            console.error('[DL] html2canvas error:', err);
            $modal.css('visibility', '');
            $ov.css('visibility', '');
          });
        }, 100);
      } else {
        if (!cfg.captureScreenshot) console.log('[DL] Screenshot disabled in config');
        if (typeof html2canvas !== 'function') console.warn('[DL] html2canvas not loaded');
      }

      setTimeout(function () { $('#dl-comment').focus(); }, 300);
    }

    function closeModal() {
      $ov.hide();
      $modal.hide();
      $('body').css('overflow', '');
    }

    $(document).on('click', '#dl-btn-edit-ss, #dl-ss-thumb', function (e) {
      e.preventDefault();
      dlOpenEditor();
    });

    function showToast(msg, isError) {
      $toast.text(msg).removeClass('error');
      if (isError) $toast.addClass('error');
      $toast.show();
      clearTimeout($toast.data('dl-t'));
      $toast.data('dl-t', setTimeout(function () { $toast.hide(); }, 3800));
    }

    // Use event delegation so floating button (injected div) also fires
    $(document).on('click', '#btn-debug-logger', function (e) { e.preventDefault(); openModal(); });

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
