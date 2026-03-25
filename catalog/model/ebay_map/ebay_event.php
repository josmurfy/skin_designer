<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapEbayEvent extends Model {

  /**
   * [_getUserAccount user account id of seller]
   * @param  [type] $userid [user account name userid]
   * @return [type]           [array]
   */
   public function _getUserAccount($userid) {
     $user = $this->db->query("SELECT * FROM " . DB_PREFIX . "wk_ebay_accounts WHERE ebay_connector_ebay_user_id = '".$this->db->escape($userid)."'");
     return $user->row;
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

 public function addAttributeGroup($data) {
   $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group SET sort_order = '" . (int)$data['sort_order'] . "'");

   $attribute_group_id = $this->db->getLastId();

   foreach ($data['attribute_group_description'] as $language_id => $value) {
     $this->db->query("INSERT INTO " . DB_PREFIX . "attribute_group_description SET attribute_group_id = '" . (int)$attribute_group_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
   }

   return $attribute_group_id;
 }

 public function __ItemClosed($item_data, $account_id){

   $import_products = array();

   $this->load->model('ebay_map/ebay_map_category');

   $this->load->model('setting/store');

   $stores = $this->model_setting_store->getStores();

   $this->load->model('localisation/language');

   $languages = $this->model_localisation_language->getLanguages();
    $getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($account_id);
    array_push($stores,array('store_id' => 0,'name' => 'Default','url' => HTTPS_SERVER, 'ssh' => ''));

       $item_data = json_decode((json_encode($item_data)), true);

       $params = array(
           'Version' 			=> 1039,
           'DetailLevel' 	=> 'ReturnAll',
           'ItemID' 				=> $item_data['ItemID'],
           'IncludeItemSpecifics' => true);
       $results = $getEbayClient->GetItem($params);
       if(isset($results->Item->SellingStatus->QuantitySold)){
        $product_quantity = $results->Item->Quantity - $results->Item->SellingStatus->QuantitySold;
      }else{
        $product_quantity = $results->Item->Quantity;
      }

       $product_category = array();

       $item_price 	= $results->Item->SellingStatus->CurrentPrice->_;

       $item_currency 	= $results->Item->SellingStatus->CurrentPrice->currencyID;

       $ebayProductCategoryId 	= $results->Item->PrimaryCategory->CategoryID;

       $EbayProductCategory 	  = explode(':', $results->Item->PrimaryCategory->CategoryName);

       $EbayProductCategoryName = $EbayProductCategory[count($EbayProductCategory) - 1];

       $oc_ebay_Category        = $this->model_ebay_map_ebay_map_category->getMapCategories(array('filter_ebay_category_id' => $ebayProductCategoryId, 'filter_account_id' => $account_id));

       $oc_category_id          = false;
       $category_type 	        = '';
       if(isset($oc_ebay_Category[0]['opencart_category_id']) && isset($oc_ebay_Category[0]['ebay_category_id'])){
           $oc_category_id 	= $oc_ebay_Category[0]['opencart_category_id'];
           $product_category = array($oc_category_id);
           $category_type		= 'mapped';
       } else {
           $oc_category_id 		= $this->config->get('ebay_connector_default_category');
           $product_category 	= array($this->config->get('ebay_connector_default_category'));
           $category_type			= 'default';
       }
       $getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($account_id);
       $getMappedEntry = $this->__getItemRecord(array('item_id' => $item_data['ItemID'], 'oc_category_id' => $oc_category_id), $account_id);

         if(!empty($getMappedEntry) && isset($getMappedEntry['id'])){


          if($this->config->get('ebay_connector_product_delete') && $product_quantity == 0){

            $this->db->query("DELETE FROM ".DB_PREFIX."product WHERE product_id = '".(int)$getMappedEntry['oc_product_id']."' ");
            $this->db->query("DELETE FROM ".DB_PREFIX."product_description WHERE product_id = '".(int)$getMappedEntry['oc_product_id']."' ");
            $this->db->query("DELETE FROM ".DB_PREFIX."product_option WHERE product_id = '".(int)$getMappedEntry['oc_product_id']."' ");
            $this->db->query("DELETE FROM ".DB_PREFIX."product_attribute WHERE product_id = '".(int)$getMappedEntry['oc_product_id']."' ");
            $this->db->query("DELETE FROM ".DB_PREFIX."product_option_value WHERE product_id = '".(int)$getMappedEntry['oc_product_id']."' ");
            $this->db->query("DELETE FROM ".DB_PREFIX."product_image WHERE product_id = '".(int)$getMappedEntry['oc_product_id']."' ");
            $this->db->query("DELETE FROM ".DB_PREFIX."product_to_category WHERE product_id = '".(int)$getMappedEntry['oc_product_id']."' ");
            $this->db->query("DELETE FROM ".DB_PREFIX."product_to_store WHERE product_id = '".(int)$getMappedEntry['oc_product_id']."' ");

            $this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_product_to_condition WHERE oc_product_id = '".(int)$getMappedEntry['oc_product_id']."' ");

            $this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_oc_product_map WHERE oc_product_id = '".(int)$getMappedEntry['oc_product_id']."' ");
          }else{
           $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = 0,status=0 WHERE product_id = '" . (int)$getMappedEntry['oc_product_id']);
          }

            $result = true;
         }

   return $result;
 }

 public function __realTimeProductManagement($ebay_products, $account_id){

   $import_products = array();

   $this->log->write('======__realTimeProductManagement====');

   $this->load->model('ebay_map/ebay_map_category');

   $this->load->model('setting/store');

   $stores = $this->model_setting_store->getStores();

   $this->load->model('localisation/language');

   $languages = $this->model_localisation_language->getLanguages();

   array_push($stores,array('store_id' => 0,'name' => 'Default','url' => HTTPS_SERVER, 'ssh' => ''));

    $getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($account_id);

    $item_data = $ebay_products;

    $item_data = json_decode((json_encode($item_data)), true);

    $product_category = $product_condition = $product_option = $product_attribute = $product_images = array();

    $params = array(
       'Version' 			=> 1045,
       'DetailLevel' 	=> 'ReturnAll',
       'ItemID' 				=> $item_data['ItemID'],
       'IncludeItemSpecifics' => true
    );

    $results = $getEbayClient->GetItem($params);

    if(isset($results->Ack) && $results->Ack == 'Success') {

           $item_price 	= $results->Item->SellingStatus->CurrentPrice->_;

           $item_currency 	= $results->Item->SellingStatus->CurrentPrice->currencyID;

           if (isset($item_data['Variations'])) {
               $product_option = $this->__createOpencartOptions($item_data['Variations'], $languages);
           }

           $ebayProductCategoryId 	= $results->Item->PrimaryCategory->CategoryID;

           $EbayProductCategory 	  = explode(':', $results->Item->PrimaryCategory->CategoryName);

           $EbayProductCategoryName = $EbayProductCategory[count($EbayProductCategory) - 1];

           $oc_ebay_Category        = $this->model_ebay_map_ebay_map_category->getMapCategories(array('filter_ebay_category_id' => $ebayProductCategoryId, 'filter_account_id' => $account_id));

           $oc_category_id          = false;
           $category_type 	        = '';

           if(isset($oc_ebay_Category[0]['opencart_category_id']) && isset($oc_ebay_Category[0]['ebay_category_id'])){
               $oc_category_id 	= $oc_ebay_Category[0]['opencart_category_id'];
               $product_category = array($oc_category_id);
               $category_type		= 'mapped';
               if(isset($oc_ebay_Category[0]['pro_condition_attr']) && $oc_ebay_Category[0]['pro_condition_attr'] != 'N/A'){
                   $ebay_ConditionValue = $results->Item->ConditionDisplayName;

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

             }
           }

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
                     $arribute_grp_id = $this->addAttributeGroup($attribute_grp);

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
                if(isset($results->Item->ProductListingDetails->UPC) && $results->Item->ProductListingDetails->UPC){
                  $upc = $results->Item->ProductListingDetails->UPC;
                } else{
                  $upc = '';
                }

               $brand = $results->Item->ProductListingDetails->BrandMPN->Brand;
               $brands = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer WHERE name = '" . $this->db->escape($brand) . "'")->row;
               if(isset($brands['manufacturer_id'])){
                 $manufacturer_id = $brands['manufacturer_id'];
               } else {
                 $manufacturer_id = $this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($brand) . "'");
               }
                if(isset($results->Item->ProductListingDetails->BrandMPN->MPN) && $results->Item->ProductListingDetails->BrandMPN->MPN){
                  $mpn = $results->Item->ProductListingDetails->BrandMPN->MPN;
                } else{
                  $mpn = '';
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
        if(isset($results->ShippingDetails->CalculatedShippingRate)){
            $ebayItemWeight = $results->ShippingDetails->CalculatedShippingRate;

            if (is_array($ebayItemWeight)) {
                foreach ($ebayItemWeight as $weight) {
                    if (isset($weight['unit']) && $weight['unit'] == 'lbs') {
                        $product_weight = $weight['_'];
                        $product_weight_class_id = 5;
                    }
                    if (isset($weight['unit']) && $weight['unit'] == 'oz') {
                        $product_weight = $product_weight + $weight['_']/16;
                        $product_weight_class_id = 5;
                    }
                }
            }

            $product_weight = $product_weight ? $product_weight : 1;
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

       if(isset($results->Item->SellingStatus->QuantitySold)){
           $product_quantity = $results->Item->Quantity - $results->Item->SellingStatus->QuantitySold;
       }else{
           $product_quantity = $results->Item->Quantity;
       }

       $price =  $this->currency->convert($results->Item->SellingStatus->CurrentPrice->_,$results->Item->SellingStatus->CurrentPrice->currencyID, $this->config->get('config_currency'));

       foreach ($languages as $key => $language) {
           $product_description[$language['language_id']] = array(
             'name' 				=> $this->validateTags($results->Item->Title),
             'description' => $this->validateTags(isset($results->Item->Description) ? $results->Item->Description : $item_data['Title']),
             'meta_title' 	=> $this->validateTags($results->Item->Title));
       }

       $defaultImage['image'] = $ebay_defaultImage = '';
       $i = 0;

       if (isset($results->Item->PictureDetails->PictureURL)) {
         // $lastKey = end(array_keys($results->PictureDetails->PictureURL));
           if (!is_array($results->Item->PictureDetails->PictureURL)) {
               if ($i == 0) {
                   $defaultImage = $this->__saveproductImage($results->Item->PictureDetails->PictureURL);
                   $ebay_defaultImage = $results->Item->PictureDetails->PictureURL;
               }
           } else {
               foreach ($results->Item->PictureDetails->PictureURL as $key => $value) {
                   if ($key == $i) {
                     $defaultImage = $this->__saveproductImage($value);
                     $ebay_defaultImage = $value;
                   }
               }
           }
       }

       $product_options = $product_option_value = array();
       if(!empty($product_option)){
         $option_id = false;
         foreach ($product_option as $key => $option) {
           $getVariationEntry = $this->__getVariationEntry($option['option_value']);

           if(!empty($getVariationEntry) && $option['option_value'] == $getVariationEntry['value_name']){
             $option_id = $getVariationEntry['variation_id'];
             $opt_price = $this->currency->convert($option['opt_price']['price'],$option['opt_price']['currency'], $this->config->get('config_currency'));

             if((float)$opt_price >(float)$price){
               $option_price = (float)$opt_price - (float)$price;
               $prefix = '+';
             }else{
               $option_price = (float)$price - (float)$opt_price;
               $prefix = '-';
             }
             $product_option_value[$getVariationEntry['value_id']] = array(
               'option_value_id'	=> $getVariationEntry['value_id'],
               'quantity'				=> $option['quantity'],
               'price'						=> $option_price,
               'subtract'				=> 1,
               'price_prefix' 		=> $prefix);
           }
         }
         $product_options[] = array(
             'name'				=> 'Variations',
             'type'				=> 'select',
             'option_id'		=> $option_id,
             'required'		=> 1,
             'product_option_value' => $product_option_value,);
       }
     $product_array = array();

     $product_array = array(
         'ItemID'							=> $results->Item->ItemID,
         'account_id'					=> $account_id,
         'model'            		=> $results->Item->ItemID,
         'sku'              		=> $sku,
         'location'         		=> $results->Item->Location,
         'quantity'         		=> $product_quantity,
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
         'stock_status_id'				=> 7,
       );

       $getMappedEntry = $this->__getItemRecord(array('item_id' => $product_array['ItemID'], 'oc_category_id' => $product_array['category_id']), $product_array['account_id']);

       if(!empty($getMappedEntry) && isset($getMappedEntry)){
         if(isset($getMappedEntry['product_id']) && $getMappedEntry['product_id']){
           $product_array['product_id'] = $getMappedEntry['product_id'];

           if($this->config->get('ebay_connector_syncproduct_status')) {
             $temp = array(
             'product_id' => $product_array['product_id'],
             'quantity'   => $product_array['quantity'],
             'price'      => $product_array['price'],
             'getMapWhenEvent' => true
             );

             if(!$this->registry->has('Ebaysyncproducts')) {
               $this->registry->set('Ebaysyncproducts', new Ebaysyncproducts($this->registry));
             }

             $this->Ebaysyncproducts->get_map_when_event($temp);
           }

           $import_products['success'][] = $this->__editEbayProduct($product_array);
         }else{
             $this->deleteMapProducts(array('map_id' => $getMappedEntry['id'], 'account_id' => $getMappedEntry['account_id']));
             $import_products['success'][] = $this->__saveEbayProduct($product_array);
         }
       }else{
         $getProductId = $this->checkForSKU($product_array['ItemID']);
          // $this->log->write('checkForSKU is ProductId =>'.$getProductId);
         if($getProductId){
             $product_array['product_id'] 	= $getProductId;
             $product_array['action_type'] = true;

             $import_products['success'][] = $this->__editEbayProduct($product_array);
         }else{
             $import_products['success'][] = $this->__saveEbayProduct($product_array);
         }
       }

  //  $this->log->write('Import Real Time Product Results =>'.$import_products);

   return $import_products;
 }

 public function __mapEbayOrder($eBayOrder = array(), $account_id = false){
       $item_shipping 	= 0;
       $shipMethod = '';
       $sync_result = $order_data = array();

       if (isset($eBayOrder['ShippingServiceSelected']['ShippingService'])) {
           $shipMethod .= $eBayOrder['ShippingServiceSelected']['ShippingService'];
       }

       $transactionList = $eBayOrder['TransactionArray']['Transaction'];

       if (!isset($transactionList[0])) {
           $transactionList = [0 => $transactionList];
       }
       $this->load->model('ebay_map/ebay_map_product');
       foreach ($transactionList as $transaction) {
         //get item shipping cost
           if (isset($transaction['ActualShippingCost']['_'])) {
               $item_shipping = $item_shipping + floatval($transaction['ActualShippingCost']['_']);
           }

           //get order buyer name
           if (isset($transaction['Buyer']['UserFirstName'])) {
               $firstname 	= $transaction['Buyer']['UserFirstName'];
               $lastname 	= $transaction['Buyer']['UserLastName'];
           }else{
               $firstname 	= 'Guest';
               $lastname 	= 'User';
           }

           //check ordered item entry in opencart(item is synced or not)
           $getItemSyncEntry = $this->getProducts(array('filter_ebay_product_id' => $transaction['Item']['ItemID']));

           $product_data = array();
           if (isset($getItemSyncEntry[0]['oc_product_id']) && $getItemSyncEntry[0]['oc_product_id']) {
             $product_data = $getItemSyncEntry[0];
               if(isset($transaction['Variation']['VariationSpecifics']['NameValueList'])){
                 $ebayItemvariations = $transaction['Variation']['VariationSpecifics']['NameValueList'];
                 if (!isset($ebayItemvariations[0])) {
                     $VariationList = [0 => $ebayItemvariations];
                 }
               }
               $orderItems[] = [
                         'product_id' => $product_data['oc_product_id'],
                         'name'       => $product_data['product_name'],
                         'model'      => $product_data['model'],
                         'option'     => '',
                         'quantity'   => $transaction['QuantityPurchased'],
                         'price'      => $this->currency->convert($transaction['TransactionPrice']['_'], $transaction['TransactionPrice']['currencyID'], $this->config->get('config_currency')),
                         'total'      => $this->currency->convert($transaction['TransactionPrice']['_'], $transaction['TransactionPrice']['currencyID'], $this->config->get('config_currency')) * $transaction['QuantityPurchased'],
                         'tax'        => 0,
                         'reward'     => 0
                 ];
           } else {
             $sync_result['error'] = array(
               'error_status'  => 1,
               'error_message' => 'eBay order id : <b> '.$eBayOrder['OrderID']." </b> not sync because Product <b> '" .$transaction['Item']['Title'].' '.$transaction['Item']['ItemID']."' </b> not Synced on Opencart. <br />",
               );
             return $sync_result;
           }
     }

   $getZoneId = array();
   $getCountryId['country_id'] = 0;
   if(isset($eBayOrder['ShippingAddress']['Country']) && $eBayOrder['ShippingAddress']['Country']){
     $getCountryId = $this->db->query("SELECT country_id,address_format FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($eBayOrder['ShippingAddress']['Country']) . "' ")->row;
   }

   if(isset($eBayOrder['ShippingAddress']['StateOrProvince']) && $eBayOrder['ShippingAddress']['StateOrProvince']){
     $getZoneId = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE code = '" . $this->db->escape($eBayOrder['ShippingAddress']['StateOrProvince']) . "' ")->row;
     if(!isset($getZoneId['zone_id'])){
         $getZoneId['zone_id'] = 0;
     }
   }
   $getCurrencyId = array();
   if(isset($eBayOrder['AdjustmentAmount']['currencyID']) && $eBayOrder['AdjustmentAmount']['currencyID']){
     $getCurrencyId = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->db->escape($eBayOrder['AdjustmentAmount']['currencyID']) . "'")->row;
   }
   if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
     $forwarded_ip = $this->request->server['HTTP_X_FORWARDED_FOR'];
   } elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
     $forwarded_ip = $this->request->server['HTTP_CLIENT_IP'];
   } else {
     $forwarded_ip = '';
   }

   if (isset($this->request->server['HTTP_USER_AGENT'])) {
     $user_agent = $this->request->server['HTTP_USER_AGENT'];
   } else {
     $user_agent = '';
   }
   if($this->config->get('pp_standard_status') && isset($eBayOrder['CheckoutStatus']['PaymentMethod']) && $eBayOrder['CheckoutStatus']['PaymentMethod'] == 'PayPal'){
     $payment_method = $eBayOrder['CheckoutStatus']['PaymentMethod'];
           $payment_code   = 'pp_standard';
   }else{
     $payment_method = $eBayOrder['CheckoutStatus']['PaymentMethod'];
           $payment_code   = 'cod';
   }


     $order_data = [
           'ebay_order_id' => $eBayOrder['OrderID'],
           'invoice_no'	=> 0,
           'invoice_prefix'=> $this->config->get('config_invoice_prefix'),
           'store_id'		=> $this->config->get('ebay_connector_ordersync_store'),
           'store_name'	=> $this->config->get('config_name'),
           'store_url'		=> $this->config->get('config_store_id') ? $this->config->get('config_url') : ($this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER),
           'order_status_id'=> $this->config->get('ebay_connector_order_status'),
           'currency_id' 	=> $getCurrencyId['currency_id'],
           'currency_code' => $getCurrencyId['code'],
           'currency_value'=> $getCurrencyId['value'],
           'firstname' 	=> $firstname,
           'lastname' 		=> $lastname,
           'email' 		=> $eBayOrder['SellerEmail'],
           'language_id' 	=> $this->config->get('config_language_id'),
           'shipping_address' => [
                   'shipping_firstname' 	=> $firstname,
                   'shipping_lastname' 	=> $lastname,
                   'shipping_address_1' 	=> $eBayOrder['ShippingAddress']['Street1'],
                   'shipping_address_2' 	=> $eBayOrder['ShippingAddress']['Street2'],
                   'shipping_city' 			=> $eBayOrder['ShippingAddress']['CityName'],
                   'shipping_postcode'		=> $eBayOrder['ShippingAddress']['PostalCode'],
                   'shipping_zone'				=> $eBayOrder['ShippingAddress']['StateOrProvince'],
                   'shipping_zone_id'		=> isset($getZoneId['zone_id']) ? $getZoneId['zone_id'] : 0,
                   'shipping_country'		=> $eBayOrder['ShippingAddress']['CountryName'],
                   'shipping_country_id'	=> $getCountryId['country_id'],
                   'shipping_address_format'=> $getCountryId['address_format'],
                   ],
           'payment_method' => $payment_method,
           'payment_code' 	 => $payment_code,
           'customer_group_id'	=> $this->config->get('config_customer_group_id'),
           'telephone' 	=> $eBayOrder['ShippingAddress']['Phone'],
           'fax' 			=> $eBayOrder['ShippingAddress']['Phone'],
           'items' 		=> $orderItems,
           'shipping_method'=> $shipMethod,
           'shipping_cost' => $item_shipping,
           'affiliate_id' 	=> 0,
           'commission' 	=> 0,
           'marketing_id' 	=> 0,
           'tracking' 		=> '',
           'user_agent'  	=> $user_agent,
           'forwarded_ip'  => $forwarded_ip,
           'ip' 			=> $this->request->server['REMOTE_ADDR'],
           'account_id' 	=> $account_id,
     ];

     $getOrderId = $this->saveOrder($order_data);
     $this->addOrderHistory($getOrderId, $this->config->get('ebay_connector_order_status'));

     if($getOrderId){
       $this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_order_map SET `oc_order_id` = '".(int)$getOrderId."', `ebay_order_id` = '".$this->db->escape($order_data['ebay_order_id'])."', `ebay_order_status` = '".$this->db->escape($eBayOrder['OrderStatus'])."', `sync_date` = NOW(), `account_id` = '".(int)$account_id."' ");
       $map_id = $this->db->getLastId();

       if($map_id){
         $sync_result['success'] = array(
           'success_status'  => 1,
           'success_message' => 'eBay order id : <b> '.$eBayOrder['OrderID']." </b> has been synchronized with opencart's order id : <b> '" .$getOrderId. "' </b>. <br />",
               );
       }
     }
     return $sync_result;
 }

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

 public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false){

   // Stock subtraction
   $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

   foreach ($order_product_query->rows as $order_product) {
     $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

     $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'");

     foreach ($order_option_query->rows as $option) {
       $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
     }
   }

   // Update the DB with the new statuses
   $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

   $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
 }

 public function saveOrder($data = array()){
   $this->load->model('account/customer');
   if(isset($data['email'])){
     $checkCustomerEntry = $this->model_account_customer->getCustomerByEmail($data['email']);
     if(!empty($checkCustomerEntry) && $checkCustomerEntry['customer_id']){
       $customer_id = $checkCustomerEntry['customer_id'];
     }else{
       $address 			= array();
       $address[0] 		= array(
                   'firstname' => $data['firstname'],
                   'lastname' 	=> $data['lastname'],
                   'company' 	=> '',
                   'address_1' => $data['shipping_address']['shipping_address_1'],
                   'address_2' => $data['shipping_address']['shipping_address_2'],
                   'city' 		=> $data['shipping_address']['shipping_city'],
                   'postcode' 	=> $data['shipping_address']['shipping_postcode'],
                   'country_id' => $data['shipping_address']['shipping_country_id'],
                   'zone_id' 	=> $data['shipping_address']['shipping_zone_id'],
                   'default'	=> true,
                   );

       $data['password'] 	= $data['shipping_address']['shipping_lastname'].'_'.$data['shipping_address']['shipping_firstname'];
       $data['status'] 	= $data['approved'] = 1;
       $data['safe'] 		= $data['newsletter'] = 0;
       $data['custom_field'] = '';
       $data['address'] 	= $address;
       $customer_id 		= $this->model_customer_customer->addCustomer($data);
     }
   }else{
     $customer_id = 0;
   }
   $order_total = $order_sub_total = 0;
   foreach ($data['items'] as $key => $product) {
     $order_sub_total 	+= $product['total'];
     // $order_total 		+= $product['total'];
   }
   $order_total = $order_sub_total + $data['shipping_cost'];

   $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$customer_id . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['firstname']) . "', payment_lastname = '" . $this->db->escape($data['lastname']) . "', payment_company = '', payment_address_1 = '" . $this->db->escape($data['shipping_address']['shipping_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['shipping_address']['shipping_address_2']) . "', payment_city = '" . $this->db->escape($data['shipping_address']['shipping_city']) . "', payment_postcode = '" . $this->db->escape($data['shipping_address']['shipping_postcode']) . "', payment_country = '" . $this->db->escape($data['shipping_address']['shipping_country']) . "', payment_country_id = '" . (int)$data['shipping_address']['shipping_country_id'] . "', payment_zone = '" . $this->db->escape($data['shipping_address']['shipping_zone']) . "', payment_zone_id = '" . (int)$data['shipping_address']['shipping_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['shipping_address']['shipping_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_address']['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_address']['shipping_lastname']) . "', shipping_company = '', shipping_address_1 = '" . $this->db->escape($data['shipping_address']['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address']['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_address']['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_address']['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_address']['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_address']['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_address']['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_address']['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address']['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_method']) . "', comment = '', total = '" . (float)$order_total . "', `order_status_id` = '".(int)$data['order_status_id']."', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', date_added = NOW(), date_modified = NOW()");

   $order_id = $this->db->getLastId();

   // Products
   if (isset($data['items'])) {
     foreach ($data['items'] as $product) {
       $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

       $order_product_id = $this->db->getLastId();

     }
   }
   $totals 	=	[array(
             'code'       => 'sub_total',
             'title'      => 'Sub-Total',
             'value'      => (float)$order_sub_total,
             'sort_order' => $this->config->get('sub_total_sort_order')
           ),
           array(
             'code'       => 'shipping',
             'title'      => $data['shipping_method'],
             'value'      => (float)$data['shipping_cost'],
             'sort_order' => $this->config->get('shipping_sort_order')
           ),
           array(
             'code'       => 'total',
             'title'      => 'Total',
             'value'      => (float)$order_total,
             'sort_order' => $this->config->get('total_sort_order')
           )];
   // Totals
   if (isset($totals)) {
     foreach ($totals as $total) {
       $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
     }
   }
   return $order_id;
 }

  public function updateItemQty($id, $results)
  {
        $count_array = array();
        if (isset($results->OrderArray->Order)) {
            $eBayOrders = json_decode(
                				json_encode($results->OrderArray->Order),
                				true
            			);
            $eBayOrders 	= isset($eBayOrders[0]) ? $eBayOrders : [0 => $eBayOrders];

            foreach ($eBayOrders as $key => $order) {
            	$getSyncResult = array();
            	if(is_array($order_ids) && in_array($order['OrderID'], $order_ids)){
            		$getSyncResult 	= $this->_ebayMapOrder->__mapEbayOrder($order, $account_id);
            		if(isset($getSyncResult['success'])){
            			$json['success'][] = array(
														'success_status'  => 1,
														'success_message' => $getSyncResult['success']['success_message']
														);
            			$count_success = $count_success + 1;
            		}
            		if(isset($getSyncResult['error'])){
            			$json['error'][] = array(
													'error_status'  => 1,
													'error_message' => $getSyncResult['error']['error_message']
													);

            		}
            	}
            }
        } else {
            if (isset($results->Ack) && $results->Ack != 'Success') {
            	$json['error'][] = array(
											'error_status'  => 1,
											'error_message' => $results->Errors->LongMessage
											);
                // break;
            }
        }
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
         		'text' => $attribute->Value,);
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

 		if($product_id){
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
       $price_map_rules = $this->db->query("SELECT price FROM " . DB_PREFIX . "product WHERE product_id =".(int)$product_id."");
       $param['price'] = $price_map_rules->row['price'];
       $param['product_id'] = $product_id;
       $this->load->controller('price_rules/import_map/edit',$param);
     }
     /**
      * [Ebay PRice Rules Code]
      * @var [ends]
      */
 		$updateResult = array();
 		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE product_id = '".(int)$product_id."' ");

 		if($product_id){
 			if(isset($data['action_type']) && $data['action_type']){
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

 		$getMappedOption = $this->db->query("SELECT ev.*, pov.product_option_id, pov.product_option_value_id FROM ".DB_PREFIX."wk_ebay_variation ev LEFT JOIN ".DB_PREFIX."product_option_value pov ON ((ev.variation_id = pov.option_id) AND (ev.value_id = pov.option_value_id)) WHERE pov.product_id = '".(int)$product_id."' ")->rows;
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
 				$result = $this->db->query("SELECT wkv.* FROM ".DB_PREFIX."wk_ebay_variation wkv LEFT JOIN ".DB_PREFIX."option_value ov ON((wkv.variation_id = ov.option_id) AND (wkv.value_id = ov.option_value_id)) LEFT JOIN ".DB_PREFIX."option op ON(wkv.variation_id = op.option_id) WHERE wkv.value_name = '".$this->db->escape($varition_value)."' AND wkv.variation_id = '".(int)$getGlobalOption['option_id']."' AND ov.option_id = '".(int)$getGlobalOption['option_id']."' AND op.option_id = '".(int)$getGlobalOption['option_id']."' ")->row;
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
             $this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_variation SET `variation_id` = '".(int)$getGlobal_Option['option_id']."', `value_id` = '".(int)$option_value_id."',  `value_name` = '".$this->db->escape($option['option_value'])."', `label` = '".$this->db->escape($variation_label)."' ");

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
 				if($this->config->get('ebay_connector_ebay_item_delete') && $getMapEntry['sync_source'] == 'Opencart Product'){
 					$getResult = $this->GetItem(array('ebay_product_id' => $getMapEntry['ebay_product_id'], 'account_id' => $data['account_id']));

 					if((isset($getResult['relist']) && !$getResult['relist']) && (isset($getResult['error']) && !$getResult['relist']) ){
 						$getEbayClient 	= $this->Ebayconnector->_eBayAuthSession($data['account_id']);
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


 	public function validateTags($data = ''){
 		$textValidate = preg_replace("~<script[^<>]*>.*</script>~Uis", "", $data);
 		return htmlspecialchars($textValidate, ENT_QUOTES);
 	}


}
