<?php
/* === Advanced Category Wall Module v1.1.0 for OpenCart by vytasmk at gmail === */
class ControllerExtensionModuleAdvancedCategoryWall extends Controller {
	public function index($settings) {
		$this->load->language('extension/module/advanced_category_wall');

		// Load additional modules that will be used
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');

		$data['categories'] = array();

		// Set some default values for using in controller
		if (!isset($settings['filter'])) 		$settings['filter'] = 'filter_all';
		if (!isset($settings['limit'])) 		$settings['limit'] = 0;
		if (!isset($settings['show_empty'])) 	$settings['show_empty'] = 1;	

		// Set some default values for using in template 
		// heading_title updated to support multilanguage 2019.03.24
		$data['heading_title'] 	= isset($settings['title_lang'][$this->config->get('config_language_id')])
			? $settings['title_lang'][$this->config->get('config_language_id')] 
			: (isset($settings['title']) ? $settings['title'] : '');

		$data['columns'] 		= isset($settings['columns']) 		? (int)$settings['columns'] : 4;
		$data['show_catname'] 	= isset($settings['show_catname']) 	? (int)$settings['show_catname'] : 1;

		$categories = array();

		// Check what filter we need to use
		switch ($settings['filter']) {
			case 'filter_selected':
				// Get only selected categories
				if (!empty($settings['category']))
					foreach ($settings['category'] as $category_id)
						$categories[] = $this->model_catalog_category->getCategory($category_id);
				break;

			case 'filter_all':
			default:
				// Get all top level categories if filter is set to show all top level categories
				$categories = $this->model_catalog_category->getCategories(0);
				break;
		}

		if (!empty($categories)) {
			// If limit is set then shown only selected amount of category
			if ((int)$settings['limit'] > 0)
				$categories = array_slice($categories, 0, (int)$settings['limit']);

			// Get category information
			foreach ($categories as $category_info) {

				// If is set to not show empty categories
				if (!$settings['show_empty'])
					// then count products inside that category (including subcategories)
					if (!$this->model_catalog_product->getTotalProducts(array('filter_category_id'  => $category_info['category_id'],'filter_sub_category' => true)))
						continue; // and if it is empty (product count is 0) then jump to next categorie

				// Get image of the categorie or show default placeholder image
				if ($category_info['image']) {
					$image = $this->model_tool_image->resize($category_info['image'], $settings['width'], $settings['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $settings['width'], $settings['height']);
				}

				// Prepare data for template
				$data['categories'][] = array(
					'category_id'  => $category_info['category_id'],
					'thumb'       => $image,
					'name'        => $category_info['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('config_product_description_length')) . '..',
					'href'        => $this->url->link('product/category', 'path=' . $category_info['category_id'])
				);
			}
		}

		// If there is something to show then call template
		if (!empty($data['categories'])) {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/advanced_category_wall.css');
			return $this->load->view('extension/module/advanced_category_wall.tpl', $data);
		}
	}
}