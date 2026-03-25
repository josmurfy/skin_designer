<?php

	/********************************************************************
	Version 1.0
		RapidCart Connector for OpenCart
		Copyright 2014 Purple Turtle Productions. All rights reserved.
		license	GNU General Public License version 3 or later; see GPLv3.txt
	By: Keneto 2014-11
	********************************************************************/

class ControllerModuleCartproductfeed extends Controller {

	function safeGetPostData($index) {
		if (isset($_POST[$index]))
			return $_POST[$index];
		else
			return '';
	}

	public function index() {

		//Load the language file for this module - catalog/language/module/cartproductfeed.php
		$this->language->load('module/cartproductfeed');

		//Get the title from the language file
    //  	$this->data['heading_title'] = $this->language->get('heading_title');

		//Load any required model files - catalog/product is a common one, or you can make your own DB access
		//methods in catalog/model/module/cartproductfeed.php
		$this->load->model('module/cartproductfeed');

		$token = $this->safeGetPostData('token');
		$request = $this->safeGetPostData('request');
		$product_limit_low = $this->safeGetPostData('product_limit_low');
		$product_limit_high = $this->safeGetPostData('product_limit_high');
		$specifier = $this->safeGetPostData('specifier');

		$error = 0;
		if (strlen($token) == 0)
			$error = 1;
		if (strlen($request) == 0)
			$error = 2;

		//Check token
		$data = $this->db->query('SELECT value FROM ' . DB_PREFIX . 'setting a WHERE a.key = \'cart_product_feed_token\';');
		if ($data->num_rows > 0)
			$saved_token = $data->row['value'];
		else
			$saved_token = '';

		if (strlen($saved_token) == 0)
			$error = 5;
		else if ($token != $saved_token)
			$error = 4;

		//Make Specifier safe from SQL injection
		if (strlen($specifier) > 0) {
			$specifierArray = explode(',', $specifier);
			foreach($specifierArray as &$item)
				$item = (int) $item;
			$specifier = implode(',', $specifier);
		}

		//Parse the request
		switch ($request) {
			case 'categories':
				$query = '
					SELECT cat.category_id as id, catdesc.name, cat.parent_id
					FROM ' . DB_PREFIX . 'category cat
					LEFT JOIN ' . DB_PREFIX . 'category_description catdesc ON (catdesc.category_id = cat.category_id)
					WHERE cat.status = 1
					';
				break;
			case 'options':
				$query = '
					SELECT opt.option_id, optval.option_value_id, ocd.name as attr_name, ovd.name as attr_val
					FROM ' . DB_PREFIX . 'option opt
					LEFT JOIN ' . DB_PREFIX . 'option_value optval ON (optval.option_id = opt.option_id) 
					LEFT JOIN ' . DB_PREFIX . 'option_description ocd ON (ocd.option_id = opt.option_id)
					LEFT JOIN ' . DB_PREFIX . 'option_value_description ovd ON (ovd.option_value_id = optval.option_value_id)
				';
				break;
			case 'optionlinks':
				$query = '
					SELECT *
					FROM ' . DB_PREFIX . 'product_option_value
				';
				break;
			/*case 'attributes':
				$query = '
					SELECT product_id, text
					FROM ' . DB_PREFIX . 'product_attribute att
					#LEFT JOIN ' . DB_PREFIX . 'attributes attdesc ON (attdesc.attribute_id = att.attribute_id)
				';
				if (strlen($specifier) > 0)
					$query .= "WHERE post_id in ($specifier) ";
				break;*/
			case 'products':
				$query = '
						SELECT product.product_id as id, productdesc.name as title, productdesc.description as description,
							product.model, product.sku, product.upc, product.jan, product.isbn, product.mpn, product.location, product.quantity,
							product.image, product.price, product.weight, product.length, product.width, product.height, product.minimum,
							stock.name as stock_status, manufacturer.name as vendor
						FROM ' . DB_PREFIX . 'product product
						LEFT JOIN ' . DB_PREFIX . 'product_description productdesc ON (productdesc.product_id = product.product_id)
						LEFT JOIN ' . DB_PREFIX . 'stock_status stock ON (stock.stock_status_id = product.stock_status_id)
						LEFT JOIN ' . DB_PREFIX . 'manufacturer manufacturer ON (manufacturer.manufacturer_id = product.manufacturer_id)
						WHERE product.status = 1
					';
				break;
			case 'productCatLinks':
				$query = '
					SELECT product_id, category_id
					FROM ' . DB_PREFIX . 'product_to_category';
				break;
			case 'init':
				//Do nothing
				$query = '';
				break;
			default:
				if ($error == 0)
					$error = 3;
		}

		//Parse any limits
		if ($product_limit_low + $product_limit_high > 0)
			$query .=  ' LIMIT ' . (int) $product_limit_low . ', ' . (int) $product_limit_high;

		$result = new stdClass();
		$result->version = 2;
		$result->error = $error;
		$result->endOfResults = false;
		if ($error == 0) {
			//$result->results = $this->model_module_cartproductfeed->db->query($query);
			if (strlen($query) > 0) {
				$result->results = $this->db->query($query);
				$result->results = $result->results->rows;
			} else
				$result->results = array();
		} else
			$result->results = array();
		$result->resultCount = count($result->results);

		if ($request == 'products') {
			$this->load->model('catalog/product');
			foreach($result->results as &$this_result) {

				//Lookup Attributes
				$query = '
					SELECT attr_d.name, attr.text
					FROM ' . DB_PREFIX . 'product_attribute attr
					LEFT JOIN ' . DB_PREFIX . 'attribute_description attr_d ON (attr_d.attribute_id = attr.attribute_id)
					WHERE attr.product_id=' . $this_result['id'];
				$att = $this->db->query($query);
				$this_result['attributes'] = $att->rows;

				//Ensure description exists
				if (!isset($this_result['description']))
					$this_result['description'] = '';
				else
					$this_result['description'] = strip_tags($this_result['description']);
				if (isset($this_result['description']) && (strlen($this_result['description']) > 2048))
					$this_result['description'] = substr($this_result['description'], 0, 2048);

				//url
				$this_result['url'] = $this->url->link('product/product', 'product_id=' . $this_result['id']);

				//image
				$images = $this->model_catalog_product->getProductImages($this_result['id']);
				foreach ($images as $index => $image)
					$this_result['image_url' . $index] = $image['image'];

			}
		}

		if ($request == 'init') {

			$query = 'SELECT COUNT(*) as product_count FROM ' . DB_PREFIX . 'product';
			$rs = $this->db->query($query);
			$result->productCount = $rs->row['product_count'];

			$query = 'SELECT COUNT(*) as category_count FROM ' . DB_PREFIX . 'category';
			$rs = $this->db->query($query);
			$result->categoryCount = $rs->row['category_count'];

			$query = 'SELECT COUNT(*) as category_link_count FROM ' . DB_PREFIX . 'product_to_category';
			$rs = $this->db->query($query);
			$result->categoryLinkCount = $rs->row['category_link_count'];

			$query = '
					SELECT COUNT(*) as option_count
					FROM ' . DB_PREFIX . 'option opt
					LEFT JOIN ' . DB_PREFIX . 'option_value optval ON (optval.option_id = opt.option_id)';
			$rs = $this->db->query($query);
			$result->optionCount = $rs->row['option_count'];

			$query = 'SELECT COUNT(*) as product_option_count FROM ' . DB_PREFIX . 'product_option_value';
			$rs = $this->db->query($query);
			$result->productOptionCount = $rs->row['product_option_count'];
			
		}

		//Display
		//$this->template = 'default/template/module/cartproductfeed.tpl';
		//$this->response->setOutput($this->render());

		echo json_encode($result);
	}
}
?>