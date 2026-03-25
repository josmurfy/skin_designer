<?php
/**
 * @version [Supported opencart version 3.x.x.x.]
 * @category Webkul
 * @package Opencart eBay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapSellerCategory extends Model {
  /**
  * This function is used to add the category after adding it to eBay Store
  * @param array [$data]
  * @return no return type
  */
  public function addEbaySellerCategory($data) {
    if ($data) {

      foreach ($data as $category) {

        $this->db->query("DELETE FROM `" . DB_PREFIX . "wk_ebay_categories` WHERE `ebay_category_id` = " . (int)$category['ebay_category_id'] . "");
        $this->db->query("DELETE FROM `" . DB_PREFIX . "wk_ebay_seller_category` WHERE `ebay_category_id` = " . (int)$category['ebay_category_id'] . "");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "wk_ebay_categories` SET `ebay_category_id` = " . (int)$category['ebay_category_id'] . ", `ebay_category_parent_id` = " . (int)$category['ebay_category_parent_id'] . ", `ebay_category_level` = " . (int)$category['ebay_category_level'] . ", `ebay_category_name` = '" . $this->db->escape($category['ebay_category_name']) . "', `ebay_site_id` = " . (int)$category['ebay_site_id'] . ", `account_id` = " . (int)$category['account_id'] . ", `added_date` = NOW()");

        $this->db->query("INSERT INTO `" . DB_PREFIX . "wk_ebay_seller_category` SET `ebay_category_id` = " . (int)$category['ebay_category_id'] . ", `account_id` = " . (int)$category['account_id'] . "");

      }
    }
  }

  /**
  * This function is used to get the list of ebay seller category
  * @param array [$data]
  * @return array [$data] array data of eBay Seller Category List
  */
  public function getEbaySellerCategories($data = array()) {
    $sql = "SELECT *, wea.id AS account_id, wea.ebay_connector_store_name FROM `" . DB_PREFIX . "wk_ebay_categories` wec LEFT JOIN `" . DB_PREFIX . "wk_ebay_accounts` wea ON (wea.ebay_connector_ebay_sites = wec.ebay_site_id) RIGHT JOIN `" . DB_PREFIX . "wk_ebay_seller_category` wesc ON (wec.ebay_category_id = wesc.ebay_category_id) WHERE 1";

    if (!empty($data['filter_account_id'])) {
      $sql .= " AND wea.id = " . (int)$data['filter_account_id'];
    }

    if (!empty($data['filter_ebay_category_id'])) {
      $sql .= " AND wec.ebay_category_id = " . (int)$data['filter_ebay_category_id'];
    }

    if (!empty($data['filter_ebay_category_name'])) {
      $sql .= " AND wec.ebay_category_name = " . (int)$data['filter_ebay_category_name'];
    }

    if (!empty($data['filter_account_id'])) {
      $sql .= " AND wea.id = " . (int)$data['filter_account_id'];
    }

    if (!empty($data['filter_category_level'])) {
      $sql .= " AND wec.ebay_category_level = " . (int)$data['filter_category_level'];
    }

    if (!empty($data['filter_ebay_site_id'])) {
      $sql .= " AND wec.ebay_site_id = " . (int)$data['filter_ebay_site_id'];
    }

    $sort_data = array(
      'ebay_category_name',
      'wec.ebay_category_id',
      'ebay_category_level'
    );

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
      $sql .= " ORDER BY " . $data['sort'];
    } else {
      $sql .= " ORDER BY ebay_category_name";
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
  * This function is used to get Total of the seller category of the ebay
  * @param array [$data]
  * @return int [count]
  */

  public function getTotalEbaySellerCategory($data = array()) {
    $sql = "SELECT *, wea.id AS account_id, wea.ebay_connector_store_name FROM `" . DB_PREFIX . "wk_ebay_categories` wec LEFT JOIN `" . DB_PREFIX . "wk_ebay_accounts` wea ON (wea.ebay_connector_ebay_sites = wec.ebay_site_id) RIGHT JOIN `" . DB_PREFIX . "wk_ebay_seller_category` wesc ON (wec.ebay_category_id = wesc.ebay_category_id) WHERE 1";

    if (!empty($data['filter_account_id'])) {
      $sql .= " AND wea.id = " . (int)$data['filter_account_id'];
    }

    if (!empty($data['filter_ebay_category_id'])) {
      $sql .= " AND wec.ebay_category_id = " . (int)$data['filter_ebay_category_id'];
    }

    if (!empty($data['filter_ebay_category_name'])) {
      $sql .= " AND wec.ebay_category_name = " . (int)$data['filter_ebay_category_name'];
    }

    if (!empty($data['filter_account_id'])) {
      $sql .= " AND wea.id = " . (int)$data['filter_account_id'];
    }

    if (!empty($data['filter_category_level'])) {
      $sql .= " AND wec.ebay_category_level = " . (int)$data['filter_category_level'];
    }

    if (!empty($data['filter_ebay_site_id'])) {
      $sql .= " AND wec.ebay_site_id = " . (int)$data['filter_ebay_site_id'];
    }

    return $this->db->query($sql)->num_rows;

  }

  /**
  * This function is used to get the list of eBay Categories
  * @param array [$data] to filter the list of category
  * @return array [$data] of category info
  */
  public function getEbayCategories($data = array()) {

    $sql = "SELECT ebay_category_id, ebay_category_name FROM `" . DB_PREFIX . "wk_ebay_categories` WHERE 1";
    if (isset($data['filter_ebay_category_name']) && $data['filter_ebay_category_name']) {
      $sql .= " AND `ebay_category_name` LIKE '" . $this->db->escape($data['filter_ebay_category_name']) . "%'";
    }

    if (isset($data['child']) && $data['child']) {
      $sql .= " AND ebay_category_id != ebay_category_parent_id";
    }

    $sql .= " ORDER BY ebay_category_name ASC LIMIT 0, 8";

    return $this->db->query($sql)->rows;

  }

  /**
  * This function is used to save the custom categories of ebay account
  * @param array [$data] of seller category
  * @return no return
  */
  public function saveSellerCategories($data, $count, $account_id) {
    $this->db->query("INSERT INTO `" . DB_PREFIX . "wk_ebay_seller_category_data` SET `data` = '" . json_encode($data) . "', `count` = " . (int)$count . ", `account_id` = " . (int)$account_id . "");
  }

  public function getSellerCategoryData($account_id) {
    $query = $this->db->query("SELECT `data` FROM `" . DB_PREFIX . "wk_ebay_seller_category_data` WHERE `account_id` = " . (int)$account_id . " ORDER BY `id` DESC");

    return $query->row;
  }

  public function getSiteId($account_id) {
    $query = $this->db->query("SELECT `ebay_connector_ebay_sites` FROM `" . DB_PREFIX . "wk_ebay_accounts` WHERE `id` = " . (int)$account_id . "");
    if ($query->num_rows) {
      return $query->row['ebay_connector_ebay_sites'];
    }
    return 0;
  }
}
