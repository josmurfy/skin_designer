<?php
class ModelCatalogEbayProductMap extends Model {

	public function getProducts($data = array()) {
		$sql = "SELECT pm.*, cd.name as oc_category_name, pd.name as product_name, p.model, p.tax_class_id, p.price FROM ".DB_PREFIX."wk_ebay_oc_product_map pm LEFT JOIN ".DB_PREFIX."product p ON (pm.oc_product_id = p.product_id) LEFT JOIN ".DB_PREFIX."product_description pd ON(pm.oc_product_id = pd.product_id) LEFT JOIN ".DB_PREFIX."category c ON(pm.oc_category_id = c.category_id) LEFT JOIN ".DB_PREFIX."category_description cd ON(pm.oc_category_id = cd.category_id) WHERE c.status = '1' AND cd.language_id = '".(int)$this->config->get('config_language_id')."' AND pd.language_id = '".(int)$this->config->get('config_language_id')."'   ";

		if(!empty($data['filter_oc_product_id'])){
			$sql .= " AND pm.oc_product_id ='".(int)$data['filter_oc_product_id']."' ";
		}

		return $this->db->query($sql)->row;
	}

	public function updateStockAteBay($order_info, $order_status, $comment = ''){
      $type_status 		= false;
  		$order_id       = $order_info['order_id'];
      $SetValueList   = [];
      $response 			= array();
  		$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'")->rows;
      foreach ($order_product_query as $key => $ordered_product) {
					$category_condition_id 					= '1000';
					$category_condition_value_id 		= '';
					$category_condition_description = 'New';
      		$getMapEntry = $this->getProducts(array('filter_oc_product_id' => $ordered_product['product_id']));

          if(!empty($getMapEntry) && isset($getMapEntry['ebay_product_id']) && $getMapEntry['ebay_product_id']){
              $getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($getMapEntry['account_id']);

              if(isset($getEbayClient) && $getEbayClient){

                    $getOrderProductOptionEntry = $this->db->query("SELECT * FROM ".DB_PREFIX."order_option oo LEFT JOIN ".DB_PREFIX."product_option_value pov ON ((oo.product_option_value_id = pov.product_option_value_id) AND (oo.product_option_id)) LEFT JOIN ".DB_PREFIX."wk_ebay_variation ev ON ((pov.option_id = ev.variation_id) AND (pov.option_value_id = ev.value_id)) WHERE `order_id` = '".(int)$ordered_product['order_id']."' AND `order_product_id` = '".(int)$ordered_product['order_product_id']."' ")->row;

                    $params = [
                              'Version'       => 1039,
                              'ItemID'        => $getMapEntry['ebay_product_id'],
                              'DetailLevel'   => 'ReturnAll'
                          ];

                    $eBayProductData = $getEbayClient->GetItem($params);


                    $ebayProductTotalQty = $eBayProductData->Item->Quantity;
                    $ebayProductSoldQty  = $eBayProductData->Item->SellingStatus->QuantitySold;
                    $ebayProductActualQty= $ebayProductTotalQty - ($ebayProductSoldQty + $ordered_product['quantity']);
										// $ebayProductActualQty become zero then delete it from ebay if these two setting is enabled

										if($ebayProductActualQty<=0 && $this->config->get('ebay_connector_ebay_item_delete') && $this->config->get('ebay_connector_ebay_item_delete_quantity')){

											$getResult = $this->GetItem(array('ebay_product_id' => $getMapEntry['ebay_product_id'], 'account_id' => $getMapEntry['account_id']));

											if((isset($getResult['relist']) && !$getResult['relist']) && (isset($getResult['error']) && !$getResult['relist']) ){
												$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($getMapEntry['account_id']);
												if(isset($getEbayClient) && $getEbayClient){
													$params = 	[
																'EndingReason' 	=> 'NotAvailable',
																'ItemID' 				=> $getMapEntry['ebay_product_id'],
																'SKU'						=> isset($getResult['sku']) ? $getResult['sku'] : '',
																'ErrorLanguage' => 'en_US',
																'Version'				=> 891,
																'WarningLevel'	=> 'High',
															];

														$error_message = '';
														$results = $getEbayClient->EndFixedPriceItem($params);

														// if(isset($results->Errors) && $results->Errors){
														// 		$this->log->write($results->Errors->LongMessage);
														// }else if(isset($results->Ack) && ((string)$results->Ack == 'Success' || (string)$results->Ack == 'Warning')){
														// 	$this->log->write('Product Id:'.$getMapEntry['oc_product_id'].' Name : '.$getMapEntry['product_name'].'deleted from ebay store!');
														//
														// }
													}
											}
											$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_oc_product_map WHERE oc_product_id = '".(int)$getMapEntry['oc_product_id']."' ");
										}else {
										// for quanity update on ebay site

										$getCatMappedEntry = $this->getMappedcategory(array($getMapEntry['oc_category_id']), $getMapEntry['account_id']);

										if(!empty($getCatMappedEntry) && isset($getCatMappedEntry)){
											$getConditions = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition WHERE oc_category_id = '".(int)$getCatMappedEntry['opencart_category_id']."' AND ebay_category_id = '".$this->db->escape($getCatMappedEntry['ebay_category_id'])."' ")->row;

											if(isset($getConditions) && $getConditions){
												if(isset($data['product_condition'][$getConditions['id']]) && $data['product_condition'][$getConditions['id']]){
													$condition_value_id = explode('_', $data['product_condition'][$getConditions['id']]);

													$getConditionValue = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebay_prod_condition pc LEFT JOIN ".DB_PREFIX."wk_ebay_prod_condition_value pcv ON (pc.id = pcv.condition_value_id) WHERE pcv.condition_value_id = '".(int)$getConditions['id']."' AND pcv.condition_id = '".$condition_value_id[1]."' ")->row;

													if(isset($getConditionValue['value'])){
														$category_condition_id 		    	= $getConditionValue['condition_id'];
														$category_condition_description = $getConditionValue['value'];
													}
												}
											}
										}

                    //simple mapped product ordered
                    $params_update = [
                                  'Version' 					=> 891,
                                  'Item' 						  => [
                                      'ItemID' 				      => $getMapEntry['ebay_product_id'],
                                      'Quantity'            => $ebayProductActualQty,
                                      'ProductListingDetails' => ['ISBN'				=> 9780307338402],
																			'ConditionDescription'	=> $category_condition_description,
																			'ConditionID'						=> $category_condition_id,
                                  ],
                                  'WarningLevel' 			 => 'High'
                              ];

                    $product_variation 	 = [];
                    if(!empty($getOrderProductOptionEntry) && isset($getOrderProductOptionEntry['value_id']) && $getOrderProductOptionEntry['value_id']){
                      //variation mapped product ordered
                        $type_status 		           = true;
                        $variation_label           = array();

                        if(isset($getOrderProductOptionEntry['label']) && $getOrderProductOptionEntry['label']){
                          $variation_label['label'] = unserialize($getOrderProductOptionEntry['label']);
                        }else{
                          $variation_label['label']  = '';
                        }

                        $option_price = 0;
                        if($this->config->get('ebay_connector_product_tax')){
                          $option_price = $this->tax->calculate($getOrderProductOptionEntry['price'], $getMapEntry['tax_class_id'], $this->config->get('config_tax') ? 'P' : false);
                        }else{
                          $option_price = $getOrderProductOptionEntry['price'];
                        }

                        if(isset($getOrderProductOptionEntry['price_prefix']) && $getOrderProductOptionEntry['price_prefix'] == '+'){
                          $optionPrice = (float)$getMapEntry['price'] + (float)$option_price;
                        }else{
                          $optionPrice = (float)$getMapEntry['price'] - (float)$option_price;
                        }

                        $option_name = $NameValueList = [];
                        $option_name = explode('-', $getOrderProductOptionEntry['value_name']);
                        foreach ($option_name as $key => $opt_value) {
                          $NameValueList[] = [
                                                'Name' 	=> $variation_label['label'][$key],
                                                'Value' => $opt_value,
                                              ];
                        }
                        foreach ($NameValueList as $key => $variation_name_value) {
                            if(isset($SetValueList[$variation_name_value['Name']])){
                                array_push($SetValueList[$variation_name_value['Name']], $variation_name_value['Value']);
                            }else{
                                $SetValueList[$variation_name_value['Name']] = [$variation_name_value['Value']];
                            }
                        }

                        if(isset($getOrderProductOptionEntry['subtract']) && $getOrderProductOptionEntry['subtract']){
                            $variationTotalQty = $getOrderProductOptionEntry['quantity'];
                        }else{
                            $variationTotalQty = $getOrderProductOptionEntry['quantity'] - $ordered_product['quantity'];
                        }

                        $product_variation[] = [
                                 'SKU' 			    => html_entity_decode($getMapEntry['product_name']).'-'.$getOrderProductOptionEntry['value_id'],
                                 'StartPrice' 	=> $optionPrice,
                                 'Quantity' 		=> $variationTotalQty,
                                 'VariationSpecifics' => ['NameValueList' => $NameValueList],
                        ];
                        $params_update['Item']['Variations'] = ['Variation' => $product_variation];

                    }

                    if($type_status){
                        unset($params_update['Item']['Quantity']);
                        $response = $getEbayClient->ReviseFixedPriceItem($params_update);
                    }else{
                        $response = $getEbayClient->ReviseItem($params_update);
                    }

                    if(isset($response->Ack) && ((string)$response->Ack == 'Success' || (string)$response->Ack == 'Warning')){
                        $this->log->write('Product Id:'.$getMapEntry['oc_product_id'].' Name : '.$getMapEntry['product_name'].'updated on ebay(item id:'.$params_update['Item']['ItemID'].') successfully!');
                    }
									}
              }
          }
      }
  }

  // for quanity update on ebay site

	public function getMappedcategory($data = array() , $account_id = false){
		$getEntry = array();
		foreach ($data as $key => $category_id) {
			$getMapEntry = $this->db->query("SELECT * FROM ".DB_PREFIX."wk_ebaysync_categories e_cat LEFT JOIN ".DB_PREFIX."category c ON (e_cat.opencart_category_id = c.category_id) WHERE e_cat.account_id = '".(int)$account_id."' AND e_cat.opencart_category_id = '".(int)$category_id."' ")->row;

			if(!empty($getMapEntry)){
				$getEntry = $getMapEntry;
				return $getEntry;
			}
		}

		return $getEntry;
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
					'Version' 			=> 659,
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
	// for quanity update on ebay site
}
