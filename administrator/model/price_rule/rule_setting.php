<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelPriceRuleRuleSetting extends Model {

  public function managePriceRulesSetting($db_value, $post_value){
        if($db_value) {
          $this->_managePriceRuleOnExport();
        } else {
          $this->_managePriceRuleOnImport();
        }
  }

  public function _managePriceRuleOnExport(){
       $rules = $this->_getRuleBySource('ebay');
       if(!empty($rules)) {
         foreach ($rules as $key => $rule) {
           $getPrice = $this->_getPrice($rule['rule_product_id']);

            if($rule['change_type']) {
              $getPrice -= $rule['change_price'];
            } else {
              $getPrice += $rule['change_price'];
            }
           $status = $this->_updatePrice($rule['rule_product_id'],$getPrice);
           if($status) { //if Updated the price of the product on the opencart store
             $this->_deleteRuleById($rule['rule_product_id']);
           }
         }
       }
  }

  public function _managePriceRuleOnImport(){
      $rules = $this->_getRuleBySource('opencart');
       $this->load->model('ebay_map/ebay_map_product');
      if(!empty($rules)) {
        foreach ($rules as $key => $rule) {
          $getProduct = $this->_getProduct($rule['rule_product_id']);
          $getProduct['isConfigChange'] = 1; // This variable is used to check if __reviseEbayItem is called from product page or from ebay module config page

          if($rule['change_type']) {
            $getProduct['price'] += $rule['change_price'];
          } else {
            $getProduct['price'] -= $rule['change_price'];
          }
          $status =  $this->model_ebay_map_ebay_map_product->__reviseEbayItem($rule['rule_product_id'],$getProduct);
         //if Updated the price of the product on the opencart store
          $this->_deleteRuleById($rule['rule_product_id']);
        }
      }
  }

  public function _getRuleBySource($source){
       $allEnableRulesBySource = $this->db->query("SELECT * FROM ".DB_PREFIX."ebay_price_rules_map_product WHERE source = '".$this->db->escape($source)."'");
       return $allEnableRulesBySource->rows;

  }

  public function _updatePrice($product_id,$price){
       $status = $this->db->query("UPDATE ".DB_PREFIX."product set price = ".(int)$price." WHERE product_id = ".(int)$product_id."");
       return $status;
  }

  public function _deleteRuleById($product_id){
       $status = $this->db->query("DELETE FROM ".DB_PREFIX."ebay_price_rules_map_product WHERE rule_product_id = ".(int)$product_id."");
       return $status;
  }

  public function _getPrice($product_id){
       $allEnableRulesBySource = $this->db->query("SELECT price FROM ".DB_PREFIX."product WHERE product_id = ".(int)$product_id."");
       return $allEnableRulesBySource->row['price'];

  }

  public function _getProduct($product_id){
    $query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

    return $query->row;

  }


}
