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
        $data['button_force_refresh'] = ($lang['button_force_refresh'] ?? '');

        // Tooltips
        $data['tooltip_import_from_ebay'] = ($lang['tooltip_import_from_ebay'] ?? '');
        $data['tooltip_refresh_item'] = ($lang['tooltip_refresh_item'] ?? '');
        $data['tooltip_force_refresh'] = ($lang['tooltip_force_refresh'] ?? '');

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
        $data['text_confirm_force_refresh'] = ($lang['text_confirm_force_refresh'] ?? '');
        $data['button_scan_image_backup']    = ($lang['button_scan_image_backup'] ?? '');
        $data['tooltip_scan_image_backup']   = ($lang['tooltip_scan_image_backup'] ?? '');
        $data['text_scan_backup_confirm']    = ($lang['text_scan_backup_confirm'] ?? '');

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
        $data['image_mismatch_content'] = $this->getImageMismatchContent();

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
     * Get initial Image Count Mismatch tab content (OC vs eBay)
     */
    private function getImageMismatchContent(): string {
        $this->load->model('shopmanager/inventory/sync');
        $lang = $this->load->language('shopmanager/inventory/sync');

        $limit = 20;
        $filter_data = ['start' => 0, 'limit' => $limit, 'sort' => 'product_id', 'order' => 'ASC'];

        $image_mismatch = $this->model_shopmanager_inventory_sync->getImageMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalImageMismatch();

        $backup_mismatch = $this->model_shopmanager_inventory_sync->getImageBackupMismatch($filter_data);
        $backup_total    = $this->model_shopmanager_inventory_sync->getTotalImageBackupMismatch();

        $data['image_mismatch']            = $image_mismatch;
        $data['image_mismatch_total']      = $total;
        $data['image_mismatch_page']       = 1;
        $data['image_mismatch_sort']       = 'product_id';
        $data['image_mismatch_order']      = 'ASC';
        $data['image_mismatch_start']      = 1;
        $data['image_mismatch_end']        = min($limit, $total);
        $data['image_mismatch_num_pages']  = ceil($total / $limit);
        $data['image_mismatch_pagination'] = $total > $limit;

        $data['backup_mismatch']            = $backup_mismatch;
        $data['backup_mismatch_total']      = $backup_total;
        $data['backup_mismatch_page']       = 1;
        $data['backup_mismatch_sort']       = 'product_id';
        $data['backup_mismatch_order']      = 'ASC';
        $data['backup_mismatch_start']      = 1;
        $data['backup_mismatch_end']        = min($limit, $backup_total);
        $data['backup_mismatch_num_pages']  = ceil($backup_total / $limit);
        $data['backup_mismatch_pagination'] = $backup_total > $limit;

        $data['user_token'] = $this->session->data['user_token'];
        $new_keys = ['text_backup_table_title','text_backup_table_info','button_open_backup_popup',
                     'text_popup_backup_title','button_transfer_to_oc','button_delete_from_backup',
                     'text_backup_select_all','text_backup_no_files','text_backup_already_in_oc',
                     'text_backup_type_primary','text_backup_type_secondary','column_backup_extra',
                     'text_backup_transferred','text_backup_deleted','text_backup_confirm_delete'];
        foreach (array_merge(['column_product_id','column_product','column_ebay_id','column_location',
                  'column_oc_images','column_ebay_images','column_diff','column_actions',
                  'text_image_mismatch_info','text_no_results','button_refresh',
                  'button_close','button_bulk_fix_images','button_fix_single_image',
                  'text_bulk_fix_tooltip','text_bulk_fix_confirm','text_bulk_fix_modal_title',
                  'text_bulk_fix_processing','text_bulk_fix_imported','text_bulk_fix_skipped',
                  'text_bulk_fix_errors','text_bulk_fix_reset_info','text_bulk_fix_error_details',
                  'column_backup_images','text_backup_not_scanned','button_scan_image_backup',
                  'tooltip_scan_image_backup','text_scan_backup_confirm'], $new_keys) as $key) {
            $data[$key] = $lang[$key] ?? '';
        }

        return $this->load->view('shopmanager/inventory/sync_image_mismatch', $data);
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
        $data['image_mismatch_count'] = $this->model_shopmanager_inventory_sync->getTotalImageMismatch();

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

        file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - Models loaded\n", FILE_APPEND);
        
        $json = [];

        try {
            // Get parameters
            $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
            $offset = isset($this->request->get['offset']) ? max(0, (int)$this->request->get['offset']) : 0;
            $batch_size = 20; // max items traités par request (pour éviter timeout 502)
            $marketplace_account_id = isset($this->request->get['account_id']) ? (int)$this->request->get['account_id'] : 1;
            $force_refresh = !empty($this->request->get['force_refresh']); // Force GetItem even if data already exists
            $limit = 200; // eBay allows up to 200 per page for GetMyeBaySelling

            file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - Page: $page, Account: $marketplace_account_id\n", FILE_APPEND);

            // Use GetMyeBaySelling instead - more efficient for bulk sync
            $response = $this->model_shopmanager_ebay->getMyeBaySellingBulk($page, $marketplace_account_id);
            
            file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - API response received\n", FILE_APPEND);

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
            
            file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - Found " . count($items) . " items (offset=$offset, batch=$batch_size)\n", FILE_APPEND);
            
            // Calcul pagination eBay (pour total_pages)
            $total_pages = 1;
            $total_entries = 0;
            if (isset($response['ActiveList']['PaginationResult'])) {
                $total_pages = isset($response['ActiveList']['PaginationResult']['TotalNumberOfPages'])
                    ? (int)$response['ActiveList']['PaginationResult']['TotalNumberOfPages'] : 1;
                $total_entries = isset($response['ActiveList']['PaginationResult']['TotalNumberOfEntries'])
                    ? (int)$response['ActiveList']['PaginationResult']['TotalNumberOfEntries'] : 0;
            }

            if (empty($items)) {
                $json['success'] = true;
                $json['completed'] = true;
                $json['page_complete'] = true;
                $json['message'] = 'Synchronization completed';
                $json['processed'] = 0;
                $json['page'] = $page;
            } else {
                $total_items_on_page = count($items);
                // Extraire seulement le batch courant
                $batch = array_slice($items, $offset, $batch_size);
                $processed = 0;

                // Process each item in the batch
                foreach ($batch as $item) {
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
                    $needs_getitem = $force_refresh || !$existing_data || 
                                    is_null($existing_data['category_id']) || 
                                    is_null($existing_data['condition_id']) || 
                                    is_null($existing_data['specifics']) ||
                                    empty($existing_data['ebay_image_count']);
                    
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
                        'last_import_time' => date('Y-m-d H:i:s'),
                        'ebay_image_count' => $this->extractEbayImageCount($item, $item_details),
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

                // Calcul batch/page completion
                $next_offset = $offset + $batch_size;
                $page_complete = ($next_offset >= $total_items_on_page);
                $completed = $page_complete && ($page >= $total_pages);

                file_put_contents('/home/n7f9655/import_test.txt', date('Y-m-d H:i:s') . " - Batch done: processed=$processed, offset=$offset, next=$next_offset, page_complete=" . ($page_complete?'true':'false') . ", completed=" . ($completed?'true':'false') . "\n", FILE_APPEND);

                $json['success'] = true;
                $json['completed'] = $completed;
                $json['page_complete'] = $page_complete;
                $json['next_offset'] = $next_offset;
                $json['processed'] = $processed;
                $json['page'] = $page;
                $json['total_pages'] = $total_pages;
                $json['total_entries'] = $total_entries;
                $json['force_refresh'] = $force_refresh;
                $json['message'] = ($force_refresh ? '[Force Refresh] ' : '') . "Processed $processed items (page $page/" . $total_pages . ", batch " . ($offset/$batch_size+1) . ")";
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
        $this->load->model('shopmanager/marketplace');
        $json = [];

        try {
            if (!isset($this->request->post['product_id'])) {
                throw new \Exception('Product ID required');
            }

            $product_id = (int)$this->request->post['product_id'];

            $response = $this->model_shopmanager_marketplace->editQuantityToMarketplace($product_id);

            $this->model_shopmanager_marketplace->updateMarketplaceLastSync($product_id);

            if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                $json['success'] = true;
                $json['message'] = 'Product #' . $product_id . ' synced successfully to eBay';
            } else {
                $error_msg = $response['Errors']['ShortMessage'] ?? $response['Errors'][0]['ShortMessage'] ?? 'Sync failed';
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
                    $this->model_shopmanager_marketplace->resetSyncState($product_id);
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
        $this->load->model('shopmanager/marketplace');
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
                $this->model_shopmanager_marketplace->resetSyncState($product_id);
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
        $this->load->model('shopmanager/marketplace');
        $json = [];
        $product_id = (int)($this->request->post['product_id'] ?? 0);

        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $response = $this->model_shopmanager_marketplace->editQuantityToMarketplace($product_id);

                if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                    $this->model_shopmanager_marketplace->resetSyncState($product_id);
                    $json['success'] = 'Quantity synced to eBay';
                } else {
                    $json['error'] = $response['Errors']['ShortMessage'] ?? $response['Errors'][0]['ShortMessage'] ?? 'Update failed';
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
        $this->load->model('shopmanager/marketplace');
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
                $this->model_shopmanager_marketplace->resetSyncState($product_id);
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
                    $this->model_shopmanager_marketplace->resetSyncState($product_id);
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
        $this->load->model('shopmanager/marketplace');
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
                $this->model_shopmanager_marketplace->resetSyncState($product_id);
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
     * Get Image Count Mismatch Tab - AJAX reload endpoint
     */
    public function getImageMismatchTab(): void {
        $lang = $this->load->language('shopmanager/inventory/sync');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/inventory/sync');

        // === eBay mismatch pagination ===
        $page  = isset($this->request->get['page'])  ? (int)$this->request->get['page']  : 1;
        $limit = 20;
        $start = ($page - 1) * $limit;
        $sort  = isset($this->request->get['sort'])  ? $this->request->get['sort']  : 'product_id';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';

        $filter_data = ['start' => $start, 'limit' => $limit, 'sort' => $sort, 'order' => $order];
        $image_mismatch = $this->model_shopmanager_inventory_sync->getImageMismatch($filter_data);
        $total = $this->model_shopmanager_inventory_sync->getTotalImageMismatch();

        $data['image_mismatch']            = $image_mismatch;
        $data['image_mismatch_total']      = $total;
        $data['image_mismatch_page']       = $page;
        $data['image_mismatch_sort']       = $sort;
        $data['image_mismatch_order']      = $order;
        $data['image_mismatch_start']      = $start + 1;
        $data['image_mismatch_end']        = min($start + $limit, $total);
        $data['image_mismatch_num_pages']  = ceil($total / $limit);
        $data['image_mismatch_pagination'] = $total > $limit;

        // === Backup mismatch pagination ===
        $bpage  = isset($this->request->get['bpage'])  ? (int)$this->request->get['bpage']  : 1;
        $bstart = ($bpage - 1) * $limit;
        $bsort  = isset($this->request->get['bsort'])  ? $this->request->get['bsort']  : 'product_id';
        $border = isset($this->request->get['border']) ? $this->request->get['border'] : 'ASC';

        $bfilter = ['start' => $bstart, 'limit' => $limit, 'sort' => $bsort, 'order' => $border];
        $backup_mismatch = $this->model_shopmanager_inventory_sync->getImageBackupMismatch($bfilter);
        $backup_total    = $this->model_shopmanager_inventory_sync->getTotalImageBackupMismatch();

        $data['backup_mismatch']            = $backup_mismatch;
        $data['backup_mismatch_total']      = $backup_total;
        $data['backup_mismatch_page']       = $bpage;
        $data['backup_mismatch_sort']       = $bsort;
        $data['backup_mismatch_order']      = $border;
        $data['backup_mismatch_start']      = $bstart + 1;
        $data['backup_mismatch_end']        = min($bstart + $limit, $backup_total);
        $data['backup_mismatch_num_pages']  = ceil($backup_total / $limit);
        $data['backup_mismatch_pagination'] = $backup_total > $limit;

        $data['user_token'] = $this->session->data['user_token'];
        $new_keys = ['text_backup_table_title','text_backup_table_info','button_open_backup_popup',
                     'text_popup_backup_title','button_transfer_to_oc','button_delete_from_backup',
                     'text_backup_select_all','text_backup_no_files','text_backup_already_in_oc',
                     'text_backup_type_primary','text_backup_type_secondary','column_backup_extra',
                     'text_backup_transferred','text_backup_deleted','text_backup_confirm_delete'];
        foreach (array_merge(['column_product_id','column_product','column_ebay_id','column_location',
                  'column_oc_images','column_ebay_images','column_diff','column_actions',
                  'text_image_mismatch_info','text_no_results','button_refresh',
                  'button_close','button_bulk_fix_images','button_fix_single_image',
                  'text_bulk_fix_tooltip','text_bulk_fix_confirm','text_bulk_fix_modal_title',
                  'text_bulk_fix_processing','text_bulk_fix_imported','text_bulk_fix_skipped',
                  'text_bulk_fix_errors','text_bulk_fix_reset_info','text_bulk_fix_error_details',
                  'column_backup_images','text_backup_not_scanned','button_scan_image_backup',
                  'tooltip_scan_image_backup','text_scan_backup_confirm'], $new_keys) as $key) {
            $data[$key] = $lang[$key] ?? '';
        }

        $this->response->setOutput($this->load->view('shopmanager/inventory/sync_image_mismatch', $data));
    }

    /**
     * Scan image_backup directory to count backup images per product.
     * Updates oc_product_marketplace.image_backup_count.
     * Called via AJAX button "Scan image_backup".
     */
    public function scanImageBackupCounts(): void {
        $this->load->model('shopmanager/inventory/sync');

        $json = [];
        try {
            // DIR_OPENCART is the project root (with trailing slash)
            $backup_dir = DIR_OPENCART . 'image_backup/';

            if (!is_dir($backup_dir)) {
                $json['error'] = 'image_backup directory not found: ' . $backup_dir;
                $this->response->addHeader('Content-Type: application/json');
                $this->response->setOutput(json_encode($json));
                return;
            }

            $stats = $this->model_shopmanager_inventory_sync->scanImageBackupCounts($backup_dir);

            $json['success']     = true;
            $json['scanned']     = $stats['scanned'];
            $json['with_images'] = $stats['with_images'];
            $json['empty']       = $stats['empty'];
            $json['not_found']   = $stats['not_found'];
            $json['message']     = sprintf(
                '%d products scanned — %d with backup images, %d empty dirs, %d not found in backup',
                $stats['scanned'], $stats['with_images'], $stats['empty'], $stats['not_found']
            );
        } catch (\Exception $e) {
            $json['error'] = $e->getMessage();
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Get list of images in image_backup/data/product/{product_id}/
     * Returns JSON array of file info for the backup popup.
     */
    public function getBackupImagesList(): void {
        ini_set('display_errors', '0');
        $this->response->addHeader('Content-Type: application/json');
        $json = [];
        try {
        $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;

        if (!$product_id) {
            $json['error'] = 'Missing product_id';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $backup_dir = DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/';
        if (!is_dir($backup_dir)) {
            $json['files'] = [];
            $json['success'] = true;
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $files = [];
        foreach (scandir($backup_dir) as $file) {
            if ($file === '.' || $file === '..') continue;
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_ext)) continue;

            $subfolder = substr((string)$product_id, 0, 2);
            $oc_rel_path = 'catalog/product/' . $subfolder . '/' . $product_id . '/' . $file;
            $oc_exists = file_exists(DIR_IMAGE . $oc_rel_path);

            // Determine type (pri/sec) from filename pattern {product_id}priN or {product_id}secN
            $type = 'unknown';
            if (preg_match('/^' . preg_quote((string)$product_id) . 'pri\d+/', pathinfo($file, PATHINFO_FILENAME))) {
                $type = 'primary';
            } elseif (preg_match('/^' . preg_quote((string)$product_id) . 'sec\d+/', pathinfo($file, PATHINFO_FILENAME))) {
                $type = 'secondary';
            }

            set_error_handler(function() { return true; });
            $img_info = is_readable($backup_dir . $file) ? getimagesize($backup_dir . $file) : false;
            restore_error_handler();
            $files[] = [
                'filename'   => $file,
                'type'       => $type,
                'oc_exists'  => $oc_exists,
                'oc_path'    => $oc_rel_path,
                'backup_url' => HTTP_CATALOG . 'image_backup/data/product/' . $product_id . '/' . rawurlencode($file),
                'size'       => filesize($backup_dir . $file),
                'width'      => $img_info ? $img_info[0] : 0,
                'height'     => $img_info ? $img_info[1] : 0,
            ];
        }

        // Sort: primary first, then secondary
        usort($files, function($a, $b) {
            if ($a['type'] === $b['type']) return strcmp($a['filename'], $b['filename']);
            return ($a['type'] === 'primary') ? -1 : 1;
        });

        $json['success'] = true;
        $json['product_id'] = $product_id;
        $json['files'] = $files;
        } catch (\Exception $e) {
            $json['error'] = 'getBackupImagesList: ' . $e->getMessage();
        }
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Transfer selected backup images to OC (copy file + insert into DB).
     * POST: product_id, filenames[] (array)
     */
    public function transferBackupImages(): void {
        ini_set('display_errors', '0');
        $this->response->addHeader('Content-Type: application/json');
        $this->load->model('shopmanager/inventory/sync');
        $json = [];
        try {
        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        $filenames  = isset($this->request->post['filenames']) ? (array)$this->request->post['filenames'] : [];

        if (!$product_id || empty($filenames)) {
            $json['error'] = 'Missing product_id or filenames';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $backup_dir  = DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/';
        $subfolder   = substr((string)$product_id, 0, 2);
        $dest_rel    = 'catalog/product/' . $subfolder . '/' . $product_id . '/';
        $dest_dir    = DIR_IMAGE . $dest_rel;

        if (!is_dir($dest_dir)) {
            mkdir($dest_dir, 0755, true);
        }

        $transferred = 0;
        $skipped     = 0;
        $errors      = [];

        // Get current primary image for this product
        $prod_query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . $product_id . "'");
        $current_main = $prod_query->row['image'] ?? '';

        // Get max sort_order in product_image
        $sort_query = $this->db->query("SELECT MAX(sort_order) as max_sort FROM " . DB_PREFIX . "product_image WHERE product_id = '" . $product_id . "'");
        $next_sort = (int)($sort_query->row['max_sort'] ?? 0) + 1;

        foreach ($filenames as $raw_filename) {
            $filename = basename($raw_filename); // security: no path traversal
            $src = $backup_dir . $filename;
            $dst = $dest_dir . $filename;

            if (!file_exists($src)) {
                $errors[] = $filename . ' not found in backup';
                continue;
            }

            if (file_exists($dst)) {
                $skipped++;
                continue; // already there
            }

            if (!copy($src, $dst)) {
                $errors[] = 'Failed to copy ' . $filename;
                continue;
            }

            $oc_path = $dest_rel . $filename;

            // Determine if primary or secondary
            $is_primary = preg_match('/^' . preg_quote((string)$product_id) . 'pri\d+/', pathinfo($filename, PATHINFO_FILENAME));

            if ($is_primary && (empty($current_main) || $current_main === 'no_image.png')) {
                // Set as main image
                $this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($oc_path) . "' WHERE product_id = '" . $product_id . "'");
                $current_main = $oc_path;
            } else {
                // Insert as secondary image
                $check = $this->db->query("SELECT product_image_id FROM " . DB_PREFIX . "product_image WHERE product_id = '" . $product_id . "' AND image = '" . $this->db->escape($oc_path) . "'");
                if (!$check->num_rows) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . $product_id . "', image = '" . $this->db->escape($oc_path) . "', sort_order = '" . $next_sort . "'");
                    $next_sort++;
                }
            }

            $transferred++;
        }

        // Update image_backup_count in product_marketplace
        $new_count = 0;
        if (is_dir($backup_dir)) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            foreach (scandir($backup_dir) as $f) {
                if ($f !== '.' && $f !== '..' && in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allowed_ext)) {
                    $new_count++;
                }
            }
        }
        $this->db->query("UPDATE " . DB_PREFIX . "product_marketplace SET image_backup_count = '" . $new_count . "' WHERE product_id = '" . $product_id . "'");

        $json['success']     = true;
        $json['transferred'] = $transferred;
        $json['skipped']     = $skipped;
        $json['errors']      = $errors;
        $json['message']     = "$transferred image(s) transférée(s) dans OC.";
        } catch (\Exception $e) {
            $json['error'] = 'transferBackupImages: ' . $e->getMessage();
        }
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Delete selected backup images physically from image_backup/data/product/{id}/.
     * POST: product_id, filenames[] (array)
     */
    public function deleteBackupImages(): void {
        ini_set('display_errors', '0');
        $this->response->addHeader('Content-Type: application/json');
        $json = [];
        try {
        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        $filenames  = isset($this->request->post['filenames']) ? (array)$this->request->post['filenames'] : [];

        if (!$product_id || empty($filenames)) {
            $json['error'] = 'Missing product_id or filenames';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $backup_dir = DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/';
        $deleted = 0;
        $errors  = [];

        foreach ($filenames as $raw_filename) {
            $filename = basename($raw_filename);
            $path = $backup_dir . $filename;
            if (!file_exists($path)) { continue; }
            if (unlink($path)) {
                $deleted++;
            } else {
                $errors[] = 'Cannot delete ' . $filename;
            }
        }

        // Update image_backup_count
        $new_count = 0;
        if (is_dir($backup_dir)) {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            foreach (scandir($backup_dir) as $f) {
                if ($f !== '.' && $f !== '..' && in_array(strtolower(pathinfo($f, PATHINFO_EXTENSION)), $allowed_ext)) {
                    $new_count++;
                }
            }
        }
        $this->db->query("UPDATE " . DB_PREFIX . "product_marketplace SET image_backup_count = '" . $new_count . "' WHERE product_id = '" . $product_id . "'");

        $json['success'] = true;
        $json['deleted']  = $deleted;
        $json['errors']   = $errors;
        $json['message']  = "$deleted image(s) supprimée(s) du backup.";
        } catch (\Exception $e) {
            $json['error'] = 'deleteBackupImages: ' . $e->getMessage();
        }
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Returns both OC images and backup images for a product, with dimensions.
     * GET: product_id
     */
    public function getProductImagesFull(): void {
        ini_set('display_errors', '0');
        $this->response->addHeader('Content-Type: application/json');
        $json = [];
        try {
            $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
            if (!$product_id) { $json['error'] = 'Missing product_id'; $this->response->setOutput(json_encode($json)); return; }

            // ── OC images ─────────────────────────────────────────────────
            $oc_images = [];
            $prod = $this->db->query("SELECT image FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $product_id . "'");
            $main_path = $prod->row['image'] ?? '';
            if ($main_path && $main_path !== 'no_image.png' && $main_path !== '') {
                $abs  = DIR_IMAGE . $main_path;
                set_error_handler(function() { return true; });
                $info = is_readable($abs) ? getimagesize($abs) : false;
                restore_error_handler();
                $oc_images[] = ['role' => 'primary', 'image' => $main_path, 'url' => HTTP_CATALOG . 'image/' . $main_path, 'width' => $info ? $info[0] : 0, 'height' => $info ? $info[1] : 0];
            }
            $secs = $this->db->query("SELECT image, sort_order FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "' ORDER BY sort_order ASC");
            foreach ($secs->rows as $row) {
                $abs  = DIR_IMAGE . $row['image'];
                set_error_handler(function() { return true; });
                $info = is_readable($abs) ? getimagesize($abs) : false;
                restore_error_handler();
                $oc_images[] = ['role' => 'secondary', 'image' => $row['image'], 'url' => HTTP_CATALOG . 'image/' . $row['image'], 'width' => $info ? $info[0] : 0, 'height' => $info ? $info[1] : 0, 'sort_order' => (int)$row['sort_order']];
            }

            // ── Backup images ──────────────────────────────────────────────
            $backup_dir    = DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/';
            $backup_images = [];
            if (is_dir($backup_dir)) {
                $allowed_ext = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                foreach (scandir($backup_dir) as $file) {
                    if ($file === '.' || $file === '..') continue;
                    if (!in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), $allowed_ext)) continue;
                    $subfolder = substr((string)$product_id, 0, 2);
                    $oc_rel    = 'catalog/product/' . $subfolder . '/' . $product_id . '/' . $file;
                    $full_path = $backup_dir . $file;
                    set_error_handler(function() { return true; });
                    $info = is_readable($full_path) ? getimagesize($full_path) : false;
                    restore_error_handler();
                    $basename  = pathinfo($file, PATHINFO_FILENAME);
                    $type = 'unknown';
                    if (preg_match('/^' . preg_quote((string)$product_id) . 'pri\d+/', $basename)) $type = 'primary';
                    elseif (preg_match('/^' . preg_quote((string)$product_id) . 'sec\d+/', $basename)) $type = 'secondary';
                    $backup_images[] = ['filename' => $file, 'type' => $type, 'url' => HTTP_CATALOG . 'image_backup/data/product/' . $product_id . '/' . rawurlencode($file), 'oc_exists' => file_exists(DIR_IMAGE . $oc_rel), 'oc_path' => $oc_rel, 'width' => $info ? $info[0] : 0, 'height' => $info ? $info[1] : 0];
                }
                usort($backup_images, function($a, $b) {
                    if ($a['type'] === $b['type']) return strcmp($a['filename'], $b['filename']);
                    return ($a['type'] === 'primary') ? -1 : 1;
                });
            }

            $json['success']       = true;
            $json['product_id']    = $product_id;
            $json['oc_images']     = $oc_images;
            $json['backup_images'] = $backup_images;
        } catch (\Exception $e) {
            $json['error'] = 'getProductImagesFull: ' . $e->getMessage();
        }
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Remove an image from OpenCart (primary clears product.image, secondary deletes from product_image).
     * POST: product_id, image_path, role
     */
    public function removeOcImage(): void {
        ini_set('display_errors', '0');
        $this->response->addHeader('Content-Type: application/json');
        $json = [];
        try {
            $product_id = (int)($this->request->post['product_id'] ?? 0);
            $image_path = $this->request->post['image_path'] ?? '';
            $role       = $this->request->post['role'] ?? 'secondary';
            if (!$product_id || !$image_path) { $json['error'] = 'Missing params'; $this->response->setOutput(json_encode($json)); return; }
            if ($role === 'primary') {
                $this->db->query("UPDATE `" . DB_PREFIX . "product` SET image = '' WHERE product_id = '" . $product_id . "'");
            } else {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "' AND image = '" . $this->db->escape($image_path) . "'");
            }
            $this->db->query("UPDATE " . DB_PREFIX . "product_marketplace SET to_update = 1 WHERE product_id = '" . $product_id . "' AND marketplace_id = 1");
            $json['success'] = true;
        } catch (\Exception $e) { $json['error'] = 'removeOcImage: ' . $e->getMessage(); }
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Promote an OC secondary image to primary (demotes old primary to secondary).
     * Also renames files on disk: priN <-> secN in filename.
     * POST: product_id, image_path
     */
    public function setOcImagePrimary(): void {
        ini_set('display_errors', '0');
        $this->response->addHeader('Content-Type: application/json');
        $json = [];
        try {
            $product_id = (int)($this->request->post['product_id'] ?? 0);
            $image_path = $this->request->post['image_path'] ?? '';
            if (!$product_id || !$image_path) { $json['error'] = 'Missing params'; $this->response->setOutput(json_encode($json)); return; }

            // ── Helper: rename priN<->secN on disk, returns new relative path
            $swapRole = function(string $rel, string $from, string $to): string {
                $abs_dir  = DIR_IMAGE . dirname($rel) . '/';
                $file     = basename($rel);
                $ext      = pathinfo($file, PATHINFO_EXTENSION);
                $base     = pathinfo($file, PATHINFO_FILENAME);
                if (preg_match('/^(\d+)' . preg_quote($from, '/') . '(\d+)$/i', $base, $m)) {
                    $newfile = $m[1] . $to . $m[2] . '.' . $ext;
                    $newrel  = trim(dirname($rel), '/') . '/' . $newfile;
                    if (file_exists($abs_dir . $file) && !file_exists($abs_dir . $newfile)) {
                        rename($abs_dir . $file, $abs_dir . $newfile);
                    }
                    return file_exists(DIR_IMAGE . $newrel) ? $newrel : $rel;
                }
                return $rel;
            };

            $cur         = $this->db->query("SELECT image FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $product_id . "'");
            $old_primary = $cur->row['image'] ?? '';
            $new_old     = $old_primary;
            $new_pri     = $image_path;

            if ($old_primary && $old_primary !== $image_path) {
                // Rename files on disk
                $new_old = $swapRole($old_primary, 'pri', 'sec');
                $new_pri = $swapRole($image_path,  'sec', 'pri');

                // Remove stale DB path if file was renamed
                if ($new_old !== $old_primary) {
                    $this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "' AND image = '" . $this->db->escape($old_primary) . "'");
                }
                // Insert demoted primary as secondary
                $chk = $this->db->query("SELECT product_image_id FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "' AND image = '" . $this->db->escape($new_old) . "'");
                if (!$chk->num_rows) {
                    $sort = $this->db->query("SELECT MAX(sort_order) as m FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "'");
                    $next = (int)($sort->row['m'] ?? 0) + 1;
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "product_image` SET product_id = '" . $product_id . "', image = '" . $this->db->escape($new_old) . "', sort_order = '" . $next . "'");
                }
            }

            // Remove new primary from secondary table + set as main
            $this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "' AND image IN ('" . $this->db->escape($image_path) . "', '" . $this->db->escape($new_pri) . "')");
            $this->db->query("UPDATE `" . DB_PREFIX . "product` SET image = '" . $this->db->escape($new_pri) . "' WHERE product_id = '" . $product_id . "'");
            $this->db->query("UPDATE " . DB_PREFIX . "product_marketplace SET to_update = 1 WHERE product_id = '" . $product_id . "' AND marketplace_id = 1");
            $json['success']     = true;
            $json['new_primary'] = $new_pri;
        } catch (\Exception $e) { $json['error'] = 'setOcImagePrimary: ' . $e->getMessage(); }
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Move (not copy) one backup image to OC with explicit role (primary|secondary).
     * POST: product_id, filename, role
     */
    public function transferBackupAsRole(): void {
        ini_set('display_errors', '0');
        $this->response->addHeader('Content-Type: application/json');
        $json = [];
        try {
            $product_id = (int)($this->request->post['product_id'] ?? 0);
            $filename   = basename($this->request->post['filename'] ?? '');
            $role       = $this->request->post['role'] ?? 'secondary';
            if (!$product_id || !$filename) { $json['error'] = 'Missing params'; $this->response->setOutput(json_encode($json)); return; }

            $backup_dir = DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/';
            $subfolder  = substr((string)$product_id, 0, 2);
            $dest_rel   = 'catalog/product/' . $subfolder . '/' . $product_id . '/';
            $dest_dir   = DIR_IMAGE . $dest_rel;
            $src        = $backup_dir . $filename;

            if (!file_exists($src)) { $json['error'] = 'Backup file not found: ' . $filename; $this->response->setOutput(json_encode($json)); return; }
            if (!is_dir($dest_dir)) mkdir($dest_dir, 0755, true);

            $dst     = $dest_dir . $filename;
            $oc_path = $dest_rel . $filename;
            if (!file_exists($dst)) {
                if (!rename($src, $dst)) { $json['error'] = 'Failed to move file'; $this->response->setOutput(json_encode($json)); return; }
            } elseif (file_exists($src)) {
                @unlink($src); // Already exists in OC — just remove from backup
            }

            // Convert to WebP if not already
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if ($ext !== 'webp' && file_exists($dst)) {
                $webp_filename = substr($filename, 0, -strlen($ext)) . 'webp';
                $webp_dst      = $dest_dir . $webp_filename;
                set_error_handler(function() { return true; });
                $img = null;
                switch ($ext) {
                    case 'jpg': case 'jpeg': $img = imagecreatefromjpeg($dst); break;
                    case 'png':
                        $img = imagecreatefrompng($dst);
                        if ($img) {
                            // Preserve transparency (white bg for WebP)
                            $bg = imagecreatetruecolor(imagesx($img), imagesy($img));
                            imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
                            imagecopy($bg, $img, 0, 0, 0, 0, imagesx($img), imagesy($img));
                            imagedestroy($img);
                            $img = $bg;
                        }
                        break;
                    case 'gif': $img = imagecreatefromgif($dst); break;
                    case 'bmp': $img = imagecreatefrombmp($dst); break;
                }
                restore_error_handler();
                if ($img) {
                    if (imagewebp($img, $webp_dst, 85)) {
                        imagedestroy($img);
                        @unlink($dst); // Supprimer l'original non-webp
                        $filename = $webp_filename;
                        $dst      = $webp_dst;
                        $oc_path  = $dest_rel . $webp_filename;
                    } else {
                        imagedestroy($img); // Conversion échouée — garder l'original
                    }
                }
            }

            if ($role === 'primary') {
                $cur = $this->db->query("SELECT image FROM `" . DB_PREFIX . "product` WHERE product_id = '" . $product_id . "'");
                $old = $cur->row['image'] ?? '';
                if ($old && $old !== $oc_path) {
                    $chk = $this->db->query("SELECT product_image_id FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "' AND image = '" . $this->db->escape($old) . "'");
                    if (!$chk->num_rows) {
                        $sort = $this->db->query("SELECT MAX(sort_order) as m FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "'");
                        $next = (int)($sort->row['m'] ?? 0) + 1;
                        $this->db->query("INSERT INTO `" . DB_PREFIX . "product_image` SET product_id = '" . $product_id . "', image = '" . $this->db->escape($old) . "', sort_order = '" . $next . "'");
                    }
                }
                $this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "' AND image = '" . $this->db->escape($oc_path) . "'");
                $this->db->query("UPDATE `" . DB_PREFIX . "product` SET image = '" . $this->db->escape($oc_path) . "' WHERE product_id = '" . $product_id . "'");
            } else {
                $chk = $this->db->query("SELECT product_image_id FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "' AND image = '" . $this->db->escape($oc_path) . "'");
                if (!$chk->num_rows) {
                    $sort = $this->db->query("SELECT MAX(sort_order) as m FROM `" . DB_PREFIX . "product_image` WHERE product_id = '" . $product_id . "'");
                    $next = (int)($sort->row['m'] ?? 0) + 1;
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "product_image` SET product_id = '" . $product_id . "', image = '" . $this->db->escape($oc_path) . "', sort_order = '" . $next . "'");
                }
            }
            $this->db->query("UPDATE " . DB_PREFIX . "product_marketplace SET to_update = 1 WHERE product_id = '" . $product_id . "' AND marketplace_id = 1");
            $json['success'] = true;
            $json['oc_path'] = $oc_path;
        } catch (\Exception $e) { $json['error'] = 'transferBackupAsRole: ' . $e->getMessage(); }
        $this->response->setOutput(json_encode($json));
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
                if (!isset($json['error'])) {
                    $this->model_shopmanager_marketplace->resetSyncState($product_id);
                }
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
                if (!isset($json['error'])) {
                    $this->model_shopmanager_marketplace->resetSyncState($product_id);
                }
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

    /**
     * Extract eBay image count from GetMyeBaySelling item or GetItem details.
     * GetMyeBaySelling item has PictureDetails; GetItem response has raw_item with PictureDetails.
     *
     * @param array      $item         Item from GetMyeBaySelling
     * @param array|null $item_details Result of getItemDetails() — has 'raw_item' key if available
     * @return int
     */
    private function extractEbayImageCount(array $item, ?array $item_details): int {
        // Try GetItem response first (most complete — includes all PictureURL)
        if ($item_details && isset($item_details['image_count'])) {
            return (int)$item_details['image_count'];
        }
        // Fallback: GetMyeBaySelling item — PictureDetails rarely present in bulk, but check anyway
        if (isset($item['PictureDetails']['PictureURL'])) {
            $urls = $item['PictureDetails']['PictureURL'];
            return is_array($urls) ? count($urls) : (empty($urls) ? 0 : 1);
        }
        // GalleryURL means at least 1 image
        if (!empty($item['PictureDetails']['GalleryURL'])) {
            return 1;
        }
        return 0;
    }
}
