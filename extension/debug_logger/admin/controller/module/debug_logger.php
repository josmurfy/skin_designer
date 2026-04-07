<?php
namespace Opencart\Admin\Controller\Extension\DebugLogger\Module;

class DebugLogger extends \Opencart\System\Engine\Controller {

    private array $error = [];

    public function index(): void {
        $this->load->language('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger_license');
        $this->load->model('setting/setting');

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->validate()) {
            $this->model_setting_setting->editSetting('module_debug_logger', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module'));
        }

        $tok = $this->session->data['user_token'];

        $data['breadcrumbs'] = [
            ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $tok)],
            ['text' => $this->language->get('text_extension'), 'href' => $this->url->link('marketplace/extension', 'user_token=' . $tok . '&type=module')],
            ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok)],
        ];

        $data['error_warning'] = $this->error['warning'] ?? '';
        $data['success'] = $this->session->data['success'] ?? '';
        unset($this->session->data['success']);

        $data['is_pro'] = $this->model_extension_debug_logger_module_debug_logger_license->isPro();
        $data['free_limit'] = 50;

        $defaults = [
            'module_debug_logger_status' => '0',
            'module_debug_logger_admin_enable' => '1',
            'module_debug_logger_catalog_enable' => '0',
            'module_debug_logger_capture_console' => '1',
            'module_debug_logger_capture_network' => '0',
            'module_debug_logger_capture_screenshot' => '0',
            'module_debug_logger_require_comment' => '0',
            'module_debug_logger_max_reports' => '500',
            'module_debug_logger_severity_bug' => '1',
            'module_debug_logger_severity_warning' => '1',
            'module_debug_logger_severity_info' => '1',
            'module_debug_logger_license_key' => '',
            'module_debug_logger_email_enable' => '0',
            'module_debug_logger_email_to' => '',
            'module_debug_logger_email_bug' => '1',
            'module_debug_logger_email_warning' => '0',
            'module_debug_logger_email_info' => '0',
            'module_debug_logger_webhook_type' => '',
            'module_debug_logger_webhook_url' => '',
        ];
        foreach ($defaults as $key => $default) {
            $data[$key] = $this->request->post[$key] ?? $this->config->get($key) ?? $default;
        }

        $data['total_reports'] = $this->model_extension_debug_logger_module_debug_logger->getTotalReports();
        $data['total_open'] = $this->model_extension_debug_logger_module_debug_logger->getTotalOpenReports();
        $data['total_admin'] = $this->model_extension_debug_logger_module_debug_logger->getTotalBySource('admin');
        $data['total_catalog'] = $this->model_extension_debug_logger_module_debug_logger->getTotalBySource('catalog');

        $data['action'] = $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $tok . '&type=module');
        $data['view_reports'] = $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok);
        $data['clear_url'] = $this->url->link('extension/debug_logger/module/debug_logger.clearReports', 'user_token=' . $tok);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/debug_logger/module/debug_logger', $data));
    }

    public function install(): void {
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('setting/event');

        $this->model_extension_debug_logger_module_debug_logger->createTable();
        $this->model_extension_debug_logger_module_debug_logger->upgrade();

        $this->model_setting_event->addEvent([
            'code' => 'debug_logger_admin',
            'description' => 'Debug Logger Admin Header',
            'trigger' => 'admin/view/common/header/after',
            'action' => 'extension/debug_logger/event/header.index',
            'status' => true,
            'sort_order' => 0,
        ]);
        $this->model_setting_event->addEvent([
            'code' => 'debug_logger_catalog',
            'description' => 'Debug Logger Catalog Header',
            'trigger' => 'catalog/view/common/header/after',
            'action' => 'extension/debug_logger/event/header.index',
            'status' => true,
            'sort_order' => 0,
        ]);
    }

    public function uninstall(): void {
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('setting/event');
        $this->load->model('setting/setting');

        $this->model_extension_debug_logger_module_debug_logger->dropTable();
        $this->model_setting_event->deleteEventByCode('debug_logger_admin');
        $this->model_setting_event->deleteEventByCode('debug_logger_catalog');
        $this->model_setting_setting->deleteSetting('module_debug_logger');
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
        $this->load->model('extension/debug_logger/module/debug_logger_license');

        $severity = strtolower((string)($this->request->post['severity'] ?? 'bug'));
        if (!in_array($severity, ['bug', 'warning', 'info'])) {
            $severity = 'bug';
        }

        $screenshot = '';
        $is_pro = $this->model_extension_debug_logger_module_debug_logger_license->isPro();
        if (
            $is_pro
            && $this->config->get('module_debug_logger_capture_screenshot')
            && !empty($this->request->post['screenshot'])
        ) {
            $ss = (string)$this->request->post['screenshot'];
            if (strpos($ss, 'data:image/') === 0 && strlen($ss) < 2097152) {
                $screenshot = $ss;
            }
        }

        $report_data = [
            'url' => substr((string)($this->request->post['url'] ?? ''), 0, 2048),
            'console_log' => substr((string)($this->request->post['console_log'] ?? ''), 0, 65535),
            'network_log' => substr((string)($this->request->post['network_log'] ?? ''), 0, 65535),
            'screenshot' => $screenshot,
            'comment' => substr((string)($this->request->post['comment'] ?? ''), 0, 4096),
            'severity' => $severity,
            'admin_user' => $this->user->isLogged() ? (string)$this->user->getUserName() : '',
            'source' => 'admin',
        ];

        $report_id = $this->model_extension_debug_logger_module_debug_logger->addReport($report_data);

        $max = $is_pro
            ? (int)($this->config->get('module_debug_logger_max_reports') ?? 500)
            : $this->model_extension_debug_logger_module_debug_logger_license->getMaxReports();
        if ($max > 0) {
            $this->model_extension_debug_logger_module_debug_logger->pruneOldest($max);
        }

        if ($is_pro) {
            $this->trySendEmail($report_id, $report_data);
            $this->trySendWebhook($report_id, $report_data);
        }

        $this->response->setOutput(json_encode(['success' => true, 'id' => $report_id]));
    }

    public function assignReport(): void {
        $this->response->addHeader('Content-Type: application/json');

        if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !isset($this->session->data['user_token'])) {
            $this->response->setOutput(json_encode(['error' => 'Forbidden']));
            return;
        }

        $this->load->model('extension/debug_logger/module/debug_logger');
        $rid = (int)($this->request->post['report_id'] ?? 0);
        $uid = (int)($this->request->post['user_id'] ?? 0);

        $this->model_extension_debug_logger_module_debug_logger->assignReport($rid, $uid);
        $this->response->setOutput(json_encode(['success' => true]));
    }

    public function reports(): void {
        if (
            !isset($this->session->data['user_token'])
            || ($this->request->get['user_token'] ?? '') !== $this->session->data['user_token']
        ) {
            $this->response->redirect($this->url->link('common/login'));
            return;
        }

        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger_license');
        $tok = $this->session->data['user_token'];
        $base_raw = str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok));
        $base_html = str_replace('&', '&amp;', $base_raw);
        $is_pro = $this->model_extension_debug_logger_module_debug_logger_license->isPro();

        if (isset($this->request->get['action'])) {
            $action = $this->request->get['action'];
            $rid = (int)($this->request->get['report_id'] ?? 0);

            if ($action === 'export' && $is_pro) {
                $format = $this->request->get['format'] ?? 'csv';
                $fs = isset($this->request->get['filter_status']) ? (int)$this->request->get['filter_status'] : -1;
                $this->exportReports($format, $fs, (string)($this->request->get['filter_source'] ?? ''));
                return;
            }

            switch ($action) {
                case 'open': $this->model_extension_debug_logger_module_debug_logger->setStatus($rid, 0); break;
                case 'close': $this->model_extension_debug_logger_module_debug_logger->setStatus($rid, 1); break;
                case 'delete': $this->model_extension_debug_logger_module_debug_logger->deleteReport($rid); break;
                case 'clear_all': $this->model_extension_debug_logger_module_debug_logger->clearAllReports(); break;
            }
            $qs = isset($this->request->get['filter_status']) ? '&filter_status=' . (int)$this->request->get['filter_status'] : '';
            $this->response->redirect($base_raw . $qs);
            return;
        }

        $filter_status = isset($this->request->get['filter_status']) ? (int)$this->request->get['filter_status'] : -1;
        $filter_source = (string)($this->request->get['filter_source'] ?? '');
        $results = $this->model_extension_debug_logger_module_debug_logger->getReports($filter_status, $filter_source);
        $total_open = $this->model_extension_debug_logger_module_debug_logger->getTotalOpenReports();
        $total = $this->model_extension_debug_logger_module_debug_logger->getTotalReports();
        $total_closed = $total - $total_open;

        $admin_users = $is_pro ? $this->model_extension_debug_logger_module_debug_logger->getAdminUsers() : [];
        $assign_url_raw = $is_pro ? str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger.assignReport', 'user_token=' . $tok)) : '';

        $sev_colors = ['bug' => '#ef4444', 'warning' => '#f59e0b', 'info' => '#3b82f6'];
        $src_colors = ['admin' => '#6366f1', 'catalog' => '#10b981'];
        $cfg_url_html = str_replace('&', '&amp;', str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok)));

        $h = '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        $h .= '<meta name="viewport" content="width=device-width,initial-scale=1">';
        $h .= '<title>Debug Logger - Reports</title>';
        $h .= '<style>';
        $h .= ':root{--bg:#0f172a;--card:#1e293b;--border:#334155;--text:#f1f5f9;--muted:#94a3b8}';
        $h .= '*{box-sizing:border-box}body{background:var(--bg);color:var(--text);font-family:system-ui,sans-serif;margin:0;padding:0}';
        $h .= '.container{max-width:1100px;margin:0 auto;padding:1.5rem 1rem}';
        $h .= '.page-header{display:flex;align-items:center;gap:.75rem;margin-bottom:1.5rem;flex-wrap:wrap}';
        $h .= '.page-header h1{margin:0;font-size:1.2rem;font-weight:700}';
        $h .= '.pro-badge{background:linear-gradient(135deg,#f59e0b,#eab308);color:#000;padding:.15rem .5rem;border-radius:4px;font-size:.65rem;font-weight:800;letter-spacing:.5px}';
        $h .= '.free-badge{background:#334155;color:#94a3b8;padding:.15rem .5rem;border-radius:4px;font-size:.65rem;font-weight:700}';
        $h .= '.toolbar{display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:1rem;align-items:center}';
        $h .= '.filter-btn{background:var(--card);border:1px solid var(--border);border-radius:20px;padding:.35rem .9rem;font-size:.78rem;color:var(--muted);text-decoration:none}';
        $h .= '.filter-btn.active{background:#1e3a5f;color:#93c5fd;border-color:#3b82f6}';
        $h .= '.stats{font-size:.72rem;color:var(--muted)}';
        $h .= '.export-btn{background:#14532d;border:1px solid #15803d;border-radius:6px;padding:.3rem .75rem;font-size:.78rem;color:#86efac;text-decoration:none}';
        $h .= '.clear-all{background:#7f1d1d;border:1px solid #991b1b;border-radius:6px;padding:.3rem .75rem;font-size:.78rem;color:#fca5a5;text-decoration:none;margin-left:auto}';
        $h .= '.report-card{background:var(--card);border:1px solid var(--border);border-radius:8px;padding:1rem;margin-bottom:.75rem}';
        $h .= '.badge{display:inline-flex;align-items:center;gap:4px;padding:.2rem .6rem;border-radius:12px;font-size:.75rem;font-weight:600;color:#fff}';
        $h .= '.meta{display:flex;align-items:center;gap:.5rem;flex-wrap:wrap;margin-bottom:.5rem;font-size:.78rem}';
        $h .= '.url-row{font-size:.78rem;color:var(--muted);word-break:break-all;margin:.4rem 0}';
        $h .= '.comment{background:#0f172a;border-left:3px solid #3b82f6;padding:.5rem .75rem;font-size:.8rem;border-radius:0 4px 4px 0;margin:.5rem 0;white-space:pre-wrap}';
        $h .= 'pre{background:#0f172a;border:1px solid var(--border);border-radius:6px;padding:.75rem;font-size:.72rem;overflow-x:auto;max-height:200px;margin:.5rem 0}';
        $h .= '.screenshot-thumb{max-width:250px;border-radius:6px;border:1px solid var(--border);margin:.5rem 0;cursor:pointer}';
        $h .= '.ss-overlay{position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.85);display:flex;align-items:center;justify-content:center;cursor:pointer}';
        $h .= '.ss-overlay img{max-width:95vw;max-height:95vh;border-radius:8px}';
        $h .= '.assign-select{background:var(--bg);color:var(--text);border:1px solid var(--border);border-radius:4px;font-size:.75rem;padding:.2rem .4rem}';
        $h .= '.actions{display:flex;gap:.5rem;flex-wrap:wrap;margin-top:.75rem;align-items:center}';
        $h .= '.btn{display:inline-flex;align-items:center;gap:4px;padding:.3rem .75rem;border-radius:6px;font-size:.78rem;text-decoration:none;border:1px solid}';
        $h .= '.btn-open{background:#14532d;border-color:#15803d;color:#86efac}';
        $h .= '.btn-close{background:#1e3a5f;border-color:#1d4ed8;color:#93c5fd}';
        $h .= '.btn-del{background:#7f1d1d;border-color:#991b1b;color:#fca5a5}';
        $h .= '.empty{text-align:center;padding:3rem;color:var(--muted)}';
        $h .= '.cfg-link{font-size:.78rem;color:var(--muted);text-decoration:none;margin-left:auto}';
        $h .= '.closed-card{opacity:.55}';
        $h .= '</style></head><body><div class="container">';

        $h .= '<div class="page-header">';
        $h .= '<h1>Debug Logger - Reports</h1>';
        $badge_class = $is_pro ? 'pro-badge' : 'free-badge';
        $badge_text = $is_pro ? 'PRO' : 'FREE';
        $h .= '<span class="' . $badge_class . '">' . $badge_text . '</span>';
        $h .= '<a href="' . $cfg_url_html . '" class="cfg-link">Settings</a>';
        $h .= '</div>';

        $h .= '<div class="toolbar">';
        $all_active = ($filter_status < 0 && !$filter_source) ? ' active' : '';
        $h .= '<a href="' . $base_html . '" class="filter-btn' . $all_active . '">All (' . $total . ')</a>';

        $open_active = ($filter_status === 0) ? ' active' : '';
        $h .= '<a href="' . $base_html . '&amp;filter_status=0" class="filter-btn' . $open_active . '">Open (' . $total_open . ')</a>';

        $closed_active = ($filter_status === 1 && !$filter_source) ? ' active' : '';
        $h .= '<a href="' . $base_html . '&amp;filter_status=1" class="filter-btn' . $closed_active . '">Closed (' . $total_closed . ')</a>';

        $admin_active = ($filter_source === 'admin') ? ' active' : '';
        $h .= '<a href="' . $base_html . '&amp;filter_source=admin" class="filter-btn' . $admin_active . '">Admin</a>';

        $catalog_active = ($filter_source === 'catalog') ? ' active' : '';
        $h .= '<a href="' . $base_html . '&amp;filter_source=catalog" class="filter-btn' . $catalog_active . '">Catalog</a>';

        $h .= '<span class="stats">Showing: ' . count($results) . '</span>';
        if ($is_pro) {
            $h .= '<a href="' . $base_html . '&amp;action=export&amp;format=csv" class="export-btn">CSV</a>';
            $h .= '<a href="' . $base_html . '&amp;action=export&amp;format=json" class="export-btn">JSON</a>';
        }
        if ($total > 0) {
            $h .= '<a href="' . $base_html . '&amp;action=clear_all" class="clear-all"';
            $h .= " onclick=\"return confirm('Delete ALL reports?')\">Clear All</a>";
        }
        $h .= '</div>';

        if (empty($results)) {
            $h .= '<div class="empty">No reports found.</div>';
        } else {
            foreach ($results as $row) {
                $sev = htmlspecialchars($row['severity']);
                $color = $sev_colors[$row['severity']] ?? '#ef4444';
                $src = $row['source'];
                $src_c = $src_colors[$src] ?? '#6b7280';
                $closed = (int)$row['status'] === 1;
                $rid = (int)$row['id'];
                $card_class = $closed ? 'report-card closed-card' : 'report-card';

                $h .= '<div class="' . $card_class . '">';
                $h .= '<div class="meta">';
                $h .= '<span class="badge" style="background:' . $color . '">' . $sev . '</span>';
                $h .= '<span class="badge" style="background:' . $src_c . '">' . htmlspecialchars($src) . '</span>';
                if (!empty($row['admin_user'])) {
                    $h .= '<span style="color:var(--muted)">' . htmlspecialchars($row['admin_user']) . '</span>';
                }
                if ($is_pro && !empty($admin_users)) {
                    $assigned = (int)($row['assigned_to'] ?? 0);
                    $h .= '<select class="assign-select" data-rid="' . $rid . '">';
                    $h .= '<option value="0">-- Unassigned --</option>';
                    foreach ($admin_users as $u) {
                        $sel = ($assigned === (int)$u['user_id']) ? ' selected' : '';
                        $uname = htmlspecialchars($u['firstname'] . ' ' . $u['lastname']);
                        $h .= '<option value="' . (int)$u['user_id'] . '"' . $sel . '>' . $uname . '</option>';
                    }
                    $h .= '</select>';
                }
                $date_str = htmlspecialchars((string)($row['date_added'] ?? ''));
                $h .= '<span style="color:var(--muted);margin-left:auto">#' . $rid . ' &middot; ' . $date_str . '</span>';
                $h .= '</div>';

                if (!empty($row['url'])) {
                    $h .= '<div class="url-row">' . htmlspecialchars($row['url']) . '</div>';
                }
                if (!empty($row['comment']) && trim((string)$row['comment'])) {
                    $h .= '<div class="comment">' . nl2br(htmlspecialchars($row['comment'])) . '</div>';
                }
                if (!empty($row['console_log']) && trim((string)$row['console_log'])) {
                    $h .= '<pre>' . htmlspecialchars($row['console_log']) . '</pre>';
                }
                if (!empty($row['network_log']) && trim((string)$row['network_log'])) {
                    $h .= '<details><summary style="cursor:pointer;font-size:.78rem;color:var(--muted)">Network log</summary>';
                    $h .= '<pre>' . htmlspecialchars($row['network_log']) . '</pre></details>';
                }
                if (!empty($row['screenshot'])) {
                    $ss_src = htmlspecialchars($row['screenshot']);
                    $h .= '<img class="screenshot-thumb" src="' . $ss_src . '" alt="Screenshot" onclick="dlShowSS(this.src)">';
                }

                $h .= '<div class="actions">';
                if ($closed) {
                    $h .= '<a href="' . $base_html . '&amp;action=open&amp;report_id=' . $rid . '" class="btn btn-open">Reopen</a>';
                } else {
                    $h .= '<a href="' . $base_html . '&amp;action=close&amp;report_id=' . $rid . '" class="btn btn-close">Close</a>';
                }
                $h .= '<a href="' . $base_html . '&amp;action=delete&amp;report_id=' . $rid . '"';
                $h .= " class=\"btn btn-del\" onclick=\"return confirm('Delete?')\">Delete</a>";
                $h .= '</div></div>';
            }
        }

        $h .= '<script>';
        $h .= 'function dlShowSS(src){';
        $h .= 'var d=document.createElement("div");d.className="ss-overlay";';
        $h .= 'd.onclick=function(){d.remove()};';
        $h .= 'd.innerHTML="<img src=\'"+src+"\'>";';
        $h .= 'document.body.appendChild(d)}';
        if ($is_pro && !empty($admin_users)) {
            $h .= "\n";
            $h .= 'document.querySelectorAll(".assign-select").forEach(function(sel){';
            $h .= 'sel.addEventListener("change",function(){';
            $assign_safe = addslashes($assign_url_raw);
            $h .= 'fetch("' . $assign_safe . '",{method:"POST",';
            $h .= 'headers:{"Content-Type":"application/x-www-form-urlencoded"},';
            $h .= 'body:"report_id="+this.dataset.rid+"&user_id="+this.value});';
            $h .= '});});';
        }
        $h .= '</script>';

        $h .= '</div></body></html>';
        $this->response->setOutput($h);
    }

    private function exportReports(string $format, int $filter_status, string $filter_source): void {
        $reports = $this->model_extension_debug_logger_module_debug_logger->getReports($filter_status, $filter_source);

        if ($format === 'json') {
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="debug_reports_' . date('Y-m-d_His') . '.json"');
            $clean = array_map(function ($r) {
                unset($r['screenshot']);
                return $r;
            }, $reports);
            echo json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="debug_reports_' . date('Y-m-d_His') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Date', 'Severity', 'Source', 'URL', 'User', 'Comment', 'Console Errors', 'Status']);
        foreach ($reports as $row) {
            fputcsv($out, [
                $row['id'],
                $row['date_added'] ?? '',
                $row['severity'],
                $row['source'],
                $row['url'],
                $row['admin_user'] ?? '',
                $row['comment'],
                $row['console_log'] ?? '',
                (int)($row['status'] ?? 0) === 1 ? 'Closed' : 'Open',
            ]);
        }
        fclose($out);
        exit;
    }

    private function trySendEmail(int $report_id, array $data): void {
        try {
            if (!$this->config->get('module_debug_logger_email_enable')) {
                return;
            }
            $sev_key = 'module_debug_logger_email_' . ($data['severity'] ?? 'bug');
            if (!$this->config->get($sev_key)) {
                return;
            }
            $to = (string)$this->config->get('module_debug_logger_email_to');
            if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                return;
            }

            $subject = '[Debug Logger] ' . strtoupper($data['severity']) . ' Report #' . $report_id;
            $body  = "New debug report:\n\n";
            $body .= "ID:       #" . $report_id . "\n";
            $body .= "Severity: " . $data['severity'] . "\n";
            $body .= "Source:   " . ($data['source'] ?? 'admin') . "\n";
            $body .= "URL:      " . ($data['url'] ?? '') . "\n";
            $body .= "User:     " . ($data['admin_user'] ?? 'Guest') . "\n";
            $body .= "Date:     " . date('Y-m-d H:i:s') . "\n\n";
            if (!empty($data['comment'])) {
                $body .= "Comment:\n" . $data['comment'] . "\n\n";
            }
            if (!empty($data['console_log'])) {
                $body .= "Console:\n" . substr($data['console_log'], 0, 2000) . "\n";
            }

            $mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine'));
            $mail->parameter = $this->config->get('config_mail_parameter');
            $mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
            $mail->smtp_username = $this->config->get('config_mail_smtp_username');
            $mail->smtp_password = html_entity_decode(
                (string)$this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'
            );
            $mail->smtp_port = $this->config->get('config_mail_smtp_port');
            $mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
            $mail->setTo($to);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode(
                (string)$this->config->get('config_name'), ENT_QUOTES, 'UTF-8'
            ));
            $mail->setSubject($subject);
            $mail->setText($body);
            $mail->send();
        } catch (\Throwable $e) {
            $this->log->write('Debug Logger email error: ' . $e->getMessage());
        }
    }

    private function trySendWebhook(int $report_id, array $data): void {
        try {
            $type = (string)$this->config->get('module_debug_logger_webhook_type');
            $url = (string)$this->config->get('module_debug_logger_webhook_url');
            if (!$type || !$url || !filter_var($url, FILTER_VALIDATE_URL)) {
                return;
            }

            $payload = ($type === 'discord')
                ? $this->buildDiscordPayload($report_id, $data)
                : $this->buildSlackPayload($report_id, $data);

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_TIMEOUT => 5,
            ]);
            curl_exec($ch);
            curl_close($ch);
        } catch (\Throwable $e) {
            $this->log->write('Debug Logger webhook error: ' . $e->getMessage());
        }
    }

    private function buildSlackPayload(int $id, array $d): array {
        $emoji = ['bug' => ':beetle:', 'warning' => ':warning:', 'info' => ':information_source:'];
        $color = ['bug' => '#ef4444', 'warning' => '#f59e0b', 'info' => '#3b82f6'];
        $sev = $d['severity'] ?? 'bug';
        return [
            'text' => ($emoji[$sev] ?? '') . ' Debug Report #' . $id,
            'attachments' => [[
                'color' => $color[$sev] ?? '#6b7280',
                'fields' => [
                    ['title' => 'Severity', 'value' => strtoupper($sev), 'short' => true],
                    ['title' => 'Source', 'value' => ucfirst($d['source'] ?? 'admin'), 'short' => true],
                    ['title' => 'URL', 'value' => $d['url'] ?? '', 'short' => false],
                    ['title' => 'Comment', 'value' => $d['comment'] ?: '_No comment_', 'short' => false],
                ],
                'footer' => 'Debug Logger Pro',
                'ts' => time(),
            ]],
        ];
    }

    private function buildDiscordPayload(int $id, array $d): array {
        $color = ['bug' => 0xef4444, 'warning' => 0xf59e0b, 'info' => 0x3b82f6];
        $sev = $d['severity'] ?? 'bug';
        return [
            'embeds' => [[
                'title' => 'Debug Report #' . $id,
                'color' => $color[$sev] ?? 0x6b7280,
                'fields' => [
                    ['name' => 'Severity', 'value' => strtoupper($sev), 'inline' => true],
                    ['name' => 'Source', 'value' => ucfirst($d['source'] ?? 'admin'), 'inline' => true],
                    ['name' => 'URL', 'value' => $d['url'] ?? ''],
                    ['name' => 'Comment', 'value' => $d['comment'] ?: '_No comment_'],
                ],
                'timestamp' => date('c'),
                'footer' => ['text' => 'Debug Logger Pro'],
            ]],
        ];
    }

    public function clearReports(): void {
        if (
            !isset($this->session->data['user_token'])
            || ($this->request->get['user_token'] ?? '') !== $this->session->data['user_token']
        ) {
            $this->response->redirect($this->url->link('common/login'));
            return;
        }

        $this->load->language('extension/debug_logger/module/debug_logger');
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
