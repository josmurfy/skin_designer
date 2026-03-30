<?php
namespace Opencart\Admin\Controller\Shopmanager\Card;

class CardImportSold extends \Opencart\System\Engine\Controller {

    // ─── Main page ────────────────────────────────────────────────────────────

    public function index(): void {
        $lang = $this->load->language('shopmanager/card/card_import_sold');
        $this->load->model('shopmanager/card/card_import_sold');
        $this->model_shopmanager_card_card_import_sold->ensureTable();

        $data = $lang;
        $data['heading_title'] = $lang['heading_title'];

        $data['breadcrumbs'] = [
            ['text' => $lang['text_home'],  'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)],
            ['text' => $lang['heading_title'], 'href' => $this->url->link('shopmanager/card/card_import_sold', 'user_token=' . $this->session->data['user_token'], true)],
        ];

        // Filter params
        $filters = [
            'filter_title'                => $this->request->get['filter_title']               ?? '',
            'filter_category'             => $this->request->get['filter_category']            ?? '',
            'filter_year'                 => $this->request->get['filter_year']                ?? '',
            'filter_brand'                => $this->request->get['filter_brand']               ?? '',
            'filter_set'                  => $this->request->get['filter_set']                 ?? '',
            'filter_player'               => $this->request->get['filter_player']              ?? '',
            'filter_card_number'          => $this->request->get['filter_card_number']         ?? '',
            'filter_grader'               => $this->request->get['filter_grader']              ?? '',
            'filter_min_price'            => $this->request->get['filter_min_price']           ?? '',
            'filter_max_price'            => $this->request->get['filter_max_price']           ?? '',
            'filter_missing_card_number'  => $this->request->get['filter_missing_card_number'] ?? '',
        ];
        $sort  = $this->request->get['sort']  ?? 'sold_id';
        $order = $this->request->get['order'] ?? 'DESC';
        $limit = (int)($this->request->get['limit'] ?? 25);
        $page  = max(1, (int)($this->request->get['page'] ?? 1));
        $start = ($page - 1) * $limit;

        $queryData = array_merge($filters, [
            'sort'  => $sort,
            'order' => $order,
            'start' => $start,
            'limit' => $limit,
        ]);

        $total = $this->model_shopmanager_card_card_import_sold->getTotalSoldRecords($queryData);
        $records = $this->model_shopmanager_card_card_import_sold->getSoldRecords($queryData);

        $data['list']    = $this->buildListHtml($records, $lang, $sort, $order, $queryData, $total, $page, $limit);
        $data['filters'] = $filters;
        $data['limit']   = $limit;
        $data['brands']  = $this->model_shopmanager_card_card_import_sold->getDistinctValues('brand');
        $data['graders'] = $this->model_shopmanager_card_card_import_sold->getDistinctValues('grader');

        // URLs
        $ut = 'user_token=' . $this->session->data['user_token'];
        $data['user_token']   = $this->session->data['user_token'];
        $data['upload']       = $this->url->link('shopmanager/card/card_import_sold.upload',   $ut, true);
        $data['save']         = $this->url->link('shopmanager/card/card_import_sold.save',     $ut, true);
        $data['delete']       = $this->url->link('shopmanager/card/card_import_sold.delete',   $ut, true);
        $data['truncate']     = $this->url->link('shopmanager/card/card_import_sold.truncate', $ut, true);

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/card/card_import_sold', $data));
    }

    // ─── AJAX: list (returns HTML fragment) ──────────────────────────────────

    public function list(): void {
        $lang = $this->load->language('shopmanager/card/card_import_sold');
        $this->load->model('shopmanager/card/card_import_sold');

        $filters = [
            'filter_title'               => $this->request->get['filter_title']               ?? '',
            'filter_category'            => $this->request->get['filter_category']            ?? '',
            'filter_year'                => $this->request->get['filter_year']                ?? '',
            'filter_brand'               => $this->request->get['filter_brand']               ?? '',
            'filter_set'                 => $this->request->get['filter_set']                 ?? '',
            'filter_player'              => $this->request->get['filter_player']              ?? '',
            'filter_card_number'         => $this->request->get['filter_card_number']         ?? '',
            'filter_grader'              => $this->request->get['filter_grader']              ?? '',
            'filter_min_price'           => $this->request->get['filter_min_price']           ?? '',
            'filter_max_price'           => $this->request->get['filter_max_price']           ?? '',
            'filter_missing_card_number' => $this->request->get['filter_missing_card_number'] ?? '',
        ];
        $sort  = $this->request->get['sort']  ?? 'sold_id';
        $order = $this->request->get['order'] ?? 'DESC';
        $limit = (int)($this->request->get['limit'] ?? 25);
        $page  = max(1, (int)($this->request->get['page'] ?? 1));
        $start = ($page - 1) * $limit;

        $queryData = array_merge($filters, [
            'sort' => $sort, 'order' => $order, 'start' => $start, 'limit' => $limit,
        ]);

        $total   = $this->model_shopmanager_card_card_import_sold->getTotalSoldRecords($queryData);
        $records = $this->model_shopmanager_card_card_import_sold->getSoldRecords($queryData);

        $this->response->setOutput(
            $this->buildListHtml($records, $lang, $sort, $order, $queryData, $total, $page, $limit)
        );
    }

    // ─── AJAX: upload CSV → preview ───────────────────────────────────────────

    public function upload(): void {
        $lang = $this->load->language('shopmanager/card/card_import_sold');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_sold')) {
            $json['error'] = $lang['error_permission'];
            $this->sendJsonResponse($json);
            return;
        }

        if (empty($_FILES['file']['tmp_name']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $json['error'] = $lang['error_no_file'];
            $this->sendJsonResponse($json);
            return;
        }

        $tmpFile = $_FILES['file']['tmp_name'];
        $handle  = fopen($tmpFile, 'r');
        if (!$handle) {
            $json['error'] = $lang['error_empty_file'];
            $this->sendJsonResponse($json);
            return;
        }

        // Read header
        $header = fgetcsv($handle);
        if (!$header) {
            fclose($handle);
            $json['error'] = $lang['error_empty_file'];
            $this->sendJsonResponse($json);
            return;
        }
        $header = array_map('trim', $header);
        // Remove BOM
        if (isset($header[0])) {
            $header[0] = ltrim($header[0], "\xEF\xBB\xBF\xFF\xFE");
        }

        $rows    = [];
        $index   = 0;
        while (($row = fgetcsv($handle)) !== false) {
            if (count(array_filter($row, fn($v) => trim($v) !== '')) === 0) continue;
            $assoc = [];
            foreach ($header as $i => $col) {
                $assoc[$col] = trim((string)($row[$i] ?? ''));
            }
            $assoc['_index'] = $index++;
            $rows[] = $assoc;
        }
        fclose($handle);

        if (empty($rows)) {
            $json['error'] = $lang['error_empty_file'];
            $this->sendJsonResponse($json);
            return;
        }

        $json['success']      = true;
        $json['total']        = count($rows);
        $json['cards']        = $rows;
        $json['preview_html'] = $this->buildPreviewHtml($rows, $lang);

        $this->sendJsonResponse($json);
    }

    // ─── AJAX: save rows to DB ────────────────────────────────────────────────

    public function save(): void {
        $lang = $this->load->language('shopmanager/card/card_import_sold');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_sold')) {
            $json['error'] = $lang['error_permission'];
            $this->sendJsonResponse($json);
            return;
        }

        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, true);
        $rows = $body['rows'] ?? [];

        if (!is_array($rows) || empty($rows)) {
            $rows = $this->request->post['rows'] ?? [];
        }

        if (!is_array($rows) || empty($rows)) {
            $json['error'] = $lang['error_no_data'];
            $this->sendJsonResponse($json);
            return;
        }

        $this->load->model('shopmanager/card/card_import_sold');

        $inserted = 0;
        $skipped  = 0;

        foreach ($rows as $row) {
            if (!is_array($row)) { $skipped++; continue; }
            try {
                $this->model_shopmanager_card_card_import_sold->insertSoldRecord($row);
                $inserted++;
            } catch (\Throwable $e) {
                $this->clog('[save] Exception: ' . $e->getMessage());
                $skipped++;
            }
        }

        $json['success']  = true;
        $json['inserted'] = $inserted;
        $json['skipped']  = $skipped;
        $json['total']    = $inserted + $skipped;

        $this->sendJsonResponse($json);
    }

    // ─── AJAX: delete selected ────────────────────────────────────────────────

    public function delete(): void {
        $lang = $this->load->language('shopmanager/card/card_import_sold');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_sold')) {
            $json['error'] = $lang['error_permission'];
            $this->sendJsonResponse($json);
            return;
        }

        $selected = $this->request->post['selected'] ?? [];
        if (!is_array($selected) || empty($selected)) {
            $json['error'] = $lang['error_no_data'];
            $this->sendJsonResponse($json);
            return;
        }

        $this->load->model('shopmanager/card/card_import_sold');
        $this->model_shopmanager_card_card_import_sold->deleteSoldRecords($selected);

        $json['success'] = true;
        $json['deleted'] = count($selected);

        $this->sendJsonResponse($json);
    }

    // ─── AJAX: truncate ─────────────────────────────────────────────────────

    public function truncate(): void {
        $lang = $this->load->language('shopmanager/card/card_import_sold');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/card/card_import_sold')) {
            $json['error'] = $lang['error_permission'];
            $this->sendJsonResponse($json);
            return;
        }

        $this->load->model('shopmanager/card/card_import_sold');
        $this->model_shopmanager_card_card_import_sold->truncateSold();

        $json['success'] = true;
        $json['total']   = 0;

        $this->sendJsonResponse($json);
    }

    // ─── AJAX: autocomplete ──────────────────────────────────────────────────

    public function autocomplete(): void {
        $this->load->model('shopmanager/card/card_import_sold');
        $field  = trim((string)($this->request->get['field'] ?? ''));
        $term   = trim((string)($this->request->get['term']  ?? ''));
        $values = $this->model_shopmanager_card_card_import_sold->autocompleteField($field, $term);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($values));
    }

    // ─── Build Preview HTML ───────────────────────────────────────────────────

    private function buildPreviewHtml(array $rows, array $lang): string {
        if (empty($rows)) {
            return '<p class="text-muted">' . htmlspecialchars($lang['text_no_records']) . '</p>';
        }

        // Sort by card_number for informational grouping (empty last)
        usort($rows, function(array $a, array $b): int {
            $na = trim((string)($a['card_number'] ?? ''));
            $nb = trim((string)($b['card_number'] ?? ''));
            if ($na === '' && $nb === '') return 0;
            if ($na === '') return 1;
            if ($nb === '') return -1;
            // natural sort for numbers
            return strnatcasecmp($na, $nb);
        });

        $html  = '<div class="table-responsive" style="font-size:12px;">';
        $html .= '<table class="table table-bordered table-hover table-sm mb-0" id="preview-table">';
        $html .= '<thead class="table-dark"><tr>';
        $html .= '<th style="width:36px;"><input type="checkbox" id="preview-check-all" class="form-check-input" checked></th>';
        $html .= '<th>#</th>';
        $html .= '<th>' . htmlspecialchars($lang['column_front_image'])   . '</th>';
        $html .= '<th style="min-width:160px;">'  . htmlspecialchars($lang['column_title'])       . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_category'])     . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_year'])         . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_brand'])        . '</th>';
        $html .= '<th style="min-width:140px;">'  . htmlspecialchars($lang['column_set'])         . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_subset'])       . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_player'])       . '</th>';
        $html .= '<th style="min-width:80px;"><span class="text-warning">★ ' . htmlspecialchars($lang['column_card_number']) . '</span></th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_attributes'])   . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_team'])         . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_variation'])    . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_grader'])       . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_grade'])        . '</th>';
        $html .= '<th style="min-width:80px;">'   . htmlspecialchars($lang['column_price'])       . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_currency'])     . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_type_listing']) . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_bids'])         . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_total_sold'])   . '</th>';
        $html .= '<th>'  . htmlspecialchars($lang['column_ebay_item_id']) . '</th>';
        $html .= '<th style="min-width:80px;">'   . htmlspecialchars($lang['column_status'])      . '</th>';
        $html .= '<th style="min-width:110px;">'  . htmlspecialchars($lang['column_date_sold'])   . '</th>';
        $html .= '<th style="width:36px;"></th>';
        $html .= '</tr></thead><tbody>';

        $lastCardNumber = '~~NONE~~';
        $groupNum       = 0;
        $rowNum         = 1;

        foreach ($rows as $row) {
            $cardNumber    = trim((string)($row['card_number'] ?? ''));
            $missingCardNr = ($cardNumber === '');

            // Group separator when card_number changes
            if ($cardNumber !== $lastCardNumber) {
                $lastCardNumber = $cardNumber;
                $groupNum++;
                $groupLabel = $missingCardNr
                    ? '<span class="text-danger fw-bold">⚠ ' . htmlspecialchars($lang['text_missing_card_number']) . '</span>'
                    : '<span class="badge bg-secondary me-1"># ' . htmlspecialchars($cardNumber) . '</span>';
                $html .= '<tr class="table-secondary" style="font-size:11px;"><td colspan="26" class="py-1 px-2">'
                       . '<i class="fa-solid fa-layer-group me-1 text-muted"></i>'
                       . htmlspecialchars($lang['text_group']) . ' ' . $groupNum . ' — ' . $groupLabel
                       . '</td></tr>';
            }

            $rowClass = $missingCardNr ? 'table-danger' : '';
            $idx      = (int)($row['_index'] ?? 0);

            $html .= '<tr class="' . $rowClass . '" data-index="' . $idx . '">';

            // Checkbox
            $html .= '<td><input type="checkbox" class="form-check-input preview-row-check" checked></td>';

            // Row number
            $html .= '<td class="text-center text-muted">' . $rowNum++ . '</td>';

            // Image
            $img = htmlspecialchars((string)($row['front_image'] ?? ''));
            if ($img) {
                $html .= '<td><img src="' . $img . '" style="max-height:60px;max-width:60px;cursor:zoom-in;" class="preview-thumb-img" data-fullsrc="' . $img . '" onerror="this.style.display=\'none\'"></td>';
            } else {
                $html .= '<td class="text-muted text-center">—</td>';
            }

            // Static display fields (non-editable in preview)
            $html .= '<td>' . htmlspecialchars((string)($row['title']      ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)($row['category']   ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)($row['year']       ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)($row['brand']      ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)($row['set_name']   ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)($row['subset']     ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)($row['player']     ?? '')) . '</td>';

            // card_number — editable, red border if empty
            $cnBorder = $missingCardNr ? 'border-color:#dc3545;background:#fff3cd;' : '';
            $html .= '<td><input type="text" class="form-control form-control-sm field-card_number" '
                   . 'style="min-width:70px;' . $cnBorder . '" '
                   . 'value="' . htmlspecialchars($cardNumber) . '" '
                   . 'data-index="' . $idx . '"></td>';

            // More static fields
            $html .= '<td>' . htmlspecialchars((string)($row['attributes'] ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)($row['team']       ?? '')) . '</td>';
            $html .= '<td>' . htmlspecialchars((string)($row['variation']  ?? '')) . '</td>';

            // Editable: grader
            $html .= '<td><input type="text" class="form-control form-control-sm field-grader" style="min-width:60px;" '
                   . 'value="' . htmlspecialchars((string)($row['grader'] ?? '')) . '" data-index="' . $idx . '"></td>';

            // Editable: grade
            $html .= '<td><input type="text" class="form-control form-control-sm field-grade" style="min-width:50px;" '
                   . 'value="' . htmlspecialchars((string)($row['grade'] ?? '')) . '" data-index="' . $idx . '"></td>';

            // Editable: price
            $html .= '<td><input type="number" class="form-control form-control-sm field-price" style="min-width:70px;" '
                   . 'step="0.01" min="0" '
                   . 'value="' . htmlspecialchars((string)($row['price'] ?? '0')) . '" data-index="' . $idx . '"></td>';

            // Editable: currency
            $html .= '<td><select class="form-select form-select-sm field-currency" style="min-width:60px;" data-index="' . $idx . '">';
            foreach (['CAD','USD','EUR','GBP'] as $cur) {
                $sel = ((string)($row['currency'] ?? 'CAD') === $cur) ? ' selected' : '';
                $html .= '<option value="' . $cur . '"' . $sel . '>' . $cur . '</option>';
            }
            $html .= '</select></td>';

            // Editable: type_listing
            $html .= '<td><select class="form-select form-select-sm field-type_listing" style="min-width:90px;" data-index="' . $idx . '">';
            foreach (['buy_it_now' => 'BIN', 'auction' => 'Auction'] as $val => $label) {
                $sel = ((string)($row['type_listing'] ?? '') === $val) ? ' selected' : '';
                $html .= '<option value="' . $val . '"' . $sel . '>' . $label . '</option>';
            }
            $html .= '</select></td>';

            // Editable: bids
            $html .= '<td><input type="number" class="form-control form-control-sm field-bids" style="min-width:50px;" '
                   . 'min="0" value="' . (int)($row['bids'] ?? 0) . '" data-index="' . $idx . '"></td>';

            // Editable: total_sold
            $html .= '<td><input type="number" class="form-control form-control-sm field-total_sold" style="min-width:50px;" '
                   . 'min="0" value="' . (int)($row['total_sold'] ?? 1) . '" data-index="' . $idx . '"></td>';

            // eBay item ID
            $eid = htmlspecialchars((string)($row['ebay_item_id'] ?? ''));
            if ($eid) {
                $html .= '<td><a href="https://www.ebay.ca/itm/' . $eid . '" target="_blank" style="font-size:11px;">' . $eid . '</a></td>';
            } else {
                $html .= '<td class="text-muted">—</td>';
            }

            // Editable: status
            $html .= '<td><select class="form-select form-select-sm field-status" data-index="' . $idx . '">';
            $s1 = ((string)($row['status'] ?? '1') === '1') ? ' selected' : '';
            $s0 = ((string)($row['status'] ?? '1') === '0') ? ' selected' : '';
            $html .= '<option value="1"' . $s1 . '>' . htmlspecialchars($lang['text_enabled'])  . '</option>';
            $html .= '<option value="0"' . $s0 . '>' . htmlspecialchars($lang['text_disabled']) . '</option>';
            $html .= '</select></td>';

            // Editable: date_sold
            $html .= '<td><input type="date" class="form-control form-control-sm field-date_sold" '
                   . 'value="' . htmlspecialchars((string)($row['date_sold'] ?? '')) . '" data-index="' . $idx . '"></td>';

            // Delete button
            $html .= '<td><button type="button" class="btn btn-sm btn-outline-danger btn-preview-delete" title="' . htmlspecialchars($lang['button_remove']) . '"><i class="fa-solid fa-xmark"></i></button></td>';

            $html .= '</tr>';
        }

        $html .= '</tbody></table></div>';
        return $html;
    }

    // ─── Build list HTML ─────────────────────────────────────────────────────

    private function buildListHtml(array $records, array $lang, string $sort, string $order, array $queryData, int $total, int $page, int $limit): string {
        $ut = 'user_token=' . $this->session->data['user_token'];

        // Build sort URLs
        $buildSortUrl = function(string $field) use ($sort, $order, $queryData, $ut): string {
            $newOrder = ($sort === $field && $order === 'ASC') ? 'DESC' : 'ASC';
            $params   = array_merge(array_filter($queryData, fn($v, $k) => !in_array($k, ['sort','order','start','limit']), ARRAY_FILTER_USE_BOTH));
            return 'index.php?route=shopmanager/card/card_import_sold.list&' . $ut . '&sort=' . $field . '&order=' . $newOrder . '&limit=' . $queryData['limit'] . '&' . http_build_query($params);
        };

        $data = [];
        foreach (['sold_id','title','category','year','brand','set_name','player','card_number','price','grader','grade','date_sold','date_added'] as $f) {
            $data['sort_' . $f] = $buildSortUrl($f);
        }
        $data['sort']   = $sort;
        $data['order']  = $order;
        $data['records'] = $records;
        $data  += $lang;

        // Pagination
        $this->load->language('shopmanager/card/card_import_sold');
        $this->load->library('pagination');
        $pagination = new \Opencart\System\Library\Pagination();
        $pagination->total = $total;
        $pagination->page  = $page;
        $pagination->limit = $limit;

        $paginationParams = array_filter($queryData, fn($v, $k) => !in_array($k, ['start','limit']), ARRAY_FILTER_USE_BOTH);
        $pagination->url   = 'index.php?route=shopmanager/card/card_import_sold.list&' . $ut . '&' . http_build_query($paginationParams) . '&page={page}';
        $data['pagination'] = $pagination->render();

        $start_row = ($page - 1) * $limit + 1;
        $end_row   = min($page * $limit, $total);
        $data['results'] = $total > 0
            ? sprintf($lang['text_pagination'] ?? 'Showing %d-%d of %d', $start_row, $end_row, $total)
            : ($lang['text_no_records'] ?? 'No records');

        return $this->load->view('shopmanager/card/card_import_sold_list', $data);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function sendJsonResponse(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    private function clog(string $msg): void {
        $logFile = '/home/n7f9655/public_html/storage_phoenixliquidation/logs/card_import_sold.log';
        file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] ' . $msg . "\n", FILE_APPEND | LOCK_EX);
    }
}
