<?php
namespace Opencart\Admin\Controller\Shopmanager\Maintenance;

class Image extends \Opencart\System\Engine\Controller {
    
    public function index(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/maintenance/image');
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        
        // Filters
        if (isset($this->request->get['filter_product_id'])) {
            $filter_product_id = $this->request->get['filter_product_id'];
        } else {
            $filter_product_id = '';
        }
        
        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = '';
        }
        
        if (isset($this->request->get['filter_model'])) {
            $filter_model = $this->request->get['filter_model'];
        } else {
            $filter_model = '';
        }
        
        
        // Check if this is a form submission or first load
        $is_filtered = isset($this->request->get['filter_product_id']) || 
                      isset($this->request->get['filter_name']) || 
                      isset($this->request->get['filter_model']) ||
                      isset($this->request->get['filter_image_issue']) ||
                      isset($this->request->get['filter_low_resolution']) ||
                      isset($this->request->get['filter_wrong_path']) ||
                      isset($this->request->get['filter_old_nomenclature']) ||
                      isset($this->request->get['filter_orphan_images']) ||
                      isset($this->request->get['filter_zero_quantity']);
        
        // Get filter values - default to '1' only on first load, otherwise respect explicit values
        $filter_image_issue = isset($this->request->get['filter_image_issue']) ? $this->request->get['filter_image_issue'] : ($is_filtered ? '' : '1');
        $filter_low_resolution = isset($this->request->get['filter_low_resolution']) ? $this->request->get['filter_low_resolution'] : ($is_filtered ? '' : '1');
        $filter_wrong_path = isset($this->request->get['filter_wrong_path']) ? $this->request->get['filter_wrong_path'] : ($is_filtered ? '' : '1');
        $filter_old_nomenclature = isset($this->request->get['filter_old_nomenclature']) ? $this->request->get['filter_old_nomenclature'] : ($is_filtered ? '' : '1');
        $filter_orphan_images = isset($this->request->get['filter_orphan_images']) ? $this->request->get['filter_orphan_images'] : ($is_filtered ? '' : '1');
        $filter_zero_quantity = isset($this->request->get['filter_zero_quantity']) ? $this->request->get['filter_zero_quantity'] : '';
        
        // Build URL for filters
        $url = '';
        
        if (isset($this->request->get['filter_product_id'])) {
            $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
        }
        
        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode($this->request->get['filter_name']);
        }
        
        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode($this->request->get['filter_model']);
        }
        
        
        if (isset($this->request->get['filter_image_issue'])) {
            $url .= '&filter_image_issue=' . urlencode($this->request->get['filter_image_issue']);
        }
        
        if (isset($this->request->get['filter_low_resolution'])) {
            $url .= '&filter_low_resolution=' . $this->request->get['filter_low_resolution'];
        }
        
        if (isset($this->request->get['filter_wrong_path'])) {
            $url .= '&filter_wrong_path=' . $this->request->get['filter_wrong_path'];
        }
        
        if (isset($this->request->get['filter_old_nomenclature'])) {
            $url .= '&filter_old_nomenclature=' . $this->request->get['filter_old_nomenclature'];
        }

        if (isset($this->request->get['filter_orphan_images'])) {
            $url .= '&filter_orphan_images=' . $this->request->get['filter_orphan_images'];
        }
        
        if (isset($this->request->get['filter_zero_quantity'])) {
            $url .= '&filter_zero_quantity=' . $this->request->get['filter_zero_quantity'];
        }
        
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        
        $data['breadcrumbs'] = [];
        
        $data['breadcrumbs'][] = [
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        
        $data['breadcrumbs'][] = [
            'text' => ($lang['text_maintenance'] ?? ''),
            'href' => '#'
        ];
        
        $data['breadcrumbs'][] = [
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/maintenance/image', 'user_token=' . $this->session->data['user_token'] . $url)
        ];
        
        $data['list'] = $this->load->controller('shopmanager/maintenance/image.getList');
        
        $data['filter_product_id'] = $filter_product_id;
        $data['filter_name'] = $filter_name;
        $data['filter_model'] = $filter_model;
        $data['filter_image_issue'] = $filter_image_issue;
        $data['filter_low_resolution'] = $filter_low_resolution;
        $data['filter_wrong_path'] = $filter_wrong_path;
        $data['filter_old_nomenclature'] = $filter_old_nomenclature;
        $data['filter_orphan_images'] = $filter_orphan_images;
        $data['filter_zero_quantity'] = $filter_zero_quantity;
        
        $data['user_token'] = $this->session->data['user_token'];
        
        // Vérifier si validation est nécessaire
        $data['check_validation_url'] = $this->url->link('shopmanager/maintenance/image.checkValidationStatus', 'user_token=' . $this->session->data['user_token']);
        $data['create_table_url'] = $this->url->link('shopmanager/maintenance/image.createValidationTable', 'user_token=' . $this->session->data['user_token']);
        $data['validate_batch_url'] = $this->url->link('shopmanager/maintenance/image.validateBatch', 'user_token=' . $this->session->data['user_token']);
        
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('shopmanager/maintenance/image', $data));
    }
    
    /**
     * Check if validation is needed and get validation status
     */
    public function checkValidationStatus(): void {
        $this->load->model('shopmanager/maintenance/image');
        $json = [];
        
        $column_exists = $this->model_shopmanager_maintenance_image->maintenanceColumnExists();
        
        if (!$column_exists) {
            $total_products = $this->model_shopmanager_maintenance_image->getTotalProducts();
            $json['needs_validation'] = true;
            $json['total_products'] = $total_products;
            $json['validated'] = 0;
            $json['message'] = 'La colonne maintenance_data doit être créée.';
        } else {
            $total_needing_validation = $this->model_shopmanager_maintenance_image->getTotalNeedingValidation();
            
            if ($total_needing_validation > 0) {
                $json['needs_validation'] = true;
                $json['total_products'] = $total_needing_validation;
                $json['validated'] = 0;
                $json['percentage'] = 0;
            } else {
                $json['needs_validation'] = false;
                $json['total_products'] = 0;
                $json['validated'] = 0;
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Create validation table if not exists
     */
    public function createValidationTable(): void {
        $json = [];
        
        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            try {
                $sql = file_get_contents(DIR_APPLICATION . 'controller/shopmanager/install_maintenance_image_table.sql');
                
                // Execute each statement
                $statements = array_filter(array_map('trim', explode(';', $sql)));
                foreach ($statements as $statement) {
                    if (!empty($statement)) {
                        $this->db->query($statement);
                    }
                }
                
                $json['success'] = 'Colonne maintenance_data créée avec succès.';
            } catch (\Exception $e) {
                $json['error'] = 'Erreur lors de la création de la table: ' . $e->getMessage();
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Validate products in batch (AJAX)
     */
    public function validateBatch(): void {
        // Start output buffering and suppress all errors for clean JSON output
        ob_start();
        $old_error_reporting = error_reporting(0);
        ini_set('display_errors', '0');
        
        $this->load->model('shopmanager/maintenance/image');
        $json = [];
        
        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            $batch_size = 50;
            $offset = 0; // Always start at 0 since validated products are removed from the query
            
            $products = $this->model_shopmanager_maintenance_image->getProductsNeedingValidation($offset, $batch_size);
            $validated_count = 0;
            $errors = [];
            
            foreach ($products as $product) {
                try {
                    $this->validateProduct($product['product_id'], $product);
                    $validated_count++;
                } catch (\Exception $e) {
                    $errors[] = 'Product ' . $product['product_id'] . ': ' . $e->getMessage();
                    // Continue with next product instead of stopping
                }
            }
            
            // Recalculate total after validation
            $total_to_validate = $this->model_shopmanager_maintenance_image->getTotalNeedingValidation();
            
            $json['success'] = true;
            $json['validated'] = $validated_count;
            $json['remaining'] = $total_to_validate;
            $json['next_offset'] = 0; // Always 0
            $json['completed'] = $total_to_validate <= 0 || $validated_count == 0;
            
            if (!empty($errors)) {
                $json['warnings'] = $errors;
            }
        }
        
        // Clean buffer and restore error reporting
        ob_end_clean();
        error_reporting($old_error_reporting);
        ini_set('display_errors', '1');
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Validate a single product and store results
     */
    private function validateProduct($product_id, $product_data = null): void {
        $this->load->model('shopmanager/maintenance/image');
        
        if (!$product_data) {
            $product_data = $this->model_shopmanager_maintenance_image->getProduct($product_id);
        }
        
        if (!$product_data) {
            return;
        }
        
        $this->load->model('tool/image');
        
        $validation = [
            'product_id' => $product_id,
            'validation_date' => date('Y-m-d H:i:s'),
            'has_issues' => 0,
            'issue_types' => '',
            'main_image_status' => 'ok',
            'main_image_path' => $product_data['image'],
            'main_image_resolution' => '',
            'secondary_images_count' => 0,
            'secondary_images_missing' => 0,
            'secondary_images_issues' => '[]',
            'orphan_images_count' => 0,
            'orphan_images_list' => '[]',
            'needs_revalidation' => 0
        ];
        
        $issues = [];
        
        // Check main image
        if (empty($product_data['image'])) {
            $validation['main_image_status'] = 'missing';
            $issues[] = 'missing';
        } else {
            $image_path = DIR_IMAGE . $product_data['image'];
            
            if (!file_exists($image_path)) {
                $validation['main_image_status'] = 'not_found';
                $issues[] = 'missing';
            } else if (!is_file($image_path)) {
                // Path exists but is not a file (probably a directory)
                $validation['main_image_status'] = 'not_found';
                $issues[] = 'missing';
            } else if (filesize($image_path) == 0) {
                // File is empty/corrupted
                $validation['main_image_status'] = 'not_found';
                $issues[] = 'missing';
            } else {
                // Check resolution
                $image_info = @getimagesize($image_path);
                if ($image_info) {
                    $width = $image_info[0];
                    $height = $image_info[1];
                    $validation['main_image_resolution'] = $width . 'x' . $height;
                    
                    if ($width < 800 || $height < 800) {
                        $validation['main_image_status'] = 'low_res';
                        $issues[] = 'low_res';
                    }
                }
                
                // Check wrong path (old directories)
                $old_dirs = ['data2017/', 'data2018/', 'data2019/', 'data2020/', 'costumes/', 'products/'];
                foreach ($old_dirs as $old_dir) {
                    if (strpos($product_data['image'], $old_dir) !== false) {
                        $validation['main_image_status'] = 'wrong_path';
                        $issues[] = 'wrong_path';
                        break;
                    }
                }
                
                // Check nomenclature: catalog/product/XX/XXYYYY/XXYYYYpriZZ.webp or XXYYYYsecZZ.webp
                $image_basename = basename($product_data['image'], '.webp');
                $image_basename = basename($image_basename, '.jpg');
                $image_basename = basename($image_basename, '.png');
                
                $is_correct_nomenclature = false;
                $expected_subfolder = substr((string)$product_id, 0, 2);
                $expected_path_pattern = 'catalog/product/' . $expected_subfolder . '/' . $product_id . '/';
                
                // Check if path structure is correct: catalog/product/XX/XXYYYY/
                if (strpos($product_data['image'], $expected_path_pattern) !== false) {
                    // Check if filename follows pattern: XXYYYYpriZZ or XXYYYYsecZZ
                    if (preg_match('/^' . preg_quote($product_id, '/') . '(pri|sec)\d+$/', $image_basename)) {
                        $is_correct_nomenclature = true;
                    }
                }
                
                if (!$is_correct_nomenclature) {
                    if (!in_array('old_nomenclature', $issues)) {
                        $issues[] = 'old_nomenclature';
                    }
                }
            }
        }
        
        // Check secondary images - store ALL of them (not just problematic ones)
        $secondary_images = $this->model_shopmanager_maintenance_image->getSecondaryImages($product_id);
        
        $validation['secondary_images_count'] = count($secondary_images);
        $secondary_all = [];
        $missing_count = 0;
        
        foreach ($secondary_images as $sec_img) {
            $sec_path = DIR_IMAGE . $sec_img['image'];
            $sec_data = [
                'image' => $sec_img['image']
            ];
            
            if (!file_exists($sec_path) || !is_file($sec_path) || filesize($sec_path) == 0) {
                $missing_count++;
                $sec_data['issue'] = 'not_found';
                if (!in_array('missing', $issues)) {
                    $issues[] = 'missing';
                }
            } else {
                // Check resolution
                $image_info = @getimagesize($sec_path);
                if ($image_info) {
                    $sec_data['resolution'] = $image_info[0] . 'x' . $image_info[1];
                    if ($image_info[0] < 800 || $image_info[1] < 800) {
                        $sec_data['issue'] = 'low_res';
                        if (!in_array('low_res', $issues)) {
                            $issues[] = 'low_res';
                        }
                    } else {
                        $sec_data['issue'] = 'ok';
                    }
                } else {
                    $sec_data['issue'] = 'ok';
                }
            }
            
            $secondary_all[] = $sec_data;
        }
        
        $validation['secondary_images_missing'] = $missing_count;
        $validation['secondary_images_all'] = $secondary_all; // Keep as array
        
        // Check for orphan images
        $orphans = $this->findOrphanImages($product_id, $product_data['model']);
        $validation['orphan_images_count'] = count($orphans);
        $validation['orphan_images_list'] = $orphans; // Keep as array
        
        // Set final status
        $validation['has_issues'] = !empty($issues) ? 1 : 0;
        $validation['issue_types'] = array_unique($issues);
        
        // Get existing maintenance_data
        $product = $this->model_shopmanager_maintenance_image->getProduct($product_id);
        $maintenance = [];
        if (!empty($product['maintenance_data'])) {
            $maintenance = json_decode($product['maintenance_data'], true) ?: [];
        }
        
        // Update only the images section - optimize storage
        $maintenance['images'] = [
            'validation_date' => $validation['validation_date'],
            'has_issues' => $validation['has_issues'],
            'issue_types' => $validation['issue_types']
        ];
        
        // Main image: store details only if there's a problem
        if ($validation['main_image_status'] !== 'ok') {
            $maintenance['images']['main_status'] = $validation['main_image_status'];
            $maintenance['images']['main_path'] = $validation['main_image_path'];
            if (!empty($validation['main_image_resolution'])) {
                $maintenance['images']['main_resolution'] = $validation['main_image_resolution'];
            }
        } else {
            $maintenance['images']['main_status'] = 'ok';
        }
        
        // Secondary images: store ALL of them for display
        if ($validation['secondary_images_count'] > 0) {
            $maintenance['images']['secondary_count'] = $validation['secondary_images_count'];
            $maintenance['images']['secondary_missing'] = $validation['secondary_images_missing'];
            $maintenance['images']['secondary_all'] = $validation['secondary_images_all'];
        }
        
        // Orphan images: store only if found
        if ($validation['orphan_images_count'] > 0) {
            $maintenance['images']['orphan_count'] = $validation['orphan_images_count'];
            $maintenance['images']['orphan_list'] = $validation['orphan_images_list'];
        }
        
        // Save as JSON
        $json_data = json_encode($maintenance, JSON_UNESCAPED_UNICODE);
        
        if ($json_data === false) {
            throw new \Exception('JSON encoding failed for product ' . $product_id);
        }
        
        $this->db->query("
            UPDATE `" . DB_PREFIX . "product` 
            SET maintenance_data = '" . $this->db->escape($json_data) . "' 
            WHERE product_id = " . (int)$product_id
        );
    }
    
    public function list(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $data = $data ?? [];
        $data += $lang;
        
        $this->response->setOutput($this->getList());
    }
    
    public function getList(): string {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $this->load->model('tool/image');
        $this->load->model('shopmanager/maintenance/image');
        
        // Filters
        $filter_product_id = $this->request->get['filter_product_id'] ?? '';
        $filter_name = $this->request->get['filter_name'] ?? '';
        $filter_model = $this->request->get['filter_model'] ?? '';
        
        // Check if this is a form submission or first load
        $is_filtered = isset($this->request->get['filter_product_id']) || 
                      isset($this->request->get['filter_name']) || 
                      isset($this->request->get['filter_model']) ||
                      isset($this->request->get['filter_image_issue']) ||
                      isset($this->request->get['filter_low_resolution']) ||
                      isset($this->request->get['filter_wrong_path']) ||
                      isset($this->request->get['filter_old_nomenclature']) ||
                      isset($this->request->get['filter_orphan_images']) ||
                      isset($this->request->get['filter_zero_quantity']);
        
        // Default filters on first load: show all problem types except zero quantity
        $filter_image_issue = $this->request->get['filter_image_issue'] ?? ($is_filtered ? '' : '1');
        $filter_low_resolution = $this->request->get['filter_low_resolution'] ?? ($is_filtered ? '' : '1');
        $filter_wrong_path = $this->request->get['filter_wrong_path'] ?? ($is_filtered ? '' : '1');
        $filter_old_nomenclature = $this->request->get['filter_old_nomenclature'] ?? ($is_filtered ? '' : '1');
        $filter_orphan_images = $this->request->get['filter_orphan_images'] ?? ($is_filtered ? '' : '1');
        $filter_zero_quantity = $this->request->get['filter_zero_quantity'] ?? '';
        
        $page = (int)($this->request->get['page'] ?? 1);
        $sort = $this->request->get['sort'] ?? 'p.product_id';
        $order = $this->request->get['order'] ?? 'ASC';
        
        $limit = 25;
        $start = ($page - 1) * $limit;
        
        // Build URL for filters
        $url = '';
        if ($filter_product_id) $url .= '&filter_product_id=' . $filter_product_id;
        if ($filter_name) $url .= '&filter_name=' . urlencode($filter_name);
        if ($filter_model) $url .= '&filter_model=' . urlencode($filter_model);
        if ($filter_image_issue) $url .= '&filter_image_issue=' . $filter_image_issue;
        if ($filter_low_resolution) $url .= '&filter_low_resolution=' . $filter_low_resolution;
        if ($filter_wrong_path) $url .= '&filter_wrong_path=' . $filter_wrong_path;
        if ($filter_old_nomenclature) $url .= '&filter_old_nomenclature=' . $filter_old_nomenclature;
        if ($filter_orphan_images) $url .= '&filter_orphan_images=' . $filter_orphan_images;
        if ($filter_zero_quantity) $url .= '&filter_zero_quantity=' . $filter_zero_quantity;
        if (isset($this->request->get['sort'])) $url .= '&sort=' . $sort;
        if (isset($this->request->get['order'])) $url .= '&order=' . $order;
        
        $data['action'] = $this->url->link('shopmanager/maintenance/image.list', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['user_token'] = $this->session->data['user_token'];
        
        // Build filter data for model
        $filter_data = [
            'filter_product_id' => $filter_product_id,
            'filter_name' => $filter_name,
            'filter_model' => $filter_model,
            'filter_image_issue' => $filter_image_issue,
            'filter_low_resolution' => $filter_low_resolution,
            'filter_wrong_path' => $filter_wrong_path,
            'filter_old_nomenclature' => $filter_old_nomenclature,
            'filter_orphan_images' => $filter_orphan_images,
            'filter_zero_quantity' => $filter_zero_quantity,
            'sort' => $sort,
            'order' => $order,
            'start' => $start,
            'limit' => $limit
        ];
        
        $results = $this->model_shopmanager_maintenance_image->getProducts($filter_data);
        $total_filtered = $this->model_shopmanager_maintenance_image->getTotalProductsFiltered($filter_data);
        
        $data['products'] = [];
        $data['missing_images'] = 0;
        $data['total_checked'] = count($results);
        
        foreach ($results as $row) {
            $product_id = $row['product_id'];
            $maintenance = !empty($row['maintenance_data']) ? json_decode($row['maintenance_data'], true) : null;
            
            // Si pas de cache, valider maintenant
            if (!$maintenance || !isset($maintenance['images'])) {
                $this->validateProduct($product_id, $row);
                // Recharger les données
                $fresh = $this->model_shopmanager_maintenance_image->getProduct($product_id);
                $maintenance = !empty($fresh['maintenance_data']) ? json_decode($fresh['maintenance_data'], true) : [];
            }
            
            $img_data = $maintenance['images'] ?? [];
            $has_issues = $img_data['has_issues'] ?? 0;
            $main_status = $img_data['main_status'] ?? 'ok';
            $main_path = $img_data['main_path'] ?? $row['image'] ?? '';
            $main_resolution = $img_data['main_resolution'] ?? '';
            $secondary_count = $img_data['secondary_count'] ?? 0;
            $secondary_missing = $img_data['secondary_missing'] ?? 0;
            
            // Support both old and new cache format
            $secondary_all = $img_data['secondary_all'] ?? [];
            $secondary_issues_old = $img_data['secondary_issues'] ?? [];
            
            $orphan_count = $img_data['orphan_count'] ?? 0;
            $orphan_list = $img_data['orphan_list'] ?? [];
            $issue_types = $img_data['issue_types'] ?? [];
            
            // Générer thumbnails seulement si fichier existe
            $main_image_thumb = '';
            $main_image_fullsize = '';
            if (!empty($main_path) && $main_status !== 'missing' && $main_status !== 'not_found') {
                // Suppress mkdir warnings during thumbnail generation
                set_error_handler(function($errno, $errstr) {
                    if (strpos($errstr, 'mkdir') !== false && strpos($errstr, 'File exists') !== false) {
                        return true;
                    }
                    return false;
                });
                
                $main_image_thumb = $this->model_tool_image->resize($main_path, 40, 40);
                $main_image_fullsize = $main_path;
                
                restore_error_handler();
            }
            
            // Build secondary images avec thumbnails
            $secondary_images_status = [];
            
            // New format: secondary_all contains ALL images with status
            if ($secondary_count > 0 && !empty($secondary_all)) {
                // Suppress mkdir warnings during thumbnail generation
                set_error_handler(function($errno, $errstr) {
                    if (strpos($errstr, 'mkdir') !== false && strpos($errstr, 'File exists') !== false) {
                        return true;
                    }
                    return false;
                });
                
                foreach ($secondary_all as $sec_data) {
                    $img_path = $sec_data['image'] ?? '';
                    $issue = $sec_data['issue'] ?? 'ok';
                    $exists = ($issue !== 'not_found');
                    
                    $secondary_images_status[] = [
                        'image' => $img_path,
                        'exists' => $exists,
                        'thumb' => $exists && !empty($img_path) ? $this->model_tool_image->resize($img_path, 40, 40) : '',
                        'fullsize' => $img_path,
                        'resolution' => $sec_data['resolution'] ?? '',
                        'is_low_resolution' => ($issue === 'low_res'),
                        'wrong_extension' => false
                    ];
                }
                
                restore_error_handler();
            }
            // Old format: secondary_issues contains only problematic images
            else if ($secondary_count > 0 && !empty($secondary_issues_old)) {
                foreach ($secondary_issues_old as $sec_issue) {
                    $img_path = $sec_issue['image'] ?? '';
                    $exists = !isset($sec_issue['issue']) || $sec_issue['issue'] !== 'not_found';
                    
                    $secondary_images_status[] = [
                        'image' => $img_path,
                        'exists' => $exists,
                        'thumb' => $exists && !empty($img_path) ? $this->model_tool_image->resize($img_path, 40, 40) : '',
                        'fullsize' => $img_path,
                        'resolution' => $sec_issue['resolution'] ?? '',
                        'is_low_resolution' => isset($sec_issue['issue']) && $sec_issue['issue'] === 'low_res',
                        'wrong_extension' => false
                    ];
                }
            }
            // Old format: no issues stored, but has secondary_status = 'ok'
            else if ($secondary_count > 0 && isset($img_data['secondary_status']) && $img_data['secondary_status'] === 'ok') {
                // Cannot display images without paths - need re-validation
                // For now, just show count
            }
            
            // Build orphan images
            $orphan_images = [];
            if ($orphan_count > 0 && !empty($orphan_list)) {
                // Suppress mkdir warnings during thumbnail generation
                set_error_handler(function($errno, $errstr) {
                    if (strpos($errstr, 'mkdir') !== false && strpos($errstr, 'File exists') !== false) {
                        return true; // Suppress the warning
                    }
                    return false; // Let other errors through
                });
                
                foreach ($orphan_list as $orphan) {
                    $orphan_images[] = [
                        'image' => $orphan['image'],
                        'thumb' => $this->model_tool_image->resize($orphan['image'], 40, 40),
                        'fullsize' => $orphan['image'],
                        'resolution' => $orphan['resolution'] ?? '',
                        'is_low_resolution' => false
                    ];
                }
                
                restore_error_handler();
            }
            
            // Build error messages
            $error_messages = [];
            $has_missing = in_array('missing', $issue_types);
            $is_low_resolution = in_array('low_res', $issue_types);
            $is_wrong_path = in_array('wrong_path', $issue_types);
            $is_old_nomenclature = in_array('old_nomenclature', $issue_types);
            
            if ($main_status === 'missing') {
                $error_messages[] = 'No main image set';
            } elseif ($main_status === 'not_found') {
                $error_messages[] = 'Missing main image: ' . $main_path;
            }
            if ($secondary_missing > 0) {
                $error_messages[] = $secondary_missing . ' missing secondary image(s)';
            }
            if ($is_low_resolution) {
                $error_messages[] = 'Low resolution: ' . $main_resolution;
            }
            if ($is_wrong_path) {
                $error_messages[] = 'Wrong directory (belongs to different product_id): ' . $main_path;
            }
            if ($is_old_nomenclature) {
                $error_messages[] = 'Old naming format (should be /XX/XXYYY/filename.webp): ' . $main_path;
            }
            
            if ($has_missing) {
                $data['missing_images']++;
            }
            
            $data['products'][] = [
                'product_id' => $product_id,
                'name' => $row['name'] ?? 'N/A',
                'model' => $row['model'],
                'main_image' => $main_path,
                'main_image_exists' => $main_status !== 'missing' && $main_status !== 'not_found',
                'main_image_path' => DIR_IMAGE . $main_path,
                'main_image_thumb' => $main_image_thumb,
                'main_image_fullsize' => $main_image_fullsize,
                'main_image_resolution' => $main_resolution,
                'is_low_resolution' => $is_low_resolution,
                'is_wrong_path' => $is_wrong_path,
                'is_old_nomenclature' => $is_old_nomenclature,
                'secondary_images' => $secondary_images_status,
                'orphan_images' => $orphan_images,
                'missing_secondary' => $secondary_missing,
                'has_missing' => $has_missing,
                'status_message' => !empty($error_messages) ? implode('<br>', $error_messages) : 'OK',
                'edit' => $this->url->link('shopmanager/catalog/product.form', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product_id)
            ];
        }
        
        // Pagination
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total_filtered,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link('shopmanager/maintenance/image.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
        ]);
        
        $data['results'] = sprintf(($lang['text_displaying'] ?? ''), (($page - 1) * $limit) + 1, min($page * $limit, $total_filtered), $total_filtered, ceil($total_filtered / $limit));
        
        // Language variables
        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_list'] = ($lang['text_list'] ?? '');
        $data['text_statistics'] = ($lang['text_statistics'] ?? '');
        $data['text_total_checked'] = ($lang['text_total_checked'] ?? '');
        $data['text_products'] = ($lang['text_products'] ?? '');
        $data['text_missing_images'] = ($lang['text_missing_images'] ?? '');
        $data['text_no_image'] = ($lang['text_no_image'] ?? '');
        $data['text_file_not_found'] = ($lang['text_file_not_found'] ?? '');
        $data['text_none'] = ($lang['text_none'] ?? '');
        $data['text_missing'] = ($lang['text_missing'] ?? '');
        $data['text_total'] = ($lang['text_total'] ?? '');
        $data['column_id'] = ($lang['column_id'] ?? '');
        $data['column_model'] = ($lang['column_model'] ?? '');
        $data['column_name'] = ($lang['column_name'] ?? '');
        $data['column_main_image'] = ($lang['column_main_image'] ?? '');
        $data['column_secondary_images'] = ($lang['column_secondary_images'] ?? '');
        $data['column_orphan_images'] = ($lang['column_orphan_images'] ?? '');
        $data['column_status'] = ($lang['column_status'] ?? '');
        $data['column_action'] = ($lang['column_action'] ?? '');
        
        // Sort links
        $url_sort = $url; // Réutiliser l'URL déjà construite
        
        $data['sort'] = $sort;
        $data['order'] = $order;
        
        $data['sort_product_id'] = $this->url->link('shopmanager/maintenance/image.list', 'user_token=' . $this->session->data['user_token'] . $url_sort . '&sort=p.product_id&order=' . ($sort == 'p.product_id' && $order == 'ASC' ? 'DESC' : 'ASC'));
        $data['sort_model'] = $this->url->link('shopmanager/maintenance/image.list', 'user_token=' . $this->session->data['user_token'] . $url_sort . '&sort=p.model&order=' . ($sort == 'p.model' && $order == 'ASC' ? 'DESC' : 'ASC'));
        $data['sort_name'] = $this->url->link('shopmanager/maintenance/image.list', 'user_token=' . $this->session->data['user_token'] . $url_sort . '&sort=pd.name&order=' . ($sort == 'pd.name' && $order == 'ASC' ? 'DESC' : 'ASC'));
        $data['entry_language'] = ($lang['entry_language'] ?? '');
        $data['button_print'] = ($lang['button_print'] ?? '');
        $data['button_edit'] = ($lang['button_edit'] ?? '');
        $data['button_filter'] = ($lang['button_filter'] ?? '');
        $data['button_import_ebay_images'] = ($lang['button_import_ebay_images'] ?? '');
        $data['button_check_ebay_images'] = ($lang['button_check_ebay_images'] ?? '');
        $data['status_problem'] = ($lang['status_problem'] ?? '');
        $data['status_ok'] = ($lang['status_ok'] ?? '');
        $data['text_filter'] = ($lang['text_filter'] ?? '');
        $data['entry_product_id'] = ($lang['entry_product_id'] ?? '');
        $data['entry_name'] = ($lang['entry_name'] ?? '');
        $data['entry_model'] = ($lang['entry_model'] ?? '');
        $data['entry_status'] = ($lang['entry_status'] ?? '');
        $data['text_import_ebay_modal_title'] = ($lang['text_import_ebay_modal_title'] ?? '');
        $data['text_import_ebay_confirm'] = ($lang['text_import_ebay_confirm'] ?? '');
        $data['text_import_ebay_preparing'] = ($lang['text_import_ebay_preparing'] ?? '');
        $data['text_import_ebay_processing'] = ($lang['text_import_ebay_processing'] ?? '');
        $data['text_import_ebay_complete'] = ($lang['text_import_ebay_complete'] ?? '');
        $data['text_import_ebay_refreshing'] = ($lang['text_import_ebay_refreshing'] ?? '');
        $data['text_import_ebay_no_selection'] = ($lang['text_import_ebay_no_selection'] ?? '');
        $data['text_check_ebay_modal_title'] = ($lang['text_check_ebay_modal_title'] ?? '');
        $data['text_check_ebay_confirm'] = ($lang['text_check_ebay_confirm'] ?? '');
        $data['text_check_ebay_preparing'] = ($lang['text_check_ebay_preparing'] ?? '');
        $data['text_check_ebay_processing'] = ($lang['text_check_ebay_processing'] ?? '');
        $data['text_check_ebay_complete'] = ($lang['text_check_ebay_complete'] ?? '');
        $data['text_check_ebay_no_selection'] = ($lang['text_check_ebay_no_selection'] ?? '');
        $data['text_check_ebay_notify_more'] = ($lang['text_check_ebay_notify_more'] ?? '');
        $data['text_check_ebay_ok'] = ($lang['text_check_ebay_ok'] ?? '');
        
        return $this->load->view('shopmanager/maintenance/image_list', $data);
    }

    /**
     * Compare eBay image count/names vs DB images (main + secondary) for one product.
     */
    public function checkEbayImageComparison(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            $product_id = isset($this->request->post['product_id']) ? (int)$this->request->post['product_id'] : 0;

            if ($product_id <= 0) {
                $json['error'] = ($lang['error_product_id_required'] ?? 'Product ID is required.');
            } else {
                $this->load->model('shopmanager/catalog/product');
                $this->load->model('shopmanager/ebay');
                $this->load->model('shopmanager/marketplace');

                $json = $this->buildEbayImageComparison($product_id, $lang);
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Remove missing images from database
     */
    public function removeMissingImages(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/maintenance/image');
        
        $json = [];
        
        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            if (isset($this->request->post['images']) && is_array($this->request->post['images'])) {
                
                $removed_count = 0;
                
                foreach ($this->request->post['images'] as $image_data) {
                    if (!isset($image_data['product_id']) || !isset($image_data['type'])) {
                        continue;
                    }
                    
                    $product_id = (int)$image_data['product_id'];
                    $type = $image_data['type'];
                    $image_path = isset($image_data['image_path']) ? $image_data['image_path'] : '';
                    
                    if ($type === 'main') {
                        $this->model_shopmanager_maintenance_image->updateMainImage($product_id, '');
                        $removed_count++;
                    } elseif ($type === 'secondary' && $image_path) {
                        $this->model_shopmanager_maintenance_image->deleteSecondaryImage($product_id, $image_path);
                        $removed_count++;
                    }
                }
                
                if ($removed_count > 0) {
                    $json['success'] = sprintf('Successfully removed %d missing image(s) from database.', $removed_count);
                } else {
                    $json['error'] = 'No images were removed.';
                }
            } else {
                $json['error'] = 'No images specified for removal.';
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Bulk: import eBay images for ALL products that have an image count mismatch.
     * After importing, resets ebay_image_count = 0 so the next eBay import
     * re-fetches the true count from the API.
     * Called from the Image Mismatch tab in the Inventory/Sync dashboard.
     */
    public function bulkImportMismatchImages(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = $lang['error_permission'] ?? 'Permission denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(600);
        ini_set('memory_limit', '512M');

        $this->load->model('shopmanager/catalog/product');
        $this->load->model('shopmanager/tools');
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        $this->load->model('tool/image');

        $this->load->model('shopmanager/maintenance/image');
        $rows = $this->model_shopmanager_maintenance_image->getImageMismatchList();

        if (empty($rows)) {
            $json['success'] = true;
            $json['total'] = 0;
            $json['message'] = 'No image mismatch products to process.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $total         = count($rows);
        $success_count = 0;
        $skipped_count = 0;
        $error_count   = 0;
        $results       = [];

        foreach ($rows as $row) {
            $product_id = (int)$row['product_id'];
            $oc_count   = (int)$row['oc_image_count'];
            $ebay_count = (int)$row['ebay_count'];

            if ($oc_count > $ebay_count) {
                // OC a plus que eBay → pousser les images OC vers eBay via ReviseItem complet
                try {
                 
                    $this->model_shopmanager_marketplace->updateMarketplaceListings($product_id);
                    $this->model_shopmanager_marketplace->resetEbayImageCount($product_id);
                    $success_count++;
                    $result = ['success' => true, 'skipped' => false,
                               'image_count' => $oc_count,
                               'message' => 'OC→eBay: images poussées via ReviseItem'];
                } catch (\Exception $e) {
                    $error_count++;
                    $result = ['success' => false, 'skipped' => false,
                               'image_count' => 0, 'message' => $e->getMessage()];
                }
            } else {
                // eBay a plus que OC → importer les images eBay dans OC
                $result = $this->importEbayImagesForProduct($product_id, $lang);
                $this->model_shopmanager_marketplace->updateMarketplaceListings($product_id);
                $this->model_shopmanager_marketplace->resetEbayImageCount($product_id);
               
                if (!empty($result['success'])) {
                    $success_count++;
                } elseif (!empty($result['skipped'])) {
                    $skipped_count++;
                } else {
                    $error_count++;
                }
            }

            $results[] = [
                'product_id'  => $product_id,
                'success'     => $result['success']     ?? false,
                'skipped'     => $result['skipped']     ?? false,
                'image_count' => $result['image_count'] ?? 0,
                'message'     => $result['message']     ?? ($result['error'] ?? ''),
            ];
        }

        $json['success']       = true;
        $json['total']         = $total;
        $json['success_count'] = $success_count;
        $json['skipped_count'] = $skipped_count;
        $json['error_count']   = $error_count;
        $json['results']       = $results;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Import eBay images for a single product (by product_id via POST).
     * Also resets ebay_image_count = 0 to force re-fetch on next eBay import.
     * Used for the per-row "Import" button in the Image Mismatch tab.
     */
    public function importEbayImagesForProductAjax(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = $lang['error_permission'] ?? 'Permission denied';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $product_id = (int)($this->request->post['product_id'] ?? 0);

        if ($product_id <= 0) {
            $json['error'] = 'Missing product_id';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        set_time_limit(120);
        ini_set('memory_limit', '256M');

        $this->load->model('shopmanager/catalog/product');
        $this->load->model('shopmanager/tools');
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
        $this->load->model('tool/image');

        // Direction via models uniquement — aucun SQL dans le controller
        $product    = $this->model_shopmanager_catalog_product->getProduct($product_id);
        $sec_images = $this->model_shopmanager_catalog_product->getImages($product_id);
        $oc_count   = ((!empty($product['image']) && $product['image'] !== 'no_image.png') ? 1 : 0)
                    + count($sec_images);
        $ebay_count = $this->model_shopmanager_marketplace->getEbayImageCount($product_id);

        if ($oc_count > $ebay_count) {
            // OC a plus que eBay → pousser les images OC vers eBay via ReviseItem complet
          
            $this->model_shopmanager_marketplace->updateMarketplaceListings($product_id);
            $this->model_shopmanager_marketplace->resetEbayImageCount($product_id);
            $this->model_shopmanager_marketplace->resetSyncState($product_id);
            $json = ['success' => true, 'image_count' => $oc_count,
                     'direction' => 'oc_to_ebay',
                     'message' => 'Images OC poussées vers eBay via ReviseItem'];
        } else {
            // eBay a plus (ou égal) → importer les images eBay dans OC
            $result = $this->importEbayImagesForProduct($product_id, $lang);
            $this->model_shopmanager_marketplace->resetEbayImageCount($product_id);
            if (!empty($result['success'])) {
                $this->model_shopmanager_marketplace->resetSyncState($product_id);
            }
            $json = $result;
            $json['direction'] = 'ebay_to_oc';
        }
        $json['ebay_image_count_reset'] = true;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Fix image nomenclature and convert to webp
     */
    public function fixImages(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $data = $data ?? [];
        $data += $lang;
        
        $json = [];
        
        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            if (isset($this->request->post['product_ids']) && is_array($this->request->post['product_ids'])) {
                $this->load->model('shopmanager/catalog/product');
                $this->load->model('shopmanager/maintenance/image');
                
                $fixed_count = 0;
                $converted_count = 0;
                $orphans_added = 0;
                $errors = [];
                
                // Get orphans to add from POST data
                $orphans_to_add = [];
                if (isset($this->request->post['orphans']) && is_array($this->request->post['orphans'])) {
                    foreach ($this->request->post['orphans'] as $orphan_data) {
                        if (isset($orphan_data['product_id']) && isset($orphan_data['image_path'])) {
                            $pid = (int)$orphan_data['product_id'];
                            if (!isset($orphans_to_add[$pid])) {
                                $orphans_to_add[$pid] = [];
                            }
                            $orphans_to_add[$pid][] = $orphan_data['image_path'];
                        }
                    }
                }
                
                foreach ($this->request->post['product_ids'] as $product_id) {
                    $product_id = (int)$product_id;
                    
                    // Get product main image and all secondary images
                    $product = $this->model_shopmanager_catalog_product->getProduct($product_id);
                    if (!$product) {
                        continue;
                    }
                    
                    $images_to_process = [];
                    
                    // Add main image
                    if (!empty($product['image'])) {
                        $images_to_process[] = [
                            'path' => $product['image'],
                            'type' => 'main'
                        ];
                    }
                    
                    // Add secondary images
                    $secondary_images = $this->model_shopmanager_catalog_product->getImages($product_id);
                    foreach ($secondary_images as $img) {
                        $images_to_process[] = [
                            'path' => $img['image'],
                            'type' => 'secondary',
                            'product_image_id' => $img['product_image_id']
                        ];
                    }
                    
                    // Process each image
                    foreach ($images_to_process as $img_data) {
                        $old_path = $img_data['path'];
                        $old_full_path = DIR_IMAGE . $old_path;
                        
                        // Skip if file doesn't exist
                        if (!file_exists($old_full_path)) {
                            continue;
                        }
                        
                        // Check if this is a wrong_path (belongs to another product)
                        // wrong_path = doesn't contain current product_id in path
                        $is_wrong_path = (strpos($old_path, (string)$product_id) === false);
                        
                        // Check correct directory structure
                        $expected_subfolder = substr($product_id, 0, 2);
                        $expected_dir = 'catalog/product/' . $expected_subfolder . '/' . $product_id . '/';
                        
                        // Generate new filename with proper nomenclature
                        // Format: {product_id}pri{XX}.webp or {product_id}sec{XX}.webp
                        $random_suffix = rand(10, 99);
                        $prefix = ($img_data['type'] === 'main') ? 'pri' : 'sec';
                        $new_filename = $product_id . $prefix . $random_suffix . '.webp';
                        
                        // Make sure the filename is unique in the directory
                        $counter = 0;
                        while (file_exists(DIR_IMAGE . $expected_dir . $new_filename) && $counter < 100) {
                            $random_suffix = rand(10, 99);
                            $new_filename = $product_id . $prefix . $random_suffix . '.webp';
                            $counter++;
                        }
                        
                        // Check if conversion is needed - convert anything that's not .webp
                        // This includes files with no extension, wrong extensions, or junk like ._AC_
                        $needs_conversion = !preg_match('/\.webp$/i', $old_path);
                        
                        $new_path = $expected_dir . $new_filename;
                        $new_full_path = DIR_IMAGE . $new_path;
                        
                        // Create directory if it doesn't exist
                        $new_dir_full = dirname($new_full_path);
                        if (!is_dir($new_dir_full)) {
                            if (!mkdir($new_dir_full, 0755, true)) {
                                $errors[] = "Failed to create directory for product $product_id";
                                continue;
                            }
                        }
                        
                        // Convert to webp if needed
                        if ($needs_conversion) {
                            if ($this->convertToWebp($old_full_path, $new_full_path)) {
                                $converted_count++;
                                // Only delete old file if it's not a wrong_path (belongs to this product)
                                if (!$is_wrong_path) {
                                    @unlink($old_full_path);
                                }
                            } else {
                                // If conversion fails, just copy the file
                                if (!copy($old_full_path, $new_full_path)) {
                                    $errors[] = "Failed to copy image for product $product_id";
                                    continue;
                                }
                            }
                        } else {
                            // If wrong_path, copy instead of move (belongs to another product)
                            if ($is_wrong_path) {
                                if (!copy($old_full_path, $new_full_path)) {
                                    $errors[] = "Failed to copy image for product $product_id";
                                    continue;
                                }
                            } else {
                                // Move the file (old_nomenclature case)
                                if (!rename($old_full_path, $new_full_path)) {
                                    $errors[] = "Failed to move image for product $product_id";
                                    continue;
                                }
                            }
                        }
                        
                        // Update database
                        if ($img_data['type'] === 'main') {
                            $this->db->query("UPDATE `" . DB_PREFIX . "product` SET `image` = '" . $this->db->escape($new_path) . "' WHERE product_id = '" . $product_id . "'");
                        } else {
                            $this->db->query("UPDATE `" . DB_PREFIX . "product_image` SET `image` = '" . $this->db->escape($new_path) . "' WHERE product_image_id = '" . (int)$img_data['product_image_id'] . "'");
                        }
                        
                        $fixed_count++;
                    }
                    
                    // Process orphans for this product if any
                    if (isset($orphans_to_add[$product_id])) {
                        foreach ($orphans_to_add[$product_id] as $orphan_path) {
                            $old_full_path = DIR_IMAGE . $orphan_path;
                            
                            // Skip if file doesn't exist
                            if (!file_exists($old_full_path)) {
                                continue;
                            }
                            
                            // Generate new filename with proper nomenclature
                            $expected_subfolder = substr($product_id, 0, 2);
                            $expected_dir = 'catalog/product/' . $expected_subfolder . '/' . $product_id . '/';
                            
                            $random_suffix = rand(10, 99);
                            $new_filename = $product_id . 'sec' . $random_suffix . '.webp';
                            
                            // Make sure the filename is unique
                            $counter = 0;
                            while (file_exists(DIR_IMAGE . $expected_dir . $new_filename) && $counter < 100) {
                                $random_suffix = rand(10, 99);
                                $new_filename = $product_id . 'sec' . $random_suffix . '.webp';
                                $counter++;
                            }
                            
                            $new_path = $expected_dir . $new_filename;
                            $new_full_path = DIR_IMAGE . $new_path;
                            
                            // Create directory if needed
                            $new_dir_full = dirname($new_full_path);
                            if (!is_dir($new_dir_full)) {
                                if (!mkdir($new_dir_full, 0755, true)) {
                                    $errors[] = "Failed to create directory for orphan in product $product_id";
                                    continue;
                                }
                            }
                            
                            // Convert to webp if needed
                            $needs_conversion = !preg_match('/\.webp$/i', basename($orphan_path));
                            
                            if ($needs_conversion) {
                                if ($this->convertToWebp($old_full_path, $new_full_path)) {
                                    $converted_count++;
                                } else {
                                    // If conversion fails, copy
                                    if (!copy($old_full_path, $new_full_path)) {
                                        $errors[] = "Failed to copy orphan for product $product_id";
                                        continue;
                                    }
                                }
                            } else {
                                // Move/rename the file
                                if (!rename($old_full_path, $new_full_path)) {
                                    $errors[] = "Failed to move orphan for product $product_id";
                                    continue;
                                }
                            }
                            
                            // Add to database as secondary image
                            $this->model_shopmanager_maintenance_image->addSecondaryImage($product_id, $new_path);
                            $orphans_added++;
                            $fixed_count++;
                        }
                    }
                    
                    // Re-validate this product to update its status
                    // Use model method to get fresh product data
                    try {
                        $product_fresh = $this->model_shopmanager_maintenance_image->getFreshProduct($product_id);
                        if ($product_fresh) {
                            $this->validateProduct($product_id, $product_fresh);
                        }
                    } catch (\Exception $e) {
                        // Continue even if validation fails
                    }
                    
                    // After processing all images for this product, check and clean old directories
                    // Only for old_nomenclature (not wrong_path which belong to other products)
                    $old_directories_checked = [];
                    foreach ($images_to_process as $img_data) {
                        $old_path = $img_data['path'];
                        $old_dir = dirname(DIR_IMAGE . $old_path);
                        
                        // Check if this was a wrong_path (belongs to another product)
                        $was_wrong_path = (strpos($old_path, (string)$product_id) === false);
                        
                        // Skip cleanup if it was wrong_path (belongs to another product)
                        // Skip if already checked or if it's the new directory
                        if ($was_wrong_path || in_array($old_dir, $old_directories_checked) || strpos($old_path, $expected_dir) === 0) {
                            continue;
                        }
                        
                        $old_directories_checked[] = $old_dir;
                        
                        // Check if directory exists and get remaining files
                        if (is_dir($old_dir)) {
                            $remaining_files = array_diff(scandir($old_dir), ['.', '..']);
                            
                            if (empty($remaining_files)) {
                                // Directory is empty, delete it
                                @rmdir($old_dir);
                            } else {
                                // Directory has remaining files, add to warning list
                                if (!isset($json['remaining_files'])) {
                                    $json['remaining_files'] = [];
                                }
                                $json['remaining_files'][$product_id] = [
                                    'directory' => str_replace(DIR_IMAGE, '', $old_dir),
                                    'files' => array_values($remaining_files)
                                ];
                            }
                        }
                    }
                }
                
                if ($fixed_count > 0 || $converted_count > 0 || $orphans_added > 0) {
                    $message = "Successfully fixed $fixed_count image(s)";
                    if ($converted_count > 0) {
                        $message .= " and converted $converted_count image(s) to WebP";
                    }
                    if ($orphans_added > 0) {
                        $message .= ", added $orphans_added orphan(s)";
                    }
                    if (!empty($errors)) {
                        $message .= ". Errors: " . implode(', ', $errors);
                    }
                    $json['success'] = $message;
                } else {
                    $json['error'] = 'No images were fixed. ' . (!empty($errors) ? 'Errors: ' . implode(', ', $errors) : '');
                }
            } else {
                $json['error'] = 'No products specified for fixing.';
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function importEbayImagesForProduct(int $product_id, array $lang): array {
        $json = [
            'product_id' => $product_id,
            'success' => false,
            'skipped' => false,
            'image_count' => 0,
            'product_images' => [
                'primary' => null,
                'secondary' => []
            ]
        ];

        try {
            $marketplace_data = $this->model_shopmanager_marketplace->getMarketplace([
                'product_id' => $product_id,
                'marketplace_id' => 1
            ]);

            if (empty($marketplace_data) || !is_array($marketplace_data)) {
                $json['skipped'] = true;
                $json['message'] = ($lang['text_import_ebay_no_listing'] ?? 'No eBay listing found for this product.');
                return $json;
            }

            $first_account = reset($marketplace_data);
            $marketplace_item_id = trim((string)($first_account['marketplace_item_id'] ?? ''));

            if ($marketplace_item_id === '' || $marketplace_item_id === '0') {
                $json['skipped'] = true;
                $json['message'] = ($lang['text_import_ebay_no_listing'] ?? 'No eBay listing found for this product.');
                return $json;
            }

            $imageUrls = $this->model_shopmanager_ebay->getImages($marketplace_item_id);

            if (empty($imageUrls) || !is_array($imageUrls)) {
                $json['skipped'] = true;
                $json['message'] = ($lang['text_import_ebay_no_images'] ?? 'No images found in the eBay listing.');
                return $json;
            }

            $this->model_shopmanager_tools->deleteProductImages($product_id, 'all');

            $imported_count = 0;
            $primary_set = false;

            foreach ($imageUrls as $image_url) {
                if (empty($image_url)) {
                    continue;
                }

                if (!$primary_set) {
                    $primary_image = $this->model_shopmanager_tools->uploadImages($image_url, $product_id, 'pri');

                    if ($primary_image) {
                        $this->model_shopmanager_catalog_product->updateProductImage($product_id, $primary_image);
                        $json['product_images']['primary'] = [
                            'image' => $primary_image,
                            'thumb' => $this->model_tool_image->resize($primary_image, 100, 100)
                        ];
                        $primary_set = true;
                        $imported_count++;
                    }

                    continue;
                }

                $secondary_image = $this->model_shopmanager_tools->uploadImages($image_url, $product_id, 'sec');

                if ($secondary_image) {
                    $this->model_shopmanager_catalog_product->insertProductImage($product_id, $secondary_image);
                    $json['product_images']['secondary'][] = [
                        'image' => $secondary_image,
                        'thumb' => $this->model_tool_image->resize($secondary_image, 100, 100),
                        'sort_order' => 0
                    ];
                    $imported_count++;
                }
            }

            if ($imported_count > 0) {
                $this->load->model('shopmanager/maintenance/image');
                $product_fresh = $this->model_shopmanager_maintenance_image->getFreshProduct($product_id);

                if ($product_fresh) {
                    $this->validateProduct($product_id, $product_fresh);
                }

                $json['success'] = true;
                $json['image_count'] = $imported_count;
                $json['message'] = sprintf(
                    ($lang['text_import_ebay_success'] ?? 'Imported %d eBay image(s) for product #%d.'),
                    $imported_count,
                    $product_id
                );
            } else {
                $json['skipped'] = true;
                $json['message'] = ($lang['text_import_ebay_no_images'] ?? 'No images found in the eBay listing.');
            }
        } catch (\Throwable $e) {
            $json['error'] = sprintf(
                ($lang['text_import_ebay_error'] ?? 'Error importing eBay images for product #%d: %s'),
                $product_id,
                $e->getMessage()
            );
        }

        return $json;
    }

    private function buildEbayImageComparison(int $product_id, array $lang): array {
        $result = [
            'success' => false,
            'product_id' => $product_id,
            'notify' => false,
            'ebay_count' => 0,
            'db_count' => 0,
            'db_main_count' => 0,
            'db_secondary_count' => 0,
            'marketplace_item_id' => '',
            'ebay_image_names' => [],
            'db_image_names' => [],
            'missing_in_db' => [],
            'missing_on_ebay' => [],
        ];

        $marketplace_data = $this->model_shopmanager_marketplace->getMarketplace([
            'product_id' => $product_id,
            'marketplace_id' => 1
        ]);

        if (empty($marketplace_data) || !is_array($marketplace_data)) {
            $result['error'] = ($lang['text_import_ebay_no_listing'] ?? 'No valid eBay Item ID found for this product.');
            return $result;
        }

        $first_account = reset($marketplace_data);
        $marketplace_item_id = trim((string)($first_account['marketplace_item_id'] ?? ''));
        $result['marketplace_item_id'] = $marketplace_item_id;

        if ($marketplace_item_id === '' || $marketplace_item_id === '0') {
            $result['error'] = ($lang['text_import_ebay_no_listing'] ?? 'No valid eBay Item ID found for this product.');
            return $result;
        }

        $product = $this->model_shopmanager_catalog_product->getProduct($product_id);
        $db_names = [];

        if (!empty($product['image'])) {
            $db_names[] = $this->normalizeImageName($product['image']);
            $result['db_main_count'] = 1;
        }

        $secondary_images = $this->model_shopmanager_catalog_product->getImages($product_id);
        if (!empty($secondary_images) && is_array($secondary_images)) {
            foreach ($secondary_images as $img) {
                if (!empty($img['image'])) {
                    $db_names[] = $this->normalizeImageName($img['image']);
                    $result['db_secondary_count']++;
                }
            }
        }

        $db_names = array_values(array_unique(array_filter($db_names)));
        $result['db_image_names'] = $db_names;
        $result['db_count'] = count($db_names);

        $ebay_urls = $this->model_shopmanager_ebay->getImages($marketplace_item_id);
        $ebay_names = [];

        if (!empty($ebay_urls) && is_array($ebay_urls)) {
            foreach ($ebay_urls as $url) {
                $path = parse_url((string)$url, PHP_URL_PATH);
                $name = $this->normalizeImageName((string)$path);
                if ($name !== '') {
                    $ebay_names[] = $name;
                }
            }
        }

        $ebay_names = array_values(array_unique(array_filter($ebay_names)));
        $result['ebay_image_names'] = $ebay_names;
        $result['ebay_count'] = count($ebay_names);

        $result['missing_in_db'] = array_values(array_diff($ebay_names, $db_names));
        $result['missing_on_ebay'] = array_values(array_diff($db_names, $ebay_names));
        $result['notify'] = ($result['ebay_count'] > $result['db_count']);
        $result['success'] = true;

        return $result;
    }

    private function normalizeImageName(string $path): string {
        $name = basename(rawurldecode($path));
        $name = trim($name);

        return $name !== '' ? strtolower($name) : '';
    }
    
    /**
     * Delete remaining files in old directories
     */
    public function deleteRemainingFiles(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $data = $data ?? [];
        $data += $lang;
        
        $json = [];
        
        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            if (isset($this->request->post['directories']) && is_array($this->request->post['directories'])) {
                $deleted_count = 0;
                $errors = [];
                
                foreach ($this->request->post['directories'] as $dir_data) {
                    if (!isset($dir_data['directory']) || !isset($dir_data['files'])) {
                        continue;
                    }
                    
                    $directory = DIR_IMAGE . $dir_data['directory'];
                    
                    // Security check: ensure directory is within DIR_IMAGE
                    if (strpos(realpath($directory), realpath(DIR_IMAGE)) !== 0) {
                        $errors[] = "Invalid directory path";
                        continue;
                    }
                    
                    // Delete each file
                    foreach ($dir_data['files'] as $file) {
                        $file_path = $directory . '/' . $file;
                        if (file_exists($file_path) && is_file($file_path)) {
                            if (@unlink($file_path)) {
                                $deleted_count++;
                            } else {
                                $errors[] = "Failed to delete: " . $file;
                            }
                        }
                    }
                    
                    // Try to remove directory if now empty
                    $remaining = array_diff(scandir($directory), ['.', '..']);
                    if (empty($remaining)) {
                        @rmdir($directory);
                    }
                }
                
                if ($deleted_count > 0) {
                    $message = "Successfully deleted $deleted_count remaining file(s)";
                    if (!empty($errors)) {
                        $message .= ". Errors: " . implode(', ', $errors);
                    }
                    $json['success'] = $message;
                } else {
                    $json['error'] = 'No files were deleted. ' . (!empty($errors) ? 'Errors: ' . implode(', ', $errors) : '');
                }
            } else {
                $json['error'] = 'No directories specified.';
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Convert image to WebP format
     * Handles files with any extension or no extension by detecting actual image type from content
     */
    private function convertToWebp(string $source, string $destination): bool {
        // Verify source file exists and is readable
        if (!file_exists($source) || !is_readable($source)) {
            return false;
        }
        
        // Get image info - this reads the file content, not the extension
        $info = @getimagesize($source);
        if (!$info) {
            // Try to detect mime type as fallback
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $source);
            finfo_close($finfo);
            
            // If it's not an image mime type, fail
            if (!$mime || strpos($mime, 'image/') !== 0) {
                return false;
            }
            
            // For some edge cases, getimagesize might fail but mime detection works
            // Try to force detection by common image types
            if ($mime === 'image/jpeg' || $mime === 'image/jpg') {
                $info = [2 => IMAGETYPE_JPEG];
            } elseif ($mime === 'image/png') {
                $info = [2 => IMAGETYPE_PNG];
            } elseif ($mime === 'image/gif') {
                $info = [2 => IMAGETYPE_GIF];
            } elseif ($mime === 'image/webp') {
                $info = [2 => IMAGETYPE_WEBP];
            } else {
                return false;
            }
        }
        
        $image = null;
        switch ($info[2]) {
            case IMAGETYPE_JPEG:
                $image = @imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $image = @imagecreatefrompng($source);
                break;
            case IMAGETYPE_GIF:
                $image = @imagecreatefromgif($source);
                break;
            case IMAGETYPE_WEBP:
                // Already webp, just copy
                return copy($source, $destination);
            default:
                return false;
        }
        
        if (!$image) {
            return false;
        }
        
        // Convert to webp with quality 85
        $result = @imagewebp($image, $destination, 85);
        imagedestroy($image);
        
        return $result;
    }
    
    /**
     * Add orphan images to product
     */
    public function addOrphanImages(): void {
        $lang = $this->load->language('shopmanager/maintenance/image');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/maintenance/image');
        
        $json = [];
        
        if (!$this->user->hasPermission('modify', 'shopmanager/maintenance/image')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            if (isset($this->request->post['product_id']) && isset($this->request->post['images']) && is_array($this->request->post['images'])) {
                $product_id = (int)$this->request->post['product_id'];
                $images = $this->request->post['images'];
                $added_count = 0;
                
                foreach ($images as $image_data) {
                    if (!isset($image_data['image']) || !isset($image_data['type'])) {
                        continue;
                    }
                    
                    $image_path = $image_data['image'];
                    $type = $image_data['type']; // 'main' or 'secondary'
                    
                    // Verify the image file exists
                    if (!file_exists(DIR_IMAGE . $image_path)) {
                        continue;
                    }
                    
                    if ($type === 'main') {
                        $this->model_shopmanager_maintenance_image->updateMainImage($product_id, $image_path);
                        $added_count++;
                    } elseif ($type === 'secondary') {
                        $this->model_shopmanager_maintenance_image->addSecondaryImage($product_id, $image_path);
                        $added_count++;
                    }
                }
                
                if ($added_count > 0) {
                    $json['success'] = 'Successfully added ' . $added_count . ' image(s) to product.';
                } else {
                    $json['error'] = 'No images were added.';
                }
            } else {
                $json['error'] = 'Invalid request data.';
            }
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Find orphan images in product directories
     */
    private function findOrphanImages($product_id, $model): array {
        $orphans = [];
        
        // Get all images linked to this product
        $this->load->model('shopmanager/maintenance/image');
        $linked_images = [];
        $expected_dir = null;
        
        // Get main image
        $product = $this->model_shopmanager_maintenance_image->getProduct($product_id);
        if (!empty($product['image'])) {
            $linked_images[] = basename($product['image']);
            // Extract directory from main image path
            $expected_dir = DIR_IMAGE . dirname($product['image']) . '/';
        }
        
        // Get secondary images
        $secondary_images = $this->model_shopmanager_maintenance_image->getSecondaryImages($product_id);
        foreach ($secondary_images as $img) {
            if (!empty($img['image'])) {
                $linked_images[] = basename($img['image']);
                // Use directory from first secondary image if main not found
                if (!$expected_dir) {
                    $expected_dir = DIR_IMAGE . dirname($img['image']) . '/';
                }
            }
        }
        
        // If no images found in DB, try standard locations
        if (!$expected_dir) {
            $expected_subfolder = substr($product_id, 0, 2);
            $possible_dirs = [
                DIR_IMAGE . 'catalog/product/' . $product_id . '/',
                DIR_IMAGE . 'catalog/product/' . $expected_subfolder . '/' . $product_id . '/'
            ];
            
            foreach ($possible_dirs as $dir) {
                if (is_dir($dir)) {
                    $expected_dir = $dir;
                    break;
                }
            }
        }
        
        if (!$expected_dir || !is_dir($expected_dir)) {
            return $orphans;
        }
        
        // Scan directory
        $files = scandir($expected_dir);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $full_path = $expected_dir . $file;
            
            // Only process image files
            if (is_file($full_path) && preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $file)) {
                // Check if not linked
                if (!in_array($file, $linked_images)) {
                    // Build relative path based on actual directory structure
                    $rel_path = str_replace(DIR_IMAGE, '', $expected_dir) . $file;
                    
                    $orphan_info = @getimagesize($full_path);
                    $resolution = '';
                    if ($orphan_info) {
                        $resolution = $orphan_info[0] . 'x' . $orphan_info[1];
                    }
                    
                    $orphans[] = [
                        'image' => $rel_path,
                        'resolution' => $resolution
                    ];
                }
            }
        }
        
        return $orphans;
    }
}
