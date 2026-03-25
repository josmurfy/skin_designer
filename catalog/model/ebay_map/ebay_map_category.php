<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapEbayMapCategory extends Model {
	public function getMapCategories($data = array()) {
		$sql = "SELECT *, wk_eSync.id as map_id FROM ".DB_PREFIX."wk_ebaysync_categories wk_eSync LEFT JOIN ".DB_PREFIX."wk_ebay_categories eCat ON (wk_eSync.ebay_category_id = eCat.ebay_category_id) LEFT JOIN ".DB_PREFIX."category c ON(wk_eSync.opencart_category_id = c.category_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(wk_eSync.opencart_category_id = cd.category_id) WHERE c.status = '1' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' ";

		if(!empty($data['filter_ebay_category_name'])){
			$sql .= " AND wk_eSync.ebay_category_name LIKE '%".$this->db->escape($data['filter_ebay_category_name'])."%' ";
		}

		if(!empty($data['filter_ebay_category_id'])){
			$sql .= " AND wk_eSync.ebay_category_id = '".(int)$data['filter_ebay_category_id']."' ";
		}

		if(!empty($data['filter_oc_category_id'])){
			$sql .= " AND wk_eSync.opencart_category_id = '".(int)$data['filter_oc_category_id']."' ";
		}

		if(!empty($data['filter_oc_category_name'])){
			$sql .= " AND LCASE(cd.name) LIKE '".$this->db->escape(strtolower($data['filter_oc_category_name']))."%' ";
		}

		if (isset($data['filter_variation_type']) && $data['filter_variation_type'] !== '') {
			$sql .= " AND wk_eSync.variations_enabled = '".(int)$data['filter_variation_type']."' ";
		}

		if(!empty($data['filter_account_id'])){
			$sql .= " AND wk_eSync.account_id = '".(int)$data['filter_account_id']."' ";
		}

		$sql .= " GROUP BY c.category_id ORDER BY wk_eSync.id";

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

	public function __createProductConditionAttribute($data = array(), $ebayCategory = array(), $ocCategoryId = false){

		if(!empty($ebayCategory)){
			$conditionValue = array();
			$getConditionArray 			= $this->_convertEbayConditionInArray($data);

		 	$ConditionAttributeCode 	= str_replace(' ', '_', $ebayCategory['ebay_category_name']);
			$ConditionAttributeCode 	= preg_replace('/[^A-Za-z0-9\_]/', '', $ConditionAttributeCode);
			$opencart_Cond_Attr_Code 	= substr('ebay_cat_cond_'.strtolower($ConditionAttributeCode), 0, 30);
			$condition_Attr_Name 			= 'Condition ( '.$ebayCategory['ebay_category_name'].' )';

			$checkCondEntry 			= $this->__getEbay_ConditionInfo($opencart_Cond_Attr_Code);

			//save condition as attribute in opencart default table
			if(!isset($checkCondEntry['id'])){
				$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_prod_condition SET `name` = '".$this->db->escape($condition_Attr_Name)."', `ebay_category_id` = '".(int)$ebayCategory['ebay_category_id']."', `oc_category_id` = '".(int)$ocCategoryId."', `condition_attr_code` = '".$this->db->escape($opencart_Cond_Attr_Code)."' ");

				$condition_attr_id = $this->db->getLastId();

	        	foreach ($getConditionArray as $key => $condition) {
	        		$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_prod_condition_value SET condition_value_id = '".(int)$condition_attr_id."', `condition_id` = '".(int)$condition['condition_id']."', `value` = '".$this->db->escape($condition['condition_name'])."'");
	        	}
			}else{
				$getConditionValues = $this->__getEbay_ConditionValue($checkCondEntry);

	        	if(isset($getConditionValues) && $getConditionValues){
	        		foreach ($getConditionValues as $key => $old_ConditionValue) {
		        		array_push($conditionValue, $old_ConditionValue['value']);
		        	}
	        	}

	        	foreach ($getConditionArray as $key => $condition_value) {
	        		if(!in_array($condition_value['condition_name'], $conditionValue) && $condition_value['condition_name'] != ''){
	        			$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_prod_condition_value SET condition_value_id = '".(int)$checkCondEntry['id']."', `condition_id` = '".(int)$condition_value['condition_id']."', `value` = '".$this->db->escape($condition_value['condition_name'])."'");
	        		}
	        	}
			}
		}
		return $opencart_Cond_Attr_Code;
    }

		public function __getEbay_SpecificationInfo($ebay_specification_code = false, $oc_category_id = false){
			if($oc_category_id){
				$sql = " AND sm.oc_category_id = '".(int)$oc_category_id."' ";
			}else{
				$sql = '';
			}
			$result = $this->db->query("SELECT sm.* FROM ".DB_PREFIX."wk_specification_map sm LEFT JOIN ".DB_PREFIX."attribute_group ag ON(sm.attr_group_id = ag.attribute_group_id) LEFT JOIN ".DB_PREFIX."attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE sm.ebay_specification_code = '".$this->db->escape($ebay_specification_code)."' ".$sql." ")->row;

			return $result;
		}

		public function __getEbay_ConditionValue($data = array()){
			$sql = "SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition_value wk_con_val LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition wk_con ON(wk_con_val.condition_value_id = wk_con.id) WHERE 1 ";

			if(!empty($data['id'])){
				$sql .= " AND wk_con_val.condition_value_id = '".(int)$data['id']."' ";
			}

			$results = $this->db->query($sql)->rows;
			return $results;
		}

		public function __getEbay_ConditionInfo($condition_code = false){
		 $result = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition WHERE condition_attr_code = '".$this->db->escape($condition_code)."' ")->row;

		 return $result;
		}

		private function _convertEbayConditionInArray($ebayCondition = array()) {
				$condition_array = array();

				if (isset($ebayCondition->Condition)) {
						foreach ($ebayCondition->Condition as $key => $condition) {
								$condition_array[$key] = array(
									'condition_id' 		=> $condition->ID,
									'condition_name' 	=> $condition->DisplayName,
									);
						}
				} elseif (is_array($ebayCondition)) {
						$condition_array[] = array(
							'condition_id' 		=> $ebayCondition['condition_id'],
							'condition_name' 	=> $ebayCondition['condition_name'],
							);
				}
				return $condition_array;
		}
}
