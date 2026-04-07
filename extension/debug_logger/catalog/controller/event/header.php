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

        $asset_base  = HTTP_SERVER . 'extension/debug_logger/catalog/view/';
        $save_url    = HTTP_SERVER . 'index.php?route=extension/debug_logger/debug_logger.save';

        $config_json = json_encode([
            'saveUrl'         => $save_url,
            'captureConsole'  => (bool)($this->config->get('module_debug_logger_capture_console') ?? 1),
            'captureNetwork'  => (bool)$this->config->get('module_debug_logger_capture_network'),
            'requireComment'  => (bool)$this->config->get('module_debug_logger_require_comment'),
            'severityBug'     => (bool)($this->config->get('module_debug_logger_severity_bug') ?? 1),
            'severityWarning' => (bool)($this->config->get('module_debug_logger_severity_warning') ?? 1),
            'severityInfo'    => (bool)($this->config->get('module_debug_logger_severity_info') ?? 1),
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES);

        $a = htmlspecialchars($asset_base, ENT_QUOTES | ENT_HTML5);

        $inject = '<!-- Debug Logger v2.0.0 catalog -->'
            . '<link rel="stylesheet" href="' . $a . 'stylesheet/debug_logger.css">'
            . '<button id="dl-btn-trigger" type="button">&#x1F41B; Debug</button>'
            . '<div id="dl-overlay"></div>'
            . '<div id="dl-modal">'
            .   '<div id="dl-modal-head">'
            .     '<span>&#x1F41B;</span>'
            .     '<h5>Report a Problem</h5>'
            .     '<button id="dl-modal-close" type="button">&#x2715;</button>'
            .   '</div>'
            .   '<div id="dl-modal-body">'
            .     '<div class="dl-field"><label>Page</label><div class="dl-url-val" id="dl-url-display"></div></div>'
            .     '<div class="dl-field"><label>Severity</label>'
            .       '<select id="dl-severity">'
            .         '<option value="bug">&#x1F41B; Bug</option>'
            .         '<option value="warning">&#x26A0; Warning</option>'
            .         '<option value="info">&#x2139; Info</option>'
            .       '</select>'
            .     '</div>'
            .     '<div class="dl-field"><label>Comment</label>'
            .       '<textarea id="dl-comment" rows="3" placeholder="Describe the issue..."></textarea>'
            .     '</div>'
            .   '</div>'
            .   '<div id="dl-modal-foot">'
            .     '<button class="btn-dl-cancel" id="dl-btn-cancel" type="button">Cancel</button>'
            .     '<button class="btn-dl-save" id="dl-btn-save" type="button">Save</button>'
            .   '</div>'
            . '</div>'
            . '<div id="dl-toast"></div>'
            . '<script>window.DL_CONFIG=' . $config_json . ';</script>'
            . '<script src="' . $a . 'javascript/debug_logger.js"></script>';

        $output = str_replace('</header>', '</header>' . $inject, $output);
    }
}
