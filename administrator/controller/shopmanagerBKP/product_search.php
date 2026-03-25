<?php

//use Google\Service\TrafficDirectorService\NullMatch;
//use setasign\Fpdi\Fpdi;


class ControllerShopmanagerProductSearch extends Controller {

    
public function index() {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$start_time = microtime(true); 
	$execution_times = [];
	$n=0;
	$this->load->language('shopmanager/product_search');
	$this->document->addScript('view/javascript/shopmanager/product_search.js');
	$this->document->addScript('view/javascript/shopmanager/tools.js');
		$this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
		//$this->document->addScript('view/javascript/shopmanager/alert_popup.js');

//	$this->document->addScript('view/javascript/shopmanager/chrome_debug.js');

	
	$this->document->setTitle($this->language->get('heading_title'));

	
	// Charger le modèle
	 

	$this->load->model('shopmanager/product_search');
	$this->load->model('shopmanager/manufacturer');
	$this->load->model('shopmanager/tools');
	$this->load->model('shopmanager/ai');
	$this->load->model('shopmanager/product');
	$this->load->model('shopmanager/condition');
	$this->load->model('shopmanager/catalog/category');
	$this->load->model('shopmanager/product_specific');

	// Définir les variables de texte


	$data['heading_title'] = $this->language->get('heading_title');


	
	$data['entry_condition'] = $this->language->get('entry_condition');
	$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
	$data['entry_model'] = $this->language->get('entry_model');
	$data['entry_search'] = $this->language->get('entry_search');
	$data['entry_name'] = $this->language->get('entry_name');
	$data['entry_price'] = $this->language->get('entry_price');
	$data['entry_price_with_shipping'] = $this->language->get('entry_price_with_shipping');
	$data['entry_unallocated_quantity'] = $this->language->get('entry_unallocated_quantity');
	$data['entry_quantity'] = $this->language->get('entry_quantity');
	$data['entry_shipping_cost'] = $this->language->get('entry_shipping_cost');
	$data['entry_specifics'] = $this->language->get('entry_specifics');
	
	$data['button_search_by_upc'] = $this->language->get('button_search_by_upc');
	$data['button_save'] = $this->language->get('button_save');
	$data['button_feed'] = $this->language->get('button_feed');
	$data['button_cancel'] = $this->language->get('button_cancel');
	$data['button_add_specifics'] = $this->language->get('button_add_specifics');
	$data['button_search_by_name'] = $this->language->get('button_search_by_name');
	 

	$data['placeholder_search'] = $this->language->get('placeholder_search');
	$data['text_no_data'] = $this->language->get('text_no_data');

	// Variables pour la vue
	$data['text_search'] = $this->language->get('text_search');
	$data['text_name'] = $this->language->get('text_name');
	$data['text_brand'] = $this->language->get('text_brand');
	$data['text_model'] = $this->language->get('text_model');
	$data['text_upc'] = $this->language->get('text_upc');
	$data['entry_category'] = $this->language->get('entry_category');
	$data['text_description_supp'] = $this->language->get('text_description_supp');
	$data['entry_image'] = $this->language->get('entry_image');
	$data['entry_additional_image'] = $this->language->get('entry_additional_image');
	
	
	$data['entry_external_images'] = $this->language->get('entry_external_images');
	$data['text_image_alt'] = $this->language->get('text_image_alt');
	$data['entry_main_image'] = $this->language->get('entry_main_image');
	
	$data['text_no_images'] = $this->language->get('text_no_images');
	$data['text_prices'] = $this->language->get('text_prices');
	$data['text_lowest_price'] = $this->language->get('text_lowest_price');
	$data['text_highest_price'] = $this->language->get('text_highest_price');
	$data['text_offers'] = $this->language->get('text_offers');
	$data['text_merchant'] = $this->language->get('text_merchant');
	$data['text_price'] = $this->language->get('text_price');
	$data['text_condition'] = $this->language->get('text_condition');
	$data['text_shipping'] = $this->language->get('text_shipping');
	$data['text_availability'] = $this->language->get('text_availability');
	$data['text_url'] = $this->language->get('text_url');
	$data['text_url_sold'] = $this->language->get('text_url_sold');
	
	$data['text_view_offer'] = $this->language->get('text_view_offer');
	$data['text_no_offers'] = $this->language->get('text_no_offers');
	$data['text_select'] = $this->language->get('text_select');
	$data['text_select_name'] = $this->language->get('text_select_name');
	$data['text_select_image'] = $this->language->get('text_select_image');

	$data['text_offer_details'] = $this->language->get('text_offer_details');
	$data['text_get_source'] = $this->language->get('text_get_source');
	$data['text_google_search'] = $this->language->get('text_google_search');
	$data['text_ebay_search'] = $this->language->get('text_ebay_search');
	$data['text_source'] = $this->language->get('text_source');

	$data['text_price_with_shipping'] = $this->language->get('text_price_with_shipping');
	$data['text_original_retail_price_with_shipping'] = $this->language->get('text_original_retail_price_with_shipping');

	$data['entry_category_id'] = $this->language->get('entry_category_id');
	$data['text_percent'] = $this->language->get('text_percent');
	$data['text_condition_name'] = $this->language->get('text_condition_name');
	$data['text_condition_id'] = $this->language->get('text_condition_id');

	$data['text_color'] = $this->language->get('text_color');
	$data['text_size'] = $this->language->get('text_size');
	$data['text_dimension'] = $this->language->get('text_dimension');
	$data['text_weight'] = $this->language->get('text_weight');
	$data['text_category_name'] = $this->language->get('text_category_name');

	$data['text_identifier_type'] = $this->language->get('text_identifier_type');
	$data['text_identifier_value'] = $this->language->get('text_identifier_value');
	$data['text_dimension_type'] = $this->language->get('text_dimension_type');
	$data['text_dimension_value'] = $this->language->get('text_dimension_value');
	$data['text_image_type'] = $this->language->get('text_image_type');
	$data['text_image_url'] = $this->language->get('text_image_url');
	$data['entry_specific_type'] = $this->language->get('entry_specific_type');
	$data['entry_specific_value'] = $this->language->get('entry_specific_value');

	$data['entry_category_id'] = $this->language->get('entry_category_id');
	$data['entry_category'] = $this->language->get('entry_category');
	$data['help_category'] = $this->language->get('help_category');

	if (isset($this->error)){
		$errors = ['location', 'keyword', 'model', 'manufacturer_id', 'height', 'width', 'length', 'weight', 'warning', 'category','shipping_cost'];

		foreach ($errors as $error) {
			$data['error_' . $error] = isset($this->error[$error]) ? $this->error[$error] : '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['manufacturer_id'])) {
			$data['error_manufacturer_id'] = $this->error['manufacturer_id'];
		} else {
			$data['error_manufacturer_id'] = array();
		}

		if (isset($this->error['location'])) {
			$data['error_location'] = $this->error['location'];
		} else {
			$data['error_location'] = array();
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = array();
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = array();
		}

		if (isset($this->error['length'])) {
			$data['error_length'] = $this->error['length'];
		} else {
			$data['error_length'] = array();
		}

		if (isset($this->error['weight'])) {
			$data['error_weight'] = $this->error['weight'];
		} else {
			$data['error_weight'] = array();
		}

		if (isset($this->error['shipping_cost'])) {
			$data['error_shipping_cost'] = $this->error['shipping_cost'];
		} else {
			$data['error_shipping_cost'] = array();
		}

	
	}

	$data['token'] = $this->session->data['token'];

	$this->load->model('shopmanager/localisation/country');

	$data['countries'] = $this->model_shopmanager_localisation_country->getCountries();


	$url='';
	
	$data['breadcrumbs'] = array();

	$data['breadcrumbs'][] = array(
		'text' => $this->language->get('text_home'),
		'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
	);

	$data['breadcrumbs'][] = array(
		'text' => $this->language->get('heading_title'),
		'href' => $this->url->link('shopmanager/product_search', 'token=' . $this->session->data['token'] . $url, true)
	);
	$data['cancel'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true);
	 
	if (isset($this->error['warning'])) {
		$data['error_warning'] = $this->error['warning'];
	} 
	if (isset($this->error)){
		$errors = ['location', 'keyword', 'model', 'manufacturer_id', 'height', 'width', 'length', 'weight', 'warning', 'category','shipping_cost'];

		foreach ($errors as $error) {
			$data['error_' . $error] = isset($this->error[$error]) ? $this->error[$error] : '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}
	}


	// Définir l'action du formulaire de recherche
	$data['action'] = $this->url->link('shopmanager/product_search', 'token=' . $this->session->data['token'], true);

	// Définir l'action du formulaire de sauvegarde
	$data['save_action'] = $this->url->link('shopmanager/product_search/product_feed', 'token=' . $this->session->data['token'], true);

	$data['header'] = $this->load->controller('common/header');
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = '';//$this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = '';//$this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = '';//$this->load->controller('shopmanager/marketplace_popup');
	
	$data['ocr'] = $this->load->controller('shopmanager/ocr');
//print("<pre>".print_r ($this->request->get ,true )."</pre>"); 
// Vérifier si le formulaire a été soumis via POST
if (($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['upc'])) || isset($this->request->get['upc'])|| isset($this->request->get['product_id']))  {

		// 1. Récupération de l'UPC à partir de la requête POST ou GET
		$upc = $this->request->post['upc'] ?? $this->request->get['upc'] ?? null;

		// Si l'UPC n'est pas présent, essayer de l'extraire d'un SKU, si disponible
	/*	if (empty($upc) && isset($this->request->get['filter_sku'])) {
			$product_id = $this->model_shopmanager_product->getProductIDbySku($this->request->get['filter_sku']);
			$upc = $this->model_shopmanager_product->getUPCBySku($this->request->get['filter_sku']);
		}*/

		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		}

		if (isset($this->request->get['upc'])) {
			$upc = $this->request->get['upc'];
		}

		// 2. Vérifier si l'UPC est valide avant de continuer
		if (!$upc) {
			// Gestion des erreurs ici (par exemple, retour de message d'erreur ou fin de la fonction)
			return;
		}

		// 3. Initialiser le suivi du temps d'exécution
		$execution_times = [];
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		$n = 1; // Compteur pour le suivi du temps d'exécution

	
		
		if (isset($product_id)) {
		
			
		
			$product_info = $this->model_shopmanager_product->getProduct($product_id);
		
			
			$data = array_merge_recursive($data,$product_info);

		//print("<pre>" . print_r('3397:product.php', true) . "</pre>");
		//print("<pre>" . print_r($product_info, true) . "</pre>");
			$this->load->model('localisation/language');
		
			$data['languages'] = $this->model_localisation_language->getLanguages();
		
			
		
			
		
			
		//print("<pre>".print_r ($data['product_description'] ,true )."</pre>");
				
			$data['product_images'] = array();
		
			$categories=[];
			if (!empty($product_info)) {
				$data['product_id'] = $product_info['product_id'];
				$data['condition_id'] = $product_info['condition_id'];
				$data['image'] = $product_info['image'];
				
		
		
			// Images
			
			$product_images = $this->model_shopmanager_product->getProductImages($product_id);
			
		
		
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); //echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		
			foreach ($product_images as $product_image) {
				if (is_file(DIR_IMAGE . $product_image['image'])) {
					$image = $product_image['image'];
					$thumb = $product_image['image'];
				} else {
					$image = '';
					$thumb = 'no_image.png';
				}
		
				$data['product_images'][] = array(
					'image'      => $image,
					'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
					'sort_order' => $product_image['sort_order']
				);
			}
			
		
				
				$categories = $this->model_shopmanager_product->getProductCategories($product_id);

					$this->load->model('shopmanager/condition');
		
				if(isset($product_info['category_id'])){
							// Conditions
					//print("<pre>" . print_r(value: '360:PRODUCT_SEARCH.php') . "</pre>");
					$data['conditions']=$this->model_shopmanager_condition->getConditionDetails($product_info['category_id']);
					$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($product_info['category_id']);
					$category_leaf = $this->model_shopmanager_catalog_category->getCategoryLeaf($product_info['category_id']);
		
					if (!is_array($category_specific_info[1]['specifics'])) {
						
						if($category_leaf ==1){
						//print("<pre>".print_r ('getform:2631',true )."</pre>");
							$this->response->redirect($this->url->link('shopmanager/catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $product_info['category_id'] . '&product_id='.$product_info['product_id'] . '&upc='.$product_info['upc'], true));
						}else{
							$this->error['category']= $this->language->get('error_category_not_leaf');
						}
					}
				}
			
			} 
			// Image
			
			
		
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); //echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		// Categories
			
			
		
				
				
			//print("<pre>".print_r ($data['ebay_info'],true )."</pre>");
				$data['product_categories'] = array();
			
				foreach ($categories as $category_id) {
					$category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
					//print("<pre>".print_r ($category_info,true )."</pre>");
					if ($category_info) {
						$data['product_categories'][] = array(
							'category_id' => $category_info['category_id'],
							'name_category' => $category_info['name'],
							'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
							'leaf' => $category_info['leaf'],
						);
					/*	if( $category_info['leaf']=='1'){
							$category_specific=$category_info['category_id'];
						}*/
					}
				}
			
			//print("<pre>" . print_r($data['product_categories'], true) . "</pre>");
		}else {
				
			$data['product_id'] = null;
			$data['condition_id'] = '';
			$data['image'] = '';
			$image = '';
			$thumb = 'no_image.png';
			$data['product_images'] = array();
		}
	//print("<pre>" . print_r($data, true) . "</pre>");
		// 4. Récupérer ou rafraîchir les informations sur le produit en fonction de l'UPC 
		if(isset($upc) && is_numeric($upc)){
		$product_info_source = $this->model_shopmanager_product_search->manageProductInfoSources($upc);
		// 5. Récupérer les informations mises à jour de la table `product_info_sources`
		
		
		// Suivi du temps d'exécution après la gestion des informations de la source
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);$start_time = microtime(true);
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		// 6. Récupérer les informations depuis la table si disponibles, sinon définir à `null`
		$upc_tmp_search = isset($product_info_source['upc_tmp_search']) ? json_decode($product_info_source['upc_tmp_search'], true) : null;
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);$start_time = microtime(true);
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		$google_search = isset($product_info_source['google_search']) ? json_decode($product_info_source['google_search'], true) : null;
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);$start_time = microtime(true);
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		$algopix_search = isset($product_info_source['algopix_search']) ? json_decode($product_info_source['algopix_search'], true) : null;
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);$start_time = microtime(true);
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		$ebay_search = isset($product_info_source['ebay_search']) ? json_decode($product_info_source['ebay_search'], true) : null;

		$ebay_category = isset($product_info_source['ebay_category']) ? json_decode($product_info_source['ebay_category'], true) : null;
		$ebay_pricevariant = isset($product_info_source['ebay_pricevariant']) ? json_decode($product_info_source['ebay_pricevariant'], true) : null;
		$ebay_specific_info = isset($product_info_source['ebay_specific_info']) ? json_decode($product_info_source['ebay_specific_info'], true) : null;

		//print("<pre>" . print_r($ebay_specific_info, true) . "</pre>");
		//print("<pre>" . print_r('3526:product.php', true) . "</pre>");
     //print("<pre>" . print_r(json_decode($product_info_source['ebay_search'], true), true) . "</pre>");

	//print("<pre>" . print_r('3529:product.php', true) . "</pre>");
    //print("<pre>" . print_r($ebay_search, true) . "</pre>");
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		$epid= isset($product_info_source['epid']) ? json_decode($product_info_source['epid'], true) : null;
		$epid_details = isset($product_info_source['epid_details']) ? json_decode($product_info_source['epid_details'], true) : null;
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		if (isset($upc_tmp_search['category_name'])) { 
			$data['category_name'] = $upc_tmp_search['category_name']??null; 
		}

	//print("<pre>" . print_r($product_info_source, true) . "</pre>");
	//print("<pre>" . print_r($this->model_shopmanager_ebay->get($upc),true) . "</pre>");
														
	//print("<pre>" . print_r($this->model_shopmanager_ebay->findProductIDByGTIN($upc), true) . "</pre>");
		// 4. Récupérer ou rafraîchir les informations sur le produit en fonction de l'UPC
		
		//$this->model_shopmanager_ebay->getProductDetailsByepid('25046076135');
		// 7. Si l'API eBay n'a pas retourné de résultats et que nous avons un titre d'Algopix, tenter de récupérer via eBay à nouveau
	

		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time, 2);
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		// 8. Stocker les résultats dans `$data` pour affichage ou traitement ultérieur
		$data['upc_tmp_search'] = $upc_tmp_search;
		$data['google_search'] = $google_search;
		$data['algopix_search'] = $algopix_search;
		$data['ebay_search'] = $ebay_search;
		$data['ebay_category'] = $ebay_category;
		$data['ebay_pricevariant'] = $ebay_pricevariant;

		
		$data['epid_details'] = $epid_details;
		$data['epid'] = $epid;
		
	
		// Afficher les temps d'exécution pour le débogage
	//	print_r($execution_times);
	//print("<pre>" . print_r('Jo3560',true) . "</pre>");
//print("<pre>" . print_r($epid_details,true) . "</pre>");




// Récupération et traitement des images
//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 


//$data['images'] = !empty($google_search) ? $this->model_shopmanager_product_search->processUniqueImages($google_search) : ['error' => 'No images found from the specified sites'];

// Ajout des informations Algopix et eBay




		if (isset($upc_tmp_search['error']) && isset($algopix_search['commonAttributes'] ['title'])) { 
			$upc_tmp_search = $this->model_shopmanager_upctmp->search($algopix_search['commonAttributes'] ['title']);
		}
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		//print("<pre>" . print_r($upc_tmp_search, true) . "</pre>");
		//print("<pre>" . print_r($algopix_search, true) . "</pre>");

		if(isset($upc_tmp_search['error']) && isset($algopix_search['error']) && isset($product_id)){
			$this->response->redirect($this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product_id . $url, true));
		}

		if (!empty($algopix_search['dimensions']['packageDimensions'])) { 
			$data['package_dimensions'] = $algopix_search['dimensions']['packageDimensions'];		
		}

		if (!empty($algopix_search['identifiers'])) { 
			$data['identifiers'] = $algopix_search['identifiers'];		
		}else{
			$data['identifiers'] =[];
		}



		// Gestion des conditions en fonction de la catégorie eBay
		//print("<pre>" . print_r($product_info['category_id'], true) . "</pre>");
		$category_id = $epid_details['primaryCategoryId'] ?? ($ebay_category[0]['category_id'] ?? $product_info['category_id'] ?? null);
			//print("<pre>" . print_r($ebay_category, true) . "</pre>");
		$data['category_id']  = $category_id;
		$data['category_name'] = $ebay_category[0]['category_name'] ?? $data['category_name'];
		
		if (!isset($category_id) && !isset($data['category_name']) && isset($algopix_search['channelSpecificAttributes'] ['productType'])){
			$category_name = str_replace('_', ' ',$algopix_search['channelSpecificAttributes'] ['productType']);
			$category_info = $this->model_shopmanager_ai->getCategoryID($category_name);
			if(isset($category_info)){
				$category_id=trim($category_info['category_id'])??null;
				$data['category_id']  = $category_id;
				$data['category_name'] = $category_info['category_name'] ?? null;
				$data['ebay_category'][0]=$category_info;
				$data['ebay_category'][0]['percent']=100;
			}
			
		}
		// 
		if(empty($data['product_categories']) && isset($category_id) ){
			
				$category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
				//print("<pre>".print_r ($category_info,true )."</pre>");
				if ($category_info) {
					$data['product_categories'][] = array(
						'category_id' => $category_info['category_id'],
						'name_category' => $category_info['name'],
						'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
						'leaf' => $category_info['leaf'],
					);
				/*	if( $category_info['leaf']=='1'){
						$category_specific=$category_info['category_id'];
					}*/
				}
			
		}
			//print("<pre>" . print_r(value: '574:PRODUCTSEARCH.php') . "</pre>");
			$conditions = $category_id 
				? $this->model_shopmanager_condition->getConditionDetails($category_id) 
				: [];

			$data['conditions'] = $conditions[1] ?? [];

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['images']  = $this->model_shopmanager_product_search->getAllImageUrls($upc_tmp_search??null, $google_search??null, $ebay_search??null, $algopix_search??null,$algopix_search_fr??null,$epid_details??null);
		//print("<pre>" . print_r('3638:product.php', true) . "</pre>");
		//print("<pre>" . print_r($data['images'], true) . "</pre>");
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['titles']  = $this->model_shopmanager_product_search->getAllTitles($upc_tmp_search??null, $google_search??null, $ebay_search??null, $algopix_search??null,$algopix_search_fr??null,$epid_details??null);

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['manufacturers'] = $this->model_shopmanager_product_search->getAllManufacturers($data);
				//print("<pre>" . print_r($data['manufacturers'], true) . "</pre>");



		}
		//print("<pre>" . print_r('3650:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
			if(isset($data['manufacturers'])){
				if (is_string($data['manufacturers'])) {
					$data['manufacturers'] = [$data['manufacturers']];
				}
				if(count($data['manufacturers'])==1 && is_array($data['manufacturers'][0])){
					$findmanufacturer = $this->model_shopmanager_manufacturer->getManufacturerByName(reset($data['manufacturers']));
				//print("<pre>" . print_r('3669:product.php', true) . "</pre>");
				//print("<pre>" . print_r($findmanufacturer, true) . "</pre>");
					//$findmanufacturer = $this->model_shopmanager_manufacturer->getManufacturers($manufacturer_data);
					if(!isset($findmanufacturer)){
						$data_value = [
							'name' => reset($data['manufacturers']),  // Nom du fabricant
							'sort_order' => 1,              // Ordre de tri du fabricant
							'image' => '', // Chemin vers l'image du fabricant
							'manufacturer_store' => [0], // Liste des ID de magasin où le fabricant est affiché
							'keyword' => reset($data['manufacturers']) // Mot-clé SEO pour le fabricant
						];
	
						// Résultat final
						$manufacturer_result ['manufacturer_id'] = $this->model_shopmanager_manufacturer->addManufacturer($data_value);
						$manufacturer_result ['name'] = reset($data['manufacturers']);
			 //print("<pre>" . print_r('916:ai.php', true) . "</pre>");
		   //print("<pre>" . print_r($data_value, true) . "</pre>");
			//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");
	
				//print("<pre>" . print_r($product_info['manufacturer_id'], true) . "</pre>");
				//print("<pre>" . print_r($manufacturers, true) . "</pre>");
						// Vous pouvez maintenant utiliser $result pour vos besoins
					}else{
						$manufacturer_result ['manufacturer_id'] =  $findmanufacturer['manufacturer_id'];
						$manufacturer_result ['name'] = $findmanufacturer['name'];
					}
		//			//print("<pre>" . print_r('3650:product.php', true) . "</pre>");
		//			//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");
				//	$manufacturer_result = $manufacturer_result[0];
				}else{
					
					$manufacturer_result=$this->model_shopmanager_ai->getManufacturer($data['manufacturers']);
		//			//print("<pre>" . print_r('3715:product.php', true) . "</pre>");
			//		//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");
		//			//print("<pre>" . print_r($data['manufacturers'], true) . "</pre>");
				}
				$data['manufacturer_id']=(isset($data['manufacturer_id']) && $data['manufacturer_id']>0)?$data['manufacturer_id']:$manufacturer_result['manufacturer_id']??0;
				$data['manufacturer']=$manufacturer_result['name']??'';
				$data['brand']=$manufacturer_result['name']??'';
				$data['product_info']['manufacturer']=$manufacturer_result['name']??'';
			}else{
			//print("<pre>" . print_r('3723:product.php', true) . "</pre>");
			//print("<pre>" . print_r($data, true) . "</pre>");
				$data['manufacturer_id']=$data['manufacturer_id']??0;
				$data['manufacturer']=$data['brand']??'';
				$data['brand']=$data['brand']??'';
			//print("<pre>" . print_r('3655:product.php', true) . "</pre>");
			//print("<pre>" . print_r($data, true) . "</pre>");
			}
		
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		//$product_description = $this->model_shopmanager_product->getProductDescriptions($product_id);

		// Vérifie si le nom en français (language_id = 1) est vide ou trop court
		$product_description = isset($product_id)?$this->model_shopmanager_product->getProductDescriptions($product_id):null;

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
			$data['short_title'] = $title_result['short_title'] ?? null;
		}else{
			$data['title'] =  $product_description[1]['name'];
			$data['name_description'] = $product_description[1]['name'];
			$data['short_title'] =  null;

		}
			$data['upc']= $data['upc']??$this->request->get['upc']??'';
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		//print("<pre>".print_r ('3719:product.php' ,true )."</pre>");
	//		//print("<pre>".print_r ($data ,true )."</pre>");
			if (isset($data['marketplace_item_id']) && ($data['marketplace_item_id'] > 0) && $category_leaf == 1) {
				$ebay_specific_info = $this->model_shopmanager_ebay->getProductSpecificsOLD($data['marketplace_item_id']);
				//print("<pre>" . print_r(('3687:product.php'), true) . "</pre>");
				//print("<pre>" . print_r(($ebay_specific_info), true) . "</pre>"); 
				//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
				 
				//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
	
			}/*else{
				
	//	//print("<pre>" . print_r(('3687:product.php'), true) . "</pre>");
	//	//print("<pre>" . print_r(($ebay_search), true) . "</pre>"); 
					$ebay_search[] = $ebay_search[0] ?? null;
					//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
					//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
					$ebay_search = array_slice($ebay_search, 0, 10);
					//print("<pre>" . print_r(($ebay_search), true) . "</pre>"); 
					//echo '<br>Nombre de item pour ebay: ' . count($ebay_search);
					
					if (!is_null($ebay_search[0])) {
						$ebay_specific_info = $this->model_shopmanager_ebay->getProductSpecificsSellers($ebay_search, $data['category_id']);
					} else {
						$ebay_specific_info = null; // Ou toute autre logique si nécessaire
					}
					//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
					//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		
			}*/
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			
			// Si les deux sont null, $mergeArrayForSpecifics reste un tableau vide
			$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($category_id,1);

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

	//		//print("<pre>" . print_r(('3699:product.php'), true) . "</pre>");
	//		//print("<pre>" . print_r(($category_specific_info), true) . "</pre>");
			$mergeArrayForSpecifics = array();
			if(isset($epid_details)){
				//print("<pre>" . print_r(('3808:product.php'), true) . "</pre>");
			//print("<pre>" . print_r(($epid_details), true) . "</pre>");
			
					$ebay_sources = isset($epid_details['aspects'])?$this->model_shopmanager_ebay->formatEpidDetails($epid_details['aspects'],$category_specific_info):[];;
					$data['epid_sources_json'] = json_encode($ebay_sources)??'';
					$mergeArrayForSpecifics = $ebay_sources;
					//$mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources($ebay_sources,$mergeArrayForSpecifics);
				//	//print("<pre>" . print_r(value: 663) . "</pre>");
				//	//print("<pre>" . print_r($ebay_sources, true) . "</pre>");
				}else{
					$data['epid_sources_json'] = '';
				}
			
			//	//print("<pre>" . print_r($ebay_specific_info, true) . "</pre>");
			// Si les deux variables ne sont pas null, faire array_merge_recursive
			if (!is_null($ebay_specific_info)) {
				$mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources($this->model_shopmanager_ebay->formatActualDetails($ebay_specific_info),$mergeArrayForSpecifics);
			//	//print("<pre>" . print_r(value: 634, return: true) . "</pre>");
			//	//print("<pre>" . print_r($mergeArrayForSpecifics, true) . "</pre>");
			}elseif (!is_null($upc_tmp_search) && !is_null($algopix_search)) {
				$mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources(array_merge_recursive($upc_tmp_search, $algopix_search),$mergeArrayForSpecifics);
			//	//print("<pre>" . print_r(value: 638) . "</pre>");
			//	//print("<pre>" . print_r($mergeArrayForSpecifics, true) . "</pre>");
			}
			// Si seulement $upc_tmp_search n'est pas null, l'utiliser comme le tableau fusionné 
			elseif (!is_null($upc_tmp_search)) {
				$mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources($upc_tmp_search,$mergeArrayForSpecifics);
			//	//print("<pre>" . print_r(value: 640) . "</pre>");
			//	//print("<pre>" . print_r($upc_tmp_search, true) . "</pre>");
			}
			// Si seulement $algopix_search n'est pas null, l'utiliser comme le tableau fusionné
			elseif (!is_null($algopix_search)) {
				$mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources($algopix_search,$mergeArrayForSpecifics);
			//	//print("<pre>" . print_r(value: 646) . "</pre>");
			//	//print("<pre>" . print_r($algopix_search, true) . "</pre>");
				
			}
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			
		//print("<pre>" . print_r(('3716:product.php'), true) . "</pre>");
		//print("<pre>" . print_r(($mergeArrayForSpecifics), true) . "</pre>");

			$mergeArrayForSpecifics['commonAttributes']['short_title']=$data['short_title']??$data['title'];
			$mergeArrayForSpecifics['commonAttributes']['title']=$data['title']??'';
			$mergeArrayForSpecifics['commonAttributes']['manufacturer']=$data['manufacturer']??$upc_tmp_search['brand'];
			$mergeArrayForSpecifics['commonAttributes']['brand']=$data['manufacturer']??$upc_tmp_search['brand'];
			//$mergeArrayForSpecifics['upc']=$data['upc']??$upc_tmp_search['upc'];

			$mergeArrayForSpecifics['category_name'] = $data['category_name'];
			$mergeArrayForSpecifics['category_id']= $data['category_id'];
			$mergeArrayForSpecificsResult = $this->model_shopmanager_product_search->filterArrayForSpecifics($mergeArrayForSpecifics);
		
			unset($mergeArrayForSpecificsResult['error']);
			unset($mergeArrayForSpecificsResult['category_name']);
			unset($mergeArrayForSpecificsResult['category_id']);
			unset($mergeArrayForSpecificsResult['objectType']);
			unset($mergeArrayForSpecificsResult['itemClassification']);
			unset($mergeArrayForSpecificsResult['tradeInEligible']);

		//print("<pre>" . print_r(('3728:product.php'), true) . "</pre>");
		//print("<pre>" . print_r(($mergeArrayForSpecificsResult), true) . "</pre>");

			$data['specifics_result'] = 	$mergeArrayForSpecificsResult;

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			
			//print("<pre>" . print_r($category_id, true) . "</pre>");
			
			$category_specifics=$category_specific_info[1]['specifics']??[];
			$category_specific_key = [];
			$category_specific_names = [];

			foreach($category_specifics as $key => $specific) {
				$value = stripslashes($key);
				$category_specific_names[] = $value;
			}

			// Trier $category_specific_names par ordre alphabétique
			//sort($category_specific_names);

			$data['category_specific_names'] = $category_specific_names;

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			// Parcourir les clés de $mergeArrayForSpecificsResult
			foreach ($mergeArrayForSpecificsResult as $specific_key_name => $value) {
				//print("<pre>" . print_r($specific_key_name, true) . "</pre>");
				// Vérifier si la clé existe dans la base de données
				$replacement_term = $this->model_shopmanager_product_specific->getSpecificKey($specific_key_name, $category_id);

			//	//print("<pre>" . print_r($replacement_term, true) . "</pre>");
			//	//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); //echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
				// Si la clé existe déjà dans la base de données
				if ($replacement_term != 'not_set') {
					if ($replacement_term =='') {
						$key_set=0;
					}else{
						$key_set=1;
					}
				//	//print("<pre>" . print_r($key_set, true) . "</pre>"); 
					$category_specific_key[$specific_key_name] = [
					
						'replacement_term' => $replacement_term,     // Pas de suggestion nécessaire, donc on garde la clé originale
						'key_set' => $key_set                             // 0 car la clé existe déjà dans la base
					];
				} else {
					
					if ($replacement_term == 'not_set') {
					// Si la clé n'existe pas dans la base, obtenir un terme suggéré via la fonction getSpecificsKey()
						$suggest_replacement_term = $this->model_shopmanager_ai->getSpecificKey($specific_key_name, $category_specifics);
					//	//print("<pre>" . print_r($suggest_replacement_term, true) . "</pre>");
						if(isset($suggest_replacement_term)){
							$this->model_shopmanager_product_specific->addSpecificKey($specific_key_name, $category_id, $suggest_replacement_term);
							unset($category_specifics[$suggest_replacement_term]);
							$key_set=2;
						}else{
							$this->model_shopmanager_product_specific->addSpecificKey($specific_key_name, $category_id, '');
							$key_set=0;
						}
					}else{
						$suggest_replacement_term='';
						$key_set=0;
					}
					// Ajouter l'entrée au tableau $category_specific_key
					$category_specific_key[$specific_key_name] = [
						
						'replacement_term' => $suggest_replacement_term??'',      // Terme suggéré
						'key_set' => $key_set                             // 1 car la clé doit être ajoutée avec le terme suggéré
					];
					
				}
			}

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['category_specific_key']= $category_specific_key;
		//print("<pre>" . print_r('3800:product.php', true) . "</pre>");
	//	//print("<pre>" . print_r($data['identifiers'], true) . "</pre>");
			//print("<pre>" . print_r($data, true) . "</pre>");
			//print("<pre>".print_r ($execution_times ,true )."</pre>");
			$total_execution_time = array_sum($execution_times);

			
		if(isset($this->request->get['type'])){
			$json['html']= $this->load->view('shopmanager/product_search_'.$this->request->get['type'], $data);;
			$json['success'] = true;
			$json['message'] = "";
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));

		}else{
			//echo "<br><br>Temps total d'exécution : " . $total_execution_time . " secondes\n";
			$this->response->setOutput($this->load->view('shopmanager/product_search_info', $data));
		}	
		//print("<pre>" . print_r('894:product.php', true) . "</pre>");
		//print("<pre>" . print_r($data, true) . "</pre>");
		  
	}else{ 
	//	$data['data_result']=$data;
	//print("<pre>" . print_r('894:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
		$this->response->setOutput($this->load->view('shopmanager/product_search', $data));
	}  
}

    protected function getForm() {

		$this->load->model('shopmanager/tools');
		$this->load->model('shopmanager/product_search'); 
		$this->document->addScript('view/javascript/shopmanager/ebay.js');
        $this->document->addScript('view/javascript/shopmanager/product_form.js');
        $this->document->addScript('view/javascript/shopmanager/ai.js');
		$this->document->addScript('view/javascript/shopmanager/translate.js');
		$this->document->addScript('view/javascript/shopmanager/ocr.js');
		$this->document->addScript('view/javascript/shopmanager/tools.js');
		$this->document->addScript('view/javascript/shopmanager/marketplace_error_popup.js');
		//$this->document->addScript('view/javascript/shopmanager/alert_popup.js');

	//	$this->document->addScript('view/javascript/fontawesome/css/fontawesome.css');

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_plus'] = $this->language->get('text_plus');
		$data['text_minus'] = $this->language->get('text_minus');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_option'] = $this->language->get('text_option');
		$data['text_option_value'] = $this->language->get('text_option_value');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_percent'] = $this->language->get('text_percent');
		$data['text_amount'] = $this->language->get('text_amount');
		$data['entry_category_id'] = $this->language->get('entry_category_id');
        $data['text_percent'] = $this->language->get('text_percent');
        $data['text_condition_name'] = $this->language->get('text_condition_name');
        $data['text_condition_id'] = $this->language->get('text_condition_id');
		$data['text_no_data'] = $this->language->get('text_no_data');
		$data['text_price'] = $this->language->get('text_price');
		$data['text_url'] = $this->language->get('text_url');
		$data['text_url_sold'] = $this->language->get('text_url_sold');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_meta_title'] = $this->language->get('entry_meta_title');
		$data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$data['entry_keyword'] = $this->language->get('entry_keyword');
		$data['entry_category_id'] = $this->language->get('entry_category_id');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_sku'] = $this->language->get('entry_sku');
		$data['entry_upc'] = $this->language->get('entry_upc');
		$data['entry_ean'] = $this->language->get('entry_ean');
		$data['entry_jan'] = $this->language->get('entry_jan');
		$data['entry_isbn'] = $this->language->get('entry_isbn');
		$data['entry_mpn'] = $this->language->get('entry_mpn');
		$data['entry_location'] = $this->language->get('entry_location');
		$data['entry_minimum'] = $this->language->get('entry_minimum');
		$data['entry_shipping'] = $this->language->get('entry_shipping');
		$data['entry_date_available'] = $this->language->get('entry_date_available');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_price_with_shipping'] = $this->language->get('entry_price_with_shipping');
		$data['entry_unallocated_quantity'] = $this->language->get('entry_unallocated_quantity');
		$data['entry_quantity'] = $this->language->get('entry_quantity');

		$data['entry_marketplace_account_id'] = $this->language->get('entry_marketplace_account_id');
		$data['entry_color'] = $this->language->get('entry_color');
		$data['entry_condition'] = $this->language->get('entry_condition');
		$data['entry_description_supp'] = $this->language->get('entry_description_supp');
		$data['entry_condition_supp'] = $this->language->get('entry_condition_supp');
		$data['entry_included_accessories'] = $this->language->get('entry_included_accessories');
		$data['entry_shipping_cost'] = $this->language->get('entry_shipping_cost');
       
        $data['entry_location'] = $this->language->get('entry_location');
		$data['entry_stock_status'] = $this->language->get('entry_stock_status');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_price_with_shipping'] = $this->language->get('entry_price_with_shipping');
		$data['entry_unallocated_quantity'] = $this->language->get('entry_unallocated_quantity');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_points'] = $this->language->get('entry_points');
		$data['entry_option_points'] = $this->language->get('entry_option_points');
		$data['entry_subtract'] = $this->language->get('entry_subtract');
		$data['entry_weight_class'] = $this->language->get('entry_weight_class');
		$data['entry_weight'] = $this->language->get('entry_weight');
		$data['entry_weight_oz'] = $this->language->get('entry_weight_oz');
		$data['entry_dimension'] = $this->language->get('entry_dimension');
		$data['entry_length_class'] = $this->language->get('entry_length_class');
		$data['entry_length'] = $this->language->get('entry_length');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_additional_image'] = $this->language->get('entry_additional_image');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$data['entry_download'] = $this->language->get('entry_download');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_filter'] = $this->language->get('entry_filter');
		$data['entry_related'] = $this->language->get('entry_related');
		$data['entry_attribute'] = $this->language->get('entry_attribute');
		$data['entry_text'] = $this->language->get('entry_text');
		$data['entry_option'] = $this->language->get('entry_option');
		$data['entry_option_value'] = $this->language->get('entry_option_value');
		$data['entry_required'] = $this->language->get('entry_required');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_date_start'] = $this->language->get('entry_date_start');
		$data['entry_date_end'] = $this->language->get('entry_date_end');
		$data['entry_priority'] = $this->language->get('entry_priority');
		$data['entry_tag'] = $this->language->get('entry_tag');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['entry_reward'] = $this->language->get('entry_reward');
		$data['entry_layout'] = $this->language->get('entry_layout');
		$data['entry_recurring'] = $this->language->get('entry_recurring');
		$data['entry_ebay_search'] = $this->language->get('entry_ebay_search');
		$data['column_specifics'] = $this->language->get('column_specifics');
		$data['column_found_value'] = $this->language->get('column_found_value');
		$data['column_actual_value'] = $this->language->get('column_actual_value');

		$data['text_upload_images'] = $this->language->get('text_upload_images');
        $data['entry_image_principal'] = $this->language->get('entry_image_principal');
        $data['entry_additional_images'] = $this->language->get('entry_additional_images');
        $data['text_drop_here'] = $this->language->get('text_drop_here');
        $data['text_drop_additional_here'] = $this->language->get('text_drop_additional_here');
        $data['button_image_upload'] = $this->language->get('button_image_upload');
        $data['error_upload'] = $this->language->get('error_upload');
		$data['entry_sourcecode'] = $this->language->get('entry_sourcecode');
        $data['placeholder_sourcecode'] = $this->language->get('placeholder_sourcecode');
		$data['text_image_upload'] = $this->language->get('text_image_upload');
        $data['text_recognized_text'] = $this->language->get('text_recognized_text');
        $data['text_drag_drop'] = $this->language->get('text_drag_drop');
	

		


		$data['help_keyword'] = $this->language->get('help_keyword');
		$data['help_sku'] = $this->language->get('help_sku');
		$data['help_upc'] = $this->language->get('help_upc');
		$data['help_ean'] = $this->language->get('help_ean');
		$data['help_jan'] = $this->language->get('help_jan');
		$data['help_isbn'] = $this->language->get('help_isbn');
		$data['help_mpn'] = $this->language->get('help_mpn');
		$data['help_minimum'] = $this->language->get('help_minimum');
		$data['help_manufacturer'] = $this->language->get('help_manufacturer');
		$data['help_stock_status'] = $this->language->get('help_stock_status');
		$data['help_points'] = $this->language->get('help_points');
		$data['help_category'] = $this->language->get('help_category');
		$data['help_filter'] = $this->language->get('help_filter');
		$data['help_download'] = $this->language->get('help_download');
		$data['help_related'] = $this->language->get('help_related');
		$data['help_tag'] = $this->language->get('help_tag');

	
		$data['button_update_marketplace'] = $this->language->get('button_update_marketplace');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_attribute_add'] = $this->language->get('button_attribute_add');
		$data['button_option_add'] = $this->language->get('button_option_add');
		$data['button_option_value_add'] = $this->language->get('button_option_value_add');
		$data['button_discount_add'] = $this->language->get('button_discount_add');
		$data['button_special_add'] = $this->language->get('button_special_add');
		$data['button_image_add'] = $this->language->get('button_image_add');
		$data['button_remove'] = $this->language->get('button_remove');
		$data['button_recurring_add'] = $this->language->get('button_recurring_add');
		$data['button_check_all'] = $this->language->get('button_check_all');
		$data['button_get_specifics'] = $this->language->get('button_get_specifics');
		$data['button_add_specifics'] = $this->language->get('button_add_specifics');

		$data['button_remove_from_marketplace'] = $this->language->get('button_remove_from_marketplace');
		$data['button_list_on_marketplace'] = $this->language->get('button_list_on_marketplace');
		$data['button_relist_on_marketplace'] = $this->language->get('button_relist_on_marketplace');
		$data['button_ai_description_supp'] = $this->language->get('button_ai_description_supp');
		$data['button_ai_suggest_entry_name'] = $this->language->get('button_ai_suggest_entry_name');
		$data['button_regenerate_specifics'] = $this->language->get('button_regenerate_specifics');
		


		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');
		$data['tab_attribute'] = $this->language->get('tab_attribute');
		$data['tab_option'] = $this->language->get('tab_option');
		$data['tab_recurring'] = $this->language->get('tab_recurring');
		$data['tab_discount'] = $this->language->get('tab_discount');
		$data['tab_special'] = $this->language->get('tab_special');
		$data['tab_image'] = $this->language->get('tab_image');
		$data['tab_links'] = $this->language->get('tab_links');
		$data['tab_reward'] = $this->language->get('tab_reward');
		$data['tab_design'] = $this->language->get('tab_design');
		$data['tab_openbay'] = $this->language->get('tab_openbay');
		$data['tab_specifics'] = $this->language->get('tab_specifics');

		$data['column_action'] = $this->language->get('column_action');


	


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error)){
			$errors = ['location', 'keyword', 'model', 'manufacturer_id', 'height', 'width', 'length', 'weight', 'warning', 'category','shipping_cost'];

			foreach ($errors as $error) {
				$data['error_' . $error] = isset($this->error[$error]) ? $this->error[$error] : '';
			}

			if (isset($this->error['name'])) {
				$data['error_name'] = $this->error['name'];
			} else {
				$data['error_name'] = array();
			}
	
			if (isset($this->error['meta_title'])) {
				$data['error_meta_title'] = $this->error['meta_title'];
			} else {
				$data['error_meta_title'] = array();
			}
		}
		

	

	

		$url = '';

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace_account'])) {
			$url .= '&filter_marketplace_account=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

        if (isset($this->request->get['filter_unallocated_quantity'])) {
			$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
		}

        if (isset($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . $this->request->get['filter_location'];
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_specifics'])) {
			$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])){
			$url .= '&limit=' . $this->request->get['limit'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true)
		);

		if (!isset($this->request->get['product_id'])) {
			$data['action'] = $this->url->link('shopmanager/product/add', 'token=' . $this->session->data['token'] . $url, true);
		
		} else {
			$data['action'] = $this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);
			$data['upload_images_action'] = $this->url->link('shopmanager/tools/uploadImagesFiles', 'token=' . $this->session->data['token'] . '&product_id=' . $this->request->get['product_id'] . $url, true);

		}


		$data['cancel'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] . $url, true);

		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_shopmanager_product->getProduct($this->request->get['product_id']);
	//print("<pre>".print_r ('1531 product' ,true )."</pre>");
	//print("<pre>".print_r ($product_info,true )."</pre>");
		}
	//print("<pre>".print_r ('1534 product' ,true )."</pre>");
	//print("<pre>".print_r ($this->request->post,true )."</pre>");
		$data['token'] = $this->session->data['token'];
		

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_shopmanager_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}
	//	//print("<pre>".print_r ($data['product_description'] ,true )."</pre>");
	

		if (isset($this->request->post['product_id'])) {
			$data['product_id'] = $this->request->post['product_id'];
		} elseif (!empty($product_info)) {
			$data['product_id'] = $product_info['product_id'];
		} else {
			$data['product_id'] = null;
		}

		if (isset($this->request->post['category_id'])) {
			$data['category_id'] = $this->request->post['category_id'];
		} elseif (!empty($product_info)) {
			$data['category_id'] = $product_info['category_id'];
		} else {
			$data['category_id'] = null;
		}

		if (isset($this->request->post['condition_id'])) {
			$data['condition_id'] = $this->request->post['condition_id'];
		} elseif (!empty($product_info)) {
			$data['condition_id'] = $product_info['condition_id'];
		} else {
			$data['condition_id'] = null;
		}


		if (isset($this->request->post['marketplace_item_id'])) {
			$data['marketplace_item_id'] = $this->request->post['marketplace_item_id'];
		} elseif (!empty($product_info)) {
			$data['marketplace_item_id'] = $product_info['marketplace_item_id'];
		} else {
			$data['marketplace_item_id'] = NULL;
		}

		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = 'n/a';
		}

		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($product_info)) {
			$data['sku'] = $product_info['sku'];
		} else {
			$data['sku'] = '';
		}

		if (isset($this->request->post['upc'])) {
			$data['upc'] = $this->request->post['upc'];
		} elseif (!empty($product_info)) {
			$data['upc'] = $product_info['upc'];
		} else {
			$data['upc'] = '';
		}

		if (isset($this->request->post['ean'])) {
			$data['ean'] = $this->request->post['ean'];
		} elseif (!empty($product_info)) {
			$data['ean'] = $product_info['ean'];
		} else {
			$data['ean'] = '';
		}

		if (isset($this->request->post['jan'])) {
			$data['jan'] = $this->request->post['jan'];
		} elseif (!empty($product_info)) {
			$data['jan'] = $product_info['jan'];
		} else {
			$data['jan'] = '';
		}

		if (isset($this->request->post['isbn'])) {
			$data['isbn'] = $this->request->post['isbn'];
		} elseif (!empty($product_info)) {
			$data['isbn'] = $product_info['isbn'];
		} else {
			$data['isbn'] = '';
		}

		if (isset($this->request->post['mpn'])) {
			$data['mpn'] = $this->request->post['mpn'];
		} elseif (!empty($product_info)) {
			$data['mpn'] = $product_info['mpn'];
		} else {
			$data['mpn'] = '';
		}

		if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}
	
		

		$this->load->model('setting/store');

		$data['stores'] = $this->model_setting_store->getStores();
  
		if (isset($this->request->post['product_store'])) {
			$data['product_store'] = $this->request->post['product_store'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_store'] = $this->model_shopmanager_product->getProductStores($this->request->get['product_id']);
		} else {
			$data['product_store'] = array(0);
		}

		/*if (isset($this->request->post['keyword'])) {
			$data['keyword'] = $this->request->post['keyword'];
		} elseif (!empty($product_info['keyword'])) {
			$data['keyword'] = $product_info['keyword'];
		} else {
			$data['keyword'] = '';
		}*/
   
		if (isset($this->request->post['shipping'])) {
			$data['shipping'] = $this->request->post['shipping'];
		} elseif (!empty($product_info['shipping'])) {
			$data['shipping'] = $product_info['shipping'];
		} else {
			$data['shipping'] = 1;
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = round($this->request->post['price'],2);
		} elseif (!empty($product_info)) {
			$data['price'] = round($product_info['price'],2);
		} else {
			$data['price'] = 0;
		}
		if (isset($this->request->post['price_with_shipping'])) {
			$data['price_with_shipping'] = round($this->request->post['price_with_shipping'],2);
		} elseif (!empty($product_info)) {
			$data['price_with_shipping'] = round($product_info['price_with_shipping'],2);
		} else {
			$data['price_with_shipping'] = 0;
		}

		if (isset($this->request->post['shipping_cost'])) {
			$data['shipping_cost'] = round($this->request->post['shipping_cost'],2);
		} elseif (!empty($product_info)) {
			//print("<pre>" . print_r(1387, true) . "</pre>");
			//print("<pre>" . print_r($product_info['shipping_cost'], true) . "</pre>");
			if($product_info['shipping_cost']==0 || !isset($product_info['shipping_cost'])){
				$this->load->model('shopmanager/shipping');
				$result=$this->model_shopmanager_shipping->calculateShippingRates($product_info);
				$data['shipping_cost']=$result['shipping_cost'];
				$data['shipping_carrier']=$result['shipping_carrier'];
				$data['price_with_shipping']=$data['price']+$result['shipping_cost'];

			}else{
				$data['shipping_cost'] = round($product_info['shipping_cost'],2);
			}
		} else {
			$data['shipping_cost'] = 0;
		}

		if (isset($this->request->post['shipping_carrier'])) {
			$data['shipping_carrier'] = $this->request->post['shipping_carrier'];
		} elseif (!empty($product_info)) {
			$data['shipping_carrier'] = $product_info['shipping_carrier'];
		} else {
			$data['shipping_carrier'] = '';
		}
		$this->load->model('shopmanager/recurring');

		$data['recurrings'] = $this->model_shopmanager_recurring->getRecurrings();

		if (isset($this->request->post['product_recurrings'])) {
			$data['product_recurrings'] = $this->request->post['product_recurrings'];
		} elseif (!empty($product_info)) {
			$data['product_recurrings'] = $this->model_shopmanager_product->getRecurrings($product_info['product_id']);
		} else {
			$data['product_recurrings'] = array();
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['tax_class_id'])) {
			$data['tax_class_id'] = $this->request->post['tax_class_id'];
		} elseif (!empty($product_info)) {
			$data['tax_class_id'] = $product_info['tax_class_id'];
		} else {
			$data['tax_class_id'] = 0;
		}

		if (isset($this->request->post['date_available'])) {
			$data['date_available'] = $this->request->post['date_available'];
		} elseif (!empty($product_info)) {
			$data['date_available'] = ($product_info['date_available'] != '0000-00-00') ? $product_info['date_available'] : '';
		} else {
			$data['date_available'] = date('Y-m-d');
		}
		//print("<pre>".print_r ($this->request->post,true )."</pre>");
		
		if (isset($this->request->post['quantity'])) {
			$data['quantity'] = $this->request->post['quantity'];
		} elseif (!empty($product_info)) {
			$data['quantity'] = $product_info['quantity'];
		} else {
			$data['quantity'] = 0;
		}
        if (isset($this->request->post['unallocated_quantity'])) {
			$data['unallocated_quantity'] = $this->request->post['unallocated_quantity'];
		} elseif (!empty($product_info)) {
			$data['unallocated_quantity'] = $product_info['unallocated_quantity'];
		} else {
			$data['unallocated_quantity'] = 0;
		}

		

        if (isset($this->request->post['location'])) {
			$data['location'] = $this->request->post['location'];
		} elseif (!empty($product_info)) {
			$data['location'] = $product_info['location'];
		} else {
			$data['location'] = '';
		}
		
		if (isset($this->request->post['condition_id'])) {
			$data['condition_id'] = $this->request->post['condition_id'];
		} elseif (!empty($product_info)) {
			$data['condition_id'] = $product_info['condition_id'];
		} else {
			$data['condition_id'] = 9;
		}
		if (isset($data['product_id'])) {
			if($data['unallocated_quantity']==0 && $data['quantity'] ==0){
				$data['location']='';
				$data['sku']=($data['condition_id']==9)?$data['upc']:$data['product_id'];
			}
		}
		if($data['upc']!=''){
			$existing_products = $this->model_shopmanager_product->getProductByUPC($data['upc'],$data['condition_id']);
			foreach ($existing_products  as $existing_product) {
					
				
				$data['existing_products'][$existing_product['condition_id']] = array(
					'product_id' => $existing_product['product_id'],
					'condition_id' => $existing_product['condition_id'],
					'upc' => $existing_product['upc'],
					'url' => $existing_product['has_specifics'] ? $this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $existing_product['product_id'] . $url, true) : $this->url->link('shopmanager/product_search', 'token=' . $this->session->data['token'] . '&upc='.$existing_product['upc'].'&product_id=' . $existing_product['product_id']. '&condition_id=' . $existing_product['condition_id'] . $url, true)
				);
			}
		//	//print("<pre>".print_r ($data['existing_products'],true )."</pre>");
		}

		if (isset($this->request->post['minimum'])) {
			$data['minimum'] = $this->request->post['minimum'];
		} elseif (!empty($product_info)) {
			$data['minimum'] = $product_info['minimum'];
		} else {
			$data['minimum'] = 1;
		}

		if (isset($this->request->post['subtract'])) {
			$data['subtract'] = $this->request->post['subtract'];
		} elseif (!empty($product_info)) {
			$data['subtract'] = $product_info['subtract'];
		} else {
			$data['subtract'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($product_info)) {
			$data['sort_order'] = $product_info['sort_order'];
		} else {
			$data['sort_order'] = 1;
		}

		$this->load->model('localisation/stock_status');

		$data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

		if (isset($this->request->post['stock_status_id'])) {
			$data['stock_status_id'] = $this->request->post['stock_status_id'];
		} elseif (!empty($product_info)) {
			$data['stock_status_id'] = $product_info['stock_status_id'];
		} else {
			$data['stock_status_id'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
		}

		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($product_info)) {
			$data['weight'] = round($product_info['weight'],3);
		} else {
			$data['weight'] = 0;
		}

		
		if (isset($product_info['weight_class_id'])){
			$this->load->model('shopmanager/localisation/weight_class');
			$weight_class_info = $this->model_shopmanager_localisation_weight_class->getWeightClasses(array('weight_class_id' => $product_info['weight_class_id']));
			//	//print("<pre>".print_r ($weight_class_info,true )."</pre>");
			$product_info['weight_class_title']=$weight_class_info[0]['unit'];
		}

		if (isset($this->request->post['weight_class_id'])) {
			$data['weight_class_id'] = $this->request->post['weight_class_id'];
		} elseif (!empty($product_info)) {
			$data['weight_class_id'] = $product_info['weight_class_id'];
		} else {
			$data['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		if (isset($this->request->post['weight_class_title'])) {
			$data['weight_class_title'] = $this->request->post['weight_class_title'];
		} elseif (!empty($product_info)) {
			$data['weight_class_title'] = $product_info['weight_class_title'];
		} else {
			$data['weight_class_title'] = 'Lbs';
		}

		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($product_info)) {
			$data['length'] = round($product_info['length'],1);
		} else {
			$data['length'] = 0;
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($product_info)) {
			$data['width'] = round($product_info['width'],1);
		} else {
			$data['width'] = 0;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($product_info)) {
			$data['height'] = round($product_info['height'],1);
		} else {
			$data['height'] = 0;
		}

		if (isset($product_info['length_class_id'])){
			$this->load->model('shopmanager/localisation/length_class');

			$length_class_info = $this->model_shopmanager_localisation_length_class->getLengthClasses(array('length_class_id' => $product_info['length_class_id']));
			//print("<pre>".print_r ($length_class_info,true )."</pre>");
			$product_info['length_class_title']=$length_class_info[0]['unit'];
		}
		
		if (isset($this->request->post['length_class_id'])) {
			$data['length_class_id'] = $this->request->post['length_class_id'];
		} elseif (!empty($product_info)) {
			$data['length_class_id'] = $product_info['length_class_id'];
		} else {
			$data['length_class_id'] = $this->config->get('config_length_class_id');
		}

		if (isset($this->request->post['length_class_title'])) {
			$data['length_class_title'] = $this->request->post['length_class_title'];
		} elseif (!empty($product_info)) {
			$data['length_class_title'] = $product_info['length_class_title'];
		} else {
			$data['length_class_title'] = '';
		}

		$this->load->model('shopmanager/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($product_info)) {
			$manufacturer_info = $this->model_shopmanager_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		$this->load->model('shopmanager/ebay');
		if (isset($data['upc']) && is_numeric($data['upc'])) { 
			$ProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources($data['upc']);
			//$data['ebay_info']=$this->model_shopmanager_ebay->get($data['upc'],null,'sold',null,100,$data['marketplace_item_id']);
			$data['ebay_info']=json_decode($ProductInfoSources['ebay_search'],true);
		}elseif(isset($data['product_id'])){
			$ProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources(null,null,$data['product_id']);
			$data['ebay_info']=json_decode($ProductInfoSources['ebay_search'],true);
		//print("<pre>".print_r ($data['ebay_info'],true )."</pre>"); 
		}   

		// Categories
		$this->load->model('shopmanager/catalog/category');

		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {
			$categories = $this->model_shopmanager_product->getProductCategories($this->request->get['product_id']);
		} else {
		
			$categories = array();
		}

		
		
	//print("<pre>".print_r ($data['ebay_info'],true )."</pre>");
		$data['product_categories'] = array();
	
		foreach ($categories as $category_id) {
			$category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
			//print("<pre>".print_r ($category_info,true )."</pre>");
			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name_category' => $category_info['name'],
					'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
				);
				if( $category_info['leaf']=='1'){
					$data['category_id']=$category_info['category_id'];
				}
			}
		}
		$this->load->model('shopmanager/condition');

		if(isset($data['category_id'])){
					// Conditions
			//print("<pre>" . print_r(value: '1692:PRODUCTSEARCH.php') . "</pre>");
			$data['conditions']=$this->model_shopmanager_condition->getConditionDetails($data['category_id']);
			$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($data['category_id']);

			$category_leaf = $this->model_shopmanager_catalog_category->getCategoryLeaf($data['category_id']);

			if (!is_array($category_specific_info)) {
				
				if($category_leaf ==1){
				//	//print("<pre>".print_r ('getform:2631',true )."</pre>");
					$this->response->redirect($this->url->link('shopmanager/catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $data['category_id'] . '&product_id='.$data['product_id'], true));
				}else{
					$this->error['category']= $this->language->get('error_category_not_leaf');
				}
			}
	
		
			
		

		if (empty($data['product_description'][1]['specifics']) && isset( $category_specific_info[1]) && $category_leaf ==1 && is_array($category_specific_info[1]['specifics'])) {
		
			$this->load->model('shopmanager/ai');
			$this->model_shopmanager_ai->getProductSpecifics($this->request->get['product_id'],$data,$category_specific_info[1]);
			//print("<pre>".print_r ($specifics,true )."</pre>");
			
			$data['product_description'] = $this->model_shopmanager_product->getProductDescriptions($this->request->get['product_id']);
			$data['specifics']='class="active"';

		}elseif(!is_array($category_specific_info[1]['specifics'])){
			//print("<pre>".print_r ('getform:2652',true )."</pre>");
			$this->response->redirect($this->url->link('shopmanager/catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $data['category_id'] . '&product_id='.$data['product_id'], true));
		}

		

			if (isset($data['marketplace_item_id']) || $data['marketplace_item_id']>0 && $category_leaf ==1 ) {
				
				$this->load->model('shopmanager/ebay');
				$ebay_specific_info=$this->model_shopmanager_ebay->getProductSpecificsOLD($data['marketplace_item_id']);
				//$ebay_specific_info_tmp=$this->model_shopmanager_ebay->getProductSpecifics($data['marketplace_item_id']);
				//print("<pre>" . print_r(($ebay_specific_info_tmp), true) . "</pre>");

			}

		}else{
			//print("<pre>" . print_r(value: '1735:PRODUCTSEARCH.php') . "</pre>");

			$data['conditions'] =  $this->model_shopmanager_condition->getConditionDetails();
		}
	//	//print("<pre>".print_r ($data['conditions'],true )."</pre>");
	//	//print("<pre>".print_r ($data['product_description'],true )."</pre>");
	/*	if (!is_array($data['product_description'][1]['specifics'])) {*/
	foreach ($data['product_description'] as $key => $product_description) {
		// Affichage des détails spécifiques du produit avant la fusion
	//	//print("<pre>" . print_r(($product_description['specifics']), true) . "</pre>");
		if(isset($category_leaf) && $category_leaf ==1){
				$category_specifics_to_merge=array();
				if(is_array($category_specific_info[$key]['specifics'])){
					foreach($category_specific_info[$key]['specifics'] as $keyName=>$specific){
						$category_specifics_to_merge[$keyName]['specific_info']=$specific;
					}
				}else{
				//	//print("<pre>".print_r ('getform:2683',true )."</pre>");
					//$this->response->redirect($this->url->link('shopmanager/catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $data['category_id'] . '&product_id='.$data['product_id'], true));
				}
		
				
			


			// Vérification de l'existence de l'index $key dans $category_specific_info
			if (is_array($data['product_description'][$key]['specifics']  )) {
			
				if(is_array($ebay_specific_info) && $key==1){
					
					//	//print("<pre>".print_r ($product_description['specifics'],true )."</pre>");
							$data['product_description'][1]['specifics'] = //$ebay_specific_info;
							$this->model_shopmanager_tools->custom_merge_recursive( $product_description['specifics'],$ebay_specific_info);
						}
					//	$data['product_description'][$key]['specifics'] = $category_specific_info[$key];
					
			//		//print("<pre>".print_r ($data['product_description'][$key]['specifics'] ,true )."</pre>");
				//	//print("<pre>".print_r ($category_specifics_to_merge,true )."</pre>");
				//	//print("<pre>" . print_r(($data['product_description'][$key]['specifics'] ), true) . "</pre>");	
					//print("<pre>" . print_r($category_specifics_to_merge, true) . "</pre>");
			} else {
				/*$this->load->model('shopmanager/ai');
				$specifics_to_translate=$data['product_description'][1]['specifics'];
				
				$specifics_to_translated=$this->model_shopmanager_ai->translate_specifics($data['product_id'],$specifics_to_translate,['code'=>'Fr','language_id' => $key]);
				$data['product_description'][$key]['specifics'] =$specifics_to_translated;*/

				$this->response->redirect($this->url->link('shopmanager/product_search', 'token=' . $this->session->data['token'] . '&upc='.$data['upc'].'&product_id=' . $data['product_id']. '&condition_id=' . $data['condition_id'] . $url, true));

			}
			$data['product_description'][$key]['specifics'] =
			$this->model_shopmanager_tools->custom_merge_recursive($data['product_description'][$key]['specifics'] , $category_specifics_to_merge);
			// Affichage des détails spécifiques du produit après la fusion
		//	//print("<pre>".print_r ($data['product_description'][$key]['specifics'] ,true )."</pre>");
			
		}else{
		
			$data['error']= $this->language->get('error_category_not_leaf');
			$data['error_category']= $this->language->get('error_category_not_leaf');
		}


}	
		// Filters
		$this->load->model('shopmanager/filter');

		if (isset($this->request->post['product_filter'])) {
			$filters = $this->request->post['product_filter'];
		} elseif (isset($this->request->get['product_id'])) {
			$filters = $this->model_shopmanager_product->getProductFilters($this->request->get['product_id']);
		} else {
			$filters = array();
		}

		$data['product_filters'] = array();

		foreach ($filters as $filter_id) {
			$filter_info = $this->model_shopmanager_filter->getFilter($filter_id);

			if ($filter_info) {
				$data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}
	
		// Attributes
		$this->load->model('shopmanager/attribute');

		if (isset($this->request->post['product_attribute'])) {
			$product_attributes = $this->request->post['product_attribute'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_attributes = $this->model_shopmanager_product->getProductAttributes($this->request->get['product_id']);
		} else {
			$product_attributes = array();
		}

		$data['product_attributes'] = array();

		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_shopmanager_attribute->getAttribute($product_attribute['attribute_id']);

			if ($attribute_info) {
				$data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => $attribute_info['name'],
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}

		// Options
		$this->load->model('shopmanager/option');

		if (isset($this->request->post['product_option'])) {
			$product_options = $this->request->post['product_option'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_options = $this->model_shopmanager_product->getProductOptions($this->request->get['product_id']);
		} else {
			$product_options = array();
		}

		$data['product_options'] = array();

		foreach ($product_options as $product_option) {
			$product_option_value_data = array();

			if (isset($product_option['product_option_value'])) {
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
                        'unallocated_quantity'    => $product_option_value['unallocated_quantity'],
                        'location'                => $product_option_value['location'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => round($product_option_value['price'],2),
						'price_with_shipping'     => $product_option_value['price_with_shipping'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
			}

			$data['product_options'][] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',
				'required'             => $product_option['required']
			);
		}

		$data['option_values'] = array();

		foreach ($data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($data['option_values'][$product_option['option_id']])) {
					$data['option_values'][$product_option['option_id']] = $this->model_shopmanager_option->getOptionValues($product_option['option_id']);
				}
			}
		}

		$this->load->model('customer/customer_group');

		$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		if (isset($this->request->post['product_discount'])) {
			$product_discounts = $this->request->post['product_discount'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_discounts = $this->model_shopmanager_product->getProductDiscounts($this->request->get['product_id']);
		} else {
			$product_discounts = array();
		}

		$data['product_discounts'] = array();

		foreach ($product_discounts as $product_discount) {
			$data['product_discounts'][] = array(
				'customer_group_id' => $product_discount['customer_group_id'],
				'quantity'          => $product_discount['quantity'],
                'unallocated_quantity'          => $product_discount['unallocated_quantity'],
                'location'          => $product_discount['location'],
				'priority'          => $product_discount['priority'],
				'price'             => round($product_discount['price'],2),
				'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
			);
		}

		if (isset($this->request->post['product_special'])) {
			$product_specials = $this->request->post['product_special'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_specials = $this->model_shopmanager_product->getProductSpecials($this->request->get['product_id']);
		} else {
			$product_specials = array();
		}

		$data['product_specials'] = array();

		foreach ($product_specials as $product_special) {
			$data['product_specials'][] = array(
				'customer_group_id' => $product_special['customer_group_id'],
				'priority'          => $product_special['priority'],
				'price'             => round($product_special['price'],2),
				'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
			);
		}
		
		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($product_info)) {
			$data['image'] = $product_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100); 
		} elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_images = $this->model_shopmanager_product->getProductImages($this->request->get['product_id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order']
			);
		}

		// Downloads
		$this->load->model('shopmanager/download');

		if (isset($this->request->post['product_download'])) {
			$product_downloads = $this->request->post['product_download'];
		} elseif (isset($this->request->get['product_id'])) {
			$product_downloads = $this->model_shopmanager_product->getProductDownloads($this->request->get['product_id']);
		} else {
			$product_downloads = array();
		}

		$data['product_downloads'] = array();

		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_shopmanager_download->getDownload($download_id);

			if ($download_info) {
				$data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}

		if (isset($this->request->post['product_related'])) {
			$products = $this->request->post['product_related'];
		} elseif (isset($this->request->get['product_id'])) {
			$products = $this->model_shopmanager_product->getProductRelated($this->request->get['product_id']);
		} else {
			$products = array();
		}

		$data['product_relateds'] = array();

		foreach ($products as $product_id) {
			$related_info = $this->model_shopmanager_product->getProduct($product_id);

			if ($related_info) {
				$data['product_relateds'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		if (isset($this->request->post['points'])) {
			$data['points'] = $this->request->post['points'];
		} elseif (!empty($product_info)) {
			$data['points'] = $product_info['points'];
		} else {
			$data['points'] = '';
		}

		if (isset($this->request->post['product_reward'])) {
			$data['product_reward'] = $this->request->post['product_reward'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_reward'] = $this->model_shopmanager_product->getProductRewards($this->request->get['product_id']);
		} else {
			$data['product_reward'] = array();
		}

		if (isset($this->request->post['product_layout'])) {
			$data['product_layout'] = $this->request->post['product_layout'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_layout'] = $this->model_shopmanager_product->getProductLayouts($this->request->get['product_id']);
		} else {
			$data['product_layout'] = array();
		}

		$this->load->model('design/layout');
	//print("<pre>".print_r ('2320:product.php' ,true )."</pre>");
	//print("<pre>".print_r ($data ,true )."</pre>");
//		//print("<pre>".print_r ($data['product_description'][1] ,true )."</pre>");
		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = '';//$this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = '';//$this->load->controller('shopmanager/marketplace_popup');
		$data['wait_popup'] = '';//$this->load->controller('shopmanager/wait_popup');
		$data['ocr'] = $this->load->controller('shopmanager/ocr');
	
		$this->response->setOutput($this->load->view('shopmanager/product_form', $data));
	}


public function product_feed() {
	
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
	$this->load->language('shopmanager/product_search');
	$this->load->model('shopmanager/product_search');
	$this->load->model('shopmanager/product');
	$this->load->model('shopmanager/ai');
	$this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/product_specific');
	$this->load->model('shopmanager/tools');
//print("<pre>" . print_r('1937:product', true) . "</pre>");
//print("<pre>" . print_r($this->request->post, true) . "</pre>");

	
	if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post)) {
		// Logique pour sauvegarder les données sélectionnées

	
	//print("<pre>" . print_r($save_data, true) . "</pre>");
	   // $selected_data = $this->request->post['save_data'];
	   if(isset($this->request->post['product_id'])){
			$product_info=$this->model_shopmanager_product->getProduct($this->request->post['product_id']);
			unset($product_info['description']);
		//print("<pre>" . print_r('3864:product', true) . "</pre>");
		//print("<pre>" . print_r($product_info, true) . "</pre>");
			foreach ($product_info as $key => $value) {
				if (!array_key_exists($key, $this->request->post)) {
					$this->request->post[$key] =  $value;
				}
			}
			$this->request->post['product_description']=$this->model_shopmanager_product->getProductDescriptions($this->request->post['product_id']);


			
			//print("<pre>" . print_r('3872:product', true) . "</pre>");
		//print("<pre>" . print_r($this->request->post, true) . "</pre>");
		//return 'stop';
		} 
		
//	//print("<pre>" . print_r('4011:product', true) . "</pre>");
//print("<pre>" . print_r($this->request->post, true) . "</pre>");
		if(isset($this->request->post['manageProductInfoSources'])){

			$manageProductInfoSources=json_decode(htmlspecialchars_decode($this->request->post['manageProductInfoSources']),true);
		//	//print("<pre>" . print_r('4020:product', true) . "</pre>");
		//	//print("<pre>" . print_r($manageProductInfoSources, true) . "</pre>");

		}
//		//print("<pre>" . print_r($this->request->post, true) . "</pre>");
	   $this->request->post=$this->model_shopmanager_product_search->processProductSearchData($this->request->post);
	//print("<pre>" . print_r('1981:product', true) . "</pre>");
	//print("<pre>" . print_r($this->request->post, true) . "</pre>");
					   
//return NULL;
	
		//&& $this->validateForm()
	

		if(!isset($this->request->post['product_id'])){
			
			$product_id=$this->model_shopmanager_product->addProduct($this->request->post); 
			//print("<pre>" . print_r('4038:product', true) . "</pre>");
			//print("<pre>" . print_r($manageProductInfoSources, true) . "</pre>");
			if(!isset($manageProductInfoSources)){
				
				$manageProductInfoSources=$this->model_shopmanager_product_search->manageProductInfoSources(null, $manageProductInfoSources,$product_id );
				//print("<pre>" . print_r('4039:product', true) . "</pre>");
				//print("<pre>" . print_r($manageProductInfoSources, true) . "</pre>");

			}
			//print("<pre>" . print_r('4038:product', true) . "</pre>");
			//print("<pre>" . print_r($product_id, true) . "</pre>");
	
			$this->session->data['success'] = $this->language->get('text_success');
		
		}else{
			$product_id=$this->request->post['product_id'];
			
			$product_info['product_image']=$this->model_shopmanager_product->getProductImages($product_id);
			$product_info['product_description']=$this->model_shopmanager_product->getProductDescriptions($product_id);

			if (isset($this->request->post['images'])){
				unset($product_info['image']);
				unset($product_info['product_image']);
			}

			foreach ($this->request->post as $key => $value) {
		//		if (array_key_exists($key, $product_info)) {
					$product_info[$key] =  $value;
			//	}
			}
		//print("<pre>" . print_r('3923:product_id', true) . "</pre>");   
		//print("<pre>" . print_r($product_info, true) . "</pre>");
		//	//print("<pre>" . print_r($product_info, true) . "</pre>");
			$this->model_shopmanager_product->editProduct($product_id,$product_info);
			
		}
	/*	if (!isset($product_info['marketplace_item_id']) || $product_info['marketplace_item_id']==0) {

	$result=$this->model_shopmanager_ebay->add($product_id, 0);
		//print("<pre>" . print_r('3895:product_id', true) . "</pre>");   
//print("<pre>" . print_r($result, true) . "</pre>");

		   if ($result['Ack']!='Failure') {
			 
				$this->model_shopmanager_product->editProductMarketplaceItemId($product_id,$result['ItemID']);
				$json['success'] = true;
				$json['message'] = $result['ItemID'];
			} else {
				$json['error'] = false;
				$json['message'] =$result['Errors']['LongMessage']??'';
			}
}*/
		$url = '';

		

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_product_id'])) {
			$url .= '&filter_product_id=' . urlencode(html_entity_decode($this->request->get['filter_product_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($product_id)) {
		//	$url .= '&filter_product_id=' . urlencode(html_entity_decode($product_id, ENT_QUOTES, 'UTF-8'));
			$url .= '&product_id=' . urlencode(html_entity_decode($product_id, ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_marketplace_account'])) {
			$url .= '&filter_marketplace_account=' . urlencode(html_entity_decode($this->request->get['filter_marketplace_account'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . urlencode(html_entity_decode($this->request->get['filter_category_id'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_price'])) {
			$url .= '&filter_price=' . $this->request->get['filter_price'];
		}

		if (isset($this->request->get['filter_quantity'])) {
			$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
		}

		if (isset($this->request->get['filter_unallocated_quantity'])) {
			$url .= '&filter_unallocated_quantity=' . $this->request->get['filter_unallocated_quantity'];
		}

		if (isset($this->request->get['filter_location'])) {
			$url .= '&filter_location=' . $this->request->get['filter_location'];
		}
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if (isset($this->request->get['filter_specifics'])) {
			$url .= '&filter_specifics=' . $this->request->get['filter_specifics'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (isset($this->request->get['limit'])){
			$url .= '&limit=' . $this->request->get['limit'];
		}
		$url .= '&updated=yes';
		unset($this->request->post);
		$this->session->data['success'] = $this->language->get('text_success');
		$this->response->redirect($this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . $url, true));
	}

	$this->getForm();
	 
}

public function product_source_info_feed() {

	$execution_times = [];
	$n=0;

	
	// Charger le modèle
	$start_time = microtime(true);

	$this->load->model('shopmanager/product');
	$this->load->model('shopmanager/product_search');
	$this->load->model('shopmanager/product');
	$this->load->model('shopmanager/condition');
	$this->load->model('shopmanager/catalog/category');
	$this->load->model('shopmanager/product_specific');

	// Définir les variables de texte

	$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
	$test_selected =[];
	//$test_selected = array('11634'=>'11634');
	$test_selected = ['3450'];
	// ['11634', '19183', '24300']

	//print("<pre>" . print_r($test_selected,true) . "</pre>");
	   // Récupérer les données `product_id` envoyées dans la requête POST
	   $json = [];
	   
	   $data = json_decode(file_get_contents('php://input'), true);
	   $product_ids=$data['product_ids']??null;
	 //  $product_ids = isset($this->request->post['product_ids']) ? json_decode($this->request->post['product_ids'], true) : null;
	// $product_ids = $test_selected ;
   $product_ids = $product_ids ?? $test_selected;

   //print("<pre>" . print_r($product_ids,true) . "</pre>");
//	if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['selected']))  {
if (!empty($product_ids))  {
	
	foreach($product_ids as $product_id){
		
			$json['product_id'][$product_id] = $product_id;

			$product_info=[];

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true);
		
			$product_info = $this->model_shopmanager_product->getProduct($product_id);
			
			
if(isset($product_info['upc']) && is_numeric($product_info['upc'])){
	$this->model_shopmanager_product_search->manageProductInfoSources($product_info['upc']);
	//$product_info_source = 
}

		//	echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
		//sleep(1);
		}
		$json['success'] = true;
		$json['message'] = 'Success';
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
public function product_source_info_feed_from_search() {

	$execution_times = [];
	$n=0;
	$start_time = microtime(true); 
	
	// Charger le modèle
	 

//	$this->load->model('shopmanager/fast_add');
	$this->load->model('shopmanager/product_search');
	$this->load->model('shopmanager/product');
	$this->load->model('shopmanager/condition');
	$this->load->model('shopmanager/catalog/category');
	$this->load->model('shopmanager/product_specific');
	$this->load->model('shopmanager/ai');
	$this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/tools');

	// Définir les variables de texte

	
	

	   // Récupérer les données `product_id` envoyées dans la requête POST
	   $json = [];
	   
	   $data = json_decode(file_get_contents('php://input'), true);

	       // Vérifier et normaliser les clés
		   $normalizedData = [];
		   foreach ($data as $key => $value) {
			   // Retirer les crochets [] si présents dans la clé
			   $normalizedKey = preg_replace('/\[\]$/', '', $key);
			   
			   // Ajouter au tableau normalisé
			   $normalizedData[$normalizedKey] = $value;
		   }
	  $data =$normalizedData;
//	  //print("<pre>" . print_r('2151product_search',true) . "</pre>");
	//print("<pre>" . print_r($data,true) . "</pre>");
	   $product_id=$data['product_id']??null;
		
			$search_data['product_id'][$product_id] = $product_id??null;

			$product_info=[];

			
		if($product_id){
			$product_info = $this->model_shopmanager_product->getProduct($product_id);
			$search_data['product_info_source'] = $this->model_shopmanager_product_search->manageProductInfoSources($product_info['upc']);
		}elseif(isset($data['search']) && is_numeric($data['search']) ){
			
			$search_data['product_info_source'] = $this->model_shopmanager_product_search->manageProductInfoSources($data['search']);
			
			$search_data['product_info_source']['upc'] =$data['search']??''; 

			$ebay_search = json_decode($search_data['product_info_source']['ebay_search'], true); // Décoder les items JSON
			$responseItem = [];
			
			// Vérifier que json_items est un tableau
			if (isset($ebay_search) &&is_array($ebay_search)) {
				// Parcourir les `selected_ebay_item` pour trouver les informations correspondantes
				foreach ($ebay_search as $item) {
					$responseItem[] = $item; 
				}
			}
			$search_data['responseItem'] =$responseItem;   
	
			$search_data['product_info_source']['manageProductInfoSources']=$responseItem;
			$data['product_info_source'] = $search_data['product_info_source']??'' ; 
	//		//print("<pre>" . print_r('2190',true) . "</pre>");
	//		//print("<pre>" . print_r($data,true) . "</pre>");
			$json['html']=$this->process_search_field($data);
		
		}elseif (isset($data['selected_ebay_item'])) {
			
			$json_items = json_decode(($data['json_items']), true); // Décoder les items JSON
		//print("<pre>" . print_r('elseif',true) . "</pre>");
		//print("<pre>" . print_r($data,true) . "</pre>");
		//print("<pre>" . print_r($json_items,true) . "</pre>");
			$responseItem = [];
			if (!is_array($data['selected_ebay_item'])) {
				// Si la valeur est une chaîne, la convertir en tableau
				$data['selected_ebay_item'] = [$data['selected_ebay_item']];
			}
			
		/*	// Si le tableau n'a pas d'index [0], on ajuste la structure
			if (!isset($data['selected_ebay_item'][0])) {
				$data['selected_ebay_item_test'][0] = $data['selected_ebay_item'];
				$data['selected_ebay_item'] = $data['selected_ebay_item_test'];
			}*/
			
			// Debugging
		//	//print("<pre>" . print_r('2205', true) . "</pre>");
		//	//print("<pre>" . print_r($data, true) . "</pre>");
			

			// Vérifier que json_items est un tableau
			if (is_array($json_items)) {
				// Parcourir les `selected_ebay_item` pour trouver les informations correspondantes
				foreach ($data['selected_ebay_item'] as $selectedProductId) {
					foreach ($json_items as $key=>$item) {
						$search_data[$key] =$item;   
						if (isset($item['marketplace_item_id']) && $item['marketplace_item_id'] == $selectedProductId) {
							// Ajouter l'item correspondant à $responseItem
							
							$responseItem[] = $item; 
						
							break; // Passer au prochain `selectedProductId`
						}
					}
				}
			}
			$search_data['responseItem'] =$responseItem;   
	//		//print("<pre>" . print_r($search_data['responseItem'],true) . "</pre>");
			// Appeler la gestion des informations du produit
			$search_data['product_info_source'] = $this->model_shopmanager_product_search->manageProductInfoSources(null, $responseItem)??'';  
			if (isset($responseItem) && is_array($responseItem)) {
			//print("<pre>" . print_r($responseItem,true) . "</pre>");
				$search_data['product_info_source']['manageProductInfoSources'] = $responseItem;
			} else {
				// Gérer le cas où $responseItem n'est pas un tableau
				$search_data['product_info_source']['manageProductInfoSources'] = [];
				error_log("Expected array but received: " . gettype($responseItem) . " with value: " . (is_scalar($responseItem) ? $responseItem : json_encode($responseItem)));
			}
			
		//	$search_data['product_info_source']['manufacturer'] = $data['manufacturer']??'';
		//	$search_data['product_info_source']['model'] = $data['model']??'';
		//	$search_data['product_info_source']['category_id'] = $data['category_id']??'';
		//	$search_data['product_info_source']['title'] = $data['title'];
			$data['product_info_source'] = $search_data['product_info_source']??'' ; 
			//print("<pre>" . print_r(json_decode($json['product_info_source']['ebay_search'],true),true) . "</pre>");
		//	//print("<pre>" . print_r('2252',true) . "</pre>");
		//	//print("<pre>" . print_r($data,true) . "</pre>");
		//print("<pre>" . print_r('2487',true) . "</pre>");
			//$json['html']=$this->process_search_field($data);
		//print("<pre>" . print_r($json['html'],true) . "</pre>");
		}else{
			$search_data['upc'] =$data['upc']??''; 
			$search_data['json_items'] =$data['json_items']??''; 
			if (isset($data['selected_ebay_item']) && is_array($data['selected_ebay_item'])) {
				$search_data['selected_ebay_item'] = $data['selected_ebay_item'];
			} elseif (isset($data['selected_ebay_item'])) { // Si la clé est nommée différemment
				$search_data['selected_ebay_item'] = $data['selected_ebay_item'];
			} else {
				$search_data['selected_ebay_item'] = null;
				$json['error'] = 'Impossible de récupérer selected_ebay_item.';
			}
			//$search_data['selected_ebay_item'] =$data['selected_ebay_item']; 
		//	$search_data['selected_ebay_item'] =is_array($data['selected_ebay_item'])?json_encode($data['selected_ebay_item']):'';
			$data['product_info_source'] = $search_data['product_info_source']??'' ; 
			//$search_data['product_info_source']['title'] = $data['title'];
		//print("<pre>" . print_r('2504',true) . "</pre>");
		
			//$json['html']=$this->process_search_field($data);
			
		}
		
			
			
		$json['html']=$this->process_search_field($data);
	
	

				$total_execution_time = array_sum($execution_times);

			//	//echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
			//sleep(1);
		
			$json['success'] = true;
			$json['message'] = "";
			//print("<pre>" . print_r($json,true) . "</pre>");
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		
	
}
public function product_source_info_fast_feed() {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$execution_times = [];
	$n=0;

	$start_time = microtime(true); 
	// Charger le modèle
	 

//	$this->load->model('shopmanager/fast_add');
	$this->load->model('shopmanager/product_search');
	$this->load->model('shopmanager/product');
	$this->load->model('shopmanager/condition');
	$this->load->model('shopmanager/catalog/category');
	$this->load->model('shopmanager/product_specific');
	$this->load->model('shopmanager/ai');
	$this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/tools');

	// Définir les variables de texte

	
	

	   // Récupérer les données `product_id` envoyées dans la requête POST
	   $json = [];
	   
	   $data = json_decode(file_get_contents('php://input'), true);
	   $data['product_id']= $data['product_id']??26199;
	       // Vérifier et normaliser les clés
		   $normalizedData = [];
		   foreach ($data as $key => $value) {
			   // Retirer les crochets [] si présents dans la clé
			   $normalizedKey = preg_replace('/\[\]$/', '', $key);
			   
			   // Ajouter au tableau normalisé
			   $normalizedData[$normalizedKey] = $value;
		   }
	  $data =$normalizedData;
//	  //print("<pre>" . print_r('2151product_search',true) . "</pre>");
	//print("<pre>" . print_r($data,true) . "</pre>");
	   $product_id=$data['product_id']??null;
		
			$json['product_id'][$product_id] = $product_id??null;
			
		

		//	$product_info=[];

			
		if($product_id){
			$data = $this->model_shopmanager_product->getProduct($product_id);
			$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($data['category_id']);
		//	//print("<pre>" . print_r($category_specific_info,true) . "</pre>");
			if(!isset($category_specific_info[1])){
						$json['html']='category';
				//	//print("<pre>" . print_r($data,true) . "</pre>");
					
					
						$total_execution_time = array_sum($execution_times);
			
						//	//echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
						//sleep(1);
					
						$json['success'] = true;
						$json['message'] = "";
						$this->response->addHeader('Content-Type: application/json');
						$this->response->setOutput(json_encode($json));
						return null;
			}

			$data['product_info_source'] = $this->model_shopmanager_product_search->manageProductInfoSources($data['upc']);
		
		
			//print("<pre>" . print_r(json_decode($json['data_source']['ebay_search'],true),true) . "</pre>");
		//	//print("<pre>" . print_r('2252',true) . "</pre>");
		if(isset($this->request->get['view'])){
			$data['view']=$this->request->get['view'];
		}
		//	//print("<pre>" . print_r($data,true) . "</pre>");
			$json['html']=$this->process_search_field_fast_list($data);
	//	//print("<pre>" . print_r($data,true) . "</pre>");
		}
		
			$total_execution_time = array_sum($execution_times);

			//	//echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
			//sleep(1);
		
			$json['success'] = true;
			$json['message'] = "";
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		
	
}

public function generateProductSpecifics() {
		
    $json = array();
    if (isset($this->request->get['category_id']) || isset($this->request->get['product_id'])) {
            $category_id = $this->request->get['category_id'];
            $product_id = $this->request->get['product_id'];
            $marketplace_item_id = $this->request->get['marketplace_item_id']??null;
    }else{
        $category_id = $_GET['category_id']??null;
        $product_id = $_GET['product_id']??null;
        $marketplace_item_id = $_GET['marketplace_item_id']??null;
    }

    if (isset($category_id) || isset($product_id)) {
            $this->load->model('shopmanager/product');
            $this->load->model('shopmanager/catalog/category');
            $this->load->model('shopmanager/ebay');
            $this->load->model('shopmanager/ai');
            $this->load->model('localisation/language');
			$this->load->model('shopmanager/tools');
			$this->load->model('shopmanager/manufacturer');


            
            

            $this->model_shopmanager_product->removeProductSpecifics($product_id);

            $category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($category_id);
            
            $category_leaf = $this->model_shopmanager_catalog_category->getCategoryLeaf($category_id);

            if (is_array($category_specific_info)) {
            //	//print("<pre>".print_r ($category_specific_info,true )."</pre>");
            $product_info = $this->model_shopmanager_product->getProduct($product_id);	
        
            
             $product_info['color']=$product_info['color_description'];



                if (isset( $category_specific_info[1]) && $category_leaf ==1 && is_array($category_specific_info[1]['specifics'])) {
            
                //jomod
        //        $specifics=$this->model_shopmanager_ai->getProductSpecifics($product_id,$product_info,$category_specific_info[1]);
            //	//print("<pre>".print_r ($specifics,true )."</pre>");
            //	
                $ProductSpecifics = $this->model_shopmanager_product->getProductSpecifics($product_id);
            //	$data['specifics']='class="active"';
        //	//print("<pre>".print_r ($ProductSpecifics,true )."</pre>");
            

            

                   /* if (isset($marketplace_item_id) || $marketplace_item_id > 0 && $category_leaf ==1 ) {				
                        $ebay_specific_info=$this->model_shopmanager_ebay->getProductSpecifics($marketplace_item_id);
                    }*/

                    //print("<pre>".print_r ($production_descriptions,true )."</pre>");
                /*	if (!is_array($production_descriptions[1]['specifics'])) {*/
                foreach ($ProductSpecifics as $key => $ProductSpecific) {
                    // Affichage des détails spécifiques du produit avant la fusion
                //	//print("<pre>" . print_r(($ProductSpecific['specifics']), true) . "</pre>");
                    if(isset($category_leaf) && $category_leaf ==1){
                            $category_specifics_to_merge=array();
                            if(is_array($category_specific_info[$key]['specifics'])){
                                foreach($category_specific_info[$key]['specifics'] as $keyName=>$specific){
                                    $category_specifics_to_merge[$keyName]['specific_info']=$specific;
                                }
                            }else{
                                //print("<pre>".print_r ('generate specific:3251',true )."</pre>");
                            //	$this->response->redirect($this->url->link('shopmanager/catalog/category/edit', 'token=' . $this->session->data['token'] . '&category_id=' . $category_id. '&product_id='.$product_id, true));
                            }
                    
                            
                        


                        // Vérification de l'existence de l'index $key dans $category_specific_info
                        if (is_array($ProductSpecifics[$key]['specifics']  )) {
                        
                            if(isset($ebay_specific_info) && is_array($ebay_specific_info) && $key==1){

                                        $json[1] = //$ebay_specific_info;
                                        $this->model_shopmanager_tools->custom_merge_recursive( $ProductSpecific['specifics'],$ebay_specific_info);
                            }

                        } else {
                            $this->load->model('shopmanager/ai');
                            $specifics_to_translate=$ProductSpecifics[1]['specifics'];
                            $specifics_to_translated=$this->model_shopmanager_ai->translate_specifics($product_id,$specifics_to_translate,['code'=>'Fr','language_id' => $key]);
                            $json[$key] =$specifics_to_translated;
                        //	$this->model_shopmanager_tools->custom_merge_recursive( , $category_specifics_to_merge);
                        }
                        $json[$key]=
                        $this->model_shopmanager_tools->custom_merge_recursive($ProductSpecifics[$key]['specifics'] , $category_specifics_to_merge);
                        // Affichage des détails spécifiques du produit après la fusion
                    //	//print("<pre>".print_r ($ProductSpecifics[$key]['specifics'] ,true )."</pre>");
                    }
                

                    
                }
            }
            
        //	$json=$ProductSpecifics;
        //	$json = stripslashes($json);
    //		//print("<pre>".print_r ($json ,true )."</pre>");
        }
    }
$this->response->addHeader('Content-Type: application/json');
$this->response->setOutput(json_encode($json));
}

  
public function process_search_field_fast_list($data) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$start_time = microtime(true); 
	$this->load->model('shopmanager/tools');

//	//print("<pre>" . print_r('2439:product.php', true) . "</pre>");
	//print("<pre>" . print_r(($data), true) . "</pre>");
	//print("<pre>" . print_r('2440:product.php', true) . "</pre>");
	$product_info_source=$data['product_info_source'];
    $this->load->language('shopmanager/product_search');
	$this->load->model('shopmanager/manufacturer');
	
	$data['entry_condition'] = $this->language->get('entry_condition');
	$data['entry_upc'] = $this->language->get('entry_upc');
	$data['entry_name'] = $this->language->get('entry_name');
	$data['entry_model'] = $this->language->get('entry_model');
	$data['entry_weight_class'] = $this->language->get('entry_weight_class');
	$data['entry_weight'] = $this->language->get('entry_weight');
	$data['entry_weight_oz'] = $this->language->get('entry_weight_oz');
	$data['entry_dimension'] = $this->language->get('entry_dimension');
	$data['entry_length_class'] = $this->language->get('entry_length_class');
	$data['entry_length'] = $this->language->get('entry_length');
	$data['entry_width'] = $this->language->get('entry_width');
	$data['entry_height'] = $this->language->get('entry_height');
	$data['button_search_by_upc'] = $this->language->get('button_search_by_upc');
	$data['button_save'] = $this->language->get('button_save');
	$data['button_feed'] = $this->language->get('button_feed');
	$data['button_cancel'] = $this->language->get('button_cancel');
	$data['button_add_specifics'] = $this->language->get('button_add_specifics');
	$data['button_search_by_name'] = $this->language->get('button_search_by_name');
	

	$data['placeholder_upc'] = $this->language->get('placeholder_upc');
	$data['text_no_data'] = $this->language->get('text_no_data');

	// Variables pour la vue
	$data['text_search'] = $this->language->get('text_search');
	$data['text_name'] = $this->language->get('text_name');
	$data['text_brand'] = $this->language->get('text_brand');
	$data['text_model'] = $this->language->get('text_model');
	$data['text_upc'] = $this->language->get('text_upc');
	$data['entry_category'] = $this->language->get('entry_category');
	$data['text_description_supp'] = $this->language->get('text_description_supp');
	$data['entry_image'] = $this->language->get('entry_image');
	$data['entry_external_images'] = $this->language->get('entry_external_images');
	$data['text_image_alt'] = $this->language->get('text_image_alt');
	$data['entry_main_image'] = $this->language->get('entry_main_image');
	$data['text_no_images'] = $this->language->get('text_no_images');
	$data['text_prices'] = $this->language->get('text_prices');
	$data['text_lowest_price'] = $this->language->get('text_lowest_price');
	$data['text_highest_price'] = $this->language->get('text_highest_price');
	$data['text_offers'] = $this->language->get('text_offers');
	$data['text_merchant'] = $this->language->get('text_merchant');
	$data['text_price'] = $this->language->get('text_price');
	$data['text_condition'] = $this->language->get('text_condition');
	$data['text_shipping'] = $this->language->get('text_shipping');
	$data['text_availability'] = $this->language->get('text_availability');
	$data['text_url'] = $this->language->get('text_url');
	$data['text_url_sold'] = $this->language->get('text_url_sold');
	
	$data['text_view_offer'] = $this->language->get('text_view_offer');
	$data['text_no_offers'] = $this->language->get('text_no_offers');
	$data['text_select'] = $this->language->get('text_select');
	$data['text_select_name'] = $this->language->get('text_select_name');
	$data['text_select_image'] = $this->language->get('text_select_image');

	$data['text_offer_details'] = $this->language->get('text_offer_details');
	$data['text_get_source'] = $this->language->get('text_get_source');
	$data['text_google_search'] = $this->language->get('text_google_search');
	$data['text_ebay_search'] = $this->language->get('text_ebay_search');
	$data['text_source'] = $this->language->get('text_source');

	$data['text_price_with_shipping'] = $this->language->get('text_price_with_shipping');
	$data['text_original_retail_price_with_shipping'] = $this->language->get('text_original_retail_price_with_shipping');

	$data['entry_category_id'] = $this->language->get('entry_category_id');
	$data['text_percent'] = $this->language->get('text_percent');
	$data['text_condition_name'] = $this->language->get('text_condition_name');
	$data['text_condition_id'] = $this->language->get('text_condition_id');

	$data['text_color'] = $this->language->get('text_color');
	$data['text_size'] = $this->language->get('text_size');
	$data['text_dimension'] = $this->language->get('text_dimension');
	$data['text_weight'] = $this->language->get('text_weight');
	$data['text_category_name'] = $this->language->get('text_category_name');

	$data['text_identifier_type'] = $this->language->get('text_identifier_type');
	$data['text_identifier_value'] = $this->language->get('text_identifier_value');
	$data['text_dimension_type'] = $this->language->get('text_dimension_type');
	$data['text_dimension_value'] = $this->language->get('text_dimension_value');
	$data['text_image_type'] = $this->language->get('text_image_type');
	$data['text_image_url'] = $this->language->get('text_image_url');
	$data['entry_specific_type'] = $this->language->get('entry_specific_type');
	$data['entry_specific_value'] = $this->language->get('entry_specific_value');
 
	$data['entry_category_id'] = $this->language->get('entry_category_id');
	$data['entry_category'] = $this->language->get('entry_category');
	$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
	$data['help_category'] = $this->language->get('help_category');

	$data['cancel'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] , true);
	if (isset($this->error['warning'])) {
		$data['error_warning'] = $this->error['warning'];
	} else {
		$data['error_warning'] = '';
	}
	if (isset($this->error)){
		$errors = ['location', 'keyword', 'model', 'manufacturer_id', 'height', 'width', 'length', 'weight', 'warning', 'category','shipping_cost'];

		foreach ($errors as $error) {
			$data['error_' . $error] = isset($this->error[$error]) ? $this->error[$error] : '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['manufacturer_id'])) {
			$data['error_manufacturer_id'] = $this->error['manufacturer_id'];
		} else {
			$data['error_manufacturer_id'] = array();
		}

		if (isset($this->error['location'])) {
			$data['error_location'] = $this->error['location'];
		} else {
			$data['error_location'] = array();
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = array();
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = array();
		}

		if (isset($this->error['length'])) {
			$data['error_length'] = $this->error['length'];
		} else {
			$data['error_length'] = array();
		}

		if (isset($this->error['weight'])) {
			$data['error_weight'] = $this->error['weight'];
		} else {
			$data['error_weight'] = array();
		}

		if (isset($this->error['shipping_cost'])) {
			$data['error_shipping_cost'] = $this->error['shipping_cost'];
		} else {
			$data['error_shipping_cost'] = array();
		}

	
	}

	if (isset($this->request->post['weight'])) {
		$data['weight'] = $this->request->post['weight'];
	} elseif (!empty($product_info)) {
		$data['weight'] = round($product_info['weight'],3);
	} else {
		$data['weight'] = 0;
	}

	
	if (isset($product_info['weight_class_id'])){
		$this->load->model('shopmanager/localisation/weight_class');
		$weight_class_info = $this->model_shopmanager_localisation_weight_class->getWeightClasses(array('weight_class_id' => $product_info['weight_class_id']));
	//	//print("<pre>".print_r ('1921',true )."</pre>");
	//	//print("<pre>".print_r ($product_info['weight_class_id'],true )."</pre>");
		$product_info['weight_class_title']=$weight_class_info[0]['unit'];
	}

	if (isset($this->request->post['weight_class_id'])) {
		$data['weight_class_id'] = $this->request->post['weight_class_id'];
	} elseif (!empty($product_info)) {
		$data['weight_class_id'] = $product_info['weight_class_id'];
	} else {
		$data['weight_class_id'] = $this->config->get('config_weight_class_id'); 
	}

	if (isset($this->request->post['weight_class_title'])) {
		$data['weight_class_title'] = $this->request->post['weight_class_title'];
	} elseif (!empty($product_info)) {
		$data['weight_class_title'] = $product_info['weight_class_title'];
	} else {
		$data['weight_class_title'] = 'Lbs';
	}

	if (isset($this->request->post['length'])) {
		$data['length'] = $this->request->post['length'];
	} elseif (!empty($product_info)) {
		$data['length'] = round($product_info['length'],1);
	} else {
		$data['length'] = 0;
	}

	if (isset($this->request->post['width'])) {
		$data['width'] = $this->request->post['width'];
	} elseif (!empty($product_info)) {
		$data['width'] = round($product_info['width'],1);
	} else {
		$data['width'] = 0;
	}

	if (isset($this->request->post['height'])) {
		$data['height'] = $this->request->post['height'];
	} elseif (!empty($product_info)) {
		$data['height'] = round($product_info['height'],1);
	} else {
		$data['height'] = 0;
	}

	if (isset($product_info['length_class_id'])){
		$this->load->model('shopmanager/localisation/length_class');

		$length_class_info = $this->model_shopmanager_localisation_length_class->getLengthClasses(array('length_class_id' => $product_info['length_class_id']));
		//print("<pre>".print_r ($length_class_info,true )."</pre>");
		$product_info['length_class_title']=$length_class_info[0]['unit'];
	}
	
	if (isset($this->request->post['length_class_id'])) {
		$data['length_class_id'] = $this->request->post['length_class_id'];
	} elseif (!empty($product_info)) {
		$data['length_class_id'] = $product_info['length_class_id'];
	} else {
		$data['length_class_id'] = $this->config->get('config_length_class_id');
	}

	if (isset($this->request->post['length_class_title'])) {
		$data['length_class_title'] = $this->request->post['length_class_title'];
	} elseif (!empty($product_info)) {
		$data['length_class_title'] = $product_info['length_class_title'];
	} else {
		$data['length_class_title'] = '';
	}

	
    $execution_times = [];
	$n=0;
    
    $url = '';
    $category_leaf=1;
		//$product_info_source = $this->model_shopmanager_product_search->manageProductInfoSources($upc);
		// 5. Récupérer les informations mises à jour de la table `product_info_sources`
		
		
		// Suivi du temps d'exécution après la gestion des informations de la source


		// 6. Récupérer les informations depuis la table si disponibles, sinon définir à `null`
		$upc_tmp_search = isset($product_info_source['upc_tmp_search']) ? json_decode($product_info_source['upc_tmp_search'], true) : null;

		$google_search = isset($product_info_source['google_search']) ? json_decode($product_info_source['google_search'], true) : null;

		$algopix_search = isset($product_info_source['algopix_search']) ? json_decode($product_info_source['algopix_search'], true) : null;
	

		$ebay_search = isset($product_info_source['ebay_search']) ? json_decode($product_info_source['ebay_search'], true) : null;
		$ebay_category = isset($product_info_source['ebay_category']) ? json_decode($product_info_source['ebay_category'], true) : null;

		$ebay_pricevariant = isset($product_info_source['ebay_pricevariant']) ? json_decode($product_info_source['ebay_pricevariant'], true) : null;
		$ebay_specific_info = isset($product_info_source['ebay_specific_info']) ? json_decode($product_info_source['ebay_specific_info'], true) : null;
		//print("<pre>" . print_r('3526:product.php', true) . "</pre>");
	//	//print("<pre>" . print_r($ebay_search, true) . "</pre>");
	//	//print("<pre>" . print_r($ebay_category, true) . "</pre>");
	//	//print("<pre>" . print_r($ebay_pricevariant, true) . "</pre>");
  
	//print("<pre>" . print_r('3529:product.php', true) . "</pre>");
    // 	//print("<pre>" . print_r($ebay_search, true) . "</pre>");
		
		$epid= isset($product_info_source['epid']) ? json_decode($product_info_source['epid'], true) : null;
		$epid_details = isset($product_info_source['epid_details']) ? json_decode($product_info_source['epid_details'], true) : null;
	

		if (isset($upc_tmp_search['category_name'])) { 
			$data['category_name'] = $upc_tmp_search['category_name']??null; 
		}

	//print("<pre>" . print_r($product_info_source, true) . "</pre>");
	//	//print("<pre>" . print_r($this->model_shopmanager_ebay->get($upc),true) . "</pre>");
														
	//	//print("<pre>" . print_r($this->model_shopmanager_ebay->findProductIDByGTIN($upc), true) . "</pre>");
		// 4. Récupérer ou rafraîchir les informations sur le produit en fonction de l'UPC
		
		//$this->model_shopmanager_ebay->getProductDetailsByepid('25046076135');
		// 7. Si l'API eBay n'a pas retourné de résultats et que nous avons un titre d'Algopix, tenter de récupérer via eBay à nouveau
	

		

		// 8. Stocker les résultats dans `$data` pour affichage ou traitement ultérieur
		$data['upc_tmp_search'] = $upc_tmp_search;
		$data['google_search'] = $google_search;
		$data['algopix_search'] = $algopix_search;
		$data['ebay_search'] = $ebay_search;
		$data['ebay_category'] = $ebay_category;
		$data['ebay_pricevariant'] = $ebay_pricevariant;
		$data['epid_details'] = $epid_details;
		$data['epid'] = $epid;
        $data['manageProductInfoSources'] = $product_info_source??'';
		
	
		// Afficher les temps d'exécution pour le débogage
	//	print_r($execution_times);
//print("<pre>" . print_r('Jo3560',true) . "</pre>");
//print("<pre>" . print_r($data,true) . "</pre>");




//$data['images'] = !empty($google_search) ? $this->model_shopmanager_product_search->processUniqueImages($google_search) : ['error' => 'No images found from the specified sites'];

// Ajout des informations Algopix et eBay




		if (isset($upc_tmp_search['error']) && isset($algopix_search['commonAttributes'] ['title'])) { 
			$upc_tmp_search = $this->model_shopmanager_upctmp->search($algopix_search['commonAttributes'] ['title']);
		}
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		//	//print("<pre>" . print_r($upc_tmp_search, true) . "</pre>");
		//	//print("<pre>" . print_r($algopix_search, true) . "</pre>");

		if(isset($upc_tmp_search['error']) && isset($algopix_search['error']) && isset($product_id)){
			$this->response->redirect($this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product_id . $url, true));
		}

		if (!empty($algopix_search['dimensions']['packageDimensions'])) { 
			$data['package_dimensions'] = $algopix_search['dimensions']['packageDimensions'];		
		}

		if (!empty($algopix_search['identifiers'])) { 
			$data['identifiers'] = $algopix_search['identifiers'];		
		}else{
			$data['identifiers'] =[];
		}



		// Gestion des conditions en fonction de la catégorie eBay
		//print("<pre>" . print_r($product_info['category_id'], true) . "</pre>");
		$category_id = $epid_details['primaryCategoryId'] ?? ($ebay_category[0]['category_id'] ?? $product_info['category_id'] ?? null);
			//print("<pre>" . print_r($ebay_category, true) . "</pre>");
		$data['category_id']  = $category_id;
		$data['category_name'] = $ebay_category[0]['category_name'] ?? $data['category_name'] ?? '';
		
		if (!isset($category_id) && !isset($data['category_name']) && isset($algopix_search['channelSpecificAttributes'] ['productType'])){
			$category_name = str_replace('_', ' ',$algopix_search['channelSpecificAttributes'] ['productType']);
			$category_info = $this->model_shopmanager_ai->getCategoryID($category_name);
			if(isset($category_info)){
				$category_id=trim($category_info['category_id'])??null;
				$data['category_id']  = $category_id;
				$data['category_name'] = $category_info['category_name'] ?? null;
				$data['ebay_category'][0]=$category_info;
				$data['ebay_category'][0]['percent']=100;
			}
			
		}
		// 
		if(empty($data['product_categories']) && isset($category_id) ){
			
				$category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
				//print("<pre>".print_r ($category_info,true )."</pre>");
				if ($category_info) {
					$data['site_id']=$category_info['site_id'];
					$site_id=$category_info['site_id'];
					$data['product_categories'][] = array(
						'category_id' => $category_info['category_id'],
						'site_id' => $category_info['site_id'],
						'name_category' => $category_info['name'],
						'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
						'leaf' => $category_info['leaf'],
					);
				/*	if( $category_info['leaf']=='1'){
						$category_specific=$category_info['category_id'];
					}*/
				}
			
		}
		//print("<pre>" . print_r('2838:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
	//$site_id=100;
	//print("<pre>" . print_r(value: '3160:PRODUCTSEARCH.php') . "</pre>");

			$conditions = $category_id 
				? $this->model_shopmanager_condition->getConditionDetails($category_id,null,null,$site_id) 
				: [];

			$data['conditions'] = $conditions[1] ?? [];
			//print("<pre>" . print_r('2845:product.php', true) . "</pre>");
			//print("<pre>" . print_r($conditions, true) . "</pre>");
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['images']  = $this->model_shopmanager_product_search->getAllImageUrls($upc_tmp_search??null, $google_search??null, $ebay_search??null, $algopix_search??null,$algopix_search_fr??null,$epid_details??null);
//print("<pre>" . print_r('3638:product.php', true) . "</pre>");
//	//print("<pre>" . print_r($product_info_source, true) . "</pre>");
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['titles']  = $this->model_shopmanager_product_search->getAllTitles($upc_tmp_search??null, $google_search??null, $ebay_search??null, $algopix_search??null,$algopix_search_fr??null,$epid_details??null);
			if(isset($data['title_search'])){
				$data['titles'] []=$data['title_search'];
			}
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
if(isset($data['manufacturer_id']) && $data['manufacturer_id']>0){

		
		$data['brand']=$data['manufacturer']??'';
		$data['product_info']['manufacturer']=$data['manufacturer']??'';

}else{
	$data['manufacturers'] = $this->model_shopmanager_product_search->getAllManufacturers($data)??null;
	if(isset($data['manufacturers'])){
		if(count($data['manufacturers'])==1){
		//print("<pre>" . print_r($data['manufacturers'], true) . "</pre>");
		//	$manufacturer_data = array(
		//		'filter_name' => reset($data['manufacturers']),
		//	);
		//print("<pre>" . print_r('3669:product.php', true) . "</pre>");
		//	//print("<pre>" . print_r($data['manufacturers'], true) . "</pre>");
			$findmanufacturer = $this->model_shopmanager_manufacturer->getManufacturerByName(reset($data['manufacturers']));
		//print("<pre>" . print_r('3669:product.php', true) . "</pre>");
		//print("<pre>" . print_r($findmanufacturer, true) . "</pre>");
			//$findmanufacturer = $this->model_shopmanager_manufacturer->getManufacturers($manufacturer_data);
			if(!isset($findmanufacturer)){
				$data_value = [
					'name' => reset($data['manufacturers']),  // Nom du fabricant
					'sort_order' => 1,              // Ordre de tri du fabricant
					'image' => '', // Chemin vers l'image du fabricant
					'manufacturer_store' => [0], // Liste des ID de magasin où le fabricant est affiché
					'keyword' => reset($data['manufacturers']) // Mot-clé SEO pour le fabricant
				];

				// Résultat final
				$manufacturer_result ['manufacturer_id'] = $this->model_shopmanager_manufacturer->addManufacturer($data_value);
				$manufacturer_result ['name'] = reset($data['manufacturers']);
	 //print("<pre>" . print_r('916:ai.php', true) . "</pre>");
   //print("<pre>" . print_r($data_value, true) . "</pre>");
	//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");

		//print("<pre>" . print_r($product_info['manufacturer_id'], true) . "</pre>");
		//print("<pre>" . print_r($manufacturers, true) . "</pre>");
				// Vous pouvez maintenant utiliser $result pour vos besoins
			}else{
				$manufacturer_result ['manufacturer_id'] =  $findmanufacturer['manufacturer_id'];
				$manufacturer_result ['name'] = $findmanufacturer['name'];
			}
//			//print("<pre>" . print_r('3650:product.php', true) . "</pre>");
//			//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");
		//	$manufacturer_result = $manufacturer_result[0];
		}else{
			
			$manufacturer_result=$this->model_shopmanager_ai->getManufacturer($data['manufacturers']);
//			//print("<pre>" . print_r('3715:product.php', true) . "</pre>");
	//		//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");
//			//print("<pre>" . print_r($data['manufacturers'], true) . "</pre>");
		}
		$data['manufacturer_id']=(isset($data['manufacturer_id']) && $data['manufacturer_id']>0)?$data['manufacturer_id']:$manufacturer_result['manufacturer_id']??0;
		$data['manufacturer']=$manufacturer_result['name']??'';
		$data['brand']=$manufacturer_result['name']??'';
		$data['product_info']['manufacturer']=$manufacturer_result['name']??'';
	}else{
	//print("<pre>" . print_r('3723:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
		$data['manufacturer_id']=$data['manufacturer_id']??0;
		$data['manufacturer']=$data['brand']??'';
		$data['brand']=$data['brand']??'';
	//print("<pre>" . print_r('3655:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
	}

}
			

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		
		//print("<pre>" . print_r('3650:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
		
		
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$product_description = isset($product_id)?$this->model_shopmanager_product->getProductDescriptions($product_id):null;

			// Vérifie si le nom en français (language_id = 1) est vide ou trop court
			$title_source = $product_description[1]['name'] ?? '';
			
			if (empty($title_source) || mb_strlen(trim($title_source)) < 5) {
				$title_result = $this->model_shopmanager_ai->getTitle($data['titles'], $category_id, $data);
	
				// Débogage si nécessaire
				// print("<pre>" . print_r('3670:product_search.php', true) . "</pre>");
				// print("<pre>" . print_r($title_result, true) . "</pre>");
	
				$data['title'] = $title_result['title'];
				$data['name_description'] = $title_result['title'];
				$data['short_title'] = $title_result['short_title'] ?? null;
			}else{
				$data['title'] =  $product_description[1]['name'];
				$data['name_description'] = $product_description[1]['name'];
				$data['short_title'] =  null;
	
			}		//print("<pre>" . print_r('3670:product_seach.php', true) . "</pre>");
		

			$data['upc']= $data['upc']??$product_info_source['upc']??$this->request->get['upc']??'';
		

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		//print("<pre>".print_r ('3719:product.php' ,true )."</pre>");
	//		//print("<pre>".print_r ($data ,true )."</pre>");
			if (isset($data['marketplace_item_id']) && ($data['marketplace_item_id'] > 0) && $category_leaf == 1) {
				$ebay_specific_info = $this->model_shopmanager_ebay->getProductSpecificsOLD($data['marketplace_item_id']);
				//print("<pre>" . print_r(('3687:product.php'), true) . "</pre>");
				//print("<pre>" . print_r(($ebay_specific_info), true) . "</pre>"); 
			}/*else{
				
	//	//print("<pre>" . print_r(('1622:product.php'), true) . "</pre>");
	//		//print("<pre>" . print_r(($ebay_search), true) . "</pre>"); 
            if(isset($ebay_search) && !isset($ebay_search[0]) && count($ebay_search)>0){ 
                $items_onearray=$ebay_search;
                unset($ebay_search);
                $ebay_search[]=$items_onearray;
            }
			// Vérifie que $ebay_search est bien un tableau avant array_slice
			if (!is_array($ebay_search)) {
				$ebay_search = []; // Initialise comme tableau vide si null
			}
			//	$ebay_search[]=$ebay_search[0];
			$ebay_search = array_slice($ebay_search, 0, length: 10);

			//echo '<br>Nombre de item pour ebay: ' . count($ebay_search);
			
			if (isset($ebay_search[0]) && !is_null($ebay_search[0])) {
				$ebay_specific_info = $this->model_shopmanager_ebay->getProductSpecificsSellers($ebay_search, $data['category_id']);
			} else {
				$ebay_specific_info = null; // Ou toute autre logique si nécessaire
			}
			//	$ebay_specific_info = isset($ebay_search)?$this->model_shopmanager_ebay->getProductSpecificsSellers($ebay_search,$data['category_id']):[]; 
		//		//print("<pre>" . print_r(('1627:product.php'), true) . "</pre>");
			//print("<pre>" . print_r(($ebay_specific_info), true) . "</pre>"); 
			}*/
			
			
			// Si les deux sont null, $mergeArrayForSpecifics reste un tableau vide
			$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($category_id,1);
			
	//		//print("<pre>" . print_r(('3699:product.php'), true) . "</pre>");
	//		//print("<pre>" . print_r(($category_specific_info), true) . "</pre>");
			$mergeArrayForSpecifics = array();

			// Si les deux variables ne sont pas null, faire array_merge_recursive

			// Si seulement $upc_tmp_search n'est pas null, l'utiliser comme le tableau fusionné
			if (!is_null($upc_tmp_search)) {
                $mergeArrayForSpecifics = array_merge_recursive($mergeArrayForSpecifics,$upc_tmp_search);
			}
			// Si seulement $algopix_search n'est pas null, l'utiliser comme le tableau fusionné
			if (!is_null($algopix_search)) {
                $mergeArrayForSpecifics = array_merge_recursive($mergeArrayForSpecifics, $algopix_search);
				
			}

            if (is_null($epid_details) && !is_null($ebay_specific_info)) {
				$data['ebay_specific_info'] =$ebay_specific_info;
				$mergeArrayForSpecifics = array_merge_recursive($mergeArrayForSpecifics, $this->model_shopmanager_ebay->formatActualDetails($ebay_specific_info));
				$data['epid_sources_json'] = '';
             //   $data['epid_sources_json'] =  json_encode($mergeArrayForSpecifics);
			}elseif(isset($epid_details)){ 
			//print("<pre>" . print_r(('3808:product.php'), true) . "</pre>");
		//print("<pre>" . print_r(($epid_details), true) . "</pre>");
				$ebay_sources = $this->model_shopmanager_ebay->formatEpidDetails($epid_details['aspects'],$category_specific_info);
				$data['epid_sources_json'] = json_encode($ebay_sources)??'';
				$mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources($ebay_sources,$mergeArrayForSpecifics);

			}else{
			//	$data['epid_sources_json'] =  'else';
                $data['epid_sources_json'] = '';
            }
		//print("<pre>" . print_r(('3716:product.php'), true) . "</pre>");
		//print("<pre>" . print_r(($data), true) . "</pre>");

		//	$mergeArrayForSpecifics['commonAttributes']['short_title']=$data['short_title']??$data['title']??$data['title_search'];
			$mergeArrayForSpecifics['commonAttributes']['title']=$data['title']??$data['title_']??$data['title_search'];
			$mergeArrayForSpecifics['commonAttributes']['manufacturer']=$data['manufacturer']??$upc_tmp_search['brand'];
			$mergeArrayForSpecifics['commonAttributes']['brand']=$data['manufacturer']??$upc_tmp_search['brand'];
			//$mergeArrayForSpecifics['upc']=$data['upc']??$upc_tmp_search['upc'];

			$mergeArrayForSpecifics['category_name'] = $data['category_name'];
			$mergeArrayForSpecifics['category_id']= $data['category_id'];
			$mergeArrayForSpecificsResult = $this->model_shopmanager_product_search->filterArrayForSpecifics($mergeArrayForSpecifics);
		
			unset($mergeArrayForSpecificsResult['error']);
			unset($mergeArrayForSpecificsResult['category_name']);
			unset($mergeArrayForSpecificsResult['category_id']);
			unset($mergeArrayForSpecificsResult['objectType']);
			unset($mergeArrayForSpecificsResult['itemClassification']);
			unset($mergeArrayForSpecificsResult['tradeInEligible']);

		//print("<pre>" . print_r(('3728:product.php'), true) . "</pre>");
		//print("<pre>" . print_r(($mergeArrayForSpecificsResult), true) . "</pre>");

			$data['specifics_result'] = 	$mergeArrayForSpecificsResult;

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			
			//print("<pre>" . print_r($category_id, true) . "</pre>");
			$category_specifics=$category_specific_info[1]['specifics']??[];
			$category_specific_key = [];
			$category_specific_names = [];

			foreach($category_specifics as $key => $specific) {
				$value = stripslashes($key);
				$category_specific_names[] = $value;
			}

			// Trier $category_specific_names par ordre alphabétique
			//sort($category_specific_names);

			$data['category_specific_names'] = $category_specific_names;

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			// Parcourir les clés de $mergeArrayForSpecificsResult
			foreach ($mergeArrayForSpecificsResult as $specific_key_name => $value) {
				//print("<pre>" . print_r($specific_key_name, true) . "</pre>");
				// Vérifier si la clé existe dans la base de données
				$replacement_term = $this->model_shopmanager_product_specific->getSpecificKey($specific_key_name, $category_id);

						// Si la clé existe déjà dans la base de données
				if ($replacement_term != 'not_set') {
					if ($replacement_term =='') {
						$key_set=0;
					}else{
						$key_set=1;
					}
				//	//print("<pre>" . print_r($key_set, true) . "</pre>"); 
					$category_specific_key[$specific_key_name] = [
					
						'replacement_term' => $replacement_term,     // Pas de suggestion nécessaire, donc on garde la clé originale
						'key_set' => $key_set                             // 0 car la clé existe déjà dans la base
					];
				} else {
					
					if ($replacement_term == 'not_set') {
					// Si la clé n'existe pas dans la base, obtenir un terme suggéré via la fonction getSpecificsKey()
						$suggest_replacement_term = $this->model_shopmanager_ai->getSpecificKey($specific_key_name, $category_specifics);
					//	//print("<pre>" . print_r($suggest_replacement_term, true) . "</pre>");
						if(isset($suggest_replacement_term)){
							$this->model_shopmanager_product_specific->addSpecificKey($specific_key_name, $category_id, $suggest_replacement_term);
							unset($category_specifics[$suggest_replacement_term]);
							$key_set=2;
						}else{
							$this->model_shopmanager_product_specific->addSpecificKey($specific_key_name, $category_id, '');
							$key_set=0;
						}
					}else{
						$suggest_replacement_term='';
						$key_set=0;
					}
					// Ajouter l'entrée au tableau $category_specific_key
					$category_specific_key[$specific_key_name] = [
						
						'replacement_term' => $suggest_replacement_term??'',      // Terme suggéré
						'key_set' => $key_set                             // 1 car la clé doit être ajoutée avec le terme suggéré
					];
					
				}
			

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['category_specific_key']= $category_specific_key;
			$data['data_result']=$data;
		//	//print("<pre>" . print_r('3115:product.php', true) . "</pre>");
		//	//print("<pre>" . print_r($data['manageProductInfoSources'], true) . "</pre>");
		//	//print("<pre>" . print_r($data, true) . "</pre>");
			//print("<pre>".print_r ($execution_times ,true )."</pre>");
			$data['total_execution_time'] = array_sum($execution_times);
            
		//	//echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
		if (isset($data['manageProductInfoSources']) && is_array($data['manageProductInfoSources']) && count($data['manageProductInfoSources']) > 0) {
			if(isset($data['view'])){
				if(!isset($data['condition_marketplace_item_id'] )){
					//print("<pre>" . print_r(value: '3462:PRODUCTSEARCH.php') . "</pre>");

					$data['conditions_marketplace_item_id'] =  $this->model_shopmanager_condition->getConditionDetails($category_id,$data['condition_id'],null,$data['site_id']??0);
					$data['condition_marketplace_item_id'] =  $data['conditions_marketplace_item_id'][1][$data['condition_id']]['condition_marketplace_item_id'];
				}
				$data['save_action'] = $this->url->link('shopmanager/product_search/product_feed', 'token=' . $this->session->data['token'], true);

				return $this->load->view('shopmanager/product_search_'.$data['view'], $data);
			}else{
				return $this->load->view('shopmanager/product_search_form', $data);
			}
			
		} else {
			return $this->load->view('shopmanager/product_search_form', $data);
		}
        
}
}
   
public function process_search_field($data) {
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$start_time = microtime(true); 
	$this->load->model('shopmanager/tools');

//	//print("<pre>" . print_r('2439:product.php', true) . "</pre>");
	//print("<pre>" . print_r(($data), true) . "</pre>");
	//print("<pre>" . print_r('2440:product.php', true) . "</pre>");
	$product_info_source=$data['product_info_source'];
    $this->load->language('shopmanager/product_search');
	$this->load->model('shopmanager/manufacturer');
	$this->load->model('shopmanager/localisation/country');

	$data['countries'] = $this->model_shopmanager_localisation_country->getCountries(array('sort'=>'name'));

	$data['entry_condition'] = $this->language->get('entry_condition');
	$data['entry_upc'] = $this->language->get('entry_upc');
	$data['entry_name'] = $this->language->get('entry_name');
	$data['entry_model'] = $this->language->get('entry_model');
	$data['entry_weight_class'] = $this->language->get('entry_weight_class');
	$data['entry_weight'] = $this->language->get('entry_weight');
	$data['entry_weight_oz'] = $this->language->get('entry_weight_oz');
	$data['entry_dimension'] = $this->language->get('entry_dimension');
	$data['entry_length_class'] = $this->language->get('entry_length_class');
	$data['entry_length'] = $this->language->get('entry_length');
	$data['entry_width'] = $this->language->get('entry_width');
	$data['entry_height'] = $this->language->get('entry_height');
	$data['button_search_by_upc'] = $this->language->get('button_search_by_upc');
	$data['button_save'] = $this->language->get('button_save');
	$data['button_feed'] = $this->language->get('button_feed');
	$data['button_cancel'] = $this->language->get('button_cancel');
	$data['button_add_specifics'] = $this->language->get('button_add_specifics');
	$data['button_search_by_name'] = $this->language->get('button_search_by_name');
	

	$data['placeholder_upc'] = $this->language->get('placeholder_upc');
	$data['text_no_data'] = $this->language->get('text_no_data');

	// Variables pour la vue
	$data['text_search'] = $this->language->get('text_search');
	$data['text_name'] = $this->language->get('text_name');
	$data['text_brand'] = $this->language->get('text_brand');
	$data['text_model'] = $this->language->get('text_model');
	$data['text_upc'] = $this->language->get('text_upc');
	$data['text_category'] = $this->language->get('text_category');
	$data['text_category_id'] = $this->language->get('text_category_id');
	$data['text_specific_type'] = $this->language->get('text_specific_type');
	$data['text_specific_value'] = $this->language->get('text_specific_value');
	$data['text_images'] = $this->language->get('text_images');

	$data['entry_category'] = $this->language->get('entry_category');
	$data['text_description_supp'] = $this->language->get('text_description_supp');
	$data['entry_image'] = $this->language->get('entry_image');
	$data['entry_external_images'] = $this->language->get('entry_external_images');
	$data['text_image_alt'] = $this->language->get('text_image_alt');
	$data['entry_main_image'] = $this->language->get('entry_main_image');
	$data['text_no_images'] = $this->language->get('text_no_images');
	$data['text_prices'] = $this->language->get('text_prices');
	$data['text_lowest_price'] = $this->language->get('text_lowest_price');
	$data['text_highest_price'] = $this->language->get('text_highest_price');
	$data['text_offers'] = $this->language->get('text_offers');
	$data['text_merchant'] = $this->language->get('text_merchant');
	$data['text_price'] = $this->language->get('text_price');
	$data['text_condition'] = $this->language->get('text_condition');
	$data['text_shipping'] = $this->language->get('text_shipping');
	$data['text_availability'] = $this->language->get('text_availability');
	$data['text_url'] = $this->language->get('text_url');
	$data['text_url_sold'] = $this->language->get('text_url_sold');
	
	$data['text_view_offer'] = $this->language->get('text_view_offer');
	$data['text_no_offers'] = $this->language->get('text_no_offers');
	$data['text_select'] = $this->language->get('text_select');
	$data['text_select_name'] = $this->language->get('text_select_name');
	$data['text_select_image'] = $this->language->get('text_select_image');

	$data['text_offer_details'] = $this->language->get('text_offer_details');
	$data['text_get_source'] = $this->language->get('text_get_source');
	$data['text_google_search'] = $this->language->get('text_google_search');
	$data['text_ebay_search'] = $this->language->get('text_ebay_search');
	$data['text_source'] = $this->language->get('text_source');

	$data['text_price_with_shipping'] = $this->language->get('text_price_with_shipping');
	$data['text_original_retail_price_with_shipping'] = $this->language->get('text_original_retail_price_with_shipping');

	$data['entry_category_id'] = $this->language->get('entry_category_id');
	$data['text_percent'] = $this->language->get('text_percent');
	$data['text_condition_name'] = $this->language->get('text_condition_name');
	$data['text_condition_id'] = $this->language->get('text_condition_id');

	$data['text_color'] = $this->language->get('text_color');
	$data['text_size'] = $this->language->get('text_size');
	$data['text_dimension'] = $this->language->get('text_dimension');
	$data['text_weight'] = $this->language->get('text_weight');
	$data['text_category_name'] = $this->language->get('text_category_name');

	$data['text_identifier_type'] = $this->language->get('text_identifier_type');
	$data['text_identifier_value'] = $this->language->get('text_identifier_value');
	$data['text_dimension_type'] = $this->language->get('text_dimension_type');
	$data['text_dimension_value'] = $this->language->get('text_dimension_value');
	$data['text_image_type'] = $this->language->get('text_image_type');
	$data['text_image_url'] = $this->language->get('text_image_url');
	$data['entry_specific_type'] = $this->language->get('entry_specific_type');
	$data['entry_specific_value'] = $this->language->get('entry_specific_value');
 
	$data['entry_category_id'] = $this->language->get('entry_category_id');
	$data['entry_category'] = $this->language->get('entry_category');
	$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
	$data['entry_price'] = $this->language->get('entry_price');
	$data['entry_price_with_shipping'] = $this->language->get('entry_price_with_shipping');
	$data['entry_unallocated_quantity'] = $this->language->get('entry_unallocated_quantity');
	$data['entry_quantity'] = $this->language->get('entry_quantity');
	$data['help_category'] = $this->language->get('help_category');

	// Champs de formulaire
$data['entry_made_in_country'] = $this->language->get('entry_made_in_country');
$data['entry_shipping_cost'] = $this->language->get('entry_shipping_cost');


	$data['cancel'] = $this->url->link('shopmanager/product', 'token=' . $this->session->data['token'] , true);
	if (isset($this->error['warning'])) {
		$data['error_warning'] = $this->error['warning'];
	} else {
		$data['error_warning'] = '';
	}
	if (isset($this->error)){
		$errors = ['location', 'keyword', 'model', 'manufacturer_id', 'height', 'width', 'length', 'weight', 'warning', 'category','shipping_cost'];

		foreach ($errors as $error) {
			$data['error_' . $error] = isset($this->error[$error]) ? $this->error[$error] : '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['meta_title'])) {
			$data['error_meta_title'] = $this->error['meta_title'];
		} else {
			$data['error_meta_title'] = array();
		}

		if (isset($this->error['manufacturer_id'])) {
			$data['error_manufacturer_id'] = $this->error['manufacturer_id'];
		} else {
			$data['error_manufacturer_id'] = array();
		}

		if (isset($this->error['location'])) {
			$data['error_location'] = $this->error['location'];
		} else {
			$data['error_location'] = array();
		}

		if (isset($this->error['height'])) {
			$data['error_height'] = $this->error['height'];
		} else {
			$data['error_height'] = array();
		}

		if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
		} else {
			$data['error_width'] = array();
		}

		if (isset($this->error['length'])) {
			$data['error_length'] = $this->error['length'];
		} else {
			$data['error_length'] = array();
		}

		if (isset($this->error['weight'])) {
			$data['error_weight'] = $this->error['weight'];
		} else {
			$data['error_weight'] = array();
		}

		if (isset($this->error['shipping_cost'])) {
			$data['error_shipping_cost'] = $this->error['shipping_cost'];
		} else {
			$data['error_shipping_cost'] = array();
		}
		if (isset($this->error['made_in_country_id'])) {
			$data['error_made_in_country_id'] = $this->error['made_in_country_id'];
		} else {
			$data['error_made_in_country_id'] = array();
		}
	
	}
	// Variables de formulaire

	if (isset($this->request->post['made_in_country_id'])) {
		$data['made_in_country_id'] = $this->request->post['made_in_country_id'];
	} elseif (!empty($product_info)) {
		$data['made_in_country_id'] = $product_info['made_in_country_id'];
	} else {
		$data['made_in_country_id'] = 0;
	}

	if (isset($this->request->post['price'])) {
		$data['price'] = $this->request->post['price'];
	} elseif (!empty($product_info)) {
		$data['price'] = round($product_info['price'],2);
	} else {
		$data['price'] = 0;
	}
	if (isset($this->request->post['price_with_shipping'])) {
		$data['price_with_shipping'] = round($this->request->post['price_with_shipping'],2);
	} elseif (!empty($product_info)) {
		$data['price_with_shipping'] = round($product_info['price_with_shipping'],2);
	} else {
		$data['price_with_shipping'] = 0;
	}

	if (isset($this->request->post['shipping_cost'])) {
		$data['shipping_cost'] = is_numeric($this->request->post['shipping_cost'])? round($this->request->post['shipping_cost'],2):0;
	} elseif (!empty($product_info)) {
		
		if($product_info['shipping_cost']==0 || !isset($product_info['shipping_cost'])){
		
			$result=$this->model_shopmanager_shipping->calculateShippingRates($product_info);
			$data['shipping_cost']=$result['shipping_cost'];
			$data['shipping_carrier']=$result['shipping_carrier'];
			$data['price_with_shipping']=$data['price']+$result['shipping_cost'];

		}else{
			$data['shipping_cost'] = round($product_info['shipping_cost'],2);
		}
	} else {
		$data['shipping_cost'] = 0;
	}

	if (isset($this->request->post['shipping_carrier'])) {
		$data['shipping_carrier'] = $this->request->post['shipping_carrier'];
	} elseif (!empty($product_info)) {
		$data['shipping_carrier'] = $product_info['shipping_carrier'];
	} else {
		$data['shipping_carrier'] = '';
	}
	if (isset($this->request->post['weight'])) {
		$data['weight'] = $this->request->post['weight'];
	} elseif (!empty($product_info)) {
		$data['weight'] = round($product_info['weight'],3);
	} else {
		$data['weight'] = 0;
	}

	
	if (isset($product_info['weight_class_id'])){
		$this->load->model('shopmanager/localisation/weight_class');
		$weight_class_info = $this->model_shopmanager_localisation_weight_class->getWeightClasses(array('weight_class_id' => $product_info['weight_class_id']));
	//	//print("<pre>".print_r ('1921',true )."</pre>");
	//	//print("<pre>".print_r ($product_info['weight_class_id'],true )."</pre>");
		$product_info['weight_class_title']=$weight_class_info[0]['unit'];
	}

	if (isset($this->request->post['weight_class_id'])) {
		$data['weight_class_id'] = $this->request->post['weight_class_id'];
	} elseif (!empty($product_info)) {
		$data['weight_class_id'] = $product_info['weight_class_id'];
	} else {
		$data['weight_class_id'] = $this->config->get('config_weight_class_id'); 
	}

	if (isset($this->request->post['weight_class_title'])) {
		$data['weight_class_title'] = $this->request->post['weight_class_title'];
	} elseif (!empty($product_info)) {
		$data['weight_class_title'] = $product_info['weight_class_title'];
	} else {
		$data['weight_class_title'] = 'Lbs';
	}

	if (isset($this->request->post['length'])) {
		$data['length'] = $this->request->post['length'];
	} elseif (!empty($product_info)) {
		$data['length'] = round($product_info['length'],1);
	} else {
		$data['length'] = 0;
	}

	if (isset($this->request->post['width'])) {
		$data['width'] = $this->request->post['width'];
	} elseif (!empty($product_info)) {
		$data['width'] = round($product_info['width'],1);
	} else {
		$data['width'] = 0;
	}

	if (isset($this->request->post['height'])) {
		$data['height'] = $this->request->post['height'];
	} elseif (!empty($product_info)) {
		$data['height'] = round($product_info['height'],1);
	} else {
		$data['height'] = 0;
	}

	if (isset($product_info['length_class_id'])){
		$this->load->model('shopmanager/localisation/length_class');

		$length_class_info = $this->model_shopmanager_localisation_length_class->getLengthClasses(array('length_class_id' => $product_info['length_class_id']));
		//print("<pre>".print_r ($length_class_info,true )."</pre>");
		$product_info['length_class_title']=$length_class_info[0]['unit'];
	}
	
	if (isset($this->request->post['length_class_id'])) {
		$data['length_class_id'] = $this->request->post['length_class_id'];
	} elseif (!empty($product_info)) {
		$data['length_class_id'] = $product_info['length_class_id'];
	} else {
		$data['length_class_id'] = $this->config->get('config_length_class_id');
	}

	if (isset($this->request->post['length_class_title'])) {
		$data['length_class_title'] = $this->request->post['length_class_title'];
	} elseif (!empty($product_info)) {
		$data['length_class_title'] = $product_info['length_class_title'];
	} else {
		$data['length_class_title'] = '';
	}

    $execution_times = [];
	$n=0;
    
    $url = '';
    $category_leaf=1;
		//$product_info_source = $this->model_shopmanager_product_search->manageProductInfoSources($upc);
		// 5. Récupérer les informations mises à jour de la table `product_info_sources`
		
		
		// Suivi du temps d'exécution après la gestion des informations de la source


		// 6. Récupérer les informations depuis la table si disponibles, sinon définir à `null`
		$upc_tmp_search = isset($product_info_source['upc_tmp_search']) ? json_decode($product_info_source['upc_tmp_search'], true) : null;

		$google_search = isset($product_info_source['google_search']) ? json_decode($product_info_source['google_search'], true) : null;

		$algopix_search = isset($product_info_source['algopix_search']) ? json_decode($product_info_source['algopix_search'], true) : null;
	

		$ebay_search = isset($product_info_source['ebay_search']) ? json_decode($product_info_source['ebay_search'], true) : null;
		$ebay_category = isset($product_info_source['ebay_category']) ? json_decode($product_info_source['ebay_category'], true) : null;
		$product_info_source['ebay_pricevariant'] = json_encode($this->model_shopmanager_ebay->calculateMissingPrices(json_decode($product_info_source['ebay_pricevariant'],true)));
		$ebay_pricevariant = isset($product_info_source['ebay_pricevariant']) ? json_decode($product_info_source['ebay_pricevariant'], true) : null;
		$ebay_specific_info = isset($product_info_source['ebay_specific_info']) ? json_decode($product_info_source['ebay_specific_info'], true) : null;
	
		
		$epid= isset($product_info_source['epid']) ? json_decode($product_info_source['epid'], true) : null;
		$epid_details = isset($product_info_source['epid_details']) ? json_decode($product_info_source['epid_details'], true) : null;
	

		if (isset($upc_tmp_search['category_name'])) { 
			$data['category_name'] = $upc_tmp_search['category_name']??null; 
		}

	

		// 8. Stocker les résultats dans `$data` pour affichage ou traitement ultérieur
		$data['upc_tmp_search'] = $upc_tmp_search;
		$data['google_search'] = $google_search;
		$data['algopix_search'] = $algopix_search;
		$data['ebay_search'] = $ebay_search;
		$data['ebay_category'] = $ebay_category;
		$data['ebay_pricevariant'] = $ebay_pricevariant;
		$data['epid_details'] = $epid_details;
		$data['epid'] = $epid;
        $data['manageProductInfoSources'] = $product_info_source??'';
		
	
		// Afficher les temps d'exécution pour le débogage
	//	print_r($execution_times);

//print("<pre>" . print_r($data,true) . "</pre>");




//$data['images'] = !empty($google_search) ? $this->model_shopmanager_product_search->processUniqueImages($google_search) : ['error' => 'No images found from the specified sites'];

// Ajout des informations Algopix et eBay




		if (isset($upc_tmp_search['error']) && isset($algopix_search['commonAttributes'] ['title'])) { 
			$upc_tmp_search = $this->model_shopmanager_upctmp->search($algopix_search['commonAttributes'] ['title']);
		}
		//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
		//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		//	//print("<pre>" . print_r($upc_tmp_search, true) . "</pre>");
		//	//print("<pre>" . print_r($algopix_search, true) . "</pre>");

		if(isset($upc_tmp_search['error']) && isset($algopix_search['error']) && isset($product_id)){
			$this->response->redirect($this->url->link('shopmanager/product/edit', 'token=' . $this->session->data['token'] . '&product_id=' . $product_id . $url, true));
		}

		if (!empty($algopix_search['dimensions']['packageDimensions'])) { 
			$data['package_dimensions'] = $algopix_search['dimensions']['packageDimensions'];		
		}

		if (!empty($algopix_search['identifiers'])) { 
			$data['identifiers'] = $algopix_search['identifiers'];		
		}else{
			$data['identifiers'] =[];
		}



		// Gestion des conditions en fonction de la catégorie eBay
		//print("<pre>" . print_r($product_info['category_id'], true) . "</pre>");
		//$category_id = $epid_details['primaryCategoryId'] ?? ($ebay_category[0]['category_id'] ?? $product_info['category_id'] ?? null);
			//print("<pre>" . print_r($ebay_category, true) . "</pre>");
		//$data['category_id']  = $category_id;
		//$data['category_name'] = $ebay_category[0]['category_name'] ?? $data['category_name'] ?? '';
		
		if (!isset($data['category_id']) && !isset($data['category_name']) && isset($algopix_search['channelSpecificAttributes'] ['productType'])){
			$category_name = str_replace('_', ' ',$algopix_search['channelSpecificAttributes'] ['productType']);
			$category_info = $this->model_shopmanager_ai->getCategoryID($category_name);
			if(isset($category_info)){
				$data['category_id']=trim($category_info['category_id'])??null;
				//$data['category_id']  = $data['category_id'];
				$data['category_name'] = $category_info['category_name'] ?? null;
				$data['ebay_category'][0]=$category_info;
				$data['ebay_category'][0]['percent']=100;
			}
			
		}
	
		if(empty($data['product_categories']) && isset($data['category_id']) ){
			
				$category_info = $this->model_shopmanager_catalog_category->getCategory($data['category_id']);
				//print("<pre>".print_r ($category_info,true )."</pre>");
				if ($category_info) {
					$data['site_id']=$category_info['site_id'];
					$site_id=$category_info['site_id'];
					$data['product_categories'][] = array(
						'category_id' => $category_info['category_id'],
						'site_id' => $category_info['site_id'],
						'name_category' => $category_info['name'],
						'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name'],
						'leaf' => $category_info['leaf'],
					);
				/*	if( $category_info['leaf']=='1'){
						$category_specific=$category_info['category_id'];
					}*/
				}
			
		}
		//print("<pre>" . print_r('2838:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
	//$site_id=100;
			$conditions = $data['category_id'] 
				? $this->model_shopmanager_condition->getConditionDetails($data['category_id'],null,null,$site_id) 
				: [];

			$data['conditions'] = $conditions[1] ?? [];
			//print("<pre>" . print_r('2845:product.php', true) . "</pre>");
			//print("<pre>" . print_r($conditions, true) . "</pre>");
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['images']  = $this->model_shopmanager_product_search->getAllImageUrls($upc_tmp_search??null, $google_search??null, $ebay_search??null, $algopix_search??null,$algopix_search_fr??null,$epid_details??null);
//print("<pre>" . print_r('3638:product.php', true) . "</pre>");
//	//print("<pre>" . print_r($product_info_source, true) . "</pre>");
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['titles']  = $this->model_shopmanager_product_search->getAllTitles($upc_tmp_search??null, $google_search??null, $ebay_search??null, $algopix_search??null,$algopix_search_fr??null,$epid_details??null);
			if(isset($data['title_search'])){
				$data['titles'] []=$data['title_search'];
			}
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		
if(isset($data['manufacturer_id']) && $data['manufacturer_id']>0){
	
		
		$data['brand']=$data['manufacturer']??'';
		$data['product_info']['manufacturer']=$data['manufacturer']??'';

}else{
	$data['manufacturers'] = $this->model_shopmanager_product_search->getAllManufacturers($data)??null;
	if(isset($data['manufacturers'])){
		if(count($data['manufacturers'])==1){
		//print("<pre>" . print_r($data['manufacturers'], true) . "</pre>");
		//	$manufacturer_data = array(
		//		'filter_name' => reset($data['manufacturers']),
		//	);
		//print("<pre>" . print_r('3669:product.php', true) . "</pre>");
		//	//print("<pre>" . print_r($data['manufacturers'], true) . "</pre>");
			$findmanufacturer = $this->model_shopmanager_manufacturer->getManufacturerByName(reset($data['manufacturers']));
		//print("<pre>" . print_r('3669:product.php', true) . "</pre>");
		//print("<pre>" . print_r($findmanufacturer, true) . "</pre>");
			//$findmanufacturer = $this->model_shopmanager_manufacturer->getManufacturers($manufacturer_data);
			if(!isset($findmanufacturer)){
				$data_value = [
					'name' => reset($data['manufacturers']),  // Nom du fabricant
					'sort_order' => 1,              // Ordre de tri du fabricant
					'image' => '', // Chemin vers l'image du fabricant
					'manufacturer_store' => [0], // Liste des ID de magasin où le fabricant est affiché
					'keyword' => reset($data['manufacturers']) // Mot-clé SEO pour le fabricant
				];

				// Résultat final
				$manufacturer_result ['manufacturer_id'] = $this->model_shopmanager_manufacturer->addManufacturer($data_value);
				$manufacturer_result ['name'] = reset($data['manufacturers']);
	 //print("<pre>" . print_r('916:ai.php', true) . "</pre>");
   //print("<pre>" . print_r($data_value, true) . "</pre>");
	//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");

		//print("<pre>" . print_r($product_info['manufacturer_id'], true) . "</pre>");
		//print("<pre>" . print_r($manufacturers, true) . "</pre>");
				// Vous pouvez maintenant utiliser $result pour vos besoins
			}else{
				$manufacturer_result ['manufacturer_id'] =  $findmanufacturer['manufacturer_id'];
				$manufacturer_result ['name'] = $findmanufacturer['name'];
			}
//			//print("<pre>" . print_r('3650:product.php', true) . "</pre>");
//			//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");
		//	$manufacturer_result = $manufacturer_result[0];
		}else{
			
			$manufacturer_result=$this->model_shopmanager_ai->getManufacturer($data['manufacturers']);
//			//print("<pre>" . print_r('3715:product.php', true) . "</pre>");
	//		//print("<pre>" . print_r($manufacturer_result, true) . "</pre>");
//			//print("<pre>" . print_r($data['manufacturers'], true) . "</pre>");
		}
		$data['manufacturer_id']=(isset($data['manufacturer_id']) && $data['manufacturer_id']>0)?$data['manufacturer_id']:$manufacturer_result['manufacturer_id']??0;
		$data['manufacturer']=$manufacturer_result['name']??'';
		$data['brand']=$manufacturer_result['name']??'';
		$data['product_info']['manufacturer']=$manufacturer_result['name']??'';
	}else{
	//print("<pre>" . print_r('3723:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
		$data['manufacturer_id']=$data['manufacturer_id']??0;
		$data['manufacturer']=$data['brand']??'';
		$data['brand']=$data['brand']??'';
		
		
	//print("<pre>" . print_r('3655:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
	}

}
			

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		
		//print("<pre>" . print_r('3650:product.php', true) . "</pre>");
	//print("<pre>" . print_r($data, true) . "</pre>");
		
		
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

		$product_description = isset($product_id)?$this->model_shopmanager_product->getProductDescriptions($product_id):null;

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
			$data['short_title'] = $title_result['short_title'] ?? null;
		}else{
			$data['title'] =  $product_description[1]['name'];
			$data['name_description'] = $product_description[1]['name'];
			$data['short_title'] =  null;

		}
			$data['upc']= $data['upc']??$product_info_source['upc']??$this->request->get['upc']??'';
		
		
			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 
		
		//print("<pre>".print_r ('3719:product.php' ,true )."</pre>");
	//		//print("<pre>".print_r ($data ,true )."</pre>");
			if (isset($data['marketplace_item_id']) && ($data['marketplace_item_id'] > 0) && $category_leaf == 1) {
				$ebay_specific_info = $this->model_shopmanager_ebay->getProductSpecificsOLD($data['marketplace_item_id']);
				//print("<pre>" . print_r(('3687:product.php'), true) . "</pre>");
				//print("<pre>" . print_r(($ebay_specific_info), true) . "</pre>"); 
			}
			//print("<pre>" . print_r('4051',true) . "</pre>");
			//print("<pre>" . print_r($data, true) . "</pre>"); 
			/*else{
				
	//	//print("<pre>" . print_r(('1622:product.php'), true) . "</pre>");
	//		//print("<pre>" . print_r(($ebay_search), true) . "</pre>"); 
            if(isset($ebay_search) && !isset($ebay_search[0]) && count($ebay_search)>0){ 
                $items_onearray=$ebay_search;
                unset($ebay_search);
                $ebay_search[]=$items_onearray;
            }
			// Vérifie que $ebay_search est bien un tableau avant array_slice
			if (!is_array($ebay_search)) {
				$ebay_search = []; // Initialise comme tableau vide si null
			}
			//	$ebay_search[]=$ebay_search[0];
			$ebay_search = array_slice($ebay_search, 0, length: 10);

			//echo '<br>Nombre de item pour ebay: ' . count($ebay_search);
			
			if (isset($ebay_search[0]) && !is_null($ebay_search[0])) {
				$ebay_specific_info = $this->model_shopmanager_ebay->getProductSpecificsSellers($ebay_search, $data['category_id']);
			} else {
				$ebay_specific_info = null; // Ou toute autre logique si nécessaire
			}
			//	$ebay_specific_info = isset($ebay_search)?$this->model_shopmanager_ebay->getProductSpecificsSellers($ebay_search,$data['category_id']):[]; 
		//		//print("<pre>" . print_r(('1627:product.php'), true) . "</pre>");
			//print("<pre>" . print_r(($ebay_specific_info), true) . "</pre>"); 
			}*/
			
			
			// Si les deux sont null, $mergeArrayForSpecifics reste un tableau vide
			$category_specific_info = $this->model_shopmanager_catalog_category->getSpecific($data['category_id'],1);
			
	//		//print("<pre>" . print_r(('3699:product.php'), true) . "</pre>");
	//		//print("<pre>" . print_r(($category_specific_info), true) . "</pre>");
			$mergeArrayForSpecifics = array();

			// Si les deux variables ne sont pas null, faire array_merge_recursive

			// Si seulement $upc_tmp_search n'est pas null, l'utiliser comme le tableau fusionné
			if (!is_null($upc_tmp_search)) {
                $mergeArrayForSpecifics = array_merge_recursive($mergeArrayForSpecifics,$upc_tmp_search);
			}
			// Si seulement $algopix_search n'est pas null, l'utiliser comme le tableau fusionné
			if (!is_null($algopix_search)) {
                $mergeArrayForSpecifics = array_merge_recursive($mergeArrayForSpecifics, $algopix_search);
				
			}

            if (is_null($epid_details) && !is_null($ebay_specific_info)) {
				$data['ebay_specific_info'] =$ebay_specific_info;
				$mergeArrayForSpecifics = array_merge_recursive($mergeArrayForSpecifics, $this->model_shopmanager_ebay->formatActualDetails($ebay_specific_info));
				$data['epid_sources_json'] = '';
             //   $data['epid_sources_json'] =  json_encode($mergeArrayForSpecifics);
			}elseif(isset($epid_details)){ 
			//print("<pre>" . print_r(('3808:product.php'), true) . "</pre>");
		//print("<pre>" . print_r(($epid_details), true) . "</pre>");
				$ebay_sources = $this->model_shopmanager_ebay->formatEpidDetails($epid_details['aspects'],$category_specific_info);
				$data['epid_sources_json'] = json_encode($ebay_sources)??'';
				$mergeArrayForSpecifics = $this->model_shopmanager_tools->compareSources($ebay_sources,$mergeArrayForSpecifics);

			}else{
			//	$data['epid_sources_json'] =  'else';
                $data['epid_sources_json'] = '';
            }
		//print("<pre>" . print_r(('3716:product.php'), true) . "</pre>");
		//print("<pre>" . print_r(($data), true) . "</pre>");

		//	$mergeArrayForSpecifics['commonAttributes']['short_title']=$data['short_title']??$data['title']??$data['title_search'];
			$mergeArrayForSpecifics['commonAttributes']['title']=$data['title']??$data['title_']??$data['title_search'];
			$mergeArrayForSpecifics['commonAttributes']['manufacturer']=$data['manufacturer']??$upc_tmp_search['brand'];
			//$mergeArrayForSpecifics['commonAttributes']['brand']=$data['manufacturer']??$upc_tmp_search['brand'];
			//$mergeArrayForSpecifics['upc']=$data['upc']??$upc_tmp_search['upc'];

			$mergeArrayForSpecifics['category_name'] = $data['category_name'];
			$mergeArrayForSpecifics['category_id']= $data['category_id'];
			$mergeArrayForSpecificsResult = $this->model_shopmanager_product_search->filterArrayForSpecifics($mergeArrayForSpecifics);
		
			unset($mergeArrayForSpecificsResult['error']);
			unset($mergeArrayForSpecificsResult['category_name']);
			unset($mergeArrayForSpecificsResult['category_id']);
			unset($mergeArrayForSpecificsResult['objectType']);
			unset($mergeArrayForSpecificsResult['itemClassification']);
			unset($mergeArrayForSpecificsResult['tradeInEligible']);

		//print("<pre>" . print_r(('3728:product.php'), true) . "</pre>");
		//print("<pre>" . print_r(($mergeArrayForSpecificsResult), true) . "</pre>");

			$data['specifics_result'] = 	$mergeArrayForSpecificsResult;

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			
			//print("<pre>" . print_r($data['category_id'], true) . "</pre>");
			$category_specifics=$category_specific_info[1]['specifics']??[];
			$category_specific_key = [];
			$category_specific_names = [];

			foreach($category_specifics as $key => $specific) {
				$value = stripslashes($key);
				$category_specific_names[] = $value;
			}

			// Trier $category_specific_names par ordre alphabétique
			//sort($category_specific_names);

			$data['category_specific_names'] = $category_specific_names;

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			// Parcourir les clés de $mergeArrayForSpecificsResult
			foreach ($mergeArrayForSpecificsResult as $specific_key_name => $value) {
				//print("<pre>" . print_r($specific_key_name, true) . "</pre>");
				// Vérifier si la clé existe dans la base de données
				$replacement_term = $this->model_shopmanager_product_specific->getSpecificKey($specific_key_name, $data['category_id']);

				// Si la clé existe déjà dans la base de données
				if ($replacement_term != 'not_set') {
					if ($replacement_term =='') {
						$key_set=0;
					}else{
						$key_set=1;
					}
				//	//print("<pre>" . print_r($key_set, true) . "</pre>"); 
					$category_specific_key[$specific_key_name] = [
					
						'replacement_term' => $replacement_term,     // Pas de suggestion nécessaire, donc on garde la clé originale
						'key_set' => $key_set                             // 0 car la clé existe déjà dans la base
					];
				} else {
					
					if ($replacement_term == 'not_set') {
					// Si la clé n'existe pas dans la base, obtenir un terme suggéré via la fonction getSpecificsKey()
						$suggest_replacement_term = $this->model_shopmanager_ai->getSpecificKey($specific_key_name, $category_specifics);
					//	//print("<pre>" . print_r($suggest_replacement_term, true) . "</pre>");
						if(isset($suggest_replacement_term)){
							$this->model_shopmanager_product_specific->addSpecificKey($specific_key_name, $data['category_id'], $suggest_replacement_term);
							unset($category_specifics[$suggest_replacement_term]);
							$key_set=2;
						}else{
							$this->model_shopmanager_product_specific->addSpecificKey($specific_key_name, $data['category_id'], '');
							$key_set=0;
						}
					}else{
						$suggest_replacement_term='';
						$key_set=0;
					}
					// Ajouter l'entrée au tableau $category_specific_key
					$category_specific_key[$specific_key_name] = [
						
						'replacement_term' => $suggest_replacement_term??'',      // Terme suggéré
						'key_set' => $key_set                             // 1 car la clé doit être ajoutée avec le terme suggéré
					];
					
				}
			

			//$execution_times[($n++).'_Chargement line:'. __LINE__ ] = round(microtime(true) - $start_time,2);$start_time = microtime(true); 
			//echo '<br>_Chargement line:'. __LINE__.': *********'.round(microtime(true) - $start_time,2); 

			$data['category_specific_key']= $category_specific_key;
			$data['data_result']=$data;
		//	//print("<pre>" . print_r('3115:product.php', true) . "</pre>");
		//	//print("<pre>" . print_r($data['manageProductInfoSources'], true) . "</pre>");
		
			//print("<pre>".print_r ($execution_times ,true )."</pre>");
			$data['total_execution_time'] = array_sum($execution_times);
            
		//	//echo "Temps total d'exécution : " . $total_execution_time . " secondes\n";
		if (isset($data['manageProductInfoSources']) && is_array($data['manageProductInfoSources']) && count($data['manageProductInfoSources']) > 0) {
			if(isset($data['view'])){
				if(!isset($data['condition_marketplace_item_id'] )){
					//print("<pre>" . print_r(110, true) . "</pre>");
				   //print("<pre>" . print_r($product_search, true) . "</pre>");
				//}else{
					//print("<pre>" . print_r(value: '4189:PRODUCTSEARCH.php') . "</pre>");

					$data['conditions_marketplace_item_id'] =  $this->model_shopmanager_condition->getConditionDetails($data['category_id'],$data['condition_id'],null,$data['site_id']??0);
					$data['condition_marketplace_item_id'] =  $data['conditions_marketplace_item_id'][1][$data['condition_id']]['condition_marketplace_item_id'];
				}
				$data['save_action'] = $this->url->link('shopmanager/product_search/product_feed', 'token=' . $this->session->data['token'], true);

				return $this->load->view('shopmanager/product_search_'.$data['view'], $data);
			}else{
				return $this->load->view('shopmanager/product_search_form', $data);
			}
			
		} else {
			return $this->load->view('shopmanager/product_search_form', $data);
		}
        
}
$data['total_execution_time'] = array_sum($execution_times);
return $this->load->view('shopmanager/product_search_form', $data);
}

public function getProductSearchData() {

	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

	$this->load->language('shopmanager/product');
    $json = [];
	$data = [];
	$url='';
    // Lire les données envoyées en JSON
	//if (isset($this->request->post)) {
		//	//print("<pre>".print_r ($this->request->post,true )."</pre>");
	$product_id = $this->request->post['product_id']??27833;//27319 ;
	
    // Charger les modèles nécessaires
		$this->load->model('shopmanager/tools');
		$this->load->model('shopmanager/product_search'); 
		$this->load->model('localisation/language');
		$this->load->model('shopmanager/manufacturer');
		$this->load->model('shopmanager/ebay');
		$this->load->model('shopmanager/marketplace');
		$this->load->model('shopmanager/catalog/category');
		$this->load->model('shopmanager/condition');
		$this->load->model('shopmanager/product');
		$this->load->model('shopmanager/ai');
		$this->load->model('tool/image');


        // Récupérer les informations du produit
        $product_info = $this->model_shopmanager_product->getProduct($product_id);
           
		$data['product_description'] = $this->model_shopmanager_product->getProductDescriptions($product_id);

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['product_id'] = $product_info['product_id'];
		
		$data['category_id'] = $product_info['category_id'];

		$data['condition_id'] = $product_info['condition_id'];

		$data['model'] = $product_info['model'];

		$data['sku'] = $product_info['sku'];
		
		$data['upc'] = $product_info['upc'];

		$data['ean'] = $product_info['ean'];
	
		$data['jan'] = $product_info['jan'];
		
		$data['isbn'] = $product_info['isbn'];

		$data['mpn'] = $product_info['mpn'];
		
		$data['location'] = $product_info['location'];
		
		$data['made_in_country_id'] = $product_info['made_in_country_id'];

		$data['shipping'] = $product_info['shipping'];
	
		$data['price'] = round($product_info['price'],2);
		
		$data['price_with_shipping'] = round($product_info['price_with_shipping'],2);
			
		$data['shipping_carrier'] = $product_info['shipping_carrier'];
				
			$data['quantity'] = $product_info['quantity'];
		
			$data['unallocated_quantity'] = $product_info['unallocated_quantity'];
		
			$data['location'] = $product_info['location'];
		
			$data['condition_id'] = $product_info['condition_id'];

			$data['status'] = $product_info['status'];
		
			$data['weight'] = round($product_info['weight'],3);
				
	
			$data['length'] = round($product_info['length'],1);
	
			$data['width'] = round($product_info['width'],1);
		
	
			$data['height'] = round($product_info['height'],1);
		
		
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		
			$manufacturer_info = $this->model_shopmanager_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
			
			$categories = $this->model_shopmanager_product->getProductCategories($product_id);
			
	
			$data['product_categories'] = array();
		
			foreach ($categories as $category_id) {
				$category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
				if ($category_info) {
					$data['product_categories'][] = array(
						'category_id' => $category_info['category_id'],
						'site_id' => $category_info['site_id'],
						'name_category' => $category_info['name'],
						'name'        => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
					);
					if( $category_info['leaf']=='1'){
						$data['category_id']=$category_info['category_id'];
						$data['site_id']=$category_info['site_id'];
					}
				}
			}
	

	
			$data['image'] = $product_info['image'];
			if (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
				$data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
			} else {
				$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			}
	
			$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
			$product_images = $this->model_shopmanager_product->getProductImages($product_id);

		$data['product_images'] = array();

		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order']
			);
		}
		
	
			
	
		
		

        // Obtenir les données de recherche du produit
        $data['product_search_data'] = $this->model_shopmanager_product_search->getProductSearchData(
            $product_info['upc'] ?? '',
            $product_info['product_id']
        );

        // Fusionner les nouvelles données avec celles du produit
        $data_json = $this->model_shopmanager_product_search->feedProductInfoWithSearchData(json_encode($data));
        $data = json_decode($data_json, true);
		//print("<pre>".print_r ($data,true )."</pre>");
		$this->model_shopmanager_product_search->editProduct($product_id, $data);
		//print("<pre>".print_r ($data,true )."</pre>");

	$result = $this->model_shopmanager_product->getProducts(['filter_product_id' => $product_id]);
	$result = $result[0];

	if (is_file(DIR_IMAGE . $result['image'])) {
		$image = $this->model_tool_image->resize($result['image'], 100, 100);
	} else {
		$image = $this->model_tool_image->resize('no_image.png', 100, 100);
	}

	$special = false;

	$product_specials = $this->model_shopmanager_product->getProductSpecials($result['product_id']);

	foreach ($product_specials  as $product_special) {
		if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
			$special = $product_special['price'];

			break;
		}
	}


			// Ajouter le produit mis à jour au JSON de réponse
			$json[$product_id] = [
				'product_id' => $result['product_id'],
				'condition_id' => $result['condition_id'],
				'category_id' => $result['category_id'],
				'made_in_country_id' => $result['made_in_country_id'],
				'condition_name' => $result['condition_name'],
				'marketplace_accounts_id' => $result['marketplace_accounts_id'], 
				'image' => $image,
				'name' => $result['name'],
				'model' => $result['model'],
				'price' => round($result['price'], 2),
				'special' => $special,
				'quantity' => $result['quantity'],
				'unallocated_quantity' => $result['unallocated_quantity'],
				'location' => $result['location'],  
				'status_id' => $result['status'],
				'has_sources' => $result['has_sources'] ? $this->language->get('text_sources_set') : $this->language->get('text_sources_not_set'),
				'has_specifics' => $result['has_specifics'] ? $this->language->get('text_specifics_set') : $this->language->get('text_specifics_not_set'),
				'filled_specifics_count' => $result['filled_specifics_count'],
				'total_specifics_count' => $result['total_specifics_count'],
				'status' => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit' => $this->url->link(
					'shopmanager/product/edit', 
					'token=' . $this->session->data['token'] . '&product_id=' . $result['product_id'] . 
					($result['upc'] == '' || $result['has_specifics'] ? '' : '&product_search=true'), 
					true
				),
			];
		
		
		// Vérification finale : s'il n'y a pas de produit, renvoyer un message d'erreur
	/*  }else{ 
			$json['error'] = 'Aucun produit valide trouvé.';
		}*/

		// Retourner les produits mis à jour en JSON
		$this->response->setOutput(json_encode($json, JSON_PRETTY_PRINT));
	}
}