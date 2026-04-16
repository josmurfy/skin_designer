<?php
// Original: warehouse/card/search.php
namespace Opencart\Admin\Controller\Warehouse\Card;

/**
 * Class Search
 *
 * Card Value Search — filter cards from oc_card_set by year/category/set/brand/player
 * and display only those worth pulling from a physical box.
 *
 * Uses model: warehouse/card/set (oc_card_set + oc_card_price_sold + oc_card_price_active)
 *
 * @package Opencart\Admin\Controller\Shopmanager\Card
 */
class Search extends \Opencart\System\Engine\Controller {

    /**
     * Main page — wrapper with filter panel + AJAX list area
     */
    public function index(): void {
        $this->load->language('warehouse/card/search');
        $data = [];
        

        $this->document->setTitle($lang['heading_title'] ?? 'Card Value Search');

        // ── Collect filter values for form pre-fill ──
        $filters = $this->getFiltersFromRequest();

        $data += $filters;

        // Breadcrumbs
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $lang['text_home'] ?? 'Home',
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        ];
        $data['breadcrumbs'][] = [
            'text' => $lang['heading_title'] ?? '',
            'href' => $this->url->link('warehouse/card/search', 'user_token=' . $this->session->data['user_token'], true)
        ];

        // ── Dropdown options from oc_card_set (cascading context) ──
        $this->load->model('warehouse/card/set');
        $context = $this->buildContext($filters);

        $data['options_category'] = $this->model_warehouse_card_set->getFilteredDistinct('category', $context);
        $data['options_year']     = $this->model_warehouse_card_set->getFilteredDistinct('year', $context);
        $data['options_brand']    = $this->model_warehouse_card_set->getFilteredDistinct('brand', $context);
        $data['options_set_name'] = $this->model_warehouse_card_set->getFilteredDistinct('set_name', $context);
        $data['options_player']   = $this->model_warehouse_card_set->getFilteredDistinct('player', $context);

        $data['user_token'] = $this->session->data['user_token'];

        $this->document->addScript('view/javascript/warehouse/card/search.js');

        // Pre-load list if any filter is set
        $has_filter = !empty($filters['filter_year']) || !empty($filters['filter_category'])
                   || !empty($filters['filter_brand']) || !empty($filters['filter_set'])
                   || !empty($filters['filter_player']) || !empty($filters['filter_card_number']);
        $data['list'] = $has_filter ? $this->getList() : '';

        $data['per_page_options'] = [25, 50, 100, 200, 500];

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('warehouse/card/search', $data));
    }

    /**
     * AJAX endpoint — returns only the list HTML fragment
     */
    public function list(): void {
        $this->load->language('warehouse/card/search');
        $this->response->setOutput($this->getList());
    }

    /**
     * Core list builder — queries oc_card_set + oc_card_price_sold
     *
     * @return string  Rendered HTML
     */
    public function getList(): string {
        $this->load->language('warehouse/card/search');

        $filters = $this->getFiltersFromRequest();

        // Sort / order / page
        $sort  = $this->request->get['sort'] ?? 'best_price';
        $order = $this->request->get['order'] ?? 'DESC';
        $page  = max(1, (int)($this->request->get['page'] ?? 1));
        $limit = (int)($this->request->get['limit'] ?? 50);

        // ── Build model filter array ──
        $filter_data = [
            'filter_year'        => $filters['filter_year'],
            'filter_category'    => $filters['filter_category'],
            'filter_brand'       => $filters['filter_brand'],
            'filter_set'         => $filters['filter_set'],
            'filter_player'      => $filters['filter_player'],
            'filter_card_number' => $filters['filter_card_number'],
            'filter_min_price'   => $filters['filter_min_price'],
            'filter_max_price'   => $filters['filter_max_price'],
            'sort'               => $sort,
            'order'              => $order,
            'start'              => ($page - 1) * $limit,
            'limit'              => $limit,
        ];

        // ── Fetch data ──
        $this->load->model('warehouse/card/set');

        $results    = $this->model_warehouse_card_set->getCardSets($filter_data);
        $total      = $this->model_warehouse_card_set->getTotalCardSets($filter_data);

        // ── Enrich with sold data ──
        $sold_bilan = [];
        if (!empty($results)) {
            $sold_bilan = $this->model_warehouse_card_set->getSoldBilanForCards($results);
        }

        // ── Enrich with active prices ──
        $active_prices = [];
        if (!empty($results)) {
            $card_raw_ids = array_column($results, 'card_raw_id');
            $active_prices = $this->model_warehouse_card_set->getActivePricesForCards($card_raw_ids);
        }

        // ── Business constants (USD) for grading ROI ──
        $ebay_fee_rate  = 0.13;    // 13% eBay final value fee
        $psa_fee_usd    = 25.00;   // PSA Economy grading fee (USD)
        $listing_fee    = 0.35;    // eBay insertion fee per listing

        // ── Map results ──
        $data = $lang;
        $data['cards'] = [];
        $sum_best = 0;
        $count_above = 0;
        $threshold = (float)($filters['filter_min_price'] ?: 5);

        foreach ($results as $row) {
            $ungraded = (float)($row['ungraded'] ?? 0);
            $grade_9  = (float)($row['grade_9']  ?? 0);
            $grade_10 = (float)($row['grade_10'] ?? 0);
            $best     = max($ungraded, $grade_9, $grade_10);

            // Price category
            if ($best >= 50) {
                $price_category = 'gold';
            } elseif ($best >= 10) {
                $price_category = 'orange';
            } elseif ($best >= 2) {
                $price_category = 'blue';
            } else {
                $price_category = 'gray';
            }

            // Sold stats
            $sold_key  = ($row['card_number'] ?? '') . '|||' . ($row['set_name'] ?? '');
            $sold_rows = $sold_bilan[$sold_key] ?? [];
            $sold_count = count($sold_rows);
            $sold_avg   = 0;
            if ($sold_count > 0) {
                $sold_avg = array_sum(array_column($sold_rows, 'price')) / $sold_count;
            }

            // Grading potential: grade_10 significantly > ungraded
            $grading_potential = ($grade_10 > 0 && $ungraded > 0) ? round(($grade_10 / $ungraded) * 100 - 100) : 0;

            // ── Grading profitability (USD) ──
            $net_raw    = ($ungraded > 0) ? $ungraded * (1 - $ebay_fee_rate) - $listing_fee : 0;
            $net_psa9   = ($grade_9 > 0)  ? $grade_9  * (1 - $ebay_fee_rate) - $listing_fee - $psa_fee_usd : 0;
            $net_psa10  = ($grade_10 > 0) ? $grade_10 * (1 - $ebay_fee_rate) - $listing_fee - $psa_fee_usd : 0;
            $best_graded_net = max($net_psa9, $net_psa10);
            $grading_gain = round($best_graded_net - max(0, $net_raw), 2);

            // Verdict: grade / sell_raw / lot
            if ($grading_gain > 15 && max($grade_9, $grade_10) >= 75) {
                $verdict = 'grade';
            } elseif ($best >= 5) {
                $verdict = 'sell_raw';
            } else {
                $verdict = 'lot';
            }

            // Active prices
            $card_raw_id = (int)($row['card_raw_id'] ?? 0);
            $active_rows = $active_prices[$card_raw_id] ?? [];
            $active_count = count($active_rows);
            $active_avg = 0;
            if ($active_count > 0) {
                $active_avg = array_sum(array_column($active_rows, 'price_usd')) / $active_count;
            }

            $sum_best += $best;
            if ($best >= $threshold) {
                $count_above++;
            }

            $data['cards'][] = [
                'card_raw_id'       => $row['card_raw_id'] ?? '',
                'card_number'       => $row['card_number'] ?? '',
                'player'            => $row['player'] ?? '',
                'team'              => $row['team'] ?? '',
                'set_name'          => $row['set_name'] ?? '',
                'subset'            => $row['subset'] ?? '',
                'year'              => $row['year'] ?? '',
                'brand'             => $row['brand'] ?? '',
                'category'          => $row['category'] ?? '',
                'attributes'        => $row['attributes'] ?? '',
                'variation'         => $row['variation'] ?? '',
                'ungraded'          => $ungraded,
                'grade_9'           => $grade_9,
                'grade_10'          => $grade_10,
                'best_price'        => $best,
                'price_category'    => $price_category,
                'front_image'       => $row['front_image'] ?? '',
                'sold_count'        => $sold_count,
                'sold_avg'          => round($sold_avg, 2),
                'active_count'      => $active_count,
                'active_avg'        => round($active_avg, 2),
                'grading_potential' => $grading_potential,
                'net_raw'           => round(max(0, $net_raw), 2),
                'net_psa10'         => round(max(0, $net_psa10), 2),
                'grading_gain'      => $grading_gain,
                'verdict'           => $verdict,
            ];
        }

        // ── Summary stats ──
        $data['summary'] = [
            'total_cards'  => $total,
            'total_value'  => round($sum_best, 2),
            'avg_value'    => ($total > 0) ? round($sum_best / min($total, count($results)), 2) : 0,
            'cards_above'  => $count_above,
            'threshold'    => $threshold,
        ];

        $data['user_token'] = $this->session->data['user_token'];
        $data['sort']       = $sort;
        $data['order']      = $order;

        // ── Sort links ──
        $url = $this->buildFilterUrl($filters);

        $sort_fields = ['card_number','player','set_name','year','brand','ungraded','grade_9','grade_10','best_price','grading_gain'];
        foreach ($sort_fields as $sf) {
            $data['sort_' . $sf] = $this->url->link(
                'warehouse/card/search.list',
                'user_token=' . $this->session->data['user_token'] . '&sort=' . $sf . $url, true
            );
        }

        // ── Pagination ──
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('warehouse/card/search.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true),
            'text'  => $lang['text_pagination'] ?? '',
        ]);

        $data['results'] = sprintf(
            ($lang['text_pagination'] ?? ''),
            ($total) ? (($page - 1) * $limit) + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
            $total,
            ceil($total / max($limit, 1))
        );

        // Pass filters for active state display
        $data += $filters;

        return $this->load->view('warehouse/card/search_list', $data);
    }

    /**
     * AJAX endpoint — returns cascading dropdown options as JSON.
     * Called when a dropdown changes to refresh the other dropdowns.
     */
    public function filterOptions(): void {
        $filters = $this->getFiltersFromRequest();
        $context = $this->buildContext($filters);

        $this->load->model('warehouse/card/set');

        $json = [
            'category' => $this->model_warehouse_card_set->getFilteredDistinct('category', $context),
            'year'     => $this->model_warehouse_card_set->getFilteredDistinct('year', $context),
            'brand'    => $this->model_warehouse_card_set->getFilteredDistinct('brand', $context),
            'set_name' => $this->model_warehouse_card_set->getFilteredDistinct('set_name', $context),
            'player'   => $this->model_warehouse_card_set->getFilteredDistinct('player', $context),
        ];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // ──────────────────────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────────────────

    /**
     * Extract all filter values from GET request
     */
    private function getFiltersFromRequest(): array {
        return [
            'filter_year'        => $this->request->get['filter_year']        ?? '',
            'filter_category'    => $this->request->get['filter_category']    ?? '',
            'filter_brand'       => $this->request->get['filter_brand']       ?? '',
            'filter_set'         => $this->request->get['filter_set']         ?? '',
            'filter_player'      => $this->request->get['filter_player']      ?? '',
            'filter_card_number' => $this->request->get['filter_card_number'] ?? '',
            'filter_min_price'   => $this->request->get['filter_min_price']   ?? '',
            'filter_max_price'   => $this->request->get['filter_max_price']   ?? '',
        ];
    }

    /**
     * Build context array for cascading dropdown queries.
     * Maps filter_* keys to DB column names.
     */
    private function buildContext(array $filters): array {
        $context = [];
        if (!empty($filters['filter_year']))        $context['year']        = $filters['filter_year'];
        if (!empty($filters['filter_category']))    $context['category']    = $filters['filter_category'];
        if (!empty($filters['filter_brand']))       $context['brand']       = $filters['filter_brand'];
        if (!empty($filters['filter_set']))         $context['set_name']    = $filters['filter_set'];
        if (!empty($filters['filter_player']))      $context['player']      = $filters['filter_player'];
        if (!empty($filters['filter_card_number'])) $context['card_number'] = $filters['filter_card_number'];
        return $context;
    }

    /**
     * Build URL query string from filter values
     */
    private function buildFilterUrl(array $filters): string {
        $url = '';
        foreach ($filters as $key => $val) {
            if ($val !== '' && $val !== null) {
                $url .= '&' . $key . '=' . urlencode((string)$val);
            }
        }
        if (isset($this->request->get['limit'])) {
            $url .= '&limit=' . (int)$this->request->get['limit'];
        }
        return $url;
    }
}
