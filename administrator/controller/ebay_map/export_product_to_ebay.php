<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapExportProductToEbay extends Controller {
	private $error = array();

	private $sync_limit = 0;

	public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('ebay_map/export_product_to_ebay');
		$this->_exportProductEbay = $this->model_ebay_map_export_product_to_ebay;
    }

    public function index() {
		$data = array();
		$data = array_merge($data, $this->load->language('ebay_map/export_product_to_ebay'));

		$this->document->addScript('view/javascript/ebay_connector/webkul_ebay_connector.js');

		if(isset($this->request->get['account_id'])) {
			$account_id = $data['account_id'] = $this->request->get['account_id'];
		}else{
			$account_id = $data['account_id'] = 0;
		}

		if (isset($this->request->get['filter_oc_prod_id'])) {
			$filter_oc_prod_id = $this->request->get['filter_oc_prod_id'];
		} else {
			$filter_oc_prod_id = '';
		}

		if (isset($this->request->get['filter_oc_prod_name'])) {
			$filter_oc_prod_name = $this->request->get['filter_oc_prod_name'];
		} else {
			$filter_oc_prod_name = '';
		}

		if (isset($this->request->get['filter_oc_cat_name'])) {
			$filter_oc_cat_name = $this->request->get['filter_oc_cat_name'];
		} else {
			$filter_oc_cat_name = '';
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		$url .= '&status=account_import_to_ebay';

		if (isset($this->request->get['filter_oc_prod_id'])) {
			$url .= '&filter_oc_prod_id=' . $this->request->get['filter_oc_prod_id'];
		}

		if (isset($this->request->get['filter_oc_prod_name'])) {
			$url .= '&filter_oc_prod_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_prod_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_cat_name'])) {
			$url .= '&filter_oc_cat_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_cat_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['export_to_ebay'] = $this->url->link('ebay_map/export_product_to_ebay/edit', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('ebay_map/export_product_to_ebay/delete', 'token=' . $this->session->data['token'] . $url, true);


		$data['oc_products'] = array();

		$filter_data = array(
			'account_id'						=> $account_id,
			'filter_oc_prod_id'			=> $filter_oc_prod_id,
			'filter_oc_prod_name'		=> $filter_oc_prod_name,
			'filter_oc_cat_name' 		=> $filter_oc_cat_name,
			'filter_price'					=> $filter_price,
			'filter_quantity' 			=> $filter_quantity,
			'sort'  								=> $sort,
			'order' 								=> $order,
			'start' 								=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 								=> $this->config->get('config_limit_admin')
		);

		$ocUnmappedProductTotal = $this->_exportProductEbay->getTotalOcUnmappedProducts($filter_data);

		$results = $this->_exportProductEbay->getOcUnmappedProducts($filter_data);

		$this->load->model('catalog/product');
		$this->load->model('catalog/category');

		if($results){
			foreach ($results as $result) {

				$getProductTemplate = $this->Ebayconnector->_getProductTemplate(array('filter_product_id' => $result['product_id']));
				if (!empty($getProductTemplate)) {
						if(isset($this->request->get['account_id']) && ($this->request->get['account_id'] != $getProductTemplate['account_id'])){
							$ocUnmappedProductTotal = $ocUnmappedProductTotal - 1;
							continue;
						}
				}

				// Categories
				if (isset($result['product_id'])) {
					$categories = $this->model_catalog_product->getProductCategories($result['product_id']);
				} else {
					$categories = array();
				}

				$product_categories = array();

				foreach ($categories as $category_id) {
					$category_info = $this->model_catalog_category->getCategory($category_id);

					if ($category_info) {
						$product_categories[] = array(
							'category_id' => $category_info['category_id'],
							'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
						);
					}
				}

				$data['oc_products'][] = array(
					'product_id' 		=> $result['product_id'],
					'name' 					=> $result['name'],
					'category' 			=> $product_categories,
					'price'	 				=> $result['price'],
					'quantity'			=> $result['quantity'],
				);
			}
		}

		if(isset($this->session->data['product_export_result'])){
			$data['product_export_result'] = $this->session->data['product_export_result'];
		}else{
			$data['product_export_result'] = array();
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

		$url .= '&status=account_import_to_ebay';

		if (isset($this->request->get['filter_oc_prod_id'])) {
			$url .= '&filter_oc_prod_id=' . $this->request->get['filter_oc_prod_id'];
		}

		if (isset($this->request->get['filter_oc_prod_name'])) {
			$url .= '&filter_oc_prod_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_prod_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_cat_name'])) {
			$url .= '&filter_oc_cat_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_cat_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['account_id'])) {
			$url .= '&account_id=' . $this->request->get['account_id'];
			$data['clear_export_filter'] 	= $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] .'&account_id=' . $this->request->get['account_id']. '&status=account_import_to_ebay', true);
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_oc_cat_name'] = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&sort=name' . $url, true);
		$data['sort_ebay_category_id'] = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&sort=sort_order' . $url, true);
		$data['sort_ebay_category_name'] = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&sort=name' . $url, true);

		$url = '';

		$url .= '&status=account_import_to_ebay';

		if (isset($this->request->get['filter_oc_prod_id'])) {
			$url .= '&filter_oc_prod_id=' . $this->request->get['filter_oc_prod_id'];
		}

		if (isset($this->request->get['filter_oc_prod_name'])) {
			$url .= '&filter_oc_prod_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_prod_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_oc_cat_name'])) {
			$url .= '&filter_oc_cat_name=' . urlencode(html_entity_decode($this->request->get['filter_oc_cat_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['account_id'])) {
			$url .= '&account_id=' . $this->request->get['account_id'];
		}

		if (isset($this->request->get['sort']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order']) && (isset($this->request->get['status']) && $this->request->get['status'] == 'account_import_to_ebay')) {
			$url .= '&order=' . $this->request->get['order'];
		}
		if(isset($this->request->get['account_id'])){
			$data['redirect'] = html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . '&status=account_import_to_ebay&account_id=' .$this->request->get['account_id'] , true));
		}else{
			$data['redirect'] = html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'], true));
		}


		$pagination = new Pagination();
		$pagination->total = $ocUnmappedProductTotal;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_account/edit', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ocUnmappedProductTotal) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($ocUnmappedProductTotal - $this->config->get('config_limit_admin'))) ? $ocUnmappedProductTotal : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $ocUnmappedProductTotal, ceil($ocUnmappedProductTotal / $this->config->get('config_limit_admin')));

		$data['filter_oc_prod_id'] 		= $filter_oc_prod_id;
		$data['filter_oc_prod_name'] 	= $filter_oc_prod_name;
		$data['filter_oc_cat_name'] 	= $filter_oc_cat_name;
		$data['filter_price'] 				= $filter_price;
		$data['filter_quantity'] 			= $filter_quantity;
		$data['sort'] 	= $sort;
		$data['order'] 	= $order;

		return $this->load->view('ebay_map/export_product_to_ebay', $data);
	}


	public function export_product() {
		$final_array = $json = array();
		$this->load->language('ebay_map/export_product_to_ebay');
		unset($this->session->data['product_export_result']);
		if (isset($this->request->post['selected']) && isset($this->request->get['account_id'])) {
			$this->sync_limit 	= $this->config->get('ebay_connector_sync_record');
			$final_array = array_chunk(array_filter($this->request->post['selected']), $this->sync_limit);

			foreach ($final_array as $key => $value) {
				$count = count($value);
				$product_string = '';
				$product_string = implode(',', $value);
				$json['step'][] = array(
					'text' => sprintf($this->language->get('text_sync_process_product'),$count),
					'url'  => str_replace('&amp;', '&', $this->url->link('ebay_map/export_product_to_ebay/start_syncronize', 'token=' . $this->session->data['token'].'&account_id='.$this->request->get['account_id'], true)),
					'process_data' => $product_string,
				);
			}
		}else{
			$json['redirect'] = str_replace('&amp;', '&', $this->url->link('ebay_map/ebay_account/edit&', 'token=' . $this->session->data['token'].'&account_id='.$this->request->get['account_id'], true));
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function start_syncronize(){
		$json = array();
		$count_success 	= 0;
		$count_error 	= 0;
		$file = false;
		$product_ids = array();

		if (isset($this->request->files['export_csv_xls']) && $this->request->files['export_csv_xls']) {
			$type = $this->request->files['export_csv_xls']['type'];

			if ($type == 'application/vnd.ms-excel' || $type == 'text/csv') {
				$file = $this->request->files['export_csv_xls'];
			} else {
				$json['error'] = 'Warning: Invalid file type';
			}
			if ($file) {
				$file_path =  DIR_STORAGE . 'upload' . $file['name'];

				move_uploaded_file($file['tmp_name'], $file_path);

				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
				$sheet_data = $spreadsheet->getActiveSheet()->toArray();
				if ($sheet_data) {
					unset($sheet_data[0]);
				}
				if ($sheet_data) {
					$key = 0;
					foreach ($sheet_data as $product) {
						$product_ids[$key] = $product[0];
						$key ++;
					}
				}
			}

			$this->request->post['product_id'] = $product_ids;
		}

		if(isset($this->request->post['product_id']) && $this->request->post['product_id'] && isset($this->request->get['account_id'])) {

			$this->load->language('ebay_map/export_product_to_ebay');

			$product_ids 	= $this->request->post['product_id'];
			$account_id 	= $this->request->get['account_id'];

			$getOcProducts = $this->_exportProductEbay->getOcProducts(array('account_id' => $account_id, 'product_id' => $product_ids, 'start' => 0, 'limit' => $this->config->get('ebay_connector_sync_record')));

			if(!empty($getOcProducts) && isset($getOcProducts) && $getOcProducts){
				foreach ($getOcProducts as $key => $product) {

					if(isset($product['product_id']) && $product['product_id']){
						$getMappEntry = $this->_exportProductEbay->getMappProductId(array('account_id' => $account_id, 'product_id' => $product['product_id']));

						if(empty($getMappEntry)){
							$this->load->model('catalog/product');
							$getOcProductCategory = $this->model_catalog_product->getProductCategories($product['product_id']);

							if(!empty($getOcProductCategory) && isset($getOcProductCategory) && $getOcProductCategory){

								$getCatMappedEntry = $this->_exportProductEbay->getMappedcategory($getOcProductCategory, $account_id);

								if(!empty($getCatMappedEntry)){
									$data = array(
												'product' 			=> $product,
												'account_id' 		=> $account_id,
												'category_map'	=> $getCatMappedEntry);
									$getResult = $this->_exportProductEbay->__syncProductToEbay($data);
									if(isset($getResult['ebay_item_id']) && $getResult['ebay_item_id']){
										$json['success'][] = array(
														'success_status' 	=> 0,
														'success_message' => sprintf($this->language->get('success_export_to_ebay'), 'EbayId: '.$getResult['ebay_item_id'].', Name: '.$getResult['name']),
														);
										$count_success = $json['success']['count'] = $count_success + 1;
									}else{
											if(isset($getResult['error_status'])){
													$json['error'][] = array(
															'error_status' 	=> 1,
															'error_message' => $getResult['error_message']);
											}
									}
								}else{
									$json['error'][] = array(
											'error_status' 	=> 1,
											'error_message' => sprintf($this->language->get('error_category_not_mapped'), $product['name']));
								}
							}else{
								$json['error'][] = array(
											'error_status' 	=> 1,
											'error_message' => sprintf($this->language->get('error_category_not_assign'), $product['name']));
							}// check product has oc category
						}else{
							$json['error'][] = array(
											'error_status' 	=> 1,
											'error_message' => sprintf($this->language->get('error_already_mapped_product'), $product['name']));
						}// check map entry
					}else{
						$json['error'][] = array(
									'error_status' 	=> 1,
									'error_message' => sprintf($this->language->get('error_invalid_product'), $product['name']));
					}// product id check
				}// end loop
			}else{
				$json['error'][] = array(
									'error_status' 	=> 1,
									'error_message' => $this->language->get('error_no_product_to_sync'));
			}
		}

		if(isset($json['error']) && $json['error']){
			$json['error']['count'] = $this->sync_limit - $count_success;
			foreach ($json['error'] as $key => $error) {
				if($key !== 'count'){
					$this->session->data['product_export_result']['error'][] = $error;
				}
			}
		}
		if(isset($json['success']) && $json['success']){
			$this->session->data['success'] = $this->language->get('text_success_ebay_export');
			foreach ($json['success'] as $key => $success) {
				if($key !== 'count'){
					$this->session->data['product_export_result']['success'][] = $success;
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete(){
		$json = array();

			if(isset($this->request->get['account_id']) && (isset($this->request->get['filter_oc_prod_name']) || isset($this->request->get['filter_oc_cat_name']))){
					$getFilter = '';
					if(isset($this->request->get['filter_oc_prod_name'])){
						$getFilter = 'oc_product';
						$oc_product = $this->request->get['filter_oc_prod_name'];
					}else{
						$oc_product = '';
					}

					if(isset($this->request->get['filter_oc_cat_name'])){
						$getFilter = 'oc_category';
						$oc_category = $this->request->get['filter_oc_cat_name'];
					}else{
						$oc_category = '';
					}

					$filter_data = array(
						'account_id' 							=> $this->request->get['account_id'],
						'filter_oc_prod_name' 		=> $oc_product,
						'filter_oc_cat_name' 			=> $oc_category,
						'order'       => 'ASC',
						'start'       => 0,
						'limit'       => 5
					);

					$results = $this->_exportProductEbay->getOcUnmappedProducts($filter_data);

					foreach ($results as $result) {
							if($getFilter == 'oc_product'){
									$json[$result['product_id']] = array(
										'item_id' 		=> $result['product_id'],
										'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
									);
							}else if($getFilter == 'oc_category'){
									$json[$result['category_id']] = array(
										'item_id' 		=> $result['category_id'],
										'name'        => strip_tags(html_entity_decode($result['category_name'], ENT_QUOTES, 'UTF-8'))
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
