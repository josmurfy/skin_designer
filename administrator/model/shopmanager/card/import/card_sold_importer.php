<?php
namespace Opencart\Admin\Model\Shopmanager\Card\Import;

class CardSoldImporter extends \Opencart\System\Engine\Model {


    // ─── READ ────────────────────────────────────────────────────────────────

    public function getSoldRecords(array $data = []): array {
        

        $sql  = 'SELECT * FROM ' . DB_PREFIX . 'card_price_sold';
        $where = $this->buildWhere($data);
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);

        $valid_sorts = [
            'card_price_sold_id','title','category','year','brand','set_name',
            'player','card_number','price','date_sold','date_added','grader','grade'
        ];
        $sort  = (isset($data['sort']) && in_array($data['sort'], $valid_sorts))
                  ? $data['sort'] : 'card_price_sold_id';
        $order = (isset($data['order']) && strtoupper($data['order']) === 'ASC')
                  ? 'ASC' : 'DESC';
        $sql  .= ' ORDER BY ' . $sort . ' ' . $order;

        if (isset($data['start']) || isset($data['limit'])) {
            $start = max(0, (int)($data['start'] ?? 0));
            $limit = max(1, (int)($data['limit'] ?? 25));
            $sql  .= ' LIMIT ' . $start . ',' . $limit;
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalSoldRecords(array $data = []): int {
        

        $sql   = 'SELECT COUNT(*) AS total FROM ' . DB_PREFIX . 'card_price_sold';
        $where = $this->buildWhere($data);
        if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);

        $row = $this->db->query($sql)->row;
        return (int)($row['total'] ?? 0);
    }

    // ─── WRITE ───────────────────────────────────────────────────────────────

    public function insertSoldRecord(array $row): int {
        

        $dateSold = trim((string)($row['date_sold'] ?? ''));
        $dateSoldSql = ($dateSold !== '' && $dateSold !== '0000-00-00')
            ? "'" . $this->db->escape($dateSold) . "'"
            : 'NULL';

        $this->db->query("
            INSERT INTO `" . DB_PREFIX . "card_price_sold`
                (title, category, year, brand, set_name, subset, player,
                 card_number, attributes, team, variation, grader, grade,
                 price, currency, type_listing, bids, total_sold, ebay_item_id,
                 front_image, status, date_sold, date_added)
            VALUES (
                '" . $this->db->escape((string)($row['title']        ?? '')) . "',
                '" . $this->db->escape((string)($row['category']     ?? '')) . "',
                '" . $this->db->escape((string)($row['year']         ?? '')) . "',
                '" . $this->db->escape((string)($row['brand']        ?? '')) . "',
                '" . $this->db->escape((string)($row['set_name']     ?? '')) . "',
                '" . $this->db->escape((string)($row['subset']       ?? '')) . "',
                '" . $this->db->escape((string)($row['player']       ?? '')) . "',
                '" . $this->db->escape((string)($row['card_number']  ?? '')) . "',
                '" . $this->db->escape((string)($row['attributes']   ?? '')) . "',
                '" . $this->db->escape((string)($row['team']         ?? '')) . "',
                '" . $this->db->escape((string)($row['variation']    ?? '')) . "',
                '" . $this->db->escape((string)($row['grader']       ?? '')) . "',
                '" . $this->db->escape((string)($row['grade']        ?? '')) . "',
                '" . (float)($row['price']                            ?? 0) . "',
                '" . $this->db->escape((string)($row['currency']     ?? 'CAD')) . "',
                '" . $this->db->escape((string)($row['type_listing'] ?? '')) . "',
                '" . (int)($row['bids']                               ?? 0) . "',
                '" . (int)($row['total_sold']                         ?? 1) . "',
                '" . $this->db->escape((string)($row['ebay_item_id'] ?? '')) . "',
                '" . $this->db->escape((string)($row['front_image']  ?? '')) . "',
                '" . (int)($row['status']                             ?? 1) . "',
                " . $dateSoldSql . ",
                NOW()
            )
        ");

        return (int)$this->db->getLastId();
    }

    public function deleteSoldRecords(array $ids): void {
        if (empty($ids)) return;
        
        $ids = array_map('intval', $ids);
        $this->db->query(
            'DELETE FROM ' . DB_PREFIX . 'card_price_sold WHERE card_price_sold_id IN (' . implode(',', $ids) . ')'
        );
    }

    public function truncateSold(): void {
        
        $this->db->query('TRUNCATE TABLE ' . DB_PREFIX . 'card_price_sold');
    }

    // ─── AUTOCOMPLETE ────────────────────────────────────────────────────────

    public function autocompleteField(string $field, string $term): array {
        

        $allowed = ['title','category','year','brand','set_name','player','card_number','grader'];
        if (!in_array($field, $allowed)) return [];

        $sql = "SELECT DISTINCT `" . $field . "` AS val
                FROM " . DB_PREFIX . "card_price_sold
                WHERE `" . $field . "` LIKE '%" . $this->db->escape($term) . "%'
                ORDER BY `" . $field . "` ASC
                LIMIT 20";

        $rows = $this->db->query($sql)->rows;
        return array_column($rows, 'val');
    }

    // ─── Distinct values for dropdowns ───────────────────────────────────────

    public function getDistinctValues(string $field): array {
        
        $allowed = ['brand','currency','grader','type_listing'];
        if (!in_array($field, $allowed)) return [];

        $sql = "SELECT DISTINCT `" . $field . "` AS val
                FROM " . DB_PREFIX . "card_price_sold
                WHERE `" . $field . "` != ''
                ORDER BY `" . $field . "` ASC";

        $rows = $this->db->query($sql)->rows;
        return array_column($rows, 'val');
    }

    /**
     * Fetch all sold records from oc_card_price_sold matching a list of
     * (card_number + set_name) pairs.  Returns a map keyed by
     * "card_number|||set_name" => [ rows... ] for O(1) lookup by the caller.
     *
     * @param  array $cards  Each element must have keys 'card_number' and 'set_name' (or 'set').
     * @return array<string, array[]>
     */
    public function getSoldBilanForCards(array $cards): array {
        if (empty($cards)) return [];
        

        $conditions = [];
        foreach ($cards as $card) {
            $cn = $this->db->escape(trim((string)($card['card_number'] ?? '')));
            $sn = $this->db->escape(trim((string)($card['set_name'] ?? $card['set'] ?? '')));
            if ($cn === '' && $sn === '') continue;
            $parts = [];
            if ($cn !== '') $parts[] = "card_number = '$cn'";
            if ($sn !== '') $parts[] = "set_name    = '$sn'";
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

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function buildWhere(array $data): array {
        $where = [];

        if (!empty($data['filter_title']))
            $where[] = "title LIKE '%" . $this->db->escape($data['filter_title']) . "%'";
        if (!empty($data['filter_category']))
            $where[] = "category LIKE '%" . $this->db->escape($data['filter_category']) . "%'";
        if (!empty($data['filter_year']))
            $where[] = "year = '" . $this->db->escape($data['filter_year']) . "'";
        if (!empty($data['filter_brand']))
            $where[] = "brand = '" . $this->db->escape($data['filter_brand']) . "'";
        if (!empty($data['filter_set']))
            $where[] = "set_name LIKE '%" . $this->db->escape($data['filter_set']) . "%'";
        if (!empty($data['filter_player']))
            $where[] = "player LIKE '%" . $this->db->escape($data['filter_player']) . "%'";
        if (!empty($data['filter_card_number']))
            $where[] = "card_number LIKE '%" . $this->db->escape($data['filter_card_number']) . "%'";
        if (!empty($data['filter_grader']))
            $where[] = "grader = '" . $this->db->escape($data['filter_grader']) . "'";
        if (isset($data['filter_min_price']) && $data['filter_min_price'] !== '')
            $where[] = "price >= '" . (float)$data['filter_min_price'] . "'";
        if (isset($data['filter_max_price']) && $data['filter_max_price'] !== '')
            $where[] = "price <= '" . (float)$data['filter_max_price'] . "'";
        if (isset($data['filter_missing_card_number']) && $data['filter_missing_card_number'])
            $where[] = "(card_number = '' OR card_number IS NULL)";
        if (!empty($data['filter_has_price']))
            $where[] = "card_number != '' AND EXISTS (SELECT 1 FROM " . DB_PREFIX . "card_price p WHERE p.card_number = card_number)";

        return $where;
    }
}
