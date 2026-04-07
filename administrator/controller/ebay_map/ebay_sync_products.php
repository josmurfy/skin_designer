<?php
class ControllerEbayMapEbaySyncProducts extends Controller {
	private $error = array();

	public function __construct($registory) {
		parent::__construct($registory);
		if (!$this->config->get('ebay_connector_syncproduct_status')) {
			$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true));
		}
		$this->registry->set('Ebaysyncproducts', new Ebaysyncproducts($this->registry));
		$this->load->model('ebay_map/export_product_to_ebay');
		$this->_exportProductEbay = $this->model_ebay_map_export_product_to_ebay;
		$this->load->model('ebay_map/ebay_map_product');
		$this->_ebayMapProduct = $this->model_ebay_map_ebay_map_product;
  }

	public function index() {

		if (!$this->config->get('ebay_connector_syncproduct_status')) {
			$this->response->redirect($this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true));
		}

    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_account'));
		$data = array_merge($data, $this->load->language('ebay_map/ebay_sync_products'));
		$this->document->setTitle($data['heading_title']);

		$data['error_warning'] = "";
		$data['success'] = "";
		$data['token'] = $this->session->data['token'];
		$data['account_id'] = isset($this->request->get['account_id']) ? $this->request->get['account_id'] : 0;

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
		 'text' => $this->language->get('text_home'),
		 'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
		 'text' => $this->language->get('heading_title'),
		 'href' => $this->url->link('ebay_map/ebay_account', 'user_token=' . $this->session->data['token'], true)
		);

		if (isset($this->request->get['page'])) {
			$data['page'] = $this->request->get['page'];
		} elseif(isset($this->request->post['page'])) {
			$data['page'] = $this->request->post['page'];
		} else {
			$data['page'] = 1;
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$data['clear_product_filter'] = $this->url->link('ebay_map/ebay_sync_products', 'user_token=' . $this->session->data['token'], true);
		$data['action'] = $this->url->link('ebay_map/ebay_sync_products/setMap', 'user_token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('ebay_map/ebay_account', 'user_token=' . $data['token'], true);

		if(!isset($data['product_attributes'])) {
			$data['product_attributes'] = array();
		}

		$filter_arr = array(
		 'filter_ebay_source_product_id',
		 'filter_oc_product_id',
		 'filter_oc_product_name',
		 'filter_name',
	  );

		foreach ($filter_arr as $value) {
			if(!isset($data[$value])) {
				$data[$value] = '';
			}
		}

		$data['account_ebay_sync_products'] = $this->load->view('ebay_map/ebay_sync_products', $data);
		$data['account_ebay_sync_products_all'] = $this->load->view('ebay_map/ebay_sync_products_all', $data);
		$data['link'] = html_entity_decode($this->url->link('ebay_map/ebay_sync_products', 'user_token=' . $this->session->data['token'], true));
		$data['source_product_id'] = isset($this->request->get['source_product_id']) ? (int) $this->request->get['source_product_id'] : '';

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

		$this->response->setOutput($this->load->view('ebay_map/ebay_sync_products_page', $data));
	}

	public function setMap($data = array()) {
		$json = array();
		extract($this->request->post);

		$result = ($data && count($data)) ? $this->Ebaysyncproducts->saveMapping($data) : $this->Ebaysyncproducts->saveMapping($this->request->post);

		$json = array_merge($result ,$this->request->post);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_sync_products')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function delete($data = array()) {
		$this->load->language('ebay_map/ebay_sync_products');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $source_product_id) {
				$this->Ebaysyncproducts->deleteMapping($source_product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success_delete_category');

		}
		$this->response->redirect($this->url->link('ebay_map/ebay_sync_products', 'user_token=' . $this->session->data['token'], true));
	}

	public function getVariations($product_id, $returnVariations = false) {

		$json['variations'] = false;
		$variations = $this->Ebayconnector->_getProductVariation($product_id, 'product_variation_value');

		if( !$returnVariations ) {
			/* return hasVariation or not */
			return COUNT($variations) ? true: false;
		}

		return $variations;
	}

	public function getMap($data = array()) {

		$json = $result = array();

		extract($this->request->post);
		$json['error'] = $json['success'] = false;

		if(isset($product_id) && $product_id && isset($account_id)) {
			$json['hasVariation'] = false;
			$this->load->model('ebay_map/export_product_to_ebay');

			$accounts = $this->Ebaysyncproducts->getAccountsUsingProductId($product_id);

			$getMappEntry = $this->_exportProductEbay->getMappProductId(array('account_id' => (int)$accounts['account_id'], 'product_id' => (int)$product_id));
			if( isset($getMappEntry['ebay_product_id']) ) {
				$result = $this->Ebaysyncproducts->getOtherAccountIds($accounts['account_id']);
				$json['success'] = true;
				$json['ebay_product_id'] = $getMappEntry['ebay_product_id'];
			} else {
				$json['success'] = true;
				$result = $this->Ebaysyncproducts->getOtherAccountIds();
			}

			foreach ($result as $key => $value) {
				$json['mappedAccountIds'][] = array(
				 'id' 											 => $value['id'],
				 'ebay_connector_store_name' => $value['ebay_connector_store_name']
				);
			}

			$json = array_merge($json, $accounts);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getEbayStoreProduct() {

		$json['error'] = $json['success'] = false;
		$json = array();

		extract($this->request->post);

		$getAllEbayStoreProducts = $this->Ebaysyncproducts->getAllEbayStoreProducts((int)$account_id, $product_name);

		if(COUNT($getAllEbayStoreProducts)) {
			$json['products'] = array(
				$getAllEbayStoreProducts
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete() {

		$json = array();

		if(isset($this->request->get['account_id']) && (isset($this->request->get['filter_oc_product_name']) || isset($this->request->get['filter_category_name']))) {
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
					'filter_source_sync' 			  => '',
					'order'       => 'ASC',
					'start'       => 0,
					'limit'       => 5
				);

				$results = $this->_ebayMapProduct->getProducts($filter_data);

				foreach ($results as $result) {
						if($getFilter == 'oc_product') {
								$json[$result['oc_product_id']] = array(
								 'item_id' 		=> $result['oc_product_id'],
								 'hasVariation' 		=> $this->getVariations($result['oc_product_id']),
								 'name'        => strip_tags(html_entity_decode($result['product_name'], ENT_QUOTES, 'UTF-8'))
								);
						} else if($getFilter == 'oc_category') {
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

	public function getSingleSyncProduct() {

		$filter_product_name = isset($this->request->get['filter_product_name']) ? $this->request->get['filter_product_name'] : 1;

		$source_product_id = isset($this->request->get['source_product_id']) ? $this->request->get['source_product_id'] : 0;

		$sort = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';

		$page = isset($this->request->get['page']) ? $this->request->get['page'] : 1;

		$limit = isset($this->request->get['limit']) ? $this->request->get['limit'] : 10;

		$filter_data = array(
		 'filter_product_name' => $filter_product_name,
		 'source_product_id'   => $source_product_id,
		 'order'       				 => $sort,
		 'start'       				 => ($page - 1) * $limit,
		 'limit'       				 => $limit
		);

		$result = $this->Ebaysyncproducts->getAllSyncProducts($filter_data);

		$data['token'] = $this->session->data['token'];
		$filter_data['filter_name'] = isset($result[0]['source_product']) && isset($result['product_names']) && isset($result['product_names'][$result[0]['source_product']]) ? $result['product_names'][$result[0]['source_product']] : '';

		$result['product'] = $this->Ebaysyncproducts->getSingleSyncProduct($filter_data);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));

	}

	public function getAllSyncProductsNow() {

		$sort = isset($this->request->get['order']) ? $this->request->get['order'] : 'ASC';

		$filter_oc_product_name = isset($this->request->get['filter_oc_product_name']) ? $this->request->get['filter_oc_product_name'] : '';

		$filter_ebay_source_product_id = isset($this->request->get['filter_ebay_source_product_id']) ? (int)$this->request->get['filter_ebay_source_product_id'] : 0;

		$filter_ebay_destination_product_id = isset($this->request->get['filter_ebay_destination_product_id']) ? (int)$this->request->get['filter_ebay_destination_product_id'] : 0;

		$filter_oc_source_product_id = isset($this->request->get['filter_oc_source_product_id']) ? (int)$this->request->get['filter_oc_source_product_id'] : 0;

		$filter_oc_destination_product_id = isset($this->request->get['filter_oc_destination_product_id']) ? (int)$this->request->get['filter_oc_destination_product_id'] : 0;

		$page = isset($this->request->get['page']) ? $this->request->get['page'] : (isset($this->request->post['page']) ? $this->request->post['page'] : 1);

		$limit = isset($this->request->get['limit']) ? $this->request->get['limit'] : 10;

		$filter_data = array(
		 'filter_oc_product_name' 						=> $filter_oc_product_name,
		 'filter_ebay_destination_product_id' => $filter_ebay_destination_product_id,
		 'filter_ebay_source_product_id'  		=> $filter_ebay_source_product_id,
		 'source_product_id' 									=> $filter_oc_source_product_id,
		 'filter_oc_destination_product_id'   => $filter_oc_destination_product_id,
		 'order'       											  => $sort,
		 'start'       												=> ($page - 1) * $limit,
		 'limit'       												=> $limit
		);

		$total_sync_product = $this->Ebaysyncproducts->getTotalSyncProducts($filter_data);
		$result = $this->Ebaysyncproducts->getAllSyncProducts($filter_data);

		$pagination = new Pagination();
		$pagination->total = $total_sync_product;
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->url = $this->url->link('ebay_map/ebay_sync_products', 'user_token=' . $this->session->data['token'] . '&page={page}', true);

		$result['pagination'] = $pagination->render();

		$result['results'] = sprintf($this->language->get('text_pagination'), ($total_sync_product) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($total_sync_product - $limit)) ? $total_sync_product : ((($page - 1) * $limit) + $limit), $total_sync_product, ceil($total_sync_product / $limit));

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($result));

	}

}
