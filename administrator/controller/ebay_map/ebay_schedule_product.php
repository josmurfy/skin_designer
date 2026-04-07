<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapEbayScheduleProduct extends Controller {
	private $error = array();

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('ebay_map/ebay_map_product');
		$this->_ebayMapProduct = $this->model_ebay_map_ebay_map_product;
    }

    public function index() {
		$data = array();
		$data = array_merge($data, $this->load->language('ebay_map/ebay_map_product'));
	$data = array_merge($data, $this->load->language('ebay_map/ebay_schedule_product'));
		$this->document->addScript('view/javascript/ebay_connector/webkul_ebay_connector.js');

		$data['text_currently_sync'] = sprintf($this->language->get('text_currently_sync'), 10);

		if(isset($this->request->get['account_id'])) {
			$account_id = $data['account_id'] = $this->request->get['account_id'];
		}else{
			$account_id = $data['account_id'] = 0;
		}

		if (isset($this->request->get['filter_oc_product_id_schedule'])) {
			$filter_oc_product_id = $this->request->get['filter_oc_product_id_schedule'];
		} else {
			$filter_oc_product_id = '';
		}

		if (isset($this->request->get['filter_oc_product_name_schedule'])) {
			$filter_oc_product_name = $this->request->get['filter_oc_product_name_schedule'];
		} else {
			$filter_oc_product_name = '';
		}

		if (isset($this->request->get['filter_ebay_product_id_schedule'])) {
			$filter_ebay_product_id = $this->request->get['filter_ebay_product_id_schedule'];
		} else {
			$filter_ebay_product_id = '';
		}

		if (isset($this->request->get['filter_category_name_schedule'])) {
			$filter_category_name = $this->request->get['filter_category_name_schedule'];
		} else {
			$filter_category_name = '';
		}

		if (isset($this->request->get['filter_source_sync_schedule'])) {
			$filter_source_sync = $this->request->get['filter_source_sync_schedule'];
		} else {
			$filter_source_sync = null;
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pm.id';
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		$url .= '&status=product_scheduling';

		if (isset($this->request->get['filter_oc_product_id_schedule'])) {
			$url .= '&filter_oc_product_id=' . $this->request->get['filter_oc_product_id_schedule'];
		}

		if (isset($this->request->get['filter_oc_product_name_schedule'])) {
			$url .= '&filter_oc_product_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_product_name_schedule'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_product_id_schedule'])) {
			$url .= '&filter_ebay_product_id=' . $this->request->get['filter_ebay_product_id_schedule'];
		}

		if (isset($this->request->get['filter_category_name_schedule'])) {
			$url .= '&filter_category_name=' . urlencode(html_entity_decode($this->request->get['filter_category_name_schedule'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_source_sync_schedule'])) {
			$url .= '&filter_source_sync=' . $this->request->get['filter_source_sync_schedule'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$url .= '&page=' . $this->request->get['page'];
		}




		$data['import_products'] = array();

		$filter_data = array(
			'filter_account_id' 				=> $account_id,
			'filter_oc_product_id'	  	=> $filter_oc_product_id,
			'filter_oc_product_name'	  => $filter_oc_product_name,
			'filter_ebay_product_id'		=> $filter_ebay_product_id,
			'filter_category_name' 			=> $filter_category_name,
			'filter_source_sync' 				=> $filter_source_sync,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$eBayProductTotal = $this->_ebayMapProduct->getTotalEbayProducts_schedule($filter_data);

		$results = $this->_ebayMapProduct->getProducts_schedule($filter_data);

		if($results){
			foreach ($results as $result) {
				$data['import_products'][] = array(
					'map_id' 					=> $result['id'],
					'oc_product_id' 	=> $result['oc_product_id'],
					'ebay_product_id' => $result['ebay_product_id'],
					'product_name'	 	=> $result['product_name'],
					'category_name'		=> $result['oc_category_name'],
					'sync_source'			=> $result['sync_source'],
					'schedule_date_time'=>date('d-m-Y', strtotime($result['scheduling_date'])).' '.$result['scheduling_time']
				);
			}
		}

		$data['token'] 	= $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		$url .= '&status=product_scheduling';

		if (isset($this->request->get['filter_oc_product_id_schedule'])) {
			$url .= '&filter_oc_product_id=' . $this->request->get['filter_oc_product_id_schedule'];
		}

		if (isset($this->request->get['filter_oc_product_name_schedule'])) {
			$url .= '&filter_oc_product_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_product_name_schedule'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_product_id_schedule'])) {
			$url .= '&filter_ebay_product_id=' . $this->request->get['filter_ebay_product_id_schedule'];
		}

		if (isset($this->request->get['filter_category_name_schedule'])) {
			$url .= '&filter_category_name=' . urlencode(html_entity_decode($this->request->get['filter_category_name_schedule'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_source_sync_schedule'])) {
			$url .= '&filter_source_sync=' . $this->request->get['filter_source_sync_schedule'];
		}

		if (isset($this->request->get['account_id'])) {
			$url .= '&account_id=' . $this->request->get['account_id'];
			$data['clear_product_filter'] 	= $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] .'&account_id=' . $this->request->get['account_id']. '&status=product_scheduling', true);
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_oc_cat_name'] = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&sort=name' . $url, true);
		$data['sort_ebay_category_id'] = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&sort=sort_order' . $url, true);
		$data['sort_ebay_category_name'] = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&sort=name' . $url, true);

		$url = '';

		$url .= '&status=product_scheduling';

		if (isset($this->request->get['filter_oc_product_id'])) {
			$url .= '&filter_oc_product_id=' . $this->request->get['filter_oc_product_id'];
		}

		if (isset($this->request->get['filter_oc_product_name'])) {
			$url .= '&filter_oc_product_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_product_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ebay_product_id'])) {
			$url .= '&filter_ebay_product_id=' . $this->request->get['filter_ebay_product_id'];
		}

		if (isset($this->request->get['filter_category_name'])) {
			$url .= '&filter_category_name=' . urlencode(html_entity_decode($this->request->get['filter_category_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_source_sync'])) {
			$url .= '&filter_source_sync=' . $this->request->get['filter_source_sync'];
		}

		if (isset($this->request->get['account_id'])) {
			$url .= '&account_id=' . $this->request->get['account_id'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'product_scheduling')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if(isset($this->request->get['account_id'])){
			$data['redirect'] 	= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&status=product_scheduling&account_id=' .$this->request->get['account_id'] , true));
		}else{
			$data['redirect'] 	= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'], true));
		}

		$pagination = new Pagination();
		$pagination->total = $eBayProductTotal;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($eBayProductTotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($eBayProductTotal - $this->config->get('config_limit_admin'))) ? $eBayProductTotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $eBayProductTotal, ceil($eBayProductTotal / $this->config->get('config_limit_admin')));

		$data['filter_oc_product_id_schedule'] 			= $filter_oc_product_id;
		$data['filter_oc_product_name_schedule'] 		= $filter_oc_product_name;
		$data['filter_ebay_product_id_schedule'] 		= $filter_ebay_product_id;
		$data['filter_category_name_schedule'] 			= $filter_category_name;
		$data['filter_source_sync_schedule'] 				= $filter_source_sync;
		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('ebay_map/ebay_schedule_product_list', $data);
	}
  public function autocomplete(){
		$json = array();

			if(isset($this->request->get['account_id']) && (isset($this->request->get['filter_oc_product_name']) || isset($this->request->get['filter_category_name']))){
					$getFilter = '';
					if(isset($this->request->get['filter_oc_product_name'])){
						$getFilter = 'oc_product';
						$oc_product = $this->request->get['filter_oc_product_name'];
					}else{
						$oc_product = '';
					}

					if(isset($this->request->get['filter_category_name'])){
						$getFilter = 'oc_category';
						$oc_category = $this->request->get['filter_category_name'];
					}else{
						$oc_category = '';
					}

					$filter_data = array(
						'filter_account_id' 				=> $this->request->get['account_id'],
						'filter_oc_product_name' 		=> $oc_product,
						'filter_category_name' 			=> $oc_category,
						'order'       => 'ASC',
						'start'       => 0,
						'limit'       => 5
					);

					$results = $this->_ebayMapProduct->getProducts_schedule($filter_data);

					foreach ($results as $result) {
							if($getFilter == 'oc_product'){
									$json[$result['oc_product_id']] = array(
										'item_id' 		=> $result['oc_product_id'],
										'name'        => strip_tags(html_entity_decode($result['product_name'], ENT_QUOTES, 'UTF-8'))
									);
							}else if($getFilter == 'oc_category'){
									$json[$result['oc_category_id']] = array(
										'item_id' 		=> $result['oc_category_id'],
										'name'        => strip_tags(html_entity_decode($result['oc_category_name'], ENT_QUOTES, 'UTF-8'))
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
	public function  reScheduleProduct(){
   $message='';
		if(isset($this->request->post['product_ids'])){
			foreach ($this->request->post['product_ids'] as $key => $product_id) {
							if((int) $product_id){

					  	$message .=$this->_ebayMapProduct->reScheduleProduct($product_id);
					}
			}

		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($message));
	}
	public function cancelScheduleProduct(){

		$message='';
		if(isset($this->request->post['product_ids'])){
			foreach ($this->request->post['product_ids'] as $key => $product_id) {
							if((int) $product_id){
        //When live then uncomment it
					 $message .=$this->_ebayMapProduct->cancelScheduleProduct($product_id);
					}
			}

		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($message));
	}

}
