<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapEbayMapCategory extends Controller {
	private $error = array();

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('ebay_map/ebay_map_category');
		$this->_ebayMapCategory = $this->model_ebay_map_ebay_map_category;
    }

    public function index() {
		$data = array();
		$data = array_merge($data, $this->load->language('ebay_map/ebay_map_category'));

		$this->document->addScript('view/javascript/ebay_connector/webkul_ebay_connector.js');

		if(isset($this->request->get['account_id'])) {
			$account_id = $data['account_id'] = $this->request->get['account_id'];
		}else{
			$account_id = $data['account_id'] = 0;
		}

		if (isset($this->request->get['filter_oc_category_id'])) {
			$filter_oc_category_id = $this->request->get['filter_oc_category_id'];
		} else {
			$filter_oc_category_id = '';
		}

		if (isset($this->request->get['filter_oc_category_name'])) {
			$filter_oc_category_name = $this->request->get['filter_oc_category_name'];
		} else {
			$filter_oc_category_name = '';
		}

		if (isset($this->request->get['filter_ebay_category_id'])) {
			$filter_ebay_category_id = $this->request->get['filter_ebay_category_id'];
		} else {
			$filter_ebay_category_id = '';
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$filter_ebay_category_name = $this->request->get['filter_ebay_category_name'];
		} else {
			$filter_ebay_category_name = '';
		}

		if (isset($this->request->get['filter_variation_type'])) {
			$filter_variation_type = $this->request->get['filter_variation_type'];
		} else {
			$filter_variation_type = null;
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		$url .= '&status=account_category_map';

		if (isset($this->request->get['filter_oc_category_id'])) {
			$url .= '&filter_oc_category_id=' . $this->request->get['filter_oc_category_id'];
		}

		if (isset($this->request->get['filter_oc_category_name'])) {
			$url .= '&filter_oc_category_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_category_id'])) {
			$url .= '&filter_ebay_category_id=' . $this->request->get['filter_ebay_category_id'];
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_variation_type'])) {
			$url .= '&filter_variation_type=' . $this->request->get['filter_variation_type'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['map_new_category'] = $this->url->link('ebay_map/ebay_map_category/new_map', 'user_token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('ebay_map/ebay_map_category/delete', 'user_token=' . $this->session->data['token'] . $url, true);


		$data['map_categories'] = array();

		$filter_data = array(
			'filter_account_id' 				=> $account_id,
			'filter_oc_category_id'	  	=> $filter_oc_category_id,
			'filter_oc_category_name'	  => $filter_oc_category_name,
			'filter_ebay_category_id'		=> $filter_ebay_category_id,
			'filter_ebay_category_name' => $filter_ebay_category_name,
			'filter_variation_type' 		=> $filter_variation_type,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$mapCategoryTotal = $this->_ebayMapCategory->getTotalMapCategories($filter_data);

		$results = $this->_ebayMapCategory->getMapCategories($filter_data);

		if($results){
			foreach ($results as $result) {
				$data['map_categories'][] = array(
					'map_id' 					=> $result['map_id'],
					'oc_cat_name'  		=> $result['name'],
					'ebay_cat_id'  		=> $result['ebay_category_id'],
					'ebay_cat_name' 	=> $result['ebay_category_name'],
					'ebay_condition_attr' 	=> $result['pro_condition_attr'],
					'ebay_varitions' 	=> $result['variations_enabled'],
				);
			}
		}

		/**
		 * get opencart parent categories only
		 */
		$data['opencart_categories'] 	= $this->_ebayMapCategory->get_OpencartCategories(array('filter_parent_id' => false));

		$data['ebay_categories']		= array();
		if($account_id){
				$getAccountDetails = $this->Ebayconnector->_getModuleConfiguration($account_id);
				if(isset($getAccountDetails['ebaySites']))
					$data['ebay_categories']		= $this->_ebayMapCategory->get_EbayCategories(array('filter_parent_id' => false, 'filter_ebay_site_id' => $getAccountDetails['ebaySites']));

		}

		$data['token'] 	= $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['error_select_opencart_category'])) {
			$data['error_opencart_category'] = $this->error['error_select_opencart_category'];
		} else {
			$data['error_opencart_category'] = '';
		}

		if (isset($this->error['error_select_ebay_category'])) {
			$data['error_ebay_category'] = $this->error['error_select_ebay_category'];
		} else {
			$data['error_ebay_category'] = '';
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

		$url .= '&status=account_category_map';

		if (isset($this->request->get['filter_oc_category_id'])) {
			$url .= '&filter_oc_category_id=' . $this->request->get['filter_oc_category_id'];
		}

		if (isset($this->request->get['filter_oc_category_name'])) {
			$url .= '&filter_oc_category_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_category_id'])) {
			$url .= '&filter_ebay_category_id=' . $this->request->get['filter_ebay_category_id'];
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_variation_type'])) {
			$url .= '&filter_variation_type=' . $this->request->get['filter_variation_type'];
		}

		if (isset($this->request->get['account_id'])) {
			$url .= '&account_id=' . $this->request->get['account_id'];
			$data['clear_category_filter'] 	= $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] .'&account_id=' . $this->request->get['account_id']. '&status=account_category_map', true);
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_oc_cat_name'] = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&sort=name' . $url, true);
		$data['sort_ebay_category_id'] = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&sort=sort_order' . $url, true);
		$data['sort_ebay_category_name'] = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&sort=name' . $url, true);

		$url = '';

		$url .= '&status=account_category_map';

		if (isset($this->request->get['filter_oc_category_id'])) {
			$url .= '&filter_oc_category_id=' . $this->request->get['filter_oc_category_id'];
		}

		if (isset($this->request->get['filter_oc_category_name'])) {
			$url .= '&filter_oc_category_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_category_id'])) {
			$url .= '&filter_ebay_category_id=' . $this->request->get['filter_ebay_category_id'];
		}

		if (isset($this->request->get['filter_ebay_category_name'])) {
			$url .= '&filter_ebay_category_name=' . urlencode(html_entity_decode($this->request->get['filter_ebay_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_variation_type'])) {
			$url .= '&filter_variation_type=' . $this->request->get['filter_variation_type'];
		}

		if (isset($this->request->get['account_id'])) {
			$url .= '&account_id=' . $this->request->get['account_id'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_category_map')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $mapCategoryTotal;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($mapCategoryTotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($mapCategoryTotal - $this->config->get('config_limit_admin'))) ? $mapCategoryTotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $mapCategoryTotal, ceil($mapCategoryTotal / $this->config->get('config_limit_admin')));

		$data['filter_oc_category_id'] 			= $filter_oc_category_id;
		$data['filter_oc_category_name'] 		= $filter_oc_category_name;
		$data['filter_ebay_category_id'] 		= $filter_ebay_category_id;
		$data['filter_ebay_category_name'] 	= $filter_ebay_category_name;
		$data['filter_variation_type'] 			= $filter_variation_type;
		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('ebay_map/ebay_map_category_list', $data);
	}

	public function deleteMapCategory() {
		$json = array();
		$this->load->language('ebay_map/ebay_map_category');

		if (isset($this->request->post['selected']) && $this->validateDelete() && $this->request->get['account_id']) {
			foreach ($this->request->post['selected'] as $category_id) {
				$this->_ebayMapCategory->deleteMapCategory($category_id);
			}
			$json['success']	=	$this->language->get('text_success_map_cat_delete');
			$this->session->data['success'] = $this->language->get('text_success_map_cat_delete');
			$json['redirect'] 				= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&status=account_category_map&account_id=' .$this->request->get['account_id'] , true));
		}
		if(isset($this->error['warning'])){
			$json['error_permission']	=	$this->language->get('error_permission');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


	public function validateCategoryForm(){
		$json = array();

		$this->load->language('ebay_map/ebay_map_category');

		if(isset($this->request->post['opencart_category'][0]) && !$this->request->post['opencart_category'][0]){
			$json['error_select_opencart_category'] = $this->language->get('error_select_opencart_category');
		}

		if(isset($this->request->post['ebay_category'][0]) && !$this->request->post['ebay_category'][0]){
			$json['error_select_ebay_category'] = $this->language->get('error_select_ebay_category');
		}

		if(!$json){
			$opencartCategory = array_filter($this->request->post['opencart_category']);
			$result_opencart = $this->_ebayMapCategory->validateBothCategory(array('opencart_category' => end($opencartCategory), 'account_id' => $this->request->get['account_id']), $status = 'opencart');
			if(!empty($result_opencart) && isset($result_opencart['opencart'])){
				$json['error_select_opencart_category'] = $this->language->get('error_opencart_cat_already_map');
			}
			$result_ebay = $this->_ebayMapCategory->validateBothCategory(array('ebay_category' => end($this->request->post['ebay_category']), 'account_id' => $this->request->get['account_id']), $status = 'ebay');

			if(!empty($result_ebay) && isset($result_ebay['ebay'])){
				$json['error_select_ebay_category'] = $this->language->get('error_ebay_cat_already_map');
			}
		}

		if ($json && !isset($json['warning'])) {
			$json['warning'] = $this->language->get('error_fields');
		}else{
			$opencartCategory = array_filter($this->request->post['opencart_category']);
			$json = $this->_ebayMapCategory->map_category(array('account_id' => $this->request->post['account_id'], 'opencart_category' => end($opencartCategory), 'ebay_category' => end($this->request->post['ebay_category'])) );
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_map_category')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function getOcChildCategories() {
		$json = array();

		if (isset($this->request->post['parent_category_id'])) {

			$filter_data = array(
				'filter_parent_id' => $this->request->post['parent_category_id'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->_ebayMapCategory->get_OpencartCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['category_id'],
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

	public function geteBayChildCategories() {
		$json = array();

		if (isset($this->request->post['parent_category_id']) && isset($this->request->get['ebay_site_id'])) {

			$filter_data = array(
				'filter_parent_id' 		=> (int)$this->request->post['parent_category_id'],
				'filter_ebay_site_id' => (int)$this->request->get['ebay_site_id'],
				'sort'        => 'name',
				'order'       => 'ASC',
			);

			$results = $this->_ebayMapCategory->get_EbayCategories($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'category_id' => $result['ebay_category_id'],
					'name'        => strip_tags(html_entity_decode($result['ebay_category_name'], ENT_QUOTES, 'UTF-8'))
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

	public function autocomplete(){
		$json = array();

			if(isset($this->request->get['account_id']) && (isset($this->request->get['filter_oc_category_name']) || isset($this->request->get['filter_ebay_category_name']))){
					$getFilter = '';
					if(isset($this->request->get['filter_oc_category_name'])){
						$getFilter = 'oc_category';
						$oc_category = $this->request->get['filter_oc_category_name'];
					}else{
						$oc_category = '';
					}

					if(isset($this->request->get['filter_ebay_category_name'])){
						$getFilter = 'ebay_category';
						$ebay_category = $this->request->get['filter_ebay_category_name'];
					}else{
						$ebay_category = '';
					}

					$filter_data = array(
						'filter_account_id' 				=> $this->request->get['account_id'],
						'filter_oc_category_name' 	=> $oc_category,
						'filter_ebay_category_name' => $ebay_category,
						'order'       => 'ASC',
						'start'       => 0,
						'limit'       => 5
					);

					$results = $this->_ebayMapCategory->getMapCategories($filter_data);

					foreach ($results as $result) {
							if($getFilter == 'oc_category'){
									$json[] = array(
										'category_id' => $result['opencart_category_id'],
										'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
									);
							}else if($getFilter == 'ebay_category'){
									$json[] = array(
										'category_id' => $result['ebay_category_id'],
										'name'        => strip_tags(html_entity_decode($result['ebay_category_name'], ENT_QUOTES, 'UTF-8'))
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
	}
}
