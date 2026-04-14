<?php
namespace Opencart\Admin\Controller\Extension\DebugLogger\Module;

class DebugLogger extends \Opencart\System\Engine\Controller {

    private const VERSION = '3.3.3';
    private array $error = [];

    public function index(): void {
        $this->load->language('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger_license');
        $this->load->model('setting/setting');

        // Auto-fix: deduplicate events + upgrade schema on settings page load
        $this->model_extension_debug_logger_module_debug_logger->upgrade();

        $this->document->setTitle($this->language->get('heading_title'));

        if ($this->request->server['REQUEST_METHOD'] === 'POST' && $this->validate()) {
            $post = $this->request->post;
            // Encode allowed groups as JSON for storage
            if (isset($post['module_debug_logger_allowed_groups']) && is_array($post['module_debug_logger_allowed_groups'])) {
                $post['module_debug_logger_allowed_groups'] = json_encode(array_map('intval', $post['module_debug_logger_allowed_groups']));
            } else {
                $post['module_debug_logger_allowed_groups'] = '[]';
            }
            $this->model_setting_setting->editSetting('module_debug_logger', $post);
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
            'module_debug_logger_btn_color' => '#dc2626',
            'module_debug_logger_header_color' => '#1e293b',
            'module_debug_logger_accent_color' => '#3b82f6',
            'module_debug_logger_btn_position' => 'navbar',
            'module_debug_logger_btn_size' => 'medium',
        ];
        foreach ($defaults as $key => $default) {
            $val = $this->request->post[$key] ?? $this->config->get($key);
            $data[$key] = (isset($val) && $val !== '') ? $val : $default;
        }

        // Update check
        $data['current_version'] = self::VERSION;
        $data['check_update_url'] = html_entity_decode($this->url->link('extension/debug_logger/module/debug_logger.checkUpdate', 'user_token=' . $tok . '&flush=1'));
        $data['install_update_url'] = html_entity_decode($this->url->link('extension/debug_logger/module/debug_logger.installUpdate', 'user_token=' . $tok));

        // Appearance defaults for reset
        $data['default_btn_color'] = '#dc2626';
        $data['default_header_color'] = '#1e293b';
        $data['default_accent_color'] = '#3b82f6';
        $data['default_btn_position'] = 'navbar';
        $data['default_btn_size'] = 'medium';

        $data['total_reports'] = $this->model_extension_debug_logger_module_debug_logger->getTotalReports();
        $data['total_open'] = $this->model_extension_debug_logger_module_debug_logger->getTotalOpenReports();
        $data['total_admin'] = $this->model_extension_debug_logger_module_debug_logger->getTotalBySource('admin');
        $data['total_catalog'] = $this->model_extension_debug_logger_module_debug_logger->getTotalBySource('catalog');

        $data['github_repo'] = 'josmurfy/debug-logger-releases';
        $data['action'] = $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok);
        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $tok . '&type=module');
        $data['view_reports'] = $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok);
        $data['analytics_url'] = $this->url->link('extension/debug_logger/module/debug_logger.analytics', 'user_token=' . $tok);
        $data['clear_url'] = $this->url->link('extension/debug_logger/module/debug_logger.clearReports', 'user_token=' . $tok);
        $data['test_email_url'] = html_entity_decode($this->url->link('extension/debug_logger/module/debug_logger.testEmail', 'user_token=' . $tok));

        // User groups for permissions tab
        $this->load->model('user/user_group');
        $data['user_groups'] = $this->model_user_user_group->getUserGroups();
        $saved_groups = $this->request->post['module_debug_logger_allowed_groups']
            ?? (($raw = $this->config->get('module_debug_logger_allowed_groups')) ? json_decode($raw, true) : []);
        $data['allowed_groups'] = is_array($saved_groups) ? array_map('intval', $saved_groups) : [];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/debug_logger/module/debug_logger', $data));
    }

    /**
     * AJAX: Check GitHub Releases for updates.
     * Caches result for 6 hours in oc_setting.
     */
    public function checkUpdate(): void {
        $this->load->language('extension/debug_logger/module/debug_logger');

        $json = [];
        $current = self::VERSION;
        $repo = 'josmurfy/debug-logger-releases';
        $cache_key = 'module_debug_logger_update_cache';
        $cache_ts_key = 'module_debug_logger_update_cache_ts';
        $flush = !empty($this->request->get['flush']);

        // Check cache (6h) — skip if flush requested
        if (!$flush) {
            $cached_ts = (int)$this->config->get($cache_ts_key);
            if ($cached_ts && (time() - $cached_ts) < 21600) {
                $cached = $this->config->get($cache_key);
                if ($cached) {
                    $json = json_decode($cached, true) ?: [];
                    $json['from_cache'] = true;
                    $this->response->addHeader('Content-Type: application/json');
                    $this->response->setOutput(json_encode($json));
                    return;
                }
            }
        }

        // Call GitHub API — fetch all releases (includes latest)
        $url = 'https://api.github.com/repos/' . $repo . '/releases?per_page=20';
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Accept: application/vnd.github.v3+json',
                'User-Agent: DebugLoggerOC4',
            ],
        ]);
        $response = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200 || !$response) {
            $json['error'] = true;
            $json['message'] = $this->language->get('text_update_error');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $all_releases = json_decode($response, true);
        if (!is_array($all_releases) || empty($all_releases)) {
            $json['error'] = true;
            $json['message'] = $this->language->get('text_update_error');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Latest = first non-prerelease, non-draft
        $release = $all_releases[0];
        $latest = ltrim($release['tag_name'] ?? '', 'vV');
        $download_url = '';
        foreach ($release['assets'] ?? [] as $asset) {
            if (str_ends_with($asset['name'], '.zip')) {
                $download_url = $asset['browser_download_url'];
                break;
            }
        }

        // Build version history list
        $versions = [];
        foreach ($all_releases as $rel) {
            if (!empty($rel['draft'])) continue;
            $ver = ltrim($rel['tag_name'] ?? '', 'vV');
            $dl = '';
            foreach ($rel['assets'] ?? [] as $a) {
                if (str_ends_with($a['name'], '.zip')) {
                    $dl = $a['browser_download_url'];
                    break;
                }
            }
            $versions[] = [
                'version'      => $ver,
                'tag'          => $rel['tag_name'] ?? '',
                'changelog'    => $rel['body'] ?? '',
                'published_at' => $rel['published_at'] ?? '',
                'html_url'     => $rel['html_url'] ?? '',
                'download_url' => $dl,
                'is_current'   => version_compare($ver, $current, '=='),
            ];
        }

        $json = [
            'current_version' => $current,
            'latest_version'  => $latest,
            'has_update'      => version_compare($latest, $current, '>'),
            'download_url'    => $download_url,
            'changelog'       => $release['body'] ?? '',
            'published_at'    => $release['published_at'] ?? '',
            'html_url'        => $release['html_url'] ?? '',
            'versions'        => $versions,
            'from_cache'      => false,
        ];

        // Cache result
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_debug_logger_update', [
            $cache_key    => json_encode($json),
            $cache_ts_key => (string)time(),
        ]);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * AJAX: Download and install update from GitHub release asset.
     */
    public function installUpdate(): void {
        $this->load->language('extension/debug_logger/module/debug_logger');
        $this->response->addHeader('Content-Type: application/json');

        if (!$this->user->hasPermission('modify', 'extension/debug_logger/module/debug_logger')) {
            $this->response->setOutput(json_encode(['error' => true, 'message' => $this->language->get('error_permission')]));
            return;
        }

        $download_url = $this->request->get['download_url'] ?? '';
        if (!$download_url || !str_starts_with($download_url, 'https://github.com/josmurfy/')) {
            $this->response->setOutput(json_encode(['error' => true, 'message' => 'Invalid download URL']));
            return;
        }

        // Download ZIP to temp
        $tmp_file = tempnam(sys_get_temp_dir(), 'dl_update_') . '.zip';
        $ch = curl_init($download_url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS      => 5,
            CURLOPT_HTTPHEADER     => ['User-Agent: DebugLoggerOC4'],
        ]);
        $zip_data = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 200 || !$zip_data || strlen($zip_data) < 1000) {
            @unlink($tmp_file);
            $this->response->setOutput(json_encode(['error' => true, 'message' => $this->language->get('text_install_download_error')]));
            return;
        }
        file_put_contents($tmp_file, $zip_data);

        // Extract ZIP
        $zip = new \ZipArchive();
        if ($zip->open($tmp_file) !== true) {
            @unlink($tmp_file);
            $this->response->setOutput(json_encode(['error' => true, 'message' => $this->language->get('text_install_extract_error')]));
            return;
        }

        $ext_dir = DIR_EXTENSION . 'debug_logger/';
        $tmp_extract = sys_get_temp_dir() . '/dl_update_extract_' . time() . '/';
        $zip->extractTo($tmp_extract);
        $zip->close();
        @unlink($tmp_file);

        // Copy files over existing extension
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($tmp_extract, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $copied = 0;
        $failed = [];
        foreach ($iterator as $item) {
            $relative = str_replace($tmp_extract, '', $item->getPathname());
            $target = $ext_dir . $relative;
            if ($item->isDir()) {
                if (!is_dir($target)) {
                    @mkdir($target, 0755, true);
                }
            } else {
                if (is_file($target)) {
                    @unlink($target);
                }
                if (@copy($item->getPathname(), $target)) {
                    @chmod($target, 0644);
                    $copied++;
                } else {
                    $failed[] = $relative;
                }
            }
        }

        // Cleanup temp extraction
        $this->rrmdir($tmp_extract);

        if ($copied === 0 && !empty($failed)) {
            $this->response->setOutput(json_encode(['error' => true, 'message' => 'Permission denied: could not overwrite files. Check file ownership (chown).']));
            return;
        }

        // Clear update cache
        $this->load->model('setting/setting');
        $this->model_setting_setting->editSetting('module_debug_logger_update', [
            'module_debug_logger_update_cache'    => '',
            'module_debug_logger_update_cache_ts'  => '0',
        ]);

        // Read new version from install.json
        $new_version = self::VERSION;
        $install_json = $ext_dir . 'install.json';
        if (is_file($install_json)) {
            $info = json_decode(file_get_contents($install_json), true);
            $new_version = $info['version'] ?? $new_version;
        }

        $this->response->setOutput(json_encode([
            'success'     => true,
            'message'     => sprintf($this->language->get('text_install_success'), $new_version),
            'new_version' => $new_version,
            'files_count' => $copied,
        ]));
    }

    private function rrmdir(string $dir): void {
        if (!is_dir($dir)) return;
        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }
        rmdir($dir);
    }

    public function install(): void {
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('setting/event');

        $this->model_extension_debug_logger_module_debug_logger->createTable();
        $this->model_extension_debug_logger_module_debug_logger->upgrade();

        // Remove stale events before re-adding (prevents duplicates on reinstall)
        $this->model_setting_event->deleteEventByCode('debug_logger_admin');
        $this->model_setting_event->deleteEventByCode('debug_logger_catalog');
        $this->model_setting_event->deleteEventByCode('debug_logger_menu');

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
        $this->model_setting_event->addEvent([
            'code' => 'debug_logger_menu',
            'description' => 'Debug Logger Admin Menu',
            'trigger' => 'admin/view/common/column_left/before',
            'action' => 'extension/debug_logger/event/header.columnLeft',
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
        $this->model_setting_event->deleteEventByCode('debug_logger_menu');
        $this->model_setting_setting->deleteSetting('module_debug_logger');

        // Remove extension directory so reinstall via Extension Installer works
        $ext_dir = DIR_EXTENSION . 'debug_logger/';
        if (is_dir($ext_dir)) {
            $this->rrmdir($ext_dir);
        }
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

        // Ensure schema is up-to-date (route column may not exist yet)
        $this->model_extension_debug_logger_module_debug_logger->upgrade();

        $severity = strtolower((string)($this->request->post['severity'] ?? 'bug'));
        if (!in_array($severity, ['bug', 'warning', 'info'])) {
            $severity = 'bug';
        }

        $screenshot = '';
        $is_pro = $this->model_extension_debug_logger_module_debug_logger_license->isPro();
        if ($is_pro && $this->config->get('module_debug_logger_capture_screenshot')) {
            if (!empty($this->request->post['screenshot'])) {
                $ss = (string)$this->request->post['screenshot'];
                if (strpos($ss, 'data:image/') === 0 && strlen($ss) < 4194304) {
                    $screenshot = $ss;
                } else {
                    $this->log->write('Debug Logger: screenshot rejected — prefix=' . substr($ss, 0, 30) . ' len=' . strlen($ss));
                }
            } else {
                $this->log->write('Debug Logger: screenshot enabled but empty in POST');
            }
        }

        $report_data = [
            'url' => html_entity_decode(substr((string)($this->request->post['url'] ?? ''), 0, 2048), ENT_QUOTES, 'UTF-8'),
            'route' => substr((string)($this->request->post['route'] ?? ''), 0, 255),
            'console_log' => html_entity_decode(substr((string)($this->request->post['console_log'] ?? ''), 0, 65535), ENT_QUOTES, 'UTF-8'),
            'network_log' => html_entity_decode(substr((string)($this->request->post['network_log'] ?? ''), 0, 65535), ENT_QUOTES, 'UTF-8'),
            'screenshot' => $screenshot,
            'comment' => substr((string)($this->request->post['comment'] ?? ''), 0, 4096),
            'loaded_files' => html_entity_decode(substr((string)($this->request->post['loaded_files'] ?? ''), 0, 65535), ENT_QUOTES, 'UTF-8'),
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

        // Send assignment notification email
        if ($uid > 0 && $this->config->get('module_debug_logger_email_enable')) {
            $this->trySendAssignmentEmail($rid, $uid);
        }

        $this->response->setOutput(json_encode(['success' => true]));
    }

    public function updateReport(): void {
        $this->response->addHeader('Content-Type: application/json');

        if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !isset($this->session->data['user_token'])) {
            $this->response->setOutput(json_encode(['error' => 'Forbidden']));
            return;
        }

        $this->load->model('extension/debug_logger/module/debug_logger');
        $rid = (int)($this->request->post['report_id'] ?? 0);
        $field = (string)($this->request->post['field'] ?? '');
        $value = (string)($this->request->post['value'] ?? '');

        if ($field === 'comment') {
            $this->model_extension_debug_logger_module_debug_logger->updateComment($rid, $value);
        } elseif ($field === 'severity') {
            $this->model_extension_debug_logger_module_debug_logger->updateSeverity($rid, $value);
        } elseif ($field === 'resolution') {
            $this->model_extension_debug_logger_module_debug_logger->updateResolution($rid, $value);
        } else {
            $this->response->setOutput(json_encode(['error' => 'Invalid field']));
            return;
        }

        $this->response->setOutput(json_encode(['success' => true]));
    }

    public function addTag(): void {
        $this->response->addHeader('Content-Type: application/json');
        if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !isset($this->session->data['user_token'])) {
            $this->response->setOutput(json_encode(['error' => 'Forbidden']));
            return;
        }
        $this->load->model('extension/debug_logger/module/debug_logger');
        $rid = (int)($this->request->post['report_id'] ?? 0);
        $tag = trim((string)($this->request->post['tag_name'] ?? ''));
        if (!$rid || !$tag) {
            $this->response->setOutput(json_encode(['error' => 'Missing data']));
            return;
        }
        $tag_id = $this->model_extension_debug_logger_module_debug_logger->addTag($rid, $tag);
        $this->response->setOutput(json_encode(['success' => true, 'tag_id' => $tag_id, 'tag_name' => $tag]));
    }

    public function removeTag(): void {
        $this->response->addHeader('Content-Type: application/json');
        if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !isset($this->session->data['user_token'])) {
            $this->response->setOutput(json_encode(['error' => 'Forbidden']));
            return;
        }
        $this->load->model('extension/debug_logger/module/debug_logger');
        $tag_id = (int)($this->request->post['tag_id'] ?? 0);
        if (!$tag_id) {
            $this->response->setOutput(json_encode(['error' => 'Missing tag_id']));
            return;
        }
        $this->model_extension_debug_logger_module_debug_logger->removeTag($tag_id);
        $this->response->setOutput(json_encode(['success' => true]));
    }

    public function bulkAction(): void {
        $this->response->addHeader('Content-Type: application/json');
        if ($this->request->server['REQUEST_METHOD'] !== 'POST' || !isset($this->session->data['user_token'])) {
            $this->response->setOutput(json_encode(['error' => 'Forbidden']));
            return;
        }
        $this->load->model('extension/debug_logger/module/debug_logger');
        $ids = $this->request->post['ids'] ?? [];
        if (is_string($ids)) $ids = json_decode($ids, true);
        if (!is_array($ids) || empty($ids)) {
            $this->response->setOutput(json_encode(['error' => 'No reports selected']));
            return;
        }
        $action = (string)($this->request->post['action'] ?? '');
        $count = 0;
        foreach ($ids as $id) {
            $id = (int)$id;
            if (!$id) continue;
            switch ($action) {
                case 'close':
                    $this->model_extension_debug_logger_module_debug_logger->setStatus($id, 1);
                    $count++;
                    break;
                case 'open':
                    $this->model_extension_debug_logger_module_debug_logger->setStatus($id, 0);
                    $count++;
                    break;
                case 'delete':
                    $this->model_extension_debug_logger_module_debug_logger->deleteReport($id);
                    $count++;
                    break;
            }
        }
        $this->response->setOutput(json_encode(['success' => true, 'count' => $count]));
    }

    public function reports(): void {
        if (
            !isset($this->session->data['user_token'])
            || ($this->request->get['user_token'] ?? '') !== $this->session->data['user_token']
        ) {
            $this->response->redirect($this->url->link('common/login'));
            return;
        }

        $data = $this->load->language('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger_license');
        $tok = $this->session->data['user_token'];
        $base_raw = str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok));
        $is_pro = $this->model_extension_debug_logger_module_debug_logger_license->isPro();

        // Handle actions (open/close/delete/clear/export)
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
        $filter_tag = (string)($this->request->get['filter_tag'] ?? '');
        $results = $this->model_extension_debug_logger_module_debug_logger->getReports($filter_status, $filter_source);

        // Filter by tag
        if ($filter_tag !== '') {
            $report_ids = array_column($results, 'id');
            $all_tags = $this->model_extension_debug_logger_module_debug_logger->getTagsByReportIds(array_map('intval', $report_ids));
            $results = array_filter($results, function($r) use ($all_tags, $filter_tag) {
                $tags = $all_tags[(int)$r['id']] ?? [];
                foreach ($tags as $t) {
                    if (strcasecmp($t['tag_name'], $filter_tag) === 0) return true;
                }
                return false;
            });
        }

        $total_open = $this->model_extension_debug_logger_module_debug_logger->getTotalOpenReports();
        $total = $this->model_extension_debug_logger_module_debug_logger->getTotalReports();
        $total_closed = $total - $total_open;

        $report_ids = array_column($results, 'id');
        $tags_map = $this->model_extension_debug_logger_module_debug_logger->getTagsByReportIds(array_map('intval', $report_ids));
        $all_tag_names = $this->model_extension_debug_logger_module_debug_logger->getAllTagNames();
        $admin_users = $is_pro ? $this->model_extension_debug_logger_module_debug_logger->getAdminUsers() : [];

        $sev_colors = ['bug' => '#ef4444', 'warning' => '#f59e0b', 'info' => '#3b82f6'];
        $src_colors = ['admin' => '#6366f1', 'catalog' => '#10b981'];

        // Prepare reports for twig
        $twig_reports = [];
        foreach ($results as $row) {
            $rid = (int)$row['id'];
            $clean_url = html_entity_decode((string)($row['url'] ?? ''), ENT_QUOTES, 'UTF-8');
            // Route: use dedicated column, fallback to extraction from URL for legacy reports
            $route = trim((string)($row['route'] ?? ''));
            if (!$route && preg_match('/[?&]route=([^&]+)/', $clean_url, $rm)) {
                $route = $rm[1];
            }
            $twig_reports[] = [
                'id'          => $rid,
                'severity'    => $row['severity'],
                'sev_color'   => $sev_colors[$row['severity']] ?? '#ef4444',
                'source'      => $row['source'],
                'src_color'   => $src_colors[$row['source']] ?? '#6b7280',
                'status'      => (int)$row['status'],
                'admin_user'  => $row['admin_user'] ?? '',
                'assigned_to' => (int)($row['assigned_to'] ?? 0),
                'date_added'  => $row['date_added'] ?? '',
                'url'         => $clean_url,
                'route'       => $route,
                'comment'     => (string)($row['comment'] ?? ''),
                'resolution'  => (string)($row['resolution'] ?? ''),
                'console_log' => trim((string)($row['console_log'] ?? '')),
                'network_log' => trim((string)($row['network_log'] ?? '')),
                'loaded_files'=> json_decode((string)($row['loaded_files'] ?? ''), true) ?: [],
                'screenshot'  => $row['screenshot'] ?? '',
                'tags'        => $tags_map[$rid] ?? [],
            ];
        }

        $this->document->setTitle($this->language->get('heading_title') . ' - ' . $this->language->get('text_reports'));

        $data['heading_title']   = $this->language->get('heading_title');
        $data['text_reports']    = $this->language->get('text_reports');
        $data['text_analytics']  = $this->language->get('text_analytics');
        $data['text_confirm_clear_all'] = 'Delete ALL reports?';
        $data['showing']         = 'Showing:';

        $data['breadcrumbs'] = [
            ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $tok)],
            ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok)],
            ['text' => $this->language->get('text_reports'), 'href' => $base_raw],
        ];

        $data['reports']       = $twig_reports;
        $data['report_count']  = count($twig_reports);
        $data['is_pro']        = $is_pro;
        $data['admin_users']   = $admin_users;
        $data['sev_colors']    = $sev_colors;
        $data['all_tag_names'] = $all_tag_names;
        $data['total']         = $total;
        $data['total_open']    = $total_open;
        $data['total_closed']  = $total_closed;
        $data['filter_status'] = $filter_status;
        $data['filter_source'] = $filter_source;
        $data['filter_tag']    = $filter_tag;

        $data['base_url']      = str_replace('&', '&amp;', $base_raw);
        $data['analytics_url'] = $this->url->link('extension/debug_logger/module/debug_logger.analytics', 'user_token=' . $tok);
        $data['settings_url']  = $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok);
        $data['update_url']    = str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger.updateReport', 'user_token=' . $tok));
        $data['add_tag_url']   = str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger.addTag', 'user_token=' . $tok));
        $data['remove_tag_url'] = str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger.removeTag', 'user_token=' . $tok));
        $data['bulk_url']      = str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger.bulkAction', 'user_token=' . $tok));
        $data['assign_url']    = $is_pro ? str_replace('&amp;', '&', $this->url->link('extension/debug_logger/module/debug_logger.assignReport', 'user_token=' . $tok)) : '';

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/debug_logger/module/debug_logger_reports', $data));
    }

    private function exportReports(string $format, int $filter_status, string $filter_source): void {
        $reports = $this->model_extension_debug_logger_module_debug_logger->getReports($filter_status, $filter_source);

        // Load tags for all reports
        $report_ids = array_column($reports, 'id');
        $tags_map = $this->model_extension_debug_logger_module_debug_logger->getTagsByReportIds(array_map('intval', $report_ids));

        if ($format === 'json') {
            header('Content-Type: application/json; charset=utf-8');
            header('Content-Disposition: attachment; filename="debug_reports_' . date('Y-m-d_His') . '.json"');
            $clean = array_map(function ($r) use ($tags_map) {
                unset($r['screenshot']);
                $r['tags'] = array_column($tags_map[(int)$r['id']] ?? [], 'tag_name');
                return $r;
            }, $reports);
            echo json_encode($clean, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;
        }

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="debug_reports_' . date('Y-m-d_His') . '.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID', 'Date', 'Severity', 'Source', 'URL', 'User', 'Comment', 'Resolution', 'Tags', 'Console Errors', 'Status']);
        foreach ($reports as $row) {
            $tags_str = implode(', ', array_column($tags_map[(int)$row['id']] ?? [], 'tag_name'));
            fputcsv($out, [
                $row['id'],
                $row['date_added'] ?? '',
                $row['severity'],
                $row['source'],
                $row['url'],
                $row['admin_user'] ?? '',
                $row['comment'],
                $row['resolution'] ?? '',
                $tags_str,
                $row['console_log'] ?? '',
                (int)($row['status'] ?? 0) === 1 ? 'Closed' : 'Open',
            ]);
        }
        fclose($out);
        exit;
    }

    /**
     * AJAX: Send a test email to verify mail configuration.
     */
    public function testEmail(): void {
        $this->load->language('extension/debug_logger/module/debug_logger');
        $this->response->addHeader('Content-Type: application/json');

        if (!isset($this->session->data['user_token'])) {
            $this->response->setOutput(json_encode(['error' => true, 'message' => 'Forbidden']));
            return;
        }

        $to = (string)($this->request->get['email'] ?? $this->config->get('module_debug_logger_email_to') ?? '');
        if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $this->response->setOutput(json_encode(['error' => true, 'message' => $this->language->get('text_test_email_invalid')]));
            return;
        }

        $engine = $this->config->get('config_mail_engine') ?: 'mail';
        $from   = $this->config->get('config_email');
        $sender = html_entity_decode((string)$this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

        try {
            $mail_option = [
                'parameter'     => $this->config->get('config_mail_parameter'),
                'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
                'smtp_username' => $this->config->get('config_mail_smtp_username'),
                'smtp_password' => html_entity_decode((string)$this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
                'smtp_port'     => (int)$this->config->get('config_mail_smtp_port'),
                'smtp_timeout'  => (int)$this->config->get('config_mail_smtp_timeout'),
            ];

            $mail = new \Opencart\System\Library\Mail($engine, $mail_option);
            $mail->setTo($to);
            $mail->setFrom($from);
            $mail->setSender($sender);
            $mail->setSubject('[Debug Logger] Test Email');
            $mail->setText("This is a test email from Debug Logger OC4.\n\nIf you receive this, your mail configuration is working correctly.\n\nEngine: " . $engine . "\nFrom: " . $from . "\nDate: " . date('Y-m-d H:i:s'));
            $mail->send();

            $this->log->write('Debug Logger: test email sent to ' . $to . ' (engine: ' . $engine . ')');

            $this->response->setOutput(json_encode([
                'success' => true,
                'message' => sprintf($this->language->get('text_test_email_sent'), $to),
                'engine'  => $engine,
                'from'    => $from,
            ]));
        } catch (\Throwable $e) {
            $this->log->write('Debug Logger test email error: ' . $e->getMessage());
            $this->response->setOutput(json_encode([
                'error'   => true,
                'message' => sprintf($this->language->get('text_test_email_failed'), $e->getMessage()),
                'engine'  => $engine,
            ]));
        }
    }

    private function trySendEmail(int $report_id, array $data): void {
        try {
            if (!$this->config->get('module_debug_logger_email_enable')) {
                return;
            }

            // Check severity filter — if none checked, send for all
            $sev = $data['severity'] ?? 'bug';
            $any_checked = $this->config->get('module_debug_logger_email_bug')
                        || $this->config->get('module_debug_logger_email_warning')
                        || $this->config->get('module_debug_logger_email_info');
            if ($any_checked && !$this->config->get('module_debug_logger_email_' . $sev)) {
                return;
            }

            $to = (string)$this->config->get('module_debug_logger_email_to');
            if (!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)) {
                $this->log->write('Debug Logger: email_to invalid or empty [' . $to . ']');
                return;
            }

            $subject = '[Debug Logger] ' . strtoupper($data['severity']) . ' Report #' . $report_id;
            $url_clean = html_entity_decode((string)($data['url'] ?? ''), ENT_QUOTES, 'UTF-8');
            // Extract internal route from URL
            $route = '';
            if (preg_match('/[?&]route=([^&]+)/', $url_clean, $m)) {
                $route = urldecode($m[1]);
            }

            $body  = "New debug report:\n\n";
            $body .= "ID:       #" . $report_id . "\n";
            $body .= "Severity: " . $data['severity'] . "\n";
            $body .= "Source:   " . ($data['source'] ?? 'admin') . "\n";
            if ($route) {
                $body .= "Route:    " . $route . "\n";
            }
            $body .= "URL:      " . $url_clean . "\n";
            $body .= "User:     " . ($data['admin_user'] ?? 'Guest') . "\n";
            $body .= "Date:     " . date('Y-m-d H:i:s') . "\n\n";
            if (!empty($data['comment'])) {
                $body .= "Comment:\n" . $data['comment'] . "\n\n";
            }
            if (!empty($data['console_log'])) {
                $body .= "Console:\n" . substr($data['console_log'], 0, 2000) . "\n";
            }

            // OC4 Mail: adaptor + options array
            $mail_option = [
                'parameter'     => $this->config->get('config_mail_parameter'),
                'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
                'smtp_username' => $this->config->get('config_mail_smtp_username'),
                'smtp_password' => html_entity_decode((string)$this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
                'smtp_port'     => (int)$this->config->get('config_mail_smtp_port'),
                'smtp_timeout'  => (int)$this->config->get('config_mail_smtp_timeout'),
            ];

            $mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine') ?: 'mail', $mail_option);
            $mail->setTo($to);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode((string)$this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $mail->setSubject($subject);
            $mail->setText($body);

            // Attach screenshot if present
            if (!empty($data['screenshot']) && strpos($data['screenshot'], 'data:image/') === 0) {
                $parts = explode(',', $data['screenshot'], 2);
                if (!empty($parts[1])) {
                    $decoded = base64_decode($parts[1]);
                    if ($decoded !== false) {
                        $tmp = tempnam(sys_get_temp_dir(), 'dl_ss_');
                        $ext = '.jpg';
                        $tmp_file = $tmp . $ext;
                        file_put_contents($tmp_file, $decoded);
                        $mail->addAttachment($tmp_file);
                    }
                }
            }

            $mail->send();

            // Cleanup temp file
            if (!empty($tmp_file) && is_file($tmp_file)) {
                @unlink($tmp_file);
            }
            if (!empty($tmp) && is_file($tmp)) {
                @unlink($tmp);
            }

            $this->log->write('Debug Logger: email sent to ' . $to . ' for report #' . $report_id);
        } catch (\Throwable $e) {
            $this->log->write('Debug Logger email error: ' . $e->getMessage());
        }
    }

    private function trySendAssignmentEmail(int $report_id, int $user_id): void {
        try {
            // Lookup assigned user's email
            $user_query = $this->db->query("SELECT `email`, `firstname`, `lastname` FROM `" . DB_PREFIX . "user` WHERE `user_id` = " . (int)$user_id . " AND `status` = 1");
            if (!$user_query->num_rows || empty($user_query->row['email'])) return;

            $to = $user_query->row['email'];
            $name = trim($user_query->row['firstname'] . ' ' . $user_query->row['lastname']);
            $assigner = $this->user->isLogged() ? (string)$this->user->getUserName() : 'System';

            $subject = '[Debug Logger] Report #' . $report_id . ' assigned to you';
            $body  = "Hi " . $name . ",\n\n";
            $body .= "Report #" . $report_id . " has been assigned to you by " . $assigner . ".\n\n";
            $body .= "Please review and resolve this report.\n\n";
            $body .= "— Debug Logger Pro";

            $mail_option = [
                'parameter'     => $this->config->get('config_mail_parameter'),
                'smtp_hostname' => $this->config->get('config_mail_smtp_hostname'),
                'smtp_username' => $this->config->get('config_mail_smtp_username'),
                'smtp_password' => html_entity_decode((string)$this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8'),
                'smtp_port'     => (int)$this->config->get('config_mail_smtp_port'),
                'smtp_timeout'  => (int)$this->config->get('config_mail_smtp_timeout'),
            ];

            $mail = new \Opencart\System\Library\Mail($this->config->get('config_mail_engine') ?: 'mail', $mail_option);
            $mail->setTo($to);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender(html_entity_decode((string)$this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
            $mail->setSubject($subject);
            $mail->setText($body);
            $mail->send();

            $this->log->write('Debug Logger: assignment email sent to ' . $to . ' for report #' . $report_id);
        } catch (\Throwable $e) {
            $this->log->write('Debug Logger assignment email error: ' . $e->getMessage());
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

    public function analytics(): void {
        if (
            !isset($this->session->data['user_token'])
            || ($this->request->get['user_token'] ?? '') !== $this->session->data['user_token']
        ) {
            $this->response->redirect($this->url->link('common/login'));
            return;
        }

        $data = $this->load->language('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger');
        $this->load->model('extension/debug_logger/module/debug_logger_license');
        $tok = $this->session->data['user_token'];
        $is_pro = $this->model_extension_debug_logger_module_debug_logger_license->isPro();

        $total       = $this->model_extension_debug_logger_module_debug_logger->getTotalReports();
        $total_open  = $this->model_extension_debug_logger_module_debug_logger->getTotalOpenReports();
        $total_closed = $total - $total_open;
        $by_severity = $this->model_extension_debug_logger_module_debug_logger->getCountBySeverity();
        $by_source   = $this->model_extension_debug_logger_module_debug_logger->getCountBySource();
        $by_day      = $this->model_extension_debug_logger_module_debug_logger->getReportsByDay(30);
        $by_hour     = $this->model_extension_debug_logger_module_debug_logger->getReportsByHour();
        $top_pages   = $this->model_extension_debug_logger_module_debug_logger->getTopPages(10);
        $grouped     = $this->model_extension_debug_logger_module_debug_logger->getGroupedReports(10);
        $recent      = $this->model_extension_debug_logger_module_debug_logger->getRecentActivity(10);
        $avg_hours   = $this->model_extension_debug_logger_module_debug_logger->getAvgResolutionTime();

        $sev_data = ['bug' => 0, 'warning' => 0, 'info' => 0];
        foreach ($by_severity as $s) { $sev_data[$s['severity']] = (int)$s['cnt']; }

        $src_data = ['admin' => 0, 'catalog' => 0];
        foreach ($by_source as $s) { $src_data[$s['source']] = (int)$s['cnt']; }

        // Prepare daily timeline (last 30 days)
        $days_map = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-{$i} days"));
            $days_map[$d] = ['bug' => 0, 'warning' => 0, 'info' => 0];
        }
        foreach ($by_day as $row) {
            if (isset($days_map[$row['day']][$row['severity']])) {
                $days_map[$row['day']][$row['severity']] = (int)$row['cnt'];
            }
        }

        $hour_data = array_fill(0, 24, 0);
        foreach ($by_hour as $row) { $hour_data[(int)$row['hour']] = (int)$row['cnt']; }

        // Enrich table data with route/display
        $enrichUrl = function(string $raw): array {
            $clean = html_entity_decode($raw, ENT_QUOTES, 'UTF-8');
            $route = '';
            if (preg_match('/[?&]route=([^&]+)/', $clean, $rm)) $route = $rm[1];
            return ['url' => $clean, 'route' => $route, 'display' => $route ?: substr($clean, 0, 100)];
        };

        $twig_top_pages = [];
        foreach ($top_pages as $p) {
            $u = $enrichUrl((string)$p['url']);
            $twig_top_pages[] = array_merge($u, ['cnt' => (int)$p['cnt']]);
        }

        $twig_grouped = [];
        foreach ($grouped as $g) {
            $u = $enrichUrl((string)$g['url']);
            $twig_grouped[] = array_merge($u, [
                'severity'   => $g['severity'],
                'cnt'        => (int)$g['cnt'],
                'first_seen' => $g['first_seen'],
                'last_seen'  => $g['last_seen'],
            ]);
        }

        $twig_recent = [];
        foreach ($recent as $r) {
            $u = $enrichUrl((string)$r['url']);
            $twig_recent[] = array_merge($u, [
                'id'         => (int)$r['id'],
                'date_added' => $r['date_added'],
                'severity'   => $r['severity'],
                'source'     => $r['source'],
                'status'     => (int)$r['status'],
                'admin_user' => $r['admin_user'] ?? '',
            ]);
        }

        $this->document->setTitle($this->language->get('heading_title') . ' - ' . $this->language->get('text_analytics'));

        $data['heading_title']  = $this->language->get('heading_title');
        $data['text_analytics'] = $this->language->get('text_analytics');
        $data['text_reports']   = $this->language->get('text_reports');

        $data['breadcrumbs'] = [
            ['text' => $this->language->get('text_home'), 'href' => $this->url->link('common/dashboard', 'user_token=' . $tok)],
            ['text' => $this->language->get('heading_title'), 'href' => $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok)],
            ['text' => $this->language->get('text_analytics'), 'href' => $this->url->link('extension/debug_logger/module/debug_logger.analytics', 'user_token=' . $tok)],
        ];

        $data['is_pro']       = $is_pro;
        $data['total']        = $total;
        $data['total_open']   = $total_open;
        $data['total_closed'] = $total_closed;
        $data['avg_resolution'] = $avg_hours !== null ? $avg_hours . 'h' : '—';
        $data['sev_bug']      = $sev_data['bug'];
        $data['sev_warning']  = $sev_data['warning'];
        $data['sev_info']     = $sev_data['info'];
        $data['src_admin']    = $src_data['admin'];
        $data['src_catalog']  = $src_data['catalog'];

        // Chart JSON data
        $data['day_labels']   = json_encode(array_map(fn($d) => date('M j', strtotime($d)), array_keys($days_map)));
        $data['day_bug']      = json_encode(array_column(array_values($days_map), 'bug'));
        $data['day_warn']     = json_encode(array_column(array_values($days_map), 'warning'));
        $data['day_info']     = json_encode(array_column(array_values($days_map), 'info'));
        $data['hour_labels']  = json_encode(array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23)));
        $data['hour_values']  = json_encode($hour_data);

        // Table data
        $data['top_pages']    = $twig_top_pages;
        $data['grouped']      = $twig_grouped;
        $data['recent']       = $twig_recent;

        // URLs
        $data['reports_url']  = $this->url->link('extension/debug_logger/module/debug_logger.reports', 'user_token=' . $tok);
        $data['settings_url'] = $this->url->link('extension/debug_logger/module/debug_logger', 'user_token=' . $tok);
        $data['chart_js_url'] = '../view/javascript/chart.min.js';

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/debug_logger/module/debug_logger_analytics', $data));
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
