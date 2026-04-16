<?php
// Original: shopmanager/inventory/location.php
namespace Opencart\Admin\Controller\Shopmanager\Inventory;

class Location extends \Opencart\System\Engine\Controller {
    private array $error = [];

    public function index(): void {
        $this->load->language('shopmanager/inventory/location');
        $data = [];
        
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        $this->document->addScript('view/javascript/shopmanager/inventory/location.js?v=' . time());

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/inventory/location', 'user_token=' . $this->session->data['user_token'])
        ];

        // Language
        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_list'] = ($lang['text_list'] ?? '');
        $data['text_filter'] = ($lang['text_filter'] ?? '');
        $data['text_no_results'] = ($lang['text_no_results'] ?? '');
        $data['button_filter'] = ($lang['button_filter'] ?? '');
        $data['button_reset'] = ($lang['button_reset'] ?? '');
        $data['entry_location'] = ($lang['entry_location'] ?? '');
        $data['entry_sku'] = ($lang['entry_sku'] ?? '');

        // Filter values
        $data['filter_location'] = $this->request->get['filter_location'] ?? '';

        // Action URLs
        $data['action_update'] = $this->url->link('shopmanager/inventory/location.updateLocation', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->getList();

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/inventory/location', $data));
    }

    public function list(): void {
        $this->load->language('shopmanager/inventory/location');
        $data = [];
        

        $this->response->setOutput($this->getList());
    }

    public function searchProduct(): void {
        $this->load->language('shopmanager/inventory/location');
        $data = [];
        
        $this->load->model('shopmanager/inventory');
        $this->load->model('tool/image');

        $json = [];

        if (isset($this->request->get['sku'])) {
            $sku = $this->request->get['sku'];

            $filter_data = [
                'filter_sku_exact' => $sku,
                'start' => 0,
                'limit' => 1
            ];

            $results = $this->model_shopmanager_inventory->getProducts($filter_data);

            if ($results) {
                $result = $results[0];

                if (!empty($result['image']) && is_file(DIR_IMAGE . $result['image'])) {
                    $image = $this->model_tool_image->resize($result['image'], 75, 75);
                } else {
                    $image = $this->model_tool_image->resize('no_image.png', 75, 75);
                }

                $json['success'] = true;
                $json['product'] = [
                    'product_id' => $result['product_id'],
                    'sku' => $result['sku'],
                    'image' => $image,
                    'name' => $result['name'],
                    'quantity' => $result['quantity'],
                    'unallocated_quantity' => $result['unallocated_quantity'] ?? 0,
                    'location' => $result['location'] ?? '',
                    'status_id' => $result['status'],
                    'status' => $result['status'] ? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? '')
                ];
            } else {
                $json['error'] = 'Product not found';
            }
        } else {
            $json['error'] = 'SKU not provided';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function getList(): string {
        $this->load->model('shopmanager/inventory');
        $this->load->model('tool/image');

        // Filters
        $filter_location = $this->request->get['filter_location'] ?? null;
        $sort = $this->request->get['sort'] ?? 'pd.name';
        $order = $this->request->get['order'] ?? 'ASC';

        // Build URL for sorting
        $url = '';
        if (!empty($this->request->get['filter_location'])) {
            $url .= '&filter_location=' . urlencode($this->request->get['filter_location']);
        }
        if (!empty($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (!empty($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $data['products'] = [];

        // Only query if location is provided
        if (!empty($filter_location)) {
            $filter_data = [
                'filter_location' => $filter_location,
                'filter_quantity_greater_than_zero' => true, // Only products with quantity > 0
                'sort' => $sort,
                'order' => $order,
                'start' => 0,
                'limit' => 999999
            ];

            $results = $this->model_shopmanager_inventory->getProducts($filter_data);

            foreach ($results as $result) {
                if (!empty($result['image']) && is_file(DIR_IMAGE . $result['image'])) {
                    $image = $this->model_tool_image->resize($result['image'], 75, 75);
                } else {
                    $image = $this->model_tool_image->resize('no_image.png', 75, 75);
                }

                $data['products'][] = [
                    'product_id' => $result['product_id'],
                    'sku' => $result['sku'],
                    'image' => $image,
                    'name' => $result['name'],
                    'quantity' => $result['quantity'],
                    'unallocated_quantity' => $result['unallocated_quantity'] ?? 0,
                    'location' => $result['location'] ?? '',
                    'made_in_country_id' => $result['made_in_country_id'] ?? 0,
                    'status_id' => $result['status'],
                    'status' => $result['status'] ? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? ''),
                    'date_modified' => $result['date_modified'] ?? null,
                    'date_added' => $result['date_added'] ?? null
                ];
            }
        }

        // Load countries
        $this->load->model('shopmanager/localisation/country');
        $countries = $this->model_shopmanager_localisation_country->getCountries(['sort'=>'name']);
        
        $countries_used = [];

        // Pays les plus utilisés en premier (same as product list)
        $priority_country_names = ['Canada', 'United States', 'China', 'Mexico'];

        foreach ($priority_country_names as $priority_country_name) {
            foreach ($countries as $key => $country) {
                if (($country['name'] ?? '') === $priority_country_name) {
                    $countries_used[] = $country;
                    unset($countries[$key]);
                    break;
                }
            }
        }

        // Séparateur
        $countries_used[] = [
            'country_id' => 0,
            'name'       => '-----------------------------------'
        ];

        // Ajouter le reste
        foreach ($countries as $item) {
            $countries_used[] = $item;
        }

        $data['countries'] = $countries_used;

        // Language
        $data['column_sku'] = ($lang['column_sku'] ?? '');
        $data['column_image'] = ($lang['column_image'] ?? '');
        $data['column_name'] = ($lang['column_name'] ?? '');
        $data['column_quantity'] = ($lang['column_quantity'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_made_in_country'] = ($lang['column_made_in_country'] ?? '');
        $data['column_action'] = ($lang['column_action'] ?? '');
        $data['entry_sku'] = ($lang['entry_sku'] ?? '');
        $data['entry_new_location'] = ($lang['entry_new_location'] ?? '');
        $data['entry_made_in_country'] = ($lang['entry_made_in_country'] ?? '');
        $data['button_submit'] = ($lang['button_submit'] ?? '');
        $data['button_update_quantity'] = ($lang['button_update_quantity'] ?? '');
        $data['button_apply'] = ($lang['button_apply'] ?? '');
        $data['text_no_results'] = ($lang['text_no_results'] ?? '');
        $data['text_country_required'] = ($lang['text_country_required'] ?? '');
        $data['text_select_country_message']       = ($lang['text_select_country_message'] ?? '');
        $data['text_select_country']               = ($lang['text_select_country'] ?? '');
        $data['text_error_save_country']           = ($lang['text_error_save_country'] ?? '');
        $data['text_error_save_country_generic']   = ($lang['text_error_save_country_generic'] ?? '');
        $data['user_token'] = $this->session->data['user_token'];

        // Sort URLs
        $url_order = ($order == 'ASC') ? 'DESC' : 'ASC';
        
        $data['sort_sku'] = $this->url->link('shopmanager/inventory/location.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sku&order=' . $url_order . $url);
        $data['sort_name'] = $this->url->link('shopmanager/inventory/location.list', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name&order=' . $url_order . $url);
        $data['sort_quantity'] = $this->url->link('shopmanager/inventory/location.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity&order=' . $url_order . $url);
        $data['sort_location'] = $this->url->link('shopmanager/inventory/location.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.location&order=' . $url_order . $url);

        $data['sort'] = $sort;
        $data['order'] = $order;
        $data['results'] = sprintf('Showing %s results', count($data['products']));
        $data['filter_location'] = $filter_location;
        $data['action_update'] = $this->url->link('shopmanager/inventory/location.updateLocation', 'user_token=' . $this->session->data['user_token']);

        return $this->load->view('shopmanager/inventory_list', $data);
    }

    public function updateQuantity(): void {
        $this->load->language('shopmanager/inventory/location');
        $data = [];
        
        $this->load->model('shopmanager/inventory');

        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/inventory/location')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        }

        if (!$json) {
            $product_id = (int)($this->request->post['product_id'] ?? 0);
            $new_quantity = (int)($this->request->post['quantity'] ?? 0);

            if ($product_id && $new_quantity >= 0) {
                $this->model_shopmanager_inventory->updateQuantity($product_id, $new_quantity);
                $json['success'] = 'Quantity updated successfully';
            } else {
                $json['error'] = 'Invalid data';
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function updateLocation(): void {
        $this->load->language('shopmanager/inventory/location');
        $data = [];
        
        $this->load->model('shopmanager/inventory');
        $this->load->model('shopmanager/catalog/product');

        $json = [];

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $product_ids = $this->request->post['product_id'] ?? [];
            $scanned_quantities = $this->request->post['scanned_quantity'] ?? [];
            $new_location = strtoupper($this->request->post['new_location'] ?? '');

            if (!empty($product_ids) && !empty($new_location)) {
                foreach ($product_ids as $product_id) {
                    // Get current product info
                    $product_info = $this->model_shopmanager_catalog_product->getProduct($product_id);
                    
                    if ($product_info) {
                        $old_location = $product_info['location'] ?? '';
                        $current_quantity = $product_info['quantity'] ?? 0;
                        $current_unallocated = $product_info['unallocated_quantity'] ?? 0;
                        
                        // Use scanned quantity if provided and > 0, otherwise keep current
                        $new_quantity = (isset($scanned_quantities[$product_id]) && $scanned_quantities[$product_id] > 0) 
                            ? $scanned_quantities[$product_id] 
                            : $current_quantity;
                        
                        // Keep unallocated_quantity the same
                        $this->model_shopmanager_inventory->updateProductLocation(
                            $product_id, 
                            $new_location, 
                            $old_location, 
                            $new_quantity, 
                            $current_unallocated
                        );
                    }
                }
                $json['success'] = sprintf('Successfully updated %d product(s) to location: %s', count($product_ids), $new_location);
            } else {
                $json['error'] = 'Missing product IDs or location';
            }
        } else {
            $json['error'] = 'Invalid request method';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
