<?php
// Original: warehouse/maintenance/description.php
namespace Opencart\Admin\Controller\Warehouse\Maintenance;

class Description extends \Opencart\System\Engine\Controller {
	
	public function index(): void {
		$this->load->language('warehouse/maintenance/description');
		$data = [];
		

		$this->document->setTitle(($lang['heading_title'] ?? ''));
		
		// Add warehouse CSS for image popups
		$this->document->addStyle('view/stylesheet/warehouse.css');

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('warehouse/maintenance/description', 'user_token=' . $this->session->data['user_token'])
		];

		$data['list'] = $this->getList();

		// Filter variables
		$data['filter_product_id'] = $this->request->get['filter_product_id'] ?? '';
		$data['filter_name'] = $this->request->get['filter_name'] ?? '';
		$data['filter_missing_type'] = $this->request->get['filter_missing_type'] ?? '';
		$data['filter_outdated'] = $this->request->get['filter_outdated'] ?? '';
		$data['filter_quantity_from'] = $this->request->get['filter_quantity_from'] ?? '';
		$data['filter_quantity_to'] = $this->request->get['filter_quantity_to'] ?? '';
		$data['text_filter'] = 'Filters';
		$data['button_filter'] = 'Filter';

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('warehouse/maintenance/description', $data));
	}

	public function list(): void {
		$this->load->language('warehouse/maintenance/description');
		$data = [];
		

		$this->response->setOutput($this->getList());
	}

	protected function getList(): string {
		$this->load->model('warehouse/maintenance/description');
		$this->load->model('tool/image');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else {
			$sort = 'pd.name';
		}

		if (isset($this->request->get['order'])) {
			$order = (string)$this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		$limit = 20;

		$data['products'] = [];

		$filter_data = [
			'sort' => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit
		];

		// Apply filters
		if (isset($this->request->get['filter_product_id']) && $this->request->get['filter_product_id'] !== '') {
			$filter_data['filter_product_id'] = (int)$this->request->get['filter_product_id'];
		}

		if (isset($this->request->get['filter_name']) && $this->request->get['filter_name'] !== '') {
			$filter_data['filter_name'] = $this->request->get['filter_name'];
		}

		if (isset($this->request->get['filter_missing_type']) && $this->request->get['filter_missing_type'] !== '*' && $this->request->get['filter_missing_type'] !== '') {
			$filter_data['filter_missing_type'] = $this->request->get['filter_missing_type'];
		}

		if (isset($this->request->get['filter_outdated']) && $this->request->get['filter_outdated'] !== '*' && $this->request->get['filter_outdated'] !== '') {
			$filter_data['filter_outdated'] = $this->request->get['filter_outdated'];
		}

		if (isset($this->request->get['filter_quantity_from']) && $this->request->get['filter_quantity_from'] !== '') {
			$filter_data['filter_quantity_from'] = (int)$this->request->get['filter_quantity_from'];
		}

		if (isset($this->request->get['filter_quantity_to']) && $this->request->get['filter_quantity_to'] !== '') {
			$filter_data['filter_quantity_to'] = (int)$this->request->get['filter_quantity_to'];
		}

		$product_total = $this->model_warehouse_maintenance_description->getTotalOutdatedProducts($filter_data);
		$results = $this->model_warehouse_maintenance_description->getOutdatedProducts($filter_data);

		foreach ($results as $result) {
			// Préparer l'image
			if ($result['image'] && is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))) {
				$image = $this->model_tool_image->resize(html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'), 40, 40);
				$fullsize_image = $result['image'];
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 40, 40);
				$fullsize_image = 'placeholder.png';
			}

			$data['products'][] = [
				'product_id' => $result['product_id'],
				'name' => $result['name'],
				'image' => $image,
				'fullsize_image' => $fullsize_image,
				'quantity' => $result['quantity'],
				'date_modified' => !empty($result['date_modified']) ? date(($lang['date_format_short'] ?? ''), strtotime($result['date_modified'])) : 'N/A',
				'has_included_accessories' => !empty($result['included_accessories']),
				'has_condition_supp' => !empty($result['condition_supp']),
				'has_description_supp' => !empty($result['description_supp']),
				'missing_fields' => $this->getMissingFieldsCount($result),
				'edit' => $this->url->link('warehouse/product/product.form', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'])
			];
		}

		$data['user_token'] = $this->session->data['user_token'];

		$url = '';

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_missing_type'])) {
			$url .= '&filter_missing_type=' . $this->request->get['filter_missing_type'];
		}

		if (isset($this->request->get['filter_outdated'])) {
			$url .= '&filter_outdated=' . $this->request->get['filter_outdated'];
		}

		if (isset($this->request->get['filter_quantity_from'])) {
			$url .= '&filter_quantity_from=' . $this->request->get['filter_quantity_from'];
		}

		if (isset($this->request->get['filter_quantity_to'])) {
			$url .= '&filter_quantity_to=' . $this->request->get['filter_quantity_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('warehouse/maintenance/description.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf(($lang['text_pagination'] ?? ''), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

		// Sort URLs
		$url = '';

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_missing_type'])) {
			$url .= '&filter_missing_type=' . $this->request->get['filter_missing_type'];
		}

		if (isset($this->request->get['filter_outdated'])) {
			$url .= '&filter_outdated=' . $this->request->get['filter_outdated'];
		}

		if (isset($this->request->get['filter_quantity_from'])) {
			$url .= '&filter_quantity_from=' . $this->request->get['filter_quantity_from'];
		}

		if (isset($this->request->get['filter_quantity_to'])) {
			$url .= '&filter_quantity_to=' . $this->request->get['filter_quantity_to'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_id'] = $this->url->link('warehouse/maintenance/description.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.product_id' . $url);
		$data['sort_name'] = $this->url->link('warehouse/maintenance/description.list', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url);
		$data['sort_quantity'] = $this->url->link('warehouse/maintenance/description.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url);
		$data['sort_modified'] = $this->url->link('warehouse/maintenance/description.list', 'user_token=' . $this->session->data['user_token'] . '&sort=p.date_modified' . $url);
		$data['sort_missing'] = $this->url->link('warehouse/maintenance/description.list', 'user_token=' . $this->session->data['user_token'] . '&sort=missing_fields' . $url);

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('warehouse/maintenance/description_list', $data);
	}

	private function getMissingFieldsCount(array $product): int {
		$count = 0;
		
		if (empty($product['included_accessories'])) {
			$count++;
		}
		
		if (empty($product['condition_supp'])) {
			$count++;
		}
		
		if (empty($product['description_supp'])) {
			$count++;
		}
		
		return $count;
	}

	public function aiSuggest(): void {
		$this->load->language('warehouse/maintenance/description');
		$data = [];
		

		$json = [];

		if (!isset($this->request->get['product_id'])) {
			$json['error'] = ($lang['error_product_id'] ?? '');
		}

		if (!$json) {
			$product_id = (int)$this->request->get['product_id'];
			
			// Appeler la fonction AI existante (à adapter selon votre implémentation)
			// Cette route devrait rediriger vers le même système que product_form
			$json['success'] = ($lang['text_ai_triggered'] ?? '');
			$json['redirect'] = $this->url->link('warehouse/product/product.form', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product_id);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
