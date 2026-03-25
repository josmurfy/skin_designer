<?php
namespace Opencart\Admin\Controller\Shopmanager\Inventory;

/**
 * Class Sync
 *
 * Inventory Sync & Issues Dashboard - eBay synchronization and quantity management
 *
 * @package Opencart\Admin\Controller\Shopmanager\Inventory
 */
class Sync extends \Opencart\System\Engine\Controller {
    private array $error = [];

    /**
     * Index - Affiche le tableau de bord analytique
     *
     * @return void
     */
    public function index(): void {
        $lang = $this->load->language('shopmanager/inventory/sync');
        $data = $data ?? [];
        $data += $lang;
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        $this->document->addScript('view/javascript/shopmanager/inventory/sync.js');

        // Breadcrumbs
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/inventory/sync', 'user_token=' . $this->session->data['user_token'])
        ];

        // Language strings
        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_refresh'] = ($lang['text_refresh'] ?? '');
        $data['text_sync_progress'] = ($lang['text_sync_progress'] ?? '');
        $data['text_starting_sync'] = ($lang['text_starting_sync'] ?? '');
        $data['text_listed_ebay'] = ($lang['text_listed_ebay'] ?? '');
        $data['text_not_listed_qty'] = ($lang['text_not_listed_qty'] ?? '');
        $data['text_marketplace_errors'] = ($lang['text_marketplace_errors'] ?? '');
        $data['text_not_synced'] = ($lang['text_not_synced'] ?? '');
        $data['text_quantity_mismatch'] = ($lang['text_quantity_mismatch'] ?? '');
        $data['text_sync_ebay'] = ($lang['text_sync_ebay'] ?? '');
        $data['text_refresh_data'] = ($lang['text_refresh_data'] ?? '');
        
        // Tabs
        $data['tab_errors'] = ($lang['tab_errors'] ?? '');
        $data['tab_not_listed'] = ($lang['tab_not_listed'] ?? '');
        $data['tab_not_synced'] = ($lang['tab_not_synced'] ?? '');
        $data['tab_mismatch'] = ($lang['tab_mismatch'] ?? '');
        $data['tab_slow_moving'] = ($lang['tab_slow_moving'] ?? '');
        
        // Table Headers
        $data['text_products_errors'] = ($lang['text_products_errors'] ?? '');
        $data['text_products_not_listed'] = ($lang['text_products_not_listed'] ?? '');
        $data['text_products_not_synced'] = ($lang['text_products_not_synced'] ?? '');
        $data['text_quantity_mismatches'] = ($lang['text_quantity_mismatches'] ?? '');
        $data['text_slow_moving_items'] = ($lang['text_slow_moving_items'] ?? '');
        $data['text_no_products'] = ($lang['text_no_products'] ?? '');
        
        // Column labels
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_sku'] = ($lang['column_sku'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_local_qty'] = ($lang['column_local_qty'] ?? '');
        $data['column_unallocated'] = ($lang['column_unallocated'] ?? '');
        $data['column_total'] = ($lang['column_total'] ?? '');
        $data['column_ebay_available'] = ($lang['column_ebay_available'] ?? '');
        $data['column_difference'] = ($lang['column_difference'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        // Button labels
        $data['button_sync_ebay'] = ($lang['button_sync_ebay'] ?? '');
        $data['button_print_report'] = ($lang['button_print_report'] ?? '');
        $data['button_import_from_ebay'] = ($lang['button_import_from_ebay'] ?? '');
        $data['button_edit'] = ($lang['button_edit'] ?? '');

        // Tooltips
        $data['tooltip_import_from_ebay'] = ($lang['tooltip_import_from_ebay'] ?? '');
        $data['tooltip_refresh_item'] = ($lang['tooltip_refresh_item'] ?? '');

        // Sync table labels
        $data['text_edit_product']  = ($lang['text_edit_product'] ?? '');
        $data['text_sync_to_ebay']  = ($lang['text_sync_to_ebay'] ?? '');
        $data['text_sync_from_ebay']= ($lang['text_sync_from_ebay'] ?? '');
        $data['text_last_sync']     = ($lang['text_last_sync'] ?? '');
        $data['text_never_synced']  = ($lang['text_never_synced'] ?? '');
        $data['text_no_data']       = ($lang['text_no_data'] ?? '');
        $data['column_price']       = ($lang['column_price'] ?? '');
        $data['column_quantity']    = ($lang['column_quantity'] ?? '');
        $data['column_specifics']   = ($lang['column_specifics'] ?? '');
        $data['column_stock']       = ($lang['column_stock'] ?? '');
        $data['column_sales']       = ($lang['column_sales'] ?? '');
        
        $data['text_confirm_sync_all']     = ($lang['text_confirm_sync_all'] ?? '');
        $data['text_error_sync_url']       = ($lang['text_error_sync_url'] ?? '');
        $data['text_confirm_sync_product'] = ($lang['text_confirm_sync_product'] ?? '');
        $data['text_confirm_refresh_all']  = ($lang['text_confirm_refresh_all'] ?? '');

        // Messages for JavaScript
        $data['text_update_confirm'] = ($lang['text_update_confirm'] ?? '');
        $data['text_update_success'] = ($lang['text_update_success'] ?? '');
        $data['text_update_error'] = ($lang['text_update_error'] ?? '');
        $data['text_updating'] = ($lang['text_updating'] ?? '');

        // Get period from request (non utilisé mais gardé pour compatibilité)
        $period = $this->request->get['period'] ?? 'month';
        $data['period'] = $period;

        // URLs
        $data['url_get_data'] = $this->url->link('shopmanager/inventory/sync.getData', 'user_token=' . $this->session->data['user_token']);
        
        $data['user_token'] = $this->session->data['user_token'];

        // Load initial data
        $data['analytics_data'] = $this->getAnalyticsData($period);
        
        // Load initial tab contents with pagination
        $data['price_mismatch_content'] = $this->getPriceMismatchContent();
        $data['qty_mismatch_content'] = $this->getQtyMismatchContent();
        $data['specifics_mismatch_content'] = $this->getSpecificsMismatchContent();
        $data['condition_mismatch_content'] = $this->getConditionMismatchContent();
        $data['category_mismatch_content'] = $this->getCategoryMismatchContent();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/inventory/sync', $data));
    }
    
    /**
     * Get initial Price Mismatch tab content
     */
    private function getPriceMismatchContent(): string {
        $this->load->model('shopmanager/inventory/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $price_mismatch = $this->model_shopmanager_inventory_sync->getPriceMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalPriceMismatch();
        
        $data['price_mismatch'] = $price_mismatch;
        $data['price_mismatch_total'] = $total;
        $data['price_mismatch_page'] = 1;
        $data['price_mismatch_sort'] = 'product_id';
        $data['price_mismatch_order'] = 'ASC';
        $data['price_mismatch_start'] = 1;
        $data['price_mismatch_end'] = min($limit, $total);
        $data['price_mismatch_num_pages'] = ceil($total / $limit);
        $data['price_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        return $this->load->view('shopmanager/inventory/sync_price_mismatch', $data);
    }
    
    /**
     * Get initial Quantity Mismatch tab content
     */
    private function getQtyMismatchContent(): string {
        $this->load->model('shopmanager/inventory/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $qty_mismatch = $this->model_shopmanager_inventory_sync->getQuantityMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalQuantityMismatch();
        
        $data['qty_mismatch'] = $qty_mismatch;
        $data['qty_mismatch_total'] = $total;
        $data['qty_mismatch_page'] = 1;
        $data['qty_mismatch_sort'] = 'product_id';
        $data['qty_mismatch_order'] = 'ASC';
        $data['qty_mismatch_start'] = 1;
        $data['qty_mismatch_end'] = min($limit, $total);
        $data['qty_mismatch_num_pages'] = ceil($total / $limit);
        $data['qty_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        return $this->load->view('shopmanager/inventory/sync_qty_mismatch', $data);
    }
    
    /**
     * Get initial Specifics Mismatch tab content
     */
    private function getSpecificsMismatchContent(): string {
        $this->load->model('shopmanager/inventory/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $specifics_mismatch = $this->model_shopmanager_inventory_sync->getSpecificsMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalSpecificsMismatch();
        
        $data['specifics_mismatch'] = $specifics_mismatch;
        $data['specifics_mismatch_total'] = $total;
        $data['specifics_mismatch_page'] = 1;
        $data['specifics_mismatch_sort'] = 'product_id';
        $data['specifics_mismatch_order'] = 'ASC';
        $data['specifics_mismatch_start'] = 1;
        $data['specifics_mismatch_end'] = min($limit, $total);
        $data['specifics_mismatch_num_pages'] = ceil($total / $limit);
        $data['specifics_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        return $this->load->view('shopmanager/inventory/sync_specifics_mismatch', $data);
    }
    
    /**
     * Get initial Condition Mismatch tab content
     */
    private function getConditionMismatchContent(): string {
        $this->load->model('shopmanager/inventory/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $condition_mismatch = $this->model_shopmanager_inventory_sync->getConditionMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalConditionMismatch();
        
        $data['condition_mismatch'] = $condition_mismatch;
        $data['condition_mismatch_total'] = $total;
        $data['condition_mismatch_page'] = 1;
        $data['condition_mismatch_sort'] = 'product_id';
        $data['condition_mismatch_order'] = 'ASC';
        $data['condition_mismatch_start'] = 1;
        $data['condition_mismatch_end'] = min($limit, $total);
        $data['condition_mismatch_num_pages'] = ceil($total / $limit);
        $data['condition_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        return $this->load->view('shopmanager/inventory/sync_condition_mismatch', $data);
    }
    
    /**
     * Get initial Category Mismatch tab content
     */
    private function getCategoryMismatchContent(): string {
        $this->load->model('shopmanager/inventory/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $category_mismatch = $this->model_shopmanager_inventory_sync->getCategoryMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalCategoryMismatch();
        
        $data['category_mismatch'] = $category_mismatch;
        $data['category_mismatch_total'] = $total;
        $data['category_mismatch_page'] = 1;
        $data['category_mismatch_sort'] = 'product_id';
        $data['category_mismatch_order'] = 'ASC';
        $data['category_mismatch_start'] = 1;
        $data['category_mismatch_end'] = min($limit, $total);
        $data['category_mismatch_num_pages'] = ceil($total / $limit);
        $data['category_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        return $this->load->view('shopmanager/inventory/sync_category_mismatch', $data);
    }

    /**
     * Get Data - AJAX endpoint pour rafraîchir les données
     *
     * @return void
     */
    public function getData(): void {
        $lang = $this->load->language('shopmanager/inventory/sync');
        $data = $data ?? [];
        $data += $lang;

        $json = [];

        try {
            if (isset($this->request->get['period'])) {
                $period = $this->request->get['period'];
                $json = $this->getAnalyticsData($period);
                $json['success'] = true;
            } else {
                $json['error'] = 'Period parameter missing';
                $json['success'] = false;
            }
        } catch (\Exception $e) {
            $json['success'] = false;
            $json['error'] = $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Get Analytics Data - Collecte sync issues et inventory data
     *
     * @param string $period (non utilisé mais gardé pour compatibilité)
     * @return array
     */
    private function getAnalyticsData(string $period): array {
        $this->load->model('shopmanager/inventory/sync');

        $data = [];

        // Inventory Stats
        $data['total_listed_ebay'] = $this->model_shopmanager_inventory_sync->getTotalListedOnEbay();

        // Sync Issues
        $data['products_with_errors'] = $this->model_shopmanager_inventory_sync->getProductsWithErrors();
        $data['products_not_synced'] = $this->model_shopmanager_inventory_sync->getProductsNotSynced();
        $data['products_not_listed'] = $this->model_shopmanager_inventory_sync->getProductsNotListed();
        $data['quantity_mismatch'] = $this->model_shopmanager_inventory_sync->getQuantityMismatch();

        // Error summary for dashboard
        $data['error_summary'] = $this->model_shopmanager_inventory_sync->getErrorSummary();

        // Counts for dashboard
        $data['error_count'] = count($data['products_with_errors']);
        $data['not_synced_count'] = count($data['products_not_synced']);
        $data['not_listed_count'] = count($data['products_not_listed']);
        $data['mismatch_count'] = count($data['quantity_mismatch']);
        
        // Counts for separate mismatch types - use getTotalXXX methods instead of loading all data
        $data['price_mismatch_count'] = $this->model_shopmanager_inventory_sync->getTotalPriceMismatch();
        $data['qty_mismatch_count'] = $this->model_shopmanager_inventory_sync->getTotalQuantityMismatch();
        $data['specifics_mismatch_count'] = $this->model_shopmanager_inventory_sync->getTotalSpecificsMismatch();
        $data['condition_mismatch_count'] = $this->model_shopmanager_inventory_sync->getTotalConditionMismatch();
        $data['category_mismatch_count'] = $this->model_shopmanager_inventory_sync->getTotalCategoryMismatch();

        // Slow Moving Products
        $data['bottom_products'] = $this->model_shopmanager_inventory_sync->getBottomProducts($period, 10);

        // Alerts
        $data['alerts'] = $this->model_shopmanager_inventory_sync->getAlerts();

        return $data;
    }

    /**
     * Export - Exporte les données analytiques en CSV
     *
     * @return void
     */
    public function export(): void {
        $this->load->model('shopmanager/inventory/sync');

        $period = $this->request->get['period'] ?? 'month';
        $data = $this->getAnalyticsData($period);

        // Create CSV content
        $csv = "Phoenix Liquidation - Analytics Report\n";
        $csv .= "Generated: " . date('Y-m-d H:i:s') . "\n";
        $csv .= "Period: " . $period . "\n\n";

        // Overview Section
        $csv .= "OVERVIEW\n";
        $csv .= "Metric,Value\n";
        foreach ($data['overview'] as $key => $value) {
            $csv .= "$key,$value\n";
        }
        $csv .= "\n";

        // Top Products
        $csv .= "TOP PRODUCTS\n";
        $csv .= "Rank,Product Name,SKU,Sales,Revenue\n";
        $rank = 1;
        foreach ($data['top_products'] as $product) {
            $csv .= "$rank,{$product['name']},{$product['sku']},{$product['sales']},{$product['revenue']}\n";
            $rank++;
        }

        // Set headers for download
        $filename = 'analytics_report_' . date('Y-m-d') . '.csv';
        $this->response->addHeader('Content-Type: text/csv');
        $this->response->addHeader('Content-Disposition: attachment; filename="' . $filename . '"');
        $this->response->setOutput($csv);
    }

    /**
     * Quick Stats - Widget pour afficher des stats rapides
     *
     * @return string
     */
    public function quickStats(): string {
        $this->load->model('shopmanager/inventory/sync');
        $lang = $this->load->language('shopmanager/analytics');
        $data = $data ?? [];
        $data += $lang;

        $data = [];
        $data['total_products'] = $this->model_shopmanager_analytics->getTotalProducts();
        $data['low_stock'] = $this->model_shopmanager_analytics->getLowStockCount();
        $data['pending_orders'] = $this->model_shopmanager_analytics->getPendingOrders();
        $data['todays_revenue'] = $this->model_shopmanager_analytics->getTodaysRevenue();

        return $this->load->view('shopmanager/analytics_widget', $data);
    }

    /**
     * Sync Marketplace Data - Synchronise l'inventaire eBay page par page
     *
     * @return void
     */
    public function importMarketplace(): void {
        // Force write to a test file to confirm function is called
        file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - importMarketplace CALLED\n", FILE_APPEND);
        
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');

        // file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - Models loaded\n", FILE_APPEND);
        
        $json = [];

        try {
            // Get parameters
            $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
            $marketplace_account_id = isset($this->request->get['account_id']) ? (int)$this->request->get['account_id'] : 1;
            $limit = 200; // eBay allows up to 200 per page for GetMyeBaySelling

            // file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - Page: $page, Account: $marketplace_account_id\n", FILE_APPEND);

            // Use GetMyeBaySelling instead - more efficient for bulk sync
            $response = $this->model_shopmanager_ebay->getMyeBaySellingBulk($page, $marketplace_account_id);
            
            // file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - API response received\n", FILE_APPEND);

            // Check if we have products (GetMyeBaySelling uses different structure)
            $items = [];
            if (isset($response['ActiveList']['ItemArray']['Item'])) {
                $items = $response['ActiveList']['ItemArray']['Item'];
                // If single item, wrap in array
                if (isset($items['ItemID'])) {
                    $items = [$items];
                }
                
                // DUMP first item to see what eBay is actually returning
                if (!empty($items)) {
                    // file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - FIRST ITEM DUMP:\n" . print_r($items[0], true) . "\n", FILE_APPEND);
                }
            }
            
            // file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - Found " . count($items) . " items\n", FILE_APPEND);
            
            if (empty($items)) {
                $json['success'] = true;
                $json['completed'] = true;
                $json['message'] = 'Synchronization completed';
                $json['processed'] = 0;
                $json['page'] = $page;
            } else {
                $processed = 0;

                // Process each item
                foreach ($items as $item) {
                    // // Stop after 10 products for testing
                    // if ($processed >= 10) {
                    //     break;
                    // }
                    
                    // Check if ItemID exists
                    if (!isset($item['ItemID']) || empty($item['ItemID'])) {
                        continue;
                    }

                    $item_id = $item['ItemID'];
                    $sku = isset($item['SKU']) ? trim($item['SKU']) : '';
                    
                    if (empty($sku)) {
                        continue;
                    }
                    
                    // Determine which database based on SKU
                    $is_com = (substr($sku, 0, 4) === 'COM_') ? 1 : 0;
                    
                    $product_id = 0;
                    $db_active = null;
                    
                    if ($is_com) {
                        // phoenixsupplies: SKU = COM_xxxx, need to search
                        $db_active = mysqli_connect('localhost', 'n7f9655_n7f9655', 'jnthngrvs01$$', 'n7f9655_phoenixsupplies');
                        if (!$db_active) {
                            continue;
                        }
                        
                        $product_id = $this->model_shopmanager_marketplace->getProductIdBySku($sku, $db_active);
                    } else {
                        // phoenixliquidation: SKU = product_id (numeric)
                        if (is_numeric($sku)) {
                            $product_id = (int)$sku;
                        }
                    }
                    
                    // If product not found, skip
                    if ($product_id == 0) {
                        if ($db_active) mysqli_close($db_active);
                        continue;
                    }

                    // Check if we already have category, condition, and specifics in database
                    $existing_data = $this->model_shopmanager_marketplace->getMarketplaceExistingData(
                        $product_id, 
                        1, 
                        $is_com ? $db_active : null
                    );

                    // Only call GetItem if we don't have complete data already
                    $item_details = null;
                    $needs_getitem = !$existing_data || 
                                    is_null($existing_data['category_id']) || 
                                    is_null($existing_data['condition_id']) || 
                                    is_null($existing_data['specifics']);
                    
                    if ($needs_getitem) {
                        // GET FULL ITEM DETAILS with GetItem API call (for category, condition, specifics)
                        $item_details = $this->model_shopmanager_ebay->getItemDetails($item_id, $marketplace_account_id);
                        // file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - GetItem called for $item_id (missing data)\n", FILE_APPEND);
                    } else {
                        // file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - Skipped GetItem for $item_id (data already exists)\n", FILE_APPEND);
                    }

                    // Get eBay dates
                    $date_added = null;
                    $date_ended = null;
                    
                    if (isset($item['ListingDetails']['StartTime'])) {
                        $date_added = date('Y-m-d H:i:s', strtotime($item['ListingDetails']['StartTime']));
                    }
                    
                    if (isset($item['ListingDetails']['EndTime'])) {
                        $date_ended = date('Y-m-d H:i:s', strtotime($item['ListingDetails']['EndTime']));
                    }

                    // Extract category, condition, and specifics from GetItem response OR use existing data
                    $category_id = null;
                    $condition_id = null;
                    $specifics = null;
                    
                    if ($item_details) {
                        // Use data from GetItem API call
                        $category_id = $item_details['category_id'];
                        $condition_id = $item_details['condition_id'];
                        
                        // Format ItemSpecifics as JSON
                        if ($item_details['item_specifics']) {
                            $specs_array = $item_details['item_specifics'];
                            // If single item, wrap in array
                            if (isset($specs_array['Name'])) {
                                $specs_array = [$specs_array];
                            }
                            $specifics_formatted = [];
                            foreach ($specs_array as $spec) {
                                if (isset($spec['Name']) && isset($spec['Value'])) {
                                    $specifics_formatted[$spec['Name']] = $spec['Value'];
                                }
                            }
                            $specifics = !empty($specifics_formatted) ? json_encode($specifics_formatted) : null;
                        }
                    } elseif ($existing_data) {
                        // Use existing data from database (skip GetItem)
                        $category_id = $existing_data['category_id'];
                        $condition_id = $existing_data['condition_id'];
                        $specifics = $existing_data['specifics'];
                    }
                    
                    // Get the ORIGINAL price before discounts
                    $listing_price = 0.00;
                    if (isset($item['SellingStatus']['PromotionalSaleDetails']['OriginalPrice'])) {
                        // Use original price if product is on sale
                        $listing_price = (float)$item['SellingStatus']['PromotionalSaleDetails']['OriginalPrice'];
                    } elseif (isset($item['SellingStatus']['CurrentPrice'])) {
                        // Use current price if not on sale
                        $listing_price = (float)$item['SellingStatus']['CurrentPrice'];
                    }

                    // Prepare marketplace data with full details from GetItem
                    $marketplace_data = [
                        'product_id' => $product_id,
                        'customer_id' => 1,
                        'marketplace_id' => 1,
                        'marketplace_account_id' => $marketplace_account_id,
                        'marketplace_item_id' => $item['ItemID'],
                        'category_id' => $category_id,
                        'condition_id' => $condition_id,
                        'currency' => 'CAD', // Default for Canada site
                        'price' => $listing_price,
                        'price_usd' => $listing_price, // Same for CAD site
                        'quantity_listed' => isset($item['Quantity']) ? (int)$item['Quantity'] : 0,
                        'quantity_sold' => isset($item['SellingStatus']['QuantitySold']) ? (int)$item['SellingStatus']['QuantitySold'] : 0,
                        'specifics' => $specifics,
                        'status' => isset($item['ListingDetails']['EndingReason']) ? 0 : 1,
                        'is_com' => $is_com,
                        'date_added' => $date_added,
                        'date_ended' => $date_ended,
                        'last_import_time' => date('Y-m-d H:i:s')
                    ];

                    // Update or insert in product_marketplace table (in the appropriate database)
                    $this->model_shopmanager_marketplace->upsertProductMarketplace(
                        $marketplace_data,
                        $is_com ? $db_active : null
                    );
                    
                    if ($is_com && $db_active) {
                        mysqli_close($db_active);
                    }

                    $processed++;
                }

                // Check if there are more pages (GetMyeBaySelling structure)
                $total_pages = 1;
                $total_entries = 0;
                
                if (isset($response['ActiveList']['PaginationResult'])) {
                    $total_pages = isset($response['ActiveList']['PaginationResult']['TotalNumberOfPages']) ? 
                        (int)$response['ActiveList']['PaginationResult']['TotalNumberOfPages'] : 1;
                    $total_entries = isset($response['ActiveList']['PaginationResult']['TotalNumberOfEntries']) ? 
                        (int)$response['ActiveList']['PaginationResult']['TotalNumberOfEntries'] : 0;
                }

                // Check if there are more pages to process
                $json['success'] = true;
                $json['completed'] = ($page >= $total_pages);
                $json['processed'] = $processed;
                $json['page'] = $page;
                $json['total_pages'] = $total_pages;
                $json['total_entries'] = $total_entries;
                $json['message'] = "Processed $processed products on page $page of $total_pages";
            }
        } catch (\Exception $e) {
            error_log("[Analytics Sync] Error: " . $e->getMessage());
            $json['success'] = false;
            $json['error'] = $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Update eBay Quantity - Met à jour la quantité sur eBay avec la quantité locale
     *
     * @return void
     */
    public function updateEbayQuantity(): void {
        $json = [];

        try {
            if (!isset($this->request->post['product_id'])) {
                throw new \Exception('Product ID required');
            }

            $product_id = (int)$this->request->post['product_id'];

            // Get product info using model
            $product = $this->model_shopmanager_marketplace->getProductQuantities($product_id);
            
            if (!$product) {
                throw new \Exception('Product not found');
            }

            // Get marketplace info using model
            $marketplace = $this->model_shopmanager_marketplace->getMarketplaceForRefresh($product_id);

            if (!$marketplace) {
                throw new \Exception('Product not listed on eBay');
            }

            $marketplace_item_id = $marketplace['marketplace_item_id'];
            $marketplace_account_id = (int)$marketplace['marketplace_account_id'];
            $new_quantity = (int)$product['quantity'] + (int)$product['unallocated_quantity'];

            // Load models
            $this->load->model('shopmanager/ebay');
            $this->load->model('shopmanager/marketplace');
            
            // Get marketplace account settings
            $account_info = $this->model_shopmanager_marketplace->getMarketplaceAccount(['marketplace_account_id' => $marketplace_account_id], true);
            $site_settings = [];
            
            if ($account_info && !empty($account_info['site_setting'])) {
                $site_settings = json_decode($account_info['site_setting'], true) ?: [];
            }
            
            // Set all default values if not present
            if (!isset($site_settings['Currency']['Currency'])) {
                $site_settings['Currency']['Currency'] = 'CAD';
            }
            if (!isset($site_settings['Location']['Location'])) {
                $site_settings['Location']['Location'] = 'CA';
            }
            if (!isset($site_settings['Location']['PostalCode'])) {
                $site_settings['Location']['PostalCode'] = 'J0H1L0';
            }
            if (!isset($site_settings['Country']['Country'])) {
                $site_settings['Country']['Country'] = 'CA';
            }
            // Language is a direct string, not nested
            if (!isset($site_settings['Language'])) {
                $site_settings['Language'] = 'en';
            }
            
            // Update quantity on eBay
            $response = $this->model_shopmanager_ebay->editQuantity(
                $marketplace_item_id, 
                $new_quantity, 
                null,
                $product_id, 
                $marketplace_account_id,
                $site_settings
            );

            // Update local quantity_listed using model
            $this->model_shopmanager_marketplace->updateMarketplaceQuantityListed($product_id, $new_quantity);

            if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                $json['success'] = true;
                $json['message'] = 'Quantity updated to ' . $new_quantity . ' on eBay';
            } else {
                $error_msg = 'Update failed';
                if (isset($response['Errors']['ShortMessage'])) {
                    $error_msg = $response['Errors']['ShortMessage'];
                } elseif (isset($response['Errors'][0]['ShortMessage'])) {
                    $error_msg = $response['Errors'][0]['ShortMessage'];
                }
                $json['success'] = false;
                $json['error'] = $error_msg;
            }

        } catch (\Exception $e) {
            $json['success'] = false;
            $json['error'] = $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Sync Single Product - Synchronise un seul produit vers eBay
     *
     * @return void
     */
    public function syncSingleProduct(): void {
        $json = [];

        try {
            if (!isset($this->request->post['product_id'])) {
                throw new \Exception('Product ID required');
            }

            $product_id = (int)$this->request->post['product_id'];

            // Get product info using model
            $product = $this->model_shopmanager_marketplace->getProductForRefresh($product_id);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            // Get marketplace info using model
            $marketplace = $this->model_shopmanager_marketplace->getMarketplaceForRefresh($product_id);

            if (!$marketplace) {
                throw new \Exception('Product not listed on eBay');
            }

            $marketplace_item_id = $marketplace['marketplace_item_id'];
            $new_quantity = (int)$product['quantity'] + (int)$product['unallocated_quantity'];

            // Call eBay API to update quantity
            require_once(DIR_SYSTEM . 'library/ebay/ebay_trading.php');
            $ebay = new EbayTrading();
            
            $response = $ebay->editQuantity($marketplace_item_id, $new_quantity);

            // Update last_sync timestamp using model
            $this->model_shopmanager_marketplace->updateMarketplaceLastSync($product_id);

            if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                $json['success'] = true;
                $json['message'] = 'Product "' . $product['name'] . '" synced successfully to eBay (Qty: ' . $new_quantity . ')';
            } else {
                $error_msg = 'Sync failed';
                if (isset($response['Errors']['ShortMessage'])) {
                    $error_msg = $response['Errors']['ShortMessage'];
                } elseif (isset($response['Errors'][0]['ShortMessage'])) {
                    $error_msg = $response['Errors'][0]['ShortMessage'];
                }
                $json['success'] = false;
                $json['error'] = $error_msg;
            }

        } catch (\Exception $e) {
            $json['success'] = false;
            $json['error'] = $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Print Mismatch Report - Génère un rapport d'impression pour les produits avec mismatch
     *
     * @return void
     */
    public function printMismatchReport(): void {
        $this->load->model('shopmanager/inventory/sync');
        
        $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
        
        if ($product_id) {
            // Single product report
            $products = $this->model_shopmanager_inventory_sync->getQuantityMismatch();
            $product = null;
            foreach ($products as $p) {
                if ($p['product_id'] == $product_id) {
                    $product = $p;
                    break;
                }
            }
            
            if (!$product) {
                echo "Product not found";
                return;
            }
            
            $products = [$product];
        } else {
            // All mismatches report
            $products = $this->model_shopmanager_inventory_sync->getQuantityMismatch();
        }
        
        // Generate HTML for printing
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quantity Mismatch Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; font-weight: bold; }
        .text-end { text-align: right; }
        .location { font-weight: bold; background-color: #ffffcc; }
        @media print {
            button { display: none; }
        }
    </style>
</head>
<body>
    <h1>Quantity Mismatch Report - ' . date('Y-m-d H:i') . '</h1>
    <table>
        <thead>
            <tr>
                <th>Product ID</th>
                <th>Product Name</th>
                <th>SKU</th>
                <th class="text-end">Local</th>
                <th class="text-end">Unalloc</th>
                <th class="text-end">Total</th>
                <th class="text-end">eBay Avail</th>
                <th class="text-end">Diff</th>
            </tr>
        </thead>
        <tbody>';
        
        $currentLocation = null;
        foreach ($products as $product) {
            // Add location header row when location changes
            if ($currentLocation !== $product['location']) {
                $currentLocation = $product['location'];
                $html .= '<tr class="table-active">
                    <td colspan="8" style="background-color: #ffffcc; font-weight: bold; font-size: 14px;">
                        <i class="fa-solid fa-map-marker-alt"></i> LOCATION: ' . htmlspecialchars($currentLocation ?: 'NO LOCATION') . '
                    </td>
                </tr>';
            }
            
            $diff = $product['ebay_available'] - $product['total_quantity'];
            $html .= '<tr>
                <td>' . htmlspecialchars($product['product_id']) . '</td>
                <td>' . htmlspecialchars($product['name']) . '</td>
                <td>' . htmlspecialchars($product['sku']) . '</td>
                <td class="text-end">' . $product['quantity'] . '</td>
                <td class="text-end">' . $product['unallocated_quantity'] . '</td>
                <td class="text-end">' . $product['total_quantity'] . '</td>
                <td class="text-end">' . $product['ebay_available'] . '</td>
                <td class="text-end">' . $diff . '</td>
            </tr>';
        }
        
        $html .= '</tbody>
    </table>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>';
        
        $this->response->setOutput($html);
    }

    /**
     * Sync Price To eBay - Envoie le prix local vers eBay
     *
     * @return void
     */
    public function syncPriceToEbay(): void {
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $product = $this->model_shopmanager_marketplace->getProductPrice($product_id);
                if (!$product) {
                    throw new \Exception("Product not found");
                }
                
                $marketplace = $this->model_shopmanager_marketplace->getMarketplaceItem($product_id, 1);
                if (!$marketplace) {
                    throw new \Exception("Product not listed on eBay");
                }
                
                $price = (float)$product['price'];
                $marketplace_item_id = $marketplace['marketplace_item_id'];
                $marketplace_account_id = $marketplace['marketplace_account_id'];
                
                $account_info = $this->model_shopmanager_marketplace->getMarketplaceAccount(['marketplace_account_id' => $marketplace_account_id], true);
                $site_settings = json_decode($account_info['site_setting'] ?? '{}', true);
                
                $response = $this->model_shopmanager_ebay->editPrice($marketplace_item_id, $price, $marketplace_account_id, $site_settings);
                
                if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                    $this->model_shopmanager_marketplace->updateMarketplacePrice($product_id, $price);
                    $json['success'] = 'Price synced to eBay: $' . number_format($price, 2);
                } else {
                    $error_msg = $response['Errors']['ShortMessage'] ?? $response['Errors'][0]['ShortMessage'] ?? 'Update failed';
                    $json['error'] = $error_msg;
                }
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Sync Price From eBay - Récupère le prix eBay vers local
     *
     * @return void
     */
    public function syncPriceFromEbay(): void {
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $marketplace = $this->model_shopmanager_marketplace->getMarketplaceItem($product_id, 1);
                if (!$marketplace || !isset($marketplace['price'])) {
                    throw new \Exception("Product not listed on eBay");
                }
                
                $ebay_price = (float)$marketplace['price'];
                $this->model_shopmanager_marketplace->updateProductPrice($product_id, $ebay_price);
                $json['success'] = 'Local price updated from eBay: $' . number_format($ebay_price, 2);
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Sync Quantity To eBay - Envoie la quantité locale vers eBay
     *
     * @return void
     */
    public function syncQuantityToEbay(): void {
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $product = $this->model_shopmanager_marketplace->getProductQuantities($product_id);
                if (!$product) {
                    throw new \Exception("Product not found");
                }
                
                $marketplace = $this->model_shopmanager_marketplace->getMarketplaceItem($product_id, 1);
                if (!$marketplace) {
                    throw new \Exception("Product not listed on eBay");
                }
                
                $quantity = (int)$product['quantity'] + (int)$product['unallocated_quantity'];
                $marketplace_item_id = $marketplace['marketplace_item_id'];
                $marketplace_account_id = $marketplace['marketplace_account_id'];
                
                $account_info = $this->model_shopmanager_marketplace->getMarketplaceAccount(['marketplace_account_id' => $marketplace_account_id], true);
                $site_settings = json_decode($account_info['site_setting'] ?? '{}', true);
                
                $response = $this->model_shopmanager_ebay->editQuantity($marketplace_item_id, $quantity, null, $product_id, $marketplace_account_id, $site_settings);
                
                if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                    $this->model_shopmanager_marketplace->updateMarketplaceQuantity($product_id, $quantity);
                    $json['success'] = 'Quantity synced to eBay: ' . $quantity;
                } else {
                    $error_msg = $response['Errors']['ShortMessage'] ?? $response['Errors'][0]['ShortMessage'] ?? 'Update failed';
                    $json['error'] = $error_msg;
                }
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Sync Quantity From eBay - Récupère la quantité eBay vers local
     *
     * @return void
     */
    public function syncQuantityFromEbay(): void {
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $marketplace = $this->model_shopmanager_marketplace->getMarketplaceItem($product_id, 1);
                if (!$marketplace || !isset($marketplace['quantity_listed'])) {
                    throw new \Exception("Product not listed on eBay");
                }
                
                $ebay_quantity = (int)$marketplace['quantity_listed'];
                $this->model_shopmanager_marketplace->updateProductQuantity($product_id, $ebay_quantity);
                $json['success'] = 'Local quantity updated from eBay: ' . $ebay_quantity;
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Sync Specifics To eBay - Envoie les specifics locaux vers eBay
     *
     * @return void
     */
    public function syncSpecificsToEbay(): void {
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $product_desc = $this->model_shopmanager_marketplace->getProductSpecifics($product_id, 1);
                if (!$product_desc || empty($product_desc['specifics'])) {
                    throw new \Exception("No local specifics found");
                }
                
                $marketplace = $this->model_shopmanager_marketplace->getMarketplaceItem($product_id, 1);
                if (!$marketplace) {
                    throw new \Exception("Product not listed on eBay");
                }
                
                $specifics = json_decode($product_desc['specifics'], true);
                $marketplace_item_id = $marketplace['marketplace_item_id'];
                $marketplace_account_id = $marketplace['marketplace_account_id'];
                
                $account_info = $this->model_shopmanager_marketplace->getMarketplaceAccount(['marketplace_account_id' => $marketplace_account_id], true);
                $site_settings = json_decode($account_info['site_setting'] ?? '{}', true);
                
                $response = $this->model_shopmanager_ebay->editSpecifics($marketplace_item_id, $specifics, $marketplace_account_id, $site_settings);
                
                if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                    $this->model_shopmanager_marketplace->updateMarketplaceSpecifics($product_id, $specifics);
                    $json['success'] = 'Specifics synced to eBay';
                } else {
                    $error_msg = $response['Errors']['ShortMessage'] ?? $response['Errors'][0]['ShortMessage'] ?? 'Update failed';
                    $json['error'] = $error_msg;
                }
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Sync Specifics From eBay - Récupère les specifics eBay vers local
     *
     * @return void
     */
    public function syncSpecificsFromEbay(): void {
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $marketplace = $this->model_shopmanager_marketplace->getMarketplaceItem($product_id, 1);
                if (!$marketplace || empty($marketplace['specifics'])) {
                    throw new \Exception("No eBay specifics found");
                }
                
                $ebay_specifics = $marketplace['specifics'];
                $this->model_shopmanager_marketplace->updateProductDescriptionSpecifics($product_id, $ebay_specifics, 1);
                $json['success'] = 'Local specifics updated from eBay';
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Refresh Item From eBay - Récupère les dernières infos d'un item depuis eBay (prix, quantité, specifics, dates)
     *
     * @return void
     */
    public function refreshItemFromEbay(): void {
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
        
        try {
            $this->load->model('shopmanager/ebay');
            
            // Get marketplace item ID from database using model
            $marketplace = $this->model_shopmanager_marketplace->getMarketplaceForRefresh($product_id);
            
            if (!$marketplace) {
                throw new \Exception("Product not found on eBay marketplace");
            }
            
            $marketplace_item_id = $marketplace['marketplace_item_id'];
            $marketplace_account_id = $marketplace['marketplace_account_id'];
            $is_com = $marketplace['is_com'];
            
            // Call eBay API to get item details
            $response = $this->model_shopmanager_ebay->getItem($marketplace_item_id, $marketplace_account_id);
            
            if (isset($response['error'])) {
                throw new \Exception("eBay API Error: " . $response['message']);
            }
            
            // Extract item data from response
            $item = isset($response[0]['Item']) ? $response[0]['Item'] : null;
            
            if (!$item) {
                throw new \Exception("No item data returned from eBay");
            }
            
            // Extract specifics from ItemSpecifics
            $specifics = '';
            if (isset($item['ItemSpecifics']['NameValueList'])) {
                $specifics_array = $item['ItemSpecifics']['NameValueList'];
                // If single specific, wrap in array
                if (isset($specifics_array['Name'])) {
                    $specifics_array = [$specifics_array];
                }
                $specifics_json = [];
                foreach ($specifics_array as $spec) {
                    $name = $spec['Name'];
                    $value = is_array($spec['Value']) ? implode(', ', $spec['Value']) : $spec['Value'];
                    $specifics_json[] = ['name' => $name, 'value' => $value];
                }
                $specifics = json_encode($specifics_json);
            }
            
            // Extract dates
            $date_added = null;
            $date_ended = null;
            
            if (isset($item['ListingDetails']['StartTime'])) {
                $date_added = date('Y-m-d H:i:s', strtotime($item['ListingDetails']['StartTime']));
            }
            
            if (isset($item['ListingDetails']['EndTime'])) {
                $date_ended = date('Y-m-d H:i:s', strtotime($item['ListingDetails']['EndTime']));
            }
            
            // Prepare update data
            $price = isset($item['SellingStatus']['CurrentPrice']) ? (float)$item['SellingStatus']['CurrentPrice'] : 0.00;
            $quantity_listed = isset($item['Quantity']) ? (int)$item['Quantity'] : 0;
            $quantity_sold = isset($item['SellingStatus']['QuantitySold']) ? (int)$item['SellingStatus']['QuantitySold'] : 0;
            $currency = isset($item['Currency']) ? $item['Currency'] : 'CAD';
            $category_id = isset($item['PrimaryCategory']['CategoryID']) ? (int)$item['PrimaryCategory']['CategoryID'] : 0;
            
            // Update product_marketplace table using model
            $marketplace_data = [
                'price' => (float)$price,
                'currency' => $currency,
                'quantity_listed' => (int)$quantity_listed,
                'quantity_sold' => (int)$quantity_sold,
                'category_id' => (int)$category_id,
                'specifics' => $specifics,
                'date_added' => $date_added,
                'date_ended' => $date_ended
            ];
            
            $this->model_shopmanager_marketplace->updateMarketplaceFullRefresh($product_id, $marketplace_data);
            
            $json['success'] = true;
            $json['message'] = 'Item refreshed from eBay successfully';
            $json['data'] = [
                'price' => $price,
                'currency' => $currency,
                'quantity_listed' => $quantity_listed,
                'quantity_sold' => $quantity_sold,
                'quantity_available' => $quantity_listed - $quantity_sold,
                'category_id' => $category_id,
                'date_added' => $date_added,
                'date_ended' => $date_ended,
                'specifics_updated' => !empty($specifics)
            ];
            
        } catch (\Exception $e) {
            $json['success'] = false;
            $json['error'] = $e->getMessage();
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * AJAX method to load Price Mismatch tab with pagination
     */
    public function getPriceMismatchTab(): void {
        $lang = $this->load->language('shopmanager/inventory/sync');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/inventory/sync');
        
        // Pagination parameters
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 20;
        $start = ($page - 1) * $limit;
        
        // Sorting parameters
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'product_id';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        
        // Get data with pagination
        $filter_data = [
            'start' => $start,
            'limit' => $limit,
            'sort' => $sort,
            'order' => $order
        ];
        
        $price_mismatch = $this->model_shopmanager_inventory_sync->getPriceMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalPriceMismatch();
        
        $data['price_mismatch'] = $price_mismatch;
        $data['price_mismatch_total'] = $total;
        $data['price_mismatch_page'] = $page;
        $data['price_mismatch_sort'] = $sort;
        $data['price_mismatch_order'] = $order;
        $data['price_mismatch_start'] = $start + 1;
        $data['price_mismatch_end'] = min($start + $limit, $total);
        $data['price_mismatch_num_pages'] = ceil($total / $limit);
        $data['price_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        $this->response->setOutput($this->load->view('shopmanager/inventory/sync_price_mismatch', $data));
    }
    
    /**
     * AJAX method to load Quantity Mismatch tab with pagination
     */
    public function getQtyMismatchTab(): void {
        $lang = $this->load->language('shopmanager/inventory/sync');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/inventory/sync');
        
        // Pagination parameters
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 20;
        $start = ($page - 1) * $limit;
        
        // Sorting parameters
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'product_id';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        
        // Get data with pagination
        $filter_data = [
            'start' => $start,
            'limit' => $limit,
            'sort' => $sort,
            'order' => $order
        ];
        
        $qty_mismatch = $this->model_shopmanager_inventory_sync->getQuantityMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalQuantityMismatch();
        
        $data['qty_mismatch'] = $qty_mismatch;
        $data['qty_mismatch_total'] = $total;
        $data['qty_mismatch_page'] = $page;
        $data['qty_mismatch_sort'] = $sort;
        $data['qty_mismatch_order'] = $order;
        $data['qty_mismatch_start'] = $start + 1;
        $data['qty_mismatch_end'] = min($start + $limit, $total);
        $data['qty_mismatch_num_pages'] = ceil($total / $limit);
        $data['qty_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        $this->response->setOutput($this->load->view('shopmanager/inventory/sync_qty_mismatch', $data));
    }
    
    /**
     * AJAX method to load Specifics Mismatch tab with pagination
     */
    public function getSpecificsMismatchTab(): void {
        $lang = $this->load->language('shopmanager/inventory/sync');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/inventory/sync');
        
        // Pagination parameters
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 20;
        $start = ($page - 1) * $limit;
        
        // Sorting parameters
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'product_id';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        
        // Get data with pagination
        $filter_data = [
            'start' => $start,
            'limit' => $limit,
            'sort' => $sort,
            'order' => $order
        ];
        
        $specifics_mismatch = $this->model_shopmanager_inventory_sync->getSpecificsMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalSpecificsMismatch();
        
        $data['specifics_mismatch'] = $specifics_mismatch;
        $data['specifics_mismatch_total'] = $total;
        $data['specifics_mismatch_page'] = $page;
        $data['specifics_mismatch_sort'] = $sort;
        $data['specifics_mismatch_order'] = $order;
        $data['specifics_mismatch_start'] = $start + 1;
        $data['specifics_mismatch_end'] = min($start + $limit, $total);
        $data['specifics_mismatch_num_pages'] = ceil($total / $limit);
        $data['specifics_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        $this->response->setOutput($this->load->view('shopmanager/inventory/sync_specifics_mismatch', $data));
    }
    
    /**
     * AJAX method to load Condition Mismatch tab with pagination
     */
    public function getConditionMismatchTab(): void {
        $lang = $this->load->language('shopmanager/inventory/sync');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/inventory/sync');
        
        // Pagination parameters
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 20;
        $start = ($page - 1) * $limit;
        
        // Sorting parameters
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'product_id';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        
        // Get data with pagination
        $filter_data = [
            'start' => $start,
            'limit' => $limit,
            'sort' => $sort,
            'order' => $order
        ];
        
        $condition_mismatch = $this->model_shopmanager_inventory_sync->getConditionMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalConditionMismatch();
        
        $data['condition_mismatch'] = $condition_mismatch;
        $data['condition_mismatch_total'] = $total;
        $data['condition_mismatch_page'] = $page;
        $data['condition_mismatch_sort'] = $sort;
        $data['condition_mismatch_order'] = $order;
        $data['condition_mismatch_start'] = $start + 1;
        $data['condition_mismatch_end'] = min($start + $limit, $total);
        $data['condition_mismatch_num_pages'] = ceil($total / $limit);
        $data['condition_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        $this->response->setOutput($this->load->view('shopmanager/inventory/sync_condition_mismatch', $data));
    }
    
    /**
     * AJAX method to load Category Mismatch tab with pagination
     */
    public function getCategoryMismatchTab(): void {
        $lang = $this->load->language('shopmanager/inventory/sync');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/inventory/sync');
        
        // Pagination parameters
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 20;
        $start = ($page - 1) * $limit;
        
        // Sorting parameters
        $sort = isset($this->request->get['sort']) ? $this->request->get['sort'] : 'product_id';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';
        
        // Get data with pagination
        $filter_data = [
            'start' => $start,
            'limit' => $limit,
            'sort' => $sort,
            'order' => $order
        ];
        
        $category_mismatch = $this->model_shopmanager_inventory_sync->getCategoryMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalCategoryMismatch();
        
        $data['category_mismatch'] = $category_mismatch;
        $data['category_mismatch_total'] = $total;
        $data['category_mismatch_page'] = $page;
        $data['category_mismatch_sort'] = $sort;
        $data['category_mismatch_order'] = $order;
        $data['category_mismatch_start'] = $start + 1;
        $data['category_mismatch_end'] = min($start + $limit, $total);
        $data['category_mismatch_num_pages'] = ceil($total / $limit);
        $data['category_mismatch_pagination'] = $total > $limit;
        
        $data['user_token'] = $this->session->data['user_token'];
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_product'] = ($lang['column_product'] ?? '');
        $data['column_ebay_id'] = ($lang['column_ebay_id'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_actions'] = ($lang['column_actions'] ?? '');
        
        $this->response->setOutput($this->load->view('shopmanager/inventory/sync_category_mismatch', $data));
    }
    
    /**
     * Sync Category To eBay - Export single category
     */
    public function syncCategoryToEbay(): void {
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        $this->load->model('shopmanager/inventory/sync');
        
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $result = $this->model_shopmanager_inventory_sync->exportCategoryToEbay($product_id);
                $json = $result;
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Bulk Sync Categories To eBay - Export multiple categories
     */
    public function syncCategoryBulkToEbay(): void {
        $this->load->model('shopmanager/inventory/sync');
        
        $json = [];
        $product_ids = $this->request->post['product_ids'] ?? [];
        
        if (empty($product_ids) || !is_array($product_ids)) {
            $json['error'] = 'No products selected';
        } else {
            try {
                $result = $this->model_shopmanager_inventory_sync->exportCategoriesToEbayBulk($product_ids);
                $json = $result;
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Sync Category From eBay - Import single category
     */
    public function syncCategoryFromEbay(): void {
        $this->load->model('shopmanager/marketplace');
        $this->load->model('shopmanager/inventory/sync');
        
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $result = $this->model_shopmanager_inventory_sync->importCategoryFromEbay($product_id);
                $json = $result;
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Bulk Sync Categories From eBay - Import multiple categories
     */
    public function syncCategoryBulkFromEbay(): void {
        $this->load->model('shopmanager/inventory/sync');
        
        $json = [];
        $product_ids = $this->request->post['product_ids'] ?? [];
        
        if (empty($product_ids) || !is_array($product_ids)) {
            $json['error'] = 'No products selected';
        } else {
            try {
                $result = $this->model_shopmanager_inventory_sync->importCategoriesFromEbayBulk($product_ids);
                $json = $result;
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
