<?php
namespace Opencart\Admin\Controller\Shopmanager\Card\Import;

class CardPriceActive extends \Opencart\System\Engine\Controller {

    // ------------------------------------------------------------------ //
    //  Main page
    // ------------------------------------------------------------------ //

    public function index(): void {
        $lang = $this->load->language('shopmanager/card/import/card_price_active');
        $this->load->model('shopmanager/card/import/card_price_active');

        $data = $lang;
        $data['heading_title'] = $lang['heading_title'];

        $data['breadcrumbs'] = [
            [
                'text' => $lang['text_home'],
                'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true),
            ],
            [
                'text' => $lang['heading_title'],
                'href' => $this->url->link('shopmanager/card/import/card_price_active', 'user_token=' . $this->session->data['user_token'], true),
            ],
        ];

        // Filter params (kept in URL for pagination)
        $filter_keyword    = $this->request->get['filter_keyword']    ?? '';
        $filter_is_graded  = $this->request->get['filter_is_graded']  ?? '';
        $filter_grader     = $this->request->get['filter_grader']     ?? '';
        $filter_date_from  = $this->request->get['filter_date_from']  ?? '';
        $filter_date_to    = $this->request->get['filter_date_to']    ?? '';
        $sort              = $this->request->get['sort']              ?? 'date_added';
        $order             = $this->request->get['order']             ?? 'DESC';
        $limit             = (int)($this->request->get['limit']       ?? 25);
        $page              = max(1, (int)($this->request->get['page'] ?? 1));
        $start             = ($page - 1) * $limit;

        $filter = [
            'keyword'   => $filter_keyword,
            'is_graded' => $filter_is_graded,
            'grader'    => $filter_grader,
            'date_from' => $filter_date_from,
            'date_to'   => $filter_date_to,
            'sort'      => $sort,
            'order'     => $order,
        ];

        $total   = $this->model_shopmanager_card_import_card_price_active->getActiveTotalRows($filter);
        $records = $this->model_shopmanager_card_import_card_price_active->getActiveList($filter, $start, $limit);
        $stats   = $this->model_shopmanager_card_import_card_price_active->getRawStats();
        $cards_without_prices = $this->model_shopmanager_card_import_card_price_active->getCardSetWithoutActivePrices();
        $categories           = $this->model_shopmanager_card_import_card_price_active->getDistinctCategories();

        $data['records']              = $records;
        $data['cards_without_prices'] = $cards_without_prices;
        $data['categories']           = $categories;
        $data['total']       = $total;
        $data['stats']       = $stats;
        $data['page']        = $page;
        $data['limit']       = $limit;
        $data['sort']        = $sort;
        $data['order']       = $order;
        $data['filter_keyword']   = $filter_keyword;
        $data['filter_is_graded'] = $filter_is_graded;
        $data['filter_grader']    = $filter_grader;
        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to']   = $filter_date_to;

        // Pagination (OC4 pattern — via controller, not Library\Pagination)
        $filterUrl = '&filter_keyword=' . rawurlencode($filter_keyword)
                   . '&filter_is_graded=' . rawurlencode($filter_is_graded)
                   . '&filter_grader=' . rawurlencode($filter_grader)
                   . '&filter_date_from=' . rawurlencode($filter_date_from)
                   . '&filter_date_to=' . rawurlencode($filter_date_to);

        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link(
                'shopmanager/card/import/card_price_active',
                'user_token=' . $this->session->data['user_token'] . $filterUrl . '&sort=' . $sort . '&order=' . $order . '&page={page}',
                true
            ),
        ]);
        $data['results'] = sprintf(
            $lang['text_pagination'] ?? '%d to %d of %d',
            $total ? $start + 1 : 0,
            min($start + $limit, $total),
            $total
        );

        // JS + Token
        $data['user_token']  = $this->session->data['user_token'];
        $data['route_fetch'] = $this->url->link('shopmanager/card/import/card_price_active.fetchFromEbay', 'user_token=' . $this->session->data['user_token'], true);
        $data['route_process'] = $this->url->link('shopmanager/card/import/card_price_active.processRaw', 'user_token=' . $this->session->data['user_token'], true);
        $data['route_delete'] = $this->url->link('shopmanager/card/import/card_price_active.deleteActive', 'user_token=' . $this->session->data['user_token'], true);

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/card/import/card_price_active', $data));
    }

    // ------------------------------------------------------------------ //
    //  AJAX: fetch eBay sold items and save raw
    // ------------------------------------------------------------------ //

    public function fetchFromEbay(): void {
        @set_time_limit(60); // 1 page max per call

        $this->load->language('shopmanager/card/import/card_price_active');
        $this->load->model('shopmanager/card/import/card_price_active');
        $this->load->model('shopmanager/ebay');

        $json = [];

        $keyword    = trim($this->request->post['keyword'] ?? '');
        $page       = max(1, (int)($this->request->post['page'] ?? 1));
        $site_id    = (int)($this->request->post['site_id'] ?? 2);
        $cond       = $this->request->post['condition_type'] ?? 'all';
        $grader     = $this->request->post['grader'] ?? '';
        $sport      = strtolower(trim($this->request->post['sport'] ?? ''));
        $account_id = (int)($this->request->post['account_id'] ?? 1);
        $per_page   = 200; // eBay Browse API max per page

        if (!$keyword) {
            $json['error'] = $this->language->get('error_keyword_required') ?? 'Keyword is required.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Map category key to eBay Sport aspect filter value
        $sportAspectMap = [
            'hockey'     => 'Ice Hockey',
            'basketball' => 'Basketball',
            'baseball'   => 'Baseball',
            'football'   => 'American Football',
            'soccer'     => 'Soccer',
            'tennis'     => 'Tennis',
            'golf'       => 'Golf',
        ];
        $sportAspect = $sportAspectMap[$sport] ?? '';

        // condition_bucket = 'graded' ou 'raw' — envoyé par le JS pour le double-fetch
        // Si absent on utilise $cond directement (rétrocompatibilité)
        $bucket = $this->request->post['condition_bucket'] ?? $cond;

        $options = [
            'condition_type' => $bucket,
            'site_id'        => $site_id,
            'category_id'    => '212',
            'limit'          => $per_page,
            'page'           => $page,
        ];
        if ($grader)      $options['grader']       = $grader;
        if ($sportAspect) $options['sport_aspect'] = $sportAspect;

        // Vide le buffer uniquement au tout premier appel (page 1, bucket graded ou all)
        if ($page === 1 && in_array($bucket, ['graded', 'all'], true)) {
            $this->model_shopmanager_card_import_card_price_active->clearRawByKeyword($keyword);
        }

        $result = $this->model_shopmanager_ebay->searchActiveItems($keyword, $options, $account_id);

        if (!empty($result['error']) && empty($result['items'])) {
            $json['success']   = false;
            $json['message']   = '[eBay API] ' . $result['error'];
            $json['debug_raw'] = $result;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $ebay_total  = (int)($result['total'] ?? 0);
        $max_fetch   = min($ebay_total, 10000);
        $total_pages = $max_fetch > 0 ? (int)ceil($max_fetch / $per_page) : 1;

        if (empty($result['items']) && $page === 1) {
            $json['success']     = false;
            $json['message']     = $this->language->get('text_no_results') ?? 'No results from eBay.';
            $json['total']       = $ebay_total;
            $json['total_pages'] = $total_pages;
            $json['page']        = $page;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $inserted = 0;
        foreach ($result['items'] as $item) {
            $raw_id = $this->model_shopmanager_card_import_card_price_active->insertRaw($item, $keyword);
            if ($raw_id > 0) $inserted++;
        }

        // completed = on a atteint la limite offset eBay OU eBay n'a plus rien (items vides après page 1)
        $completed = ($page >= $total_pages) || (empty($result['items']) && $page > 1);

        $json['success']     = true;
        $json['completed']   = $completed;
        $json['fetched']     = count($result['items']);
        $json['inserted']    = $inserted;
        $json['total']       = $ebay_total;
        $json['total_pages'] = $total_pages;
        $json['page']        = $page;
        $json['keyword']     = $keyword;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // ------------------------------------------------------------------ //
    //  AJAX: process raw records -- match against oc_card_set
    // ------------------------------------------------------------------ //

    public function processRaw(): void {
        @set_time_limit(300);

        $this->load->language('shopmanager/card/import/card_price_active');
        $this->load->model('shopmanager/card/import/card_price_active');
        $this->load->model('shopmanager/card/card');

        $json = [];

        // Load all card_set rows once for matching
        $scpCards = $this->model_shopmanager_card_import_card_price_active->getCardSetAll();

        if (empty($scpCards)) {
            $json['success'] = false;
            $json['warning'] = $this->language->get('warning_no_card_set') ?? 'No cards in oc_card_set to match against.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $pending = $this->model_shopmanager_card_import_card_price_active->getRawPending();

        $matched_count  = 0;
        $deleted_count  = 0;
        $raw_ids_delete = [];

        foreach ($pending as $raw) {
            $parsed = $this->parseTitleFields($raw['title']);

            $sale = [
                'title'     => $raw['title'],
                'cardNum'   => $parsed['cardNum'],
                'titleYear' => $parsed['titleYear'],
            ];

            $match = $this->model_shopmanager_card_card->matchSale($sale, $scpCards);

            if ($match) {
                $this->model_shopmanager_card_import_card_price_active->setRawMatched((int)$raw['raw_id'], (int)$match['card_raw_id']);

                $item_for_active = [
                    'item_id'        => $raw['ebay_item_id'],
                    'title'          => $raw['title'],
                    'url'            => $raw['url'],
                    'picture'        => $raw['picture'],
                    'price'          => $raw['price'],
                    'currency'       => $raw['currency'],
                    'condition_type' => $raw['condition_type'],
                    'condition'      => $raw['condition_type'],
                    'date_sold'      => $raw['date_sold'],
                    'grade'          => $raw['grade'],
                    'grader'         => $raw['grader'],
                    'grade_score'    => $raw['grade_score'],
                    'is_graded'      => $raw['is_graded'],
                ];
                $this->model_shopmanager_card_import_card_price_active->insertActive(
                    (int)$match['card_raw_id'],
                    $item_for_active,
                    $raw['keyword']
                );
                $matched_count++;
            } else {
                // Collecte les IDs à supprimer en batch
                $raw_ids_delete[] = (int)$raw['raw_id'];
                $deleted_count++;
            }
        }

        // Hard DELETE les non-matchés en une seule requête
        if (!empty($raw_ids_delete)) {
            $this->model_shopmanager_card_import_card_price_active->deleteRawByIds($raw_ids_delete);
        }

        // Vide tout le raw buffer (matched + tout ce qui reste)
        $this->model_shopmanager_card_import_card_price_active->clearRaw();

        $stats = $this->model_shopmanager_card_import_card_price_active->getRawStats();

        $json['success']   = true;
        $json['processed'] = count($pending);
        $json['matched']   = $matched_count;
        $json['deleted']   = $deleted_count;
        $json['stats']     = $stats;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // ------------------------------------------------------------------ //
    //  AJAX: soft-delete an active record
    // ------------------------------------------------------------------ //

    public function deleteActive(): void {
        $this->load->language('shopmanager/card/import/card_price_active');
        $this->load->model('shopmanager/card/import/card_price_active');

        $json = [];

        $selected = $this->request->post['selected'] ?? [];
        if (!empty($selected) && is_array($selected)) {
            $this->model_shopmanager_card_import_card_price_active->deleteActiveByIds($selected);
            $json['success'] = true;
            $json['deleted'] = count($selected);
        } else {
            $active_id = (int)($this->request->post['active_id'] ?? 0);
            if (!$active_id) {
                $json['error'] = 'Invalid ID.';
            } else {
                $this->model_shopmanager_card_import_card_price_active->deleteActive($active_id);
                $json['success'] = true;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // ------------------------------------------------------------------ //
    //  Private helpers
    // ------------------------------------------------------------------ //

    /**
     * Extract card number and year from an eBay listing title.
     *
     * Examples:
     *   "1991-92 O-Pee-Chee Mario Lemieux #66 PSA 9"  → cardNum="66", titleYear="1991-92"
     *   "2020-21 Upper Deck #100 BGS 10"               → cardNum="100", titleYear="2020-21"
     *   "Wayne Gretzky 1984 Topps Card #99"            → cardNum="99",  titleYear="1984"
     */
    private function parseTitleFields(string $title): array {
        $cardNum   = '';
        $titleYear = '';

        // Extract year: YYYY or YYYY-YY or YYYY-YYYY
        if (preg_match('/\b((19|20)\d{2})(?:-(\d{2,4}))?\b/', $title, $m)) {
            $titleYear = $m[1];
            if (!empty($m[3])) $titleYear .= '-' . $m[3];
        }

        // Extract card number: #123 or #123a or Card 123
        if (preg_match('/#\s*([A-Z0-9]{1,10})\b/i', $title, $m)) {
            $cardNum = strtoupper(trim($m[1]));
        } elseif (preg_match('/\bcard\s+#?\s*([A-Z0-9]{1,10})\b/i', $title, $m)) {
            $cardNum = strtoupper(trim($m[1]));
        }

        return ['cardNum' => $cardNum, 'titleYear' => $titleYear];
    }
}
