<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapEbayMapProduct extends Controller {
	private $error = array();

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('ebay_map/ebay_map_product');
		$this->_ebayMapProduct = $this->model_ebay_map_ebay_map_product;
    }

    public function index() {
		$data = array();
		$data = array_merge($data, $this->load->language('ebay_map/ebay_map_product'));

		$this->document->addScript('view/javascript/ebay_connector/webkul_ebay_connector.js');

		$data['text_currently_sync'] = sprintf($this->language->get('text_currently_sync'), 10);

		if(isset($this->request->get['account_id'])) {
			$account_id = $data['account_id'] = $this->request->get['account_id'];
		}else{
			$account_id = $data['account_id'] = 0;
		}

		if (isset($this->request->get['filter_oc_product_id'])) {
			$filter_oc_product_id = $this->request->get['filter_oc_product_id'];
		} else {
			$filter_oc_product_id = '';
		}

		if (isset($this->request->get['filter_oc_product_name'])) {
			$filter_oc_product_name = $this->request->get['filter_oc_product_name'];
		} else {
			$filter_oc_product_name = '';
		}

		if (isset($this->request->get['filter_ebay_product_id'])) {
			$filter_ebay_product_id = $this->request->get['filter_ebay_product_id'];
		} else {
			$filter_ebay_product_id = '';
		}

		if (isset($this->request->get['filter_category_name'])) {
			$filter_category_name = $this->request->get['filter_category_name'];
		} else {
			$filter_category_name = '';
		}

		if (isset($this->request->get['filter_source_sync'])) {
			$filter_source_sync = $this->request->get['filter_source_sync'];
		} else {
			$filter_source_sync = null;
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pm.id';
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		$url .= '&status=account_product_map';

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

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['import_from_ebay'] = $this->url->link('ebay_map/ebay_map_product/', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('ebay_map/ebay_map_product/delete', 'token=' . $this->session->data['token'] . $url, true);


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

		$eBayProductTotal = $this->_ebayMapProduct->getTotalEbayProducts($filter_data);

		$results = $this->_ebayMapProduct->getProducts($filter_data);

		if($results){
			foreach ($results as $result) {
				$data['import_products'][] = array(
					'map_id' 					=> $result['id'],
					'oc_product_id' 	=> $result['oc_product_id'],
					'ebay_product_id' => $result['ebay_product_id'],
					'product_name'	 	=> $result['product_name'],
					'category_name'		=> $result['oc_category_name'],
					'sync_source'			=> $result['sync_source'],
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

		$url .= '&status=account_product_map';

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
			$data['clear_product_filter'] 	= $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] .'&account_id=' . $this->request->get['account_id']. '&status=account_product_map', true);
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_oc_cat_name'] = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&sort=name' . $url, true);
		$data['sort_ebay_category_id'] = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, true);
		$data['sort_ebay_category_name'] = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&sort=name' . $url, true);

		$url = '';

		$url .= '&status=account_product_map';

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

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_product_map')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if(isset($this->request->get['account_id'])){
			$data['redirect'] 	= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&status=account_product_map&account_id=' .$this->request->get['account_id'] , true));
		}else{
			$data['redirect'] 	= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'], true));
		}

		$pagination = new Pagination();
		$pagination->total = $eBayProductTotal;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($eBayProductTotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($eBayProductTotal - $this->config->get('config_limit_admin'))) ? $eBayProductTotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $eBayProductTotal, ceil($eBayProductTotal / $this->config->get('config_limit_admin')));

		$data['filter_oc_product_id'] 			= $filter_oc_product_id;
		$data['filter_oc_product_name'] 		= $filter_oc_product_name;
		$data['filter_ebay_product_id'] 		= $filter_ebay_product_id;
		$data['filter_category_name'] 			= $filter_category_name;
		$data['filter_source_sync'] 				= $filter_source_sync;
		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('ebay_map/ebay_map_product_list', $data);
	}

	public function deleteMapProduct() {
		$json = array();
		$this->load->language('ebay_map/ebay_map_product');

		if (isset($this->request->post['selected']) && $this->validateDelete() && $this->request->get['account_id']) {
			foreach ($this->request->post['selected'] as $map_id) {
				$result = $this->_ebayMapProduct->deleteMapProducts(array('map_id' => $map_id, 'account_id' => $this->request->get['account_id']));
			}
			if($result)
				$json['success']				= $this->language->get('text_success_product_delete');
				$this->session->data['success'] = $this->language->get('text_success_product_delete');
				$json['redirect'] 				= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&status=account_product_map&account_id=' .$this->request->get['account_id'] , true));
		}
		if(isset($this->error['warning'])){
			$json['error_permission']	=	$this->language->get('error_permission');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function start_syncronize($ebay_products, $account_id){
		$response = array();
		$count_success = $count_error = 0;

		if(isset($ebay_products) && !empty($ebay_products)){
				$this->load->language('ebay_map/ebay_map_product');
				$result = $this->_ebayMapProduct->import_EbayStepProduct($ebay_products, $account_id);
				$manageErrorArray  = array();
				if (isset($result['error']) && is_array($result['error'])) {
					foreach ($result['error'] as $key => $error) {
						foreach ($error as $key1 => $error_string) {
							$count_error++;
							array_push($manageErrorArray, $error_string);
						}
					}
					$response['error'] = $manageErrorArray;
				} else if(isset($result['error']) && $result['error']) {
					$response['error'] = array($result['error']);
					$count_error++;
				}
				if ($count_error) {
					$response['error_count'] = sprintf($this->language->get('error_product_sync_failed'), $count_error);
				}

				$manageSuccessArray  = array();
				if (isset($result['data']) && $result['data'] && is_array($result['data'])) {
						foreach ($result['data'] as $key => $data_array) {
							foreach ($data_array as $key1 => $value) {
								$count_success++;
								array_push($manageSuccessArray, $value);
							}
						}
						foreach ($manageSuccessArray as $key => $product_report) {
								$getProductEntry = $this->_ebayMapProduct->__getItemRecord(array('product_id' => $product_report['product_id']), $this->request->post['account_id']);
								if(isset($getProductEntry['name']) && $getProductEntry['name']){
									// $this->session->data['success'] = $this->language->get('text_success_ebay_import');
									if(isset($product_report['state']) && $product_report['state'] == 'created'){
											$response['success'][] 		= sprintf($this->language->get('text_success_import_product'), $getProductEntry['name']);
									}
									if(isset($product_report['state']) && $product_report['state'] == 'update'){
											$response['update'][] 		= sprintf($this->language->get('text_update_import_product'), $getProductEntry['name']);
									}
									$response['success_count'] = $count_success;
								}
						}
				}else{
						$response['success_count'] = $count_success;
				}
		}

		return $response;
	}

	public function getSingleEbayProduct(){
			$json = array();
			$this->load->language('ebay_map/ebay_map_product');
			$ebay_products = $sync_result = $makeItemIDs = array();
			if(isset($this->request->post['account_id']) && $this->request->post['account_id']){
					if(isset($this->request->post['ebay_item_id']) && $this->request->post['ebay_item_id']){
							$account_id = $this->request->post['account_id'];
							$separator 	= $this->request->post['separator'];
							$eBayItemId = $this->request->post['ebay_item_id'];

							if(isset($separator) && $separator){
									$makeItemIDs = explode($separator, $eBayItemId);
									if(COUNT($makeItemIDs) == 1){
											$json['error_failed'] = $this->language->get('error_wrong_separator');
									}else if(COUNT($makeItemIDs) > 10){
											$json['error_failed'] = $this->language->get('error_sync_limit');
									}
							}else{
									if(!preg_match('/^[0-9]+$/', $eBayItemId)){
											$json['error_failed'] = $this->language->get('error_wrong_separator_itemids');
									}else{
											$makeItemIDs[] = $eBayItemId;
									}
							}
							foreach($makeItemIDs as $validData){
	                   if($validData != ''){
												$arrayData[]=$validData;
										 }
							}
							$makeItemIDs = $arrayData;

							if(!$json && empty($json)){
								$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($account_id);
								if($getEbayClient){
										$getEbayAccountDetails 	= $this->Ebayconnector->_getModuleConfiguration($account_id);

										$current_date 		= new \DateTime();
										$currentDateTime	= $current_date->format('Y-m-d\TH:i:s');
																				$current_date->modify('+119 day');
										$endDateTime 			= $current_date->format('Y-m-d\TH:i:s');
										foreach ($makeItemIDs as $ebayId) {
												$item_data = [
														'Version' 					=> 849, //version
														'IncludeVariations' => true,
														'RequesterCredentials' => array(
																										'eBayAuthToken' => $getEbayAccountDetails['ebayToken'],
																									),
														'ItemID'						=> $ebayId,
														'EndTimeFrom' 			=> $currentDateTime,
														'EndTimeTo' 				=> $endDateTime,
														'DetailLevel'				=> 'ReturnAll',
												];

												$results = $getEbayClient->GetItem($item_data);

												if (isset($results->Item) && isset($results->Ack) && $results->Ack == 'Success' && isset($results->Item->Seller->UserID) && $results->Item->Seller->UserID == $getEbayAccountDetails['ebayUserId']) {
														$ebay_products[] = $results->Item;
											 	}else{
														if(!isset($results->Item) && isset($results->Ack) && $results->Ack == 'Success'){
																$json['error_failed']	= $this->language->get('error_no_item_found');
														}else if (isset($results->Item) && isset($results->Ack) && $results->Ack == 'Success' && isset($results->Item->Seller->UserID) && $results->Item->Seller->UserID != $getEbayAccountDetails['ebayUserId']) {
																$json['error'][] = sprintf($this->language->get('error_wrong_account_import'), $ebayId);
														}else{
																$json['error'][] = 'Ebay Item Id: '.$ebayId.', '.$results->Errors->LongMessage;
														}
												}
										}

										if(!isset($json['error_failed'])){
												$sync_result		= $this->start_syncronize($ebay_products, $account_id);
												if(isset($json['error'])){
														$sync_result['error'] = $json['error'];
												}
												$json = ['data' => $sync_result, 'totalPage' => 1];
										}
								}else{
										$json['error_failed'] = $this->language->get('error_ebay_configuration_wrong');
								}
							}
					}else{
							$json['error_failed'] = $this->language->get('error_ebay_itemid');
					}
			}else{
					$json['error_failed'] = $this->language->get('error_no_account_details');
			}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getTotalProductPages(){
			$this->load->language('ebay_map/ebay_map_product');

			if(isset($this->request->post['account_id']) && $this->request->post['account_id']){
				$account_id 		= $this->request->post['account_id'];
				$start_page 		= $this->request->post['page'];

				$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($account_id);

				$ebay_products 	= array();
				$totalEbayPages = 1;
					if($getEbayClient){
							$getEbayAccountDetails 	= $this->Ebayconnector->_getModuleConfiguration($account_id);

							$current_date 		= new \DateTime();
							$currentDateTime	= $current_date->format('Y-m-d\TH:i:s');
																	$current_date->modify('+119 day');
							$endDateTime 			= $current_date->format('Y-m-d\TH:i:s');

							$item_data = [
								'Version' 					=> 849, //version
								'IncludeVariations' => true,
								'UserID' 						=> $getEbayAccountDetails['ebayUserId'],
								'DetailLevel' 			=> 'ReturnAll',
								'Pagination' 				=> array(
									'EntriesPerPage'=> 10,
									'PageNumber' 		=> $start_page
								),
								'EndTimeFrom' 			=> $currentDateTime,
								'EndTimeTo' 				=> $endDateTime
							];

							$results = $getEbayClient->GetSellerList($item_data);

							if (isset($results->ItemArray->Item)) {
								if (count($results->ItemArray->Item) == 1) {
									$ebay_products = [0 => $results->ItemArray->Item];
								} else {
									$ebay_products = $results->ItemArray->Item;
								}
								$sync_result		= $this->start_syncronize($ebay_products, $account_id);
								$totalEbayPages = $results->PaginationResult->TotalNumberOfPages;
						 	} else {
								if (isset($results->Ack) && $results->Ack == 'Success') {
									$json['error_failed']	= $this->language->get('error_no_item_found');
								} else {
									$json['error_failed'] = $results->Errors->LongMessage;
								}
							}
							if(!isset($json['error_failed'])){
								$json = ['data' => $sync_result, 'totalPage' => $totalEbayPages];
							}
					}else{
						$json['error_failed'] = $this->language->get('error_ebay_configuration_wrong');
					}
			} else{
					$json['error_failed'] = $this->language->get('error_no_account_details');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
	}

	public function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_map_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function reviseEbayItem(){
		$this->load->language('ebay_map/ebay_map_product');

		$json = $product_data = $checkListing = array();
		if(isset($this->request->get['account_id']) && isset($this->request->post['product_id']) && $this->request->post['product_id']){
			$this->load->model('catalog/product');
			$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);
			if(!empty($product_info)){
					$product_data 			= $product_info;

					$product_data['product_description'] 	= $this->model_catalog_product->getProductDescriptions($this->request->post['product_id']);

					$product_specification	= $this->Ebayconnector->getProductSpecification($this->request->post['product_id']);
					$this->load->model('catalog/attribute');
					foreach ($product_specification as $key => $specification) {
		        $specification_info = $this->model_catalog_attribute->getAttribute($specification);
		        if ($specification_info) {
		            $product_data['product_specification'][] = $specification;
		          }
		      }

		      $product_condition = $this->Ebayconnector->getProductCondition($this->request->post['product_id']);

	        foreach ($product_condition as $key => $condition) {
	            $condition_info = $this->Ebayconnector->getEbayCondition($condition);
	            if (isset($condition_info) && $condition_info) {
	              $product_data['product_condition'][$key] = $condition;
	            }
	        }

          $product_data['product_variation'] = $this->Ebayconnector->_getProductVariation($this->request->post['product_id'],'product_variation');

          $product_data['product_variation_value'] = $this->Ebayconnector->_getProductVariation($this->request->post['product_id'],'product_variation_value');

          $this->load->model('catalog/category');
          $categories = $this->model_catalog_product->getProductCategories($this->request->post['product_id']);

          foreach ($categories as $category_id) {
						$category_info = $this->model_catalog_category->getCategory($category_id);

						if ($category_info) {
							$product_data['product_category'][] = $category_info['category_id'];
						}
					}

				$getMapEntry = $this->_ebayMapProduct->getProducts(array('filter_oc_product_id' => $this->request->post['product_id']));
				if(isset($getMapEntry[0]['ebay_product_id']) && $getMapEntry[0]['ebay_product_id']){

					$checkListing = $this->_ebayMapProduct->GetItem(array('ebay_product_id' => $getMapEntry[0]['ebay_product_id'], 'account_id' => $this->request->get['account_id']));

					if(isset($checkListing['error']) && $checkListing['error']){
						$json['error'] = $checkListing['message'];

					}else if(isset($checkListing['relist']) && $checkListing['relist']){

						$results = $this->_ebayMapProduct->__relistEbayItem(array('ebay_product_id' => $getMapEntry[0]['ebay_product_id'], 'account_id' => $this->request->get['account_id'], 'product_id' => $this->request->post['product_id']));

						if(isset($results['error']) && $results['error']){
							$json['error'] = $results['message'];
						}else{
							$json['success'] = $this->language->get('text_success_relisted_item');
						}
					}else{
						$json['error'] = $this->language->get('text_success_already_listed_item');
					}
				}
			}else{
				$json['error'] = $this->language->get('error_invalid_product');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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

					$results = $this->_ebayMapProduct->getProducts($filter_data);

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
			} elseif(isset($this->request->get['filter_oc_product_name'])) {
				$this->registry->set('Ebaysyncproducts', new Ebaysyncproducts($this->registry));
				$result = $this->Ebaysyncproducts->getProducts($this->request->get);
				foreach($result as $key => $value) {
					$result[$key]['name'] = html_entity_decode($value['name'], ENT_QUOTES, 'UTF-8');
				}
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($result));
			}
	}
}
