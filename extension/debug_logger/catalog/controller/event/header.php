<?php
namespace Opencart\Catalog\Controller\Extension\DebugLogger\Event;

class Header extends \Opencart\System\Engine\Controller {

    // OC4 view/after signature: ($route, $data, $output) — 3 params, no $code
    public function index(string &$route, array &$data, string &$output): void {
        $enabled = (bool)$this->config->get('module_debug_logger_status')
                && (bool)$this->config->get('module_debug_logger_catalog_enable');

        if (!$enabled) {
            return;
        }

        // Load translations
        $this->load->language('extension/debug_logger/module/debug_logger');

        $asset_base  = HTTP_SERVER . 'extension/debug_logger/catalog/view/';
        $cache_bust  = '?v=' . filemtime(DIR_EXTENSION . 'debug_logger/catalog/view/javascript/debug_logger.js');
        $save_url    = HTTP_SERVER . 'index.php?route=extension/debug_logger/debug_logger.save';

        // Current route & derived server files
        $current_route = $this->request->get['route'] ?? '';
        $server_files = [];
        if ($current_route) {
            $server_files[] = 'controller/' . $current_route . '.php';
            $server_files[] = 'model/' . $current_route . '.php';
            $lang_code = $this->config->get('config_language') ?: 'en-gb';
            $server_files[] = 'language/' . $lang_code . '/' . $current_route . '.php';
            $server_files[] = 'view/template/' . $current_route . '.twig';
        }

        // Translated strings
        $e = function($key) { return htmlspecialchars($this->language->get($key), ENT_QUOTES | ENT_HTML5); };
        $t_title   = $e('popup_title');
        $t_trigger = $e('popup_btn_trigger');
        $t_page    = $e('popup_label_page');
        $t_sev     = $e('popup_label_severity');
        $t_comment = $e('popup_label_comment');
        $t_files   = $e('popup_label_files');
        $t_place   = $e('popup_placeholder');
        $t_bug     = $e('popup_severity_bug');
        $t_warn    = $e('popup_severity_warn');
        $t_info    = $e('popup_severity_info');
        $t_cancel  = $e('popup_btn_cancel');
        $t_save    = $e('popup_btn_save');

        $sev_options = '';
        if ($this->config->get('module_debug_logger_severity_bug') ?? 1)
            $sev_options .= '<option value="bug">&#x1F41B; ' . $t_bug . '</option>';
        if ($this->config->get('module_debug_logger_severity_warning'))
            $sev_options .= '<option value="warning">&#x26A0; ' . $t_warn . '</option>';
        if ($this->config->get('module_debug_logger_severity_info'))
            $sev_options .= '<option value="info">&#x2139; ' . $t_info . '</option>';

        $req_star = $this->config->get('module_debug_logger_require_comment')
            ? ' <span style="color:#ef4444">*</span>' : '';

        $config_json = json_encode([
            'saveUrl'         => $save_url,
            'currentRoute'    => $current_route,
            'serverFiles'     => $server_files,
            'captureConsole'  => (bool)($this->config->get('module_debug_logger_capture_console') ?? 1),
            'captureNetwork'  => (bool)$this->config->get('module_debug_logger_capture_network'),
            'requireComment'  => (bool)$this->config->get('module_debug_logger_require_comment'),
            'severityBug'     => (bool)($this->config->get('module_debug_logger_severity_bug') ?? 1),
            'severityWarning' => (bool)($this->config->get('module_debug_logger_severity_warning') ?? 1),
            'severityInfo'    => (bool)($this->config->get('module_debug_logger_severity_info') ?? 1),
            'i18n' => [
                'toastSaved' => $this->language->get('popup_toast_saved'),
                'toastError' => $this->language->get('popup_toast_error'),
            ],
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES);

        $a = htmlspecialchars($asset_base, ENT_QUOTES | ENT_HTML5);

        $inject = '<!-- Debug Logger catalog -->'
            . '<link rel="stylesheet" href="' . $a . 'stylesheet/debug_logger.css' . $cache_bust . '">'
            . '<button id="dl-btn-trigger" type="button">&#x1F41B; ' . $t_trigger . '</button>'
            . '<div id="dl-overlay"></div>'
            . '<div id="dl-modal">'
            .   '<div id="dl-modal-head">'
            .     '<span>&#x1F41B;</span>'
            .     '<h5>' . $t_title . '</h5>'
            .     '<button id="dl-modal-close" type="button">&#x2715;</button>'
            .   '</div>'
            .   '<div id="dl-modal-body">'
            .     '<div class="dl-field"><label>' . $t_page . '</label><div class="dl-url-val" id="dl-url-display"></div>'
            .       '<div class="dl-url-val" id="dl-route-display" style="font-size:.8em;color:#64748b;font-family:monospace"></div></div>'
            .     '<div class="dl-field">'
            .       '<label>' . $t_files . ' <span id="dl-files-count" style="color:#64748b;font-size:.8em">(0)</span></label>'
            .       '<div class="dl-console" id="dl-files-display" style="max-height:120px"></div>'
            .     '</div>'
            .     '<div class="dl-field"><label>' . $t_sev . '</label>'
            .       '<select id="dl-severity">' . $sev_options . '</select>'
            .     '</div>'
            .     '<div class="dl-field"><label>' . $t_comment . $req_star . '</label>'
            .       '<textarea id="dl-comment" rows="3" placeholder="' . $t_place . '"></textarea>'
            .     '</div>'
            .   '</div>'
            .   '<div id="dl-modal-foot">'
            .     '<button class="btn-dl-cancel" id="dl-btn-cancel" type="button">' . $t_cancel . '</button>'
            .     '<button class="btn-dl-save" id="dl-btn-save" type="button">' . $t_save . '</button>'
            .   '</div>'
            . '</div>'
            . '<div id="dl-toast"></div>'
            . '<script>window.DL_CONFIG=' . $config_json . ';</script>'
            . '<script src="' . $a . 'javascript/debug_logger.js' . $cache_bust . '"></script>';

        $output = str_replace('</header>', '</header>' . $inject, $output);
    }
}
