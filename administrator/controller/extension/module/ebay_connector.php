<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerExtensionModuleEbayConnector extends Controller {

	private $error = array();

	public static $_importEbayCategories = array();

	private $post_fields = array(
		'ebay_connector_ebay_sites',
		'ebay_connector_ebay_mode',
		'ebay_connector_ebay_user_id',
		'ebay_connector_ebay_auth_token',
		'ebay_connector_ebay_application_id',
		'ebay_connector_ebay_developer_id',
		'ebay_connector_ebay_certification_id',
		'ebay_connector_ebay_shop_postal_code',
		);

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('extension/module/ebay_connector');
		$this->_ebayModuleEbayConnector = $this->model_extension_module_ebay_connector;

		$this->load->model('ebay_map/ebay_category_import');
		$this->_ebayCategoryImport = $this->model_ebay_map_ebay_category_import;

		$this->load->model('ebay_map/ebay_map_category');
		$this->_ebayCategoryMap = $this->model_ebay_map_ebay_map_category;

		$this->load->language('extension/module/ebay_connector');
    }

	public function install(){
		$this->_ebayModuleEbayConnector->createTables();
		$this->_ebayModuleEbayConnector->__addVariationOption();
		$this->load->model('user/user_group');
		$controllers = array(
			'ebay_map/ebay_account',
			'ebay_map/ebay_category_list',
			'ebay_map/ebay_specification_list',
			'ebay_map/ebay_condition_list',
			'ebay_map/ebay_map_category',
			'ebay_map/ebay_map_product',
			'ebay_map/ebay_map_order',
			'ebay_map/export_product_to_ebay',
		);

		foreach ($controllers as $key => $controller) {
			$this->model_user_user_group->addPermission($this->user->getId(),'access',$controller);
			$this->model_user_user_group->addPermission($this->user->getId(),'modify',$controller);
		}
	}

	public function index() {

		$data = array();

		$data = $this->load->language('extension/module/ebay_connector');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			/**
			 * [if change in the price rule option field]
			 * @var [type]
			 */

     if(isset($this->request->post['ebay_connector_price_rules']) && !is_null($this->request->post['ebay_connector_price_rules']) && is_null($this->config->get('ebay_connector_price_rules'))){
			 $status_flag = true;
			 $db_value = 1;
			 $post_value= 0;
		 } else if(!isset($this->request->post['ebay_connector_price_rules']) && !is_null($this->config->get('ebay_connector_price_rules'))){
			 $db_value = 0;
			 $post_value = 1;
			 $status_flag = true;
		 } else {
			 $status_flag = false;
		 }

			if ($status_flag) {
         $this->load->model('price_rule/rule_setting');
				 $this->model_price_rule_rule_setting->managePriceRulesSetting($db_value, $post_value);
			}

		if (!$this->config->get('ebay_connector_default_category')) {
				 $this->request->post['ebay_connector_default_category'] = $this->createOtherCategory();
		 } else {
			 $this->request->post['ebay_connector_default_category'] = $this->config->get('ebay_connector_default_category');
		 }



			/**
			 * [if change in the price rule option field]
			 * @var [type]
			 */
			$this->model_setting_setting->editSetting('ebay_connector', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		$data['text_sync_process_category'] = sprintf($this->language->get('text_sync_process_category'), $this->config->get('ebay_connector_category_row') ? $this->config->get('ebay_connector_category_row') : 100);

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
			'href' => $this->url->link('extension/module/ebay_connector', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/module/ebay_connector', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if(!file_exists(DIR_APPLICATION.'../vendor/autoload.php')){
 		  $data['is_installed_composer'] = $this->language->get('error_install_composer');
 	  } else {
 		  $data['is_installed_composer'] = false;
 	  }

		$post_data = array(
			'ebay_connector_status',
			//general tab
			'ebay_connector_ebay_sites',
			'ebay_connector_ebay_mode',
			'ebay_connector_ebay_user_id',
			'ebay_connector_ebay_auth_token',
			'ebay_connector_ebay_application_id',
			'ebay_connector_ebay_developer_id',
			'ebay_connector_ebay_certification_id',
			'ebay_connector_ebay_shop_postal_code',

			'ebay_connector_category_row',

			'ebay_connector_default_item_quantity',
			'ebay_connector_product_tax',
			'ebay_connector_default_category',
			'ebay_connector_account_delete',
			'ebay_connector_product_delete',

			'ebay_connector_revise_ebayitem',
			'ebay_connector_return_policy',
			'ebay_connector_return_days',
			'ebay_connector_pay_by',
			'ebay_connector_other_info',

			'ebay_connector_listing_duration',
			'ebay_connector_ebay_item_delete',
			'ebay_connector_oc_product_delete',
			'ebay_connector_dispatch_time',

			'ebay_connector_paypal_email',

			'ebay_connector_shipping_priority',
			'ebay_connector_shipping_service',
			'ebay_connector_shipping_service_cost',
			'ebay_connector_shipping_service_add_cost',
			'ebay_connector_shipping_min_time',
			'ebay_connector_shipping_max_time',
			'ebay_connector_shipping_free_status',

			'ebay_connector_ordersync_store',
			'ebay_connector_order_status',
			'ebay_connector_sync_record',
			'ebay_connector_price_rules',
			'ebay_connector_realtime_sync',
			'ebay_connector_ebay_item_delete_quantity',

			'ebay_connector_syncproduct_status',
			);

		foreach ($post_data as $key => $post) {
			if (isset($this->request->post[$post])) {
				$data[$post] = $this->request->post[$post];
			} else {
				$data[$post] = $this->config->get($post);
			}
		}

   $data['category_import_result']  = $this->ocwebkul->_manageSessionVariable('category_import_result',$default=array());

   $data['invalid_warning']         = $this->ocwebkul->_manageSessionVariable('invalid_warning',$default= '');

	 $data['success']                 = $this->ocwebkul->_manageSessionVariable('success',$default= '');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}



		foreach ($this->post_fields as $key => $error_value) {
			$data['error_'.$error_value] = (isset($this->error['error_'.$error_value])  && $error_value != 'ebay_connector_ebay_sites' && $error_value != 'ebay_connector_ebay_mode') ?  $this->error['error_'.$error_value] : '';
		}

		$data['ebay_sites'] = array(
			0 	=> 'eBay United States',
			2 	=> 'eBay Canada (English)',
			3 	=> 'eBay UK',
			15 	=> 'eBay Australia',
			16 	=> 'eBay Austria',
			23 	=> 'eBay Belgium (French)',
			71 	=> 'eBay France',
			77 	=> 'eBay Germany',
			186 => 'eBay Spain',
			193 => 'eBay Switzerland',
			100 => 'eBay Motors',
			101 => 'eBay Italy',
			123 => 'eBay Belgium (Dutch)',
			146 => 'eBay Netherlands',
			201 => 'eBay Hong Kong',
			203 => 'eBay India',
			205 => 'eBay Ireland',
			207 => 'eBay Malaysia',
			210 => 'eBay Canada (French)',
			211 => 'eBay Philippines',
			212 => 'eBay Poland',
			216 => 'eBay Singapore',
		);

		$data['return_days'] = array(
								array('value' => 'Days_14','name' => $this->language->get('text_days_14')),
                       			array('value' => 'Days_30','name' => $this->language->get('text_days_30')),
                       			array('value' => 'Days_60','name' => $this->language->get('text_days_60')),
                           		);

		$data['pay_by'] = array(
								array('value' => 'Buyer','name' => $this->language->get('text_pay_buyer')),
                				array('value' => 'Seller','name' => $this->language->get('text_pay_seller'))
                				);
		$data['listing_duration'] = array(
			 					array('value' => 'GTC','name' => $this->language->get('text_good_cancel')),
	                            array('value' => 'Days_1','name' => $this->language->get('text_day_1')),
	                            array('value' => 'Days_3','name' => $this->language->get('text_day_3')),
	                            array('value' => 'Days_5','name' => $this->language->get('text_day_5')),
	                            array('value' => 'Days_7','name' => $this->language->get('text_day_7')),
	                            array('value' => 'Days_10','name' => $this->language->get('text_day_10')),
	                            array('value' => 'Days_30','name' => $this->language->get('text_day_30'))
	                            );

		$data['dispatch_time'] = array(
									array('value' => '0','name' => $this->language->get('text_days_0')),
		                            array('value' => '1','name' => $this->language->get('text_days_1')),
		                            array('value' => '2','name' => $this->language->get('text_days_2')),
		                            array('value' => '3','name' => $this->language->get('text_days_3')),
		                            array('value' => '4','name' => $this->language->get('text_days_4')),
		                            array('value' => '5','name' => $this->language->get('text_days_5')),
		                            array('value' => '10','name' => $this->language->get('text_days_10')),
		                            array('value' => '15','name' => $this->language->get('text_days_15')),
		                            array('value' => '20','name' => $this->language->get('text_days_20')),
		                            array('value' => '30','name' => $this->language->get('text_days_30')),
		                            );

		$data['getOcParentCategory'] = $this->_ebayCategoryMap->get_OpencartCategories(array());

		$this->load->model('localisation/order_status');
		$data['order_status'] = $this->model_localisation_order_status->getOrderStatuses();

		$data['shipping_services'] = array(
										array('value' => 'Other', 'name' => $this->language->get('text_eco_shipping')),
										array('value' => 'UK_OtherCourier', 'name' => $this->language->get('text_uk_shipping')),
										array('value' => 'DE_Pickup', 'name' => $this->language->get('text_pickup_shipping')),

										);

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$data['token'] =  $this->session->data['token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/ebay_connector', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/ebay_connector')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		//post fields blank check
		foreach ($this->post_fields as $key => $post_value) {
			if(empty($this->request->post[$post_value]) && $post_value != 'ebay_connector_ebay_sites' && $post_value != 'ebay_connector_ebay_mode'){
				$this->error['error_'.$post_value]	=	$this->language->get('error_'.$post_value);
				$this->error['warning'] = $this->language->get('error_field_required');
			}
		}

		return !$this->error;
	}

	public function _importEbayCategories($ebay_AccountId = false){
		$json 						= array();
		$this->load->language('extension/module/ebay_connector');
		$getConfig 				= $this->Ebayconnector->_getModuleConfiguration($ebay_AccountId);
		$getEbayClient 		= $this->Ebayconnector->_eBayAuthSession($ebay_AccountId);

		if($getEbayClient){
				$params = [
									'Version' 				=> 853,
									'SiteID' 					=> 0,
									'CategorySiteID' 	=> $getConfig['ebaySites'],
									'LevelLimit' 			=> 5,
									'ViewAllNodes' 		=> true,
									'DetailLevel' 		=> 'ReturnAll',
                    ];
				$results = $getEbayClient->GetCategories($params);

				if(isset($results->CategoryArray->Category) && $results->CategoryArray->Category){
						$ebay_category = $results->CategoryArray->Category;
          	$total_category = $results->CategoryCount;

						$getTotalCategory	= $this->_ebayCategoryImport->saveEbayCategoryData(array('data' => $ebay_category, 'count' => $total_category));

						if(isset($getTotalCategory['count']) && $getTotalCategory['count']){

								$total_row 	= $getTotalCategory['count']/$this->config->get('ebay_connector_category_row');
								$total_row 	= ceil($total_row);
								$json['totalcategory'] = $total_row;
								$json['success'] 			 = sprintf($this->language->get('text_total_category_found'), $getTotalCategory['count']);
						}
        }else{
						$json['error'] = $this->session->data['invalid_warning'] = $this->language->get('error_invalid_details');
        		$json['redirect'] = str_replace('&amp;', '&', $this->url->link('extension/module/ebay_connector', 'token=' . $this->session->data['token'], true));
        }
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function start_syncronize(){
		$json = array();
		$count_success 	= 0;
		$count_error 	= 0;

		if(isset($this->request->post['page']) && $this->request->post['page']){
			$this->load->language('extension/module/ebay_connector');
			$filter_data = array(
					'page' => $this->request->post['page'],
					'limit' => $this->config->get('ebay_connector_category_row'),
			);
			$getOcProducts	= $this->_ebayCategoryImport->import_ebay_category($filter_data);

			if(!empty($getOcProducts)){
				if($getOcProducts['success']){
						$json['success_msg'] 			= sprintf($this->language->get('text_success_import_category'), $getOcProducts['success']);
						$json['success_count'] 		= $getOcProducts['success'];
				}
				if($getOcProducts['already']){
						$json['success_already_msg'] 	= sprintf($this->language->get('text_already_import_category'), $getOcProducts['already']);
						$json['success_already'] 	= $getOcProducts['already'];
				}
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function createOtherCategory() {
		$category_data = array(
			'path' 						=> '',
			'parent_id' 			=> 0,
			'filter' 					=> '',
			'keyword' 				=> 'Default-Category',
			'image' 					=> '',
			'top' 						=> 1,
			'column' 					=> 1,
			'sort_order' 			=> 0,
			'status' 					=> 1,
			'category_layout' => array(),
		);

		$this->language->load('localisation/language');
		$this->load->model('localisation/language');
		$getAllLanguage = $this->model_localisation_language->getLanguages();

		foreach ($getAllLanguage as $key => $language) {
			$category_data['category_description'][$language['language_id']] = array(
				'name' 							=> 'Default Category',
				'description' 			=> '<p>Default Category<br></p>',
				'meta_title' 				=> 'default category',
				'meta_description' 	=> '',
				'meta_keyword' 			=> ''
			);
		}

		$category_data['category_store'] = array(0);
		
		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $key => $store) {
			$category_data['category_store'] = array([0] => $store['store_id'] ? $store['store_id'] : 0);
		}

		$this->load->model('catalog/category');
		$stores = $this->model_catalog_category->addCategory($category_data);
	}

	public function uninstall(){
		$this->load->model('catalog/category');
		$this->model_catalog_category->deleteCategory($this->config->get('ebay_connector_default_category'));
		$this->_ebayModuleEbayConnector->removeTables();
		$this->_ebayModuleEbayConnector->removeVariationOption();

	}
}
