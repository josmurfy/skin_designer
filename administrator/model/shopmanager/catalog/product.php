<?php
namespace Opencart\Admin\Model\Shopmanager\Catalog;
/**
 * Class Product
 *
 * Can be loaded using $this->load->model('shopmanager/catalog/product');
 *
 * @package Opencart\Admin\Model\Shopmanager
 */
class Product extends \Opencart\System\Engine\Model {
	/**
	 * Add Product
	 *
	 * Create a new product record in the database.
	 *
	 * @param array<string, mixed> $data array of data
	 *
	 * @return int returns the primary key of the new product record
	 *
	 * @example
	 *
	 * $product_data = [
	 *     'product_description'           => [],
	 *     'product_attribute_description' => [],
	 *     'master_id'                     => 'Master ID',
	 *     'model'                         => 'Product Model',
	 *     'sku'                           => 'Product Sku',
	 *     'upc'                           => 'Product Upc',
	 *     'ean'                           => 'Product Ean',
	 *     'jan'                           => 'Product Jan',
	 *     'isbn'                          => 'Product Isbn',
	 *     'mpn'                           => 'Product Mpn',
	 *     'location'                      => 'Location',
	 *     'variant'                       => [],
	 *     'override'                      => [],
	 *     'quantity'                      => 1,
	 *     'minimum'                       => 1,
	 *     'subtract'                      => 0,
	 *     'stock_status_id'               => 1,
	 *     'date_available'                => '2021-01-01',
	 *     'manufacturer_id'               => 0,
	 *     'shipping'                      => 0,
	 *     'price'                         => 1.00,
	 *     'points'                        => 0,
	 *     'weight'                        => 0.00000000,
	 *     'weight_class_id'               => 0,
	 *     'length'                        => 0.00000000,
	 *     'width'                         => 0.00000000,
	 *     'height'                        => 0.00000000,
	 *     'length_class_id'               => 0,
	 *     'status'                        => 0,
	 *     'tax_class_id'                  => 0,
	 *     'condition_id'                  => 0,
	 *     'unallocated_quantity'          => 1,
	 *     'to_feed'                       => 0,
	 *     'price_with_shipping'           => 1.00,
	 *     'shipping_cost'                 => 0.00,
	 *     'shipping_carrier'              => '',
	 *     'made_in_country_id'            => 0,
	 *     'sort_order'                    => 0,
	 *     'image'                         => ''
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_id = $this->model_shopmanager_catalog_product->addProduct($product_data);
	 */
	public function addProduct(array $data): int {

	//	print("<pre>".print_r (70,true )."</pre>");
	//print("<pre>".print_r ($data,true )."</pre>");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product` SET `master_id` = '" . (int)$data['master_id'] . "',
		`model` = '" . $this->db->escape($data['model']) . "', 
 		`variant` = '" . $this->db->escape(!empty($data['variant']) ? json_encode($data['variant']) : '') . "', 
		`override` = '" . $this->db->escape(!empty($data['override']) ? json_encode($data['override']) : '') . "',
		`sku` = '" . $this->db->escape($data['sku']) . "', `upc` = '" . $this->db->escape($data['upc']) . "', 
		`condition_id` = '" . $this->db->escape($data['condition_id']) . "', 
        `ean` = '" . $this->db->escape($data['ean']) . "', `jan` = '" . $this->db->escape($data['jan']) . "', 
        `isbn` = '" . $this->db->escape($data['isbn']) . "', `mpn` = '" . $this->db->escape($data['mpn']) . "',  
        `location` = '" . $this->db->escape($data['location']) . "', `quantity` = '" . (int)$data['quantity'] . "', 
        `unallocated_quantity` = '" . (int)$data['unallocated_quantity'] . "', `minimum` = '" . (int)$data['minimum'] . "', 
        `subtract` = '" . (int)$data['subtract'] . "', `stock_status_id` = '" . (int)$data['stock_status_id'] . "',
		`to_feed` = '". ($data['to_feed']??0) ."',`date_available` = '" . $this->db->escape($data['date_available']) . "', 
		`manufacturer_id` = '" . (int)$data['manufacturer_id'] . "',`shipping` = '" . (int)$data['shipping'] . "', 
		`price` = '" . (float)$data['price'] . "', `price_with_shipping` = '" . (float)$data['price_with_shipping'] . "', 
        `points` = '" . (int)$data['points'] . "', `weight` = '" . (float)$data['weight'] . "',
		`weight_class_id` = '" . (int)$data['weight_class_id'] . "',`length` = '" . (float)$data['length'] . "', 
		`width` = '" . (float)$data['width'] . "', `height` = '" . (float)$data['height'] . "', 
		`length_class_id` = '" . (int)$data['length_class_id'] . "', `status` = '" . (int)$data['status'] . "', 
		`tax_class_id` = '" . (int)$data['tax_class_id'] . "',`shipping_cost` = '" . (float)$data['shipping_cost'] . "',
		`shipping_carrier` = '" . $this->db->escape($data['shipping_carrier']). "',
		`made_in_country_id` = '" . $this->db->escape($data['made_in_country_id']??0) . "' ,
		`sort_order` = '" . (int)$data['sort_order'] . "', 	`date_added` = NOW()");
		$product_id = $this->db->getLastId();

		if ($data['image']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `image` = '" . $this->db->escape((string)$data['image']) . "' WHERE `product_id` = '" . (int)$product_id . "'");
		}

		if (isset($data['image_temp'])) {
			$this->load->model('shopmanager/tools');
			$this->model_shopmanager_tools->transferTempImages($product_id,$data); 
		}
		
		// Conditions
		if(isset($data['category_id'])){
			$this->load->model('shopmanager/condition');
			
			$data['conditions']=$this->model_shopmanager_condition->getConditionDetails($data['category_id'],$data['condition_id'],$data['condition_marketplace_item_id']??null);			
		}

		if ($data['sku']=='' && $data['condition_id']==1000){
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `sku` = '" . $this->db->escape($this->db->escape($data['upc'])) . "' WHERE `product_id` = '" . (int)$product_id . "'");
		}elseif ($data['sku']=='' && $data['condition_id']!=1000){
			$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `sku` = '" . $this->db->escape($product_id) . "' WHERE `product_id` = '" . (int)$product_id . "'");
		}

		
		// Description
		foreach ($data['product_description'] as $language_id => $value) {
			$this->model_shopmanager_catalog_product->addDescription($product_id, (int)$language_id, $data);
		}
		$this->model_shopmanager_catalog_product->refreshDescriptions($product_id);

		// Code
		if (isset($data['product_code'])) {
			foreach ($data['product_code'] as $product_code) {
				$this->model_shopmanager_catalog_product->addCode($product_id, $product_code);

			}
		}

		// Categories
		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->model_shopmanager_catalog_product->addCategory($product_id, $category_id);

			}
		}

		// Filters
		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->model_shopmanager_catalog_product->addFilter($product_id, $filter_id);
			}
		}

		// Stores
		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->model_shopmanager_catalog_product->addStore($product_id, $store_id);
			}
		}

		// Downloads
		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->model_shopmanager_catalog_product->addDownload($product_id, $download_id);
			}
		}

		// Related
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->model_shopmanager_catalog_product->addRelated($product_id, $related_id);
			}
		}

		// Attributes
		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->model_shopmanager_catalog_product->deleteAttributes($product_id, $product_attribute['attribute_id']);

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->model_shopmanager_catalog_product->addAttribute($product_id, $product_attribute['attribute_id'], $language_id, $product_attribute_description);
					}
				}
			}
		}

		// Options
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				$this->model_shopmanager_catalog_product->addOption($product_id, $product_option);
			}
		}

		// Subscription
		if (isset($data['product_subscription'])) {
			foreach ($data['product_subscription'] as $product_subscription) {
				$this->model_shopmanager_catalog_product->addSubscription($product_id, $product_subscription);
			}
		}

		// Discounts
		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->model_shopmanager_catalog_product->addDiscount($product_id, $product_discount);
			}
		}

		// Images
		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->model_shopmanager_catalog_product->addImage($product_id, $product_image);

			}
		}

		// Reward
		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->model_shopmanager_catalog_product->addReward($product_id, $customer_group_id, $product_reward);
				}
			}
		}

		// SEO
		if (isset($data['product_seo_url'])) {
			$this->load->model('design/seo_url');

			foreach ($data['product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					$this->model_design_seo_url->addSeoUrl('product_id', $product_id, $keyword, $store_id, $language_id);
				}
			}
		}

		// Layout
		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				if ($layout_id) {
					$this->model_shopmanager_catalog_product->addLayout($product_id, $store_id, $layout_id);
				}
			}
		}

		$this->cache->delete('product');

		return $product_id;
	}

	/**
	 * Edit Product
	 *
	 * Edit product record in the database.
	 *
	 * @param int                  $product_id primary key of the product record
	 * @param array<string, mixed> $data       array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $product_data = [
	 *     'product_description'           => [],
	 *     'product_attribute_description' => [],
	 *     'master_id'                     => 'Master ID',
	 *     'model'                         => 'Product Model',
	 *     'sku'                           => 'Product Sku',
	 *     'upc'                           => 'Product Upc',
	 *     'ean'                           => 'Product Ean',
	 *     'jan'                           => 'Product Jan',
	 *     'isbn'                          => 'Product Isbn',
	 *     'mpn'                           => 'Product Mpn',
	 *     'condition_id'                  => 0,
	 *     'location'                      => 'Location',
	 *     'variant'                       => [],
	 *     'override'                      => [],	
	 *     'quantity'                      => 1,
	 *     'unallocated_quantity'          => 1,
	 *     'minimum'                       => 1,
	 *     'subtract'                      => 0,
	 *     'stock_status_id'               => 1,
	 *     'date_available'                => '2021-01-01',
	 *     'manufacturer_id'               => 0,
	 *     'shipping'                      => 0,
	 *     'price'                         => 1.00,
	 *     'points'                        => 0,
	 *     'weight'                        => 0.00000000,
	 *     'weight_class_id'               => 0,
	 *     'length'                        => 0.00000000,
	 *     'width'                         => 0.00000000,
	 *     'height'                        => 0.00000000,
	 *     'length_class_id'               => 0,
	 *     'status'                        => 1,
	 *     'tax_class_id'                  => 0,
	 *     'sort_order'                    => 0,
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->editProduct($product_id, $product_data);
	 */
	public function editProduct(int $product_id, array $data): void {
		// Charger les models nécessaires
		$this->load->model('design/seo_url');
		
		//$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `model` = '" . $this->db->escape((string)$data['model']) . "', `location` = '" . $this->db->escape((string)$data['location']) . "', `variant` = '" . $this->db->escape(!empty($data['variant']) ? json_encode($data['variant']) : '') . "', `override` = '" . $this->db->escape(!empty($data['override']) ? json_encode($data['override']) : '') . "', `quantity` = '" . (int)$data['quantity'] . "', `minimum` = '" . (int)$data['minimum'] . "', `subtract` = '" . (isset($data['subtract']) ? (bool)$data['subtract'] : 0) . "', `stock_status_id` = '" . (int)$data['stock_status_id'] . "', `image` = '" . $this->db->escape((string)$data['image']) . "', `date_available` = '" . $this->db->escape((string)$data['date_available']) . "', `manufacturer_id` = '" . (int)$data['manufacturer_id'] . "', `shipping` = '" . (isset($data['shipping']) ? (bool)$data['shipping'] : 0) . "', `price` = '" . (float)$data['price'] . "', `points` = '" . (int)$data['points'] . "', `weight` = '" . (float)$data['weight'] . "', `weight_class_id` = '" . (int)$data['weight_class_id'] . "', `length` = '" . (float)$data['length'] . "', `width` = '" . (float)$data['width'] . "', `height` = '" . (float)$data['height'] . "', `length_class_id` = '" . (int)$data['length_class_id'] . "', `status` = '" . (bool)($data['status'] ?? 0) . "', `tax_class_id` = '" . (int)$data['tax_class_id'] . "', `sort_order` = '" . (int)$data['sort_order'] . "', `date_modified` = NOW() WHERE `product_id` = '" . (int)$product_id . "'");
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `model` = '" . $this->db->escape($data['model']) . "', 
				 `variant` = '" . $this->db->escape(!empty($data['variant']) ? json_encode($data['variant']) : '') . "', 
				 `override` = '" . $this->db->escape(!empty($data['override']) ? json_encode($data['override']) : '') . "',
				`shipping_cost` = '" . (float)$data['shipping_cost'] . "', `shipping_carrier` = '" . $this->db->escape($data['shipping_carrier']). "',
				`sku` = '" . $this->db->escape($data['sku']) . "', `upc` = '" . $this->db->escape($data['upc']) . "', 
				`ean` = '" . $this->db->escape($data['ean']) . "', `jan` = '" . $this->db->escape($data['jan']) . "', 
				`isbn` = '" . $this->db->escape($data['isbn']) . "', `mpn` = '" . $this->db->escape($data['mpn']) . "', 
				`location` = '" . $this->db->escape($data['location']) . "', `quantity` = '" . (int)$data['quantity'] . "',
				`condition_id` = '" . (int)$data['condition_id'] . "',
				`unallocated_quantity` = '" . (int)$data['unallocated_quantity'] . "', `minimum` = '" . (int)($data['minimum'] ?? 1) . "', 
				`subtract` = '" . (int)($data['subtract'] ?? 1) . "', `stock_status_id` = '" . (int)($data['stock_status_id'] ?? 0) . "', 
				`date_available` = '" . $this->db->escape($data['date_available'] ?? date('Y-m-d')) . "', `manufacturer_id` = '" . (int)$data['manufacturer_id'] . "', 
				`shipping` = '" . (int)$data['shipping'] . "', `price` = '" . (float)$data['price'] . "', 
				`price_with_shipping` = '" . (float)$data['price_with_shipping'] . "',
				`points` = '" . (int)($data['points'] ?? 0) . "', `weight` = '" . (float)$data['weight'] . "', `weight_class_id` = '" . (int)($data['weight_class_id'] ?? (int)$this->config->get('config_weight_class_id')) . "', 
				`length` = '" . (float)$data['length'] . "', `width` = '" . (float)$data['width'] . "', `height` = '" . (float)$data['height'] . "', 
				`length_class_id` = '" . (int)($data['length_class_id'] ?? (int)$this->config->get('config_length_class_id')) . "', `status` = '" . (int)$data['status'] . "', 
				`tax_class_id` = '" . (int)($data['tax_class_id'] ?? 0) . "', `sort_order` = '" . (int)($data['sort_order'] ?? 1) . "', 
				`date_modified` = NOW(),  `made_in_country_id` = '" . $this->db->escape($data['made_in_country_id']??'') . "' 
				WHERE `product_id` = '" . (int)$product_id . "'");

		// Description
		$this->model_shopmanager_catalog_product->deleteDescriptions($product_id);

		foreach ($data['product_description'] as $language_id => $value) {
			$this->model_shopmanager_catalog_product->addDescription($product_id, (int)$language_id, $data);
		}
		$this->model_shopmanager_catalog_product->refreshDescriptions($product_id);

		// Code
		$this->model_shopmanager_catalog_product->deleteCodes($product_id);

		if (isset($data['product_code'])) {
			foreach ($data['product_code'] as $product_code) {
				$this->model_shopmanager_catalog_product->addCode($product_id, $product_code);

			}
		}

		// Categories
		$this->model_shopmanager_catalog_product->deleteCategories($product_id);

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->model_shopmanager_catalog_product->addCategory($product_id, $category_id);
			}
		}

		// Filters
		$this->model_shopmanager_catalog_product->deleteFilters($product_id);

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->model_shopmanager_catalog_product->addFilter($product_id, $filter_id);
			}
		}

		// Stores
		$this->model_shopmanager_catalog_product->deleteStores($product_id);

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->model_shopmanager_catalog_product->addStore($product_id, $store_id);
			}
		}

		// Downloads
		$this->model_shopmanager_catalog_product->deleteDownloads($product_id);

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->model_shopmanager_catalog_product->addDownload($product_id, $download_id);
			}
		}

		// Related
		$this->model_shopmanager_catalog_product->deleteRelated($product_id);

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->model_shopmanager_catalog_product->addRelated($product_id, $related_id);
			}
		}

		// Attributes
		$this->model_shopmanager_catalog_product->deleteAttributes($product_id);

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->model_shopmanager_catalog_product->deleteAttributes($product_id, $product_attribute['attribute_id']);

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->model_shopmanager_catalog_product->addAttribute($product_id, $product_attribute['attribute_id'], $language_id, $product_attribute_description);
					}
				}
			}
		}

		// Options
		$this->model_shopmanager_catalog_product->deleteOptions($product_id);

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				$this->model_shopmanager_catalog_product->addOption($product_id, $product_option);
			}
		}

		// Subscription
		$this->model_shopmanager_catalog_product->deleteSubscriptions($product_id);

		if (isset($data['product_subscription'])) {
			foreach ($data['product_subscription'] as $product_subscription) {
				$this->model_shopmanager_catalog_product->addSubscription($product_id, $product_subscription);
			}
		}

		// Discounts
		$this->model_shopmanager_catalog_product->deleteDiscounts($product_id);

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->model_shopmanager_catalog_product->addDiscount($product_id, $product_discount);
			}
		}

		// Images
		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}
		$this->model_shopmanager_catalog_product->deleteImages($product_id);

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->model_shopmanager_catalog_product->addImage($product_id, $product_image);
			}
		}

		if (isset($data['image_temp'])) {
			$this->load->model('shopmanager/tools');
			if(!isset($data['product_image'])){
				$this->model_shopmanager_tools->deleteProductImagesFiles($product_id);
			}
			$this->model_shopmanager_tools->transferTempImages($product_id,$data);
		}

		// Rewards
		$this->model_shopmanager_catalog_product->deleteRewards($product_id);

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->model_shopmanager_catalog_product->addReward($product_id, $customer_group_id, $value);
				}
			}
		}

		// SEO
		$this->model_design_seo_url->deleteSeoUrlsByKeyValue('product_id', $product_id);

		if (isset($data['product_seo_url'])) {
			foreach ($data['product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					$this->model_design_seo_url->addSeoUrl('product_id', $product_id, $keyword, $store_id, $language_id);
				}
			}
		}

		// Layout
		$this->model_shopmanager_catalog_product->deleteLayouts($product_id);

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				if ($layout_id) {
					$this->model_shopmanager_catalog_product->addLayout($product_id, $store_id, $layout_id);
				}
			}
		}

		$this->cache->delete('product');
	}

	/**
	 * Copy Product
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return int
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->copyProduct($product_id);
	 */
	public function copyProduct(int $product_id): int {
		$new_product_id = 0;

		$product_info = $this->model_shopmanager_catalog_product->getProduct($product_id);

		if ($product_info) {
			$product_data = $product_info;

			$product_data['sku'] = '';//$product_id; //sku temporaory set to product id, will be updated in addProduct function
			//$product_data['upc'] = '';
			$product_data['rating'] = '0';
			$product_data['status'] = '1';
			$product_data['marketplace_item_id'] = 0;
			//$product_data['image'] ='';

			$product_data['product_attribute'] = $this->model_shopmanager_catalog_product->getAttributes($product_id);
			$product_data['product_code'] = $this->model_shopmanager_catalog_product->getCodes($product_id);
			$product_data['product_category'] = $this->model_shopmanager_catalog_product->getCategories($product_id);
			$product_data['product_description'] = $this->model_shopmanager_catalog_product->getDescriptions($product_id);
			$product_data['product_discount'] = $this->model_shopmanager_catalog_product->getDiscounts($product_id);
			$product_data['product_download'] = $this->model_shopmanager_catalog_product->getDownloads($product_id);
			$product_data['product_filter'] = $this->model_shopmanager_catalog_product->getFilters($product_id);
			$product_data['product_image'] = $this->model_shopmanager_catalog_product->getImages($product_id);
			$product_data['product_layout'] = $this->model_shopmanager_catalog_product->getLayouts($product_id);
			$product_data['product_option'] = $this->model_shopmanager_catalog_product->getOptions($product_id);
			$product_data['product_subscription'] = $this->model_shopmanager_catalog_product->getSubscriptions($product_id);
			$product_data['product_related'] = $this->model_shopmanager_catalog_product->getRelated($product_id);
			$product_data['product_reward'] = $this->model_shopmanager_catalog_product->getRewards($product_id);
			$product_data['product_store'] = $this->model_shopmanager_catalog_product->getStores($product_id);

			foreach ($product_data['product_option'] as $po => $product_option) {
				$product_data['product_option'][$po]['product_option_id'] = 0;

				if (!empty($product_option['product_option_value'])) {
					foreach ($product_option['product_option_value'] as $pov => $product_option_value) {
						$product_data['product_option'][$po]['product_option_value'][$pov]['product_option_value_id'] = 0;
					}
				}
			}

			$new_product_id = $this->model_shopmanager_catalog_product->addProduct($product_data);
		}

		return $new_product_id;
	}

	/**
	 * Delete Product
	 *
	 * Delete product record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteProduct($product_id);
	 */
	public function deleteProduct(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . (int)$product_id . "'");

		$this->model_shopmanager_catalog_product->deleteAttributes($product_id);
		$this->model_shopmanager_catalog_product->deleteCodes($product_id);
		$this->model_shopmanager_catalog_product->deleteCategories($product_id);
		$this->model_shopmanager_catalog_product->deleteDescriptions($product_id);
		$this->model_shopmanager_catalog_product->deleteDiscounts($product_id);
		$this->model_shopmanager_catalog_product->deleteDownloads($product_id);
		$this->model_shopmanager_catalog_product->deleteFilters($product_id);
		$this->model_shopmanager_catalog_product->deleteImages($product_id);
		$this->model_shopmanager_catalog_product->deleteLayouts($product_id);
		$this->model_shopmanager_catalog_product->deleteOptions($product_id);
		$this->model_shopmanager_catalog_product->deleteRelated($product_id);
		$this->model_shopmanager_catalog_product->deleteReports($product_id);
		$this->model_shopmanager_catalog_product->deleteRewards($product_id);
		$this->model_shopmanager_catalog_product->deleteStores($product_id);
		$this->model_shopmanager_catalog_product->deleteSubscriptions($product_id);

		$this->load->model('shopmanager/tools');	
		$this->model_shopmanager_tools->deleteProductImagesFiles($product_id);
	

		// Review
		$this->load->model('shopmanager/review');

		$this->model_shopmanager_review->deleteReviewsByProductId($product_id);

		// SEO
		$this->load->model('design/seo_url');

		$this->model_design_seo_url->deleteSeoUrlsByKeyValue('product_id', $product_id);

		// Coupon
		$this->load->model('marketing/coupon');

		$this->model_marketing_coupon->deleteProductsByProductId($product_id);

		$this->model_shopmanager_catalog_product->editMasterId($product_id, 0);

		$this->cache->delete('product');
	}

	/**
	 * Add Variant
	 *
	 * @param int                  $master_id primary key of the product record
	 * @param array<string, mixed> $data      array of data
	 *
	 * @return int
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_id = $this->model_shopmanager_catalog_product->addVariant($master_id, $data);
	 */
	public function addVariant(int $master_id, array $data): int {
		$product_data = [];

		// Use master values to override the values
		$master_info = $this->model_shopmanager_catalog_product->getProduct($master_id);

		if ($master_info) {
			// We use the override to override the master product values
			if (isset($data['override'])) {
				$override = (array)$data['override'];
			} else {
				$override = [];
			}

			$ignore = [
				'product_id',
				'master_id',
				'quantity',
				'override',
				'variant'
			];

			foreach ($master_info as $key => $value) {
				// So if key not in override or ignore list we replace with master value
				if (!array_key_exists($key, $override) && !in_array($key, $ignore)) {
					$product_data[$key] = $value;
				}
			}

			// Descriptions
			$product_descriptions = $this->model_shopmanager_catalog_product->getDescriptions($master_id);

			foreach ($product_descriptions as $language_id => $product_description) {
				foreach ($product_description as $key => $value) {
					// If an override has been found, we use the POST data values
					if (!isset($override['product_description'][$language_id][$key])) {
						$product_data['product_description'][$language_id][$key] = $value;
					}
				}
			}

			// Attributes
			if (!isset($override['product_attribute'])) {
				$product_data['product_attribute'] = $this->model_shopmanager_catalog_product->getAttributes($master_id);
			}

			// Category
			if (!isset($override['product_category'])) {
				$product_data['product_category'] = $this->model_shopmanager_catalog_product->getCategories($master_id);
			}

			// Discounts
			if (!isset($override['product_discount'])) {
				$product_data['product_discount'] = $this->model_shopmanager_catalog_product->getDiscounts($master_id);
			}

			// Downloads
			if (!isset($override['product_download'])) {
				$product_data['product_download'] = $this->model_shopmanager_catalog_product->getDownloads($master_id);
			}

			// Filters
			if (!isset($override['product_filter'])) {
				$product_data['product_filter'] = $this->model_shopmanager_catalog_product->getFilters($master_id);
			}

			// Images
			if (!isset($override['product_image'])) {
				$product_data['product_image'] = $this->model_shopmanager_catalog_product->getImages($master_id);
			}

			// Layouts
			if (!isset($override['product_layout'])) {
				$product_data['product_layout'] = $this->model_shopmanager_catalog_product->getLayouts($master_id);
			}

			// Options
			// product_option should not be used if variant product

			// Subscriptions
			if (!isset($override['product_subscription'])) {
				$product_data['product_subscription'] = $this->model_shopmanager_catalog_product->getSubscriptions($master_id);
			}

			// Related
			if (!isset($override['product_related'])) {
				$product_data['product_related'] = $this->model_shopmanager_catalog_product->getRelated($master_id);
			}

			// Rewards
			if (!isset($override['product_reward'])) {
				$product_data['product_reward'] = $this->model_shopmanager_catalog_product->getRewards($master_id);
			}

			// SEO
			// product_seo table is not overwritten because that needs to have unique seo keywords for every product

			// Stores
			if (!isset($override['product_store'])) {
				$product_data['product_store'] = $this->model_shopmanager_catalog_product->getStores($master_id);
			}
		}

		// If override set the POST data values
		foreach ($data as $key => $value) {
			if (!isset($product_data[$key])) {
				$product_data[$key] = $value;
			}
		}

		// Product Description
		if (isset($data['product_description'])) {
			foreach ($data['product_description'] as $language_id => $product_description) {
				foreach ($product_description as $key => $value) {
					if (!isset($product_data['product_description'][$language_id][$key])) {
						$product_data['product_description'][$language_id][$key] = $value;
					}
				}
			}
		}

		// Product add with master product overridden values
		return $this->model_shopmanager_catalog_product->addProduct($product_data);
	}

	/**
	 * Edit Variant
	 *
	 * @param int                  $master_id  primary key of the product record
	 * @param int                  $product_id primary key of the product record
	 * @param array<string, mixed> $data       array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->editVariant($master_id, $product_id, $data);
	 */
	public function editVariant(int $master_id, int $product_id, array $data): void {
		$product_data = [];

		// Use master values to override the values
		$master_info = $this->model_shopmanager_catalog_product->getProduct($master_id);

		if ($master_info) {
			// We use the override to override the master product values
			if (isset($data['override'])) {
				$override = (array)$data['override'];
			} else {
				$override = [];
			}

			$ignore = [
				'product_id',
				'master_id',
				'quantity',
				'override',
				'variant'
			];

			foreach ($master_info as $key => $value) {
				// So if key not in override or ignore list we replace with master value
				if (!array_key_exists($key, $override) && !in_array($key, $ignore)) {
					$product_data[$key] = $value;
				}
			}

			// Description
			$product_descriptions = $this->model_shopmanager_catalog_product->getDescriptions($master_id);

			foreach ($product_descriptions as $language_id => $product_description) {
				foreach ($product_description as $key => $value) {
					if (!isset($override['product_description'][$language_id][$key])) {
						$product_data['product_description'][$language_id][$key] = $value;
					}
				}
			}

			// Attributes
			if (!isset($override['product_attribute'])) {
				$product_data['product_attribute'] = $this->model_shopmanager_catalog_product->getAttributes($master_id);
			}

			// Category
			if (!isset($override['product_category'])) {
				$product_data['product_category'] = $this->model_shopmanager_catalog_product->getCategories($master_id);
			}

			// Discounts
			if (!isset($override['product_discount'])) {
				$product_data['product_discount'] = $this->model_shopmanager_catalog_product->getDiscounts($master_id);
			}

			// Downloads
			if (!isset($override['product_download'])) {
				$product_data['product_download'] = $this->model_shopmanager_catalog_product->getDownloads($master_id);
			}

			// Filters
			if (!isset($override['product_filter'])) {
				$product_data['product_filter'] = $this->model_shopmanager_catalog_product->getFilters($master_id);
			}

			// Images
			if (!isset($override['product_image'])) {
				$product_data['product_image'] = $this->model_shopmanager_catalog_product->getImages($master_id);
			}

			// Layouts
			if (!isset($override['product_layout'])) {
				$product_data['product_layout'] = $this->model_shopmanager_catalog_product->getLayouts($master_id);
			}

			// Options
			// product_option should not be used if variant product

			// Subscription
			if (!isset($override['product_subscription'])) {
				$product_data['product_subscription'] = $this->model_shopmanager_catalog_product->getSubscriptions($master_id);
			}

			// Related
			if (!isset($override['product_related'])) {
				$product_data['product_related'] = $this->model_shopmanager_catalog_product->getRelated($master_id);
			}

			// Rewards
			if (!isset($override['product_reward'])) {
				$product_data['product_reward'] = $this->model_shopmanager_catalog_product->getRewards($master_id);
			}

			// SEO
			// product_seo table is not overwritten because that needs to have unique seo keywords for every product

			// Stores
			if (!isset($override['product_store'])) {
				$product_data['product_store'] = $this->model_shopmanager_catalog_product->getStores($master_id);
			}
		}

		// If override set the POST data values
		foreach ($data as $key => $value) {
			if (!isset($product_data[$key])) {
				$product_data[$key] = $value;
			}
		}

		// Product Description
		if (isset($data['product_description'])) {
			foreach ($data['product_description'] as $language_id => $product_description) {
				foreach ($product_description as $key => $value) {
					if (!isset($product_data['product_description'][$language_id][$key])) {
						$product_data['product_description'][$language_id][$key] = $value;
					}
				}
			}
		}

		// Override the variant product data with the master product values
		$this->model_shopmanager_catalog_product->editProduct($product_id, $product_data);
	}

	/**
	 * Edit Variants
	 *
	 * @param int                  $master_id primary key of the product record
	 * @param array<string, mixed> $data      array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->editVariants($product_id, $data);
	 */
	public function editVariants(int $master_id, array $data): void {
		// product_option should not be passed to product variants
		unset($data['product_option']);

		// If product is master update variants
		$products = $this->model_shopmanager_catalog_product->getProducts(['filter_master_id' => $master_id]);

		foreach ($products as $product) {
			$product_data = [];

			// We use the override to override the master product values
			if ($product['override']) {
				$override = $product['override'];
			} else {
				$override = [];
			}

			$replace = [
				'product_id',
				'master_id',
				'quantity',
				'override',
				'variant'
			];

			// Now we want to
			foreach ($product as $key => $value) {
				// So if key not in override or ignore list we replace with master value
				if (array_key_exists($key, $override) || in_array($key, $replace)) {
					$product_data[$key] = $value;
				}
			}

			// Descriptions
			$product_descriptions = $this->model_shopmanager_catalog_product->getDescriptions($product['product_id']);

			foreach ($product_descriptions as $language_id => $product_description) {
				foreach ($product_description as $key => $value) {
					// If override set use the POST data values
					if (isset($override['product_description'][$language_id][$key])) {
						$product_data['product_description'][$language_id][$key] = $value;
					}
				}
			}

			// Attributes
			if (isset($override['product_attribute'])) {
				$product_data['product_attribute'] = $this->model_shopmanager_catalog_product->getAttributes($product['product_id']);
			}

			// Category
			if (isset($override['product_category'])) {
				$product_data['product_category'] = $this->model_shopmanager_catalog_product->getCategories($product['product_id']);
			}

			// Discounts
			if (isset($override['product_discount'])) {
				$product_data['product_discount'] = $this->model_shopmanager_catalog_product->getDiscounts($product['product_id']);
			}

			// Downloads
			if (isset($override['product_download'])) {
				$product_data['product_download'] = $this->model_shopmanager_catalog_product->getDownloads($product['product_id']);
			}

			// Filters
			if (isset($override['product_filter'])) {
				$product_data['product_filter'] = $this->model_shopmanager_catalog_product->getFilters($product['product_id']);
			}

			// Images
			if (isset($override['product_image'])) {
				$product_data['product_image'] = $this->model_shopmanager_catalog_product->getImages($product['product_id']);
			}

			// Layouts
			if (isset($override['product_layout'])) {
				$product_data['product_layout'] = $this->model_shopmanager_catalog_product->getLayouts($product['product_id']);
			}

			// Subscription
			if (isset($override['product_subscription'])) {
				$product_data['product_subscription'] = $this->model_shopmanager_catalog_product->getSubscriptions($product['product_id']);
			}

			// Related
			if (isset($override['product_related'])) {
				$product_data['product_related'] = $this->model_shopmanager_catalog_product->getRelated($product['product_id']);
			}

			// Rewards
			if (isset($override['product_reward'])) {
				$product_data['product_reward'] = $this->model_shopmanager_catalog_product->getRewards($product['product_id']);
			}

			// SEO
			$product_data['product_seo_url'] = $this->model_shopmanager_catalog_product->getSeoUrls($product['product_id']);

			// Stores
			if (isset($override['product_store'])) {
				$product_data['product_store'] = $this->model_shopmanager_catalog_product->getStores($product['product_id']);
			}

			// If override set the POST data values
			foreach ($data as $key => $value) {
				if (!isset($product_data[$key])) {
					$product_data[$key] = $value;
				}
			}

			// Descriptions
			if (isset($data['product_description'])) {
				foreach ($data['product_description'] as $language_id => $product_description) {
					foreach ($product_description as $key => $value) {
						// If override set use the POST data values
						if (!isset($product_data['product_description'][$language_id][$key])) {
							$product_data['product_description'][$language_id][$key] = $value;
						}
					}
				}
			}

			$this->model_shopmanager_catalog_product->editProduct($product['product_id'], $product_data);
		}
	}

	/**
	 * Edit Rating
	 *
	 * Edit product rating record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 * @param int $rating
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->editRating($result['product_id'], $this->model_shopmanager_review->getRating($product_id));
	 */
	public function editRating(int $product_id, int $rating): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `rating` = '" . (int)$rating . "', `date_modified` = NOW() WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Edit Master ID
	 *
	 * Edit product master record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 * @param int $master_id  primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->editMasterId($product_id, 0);
	 */
	public function editMasterId(int $product_id, int $master_id): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET `master_id` = '" . (int)$master_id . "', `date_modified` = NOW() WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Get Product
	 *
	 * Get the record of the product record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<string, mixed> product record that has product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_info = $this->model_shopmanager_catalog_product->getProduct($product_id);
	 */
	public function getProduct(int $product_id): array {

		$product_data = [];
		$query = $this->db->query("SELECT DISTINCT *,pd.`name` AS `name_description`,pd.`color` AS `color_description` , `condition_id` ,
			(SELECT `name` FROM `" . DB_PREFIX . "manufacturer` ma WHERE  p.`manufacturer_id` = ma.`manufacturer_id` ) AS brand,
			(SELECT `name` FROM `" . DB_PREFIX . "manufacturer` ma WHERE  p.`manufacturer_id` = ma.`manufacturer_id` ) AS manufacturer,
			(SELECT `name` FROM `" . DB_PREFIX . "condition` c WHERE p.`condition_id` = c.`condition_id` AND c.`language_id` = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1) AS `condition_name`,
			
			(SELECT ca.`category_id` 
				FROM `" . DB_PREFIX . "product_to_category` pc 
				LEFT JOIN `" . DB_PREFIX . "category` ca ON (pc.`category_id` = ca.`category_id`)
				LEFT JOIN `" . DB_PREFIX . "category_description` cad ON (ca.`category_id` = cad.`category_id` AND cad.`language_id` = '" . (int)$this->config->get('config_language_id') . "' )
				WHERE pc.`product_id` = '" . (int)$product_id . " ' AND ca.`leaf` = 1
				LIMIT 1) AS `category_id`,
			(SELECT ca.`site_id`
			FROM `" . DB_PREFIX . "product_to_category` pc
				LEFT JOIN `" . DB_PREFIX . "category` ca 
				ON (pc.`category_id` = ca.`category_id`)
				WHERE pc.`product_id` = '" . (int)$product_id . "' 
				AND ca.`leaf` = 1
				LIMIT 1) AS `site_id`,
			(SELECT cad.`name` 
				FROM `" . DB_PREFIX . "product_to_category` pc 
				LEFT JOIN `" . DB_PREFIX . "category` ca ON (pc.`category_id` = ca.`category_id`)
				LEFT JOIN `" . DB_PREFIX . "category_description` cad ON (ca.`category_id` = cad.`category_id` AND cad.`language_id` = '" . (int)$this->config->get('config_language_id') . "'  )
				WHERE pc.`product_id` = '" . (int)$product_id . "' AND ca.`leaf` = 1
				LIMIT 1) AS `category_name`
			FROM `" . DB_PREFIX . "product` p 
			LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`) 
			
			WHERE p.`product_id` = '" . (int)$product_id . "' AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");//, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword
	
		if ($query->num_rows) {
			$product_data = $query->row;

			$product_data['variant'] = $product_data['variant'] ? json_decode($product_data['variant'], true) : [];
			$product_data['override'] = $product_data['override'] ? json_decode($product_data['override'], true) : [];
		}

		return $product_data;
	}

	/**
	 * Get Products
	 *
	 * Get the record of the product records in the database.
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return array<int, array<string, mixed>> product records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'filter_name'            => 'Product Name',
	 *     'filter_model'           => 'Product Model',
	 *     'filter_category_id'     => 0,
	 *     'filter_manufacturer_id' => 0,
	 *     'filter_price_from'      => '0.0000',
	 *     'filter_price_to'        => '100.0000',
	 *     'filter_quantity_from'   => 1,
	 *     'filter_quantity_to'     => 100,
	 *     'filter_status'          => 1,
	 *     'start'                  => 0,
	 *     'limit'                  => 10
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $results = $this->model_shopmanager_catalog_product->getProducts($filter_data);
	 */
	public function getProducts(array $data = []): array {


	$sql = "SELECT p.*, 
	(SELECT ptc.`category_id` FROM `" . DB_PREFIX . "product_to_category` ptc WHERE ptc.`product_id` = p.`product_id` LIMIT 1) AS category_id,
	pd.`name`,
	IF(pd.`specifics` IS NOT NULL AND pd.`specifics` != '', '1', '0') AS `has_specifics`,
	EXISTS(SELECT 1 FROM `" . DB_PREFIX . "product_info_sources` WHERE `upc` = p.`upc`) AS `has_sources`,
	(SELECT c.`name` 
		FROM `" . DB_PREFIX . "condition` c 
		WHERE p.`condition_id` = c.`condition_id` 
		AND c.`language_id` = '" . (int)$this->config->get('config_language_id') . "' 
		LIMIT 1
	) AS `condition_name`
	FROM `" . DB_PREFIX . "product` p
	INNER JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id` AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "')";
	if (!empty($data['filter_marketplace_account_id'])) {
		
		$sql .= " LEFT JOIN `" . DB_PREFIX . "product_marketplace` pm ON (p.`product_id`=pm.`product_id` 
		AND pm.`marketplace_account_id` = '" . $this->db->escape($data['filter_marketplace_account_id']) . "')"; 
	}
	
	$sql .= " WHERE 1=1";

	if (!empty($data['filter_master_id'])) {
			$sql .= " AND `p`.`master_id` = '" . (int)$data['filter_master_id'] . "'";
	}

	if (!empty($data['filter_name'])) {
		$sql .= " AND LCASE(`pd`.`name`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_name']) . '%') . "'";
	}

	if (!empty($data['filter_model'])) {
			$sql .= " AND (LCASE(`p`.`model`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_model']) . '%') . "' OR LCASE(`pc`.`value`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_model']) . '%') . "')";
	}

	if (isset($data['filter_category_id']) && $data['filter_category_id'] !== '') {
		//		$sql .= " AND EXISTS(SELECT 1 FROM " . DB_PREFIX . "product_to_category ptc2 WHERE ptc2.product_id = p.product_id AND ptc2.category_id LIKE '" . $this->db->escape($data['filter_category_id']) . "%')";
			$sql .= " AND `p`.`product_id` IN (SELECT `p2c`.`product_id` FROM `" . DB_PREFIX . "product_to_category` `p2c` WHERE `p2c`.`category_id` = '" . (int)$data['filter_category_id'] . "')";
	}

	if (isset($data['filter_manufacturer_id']) && $data['filter_manufacturer_id'] !== '') {
		$sql .= " AND `p`.`manufacturer_id` = '" . (int)$data['filter_manufacturer_id'] . "'";
	}

	if (isset($data['filter_price_from']) && $data['filter_price_from'] !== '') {
		$sql .= " AND `p`.`price` >= '" . (float)$data['filter_price_from'] . "'";
	}

	if (isset($data['filter_price_to']) && $data['filter_price_to'] !== '') {
		$sql .= " AND `p`.`price` <= '" . (float)$data['filter_price_to'] . "'";
	}

	if (isset($data['filter_quantity_from']) && $data['filter_quantity_from'] !== '') {
		$sql .= " AND (`p`.`quantity` +  p.`unallocated_quantity`) >= '" . (int)$data['filter_quantity_from'] . "'";
	}

	if (isset($data['filter_quantity_to']) && $data['filter_quantity_to'] !== '') {
		$sql .= " AND (`p`.`quantity` +  p.`unallocated_quantity`) <= '" . (int)$data['filter_quantity_to'] . "'";
	}

	if (isset($data['filter_status']) && $data['filter_status'] !== '') {
		$sql .= " AND `p`.`status` = '" . (int)$data['filter_status'] . "'";
	}

	if (isset($data['filter_name_length'])) {
		if ($data['filter_name_length'] === '1') {
			$sql .= " AND LENGTH(CONVERT(pd.`name` USING ascii)) > 80";
		} elseif ($data['filter_name_length'] === '0') {
			$sql .= " AND (pd.`name` IS NULL OR pd.`name` = '')";
		}
	}
	
	if (isset($data['filter_invalid_price']) && $data['filter_invalid_price'] === '1') {
		$sql .= " AND p.`price` < 0";
	}

	if (!empty($data['filter_sku'])) {
		$sql .= " AND (p.`sku` LIKE '" . $this->db->escape($data['filter_sku']) . "%' OR p.`upc` LIKE '" . $this->db->escape($data['filter_sku']) . "%' OR p.`product_id` LIKE '" . $this->db->escape($data['filter_sku']) . "%') ";
	}

	if (!empty($data['filter_product_id'])) {
		$sql .= " AND p.`product_id` LIKE '" . $this->db->escape($data['filter_product_id']) . "%'";
	}
	if (isset($data['filter_unallocated_quantity']) && !is_null($data['filter_unallocated_quantity'])) {
		$sql .= " AND p.`unallocated_quantity` = '" . (int)$data['filter_unallocated_quantity'] . "'";
	}
	if (isset($data['filter_location']) && !is_null($data['filter_location'])) {
		$sql .= " AND p.`location` = '" . (int)$data['filter_location'] . "'";
	}

	if (!empty($data['filter_marketplace_account_id'])) {
		$sql .= " AND pm.`marketplace_account_id` = '" . $this->db->escape($data['filter_marketplace_account_id']) . "'"; 
	}

	if (isset($data['filter_specifics']) && $data['filter_specifics'] !== '*') {
		switch ($data['filter_specifics']) {
			case '0':
				$sql .= " AND (pd.`specifics` IS NULL OR pd.`specifics` = '' OR pd.`specifics` = 'null')";
				break;
			case '1':
				$sql .= " AND NOT (pd.`specifics` IS NULL OR pd.`specifics` IN ('', 'null', '[]')) 
							AND JSON_VALID(pd.`specifics`) = 1";
				break;
			case '2':
				$sql .= " AND pd.`specifics` IN ('null', '[]') 
							AND (JSON_VALID(pd.`specifics`) = 0 OR pd.`specifics` = '[]')";
				break;
		}
	}
	
	// MODIFIÉ : filtre sources avec EXISTS
	if (isset($data['filter_sources']) && $data['filter_sources'] !== '*') {
		switch ($data['filter_sources']) {
			case '0':
				$sql .= " AND NOT EXISTS(SELECT 1 FROM `" . DB_PREFIX . "product_info_sources` WHERE `upc` = p.`upc`)";
				break;
			case '1':
				$sql .= " AND EXISTS(SELECT 1 FROM `" . DB_PREFIX . "product_info_sources` WHERE `upc` = p.`upc`)";
				break;
			case '2':
				$sql .= " AND EXISTS(SELECT 1 FROM `" . DB_PREFIX . "product_info_sources` WHERE `upc` = p.`upc` AND `upc` IN ('null', '[]'))";
				break;
		}
	}
	
	if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
		if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
			$sql .= " AND (p.`image` IS NOT NULL AND p.`image` <> '' AND p.`image` <> 'no_image.png')";
		} else {
			$sql .= " AND (p.`image` IS NULL OR p.`image` = '' OR p.`image` = 'no_image.png')";
		}
	}

	// Filtre marketplace
	if (isset($data['filter_marketplace']) && $data['filter_marketplace'] !== '' && $data['filter_marketplace'] !== '*') {
		if ($data['filter_marketplace'] == '0') {
			// NOT LISTED: Produits NON listés sur marketplace ET avec quantity totale > 0
			$sql .= " AND NOT EXISTS(
				SELECT 1 FROM `" . DB_PREFIX . "product_marketplace` pm 
				WHERE pm.`product_id` = p.`product_id` 
				AND pm.`marketplace_item_id` IS NOT NULL 
				AND pm.`marketplace_item_id` != ''
			)";
			$sql .= " AND (p.`quantity` + p.`unallocated_quantity`) > 0";
		} elseif ($data['filter_marketplace'] == '1') {
			// LISTED: Produits listés sur au moins un marketplace
			$sql .= " AND EXISTS(
				SELECT 1 FROM `" . DB_PREFIX . "product_marketplace` pm 
				WHERE pm.`product_id` = p.`product_id` 
				AND pm.`marketplace_item_id` IS NOT NULL 
				AND pm.`marketplace_item_id` != ''
			)";
		} elseif ($data['filter_marketplace'] == '2') {
			// ERROR LISTING: Produits avec erreur de listing
			$sql .= " AND EXISTS(
				SELECT 1 FROM `" . DB_PREFIX . "product_marketplace` pm 
				WHERE pm.`product_id` = p.`product_id` 
				AND (pm.`error` IS NOT NULL AND pm.`error` != '')
			)";
		}
	}

	// SUPPRIMÉ : GROUP BY p.product_id

	$sort_data = [
		'pd.name',
		'p.model',
		'p.price',
		'p.quantity',
		'p.status',
		'p.sort_order',
		'p.product_id',
		'p.condition_id',
		'p.unallocated_quantity',
		'p.location',
		'has_specifics',
		'has_sources',
	];

	if (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2) {
		$sql .= " ORDER BY p.`quantity`";
	}elseif (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		$sql .= " ORDER BY " . $data['sort'];
	} else {
		$sql .= " ORDER BY pd.`name`";
	}

	if (isset($data['order']) && ($data['order'] == 'DESC')) {
		$sql .= " DESC";
	} elseif (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2){
		$sql .= " ASC";
	}else {
		$sql .= " ASC";
	}

	if (isset($data['start']) || isset($data['limit'])) {
		if ($data['start'] < 0) {
			$data['start'] = 0;
		}
		
		if ($data['limit'] < 1) {
			$data['limit'] = 20;
		}/*else {
			$data['start'] = 0;
			$data['limit'] = 20;
		}*/

		/*if (
			((isset($data['filter_marketplace']) && $data['filter_marketplace'] == '0') ||
			(isset($data['filter_image']) && $data['filter_image'] == '2' )||
			(isset($data['filter_sources']) && $data['filter_sources'] == '0') ||
			(isset($data['filter_specifics']) && $data['filter_specifics'] == '0')  ||
			(isset($data['filter_invalid_price']) && $data['filter_invalid_price'] == '1' )||
			(isset($data['filter_name_length']) && $data['filter_name_length'] == '0') ||
			(isset($data['filter_name_length']) && $data['filter_name_length'] == '1') ||
			(isset($data['filter_invalid_price']) && $data['filter_invalid_price'] == '1')) &&
			(isset($data['filter_status']) && $data['filter_status']=='1')
		) {
			$data['start'] = 0;
			$data['limit'] = 50000;
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		} else {*/
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		//}
	}
	
	//print("<pre>".print_r ($sql,true )."</pre>");  

	$product_data = [];

	$query = $this->db->query($sql);

	$this->load->model('shopmanager/marketplace');

	if (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2){
		$query->rows=$this->model_shopmanager_catalog_product->removeProductsNoMissingFile($query->rows);
	}

	$this->load->model('shopmanager/catalog/product_search');
	
	foreach ($query->rows as $key => $result) {
			$product_data[$key] = $result;

			$product_data[$key]['variant'] = $result['variant'] ? json_decode($result['variant'], true) : [];
			$product_data[$key]['override'] = $result['override'] ? json_decode($result['override'], true) : [];

			$product_data[$key]['marketplace_accounts_id'] = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $result['product_id']]);
			$nb_marketplace_accounts_id = count($product_data[$key]['marketplace_accounts_id']);
	}
	
	return $this->model_shopmanager_catalog_product->addSpecificsStatsToProducts($product_data);
}


	/**
	 * Get Total Products
	 *
	 * Get the total number of product records in the database.
	 *
	 * @param array<string, mixed> $data array of filters
	 *
	 * @return int total number of product records
	 *
	 * @example
	 *
	 * $filter_data = [
	 *     'filter_name'            => 'Product Name',
	 *     'filter_model'           => 'Product Model',
	 *     'filter_category_id'     => 0,
	 *     'filter_manufacturer_id' => 0,
	 *     'filter_price_from'      => '0.0000',
	 *     'filter_price_to'        => '100.0000',
	 *     'filter_quantity_from'   => 1,
	 *     'filter_quantity_to'     => 100,
	 *     'filter_status'          => 1,
	 *     'start'                  => 0,
	 *     'limit'                  => 10
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalProducts();
	 */
public function getTotalProducts(array $data = []): int {
	
	$sql = "SELECT COUNT(DISTINCT p.`product_id`) AS `total` 
	FROM `" . DB_PREFIX . "product` p 
	INNER JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id` AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "')";
	
	$sql .= " WHERE 1=1";

	if (!empty($data['filter_name'])) {
		$sql .= " AND LCASE(`pd`.`name`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_name']) . '%') . "'";
	}

	if (!empty($data['filter_model'])) {
		$sql .= " AND (LCASE(`p`.`model`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_model']) . '%') . "' OR LCASE(`pc`.`value`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_model']) . '%') . "')";
	}
	if (isset($data['filter_category_id']) && $data['filter_category_id'] !== '') {
		//		$sql .= " AND EXISTS(SELECT 1 FROM `" . DB_PREFIX . "product_to_category` ptc WHERE ptc.`product_id` = p.`product_id` AND ptc.`category_id` LIKE '" . $this->db->escape($data['filter_category_id']) . "%')";
		$sql .= " AND `p`.`product_id` IN (SELECT `p2c`.`product_id` FROM `" . DB_PREFIX . "product_to_category` `p2c` WHERE `p2c`.`category_id` = '" . (int)$data['filter_category_id'] . "')";
	}

	if (isset($data['filter_manufacturer_id']) && $data['filter_manufacturer_id'] !== '') {
		$sql .= " AND `p`.`manufacturer_id` = '" . (int)$data['filter_manufacturer_id'] . "'";
	}

	if (isset($data['filter_price_from']) && $data['filter_price_from'] !== '') {
		$sql .= " AND `p`.`price` >= '" . (float)$data['filter_price_from'] . "'";
	}

	if (isset($data['filter_price_to']) && $data['filter_price_to'] !== '') {
		$sql .= " AND `p`.`price` <= '" . (float)$data['filter_price_to'] . "'";
	}

	if (isset($data['filter_quantity_from']) && $data['filter_quantity_from'] !== '') {
		$sql .= " AND (p.`quantity` + p.`unallocated_quantity`) >= '" . (int)$data['filter_quantity_from'] . "'";
	}

	if (isset($data['filter_quantity_to']) && $data['filter_quantity_to'] !== '') {
		$sql .= " AND (p.`quantity` + p.`unallocated_quantity`) <= '" . (int)$data['filter_quantity_to'] . "'";
	}

	if (isset($data['filter_status']) && $data['filter_status'] !== '') {
		$sql .= " AND `p`.`status` = '" . (int)$data['filter_status'] . "'";
	}

	if (isset($data['filter_unallocated_quantity']) && !is_null($data['filter_unallocated_quantity'])) {
		$sql .= " AND p.`unallocated_quantity` = '" . (int)$data['filter_unallocated_quantity'] . "'";
	}

	if (isset($data['filter_location']) && !is_null($data['filter_location'])) {
		$sql .= " AND p.`location` = '" . (int)$data['filter_location'] . "'";
	}

	if (isset($data['filter_name_length'])) {
		if ($data['filter_name_length'] === '1') {
			$sql .= " AND LENGTH(CONVERT(pd.`name` USING ascii)) > 80";
		} elseif ($data['filter_name_length'] === '0') {
			$sql .= " AND (pd.`name` IS NULL OR pd.`name` = '')";
		}
	}
	
	if (isset($data['filter_invalid_price']) && $data['filter_invalid_price'] === '1') {
		$sql .= " AND p.`price` < 0";
	}

	if (!empty($data['filter_sku'])) {
		$sql .= " AND (p.`sku` LIKE '" . $this->db->escape($data['filter_sku']) . "' OR p.`upc` LIKE '" . $this->db->escape($data['filter_sku']) . "') ";
	}

	if (!empty($data['filter_product_id'])) {
		$sql .= " AND p.`product_id` = '" . $this->db->escape($data['filter_product_id']) . "'";
	}

	if (!empty($data['filter_marketplace_account_id'])) {
		$sql .= " AND p.`product_id` IN (
			SELECT pm.`product_id` FROM `" . DB_PREFIX . "product_marketplace` pm 
			WHERE pm.`marketplace_item_id` = '" . $this->db->escape($data['filter_marketplace_account_id']) . "'
		)";
	}

	if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
		if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
			$sql .= " AND (p.`image` IS NOT NULL AND p.`image` <> '' AND p.`image` <> 'no_image.png')";
		} else {
			$sql .= " AND (p.`image` IS NULL OR p.`image` = '' OR p.`image` = 'no_image.png')";
		}
	}

	// Filtre marketplace (même logique que getProducts)
	if (isset($data['filter_marketplace']) && $data['filter_marketplace'] !== '' && $data['filter_marketplace'] !== '*') {
		if ($data['filter_marketplace'] == '0') {
			// NOT LISTED: Produits NON listés sur marketplace ET avec quantity totale > 0
			$sql .= " AND NOT EXISTS(
				SELECT 1 FROM `" . DB_PREFIX . "product_marketplace` pm 
				WHERE pm.`product_id` = p.`product_id` 
				AND pm.`marketplace_item_id` IS NOT NULL 
				AND pm.`marketplace_item_id` != ''
			)";
			$sql .= " AND (p.`quantity` + p.`unallocated_quantity`) > 0";
		} elseif ($data['filter_marketplace'] == '1') {
			// LISTED: Produits listés sur au moins un marketplace
			$sql .= " AND EXISTS(
				SELECT 1 FROM `" . DB_PREFIX . "product_marketplace` pm 
				WHERE pm.`product_id` = p.`product_id` 
				AND pm.`marketplace_item_id` IS NOT NULL 
				AND pm.`marketplace_item_id` != ''
			)";
		} elseif ($data['filter_marketplace'] == '2') {
			// ERROR LISTING: Produits avec erreur de listing
			$sql .= " AND EXISTS(
				SELECT 1 FROM `" . DB_PREFIX . "product_marketplace` pm 
				WHERE pm.`product_id` = p.`product_id` 
				AND (pm.`error` IS NOT NULL AND pm.`error` != '')
			)";
		}
	}

	if (isset($data['filter_specifics']) && $data['filter_specifics'] !== '*') {
		if ($data['filter_specifics'] == '0') {
			$sql .= " AND (pd.`specifics` IS NULL OR pd.`specifics` = '' OR pd.`specifics` = 'null')";
		} elseif ($data['filter_specifics'] == '1') {
			$sql .= " AND NOT (pd.`specifics` IS NULL OR pd.`specifics` IN ('', 'null', '[]')) 
					  AND JSON_VALID(pd.`specifics`) = 1";
		} elseif ($data['filter_specifics'] == '2') {
			$sql .= " AND pd.`specifics` IN ('null', '[]') 
					  AND (JSON_VALID(pd.`specifics`) = 0 OR pd.`specifics` = '[]')";
		}
	}

	// MODIFIÉ : filtre sources avec EXISTS
	if (isset($data['filter_sources']) && $data['filter_sources'] !== '*') {
		switch ($data['filter_sources']) {
			case '0':
				$sql .= " AND NOT EXISTS(SELECT 1 FROM `" . DB_PREFIX . "product_info_sources` WHERE `upc` = p.`upc`)";
				break;
			case '1':
				$sql .= " AND EXISTS(SELECT 1 FROM `" . DB_PREFIX . "product_info_sources` WHERE `upc` = p.`upc`)";
				break;
			case '2':
				$sql .= " AND EXISTS(SELECT 1 FROM `" . DB_PREFIX . "product_info_sources` WHERE `upc` = p.`upc` AND `upc` IN ('null', '[]'))";
				break;
		}
	}

	$query = $this->db->query($sql);

	if (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2){
		$total=$query->row['total']-$this->model_shopmanager_catalog_product->removeTotalProductsNoMissingFile($data);
	}else{
		$total=$query->row['total'];
	}
	
	return $total;
}

	/**
	 * Get Total Products By Manufacturer ID
	 *
	 * Get the total number of products by manufacturer records in the database.
	 *
	 * @param int $manufacturer_id primary key of the manufacturer record
	 *
	 * @return int total number of product records that have manufacturer ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalProductsByManufacturerId($manufacturer_id);
	 */
	public function getTotalProductsByManufacturerId(int $manufacturer_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product` WHERE `manufacturer_id` = '" . (int)$manufacturer_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Products By Tax Class ID
	 *
	 * Get the total number of products by tax class records in the database.
	 *
	 * @param int $tax_class_id primary key of the tax class record
	 *
	 * @return int total number of product records that have tax class ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalProductsByTaxClassId($tax_class_id);
	 */
	public function getTotalProductsByTaxClassId(int $tax_class_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product` WHERE `tax_class_id` = '" . (int)$tax_class_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Products By Stock Status ID
	 *
	 * Get the total number of products by stock status records in the database.
	 *
	 * @param int $stock_status_id primary key of the stock status record
	 *
	 * @return int total number of product records that have stock status ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalProductsByStockStatusId($stock_status_id);
	 */
	public function getTotalProductsByStockStatusId(int $stock_status_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product` WHERE `stock_status_id` = '" . (int)$stock_status_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Products By Weight Class ID
	 *
	 * Get the total number of products by weight class records in the database.
	 *
	 * @param int $weight_class_id primary key of the weight class record
	 *
	 * @return int total number of product records that have weight class ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalProductsByWeightClassId($weight_class_id);
	 */
	public function getTotalProductsByWeightClassId(int $weight_class_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product` WHERE `weight_class_id` = '" . (int)$weight_class_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Get Total Products By Length Class ID
	 *
	 * Get the total number of products by length class records in the database.
	 *
	 * @param int $length_class_id primary key of the length class record
	 *
	 * @return int total number of product records that have length class ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalProductsByLengthClassId($length_class_id);
	 */
	public function getTotalProductsByLengthClassId(int $length_class_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product` WHERE `length_class_id` = '" . (int)$length_class_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Add Description
	 *
	 * Create a new product description record in the database.
	 *
	 * @param int                  $product_id  primary key of the product record
	 * @param int                  $language_id primary key of the language record
	 * @param array<string, mixed> $data        array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $product_data['product_description'] = [
	 *     'name'             => 'Product Name',
	 *     'description'      => 'Product Description',
	 *     'tag'              => 'Product Tag',
	 *     'meta_title'       => 'Meta Title',
	 *     'meta_description' => 'Meta Description',
	 *     'meta_keyword'     => 'Meta Keyword'
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addDescription($product_id, $language_id, $product_data);
	 */
	public function addDescription(int $product_id, int $language_id, array $data): void {
		// $data contient le tableau complet du produit (pas seulement la description de cette langue)
		// On récupère les données de description pour cette langue
		$desc = isset($data['product_description'][$language_id]) ? $data['product_description'][$language_id] : $data;


		//$specifics_value = (!empty($data['specifics'])) ? " '".$this->db->escape(json_encode($data['specifics'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))."' " : ' NULL';
		
		$specifics_slq='';
		
		$specifics_src = $desc['specifics'] ?? $data['specifics'] ?? null;
		if (isset($specifics_src) && $specifics_src!='null'){
			$specifics=array();
			foreach ($specifics_src as $key=>$product_specific ){
				unset($product_specific['specifics_id']);
				$specifics[$key]=$product_specific;
			}
			$specifics_slq= ", specifics = '".$this->db->escape(json_encode($specifics, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))."'";
		//print("<pre>".print_r ($specifics,true )."</pre>");
		}else{
			$specifics_slq= ", specifics = NULL ";
		}
		//$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` SET `product_id` = '" . (int)$product_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($data['name']) . "', `description` = '" . $this->db->escape($data['description']) . "', `tag` = '" . $this->db->escape($data['tag']) . "', `meta_title` = '" . $this->db->escape($data['meta_title']) . "', `meta_description` = '" . $this->db->escape($data['meta_description']) . "', `meta_keyword` = '" . $this->db->escape($data['meta_keyword']) . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_description` 
		SET  `product_id` = '" . (int)$product_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($desc['name'] ?? '') . "', 
		`description` = '', `tag` = '" . $this->db->escape($desc['tag'] ?? '') . "',
		`meta_title` = '" . $this->db->escape($desc['meta_title'] ?? '') . "', `meta_description` = '" . $this->db->escape($desc['meta_description'] ?? '') . "', 
		`meta_keyword` = '" . $this->db->escape($desc['meta_keyword'] ?? '') . "' " .$specifics_slq . " , 
		`color` = '" . $this->db->escape($desc['color'] ?? '') . "', 
		`description_supp` = '" . $this->db->escape($desc['description_supp'] ?? '') . "',
		`condition_supp` = '" . $this->db->escape($desc['condition_supp'] ?? '') . "' , 
		`included_accessories` = '" . $this->db->escape($desc['included_accessories'] ?? '') . "'");
	
	// SEO URL - Sauvegarder dans oc_seo_url (OpenCart 4)
	if (isset($desc['keyword']) && $desc['keyword']) {
		$seo_keyword = str_replace('.', '', $desc['keyword']);
		
		// Supprimer l'ancienne URL pour cette langue/produit
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `key` = 'product_id' AND `value` = '" . (int)$product_id . "' AND language_id = '" . (int)$language_id . "'");
		
		// Insérer la nouvelle URL avec les colonnes key/value
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "seo_url 
			SET store_id = 0,
			    language_id = '" . (int)$language_id . "',
			    `key` = 'product_id',
			    `value` = '" . (int)$product_id . "',
			    keyword = '" . $this->db->escape($seo_keyword) . "',
			    query = 'product_id=" . (int)$product_id . "',
			    sort_order = 0
		");
	}

}

	/**
	 * Delete Descriptions
	 *
	 * Delete product description records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteDescriptions($product_id);
	 */
	public function deleteDescriptions(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Descriptions By Language ID
	 *
	 * Delete product descriptions by language records in the database.
	 *
	 * @param int $language_id primary key of the language record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteDescriptionsByLanguageId($language_id);
	 */
	public function deleteDescriptionsByLanguageId(int $language_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_description` WHERE `language_id` = '" . (int)$language_id . "'");
	}

	/**
	 * Get Descriptions
	 *
	 * Get the record of the product description records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, array<string, mixed>> description records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_description = $this->model_shopmanager_catalog_product->getDescriptions($product_id);
	 */
	public function getDescriptions($product_id) {

		$this->load->model('shopmanager/tools');

		$product_description_data = array();

		

		// Auto-générer description/meta si vide pour au moins une langue
		
				$this->refreshDescriptions((int)$product_id);
				// Re-charger après regénération
				$query = $this->db->query("
					SELECT pd.*, ua.keyword 
					FROM " . DB_PREFIX . "product_description pd 
					LEFT JOIN " . DB_PREFIX . "seo_url ua 
					ON (ua.`key` = 'product_id' AND ua.`value` = '" . (int)$product_id . "' AND ua.language_id = pd.language_id) 
					WHERE pd.product_id = '" . (int)$product_id . "'");
				$rows = $query->rows;


		$condition_name = $this->getCondition($product_id);
		
		foreach ($rows as $result) {
	
			// Les champs Summernote gèrent le HTML directement - pas de conversion
			$condition_supp = $result['condition_supp'];
			$included_accessories = $result['included_accessories'];
			$description_supp = $result['description_supp'];

			$product_description_data[$result['language_id']] = array(
				'name'             	=> $result['name'],
				'description'      	=> $result['description'],
				'color'      		=> $result['color'],
				'description_supp'  => $description_supp,
				'condition_supp'   	=> $condition_supp,
				'included_accessories'   	=> $included_accessories,
				'meta_title'       	=> $result['meta_title'],
				'meta_description' 	=> $result['meta_description'],
				'meta_keyword'     	=> $result['meta_keyword'],
				'keyword'     		=> $result['keyword'],
				'tag'              	=> $result['tag'],
				//'specifics'         => json_decode($result['specifics'],true),
			);
		
		}
		$product_description_data=$this->model_shopmanager_tools->custom_merge_recursive($product_description_data, $condition_name);

		$specifics=$this->getSpecifics($product_id);

		$product_description_data=$this->model_shopmanager_tools->custom_merge_recursive($product_description_data, $specifics);
		
		return $product_description_data;
	}

	/**
	 * Refresh Descriptions
	 *
	 * Génère ou régénère description HTML et meta tags pour toutes les langues
	 * d'un produit, en mettant à jour directement oc_product_description.
	 * Appelle generateDescription() et generateMetaTag() par langue.
	 *
	 * @param int $product_id
	 * @return void
	 */
	public function refreshDescriptions(int $product_id): void {
		// Guard contre la récursion infinie :
		// getDescriptions() → refreshDescriptions() → generateDescription/generateMetaTag() → getDescriptions() → ...
		static $in_progress = [];
		if (isset($in_progress[$product_id])) return;
		$in_progress[$product_id] = true;

		$this->generateDescription($product_id);
		$this->generateMetaTag($product_id);

		unset($in_progress[$product_id]);
	}

	public function getDescriptionsNOT_USED2($product_id) {
		$this->load->model('shopmanager/tools');
		$this->load->model('shopmanager/catalog/category');
		
		$product_id = (int)$product_id;
		$product_description_data = array();
		
		// **UNE SEULE REQUÊTE** optimisée - prend la catégorie leaf
		$query = $this->db->query("
			SELECT 
				pd.*, 
				ua.keyword,
				c.name as condition_name,
				c.condition_id,
				(SELECT pc.category_id 
				FROM " . DB_PREFIX . "product_to_category pc 
				INNER JOIN " . DB_PREFIX . "category cat 
					ON (pc.category_id = cat.category_id AND cat.leaf = 1)
				WHERE pc.product_id = '" . $product_id . "' 
				LIMIT 1) as category_id
			FROM " . DB_PREFIX . "product_description pd
			LEFT JOIN " . DB_PREFIX . "seo_url ua 
				ON (ua.`key` = 'product_id' AND ua.`value` = '" . (int)$product_id . "' AND ua.language_id = pd.language_id)
			LEFT JOIN " . DB_PREFIX . "product p 
				ON (p.product_id = pd.product_id)
			LEFT JOIN " . DB_PREFIX . "condition c 
				ON (p.condition_id = c.condition_id AND c.language_id = pd.language_id)
			WHERE pd.product_id = '" . $product_id . "'
		");
		
		if (!$query->num_rows) {
			return array();
		}
		
		$rows = $query->rows;
		$category_id = !empty($rows[0]['category_id']) ? (int)$rows[0]['category_id'] : 0;
		
		// Récupérer les category specifics UNE SEULE FOIS
		$category_specifics = array();
		if ($category_id > 0) {
			$category_specifics = $this->model_shopmanager_catalog_category->getSpecifics($category_id);
		}
		
		// Construire le tableau indexé par language_id
		foreach ($rows as $result) {
			$language_id = (int)$result['language_id'];
			
			// Convertir les champs avec virgules en listes HTML si pas de balises HTML
			$condition_supp = $result['condition_supp'];
			if (!empty($condition_supp) && strpos($condition_supp, ',') !== false && strip_tags($condition_supp) === $condition_supp) {
				$items = array_map('trim', explode(',', $condition_supp));
				$condition_supp = '<ul><li>' . implode('</li><li>', $items) . '</li></ul>';
			}
			
			// Les champs Summernote gèrent le HTML directement - pas de conversion
			$condition_supp = $result['condition_supp'];
			$included_accessories = $result['included_accessories'];
			$description_supp = $result['description_supp'];
			
			// Structure de base comme OpenCart 4
			$product_description_data[$language_id] = array(
				'name'                    => $result['name'],
				'description'             => $result['description'],
				'color'                   => $result['color'],
				'description_supp'        => $description_supp,
				'condition_supp'          => $condition_supp,
				'included_accessories'    => $included_accessories,
				'meta_title'              => $result['meta_title'],
				'meta_description'        => $result['meta_description'],
				'meta_keyword'            => $result['meta_keyword'],
				'keyword'                 => $result['keyword'],
				'tag'                     => $result['tag'],
				'condition'               => $result['condition_name'],
				'condition_id'            => (int)$result['condition_id']
			);
			
			// Initialiser les specifics avec les valeurs de la catégorie
			$product_description_data[$language_id]['specifics'] = array();
			
			if (isset($category_specifics[$language_id]['specifics'])) {
				foreach ($category_specifics[$language_id]['specifics'] as $key => $category_specific) {
					$product_description_data[$language_id]['specifics'][$key] = array(
						'Name'           => $category_specific['localizedAspectName'],
						'Value'          => '',
						'VerifiedSource' => '',
						'specific_info'  => $category_specific
					);
				}
			}
			
			// Fusionner avec les specifics du produit
			$product_specifics = json_decode(
				trim(html_entity_decode($result['specifics'] ?? '', ENT_QUOTES | ENT_HTML5)),
				true
			);
			
			if (!empty($product_specifics) && is_array($product_specifics)) {
				foreach ($product_specifics as $key => $specific) {
					if (isset($product_description_data[$language_id]['specifics'][$key])) {
						// Mettre à jour les valeurs existantes
						$product_description_data[$language_id]['specifics'][$key]['Value'] = 
							isset($specific['Value']) ? $specific['Value'] : '';
						$product_description_data[$language_id]['specifics'][$key]['VerifiedSource'] = 
							isset($specific['VerifiedSource']) ? $specific['VerifiedSource'] : '';
					}
				}
			}
		}
		
		return $product_description_data;
	}


	public function getDescriptions_NOTUSED(int $product_id): array {
		$product_description_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE `product_id` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = $result;
		}

		return $product_description_data;
	}

	/**
	 * Get Descriptions By Language ID
	 *
	 * Get the record of the product descriptions by language record in the database.
	 *
	 * @param int $language_id primary key of the language record
	 *
	 * @return array<int, array<string, string>> description records that have language ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $results = $this->model_shopmanager_catalog_product->getDescriptionsByLanguageId($language_id);
	 */
	public function getDescriptionsByLanguageId(int $language_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_description` WHERE `language_id` = '" . (int)$language_id . "'");

		return $query->rows;
	}

	/**
	 * Add Code
	 *
	 * Create a new product code record in the database.
	 *
	 * @param int                  $product_id primary key of the product record
	 * @param array<string, mixed> $data       array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $data['product_code'] = [
	 *     'product_id' => 1,
	 *     'code'       => 'Product Code',
	 *     'value'      => 'Product Value'
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $results = $this->model_shopmanager_catalog_product->addCode($product_id, $data);
	 */
	public function addCode(int $product_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_code` SET `product_id` = '" . (int)$product_id . "', `code` = '" . $this->db->escape($data['code']) . "', `value` = '" . $this->db->escape($data['value']) . "'");
	}

	/**
	 * Delete Codes
	 *
	 * Delete product code records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $results = $this->model_shopmanager_catalog_product->deleteCodes($product_id);
	 */
	public function deleteCodes(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_code` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Get Codes
	 *
	 * Get the record of the product code records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, array<string, mixed>>
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $results = $this->model_shopmanager_catalog_product->getCodes($product_id);
	 */
	public function getCodes(int $product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_code` WHERE `product_id` = '" . (int)$product_id . "'");

		return $query->rows;
	}

	/**
	 * Add Category
	 *
	 * Create a new product category record in the database.
	 *
	 * @param int $product_id  primary key of the product record
	 * @param int $category_id primary key of the category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addCategory($product_id, $category_id);
	 */
	public function addCategory(int $product_id, int $category_id): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_category` SET `product_id` = '" . (int)$product_id . "', `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Delete Categories
	 *
	 * Delete product category records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteCategories($product_id);
	 */
	public function deleteCategories(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Categories By Category ID
	 *
	 * Delete categories by category record in the database.
	 *
	 * @param int $category_id primary key of the category record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteCategoriesByCategoryId($category_id);
	 */
	public function deleteCategoriesByCategoryId(int $category_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_category` WHERE `category_id` = '" . (int)$category_id . "'");
	}

	/**
	 * Get Categories
	 *
	 * Get the record of the product category records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, int> category records that have category ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $categories = $this->model_shopmanager_catalog_product->getCategories($product_id);
	 */
	public function getCategories(int $product_id): array {
		$product_category_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_category` WHERE `product_id` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getCategoryLeaf(int $product_id): int {
		$query = $this->db->query("SELECT C.category_id FROM `" . DB_PREFIX . "product_to_category` AS `PTC`
							   INNER JOIN `" . DB_PREFIX . "category` AS C ON (`PTC`.category_id=`C`.category_id AND `C`.leaf=1) 
							   WHERE `PTC`.`product_id` = '" . (int)$product_id . "' 
							   LIMIT 1");

		return !empty($query->row['category_id']) ? (int)$query->row['category_id'] : 0;
	}

	public function deleteLeafCategories(int $product_id): void {
		$this->db->query("DELETE pc FROM `" . DB_PREFIX . "product_to_category` pc
			INNER JOIN `" . DB_PREFIX . "category` c ON pc.category_id = c.category_id
			WHERE pc.product_id = '" . (int)$product_id . "'
			AND c.leaf = 1");
	}

	public function addCategoryIfNotExists(int $product_id, int $category_id): void {
		$existing = $this->db->query("SELECT 1 FROM `" . DB_PREFIX . "product_to_category`
			WHERE product_id = '" . (int)$product_id . "'
			AND category_id = '" . (int)$category_id . "'");
		if (!$existing->num_rows) {
			$this->addCategory($product_id, $category_id);
		}
	}

	public function setProductLeafCategory(int $product_id, int $new_category_id): void {
		// Preserve all non-leaf categories (excluding new category to avoid duplicate)
		$non_leaf = $this->db->query("
			SELECT pc.category_id
			FROM `" . DB_PREFIX . "product_to_category` pc
			LEFT JOIN `" . DB_PREFIX . "category` c ON pc.category_id = c.category_id
			WHERE pc.product_id = '" . (int)$product_id . "'
			AND (c.leaf = 0 OR c.leaf IS NULL)
			AND pc.category_id != '" . (int)$new_category_id . "'
		");
		$keep = array_column($non_leaf->rows, 'category_id');

		// Delete all, then re-add non-leaf + new leaf (same pattern as editProduct)
		$this->deleteCategories($product_id);
		foreach ($keep as $cat_id) {
			$this->addCategory($product_id, (int)$cat_id);
		}
		$this->addCategory($product_id, $new_category_id);
	}

	/**
	 * Add Filter
	 *
	 * Create a new product filter record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 * @param int $filter_id  primary key of the filter record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addFilter($product_id, $filter_id);
	 */
	public function addFilter(int $product_id, int $filter_id): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_filter` SET `product_id` = '" . (int)$product_id . "', `filter_id` = '" . (int)$filter_id . "'");
	}

	/**
	 * Delete Filters
	 *
	 * Delete product filter records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteFilters($product_id);
	 */
	public function deleteFilters(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_filter` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Filters By Filter ID
	 *
	 * Delete product filters by filter records in the database.
	 *
	 * @param int $filter_id primary key of the filter record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteFiltersByFilterId($filter_id);
	 */
	public function deleteFiltersByFilterId(int $filter_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_filter` WHERE `filter_id` = '" . (int)$filter_id . "'");
	}

	/**
	 * Get Filters
	 *
	 * Get the record of the product filter records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, int> filter records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $filters = $this->model_shopmanager_catalog_product->getFilters($product_id);
	 */
	public function getFilters(int $product_id): array {
		$product_filter_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_filter` WHERE `product_id` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	/**
	 * Add Attribute
	 *
	 * Create a new product attribute record in the database.
	 *
	 * @param int                  $product_id   primary key of the product record
	 * @param int                  $attribute_id primary key of the attribute record
	 * @param int                  $language_id  primary key of the language record
	 * @param array<string, mixed> $data         array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $product_data['product_attribute'] = [
	 *     'text' => 'Product Attribute Text'
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addAttribute($product_id, $attribute_id, $language_id, $product_data);
	 */
	public function addAttribute(int $product_id, int $attribute_id, int $language_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_attribute` SET `product_id` = '" . (int)$product_id . "', `attribute_id` = '" . (int)$attribute_id . "', `language_id` = '" . (int)$language_id . "', `text` = '" . $this->db->escape($data['text']) . "'");
	}

	/**
	 * Delete Attributes
	 *
	 * Delete product attribute records in the database.
	 *
	 * @param int $product_id   primary key of the product record
	 * @param int $attribute_id primary key of the attribute record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteAttributes($product_id, $attribute_id);
	 */
 public function deleteAttributes(int $product_id, int $attribute_id = 0): void {
		$sql = "DELETE FROM `" . DB_PREFIX . "product_attribute` WHERE `product_id` = '" . (int)$product_id . "'";

		if ($attribute_id) {
			$sql .= " AND `attribute_id` = '" . (int)$attribute_id . "'";
		}

		$this->db->query($sql);
	}

	/**
	 * Delete Attributes By Language ID
	 *
	 * Delete product attributes by language records in the database.
	 *
	 * @param int $language_id primary key of the language record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteAttributesByLanguageId($language_id);
	 */
	public function deleteAttributesByLanguageId(int $language_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_attribute` WHERE `language_id` = '" . (int)$language_id . "'");
	}

	/**
	 * Get Attributes
	 *
	 * Get the product attribute records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, array<string, mixed>> attribute records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_attributes = $this->model_shopmanager_catalog_product->getAttributes($product_id);
	 */
	public function getAttributes(int $product_id): array {
		$product_attribute_data = [];

		$product_attribute_query = $this->db->query("SELECT `pa`.`attribute_id` FROM `" . DB_PREFIX . "product_attribute` `pa` LEFT JOIN `" . DB_PREFIX . "attribute` a ON (`a`.`attribute_id` = `pa`.`attribute_id`) LEFT JOIN `" . DB_PREFIX . "attribute_group` `ag` ON (`ag`.`attribute_group_id` = `a`.`attribute_group_id`) WHERE `pa`.`product_id` = '" . (int)$product_id . "' GROUP BY `pa`.`attribute_id` ORDER BY `ag`.`sort_order` ASC, `a`.`sort_order` ASC");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = [];

			$product_attribute_description_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_attribute` WHERE `product_id` = '" . (int)$product_id . "' AND `attribute_id` = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = $product_attribute_description;
			}

			$product_attribute_data[] = ['product_attribute_description' => $product_attribute_description_data] + $product_attribute;
		}

		return $product_attribute_data;
	}

	/**
	 * Get Attributes By Language ID
	 *
	 * Get the product attributes by language records in the database.
	 *
	 * @param int $language_id primary key of the language record
	 *
	 * @return array<int, array<string, mixed>> attribute records that have language ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $results = $this->model_shopmanager_catalog_product->getAttributesByLanguageId($language_id);
	 */
	public function getAttributesByLanguageId(int $language_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_attribute` WHERE `language_id` = '" . (int)$language_id . "'");

		return $query->rows;
	}

	/**
	 * Get Total Attributes By Attribute ID
	 *
	 * Get the total number of product attributes by attribute records in the database.
	 *
	 * @param int $attribute_id primary key of the attribute record
	 *
	 * @return int total number of attribute records that have attribute ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalAttributesByAttributeId($attribute_id);
	 */
	public function getTotalAttributesByAttributeId(int $attribute_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product_attribute` WHERE `attribute_id` = '" . (int)$attribute_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Add Option
	 *
	 * Create a new product option record in the database.
	 *
	 * @param int                  $product_id primary key of the product record
	 * @param array<string, mixed> $data       array of data
	 *
	 * @return int returns the primary key of the new product option record
	 *
	 * @example
	 *
	 * $product_data['product_option'] = [
	 *     'product_option_id' => 1,
	 *     'product_id'        => 1,
	 *     'option_id'         => 1,
	 *     'value'             => 'Product Option Value',
	 *     'required'          => 0
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addOption($product_id, $product_data);
	 */
	public function addOption(int $product_id, array $data): int {
		if ($data['product_option_id']) {
			$sql = "INSERT INTO `" . DB_PREFIX . "product_option` SET `product_option_id` = '" . (int)$data['product_option_id'] . "', `product_id` = '" . (int)$product_id . "'";
		} else {
			$sql = "INSERT INTO `" . DB_PREFIX . "product_option` SET `product_id` = '" . (int)$product_id . "'";
		}

		$sql .= ", `option_id` = '" . (int)$data['option_id'] . "'";

		if (!isset($data['product_option_value'])) {
			$sql .= ", `value` = '" . $this->db->escape($data['value']) . "'";
		}

		$sql .= ", `required` = '" . (int)$data['required'] . "'";

		$this->db->query($sql);

		$product_option_id = $this->db->getLastId();

		if (isset($data['product_option_value'])) {
			foreach ($data['product_option_value'] as $product_option_value) {
				$this->model_shopmanager_catalog_product->addOptionValue($product_id, $product_option_id, $data['option_id'], $product_option_value);
			}
		}

		return $product_option_id;
	}

	/**
	 * Delete Options
	 *
	 * Delete product option description records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteOptions($product_id);
	 */
	public function deleteOptions(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option` WHERE `product_id` = '" . (int)$product_id . "'");

		$this->model_shopmanager_catalog_product->deleteOptionValues($product_id);
	}

	/**
	 * Get Option
	 *
	 * Get the record of the product option record in the database.
	 *
	 * @param int $product_id        primary key of the product record
	 * @param int $product_option_id primary key of the product option record
	 *
	 * @return array<string, mixed> option record that has product ID, product option ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_option_info = $this->model_shopmanager_catalog_product->getOption($product_id, $product_option_id);
	 */
	public function getOption(int $product_id, int $product_option_id): array {
		$query = $this->db->query("SELECT *, (SELECT `name` FROM `" . DB_PREFIX . "option_description` `od` WHERE `o`.`option_id` = `od`.`option_id` AND `od`.`language_id` = '" . (int)$this->config->get('config_language_id') . "') AS `name` FROM `" . DB_PREFIX . "product_option` `po` LEFT JOIN `" . DB_PREFIX . "option` `o` ON (`po`.`option_id` = `o`.`option_id`) WHERE `po`.`product_id` = '" . (int)$product_id . "' AND `po`.`product_option_id` = '" . (int)$product_option_id . "'");

		return $query->row;
	}

	/**
	 * Get Options
	 *
	 * Get the record of the product option records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, array<string, mixed>> option records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_options = $this->model_shopmanager_catalog_product->getOptions($product_id);
	 */
	public function getOptions(int $product_id): array {
		$product_option_data = [];

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` `po` LEFT JOIN `" . DB_PREFIX . "option` `o` ON (`po`.`option_id` = `o`.`option_id`) LEFT JOIN `" . DB_PREFIX . "option_description` `od` ON (`o`.`option_id` = `od`.`option_id`) WHERE `po`.`product_id` = '" . (int)$product_id . "' AND `od`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `o`.`sort_order` ASC");

		foreach ($product_option_query->rows as $product_option) {
			$value = $product_option['value'];

			if ($product_option['type'] == 'date' && $value) {
				$value = date('Y-m-d', strtotime($value));
			}

			if ($product_option['type'] == 'time' && $value) {
				$value = date('H:i:s', strtotime($value));
			}

			if ($product_option['type'] == 'datetime' && $value) {
				$value = date('Y-m-d H:i:s', strtotime($value));
			}

			$product_option_value_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option_value` `pov` LEFT JOIN `" . DB_PREFIX . "option_value` `ov` ON (`pov`.`option_value_id` = `ov`.`option_value_id`) WHERE `pov`.`product_option_id` = '" . (int)$product_option['product_option_id'] . "' ORDER BY `ov`.`sort_order` ASC");

			$product_option_data[] = [
				'product_option_value' => $product_option_value_query->rows,
				'value'                => $value
			] + $product_option;
		}

		return $product_option_data;
	}

	/**
	 * Get Total Options By Option ID
	 *
	 * Get the total number of product options by option records in the database.
	 *
	 * @param int $option_id primary key of the option record
	 *
	 * @return int total number of option records that have option ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalOptionsByOptionId($option_id);
	 */
	public function getTotalOptionsByOptionId(int $option_id): int {
		$query = $this->db->query("SELECT COUNT(DISTINCT `product_id`) AS `total` FROM `" . DB_PREFIX . "product_option` WHERE `option_id` = '" . (int)$option_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Add Option Value
	 *
	 * Create a new product option value record in the database.
	 *
	 * @param int                  $product_id        primary key of the product record
	 * @param int                  $product_option_id primary key of the product option record
	 * @param int                  $option_id         primary key of the option record
	 * @param array<string, mixed> $data              array of data
	 *
	 * @return int
	 *
	 * @example
	 *
	 * $product_data['product_option_value'] = [
	 *     'product_option_value_id' => 1,
	 *     'product_option_id'       => 1,
	 *     'product_id'              => 1,
	 *     'option_id'               => 1,
	 *     'option_value_id'         => 1,
	 *     'quantity'                => 1,
	 *     'subtract'                => 0,
	 *     'price'                   => '0.0000',
	 *     'price_prefix'            => '',
	 *     'points'                  => '0',
	 *     'points_prefix'           => '',
	 *     'weight'                  => '0.0000',
	 *     'weight_prefix'           => ''
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addOptionValue($product_id, $product_option_id, $option_id, $product_option_value);
	 */
	public function addOptionValue(int $product_id, int $product_option_id, int $option_id, array $data): int {
		$sql = "INSERT INTO `" . DB_PREFIX . "product_option_value` SET ";

		if (isset($data['product_option_value_id'])) {
			$sql .= "`product_option_value_id` = '" . (int)$data['product_option_value_id'] . "', ";
		}

		$sql .= "`product_option_id` = '" . (int)$product_option_id . "', `product_id` = '" . (int)$product_id . "', `option_id` = '" . (int)$option_id . "', `option_value_id` = '" . (int)$data['option_value_id'] . "', `quantity` = '" . (int)$data['quantity'] . "', `subtract` = '" . (int)$data['subtract'] . "', `price` = '" . (float)$data['price'] . "', `price_prefix` = '" . $this->db->escape($data['price_prefix']) . "', `points` = '" . (int)$data['points'] . "', `points_prefix` = '" . $this->db->escape($data['points_prefix']) . "', `weight` = '" . (float)$data['weight'] . "', `weight_prefix` = '" . $this->db->escape($data['weight_prefix']) . "'";

		$this->db->query($sql);

		return $this->db->getLastId();
	}

	/**
	 * Delete Option Values
	 *
	 * Delete product option value records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteOptionValues($product_id);
	 */
	public function deleteOptionValues(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_option_value` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Get Option Value
	 *
	 * Get the record of the product option value record in the database.
	 *
	 * @param int $product_id              primary key of the product record
	 * @param int $product_option_value_id primary key of the product option value record
	 *
	 * @return array<string, mixed> option value record that has product ID, product option value ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_option_value_info = $this->model_shopmanager_catalog_product->getOptionValue($product_id, $product_option_value_id);
	 */
	public function getOptionValue(int $product_id, int $product_option_value_id): array {
		$query = $this->db->query("SELECT `pov`.`option_value_id`, `ovd`.`name`, `pov`.`quantity`, `pov`.`subtract`, `pov`.`price`, `pov`.`price_prefix`, `pov`.`points`, `pov`.`points_prefix`, `pov`.`weight`, `pov`.`weight_prefix` FROM `" . DB_PREFIX . "product_option_value` `pov` LEFT JOIN `" . DB_PREFIX . "option_value` `ov` ON (`pov`.`option_value_id` = `ov`.`option_value_id`) LEFT JOIN `" . DB_PREFIX . "option_value_description` `ovd` ON (`ov`.`option_value_id` = `ovd`.`option_value_id`) WHERE `pov`.`product_id` = '" . (int)$product_id . "' AND `pov`.`product_option_value_id` = '" . (int)$product_option_value_id . "' AND `ovd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	/**
	 * Get Option Values By Option ID
	 *
	 * Get the record of the product option values by option record in the database.
	 *
	 * @param int $option_id primary key of the option record
	 *
	 * @return array<int, array<string, mixed>> option value records that have option ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_option_values = $this->model_shopmanager_catalog_product->getOptionValuesByOptionId($option_id);
	 */
	public function getOptionValuesByOptionId(int $option_id): array {
		$query = $this->db->query("SELECT DISTINCT `option_value_id` FROM `" . DB_PREFIX . "product_option_value` WHERE `option_id` = '" . (int)$option_id . "'");

		return $query->rows;
	}

	/**
	 * Get Total Option Values By Option Value ID
	 *
	 * Get the total number of product option values by option value records in the database.
	 *
	 * @param int $option_value_id primary key of the option value record
	 *
	 * @return int total number of option value records that have option value ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $option_value = $this->model_shopmanager_catalog_product->getTotalOptionValuesByOptionValueId($option_value_id);
	 */
	public function getTotalOptionValuesByOptionValueId(int $option_value_id): int {
		$query = $this->db->query("SELECT COUNT(DISTINCT `product_id`) AS `total` FROM `" . DB_PREFIX . "product_option_value` WHERE `option_value_id` = '" . (int)$option_value_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Add Image
	 *
	 * Create a new product image record in the database.
	 *
	 * @param int                  $product_id primary key of the product record
	 * @param array<string, mixed> $data       array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $product_data['product_image'] = [
	 *     'image'      => 'product_image',
	 *     'sort_order' => 0
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addImage($product_id, $product_data);
	 */
	public function addImage(int $product_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_image` SET `product_id` = '" . (int)$product_id . "', `image` = '" . $this->db->escape($data['image']) . "', `sort_order` = '" . (int)$data['sort_order'] . "'");
	}

	/**
	 * Delete Images
	 *
	 * Delete product image records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteImages($product_id);
	 */
	public function deleteImages(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	public function deleteProductImageById(int $product_image_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_image` WHERE `product_image_id` = '" . (int)$product_image_id . "'");
	}

	/**
	 * Get Images
	 *
	 * Get the record of the product image records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, array<string, mixed>> image records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_images = $this->model_shopmanager_catalog_product->getImages($product_id);
	 */
	public function getImages(int $product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_image` WHERE `product_id` = '" . (int)$product_id . "' ORDER BY `sort_order` ASC");

		return $query->rows;
	}

	/**
	 * Add Discount
	 *
	 * Create a new discount record in the database.
	 *
	 * @param int                  $product_id primary key of the product record
	 * @param array<string, mixed> $data       array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $product_data['product_discount'] = [
	 *     'customer_group_id' => 1,
	 *     'quantity'          => 1,
	 *     'priority'          => 0,
	 *     'price'             => '0.0000',
	 *     'type'              => 'Product Discount Type',
	 *     'special'           => 0,
	 *     'date_start'        => '2021-01-01',
	 *     'date_end'          => '2021-01-31'
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addDiscount($product_id, $product_data);
	 */
	public function addDiscount(int $product_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_discount` SET `product_id` = '" . (int)$product_id . "', `customer_group_id` = '" . (int)$data['customer_group_id'] . "', `quantity` = '" . (int)$data['quantity'] . "', `priority` = '" . (int)$data['priority'] . "', `price` = '" . (float)$data['price'] . "', `type` = '" . $this->db->escape($data['type']) . "', `special` = '" . (bool)$data['special'] . "', `date_start` = '" . $this->db->escape($data['date_start']) . "', `date_end` = '" . $this->db->escape($data['date_end']) . "'");
	}

	/**
	 * Delete Discounts
	 *
	 * Delete discount records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteDiscounts($product_id);
	 */
	public function deleteDiscounts(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Discounts By Customer ID
	 *
	 * Delete discounts by customer group records in the database.
	 *
	 * @param int $customer_group_id primary key of the customer group record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteDiscountsByCustomerGroupId($customer_group_id);
	 */
	public function deleteDiscountsByCustomerGroupId(int $customer_group_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_discount` WHERE `customer_group_id` = '" . (int)$customer_group_id . "'");
	}

	/**
	 * Get Discounts
	 *
	 * Get the record of the discount records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, array<string, mixed>> discount records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_discounts = $this->model_shopmanager_catalog_product->getDiscounts($product_id);
	 */
	public function getDiscounts(int $product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_discount` WHERE `product_id` = '" . (int)$product_id . "' ORDER BY `quantity`, `priority`, `price`");

		return $query->rows;
	}

	/**
	 * Add Reward
	 *
	 * Create a new reward record in the database.
	 *
	 * @param int                  $product_id        primary key of the product record
	 * @param int                  $customer_group_id primary key of the customer group record
	 * @param array<string, mixed> $data              array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $product_data['product_reward'] = [
	 *     'points' => 0
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addReward($product_id, $customer_group_id, $product_data);
	 */
	public function addReward(int $product_id, int $customer_group_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_reward` SET `product_id` = '" . (int)$product_id . "', `customer_group_id` = '" . (int)$customer_group_id . "', `points` = '" . (int)$data['points'] . "'");
	}

	/**
	 * Delete Rewards
	 *
	 * Delete product reward records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteRewards($product_id);
	 */
	public function deleteRewards(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_reward` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Rewards By Customer Group ID
	 *
	 * Delete rewards by customer group records in the database.
	 *
	 * @param int $customer_group_id primary key of the customer group record to be deleted
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteRewardsByCustomerGroupId($customer_group_id);
	 */
	public function deleteRewardsByCustomerGroupId(int $customer_group_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_reward` WHERE `customer_group_id` = '" . (int)$customer_group_id . "'");
	}

	/**
	 * Get Rewards
	 *
	 * Get the record of the reward records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, array<string, mixed>> reward records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_reward = $this->model_shopmanager_catalog_product->getRewards($product_id);
	 */
	public function getRewards(int $product_id): array {
		$product_reward_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_reward` WHERE `product_id` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = $result;
		}

		return $product_reward_data;
	}

	/**
	 * Add Download
	 *
	 * Create a product download record in the database.
	 *
	 * @param int $product_id  primary key of the product record
	 * @param int $download_id primary key of the download record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addDownload($product_id, $download_id);
	 */
	public function addDownload(int $product_id, int $download_id): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_download` SET `product_id` = '" . (int)$product_id . "', `download_id` = '" . (int)$download_id . "'");
	}

	/**
	 * Delete Downloads
	 *
	 * Delete download records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteDownloads($product_id);
	 */
	public function deleteDownloads(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_download` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Downloads By Download ID
	 *
	 * Delete product downloads by download records in the database.
	 *
	 * @param int $download_id primary key of the download record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteDownloadsByDownloadId($download_id);
	 */
	public function deleteDownloadsByDownloadId(int $download_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_download` WHERE `download_id` = '" . (int)$download_id . "'");
	}

	/**
	 * Get Downloads
	 *
	 * Get the record of the product download records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, int> download records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_download = $this->model_shopmanager_catalog_product->getDownloads($product_id);
	 */
	public function getDownloads(int $product_id): array {
		$product_download_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_download` WHERE `product_id` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	/**
	 * Get Total Downloads By Download ID
	 *
	 * Get the total number of product downloads by download records in the database.
	 *
	 * @param int $download_id primary key of the download record
	 *
	 * @return int total number of download records that have download ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalDownloadsByDownloadId($download_id);
	 */
	public function getTotalDownloadsByDownloadId(int $download_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product_to_download` WHERE `download_id` = '" . (int)$download_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Add Store
	 *
	 * Create a new product store record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 * @param int $store_id   primary key of the store record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addStore($product_id, $store_id);
	 */
	public function addStore(int $product_id, int $store_id): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_store` SET `product_id` = '" . (int)$product_id . "', `store_id` = '" . (int)$store_id . "'");
	}

	/**
	 * Delete Stores
	 *
	 * Delete product store records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteStores($product_id);
	 */
	public function deleteStores(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_store` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Stores By Store ID
	 *
	 * Delete product stores by store records in the database.
	 *
	 * @param int $store_id primary key of the store record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteStoresByStoreId($store_id);
	 */
	public function deleteStoresByStoreId(int $store_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_store` WHERE `store_id` = '" . (int)$store_id . "'");
	}

	/**
	 * Get Stores
	 *
	 * Get the record of the product store records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, int> store records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_store = $this->model_shopmanager_catalog_product->getStores($product_id);
	 */
	public function getStores(int $product_id): array {
		$product_store_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_store` WHERE `product_id` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	/**
	 * Add Layout
	 *
	 * Create a new product layout record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 * @param int $store_id   primary key of the store record
	 * @param int $layout_id  primary key of the layout record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addLayout($product_id, $store_id, $layout_id);
	 */
	public function addLayout(int $product_id, int $store_id, int $layout_id): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_to_layout` SET `product_id` = '" . (int)$product_id . "', `store_id` = '" . (int)$store_id . "', `layout_id` = '" . (int)$layout_id . "'");
	}

	/**
	 * Delete Layouts
	 *
	 * Delete product layout records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteLayouts($product_id);
	 */
	public function deleteLayouts(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_layout` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Layouts By Layout ID
	 *
	 * Delete product layouts by layout records in the database.
	 *
	 * @param int $layout_id primary key of the layout record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteLayoutsByLayoutId($layout_id);
	 */
	public function deleteLayoutsByLayoutId(int $layout_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_layout` WHERE `layout_id` = '" . (int)$layout_id . "'");
	}

	/**
	 * Delete Layouts By Store ID
	 *
	 * Delete product layouts by store records in the database.
	 *
	 * @param int $store_id primary key of the store record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteLayoutsByStoreId($store_id);
	 */
	public function deleteLayoutsByStoreId(int $store_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_to_layout` WHERE `store_id` = '" . (int)$store_id . "'");
	}

	/**
	 * Get Seo Urls
	 *
	 * Get the record of the seo url records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, string> seo url records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_seo_url = $this->model_shopmanager_catalog_product->getSeoUrls($product_id);
	 */
	public function getSeoUrls(int $product_id): array {
		$product_seo_url_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "seo_url` WHERE `key` = 'product_id' AND `value` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $product_seo_url_data;
	}

	/**
	 * Get Layouts
	 *
	 * Get the record of the product layout records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, int> layout records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_layout = $this->model_shopmanager_catalog_product->getLayouts($product_id);
	 */
	public function getLayouts(int $product_id): array {
		$product_layout_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_to_layout` WHERE `product_id` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	/**
	 * Get Total Layouts By Layout ID
	 *
	 * Get the record of the product layouts by layout records in the database.
	 *
	 * @param int $layout_id primary key of the layout record
	 *
	 * @return int total number of layout records that have layout ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalLayoutsByLayoutId($layout_id);
	 */
	public function getTotalLayoutsByLayoutId(int $layout_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product_to_layout` WHERE `layout_id` = '" . (int)$layout_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Add Related
	 *
	 * Create a new related record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 * @param int $related_id primary key of the product related record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addRelated($product_id, $related_id);
	 */
	public function addRelated(int $product_id, int $related_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_related` WHERE `product_id` = '" . (int)$product_id . "' AND `related_id` = '" . (int)$related_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_related` SET `product_id` = '" . (int)$product_id . "', `related_id` = '" . (int)$related_id . "'");

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_related` WHERE `product_id` = '" . (int)$related_id . "' AND `related_id` = '" . (int)$product_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "product_related` SET `product_id` = '" . (int)$related_id . "', `related_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Delete Related
	 *
	 * Delete related record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteRelated($product_id);
	 */
	public function deleteRelated(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_related` WHERE `product_id` = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_related` WHERE `related_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Get Related
	 *
	 * Get the record of the related record in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, int> related records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_relateds = $this->model_shopmanager_catalog_product->getRelated($product_id);
	 */
	public function getRelated(int $product_id): array {
		$product_related_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_related` WHERE `product_id` = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	/**
	 * Add Subscription
	 *
	 * Create a new product subscription record in the database.
	 *
	 * @param int                  $product_id primary key of the product record
	 * @param array<string, mixed> $data       array of data
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $product_data['product_subscription'] = [
	 *     'customer_group_id'    => 1,
	 *     'subscription_plan_id' => 1,
	 *     'trial_price'          => 0.0000,
	 *     'price'                => 10.0000
	 * ];
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->addSubscription($product_id, $product_data);
	 */
	public function addSubscription(int $product_id, array $data): void {
		$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product_subscription` WHERE `product_id` = '" . (int)$product_id . "' AND `customer_group_id` = '" . (int)$data['customer_group_id'] . "' AND `subscription_plan_id` = '" . (int)$data['subscription_plan_id'] . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "product_subscription` SET `product_id` = '" . (int)$product_id . "', `customer_group_id` = '" . (int)$data['customer_group_id'] . "', `subscription_plan_id` = '" . (int)$data['subscription_plan_id'] . "', `trial_price` = '" . (float)$data['trial_price'] . "', `price` = '" . (float)$data['price'] . "'");
		}
	}

	/**
	 * Delete Subscriptions
	 *
	 * Delete product subscription records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteSubscriptions($product_id);
	 */
	public function deleteSubscriptions(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_subscription` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Get Subscription
	 *
	 * Get the record of the product subscription record in the database.
	 *
	 * @param int $product_id           primary key of the product record
	 * @param int $subscription_plan_id primary key of the subscription plan record
	 * @param int $customer_group_id    primary key of the customer group record
	 *
	 * @return array<string, mixed> subscription record that has product ID, subscription plan ID, customer group ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_subscription_info = $this->model_shopmanager_catalog_product->getSubscription($product_id, $subscription_plan_id);
	 */
	public function getSubscription(int $product_id, int $subscription_plan_id, int $customer_group_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_subscription` WHERE `product_id` = '" . (int)$product_id . "' AND `subscription_plan_id` = '" . (int)$subscription_plan_id . "' AND `customer_group_id` = '" . (int)$customer_group_id . "'");

		return $query->row;
	}

	/**
	 * Get Subscriptions
	 *
	 * Get the record of the product subscription records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return array<int, array<string, mixed>> subscription records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_subscriptions = $this->model_shopmanager_catalog_product->getSubscriptions($product_id);
	 */
	public function getSubscriptions(int $product_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_subscription` WHERE `product_id` = '" . (int)$product_id . "'");

		return $query->rows;
	}

	/**
	 * Delete Subscriptions By Subscription Plan ID
	 *
	 * Delete product subscriptions by subscription plan records in the database.
	 *
	 * @param int $subscription_plan_id primary key of the subscription plan record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteSubscriptionsBySubscriptionPlanId($subscription_plan_id);
	 */
	public function deleteSubscriptionsBySubscriptionPlanId(int $subscription_plan_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_subscription` WHERE `subscription_plan_id` = '" . (int)$subscription_plan_id . "'");
	}

	/**
	 * Get Total Subscriptions By Subscription Plan ID
	 *
	 * Get the total number of product subscriptions by subscription plan records in the database.
	 *
	 * @param int $subscription_plan_id primary key of the subscription plan record
	 *
	 * @return int total number of subscription records that have subscription plan ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $product_total = $this->model_shopmanager_catalog_product->getTotalSubscriptionsBySubscriptionPlanId($subscription_plan_id);
	 */
	public function getTotalSubscriptionsBySubscriptionPlanId(int $subscription_plan_id): int {
		$query = $this->db->query("SELECT COUNT(DISTINCT `product_id`) AS `total` FROM `" . DB_PREFIX . "product_subscription` WHERE `subscription_plan_id` = '" . (int)$subscription_plan_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Delete Reports
	 *
	 * Delete product report records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return void
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $this->model_shopmanager_catalog_product->deleteReports($product_id);
	 */
	public function deleteReports(int $product_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_report` WHERE `product_id` = '" . (int)$product_id . "'");
	}

	/**
	 * Get Reports
	 *
	 * Get the record of the product report records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 * @param int $start
	 * @param int $limit
	 *
	 * @return array<int, array<string, mixed>> report records that have product ID
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $results = $this->model_shopmanager_catalog_product->getReports($product_id, $start, $limit);
	 */
	public function getReports(int $product_id, int $start = 0, int $limit = 10): array {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT `ip`, `store_id`, `country`, `date_added` FROM `" . DB_PREFIX . "product_report` WHERE `product_id` = '" . (int)$product_id . "' ORDER BY `date_added` ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	/**
	 * Get Total Reports
	 *
	 * Get the total number of product report records in the database.
	 *
	 * @param int $product_id primary key of the product record
	 *
	 * @return int total number of product records
	 *
	 * @example
	 *
	 * $this->load->model('shopmanager/catalog/product');
	 *
	 * $report_total = $this->model_shopmanager_catalog_product->getTotalReports($product_id);
	 */
	public function getTotalReports(int $product_id): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "product_report` WHERE `product_id` = '" . (int)$product_id . "'");

		return (int)$query->row['total'];
	}

	/**
	 * Generate Meta Tags
	 * Génère meta_title, meta_description, meta_keyword, tag pour toutes les langues
	 * et met à jour directement oc_product_description.
	 *
	 * @param int $product_id
	 */
	public function generateMetaTag(int $product_id): void {
		$product = $this->getProduct($product_id);
		if (empty($product)) return;

		$upc = $product['upc'] ?? '';
		$descriptions = $this->getDescriptions($product_id);
		if (empty($descriptions)) return;

		foreach ($descriptions as $language_id => $description) {
			$name = trim($description['name'] ?? '');
			$conditionname = isset($description['condition'])
				? (is_array($description['condition'])
					? trim(implode(' ', $description['condition']))
					: trim($description['condition'])
				)
				: '';

			$additionalDescriptionHtml = trim($description['description_supp'] ?? '');
			$additionalDescriptionText = preg_replace('/<\/?[^>]+(>|$)/', '', $additionalDescriptionHtml);
			$additionalDescriptionText = str_replace('&nbsp;', ' ', $additionalDescriptionText);
			$additionalDescriptionText = preg_replace('/\s+/', ' ', $additionalDescriptionText);

			$metaTagTitle = '(' . $conditionname . ') ' . $name . ' UPC: ' . $upc;
			$metaTagDescription = $additionalDescriptionText;

			$tagkeywords = trim($conditionname . ' ' . $name . ' ' . $upc);
			$tagkeywords = preg_replace('/[.,;:\'"\{\}\[\]\(\)@%$&\-]/', '', $tagkeywords);
			$tagkeywords = implode(',', array_filter(explode(' ', $tagkeywords)));
			if (substr($tagkeywords, -1) === ',') {
				$tagkeywords = substr($tagkeywords, 0, -1);
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "product_description`
				SET `meta_title` = '" . $this->db->escape($metaTagTitle) . "',
				    `meta_description` = '" . $this->db->escape($metaTagDescription) . "',
				    `meta_keyword` = '" . $this->db->escape($tagkeywords) . "',
				    `tag` = '" . $this->db->escape($tagkeywords) . "'
				WHERE `product_id` = '" . (int)$product_id . "' AND `language_id` = '" . (int)$language_id . "'");
		}
	}

	public function editProductSpecifics($product_id, $product_specific, $language_id=1) {
		$this->db->query("UPDATE " . DB_PREFIX . "product_description SET specifics = " . (isset($product_specific)? "'".$this->db->escape($product_specific)."'" :'null') . "
		  WHERE language_id='".$language_id."' AND product_id = '" . (int)$product_id . "'");
}

public function editProductError($product_id, $json_error) {
	$this->db->query("UPDATE " . DB_PREFIX . "product SET error = " . (isset($json_error)? "'".$this->db->escape($json_error)."'" :'') . "
	  WHERE product_id = '" . (int)$product_id . "'");
}

	public function editProductMarketplaceItemId($product_id, $marketplace_id, $marketplace_item_id) {
		$this->db->query("
			INSERT INTO `" . DB_PREFIX . "product_marketplace` 
			SET `product_id` = '" . (int)$product_id . "',
				`marketplace_id` = '" . (int)$marketplace_id . "',
				`marketplace_item_id` = '" . $this->db->escape($marketplace_item_id) . "',
				`error` = '',
				`to_update` = 0,
				`date_modified` = NOW()
			ON DUPLICATE KEY UPDATE 
				`marketplace_item_id` = VALUES(`marketplace_item_id`),
				`error` = '',
				`to_update` = 0,
				`date_modified` = NOW()
		");
	}
	
	public function editProductSku($product_id,$sku = null){
		$this->db->query("UPDATE " . DB_PREFIX . "product SET sku = '" . $this->db->escape($sku??$product_id) . "' 
		WHERE product_id = '" . (int)$product_id . "'");
	}

	public function editMadeInCountry($product_id,$made_in_country_id){
		$this->db->query("UPDATE " . DB_PREFIX . "product SET made_in_country_id = '" . $this->db->escape($made_in_country_id) . "' 
		WHERE product_id = '" . (int)$product_id . "'");


	}

	
	public function editStatus($product_id,$status = 1){
		$this->db->query("UPDATE " . DB_PREFIX . "product SET status = ".$status." 
		WHERE product_id = '" . (int)$product_id . "'");
	}

	public function editShipping(int $product_id, float $shipping_cost, string $shipping_carrier): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "product` SET
			`shipping_cost` = '" . (float)$shipping_cost . "',
			`shipping_carrier` = '" . $this->db->escape($shipping_carrier) . "'
			WHERE `product_id` = '" . $product_id . "'");
	}

	public function editPriceWithShipping($product_id, $price_with_shipping) {
		$product_id = (int)$product_id;
		$price_with_shipping = (float)$price_with_shipping;
	
		// Récupérer le shipping_cost actuel
		$query = $this->db->query("SELECT `shipping_cost` FROM `" . DB_PREFIX . "product` WHERE `product_id` = '" . $product_id . "'");
	
		if (!$query->num_rows) {
			return 'ERROR'; // produit introuvable
		}
	
		$shipping_cost = (float)$query->row['shipping_cost'];
		$price = round($price_with_shipping - $shipping_cost, 2);
	
		// Si le prix final est trop bas, on le corrige et on réécrit le price_with_shipping
		if ($price < 0.99) {
			$price = 0.99;
			$price_with_shipping = round($price + $shipping_cost, 2);
		}
	
		// Mise à jour des deux colonnes
		$this->db->query("UPDATE `" . DB_PREFIX . "product` 
			SET 
				`price_with_shipping` = '" . $this->db->escape($price_with_shipping) . "', 
				`price` = '" . $this->db->escape($price) . "' 
			WHERE `product_id` = '" . $product_id . "'");
	
		return $price;
	}

		public function addSpecificsStatsToProducts($products, $language_id = 1) {
		foreach ($products as &$product) {
			$filled_count = 0;
			$total_count = 0;
	
			$product_specifics = [];
			$category_specifics = [];
	
			$product_id = (int)$product['product_id'];
	
			// Récupérer specifics du produit + catégorie_id
			$query_product = $this->db->query("
				SELECT pd.`specifics` as `product_specifics`, cd.`specifics` as `category_specifics`
				FROM `" . DB_PREFIX . "product_description` pd
				LEFT JOIN `" . DB_PREFIX . "product` p ON p.`product_id` = pd.`product_id`
				LEFT JOIN `" . DB_PREFIX . "product_to_category` ptc ON (p.`product_id` = ptc.`product_id`)
				LEFT JOIN `" . DB_PREFIX . "category` c ON (ptc.`category_id` = c.`category_id`)
				LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.`category_id` = cd.`category_id` AND cd.`language_id` = '" . (int)$language_id . "')
				WHERE c.`leaf`=1 AND pd.`product_id` = '" . $product_id . "' AND pd.`language_id` = '" . (int)$language_id . "'
			");
	
			if ($query_product->num_rows) {
				$product_row = $query_product->row;

				$product_specifics = (!empty($product_row['product_specifics']) && $product_row['product_specifics'] !== null) 
					? json_decode($product_row['product_specifics'], true) 
					: [];

				$category_specifics = (!empty($product_row['category_specifics']) && $product_row['category_specifics'] !== null) 
					? json_decode($product_row['category_specifics'], true) 
					: [];
			}
	
			// Calcul des counts
			if (is_array($category_specifics)) {
				foreach ($category_specifics as $key => $cat_spec) {
					$total_count++;
	
					if (
						isset($product_specifics[$key]['Value']) &&
						!empty($product_specifics[$key]['Value']) &&
						!(
							is_array($product_specifics[$key]['Value']) &&
							count(array_filter($product_specifics[$key]['Value'])) === 0
						)
					) {
						$filled_count++;
					}
				}
			}
	
			// Ajouter les résultats au produit
			$product['total_specifics_count'] = $total_count;
			$product['filled_specifics_count'] = $filled_count;
		}
	
		return $products;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` p 
		LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`)
		LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON (p.`product_id` = p2c.`product_id`) 
		WHERE pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "' AND p2c.`category_id` = '" . (int)$category_id . "' ORDER BY pd.`name` ASC");

		return $query->rows;
	}

	public function getIDbySku($sku) {
		$query = $this->db->query("SELECT `product_id` FROM `" . DB_PREFIX . "product` p 
		WHERE p.`sku` = '"  . $this->db->escape($sku) . "' ");

		return $query->row['product_id'];
	}
	public function getUPCBySku($sku) {
		$query = $this->db->query("SELECT `upc` FROM `" . DB_PREFIX . "product` p 
		WHERE p.`sku` = '"  . $this->db->escape($sku) . "' ");

		return $query->row['upc'];
	}
	public function getProductsCondition_NOTUSED($product_id) {
		$product_condition_data = [];

		$query = $this->db->query("SELECT c.`name`,c.`language_id`,c.`condition_id` FROM `" . DB_PREFIX . "product` p 
		LEFT JOIN `" . DB_PREFIX . "condition` c ON (p.`condition_id` = c.`condition_id`)
		WHERE p.`product_id` = '" . (int)$product_id . "'");
		$rows=$query->rows;
		
		foreach ($rows as $result) {

			$product_condition_data[$result['language_id']] = array(
				'condition'    => $result['name'],
				'condition_id'    => $result['condition_id'],
			);
		}
	//print("<pre>".print_r ($product_condition_data,true )."</pre>");
			return $product_condition_data;	
	}

	public function updateQuantity($product_id, $quantity) {
		// Mise à jour de la quantité du produit
		$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$product_id . "'");
	}
	
	public function updateUnallocatedQuantity($product_id, $quantity) {
		// Mise à jour de la quantité non allouée du produit (mettre à 0)
		$this->db->query("UPDATE " . DB_PREFIX . "product SET unallocated_quantity = '" . (int)$quantity . "' WHERE product_id = '" . (int)$product_id . "'");
	}
	
	public function updateLocation($product_id, $location) {
		// Mise à jour de la localisation du produit
		$this->db->query("UPDATE " . DB_PREFIX . "product SET location = '" . $this->db->escape($location) . "' WHERE product_id = '" . (int)$product_id . "'");
	}


	public function updateProductImage($product_id, $image_path) {
		if (!empty($image_path)) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($image_path) . "' WHERE product_id = '" . (int)$product_id . "'");
		}
	}

	// Fonction publique pour insérer une nouvelle image dans la table `product_image`
	public function insertProductImage($product_id, $image_path) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($image_path) . "'");
	}

	public function getByUPC($upc, $exclude_condition_id = null, $condition_id = null) {
		// Initialisation de la clause WHERE
		//print("<pre>" . print_r($upc, true) . "</pre>");
		//print("<pre>" . print_r($exclude_condition_id, true) . "</pre>");
		//print("<pre>" . print_r($condition_id, true) . "</pre>");

		$where_clause = "WHERE p.upc = '" . $this->db->escape($upc) . "'";

		// Si exclude_condition_id est spécifié, ajouter la condition d'exclusion
		if (!empty($exclude_condition_id)) {
			$where_clause .= " AND p.condition_id != '" . $this->db->escape($exclude_condition_id) . "'";
		}

		// Si condition_id est spécifié, ajouter la condition supplémentaire
		if (!empty($condition_id)) {
			$where_clause .= " AND p.condition_id = '" . $this->db->escape($condition_id) . "'";
		}
		$where_clause .= " ORDER BY has_specifics DESC";
		// Construire la requête SQL complète
		$query = $this->db->query(
			"SELECT p.*, 
				(SELECT pc.category_id
					FROM " . DB_PREFIX . "product_to_category pc 
					LEFT JOIN " . DB_PREFIX . "category ca ON (pc.category_id = ca.category_id)
					WHERE pc.product_id = p.product_id AND ca.leaf = 1
					LIMIT 1) AS category_id,
				IF(pd.specifics IS NOT NULL AND pd.specifics != '', '1', '0') AS has_specifics 
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') " . $where_clause
		);

		// Vérifier si un produit a été trouvé
		if ($query->num_rows) {
			// Créer un tableau associatif avec product_id comme clé
			$products = [];
			foreach ($query->rows as $row) {
				$products[$row['product_id']] = $row;
			}
			return $products; // Retourner le tableau associatif
		} else {
			return array(); // Retourner un tableau vide si aucun produit n'est trouvé
		}
	}

	
	/**
	 * Generate Description
	 * Génère le HTML de description pour toutes les langues d'un produit
	 * et met à jour directement oc_product_description.
	 *
	 * @param int $product_id
	 */
	public function generateDescription(int $product_id): void {
		$product = $this->getProduct($product_id);
		if (empty($product)) return;

		$product_images = $this->getImages($product_id);
		$descriptions   = $this->getDescriptions($product_id);
		if (empty($descriptions)) return;

		$category_id = (int)($product['category_id'] ?? 0);

		foreach ($descriptions as $language_id => $product_description) {
			if (!isset($product_description['name']) || is_array($product_description['name'])) continue;

			$name = htmlspecialchars($product_description['name']);
			$description = '<style>';
			$description .= '.secondary-list-item { list-style-type: none; padding-left: 3em; text-indent: -1em; }';
			$description .= '</style>';
			$description .= '<h1>' . $name . '</h1>';

			// Condition du produit
			$condition = $product_description['condition'] ?? '';
			if (!in_array($category_id, [73836, 20349, 178893, 182066, 123417, 112529, 58540, 33602, 146496, 48619, 20357, 80077, 123422, 96991, 35190, 48677, 182068, 42425])) {
				$description .= '<h3 style="color: darkblue;"><b>Condition:</b> <b style="color: black;">' . $condition . '</b></h3>';
				$additionalConditions = trim($product_description['condition_supp'] ?? '');
				if ($additionalConditions) {
					$description .= '<h4 style="color: red;"><b>Additional Conditions:</b></h4>';
					$description .= $additionalConditions;
				}
			}

			// Accessoires inclus
			$includedAccessories = trim($product_description['included_accessories'] ?? '');
			if ($includedAccessories) {
				$description .= '<h3 style="color: darkblue;"><b>Included Accessories:</b></h3>';
				$description .= $includedAccessories;
			}

			// Description supplémentaire
			$additionalDescription = trim($product_description['description_supp'] ?? '');
			if ($additionalDescription) {
				$description .= '<h3 style="color: darkblue;"><b>Description:</b></h3>';
				$description .= htmlspecialchars_decode($additionalDescription);
			}

			// Caractéristiques spécifiques
			$description .= '<h3 style="color: darkblue;"><b>Specific Features:</b></h3><ul class="three-columns">';
			if (!isset($product_description['specifics']) || empty($product_description['specifics'])) {
				$description .= '<li>No specific features available.</li>';
			} else {
				foreach ($product_description['specifics'] as $specific) {
					$spec_name = trim($specific['Name']);
					$values = [];
					if (isset($specific['Value'])) {
						$values = is_array($specific['Value']) ? $specific['Value'] : [$specific['Value']];
					}
					if (!empty($values) && !($values[0] == '' && count($values) == 1)) {
						$valueList = array_filter($values, fn($v) => trim($v) != '');
						$description .= '<li><b>' . $spec_name . ':</b> ' . implode(', ', $valueList) . '</li>';
					}
				}
			}
			$description .= '</ul>';

			// Modèle, dimensions et poids
			if ($language_id == 1) {
				$model = 'Model:'; $dimension = 'Package Dimension:'; $weight = 'Package Weight:'; $lbs = ' Lbs'; $inch = ' Inch';
			} else {
				$model = 'Modèle: '; $dimension = 'Dimensions du colis: '; $weight = 'Poids du colis: '; $lbs = ' Livres'; $inch = ' Pouces';
			}
			$description .= '<p><b>' . $model . '</b> ' . htmlspecialchars($product['model'] ?? '') . '</p>';
			$description .= '<p><b>' . $dimension . '</b> ' . doubleval($product['length'] ?? 0) . 'x' . doubleval($product['width'] ?? 0) . 'x' . doubleval($product['height'] ?? 0) . $inch . '</p>';
			$description .= '<p><b>' . $weight . '</b> ' . doubleval($product['weight'] ?? 0) . $lbs . '</p>';

			// Images du produit
			$description .= '<h3 style="color: darkblue;"><b>Photos:</b></h3>';
			$description .= '<table bgcolor="FFFFFF" style="width: 500px;" border="1" cellspacing="1" cellpadding="5" align="center"><tbody>';
			if (!empty($product['image'])) {
				$description .= '<tr><td style="text-align: center;" align="center" valign="middle"><img src="' . HTTP_CATALOG . 'image/' . $product['image'] . '" width="450"></td></tr>';
			}
			foreach ($product_images as $image) {
				if (!is_array($image)) $image = ['image' => $image];
				$description .= '<tr><td style="text-align: center;" align="center" valign="middle"><img src="' . HTTP_CATALOG . 'image/' . ($image['image'] ?? '') . '" width="450"></td></tr>';
			}
			$description .= '</tbody></table>';

			$this->db->query("UPDATE `" . DB_PREFIX . "product_description`
				SET `description` = '" . $this->db->escape($description) . "'
				WHERE `product_id` = '" . (int)$product_id . "' AND `language_id` = '" . (int)$language_id . "'");
		}
	}

	public function TranslateDescription($productDescription, $language = ['code' => 'Fr', 'language_id' => '2']) {
		$this->load->model('shopmanager/tools');
		//$this->model_shopmanager_tools->debug_function_trace();
		// 🔹 Vérification et préparation des données
		if (empty($productDescription)) {
			return ['error' => 'Empty product description'];
		}

		$formattedDescription = [];
		$data_name_key = [];
		$specifics = $productDescription['specifics'] ?? [];
		unset( 
			$productDescription['meta_title'], 
			$productDescription['meta_description'], 
			$productDescription['description'], 
			$productDescription['meta_keyword'], 
			$productDescription['tag'], 
			$productDescription['keyword']
		);//$productDescription['specifics'],

		// 🔹 Réindexer les clés numériques
		$i = 0;
		foreach ($productDescription as $key => $value) {
			$formattedDescription[$key] = $value;
			$data_name_key[$i] = $key;
			$i++;
		}

		// 🔹 Encodage en JSON propre
		$jsonData = json_encode($formattedDescription, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

		// 🔹 Traduction via OpenAI
		$this->load->model('shopmanager/ai');
		$translatedText = $this->model_shopmanager_catalog_product->model_shopmanager_ai->translate($jsonData, $language['code']);

		// 🔹 Vérification et décodage JSON si nécessaire
		if (is_string($translatedText)) {
			$translatedText = json_decode($translatedText, true);
		}
		//print("<pre>".print_r ('1837:product.php',true )."</pre>");
		//print("<pre>".print_r ($translatedText,true )."</pre>");
		// 🔹 Vérification que la traduction contient bien un tableau
	/* if (!isset($translatedText) || !is_array($translatedText) ) {
			//error_log("❌ Erreur : traduction invalide\n" . print_r($translatedText, true));
			//print("<pre>".print_r ('1837:product.php',true )."</pre>");
			//print("<pre>".print_r ($translatedText,true )."</pre>");
			return ['error' => 'Invalid AI response format'];
		}*/

		// 🔹 Extraction des traductions
		//$translatedText = $translatedText;
		//print("<pre>".print_r ('1853:product.php',true )."</pre>");
		//print("<pre>".print_r ($translatedText,true )."</pre>");
		// 🔹 Vérification de la structure du tableau
		if (is_array($translatedText)) {
			$firstKey = array_key_first($translatedText);
			if (strtolower($firstKey) === strtolower($language['code'])) {
				$translatedText = $translatedText[$firstKey];
				//print("<pre>".print_r ('1847:product.php',true )."</pre>");
				//print("<pre>".print_r ($translatedText,true )."</pre>");
			}
			
		}

		// 🔹 Conversion en structure finale avec vérification des indices
		$finalDescription = [];
	/*	if (!is_array($translatedText) && count($translatedText) == count($data_name_key)) {
			$translatedText =$translatedText;
			//print("<pre>".print_r ('1858:product.php',true )."</pre>");
			//print("<pre>".print_r ($translatedText,true )."</pre>");

		}*/
		foreach ($translatedText as $key => $value) {
				if (isset($data_name_key[$key])) {
					$name = $data_name_key[$key];
					$finalDescription[$name] = $value;
				} else {
					//error_log("⚠️ Attention : clé introuvable dans `data_name_key` → key: " . print_r($key, true));
					//print("<pre>".print_r ('1871:product.php',true )."</pre>");
					//print("<pre>".print_r ($translatedText,true )."</pre>");
					$finalDescription = $translatedText;
					break;
				}
			}
		

		// 🔹 Traduction des spécificités
		//$translated_specifics = $this->model_shopmanager_catalog_product->model_shopmanager_ai->translate_specifics(null, $specifics, $language);
		//$finalDescription['specifics'] = $translated_specifics;

		return $finalDescription;
	}

	public function getProductsToSyncEbay($limit = 100, $offset = 0) {
		$query = $this->db->query("
		SELECT p.product_id, pm.marketplace_item_id, p.quantity, p.unallocated_quantity
			FROM " . DB_PREFIX . "product p
			LEFT JOIN " . DB_PREFIX . "product_marketplace pm 
			ON p.product_id = pm.product_id 
			WHERE pm.marketplace_item_id IS NOT NULL 
			AND pm.marketplace_item_id != '' 
			AND (p.quantity + p.unallocated_quantity) > 0 
			AND p.ebay_json IS NULL
			ORDER BY p.quantity DESC
			LIMIT " . (int)$limit . " OFFSET " . (int)$offset

		);

		return $query->rows;
	}

	public function getSpecifics($product_id) {
	$query = $this->db->query("SELECT specifics, language_id FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
	$rows = $query->rows;
	$data_return = [];
	$data_product = [];
	//print("<pre>".print_r ($rows,true )."</pre>");
	$this->load->model('shopmanager/catalog/category');

	if (!empty($rows)) {
		$category_id = $this->getCategoryLeaf($product_id);
		//print("<pre>".print_r ($category_id,true )."</pre>");
		if ($category_id > 0) {
			// **Récupérer les category_specifics**
			$category_specifics = $this->model_shopmanager_catalog_category->getSpecifics($category_id);
			//print("<pre>".print_r ($category_id,true )."</pre>");
			//print("<pre>".print_r ($category_specifics,true )."</pre>");				// **Initialisation des valeurs par défaut à partir de category_specifics**
				foreach ($category_specifics as $language_id => $specific_data) {
					if(isset($specific_data['specifics'])){
						foreach ($specific_data['specifics'] as $key => $category_specific) {
							$data_return[$language_id]['specifics'][$key] = 
								[
									'Name' => $category_specific['localizedAspectName'],
									'Value' => '',
									'VerifiedSource' => '',
									'to_translate' => $this->model_shopmanager_catalog_category->isSpecificValueToTranslated($category_specific['localizedAspectName']),
									'specific_info' =>$category_specific
								];
								
						}
					}
				}

				// **Stocker les specifics de product_description**
				foreach ($rows as $data) {
					$language_id = $data['language_id'];
					$specifics = json_decode(trim(html_entity_decode($data['specifics'] ?? '', ENT_QUOTES | ENT_HTML5)), true);
					if (!empty($specifics)) {
						$data_product[$language_id] = $specifics;
					}
				}
		//print("<pre>".print_r ($data_product,true )."</pre>");
				// **Fusionner les specifics de product_description avec category_specifics**
				foreach ($data_product as $language_id => $specifics) {
					foreach ($specifics as $key => $specific) {
						if (isset($data_return[$language_id]['specifics'][$key])) {
							$data_return[$language_id]['specifics'][$key] = array_merge(
								$data_return[$language_id]['specifics'][$key],
								[
									
									'Value' => $specific['Value']??'',
									'VerifiedSource' => $specific['VerifiedSource']??''
								]
							);
						}else{
						/*	$data_return[$language_id]['specifics'][$key] = 
							[
								'Name' => $specific['Name']??'',
								'Value' => $specific['Value']??'',
								'VerifiedSource' => $specific['VerifiedSource']??''
							];*/
						}
					}
				}
			}
		}
		//print("<pre>".print_r (1138,true )."</pre>");
		//print("<pre>".print_r ($data_return,true )."</pre>");
		return !empty($data_return) ? $data_return : [];
	}

	public function extractProductSpecifics($product_description) {
		$data_return = array();
		
		// Si aucune donnée fournie, retourner tableau vide
		if (empty($product_description)) {
			return array();
		}
		
		// Extraire simplement les specifics déjà formatés
		foreach ($product_description as $language_id => $desc_data) {
			if (isset($desc_data['specifics']) && !empty($desc_data['specifics'])) {
				$data_return[$language_id] = $desc_data['specifics'];
			}
		}
		
		return $data_return;
	}
	public function getCondition($product_id) {
		$product_condition_data = array();

		$query = $this->db->query("SELECT c.name,c.language_id,c.condition_id FROM " . DB_PREFIX . "product p 
		LEFT JOIN " . DB_PREFIX . "condition c ON (p.condition_id = c.condition_id)
		WHERE p.product_id = '" . (int)$product_id . "'");
		$rows=$query->rows;
		
		foreach ($rows as $result) {

			$product_condition_data[$result['language_id']] = array(
				'condition'    => $result['name'],
				'condition_id'    => $result['condition_id'],
			);
		}
	//print("<pre>".print_r ($product_condition_data,true )."</pre>");
			return $product_condition_data;	
	}

	public function removeProductsNoMissingFile($results){

			$i=0;
			foreach ($results as $product_info) {
				if (is_file(DIR_IMAGE . $product_info['image'])) {
					unset($results[$i]);
				}
				$i++;
			}
			return $results;
	}

	 public function removeTotalProductsNoMissingFile($data = array()) {
            $sql = "SELECT p.image FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id) ";
            $sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
    
            if (!empty($data['filter_sku'])) {
                $sql .= " AND (p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "' OR p.upc LIKE '" . $this->db->escape($data['filter_sku']) . "') ";
            }
    
            if (!empty($data['filter_product_id'])) {
                $sql .= " AND p.product_id = '" . $this->db->escape($data['filter_product_id']) . "'";
            }
    
            if (!empty($data['filter_marketplace_account'])) {
                $sql .= " AND p.marketplace_item_id = '" . $this->db->escape($data['filter_marketplace_account']) . "'";
            }
    
            if (!empty($data['filter_name'])) {
                $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
            }
    
            if (!empty($data['filter_category_id'])) {
                $sql .= " AND ptc.category_id LIKE '" . $this->db->escape($data['filter_category_id']) . "%'";
            }
    
            if (!empty($data['filter_model'])) {
                $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
            }
    
            if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
                $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
            }
    
            if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
                $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
            }
    
            if (isset($data['filter_unallocated_quantity']) && !is_null($data['filter_unallocated_quantity'])) {
                $sql .= " AND p.unallocated_quantity = '" . (int)$data['filter_unallocated_quantity'] . "'";
            }
    
            if (isset($data['filter_location']) && !is_null($data['filter_location'])) {
                $sql .= " AND p.location = '" . (int)$data['filter_location'] . "'";
            }
    
            if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
                $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
            }
    
            if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
                if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
                    $sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
                } else {
                    $sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
                }
            }
    
            $query = $this->db->query($sql);
    
            $total=0;
            $rows=$query->rows;
            
    
                foreach ($rows as $row) {
                    if (is_file(DIR_IMAGE . $row['image'])) {					
                        $total++;
                    }
                }
            //	//print("<pre>".print_r ($total,true )."</pre>");
            return $total;
        }

}
