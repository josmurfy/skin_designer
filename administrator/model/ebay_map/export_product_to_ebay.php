<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapExportProductToEbay extends Model {


	public function getOcUnmappedProducts($data = array()) {
		$sql = "SELECT p.*,pd.name, cd.name as category_name, cd.category_id FROM " . DB_PREFIX . "product p LEFT JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."product_to_category p2c ON(p.product_id = p2c.product_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(p2c.category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."wk_ebay_oc_product_map wk_map ON(p.product_id = wk_map.oc_product_id) WHERE p.product_id NOT IN (SELECT oc_product_id FROM ".DB_PREFIX."wk_ebay_oc_product_map ) AND pd.language_id = '".(int)$this->config->get('config_language_id')."' AND p.status = '1' ";

		if(!empty($data['filter_oc_prod_id'])){
			$sql .= " AND p.product_id ='".(int)$data['filter_oc_prod_id']."' ";
		}

		if(!empty($data['filter_oc_prod_name'])){
			$sql .= " AND LCASE(pd.name) LIKE '".$this->db->escape(strtolower($data['filter_oc_prod_name']))."%' ";
		}

		if(!empty($data['filter_oc_cat_name'])){
			$sql .= " AND cd.name LIKE '%".$this->db->escape($data['filter_oc_cat_name'])."%' ";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

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
	 * [getTotalEbayAccount to get the total number of ebay account]
	 * @param  array  $data [filter data array]
	 * @return [type]       [total number of ebay account records]
	 */
	public function getTotalOcUnmappedProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."product_to_category p2c ON(p.product_id = p2c.product_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(p2c.category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."wk_ebay_oc_product_map wk_map ON(p.product_id = wk_map.oc_product_id) WHERE p.product_id NOT IN (SELECT oc_product_id FROM ".DB_PREFIX."wk_ebay_oc_product_map ) AND pd.language_id = '".(int)$this->config->get('config_language_id')."' AND p.status = '1' ";

		if (!empty($data['filter_oc_prod_id'])) {
			$sql .= " AND p.product_id ='".(int)$data['filter_oc_prod_id']."' ";
		}

		if (!empty($data['filter_oc_prod_name'])) {
			$sql .= " AND LCASE(pd.name) LIKE '".$this->db->escape(strtolower($data['filter_oc_prod_name']))."%' ";
		}

		if(!empty($data['filter_oc_cat_name'])) {
			$sql .= " AND cd.name LIKE '%".$this->db->escape($data['filter_oc_cat_name'])."%' ";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getOcProducts($data = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."product p LEFT JOIN ".DB_PREFIX."product_description pd ON(p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.product_id IN (".$data['product_id'].") ";

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
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
				$data['limit'] = 5;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getMappProductId($data = array()){

		$getMapEntry = $this->db->query("SELECT em.* FROM ".DB_PREFIX."wk_ebay_oc_product_map em LEFT JOIN ".DB_PREFIX."product p ON (em.oc_product_id = p.product_id) WHERE em.oc_product_id = '".(int)$data['product_id']."' AND em.account_id = '".(int)$data['account_id']."' ")->row;

		return $getMapEntry;
	}

	public function getMappedcategory($data = array() , $account_id = false){
		$getEntry = array();
		foreach ($data as $key => $category_id) {
			$getMapEntry = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebaysync_categories e_cat LEFT JOIN ".DB_PREFIX."category c ON (e_cat.opencart_category_id = c.category_id) WHERE e_cat.account_id = '".(int)$account_id."' AND e_cat.opencart_category_id = '".(int)$category_id."' ORDER BY e_cat.id ASC ")->row;

			if(!empty($getMapEntry)){
				$getEntry = $getMapEntry;
				return $getEntry;
			}
		}

		return $getEntry;
	}

	public function __syncProductToEbay($data = array()){
		$error_flag = false;
		$this->load->language('ebay_map/export_product_to_ebay');
		$sync_data = $ebay_categoryID = $product_specification = $product_variation =array();
		if(isset($data['product']['product_id']) && isset($data['category_map'])){
			/**
			 * [ price rule changes]
			 * @var [starts]
			 */

			// if ($this->config->get('ebay_connector_price_rules')) {
			// 	$this->load->model('price_rule/export_map');
			// 	$params['price']          = $data['product']['price'];
			// 	$params['product_id']     = $data['product']['product_id'];
			// 	$data['product']['price'] = $this->model_price_rule_export_map->_applyPriceRule($params);
			// }



			 /**
			 * [price rule changes]
			 * @var [ends]
			 */
			// get ebay category id
			if(isset($data['category_map']['ebay_category_id']) && $data['category_map']['ebay_category_id']){
				$ebay_categoryID = ['CategoryID' => $data['category_map']['ebay_category_id']];
			}
			$ebay_conditionId 		= false;
			$ebay_condition_text 	= '';

			$listing_type = 'FixedPriceItem';

			$ebay_auction = $this->isAuctionProduct($data['product']['product_id']);

			if ($ebay_auction) {
				$listing_type = 'AddItem';

				if ($ebay_auction['price_rule_status'] == 'enabled' && $ebay_auction['buy_it_now_price']) {
					$data['product']['price'] = $this->Ebayconnector->calculatePrice($ebay_auction['buy_it_now_price'], 'export');
				} else if ($ebay_auction['buy_it_now_price']) {
					$data['product']['price'] = $ebay_auction['buy_it_now_price'];
				}
			} else {
				$data['product']['price'] = $this->Ebayconnector->calculatePrice($data['product']['price'], 'export');
			}

					// get ebay product category condition
					if (isset($data['category_map']['pro_condition_attr']) && $data['category_map']['pro_condition_attr'] != 'N/A') {
						$condition_result = $this->getProductConditionEntry(array('category_map' => $data['category_map'], 'product_id' => $data['product']['product_id']));

						if (isset($condition_result['condition_id']) && $condition_result['condition_id']) {
							$ebay_conditionId 		= (int) $condition_result['condition_id'];
							$ebay_condition_text 	= $condition_result['value'];
						} else {
						  $error_flag = true;
							$sync_data = [
								'product_id' 		=> $data['product']['product_id'],
								'error_status' 	=> 1,
								'error_message' => 'Warning: Oc-Product: <b>' . $data['product']['name'] . ' [Id: ' . $data['product']['product_id'] . ']</b>, product\'s Condition is missing'
							];
						}
		      }

	        //get product specification(attributes)
	        $getSpecification = $this->getAllCategorySpecification($data['category_map']);

	        if(!empty($getSpecification)){
	        	foreach ($getSpecification as $key => $attr_entry) {
	        		$getProductSpecification = $this->getProductCategorySpecification(array('product_id' => $data['product']['product_id'], 'attribute_id' => $attr_entry['attribute_id']));

	        		if (isset($getProductSpecification['attribute_id']) && $getProductSpecification['attribute_id']) {
	        			$product_specification[$getProductSpecification['product_id'].'_'.$getProductSpecification['attribute_id']] = [
									'Name' 	=> $attr_entry['attr_group_name'],
									'Value' => $getProductSpecification['text'] ? $getProductSpecification['text'] : ''
								];
	        		}
	        	}

						if (!empty($product_specification)) {
							$product_specification = array_values($product_specification);
						} else {
							  $error_flag = true;

							if (isset($sync_data['product_id']) && isset($sync_data['error_message'])) {
									$sync_data['error_message'] =  $sync_data['error_message']. 'and product\'s Specification is missing.';
							} else {
								$sync_data = [
									'product_id' 		=> $data['product']['product_id'],
									'error_status' 	=> 1,
									'error_message' => 'Warning: Oc-Product: <b>'.$data['product']['name'].' [Id: ' . $data['product']['product_id'] . ']</b>, product\'s Specification is missing.'
								];
							}
						}
	        }

	        $getConfAccountSetting 	= $this->Ebayconnector->_getModuleConfiguration($data['account_id']);
	        $getConfDefaultSetting 	= $this->Ebayconnector->getConfigDefaultSetting();

	        if (isset($getConfDefaultSetting['Country'])) {
	        	$this->load->model('localisation/country');
	        	$getCountry = $this->model_localisation_country->getCountry($getConfDefaultSetting['Country']);
	        	$country_code = $getCountry['iso_code_2'];
	        } else {
	        	$country_code = 'US';
	        }

	        $getProductImageURL 	= $this->Ebayconnector->getProductImageURL(array('product_id' => $data['product']['product_id'], 'image' => $data['product']['image']));

	        if($this->config->get('ebay_connector_product_tax')){
	        	$price = $this->tax->calculate($data['product']['price'], $data['product']['tax_class_id'], $this->config->get('config_tax')) ;
	        }else{
	        	$price = $data['product']['price'];
	        }

	        if (isset($data['product']['quantity']) && $data['product']['quantity'] == 0) {
	        	$product_quantity = $this->config->get('ebay_connector_default_item_quantity');
	        } else {
	        	$product_quantity = $data['product']['quantity'];
	        }

					$product_quantity = $this->Ebayconnector->calculateQuantity($product_quantity, 'export');

	        $item_data = [
              'ListingType'       => $listing_type,
              'Title'             => $data['product']['name'],
              'Subtitle'          => $data['product']['meta_title'],
							'ProductListingDetails' => [
								'EAN' =>	(isset($data['product']['ean']) && $data['product']['ean']) ? $data['product']['ean'] : 5025657000512,
							],
              'SKU'          			=> (isset($data['product']['sku']) && $data['product']['sku']) ? $data['product']['sku'] : substr($data['product']['name'], 0, 49),
              'PictureDetails'    => $getProductImageURL,
							'Description'       => $data['product']['description'],
							'DescriptionMode'		=> $data['product']['description'],
              'Quantity'          => $product_quantity,
              'Location'          => $data['product']['location'] ? $data['product']['location'] : $country_code,
              'ItemSpecifics'     => $product_specification,
              'PostalCode'        => $getConfAccountSetting['ebayPostCode'],
              'Currency'          => $getConfAccountSetting['ebayCurrency'],
              'PaymentMethods'    => 'PayPal',
              'PayPalEmailAddress'=> $getConfDefaultSetting['PayPalEmailAddress'],
              'Country'           => 'US',
              'ListingDuration'   => $getConfDefaultSetting['ListingDuration'],
              'DispatchTimeMax'   => $getConfDefaultSetting['DispatchTimeMax'],
              'ReturnPolicy'      => $getConfDefaultSetting['ReturnPolicy'],
              'StartPrice'        => $price,
              'PrimaryCategory'   => $ebay_categoryID,
              'CategoryMappingAllowed' => true,
          ];

					if ($this->getScheduleDate($data['product']['product_id'])) {
						 $item_data['ScheduleTime'] = $this->getScheduleDate($data['product']['product_id']);
					}

					if ($ebay_conditionId) {
						$item_data['ConditionID'] 	= $ebay_conditionId; //1000 for new product
						$item_data['ConditionText'] = $ebay_condition_text;
          }

					$shipping_info = $this->getShippingDetails($data['account_id']);

					if (isset($shipping_info['shipping_priority'])) {
						$shipping_priority = $shipping_info['shipping_priority'];
					} else {
						$shipping_priority = $this->config->get('ebay_connector_shipping_priority');
					}

					if (isset($shipping_info['shipping_service']) && $shipping_info['shipping_service']) {
						$shipping_service = $shipping_info['shipping_service'];
					} else {
						$shipping_service = $this->config->get('ebay_connector_shipping_service');
					}

					if (isset($shipping_info['shipping_cost']) && $shipping_info['shipping_cost']) {
						$shipping_cost = $shipping_info['shipping_cost'];
					} else {
						$shipping_cost = $this->config->get('ebay_connector_shipping_service_cost');
					}

					if (isset($shipping_info['shipping_additional_cost']) && $shipping_info['shipping_additional_cost']) {
						$shipping_additional_cost = $shipping_info['shipping_additional_cost'];
					} else {
						$shipping_additional_cost = $this->config->get('ebay_connector_shipping_service_add_cost');
					}

					if (isset($shipping_info['shipping_min_time']) && $shipping_info['shipping_min_time']) {
						$shipping_min_time = $shipping_info['shipping_min_time'];
					} else {
						$shipping_min_time = $this->config->get('ebay_connector_shipping_min_time');
					}

					if (isset($shipping_info['shipping_max_time']) && $shipping_info['shipping_max_time']) {
						$shipping_max_time = $shipping_info['shipping_max_time'];
					} else {
						$shipping_max_time = $this->config->get('ebay_connector_shipping_max_time');
					}

					if (isset($shipping_info['free_shipping_status']) && $shipping_info['free_shipping_status']) {
						$free_shipping_status = $shipping_info['free_shipping_status'];
					} else {
						$free_shipping_status = $this->config->get('ebay_connector_shipping_free_status');
					}

          if ($free_shipping_status) {
		        	$shipping_setting = [
		        		'ShippingDetails' 		=> [
			                'ShippingServiceOptions' => [
			                	[
			                	'ShippingServicePriority' 	=> $shipping_priority,
			                	'ShippingService' 					=> $shipping_service,
			                	'ShippingTimeMin' 					=> $shipping_min_time,
			                	'ShippingTimeMax' 					=> $shipping_max_time,
			                	'FreeShipping' 							=> $free_shipping_status,
			                ]
										],
			            ],
		            ];
	         } else {
	        		$shipping_setting = [
								'ShippingDetails' 		=> [
		                'ShippingServiceOptions' => [
		                	[
		                	'ShippingServicePriority' 			=> $shipping_priority,
		                	'ShippingService' 							=> $shipping_service,
		                	'ShippingServiceCost' 					=> $shipping_cost,
		                	'ShippingServiceAdditionalCost' => $shipping_additional_cost,
		                	'ShippingTimeMin' 							=> $shipping_min_time,
		                	'ShippingTimeMax' 							=> $shipping_max_time,
		                	'FreeShipping' 									=> $free_shipping_status
		                ]
									],
		            ],
							];
	        }

		      $item_data['ShippingDetails'] = $shipping_setting['ShippingDetails'];

          //get Product Ebay Variation
	        $getProductEbayVariation = $this->Ebayconnector->_getProductVariation($data['product']['product_id'], 'product_variation_value');

	        if (isset($getProductEbayVariation) && $getProductEbayVariation) {
	        	$SetValues = $SetValueList = [];

	        	foreach ($getProductEbayVariation as $key => $variation) {
	        		foreach ($variation['option_value'] as $option_value => $variation_values) {
	        			$option_price = $this->tax->calculate($variation_values['price'], $data['product']['tax_class_id'], $this->config->get('config_tax') ? 'P' : false);

	        			if(isset($variation_values['price_prefix']) && $variation_values['price_prefix'] == '+'){
	        				$optionPrice = (float)$price + (float)$option_price;
	        			}else{
	        				$optionPrice = (float)$price - (float)$option_price;
	        			}
	        			$option_name = $NameValueList = [];
	        			$option_name = explode('-', $variation_values['name']);
        				foreach ($option_name as $key1 => $opt_value) {
        					$NameValueList[] = ['Name' 	=> $variation_values['label'][$key1],
        										'Value' => $opt_value,];
        				}

        				foreach ($NameValueList as $variation_name_value) {
        					if(isset($SetValueList[$variation_name_value['Name']])){
        						foreach ($SetValueList[$variation_name_value['Name']] as $optValue) {
        							if($optValue != $variation_name_value['Value']){
        								array_push($SetValueList[$variation_name_value['Name']], $variation_name_value['Value']);
        							}
        						}
        					}else{
        						$SetValueList[$variation_name_value['Name']] = [$variation_name_value['Value']];
        					}
        				}
	        			$product_variation[] = [
									'SKU' 			=> $data['product']['name'].'-'.$option_value,
									'StartPrice' 	=> ['_' => $optionPrice, 'currencyID' => $this->config->get('config_currency')],
									'Quantity' 		=> $variation_values['quantity'] ? $variation_values['quantity'] : $data['product']['quantity'],
									'VariationSpecifics' => ['NameValueList' => $NameValueList],
									'VariationProductListingDetails' => ['EAN' => (isset($data['product']['ean']) && $data['product']['ean'])  ? $data['product']['ean'] : 5025657000512],
								];
	        		}
	        	}

	        	if (isset($product_variation) && $product_variation && !empty($product_variation)) {
	        		if (!empty($SetValueList)) {
	        			foreach ($SetValueList as $key => $varSet) {
									$varSet = array_unique($varSet);
									ksort($varSet);
									$varSet = array_values($varSet);

        					$SetValues[] = ['Name' => $key, 'Value' => $varSet];
        				}
	        		}

	        		$item_data['Variations'] = [
								'Variation' => $product_variation,
								'VariationSpecificsSet' => ['NameValueList' => $SetValues]
							];
              unset($item_data['StartPrice']);
              unset($item_data['Quantity']);
	        	}
	        }

					if (empty($sync_data)) {
							$getEbayClient = $this->Ebayconnector->_eBayAuthSession($data['account_id']);

							if ($getEbayClient) {
									$product_description = $this->checkTemplateContent(array_merge($data['product'], $item_data));
									if(isset($product_description) && !empty($product_description)){
											if(isset($product_description['description'])){
													$item_data['Description'] =	$item_data['DescriptionMode'] =  $product_description['description'];
											}
											if(isset($product_description['shipping']) && !empty($product_description['shipping'])){
													$item_data['ShippingDetails'] =	$product_description['shipping'];
											}
											if(isset($product_description['return']) && !empty($product_description['return'])){
													$item_data['ReturnPolicy'] =	$product_description['return'];
											}
									}

									$params = array(
											'Version' 			=> 891,
											'ErrorLanguage'	=> 'en_US',
											'Item' 					=> $item_data
									);

									if ($listing_type == 'AddItem') {
										$results = $getEbayClient->AddItem($params);
									} else {
										$results = $getEbayClient->AddFixedPriceItem($params);
									}

									if(isset($results->Ack) && ((string)$results->Ack == 'Success' || (string)$results->Ack == 'Warning')){
										$save_data = array(
												'oc_product_id' 		=> $data['product']['product_id'],
												'ebay_product_id' 	=> $results->ItemID,
												'oc_category_id'		=> $data['category_map']['opencart_category_id'],
												'oc_image'					=> $data['product']['image'],
												'product_images'		=> '',
												'account_id'				=> $data['account_id'],
											);
										$getMapLastId = $this->saveProductMap($save_data);
										$sync_data = ['name' => $data['product']['name'], 'ebay_item_id' => $results->ItemID];
										$this->log->write('******Successfully Export Opencart Product To Ebay****** : '. $results->ItemID);
									}else{
										$error_flag = true;
										$report_error = '';
										if (isset($results->Errors)) {
												if (is_object($results->Errors)) {
														$report_error = $results->Errors->ShortMessage.' ( '.$results->Errors->LongMessage.' ).';
												} else {
															foreach ($results->Errors as $key => $error_obj) {
																	$report_error = $report_error.' '.($key+1).') '.$error_obj->ShortMessage.' ( '.$error_obj->LongMessage.' ).';
														}
												}
											}else{
												$report_error = 'Internal error to the application.';
											}
											$sync_data = [
													'product_id' 		=> $data['product']['product_id'],
													'error_status' 	=> 1,
													'error_message' => 'Oc-Product: <b>'.$data['product']['name'].' [Id: '.$data['product']['product_id'].']</b> ( '.$report_error.' )',
											];

											$this->log->write('Export To Ebay Error: OcProductId: '.$data['product']['product_id'].' ( '.$report_error.' )');
									}
							}else{
								$error_flag = true;
								$sync_data = ['error_status' => 1,'error_message' => $this->language->get('error_ebay_account_details')];
							}
					}
		}
		if ($error_flag && $this->config->get('ebay_connector_price_rules')) {
			$this->load->model('price_rule/export_map');
			$this->model_price_rule_export_map->deleteEntry($data['product']['product_id']);
		}
		return $sync_data;
	}

	public function checkTemplateContent($data = array()){

		   if(!isset($data['StartPrice'])) {
				 $data['StartPrice'] = $data['price'];
			 }

			$product_description 	= '';
			$keywordValues				= $find = $replace = $ebaySpecification = array();
			$shipping_condition		= $return_policy	 = array();
			$keywordValues = array(
												'basic_name'				=> $data['name'],
												'basic_meta_title'	=> $data['meta_title'],
												'basic_model'				=> $data['model'],
												'basic_sku'					=> $data['sku'],
												'basic_location'		=> $data['Location'],
												'basic_price'				=> $data['StartPrice'],
												'basic_quantity'		=> $data['quantity'],
												'basic_date_available' => $data['date_available'],
												'basic_weight' 			=> $this->weight->format($this->weight->convert($data['weight'], $data['weight_class_id'], $this->config->get('config_weight_class_id')), $this->config->get('config_weight_class_id'), '.', ','),
												'image_main' 				=> '',
												'image_thumb' 			=> '',
												'shipping_condition'=> '',
												'return_policy'			=> '',
											);

			if(isset($data['template_id'])){
					$getProductTemplate = $this->Ebayconnector->_getProductTemplate(array('filter_template_id' => $data['template_id']));
			}else{
					$getProductTemplate = $this->Ebayconnector->_getProductTemplate(array('filter_product_id' => $data['product_id']));
			}

			if(!empty($getProductTemplate) && isset($getProductTemplate['template_id'])){

				$getTemplateSpecification = $this->Ebayconnector->__getTemplateListingAttributes($getProductTemplate['template_id']);
				if(!empty($getTemplateSpecification)){
						$ebaySpecification 				= array_column($data['ItemSpecifics'], 'Value', 'Name');
						$allSpecificationKeyword 	= array_column($getTemplateSpecification, 'placeholder', 'attribute_group_id');

						foreach ($allSpecificationKeyword as $attr_grp_id => $keyword) {
								$getSpecificationEntry = $this->Ebayconnector->checkSpecificationEntry(array('attribute_group_id' => $attr_grp_id));
									if(!empty($getSpecificationEntry) && ($getSpecificationEntry['attribute_group_id'] == $attr_grp_id)){
										$find['attr_'.strtolower($getSpecificationEntry['group_name'])] 		= $keyword;
										$replace['attr_'.strtolower($getSpecificationEntry['group_name'])] 	= $ebaySpecification[$getSpecificationEntry['group_name']];
									}
						}
				}
				if(!empty($getProductTemplate['template_condition'])){
					$find['prod_condition'] 		= $getProductTemplate['template_condition'];
					$replace['prod_condition'] 	= $data['ConditionText'];
				}

				if(isset($getProductTemplate['template_basicDetails'])){
					$basicDetails = unserialize($getProductTemplate['template_basicDetails']);
					if(!empty($basicDetails)){
							foreach ($basicDetails as $key_index => $basic_keyword) {
									$find['basic_'.$key_index] 		= $basic_keyword;
									$replace['basic_'.$key_index] = '';
							}
					}
				}

				if(isset($getProductTemplate['shipping_condition']) && $getProductTemplate['shipping_condition']){
					$shippingDetails = unserialize($getProductTemplate['shipping_condition']);
					if(!empty($shippingDetails) && isset($shippingDetails['status']) && $shippingDetails['status']){
							$find['basic_shippingEbay'] 		= $shippingDetails['keyword'];
							$getSubTemp = $this->make_subTemplate($shippingDetails, $type = 'shipping');
							$replace['basic_shippingEbay'] 	= '';
							if(isset($getSubTemp['htmlTemp']) && $getSubTemp['htmlTemp']){
									$replace['basic_shippingEbay'] 	= $getSubTemp['htmlTemp'];
							}
							if(isset($getSubTemp['updateSetting']) && !empty($getSubTemp['updateSetting'])){
									$shipping_condition 	= $getSubTemp['updateSetting'];
							}
					}
				}

				if(isset($getProductTemplate['return_policy']) && $getProductTemplate['return_policy']){
					$policyDetails = unserialize($getProductTemplate['return_policy']);
					if(!empty($policyDetails) && isset($policyDetails['status']) && $policyDetails['status']){
							$find['basic_returnPolicyEbay'] 		= $policyDetails['keyword'];
							$getSubTemp = $this->make_subTemplate($policyDetails, $type = 'return_policy');
							$replace['basic_returnPolicyEbay'] 	= '';
							if(isset($getSubTemp['htmlTemp']) && $getSubTemp['htmlTemp']){
									$replace['basic_returnPolicyEbay'] 	= $getSubTemp['htmlTemp'];
							}
							if(isset($getSubTemp['updateSetting']) && !empty($getSubTemp['updateSetting'])){
									$return_policy 	= $getSubTemp['updateSetting'];
							}
					}
				}

				if(isset($getProductTemplate['template_images'])){
						$imageDetails = unserialize($getProductTemplate['template_images']);
						if(!empty($imageDetails)){
								foreach ($imageDetails as $key_index => $imageKeyword) {
										if($key_index == 'main'){
												$find['image_'.$key_index] 		= $imageKeyword;
												$keywordValues['image_main'] = '<img src="'.$data['PictureDetails']['GalleryURL'].'" alt="'.$data['Title'].'" width="'.$this->config->get('theme_default_image_thumb_width').'" height="'.$this->config->get('theme_default_image_thumb_height').'" />';
										}
										if($key_index == 'thumb'){
											$html = '';
											$find['image_'.$key_index] 		= $imageKeyword;
											if(isset($data['PictureDetails']['PictureURL']) && !empty($data['PictureDetails']['PictureURL'])){
													$thumbArray = $data['PictureDetails']['PictureURL'];
													if(isset($imageDetails['thumb_number']) && $imageDetails['thumb_number']){
															$thumbArray = array_slice($data['PictureDetails']['PictureURL'], 0, $imageDetails['thumb_number']);
													}
													if(!empty($thumbArray))
														foreach ($thumbArray as $key => $thumb_img) {
															if($thumb_img == $data['PictureDetails']['GalleryURL']){
																continue;
															}
															$html .= '<div style="border:1px solid #ddd;margin:0 5px;padding:5px;display:block;width:75;float:left;"><img src="'.$thumb_img.'" alt="'.$data['Title'].'" width="'.$this->config->get('theme_default_image_additional_width').'" height="'.$this->config->get('theme_default_image_additional_height').'" /></div>';
														}

											}
												$keywordValues['image_thumb'] = $html;
										}
								}
						}
				}

				$replace = array_merge($replace, $keywordValues);

				ksort($find);
				ksort($replace);

				if($getProductTemplate['description_type'] == 'product'){
					$product_description = trim(str_replace($find, $replace, $data['description']));
				}else if($getProductTemplate['description_type'] == 'custom'){
						$product_description = trim(str_replace($find, $replace, $getProductTemplate['description_content']));
				}else{
						$product_description = $data['description'];
				}
			}else{
				$product_description = $data['description'];
			}

			return array('description' => $this->validateTags(urldecode(html_entity_decode($product_description))), 'shipping' => $shipping_condition, 'return' => $return_policy);
	}

	public function make_subTemplate($customDetails, $type = 'shipping'){
		$htmlSubTemp 			= '';
		$update_setting 	= array();
			if($type == 'shipping'){
					$htmlSubTemp .= '<div style="border: 1px solid rgb(204, 204, 204); position: relative; padding: 25px 10px;width:80%;clear:both;top:35px;"><span style="background-color: #fff;color: #85b716;font-size: 16px;font-weight: 600;left: 32px;overflow: hidden;padding: 0 10px;position: absolute;top: -10px;width: auto;">'.$customDetails['title'].'</span><table cellpadding="0" cellspacing="0" style="border:1px solid #CCC;text-align:left;width:100%;padding:5px;"><thead style="background-color:#ddd;color:333#;"><tr>';
					if($customDetails['icon_status']){
							$htmlSubTemp .= '<th style="padding:6px;">Label</th>';
					}
					$htmlSubTemp .= '<th style="padding:6px;">Shipping and Handling</th>';
					if(!$customDetails['free']){
						$htmlSubTemp .= '<th style="padding:6px;">Each additional item</th>';
					}
					$htmlSubTemp .= '<th style="padding:6px;">Service</th></tr></thead><tbody><tr>';
					$update_setting = [
								'ShippingDetails' 		=> [
											'ShippingServiceOptions' => [
												[
												'ShippingServicePriority' 	=> $this->config->get('ebay_connector_shipping_priority'),
												'ShippingService' 					=> $customDetails['service'],
												'ShippingTimeMin' 					=> $this->config->get('ebay_connector_shipping_min_time'),
												'ShippingTimeMax' 					=> $this->config->get('ebay_connector_shipping_max_time'),
												'FreeShipping' 							=> $customDetails['free'],
											]
										],
								],
					];
					if($customDetails['icon_status']){
							if(isset($customDetails['icon']) && file_exists(DIR_IMAGE.$customDetails['icon'])){
								$this->load->model('tool/image');
								$shipping_icon = $this->model_tool_image->resize($customDetails['icon'], $customDetails['icon_size'], $customDetails['icon_size']);
								$htmlSubTemp .= '<td style="padding:6px;"><img src="'.$shipping_icon.'" width="'.$customDetails['icon_size'].'" height="'.$customDetails['icon_size'].'" /> </td>';
							}

					}
					if(!$customDetails['free']){
							$update_setting['ShippingDetails']['ShippingServiceOptions'][0]['ShippingServiceCost'] = $customDetails['cost'];
							$update_setting['ShippingDetails']['ShippingServiceOptions'][0]['ShippingServiceAdditionalCost'] = $customDetails['add_cost'];
							$htmlSubTemp .= '<td style="padding:6px;">'.$customDetails['cost'].'</td>';
							$htmlSubTemp .= '<td style="padding:6px;">'.$customDetails['add_cost'].'</td>';
					}else{
						$htmlSubTemp .= '<td style="padding:6px;"><b>Free shipping</b></td>';
					}
					$htmlSubTemp .= '<td style="padding:6px;">'.$customDetails['service'].'</td>';
					$htmlSubTemp .= '</tr></tbody></table>';
					$htmlSubTemp .= '<table cellpadding="0" cellspacing="0" style="border:1px solid #CCC;text-align:left;width:100%;padding:5px;"><thead style="background-color:#ddd;color:333#;"><tr><th style="padding:6px;">Other Shipping details</th></tr></thead>';
					$htmlSubTemp .= '<tbody><tr><td style="padding:6px;">'.$customDetails['details'].'</td></tr></tbody></table></div>';

					return array('htmlTemp' => $htmlSubTemp, 'updateSetting' => isset($update_setting['ShippingDetails']) ? $update_setting['ShippingDetails'] : array());
			}

			if($type == 'return_policy'){
					$htmlSubTemp .= '<div style="border: 1px solid rgb(204, 204, 204); position: relative; padding: 25px 10px;width:80%;clear:both;top:35px;"><span style="background-color: #fff;color: #85b716;font-size: 16px;font-weight: 600;left: 32px;overflow: hidden;padding: 0 10px;position: absolute;top: -10px;width: auto;">'.$customDetails['title'].'</span><table cellpadding="0" cellspacing="0" style="border:1px solid #CCC;text-align:left;width:100%;padding:5px;"><thead style="background-color:#ddd;color:333#;"><tr>';

					$htmlSubTemp .= '<th style="padding:6px;">Return Policy</th>';
					$htmlSubTemp .= '<th style="padding:6px;">After receiving the item, contact seller within</th>';
					$htmlSubTemp .= '<th style="padding:6px;">Return shipping</th></tr></thead><tbody><tr>';

					$htmlSubTemp .= '<td style="padding:6px;">'.$customDetails['policy_type'].'</td>';
					$htmlSubTemp .= '<td style="padding:6px;">'.$customDetails['days'].'</td>';
					$htmlSubTemp .= '<td style="padding:6px;">'.$customDetails['pay_by'].'</td>';
					$htmlSubTemp .= '</tr></tbody></table>';
					$htmlSubTemp .= '<table cellpadding="0" cellspacing="0" style="border:1px solid #CCC;text-align:left;width:100%;padding:5px;"><thead style="background-color:#ddd;color:333#;"><tr><th style="padding:6px;">Return policy details</th></tr></thead>';
					$htmlSubTemp .= '<tbody><tr><td style="padding:6px;">'.$customDetails['other_info'].'</td></tr></tbody></table></div>';
					$update_setting = [
														'ReturnPolicy' => [
																								'ReturnsAcceptedOption' 	=> $customDetails['policy_type'],
																								'ReturnsWithinOption' 		=> $customDetails['days'],
																								'Description' 						=> $customDetails['other_info'],
																								'ShippingCostPaidByOption'=> $customDetails['pay_by'],
														]
												];

					return array('htmlTemp' => $htmlSubTemp, 'updateSetting' => isset($update_setting['ReturnPolicy']) ? $update_setting['ReturnPolicy'] : array());
			}
	}

	public function getProductConditionEntry($data = array()){
		$result = $this->db->query("SELECT p2c.* FROM ".DB_PREFIX."wk_ebay_product_to_condition p2c LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition pc ON(p2c.oc_category_id = pc.oc_category_id) WHERE p2c.oc_product_id = '".(int)$data['product_id']."' AND p2c.oc_category_id = '".(int)$data['category_map']['opencart_category_id']."' ")->row;

		return $result;
	}

	public function getAllCategorySpecification($data = array()){
		$results = $this->db->query("SELECT a.attribute_id, ad.name as attribute_name, sm.oc_category_id, sm.ebay_category_id, ag.attribute_group_id, agd.name as attr_group_name  FROM ".DB_PREFIX."attribute a LEFT JOIN ".DB_PREFIX."attribute_description ad ON(a.attribute_id = ad.attribute_id) LEFT JOIN ".DB_PREFIX."attribute_group ag ON(a.attribute_group_id = ag.attribute_group_id) LEFT JOIN ".DB_PREFIX."attribute_group_description agd ON(ag.attribute_group_id = agd.attribute_group_id) LEFT JOIN ".DB_PREFIX."wk_specification_map sm ON(sm.attr_group_id = a.attribute_group_id) WHERE sm.oc_category_id = '".(int)$data['opencart_category_id']."' AND sm.ebay_category_id = '".$this->db->escape($data['ebay_category_id'])."' ")->rows;
		return $results;
	}

	public function getProductCategorySpecification($data = array()){
		$result = array();
		if(isset($data['product_id']) && isset($data['attribute_id'])){
			$result = $this->db->query("SELECT *, agd.name as attr_group_name FROM ".DB_PREFIX."product_attribute pa LEFT JOIN ".DB_PREFIX."attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN ".DB_PREFIX."attribute_description ad ON (a.attribute_id = ad.attribute_id) LEFT JOIN ".DB_PREFIX."attribute_group_description agd ON (a.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '".(int)$data['product_id']."' AND pa.attribute_id = '".(int)$data['attribute_id']."' AND ad.language_id = '".(int)$this->config->get('config_language_id')."' AND agd.language_id = '".(int)$this->config->get('config_language_id')."' ")->row;
		}
		return $result;
	}

	public function saveProductMap($data = array()){
		$map_id = false;
		if(!empty($data)){
			$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_oc_product_map SET `oc_product_id` = '".(int)$data['oc_product_id']."', `ebay_product_id` = '".$this->db->escape($data['ebay_product_id'])."', `oc_category_id` = '".(int)$data['oc_category_id']."', `account_id` = '".(int)$data['account_id']."', `sync_source` = 'Opencart Product' ");
			$map_id = $this->db->getLastId();
		}
		return $map_id;
	}

	public function validateTags($data = ''){
		return preg_replace("~<script[^<>]*>.*</script>~Uis", "", $data);;
	}
	public function getScheduleDate($product_id){
		$schedule = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_product_schedule WHERE oc_product_id='".$product_id."'")->row;
		if(isset($schedule['scheduling_type']) && $schedule['scheduling_type']!='fix'){
			if(isset($schedule['scheduling_date']) && isset($schedule['scheduling_time'])){
				$date=$schedule['scheduling_date'].' '.$schedule['scheduling_time'];
				return date("Y-m-d\TH:i:s\Z",strtotime($date));
			}else{
				return false;
			}

		}else{
			return false;
		}

	}

	public function getShippingDetails($account_id) {
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_ebay_shipping_details` WHERE `account_id` = " . (int)$account_id . "")->row;
	}

	protected function isAuctionProduct($product_id) {
		return $this->db->query("SELECT * FROM `" . DB_PREFIX . "wk_ebay_auction_product` WHERE `product_id` = " . (int)$product_id . " AND `auction_status` = 1")->row;
	}
}
