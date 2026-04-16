<?php
// Original: shopmanager/card/import/card_price_active.php
namespace Opencart\Admin\Model\Shopmanager\Card\Import;

class CardPriceActive extends \Opencart\System\Engine\Model {

    // ------------------------------------------------------------------ //
    //  oc_card_price_raw — temporary eBay listings before matching
    // ------------------------------------------------------------------ //

    public function insertRaw(array $item, string $keyword): int {
        $this->db->query(
            "INSERT IGNORE INTO `" . DB_PREFIX . "card_price_raw`
             SET `ebay_item_id`   = '" . $this->db->escape($item['item_id'] ?? '') . "',
                 `keyword`        = '" . $this->db->escape($keyword) . "',
                 `title`          = '" . $this->db->escape($item['title'] ?? '') . "',
                 `url`            = '" . $this->db->escape($item['url'] ?? '') . "',
                 `picture`        = '" . $this->db->escape($item['picture'] ?? '') . "',
                 `price`          = '" . (float)($item['price'] ?? 0) . "',
                 `currency`       = '" . $this->db->escape($item['currency'] ?? 'USD') . "',
                 `condition_type` = '" . $this->db->escape($item['condition'] ?? '') . "',
                 `date_sold`      = '" . $this->db->escape($item['date_sold'] ?? '') . "',
                 `grade`          = '" . $this->db->escape($item['grade'] ?? '') . "',
                 `grader`         = '" . $this->db->escape($item['grader'] ?? '') . "',
                 `grade_score`    = '" . $this->db->escape($item['grade_score'] ?? '') . "',
                 `is_graded`      = '" . (int)($item['is_graded'] ?? 0) . "',
                 `status`         = 0,
                 `date_added`     = NOW()"
        );
        return (int)$this->db->getLastId();
    }

    public function getRawPending(): array {
        return $this->db->query(
            "SELECT * FROM `" . DB_PREFIX . "card_price_raw`
             WHERE `status` = 0
             ORDER BY `raw_id` ASC"
        )->rows;
    }

    public function setRawMatched(int $raw_id, int $card_raw_id): void {
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card_price_raw`
             SET `card_raw_id` = " . $card_raw_id . ",
                 `status`      = 1
             WHERE `raw_id` = " . $raw_id
        );
    }

    public function setRawRejected(int $raw_id): void {
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card_price_raw`
             SET `status` = 2
             WHERE `raw_id` = " . $raw_id
        );
    }

    public function getRawStats(): array {
        $row = $this->db->query(
            "SELECT
               COUNT(*) AS total,
               SUM(status = 0) AS pending,
               SUM(status = 1) AS matched,
               SUM(status = 2) AS rejected
             FROM `" . DB_PREFIX . "card_price_raw`"
        )->row;
        return $row;
    }

    public function clearRaw(): void {
        $this->db->query("TRUNCATE `" . DB_PREFIX . "card_price_raw`");
    }

    public function clearRawByKeyword(string $keyword): void {
        $this->db->query(
            "DELETE FROM `" . DB_PREFIX . "card_price_raw` WHERE `keyword` = '" . $this->db->escape($keyword) . "'"
        );
    }

    public function deleteRawByIds(array $ids): void {
        if (empty($ids)) return;
        $safe = implode(',', array_map('intval', $ids));
        $this->db->query("DELETE FROM `" . DB_PREFIX . "card_price_raw` WHERE `raw_id` IN (" . $safe . ")");
    }

    // ------------------------------------------------------------------ //
    //  oc_card_price_active — confirmed matched prices
    // ------------------------------------------------------------------ //

    public function insertActive(int $card_raw_id, array $item, string $keyword, float $price_cad = 0): int {
        $this->db->query(
            "INSERT IGNORE INTO `" . DB_PREFIX . "card_price_active`
             SET `card_raw_id`    = " . $card_raw_id . ",
                 `ebay_item_id`   = '" . $this->db->escape($item['ebay_item_id'] ?? $item['item_id'] ?? '') . "',
                 `title`          = '" . $this->db->escape($item['title'] ?? '') . "',
                 `url`            = '" . $this->db->escape($item['url'] ?? '') . "',
                 `picture`        = '" . $this->db->escape($item['picture'] ?? '') . "',
                 `price_usd`      = '" . (float)($item['price'] ?? 0) . "',
                 `price_cad`      = '" . (float)$price_cad . "',
                 `condition_type` = '" . $this->db->escape($item['condition'] ?? $item['condition_type'] ?? '') . "',
                 `date_sold`      = '" . $this->db->escape($item['date_sold'] ?? '') . "',
                 `grade`          = '" . $this->db->escape($item['grade'] ?? '') . "',
                 `grader`         = '" . $this->db->escape($item['grader'] ?? '') . "',
                 `grade_score`    = '" . $this->db->escape($item['grade_score'] ?? '') . "',
                 `is_graded`      = '" . (int)($item['is_graded'] ?? 0) . "',
                 `keyword`        = '" . $this->db->escape($keyword) . "',
                 `status`         = 1,
                 `date_added`     = NOW()"
        );
        return (int)$this->db->getLastId();
    }

    public function getActiveList(array $filter = [], int $start = 0, int $limit = 25): array {
        $sql = "SELECT a.*, cs.player, cs.year, cs.brand, cs.set_name, cs.card_number
                FROM `" . DB_PREFIX . "card_price_active` a
                LEFT JOIN `" . DB_PREFIX . "card_set` cs ON (cs.card_raw_id = a.card_raw_id)
                WHERE a.status = 1";

        if (!empty($filter['card_raw_id'])) {
            $sql .= " AND a.`card_raw_id` = " . (int)$filter['card_raw_id'];
        }
        if (!empty($filter['is_graded']) && $filter['is_graded'] !== '') {
            $sql .= " AND a.`is_graded` = " . (int)$filter['is_graded'];
        }
        if (!empty($filter['grader'])) {
            $sql .= " AND a.`grader` = '" . $this->db->escape($filter['grader']) . "'";
        }
        if (!empty($filter['keyword'])) {
            $sql .= " AND a.`keyword` LIKE '%" . $this->db->escape($filter['keyword']) . "%'";
        }
        if (!empty($filter['date_from'])) {
            $sql .= " AND a.`date_sold` >= '" . $this->db->escape($filter['date_from']) . "'";
        }
        if (!empty($filter['date_to'])) {
            $sql .= " AND a.`date_sold` <= '" . $this->db->escape($filter['date_to']) . "'";
        }

        $sort_map = ['date_sold', 'price_usd', 'card_raw_id', 'grader', 'grade_score', 'date_added'];
        $sort = in_array($filter['sort'] ?? '', $sort_map) ? $filter['sort'] : 'date_added';
        $order = strtoupper($filter['order'] ?? '') === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY a.`" . $sort . "` " . $order;
        $sql .= " LIMIT " . (int)$start . ", " . (int)$limit;

        return $this->db->query($sql)->rows;
    }

    public function getActiveTotalRows(array $filter = []): int {
        $sql = "SELECT COUNT(*) AS total
                FROM `" . DB_PREFIX . "card_price_active` a
                WHERE a.status = 1";

        if (!empty($filter['card_raw_id'])) {
            $sql .= " AND a.`card_raw_id` = " . (int)$filter['card_raw_id'];
        }
        if (!empty($filter['is_graded']) && $filter['is_graded'] !== '') {
            $sql .= " AND a.`is_graded` = " . (int)$filter['is_graded'];
        }
        if (!empty($filter['grader'])) {
            $sql .= " AND a.`grader` = '" . $this->db->escape($filter['grader']) . "'";
        }
        if (!empty($filter['keyword'])) {
            $sql .= " AND a.`keyword` LIKE '%" . $this->db->escape($filter['keyword']) . "%'";
        }
        if (!empty($filter['date_from'])) {
            $sql .= " AND a.`date_sold` >= '" . $this->db->escape($filter['date_from']) . "'";
        }
        if (!empty($filter['date_to'])) {
            $sql .= " AND a.`date_sold` <= '" . $this->db->escape($filter['date_to']) . "'";
        }

        return (int)$this->db->query($sql)->row['total'];
    }

    public function deleteActive(int $active_id): void {
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card_price_active`
             SET `status` = 0
             WHERE `active_id` = " . $active_id
        );
    }

    public function deleteActiveByIds(array $ids): void {
        if (empty($ids)) return;
        $escaped = implode(',', array_map('intval', $ids));
        $this->db->query(
            "UPDATE `" . DB_PREFIX . "card_price_active`
             SET `status` = 0
             WHERE `active_id` IN (" . $escaped . ")"
        );
    }

    public function getCardSetAll(): array {
        return $this->db->query(
            "SELECT `card_raw_id`, `card_number`, `year`, `brand`, `player`, `set_name`, `subset`
             FROM `" . DB_PREFIX . "card_set`
             ORDER BY `card_raw_id` ASC"
        )->rows;
    }

    public function getCardSetById(int $card_raw_id): array {
        return $this->db->query(
            "SELECT * FROM `" . DB_PREFIX . "card_set`
             WHERE `card_raw_id` = " . $card_raw_id
        )->row;
    }

    /**
     * Card SETS (year+brand+set_name) from oc_card_set where NONE of the cards
     * in that set have active prices yet in oc_card_price_active.
     */
    public function getCardSetWithoutActivePrices(): array {
        return $this->db->query(
            "SELECT cs.`category`, cs.`year`, cs.`brand`, cs.`set_name`,
                    COUNT(*) AS card_count
             FROM `" . DB_PREFIX . "card_set` cs
             WHERE NOT EXISTS (
                 SELECT 1 FROM `" . DB_PREFIX . "card_price_active` cpa
                 INNER JOIN `" . DB_PREFIX . "card_set` cs2
                     ON cs2.`card_raw_id` = cpa.`card_raw_id`
                    AND cs2.`category`  = cs.`category`
                    AND cs2.`year`      = cs.`year`
                    AND cs2.`brand`     = cs.`brand`
                    AND cs2.`set_name`  = cs.`set_name`
                 WHERE cpa.`status` = 1
             )
             GROUP BY cs.`category`, cs.`year`, cs.`brand`, cs.`set_name`
             ORDER BY cs.`category`, cs.`year` DESC, cs.`brand`, cs.`set_name`"
        )->rows;
    }

    public function getDistinctCategories(): array {
        return $this->db->query(
            "SELECT DISTINCT `category`
             FROM `" . DB_PREFIX . "card_set`
             WHERE `category` != ''
             ORDER BY `category`"
        )->rows;
    }
}
