<?php
namespace Opencart\Admin\Model\Shopmanager;


/**
 * @version [3.0.0.0] [Supported opencart version 3.x.x.x]
 * @category Webkul
 * @package Marketplace eBay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

class Connect extends \Opencart\System\Engine\Model {


	/**
	 * [getMarketplaceAccount to get Marketplace Account list or particular account details]
	 * @param  array  $data [filter data array]
	 * @return [type]       [list of ebay accounts]
	 */
	public function getAccountConnect($data = array(), $type = false) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "seller_connect_accounts` WHERE `seller_id` = " . (int)$this->customer->getId() . "";

		if (!empty($data['filter_account_id'])) {
			$sql .= " AND `id` = " . (int)$data['filter_account_id'] . "";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND `store_name` LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}else if (!empty($data['filter_store_name']) && $type) {
			$sql .= " AND `store_name` = '" . $this->db->escape($data['filter_store_name']) . "'";
		}

		if (!empty($data['filter_ebay_user_id'])) {
			$sql .= " AND user_id LIKE '%" . $this->db->escape($data['filter_ebay_user_id']) . "%'";
		}
		if (!empty($data['filter_marketplace_id'])) {
			$sql .= " AND marketplace_id LIKE '%" . $this->db->escape($data['filter_marketplace_id']) . "%'";
		}
		$sort_data = array(
			'id',
			'store_name',
			'user_id',
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
	 * [getTotalAccountConnect to get the total number of ebay account]
	 * @param  array  $data [filter data array]
	 * @return [type]       [total number of ebay account records]
	 */
	public function getTotalAccountConnect($data = array()) {
		$sql = "SELECT COUNT(DISTINCT id) AS total FROM " . DB_PREFIX . "seller_connect_accounts WHERE `seller_id` = " . (int)$this->customer->getId() . "";

		if (!empty($data['filter_account_id'])) {
			$sql .= " AND `id` = " . (int)$data['filter_account_id'] . "";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND `store_name` LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}

		if (!empty($data['filter_ebay_user_id'])) {
			$sql .= " AND `user_id` LIKE '%" . $this->db->escape($data['filter_ebay_user_id']) . "%'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	/**
	 * [addMarketplaceAccount to add/update the ebay account details]
	 * @param  array  $data [details of ebay account]
	 * @return [type]       [description]
	 */
	
	public function deleteAccount($account_id = false) {
		if ($account_id) {
			$this->db->query("DELETE FROM ".DB_PREFIX."seller_connect_accounts WHERE id = '".(int)$account_id."' "); 
		}
	}
}
