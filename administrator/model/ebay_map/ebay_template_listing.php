<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapEbayTemplateListing extends Model {

	/**
	 * [getDescriptionTemplate to get the all description template]
	 * @param  array  $data [filter data array]
	 * @return [type]       [list of ebay accounts]
	 */
	public function getDescriptionTemplate($data = array(), $type = false) {
		$sql = "SELECT eTemp.*, eCat.ebay_category_name FROM " . DB_PREFIX . "wk_ebay_template eTemp LEFT JOIN ".DB_PREFIX."wk_ebay_categories eCat ON ((eTemp.ebay_category_id = eCat.ebay_category_id) && (eTemp.ebay_site_id = eCat.ebay_site_id)) WHERE 1 ";

		if (!empty($data['filter_template_id'])) {
			$sql .= " AND eTemp.id = '" . (int)$data['filter_template_id'] . "'";
		}

		if(!empty($data['filter_template_title'])){
			$sql .= " AND eTemp.title LIKE '%" . $this->db->escape($data['filter_template_title']) . "'";
		}

		if (!empty($data['filter_ebay_site_id'])) {
			$sql .= " AND eTemp.ebay_site_id = '" . (int)$data['filter_ebay_site_id'] . "'";
		}

		if(!empty($data['filter_mapped_ebay_category'])){
			$sql .= " AND eCat.ebay_category_name LIKE '%" . $this->db->escape($data['filter_mapped_ebay_category']) . "%'";
		}

		if(!empty($data['filter_created_date'])){
			$sql .= " AND eTemp.create_date LIKE '%" . $this->db->escape($data['filter_created_date']) . "'";
		}

		if(!empty($data['filter_modify_date'])){
			$sql .= " AND eTemp.modify_date LIKE '%" . $this->db->escape($data['filter_modify_date']) . "'";
		}

		$sort_data = array(
			'id',
			'ebay_connector_store_name',
			'ebay_connector_ebay_user_id',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id";
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
	 * [getTotalDescriptionTemplate to get the total number of description template]
	 * @param  array  $data [filter data array]
	 * @return [type]       [total number of ebay account records]
	 */
	public function getTotalDescriptionTemplate($data = array()) {
		$sql = "SELECT COUNT(DISTINCT eTemp.id) AS total FROM " . DB_PREFIX . "wk_ebay_template eTemp LEFT JOIN ".DB_PREFIX."wk_ebay_categories eCat ON ((eTemp.ebay_category_id = eCat.ebay_category_id) && (eTemp.ebay_site_id = eCat.ebay_site_id)) WHERE 1 ";

		if (!empty($data['filter_template_id'])) {
			$sql .= " AND eTemp.id = '" . (int)$data['filter_template_id'] . "'";
		}

		if(!empty($data['filter_template_title'])){
			$sql .= " AND eTemp.title LIKE '%" . $this->db->escape($data['filter_template_title']) . "'";
		}

		if (!empty($data['filter_ebay_site_id'])) {
			$sql .= " AND eTemp.ebay_site_id = '" . (int)$data['filter_ebay_site_id'] . "'";
		}

		if(!empty($data['filter_mapped_ebay_category'])){
			$sql .= " AND LCASE(eCat.ebay_category_name) LIKE '%" . $this->db->escape(strtolower($data['filter_mapped_ebay_category'])) . "%'";
		}

		if(!empty($data['filter_created_date'])){
			$sql .= " AND eTemp.create_date LIKE '%" . $this->db->escape($data['filter_created_date']) . "'";
		}

		if(!empty($data['filter_modify_date'])){
			$sql .= " AND eTemp.modify_date LIKE '%" . $this->db->escape($data['filter_modify_date']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

  public function __getTemplateListing($template_id){
		$sql = "SELECT eTemp.*, eSynCat.account_id, eCat.ebay_category_name FROM " . DB_PREFIX . "wk_ebay_template eTemp LEFT JOIN ".DB_PREFIX."wk_ebaysync_categories eSynCat ON (eTemp.ebay_category_id = eSynCat.ebay_category_id) LEFT JOIN ".DB_PREFIX."wk_ebay_categories eCat ON ((eTemp.ebay_category_id = eCat.ebay_category_id) && (eTemp.ebay_site_id = eCat.ebay_site_id)) WHERE eTemp.id = '".(int)$template_id."' ";
		$query = $this->db->query($sql);

		return $query->row;
  }

  public function __addTemplateListing($data = array()){
			$basicDetails = $imageDetails = $shipping_details = $return_policy = '';
			if(isset($data['template_basicDetails']) && $data['template_basicDetails']){
				$basicDetails = serialize($data['template_basicDetails']);
			}
			if(isset($data['template_images']) && $data['template_images']){
				$imageDetails = serialize($data['template_images']);
			}
			if(isset($data['shipping']) && $data['shipping']){
				$shipping_details = serialize($data['shipping']);
			}
			if(isset($data['return_policy']) && $data['return_policy']){
				$return_policy = serialize($data['return_policy']);
			}
			$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_template SET `title` = '".$this->db->escape($data['template_title'])."', `ebay_site_id` = '".(int)$data['template_ebay_site']."', `ebay_category_id` = '".$this->db->escape($data['template_mapped_category'])."', `template_condition` = '".$this->db->escape($data['template_condition'])."', `template_basicDetails` = '".$this->db->escape($basicDetails)."', `template_images` = '".$this->db->escape($imageDetails)."', `description_type` = '".$this->db->escape($data['template_description'])."', `description_content` = '".$this->db->escape(isset($data['custom_description']) ? $data['custom_description'] : '')."', `shipping_condition` = '".$shipping_details."', `return_policy` = '".$return_policy."', `create_date` = NOW(), `modify_date` = NOW(), `status` = '".(int)$data['status']."' ");

			$template_id = $this->db->getLastId();

			if($template_id){
					if(isset($data['template']['specification']) && !empty($data['template']['specification'])){
							foreach ($data['template']['specification'] as $key => $specification) {
									$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_template_placeholder SET `template_id` = '".(int)$template_id."', `attribute_group_id` = '".(int)$specification."', `placeholder` = '".$this->db->escape(isset($data['template']['keyword'][$key]) ? $data['template']['keyword'][$key] : '')."' ");
							}
					}
			}
			return $template_id;
  }

	public function __editTemplateListing($data = array()){
			if(isset($data['template_id'])){
					$basicDetails = $imageDetails = $shipping_details = $return_policy = '';
					if(isset($data['template_basicDetails']) && $data['template_basicDetails']){
						$basicDetails = serialize($data['template_basicDetails']);
					}
					if(isset($data['template_images']) && $data['template_images']){
						$imageDetails = serialize($data['template_images']);
					}
					if(isset($data['shipping']) && $data['shipping']){
						$shipping_details = serialize($data['shipping']);
					}
					if(isset($data['return_policy']) && $data['return_policy']){
						$return_policy = serialize($data['return_policy']);
					}
					$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_template SET `title` = '".$this->db->escape($data['template_title'])."', `ebay_site_id` = '".(int)$data['template_ebay_site']."', `ebay_category_id` = '".$this->db->escape($data['template_mapped_category'])."', `template_condition` = '".$this->db->escape($data['template_condition'])."', `template_basicDetails` = '".$this->db->escape($basicDetails)."', `template_images` = '".$this->db->escape($imageDetails)."', `description_type` = '".$this->db->escape($data['template_description'])."', `description_content` = '".$this->db->escape(isset($data['custom_description']) ? $data['custom_description'] : '')."', `shipping_condition` = '".$shipping_details."', `return_policy` = '".$return_policy."', `modify_date` = NOW(), `status` = '".(int)$data['status']."' WHERE id = '".(int)$data['template_id']."' ");

					$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_template_placeholder WHERE `template_id` = '".(int)$data['template_id']."' ");
					if(isset($data['template']['specification']) && !empty($data['template']['specification'])){
							foreach ($data['template']['specification'] as $key => $specification) {
									$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_template_placeholder SET `template_id` = '".(int)$data['template_id']."', `attribute_group_id` = '".(int)$specification."', `placeholder` = '".$this->db->escape(isset($data['template']['keyword'][$key]) ? $data['template']['keyword'][$key] : '')."' ");
							}
					}
			}
  }

	public function __geteBayMappedCategory($data = array()){
		$sql = "SELECT wk_eSync.*, cd.*, wk_eSync.id as map_id, wea.ebay_connector_ebay_sites FROM ".DB_PREFIX."wk_ebaysync_categories wk_eSync LEFT JOIN ".DB_PREFIX."wk_ebay_categories eCat ON (wk_eSync.ebay_category_id = eCat.ebay_category_id) LEFT JOIN ".DB_PREFIX."category c ON(wk_eSync.opencart_category_id = c.category_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(wk_eSync.opencart_category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."wk_ebay_accounts wea ON(wk_eSync.account_id = wea.id) WHERE c.status = '1' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' ";

		if(!empty($data['filter_ebay_site_id'])){
			$sql .= " AND wea.ebay_connector_ebay_sites = '".(int)$data['filter_ebay_site_id']."' ";
		}
		$sql .= " GROUP BY c.category_id";

		$query = $this->db->query($sql);

		return $query->rows;
  }

	public function deleteEbayCondition($template_id){
			$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_template_placeholder WHERE template_id = '".(int)$template_id."' ");
			$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_template WHERE id = '".(int)$template_id."' ");
	}

	public function __validateProducteBayTemplate($product_id = false, $data = array()){
		$response = array();
		$categoryFlag = false;
		$this->load->language('ebay_map/ebay_template_listing');
		if(isset($data['product_category']) && !empty($data['product_category'])){
			$getTemplateEntry = $this->__getTemplateListing($data['product_ebay_template']);

			$this->load->model('ebay_map/ebay_map_category');
			sort($data['product_category']);
			foreach ($data['product_category'] as $key => $product_category) {
					$getMappedEntry = $this->model_ebay_map_ebay_map_category->getMapCategories(array('filter_oc_category_id' => $product_category));

					if(isset($getMappedEntry[0]) && (count($getMappedEntry) == 1)){
							if(($getMappedEntry[0]['ebay_category_id'] == $getTemplateEntry['ebay_category_id']) && ($getMappedEntry[0]['ebay_site_id'] == $getTemplateEntry['ebay_site_id'])){
									$categoryFlag = true;
							}
					}
			}
			if($categoryFlag){
					$response = array('success' => true, 'message' => 'Template validate');
			}else{
					$response = array('error' => true, 'message' => $this->language->get('error_no_category_mapped'));
			}
		}else{
			$response = array('error' => true, 'message' => $this->language->get('error_no_category_mapped'));
		}
		return $response;
	}

	public function __addProductToTemplate($product_id = false, $data = array()) {
			if($product_id && !empty($data)){
				$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_template_to_product SET `product_id` = '".(int)$product_id."', `template_id` = '".(int)$data['id']."', `ebay_site_id` = '".(int)$data['ebay_site_id']."', `account_id` = '".(int)$data['account_id']."' ");

				return $this->db->getLastId();
			}
	}
}
