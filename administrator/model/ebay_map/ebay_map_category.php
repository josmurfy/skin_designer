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

	public function getTotalMapCategories($data = array()){
		$sql = "SELECT COUNT(DISTINCT wk_eSync.ebay_category_id) as total FROM ".DB_PREFIX."wk_ebaysync_categories wk_eSync LEFT JOIN ".DB_PREFIX."wk_ebay_categories eCat ON (wk_eSync.ebay_category_id = eCat.ebay_category_id) LEFT JOIN ".DB_PREFIX."category c ON(wk_eSync.opencart_category_id = c.category_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(wk_eSync.opencart_category_id = cd.category_id) WHERE c.status = '1' AND cd.language_id = '".(int)$this->config->get('config_language_id')."'  ";

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

		$result = $this->db->query($sql)->row;
		return $result['total'];
	}

	public function get_OpencartCategories($data = array()){

		$sql = "SELECT * FROM ".DB_PREFIX."category oc_cat LEFT JOIN ".DB_PREFIX."category_description oc_cat_desc ON(oc_cat.category_id = oc_cat_desc.category_id) WHERE oc_cat.status = '1' AND oc_cat_desc.language_id = '".(int)$this->config->get('config_language_id')."' ";

		if(!empty($data['filter_parent_id'])){
			$sql .= " AND oc_cat.parent_id = '".(int)$data['filter_parent_id']."' ";
		}else{
			$sql .= " AND oc_cat.parent_id = '0' ";
		}

		if(!empty($data['filter_category_id'])){
			$sql .= " AND oc_cat.category_id = '".(int)$data['filter_category_id']."' ";
		}
		$results = $this->db->query($sql)->rows;

		return $results;
	}

	public function get_EbayCategories($data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."wk_ebay_categories WHERE 1 ";

		if(!empty($data['filter_parent_id'])){
			$sql .= " AND ebay_category_parent_id = '".(int)$data['filter_parent_id']."' AND ebay_category_id <> '".(int)$data['filter_parent_id']."' ";
		}else if(isset($data['filter_parent_id']) && !$data['filter_parent_id']){
			$sql .= " AND (ebay_category_id = ebay_category_parent_id) ";
		}

		if(!empty($data['filter_ebay_category_name'])){
			$getLastCategory = explode("&gt;",$data['filter_ebay_category_name']);
			$sql .= " AND LCASE(ebay_category_name) LIKE '%".$this->db->escape(strtolower(trim(end($getLastCategory))))."%' ";
		}

		if(!empty($data['filter_row_id'])){
			$sql .= " AND id = '".(int)$data['filter_row_id']."' ";
		}

		if(!empty($data['filter_category_id'])){
			$sql .= " AND ebay_category_id = '".(int)$data['filter_category_id']."' ";
		}

		if(!empty($data['filter_category_level'])){
			$sql .= " AND ebay_category_level = '".(int)$data['filter_category_level']."' ";
		}

		if(isset($data['filter_ebay_site_id'])){
			$sql .= " AND ebay_site_id = '".(int)$data['filter_ebay_site_id']."' ";
		}

		$sort_data = array(
			'ebay_category_id',
			'ebay_category_name',
			'ebay_category_level'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY ebay_category_id";
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

		$results = $this->db->query($sql)->rows;

		return $results;
	}

	public function validateBothCategory($data = array(), $status = 'opencart'){
		$record_found = array();
		if(!empty($data['opencart_category']) && $status == 'opencart'){
			$result_opencart = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebaysync_categories cat_sync LEFT JOIN ".DB_PREFIX."category c ON(cat_sync.opencart_category_id = c.category_id) WHERE cat_sync.opencart_category_id = '".(int)$data['opencart_category']."' AND cat_sync.account_id = '".(int)$data['account_id']."' AND c.category_id = '".(int)$data['opencart_category']."' AND c.status = '1'")->row;

			if(!empty($result_opencart)){
				$record_found['opencart'] = true;
			}
		}

		if(!empty($data['ebay_category']) && $status == 'ebay'){
			$result_ebay = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebaysync_categories WHERE ebay_category_id = '".$this->db->escape($data['ebay_category'])."' AND account_id = '".(int)$data['account_id']."' ")->row;

			if(!empty($result_ebay)){
				$record_found['ebay'] = true;
			}
		}
		return $record_found;
	}

	public function map_category($data = array()){
		$this->load->language('ebay_map/ebay_map_category');

		if($data['account_id']){
			$data['account_id'] = $data['account_id'];
		}else{
			$data['account_id'] = 0;
		}

		$getEbayClient 		= $this->Ebayconnector->_eBayAuthSession($data['account_id']);

		if((isset($data['ebay_category']) && $data['ebay_category']) && (isset($data['opencart_category']) && $data['opencart_category']) ){
			$getOcCategory = $this->db->query("SELECT * FROM ".DB_PREFIX."category c LEFT JOIN ".DB_PREFIX."category_description cd ON(c.category_id = cd.category_id) WHERE cd.language_id = '".(int)$this->config->get('config_language_id')."' AND c.category_id = '".(int)$data['opencart_category']."' ")->row;
			/**
			 * get ebay category info from oc DB
			 */
			$getEbayCategoryInfo = $this->get_EbayCategories(array('filter_category_id' => $data['ebay_category']));

 			if(isset($getEbayCategoryInfo[0]['ebay_category_name']) && $getEbayCategoryInfo[0]['ebay_category_name'] && isset($getOcCategory['name'])){

 				$ebay_category_name = $getEbayCategoryInfo[0]['ebay_category_name'];

 				/**
				 * [$arguments get ebay category's features(variations) and conditions]
				 * @var [type]
				 */
				$arguments = array('Version' 				=> 1039,
													'DetailLevel' 		=> 'ReturnAll',
	                        'WarningLevel' 		=> 'High',
	                        'CategoryID' 			=> $data['ebay_category'],
	                        'AllFeaturesForCategory' => true,
	                        'ViewAllNodes' 		=> true,
	                      );
	      $get_EbayCat_Features = $getEbayClient->GetCategoryFeatures($arguments);

				$VariationsEnabled 	= false;
				$ConditionAttribute = 'N/A';

	        	if(isset($get_EbayCat_Features->Ack) && $get_EbayCat_Features->Ack == 'Success'){
	        		if(isset($get_EbayCat_Features->Category->VariationsEnabled)){
	        			$VariationsEnabled = $get_EbayCat_Features->Category->VariationsEnabled;
	        		}

	        		/**
	        		 * save ebay product condition(like marketplace custom fields) as category
	        		 */
	        		if(isset($get_EbayCat_Features->Category->ConditionValues)){
	        			$ConditionAttribute = $this->__createProductConditionAttribute($get_EbayCat_Features->Category->ConditionValues, $getEbayCategoryInfo[0], $data['opencart_category']);
	        		}
							/**
							 * get specification(attribute) of category form eBay sites
							 */
                $arguments = array(
                  'CategorySpecific' 	=> array('CategoryID' => $data['ebay_category']),
                  'MaxValuesPerName' 	=> 2147483647,
                  'Version' 					=> 1039,
                );

                $get_EbayCat_Specification = $getEbayClient->GetCategorySpecifics($arguments);

                if(isset($get_EbayCat_Specification->Ack) && $get_EbayCat_Specification->Ack == 'Success' && isset($get_EbayCat_Specification->Recommendations->NameRecommendation)){

	                	$this->load->model('catalog/attribute');
						        $this->load->model('catalog/attribute_group');
						        $this->load->model('localisation/language');

										foreach ($get_EbayCat_Specification->Recommendations->NameRecommendation as $key => $ebay_specification) {

											if(isset($ebay_specification->ValueRecommendation)){
												$this->_saveEbaySpecification(array('ebay_category_id' => $data['ebay_category'], 'ebay_category_name' => $ebay_category_name, 'oc_category_id' => $data['opencart_category'], 'oc_category_name' => $getOcCategory['name']), $ebay_specification);
											}
										}
								}

					/**
					 * save the mapping of ebay and opencart
					 */
					$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebaysync_categories SET `opencart_category_id` = '".(int)$data['opencart_category']."', `ebay_category_id` = '".(int)$data['ebay_category']."', `ebay_category_name` = '".$this->db->escape($ebay_category_name)."', `pro_condition_attr` = '".$this->db->escape($ConditionAttribute)."', `variations_enabled` = '".$VariationsEnabled."', `added_date` = NOW(), `account_id` = '".(int)$data['account_id']."' ");

					$this->session->data['success'] = $result['success'] = $this->language->get('text_success_map_category');
					$result['redirect'] 				= html_entity_decode($this->url->link('ebay_map/ebay_account/edit', 'user_token=' . $this->session->data['token'] . '&status=account_category_map&account_id=' .$data['account_id'] , true));

	           	}else{
	           		$result['warning'] = $this->language->get('error_ebay_connection');
	           	}
 			}else{
 				$result['warning'] = $this->language->get('error_ebay_category_import');
 			}
		}else{
			$result['warning'] = $this->language->get('error_ebay_category_select');
		}
		return $result;
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


    private function _convertEbayConditionInArray($ebayCondition = array())
    {
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

    public function __getEbay_ConditionInfo($condition_code = false){
    	$result = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition WHERE condition_attr_code = '".$this->db->escape($condition_code)."' ")->row;

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

    public function _saveEbaySpecification($category_details = array(), $ebay_specification){
	    	$specificationValue = $attribute_grp = array();
				$catCode						= '';
				$catCode 						= str_replace(' ', '_', $category_details['oc_category_name']);
				$catCode 						= preg_replace('/[^A-Za-z0-9\_]/', '', $catCode);
				$ocCatCode 					= substr(strtolower($catCode), 0, 10);

				$attributeCode 			= str_replace(' ', '_', $ebay_specification->Name);
				$attributeCode 			= preg_replace('/[^A-Za-z0-9\_]/', '', $attributeCode);
				$ocSpecifCode 			= substr('ebay_'.$ocCatCode.'_'.strtolower($attributeCode), 0, 45);
				$specification_Id 	= false;

        $AllLanguages = $this->model_localisation_language->getLanguages();

        $checkcoSpecfEntry = $this->__getEbay_SpecificationInfo($ocSpecifCode, $category_details['oc_category_id']);

        if(!isset($checkcoSpecfEntry['attr_group_id'])){
        	foreach ($AllLanguages as $key => $language) {
        		$attribute_grp['attribute_group_description'][$language['language_id']] = array('name' => $ebay_specification->Name);
        		$attribute_grp['sort_order'] = 0;
        	}

        	// save attribute group
        	$arribute_grp_id = $this->model_catalog_attribute_group->addAttributeGroup($attribute_grp);
        	if($arribute_grp_id){
						$required = 0;
						if(isset($ebay_specification->ValidationRules->MinValues)){
								$required = 1;
						}
        		$this->db->query("INSERT INTO ".DB_PREFIX."wk_specification_map SET `attr_group_id` = '".(int)$arribute_grp_id."', `valuetype` = '".$this->db->escape($ebay_specification->ValidationRules->ValueType)."', `required` = '".(int)$required."', `oc_category_id` = '".(int)$category_details['oc_category_id']."', `ebay_category_id` = '".(int)$category_details['ebay_category_id']."', `ebay_category_name` = '".$this->db->escape($category_details['ebay_category_name'])."', `ebay_specification_code` = '".$this->db->escape($ocSpecifCode)."' ");

        		$specification_id = $this->db->getLastId();

						$specification_array = array();
						if(isset($ebay_specification->ValueRecommendation->Value)){
								$specification_array[0] =  $ebay_specification->ValueRecommendation;
						}else{
								$specification_array =  $ebay_specification->ValueRecommendation;
						}

        		foreach ($specification_array as $key => $specification_value) {
        			if(isset($specification_value->Value)){
        				$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$arribute_grp_id . "', sort_order = '0'");

        				$attribute_id = $this->db->getLastId();

		        		foreach ($AllLanguages as $key => $language) {
		        			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language['language_id'] . "', name = '" . $this->db->escape($specification_value->Value) . "'");
			        	}
	        		}
	        	}
        	}
        }else{
        	$getSpeificationValues = $this->model_catalog_attribute->getAttributes(array('filter_attribute_group_id' => $checkcoSpecfEntry['attr_group_id']));

        	if(isset($getSpeificationValues) && $getSpeificationValues){
        		foreach ($getSpeificationValues as $key => $old_SpecificationValue) {
	        		array_push($specificationValue, $old_SpecificationValue['name']);
	        	}
        	}

        	foreach ($ebay_specification->ValueRecommendation as $key => $specification_value) {
        		if(isset($specification_value->Value) && !in_array($specification_value->Value, $specificationValue) && $specification_value->Value != ''){

        			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$checkcoSpecfEntry['attr_group_id'] . "', sort_order = '0'");
    				$attribute_id = $this->db->getLastId();

	        		foreach ($AllLanguages as $key => $language) {
	        			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language['language_id'] . "', name = '" . $this->db->escape($specification_value->Value) . "'");
		        	}
        		}
        	}
        }
        return $ocSpecifCode;
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

    public function deleteMapCategory($map_category_id = false){
    	if($map_category_id){
    		$getMapCat = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebaysync_categories WHERE id = '".(int)$map_category_id."'")->row;

    		if(!empty($getMapCat)){
    			// get conditions based on category and delete them
    			$getCatCond = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition WHERE oc_category_id = '".(int)$getMapCat['opencart_category_id']."' AND ebay_category_id = '".$getMapCat['ebay_category_id']."' ")->row;
    			if(!empty($getCatCond)){
    				$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_product_to_condition WHERE oc_category_id = '".(int)$getCatCond['oc_category_id']."' ");

    				$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_prod_condition_value WHERE condition_value_id = '".(int)$getCatCond['id']."' ");

    				$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_prod_condition WHERE id = '".(int)$getCatCond['id']."' ");
    			}

    			//get specification(attribute and attribute group) based on category and delete them all
    			$getSpecification = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_specification_map WHERE oc_category_id = '".(int)$getMapCat['opencart_category_id']."' AND ebay_category_id = '".$getMapCat['ebay_category_id']."' ")->rows;

    			if(isset($getSpecification) && $getSpecification){

    				foreach ($getSpecification as $key => $specification) {
    					$getAttributes = $this->db->query("SELECT * FROM ".DB_PREFIX."attribute WHERE attribute_group_id = '".(int)$specification['attr_group_id']."' ")->rows;
    					//delete attribute
    					if(!empty($getAttributes)){
    						foreach ($getAttributes as $key => $attribute) {
    							$this->db->query("DELETE FROM ".DB_PREFIX."product_attribute WHERE attribute_id = '".(int)$attribute['attribute_id']."' ");

    							$this->db->query("DELETE FROM ".DB_PREFIX."attribute_description WHERE attribute_id = '".(int)$attribute['attribute_id']."' ");

    							$this->db->query("DELETE FROM ".DB_PREFIX."attribute WHERE attribute_id = '".(int)$attribute['attribute_id']."' AND attribute_group_id = '".(int)$specification['attr_group_id']."' ");
    						}
    					}
    					//delete attribute_group
    					$this->db->query("DELETE FROM ".DB_PREFIX."attribute_group_description WHERE attribute_group_id = '".(int)$specification['attr_group_id']."' ");

    					$this->db->query("DELETE FROM ".DB_PREFIX."attribute_group WHERE attribute_group_id = '".(int)$specification['attr_group_id']."' ");
    				}
    				$this->db->query("DELETE FROM ".DB_PREFIX."wk_specification_map WHERE oc_category_id = '".(int)$getMapCat['opencart_category_id']."' AND ebay_category_id = '".(int)$getMapCat['ebay_category_id']."' ");
    			}
					// delete product mapped entry also
					$getCategoryProducts = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_oc_product_map WHERE oc_category_id = '".(int)$getMapCat['opencart_category_id']."' ")->rows;
					if(!empty($getCategoryProducts)){
							$this->load->model('ebay_map/ebay_map_product');
							foreach ($getCategoryProducts as $key => $mapped_product) {
								$this->model_ebay_map_ebay_map_product->deleteMapProducts(array('map_id' => $mapped_product['id'], 'account_id' => $mapped_product['account_id']));
							}
					}
    			$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebaysync_categories WHERE id = '".(int)$map_category_id."' ");
    		}
    	}

    	return true;
    }

		// ebay category listing code start here
		public function get_EbayCategoryList($data = array()){
			$data_category = array();
			$getParentCategory = $this->get_EbayCategories($data);

					if(!empty($getParentCategory)){
						foreach ($getParentCategory as $key => $ebay_category) {
							$flag_status = true;
							if($ebay_category['ebay_category_parent_id'] != $ebay_category['ebay_category_id']){
								$filter_parent_id = $ebay_category['ebay_category_parent_id'];
								$_ebay_cat_name  = array();
								while ($flag_status) {
										$getChildCategory = array();
										$getChildCategory = $this->get_EbayCategories(array('filter_category_id' => $filter_parent_id, 'filter_ebay_site_id' => $ebay_category['ebay_site_id']));

										if(!empty($getChildCategory) && isset($getChildCategory[0]['ebay_category_id']) && $getChildCategory[0]['ebay_category_id'] && (count($getChildCategory) == 1)){
												array_push($_ebay_cat_name, $getChildCategory[0]['ebay_category_name']);
												$filter_parent_id  = $getChildCategory[0]['ebay_category_parent_id'];
										}
										if(!empty($getChildCategory) && ($getChildCategory[0]['ebay_category_id'] == $getChildCategory[0]['ebay_category_parent_id']) && ($getChildCategory[0]['ebay_site_id'] == $ebay_category['ebay_site_id'])){
											array_unshift($_ebay_cat_name, $ebay_category['ebay_category_name']);
											$makeCorrectArray = array_reverse($_ebay_cat_name);
											$catcat_name 			= implode($makeCorrectArray, ' > ');
											$ebay_category['ebay_category_name'] = $catcat_name;
											$data_category[] 											= $ebay_category;
											break;
										}elseif(empty($getChildCategory)){
												break;
										}
								}
							}else{
								$data_category[] = $ebay_category;
							}
						}
					}
			return $data_category;
		}

		public function get_TotalEbayCategoryList($data = array()){
			$sql = "SELECT COUNT(*) AS total FROM ".DB_PREFIX."wk_ebay_categories WHERE 1 ";

			if(!empty($data['filter_parent_id'])){
				$sql .= " AND ebay_category_parent_id = '".(int)$data['filter_parent_id']."' AND ebay_category_id <> '".(int)$data['filter_parent_id']."' ";
			}else if(isset($data['filter_parent_id']) && !$data['filter_parent_id']){
				$sql .= " AND (ebay_category_id = ebay_category_parent_id) ";
			}

			if(!empty($data['filter_ebay_category_name'])){
				$getLastCategory = explode(" ",$data['filter_ebay_category_name']);
				$sql .= " AND LCASE(ebay_category_name) LIKE '%".$this->db->escape(strtolower(end($getLastCategory)))."%' ";
			}

			if(!empty($data['filter_row_id'])){
				$sql .= " AND id = '".(int)$data['filter_row_id']."' ";
			}

			if(!empty($data['filter_category_id'])){
				$sql .= " AND ebay_category_id = '".(int)$data['filter_category_id']."' ";
			}

			if(!empty($data['filter_category_level'])){
				$sql .= " AND ebay_category_level = '".(int)$data['filter_category_level']."' ";
			}

			if(isset($data['filter_ebay_site_id'])){
				$sql .= " AND ebay_site_id = '".(int)$data['filter_ebay_site_id']."' ";
			}

			$results = $this->db->query($sql)->row;

			return $results['total'];
		}

		public function deleteEbayCategory($data = array()){
			$mapEntryExist = array();
			$getEbayCategoryDetails = $this->get_EbayCategories($data);
			if(!empty($getEbayCategoryDetails) && isset($getEbayCategoryDetails[0]['id']) && $getEbayCategoryDetails[0]['id'] == $data['filter_row_id']){
					$checkMapEntry = $this->getMapCategories(array('filter_ebay_category_id' => $getEbayCategoryDetails[0]['ebay_category_id']));
					if(!empty($checkMapEntry) && (count($checkMapEntry) == 1) && isset($checkMapEntry[0]['ebay_category_id']) && ($checkMapEntry[0]['ebay_category_id'] == $getEbayCategoryDetails[0]['ebay_category_id']) && isset($checkMapEntry[0]['category_id']) && $checkMapEntry[0]['category_id']){
							$mapEntryExist = $checkMapEntry[0];
					}
			}
			return $mapEntryExist;
		}

		public function deleteCategory($id = false){
				$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_categories WHERE id = '".(int)$id."' ");
		}
}
