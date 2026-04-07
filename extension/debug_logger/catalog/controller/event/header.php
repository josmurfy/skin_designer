<?php
namespace Opencart\Catalog\Controller\Extension\DebugLogger\Event;

class Header extends \Opencart\System\Engine\Controller {

    public function index(string &$route, array &$data, mixed &$code, string &$output): void {
        $enabled = (bool)$this->config->get('module_debug_logger_status')
                && (bool)$this->config->get('module_debug_logger_catalog_enable');

        $data['debug_logger_catalog_enable']   = $enabled;
        $data['debug_logger_capture_console']  = (bool)($this->config->get('module_debug_logger_capture_console') ?? 1);
        $data['debug_logger_capture_network']  = (bool)$this->config->get('module_debug_logger_capture_network');
        $data['debug_logger_require_comment']  = (bool)$this->config->get('module_debug_logger_require_comment');
        $data['debug_logger_severity_bug']     = (bool)($this->config->get('module_debug_logger_severity_bug') ?? 1);
        $data['debug_logger_severity_warning'] = (bool)($this->config->get('module_debug_logger_severity_warning') ?? 1);
        $data['debug_logger_severity_info']    = (bool)($this->config->get('module_debug_logger_severity_info') ?? 1);

        $data['debug_logger_catalog_asset_base'] = HTTP_SERVER . 'extension/debug_logger/catalog/view/';

        if ($enabled) {
            $save_url = HTTP_SERVER . 'index.php?route=extension/debug_logger/debug_logger.save';
            $data['debug_logger_catalog_save_url_json'] = json_encode($save_url);
        } else {
            $data['debug_logger_catalog_save_url_json'] = '""';
        }
    }
}
