<?php
namespace Opencart\Admin\Controller\Shopmanager\Card;

/**
 * CardMarket — Recherche eBay marché des cartes vendues
 * Route : shopmanager/card/card_market
 * Pattern OC4 : index() / list() / getList()
 */
class CardMarket extends \Opencart\System\Engine\Controller {

    private function filters(): array {
        $g = $this->request->get;
        return [
            'filter_player'        => $g['filter_player']        ?? '',
            'filter_set'           => $g['filter_set']           ?? '',
            'filter_year'          => $g['filter_year']          ?? '',
            'filter_brand'         => $g['filter_brand']         ?? '',
            'filter_sport'         => $g['filter_sport']         ?? '',
            'filter_card_number'   => $g['filter_card_number']   ?? '',
            'filter_listing_type'  => $g['filter_listing_type']  ?? 'all',
            'filter_sale_mode'     => $g['filter_sale_mode']     ?? 'present',
            'filter_condition'     => $g['filter_condition']     ?? 'all',
            'filter_grader'        => $g['filter_grader']        ?? 'all',
            'filter_grade'         => $g['filter_grade']         ?? '',
            'filter_image_token'   => $g['filter_image_token']   ?? '',
            'filter_site_id'       => isset($g['filter_site_id']) ? (int)$g['filter_site_id'] : 0,
        ];
    }

    private function buildUrl(array $f): string {
        $url = '';
        foreach ($f as $k => $v) {
            if ($k === 'filter_condition'    && $v === 'all') continue;
            if ($k === 'filter_grader'       && $v === 'all') continue;
            if ($k === 'filter_listing_type' && $v === 'all') continue;
            if ($k === 'filter_sale_mode'    && $v === 'present') continue;
            if ($v !== '' && $v !== false) $url .= '&'.rawurlencode($k).'='.rawurlencode((string)$v);
        }
        return $url;
    }

    public function index(): void {
        $lang = $this->load->language('shopmanager/card/card_market');
        $data = $data ?? [];
        $data += $lang;
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        $f     = $this->filters();
        $sort  = $this->request->get['sort']  ?? 'price_desc';
        $limit = isset($this->request->get['limit']) ? (int)$this->request->get['limit'] : 100;
        $url   = $this->buildUrl($f);
        if ($sort  !== 'price_desc') $url .= '&sort='.rawurlencode($sort);
        if ($limit !== 100)           $url .= '&limit='.$limit;

        $data['breadcrumbs'] = [
            ['text' => ($lang['text_home'] ?? ''),     'href' => $this->url->link('common/dashboard',            'user_token='.$this->session->data['user_token'])],
            ['text' => ($lang['heading_title'] ?? ''),'href' => $this->url->link('shopmanager/card/card_market','user_token='.$this->session->data['user_token'].$url)],
        ];

        $this->load->model('shopmanager/card/card_manufacturer');
        $this->load->model('shopmanager/card/card_listing');
        $this->load->model('shopmanager/card/card_type');
        $data['manufacturers'] = array_column($this->model_shopmanager_card_card_manufacturer->getManufacturers(['filter_status' => 1]), 'name');
        $data['card_types']    = $this->model_shopmanager_card_card_type->getCardTypes();
        $data['list'] = $this->load->controller('shopmanager/card/card_market.getList');
        foreach ($f as $k => $v) $data[$k] = $v;
        $data['sort']             = $sort;
        $data['limit']            = $limit;
        $data['per_page_options'] = [50, 100, 200];
        $data['user_token']       = $this->session->data['user_token'];

        foreach (['heading_title','text_home','text_list','text_filter','button_filter','button_reset',
            'text_filter_player','text_filter_set','text_filter_year','text_filter_brand',
            'text_filter_sport','text_filter_card_number','text_filter_condition','text_filter_listing_type','text_filter_sale_mode',
            'text_filter_image','text_drop_image','text_drop_image_help','text_image_search',
            'text_filter_grader','text_filter_grade','text_filter_site','text_filter_per_page',
            'text_all','text_graded_only','text_raw_only','text_auction','text_fixed_price','text_sale_mode_present','text_sale_mode_sold',
            'entry_player','entry_set','entry_year','entry_brand','entry_sport',
            'entry_card_number','entry_grade','entry_limit',
            'site_us','site_ca','site_gb','site_de','site_fr','text_loading','button_clear_image'] as $k)
            $data[$k] = ($lang[$k] ?? '');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('shopmanager/card/card_market', $data));
    }

    public function list(): void {
        // $this->log->write('[CardMarket] list() called – user_token=' . ($this->session->data['user_token'] ?? 'NONE'));
        $lang = $this->load->language('shopmanager/card/card_market');
        $data = $data ?? [];
        $data += $lang;
        try {
            $output = $this->load->controller('shopmanager/card/card_market.getList');
            // $this->log->write('[CardMarket] list() OK – output len=' . strlen($output));
            $this->response->setOutput($output);
        } catch (\Exception $e) {
            // $this->log->write('[CardMarket] list() EXCEPTION: ' . $e->getMessage());
            $this->response->setOutput('<div class="alert alert-danger"><strong>CardMarket list() error:</strong> ' . htmlspecialchars($e->getMessage()) . '</div>');
        }
    }

    public function getList(): string {
        // $this->log->write('[CardMarket] getList() called – GET=' . json_encode($this->request->get));
        $lang = $this->load->language('shopmanager/card/card_market');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/card/card_market');
        $f     = $this->filters();
        $sort  = $this->request->get['sort']  ?? 'price_desc';
        $page  = max(1, (int)($this->request->get['page']  ?? 1));
        $limit = max(1, (int)($this->request->get['limit'] ?? 100));
        $url   = $this->buildUrl($f);
        if ($sort !== 'price_desc') $url .= '&sort='.rawurlencode($sort);
        $url .= '&limit='.$limit;

        $data = ['items' => [], 'total_found' => 0, 'error' => '', 'keyword' => ''];
        $has  = false;
        foreach ($f as $k => $v) {
            if ($k === 'filter_site_id') continue;
            if ($k === 'filter_listing_type' && $v === 'all') continue;
            if ($k === 'filter_sale_mode' && $v === 'present') continue;
            if (($k === 'filter_condition' || $k === 'filter_grader') && $v === 'all') continue;
            if ($v !== '') { $has = true; break; }
        }

        // $this->log->write('[CardMarket] getList() has_filters=' . ($has ? '1' : '0') . ' sort=' . $sort . ' page=' . $page . ' limit=' . $limit);

        if ($has) {
            try {
                $r = $this->model_shopmanager_card_card_market->searchCompletedItems(
                    array_merge($f, ['sort' => $sort, 'page' => $page, 'limit' => $limit])
                );
                $data['items']       = $r['items']  ?? [];
                $data['total_found'] = $r['total']   ?? 0;
                $data['keyword']     = $r['keyword'] ?? '';
                if (!empty($r['error'])) $data['error'] = $r['error'];
                // $this->log->write('[CardMarket] getList() searchCompletedItems returned total=' . (int)$data['total_found'] . ' items=' . count($data['items']) . ' keyword=' . (string)$data['keyword'] . ' error=' . (string)$data['error']);

                // Convert all prices to CAD using OpenCart currency library
                foreach ($data['items'] as &$it) {
                    $fromCurrency = !empty($it['currency']) ? $it['currency'] : 'USD';
                    $it['price_cad'] = $this->currency->convert((float)$it['price'], $fromCurrency, 'CAD');
                }
                unset($it);
            } catch (\Exception $e) {
                // $this->log->write('[CardMarket] searchCompletedItems() EXCEPTION: ' . $e->getMessage());
                $data['error'] = $e->getMessage();
            }
        }

        $total = (int)$data['total_found'];
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('shopmanager/card/card_market.list',
                'user_token='.$this->session->data['user_token'].$url.'&page={page}')
        ]);
        $data['results'] = sprintf(($lang['text_pagination'] ?? ''),
            $total ? ($page - 1) * $limit + 1 : 0,
            (($page - 1) * $limit > $total - $limit) ? $total : ($page - 1) * $limit + $limit,
            $total, ceil($total / ($limit ?: 1)));

        $data['sort'] = $sort;
        $base = 'user_token='.$this->session->data['user_token'].$url.'&page='.$page;
        $data['sort_price_desc'] = $this->url->link('shopmanager/card/card_market.list', $base.'&sort=price_desc');
        $data['sort_price_asc']  = $this->url->link('shopmanager/card/card_market.list', $base.'&sort=price_asc');
        $data['sort_date_desc']  = $this->url->link('shopmanager/card/card_market.list', $base.'&sort=date_desc');
        $kw = $data['keyword'] ?? '';
        $saleMode = (string)($f['filter_sale_mode'] ?? 'present');
        if ($kw === ($lang['text_image_search'] ?? '')) {
            $data['keyword_line'] = '<strong>' . htmlspecialchars($kw) . '</strong> &mdash; ' . $total . ' ' . ($lang['text_results'] ?? '');
        } else {
            $suffix = $saleMode === 'sold' ? ' sold' : ' present';
            $data['keyword_line'] = $kw ? '<strong>'.htmlspecialchars($kw).'</strong> &mdash; '.$total.$suffix : '';
        }

        foreach (['text_no_results','text_graded_badge','text_raw_badge','text_view_on_ebay','text_results',
            'text_keyword_used','text_search_notice','column_title','column_price',
            'column_date_sold','column_date_created','column_date_ended','column_grade','column_player','column_card_number',
            'text_sort_price_desc','text_sort_price_asc','text_sort_date','text_pagination',
            'text_auction','text_fixed_price','text_bids','text_cad_price','text_image_search'] as $k)
            $data[$k] = ($lang[$k] ?? '');

        $data['user_token'] = $this->session->data['user_token'];
        return $this->load->view('shopmanager/card/card_market_list', $data);
    }

    public function uploadImage(): void {
        $lang = $this->load->language('shopmanager/card/card_market');
        $data = $data ?? [];
        $data += $lang;

        $json = [];

        if (!$this->user->hasPermission('access', 'shopmanager/card/card_market')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } elseif (empty($this->request->files['image'])) {
            $json['error'] = ($lang['error_image_required'] ?? '');
        } else {
            $this->load->model('shopmanager/card/card_market');

            try {
                $result = $this->model_shopmanager_card_card_market->saveUploadedSearchImage($this->request->files['image']);

                $json['success'] = true;
                $json['token'] = $result['token'];
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }

    /** @deprecated kept for backward compat - not used */
    public function search(): void {
        $lang = $this->load->language('shopmanager/card/card_market');
        $data = $data ?? [];
        $data += $lang;
        $json = [];

        if (!$this->user->hasPermission('access', 'shopmanager/card/card_market')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $filters = [
            'set'            => trim($this->request->post['set'] ?? ''),
            'year'           => trim($this->request->post['year'] ?? ''),
            'brand'          => trim($this->request->post['brand'] ?? ''),
            'sport'          => trim($this->request->post['sport'] ?? ''),
            'player'         => trim($this->request->post['player'] ?? ''),
            'card_number'    => trim($this->request->post['card_number'] ?? ''),
            'condition_type' => trim($this->request->post['condition_type'] ?? 'all'), // all|graded|raw
            'grader'         => trim($this->request->post['grader'] ?? 'all'),          // all|psa|bgs|sgc|csa|hga
            'grade'          => trim($this->request->post['grade'] ?? ''),
            'site_id'        => (int)($this->request->post['site_id'] ?? 0),
            'per_page'       => min((int)($this->request->post['per_page'] ?? 25), 100),
            'page'           => max((int)($this->request->post['page'] ?? 1), 1),
        ];

        // Besoin d'au moins un mot-clé
        $keyword = implode(' ', array_filter([
            $filters['year'],
            $filters['brand'],
            $filters['set'],
            $filters['sport'],
            $filters['player'],
            $filters['card_number'] ? '#' . $filters['card_number'] : '',
        ]));

        if (strlen($keyword) < 3) {
            $json['error'] = ($lang['error_keyword_required'] ?? '');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        // Ajouter grader au mot-clé si filtré
        if ($filters['condition_type'] === 'graded' && $filters['grader'] !== 'all') {
            $keyword .= ' ' . strtoupper($filters['grader']);
        }
        if ($filters['grade'] !== '') {
            $keyword .= ' ' . $filters['grade'];
        }

        $filters['keyword'] = trim($keyword);

        $this->load->model('shopmanager/card/card_market');

        try {
            $result = $this->model_shopmanager_card_card_market->searchCompletedItems($filters);
            $json['success']    = true;
            $json['items']      = $result['items'];
            $json['total']      = $result['total'];
            $json['page']       = $filters['page'];
            $json['per_page']   = $filters['per_page'];
            $json['keyword']    = $filters['keyword'];
        } catch (\Exception $e) {
            $json['error'] = $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE));
    }
}
