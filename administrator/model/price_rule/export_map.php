<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelPriceRuleExportMap extends Model {

    public function _applyPriceRule($params) {

      $price_change = 0 ;
      $newprice = $params['price'];

      $rule_ranges = $this->getPriceRules();

      foreach ($rule_ranges as $key => $rule_range) {

        if(!$this->getMapProduct($params['product_id'])){

          if($this->_validateRuleRange($params['price'], $rule_range['price_from'], $rule_range['price_to'])){

           if($rule_range['price_opration']) { // take the precentage of the price of product
              $price_change += ($params['price'] * $rule_range['price_value']) / 100 ;
           } else{

             $price_change += $rule_range['price_value'];
           }

           if($rule_range['price_type']) { // take the precentage of the price of product
            $newprice = $params['price'] + $price_change ;
           } else{
             $newprice = $params['price'] - $price_change ;
           }

           $this->session->data['changePrices'] = $newprice;

           $updateEntry = array(
             'product_id'     => $params['product_id'],
             'change_type'    => $rule_range['price_type'],
             'price_change'   => $price_change,
             'source'         => 'opencart',
             'rule_id'        => $rule_range['id'],
           );

          $this->addRuleMapProduct($updateEntry);
         }
       }
      }

      if(isset($this->session->data['changePrices'])){
        $newprice = $this->session->data['changePrices'];
        unset($this->session->data['changePrices']);
      }

      return $newprice;
    }

    public function _realtimeUpdatePriceRule($params){

        $this->deleteEntry($params['product_id']);
        $price = $this->_applyPriceRule($params);
        return $price;

    }

    public function _validateRuleRange($price, $min, $max){

      if($price >= $min && $price <= $max) {
        return 1;
      } else {
        return 0;
      }
  	 }

     public function getPriceRules() {

        $sql_query = '';

        $sql_query = "SELECT * FROM " . DB_PREFIX . "ebay_price_rules WHERE price_status = 1";

        $query = $this->db->query($sql_query);

        return $query->rows;
     }

     public function addRuleMapProduct($data) {

        $this->db->query("INSERT INTO ".DB_PREFIX."ebay_price_rules_map_product SET rule_product_id = '" . (int)$data['product_id'] . "', change_price = '" . (float)$data['price_change'] . "',change_type = '" . (int)$data['change_type'] . "', source = '" . $this->db->escape($data['source']) . "',rule_id = '" . (int)$data['rule_id'] . "'");


     }

     public function updateRuleMapProduct($data) {

        $this->db->query("UPDATE ".DB_PREFIX."ebay_price_rules_map_product SET rule_product_id = '" . (int)$data['product_id'] . "', change_price = '" . (float)$data['price_change'] . "',change_type = '" . (int)$data['change_type'] . "', source = '" . $this->db->escape($data['source']) . "' WHERE rule_id = '" . (int)$data['rule_id'] . "'");

     }

     public function getMapProduct($rule_product_id) {
       $price_map_rules = $this->db->query("SELECT rule_product_id FROM " . DB_PREFIX . "ebay_price_rules_map_product WHERE rule_product_id =".(int)$rule_product_id."");

       return $price_map_rules->num_rows;
     }

     public function deleteEntry($product_id) {
       $status = $this->db->query("DELETE FROM " . DB_PREFIX . "ebay_price_rules_map_product WHERE rule_product_id =".(int)$product_id."");
       return $status;
     }

}
