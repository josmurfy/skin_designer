<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapEbayMapOrder extends Controller {
	private $error = array();

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('ebay_map/ebay_map_order');
		$this->_ebayMapOrder = $this->model_ebay_map_ebay_map_order;
    }

    public function index() {
		$data = array();
		$data = array_merge($data, $this->load->language('ebay_map/ebay_map_order'));

		$this->document->addScript('view/javascript/ebay_connector/webkul_ebay_connector.js');

		if(isset($this->request->get['account_id'])) {
			$account_id = $data['account_id'] = $this->request->get['account_id'];
		}else{
			$account_id = $data['account_id'] = 0;
		}

		if (isset($this->request->get['filter_oc_order_id'])) {
			$filter_oc_order_id = $this->request->get['filter_oc_order_id'];
		} else {
			$filter_oc_order_id = '';
		}

		if (isset($this->request->get['filter_ebay_order_id'])) {
			$filter_ebay_order_id = $this->request->get['filter_ebay_order_id'];
		} else {
			$filter_ebay_order_id = '';
		}

		if (isset($this->request->get['filter_order_total'])) {
			$filter_order_total = $this->request->get['filter_order_total'];
		} else {
			$filter_order_total = null;
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = '';
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		$url .= '&status=account_order_map';

		if (isset($this->request->get['filter_oc_order_id'])) {
			$url .= '&filter_oc_order_id=' . $this->request->get['filter_oc_order_id'];
		}

		if (isset($this->request->get['filter_ebay_order_id'])) {
			$url .= '&filter_ebay_order_id=' . $this->request->get['filter_ebay_order_id'];
		}

		if (isset($this->request->get['filter_order_total'])) {
			$url .= '&filter_order_total=' . $this->request->get['filter_order_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . urlencode(html_entity_decode($this->request->get['filter_order_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['export_to_ebay'] = $this->url->link('ebay_map/ebay_map_order/edit', 'user_token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('ebay_map/ebay_map_order/delete', 'user_token=' . $this->session->data['token'] . $url, true);


		$data['map_orders'] = array();

		$filter_data = array(
			'filter_account_id'		=> $account_id,
			'filter_oc_order_id'	=> $filter_oc_order_id,
			'filter_ebay_order_id'=> $filter_ebay_order_id,
			'filter_order_total'	=> $filter_order_total,
			'filter_date_added'		=> $filter_date_added,
			'filter_order_status'	=> $filter_order_status,
			'sort'  		=> $sort,
			'order' 		=> $order,
			'start' 		=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 		=> $this->config->get('config_limit_admin')
		);

		$order_mapped = $this->_ebayMapOrder->getTotalEbayOrderMap($filter_data);

		$results = $this->_ebayMapOrder->getEbayOrderMap($filter_data);

		if($results){
			foreach ($results as $result) {
				$data['map_orders'][] = array(
					'map_id' 			=> $result['id'],
					'ebay_order_id' 	=> $result['ebay_order_id'],
					'oc_order_id' 		=> $result['oc_order_id'],
					'ebay_order_status' => $result['ebay_order_status'],
					'order_total'	 	=> $result['total'],
					'created_date'		=> $result['date_added'],
				);
			}
		}

		if(isset($this->session->data['order_import_result'])){
			$data['order_import_result'] = $this->session->data['order_import_result'];
		}else{
			$data['order_import_result'] = array();
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

		$url .= '&status=account_order_map';

		if (isset($this->request->get['filter_oc_order_id'])) {
			$url .= '&filter_oc_order_id=' . $this->request->get['filter_oc_order_id'];
		}

		if (isset($this->request->get['filter_ebay_order_id'])) {
			$url .= '&filter_ebay_order_id=' . $this->request->get['filter_ebay_order_id'];
		}

		if (isset($this->request->get['filter_order_total'])) {
			$url .= '&filter_order_total=' . $this->request->get['filter_order_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . urlencode(html_entity_decode($this->request->get['filter_order_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['account_id'])) {
			$url .= '&account_id=' . $this->request->get['account_id'];
			$data['clear_order_filter'] 	= $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] .'&account_id=' . $this->request->get['account_id']. '&status=account_order_map', true);
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_oc_cat_name'] = $this->url->link('ebay_map/ebay_map_order', 'user_token=' . $this->session->data['token'] . '&sort=name' . $url, true);
		$data['sort_ebay_category_id'] = $this->url->link('ebay_map/ebay_map_order', 'user_token=' . $this->session->data['token'] . '&sort=sort_order' . $url, true);
		$data['sort_ebay_category_name'] = $this->url->link('ebay_map/ebay_map_order', 'user_token=' . $this->session->data['token'] . '&sort=name' . $url, true);

		$url = '';

		$url .= '&status=account_order_map';

		if (isset($this->request->get['filter_oc_order_id'])) {
			$url .= '&filter_oc_order_id=' . $this->request->get['filter_oc_order_id'];
		}

		if (isset($this->request->get['filter_ebay_order_id'])) {
			$url .= '&filter_ebay_order_id=' . $this->request->get['filter_ebay_order_id'];
		}

		if (isset($this->request->get['filter_order_total'])) {
			$url .= '&filter_order_total=' . $this->request->get['filter_order_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . urlencode(html_entity_decode($this->request->get['filter_order_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['account_id'])) {
			$url .= '&account_id=' . $this->request->get['account_id'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_order_map')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if(isset($this->request->get['account_id'])){
			$data['redirect'] 	= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&status=account_order_map&account_id=' .$this->request->get['account_id'] , true));
		}else{
			$data['redirect'] 	= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'], true));
		}

		$pagination = new Pagination();
		$pagination->total = $order_mapped;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_mapped) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_mapped - $this->config->get('config_limit_admin'))) ? $order_mapped : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_mapped, ceil($order_mapped / $this->config->get('config_limit_admin')));

		$data['filter_oc_order_id'] 		= $filter_oc_order_id;
		$data['filter_ebay_order_id'] 	= $filter_ebay_order_id;
		$data['filter_order_total'] 		= $filter_order_total;
		$data['filter_date_added'] 			= $filter_date_added;
		$data['filter_order_status'] 		= $filter_order_status;
		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('ebay_map/ebay_map_order', $data);
	}

	public function getSingleEbayOrder(){
			$json = array();
			$this->load->language('ebay_map/ebay_map_order');
			$ebay_orders = $sync_result = array();
			if(isset($this->request->post['account_id']) && $this->request->post['account_id']){
					if(isset($this->request->post['ebay_order_id']) && $this->request->post['ebay_order_id']){
							$account_id 			= $this->request->post['account_id'];
							$eBayOrderId 			= $this->request->post['ebay_order_id'];

							// check eBay Order already synchronized or not
							$checkOrderEntry 	= $this->_ebayMapOrder->getEbayOrderMap(array('filter_ebay_order_id' => $eBayOrderId));

							if(!empty($checkOrderEntry) && isset($checkOrderEntry[0]['ebay_order_id']) && $checkOrderEntry[0]['ebay_order_id'] == $eBayOrderId){
								$json['update'][] = array(
												'error_status'  => 1,
												'error_message' => sprintf($this->language->get('error_alreadysync'), $checkOrderEntry[0]['ebay_order_id']).' '.$checkOrderEntry[0]['oc_order_id'].'.',
												);
							}
							if(!isset($json['update'])){
									$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($account_id);
									if($getEbayClient){
											$getEbayAccountDetails 			= $this->Ebayconnector->_getModuleConfiguration($account_id);
											$current_date 							= new \DateTime();
											$currentDateTime						= $current_date->format('Y-m-d\TH:i:s');
																										$current_date->modify('+119 day');
											$endDateTime 								= $current_date->format('Y-m-d\TH:i:s');
											$params = [	'Version' 			=> 1039,
			                    				'DetailLevel' 	=> 'ReturnAll',
																	'OrderIDArray'	=> array(
																								 				'OrderID' 	=> $eBayOrderId,
																											),
																	'Pagination' 	=> [
			                            			'EntriesPerPage' => '1',
			                            			'PageNumber' => (1)
			                        			],
			                					];
			                $results = $getEbayClient->GetOrders($params);

											if (isset($results->OrderArray->Order) && isset($results->OrderArray->Order->SellerUserID) && $results->OrderArray->Order->SellerUserID == $getEbayAccountDetails['ebayUserId']) {
													$ebay_orders 		= json_decode(json_encode($results->OrderArray->Order),true);
													$getSyncResult 	= $this->_ebayMapOrder->__mapEbayOrder($ebay_orders, $account_id);

	                    		if(isset($getSyncResult['success'])){
	                    			$json['success'][] = array(
																			'success_status'  => 1,
																			'success_message' => $getSyncResult['success']['success_message']
																			);
	                    		}
	                    		if(isset($getSyncResult['error'])){
	                    			$json['error'][] = array(
																		'error_status'  => 1,
																		'error_message' => $getSyncResult['error']['error_message']
																		);
	                    		}
											}else{
														if (isset($results->Ack) && $results->Ack != 'Success') {
				                    	$json['error'][] = array(
																			'error_status'  => 1,
																			'error_message' => $results->Errors->LongMessage
																			);
														}else if (isset($results->OrderArray->Order) && isset($results->OrderArray->Order->SellerUserID) && $results->OrderArray->Order->SellerUserID != $getEbayAccountDetails['ebayUserId']) {
 														 $json['error'][] = array(
 																		 'error_status'  => 1,
 																		 'error_message' => sprintf($this->language->get('text_sync_process_order'), $eBayOrderId),
 																		 );
 													 }
											}
									}
							}
					}else{
							$json['error_failed'] = $this->language->get('error_ebay_orderid');
					}
			}else{
					$json['error_failed'] = $this->language->get('error_no_account_details');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
	}

	public function import_order() {
		$final_array = $json = array();
		$this->load->language('ebay_map/ebay_map_order');
		unset($this->session->data['order_import_result']);
		if (isset($this->request->get['account_id'])) {
			$sync_limit = $this->config->get('ebay_connector_sync_record');

			$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($this->request->get['account_id']);
			if($getEbayClient){
				$datetime 		= new \DateTime();
                $currentDate 	= $datetime->format('Y-m-d\TH:i:s');
                				  $datetime->modify('-90 day');
                $endTime 		= $datetime->format('Y-m-d\TH:i:s');
                $pageNumber = 0;
                do {
                    $pagenumber = $pageNumber ? $pageNumber + 1 : 1;
                    /****/
                    $params = [	'Version' 		=> 1039,
                        		'DetailLevel' 	=> 'ReturnAll',
                        		'Pagination' 	=> [
                            			'EntriesPerPage' => '100',
                            			'PageNumber' => ($pagenumber ? $pagenumber : 1)
                        			],
                        		'CreateTimeFrom'=> $endTime,
                        		'CreateTimeTo' 	=> $currentDate,
                        		'OrderStatus' 	=> 'Completed',
                    		];

                    $results = $getEbayClient->GetOrders($params);

										$count_array = $ebay_orders_array = array();
			              if (isset($results->OrderArray->Order)) {
											if(isset($results->OrderArray->Order->OrderID)){
													$ebay_orders_array = array($results->OrderArray->Order);
											}else{
													$ebay_orders_array = $results->OrderArray->Order;
											}
											foreach ($ebay_orders_array as $key => $order) {
												$count_array[$key] = $order->OrderID;
											}

                    	$final_array = array_chunk(array_filter($count_array), $sync_limit);

												foreach ($final_array as $key => $value) {
													$count = count($value);
													$product_string = '';
													$product_string = implode(',', $value);
													$json['step'][] = array(
														'text' 							=> sprintf($this->language->get('text_sync_process_order'),$count),
														'url'  							=> str_replace('&amp;', '&', $this->url->link('ebay_map/ebay_map_order/start_syncronize', 'user_token=' . $this->session->data['token'].'&account_id='.$this->request->get['account_id'], true)),
														'process_data' 			=> $product_string,
														'page_no' 					=> $pagenumber,
														'order_count'				=> $count,
													);
												}
                        $pageNumber = (int) $results->PageNumber;
                    } else {
                        if (isset($results->Ack) && $results->Ack != 'Success') {
                        	$json['error'] = array(
																'error_status'  => 1,
																'error_message' => $results->Errors->LongMessage
																);
                            break;
												}else if(isset($results->ReturnedOrderCountActual) && $results->ReturnedOrderCountActual == 0){
														$json['error'] = array(
																		'error_status'  => 1,
																		'error_message' => $this->language->get('error_no_order_found'),
																		);
                            break;
												}
                    }
                } while ($results->ReturnedOrderCountActual == 100);
			}else{
				$json['error'] = array(
						'error_status'  => 1,
						'error_message' => $this->language->get('error_no_account_details')
						);
			}
		}else{
            $json['error'] = array(
					'error_status'  => 1,
					'error_message' => $this->language->get('error_invalid_request'));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function start_syncronize(){
		$json = $session_data = array();
		$count_success 	= 0;
		$count_error 	= 0;

		if(isset($this->request->post['order_id']) && $this->request->post['order_id'] && isset($this->request->get['account_id'])){

			$this->load->language('ebay_map/ebay_map_order');
			$order_ids 	= array();
			$order_ids 	= explode(',', $this->request->post['order_id']);
			$page_no	= $this->request->post['page_no'];
			$account_id = $this->request->get['account_id'];
			$sync_limit = count($order_ids);

			foreach ($order_ids as $key => $order_id) {
				$checkOrderEntry = $this->_ebayMapOrder->getEbayOrderMap(array('filter_ebay_order_id' => $order_id));
				if(!empty($checkOrderEntry) && isset($checkOrderEntry[0])){
					$json['error'][] = array(
									'error_status'  => 1,
									'error_message' => sprintf($this->language->get('error_alreadysync'), $checkOrderEntry[0]['ebay_order_id'])
									);
					unset($order_ids[$key]);
					continue;
				}
			}

			$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($account_id);
			if($getEbayClient){
				$datetime 		= new \DateTime();
                $currentDate 	= $datetime->format('Y-m-d\TH:i:s');
                				  $datetime->modify('-90 day');
                $endTime 		= $datetime->format('Y-m-d\TH:i:s');
                $params = [	'Version' 		=> 1039,
                    		'DetailLevel' 	=> 'ReturnAll',
                    		'Pagination' 	=> [
                        			'EntriesPerPage' => '100',
                        			'PageNumber' => ($page_no ? $page_no : 1)
                    			],
                    		'CreateTimeFrom'=> $endTime,
                    		'CreateTimeTo' 	=> $currentDate,
                    		'OrderStatus' 	=> 'Completed',
                		];

                $results = $getEbayClient->GetOrders($params);

                $count_array = array();
                if (isset($results->OrderArray->Order)) {
                    $eBayOrders = json_decode(
                        				json_encode($results->OrderArray->Order),
                        				true
                    			);
                    $eBayOrders 	= isset($eBayOrders[0]) ? $eBayOrders : [0 => $eBayOrders];

                    foreach ($eBayOrders as $key => $order) {
                    	$getSyncResult = array();
                    	if(is_array($order_ids) && in_array($order['OrderID'], $order_ids)){
                    		$getSyncResult 	= $this->_ebayMapOrder->__mapEbayOrder($order, $account_id);
                    		if(isset($getSyncResult['success'])){
                    			$json['success'][] = array(
																		'success_status'  => 1,
																		'success_message' => $getSyncResult['success']['success_message']
																		);
                    			$count_success = $count_success + 1;
                    		}
                    		if(isset($getSyncResult['error'])){
                    			$json['error'][] = array(
																	'error_status'  => 1,
																	'error_message' => $getSyncResult['error']['error_message']
																	);

                    		}
                    	}
                    }
                } else {
                    if (isset($results->Ack) && $results->Ack != 'Success') {
                    	$json['error'][] = array(
															'error_status'  => 1,
															'error_message' => $results->Errors->LongMessage
															);
                        // break;
                    }
                }
			}else{
					$json['error'][] = array(
										'error_status'  => 1,
										'error_message' => $this->language->get('error_no_account_details')
										);

				}
		}

		if(isset($json['error']) && $json['error']){
			foreach ($json['error'] as $key => $error) {
				$this->session->data['order_import_result']['error'][] = $error;
			}
		}
		if(isset($json['success']) && $json['success']){
			$this->session->data['success'] = $this->language->get('text_success_order_sync');
			foreach ($json['success'] as $key => $success) {
				$this->session->data['order_import_result']['success'][] = $success;
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function deleteMapOrder() {
		$json = array();
		$this->load->language('ebay_map/ebay_map_order');

		if (isset($this->request->post['selected']) && $this->validateDelete() && $this->request->get['account_id']) {
			foreach ($this->request->post['selected'] as $oc_order_id) {
				$result = $this->_ebayMapOrder->deleteMapOrder($oc_order_id);
			}
			if($result)
				$json['success']				= $this->language->get('text_success_order_delete');
				$this->session->data['success'] = $this->language->get('text_success_order_delete');
				$json['redirect'] 				= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&status=account_order_map&account_id=' .$this->request->get['account_id'] , true));
		}
		if(isset($this->error['warning'])){
			$json['error_permission']	=	$this->language->get('error_permission');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_map_order')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
