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

        // Ensure schema is up-to-date (route column may not exist yet)
        $this->model_extension_debug_logger_module_debug_logger->upgrade();

        $severity = $this->request->post['severity'] ?? 'bug';
        if (!in_array($severity, ['bug', 'warning', 'info'])) {
            $severity = 'bug';
        }

        $id = $this->model_extension_debug_logger_module_debug_logger->addReport([
            'url'          => html_entity_decode((string)($this->request->post['url'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'route'        => $this->request->post['route'] ?? '',
            'console_log'  => html_entity_decode((string)($this->request->post['console_log'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'comment'      => $this->request->post['comment'] ?? '',
            'loaded_files' => html_entity_decode((string)($this->request->post['loaded_files'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'severity'     => $severity,
            'admin_user'   => '',
            'source'       => 'catalog',
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
