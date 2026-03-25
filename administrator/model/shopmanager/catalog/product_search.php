<?php
namespace Opencart\Admin\Model\Shopmanager\Catalog;

class ProductSearch extends \Opencart\System\Engine\Model {

    // ⚡ CACHE EN MÉMOIRE pour éviter requêtes eBay multiples
    private static $cache_search_data = [];
    private static $cache_info_sources = [];
    
    /**
     * ⚡ Vider le cache (utile pour forcer refresh)
     */
    public function clearCache() {
        self::$cache_search_data = [];
        self::$cache_info_sources = [];
    }
    
    // Pattern OC4: Utiliser directement les fonctions de product.php model
    // editProduct() et editDescription() supprimés - appeler model_shopmanager_catalog_product directement
        
    public function feedInfoWithSearchData($data_json) {
        $this->load->model('localisation/language');
        $this->load->model('shopmanager/tools');
        $this->load->model('shopmanager/shipping');
        $this->load->model('shopmanager/ai');
    
        $execution_times = [];
        $n = 0;
        $start_time = microtime(true);
        $data= json_decode($data_json,true);

        unset($data['product_search_data']['specifics_result']['specific_info']);
        $product_search_data = $data['product_search_data'];
        $specifics = $product_search_data ['specifics_result'];
        foreach($specifics as $key=>$value){
            if(isset($specifics[$key]['specific_info'])){
                unset($specifics[$key]['specific_info']);
            }else{
                unset($specifics[$key]);
            }
  
        }
       
       
        // 🔹 Application des valeurs de `product_search_data` si existantes
        $data['product_categories']=$data['product_categories']??[];
        $data['category_id'] = (!empty($data['category_id']) && $data['category_id'] != 0) 
                                ? $data['category_id'] 
                                : ($product_search_data ['category_id'] ?? 0);
    
        $data['product_category'] = (!empty($data['product_category'])) 
                                    ? $data['product_category'] 
                                    : [($product_search_data ['category_id'] ?? 0)];
    
        $data['category_name'] = (!empty($data['category_name'])) 
                                ? $data['category_name'] 
                                : ($product_search_data ['category_name'] ?? '');
    
        $data['ebay_info'] = (!empty($data['ebay_info'])) 
                                    ? $data['ebay_info'] 
                                    : ($product_search_data ['ebay_pricevariant'] ?? null);
    	
		$data['ebay_info']=$product_search_data ['ebay_search']?? null;
		$data['ebay_pricevariant']=$product_search_data ['ebay_pricevariant'];

        $data['ebay_category'] = (!empty($data['ebay_category'])) 
                                ? $data['ebay_category'] 
                                : ($product_search_data ['ebay_category'] ?? null);
    
        $data['model'] = (!empty($data['model']) && $data['model'] != 'n/a')
        ? (is_array($data['model']) ? reset($data['model']) : $data['model'])
        : (
            isset($specifics['Model']['Value'])
                ? (is_array($specifics['Model']['Value']) ? reset($specifics['Model']['Value']) : $specifics['Model']['Value'])
                : 'n/a'
        );
        $data['mpn'] = (isset($data['mpn']) && $data['mpn'] !== '') 
        ? $data['mpn'] 
        : (isset($specifics['MPN']['Value']) 
            ? (is_array($specifics['MPN']['Value']) 
                ? implode(', ', $specifics['MPN']['Value']) 
                : $specifics['MPN']['Value']) 
            : ''); 
        $data['sku'] = (!empty($data['sku']) && $data['sku'] != $data['product_id']) ? $data['sku'] : $data['product_id'];
        $data['ean'] = (!empty($data['ean'])) ? $data['ean'] : ($product_search_data ['ean'] ?? '');
        $data['jan'] = (!empty($data['jan'])) ? $data['jan'] : ($product_search_data ['jan'] ?? '');
        $data['asin'] = (!empty($data['asin'])) ? $data['asin'] : ($product_search_data ['asin'] ?? '');
        $data['isbn'] = (!empty($data['isbn'])) ? $data['isbn'] : ($product_search_data ['isbn'] ?? '');

    // 🔸 Groupes de catégories avec leurs dimensions par défaut
        $category_id = (string)$data['category_id'];

        $group1_categories = ['617', '139973'];
        $group2_categories = ['176984'];

        if (in_array($category_id, $group1_categories)) {
            $use_default_dimensions = true;
            $default_length = 7.5;
            $default_width = 5.25;
            $default_height = 0.5;
            $default_weight = 0.4;
        } elseif (in_array($category_id, $group2_categories)) {
            $use_default_dimensions = true;
            $default_length = 5.5;
            $default_width = 5.0;
            $default_height = 0.25;
            $default_weight = 0.2;
        } else {
            $use_default_dimensions = false;
            $default_length = 0.0;
            $default_width = 0.0;
            $default_height = 0.0;
            $default_weight = 0.0;
        }

        // 🔸 Poids
        $data['weight'] = (!empty($data['weight']) && $data['weight'] != 0)
            ? round($data['weight'], 3)
            : (
                isset($product_search_data['weight']['value']) && $product_search_data['weight']['value'] != 0
                    ? round($product_search_data['weight']['value'], 3)
                    : $default_weight
            );

        // 🔸 Longueur
        $data['length'] = (!empty($data['length']) && $data['length'] != 0)
            ? round($data['length'], 1)
            : (
                isset($product_search_data['length']['value']) && $product_search_data['length']['value'] != 0
                    ? round($product_search_data['length']['value'], 1)
                    : $default_length
            );

        // 🔸 Largeur
        $data['width'] = (!empty($data['width']) && $data['width'] != 0)
            ? round($data['width'], 1)
            : (
                isset($product_search_data['width']['value']) && $product_search_data['width']['value'] != 0
                    ? round($product_search_data['width']['value'], 1)
                    : $default_width
            );

        // 🔸 Hauteur
        $data['height'] = (!empty($data['height']) && $data['height'] != 0)
            ? round($data['height'], 1)
            : (
                isset($product_search_data['height']['value']) && $product_search_data['height']['value'] != 0
                    ? round($product_search_data['height']['value'], 1)
                    : $default_height
            );


      //print("<pre>".print_r ($data,true )."</pre>");
       //print("<pre>".print_r ($data['thumb'],true )."</pre>");
       if($data['image'] == ''){
        $data['image'] = $product_search_data ['image'] ?? $data['image'];
        $data['thumb'] =$product_search_data ['thumb'] ?? $data['thumb'];
        $data['placeholder'] =$product_search_data ['thumb'] ?? $data['placeholder'];
       }
       $data['similar_image'] = $product_search_data ['similar_image'] ?? '';
       
       $data['price_with_shipping'] = (!empty($data['price_with_shipping']) && $data['price_with_shipping'] > 3.91) 
       ? $data['price_with_shipping'] 
       : ($product_search_data ['ebay_pricevariant'][$data['condition_id']]['price'] 
       ?? 0.00000000);

      
       $shippingRates = $this->model_shopmanager_shipping->calculateShippingRates($data)??3.91;
       //print("<pre>".print_r ('233 product_seach',true )."</pre>");
       //print("<pre>".print_r ($data['price'],true )."</pre>");
      //print("<pre>".print_r ($product_search_data ['ebay_pricevariant'],true )."</pre>");
       //print("<pre>".print_r ($data['price_with_shipping'],true )."</pre>");
       //print("<pre>".print_r ($shippingRates,true )."</pre>");

       $data['shipping_cost']=$shippingRates['shipping_cost'];
       $data['shipping_carrier']=$shippingRates['shipping_carrier'];
       $data['price'] =  $data['price_with_shipping']-$data['shipping_cost']??3.79;
// 🔹 Vérification et ajout du `made_in_country_id`
            // 🔹 Prioriser "Country/Region of Manufacture" si elle est disponible
            if (isset($specifics['Country/Region of Manufacture']['Value']) && !empty($specifics['Country/Region of Manufacture']['Value'])) {
                $countryKey = 'Country/Region of Manufacture';
            } else {
                // 🔹 Rechercher une autre clé contenant "country"
                $countryKey = null;
                foreach ($specifics as $key => $value) {
                    if (stripos($key, 'country') !== false) {
                        $countryKey = $key;
                        break;
                    }
                }
            }
            
            if ($countryKey && isset($specifics[$countryKey]['Value']) && !empty($specifics[$countryKey]['Value'])) {
                $value = $specifics[$countryKey]['Value'];
            
                // Gérer les cas où `Value` est un tableau
                if (is_array($value) && count($value) === 1) {
                    $value = trim($value[0]);
                } elseif (is_array($value)) {
                    $value = null;
                }
            
                // Vérifier si la valeur est une chaîne valide
                if (is_string($value) && trim($value) !== '' && strtolower($value) !== 'unknown') {
            
                    // 🔁 Remplacer les variations de "USA" par "United States"
                    $usa_variants = ['usa', 'us', 'u.s.', 'u.s.a.', 'america', 'american', 'u.s', 'u.s.a'];
                    $value_lower = strtolower($value);
            
                    foreach ($usa_variants as $variant) {
                        if ($value_lower === $variant || strpos($value_lower, $variant) !== false) {
                            $value = 'United States';
                            break;
                        }
                    }
            
                    // Charger le modèle des pays
                    $this->load->model('shopmanager/localisation/country');
                    //print("<pre>" . print_r('225:product_search', true) . "</pre>");
                    //print("<pre>" . print_r($value, true) . "</pre>");
            
                    if (!isset($data['made_in_country_id']) || $data['made_in_country_id'] == 0) {
                        $data['made_in_country_id'] = $this->model_shopmanager_localisation_country->getCountryID($value);
                    }
                }
            }
            
            if (!isset($data['made_in_country_id'])) {
                $data['made_in_country_id'] = 0;
            }
            



        $data['status'] = (($data['quantity']>0 || $data['unallocated_quantity']>0) && $data['price']>0 && $data['product_description'][1]['name']!=='')?1:1;
    
        // 🔹 Gestion du fabricant
        $data['manufacturer_id'] = (!empty($data['manufacturer_id']) && $data['manufacturer_id'] != 0) 
                                    ? $data['manufacturer_id'] 
                                    : ($product_search_data ['manufacturer_id'] ?? 0);
    
        $data['manufacturer'] = (!empty($data['manufacturer']) && $data['manufacturer'] != 'Unbranded Generic') 
                                ? $data['manufacturer'] 
                                : ($product_search_data ['manufacturer'] ?? '');
    
        $data['brand'] = (!empty($data['brand'])&& $data['brand'] != 'Unbranded Generic') 
                        ? $data['brand'] 
                        : ($product_search_data ['brand'] ?? $data['manufacturer']);
    
        // 🔹 Gestion des langues et traductions


        $languages = $this->model_localisation_language->getLanguages();
               
          //print("<pre>".print_r ($data['product_description'],true )."</pre>"); 
          //print("<pre>".print_r ($product_search_data,true )."</pre>"); 
        foreach ($languages as $code => $language) {

            $language_id = $language['language_id'];
        
            $data['product_description'][$language_id]['name'] =
            (isset($data['product_description'][$language_id]['name']) &&
             $data['product_description'][$language_id]['name'] !== '' &&
             strlen($data['product_description'][$language_id]['name']) <= 80)
                ? $data['product_description'][$language_id]['name']
                : (
                    isset($product_search_data['name']) && strlen($product_search_data['name']) <= 80
                        ? $product_search_data['name']
                        : ''
                );
        
            $data['product_description'][$language_id]['color'] = 
                (isset($data['product_description'][$language_id]['color']) && $data['product_description'][$language_id]['color'] !== '') 
                    ? $data['product_description'][$language_id]['color'] 
                    : (isset($specifics['Color']['Value']) 
                        ? (is_array($specifics['Color']['Value']) 
                            ? implode(', ', $specifics['Color']['Value']) 
                            : $specifics['Color']['Value']) 
                        : '');
            
            $data['product_description'][$language_id]['description_supp'] = 
                (isset($data['product_description'][$language_id]['description_supp']) && $data['product_description'][$language_id]['description_supp'] !== '')
                    ? $data['product_description'][$language_id]['description_supp'] 
                    : ($product_search_data ['description_supp'] ?? '');
        
           
        
            $data['product_description'][$language_id]['condition_supp'] = 
                (isset($data['product_description'][$language_id]['condition_supp']) && $data['product_description'][$language_id]['condition_supp'] !== '')
                    ? $data['product_description'][$language_id]['condition_supp'] 
                    : ((($data['category_id'] == '617' || $data['category_id'] == '139973') && $data['condition_id'] != 1000) 
                        ? '<ul><li>Comes from a former rental store</li><li>Could have a RFID sticker in the middle of the disk</li><li>No Digital Code included</li></ul>' 
                        : '');

            //print("<pre>".print_r ($language,true )."</pre>");
            

            $data['product_description'][$language_id]['specifics'] = ($specifics);
            //print("<pre>".print_r ($specifics,true )."</pre>");
             // Si la description est toujours vide, générer avec l'IA
             if ($code=='en') {
              
               
                $title = (isset($data['product_description'][$language_id]['name']) && $data['product_description'][$language_id]['name'] !== '')
                ? $data['product_description'][$language_id]['name'] 
                : ($product_search_data ['name'] );
               
                $formdata = [];
                $category_id = $data['category_id'] ?? 0;
                if(is_string($title) && $title != ''){
                    
                    foreach($specifics as $key=>$value){
                        $formdata[$key] = $value['Value'];
                    }
                    //print("<pre>".print_r ('213 Product_search',true )."</pre>");
                    //print("<pre>".print_r ($title,true )."</pre>");
                    $desc_supp = $data['product_description'][$language_id]['description_supp'] ?? '';

                    if (empty($desc_supp) || mb_strlen(strip_tags($desc_supp)) < 20) {
                        $response = $this->model_shopmanager_ai->getDescriptionSupp($formdata, $title, $category_id);
                    
                        // Si c'est un tableau avec 'html', on prend ça, sinon on prend la chaîne brute
                        $data['product_description'][$language_id]['description_supp'] = $response['html'] ?? $response ?? '';
                    
                        $product_search_data['description_supp'] = $data['product_description'][$language_id]['description_supp'];
                    }
                    
                        
                }else{
                    //print("<pre>".print_r ('219 Product_search',true )."</pre>");
                    //print("<pre>".print_r ($title,true )."</pre>");
                    $data['product_description'][$language_id]['description'] = '';
                    $data['product_description'][$language_id]['description_supp'] ='';
                    $product_search_data ['description_supp']=$data['product_description'][$language_id]['description_supp'];
                 }
                       
                   
     
                    
                $product_search_data ['description_supp']=$data['product_description'][$language_id]['description_supp']??'';
            }
 
        }
        //print("<pre>".print_r ('206product_search',true )."</pre>");
        //print("<pre>".print_r ($data['product_description'],true )."</pre>");
        

        // enlever la languge english
        unset($languages['en']);
        foreach ($languages as $code => $language) {
            $language_id = $language['language_id'];
           // $data['product_description'][$language_id] =  $this->model_shopmanager_catalog_product->TranslateDescription( $data['product_description'][1], ['code' => $code, 'language_id' => $language_id]);
          
        }
        //print("<pre>".print_r ('234product_search',true )."</pre>");
        //print("<pre>".print_r ($data['product_description'],true )."</pre>");
       
        

        

        //print("<pre>".print_r ($data['product_description'],true )."</pre>");
        // 🔹 Mise à jour finale des descriptions dans `$data`
       

       //print("<pre>".print_r ($data['product_description'],true )."</pre>");
        //print("<pre>".print_r ($data ,true )."</pre>");
        return json_encode($data);
    }
    
    
    public function processSearchData($product_search) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        
	$execution_times = [];
	$n=0;
    $start_time = microtime(true);
               
        //$this->load->model('shopmanager/translate");
        $this->load->model('shopmanager/catalog/category');
        $this->load->model('shopmanager/condition');
        $this->load->model('localisation/language');
        $this->load->model('shopmanager/shipping');
        $this->load->model('shopmanager/tools');
        $this->load->model('shopmanager/ai');
       
      // $product_search = [];
      if(isset($product_search['primary_product_category_checkbox'])){
        $category_id = $product_search['category_id'];
        $product_search['product_category']=$product_search['primary_product_category'];
      }else{
        $category_id = $product_search['product_category'][0]??$product_search['category_id'];
      }
       
      unset($product_search['search']);
      unset($product_search['name_description']);
      unset($product_search['recognizedText']);
      unset($product_search['token']);
      unset($product_search['category']);
      unset($product_search['primary_product_category']);


      

        unset($product_search['image_dimensions']);
        unset($product_search['category_specific_names_json']);
        unset($product_search['specifics_select']);
        unset($product_search['specifics_text']);
 //print("<pre>" . print_r($product_search, true) . "</pre>");
        $product_search['specifics_source'] = $product_search['specifics_checkbox'] ?? null;
        unset($product_search['specifics_checkbox']);
      
      
        $product_search['category_id'] = $category_id;
        
        $product_search['product_category'][0]= $category_id;
        $product_search['category_name'] = $product_search['category_name'] ?? '';
        // Alimentation des informations générales
        $product_search['marketplace_item_id'] = $product_search['marketplace_item_id'] ?? 0;
        $product_search['model'] = is_array($product_search['model']) ? reset($product_search['model']) : ($product_search['model'] ?? '');
        $product_search['mpn'] = $product_search['mpn']??'';
        $product_search['sku'] = $product_search['sku'] ?? '';

    
        if (isset($product_search['price_ebay'])) {
            //a       //print("<pre>" . print_r($product_search['price'], true) . "</pre>");
                   // On suppose que le premier prix dans le tableau price est utilisé pour le champ price
            /*       foreach($product_search['price_ebay'] as $condition_marketplace_item_id_key=>$price){
                    //   $product_search['price_with_shipping'] = $price;
                     //  $shippingRates = $this->model_shopmanager_shipping->calculateShippingRates($product_search);
                       //get_shipping();
                      // $product_search['shipping_cost']=$shippingRates['shipping_cost'];
                     //  $product_search['shipping_carrier']=$shippingRates['shipping_carrier'];
                     //  $product_search['price'] = $price-$product_search['shipping_cost']??3.79;
                       $condition_marketplace_item_id = $condition_marketplace_item_id_key;
                       $product_search['condition_marketplace_item_id']=$condition_marketplace_item_id;
                   }*/
               //print("<pre>" . print_r('1014:product_search.php', true) . "</pre>");
              //print("<pre>" . print_r($product_search, true) . "</pre>");
//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 


                foreach($product_search['price_ebay'] as $condition_marketplace_item_id_key=>$price){
                   if(!isset($product_search['product_id'])){
                        if(isset($price) &&  $price>0){
     /*                       $product_search['price_with_shipping'] = $price??9999;
                            $shippingRates = $this->model_shopmanager_shipping->calculateShippingRates($product_search);
                            //get_shipping();
                            $product_search['shipping_cost']=$shippingRates['shipping_cost'];
                            $product_search['shipping_carrier']=$shippingRates['shipping_carrier'];
                            $product_search['price'] = $price-$product_search['shipping_cost']??3.79;
*/
                        }else{
                           // $price=9999;
                        }
                   }elseif($product_search['price_with_shipping'] <=0 || $product_search['price'] <=0){
                    $product_search['price_with_shipping'] = is_numeric($price)?$price:9999;
                    $shippingRates = $this->model_shopmanager_shipping->calculateShippingRates($product_search);
                    //get_shipping();
                    $product_search['shipping_cost']=$shippingRates['shipping_cost']??3.79;
                    $product_search['shipping_carrier']=$shippingRates['shipping_carrier'];
                    $product_search['price'] = $product_search['price_with_shipping']-$product_search['shipping_cost'];
                    if($product_search['price']<.99){
                    
                     
                        $product_search['price'] = .99;
                        $product_search['price_with_shipping'] = $product_search['shipping_cost']+$product_search['price'];

                    }
                }
                if($product_search['price']<.99){
                    
                     
                    $product_search['price'] = .99;
                    $product_search['price_with_shipping'] = $product_search['shipping_cost']+$product_search['price'];

                }
                    $condition_marketplace_item_id = $condition_marketplace_item_id_key;
                    $product_search['condition_marketplace_item_id']=$condition_marketplace_item_id;
                   
                }
                //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
                 
               }
             //print("<pre>" . print_r($category_id, true) . "</pre>");
              
               if(isset( $product_search['condition_marketplace_item_id'] )){
           //print("<pre>" . print_r(110, true) . "</pre>");
          //print("<pre>" . print_r($product_search, true) . "</pre>");
          //print("<pre>" . print_r(value: '116:PRODUCTSEARCH.php') . "</pre>");

                    $condition_data =  $this->model_shopmanager_condition->getConditionDetails($category_id,null,$product_search['condition_marketplace_item_id'],$product_search['site_id']??0);
               }else{
                    $condition_data =  $this->model_shopmanager_condition->getConditionDetails($category_id,$product_search['condition_id'],null,$product_search['site_id']??0);
               }
               //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
            //print("<pre>" . print_r(118, true) . "</pre>");
        //print("<pre>" . print_r($condition_data, true) . "</pre>");
        //     $prefix= $condition_data[1][$product_search['condition_id']]['prefix'];
             $product_search['condition_name']=$condition_data[1][$product_search['condition_id']]['condition_name']??'';

        if (isset($product_search['upc']) &&  !isset($product_search['product_id'])) {
         //   $product_search['sku'] = $product_search['upc'].$prefix;
            $product_search['sku'] = '';
        }
        $product_search['ean'] =  $product_search['ean'] ??'';
        $product_search['jan'] = $product_search['jan'] ??'';
        $product_search['asin'] =  $product_search['asin'] ??'';
        $product_search['isbn'] =  $product_search['isbn'] ??'';
        $product_search['location'] = $product_search['location'] ?? '';
        $product_search['anc_loc'] = $product_search['anc_loc'] ?? '';
        $product_search['quantity'] = $product_search['quantity'] ?? 0;
        $product_search['unallocated_quantity'] = $product_search['unallocated_quantity'] ?? 0;  // Set default
        $product_search['stock_status_id'] = 7;       // Set default or based on your logic
        $product_search['image'] = $product_search['image'] ?? ''; // Taking first image as main image
        $product_search['manufacturer_id'] = $product_search['manufacturer_id'] ?? 0;   // Adjust based on manufacturer mapping
        $product_search['shipping'] = 1;
        $product_search['price'] = $product_search['price'] ?? 0;               // Set default or fetch from data if available
        $product_search['price_with_shipping'] = $product_search['price_with_shipping'] ?? 0; 
        $product_search['points'] = 0;
        $product_search['tax_class_id'] = 9;
        $product_search['date_available'] = '2021-11-02'; // Default date, can be dynamic
        $product_search['weight'] = $product_search['weight']??0.00000000;
        $product_search['weight_class_id'] = 5;
        $product_search['length'] = $product_search['length']??0.00000000;
        $product_search['width'] =  $product_search['width']??0.00000000;
        $product_search['height'] = $product_search['height']??0.00000000;
        $product_search['shipping_cost'] = $product_search['shipping_cost'] ?? 0; 
        $product_search['shipping_carrier'] = $product_search['shipping_carrier'] ?? ''; 
        $product_search['length_class_id'] = 3;
        $product_search['subtract'] = 1;
        $product_search['minimum'] = 1;
        $product_search['sort_order'] = 0;
        $product_search['status'] =  $product_search['status'] ?? 1;

        if (isset($product_search['image'])) {
		/*	if(isset($product_search['image'][0])){
				$product_search['image']=$product_search['image'][0];
			}*/
		}
        $i=0;
        if (isset($product_search['product_image'])) {
			foreach ($product_search['product_image'] as $id=>$product_image) {
			//	//print("<pre>" . print_r($product_image, true) . "</pre>");
				
				// Vérifier si $product_image est un tableau, sinon le transformer en tableau
				if (!is_array($product_image)) {
					$product_search['product_image'][$id] = ['image' => $product_image, 'sort_order' => $i++];
				}
		
				// S'assurer que l'index 'image' est bien défini
		
				// Insérer dans la base de données
			}
		}
      //  $data['color'] = ( !empty($data['relationships'][0]['variationTheme']['attributes']) ? $data['relationships'][0]['variationTheme']['attributes'] : '');
      if (isset($product_search['images'])) {
        $image_temp=json_decode(html_entity_decode($product_search['images'][0]),true);
      //print("<pre>" . print_r($image_temp, true) . "</pre>");
        $product_search['image_temp'] = $this->model_shopmanager_tools->uploadImages($image_temp['url'],null,'pri');
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
        if(count($product_search['images'])>1){
            unset($product_search['images'][0]);
            foreach($product_search['images'] as $key=>$image ){
                $image=json_decode(html_entity_decode($product_search['images'][$key]),true);
                $product_search['product_image_temp'][] = $this->model_shopmanager_tools->uploadImages($image['url'],null,'sec');
            }
            
        }
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
    }
        $description_supp = $this->generateDescription($product_search,$category_id);
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
        $category_info = $this->model_shopmanager_catalog_category->getSpecific($category_id,1);
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
      //print("<pre>".print_r ($category_info,true )."</pre>");
  //      $category_specific_info = [];
//        $category_specific_info['specifics']= json_decode($category_info[1]['specifics'],true);
  //print("<pre>".print_r ($category_info[1]['specifics'],true )."</pre>");
        if(isset($product_search['specifics_source'])){
            $product_search['specifics_source']= $this->model_shopmanager_tools->cleanArray($product_search['specifics_source']);
    
        } 
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
    //print("<pre>".print_r ('jo1117',true )."</pre>");
    //print("<pre>".print_r ($product_search['epid_sources_json'],true )."</pre>");
   // $specifics=$this->getSpecifics($product_search['product_id']??null,$product_search, $category_info[1],$product_search['specifics_source']??null,json_decode(html_entity_decode($product_search['epid_sources_json']),true));
    //print("<pre>".print_r ('jo1117',true )."</pre>");
   //print("<pre>".print_r ($specifics,true )."</pre>");
        $specifics=$this->model_shopmanager_product_search->getSpecifics($product_search['product_id']??null,$product_search, $category_info[1],$product_search['specifics_source']??null,json_decode(html_entity_decode($product_search['epid_sources_json']??''),true));
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
    //print("<pre>".print_r ('jo1117',true )."</pre>");
   //print("<pre>".print_r ($specifics,true )."</pre>");
        // Traiter les autres langues avec traduction uniquement si nécessaire
        $languages = $this->model_localisation_language->getLanguages();
        $product_description = [];
        $default_language_code = 'en';  // Définir ici la langue par défaut
        $default_language_id = null;

        // Rechercher la langue par défaut
        foreach ($languages as $language_id => $language) {
            if ($language['code'] === $default_language_code) {
                $default_language_id = $language['language_id'];
                break;
            }
        }
/*      <input type="hidden" name="manufacturer_id" value="<?php echo $manufacturer_id; ?>" class="form-control"/>
    
      <input type="hidden" name="name_description" value="<?php echo $name_description; ?>" class="form-control"/>
      <input type="hidden" name="brand" value="<?php echo $brand; ?>" class="form-control"/>
      <input type="hidden" name="upc" value="<?php echo $upc; ?>" class="form-control"/>*/ 

      
        // Si une langue par défaut a été trouvée, on initialise les descriptions pour la langue par défaut
        if ($default_language_id !== null) {
          //  $additionalDescriptionHtml = ''; // Assigner la valeur du HTML à cette variable
            $conditionname = $product_search['product_description'][$default_language_id]['condition']??''; // Assigner la valeur de la condition
            $name = $product_search['title']; // Assigner le nom du produit
            $upc = $product_search['upc']; // Assigner le UPC du produit

            
            $metaTagDescription = preg_replace('/<\/?[^>]+(>|$)/', '', $description_supp);
            $metaTagDescription = str_replace('&nbsp;', ' ', $metaTagDescription);

            $metaTagDescription = preg_replace('/\s+/', ' ', $metaTagDescription);
            $metaTagTitle = '(' . $conditionname . ') ' . $name . ' UPC: ' . $upc;
          
            $metaTagKeyword = $conditionname . ' ' . $name . ' ' . $upc;
            $metaTagKeyword = preg_replace('/[,;:\'"\{\}\[\]\(\)@%$&\-]/', '', $metaTagKeyword);
            $metaTagKeyword = implode(',', preg_split('/\s+/', $metaTagKeyword));

            // Supprimer la dernière virgule si elle existe
            if (substr($metaTagKeyword, -1) === ',') {
                $metaTagKeyword = substr($metaTagKeyword, 0, -1);
            }

          
         
            $keyword = $conditionname . ' ' . $name;
            $keyword = preg_replace('/[.,;:\'"\{\}\[\]\(\)@%$&\-]/', '', $keyword);
            $keyword = implode('-', preg_split('/\s+/', $keyword));

            // Supprimer le dernier tiret s'il existe
            if (substr($keyword, -1) === '-') {
                $keyword = substr($keyword, 0, -1);
            }


            $product_description[$default_language_id] = [
                'name'                  => !empty($product_search['title']) ? $product_search['title'] : '',
                'description'           => '',  // Ajouter ici la description pour la langue par défaut si nécessaire
                'color'                 => !empty($product_search['color']) ? $product_search['color'] : '',
                'description_supp'      => !empty($description_supp) ? $description_supp : '',
                'condition_supp'        => (($category_id == '617' || $category_id == '139973') && $product_search['condition_id']!='1000') ? '&lt;ul&gt;&lt;li&gt;Comes from a former rental store&lt;/li&gt;&lt;li&gt;Could have a RFID sticker in the middle of the disk&lt;/li&gt;&lt;li&gt;No Digital Code included&lt;/li&gt;&lt;/ul&gt;' : '',
                'included_accessories'  => !empty($included_accessories) ? $included_accessories : '',
                'meta_title'            => !empty($metaTagTitle) ? $metaTagTitle : '',
                'meta_description'      => !empty($metaTagDescription) ? $metaTagDescription : '',
                'meta_keyword'          => !empty($metaTagKeyword) ? $metaTagKeyword : '',
                'keyword'               => !empty($keyword) ? $keyword : '',
                'tag'                   => !empty($metaTagKeyword) ? $metaTagKeyword : '',
                'specifics'             => isset($specifics[$default_language_id]) ? $specifics[$default_language_id] : []
            ];
            //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
        }
    //print("<pre>".print_r ('264:product_search' ,true )."</pre>");
     //print("<pre>".print_r ($product_description[$default_language_id] ,true )."</pre>");
        // Traiter les autres langues avec traduction uniquement si nécessaire
        foreach ($languages as $language) {
            $language_id =$language['language_id'];
            if ($language_id == $default_language_id) {
                continue; // Passer la langue par défaut qui a déjà été traitée
            }

            // Traduire uniquement les champs non vides
            $translated_name = !empty($product_description[$default_language_id]['name']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['name'], $language['code']) : '';
            $translated_meta_title = !empty($product_description[$default_language_id]['meta_title']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['meta_title'], $language['code']) : '';
            $translated_color = !empty($product_search['color']) ? $this->model_shopmanager_translate->translate($product_search['color'], $language['code']) : '';

            // Remplir la description pour la langue traduite en utilisant les valeurs traduites et celles de la langue par défaut
            $product_description[$language_id] = [
                'name'                  => $translated_name,
                'description'           => '',  // Ajouter ici la description traduite si nécessaire
                'color'                 => $translated_color,
                'description_supp'      => !empty($product_description[$default_language_id]['description_supp']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['description_supp'] , $language['code']): '',
                'condition_supp'        => !empty($product_description[$default_language_id]['condition_supp']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['condition_supp'], $language['code']) : '',
                'included_accessories'  => !empty($product_description[$default_language_id]['included_accessories']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['included_accessories'], $language['code']) : '',
                'meta_title'            => $translated_meta_title,
                'meta_description'      => !empty($product_description[$default_language_id]['meta_description']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['meta_description'] , $language['code']): '',
                'meta_keyword'          => !empty($product_description[$default_language_id]['meta_keyword']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['meta_keyword'] , $language['code']): '',
                'keyword'               => !empty($product_description[$default_language_id]['keyword']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['keyword'] , $language['code']): '',
                'tag'                   => !empty($product_description[$default_language_id]['tag']) ? $this->model_shopmanager_translate->translate($product_description[$default_language_id]['tag'] , $language['code']): '',
                'specifics'             => isset($specifics[$language_id]) ? $specifics[$language_id] : []                    ];

        }
   //print("<pre>".print_r ('299:product_search' ,true )."</pre>");
    //print("<pre>".print_r ($product_description[$language_id] ,true )."</pre>");
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
    //    error_log('product_description ' . __FILE__ . ' on line ' . __LINE__);


        $return_data = [];
        $return_data=$product_search;
        $return_data['product_description']=$product_description;
      
		$total_execution_time = array_sum($execution_times);
     //   echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
    /*    $product_search_specifics=[];
        $product_search_specifics= $this->model_shopmanager_tools->cleanArray($data['algopix_search']['additionalAttributes']);
        $data_specifics['category_id']=$product_search['category_id'];
        $data_specifics['product_category']=$product_search['product_category'];
        $data_specifics['category_name']=$product_search['category_name'];

        $specifics=$this->generateSpecifics($data_specifics) ;*/

        return $return_data;
    }

  
    public function cleanTitle($title) {
        $this->load->model('shopmanager/tools');

        // Liste des mots à exclure
        $excludeWords = [
            'amazoncom', 'amazonca', 'amazon com', 'ebay', 'nib', 'new', 'used', 
            'refurbished', 'shipping', 'free shipping', 'fast shipping', 'express shipping',
            'in box', 'sealed', 'brand new', 'like new', 'pre-owned', 'warranty', 'guarantee', 
            'discount', 'clearance', 'sale', 'deal', 'best price', 'free returns', 
            'no returns', 'with warranty', 'as is', 'open box', 'in original packaging', 
            'bulk', 'lot', 'bundle', 'kit', 'fast', 'quick', 'same day', 'overnight', 
            'two-day shipping', 'next day shipping', 'free', 'buy now', 'best seller',
            'hot item', 'best deal', 'limited stock',
            'for parts', 'damaged', 'tested', 'working', 'not working', 'great condition',
            'good condition', 'fair condition', 'excellent condition', 'with accessories',
            'no accessories', 'all included', 'includes', 'tested and working', 
            'free gift', 'promo', 'special offer', 'giveaway', 'stock clearance', 
            'online only', 'store exclusive', 'brand new', 'in the box' ,'dmg', 'brand'
        ];
    
        // Remplacer les caractères indésirables par des espaces
        $cleanTitle = preg_replace('/[^\p{L}\p{N}]+/u', ' ', $title);
    
        // Supprimer les mots non désirés
        foreach ($excludeWords as $word) {
            $cleanTitle = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', '', $cleanTitle);
        }
    
        // Nettoyer les espaces multiples et les espaces en début/fin
        $cleanTitle = trim(preg_replace('/\s+/', ' ', $cleanTitle));
    
        return $cleanTitle;
    }


    
    
    public function getAllImageUrls($upc_tmp_search = null, $google_search = null, $ebay_search = null, $algopix_search = null, $algopix_search_fr = null, $epid_details = null) {
        $this->load->model('shopmanager/tools');

        $all_images = [];
      // Récupérer les images de $algopix_search['images']
      if (!empty($algopix_search['images'])) {
        //print("<pre>" . print_r( '810 product_searc', true) . "</pre>");
        //print("<pre>" . print_r( $algopix_search['images'], true) . "</pre>");
        $all_images['amazon_us'] =$this->getBestImageUrl($algopix_search['images']);
        /*foreach ($algopix_search['images'] as $image) {
     //print("<pre>" . print_r( $image, true) . "</pre>");
            if(is_array($image['url'])){
                foreach ($image['url'] as $img) {
                 
                    $all_images[]  = str_replace('http:', 'https:', $img);
                    
                }
            }else{
                 $all_images[] = $image['url'];
            }
        //print("<pre>" . print_r( $image, true) . "</pre>");
        }*/
        //print("<pre>" . print_r( $all_images, true) . "</pre>");
    }

     // Récupérer les images de $algopix_search['images']
     if (!empty($algopix_search_fr['images'])) {
        //print("<pre>" . print_r( '829 product_searc', true) . "</pre>");
        //print("<pre>" . print_r( $algopix_search_fr['images'], true) . "</pre>");
        $all_images['amazon_ca'] =$this->getBestImageUrl($algopix_search_fr['images']);
        $seen_urls[] =  $all_images['amazon_ca'] ;
       /* foreach ($algopix_search_fr['images'] as $image) {
     //print("<pre>" . print_r( $image, true) . "</pre>");
            if(is_array($image['url'])){
                foreach ($image['url'] as $img) {
                 
                    $all_images[]  = str_replace('http:', 'https:', $img);
                    
                }
            }else{
                 $all_images[] = $image['url'];
            }
        //print("<pre>" . print_r( $image, true) . "</pre>");
        }*/
        //print("<pre>" . print_r( $all_images, true) . "</pre>");
    }
 //print("<pre>" . print_r( '849 product_search', true) . "</pre>");
 //print("<pre>" . print_r( $epid_details, true) . "</pre>");
 $seen_urls = [];
    // Récupérer les images de $product_info['images']
    if (!empty($epid_details['image'])) {
       
       
        //print("<pre>" . print_r($upc_tmp_search['images'], true) . "</pre>");
      /*  if(isset($epid_details['image']['imageUrl'])){
            $epid_details['image'][0]=$epid_details['image'];
            unset($epid_details['image']['imageUrl']);
        }*/
        //print("<pre>" . print_r( '849 product_searc', true) . "</pre>");
        //print("<pre>" . print_r( $epid_details['image']['imageUrl'], true) . "</pre>");
        $all_images['epid_image'] =$this->getBestImageUrl($epid_details['image']['imageUrl']);
        $seen_urls[] =  $all_images['epid_image'];
     /*    foreach ($epid_details['image'] as $image) {
             $image = str_replace('http:', 'https:', $image);
             $all_images['epid_image'][] = $image;
         }*/
     }elseif(isset($epid_details)){
        //print("<pre>" . print_r( '866 product_searc', true) . "</pre>");
        //print("<pre>" . print_r( $epid_details, true) . "</pre>");
     }
        // Récupérer les images de $product_info['images']
        if (!empty($upc_tmp_search['images'])) {
           //print("<pre>" . print_r( '871 product_searc', true) . "</pre>");
           //print("<pre>" . print_r($upc_tmp_search['images'], true) . "</pre>");
            foreach ($upc_tmp_search['images'] as $image) {
                $image = str_replace('http:', 'https:', $image);
                $all_images[] = $image;
            }
        }
    //print("<pre>" . print_r($google_search, true) . "</pre>");
        // Récupérer les images de $images (par sitename)
        if (isset($google_search) && !empty($google_search) && !isset($google_search['error'])) {
            //print("<pre>" . print_r( '873 product_searc', true) . "</pre>");
            //print("<pre>" . print_r( $google_search, true) . "</pre>");
            foreach ($google_search as $maket_name=>$imageinfo) {
              
                //foreach ($imageinfo as $image) {
                    $image=$this->getBestImageUrl($imageinfo, 'image');
                    if (isset($image) && !in_array($image, $seen_urls)) {
                        //$image = str_replace('http:', 'https:', $image['image']);
                        $all_images[$maket_name] = $image;                 
                        $seen_urls[] = $image;
                    }
                //}
            }
            //print("<pre>" . print_r( '895 product_searc', true) . "</pre>");
            //print("<pre>" . print_r( $all_images, true) . "</pre>");
        }
       //print("<pre>" . print_r($ebay_search, true) . "</pre>");
        // Récupérer les images de $ebay_search['items']
        if (!empty($ebay_search)) {
          $ebay_images =[];
            
            foreach ($ebay_search as $item) {
                //print("<pre>" . print_r($item, true) . "</pre>");
                if (!empty($item['images'])) {
                    foreach ($item['images'] as $image) {
                        $image = str_replace('http:', 'https:', $image);
                        $ebay_images[] = $image;
                        $all_images[] = $image;
                    }
                }
               
            }
            $image=$this->getBestImageUrl($ebay_images);
            if (isset($image) && !in_array($image, $seen_urls)) {
                //$image = str_replace('http:', 'https:', $image['image']);
                $all_images['eBay'] = $image;                 
                $seen_urls[] = $image;
            }
        
        }
        //print("<pre>" . print_r( '916 product_searc', true) . "</pre>");
        //print("<pre>" . print_r( $all_images, true) . "</pre>");
    
        return $this->sortImagesByPriority($all_images)??null;
    
    }

    public function getBestImageUrl($images, $urlKey = 'url') {
        $bestImage = null;
        $maxResolution = 0;
     //print("<pre>" . print_r( '936 product_searc', true) . "</pre>");
     //print("<pre>" . print_r( $images, true) . "</pre>");
        if (is_string($images)) {
            $images = [$images]; // Si c'est une chaîne, la convertir en tableau pour uniformiser le traitement
        }
        foreach ($images as $keyimg=>$img) {
            // 🔹 Si $img est directement une URL (string)
            if (is_string($img)) {
                $url = str_replace('http:', 'https:', $img);
            
                $headers = @get_headers($url, 1);
                if ($headers && strpos($headers[0], '200') !== false) {
                    $size = @getimagesize($url);
                    if ($size && isset($size[0], $size[1])) {
                        $resolution = $size[0] * $size[1];
                    } else {
                        unset($images[$keyimg]);
                        continue; // Format non reconnu
                    }
                } else {
                    // Image inaccessible (401, 403, 404, etc.)
                    $resolution = 0;
                }
            }
            
    
            // 🔹 Sinon, tableau de type ['url' => ..., 'height' => ..., 'width' => ...]
            elseif (is_array($img) && isset($img[$urlKey])) {
                $url = str_replace('http:', 'https:', $img[$urlKey]);
    
                if (isset($img['height'], $img['width']) && $img['height'] > 0 && $img['width'] > 0) {
                    $resolution = $img['height'] * $img['width'];
                } else {
                    $size = @getimagesize($url);
                    $resolution = ($size && isset($size[0], $size[1])) ? $size[0] * $size[1] : 0;
                }
            } else {
                continue; // Format non reconnu
            }
    
            if ($resolution > $maxResolution) {
                $maxResolution = $resolution;
                $bestImage = $url;
            }
        }
    
        return $bestImage ?? null;
    }
    
    
    public function getAllTitles($upc_tmp_search = null, $google_search = null, $ebay_search = null, $algopix_search = null, $algopix_search_fr = null, $epid_details = null) {
        $this->load->model('shopmanager/tools');

        //print("<pre>" . print_r( $upc_tmp_search, true) . "</pre>");
        //print("<pre>" . print_r( $google_search, true) . "</pre>");
        //print("<pre>" . print_r( $ebay_search, true) . "</pre>");
        //print("<pre>" . print_r( $algopix_search, true) . "</pre>");
        //print("<pre>" . print_r( $algopix_search_fr, true) . "</pre>");
        //print("<pre>" . print_r( $epid_details, true) . "</pre>");

        $all_titles = [];
        $seen_titles = [];
        // Récupérer les titres de $upc_tmp_search
        if (!empty($upc_tmp_search)) {
           
                if (isset($upc_tmp_search['name']) ) {
                    $title = $this->cleanTitle( $upc_tmp_search['name']); // Supprimer les points du titre
                    $title = substr($title, 0, 80);
                    $all_titles[] = $title;
                    $seen_titles[] = $title;
                  
                }
               
        }
    
        // Récupérer les titres de $google_search (par sitename)
        if (isset($google_search) && !empty($google_search) && !isset($google_search['error'])) {
            foreach ($google_search as $productinfo) {
              
                foreach ($productinfo as $product) {
                    if (isset($product['name']) && !in_array($product['name'], $seen_titles)) {
                        $title = $this->cleanTitle( $product['name']); // Supprimer les points du titre
                        $title = substr($title, 0, 80);
                        if(is_numeric($title)){
                            continue;
                        }
                        $all_titles[] = $title;
                        $seen_titles[] = $title;
                     
                    }
                   
                }
            }
        }
    
        // Récupérer les titres de $ebay_search['items']
        if (!empty($ebay_search)) {
          
            foreach ($ebay_search as $product) {
                if (isset($product['name']) && !in_array($product['name'], $seen_titles)) {
                    $title = $this->cleanTitle( $product['name']); // Supprimer les points du titre
                    $title = substr($title, 0, 80);
                    $all_titles[] = $title;
                    $seen_titles[] = $title;
                }
            }
        }
    
        // Récupérer les titres de $algopix_search
     //print("<pre>" . print_r( $algopix_search, true) . "</pre>");
        if (isset($algopix_search) && !empty($algopix_search) && !isset($algopix_search['error'])) {
        //print("<pre>" . print_r( $algopix_search, true) . "</pre>");
            if (is_array($algopix_search['commonAttributes']['title']) ) {
                foreach ($algopix_search['commonAttributes']['title'] as $title) {
                    if (isset($title) && !in_array($title, $seen_titles)) {
                        $title = $this->cleanTitle($title); // Supprimer les points du titre
                        $title = substr($title, 0, 80);
                        $all_titles[] = $title;
                        $seen_titles[] = $title;
                       //print("<pre>" . print_r( $title, true) . "</pre>");
                    }
                }
            }else{
                $title = $this->cleanTitle($algopix_search['commonAttributes']['title']); // Supprimer les points du titre
                if (isset($title) && !in_array($title, $seen_titles)) {
                    $title = substr($title, 0, 80);
                    $all_titles[] = $title;
                    $seen_titles[] = $title;
                }
            //print("<pre>" . print_r( $algopix_search['commonAttributes']['title'], true) . "</pre>");
            }
        }

        if (isset($algopix_search_fr) && !empty($algopix_search_fr) && !isset($algopix_search_fr['error'])) {
            //print("<pre>" . print_r( $algopix_search_fr, true) . "</pre>");
                if (is_array($algopix_search_fr['commonAttributes']['title']) ) {
                    foreach ($algopix_search_fr['commonAttributes']['title'] as $title) {
                        if (isset($title) && !in_array($title, $seen_titles)) {
                            $title = $this->cleanTitle($title); // Supprimer les points du titre
                            $title = substr($title, 0, 80);
                            $all_titles[] = $title;
                            $seen_titles[] = $title;
                           //print("<pre>" . print_r( $title, true) . "</pre>");
                        }
                    }
                }else{
                    $title = $this->cleanTitle($algopix_search_fr['commonAttributes']['title']); // Supprimer les points du titre
                    if (isset($title) && !in_array($title, $seen_titles)) {
                        $title = substr($title, 0, 80);
                        $all_titles[] = $title;
                        $seen_titles[] = $title;
                    }
                //print("<pre>" . print_r( $algopix_search['commonAttributes']['title'], true) . "</pre>");
                }
            }
        //print("<pre>" . print_r( 577, true) . "</pre>");
        //print("<pre>" . print_r( $all_titles, true) . "</pre>");
        return $all_titles??null;
    }

    public function sortImagesByPriority($images) {

        $priorityOrder = [
            'archambault', 'renaud_bray', 'discogs', 'sunrise_records', 'walmart_ca', 'walmart_com', 'target', 'epid_image',
            'amazon_ca', 'amazon_us', 'amazon_com', 'amazon_eg', 'sears_ca', 'indigo_ca', 'indigo_com',
            'ebay_com', 'ebayimg', 'ebay_ca', 'bigcommerce', 'chicagocostume', 'eBay'
        ];
      //  $priorityOrder = ['archambault', 'amazon_ca', 'amazon_com', 'epid_image', 'ebay_ca', 'ebay_com', 'eBay'];
    
        $sortedImages = [];
    
        // Ajoute d'abord les clés prioritaires
        foreach ($priorityOrder as $key) {
            if (isset($images[$key])) {
                $sortedImages[$key] = $images[$key];
                unset($images[$key]);
            }
        }
    
        // Trie les clés numériques restantes en ordre croissant
        ksort($images, SORT_NUMERIC);
    
        // Fusionne les deux tableaux
        return array_merge($sortedImages, $images);
    }
    
    public function getManufacturer($manufacturers = []) {
        $this->load->model('shopmanager/manufacturer');
        $this->load->model('shopmanager/ai');
    
        $cleanManufacturers = [];
        $found_manufacturer = [];
        $manufacturer_result = [];
    
        // Étape 1 : Recherche des fabricants existants en base de données
        foreach ($manufacturers as $manufacturer) {
            $manufacturer_words = explode(' ', $manufacturer);
    
            foreach ($manufacturer_words as $word) {
                if (strlen($word) <= 2) continue; // Ignore les mots courts
    
                $existingManufacturers = $this->model_shopmanager_manufacturer->getManufacturers(['filter_name' => $word]);
    
                foreach ($existingManufacturers as $Manufacturer) {
                    $cleanManufacturers[$Manufacturer['manufacturer_id']] = $Manufacturer['name'];
                }
            }
        }
    
        // Étape 2 : Vérification si un fabricant a été trouvé en base
        if (!empty($cleanManufacturers)) {
            if (count($cleanManufacturers) === 1) {
                $manufacturer_id = array_key_first($cleanManufacturers);
                return [
                    'manufacturer_id' => $manufacturer_id,
                    'name' => $cleanManufacturers[$manufacturer_id]
                ];
            }
            $response_data = $this->model_shopmanager_ai->getManufacturer($cleanManufacturers, $manufacturer);
            if (isset($response_data['manufacturer_id']) && is_numeric($response_data['manufacturer_id']) && $response_data['manufacturer_id'] != 0) {
                return [
                    'manufacturer_id' => $response_data['manufacturer_id'],
                    'name' => $manufacturer
                ];
            }
           
        }
    
        // Étape 3 : Vérification si le fabricant existe déjà en base
        $findmanufacturer = $this->model_shopmanager_manufacturer->getManufacturerByName($manufacturer);
    
        if (!empty($findmanufacturer)) {
            return [
                'manufacturer_id' => $findmanufacturer['manufacturer_id'],
                'name' => $manufacturer
            ];
        }
    
        // Étape 4 : Création d'un nouveau fabricant si aucun n'existe en base
        $data_value = [
            'name' => $manufacturer,
            'sort_order' => 1,
            'image' => '',
            'manufacturer_store' => [0],
            'keyword' => strtolower($manufacturer)
        ];
    
        $manufacturer_id = $this->model_shopmanager_manufacturer->addManufacturer($data_value);
    
        return [
            'manufacturer_id' => $manufacturer_id,
            'name' => $manufacturer
        ];
    }

    public function getAllManufacturers($data) {
        $this->load->model('shopmanager/tools');
    
        $sources = ['upc_tmp_search', 'algopix_search', 'algopix_search_fr', 'google_search', 'ebay_search', 'epid_details'];
        $all_manufacturers = [];
        $seen_manufacturers = [];
    
        // Liste extensible de mots-clés pouvant désigner un manufacturier
        $possible_keys = ['brand', 'studio', 'manufacturer', 'editor', 'publisher', 'label', 'make', 'produced by', 'produced_by'];
    
        foreach ($sources as $source) {
            if (!isset($data[$source]) || empty($data[$source]) || isset($data[$source]['error'])) {
                continue;
            }
    
            $source_data = $data[$source];
    
            // Parcourir toutes les clés du tableau source
            foreach ($source_data as $key => $value) {
                $key_normalized = strtolower(trim($key));
    
                foreach ($possible_keys as $possible) {
                    if (strpos($key_normalized, $possible) !== false && !empty($value)) {
                        if (is_array($value)) {
                            foreach ($value as $item) {
                                $item = trim($item);
                                if (!in_array($item, $seen_manufacturers)) {
                                    $all_manufacturers[] = $item;
                                    $seen_manufacturers[] = $item;
                                }
                            }
                        } else {
                            $manufacturer = trim($value);
                            if (!in_array($manufacturer, $seen_manufacturers)) {
                                $all_manufacturers[] = $manufacturer;
                                $seen_manufacturers[] = $manufacturer;
                            }
                        }
                    }
                }
            }
    
            // Recherche spécifique dans `commonAttributes`
            if (isset($source_data['commonAttributes']) && is_array($source_data['commonAttributes'])) {
                foreach ($source_data['commonAttributes'] as $key => $value) {
                    $key_normalized = strtolower(trim($key));
                    foreach ($possible_keys as $possible) {
                        if (strpos($key_normalized, $possible) !== false && !empty($value)) {
                            if (is_array($value)) {
                                foreach ($value as $item) {
                                    $item = trim($item);
                                    if (!in_array($item, $seen_manufacturers)) {
                                        $all_manufacturers[] = $item;
                                        $seen_manufacturers[] = $item;
                                    }
                                }
                            } else {
                                $manufacturer = trim($value);
                                if (!in_array($manufacturer, $seen_manufacturers)) {
                                    $all_manufacturers[] = $manufacturer;
                                    $seen_manufacturers[] = $manufacturer;
                                }
                            }
                        }
                    }
                }
            }
        }
    
        // Nettoyage
        $all_manufacturers = array_values(array_unique(array_filter($all_manufacturers)));
    
        return !empty($all_manufacturers) ? $all_manufacturers : ($data['manufacturer_id'] ?? null);
    }
    
    public function getAllManufacturersOLD($data) {

        $this->load->model('shopmanager/tools');
    
        $sources = ['upc_tmp_search', 'algopix_search', 'algopix_search_fr'];
        $all_manufacturers = [];
        $seen_manufacturers = [];
    
        foreach ($sources as $source) {
            if (!isset($data[$source]) || empty($data[$source]) || isset($data[$source]['error'])) {
                continue;
            }
    
            $source_data = $data[$source];
    
            // Extraction directe de la marque si elle existe
            if (isset($source_data['brand']) && !empty($source_data['brand'])) {
                $manufacturer = trim($source_data['brand']);
                if (!in_array($manufacturer, $seen_manufacturers)) {
                    $all_manufacturers[] = $manufacturer;
                    $seen_manufacturers[] = $manufacturer;
                }
            }
    
            // Extraction de la marque via `commonAttributes`
            if (isset($source_data['commonAttributes']['manufacturer'])) {
                $manufacturers = (array) $source_data['commonAttributes']['manufacturer'];
                foreach ($manufacturers as $manufacturer) {
                    $manufacturer = trim($manufacturer);
                    if (!in_array($manufacturer, $seen_manufacturers)) {
                        $all_manufacturers[] = $manufacturer;
                        $seen_manufacturers[] = $manufacturer;
                    }
                }
            }
        }
    
        // Nettoyage des valeurs vides
        $all_manufacturers = array_values(array_unique(array_filter($all_manufacturers)));
    
        return !empty($all_manufacturers) ? $all_manufacturers : ($data['manufacturer_id'] ?? null);
    }
     
        public function fetchRemoteContent($url) {
            $this->load->model('shopmanager/tools');

            // Initialiser cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Suivre les redirections
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10); // Limiter le nombre de redirections
        
            // Exécuter la requête cURL
            $response = curl_exec($ch);
        
            // Vérifier les erreurs cURL
            if ($response === false) {
                $error_msg = 'cURL Error: ' . curl_error($ch);
                
                return $error_msg;
            }
        
            // Vérification du code de statut HTTP
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($http_code !== 200) {
                
                return 'HTTP Error: ' . $http_code;
            }
        
            // Fermer cURL
            
        
            // Retourner la réponse obtenue
            return $response;
        }

      

 
        
            private function generateDescription($data, $category_id = 0) {
                $this->load->model('shopmanager/tools');
                $this->load->model('shopmanager/ai');
                $formdata = [];
                $title = $data['title'] ?? null;
                unset($data['manageInfoSources']);
                unset($data['title_search']);
                unset($data['category_id']);
                unset($data['manufacturer_id']);
                unset($data['site_id']);
                unset($data['product_category']);
                unset($data['epid_sources_json']);
                unset($data['condition_id']);
                unset($data['price_ebay']);

                unset($data['made_in_country_id']);
                unset($data['price']);
                unset($data['shipping_carrier']);
                unset($data['shipping_cost']);

                unset($data['price_with_shipping']);
                unset($data['discount']);
                unset($data['length']);
                unset($data['width']);

                unset($data['height']);
                unset($data['length_class_id']);
                unset($data['weight']);
                unset($data['weight_class_id']);

                unset($data['marketplace_item_id']);
                unset($data['sku']);
                unset($data['condition_marketplace_item_id']);
                unset($data['location']);

                unset($data['anc_loc']);
                unset($data['quantity']);
                unset($data['unallocated_quantity']);
                unset($data['stock_status_id']);

                unset($data['image']);
                unset($data['shipping']);
                unset($data['points']);
                unset($data['tax_class_id']);

                unset($data['date_available']);
                unset($data['subtract']);
                unset($data['minimum']);
                unset($data['sort_order']);
                unset($data['status']);
                unset($data['weight_oz']);

                foreach($data as $key => $value) {
                    if (is_array($value)) {
                        foreach($value as $key2 => $value2) {
                            if (is_array($value2)) {
                                $formdata[] = str_replace('_', '',strtoupper($key2)) . ': '. implode(', ', $value2);
                            }else{
                                $formdata[] =  str_replace('_', '',strtoupper($key2)) . ': ' . $value2;
                            }
                           
                        }
                    }elseif($value != ''){
                        $formdata[] =  str_replace('_', '',strtoupper($key)) . ': ' . $value;
                    }

                }
                //print("<pre>" . print_r($category_id, true) . "</pre>");

                //print("<pre>" . print_r($data, true) . "</pre>");

                // Fonction pour vérifier si une valeur contient un underscore et la traiter en conséquence
               
                // Titre ou nom du produit
                if (isset($data['title'])) {
                    $formdata[] = "Product Name: " .($title);
                }
                if(isset($data['upc_tmp_search']['description_supp'])){
                    $description_supp =($data['upc_tmp_search']['description_supp']);
                    if (!empty($description_supp)) {
                        $formdata[] = "Additional Description: " . $description_supp;
                    }
                }
                 //Color

                 if (isset($data['color'])) {
                    $formdata[] = "Color: " . $data['color'];
                }


                // Marque
                if (isset($data['algopix_search']['commonAttributes']['brand'])) {
                    $brand =($data['algopix_search']['commonAttributes']['brand']);
                    if (!empty($brand)) {
                        $formdata[] = "Brand: " . $brand;
                    }
                }
            
                // Modèle
                if (isset($data['algopix_search']['commonAttributes']['modelNumber'])) {
                    $modelNumber =($data['algopix_search']['commonAttributes']['modelNumber']);
                    if (!empty($modelNumber)) {
                        $formdata[] = "Model: " . $modelNumber;
                    }
                }
            
                // Numéro de pièce (part number)
                if (isset($data['algopix_search']['commonAttributes']['partNumber'])) {
                    $partNumber =($data['algopix_search']['commonAttributes']['partNumber']);
                    if (!empty($partNumber)) {
                        $formdata[] = "Part Number: " . $partNumber;
                    }
                }
            
                // Dimensions de l'article
                if (isset($data['algopix_search']['dimensions']['itemDimensions'])) {
                    $dimensions = $data['algopix_search']['dimensions']['itemDimensions'];
                    $dimensionText = "Dimensions (LxWxH): ";
            
                    // Ajouter la longueur si elle existe
                    if (isset($dimensions['length']['value'])) {
                        $dimensionText .=(number_format($dimensions['length']['value'], 2)) . " " . ($dimensions['length']['unit'] ?? 'inches') . " x ";
                    }
            
                    // Ajouter la largeur si elle existe
                    if (isset($dimensions['width']['value'])) {
                        $dimensionText .=(number_format($dimensions['width']['value'], 2)) . " " . ($dimensions['width']['unit'] ?? 'inches') . " x ";
                    }
            
                    // Ajouter la hauteur si elle existe
                    if (isset($dimensions['height']['value'])) {
                        $dimensionText .=(number_format($dimensions['height']['value'], 2)) . " " . ($dimensions['height']['unit'] ?? 'inches');
                    }
            
                    // Ajouter le texte des dimensions au formdata s'il est complet
                    if ($dimensionText !== "Dimensions (LxWxH): ") {
                        $formdata[] = $dimensionText;
                    }
                }
            
                // Vérifier si le poids de l'article existe
                if (isset($data['algopix_search']['dimensions']['itemDimensions']['weight'])) {
                    $weightValue =($data['algopix_search']['dimensions']['itemDimensions']['weight']['value']);
                    if (!empty($weightValue)) {
                        $formdata[] = "Weight: " . number_format($weightValue, 2) . " " . ($data['algopix_search']['dimensions']['itemDimensions']['weight']['unit'] ?? 'pounds');
                    }
                }
               
                // Bullet points
                if (isset($data['algopix_search']['additionalAttributes']['bulletPoint']) && is_array($data['algopix_search']['additionalAttributes']['bulletPoint'])) {
                    $features = "Features: ";
                    foreach ($data['algopix_search']['additionalAttributes']['bulletPoint'] as $point) {
                        $point =($point);
                        if (!empty($point)) {
                            $features .= "- " . $point;
                        }
                    }
                    $formdata[] =  $features;
                }
            
                // Style ou thème du produit
                if (isset($data['algopix_search']['channelSpecificAttributes']['style'])) {
                    // Vérifier si c'est un tableau
                    if (is_array($data['algopix_search']['channelSpecificAttributes']['style'])) {
                        $styles = $this->model_shopmanager_tools->cleanArray(array_map([$this, 'convert_one_array_to_string'], $data['algopix_search']['channelSpecificAttributes']['style']));
                        $styles = array_filter($styles); // Retirer les styles vides
                        if (!empty($styles)) {
                            $formdata[] = "Style: " . implode(", ", $styles);
                        }
                    } else {
                        // Si ce n'est pas un tableau, l'ajouter directement
                        $formdata[] = "Style: " .($data['algopix_search']['channelSpecificAttributes']['style']);
                    }
                }

                // Utilisation ou type d'occasion
                if (isset($data['algopix_search']['additionalAttributes']['occasionType'])) {
                    if (is_array($data['algopix_search']['additionalAttributes']['occasionType'])) {
                        $occasions = $this->model_shopmanager_tools->cleanArray(array_map([$this, 'convert_one_array_to_string'], $data['algopix_search']['additionalAttributes']['occasionType']));
                        $occasions = array_filter($occasions); // Retirer les occasions vides
                        if (!empty($occasions)) {
                            $formdata[] = "Occasion: " . implode(", ", $occasions);
                        }
                    } else {
                        // Si ce n'est pas un tableau, l'ajouter directement
                        $formdata[] = "Occasion: " .($data['algopix_search']['additionalAttributes']['occasionType']);
                    }
                }

                // Matériaux utilisés
                if (isset($data['algopix_search']['additionalAttributes']['material'])) {
                    if (is_array($data['algopix_search']['additionalAttributes']['material'])) {
                        $materials = $this->model_shopmanager_tools->cleanArray(array_map([$this, 'convert_one_array_to_string'], $data['algopix_search']['additionalAttributes']['material']));
                        $materials = array_filter($materials); // Retirer les matériaux vides
                        if (!empty($materials)) {
                            $formdata[] = "Material: " . implode(", ", $materials);
                        }
                    } else {
                        // Si ce n'est pas un tableau, l'ajouter directement
                        $formdata[] = "Material: " .($data['algopix_search']['additionalAttributes']['material']);
                    }
                }

            
                // Nombre de pièces
                if (isset($data['algopix_search']['additionalAttributes']['numberOfItems'])) {
                    $numberOfItems =($data['algopix_search']['additionalAttributes']['numberOfItems']);
                    if (!empty($numberOfItems)) {
                        $formdata[] = "Number of Items: " . $numberOfItems;
                    }
                }
              
                //FRANCAIS

                // Marque
                if (isset($data['algopix_search_fr']['commonAttributes']['brand'])) {
                    $brand =($data['algopix_search_fr']['commonAttributes']['brand']);
                    if (!empty($brand)) {
                        $formdata[] = "Brand: " . $brand;
                    }
                }
            
                // Modèle
                if (isset($data['algopix_search_fr']['commonAttributes']['modelNumber'])) {
                    $modelNumber =($data['algopix_search_fr']['commonAttributes']['modelNumber']);
                    if (!empty($modelNumber)) {
                        $formdata[] = "Model: " . $modelNumber;
                    }
                }
            
                // Numéro de pièce (part number)
                if (isset($data['algopix_search_fr']['commonAttributes']['partNumber'])) {
                    $partNumber =($data['algopix_search_fr']['commonAttributes']['partNumber']);
                    if (!empty($partNumber)) {
                        $formdata[] = "Part Number: " . $partNumber;
                    }
                }
            
                // Dimensions de l'article
                if (isset($data['algopix_search_fr']['dimensions']['itemDimensions'])) {
                    $dimensions = $data['algopix_search_fr']['dimensions']['itemDimensions'];
                    $dimensionText = "Dimensions (LxWxH): ";
            
                    // Ajouter la longueur si elle existe
                    if (isset($dimensions['length']['value'])) {
                        $dimensionText .=(number_format($dimensions['length']['value'], 2)) . " " . ($dimensions['length']['unit'] ?? 'inches') . " x ";
                    }
            
                    // Ajouter la largeur si elle existe
                    if (isset($dimensions['width']['value'])) {
                        $dimensionText .=(number_format($dimensions['width']['value'], 2)) . " " . ($dimensions['width']['unit'] ?? 'inches') . " x ";
                    }
            
                    // Ajouter la hauteur si elle existe
                    if (isset($dimensions['height']['value'])) {
                        $dimensionText .=(number_format($dimensions['height']['value'], 2)) . " " . ($dimensions['height']['unit'] ?? 'inches');
                    }
            
                    // Ajouter le texte des dimensions au formdata s'il est complet
                    if ($dimensionText !== "Dimensions (LxWxH): ") {
                        $formdata[] = $dimensionText;
                    }
                }
            
                // Vérifier si le poids de l'article existe
                if (isset($data['algopix_search_fr']['dimensions']['itemDimensions']['weight'])) {
                    $weightValue =($data['algopix_search_fr']['dimensions']['itemDimensions']['weight']['value']);
                    if (!empty($weightValue)) {
                        $formdata[] = "Weight: " . number_format($weightValue, 2) . " " . ($data['algopix_search_fr']['dimensions']['itemDimensions']['weight']['unit'] ?? 'pounds');
                    }
                }
               
                // Bullet points
                if (isset($data['algopix_search_fr']['additionalAttributes']['bulletPoint']) && is_array($data['algopix_search_fr']['additionalAttributes']['bulletPoint'])) {
                    $features = "Features: ";
                    foreach ($data['algopix_search_fr']['additionalAttributes']['bulletPoint'] as $point) {
                        $point =($point);
                        if (!empty($point)) {
                            $features .= "- " . $point;
                        }
                    }
                    $formdata[] =  $features;
                }
            
                // Style ou thème du produit
                if (isset($data['algopix_search_fr']['channelSpecificAttributes']['style'])) {
                    // Vérifier si c'est un tableau
                    if (is_array($data['algopix_search_fr']['channelSpecificAttributes']['style'])) {
                        $styles = $this->model_shopmanager_tools->cleanArray(array_map([$this, 'convert_one_array_to_string'], $data['algopix_search_fr']['channelSpecificAttributes']['style']));
                        $styles = array_filter($styles); // Retirer les styles vides
                        if (!empty($styles)) {
                            $formdata[] = "Style: " . implode(", ", $styles);
                        }
                    } else {
                        // Si ce n'est pas un tableau, l'ajouter directement
                        $formdata[] = "Style: " .($data['algopix_search_fr']['channelSpecificAttributes']['style']);
                    }
                }

                // Utilisation ou type d'occasion
                if (isset($data['algopix_search_fr']['additionalAttributes']['occasionType'])) {
                    if (is_array($data['algopix_search_fr']['additionalAttributes']['occasionType'])) {
                        $occasions = $this->model_shopmanager_tools->cleanArray(array_map([$this, 'convert_one_array_to_string'], $data['algopix_search_fr']['additionalAttributes']['occasionType']));
                        $occasions = array_filter($occasions); // Retirer les occasions vides
                        if (!empty($occasions)) {
                            $formdata[] = "Occasion: " . implode(", ", $occasions);
                        }
                    } else {
                        // Si ce n'est pas un tableau, l'ajouter directement
                        $formdata[] = "Occasion: " .($data['algopix_search_fr']['additionalAttributes']['occasionType']);
                    }
                }

                // Matériaux utilisés
                if (isset($data['algopix_search_fr']['additionalAttributes']['material'])) {
                    if (is_array($data['algopix_search_fr']['additionalAttributes']['material'])) {
                        $materials = $this->model_shopmanager_tools->cleanArray(array_map([$this, 'convert_one_array_to_string'], $data['algopix_search_fr']['additionalAttributes']['material']));
                        $materials = array_filter($materials); // Retirer les matériaux vides
                        if (!empty($materials)) {
                            $formdata[] = "Material: " . implode(", ", $materials);
                        }
                    } else {
                        // Si ce n'est pas un tableau, l'ajouter directement
                        $formdata[] = "Material: " .($data['algopix_search_fr']['additionalAttributes']['material']);
                    }
                }

            
                // Nombre de pièces
                if (isset($data['algopix_search_fr']['additionalAttributes']['numberOfItems'])) {
                    $numberOfItems =($data['algopix_search_fr']['additionalAttributes']['numberOfItems']);
                    if (!empty($numberOfItems)) {
                        $formdata[] = "Number of Items: " . $numberOfItems;
                    }
                }
           //print("<pre>" . print_r($formdata, true) . "</pre>");

                $airesult=$this->model_shopmanager_ai->getDescriptionSupp($formdata, $title, $category_id);
                //print("<pre>" . print_r($airesult, true) . "</pre>");
                $description = htmlspecialchars($airesult['html'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); 

            //print("<pre>" . print_r($description, true) . "</pre>");

                return  $description;
            }
            
            private function getIdentifierValue($identifiers, $type) {

          //print("<pre>" . print_r($identifiers, true) . "</pre>");
          $this->load->model('shopmanager/tools');
                foreach ($identifiers as $identifier) {
                    if (isset($identifier['type']) && $identifier['type'] === $type) {
                        $value = ($identifier['values']) ?? '';
                        
                        return (strpos($value, '_') !== false) ? '' : $value; // Vérification de l'underscore
                    }
                }
                return ''; // Retourne une chaîne vide si l'identifiant n'est pas trouvé
            }
            
  
            
          
            public function filterArrayForSpecifics($inputArray) {
                $this->load->model('shopmanager/tools');
                $keysToUnset = [
                    'name',  'description_supp', 'categories', 'currency',
                    'lowest_recorded_price', 'highest_recorded_price', 'images', 'offers',
                    'aid', 'language', 'primaryChannelIdType', 'primaryChannelId', 'identifiers',
                    'commonAttributes' => ['title', 'manufacturer'],
                    'dimensions' => ['packageDimensions'],
                    'additionalAttributes' => [
                        'listPrice', 'manufacturerContactInformation', 'supplierDeclaredDgHzRegulation',
                        'itemTypeKeyword', 'unspscCode'
                    ] //'brand',
                ];
                
                // Suppression des clés principales
                foreach ($keysToUnset as $key => $subkeys) {
                    if (is_array($subkeys)) {
                        // Suppression des sous-clés
                        foreach ($subkeys as $subkey) {
                            unset($inputArray[$key][$subkey]);
                        }
                    } else {
                        unset($inputArray[$subkeys]);
                    }
                }

                // Assignation de 'manufacturer' à 'brand' et suppression de l'ancienne clé

               /* $inputArray['Brand'] = $inputArray['manufacturer'] ?? '';
                unset($inputArray['manufacturer']);*/

                // Suppression des clés restantes
                $inputArray = $this->model_shopmanager_tools->removeEmptyKeys($inputArray);

                // Sauvegarde et suppression des autres attributs
                $commonAttributes = $inputArray['commonAttributes'] ?? [];
                unset($inputArray['commonAttributes']);

                $dimensions = $inputArray['dimensions'] ?? [];
                unset($inputArray['dimensions']);

                $channelSpecificAttributes = $inputArray['channelSpecificAttributes'] ?? [];
                unset($inputArray['channelSpecificAttributes']);

                $additionalAttributes = $inputArray['additionalAttributes'] ?? [];
                unset($inputArray['additionalAttributes']);
           //print("<pre>" . print_r($inputArray, true) . "</pre>");
                $dimensionsCleaned=$this->filterChannelSpecifics($dimensions);
            //print("<pre>" . print_r($commonAttributes, true) . "</pre>");
                $commonAttributesCleaned=$this->filterChannelSpecifics($commonAttributes);
           //print("<pre>" . print_r($commonAttributesCleaned, true) . "</pre>");
                $channelSpecificAttributesCleaned=$this->filterChannelSpecifics($channelSpecificAttributes);
                $additionalAttributesCleaned=$this->filterChannelSpecifics($additionalAttributes);
            
                $mergedArray = array_merge_recursive(
                    $inputArray, 
                    $dimensionsCleaned, 
                    $commonAttributesCleaned, 
                    $channelSpecificAttributesCleaned, 
                    $additionalAttributesCleaned
                );
            
            //print("<pre>" . print_r($mergedArray, true) . "</pre>");
                
                
                    return $mergedArray;
        }
            
      
        private function filterChannelSpecifics($inputArray) {
            $this->load->model('shopmanager/tools');
            $result = [];
         //print("<pre>" . print_r(867, true) . "</pre>");
        //print("<pre>" . print_r($inputArray['itemDimensions']['weight']['unit'], true) . "</pre>");

            $exclusionCriteria = ['amazon', 'unknown', 'not_applicable'];
            // Traitement des dimensions
            if (isset($inputArray['itemDimensions'])) {
                // Conversion du poids
                if (isset($inputArray['itemDimensions']['weight'])) {
                    $weightValue = isset($inputArray['itemDimensions']['weight']['value'][0])?number_format($inputArray['itemDimensions']['weight']['value'][0],2):number_format($inputArray['itemDimensions']['weight']['value'],2);
                    if (is_array($inputArray['itemDimensions']['weight']['unit'])) {
                        $weightUnit = strtolower(($inputArray['itemDimensions']['weight']['unit'][0]));
                    } else {
                        $weightUnit = strtolower($inputArray['itemDimensions']['weight']['unit']);
                    }
                    
            
                    // Vérifier et convertir les unités de poids
                    if ($weightUnit === 'pound' || $weightUnit === 'lb') {
                        $inputArray['weight'] = $weightValue . ' lb';
                    } elseif ($weightUnit === 'oz') {
                        // Convertir les onces en livres (1 lb = 16 oz)
                        $inputArray['weight'] = ($weightValue / 16) . ' lb';
                    } else {
                        $inputArray['weight'] = null;  // Unité non supportée
                    }
                }
        
                // Conversion des dimensions (largeur, longueur, hauteur)
                foreach (['width', 'length', 'height'] as $dimension) {
                    if (isset($inputArray['itemDimensions'][$dimension])) {
                        $dimensionValue = isset($inputArray['itemDimensions'][$dimension]['value'][0])?number_format($inputArray['itemDimensions'][$dimension]['value'][0],2):number_format($inputArray['itemDimensions'][$dimension]['value'],2);
                       
                        if (is_array($inputArray['itemDimensions'][$dimension]['unit'])) {
                            $dimensionUnit = strtolower(($inputArray['itemDimensions'][$dimension]['unit'][0]));
                        } else {
                            $dimensionUnit = strtolower($inputArray['itemDimensions'][$dimension]['unit']);
                        }
                       //  $dimensionUnit = strtolower($inputArray['itemDimensions'][$dimension]['unit']);
            
                        // Vérifier et convertir les unités de dimensions
                        if ($dimensionUnit === 'inch' || $dimensionUnit === 'in') {
                            $inputArray[$dimension] = $dimensionValue . ' in';
                        } elseif ($dimensionUnit === 'feet' || $dimensionUnit === 'ft') {
                            // Convertir les pieds en pouces (1 ft = 12 in)
                            $inputArray[$dimension] = ($dimensionValue * 12) . ' in';
                        } else {
                            $inputArray[$dimension] = null;  // Unité non supportée
                        }
                    }
                }
                unset($inputArray['itemDimensions']);
            }
        
            // Parcourir les autres données de $inputArray
            foreach ($inputArray as $key => $value) {
                // Ignorer les valeurs liées à Amazon ou contenant "unknown"
             //   if (!$this->containsExclusionCriteria($value, $exclusionCriteria)) {          
             //       continue;
            //    }
        
                // Vérifier si la valeur est vide ou null, dans ce cas, on l'ignore
                if (empty($value)) {
                    $result[$key] = '';
                    continue;
                }
        
                // Si la valeur est un tableau, nous devons extraire les valeurs intéressantes
                if (is_array($value)) {
                    $processedArray = [];
                    
                    if ($key === 'runtime') {
                        // Vérifier que les clés 'value' et 'unit' existent
                        if (isset($value['value']) && isset($value['unit'])) {
                            // Combiner la valeur et l'unité en une chaîne de caractères
                     //print("<pre>".print_r (1518,true )."</pre>");
                  //print("<pre>".print_r ($value,true )."</pre>");
                            $result['Run Time'] = is_array($value['value'])?$value['value'][0]:$value['value'] . ' ' . $value['unit']; 
                            $result['Run Time'] =($result['Run Time']);
                        }elseif (isset($value['value']) && !isset($value['unit'])) {
                            // Combiner la valeur et l'unité en une chaîne de caractères
                            $result['Run Time'] = (is_array($value['value']) ? reset($value['value']) : $value['value']) . ' minutes';

                            $result['Run Time'] =($result['Run Time']);
                        }
                    //print("<pre>".print_r (1511,true )."</pre>");
                    //print("<pre>".print_r ($result,true )."</pre>");
                        continue;
                    } elseif ($key === 'pages'){
                        // Vérifier que les clés 'value' et 'unit' existent
                        if (isset($value['value']) && is_array($value['value'])) {
                            // Combiner la valeur et l'unité en une chaîne de caractères
                            $result['Pages'] = $value['value'][0] . ' Pages' ;
                            $result['Pages'] =($result['Pages']);
                        }elseif (isset($value['value'])) {
                            // Combiner la valeur et l'unité en une chaîne de caractères
                            $result['Pages'] = $value['value'] . ' Pages' ;
                            $result['Pages'] =($result['Pages']);
                        }
                 //print("<pre>".print_r (1525,true )."</pre>");
                    //print("<pre>".print_r ($result,true )."</pre>");
                        continue;
                    
                    } elseif (stripos($key, 'gpu') !== false) {
                        // Vérifier que les clés 'value' et 'unit' existent
                        if (isset($value['value']) && isset($value['unit'])) {
                            // Combiner la valeur et l'unité en une chaîne de caractères
                            $result['gpuClockSpeed'] = $value['value'] . ' ' . $value['unit'];
                            $result['gpuClockSpeed'] =($result['gpuClockSpeed']);
                        } elseif (isset($value['value']) && !isset($value['unit'])) {
                            // Combiner la valeur et l'unité en une chaîne de caractères
                            $result['gpuClockSpeed'] = $value['value'] . ' minutes';
                            $result['gpuClockSpeed'] =($result['gpuClockSpeed']);
                        }
                 //print("<pre>".print_r (1525,true )."</pre>");
                    //print("<pre>".print_r ($result,true )."</pre>");
                        continue;
                    } elseif ($key === 'language') {
                        // Appeler la fonction pour traiter les contributeurs
                        $languageResult = $this->processContributors($value);
                        // Fusionner le résultat des contributeurs avec le tableau principal
                        $result =(array_merge_recursive($result, $languageResult));
                  //print("<pre>".print_r (1533,true )."</pre>");
                   //print("<pre>".print_r ($result,true )."</pre>");
                        continue;
                    } elseif ($key === 'contributors') {
                        // Appeler la fonction pour traiter les contributeurs
                        $contributorsResult = $this->processContributors($value);
                        // Fusionner le résultat des contributeurs avec le tableau principal
                        $result =(array_merge_recursive($result, $contributorsResult));
                   //print("<pre>".print_r (1541,true )."</pre>");
                  //print("<pre>".print_r ($result,true )."</pre>");
                        continue;
                    }elseif (is_array($value)) {
                        // Extraire uniquement les valeurs d'intérêt dans les tableaux normaux
                        //print("<pre>".print_r ($value,true )."</pre>");
                        foreach ($value as $subKey => $subValue) {
                            if (is_string($subValue)) {
                                // Ignorer les valeurs "unknown"
                                if (strtolower($subValue) !== 'unknown') {
                                    $processedArray[] = ($this->model_shopmanager_tools->splitNames($subValue));
                                }
                           //print("<pre>".print_r (1553,true )."</pre>");
                            //print("<pre>".print_r ($processedArray,true )."</pre>");
                            } elseif (is_array($subValue) && isset($subValue['value'])) {
                              
                                // Vérifier que 'value' est une chaîne de caractères avant d'utiliser strtolower
                                if (is_string($subValue['value']) && strtolower($subValue['value']) !== 'unknown') {
                                    $processedArray[] = (($this->model_shopmanager_tools->splitNames($subValue['value'])));
                                } elseif (is_array($subValue['value'])){
                                   
                                  //print("<pre>".print_r (1561,true )."</pre>");
                        
                                    
                                    foreach ($subValue['value'] as $subsubkey => $subsubvalue) {
                                //print("<pre>".print_r (1566,true )."</pre>");
                                 //print("<pre>".print_r ($key,true )."</pre>");
                                 //print("<pre>".print_r ($subKey,true )."</pre>");
                                 //print("<pre>".print_r ($value,true )."</pre>");
                                 //print("<pre>".print_r ($subsubkey,true )."</pre>");
                                   //print("<pre>".print_r ($subsubvalue,true )."</pre>");
                                        if (is_string($subsubvalue) && strtolower($subsubvalue) !== 'unknown') {
                                            $processedArray[] =(($this->model_shopmanager_tools->splitNames($subsubvalue)));
                                          //print("<pre>".print_r ($processedArray,true )."</pre>");
                                        } elseif (is_array($subsubvalue)){
                                        //print("<pre>".print_r ($subsubvalue,true )."</pre>");
                                            foreach ($subsubvalue as $subsubsubkey => $subsubsubvalue) {
                                          //print("<pre>".print_r ($subsubsubvalue,true )."</pre>");
                                                if (is_string($subsubsubvalue['value']) && strtolower($subsubsubvalue['value']) !== 'unknown') {
                                                    $processedArray[] =(($this->model_shopmanager_tools->splitNames($subsubsubvalue['value'])));
                                                } 
                                            }
                               //         $processedArray[] =(($this->model_shopmanager_tools->splitNames($subsubvalue['value'])));
                                        }
                                    }
                                }
                            //print("<pre>".print_r (1560,true )."</pre>");
                            //print("<pre>".print_r ($processedArray,true )."</pre>");
                            }
                        }
        
                        // Ajouter le tableau nettoyé au résultat s'il n'est pas vide
                        if (!empty($processedArray)) {
                            $result[$key] =($this->model_shopmanager_tools->removeArrayDuplicates($this->model_shopmanager_tools->flattenArray($processedArray)));
                        }
                  //print("<pre>".print_r (1569,true )."</pre>");
                        
                   //print("<pre>".print_r ($key,true )."</pre>");
                    //print("<pre>".print_r ($subKey,true )."</pre>");
                   //print("<pre>".print_r ($value,true )."</pre>");
                  //print("<pre>".print_r ($result[$key],true )."</pre>");
                    }else{
                                 //print("<pre>".print_r (1617,true )."</pre>");
                                        //print("<pre>".print_r ($key,true )."</pre>");
                               
                                    //print("<pre>".print_r ($value,true )."</pre>");
                             
                         
                }
            } else {
                // Vérifier s'il y a des underscores dans la valeur et les remplacer par des espaces
                if (is_string($value) && strpos($value, '_') !== false) {
                    $value = str_replace('_', ' ', $value);
                    // Ne pas utiliser ucwords(strtolower()) car cela détruit les caractères Unicode (Ș, ă, etc.)
                    $value = $this->model_shopmanager_tools->cleanStringValue($value);            
                    
                }else{
                  //print("<pre>".print_r (1631,true )."</pre>");
                //print("<pre>".print_r ($key,true )."</pre>");
           
                 //print("<pre>".print_r ($value,true )."</pre>");
                }        
                    // Ajouter la valeur au tableau de résultats si ce n'est pas un tableau
                    $result[$key] = $this->model_shopmanager_tools->convertToYear(($value));

                   
                }
            }
       //print("<pre>".print_r (1588,true )."</pre>");
        //print("<pre>".print_r ($result,true )."</pre>");
            return $result;
        }

        // Fonction pour traiter les contributeurs
private function processContributors($contributors) {
    $this->load->model('shopmanager/tools');
    $result = [];
    $exclusionCriteria = ['amazon', 'unknown', 'not_applicable'];
    // Vérifier si c'est un tableau de contributeurs avec indices 0, 1, 2, etc.
    if (isset($contributors[0])) {
      
        foreach ($contributors as $contributor) {

            if (isset($contributor['name']) && isset($contributor['role'])) {

             //print("<pre>" . print_r($contributor, true) . "</pre>");
                if (is_array($contributor['role']) && count($contributor['role'])>1){
                    
                    foreach ($contributor['role'] as $nbkey=>$value) {
                        $result[$value][$nbkey] = trim($contributor['name'][$nbkey]);
                    }
    
                }else{
                // Appeler la fonction pour traiter les noms
                
                    if (is_array($contributor['name'])){
                        $namesArray = $contributor['name'];
                    }else{
                        $namesArray = $this->model_shopmanager_tools->splitNames($contributor['name']);

                    }
                    foreach ($namesArray as $name) {
                        if (!$this->containsExclusionCriteria($name, $exclusionCriteria)) {

                            $result[$contributor['role']][] = trim($name);
                        }
                    }
                }
            }
        }
    } else {
        // Si c'est un contributeur unique avec 'name' et 'role'
        if (isset($contributors['name']) && isset($contributors['role'])) {
            //print("<pre>" . print_r($contributors, true) . "</pre>");
            if(is_array($contributors['role'])){
                $contributors= $this->model_shopmanager_tools->cleanArray($contributors);
                if(count( $contributors['name'])==0){
                    $contributors['name'] = $contributors['name'][0];
                    $result[$contributors['role']][] = trim($contributors['name']);
                }else{
                    $contributors['name'] = implode(';',$contributors['name']);
                }
               
            }else{
                //print("<pre>" . print_r($contributors, true) . "</pre>");
                $namesArray = $this->model_shopmanager_tools->splitNames($contributors['name']);
               //print("<pre>" . print_r($namesArray, true) . "</pre>");
                foreach ($namesArray as $name) {
                    if (!$this->containsExclusionCriteria($name, $exclusionCriteria)) {
    
                        $result[$contributors['role']][] = trim($name);
                    }
                }
            }
            
        }
    }

    return $result;
}
private function containsExclusionCriteria($name, $exclusionCriteria) {
    $this->load->model('shopmanager/tools');

    foreach ($exclusionCriteria as $criteria) {
        if (stripos($name, $criteria) !== false) {
            return true; // Le nom contient un critère d'exclusion
        }
    }
    return false; // Le nom ne contient aucun critère d'exclusion
}


    
public function getInfoSources($value) {
    $this->load->model('shopmanager/tools');

    // Récupérer les informations de la table oc_product_info_sources basées sur le UPC
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_info_sources` WHERE upc = '" . $this->db->escape($value) . "' OR  product_id = '" . $this->db->escape($value) . "' ");
 //print("<pre>".print_r (1152,true )."</pre>");  
//	//print("<pre>".print_r ("SELECT * FROM `" . DB_PREFIX . "product_info_sources` WHERE upc = '" . $this->db->escape($value) . "' OR  product_id = '" . $this->db->escape($value) . "' ",true )."</pre>"); 
    // Si une entrée est trouvée, retourner les informations du produit
    if ($query->num_rows) {
        return $query->row;
    } else {
        return null; // Retourner false si aucune entrée n'est trouvée
    }
}

public function addInfoSources($data) {
    $this->load->model('shopmanager/tools');

    // Ajouter une nouvelle entrée dans la table oc_product_info_sources
        if(isset($data['upc'])){
            $upc = " upc='" . $this->db->escape($data['upc']??'') . "', ";
        }else{
            $upc ='';
        }
    $this->db->query("INSERT INTO `" . DB_PREFIX . "product_info_sources` SET 
        ".$upc."
        product_id = '" . $this->db->escape($data['product_id']??'') . "', 
        epid = " . (!isset($data['epid']) ? "NULL" : "'" . $this->db->escape($data['epid']) . "'") . ", 
        epid_details = " . (!isset($data['epid_details']) ? "NULL" : "'" . $this->db->escape(($data['epid_details'])) . "'") . ", 
        ebay_search = " . (!isset($data['ebay_search']) ? "NULL" : "'" . $this->db->escape(($data['ebay_search'])) . "'") . ", 
        ebay_pricevariant = " . (!isset($data['ebay_pricevariant']) ? "NULL" : "'" . $this->db->escape(($data['ebay_pricevariant'])) . "'") . ", 
        ebay_category = " . (!isset($data['ebay_category']) ? "NULL" : "'" . $this->db->escape(($data['ebay_category'])) . "'") . ", 
        ebay_specific_info = " . (!isset($data['ebay_specific_info']) ? "NULL" : "'" . $this->db->escape(($data['ebay_specific_info'])) . "'") . ", 
        algopix_search = " . (isset($data['algopix_search']) && $data['algopix_search'] !== 'null' ? "'" . $this->db->escape($data['algopix_search']) . "'" : "NULL") . ", 
        algopix_search_fr = " . (isset($data['algopix_search_fr']) && $data['algopix_search_fr'] !== 'null' ? "'" . $this->db->escape($data['algopix_search_fr']) . "'" : "NULL") . ",
        upc_tmp_search = " . (isset($data['upc_tmp_search']) && $data['upc_tmp_search'] !== 'null' ? "'" . $this->db->escape($data['upc_tmp_search']) . "'" : "NULL") . ", 
        google_search = " . (isset($data['google_search']) && $data['google_search'] !== 'null' ? "'" . $this->db->escape($data['google_search']) . "'" : "NULL") . ", 
        date_added = '" . date('Y-m-d H:i:s') . "', 
        date_modified = '" . date('Y-m-d H:i:s') . "'  
    ");
}

public function editInfoSources($data) {
    $this->load->model('shopmanager/tools');

    // Mettre à jour l'enregistrement existant basé sur le UPC
 //print("<pre>" . print_r('1897:prodcut_search.php', true) . "</pre>");
 //print("<pre>" . print_r($data, true) . "</pre>");

    $this->db->query("UPDATE `" . DB_PREFIX . "product_info_sources` SET 
        epid = " . (!isset($data['epid']) ? "NULL" : "'" . $this->db->escape($data['epid']) . "'") . ", 
        epid_details = " . (!isset($data['epid_details']) ? "NULL" : "'" . $this->db->escape(($data['epid_details'])) . "'") . ", 
        ebay_search = " . (!isset($data['ebay_search']) ? "NULL" : "'" . $this->db->escape(($data['ebay_search'])) . "'") . ", 
        ebay_pricevariant = " . (!isset($data['ebay_pricevariant']) ? "NULL" : "'" . $this->db->escape(($data['ebay_pricevariant'])) . "'") . ", 
        ebay_category = " . (!isset($data['ebay_category']) ? "NULL" : "'" . $this->db->escape(($data['ebay_category'])) . "'") . ", 
        ebay_specific_info = " . (!isset($data['ebay_specific_info']) ? "NULL" : "'" . $this->db->escape(($data['ebay_specific_info'])) . "'") . ", 
        algopix_search = " . (isset($data['algopix_search']) && $data['algopix_search'] !== 'null' ? "'" . $this->db->escape($data['algopix_search']) . "'" : "NULL") . ", 
        algopix_search_fr = " . (isset($data['algopix_search_fr']) && $data['algopix_search_fr'] !== 'null' ? "'" . $this->db->escape($data['algopix_search_fr']) . "'" : "NULL") . ",
        upc_tmp_search = " . (isset($data['upc_tmp_search']) && $data['upc_tmp_search'] !== 'null' ? "'" . $this->db->escape($data['upc_tmp_search']) . "'" : "NULL") . ", 
        google_search = " . (isset($data['google_search']) && $data['google_search'] !== 'null' ? "'" . $this->db->escape($data['google_search']) . "'" : "NULL") . ", 
        date_modified = '" . date('Y-m-d H:i:s') . "' 
        WHERE upc = '" . $this->db->escape($data['upc']) . "'
    ");
}

public function getInfoSourcesPrice($upc = null, $product_id = null){
    $this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/ai');
	$this->load->model('shopmanager/algopix');
	$this->load->model('shopmanager/upctmp');
	$this->load->model('shopmanager/google');
    $this->load->model('shopmanager/tools');


    if($upc){    // Récupérer les informations actuelles du produit à partir de la table
        $product_info = $this->getInfoSources($upc)??null;
   
        $threshold = 60 * 24 * 60 * 60;
        $current_time = time();
 
        if ($product_info) {
            // Convertir date_modified en timestamp
            $date_modified = strtotime($product_info['date_modified']);
            
            // Vérifier si les données doivent être rafraîchies
            if (($current_time - $date_modified) > $threshold || $product_info['ebay_search'] == null) {
                $product_info = $this->getInfoFromSources($upc);
                if ($product_info) {
                    $this->editInfoSources($product_info);
                } else {
                    return null;
                }

            }else{
                return $product_info;
            }
        }else{
           $product_info = $this->getInfoFromSources($upc);
            if ($product_info) {
                $this->addInfoSources($product_info);
            } else {
                return null;
            }
        }
      
    
    }elseif($product_id){
        $product_info = $this->getInfoSources($product_id)??null;
    }else{
        return null;
    }

   /* if (!$ebay_search && isset($algopix_search['commonAttributes']['title'])) {
        $ebay_search = $this->model_shopmanager_ebay->get($algopix_search['commonAttributes']['title'], $data['product_categories'][0]['name_category']??null, null, 100);
        $data['ebay_search'] = $ebay_search ?? null;
        $data['epid'] = $ebay_search['epid'] ?? null;

        // Mettre à jour les informations de la table `product_info_sources` avec les nouvelles données eBay
        $this->model_shopmanager_product_search->editInfoSources($upc, [
            'ebay_search' => $ebay_search,
            'algopix_search' => $algopix_search,
            'upc_tmp_search' => $upc_tmp_search,
            'google_search' => $google_search,
            'epid_details' => $epid_details,
            'epid' =>  $epid,
        ]);
    }*/
    return $product_info;
}

private function getInfoFromSources($upc = null){
    $this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/ai');
	$this->load->model('shopmanager/algopix');
	$this->load->model('shopmanager/upctmp');
	$this->load->model('shopmanager/google');
    $this->load->model('shopmanager/tools');

    $ebay_search=$this->model_shopmanager_ebay->get($upc);
        if (isset($ebay_search['items'][0])) {
            $product_info['ebay_search'] = json_encode($ebay_search['items']);
        } elseif (isset($ebay_search['items'])) {
            $product_info['ebay_search'] = json_encode([0 => $ebay_search['items']]);
        } else {
            $product_info['ebay_search'] = null;
        }
        //print("<pre>".print_r ($ebay_search,true )."</pre>");
    $product_info['ebay_pricevariant'] = $ebay_search['pricevariant']?json_encode($this->model_shopmanager_ebay->calculateMissingPrices($ebay_search['pricevariant'])):null;
    $product_info['ebay_category'] = $ebay_search['category']?json_encode($ebay_search['category']):null;
    $product_info['ebay_specific_info'] = $ebay_search['specific_info']?json_encode($ebay_search['specific_info']):null;

    if(isset($ebay_search['epid'][0]) && is_numeric($ebay_search['epid'][0])){
        $product_info['epid'] = (int) $ebay_search['epid'][0];
        $product_info['epid_details'] = json_encode($ebay_search['epid_details']);

    }elseif(isset($ebay_search['epid']) && is_numeric($ebay_search['epid'])){
        $product_info['epid'] = (int) $ebay_search['epid'];
        $product_info['epid_details'] = json_encode($ebay_search['epid_details']);
    }

    $product_info['algopix_search'] = json_encode($this->model_shopmanager_algopix->get($upc, 'UPC' , ['ENGLISH_US']));
    $product_info['algopix_search_fr'] = json_encode($this->model_shopmanager_algopix->get($upc,'UPC' , ['ENGLISH_CA']));
   
    $product_info['upc_tmp_search'] = NULL;// json_encode($this->model_shopmanager_upctmp->get($upc));
    $product_info['google_search'] = json_encode($this->model_shopmanager_google->get($upc));   

    return $product_info;
}

            
          
       

public function getSpecificsPRODUCTSEARCH($product_id = null,$product_info = null, $category_specific_info = null,$source_value = null,$epid_sources = null) {

    $this->load->model('localisation/language');
    $this->load->model('shopmanager/catalog/product');
    //$this->load->model('shopmanager/translate");
    $this->load->model('shopmanager/ai');
    $this->load->model('shopmanager/tools');
    $categorySpecifics=[];
    $source_merge=[];
   
    
$execution_times = [];
$n=0;
$start_time = microtime(true);

if (isset($source_value)) {
    $source_value['Unit Type'] = 'Unit';
//      $source_value = $this->model_shopmanager_tools->cleanArray($source_value);
  //print("<pre>".print_r ('jo107',true )."</pre>");
  //print("<pre>".print_r ($source_value,true )."</pre>");
//    $categorySpecifics=$this->model_shopmanager_tools->processCategorySpecifics($source_value, $category_specific_info);
   //print("<pre>".print_r ('139:ai.php',true )."</pre>");
   //print("<pre>".print_r ($source_value,true )."</pre>");
    $source_merge =(isset($epid_sources) && is_array($epid_sources))?$this->model_shopmanager_tools->compareSources( $this->model_shopmanager_tools->cleanArray($epid_sources), $this->model_shopmanager_tools->cleanArray($source_value)):$this->model_shopmanager_tools->cleanArray($source_value);//$source_value;//$this->model_shopmanager_tools->cleanArray($source_value);
      //print("<pre>".print_r ('139:ai.php',true )."</pre>");
   //print("<pre>".print_r ($source_merge,true )."</pre>");
    $categorySpecifics=$this->model_shopmanager_tools->processCategorySpecifics($source_merge, $category_specific_info);
 

}
$resultspecifics=array();
if(!isset($categorySpecifics)){
    return null;
}else{
   //print("<pre>".print_r ($categorySpecifics,true )."</pre>");
    foreach ($categorySpecifics as $key=>$categorySpecific) {
     //   $categorySpecific= (strtolower ($key)=='unit quantity' && strtolower ($categorySpecific)=='none')?1:$categorySpecific;
     //   $categorySpecific= (strtolower ($key)=='unit type' && strtolower ($categorySpecific)=='none')?'Unit':$categorySpecific;
     //   $categorySpecific= (strtolower ($key)=='brand' && strtolower ($categorySpecific)=='none')?$product_info['brand']:$categorySpecific;
   //     if(strtolower ($key)=='mpn' ){
    //        unset($categorySpecifics['mpn']);
  //      }
        $categorySpecific=str_replace('"','',$categorySpecific);
//print("<pre>".print_r ($key,true )."</pre>");
//print("<pre>".print_r ($categorySpecific,true )."</pre>");
        if(!is_array($categorySpecific)){
            $categorySpecific= (strtolower ($categorySpecific)!='none')?$categorySpecific:'';
            $categorySpecific = ltrim($categorySpecific);
            $categorySpecific = rtrim($categorySpecific, '.');
        }
        if(strtolower ($key)!='region code'){
        /*  if(($categorySpecific[0]=='') && isset($source_value)){
                $categorySpecific=json_decode($source_value,true);
                //print("<pre>".print_r ('No result',true )."</pre>");
            }*/
        //print("<pre>".print_r ('189:ai.php',true )."</pre>");
        //print("<pre>".print_r ($key,true )."</pre>");
          //print("<pre>".print_r ($source_value[$key],true )."</pre>");
         //print("<pre>".print_r ($categorySpecific,true )."</pre>");
        //    $categorySpecific=(($categorySpecific=='') && isset($source_value[$key]))?implode(',',json_decode($source_value[$key],true)):$categorySpecific;
        if (isset($source_value[$key])) {
            if (!is_array($source_value[$key])) {
                $decoded_value = json_decode($source_value[$key], true);
            } else {
                $decoded_value = $source_value[$key];
                if(is_array($decoded_value) && count($decoded_value) >10){
                  // $this->model_shopmanager_ai->reduceArrayValue($decoded_value,$key);
                    $decoded_value=$this->model_shopmanager_ai->reduceArrayValue($decoded_value,$key);
                }
            }
        } else {
            $decoded_value = null;
        }
        
        // Vérifier si le résultat est un tableau avant d'utiliser implode()
        $categorySpecific = (($categorySpecific == '') && isset($source_value[$key]) && is_array($decoded_value)) 
            ? implode(',', $decoded_value) 
            : $categorySpecific;
          //print("<pre>".print_r ('230:ai.php',true )."</pre>");
         //print("<pre>".print_r ($categorySpecific,true )."</pre>");
       //     $categorySpecific=(($categorySpecific=='') && isset($source_value[$key]))?implode(',',($source_value[$key])):$categorySpecific;
            $categorySpecific=str_replace(',','@@',$categorySpecific);
        }else {
            // Pour 'region code', ajouter "DVD: 1 (US, Canada...)" si nécessaire
            $categorySpecificArray = array_filter(explode('@@', $categorySpecific)); // Utiliser @@ comme séparateur
            if (!in_array('DVD: 1 (US, Canada...)', $categorySpecificArray)) {
                // Ajouter "DVD: 1 (US, Canada...)" s'il n'est pas présent
                $categorySpecificArray[] = 'DVD: 1 (US, Canada...)';
            }
            
            $categorySpecific = implode('@@', $categorySpecificArray); // Recréer la chaîne avec @@ comme séparateur
        }
      //print("<pre>".print_r ($categorySpecific,true )."</pre>");
        $value_to_trim=explode('@@',$this->model_shopmanager_tools->escape_special_chars($categorySpecific));
        foreach($value_to_trim as $key_value=>$value){
            $value_to_trim[$key_value]=trim($value);

        }
        $resultspecifics[$key]=array (
            'Name' 	=>$this->model_shopmanager_tools->escape_special_chars($key),
            'Value' => $value_to_trim
        );
    
}
//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
//print("<pre>".print_r ($resultspecifics,true )."</pre>");
// sort specifics 
// Initialiser un tableau de résultat trié.
$sorting_result = [];

// Parcourir chaque élément spécifique dans le tableau des aspects.
foreach ($category_specific_info['specifics'] as $specific_name=>$aspect) {
  //print("<pre>".print_r ($aspect,true )."</pre>");
    // Vérifier si l'aspect existe dans le tableau $resultspecifics avant de l'ajouter à $sorting_result.
    if (isset($resultspecifics[$specific_name])) {
        $sorting_result[$specific_name] = $resultspecifics[$specific_name];
        unset($resultspecifics[$specific_name]); // Supprimer l'élément de $resultspecifics pour éviter les doublons.
    }
}

// Fusionner les éléments triés avec les éléments restants dans $resultspecifics.
$resultspecifics = array_merge($sorting_result, $resultspecifics);

if(isset($source_merge)){
   
    foreach($source_merge as $aspect=>$value){
           // Utiliser la valeur dans $source_value si elle existe
            
            $resultspecifics[$aspect]['VerifiedSource'] = 'yes';
      
    }
 //print("<pre>".print_r ($resultspecifics,true )."</pre>");
}
$languages = $this->model_localisation_language->getLanguages();
       
        foreach($languages as $language){
            if( $language['code']=='en'){
                if(isset($product_id)){
                    $this->model_shopmanager_catalog_product->editSpecifics($product_id, json_encode($resultspecifics),$language['language_id']);
                }
                $productspecifics[$language['language_id']]=$resultspecifics;
              //print("<pre>".print_r ($productspecifics,true )."</pre>");
            }else{
               $product_specific_info=$resultspecifics;
           //    $this->load->model('shopmanager/catalog/product_specific');
             //  $this->model_shopmanager_product_specific->translateMissingTerms();        
           //print("<pre>".print_r ($productspecifics,true )."</pre>");
                $productspecifics[$language['language_id']]= $this->model_shopmanager_ai->translate_specifics($product_id,$product_specific_info,$language);

               //print("<pre>".print_r ('298:ai.php',true )."</pre>");
               //print("<pre>".print_r ($productspecifics,true )."</pre>");
                if(isset($product_id)){
                    $this->model_shopmanager_catalog_product->editSpecifics($product_id, json_encode($productspecifics[$language['language_id']]),$language['language_id']);
                }
                unset($product_specific_info);
            }

        }
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
   //print("<pre>".print_r ('299:ai.php' ,true )."</pre>");
  //print("<pre>".print_r ($execution_times ,true )."</pre>");
		$total_execution_time = array_sum($execution_times);
  //      echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";

            return  $productspecifics;
        }
}



private function processCategorySpecifics($source_value, $category_specific_info) {
    $this->load->model('shopmanager/tools');

    $categorySpecifics = [];
    $decoded_string='';
    // Parcourir chaque aspect et sa valeur dans $source_value
    foreach ($source_value as $aspect => $value) {
        // Vérifie si $category_specific_info['specifics'] existe et contient l'aspect
        $category_specific_tocheck = $category_specific_info['specifics'][$aspect] ?? null;

        if ($category_specific_tocheck && isset($category_specific_tocheck['aspectConstraint']['itemToAspectCardinality'])) {
            $cardinality = $category_specific_tocheck['aspectConstraint']['itemToAspectCardinality'];

            // Vérifier et formater en fonction de 'itemToAspectCardinality'
            if ($cardinality == 'SINGLE' && $category_specific_tocheck['aspectConstraint']['aspectMode'] == 'SELECTION_ONLY') {
                $string = is_array($value) ? implode(' ', $value) : str_replace(',', ' ', $value);
                $decoded_string = trim(html_entity_decode($string, ENT_QUOTES | ENT_HTML5));
            } elseif ($cardinality == 'SINGLE' && $category_specific_tocheck['aspectConstraint']['aspectMode'] == 'FREE_TEXT') {
                $string = is_array($value) ? implode('@@', $value) : $value;
                $decoded_string = trim(html_entity_decode($string, ENT_QUOTES | ENT_HTML5));
            } elseif ($cardinality == 'MULTI') {
                $string = is_array($value) ? implode('@@', $value) : $value;
                $decoded_string = trim(html_entity_decode($string, ENT_QUOTES | ENT_HTML5));
            }
        } else {
            // Si aucun aspectConstraint n'est défini, traiter comme un texte simple
            $string = is_array($value) ? implode(',', $value) : $value;
            $decoded_string = trim(html_entity_decode($string, ENT_QUOTES | ENT_HTML5));
        }

        // Si le string décodé n'est pas vide, l'ajouter à categorySpecifics
        if (!empty($decoded_string)) {
            $categorySpecifics[$aspect] = $decoded_string;
        } else {
            // Supprime l'aspect si la valeur est vide
            unset($source_value[$aspect]);
        }
    }

    return $categorySpecifics;
}



        
    public function getSpecifics($product_id = null,$product_info = null, $category_specific_info = null,$source_value = null,$epid_sources = null) {

        $this->load->model('localisation/language');
        $this->load->model('shopmanager/catalog/product');
        //$this->load->model('shopmanager/translate");
        $this->load->model('shopmanager/tools');
        $this->load->model('shopmanager/ai');
        $categorySpecifics=[];
        $source_merge=[];
        
        
	$execution_times = [];
	$n=0;
    $start_time = microtime(true);

    if (isset($source_value)) {
        $source_value['Unit Type'] = 'Unit';
  //      $source_value = $this->model_shopmanager_tools->cleanArray($source_value);
    //print("<pre>".print_r ('jo107',true )."</pre>");
   //print("<pre>".print_r ($source_value,true )."</pre>");
    //    $categorySpecifics=$this->model_shopmanager_tools->processCategorySpecifics($source_value, $category_specific_info);
       //print("<pre>".print_r ('139:ai.php',true )."</pre>");
       //print("<pre>".print_r ($source_value,true )."</pre>");
        $source_merge =isset($epid_sources)?$this->model_shopmanager_tools->compareSources( $this->model_shopmanager_tools->cleanArray($epid_sources), $this->model_shopmanager_tools->cleanArray($source_value)):$this->model_shopmanager_tools->cleanArray($source_value);
        $source_merge = $this->model_shopmanager_tools->cleanArray($source_merge);

        //     $source_merge =isset($epid_sources)?$this->model_shopmanager_tools->compareSources( ($epid_sources), ($source_value)):($source_value);

   
        //print("<pre>".print_r ('139:ai.php',true )."</pre>");
       //print("<pre>".print_r ($source_merge,true )."</pre>");
        $categorySpecifics=$this->model_shopmanager_tools->processCategorySpecifics($source_merge, $category_specific_info);
     
     //print("<pre>".print_r ('1548product_search.php',true )."</pre>");    
     //print("<pre>".print_r ($source_merge,true )."</pre>");     
    }
    //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
  /*  if(isset($category_specific_info['specifics']['Unit Type'])){
        $categorySpecifics['Unit Type'] = 'Unit';
        
    }*/

        foreach ($category_specific_info['specifics'] as $aspect) {
         /*   if (!isset($source_value[$aspect['localizedAspectName']]) && $aspect['localizedAspectName']!='Unit Type' && !isset($epid_sources)) {

                // Sinon, utiliser la fonction getSpecific
                $categorySpecifics[$aspect['localizedAspectName']] = $this->model_shopmanager_ai->getSpecific(
                    $product_info,
                    $aspect['localizedAspectName'],
                    $aspect['aspectValues'] ?? [],
                    $aspect['aspectConstraint'],
                    json_encode($source_value)
                );
            }elseif ((!isset($source_value[$aspect['localizedAspectName']]) && $aspect['localizedAspectName']!='Unit Type' && isset($epid_sources))){
                $categorySpecifics[$aspect['localizedAspectName']] = '';
               $categorySpecifics[$aspect['localizedAspectName']] = $this->model_shopmanager_ai->getSpecific(
                    $product_info,
                    $aspect['localizedAspectName'],
                    $aspect['aspectValues'] ?? [],
                    $aspect['aspectConstraint'],
                    json_encode($source_value)
                );
            }*/
          
        }
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
               //print("<pre>".print_r ($categorySpecifics,true )."</pre>");
                $resultspecifics=array();
            if(!isset($categorySpecifics)){
                return null;
            }else{
               //print("<pre>".print_r ($categorySpecifics,true )."</pre>");
                foreach ($categorySpecifics as $key=>$categorySpecific) {
                 //   $categorySpecific= (strtolower ($key)=='unit quantity' && strtolower ($categorySpecific)=='none')?1:$categorySpecific;
                 //   $categorySpecific= (strtolower ($key)=='unit type' && strtolower ($categorySpecific)=='none')?'Unit':$categorySpecific;
                 //   $categorySpecific= (strtolower ($key)=='brand' && strtolower ($categorySpecific)=='none')?$product_info['brand']:$categorySpecific;
               //     if(strtolower ($key)=='mpn' ){
                //        unset($categorySpecifics['mpn']);
              //      }
                    $categorySpecific=str_replace('"','',$categorySpecific);
           //print("<pre>".print_r ($key,true )."</pre>");
            //print("<pre>".print_r ($categorySpecific,true )."</pre>");
                    if(!is_array($categorySpecific)){
                        $categorySpecific= (strtolower ($categorySpecific)!='none')?$categorySpecific:'';
                        $categorySpecific = ltrim($categorySpecific);
                        $categorySpecific = rtrim($categorySpecific, '.');
                    }
                    if(strtolower ($key)!='region code'){
                    /*  if(($categorySpecific[0]=='') && isset($source_value)){
                            $categorySpecific=json_decode($source_value,true);
                            //print("<pre>".print_r ('No result',true )."</pre>");
                        }*/
                    //print("<pre>".print_r ('189:ai.php',true )."</pre>");
                    //print("<pre>".print_r ($key,true )."</pre>");
                      //print("<pre>".print_r ($source_value[$key],true )."</pre>");
                     //print("<pre>".print_r ($categorySpecific,true )."</pre>");
                    //    $categorySpecific=(($categorySpecific=='') && isset($source_value[$key]))?implode(',',json_decode($source_value[$key],true)):$categorySpecific;
                    if (isset($source_value[$key])) {
                        if (!is_array($source_value[$key])) {
                            $decoded_value = json_decode($source_value[$key], true);
                        } else {
                            $decoded_value = $source_value[$key];
                            if(is_array($decoded_value) && count($decoded_value) >10){
                              // $this->model_shopmanager_ai->reduceArrayValue($decoded_value,$key);
                                $decoded_value=$this->model_shopmanager_ai->reduceArrayValue($decoded_value,$key);
                            }
                        }
                    } else {
                        $decoded_value = null;
                    }
                    
                    // Vérifier si le résultat est un tableau avant d'utiliser implode()
                    $categorySpecific = (($categorySpecific == '') && isset($source_value[$key]) && is_array($decoded_value)) 
                        ? implode(',', $decoded_value) 
                        : $categorySpecific;
                      //print("<pre>".print_r ('230:ai.php',true )."</pre>");
                     //print("<pre>".print_r ($categorySpecific,true )."</pre>");
                   //     $categorySpecific=(($categorySpecific=='') && isset($source_value[$key]))?implode(',',($source_value[$key])):$categorySpecific;
                        $categorySpecific=str_replace(',','@@',$categorySpecific);
                    }else {
                        // Pour 'region code', ajouter "DVD: 1 (US, Canada...)" si nécessaire
                        $categorySpecificArray = array_filter(explode('@@', $categorySpecific)); // Utiliser @@ comme séparateur
                        if (!in_array('DVD: 1 (US, Canada...)', $categorySpecificArray)) {
                            // Ajouter "DVD: 1 (US, Canada...)" s'il n'est pas présent
                            $categorySpecificArray[] = 'DVD: 1 (US, Canada...)';
                        }
                        
                        $categorySpecific = implode('@@', $categorySpecificArray); // Recréer la chaîne avec @@ comme séparateur
                    }
                  //print("<pre>".print_r ($categorySpecific,true )."</pre>");
                    $value_to_trim=explode('@@',$this->model_shopmanager_tools->escape_special_chars($categorySpecific));
                    foreach($value_to_trim as $key_value=>$value){
                        $value_to_trim[$key_value]=trim($value);

                    }
                    $resultspecifics[$key]=array (
                        'Name' 	=>$this->model_shopmanager_tools->escape_special_chars($key),
                        'Value' => $value_to_trim
                    );
                
            }
          
            //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
       //print("<pre>".print_r ($resultspecifics,true )."</pre>");
            // sort specifics 
            // Initialiser un tableau de résultat trié.
            $sorting_result = [];

            // Parcourir chaque élément spécifique dans le tableau des aspects.
            foreach ($category_specific_info['specifics'] as $specific_name=>$aspect) {
              //print("<pre>".print_r ($aspect,true )."</pre>");
                // Vérifier si l'aspect existe dans le tableau $resultspecifics avant de l'ajouter à $sorting_result.
                if (isset($resultspecifics[$specific_name])) {
                    $sorting_result[$specific_name] = $resultspecifics[$specific_name];
                    unset($resultspecifics[$specific_name]); // Supprimer l'élément de $resultspecifics pour éviter les doublons.
                }
            }

            // Fusionner les éléments triés avec les éléments restants dans $resultspecifics.
            $resultspecifics = array_merge($sorting_result, $resultspecifics);
           
            if(isset($source_merge)){
               
                foreach($source_merge as $aspect=>$value){
                       // Utiliser la valeur dans $source_value si elle existe
                        
                        $resultspecifics[$aspect]['VerifiedSource'] = 'yes';
                  
                }
             //print("<pre>".print_r ($resultspecifics,true )."</pre>");
            }
         //print("<pre>".print_r ($resultspecifics,true )."</pre>");
		$languages = $this->model_localisation_language->getLanguages();
       
        foreach($languages as $language){
            if( $language['code']=='en'){
                if(isset($product_id)){
                    $this->model_shopmanager_catalog_product->editSpecifics($product_id, json_encode($resultspecifics),$language['language_id']);
                }
                $productspecifics[$language['language_id']]=$resultspecifics;
              //print("<pre>".print_r ($productspecifics,true )."</pre>");
            }else{
               $product_specific_info=$resultspecifics;
           //    $this->load->model('shopmanager/catalog/product_specific');
             //  $this->model_shopmanager_product_specific->translateMissingTerms();     
         
                $productspecifics[$language['language_id']]= $this->model_shopmanager_ai->translate_specifics($product_id,$product_specific_info,$language);

               //print("<pre>".print_r ('298:ai.php',true )."</pre>");
               //print("<pre>".print_r ($productspecifics,true )."</pre>");
                if(isset($product_id)){
                    $this->model_shopmanager_catalog_product->editSpecifics($product_id, json_encode($productspecifics[$language['language_id']]),$language['language_id']);
                }
                unset($product_specific_info);
            }

        }
        //$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
   //print("<pre>".print_r ('299:ai.php' ,true )."</pre>");
  //print("<pre>".print_r ($execution_times ,true )."</pre>");
		$total_execution_time = array_sum($execution_times);
  //      echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";

            return  $productspecifics;
        }
    }

    public function getSearchData($upc, $product_id = null) {
        // ⚡ CACHE: Vérifier si déjà en mémoire (évite requêtes multiples)
        $cache_key = $product_id ?? $upc;
        if (isset(self::$cache_search_data[$cache_key])) {
            return self::$cache_search_data[$cache_key];
        }
        
        $start_time = microtime(true);
        $execution_times = [];
        $n = 1;
        $data = [];
    	$this->load->model('shopmanager/manufacturer');
        $this->load->model('shopmanager/tools');
        $this->load->model('shopmanager/ai');
        $this->load->model('shopmanager/catalog/product');
        $this->load->model('shopmanager/condition');
        $this->load->model('shopmanager/catalog/category');
        $this->load->model('shopmanager/catalog/product_specific');
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/ocr');

        if (!isset($upc) || !is_numeric($upc)) {
            return ['error' => 'Invalid UPC'];
        }
   
        if(isset($product_id)){
            $product_info = $this->model_shopmanager_catalog_product->getProduct($product_id);
			//$product_info['marketplace_accounts_id'] = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id]);

            //print("<pre>".print_r ($product_info,true )."</pre>");
        } else{
            $product_info_array = $this->model_shopmanager_catalog_product->getByUPC($upc);
            $product_info = reset($product_info_array);
        }
        // 1. Récupération des sources d'information produit
        $product_info_source = $this->manageInfoSources($upc);
        //print("<pre>".print_r ($product_info_source,true )."</pre>");
        // 2. Extraction des informations des différentes sources
        $sources = [
            'upc_tmp_search', 'google_search', 'algopix_search', 'algopix_search_fr', 
            'ebay_search', 'ebay_category', 'ebay_pricevariant', 
            'ebay_specific_info', 'epid', 'epid_details'
        ];
    
        foreach ($sources as $source) {
            $data[$source] = isset($product_info_source[$source]) 
                ? json_decode($product_info_source[$source], true) 
                : null;
        }

        // Vérification et nettoyage des doublons dans primaryCategoryId
        if (isset($data['epid_details']['primaryCategoryId'])) {
            $data['epid_details']['primaryCategoryId'] = $this->model_shopmanager_tools->removeArrayDuplicates($data['epid_details']['primaryCategoryId']);
        } else {
            $data['epid_details']['primaryCategoryId'] = '';
        }

        // Traitement des catégories et conditions eBay
        if (!empty($data['epid_details']['primaryCategoryId'])) {
            $category_id = is_array($data['epid_details']['primaryCategoryId'])
                ? reset($data['epid_details']['primaryCategoryId'])
                : $data['epid_details']['primaryCategoryId'];
        } elseif (!empty($data['ebay_category'][0]['category_id'])) {
            $category_id = $data['ebay_category'][0]['category_id'];
        } elseif (!empty($product_info['category_id'])) {
            $category_id = $product_info['category_id'];
        } else {
            $category_id = null;
        }

    
        if (!empty($data['algopix_search']['dimensions']['packageDimensions'])) { 
            $package_dimensions = $data['algopix_search']['dimensions']['packageDimensions'];		
            foreach ($package_dimensions as $dimension => $value) { 
                $data[$dimension] = $value;
            }
        }

        if (!empty($data['algopix_search']['identifiers'])) { 
            $identifiers = $data['algopix_search']['identifiers'];		
            foreach ($identifiers as $identifier) { 
                  // Vérifiez si le type est un tableau
                  if (is_array($identifier['type'])) {
                        $nb_types = count($identifier['type']);
                        foreach ($identifier['type'] as $index => $type) {
                            if (is_array($identifier['values']) && isset($identifier['values'][$index])) {
                                if (is_array($identifier['values'][$index])) {
                                // Si values est un tableau imbriqué
                                foreach ($identifier['values'][$index] as $value) { 
                                    $data[strtolower($type)] = $value;
                                }
                                
                                } else { 
                                    $data[strtolower($type)] = $identifier['values'][$index];
                                }
                            
                            }
                        }
                } else {
                    // Si le type n'est pas un tableau
                        if (is_array($identifier['values'])) {
                            foreach ($identifier['values'] as $value) { 
                                $data[strtolower($identifier['type'])] = $value;
                            }
                        } else { 
                            $data[strtolower($identifier['type'])] = $identifier['values'];
                        }
                }
                 
            }
        }
        $data['category_id'] = $category_id;
        $data['category_name'] = $data['ebay_category'][0]['category_name'] ?? $product_info['category_name'] ?? null;
    
        // Récupération des conditions en fonction de la catégorie eBay
        //print("<pre>" . print_r(value: '2028:PRODUCTSEARCH.php') . "</pre>");
        //print("<pre>".print_r ($category_id,true )."</pre>");
        $data['conditions'] = $this->model_shopmanager_condition->getConditionDetails($category_id);
    
        // 4. Gestion des images et titres
        $data['images'] = $this->model_shopmanager_product_search->getAllImageUrls(
            $data['upc_tmp_search'], $data['google_search'], $data['ebay_search'], 
            $data['algopix_search'], $data['algopix_search_fr'], $data['epid_details']
        );
      
        $data['titles'] = $this->model_shopmanager_product_search->getAllTitles(
            $data['upc_tmp_search'], $data['google_search'], $data['ebay_search'], 
            $data['algopix_search'], $data['algopix_search_fr'], $data['epid_details']
        );
      
// 5. Récupération des fabricants et marques
        $manufacturers = $this->model_shopmanager_product_search->getAllManufacturers($data);

        if (!empty($manufacturers)) {
            if (is_string($manufacturers)) {
                $manufacturers = [$manufacturers];
            }

            // Appel de `getManufacturer()` pour obtenir le bon fabricant
            $manufacturer_data = $this->model_shopmanager_product_search->getManufacturer($manufacturers);

            // Mise à jour des informations du fabricant dans $data
            $data['manufacturer'] = $manufacturer_data['name'] ?? null;
            $data['manufacturer_id'] = $manufacturer_data['manufacturer_id'] ?? null;
            $data['brand'] = $data['manufacturer'];
            $data['algopix_search']['brand'] = $data['brand'];
           
        }

        $product_description = isset($product_id)?$this->model_shopmanager_catalog_product->getDescriptions($product_id):null;

		// Vérifie si le nom en français (language_id = 1) est vide ou trop court
		$title_source = $product_description[1]['name'] ?? '';
		$category_id = $data['category_id'] ?? null;
		if (empty($title_source) || mb_strlen(trim($title_source)) < 5) {
			$title_result = $this->model_shopmanager_ai->getTitle($data['titles'], $category_id, $data);

			// Débogage si nécessaire
			// print("<pre>" . print_r('3670:product_search.php', true) . "</pre>");
			// print("<pre>" . print_r($title_result, true) . "</pre>");

			$data['title'] = $title_result['title'];
			$data['name_description'] = $title_result['title'];
            $data['name'] = $title_result['title'];
            $data['algopix_search']['product name'] = $title_result['title'];
			$data['short_title'] = $title_result['short_title'] ?? null;
		}else{
			$data['title'] =  $product_description[1]['name'];
			$data['name_description'] = $product_description[1]['name'];
            $data['name'] = $product_description[1]['name'];
            $data['algopix_search']['product name'] = $product_description[1]['name'];
			$data['short_title'] =  null;

		}
      
       

        //print("<pre>".print_r ('2491_producsearch',true )."</pre>");
        //print("<pre>".print_r ( $data['name'],true )."</pre>");
       
        //$data['short_title'] = $title_result['short_title'] ?? null;
        //$data['name_description'] = $title_result['title'];
        //print("<pre>".print_r ($data['epid_details'],true )."</pre>");

        // 7. Association des spécificités produit
        $category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($category_id, 1);
       
        
        $mergeArrayForSpecifics = [];

       /* if (!empty($data['epid_details'][0]['aspects'])) {
            $ebay_sources = $this->model_shopmanager_ebay->formatEpidDetails(
                $data['epid_details'][0]['aspects'], $category_specific_info
            );
            $mergeArrayForSpecifics = $ebay_sources;
        }*/
    
       /* if (!empty($data['ebay_specific_info'])) {

            $mergeArrayForSpecifics = $data['ebay_specific_info'];
        } elseif (!empty($data['upc_tmp_search']) && !empty($data['algopix_search'])) {
            $mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources(
                array_merge_recursive($data['upc_tmp_search'], $data['algopix_search']), 
                $mergeArrayForSpecifics
            );
        }*/
        //$mergeArrayForSpecifics = $data['ebay_specific_info'];
        $mergeArrayForStrings= [];
        $keysToMerge = [ 'algopix_search', 'algopix_search_fr', 'upc_tmp_search'];
       
        //print_r("<pre>" . print_r($data['epid_details'], true) . "</pre>");
        //print_r("<pre>" . print_r($data['ebay_specific_info'], true) . "</pre>");
        //print_r("<pre>" . print_r($data['algopix_search'], true) . "</pre>");
        //print_r("<pre>" . print_r($data['algopix_search_fr'], true) . "</pre>");
        //print_r("<pre>" . print_r($data['upc_tmp_search'], true) . "</pre>");
        
        foreach ($keysToMerge as $key) {
            if (!empty($data[$key])) {
                if (empty($mergeArrayForSpecifics)) {
                    $mergeArrayForSpecifics = $data[$key];
                } else {
                    $mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources(
                        array_merge_recursive($mergeArrayForSpecifics, $data[$key]),
                        $mergeArrayForSpecifics
                    );
                }
            }
        }

        //print_r("<pre>" . print_r($mergeArrayForSpecifics, true) . "</pre>");
        
        
        $specifics_result = $this->model_shopmanager_product_search->filterArrayForSpecifics($mergeArrayForSpecifics);
        //$data['product_data']=$specifics_result;
     
        

        unset($specifics_result['error'], $specifics_result['category_name'], $specifics_result['category_id']);
    
        $data['specifics_result'] = $specifics_result; 
        //print("<pre>" . print_r($data['specifics_result'], true) . "</pre>");
        $mergeArrayForStrings= $this->model_shopmanager_tools->allarrayToString($specifics_result);
        //print_r("<pre>" . print_r($mergeArrayForStrings, true) . "</pre>");
        $data_return=[];
        //$formatEpidDetailsToSpecifics = isset($data['epid_details']['aspects'])?$this->model_shopmanager_ebay->formatEpidDetailsToSpecifics($data['epid_details']['aspects']):[];
        //print("<pre>".print_r ($data['ebay_specific_info'],true )."</pre>");
        //$mergeArrayForSpecifics =  $this->model_shopmanager_tools->custom_merge_recursive($formatEpidDetailsToSpecifics, $data['ebay_specific_info']);
        //print("<pre>".print_r ($mergeArrayForSpecifics,true )."</pre>");
        if(isset($category_specific_info[1]['specifics'])){
            foreach ($category_specific_info[1]['specifics'] as $key => $category_specific) {
                $data_return[$key] = [       
                    'Name' => $key,
                    'Value' => (strtolower($key) == 'unit quantity') ? [1] : 
                            ((strtolower($key) == 'unit type') ? ['Unit'] : 
                            ((strtolower($key) == 'aspect ratio') ? ['find if its 16:9 or 4:3'] : 
                            ((strtolower($key) == 'subtitle language') ? ['find languages available then put english'] : ''))),
                    'VerifiedSource' => '',
                    'specific_info' => $category_specific
                ];
                        
                }
        }
        $specifics= $data['ebay_specific_info']??[];
        //print("<pre>".print_r ($data_return,true )."</pre>");
        /*$specifics = $this->model_shopmanager_tools->compareSources(
            array_merge_recursive($data['specifics_result'], $data['ebay_specific_info']),$mergeArrayForSpecifics);*/
        
                foreach ($specifics as $key => $specific) {
                    //print("<pre>".print_r ($specific,true )."</pre>");
                    if (isset($data_return[$key])) {
                        $data_return[$key] = array (
                            
                            
                                'Name' => $specific['Name']??'',
                                'Value' => $specific['Value']??'',
                                'VerifiedSource' => $specific['VerifiedSource']??'',
                                'specific_info' => $data_return[$key]['specific_info']
                            
                        );
                    }else{
                        $data_return[$key] = array (
                            
                            
                                'Name' => $specific['Name']??'',
                                'Value' => $specific['Value']??'',
                                'VerifiedSource' => $specific['VerifiedSource']??'',
                                'specific_info' => ''
                            
                        );
                    }
                }
            
        //print("<pre>".print_r ($data_return,true )."</pre>");
        //$data['specifics_result']=$data_return??[];
        $data['specifics_result'] = $this->model_shopmanager_ai->feedEmptySpecifics($data_return,$mergeArrayForStrings);
        //print("<pre>".print_r ($data['specifics_result'],true )."</pre>");
        // Nettoyage des données inutiles
    
        // 8. Vérification des clés spécifiques à la catégorie
       /* $category_specifics = $category_specific_info[1]['specifics'] ?? [];
        $category_specific_key = [];
    
        foreach ($specifics_result as $specific_key_name => $value) {
            $replacement_term = $this->model_shopmanager_product_specific->getSpecificKey($specific_key_name, $category_id);
            if ($replacement_term !== 'not_set') {
                $category_specific_key[$specific_key_name] = [
                    'replacement_term' => $replacement_term, 
                    'key_set' => empty($replacement_term) ? 0 : 1
                ];
            } else {
                $suggest_replacement_term = $this->model_shopmanager_ai->getSpecificKey($specific_key_name, $category_specifics);
                $this->model_shopmanager_product_specific->addSpecificKey(
                    $specific_key_name, 
                    $category_id, 
                    $suggest_replacement_term ?? ''
                );
                $category_specific_key[$specific_key_name] = [
                    'replacement_term' => $suggest_replacement_term ?? '',
                    'key_set' => isset($suggest_replacement_term) ? 2 : 0
                ];
            }
        }
    
        $data['category_specific_key'] = $category_specific_key;*/
    
        // Temps total d'exécution
        unset($data['titles']);
        unset($data['category_name']);
        unset($data['conditions']);
       //nset($data['epid']);
        unset($data['algopix_search']);
        unset($data['algopix_search_fr']);
        unset($data['upc_tmp_search']);
        unset($data['google_search']);
        unset($data['ebay_search']);
        unset($data['ebay_specific_info']);
        $data['epid_specific_info']= $this->model_shopmanager_ebay->formatEpidDetailsToSpecifics($data['epid_details']['aspects']??[])??[];

        //print("<pre>".print_r ('2835 product_search',true )."</pre>");
        //print("<pre>".print_r ($data['images'],true )."</pre>");
        if (empty($product_info['image']) && isset($data['images']) && is_array($data['images']) && !empty($data['images'])) {
            $first_image_url = reset($data['images']);
            $data['similar_images'] = $this->model_shopmanager_ocr->getBestSimilarImage($first_image_url);
            $data['image_temp'] = null; // Initialisation
         //print("<pre>".print_r ($data['similar_images'],true )."</pre>");
            if (!empty($data['similar_images']) && is_array($data['similar_images'])) {
                foreach ($data['similar_images'] as $similar_images) {
                    $uploaded_image = $this->model_shopmanager_tools->uploadImages($similar_images['url'], null, 'pri');
        
                    if ($uploaded_image) { // Si l'upload réussit, on garde l'image et on stoppe la boucle
                        $data['image_temp'] = $uploaded_image;
                        break;
                    }
                }
            }
        
            // Si aucune image n'a été uploadée avec succès, afficher une erreur (optionnel)
            if (!$data['image_temp']) {
                //error_log("Aucune image n'a pu être uploadée avec succès.");
            } else {
                $data = $this->model_shopmanager_tools->transferTempImages($product_id, $data);
                $data['thumb'] = $this->model_tool_image->resize($data['image'], 100, 100);
            }
        } elseif(!empty($product_info['image'])) {
            // Si aucune image n'est trouvée, définir une image par défaut (optionnel)
            $data['image'] = $this->model_tool_image->resize($product_info['image'], 100, 100); // Image par défaut
            $data['thumb'] = $data['image'];
            $data['similar_images'] = [];
        } else {
            // Si aucune image n'est trouvée, définir une image par défaut (optionnel)
            $data['image'] = $this->model_tool_image->resize('no_image.png', 100, 100); // Image par défaut
            $data['thumb'] = $data['image'];
            $data['similar_images'] = [];
        }
        $data['execution_time'] = round(microtime(true) - $start_time, 2);
        
        // ⚡ CACHE: Sauvegarder en mémoire pour éviter requêtes multiples
        $cache_key = $product_id ?? $upc;
        self::$cache_search_data[$cache_key] = $data;
        
        return $data;
    }
 
   
public function manageInfoSources($upc = null, $source_value_items = null, $product_id = null) {
    // ⚡ CACHE: Éviter requêtes eBay/Algopix multiples
    $cache_key = $upc ?? $product_id;
    if ($cache_key && isset(self::$cache_info_sources[$cache_key])) {
        return self::$cache_info_sources[$cache_key];
    }
    
    $this->load->model('shopmanager/ebay');
    $this->load->model('shopmanager/ai');
    $this->load->model('shopmanager/algopix');
    $this->load->model('shopmanager/upctmp');
    $this->load->model('shopmanager/google');
    $this->load->model('shopmanager/tools');

    $threshold = 60 * 24 * 60 * 60 * 60; // 7 days in seconds
    $current_time = time();

    if ($upc) {
        $product_info = $this->getInfoSources($upc) ?? null;

        if ($product_info) {
           // $date_modified = strtotime($product_info['date_modified']);

          /*  if (($current_time - $date_modified) > $threshold  || $product_info['ebay_search'] == null) {
                $product_info = $this->refreshInfo($upc);
                $this->editInfoSources($product_info);
            }*/

          /*  if (!isset($product_info['epid_details']) && ($current_time - $date_modified) > $threshold) {
                $product_info = $this->refreshInfo($upc);
                $this->editInfoSources($product_info);
            }*/

         /*   if (strpos($product_info['upc_tmp_search'], 'error') !== false || !isset($product_info['upc_tmp_search'])) {
                $product_info['upc_tmp_search'] = null;
                $this->editInfoSources($product_info);
            }*/
            $product_info['upc_tmp_search'] = null;
            //print("<pre>".print_r ($product_info,true )."</pre>");
            $product_info['ebay_pricevariant'] = $product_info['ebay_pricevariant']?json_encode($this->model_shopmanager_ebay->calculateMissingPrices(json_decode($product_info['ebay_pricevariant'],true))):null;

        } else {
            $product_info = $this->refreshInfo($upc);
            $this->addInfoSources($product_info);
        } 
    } elseif ($source_value_items) {
        $product_info = $this->buildInfoFromSourceItems($source_value_items);
        if ($product_id) {
            $product_info['product_id'] = $product_id;
            $this->addInfoSources($product_info);
        }
    } elseif ($product_id) {
        $product_info = $this->getInfoSources($product_id) ?? null;
    } else {
        return null;
    }

    // ⚡ CACHE: Sauvegarder résultat en mémoire
    if ($cache_key) {
        self::$cache_info_sources[$cache_key] = $product_info;
    }

    return $product_info;
}

private function refreshInfo($upc) {


  
                                                 

    // Récupération des résultats avant intégration dans le tableau final
    
    $algopix_en_us = $this->model_shopmanager_algopix->get($upc, 'UPC', ['ENGLISH_US']);
    //print("<pre>" . print_r('3025 product_search', true) . "</pre>");
    //print("<pre>" . print_r($algopix_en_us, true) . "</pre>");
    $algopix_en_ca = $this->model_shopmanager_algopix->get($upc, 'UPC', ['ENGLISH_CA']);
    //print("<pre>" . print_r('3028 product_search', true) . "</pre>");
    //print("<pre>" . print_r($algopix_en_ca, true) . "</pre>");
    $google_search = $this->model_shopmanager_google->get($upc);
    //print("<pre>" . print_r('3031 product_search', true) . "</pre>");
    //print("<pre>" . print_r($google_search, true) . "</pre>");
    $product_name = null;
    //print("<pre>" . print_r($algopix_en_us, true) . "</pre>");
    if (isset($algopix_en_ca['commonAttributes']['title'])) {
        $product_name = $algopix_en_ca['commonAttributes']['title'];
    } elseif (isset($algopix_en_us['commonAttributes']['title'])) {
        $product_name = $algopix_en_us['commonAttributes']['title'];
    } elseif (isset($google_search['renaud_bray'])) {
        $product_name = $google_search['renaud_bray'][0]['name'];
    } elseif (!empty($google_search['amazon_ca'])) {
        $product_name = $google_search['amazon_ca'][0]['name'];
    } elseif (!empty($google_search['ebay_ca'])) {
        $product_name = $google_search['ebay_ca'][0]['name'];
    } elseif (!empty($google_search['ebay_com'])) {
        $product_name = $google_search['ebay_com'][0]['name'];
    }


    $ebay_search = $this->model_shopmanager_ebay->get($upc, null, null, null, 10, null, null, 1, $product_name);
    // Traitements intermédiaires pour clarifier les variables
    $ebay_items = null;
    if (isset($ebay_search['items'][0])) {
        $ebay_items = json_encode($ebay_search['items']);
    } elseif (isset($ebay_search['items'])) {
        $ebay_items = json_encode([0 => $ebay_search['items']]);
    }

    $ebay_pricevariant = isset($ebay_search['pricevariant']) ? json_encode($ebay_search['pricevariant']) : null;
    $ebay_category = isset($ebay_search['category']) ? json_encode($ebay_search['category']) : null;
    $ebay_specific_info = isset($ebay_search['specific_info']) ? json_encode($ebay_search['specific_info']) : null;

    $epid = null;
    if (isset($ebay_search['epid'][0]) && is_numeric($ebay_search['epid'][0])) {
        $epid = (int)$ebay_search['epid'][0];
    } elseif (isset($ebay_search['epid']) && is_numeric($ebay_search['epid'])) {
        $epid = (int)$ebay_search['epid'];
    }

    $epid_details = isset($ebay_search['epid_details']) ? json_encode($ebay_search['epid_details']) : null;

    // Tableau final
    $product_info = [
        'upc' => $upc,
        'ebay_search' => $ebay_items,
        'ebay_pricevariant' => $ebay_pricevariant,
        'ebay_category' => $ebay_category,
        'ebay_specific_info' => $ebay_specific_info,
        'algopix_search' => json_encode($algopix_en_us),
        'algopix_search_fr' => json_encode($algopix_en_ca),
        'upc_tmp_search' => null,
        'google_search' => json_encode($google_search),
        'epid' => $epid,
        'epid_details' => $epid_details,
    ];


    return $product_info;
}



private function buildInfoFromSourceItems($source_value_items) {
    $ebay_search = $this->model_shopmanager_ebay->buildFinalResult($source_value_items);
    //print("<pre>" . print_r(2339, true) . "</pre>");
    //print("<pre>" . print_r($source_value_items, true) . "</pre>");
    $product_info = [
        'ebay_search' => isset($ebay_search['items'][0]) ? json_encode($ebay_search['items']) : (isset($ebay_search['items']) ? json_encode([0 => $ebay_search['items']]) : null),
        'ebay_pricevariant' => isset($ebay_search['pricevariant']) ? json_encode($ebay_search['pricevariant']) : null,
        'ebay_category' => isset($ebay_search['category']) ? json_encode($ebay_search['category']) : null,
        'ebay_specific_info' => isset($ebay_search['specific_info']) ? json_encode($ebay_search['specific_info']) : null,
        'upc_tmp_search' => null,
        'algopix_search' => null,
        'algopix_search_fr' => null,
        'google_search' => null,
        'epid' => isset($ebay_search['epid'][0]) && is_numeric($ebay_search['epid'][0]) ? (int) $ebay_search['epid'][0] : (isset($ebay_search['epid']) && is_numeric($ebay_search['epid']) ? (int) $ebay_search['epid'] : null),
        'epid_details' => isset($ebay_search['epid_details']) ? json_encode($ebay_search['epid_details']) : null,
    ];

    return $product_info;
}
}