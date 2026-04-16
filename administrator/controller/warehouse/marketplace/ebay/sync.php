<?php
// Original: warehouse/marketplace/ebay/sync.php
namespace Opencart\Admin\Controller\Warehouse\Marketplace\Ebay;

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
        $this->load->language('warehouse/marketplace/ebay/sync');
        $data = [];
        
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        $this->document->addScript('view/javascript/warehouse/marketplace/ebay/sync.js?v=' . filemtime(DIR_APPLICATION . 'view/javascript/warehouse/marketplace/ebay/sync.js'));

        // Breadcrumbs
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('warehouse/marketplace/ebay/sync', 'user_token=' . $this->session->data['user_token'])
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
        $data['url_get_data'] = $this->url->link('warehouse/marketplace/ebay/sync.getData', 'user_token=' . $this->session->data['user_token']);
        
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
        $data['not_imported_content'] = $this->getNotImportedContent();
        $data['to_update_content']    = $this->getToUpdateContent();
        $data['not_synced_content']   = $this->getNotSyncedContent();

        // Pagination AJAX URLs
        $data['url_not_imported_tab'] = $this->url->link('warehouse/marketplace/ebay/sync.getNotImportedTab', 'user_token=' . $this->session->data['user_token']);
        $data['url_to_update_tab']    = $this->url->link('warehouse/marketplace/ebay/sync.getToUpdateTab',    'user_token=' . $this->session->data['user_token']);
        $data['url_not_synced_tab']   = $this->url->link('warehouse/marketplace/ebay/sync.getNotSyncedTab',   'user_token=' . $this->session->data['user_token']);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync', $data));
    }
    
    /**
     * Get initial Price Mismatch tab content
     */
    private function getPriceMismatchContent(): string {
        $this->load->model('warehouse/marketplace/ebay/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $price_mismatch = $this->model_warehouse_marketplace_ebay_sync->getPriceMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalPriceMismatch();
        
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
        
        return $this->load->view('warehouse/marketplace/ebay/sync_price_mismatch', $data);
    }
    
    /**
     * Get initial Quantity Mismatch tab content
     */
    private function getQtyMismatchContent(): string {
        $this->load->model('warehouse/marketplace/ebay/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $qty_mismatch = $this->model_warehouse_marketplace_ebay_sync->getQuantityMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalQuantityMismatch();
        
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
        
        return $this->load->view('warehouse/marketplace/ebay/sync_qty_mismatch', $data);
    }
    
    /**
     * Get initial Specifics Mismatch tab content
     */
    private function getSpecificsMismatchContent(): string {
        $this->load->model('warehouse/marketplace/ebay/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $specifics_mismatch = $this->model_warehouse_marketplace_ebay_sync->getSpecificsMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalSpecificsMismatch();
        
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
        
        return $this->load->view('warehouse/marketplace/ebay/sync_specifics_mismatch', $data);
    }
    
    /**
     * Get initial Condition Mismatch tab content
     */
    private function getConditionMismatchContent(): string {
        $this->load->model('warehouse/marketplace/ebay/sync');
        
        $limit = 20;
        $filter_data = [
            'start' => 0,
            'limit' => $limit,
            'sort' => 'product_id',
            'order' => 'ASC'
        ];
        
        $condition_mismatch = $this->model_warehouse_marketplace_ebay_sync->getConditionMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalConditionMismatch();
        
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
        
        return $this->load->view('warehouse/marketplace/ebay/sync_condition_mismatch', $data);
    }
    
    /**
     * Get initial Category Mismatch tab content
     */
    private function getCategoryMismatchContent(): string {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/marketplace/ebay/sync');

        $limit    = 50;
        $all      = $this->model_warehouse_marketplace_ebay_sync->getAllCategoryMismatchCandidates();
        $filtered = $this->getFilteredCategoryMismatches($all);
        $total    = count($filtered);
        $rows     = array_slice($filtered, 0, $limit);

        $data = [];
        $data['category_mismatch']            = $rows;
        $data['category_mismatch_total']      = $total;
        $data['category_mismatch_page']       = 1;
        $data['category_mismatch_sort']       = 'product_id';
        $data['category_mismatch_order']      = 'ASC';
        $data['category_mismatch_start']      = $total > 0 ? 1 : 0;
        $data['category_mismatch_end']        = min($limit, $total);
        $data['category_mismatch_num_pages']  = ceil($total / $limit);
        $data['category_mismatch_pagination'] = $total > $limit;
        $data['user_token']                   = $this->session->data['user_token'];

        return $this->load->view('warehouse/marketplace/ebay/sync_category_mismatch', $data);
    }

    /**
     * Enrichit chaque ligne de getCategoryMismatch avec la meilleure catégorie
     * trouvée dans oc_product_info_sources (champ ebay_category JSON).
     * Ajoute les clés : source_category_id, source_category_name, source_percent
     */
    private function enrichCategoryRowsWithInfoSource(array &$rows): void {
        if (empty($rows)) return;

        // Collect unique UPCs
        $upcs = array_filter(array_unique(array_column($rows, 'upc')));
        if (empty($upcs)) return;

        $this->load->model('warehouse/marketplace/ebay/sync');
        $map = $this->model_warehouse_marketplace_ebay_sync->getBestCategoryByUpcs(array_values($upcs));

        // Merge into rows
        foreach ($rows as &$row) {
            $info = $map[$row['upc'] ?? ''] ?? null;
            $row['source_category_id']   = $info['source_category_id']   ?? null;
            $row['source_category_name'] = $info['source_category_name'] ?? null;
            $row['source_percent']       = $info['source_percent']       ?? null;
        }
        unset($row);
    }

    /**
     * Filter category mismatch candidates with full business logic:
     *
     * Condition 1 (infosource): source has a best-% category AND
     *   source_category_id != local_leaf_category_id  OR
     *   source_category_id != ebay_category_id
     *
     * Condition 2 (eBay vs local): ebay_category_id != local_leaf_category_id,
     *   or one is null while the other is not.
     *
     * Enriches rows with infosource data before filtering (sets source_* keys).
     */
    private function getFilteredCategoryMismatches(array $rows): array {
        $this->enrichCategoryRowsWithInfoSource($rows);
        return array_values(array_filter($rows, function ($row) {
            $local  = isset($row['local_category_id'])  && $row['local_category_id']  !== null ? (int)$row['local_category_id']  : null;
            $ebay   = isset($row['ebay_category_id'])   && $row['ebay_category_id']   !== null ? (int)$row['ebay_category_id']   : null;
            $source = isset($row['source_category_id']) && $row['source_category_id'] !== null ? (int)$row['source_category_id'] : null;

            // Condition 2: eBay category != local leaf category
            if ($local !== null && $ebay !== null && $local !== $ebay) return true;
            if ($local === null && $ebay !== null) return true;
            if ($local !== null && $ebay === null) return true;

            // Condition 1: infosource best-% category doesn't match local OR eBay
            if ($source !== null) {
                if ($source !== $local || $source !== $ebay) return true;
            }

            return false;
        }));
    }

    /**
     * Compute the true category mismatch total (PHP-filtered, both conditions).
     */
    private function computeCategoryMismatchTotal(): int {
        $this->load->model('warehouse/marketplace/ebay/sync');
        $all = $this->model_warehouse_marketplace_ebay_sync->getAllCategoryMismatchCandidates();
        return count($this->getFilteredCategoryMismatches($all));
    }

    /**
     * Get initial Image Count Mismatch tab content (OC vs eBay)
     */
    private function getImageMismatchContent(): string {
        $this->load->model('warehouse/marketplace/ebay/sync');
        $this->load->language('warehouse/marketplace/ebay/sync');

        $limit = 20;
        $filter_data = ['start' => 0, 'limit' => $limit, 'sort' => 'product_id', 'order' => 'ASC'];

        $image_mismatch = $this->model_warehouse_marketplace_ebay_sync->getImageMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalImageMismatch();

        $backup_mismatch = $this->model_warehouse_marketplace_ebay_sync->getImageBackupMismatch($filter_data);
        $backup_total    = $this->model_warehouse_marketplace_ebay_sync->getTotalImageBackupMismatch();

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

        $resolution_upgrade       = $this->model_warehouse_marketplace_ebay_sync->getResolutionUpgradeMismatch(['start' => 0, 'limit' => $limit, 'sort' => 'backup_max_width', 'order' => 'DESC']);
        $resolution_upgrade_total = $this->model_warehouse_marketplace_ebay_sync->getTotalResolutionUpgradeMismatch();
        $data['resolution_upgrade']            = $resolution_upgrade;
        $data['resolution_upgrade_total']      = $resolution_upgrade_total;
        $data['resolution_upgrade_page']       = 1;
        $data['resolution_upgrade_sort']       = 'backup_max_width';
        $data['resolution_upgrade_order']      = 'DESC';
        $data['resolution_upgrade_start']      = 1;
        $data['resolution_upgrade_end']        = min($limit, $resolution_upgrade_total);
        $data['resolution_upgrade_num_pages']  = ceil($resolution_upgrade_total / $limit);
        $data['resolution_upgrade_pagination'] = $resolution_upgrade_total > $limit;

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

        return $this->load->view('warehouse/marketplace/ebay/sync_image_mismatch', $data);
    }

    /**
     * Get initial Not Imported tab content
     */
    private function getNotImportedContent(): string {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/marketplace/ebay/sync');

        $limit = 50;
        $filter_data = ['start' => 0, 'limit' => $limit, 'sort' => 'product_id', 'order' => 'ASC'];

        $rows  = $this->model_warehouse_marketplace_ebay_sync->getNotImported($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalNotImported();

        $data = [];
        $data['not_imported']            = $rows;
        $data['not_imported_total']      = $total;
        $data['not_imported_page']       = 1;
        $data['not_imported_sort']       = 'product_id';
        $data['not_imported_order']      = 'ASC';
        $data['not_imported_start']      = $total > 0 ? 1 : 0;
        $data['not_imported_end']        = min($limit, $total);
        $data['not_imported_num_pages']  = ceil($total / $limit);
        $data['not_imported_pagination'] = $total > $limit;
        $data['user_token'] = $this->session->data['user_token'];

        return $this->load->view('warehouse/marketplace/ebay/sync_not_imported', $data);
    }

    /**
     * Get initial To Update tab content
     */
    private function getToUpdateContent(): string {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/marketplace/ebay/sync');

        $limit = 50;
        $filter_data = ['start' => 0, 'limit' => $limit, 'sort' => 'product_id', 'order' => 'ASC'];

        $rows  = $this->model_warehouse_marketplace_ebay_sync->getToUpdate($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalToUpdate();

        $data = [];
        $data['to_update']            = $rows;
        $data['to_update_total']      = $total;
        $data['to_update_page']       = 1;
        $data['to_update_sort']       = 'product_id';
        $data['to_update_order']      = 'ASC';
        $data['to_update_start']      = $total > 0 ? 1 : 0;
        $data['to_update_end']        = min($limit, $total);
        $data['to_update_num_pages']  = ceil($total / $limit);
        $data['to_update_pagination'] = $total > $limit;
        $data['user_token'] = $this->session->data['user_token'];

        return $this->load->view('warehouse/marketplace/ebay/sync_to_update', $data);
    }

    /**
     * Get initial Not Synced tab content
     */
    private function getNotSyncedContent(): string {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/marketplace/ebay/sync');

        $limit = 50;
        $filter_data = ['start' => 0, 'limit' => $limit, 'sort' => 'product_id', 'order' => 'ASC'];

        $rows  = $this->model_warehouse_marketplace_ebay_sync->getProductsNotSynced($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalNotSynced();

        $data = [];
        $data['not_synced']            = $rows;
        $data['not_synced_total']      = $total;
        $data['not_synced_page']       = 1;
        $data['not_synced_start']      = $total > 0 ? 1 : 0;
        $data['not_synced_end']        = min($limit, $total);
        $data['not_synced_num_pages']  = ceil($total / $limit);
        $data['not_synced_pagination'] = $total > $limit;
        $data['user_token'] = $this->session->data['user_token'];

        return $this->load->view('warehouse/marketplace/ebay/sync_not_synced', $data);
    }

    /**
     * AJAX: paginate Not Synced tab
     */
    public function getNotSyncedTab(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/marketplace/ebay/sync');

        $page  = max(1, (int)($this->request->get['page'] ?? 1));
        $limit = 50;
        $start = ($page - 1) * $limit;

        $filter_data = ['start' => $start, 'limit' => $limit, 'sort' => 'product_id', 'order' => 'ASC'];

        $rows  = $this->model_warehouse_marketplace_ebay_sync->getProductsNotSynced($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalNotSynced();

        $data = [];
        $data['not_synced']            = $rows;
        $data['not_synced_total']      = $total;
        $data['not_synced_page']       = $page;
        $data['not_synced_start']      = $start + 1;
        $data['not_synced_end']        = min($start + $limit, $total);
        $data['not_synced_num_pages']  = ceil($total / $limit);
        $data['not_synced_pagination'] = $total > $limit;
        $data['user_token'] = $this->session->data['user_token'];

        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_not_synced', $data));
    }

    /**
     * Get Data - AJAX endpoint pour rafraîchir les données
     *
     * @return void
     */
    public function getData(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $data = [];
        

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
        $this->load->model('warehouse/marketplace/ebay/sync');

        $data = [];

        // Inventory Stats
        $data['total_listed_ebay'] = $this->model_warehouse_marketplace_ebay_sync->getTotalListedOnEbay();
        $data['not_imported_count'] = $this->model_warehouse_marketplace_ebay_sync->getTotalNotImported();
        $data['to_update_count']    = $this->model_warehouse_marketplace_ebay_sync->getTotalToUpdate();

        // Sync Issues
        $data['products_with_errors'] = $this->model_warehouse_marketplace_ebay_sync->getProductsWithErrors();
        $data['products_not_listed'] = $this->model_warehouse_marketplace_ebay_sync->getProductsNotListed();
        $data['quantity_mismatch'] = $this->model_warehouse_marketplace_ebay_sync->getQuantityMismatch();

        // Error summary for dashboard
        $data['error_summary'] = $this->model_warehouse_marketplace_ebay_sync->getErrorSummary();

        // Counts for dashboard
        $data['error_count'] = count($data['products_with_errors']);
        $data['not_synced_count'] = $this->model_warehouse_marketplace_ebay_sync->getTotalNotSynced();
        $data['not_listed_count'] = count($data['products_not_listed']);
        $data['mismatch_count'] = count($data['quantity_mismatch']);
        
        // Counts for separate mismatch types - use getTotalXXX methods instead of loading all data
        $data['price_mismatch_count'] = $this->model_warehouse_marketplace_ebay_sync->getTotalPriceMismatch();
        $data['qty_mismatch_count'] = $this->model_warehouse_marketplace_ebay_sync->getTotalQuantityMismatch();
        $data['specifics_mismatch_count'] = $this->model_warehouse_marketplace_ebay_sync->getTotalSpecificsMismatch();
        $data['condition_mismatch_count'] = $this->model_warehouse_marketplace_ebay_sync->getTotalConditionMismatch();
        $data['category_mismatch_count'] = $this->computeCategoryMismatchTotal();
        $data['image_mismatch_count']          = $this->model_warehouse_marketplace_ebay_sync->getTotalImageMismatch();
        $data['backup_image_mismatch_count']   = $this->model_warehouse_marketplace_ebay_sync->getTotalImageBackupMismatch();
        $data['resolution_upgrade_count']      = $this->model_warehouse_marketplace_ebay_sync->getTotalResolutionUpgradeMismatch();
        $data['total_mismatch_count']          = $data['price_mismatch_count']
                                               + $data['qty_mismatch_count']
                                               + $data['specifics_mismatch_count']
                                               + $data['condition_mismatch_count']
                                               + $data['category_mismatch_count']
                                               + $data['image_mismatch_count']
                                               + $data['backup_image_mismatch_count']
                                               + $data['resolution_upgrade_count'];

        // Slow Moving Products
        $data['bottom_products'] = $this->model_warehouse_marketplace_ebay_sync->getBottomProducts($period, 10);

        // Alerts
        $data['alerts'] = $this->model_warehouse_marketplace_ebay_sync->getAlerts();

        return $data;
    }

    /**
     * Export - Exporte les données analytiques en CSV
     *
     * @return void
     */
    public function export(): void {
        $this->load->model('warehouse/marketplace/ebay/sync');

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
        $this->load->model('warehouse/marketplace/ebay/sync');
        $this->load->language('warehouse/marketplace/ebay/sync');
        $data = [];
        

        $data = [];
        $data['total_products'] = $this->model_warehouse_marketplace_ebay_sync->getTotalProducts();
        $data['low_stock'] = $this->model_warehouse_marketplace_ebay_sync->getLowStockCount();
        $data['pending_orders'] = $this->model_warehouse_marketplace_ebay_sync->getPendingOrders();
        $data['todays_revenue'] = $this->model_warehouse_marketplace_ebay_sync->getTodaysRevenue();

        return $this->load->view('warehouse/marketplace/ebay/sync_widget', $data);
    }

    /**
     * Sync Marketplace Data - Synchronise l'inventaire eBay page par page
     *
     * @return void
     */
    public function importMarketplace(): void {
        file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - importMarketplace CALLED\n", FILE_APPEND);
        
        $this->load->model('warehouse/marketplace/ebay/api');
        $this->load->model('warehouse/marketplace/listing');

        file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - Models loaded\n", FILE_APPEND);
        
        $json = [];

        try {
            // Get parameters
            $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
            $offset = isset($this->request->get['offset']) ? max(0, (int)$this->request->get['offset']) : 0;
            $force_refresh = !empty($this->request->get['force_refresh']); // Force GetItem even if data already exists
            $selected_product_ids_raw = isset($this->request->get['selected_product_ids']) ? (string)$this->request->get['selected_product_ids'] : '';
            $selected_product_ids = [];

            if ($selected_product_ids_raw !== '') {
                foreach (explode(',', $selected_product_ids_raw) as $pid) {
                    $pid = (int)trim($pid);

                    if ($pid > 0) {
                        $selected_product_ids[$pid] = true;
                    }
                }
            }

            $only_selected_getitem = !empty($this->request->get['only_selected_getitem']) && !empty($selected_product_ids);
            // force_refresh = GetItem API call per item (~2s each) → batch petit pour éviter 502
            $batch_size = $force_refresh ? 3 : 20;
            $marketplace_account_id = isset($this->request->get['account_id']) ? (int)$this->request->get['account_id'] : 1;
            // Timestamp captured by frontend when the sync run started — used to sweep ended listings
            $started_at = isset($this->request->get['started_at']) ? trim($this->request->get['started_at']) : '';
            $limit = 200; // eBay allows up to 200 per page for GetMyeBaySelling

            file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - Page: $page, Account: $marketplace_account_id\n", FILE_APPEND);

            // Cache GetMyeBaySelling response to avoid redundant API calls when paginating within the same page
            // For a page of 200 items with batch_size=20, this cuts 10 API calls down to 1
            $cache_file = DIR_CACHE . 'ebay_import_' . $marketplace_account_id . '_p' . $page . '.json';
            $cache_ttl = 300; // 5 minutes
            if ($offset > 0 && file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_ttl) {
                $response = json_decode(file_get_contents($cache_file), true) ?: [];
            } else {
                $response = $this->model_warehouse_marketplace_ebay_api->getMyeBaySellingBulk($page, $marketplace_account_id);
                // Only cache successful responses — never cache eBay error responses
                $is_success = !empty($response) && (!isset($response['Ack']) || $response['Ack'] === 'Success' || $response['Ack'] === 'Warning');
                if ($is_success) {
                    file_put_contents($cache_file, json_encode($response));
                }
            }

            // Detect eBay API-level failures (Ack: Failure) and surface them instead of silently returning 0 items
            if (!empty($response['Ack']) && $response['Ack'] === 'Failure') {
                $ebay_error = $response['Errors']['ShortMessage'] ?? 'eBay API error';
                $ebay_code  = $response['Errors']['ErrorCode'] ?? '';
                file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - eBay API Failure (code $ebay_code): $ebay_error\n", FILE_APPEND);
                throw new \Exception("eBay API error ($ebay_code): $ebay_error");
            }

            file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - API response received\n", FILE_APPEND);

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
                    // file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - FIRST ITEM DUMP:\n" . print_r($items[0], true) . "\n", FILE_APPEND);
                }
            }
            
            file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - Found " . count($items) . " items (offset=$offset, batch=$batch_size)\n", FILE_APPEND);
            
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

                // Pre-load existing marketplace data for all batch items in one query (avoids N+1 SELECTs)
                $batch_product_ids = [];
                foreach ($batch as $_item) {
                    $_sku = isset($_item['SKU']) ? trim($_item['SKU']) : '';
                    if (!empty($_sku) && is_numeric($_sku)) {
                        $batch_product_ids[] = (int)$_sku;
                    }
                }
                $existing_data_map = $this->model_warehouse_marketplace_listing->getBulkMarketplaceExistingData($batch_product_ids, 1);

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

                    // Skip COM_ and CARD_LISTING_ SKUs
                    if (substr($sku, 0, 4) === 'COM_' || substr($sku, 0, 13) === 'CARD_LISTING_') {
                        continue;
                    }
                    
                    // Determine which database based on SKU
                    $is_com = 0;
                    
                    $product_id = 0;
                    $db_active = null;
                    
                    // phoenixliquidation: SKU = product_id (numeric)
                    if (is_numeric($sku)) {
                        $product_id = (int)$sku;
                    }
                    
                    // If product not found, skip
                    if ($product_id == 0) {
                        continue;
                    }

                    // Use pre-loaded existing data map (populated before loop in one bulk query)
                    $existing_data = $existing_data_map[$product_id] ?? null;

                    // Only call GetItem if we don't have complete data already
                    $item_details = null;
                    $required_fields = ['category_id', 'condition_id', 'specifics', 'ebay_image_count'];
                    if ($only_selected_getitem) {
                        // In selective mode, call GetItem only for user-selected products.
                        $needs_getitem = isset($selected_product_ids[$product_id]);
                    } else {
                        $needs_getitem = $force_refresh || !$existing_data ||
                                        (bool) array_filter($required_fields, fn($k) => empty($existing_data[$k]));
                    }
                    
                    if ($needs_getitem) {
                        // GET FULL ITEM DETAILS with GetItem API call (for category, condition, specifics)
                        $item_details = $this->model_warehouse_marketplace_ebay_api->getItemDetails($item_id, $marketplace_account_id);
                        // file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - GetItem called for $item_id (missing data)\n", FILE_APPEND);
                    } else {
                        // file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - Skipped GetItem for $item_id (data already exists)\n", FILE_APPEND);
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
                        'quantity_listed' => isset($item['Quantity']) ? (int)$item['Quantity'] : 0,
                        'quantity_sold' => isset($item['SellingStatus']['QuantitySold']) ? (int)$item['SellingStatus']['QuantitySold'] : 0,
                        'specifics' => $specifics,
                        'status' => isset($item['ListingDetails']['EndingReason']) ? 0 : 1,
                        'date_added' => $date_added,
                        'date_ended' => $date_ended,
                        'last_import_time' => date('Y-m-d H:i:s'),
                        'ebay_image_count' => $this->extractEbayImageCount($item, $item_details),
                    ];

                    // Update or insert in product_marketplace table
                    $this->model_warehouse_marketplace_listing->upsertProductMarketplace($marketplace_data, null);

                    $processed++;
                }

                // Calcul batch/page completion
                $next_offset = $offset + $batch_size;
                $page_complete = ($next_offset >= $total_items_on_page);
                $completed = $page_complete && ($page >= $total_pages);

                // Clean up cache file once the page is fully processed
                if ($page_complete && isset($cache_file) && file_exists($cache_file)) {
                    @unlink($cache_file);
                }

                // Sweep ended eBay listings: when the full import is done, mark status=0 for items
                // that were not seen in this run (not in eBay's ActiveList → ended/expired listings)
                if ($completed && !empty($started_at)) {
                    $swept = $this->model_warehouse_marketplace_listing->sweepEndedListings($marketplace_account_id, $started_at);
                    file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - Sweep ended listings: $swept rows marked status=0\n", FILE_APPEND);
                    $json['swept'] = $swept;
                }

                file_put_contents('/home/n7f9655/public_html/storage_phoenixliquidation/logs/ebay.log', date('Y-m-d H:i:s') . " - Batch done: processed=$processed, offset=$offset, next=$next_offset, page_complete=" . ($page_complete?'true':'false') . ", completed=" . ($completed?'true':'false') . "\n", FILE_APPEND);

                $json['success'] = true;
                $json['completed'] = $completed;
                $json['page_complete'] = $page_complete;
                $json['next_offset'] = $next_offset;
                $json['processed'] = $processed;
                $json['page'] = $page;
                $json['total_pages'] = $total_pages;
                $json['total_entries'] = $total_entries;
                $json['force_refresh'] = $force_refresh;
                $json['only_selected_getitem'] = $only_selected_getitem;
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

            // Load models
            $this->load->model('warehouse/marketplace/ebay/api');
            $this->load->model('warehouse/marketplace/listing');
            $this->load->model('warehouse/product/product');

            // Get product info
            $product = $this->model_warehouse_product_product->getProduct($product_id);

            if (empty($product)) {
                throw new \Exception('Product not found');
            }

            // Get marketplace info
            $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);

            if (!$marketplace) {
                throw new \Exception('Product not listed on eBay');
            }

            $marketplace_item_id = $marketplace['marketplace_item_id'];
            $marketplace_account_id = (int)$marketplace['marketplace_account_id'];
            $new_quantity = (int)($product['quantity'] ?? 0) + (int)($product['unallocated_quantity'] ?? 0);
            
            // Get marketplace account settings
            $account_info = $this->model_warehouse_marketplace_listing->getMarketplaceAccount(['marketplace_account_id' => $marketplace_account_id], true);
            $site_settings = [];
            
            if ($account_info && !empty($account_info['site_setting'])) {
                $site_settings = json_decode($account_info['site_setting'], true) ?: [];
            }
            
            
            // Update quantity on eBay (state management handled by marketplace layer)
            $response = $this->model_warehouse_marketplace_listing->editQuantity($product_id, $marketplace_account_id);

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
        $this->load->model('warehouse/marketplace/listing');
        $json = [];

        try {
            if (!isset($this->request->post['product_id'])) {
                throw new \Exception('Product ID required');
            }

            $product_id = (int)$this->request->post['product_id'];

            $response = $this->model_warehouse_marketplace_listing->editQuantity($product_id);

            
            if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                $this->model_warehouse_marketplace_listing->updateMarketplaceLastSync($product_id);
                $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
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
        $this->load->model('warehouse/marketplace/ebay/sync');
        
        $product_id = isset($this->request->get['product_id']) ? (int)$this->request->get['product_id'] : 0;
        
        if ($product_id) {
            // Single product report
            $products = $this->model_warehouse_marketplace_ebay_sync->getQuantityMismatch();
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
            $products = $this->model_warehouse_marketplace_ebay_sync->getQuantityMismatch();
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
        $this->load->model('warehouse/marketplace/ebay/api');
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/product/product');
        
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $product = $this->model_warehouse_product_product->getProduct($product_id);
                if (!$product) {
                    throw new \Exception("Product not found");
                }
                
                $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                if (!$marketplace) {
                    throw new \Exception("Product not listed on eBay");
                }
                
                $price = (float)$product['price'];
                $marketplace_item_id = $marketplace['marketplace_item_id'];
                $marketplace_account_id = $marketplace['marketplace_account_id'];
                
                $account_info = $this->model_warehouse_marketplace_listing->getMarketplaceAccount(['marketplace_account_id' => $marketplace_account_id], true);
                $site_settings = json_decode($account_info['site_setting'] ?? '{}', true);
                
                $response = $this->model_warehouse_marketplace_ebay_api->editPrice($marketplace_item_id, $price, $marketplace_account_id, $site_settings);
                
                if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                    $this->model_warehouse_marketplace_listing->updateMarketplacePrice($product_id, $price);
                    $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
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
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/product/product');
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                if (!$marketplace || !isset($marketplace['price'])) {
                    $json['error'] = "Product not listed on eBay";
                    throw new \Exception("Product not listed on eBay");
                }else{
                    $ebay_price = (float)$marketplace['price'];
                    $this->model_warehouse_product_product->editProductPrice($product_id, $ebay_price);
                    $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
                    $json['success'] = 'Local price updated from eBay: $' . number_format($ebay_price, 2);
                }
                
                
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
        $this->load->model('warehouse/marketplace/listing');
        $json = [];
        $product_id = (int)($this->request->post['product_id'] ?? 0);

        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $response = $this->model_warehouse_marketplace_listing->editQuantity($product_id);

                if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                    $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
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
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/product/product');
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                if (!$marketplace || !isset($marketplace['quantity_listed'])) {
                    $json['error'] = "Product not listed on eBay";
                    throw new \Exception("Product not listed on eBay");
                    
                }else{

                    // eBay available = listed - sold  (same formula used in the mismatch detection query)
                    $ebay_quantity = max(0, (int)$marketplace['quantity_listed'] - (int)($marketplace['quantity_sold'] ?? 0));
                    $this->model_warehouse_product_product->editProductQuantity($product_id, $ebay_quantity);
                    $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
                    $json['success'] = 'Local quantity updated from eBay: ' . $ebay_quantity;
                }
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
        $this->load->model('warehouse/marketplace/ebay/api');
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/product/product');
        
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $product_desc = $this->model_warehouse_product_product->getProduct($product_id);
                if (!$product_desc || empty($product_desc['specifics'])) {
                    throw new \Exception("No local specifics found");
                }
                
                $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                if (!$marketplace) {
                    throw new \Exception("Product not listed on eBay");
                }
                
                $specifics = json_decode($product_desc['specifics'], true);
                $marketplace_item_id = $marketplace['marketplace_item_id'];
                $marketplace_account_id = $marketplace['marketplace_account_id'];
                
                $account_info = $this->model_warehouse_marketplace_listing->getMarketplaceAccount(['marketplace_account_id' => $marketplace_account_id], true);
                $site_settings = json_decode($account_info['site_setting'] ?? '{}', true);
                
                $response = $this->model_warehouse_marketplace_ebay_api->editSpecifics($marketplace_item_id, $specifics, $marketplace_account_id, $site_settings);
                
                if (isset($response['Ack']) && ($response['Ack'] == 'Success' || $response['Ack'] == 'Warning')) {
                    $this->model_warehouse_marketplace_listing->updateMarketplaceSpecifics($product_id, $specifics);
                    $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
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
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/product/product');
        $json = [];
        $product_id = $this->request->post['product_id'] ?? 0;
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                if (!$marketplace || empty($marketplace['specifics'])) {
                    $json['error'] = "Product not listed on eBay or no specifics found";
                    throw new \Exception("No eBay specifics found");
                }else{
                
                    $ebay_specifics = $marketplace['specifics'];
                    $this->model_warehouse_product_product->editProductSpecifics($product_id, $ebay_specifics, 1);
                    $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
                    $json['success'] = 'Local specifics updated from eBay';
                }
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
            $this->load->model('warehouse/marketplace/ebay/api');
            
            // Get marketplace item ID from database using model
            $this->load->model('warehouse/marketplace/listing');
            $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
            
            if (!$marketplace) {
                throw new \Exception("Product not found on eBay marketplace");
            }
            
            $marketplace_item_id = $marketplace['marketplace_item_id'];
            $marketplace_account_id = $marketplace['marketplace_account_id'];
            
            // Call eBay API to get item details
            $response = $this->model_warehouse_marketplace_ebay_api->getItem($marketplace_item_id, $marketplace_account_id);
            
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

            // Count eBay images from PictureDetails
            $ebay_image_count = 0;
            if (isset($item['PictureDetails']['PictureURL'])) {
                $pics = $item['PictureDetails']['PictureURL'];
                $ebay_image_count = is_array($pics) ? count($pics) : 1;
            }

            // Update product_marketplace table using model
            $marketplace_data = [
                'price' => (float)$price,
                'currency' => $currency,
                'quantity_listed' => (int)$quantity_listed,
                'quantity_sold' => (int)$quantity_sold,
                'category_id' => (int)$category_id,
                'specifics' => $specifics,
                'date_added' => $date_added,
                'date_ended' => $date_ended,
                'ebay_image_count' => $ebay_image_count,
            ];

            $this->model_warehouse_marketplace_listing->updateMarketplaceFullRefresh($product_id, $marketplace_data);

            // Refresh backup image count + resolution widths (reuses same request, no extra eBay call)
            $this->load->model('warehouse/marketplace/ebay/sync');
            $backup_dir = DIR_OPENCART . 'image_backup/';
            if (is_dir($backup_dir)) {
                $this->model_warehouse_marketplace_ebay_sync->refreshProductResolutionScan((int)$product_id, $backup_dir);
            }

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
                'ebay_image_count' => $ebay_image_count,
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
        $this->load->language('warehouse/marketplace/ebay/sync');
        $data = [];
        
        $this->load->model('warehouse/marketplace/ebay/sync');
        
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
        
        $price_mismatch = $this->model_warehouse_marketplace_ebay_sync->getPriceMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalPriceMismatch();
        
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
        
        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_price_mismatch', $data));
    }
    
    /**
     * AJAX method to load Quantity Mismatch tab with pagination
     */
    public function getQtyMismatchTab(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $data = [];
        
        $this->load->model('warehouse/marketplace/ebay/sync');
        
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
        
        $qty_mismatch = $this->model_warehouse_marketplace_ebay_sync->getQuantityMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalQuantityMismatch();
        
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
        
        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_qty_mismatch', $data));
    }
    
    /**
     * AJAX method to load Specifics Mismatch tab with pagination
     */
    public function getSpecificsMismatchTab(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $data = [];
        
        $this->load->model('warehouse/marketplace/ebay/sync');
        
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
        
        $specifics_mismatch = $this->model_warehouse_marketplace_ebay_sync->getSpecificsMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalSpecificsMismatch();
        
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
        
        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_specifics_mismatch', $data));
    }
    
    /**
     * AJAX method to load Condition Mismatch tab with pagination
     */
    public function getConditionMismatchTab(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $data = [];
        
        $this->load->model('warehouse/marketplace/ebay/sync');
        
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
        
        $condition_mismatch = $this->model_warehouse_marketplace_ebay_sync->getConditionMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalConditionMismatch();
        
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
        
        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_condition_mismatch', $data));
    }
    
    /**
     * Get Image Count Mismatch Tab - AJAX reload endpoint
     */
    public function getImageMismatchTab(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $data = [];
        
        $this->load->model('warehouse/marketplace/ebay/sync');

        // === eBay mismatch pagination ===
        $page  = isset($this->request->get['page'])  ? (int)$this->request->get['page']  : 1;
        $limit = 20;
        $start = ($page - 1) * $limit;
        $sort  = isset($this->request->get['sort'])  ? $this->request->get['sort']  : 'product_id';
        $order = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';

        $filter_data = ['start' => $start, 'limit' => $limit, 'sort' => $sort, 'order' => $order];
        $image_mismatch = $this->model_warehouse_marketplace_ebay_sync->getImageMismatch($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalImageMismatch();

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
        $backup_mismatch = $this->model_warehouse_marketplace_ebay_sync->getImageBackupMismatch($bfilter);
        $backup_total    = $this->model_warehouse_marketplace_ebay_sync->getTotalImageBackupMismatch();

        $data['backup_mismatch']            = $backup_mismatch;
        $data['backup_mismatch_total']      = $backup_total;
        $data['backup_mismatch_page']       = $bpage;
        $data['backup_mismatch_sort']       = $bsort;
        $data['backup_mismatch_order']      = $border;
        $data['backup_mismatch_start']      = $bstart + 1;
        $data['backup_mismatch_end']        = min($bstart + $limit, $backup_total);
        $data['backup_mismatch_num_pages']  = ceil($backup_total / $limit);
        $data['backup_mismatch_pagination'] = $backup_total > $limit;

        // === Resolution upgrade pagination ===
        $rpage  = isset($this->request->get['rpage'])  ? (int)$this->request->get['rpage']  : 1;
        $rstart = ($rpage - 1) * $limit;
        $rsort  = isset($this->request->get['rsort'])  ? $this->request->get['rsort']  : 'backup_max_width';
        $rorder = isset($this->request->get['rorder']) ? $this->request->get['rorder'] : 'DESC';

        $rfilter = ['start' => $rstart, 'limit' => $limit, 'sort' => $rsort, 'order' => $rorder];
        $resolution_upgrade       = $this->model_warehouse_marketplace_ebay_sync->getResolutionUpgradeMismatch($rfilter);
        $resolution_upgrade_total = $this->model_warehouse_marketplace_ebay_sync->getTotalResolutionUpgradeMismatch();

        $data['resolution_upgrade']            = $resolution_upgrade;
        $data['resolution_upgrade_total']      = $resolution_upgrade_total;
        $data['resolution_upgrade_page']       = $rpage;
        $data['resolution_upgrade_sort']       = $rsort;
        $data['resolution_upgrade_order']      = $rorder;
        $data['resolution_upgrade_start']      = $rstart + 1;
        $data['resolution_upgrade_end']        = min($rstart + $limit, $resolution_upgrade_total);
        $data['resolution_upgrade_num_pages']  = ceil($resolution_upgrade_total / $limit);
        $data['resolution_upgrade_pagination'] = $resolution_upgrade_total > $limit;
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

        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_image_mismatch', $data));
    }

    /**
     * Scan image_backup directory to count backup images per product.
     * Updates oc_product_marketplace.image_backup_count.
     * Called via AJAX button "Scan image_backup".
     */
    public function scanImageBackupCounts(): void {
        $this->load->model('warehouse/marketplace/ebay/sync');

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

            $stats = $this->model_warehouse_marketplace_ebay_sync->scanImageBackupCounts($backup_dir);

            // Purge orphan empty dirs (product_id folders not in DB, with no files)
            $product_dir = $backup_dir . 'data/product/';
            $orphans_deleted = 0;
            if (is_dir($product_dir)) {
                foreach (scandir($product_dir) as $entry) {
                    if ($entry === '.' || $entry === '..') continue;
                    $full = $product_dir . $entry . '/';
                    if (!is_dir($full)) continue;
                    $all_entries = array_diff(scandir($full) ?: [], ['.', '..']);
                    if (empty($all_entries)) {
                        rmdir($full);
                        $orphans_deleted++;
                    }
                }
            }

            $json['success']     = true;
            $json['scanned']     = $stats['scanned'];
            $json['with_images'] = $stats['with_images'];
            $json['empty']       = $stats['empty'];
            $json['not_found']          = $stats['not_found'];
            $json['not_found_samples'] = $stats['not_found_samples'];
            $json['orphans_deleted'] = $orphans_deleted;
            $json['message']     = sprintf(
                '%d products scanned — %d with backup images, %d empty dirs, %d not found in backup, %d orphan dirs deleted',
                $stats['scanned'], $stats['with_images'], $stats['empty'], $stats['not_found'], $orphans_deleted
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

        $prefix = substr((string)$product_id, 0, 2);
        $backup_dir_flat   = DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/';
        $backup_dir_nested = DIR_OPENCART . 'image_backup/data/product/' . $prefix . '/' . $product_id . '/';
        $backup_dir = is_dir($backup_dir_flat) ? $backup_dir_flat : (is_dir($backup_dir_nested) ? $backup_dir_nested : null);
        if ($backup_dir === null) {
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
                'backup_url' => HTTP_CATALOG . substr($backup_dir, strlen(DIR_OPENCART)) . rawurlencode($file),
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
        $this->load->model('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/product/product');
        $this->load->model('warehouse/marketplace/listing');
        $json = [];
        try {
        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        $filenames  = isset($this->request->post['filenames']) ? (array)$this->request->post['filenames'] : [];

        if (!$product_id || empty($filenames)) {
            $json['error'] = 'Missing product_id or filenames';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $prefix      = substr((string)$product_id, 0, 2);
        $backup_dir  = is_dir(DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/')
            ? DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/'
            : DIR_OPENCART . 'image_backup/data/product/' . $prefix . '/' . $product_id . '/';
        $subfolder   = $prefix;
        $dest_rel    = 'catalog/product/' . $subfolder . '/' . $product_id . '/';
        $dest_dir    = DIR_IMAGE . $dest_rel;

        if (!is_dir($dest_dir)) {
            mkdir($dest_dir, 0755, true);
        }

        $transferred = 0;
        $skipped     = 0;
        $errors      = [];

        // Get current primary image for this product
        $product      = $this->model_warehouse_product_product->getProduct($product_id);
        $current_main = $product['image'] ?? '';

        // Get max sort_order in product_image
        $existing_images = $this->model_warehouse_product_product->getImages($product_id);
        $next_sort = count($existing_images) ? max(array_column($existing_images, 'sort_order')) + 1 : 1;

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
                $this->model_warehouse_product_product->setPrimaryImage($product_id, $oc_path);
                $current_main = $oc_path;
            } else {
                // Insert as secondary image
                $this->model_warehouse_product_product->addImageIfNotExists($product_id, $oc_path);
                $next_sort++;
            }

            $transferred++;
        }


        $this->model_warehouse_marketplace_listing->resetSyncState($product_id);

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
        $this->load->model('warehouse/marketplace/listing');
        $json = [];
        try {
        $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;
        $filenames  = isset($this->request->post['filenames']) ? (array)$this->request->post['filenames'] : [];

        if (!$product_id || empty($filenames)) {
            $json['error'] = 'Missing product_id or filenames';
            $this->response->setOutput(json_encode($json));
            return;
        }

        $prefix     = substr((string)$product_id, 0, 2);
        $backup_dir = is_dir(DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/')
            ? DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/'
            : DIR_OPENCART . 'image_backup/data/product/' . $prefix . '/' . $product_id . '/';
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

       
        
        $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
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
     * Reset oc_max_width + backup_max_width to NULL so the product is
     * recomputed on the next scanImageBackupCounts run.
     * POST: product_id
     */
    public function resetProductImageScan(): void {
        $this->response->addHeader('Content-Type: application/json');
        $json = [];
        $product_id = (int)($this->request->post['product_id'] ?? 0);
        if (!$product_id) {
            $json['error'] = 'Missing product_id';
            $this->response->setOutput(json_encode($json));
            return;
        }
        $this->load->model('warehouse/marketplace/ebay/sync');
        $this->model_warehouse_marketplace_ebay_sync->resetProductImageScan($product_id);
        $json['success'] = true;
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
            $this->load->model('warehouse/product/product');
            $product_data = $this->model_warehouse_product_product->getProduct($product_id);
            $main_path = $product_data['image'] ?? '';
            if ($main_path && $main_path !== 'no_image.png' && $main_path !== '') {
                $abs  = DIR_IMAGE . $main_path;
                set_error_handler(function() { return true; });
                $info = is_readable($abs) ? getimagesize($abs) : false;
                restore_error_handler();
                $oc_images[] = ['role' => 'primary', 'image' => $main_path, 'url' => HTTP_CATALOG . 'image/' . $main_path, 'width' => $info ? $info[0] : 0, 'height' => $info ? $info[1] : 0];
            }
            $secondary_images = $this->model_warehouse_product_product->getImages($product_id);
            foreach ($secondary_images as $row) {
                $abs  = DIR_IMAGE . $row['image'];
                set_error_handler(function() { return true; });
                $info = is_readable($abs) ? getimagesize($abs) : false;
                restore_error_handler();
                $oc_images[] = ['role' => 'secondary', 'image' => $row['image'], 'url' => HTTP_CATALOG . 'image/' . $row['image'], 'width' => $info ? $info[0] : 0, 'height' => $info ? $info[1] : 0, 'sort_order' => (int)$row['sort_order']];
            }

            // ── Backup images ──────────────────────────────────────────────
            $prefix         = substr((string)$product_id, 0, 2);
            $backup_dir_flat   = DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/';
            $backup_dir_nested = DIR_OPENCART . 'image_backup/data/product/' . $prefix . '/' . $product_id . '/';
            $backup_dir     = is_dir($backup_dir_flat) ? $backup_dir_flat : (is_dir($backup_dir_nested) ? $backup_dir_nested : null);
            $backup_images = [];
            if ($backup_dir !== null) {
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
                    $backup_images[] = ['filename' => $file, 'type' => $type, 'url' => HTTP_CATALOG . substr($backup_dir, strlen(DIR_OPENCART)) . rawurlencode($file), 'oc_exists' => file_exists(DIR_IMAGE . $oc_rel), 'oc_path' => $oc_rel, 'width' => $info ? $info[0] : 0, 'height' => $info ? $info[1] : 0];
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
            $this->load->model('warehouse/product/product');
            $this->load->model('warehouse/marketplace/listing');
            if ($role === 'primary') {
                $this->model_warehouse_product_product->clearPrimaryImage($product_id);
            } else {
                $this->model_warehouse_product_product->deleteImageByPath($product_id, $image_path);
            }
            $this->model_warehouse_marketplace_listing->setToUpdate($product_id);
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

            $this->load->model('warehouse/product/product');
            $this->load->model('warehouse/marketplace/listing');
            $old_primary = $this->model_warehouse_product_product->getProduct($product_id)['image'] ?? '';
            $new_old     = $old_primary;
            $new_pri     = $image_path;

            if ($old_primary && $old_primary !== $image_path) {
                // Rename files on disk
                $new_old = $swapRole($old_primary, 'pri', 'sec');
                $new_pri = $swapRole($image_path,  'sec', 'pri');

                // Remove stale DB path if file was renamed
                if ($new_old !== $old_primary) {
                    $this->model_warehouse_product_product->deleteImageByPath($product_id, $old_primary);
                }
                // Insert demoted primary as secondary
                $this->model_warehouse_product_product->addImageIfNotExists($product_id, $new_old);
            }

            // Remove new primary from secondary table + set as main
            $this->model_warehouse_product_product->deleteImagesByPaths($product_id, [$image_path, $new_pri]);
            $this->model_warehouse_product_product->setPrimaryImage($product_id, $new_pri);
            $this->model_warehouse_marketplace_listing->setToUpdate($product_id);
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

            $subfolder  = substr((string)$product_id, 0, 2);
            $backup_dir = is_dir(DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/')
                ? DIR_OPENCART . 'image_backup/data/product/' . $product_id . '/'
                : DIR_OPENCART . 'image_backup/data/product/' . $subfolder . '/' . $product_id . '/';
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

            $this->load->model('warehouse/product/product');
            $this->load->model('warehouse/marketplace/listing');
            if ($role === 'primary') {
                $old = $this->model_warehouse_product_product->getProduct($product_id)['image'] ?? '';
                if ($old && $old !== $oc_path) {
                    $this->model_warehouse_product_product->addImageIfNotExists($product_id, $old);
                }
                $this->model_warehouse_product_product->deleteImageByPath($product_id, $oc_path);
                $this->model_warehouse_product_product->setPrimaryImage($product_id, $oc_path);
            } else {
                $this->model_warehouse_product_product->addImageIfNotExists($product_id, $oc_path);
            }
            $this->model_warehouse_marketplace_listing->setToUpdate($product_id);
            $json['success'] = true;
            $json['oc_path'] = $oc_path;
        } catch (\Exception $e) { $json['error'] = 'transferBackupAsRole: ' . $e->getMessage(); }
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Sync Category To eBay - Full listing update (like product save)
     */
    public function syncCategoryToEbay(): void {
        $this->load->model('warehouse/marketplace/listing');

        $json = [];
        $product_id = (int)($this->request->post['product_id'] ?? 0);

        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $this->model_warehouse_marketplace_listing->updateMarketplaceListings($product_id);
                $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
                $json['success'] = 'Listing updated on eBay';
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Bulk Sync Categories To eBay - Full listing update for multiple products
     */
    public function syncCategoryBulkToEbay(): void {
        $this->load->model('warehouse/marketplace/listing');

        $json = [];
        $product_ids = $this->request->post['product_ids'] ?? [];

        if (empty($product_ids) || !is_array($product_ids)) {
            $json['error'] = 'No products selected';
        } else {
            $success_count = 0;
            $error_count   = 0;
            $errors        = [];
            $updated_ids   = [];
            $failed_ids    = [];

            foreach ($product_ids as $product_id) {
                $product_id = (int)$product_id;
                try {
                    $this->model_warehouse_marketplace_listing->updateMarketplaceListings($product_id);

                    // Detect real outcome from product_marketplace row: editListing writes error/to_update.
                    $marketplace_row = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                    $has_error = !$marketplace_row || !empty($marketplace_row['error']);

                    if ($has_error) {
                        $err_msg = 'eBay update failed';

                        if ($marketplace_row && !empty($marketplace_row['error'])) {
                            $decoded = json_decode((string)$marketplace_row['error'], true);
                            if (is_array($decoded)) {
                                $err_msg = $decoded['Errors']['ShortMessage']
                                    ?? $decoded['Errors'][0]['ShortMessage']
                                    ?? $err_msg;
                            }
                        }

                        $errors[] = "Product $product_id: $err_msg";
                        $failed_ids[$product_id] = $err_msg;
                        $error_count++;
                    } else {
                        // Update succeeded on eBay: clear to_update and keep row out of To Update tab.
                        $this->model_warehouse_marketplace_listing->updateMarketplaceLastSync($product_id);
                        $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
                        $updated_ids[] = $product_id;
                        $success_count++;
                    }
                } catch (\Exception $e) {
                    $err_msg = $e->getMessage();
                    $errors[] = "Product $product_id: " . $err_msg;
                    $failed_ids[$product_id] = $err_msg;
                    $error_count++;
                }
            }

            if ($success_count > 0) {
                $json['success'] = "$success_count listing(s) updated on eBay";
                if ($error_count > 0) $json['success'] .= " ($error_count failed)";
            } else {
                $json['error'] = 'All updates failed';
            }
            if (!empty($errors)) {
                $json['details'] = implode("\n", array_slice($errors, 0, 5));
            }
            $json['updated_ids'] = $updated_ids;
            $json['failed_ids'] = $failed_ids;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Sync Category From eBay - Import single category
     */
    public function syncCategoryFromEbay(): void {
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/product/product');
        $this->load->model('warehouse/product/category');
        
        $json = [];
        $product_id = (int)($this->request->post['product_id'] ?? 0);
        
        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                if (!$marketplace || !isset($marketplace['category_id'])) {
                    throw new \Exception('Product not listed on eBay or no category_id');
                }
                $ebay_category_id = (int)$marketplace['category_id'];

                if (!$this->model_warehouse_product_category->getCategory($ebay_category_id)) {
                    throw new \Exception("eBay category $ebay_category_id does not exist in local database");
                }

                $this->model_warehouse_product_product->setProductLeafCategory($product_id, $ebay_category_id);
                if (!empty($marketplace['specifics'])) {
                    $this->model_warehouse_product_product->editProductSpecifics($product_id, $marketplace['specifics'], 1);
                }
                $this->model_warehouse_marketplace_listing->setToUpdate($product_id);
                $json['success'] = "Category imported: $ebay_category_id";
            } catch (\Exception $e) {
                $json['error'] = $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * AJAX method to load Category Mismatch tab with pagination (reload after fix)
     */
    public function getCategoryMismatchTab(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/marketplace/ebay/sync');

        $page  = max(1, (int)($this->request->get['page'] ?? 1));
        $limit = 50;
        $start = ($page - 1) * $limit;
        $sort  = $this->request->get['sort']  ?? 'product_id';
        $order = $this->request->get['order'] ?? 'ASC';

        $allowed_sorts = ['product_id', 'name', 'location'];
        if (!in_array($sort, $allowed_sorts)) $sort = 'product_id';
        if ($order !== 'ASC' && $order !== 'DESC') $order = 'ASC';

        $all      = $this->model_warehouse_marketplace_ebay_sync->getAllCategoryMismatchCandidates();
        $filtered = $this->getFilteredCategoryMismatches($all);

        // Sort filtered in PHP
        usort($filtered, function ($a, $b) use ($sort, $order) {
            if ($sort === 'name') {
                $cmp = strcmp((string)($a['name'] ?? ''), (string)($b['name'] ?? ''));
            } elseif ($sort === 'location') {
                $cmp = strcmp((string)($a['location'] ?? ''), (string)($b['location'] ?? ''));
            } else {
                $cmp = (int)($a['product_id'] ?? 0) - (int)($b['product_id'] ?? 0);
            }
            return $order === 'DESC' ? -$cmp : $cmp;
        });

        $total = count($filtered);
        $rows  = array_slice($filtered, $start, $limit);

        $data = [];
        $data['category_mismatch']            = $rows;
        $data['category_mismatch_total']      = $total;
        $data['category_mismatch_page']       = $page;
        $data['category_mismatch_sort']       = $sort;
        $data['category_mismatch_order']      = $order;
        $data['category_mismatch_start']      = $start + 1;
        $data['category_mismatch_end']        = min($start + $limit, $total);
        $data['category_mismatch_num_pages']  = ceil($total / $limit);
        $data['category_mismatch_pagination'] = $total > $limit;
        $data['user_token']                   = $this->session->data['user_token'];

        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_category_mismatch', $data));
    }

    /**
     * AJAX: paginate Not Imported tab
     */
    public function getNotImportedTab(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/marketplace/ebay/sync');

        $page  = max(1, (int)($this->request->get['page'] ?? 1));
        $limit = 50;
        $start = ($page - 1) * $limit;
        $sort  = $this->request->get['sort']  ?? 'product_id';
        $order = $this->request->get['order'] ?? 'ASC';

        $filter_data = ['start' => $start, 'limit' => $limit, 'sort' => $sort, 'order' => $order];

        $rows  = $this->model_warehouse_marketplace_ebay_sync->getNotImported($filter_data);
        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalNotImported();

        $data = [];
        $data['not_imported']            = $rows;
        $data['not_imported_total']      = $total;
        $data['not_imported_page']       = $page;
        $data['not_imported_sort']       = $sort;
        $data['not_imported_order']      = $order;
        $data['not_imported_start']      = $start + 1;
        $data['not_imported_end']        = min($start + $limit, $total);
        $data['not_imported_num_pages']  = ceil($total / $limit);
        $data['not_imported_pagination'] = $total > $limit;
        $data['user_token'] = $this->session->data['user_token'];

        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_not_imported', $data));
    }

    /**
     * AJAX: paginate To Update tab
     */
    public function getToUpdateTab(): void {
        $this->load->language('warehouse/marketplace/ebay/sync');
        $this->load->model('warehouse/marketplace/ebay/sync');

        $all   = isset($this->request->get['all']) && $this->request->get['all'] == '1';
        $page  = max(1, (int)($this->request->get['page'] ?? 1));
        $sort  = $this->request->get['sort']  ?? 'product_id';
        $order = $this->request->get['order'] ?? 'ASC';

        $total = $this->model_warehouse_marketplace_ebay_sync->getTotalToUpdate();

        if ($all) {
            $limit = $total;
            $start = 0;
            $page  = 1;
        } else {
            $limit = 50;
            $start = ($page - 1) * $limit;
        }

        $filter_data = ['start' => $start, 'limit' => $limit, 'sort' => $sort, 'order' => $order];

        $rows  = $this->model_warehouse_marketplace_ebay_sync->getToUpdate($filter_data);

        $data = [];
        $data['to_update']            = $rows;
        $data['to_update_total']      = $total;
        $data['to_update_page']       = $page;
        $data['to_update_sort']       = $sort;
        $data['to_update_order']      = $order;
        $data['to_update_start']      = $total > 0 ? $start + 1 : 0;
        $data['to_update_end']        = min($start + $limit, $total);
        $data['to_update_num_pages']  = $limit > 0 ? ceil($total / $limit) : 1;
        $data['to_update_pagination'] = !$all && $total > $limit;
        $data['user_token'] = $this->session->data['user_token'];

        $this->response->setOutput($this->load->view('warehouse/marketplace/ebay/sync_to_update', $data));
    }

    /**
     * Sync Category From Info Source - Applique la meilleure catégorie oc_product_info_sources
     */
    public function syncCategoryFromInfoSource(): void {
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/product/product');
        $this->load->model('warehouse/product/category');

        $json = [];
        $product_id = (int)($this->request->post['product_id'] ?? 0);

        if (!$product_id) {
            $json['error'] = 'Product ID required';
        } else {
            try {
                $product = $this->model_warehouse_product_product->getProduct($product_id);
                if (!$product || empty($product['upc'])) {
                    throw new \Exception('Product has no UPC');
                }
                $upc = $product['upc'];

                $this->load->model('warehouse/marketplace/ebay/sync');
                $source_rows = $this->model_warehouse_marketplace_ebay_sync->getInfoSourcesByUpc($upc);
                if (empty($source_rows)) {
                    throw new \Exception('No info sources found for UPC ' . htmlspecialchars($upc));
                }

                $best_id   = null;
                $best_pct  = -1;
                $best_name = '';
                foreach ($source_rows as $row) {
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
                    throw new \Exception('No valid category found in info sources');
                }

                if (!$this->model_warehouse_product_category->getCategory($best_id)) {
                    throw new \Exception("Category $best_id from info source not in local database");
                }

                $this->model_warehouse_product_product->setProductLeafCategory($product_id, $best_id);
                $this->model_warehouse_marketplace_listing->setToUpdate($product_id);

                $label = $best_name ? "$best_id - $best_name" : "$best_id";
                $json['success'] = "Category $label ($best_pct%) applied from info source";
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
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/product/product');
        $this->load->model('warehouse/product/category');
        
        $json = [];
        $product_ids = $this->request->post['product_ids'] ?? [];
        
        if (empty($product_ids) || !is_array($product_ids)) {
            $json['error'] = 'No products selected';
        } else {
            try {
                $success_count = 0;
                $error_count   = 0;
                $errors        = [];

                foreach ($product_ids as $product_id) {
                    $product_id = (int)$product_id;
                    try {
                        $marketplace = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                        if (!$marketplace || !isset($marketplace['category_id'])) {
                            $errors[] = "Product $product_id: Not on eBay";
                            $error_count++; continue;
                        }
                        $ebay_category_id = (int)$marketplace['category_id'];

                        if (!$this->model_warehouse_product_category->getCategory($ebay_category_id)) {
                            $errors[] = "Product $product_id: Category $ebay_category_id not in database";
                            $error_count++; continue;
                        }

                        $this->model_warehouse_product_product->setProductLeafCategory($product_id, $ebay_category_id);
                        if (!empty($marketplace['specifics'])) {
                            $this->model_warehouse_product_product->editProductSpecifics($product_id, $marketplace['specifics'], 1);
                        }
                        $this->model_warehouse_marketplace_listing->setToUpdate($product_id);
                        $success_count++;
                    } catch (\Exception $e) {
                        $errors[] = "Product $product_id: " . $e->getMessage();
                        $error_count++;
                    }
                }

                if ($success_count > 0) {
                    $json['success'] = "$success_count category(ies) imported";
                    if ($error_count > 0) $json['success'] .= " ($error_count failed)";
                } else {
                    $json['error'] = 'All imports failed';
                }
                if (!empty($errors)) {
                    $json['details'] = implode("\n", array_slice($errors, 0, 5));
                }
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

    /**
     * Bulk GetItem — Not Imported tab
     * Fetches each product's eBay listing data via GetItem and stores it locally.
     * POST product_ids[]
     */
    public function bulkGetItemNotImported(): void {
        $json = [];

        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/marketplace/ebay/api');

        $product_ids = isset($this->request->post['product_ids']) ? $this->request->post['product_ids'] : [];

        if (empty($product_ids) || !is_array($product_ids)) {
            $json['error'] = 'No products selected';
        } else {
            $imported_ids = [];
            $failed_ids   = [];
            $success_count = 0;
            $error_count   = 0;

            foreach ($product_ids as $product_id) {
                $product_id = (int)$product_id;
                try {
                    $marketplace_row = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);

                    if (!$marketplace_row || empty($marketplace_row['marketplace_item_id'])) {
                        $failed_ids[$product_id] = 'No eBay item ID found in database';
                        $error_count++;
                        continue;
                    }

                    $item_id               = $marketplace_row['marketplace_item_id'];
                    $marketplace_account_id = (int)($marketplace_row['marketplace_account_id'] ?? 1);

                    $item_details = $this->model_warehouse_marketplace_ebay_api->getItemDetails($item_id, $marketplace_account_id);

                    if (!$item_details) {
                        $failed_ids[$product_id] = 'GetItem returned no data for eBay item ' . $item_id;
                        $error_count++;
                        continue;
                    }

                    // Parse ItemSpecifics
                    $specifics = null;
                    if (!empty($item_details['item_specifics'])) {
                        $specs_array = $item_details['item_specifics'];
                        if (isset($specs_array['Name'])) {
                            $specs_array = [$specs_array];
                        }
                        $specifics_formatted = [];
                        foreach ($specs_array as $spec) {
                            if (isset($spec['Name']) && isset($spec['Value'])) {
                                $specifics_formatted[$spec['Name']] = $spec['Value'];
                            }
                        }
                        if (!empty($specifics_formatted)) {
                            $specifics = json_encode($specifics_formatted);
                        }
                    }

                    $this->model_warehouse_marketplace_listing->updateFromGetItem(
                        $product_id,
                        $item_details['category_id'] ?? null,
                        $item_details['condition_id'] ?? null,
                        $specifics,
                        $item_details['image_count'] ?? 0
                    );

                    $imported_ids[] = $product_id;
                    $success_count++;

                } catch (\Exception $e) {
                    $failed_ids[$product_id] = $e->getMessage();
                    $error_count++;
                }
            }

            if ($success_count > 0) {
                $json['success'] = true;
                $json['message'] = "$success_count product(s) imported from eBay via GetItem";
                if ($error_count > 0) $json['message'] .= " ($error_count failed)";
            } else {
                $json['success'] = false;
                $json['error']   = 'All GetItem calls failed';
            }
            $json['imported_ids'] = $imported_ids;
            $json['failed_ids']   = $failed_ids;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Bulk Publish (add listing) — Not Imported tab
     * POST product_ids[] + marketplace_account_id
     */
    public function bulkPublishToEbay(): void {
        $this->load->model('warehouse/marketplace/listing');
        $this->load->model('warehouse/marketplace/ebay/api');

        $json = [];
        $product_ids           = $this->request->post['product_ids'] ?? [];
        $marketplace_account_id = (int)($this->request->post['marketplace_account_id'] ?? 1);

        if (empty($product_ids) || !is_array($product_ids)) {
            $json['error'] = 'No products selected';
        } else {
            $success_count = 0;
            $error_count   = 0;
            $errors        = [];
            $published_ids = [];
            $failed_ids    = [];

            foreach ($product_ids as $product_id) {
                $product_id = (int)$product_id;
                try {
                    $result = $this->model_warehouse_marketplace_listing->addToMarketplace($product_id, $marketplace_account_id);
                    if (isset($result['Ack']) && $result['Ack'] != 'Failure') {
                        $success_count++;
                        $published_ids[] = $product_id;
                    } else {
                        $err_msg = $result['Errors']['ShortMessage'] ?? $result['Errors'][0]['ShortMessage'] ?? json_encode($result);
                        $errors[] = "Product $product_id: $err_msg";
                        $failed_ids[$product_id] = $err_msg;
                        $error_count++;
                    }
                } catch (\Exception $e) {
                    $err_msg = $e->getMessage();
                    $errors[] = "Product $product_id: " . $err_msg;
                    $failed_ids[$product_id] = $err_msg;
                    $error_count++;
                }
            }

            if ($success_count > 0) {
                $json['success'] = true;
                $json['message'] = "$success_count product(s) published to eBay";
                if ($error_count > 0) $json['message'] .= " ($error_count failed)";
            } else {
                $json['success'] = false;
                $json['error']   = 'All publishes failed';
            }
            if (!empty($errors)) {
                $json['details'] = implode("\n", array_slice($errors, 0, 5));
            }

            $json['published_ids'] = $published_ids;
            $json['failed_ids'] = $failed_ids;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Bulk Update Listing — To Update tab
     * POST product_id (single) — JS calls sequentially per product to avoid 502 timeout
     */
    public function bulkUpdateToEbay(): void {
        ob_start(); // Capture any stray PHP output (warnings, debug echoes) that would corrupt JSON
        set_time_limit(120);
        $this->load->model('warehouse/marketplace/listing');

        $json = [];
        // Accept single product_id to avoid nginx proxy timeout when looping many products
        $product_id = (int)($this->request->post['product_id'] ?? 0);

        if (!$product_id) {
            $json['error'] = 'No product_id provided';
        } else {
            try {
                $this->model_warehouse_marketplace_listing->updateMarketplaceListings($product_id);

                // Detect real outcome: editListing writes error field on failure
                $marketplace_row = $this->model_warehouse_marketplace_listing->getMarketplaceItem($product_id, 1);
                $has_error = !$marketplace_row || !empty($marketplace_row['error']);

                if ($has_error) {
                    $err_msg = 'eBay update failed';
                    if ($marketplace_row && !empty($marketplace_row['error'])) {
                        $decoded = json_decode((string)$marketplace_row['error'], true);
                        if (is_array($decoded)) {
                            $err_msg = $decoded['Errors']['ShortMessage']
                                ?? $decoded['Errors'][0]['ShortMessage']
                                ?? $err_msg;
                        }
                    }
                    $json['success'] = false;
                    $json['error']   = $err_msg;
                } else {
                    $this->model_warehouse_marketplace_listing->updateMarketplaceLastSync($product_id);
                    $this->model_warehouse_marketplace_listing->resetSyncState($product_id);
                    $json['success'] = true;
                }
            } catch (\Exception $e) {
                $json['success'] = false;
                $json['error']   = $e->getMessage();
            }
        }

        // Capture any stray PHP output (warnings, debug prints) that would corrupt the JSON response
        $stray_output = trim((string)ob_get_clean());
        if ($stray_output) {
            $error_msg = 'Server output error: ' . substr(strip_tags($stray_output), 0, 400);
            // If the success path already ran (resetSyncState called), the marketplace error state
            // was wiped — force it back to error so the product stays findable in marketplace errors
            if ($product_id && ($json['success'] ?? false) === true) {
                $this->model_warehouse_marketplace_listing->setMarketplaceError($product_id, $error_msg);
            }
            $json['success'] = false;
            $json['error']   = $error_msg;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
