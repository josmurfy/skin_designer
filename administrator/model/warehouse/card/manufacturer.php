<?php
// Original: warehouse/card/manufacturer.php
namespace Opencart\Admin\Model\Warehouse\Card;

class Manufacturer extends \Opencart\System\Engine\Model {

    /**
     * Get all manufacturers, including comma-separated card type names via JOIN
     */
    public function getManufacturers(array $data = []): array {
        $sql = "SELECT m.manufacturer_id, m.name, m.status,
                    GROUP_CONCAT(ct.name ORDER BY ct.name SEPARATOR ', ') AS card_type
                FROM " . DB_PREFIX . "card_manufacturer m
                LEFT JOIN " . DB_PREFIX . "card_manufacturer_card_type mct ON mct.manufacturer_id = m.manufacturer_id
                LEFT JOIN " . DB_PREFIX . "card_type ct ON ct.card_type_id = mct.card_type_id";

        $implode = [];

        if (!empty($data['filter_name'])) {
            $implode[] = "m.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_card_type'])) {
            $implode[] = "m.manufacturer_id IN (
                SELECT manufacturer_id FROM " . DB_PREFIX . "card_manufacturer_card_type
                WHERE card_type_id = '" . (int)$data['filter_card_type'] . "'
            )";
        }
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $implode[] = "m.status = '" . (int)$data['filter_status'] . "'";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $sql .= " GROUP BY m.manufacturer_id";

        $sort_data = ['name', 'status'];

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY m." . $data['sort'];
        } else {
            $sql .= " ORDER BY m.name";
        }

        if (isset($data['order']) && $data['order'] === 'DESC') {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }

    /**
     * Get total count of manufacturers (for pagination)
     */
    public function getTotalManufacturers(array $data = []): int {
        $sql = "SELECT COUNT(DISTINCT m.manufacturer_id) AS total
                FROM " . DB_PREFIX . "card_manufacturer m";

        $implode = [];

        if (!empty($data['filter_name'])) {
            $implode[] = "m.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        if (!empty($data['filter_card_type'])) {
            $implode[] = "m.manufacturer_id IN (
                SELECT manufacturer_id FROM " . DB_PREFIX . "card_manufacturer_card_type
                WHERE card_type_id = '" . (int)$data['filter_card_type'] . "'
            )";
        }
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $implode[] = "m.status = '" . (int)$data['filter_status'] . "'";
        }

        if ($implode) {
            $sql .= " WHERE " . implode(" AND ", $implode);
        }

        $query = $this->db->query($sql);

        return (int)$query->row['total'];
    }

    /**
     * Get a single manufacturer by ID
     */
    public function getManufacturer(int $manufacturer_id): array {
        $query = $this->db->query(
            "SELECT * FROM " . DB_PREFIX . "card_manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'"
        );

        return $query->row ?? [];
    }

    /**
     * Get the card_type_ids linked to a manufacturer (for form pre-selection)
     */
    public function getManufacturerCardTypeIds(int $manufacturer_id): array {
        $query = $this->db->query(
            "SELECT card_type_id FROM " . DB_PREFIX . "card_manufacturer_card_type
             WHERE manufacturer_id = '" . (int)$manufacturer_id . "'"
        );

        return array_column($query->rows, 'card_type_id');
    }

    /**
     * Add a new manufacturer
     */
    public function addManufacturer(array $data): int {
        $this->db->query(
            "INSERT INTO " . DB_PREFIX . "card_manufacturer SET
                name   = '" . $this->db->escape($data['name']) . "',
                status = '" . (int)($data['status'] ?? 1) . "'"
        );

        $manufacturer_id = $this->db->getLastId();

        $this->saveCardTypes($manufacturer_id, $data['card_type_ids'] ?? []);

        return $manufacturer_id;
    }

    /**
     * Edit an existing manufacturer
     */
    public function editManufacturer(int $manufacturer_id, array $data): void {
        $this->db->query(
            "UPDATE " . DB_PREFIX . "card_manufacturer SET
                name   = '" . $this->db->escape($data['name']) . "',
                status = '" . (int)($data['status'] ?? 1) . "'
             WHERE manufacturer_id = '" . (int)$manufacturer_id . "'"
        );

        $this->saveCardTypes($manufacturer_id, $data['card_type_ids'] ?? []);
    }

    /**
     * Delete a manufacturer and its card type associations
     */
    public function deleteManufacturer(int $manufacturer_id): void {
        $this->db->query(
            "DELETE FROM " . DB_PREFIX . "card_manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'"
        );
        $this->db->query(
            "DELETE FROM " . DB_PREFIX . "card_manufacturer_card_type WHERE manufacturer_id = '" . (int)$manufacturer_id . "'"
        );
    }

    /**
     * Replace the card type associations for a manufacturer
     */
    private function saveCardTypes(int $manufacturer_id, array $card_type_ids): void {
        $this->db->query(
            "DELETE FROM " . DB_PREFIX . "card_manufacturer_card_type WHERE manufacturer_id = '" . (int)$manufacturer_id . "'"
        );

        foreach ($card_type_ids as $card_type_id) {
            if ((int)$card_type_id > 0) {
                $this->db->query(
                    "INSERT IGNORE INTO " . DB_PREFIX . "card_manufacturer_card_type SET
                        manufacturer_id = '" . (int)$manufacturer_id . "',
                        card_type_id    = '" . (int)$card_type_id . "'"
                );
            }
        }
    }
/**
     * Get list of card manufacturers from database
     * @return array Array of manufacturer names
     */
   public function getCardManufacturers(): array {
        $query = $this->db->query("
            SELECT name 
            FROM " . DB_PREFIX . "card_manufacturer 
            WHERE status = 1 
            ORDER BY name ASC
        ");
        
        $manufacturers = [];
        foreach ($query->rows as $row) {
            $manufacturers[] = $row['name'];
        }
        
        return $manufacturers;
    }   
    /**
     * Check if a manufacturer name already exists (for validation)
     */
    public function nameExists(string $name, int $exclude_id = 0): bool {
        $query = $this->db->query(
            "SELECT manufacturer_id FROM " . DB_PREFIX . "card_manufacturer
             WHERE name = '" . $this->db->escape($name) . "'
             AND manufacturer_id != '" . (int)$exclude_id . "'"
        );

        return !empty($query->row);
    }
}
