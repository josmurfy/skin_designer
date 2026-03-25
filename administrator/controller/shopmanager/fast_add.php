<?php
namespace Opencart\Admin\Controller\Shopmanager;

class FastAdd extends \Opencart\System\Engine\Controller {
    private $error = array();

    // Méthode principale : index
    public function index() {
        $lang = $this->load->language('shopmanager/fast_add');
        $data = $data ?? [];
        $data += $lang;
        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('shopmanager/catalog/product');

        $this->getList();
    }
	public function delete() {
		$lang = $this->load->language('shopmanager/catalog/product');
		$data = $data ?? [];
		$data += $lang;

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		$this->load->model('shopmanager/catalog/product');
		$this->load->model('shopmanager/tools');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
		//	//print("<pre>".print_r ($this->request->post['selected'] ,true )."</pre>");
		//	echo count($this->request->post['selected']);
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_shopmanager_catalog_product->deleteProduct($product_id);
				$this->model_shopmanager_tools->deleteProductImagesFiles($product_id);
				
				
			}

			$this->session->data['success'] = ($lang['text_success'] ?? '');

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

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			

			$this->response->redirect($this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	public function enable() {
		$lang = $this->load->language('shopmanager/catalog/product');
		$data = $data ?? [];
		$data += $lang;

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		$this->load->model('shopmanager/catalog/product');

		if (isset($this->request->post['selected'])) {
		//	//print("<pre>".print_r ($this->request->post['selected'] ,true )."</pre>");
		//	echo count($this->request->post['selected']);
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_shopmanager_catalog_product->enableProduct($product_id);
			}

			$this->session->data['success'] = ($lang['text_success'] ?? '');

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

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->response->redirect($this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	public function disable() {
		$lang = $this->load->language('shopmanager/catalog/product');
		$data = $data ?? [];
		$data += $lang;

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		$this->load->model('shopmanager/catalog/product');

		if (isset($this->request->post['selected'])) {
		//	//print("<pre>".print_r ($this->request->post['selected'] ,true )."</pre>");
		//	echo count($this->request->post['selected']);
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_shopmanager_catalog_product->enableProduct($product_id,0);
			}

			$this->session->data['success'] = ($lang['text_success'] ?? '');

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

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (isset($this->request->get['limit'])){
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$this->response->redirect($this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	public function copy() {
		$lang = $this->load->language('shopmanager/catalog/product');
		$data = $data ?? [];
		$data += $lang;

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		$this->load->model('shopmanager/catalog/product');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_shopmanager_catalog_product->copyProduct($product_id);
			}

			$this->session->data['success'] = ($lang['text_success'] ?? '');

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

			$this->response->redirect($this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'shopmanager/catalog/product')) {
			$this->error['warning'] = ($lang['error_permission'] ?? '');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'shopmanager/catalog/product')) {
			$this->error['warning'] = ($lang['error_permission'] ?? '');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('shopmanager/catalog/product');
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

			if (isset($this->request->get['filter_marketplace_account'])) {
				$filter_marketplace_account = $this->request->get['filter_marketplace_account'];
			} else {
				$filter_marketplace_account = '';
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
				'filter_marketplace_account' 	=> $filter_marketplace_account,
				'filter_name'  		=> $filter_name,
				'filter_category_id' 		=> $filter_category_id,
				'filter_model' 		=> $filter_model,
				'start'        		=> 0,
				'limit'        		=> $limit
			);

			$results = $this->model_shopmanager_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_shopmanager_catalog_product->getProductOptions($result['product_id']);

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

    // Méthode : getList
	protected function getList() {

		ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

		$this->document->addScript('view/javascript/shopmanager/fast_add.js');
		$this->document->addScript('view/javascript/shopmanager/fast_add_list.js');
		$this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
		$this->document->addScript('view/javascript/shopmanager/alert_popup.js');

	//	$this->document->addScript('view/javascript/fontawesome/css/fontawesome.css');
		
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
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['product_search'] = $this->url->link('shopmanager/fast_add.product_search', 'user_token=' . $this->session->data['user_token']. $url, true);
	
		$data['print_report'] = $this->url->link('shopmanager/print_report', 'type_report=fast_add&user_token=' . $this->session->data['user_token'] . $url, true);
		$data['add'] = $this->url->link('shopmanager/fast_add.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('shopmanager/fast_add.copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('shopmanager/fast_add.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['enable'] = $this->url->link('shopmanager/fast_add.enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['disable'] = $this->url->link('shopmanager/fast_add.disable', 'user_token=' . $this->session->data['user_token'] . $url, true);

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
		
		$this->load->model('tool/image');
		$this->load->model('shopmanager/fast_add');

	
		$product_total = $this->model_shopmanager_catalog_product->getTotalProducts($filter_data);

		
		$results = $this->model_shopmanager_catalog_product->getProducts($filter_data);

		
		
		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_shopmanager_fast_add->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $product_special['price'];

					break;
				}
			}
//print("<pre>".print_r ($result['marketplace_accounts_id'] ,true )."</pre>");
		//$this->load->model('shopmanager/catalog/category');
		$this->load->model('shopmanager/marketplace');
		$data['marketplace_accounts']= $this->model_shopmanager_marketplace->getMarketplaceAccount(['customer_id' => 10 ]);
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
			
					$result['marketplace_accounts_id'][$marketplace_id]['thumb'] = $this->model_tool_image->resize($newImage, 100, 100);
				
			
		}
		
			$this->load->model('shopmanager/catalog/category');
			$categorySpecifics = $this->model_shopmanager_catalog_category->getSpecific($result['category_id'], (int)$this->config->get('config_language_id'));
		
		//print("<pre>".print_r($categorySpecifics, true)."</pre>");

			$categorySpecifics=$categorySpecifics[(int)$this->config->get('config_language_id')]['specifics']??[];
			
			$total_specifics_count = count($categorySpecifics);
			$filled_specifics_count = 0;
			
			if (!empty($result['specifics'])) {
				$product_specifics = json_decode($result['specifics'], true);
				foreach ($product_specifics as $key => $value) {
				//	//print("<pre>".print_r($value, true)."</pre>");
					if (!empty($value['Actual_value'])) {
						$filled_specifics_count++;
					}
				}
			}
			$data['products'][] = array('product_id' => $result['product_id'],
				'condition_id' => $result['condition_id'],
				'category_id' => $result['category_id'],
				'made_in_country_id' => $result['made_in_country_id'],
				'condition_name' => $result['condition_name'],
				'marketplace_accounts_id' => $result['marketplace_accounts_id'], 
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'price'      => round($result['price'],2),
				'special'    => $special,
				'quantity'   => $result['quantity'],
                'unallocated_quantity'   => $result['unallocated_quantity'],
                'location'   => $result['location'],  
				'status_id'   => $result['status'],
				'has_sources'   => $result['has_sources'] ? ($lang['text_sources_set'] ?? '') : ($lang['text_sources_not_set'] ?? ''),
				'has_specifics'   => $result['has_specifics'] ? ($lang['text_specifics_set'] ?? '') : ($lang['text_specifics_not_set'] ?? ''),
				'filled_specifics_count' => $filled_specifics_count,
				'total_specifics_count' => $total_specifics_count,
				'status'     => $result['status'] ? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? ''),
				//'edit'       => ($result['upc']=='' || $result['has_specifics']) ? $this->url->link('shopmanager/catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true) : $this->url->link('shopmanager/catalog/product_search', 'user_token=' . $this->session->data['user_token'] . '&upc='.$result['upc'].'&product_id=' . $result['product_id']. '&condition_id=' . $result['condition_id'] , true)
				'edit'       => $result['has_specifics'] ? $this->url->link('shopmanager/catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true) : $this->url->link('shopmanager/catalog/product_search', 'user_token=' . $this->session->data['user_token'] . '&upc='.$result['upc'].'&product_id=' . $result['product_id']. '&condition_id=' . $result['condition_id'] . $url, true)
			);
		}

		$data['heading_title'] = ($lang['heading_title'] ?? '');

		$data['text_list'] = ($lang['text_list'] ?? '');
		$data['text_enabled'] = ($lang['text_enabled'] ?? '');
		$data['text_disabled'] = ($lang['text_disabled'] ?? '');
		$data['text_no_results'] = ($lang['text_no_results'] ?? '');
		$data['text_confirm'] = ($lang['text_confirm'] ?? '');
		$data['text_missing_image_file'] = ($lang['text_missing_image_file'] ?? '');
		$data['text_specifics_not_set'] = ($lang['text_specifics_not_set'] ?? '');
		$data['text_specifics_set'] = ($lang['text_specifics_set'] ?? '');
		$data['text_specifics_error'] = ($lang['text_specifics_error'] ?? '');
		

		$data['column_image'] = ($lang['column_image'] ?? '');
		$data['column_condition_id'] = ($lang['column_condition_id'] ?? '');
		$data['column_marketplace_item_id'] = ($lang['column_marketplace_item_id'] ?? '');
		$data['column_name'] = ($lang['column_name'] ?? '');
		$data['column_model'] = ($lang['column_model'] ?? '');
		$data['column_price'] = ($lang['column_price'] ?? '');
		$data['column_quantity'] = ($lang['column_quantity'] ?? '');
        $data['column_unallocated_quantity'] = ($lang['column_unallocated_quantity'] ?? '');
        $data['column_location'] = ($lang['column_location'] ?? '');
		$data['column_status'] = ($lang['column_status'] ?? '');
		$data['column_action'] = ($lang['column_action'] ?? '');
        $data['column_product_id'] = ($lang['column_product_id'] ?? '');
		$data['column_specifics'] = ($lang['column_specifics'] ?? '');
		$data['column_sources'] = ($lang['column_sources'] ?? '');

		$data['entry_sku'] = ($lang['entry_sku'] ?? '');
		$data['entry_product_id'] = ($lang['entry_product_id'] ?? '');
		$data['entry_name'] = ($lang['entry_name'] ?? '');
		$data['entry_specifics'] = ($lang['entry_specifics'] ?? '');


		$data['entry_condition_id'] = ($lang['entry_condition_id'] ?? '');
		$data['entry_marketplace_account_id'] = ($lang['entry_marketplace_account_id'] ?? '');
		$data['entry_category_id'] = ($lang['entry_category_id'] ?? '');
		$data['entry_model'] = ($lang['entry_model'] ?? '');
		$data['entry_upc']   = ($lang['entry_upc'] ?? '');
		$data['entry_price'] = ($lang['entry_price'] ?? '');
		$data['entry_price_with_shipping'] = ($lang['entry_price_with_shipping'] ?? '');
		$data['entry_unallocated_quantity'] = ($lang['entry_unallocated_quantity'] ?? '');
		$data['entry_quantity'] = ($lang['entry_quantity'] ?? '');
       
        $data['entry_location'] = ($lang['entry_location'] ?? '');
		$data['entry_status'] = ($lang['entry_status'] ?? '');
		$data['entry_image'] = ($lang['entry_image'] ?? '');

		$data['button_copy'] = ($lang['button_copy'] ?? '');
		$data['button_add'] = ($lang['button_add'] ?? '');
		$data['button_edit'] = ($lang['button_edit'] ?? '');
		$data['button_delete'] = ($lang['button_delete'] ?? '');
		$data['button_enable'] = ($lang['button_enable'] ?? '');
		$data['button_disable'] = ($lang['button_disable'] ?? '');
		$data['button_print'] = ($lang['button_print'] ?? '');
		$data['button_filter'] = ($lang['button_filter'] ?? '');

		$data['button_product_search'] = ($lang['button_product_search'] ?? '');

		$data['entry_limit'] = ($lang['entry_limit'] ?? '');

       


		$data['per_page_options']=[20, 50, 100, 200];



		$data['user_token'] = $this->session->data['user_token'];

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

		$data['sort_condition_id'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.condition_id' . $url, true);
		$data['sort_marketplace_item_id'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.marketplace_item_id' . $url, true);
		$data['sort_name'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_price'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
        $data['sort_unallocated_quantity'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.unallocated_quantity' . $url, true);
		$data['sort_location'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.location' . $url, true);
		$data['sort_sources'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=has_sources' . $url, true);
		$data['sort_specifics'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=has_specifics' . $url, true);
        $data['sort_status'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

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

		

		$data['limit_link'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}&limit=', true);
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}&limit=' . $limit, true)
		]);

		$data['results'] = sprintf(($lang['text_pagination'] ?? ''), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));

		$data['filter_sku'] = $filter_sku;
		$data['filter_product_id'] = $filter_product_id;
		$data['filter_marketplace_account'] = $filter_marketplace_account;
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

		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['limit'] = $limit;

		// JS text strings for fast_add_list.js
		$data['text_location_empty']         = ($lang['text_location_empty'] ?? '');
		$data['text_error_update_prefix']    = ($lang['text_error_update_prefix'] ?? '');
		$data['text_error_api']              = ($lang['text_error_api'] ?? '');
		$data['text_confirm_qty_transfer']   = ($lang['text_confirm_qty_transfer'] ?? '');
		$data['text_error_update']           = ($lang['text_error_update'] ?? '');
		$data['text_invalid_unallocated_qty']= ($lang['text_invalid_unallocated_qty'] ?? '');
		$data['text_error_qty_update']       = ($lang['text_error_qty_update'] ?? '');
		$data['text_error_location_update']  = ($lang['text_error_location_update'] ?? '');
		$data['text_invalid_qty']            = ($lang['text_invalid_qty'] ?? '');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/alert_popup');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		//print("<pre>".print_r ($data ,true )."</pre>");
	/*	if(count($results)==1 && !isset($this->request->get['updated']) ){
			
			$this->response->redirect($result['has_specifics'] ? $this->url->link('shopmanager/fast_add.edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true) : $this->url->link('shopmanager/fast_add.product_search', 'user_token=' . $this->session->data['user_token'] . '&upc='.$result['sku'].'&product_id=' . $result['product_id']. '&condition_id=' . $result['condition_id'] . $url, true));
		}else{*/
			$this->response->setOutput($this->load->view('shopmanager/fast_add_list', $data));
	//	}
		
	}

    // Méthode : getForm
    protected function getForm() {
        $this->document->addScript('view/javascript/shopmanager/fast_add_form.js');
        $this->document->addScript('view/javascript/shopmanager/condition.js');
        $data['heading_title'] = ($lang['heading_title'] ?? '');

        $data['button_save'] = ($lang['button_save'] ?? '');
	    $data['button_cancel'] = ($lang['button_cancel'] ?? '');
        $data['entry_category'] = ($lang['entry_category'] ?? '');
	    $data['entry_location'] = ($lang['entry_location'] ?? '');
        $data['entry_unallocated_quantity'] = ($lang['entry_unallocated_quantity'] ?? '');
        $data['entry_condition'] = ($lang['entry_condition'] ?? '');
        $data['entry_category'] = ($lang['entry_category'] ?? '');
        $data['entry_category'] = ($lang['entry_category'] ?? '');
        $data['help_category'] = ($lang['help_category'] ?? '');
        $data['error_invalid_upc'] = ($lang['error_invalid_upc'] ?? '');

        

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

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])){
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
        // Définir les boutons d'action
		$data['action'] = !isset($this->request->get['product_id']) ? $this->url->link('shopmanager/fast_add.add', 'user_token=' . $this->session->data['user_token'], true) : $this->url->link('shopmanager/fast_add.edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $this->request->get['product_id'], true);
        $data['cancel'] = $this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'], true);

        $data['user_token'] = $this->session->data['user_token'];

        $this->load->model('localisation/language');

     //   $current_language = $this->config->get('config_language'); 
    //print("<pre>".print_r ($current_language,true )."</pre>");

		$current_language = (string)$this->config->get('config_language');
		$data['languages'] = $this->model_localisation_language->getLanguageByCode($current_language);

		if (!$data['languages']) {
			$data['languages'] = $this->model_localisation_language->getLanguageByCode('en-gb');
		}

		if (!$data['languages']) {
			$data['languages'] = $this->model_localisation_language->getLanguageByCode('en');
		}

		$data['language_id'] = $data['languages']['language_id'] ?? (int)$this->config->get('config_language_id');
    //print("<pre>".print_r ( $data['languages'],true )."</pre>");


        if (isset($this->request->get['product_id'])) {
            $product_info = $this->model_shopmanager_catalog_product->getProduct($this->request->get['product_id']);
			$this->load->model('shopmanager/marketplace');
			$product_info['marketplace_accounts_id'] = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $this->request->get['product_id']]);

        }else{
            $product_info= null;
        }
      //print("<pre>".print_r ($this->request->post ,true )."</pre>");
        if (isset($this->request->post['category_id'])) {
			$data['category_id'] = $this->request->post['category_id'];
       //     $data['location'] = 'a';
		} elseif (!empty($product_info)) {
			$data['category_id'] = $product_info['category_id'];
     //       $data['location'] = 'b';
		} else {
			$data['category_id'] = null;
		} 

		if (isset($this->request->post['condition_id'])) {
			$data['condition_id'] = $this->request->post['condition_id'];
       //     $data['location'] = 'a';
		} elseif (!empty($product_info)) {
			$data['condition_id'] = $product_info['condition_id'];
     //       $data['location'] = 'b';
		} else {
			$data['condition_id'] = null;
		} 

        if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
       //     $data['location'] = 'a';
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
     //       $data['location'] = 'b';
		} else {
			$data['location'] = '';
		} 
       // $data['location'] = '';
        if (isset($this->request->post['unallocated_quantity'])) {
			$data['unallocated_quantity'] = $this->request->post['unallocated_quantity'];
		} elseif (!empty($product_info)) {
			$data['unallocated_quantity'] = $product_info['unallocated_quantity'];
		} else {
			$data['unallocated_quantity'] = 0;
		}
        if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_shopmanager_catalog_product->getProductCategories($this->request->get['product_id']);
		} else {
		
			$categories = array();
		}
		$data['product_categories'] = array();
		$this->load->model('shopmanager/catalog/category');

		foreach ($categories as $category_id) {
		//	//print("<pre>".print_r ($category_id,true )."</pre>");
			$category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
			//print("<pre>".print_r ($category_info,true )."</pre>");
			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name_category' => $category_info['name'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
			/*	if( $category_info['leaf']=='1'){
					$category_specific=$category_info['category_id'];
				}*/
			}
		}
        $this->load->model('shopmanager/condition');

		if(isset($data['category_id'])){
					// Conditions
			$data['conditions']=$this->model_shopmanager_condition->getConditionDetails($data['category_id']);
			
			$data['conditions']=$data['conditions'][1];
		//	//print("<pre>".print_r ($data['conditions'],true )."</pre>");
        }else{
            $this->error['category']= ($lang['error_category_not_leaf'] ?? '');
            $data['conditions'] =  null;
           
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

		if (isset($this->session->data['error'])) {
			$data['error'] = $this->session->data['error'];

			unset($this->session->data['error']);
		} else {
			$data['error'] = '';
		}

		if (isset($this->session->data['warning'])) {
			$data['warning'] = $this->session->data['warning'];

			unset($this->session->data['warning']);
		} else {
			$data['warning'] = '';
		}
        $data['name'] = isset($product_info) ? $product_info['name'] : '';
        $data['quantity'] = isset($product_info) ? $product_info['quantity'] : 0;
        $data['unallocated_quantity'] = isset($product_info) ? $product_info['unallocated_quantity'] : 0;
        $data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
$data['alert_popup'] = $this->load->controller('shopmanager/alert_popup');

                $data['text_invalid_upc']        = ($lang['text_invalid_upc'] ?? '');
        $data['text_category_not_found'] = ($lang['text_category_not_found'] ?? '');
        $this->response->setOutput($this->load->view('shopmanager/fast_add_form', $data));
    }

  
	public function add() {

		ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
		$lang = $this->load->language('shopmanager/fast_add');
		$data = $data ?? [];
		$data += $lang;
		$this->document->setTitle(($lang['heading_title'] ?? ''));
	
		$this->load->model('shopmanager/catalog/product');
		$this->load->model('shopmanager/fast_add');
		$this->load->model('shopmanager/tools');
		
	
		// Initialiser la variable $product_id à null
		$product_id = null;
	
		// Vérifier si le formulaire a été soumis en POST et valider les champs
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$url = '';
	
			// Vérifier si le produit existe déjà avec l'UPC et la condition donnée
			$data = $this->model_shopmanager_fast_add->checkProductExists($this->request->post['upc'], $this->request->post['condition_id']);
			if (isset($data) && $data) {
				// Incrémenter la quantité si le produit existe déjà
				$this->model_shopmanager_fast_add->incrementQuantity($data['product_id'], $this->request->post['unallocated_quantity']);
				
				if ($data['total_quantity'] < 1) {
					$this->model_shopmanager_catalog_product->editProductSku($data['product_id']);
					$sku = $data['product_id'];
				} else {
					$sku = $data['sku'];
				}
	
				$product_id = $data['product_id'];
				$this->session->data['warning'] = ($lang['text_success_add_quantity'] ?? '');
			} else {
				// Générer un SKU aléatoire si non fourni
				$this->request->post['sku'] = rand(10000000, 99999999);
				
				// Remplir les champs par défaut si nécessaire
				$this->request->post = $this->model_shopmanager_fast_add->populateDefaultValues($this->request->post);
				
				// Ajouter le produit à la base de données
				$product_id = $this->model_shopmanager_catalog_product->addProduct($this->request->post);
				
				// Mettre à jour le SKU avec l'ID du produit si nécessaire
				$this->model_shopmanager_catalog_product->editProductSku($product_id);
				$sku = $product_id;
				$this->session->data['success'] = ($lang['text_success_add_product'] ?? '');
			}
	
			// Si un product_id est défini, ouvrir la fenêtre d'impression des étiquettes
			if (($product_id &&  $this->request->post['condition_id']!='1000') || $this->request->post['upc'] == '') {
				$upc = $this->request->post['upc'];
				$quantity = $this->request->post['unallocated_quantity'];
				
				// Construire le script JavaScript pour ouvrir la fenêtre d'impression
				echo '<script>
					window.open("index.php?route=shopmanager/tools.create_label&sku=' . $sku . '&quantity=' . $quantity . '&upc=' . $upc . '&user_token=' . $this->session->data['user_token'] . '", "printWindow", "width=288,height=96");
				</script>';		
		
		}
			
			// Rediriger vers la page de succès ou de liste si nécessaire
			// $this->response->redirect($this->url->link('shopmanager/fast_add/add', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
	
		$this->getForm();
	}
	
    // Méthode : edit (Modification d'un produit existant)
    public function edit() {
        $lang = $this->load->language('shopmanager/fast_add');
        $data = $data ?? [];
        $data += $lang;
        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('shopmanager/catalog/product');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $this->model_shopmanager_catalog_product->editProduct($this->request->get['product_id'], $this->request->post);

            $this->session->data['success'] = ($lang['text_success'] ?? '');
            $this->response->redirect($this->url->link('shopmanager/fast_add', 'user_token=' . $this->session->data['user_token'], true));
        }

        $this->getForm();
    }

    // Méthode de validation du formulaire
    protected function validateForm() {
        if (!$this->user->hasPermission('modify', 'shopmanager/fast_add')) {
            $this->error['warning'] = ($lang['error_permission'] ?? '');
        }

        if ((utf8_strlen($this->request->post['upc']) < 1) || (utf8_strlen($this->request->post['upc']) > 14)) {
            $this->error['upc'] = ($lang['error_upc'] ?? '');
        }

        if ((($this->request->post['unallocated_quantity']) < 1) || (($this->request->post['unallocated_quantity']) == '')) {
            $this->error['unallocated_quantity'] = ($lang['error_unallocated_quantity'] ?? '');
        }

		if (!isset($this->request->post['product_category'][0])) {
            $this->error['product_category'] = ($lang['error_product_category'] ?? '');
        }

		if (!isset($this->request->post['condition_id'])) {
            $this->error['condition_id'] = ($lang['error_condition_id'] ?? '');
        }

        return !$this->error;
    }
	public function product_source_info_feed() {

		$execution_times = [];
		$n=0;

		
		// Charger le modèle
		$start_time = microtime(true);

		$this->load->model('shopmanager/catalog/product');
		$this->load->model('shopmanager/catalog/product_search');
		$this->load->model('shopmanager/catalog/product');
		$this->load->model('shopmanager/condition');
		$this->load->model('shopmanager/catalog/category');
		$this->load->model('shopmanager/catalog/product_specific');

		// Définir les variables de texte

		$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
		
		//$test_selected = array('27061'=>'27061');
	//	$test_selected = ['27077', '27078', '27079', '27090', '27091','27092', '27093', '27094', '27095', '27096'];
	$test_selected =[];
		   // Récupérer les données `product_id` envoyées dans la requête POST
		   $json = [];
		   
		   $data = json_decode(file_get_contents('php://input'), true);
		   $product_ids=$data['product_ids']??null;
		 //  $product_ids = isset($this->request->post['product_ids']) ? json_decode($this->request->post['product_ids'], true) : null;
		// $product_ids = $test_selected ;
	   $product_ids = $product_ids ?? $test_selected;
//	if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['selected']))  {
	if (!empty($product_ids))  {
		
		foreach($product_ids as $product_id){
			
				$json['product_id'][$product_id] = $product_id;

				$product_info=[];
	
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
			
				$product_info = $this->model_shopmanager_catalog_product->getProduct($product_id);
				$this->load->model('shopmanager/marketplace');
				$product_info['marketplace_accounts_id'] = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id]);
	
	
				
				
	if(isset($product_info['upc']) && is_numeric($product_info['upc'])){
		$product_info_source = $this->model_shopmanager_catalog_product_search->manageProductInfoSources($product_info['upc']);
	}

			// Afficher les temps d'exécution pour le débogage
		//	print_r($execution_times);
		//print("<pre>" . print_r('Jo3560',true) . "</pre>");
	//	//print("<pre>" . print_r($epid_details,true) . "</pre>");




	// Récupération et traitement des images
	$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
	$start_time = microtime(true);


	//$product_info['images'] = !empty($google_search) ? $this->model_shopmanager_catalog_product_search->processUniqueImages($google_search) : ['error' => 'No images found from the specified sites'];

	// Ajout des informations Algopix et eBay




		
			//	//print("<pre>" . print_r('1408:product.php', true) . "</pre>");
			//	//print("<pre>" . print_r($product_info, true) . "</pre>"); 
				//print("<pre>" . print_r($product_info, true) . "</pre>");
				//print("<pre>".print_r ($execution_times ,true )."</pre>");
				$total_execution_time = array_sum($execution_times);

			//	echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
			//sleep(1);
			}
			$json['success'] = true;
			$json['message'] = "Temps total d'exécution : " . $total_execution_time . " secondes\n";
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}



		
	public function product_search() {

			$execution_times = [];
			$n=0;

			
			// Charger le modèle
			$start_time = microtime(true);

			$this->load->model('shopmanager/catalog/product');
			$this->load->model('shopmanager/catalog/product_search');
			$this->load->model('shopmanager/catalog/product');
			$this->load->model('shopmanager/condition');
			$this->load->model('shopmanager/catalog/category');
			$this->load->model('shopmanager/catalog/product_specific');

			// Définir les variables de texte

			$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
			
			$test_selected = array('27061'=>'27061');
			$this->request->post['selected']=$this->request->post['selected']??$test_selected;
	//	if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['selected']))  {
		if ( isset($this->request->post['selected']))  {
			$selected=$this->request->post['selected'];
			$this->load->model('shopmanager/marketplace');
			foreach($selected as $product_id){
				
				

					$product_info=[];
		
					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
				
					$product_info = $this->model_shopmanager_catalog_product->getProduct($product_id);
				
					$product_info['marketplace_accounts_id'] = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id]);
		
		
					
					
		
				//	//print("<pre>" . print_r('3397:product.php', true) . "</pre>");
				//	//print("<pre>" . print_r($product_info, true) . "</pre>");
					$this->load->model('localisation/language');
				
					$product_info['languages'] = $this->model_localisation_language->getLanguages();
				
					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
				
					
				
					//$product_info['product_description'] = $this->model_shopmanager_catalog_product->getProductDescriptions($product_id);
					
				//	//print("<pre>".print_r ($product_info['product_description'] ,true )."</pre>");
				

					
				
					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
				
					$this->load->model('tool/image');
				
					// Images
					
					$product_images = $this->model_shopmanager_catalog_product->getProductImages($product_id);
					
				
					$product_info['product_images'] = array();
					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
				
					foreach ($product_images as $product_image) {
						if (is_file(DIR_IMAGE . $product_image['image'])) {
							$image = $product_image['image'];
							$thumb = $product_image['image'];
						} else {
							$image = '';
							$thumb = 'no_image.png';
						}
				
						$product_info['product_images'][] = array(
							'image'      => $image,
							'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
							'sort_order' => $product_image['sort_order']
						);
					}
						// Categories
						$this->load->model('shopmanager/catalog/category');
				
						
						$categories = $this->model_shopmanager_catalog_product->getProductCategories($product_id);
					
				
						
				//	//print("<pre>".print_r ('1058:fast_add.php',true )."</pre>");
				//	//print("<pre>".print_r ($product_info,true )."</pre>");
						$product_info['product_categories'] = array();
					
						foreach ($categories as $category_id) {
							$category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
							//print("<pre>".print_r ($category_info,true )."</pre>");
							if ($category_info) {
								$product_info['product_categories'][] = array(
									'category_id' => $category_info['category_id'],
									'name_category' => $category_info['name'],
									'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
									'leaf' => $category_info['leaf'],
								);
							/*	if( $category_info['leaf']=='1'){
									$category_specific=$category_info['category_id'];
								}*/
							}
						}
						$this->load->model('shopmanager/condition');
				
						if(isset($product_info['category_id'])){
									// Conditions
							$product_info['conditions']=$this->model_shopmanager_condition->getConditionDetails($product_info['category_id']);
					//		$product_info['conditions'] =  $this->model_shopmanager_condition->getConditionDetails($product_info['category_id'],$product_info['condition_id']??null);
						//	//print("<pre>".print_r ($product_info['category_id'],true )."</pre>"); 
							$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($product_info['category_id']);
						//	//print("<pre>".print_r ($category_specific_info,true )."</pre>"); 
							$category_leaf = $this->model_shopmanager_catalog_category->getCategoryLeaf($product_info['category_id']);
				
							if (!is_array($category_specific_info[1]['specifics'])) {
								
								if($category_leaf ==1){
								//	//print("<pre>".print_r ('getform:2631',true )."</pre>");
									$this->response->redirect($this->url->link('shopmanager/catalog/category/edit', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $product_info['category_id'] . '&product_id='.$product_info['product_id'] . '&upc='.$product_info['upc'], true));
								}else{
									$this->error['category']= ($lang['error_category_not_leaf'] ?? '');
								}
							}
						}
					//	//print("<pre>" . print_r($product_info['product_categories'], true) . "</pre>");
				
				$product_info_source = $this->model_shopmanager_catalog_product_search->manageProductInfoSources($product_info['upc']);
				// 5. Récupérer les informations mises à jour de la table `product_info_sources`
			//	//print("<pre>" . print_r('3501:product.php', true) . "</pre>");
			// 	//print("<pre>" . print_r($product_info_source, true) . "</pre>");
				
				// Suivi du temps d'exécution après la gestion des informations de la source
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
				$start_time = microtime(true);

				// 6. Récupérer les informations depuis la table si disponibles, sinon définir à `null`
				$upc_tmp_search = isset($product_info_source['upc_tmp_search']) ? json_decode($product_info_source['upc_tmp_search'], true) : null;
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
				$start_time = microtime(true);

				$google_search = isset($product_info_source['google_search']) ? json_decode($product_info_source['google_search'], true) : null;
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
				$start_time = microtime(true);

				$algopix_search = isset($product_info_source['algopix_search']) ? json_decode($product_info_source['algopix_search'], true) : null;
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
				$start_time = microtime(true);
				
				$algopix_search_fr = isset($product_info_source['algopix_search_fr']) ? json_decode($product_info_source['algopix_search_fr'], true) : null;
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
				$start_time = microtime(true);

				$ebay_search = isset($product_info_source['ebay_search']) ? json_decode($product_info_source['ebay_search'], true) : null;
				$ebay_category = isset($product_info_source['ebay_category']) ? json_decode($product_info_source['ebay_category'], true) : null;
				$ebay_pricevariant = isset($product_info_source['ebay_pricevariant']) ? json_decode($product_info_source['ebay_pricevariant'], true) : null;
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
				$start_time = microtime(true);
				$epid= isset($product_info_source['epid']) ? json_decode($product_info_source['epid'], true) : null;
				$epid_details = isset($product_info_source['epid_details']) ? json_decode($product_info_source['epid_details'], true) : null;
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
				$start_time = microtime(true);

				if (isset($upc_tmp_search['category_name'])) { 
					$product_info['category_name'] = $upc_tmp_search['category_name']; 
				}

			//	//print("<pre>" . print_r($product_info_source, true) . "</pre>");
			//	//print("<pre>" . print_r($this->model_shopmanager_ebay->get($upc),true) . "</pre>");
																
			//	//print("<pre>" . print_r($this->model_shopmanager_ebay->findProductIDByGTIN($upc), true) . "</pre>");
				// 4. Récupérer ou rafraîchir les informations sur le produit en fonction de l'UPC
				
				//$this->model_shopmanager_ebay->getProductDetailsByepid('25046076135');
				// 7. Si l'API eBay n'a pas retourné de résultats et que nous avons un titre d'Algopix, tenter de récupérer via eBay à nouveau
			

				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
				$start_time = microtime(true);

				// 8. Stocker les résultats dans `$product_info` pour affichage ou traitement ultérieur
				$product_info['upc_tmp_search'] = $upc_tmp_search;
				$product_info['google_search'] = $google_search;
				$product_info['algopix_search'] = $algopix_search;
				$product_info['algopix_search_fr'] = $algopix_search_fr;
				$product_info['ebay_search'] = $ebay_search;
				$product_info['ebay_category'] = $ebay_category;
				$product_info['ebay_pricevariant'] = $ebay_pricevariant;

				
				$product_info['epid_details'] = $epid_details;
				$product_info['epid'] = $epid;
				

				// Afficher les temps d'exécution pour le débogage
			//	print_r($execution_times);
			//print("<pre>" . print_r('Jo3560',true) . "</pre>");
		//	//print("<pre>" . print_r($epid_details,true) . "</pre>");

	


		// Récupération et traitement des images
		$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
		$start_time = microtime(true);


		//$product_info['images'] = !empty($google_search) ? $this->model_shopmanager_catalog_product_search->processUniqueImages($google_search) : ['error' => 'No images found from the specified sites'];

		// Ajout des informations Algopix et eBay




				if (isset($upc_tmp_search['error']) && isset($algopix_search['commonAttributes'] ['title'])) { 
					$upc_tmp_search = $this->model_shopmanager_upctmp->search($algopix_search['commonAttributes'] ['title']);
				}
				$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
				$start_time = microtime(true);
				//	//print("<pre>" . print_r($upc_tmp_search, true) . "</pre>");
				//	//print("<pre>" . print_r($algopix_search, true) . "</pre>");

				if(isset($upc_tmp_search['error']) && isset($algopix_search['error']) && isset($product_id)){
					$this->response->redirect($this->url->link('shopmanager/catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product_id, true));
				}

				if (!empty($algopix_search['dimensions']['packageDimensions'])) { 
					$product_info['package_dimensions'] = $algopix_search['dimensions']['packageDimensions'];		
				}

				if (!empty($algopix_search['identifiers'])) { 
					$product_info['identifiers'] = $algopix_search['identifiers'];		
				}else{
					$product_info['identifiers'] =[];
				}



				// Gestion des conditions en fonction de la catégorie eBay
				//print("<pre>" . print_r($product_info['category_id'], true) . "</pre>");
				$category_id = $epid_details['primaryCategoryId'] ?? ($ebay_category[0]['category_id'] ?? $product_info['category_id'] ?? null);
					//print("<pre>" . print_r($ebay_category, true) . "</pre>");
				$product_info['category_id']  = $category_id;
				$product_info['category_name'] = $ebay_category[0]['category_name'] ?? null;
				// 
				if (!isset($category_id) && !isset($product_info['category_name']) && isset($algopix_search['channelSpecificAttributes'] ['productType'])){
					$category_name = str_replace('_', ' ',$algopix_search['channelSpecificAttributes'] ['productType']);
					$category_info = $this->model_shopmanager_ai->getCategoryID($category_name);
					if(isset($category_info)){
						$category_id=trim($category_info['category_id'])??null;
						$product_info['category_id']  = $category_id;
						$product_info['category_name'] = $category_info['category_name'] ?? null;
						$product_info['ebay_category'][0]=$category_info;
						$product_info['ebay_category'][0]['percent']=100;
					}
					
				}

					$conditions = $category_id 
						? $this->model_shopmanager_condition->getConditionDetails($category_id) 
						: [];

					$product_info['conditions'] = $conditions[1] ?? [];

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);

					$product_info['images']  = $this->model_shopmanager_catalog_product_search->getAllImageUrls($upc_tmp_search??null, $google_search??null, $ebay_search??null, $algopix_search??null,$algopix_search_fr??null,$epid_details??null);

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);

					$product_info['titles']  = $this->model_shopmanager_catalog_product_search->getAllTitles($upc_tmp_search??null, $google_search??null, $ebay_search??null, $algopix_search??null,$epid_details??null);

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);

					$product_info['manufacturers'] = $this->model_shopmanager_catalog_product_search->getAllManufacturers($product_info);
						//print("<pre>" . print_r($product_info['manufacturers'], true) . "</pre>");

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);

					
					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);
					//print("<pre>" . print_r('3645:product.php', true) . "</pre>");
					//	//print("<pre>" . print_r($product_info['manufacturers'], true) . "</pre>");
				//	//print("<pre>" . print_r('3650:product.php', true) . "</pre>");
			//	//print("<pre>" . print_r($product_info, true) . "</pre>");
					if(isset($product_info['manufacturers'])){
						$manufacturer_result=(isset($product_info['manufacturers']))?$this->model_shopmanager_ai->getManufacturer($product_info['manufacturers']):null;
						$product_info['manufacturer_id']=$manufacturer_result['manufacturer_id']??0;
						$product_info['manufacturer']=$manufacturer_result['manufacturer']??'';
						$product_info['brand']=$manufacturer_result['manufacturer']??'';
						$product_info['product_info']['manufacturer']=$manufacturer_result['manufacturer']??'';
					}else{
						$product_info['manufacturer_id']=$product_info['manufacturer_id']??0;
						$product_info['manufacturer']=$product_info['brand']??'';
						$product_info['brand']=$product_info['brand']??'';
					//	//print("<pre>" . print_r('3655:product.php', true) . "</pre>");
					//	//print("<pre>" . print_r($product_info, true) . "</pre>");
					}
				
					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);
					$product_description = isset($product_id)?$this->model_shopmanager_catalog_product->getProductDescriptions($product_id):null;

					// Vérifie si le nom en français (language_id = 1) est vide ou trop court
					$title_source = $product_description[1]['name'] ?? '';
					//$category_id = $data['category_id'] ?? null;
					if (empty($title_source) || mb_strlen(trim($title_source)) < 5) {
						$title_result = $this->model_shopmanager_ai->getTitle($product_info['titles'], $category_id, $product_info);
			
						// Débogage si nécessaire
						// print("<pre>" . print_r('3670:product_search.php', true) . "</pre>");
						// print("<pre>" . print_r($title_result, true) . "</pre>");
			
						$product_info['title'] = $title_result['title'];
						$product_info['name_description'] = $title_result['title'];
						$product_info['short_title'] = $title_result['short_title'] ?? null;
						$product_info['name'] = $title_result['title'];
						$product_info['algopix_search']['product name'] = $title_result['name'];
					}else{
						$product_info['title'] =  $product_description[1]['name'];
						$product_info['name_description'] = $product_description[1]['name'];
						$product_info['short_title'] =  null;
						$product_info['name'] = $product_description[1]['name'];
						$product_info['algopix_search']['product name'] = $product_description[1]['name'];
			
					}
				

					$product_info['upc']= $product_info['upc']??$this->request->get['filter_sku'];
				

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);
					if (isset($product_info['marketplace_item_id']) || $product_info['marketplace_item_id']>0 && $category_leaf ==1 ) {

						$ebay_specific_info=$this->model_shopmanager_ebay->getProductSpecifics($product_info['marketplace_item_id']);
					//	//print("<pre>" . print_r(('3687:product.php'), true) . "</pre>");
					//	//print("<pre>" . print_r(($ebay_specific_info), true) . "</pre>");

					}

					// Si les deux sont null, $mergeArrayForSpecifics reste un tableau vide
					$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($category_id,1);
					if(isset($epid_details)){
						$ebay_sources = $this->model_shopmanager_ebay->formatEpidDetails($epid_details['aspects'],$category_specific_info);
						$product_info['epid_sources_json'] = json_encode($ebay_sources);
					}else{
						$product_info['epid_sources_json'] = '';
					}
			//		//print("<pre>" . print_r(('3699:product.php'), true) . "</pre>");
			//		//print("<pre>" . print_r(($category_specific_info), true) . "</pre>");
					$mergeArrayForSpecifics = array();

					// Si les deux variables ne sont pas null, faire array_merge_recursive
					if (is_null($upc_tmp_search) && is_null($algopix_search) && !is_null($ebay_specific_info)) {
						$mergeArrayForSpecifics = $this->model_shopmanager_ebay->formatActualDetails($ebay_specific_info);
					}elseif (!is_null($upc_tmp_search) && !is_null($algopix_search)) {
						$mergeArrayForSpecifics = array_merge_recursive($upc_tmp_search, $algopix_search);
					}
					// Si seulement $upc_tmp_search n'est pas null, l'utiliser comme le tableau fusionné
					elseif (!is_null($upc_tmp_search)) {
						$mergeArrayForSpecifics = $upc_tmp_search;
					}
					// Si seulement $algopix_search n'est pas null, l'utiliser comme le tableau fusionné
					elseif (!is_null($algopix_search)) {
						$mergeArrayForSpecifics = $algopix_search;
					}
				//	//print("<pre>" . print_r(('3716:product.php'), true) . "</pre>");
				//	//print("<pre>" . print_r(($mergeArrayForSpecifics), true) . "</pre>");

					$mergeArrayForSpecifics['commonAttributes']['short_title']=$product_info['short_title']??$product_info['title'];
					$mergeArrayForSpecifics['commonAttributes']['title']=$product_info['title']??'';
					$mergeArrayForSpecifics['commonAttributes']['manufacturer']=$product_info['manufacturer']??$upc_tmp_search['brand'];
					$mergeArrayForSpecifics['commonAttributes']['brand']=$product_info['manufacturer']??$upc_tmp_search['brand'];
					//$mergeArrayForSpecifics['upc']=$product_info['upc']??$upc_tmp_search['upc'];

					$mergeArrayForSpecifics['category_name'] = $product_info['category_name'];
					$mergeArrayForSpecifics['category_id']= $product_info['category_id'];
					$mergeArrayForSpecificsResult = $this->model_shopmanager_catalog_product_search->filterArrayForSpecifics($mergeArrayForSpecifics);
				
					unset($mergeArrayForSpecificsResult['error']);
					unset($mergeArrayForSpecificsResult['category_name']);
					unset($mergeArrayForSpecificsResult['category_id']);
					unset($mergeArrayForSpecificsResult['objectType']);
					unset($mergeArrayForSpecificsResult['itemClassification']);
					unset($mergeArrayForSpecificsResult['tradeInEligible']);

				//	//print("<pre>" . print_r(('3728:product.php'), true) . "</pre>");
				//	//print("<pre>" . print_r(($mergeArrayForSpecificsResult), true) . "</pre>");

					$product_info['specifics_result'] = 	$mergeArrayForSpecificsResult;

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);

					
					//print("<pre>" . print_r($category_id, true) . "</pre>");
					$category_specifics=$category_specific_info[1]['specifics']??[];
					$category_specific_key = [];
					$category_specific_names = [];

					foreach($category_specifics as $key => $specific) {
						$value = stripslashes($key);
						$category_specific_names[] = $value;
					}

					// Trier $category_specific_names par ordre alphabétique
					//sort($category_specific_names);

					$product_info['category_specific_names'] = $category_specific_names;

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);

					// Parcourir les clés de $mergeArrayForSpecificsResult
					foreach ($mergeArrayForSpecificsResult as $specific_key_name => $value) {
						//print("<pre>" . print_r($specific_key_name, true) . "</pre>");
						// Vérifier si la clé existe dans la base de données
						$replacement_term = $this->model_shopmanager_catalog_product_specific->getSpecificKey($specific_key_name, $category_id);

					//	//print("<pre>" . print_r($replacement_term, true) . "</pre>");
					//	$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
						// Si la clé existe déjà dans la base de données
						if ($replacement_term != 'not_set') {
							if ($replacement_term =='') {
								$key_set=0;
							}else{
								$key_set=1;
							}
						//	//print("<pre>" . print_r($key_set, true) . "</pre>"); 
							$category_specific_key[$specific_key_name] = [
							
								'replacement_term' => $replacement_term,     // Pas de suggestion nécessaire, donc on garde la clé originale
								'key_set' => $key_set                             // 0 car la clé existe déjà dans la base
							];
						} else {
							
							if ($replacement_term == 'not_set') {
							// Si la clé n'existe pas dans la base, obtenir un terme suggéré via la fonction getSpecificsKey()
								$suggest_replacement_term = $this->model_shopmanager_ai->getSpecificKey($specific_key_name, $category_specifics);
							//	//print("<pre>" . print_r($suggest_replacement_term, true) . "</pre>");
								if(isset($suggest_replacement_term)){
									$this->model_shopmanager_catalog_product_specific->addSpecificKey($specific_key_name, $category_id, $suggest_replacement_term);
									unset($category_specifics[$suggest_replacement_term]);
									$key_set=2;
								}else{
									$this->model_shopmanager_catalog_product_specific->addSpecificKey($specific_key_name, $category_id, '');
									$key_set=0;
								}
							}else{
								$suggest_replacement_term='';
								$key_set=0;
							}
							// Ajouter l'entrée au tableau $category_specific_key
							$category_specific_key[$specific_key_name] = [
								
								'replacement_term' => $suggest_replacement_term??'',      // Terme suggéré
								'key_set' => $key_set                             // 1 car la clé doit être ajoutée avec le terme suggéré
							];
							
						}
					}

					$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);
					$start_time = microtime(true);

					$product_info['category_specific_key']= $category_specific_key;
					//print("<pre>" . print_r('1408:product.php', true) . "</pre>");
					//print("<pre>" . print_r($product_info, true) . "</pre>"); 
					//print("<pre>" . print_r($product_info, true) . "</pre>");
					//print("<pre>".print_r ($execution_times ,true )."</pre>");
					$total_execution_time = array_sum($execution_times);

					echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
		
				}
			}
		}

	

	public function product_feed() {
			$lang = $this->load->language('shopmanager/catalog/product_search');
			$data = $data ?? [];
			$data += $lang;
			$this->load->model('shopmanager/catalog/product_search');
			$this->load->model('shopmanager/catalog/product');
			$this->load->model('shopmanager/ai');
			$this->load->model('shopmanager/ebay');
			$this->load->model('shopmanager/catalog/product_specific');
		
			if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post)) {
				// Logique pour sauvegarder les données sélectionnées

			
			//print("<pre>" . print_r($save_data, true) . "</pre>");
			// $selected_data = $this->request->post['save_data'];
			if(isset($this->request->post['product_id'])){
					$product_info=$this->model_shopmanager_catalog_product->getProduct($this->request->post['product_id']);
					$this->load->model('shopmanager/marketplace');
					$product_info['marketplace_accounts_id'] = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $this->request->post['product_id']]);
		
	
					foreach ($product_info as $key => $value) {
						if (!array_key_exists($key, $this->request->post)) {
							$this->request->post[$key] =  $value;
						}
					}
				} 
				
				// Traiter les données spécifiques
				/* if (isset($this->request->post['specifics_checkbox']) && isset($this->request->post['specifics_select'])) {
					$specifics_checkbox = $this->request->post['specifics_checkbox'];
					$specifics_select = $this->request->post['specifics_select'];

					// Appeler la méthode updateProductSpecifics pour mettre à jour les "specifics"
					$this->model_shopmanager_catalog_product->updateProductSpecifics($specifics_checkbox, $specifics_select, $this->request->post['category_id']);
				}*/
		//   $this->request->post=$this->model_shopmanager_catalog_product_search->feedPostArray($this->model_shopmanager_catalog_product_search->cleanArray($this->request->post));
			$this->request->post=$this->model_shopmanager_catalog_product_search->processProductSearchData($this->request->post);
		//	//print("<pre>" . print_r($this->request->post, true) . "</pre>");
				// Traitez les données sélectionnées ici (par exemple, en les enregistrant dans la base de données)
				// Exemple simplifié d'enregistrement des données
				//$this->session->data['success'] = ($lang['text_success_save'] ?? '');
			//	//print("<pre>" . print_r($this->request->post, true) . "</pre>");
			}
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

				if(!isset($this->request->post['product_id'])){
					
					
				$product_id=$this->model_shopmanager_catalog_product->addProduct($this->request->post);

				$this->session->data['success'] = ($lang['text_success'] ?? '');
				$result=$this->model_shopmanager_ebay->add($product_id, 0);
		
			//	//print("<pre>" . print_r($result, true) . "</pre>");

				if ($result['Ack']!='Failure') {
					
						$this->model_shopmanager_catalog_product->editProductMarketplaceItemId($product_id,$result['ItemID']);
						$json['success'] = true;
						$json['message'] = $result['ItemID'];
					} else {
						$json['error'] = false;
						$json['message'] =$result['Errors']['LongMessage']??'';
					}
				}else{
					$product_id=$this->request->post['product_id'];
					
					$product_info['product_image']=$this->model_shopmanager_catalog_product->getProductImages($product_id);
					$product_info['product_description']=$this->model_shopmanager_catalog_product->getProductDescriptions($product_id);

					if (isset($this->request->post['images'])){
						unset($product_info['image']);
						unset($product_info['product_image']);
					}

					foreach ($this->request->post as $key => $value) {
				//		if (array_key_exists($key, $product_info)) {
							$product_info[$key] =  $value;
					//	}
					}
				//	//print("<pre>" . print_r($product_info, true) . "</pre>");
					$this->model_shopmanager_catalog_product->editProduct($product_id,$product_info);
					
				}

				$url = '';

				

				if (isset($this->request->get['filter_sku'])) {
					$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
				}

				if (isset($this->request->get['filter_product_id'])) {
					$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
				}

				if (isset($product_id)) {
					$url .= '&filter_product_id=' . urlencode(html_entity_decode($product_id, ENT_QUOTES, 'UTF-8'));
					$url .= '&product_id=' . urlencode(html_entity_decode($product_id, ENT_QUOTES, 'UTF-8'));
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

				if (isset($this->request->get['filter_image'])) {
					$url .= '&filter_image=' . $this->request->get['filter_image'];
				}

				if (isset($this->request->get['filter_specifics'])) {
					$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
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
				$url .= '&updated=yes';

				$this->response->redirect($this->url->link('shopmanager/catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}

			$this->getForm();
			
		}
}

