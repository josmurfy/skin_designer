<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapEbayAccount extends Controller {
	private $error = array();
	private $post_fields = array(
		'ebay_connector_store_name',
		'ebay_connector_ebay_sites',
		'ebay_connector_ebay_user_id',
		'ebay_connector_ebay_auth_token',
		'ebay_connector_ebay_application_id',
		'ebay_connector_ebay_developer_id',
		'ebay_connector_ebay_certification_id',
		'ebay_connector_ebay_shop_postal_code',
		'ebay_connector_ebay_currency',
		// 'ebay_connector_ebay_conversion_rate',
	);

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('ebay_map/ebay_account');
		$this->_ebayAccount = $this->model_ebay_map_ebay_account;
    }

    public function index() {
    	$this->load->language('ebay_map/ebay_account');

		$this->getList();
	}

	public function delete() {
		$this->load->language('ebay_map/ebay_account');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $account_id) {
				$this->_ebayAccount->deleteAccount($account_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = $this->ocwebkul->_setUrlVars();

			$this->response->redirect($this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	public function __manageUrlFilter() {
		$url  = $this->ocwebkul->_appendNumericVarToUrl('filter_account_id');

 	  $url .= $this->ocwebkul->_appendStringVarToUrl('filter_store_name');

 	  $url .= $this->ocwebkul->_appendStringVarToUrl('filter_ebay_user_id');

	  return $url;
  }

  public function __manageUrlFilterWithotPageVar() {
		$url  = $this->__manageUrlFilter();

 	  $url .= $this->ocwebkul->_appendNumericVarToUrl('sort');

 	  $url .= $this->ocwebkul->_appendNumericVarToUrl('order');

	  return $url;
  }

  public function __manageUrlSOPFilter() {
    $url  = $this->__manageUrlFilterWithotPageVar();
		$url .= $this->ocwebkul->_appendNumericVarToUrl('page');
		return $url;
  }

	protected function getList() {
		$data = array();
		$data = $this->load->language('ebay_map/ebay_account');

		$this->document->setTitle($this->language->get('heading_title_add'));

	  $filter_account_id   = $this->ocwebkul->_manageGetVariable('filter_account_id',null);

		$filter_store_name   = $this->ocwebkul->_manageGetVariable('filter_store_name',null);

		$filter_ebay_user_id = $this->ocwebkul->_manageGetVariable('filter_ebay_user_id',null);

		$sort   = $this->ocwebkul->_manageGetVariable('sort','name');

    $order  = $this->ocwebkul->_manageGetVariable('order','ASC');

		$page   = $this->ocwebkul->_manageGetVariable('page',1);

		$url    =  $this->__manageUrlSOPFilter();

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . $url, true)
		);

		$data['add_account'] = $this->url->link('ebay_map/ebay_account/getForm', 'token=' . $this->session->data['token'] . $url, true);

		$data['delete'] = $this->url->link('ebay_map/ebay_account/delete', 'token=' . $this->session->data['token'] . $url, true);

		$data['clear_filter'] = $this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'], true);

		$data['token'] 	= $this->session->data['token'];


		$data['ebay_accounts'] = array();

		$filter_data = array(
			'filter_account_id'	  	=> $filter_account_id,
			'filter_store_name'	  	=> $filter_store_name,
			'filter_ebay_user_id'	=> $filter_ebay_user_id,
			'sort'  				=> $sort,
			'order' 				=> $order,
			'start' 				=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'				 	=> $this->config->get('config_limit_admin')
		);

		$eBayTotalAccount = $this->_ebayAccount->getTotalEbayAccount($filter_data);

		$results = $this->_ebayAccount->getEbayAccount($filter_data);

		if($results){
			foreach ($results as $result) {
				$data['ebay_accounts'][] = array(
					'account_id' 		=> $result['id'],
					'store_name'    => $result['ebay_connector_store_name'],
					'ebay_user_id'  => $result['ebay_connector_ebay_user_id'],
					'edit'  				=> $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&account_id=' . $result['id'] . $url, true),
				);
			}
		}

		if(isset($this->session->data['error_warning']) && $this->session->data['error_warning']) {
			$this->error['warning'] = $this->session->data['error_warning'];
			unset($this->session->data['error_warning']);
		}

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

		$url  = $this->__manageUrlFilter();

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['ebay_events']  = array(
		 'ItemSold' => $this->data['text_sold'],
		 'ItemListed' => $this->data['text_listed'],
		 'ItemRevised' =>$this->data['text_revised'],
		 'ItemClosed' =>$this->data['text_delete']
		);

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_account_id'] = $this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . '&sort=id' . $url, true);
		$data['sort_ebay_store_name'] = $this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . '&sort=ebay_connector_store_name' . $url, true);
		$data['sort_ebay_user_id'] = $this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . '&sort=ebay_connector_ebay_user_id' . $url, true);

		$url = $this->__manageUrlFilterWithotPageVar();

		$pagination = new Pagination();
		$pagination->total = $eBayTotalAccount;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($eBayTotalAccount) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($eBayTotalAccount - $this->config->get('config_limit_admin'))) ? $eBayTotalAccount : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $eBayTotalAccount, ceil($eBayTotalAccount / $this->config->get('config_limit_admin')));

		$data['filter_account_id'] 	= $filter_account_id;
		$data['filter_store_name'] 	= $filter_store_name;
		$data['filter_ebay_user_id']= $filter_ebay_user_id;

		$data['sort'] = $sort;
		$data['order'] = $order;

    $data  = array_merge($data,$this->_loadCommonControllers());

		$this->response->setOutput($this->load->view('ebay_map/ebay_account', $data));
	}

	public function add() {

		$data = array();

		$data = $this->load->language('ebay_map/ebay_account');

		$this->document->setTitle($this->language->get('heading_title_add'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAccount()) {

			$this->_ebayAccount->__addEbayAccount($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_add');

			$url = $this->ocwebkul->_setUrlVars();

			$this->response->redirect($this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}


	public function edit() {
		$data = array();
		$data = array_merge($data, $this->load->language('ebay_map/ebay_account'));

		$this->document->setTitle($this->language->get('heading_title_edit'));

		$current_account_id = isset($this->request->get['account_id']) ? $this->request->get['account_id'] : 0;

		$response = $this->Ebayconnector->getEbayStoreDetails($current_account_id);

		if(empty($response)) {
			$this->session->data['error_warning'] = $this->language->get('error_wrong_account_id');
			$this->response->redirect($this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'], true));
		}

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateAccount()) {

			$this->_ebayAccount->__addEbayAccount($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success_add');

			$url = $this->ocwebkul->_setUrlVars();

			$this->response->redirect($this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function _loadCommonControllers() {
		$data['header'] = $this->load->controller('common/header');

		$data['column_left'] = $this->load->controller('common/column_left');

		$data['footer'] = $this->load->controller('common/footer');

		return $data;
	}

	public function getForm() {
		$data = array();
		$data = $this->load->language('ebay_map/ebay_account');

		if(isset($this->request->get['account_id'])){
			$this->document->setTitle($this->language->get('heading_title_edit'));
			$data['text_account_tab'] = $this->language->get('text_account_edit_tab');
		}else{
			$this->document->setTitle($this->language->get('heading_title_add'));
			$data['text_account_tab'] = $this->language->get('text_account_add_tab');
		}

		$ebaySites = $this->Ebayconnector->getEbaySiteList();
		$data['ebay_sites'] = $ebaySites['ebay_sites'];

		$data['ebaySiteCurrency'] = $this->Ebayconnector->getEbaySiteCurrency();

		$url = '';
		$data['ebay_events']  = array(
						 'ItemSold' => $this->data['text_sold'],
						 'ItemListed' => $this->data['text_listed'],
						 'ItemRevised' =>$this->data['text_revised'],
						 'ItemClosed' =>$this->data['text_delete']
		);

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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['account_id'])) {
			$data['action'] = $this->url->link('ebay_map/ebay_account/add', 'token=' . $this->session->data['token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&account_id=' . $this->request->get['account_id'] .$url, true);
		}

		$data['cancel'] = $this->url->link('ebay_map/ebay_account', 'token=' . $this->session->data['token'] , true);

		$data['token'] 	= $this->session->data['token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['error_auth'])) {
			$data['error_ebay_connector_ebay_auth_token'] = $this->error['error_auth'];
		} else {
			$data['error_ebay_connector_ebay_auth_token'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		foreach ($this->post_fields as $key => $error_value) {
			if (isset($this->error['error_'.$error_value])  && $error_value != 'ebay_connector_ebay_sites') {
				$data['error_'.$error_value] = $this->error['error_'.$error_value];
			} else {
				$data['error_'.$error_value] = '';
			}
		}

		if (isset($this->error['error_ebay_connector_store_exist'])) {
			$data['error_ebay_connector_store_exist'] = $this->error['error_ebay_connector_store_exist'];
		} else {
			$data['error_ebay_connector_store_exist'] = '';
		}
		if(isset($this->request->get['account_id'])){
			$data['account_id'] = $this->request->get['account_id'];
		}else{
			$data['account_id'] = '';
		}

		$shipping_info = array();

		if (isset($this->request->get['account_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$account_info = $this->_ebayAccount->getEbayAccount(array('filter_account_id' => $this->request->get['account_id']), false);

			$shipping_info = $this->_ebayAccount->getShippingDetails($this->request->get['account_id']);
		}

		foreach ($this->post_fields as $key => $post_value) {
			if (isset($this->request->post[$post_value])) {
				$data[$post_value] = $this->request->post[$post_value];
			} elseif (!empty($account_info[0])) {
				$data[$post_value] = $account_info[0][$post_value];
			} else {
				$data[$post_value] = '';
			}
		}

		$shipping_details = array(
			'shipping_priority',
			'shipping_service',
			'shipping_cost',
			'shipping_additional_cost',
			'shipping_min_time',
			'shipping_max_time',
			'free_shipping_status'
		);

		foreach ($shipping_details as $shipping) {
			if (isset($this->request->post[$shipping])) {
				$data[$shipping] = $this->request->post[$shipping];
			} else if (isset($shipping_info[$shipping])) {
				$data[$shipping] = $shipping_info[$shipping];
			} else {
				$data[$shipping] = '';
			}
		}

		$data['category_map'] 	= $this->load->controller('ebay_map/ebay_map_category');
		$data['product_map'] 	= $this->load->controller('ebay_map/ebay_map_product');
		$data['schedule_product'] 	= $this->load->controller('ebay_map/ebay_schedule_product');
		$data['export_product'] = $this->load->controller('ebay_map/export_product_to_ebay');
		$data['order_map'] 		= $this->load->controller('ebay_map/ebay_map_order');
    // $data['single_export'] 	= $this->load->controller('ebay_map/single_export');

		$data  = array_merge($data,$this->_loadCommonControllers());

		$this->response->setOutput($this->load->view('ebay_map/ebay_account_form', $data));
	}

	protected function validateAccount() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_account')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		//post fields blank check
		foreach ($this->post_fields as $key => $post_value) {
			if(empty($this->request->post[$post_value]) && $post_value != 'ebay_connector_ebay_sites'){
				$this->error['error_'.$post_value]	=	$this->language->get('error_'.$post_value);
				$this->error['warning'] = $this->language->get('error_field_required');
			}
		}

		if(isset($this->request->post['ebay_connector_store_name']) && $this->request->post['ebay_connector_store_name'] && !isset($this->request->get['account_id'])){
			$getEbayAccount = $this->_ebayAccount->getEbayAccount(array('filter_store_name' => $this->request->post['ebay_connector_store_name']), true);

			if(isset($getEbayAccount[0]['id']) && $getEbayAccount[0]['id']){
				$this->error['error_ebay_connector_store_exist'] = $this->language->get('error_ebay_connector_store_exist');
			}
		}

		if(!$this->error){
				$getEbayClient 		= $this->Ebayconnector->_eBayAuthSession($this->request->post);
				if($getEbayClient){
						$current_date 		= new \DateTime();
						$currentDateTime	= $current_date->format('Y-m-d\TH:i:s');
																$current_date->modify('+119 day');
						$endDateTime 			= $current_date->format('Y-m-d\TH:i:s');

						$item_data = [
								'Version' 					=> 849, //version
								'IncludeVariations' => false,
								'UserID' 						=> $this->request->post['ebay_connector_ebay_user_id'],
								'DetailLevel' 			=> 'ItemReturnDescription',
								'Pagination' 				=> array(
																					'EntriesPerPage'=> 1,
																					'PageNumber' 		=> 1
																			),
								'EndTimeFrom' 			=> $currentDateTime,
								'EndTimeTo' 				=> $endDateTime,
						];
						$results = $getEbayClient->GetSellerList($item_data);

						if(isset($results->faultstring)){
							$this->error['warning'] = $results->faultstring;
							$this->error['error_auth'] = $results->faultstring;
						} else if(isset($results->Ack) && isset($results->Errors) && $results->Ack == 'Failure'){
							  if(isset($results->Errors->LongMessage) && $results->Errors->LongMessage){
									$this->error['warning'] = $this->language->get('error_wrong_account_details'). ' .'.$results->Errors->LongMessage;
								} else{
									$this->error['warning'] = $this->language->get('error_wrong_account_details');
								}
					} else {

					}
				}
		}
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_account')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
