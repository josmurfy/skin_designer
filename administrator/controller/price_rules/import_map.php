<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ControllerPriceRulesImportMap extends Controller {

	public function __construct($registory) {
		parent::__construct($registory);
    $this->load->model('price_rule/import_map');
		$this->_ebayRuleMap = $this->model_price_rule_import_map;
  }

  public function index($params) {
    $price_change = 0 ;
		$newprice = 0;
    $rule_ranges = $this->_ebayRuleMap->getPriceRules();

    foreach ($rule_ranges as $key => $rule_range) {

      if(!$this->_ebayRuleMap->getMapProduct($params['product_id'])){
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

					$updateEntry = array(
						'product_id'     => $params['product_id'],
						'price'      => $newprice,
						'change_type'     => $rule_range['price_type'],
						'price_change'   => $price_change,
						'source'         => 'ebay',
						'rule_id'        => $rule_range['id'],
					);

          $this->_ebayRuleMap->updateRuleMapProduct($updateEntry);
				}
      }
    }

	}
	public function edit($params){

		if($this->_ebayRuleMap->getMapProduct($params['product_id'])) {

         $price_rule = $this->_ebayRuleMap->getPriceRule($params['product_id']);

				 if($price_rule['change_type']){
					 $orgin_price = (float)$params['price'] + (float)$price_rule['change_price'];
				 } else {
					 $orgin_price = (float)$params['price'] - (float)$price_rule['change_price'];
				 }
				 $current_price = (int)$this->_ebayRuleMap->getPrice($params['product_id']);
         $this->_ebayRuleMap->deleteEntry($params['product_id']);
         $this->index($params);
		}
	}

  public function realtime_update($params){

      if($this->_ebayRuleMap->deleteEntry($params['product_id'])){
				$this->index($params);
			}
	}
	public function _validateRuleRange($price, $min, $max){

		if($price >= $min && $price <= $max) {
			return 1;
		} else {
			return 0;
		}
	 }


}
