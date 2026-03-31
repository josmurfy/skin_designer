<?php
namespace Opencart\Admin\Controller\Shopmanager\Card;

class CardImportPrice extends \Opencart\System\Engine\Controller {

    public function index(): void {
        $lang = $this->load->language('shopmanager/card/card_import_price');
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
             'href' => html_entity_decode($this->url->link('shopmanager/card/card_import_price','user_token='.$user_token),ENT_QUOTES,'UTF-8')],
        ];

        // Action URLs
        $data['upload']      = html_entity_decode($this->url->link('shopmanager/card/card_import_price.upload',      'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['save']        = html_entity_decode($this->url->link('shopmanager/card/card_import_price.save',        'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['delete']      = html_entity_decode($this->url->link('shopmanager/card/card_import_price.delete',      'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['truncate']    = html_entity_decode($this->url->link('shopmanager/card/card_import_price.truncate',    'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['url_get_list']        = html_entity_decode($this->url->link('shopmanager/card/card_import_price.getList',        'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['url_autocomplete']    = html_entity_decode($this->url->link('shopmanager/card/card_import_price.autocomplete',    'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['url_find_duplicates'] = html_entity_decode($this->url->link('shopmanager/card/card_import_price.findDuplicates', 'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['url_fetch_market_prices'] = html_entity_decode($this->url->link('shopmanager/card/card_import_price.fetchMarketPrices', 'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['url_fetch_preview_market_prices'] = html_entity_decode($this->url->link('shopmanager/card/card_import_price.fetchPreviewMarketPrices', 'user_token='.$user_token),ENT_QUOTES,'UTF-8');
        $data['user_token']  = $user_token;

        // Current filter state for template
        $data['filters']     = $filters;
        $data['sort']        = $sort;
        $data['order']       = $order;
        $data['page']        = $page;
        $data['limit']       = $limit;

        // Initial list (rendered HTML — delegates to getList())
        $this->load->model('shopmanager/card/card_import_price');
        $raw_brands = $this->model_shopmanager_card_card_import_price->getDistinctField('brand', '', 999);
        $data['brands'] = array_unique(array_map('urldecode', $raw_brands));
        sort($data['brands']);
        $data['list'] = $this->load->controller('shopmanager/card/card_import_price.getList');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/card/card_import_price', $data));
    }

    /**
     * AJAX endpoint for jQuery $.load() — called from twig inline JS
     */
    public function list(): void {
        $this->response->setOutput($this->load->controller('shopmanager/card/card_import_price.getList'));
    }

    /**
     * Returns paginated list as rendered HTML string (_list partial).
     * Called by index() for initial render and by list() for AJAX reloads.
     */
    public function getList(): string {
        $lang = $this->load->language('shopmanager/card/card_import_price');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/card/card_import_price');

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
        $data['cards'] = $this->model_shopmanager_card_card_import_price->getCardPrices($query_params);
        $soldBilanMap = $this->getSoldBilanMap($data['cards']);
        foreach ($data['cards'] as &$card) {
            $bilanKey = ($card['card_number'] ?? '') . '|||' . ($card['set_name'] ?? '');
            $card['has_sold']          = !empty($soldBilanMap[$bilanKey]);
            $card['ebay_sales_rendered'] = $this->renderSoldBilanHtml($soldBilanMap[$bilanKey] ?? []);
        }
        unset($card);
        $data['total'] = $this->model_shopmanager_card_card_import_price->getTotalCardPrices(array_merge($filters, ['filter_has_sold' => '1']));

        // Sort links — OC4 standard with ASC/DESC toggle per column
        // Pattern: if already sorted on this col → flip order; otherwise default ASC
        $ut = $this->session->data['user_token'];
        $data['sort_card_raw_id']  = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=card_raw_id'  . '&order=' . ($sort == 'card_raw_id'  ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_title']        = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=title'        . '&order=' . ($sort == 'title'        ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_category']     = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=category'     . '&order=' . ($sort == 'category'     ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_year']         = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=year'         . '&order=' . ($sort == 'year'         ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_brand']        = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=brand'        . '&order=' . ($sort == 'brand'        ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_set_name']     = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=set_name'     . '&order=' . ($sort == 'set_name'     ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_player']       = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=player'       . '&order=' . ($sort == 'player'       ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_card_number']  = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=card_number'  . '&order=' . ($sort == 'card_number'  ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_ungraded']     = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=ungraded'     . '&order=' . ($sort == 'ungraded'     ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_grade_9']      = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=grade_9'      . '&order=' . ($sort == 'grade_9'      ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_grade_10']     = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=grade_10'     . '&order=' . ($sort == 'grade_10'     ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_date_added']   = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=date_added'   . '&order=' . ($sort == 'date_added'   ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'ASC') . $url, true);
        $data['sort_best_price']   = $this->url->link('shopmanager/card/card_import_price.list', 'user_token=' . $ut . '&sort=best_price'   . '&order=' . ($sort == 'best_price'   ? ($order == 'ASC' ? 'DESC' : 'ASC') : 'DESC') . $url, true);
        $data['sort']  = $sort;
        $data['order'] = $order;
        $data['limit'] = $limit;

        // Pagination
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $data['total'],
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('shopmanager/card/card_import_price.list',
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
                  'column_grade_10','column_front_image','column_ebay_sold_raw','column_ebay_sold_graded',
                  'column_ebay_list_raw','column_ebay_list_graded','column_ebay_checked_at','column_actions',
                  'column_ebay_sales','button_fetch_ebay','button_sold_graded','text_no_records','text_bid_singular','text_bid_plural'] as $key) {
            $data[$key] = ($lang[$key] ?? '');
        }

        return $this->load->view('shopmanager/card/card_import_price_list', $data);
    }

    /**
     * AJAX: return distinct values for a filter field (autocomplete)
     * GET: field=brand&term=Upper
     */
    public function autocomplete(): void {
        $this->load->model('shopmanager/card/card_import_price');
        $field = $this->request->get['field'] ?? '';
        $term  = trim($this->request->get['term']  ?? '');
        $values = $this->model_shopmanager_card_card_import_price->getDistinctField($field, $term, 12);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($values));
    }

    /**
     * Upload CSV — parse, return cards + preview HTML in JSON. No DB insert, no session, no temp file.
     * Also returns already_imported flag so JS can warn the user.
     */
    public function upload(): void {
        $lang = $this->load->language('shopmanager/card/card_import_price');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        try {
            if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_price')) {
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

            $this->load->model('shopmanager/card/card_import_price');

            $parse_result = $this->model_shopmanager_card_card_import_price->parseCSV($file['tmp_name']);
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
                if ($this->model_shopmanager_card_card_import_price->checkCardExistsBySampleRow($s)) {
                    $matched++;
                }
            }

            // Threshold: majority of samples match (2/3, 1/2, 1/1)
            $threshold = max(1, (int)ceil(count($samples) * 0.6));

            if (count($samples) > 0 && $matched >= $threshold) {
                // Duplicate detected — do NOT show preview, show existing DB records instead
                $db_records = $this->model_shopmanager_card_card_import_price->getCardsByContext($cards, 500);
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
        $lang = $this->load->language('shopmanager/card/card_import_price');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        try {
            if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_price')) {
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

            $this->load->model('shopmanager/card/card_import_price');

            $batch_result = $this->model_shopmanager_card_card_import_price->addCardPriceBatch($cards);
            $total_in_db  = $this->model_shopmanager_card_card_import_price->getTotalCardPrices([]);

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
        $lang = $this->load->language('shopmanager/card/card_import_price');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_price')) {
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

        $this->load->model('shopmanager/card/card_import_price');
        $deleted = $this->model_shopmanager_card_card_import_price->deleteCardPrices($selected);
        $total   = $this->model_shopmanager_card_card_import_price->getTotalCardPrices([]);

        $json['success']  = $deleted . ' record(s) deleted.';
        $json['total']    = $total;

        $this->sendJsonResponse($json);
    }

    /**
     * Fetch eBay market prices (single row or batch) and save to DB.
     * Cache rule: skip API call if checked in last 30 days unless force=1.
     */
    public function fetchMarketPrices(): void {
        $lang = $this->load->language('shopmanager/card/card_import_price');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_price')) {
            $json['error'] = ($lang['error_permission'] ?? 'Permission denied');
            $this->sendJsonResponse($json);
            return;
        }

        $ids = [];
        $postedIds = $this->request->post['card_raw_ids'] ?? [];
        if (is_array($postedIds)) {
            $ids = array_map('intval', $postedIds);
        }

        if (empty($ids) && !empty($this->request->post['card_raw_id'])) {
            $ids[] = (int)$this->request->post['card_raw_id'];
        }

        $ids = array_values(array_filter($ids, static function(int $id): bool {
            return $id > 0;
        }));

        if (empty($ids)) {
            $json['error'] = ($lang['error_no_data'] ?? 'No records selected.');
            $this->sendJsonResponse($json);
            return;
        }

        $force = (int)($this->request->post['force'] ?? 0) === 1;

        $this->load->model('shopmanager/card/card_import_price');
        $this->load->model('shopmanager/ebay');

        $rows = $this->model_shopmanager_card_card_import_price->getCardsByIds($ids);
        $rowsById = [];
        foreach ($rows as $row) {
            $rowsById[(int)$row['card_raw_id']] = $row;
        }

        $results = [];
        $processed = 0;
        $cached = 0;
        $errors = 0;
        $rateLimited = false;
        $rateLimitedMessage = '';

        foreach ($ids as $cardRawId) {
            if (!isset($rowsById[$cardRawId])) {
                $results[$cardRawId] = [
                    'success' => false,
                    'error' => 'Card not found',
                    'cached' => false,
                    'rate_limited' => false,
                ];
                $errors++;
                continue;
            }

            $row = $rowsById[$cardRawId];
            $keyword = $this->buildCardPriceKeyword($row);
            $manualUrls = $this->model_shopmanager_ebay->buildManualEbayUrls($keyword);

            if ($keyword === '' || strlen($keyword) < 3) {
                $results[$cardRawId] = [
                    'success' => false,
                    'cached' => false,
                    'rate_limited' => false,
                    'keyword' => $keyword,
                    'error' => 'keyword too short (min 3 chars)',
                    'manual_urls' => $manualUrls,
                ];
                $errors++;
                continue;
            }

            if (!$force && $this->model_shopmanager_card_card_import_price->isMarketDataFresh($cardRawId, 30)) {
                $cached++;
                $results[$cardRawId] = [
                    'success' => true,
                    'cached' => true,
                    'rate_limited' => false,
                    'keyword' => $keyword,
                    'manual_urls' => $manualUrls,
                    'ebay_price_sold_raw' => $row['ebay_price_sold_raw'] ?? null,
                    'ebay_price_sold_raw_url' => $row['ebay_price_sold_raw_url'] ?? '',
                    'ebay_price_sold_raw_bids' => $row['ebay_price_sold_raw_bids'] ?? null,
                    'ebay_price_sold_graded' => $row['ebay_price_sold_graded'] ?? null,
                    'ebay_price_sold_graded_url' => $row['ebay_price_sold_graded_url'] ?? '',
                    'ebay_price_sold_graded_bids' => $row['ebay_price_sold_graded_bids'] ?? null,
                    'ebay_price_sold_graded_grade' => $row['ebay_price_sold_graded_grade'] ?? '',
                    'ebay_price_list_raw' => $row['ebay_price_list_raw'] ?? null,
                    'ebay_price_list_raw_url' => $row['ebay_price_list_raw_url'] ?? '',
                    'ebay_price_list_graded' => $row['ebay_price_list_graded'] ?? null,
                    'ebay_price_list_graded_url' => $row['ebay_price_list_graded_url'] ?? '',
                    'ebay_price_list_graded_grade' => $row['ebay_price_list_graded_grade'] ?? '',
                    'ebay_market_checked_at' => $row['ebay_market_checked_at'] ?? '',
                ];
                continue;
            }

            if ($rateLimited) {
                $results[$cardRawId] = [
                    'success' => false,
                    'cached' => false,
                    'rate_limited' => true,
                    'keyword' => $keyword,
                    'error' => $rateLimitedMessage !== '' ? $rateLimitedMessage : 'Rate limited',
                    'manual_urls' => $manualUrls,
                ];
                $errors++;
                continue;
            }

            $market = $this->fetchMarketPricesByKeyword($keyword);

            if (!empty($market['rate_limited'])) {
                $rateLimited = true;
                $rateLimitedMessage = (string)($market['error'] ?? $market['api_error'] ?? 'Rate limited');
                $results[$cardRawId] = [
                    'success' => false,
                    'cached' => false,
                    'rate_limited' => true,
                    'keyword' => $keyword,
                    'error' => $rateLimitedMessage,
                    'manual_urls' => $manualUrls,
                ];
                $errors++;
                continue;
            }

            if (empty($market['success'])) {
                $results[$cardRawId] = [
                    'success' => false,
                    'cached' => false,
                    'rate_limited' => false,
                    'keyword' => $keyword,
                    'error' => (string)($market['error'] ?? $market['api_error'] ?? 'unknown error'),
                    'manual_urls' => $manualUrls,
                ];
                $errors++;
                continue;
            }

            $saveData = [
                'ebay_price_sold_raw' => $market['price_sold'] ?? null,
                'ebay_price_sold_raw_url' => (string)($market['price_sold_url'] ?? ''),
                'ebay_price_sold_raw_bids' => $market['price_sold_bids'] ?? null,
                'ebay_price_sold_graded' => $market['price_sold_graded'] ?? null,
                'ebay_price_sold_graded_url' => (string)($market['price_sold_graded_url'] ?? ''),
                'ebay_price_sold_graded_bids' => $market['price_sold_graded_bids'] ?? null,
                'ebay_price_sold_graded_grade' => (string)($market['price_sold_graded_grade'] ?? ''),
                'ebay_price_list_raw' => $market['price_list'] ?? null,
                'ebay_price_list_raw_url' => (string)($market['price_list_url'] ?? ''),
                'ebay_price_list_graded' => $market['price_list_graded'] ?? null,
                'ebay_price_list_graded_url' => (string)($market['price_list_graded_url'] ?? ''),
                'ebay_price_list_graded_grade' => (string)($market['price_list_graded_grade'] ?? ''),
            ];

            // TEMP: DB update disabled — fetch only returns data without writing
            // $this->model_shopmanager_card_card_import_price->updateMarketPrices($cardRawId, $saveData);

            $results[$cardRawId] = [
                'success' => true,
                'cached' => false,
                'rate_limited' => false,
                'keyword' => $keyword,
                'manual_urls' => $manualUrls,
                'ebay_price_sold_raw' => $saveData['ebay_price_sold_raw'] ?? null,
                'ebay_price_sold_raw_url' => $saveData['ebay_price_sold_raw_url'] ?? '',
                'ebay_price_sold_raw_bids' => $saveData['ebay_price_sold_raw_bids'] ?? null,
                'ebay_price_sold_graded' => $saveData['ebay_price_sold_graded'] ?? null,
                'ebay_price_sold_graded_url' => $saveData['ebay_price_sold_graded_url'] ?? '',
                'ebay_price_sold_graded_bids' => $saveData['ebay_price_sold_graded_bids'] ?? null,
                'ebay_price_sold_graded_grade' => $saveData['ebay_price_sold_graded_grade'] ?? '',
                'ebay_price_list_raw' => $saveData['ebay_price_list_raw'] ?? null,
                'ebay_price_list_raw_url' => $saveData['ebay_price_list_raw_url'] ?? '',
                'ebay_price_list_graded' => $saveData['ebay_price_list_graded'] ?? null,
                'ebay_price_list_graded_url' => $saveData['ebay_price_list_graded_url'] ?? '',
                'ebay_price_list_graded_grade' => $saveData['ebay_price_list_graded_grade'] ?? '',
                'ebay_market_checked_at' => date('Y-m-d H:i:s'),
            ];
            $processed++;
        }

        $json['success'] = true;
        $json['processed'] = $processed;
        $json['cached'] = $cached;
        $json['errors'] = $errors;
        $json['rate_limited'] = $rateLimited;
        $json['rate_limited_message'] = $rateLimitedMessage;
        $json['results'] = $results;

        $this->sendJsonResponse($json);
    }

    /**
     * Fetch eBay market prices for preview rows (not yet in DB).
     * Input: JSON { cards: [ ...csv rows with _index... ] }
     */
    public function fetchPreviewMarketPrices(): void {
        $lang = $this->load->language('shopmanager/card/card_import_price');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_price')) {
            $json['error'] = ($lang['error_permission'] ?? 'Permission denied');
            $this->sendJsonResponse($json);
            return;
        }

        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);
        $cards = $body['cards'] ?? [];

        if (empty($cards) && !empty($this->request->post['cards'])) {
            $rawCards = $this->request->post['cards'];
            $cards = is_array($rawCards) ? $rawCards : json_decode((string)$rawCards, true);
        }

        if (!is_array($cards) || empty($cards)) {
            $json['error'] = ($lang['error_no_data'] ?? 'No records selected.');
            $this->sendJsonResponse($json);
            return;
        }

        $this->load->model('shopmanager/ebay');

        $results = [];
        $processed = 0;
        $errors = 0;
        $rateLimited = false;
        $rateLimitedMessage = '';

        foreach ($cards as $i => $card) {
            if (!is_array($card)) {
                continue;
            }

            $rowIndex = (int)($card['_index'] ?? $i);
            $keyword = $this->buildCardPriceKeyword($card);
            $manualUrls = $this->model_shopmanager_ebay->buildManualEbayUrls($keyword);

            if ($keyword === '' || strlen($keyword) < 3) {
                $results[$rowIndex] = [
                    'success' => false,
                    'cached' => false,
                    'rate_limited' => false,
                    'keyword' => $keyword,
                    'error' => 'keyword too short (min 3 chars)',
                    'manual_urls' => $manualUrls,
                ];
                $errors++;
                continue;
            }

            if ($rateLimited) {
                $results[$rowIndex] = [
                    'success' => false,
                    'cached' => false,
                    'rate_limited' => true,
                    'keyword' => $keyword,
                    'error' => $rateLimitedMessage !== '' ? $rateLimitedMessage : 'Rate limited',
                    'manual_urls' => $manualUrls,
                ];
                $errors++;
                continue;
            }

            $market = $this->fetchMarketPricesByKeyword($keyword);

            if (!empty($market['rate_limited'])) {
                $rateLimited = true;
                $rateLimitedMessage = (string)($market['error'] ?? $market['api_error'] ?? 'Rate limited');
                $results[$rowIndex] = [
                    'success' => false,
                    'cached' => false,
                    'rate_limited' => true,
                    'keyword' => $keyword,
                    'error' => $rateLimitedMessage,
                    'manual_urls' => $manualUrls,
                ];
                $errors++;
                continue;
            }

            if (empty($market['success'])) {
                $results[$rowIndex] = [
                    'success' => false,
                    'cached' => false,
                    'rate_limited' => false,
                    'keyword' => $keyword,
                    'error' => (string)($market['error'] ?? $market['api_error'] ?? 'unknown error'),
                    'manual_urls' => $manualUrls,
                ];
                $errors++;
                continue;
            }

            $checkedAt = date('Y-m-d H:i:s');
            // fetchMarketPricesByKeyword returns keys: price_sold, price_sold_graded, price_list, price_list_graded
            $result = [
                'success' => true,
                'cached' => false,
                'rate_limited' => false,
                'keyword' => $keyword,
                'manual_urls' => $manualUrls,
                'ebay_price_sold_raw' => $market['price_sold'] ?? null,
                'ebay_price_sold_raw_url' => (string)($market['price_sold_url'] ?? ''),
                'ebay_price_sold_raw_bids' => $market['price_sold_bids'] ?? null,
                'ebay_price_sold_graded' => $market['price_sold_graded'] ?? null,
                'ebay_price_sold_graded_url' => (string)($market['price_sold_graded_url'] ?? ''),
                'ebay_price_sold_graded_bids' => $market['price_sold_graded_bids'] ?? null,
                'ebay_price_sold_graded_grade' => (string)($market['price_sold_graded_grade'] ?? ''),
                'ebay_price_list_raw' => $market['price_list'] ?? null,
                'ebay_price_list_raw_url' => (string)($market['price_list_url'] ?? ''),
                'ebay_price_list_graded' => $market['price_list_graded'] ?? null,
                'ebay_price_list_graded_url' => (string)($market['price_list_graded_url'] ?? ''),
                'ebay_price_list_graded_grade' => (string)($market['price_list_graded_grade'] ?? ''),
                'ebay_market_checked_at' => $checkedAt,
            ];

            $results[$rowIndex] = $result;
            $processed++;
        }

        $json['success'] = true;
        $json['processed'] = $processed;
        $json['cached'] = 0;
        $json['errors'] = $errors;
        $json['rate_limited'] = $rateLimited;
        $json['results'] = $results;

        $this->sendJsonResponse($json);
    }

    /**
     * Truncate the entire card_raw table
     */
    public function truncate(): void {
        $lang = $this->load->language('shopmanager/card/card_import_price');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_price')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->sendJsonResponse($json);
            return;
        }

        $this->load->model('shopmanager/card/card_import_price');
        $this->model_shopmanager_card_card_import_price->truncateCardPrices();

        $json['success'] = 'All records deleted.';
        $json['total']   = 0;

        $this->sendJsonResponse($json);
    }

    /**
     * AJAX: scan DB for duplicate records and return results HTML
     */
    public function findDuplicates(): void {
        $lang = $this->load->language('shopmanager/card/card_import_price');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/card/card_import_price');
        $json = [];

        try {
            $rows = $this->model_shopmanager_card_card_import_price->findDbDuplicates();

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

        // ── 8 visible columns ──────────────────────────────────────────────
        // ☐  |  #  |  Image  |  Card Info  |  Prices  |  Market eBay  |  eBay Sales  |  ✕
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
        $html .= '<th style="min-width:180px;">Market eBay</th>';
        $html .= '<th style="min-width:160px;">' . htmlspecialchars((string)$this->language->get('column_ebay_sales')) . '</th>';
        $html .= '<th style="width:44px;" class="text-center" title="Fusion"></th>';
        $html .= '<th style="width:36px;"></th>';
        $html .= '</tr></thead><tbody>';

        // Load sold bilan from oc_card_price_sold (one batch query)
        $soldBilanMap = $this->getSoldBilanMap($cards);

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
            $keyword     = $this->buildCardPriceKeyword($card);
            $html .= '<tr class="' . $row_class . '" data-index="' . (int)($card['_index'] ?? 0) . '" data-card-raw-id="' . $card_raw_id . '" data-keyword="' . htmlspecialchars($keyword) . '">';

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
            // Sold Graded search button under G10
            if ($keyword !== '') {
                $soldUrl = 'https://www.ebay.ca/sch/i.html?' . http_build_query([
                    '_nkw' => $keyword,
                    '_sacat' => '261328',
                    '_from' => 'R40',
                    '_udlo' => '35',
                    'LH_Sold' => '1',
                    '_sop' => '2',
                    'Graded' => 'Yes',
                    '_dcat' => '261328'
                ]);
                $html .= '<div style="margin-top:4px;"><a class="btn btn-sm btn-dark w-100" style="font-size:10px;padding:2px 4px;line-height:1.15;" target="ebay_market_preview" href="' . htmlspecialchars($soldUrl) . '">'
                       . htmlspecialchars((string)$this->language->get('button_sold_graded')) . '</a></div>';
            }
            $html .= '</td>';

            // ── Col 6: Market eBay (4 sub-items merged) ───────────────────────
            $marketItems = [
                ['key' => 'ebay_price_sold_raw',    'label' => 'Auc. Raw',   'bids_key' => 'ebay_price_sold_raw_bids',    'grade_key' => ''],
                ['key' => 'ebay_price_sold_graded', 'label' => 'Auc. Grad.', 'bids_key' => 'ebay_price_sold_graded_bids', 'grade_key' => 'ebay_price_sold_graded_grade'],
                ['key' => 'ebay_price_list_raw',    'label' => 'BIN Raw',    'bids_key' => '',                            'grade_key' => ''],
                ['key' => 'ebay_price_list_graded', 'label' => 'BIN Grad.',  'bids_key' => '',                            'grade_key' => 'ebay_price_list_graded_grade'],
            ];
            $html .= '<td style="font-size:11px;min-width:180px;">';
            $cssClassMap = [
                'ebay_price_sold_raw'    => 'market-sold-raw',
                'ebay_price_sold_graded' => 'market-sold-graded',
                'ebay_price_list_raw'    => 'market-list-raw',
                'ebay_price_list_graded' => 'market-list-graded',
            ];
            foreach ($marketItems as $mi) {
                $val       = $card[$mi['key']] ?? null;
                $url       = (string)($card[$mi['key'] . '_url'] ?? '');
                $bids      = $mi['bids_key']  ? ($card[$mi['bids_key']]  ?? null) : null;
                $grade_str = $mi['grade_key'] ? (string)($card[$mi['grade_key']] ?? '') : '';
                $rendered  = $this->renderMarketColumnHtml($val, $url, $bids, $grade_str, $ungraded);
                $css       = $cssClassMap[$mi['key']] ?? '';
                $html .= '<div style="display:flex;align-items:flex-start;gap:4px;margin-bottom:3px;">'
                       . '<span class="badge bg-light text-dark border" style="font-size:9px;white-space:nowrap;min-width:60px;text-align:center;flex-shrink:0;">' . $mi['label'] . '</span>'
                       . '<span class="' . $css . '" style="line-height:1.2;">' . $rendered . '</span>'
                       . '</div>';
            }
            $checkedAt = $card['ebay_market_checked_at'] ?? '';
            $html .= '<div class="market-checked-at" style="margin-top:2px;font-size:9px;color:#888;">';
            $html .= $checkedAt ? ('✓ ' . htmlspecialchars((string)$checkedAt)) : '';
            $html .= '</div>';
            $html .= '</td>';

            // ── Col 7: eBay Sales (from oc_card_price_sold) ──────────────────
            $bilanKey = ($card['card_number'] ?? '') . '|||' . ($card['set_name'] ?? $card['set'] ?? '');
            $html .= '<td>' . $this->renderSoldBilanHtml($soldBilanMap[$bilanKey] ?? []) . '</td>';

            // ── Col 8: merge inline ───────────────────────────────────────────
            $html .= '<td class="preview-merge-col" style="padding:1px;vertical-align:middle;text-align:center;"></td>';
            // ── Col 9: delete ─────────────────────────────────────────────────
            $html .= '<td><button type="button" class="btn btn-sm btn-outline-danger btn-preview-delete" title="Remove row"><i class="fa-solid fa-xmark"></i></button></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    private function hasPreviewImportableValue(array $card): bool {
        if ((float)($card['ungraded'] ?? 0) > 0 || (float)($card['grade_9'] ?? 0) > 0 || (float)($card['grade_10'] ?? 0) > 0) {
            return true;
        }

        $sales = $card['ebay_sales'] ?? [];

        if (is_array($sales)) {
            return !empty($sales);
        }

        $sales = trim((string)$sales);
        return $sales !== '' && $sales !== '[]';
    }

    private function buildCardPriceKeyword(array $row): string {
        $title = trim((string)($row['title'] ?? ''));
        $year = trim((string)($row['year'] ?? ''));
        $setName = trim((string)($row['set_name'] ?? $row['set'] ?? ''));
        $player = trim((string)($row['player'] ?? ''));
        $cardNumber = trim((string)($row['card_number'] ?? ''));

        if ($year !== '' && $setName !== '') {
            $yearPattern = preg_quote(substr($year, 0, 4), '/');
            $setName = preg_replace('/^' . $yearPattern . '(?:[-\/][0-9]{2,4})?\s+/i', '', $setName) ?? $setName;
            $setName = trim($setName);
        }

        $parts = [];
        if ($year !== '') {
            $parts[] = $year;
        }
        if ($setName !== '') {
            $parts[] = $setName;
        }
        if ($cardNumber !== '') {
            $parts[] = '#' . $cardNumber;
        }
        if ($player !== '') {
            $parts[] = $player;
        }

        $keyword = trim(implode(' ', array_filter($parts)));

        if ($keyword !== '') {
            return $keyword;
        }

        return $title;
    }

    private function renderMarketColumnHtml($price, string $url = '', $bidCount = null, string $grade = '', float $comparePrice = 0.0): string {
        if ($price === null || $price === '') {
            return '<span class="text-muted">—</span>';
        }

        $priceFloat = (float)$price;
        $color = ($comparePrice > 0 && $priceFloat <= $comparePrice) ? '#dc3545' : '#28a745';
        $priceHtml = '<span style="font-weight:700;color:#fff;background:' . $color . ';padding:2px 6px;border-radius:4px;display:inline-block;">$' . number_format($priceFloat, 2, '.', '') . '</span>';

        if ($url !== '') {
            $priceHtml = '<a href="' . htmlspecialchars($url) . '" target="ebay_market_preview" style="text-decoration:none;">' . $priceHtml . '</a>';
        }

        $html = $priceHtml;

        if ($grade !== '') {
            $html .= '<div style="font-size:11px;color:#555;line-height:1.1;">' . htmlspecialchars($grade) . '</div>';
        }

        if ($bidCount !== null && $bidCount !== '' && (int)$bidCount >= 0) {
            $label = (int)$bidCount === 1 ? (string)$this->language->get('text_bid_singular') : (string)$this->language->get('text_bid_plural');
            $html .= '<div style="font-size:11px;color:#666;line-height:1.1;">' . (int)$bidCount . ' ' . htmlspecialchars($label) . '</div>';
        }

        return $html;
    }

    /**
     * Load sold bilan map from oc_card_price_sold for an array of cards.
     * Returns array keyed "card_number|||set_name" => [rows...].
     */
    private function getSoldBilanMap(array $cards): array {
        $this->load->model('shopmanager/card/card_import_sold');
        return $this->model_shopmanager_card_card_import_sold->getSoldBilanForCards($cards);
    }

    /**
     * Render the sold bilan for the "eBay Sales" column from oc_card_price_sold rows.
     * Distinguishes AUC/BIN and graded/ungraded.
     * Shows grading recommendation if a grade 7+ entry exists.
     */
    private function renderSoldBilanHtml(array $soldRows): string {
        if (empty($soldRows)) {
            return '<span class="text-muted" style="font-size:11px;">—</span>';
        }

        $bin_raw    = [];  // BIN non gradée
        $auc_raw    = [];  // AUC non gradée
        $graded_auc = [];  // AUC gradée, clé = numéro de grade (ex: "9", "9.5", "10")
        $graded_bin = [];  // BIN gradée, clé = numéro de grade
        $all_graded = [];  // toutes les entrées gradées (pour le bilan)

        foreach ($soldRows as $row) {
            $currency = strtoupper(trim((string)($row['currency'] ?? 'CAD')));
            $priceRaw = (float)($row['price'] ?? 0);
            $priceCad = ($currency !== 'CAD')
                ? round((float)$this->currency->convert($priceRaw, $currency, 'CAD'), 2)
                : $priceRaw;

            $grader   = trim((string)($row['grader'] ?? ''));
            $gradeRaw = trim((string)($row['grade']  ?? ''));
            $gradeNum = ($gradeRaw !== '') ? (float)$gradeRaw : 0.0;
            $isGraded = ($grader !== '' || $gradeNum > 0);
            $typeRaw  = strtoupper(trim((string)($row['type_listing'] ?? '')));
            $isAuc    = ($typeRaw === 'AUCTION' || $typeRaw === 'AUC');
            $dateSold = trim((string)($row['date_sold'] ?? ''));
            $bids     = (int)($row['bids'] ?? 0);

            $entry = [
                'price'     => $priceCad,
                'date'      => ($dateSold !== '' && $dateSold !== '0000-00-00') ? substr($dateSold, 0, 7) : '',
                'date_raw'  => $dateSold,
                'bids'      => $bids,
                'grade_num' => $gradeNum,
                'eid'       => trim((string)($row['ebay_item_id'] ?? '')),
            ];

            if ($isGraded) {
                // Clé = numéro de grade seulement (PSA 9 + BGS 9 + SGC 9 → "9")
                $gnKey = ($gradeNum == (int)$gradeNum) ? (string)(int)$gradeNum : (string)$gradeNum;
                if ($isAuc) {
                    $graded_auc[$gnKey][] = $entry;
                } else {
                    $graded_bin[$gnKey][] = $entry;
                }
                $all_graded[] = $entry;
            } elseif ($isAuc) {
                $auc_raw[] = $entry;
            } else {
                $bin_raw[] = $entry;
            }
        }

        ksort($graded_auc, SORT_NUMERIC);
        ksort($graded_bin, SORT_NUMERIC);

        // ── Helper: compute stats ──
        $computeStats = function(array $entries): ?array {
            if (empty($entries)) return null;
            usort($entries, fn(array $a, array $b): int => $a['price'] <=> $b['price']);
            $n      = count($entries);
            $low    = $entries[0];
            $high   = $entries[$n - 1];
            $midIdx = (int)floor(($n - 1) / 2);
            $median = ($n % 2 === 0)
                ? round(($entries[$midIdx]['price'] + $entries[$midIdx + 1]['price']) / 2, 2)
                : $entries[$midIdx]['price'];
            $totalBids = array_sum(array_column($entries, 'bids'));
            $avgBids   = $n > 0 ? round($totalBids / $n, 1) : 0;
            $withDate  = array_filter($entries, fn(array $e): bool => ($e['date_raw'] ?? '') !== '' && ($e['date_raw'] ?? '') !== '0000-00-00');
            $recent    = null;
            if (!empty($withDate)) {
                usort($withDate, fn(array $a, array $b): int => strcmp($b['date_raw'] ?? '', $a['date_raw'] ?? ''));
                $recent = array_values($withDate)[0];
            }
            return [
                'low'        => $low['price'],
                'low_d'      => $low['date'],
                'median'     => $median,
                'high'       => $high['price'],
                'high_d'     => $high['date'],
                'count'      => $n,
                'avg_bids'   => $avgBids,
                'recent_d'   => $recent ? substr($recent['date_raw'], 0, 7) : '',
                'recent_eid' => $recent ? ($recent['eid'] ?? '') : '',
            ];
        };

        // ── Helper: render one summary row ──
        $statsRow = function(string $badge, ?array $stats, bool $showBids = false): string {
            if ($stats === null) return '';
            $fmt    = fn(float $v): string => '$' . number_format($v, 2);
            $dateFn = fn(string $d): string => $d !== ''
                ? '<span style="font-size:9px;color:#aaa;">(' . htmlspecialchars($d) . ')</span>' : '';
            $bidsCell = $showBids && $stats['avg_bids'] > 0
                ? '<td style="padding:1px 3px;color:#888;font-size:9px;">~' . number_format($stats['avg_bids'], 1) . ' enchère' . ($stats['avg_bids'] != 1 ? 's' : '') . '</td>'
                : '<td></td>';
            $recentEid = $stats['recent_eid'] ?? '';
            $recentD   = $stats['recent_d']   ?? '';
            if ($recentEid !== '') {
                $recentCell = '<td style="padding:1px 3px;white-space:nowrap;">'
                    . '<span style="font-size:9px;color:#888;">' . htmlspecialchars($recentD) . '</span> '
                    . '<a href="https://www.ebay.ca/itm/' . htmlspecialchars($recentEid) . '" target="ebay_sold" style="font-size:10px;color:#0d6efd;text-decoration:none;" title="' . htmlspecialchars($recentEid) . '">🔗</a>'
                    . '</td>';
            } else {
                $recentCell = '<td style="padding:1px 3px;font-size:9px;color:#888;">' . htmlspecialchars($recentD) . '</td>';
            }
            $countLabel = $stats['count'] . ' vente' . ($stats['count'] > 1 ? 's' : '');
            return '<tr>'
                 . '<td style="white-space:nowrap;padding:1px 4px 1px 0;">' . $badge . '</td>'
                 . '<td style="white-space:nowrap;padding:1px 3px;font-size:10px;">'
                 .   '<span style="color:#dc3545;font-weight:600;">' . $fmt($stats['low']) . '</span> ' . $dateFn($stats['low_d'])
                 . '</td>'
                 . '<td style="white-space:nowrap;padding:1px 3px;color:#0d6efd;font-weight:600;font-size:10px;">' . $fmt($stats['median']) . '</td>'
                 . '<td style="white-space:nowrap;padding:1px 3px;font-size:10px;">'
                 .   '<span style="color:#198754;font-weight:600;">' . $fmt($stats['high']) . '</span> ' . $dateFn($stats['high_d'])
                 . '</td>'
                 . '<td style="padding:1px 3px;color:#888;font-size:9px;">' . $countLabel . '</td>'
                 . $bidsCell
                 . $recentCell
                 . '</tr>';
        };

        // ── Helper: separator row ──
        $sepRow = fn(string $label): string =>
            '<tr><td colspan="7" style="padding:4px 2px 2px;font-size:9px;font-weight:700;color:#555;text-transform:uppercase;letter-spacing:.4px;border-top:1px solid #dee2e6;">'
            . $label . '</td></tr>';

        $BIN_badge = '<span style="font-size:9px;font-weight:700;color:#fff;background:#0d6efd;padding:1px 4px;border-radius:3px;">BIN</span>';
        $AUC_badge = '<span style="font-size:9px;font-weight:700;color:#212529;background:#dee2e6;padding:1px 4px;border-radius:3px;">AUC</span>';

        $statsBin = $computeStats($bin_raw);
        $statsAuc = $computeStats($auc_raw);

        $hasUngraded  = ($statsBin !== null || $statsAuc !== null);
        $hasAucGraded = !empty($graded_auc);
        $hasBinGraded = !empty($graded_bin);
        $hasAny       = $hasUngraded || $hasAucGraded || $hasBinGraded;

        $html = '<div style="min-width:200px;font-size:11px;">';

        if ($hasAny) {
            $html .= '<table style="border-collapse:collapse;width:100%;"><thead><tr>'
                   . '<th style="padding:0 4px 2px 0;font-size:9px;color:#888;border-bottom:1px solid #dee2e6;"></th>'
                   . '<th style="padding:0 3px 2px;font-size:9px;color:#888;border-bottom:1px solid #dee2e6;">Bas</th>'
                   . '<th style="padding:0 3px 2px;font-size:9px;color:#888;border-bottom:1px solid #dee2e6;">Med</th>'
                   . '<th style="padding:0 3px 2px;font-size:9px;color:#888;border-bottom:1px solid #dee2e6;">Haut</th>'
                   . '<th style="padding:0 3px 2px;font-size:9px;color:#888;border-bottom:1px solid #dee2e6;"></th>'
                   . '<th style="padding:0 3px 2px;font-size:9px;color:#888;border-bottom:1px solid #dee2e6;"></th>'
                   . '<th style="padding:0 3px 2px;font-size:9px;color:#888;border-bottom:1px solid #dee2e6;">Récent</th>'
                   . '</tr></thead><tbody>';

            // ── Section 1 : Non gradée ──
            if ($hasUngraded) {
                $html .= $sepRow('📦 Non gradée');
                $html .= $statsRow($BIN_badge, $statsBin, false);
                $html .= $statsRow($AUC_badge, $statsAuc, true);
            }

            // ── Section 2 : Gradée — Enchère (AUC) ──
            if ($hasAucGraded) {
                $html .= $sepRow('🏷 Gradée — Enchère (AUC)');
                foreach ($graded_auc as $gnKey => $entries) {
                    $gBadge = '<span style="font-size:9px;font-weight:700;color:#fff;background:#6f42c1;padding:1px 4px;border-radius:3px;white-space:nowrap;">Grade ' . htmlspecialchars((string)$gnKey) . '</span>'
                            . '&#8201;<span style="font-size:9px;font-weight:700;color:#212529;background:#dee2e6;padding:1px 4px;border-radius:3px;">AUC</span>';
                    $html .= $statsRow($gBadge, $computeStats($entries), true);
                }
            }

            // ── Section 3 : Gradée — Prix fixe (BIN) ──
            if ($hasBinGraded) {
                $html .= $sepRow('🏷 Gradée — Prix fixe (BIN)');
                foreach ($graded_bin as $gnKey => $entries) {
                    $gBadge = '<span style="font-size:9px;font-weight:700;color:#fff;background:#6f42c1;padding:1px 4px;border-radius:3px;white-space:nowrap;">Grade ' . htmlspecialchars((string)$gnKey) . '</span>'
                            . '&#8201;<span style="font-size:9px;font-weight:700;color:#fff;background:#0d6efd;padding:1px 4px;border-radius:3px;">BIN</span>';
                    $html .= $statsRow($gBadge, $computeStats($entries), false);
                }
            }

            $html .= '</tbody></table>';
        }

        // ── Grading bilan (vaut la peine?) ──
        if (!empty($all_graded)) {
            $GRADING_COST   = 55.0;
            $EBAY_FEE       = 0.13;
            $MIN_NET_PROFIT = 20.0;

            $above7 = array_values(array_filter($all_graded, fn(array $e): bool => $e['grade_num'] >= 7.0));

            if (!empty($above7)) {
                usort($above7, fn(array $a, array $b): int => $a['price'] <=> $b['price']);
                $min7       = $above7[0]['price'];
                $netProceed = $min7 * (1 - $EBAY_FEE) - $GRADING_COST;
                $worthIt    = ($netProceed > $MIN_NET_PROFIT);

                if ($worthIt) {
                    $html .= '<div style="margin-top:4px;background:#d1e7dd;border-radius:4px;padding:3px 6px;">'
                           . '<div style="font-size:10px;font-weight:700;color:#0a3622;">✅ Vaut la peine</div>'
                           . '<div style="font-size:9px;color:#0a3622;">Low grade 7+ : $' . number_format($min7, 2)
                           . ' → net $' . number_format($netProceed, 2) . ' CAD</div>'
                           . '</div>';
                } else {
                    $html .= '<div style="margin-top:4px;background:#f8d7da;border-radius:4px;padding:3px 6px;">'
                           . '<div style="font-size:10px;font-weight:700;color:#842029;">❌ Pas rentable</div>'
                           . '<div style="font-size:9px;color:#842029;">Low grade 7+ : $' . number_format($min7, 2)
                           . ' → net $' . number_format($netProceed, 2)
                           . ' ≤ $' . number_format($MIN_NET_PROFIT, 0) . '</div>'
                           . '</div>';
                }
            }
        }

        $html .= '</div>';
        return $html;
    }

    private function renderEbaySalesColumnHtml($salesData): string {
        $entries = $this->decodeEbaySalesEntries($salesData);

        if (empty($entries)) {
            return '<span class="text-muted">—</span>';
        }

        usort($entries, function(array $a, array $b): int {
            $gradeA = $this->getEbaySalesSortValue($a);
            $gradeB = $this->getEbaySalesSortValue($b);

            if ($gradeA !== $gradeB) {
                return $gradeB <=> $gradeA;
            }

            $typeOrderA = (($a['sale_type'] ?? 'BIN') === 'BIN') ? 0 : 1;
            $typeOrderB = (($b['sale_type'] ?? 'BIN') === 'BIN') ? 0 : 1;
            if ($typeOrderA !== $typeOrderB) {
                return $typeOrderA <=> $typeOrderB;
            }

            $priceA = (float)($a['price'] ?? 0);
            $priceB = (float)($b['price'] ?? 0);
            if ($priceA !== $priceB) {
                return $priceA <=> $priceB;
            }

            return strcmp((string)($a['condition'] ?? ''), (string)($b['condition'] ?? ''));
        });

        $html = '<div style="min-width:170px;">';
        foreach ($entries as $entry) {
            $condition = trim((string)($entry['condition'] ?? 'Ungraded'));
            $saleType = strtoupper(trim((string)($entry['sale_type'] ?? 'BIN')));
            $price = (float)($entry['price'] ?? 0);
            $bids = (int)($entry['bids'] ?? 0);
            $saleBadgeColor = $saleType === 'AUC' ? '#212529' : '#0d6efd';

            $html .= '<div style="border:1px solid #dee2e6;border-radius:4px;padding:4px 6px;margin-bottom:4px;background:#fff;">';
            $html .= '<div style="font-size:11px;color:#555;line-height:1.1;font-weight:600;">' . htmlspecialchars($condition) . '</div>';
            $html .= '<div style="margin-top:3px;display:flex;gap:4px;align-items:center;flex-wrap:wrap;">';
            $html .= '<span style="font-size:10px;font-weight:700;color:#fff;background:' . $saleBadgeColor . ';padding:2px 6px;border-radius:4px;display:inline-block;">' . htmlspecialchars($saleType) . '</span>';
            $html .= '<span style="font-weight:700;color:#fff;background:#198754;padding:2px 6px;border-radius:4px;display:inline-block;">$' . number_format($price, 2, '.', '') . '</span>';
            $html .= '</div>';

            if ($saleType === 'AUC') {
                $label = $bids === 1 ? (string)$this->language->get('text_bid_singular') : (string)$this->language->get('text_bid_plural');
                $html .= '<div style="font-size:11px;color:#666;line-height:1.1;margin-top:2px;">' . (int)$bids . ' ' . htmlspecialchars($label) . '</div>';
            }

            $html .= '</div>';
        }
        $html .= '</div>';

        return $html;
    }

    private function decodeEbaySalesEntries($salesData): array {
        if (is_array($salesData)) {
            return array_values(array_filter($salesData, static function($entry): bool {
                return is_array($entry) && isset($entry['price']);
            }));
        }

        $salesText = trim((string)$salesData);
        if ($salesText === '' || $salesText === '[]') {
            return [];
        }

        $decoded = json_decode($salesText, true);
        if (!is_array($decoded)) {
            return [];
        }

        return array_values(array_filter($decoded, static function($entry): bool {
            return is_array($entry) && isset($entry['price']);
        }));
    }

    private function getEbaySalesSortValue(array $entry): float {
        if (isset($entry['grade_numeric']) && $entry['grade_numeric'] !== null && $entry['grade_numeric'] !== '') {
            return (float)$entry['grade_numeric'];
        }

        $condition = strtolower(trim((string)($entry['condition'] ?? '')));
        if ($condition === 'ungraded' || $condition === 'raw') {
            return -1.0;
        }

        return 0.0;
    }

    private function fetchMarketPricesByKeyword(string $keyword): array {
        $keyword = trim($keyword);
        $this->clog('[fetchMarketPricesByKeyword] keyword="' . $keyword . '"');

        if (strlen($keyword) < 3) {
            $this->clog('[fetchMarketPricesByKeyword] SKIP: keyword too short');
            return [
                'success' => false,
                'keyword' => $keyword,
                'error' => 'keyword too short (min 3 chars)',
                'rate_limited' => false,
                'manual_urls' => $this->model_shopmanager_ebay->buildManualEbayUrls($keyword)
            ];
        }

        try {
            $searchOptions = [
                'sort' => 'price_asc',
                'limit' => 100,
                'page' => 1,
                'site_id' => 2,
                'condition_type' => 'all',
                'category_id' => '261328',
            ];
            $this->clog('[fetchMarketPricesByKeyword] searchOptions=' . json_encode($searchOptions));

            $marketData = $this->model_shopmanager_ebay->searchAndClassifyPresentItems($keyword, $searchOptions, 1);
            $this->clog('[fetchMarketPricesByKeyword] marketData error="' . ($marketData['error'] ?? '') . '" total=' . ($marketData['total'] ?? '?') . ' items_count=' . count($marketData['items'] ?? []));
            $this->clog('[fetchMarketPricesByKeyword] buckets=' . json_encode($marketData['buckets'] ?? []));

            $apiError = (string)($marketData['error'] ?? '');
            $manualUrls = $this->model_shopmanager_ebay->buildManualEbayUrls($keyword);

            if ($this->isApiRateLimitedMessage($apiError)) {
                $this->clog('[fetchMarketPricesByKeyword] RATE LIMITED: ' . $apiError);
                return [
                    'success' => false,
                    'keyword' => $keyword,
                    'rate_limited' => true,
                    'api_error' => $apiError,
                    'manual_urls' => $manualUrls
                ];
            }

            $auctionRaw = $marketData['buckets']['auction_raw'] ?? null;
            $auctionGraded = $marketData['buckets']['auction_graded'] ?? null;
            $buyNowRaw = $marketData['buckets']['buy_now_raw'] ?? null;
            $buyNowGraded = $marketData['buckets']['buy_now_graded'] ?? null;

            if ($auctionRaw !== null) {
                $auctionRaw['price'] = round((float)$this->currency->convert((float)$auctionRaw['price'], (string)($auctionRaw['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($auctionGraded !== null) {
                $auctionGraded['price'] = round((float)$this->currency->convert((float)$auctionGraded['price'], (string)($auctionGraded['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($buyNowRaw !== null) {
                $buyNowRaw['price'] = round((float)$this->currency->convert((float)$buyNowRaw['price'], (string)($buyNowRaw['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($buyNowGraded !== null) {
                $buyNowGraded['price'] = round((float)$this->currency->convert((float)$buyNowGraded['price'], (string)($buyNowGraded['currency'] ?? 'USD'), 'CAD'), 2);
            }

            $result = [
                'success' => true,
                'keyword' => $keyword,
                'price_sold' => $auctionRaw !== null ? number_format($auctionRaw['price'], 2, '.', '') : null,
                'price_sold_url' => $auctionRaw['url'] ?? '',
                'price_sold_bids' => $auctionRaw['bids'] ?? null,
                'price_sold_graded' => $auctionGraded !== null ? number_format($auctionGraded['price'], 2, '.', '') : null,
                'price_sold_graded_url' => $auctionGraded['url'] ?? '',
                'price_sold_graded_bids' => $auctionGraded['bids'] ?? null,
                'price_sold_graded_grade' => $auctionGraded['grade'] ?? '',
                'price_list' => $buyNowRaw !== null ? number_format($buyNowRaw['price'], 2, '.', '') : null,
                'price_list_url' => $buyNowRaw['url'] ?? '',
                'price_list_graded' => $buyNowGraded !== null ? number_format($buyNowGraded['price'], 2, '.', '') : null,
                'price_list_graded_url' => $buyNowGraded['url'] ?? '',
                'price_list_graded_grade' => $buyNowGraded['grade'] ?? '',
                'api_error' => $apiError,
                'rate_limited' => false,
                'manual_urls' => $manualUrls,
                // eBay column keys expected by addCardPrice / preview
                'ebay_price_sold_raw'          => $auctionRaw !== null ? number_format($auctionRaw['price'], 2, '.', '') : null,
                'ebay_price_sold_raw_url'      => $auctionRaw['url'] ?? '',
                'ebay_price_sold_raw_bids'     => $auctionRaw['bids'] ?? null,
                'ebay_price_sold_graded'       => $auctionGraded !== null ? number_format($auctionGraded['price'], 2, '.', '') : null,
                'ebay_price_sold_graded_url'   => $auctionGraded['url'] ?? '',
                'ebay_price_sold_graded_bids'  => $auctionGraded['bids'] ?? null,
                'ebay_price_sold_graded_grade' => $auctionGraded['grade'] ?? '',
                'ebay_price_list_raw'          => $buyNowRaw !== null ? number_format($buyNowRaw['price'], 2, '.', '') : null,
                'ebay_price_list_raw_url'      => $buyNowRaw['url'] ?? '',
                'ebay_price_list_graded'       => $buyNowGraded !== null ? number_format($buyNowGraded['price'], 2, '.', '') : null,
                'ebay_price_list_graded_url'   => $buyNowGraded['url'] ?? '',
                'ebay_price_list_graded_grade' => $buyNowGraded['grade'] ?? '',
            ];
            $this->clog('[fetchMarketPricesByKeyword] RESULT: price_sold=' . ($result['price_sold'] ?? 'null') . ' price_list=' . ($result['price_list'] ?? 'null') . ' sold_graded=' . ($result['price_sold_graded'] ?? 'null') . ' api_error="' . $apiError . '"');
            return $result;
        } catch (\Throwable $e) {
            $error = 'Exception: ' . $e->getMessage();
            $this->clog('[fetchMarketPricesByKeyword] EXCEPTION: ' . $error);

            return [
                'success' => false,
                'keyword' => $keyword,
                'error' => $error,
                'rate_limited' => $this->isApiRateLimitedMessage($error),
                'manual_urls' => $this->model_shopmanager_ebay->buildManualEbayUrls($keyword)
            ];
        }
    }

    private function isApiRateLimitedMessage(string $message): bool {
        if ($message === '') {
            return false;
        }

        $text = strtolower($message);
        $patterns = [
            'call limit',
            'rate limit',
            'quota',
            'too many requests',
            'http 429',
            'error 429',
            'request limit'
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function sendJsonResponse(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    /**
     * Debug logger — writes to storage_phoenixliquidation/logs/card_import.log
     */
    private function clog(string $msg): void {
        $logFile = '/home/n7f9655/public_html/storage_phoenixliquidation/logs/card_import.log';
        file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n", FILE_APPEND | LOCK_EX);
    }
}
