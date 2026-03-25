<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
 if(file_exists(DIR_APPLICATION.'../vendor/autoload.php')){
   require_once \VQMod::modCheck(DIR_APPLICATION.'../vendor/autoload.php');
 }


Use Ebay\eBaySession;

class Ebayconnector {
	private $customer_id;
	private $firstname;
	private $lastname;

	public function __construct($registry) {
		$this->config 	= $registry->get('config');
		$this->db 		= $registry->get('db');
		$this->request 	= $registry->get('request');
		$this->session 	= $registry->get('session');
	}

	/**
	 * [_getModuleConfiguration to get module config setting]
	 * @param  boolean $ebay_AccountId [ebay account id]
	 * @return [type]                  [array of config data]
	 */
	public function _getModuleConfiguration($ebay_AccountId = false)
	{
		$configData = array();

		if($ebay_AccountId){
			$storeEbayConfiguration = $this->getEbayStoreDetails($ebay_AccountId);
			$configData = array(
				'ebaySites'		=>	$storeEbayConfiguration['ebay_connector_ebay_sites'],
				'ebayMode'		=>	$this->config->get('ebay_connector_ebay_mode'),
				'ebayUserId'	=>	$storeEbayConfiguration['ebay_connector_ebay_user_id'],
				'ebayToken'		=>	$storeEbayConfiguration['ebay_connector_ebay_auth_token'],
				'ebayAppId'		=>	$storeEbayConfiguration['ebay_connector_ebay_application_id'],
				'ebayDevId'		=>	$storeEbayConfiguration['ebay_connector_ebay_developer_id'],
				'ebayCertId'	=>	$storeEbayConfiguration['ebay_connector_ebay_certification_id'],
        'ebayCurrency'=>  $storeEbayConfiguration['ebay_connector_ebay_currency'],
				'ebayPostCode'=>	$storeEbayConfiguration['ebay_connector_ebay_shop_postal_code'],
				'location' 		=> 'https://api.ebay.com/wsapi',
			);
		}else{
			$configData = array(
				'ebaySites'		=>	$this->config->get('ebay_connector_ebay_sites'),
				'ebayMode'		=>	$this->config->get('ebay_connector_ebay_mode'),
				'ebayUserId'	=>	$this->config->get('ebay_connector_ebay_user_id'),
				'ebayToken'		=>	$this->config->get('ebay_connector_ebay_auth_token'),
				'ebayAppId'		=>	$this->config->get('ebay_connector_ebay_application_id'),
				'ebayDevId'		=>	$this->config->get('ebay_connector_ebay_developer_id'),
				'ebayCertId'	=>	$this->config->get('ebay_connector_ebay_certification_id'),
        'ebayCurrency'=>  $this->config->get('config_currency'),
				'ebayPostCode'=>	$this->config->get('ebay_connector_ebay_shop_postal_code'),
				'location' 		=> 'https://api.ebay.com/wsapi',
			);
		}

		if($configData['ebayMode'] == 'sandbox'){
			$configData['location'] = 'https://api.sandbox.ebay.com/wsapi';
		}
		return $configData;
	}

	/**
	 * [_eBayAuthSession to get module config info and create ebay's session and client]
	 * @param  boolean $ebay_AccountId [ebay account id]
	 * @return [type]                  [object of ebay session]
	 */
	public function _eBayAuthSession($ebay_AccountId = false)
    {
    	try {
	        $client = null;
					$eBayConfigData = array();
					if(!isset($ebay_AccountId['ebay_connector_ebay_auth_token'])){
	        		$eBayConfigData = $this->_getModuleConfiguration($ebay_AccountId);
					}
					if($ebay_AccountId && is_array($ebay_AccountId)){
							$eBayConfigData['ebayDevId'] 	= $ebay_AccountId['ebay_connector_ebay_developer_id'];
							$eBayConfigData['ebayAppId'] 	= $ebay_AccountId['ebay_connector_ebay_application_id'];
							$eBayConfigData['ebayCertId'] = $ebay_AccountId['ebay_connector_ebay_certification_id'];
							$eBayConfigData['ebayToken'] 	= $ebay_AccountId['ebay_connector_ebay_auth_token'];
							$eBayConfigData['ebaySites'] 	= $ebay_AccountId['ebay_connector_ebay_sites'];
							$eBayConfigData['location'] 	= 'https://api.ebay.com/wsapi';
							if($this->config->get('ebay_connector_ebay_mode') == 'sandbox'){
								$eBayConfigData['location'] = 'https://api.sandbox.ebay.com/wsapi';
							}
					}

          if ($eBayConfigData) {
						if(file_exists(DIR_APPLICATION.'../vendor/autoload.php')){
							$session = new Ebay\eBaySession(
   								 $eBayConfigData['ebayDevId'],
   								 $eBayConfigData['ebayAppId'],
   								 $eBayConfigData['ebayCertId']
   						 );
   						 $session->token 	   = $eBayConfigData['ebayToken'];
   						 $session->site 		 = $eBayConfigData['ebaySites'];
   						 $session->location  = $eBayConfigData['location'];
   						 $client = new Ebay\eBaySOAP($session);
						}
	        }

	        return $client;
        }catch(Exception $e){
            $this->log->write('Error : '. $e->getMessage());
            return false;
        }
    }

    /**
     * [getConfigDefaultSetting module default config options]
     * @return [type] [description]
     */
    public function getConfigDefaultSetting(){
    	$config_data = [
    		'PayPalEmailAddress' 	=> $this->config->get('ebay_connector_paypal_email'),
            'ListingDuration' => $this->config->get('ebay_connector_listing_duration'),
            'PostalCode' 			=> $this->config->get('ebay_connector_ebay_shop_postal_code'),
            'DispatchTimeMax' => $this->config->get('ebay_connector_dispatch_time'),
            'Country' 				=> $this->config->get('config_country_id'),
            'Currency' 				=> $this->config->get('config_currency'),
            'DefaultOrderStatus' 	=> $this->config->get('ebay_connector_order_status'),
            'DefaultProQty' 	=> $this->config->get('ebay_connector_default_item_quantity'),
            'ShippingDetails' => [
                'ShippingServiceOptions' => [
                	[
                	'ShippingServicePriority' => $this->config->get('ebay_connector_shipping_priority'),
                	'ShippingService' 				=> $this->config->get('ebay_connector_shipping_service'),
                	'ShippingServiceCost' 		=> $this->config->get('ebay_connector_shipping_service_cost'),
                	'ShippingServiceAdditionalCost' => $this->config->get('ebay_connector_shipping_service_add_cost'),
                	'ShippingTimeMin' 				=> $this->config->get('ebay_connector_shipping_min_time'),
                	'ShippingTimeMax' 				=> $this->config->get('ebay_connector_shipping_max_time'),
                	'FreeShipping' 						=> $this->config->get('ebay_connector_shipping_free_status'),
                ]],
            ],
            'ReturnPolicy' => [
                        'ReturnsAcceptedOption' 	=> $this->config->get('ebay_connector_return_policy'),
                        'ReturnsWithinOption' 		=> $this->config->get('ebay_connector_return_days'),
                        'Description' 						=> $this->config->get('ebay_connector_other_info'),
                        'ShippingCostPaidByOption'=> $this->config->get('ebay_connector_pay_by'),
                        ],
        ];

    	return $config_data;
    }

    public function getEbayStoreDetails($ebay_AccountId = false){
    	$result = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_accounts WHERE id = '".(int)$ebay_AccountId."' ")->row;

    	return $result;
    }

	/**
	 * [_getProductSpecification to get all the ebay specification]
	 * @return [type] [description]
	 */
	public function _getProductSpecification($data = array()){
    $eBaySpecifications = array();
    $sql = '';
		if(!empty($data['filter_ebay_category_id'])){
			$sql = " AND sm.ebay_category_id = '".$data['filter_ebay_category_id']."' ";
		}
		$getEbaySpecifications = $this->db->query("SELECT * FROM ".DB_PREFIX."attribute_group ag LEFT JOIN ".DB_PREFIX."attribute_group_description agd ON(ag.attribute_group_id = agd.attribute_group_id) RIGHT JOIN ".DB_PREFIX."wk_specification_map sm ON(ag.attribute_group_id = sm.attr_group_id) WHERE agd.language_id = '".(int)$this->config->get('config_language_id')."' $sql ORDER BY sm.ebay_category_name ASC ")->rows;

		if(isset($getEbaySpecifications) && $getEbaySpecifications){
			foreach ($getEbaySpecifications as $key => $specification) {
				$getConditionsValue = $this->db->query("SELECT * FROM ".DB_PREFIX."attribute a LEFT JOIN ".DB_PREFIX."attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE a.attribute_group_id = ".(int)$specification['attribute_group_id']." AND ad.language_id = '".(int)$this->config->get('config_language_id')."' ")->rows;
				$specification['attributes'] = $getConditionsValue;
				$eBaySpecifications[] = $specification;
			}
		}

		return $eBaySpecifications;
	}

	/**
	 * [_getEbaySpecificationList to get all the ebay specification]
	 * @return [type] [description]
	 */
	public function _getEbaySpecificationList($data = array()){
		$sql = "SELECT a.attribute_id, ad.name as attribute_name, ag.attribute_group_id, agd.name as attribute_group_name, sm.*, cd.name as oc_category_name FROM ".DB_PREFIX."attribute a LEFT JOIN ".DB_PREFIX."attribute_description ad ON (a.attribute_id = ad.attribute_id) LEFT JOIN ".DB_PREFIX."attribute_group ag ON(a.attribute_group_id = ag.attribute_group_id) LEFT JOIN ".DB_PREFIX."attribute_group_description agd ON(ag.attribute_group_id = agd.attribute_group_id) RIGHT JOIN ".DB_PREFIX."wk_specification_map sm ON(ag.attribute_group_id = sm.attr_group_id) LEFT JOIN ".DB_PREFIX."category_description cd ON (sm.oc_category_id = cd.category_id) WHERE ad.language_id = '".(int)$this->config->get('config_language_id')."' AND agd.language_id = '".(int)$this->config->get('config_language_id')."' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' ";

		if(!empty($data['filter_specification_id'])){
			$sql .= " AND a.attribute_id LIKE '".(int)$data['filter_specification_id']."%' ";
		}

		if(!empty($data['filter_specification_name'])){
			$sql .= " AND LCASE(ad.name) LIKE '".$this->db->escape(strtolower($data['filter_specification_name']))."%' ";
		}

		if(!empty($data['filter_specification_group_name'])){
			$sql .= " AND LCASE(agd.name) LIKE '".$this->db->escape(strtolower($data['filter_specification_group_name']))."%' ";
		}

		if(!empty($data['filter_ebay_category_name'])){
			$categoryChild = explode('&gt;', $data['filter_ebay_category_name']);
			$sql .= " AND sm.ebay_category_name LIKE '".$this->db->escape(rtrim(end($categoryChild)),' ')."%' ";
		}

		if(!empty($data['filter_oc_category_name'])){
			$sql .= " AND cd.name LIKE '".$this->db->escape($data['filter_oc_category_name'])."%' ";
		}

		$sort_data = array(
			'a.attribute_id',
			'ad.name',
			'agd.name',
			'sm.ebay_category_name',
			'cd.name',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY a.attribute_id";
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

		return $this->db->query($sql)->rows;
	}

	/**
	 * [_getEbaySpecificationList to get all the ebay specification]
	 * @return [type] [description]
	 */
	public function _getEbaySpecificationListTotal($data = array()){
		$sql = "SELECT COUNT(*) as total FROM ".DB_PREFIX."attribute a LEFT JOIN ".DB_PREFIX."attribute_description ad ON (a.attribute_id = ad.attribute_id) LEFT JOIN ".DB_PREFIX."attribute_group ag ON(a.attribute_group_id = ag.attribute_group_id) LEFT JOIN ".DB_PREFIX."attribute_group_description agd ON(ag.attribute_group_id = agd.attribute_group_id) RIGHT JOIN ".DB_PREFIX."wk_specification_map sm ON(ag.attribute_group_id = sm.attr_group_id) LEFT JOIN ".DB_PREFIX."category_description cd ON (sm.oc_category_id = cd.category_id) WHERE ad.language_id = '".(int)$this->config->get('config_language_id')."' AND agd.language_id = '".(int)$this->config->get('config_language_id')."' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' ";

		if(!empty($data['filter_specification_id'])){
			$sql .= " AND a.attribute_id LIKE '".(int)$data['filter_specification_id']."%' ";
		}

		if(!empty($data['filter_specification_name'])){
			$sql .= " AND LCASE(ad.name) LIKE '".$this->db->escape(strtolower($data['filter_specification_name']))."%' ";
		}

		if(!empty($data['filter_specification_group_name'])){
			$sql .= " AND LCASE(agd.name) LIKE '".$this->db->escape(strtolower($data['filter_specification_group_name']))."%' ";
		}

		if(!empty($data['filter_ebay_category_name'])){
			$categoryChild = explode('&gt;', $data['filter_ebay_category_name']);

			$sql .= " AND sm.ebay_category_name = '".$this->db->escape(end($categoryChild))."' ";
		}

		if(!empty($data['filter_oc_category_name'])){
			$sql .= " AND cd.name LIKE '".$this->db->escape($data['filter_oc_category_name'])."%' ";
		}

		$result = $this->db->query($sql)->row;

		return $result['total'];
	}

	public function _getSpecificationFilter($data = array(), $filter = false){
		$results = array();
		$sql = '';
		switch ($filter) {
			case 'filter_specification_name':
						if(!empty($data['filter_specification_name'])){
								$sql = " AND ad.name LIKE '".$this->db->escape($data['filter_specification_name'])."%' ";
						}
						$results = $this->db->query("SELECT DISTINCT ad.name, ad.attribute_id as filter_id FROM ".DB_PREFIX."attribute a LEFT JOIN ".DB_PREFIX."attribute_description ad ON (a.attribute_id = ad.attribute_id) LEFT JOIN ".DB_PREFIX."attribute_group ag ON(a.attribute_group_id = ag.attribute_group_id) LEFT JOIN ".DB_PREFIX."attribute_group_description agd ON(ag.attribute_group_id = agd.attribute_group_id) RIGHT JOIN ".DB_PREFIX."wk_specification_map sm ON(ag.attribute_group_id = sm.attr_group_id) WHERE ad.language_id = '".(int)$this->config->get('config_language_id')."' AND agd.language_id = '".(int)$this->config->get('config_language_id')."' ".$sql." ORDER BY ad.name ASC LIMIT 5 ")->rows;
				break;

			case 'filter_specification_group_name':
						if(!empty($data['filter_specification_group_name'])){
								$sql = " AND LCASE(agd.name) LIKE '".$this->db->escape(strtolower($data['filter_specification_group_name']))."%' ";
						}
						$results = $this->db->query("SELECT DISTINCT agd.name, agd.attribute_group_id  as filter_id FROM ".DB_PREFIX."attribute_group_description agd LEFT JOIN ".DB_PREFIX."wk_specification_map sm ON(agd.attribute_group_id = sm.attr_group_id) WHERE agd.language_id = '".(int)$this->config->get('config_language_id')."' ".$sql." ORDER BY agd.name ASC LIMIT 5 ")->rows;
				break;

			case 'filter_oc_category_name':
						if(!empty($data['filter_oc_category_name'])){
								$sql = " AND LCASE(cd.name) LIKE '".$this->db->escape(strtolower($data['filter_oc_category_name']))."%' ";
						}
						$results = $this->db->query("SELECT DISTINCT cd.name, cd.category_id as filter_id FROM ".DB_PREFIX."category_description cd LEFT JOIN ".DB_PREFIX."wk_specification_map sm ON(cd.category_id = sm.oc_category_id) WHERE cd.language_id = '".(int)$this->config->get('config_language_id')."' ".$sql." ORDER BY cd.name ASC LIMIT 5 ")->rows;
				break;

			case 'filter_ebay_category_name':
						if(!empty($data['filter_ebay_category_name'])){
								$sql = " AND LCASE(ec.ebay_category_name) LIKE '".$this->db->escape(strtolower($data['filter_ebay_category_name']))."%' ";
						}
						$results = $this->db->query("SELECT DISTINCT ec.ebay_category_name as name, ec.ebay_category_id as filter_id FROM ".DB_PREFIX."wk_ebay_categories ec WHERE 1 ".$sql." ORDER BY ec.ebay_category_name ASC LIMIT 5 ")->rows;
				break;

			default:
				$results;
				break;
		}
		return $results;
	}

	public function deleteEbaySpecification($filter_row_id = false){
		$deleteStatus = false;
		if($filter_row_id){
				$getAttributeId = explode('_', $filter_row_id);
				if(isset($getAttributeId[1])){
					$this->db->query("DELETE FROM ".DB_PREFIX."product_attribute WHERE attribute_id = '".(int)$getAttributeId[1]."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."attribute_description WHERE attribute_id = '".(int)$getAttributeId[1]."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."attribute WHERE attribute_id = '".(int)$getAttributeId[1]."' ");
					$deleteStatus = true;
				}
		}
		return $deleteStatus;
	}

	/**
	 * [getProductSpecification to get opencart product specification]
	 * @param  boolean $product_id [description]
	 * @return [type]              [description]
	 */
	public function getProductSpecification($product_id = false){
		$productSpecification = array();

		$getOcProductSpecification = $this->db->query("SELECT * FROM ".DB_PREFIX."product_attribute pa LEFT JOIN ".DB_PREFIX."attribute a ON(pa.attribute_id = a.attribute_id) LEFT JOIN ".DB_PREFIX."attribute_group ag ON(a.attribute_group_id = ag.attribute_group_id) RIGHT JOIN ".DB_PREFIX."wk_specification_map sm ON(a.attribute_group_id = sm.attr_group_id) WHERE pa.product_id = '".(int)$product_id."' ")->rows;
		if(!empty($getOcProductSpecification)){
			foreach ($getOcProductSpecification as $key => $attribute) {
				$productSpecification[$attribute['attribute_group_id']] = $attribute['attribute_id'];
			}
		}
		return $productSpecification;
	}
	/**
	 * [checkSpecificationEntry to get only the ebay specification [used to hide the specification for opencart attribute]]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function checkSpecificationEntry($data = array()){
		$sql = "SELECT a.*, ag.*, agd.name as group_name FROM ".DB_PREFIX."attribute a LEFT JOIN ".DB_PREFIX."attribute_group ag ON(a.attribute_group_id = ag.attribute_group_id) LEFT JOIN ".DB_PREFIX."attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE a.attribute_group_id IN (SELECT attr_group_id FROM ".DB_PREFIX."wk_specification_map) ";

		if(isset($data['attribute_id'])){
			$sql .= " AND a.attribute_id = '".(int)$data['attribute_id']."'";
		}

		if(isset($data['attribute_group_id'])){
			$sql .= " AND a.attribute_group_id = '".(int)$data['attribute_group_id']."' AND ag.attribute_group_id = '".(int)$data['attribute_group_id']."' ";
		}

		$getSpecificationEntry = $this->db->query($sql)->row;

		return $getSpecificationEntry;
	}

	/**
     * [_getProductConditions to get all ebay category specification for products]
     * @return [type] [description]
     */
    public function _getProductConditions(){
    	$eBayConditions = array();
    	$getConditions = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition ")->rows;

    	if(!empty($getConditions) && isset($getConditions) && $getConditions){

    		foreach ($getConditions as $key => $condition) {
    			$getConditionsValue = $this->db->query("SELECT pcv.* FROM ".DB_PREFIX."wk_ebay_prod_condition_value pcv LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition pc ON (pcv.condition_value_id = pc.id) WHERE pcv.condition_value_id = ".(int)$condition['id']." AND pc.id = ".(int)$condition['id']." ")->rows;
    			$condition['condition_values'] = $getConditionsValue;
    			$eBayConditions[] = $condition;
    		}
    	}
    	return $eBayConditions;
    }

	/**
	 * [getEbayCondition to get the entry of ebay condition [format: 'cond_value_id'_'condition_id']]
	 * @param  boolean $condition_condValue [description]
	 * @return [type]                       [description]
	 */
	public function getEbayCondition($condition_condValue = false){
		$result = array();
		if($condition_condValue){
			$getCondition = explode('_', $condition_condValue);
			if(isset($getCondition[0]) && isset($getCondition[1])){
				$result = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition_value pcv LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition pc ON(pcv.condition_value_id = pc.id) WHERE pcv.condition_id = '".(int)$getCondition[1]."' AND pcv.condition_value_id = '".(int)$getCondition[0]."' AND pc.id = '".(int)$getCondition[0]."'")->row;
			}
		}
		return $result;
	}

	public function getProductCondition($product_id = false){
		$productCondition = array();

		$getOcProductCondition = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_product_to_condition p2c LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition_value cv ON((p2c.condition_value_id = cv.condition_value_id) AND (p2c.condition_id = cv.condition_id)) LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition pc ON(p2c.condition_value_id = pc.id) WHERE p2c.oc_product_id = '".(int)$product_id."' ")->rows;

		if(!empty($getOcProductCondition)){
			foreach ($getOcProductCondition as $key => $condition) {
				$productCondition[$condition['condition_value_id']] = $condition['condition_value_id'].'_'.$condition['condition_id'];
			}
		}
		return $productCondition;
	}
	/**
	 * [getAllEbayCondition to get all the ebay conditions for Condition Listing Left Menu]
	 * @param  boolean $condition_condValue [description]
	 * @return [type]                       [description]
	 */
	public function getAllEbayCondition($data = array()){
		$sql = "SELECT pcv.id_no, pc.id as condition_id, pc.name, pcv.value as condition_value, pcv.condition_id as condition_value_id, ecat.ebay_category_name, cd.name as oc_category_name, ec.ebay_site_id, pc.ebay_category_id, pc.oc_category_id FROM ".DB_PREFIX."wk_ebay_prod_condition_value pcv LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition pc ON(pcv.condition_value_id = pc.id) LEFT JOIN ".DB_PREFIX."wk_ebaysync_categories ecat ON ((pc.ebay_category_id = ecat.ebay_category_id) AND (pc.oc_category_id = ecat.opencart_category_id)) LEFT JOIN ".DB_PREFIX."category_description cd ON(pc.oc_category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."wk_ebay_categories ec ON (pc.ebay_category_id = ec.ebay_category_id) WHERE cd.language_id = '".(int)$this->config->get('config_language_id')."' ";

		if(!empty($data['filter_row_id'])){
			$sql .= " AND pcv.id_no = '".(int)$data['filter_row_id']."' ";
		}

		if(!empty($data['filter_condition_value'])){
			$sql .= " AND LCASE(pcv.value) LIKE '".$this->db->escape(strtolower($data['filter_condition_value']))."%' ";
		}

		if(!empty($data['filter_condition_name'])){
			$sql .= " AND pc.name LIKE '".$this->db->escape($data['filter_condition_name'])."%' ";
		}

		if(!empty($data['filter_ebay_category_name'])){
			$sql .= " AND ecat.ebay_category_name LIKE '".$this->db->escape(strtolower($data['filter_ebay_category_name']))."%' ";
		}

		if(!empty($data['filter_oc_category_name'])){
			$sql .= " AND cd.name LIKE '".$this->db->escape($data['filter_oc_category_name'])."%' ";
		}

		$sql .= " GROUP BY pcv.condition_id, pc.id";

		$sort_data = array(
			'pcv.condition_id',
			'pcv.value',
			'pc.id',
			'pc.name',
			'ecat.ebay_category_name',
			'oc_category_name',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pc.id";
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

		return $this->db->query($sql)->rows;
	}

	/**
	 * [getAllEbayCondition to get all the ebay conditions for Condition Listing Left Menu]
	 * @param  boolean $condition_condValue [description]
	 * @return [type]                       [description]
	 */
	public function getAllEbayConditionTotal($data = array()){
		$sql = "SELECT pcv.* FROM ".DB_PREFIX."wk_ebay_prod_condition_value pcv LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition pc ON(pcv.condition_value_id = pc.id) LEFT JOIN ".DB_PREFIX."wk_ebaysync_categories ecat ON ((pc.ebay_category_id = ecat.ebay_category_id) AND (pc.oc_category_id = ecat.opencart_category_id)) LEFT JOIN ".DB_PREFIX."category_description cd ON(pc.oc_category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."wk_ebay_categories ec ON (pc.ebay_category_id = ec.ebay_category_id) WHERE cd.language_id = '".(int)$this->config->get('config_language_id')."' ";

		if(!empty($data['filter_condition_value'])){
			$sql .= " AND LCASE(pcv.value) LIKE '".$this->db->escape(strtolower($data['filter_condition_value']))."%' ";
		}

		if(!empty($data['filter_condition_name'])){
			$sql .= " AND LCASE(pc.name) LIKE '".$this->db->escape(strtolower($data['filter_condition_name']))."%' ";
		}

		if(!empty($data['filter_ebay_category_name'])){
			$sql .= " AND ecat.ebay_category_name LIKE '".$this->db->escape(strtolower($data['filter_ebay_category_name']))."%' ";
		}

		if(!empty($data['filter_oc_category_name'])){
			$sql .= " AND cd.name LIKE '".$this->db->escape($data['filter_oc_category_name'])."%' ";
		}

		$sql .= " GROUP BY pcv.condition_id, pc.id";

		$result = $this->db->query($sql)->rows;

		return count($result);
	}

  public function deleteEbayCondition($data = array()){
		$deleteStatus = false;
		if(!empty($data['filter_row_id'])) {
				$getConditionvalueEntry = $this->getAllEbayCondition($data);
				if(isset($getConditionvalueEntry[0]) && count($getConditionvalueEntry) == 1){
					$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_prod_condition_value WHERE id_no = '".(int)$data['filter_row_id']."' ");
				}

				$deleteStatus = true;
		}
		return $deleteStatus;
	}

	/**
	 * [checkVariationEntry to check ebay variation entry]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function checkVariationEntry($data = array()){
		$sql = "SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.option_id IN (SELECT variation_id FROM ".DB_PREFIX."wk_ebay_variation) ";

		if(isset($data['product_id'])){
			$sql .= " AND po.product_id = '" . (int)$data['product_id'] . "' ";
		}

		if(isset($data['option_id'])){
			$sql .= " AND po.option_id = '" . (int)$data['option_id'] . "' ";
		}

		$getVariationEntry = $this->db->query($sql)->row;

		return $getVariationEntry;
	}

	public function getProductImageURL($data = array()){
    $image_details 	= $_productImagesList = [];

		if(isset($data['product_id']) && $data['product_id']){
        if(isset($data['related_images']) && !empty($data['related_images'])){
            $_productImagesList = $data['related_images'];
        }else{
            $_productImagesList = $this->db->query("SELECT pi.* FROM ".DB_PREFIX."product_image pi LEFT JOIN ".DB_PREFIX."product p ON(pi.product_id = p.product_id) WHERE pi.product_id = '".(int)$data['product_id']."' ")->rows;
        }

        $prod_images 		= [];
				$prod_images[] 	= HTTP_CATALOG.'image/'.$data['image'];
	        foreach ($_productImagesList as $product_image) {
							if(isset($product_image['image']) && $product_image['image'] && file_exists(DIR_IMAGE.$product_image['image'])){
									$prod_images[] = HTTP_CATALOG.'image/'.$product_image['image'];
							}
	        }

	        $image_details = [
                                'GalleryType' 	=> 'Featured',
                                'GalleryURL' 	  => HTTP_CATALOG.'image/'.$data['image'],
                                'PhotoDisplay' 	=> 'PicturePack',
                                'PictureURL' 	  => $prod_images,
	                        ];

	        return $image_details;
		}
	}

	public function _getEbayVariation(){
		$results = array();
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."option o LEFT JOIN ".DB_PREFIX."option_description od ON(o.option_id = od.option_id) WHERE od.name = '".$this->db->escape('Variations')."' AND od.language_id = '".(int)$this->config->get('config_language_id')."' ")->row;

		if(isset($result['option_id']) && $result['option_id']){
			$query = $this->db->query("SELECT ev.*, ovd.*, ov.option_id FROM ".DB_PREFIX."wk_ebay_variation ev LEFT JOIN ".DB_PREFIX."option_value ov ON(ev.value_id = ov.option_value_id) LEFT JOIN ".DB_PREFIX."option_value_description ovd ON(ov.option_value_id = ovd.option_value_id) WHERE ev.variation_id = '".(int)$result['option_id']."' AND ov.option_id = '".(int)$result['option_id']."' AND ovd.language_id = '".(int)$this->config->get('config_language_id')."' ")->rows;
			$results = array(
				'option_id' 	=> $result['option_id'],
				'type' 			=>$result['type'],
				'option_name' 	=>$result['name'],
				'option_values' => $query,
				);
		}
		return $results;
	}

  public function _getEbayTemplates(){
    $sql = "SELECT eTemp.* FROM ".DB_PREFIX."wk_ebay_template eTemp LEFT JOIN ".DB_PREFIX."wk_ebay_categories eCat ON ((eTemp.ebay_category_id = eCat.ebay_category_id) && (eTemp.ebay_site_id = eCat.ebay_site_id)) WHERE eTemp.status = '1' ";

    $results = $this->db->query($sql)->rows;
    return $results;
  }

  public function _getProductTemplate($data = array()){
    $sql = "SELECT eTemp.*, ePT.* FROM ".DB_PREFIX."wk_ebay_template_to_product ePT LEFT JOIN ".DB_PREFIX."wk_ebay_template eTemp ON (ePT.template_id = eTemp.id) LEFT JOIN ".DB_PREFIX."wk_ebay_categories eCat ON ((eTemp.ebay_category_id = eCat.ebay_category_id) && (eTemp.ebay_site_id = eCat.ebay_site_id)) WHERE eTemp.status = '1' ";

    if(!empty($data['filter_product_id'])){
      $sql .= " AND ePT.product_id = '".(int)$data['filter_product_id']."' ";
    }
    if(!empty($data['filter_template_id'])){
      $sql .= " AND ePT.template_id = '".(int)$data['filter_template_id']."' AND eTemp.id = '".(int)$data['filter_template_id']."' ";
    }

    $result = $this->db->query($sql)->row;
    return $result;
  }

	public function __getTemplateListingAttributes($template_id){
		$sql = "SELECT eTP.* FROM " . DB_PREFIX . "wk_ebay_template_placeholder eTP LEFT JOIN ".DB_PREFIX."wk_ebay_template eTemp ON (eTP.template_id = eTemp.id) WHERE eTP.template_id = '".(int)$template_id."' AND eTemp.id = '".(int)$template_id."'  ";
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function _getProductVariation($product_id = false, $type = 'product_variation'){
		$results = $product_option_value = array();

		$result = $this->db->query("SELECT po.*, od.* FROM ".DB_PREFIX."product_option po LEFT JOIN ".DB_PREFIX."product p ON(po.product_id = p.product_id) LEFT JOIN ".DB_PREFIX."option o ON(po.option_id = o.option_id) LEFT JOIN ".DB_PREFIX."option_description od ON(po.option_id = od.option_id) LEFT JOIN ".DB_PREFIX."wk_ebay_variation ev ON (o.option_id = ev.variation_id) WHERE po.product_id = '".(int)$product_id."' AND od.name = '".$this->db->escape('Variations')."' ")->row;

		if(isset($result['option_id']) && $result['option_id']){
			$query = $this->db->query("SELECT pov.*, ev.* FROM ".DB_PREFIX."product_option_value pov LEFT JOIN ".DB_PREFIX."option_value_description ovd ON ((pov.option_value_id = ovd.option_value_id) AND (pov.option_id = ovd.option_id)) LEFT JOIN ".DB_PREFIX."wk_ebay_variation ev ON ((pov.option_value_id = ev.value_id) AND (pov.option_id = ev.variation_id)) WHERE pov.option_id = '".(int)$result['option_id']."' AND pov.product_id = '".(int)$product_id."' AND pov.product_option_id = '".(int)$result['product_option_id']."' AND ovd.language_id = '".(int)$this->config->get('config_language_id')."' ")->rows;

			if(!empty($query)){
				foreach ($query as $key => $product_option_entry) {
					if($type == 'product_variation'){
						$results[] = $product_option_entry['option_value_id'];
					}

					if($type == 'product_variation_value'){
						$product_option_value[$product_option_entry['option_value_id']] = array(
							'name' 			=> $product_option_entry['value_name'],
							'quantity' 		=> $product_option_entry['quantity'],
							'price' 		=> $product_option_entry['price'],
							'price_prefix' 	=> $product_option_entry['prefix'],
							'label' 		=> unserialize($product_option_entry['label']));
					}
				}
			}
			if($type == 'product_variation_value'){
				$results[$result['option_id']] = array(
									'option_id' 	=> $result['option_id'],
									'option_value' 	=> $product_option_value,
									);
			}
		}

		return $results;
	}
	public function getEbaySiteList(){
		$data = array();
		$data['ebay_sites'] = array(
			0 	=> 'eBay United State',
			2 	=> 'eBay Canada (English)',
			3 	=> 'eBay UK',
			15 	=> 'eBay Australia',
			16 	=> 'eBay Austria',
			23 	=> 'eBay Belgium (French)',
			71 	=> 'eBay France',
			77 	=> 'eBay Germany',
			186 => 'eBay Spain',
			193 => 'eBay Switzerland',
			100 => 'eBay Motors',
			101 => 'eBay Italy',
			123 => 'eBay Belgium (Dutch)',
			146 => 'eBay Netherlands',
			201 => 'eBay Hong Kong',
			203 => 'eBay India',
			205 => 'eBay Ireland',
			207 => 'eBay Malaysia',
			210 => 'eBay Canada (French)',
			211 => 'eBay Philippines',
			212 => 'eBay Poland',
			216 => 'eBay Singapore',
		);
		return $data;
	}

  public function getEbaySiteCurrency(){
		return $ebaySitesCurrency = array(
			'USD' 	=> 'USD - (US Dollar)',
			'CAD' 	=> 'CAD - (Canadian Dollar)',
      'GBP' 	=> 'GBP - (British Pound)',
      'AUD' 	=> 'AUD - (Australian Dollar)',
      'EUR' 	=> 'EUR - (Euro)',
      'CHF' 	=> 'CHF - (Swiss Franc)',
      'CNY' 	=> 'CNY - (Chinese Renminbi)',
      'HKD' 	=> 'HKD - (Hong Kong Dollar)',
      'PHP' 	=> 'PHP - (Philippines Peso)',
      'PLN' 	=> 'PLN - (Polish Zloty)',
      'SEK' 	=> 'SEK - (Sweden Krona)',
      'SGD' 	=> 'SGD - (Singapore Dollar)',
      'TWD' 	=> 'TWD - (Taiwanese Dollar)',
      'INR' 	=> 'INR - (Indian Rupee)',
      'MYR' 	=> 'MYR - (Malaysian Ringgit)',
		);
	}

  public function calculateQuantity($quantity, $pply_on) {

    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_ebay_price_qty_rule` WHERE `rule_for` = 'quantity' AND `portation` = '" . $pply_on . "' AND `min` <= " . (int)$quantity . " AND `max` >= " . (int)$quantity . " AND `status` = '1' ORDER BY `sort_order` ASC LIMIT 1");

    if ($query->num_rows) {
      if ($query->row['operation'] == 'fixed') {
        if ($query->row['operation_type'] == '+') {
          $quantity += $query->row['value'];
        } else if ($query->row['operation_type'] == '-') {
          $quantity -= $query->row['value'];
        }
      } else if ($query->row['operation'] == 'percentage') {
        $percentage_quantity = ($quantity * $query->row['value']) / 100;

        if ($query->row['operation_type'] == '+') {
          $quantity += (int)$percentage_quantity;
        } else if ($query->row['operation_type'] == '-') {
          $quantity -= (int)$percentage_quantity;
        }

      }
    }

    return $quantity;
	}

	public function calculatePrice($price, $pply_on) {
    $sql = "SELECT * FROM `" . DB_PREFIX . "wk_ebay_price_qty_rule` WHERE `rule_for` = 'price' AND `portation` = '" . $pply_on . "' AND `min` <= " . (int)$price . " AND `max` >= " . (int)$price . " AND `status` = '1' ORDER BY `sort_order` ASC LIMIT 1";

    $query = $this->db->query($sql);

    if ($query->num_rows) {
      if ($query->row['operation'] == 'fixed') {
        if ($query->row['operation_type'] == '+') {
          $price += $query->row['value'];
        } else if ($query->row['operation_type'] == '-') {
          $price -= $query->row['value'];
        }
      } else if ($query->row['operation'] == 'percentage') {
        $percentage_quantity = ($price * $query->row['value']) / 100;

        if ($query->row['operation_type'] == '+') {
          $price += (int)$percentage_quantity;
        } else if ($query->row['operation_type'] == '-') {
          $price -= (int)$percentage_quantity;
        }
      }
    }
    
    return $price;
	}
}
