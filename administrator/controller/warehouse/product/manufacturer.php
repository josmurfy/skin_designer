<?php
// Original: shopmanager/manufacturer.php
namespace Opencart\Admin\Controller\Shopmanager;

class Manufacturer extends \Opencart\System\Engine\Controller {
    private $error = array();

	public function index() {
		$this->load->language('shopmanager/manufacturer');
		$data = [];
		

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		$this->load->model('shopmanager/manufacturer');

		$this->getList();
	}

	public function add() {
		
	
		$json = [];

		if (!$this->user->hasPermission('modify', 'shopmanager/manufacturer')) {
			$json['error'] = 'Permission refusée!';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->load->language('shopmanager/manufacturer');
		$data = [];
		
		$this->document->setTitle(($lang['heading_title'] ?? ''));
		$this->load->model('shopmanager/manufacturer');
	
		// Détecter si c'est une requête AJAX
		$isAjax = isset($this->request->get['ajax']) && $this->request->get['ajax'] == 'true';
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$manufacturer_id = $this->model_shopmanager_manufacturer->addManufacturer($this->request->post);
			$this->session->data['success'] = ($lang['text_success'] ?? '');
	
			$json['success'] = ($lang['text_success'] ?? '');
			$json['manufacturer_id'] = $manufacturer_id;
	
			if ($isAjax) {
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
				return;
			}
	
			$url = '';
	
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
	
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
	
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
	
			$this->response->redirect($this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
	
		if ($isAjax) {
			$json['error'] = ($lang['error_form'] ?? '');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
	
		$this->getForm();
	}
	
	public function edit() {

		$json = [];

		if (!$this->user->hasPermission('modify', 'shopmanager/manufacturer')) {
			$json['error'] = 'Permission refusée!';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->load->language('shopmanager/manufacturer');
		$data = [];
		
		$this->document->setTitle(($lang['heading_title'] ?? ''));
		$this->load->model('shopmanager/manufacturer');

	
		// Détecter si c'est une requête AJAX
		$isAjax = isset($this->request->get['ajax']) && $this->request->get['ajax'] == 'true';
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_shopmanager_manufacturer->editManufacturer($this->request->get['manufacturer_id'], $this->request->post);
			$this->session->data['success'] = ($lang['text_success'] ?? '');
	
			$json['success'] = ($lang['text_success'] ?? '');
	
			if ($isAjax) {
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
				return;
			}
	
			$url = '';
	
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
	
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
	
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
	
			$this->response->redirect($this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
	
		if ($isAjax) {
			$json['error'] = ($lang['error_form'] ?? '');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
	
		$this->getForm();
	}
	
	public function delete() {
		$json = [];

		if (!$this->user->hasPermission('modify', 'shopmanager/manufacturer')) {
			$json['error'] = 'Permission refusée!';
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}

		$this->load->language('shopmanager/manufacturer');
		$data = [];
		
		$this->document->setTitle(($lang['heading_title'] ?? ''));
		$this->load->model('shopmanager/manufacturer');

	
		// Détecter si c'est une requête AJAX
		$isAjax = isset($this->request->get['ajax']) && $this->request->get['ajax'] == 'true';
	
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $manufacturer_id) {
				// Vérifier s'il y a des produits associés avant de supprimer
				$products = $this->model_shopmanager_manufacturer->getProductsByManufacturerId($manufacturer_id);
				if (!empty($products)) {
					$json['warning'] = "Ce fabricant est associé à des produits. Veuillez sélectionner un nouveau fabricant pour les produits avant de supprimer.";
					if ($isAjax) {
						$json['products'] = $products;
						$this->response->addHeader('Content-Type: application/json');
						$this->response->setOutput(json_encode($json));
						return;
					}
					// Ouvrir un popup ou afficher un message d'avertissement ici.
					// Logique à implémenter dans le front-end pour gérer cette situation.
				} else {
					// Supprimer le fabricant s'il n'a pas de produits associés
					$this->model_shopmanager_manufacturer->deleteManufacturer($manufacturer_id);
					$json['success'] = ($lang['text_success'] ?? '');
				}
			}
	
			$this->session->data['success'] = ($lang['text_success'] ?? '');
	
			if ($isAjax) {
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
				return;
			}
	
			$url = '';
	
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
	
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
	
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
	
			$this->response->redirect($this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
	
		if ($isAjax) {
			$json['error'] = ($lang['error_delete'] ?? '');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			return;
		}
	
		$this->getList();
	}
	

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('shopmanager/manufacturer/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('shopmanager/manufacturer/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['manufacturers'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$manufacturer_total = $this->model_shopmanager_manufacturer->getTotalManufacturers();

		$results = $this->model_shopmanager_manufacturer->getManufacturers($filter_data);

		foreach ($results as $result) {
			$data['manufacturers'][] = array(
				'manufacturer_id' => $result['manufacturer_id'],
				'name'            => $result['name'],
				'sort_order'      => $result['sort_order'],
				'edit'            => $this->url->link('shopmanager/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $result['manufacturer_id'] . $url, true)
			);
		}

		$data['heading_title'] = ($lang['heading_title'] ?? '');

		$data['text_list'] = ($lang['text_list'] ?? '');
		$data['text_no_results'] = ($lang['text_no_results'] ?? '');
		$data['text_confirm'] = ($lang['text_confirm'] ?? '');

		$data['column_name'] = ($lang['column_name'] ?? '');
		$data['column_sort_order'] = ($lang['column_sort_order'] ?? '');
		$data['column_action'] = ($lang['column_action'] ?? '');

		$data['button_add'] = ($lang['button_add'] ?? '');
		$data['button_edit'] = ($lang['button_edit'] ?? '');
		$data['button_delete'] = ($lang['button_delete'] ?? '');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		$data['sort_sort_order'] = $this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $manufacturer_total,
			'page'  => $page,
			'limit' => $this->config->get('config_limit_admin'),
			'url'   => $this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true)
		]);

		$data['results'] = sprintf(($lang['text_pagination'] ?? ''), ($manufacturer_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($manufacturer_total - $this->config->get('config_limit_admin'))) ? $manufacturer_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $manufacturer_total, ceil($manufacturer_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');

		$this->response->setOutput($this->load->view('shopmanager/manufacturer_list', $data));
	}

	protected function getForm() {
		$data['heading_title'] = ($lang['heading_title'] ?? '');

		$data['text_form'] = !isset($this->request->get['manufacturer_id']) ? ($lang['text_add'] ?? '') : ($lang['text_edit'] ?? '');
		$data['text_enabled'] = ($lang['text_enabled'] ?? '');
		$data['text_disabled'] = ($lang['text_disabled'] ?? '');
		$data['text_default'] = ($lang['text_default'] ?? '');
		$data['text_percent'] = ($lang['text_percent'] ?? '');
		$data['text_amount'] = ($lang['text_amount'] ?? '');

		$data['entry_name'] = ($lang['entry_name'] ?? '');
		$data['entry_store'] = ($lang['entry_store'] ?? '');
		$data['entry_keyword'] = ($lang['entry_keyword'] ?? '');
		$data['entry_image'] = ($lang['entry_image'] ?? '');
		$data['entry_sort_order'] = ($lang['entry_sort_order'] ?? '');
		$data['entry_customer_group'] = ($lang['entry_customer_group'] ?? '');

		$data['help_keyword'] = ($lang['help_keyword'] ?? '');

		$data['button_save'] = ($lang['button_save'] ?? '');
		$data['button_cancel'] = ($lang['button_cancel'] ?? '');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}

		if (isset($this->error['keyword'])) {
			$data['error_keyword'] = $this->error['keyword'];
		} else {
			$data['error_keyword'] = '';
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['manufacturer_id'])) {
			$data['action'] = $this->url->link('shopmanager/manufacturer/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('shopmanager/manufacturer/edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $this->request->get['manufacturer_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('shopmanager/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['manufacturer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$manufacturer_info = $this->model_shopmanager_manufacturer->getManufacturer($this->request->get['manufacturer_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($manufacturer_info)) {
			$data['name'] = $manufacturer_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->post['manufacturer_store'])) {
			$data['manufacturer_store'] = $this->request->post['manufacturer_store'];
		} elseif (isset($this->request->get['manufacturer_id'])) {
			$data['manufacturer_store'] = $this->model_shopmanager_manufacturer->getManufacturerStores($this->request->get['manufacturer_id']);
		} else {
			$data['manufacturer_store'] = array(0);
		}

		if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($manufacturer_info)) {
			$data['keyword'] = $manufacturer_info['keyword'];
		} else {
			$data['keyword'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($manufacturer_info)) {
			$data['image'] = $manufacturer_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($manufacturer_info) && is_file(DIR_IMAGE . $manufacturer_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($manufacturer_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($manufacturer_info)) {
			$data['sort_order'] = $manufacturer_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');

		$this->response->setOutput($this->load->view('shopmanager/manufacturer_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'shopmanager/manufacturer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$name = $this->request->post['name'] ?? '';
		if ((mb_strlen($name) < 2) || (mb_strlen($name) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		$keyword = $this->request->post['keyword'] ?? '';
		if (mb_strlen($keyword) > 0) {
			$this->load->model('shopmanager/url_alias');

			$url_alias_info = $this->model_shopmanager_url_alias->getUrlAlias($keyword);

			if ($url_alias_info && isset($this->request->get['manufacturer_id']) && $url_alias_info['query'] != 'manufacturer_id=' . $this->request->get['manufacturer_id']) {
				$this->error['keyword'] = $this->language->get('error_keyword');
			}

			if ($url_alias_info && !isset($this->request->get['manufacturer_id'])) {
				$this->error['keyword'] = $this->language->get('error_keyword');
			}
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'shopmanager/manufacturer')) {
			$this->error['warning'] = ($lang['error_permission'] ?? '');
		}

		$this->load->model('shopmanager/catalog/product');

		foreach ($this->request->post['selected'] as $manufacturer_id) {
			$product_total = $this->model_shopmanager_catalog_product->getTotalProductsByManufacturerId($manufacturer_id);

			if ($product_total) {
				$this->error['warning'] = sprintf(($lang['error_product'] ?? ''), $product_total);
			}
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('shopmanager/manufacturer');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_shopmanager_manufacturer->getManufacturers($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT product_id, name FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
	
		return $query->rows;
	}
	
}