<?php
namespace Opencart\Admin\Model\Shopmanager\Card;

/**
 * CardType — Central model for oc_card_type table
 * All functions related to card types must live here.
 */
class CardType extends \Opencart\System\Engine\Model {

    /**
     * Get all card types (active only by default)
     *
     * @param bool $activeOnly  If true, only return rows with status = 1
     * @return array  [ ['card_type_id'=>int, 'name'=>string, 'code'=>string, 'description'=>string, 'status'=>int], … ]
     */
    public function getCardTypes(bool $activeOnly = true): array {
        $where = $activeOnly ? "WHERE status = 1" : "";
        $query = $this->db->query(
            "SELECT card_type_id, name, code, description, status
             FROM " . DB_PREFIX . "card_type
             {$where}
             ORDER BY name ASC"
        );

        return $query->rows;
    }

    /**
     * Get a single card type by ID
     *
     * @param int $card_type_id
     * @return array  Row array, or empty array if not found
     */
    public function getCardType(int $card_type_id): array {
        $query = $this->db->query(
            "SELECT card_type_id, name, code, description, status
             FROM " . DB_PREFIX . "card_type
             WHERE card_type_id = '" . (int)$card_type_id . "'"
        );

        return $query->row ?? [];
    }

    /**
     * Get card_type_id from a category / sport name (case-insensitive).
     * Matches against both the name column and the code column.
     *
     * @param string $category_name  e.g. "Basketball", "NBA", "Pokemon"
     * @return int  card_type_id, or 0 if not found
     */
    public function getCardTypeIdByName(string $category_name): int {
        if (empty($category_name)) {
            return 0;
        }

        $query = $this->db->query(
            "SELECT card_type_id FROM `" . DB_PREFIX . "card_type`
             WHERE UPPER(name) = '" . $this->db->escape(strtoupper($category_name)) . "'
                OR UPPER(code) = '" . $this->db->escape(strtoupper($category_name)) . "'
             LIMIT 1"
        );

        return $query->num_rows > 0 ? (int)$query->row['card_type_id'] : 0;
    }

    /**
     * Count total card types (optionally only active ones)
     *
     * @param bool $activeOnly
     * @return int
     */
    public function getTotalCardTypes(bool $activeOnly = false): int {
        $where = $activeOnly ? "WHERE status = 1" : "";
        $query = $this->db->query(
            "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "card_type {$where}"
        );

        return (int)($query->row['total'] ?? 0);
    }
}
