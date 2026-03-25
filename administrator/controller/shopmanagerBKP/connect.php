<?php
/**
 * @version [3.0.0.0] [Supported opencart version 3.x.x.x]
 * @category Webkul
 * @package Marketplace eBay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html 
 */
class ControllerShopmanagerConnect extends Controller {
	private $error = array();
	private $post_fields = array(
		'store_name',
		'sites',
		'user_id',
		'auth_token',
		'application_id',
		'developer_id',
		'certification_id',
		'shop_postal_code',
		'currency'
		);

  public function index() {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
  	$this->load->language('shopmanager/connect');

		$this->getList();
	}

	public function delete() {
		$this->load->language('shopmanager/connect');
		$this->load->model('shopmanager/marketplace');
		//$this->checkStatus();
		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['delete_marketplace_account_id']) && $this->validateDelete()) {
		//	foreach ($this->request->post['selected'] as $marketplace_account_id) {
				$this->model_shopmanager_marketplace->deleteAccount($this->request->post['delete_marketplace_account_id']);
		//	}
//echo $this->request->post['delete_marketplace_account_id'];
			$this->session->data['success'] = $this->language->get('text_success');

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

			$this->response->redirect($this->url->link('shopmanager/connect', $url, true));
		}

		$this->getList();
	}
	public function add_Account() {

		//$this->checkStatus('shopmanager/account/add_Account');
		//$this->load->model('shopmanager/dashboard');
		//$this->load->model('shopmanager/account');
		$data =$this->language->load('shopmanager/connect');

		
        $url = '';
       // echo "allo331";
		$this->document->setTitle($this->language->get('heading_title_add'));
		$this->response->setOutput($this->load->view('shopmanager/connect_form.tpl', $data));
	
	} 
	protected function getList() {
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		//$this->checkStatus();
		$this->load->model('tool/image');
		$data = $this->load->language('shopmanager/connect');
		$data['ebay']=$this->model_tool_image->resize('catalog/marketplace/ebay.png',150, 40);
		$data['amazon']=$this->model_tool_image->resize('catalog/marketplace/amazon.png',150, 40);
		$data['bonanza']=$this->model_tool_image->resize('catalog/marketplace/bonanza.png',150, 40);
		$data['etsy']=$this->model_tool_image->resize('catalog/marketplace/etsy.png',150, 40);
		$data['opencart']=$this->model_tool_image->resize('catalog/marketplace/opencart.png',150, 40);
		$data['shipstation']=$this->model_tool_image->resize('catalog/marketplace/shipstation.png',150, 40);
		$data['shopify']=$this->model_tool_image->resize('catalog/marketplace/shopify.png',150, 40);

		$this->document->setTitle($this->language->get('heading_title_add'));

		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$filter_marketplace_account_id = $this->request->get['filter_marketplace_account_id'];
		} else {
			$filter_marketplace_account_id = null;
		}

		if (isset($this->request->get['filter_store_name'])) {
			$filter_store_name = $this->request->get['filter_store_name'];
		} else {
			$filter_store_name = null;
		}

		if (isset($this->request->get['filter_user_id'])) {
			$filter_user_id = $this->request->get['filter_user_id'];
		} else {
			$filter_user_id = null;
		}
		if (isset($this->request->get['filter_marketplace_id'])) {
			$filter_marketplace_id = $this->request->get['filter_marketplace_id'];
		} else {
			$filter_marketplace_id = null;
		}
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

		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$url .= '&filter_marketplace_account_id=' . $this->request->get['filter_marketplace_account_id'];
		}

		if (isset($this->request->get['filter_store_name'])) {
			$url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . urlencode(html_entity_decode($this->request->get['filter_user_id'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_marketplace_id'])) {
			$url .= '&filter_marketplace_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_id'], ENT_QUOTES, 'UTF-8'));
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/shopmanager/shopmanager/dashboard', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shopmanager/connect', $url, true)
		);
		$data['button_add_ebay_account'] =$this->language->get('button_add_ebay_account');
		$data['button_add_amazon_account'] =$this->language->get('button_add_amazon_account');
		$data['button_add_bonanza_account'] =$this->language->get('button_add_bonanza_account');
		$data['button_add_opencart_account'] =$this->language->get('button_add_opencart_account');
		$data['button_add_etsy_account'] =$this->language->get('button_add_etsy_account');
		$data['button_add_shipstation_account'] =$this->language->get('button_add_shipstation_account');
		$data['button_add_shopify_account'] =$this->language->get('button_add_shopify_account');
		$data['add_ebay_account'] = $this->url->link('shopmanager/ebay/add&api=yes', $url, true);
		$data['add_account'] = $this->url->link('shopmanager/connect/add_account', $url, true);
		$data['add_amazon_account'] = $this->url->link('shopmanager/amazon/add&api=yes', $url, true);
		$data['add_bonanza_account'] = $this->url->link('shopmanager/bonanza/add&api=yes', $url, true);
		$data['add_opencart_account'] = $this->url->link('shopmanager/opencart/add&api=yes', $url, true);
		$data['add_shipstation_account'] =$this->language->get('shopmanager/shipstation/add&api=yes');
		$data['add_shopify_account'] = $this->url->link('shopmanager/shopify/add&api=yes', $url, true);
		$data['add_etsy_account'] = $this->url->link('shopmanager/etsy/add&api=yes', $url, true);

		$data['delete'] = $this->url->link('shopmanager/connect/delete', $url, true);

		$data['clear_filter'] = $this->url->link('shopmanager/connect', '', true);

		$data['marketplace_accounts'] = array();

		$filter_data = array(
			'customer_id'					=>10,
			'filter_marketplace_account_id'	  => $filter_marketplace_account_id,
			'filter_store_name'	  => $filter_store_name,
			'filter_user_id'	=> $filter_user_id,
			'filter_marketplace_id'	=> $filter_marketplace_id,
			'sort'  							=> $sort,
			'order' 							=> $order,
			'start' 							=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'				 				=> $this->config->get('config_limit_admin')
		);

		$this->load->model('shopmanager/marketplace');

		$marketplace_total_account = $this->model_shopmanager_marketplace->getTotalMarketplaceAccount($filter_data);

		$results = $this->model_shopmanager_marketplace->getMarketplaceAccount($filter_data);

/* 		$sites = $this->Ebayconnector->getEbaySiteList();
		$ebay_sites = $result['marketplace_image']; */
		//print("<pre>".print_r ($results,true )."</pre>");
		$this->load->model('tool/image');
		
		if ($results) {
			foreach ($results as $result) {
				if($result['site_setting']!=""){
					$site_setting= array();
				//	//print("<pre>".print_r ($result,true )."</pre>");
				
					$site_setting=$result['site_setting'];
				//	//print("<pre>".print_r ($site_setting,true )."</pre>");
				}
				if($result['marketplace_id']==8){
					$dataresult = array (
						'SS-UserName' => $result['user_id'],
						'SS-Password' => $result['auth_token'],
						'url'	=> $site_setting->url,
						'version'	=> $site_setting->version
					);
					$this->load->model('shopmanager/opencart');
					//$result['image']=$this->model_tool_image->resize($site_setting->url."/image/".$site_setting->config_image,40, 40);;
					$result['image']=$this->model_tool_image->resize($result['image'],150, 40);

					$status=$this->model_shopmanager_opencart->getStatus($dataresult);
					$marketplace_status_image=$status['status_image'];
				}else{
					/*
					$result["marketplace_name"]=str_replace('Canada','',$result["marketplace_name"]);
					//print("<pre>".print_r ($result["marketplace_name"],true )."</pre>");
					$this->load->model(
						"shopmanager/" . strtolower($result["marketplace_name"]) . ""
					);
					
					$marketplace_status_image=$this->{"model_shopmanager_" .strtolower($result["marketplace_name"])}->getStatus($result);
					*/ 
					$marketplace_status_image= '<i class="fas fa-check-circle fa-2x" style="color:green"></i>';
					//$result['image'] = '<i class="fas fa-check-circle fa-2x" style="color:green"></i>';
				//	$result['image']=$this->model_tool_image->resize($result['image'],150, 40);
				}
			/*	}else{
					$marketplace_status_image="";
					$result['image']=$this->model_tool_image->resize($result['image'],150, 40);
				}*/
				
				$data['marketplace_accounts'][] = array(
					'marketplace_account_id' 		=> $result['marketplace_account_id'],
					'store_name'    		=> $result['store_name'],
					'marketplace_image'     => $result['marketplace_image'],
					'marketplace_name'     => $result['name'],
					'marketplace_user_id'  => $result['user_id'],
					'url_connexion'  		=> $result['url_connexion'],
					'marketplace_status_image'  => $marketplace_status_image,
					'edit'  				=> $this->url->link('shopmanager/connect/edit', 'marketplace_account_id=' . $result['marketplace_account_id'] . $url, true),
				);
			}
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

		$url = '';

		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$url .= '&filter_marketplace_account_id=' . $this->request->get['filter_marketplace_account_id'];
		}

		if (isset($this->request->get['filter_store_name'])) {
			$url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . urlencode(html_entity_decode($this->request->get['filter_user_id'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_marketplace_id'])) {
			$url .= '&filter_marketplace_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_id'], ENT_QUOTES, 'UTF-8'));
		}
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_marketplace_account_id'] = $this->url->link('shopmanager/connect', 'sort=marketplace_account_id' . $url, true);
		$data['sort_marketplace_store_name'] = $this->url->link('shopmanager/connect',  'sort=store_name' . $url, true);
		$data['sort_marketplace_user_id'] = $this->url->link('shopmanager/connect',  'sort=user_id' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$url .= '&filter_marketplace_account_id=' . $this->request->get['filter_marketplace_account_id'];
		}

		if (isset($this->request->get['filter_store_name'])) {
			$url .= '&filter_store_name=' . urlencode(html_entity_decode($this->request->get['filter_store_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . urlencode(html_entity_decode($this->request->get['filter_user_id'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_marketplace_id'])) {
			$url .= '&filter_marketplace_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_id'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $marketplace_total_account;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('shopmanager/connect', $url . '&page={page}', '', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($marketplace_total_account) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($marketplace_total_account - $this->config->get('config_limit_admin'))) ? $marketplace_total_account : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $marketplace_total_account, ceil($marketplace_total_account / $this->config->get('config_limit_admin')));

		$data['filter_marketplace_account_id'] 	= $filter_marketplace_account_id;
		$data['filter_store_name'] 	= $filter_store_name;
		$data['filter_user_id']= $filter_user_id;
		$data['filter_marketplace_id']= $filter_marketplace_id;

		$data['sort'] = $sort;
		$data['order'] = strtolower($order);

		  $data['separate_view'] = true;
		  

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');

		 $this->response->setOutput($this->load->view('shopmanager/connect_list', $data));
		
	}	
	
	public function edit() {

		//$this->checkStatus();
		$data = array();
		$data = array_merge($data, $this->load->language('shopmanager/connect'));

		$this->document->setTitle($this->language->get('heading_title_edit'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

			$result=$this->model_shopmanager_marketplace->addMarketplaceAccount($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_'.$result);

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

			$this->response->redirect($this->url->link('shopmanager/connect', $url, true));
		}

		$this->getForm();
	}

	public function getForm() {
		$data = $this->load->language('shopmanager/connect');
		$this->load->model('shopmanager/marketplace');

		$this->document->setTitle($this->language->get('text_add'));

		if (isset($this->request->get['marketplace_account_id'])) {
			$this->document->setTitle($this->language->get('heading_title_edit'));
			$data['text_account_tab'] = $this->language->get('text_account_edit_tab');
		} else {
			$this->document->setTitle($this->language->get('heading_title_add'));
			$data['text_account_tab'] = $this->language->get('text_account_add_tab');
		}

		//$ebay_sites = $this->Ebayconnector->getEbaySiteList();

	//	$data['ebay_sites'] = $ebay_sites['ebay_sites'];

	//	$data['ebay_site_currency'] = $this->Ebayconnector->getEbaySiteCurrency();
 
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
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('shopmanager/dashboard', '', true)
		);

/* 		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shopmanager/connect', $url, true)
		);

		if (isset($this->request->get['marketplace_account_id'])) {
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title_edit'),
			'href' => $this->url->link('shopmanager/connect/edit', 'marketplace_account_id=' . $this->request->get['marketplace_account_id'] . $url, true)
		);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title_add'),
				'href' => $this->url->link('shopmanager/connect/add', $url, true)
			);
		}

		if (!isset($this->request->get['marketplace_account_id'])) {
			$data['action'] = $this->url->link('shopmanager/connect/add', $url, true);
		} else {
			$data['action'] = $this->url->link('shopmanager/connect/edit', 'marketplace_account_id=' . $this->request->get['marketplace_account_id'] . $url, true);
		} */

		$data['cancel'] = $this->url->link('shopmanager/connect', '' . $url,  true);

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

		foreach ($this->post_fields as $key => $error_site_setting) {
			if (isset($this->error['error_' . $error_site_setting])  && $error_site_setting != 'sites') {
				$data['error_' . $error_site_setting] = $this->error['error_' . $error_site_setting];
			} else {
				$data['error_' . $error_site_setting] = '';
			}
		}

	/* 	if (isset($this->error['permission'])) {
			$data['error_permission'] = $this->error['permission'];
		} else {
			$data['error_permission'] = '';
		}

		if (isset($this->error['error_ebay_store_exist'])) {
			$data['error_ebay_store_exist'] = $this->error['error_ebay_store_exist'];
		} else {
			$data['error_ebay_store_exist'] = '';
		} */
		if (isset($this->request->get['marketplace_account_id'])) {
			$data['marketplace_account_id'] = $this->request->get['marketplace_account_id'];
		}else{
			$data['marketplace_account_id'] = '';
		}

	//	$shipping_info = array();
		$account_info = array();

		if (isset($this->request->get['marketplace_account_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$account_info = $this->model_shopmanager_marketplace->getMarketplaceAccount(array('filter_marketplace_account_id' => $this->request->get['marketplace_account_id']), false);
		//	$shipping_info = $this->model_shopmanager_marketplace->getShippingDetails($this->request->get['marketplace_account_id']);
		}

		foreach ($this->post_fields as $key => $post_site_setting) {
			if (isset($this->request->post[$post_site_setting])) {
				$data[$post_site_setting] = $this->request->post[$post_site_setting];
			} elseif (!empty($account_info[0])) {
				$data[$post_site_setting] = $account_info[0][$post_site_setting];
			} else {
				$data[$post_site_setting] = '';
			}
		}

/* 		$shipping_details = array(
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
 */
		// eBay Connector Menu

	//	$data['wkebay_link'] = array();
	//	$data['wkebay_menu'] = array();

		
/* 		 $this->load->language('account/wkebay/ocmod');

				$data['wkebay_menu']['account'] =  $this->language->get('text_ebay_account');
				$data['wkebay_link']['account'] = $this->url->link('shopmanager/connect', '', true);

				$data['wkebay_menu']['template_listing'] = $this->language->get('text_ebay_template_listing');
				$data['wkebay_link']['template_listing'] = $this->url->link('account/wkebay/template_listing', '', true);

				$data['wkebay_menu']['price_qty_rule'] = $this->language->get('text_price_quantity_rule');
				$data['wkebay_link']['price_qty_rule'] = $this->url->link('account/wkebay/price_qty_rule', '', true);

				$data['wkebay_menu']['seller_category'] = $this->language->get('text_seller_category');
				$data['wkebay_link']['seller_category'] = $this->url->link('account/wkebay/seller_category', '', true);

				$data['wkebay_menu']['category_list'] = $this->language->get('text_ebay_category_list');
				$data['wkebay_link']['category_list'] = $this->url->link('account/wkebay/category_list', '', true);

				$data['wkebay_menu']['condition_list'] = $this->language->get('text_ebay_condition_list');
				$data['wkebay_link']['condition_list'] = $this->url->link('account/wkebay/condition_list', '', true);

				$data['wkebay_menu']['specification_list'] = $this->language->get('text_ebay_specification_list');
				$data['wkebay_link']['specification_list'] = $this->url->link('account/wkebay/specification_list', '', true);

				$data['wkebay_menu']['product_data'] = $this->language->get('text_product_data_mapping');
				$data['wkebay_link']['product_data'] = $this->url->link('account/wkebay/product_data', '', true);
 */

		if ($account_info) {
			$data['account_info'] = true;
		//	$data['category_map'] = $this->load->controller('account/wkebay/map_category');
		//	$data['product_map'] = $this->load->controller('account/wkebay/map_product');
		//	$data['export_product'] = $this->load->controller('account/wkebay/export_product_to_ebay');
			// $data['schedule_product'] = $this->load->controller('account/wkebay/schedule_product');
		//	$data['schedule_product'] = '';
		//	$data['order_map'] = $this->load->controller('account/wkebay/map_order');
		} else {
			$data['account_info'] = false;
			$data['category_map'] = '';
			$data['product_map'] = '';
			$data['export_product'] = '';
			$data['schedule_product'] = '';
			$data['order_map'] = '';
		}

		

		  
		  $data['header'] = $this->load->controller('common/header');
		  $data['column_left'] = $this->load->controller('common/column_left');
		  $data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');


		 $this->response->setOutput($this->load->view('default/template/shopmanager/connect_form.tpl', $data));


	}

	/* protected function validateAccount() {
		if (!$this->wkebay->hasPermission('modify', 'account')) {
			$this->error['permission'] = $this->language->get('error_permission');
		} 
		//echo "allo838<br>";
		$this->load->model('shopmanager/marketplace');

		//post fields blank check
		foreach ($this->post_fields as $key => $post_site_setting) {
			if (empty($this->request->post[$post_site_setting]) && $post_site_setting != 'sites') {
				//echo "allo844<br>";
				$this->error['error_'.$post_site_setting]	=	$this->language->get('error_'.$post_site_setting);
				$this->error['warning'] = $this->language->get('error_field_required');
			}
		}

		if (isset($this->request->post['store_name']) && $this->request->post['store_name'] && !isset($this->request->get['marketplace_account_id'])) {
			$ebay_account = $this->model_shopmanager_marketplace->getMarketplaceAccount(array('filter_store_name' => $this->request->post['store_name']), true);
			//echo "allo852<br>";
			if (isset($ebay_account[0]['marketplace_account_id']) && $ebay_account[0]['marketplace_account_id']) {
				//echo "allo854<br>";
				$this->error['error_ebay_store_exist'] = $this->language->get('error_ebay_store_exist');
			}
		}
		//print_r($this->error);
		if (!$this->error) {
				$ebay_client = $this->Ebayconnector->_eBayAuthSession($this->request->post);
				//echo "allo861<br>";
				if ($ebay_client) {
					//echo "allo863<br>";
					//echo $this->request->post['user_id'];
					//print_r($ebay_client)."alloallo";
						$current_date 		= new \DateTime();
						$current_datetime	= $current_date->format('Y-m-d\TH:i:s');
						$current_date->modify('+119 day');
						$end_datetime 		= $current_date->format('Y-m-d\TH:i:s');

						$item_data = [
							'Version' 					=> 849, //version
							'IncludeVariations' => false,
							'UserID' 						=> $this->request->post['user_id'],
							'DetailLevel' 			=> 'ItemReturnDescription',
							'Pagination' 				=> array(
								'EntriesPerPage'	=> 1,
								'PageNumber' 			=> 1
							),
							'EndTimeFrom' 			=> $current_datetime,
							'EndTimeTo' 				=> $end_datetime
						];

						$results = $ebay_client->GetSellerList($item_data);
						//print_r($results);
						if (!isset($results->Ack)) {
							$this->error['warning'] = $this->language->get('error_wrong_account_details');
						}
				}
		}

		return !$this->error;
	}
 */
	public function autocomplete() {
		$json = array();
		if (isset($this->request->post['filter_store_name'])) {
			$filter_data = array(
				'filter_store_name' => $this->request->post['filter_store_name'],
				'limit'							=> 8
			);

			$results = $this->model_shopmanager_marketplace->getMarketplaceAccount($filter_data);
			if ($results) {
				foreach ($results as $result) {
					$json['marketplace_accounts'][] = array(
						'marketplace_account_id' 		=> $result['marketplace_account_id'],
						'store_name'    => $result['store_name'],
						'ebay_user_id'  => $result['user_id']
					);
				}
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateDelete() {
/* 		if (!$this->wkebay->hasPermission('delete', 'account')) {
			$this->error['warning'] = $this->language->get('error_permission_delete');
		} */

		return !$this->error;
	}

	protected function checkStatus() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('shopmanager/connect', '', true);
			$this->response->redirect($this->url->link('shopmanager/account/login', '', true));
		}

	}
}
