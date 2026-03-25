<?php
//==============================================================================
// Smart Search v303.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
// 
// All code within this file is copyright Clear Thinking, LLC.
// You may not copy or reuse code within this file without written permission.
//==============================================================================

class ControllerExtensionModuleSmartsearch extends Controller {
	private $type = 'module';
	private $name = 'smartsearch';
	
	public function livesearch() {
		$settings = $this->getSettings();
		if (empty($settings['live_search'])) return;
		
		$currency = $this->session->data['currency'];
		$language = (isset($this->session->data['language'])) ? $this->session->data['language'] : $this->config->get('config_language');
		$data = $this->language->load('product/search');
		
		$filter_data = array(
			'filter_name'			=> $this->request->get['search'],
			'filter_tag'			=> $this->request->get['search'],
			'filter_description'	=> '',
			'filter_category_id'	=> 0,
			'filter_sub_category'	=> '',
			'sort'					=> '',
			'order'					=> '',
			'start'					=> 0,
			'limit'					=> $settings['live_limit'],
			'ajax'					=> true
		);
		
		$this->load->model('tool/image');
		$products = array();
		
		$this->load->model('extension/' . $this->type . '/' . $this->name);
		$smartsearch_results = $this->{'model_extension_' . $this->type . '_' . $this->name}->smartsearch($filter_data);
		$results = $this->{'model_extension_' . $this->type . '_' . $this->name}->getProducts($smartsearch_results, $filter_data);
		
		$this->load->model('catalog/product');
		$keywords = explode(' ', $this->request->get['search']);
		
		foreach ($results as $result) {
			if (empty($result)) continue;
			
			$image = ($settings['live_image_width']) ? $this->model_tool_image->resize($result['image'] ? $result['image'] : 'no_image.png', (int)$settings['live_image_width'], (int)$settings['live_image_height']) : false;
			$options = $this->model_catalog_product->getProductOptions($result['product_id']);
			$rating = ($this->config->get('config_review_status')) ? (int)$result['rating'] : false;
			
			$result['add']			= $this->url->link(($options ? 'product/product' : 'checkout/cart'), 'product_id=' . $result['product_id']);
			$result['description']	= implode('', array_slice(preg_split("//u", strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), -1, PREG_SPLIT_NO_EMPTY), 0, (int)$settings['live_description'])) . '...';
			$result['href']			= $this->url->link('product/product', 'product_id=' . $result['product_id']);
			$result['image']		= $image;
			$result['options']		= $options;
			$result['price']		= (!$this->config->get('config_customer_price') || $this->customer->isLogged()) ? $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $currency) : false;
			$result['rating']		= $rating;
			$result['reviews']		= sprintf($data['text_reviews'], (int)$result['reviews']);
			$result['special']		= ((float)$result['special']) ? $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $currency) : false;
			$result['tax']			= ($this->config->get('config_tax')) ? $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $currency) : false;
			$result['thumb']		= $image;
			
			foreach (array('name', 'description') as $field) {
				$highlighted = preg_filter('/' . str_replace('/', '\/', implode('|', array_map('preg_quote', $keywords))) . '/i', '<span class="highlight">$0</span>', str_replace('&amp;', '&', $result[$field]));
				if (!empty($highlighted)) {
					$result[$field] = $highlighted;
				}
			}
			
			$products[] = $result;
		}
		
		echo json_encode($products);
	}
	
	//==============================================================================
	// getSettings()
	//==============================================================================
	private function getSettings() {
		$code = (version_compare(VERSION, '3.0', '<') ? '' : $this->type . '_') . $this->name;
		
		$settings = array();
		$settings_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `code` = '" . $this->db->escape($code) . "' ORDER BY `key` ASC");
		
		foreach ($settings_query->rows as $setting) {
			$value = $setting['value'];
			if ($setting['serialized']) {
				$value = (version_compare(VERSION, '2.1', '<')) ? unserialize($setting['value']) : json_decode($setting['value'], true);
			}
			$split_key = preg_split('/_(\d+)_?/', str_replace($code . '_', '', $setting['key']), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			
				if (count($split_key) == 1)	$settings[$split_key[0]] = $value;
			elseif (count($split_key) == 2)	$settings[$split_key[0]][$split_key[1]] = $value;
			elseif (count($split_key) == 3)	$settings[$split_key[0]][$split_key[1]][$split_key[2]] = $value;
			elseif (count($split_key) == 4)	$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]] = $value;
			else 							$settings[$split_key[0]][$split_key[1]][$split_key[2]][$split_key[3]][$split_key[4]] = $value;
		}
		
		return $settings;
	}
}
?>