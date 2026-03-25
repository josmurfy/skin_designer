<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapEbayMapProduct extends Model {

	public function getProducts($data = array()) {
		$sql = "SELECT pm.*, cd.name as oc_category_name, pd.name as product_name, p.model FROM ".DB_PREFIX."wk_ebay_oc_product_map pm LEFT JOIN ".DB_PREFIX."product p ON (pm.oc_product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(pm.oc_product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."category c ON(pm.oc_category_id = c.category_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(pm.oc_category_id = cd.category_id) WHERE c.status = '1' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' AND pd.language_id = '".(int)$this->config->get('config_language_id')."'   ";

		if(!empty($data['filter_category_name'])){
			$sql .= " AND cd.name LIKE '%".$this->db->escape($data['filter_category_name'])."%' ";
		}

		if(!empty($data['filter_oc_product_name'])){
			$sql .= " AND LCASE(pd.name) LIKE '".$this->db->escape(strtolower($data['filter_oc_product_name']))."%' ";
		}

		if(!empty($data['filter_category_id'])){
			$sql .= " AND pm.oc_category_id ='".(int)$data['filter_category_id']."' ";
		}

		if(!empty($data['filter_oc_product_id'])){
			$sql .= " AND pm.oc_product_id ='".(int)$data['filter_oc_product_id']."' ";
		}

		if(!empty($data['filter_ebay_product_id'])){
			$sql .= " AND pm.ebay_product_id ='".$this->db->escape($data['filter_ebay_product_id'])."' ";
		}

		if (isset($data['filter_source_sync']) && $data['filter_source_sync'] !== '') {
			if($data['filter_source_sync'] == 'Ebay Item'){
					$sql .= " AND pm.sync_source = 'Ebay Item' ";
			}
			if($data['filter_source_sync'] == 'Opencart Product'){
					$sql .= " AND pm.sync_source = 'Opencart Product' ";
			}
		}

		if(!empty($data['filter_account_id'])){
			$sql .= " AND pm.account_id ='".(int)$data['filter_account_id']."' ";
		}
		$sort_data = array(
			'pm.id',
			'pm.oc_product_id',
			'pm.ebay_product_id',
			'pd.name',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pm.id";
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

	public function getTotalEbayProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT pm.id) as total FROM ".DB_PREFIX."wk_ebay_oc_product_map pm LEFT JOIN ".DB_PREFIX."product p ON (pm.oc_product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(pm.oc_product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."category c ON(pm.oc_category_id = c.category_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(pm.oc_category_id = cd.category_id) WHERE c.status = '1' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' AND pd.language_id = '".(int)$this->config->get('config_language_id')."'   ";

		if(!empty($data['filter_category_name'])){
			$sql .= " AND cd.name LIKE '%".$this->db->escape($data['filter_category_name'])."%' ";
		}

		if(!empty($data['filter_oc_product_name'])){
			$sql .= " AND LCASE(pd.name) LIKE '".$this->db->escape(strtolower($data['filter_oc_product_name']))."%' ";
		}

		if(!empty($data['filter_category_id'])){
			$sql .= " AND pm.oc_category_id ='".(int)$data['filter_category_id']."' ";
		}

		if(!empty($data['filter_oc_product_id'])){
			$sql .= " AND pm.oc_product_id ='".(int)$data['filter_oc_product_id']."' ";
		}

		if(!empty($data['filter_ebay_product_id'])){
			$sql .= " AND pm.ebay_product_id ='".$this->db->escape($data['filter_ebay_product_id'])."' ";
		}

		if (isset($data['filter_source_sync']) && $data['filter_source_sync'] !== '') {
			if($data['filter_source_sync'] == 'Ebay Item'){
					$sql .= " AND pm.sync_source = 'Ebay Item' ";
			}
			if($data['filter_source_sync'] == 'Opencart Product'){
					$sql .= " AND pm.sync_source = 'Opencart Product' ";
			}
		}

		if(!empty($data['filter_account_id'])){
			$sql .= " AND pm.account_id ='".(int)$data['filter_account_id']."' ";
		}

		$result = $this->db->query($sql)->row;
		return $result['total'];
	}

	public function import_EbayStepProduct($ebay_products, $account_id){
		 $itemSync = $sync_result = $data_success = $data_error = array();
		 $itemSync = $this->__manageEbayProduct($ebay_products, $account_id);

			 if(isset($itemSync['error'])){
				 $data_error[] = $itemSync['error'];
				 unset($itemSync['error']);
			 }
			 if(isset($itemSync['success'])){
				 $this->log->write('**************Success: eBay items sync!*************');
				 $data_success[] = $itemSync['success'];
				 unset($itemSync['success']);
			 }
			 if(!empty($data_success)){
			 	 $sync_result['data'] = $data_success;
			 }
			 if(!empty($data_error)){
			 	 $sync_result['error'] = $data_error;
			 }
			return $sync_result;
	}

	public function __manageEbayProduct($ebay_products, $account_id){
		$this->load->model('ebay_map/ebay_map_category');
		$this->load->model('catalog/attribute');
		$this->load->model('catalog/attribute_group');

		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();

		$this->load->model('setting/store');
		$stores = $this->model_setting_store->getStores();

		array_push($stores,array('store_id' => 0,'name' => 'Default','url' => HTTP_CATALOG, 'ssh' => ''));

		$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($account_id);

		$import_products = array();

		foreach ($ebay_products as $key => $item_data) {
			$item_data = json_decode((json_encode($item_data)), true);

			$item_price 	= $item_data['SellingStatus']['CurrentPrice']['_'];
			$item_currency 	= $item_data['SellingStatus']['CurrentPrice']['currencyID'];

			$product_category = $product_condition = $product_option = $product_attribute = $product_images = array();

			/**
			 * save ebay variation to opencart as options and their value
			 */
            if (isset($item_data['Variations'])) {
                $product_option = $this->__createOpencartOptions($item_data['Variations'], $languages);
            }
            /**
             * save ebay product category condition to opencart as condition(custom fields)
             */
            $ebayProductCategoryId 	= $item_data['PrimaryCategory']['CategoryID'];
            $EbayProductCategory 	= explode(':', $item_data['PrimaryCategory']['CategoryName']);
            $EbayProductCategoryName= $EbayProductCategory[count($EbayProductCategory) - 1];

            $oc_ebay_Category = $this->model_ebay_map_ebay_map_category->getMapCategories(array('filter_ebay_category_id' => $ebayProductCategoryId, 'filter_account_id' => $account_id));

						$oc_category_id = false;
						$category_type 	= '';
						if(isset($oc_ebay_Category[0]['opencart_category_id']) && isset($oc_ebay_Category[0]['ebay_category_id'])){
								$oc_category_id 	= $oc_ebay_Category[0]['opencart_category_id'];
								$product_category = array($oc_category_id);
								$category_type		= 'mapped';
								if(isset($oc_ebay_Category[0]['pro_condition_attr']) && $oc_ebay_Category[0]['pro_condition_attr'] != 'N/A'){
										$ebay_ConditionValue = $item_data['ConditionDisplayName'];

										/**
										 * [$conditionValue to get EbayConditionValue from oc table]
										 * @var [type]
										 */
										$conditionValue = $this->__getEbayConditionValue($oc_ebay_Category[0]['pro_condition_attr'], $ebay_ConditionValue);

										if(isset($conditionValue[$oc_ebay_Category[0]['pro_condition_attr']]['value']) && $conditionValue[$oc_ebay_Category[0]['pro_condition_attr']]['value']){
												// if condition value found
												$product_condition = $conditionValue;
										}else{
												// if condition value not found then insert condition value for this ebay category in category model
												$Condition_Attr_Code = $this->model_ebay_map_ebay_map_category->__createProductConditionAttribute(array('condition_id' => $item_data['ConditionID'], 'condition_name' => $item_data['ConditionDisplayName']), array('ebay_category_id' => $ebayProductCategoryId, 'ebay_category_name' => trim($EbayProductCategoryName)), $oc_ebay_Category[0]['opencart_category_id']);

												$conditionValue = $this->__getEbayConditionValue($oc_ebay_Category[0]['pro_condition_attr'], $ebay_ConditionValue);

												$product_condition = $conditionValue;
										}
							}else{
									$product_condition = array();
							}
            }else{
							//assign to this default category $this->config->get(ebay_connector_default_category)
							if($this->config->get('ebay_connector_default_category')){
									$oc_category_id 		= $this->config->get('ebay_connector_default_category');
									$product_category 	= array($this->config->get('ebay_connector_default_category'));
									$product_condition 	= array();
									$category_type			= 'default';
							}else{
	            	$import_products['error'][] = sprintf($this->language->get('error_category_not_mapped'), $EbayProductCategoryName).' to import ebay item '.$item_data['Title'];
	            	continue;
							}
            }

            /**
             * [$params save ebay product's specification as opencart product attribute]
             * @var array
             */
            $params = array(
								'Version' 			=> 1039,
								'DetailLevel' 	=> 'ReturnAll',
								'ItemID' 				=> $item_data['ItemID'],
								'IncludeItemSpecifics' => true);
    				$results = $getEbayClient->GetItem($params);

	    		if(isset($results->Ack) && $results->Ack == 'Success'){
	    			if(isset($results->Item->ItemSpecifics->NameValueList) && $category_type == 'mapped'){
	    				foreach ($results->Item->ItemSpecifics->NameValueList as $key => $attribute) {
								$catCode = '';
	    					if(isset($attribute->Value) && $attribute->Value != ''){
										$catCode 					= str_replace(' ', '_', $oc_ebay_Category[0]['name']);
										$catCode 					= preg_replace('/[^A-Za-z0-9\_]/', '', $catCode);
										$ocCatCode 				= substr(strtolower($catCode), 0, 10);

										$attributeCode 		= str_replace(' ', '_', $attribute->Name);
										$attributeCode 		= preg_replace('/[^A-Za-z0-9\_]/', '', $attributeCode);
										$ocSpecifCode 		= substr('ebay_'.$ocCatCode.'_'.strtolower($attributeCode), 0, 45);

						        $getSpecificationEntry = $this->model_ebay_map_ebay_map_category->__getEbay_SpecificationInfo($ocSpecifCode);

						        if(is_array($attribute->Value)){
						        	$attribute->Value = implode(",",$attribute->Value);
						        }

						        if(isset($getSpecificationEntry['attr_group_id'])){
						        	$getAttrValue = $this->__checkSpecificationValue(array('attr_group_id' => $getSpecificationEntry['attr_group_id'], 'attr_code' => $ocSpecifCode, 'attr_name' => $attribute->Value));
						        	if(isset($getAttrValue) && $getAttrValue && !empty($getAttrValue)){
						        		$product_attribute[] = $this->createAttributeArray($getAttrValue, $attribute, $languages);
						        	}else{
						        		//create attribute
						        		$this->createAttribute($getSpecificationEntry['attr_group_id'], $attribute, $languages);

						        		$getAttrValue = $this->__checkSpecificationValue(array('attr_group_id' => $getSpecificationEntry['attr_group_id'], 'attr_code' => $ocSpecifCode, 'attr_name' => $attribute->Value));
						        		if(isset($getAttrValue) && $getAttrValue && !empty($getAttrValue)){
							        		$product_attribute[] = $this->createAttributeArray($getAttrValue, $attribute, $languages);
							        	}
						        	}
						        }else{
						        	//create group
						        	foreach ($languages as $key => $language) {
						        		$attribute_grp['attribute_group_description'][$language['language_id']] = array('name' => $attribute->Name);
						        		$attribute_grp['sort_order'] = 0;
						        	}
						        	// save attribute group
						        	$arribute_grp_id = $this->model_catalog_attribute_group->addAttributeGroup($attribute_grp);

						        	$this->db->query("INSERT INTO ".DB_PREFIX."wk_specification_map SET `attr_group_id` = '".(int)$arribute_grp_id."', `valuetype` = 'Text', `oc_category_id` = '".(int)$oc_category_id."', `ebay_category_id` = '".$ebayProductCategoryId."', `ebay_category_name` = '".$this->db->escape($EbayProductCategoryName)."', `ebay_specification_code` = '".$this->db->escape($ocSpecifCode)."' ");

						        	$getSpecificationEntry = $this->model_ebay_map_ebay_map_category->__getEbay_SpecificationInfo($ocSpecifCode);

						        	if($arribute_grp_id && isset($getSpecificationEntry['attr_group_id'])){
						        		//create attribute
						        		$this->createAttribute($arribute_grp_id, $attribute, $languages);

						        		$getAttrValue = $this->__checkSpecificationValue(array('attr_group_id' => $getSpecificationEntry['attr_group_id'], 'attr_code' => $ocSpecifCode, 'attr_name' => $attribute->Value));
						        		if(isset($getAttrValue) && $getAttrValue && !empty($getAttrValue)){
								        	$product_attribute[] = $this->createAttributeArray($getAttrValue, $attribute, $languages);
								        }
						        	}
						        }
						    }
	    				}
	    			}

						//  multiple image import for products
						 if(isset($results->Item->PictureDetails->PictureURL) && is_array($results->Item->PictureDetails->PictureURL) ){
								 foreach ($results->Item->PictureDetails->PictureURL as $key => $variation_image) {
													// if($key != 0){ // value of o index is treated as default images commebnt for client only
														array_push($product_images, $this->__saveproductImage($variation_image));
													// }
									}
						 }
            // Adding manufacturer_id to t
						$manufacturer_id = 0;

						 if(isset($results->Item->ProductListingDetails)){
							 if(isset($results->Item->ProductListingDetails->BrandMPN->Brand)){
								$upc = isset($results->Item->ProductListingDetails->UPC) ? $results->Item->ProductListingDetails->UPC : '';
								$mpn   = isset($results->Item->ProductListingDetails->BrandMPN->MPN)? $results->Item->ProductListingDetails->BrandMPN->MPN : '';
								$brand = $results->Item->ProductListingDetails->BrandMPN->Brand;
								$brands = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE name = '" . $this->db->escape($brand) . "'")->row;
								if(isset($brands['manufacturer_id'])){
									$manufacturer_id = $brands['manufacturer_id'];
								} else {
									$manufacturer_id = $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($brand) . "'");
								}
							}
						}


						// code for product multiple images
						if(isset($results->Item->Variations->Pictures->VariationSpecificPictureSet)){
								if(is_array($results->Item->Variations->Pictures->VariationSpecificPictureSet)){
										foreach ($results->Item->Variations->Pictures->VariationSpecificPictureSet as $key => $variation_image) {
												if(isset($variation_image->PictureURL) && !is_array($variation_image->PictureURL)){
														array_push($product_images, $this->__saveproductImage($variation_image->PictureURL));
												}else if(isset($variation_image->PictureURL) && is_array($variation_image->PictureURL)){
														foreach ($variation_image->PictureURL as $key => $var_image) {
																array_push($product_images, $this->__saveproductImage($var_image));
														}
												}
										}
								}else{
										if(isset($results->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL) && !is_array($results->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL)){
											array_push($product_images, $this->__saveproductImage($results->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL));
										}else if(isset($results->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL) && is_array($results->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL)){
												foreach ($results->Item->Variations->Pictures->VariationSpecificPictureSet->PictureURL as $key => $product_picture) {
														array_push($product_images, $this->__saveproductImage($product_picture));
												}
										}
								}
						}
	    		}

					/**
					 * catculate product weight
					 */
					 $product_weight = 0;
					 $product_weight_class_id = 0;
					 $product_dimentions = array();
					 if(isset($item_data['ShippingDetails']['CalculatedShippingRate'])){
							 $ebayItemWeight = $item_data['ShippingDetails']['CalculatedShippingRate'];
							 if (is_array($ebayItemWeight)) {
									 foreach ($ebayItemWeight as $key => $weight) {
											 if (isset($weight['unit']) && $weight['unit'] == 'lbs') {
													 $product_weight = $weight['_'];
													 $product_weight_class_id = 5;
											 }
											 if (isset($weight['unit']) && $weight['unit'] == 'oz') {
													 $product_weight = $product_weight + $weight['_']/16;
													 $product_weight_class_id = 5;
											 }

											 if($key == 'any') {
												    $response = '<a>'.$weight.'</a>';
												    $dom = new DOMDocument('1.0', 'UTF-8');
														$dom->loadXml($response);
														$x = $dom->documentElement;
	                          $lengthClassId = 0;
														$product_weight_class_name = '';
														foreach ($x->childNodes AS $item) {
															if(!$lengthClassId) {
																$lengthClassId = $item->getAttribute('unit');
															}
															if($item->nodeName == 'WeightMajor'){
																$product_weight_class_name = $item->getAttribute('unit');
															}
														  $product_dimentions[$item->nodeName] = $item->nodeValue;
														}
											 }
									 }
							 }
							 $product_weight = $product_weight ? $product_weight : 1;
					 }
					 if(isset($product_weight_class_name)){
					 	if($product_weight_class_name == 'kg'){
					 		 $product_weight_class_id = 1;
					 	} else if($product_weight_class_name == 'gm'){
					 			$product_weight_class_id = 2;
					 	} else if($product_weight_class_name == 'oz'){
					 			 $product_weight_class_id = 5;
					 	}
					 }

	        $pro_lenght_class_id = 1;

					if(isset($lengthClassId)){
						 if($lengthClassId == 'cm'){
							 $pro_lenght_class_id = 1;
						 } else if($lengthClassId == 'ml'){
							 $pro_lenght_class_id = 2;
						 } else if($lengthClassId == 'inch'){
							 $pro_lenght_class_id = 3;
						}
					}

	        if (isset($item_data['SKU'])) {
	            $sku = $item_data['SKU'];
	        } else {
	            $sku = $item_data['ItemID'];
	        }

				if (isset($item_data['SellingStatus']['QuantitySold'])) {
					$product_quantity = $item_data['Quantity'] - $item_data['SellingStatus']['QuantitySold'];
				} else {
					$product_quantity = $item_data['Quantity'];
				}

				$product_quantity = $this->Ebayconnector->calculateQuantity($product_quantity, 'import');

        $price =  $this->currency->convert($item_data['SellingStatus']['CurrentPrice']['_'],$item_data['SellingStatus']['CurrentPrice']['currencyID'], $this->config->get('config_currency'));

				$price = $this->Ebayconnector->calculatePrice($price, 'import');

        foreach ($languages as $key => $language) {
					$product_description[$language['language_id']] = array(
						'name' 				=> $this->validateTags($item_data['Title']),
						'description' => $this->validateTags(isset($item_data['Description']) ? $item_data['Description'] : $item_data['Title']),
						'meta_title' 	=> $this->validateTags($item_data['Title'])
					);
        }

        $defaultImage['image'] = $ebay_defaultImage = '';
        $i = 0;

				if (isset($item_data['PictureDetails']['PictureURL'])) {
					// $lastKey = end(array_keys($item_data['PictureDetails']['PictureURL']));
          if (!is_array($item_data['PictureDetails']['PictureURL'])) {
            if ($i == 0) {
              $defaultImage = $this->__saveproductImage($item_data['PictureDetails']['PictureURL']);
              $ebay_defaultImage = $item_data['PictureDetails']['PictureURL'];
            }
          } else {
            foreach ($item_data['PictureDetails']['PictureURL'] as $key => $value) {
              if ($key == $i) {
              	$defaultImage = $this->__saveproductImage($value);
              	$ebay_defaultImage = $value;
              }
            }
          }
        }

        $product_options = $product_option_value = array();

        if (!empty($product_option)) {
        	$option_id = false;
        	foreach ($product_option as $key => $option) {
        		$getVariationEntry = $this->__getVariationEntry($option['option_value']);

        		if (!empty($getVariationEntry) && $option['option_value'] == $getVariationEntry['value_name']) {
        			$option_id = $getVariationEntry['card_id'];
        			$opt_price = $this->currency->convert($option['opt_price']['price'],$option['opt_price']['currency'], $this->config->get('config_currency'));

        			if ((float)$opt_price >(float)$price) {
								$option_price = (float)$opt_price - (float)$price;
								$prefix = '+';
							} else {
								$option_price = (float)$price - (float)$opt_price;
								$prefix = '-';
							}

        			$product_option_value[$getVariationEntry['value_id']] = array(
								'option_value_id'	=> $getVariationEntry['value_id'],
          			'quantity'				=> $option['quantity'],
          			'price'						=> $option_price,
          			'subtract'				=> 1,
          			'price_prefix' 		=> $prefix
							);
        		}
        	}

					$product_options[] = array(
      			'name'				=> 'Variations',
      			'type'				=> 'select',
      			'option_id'		=> $option_id,
      			'required'		=> 1,
      			'product_option_value' => $product_option_value
					);
        }

      $product_array = array();

			if ($product_quantity) {
				$stock_status_id = 7;
			} else {
				$stock_status_id = 5;
			}

			$product_array = array(
					'ItemID'							=> $item_data['ItemID'],
					'account_id'					=> $account_id,
					'model'            		=> $item_data['ItemID'],
					'sku'              		=> $sku,
					'location'         		=> $item_data['Location'],
					'quantity'         		=> $product_quantity,
					'stock_status_id'  		=> $stock_status_id,
					'image'            		=> $defaultImage['image'],
					'ebay_image'      		=> $ebay_defaultImage,
					'price'            		=> (float)$price,
					'tax_class_id'     		=> 0,
					'weight'           		=> (isset($product_dimentions['WeightMajor']) && $product_dimentions['WeightMajor']) ?$product_dimentions['WeightMajor']:$product_weight,
					'weight_class_id'  		=> $product_weight_class_id,
					'subtract'         		=> 1,
					'minimum'          		=> 1,
					'sort_order'       		=> 1,
					'mpn'       		      => (isset($mpn) && $mpn) ? $mpn : '',
					'upc'       		      => (isset($upc) && $upc) ? $upc : '',
					'manufacturer_id'     => (isset($manufacturer_id) && $manufacturer_id) ? $manufacturer_id : '',
					'status'           		=> 1,
					'shipping'         		=> 1,
					'category_id'					=> $oc_category_id,
					'product_description' => $product_description,
					'product_category'		=> $product_category,
					'product_image'				=> $product_images,
					'product_attribute'		=> $product_attribute,
					'product_option'			=> $product_options,
					'product_condition'		=> $product_condition,
					'product_store'				=> $stores,
					'length'			      	=> (isset($product_dimentions['PackageLength']) && $product_dimentions['PackageLength']) ?$product_dimentions['PackageLength']:0,
					'width'				        => (isset($product_dimentions['PackageWidth']) && $product_dimentions['PackageWidth']) ?$product_dimentions['PackageWidth']:0,
					'height'				      => (isset($product_dimentions['PackageDepth']) && $product_dimentions['PackageDepth']) ?$product_dimentions['PackageDepth']:0,
					'length_class_id'				=> $pro_lenght_class_id,
				);

				if(!empty($getMappedEntry) && isset($getMappedEntry) && isset($getMappedEntry['product_id']) && $getMappedEntry['product_id']){
					$this->deleteRepeatedProducts($product_array['sku'], $getMappedEntry['product_id']);
				} elseif(isset($product_array['sku']) && $product_array['sku']) {
					$this->deleteRepeatedProducts($product_array['sku'], 0);
				}

				$getMappedEntry = $this->__getItemRecord(array('item_id' => $product_array['ItemID'], 'oc_category_id' => $product_array['category_id']), $product_array['account_id']);

				if (!empty($getMappedEntry) && isset($getMappedEntry)) {
					if (isset($getMappedEntry['product_id']) && $getMappedEntry['product_id']) {
						$product_array['product_id'] = $getMappedEntry['product_id'];
						$import_products['success'][] = $this->__editEbayProduct($product_array);
					} else {
							$this->deleteMapProducts(array('map_id' => $getMappedEntry['id'], 'account_id' => $getMappedEntry['account_id']));
							$import_products['success'][] = $this->__saveEbayProduct($product_array);
					}
				} else {
					$getProductId = $this->checkForSKU($product_array['ItemID']);
					if ($getProductId) {
						$product_array['product_id'] 	= $getProductId;
						$product_array['action_type'] = true;
						$import_products['success'][] = $this->__editEbayProduct($product_array);
					} else {
						$import_products['success'][] = $this->__saveEbayProduct($product_array);
					}
				}
		}

		return $import_products;
	}

	public function checkForSKU($ebay_product_sku){
			$oc_product_id = false;
			$getProductEntry = $this->db->query("SELECT * FROM ".DB_PREFIX."product WHERE sku = '".$this->db->escape($ebay_product_sku)."' ")->row;
			if(!empty($getProductEntry) && isset($getProductEntry['product_id'])){
					$oc_product_id = $getProductEntry['product_id'];
			}
			return $oc_product_id;
	}

	public function createAttributeArray($getAttrValue, $attribute, $languages){
		$attr_description = array();
		foreach ($languages as $key => $language) {
    	$attr_description[$language['language_id']] = array(
    		'text' => $attribute->Value
			);
    }
		return array(
					'name' => $attribute->Name,
					'attribute_id' => $getAttrValue['attribute_id'],
					'product_attribute_description' => $attr_description);
	}

	public function createAttribute($attr_group_id, $attribute, $languages){
		//create attrbute
		$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$attr_group_id . "', sort_order = '0'");
		$attribute_id = $this->db->getLastId();

		foreach ($languages as $key => $language) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language['language_id'] . "', name = '" . $this->db->escape($attribute->Value) . "'");
		}
	}

	public function __saveEbayProduct($data){
		$addResult = array();
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");

		$product_id = $this->db->getLastId();

		/**
		 * [ price rule changes]
		 * @var [starts]
		 */

		if(!$this->config->get('ebay_connector_price_rules')){

		  $price_map_rules = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id =".(int)$product_id."");
		  $param['price'] = $price_map_rules->row['price'];
		  $param['product_id'] = $product_id;
      $this->log->write('calling import_map conroller with values');
      $this->log->write($param);
		  $this->load->controller('price_rules/import_map',$param);
		}

		 /**
		 * [price rule changes]
		 * @var [ends]
		 */

		if($product_id){
			$product_check = $this->db->query("SELECT * FROM ". DB_PREFIX . "wk_ebay_oc_product_map WHERE oc_product_id=".$product_id)->row;
			if(isset($product_check['oc_product_id']) && $product_check['oc_product_id']){
				$this->db->query("DELETE FROM ". DB_PREFIX . "wk_ebay_oc_product_map WHERE oc_product_id='".$product_id."' AND ebay_product_id='".$this->db->escape($data['ItemID'])."'");
			}
			$this->db->query("INSERT INTO " . DB_PREFIX . "wk_ebay_oc_product_map SET oc_product_id = '" . $product_id . "', ebay_product_id = '" . $this->db->escape($data['ItemID']) . "', oc_category_id = '" . (int)$data['category_id'] . "', oc_image = '" . $this->db->escape($data['image']) . "', ebay_image = '" . $this->db->escape($data['ebay_image']) . "', product_images = '', account_id = '".(int)$data['account_id']."', `sync_source` = 'Ebay Item' ");

			$map_product_id = $this->db->getLastId();

			if (isset($data['product_image'])) {
				$productImages = serialize($data['product_image']);
				$this->db->query("UPDATE " . DB_PREFIX . "wk_ebay_oc_product_map SET product_images = '" .$this->db->escape($productImages). "' WHERE oc_product_id = '" . (int)$product_id . "' AND ebay_product_id = '" . $this->db->escape($data['ItemID']) . "' AND id = '".(int)$map_product_id."' ");
			}

			if (isset($data['product_condition'])) {
				foreach ($data['product_condition'] as $key => $condition) {
					$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_product_to_condition WHERE oc_product_id = '".(int)$product_id."' AND condition_value_id = '".(int)$condition['condition_value_id']."' AND condition_id = '".(int)$condition['condition_id']."' ");

					$this->db->query("INSERT INTO " . DB_PREFIX . "wk_ebay_product_to_condition SET oc_product_id = '" . $product_id . "', oc_category_id = '" . (int)$condition['oc_category_id'] . "', condition_value_id = '".(int)$condition['condition_value_id']."', condition_id = '".(int)$condition['condition_id']."', value = '" . $this->db->escape($condition['value']) . "', name = '" . $this->db->escape($condition['name']) . "' ");
				}
			}
		}


		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape(str_replace("script", "idiocy", $value['description'])) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store['store_id'] . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '0', points_prefix = '+', weight = '0.00000000', weight_prefix = '+'");
						}
					}
				}
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '0'");
			}
		}

		$this->cache->delete('product');

		$addResult = array(
										'status'    => true,
										'state'			=> 'created',
										'product_id'=> $product_id,
									);
		return $addResult;
	}

	public function __editEbayProduct($data){
		$product_id = $data['product_id'];
		/**
     * [Ebay PRice Rules Code]
     * @var [starts]
     */

    if(!$this->config->get('ebay_connector_price_rules')){
      $param['price'] = $data['price'];
      $param['product_id'] = $product_id;
      $price = $this->load->controller('price_rules/import_map/edit',$param);
			$price_map_rules = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id =".(int)$product_id."")->row;
			$data['price'] = $price_map_rules['price'];
    }


    /**
     * [Ebay PRice Rules Code]
     * @var [ends]
     */
		$updateResult = array();
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE product_id = '".(int)$product_id."' ");

		if($product_id){
			if(isset($data['action_type']) && $data['action_type']){
				  $this->db->query("DELETE FROM " . DB_PREFIX . "wk_ebay_oc_product_map WHERE oc_product_id='".$product_id."' AND ebay_product_id='".$data['ItemID']."'");
					$this->db->query("INSERT INTO " . DB_PREFIX . "wk_ebay_oc_product_map SET oc_product_id = '" . $product_id . "', ebay_product_id = '" . $data['ItemID'] . "', oc_category_id = '" . (int)$data['category_id'] . "', oc_image = '" . $this->db->escape($data['image']) . "', ebay_image = '" . $this->db->escape($data['ebay_image']) . "', product_images = '', account_id = '".(int)$data['account_id']."', `sync_source` = 'Ebay Item' ");
			}else{
					$this->db->query("UPDATE " . DB_PREFIX . "wk_ebay_oc_product_map SET oc_category_id = '" . (int)$data['category_id'] . "', oc_image = '" . $this->db->escape($data['image']) . "', ebay_image = '" . $this->db->escape($data['ebay_image']) . "', product_images = '', account_id = '".(int)$data['account_id']."' WHERE oc_product_id = '".(int)$product_id."' AND ebay_product_id = '".$this->db->escape($data['ItemID'])."' ");
			}

			if (isset($data['product_image'])) {
				$productImages = serialize($data['product_image']);
				$this->db->query("UPDATE " . DB_PREFIX . "wk_ebay_oc_product_map SET product_images = '" .$this->db->escape($productImages). "' WHERE oc_product_id = '" . (int)$product_id . "' AND ebay_product_id = '" . $data['ItemID'] . "' ");
			}

			if (isset($data['product_condition'])) {
				foreach ($data['product_condition'] as $key => $condition) {
					$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_product_to_condition WHERE oc_product_id = '".(int)$product_id."' AND condition_value_id = '".(int)$condition['condition_value_id']."' AND condition_id = '".(int)$condition['condition_id']."' ");

					$this->db->query("INSERT INTO " . DB_PREFIX . "wk_ebay_product_to_condition SET oc_product_id = '" . (int)$product_id . "', oc_category_id = '" . (int)$condition['oc_category_id'] . "', condition_value_id = '".(int)$condition['condition_value_id']."', condition_id = '".(int)$condition['condition_id']."', value = '" . $this->db->escape($condition['value']) . "', name = '" . $this->db->escape($condition['name']) . "' ");
				}
			}
		}


		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}


		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("UPDATE " . DB_PREFIX . "product_description SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape(str_replace("script", "idiocy", $value['description'])) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store['store_id'] . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$getMappedOption = $this->db->query("SELECT ev.*, pov.product_option_id, pov.product_option_value_id FROM ".DB_PREFIX."wk_ebay_variation ev LEFT JOIN ".DB_PREFIX."product_option_value pov ON ((ev.card_id = pov.option_id) AND (ev.value_id = pov.option_value_id)) WHERE pov.product_id = '".(int)$product_id."' ")->rows;
		foreach ($getMappedOption as $key => $map_option) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "' AND product_option_id = '".(int)$map_option['product_option_id']."' ");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "' AND product_option_value_id = '".(int)$map_option['product_option_value_id']."' AND product_option_id = '".(int)$map_option['product_option_id']."' ");
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '0', points_prefix = '+', weight = '0.00000000', weight_prefix = '+'");
						}
					}
				}
			}
		}

		if(!isset($data['action_type'])){
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '0'");
			}
		}

		$this->cache->delete('product');

		$updateResult = array(
													'status'    => true,
													'state'			=> 'update',
													'product_id'=> $product_id,
												);
		return $updateResult;
	}


	public function __getItemRecord($data = array(), $account_id = false){
		$sql = "SELECT wk_ep.*, pd.name, p.product_id FROM ".DB_PREFIX."wk_ebay_oc_product_map wk_ep LEFT JOIN ".DB_PREFIX."product p on(wk_ep.oc_product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(wk_ep.oc_product_id = pd.product_id) WHERE 1 ";

		if(isset($data['map_id'])){
			$sql .= " AND wk_ep.id = '".(int)$data['map_id']."'";
		}

		if(isset($data['product_id'])){
			$sql .= " AND wk_ep.oc_product_id = '".(int)$data['product_id']."' AND p.product_id = '".(int)$data['product_id']."' ";
		}

		if(isset($data['item_id'])){
			$sql .= " AND wk_ep.ebay_product_id = '".$this->db->escape($data['item_id'])."' ";
		}

		if(isset($data['oc_category_id'])){
			$sql .= " AND wk_ep.oc_category_id = '".(int)$data['oc_category_id']."' ";
		}

		if($account_id){
			$sql .= " AND wk_ep.account_id = '".(int)$account_id."' ";
		}

		return $this->db->query($sql)->row;
	}

	/**
	 * [__getEbayConditionValue to get/check ebay condition and values]
	 * @param  [type] $condition_attr_code [description]
	 * @param  [type] $condition_value     [description]
	 * @return [type]                      [description]
	 */
	public function __getEbayConditionValue($condition_attr_code, $condition_value){
		$condition_value_result = array();
		$getConditionEntry = $this->db->query("SELECT pc.* FROM ".DB_PREFIX."wk_ebay_prod_condition pc LEFT JOIN ".DB_PREFIX."wk_ebaysync_categories sc ON((pc.ebay_category_id = sc.ebay_category_id) AND (pc.oc_category_id = sc.opencart_category_id) AND (pc.condition_attr_code = sc.pro_condition_attr)) WHERE pc.condition_attr_code = '".$this->db->escape($condition_attr_code)."' AND sc.pro_condition_attr = '".$this->db->escape($condition_attr_code)."' ")->row;

		if(isset($getConditionEntry) && $getConditionEntry){
			$getAll_ConditionValues = $this->__getConditionValues($getConditionEntry);

			foreach ($getAll_ConditionValues as $key => $cond_value) {
				if(strtolower($cond_value['value']) == strtolower($condition_value)){
					$condition_value_result[$condition_attr_code] = $cond_value;
				}
			}
		}
		return $condition_value_result;
	}

	/**
	 * [__getConditionValues to get all condition's values of condition_id]
	 * @param  array  $condition [description]
	 * @return [type]            [description]
	 */
	public function __getConditionValues($condition = array()){
		$sql = "SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition_value cv LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition c ON(cv.condition_value_id = c.id) WHERE 1 ";

		if(isset($condition['id'])){
			$sql .= "AND cv.condition_value_id = '".(int)$condition['id']."' AND c.id = ".(int)$condition['id']." ";
		}

		if(isset($condition['condition_id'])){
			$sql .= "AND cv.condition_id = '".(int)$condition['condition_id']."' ";
		}

		$results = $this->db->query($sql)->rows;

		return $results;
	}

	/**
	 * [__createOpencartOptions to save product variation]
	 * @param  array  $ebay_variations [description]
	 * @return [type]                  [description]
	 */
	public function __createOpencartOptions($ebay_variations = array(), $languages){
		$variations = json_decode(json_encode($ebay_variations), true);

		$ocEbayVariation = array();
		try{
			$eBay_variations = $this->__arrangeVariationData($variations['Variation']);

			foreach ($eBay_variations as $opt_key => $variation) {
          $getVariation = $this->__getVariationEntry($variation['option_value']);

					if(empty($getVariation)){
						$variation_EntryId = $this->__save_EbayVariation($variation, $languages);
					}
			}

		} catch (\Exception $e) {
				$this->log->write('Create Ebay_Varition to Opencart Store : '.$e->getMessage());
        $eBay_variations = array();
    }
    return $eBay_variations;
	}

	/**
	 * [__arrangeVariationData format the ebay varition data (option/variation[Name][] => option/variation[Value])
	 * @param  array  $variationValues [description]
	 * @return [type]                  [description]
	 */
	public function __arrangeVariationData($variationValues = array()){
 		$options = array();
 		$variationValues = isset($variationValues[0]) ? $variationValues : array($variationValues);

 		foreach ($variationValues as $key => $variation) {
 				$opt_value = array();
 				$make_option_value = '';

 				$varNameValue = $variation['VariationSpecifics']['NameValueList'];
 				if(isset($varNameValue[0]) && $varNameValue[0]){
 						foreach ($varNameValue as $key => $varition_val) {
 								if($varition_val['Value'] == ''  || $varition_val['Name'] == ''){
 									continue;
 								}
 								if(isset($opt_value['option_value']) && $opt_value['option_value'] != ''){
 										$opt_value['option_value'] = $make_option_value = $make_option_value.'-'.$varition_val['Value'];
 								}else{
 									 	$opt_value['option_value'] = $make_option_value = $varition_val['Value'];
 								}
 								$opt_value['option_name'][] 	= $varition_val['Name'];
 						}

 						if(isset($variation['SellingStatus']['QuantitySold'])){
 								$opt_value['quantity'] 	= $variation['Quantity'] - $variation['SellingStatus']['QuantitySold'];
 						}else{
 								$opt_value['quantity'] 	= $variation['Quantity'];
 						}
 						$opt_value['opt_price'] 		= array('price' => $variation['StartPrice']['_'], 'currency' => $variation['StartPrice']['currencyID']);
 						$options[] 									= $opt_value;
 				}else{
 						if($varNameValue['Value'] == ''  || $varNameValue['Name'] == ''){
 							continue;
 						}
 						if(isset($opt_value['option_value']) && $opt_value['option_value'] != ''){
 							$opt_value['option_value'] 	= $make_option_value = $make_option_value.'-'.$varNameValue['Value'];
 						}else{
 							$opt_value['option_value'] 	= $make_option_value = $varNameValue['Value'];
 						}
 						if(isset($variation['SellingStatus']['QuantitySold'])){
 								$opt_value['quantity'] 		= $variation['Quantity'] - $variation['SellingStatus']['QuantitySold'];
 						}else{
 								$opt_value['quantity'] 		= $variation['Quantity'];
 						}
 						$opt_value['option_name'][] 	= $varNameValue['Name'];
 						$opt_value['opt_price'] 			= array('price' => $variation['StartPrice']['_'], 'currency' => $variation['StartPrice']['currencyID']);
 						$options[] 										= $opt_value;
 				}
 		}
 		return $options;
 	}
	/**
	 * [__getVariationEntry to get/check variation entry]
	 * @param  boolean $variation_code [description]
	 * @return [type]                  [description]
	 */
	public function __getVariationEntry($varition_value = false){
		$result = array();
		if($varition_value){
			if($getGlobalOption = $this->__getOcGlobalOption()){
				$result = $this->db->query("SELECT wkv.* FROM ".DB_PREFIX."wk_ebay_variation wkv LEFT JOIN ".DB_PREFIX."option_value ov ON((wkv.card_id = ov.option_id) AND (wkv.value_id = ov.option_value_id)) LEFT JOIN ".DB_PREFIX."option op ON(wkv.card_id = op.option_id) WHERE wkv.value_name = '".$this->db->escape($varition_value)."' AND wkv.card_id = '".(int)$getGlobalOption['option_id']."' AND ov.option_id = '".(int)$getGlobalOption['option_id']."' AND op.option_id = '".(int)$getGlobalOption['option_id']."' ")->row;
			}
		}
		return $result;
	}

	/**
	 * [__save_EbayVariation to save/create the ebay variation option entry to oc table]
	 * @param  [type] $variation_code [description]
	 * @param  [type] $option_name    [description]
	 * @param  array  $option_values  [description]
	 * @return [type]                 [description]
	 */
	public function __save_EbayVariation($option = array(), $languages){
		$ebay_OptVar_Id = false;
		if(!empty($option) && ($getGlobal_Option = $this->__getOcGlobalOption())){
			if (isset($option['option_value'])) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "option_value SET option_id = '" . (int)$getGlobal_Option['option_id'] . "', image = '" . $this->db->escape(html_entity_decode('', ENT_QUOTES, 'UTF-8')) . "', sort_order = '0'");

				$option_value_id = $this->db->getLastId();

				foreach ($languages as $key => $language) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "option_value_description SET option_value_id = '" . (int)$option_value_id . "', language_id = '".(int)$language['language_id']."', option_id = '" . (int)$getGlobal_Option['option_id'] . "', name = '" . $this->db->escape($option['option_value']) . "'");
				}
				try {
						$variation_label = serialize($option['option_name']);
            $this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_variation SET `card_id` = '".(int)$getGlobal_Option['option_id']."', `value_id` = '".(int)$option_value_id."',  `value_name` = '".$this->db->escape($option['option_value'])."', `label` = '".$this->db->escape($variation_label)."' ");

						$ebay_OptVar_Id = $this->db->getLastId();
        } catch (\Exception $e) {
            $this->log->write('Save Varition to Opencart Store : '.$e->getMessage());
        }
			}
		}
		return $ebay_OptVar_Id;
	}

	public function __getOcGlobalOption(){
		$result = $this->db->query("SELECT * FROM ".DB_PREFIX."option o LEFT JOIN ".DB_PREFIX."option_description od ON(o.option_id = od.option_id) WHERE od.name = 'Variations' ")->row;

		return $result;
	}

	public function __saveproductImage($imageURL = false){
		$result = array();
		if($imageURL && $this->checkImageExist($imageURL)){
			$imageURLUpdate = str_replace('$_1', '$_10', $imageURL);
			$path = DIR_IMAGE.'catalog/ebay_connector/';
            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }
			$getExt 		= explode('.', $imageURLUpdate);
	    	$getExtvalue 	= $getExt[count($getExt) -1];
	    	$checkExtention	= strtolower(current(explode('?', $getExtvalue)));

	    	$allowed = array();
			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

			$filetypes = explode("\n", $extension_allowed);
			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if(in_array($checkExtention, $allowed) && ($checkExtention !== 'php')){
				$getNamevalue 	= $getExt[count($getExt) - 2];
				$makeName 		= explode('/', $getNamevalue);
		    	$imageName 		= array_slice($makeName,-2,2);
		    	$name 			= implode("_",$imageName);

				file_put_contents($path.$name.'.'.$checkExtention, file_get_contents($imageURLUpdate));
				return $result = array('image' => 'catalog/ebay_connector/'.$name.'.'.$checkExtention, 'ebay_image' => $imageURL);
			}
		}
	}

	function checkImageExist($url) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if (curl_exec($ch) !== FALSE) {
					return true;
			} else {
					return false;
			}
	}

	public function deleteMapProducts($data = array()){
		$result = false;
		if(isset($data['map_id']) && isset($data['account_id'])){
			$getMapEntry = $this->__getItemRecord(array('map_id' => $data['map_id'], $data['account_id']));

			if(!empty($getMapEntry) && isset($getMapEntry['id']) && $getMapEntry['id'] == $data['map_id']){
				/**
				 * Product delete from ebay store also
				 */
				if($this->config->get('ebay_connector_ebay_item_delete') ){
					$getResult = $this->GetItem(array('ebay_product_id' => $getMapEntry['ebay_product_id'], 'account_id' => $data['account_id']));

					if((isset($getResult['relist']) && !$getResult['relist']) && (isset($getResult['error']) && !$getResult['relist']) ){
						$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($data['account_id']);
						if(isset($getEbayClient) && $getEbayClient){
							$params = 	[
										'EndingReason' 	=> 'NotAvailable',
										'ItemID' 				=> $getMapEntry['ebay_product_id'],
										'SKU'						=> isset($getResult['sku']) ? $getResult['sku'] : '',
										'ErrorLanguage' => 'en_US',
										'Version'				=> 1039,
										'WarningLevel'	=> 'High',
									];

								$error_message = '';
				        $results = $getEbayClient->EndFixedPriceItem($params);

				        if(isset($results->Errors) && $results->Errors){
				        		$error_message .= $results->Errors->LongMessage;
				        }else if(isset($results->Ack) && ((string)$results->Ack == 'Success' || (string)$results->Ack == 'Warning')){
				        		$error_message = 'Product also deleted from ebay store!';
				        }
					    }
					}
				}else if($this->config->get('ebay_connector_oc_product_delete') && $getMapEntry['sync_source'] == 'Ebay Item'){
					$this->db->query("DELETE FROM ".DB_PREFIX."product WHERE product_id = '".(int)$getMapEntry['oc_product_id']."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_description WHERE product_id = '".(int)$getMapEntry['oc_product_id']."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_option WHERE product_id = '".(int)$getMapEntry['oc_product_id']."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_attribute WHERE product_id = '".(int)$getMapEntry['oc_product_id']."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_option_value WHERE product_id = '".(int)$getMapEntry['oc_product_id']."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_image WHERE product_id = '".(int)$getMapEntry['oc_product_id']."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_to_category WHERE product_id = '".(int)$getMapEntry['oc_product_id']."' ");
					$this->db->query("DELETE FROM ".DB_PREFIX."product_to_store WHERE product_id = '".(int)$getMapEntry['oc_product_id']."' ");

					$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_product_to_condition WHERE oc_product_id = '".(int)$getMapEntry['oc_product_id']."' ");
				}

				$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_oc_product_map WHERE oc_product_id = '".(int)$getMapEntry['oc_product_id']."' ");

				$result = true;
			}
		}
		return $result;
	}

	public function __checkSpecificationValue($data = array()){
		$result = array();
		if(isset($data['attr_group_id'])){
			$sql = "SELECT * FROM ".DB_PREFIX."attribute a LEFT JOIN ".DB_PREFIX."attribute_description ad ON (a.attribute_id = ad.attribute_id) LEFT JOIN ".DB_PREFIX."wk_specification_map sm ON(a.attribute_group_id = sm.attr_group_id) WHERE 1 ";

			if(isset($data['attr_group_id'])){
				$sql .= " AND a.attribute_group_id = '".(int)$data['attr_group_id']."' AND sm.attr_group_id = '".(int)$data['attr_group_id']."' ";
			}

			if(isset($data['attribute_id'])){
				$sql .= " AND a.attribute_id = '".(int)$data['attribute_id']."' AND ad.attribute_id = '".(int)$data['attribute_id']."' ";
			}

			if(isset($data['attr_name'])){
				$sql .= " AND ad.name = '".$this->db->escape($data['attr_name'])."' ";
			}

			if(isset($data['attr_code'])){
				$sql .= " AND sm.ebay_specification_code = '".$this->db->escape($data['attr_code'])."' ";
			}

			$result = $this->db->query($sql)->row;
		}
		return $result;
	}

	/**
	 * [__saveOpencartProductData assign ebay specification and condition to opencart product]
	 * @param  boolean $product_id [description]
	 * @param  array   $data       [description]
	 * @return [type]              [description]
	 */
	public function __saveOpencartProductData($product_id = false, $data = array()){

		if($product_id){
			$this->load->model('localisation/language');

			$languages = $this->model_localisation_language->getLanguages();

			$product_template_id = 0;

			$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_template_to_product WHERE product_id = '".(int)$product_id."' ");

			if(isset($data['product_ebay_template']) && $data['product_ebay_template']){
					$this->load->model('ebay_map/ebay_template_listing');
					$getTemplateEntry = $this->model_ebay_map_ebay_template_listing->__getTemplateListing($data['product_ebay_template']);
					if (!empty($getTemplateEntry)) {
							$product_template_id = $this->model_ebay_map_ebay_template_listing->__addProductToTemplate($product_id, $getTemplateEntry);
					}
			}

			if(isset($data['scheduling_type'])){

			$scheduling=$this->db->query("SELECT id from ".DB_PREFIX."wk_ebay_product_schedule where oc_product_id='".$product_id."'")->row;
			if (isset($scheduling['id']) && $scheduling!='')
			{
				if ($data['scheduling_type']=='fix') {
					$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_product_schedule SET  scheduling_type='".$data['scheduling_type']."' ,scheduling_date='', scheduling_time='' WHERE oc_product_id='".$product_id."'");

				} else {
					$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_product_schedule SET  scheduling_type='".$data['scheduling_type']."',scheduling_date='".date('Y-m-d', strtotime($data['scheduling_date']))."', scheduling_time='".$data['scheduling_time']."' WHERE oc_product_id='".$product_id."'");
				}

				}else{
					if($data['scheduling_type']=='fix'){
						$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_product_schedule SET oc_product_id='".$product_id."', scheduling_type='".$data['scheduling_type']."'");

					}else{
						$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_product_schedule SET oc_product_id='".$product_id."', scheduling_type='".$data['scheduling_type']."',scheduling_date='".date('Y-m-d', strtotime($data['scheduling_date']))."', scheduling_time='".$data['scheduling_time']."'");
					}

				}

		}

			if(isset($data['product_specification']) && $data['product_specification']){
				foreach ($data['product_specification'] as $attr_group_id => $attribute_id) {
					if(isset($attribute_id) && $attribute_id){
						$getAttributeEntry = $this->__checkSpecificationValue(array('attr_group_id' => $attr_group_id, 'attribute_id' => $attribute_id));

						if (isset($getAttributeEntry) && $getAttributeEntry) {
							// Removes duplicates
							$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "'");

							foreach ($languages as $key => $language) {
								$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$attribute_id . "' AND language_id = '" . (int)$language['language_id'] . "'");

								$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language['language_id'] . "', text = '" .  $this->db->escape($getAttributeEntry['name']) . "'");
							}
						}
					}
				}
			}

			if(isset($data['product_condition']) && $data['product_condition']){
				foreach ($data['product_condition'] as $cond_id => $condition) {
					if(isset($condition) && $condition){
						$getCondition = explode('_',$condition);

						if(isset($getCondition[1]) && $getCondition[1]){
							$getConditionEntry = $this->__getConditionValues(array('id' => $cond_id, 'condition_id' => $getCondition[1]));

							if(isset($getConditionEntry[0]) && $getConditionEntry[0]){
								$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_product_to_condition WHERE oc_product_id = '".(int)$product_id."' AND condition_value_id = '".(int)$cond_id."' AND condition_id = '".(int)$getConditionEntry[0]['condition_id']."' ");

								$this->db->query("INSERT INTO " . DB_PREFIX . "wk_ebay_product_to_condition SET oc_product_id = '" . $product_id . "', oc_category_id = '" . (int)$getConditionEntry[0]['oc_category_id'] . "', condition_value_id = '".(int)$cond_id."', condition_id = '".(int)$getConditionEntry[0]['condition_id']."', value = '" . $this->db->escape($getConditionEntry[0]['value']) . "', name = '" . $this->db->escape($getConditionEntry[0]['name']) . "' ");
							}
						}
					}
				}
			}

			if (isset($data['product_variation_value'])) {
				foreach ($data['product_variation_value'] as $key => $product_option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '1'");
					$product_option_id = $this->db->getLastId();

					if (isset($product_option['option_value'])) {
						foreach ($product_option['option_value'] as $key1 => $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$key1 . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '1', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '0', points_prefix = '+', weight = '0', weight_prefix = '+'");
						}
					}
				}
			}
		}

		return $product_template_id;
	}

	public function __reviseEbayItem($product_id = false, $data = array()){
		$response 			= array();
		$type_status 		= false;

		$SetValues = $SetValueList = [];

		$this->load->language('ebay_map/ebay_map_product');
		$getMapEntry = $this->getProducts(array('filter_oc_product_id' => $product_id));

		if($this->config->get('ebay_connector_product_tax')){
    	$price = $this->tax->calculate($data['price'], $data['tax_class_id'], $this->config->get('config_tax'));
    }else{
    	$price = $data['price'];
    }

		$getConfDefaultSetting 	= $this->Ebayconnector->getConfigDefaultSetting();

		if(isset($getConfDefaultSetting['Country'])){
			$this->load->model('localisation/country');
			$getCountry = $this->model_localisation_country->getCountry($getConfDefaultSetting['Country']);
			$country_code = $getCountry['iso_code_2'];
		}else{
			$country_code = 'US';
		}

		if(!empty($getMapEntry)){
			if(isset($data['quantity']) && is_numeric($data['quantity']) && $data['quantity']<=0 && $this->config->get('ebay_connector_ebay_item_delete_quantity')){
			$result_return=$this->deleteOpencartProductWithEbay($product_id);
			$this->log->write($result_return);
}else {
			$this->load->model('ebay_map/export_product_to_ebay');
			foreach ($getMapEntry as $key => $map_product) {

				/**
				* [ price rule changes]
				* @var [starts]
				*/
	      if(isset($data['isConfigChange']) && $data['isConfigChange']) {
					$status_loop = FALSE;
				} else{
					$status_loop = TRUE;
				}

			 if($status_loop) {
					$getChanges = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "ebay_price_rules_map_product WHERE rule_product_id =".(int)$product_id."");
					$params['price'] = $data['price'];
					$params['product_id'] = $product_id;

					if(!$this->config->get('ebay_connector_price_rules') && ($map_product['sync_source'] == 'Ebay Item')){
						if ($getChanges->num_rows) {
							 if ($getChanges->row['change_type']) {
									 $data['price'] -= $getChanges->row['change_price'];
							 } else {
									 $data['price'] += $getChanges->row['change_price'];
							 }
						}
						$params['price'] = $data['price'];

						$this->load->controller('price_rules/import_map/realtime_update',$params);
						$price_map_rules = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id =".(int)$product_id."")->row;
						$map_product['StartPrice'] 	=	$price = $data['price'] = $price_map_rules['price'];
					} else if($this->config->get('ebay_connector_price_rules')) {
						$this->load->model('price_rule/export_map');
            $product_price = 0;
						if ($getChanges->num_rows) {
							 $product_price = $this->model_price_rule_export_map->_realtimeUpdatePriceRule($params);
						} else {
							$product_price = $this->model_price_rule_export_map->_applyPriceRule($params);
						}

						$map_product['StartPrice'] 	=	$price = $data['price'] = (int)$product_price;
					}
			 }


				/**
				* [price rule changes]
				* @var [ends]
				*/

				$getTotalQuantity 	= $getMinimumOptionPrice = 0;
				$map_product['StartPrice'] 	=	$price;
				$map_product['Location'] 		= $country_code;

				if(isset($map_product['ebay_product_id']) && $map_product['ebay_product_id'] && ($map_product['oc_product_id'] == $product_id)){

			    if(isset($data['product_category']) && !empty($data['product_category']) && in_array($map_product['oc_category_id'], $data['product_category'])) {
						$getCatMappedEntry = $this->model_ebay_map_export_product_to_ebay->getMappedcategory($data['product_category'], $map_product['account_id']);

						if(!empty($getCatMappedEntry)){//need update product entry for mapped category

							$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($map_product['account_id']);
							if(isset($getEbayClient) && $getEbayClient){
								$category_condition_id 					= '1000';
								$category_condition_value_id 		= '';
								$category_condition_description = 'New';

								$getConditions = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition WHERE oc_category_id = '".(int)$getCatMappedEntry['opencart_category_id']."' AND ebay_category_id = '".$this->db->escape($getCatMappedEntry['ebay_category_id'])."' ")->row;

								if(isset($getConditions) && $getConditions){
									if(isset($data['product_condition'][$getConditions['id']]) && $data['product_condition'][$getConditions['id']]){
										$condition_value_id = explode('_', $data['product_condition'][$getConditions['id']]);

										$getConditionValue = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition pc LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition_value pcv ON (pc.id = pcv.condition_value_id) WHERE pcv.condition_value_id = '".(int)$getConditions['id']."' AND pcv.condition_id = '".$condition_value_id[1]."' ")->row;

										if(isset($getConditionValue['value'])){
											$category_condition_id 		    = $getConditionValue['condition_id'];
											$category_condition_description = $getConditionValue['value'];
										}
									}
								}
								$product_variation 	= [];

								//check for configrable product or not. If product has variations then config otherwise simple.
								if(isset($data['product_variation']) && $data['product_variation'] && !empty($data['product_variation_value'])){

						        	foreach ($data['product_variation_value'] as $key => $variation) {
						        		foreach ($variation['option_value'] as $option_value => $variation_values) {
						        			$variation_values['label'] = '';
						        			$getLabel = $this->db->query("SELECT label FROM ".DB_PREFIX."wk_ebay_variation WHERE card_id = '".(int)$variation['option_id']."' AND value_id = '".(int)$option_value."' ")->row;
						        			if(isset($getLabel) && $getLabel){
						        				$variation_values['label'] = unserialize($getLabel['label']);
						        			}
						        			$option_price = 0;
						        			if($this->config->get('ebay_connector_product_tax')){
						        				$option_price = $this->tax->calculate($variation_values['price'], $data['tax_class_id'], $this->config->get('config_tax') ? 'P' : false);
						        			}else{
						        				$option_price = $variation_values['price'];
						        			}

						        			if(isset($variation_values['price_prefix']) && $variation_values['price_prefix'] == '+'){
						        				$optionPrice = (float)$price + (float)$option_price;
						        			}else{
						        				$optionPrice = (float)$price - (float)$option_price;
						        			}
						        			//total product quantity
						        			$getTotalQuantity = $getTotalQuantity + $variation_values['quantity'];

						        			//get minimum option price for product
						        			if($getMinimumOptionPrice == 0){
														$getMinimumOptionPrice = $optionPrice;
						        			}else if($optionPrice < $getMinimumOptionPrice){
						        				$getMinimumOptionPrice = $optionPrice;
						        			}

						        			$option_name = $NameValueList = [];
						        			$option_name = explode('-', $variation_values['name']);
					        				foreach ($option_name as $key => $opt_value) {
					        					$NameValueList[] = ['Name' 	=> $variation_values['label'][$key],
					        										'Value' => $opt_value,];
					        				}
					        				foreach ($NameValueList as $key => $variation_name_value) {
					        					if(isset($SetValueList[$variation_name_value['Name']])){
					        						array_push($SetValueList[$variation_name_value['Name']], $variation_name_value['Value']);
					        					}else{
					        						$SetValueList[$variation_name_value['Name']] = [$variation_name_value['Value']];
					        					}
					        				}
					        				$type_status = true;
						        			$product_variation[] = [
																'SKU' 			=> $data['product_description'][$this->config->get('config_language_id')]['name'].'-'.$option_value,
																'StartPrice' 	=> $optionPrice,
																'Quantity' 		=> $variation_values['quantity'] ? $variation_values['quantity'] : $data['quantity'],
	                    					'VariationSpecifics' => ['NameValueList' => $NameValueList],
													];
						        		}
						        	}

									  $params = [
				                        'Version' 					=> 1039,
				                        'Item' 							=> [
				                            'ItemID' 						=> $map_product['ebay_product_id'],
				                            'Title' 						=> $data['product_description'][$this->config->get('config_language_id')]['name'],
				                            'Variations' 				=> ['Variation' 	=> $product_variation],
				                            'PrimaryCategory' 	=> ['CategoryID'	=> $getCatMappedEntry['ebay_category_id']],
				                            'ConditionDescription'	=> $category_condition_description,
				                            'ConditionID'				=> $category_condition_id,
				                        ],
				                        'WarningLevel' 				=> 'High'
					                    ];
	                  $data['price'] 			= $getMinimumOptionPrice;
	                  $data['quantity'] 	= $getTotalQuantity;
									}else{
										$prod_description 	=	$prod_descriptionMode =  '';
										$getProductTemplate = $this->getProductTemplate($map_product, $data);
										if(isset($getProductTemplate['description'])){
												$prod_description =	$prod_descriptionMode =  $getProductTemplate['description'];
										}
										//simple product without variations
										$params = [
	                        'Version' 			=> 1039,
	                        'Item' => [
	                            'ItemID' 					=> $map_product['ebay_product_id'],
	                            'Title' 					=> $data['product_description'][$this->config->get('config_language_id')]['name'],
	                            'StartPrice' 			=> $price,
															'Description' 		=> $prod_description,
															'DescriptionMode' => $prod_descriptionMode,
	                            'Currency' 				=> $this->config->get('config_currency'),
	                            'Quantity' 				=> $data['quantity'],
															'ProductListingDetails' => [
															 'EAN'			=>	(isset($data['ean']) && $data['ean'])  ? $data['ean'] : 5025657000512,
															 'ISBN'					=> (isset($data['isbn']) && $data['isbn'])  ? $data['isbn'] : 9780307338402,
														 ],
	                            'PrimaryCategory' => [
	                            		'CategoryID' 			=> $getCatMappedEntry['ebay_category_id'],
	                            ],
															'ConditionDescription'	=> $category_condition_description,
															'ConditionID'						=> $category_condition_id,
	                        ],
	                        'WarningLevel' 	  => 'High'
	                    ];
											if($this->getScheduleDate($product_id)){
												$params['Item']['ScheduleTime']=$this->getScheduleDate($product_id);
											}
									}

                  try {
                  	if($type_status){
                  		$results = $getEbayClient->ReviseFixedPriceItem($params);
                  	}else{
											// can be changed to $data['quantity'] == 0 for relisting of product
											$getResult = $this->GetItem(array('ebay_product_id' => $map_product['ebay_product_id'], 'account_id' => $map_product['account_id']));
											if( isset($getResult['relist']) && $getResult['relist'] ){
												return;
											}
                  		$results = $getEbayClient->ReviseItem($params);
                  	}

                   	if(isset($results->Ack) && ((string)$results->Ack == 'Success' || (string)$results->Ack == 'Warning')){
                   		$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_oc_product_map SET `oc_category_id` = '".(int)$getCatMappedEntry['opencart_category_id']."' WHERE oc_product_id = '".(int)$product_id."' AND ebay_product_id = '".$this->db->escape($map_product['ebay_product_id'])."' AND account_id = '".(int)$map_product['account_id']."' ");

                   		if(isset($getConditionValue['value'])){
                   			$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_product_to_condition SET `condition_value_id` = '".(int)$getConditionValue['condition_value_id']."', `condition_id` = '".(int)$getConditionValue['condition_id']."', `value` = '".$this->db->escape($getConditionValue['value'])."', `name` = '".$this->db->escape($getConditionValue['name'])."' WHERE oc_product_id = '".(int)$product_id."' AND oc_category_id = '".(int)$getCatMappedEntry['opencart_category_id']."' ");
                   		}

                   		$this->log->write('Product Id:'.$map_product['oc_product_id'].' Name : '.$data['product_description'][$this->config->get('config_language_id')]['name'].'updated on ebay(item id:'.$getMapEntry[0]['ebay_product_id'].') successfully!');
                   		$response = array(
												'error'   => false,
												'message' => 'Product Synced Updated!');
                    }else{
                    	$report_error = '';
                    	if (isset($results->Errors)) {
		                    if (is_object($results->Errors)) {
		                        $report_error = $results->Errors->ShortMessage.' ( '.$results->Errors->LongMessage.' ).';
		                    } else {
	                            foreach ($results->Errors as $key => $error_obj) {
	                                $report_error = $report_error.' '.($key+1).') '.$error_obj->ShortMessage.' ( '.$error_obj->LongMessage.' ).';
	                        	}
	                    	}
	                    }
	                    $response = array(
												'error'   => true,
												'message' => $report_error
											);
	                    $this->log->write('Error : '.$report_error);
                    }
	                } catch(\Exception $e) {
	                	$this->log->write($e->getMessage());
	                }
							}else{
								$response = array(
									'error'   => true,
									'message' => $this->language->get('error_connection'));
							}
						}else if($map_product['oc_category_id'] == $this->config->get('ebay_connector_default_category')){
							$response = array(
								'error'   => false,
								'message' => 'Not Synced on ebay!');
						}else{
							$response = array(
								'error'   => true,
								'message' => $this->language->get('error_category_sync_update'));
						}
					}else{
						$response = array(
							'error'   => true,
							'message' => $this->language->get('error_category_sync_update'));
					}
				}else{
					$response = array(
						'error'   => false,
						'message' => 'Not Synced on ebay!');
				}
			}
		}
	}

		return array('response' => $response, 'data' => $data);
	}

	// get product description according to selected listing template
	public function getProductTemplate($product_data = array(), $post_data = array()){
		$product_specification = array();
		$product_description	 = array();

		if(isset($post_data['product_ebay_template']) && $post_data['product_ebay_template']){
				$makeTemplateArray = array(
							'template_id'			=> $post_data['product_ebay_template'],
							'product_id'			=> $product_data['oc_product_id'],
							'Title'						=> $post_data['product_description'][$this->config->get('config_language_id')]['name'],
							'name'						=> $post_data['product_description'][$this->config->get('config_language_id')]['name'],
							'description'			=> $post_data['product_description'][$this->config->get('config_language_id')]['description'],
							'meta_title' 			=> $post_data['product_description'][$this->config->get('config_language_id')]['meta_title'],
							'model' 					=> $post_data['model'],
							'sku' 						=> isset($post_data['model']) ? $post_data['model'] : (isset($post_data['product_description'][$this->config->get('config_language_id')]['name']) ? $post_data['product_description'][$this->config->get('config_language_id')]['name'] : 'pro_sku_'.$product_data['oc_product_id']),
							'Location'				=> $post_data['location'] ? $post_data['location'] : $product_data['Location'],
							'StartPrice'			=> $product_data['StartPrice'],
							'Quantity'				=> $post_data['quantity'],
							'date_available' 	=> $post_data['date_available'],
							'weight' 					=> $post_data['weight'],
							'weight_class_id' => $post_data['weight_class_id'],
							'ConditionText' 	=> '',
							'ItemSpecifics' 	=> array(),
							'PictureDetails' 	=> array(),
				);

				//product_condition
				if(isset($post_data['product_condition']) && !empty($post_data['product_condition'])){
					$getConditionvalue = array_values(array_filter($post_data['product_condition']));
					$condition_result = $this->Ebayconnector->getEbayCondition(isset($getConditionvalue[0]) ? $getConditionvalue[0]: $getConditionvalue);
					if (isset($condition_result['condition_id']) && $condition_result['condition_id']) {
							$makeTemplateArray['ConditionText'] 	= $condition_result['value'];
					}
				}

				//get product specification(attributes)
				if(isset($post_data['product_specification']) && !empty($post_data['product_specification'])){
					foreach (array_filter($post_data['product_specification']) as $key => $attribute_id) {
						$getProductSpecification = $this->model_ebay_map_export_product_to_ebay->getProductCategorySpecification(array('product_id' => $product_data['oc_product_id'], 'attribute_id' => $attribute_id));

							if(isset($getProductSpecification['attribute_id']) && $getProductSpecification['attribute_id']){
								$product_specification[$getProductSpecification['product_id'].'_'.$getProductSpecification['attribute_id']] = [
										'Name' 	=> $getProductSpecification['attr_group_name'],
										'Value' => $getProductSpecification['text'] ? $getProductSpecification['text'] : ''
									];
							}
					}
					if(!empty($product_specification)){
							$makeTemplateArray['ItemSpecifics'] = array_values($product_specification);
					}
				}

				//get product image and related image array
				$makeTemplateArray['PictureDetails'] 	= $this->Ebayconnector->getProductImageURL(array('product_id' => $product_data['oc_product_id'], 'image' => $post_data['image'], 'related_images' => isset($post_data['product_image']) ? $post_data['product_image'] : array()));

				$product_description = $this->model_ebay_map_export_product_to_ebay->checkTemplateContent($makeTemplateArray);
		}else{
				$product_description['description'] = $this->model_ebay_map_export_product_to_ebay->validateTags(urldecode(html_entity_decode($post_data['product_description'][$this->config->get('config_language_id')]['description'])));
		}

		return $product_description;
	}

	public function GetItem($data = array(), $call_from_revise = false){
		$response = array();
		if(isset($_COOKIE['demo_timezone'])){
			$customer_Time_Zone = $_COOKIE['demo_timezone'];
		}else{
			$customer_Time_Zone = 'Asia/Kolkata';
		}

		$current_DateTime 		= new DateTime(null, new DateTimeZone($customer_Time_Zone));
		$get_current_DateTime 	= $current_DateTime->format('Y-m-d H:i:s');

		$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($data['account_id']);
			if(isset($getEbayClient) && $getEbayClient){
				$params = array(
					'Version' 			=> 1039,
					'DetailLevel' 	=> 'ReturnAll',
					'ItemID' 				=> $data['ebay_product_id'],
					);

		      $results = $getEbayClient->GetItem($params);

				if($call_from_revise){
        			return $results;
        		}
		        if(isset($results->Ack) && ((string)$results->Ack == 'Success' || (string)$results->Ack == 'Warning')){
		        	if(isset($results->Item->ListingDetails->EndingReason) && $results->Item->ListingDetails->EndingReason){

		        		$response = array(
									'error'   	=> false,
									'message' 	=> 'Need to Relist.',
									'relist' 	=> true);
							}else if(isset($results->Item->ListingDetails->EndTime) && $results->Item->ListingDetails->EndTime){
		        		$getEndDateTime = date_create($results->Item->ListingDetails->EndTime);
		        		$geteBaydate 	= date_format($getEndDateTime, 'Y-m-d H:i:s');
		        		if($get_current_DateTime > $geteBaydate){
		        			$response = array(
									'error'   	=> false,
									'message' 	=> 'Need to Relist.',
									'relist' 	=> true);
		        		}else{
		        			$response = array(
									'error'   	=> false,
									'message' 	=> 'Active Listing.',
									'relist' 	=> false,
									);
		        		}
		        	}else{
		        		$response = array(
									'error'   	=> false,
									'message' 	=> 'Active Listing.',
									'relist' 	=> false,
												);
		        		if(isset($results->Item->SKU)){
		        			$response['sku'] = $results->Item->SKU;
		        		}else{
		        			$response['sku'] = '';
		        		}
		        	}
		        }else{
		        	$report_error = '';
		        	if (isset($results->Errors)) {
		                if (is_object($results->Errors)) {
		                    $report_error = $results->Errors->ShortMessage;
		                } else {
		                    foreach ($results->Errors as $key => $error_obj) {
		                        $report_error = $report_error.' '.($key+1).') '.$error_obj->ShortMessage.' ';
		                	}
		            	}
		            }
		            $response = array(
									'error'   	=> true,
									'message' 	=> $report_error,
									'relist' 	=> false
								);
		            $this->log->write('Error : '.$report_error);
		        }
		    }else{
				$response = array(
					'error'   	=> true,
					'message' 	=> $this->language->get('error_connection'),
					'relist' 	=> false);
			}
		return $response;
	}

	public function __relistEbayItem($data = array()){
		$response = array();
		$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($data['account_id']);

		if(isset($getEbayClient) && $getEbayClient){
				$params = array(
					'Version' 			=> 1039,
					'DetailLevel' 	=> 'ReturnAll',
					'Item' 					=> [ 'ItemID' 		=> $data['ebay_product_id'], ]
					);

				if(isset($data['category_map_ebay_id'])){
					$params['Item']['PrimaryCategory']['CategoryID'] = $data['category_map_ebay_id'];
				}

        $results = $getEbayClient->RelistFixedPriceItem($params);

        if(isset($results->Ack) && ((string)$results->Ack == 'Success' || (string)$results->Ack == 'Warning')){
	        	$ebay_product_id = $results->ItemID;

	        	$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_oc_product_map SET `ebay_product_id` = '".$this->db->escape($ebay_product_id)."' WHERE oc_product_id = '".(int)$data['product_id']."' AND account_id = '".(int)$data['account_id']."' ");

	        	$response = array(
								'error'   => false,
								'message' => 'Product relisted on ebay site!');
	        }else{
	        	$report_error = '';
	        	if (isset($results->Errors)) {
	                if (is_object($results->Errors)) {
	                    $report_error = $results->Errors->ShortMessage;
	                } else {
	                    foreach ($results->Errors as $key => $error_obj) {
	                        $report_error = $report_error.' '.($key+1).') '.$error_obj->ShortMessage.' ';
	                	}
	            	}
	            }
	            $response = array(
								'error'   	=> true,
								'message' 	=> $report_error,
							);
	            $this->log->write('Error : '.$report_error);
	        }
	    }else{
			$response = array(
				'error'   	=> true,
				'message' 	=> $this->language->get('error_connection'),
			);
		}
		return $response;
	}

	// this function use when we  delete default catalog product also export in ebay then delete open cart as well as eBay product
	public function  deleteOpencartProductWithEbay($product_id){

		$getMapEntry = $this->__getItemRecord(array('product_id' => $product_id));
		if(!empty($getMapEntry) && $this->config->get('module_ebay_connector_ebay_item_delete')){

			$this->deleteMapProducts(array('map_id' => $getMapEntry['id'], 'account_id' => $getMapEntry['account_id']));
  }
}

	public function validateTags($data = ''){
		$textValidate = preg_replace("~<script[^<>]*>.*</script>~Uis", "", $data);
		return htmlspecialchars($textValidate, ENT_QUOTES);
	}
	public function getProducts_schedule($data = array()) {
		$sql = "SELECT pm.*, cd.name as oc_category_name, pd.name as product_name,ps.scheduling_date,ps.scheduling_time, p.model FROM ".DB_PREFIX."wk_ebay_oc_product_map pm LEFT JOIN ".DB_PREFIX."product p ON (pm.oc_product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(pm.oc_product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."category c ON(pm.oc_category_id = c.category_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(pm.oc_category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."wk_ebay_product_schedule ps ON(ps.oc_product_id = pm.oc_product_id) WHERE c.status = '1' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' AND pd.language_id = '".(int)$this->config->get('config_language_id')."' AND ps.scheduling_type='schedule'";

		if(!empty($data['filter_category_name'])){
			$sql .= " AND cd.name LIKE '%".$this->db->escape($data['filter_category_name'])."%' ";
		}

		if(!empty($data['filter_oc_product_name'])){
			$sql .= " AND LCASE(pd.name) LIKE '".$this->db->escape(strtolower($data['filter_oc_product_name']))."%' ";
		}

		if(!empty($data['filter_category_id'])){
			$sql .= " AND pm.oc_category_id ='".(int)$data['filter_category_id']."' ";
		}

		if(!empty($data['filter_oc_product_id'])){
			$sql .= " AND pm.oc_product_id ='".(int)$data['filter_oc_product_id']."' ";
		}

		if(!empty($data['filter_ebay_product_id'])){
			$sql .= " AND pm.ebay_product_id ='".$this->db->escape($data['filter_ebay_product_id'])."' ";
		}

		if (isset($data['filter_source_sync']) && $data['filter_source_sync'] !== '') {
			if($data['filter_source_sync'] == 'Ebay Item'){
					$sql .= " AND pm.sync_source = 'Ebay Item' ";
			}
			if($data['filter_source_sync'] == 'Opencart Product'){
					$sql .= " AND pm.sync_source = 'Opencart Product' ";
			}
		}

		if(!empty($data['filter_account_id'])){
			$sql .= " AND pm.account_id ='".(int)$data['filter_account_id']."' ";
		}
		$sort_data = array(
			'pm.id',
			'pm.oc_product_id',
			'pm.ebay_product_id',
			'pd.name',
			'a.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pm.id";
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
	public function getTotalEbayProducts_schedule($data = array()) {
		$sql = "SELECT COUNT(DISTINCT pm.id) as total FROM ".DB_PREFIX."wk_ebay_oc_product_map pm LEFT JOIN ".DB_PREFIX."product p ON (pm.oc_product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(pm.oc_product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."category c ON(pm.oc_category_id = c.category_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(pm.oc_category_id = cd.category_id) LEFT JOIN ".DB_PREFIX."wk_ebay_product_schedule ps ON(ps.oc_product_id = pm.oc_product_id) WHERE c.status = '1' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' AND pd.language_id = '".(int)$this->config->get('config_language_id')."' AND ps.scheduling_type='schedule'   ";

		if(!empty($data['filter_category_name'])){
			$sql .= " AND cd.name LIKE '%".$this->db->escape($data['filter_category_name'])."%' ";
		}

		if(!empty($data['filter_oc_product_name'])){
			$sql .= " AND LCASE(pd.name) LIKE '".$this->db->escape(strtolower($data['filter_oc_product_name']))."%' ";
		}

		if(!empty($data['filter_category_id'])){
			$sql .= " AND pm.oc_category_id ='".(int)$data['filter_category_id']."' ";
		}

		if(!empty($data['filter_oc_product_id'])){
			$sql .= " AND pm.oc_product_id ='".(int)$data['filter_oc_product_id']."' ";
		}

		if(!empty($data['filter_ebay_product_id'])){
			$sql .= " AND pm.ebay_product_id ='".$this->db->escape($data['filter_ebay_product_id'])."' ";
		}

		if (isset($data['filter_source_sync']) && $data['filter_source_sync'] !== '') {
			if($data['filter_source_sync'] == 'Ebay Item'){
					$sql .= " AND pm.sync_source = 'Ebay Item' ";
			}
			if($data['filter_source_sync'] == 'Opencart Product'){
					$sql .= " AND pm.sync_source = 'Opencart Product' ";
			}
		}

		if(!empty($data['filter_account_id'])){
			$sql .= " AND pm.account_id ='".(int)$data['filter_account_id']."' ";
		}

		$result = $this->db->query($sql)->row;
		return $result['total'];
	}

	public function  getScheduleProduct($product_id){
		$scheduling=$this->db->query("SELECT * from ".DB_PREFIX."wk_ebay_product_schedule where oc_product_id='".$product_id."'")->row;
		return $scheduling;
	}
	public function getScheduleDate($product_id){
		$schedule = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_product_schedule WHERE oc_product_id='".$product_id."'")->row;
		if(isset($schedule['scheduling_type']) && $schedule['scheduling_type']!='fix'){
			if(isset($schedule['scheduling_date']) && isset($schedule['scheduling_time'])){
				$date=$schedule['scheduling_date'].' '.$schedule['scheduling_time'];
				return gmdate("Y-m-d\TH:i:s\Z",strtotime($date));
			}else{
				return false;
			}

		}else{
			return false;
		}

	}
	public function reScheduleProduct($product_id){

							$date=$this->request->post['scheduling_date'].' '.$this->request->post['scheduling_time'];
							$schedule=gmdate("Y-m-d\TH:i:s\Z",strtotime($date));
		$getMapEntry = $this->__getItemRecord(array('product_id' => $product_id));

			if(!empty($getMapEntry)){
					$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($getMapEntry['account_id']);
					if(isset($getEbayClient) && $getEbayClient){
									$params = array(
										'Version' 			=> 659,
										'DetailLevel' 	=> 'ReturnAll',
										'Item' 					=> [ 'ItemID' 		=> $getMapEntry['ebay_product_id'], ]
										);


									 $params['Item']['ScheduleTime']=$schedule;


			           $results = $getEbayClient->ReviseItem($params);

					     if(isset($results->Ack) && ((string)$results->Ack == 'Success' || (string)$results->Ack == 'Warning')){
					$this->db->query("UPDATE ".DB_PREFIX."wk_ebay_product_schedule SET  scheduling_type='schedule' ,scheduling_date='".date('Y-m-d', strtotime($this->request->post['scheduling_date']))."', scheduling_time='".$this->request->post['scheduling_time']."' WHERE oc_product_id='".$product_id."'");

									return "<div class='alert alert-success'>Product id:".$product_id." Re-schedule successfully</div>";
								}else{

										return "<div class='alert alert-warning'>Product id:".$product_id." not  Re-schedule to eBay</div>";
								}

				 }
	 }
}
		public function cancelScheduleProduct($product_id){
						$getMapEntry = $this->__getItemRecord(array('product_id' => $product_id));

						if(!empty($getMapEntry) && $this->config->get('ebay_connector_ebay_item_delete')){

							$result=$this->deleteMapProducts(array('map_id' => $getMapEntry['id'], 'account_id' => $getMapEntry['account_id']));

							if($result){
								return "<div class='alert alert-success'>Product id: ".$product_id." successfully remove from ebay Listing!!</div>";
							}else{

								return "<div class='alert alert-warning'>Product id: ".$product_id." not remove from ebay Listing!!";
							}
					} else {
						return "<div class='alert alert-warning'>Product id: ".$product_id." not remove from ebay Listing !!";
					}

		}


	public function deleteRepeatedProducts($ebay_product_sku = '', $oc_product_id = 0) {

		$this->load->model('catalog/product');
		$query = $this->db->query("SELECT product_id FROM ".DB_PREFIX."product WHERE sku = '".$this->db->escape($ebay_product_sku)."' AND product_id <> " . (int)$oc_product_id);

		if($query->num_rows && count($query->rows) >= 1) {
			foreach ($query->rows as $product_id) {
				$this->deleteOpencartProductWithEbay((int)$product_id['product_id']);
				$this->model_catalog_product->deleteProduct((int)$product_id['product_id']);
			}
		}
	}

}
