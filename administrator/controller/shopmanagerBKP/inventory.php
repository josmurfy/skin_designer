<?php
class ControllerShopmanagerInventory extends Controller {
    public function index() {
        $this->getList();
    }

    protected function getList() {
		ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->language('shopmanager/inventory');
        $this->load->model('shopmanager/inventory');
        $this->document->addScript('view/javascript/shopmanager/inventory_list.js');
		$this->document->addScript('view/javascript/shopmanager/tools.js');
		$this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
		$this->document->addScript('view/javascript/shopmanager/alert_popup.js');


        if (isset($this->request->get['filter_sku'])) {
			$filter_sku = $this->request->get['filter_sku'];
		}else {
			$filter_sku = null;
		}


		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = null;
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

	

		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = null;
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
			$limit = 1000;
		}

		$url = '';

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}


		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
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
			$data['limit'] = $this->request->get['limit'];
		}else{
			$url .= '&limit=1000';
			$data['limit'] = 1000;
		}
        $data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . $url, true)
		);
        $data['products'] = array();

		$filter_data = array(
			'filter_sku'	  => $filter_sku,
			'filter_category_id'	  => $filter_category_id,
			'filter_quantity' => $filter_quantity,
            'filter_unallocated_quantity' => $filter_unallocated_quantity,
            'filter_location' => $filter_location,
			'filter_status'   => $filter_status,
			'filter_image'    => $filter_image,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit //$this->config->get('config_limit_admin')
		); 
		
		$this->load->model('tool/image');
        $product_total = $this->model_shopmanager_inventory->getTotalProducts($filter_data);
      //print("<pre>".print_r (164 , true )."</pre>");
      //print("<pre>".print_r ($product_total ,true )."</pre>");
		$results = $this->model_shopmanager_inventory->getProducts($filter_data);
        foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 75, 75);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 75, 75);
			}



			
			$data['products'][] = array(
				'product_id' => $result['product_id'],
                'sku' => $result['sku'],
				'image'      => $image,
				'name'       => $result['name'],
				'quantity'   => $result['quantity'],
                'unallocated_quantity'   => $result['unallocated_quantity'],
                'total_quantity'    =>  $result['unallocated_quantity']+$result['quantity'],
                'location'   => $result['location'],  
				'status_id'   => $result['status'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
			);
		}
    
        
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_list'] = $this->language->get('text_list');
        $data['column_sku'] = $this->language->get('column_sku');
        $data['column_image'] = $this->language->get('column_image');

        
        $data['column_name'] = $this->language->get('column_name');
        $data['column_total_quantity'] = $this->language->get('column_total_quantity');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_unallocated_quantity'] = $this->language->get('column_unallocated_quantity');
        $data['column_location'] = $this->language->get('column_location');

        $data['entry_sku'] = $this->language->get('entry_sku');
        $data['entry_category_id'] = $this->language->get('entry_category_id');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_total_quantity'] = $this->language->get('entry_total_quantity');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_unallocated_quantity'] = $this->language->get('entry_unallocated_quantity');
        $data['entry_location'] = $this->language->get('entry_location');
        $data['entry_new_location'] = $this->language->get('entry_new_location');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['entry_limit'] = $this->language->get('entry_limit');

		$data['per_page_options']=[20, 50, 100, 200];

        $data['entry_sku'] = $this->language->get('entry_sku');
        $data['entry_category_id'] = $this->language->get('entry_category_id');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_image'] = $this->language->get('entry_image');
        $data['entry_limit'] = $this->language->get('entry_limit');
        $data['button_submit'] = $this->language->get('button_submit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_missing_image_file'] = $this->language->get('text_missing_image_file');

        // Exemples pour les filtres (si vous en avez besoin)
        $data['filter_sku'] = $this->request->get['filter_sku'] ?? '';
        $data['filter_category_id'] = $this->request->get['filter_category_id'] ?? '';
        $data['filter_quantity'] = $this->request->get['filter_quantity'] ?? '';
        $data['filter_status'] = $this->request->get['filter_status'] ?? '*';
        $data['filter_image'] = $this->request->get['filter_image'] ?? '*';

        $data['action'] = $this->url->link('shopmanager/inventory/transfert', 'token=' . $this->session->data['token']. $url,true);
  //      $data['action'] = $this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);

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

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace_account'])) {
			$url .= '&filter_marketplace_account=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_condition_id'] = $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . '&sort=p.condition_id' . $url, true);
		$data['sort_sku'] = $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . '&sort=p.sku' . $url, true);
		$data['sort_name'] = $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . '&sort=pd.name' . $url, true);
		$data['sort_quantity'] = $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, true);
        $data['sort_unallocated_quantity'] = $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . '&sort=p.unallocated_quantity' . $url, true);
		$data['sort_location'] = $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . '&sort=p.location' . $url, true);
		$data['sort_order'] = $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace_account'])) {
			$url .= '&filter_marketplace_account=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account'], ENT_QUOTES, 'UTF-8'));
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

		

		$pagination = new Pagination();
		$pagination->total = $product_total;
		$pagination->page = $page;
		$pagination->limit = $limit; //$this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('shopmanager/inventory',  'token=' . $this->session->data['token'] . $url . '&page={page}&limit='.$limit , true);
		$data['limit_link'] = $this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'] . $url . '&page={page}&limit=', true);
		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

		$data['filter_sku'] = $filter_sku;
	
	
		$data['filter_category_id'] = $filter_category_id;
	
		$data['filter_quantity'] = $filter_quantity;
        $data['filter_unallocated_quantity'] = $filter_unallocated_quantity;
        $data['filter_location'] = $filter_location;
		$data['filter_status'] = $filter_status;
	
		$data['filter_image'] = $filter_image;

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;
        // Charger header, footer, et colonne gauche
        $data['header'] = $this->load->controller('common/header');
        $data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');
        $data['column_left'] = $this->load->controller('common/column_left');

        $this->response->setOutput($this->load->view('shopmanager/inventory_list', $data));
    }

    public function transfert() {
        $this->load->language('shopmanager/inventory');

		$this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('shopmanager/inventory');
        //print("<pre>".print_r (444 , true )."</pre>");
        //print("<pre>".print_r ($this->request->post ,true )."</pre>");
        if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['product_id'])) {

          
            foreach ($this->request->post['product_id'] as $product_id) {
                $new_location = $this->request->post['new_location'];
                $old_location = $this->request->post['old_location'][$product_id] ?? '';
                $quantity = $this->request->post['quantity'][$product_id];
                $unallocated_quantity = $this->request->post['unallocated_quantity'][$product_id];
            //    echo "<pre>";
           //     echo "Product ID: " . print_r($product_id, true) . "\n";
           //     echo "New Location: " . print_r($new_location, true) . "\n";
           //     echo "Old Location: " . print_r($old_location, true) . "\n";
           //     echo "Quantity: " . print_r($quantity, true) . "\n";
           //     echo "Unallocated Quantity: " . print_r($unallocated_quantity, true) . "\n";
            //    echo "</pre>";
                $this->model_shopmanager_inventory->updateProductLocation($product_id, $new_location, $old_location, $quantity, $unallocated_quantity);
         //print("<pre>".print_r ($this->model_shopmanager_inventory->updateProductLocation($product_id, $new_location, $old_location, $quantity, $unallocated_quantity),true )."</pre>");

            }
        }
        $this->response->redirect($this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'], true));
    }

    public function getTrimmedList() {
        $this->load->model('shopmanager/inventory');
        //print("<pre>".print_r (459 , true )."</pre>");
        //print("<pre>".print_r ($this->request->post ,true )."</pre>");
        $products = $this->model_shopmanager_inventory->getTrimmedProducts();

        foreach ($products as $product) {
            $new_loc = $product['quantity'] > 0 ? $product['location'] : '';
        //    $this->model_shopmanager_inventory->updateProductLocation($product['product_id'], $new_loc, $product['location'], 0);
        }
        $this->response->redirect($this->url->link('shopmanager/inventory', 'token=' . $this->session->data['token'], true));
    }
}

