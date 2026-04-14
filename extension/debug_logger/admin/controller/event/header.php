<?php
namespace Opencart\Admin\Controller\Extension\DebugLogger\Event;

class Header extends \Opencart\System\Engine\Controller {

    // OC4 view/after signature: ($route, $data, $output) — 3 params, no $code
    public function index(string &$route, array &$data, string &$output): void {
        // Prevent double injection if event fires more than once
        static $injected = false;
        if ($injected) return;

        $enabled = (bool)$this->config->get('module_debug_logger_status')
                && (bool)$this->config->get('module_debug_logger_admin_enable');

        if (!$enabled || !isset($this->session->data['user_token'])) {
            return;
        }

        $injected = true;

        // Check user group permission
        $allowed_groups_raw = $this->config->get('module_debug_logger_allowed_groups');
        $allowed_groups = $allowed_groups_raw ? json_decode($allowed_groups_raw, true) : [];
        if (!empty($allowed_groups) && !in_array((int)$this->user->getGroupId(), $allowed_groups, true)) {
            return;
        }

        // Load translations
        $this->load->language('extension/debug_logger/module/debug_logger');

        $tok         = $this->session->data['user_token'];
        $asset_base  = HTTP_CATALOG . 'extension/debug_logger/admin/view/';
        $cache_bust  = '?v=' . filemtime(DIR_EXTENSION . 'debug_logger/admin/view/javascript/debug_logger.js');
        $save_url    = $this->url->link('extension/debug_logger/module/debug_logger.debugSave', 'user_token=' . $tok, true);

        // View Reports only for group_id = 1 (Administrator)
        $is_admin    = $this->user->isLogged() && ((int)$this->user->getGroupId() === 1);
        $reports_url = $is_admin
            ? $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok, true)
            : '';

        // Pro: check license for screenshot feature
        $this->load->model('extension/debug_logger/module/debug_logger_license');
        $is_pro = $this->model_extension_debug_logger_module_debug_logger_license->isPro();
        $capture_screenshot = $is_pro && (bool)$this->config->get('module_debug_logger_capture_screenshot');

        // Current route & derived server files
        $current_route = $this->request->get['route'] ?? '';
        $server_files = [];
        if ($current_route) {
            $server_files[] = 'controller/' . $current_route . '.php';
            $server_files[] = 'model/' . $current_route . '.php';
            $lang_code = $this->config->get('config_language_admin') ?: 'en-gb';
            $server_files[] = 'language/' . $lang_code . '/' . $current_route . '.php';
            $server_files[] = 'view/template/' . $current_route . '.twig';
        }

        // Appearance settings (Pro defaults)
        $btn_color    = ($is_pro ? $this->config->get('module_debug_logger_btn_color') : null) ?: '#dc2626';
        $header_color = ($is_pro ? $this->config->get('module_debug_logger_header_color') : null) ?: '#1e293b';
        $accent_color = ($is_pro ? $this->config->get('module_debug_logger_accent_color') : null) ?: '#3b82f6';
        $btn_position = ($is_pro ? $this->config->get('module_debug_logger_btn_position') : null) ?: 'navbar';
        $btn_size     = ($is_pro ? $this->config->get('module_debug_logger_btn_size') : null) ?: 'medium';

        $config_json = json_encode([
            'saveUrl'           => $save_url,
            'reportsUrl'        => $reports_url,
            'currentRoute'      => $current_route,
            'serverFiles'       => $server_files,
            'captureConsole'    => (bool)($this->config->get('module_debug_logger_capture_console') ?? 1),
            'captureNetwork'    => (bool)$this->config->get('module_debug_logger_capture_network'),
            'captureScreenshot' => $capture_screenshot,
            'requireComment'    => (bool)$this->config->get('module_debug_logger_require_comment'),
            'severityBug'       => (bool)($this->config->get('module_debug_logger_severity_bug') ?? 1),
            'severityWarning'   => (bool)($this->config->get('module_debug_logger_severity_warning') ?? 1),
            'severityInfo'      => (bool)($this->config->get('module_debug_logger_severity_info') ?? 1),
            'i18n' => [
                'title'        => $this->language->get('popup_title'),
                'btnTrigger'   => $this->language->get('popup_btn_trigger'),
                'labelPage'    => $this->language->get('popup_label_page'),
                'labelSev'     => $this->language->get('popup_label_severity'),
                'labelComment' => $this->language->get('popup_label_comment'),
                'labelConsole' => $this->language->get('popup_label_console'),
                'placeholder'  => $this->language->get('popup_placeholder'),
                'sevBug'       => $this->language->get('popup_severity_bug'),
                'sevWarn'      => $this->language->get('popup_severity_warn'),
                'sevInfo'      => $this->language->get('popup_severity_info'),
                'btnCancel'    => $this->language->get('popup_btn_cancel'),
                'btnSave'      => $this->language->get('popup_btn_save'),
                'btnReports'   => $this->language->get('popup_btn_reports'),
                'tipSeverity'  => $this->language->get('popup_tip_severity'),
                'tipComment'   => $this->language->get('popup_tip_comment'),
                'tipConsole'   => $this->language->get('popup_tip_console'),
                'tipReports'   => $this->language->get('popup_tip_reports'),
                'toastSaved'   => $this->language->get('popup_toast_saved'),
                'toastError'   => $this->language->get('popup_toast_error'),
                'ssEdit'       => $this->language->get('popup_ss_edit'),
                'ssDone'       => $this->language->get('popup_ss_done'),
                'ssCancel'     => $this->language->get('popup_ss_cancel'),
                'ssDraw'       => $this->language->get('popup_ss_draw'),
                'ssArrow'      => $this->language->get('popup_ss_arrow'),
                'ssRect'       => $this->language->get('popup_ss_rect'),
                'ssText'       => $this->language->get('popup_ss_text'),
                'ssUndo'       => $this->language->get('popup_ss_undo'),
                'ssReset'      => $this->language->get('popup_ss_reset'),
                'ssThin'       => $this->language->get('popup_ss_thin'),
                'ssNormal'     => $this->language->get('popup_ss_normal'),
                'ssThick'      => $this->language->get('popup_ss_thick'),
                'ssPrompt'     => $this->language->get('popup_ss_prompt'),
            ],
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $a = htmlspecialchars($asset_base, ENT_QUOTES | ENT_HTML5);

        // Nav button label (must be defined before floating_btn uses it)
        $btn_label = htmlspecialchars($this->language->get('popup_btn_trigger'), ENT_QUOTES | ENT_HTML5);

        // Floating button for non-navbar positions
        $floating_btn = '';
        if ($btn_position !== 'navbar') {
            $pos_map = [
                'bottom-right' => 'bottom:20px;right:20px',
                'bottom-left'  => 'bottom:20px;left:20px',
                'top-right'    => 'top:80px;right:20px',
                'top-left'     => 'top:80px;left:20px',
            ];
            $pos_css = $pos_map[$btn_position] ?? 'bottom:20px;right:20px';
            $floating_btn = '<div id="btn-debug-logger" style="position:fixed;' . $pos_css . ';z-index:99999;cursor:pointer;'
                . 'background:' . htmlspecialchars($btn_color, ENT_QUOTES) . ';color:#fff;border-radius:50%;'
                . 'width:' . ($btn_size === 'small' ? '36px' : ($btn_size === 'large' ? '56px' : '46px')) . ';'
                . 'height:' . ($btn_size === 'small' ? '36px' : ($btn_size === 'large' ? '56px' : '46px')) . ';'
                . 'display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(0,0,0,.3)"'
                . ' title="' . $btn_label . '">'
                . '<i class="fa-solid fa-bug"></i></div>';
        }

        // Nav button — uses appearance settings
        $size_map = ['small' => '3px 6px', 'medium' => '5px 10px', 'large' => '7px 14px'];
        $btn_padding = $size_map[$btn_size] ?? '5px 10px';
        $font_size = $btn_size === 'small' ? 'font-size:.8rem;' : ($btn_size === 'large' ? 'font-size:1.1rem;' : '');

        if ($btn_position === 'navbar') {
            $nav_btn = '<li id="nav-debug" class="nav-item">'
                . '<a href="#" id="btn-debug-logger" class="nav-link" title="' . $btn_label . '"'
                . ' style="color:#fff;background:' . htmlspecialchars($btn_color, ENT_QUOTES) . ';border-radius:4px;padding:' . $btn_padding . ';display:inline-flex;align-items:center;gap:6px;' . $font_size . '"'
                . '><i class="fa-solid fa-bug"></i>'
                . '<span class="d-none d-md-inline">' . $btn_label . '</span>'
                . '</a></li>';
            $output = str_replace('<li id="nav-logout"', $nav_btn . '<li id="nav-logout"', $output);
        }

        // Translated strings for modal HTML (PHP-side, no DOMContentLoaded race)
        $t_title       = htmlspecialchars($this->language->get('popup_title'),         ENT_QUOTES | ENT_HTML5);
        $t_page        = htmlspecialchars($this->language->get('popup_label_page'),    ENT_QUOTES | ENT_HTML5);
        $t_sev         = htmlspecialchars($this->language->get('popup_label_severity'),ENT_QUOTES | ENT_HTML5);
        $t_comment     = htmlspecialchars($this->language->get('popup_label_comment'), ENT_QUOTES | ENT_HTML5);
        $t_console     = htmlspecialchars($this->language->get('popup_label_console'), ENT_QUOTES | ENT_HTML5);
        $t_placeholder = htmlspecialchars($this->language->get('popup_placeholder'),   ENT_QUOTES | ENT_HTML5);
        $t_sev_bug     = htmlspecialchars($this->language->get('popup_severity_bug'),  ENT_QUOTES | ENT_HTML5);
        $t_sev_warn    = htmlspecialchars($this->language->get('popup_severity_warn'), ENT_QUOTES | ENT_HTML5);
        $t_sev_info    = htmlspecialchars($this->language->get('popup_severity_info'), ENT_QUOTES | ENT_HTML5);
        $t_cancel      = htmlspecialchars($this->language->get('popup_btn_cancel'),    ENT_QUOTES | ENT_HTML5);
        $t_save        = htmlspecialchars($this->language->get('popup_btn_save'),      ENT_QUOTES | ENT_HTML5);
        $t_reports     = htmlspecialchars($this->language->get('popup_btn_reports'),   ENT_QUOTES | ENT_HTML5);
        $t_tip_sev     = htmlspecialchars($this->language->get('popup_tip_severity'),  ENT_QUOTES | ENT_HTML5);
        $t_tip_comment = htmlspecialchars($this->language->get('popup_tip_comment'),   ENT_QUOTES | ENT_HTML5);
        $t_tip_console = htmlspecialchars($this->language->get('popup_tip_console'),   ENT_QUOTES | ENT_HTML5);
        $t_tip_reports = htmlspecialchars($this->language->get('popup_tip_reports'),   ENT_QUOTES | ENT_HTML5);

        $req_star = $this->config->get('module_debug_logger_require_comment')
            ? ' <span style="color:#ef4444">*</span>' : '';

        $sev_options = '';
        if ($this->config->get('module_debug_logger_severity_bug') ?? 1)
            $sev_options .= '<option value="bug">' . $t_sev_bug . '</option>';
        if ($this->config->get('module_debug_logger_severity_warning'))
            $sev_options .= '<option value="warning">' . $t_sev_warn . '</option>';
        if ($this->config->get('module_debug_logger_severity_info'))
            $sev_options .= '<option value="info">' . $t_sev_info . '</option>';

        $hint = '<div class="dl-hint">%s</div>';
        $reports_link = $is_admin && $reports_url
            ? '<a href="' . htmlspecialchars($reports_url, ENT_QUOTES | ENT_HTML5) . '" target="_blank"'
              . ' style="font-size:.72rem;color:#64748b;display:inline-flex;align-items:center;gap:4px">'
              . '<i class="fa-solid fa-list-ul"></i> ' . $t_reports . '</a>'
            : '';

        $screenshot_section = '';
        if ($capture_screenshot) {
            $t_screenshot = htmlspecialchars($this->language->get('popup_label_screenshot') ?: 'Screenshot', ENT_QUOTES | ENT_HTML5);
            $screenshot_section = '<div class="dl-field" id="dl-screenshot-field" style="display:none">'
                . '<label>' . $t_screenshot . '</label>'
                . '<div id="dl-screenshot-preview" style="margin-top:4px"></div>'
                . '<input type="hidden" id="dl-screenshot-data" value="">'
                . '</div>';
        }

        $inject = '<!-- Debug Logger v2.0.0 -->'
            . '<style>#dl-modal-head{background:' . htmlspecialchars($header_color, ENT_QUOTES) . '}'
            . ' .btn-dl-save{background:' . htmlspecialchars($accent_color, ENT_QUOTES) . '}'
            . ' .btn-dl-save:hover{filter:brightness(1.15)}</style>'
            . ($capture_screenshot ? '<script src="' . $a . 'javascript/html2canvas.min.js' . $cache_bust . '"></script>' : '')
            . '<link rel="stylesheet" href="' . $a . 'stylesheet/debug_logger.css' . $cache_bust . '">'
            . $floating_btn
            . '<div id="dl-overlay"></div>'
            . '<div id="dl-modal">'
            .   '<div id="dl-modal-head">'
            .     '<i class="fa-solid fa-bug" style="color:#fca5a5"></i>'
            .     '<h5>' . $t_title . '</h5>'
            .     '<button id="dl-modal-close" type="button">&#x2715;</button>'
            .   '</div>'
            .   '<div id="dl-modal-body">'
            .     '<div class="dl-field"><label>' . $t_page . '</label>'
            .       '<div class="dl-url-val" id="dl-url-display"></div>'
            .       '<div class="dl-url-val" id="dl-route-display" style="font-size:.8em;color:#64748b;font-family:monospace"></div></div>'
            .     '<div class="dl-field">'
            .       '<label>' . htmlspecialchars($this->language->get('popup_label_files'), ENT_QUOTES | ENT_HTML5) . ''
            .         ' <span id="dl-files-count" style="color:#64748b;font-size:.8em">(0)</span></label>'
            .       '<div class="dl-console" id="dl-files-display" style="max-height:120px"></div>'
            .     '</div>'
            .     '<div class="dl-field">'
            .       '<label>' . $t_sev . '</label>'
            .       sprintf($hint, $t_tip_sev)
            .       '<select id="dl-severity">' . $sev_options . '</select>'
            .     '</div>'
            .     '<div class="dl-field">'
            .       '<label>' . $t_comment . $req_star . '</label>'
            .       sprintf($hint, $t_tip_comment)
            .       '<textarea id="dl-comment" rows="3" placeholder="' . $t_placeholder . '"></textarea>'
            .     '</div>'
            .     '<div class="dl-field">'
            .       '<label>' . $t_console
            .         ' <span id="dl-count" style="color:#64748b;font-size:.8em">(0)</span></label>'
            .       sprintf($hint, $t_tip_console)
            .       '<div class="dl-console" id="dl-console-display"></div>'
            .     '</div>'
            .     $screenshot_section
            .   '</div>'
            .   '<div id="dl-modal-foot">'
            .     $reports_link
            .     '<div style="display:flex;gap:.6rem">'
            .       '<button class="btn-dl-cancel" id="dl-btn-cancel" type="button">' . $t_cancel . '</button>'
            .       '<button class="btn-dl-save" id="dl-btn-save" type="button">'
            .         '<i class="fa-solid fa-save"></i> ' . $t_save
            .       '</button>'
            .     '</div>'
            .   '</div>'
            . '</div>'
            . '<div id="dl-toast"></div>'
            . '<script>window.DL_CONFIG=' . $config_json . ';</script>'
            . '<script src="' . $a . 'javascript/debug_logger.js' . $cache_bust . '"></script>';

        $output = str_replace('</header>', '</header>' . $inject, $output);
    }

    /**
     * Event: admin/view/common/column_left/before
     * Injects Debug Logger menu into admin sidebar.
     */
    public function columnLeft(string &$route, array &$args): void {
        if (!isset($this->session->data['user_token'])) {
            return;
        }

        if (!$this->user->hasPermission('access', 'extension/debug_logger/module/debug_logger')) {
            return;
        }

        $this->load->language('extension/debug_logger/module/debug_logger');

        $tok = $this->session->data['user_token'];

        $children = [];

        $children[] = [
            'name'     => $this->language->get('text_menu_dashboard'),
            'href'     => $this->url->link('extension/debug_logger/module/debug_logger.analytics', 'user_token=' . $tok),
            'children' => [],
        ];

        $children[] = [
            'name'     => $this->language->get('text_menu_reports'),
            'href'     => $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok),
            'children' => [],
        ];

        $children[] = [
            'name'     => $this->language->get('text_menu_settings'),
            'href'     => $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok),
            'children' => [],
        ];

        $args['menus'][] = [
            'id'       => 'menu-debug-logger',
            'icon'     => 'fa-solid fa-bug',
            'name'     => $this->language->get('text_menu_title'),
            'href'     => '',
            'children' => $children,
        ];
    }
}
