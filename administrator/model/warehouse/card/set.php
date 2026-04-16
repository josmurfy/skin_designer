<?php
// Original: shopmanager/card/card_set.php
namespace Opencart\Admin\Model\Shopmanager\Card;

/**
 * Class CardSet
 *
 * CRUD model for oc_card_set pricing database.
 * Enriches results with data from oc_card_price_sold and oc_card_price_active.
 *
 * @package Opencart\Admin\Model\Shopmanager\Card
 */
class CardSet extends \Opencart\System\Engine\Model {

    // ─── CREATE ──────────────────────────────────────────────────────────

    public function addCardSet(array $data): int {
        $this->db->query("INSERT INTO " . DB_PREFIX . "card_set SET
            title         = '" . $this->db->escape($data['title'] ?? '') . "',
            category      = '" . $this->db->escape($data['category'] ?? '') . "',
            year          = '" . $this->db->escape($data['year'] ?? '') . "',
            brand         = '" . $this->db->escape($data['brand'] ?? '') . "',
            set_name      = '" . $this->db->escape($data['set_name'] ?? $data['set'] ?? '') . "',
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
            status        = '" . (int)($data['status'] ?? 1) . "',
            date_added    = NOW(),
            date_modified = NOW()");

        return (int)$this->db->getLastId();
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────

    public function editCardSet(int $card_raw_id, array $data): void {
        $sets = [];

        $editable = [
            'title', 'category', 'year', 'brand', 'set_name', 'subset',
            'player', 'card_number', 'attributes', 'team', 'variation', 'front_image'
        ];
        foreach ($editable as $col) {
            if (isset($data[$col])) {
                $sets[] = "`" . $col . "` = '" . $this->db->escape($data[$col]) . "'";
            }
        }

        $numeric = ['ungraded', 'grade_9', 'grade_10'];
        foreach ($numeric as $col) {
            if (isset($data[$col])) {
                $sets[] = "`" . $col . "` = '" . (float)$data[$col] . "'";
            }
        }

        if (isset($data['status'])) {
            $sets[] = "`status` = '" . (int)$data['status'] . "'";
        }

        if (empty($sets)) {
            return;
        }

        $sets[] = "`date_modified` = NOW()";

        $this->db->query("UPDATE " . DB_PREFIX . "card_set SET " . implode(', ', $sets) . " WHERE card_raw_id = '" . (int)$card_raw_id . "'");
    }

    // ─── DELETE ──────────────────────────────────────────────────────────

    public function deleteCardSet(int $card_raw_id): void {
        $this->db->query("DELETE FROM " . DB_PREFIX . "card_set WHERE card_raw_id = '" . (int)$card_raw_id . "'");
    }

    public function deleteCardSets(array $ids): int {
        if (empty($ids)) return 0;
        $safe = implode(',', array_map('intval', $ids));
        $this->db->query("DELETE FROM " . DB_PREFIX . "card_set WHERE card_raw_id IN (" . $safe . ")");
        return count($ids);
    }

    // ─── READ: Single ────────────────────────────────────────────────────

    public function getCardSet(int $card_raw_id): array {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "card_set WHERE card_raw_id = '" . (int)$card_raw_id . "'");
        return $query->row ?? [];
    }

    // ─── READ: List with filters ─────────────────────────────────────────

    public function getCardSets(array $data = []): array {
        $bestPriceExpr = "GREATEST(COALESCE(cs.ungraded, 0), COALESCE(cs.grade_9, 0), COALESCE(cs.grade_10, 0))";

        $sql = "SELECT cs.*, " . $bestPriceExpr . " AS best_price FROM " . DB_PREFIX . "card_set cs";

        $where = $this->buildWhere($data, 'cs');
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        // Sort
        $valid_sorts = [
            'card_raw_id', 'title', 'category', 'year', 'brand', 'set_name',
            'player', 'card_number', 'ungraded', 'grade_9', 'grade_10',
            'date_added', 'best_price', 'team', 'grading_gain'
        ];
        $sort  = (isset($data['sort']) && in_array($data['sort'], $valid_sorts)) ? $data['sort'] : 'best_price';
        $order = (isset($data['order']) && strtoupper($data['order']) === 'ASC') ? 'ASC' : 'DESC';

        $gradingGainExpr = "(GREATEST(COALESCE(cs.grade_9, 0), COALESCE(cs.grade_10, 0)) * 0.87 - 25.35) - (" . $bestPriceExpr . " * 0.87 - 0.35)";

        $sortMap = [
            'best_price'    => $bestPriceExpr,
            'grading_gain'  => $gradingGainExpr,
        ];
        $sortExpr = $sortMap[$sort] ?? 'cs.' . $sort;
        $sql .= ' ORDER BY ' . $sortExpr . ' ' . $order;

        // Pagination
        if (isset($data['start']) || isset($data['limit'])) {
            $start = max(0, (int)($data['start'] ?? 0));
            $limit = max(1, (int)($data['limit'] ?? 50));
            $sql .= ' LIMIT ' . $start . ',' . $limit;
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalCardSets(array $data = []): int {
        $sql = 'SELECT COUNT(*) AS total FROM ' . DB_PREFIX . 'card_set cs';

        $where = $this->buildWhere($data, 'cs');
        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        return (int)($this->db->query($sql)->row['total'] ?? 0);
    }

    // ─── READ: Sold enrichment (oc_card_price_sold) ──────────────────────

    /**
     * For a list of cards, fetch sold records grouped by card_number|||set_name.
     *
     * @param  array $cards  Each element must have 'card_number' and 'set_name'
     * @return array<string, array[]>  keyed by "card_number|||set_name"
     */
    public function getSoldBilanForCards(array $cards): array {
        if (empty($cards)) return [];

        $conditions = [];
        foreach ($cards as $card) {
            $cn = $this->db->escape(trim((string)($card['card_number'] ?? '')));
            $sn = $this->db->escape(trim((string)($card['set_name'] ?? '')));
            if ($cn === '' && $sn === '') continue;
            $parts = [];
            if ($cn !== '') $parts[] = "card_number = '" . $cn . "'";
            if ($sn !== '') $parts[] = "set_name    = '" . $sn . "'";
            $conditions[] = '(' . implode(' AND ', $parts) . ')';
        }

        if (empty($conditions)) return [];

        $sql = 'SELECT * FROM ' . DB_PREFIX . 'card_price_sold'
             . ' WHERE status = 1 AND (' . implode(' OR ', array_unique($conditions)) . ')'
             . ' ORDER BY date_sold DESC';

        $bilan = [];
        foreach ($this->db->query($sql)->rows as $row) {
            $key = $row['card_number'] . '|||' . $row['set_name'];
            $bilan[$key][] = $row;
        }
        return $bilan;
    }

    // ─── READ: Active prices enrichment (oc_card_price_active) ───────────

    /**
     * For a list of card_raw_ids, fetch active confirmed prices.
     *
     * @param  array $card_raw_ids
     * @return array<int, array[]>  keyed by card_raw_id
     */
    public function getActivePricesForCards(array $card_raw_ids): array {
        if (empty($card_raw_ids)) return [];

        $safe = implode(',', array_map('intval', $card_raw_ids));

        $sql = "SELECT * FROM " . DB_PREFIX . "card_price_active"
             . " WHERE status = 1 AND card_raw_id IN (" . $safe . ")"
             . " ORDER BY date_sold DESC";

        $result = [];
        foreach ($this->db->query($sql)->rows as $row) {
            $result[(int)$row['card_raw_id']][] = $row;
        }
        return $result;
    }

    // ─── DROPDOWN: Cascading distinct values ─────────────────────────────

    /**
     * Return distinct values for a column, filtered by the other active filters.
     * Used for cascading dropdown population.
     */
    public function getFilteredDistinct(string $field, array $context = [], int $limit = 500): array {
        $allowed = ['title', 'category', 'year', 'brand', 'set_name', 'player', 'team', 'variation', 'card_number', 'subset'];
        if (!in_array($field, $allowed)) return [];

        $sql = 'SELECT DISTINCT `' . $field . '` AS value FROM ' . DB_PREFIX . 'card_set WHERE `' . $field . "` != '' AND `" . $field . '` IS NOT NULL';

        $ctx_fields = ['year', 'category', 'brand', 'set_name', 'player', 'card_number'];
        foreach ($ctx_fields as $cf) {
            if ($cf !== $field && !empty($context[$cf])) {
                $sql .= " AND `" . $cf . "` = '" . $this->db->escape($context[$cf]) . "'";
            }
        }

        $sql .= ' ORDER BY `' . $field . '` ASC LIMIT ' . (int)$limit;
        return array_column($this->db->query($sql)->rows, 'value');
    }

    // ─── PRIVATE: WHERE builder ──────────────────────────────────────────

    private function buildWhere(array $data, string $alias = 'cs'): array {
        $p = $alias ? $alias . '.' : '';
        $where = [];

        if (!empty($data['filter_title']))
            $where[] = $p . "title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        if (!empty($data['filter_category']))
            $where[] = $p . "category = '" . $this->db->escape($data['filter_category']) . "'";
        if (!empty($data['filter_year']))
            $where[] = $p . "year = '" . $this->db->escape($data['filter_year']) . "'";
        if (!empty($data['filter_brand'])) {
            $b = $this->db->escape($data['filter_brand']);
            $where[] = $p . "brand = '" . $b . "'";
        }
        if (!empty($data['filter_set']))
            $where[] = $p . "set_name = '" . $this->db->escape($data['filter_set']) . "'";
        if (!empty($data['filter_player']))
            $where[] = $p . "player = '" . $this->db->escape($data['filter_player']) . "'";
        if (!empty($data['filter_card_number']))
            $where[] = $p . "card_number LIKE '%" . $this->db->escape($data['filter_card_number']) . "%'";
        if (isset($data['filter_min_price']) && $data['filter_min_price'] !== '')
            $where[] = "GREATEST(COALESCE(" . $p . "ungraded, 0), COALESCE(" . $p . "grade_9, 0), COALESCE(" . $p . "grade_10, 0)) >= '" . (float)$data['filter_min_price'] . "'";
        if (isset($data['filter_max_price']) && $data['filter_max_price'] !== '')
            $where[] = "GREATEST(COALESCE(" . $p . "ungraded, 0), COALESCE(" . $p . "grade_9, 0), COALESCE(" . $p . "grade_10, 0)) <= '" . (float)$data['filter_max_price'] . "'";

        return $where;
    }
}
