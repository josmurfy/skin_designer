<?php
/**
 * @version [Supported opencart version 3.x.x.x.]
 * @category Webkul
 * @package Opencart eBay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapPriceQtyRule extends Model {
  /**
  * This function is used to add the eBay Price Quantity Rule
  * @param array [$data] array data of parameter to add to database *required
  * @return int [$id] Price Quantity Rule Id ($this->db->getLastId())
  */
  public function addRule($data) {
    $this->db->query("INSERT INTO `" . DB_PREFIX . "wk_ebay_price_qty_rule` SET `rule_for` = '" . $data['rule_for'] . "', `portation` = '" . $data['portation'] . "', `min` = " . (int)$data['min'] . ", `max` = " . (int)$data['max'] . ", `operation_type` = '" . $data['operation_type'] . "', `operation` = '" . $data['operation'] . "', `value` = " . (float)$data['value'] . ", `status` = " . (int)$data['status'] . ", `sort_order` = " . (int)$data['sort_order'] . "");

    return $this->db->getLastId();
  }

  /**
  * This function is used to update the eBay Price Quantity Rule
  * @param array [$data] array data of parameter to add to database
  * @param int [$rule_id] Price Quantity Rule Id
  * @return no return
  */
  public function updateRule($rule_id, $data) {
    $this->db->query("UPDATE `" . DB_PREFIX . "wk_ebay_price_qty_rule` SET `rule_for` = '" . $data['rule_for'] . "', `portation` = '" . $data['portation'] . "', `min` = " . (int)$data['min'] . ", `max` = " . (int)$data['max'] . ", `operation_type` = '" . $this->db->escape($data['operation_type']) . "', `operation` = '" . $data['operation'] . "', `value` = " . (float)$data['value'] . ", `status` = " . (int)$data['status'] . ", `sort_order` = " . (int)$data['sort_order'] . " WHERE `id` = " . (int)$rule_id . "");
  }

  /**
  * This function is used to get the list of Price Quantity Rule
  * @param array [$data] array data for filtering the list
  * @return array [$rows] list of rows of the price quantity rules
  */
  public function getRules($data = array()) {
    $sql = " SELECT * FROM `" . DB_PREFIX . "wk_ebay_price_qty_rule` WHERE 1";
    if (!empty($data['filter_value'])) {
      $sql .= " AND `value` = '" . (float)$data['filter_value'] . "'";
    }
    if (!empty($data['filter_min'])) {
      $sql .= " AND `min` = " . (int)$data['filter_min'];
    }
    if (!empty($data['filter_max'])) {
      $sql .= " AND `max` = " . (int)$data['filter_max'];
    }
    if (!empty($data['filter_rule_for'])) {
      $sql .= " AND `rule_for` = '" . $data['filter_rule_for'] . "'";
    }
    if (!empty($data['filter_portation'])) {
      $sql .= " AND `portation` = '" . $data['filter_portation'] . "'";
    }

    if (!empty($data['filter_operation'])) {
      $sql .= " AND `operation` = '" . $data['filter_operation'] . "'";
    }

    if (!empty($data['filter_operation_type'])) {
      $sql .= " AND `operation_type` = '" . $data['filter_operation_type'] . "'";
    }

    if (isset($data['filter_status']) && $data['filter_status'] != '') {
      $sql .= " AND `status` = " . (int)$data['filter_status'] . "";
    }
    if (!empty($data['filter_sort_order'])) {
      $sql .= " AND `sort_order` = " . (int)$data['filter_sort_order'] . "";
    }

    $sort_data = array(
			'rule_for',
			'min',
			'max',
			'portation',
			'status',
			'operation',
      'portation',
      'value'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY value";
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
  * This function is used to get the total Price Quantity Rule
  * @param array [$data] array data for filtering the list
  * @return int [$rows] total rows of the price quantity rules
  */
  public function getTotalRule($data = array()) {
    $sql = " SELECT COUNT(id) AS total FROM `" . DB_PREFIX . "wk_ebay_price_qty_rule` WHERE 1";
    if (!empty($data['filter_value'])) {
      $sql .= " AND `value` = '" . (float)$data['filter_value'] . "'";
    }
    if (!empty($data['filter_min'])) {
      $sql .= " AND `min` = " . (int)$data['filter_min'];
    }
    if (!empty($data['filter_max'])) {
      $sql .= " AND `max` = '" . $data['filter_max'] . "'";
    }
    if (!empty($data['filter_rule_for'])) {
      $sql .= " AND `rule_for` = '" . $data['filter_rule_for'] . "'";
    }

    if (!empty($data['filter_operation'])) {
      $sql .= " AND `operation` = '" . $data['filter_operation'] . "'";
    }

    if (!empty($data['filter_operation_type'])) {
      $sql .= " AND `filter_operation_type` = '" . $data['filter_operation_type'] . "'";
    }

    if (!empty($data['filter_sort_order'])) {
      $sql .= " AND `sort_order` = " . (int)$data['filter_sort_order'] . "";
    }

    if (isset($data['filter_status']) && $data['filter_status'] != '') {
      $sql .= " AND `status` = " . (int)$data['filter_status'] . "";
    }


    return $this->db->query($sql)->row['total'];
  }

  /**
  * This function is used to get a price quantity rule details
  * @param int [$rule_id]
  * @return array [$data] row data of price quantity rule information
  */
  public function getRule($rule_id) {
    return $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_ebay_price_qty_rule` WHERE `id` = " . (int)$rule_id . "")->row;
  }

  /**
  * This function is used to delete a price quantity rule
  * @param int [$rule_id]
  * @return no return available for this function
  */
  public function deleteRule($rule_id) {
    $this->db->query("DELETE FROM `" . DB_PREFIX . "wk_ebay_price_qty_rule` WHERE `id` = " . (int)$rule_id . "");
  }
}
