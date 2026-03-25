<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapEbayAccount extends Model {

	/**
	 * [getEbayAccount to get Ebay Account list or particular account details]
	 * @param  array  $data [filter data array]
	 * @return [type]       [list of ebay accounts]
	 */
	public function getEbayAccount($data = array(), $type = false) {
		$sql = "SELECT * FROM " . DB_PREFIX . "wk_ebay_accounts WHERE 1 ";

		if (!empty($data['filter_account_id'])) {
			$sql .= " AND id = '" . (int)$data['filter_account_id'] . "'";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND ebay_connector_store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}else if (!empty($data['filter_store_name']) && $type) {
			$sql .= " AND ebay_connector_store_name = '" . $this->db->escape($data['filter_store_name']) . "'";
		}

		if (!empty($data['filter_ebay_user_id'])) {
			$sql .= " AND ebay_connector_ebay_user_id LIKE '%" . $this->db->escape($data['filter_ebay_user_id']) . "%'";
		}

		$sort_data = array(
			'id',
			'ebay_connector_store_name',
			'ebay_connector_ebay_user_id',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id";
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
	 * [getTotalEbayAccount to get the total number of ebay account]
	 * @param  array  $data [filter data array]
	 * @return [type]       [total number of ebay account records]
	 */
	public function getTotalEbayAccount($data = array()) {
		$sql = "SELECT COUNT(DISTINCT id) AS total FROM " . DB_PREFIX . "wk_ebay_accounts WHERE 1 ";

		if (!empty($data['filter_account_id'])) {
			$sql .= " AND id = '" . (int)$data['filter_account_id'] . "'";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND ebay_connector_store_name LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}

		if (!empty($data['filter_ebay_user_id'])) {
			$sql .= " AND ebay_connector_ebay_user_id LIKE '%" . $this->db->escape($data['filter_ebay_user_id']) . "%'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	/**
	 * [__addEbayAccount to add/update the ebay account details]
	 * @param  array  $data [details of ebay account]
	 * @return [type]       [description]
	 */
	public function __addEbayAccount($data = array()) {
		$Id = 0;

    $allowed_ebay_events = isset($data['ebay_connector_ebay_event']) ? $data['ebay_connector_ebay_event'] : array();

		if (isset($data['account_id']) && $data['account_id']) {
			$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_accounts SET `ebay_connector_store_name` = '".$this->db->escape($data['ebay_connector_store_name'])."', `ebay_connector_ebay_sites` = '".(int)$data['ebay_connector_ebay_sites']."', `ebay_connector_ebay_user_id` = '".$this->db->escape($data['ebay_connector_ebay_user_id'])."', `ebay_connector_ebay_auth_token` = '".$this->db->escape($data['ebay_connector_ebay_auth_token'])."', `ebay_connector_ebay_application_id` = '".$this->db->escape($data['ebay_connector_ebay_application_id'])."', `ebay_connector_ebay_developer_id` = '".$this->db->escape($data['ebay_connector_ebay_developer_id'])."', `ebay_connector_ebay_certification_id` = '".$this->db->escape($data['ebay_connector_ebay_certification_id'])."', `ebay_connector_ebay_currency` = '".$this->db->escape($data['ebay_connector_ebay_currency'])."', `ebay_connector_ebay_shop_postal_code` = '".$this->db->escape($data['ebay_connector_ebay_shop_postal_code'])."',`allowed_ebay_event` = '".$this->db->escape(json_encode($allowed_ebay_events))."' WHERE `id` = '".(int)$data['account_id']."' ");

			$query = $this->db->query("SELECT `id` FROM `" . DB_PREFIX . "wk_ebay_shipping_details` WHERE `account_id` = " .(int)$data['account_id'] . "");

			if ($query->num_rows) {
				$this->db->query("UPDATE `" . DB_PREFIX . "wk_ebay_shipping_details` SET `account_id` = " . (int)$data['account_id'] . ", `shipping_priority` = " . (int)$data['shipping_priority'] . ", `shipping_service` = '" . $this->db->escape($data['shipping_service']) . "', `shipping_cost` = '" . (float)$data['shipping_cost'] . "', `shipping_additional_cost` = '" . (float)$data['shipping_additional_cost'] . "', `shipping_min_time` = " . (int)$data['shipping_min_time'] . ", `shipping_max_time` = " . (int)$data['shipping_max_time'] . ", `free_shipping_status` = " . (int)$data['free_shipping_status'] . " WHERE `id` = " . (int)$query->row['id'] . "");
			} else {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "wk_ebay_shipping_details` SET `account_id` = " . (int)$data['account_id'] . ", `shipping_priority` = " . (int)$data['shipping_priority'] . ", `shipping_service` = '" . $this->db->escape($data['shipping_service']) . "', `shipping_cost` = '" . (float)$data['shipping_cost'] . "', `shipping_additional_cost` = '" . (float)$data['shipping_additional_cost'] . "', `shipping_min_time` = " . (int)$data['shipping_min_time'] . ", `shipping_max_time` = " . (int)$data['shipping_max_time'] . ", `free_shipping_status` = " . (int)$data['free_shipping_status'] . "");
			}

		}else{
			$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_accounts SET `ebay_connector_store_name` = '".$this->db->escape($data['ebay_connector_store_name'])."', `ebay_connector_ebay_sites` = '".(int)$data['ebay_connector_ebay_sites']."', `ebay_connector_ebay_user_id` = '".$this->db->escape($data['ebay_connector_ebay_user_id'])."', `ebay_connector_ebay_auth_token` = '".$this->db->escape($data['ebay_connector_ebay_auth_token'])."', `ebay_connector_ebay_application_id` = '".$this->db->escape($data['ebay_connector_ebay_application_id'])."', `ebay_connector_ebay_developer_id` = '".$this->db->escape($data['ebay_connector_ebay_developer_id'])."', `ebay_connector_ebay_certification_id` = '".$this->db->escape($data['ebay_connector_ebay_certification_id'])."', `ebay_connector_ebay_currency` = '".$this->db->escape($data['ebay_connector_ebay_currency'])."', `ebay_connector_ebay_shop_postal_code` = '".$this->db->escape($data['ebay_connector_ebay_shop_postal_code'])."',`allowed_ebay_event` = '".$this->db->escape(json_encode($allowed_ebay_events))."' ");

			$Id = $this->db->getLastId();

			$this->db->query("INSERT INTO `" . DB_PREFIX . "wk_ebay_shipping_details` SET `account_id` = " . (int)$Id . ", `shipping_priority` = " . (int)$data['shipping_priority'] . ", `shipping_service` = '" . $this->db->escape($data['shipping_service']) . "', `shipping_cost` = '" . (float)$data['shipping_cost'] . "', `shipping_additional_cost` = '" . (float)$data['shipping_additional_cost'] . "', `shipping_min_time` = " . (int)$data['shipping_min_time'] . ", `shipping_max_time` = " . (int)$data['shipping_max_time'] . ", `free_shipping_status` = " . (int)$data['free_shipping_status'] . "");
		}

		$event = array();

		$event['id'] = isset($data['id']) && $data['id'] ? $data['id'] : (isset($Id) && $Id ? $Id :1);

		$event['events'] = $allowed_ebay_events;

		$this->load->controller('ebay_map/ebay_events' ,$event);

	}

	public function getShippingDetails($account_id) {
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_ebay_shipping_details` WHERE `account_id` = " . (int)$account_id . "")->row;
	}

	/**
	 * [deleteAccount to delete the ebay account]
	 * @param  boolean $account_id [ebay account id]
	 * @return [type]              [description]
	 */
	public function deleteAccount($account_id = false){
			if($account_id){
				$this->load->model('catalog/product');
				$getAllMappedCategories = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebaysync_categories WHERE account_id = '".(int)$account_id."' ")->rows;
				if(!empty($getAllMappedCategories)){
						foreach ($getAllMappedCategories as $key => $category_map) {

								//delete ebay condition entries
								$getCondition = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition WHERE ebay_category_id = '".(int)$category_map['ebay_category_id']."' AND oc_category_id = '".(int)$category_map['opencart_category_id']."' ")->row;

								if(!empty($getCondition)){
										$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_product_to_condition WHERE condition_value_id = '".(int)$getCondition['id']."' ");
										$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_prod_condition_value WHERE condition_value_id = '".(int)$getCondition['id']."' ");
										$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_prod_condition WHERE id = '".(int)$getCondition['id']."' ");
								}

								//delete ebay specification entries
								$getAllCategorySpecifications = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_specification_map WHERE ebay_category_id = '".(int)$category_map['ebay_category_id']."' AND oc_category_id = '".(int)$category_map['opencart_category_id']."' ")->rows;

								if(!empty($getAllCategorySpecifications)){
										foreach ($getAllCategorySpecifications as $key => $specification) {
												$getAttributes = $this->db->query("SELECT * FROM ".DB_PREFIX."attribute a LEFT JOIN ".DB_PREFIX."attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE a.attribute_group_id = '".(int)$specification['attr_group_id']."'  ")->rows;
												if(!empty($getAttributes))
													foreach ($getAttributes as $key => $attributes) {
															$this->db->query("DELETE FROM ".DB_PREFIX."product_attribute WHERE attribute_id = '".(int)$attributes['attribute_id']."' ");
															$this->db->query("DELETE FROM ".DB_PREFIX."attribute_description WHERE attribute_id = '".(int)$attributes['attribute_id']."' ");
															$this->db->query("DELETE FROM ".DB_PREFIX."attribute WHERE attribute_id = '".(int)$attributes['attribute_id']."' ");
													}
													$this->db->query("DELETE FROM ".DB_PREFIX."attribute_group WHERE attribute_group_id = '".(int)$specification['attr_group_id']."' ");
													$this->db->query("DELETE FROM ".DB_PREFIX."attribute_group_description WHERE attribute_group_id = '".(int)$specification['attr_group_id']."' ");
													$this->db->query("DELETE FROM ".DB_PREFIX."wk_specification_map WHERE attr_group_id = '".(int)$specification['attr_group_id']."' ");
										}
								}

								//delete ebay products and orders entries
								if($this->config->get('module_ebay_connector_account_delete')){
									//delete product
										$getAccountProducts = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_oc_product_map WHERE account_id = '".(int)$account_id."' ")->rows;
										if(!empty($getAccountProducts))
											foreach ($getAccountProducts as $key => $product) {
													$this->model_catalog_product->deleteProduct($product['oc_product_id']);
													$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_oc_product_map WHERE account_id = '".(int)$account_id."' AND oc_product_id = '".(int)$product['oc_product_id']."' ");
											}

										$getAccountOrders = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_order_map WHERE account_id = '".(int)$account_id."' ")->rows;
										if(!empty($getAccountOrders))
											foreach ($getAccountOrders as $key => $order) {
													$this->db->query("DELETE FROM ".DB_PREFIX."order_option WHERE order_id = '".(int)$order['oc_order_id']."' ");
													$this->db->query("DELETE FROM ".DB_PREFIX."order_history WHERE order_id = '".(int)$order['oc_order_id']."' ");
													$this->db->query("DELETE FROM ".DB_PREFIX."order_product WHERE order_id = '".(int)$order['oc_order_id']."' ");
													$this->db->query("DELETE FROM ".DB_PREFIX."order WHERE order_id = '".(int)$order['oc_order_id']."' ");
													$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_order_map WHERE account_id = '".(int)$account_id."' AND oc_order_id = '".(int)$order['oc_order_id']."' ");
											}
								}
						}
						$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebaysync_categories WHERE account_id = '".(int)$account_id."' ");
				}
				$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_accounts WHERE id = '".(int)$account_id."' ");
			}
	}

}
