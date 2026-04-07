<?php
namespace Opencart\Catalog\Controller\Extension\DebugLogger;

/**
 * Catalog AJAX endpoint for saving debug reports from the storefront.
 * Route: index.php?route=extension/debug_logger/debug_logger.save
 */
class DebugLogger extends \Opencart\System\Engine\Controller {

    public function save(): void {
        $this->response->addHeader('Content-Type: application/json');

        // Only accept POST
        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $this->response->setOutput(json_encode(['error' => 'Method not allowed']));
            return;
        }

        // Check module is enabled for catalog
        if (!(bool)$this->config->get('module_debug_logger_status')
            || !(bool)$this->config->get('module_debug_logger_catalog_enable')
        ) {
            $this->response->setOutput(json_encode(['error' => 'Disabled']));
            return;
        }

        $this->load->model('extension/debug_logger/module/debug_logger');

        $severity = $this->request->post['severity'] ?? 'bug';
        if (!in_array($severity, ['bug', 'warning', 'info'])) {
            $severity = 'bug';
        }

        $id = $this->model_extension_debug_logger_module_debug_logger->addReport([
            'url'         => $this->request->post['url'] ?? '',
            'console_log' => $this->request->post['console_log'] ?? '',
            'comment'     => $this->request->post['comment'] ?? '',
            'severity'    => $severity,
            'admin_user'  => '',
            'source'      => 'catalog',
        ]);

        // License-aware pruning
        $license = (string)$this->config->get('module_debug_logger_license_key');
        $is_pro  = (bool)preg_match('/^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/i', $license);
        $max     = $is_pro ? (int)($this->config->get('module_debug_logger_max_reports') ?? 500) : 50;
        if ($max > 0) {
            $this->model_extension_debug_logger_module_debug_logger->pruneOldest($max);
        }

        $this->response->setOutput(json_encode(['success' => true, 'id' => $id]));
    }
}
