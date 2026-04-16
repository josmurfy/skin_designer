<?php
// Original: warehouse/product/category.php
namespace Opencart\Admin\Controller\Warehouse\Product;
/**
 * Class Category
 *
 * Can be loaded using $this->load->controller('warehouse/product/category');
 *
 * @package Opencart\Admin\Controller\Catalog
 */
class Category extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
	$this->load->language('warehouse/product/category');
	$data = [];
	

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
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

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('warehouse/product/category', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['repair'] = $this->url->link('warehouse/product/category.repair', 'user_token=' . $this->session->data['user_token']);
		$data['add'] = $this->url->link('warehouse/product/category.form', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('warehouse/product/category.delete', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->load->controller('warehouse/product/category.getList');

		$data['filter_name'] = $filter_name;
		$data['filter_status'] = $filter_status;

		$data['per_page_options']=[20, 50, 100, 200];
		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('warehouse/product/category', $data));
	}

	/**
	* List
	*
	* @return void
	*/
 	public function list(): void {
		$this->load->language('warehouse/product/category');
		$data = [];
		

		$this->response->setOutput($this->load->controller('warehouse/product/category.getList'));
	}

	/**
	 * Get List
	 *
	 * @return string
	 */
	public function getList(): string {
		//$this->document->addScript('view/javascript/warehouse/product/category_list.js');
		//$this->document->addScript('view/javascript/warehouse/popup/marketplace_error.js');
		//$this->document->addScript('view/javascript/warehouse/popup/alert.js');
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

			if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = (int)$this->request->get['filter_category_id'];
		} else {
			$filter_category_id = null;
		}

		if (isset($this->request->get['filter_leaf'])) {
			$filter_leaf = (int)$this->request->get['filter_leaf'];
		} else {
			$filter_leaf = null;
		}

		if (isset($this->request->get['filter_specifics'])) {
			$filter_specifics = (int)$this->request->get['filter_specifics'];
		} else {
			$filter_specifics = null;
		}

		if (isset($this->request->get['filter_image'])) {
			$filter_image = (int)$this->request->get['filter_image'];
		} else {
			$filter_image = null;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = (string)$this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = 20;
		}

	

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url = '&filter_category_id=' . $this->request->get['filter_category_id'];
		} 

		if (isset($this->request->get['filter_leaf'])) {
			$url .= '&filter_leaf=' . $this->request->get['filter_leaf'];
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
			$limit = (int)$this->request->get['limit'];
		}else{
			$url .= '&limit=' . 20;
			$data['limit'] = 20;
			$limit = 20;
		}

		/*if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}*/
	
		$data['action'] = $this->url->link('warehouse/product/category.list', 'user_token=' . $this->session->data['user_token'] . $url);

		// Category
		$data['categories'] = [];

		$filter_data = [
			'filter_name'   => $filter_name,
			'filter_category_id'  => $filter_category_id,
			'filter_status' => $filter_status,
			'filter_leaf' =>	$filter_leaf, 
			'filter_specifics' => $filter_specifics,
			'filter_image'    => $filter_image,
			'sort'          => $sort,
			'order'         => $order,
			'start'         => ($page - 1) * $limit,
			'limit'         => $limit
		];
		// Image
		$this->load->model('tool/image');

		$this->load->model('warehouse/product/category');

		$results = $this->model_warehouse_product_category->getCategories($filter_data);

		foreach ($results as $result) {
			$image = $result['image'] && is_file(DIR_IMAGE . html_entity_decode($result['image'], ENT_QUOTES, 'UTF-8'))
				? $result['image']
				: 'no_image.png';

		//	//print("<pre>".print_r ($result,true )."</pre>");
			if($result['leaf'] && !$result['specifics'] && $result['specifics_error']){
				$specifics= ($lang['text_specifics_error'] ?? '');
			}elseif($result['leaf'] && !$result['specifics'] && !$result['specifics_error']){
				$specifics= ($lang['text_specifics_not_set'] ?? '');
			}elseif($result['leaf'] && $result['specifics'] && !$result['specifics_error']){
				$specifics= ($lang['text_specifics_set'] ?? '');
			}else{
				$specifics=($lang['text_specifics_na'] ?? '');
			}

			$data['categories'][] = [
				'category_id' => $result['category_id'],
				'image' => $this->model_tool_image->resize($image, 40, 40),
				'name'        => $result['name'],
				'leaf'      => $result['leaf']? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? ''),
				'specifics'      => $specifics ,
				'sort_order'  => $result['sort_order'],
				'status_id'   => $result['status'],
				'status'     => $result['status'] ? ($lang['text_enabled'] ?? '') : ($lang['text_disabled'] ?? ''),
				'edit'  => $this->url->link('warehouse/product/category.form', 'user_token=' . $this->session->data['user_token'] . '&category_id=' . $result['category_id'] . $url)
			] + $result;	
		}

	 		$url = '';

			if ($order == 'ASC') {
				$url .= '&order=DESC';
			} else {
				$url .= '&order=ASC';
			}

			$data['sort_name'] = $this->url->link('warehouse/product/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
			$data['sort_sort_order'] = $this->url->link('warehouse/product/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);
			$data['sort_status'] = $this->url->link('warehouse/product/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c1.status' . $url);
			$data['sort_leaf'] = $this->url->link('warehouse/product/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=c1.leaf' . $url);
			$data['sort_category_id'] = $this->url->link('warehouse/product/category.list', 'user_token=' . $this->session->data['user_token'] . '&sort=category_id' . $url);
	
			$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_leaf'])) {
			$url .= '&filter_leaf=' . $this->request->get['filter_leaf'];
		}

		if (isset($this->request->get['filter_specifics'])) {
			$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}


		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])){
			$url .= '&limit=' . $this->request->get['limit'];
		} 	

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		$category_total = $this->model_warehouse_product_category->getTotalCategories($filter_data);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $category_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('warehouse/product/category.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
			]);

		$data['results'] = sprintf(($lang['text_pagination'] ?? ''), ($category_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($category_total - $limit)) ? $category_total : ((($page - 1) * $limit) + $limit), $category_total, ceil($category_total / $limit));

		$data['sort'] = $sort;
		$data['order'] = $order;
	
		return $this->load->view('warehouse/product/category_list', $data);
	}

	/**
	 * Form
	 *
	 * @return void
	 */
	public function form(): void {
		$this->load->language('warehouse/product/category');
		$data = [];
		

		$this->document->setTitle(($lang['heading_title'] ?? ''));

		$this->document->addScript('view/javascript/ckeditor/ckeditor.js');
		$this->document->addScript('view/javascript/ckeditor/adapters/jquery.js');
		$this->document->addScript('view/javascript/warehouse/product/category_form.js');
		$this->document->addScript('view/javascript/warehouse/tools/ai.js');
		$this->document->addScript('view/javascript/summernote/summernote.js');
		$this->document->addScript('view/javascript/summernote/opencart.js');
		$this->document->addStyle('view/javascript/summernote/summernote.css');
		//$this->document->addScript('view/javascript/warehouse/popup/marketplace_error.js');
		//$this->document->addScript('view/javascript/warehouse/popup/alert.js');
		//$this->document->addScript('view/javascript/warehouse/tools/translate.js');

		$data['text_form'] = !isset($this->request->get['category_id']) ? ($lang['text_add'] ?? '') : ($lang['text_edit'] ?? '');
		
		$url = '';
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		} 

		if (isset($this->request->get['filter_leaf'])) {
			$url .= '&filter_leaf=' . $this->request->get['filter_leaf'];
		} 

		if (isset($this->request->get['filter_specifics'])) {
			$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}
		
		if (isset($this->request->get['product_id'])) {
			$url .= '&product_id=' . urlencode(html_entity_decode($this->request->get['product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['marketplace_item_id'])) {
			$url .= '&marketplace_item_id=' . urlencode(html_entity_decode($this->request->get['marketplace_item_id'], ENT_QUOTES, 'UTF-8'));
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

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => ($lang['text_home'] ?? ''),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => ($lang['heading_title'] ?? ''),
			'href' => $this->url->link('warehouse/product/category', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['save'] = $this->url->link('warehouse/product/category.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('warehouse/product/category', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['category_id'])) {
			$this->load->model('warehouse/product/category');

			$category_info = $this->model_warehouse_product_category->getCategory((int)$this->request->get['category_id']);
			$data['leaf']=$this->model_warehouse_product_category->getLeaf($this->request->get['category_id'])??0;
		}

		if (!empty($category_info)) {
			$data['category_id'] = $category_info['category_id'];
		} else {
			$data['category_id'] = 0;
		}

		// Language
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();
		

		// Build languages_json like in product controller
		$languages_array = [];
		foreach ($data['languages'] as $language) {
			// Extract just "en" from "en-gb"
			$short_code = explode('-', $language['code'])[0];
			$languages_array[$language['language_id']] = $short_code;
		}
		$data['languages_json'] = json_encode($languages_array);
	
		
		if (!empty($category_info)) {
			$data['category_description'] = $this->model_warehouse_product_category->getDescriptions($category_info['category_id']);
			
			// Prepare specifics_data for JavaScript
			$data['specifics_data'] = [];
			$has_specifics = false;
			
			foreach ($data['category_description'] as $language_id => $description) {
				if (isset($description['specifics']) && is_array($description['specifics']) && !empty($description['specifics'])) {
					$data['specifics_data'][$language_id] = $description['specifics'];
					$has_specifics = true;
				}
			}
			
			// If specifics are not loaded yet (empty or not array), mark to fetch via AJAX
			if(!$has_specifics && isset($data['leaf']) && $data['leaf']==1){
				$data['specifics']='class="active"';
			}
		} else {
			$data['category_description'] = [];
		}

		if (!empty($category_info)) {
			$data['path'] = $category_info['path'];
		} else {
			$data['path'] = '';
		}
		
		if (!empty($category_info)) {
			$data['parent_id'] = $category_info['parent_id'];
		} else {
			$data['parent_id'] = 0;
		}

		// Filter
		$this->load->model('warehouse/catalog/filter');

		if (!empty($category_info)) {
			$filters = $this->model_warehouse_product_category->getFilters($category_info['category_id']);
		} else {
			$filters = [];
		}

		$data['category_filters'] = [];

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_warehouse_catalog_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['category_filters'][] = [
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				];
			}
		}

		// Store
		$data['stores'] = [];

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => ($lang['text_default'] ?? '')
		];

		$this->load->model('setting/store');

		$results = $this->model_setting_store->getStores();

		foreach ($results as $result) {
			$data['stores'][] = $result;
		}
		
		if (!empty($category_info)) {
			$data['category_store'] = $this->model_warehouse_product_category->getStores($category_info['category_id']);
		} else {
			$data['category_store'] = [0];
		}

		// Image
		if (!empty($category_info)) {
			$data['image'] = $category_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

		if ($data['image'] && is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize($data['image'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));
		} else {
			$data['thumb'] = $data['placeholder'];
		}

		if (!empty($category_info)) {
			$data['sort_order'] = $category_info['sort_order'];
		} else {
			$data['sort_order'] = 0;
		}

		if (!empty($category_info)) {
			$data['status'] = $category_info['status'];
		} else {
			$data['status'] = true;
		}

		// Site ID (eBay marketplace: 0=US, 2=Canada, 3=UK, 77=Germany...)
		if (isset($this->request->post['site_id'])) {
			$data['site_id'] = (int)$this->request->post['site_id'];
		} elseif (!empty($category_info)) {
			$data['site_id'] = (int)($category_info['site_id'] ?? 0);
		} else {
			$data['site_id'] = 0;
		}

		// Conditions
		$this->load->model('warehouse/product/condition');

		$data['conditions']=$this->model_warehouse_product_condition->getConditionDetails($data['category_id'],null,null,$data['site_id']);

	// SEO
		$data['category_seo_url'] = [];

		if (!empty($category_info)) {
			$this->load->model('design/seo_url');

			$results = $this->model_design_seo_url->getSeoUrlsByKeyValue('path', $this->model_warehouse_product_category->getPath($category_info['category_id']));

			foreach ($results as $store_id => $languages) {
				foreach ($languages as $language_id => $keyword) {
					$pos = strrpos($keyword, '/');

					if ($pos !== false) {
						$keyword = substr($keyword, $pos + 1);
					}

					$data['category_seo_url'][$store_id][$language_id] = $keyword;
				}
			}
		}

		// Layout
		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		if (!empty($category_info)) {
			$data['category_layout'] = $this->model_warehouse_product_category->getLayouts($category_info['category_id']);
		} else {
			$data['category_layout'] = [];
		}
		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		//$data['wait_popup'] = $this->load->controller('wait_popup');
		//$data['marketplace_error_popup'] = $this->load->controller('marketplace_error_popup');
		//$data['alert_popup'] = $this->load->controller('marketplace_popup');
		
		$this->response->setOutput($this->load->view('warehouse/product/category_form', $data));
	}
	
	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('warehouse/product/category');
		$data = [];
		

		$json = [];

		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$json['error']['warning'] = ($lang['error_permission'] ?? '');
		}

		$required = [
			'category_id'          => 0,
			'category_description' => [],
			'image'                => '',
			'parent_id'            => 0,
			'sort_order'           => 0,
			'status'               => 0
		];

		$post_info = $this->request->post + $required;

		foreach ((array)$post_info['category_description'] as $language_id => $value) {
			if (!oc_validate_length((string)$value['name'], 1, 255)) {
				$json['error']['name_' . $language_id] = ($lang['error_name'] ?? '');
			}

			if (!oc_validate_length((string)$value['meta_title'], 1, 255)) {
				$json['error']['meta_title_' . $language_id] = ($lang['error_meta_title'] ?? '');
			}
		}

		// Category
		$this->load->model('warehouse/product/category');

		if (isset($post_info['category_id']) && $post_info['parent_id']) {
			$results = $this->model_warehouse_product_category->getPaths((int)$post_info['parent_id']);

			foreach ($results as $result) {
				if ($result['path_id'] == $post_info['category_id']) {
					$json['error']['parent'] = ($lang['error_parent'] ?? '');
					break;
				}
			}
		}

		// SEO
		if ($post_info['category_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($post_info['category_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!oc_validate_length($keyword, 1, 64)) {
						$json['error']['keyword_' . $store_id . '_' . $language_id] = ($lang['error_keyword'] ?? '');
					}

					if (!oc_validate_path($keyword)) {
						$json['error']['keyword_' . $store_id . '_' . $language_id] = ($lang['error_keyword_character'] ?? '');
					}

					$seo_url_info = $this->model_design_seo_url->getSeoUrlByKeyword($keyword, $store_id);
					//print("<pre>".print_r ($seo_url_info,true )."</pre>");

					if ($seo_url_info && (!isset($post_info['category_id']) || $seo_url_info['key'] != 'path' || $seo_url_info['value'] != $this->model_warehouse_product_category->getPath($post_info['category_id']))) {
						$json['error']['keyword_' . $store_id . '_' . $language_id] = ($lang['error_keyword_exists'] ?? '');
					}
				}
			}
		}

		if (isset($json['error']) && !isset($json['error']['warning'])) {
			$json['error']['warning'] = ($lang['error_warning'] ?? '');
		}

		if (!$json) {
			if (!$post_info['category_id']) {
				$json['category_id'] = $this->model_warehouse_product_category->addCategory($post_info);
			} else {
				$this->model_warehouse_product_category->editCategory($post_info['category_id'], $post_info);
			}

			$json['success'] = ($lang['text_success'] ?? '');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Repair
	 *
	 * @return void
	 */
	public function repair(): void {
		$this->load->language('warehouse/product/category');
		$data = [];
		

		$json = [];

		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$json['error'] = ($lang['error_permission'] ?? '');
		}

		if (!$json) {
			$this->load->model('warehouse/product/category');

			$this->model_warehouse_product_category->repairCategories();

			$json['success'] = ($lang['text_success'] ?? '');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Delete
	 *
	 * @return void
	 */
	public function delete(): void {
		$this->load->language('warehouse/product/category');
		$data = [];
		

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = (array)$this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$json['error'] = ($lang['error_permission'] ?? '');
		}

		if (!$json) {
			$this->load->model('warehouse/product/category');

			foreach ($selected as $category_id) {
				$this->model_warehouse_product_category->deleteCategory($category_id);
			}

			$json['success'] = ($lang['text_success'] ?? '');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Autocomplete
	 *
	 * @return void
	 */
	public function autocomplete(): void {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('warehouse/product/category');

			$filter_data = [
				'filter_name' => $this->request->get['filter_name'] . '%',
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => $this->config->get('config_autocomplete_limit')
			];

			$results = $this->model_warehouse_product_category->getCategories($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'category_id' => $result['category_id'],
					'name'        => $result['name']
				];
			}
		}

		$sort_order = [];

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function getDetails() {
		$this->load->language('warehouse/product/category');
		$data = [];
		

		$json = [];

		if (!$this->user->hasPermission('modify', 'catalog/category')) {
			$json['error'] = ($lang['error_permission'] ?? '');
		}

		if (isset($this->request->get['category_id'])) {
			$this->load->model('warehouse/product/category');
			$this->load->model('warehouse/product/product');

			$category_id = $this->request->get['category_id'];
			
			if (isset($this->request->get['product_id'])) {
				$this->model_product->removeProductSpecifics($this->request->get['product_id']);
			}
			
			$category_info = $this->model_warehouse_product_category->getDetails($category_id);
		
			if ($category_info) {
				$json = $category_info;
			} else {
				$json['error'] = ($lang['error_not_found'] ?? '');
			}
		} else {
			$json['error'] = ($lang['error_missing_data'] ?? '');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function uploadFromLink() {
        $this->load->language('warehouse/product/category');
        $data = [];
        

		$json = [];

	// Check permissions first
	if (!$this->user->hasPermission('modify', 'catalog/category')) {
		$json['error'] = ($lang['error_permission'] ?? '');
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
		return; // Stop execution if no permission
	}

    $this->load->model('warehouse/product/category');

	if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $category_id = isset($data['category_id']) ? (int)$data['category_id'] : 0;
        $piclink = isset($data['piclink']) ? $data['piclink'] : '';

		// Validate input
		if (empty($category_id) || empty($piclink)) {
			$json['error'] = 'Missing category_id or image URL';
		} else {
			$result = $this->model_warehouse_product_category->uploadImageFromLink($category_id, $piclink);

			if ($result['success']) {
				$json['success'] = ($lang['text_success'] ?? '');
				$json['image_url'] = $result['image_url'];
			} else {
				$json['error'] = $result['error'];
			}
		}
    } else {
		$json['error'] = 'Invalid request method';
	}

	$this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}
}
