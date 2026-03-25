<?php
/**
 * @version [Supported opencart version 3.x.x.x.]
 * @category Webkul
 * @package Opencart Ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
 class ModelEbayMapProductData extends Model {
   /**
 	 * [__saveOpencartProductData assign ebay specification and condition to opencart product]
 	 * @param  boolean $product_id [description]
 	 * @param  array   $data       [description]
 	 * @return [type]              [description]
 	 */
 	public function saveMappedProductData($product_id = false, $data = array()) {
 		if ($product_id) {

 			$this->load->model('ebay_map/ebay_map_product');

 			$product_template_id = $this->model_ebay_map_ebay_map_product->__saveOpencartProductData($product_id, $data);


   		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_ebay_product_data` WHERE `product_id` = " . (int)$product_id . "");

   		if ($query->num_rows) {
   			$this->db->query("UPDATE `" . DB_PREFIX . "wk_ebay_product_data` SET `product_template_id` = " . (int)$product_template_id . " WHERE `product_id` = " . (int)$product_id . "");
   		} else {
   			$this->db->query("INSERT INTO `" . DB_PREFIX . "wk_ebay_product_data` SET `product_template_id` = " . (int)$product_template_id . ", `product_id` = " . (int)$product_id . "");
   		}

      if ($product_id) {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_ebay_auction_product` WHERE `product_id` = " . (int)$product_id . "");
        if ($query->num_rows) {
          $this->db->query("UPDATE `" . DB_PREFIX . "wk_ebay_auction_product` SET `buy_it_now_price` = '" . (float)$data['buy_it_now_price'] . "', `auction_status` = " . (int)$data['auction_status'] . ", `price_rule_status` = '" . $data['price_rule_status'] . "' WHERE `product_id` = " . (int)$product_id . "");
        } else if ($data['buy_it_now_price']) {
          $this->db->query("INSERT INTO `" . DB_PREFIX . "wk_ebay_auction_product` SET `buy_it_now_price` = '" . (float)$data['buy_it_now_price'] . "', `product_id` = " . (int)$product_id . ", `auction_status` = " . (int)$data['auction_status'] . ", `price_rule_status` = '" . $data['price_rule_status'] . "'");
        }
      }
    }
 		return true;
 	}
   	/**
   	* Updated Function
   	* This function is used to get the list mapped data [specification, condition, variations, template]
   	* @param array [$data] filter data
   	* @return array [rows] array data of list
   	*/
   	public function getMappedProductData($data = array()) {

      $category_query = '';

      if (!empty($data['filter_category_id'])) {
        $category_query = " LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON (p.product_id = p2c.product_id) LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (cd.category_id = p2c.category_id) ";
      }

   		$sql = "SELECT DISTINCT p.product_id, p.image, pd.name, p.quantity, p.model, p.price FROM `" . DB_PREFIX . "wk_ebay_product_data` wpd LEFT JOIN `" . DB_PREFIX . "product` p ON (p.product_id = wpd.product_id) LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) " . $category_query . " WHERE pd.language_id = " . (int)$this->config->get('config_language_id') . "";

      if (!empty($data['filter_category_id'])) {
        $sql .= " AND cd.language_id = " . (int)$this->config->get('config_language_id');
      }
   		if (!empty($data['filter_product'])) {
   			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
   		}

   		if (!empty($data['filter_product_id'])) {
   			$sql .= " AND p.product_id = " . (int)$data['filter_product_id'] . "";
   		}

   		if (!empty($data['filter_category_id'])) {
   			$sql .= " AND p2c.category_id = " . (int)$data['filter_category_id'] . "";
   		}

   		if (!empty($data['filter_model'])) {
   			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
   		}

   		$sort_data = array(
   			'pd.name',
   			'p.model',
   			'p.quantity',
        'p.price'
   		);

   		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
   			$sql .= " ORDER BY " . $data['sort'];
   		} else {
   			$sql .= " ORDER BY pd.name";
   		}

   		if (isset($data['order']) && ($data['order'] == 'DESC')) {
   			$sql .= " DESC";
   		} else {
   			$sql .= " ASC";
   		}

   		if (isset($data['start']) || isset($data['limit'])) {
   			if ($data['start'] < 0) {
   				$data['start'] = 0;
   			}

   			if ($data['limit'] < 1) {
   				$data['limit'] = 20;
   			}

   			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
   		}

   		$query = $this->db->query($sql);

   		return $query->rows;

   	}

   	/**
   	* Updated Function
   	* This function is used to get the total mapped data [specification, condition, variations, template]
   	* @param array [$data] filter data
   	* @return array [num_rows] number of the of the rows
   	*/
   	public function getTotalMappedProductData($data = array()) {

      $category_query = '';

      if (!empty($data['filter_category_id'])) {
        $category_query = " LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON (p.product_id = p2c.product_id) LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (cd.category_id = p2c.category_id) ";
      }

   		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM `" . DB_PREFIX . "wk_ebay_product_data` wpd LEFT JOIN `" . DB_PREFIX . "product` p ON (p.product_id = wpd.product_id) LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) " . $category_query . " WHERE pd.language_id = " . (int)$this->config->get('config_language_id') . "";

      if (!empty($data['filter_category_id'])) {
        $sql .= " AND cd.language_id = " . (int)$this->config->get('config_language_id');
      }
   		if (!empty($data['filter_product'])) {
   			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_product']) . "%'";
   		}

   		if (!empty($data['filter_product_id'])) {
   			$sql .= " AND p.product_id = " . (int)$data['filter_product_id'] . "";
   		}

   		if (!empty($data['filter_model'])) {
   			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
   		}

   		if (!empty($data['filter_category_id'])) {
   			$sql .= " AND p2c.category_id = " . (int)$data['filter_category_id'] . "";
   		}

   		if (!empty($data['filter_model'])) {
   			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
   		}

   		$query = $this->db->query($sql);

   		return $query->row['total'];

   	}

   	/**
   	* Updated Function
   	* This function is used to get the categories assigned to a product
   	* @param int [$product_id]
   	* @return array [$categories] of a product
   	*/
   	public function getMappedProductCategories($product_id) {
   		return $this->db->query("SELECT cd.name, c.category_id FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON (c.category_id = cd.category_id) LEFT JOIN `" . DB_PREFIX . "product_to_category` p2c ON (p2c.category_id = c.category_id) WHERE c.status = 1 AND p2c.product_id = " . (int)$product_id)->rows;
   	}

    /**
    * This function is used to delete the mapped product data
    * @param int [$product_id]
    * @return no return
    */
    public function deleteMappedProductData($product_id) {
      $this->load->model('ebay_map/ebay_map_product');
      $this->model_ebay_map_ebay_map_product->deleteOpencartProductWithEbay($product_id);

			$this->db->query("DELETE FROM `" . DB_PREFIX . "wk_ebay_product_data` WHERE `product_id` = " . (int)$product_id . "");
    }

    public function getAutocomplete($data = array()) {
      $sql = "SELECT DISTINCT(pd.name) AS name, p.product_id, p.model, p.price FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id) LEFT JOIN `" . DB_PREFIX . "wk_ebay_oc_product_map` eopm ON (p.product_id != eopm.oc_product_id) LEFT JOIN `" . DB_PREFIX . "wk_ebay_product_data` epd ON (p.product_id != epd.product_id) WHERE pd.language_id = " . $this->config->get('config_language_id') . "";

      if (!empty($data['filter_name'])) {
        $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
      }

      $sql .= " ORDER BY pd.name ASC LIMIT 0, 8";

      return $this->db->query($sql)->rows;
    }

    public function getProductAuction($product_id) {
      return $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_ebay_auction_product` WHERE product_id = " . (int)$product_id . "")->row;
    }
 }
