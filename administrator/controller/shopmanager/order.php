<?php
namespace Opencart\Admin\Controller\Shopmanager;

/**
 * Class Order
 *
 * @package Opencart\Admin\Controller\Shopmanager
 */
class Order extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		
		$this->load->language('shopmanager/order');
		$data = [];
		

        	
		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else { 
			$filter_date_end = '';
		}
		
		if (isset($this->request->get['filter_order_id_start'])) {
			$filter_order_id_start = $this->request->get['filter_order_id_start'];
		} else {
			$filter_order_id_start = '';
		}

		if (isset($this->request->get['filter_order_id_end'])) {
			$filter_order_id_end = $this->request->get['filter_order_id_end'];
		} else { 
			$filter_order_id_end = '';
		}
		
		$this->document->setTitle(($lang['heading_title'] ?? ''));

        $url = '';

        if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}

        $data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('shopmanager/order', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['print_report'] = $this->url->link('shopmanager/order', 'type_report=order&user_token=' . $this->session->data['user_token'] . $url);
		$data['process'] = $this->url->link('shopmanager/order.updateQuantity', 'user_token=' . $this->session->data['user_token'] . $url);   
        
        $data['user_token'] = $this->session->data['user_token'];

        if (!isset($this->request->get['type_report'])) {
            
            $data['filter_date_start'] = $filter_date_start;
		    $data['filter_date_end'] = $filter_date_end;
		    $data['filter_order_id_start'] = $filter_order_id_start;
		    $data['filter_order_id_end'] = $filter_order_id_end;
		    $data['list'] = $this->load->controller('shopmanager/order.getList');
            $data['header'] = $this->load->controller('common/header');
            $data['column_left'] = $this->load->controller('common/column_left');
            $data['footer'] = $this->load->controller('common/footer');
            $data['wait_popup'] =  $this->load->controller('shopmanager/wait_popup');
            $data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
            $this->response->setOutput($this->load->view('shopmanager/order', $data));	 
  
        }else{
            // Print report mode - load list with filters
            $data['type_report'] = $this->request->get['type_report'];
            $data['filter_date_start'] = $filter_date_start;
		    $data['filter_date_end'] = $filter_date_end;
		    $data['filter_order_id_start'] = $filter_order_id_start;
		    $data['filter_order_id_end'] = $filter_order_id_end;
		    $data['list'] = $this->load->controller('shopmanager/order.getList');
            $this->response->setOutput($this->load->view('shopmanager/order_print_report', $data));
        }

		
		
	}

	/**
	 * List
	 *
	 * @return void
	 */
 	public function list(): void {
		$this->load->language('shopmanager/order');
		$data = [];
		

		$this->response->setOutput($this->load->controller('shopmanager/order.getList'));
	}


	/**
	 * @return string
	 */
	public function getList(): string {
	
        if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else { 
			$filter_date_end = '';
		}
		
		if (isset($this->request->get['filter_order_id_start'])) {
			$filter_order_id_start = $this->request->get['filter_order_id_start'];
		} else {
			$filter_order_id_start = '';
		}

		if (isset($this->request->get['filter_order_id_end'])) {
			$filter_order_id_end = $this->request->get['filter_order_id_end'];
		} else { 
			$filter_order_id_end = '';
		}

        $url = '';

        if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}

		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
		
		if (isset($this->request->get['filter_order_id_start'])) {
			$url .= '&filter_order_id_start=' . $this->request->get['filter_order_id_start'];
		}

		if (isset($this->request->get['filter_order_id_end'])) {
			$url .= '&filter_order_id_end=' . $this->request->get['filter_order_id_end'];
		}

	    $data['action'] = $this->url->link('shopmanager/order.list', 'user_token=' . $this->session->data['user_token'] . $url);

        $this->load->model('shopmanager/order');
        $this->load->model('shopmanager/tools');

		$data['orders'] = [];

        $filter_data = [
			'filter_date_start' => $filter_date_start,
			'filter_date_end'   => $filter_date_end,
			'filter_order_id_start' => $filter_order_id_start,
			'filter_order_id_end'   => $filter_order_id_end
		];

		$orders = $this->model_shopmanager_order->getOrders($filter_data);

		foreach ($orders as $order) {
			// print("<pre>".print_r ($order,true )."</pre>"); 
			// die();
			$data['orders'][] = [
				'sales_record_number'   => $order['sales_record_number'],
				'image'                 => $order['image'] ?? '',
				'listing_id'            => $order['listing_id'] ?? '',
				'sku'                   => $order['sku'],
				'customlabel'           => $order['customlabel'],
				'customer'              => $order['customer'],
				'adress'                => $order['adress'] . ' ' . $order['city'],
				'adress2'               => $order['zipcode'] . ' ' . $order['state'] . ' ' . $order['country'],
				'name'                  => $order['name'],
				'needed_quantity'       => $order['needed_quantity'],
				'price'                 => $order['price'],
				'product_id'            => $order['product_id'],
				'quantity'              => $order['quantity'],
				'location'              => $order['location'],
				'unallocated_quantity'  => $order['unallocated_quantity'],
				'total'                 => $order['total'],
				'order_id'              => $order['order_id'],
				'country'               => $order['country'],
				'platform'              => $order['platform'],
				'transaction_site_id'   => $order['transaction_site_id'],
				'com'                   => $order['com'],
			];
		}

		$sortCriteria = [
			'sales_record_number' => [SORT_ASC, SORT_NUMERIC]
		];

		$data['orders'] = $this->model_shopmanager_tools->MultiSort($data['orders'], $sortCriteria, true);

		$sortCriteria = [
			'sku' => [SORT_ASC, SORT_NUMERIC]
		];

		$orders_sorted = $this->model_shopmanager_tools->MultiSort($data['orders'], $sortCriteria, true);
		$orders_sorted = $this->model_shopmanager_tools->MultiSort($data['orders'], $sortCriteria, true);

		$temp_array = [];
		$order_count = []; // Track how many orders per SKU
		
		foreach ($orders_sorted as $order_sorted) {
			$sku = $order_sorted['sku'] ?? null;
		
			if (!$sku) {
				continue; // Ignore les entrées sans SKU
			}
		
			if (!isset($temp_array[$sku])) {
				// Première occurrence du SKU : on stocke l'entrée complète
				$temp_array[$sku] = $order_sorted;
				$order_count[$sku] = 1;
			} else {
				// Si le SKU existe déjà, on additionne les quantités
				$temp_array[$sku]['needed_quantity'] += $order_sorted['needed_quantity'];
				$order_count[$sku]++;
			}
		}
		
		// Add order count to each item
		foreach ($temp_array as $sku => &$item) {
			$item['order_count'] = $order_count[$sku];
		}
		
		// Convertir en tableau indexé pour la suite du traitement
		$orders_sorted = array_values($temp_array);
		
		$sortCriteria = [
			'location' => [SORT_ASC, SORT_STRING]
		];
		
		$data['orders_sorted'] = $this->model_shopmanager_tools->MultiSort($orders_sorted, $sortCriteria, true);

        $data['user_token'] = $this->session->data['user_token'];
		
		$url = '';

        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
        }


        return $this->load->view('shopmanager/order_list', $data);
		
	}



	/**
	 * Update Product Quantity
	 *
	 * @return void
	 */
	public function updateQuantity(): void {
		$json = [];

		$this->load->language('shopmanager/order');
		$data = [];
		

		if (!$this->user->hasPermission('modify', 'shopmanager/order')) {
			$json['error']['warning'] = ($lang['error_permission'] ?? '');
		}

		$this->load->model('shopmanager/catalog/product');
		$this->load->model('shopmanager/order');

		// Vérifier si les données nécessaires sont envoyées via POST
		if (isset($this->request->post['vendu']) && is_array($this->request->post['vendu'])) {
			// print("<pre>".print_r ($this->request->post,true )."</pre>");
			// Mettre à jour la quantité du produit dans la base de données
			$this->model_shopmanager_order->updateQuantity($this->request->post);
            $json['success'] = ($lang['text_success'] ?? '');
			// Si marketplace_item_id n'est pas nul ou 0, mettre à jour la quantité sur eBay
			//$this->response->redirect($this->url->link('shopmanager/order', 'user_token=' . $this->session->data['user_token']));
		} else {
			// En cas de données manquantes, renvoyer une erreur
			 $json['error']['warning'] = ($lang['error_missing_data'] ?? '');
		}

		// Retourner la réponse au format JSON
		 $this->response->addHeader('Content-Type: application/json');
		 $this->response->setOutput(json_encode($json));
	}

	/**
	 * Undo Product Quantity (reverse of updateQuantity)
	 *
	 * @return void
	 */
	public function undoProductQuantity(): void {
		$json = [];

		$this->load->language('shopmanager/order');
		$data = [];
		

		if (!$this->user->hasPermission('modify', 'shopmanager/order')) {
			$json['error']['warning'] = ($lang['error_permission'] ?? '');
		}

		$this->load->model('shopmanager/catalog/product');
		$this->load->model('shopmanager/order');

		// Vérifier si les données nécessaires sont envoyées via POST
		if (isset($this->request->post['vendu']) && is_array($this->request->post['vendu'])) {
			// Mettre à jour la quantité du produit dans la base de données (mode undo)
			$this->model_shopmanager_order->undoProductQuantity($this->request->post);
            $json['success'] = ($lang['text_success_undo'] ?? '');
		} else {
			// En cas de données manquantes, renvoyer une erreur
			$json['error']['warning'] = ($lang['error_missing_data'] ?? '');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
