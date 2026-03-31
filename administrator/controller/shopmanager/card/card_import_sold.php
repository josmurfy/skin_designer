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
        $sort  = $this->request->get['sort']  ?? 'card_price_sold_id';
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
        $sort  = $this->request->get['sort']  ?? 'card_price_sold_id';
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
            return strnatcasecmp($na, $nb);
        });

        $html  = '<div class="table-responsive" style="font-size:12px;">';
        $html .= '<table class="table table-bordered table-hover table-sm mb-0" id="preview-table">';
        $html .= '<thead class="table-dark"><tr>';
        $html .= '<th style="width:36px;"><input type="checkbox" id="preview-check-all" class="form-check-input" checked></th>';
        $html .= '<th style="width:28px;">#</th>';
        $html .= '<th style="width:70px;">' . htmlspecialchars($lang['column_front_image']) . '</th>';
        $html .= '<th style="min-width:220px;">' . htmlspecialchars($lang['column_title']) . ' / ' . htmlspecialchars($lang['column_player']) . '</th>';
        $html .= '<th style="min-width:130px;"><span class="text-warning">★ ' . htmlspecialchars($lang['column_card_number']) . '</span> / ' . htmlspecialchars($lang['column_grader']) . '</th>';
        $html .= '<th style="min-width:150px;">' . htmlspecialchars($lang['column_price']) . ' / ' . htmlspecialchars($lang['column_type_listing']) . '</th>';
        $html .= '<th style="min-width:130px;">' . htmlspecialchars($lang['column_ebay_item_id']) . ' / ' . htmlspecialchars($lang['column_date_sold']) . '</th>';
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
                $html .= '<tr class="table-secondary" style="font-size:11px;"><td colspan="8" class="py-1 px-2">'
                       . '<i class="fa-solid fa-layer-group me-1 text-muted"></i>'
                       . htmlspecialchars($lang['text_group']) . ' ' . $groupNum . ' — ' . $groupLabel
                       . '</td></tr>';
            }

            $isFuzzy  = ((string)($row['match_fuzzy'] ?? '0') === '1');
            $rowClass = $missingCardNr ? 'table-danger' : ($isFuzzy ? 'table-warning' : '');
            $idx      = (int)($row['_index'] ?? 0);

            $html .= '<tr class="' . $rowClass . '" data-index="' . $idx . '">';

            // Col 1 — Checkbox
            $html .= '<td><input type="checkbox" class="form-check-input preview-row-check" checked></td>';

            // Col 2 — Row number
            $html .= '<td class="text-center text-muted">' . $rowNum++ . '</td>';

            // Col 3 — Image
            $img = htmlspecialchars((string)($row['front_image'] ?? ''));
            if ($img) {
                $html .= '<td><img src="' . $img . '" style="max-height:60px;max-width:60px;cursor:zoom-in;" class="preview-thumb-img" data-fullsrc="' . $img . '" onerror="this.style.display=\'none\'"></td>';
            } else {
                $html .= '<td class="text-muted text-center">—</td>';
            }

            // Col 4 — Card identity (title, player, + meta badges)
            $ebayTitle = trim((string)($row['ebay_title'] ?? ''));
            $html .= '<td style="white-space:normal;">';
            $html .= '<div class="cell-title" style="font-size:12px;font-weight:600;line-height:1.3;">' . htmlspecialchars((string)($row['title'] ?? '')) . '</div>';
            $html .= '<div class="cell-player" style="font-size:11px;color:#555;margin-top:1px;">' . htmlspecialchars((string)($row['player'] ?? '')) . '</div>';
            if ($ebayTitle !== '') {
                // Bold player and card_number occurrences inside ebay_title for visual verification
                $highlightTerms = array_values(array_filter([
                    trim((string)($row['player']      ?? '')),
                    trim((string)($row['card_number'] ?? '')),
                ], fn(string $t): bool => $t !== ''));

                if (!empty($highlightTerms)) {
                    $quotedTerms = array_map(fn(string $t): string => preg_quote($t, '/'), $highlightTerms);
                    $parts = preg_split('/(' . implode('|', $quotedTerms) . ')/i', $ebayTitle, -1, PREG_SPLIT_DELIM_CAPTURE);
                    $matchPattern = '/^(' . implode('|', $quotedTerms) . ')$/i';
                    $ebayTitleHtml = '';
                    foreach ($parts as $part) {
                        $ebayTitleHtml .= preg_match($matchPattern, $part)
                            ? '<strong style="color:#198754;font-style:normal;">' . htmlspecialchars($part) . '</strong>'
                            : htmlspecialchars($part);
                    }
                } else {
                    $ebayTitleHtml = htmlspecialchars($ebayTitle);
                }
                $html .= '<div style="font-size:10px;color:#888;font-style:italic;margin-top:3px;border-left:2px solid #dee2e6;padding-left:5px;line-height:1.3;" title="eBay title (vérification seulement)">🏷 ' . $ebayTitleHtml . '</div>';
            }
            if ($isFuzzy) {
                $html .= '<div style="margin-top:5px;padding:4px 7px;background:#fff3cd;border:1px solid #ffc107;border-radius:4px;font-size:10px;color:#856404;line-height:1.4;">';
                $html .= '<span style="font-weight:700;">⚠ Correspondance approx.</span> — Vérifier que cette vente correspond bien à la carte avant d\'importer.';
                $html .= '</div>';
            }
            $html .= '<div style="margin-top:4px;display:flex;flex-wrap:wrap;gap:3px;">';
            $year    = (string)($row['year']     ?? ''); if ($year    !== '') $html .= '<span class="badge bg-secondary" style="font-size:10px;">' . htmlspecialchars($year)    . '</span>';
            $brand   = (string)($row['brand']    ?? ''); if ($brand   !== '') $html .= '<span class="badge bg-secondary" style="font-size:10px;">' . htmlspecialchars($brand)   . '</span>';
            $set     = (string)($row['set_name'] ?? ''); if ($set     !== '') $html .= '<span class="badge bg-dark"      style="font-size:10px;">' . htmlspecialchars($set)     . '</span>';
            $subset  = (string)($row['subset']   ?? ''); if ($subset  !== '') $html .= '<span class="badge bg-dark"      style="font-size:10px;">' . htmlspecialchars($subset)  . '</span>';
            $cat     = (string)($row['category'] ?? ''); if ($cat     !== '') $html .= '<span class="badge bg-info text-dark" style="font-size:10px;">' . htmlspecialchars($cat) . '</span>';
            $team    = (string)($row['team']     ?? ''); if ($team    !== '') $html .= '<span class="badge bg-light text-dark border" style="font-size:10px;">' . htmlspecialchars($team) . '</span>';
            $attr    = (string)($row['attributes'] ?? ''); if ($attr  !== '') $html .= '<span class="badge bg-light text-dark border" style="font-size:10px;">' . htmlspecialchars($attr) . '</span>';
            $vari    = (string)($row['variation'] ?? ''); if ($vari   !== '') $html .= '<span class="badge bg-light text-dark border" style="font-size:10px;">' . htmlspecialchars($vari) . '</span>';
            $html .= '</div>';
            $html .= '</td>';

            // Col 5 — Card # (editable) + Grader/Grade (editable)
            $cnBorder = $missingCardNr ? 'border-color:#dc3545;background:#fff3cd;' : '';
            $html .= '<td style="white-space:nowrap;">';
            $html .= '<div style="margin-bottom:4px;">';
            $html .= '<input type="text" class="form-control form-control-sm field-card_number" '
                   . 'style="' . $cnBorder . '" placeholder="' . htmlspecialchars($lang['column_card_number']) . '" '
                   . 'value="' . htmlspecialchars($cardNumber) . '" data-index="' . $idx . '">';
            $html .= '</div>';
            $html .= '<div style="display:flex;gap:3px;">';
            $html .= '<input type="text" class="form-control form-control-sm field-grader" style="min-width:55px;" '
                   . 'placeholder="' . htmlspecialchars($lang['column_grader']) . '" '
                   . 'value="' . htmlspecialchars((string)($row['grader'] ?? '')) . '" data-index="' . $idx . '">';
            $html .= '<input type="text" class="form-control form-control-sm field-grade" style="min-width:45px;" '
                   . 'placeholder="' . htmlspecialchars($lang['column_grade']) . '" '
                   . 'value="' . htmlspecialchars((string)($row['grade'] ?? '')) . '" data-index="' . $idx . '">';
            $html .= '</div>';
            $html .= '</td>';

            // Col 6 — Price / listing details (editable)
            $html .= '<td style="white-space:nowrap;">';
            $html .= '<div style="display:flex;gap:3px;margin-bottom:4px;">';
            $html .= '<input type="number" class="form-control form-control-sm field-price" style="min-width:65px;" '
                   . 'step="0.01" min="0" placeholder="' . htmlspecialchars($lang['column_price']) . '" '
                   . 'value="' . htmlspecialchars((string)($row['price'] ?? '0')) . '" data-index="' . $idx . '">';
            $html .= '<select class="form-select form-select-sm field-currency" style="min-width:62px;" data-index="' . $idx . '">';
            foreach (['CAD','USD','EUR','GBP'] as $cur) {
                $sel = ((string)($row['currency'] ?? 'CAD') === $cur) ? ' selected' : '';
                $html .= '<option value="' . $cur . '"' . $sel . '>' . $cur . '</option>';
            }
            $html .= '</select>';
            $html .= '</div>';
            $html .= '<div style="display:flex;gap:3px;">';
            $html .= '<select class="form-select form-select-sm field-type_listing" style="min-width:78px;" data-index="' . $idx . '">';
            foreach (['buy_it_now' => 'BIN', 'auction' => 'Auction'] as $val => $label) {
                $sel = ((string)($row['type_listing'] ?? '') === $val) ? ' selected' : '';
                $html .= '<option value="' . $val . '"' . $sel . '>' . $label . '</option>';
            }
            $html .= '</select>';
            $html .= '<input type="number" class="form-control form-control-sm field-bids" style="min-width:45px;" '
                   . 'min="0" placeholder="' . htmlspecialchars($lang['column_bids']) . '" value="' . (int)($row['bids'] ?? 0) . '" data-index="' . $idx . '">';
            $html .= '<input type="number" class="form-control form-control-sm field-total_sold" style="min-width:45px;" '
                   . 'min="0" placeholder="' . htmlspecialchars($lang['column_total_sold']) . '" value="' . (int)($row['total_sold'] ?? 1) . '" data-index="' . $idx . '">';
            $html .= '</div>';
            $html .= '</td>';

            // Col 7 — eBay ID / date + status
            $eid = htmlspecialchars((string)($row['ebay_item_id'] ?? ''));
            $html .= '<td style="font-size:11px;">';
            if ($eid) {
                $html .= '<div><a href="https://www.ebay.ca/itm/' . $eid . '" target="_blank" style="font-size:11px;">' . $eid . '</a></div>';
            }
            $html .= '<div style="margin-top:3px;">';
            $html .= '<input type="date" class="form-control form-control-sm field-date_sold" '
                   . 'value="' . htmlspecialchars((string)($row['date_sold'] ?? '')) . '" data-index="' . $idx . '">';
            $html .= '</div>';
            $html .= '<div style="margin-top:3px;">';
            $html .= '<select class="form-select form-select-sm field-status" data-index="' . $idx . '">';
            $s1 = ((string)($row['status'] ?? '1') === '1') ? ' selected' : '';
            $s0 = ((string)($row['status'] ?? '1') === '0') ? ' selected' : '';
            $html .= '<option value="1"' . $s1 . '>' . htmlspecialchars($lang['text_enabled'])  . '</option>';
            $html .= '<option value="0"' . $s0 . '>' . htmlspecialchars($lang['text_disabled']) . '</option>';
            $html .= '</select>';
            $html .= '</div>';
            $html .= '</td>';

            // Col 8 — Delete button
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
        foreach (['card_price_sold_id','title','category','year','brand','set_name','player','card_number','price','grader','grade','date_sold','date_added'] as $f) {
            $data['sort_' . $f] = $buildSortUrl($f);
        }
        $data['sort']    = $sort;
        $data['order']   = $order;
        $data['records'] = $records;
        $data  += $lang;

        // Build filter string for pagination URL (without sort/order/start/limit)
        $filterParams = array_filter($queryData, fn($v, $k) => !in_array($k, ['sort','order','start','limit']), ARRAY_FILTER_USE_BOTH);
        $filterUrl = '';
        foreach ($filterParams as $k => $v) {
            if ($v !== '') $filterUrl .= '&' . $k . '=' . rawurlencode((string)$v);
        }

        // OC4 pagination via controller
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link(
                'shopmanager/card/card_import_sold.list',
                'user_token=' . $this->session->data['user_token'] . $filterUrl . '&sort=' . $sort . '&order=' . $order . '&page={page}',
                true
            ),
        ]);

        $data['results'] = sprintf(
            $lang['text_pagination'] ?? 'Showing %d-%d of %d (%d pages)',
            $total ? ($page - 1) * $limit + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit)) ? $total : (($page - 1) * $limit + $limit),
            $total,
            ceil($total / $limit)
        );

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
