<?php
class ControllerExtensionModuleMeadminsearch extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/me_admin_search');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('me_admin_search', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$labels = array('heading_title','text_edit','text_enabled','text_disabled','text_save','text_cancel','button_save','button_cancel','entry_displaypname','entry_displaypmodel','entry_displaypsku','entry_displaycategory','help_replace_addtocart','entry_status','entry_displaymanufacturer','entry_displaycustomer','entry_displaycustomeremail','entry_displayorderid','entry_displayorderbycustomer','text_category','text_product','text_manufacturer','entry_displayorderbyproduct','entry_displayorderstatus','text_yes','text_no','entry_product','entry_category','help_displaypname','help_displaypmodel','help_displaypsku','help_displaycategory','help_displaymanufacturer','help_displaycustomer','help_displaycustomeremail','help_displayorderid','help_displayorderbycustomer','help_displayorderbyproduct','help_displayorderstatus','help_displayordertotal','entry_displayordertotal','help_displayorderdate','entry_displayorderdate','help_displaycoupon','entry_displaycoupon','help_displayoption','entry_displayoption','entry_displaycustomertelephone','help_displaycustomertelephone','entry_displayorderbycustomertel','help_displayorderbycustomertel');
		
		foreach($labels as $label){
			$data[$label] = $this->language->get($label);
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/me_admin_search', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/module/me_admin_search', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->post['me_admin_search_status'])) {
			$data['me_admin_search_status'] = $this->request->post['me_admin_search_status'];
		} else {
			$data['me_admin_search_status'] = $this->config->get('me_admin_search_status');
		}
		
		if (isset($this->request->post['me_admin_search_filter'])) {
			$data['me_admin_search_filter'] = $this->request->post['me_admin_search_filter'];
		} else {
			$data['me_admin_search_filter'] = $this->config->get('me_admin_search_filter');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/me_admin_search', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/me_admin_search')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('marketing/coupon');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'c.name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_marketing_coupon->getCoupons($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'coupon_id' => $result['coupon_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
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
	
	public function pautocomplete() {
		$json = array();

		if (isset($this->request->get['filter_sku'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');
			
			if (isset($this->request->get['filter_sku'])) {
				$filter_sku = $this->request->get['filter_sku'];
			} else {
				$filter_sku = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_sku' => $filter_sku,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {				
				if($result['sku']){
					$json[] = array(
						'product_id' => $result['product_id'],
						'sku'      => $result['sku']
					);
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}