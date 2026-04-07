<?php
namespace Opencart\Admin\Controller\Extension\DebugLogger\Module;

class DebugLogger extends \Opencart\System\Engine\Controller {

    private array $error = [];

    public function index(): void {
        $this->load->language('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->validate()) {
            $this->model_setting_setting->editSetting('module_debug_logger', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link(
                'marketplace/extension',
                'user_token=' . $this->session->data['user_token'] . '&type=module'
            ));
        }

        $tok = $this->session->data['user_token'];

        $data['breadcrumbs'] = [
            ['text' => $this->language->get('text_home'),      'href' => $this->url->link('common/dashboard',      'user_token=' . $tok)],
            ['text' => $this->language->get('text_extension'), 'href' => $this->url->link('marketplace/extension', 'user_token=' . $tok . '&type=module')],
            ['text' => $this->language->get('heading_title'),  'href' => $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok)],
        ];

        $data['error_warning'] = $this->error['warning'] ?? '';
        $data['success']       = $this->session->data['success'] ?? '';
        unset($this->session->data['success']);

        $defaults = [
            'module_debug_logger_status'          => '0',
            'module_debug_logger_admin_enable'     => '1',
            'module_debug_logger_catalog_enable'   => '0',
            'module_debug_logger_capture_console'  => '1',
            'module_debug_logger_capture_network'  => '0',
            'module_debug_logger_require_comment'  => '0',
            'module_debug_logger_max_reports'      => '500',
            'module_debug_logger_severity_bug'     => '1',
            'module_debug_logger_severity_warning' => '1',
            'module_debug_logger_severity_info'    => '1',
        ];
        foreach ($defaults as $key => $default) {
            $data[$key] = $this->request->post[$key] ?? $this->config->get($key) ?? $default;
        }

        $data['total_reports'] = $this->model_extension_debug_logger_module_debug_logger->getTotalReports();
        $data['total_open']    = $this->model_extension_debug_logger_module_debug_logger->getTotalOpenReports();
        $data['total_admin']   = $this->model_extension_debug_logger_module_debug_logger->getTotalBySource('admin');
        $data['total_catalog'] = $this->model_extension_debug_logger_module_debug_logger->getTotalBySource('catalog');

        $data['action']       = $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok);
        $data['cancel']       = $this->url->link('marketplace/extension', 'user_token=' . $tok . '&type=module');
        $data['view_reports'] = $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok);
        $data['clear_url']    = $this->url->link('extension/debug_logger/module/debug_logger.clearReports', 'user_token=' . $tok);

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/debug_logger/module/debug_logger', $data));
    }

    public function install(): void {
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('setting/event');

        $this->model_extension_debug_logger_module_debug_logger->createTable();

        $this->model_setting_event->addEvent([
            'code'        => 'debug_logger_admin',
            'description' => 'Debug Logger Admin Header',
            'trigger'     => 'admin/view/common/header/before',
            'action'      => 'extension/debug_logger/event/header.index',
            'status'      => true,
            'sort_order'  => 0,
        ]);
        $this->model_setting_event->addEvent([
            'code'        => 'debug_logger_catalog',
            'description' => 'Debug Logger Catalog Header',
            'trigger'     => 'catalog/view/common/header/before',
            'action'      => 'extension/debug_logger/event/header.index',
            'status'      => true,
            'sort_order'  => 0,
        ]);
    }

    public function uninstall(): void {
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('setting/event');
        $this->load->model('setting/setting');

        // 1. Drop custom DB table
        $this->model_extension_debug_logger_module_debug_logger->dropTable();

        // 2. Remove registered events — once gone, the event handler never fires,
        //    so all {% if debug_logger_*_enable %} blocks in the OCMOD-patched twig
        //    evaluate to false and render nothing. Safe even if the twig still exists.
        $this->model_setting_event->deleteEventByCode('debug_logger_admin');
        $this->model_setting_event->deleteEventByCode('debug_logger_catalog');

        // 3. Remove all module settings from DB
        $this->model_setting_setting->deleteSetting('module_debug_logger');

        // NOTE: We intentionally do NOT delete extension/ocmod/.../header.twig here.
        // That file is shared and may contain patches from other active extensions.
        // Deleting it would break those extensions. The {% if %} guards above are
        // sufficient — no toolbar appears once events are gone.
        // After full extension removal, the user's normal Modifications > Refresh
        // will regenerate the twig without our patches.
    }

    public function debugSave(): void {
        $this->response->addHeader('Content-Type: application/json');

        if (
            $this->request->server['REQUEST_METHOD'] !== 'POST'
            || !isset($this->session->data['user_token'])
            || !$this->config->get('module_debug_logger_status')
            || !$this->config->get('module_debug_logger_admin_enable')
        ) {
            $this->response->setOutput(json_encode(['error' => 'Forbidden']));
            return;
        }

        $this->load->model('extension/debug_logger/module/debug_logger');

        $severity = strtolower((string)($this->request->post['severity'] ?? 'bug'));
        if (!in_array($severity, ['bug', 'warning', 'info'])) {
            $severity = 'bug';
        }

        $this->model_extension_debug_logger_module_debug_logger->addReport([
            'url'         => substr((string)($this->request->post['url'] ?? ''), 0, 2048),
            'console_log' => substr((string)($this->request->post['console_log'] ?? ''), 0, 65535),
            'network_log' => substr((string)($this->request->post['network_log'] ?? ''), 0, 65535),
            'comment'     => substr((string)($this->request->post['comment'] ?? ''), 0, 1024),
            'severity'    => $severity,
            'admin_user'  => $this->user->isLogged() ? (string)$this->user->getUserName() : '',
            'source'      => 'admin',
        ]);

        $max = (int)($this->config->get('module_debug_logger_max_reports') ?? 500);
        if ($max > 0) {
            $this->model_extension_debug_logger_module_debug_logger->pruneOldest($max);
        }

        $this->response->setOutput(json_encode(['success' => true]));
    }

    public function reports(): void {
        if (
            !isset($this->session->data['user_token'])
            || $this->request->get['user_token'] !== $this->session->data['user_token']
        ) {
            $this->response->redirect($this->url->link('common/login'));
            return;
        }

        $this->load->model('extension/debug_logger/module/debug_logger');
        $tok  = $this->session->data['user_token'];
        $base = $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok);

        if (isset($this->request->get['action'])) {
            $rid = (int)($this->request->get['report_id'] ?? 0);
            switch ($this->request->get['action']) {
                case 'open':      $this->model_extension_debug_logger_module_debug_logger->setStatus($rid, 0); break;
                case 'close':     $this->model_extension_debug_logger_module_debug_logger->setStatus($rid, 1); break;
                case 'delete':    $this->model_extension_debug_logger_module_debug_logger->deleteReport($rid); break;
                case 'clear_all': $this->model_extension_debug_logger_module_debug_logger->clearAllReports(); break;
            }
            $qs = isset($this->request->get['filter_status']) ? '&filter_status=' . (int)$this->request->get['filter_status'] : '';
            $this->response->redirect($base . $qs);
            return;
        }

        $filter_status = isset($this->request->get['filter_status']) ? (int)$this->request->get['filter_status'] : -1;
        $filter_source = (string)($this->request->get['filter_source'] ?? '');
        $results       = $this->model_extension_debug_logger_module_debug_logger->getReports($filter_status, $filter_source);
        $total_open    = $this->model_extension_debug_logger_module_debug_logger->getTotalOpenReports();
        $total         = $this->model_extension_debug_logger_module_debug_logger->getTotalReports();
        $total_closed  = $total - $total_open;

        $sev_colors = ['bug' => '#ef4444', 'warning' => '#f59e0b', 'info' => '#3b82f6'];
        $src_colors = ['admin' => '#6366f1', 'catalog' => '#10b981'];
        $cfg_url    = $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok);

        $h  = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
        $h .= '<title>Debug Logger - Reports</title>';
        $h .= '<style>';
        $h .= ':root{--bg:#0f172a;--card:#1e293b;--border:#334155;--text:#f1f5f9;--muted:#94a3b8}';
        $h .= '*{box-sizing:border-box}body{background:var(--bg);color:var(--text);font-family:system-ui,sans-serif;margin:0;padding:0}';
        $h .= '.container{max-width:1100px;margin:0 auto;padding:1.5rem 1rem}';
        $h .= '.page-header{display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap}';
        $h .= '.page-header h1{margin:0;font-size:1.2rem;font-weight:700}';
        $h .= '.filters{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem;align-items:center}';
        $h .= '.filter-btn{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:.35rem .9rem;font-size:.78rem;color:var(--muted);text-decoration:none}';
        $h .= '.filter-btn.active{background:#1e3a5f;color:#93c5fd;border-color:#3b82f6}';
        $h .= '.stats{margin-left:auto;font-size:.72rem;color:var(--muted)}';
        $h .= '.clear-all{background:#7f1d1d;border:1px solid #991b1b;border-radius:6px;padding:.3rem .75rem;font-size:.78rem;color:#fca5a5;text-decoration:none;margin-left:.5rem}';
        $h .= '.report-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1rem;margin-bottom:.75rem}';
        $h .= '.badge{display:inline-flex;align-items:center;gap:4px;padding:.2rem .6rem;border-radius:12px;font-size:.75rem;font-weight:600;color:#fff}';
        $h .= '.meta{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.5rem;font-size:.78rem}';
        $h .= '.url-row{font-size:.78rem;color:var(--muted);word-break:break-all;margin:.4rem 0}';
        $h .= '.comment{background:#0f172a;border-left:3px solid #3b82f6;padding:.5rem .75rem;font-size:.8rem;border-radius:0 4px 4px 0;margin:.5rem 0}';
        $h .= 'pre{background:#0f172a;border:1px solid var(--border);border-radius:6px;padding:.75rem;font-size:.72rem;overflow-x:auto;max-height:200px;margin:.5rem 0}';
        $h .= '.actions{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.75rem}';
        $h .= '.btn{display:inline-flex;align-items:center;gap:4px;padding:.3rem .75rem;border-radius:6px;font-size:.78rem;text-decoration:none;border:1px solid}';
        $h .= '.btn-open{background:#14532d;border-color:#15803d;color:#86efac}';
        $h .= '.btn-close{background:#1e3a5f;border-color:#1d4ed8;color:#93c5fd}';
        $h .= '.btn-del{background:#7f1d1d;border-color:#991b1b;color:#fca5a5}';
        $h .= '.empty{text-align:center;padding:3rem;color:var(--muted)}';
        $h .= '.cfg-link{font-size:.78rem;color:var(--muted);text-decoration:none;margin-left:auto}';
        $h .= '.closed-card{opacity:.55}';
        $h .= '</style></head><body><div class="container">';

        $h .= '<div class="page-header"><h1>Debug Logger - Reports</h1>';
        $h .= '<a href="' . htmlspecialchars($cfg_url) . '" class="cfg-link">Settings</a></div>';

        $h .= '<div class="filters">';
        $h .= '<a href="' . htmlspecialchars($base) . '" class="filter-btn' . ($filter_status < 0 && !$filter_source ? ' active' : '') . '">All (' . $total . ')</a>';
        $h .= '<a href="' . htmlspecialchars($base . '&filter_status=0') . '" class="filter-btn' . ($filter_status === 0 ? ' active' : '') . '">Open (' . $total_open . ')</a>';
        $h .= '<a href="' . htmlspecialchars($base . '&filter_status=1') . '" class="filter-btn' . ($filter_status === 1 && !$filter_source ? ' active' : '') . '">Closed (' . $total_closed . ')</a>';
        $h .= '<a href="' . htmlspecialchars($base . '&filter_source=admin') . '" class="filter-btn' . ($filter_source === 'admin' ? ' active' : '') . '">Admin</a>';
        $h .= '<a href="' . htmlspecialchars($base . '&filter_source=catalog') . '" class="filter-btn' . ($filter_source === 'catalog' ? ' active' : '') . '">Catalog</a>';
        $h .= '<span class="stats">Showing: ' . count($results) . '</span>';
        if ($total > 0) {
            $h .= '<a href="' . htmlspecialchars($base . '&action=clear_all') . '" class="clear-all" onclick="return confirm(\'Delete ALL reports?\')">Clear All</a>';
        }
        $h .= '</div>';

        if (empty($results)) {
            $h .= '<div class="empty">No reports found.</div>';
        } else {
            foreach ($results as $row) {
                $sev    = htmlspecialchars($row['severity']);
                $color  = $sev_colors[$row['severity']] ?? '#ef4444';
                $src    = $row['source'];
                $src_c  = $src_colors[$src] ?? '#6b7280';
                $closed = (int)$row['status'] === 1;
                $rid    = (int)$row['debug_log_id'];

                $h .= '<div class="report-card' . ($closed ? ' closed-card' : '') . '">';
                $h .= '<div class="meta">';
                $h .= '<span class="badge" style="background:' . $color . '">' . $sev . '</span>';
                $h .= '<span class="badge" style="background:' . $src_c . '">' . htmlspecialchars($src) . '</span>';
                if ($row['admin_user']) {
                    $h .= '<span style="color:var(--muted)">' . htmlspecialchars($row['admin_user']) . '</span>';
                }
                $h .= '<span style="color:var(--muted);margin-left:auto">' . htmlspecialchars($row['created_at']) . '</span>';
                $h .= '</div>';

                if ($row['url']) {
                    $h .= '<div class="url-row">' . htmlspecialchars($row['url']) . '</div>';
                }
                if (trim((string)$row['comment'])) {
                    $h .= '<div class="comment">' . htmlspecialchars($row['comment']) . '</div>';
                }
                if (trim((string)$row['console_log'])) {
                    $h .= '<pre>' . htmlspecialchars($row['console_log']) . '</pre>';
                }
                if (trim((string)$row['network_log'])) {
                    $h .= '<details><summary style="cursor:pointer;font-size:.78rem;color:var(--muted)">Network log</summary><pre>' . htmlspecialchars($row['network_log']) . '</pre></details>';
                }

                $h .= '<div class="actions">';
                if ($closed) {
                    $h .= '<a href="' . htmlspecialchars($base . '&action=open&report_id=' . $rid) . '" class="btn btn-open">Reopen</a>';
                } else {
                    $h .= '<a href="' . htmlspecialchars($base . '&action=close&report_id=' . $rid) . '" class="btn btn-close">Close</a>';
                }
                $h .= '<a href="' . htmlspecialchars($base . '&action=delete&report_id=' . $rid) . '" class="btn btn-del" onclick="return confirm(\'Delete?\')">Delete</a>';
                $h .= '</div>';
                $h .= '</div>';
            }
        }

        $h .= '</div></body></html>';
        $this->response->setOutput($h);
    }

    public function clearReports(): void {
        if (
            !isset($this->session->data['user_token'])
            || $this->request->get['user_token'] !== $this->session->data['user_token']
        ) {
            $this->response->redirect($this->url->link('common/login'));
            return;
        }

        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->model_extension_debug_logger_module_debug_logger->clearAllReports();

        $this->session->data['success'] = $this->language->get('text_success_clear');
        $this->response->redirect($this->url->link(
            'extension/debug_logger/module/debug_logger',
            'user_token=' . $this->session->data['user_token']
        ));
    }

    private function validate(): bool {
        if (!$this->user->hasPermission('modify', 'extension/debug_logger/module/debug_logger')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        return empty($this->error);
    }
}
