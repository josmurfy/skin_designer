<?php
namespace Opencart\Admin\Model\Shopmanager\Card;

class CardImportPrice extends \Opencart\System\Engine\Model {

    private bool $marketColumnsEnsured = false;

    public function getCardPrices(array $data = []): array {
        $this->ensureMarketColumns();

        $rawExpr = $this->getMarketJsonDecimalExpr('$.prices.raw');
        $grade9Expr = $this->getMarketJsonDecimalExpr('$.prices.grade_9');
        $grade10Expr = $this->getMarketJsonDecimalExpr('$.prices.grade_10');
        $checkedExpr = $this->getMarketJsonStringExpr('$.market.checked_at');

        $sql = 'SELECT * FROM ' . DB_PREFIX . 'card_price';

        $where = [];

        if (!empty($data['filter_title']))
            $where[] = "title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        if (!empty($data['filter_category']))
            $where[] = "category LIKE '%" . $this->db->escape($data['filter_category']) . "%'";
        if (!empty($data['filter_year']))
            $where[] = "year = '" . $this->db->escape($data['filter_year']) . "'";
        if (!empty($data['filter_brand'])) {
            $b_decoded = $this->db->escape(str_replace('%27', "'", $data['filter_brand']));
            $b_encoded = $this->db->escape(str_replace("'", '%27', $data['filter_brand']));
            $where[] = "(brand LIKE '%" . $b_decoded . "%' OR brand LIKE '%" . $b_encoded . "%')";
        }
        if (!empty($data['filter_set']))
            $where[] = "set_name LIKE '%" . $this->db->escape($data['filter_set']) . "%'";
        if (!empty($data['filter_player']))
            $where[] = "player LIKE '%" . $this->db->escape($data['filter_player']) . "%'";
        if (!empty($data['filter_card_number']))
            $where[] = "card_number LIKE '%" . $this->db->escape($data['filter_card_number']) . "%'";
        if (isset($data['filter_min_price']) && $data['filter_min_price'] !== '')
            $where[] = $rawExpr . " >= '" . (float)$data['filter_min_price'] . "'";
        if (isset($data['filter_max_price']) && $data['filter_max_price'] !== '')
            $where[] = $rawExpr . " <= '" . (float)$data['filter_max_price'] . "'";
        if (!empty($data['filter_has_sold']))
            $where[] = "card_number != '' AND EXISTS (SELECT 1 FROM " . DB_PREFIX . "card_price_sold s WHERE s.card_number = card_number)";

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $bestPriceExpr = "GREATEST(COALESCE({$rawExpr}, 0), COALESCE({$grade9Expr}, 0), COALESCE({$grade10Expr}, 0))";
        $valid_sorts = ['card_raw_id','title','category','year','brand','set_name','player','card_number','ungraded','grade_9','grade_10','date_added','ebay_market_checked_at','best_price'];
        $sort  = (isset($data['sort']) && in_array($data['sort'], $valid_sorts)) ? $data['sort'] : 'best_price';
        $order = (isset($data['order']) && strtoupper($data['order']) === 'ASC') ? 'ASC' : 'DESC';
        $sortMap = [
            'ungraded' => $rawExpr,
            'grade_9' => $grade9Expr,
            'grade_10' => $grade10Expr,
            'ebay_market_checked_at' => $checkedExpr,
            'best_price' => $bestPriceExpr,
        ];
        $sql  .= ' ORDER BY ' . ($sortMap[$sort] ?? $sort) . ' ' . $order;

        if (isset($data['start']) || isset($data['limit'])) {
            $start = max(0, (int)($data['start'] ?? 0));
            $limit = max(1, (int)($data['limit'] ?? 20));
            $sql  .= ' LIMIT ' . $start . ',' . $limit;
        }

        return $this->hydrateMarketDataRows($this->db->query($sql)->rows);
    }

    public function getTotalCardPrices(array $data = []): int {
        $this->ensureMarketColumns();

        $rawExpr = $this->getMarketJsonDecimalExpr('$.prices.raw');

        $sql = 'SELECT COUNT(*) AS total FROM ' . DB_PREFIX . 'card_price';

        $where = [];

        if (!empty($data['filter_title']))
            $where[] = "title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        if (!empty($data['filter_category']))
            $where[] = "category LIKE '%" . $this->db->escape($data['filter_category']) . "%'";
        if (!empty($data['filter_year']))
            $where[] = "year = '" . $this->db->escape($data['filter_year']) . "'";
        if (!empty($data['filter_brand'])) {
            $b_decoded = $this->db->escape(str_replace('%27', "'", $data['filter_brand']));
            $b_encoded = $this->db->escape(str_replace("'", '%27', $data['filter_brand']));
            $where[] = "(brand LIKE '%" . $b_decoded . "%' OR brand LIKE '%" . $b_encoded . "%')";
        }
        if (!empty($data['filter_set']))
            $where[] = "set_name LIKE '%" . $this->db->escape($data['filter_set']) . "%'";
        if (!empty($data['filter_player']))
            $where[] = "player LIKE '%" . $this->db->escape($data['filter_player']) . "%'";
        if (!empty($data['filter_card_number']))
            $where[] = "card_number LIKE '%" . $this->db->escape($data['filter_card_number']) . "%'";
        if (isset($data['filter_min_price']) && $data['filter_min_price'] !== '')
            $where[] = $rawExpr . " >= '" . (float)$data['filter_min_price'] . "'";
        if (isset($data['filter_max_price']) && $data['filter_max_price'] !== '')
            $where[] = $rawExpr . " <= '" . (float)$data['filter_max_price'] . "'";
        if (!empty($data['filter_has_sold']))
            $where[] = "card_number != '' AND EXISTS (SELECT 1 FROM " . DB_PREFIX . "card_price_sold s WHERE s.card_number = card_number)";

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        return (int)($this->db->query($sql)->row['total'] ?? 0);
    }

    /**
     * Return distinct values for a filter column — used by autocomplete
     */
    public function getDistinctField(string $field, string $term = '', int $limit = 10): array {
        $allowed = ['title','category','year','brand','set_name','player','team','variation','card_number'];
        if (!in_array($field, $allowed)) return [];
        $sql = 'SELECT DISTINCT ' . $field . ' AS value
                FROM ' . DB_PREFIX . 'card_price
                WHERE ' . $field . " != '' AND " . $field . ' IS NOT NULL';
        if ($term !== '')
            $sql .= " AND " . $field . " LIKE '%" . $this->db->escape($term) . "%'";
        $sql .= ' ORDER BY ' . $field . ' ASC LIMIT ' . (int)$limit;
        return array_column($this->db->query($sql)->rows, 'value');
    }

    /**
     * Check if a single CSV row already exists in DB by set_name + category + year + brand.
     * CSV uses 'set' key; DB column is 'set_name'.
     */
    public function checkCardExistsBySampleRow(array $row): bool {
        $mapping = [
            'set_name' => $row['set']      ?? $row['set_name'] ?? '',
            'category' => $row['category'] ?? '',
            'year'     => $row['year']     ?? '',
            'brand'    => $row['brand']    ?? '',
        ];
        $conditions = [];
        foreach ($mapping as $col => $val) {
            if ($val !== '' && $val !== null) {
                $conditions[] = $col . " = '" . $this->db->escape($val) . "'";
            }
        }
        if (empty($conditions)) return false;
        $sql = 'SELECT COUNT(*) AS cnt FROM ' . DB_PREFIX . 'card_price WHERE ' . implode(' AND ', $conditions);
        return (int)($this->db->query($sql)->row['cnt'] ?? 0) > 0;
    }

    /**
     * Get DB records matching the brand/year/category context found in the CSV rows.
     * Used to show user what is already in the DB when duplicate is detected.
     */
    public function getCardsByContext(array $csv_cards, int $limit = 500): array {
        $combos = [];
        foreach ($csv_cards as $card) {
            $brand    = $card['brand']    ?? '';
            $year     = $card['year']     ?? '';
            $category = $card['category'] ?? '';
            $key = $brand . '|' . $year . '|' . $category;
            if (!isset($combos[$key])) {
                $combos[$key] = compact('brand', 'year', 'category');
            }
        }
        $ors = [];
        foreach (array_values($combos) as $c) {
            $parts = [];
            if ($c['brand']    !== '') $parts[] = "brand    = '" . $this->db->escape($c['brand'])    . "'";
            if ($c['year']     !== '') $parts[] = "year     = '" . $this->db->escape($c['year'])     . "'";
            if ($c['category'] !== '') $parts[] = "category = '" . $this->db->escape($c['category']) . "'";
            if ($parts) $ors[] = '(' . implode(' AND ', $parts) . ')';
        }
        if (empty($ors)) return [];
        $sql = 'SELECT * FROM ' . DB_PREFIX . 'card_price WHERE ' . implode(' OR ', $ors)
                 . ' ORDER BY brand, year, set_name, player LIMIT ' . (int)$limit;
        return $this->hydrateMarketDataRows($this->db->query($sql)->rows);
    }

    /**
     * Get a single raw card record
     */
    public function getCardPrice(int $card_raw_id): array {
        $this->ensureMarketColumns();

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "card_price WHERE card_raw_id = '" . (int)$card_raw_id . "'");
        return $this->hydrateMarketDataRow($query->row ?? []);
    }

    public function getCardsByIds(array $ids): array {
        $this->ensureMarketColumns();

        if (empty($ids)) {
            return [];
        }

        $safe_ids = array_map('intval', $ids);
        $safe_ids = array_values(array_filter($safe_ids, static function(int $id): bool {
            return $id > 0;
        }));

        if (empty($safe_ids)) {
            return [];
        }

        $sql = "SELECT * FROM " . DB_PREFIX . "card_price WHERE card_raw_id IN (" . implode(',', $safe_ids) . ")";
        return $this->hydrateMarketDataRows($this->db->query($sql)->rows);
    }

    public function isMarketDataFresh(int $card_raw_id, int $days = 30): bool {
        $this->ensureMarketColumns();

        $days = max(1, $days);
        $sql = "SELECT JSON_UNQUOTE(JSON_EXTRACT(market_data_json, '$.market.checked_at')) AS checked_at
                FROM " . DB_PREFIX . "card_price WHERE card_raw_id = '" . (int)$card_raw_id . "' LIMIT 1";
        $row = $this->db->query($sql)->row;

        $checkedAtStr = $row['checked_at'] ?? '';
        if (empty($checkedAtStr) || $checkedAtStr === 'null') {
            return false;
        }

        $checkedAt = strtotime((string)$checkedAtStr);
        if (!$checkedAt) {
            return false;
        }

        return $checkedAt >= strtotime('-' . $days . ' days');
    }

    public function updateMarketPrices(int $card_raw_id, array $data): void {
        $this->ensureMarketColumns();

        $current = $this->getCardPrice($card_raw_id);
        $marketDataJson = "'" . $this->db->escape($this->encodeMarketDataPayload(array_merge($current, $data))) . "'";

        $this->db->query("UPDATE " . DB_PREFIX . "card_price SET
            market_data_json = " . $marketDataJson . ",
            date_modified    = NOW()
            WHERE card_raw_id = '" . (int)$card_raw_id . "'");
    }

    /**
     * Add a single raw card record
     */
    public function addCardPrice(array $data): int {
        $this->ensureMarketColumns();

        $marketDataJson = "'" . $this->db->escape($this->encodeMarketDataPayload($data)) . "'";

        $this->db->query("INSERT INTO " . DB_PREFIX . "card_price SET
            title         = '" . $this->db->escape($data['title'] ?? '') . "',
            category      = '" . $this->db->escape($data['category'] ?? '') . "',
            year          = '" . $this->db->escape($data['year'] ?? '') . "',
            brand         = '" . $this->db->escape($data['brand'] ?? '') . "',
            set_name      = '" . $this->db->escape($data['set'] ?? '') . "',
            subset        = '" . $this->db->escape($data['subset'] ?? '') . "',
            player        = '" . $this->db->escape($data['player'] ?? '') . "',
            card_number   = '" . $this->db->escape($data['card_number'] ?? '') . "',
            attributes    = '" . $this->db->escape($data['attributes'] ?? '') . "',
            team          = '" . $this->db->escape($data['team'] ?? '') . "',
            variation     = '" . $this->db->escape($data['variation'] ?? '') . "',
            market_data_json = " . $marketDataJson . ",
            front_image   = '" . $this->db->escape($data['front_image'] ?? '') . "',
            status        = '1',
            date_added    = NOW(),
            date_modified = NOW()");

        return (int)$this->db->getLastId();
    }

    /**
     * Insert multiple records at once
     * Skip rule: all prices = 0 (no value in DB)
     * Returns ['inserted' => N, 'skipped' => N]
     */
    public function addCardPriceBatch(array $cards): array {
        $inserted = 0;
        $skipped  = 0;

        foreach ($cards as $card) {
            // Skip empty title
            if (empty($card['title'])) {
                $skipped++;
                continue;
            }

            // Skip if all prices are zero and ebay_sales is empty — no useful data
            if (!$this->hasImportableValue($card)) {
                $skipped++;
                continue;
            }

            $id = $this->addCardPrice($card);
            if ($id > 0) {
                $inserted++;
            } else {
                $skipped++;
            }
        }

        return ['inserted' => $inserted, 'skipped' => $skipped];
    }

    /**
     * Find duplicate records in the DB.
     * Duplicates = same (title + year + brand + set_name + card_number + player) with multiple rows.
     * Returns all rows belonging to duplicate groups, each row has:
     *   - all card_price columns
     *   - 'is_keeper' (bool) — the record with MAX(card_raw_id) in its group → keep this one
     *   - 'group_count' — how many records share the same key
     */
    public function findDbDuplicates(): array {
        $sql = "SELECT cp.*, grp.keep_id, grp.group_count,
                       IF(cp.card_raw_id = grp.keep_id, 1, 0) AS is_keeper
                FROM " . DB_PREFIX . "card_price cp
                JOIN (
                    SELECT title, category, year, brand, set_name, card_number, player,
                           ungraded, grade_9, grade_10,
                           COUNT(*) AS group_count,
                           MAX(card_raw_id) AS keep_id
                    FROM " . DB_PREFIX . "card_price
                    GROUP BY title, category, year, brand, set_name, card_number, player,
                             ungraded, grade_9, grade_10
                    HAVING COUNT(*) > 1
                ) grp ON (
                    cp.title       = grp.title       AND
                    cp.category    = grp.category    AND
                    cp.year        = grp.year        AND
                    cp.brand       = grp.brand       AND
                    cp.set_name    = grp.set_name    AND
                    cp.card_number = grp.card_number AND
                    cp.player      = grp.player      AND
                    cp.ungraded    = grp.ungraded    AND
                    cp.grade_9     = grp.grade_9     AND
                    cp.grade_10    = grp.grade_10
                )
                ORDER BY cp.brand, cp.year, cp.category, cp.set_name, cp.player, cp.title, cp.card_raw_id";
        return $this->hydrateMarketDataRows($this->db->query($sql)->rows);
    }

    /**
     * Delete a single record
     */
    public function deleteCardPrice(int $card_raw_id): void {
        $this->db->query("DELETE FROM " . DB_PREFIX . "card_price WHERE card_raw_id = '" . (int)$card_raw_id . "'");
    }

    /**
     * Delete multiple records by array of IDs
     */
    public function deleteCardPrices(array $ids): int {
        if (empty($ids)) {
            return 0;
        }
        $safe_ids = implode(',', array_map('intval', $ids));
        $this->db->query("DELETE FROM " . DB_PREFIX . "card_price WHERE card_raw_id IN (" . $safe_ids . ")");
        return count($ids);
    }

    /**
     * Truncate entire table
     */
    public function truncateCardPrices(): void {
        $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "card_price");
    }

    /**
     * Parse CSV file — auto-detects tab/comma/semicolon separator
     * Returns ['data' => [...], 'error' => '...']
     */
    public function parseCSV(string $filepath): array {
        if (!file_exists($filepath) || !is_readable($filepath)) {
            return ['data' => [], 'error' => 'File not found or not readable: ' . $filepath];
        }

        $content = file_get_contents($filepath);
        if ($content === false || trim($content) === '') {
            return ['data' => [], 'error' => 'File is empty or unreadable'];
        }

        // Remove UTF-8 BOM if present
        $content = ltrim($content, "\xEF\xBB\xBF");

        $lines = preg_split('/\r\n|\r|\n/', $content);
        $lines = array_filter($lines, fn($l) => trim($l) !== '');
        $lines = array_values($lines);

        if (empty($lines)) {
            return ['data' => [], 'error' => 'CSV file is empty'];
        }

        $expected = ['title', 'category', 'year', 'brand', 'set', 'subset', 'player',
                     'card_number', 'attributes', 'team', 'variation',
                     'ungraded', 'grade_9', 'grade_10', 'front_image', 'url',
                     'ebay_sales', 'ebay_low_price', 'ebay_low_grader', 'ebay_low_grade', 'ebay_low_type'];        $firstLine = (string)($lines[0] ?? '');
        $delimiterCandidates = $this->getCsvDelimiterCandidates($firstLine);
        $bestError = 'CSV header not recognized. Expected at least a title column.';

        foreach ($delimiterCandidates as $separator) {
            $parsed = $this->parseCsvWithSeparator($lines, $separator, $expected);

            if (!empty($parsed['data'])) {
                return $parsed;
            }

            if (!empty($parsed['error'])) {
                $bestError = $parsed['error'];
            }
        }

        return ['data' => [], 'error' => $bestError];
    }

    private function getCsvDelimiterCandidates(string $firstLine): array {
        $scores = [
            ";" => substr_count($firstLine, ';'),
            "\t" => substr_count($firstLine, "\t"),
            "," => substr_count($firstLine, ','),
        ];

        arsort($scores);

        $candidates = array_keys($scores);
        return array_values(array_unique(array_merge($candidates, [';', "\t", ','])));
    }

    private function parseCsvWithSeparator(array $lines, string $separator, array $expected): array {
        $workingLines = $lines;
        $headerLine = array_shift($workingLines);

        if ($headerLine === null) {
            return ['data' => [], 'error' => 'CSV file is empty'];
        }

        $header = str_getcsv((string)$headerLine, $separator);
        $header = array_map(static function($value): string {
            return strtolower(trim((string)$value));
        }, $header);

        if (empty($header)) {
            return ['data' => [], 'error' => 'Unable to read CSV header'];
        }

        $col_map = [];
        foreach ($expected as $col) {
            $idx = array_search($col, $header, true);
            $col_map[$col] = ($idx !== false) ? $idx : -1;
        }

        if (($col_map['title'] ?? -1) < 0) {
            return ['data' => [], 'error' => 'CSV header not recognized with separator ' . $this->getReadableSeparatorName($separator) . '. Missing title column.'];
        }

        $cards = [];
        foreach ($workingLines as $lineNumber => $line) {
            $row = str_getcsv((string)$line, $separator);

            if (count($row) <= 1 && $separator !== ',' && strpos((string)$line, ',') !== false) {
                continue;
            }

            $card = [];
            foreach ($expected as $col) {
                $idx = $col_map[$col];
                $card[$col] = ($idx >= 0 && isset($row[$idx])) ? trim((string)$row[$idx]) : '';
            }

            $card['ungraded'] = $this->normalizeMoneyValue((string)($card['ungraded'] ?? ''));
            $card['grade_9'] = $this->normalizeMoneyValue((string)($card['grade_9'] ?? ''));
            $card['grade_10'] = $this->normalizeMoneyValue((string)($card['grade_10'] ?? ''));
            $card['ebay_low_price'] = $this->normalizeMoneyValue((string)($card['ebay_low_price'] ?? ''));

            $card['ebay_sales'] = $this->parseEbaySalesValue((string)($card['ebay_sales'] ?? ''));
            if (empty($card['ebay_sales'])) {
                $lowEntry = $this->buildEbaySalesEntryFromLowColumns($card);
                if ($lowEntry !== null) {
                    $card['ebay_sales'] = [$lowEntry];
                }
            }

            if (empty($card['ebay_price_sold_graded']) && !empty($card['ebay_low_price'])) {
                $card['ebay_price_sold_graded'] = $card['ebay_low_price'];
                $card['ebay_price_sold_graded_grade'] = trim(((string)($card['ebay_low_grader'] ?? '')) . ' ' . ((string)($card['ebay_low_grade'] ?? '')));
                $lowType = strtoupper(trim((string)($card['ebay_low_type'] ?? '')));
                if (preg_match('/AUC(?:\((\d+)\))?/i', $lowType, $mBid)) {
                    $card['ebay_price_sold_graded_bids'] = isset($mBid[1]) ? (int)$mBid[1] : 0;
                }
            }

            if (empty($card['title'])) {
                continue;
            }

            $cards[] = $card;
        }

        if (empty($cards)) {
            return ['data' => [], 'error' => 'No valid CSV rows found with separator ' . $this->getReadableSeparatorName($separator) . '.'];
        }

        return ['data' => $cards, 'error' => ''];
    }

    private function getReadableSeparatorName(string $separator): string {
        if ($separator === ';') {
            return 'semicolon (;)';
        }

        if ($separator === "\t") {
            return 'tab';
        }

        return 'comma (,)';
    }

    /**
     * Normalize a player name for comparison: strip # and : then collapse spaces.
     * e.g. "Checklist #1: 1-114" → "Checklist 1 1-114"
     */
    private function normalizePlayerName(string $name): string {
        $name = str_replace(['#', ':'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name);
        return trim($name);
    }

    /**
     * Lookup ungraded price from oc_card_price by card_number + player [+ brand]
     * Returns 0.0 when no match found.
     */
    public function getPriceByCard(string $card_number, string $player, string $brand = ''): float {
        $player_norm = $this->db->escape($this->normalizePlayerName($player));
        $rawExpr = $this->getMarketJsonDecimalExpr('$.prices.raw');
        $sql = "SELECT " . $rawExpr . " AS ungraded FROM " . DB_PREFIX . "card_price
                WHERE card_number = '" . $this->db->escape(trim($card_number)) . "'
                AND   TRIM(REPLACE(REPLACE(player, '#', ' '), ':', ' ')) = '" . $player_norm . "'";
        if ($brand !== '') {
            $sql .= " AND brand = '" . $this->db->escape(trim($brand)) . "'";
        }
        $sql .= " AND " . $rawExpr . " > 0 ORDER BY date_added DESC LIMIT 1";
        $query = $this->db->query($sql);
        return isset($query->row['ungraded']) ? (float)$query->row['ungraded'] : 0.0;
    }

    private function getMarketJsonDecimalExpr(string $jsonPath): string {
        return "CAST(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(market_data_json, '{}'), '" . $this->db->escape($jsonPath) . "')) AS DECIMAL(15,2))";
    }

    private function getMarketJsonStringExpr(string $jsonPath): string {
        return "JSON_UNQUOTE(JSON_EXTRACT(COALESCE(market_data_json, '{}'), '" . $this->db->escape($jsonPath) . "'))";
    }

    private function hydrateMarketDataRows(array $rows): array {
        foreach ($rows as $i => $row) {
            $rows[$i] = $this->hydrateMarketDataRow($row);
        }

        return $rows;
    }

    private function hydrateMarketDataRow(array $row): array {
        if (empty($row)) {
            return $row;
        }

        $marketData = $this->decodeMarketDataPayload($row['market_data_json'] ?? '');

        if (!empty($marketData['prices']) && is_array($marketData['prices'])) {
            $row['ungraded'] = $marketData['prices']['raw'] ?? ($row['ungraded'] ?? 0);
            $row['grade_9'] = $marketData['prices']['grade_9'] ?? ($row['grade_9'] ?? 0);
            $row['grade_10'] = $marketData['prices']['grade_10'] ?? ($row['grade_10'] ?? 0);
        }

        if (!empty($marketData['market']) && is_array($marketData['market'])) {
            $row['ebay_price_sold_raw'] = $marketData['market']['auction_raw']['price'] ?? ($row['ebay_price_sold_raw'] ?? null);
            $row['ebay_price_sold_raw_url'] = $marketData['market']['auction_raw']['url'] ?? ($row['ebay_price_sold_raw_url'] ?? '');
            $row['ebay_price_sold_raw_bids'] = $marketData['market']['auction_raw']['bids'] ?? ($row['ebay_price_sold_raw_bids'] ?? null);

            $row['ebay_price_sold_graded'] = $marketData['market']['auction_graded']['price'] ?? ($row['ebay_price_sold_graded'] ?? null);
            $row['ebay_price_sold_graded_url'] = $marketData['market']['auction_graded']['url'] ?? ($row['ebay_price_sold_graded_url'] ?? '');
            $row['ebay_price_sold_graded_bids'] = $marketData['market']['auction_graded']['bids'] ?? ($row['ebay_price_sold_graded_bids'] ?? null);
            $row['ebay_price_sold_graded_grade'] = $marketData['market']['auction_graded']['grade'] ?? ($row['ebay_price_sold_graded_grade'] ?? '');

            $row['ebay_price_list_raw'] = $marketData['market']['buy_now_raw']['price'] ?? ($row['ebay_price_list_raw'] ?? null);
            $row['ebay_price_list_raw_url'] = $marketData['market']['buy_now_raw']['url'] ?? ($row['ebay_price_list_raw_url'] ?? '');

            $row['ebay_price_list_graded'] = $marketData['market']['buy_now_graded']['price'] ?? ($row['ebay_price_list_graded'] ?? null);
            $row['ebay_price_list_graded_url'] = $marketData['market']['buy_now_graded']['url'] ?? ($row['ebay_price_list_graded_url'] ?? '');
            $row['ebay_price_list_graded_grade'] = $marketData['market']['buy_now_graded']['grade'] ?? ($row['ebay_price_list_graded_grade'] ?? '');

            $row['ebay_market_checked_at'] = $marketData['market']['checked_at'] ?? ($row['ebay_market_checked_at'] ?? null);
        }

        if (array_key_exists('ebay_sales', $marketData)) {
            $row['ebay_sales'] = $marketData['ebay_sales'];
        }

        return $row;
    }

    /**
     * Return ALL matching price rows for a card (no LIMIT), with 3-tier fallback.
     *
     * Tier 1 — exact normalized player match (strips # and :)
     * Tier 2 — DB player is a substring of CSV player
     *           e.g. CSV "Checklist (Bryant Stith / LaPhonso Ellis)" ↔ DB "Bryant Stith / LaPhonso Ellis"
     * Tier 3 — brand + card_number + LEFT(year,4), no player (last resort)
     *           e.g. DB "Gill / Johnson" vs CSV "Kendall Gill / Larry Johnson"
     */
    public function getPriceRowsByCard(string $card_number, string $player, string $brand = '', string $year = '', string $category = ''): array {
        $this->ensureMarketColumns();

        $num_esc      = $this->db->escape(trim($card_number));
        $brand_esc    = $this->db->escape(trim($brand));
        $category_esc = $this->db->escape(trim($category));
        $player_norm  = $this->db->escape($this->normalizePlayerName($player));
        $year4        = $this->db->escape(substr(trim($year), 0, 4)); // "1992-93" → "1992"
        $rawExpr      = $this->getMarketJsonDecimalExpr('$.prices.raw');

        $select = "SELECT card_raw_id, set_name, year, brand, category, subset, market_data_json,
                  " . $rawExpr . " AS ungraded
                   FROM " . DB_PREFIX . "card_price
                   WHERE card_number = '$num_esc'";
        $brand_clause = $brand_esc !== '' ? " AND brand = '$brand_esc'" : '';
        $category_clause = $category_esc !== '' ? " AND category = '$category_esc'" : '';
        $suffix = " AND (" . $rawExpr . " > 0 OR (market_data_json IS NOT NULL AND market_data_json != '' AND market_data_json != '{}')) ORDER BY date_added DESC";
        $norm_expr = "TRIM(REPLACE(REPLACE(player, '#', ' '), ':', ' '))";

        // Tier 1: exact normalized player
        $rows = $this->db->query(
            $select . " AND $norm_expr = '$player_norm'" . $brand_clause . $category_clause . $suffix
        )->rows;
        if ($rows) return $this->hydrateMarketDataRows($rows);

        // Tier 2: DB player is a substring of CSV player
        if ($player_norm !== '') {
            $rows = $this->db->query(
                $select . " AND '$player_norm' LIKE CONCAT('%', $norm_expr, '%')" . $brand_clause . $category_clause . $suffix
            )->rows;
            if ($rows) return $this->hydrateMarketDataRows($rows);
        }

        // Tier 3: card_number + brand + year (no player)
        if ($brand_esc !== '' && $year4 !== '') {
            $rows = $this->db->query(
                $select . $brand_clause . $category_clause . " AND LEFT(year, 4) = '$year4'" . $suffix
            )->rows;
            if ($rows) return $this->hydrateMarketDataRows($rows);
        }

        return [];
    }

    private function ensureMarketColumns(): void {
        if ($this->marketColumnsEnsured) {
            return;
        }

        $table = DB_PREFIX . 'card_price';

        $columns = [
            'market_data_json' => "ALTER TABLE `" . $table . "` ADD COLUMN `market_data_json` MEDIUMTEXT NULL AFTER `grade_10`",
        ];

        foreach ($columns as $column => $alterSql) {
            if (!$this->columnExists($table, $column)) {
                $this->db->query($alterSql);
            }
        }

        $this->marketColumnsEnsured = true;
    }

    private function columnExists(string $table, string $column): bool {
        $result = $this->db->query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $this->db->escape($column) . "'");
        return !empty($result->num_rows);
    }

    private function hasImportableValue(array $card): bool {
        $ungraded = (float)($card['ungraded'] ?? 0);
        $grade_9 = (float)($card['grade_9'] ?? 0);
        $grade_10 = (float)($card['grade_10'] ?? 0);

        if ($ungraded > 0 || $grade_9 > 0 || $grade_10 > 0) {
            return true;
        }

        $sales = $card['ebay_sales'] ?? [];

        if (is_array($sales)) {
            return !empty($sales);
        }

        $sales = trim((string)$sales);
        return $sales !== '' && $sales !== '[]';
    }

    private function normalizeEbaySalesForStorage($value): string {
        if (is_string($value)) {
            $trimmed = trim($value);

            if ($trimmed === '') {
                return '[]';
            }

            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }

            return json_encode($this->parseEbaySalesValue($trimmed), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        if (is_array($value)) {
            return json_encode(array_values($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return '[]';
    }

    public function getLowestImporterUsdPriceFromRow(array $row): float {
        $candidates = [];

        $ungraded = (float)($row['ungraded'] ?? 0);
        if ($ungraded > 0) {
            $candidates[] = $ungraded;
        }

        $marketData = $this->decodeMarketDataPayload($row['market_data_json'] ?? '');
        $auctionRawPrice = (float)($marketData['market']['auction_raw']['price'] ?? ($row['ebay_price_sold_raw'] ?? 0));
        $auctionRawBids = (int)($marketData['market']['auction_raw']['bids'] ?? ($row['ebay_price_sold_raw_bids'] ?? 0));

        if ($auctionRawPrice > 0 && $auctionRawBids > 0) {
            $candidates[] = $auctionRawPrice;
        }

        $salesEntries = !empty($marketData['ebay_sales']) && is_array($marketData['ebay_sales'])
            ? $marketData['ebay_sales']
            : $this->decodeStoredEbaySales($row['ebay_sales'] ?? '');

        foreach ($salesEntries as $entry) {
            $price = (float)($entry['price'] ?? 0);
            $gradeNumeric = $entry['grade_numeric'] ?? null;
            $condition = strtolower(trim((string)($entry['condition'] ?? '')));
            $isRawLike = $gradeNumeric === null || $gradeNumeric === '' || str_contains($condition, 'raw') || str_contains($condition, 'ungraded');

            if ($isRawLike && $price > 0) {
                $candidates[] = $price;
            }
        }

        return $candidates ? round(min($candidates), 2) : 0.0;
    }

    private function encodeMarketDataPayload(array $data): string {
        return json_encode($this->buildMarketDataPayload($data), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function buildMarketDataPayload(array $data): array {
        return [
            'prices' => [
                'raw' => isset($data['ungraded']) && $data['ungraded'] !== '' ? round((float)$data['ungraded'], 2) : null,
                'grade_9' => isset($data['grade_9']) && $data['grade_9'] !== '' ? round((float)$data['grade_9'], 2) : null,
                'grade_10' => isset($data['grade_10']) && $data['grade_10'] !== '' ? round((float)$data['grade_10'], 2) : null,
            ],
            'market' => [
                'auction_raw' => [
                    'price' => isset($data['ebay_price_sold_raw']) && $data['ebay_price_sold_raw'] !== '' ? round((float)$data['ebay_price_sold_raw'], 2) : null,
                    'url' => (string)($data['ebay_price_sold_raw_url'] ?? ''),
                    'bids' => isset($data['ebay_price_sold_raw_bids']) && $data['ebay_price_sold_raw_bids'] !== '' ? (int)$data['ebay_price_sold_raw_bids'] : null,
                ],
                'auction_graded' => [
                    'price' => isset($data['ebay_price_sold_graded']) && $data['ebay_price_sold_graded'] !== '' ? round((float)$data['ebay_price_sold_graded'], 2) : null,
                    'url' => (string)($data['ebay_price_sold_graded_url'] ?? ''),
                    'bids' => isset($data['ebay_price_sold_graded_bids']) && $data['ebay_price_sold_graded_bids'] !== '' ? (int)$data['ebay_price_sold_graded_bids'] : null,
                    'grade' => (string)($data['ebay_price_sold_graded_grade'] ?? ''),
                ],
                'buy_now_raw' => [
                    'price' => isset($data['ebay_price_list_raw']) && $data['ebay_price_list_raw'] !== '' ? round((float)$data['ebay_price_list_raw'], 2) : null,
                    'url' => (string)($data['ebay_price_list_raw_url'] ?? ''),
                ],
                'buy_now_graded' => [
                    'price' => isset($data['ebay_price_list_graded']) && $data['ebay_price_list_graded'] !== '' ? round((float)$data['ebay_price_list_graded'], 2) : null,
                    'url' => (string)($data['ebay_price_list_graded_url'] ?? ''),
                    'grade' => (string)($data['ebay_price_list_graded_grade'] ?? ''),
                ],
                'checked_at' => !empty($data['ebay_market_checked_at']) && $data['ebay_market_checked_at'] !== '—' ? (string)$data['ebay_market_checked_at'] : null,
            ],
            'ebay_sales' => $this->decodeStoredEbaySales($data['ebay_sales'] ?? []),
        ];
    }

    private function decodeMarketDataPayload($value): array {
        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    private function decodeStoredEbaySales($value): array {
        if (is_array($value)) {
            return array_values($value);
        }

        $trimmed = trim((string)$value);
        if ($trimmed === '' || $trimmed === '[]') {
            return [];
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        return $this->parseEbaySalesValue($trimmed);
    }

    private function parseEbaySalesValue(string $value): array {
        $value = trim($value);

        if ($value === '') {
            return [];
        }

        $entries = preg_split('/\s*\|\s*/', $value);
        $parsed = [];

        foreach ($entries as $entry) {
            $item = $this->parseEbaySalesEntry((string)$entry);

            if ($item !== null) {
                $parsed[] = $item;
            }
        }

        usort($parsed, function(array $a, array $b): int {
            $gradeA = isset($a['grade_numeric']) && $a['grade_numeric'] !== null ? (float)$a['grade_numeric'] : -1.0;
            $gradeB = isset($b['grade_numeric']) && $b['grade_numeric'] !== null ? (float)$b['grade_numeric'] : -1.0;

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

        return array_values($parsed);
    }

    private function parseEbaySalesEntry(string $entry): ?array {
        $entry = trim(preg_replace('/\s+/', ' ', $entry));

        if ($entry === '') {
            return null;
        }

        if (!preg_match('/\$\s*([0-9]+(?:\.[0-9]+)?)/', $entry, $priceMatch)) {
            return null;
        }

        $saleType = 'BIN';
        $bids = 0;

        if (preg_match('/\bAUC(?:\((\d+)\))?/i', $entry, $saleMatch)) {
            $saleType = 'AUC';
            $bids = isset($saleMatch[1]) && $saleMatch[1] !== '' ? (int)$saleMatch[1] : 0;
        } elseif (preg_match('/\bBIN\b/i', $entry)) {
            $saleType = 'BIN';
        }

        $condition = preg_replace('/\s+(?:BIN|AUC(?:\(\d+\))?)\s+\$\s*[0-9]+(?:\.[0-9]+)?\s*$/i', '', $entry);
        $condition = trim((string)$condition);

        if ($condition === '') {
            $condition = 'Ungraded';
        }

        $grader = '';
        if (preg_match('/^([A-Za-z0-9+\-]+)/', $condition, $graderMatch)) {
            $grader = strtoupper((string)$graderMatch[1]);
        }

        $gradeNumeric = null;
        if (preg_match('/(\d+(?:\.\d+)?)\s*$/', $condition, $gradeMatch)) {
            $gradeNumeric = (float)$gradeMatch[1];
        }

        return [
            'raw' => $entry,
            'grader' => $grader,
            'condition' => $condition,
            'grade_numeric' => $gradeNumeric,
            'sale_type' => $saleType,
            'bids' => $saleType === 'AUC' ? $bids : 0,
            'price' => round((float)$priceMatch[1], 2),
            'currency' => 'CAD'
        ];
    }

    private function normalizeMoneyValue(string $value): string {
        $value = trim($value);
        if ($value === '') {
            return '0';
        }

        $value = str_replace(['$', 'CAD', 'cad', ' '], '', $value);
        $value = str_replace(',', '', $value);

        if (!is_numeric($value)) {
            return '0';
        }

        return number_format((float)$value, 2, '.', '');
    }

    private function buildEbaySalesEntryFromLowColumns(array $card): ?array {
        $price = (float)($card['ebay_low_price'] ?? 0);
        if ($price <= 0) {
            return null;
        }

        $grader = strtoupper(trim((string)($card['ebay_low_grader'] ?? '')));
        $gradeRaw = trim((string)($card['ebay_low_grade'] ?? ''));
        $condition = trim($grader . ' ' . $gradeRaw);
        if ($condition === '') {
            $condition = 'Ungraded';
        }

        $gradeNumeric = null;
        if ($gradeRaw !== '' && is_numeric($gradeRaw)) {
            $gradeNumeric = (float)$gradeRaw;
        }

        $saleTypeRaw = strtoupper(trim((string)($card['ebay_low_type'] ?? 'BIN')));
        $saleType = 'BIN';
        $bids = 0;
        if (preg_match('/AUC(?:\((\d+)\))?/i', $saleTypeRaw, $m)) {
            $saleType = 'AUC';
            $bids = isset($m[1]) ? (int)$m[1] : 0;
        }

        $raw = $condition . ' ' . $saleType;
        if ($saleType === 'AUC') {
            $raw .= '(' . str_pad((string)$bids, 3, '0', STR_PAD_LEFT) . ')';
        }
        $raw .= ' $' . number_format($price, 2, '.', '');

        return [
            'raw' => $raw,
            'grader' => $grader,
            'condition' => $condition,
            'grade_numeric' => $gradeNumeric,
            'sale_type' => $saleType,
            'bids' => $saleType === 'AUC' ? $bids : 0,
            'price' => round($price, 2),
            'currency' => 'CAD'
        ];
    }
}
