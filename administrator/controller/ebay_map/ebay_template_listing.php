<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerEbayMapEbayTemplateListing extends Controller {
	private $error = array();

  public function __construct($registory) {
		parent::__construct($registory);
		$this->load->model('ebay_map/ebay_template_listing');
		$this->_ebayTemplate = $this->model_ebay_map_ebay_template_listing;
  }

	public function index() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_template_listing'));
		$this->document->setTitle($this->language->get('heading_title'));
		$this->getList();
	}

  public function add() {
    $this->load->language('ebay_map/ebay_template_listing');
    $this->document->setTitle($this->language->get('heading_title_add'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->_ebayTemplate->__addTemplateListing($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_add');

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

			$this->response->redirect($this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('ebay_map/ebay_template_listing');
    $this->document->setTitle($this->language->get('heading_title_edit'));

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->_ebayTemplate->__editTemplateListing($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success_edit');

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

			$this->response->redirect($this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_template_listing'));

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $ebay_row_id) {
				$this->_ebayTemplate->deleteEbayCondition($ebay_row_id);
			}

			$this->session->data['success'] = $this->language->get('text_success_delete');

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

			$this->response->redirect($this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_template_listing'));

		// $getEbayClient 	= $this->Ebayconnector->_eBayAuthSession(2);
		//
		// if($getEbayClient){
		//     $getEbayAccountDetails 	= $this->Ebayconnector->_getModuleConfiguration(2);
		//     $item_data = [
		//         'Version' 					=> 849,
		//         'UserID' 						=> $getEbayAccountDetails['ebayUserId'],
		//         'DetailLevel' 			=> 'ReturnAll',
		//     ];
		//     $results = $getEbayClient->GetDescriptionTemplates($item_data);
		// }


		if (isset($this->request->get['filter_template_title'])) {
			$filter_template_title = $this->request->get['filter_template_title'];
		} else {
			$filter_template_title = '';
		}

		if (isset($this->request->get['filter_mapped_ebay_category'])) {
			$filter_mapped_ebay_category = $this->request->get['filter_mapped_ebay_category'];
		} else {
			$filter_mapped_ebay_category = '';
		}

    if (isset($this->request->get['filter_created_date'])) {
			$filter_created_date = $this->request->get['filter_created_date'];
		} else {
			$filter_created_date = '';
		}

		if (isset($this->request->get['filter_modify_date'])) {
			$filter_modify_date = $this->request->get['filter_modify_date'];
		} else {
			$filter_modify_date = '';
		}

    if (isset($this->request->get['filter_ebay_site_id'])) {
			$filter_ebay_site_id = $this->request->get['filter_ebay_site_id'];
		} else {
			$filter_ebay_site_id = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pc.id';
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

    if (isset($this->request->get['filter_template_title'])) {
			$url .= '&filter_template_title=' . urlencode(html_entity_decode($this->request->get['filter_template_title'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_created_date'])) {
      $url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
    }

		if (isset($this->request->get['filter_mapped_ebay_category'])) {
			$url .= '&filter_mapped_ebay_category=' . urlencode(html_entity_decode($this->request->get['filter_mapped_ebay_category'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_modify_date'])) {
			$url .= '&filter_modify_date=' . urlencode(html_entity_decode($this->request->get['filter_modify_date'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_ebay_site_id'])) {
      $url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
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
			'href' => $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . $url, true)
		);

		$data['clear'] = $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'], true);

		$data['delete'] = $this->url->link('ebay_map/ebay_template_listing/delete', 'user_token=' . $this->session->data['token'] . $url, true);

		$data['token'] = $this->session->data['token'];

		$data['ebay_templates'] = array();

		$filter_data = array(
			'filter_template_title'	      => $filter_template_title,
      'filter_mapped_ebay_category'	=> $filter_mapped_ebay_category,
      'filter_created_date'         => $filter_created_date,
			'filter_modify_date'	  	    => $filter_modify_date,
      'filter_ebay_site_id'	  	    => $filter_ebay_site_id,
			'sort'  											=> $sort,
			'order' 											=> $order,
			'start' 											=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 											=> $this->config->get('config_limit_admin')
		);

    $data['ebaySites'] = $ebaySites = $this->Ebayconnector->getEbaySiteList();

    $condition_total  = $this->_ebayTemplate->getTotalDescriptionTemplate($filter_data);

		$results  = $this->_ebayTemplate->getDescriptionTemplate($filter_data);

		foreach ($results as $result) {
      $ebay_site_name = '';
      if(isset($ebaySites['ebay_sites']) && isset($ebaySites['ebay_sites'][$result['ebay_site_id']])){
          $ebay_site_name = $ebaySites['ebay_sites'][$result['ebay_site_id']];
      }

			$data['ebay_templates'][$result['id']] = array(
        'row_id' 		    					=> $result['id'],
				'template_title' 					=> $result['title'],
				'ebay_category_id' 				=> $result['ebay_category_id'],
        'ebay_category_name' 			=> $result['ebay_category_name'],
				'ebay_site_id'            => $result['ebay_site_id'],
        'ebay_site_name'          => $ebay_site_name,
				'create_date'          		=> $result['create_date'],
				'modify_date'          		=> $result['modify_date'],
				'edit'          					=> $this->url->link('ebay_map/ebay_template_listing/edit', 'user_token=' . $this->session->data['token'] . '&template_id=' . $result['id'] . $url, true)
			);
		}

    $data['add_template']   =  $this->url->link('ebay_map/ebay_template_listing/add', 'user_token=' . $this->session->data['token'], true);

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['error_delete_category_map'])) {
			$data['error_warning'] = $this->error['error_delete_category_map'];
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

    if (isset($this->request->get['filter_template_title'])) {
			$url .= '&filter_template_title=' . urlencode(html_entity_decode($this->request->get['filter_template_title'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_created_date'])) {
      $url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
    }

		if (isset($this->request->get['filter_mapped_ebay_category'])) {
			$url .= '&filter_mapped_ebay_category=' . urlencode(html_entity_decode($this->request->get['filter_mapped_ebay_category'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_modify_date'])) {
			$url .= '&filter_modify_date=' . urlencode(html_entity_decode($this->request->get['filter_modify_date'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_ebay_site_id'])) {
      $url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
    }

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_condition_value']   = $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . '&sort=pcv.value' . $url, true);
		$data['sort_condition_name']    = $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . '&sort=pc.name' . $url, true);
		$data['sort_ebay_category_name']= $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . '&sort=ecat.ebay_category_name' . $url, true);
		$data['sort_oc_category_name']  = $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . '&sort=oc_category_name' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_template_title'])) {
			$url .= '&filter_template_title=' . urlencode(html_entity_decode($this->request->get['filter_template_title'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_created_date'])) {
      $url .= '&filter_created_date=' . $this->request->get['filter_created_date'];
    }

		if (isset($this->request->get['filter_mapped_ebay_category'])) {
			$url .= '&filter_mapped_ebay_category=' . urlencode(html_entity_decode($this->request->get['filter_mapped_ebay_category'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_modify_date'])) {
			$url .= '&filter_modify_date=' . urlencode(html_entity_decode($this->request->get['filter_modify_date'], ENT_QUOTES, 'UTF-8'));
		}

    if (isset($this->request->get['filter_ebay_site_id'])) {
      $url .= '&filter_ebay_site_id=' . $this->request->get['filter_ebay_site_id'];
    }

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $condition_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($condition_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($condition_total - $this->config->get('config_limit_admin'))) ? $condition_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $condition_total, ceil($condition_total / $this->config->get('config_limit_admin')));

		$data['filter_template_title'] 			  = $filter_template_title;
    $data['filter_mapped_ebay_category'] 	= $filter_mapped_ebay_category;
    $data['filter_created_date']          = $filter_created_date;
		$data['filter_modify_date'] 				  = $filter_modify_date;
    $data['filter_ebay_site_id'] 				  = $filter_ebay_site_id;
		$data['sort'] 	                      = $sort;
		$data['order'] 	                      = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('ebay_map/ebay_template_listing', $data));
	}

  public function getForm() {
    $data = array();
    $data = array_merge($data, $this->load->language('ebay_map/ebay_template_listing'));

    if(isset($this->request->get['template_id'])){
      $this->document->setTitle($this->language->get('heading_title_edit'));
      $data['text_add'] = $this->language->get('text_edit_template');
    }else{
      $this->document->setTitle($this->language->get('heading_title_add'));
      $data['text_add'] = $this->language->get('text_add_template');
    }

    $data['ebaySites'] = $this->Ebayconnector->getEbaySiteList();

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
      'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title'),
      'href' => $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] . $url, true)
    );

    $data['breadcrumbs'][] = array(
      'text' => $this->language->get('heading_title_add'),
      'href' => $this->url->link('ebay_map/ebay_template_listing/add', 'user_token=' . $this->session->data['token'] . $url, true)
    );

    if (!isset($this->request->get['template_id'])) {
      $data['action'] = $this->url->link('ebay_map/ebay_template_listing/add', 'user_token=' . $this->session->data['token'] . $url, true);
    } else {
      $data['action'] = $this->url->link('ebay_map/ebay_template_listing/edit', 'user_token=' . $this->session->data['token'] . '&template_id=' . $this->request->get['template_id'] .$url, true);
    }

    $data['cancel'] = $this->url->link('ebay_map/ebay_template_listing', 'user_token=' . $this->session->data['token'] , true);

    $data['token'] 	= $this->session->data['token'];

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

		$errorArray = array(
			'template_title',
			'template_ebay_site',
			'template_mapped_category',
			'template_specification',
			'template_keyword',
			'template_description',
			'custom_description',
			'shipping_icon_type'
		);
    foreach ($errorArray as $key => $error_value) {
      if (isset($this->error[$error_value])) {
        $data['error_'.$error_value] = $this->error[$error_value];
      } else {
        $data['error_'.$error_value] = '';
      }
    }

    if(isset($this->request->get['template_id'])){
      $data['template_id'] = $this->request->get['template_id'];
    }else{
      $data['template_id'] = '';
    }

    if (isset($this->request->get['template_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      $template_info = $this->_ebayTemplate->__getTemplateListing($this->request->get['template_id']);
    }

		$data['basicDetailsArray'] = array(
			'name' 						=> $this->language->get('entry_product_title'),
			'meta_title' 			=> $this->language->get('entry_meta_title'),
			'model' 					=> $this->language->get('entry_model'),
			'sku' 						=> $this->language->get('entry_sku'),
			'location' 				=> $this->language->get('entry_location'),
			'price' 					=> $this->language->get('entry_price'),
			'quantity' 				=> $this->language->get('entry_qty'),
			'date_available' 	=> $this->language->get('entry_date_available'),
			'weight' 					=> $this->language->get('entry_weight'),
		);
		$data['tempImages'] = array(
			'main' 						=> $this->language->get('entry_main_image'),
			'thumb' 					=> $this->language->get('entry_thumb_image'),
			'thumb_number' 		=> $this->language->get('entry_thumb_image_number'),
		);

		$postArray = array(
			'template_title',
			'template_ebay_site',
			'template_mapped_category',
			'template',
			'template_condition',
			'template_basicDetails',
			'template_images',
			'template_description',
			'custom_description',
			'return_policy',
			'status',
		);
    foreach ($postArray as $key => $post_value) {
      if (isset($this->request->post[$post_value])) {
        $data[$post_value] = $this->request->post[$post_value];
      } elseif (!empty($template_info)) {
				$data[$post_value] 		= '';
				if($post_value == 'template_title'){
						$data[$post_value]= $template_info['title'];
				}else if($post_value == 'template_ebay_site'){
						$data[$post_value]= $template_info['ebay_site_id'];
				}else if($post_value == 'template_mapped_category'){
						$data[$post_value]= $template_info['ebay_category_id'];
				}else if($post_value == 'template'){
						$data['template'] = array();
						$template_specification = $this->Ebayconnector->__getTemplateListingAttributes($template_info['id']);
						if(!empty($template_specification)){
								foreach ($template_specification as $key => $temp_specification) {
										$data['template']['specification'][$key] 	= $temp_specification['attribute_group_id'];
										$data['template']['keyword'][$key] 				= $temp_specification['placeholder'];
								}
						}
				}else if($post_value == 'template_condition'){
						$data[$post_value] = $template_info[$post_value];
				}else if($post_value == 'template_basicDetails'){
						$data[$post_value] = unserialize($template_info['template_basicDetails']);
				}else if($post_value == 'template_images'){
						$data[$post_value] = unserialize($template_info['template_images']);
				}else if($post_value == 'template_description'){
						$data[$post_value] = $template_info['description_type'];
				}else if($post_value == 'custom_description'){
						$data[$post_value] = $template_info['description_content'];
				}else if($post_value == 'status'){
						$data[$post_value] = $template_info[$post_value];
				}else if($post_value == 'return_policy'){
						$data[$post_value] = unserialize($template_info[$post_value]);
				}
      } else {
        $data[$post_value] 		= '';
      }
    }

		if (isset($this->request->post['shipping_condition'])) {
        $data['shipping'] = $this->request->post['shipping_condition'];
     } elseif (!empty($template_info)) {
        $data['shipping'] = unserialize($template_info['shipping_condition']);
     }else {
        $data['shipping'] = '';
     }

		if(isset($this->request->post['template_mapped_category']) && $this->request->post['template_mapped_category']){
				$data['ebay_categories'] = $this->_ebayTemplate->__geteBayMappedCategory(array('filter_ebay_site_id' => $this->request->post['template_ebay_site']));
		} elseif (!empty($template_info) && isset($template_info['ebay_category_id'])) {
				$data['ebay_categories'] = $this->_ebayTemplate->__geteBayMappedCategory(array('filter_ebay_site_id' => $template_info['ebay_site_id']));
		}

		$getCategorySpecification = array();
		if(isset($this->request->post['template']['specification']) && !empty($this->request->post['template']['specification'])){
				$getCategorySpecification = $this->Ebayconnector->_getProductSpecification(array('filter_ebay_category_id' => $this->request->post['template_mapped_category']));
		} elseif (!empty($template_info) && isset($template_info['ebay_category_id'])) {
				$getCategorySpecification = $this->Ebayconnector->_getProductSpecification(array('filter_ebay_category_id' => $template_info['ebay_category_id']));
		}
		if(!empty($getCategorySpecification)){
				foreach ($getCategorySpecification as $key => $categorySpecifications) {
						$data['ebay_specifications'][$categorySpecifications['attribute_group_id']] = array(
													'attribute_group_id'	=> $categorySpecifications['attribute_group_id'],
													'attr_group_name'			=> $categorySpecifications['name'],
													'language_id'					=> $categorySpecifications['language_id'],
													'ebay_category_id'		=> $categorySpecifications['ebay_category_id'],
													'ebay_category_name'	=> $categorySpecifications['ebay_category_name'],
													'required'						=> $categorySpecifications['required'],
						);
				}
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['shipping']['icon']) && is_file(DIR_IMAGE . $this->request->post['shipping']['icon'])) {
			$data['shipping_icon'] = $this->model_tool_image->resize($this->request->post['shipping']['icon'], 50, 50);
		} elseif (isset($data['shipping']['icon']) && is_file(DIR_IMAGE . $data['shipping']['icon'])) {
			$data['shipping_icon'] = $this->model_tool_image->resize($data['shipping']['icon'], 50, 50);
		} else {
			$data['shipping_icon'] = $this->model_tool_image->resize('no_image.png', 50, 50);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 50, 50);

		$data['shipping_services'] = array(
										array('value' => 'Other', 'name' => $this->language->get('text_eco_shipping')),
										array('value' => 'UK_OtherCourier', 'name' => $this->language->get('text_uk_shipping')),
										array('value' => 'DE_Pickup', 'name' => $this->language->get('text_pickup_shipping')),
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

    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('ebay_map/ebay_template_listing_form', $data));
  }

	public function getMappedCategory(){
		$json =	$getStoreMappedCategory = array();

		if(isset($this->request->post['ebay_site_id'])){
				$getStoreMappedCategory = $this->_ebayTemplate->__geteBayMappedCategory(array('filter_ebay_site_id' => $this->request->post['ebay_site_id']));

				if(!empty($getStoreMappedCategory)){
					foreach ($getStoreMappedCategory as $key => $mapped_category) {
							$json['ebay_categories'][] = array(
								'oc_category_id'			=> $mapped_category['opencart_category_id'],
								'ebay_category_id'		=> $mapped_category['ebay_category_id'],
								'ebay_category_name'	=> $mapped_category['ebay_category_name'],
								'oc_category_name'		=> $mapped_category['name'],
								'ebay_sites_id'				=> $mapped_category['ebay_connector_ebay_sites'],
							);
					}
				}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getCategoryBasedSpecification(){
		$json =	$getCategorySpecification = array();

		if(isset($this->request->post['ebay_category_id'])){
				$getCategorySpecification = $this->Ebayconnector->_getProductSpecification(array('filter_ebay_category_id' => $this->request->post['ebay_category_id']));

				if(!empty($getCategorySpecification)){
					foreach ($getCategorySpecification as $key => $categorySpecifications) {
							$json['ebay_specifications'][$categorySpecifications['attribute_group_id']] = array(
														'attribute_group_id'	=> $categorySpecifications['attribute_group_id'],
														'attr_group_name'			=> $categorySpecifications['name'],
														'language_id'					=> $categorySpecifications['language_id'],
														'ebay_category_id'		=> $categorySpecifications['ebay_category_id'],
														'ebay_category_name'	=> $categorySpecifications['ebay_category_name'],
														'required'						=> $categorySpecifications['required'],
							);
					}
				}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}


  protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_template_listing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if((utf8_strlen(trim($this->request->post['template_title'])) < 5) || (utf8_strlen(trim($this->request->post['template_title'])) > 150)){
				$this->error['template_title'] = $this->language->get('error_template_title');
		}

		if(isset($this->request->post['template_ebay_site']) && !preg_match('/^[0-9]/', $this->request->post['template_ebay_site'])){
				$this->error['template_ebay_site'] = $this->language->get('error_template_ebay_site');
		}

		if(isset($this->request->post['template_mapped_category']) && is_null($this->request->post['template_mapped_category'])){
				$this->error['template_mapped_category'] = $this->language->get('error_template_mapped_category');
		}

		if(isset($this->request->post['template']['specification']) && isset($this->request->post['template']['keyword'])){
			$counts = array_count_values($this->request->post['template']['specification']);

			foreach ($this->request->post['template']['specification'] as $key => $attr_value) {
					if($counts[$attr_value] > 1){
							$this->error['template_specification'][$key]	= $this->language->get('error_template_specification_repeat');
					}
					if(empty($this->request->post['template']['keyword'][$key])){
							$this->error['template_keyword'][$key]	= $this->language->get('error_template_keyword');
					}
			}
		}

		if(isset($this->request->post['template_description']) && !$this->request->post['template_description']){
				$this->error['template_description'] = $this->language->get('error_template_description');
		}

		if(isset($this->request->post['template_description']) && $this->request->post['template_description'] == 'custom'){
				if((utf8_strlen(trim($this->request->post['custom_description'])) < 5) || (utf8_strlen(trim($this->request->post['custom_description'])) > 5000)){
						$this->error['custom_description'] = $this->language->get('error_template_description_length');
				}
		}

		if(isset($this->request->post['shipping']['icon']) && $this->request->post['shipping']['icon']){
				$filename = basename(html_entity_decode($this->request->post['shipping']['icon'], ENT_QUOTES, 'UTF-8'));

				// Allowed file extension types
				$allowed = array('jpg', 'jpeg', 'gif', 'png' );
				if (!in_array(utf8_strtolower(utf8_substr(strrchr($filename, '.'), 1)), $allowed)) {
					$this->error['shipping_icon_type'] = $this->language->get('error_shipping_icon_type');
				}
		}
		if($this->error && !isset($this->error['warning'])){
			$this->error['warning'] = $this->language->get('error_warning_fields');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'ebay_map/ebay_template_listing')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		return !$this->error;
	}

	public function autocomplete(){
		$json = array();

		if(isset($this->request->get['filter_template_title'])){
				if(isset($this->request->get['filter_template_title'])){
					$title = $this->request->get['filter_template_title'];
				}else{
					$title = '';
				}
				$filter_data = array(
					'filter_template_title' 		=> $title,
					'order'       => 'ASC',
					'start'       => 0,
					'limit'       => 5
				);

				$results = $this->_ebayTemplate->getDescriptionTemplate($filter_data);

				foreach ($results as $result) {
						$json[$result['id']] = array(
							'template_id' 		=> $result['id'],
							'template_title'  => strip_tags(html_entity_decode($result['title'], ENT_QUOTES, 'UTF-8'))
						);
				}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
