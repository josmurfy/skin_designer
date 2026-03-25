<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapEbayCategoryImport extends Model {

	/**
	 * [get_ebay_category to get the ebay category from opencart table]
	 * @param  array  $data [filter array]
	 * @return [array]       [array of eBay Category data]
	 */
	public function get_ebay_category($data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."wk_ebay_categories WHERE 1 ";

		if(!empty($data['ebay_category_id'])){
			$sql .= " AND ebay_category_id = '".(int)$data['ebay_category_id']."' ";
		}

		if(!empty($data['ebay_category_parent_id'])){
			$sql .= " AND ebay_category_parent_id = '".(int)$data['ebay_category_parent_id']."' ";
		}

		if(!empty($data['ebay_category_level'])){
			$sql .= " AND ebay_category_level = '".(int)$data['ebay_category_level']."' ";
		}

		$sql .= " ORDER BY ebay_category_name ASC";

		$results = $this->db->query($sql)->rows;

		return $results;
	}

	/**
	 * [import_ebay_category to save the ebay category to opencart table]
	 * @param  array  $data [details of ebay category]
	 * @return [type]       [description]
	 */
	 public function import_ebay_category($data = array()){
 		$count_success = 0;
		$getRecords		= $getCategoryData = $allCatData = array();
		$getCategoryData = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_category_data")->rows;

		if(isset($getCategoryData) && !empty($getCategoryData)){
			foreach ($getCategoryData as $key => $saveCategory) {
					$allCatData = array_merge($allCatData, unserialize($saveCategory['data']));
			}

			$getRecords 		= array_slice($allCatData, ($data['limit']*($data['page'] - 1)), $data['limit']);

			foreach ($getRecords as $key => $record) {
					$record = (array)$record;

		 			if(isset($record['CategoryID']) && $record['CategoryID'] && !empty($record) ){
							if(isset($record['CategoryParentID']) && $record['CategoryParentID']){
			 					$parent_category = $record['CategoryParentID'];
			 				}else{
			 					$parent_category = 0;
			 				}

			 				$getCategoryEntry = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_categories WHERE ebay_category_id = '".$this->db->escape($record['CategoryID'])."' AND ebay_site_id = '".(int)$this->config->get('ebay_connector_ebay_sites')."' ")->row;

			 				if(isset($getCategoryEntry['ebay_category_id']) && !empty($getCategoryEntry['ebay_category_id'])){
										$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_categories SET ebay_category_parent_id = '".$parent_category."', ebay_category_level = '".(int)$record['CategoryLevel']."', ebay_category_name = '".$this->db->escape($record['CategoryName'])."' WHERE ebay_category_id = '".$getCategoryEntry['ebay_category_id']."' AND ebay_site_id = '".(int)$this->config->get('ebay_connector_ebay_sites')."' ");
			 				}else{
			 						if(isset($record['CategoryName']) && $record['CategoryName'] && !is_array($record['CategoryName'])){
											$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_categories SET ebay_category_id = '".$record['CategoryID']."', ebay_category_parent_id = '".$parent_category."', ebay_category_level = '".(int)$record['CategoryLevel']."', ebay_category_name = '".$this->db->escape($record['CategoryName'])."', `ebay_site_id` = '".(int)$this->config->get('ebay_connector_ebay_sites')."', added_date = NOW() ");
			 							$count_success = $count_success + 1;
			 						}
			 				}
		 			}
			}
 		}

 		return array('success' => $count_success, 'already' => ($data['limit'] - $count_success));
 	}

	public function saveEbayCategoryData($data = array()){
			$result = false;
			if(isset($data['data']) && $data['data']){
				$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_category_data ");
				$getPageStep 	= $data['count']/5;
				$getPageStep 	= ceil($getPageStep);
				if($getPageStep){
						for ($pages = 1; $pages <= 5; $pages ++) {
								$getRecords		= array();
								$getRecords 	= array_slice($data['data'], ($getPageStep*($pages - 1)), $getPageStep);
								$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_category_data SET `data` = '".$this->db->escape(serialize($getRecords))."', `count` = '".$data['count']."'");
						}
				}

				$result = $this->db->query("SELECT count FROM ".DB_PREFIX."wk_ebay_category_data")->row;
			}
			return $result;
	}
}
