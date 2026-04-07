<?php
/**
 * @version [Supported opencart version 3.x.x.x.]
 * @category Webkul
 * @package Opencart eBay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapProductData extends Controller {
  private $error = array();
  public function index() {
    $this->load->language('ebay_map/product_data');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->getList();
  }

  public function add() {
    $this->load->language('ebay_map/product_data');

    $this->document->setTitle($this->language->get('text_add'));

    $this->load->model('ebay_map/product_data');

    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {

      $this->model_ebay_map_product_data->saveMappedProductData($this->request->post['product_id'], $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $url = '';

  		if (isset($this->request->get['filter_name'])) {
  			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
  		}

  		if (isset($this->request->get['filter_model'])) {
  			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
  		}

      if (isset($this->request->get['filter_model'])) {
        $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
      }

      if (isset($this->request->get['filter_category_id'])) {
  			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
  		}

  		if (isset($this->request->get['filter_price'])) {
  			$url .= '&filter_price=' . $this->request->get['filter_price'];
  		}

  		if (isset($this->request->get['filter_quantity'])) {
  			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
  		}

  		if (isset($this->request->get['filter_status'])) {
  			$url .= '&filter_status=' . $this->request->get['filter_status'];
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

      $this->response->redirect($this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . $url, true));
    }
    $this->getForm();
  }

  public function edit() {

    $this->load->language('ebay_map/product_data');

    $this->document->setTitle($this->language->get('text_edit'));

    $this->load->model('ebay_map/product_data');

    if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {

      $this->model_ebay_map_product_data->saveMappedProductData($this->request->get['product_id'], $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $url = '';

  		if (isset($this->request->get['filter_name'])) {
  			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
  		}

  		if (isset($this->request->get['filter_model'])) {
  			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
  		}

      if (isset($this->request->get['filter_category'])) {
        $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
      }

      if (isset($this->request->get['filter_category_id'])) {
  			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
  		}

  		if (isset($this->request->get['filter_price'])) {
  			$url .= '&filter_price=' . $this->request->get['filter_price'];
  		}

  		if (isset($this->request->get['filter_quantity'])) {
  			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
  		}

  		if (isset($this->request->get['filter_status'])) {
  			$url .= '&filter_status=' . $this->request->get['filter_status'];
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

      $this->response->redirect($this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . $url, true));
    }

    $this->getForm();
  }

  public function delete() {
    $this->load->language('ebay_map/product_data');

    if (isset($this->request->post['selected']) && $this->validateDelete()) {

      $this->load->model('ebay_map/product_data');

      foreach ($this->request->post['selected'] as $product_id) {

        $this->model_ebay_map_product_data->deleteMappedProductData($product_id);

      }

      $this->response->redirect($this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'], true));
    }

    $this->getList();
  }

  protected function getForm() {
    $data = $this->load->language('ebay_map/product_data');
    if (isset($this->request->get['product_id'])) {
      $product_id = $this->request->get['product_id'];
    } else {
      $product_id = 0;
    }

    if (isset($this->request->post['product_id'])) {
      $product_id = $this->request->post['product_id'];
    }

    $data['product_id']  = $product_id;

    $data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

    if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_category'])) {
      $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_category_id'])) {
      $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
    }

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . $url, true)
		);
    $data['action'] = $this->url->link('ebay_map/product_data/add', 'user_token=' . $this->session->data['token'], true);

    if (isset($this->request->get['product_id'])) {
      $data['action'] = $this->url->link('ebay_map/product_data/edit&product_id=' . $this->request->get['product_id'], 'user_token=' . $this->session->data['token'] . $url, true);
    }

    $data['cancel'] = $this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . $url, true);

    $data['token'] = $this->session->data['token'];
    $data['ebay_conditions'] = $this->Ebayconnector->_getProductConditions();
    $data['ebay_specifications'] = $this->Ebayconnector->_getProductSpecification();
    $data['ebay_variations'] = $this->Ebayconnector->_getEbayVariation();
    $data['ebay_templates'] = $this->Ebayconnector->_getEbayTemplates();

    //Ebay Template
    $data['product_ebay_template'] = '';
    if (isset($this->request->post['product_ebay_template'])) {
      $data['product_ebay_template'] = $this->request->post['product_ebay_template'];
    } elseif (isset($this->request->get['product_id'])) {
      $product_ebay_template = $this->Ebayconnector->_getProductTemplate(array('filter_product_id' => $this->request->get['product_id']));
      if (!empty($product_ebay_template)) {
        $data['product_ebay_template'] = $product_ebay_template['template_id'];

      }
    }

    // eBay Auction
    $ebay_auction = $this->model_ebay_map_product_data->getProductAuction($product_id);

    if (isset($this->request->post['buy_it_now_price'])) {
      $data['buy_it_now_price'] = $this->request->post['buy_it_now_price'];
    } else if (isset($ebay_auction['buy_it_now_price'])) {
      $data['buy_it_now_price'] = $ebay_auction['buy_it_now_price'];
    } else {
      $data['buy_it_now_price'] = '';
    }

    if (isset($this->request->post['auction_status'])) {
      $data['auction_status'] = $this->request->post['auction_status'];
    } else if (isset($ebay_auction['auction_status'])) {
      $data['auction_status'] = $ebay_auction['auction_status'];
    } else {
      $data['auction_status'] = '';
    }

    if (isset($this->request->post['auction_status'])) {
      $data['auction_status'] = $this->request->post['auction_status'];
    } else if (isset($ebay_auction['auction_status'])) {
      $data['auction_status'] = $ebay_auction['auction_status'];
    } else {
      $data['auction_status'] = '';
    }

    if (isset($this->request->post['price_rule_status'])) {
      $data['price_rule_status'] = $this->request->post['price_rule_status'];
    } else if (isset($ebay_auction['price_rule_status'])) {
      $data['price_rule_status'] = $ebay_auction['price_rule_status'];
    } else {
      $data['price_rule_status'] = '';
    }

    // eBay Specification

    $this->load->model('catalog/attribute');

    if (isset($this->request->post['product_specification'])) {
      $product_specifications = $this->request->post['product_specification'];
    } elseif (isset($this->request->get['product_id'])) {
      $product_specifications = $this->Ebayconnector->getProductSpecification($this->request->get['product_id']);
    } else {
      $product_specifications = array();
    }

    $this->load->model('catalog/product');
    $data['product_name'] = '';
    $data['product_price'] = '';

    if (isset($this->request->get['product_id'])) {
      $product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
    } else if (isset($this->request->post['product_id'])) {
      $product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);
    }

    if (isset($product_info) && $product_info) {
      $data['product_name'] = $product_info['name'];
      $data['product_price'] = $product_info['price'];
    }


    $data['product_specification'] = array();
    foreach ($product_specifications as $key => $specification) {
      $specification_info = $this->model_catalog_attribute->getAttribute($specification);
      if ($specification_info) {
        $data['product_specification'][] = $specification;
      }
    }

    //Ebay condition
    if (isset($this->request->post['product_condition'])) {
      $product_conditions = $this->request->post['product_condition'];
    } elseif (isset($this->request->get['product_id'])) {
      $product_conditions = $this->Ebayconnector->getProductCondition($this->request->get['product_id']);
    } else {
      $product_conditions = array();
    }
    $data['product_condition'] = array();
    foreach ($product_conditions as $key => $condition) {

      $condition_info = $this->Ebayconnector->getEbayCondition($condition);

      if (isset($condition_info) && $condition_info) {
        $data['product_condition'][] = $condition;
      }
    }
    //ebay variation
    if(isset($this->request->post['product_variation'])) {
       $data['product_variation'] = $this->request->post['product_variation'];
    } elseif (isset($this->request->get['product_id'])) {
      $data['product_variation'] = $this->Ebayconnector->_getProductVariation($this->request->get['product_id'],'product_variation');
    } else {
      $data['product_variation'] = array();
    }

    if (isset($this->request->post['product_variation_value'])) {
      $data['product_variation_value'] = $this->request->post['product_variation_value'];
    } elseif (isset($this->request->get['product_id'])) {
      $data['product_variation_value'] = $this->Ebayconnector->_getProductVariation($this->request->get['product_id'],'product_variation_value');
    } else {
      $data['product_variation_value'] = array();
    }

    $data['symbol_left'] = $this->currency->getSymbolLeft($this->config->get('config_currency'));
    $data['symbol_right'] = $this->currency->getSymbolRight($this->config->get('config_currency'));

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ebay_map/product_data_form', $data));
  }

  protected function getList() {
    $data = $this->load->language('ebay_map/product_data');
    $url = '';

    if (isset($this->request->get['filter_product'])) {
      $filter_product = $this->request->get['filter_product'];
    } else {
      $filter_product = '';
    }

    if (isset($this->request->get['filter_product_id'])) {
      $filter_product_id = $this->request->get['filter_product_id'];
    } else {
      $filter_product_id = '';
    }

    if (isset($this->request->get['filter_category'])) {
      $filter_category = $this->request->get['filter_category'];
    } else {
      $filter_category = '';
    }

    if (isset($this->request->get['filter_category_id'])) {
      $filter_category_id = $this->request->get['filter_category_id'];
    } else {
      $filter_category_id = '';
    }

    if (isset($this->request->get['filter_quantity'])) {
      $filter_quantity = $this->request->get['filter_quantity'];
    } else {
      $filter_quantity = '';
    }

    if (isset($this->request->get['filter_model'])) {
      $filter_model = $this->request->get['filter_model'];
    } else {
      $filter_model = '';
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

    $url = '';

    if (isset($this->request->get['filter_product'])) {
      $url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_category'])) {
      $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_model'])) {
      $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_category_id'])) {
      $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
    }
    if (isset($this->request->get['product_id'])) {
      $url .= '&product_id=' . $this->request->get['product_id'];
    }
    if (isset($this->request->get['quantity'])) {
      $url .= '&quantity=' . $this->request->get['quantity'];
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
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . $url, true)
    );
    $data['token'] = $this->session->data['token'];
    $data['add'] = $this->url->link('ebay_map/product_data/add', 'user_token=' . $this->session->data['token'] . $url, true);

    $data['delete'] = $this->url->link('ebay_map/product_data/delete', 'user_token=' . $this->session->data['token'] . $url, true);

    $filter_data = array(
      'filter_product'      => $filter_product,
      'filter_product_id'   => $filter_product_id,
      'filter_category_id'  => $filter_category_id,
      'filter_model'        => $filter_model,
      'filter_quantity'     => $filter_quantity,
      'sort'                => $sort,
      'order'               => $order,
      'start'               => ($page - 1) * $this->config->get('config_limit_admin'),
      'limit'               => $this->config->get('config_limit_admin')
    );

    $this->load->model('ebay_map/product_data');
    $this->load->model('tool/image');

    $results = $this->model_ebay_map_product_data->getMappedProductData($filter_data);

    $total = $this->model_ebay_map_product_data->getTotalMappedProductData($filter_data);

    $data['products'] = array();

    foreach ($results as $result) {
      $product_categories = '';
      $categories = $this->model_ebay_map_product_data->getMappedProductCategories($result['product_id']);
      foreach ($categories as $category) {
        $product_categories .= $category['name'] . ', ';
      }
      $product_categories = rtrim($product_categories, ', ');

      if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

      $auction = $this->model_ebay_map_product_data->getProductAuction($result['product_id']);

      $data['products'][] = array(
        'product_id'      => $result['product_id'],
        'name'            => $result['name'],
        'model'           => $result['product_id'],
        'quantity'        => $result['quantity'],
        'price'           => $result['price'],
        'image'           => $image,
        'categories'      => $product_categories,
        'auction_status'  => isset($auction['auction_status']) && $auction['auction_status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
        'edit'            => $this->url->link('ebay_map/product_data/edit&product_id=' . $result['product_id'], 'user_token=' . $this->session->data['token'], true)
      );
    }
    if (isset($this->session->data['success'])) {
      $data['success'] = $this->session->data['success'];

      unset($this->session->data['success']);
    } else {
      $data['success'] = '';
    }

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

    if (isset($this->request->get['filter_product'])) {
      $url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_category'])) {
      $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_model'])) {
      $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
    }


    if (isset($this->request->get['filter_product_id'])) {
      $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
    }

    if (isset($this->request->get['filter_category_id'])) {
      $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
    }

    if ($order == 'ASC') {
      $url .= '&order=DESC';
    } else {
      $url .= '&order=ASC';
    }

    if (isset($this->request->get['page'])) {
      $url .= '&page=' . $this->request->get['page'];
    }

    $data['sort_product'] = $this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . '&sort=pd.name' . $url, true);
    $data['sort_model'] = $this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . '&sort=p.model' . $url, true);

    $data['sort_quantity'] = $this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . '&sort=p.quantity' . $url, true);

    $data['sort_price'] = $this->url->link('ebay_map/product_data', 'user_token=' . $this->session->data['token'] . '&sort=p.price' . $url, true);

    $url = '';

    if (isset($this->request->get['filter_product'])) {
      $url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_category'])) {
      $url .= '&filter_category=' . urlencode(html_entity_decode($this->request->get['filter_category'], ENT_QUOTES, 'UTF-8'));
    }

    if (isset($this->request->get['filter_model'])) {
      $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
    }


    if (isset($this->request->get['filter_product_id'])) {
      $url .= '&filter_product_id=' . $this->request->get['filter_product_id'];
    }
    if (isset($this->request->get['filter_category_id'])) {
      $url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
    }

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

    $pagination = new Pagination();
		$pagination->total = $total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/price_qty_rule', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total - $this->config->get('config_limit_admin'))) ? $total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total, ceil($total / $this->config->get('config_limit_admin')));

    $data['filter_product'] = $filter_product;
    $data['filter_product_id'] = $filter_product_id;
    $data['filter_category'] = $filter_category;
    $data['filter_category_id'] = $filter_category_id;
    $data['filter_model'] = $filter_model;
    $data['filter_quantity'] = $filter_quantity;

    $data['sort'] = $sort;
    $data['order'] = $order;

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('ebay_map/product_data', $data));

  }

  public function autocomplete() {
    $json = array();
    if (isset($this->request->get['filter_name'])) {
      $this->load->model('ebay_map/product_data');
      $products = $this->model_ebay_map_product_data->getAutocomplete(array('filter_name' => $this->request->get['filter_name']));

      foreach ($products as $product) {
        $json[] = array(
          'product_id'  => $product['product_id'],
          'name'        => html_entity_decode($product['name'], ENT_QUOTES, 'UTF-8'),
          'model'       => html_entity_decode($product['model'], ENT_QUOTES, 'UTF-8'),
          'price'       => $this->currency->format($product['price'], $this->config->get('config_currency'))
        );
      }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
  }

  protected function validateDelete() {
    if (!$this->user->hasPermission('modify', 'ebay_map/product_data')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    return !$this->error;
  }

  protected function validate() {
    if (!$this->user->hasPermission('modify', 'ebay_map/product_data')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    if (isset($this->request->post['product_ebay_template']) && $this->request->post['product_ebay_template']) {
      $this->load->model('ebay_map/ebay_template_listing');

      $product_id = isset($this->request->post['product_id']) ? $this->request->post['product_id']: 0;

      if ($product_id) {
        $this->load->model('catalog/product');
        $this->request->post['product_category'] = $this->model_catalog_product->getProductCategories($product_id);
      } else {
        $this->request->post['product_category'] = array();
      }

      $result = $this->model_ebay_map_ebay_template_listing->__validateProducteBayTemplate($product_id, $this->request->post);

      if (isset($result['error'])) {
        $this->error['warning'] = $result['message'];
      }

    }

    return !$this->error;
  }
}
