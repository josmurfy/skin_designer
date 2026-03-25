<?php
class ControllerShopmanagerProduct extends Controller {
	private $error = array();

	public function index() {
		ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
		$this->load->language('shopmanager/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('shopmanager/product');
		$this->load->model('shopmanager/tools');


        
        $this->document->addScript('view/javascript/shopmanager/ai.js');
		$this->document->addScript('view/javascript/shopmanager/translate.js');
		$this->document->addScript('view/javascript/shopmanager/tools.js');
		$this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
		$this->document->addScript('view/javascript/shopmanager/alert_popup.js');
		$this->document->addScript('view/javascript/shopmanager/ebay.js');
	

		$this->getList();
	}
 
	public function add() {
		$this->load->language('shopmanager/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('shopmanager/product'); 

		if (($this->request->server['REQUEST_METHOD'] == 'POST') ) {// && $this->validateForm()
			$product_id=$this->model_shopmanager_product->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($product_id)) {
				$url .= '&product_id=' . urlencode(html_entity_decode($product_id, ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_name_length'])) {
				$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
			}
	
			if (isset($this->request->get['filter_invalid_price'])) {
				$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
			}

			
			if (isset($this->request->get['filter_made_in_country_id'])) {
				$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
			}
			

			if (isset($this->request->get['filter_marketplace_account_id'])) {
				$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_marketplace'])) {
				$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

            if (isset($this->request->get['filter_unallocated_quantity'])) {
				$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
			}

            if (isset($this->request->get['filter_location'])) {
				$url .= '&filter_location=' . $this->request->get['filter_location'];
			}
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_specifics'])) {
				$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
			}
	
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
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

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			if (isset($this->session->data['error'])) {
				$this->getForm();
				
			} else {
				unset($this->session->data['error']);
				$this->response->redirect($this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . $url, true));

			}
		}

		$this->getForm();
	}

	public function edit() {
	    ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
	//print("<pre>".print_r ($this->request->post,true )."</pre>");
		$this->load->language('shopmanager/product');
		$this->load->model('shopmanager/marketplace');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('shopmanager/product');
	//	//print("<pre>".print_r ($this->request->post,true )."</pre>");
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm() ) { //&& (!isset($this->request->get['bypass']))
		//	//print("<pre>".print_r ($this->request->post,true )."</pre>");
		//	die(); 

		  
			$this->model_shopmanager_product->editProduct($this->request->get['product_id'], $this->request->post);
			//die();
	//		if(isset($this->request->post['marketplace_item_id']) && $this->request->post['marketplace_item_id']>0 && $this->request->post['marketplace_item_id']!=''){
			//	$this->load->model('shopmanager/ebay');
				//$updquantity=$this->request->post['quantity']+$this->request->post['unallocated_quantity'];

				$marketplace_accounts_id = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $this->request->get['product_id']]);

				foreach($marketplace_accounts_id as $marketplace_account_id=> $marketplace_account){
					if(isset($marketplace_account['marketplace_item_id'])){
						$result=$this->model_shopmanager_marketplace->editToMarketplace($this->request->get['product_id'], $marketplace_account_id);
				
						if (isset($result['Errors']) && $result['Ack'] == 'Failure') {
							$this->error['error'] = "eBay ERROR: <br>";

							$errors = new RecursiveIteratorIterator(new RecursiveArrayIterator($result['Errors']));
							foreach ($errors as $key => $value) {
								if ($key === 'LongMessage') {
									$this->error['error'] .= '************ ' . $value . '<br>';
								}
							}

							$this->session->data['error'] = $this->error['error'];
							$this->session->data['error'] = $this->error['error'];

						}else{
							unset($result['Errors']);
						}
					}
				}
				//print("<pre>".print_r ($marketplace_accounts_id,true )."</pre>");


//die();
		//	}
			
				$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name_length'])) {
				$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
			}
	
			if (isset($this->request->get['filter_invalid_price'])) {
				$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_made_in_country_id'])) {
				$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
			}

			
			if (isset($this->request->get['filter_marketplace_account_id'])) {
				$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_marketplace'])) {
				$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

            if (isset($this->request->get['filter_unallocated_quantity'])) {
				$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
			}

            if (isset($this->request->get['filter_location'])) {
				$url .= '&filter_location=' . $this->request->get['filter_location'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_specifics'])) {
				$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
			}
	
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
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

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->response->redirect($this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true));

			
		}

			$this->getForm();

	
	
	}



	public function delete() {

		
		$this->load->language('shopmanager/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('shopmanager/product');
		$this->load->model('shopmanager/tools');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
		//print("<pre>".print_r ($this->request->post['selected'] ,true )."</pre>");
		//	echo count($this->request->post['selected']);$this->load->model('shopmanager/tools');
		$this->model_shopmanager_tools->debug_function_trace();
        // 🛠️ Vérification initiale
        if (empty($jsonData) || empty($target_language)) {
            return ['error' => 'Invalid input data'];
        }
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_shopmanager_product->deleteProduct($product_id);
				$this->model_shopmanager_tools->deleteProductImagesFiles($product_id);
				
				
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name_length'])) {
				$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
			}
	
			if (isset($this->request->get['filter_invalid_price'])) {
				$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_made_in_country_id'])) {
				$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_marketplace_account_id'])) {
				$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_marketplace'])) {
				$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

            if (isset($this->request->get['filter_unallocated_quantity'])) {
				$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
			}

            if (isset($this->request->get['filter_location'])) {
				$url .= '&filter_location=' . $this->request->get['filter_location'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_specifics'])) {
				$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
			}
	
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
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

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->response->redirect($this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}
	public function enable() {
		$this->load->language('shopmanager/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('shopmanager/product');

		if (isset($this->request->post['selected'])) {
		//print("<pre>".print_r ($this->request->post['selected'] ,true )."</pre>");
		//	echo count($this->request->post['selected']);
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_shopmanager_product->enableProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name_length'])) {
				$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
			}
	
			if (isset($this->request->get['filter_invalid_price'])) {
				$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_made_in_country_id'])) {
				$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_marketplace_account_id'])) {
				$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_marketplace'])) {
				$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

            if (isset($this->request->get['filter_unallocated_quantity'])) {
				$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
			}

            if (isset($this->request->get['filter_location'])) {
				$url .= '&filter_location=' . $this->request->get['filter_location'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_specifics'])) {
				$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
			}
	

			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
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

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->response->redirect($this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}
	public function disable() {
		$this->load->language('shopmanager/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('shopmanager/product');

		if (isset($this->request->post['selected'])) {
		//print("<pre>".print_r ($this->request->post['selected'] ,true )."</pre>");
		//	echo count($this->request->post['selected']);
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_shopmanager_product->enableProduct($product_id,0);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name_length'])) {
				$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
			}
	
			if (isset($this->request->get['filter_invalid_price'])) {
				$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_made_in_country_id'])) {
				$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
			}

			
			if (isset($this->request->get['filter_marketplace_account_id'])) {
				$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_marketplace'])) {
				$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

            if (isset($this->request->get['filter_unallocated_quantity'])) {
				$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
			}

            if (isset($this->request->get['filter_location'])) {
				$url .= '&filter_location=' . $this->request->get['filter_location'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_specifics'])) {
				$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
			}
	
			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
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

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->response->redirect($this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}
	public function copy() {
		$this->load->language('shopmanager/product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('shopmanager/product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_shopmanager_product->copyProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name_length'])) {
				$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
			}
	
			if (isset($this->request->get['filter_invalid_price'])) {
				$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_sku'])) {
				$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_product_id'])) {
				$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_made_in_country_id'])) {
				$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
			}

			
			if (isset($this->request->get['filter_marketplace_account_id'])) {
				$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_marketplace'])) {
				$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
			}
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}
            if (isset($this->request->get['filter_unallocated_quantity'])) {
                $url .=  '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
            }
            if (isset($this->request->get['filter_location'])) {
                $url .=  '&filter_location=' . $this->request->get['filter_location']; 
            } 
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_specifics'])) {
				$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
			}
	

			if (isset($this->request->get['filter_image'])) {
				$url .= '&filter_image=' . $this->request->get['filter_image'];
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

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->response->redirect($this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {

		ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
		//phpinfo();
		$this->document->addScript('view/javascript/shopmanager/product_list.js');
		
		$filter_name_length = isset($this->request->get['filter_name_length']) ? $this->request->get['filter_name_length'] : '';
		$filter_invalid_price = isset($this->request->get['filter_invalid_price']) ? $this->request->get['filter_invalid_price'] : '';
		
		if (isset($this->request->get['filter_sku'])) {
			$filter_sku = $this->request->get['filter_sku'];
		}else {
			$filter_sku = null;
		}

		if (isset($this->request->get['filter_product_id'])) {
			$filter_product_id = $this->request->get['filter_product_id'];
		}else {
			$filter_product_id = null;
		}
		if (isset($this->request->get['filter_made_in_country_id'])) {
			$filter_made_in_country_id = $this->request->get['filter_made_in_country_id'];
		}else {
			$filter_made_in_country_id = null;
		}
		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$filter_marketplace_account_id = $this->request->get['filter_marketplace_account_id'];
		} else {
			$filter_marketplace_account_id = null;
		}
		if (isset($this->request->get['filter_marketplace'])) {
			$filter_marketplace = $this->request->get['filter_marketplace'];
		} else {
			$filter_marketplace = null;
		}
	
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
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
        if (isset($this->request->get['filter_unallocated_quantity'])) {
			$filter_unallocated_quantity = $this->request->get['filter_unallocated_quantity'];
		} else {
			$filter_unallocated_quantity = null;
		}
        if (isset($this->request->get['filter_location'])) {
			$filter_location = $this->request->get['filter_location'];
		} else {
			$filter_location = null;
		}
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}

		if (isset($this->request->get['filter_specifics'])) {
			$filter_specifics = $this->request->get['filter_specifics'];
		} else {
			$filter_specifics = null;
		}

		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = null;
		}
	
	 

		if (isset($this->request->get['filter_sources'])) {
			$filter_sources = $this->request->get['filter_sources'];
		} else {
			$filter_sources = null;
		}


		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$filter_marketplace_account_id = $this->request->get['filter_marketplace_account_id'];
		} else {
			$filter_marketplace_account_id = null;
		}

		if (isset($this->request->get['filter_marketplace'])) {
			$filter_marketplace = $this->request->get['filter_marketplace'];
		} else {
			$filter_marketplace = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 20;
		}

		$filters_to_handle = $this->request->get;

		$has_zero_filter = (
			(isset($filters_to_handle['filter_status']) && $filters_to_handle['filter_status'] == 0) ||
			(isset($filters_to_handle['filter_specifics']) && $filters_to_handle['filter_specifics'] == 0) ||
			(isset($filters_to_handle['filter_sources']) && $filters_to_handle['filter_sources'] == 0) ||
			(isset($filters_to_handle['filter_image']) && $filters_to_handle['filter_image'] == 0) ||
			(isset($filters_to_handle['filter_name_length']) && $filters_to_handle['filter_name_length'] == 1) ||
			(isset($filters_to_handle['filter_invalid_price']) && $filters_to_handle['filter_invalid_price'] == 1)
		);
		
		$data['list_button_action'] = $has_zero_filter ? "handleFeedList();" : "handleList();";
		$data['list_button_title']  = $has_zero_filter
			? $this->language->get('text_feed_all_products')
			: $this->language->get('text_list_all_products');
		

		$url = '';

		if (isset($this->request->get['filter_name_length'])) {
			$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_invalid_price'])) {
			$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_made_in_country_id'])) {
			$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace'])) {
			$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

        if (isset($this->request->get['filter_unallocated_quantity'])) {
			$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
		}

        if (isset($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . $this->request->get['filter_location'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_specifics'])) {
			$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
		}

		if (isset($this->request->get['filter_sources'])) {
			$url .= '&filter_sources=' . $this->request->get['filter_sources'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
			$data['order'] = $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])){
			$url .= '&limit=' . $this->request->get['limit'];
			$data['limit'] = $this->request->get['limit'];
		}else{
			$url .= '&limit=20';
			$data['limit'] = 20;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true)
		);

		
		$data['print_report'] = $this->url->link('shopmanager/print_report', 'type_report=product&token=' . $this->session->data['token'] . $url, true);
		$data['add'] = $this->url->link('shopmanager/product/add', 'token=' . $this->session->data['token'] . $url, true);
		$data['copy'] = $this->url->link('shopmanager/product/copy', 'token=' . $this->session->data['token'] . $url, true);
		$data['delete'] = $this->url->link('shopmanager/product/delete', 'token=' . $this->session->data['token'] . $url, true);
		$data['enable'] = $this->url->link('shopmanager/product/enable', 'token=' . $this->session->data['token'] . $url, true);
		$data['disable'] = $this->url->link('shopmanager/product/disable', 'token=' . $this->session->data['token'] . $url, true);
		
		$data['form'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true);

		$data['products'] = array();
		
		$filter_data = array(
			'filter_sku'	  => $filter_sku,
			'filter_product_id'	  => $filter_product_id,
			'filter_made_in_country_id'	  => $filter_made_in_country_id,
			'filter_marketplace_account_id'	  => $filter_marketplace_account_id,
			'filter_marketplace'	  => $filter_marketplace,
			'filter_name'	  => $filter_name,
			'filter_category_id'	  => $filter_category_id,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
            'filter_unallocated_quantity' => $filter_unallocated_quantity,
            'filter_location' => $filter_location,
			'filter_status'   => $filter_status,
			'filter_image'    => $filter_image,
			'filter_specifics' => $filter_specifics,
			'filter_sources' => $filter_sources,
			'filter_name_length'     => $filter_name_length,
			'filter_invalid_price'   => $filter_invalid_price,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit //$this->config->get('config_limit_admin')
		); 

		$this->load->model('shopmanager/localisation/country');

		$data['countries'] = $this->model_shopmanager_localisation_country->getCountries(array('sort'=>'name'));
		//print("<pre>".print_r ($data['countries'] ,true )."</pre>");

		$this->load->model('tool/image');
	
		
	//	//print("<pre>".print_r ($data,true )."</pre>");
		//print("<pre>".print_r ($filter_data,true )."</pre>");
	 
		$product_total = $this->model_shopmanager_product->getTotalProducts($filter_data);

		$this->load->model('shopmanager/marketplace');
		$data['marketplace_accounts']= $this->model_shopmanager_marketplace->getMarketplaceAccount(['customer_id' => 10 ]);
//echo '958:::::';
		//print("<pre>".print_r ($data['marketplace_accounts'],true )."</pre>");

		$results = $this->model_shopmanager_product->getProducts($filter_data);
		//print("<pre>".print_r ($results,true )."</pre>");
		$upc_search='';
		if($product_total==0){
			$upc_search='&upc='.(string)$filter_sku;
		}
			
		$data['product_search'] = $this->url->link('shopmanager/product_search', 'token=' . $this->session->data['token']. $upc_search, true); 
		
		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 100, 100);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 100, 100);
			}

			$special = false;

			$product_specials = $this->model_shopmanager_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}
	//print("<pre>".print_r ($result['marketplace_accounts_id'] ,true )."</pre>");
		$this->load->model('shopmanager/catalog/category');
		foreach ($result['marketplace_accounts_id'] as $marketplace_id => $item) 
		
		{
				if (!is_array($item) || !isset($item['image'])) {
					continue; // Vérifie que $item est bien un tableau et contient 'image'
				}
		
				// Récupérer le chemin et le nom de l'image
				$imagePath = pathinfo($item['image'], PATHINFO_DIRNAME);
				$imageName = pathinfo($item['image'], PATHINFO_FILENAME);
				$imageExt = pathinfo($item['image'], PATHINFO_EXTENSION);
		
				// Vérifier et appliquer les modifications
				if (!empty($item['error'])) {
					$newImage = "{$imagePath}/{$imageName}_red.{$imageExt}";
				} elseif (empty($item['marketplace_item_id'])) {
					$newImage = "{$imagePath}/{$imageName}_grey.{$imageExt}";
				} else {
					$newImage = "{$imagePath}/{$imageName}_green.{$imageExt}"; // Image par défaut
				}
				//print("<pre>".print_r($newImage, true)."</pre>");
				// Vérifier que la classe model_tool_image existe avant d'appeler resize
			
					$result['marketplace_accounts_id'][$marketplace_id]['thumb'] = $this->model_tool_image->resize($newImage, 25,25);
				
			
		}
		
		// Affichage pour vérifier que thumb est bien ajouté
	//	echo "<pre>DEBUG: Structure de \$result['marketplace_accounts_id'] après modification :</pre>";
	//	print_r($result['marketplace_accounts_id']);
		/*
	$data['ebay'] = [
		'default' => $this->model_tool_image->resize('catalog/marketplace/ebay_ca.png', 1000, 1000),
		'green'   => $this->model_tool_image->resize('catalog/marketplace/ebay_ca_green.png', 1000, 1000),
		'red'     => $this->model_tool_image->resize('catalog/marketplace/ebay_ca_red.png', 1000, 1000),
		'grey'    => $this->model_tool_image->resize('catalog/marketplace/ebay_ca_grey.png', 1000, 1000),
	];
	
		*/
	if($result['price']<0 && count($results)==1){
		if (isset($result['upc']) && is_numeric($result['upc'])) { 
			$ProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources($result['upc']);
			//print("<pre>".print_r ('1120 product' ,true )."</pre>");
			//print("<pre>".print_r ($ProductInfoSources ,true )."</pre>");

			$data['ebay_pricevariant']=json_decode($ProductInfoSources['ebay_pricevariant']?? '[]', true);
		}elseif(isset($result['product_id'])){
			$ProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources(null,null,$result['product_id']);

			$data['ebay_pricevariant']=json_decode($ProductInfoSources['ebay_pricevariant']?? '[]', true);
		} 
		//print("<pre>".print_r ($data['ebay_pricevariant'] ,true )."</pre>");
		if (isset($data['ebay_pricevariant'][$result['condition_id']]['price']) && ($data['ebay_pricevariant'][$result['condition_id']]['price']>0 && !empty($data['ebay_pricevariant'][$result['condition_id']]['price']))) {
			$result['price']=$this->model_shopmanager_product->editProductPriceWithShipping($result['product_id'],$data['ebay_pricevariant'][$result['condition_id']]['price']);
			
		}
	}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'upc' => $result['upc'],
				'condition_id' => $result['condition_id'],
				'category_id' => $result['category_id'],
				'made_in_country_id' => $result['made_in_country_id'],
				'condition_name' => $result['condition_name'],
				'marketplace_accounts_id' => $result['marketplace_accounts_id'], 
				'image'      => $image,
				'fullsize_image'       => $result['image'],
				'name'       => $result['name'],
				'model' => is_array($result['model']) ? reset($result['model']) : $result['model'],	
				'price'      => round($result['price'],2),
				'special'    => $special,
				'quantity'   => $result['quantity'],
                'unallocated_quantity'   => $result['unallocated_quantity'],
                'location'   => $result['location'],  
				'status_id'   => $result['status'],
				'has_sources'   => $result['has_sources'] ? $this->language->get('text_sources_set') : $this->language->get('text_sources_not_set'),
				'has_specifics'   => $result['has_specifics'] ? $this->language->get('text_specifics_set') : $this->language->get('text_specifics_not_set'),
				'filled_specifics_count' => $result['filled_specifics_count'],
				'total_specifics_count' => $result['total_specifics_count'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit' => $this->url->link(
					'shopmanager/product/edit', 
					'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url . 
					($result['upc'] == '' || $result['has_specifics'] ? '' : '&product_search=true'), 
					true
				),
			);

		}
//print("<pre>".print_r ($data['products'] ,true )."</pre>");


		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_missing_image_file'] = $this->language->get('text_missing_image_file');
		$data['text_specifics_not_set'] = $this->language->get('text_specifics_not_set');
		$data['text_specifics_set'] = $this->language->get('text_specifics_set');
		$data['text_specifics_error'] = $this->language->get('text_specifics_error');
		$data['text_not_listed'] = $this->language->get('text_not_listed');
		
        $data['text_listed'] = $this->language->get('text_listed');
        $data['text_not_listed'] = $this->language->get('text_not_listed');
        $data['text_error_listing'] = $this->language->get('text_error_listing');
        $data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_sources_set'] = $this->language->get('text_sources_set');
		$data['text_sources_not_set'] = $this->language->get('text_sources_not_set');
		$data['text_sources_error'] = $this->language->get('text_sources_error');
     // Filtres personnalisés
		$data['text_filter_name_length'] = $this->language->get('text_filter_name_length');
		$data['text_filter_invalid_price'] = $this->language->get('text_filter_invalid_price');
		$data['text_name_gt_80'] = $this->language->get('text_name_gt_80');
		$data['text_name_empty'] = $this->language->get('text_name_empty');
		$data['text_price_negative'] = $this->language->get('text_price_negative');


		$data['column_image'] = $this->language->get('column_image');
		$data['column_condition_id'] = $this->language->get('column_condition_id');
		
		
		$data['column_made_in_country_id'] = $this->language->get('column_made_in_country_id');
		$data['column_marketplace_item_id'] = $this->language->get('column_marketplace_item_id');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_unallocated_quantity'] = $this->language->get('column_unallocated_quantity');
        $data['column_location'] = $this->language->get('column_location');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');
        $data['column_product_id'] = $this->language->get('column_product_id');
		$data['column_specifics'] = $this->language->get('column_specifics');
		$data['column_sources'] = $this->language->get('column_sources');


		$data['entry_sku'] = $this->language->get('entry_sku');
		$data['entry_product_id'] = $this->language->get('entry_product_id');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_specifics'] = $this->language->get('entry_specifics');
		$data['entry_made_in_country'] = $this->language->get('entry_made_in_country');
		$data['entry_marketplace'] = $this->language->get('entry_marketplace');
		$data['entry_condition_id'] = $this->language->get('entry_condition_id');
		$data['entry_marketplace_account_id'] = $this->language->get('entry_marketplace_account_id');
		$data['entry_category_id'] = $this->language->get('entry_category_id');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_price_with_shipping'] = $this->language->get('entry_price_with_shipping');
		$data['entry_unallocated_quantity'] = $this->language->get('entry_unallocated_quantity');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
       
        $data['entry_location'] = $this->language->get('entry_location');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_sources'] = $this->language->get('entry_sources');
		$data['entry_sources'] = $this->language->get('entry_sources');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_enable'] = $this->language->get('button_enable');
		$data['button_disable'] = $this->language->get('button_disable');
		$data['button_print'] = $this->language->get('button_print');
		$data['button_filter'] = $this->language->get('button_filter');
		$data['button_update_marketplace'] = $this->language->get('button_update_marketplace');
		$data['button_remove_from_marketplace'] = $this->language->get('button_remove_from_marketplace');
		$data['button_list_on_marketplace'] = $this->language->get('button_list_on_marketplace');
		$data['button_relist_on_marketplace'] = $this->language->get('button_relist_on_marketplace');
		$data['button_feed'] = $this->language->get('button_feed');


		$data['button_product_search'] = $this->language->get('button_product_search');

		$data['entry_limit'] = $this->language->get('entry_limit');

		$data['per_page_options']=[20, 50, 100, 200,1000];



		$data['token'] = $this->session->data['token'];
		

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

		if (isset($this->session->data['error'])) {
			$data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error'] = '';
		}
		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name_length'])) {
			$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_invalid_price'])) {
			$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_made_in_country_id'])) {
			$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
		}

		
		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace'])) {
			$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

        if (isset($this->request->get['filter_unallocated_quantity'])) {
			$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
		}

        if (isset($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . $this->request->get['filter_location'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_specifics'])) {
			$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['filter_sources'])) {
			$url .= '&filter_sources=' . $this->request->get['filter_sources'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])){
			$url .= '&limit=' . $this->request->get['limit'];
		} 
		$data['sort_product_id'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.product_id' . $url, true);
		$data['sort_made_in_country_id'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.made_in_country_id' . $url, true);
		$data['sort_condition_id'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.condition_id' . $url, true);
		$data['sort_marketplace_item_id'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.marketplace_item_id' . $url, true);
		$data['sort_name'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, true);
        $data['sort_unallocated_quantity'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.unallocated_quantity' . $url, true);
		$data['sort_location'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.location' . $url, true);
		$data['sort_sources'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=has_sources' . $url, true);
		$data['sort_specifics'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=has_specifics' . $url, true);
        $data['sort_status'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name_length'])) {
			$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_invalid_price'])) {
			$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_made_in_country_id'])) {
			$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace'])) {
			$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

        if (isset($this->request->get['filter_unallocated_quantity'])) {
			$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
		}

        if (isset($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . $this->request->get['filter_location'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_specifics'])) {
			$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['filter_sources'])) {
			$url .= '&filter_sources=' . $this->request->get['filter_sources'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $limit; //$this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('shopmanager/product',  'token=' . $this->session->data['token'] . $url . '&page={page}&limit='.$limit , true);
		$data['limit_link'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url . '&page={page}&limit=', true);
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));


		$data['filter_name_length'] = $filter_name_length;
		$data['filter_invalid_price'] = $filter_invalid_price;
		$data['filter_sku'] = $filter_sku;
		$data['filter_product_id'] = $filter_product_id;
		$data['filter_made_in_country_id'] = $filter_made_in_country_id;
		$data['filter_marketplace'] = $filter_marketplace;
		$data['filter_marketplace_account_id'] = $filter_marketplace_account_id;
		$data['filter_name'] = $filter_name;
		$data['filter_category_id'] = $filter_category_id;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
        $data['filter_unallocated_quantity'] = $filter_unallocated_quantity;
        $data['filter_location'] = $filter_location;
		$data['filter_status'] = $filter_status;
		$data['filter_specifics'] = $filter_specifics;
		$data['filter_image'] = $filter_image;
		$data['filter_sources'] = $filter_sources;

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');
//print("<pre>".print_r ($data,true )."</pre>");

	//	$this->model_shopmanager_marketplace->syncMarketplaceProduct();
	$this->load->model('shopmanager/ebay');
	//$this->model_shopmanager_ebay->getInventory();
	//$response = $this->model_shopmanager_ebay->syncEbayInventory();
	//$this->model_shopmanager_marketplace->syncMarketplaceProduct();
	//$this->model_shopmanager_marketplace->syncMarketplaceProductSpecifics();

	/*	if(count($results)==1 && !isset($this->request->get['updated']) ){
			
			$this->response->redirect($result['has_specifics'] ? $this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . $url, true) : $this->url->link('shopmanager/product_search', 'token=' . $this->session->data['token'] . '&filter_sku='.$result['sku'].'&product_id=' . $result['product_id']. '&condition_id=' . $result['condition_id'] . $url, true));
		}else{*/
			$this->response->setOutput($this->load->view('shopmanager/product_list', $data));
	//	}
		
	}

	protected function getForm() {
		$this->document->addScript('view/javascript/shopmanager/product_form.js');

		$this->document->addScript('view/javascript/shopmanager/ai.js');
		$this->document->addScript('view/javascript/shopmanager/translate.js');
		$this->document->addScript('view/javascript/shopmanager/tools.js');
		$this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
		$this->document->addScript('view/javascript/shopmanager/alert_popup.js');
		$this->document->addScript('view/javascript/shopmanager/ebay.js');
		
		$this->load->model('shopmanager/tools');
		$this->load->model('shopmanager/product_search'); 
		$this->load->model('localisation/language');
		$this->load->model('setting/store');
		$this->load->model('shopmanager/manufacturer');
		$this->load->model('shopmanager/shipping');
		$this->load->model('shopmanager/recurring');
		$this->load->model('localisation/tax_class'); 
		$this->load->model('localisation/stock_status');
		$this->load->model('shopmanager/localisation/weight_class');
		$this->load->model('shopmanager/localisation/length_class');
		$this->load->model('shopmanager/ebay');
		$this->load->model('shopmanager/marketplace');
		$this->load->model('shopmanager/catalog/category');
		$this->load->model('shopmanager/condition');
		$this->load->model('shopmanager/filter');
		$this->load->model('shopmanager/attribute');
		$this->load->model('shopmanager/option');
		$this->load->model('customer/customer_group');
		$this->load->model('tool/image');
		$this->load->model('shopmanager/download');
		$this->load->model('design/layout');
		$this->load->model('shopmanager/ai');

	
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_plus'] = $this->language->get('text_plus');
		$data['text_minus'] = $this->language->get('text_minus');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_option_value'] = $this->language->get('text_option_value');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');
		$data['text_category_id'] = $this->language->get('text_category_id');
        $data['text_percent'] = $this->language->get('text_percent');
        $data['text_condition_name'] = $this->language->get('text_condition_name');
        $data['text_condition_id'] = $this->language->get('text_condition_id');
		$data['text_no_data'] = $this->language->get('text_no_data');
		$data['text_price'] = $this->language->get('text_price');
		$data['text_url'] = $this->language->get('text_url');
		$data['text_url_sold'] = $this->language->get('text_url_sold');
		$data['text_not_listed'] = $this->language->get('text_not_listed');
		$data['text_listed'] = $this->language->get('text_listed');
		$data['text_error_listing'] = $this->language->get('text_error_listing');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_image_alt'] = $this->language->get('text_image_alt');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_category_id'] = $this->language->get('entry_category_id');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_sku'] = $this->language->get('entry_sku');
		$data['entry_upc'] = $this->language->get('entry_upc');
		$data['entry_ean'] = $this->language->get('entry_ean');
		$data['entry_jan'] = $this->language->get('entry_jan');
		$data['entry_isbn'] = $this->language->get('entry_isbn');
		$data['entry_mpn'] = $this->language->get('entry_mpn');
		$data['entry_location'] = $this->language->get('entry_location');
		$data['entry_minimum'] = $this->language->get('entry_minimum');
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['entry_date_available'] = $this->language->get('entry_date_available');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_made_in_country'] = $this->language->get('entry_made_in_country');
		$data['entry_marketplace_account_id'] = $this->language->get('entry_marketplace_account_id');
		$data['entry_color'] = $this->language->get('entry_color');
		$data['entry_condition'] = $this->language->get('entry_condition');
		$data['entry_description_supp'] = $this->language->get('entry_description_supp');
		$data['entry_condition_supp'] = $this->language->get('entry_condition_supp');
		$data['entry_included_accessories'] = $this->language->get('entry_included_accessories');
		$data['entry_shipping_cost'] = $this->language->get('entry_shipping_cost');
		$data['entry_location'] = $this->language->get('entry_location');
		$data['entry_stock_status'] = $this->language->get('entry_stock_status');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_price_with_shipping'] = $this->language->get('entry_price_with_shipping');
		$data['entry_unallocated_quantity'] = $this->language->get('entry_unallocated_quantity');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_points'] = $this->language->get('entry_points');
		$data['entry_option_points'] = $this->language->get('entry_option_points');
		$data['entry_subtract'] = $this->language->get('entry_subtract');
		$data['entry_weight_class'] = $this->language->get('entry_weight_class');
		$data['entry_weight'] = $this->language->get('entry_weight');
		$data['entry_weight_oz'] = $this->language->get('entry_weight_oz');
		$data['entry_dimension'] = $this->language->get('entry_dimension');
		$data['entry_length_class'] = $this->language->get('entry_length_class');
		$data['entry_length'] = $this->language->get('entry_length');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_additional_image'] = $this->language->get('entry_additional_image');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$data['entry_download'] = $this->language->get('entry_download');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_filter'] = $this->language->get('entry_filter');
		$data['entry_related'] = $this->language->get('entry_related');
		$data['entry_attribute'] = $this->language->get('entry_attribute');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_option'] = $this->language->get('entry_option');
		$data['entry_option_value'] = $this->language->get('entry_option_value');
		$data['entry_required'] = $this->language->get('entry_required');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_priority'] = $this->language->get('entry_priority');
		$data['entry_tag'] = $this->language->get('entry_tag');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_reward'] = $this->language->get('entry_reward');
		$data['entry_layout'] = $this->language->get('entry_layout');
		$data['entry_recurring'] = $this->language->get('entry_recurring');
		$data['entry_ebay_search'] = $this->language->get('entry_ebay_search');
		$data['entry_external_images'] = $this->language->get('entry_external_images');
		$data['entry_main_image'] = $this->language->get('entry_main_image');
		$data['column_specifics'] = $this->language->get('column_specifics');
		$data['column_found_value'] = $this->language->get('column_found_value');
		$data['column_actual_value'] = $this->language->get('column_actual_value');
		$data['column_condition'] = $this->language->get('column_condition');
		$data['column_made_in_country_id'] = $this->language->get('column_made_in_country_id');
		$data['text_upload_images'] = $this->language->get('text_upload_images');
        $data['entry_image_principal'] = $this->language->get('entry_image_principal');
        $data['entry_additional_images'] = $this->language->get('entry_additional_images');
        $data['text_drop_here'] = $this->language->get('text_drop_here');
        $data['text_drop_additional_here'] = $this->language->get('text_drop_additional_here');
        $data['button_image_upload'] = $this->language->get('button_image_upload');
        $data['error_upload'] = $this->language->get('error_upload');
		$data['entry_sourcecode'] = $this->language->get('entry_sourcecode');
        $data['placeholder_sourcecode'] = $this->language->get('placeholder_sourcecode');
		$data['text_image_upload'] = $this->language->get('text_image_upload');
        $data['text_recognized_text'] = $this->language->get('text_recognized_text');
        $data['text_drag_drop'] = $this->language->get('text_drag_drop');
		$data['entry_sources'] = $this->language->get('entry_sources');
        $data['text_sources_set'] = $this->language->get('text_sources_set');
        $data['text_sources_not_set'] = $this->language->get('text_sources_not_set');
        $data['text_sources_error'] = $this->language->get('text_sources_error');
		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_sku'] = $this->language->get('help_sku');
		$data['help_upc'] = $this->language->get('help_upc');
		$data['help_ean'] = $this->language->get('help_ean');
		$data['help_jan'] = $this->language->get('help_jan');
		$data['help_isbn'] = $this->language->get('help_isbn');
		$data['help_mpn'] = $this->language->get('help_mpn');
		$data['help_minimum'] = $this->language->get('help_minimum');
		$data['help_manufacturer'] = $this->language->get('help_manufacturer');
		$data['help_stock_status'] = $this->language->get('help_stock_status');
		$data['help_points'] = $this->language->get('help_points');
		$data['help_category'] = $this->language->get('help_category');
		$data['help_filter'] = $this->language->get('help_filter');
		$data['help_download'] = $this->language->get('help_download');
		$data['help_related'] = $this->language->get('help_related');
		$data['help_tag'] = $this->language->get('help_tag');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_attribute_add'] = $this->language->get('button_attribute_add');
		$data['button_option_add'] = $this->language->get('button_option_add');
		$data['button_option_value_add'] = $this->language->get('button_option_value_add');
		$data['button_discount_add'] = $this->language->get('button_discount_add');
		$data['button_special_add'] = $this->language->get('button_special_add');
		$data['button_image_add'] = $this->language->get('button_image_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_recurring_add'] = $this->language->get('button_recurring_add');
		$data['button_check_all'] = $this->language->get('button_check_all');
		$data['button_get_specifics'] = $this->language->get('button_get_specifics');
		$data['button_add_specifics'] = $this->language->get('button_add_specifics');
		$data['button_update_marketplace'] = $this->language->get('button_update_marketplace');
		$data['button_remove_from_marketplace'] = $this->language->get('button_remove_from_marketplace');
		$data['button_list_on_marketplace'] = $this->language->get('button_list_on_marketplace');
		$data['button_relist_on_marketplace'] = $this->language->get('button_relist_on_marketplace');
		$data['button_ai_description_supp'] = $this->language->get('button_ai_description_supp');
		$data['button_ai_suggest_entry_name'] = $this->language->get('button_ai_suggest_entry_name');
		$data['button_regenerate_specifics'] = $this->language->get('button_regenerate_specifics');
		$data['button_google_search'] = $this->language->get('button_google_search');
		$data['button_feed'] = $this->language->get('button_feed');

		
		$data['tab_product_search'] = $this->language->get('tab_product_search');
		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_attribute'] = $this->language->get('tab_attribute');
		$data['tab_option'] = $this->language->get('tab_option');
		$data['tab_recurring'] = $this->language->get('tab_recurring');
		$data['tab_discount'] = $this->language->get('tab_discount');
		$data['tab_special'] = $this->language->get('tab_special');
		$data['tab_image'] = $this->language->get('tab_image');
		$data['tab_links'] = $this->language->get('tab_links');
		$data['tab_reward'] = $this->language->get('tab_reward');
		$data['tab_design'] = $this->language->get('tab_design');
		$data['tab_openbay'] = $this->language->get('tab_openbay');
		$data['tab_specifics'] = $this->language->get('tab_specifics');
		$data['column_action'] = $this->language->get('column_action');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error)){
			$errors = ['location', 'keyword', 'model', 'manufacturer_id', 'height', 'width', 'length', 'weight', 'warning', 'category','shipping_cost'];

			foreach ($errors as $error) {
				$data['error_' . $error] = isset($this->error[$error]) ? $this->error[$error] : '';
			}

			if (isset($this->error['name'])) {
				$data['error_name'] = $this->error['name'];
			} else {
				$data['error_name'] = array();
			}
	
			if (isset($this->error['meta_title'])) {
				$data['error_meta_title'] = $this->error['meta_title'];
			} else {
				$data['error_meta_title'] = array();
			}

			if (isset($this->error['manufacturer_id'])) {
				$data['error_manufacturer_id'] = $this->error['manufacturer_id'];
			} else {
				$data['error_manufacturer_id'] = array();
			}

			if (isset($this->error['made_in_country_id'])) {
				$data['error_made_in_country_id'] = $this->error['made_in_country_id'];
			} else {
				$data['error_made_in_country_id'] = array();
			}

			if (isset($this->error['location'])) {
				$data['error_location'] = $this->error['location'];
			} else {
				$data['error_location'] = array();
			}

			if (isset($this->error['height'])) {
				$data['error_height'] = $this->error['height'];
			} else {
				$data['error_height'] = array();
			}

			if (isset($this->error['width'])) {
				$data['error_width'] = $this->error['width'];
			} else {
				$data['error_width'] = array();
			}

			if (isset($this->error['length'])) {
				$data['error_length'] = $this->error['length'];
			} else {
				$data['error_length'] = array();
			}

			if (isset($this->error['weight'])) {
				$data['error_weight'] = $this->error['weight'];
			} else {
				$data['error_weight'] = array();
			}

			if (isset($this->error['shipping_cost'])) {
				$data['error_shipping_cost'] = $this->error['shipping_cost'];
			} else {
				$data['error_shipping_cost'] = array();
			}

		
		}
	
		$url = '';

		if (isset($this->request->get['filter_name_length'])) {
			$url .= '&filter_name_length=' . urlencode(html_entity_decode($this->request->get['filter_name_length'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_invalid_price'])) {
			$url .= '&filter_invalid_price=' . urlencode(html_entity_decode($this->request->get['filter_invalid_price'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_made_in_country_id'])) {
			$url .= '&filter_made_in_country_id=' . urlencode(html_entity_decode($this->request->get['filter_made_in_country_id'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_marketplace_account_id'])) {
			$url .= '&filter_marketplace_account_id=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace'])) {
			$url .= '&filter_marketplace=' . urlencode(html_entity_decode($this->request->get['filter_marketplace'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

        if (isset($this->request->get['filter_unallocated_quantity'])) {
			$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
		}

        if (isset($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . $this->request->get['filter_location'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_specifics'])) {
			$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['filter_sources'])) {
			$url .= '&filter_sources=' . $this->request->get['filter_sources'];
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

		if (isset($this->request->get['limit'])){
			$url .= '&limit=' . $this->request->get['limit'];
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('shopmanager/product/add', 'token=' . $this->session->data['token'] . $url, true);
		
		} else {
			$data['action'] = $this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
			$data['upload_images_action'] = $this->url->link('shopmanager/tools/uploadImagesFiles', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);

		}


		$data['cancel'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_shopmanager_product->getProduct($this->request->get['product_id']);
			
			$product_info['marketplace_accounts_id'] = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $this->request->get['product_id']]);

		}
		$data['token'] = $this->session->data['token'];
		
		
		$data['marketplace_accounts']= $this->model_shopmanager_marketplace->getMarketplaceAccount(['customer_id' => 10 ]);

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$this->load->model('shopmanager/localisation/country');

		$data['countries'] = $this->model_shopmanager_localisation_country->getCountries(array('sort'=>'name'));

		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_shopmanager_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}
	
		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($product_info)) {
			$data['product_id'] = $product_info['product_id'];
		} else {
			$data['product_id'] = null;
		}

		if (isset($this->request->post['category_id'])) {
			$data['category_id'] = $this->request->post['category_id'];
		} elseif (!empty($product_info)) {
			$data['category_id'] = $product_info['category_id'];
		} else {
			$data['category_id'] = null;
		}

		if (isset($this->request->post['site_id'])) {
			$data['site_id'] = $this->request->post['site_id'];
		} elseif (!empty($product_info)) {
			$data['site_id'] = $product_info['site_id'];
		} else {
			$data['site_id'] = 0;
		}


		if (isset($this->request->post['condition_id'])) {
			$data['condition_id'] = $this->request->post['condition_id'];
		} elseif (!empty($product_info)) {
			$data['condition_id'] = $product_info['condition_id'];
		} else {
			$data['condition_id'] = null;
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = 'n/a';
		}

		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($product_info)) {
			$data['sku'] = $product_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['upc'])) {
			$data['upc'] = $this->request->post['upc'];
		} elseif (!empty($product_info)) {
			$data['upc'] = $product_info['upc'];
		} else {
			$data['upc'] = '';
		}

		if (isset($this->request->post['ean'])) {
			$data['ean'] = $this->request->post['ean'];
		} elseif (!empty($product_info)) {
			$data['ean'] = $product_info['ean'];
		} else {
			$data['ean'] = '';
		}

		if (isset($this->request->post['jan'])) {
			$data['jan'] = $this->request->post['jan'];
		} elseif (!empty($product_info)) {
			$data['jan'] = $product_info['jan'];
		} else {
			$data['jan'] = '';
		}

		if (isset($this->request->post['isbn'])) {
			$data['isbn'] = $this->request->post['isbn'];
		} elseif (!empty($product_info)) {
			$data['isbn'] = $product_info['isbn'];
		} else {
			$data['isbn'] = '';
		}

		if (isset($this->request->post['mpn'])) {
			$data['mpn'] = $this->request->post['mpn'];
		} elseif (!empty($product_info)) {
			$data['mpn'] = $product_info['mpn'];
		} else {
			$data['mpn'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}
	
		if (isset($this->request->post['made_in_country_id'])) {
			$data['made_in_country_id'] = $this->request->post['made_in_country_id'];
		} elseif (!empty($product_info)) {
			$data['made_in_country_id'] = $product_info['made_in_country_id'];
		} else {
			$data['made_in_country_id'] = '';
		}

		$data['stores'] = $this->model_setting_store->getStores();
  
		if (isset($this->request->post['product_store'])) {
			$data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_shopmanager_product->getProductStores($this->request->get['product_id']);
		} else {
			$data['product_store'] = array(0);
		}
   
		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($product_info['shipping'])) {
			$data['shipping'] = $product_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = round($this->request->post['price'],2);
		} elseif (!empty($product_info)) {
			$data['price'] = round($product_info['price'],2);
		} else {
			$data['price'] = 0;
		}

		if($data['price']<0 ){
			if (isset($data['upc']) && is_numeric($data['upc'])) { 
				$ProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources($data['upc']);
				//print("<pre>".print_r ('1120 product' ,true )."</pre>");
				//print("<pre>".print_r ($ProductInfoSources ,true )."</pre>");
	
				$data['ebay_pricevariant']=json_decode($ProductInfoSources['ebay_pricevariant']?? '[]', true);
			}elseif(isset($data['product_id'])){
				$ProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources(null,null,$data['product_id']);
	
				$data['ebay_pricevariant']=json_decode($ProductInfoSources['ebay_pricevariant']?? '[]', true);
			} 
			//print("<pre>".print_r ($data['ebay_pricevariant'] ,true )."</pre>");
			if (isset($data['ebay_pricevariant'][$data['condition_id']]['price']) && ($data['ebay_pricevariant'][$data['condition_id']]['price']>0 && !empty($data['ebay_pricevariant'][$data['condition_id']]['price']))) {
				$data['price']=$this->model_shopmanager_product->editProductPriceWithShipping($data['product_id'],$data['ebay_pricevariant'][$data['condition_id']]['price']);
				
			}
		}
		if (isset($this->request->post['price_with_shipping'])) {
			$data['price_with_shipping'] = round($this->request->post['price_with_shipping'],2);
		} elseif (!empty($product_info)) {
			$data['price_with_shipping'] = round($product_info['price_with_shipping'],2);
		} else {
			$data['price_with_shipping'] = 0;
		}

		if (isset($this->request->post['shipping_cost'])) {
			$data['shipping_cost'] = is_numeric($this->request->post['shipping_cost'])? round($this->request->post['shipping_cost'],2):0;
		} elseif (!empty($product_info)) {
			
			if($product_info['shipping_cost']==0 || !isset($product_info['shipping_cost'])){
			
				$result=$this->model_shopmanager_shipping->calculateShippingRates($product_info);
				$data['shipping_cost']=$result['shipping_cost'];
				$data['shipping_carrier']=$result['shipping_carrier'];
				$data['price_with_shipping']=$data['price']+$result['shipping_cost'];

			}else{
				$data['shipping_cost'] = round($product_info['shipping_cost'],2);
			}
		} else {
			$data['shipping_cost'] = 0;
		}

		if (isset($this->request->post['shipping_carrier'])) {
			$data['shipping_carrier'] = $this->request->post['shipping_carrier'];
		} elseif (!empty($product_info)) {
			$data['shipping_carrier'] = $product_info['shipping_carrier'];
		} else {
			$data['shipping_carrier'] = '';
		}
	

		$data['recurrings'] = $this->model_shopmanager_recurring->getRecurrings();

		if (isset($this->request->post['product_recurrings'])) {
			$data['product_recurrings'] = $this->request->post['product_recurrings'];
		} elseif (!empty($product_info)) {
			$data['product_recurrings'] = $this->model_shopmanager_product->getRecurrings($product_info['product_id']);
		} else {
			$data['product_recurrings'] = array();
		}


		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($product_info)) {
			$data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}
		//print("<pre>".print_r ($this->request->post,true )."</pre>");
		
		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($product_info)) {
			$data['quantity'] = $product_info['quantity'];
		} else {
			$data['quantity'] = 0;
		}
        if (isset($this->request->post['unallocated_quantity'])) {
			$data['unallocated_quantity'] = $this->request->post['unallocated_quantity'];
		} elseif (!empty($product_info)) {
			$data['unallocated_quantity'] = $product_info['unallocated_quantity'];
		} else {
			$data['unallocated_quantity'] = 0;
		}

        if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}
		
		if (isset($this->request->post['condition_id'])) {
			$data['condition_id'] = $this->request->post['condition_id'];
		} elseif (!empty($product_info)) {
			$data['condition_id'] = $product_info['condition_id'];
		} else {
			$data['condition_id'] = 9;
		}
		if (isset($data['product_id'])) {
			if($data['unallocated_quantity']==0 && $data['quantity'] ==0){
				$data['location']='';
				$data['sku']=($data['condition_id']==9)?$data['upc']:$data['product_id'];
			}
		}
		if($data['upc']!=''){
			$existing_products = $this->model_shopmanager_product->getProductByUPC($data['upc']);
			foreach ($existing_products  as $existing_product) {				
				$data['existing_products'][$existing_product['condition_id']] = array(
					'product_id' => $existing_product['product_id'],
					'condition_id' => $existing_product['condition_id'],
					'upc' => $existing_product['upc'],
					'url' => $existing_product['has_specifics'] ? $this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $existing_product['product_id'] . $url, true) : $this->url->link('shopmanager/product_search', 'token=' . $this->session->data['token'] . '&upc='.$existing_product['upc'].'&product_id=' . $existing_product['product_id']. '&condition_id=' . $existing_product['condition_id'] . $url, true)
				);
			}
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($product_info)) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($product_info)) {
			$data['subtract'] = $product_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}


		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($product_info)) {
			$data['weight'] = round($product_info['weight'],3);
		} else {
			$data['weight'] = 0;
		}

		
		if (isset($product_info['weight_class_id'])){
			$weight_class_info = $this->model_shopmanager_localisation_weight_class->getWeightClasses(array('weight_class_id' => $product_info['weight_class_id']));
			$product_info['weight_class_title']=$weight_class_info[0]['unit'];
		}

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($product_info)) {
			$data['weight_class_id'] = $product_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id'); 
		}

		if (isset($this->request->post['weight_class_title'])) {
			$data['weight_class_title'] = $this->request->post['weight_class_title'];
		} elseif (!empty($product_info)) {
			$data['weight_class_title'] = $product_info['weight_class_title'];
		} else {
			$data['weight_class_title'] = 'Lbs';
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($product_info)) {
			$data['length'] = round($product_info['length'],1);
		} else {
			$data['length'] = 0;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($product_info)) {
			$data['width'] = round($product_info['width'],1);
		} else {
			$data['width'] = 0;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($product_info)) {
			$data['height'] = round($product_info['height'],1);
		} else {
			$data['height'] = 0;
		}

		if (isset($product_info['length_class_id'])){

			$length_class_info = $this->model_shopmanager_localisation_length_class->getLengthClasses(array('length_class_id' => $product_info['length_class_id']));
			$product_info['length_class_title']=$length_class_info[0]['unit'];
		}
		
		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($product_info)) {
			$data['length_class_id'] = $product_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		if (isset($this->request->post['length_class_title'])) {
			$data['length_class_title'] = $this->request->post['length_class_title'];
		} elseif (!empty($product_info)) {
			$data['length_class_title'] = $product_info['length_class_title'];
		} else {
			$data['length_class_title'] = '';
		}

		
		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($product_info)) {
			$manufacturer_info = $this->model_shopmanager_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}
	

		if (isset($data['upc']) && is_numeric($data['upc'])) { 
			$ProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources($data['upc']);

			$data['ebay_info']=json_decode($ProductInfoSources['ebay_search']??null,true);
			$data['ebay_pricevariant']=json_decode($ProductInfoSources['ebay_pricevariant']?? '[]', true);
		}elseif(isset($data['product_id'])){
			$ProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources(null,null,$data['product_id']);

			$data['ebay_info']=json_decode($ProductInfoSources['ebay_search']??null,true);
			$data['ebay_pricevariant']=json_decode($ProductInfoSources['ebay_pricevariant']?? '[]', true);
		}   
	
		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_shopmanager_product->getProductCategories($this->request->get['product_id']);
		} else {
		
			$categories = array();
		}

		$data['product_categories'] = array();
	
		foreach ($categories as $category_id) {
			$category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'site_id' => $category_info['site_id'],
					'name_category' => $category_info['name'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
				if( $category_info['leaf']=='1'){
					$data['category_id']=$category_info['category_id'];
					$data['site_id']=$category_info['site_id'];
				}
			}
		}

		if(isset($data['category_id'])){
		
			$data['conditions']=$this->model_shopmanager_condition->getConditionDetails($data['category_id'],null,null,$data['site_id']);
	
			$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($data['category_id']);
			$category_leaf = $this->model_shopmanager_catalog_category->getCategoryLeaf($data['category_id']);

			if (!is_array($category_specific_info)) {
				
				if($category_leaf ==1){
					$this->response->redirect($this->url->link('shopmanager/catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $data['category_id'] . '&product_id='.$data['product_id'], true));
				}else{
					$this->error['category']= $this->language->get('error_category_not_leaf');
				}
			}
		
			if(!is_array($category_specific_info[1]['specifics'])){
				$this->response->redirect($this->url->link('shopmanager/catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $data['category_id'] . '&product_id='.$data['product_id'], true));
			}
		

		}else{

			$data['conditions'] =  $this->model_shopmanager_condition->getConditionDetails($data['category_id']);
		}

		// Filters

		if (isset($this->request->post['product_filter'])) {
			$filters = $this->request->post['product_filter'];
		} elseif (isset($this->request->get['product_id'])) {
			$filters = $this->model_shopmanager_product->getProductFilters($this->request->get['product_id']);
		} else {
			$filters = array();
		}

		$data['product_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_shopmanager_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}
	
		// Attributes

		if (isset($this->request->post['product_attribute'])) {
			$product_attributes = $this->request->post['product_attribute'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_attributes = $this->model_shopmanager_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$product_attributes = array();
		}

		$data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_shopmanager_attribute->getAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}

		// Options

		if (isset($this->request->post['product_option'])) {
			$product_options = $this->request->post['product_option'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_options = $this->model_shopmanager_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$data['product_options'] = array();

		foreach ($product_options as $product_option) {
			$product_option_value_data = array();

			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
                        'unallocated_quantity'    => $product_option_value['unallocated_quantity'],
                        'location'                => $product_option_value['location'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => round($product_option_value['price'],2),
						'price_with_shipping'     => $product_option_value['price_with_shipping'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
			}

			$data['product_options'][] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',
				'required'             => $product_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($data['option_values'][$product_option['option_id']])) {
					$data['option_values'][$product_option['option_id']] = $this->model_shopmanager_option->getOptionValues($product_option['option_id']);
				}
			}
		}


		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['product_discount'])) {
			$product_discounts = $this->request->post['product_discount'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_discounts = $this->model_shopmanager_product->getProductDiscounts($this->request->get['product_id']);
		} else {
			$product_discounts = array();
		}

		$data['product_discounts'] = array();

		foreach ($product_discounts as $product_discount) {
			$data['product_discounts'][] = array(
				'customer_group_id' => $product_discount['customer_group_id'],
				'quantity'          => $product_discount['quantity'],
                'unallocated_quantity'          => $product_discount['unallocated_quantity'],
                'location'          => $product_discount['location'],
				'priority'          => $product_discount['priority'],
				'price'             => round($product_discount['price'],2),
				'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
			);
		}

		if (isset($this->request->post['product_special'])) {
			$product_specials = $this->request->post['product_special'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_specials = $this->model_shopmanager_product->getProductSpecials($this->request->get['product_id']);
		} else {
			$product_specials = array();
		}

		$data['product_specials'] = array();

		foreach ($product_specials as $product_special) {
			$data['product_specials'][] = array(
				'customer_group_id' => $product_special['customer_group_id'],
				'priority'          => $product_special['priority'],
				'price'             => round($product_special['price'],2),
				'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
			);
		}
		
		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}


		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100); 
		} elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_images = $this->model_shopmanager_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order']
			);
		}
//print("<pre>".print_r ($data['product_images'] ,true )."</pre>");
		// Downloads

		if (isset($this->request->post['product_download'])) {
			$product_downloads = $this->request->post['product_download'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_downloads = $this->model_shopmanager_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$product_downloads = array();
		}

		$data['product_downloads'] = array();

		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_shopmanager_download->getDownload($download_id);

			if ($download_info) {
				$data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}

		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {
			$products = $this->model_shopmanager_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}
		//print("<pre>".print_r($products, true)."</pre>");

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_shopmanager_product->getProduct($product_id);
				//print("<pre>".print_r($related_info, true)."</pre>");

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($product_info)) {
			$data['points'] = $product_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['product_reward'])) {
			$data['product_reward'] = $this->request->post['product_reward'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_reward'] = $this->model_shopmanager_product->getProductRewards($this->request->get['product_id']);
		} else {
			$data['product_reward'] = array();
		}

		if (isset($this->request->post['product_layout'])) {
			$data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_layout'] = $this->model_shopmanager_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$data['product_layout'] = array();
		}
		if (!empty($this->request->post['marketplace_accounts_id'])) {
			// Décoder le JSON en tableau associatif
			$data['marketplace_accounts_id'] =  $this->request->post['marketplace_accounts_id'];
		} elseif (!empty($product_info['marketplace_accounts_id'])) {
			// Vérifier si `marketplace_accounts_id` est déjà un tableau, sinon le convertir
			$data['marketplace_accounts_id'] = $product_info['marketplace_accounts_id'];
		} else {
			// Si aucune donnée n'est disponible, initialiser avec un tableau vide
			$data['marketplace_accounts_id'] = [];
		}

		foreach ($data['marketplace_accounts_id'] as $marketplace_id => $item) 
		
		{
				if (!is_array($item) || !isset($item['image'])) {
					continue; // Vérifie que $item est bien un tableau et contient 'image'
				}
		
				// Récupérer le chemin et le nom de l'image
				$imagePath = pathinfo($item['image'], PATHINFO_DIRNAME);
				$imageName = pathinfo($item['image'], PATHINFO_FILENAME);
				$imageExt = pathinfo($item['image'], PATHINFO_EXTENSION);
		
				// Vérifier et appliquer les modifications
				if (!empty($item['error'])) {
					$newImage = "{$imagePath}/{$imageName}_red.{$imageExt}";
				} elseif (empty($item['marketplace_item_id'])) {
					$newImage = "{$imagePath}/{$imageName}_grey.{$imageExt}";
				} else {
					$newImage = "{$imagePath}/{$imageName}_green.{$imageExt}"; // Image par défaut
				}
			
					$data['marketplace_accounts_id'][$marketplace_id]['thumb'] = $this->model_tool_image->resize($newImage, 25,25);
				
			
		}
		if (isset($this->request->get['product_search'])) {
			$this->load->model('shopmanager/product_search');
			$data['product_search_data'] = $this->model_shopmanager_product_search->getProductSearchData($product_info['upc']??'',$product_info['product_id']);
			$data_json=$this->model_shopmanager_product_search->feedProductInfoWithSearchData(json_encode($data));
			$data= json_decode($data_json,true);
			unset($data['product_search_data']['specifics_result']);
			//print("<pre>".print_r ($data ,true )."</pre>");
		} else {
			$data['product_search_data'] = '';
		}
		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');
		
		

		$this->response->setOutput($this->load->view('shopmanager/product_form', $data));
	}

	protected function validateForm() {
	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
		// Vérification des permissions de modification
		if (!$this->user->hasPermission('modify', 'shopmanager/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
	
		// Validation des champs `product_description`
		foreach ($this->request->post['product_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 255)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
	
			if ((utf8_strlen($value['meta_title']) < 3) || (utf8_strlen($value['meta_title']) > 255)) {
				$this->error['meta_title'][$language_id] = $this->language->get('error_meta_title');
			}
			break;
		}
	
		// Validation du modèle
		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $this->language->get('error_model');
		}
	
		// Validation du fabricant
		if (!isset($this->request->post['manufacturer_id']) || $this->request->post['manufacturer_id'] == 0) {
			$this->error['manufacturer_id'] = $this->language->get('error_manufacturer_id');
		}
/*
		if (!isset($this->request->post['made_in_country_id']) || $this->request->post['made_in_country_id'] == 0) {
			$this->error['made_in_country_id'] = $this->language->get('error_made_in_country_id');
		}*/
		if (isset($this->request->post['quantity']) && $this->request->post['quantity'] > 0 && $this->request->post['location'] =="") {
			$this->error['location'] = $this->language->get('error_location');
		}
		// Validation des dimensions
		if (!isset($this->request->post['height']) || $this->request->post['height'] <= 0 || $this->request->post['height'] > 60) {
			$this->error['height'] = $this->language->get('error_height');
		}
	
		if (!isset($this->request->post['width']) || $this->request->post['width'] <= 0 || $this->request->post['width'] > 60) {
			$this->error['width'] = $this->language->get('error_width');
		}
	
		if (!isset($this->request->post['length']) || $this->request->post['length'] <= 0 || $this->request->post['length'] > 60) {
			$this->error['length'] = $this->language->get('error_length');
		}
	
		// Validation du poids
		if (!isset($this->request->post['weight']) || $this->request->post['weight'] <= 0 || $this->request->post['weight'] > 100) {
			$this->error['weight'] = $this->language->get('error_weight');
		}

		// Validation du poids
		if (!isset($this->request->post['shipping_cost']) || $this->request->post['shipping_cost'] =='' || $this->request->post['shipping_cost'] <= 0 || $this->request->post['shipping_cost'] > 100) {
			$this->error['shipping_cost'] = $this->language->get('error_shipping_cost');
		}
		
	
		// Affichage des messages de debug
	//print("<pre>" . print_r('2389', true) . "</pre>");
//		//print("<pre>" . print_r($this->request->post, true) . "</pre>");
//		//print("<pre>" . print_r($this->error, true) . "</pre>"); 
	
		// Message d'avertissement général si d'autres erreurs sont présentes
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
	
		return !$this->error;
	}
	

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'shopmanager/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'shopmanager/product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('shopmanager/product');
			$this->load->model('shopmanager/option');

			if (isset($this->request->get['filter_sku'])) {
				$filter_sku = $this->request->get['filter_sku'];
			} else {
				$filter_sku = '';
			}

			if (isset($this->request->get['filter_product_id'])) {
				$filter_product_id = $this->request->get['filter_product_id'];
			} else {
				$filter_product_id = '';
			}

			if (isset($this->request->get['filter_made_in_country_id'])) {
				$filter_made_in_country_id = $this->request->get['filter_made_in_country_id'];
			} else {
				$filter_made_in_country_id = '';
			}
			
			if (isset($this->request->get['filter_marketplace_account_id'])) {
				$filter_marketplace_account_id = $this->request->get['filter_marketplace_account_id'];
			} else {
				$filter_marketplace_account_id = '';
			}

			if (isset($this->request->get['filter_marketplace'])) {
				$filter_marketplace = $this->request->get['filter_marketplace'];
			} else {
				$filter_marketplace = '';
			}

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_category_id'])) {
				$filter_category_id = $this->request->get['filter_category_id'];
			} else {
				$filter_category_id = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_sku' 		=> $filter_sku,
				'filter_product_id' => $filter_product_id,
				'filter_made_in_country_id' => $filter_made_in_country_id,
				'filter_marketplace' 	=> $filter_marketplace,
				'filter_marketplace_account_id' 	=> $filter_marketplace_account_id,
				'filter_name'  		=> $filter_name,
				'filter_category_id' 		=> $filter_category_id,
				'filter_model' 		=> $filter_model,
				'start'        		=> 0,
				'limit'        		=> $limit
			);

			$results = $this->model_shopmanager_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_shopmanager_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_shopmanager_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_shopmanager_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => round($result['price'],2)
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


public function trfUnallocatedQuantity() {
    // Charger le modèle qui gère les produits
    $this->load->model('shopmanager/product');

    // Vérifier si les données nécessaires sont envoyées via POST
    if (isset($this->request->post['product_id']) && isset($this->request->post['unallocated_quantity']) && isset($this->request->post['quantity']) && isset($this->request->post['location'])) {
        $product_id = (int)$this->request->post['product_id'];
        $unallocated_quantity = (int)$this->request->post['unallocated_quantity'];
        $new_quantity = (int)$this->request->post['quantity'];
        $new_location = $this->db->escape($this->request->post['location']); // Sécuriser l'entrée de localisation

        // Mettre à jour la quantité du produit dans la base de données
        $this->model_shopmanager_product->updateQuantity($product_id, $new_quantity+$unallocated_quantity);

        // Remettre à 0 la quantité non allouée
        $this->model_shopmanager_product->updateUnallocatedQuantity($product_id, 0);

        // Mettre à jour la localisation du produit
        $this->model_shopmanager_product->updateProductLocation($product_id, $new_location);

        // Retourner une réponse de succès à l'interface
        $json['success'] = true;
        $json['message'] = 'Les données du produit ont été mises à jour avec succès.';
    } else {
        // En cas de données manquantes, renvoyer une erreur
        $json['success'] = false;
        $json['message'] = 'Des données sont manquantes pour effectuer la mise à jour.';
    }

    // Retourner la réponse au format JSON
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function updateUnallocatedQuantity() {
    // Charger le modèle qui gère les produits
    $this->load->model('shopmanager/product');

    // Vérifier si les données nécessaires sont envoyées via POST
    if (isset($this->request->post['product_id']) && isset($this->request->post['unallocated_quantity']) && isset($this->request->post['quantity']) && isset($this->request->post['marketplace_item_id'])) {
        $product_id = (int)$this->request->post['product_id'];
        $unallocated_quantity = (int)$this->request->post['unallocated_quantity'];
        $quantity = (int)$this->request->post['quantity'];
      

        // Calculer la nouvelle quantité totale (quantity + unallocated_quantity)
        $final_quantity = $quantity + $unallocated_quantity;

        // Mettre à jour la quantité du produit et la quantité non allouée dans la base de données
      
        $this->model_shopmanager_product->updateUnallocatedQuantity($product_id, $unallocated_quantity); // Remettre unallocated_quantity à 0

		$this->load->model('shopmanager/marketplace');
		$marketplace_accounts_id = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id]);

		foreach($marketplace_accounts_id as $marketplace_account_id=> $marketplace_account){
			if(isset($marketplace_account['marketplace_item_id'])){
            $this->load->model('shopmanager/ebay');
			$final_quantity = $quantity + $unallocated_quantity;
           // $result[] = $this->model_shopmanager_ebay->edit($product_id, $final_quantity);
			$result[] = $this->model_shopmanager_ebay->editQuantity($marketplace_account['marketplace_item_id'], $final_quantity,null,$product_id,$marketplace_account_id,$marketplace_account['site_setting']);

			}
		}
			foreach($result as $key=>$resul) {
				if (isset($resul['Errors']) && $resul['Ack'] == 'Failure') {
					$this->error['error'] = "eBay ERROR: <br>";
			
					$errors = new RecursiveIteratorIterator(new RecursiveArrayIterator($resul['Errors']));
					foreach ($errors as $key => $value) {
						if ($key === 'LongMessage') {
							$this->error['error'] .= '************ ' . $value . '<br>';
						}
					}
			
					$this->session->data['error'] = $this->error['error'];
					$json['ebay_success'] = false;
					$json['ebay_message'] = 'Erreur lors de la mise à jour de la quantité eBay.';
				} else {
					unset($resul['Errors']);
					$json['ebay_success'] = true;
					$json['ebay_message'] = 'La quantité eBay a été mise à jour avec succès.';
				}
			}

        // Retourner une réponse de succès à l'interface
        $json['success'] = true;
        $json['message'] = 'Quantité mise à jour avec succès.';
    } else {
        // En cas de données manquantes, renvoyer une erreur
        $json['success'] = false;
        $json['message'] = 'Des données sont manquantes pour effectuer la mise à jour.';
    }

    // Retourner la réponse au format JSON
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

public function updateQuantity() {
    // Charger le modèle qui gère les produits
    $this->load->model('shopmanager/product');

    // Vérifier si les données nécessaires sont envoyées via POST
    if (isset($this->request->post['product_id']) && isset($this->request->post['quantity']) && isset($this->request->post['marketplace_item_id'])) {
        $product_id = (int)$this->request->post['product_id'];
        $quantity = (int)$this->request->post['quantity'];
		$unallocated_quantity = (int)$this->request->post['unallocated_quantity'];
      

        // Mettre à jour la quantité du produit dans la base de données
        $this->model_shopmanager_product->updateQuantity($product_id, $quantity);

		$this->load->model('shopmanager/marketplace');
		$marketplace_accounts_id = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id]);

		foreach($marketplace_accounts_id as $marketplace_account_id=> $marketplace_account){
			if(isset($marketplace_account['marketplace_item_id'])){
            $this->load->model('shopmanager/ebay');
			$final_quantity = $quantity + $unallocated_quantity;
           // $result[] = $this->model_shopmanager_ebay->edit($product_id, $final_quantity);
			$result[] = $this->model_shopmanager_ebay->editQuantity($marketplace_account['marketplace_item_id'], $final_quantity,null,$product_id,$marketplace_account_id,$marketplace_account['site_setting']);

			}
		}
			foreach($result as $key=>$resul) {
				if (isset($resul['Errors']) && $resul['Ack'] == 'Failure') {
					$this->error['error'] = "eBay ERROR: <br>";
			
					$errors = new RecursiveIteratorIterator(new RecursiveArrayIterator($resul['Errors']));
					foreach ($errors as $key => $value) {
						if ($key === 'LongMessage') {
							$this->error['error'] .= '************ ' . $value . '<br>';
						}
					}
			
					$this->session->data['error'] = $this->error['error'];
					$json['ebay_success'] = false;
					$json['ebay_message'] = 'Erreur lors de la mise à jour de la quantité eBay.';
				} else {
					unset($resul['Errors']);
					$json['ebay_success'] = true;
					$json['ebay_message'] = 'La quantité eBay a été mise à jour avec succès.';
				}
			}
          
       

       
        // Retourner une réponse de succès à l'interface
        $json['success'] = true;
        $json['message'] = 'Quantité mise à jour avec succès.';
    } else {
        // En cas de données manquantes, renvoyer une erreur
        $json['success'] = false;
        $json['message'] = 'Des données sont manquantes pour effectuer la mise à jour.';
    }

    // Retourner la réponse au format JSON
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}
public function updateProductLocation() {
    // Charger le modèle qui gère les produits
    $this->load->model('shopmanager/product');

    // Vérifier si les données nécessaires sont envoyées via POST
    if (isset($this->request->post['product_id']) && isset($this->request->post['location'])) {
        $product_id = (int)$this->request->post['product_id'];
        $location = $this->db->escape($this->request->post['location']); // Sécuriser l'entrée de localisation

        // Mettre à jour la localisation du produit
        $this->model_shopmanager_product->updateProductLocation($product_id, $location);

        // Retourner une réponse de succès à l'interface
        $json['success'] = true;
        $json['message'] = 'Les données du produit ont été mises à jour avec succès.';
    } else {
        // En cas de données manquantes, renvoyer une erreur
        $json['success'] = false;
        $json['message'] = 'Des données sont manquantes pour effectuer la mise à jour.';
    }

    // Retourner la réponse au format JSON
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}
// shopmanager/controller/shopmanager/product.php

public function editMadeInCountry()
{
    // Afficher les erreurs pour le débogage (à désactiver en production)
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    $json = [];

    $this->load->model("shopmanager/product");

    // Récupération des valeurs avec des valeurs de test par défaut
    $product_id = isset($this->request->post["product_id"]) ? $this->request->post["product_id"] : 3450;
    $made_in_country_id = isset($this->request->post["made_in_country_id"]) ? $this->request->post["made_in_country_id"] : '';
//    $marketplace_item_id = isset($this->request->post["marketplace_item_id"]) ? $this->request->post["marketplace_item_id"] : 296077513399;
 //   $quantity = isset($this->request->post["quantity"]) ? $this->request->post["quantity"] : 351;

    // Exécuter la mise à jour de MadeInCountry
    $results = $this->model_shopmanager_product->editMadeInCountry($product_id, $made_in_country_id);

    // Si `marketplace_item_id` est supérieur à 0, mettre à jour la quantité eBay
 /*   if ($marketplace_item_id > 0) {
        $this->load->model("shopmanager/ebay");

        // Déterminer la quantité à mettre à jour
     //   $updated_quantity = ($made_in_country_id == 44 || $made_in_country_id === null) ? 0 : $quantity;
		$updated_quantity = $quantity;

        // Mettre à jour la quantité eBay
        $this->model_shopmanager_ebay->editQuantity($marketplace_item_id, $updated_quantity,$made_in_country_id,$product_id);
		$json['success'] = true;
        $json['message'] = 'Les données du produit ont été mises à jour avec succès et sur ebay.';
    }else{*/
		$json['success'] = true;
        $json['message'] = 'Les données du produit ont été mises à jour avec succès.';
//	}

	$this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
    // Ajouter le résultat au JSON

}


public function test_walmart() {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Charger le modèle nécessaire
    $this->load->model('shopmanager/marketplace');

    // Exemple de SKU pour tester
    $product_id = "25771"; // SKU unique
    $marketplace_account_id = 3;
    $marketplace_name = "Walmart";

    // Appeler la méthode pour ajouter le produit au marketplace
    $result = $this->model_shopmanager_marketplace->addToMarketplace($product_id, $marketplace_account_id, $marketplace_name);

    // Afficher le résultat
    echo "<pre>" . print_r($result, true) . "</pre>";
}

}
