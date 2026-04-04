<?php
namespace Opencart\Admin\Model\Shopmanager\Card\Import;

class CardSetImporter extends \Opencart\System\Engine\Model {

    public function getCardPrices(array $data = []): array {
        $sql = 'SELECT * FROM ' . DB_PREFIX . 'card_set';

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
            $where[] = "ungraded >= '" . (float)$data['filter_min_price'] . "'";
        if (isset($data['filter_max_price']) && $data['filter_max_price'] !== '')
            $where[] = "ungraded <= '" . (float)$data['filter_max_price'] . "'";
        if (!empty($data['filter_has_sold']))
            $where[] = "card_number != '' AND EXISTS (SELECT 1 FROM " . DB_PREFIX . "card_price_sold s WHERE s.card_number = card_number)";

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $bestPriceExpr = "GREATEST(COALESCE(ungraded, 0), COALESCE(grade_9, 0), COALESCE(grade_10, 0))";
        $valid_sorts = ['card_raw_id','title','category','year','brand','set_name','player','card_number','ungraded','grade_9','grade_10','date_added','best_price'];
        $sort  = (isset($data['sort']) && in_array($data['sort'], $valid_sorts)) ? $data['sort'] : 'best_price';
        $order = (isset($data['order']) && strtoupper($data['order']) === 'ASC') ? 'ASC' : 'DESC';
        $sortMap = [
            'best_price' => $bestPriceExpr,
        ];
        $sql  .= ' ORDER BY ' . ($sortMap[$sort] ?? $sort) . ' ' . $order;

        if (isset($data['start']) || isset($data['limit'])) {
            $start = max(0, (int)($data['start'] ?? 0));
            $limit = max(1, (int)($data['limit'] ?? 20));
            $sql  .= ' LIMIT ' . $start . ',' . $limit;
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalCardPrices(array $data = []): int {
        $sql = 'SELECT COUNT(*) AS total FROM ' . DB_PREFIX . 'card_set';

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
            $where[] = "ungraded >= '" . (float)$data['filter_min_price'] . "'";
        if (isset($data['filter_max_price']) && $data['filter_max_price'] !== '')
            $where[] = "ungraded <= '" . (float)$data['filter_max_price'] . "'";
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
                FROM ' . DB_PREFIX . 'card_set
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
        $sql = 'SELECT COUNT(*) AS cnt FROM ' . DB_PREFIX . 'card_set WHERE ' . implode(' AND ', $conditions);
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
        $sql = 'SELECT * FROM ' . DB_PREFIX . 'card_set WHERE ' . implode(' OR ', $ors)
                 . ' ORDER BY brand, year, set_name, player LIMIT ' . (int)$limit;
        return $this->db->query($sql)->rows;
    }

    /**
     * Get a single raw card record
     */
    public function getCardPrice(int $card_raw_id): array {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "card_set WHERE card_raw_id = '" . (int)$card_raw_id . "'");
        return $query->row ?? [];
    }

    public function getCardsByIds(array $ids): array {
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

        $sql = "SELECT * FROM " . DB_PREFIX . "card_set WHERE card_raw_id IN (" . implode(',', $safe_ids) . ")";
        return $this->db->query($sql)->rows;
    }

    /**
     * Add a single raw card record
     */
    public function addCardPrice(array $data): int {
        $this->db->query("INSERT INTO " . DB_PREFIX . "card_set SET
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
            ungraded      = '" . (float)($data['ungraded'] ?? 0) . "',
            grade_9       = '" . (float)($data['grade_9'] ?? 0) . "',
            grade_10      = '" . (float)($data['grade_10'] ?? 0) . "',
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
                FROM " . DB_PREFIX . "card_set cp
                JOIN (
                    SELECT title, category, year, brand, set_name, card_number, player,
                           ungraded, grade_9, grade_10,
                           COUNT(*) AS group_count,
                           MAX(card_raw_id) AS keep_id
                    FROM " . DB_PREFIX . "card_set
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
        return $this->db->query($sql)->rows;
    }

    /**
     * Delete a single record
     */
    public function deleteCardPrice(int $card_raw_id): void {
        $this->db->query("DELETE FROM " . DB_PREFIX . "card_set WHERE card_raw_id = '" . (int)$card_raw_id . "'");
    }

    /**
     * Delete multiple records by array of IDs
     */
    public function deleteCardPrices(array $ids): int {
        if (empty($ids)) {
            return 0;
        }
        $safe_ids = implode(',', array_map('intval', $ids));
        $this->db->query("DELETE FROM " . DB_PREFIX . "card_set WHERE card_raw_id IN (" . $safe_ids . ")");
        return count($ids);
    }

    /**
     * Truncate entire table
     */
    public function truncateCardPrices(): void {
        $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "card_set");
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
                     'ungraded', 'grade_9', 'grade_10', 'front_image', 'url'];
        $firstLine = (string)($lines[0] ?? '');
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
        $sql = "SELECT ungraded FROM " . DB_PREFIX . "card_set
                WHERE card_number = '" . $this->db->escape(trim($card_number)) . "'
                AND   TRIM(REPLACE(REPLACE(player, '#', ' '), ':', ' ')) = '" . $player_norm . "'";
        if ($brand !== '') {
            $sql .= " AND brand = '" . $this->db->escape(trim($brand)) . "'";
        }
        $sql .= " AND ungraded > 0 ORDER BY date_added DESC LIMIT 1";
        $query = $this->db->query($sql);
        return isset($query->row['ungraded']) ? (float)$query->row['ungraded'] : 0.0;
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
        $num_esc      = $this->db->escape(trim($card_number));
        $brand_esc    = $this->db->escape(trim($brand));
        $category_esc = $this->db->escape(trim($category));
        $player_norm  = $this->db->escape($this->normalizePlayerName($player));
        $year4        = $this->db->escape(substr(trim($year), 0, 4)); // "1992-93" → "1992"

        $select = "SELECT card_raw_id, set_name, year, brand, category, subset, ungraded
                   FROM " . DB_PREFIX . "card_set
                   WHERE card_number = '$num_esc'";
        $brand_clause = $brand_esc !== '' ? " AND brand = '$brand_esc'" : '';
        $category_clause = $category_esc !== '' ? " AND category = '$category_esc'" : '';
        $suffix = " AND ungraded > 0 ORDER BY date_added DESC";
        $norm_expr = "TRIM(REPLACE(REPLACE(player, '#', ' '), ':', ' '))";

        // Tier 1: exact normalized player
        $rows = $this->db->query(
            $select . " AND $norm_expr = '$player_norm'" . $brand_clause . $category_clause . $suffix
        )->rows;
        if ($rows) return $rows;

        // Tier 2: DB player is a substring of CSV player
        if ($player_norm !== '') {
            $rows = $this->db->query(
                $select . " AND '$player_norm' LIKE CONCAT('%', $norm_expr, '%')" . $brand_clause . $category_clause . $suffix
            )->rows;
            if ($rows) return $rows;
        }

        // Tier 3: card_number + brand + year (no player)
        if ($brand_esc !== '' && $year4 !== '') {
            $rows = $this->db->query(
                $select . $brand_clause . $category_clause . " AND LEFT(year, 4) = '$year4'" . $suffix
            )->rows;
            if ($rows) return $rows;
        }

        return [];
    }

    private function hasImportableValue(array $card): bool {
        $ungraded = (float)($card['ungraded'] ?? 0);
        $grade_9 = (float)($card['grade_9'] ?? 0);
        $grade_10 = (float)($card['grade_10'] ?? 0);

        return $ungraded > 0 || $grade_9 > 0 || $grade_10 > 0;
    }

    public function getLowestImporterUsdPriceFromRow(array $row): float {
        return round((float)($row['ungraded'] ?? 0), 2);
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
}
