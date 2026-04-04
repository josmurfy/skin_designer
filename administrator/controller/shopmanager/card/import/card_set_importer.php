<?php
namespace Opencart\Admin\Controller\Shopmanager\Card\Import;

class CardSetImporter extends \Opencart\System\Engine\Controller {

    public function index(): void {
        $lang = $this->load->language('shopmanager/card/import/card_set_importer');
        $data = $data ?? [];
        $data += $lang;
        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $user_token = $this->session->data['user_token'] ?? '';

        // ── Filter params (from URL for bookmarkable state) ─────────────────
        $filter_keys = ['filter_title','filter_category','filter_year','filter_brand',
                        'filter_set','filter_player','filter_card_number',
                        'filter_min_price','filter_max_price'];
        $filters = [];
        foreach ($filter_keys as $k) {
            $filters[$k] = $this->request->get[$k] ?? '';
        }
        $sort  = $this->request->get['sort']  ?? 'card_raw_id';
        $order = $this->request->get['order'] ?? 'DESC';
        $page  = max(1, (int)($this->request->get['page'] ?? 1));
        $limit = max(1, (int)($this->request->get['limit'] ?? 25));
        $start = ($page - 1) * $limit;

        // Breadcrumbs
        $data['breadcrumbs'] = [
            ['text' => ($lang['text_home'] ?? ''),
             'href' => html_entity_decode($this->url->link('common/dashboard','user_token='.$user_token),ENT_QUOTES,'UTF-8')],
            ['text' => ($lang['text_card_import'] ?? ''),
             'href' => html_entity_decode($this->url->link('shopmanager/card/import/card_set_importer','user_token='.$user_token),ENT_QUOTES,'UTF-8')],
        ];

        // Action URLs
        $data['upload']      = html_entity_decode($this->url->link('shopmanager/card/import/card_set_importer.upload',      'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['save']        = html_entity_decode($this->url->link('shopmanager/card/import/card_set_importer.save',        'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['delete']      = html_entity_decode($this->url->link('shopmanager/card/import/card_set_importer.delete',      'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['truncate']    = html_entity_decode($this->url->link('shopmanager/card/import/card_set_importer.truncate',    'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['url_get_list']        = html_entity_decode($this->url->link('shopmanager/card/import/card_set_importer.getList',        'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['url_autocomplete']    = html_entity_decode($this->url->link('shopmanager/card/import/card_set_importer.autocomplete',    'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['url_find_duplicates'] = html_entity_decode($this->url->link('shopmanager/card/import/card_set_importer.findDuplicates', 'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['user_token']  = $user_token;

        // Current filter state for template
        $data['filters']     = $filters;
        $data['sort']        = $sort;
        $data['order']       = $order;
        $data['page']        = $page;
        $data['limit']       = $limit;

        // Initial list (rendered HTML — delegates to getList())
        $this->load->model('shopmanager/card/import/card_set_importer');
        $raw_brands = $this->model_shopmanager_card_import_card_set_importer->getDistinctField('brand', '', 999);
        $data['brands'] = array_unique(array_map('urldecode', $raw_brands));
        sort($data['brands']);
        $data['list'] = $this->load->controller('shopmanager/card/import/card_set_importer.getList');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/card/import/card_set_importer', $data));
    }

    /**
     * AJAX endpoint for jQuery $.load() — called from twig inline JS
     */
    public function list(): void {
        $this->response->setOutput($this->load->controller('shopmanager/card/import/card_set_importer.getList'));
    }

    /**
     * Returns paginated list as rendered HTML string (_list partial).
     * Called by index() for initial render and by list() for AJAX reloads.
     */
    public function getList(): string {
        $lang = $this->load->language('shopmanager/card/import/card_set_importer');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/card/import/card_set_importer');

        $user_token = $this->session->data['user_token'] ?? '';

        $filter_keys = ['filter_title','filter_category','filter_year','filter_brand',
                        'filter_set','filter_player','filter_card_number',
                        'filter_min_price','filter_max_price'];
        $filters = [];
        foreach ($filter_keys as $k) {
            $filters[$k] = $this->request->get[$k] ?? '';
        }

        // ── Early exit: no filter → show placeholder ────────────────────────
        $has_filter = false;
        foreach ($filters as $v) {
            if ($v !== '') { $has_filter = true; break; }
        }
        if (!$has_filter) {
            return '<div class="p-5 text-center text-muted"><i class="fa-solid fa-filter fa-3x d-block mb-3 opacity-25"></i><p>' . ($lang['text_use_filters'] ?? 'Utilisez les filtres pour afficher les cartes.') . '</p></div>';
        }

        $sort  = $this->request->get['sort']  ?? 'best_price';
        $order = $this->request->get['order'] ?? 'DESC';
        $page  = max(1, (int)($this->request->get['page']  ?? 1));
        $limit = max(1, (int)($this->request->get['limit'] ?? 25));
        $start = ($page - 1) * $limit;

        // Build filter URL fragment for sort/pagination links (OC4 standard)
        $url = '';

        if (isset($this->request->get['filter_title'])) {
            $url .= '&filter_title=' . urlencode(html_entity_decode($this->request->get['filter_title'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_category'])) {
            $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_year'])) {
            $url .= '&filter_year=' . urlencode($this->request->get['filter_year']);
        }

        if (isset($this->request->get['filter_brand'])) {
            $url .= '&filter_brand=' . urlencode(html_entity_decode($this->request->get['filter_brand'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_set'])) {
            $url .= '&filter_set=' . urlencode(html_entity_decode($this->request->get['filter_set'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_player'])) {
            $url .= '&filter_player=' . urlencode(html_entity_decode($this->request->get['filter_player'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_card_number'])) {
            $url .= '&filter_card_number=' . urlencode($this->request->get['filter_card_number']);
        }

        if (isset($this->request->get['filter_min_price'])) {
            $url .= '&filter_min_price=' . urlencode($this->request->get['filter_min_price']);
        }

        if (isset($this->request->get['filter_max_price'])) {
            $url .= '&filter_max_price=' . urlencode($this->request->get['filter_max_price']);
        }

        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . $this->request->get['limit'];
        }

        // Note: sort/order are NOT in $url — they are added per-link for toggle logic
        // and appended to pagination separately

        // Query
        $query_params = array_merge($filters, [
            'sort' => $sort, 'order' => $order, 'start' => $start, 'limit' => $limit,
            'filter_has_sold' => '1',
        ]);
        $data['cards'] = $this->model_shopmanager_card_import_card_set_importer->getCardPrices($query_params);
        $data['total'] = $this->model_shopmanager_card_import_card_set_importer->getTotalCardPrices(array_merge($filters, ['filter_has_sold' => '1']));

        // Sort links — OC4 standard with ASC/DESC toggle per column
        // Pattern: if already sorted on this col → flip order; otherwise default ASC
        $ut = $this->session->data['user_token'];
        $data['sort_card_raw_id']  = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=card_raw_id'  . '&order=' . ($sort == 'card_raw_id'  ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_title']        = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=title'        . '&order=' . ($sort == 'title'        ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_category']     = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=category'     . '&order=' . ($sort == 'category'     ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_year']         = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=year'         . '&order=' . ($sort == 'year'         ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_brand']        = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=brand'        . '&order=' . ($sort == 'brand'        ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_set_name']     = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=set_name'     . '&order=' . ($sort == 'set_name'     ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_player']       = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=player'       . '&order=' . ($sort == 'player'       ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_card_number']  = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=card_number'  . '&order=' . ($sort == 'card_number'  ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_ungraded']     = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=ungraded'     . '&order=' . ($sort == 'ungraded'     ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_grade_9']      = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=grade_9'      . '&order=' . ($sort == 'grade_9'      ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_grade_10']     = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=grade_10'     . '&order=' . ($sort == 'grade_10'     ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_date_added']   = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=date_added'   . '&order=' . ($sort == 'date_added'   ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_best_price']   = $this->url->link('shopmanager/card/import/card_set_importer.list', 'user_token=' . $ut . '&sort=best_price'   . '&order=' . ($sort == 'best_price'   ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'DESC') . $url, true);
        $data['sort']  = $sort;
        $data['order'] = $order;
        $data['limit'] = $limit;

        // Pagination
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $data['total'],
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('shopmanager/card/import/card_set_importer.list',
                'user_token=' . $this->session->data['user_token'] . $url . '&sort=' . $sort . '&order=' . $order . '&page={page}', true),
            'text'  => ($lang['text_pagination'] ?? ''),
        ]);
        $data['results'] = sprintf(
            ($lang['text_pagination'] ?? ''),
            $data['total'] ? ($page - 1) * $limit + 1 : 0,
            ((($page - 1) * $limit) > ($data['total'] - $limit)) ? $data['total'] : (($page - 1) * $limit + $limit),
            $data['total'],
            ceil($data['total'] / $limit)
        );

        // Language keys needed by the partial
        foreach (['column_card_raw_id','column_title','column_category','column_year','column_brand',
                  'column_set','column_player','column_card_number','column_ungraded','column_grade_9',
                  'column_grade_10','column_front_image','column_actions',
                  'button_sold_graded','text_no_records'] as $key) {
            $data[$key] = ($lang[$key] ?? '');
        }

        return $this->load->view('shopmanager/card/import/card_set_importer_list', $data);
    }

    /**
     * AJAX: return distinct values for a filter field (autocomplete)
     * GET: field=brand&term=Upper
     */
    public function autocomplete(): void {
        $this->load->model('shopmanager/card/import/card_set_importer');
        $field = $this->request->get['field'] ?? '';
        $term  = trim($this->request->get['term']  ?? '');
        $values = $this->model_shopmanager_card_import_card_set_importer->getDistinctField($field, $term, 12);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($values));
    }

    /**
     * Upload CSV — parse, return cards + preview HTML in JSON. No DB insert, no session, no temp file.
     * Also returns already_imported flag so JS can warn the user.
     */
    public function upload(): void {
        $lang = $this->load->language('shopmanager/card/import/card_set_importer');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        try {
            if (!$this->user->hasPermission('modify', 'shopmanager/card/import/card_set_importer')) {
                $json['error'] = ($lang['error_permission'] ?? '');
                $this->sendJsonResponse($json);
                return;
            }

            if (!isset($this->request->files['file']['name']) || empty($this->request->files['file']['name'])) {
                $json['error'] = ($lang['error_no_file'] ?? '');
                $this->sendJsonResponse($json);
                return;
            }

            $file = $this->request->files['file'];
            if ((int)$file['error'] !== UPLOAD_ERR_OK) {
                $json['error'] = 'Upload error code: ' . (int)$file['error'];
                $this->sendJsonResponse($json);
                return;
            }

            set_time_limit(300);
            ini_set('max_execution_time', '300');
            ini_set('memory_limit', '512M');

            $this->load->model('shopmanager/card/import/card_set_importer');

            $parse_result = $this->model_shopmanager_card_import_card_set_importer->parseCSV($file['tmp_name']);
            if (!empty($parse_result['error'])) {
                $json['error'] = $parse_result['error'];
                $this->sendJsonResponse($json);
                return;
            }

            $cards = $parse_result['data'];
            if (empty($cards)) {
                $json['error'] = !empty($parse_result['error']) ? $parse_result['error'] : ($lang['error_empty_file'] ?? '');
                $this->sendJsonResponse($json);
                return;
            }

            // Tag each card with its original index for preview row tracking
            foreach ($cards as $i => &$card) {
                $card['_index'] = $i;
            }
            unset($card);

            // Count skipped (all prices = 0 and no ebay_sales)
            $would_skip = 0;
            foreach ($cards as $c) {
                if (!$this->hasPreviewImportableValue($c)) {
                    $would_skip++;
                }
            }

            // Build preview HTML — only rows WITH importable value
            $visible_cards = array_values(array_filter($cards, function($c) {
                return $this->hasPreviewImportableValue($c);
            }));
            $preview_html = $this->buildPreviewHtml($visible_cards);

            // Sample-based duplicate detection
            // Pick up to 3 random rows WITH importable value, check if they already exist in DB.
            $priced = array_values(array_filter($cards, function($c) {
                return $this->hasPreviewImportableValue($c);
            }));
            shuffle($priced);
            $samples   = array_slice($priced, 0, 3);
            $matched   = 0;
            foreach ($samples as $s) {
                if ($this->model_shopmanager_card_import_card_set_importer->checkCardExistsBySampleRow($s)) {
                    $matched++;
                }
            }

            // Threshold: majority of samples match (2/3, 1/2, 1/1)
            $threshold = max(1, (int)ceil(count($samples) * 0.6));

            if (count($samples) > 0 && $matched >= $threshold) {
                // Duplicate detected — do NOT show preview, show existing DB records instead
                $db_records = $this->model_shopmanager_card_import_card_set_importer->getCardsByContext($cards, 500);
                $json['success']            = ($lang['text_upload_success'] ?? '');
                $json['duplicate_detected'] = true;
                $json['match_count']        = $matched;
                $json['sample_total']       = count($samples);
                $json['total_in_file']      = count($cards);
                $json['db_count']           = count($db_records);
                $json['db_records_html']    = $this->buildPreviewHtml(
                    array_map(function($r) {
                        // Add _index so buildPreviewHtml doesn't error
                        $r['_index'] = (int)($r['card_raw_id'] ?? 0);
                        return $r;
                    }, $db_records)
                );
                $this->sendJsonResponse($json);
                return;
            }

            // Normal import — no duplicate detected
            $json['success']          = ($lang['text_upload_success'] ?? '');
            $json['cards']            = $cards;
            $json['total_in_file']    = count($cards);
            $json['would_skip']       = $would_skip;
            $json['would_insert']     = count($cards) - $would_skip;
            $json['preview_html']     = $preview_html;
            $json['already_imported'] = false;

        } catch (\Exception $e) {
            $json['error'] = 'Exception: ' . $e->getMessage();
        }

        $this->sendJsonResponse($json);
    }

    /**
     * Save — receives cards array from JS POST body, inserts into DB.
     */
    public function save(): void {
        $lang = $this->load->language('shopmanager/card/import/card_set_importer');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        try {
            if (!$this->user->hasPermission('modify', 'shopmanager/card/import/card_set_importer')) {
                $json['error'] = ($lang['error_permission'] ?? '');
                $this->sendJsonResponse($json);
                return;
            }

            $raw   = file_get_contents('php://input');
            $body  = json_decode($raw, true);
            $cards = $body['cards'] ?? [];

            if (empty($cards)) {
                // fallback: try POST
                $raw2  = $this->request->post['cards'] ?? '';
                $cards = is_array($raw2) ? $raw2 : json_decode($raw2, true);
            }

            if (empty($cards)) {
                $json['error'] = ($lang['error_no_data'] ?? '');
                $this->sendJsonResponse($json);
                return;
            }

            $this->load->model('shopmanager/card/import/card_set_importer');

            $batch_result = $this->model_shopmanager_card_import_card_set_importer->addCardPriceBatch($cards);
            $total_in_db  = $this->model_shopmanager_card_import_card_set_importer->getTotalCardPrices([]);

            $json['success']       = ($lang['text_upload_success'] ?? '');
            $json['total_in_file'] = count($cards);
            $json['inserted']      = $batch_result['inserted'];
            $json['skipped']       = $batch_result['skipped'];
            $json['total_in_db']   = $total_in_db;

        } catch (\Exception $e) {
            $json['error'] = 'Exception: ' . $e->getMessage();
        }

        $this->sendJsonResponse($json);
    }

    /**
     * Delete selected records by IDs
     */
    public function delete(): void {
        $lang = $this->load->language('shopmanager/card/import/card_set_importer');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/import/card_set_importer')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->sendJsonResponse($json);
            return;
        }

        $selected = isset($this->request->post['selected']) ? $this->request->post['selected'] : [];
        if (empty($selected)) {
            $json['error'] = ($lang['error_no_data'] ?? '');
            $this->sendJsonResponse($json);
            return;
        }

        $this->load->model('shopmanager/card/import/card_set_importer');
        $deleted = $this->model_shopmanager_card_import_card_set_importer->deleteCardPrices($selected);
        $total   = $this->model_shopmanager_card_import_card_set_importer->getTotalCardPrices([]);

        $json['success']  = $deleted . ' record(s) deleted.';
        $json['total']    = $total;

        $this->sendJsonResponse($json);
    }

    /**
     * Truncate the entire card_raw table
     */
    public function truncate(): void {
        $lang = $this->load->language('shopmanager/card/import/card_set_importer');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/import/card_set_importer')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->sendJsonResponse($json);
            return;
        }

        $this->load->model('shopmanager/card/import/card_set_importer');
        $this->model_shopmanager_card_import_card_set_importer->truncateCardPrices();

        $json['success'] = 'All records deleted.';
        $json['total']   = 0;

        $this->sendJsonResponse($json);
    }

    /**
     * AJAX: scan DB for duplicate records and return results HTML
     */
    public function findDuplicates(): void {
        $lang = $this->load->language('shopmanager/card/import/card_set_importer');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/card/import/card_set_importer');
        $json = [];

        try {
            $rows = $this->model_shopmanager_card_import_card_set_importer->findDbDuplicates();

            if (empty($rows)) {
                $json['success']         = true;
                $json['duplicate_count'] = 0;
                $json['group_count']     = 0;
                $json['html']            = '';
                $this->sendJsonResponse($json);
                return;
            }

            // Collect IDs to delete (non-keepers)
            $to_delete = [];
            $groups    = [];
            foreach ($rows as $r) {
                $key = $r['category'] . '|' . $r['brand'] . '|' . $r['year'] . '|' . $r['set_name'] . '|' . $r['player'] . '|' . $r['card_number'] . '|' . $r['ungraded'] . '|' . $r['grade_9'] . '|' . $r['grade_10'];
                $groups[$key] = true;
                if (!(int)($r['is_keeper'] ?? 0)) {
                    $to_delete[] = (int)$r['card_raw_id'];
                }
            }

            $json['success']         = true;
            $json['duplicate_count'] = count($to_delete);
            $json['group_count']     = count($groups);
            $json['to_delete']       = $to_delete;
            $json['html']            = $this->buildDuplicatesHtml($rows);

        } catch (\Exception $e) {
            $json['error'] = 'Exception: ' . $e->getMessage();
        }

        $this->sendJsonResponse($json);
    }

    /**
     * Build HTML table for DB duplicate results.
     * Keeper row = table-success; duplicate rows = table-warning with checkbox.
     */
    private function buildDuplicatesHtml(array $rows): string {
        if (empty($rows)) return '';

        $html  = '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-sm mb-0" id="duplicates-table">';
        $html .= '<thead class="table-light"><tr>';
        $html .= '<td style="width:1px;"><input type="checkbox" id="dup-check-all" checked title="Select all duplicates"></td>';
        $html .= '<td><strong>ID</strong></td>';
        $html .= '<td><strong>Title</strong></td>';
        $html .= '<td><strong>Category</strong></td>';
        $html .= '<td><strong>Year</strong></td>';
        $html .= '<td><strong>Brand</strong></td>';
        $html .= '<td><strong>Set</strong></td>';
        $html .= '<td><strong>Player</strong></td>';
        $html .= '<td><strong>Card #</strong></td>';
        $html .= '<td class="text-end"><strong>Ungraded</strong></td>';
        $html .= '<td class="text-end"><strong>G9</strong></td>';
        $html .= '<td class="text-end"><strong>G10</strong></td>';
        $html .= '<td><strong>Status</strong></td>';
        $html .= '</tr></thead><tbody>';

        foreach ($rows as $r) {
            $is_keeper  = (int)($r['is_keeper'] ?? 0);
            $row_class  = $is_keeper ? 'table-success' : 'table-warning';
            $id         = (int)$r['card_raw_id'];

            $html .= '<tr class="' . $row_class . '" data-id="' . $id . '">';

            if ($is_keeper) {
                $html .= '<td></td>'; // no checkbox on keeper
            } else {
                $html .= '<td class="text-center"><input type="checkbox" name="dup_selected[]" value="' . $id . '" checked class="dup-checkbox"></td>';
            }

            $html .= '<td>' . $id . '</td>';
            $html .= '<td>' . htmlspecialchars($r['title']       ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($r['category']    ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($r['year']        ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($r['brand']       ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($r['set_name']    ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($r['player']      ?? '') . '</td>';
            $html .= '<td>' . htmlspecialchars($r['card_number'] ?? '') . '</td>';
            $html .= '<td class="text-end">' . (float)($r['ungraded']  ?? 0) . '</td>';
            $html .= '<td class="text-end">' . (float)($r['grade_9']   ?? 0) . '</td>';
            $html .= '<td class="text-end">' . (float)($r['grade_10']  ?? 0) . '</td>';

            if ($is_keeper) {
                $html .= '<td><span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Keeper</span></td>';
            } else {
                $html .= '<td><span class="badge bg-warning text-dark"><i class="fa-solid fa-copy me-1"></i>Doublon</span></td>';
            }

            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    /**
     * Build a simple HTML preview table for the first N rows of the CSV
     */
    private function buildPreviewHtml(array $cards): string {
        if (empty($cards)) {
            return '<p>' . htmlspecialchars((string)$this->language->get('text_no_records')) . '</p>';
        }

        // ── 6 visible columns ──────────────────────────────────────────────
        // ☐  |  #  |  Image  |  Card Info  |  Prices  |  ✕
        // All DB data still lives in currentCards JS array via data-index.
        // ──────────────────────────────────────────────────────────────────

        $html  = '<div class="table-responsive">';
        $html .= '<table class="table table-bordered table-hover table-sm" id="preview-table">';
        $html .= '<thead class="table-dark"><tr>';
        $html .= '<th style="width:36px;"><input type="checkbox" id="preview-check-all" class="form-check-input" checked></th>';
        $html .= '<th style="width:30px;">#</th>';
        $html .= '<th style="width:80px;">' . htmlspecialchars((string)$this->language->get('column_front_image')) . '</th>';
        $html .= '<th style="min-width:200px;">' . htmlspecialchars((string)$this->language->get('column_title')) . '</th>';
        $html .= '<th style="min-width:120px;">Prices</th>';
        $html .= '<th style="width:44px;" class="text-center" title="Fusion"></th>';
        $html .= '<th style="width:36px;"></th>';
        $html .= '</tr></thead><tbody>';

        $n = 1;
        foreach ($cards as $card) {
            $ungraded  = (float)($card['ungraded'] ?? 0);
            $grade_9   = (float)($card['grade_9']  ?? 0);
            $grade_10  = (float)($card['grade_10'] ?? 0);
            $max_price = max($ungraded, $grade_9, $grade_10);

            if ($max_price >= 50) {
                $row_class = 'table-warning';
            } elseif ($max_price >= 20) {
                $row_class = 'table-info';
            } elseif ($max_price >= 5) {
                $row_class = 'table-active';
            } else {
                $row_class = '';
            }

            $card_raw_id = (int)($card['card_raw_id'] ?? 0);
            $html .= '<tr class="' . $row_class . '" data-index="' . (int)($card['_index'] ?? 0) . '">';

            // ── Col 1: checkbox ───────────────────────────────────────────────
            $html .= '<td><input type="checkbox" class="form-check-input preview-row-check" checked></td>';

            // ── Col 2: # ──────────────────────────────────────────────────────
            $html .= '<td class="text-center">' . $n++ . '</td>';

            // ── Col 3: image ──────────────────────────────────────────────────
            $img = htmlspecialchars($card['front_image'] ?? '');
            if ($img) {
                $html .= '<td><img src="' . $img . '" class="preview-thumb-img" data-fullsrc="' . $img . '" style="max-height:70px;max-width:70px;cursor:zoom-in;" onerror="this.style.display=\'none\'"></td>';
            } else {
                $html .= '<td class="text-muted text-center">—</td>';
            }

            // ── Col 4: Card Info (title + meta badges) ────────────────────────
            $set_val     = $card['set_name'] ?? $card['set'] ?? '';
            $year_val    = $card['year'] ?? '';
            $brand_val   = $card['brand'] ?? '';
            $player_val  = $card['player'] ?? '';
            $cardnum_val = $card['card_number'] ?? '';
            $cat_val     = $card['category'] ?? '';

            $html .= '<td>';
            $html .= '<div style="font-size:12px;font-weight:600;line-height:1.3;">' . htmlspecialchars($card['title'] ?? '') . '</div>';
            $html .= '<div style="margin-top:4px;display:flex;flex-wrap:wrap;gap:3px;">';
            if ($year_val  !== '') $html .= '<span class="badge bg-secondary" style="font-size:10px;">' . htmlspecialchars((string)$year_val)  . '</span>';
            if ($brand_val !== '') $html .= '<span class="badge bg-secondary" style="font-size:10px;">' . htmlspecialchars((string)$brand_val) . '</span>';
            if ($set_val   !== '') $html .= '<span class="badge bg-dark"      style="font-size:10px;">' . htmlspecialchars((string)$set_val)   . '</span>';
            if ($player_val!== '') $html .= '<span class="badge bg-primary"   style="font-size:10px;">' . htmlspecialchars((string)$player_val). '</span>';
            if ($cardnum_val!=='') $html .= '<span class="badge bg-light text-dark border" style="font-size:10px;">#' . htmlspecialchars((string)$cardnum_val) . '</span>';
            if ($cat_val   !== '') $html .= '<span class="badge bg-info text-dark" style="font-size:10px;">' . htmlspecialchars((string)$cat_val) . '</span>';
            $html .= '</div>';
            $html .= '</td>';  // Sold Graded button moved to Prices column

            // ── Col 5: Prices (ungraded / G9 / G10) ──────────────────────────
            $html .= '<td style="white-space:nowrap;">';
            $priceRows = [
                ['label' => 'Raw',  'val' => $ungraded, 'color' => 'bg-secondary'],
                ['label' => 'G9',   'val' => $grade_9,  'color' => 'bg-success'],
                ['label' => 'G10',  'val' => $grade_10, 'color' => 'bg-primary'],
            ];
            foreach ($priceRows as $pr) {
                if ($pr['val'] > 0) {
                    $html .= '<div style="margin-bottom:2px;">'
                           . '<span class="badge ' . $pr['color'] . '" style="font-size:10px;min-width:30px;">' . $pr['label'] . '</span> '
                           . '<span style="font-size:12px;font-weight:600;">$' . number_format($pr['val'], 2) . '</span>'
                           . '</div>';
                } else {
                    $html .= '<div style="margin-bottom:2px;opacity:0.35;">'
                           . '<span class="badge bg-light text-dark border" style="font-size:10px;min-width:30px;">' . $pr['label'] . '</span> '
                           . '<span style="font-size:11px;">—</span>'
                           . '</div>';
                }
            }
            $html .= '</td>';

            // ── Col 6: merge inline ───────────────────────────────────────────
            $html .= '<td class="preview-merge-col" style="padding:1px;vertical-align:middle;text-align:center;"></td>';
            // ── Col 7: delete ─────────────────────────────────────────────────
            $html .= '<td><button type="button" class="btn btn-sm btn-outline-danger btn-preview-delete" title="Remove row"><i class="fa-solid fa-xmark"></i></button></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    private function hasPreviewImportableValue(array $card): bool {
        return (float)($card['ungraded'] ?? 0) > 0 || (float)($card['grade_9'] ?? 0) > 0 || (float)($card['grade_10'] ?? 0) > 0;
    }

    private function sendJsonResponse(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }


}
