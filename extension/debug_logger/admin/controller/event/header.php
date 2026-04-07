<?php
namespace Opencart\Admin\Controller\Extension\DebugLogger\Event;

class Header extends \Opencart\System\Engine\Controller {

    public function index(string &$route, array &$data, mixed &$code, string &$output): void {
        $enabled = (bool)$this->config->get('module_debug_logger_status')
                && (bool)$this->config->get('module_debug_logger_admin_enable');

        $data['debug_logger_admin_enable']     = $enabled;
        $data['debug_logger_capture_console']  = (bool)($this->config->get('module_debug_logger_capture_console') ?? 1);
        $data['debug_logger_capture_network']  = (bool)$this->config->get('module_debug_logger_capture_network');
        $data['debug_logger_require_comment']  = (bool)$this->config->get('module_debug_logger_require_comment');
        $data['debug_logger_severity_bug']     = (bool)($this->config->get('module_debug_logger_severity_bug') ?? 1);
        $data['debug_logger_severity_warning'] = (bool)($this->config->get('module_debug_logger_severity_warning') ?? 1);
        $data['debug_logger_severity_info']    = (bool)($this->config->get('module_debug_logger_severity_info') ?? 1);

        $data['debug_logger_admin_asset_base'] = HTTP_CATALOG . 'extension/debug_logger/admin/view/';

        if ($enabled && isset($this->session->data['user_token'])) {
            $tok = $this->session->data['user_token'];
            $save_url    = $this->url->link('extension/debug_logger/module/debug_logger.debugSave', 'user_token=' . $tok, true);
            $reports_url = $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok, true);
            $data['debug_logger_save_url_json'] = json_encode($save_url);
            $data['debug_logger_reports_url']   = $reports_url;
        } else {
            $data['debug_logger_save_url_json'] = '""';
            $data['debug_logger_reports_url']   = '';
        }
    }
}
