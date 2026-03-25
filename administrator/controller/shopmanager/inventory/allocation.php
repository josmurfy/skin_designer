<?php
namespace Opencart\Admin\Controller\Shopmanager\Inventory;

class Allocation extends \Opencart\System\Engine\Controller {
    private array $error = [];

    public function index(): void {
        $lang = $this->load->language('shopmanager/inventory/allocation');
        $data = $data ?? [];
        $data += $lang;
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
        $this->document->addScript('view/javascript/shopmanager/inventory/allocation.js');
        $this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
        $this->document->addScript('view/javascript/shopmanager/alert_popup.js');

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/inventory/allocation', 'user_token=' . $this->session->data['user_token'])
        ];

        // Language
        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_list'] = ($lang['text_list'] ?? '');
        $data['text_filter'] = ($lang['text_filter'] ?? '');
        $data['text_no_results'] = ($lang['text_no_results'] ?? '');
        $data['text_confirm'] = ($lang['text_confirm'] ?? '');
        $data['button_filter'] = ($lang['button_filter'] ?? '');
        $data['button_reset'] = ($lang['button_reset'] ?? '');
        $data['entry_sku'] = ($lang['entry_sku'] ?? '');
        $data['entry_select_all'] = ($lang['entry_select_all'] ?? '');
        $data['entry_category_id'] = ($lang['entry_category_id'] ?? '');
        $data['entry_quantity'] = ($lang['entry_quantity'] ?? '');
        $data['entry_unallocated_quantity'] = ($lang['entry_unallocated_quantity'] ?? '');
        $data['entry_location'] = ($lang['entry_location'] ?? '');
        $data['entry_new_location'] = ($lang['entry_new_location'] ?? '');
        $data['entry_status'] = ($lang['entry_status'] ?? '');
        $data['button_submit'] = ($lang['button_submit'] ?? '');
        $data['text_enabled'] = ($lang['text_enabled'] ?? '');
        $data['text_disabled'] = ($lang['text_disabled'] ?? '');

        // Filter values
        $data['filter_sku'] = $this->request->get['filter_sku'] ?? '';
        $data['filter_category_id'] = $this->request->get['filter_category_id'] ?? '';
        $data['filter_quantity'] = $this->request->get['filter_quantity'] ?? '';
        $data['filter_unallocated_quantity'] = $this->request->get['filter_unallocated_quantity'] ?? '';
        $data['filter_location'] = $this->request->get['filter_location'] ?? '';
        $data['filter_status'] = $this->request->get['filter_status'] ?? '';

        // Action URLs
        $data['action'] = $this->url->link('shopmanager/inventory/allocation.transfert', 'user_token=' . $this->session->data['user_token']);
        $data['list'] = $this->getList();

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/inventory/allocation', $data));
    }

    public function list(): void {
        $lang = $this->load->language('shopmanager/inventory/allocation');
        $data = $data ?? [];
        $data += $lang;

        $this->response->setOutput($this->getList());
    }

    protected function getList(): string {
        $this->load->model('shopmanager/inventory');
        $this->load->model('tool/image');
        $this->load->model('shopmanager/localisation/country');

        // Filters using null coalescing operator
        $filter_sku = $this->request->get['filter_sku'] ?? null;
        $filter_category_id = $this->request->get['filter_category_id'] ?? null;
        $filter_quantity = $this->request->get['filter_quantity'] ?? null;
        $filter_unallocated_quantity = $this->request->get['filter_unallocated_quantity'] ?? null;
        $filter_location = $this->request->get['filter_location'] ?? null;
        $filter_status = $this->request->get['filter_status'] ?? null;
        $filter_image = $this->request->get['filter_image'] ?? null;
        $sort = $this->request->get['sort'] ?? 'pd.name';
        $order = $this->request->get['order'] ?? 'ASC';
        $page = 1; // Pas de pagination
        $limit = 999999; // Afficher tout

		// Build URL for sorting and pagination
		$url = '';

		if (!empty($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (!empty($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (!empty($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

        if (!empty($this->request->get['filter_unallocated_quantity'])) {
			$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
		}

        if (!empty($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . urlencode($this->request->get['filter_location']);
		}

		if (isset($this->request->get['filter_status']) && $this->request->get['filter_status'] !== '') {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (!empty($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (!empty($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (!empty($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['products'] = [];

		$filter_data = [
			'filter_sku' => $filter_sku,
			'filter_category_id' => $filter_category_id,
			'filter_quantity' => $filter_quantity,
            'filter_unallocated_quantity' => $filter_unallocated_quantity,
            'filter_location' => $filter_location,
			'filter_status' => $filter_status,
			'filter_image' => $filter_image,
			'filter_unallocated_only' => true, // Force unallocated filter
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		];

        $product_total = $this->model_shopmanager_inventory->getTotalProducts($filter_data);
		$results = $this->model_shopmanager_inventory->getProducts($filter_data);

        $data['product_total'] = $product_total;

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
                'unallocated_quantity' => $result['unallocated_quantity'],
                'total_quantity' => $result['unallocated_quantity'] + $result['quantity'],
                'location' => $result['location'] ?? '',
				'made_in_country_id' => $result['made_in_country_id'] ?? 0,
				'status_id' => $result['status'],
				'status' => $result['status'] ? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? ''),
				'date_modified' => $result['date_modified'] ?? null,
				'date_added' => $result['date_added'] ?? null
			];
		}

        // Language
        $data['column_sku'] = ($lang['column_sku'] ?? '');
        $data['column_image'] = ($lang['column_image'] ?? '');
        $data['column_name'] = ($lang['column_name'] ?? '');
        $data['column_total_quantity'] = ($lang['column_total_quantity'] ?? '');
        $data['column_quantity'] = ($lang['column_quantity'] ?? '');
        $data['column_unallocated_quantity'] = ($lang['column_unallocated_quantity'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
        $data['column_made_in_country'] = ($lang['column_made_in_country'] ?? '');
        $data['column_updated'] = ($lang['column_updated'] ?? '');
        $data['text_no_results'] = ($lang['text_no_results'] ?? '');
        $data['text_products_to_process'] = ($lang['text_products_to_process'] ?? '');
        $data['button_submit'] = ($lang['button_submit'] ?? '');
        $data['button_apply'] = ($lang['button_apply'] ?? '');
        $data['entry_sku'] = ($lang['entry_sku'] ?? '');
        $data['entry_new_location'] = ($lang['entry_new_location'] ?? '');
        $data['entry_made_in_country'] = ($lang['entry_made_in_country'] ?? '');
        $data['entry_select_all'] = ($lang['entry_select_all'] ?? '');
        $data['text_select'] = ($lang['text_select'] ?? '');
        $data['text_country_required'] = ($lang['text_country_required'] ?? '');
        $data['text_select_country_message'] = ($lang['text_select_country_message'] ?? '');
        $data['text_country_modal_title'] = ($lang['text_country_modal_title'] ?? '');
        $data['text_country_modal_message'] = ($lang['text_country_modal_message'] ?? '');
        $data['text_ai_analyzing'] = ($lang['text_ai_analyzing'] ?? '');
        $data['text_ai_suggestion'] = ($lang['text_ai_suggestion'] ?? '');
        $data['text_auto_accept_ai'] = ($lang['text_auto_accept_ai'] ?? '');
        $data['text_please_select_country'] = ($lang['text_please_select_country'] ?? '');
        $data['text_sku_not_found'] = ($lang['text_sku_not_found'] ?? '');
        $data['text_select_at_least_one'] = ($lang['text_select_at_least_one'] ?? '');
        $data['text_cannot_add_product'] = ($lang['text_cannot_add_product'] ?? '');
        $data['text_already_scanned'] = ($lang['text_already_scanned'] ?? '');
        $data['text_error_occurred'] = ($lang['text_error_occurred'] ?? '');
        $data['text_ajax_error'] = ($lang['text_ajax_error'] ?? '');
        $data['text_just_now'] = ($lang['text_just_now'] ?? '');
        $data['text_minutes_ago'] = ($lang['text_minutes_ago'] ?? '');
        $data['text_hours_ago'] = ($lang['text_hours_ago'] ?? '');
        $data['text_days_ago'] = ($lang['text_days_ago'] ?? '');
        $data['text_weeks_ago'] = ($lang['text_weeks_ago'] ?? '');
        $data['text_months_ago'] = ($lang['text_months_ago'] ?? '');
        $data['text_years_ago'] = ($lang['text_years_ago'] ?? '');
        $data['text_unsaved_changes'] = ($lang['text_unsaved_changes'] ?? '');

        // Load countries
        $countries = $this->model_shopmanager_localisation_country->getCountries(['sort'=>'name']);
        // Prioritize specific countries at the top: Canada, United States, China, Mexico
        $countries_used = [];
        
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
        
        // Add separator with country_id = 0
        $countries_used[] = [
            'country_id' => 0,
            'name' => '-----------------------------------'
        ];
        
        // Add remaining countries
        foreach ($countries as $item) {
            $countries_used[] = $item;
        }
        
        $data['countries'] = $countries_used;

        $data['countries'] = $countries_used;

        // Action
        $data['action'] = $this->url->link('shopmanager/inventory/allocation.transfert', 'user_token=' . $this->session->data['user_token'] . $url);
        $data['user_token'] = $this->session->data['user_token'];

		// Sort URLs
		$url_sort = str_replace('&order=' . $order, '', $url);
		$url_order = ($order == 'ASC') ? 'DESC' : 'ASC';

		$data['sort_sku'] = $this->url->link('shopmanager/inventory/allocation.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sku&order=' . $url_order . $url_sort);
		$data['sort_name'] = $this->url->link('shopmanager/inventory/allocation.list', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name&order=' . $url_order . $url_sort);
		$data['sort_quantity'] = $this->url->link('shopmanager/inventory/allocation.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity&order=' . $url_order . $url_sort);
        $data['sort_unallocated_quantity'] = $this->url->link('shopmanager/inventory/allocation.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.unallocated_quantity&order=' . $url_order . $url_sort);
		$data['sort_location'] = $this->url->link('shopmanager/inventory/allocation.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.location&order=' . $url_order . $url_sort);

		// Pas de pagination - afficher tout
        $data['pagination'] = '';
        $data['results'] = sprintf('Showing %s results', $product_total);

		$data['sort'] = $sort;
		$data['order'] = $order;

        return $this->load->view('shopmanager/inventory/allocation_list', $data);
    }

    public function transfert() {
        $lang = $this->load->language('shopmanager/inventory/allocation');
        $data = $data ?? [];
        $data += $lang;

		$this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('shopmanager/inventory');

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['product_id'])) {
            foreach ($this->request->post['product_id'] as $product_id) {
                $new_location = strtoupper($this->request->post['new_location']);
                $old_location = strtoupper($this->request->post['old_location'][$product_id] ?? '');
                $quantity = $this->request->post['quantity'][$product_id];
                $unallocated_quantity = $this->request->post['unallocated_quantity'][$product_id];

                $this->model_shopmanager_inventory->updateProductLocation($product_id, $new_location, $old_location, $quantity, $unallocated_quantity);
            }
        }
        $this->response->redirect($this->url->link('shopmanager/inventory/allocation', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function getTrimmedList() {
        $this->load->model('shopmanager/inventory');
        
        $products = $this->model_shopmanager_inventory->getTrimmedProducts();

        foreach ($products as $product) {
            $new_loc = $product['quantity'] > 0 ? $product['location'] : '';
        }
        $this->response->redirect($this->url->link('shopmanager/inventory/allocation', 'user_token=' . $this->session->data['user_token'], true));
    }
}
