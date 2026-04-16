<?php
// Original: warehouse/inventory/label.php
namespace Opencart\Admin\Controller\Warehouse\Inventory;

class Label extends \Opencart\System\Engine\Controller {
    public function index(): void {
        if (!isset($this->request->get['user_token']) || !$this->user->isLogged()) {
            $this->response->redirect($this->url->link('common/login', '', true));
        }

        if (isset($this->request->get['type_report'])) {
            $type_report=$this->request->get['type_report'];
        }
        $this->load->language('warehouse/inventory/label');
        $data = [];
        
        $this->load->model('warehouse/'.$type_report);
        $this->load->model('tool/image'); // Pour redimensionner les images

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

		if (isset($this->request->get['filter_marketplace_account'])) {
			$filter_marketplace_account = $this->request->get['filter_marketplace_account'];
		} else {
			$filter_marketplace_account = null;
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

		if (isset($this->request->get['filter_epid'])) {
			$filter_epid = $this->request->get['filter_epid'];
		} else {
			$filter_epid = null;
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
			$limit = 20;
		}
        
        // Préparation des données pour la vue
        $data['products'] = array();

		$filter_data = array(
			'filter_sku'	  => $filter_sku,
			'filter_product_id'	  => $filter_product_id,
			'filter_marketplace_account'	  => $filter_marketplace_account,
			'filter_name'	  => $filter_name,
			'filter_category_id'	  => $filter_category_id,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
            'filter_unallocated_quantity' => $filter_unallocated_quantity,
            'filter_location' => $filter_location,
			'filter_status'   => $filter_status,
			'filter_image'    => $filter_image,
			'filter_specifics'    => $filter_specifics,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $limit,
			'limit'           => $limit //$this->config->get('config_limit_admin')
		); 
        $products = $this->{"model_warehouse_" .strtolower($type_report)}->getProducts($filter_data);

        foreach ($products as $product) {
            $data['products'][] = array(
                'product_id'           => $product['product_id'],
                'name'                 => $product['name'],
                'upc'                  => $product['upc'],
                'image'                => $product['image'] ? $this->model_tool_image->resize($product['image'], 50, 50) : '',
                'price'                => $this->currency->format($product['price'], $this->config->get('config_currency')),
                'quantity'             => $product['quantity'],
                'unallocated_quantity' => $product['unallocated_quantity'],
                'location'             => $product['location']
            );
        }

        // Traductions des colonnes
        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
        $data['column_name'] = ($lang['column_name'] ?? '');
        $data['column_upc'] = ($lang['column_upc'] ?? '');
        $data['column_image'] = ($lang['column_image'] ?? '');
        $data['column_price'] = ($lang['column_price'] ?? '');
        $data['column_quantity'] = ($lang['column_quantity'] ?? '');
        $data['column_unallocated_quantity'] = ($lang['column_unallocated_quantity'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');

        // Token pour la sécurité
        $data['user_token'] = $this->session->data['user_token'];

        // Chargement du template
        $this->response->setOutput($this->load->view('warehouse/inventory/label', $data));
    }
}
