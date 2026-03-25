<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelPriceRuleImportMap extends Model {

  public function getPriceRules() {

     $sql_query = '';

     $sql_query = "SELECT * FROM " . DB_PREFIX . "ebay_price_rules WHERE price_status = 1";

     $query = $this->db->query($sql_query);

     return $query->rows;
  }

  public function getPriceRule($rule_product_id) {
    $price_map_rules = $this->db->query("SELECT * FROM " . DB_PREFIX . "ebay_price_rules_map_product WHERE rule_product_id =".(int)$rule_product_id."");
    return $price_map_rules->row;
  }

  public function getMapProduct($rule_product_id) {
    $price_map_rules = $this->db->query("SELECT rule_product_id FROM " . DB_PREFIX . "ebay_price_rules_map_product WHERE rule_product_id =".(int)$rule_product_id."");

    return $price_map_rules->num_rows;
  }

  public function updateRuleMapProduct($data) {

    if($this->_upadateProductPrice($data)) {
        $this->db->query("INSERT INTO ".DB_PREFIX."ebay_price_rules_map_product SET rule_product_id = '" . (int)$data['product_id'] . "', change_price = '" . (float)$data['price_change'] . "',change_type = '" . (int)$data['change_type'] . "', source = '" . $this->db->escape($data['source']) . "',rule_id = '" . (int)$data['rule_id'] . "'");
    }
  }

  public function _upadateProductPrice($data) {
    $status = $this->db->query("UPDATE " . DB_PREFIX . "product SET price = '".(float)$data['price']."' WHERE product_id = ".(int)$data['product_id']."");
    return $status;

  }

  public function getPrice($product_id) {
    $price = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id =".(int)$product_id."");
    return $price->row['price'];

  }

  public function deleteEntry($product_id) {
    $status = $this->db->query("DELETE FROM " . DB_PREFIX . "ebay_price_rules_map_product WHERE rule_product_id =".(int)$product_id."");
    return $status;
  }

}
