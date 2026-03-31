<?php
namespace Opencart\Admin\Model\Shopmanager\Inventory;

/**
 * Class Sync
 *
 * Model for Inventory Sync & Issues Dashboard
 *
 * @package Opencart\Admin\Model\Shopmanager\Inventory
 */
class Sync extends \Opencart\System\Engine\Model {

    /**
     * Get Overview - Métriques générales de vue d'ensemble
     *
     * @param string $period
     * @return array
     */
    public function getOverview(string $period): array {
        $date_filter = $this->getDateFilter($period);
        
        $data = [];

        // Total Products
        $data['total_products'] = $this->getTotalProducts();
        
        // Active Products
        $query = $this->db->query("SELECT COUNT(*) as total FROM " . DB_PREFIX . "product WHERE status = 1");
        $data['active_products'] = (int)$query->row['total'];

        // Total Inventory Value
        $query = $this->db->query("
            SELECT SUM(p.quantity * p.price) as total_value 
            FROM " . DB_PREFIX . "product p 
            WHERE p.status = 1
        ");
        $data['inventory_value'] = round((float)$query->row['total_value'], 2);

        // Orders This Period
        $query = $this->db->query("
            SELECT COUNT(*) as total, SUM(total) as revenue
            FROM " . DB_PREFIX . "order 
            WHERE date_added >= '{$date_filter}'
            AND order_status_id > 0
        ");
        $data['orders_count'] = (int)$query->row['total'];
        $data['revenue'] = round((float)$query->row['revenue'], 2);

        // Average Order Value
        if ($data['orders_count'] > 0) {
            $data['avg_order_value'] = round($data['revenue'] / $data['orders_count'], 2);
        } else {
            $data['avg_order_value'] = 0;
        }

        // Products Added This Period
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE date_added >= '{$date_filter}'
        ");
        $data['new_products'] = (int)$query->row['total'];

        return $data;
    }

    /**
     * Get Inventory Health - Santé de l'inventaire
     *
     * @return array
     */
    public function getInventoryHealth(): array {
        $data = [];

        // Low Stock (quantity < 5)
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE quantity < 5 AND quantity > 0 AND status = 1
        ");
        $data['low_stock'] = (int)$query->row['total'];

        // Out of Stock
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE quantity = 0 AND status = 1
        ");
        $data['out_of_stock'] = (int)$query->row['total'];

        // Unallocated Inventory
        $query = $this->db->query("
            SELECT SUM(unallocated_quantity) as total 
            FROM " . DB_PREFIX . "product 
            WHERE status = 1
        ");
        $data['unallocated'] = (int)$query->row['total'];

        // Products Without Location
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE (location = '' OR location IS NULL) AND quantity > 0 AND status = 1
        ");
        $data['without_location'] = (int)$query->row['total'];

        // Products Without Images
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE (image = '' OR image IS NULL OR image = 'no_image.png') AND status = 1
        ");
        $data['without_image'] = (int)$query->row['total'];

        // Average Stock Level
        $query = $this->db->query("
            SELECT AVG(quantity) as avg_qty 
            FROM " . DB_PREFIX . "product 
            WHERE status = 1
        ");
        $data['avg_stock_level'] = round((float)$query->row['avg_qty'], 2);

        return $data;
    }

    /**
     * Get Sales Performance - Performance des ventes
     *
     * @param string $period
     * @return array
     */
    public function getSalesPerformance(string $period): array {
        $date_filter = $this->getDateFilter($period);
        
        $data = [];

        // Sales by Status
        $query = $this->db->query("
            SELECT 
                os.name as status_name,
                COUNT(o.order_id) as count,
                SUM(o.total) as total
            FROM " . DB_PREFIX . "order o
            LEFT JOIN " . DB_PREFIX . "order_status os ON (o.order_status_id = os.order_status_id)
            WHERE o.date_added >= '{$date_filter}'
            AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'
            GROUP BY o.order_status_id
            ORDER BY count DESC
        ");
        $data['by_status'] = $query->rows;

        // Sales by Day (for charts)
        $query = $this->db->query("
            SELECT 
                DATE(date_added) as date,
                COUNT(*) as orders,
                SUM(total) as revenue
            FROM " . DB_PREFIX . "order
            WHERE date_added >= '{$date_filter}'
            AND order_status_id > 0
            GROUP BY DATE(date_added)
            ORDER BY date ASC
        ");
        $data['by_day'] = $query->rows;

        // Completed Orders
        $query = $this->db->query("
            SELECT COUNT(*) as total, SUM(total) as revenue
            FROM " . DB_PREFIX . "order
            WHERE date_added >= '{$date_filter}'
            AND order_status_id = 5
        ");
        $data['completed_orders'] = (int)$query->row['total'];
        $data['completed_revenue'] = round((float)$query->row['revenue'], 2);

        return $data;
    }

    /**
     * Get Marketplace Performance - Performance sur les marketplaces
     *
     * @param string $period
     * @return array
     */
    public function getMarketplacePerformance(string $period): array {
        $data = [];

        // Products Listed on Marketplace (using product_marketplace table)
        $query = $this->db->query("
            SELECT COUNT(DISTINCT pm.product_id) as total 
            FROM " . DB_PREFIX . "product_marketplace pm
            INNER JOIN " . DB_PREFIX . "product p ON (p.product_id = pm.product_id)
            WHERE p.status = 1
            AND pm.marketplace_item_id IS NOT NULL
            AND pm.marketplace_item_id != ''
            AND pm.marketplace_item_id != '0'
        ");
        $data['ebay_listed'] = (int)$query->row['total'];

        // Products Ready to List (has images, price, description but not listed yet)
        $query = $this->db->query("
            SELECT COUNT(DISTINCT p.product_id) as total 
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE p.status = 1
            AND p.quantity > 0
            AND p.image IS NOT NULL 
            AND p.image != ''
            AND p.image != 'no_image.png'
            AND p.price > 0
            AND pd.description IS NOT NULL
            AND pd.description != ''
            AND (pm.marketplace_item_id IS NULL OR pm.marketplace_item_id = '' OR pm.marketplace_item_id = '0')
        ");
        $data['ready_to_list'] = (int)$query->row['total'];

        // Products With Marketplace Errors (stored in product_marketplace.error column)
        $query = $this->db->query("
            SELECT COUNT(DISTINCT pm.product_id) as total 
            FROM " . DB_PREFIX . "product_marketplace pm
            WHERE pm.error IS NOT NULL 
            AND pm.error != ''
        ");
        $data['with_errors'] = (int)$query->row['total'];

        // Average Listing Price
        $query = $this->db->query("
            SELECT AVG(pm.price) as avg_price 
            FROM " . DB_PREFIX . "product_marketplace pm
            INNER JOIN " . DB_PREFIX . "product p ON (p.product_id = pm.product_id)
            WHERE p.status = 1
            AND pm.marketplace_item_id IS NOT NULL
            AND pm.marketplace_item_id != ''
            AND pm.marketplace_item_id != '0'
            AND pm.price IS NOT NULL
            AND pm.price > 0
        ");
        $data['avg_listing_price'] = round((float)$query->row['avg_price'], 2);

        return $data;
    }

    /**
     * Get Top Products - Produits les plus vendus
     *
     * @param string $period
     * @param int $limit
     * @return array
     */
    public function getTopProducts(string $period, int $limit = 10): array {
        $date_filter = $this->getDateFilter($period);

        $query = $this->db->query("
            SELECT 
                p.product_id,
                p.sku,
                p.price,
                p.quantity,
                pd.name,
                SUM(op.quantity) as units_sold,
                SUM(op.total) as revenue
            FROM " . DB_PREFIX . "order_product op
            LEFT JOIN " . DB_PREFIX . "product p ON (op.product_id = p.product_id)
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id)
            WHERE o.date_added >= '{$date_filter}'
            AND o.order_status_id > 0
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            GROUP BY op.product_id
            ORDER BY revenue DESC
            LIMIT " . (int)$limit . "
        ");

        return $query->rows;
    }

    /**
     * Get Bottom Products - Produits les moins performants
     *
     * @param string $period
     * @param int $limit
     * @return array
     */
    public function getBottomProducts(string $period, int $limit = 10): array {
        $date_filter = $this->getDateFilter($period);

        $query = $this->db->query("
            SELECT 
                p.product_id,
                p.sku,
                p.price,
                p.quantity,
                pm.marketplace_item_id,
                CASE 
                    WHEN pm.last_sync IS NULL OR pm.last_sync = '0000-00-00 00:00:00' THEN NULL
                    ELSE pm.last_sync 
                END as last_sync,
                pd.name,
                COALESCE(SUM(op.quantity), 0) as units_sold
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            LEFT JOIN " . DB_PREFIX . "order_product op ON (p.product_id = op.product_id)
            LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id AND o.date_added >= '{$date_filter}')
            WHERE p.status = 1
            AND p.quantity > 0
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            GROUP BY p.product_id
            ORDER BY units_sold ASC, 
                     CASE WHEN pm.last_sync IS NULL OR pm.last_sync = '0000-00-00 00:00:00' THEN 1 ELSE 0 END DESC,
                     pm.last_sync ASC
            LIMIT " . (int)$limit . "
        ");

        return $query->rows;
    }

    /**
     * Get Products with Marketplace Errors
     *
     * @return array
     */
    public function getProductsWithErrors(): array {
        $query = $this->db->query("
            SELECT 
                p.product_id,
                p.sku,
                p.quantity,
                p.unallocated_quantity,
                pd.name,
                pm.marketplace_item_id,
                pm.error,
                pm.last_sync
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.error IS NOT NULL 
            AND pm.error != ''
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY pm.last_sync DESC
        ");

        // Parse JSON errors to extract useful info
        $results = [];
        foreach ($query->rows as $row) {
            $row['parsed_errors'] = $this->parseEbayError($row['error']);
            // Ne garder que les produits avec des erreurs JSON valides
            if (!empty($row['parsed_errors'])) {
                $results[] = $row;
            }
        }

        return $results;
    }

    /**
     * Parse eBay error JSON and extract key information
     *
     * @param string $error_json
     * @return array
     */
    private function parseEbayError(string $error_json): array {
        $parsed = [];
        
        try {
            $error_data = json_decode($error_json, true);
            
            // Si pas de JSON valide ou pas de Errors, retourner array vide
            if (!$error_data || !isset($error_data['Errors'])) {
                return [];
            }
            
            // Errors peut être un array ou un objet unique
            $errors = $error_data['Errors'];
            
            // Si c'est un objet unique (pas un array), le convertir en array
            if (!isset($errors[0])) {
                $errors = [$errors];
            }
            
            foreach ($errors as $error) {
                $parsed[] = [
                    'code' => $error['ErrorCode'] ?? 'N/A',
                    'severity' => $error['SeverityCode'] ?? 'Error',
                    'short_message' => $error['ShortMessage'] ?? '',
                    'long_message' => $error['LongMessage'] ?? ''
                ];
            }
        } catch (\Exception $e) {
            // Si erreur de parsing, ne pas afficher le produit
            return [];
        }
        
        return $parsed;
    }

    /**
     * Get Products Not Synced Recently (7+ days)
     *
     * @return array
     */
    public function getProductsNotSynced(): array {
        $query = $this->db->query("
            SELECT 
                p.product_id,
                p.sku,
                p.quantity,
                p.unallocated_quantity,
                pd.name,
                pm.marketplace_item_id,
                pm.last_sync
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE p.quantity > 0
            AND p.status = 1
            AND pm.marketplace_id = 1
            AND pm.marketplace_item_id IS NOT NULL
            AND pm.marketplace_item_id != ''
            AND (pm.last_sync IS NULL OR pm.last_sync < DATE_SUB(NOW(), INTERVAL 7 DAY))
            AND (pm.last_import IS NULL OR pm.last_import < DATE_SUB(NOW(), INTERVAL 7 DAY))
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY pm.last_sync ASC
        ");

        return $query->rows;
    }

    /**
     * Get Products NOT Listed on eBay (but have quantity + unallocated > 0)
     * Products with available stock but NO entry in product_marketplace
     *
     * @return array
     */
    public function getProductsNotListed(): array {
        $query = $this->db->query("
            SELECT 
                p.product_id,
                p.sku,
                p.quantity,
                p.unallocated_quantity,
                (p.quantity + p.unallocated_quantity) as total_available,
                pd.name
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE (p.quantity + p.unallocated_quantity) > 0
            AND pm.product_id IS NULL
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            ORDER BY (p.quantity + p.unallocated_quantity) DESC
        ");

        return $query->rows;
    }

    /**
     * Get Total Products Listed on eBay
     *
     * @return int
     */
    public function getTotalListedOnEbay(): int {
        $query = $this->db->query("
            SELECT COUNT(DISTINCT pm.product_id) as total
            FROM " . DB_PREFIX . "product_marketplace pm
            INNER JOIN " . DB_PREFIX . "product p ON (p.product_id = pm.product_id)
            WHERE pm.is_com = 0
            AND pm.marketplace_id = 1
        ");
        return (int)$query->row['total'];
    }

    /**
     * Get Products with Quantity Mismatch
     * Compare eBay quantity_listed vs (product.quantity + product.unallocated_quantity)
     *
     * @return array
     */
    public function getQuantityMismatch(array $data = []): array {
        // Get mismatches from phoenixliquidation database ONLY (is_com = 0)
        $sql = "
            SELECT 
                p.product_id,
                p.sku,
                p.price as local_price,
                p.price_usd as local_price_usd,
                p.quantity,
                p.unallocated_quantity,
                p.location,
                (p.quantity + p.unallocated_quantity) as total_quantity,
                pd.name,
                pd.specifics as local_specifics,
                pm.marketplace_item_id,
                pm.price as ebay_price,
                pm.quantity_listed,
                pm.quantity_sold,
                pm.specifics as ebay_specifics,
                pm.is_com,
                (pm.quantity_listed - pm.quantity_sold) as ebay_available
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND (pm.quantity_listed - pm.quantity_sold) != (p.quantity + p.unallocated_quantity)
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
        ";
        
        // Sorting
        $sort = $data['sort'] ?? 'product_id';
        $order = $data['order'] ?? 'ASC';
        
        $allowed_sorts = ['product_id', 'name', 'location'];
        if (!in_array($sort, $allowed_sorts)) {
            $sort = 'product_id';
        }
        
        if ($order != 'ASC' && $order != 'DESC') {
            $order = 'ASC';
        }
        
        if ($sort == 'product_id') {
            $sql .= " ORDER BY p.product_id " . $order;
        } elseif ($sort == 'name') {
            $sql .= " ORDER BY pd.name " . $order;
        } else {
            $sql .= " ORDER BY p.location " . $order;
        }
        
        // Pagination
        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);

        // Add comparison flags for each row
        $results = [];
        foreach ($query->rows as $row) {
            // Compare local CAD price with eBay CAD price (both in CAD now)
            $local_price_cad = (float)$row['local_price'];
            $ebay_price_cad = (float)$row['ebay_price'];
            
            // Price difference - compare CAD to CAD
            $row['price_diff'] = abs($local_price_cad - $ebay_price_cad) > 0.01;
            
            // Quantity difference
            $row['qty_diff'] = ((int)$row['quantity'] + (int)$row['unallocated_quantity']) != (int)$row['ebay_available'];
            
            // Specifics difference
            $row['specifics_diff'] = false;
            if (!empty($row['local_specifics']) && !empty($row['ebay_specifics'])) {
                $local_spec = json_decode($row['local_specifics'], true);
                $ebay_spec = json_decode($row['ebay_specifics'], true);
                $row['specifics_diff'] = json_encode($local_spec) !== json_encode($ebay_spec);
            } elseif (!empty($row['local_specifics']) || !empty($row['ebay_specifics'])) {
                $row['specifics_diff'] = true;
            }
            
            $results[] = $row;
        }

        return $results;
    }

    /**
     * Get Products with Price Mismatch
     * Compare eBay price vs local price (both in CAD)
     *
     * @param array $data Pagination and sorting parameters
     * @return array
     */
    public function getPriceMismatch(array $data = []): array {
        $sql = "
            SELECT 
                p.product_id,
                p.sku,
                p.price as local_price,
                p.price_usd as local_price_usd,
                p.quantity,
                p.unallocated_quantity,
                p.location,
                (p.quantity + p.unallocated_quantity) as total_quantity,
                pd.name,
                pd.specifics as local_specifics,
                pm.marketplace_item_id,
                pm.price as ebay_price,
                pm.quantity_listed,
                pm.quantity_sold,
                pm.specifics as ebay_specifics,
                pm.is_com,
                (pm.quantity_listed - pm.quantity_sold) as ebay_available
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND ABS(p.price - pm.price) > 0.01
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
        ";
        
        // Sorting
        $sort = $data['sort'] ?? 'product_id';
        $order = $data['order'] ?? 'ASC';
        
        $allowed_sorts = ['product_id', 'name', 'location'];
        if (!in_array($sort, $allowed_sorts)) {
            $sort = 'product_id';
        }
        
        if ($order != 'ASC' && $order != 'DESC') {
            $order = 'ASC';
        }
        
        if ($sort == 'product_id') {
            $sql .= " ORDER BY p.product_id " . $order;
        } elseif ($sort == 'name') {
            $sql .= " ORDER BY pd.name " . $order;
        } else {
            $sql .= " ORDER BY p.location " . $order;
        }
        
        // Pagination
        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);

        $results = [];
        foreach ($query->rows as $row) {
            $local_price_cad = (float)$row['local_price'];
            $ebay_price_cad = (float)$row['ebay_price'];
            
            $row['price_diff'] = abs($local_price_cad - $ebay_price_cad) > 0.01;
            $row['qty_diff'] = ((int)$row['quantity'] + (int)$row['unallocated_quantity']) != (int)$row['ebay_available'];
            
            $row['specifics_diff'] = false;
            if (!empty($row['local_specifics']) && !empty($row['ebay_specifics'])) {
                $local_spec = json_decode($row['local_specifics'], true);
                $ebay_spec = json_decode($row['ebay_specifics'], true);
                $row['specifics_diff'] = json_encode($local_spec) !== json_encode($ebay_spec);
            } elseif (!empty($row['local_specifics']) || !empty($row['ebay_specifics'])) {
                $row['specifics_diff'] = true;
            }
            
            $results[] = $row;
        }

        return $results;
    }

    /**
     * Get Products with Specifics Mismatch
     * Compare eBay specifics vs local specifics
     *
     * @param array $data Pagination and sorting parameters
     * @return array
     */
    public function getSpecificsMismatch(array $data = []): array {
        $sql = "
            SELECT 
                p.product_id,
                p.sku,
                p.price as local_price,
                p.price_usd as local_price_usd,
                p.quantity,
                p.unallocated_quantity,
                p.location,
                (p.quantity + p.unallocated_quantity) as total_quantity,
                pd.name,
                pd.specifics as local_specifics,
                pm.marketplace_item_id,
                pm.price as ebay_price,
                pm.quantity_listed,
                pm.quantity_sold,
                pm.specifics as ebay_specifics,
                pm.is_com,
                (pm.quantity_listed - pm.quantity_sold) as ebay_available
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            AND (
                (pd.specifics IS NOT NULL AND pm.specifics IS NOT NULL AND pd.specifics != pm.specifics)
                OR (pd.specifics IS NULL AND pm.specifics IS NOT NULL)
                OR (pd.specifics IS NOT NULL AND pm.specifics IS NULL)
            )
        ";
        
        // Sorting
        $sort = $data['sort'] ?? 'product_id';
        $order = $data['order'] ?? 'ASC';
        
        $allowed_sorts = ['product_id', 'name', 'location'];
        if (!in_array($sort, $allowed_sorts)) {
            $sort = 'product_id';
        }
        
        if ($order != 'ASC' && $order != 'DESC') {
            $order = 'ASC';
        }
        
        if ($sort == 'product_id') {
            $sql .= " ORDER BY p.product_id " . $order;
        } elseif ($sort == 'name') {
            $sql .= " ORDER BY pd.name " . $order;
        } else {
            $sql .= " ORDER BY p.location " . $order;
        }
        
        // Pagination
        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);

        $results = [];
        foreach ($query->rows as $row) {
            $local_price_cad = (float)$row['local_price'];
            $ebay_price_cad = (float)$row['ebay_price'];
            
            $row['price_diff'] = abs($local_price_cad - $ebay_price_cad) > 0.01;
            $row['qty_diff'] = ((int)$row['quantity'] + (int)$row['unallocated_quantity']) != (int)$row['ebay_available'];
            
            // Validate specifics: check if all local values are present in eBay
            $row['specifics_diff'] = false;
            $row['missing_values'] = [];
            
            if (!empty($row['local_specifics']) && !empty($row['ebay_specifics'])) {
                $local_spec = json_decode($row['local_specifics'], true);
                $ebay_spec = json_decode($row['ebay_specifics'], true);
                
                if (is_array($local_spec) && is_array($ebay_spec)) {
                    // Helper function to flatten array and extract all final string values
                    $flatten_values = function($val) use (&$flatten_values) {
                        if (is_array($val)) {
                            $result = [];
                            foreach ($val as $v) {
                                $result = array_merge($result, $flatten_values($v));
                            }
                            return $result;
                        }
                        return [(string)$val];
                    };
                    
                    // Helper function to convert values to display string
                    $value_to_string = function($val) use (&$value_to_string) {
                        if (is_array($val)) {
                            $parts = [];
                            foreach ($val as $v) {
                                $parts[] = $value_to_string($v);
                            }
                            return implode(', ', $parts);
                        }
                        return (string)$val;
                    };
                    
                    // Check each local specific name and value
                    foreach ($local_spec as $name => $value) {
                        // Skip if no value
                        if (empty($value)) continue;
                        
                        // Check if name exists in eBay specifics
                        if (!isset($ebay_spec[$name])) {
                            $row['specifics_diff'] = true;
                            $row['missing_values'][] = $value_to_string($name) . ": " . $value_to_string($value) . " (name missing on eBay)";
                        } else {
                            // Flatten both local and eBay values to get final string values
                            $local_values = $flatten_values($value);
                            $ebay_values = $flatten_values($ebay_spec[$name]);
                            
                            // Check if all local values are present in eBay values
                            foreach ($local_values as $local_val) {
                                if (!in_array($local_val, $ebay_values)) {
                                    $row['specifics_diff'] = true;
                                    $row['missing_values'][] = $value_to_string($name) . ": " . $local_val;
                                }
                            }
                        }
                    }
                }
            } elseif (!empty($row['local_specifics'])) {
                // Local has specifics but eBay doesn't
                $row['specifics_diff'] = true;
                $row['missing_values'][] = "All local specifics missing on eBay";
            } elseif (!empty($row['ebay_specifics'])) {
                // eBay has specifics but local doesn't (inverse case - might be OK)
                $row['specifics_diff'] = false;
            }
            
            $results[] = $row;
        }

        return $results;
    }

    /**
     * Get Alerts - Alertes et avertissements (simplifié)
     *
     * @return array
     */
    public function getAlerts(): array {
        $alerts = [];

        // Products Out of Date (not synced in last 7 days)
        $query = $this->db->query("
            SELECT COUNT(DISTINCT pm.product_id) as total 
            FROM " . DB_PREFIX . "product_marketplace pm
            INNER JOIN " . DB_PREFIX . "product p ON (p.product_id = pm.product_id)
            WHERE p.quantity > 0
            AND p.status = 1
            AND (pm.last_sync IS NULL OR pm.last_sync < DATE_SUB(NOW(), INTERVAL 7 DAY))
            AND (pm.last_import IS NULL OR pm.last_import < DATE_SUB(NOW(), INTERVAL 7 DAY))
        ");
        if ($query->row['total'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => $query->row['total'] . ' products not synced in 7+ days (out of date)',
                'count' => $query->row['total']
            ];
        }

        // Products Without Images
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE (image = '' OR image IS NULL OR image = 'no_image.png') 
            AND status = 1 
            AND quantity > 0
        ");
        if ($query->row['total'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => $query->row['total'] . ' products without images',
                'count' => $query->row['total']
            ];
        }

        // Products Without Location
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE (location = '' OR location IS NULL) 
            AND quantity > 0 
            AND status = 1
        ");
        if ($query->row['total'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => $query->row['total'] . ' products without location',
                'count' => $query->row['total']
            ];
        }

        // Marketplace Errors (from product_marketplace.error column)
        $query = $this->db->query("
            SELECT COUNT(DISTINCT pm.product_id) as total 
            FROM " . DB_PREFIX . "product_marketplace pm
            WHERE pm.error IS NOT NULL 
            AND pm.error != ''
        ");
        if ($query->row['total'] > 0) {
            $alerts[] = [
                'type' => 'danger',
                'message' => $query->row['total'] . ' products with marketplace errors',
                'count' => $query->row['total']
            ];
        }

        return $alerts;
    }

    /**
     * Get Category Performance - Performance par catégorie
     *
     * @param string $period
     * @return array
     */
    public function getCategoryPerformance(string $period): array {
        $date_filter = $this->getDateFilter($period);

        $query = $this->db->query("
            SELECT 
                cd.name as category_name,
                COUNT(DISTINCT p.product_id) as product_count,
                SUM(p.quantity) as total_stock,
                COALESCE(SUM(op.quantity), 0) as units_sold,
                COALESCE(SUM(op.total), 0) as revenue
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id)
            LEFT JOIN " . DB_PREFIX . "category_description cd ON (ptc.category_id = cd.category_id)
            LEFT JOIN " . DB_PREFIX . "order_product op ON (p.product_id = op.product_id)
            LEFT JOIN " . DB_PREFIX . "order o ON (op.order_id = o.order_id AND o.date_added >= '{$date_filter}')
            WHERE p.status = 1
            AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            GROUP BY p.product_id, cd.category_id
            ORDER BY revenue DESC
            LIMIT 15
        ");

        return $query->rows;
    }

    /**
     * Get Location Analysis - Analyse par emplacement
     *
     * @return array
     */
    public function getLocationAnalysis(): array {
        $query = $this->db->query("
            SELECT 
                CASE 
                    WHEN location = '' OR location IS NULL THEN 'No Location'
                    ELSE location 
                END as location,
                COUNT(*) as product_count,
                SUM(quantity) as total_quantity,
                SUM(quantity * price) as total_value
            FROM " . DB_PREFIX . "product
            WHERE status = 1
            GROUP BY location
            ORDER BY total_value DESC
            LIMIT 20
        ");

        return $query->rows;
    }

    /**
     * Get Trend Data - Données de tendance pour les graphiques
     *
     * @param string $period
     * @return array
     */
    public function getTrendData(string $period): array {
        $date_filter = $this->getDateFilter($period);
        $data = [];

        // Revenue Trend
        $query = $this->db->query("
            SELECT 
                DATE(date_added) as date,
                SUM(total) as revenue
            FROM " . DB_PREFIX . "order
            WHERE date_added >= '{$date_filter}'
            AND order_status_id > 0
            GROUP BY DATE(date_added)
            ORDER BY date ASC
        ");
        $data['revenue'] = $query->rows;

        // Orders Trend
        $query = $this->db->query("
            SELECT 
                DATE(date_added) as date,
                COUNT(*) as orders
            FROM " . DB_PREFIX . "order
            WHERE date_added >= '{$date_filter}'
            AND order_status_id > 0
            GROUP BY DATE(date_added)
            ORDER BY date ASC
        ");
        $data['orders'] = $query->rows;

        return $data;
    }

    /**
     * Get Total Products
     *
     * @return int
     */
    public function getTotalProducts(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE quantity > 0
        ");
        return (int)$query->row['total'];
    }

    /**
     * Get Low Stock Count
     *
     * @return int
     */
    public function getLowStockCount(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "product 
            WHERE quantity < 5 AND quantity > 0 AND status = 1
        ");
        return (int)$query->row['total'];
    }

    /**
     * Get Pending Orders
     *
     * @return int
     */
    public function getPendingOrders(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total 
            FROM " . DB_PREFIX . "order 
            WHERE order_status_id = 1
        ");
        return (int)$query->row['total'];
    }

    /**
     * Get Todays Revenue
     *
     * @return float
     */
    public function getTodaysRevenue(): float {
        $query = $this->db->query("
            SELECT SUM(total) as revenue 
            FROM " . DB_PREFIX . "order 
            WHERE DATE(date_added) = CURDATE()
            AND order_status_id > 0
        ");
        return round((float)$query->row['revenue'], 2);
    }

    /**
     * Get Date Filter - Calcule la date de début selon la période
     *
     * @param string $period
     * @return string
     */
    private function getDateFilter(string $period): string {
        switch ($period) {
            case 'today':
                return date('Y-m-d 00:00:00');
            case 'week':
                return date('Y-m-d 00:00:00', strtotime('-7 days'));
            case 'month':
                return date('Y-m-d 00:00:00', strtotime('-30 days'));
            case 'year':
                return date('Y-m-d 00:00:00', strtotime('-365 days'));
            default:
                return date('Y-m-d 00:00:00', strtotime('-30 days'));
        }
    }

    /**
     * Check if a column exists in a table
     *
     * @param string $table
     * @param string $column
     * @return bool
     */
    private function checkColumnExists(string $table, string $column): bool {
        try {
            $query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . $table . "` LIKE '" . $this->db->escape($column) . "'");
            return $query->num_rows > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if a table exists
     *
     * @param string $table
     * @return bool
     */
    private function checkTableExists(string $table): bool {
        try {
            $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $this->db->escape($table) . "'");
            return $query->num_rows > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get Marketplace Sync Stats - Statistiques de synchronisation marketplace
     *
     * @return array
     */
    public function getMarketplaceSyncStats(): array {
        $data = [];

        // Last sync time
        $query = $this->db->query("
            SELECT MAX(date_modified) as last_sync
            FROM " . DB_PREFIX . "product_marketplace
        ");
        $data['last_sync'] = $query->row['last_sync'] ?? null;

        // Products needing sync (price or quantity changed)
        $query = $this->db->query("
            SELECT COUNT(DISTINCT p.product_id) as total
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE p.status = 1
            AND pm.marketplace_item_id IS NOT NULL
            AND pm.marketplace_item_id != ''
            AND pm.marketplace_item_id != '0'
            AND (
                p.price != pm.price
                OR p.quantity != pm.quantity_listed
            )
        ");
        $data['needs_sync'] = (int)$query->row['total'];

        return $data;
    }

    /**
     * Get error summary - count of each error type
     *
     * @return array
     */
    public function getErrorSummary() {
        $sql = "SELECT pm.error
                FROM " . DB_PREFIX . "product_marketplace pm
                WHERE pm.error IS NOT NULL 
                AND pm.error != ''";
        
        $result = $this->db->query($sql);
        
        $errorCounts = [];
        
        foreach ($result->rows as $row) {
            $parsedErrors = $this->parseEbayError($row['error']);
            // Ne compter que les erreurs JSON valides
            if (!empty($parsedErrors)) {
                foreach ($parsedErrors as $error) {
                    $key = $error['code'] . ' - ' . $error['short_message'];
                    if (!isset($errorCounts[$key])) {
                        $errorCounts[$key] = [
                            'code' => $error['code'],
                            'message' => $error['short_message'],
                            'severity' => $error['severity'],
                            'count' => 0
                        ];
                    }
                    $errorCounts[$key]['count']++;
                }
            }
        }
        
        // Sort by count descending
        uasort($errorCounts, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return array_values($errorCounts);
    }
    
    /**
     * Get total count of price mismatches
     *
     * @return int
     */
    public function getTotalPriceMismatch(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND ABS(p.price - pm.price) > 0.01
        ");
        
        return (int)$query->row['total'];
    }
    
    /**
     * Get total count of quantity mismatches
     *
     * @return int
     */
    public function getTotalQuantityMismatch(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND (pm.quantity_listed - pm.quantity_sold) != (p.quantity + p.unallocated_quantity)
        ");
        
        return (int)$query->row['total'];
    }
    
    /**
     * Get total count of specifics mismatches
     *
     * @return int
     */
    public function getTotalSpecificsMismatch(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            AND (
                (pd.specifics IS NOT NULL AND pm.specifics IS NOT NULL AND pd.specifics != pm.specifics)
                OR (pd.specifics IS NULL AND pm.specifics IS NOT NULL)
                OR (pd.specifics IS NOT NULL AND pm.specifics IS NULL)
            )
        ");
        
        return (int)$query->row['total'];
    }
    
    /**
     * Get Products with Condition Mismatch
     *
     * @param array $data Pagination and sorting parameters
     * @return array
     */
    public function getConditionMismatch(array $data = []): array {
        $sql = "
            SELECT 
                p.product_id,
                p.sku,
                p.location,
                pd.name,
                p.condition_id as local_condition_id,
                pm.marketplace_item_id,
                pm.condition_id as ebay_condition_id
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            AND (
                (p.condition_id IS NOT NULL AND pm.condition_id IS NOT NULL AND p.condition_id != pm.condition_id)
                OR (p.condition_id IS NULL AND pm.condition_id IS NOT NULL)
                OR (p.condition_id IS NOT NULL AND pm.condition_id IS NULL)
            )
        ";
        
        // Sorting
        $sort = $data['sort'] ?? 'product_id';
        $order = $data['order'] ?? 'ASC';
        
        $allowed_sorts = ['product_id', 'name', 'location'];
        if (!in_array($sort, $allowed_sorts)) {
            $sort = 'product_id';
        }
        
        if ($order != 'ASC' && $order != 'DESC') {
            $order = 'ASC';
        }
        
        if ($sort == 'product_id') {
            $sql .= " ORDER BY p.product_id " . $order;
        } elseif ($sort == 'name') {
            $sql .= " ORDER BY pd.name " . $order;
        } else {
            $sql .= " ORDER BY p.location " . $order;
        }
        
        // Pagination
        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }
    
    /**
     * Get total count of condition mismatches
     *
     * @return int
     */
    public function getTotalConditionMismatch(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND (
                (p.condition_id IS NOT NULL AND pm.condition_id IS NOT NULL AND p.condition_id != pm.condition_id)
                OR (p.condition_id IS NULL AND pm.condition_id IS NOT NULL)
                OR (p.condition_id IS NOT NULL AND pm.condition_id IS NULL)
            )
        ");
        
        return (int)$query->row['total'];
    }
    
    /**
     * Get Products with Category Mismatch
     *
     * @param array $data Pagination and sorting parameters
     * @return array
     */
    public function getCategoryMismatch(array $data = []): array {
        $sql = "
            SELECT 
                p.product_id,
                p.sku,
                p.upc,
                p.location,
                pd.name,
                COALESCE(
                    (SELECT pc2.category_id 
                     FROM " . DB_PREFIX . "product_to_category pc2
                     LEFT JOIN " . DB_PREFIX . "category c2 ON pc2.category_id = c2.category_id
                     WHERE pc2.product_id = p.product_id AND c2.leaf = 1
                     LIMIT 1),
                    (SELECT pc3.category_id 
                     FROM " . DB_PREFIX . "product_to_category pc3
                     WHERE pc3.product_id = p.product_id
                     LIMIT 1)
                ) as local_category_id,
                COALESCE(
                    (SELECT c2.leaf 
                     FROM " . DB_PREFIX . "product_to_category pc2
                     LEFT JOIN " . DB_PREFIX . "category c2 ON pc2.category_id = c2.category_id
                     WHERE pc2.product_id = p.product_id AND c2.leaf = 1
                     LIMIT 1),
                    (SELECT c3.leaf 
                     FROM " . DB_PREFIX . "product_to_category pc3
                     LEFT JOIN " . DB_PREFIX . "category c3 ON pc3.category_id = c3.category_id
                     WHERE pc3.product_id = p.product_id
                     LIMIT 1)
                ) as leaf,
                COALESCE(
                    (SELECT cd2.name 
                     FROM " . DB_PREFIX . "product_to_category pc2
                     LEFT JOIN " . DB_PREFIX . "category c2 ON pc2.category_id = c2.category_id
                     LEFT JOIN " . DB_PREFIX . "category_description cd2 ON c2.category_id = cd2.category_id AND cd2.language_id = '" . (int)$this->config->get('config_language_id') . "'
                     WHERE pc2.product_id = p.product_id AND c2.leaf = 1
                     LIMIT 1),
                    (SELECT cd3.name 
                     FROM " . DB_PREFIX . "product_to_category pc3
                     LEFT JOIN " . DB_PREFIX . "category_description cd3 ON pc3.category_id = cd3.category_id AND cd3.language_id = '" . (int)$this->config->get('config_language_id') . "'
                     WHERE pc3.product_id = p.product_id
                     LIMIT 1)
                ) as local_category_name,
                pm.marketplace_item_id,
                pm.category_id as ebay_category_id,
                ecd.name as ebay_category_name
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id)
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            LEFT JOIN " . DB_PREFIX . "category_description ecd ON (pm.category_id = ecd.category_id AND ecd.language_id = '" . (int)$this->config->get('config_language_id') . "')
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'
            GROUP BY p.product_id
            HAVING (
                (local_category_id IS NOT NULL AND pm.category_id IS NOT NULL AND local_category_id != pm.category_id)
                OR (local_category_id IS NULL AND pm.category_id IS NOT NULL)
                OR (local_category_id IS NOT NULL AND pm.category_id IS NULL)
            )
        ";
        
        // Sorting
        $sort = $data['sort'] ?? 'product_id';
        $order = $data['order'] ?? 'ASC';
        
        $allowed_sorts = ['product_id', 'name', 'location'];
        if (!in_array($sort, $allowed_sorts)) {
            $sort = 'product_id';
        }
        
        if ($order != 'ASC' && $order != 'DESC') {
            $order = 'ASC';
        }
        
        if ($sort == 'product_id') {
            $sql .= " ORDER BY p.product_id " . $order;
        } elseif ($sort == 'name') {
            $sql .= " ORDER BY pd.name " . $order;
        } else {
            $sql .= " ORDER BY p.location " . $order;
        }
        
        // Pagination
        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }
    
    /**
     * Get total count of category mismatches
     *
     * @return int
     */
    public function getTotalCategoryMismatch(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total
            FROM (
                SELECT p.product_id
                FROM " . DB_PREFIX . "product p
                LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id)
                LEFT JOIN " . DB_PREFIX . "category c ON (pc.category_id = c.category_id)
                INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
                WHERE pm.marketplace_id = 1
                AND pm.is_com = 0
                AND (
                    (pc.category_id IS NOT NULL AND pm.category_id IS NOT NULL AND pc.category_id != pm.category_id)
                    OR (pc.category_id IS NULL AND pm.category_id IS NOT NULL)
                    OR (pc.category_id IS NOT NULL AND pm.category_id IS NULL)
                )
                GROUP BY p.product_id
            ) as subquery
        ");
        
        return (int)$query->row['total'];
    }

    /**
     * Get products where OC image count differs from eBay image count
     * OC count = primary image (1 if set) + additional images in oc_product_image
     * eBay count = pm.ebay_image_count (populated during sync)
     *
     * @param array $data Sort/pagination options
     * @return array
     */
    public function getImageMismatch(array $data = []): array {
        $sql = "
            SELECT
                p.product_id,
                p.sku,
                p.location,
                pd.name,
                pm.marketplace_item_id,
                pm.ebay_image_count,
                pm.image_backup_count,
                (
                    CASE WHEN (p.image IS NOT NULL AND p.image != '' AND p.image != 'no_image.png') THEN 1 ELSE 0 END
                    + (SELECT COUNT(*) FROM " . DB_PREFIX . "product_image pi WHERE pi.product_id = p.product_id)
                ) AS oc_image_count,
                (
                    (CASE WHEN (p.image IS NOT NULL AND p.image != '' AND p.image != 'no_image.png') THEN 1 ELSE 0 END
                    + (SELECT COUNT(*) FROM " . DB_PREFIX . "product_image pi WHERE pi.product_id = p.product_id))
                    - pm.ebay_image_count
                ) AS image_diff,
                (
                    (CASE WHEN (p.image IS NOT NULL AND p.image != '' AND p.image != 'no_image.png') THEN 1 ELSE 0 END
                    + (SELECT COUNT(*) FROM " . DB_PREFIX . "product_image pi WHERE pi.product_id = p.product_id))
                    - COALESCE(pm.image_backup_count, 0)
                ) AS backup_diff
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND pm.ebay_image_count > 0
            HAVING oc_image_count != pm.ebay_image_count
        ";

        // Sorting
        $sort  = $data['sort']  ?? 'product_id';
        $order = $data['order'] ?? 'ASC';

        $allowed_sorts = ['product_id', 'name', 'location', 'oc_image_count', 'ebay_image_count', 'image_diff', 'backup_diff'];
        if (!in_array($sort, $allowed_sorts)) {
            $sort = 'product_id';
        }
        if ($order != 'ASC' && $order != 'DESC') {
            $order = 'ASC';
        }

        $sort_map = [
            'product_id'       => 'p.product_id',
            'name'             => 'pd.name',
            'location'         => 'p.location',
            'oc_image_count'   => 'oc_image_count',
            'ebay_image_count' => 'pm.ebay_image_count',
            'image_diff'       => 'image_diff',
            'backup_diff'      => 'backup_diff',
        ];
        $sql .= " ORDER BY " . $sort_map[$sort] . " " . $order;

        // Pagination
        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * Get total count of image count mismatches (OC vs eBay)
     *
     * @return int
     */
    public function getTotalImageMismatch(): int {
        $query = $this->db->query("
            SELECT COUNT(*) as total
            FROM (
                SELECT p.product_id,
                    (
                        CASE WHEN (p.image IS NOT NULL AND p.image != '' AND p.image != 'no_image.png') THEN 1 ELSE 0 END
                        + (SELECT COUNT(*) FROM " . DB_PREFIX . "product_image pi WHERE pi.product_id = p.product_id)
                    ) AS oc_image_count,
                    pm.ebay_image_count AS ebay_count,
                    pm.image_backup_count AS backup_count
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
                WHERE pm.marketplace_id = 1
                AND pm.is_com = 0
                AND pm.ebay_image_count > 0
                HAVING oc_image_count != ebay_count
            ) as subquery
        ");
        return (int)$query->row['total'];
    }

    /**
     * Get products where backup has MORE images than OC.
     * Only shown when image_backup_count > oc_image_count (images in backup not yet in OC).
     */
    public function getImageBackupMismatch(array $data = []): array {
        $lang_id = (int)$this->config->get('config_language_id');
        $sql = "
            SELECT
                p.product_id,
                p.sku,
                p.location,
                pd.name,
                pm.image_backup_count,
                (
                    CASE WHEN (p.image IS NOT NULL AND p.image != '' AND p.image != 'no_image.png') THEN 1 ELSE 0 END
                    + (SELECT COUNT(*) FROM " . DB_PREFIX . "product_image pi WHERE pi.product_id = p.product_id)
                ) AS oc_image_count,
                (
                    pm.image_backup_count
                    - (CASE WHEN (p.image IS NOT NULL AND p.image != '' AND p.image != 'no_image.png') THEN 1 ELSE 0 END
                       + (SELECT COUNT(*) FROM " . DB_PREFIX . "product_image pi2 WHERE pi2.product_id = p.product_id))
                ) AS backup_extra
            FROM " . DB_PREFIX . "product p
            INNER JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . $lang_id . "')
            INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
            WHERE pm.marketplace_id = 1
            AND pm.is_com = 0
            AND pm.image_backup_count IS NOT NULL
            HAVING pm.image_backup_count > oc_image_count
        ";

        $sort  = $data['sort']  ?? 'product_id';
        $order = $data['order'] ?? 'ASC';
        $allowed_sorts = ['product_id', 'name', 'location', 'oc_image_count', 'image_backup_count', 'backup_extra'];
        if (!in_array($sort, $allowed_sorts)) $sort = 'product_id';
        if ($order != 'ASC' && $order != 'DESC') $order = 'ASC';
        $sort_map = [
            'product_id'         => 'p.product_id',
            'name'               => 'pd.name',
            'location'           => 'p.location',
            'oc_image_count'     => 'oc_image_count',
            'image_backup_count' => 'pm.image_backup_count',
            'backup_extra'       => 'backup_extra',
        ];
        $sql .= " ORDER BY " . $sort_map[$sort] . " " . $order;

        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }
        return $this->db->query($sql)->rows;
    }

    /**
     * Get total count of products where backup > OC.
     */
    public function getTotalImageBackupMismatch(): int {
        $lang_id = (int)$this->config->get('config_language_id');
        $query = $this->db->query("
            SELECT COUNT(*) as total
            FROM (
                SELECT p.product_id,
                    (
                        CASE WHEN (p.image IS NOT NULL AND p.image != '' AND p.image != 'no_image.png') THEN 1 ELSE 0 END
                        + (SELECT COUNT(*) FROM " . DB_PREFIX . "product_image pi WHERE pi.product_id = p.product_id)
                    ) AS oc_image_count,
                    pm.image_backup_count
                FROM " . DB_PREFIX . "product p
                INNER JOIN " . DB_PREFIX . "product_marketplace pm ON (p.product_id = pm.product_id)
                WHERE pm.marketplace_id = 1
                AND pm.is_com = 0
                AND pm.image_backup_count IS NOT NULL
                HAVING pm.image_backup_count > oc_image_count
            ) as subquery
        ");
        return (int)$query->row['total'];
    }

    /**
     * Scan image_backup directory and update image_backup_count in product_marketplace.
     * Counts ALL image files in image_backup/data/product/{product_id}/
     * regardless of naming convention (old: {id}.jpg / {id}_{num}.jpg  OR
     * new: {id}pri{N}.jpg / {id}sec{N}.jpg).
     *
     * @param string $backup_dir  Absolute path to image_backup/ directory (trailing slash)
     * @return array ['scanned', 'with_images', 'empty', 'not_found']
     */
    public function scanImageBackupCounts(string $backup_dir): array {
        $query = $this->db->query("
            SELECT DISTINCT product_id
            FROM " . DB_PREFIX . "product_marketplace
            WHERE marketplace_id = 1
        ");

        if (!$query->num_rows) {
            return ['scanned' => 0, 'with_images' => 0, 'empty' => 0, 'not_found' => 0];
        }

        $stats = ['scanned' => 0, 'with_images' => 0, 'empty' => 0, 'not_found' => 0];
        $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        $updates = []; // product_id => count

        foreach ($query->rows as $row) {
            $product_id = (int)$row['product_id'];
            $dir   = $backup_dir . 'data/product/' . $product_id . '/';
            $count = 0;

            if (is_dir($dir)) {
                $files = @scandir($dir);
                if ($files) {
                    foreach ($files as $file) {
                        if ($file === '.' || $file === '..') continue;
                        if (!is_file($dir . $file)) continue;
                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($ext, $image_extensions)) {
                            $count++;
                        }
                    }
                }
                if ($count > 0) $stats['with_images']++;
                else             $stats['empty']++;
            } else {
                $stats['not_found']++;
            }

            $updates[$product_id] = $count;
            $stats['scanned']++;
        }

        // Batch UPDATE in chunks of 500 to avoid huge SQL statements
        foreach (array_chunk($updates, 500, true) as $chunk) {
            if (empty($chunk)) continue;
            $cases = '';
            $ids   = [];
            foreach ($chunk as $pid => $cnt) {
                $cases .= " WHEN " . (int)$pid . " THEN " . (int)$cnt;
                $ids[]  = (int)$pid;
            }
            $ids_str = implode(',', $ids);
            $this->db->query("
                UPDATE " . DB_PREFIX . "product_marketplace
                SET image_backup_count = CASE product_id" . $cases . " END
                WHERE product_id IN (" . $ids_str . ")
                AND marketplace_id = 1
            ");
        }

        return $stats;
    }

    /**
     * Import category from eBay for single product
     */
    public function importCategoryFromEbay(int $product_id): array {
        $this->load->model('shopmanager/marketplace');
        
        $marketplace = $this->model_shopmanager_marketplace->getMarketplaceItem($product_id, 1);
        if (!$marketplace || !isset($marketplace['category_id'])) {
            return ['error' => 'Product not listed on eBay or no category_id'];
        }
        
        $ebay_category_id = (int)$marketplace['category_id'];
        
        // Vérifier si la catégorie existe dans BD
        $category_check = $this->db->query("
            SELECT category_id FROM " . DB_PREFIX . "category 
            WHERE category_id = '" . (int)$ebay_category_id . "'
        ");
        
        if ($category_check->num_rows == 0) {
            return ['error' => "eBay category $ebay_category_id does not exist in local database"];
        }
        
        // Supprimer uniquement les catégories leaf=1
        $this->db->query("
            DELETE pc FROM " . DB_PREFIX . "product_to_category pc
            INNER JOIN " . DB_PREFIX . "category c ON pc.category_id = c.category_id
            WHERE pc.product_id = '" . (int)$product_id . "'
            AND c.leaf = 1
        ");
        
        // Vérifier si la catégorie existe déjà pour éviter doublon
        $existing = $this->db->query("
            SELECT 1 FROM " . DB_PREFIX . "product_to_category
            WHERE product_id = '" . (int)$product_id . "'
            AND category_id = '" . (int)$ebay_category_id . "'
        ");
        
        if ($existing->num_rows == 0) {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "product_to_category 
                SET product_id = '" . (int)$product_id . "',
                    category_id = '" . (int)$ebay_category_id . "'
            ");
        }
        
        // Reset specifics
        $this->db->query("
            UPDATE " . DB_PREFIX . "product_description
            SET specifics = NULL
            WHERE product_id = '" . (int)$product_id . "'
        ");
        
        return ['success' => "Category imported: $ebay_category_id (specifics reset)"];
    }
    
    /**
     * Import categories from eBay for multiple products
     */
    public function importCategoriesFromEbayBulk(array $product_ids): array {
        $this->load->model('shopmanager/marketplace');
        
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        foreach ($product_ids as $product_id) {
            try {
                $marketplace = $this->model_shopmanager_marketplace->getMarketplaceItem($product_id, 1);
                
                if (!$marketplace || !isset($marketplace['category_id'])) {
                    $errors[] = "Product $product_id: Not on eBay";
                    $error_count++;
                    continue;
                }
                
                $ebay_category_id = (int)$marketplace['category_id'];
                
                // Vérifier existence
                $category_check = $this->db->query("
                    SELECT category_id FROM " . DB_PREFIX . "category 
                    WHERE category_id = '" . (int)$ebay_category_id . "'
                ");
                
                if ($category_check->num_rows == 0) {
                    $errors[] = "Product $product_id: Category $ebay_category_id not in database";
                    $error_count++;
                    continue;
                }
                
                // Supprimer leaf=1
                $this->db->query("
                    DELETE pc FROM " . DB_PREFIX . "product_to_category pc
                    INNER JOIN " . DB_PREFIX . "category c ON pc.category_id = c.category_id
                    WHERE pc.product_id = '" . (int)$product_id . "'
                    AND c.leaf = 1
                ");
                
                // Éviter doublon
                $existing = $this->db->query("
                    SELECT 1 FROM " . DB_PREFIX . "product_to_category
                    WHERE product_id = '" . (int)$product_id . "'
                    AND category_id = '" . (int)$ebay_category_id . "'
                ");
                
                if ($existing->num_rows == 0) {
                    $this->db->query("
                        INSERT INTO " . DB_PREFIX . "product_to_category 
                        SET product_id = '" . (int)$product_id . "',
                            category_id = '" . (int)$ebay_category_id . "'
                    ");
                }
                
                // Reset specifics
                $this->db->query("
                    UPDATE " . DB_PREFIX . "product_description
                    SET specifics = NULL
                    WHERE product_id = '" . (int)$product_id . "'
                ");
                
                $success_count++;
            } catch (\Exception $e) {
                $errors[] = "Product $product_id: " . $e->getMessage();
                $error_count++;
            }
        }
        
        $result = [];
        if ($success_count > 0) {
            $result['success'] = "$success_count category(ies) imported (specifics reset)";
            if ($error_count > 0) {
                $result['success'] .= " ($error_count failed)";
            }
        } else {
            $result['error'] = "All imports failed";
        }
        
        if (!empty($errors)) {
            $result['details'] = implode("\n", array_slice($errors, 0, 5));
        }
        
        return $result;
    }
    
    /**
     * Export category to eBay - update eBay listing category with local category (leaf=1 priority)
     */
    public function exportCategoryToEbay(int $product_id): array {
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        $this->load->model('shopmanager/catalog/product');
        
        // Get local category with leaf=1 priority
        $category_query = $this->db->query("
            SELECT COALESCE(
                (SELECT pc2.category_id FROM " . DB_PREFIX . "product_to_category pc2
                 LEFT JOIN " . DB_PREFIX . "category c2 ON pc2.category_id = c2.category_id
                 WHERE pc2.product_id = '" . (int)$product_id . "' AND c2.leaf = 1 LIMIT 1),
                (SELECT pc3.category_id FROM " . DB_PREFIX . "product_to_category pc3
                 WHERE pc3.product_id = '" . (int)$product_id . "' LIMIT 1)
            ) as category_id
        ");
        
        if (!$category_query->num_rows || !$category_query->row['category_id']) {
            return ['error' => 'Product has no category assigned'];
        }
        
        $local_category_id = (int)$category_query->row['category_id'];
        
        // Get product info
        $product = $this->model_shopmanager_catalog_product->getProduct($product_id);
        if (!$product) {
            return ['error' => 'Product not found'];
        }
        
        // Override category with local one
        $product['category_id'] = $local_category_id;
        
        // Calculate total quantity
        $quantity = (int)$product['quantity'] + (int)($product['unallocated_quantity'] ?? 0);
        
        // Get marketplace accounts
        $marketplace_accounts = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id, 'marketplace_id' => 1]);
        if (empty($marketplace_accounts)) {
            return ['error' => 'Product not listed on eBay'];
        }
        
        // Get site settings for each account
        foreach ($marketplace_accounts as $key => $marketplace) {
            $site_setting = $marketplace['site_setting'] ?? '{}';
            $marketplace_accounts[$key]['site_setting'] = is_array($site_setting) ? $site_setting : json_decode($site_setting, true);
        }
        
        // Get site_setting from first account for edit()
        $first_site_setting = reset($marketplace_accounts)['site_setting'] ?? [];
        
        // Reset category_id, specific, and condition_id in oc_product_marketplace to force re-import
        $this->db->query("UPDATE " . DB_PREFIX . "product_marketplace 
                          SET category_id = NULL, specific = NULL, condition_id = NULL 
                          WHERE product_id = '" . (int)$product_id . "' AND marketplace_id = 1");
        
        // Use edit() - it will update category and specifics
        $response = $this->model_shopmanager_ebay->edit($product, $quantity, $first_site_setting, $marketplace_accounts);
        
        if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
            return ['success' => "Category exported to eBay: $local_category_id"];
        } else {
            $error_msg = $response['Errors']['ShortMessage'] ?? $response['Errors'][0]['ShortMessage'] ?? 'Update failed';
            return ['error' => $error_msg];
        }
    }
    
    /**
     * Export categories to eBay for multiple products
     */
    public function exportCategoriesToEbayBulk(array $product_ids): array {
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        $this->load->model('shopmanager/catalog/product');
        
        $success_count = 0;
        $error_count = 0;
        $errors = [];
        
        foreach ($product_ids as $product_id) {
            try {
                // Get local category with leaf=1 priority
                $category_query = $this->db->query("
                    SELECT COALESCE(
                        (SELECT pc2.category_id FROM " . DB_PREFIX . "product_to_category pc2
                         LEFT JOIN " . DB_PREFIX . "category c2 ON pc2.category_id = c2.category_id
                         WHERE pc2.product_id = '" . (int)$product_id . "' AND c2.leaf = 1 LIMIT 1),
                        (SELECT pc3.category_id FROM " . DB_PREFIX . "product_to_category pc3
                         WHERE pc3.product_id = '" . (int)$product_id . "' LIMIT 1)
                    ) as category_id
                ");
                
                if (!$category_query->num_rows || !$category_query->row['category_id']) {
                    $errors[] = "Product $product_id: No category assigned";
                    $error_count++;
                    continue;
                }
                
                $local_category_id = (int)$category_query->row['category_id'];
                
                // Get product info
                $product = $this->model_shopmanager_catalog_product->getProduct($product_id);
                if (!$product) {
                    $errors[] = "Product $product_id: Not found";
                    $error_count++;
                    continue;
                }
                
                // Override category with local one
                $product['category_id'] = $local_category_id;
                
                // Calculate total quantity
                $quantity = (int)$product['quantity'] + (int)($product['unallocated_quantity'] ?? 0);
                
                // Get marketplace accounts
                $marketplace_accounts = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id, 'marketplace_id' => 1]);
                if (empty($marketplace_accounts)) {
                    $errors[] = "Product $product_id: Not on eBay";
                    $error_count++;
                    continue;
                }
                
                // Get site settings for each account
                foreach ($marketplace_accounts as $key => $marketplace) {
                    $site_setting = $marketplace['site_setting'] ?? '{}';
                    $marketplace_accounts[$key]['site_setting'] = is_array($site_setting) ? $site_setting : json_decode($site_setting, true);
                }
                
                // Get site_setting from first account for edit()
                $first_site_setting = reset($marketplace_accounts)['site_setting'] ?? [];
                
                // Reset category_id, specific, and condition_id in oc_product_marketplace to force re-import
                $this->db->query("UPDATE " . DB_PREFIX . "product_marketplace 
                                  SET category_id = NULL, specific = NULL, condition_id = NULL 
                                  WHERE product_id = '" . (int)$product_id . "' AND marketplace_id = 1");
                
                // Use edit() - it will update category and specifics
                $response = $this->model_shopmanager_ebay->edit($product, $quantity, $first_site_setting, $marketplace_accounts);
                
                if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                    $success_count++;
                } else {
                    $error_msg = $response['Errors']['ShortMessage'] ?? $response['Errors'][0]['ShortMessage'] ?? 'Update failed';
                    $errors[] = "Product $product_id: $error_msg";
                    $error_count++;
                }
            } catch (\Exception $e) {
                $errors[] = "Product $product_id: " . $e->getMessage();
                $error_count++;
            }
        }
        
        $result = [];
        if ($success_count > 0) {
            $result['success'] = "$success_count category(ies) exported";
            if ($error_count > 0) {
                $result['success'] .= " ($error_count failed)";
            }
        } else {
            $result['error'] = "All exports failed";
        }
        
        if (!empty($errors)) {
            $result['details'] = implode("\n", array_slice($errors, 0, 5));
        }
        
        return $result;
    }

    /**
     * Apply the best-match category from oc_product_info_sources to a product.
     * Finds the entry with the highest percent, removes leaf=1 categories,
     * inserts the new one, resets specifics.
     */
    public function applyCategoryFromInfoSource(int $product_id): array {
        // Get product UPC
        $p = $this->db->query("
            SELECT upc FROM " . DB_PREFIX . "product
            WHERE product_id = " . (int)$product_id);
        if (!$p->num_rows || empty($p->row['upc'])) {
            return ['error' => 'Product has no UPC'];
        }
        $upc = $p->row['upc'];

        // Get all info_sources rows for this UPC
        $sources = $this->db->query("
            SELECT ebay_category FROM " . DB_PREFIX . "product_info_sources
            WHERE upc = '" . $this->db->escape($upc) . "'");
        if (!$sources->num_rows) {
            return ['error' => 'No info sources found for UPC ' . htmlspecialchars($upc)];
        }

        // Find entry with highest percent
        $best_id   = null;
        $best_pct  = -1;
        $best_name = '';
        foreach ($sources->rows as $row) {
            $cats = json_decode($row['ebay_category'] ?? '[]', true);
            if (!is_array($cats)) continue;
            foreach ($cats as $cat) {
                $pct = (int)($cat['percent'] ?? 0);
                if ($pct > $best_pct && !empty($cat['category_id'])) {
                    $best_pct  = $pct;
                    $best_id   = (int)$cat['category_id'];
                    $best_name = $cat['category_name'] ?? '';
                }
            }
        }

        if (!$best_id) {
            return ['error' => 'No valid category found in info sources'];
        }

        // Verify category exists locally
        $check = $this->db->query("
            SELECT category_id FROM " . DB_PREFIX . "category
            WHERE category_id = " . (int)$best_id);
        if (!$check->num_rows) {
            return ['error' => "Category $best_id from info source not in local database"];
        }

        // Delete existing leaf=1 categories
        $this->db->query("
            DELETE pc FROM " . DB_PREFIX . "product_to_category pc
            INNER JOIN " . DB_PREFIX . "category c ON pc.category_id = c.category_id
            WHERE pc.product_id = " . (int)$product_id . "
            AND c.leaf = 1");

        // Insert new category (avoid duplicate)
        $existing = $this->db->query("
            SELECT 1 FROM " . DB_PREFIX . "product_to_category
            WHERE product_id = " . (int)$product_id . "
            AND category_id = " . (int)$best_id);
        if (!$existing->num_rows) {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "product_to_category
                SET product_id = " . (int)$product_id . ",
                    category_id = " . (int)$best_id);
        }

        // Reset specifics (new category = new specifics needed)
        $this->db->query("
            UPDATE " . DB_PREFIX . "product_description
            SET specifics = NULL
            WHERE product_id = " . (int)$product_id);

        $label = $best_name ? "$best_id - $best_name" : "$best_id";
        return ['success' => "Category $label ($best_pct%) applied from info source (specifics reset)"];
    }
}

