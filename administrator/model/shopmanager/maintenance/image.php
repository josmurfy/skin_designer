<?php
namespace Opencart\Admin\Model\Shopmanager\Maintenance;

class Image extends \Opencart\System\Engine\Model {
    
    /**
     * Get total products count
     */
    public function getTotalProducts(): int {
        $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "product`");
        return (int)$query->row['total'];
    }
    
    /**
     * Check if maintenance_data column exists
     */
    public function maintenanceColumnExists(): bool {
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = '" . DB_DATABASE . "' 
            AND TABLE_NAME = '" . DB_PREFIX . "product' 
            AND COLUMN_NAME = 'maintenance_data'
        ");
        return $query->row['total'] > 0;
    }
    
    /**
     * Get count of validated products
     */
    public function getValidatedCount(): int {
        $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "product` WHERE maintenance_data IS NOT NULL AND JSON_EXTRACT(maintenance_data, '$.images.validation_date') IS NOT NULL");
        return (int)$query->row['total'];
    }
    
    /**
     * Get products that need validation
     */
    public function getProductsNeedingValidation(int $offset = 0, int $limit = 50): array {
        $query = $this->db->query("SELECT product_id, model, image, quantity FROM `" . DB_PREFIX . "product` WHERE maintenance_data IS NULL OR JSON_EXTRACT(maintenance_data, '$.images.validation_date') IS NULL OR DATEDIFF(NOW(), JSON_UNQUOTE(JSON_EXTRACT(maintenance_data, '$.images.validation_date'))) > 30 ORDER BY product_id LIMIT " . (int)$offset . ", " . (int)$limit);
        return $query->rows;
    }
    
    /**
     * Get count of products needing validation
     */
    public function getTotalNeedingValidation(): int {
        $query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "product` WHERE maintenance_data IS NULL OR JSON_EXTRACT(maintenance_data, '$.images.validation_date') IS NULL OR DATEDIFF(NOW(), JSON_UNQUOTE(JSON_EXTRACT(maintenance_data, '$.images.validation_date'))) > 30");
        return (int)$query->row['total'];
    }
    
    /**
     * Get product basic data
     */
    public function getProduct(int $product_id): array {
        $query = $this->db->query("SELECT product_id, model, image, quantity, maintenance_data FROM `" . DB_PREFIX . "product` WHERE product_id = " . (int)$product_id);
        return $query->num_rows > 0 ? $query->row : [];
    }
    
    /**
     * Get fresh product data with description (bypass any potential cache)
     */
    public function getFreshProduct(int $product_id): array {
        $query = $this->db->query("
            SELECT p.product_id, p.model, p.image, pd.name, p.maintenance_data
            FROM `" . DB_PREFIX . "product` p
            LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id)
            WHERE p.product_id = " . (int)$product_id . "
            AND pd.language_id = " . (int)$this->config->get('config_language_id') . "
            LIMIT 1
        ");
        return $query->num_rows > 0 ? $query->row : [];
    }
    
    /**
     * Get secondary images for product
     */
    public function getSecondaryImages(int $product_id): array {
        $query = $this->db->query("SELECT image FROM `" . DB_PREFIX . "product_image` WHERE product_id = " . (int)$product_id . " ORDER BY sort_order");
        return $query->rows;
    }
    
    /**
     * Update product maintenance data
     */
    public function updateMaintenanceData(int $product_id, array $maintenance): void {
        $json_data = json_encode($maintenance, JSON_UNESCAPED_UNICODE);
        $this->db->query("UPDATE `" . DB_PREFIX . "product` SET maintenance_data = '" . $this->db->escape($json_data) . "' WHERE product_id = " . (int)$product_id);
    }
    
    /**
     * Get products with filters
     */
    public function getProducts(array $data = []): array {        // Debug filters
        /*error_log("MAINTENANCE FILTERS: " . json_encode([
            'filter_image_issue' => $data['filter_image_issue'] ?? 'NOT SET',
            'filter_low_resolution' => $data['filter_low_resolution'] ?? 'NOT SET',
            'filter_wrong_path' => $data['filter_wrong_path'] ?? 'NOT SET',
            'filter_old_nomenclature' => $data['filter_old_nomenclature'] ?? 'NOT SET',
            'filter_orphan_images' => $data['filter_orphan_images'] ?? 'NOT SET'
        ]));*/
                $sql = "SELECT p.product_id, p.model, p.image, p.quantity, p.maintenance_data,
                       pd.name,
                       (SELECT SUM(quantity) FROM " . DB_PREFIX . "order_product op 
                        LEFT JOIN `" . DB_PREFIX . "order` o ON op.order_id = o.order_id 
                        WHERE op.product_id = p.product_id AND o.order_status_id IN (1,2,3,12,15)) as allocated_qty
                FROM " . DB_PREFIX . "product p
                LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = 1)
                WHERE 1=1 AND p.marketplace_item_id IS NOT NULL AND p.marketplace_item_id != ''";
        
        // Filtres de base
        if (!empty($data['filter_product_id'])) {
            $sql .= " AND p.product_id = " . (int)$data['filter_product_id'];
        }
        
        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
        }
        
        // Filtres basés sur le cache JSON
        $conditions = [];
        $exclusions = [];
        
        if (!empty($data['filter_image_issue']) && $data['filter_image_issue'] == '1') {
            $conditions[] = "(JSON_EXTRACT(p.maintenance_data, '\$.images.has_issues') = 1 AND JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types'), '\"missing\"'))";
        } elseif (isset($data['filter_image_issue']) && $data['filter_image_issue'] == '0') {
            // Exclude products with missing images if filter is explicitly unchecked
            $exclusions[] = "(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types') IS NULL OR NOT JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types'), '\"missing\"'))";
        }
        
        if (!empty($data['filter_low_resolution']) && $data['filter_low_resolution'] == '1') {
            $conditions[] = "(JSON_EXTRACT(p.maintenance_data, '\$.images.has_issues') = 1 AND JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types'), '\"low_res\"'))";
        } elseif (isset($data['filter_low_resolution']) && $data['filter_low_resolution'] == '0') {
            $exclusions[] = "(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types') IS NULL OR NOT JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types'), '\"low_res\"'))";
        }
        
        if (!empty($data['filter_wrong_path']) && $data['filter_wrong_path'] == '1') {
            $conditions[] = "(JSON_EXTRACT(p.maintenance_data, '\$.images.has_issues') = 1 AND JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types'), '\"wrong_path\"'))";
        } elseif (isset($data['filter_wrong_path']) && $data['filter_wrong_path'] == '0') {
            $exclusions[] = "(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types') IS NULL OR NOT JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types'), '\"wrong_path\"'))";
        }
        
        if (!empty($data['filter_old_nomenclature']) && $data['filter_old_nomenclature'] == '1') {
            $conditions[] = "(JSON_EXTRACT(p.maintenance_data, '\$.images.has_issues') = 1 AND JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types'), '\"old_nomenclature\"'))";
        } elseif (isset($data['filter_old_nomenclature']) && $data['filter_old_nomenclature'] == '0') {
            $exclusions[] = "(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types') IS NULL OR NOT JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\$.images.issue_types'), '\"old_nomenclature\"'))";
        }

        if (!empty($data['filter_orphan_images']) && $data['filter_orphan_images'] == '1') {
            $conditions[] = "(COALESCE(CAST(JSON_UNQUOTE(JSON_EXTRACT(p.maintenance_data, '\$.images.orphan_count')) AS UNSIGNED), 0) > 0)";
        } elseif (isset($data['filter_orphan_images']) && $data['filter_orphan_images'] == '0') {
            $exclusions[] = "(COALESCE(CAST(JSON_UNQUOTE(JSON_EXTRACT(p.maintenance_data, '\$.images.orphan_count')) AS UNSIGNED), 0) <= 0)";
        }
        
        if (!empty($conditions)) {
            $sql .= " AND (" . implode(" OR ", $conditions) . ")";
        } else {
            // If no specific filter, show only products with issues by default
            $has_specific_filters = !empty($data['filter_product_id']) || !empty($data['filter_name']) || !empty($data['filter_model']);
            if (!$has_specific_filters) {
                $sql .= " AND JSON_EXTRACT(p.maintenance_data, '\$.images.has_issues') = 1";
            }
        }
        
        // Apply exclusions for unchecked filters
        if (!empty($exclusions)) {
            $sql .= " AND (" . implode(" AND ", $exclusions) . ")";
        }
        
        // Debug: log the SQL query
        //error_log("MAINTENANCE SQL: " . $sql);
        
        // Filtre quantité
        $sql .= " HAVING 1=1";
        $sql .= " AND (p.quantity + COALESCE(allocated_qty, 0)) > 0";
        
        // Sort
        $sort_data = [
            'p.product_id',
            'p.model',
            'pd.name'
        ];
        
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.product_id";
        }
        
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
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
     * Get total products with filters
     */
    public function getTotalProductsFiltered(array $data = []): int {
        $sql = "SELECT COUNT(DISTINCT p.product_id) as total
                FROM " . DB_PREFIX . "product p
                LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = 1)
                WHERE 1=1
                AND p.marketplace_item_id IS NOT NULL
                AND p.marketplace_item_id != ''";
        
        if (!empty($data['filter_product_id'])) {
            $sql .= " AND p.product_id = " . (int)$data['filter_product_id'];
        }
        
        if (!empty($data['filter_name'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
        }
        
        if (!empty($data['filter_model'])) {
            $sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
        }
        
        $conditions = [];
        $exclusions = [];
        
        if (!empty($data['filter_image_issue']) && $data['filter_image_issue'] == '1') {
            $conditions[] = "(JSON_EXTRACT(p.maintenance_data, '\\$.images.has_issues') = 1 AND JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types'), '\\\"missing\\\"'))";
        } elseif (isset($data['filter_image_issue']) && $data['filter_image_issue'] == '0') {
            $exclusions[] = "(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types') IS NULL OR NOT JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types'), '\\\"missing\\\"'))";
        }
        
        if (!empty($data['filter_low_resolution']) && $data['filter_low_resolution'] == '1') {
            $conditions[] = "(JSON_EXTRACT(p.maintenance_data, '\\$.images.has_issues') = 1 AND JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types'), '\\\"low_res\\\"'))";
        } elseif (isset($data['filter_low_resolution']) && $data['filter_low_resolution'] == '0') {
            $exclusions[] = "(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types') IS NULL OR NOT JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types'), '\\\"low_res\\\"'))";
        }
        
        if (!empty($data['filter_wrong_path']) && $data['filter_wrong_path'] == '1') {
            $conditions[] = "(JSON_EXTRACT(p.maintenance_data, '\\$.images.has_issues') = 1 AND JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types'), '\\\"wrong_path\\\"'))";
        } elseif (isset($data['filter_wrong_path']) && $data['filter_wrong_path'] == '0') {
            $exclusions[] = "(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types') IS NULL OR NOT JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types'), '\\\"wrong_path\\\"'))";
        }
        
        if (!empty($data['filter_old_nomenclature']) && $data['filter_old_nomenclature'] == '1') {
            $conditions[] = "(JSON_EXTRACT(p.maintenance_data, '\\$.images.has_issues') = 1 AND JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types'), '\\\"old_nomenclature\\\"'))";
        } elseif (isset($data['filter_old_nomenclature']) && $data['filter_old_nomenclature'] == '0') {
            $exclusions[] = "(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types') IS NULL OR NOT JSON_CONTAINS(JSON_EXTRACT(p.maintenance_data, '\\$.images.issue_types'), '\\\"old_nomenclature\\\"'))";
        }
        
        if (!empty($data['filter_orphan_images']) && $data['filter_orphan_images'] == '1') {
            $conditions[] = "(COALESCE(CAST(JSON_UNQUOTE(JSON_EXTRACT(p.maintenance_data, '\\$.images.orphan_count')) AS UNSIGNED), 0) > 0)";
        } elseif (isset($data['filter_orphan_images']) && $data['filter_orphan_images'] == '0') {
            $exclusions[] = "(COALESCE(CAST(JSON_UNQUOTE(JSON_EXTRACT(p.maintenance_data, '\\$.images.orphan_count')) AS UNSIGNED), 0) <= 0)";
        }

        if (!empty($conditions)) {
            $sql .= " AND (" . implode(" OR ", $conditions) . ")";
        } else {
            // If no specific filter, show only products with issues by default
            $has_specific_filters = !empty($data['filter_product_id']) || !empty($data['filter_name']) || !empty($data['filter_model']);
            if (!$has_specific_filters) {
                $sql .= " AND JSON_EXTRACT(p.maintenance_data, '\\$.images.has_issues') = 1";
            }
        }
        
        // Apply exclusions for unchecked filters
        if (!empty($exclusions)) {
            $sql .= " AND (" . implode(" AND ", $exclusions) . ")";
        }

        $sql .= " AND (p.quantity + COALESCE((SELECT SUM(quantity) FROM " . DB_PREFIX . "order_product op 
                    LEFT JOIN `" . DB_PREFIX . "order` o ON op.order_id = o.order_id 
                    WHERE op.product_id = p.product_id AND o.order_status_id IN (1,2,3,12,15)), 0)) > 0";
        
        $query = $this->db->query($sql);
        return (int)$query->row['total'];
    }
    
    /**
     * Update product main image
     */
    public function updateMainImage(int $product_id, string $image_path): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "product` SET `image` = '" . $this->db->escape($image_path) . "' WHERE product_id = " . (int)$product_id);
    }
    
    /**
     * Delete secondary image
     */
    public function deleteSecondaryImage(int $product_id, string $image_path): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE product_id = " . (int)$product_id . " AND `image` = '" . $this->db->escape($image_path) . "'");
    }
    
    /**
     * Add secondary image
     */
    public function addSecondaryImage(int $product_id, string $image_path): void {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "product_image` SET `product_id` = " . (int)$product_id . ", `image` = '" . $this->db->escape($image_path) . "', `sort_order` = 0");
    }
    
    /**
     * Invalidate maintenance cache for a product (forces re-validation on next view)
     */
    public function invalidateMaintenanceCache(int $product_id): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "product` SET maintenance_data = NULL WHERE product_id = " . (int)$product_id);
    }

    /**
     * Returns all products with an OC vs eBay image count mismatch.
     * Each row: product_id, oc_image_count, ebay_count
     */
    public function getImageMismatchList(): array {
        $result = $this->db->query("
            SELECT product_id, oc_image_count, ebay_count
            FROM (
                SELECT p.product_id,
                    (CASE WHEN (p.image IS NOT NULL AND p.image != '' AND p.image != 'no_image.png') THEN 1 ELSE 0 END
                     + (SELECT COUNT(*) FROM " . DB_PREFIX . "product_image pi WHERE pi.product_id = p.product_id)) AS oc_image_count,
                    pm.ebay_image_count AS ebay_count
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_marketplace pm ON pm.product_id = p.product_id
                WHERE pm.marketplace_id = 1
                  AND pm.is_com = 0
                  AND pm.ebay_image_count > 0
                HAVING oc_image_count != ebay_count
            ) AS mismatch_list"
        );
        return $result->rows;
    }
}
