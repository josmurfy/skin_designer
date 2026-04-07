<?php
namespace Opencart\Admin\Controller\Shopmanager;

class Ebay extends \Opencart\System\Engine\Controller {
    private $error = array();

    public function index(): void {
        $lang = $this->load->language('shopmanager/ebay');
        $data = $data ?? [];
        $data += $lang;

        $this->document->setTitle(($lang['heading_title'] ?? ''));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
           //print("<pre>".print_r ($this->request->post,true )."</pre>");
            $this->model_setting_setting->editSetting('ebay', $this->request->post);

            $this->session->data['success'] = ($lang['text_success'] ?? '');

            $this->response->redirect($this->url->link('shopmanager/ebay', 'user_token=' . $this->session->data['user_token'], true));
        }

        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_edit'] = ($lang['text_edit'] ?? '');
        $data['text_success'] = ($lang['text_success'] ?? '');

        // Load entries
        $data['entry_seller_info'] = ($lang['entry_seller_info'] ?? '');
        $data['entry_shipping_info'] = ($lang['entry_shipping_info'] ?? '');
        $data['entry_return_policy'] = ($lang['entry_return_policy'] ?? '');
        $data['entry_product_disclaimer'] = ($lang['entry_product_disclaimer'] ?? '');
        $data['entry_payment_info'] = ($lang['entry_payment_info'] ?? '');
        $data['entry_template_style'] = ($lang['entry_template_style'] ?? '');

        $data['button_save'] = ($lang['button_save'] ?? '');
        $data['button_cancel'] = ($lang['button_cancel'] ?? '');

        // Load settings from the database
        $data['ebay_seller_info'] = $this->config->get('ebay_seller_info');
        $data['ebay_shipping_info'] = $this->config->get('ebay_shipping_info');
        $data['ebay_return_policy'] = $this->config->get('ebay_return_policy');
        $data['ebay_product_disclaimer'] = $this->config->get('ebay_product_disclaimer');
        $data['ebay_payment_info'] = $this->config->get('ebay_payment_info');
        $data['ebay_template_style'] = $this->config->get('ebay_template_style');

        // Load errors
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        // Load success message
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => ($lang['text_home'] ?? ''),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => ($lang['heading_title'] ?? ''),
            'href' => $this->url->link('shopmanager/ebay', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['action'] = $this->url->link('shopmanager/ebay', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');

        // Render the template with the data
        $this->response->setOutput($this->load->view('shopmanager/ebay_template_form', $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'shopmanager/ebay')) {
            $this->error['warning'] = ($lang['error_permission'] ?? '');
        }

        return !$this->error;
    }

    public function endListing(){

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
     
        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
          

        //    $product_id=21768;
         //   $marketplace_item_id='296605947039';

            // Vérification des paramètres
            if (isset($this->request->post)) {
               
                $product_id = $this->request->post['product_id'] ;
                $marketplace_item_id = $this->request->post['marketplace_item_id'] ;
                $marketplace_account_id = isset($this->request->post['marketplace_account_id']) ? (int)$this->request->post['marketplace_account_id'] : 0;
               //print("<pre>".print_r ($data,true )."</pre>");
                
                // Appel eBay (si déjà terminé on ignore l'erreur)
                try {
                    $result = $this->model_shopmanager_ebay->endListing($marketplace_item_id);
                } catch (\Exception $e) {
                    $result = null;
                    $json['ebay_warning'] = $e->getMessage();
                }

                // Toujours effacer l'item_id en DB même si eBay retourne une erreur
                $this->model_shopmanager_marketplace->deleteProductMarketplaceItemId($marketplace_item_id);

                $json['success'] = true;
                $json['message'] = '';
            } else {
                $json['error'] = true;
                $json['message'] = 'Paramètres invalides.';
            }
        } else {
            $json['error'] = true;
            $json['message'] = 'Méthode de requête non autorisée.';
        }
      //print("<pre>".print_r ($json,true )."</pre>");
        // Log de la réponse finale
        //$this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function relist(){

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/catalog/product');
     
        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            if (isset($this->request->post)) {
               
                $product_id = $this->request->post['product_id'] ;
                $marketplace_item_id = $this->request->post['marketplace_item_id'] ;
               
               //print("<pre>".print_r ($data,true )."</pre>");
                
                // Mise à jour des tarifs dans la base de données
                $result=$this->model_shopmanager_ebay->relist($marketplace_item_id);
           //print("<pre>".print_r ($result,true )."</pre>");
              

                if ($result['Ack']!='Failure') {
                   
                    $this->model_shopmanager_catalog_product->editProductMarketplaceItemId($product_id,$result['ItemID']);
                    $json['success'] = true;
                    $json['message'] = $result;
                    $json['marketplace_item_id'] = $result['ItemID'];
                } else {
                    $json['error'] = false;
                    $json['message'] = $result['Errors']['LongMessage'];
                }
            } else {
                $json['success'] = false;
                $json['message'] = 'Paramètres invalides.';
            }
        } else {
            $json['success'] = false;
            $json['message'] = 'Méthode de requête non autorisée.';
        }
      //print("<pre>".print_r ($json,true )."</pre>");
        // Log de la réponse finale
        //$this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function add(){

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/catalog/product');
     
        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            if (isset($this->request->post)) {
               
                $product_id = $this->request->post['product_id'] ;
                $quantity = $this->request->post['quantity'] + $this->request->post['unallocated_quantity'] ;
                $site_id = $this->request->post['site_id'] ;
               
               //print("<pre>".print_r ($data,true )."</pre>");
                
                // Mise à jour des tarifs dans la base de données
                $result=$this->model_shopmanager_ebay->addListing($product_id,$quantity,$site_id);
           //print("<pre>".print_r ($result,true )."</pre>");
              

                if ($result['Ack']!='Failure') {
                    //$this->model_shopmanager_catalog_product->editProductMarketplaceItemId($product_id,'');
           //         $this->model_shopmanager_catalog_product->editProductMarketplaceItemId($product_id,$result['ItemID']);
                    $data = array(
                        'customer_id' => 10,
                        'product_id' => $product_id,
                        'marketplace_id' => 0,
                        'marketplace_account_id' => 0,
                        'marketplace_item_id' => $result['ItemID'],
                        'quantity_listed' => $quantity,
                        'quantity_sold' => 0,
                        'category_id' => 0,
                        'currency' => '',
                        'price' => 0,
                        'specifics' => '',
                        'error' => '',
                        'status' => 1,
                        'to_update' => 0,
                        'ebay_image_count' => 0,
                    );

                    $this->model_shopmanager_marketplace->addProductMarketplace($data);
                    $json['success'] = true;
                    $json['marketplace_item_id'] = $result['ItemID'];
                    $json['message'] = $result;
                } else {
                    $json['error'] = false;
                    $json['message'] = json_encode($result['Errors']);
                }
            } else {
                $json['success'] = false;
                $json['message'] = 'Paramètres invalides.';
            }
        } else {
            $json['success'] = false;
            $json['message'] = 'Méthode de requête non autorisée.';
        }
      //print("<pre>".print_r ($json,true )."</pre>");
        // Log de la réponse finale
        //$this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function edit(){

        $this->load->model('shopmanager/ebay');
     
        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            if (isset($this->request->post)) {
               
                $product_id = $this->request->post['product_id'] ;

            //print("<pre>".print_r ($post,true )."</pre>");
                // Mise à jour des tarifs dans la base de données
                $result=$this->model_shopmanager_ebay->editListing($product_id);
               //print("<pre>".print_r ($result,true )."</pre>");
              

                if ($result['Ack']!='Failure') {
                    $json['success'] = true;
                    $json['message'] = $result['ItemID'];
                } else {
                    $json['error'] = false;
                    $json['message'] = $result['Errors']['LongMessage'];
                }
            } else {
                $json['success'] = false;
                $json['message'] = 'Paramètres invalides.';
            }
        } else {
            $json['success'] = false;
            $json['message'] = 'Méthode de requête non autorisée.';
        }
      //print("<pre>".print_r ($json,true )."</pre>");
        // Log de la réponse finale
        //$this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function editPrice(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
     
        $json = array();
 

       //$this->request->post['product_id']=$this->request->post['product_id'];//$this->request->get['product_id'];
       //$this->request->post['marketplace_item_id']=$this->request->post['marketplace_item_id']??304642515658;//$this->request->get['marketplace_item_id'];
       //$this->request->post['price']=$this->request->post['price'];//$this->request->get['price'];
       //$this->request->post['made_in_country_id']=$this->request->post['made_in_country_id']??172;//$this->request->get['made_in_country_id'];

   //     $this->request->post['product_id']='27097';
    //    $this->request->post['marketplace_item_id']='296927487192';
    //    $this->request->post['price']='3.90';
        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            if (isset($this->request->post)) {
               
                $product_id = $this->request->post['product_id'] ;
                $marketplace_item_id = $this->request->post['marketplace_item_id'] ;
                $price = $this->request->post['price'] ;
                $made_in_country_id = $this->request->post['made_in_country_id'] ;
            //print("<pre>".print_r ($this->request->post,true )."</pre>");
        
                // Mise à jour des tarifs dans la base de données
                $result=$this->model_shopmanager_ebay->editPrice($marketplace_item_id, $price,$made_in_country_id);
            
               
                if (isset($result['Ack']) && $result['Ack']!='Failure') {
                    $json['success'] = true;
                  //  $json['message'] = $result['ItemID'];
                    $json['message'] = $result;
                    $json['marketplace_item_id'] = $result['ItemID'];
                
                   
                } else {
                    
                    if (isset($result['Errors'])) {
                    //print("<pre>".print_r ('ebay:316',true )."</pre>");
                        // Terminer la liste eBay
                   //print("<pre>".print_r ($result,true )."</pre>");
                      $messageFound = false;
                  //print("<pre>".print_r ('ebay:323',true )."</pre>");
                 //print("<pre>".print_r ($result,true )."</pre>");

                      // Parcourir chaque élément dans l'array `Errors`
                   //   foreach ($result['Errors'] as $error) {
                          // Vérifier si le `LongMessage` contient le texte "part of a sale"
                        $errors=$result['Errors'];
                        foreach ($errors as $error) {
                            if (isset($error['ErrorCode']) && $error['ErrorCode'] == '21919248') {
                                // Erreur spécifique trouvée
                                $messageFound = true;
                                // Vous pouvez également accéder au message complet si nécessaire
                                $longMessage = $error['LongMessage'];
                                break; // Arrêter la boucle dès que l'erreur est trouvée
                            }
                        }
                       

                         
                       
                    //print("<pre>".print_r ('ebay:326',true )."</pre>");
                        
                      
                   //   }
                      if ($messageFound) {
                    //print("<pre>".print_r ('ebay:351',true )."</pre>");
                      //print("<pre>".print_r ($result,true )."</pre>");

                    //print("<pre>".print_r ('ebay:334',true )."</pre>");
                    //print("<pre>".print_r ($result,true )."</pre>");
                            $result = $this->model_shopmanager_ebay->endListing($marketplace_item_id);
                      //print("<pre>".print_r ('ebay:331',true )."</pre>");
                      //print("<pre>".print_r ($result,true )."</pre>");
                     
                            $existingEntry = $this->model_shopmanager_marketplace->getMarketplace([
                                'marketplace_item_id' => $marketplace_item_id
                            ]);
                            $existing_data= json_decode($existingEntry[$marketplace_item_id],true);
                            $this->model_shopmanager_marketplace->deleteProductMarketplaceItemId($marketplace_item_id);
                            // Remettre en liste l'élément eBay
                            $result = $this->model_shopmanager_ebay->relist($marketplace_item_id);
                            $existing_data['marketplace_item_id'] = $result['ItemID'];
                            $existing_data['to_update'] = 0;
                           
                            if ($result['Ack']!='Failure') {
                                //$this->model_shopmanager_catalog_product->editProductMarketplaceItemId($product_id,'');\
                                $result=$this->model_shopmanager_ebay->editPrice($result['ItemID'], $price);
                                $this->model_shopmanager_marketplace->addProductMarketplace($existing_data);
                                $json['success'] = true;
                              //  $json['message'] = $result;
                                $json['message'] = $result;
                                $json['marketplace_item_id'] = $result['ItemID'];
                            } else { 
                                $json['success'] = false;
                                $json['error'] = false;
                                $json['message'] = $result['Errors']['LongMessage'];
                            }
                        }
                    }else{
                        $json['success'] = true;
                        //  $json['message'] = $result;
                          $json['message'] = $result;
                        //  $json['marketplace_item_id'] = $result['ItemID'];
                    }
                    
                }
            } else {
                $json['success'] = false;
                $json['message'] = 'Paramètres invalides.';
            }
        } else {
            $json['success'] = false;
            $json['message'] = 'Méthode de requête non autorisée.';
        }
      //print("<pre>".print_r ($json,true )."</pre>");
        // Log de la réponse finale
        //$this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCategorySpecifics(){

        $this->load->model('shopmanager/ebay');
     
        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            if (isset($this->request->post)) {
               
              $category_id = $this->request->post['category_id'] ;
              $site_id = $this->request->post['site_id'];
           //     $category_id=617;
           //print("<pre>".print_r ($post,true )."</pre>");
                // Mise à jour des tarifs dans la base de données
                $result=$this->model_shopmanager_ebay->getCategorySpecifics($category_id,$site_id);
              
              
            //print("<pre>".print_r ($result,true )."</pre>");
            if (!isset($result['errors'])) {
              //print("<pre>".print_r ($result,true )."</pre>");
                $json['success'] = true;
                $json['data'] = $result; // Assurez-vous que $result contient bien les spécificités de la catégorie
            } else {
                $json['error'] = true;
                $json['message'] = $result['errors'][0]['message'];
            }
            } else {
                $json['success'] = false;
                $json['message'] = 'Paramètres invalides.';
            }
       } else {
          $json['success'] = false;
          $json['message'] = 'Méthode de requête non autorisée.';
       }
      //print("<pre>".print_r ($json,true )."</pre>");
        // Log de la réponse finale
        //$this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getCategoryId() {
        $this->load->model('shopmanager/ebay');
        
        $json = [];
    
        if (isset($this->request->get['upc']) && $this->request->get['upc']) {
            $gtin = $this->request->get['upc'];
            $category_id = $this->model_shopmanager_ebay->get($gtin,  null,  null,  null,  10,  null,  null, 1, null, true) ;;
    
            if ($category_id) {
                $json['success'] = true;
                $json['category_id'] = $category_id;
            } else {
                $json['success'] = false;
                $json['error'] = 'Category not found for this UPC';
            }
        } else {
            $json['success'] = false;
            $json['error'] = 'No UPC provided';
        }
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function getLeafCategories(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/catalog/category');
       $site_id=100;
        $leafCategories=$this->model_shopmanager_ebay->getLeafCategories($site_id);
    //print("<pre>".print_r ($leafCategories,true )."</pre>");
        foreach($leafCategories as $category){
            
            $category_id=$category['CategoryID'];
        
           $result=$this->model_shopmanager_catalog_category->editCategoryEbay_site_id($category_id, $site_id);

           if(!is_numeric($result)){
            //print("<pre>".print_r ($result,true )."</pre>");
           }else{
        //print("<pre>".print_r ($category,true )."</pre>");
           }
           // return NULL; 
        }
        echo "<br>NB Category Leaf eBay Motor: ".count($leafCategories);

        $site_id=0;
        $leafCategories=$this->model_shopmanager_ebay->getLeafCategories($site_id);
    //print("<pre>".print_r ($leafCategories,true )."</pre>");
        foreach($leafCategories as $category){
            
            $category_id=$category['CategoryID'];
        
           $result=$this->model_shopmanager_catalog_category->editCategoryEbay_site_id($category_id, $site_id);

           if(!is_numeric($result)){
            //print("<pre>".print_r ($result,true )."</pre>");
           }else{
        //print("<pre>".print_r ($category,true )."</pre>");
           }
           // return NULL; 
        }
        echo "<br>Nb Category Leaf: ".count($leafCategories);


    }

    public function fetchAndAddAllCategories(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        //print("<pre>".print_r ('411:ebay.php',true )."</pre>");

        $this->load->model('shopmanager/ebay');

     //   $result=$this->model_shopmanager_ebay->fetchAndAddAllCategories(100);
      //print("<pre>".print_r ($result,true )."</pre>");

        //echo $result

    }

    public function processAndCompareAllEbayCategories(){
        $this->load->model('shopmanager/catalog/category_ebay');

        $result=$this->model_shopmanager_catalog_category_ebay->processAndCompareAllEbayCategories();
    //print("<pre>".print_r ($result,true )."</pre>");

        //echo $result

    }

    public function trfCategoriesSpecifics (){
		$this->load->model('shopmanager/catalog/category_ebay');

        $this->model_shopmanager_catalog_category_ebay->trfCategoriesSpecifics();
	}
	
    public function searchByName() {
        $lang = $this->load->language('shopmanager/ebay');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/tools');
        $json = [];
      //print("<pre>".print_r (value: '446:ebay' )."</pre>");
        $data = json_decode(file_get_contents('php://input'), true);
     //   $data['keyword']=$data['keyword']??'';
      

        if (!isset($data['keyword']) || empty($data['keyword'])) {
            $json['error'] = ($lang['error_keyword_missing'] ?? '');
        } else {
            if (isset($data['selected_ebay_item']) && isset($data['json_items'])) {
                 $json_items =$data['json_items']; // Décoder les items JSON
			$selectedItem = [];
			if (!is_array($data['selected_ebay_item'])){
				$data['selected_ebay_item'][0]=$data['selected_ebay_item'];
			}
			// Vérifier que json_items est un tableau
			if (is_array($json_items)) {
				// Parcourir les `selected_ebay_item` pour trouver les informations correspondantes
				foreach ($data['selected_ebay_item'] as $selectedProductId) {
					foreach ($json_items as $key=>$item) {
						$json[$key] =$item;   
						if (isset($item['marketplace_item_id']) && $item['marketplace_item_id'] == $selectedProductId) {
							// Ajouter l'item correspondant à $selectedItem
							
							$selectedItem[] = $item; 
						
							break; // Passer au prochain `selectedProductId`
						}
					}
				}
			}
            }
          

            // Appel de la méthode get() pour récupérer les produits eBay
            $data_result = $this->model_shopmanager_ebay->get(null, $data['keyword']);
        //print("<pre>".print_r (value: '477:ebay' )."</pre>");
        //print("<pre>".print_r($data_result, true)."</pre>");
            if ($data_result) {
                if(isset($selectedItem)){
                    $data_result['selectedItem']=$selectedItem;
                }
                $data_result['column_name'] = ($lang['column_name'] ?? '');
                $data_result['column_condition'] = ($lang['column_condition'] ?? '');
                $data_result['column_category_id'] = ($lang['column_category_id'] ?? '');
                $data_result['column_picture'] = ($lang['column_picture'] ?? '');
                $data_result['column_category_name'] = ($lang['column_category_name'] ?? '');
                $data_result['column_product_id'] = ($lang['column_product_id'] ?? '');
           //     $data_result['keyword'] = ($lang['keyword'] ?? '');
                $data_result['keyword'] = $data['keyword']??'';
                 
                $json['success'] = true;
    
                // Charge la vue en tant que HTML
                $json['html'] = $this->load->view('shopmanager/ebay_search_results', $data_result);
            } else {
                $json['error'] = ($lang['error_no_items_found'] ?? '');
            }
        }
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
   
    public function getProduct() {
        $lang = $this->load->language('shopmanager/ebay'); // Charger le fichier de langue
        $json = [];

      //  if ($this->request->server['REQUEST_METHOD'] === 'POST') {
            // Récupérer les paramètres de la requête
            $marketplace_item_id = $this->request->post['marketplace_item_id'] ?? null;
          
            $marketplace_item_id="166640725049";

            // Vérifier les paramètres
            if (!$marketplace_item_id) {
                $json['success'] = false;
                $json['error'] = ($lang['error_missing_parameters'] ?? '');
            } else {
                // Charger le modèle et récupérer les données
                $this->load->model('shopmanager/ebay');
                $productData = $this->model_shopmanager_ebay->getProduct($marketplace_item_id);
             //print("<pre>".print_r ($productData,true )."</pre>");
                if ($productData) {
                   // $productData= json_decode($productData,true); 
                 //print("<pre>".print_r ($productData[0]['Item'],true )."</pre>");   
                    $json['success'] = true;
                    $json['result'] = $productData[0]['Item']; 
                } else {
                  //  $json['success'] = false;
                  //  $json['error'] = ($lang['error_no_values'] ?? '');
                    $json['success'] = true;
                    $json['error'] = $marketplace_item_id ;
                }
            }
     //   } else {
            //$json['success'] = false;
            //$json['error'] = ($lang['error_invalid_request'] ?? '');
      //      $json['success'] = true;
     //       $json['error'] = $this->request->post['marketplace_item_id'] ;
    //    }

        // Retourner la réponse JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Fetch lowest sold + active market prices for a card via eBay Finding API.
     * POST: keyword (string), card_id (int, optional — saves to DB if provided)
     * Returns: { success, price_sold, price_list, keyword, api_error }
     */
    public function getMarketPrices(): void {
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/card/card');
        $json = [];

        $keyword = trim($this->request->post['keyword'] ?? '');
        $card_id = (int)($this->request->post['card_id'] ?? 0);
        $autoCorrect = true;

        $this->log->write('[getMarketPrices][input] card_id=' . $card_id . ' keyword_post=' . $keyword);

        if (!$card_id) {
            $json['error'] = 'Missing card_id';
            $this->log->write('[getMarketPrices][error] missing card_id');
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $card = $this->model_shopmanager_card_card->getCardById($card_id);
        if (empty($card)) {
            $json['error'] = 'Card not found';
            $this->log->write('[getMarketPrices][error] card not found card_id=' . $card_id);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $keywordFromCard = trim((string)($card['title'] ?? ''));
        if ($keywordFromCard === '') {
            $keywordParts = [];
            if (!empty($card['set_name'])) {
                $keywordParts[] = trim((string)$card['set_name']);
            }
            if (!empty($card['player_name'])) {
                $keywordParts[] = trim((string)$card['player_name']);
            }
            if (!empty($card['card_number'])) {
                $keywordParts[] = '#' . trim((string)$card['card_number']);
            }
            $keywordFromCard = trim(implode(' ', array_filter($keywordParts)));
        }

        if ($keywordFromCard !== '') {
            $keyword = $keywordFromCard;
        }

        $keyword = $this->normalizeMarketKeyword($keyword);

        $conditionText = strtolower(trim((string)($card['condition'] ?? '')));
        $conditionType = 'all';
        if (strpos($conditionText, 'ungraded') !== false || strpos($conditionText, 'raw') !== false) {
            $conditionType = 'raw';
        } elseif (strpos($conditionText, 'graded') !== false) {
            $conditionType = 'graded';
        }

        $grader = trim((string)($card['grader'] ?? $card['professional_grader'] ?? 'all'));
        if ($grader === '') {
            $grader = 'all';
        }
        $grade = trim((string)($card['grade'] ?? $card['grade_name'] ?? $card['grade_number'] ?? ''));

        if (strlen($keyword) < 3) {
            $json['error'] = 'keyword too short (min 3 chars)';
            $this->log->write('[getMarketPrices][error] keyword too short card_id=' . $card_id . ' keyword=' . $keyword);
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        ob_start();
        try {
            $excludeItemId = trim((string)($card['ebay_item_id'] ?? ''));
            $siteId = (int)($card['site_id'] ?? 0);

            $convertPriceSummaryToCad = function(array $summary): array {
                if (empty($summary['current_price'])) {
                    return [];
                }

                $currency = (string)($summary['currency'] ?? 'USD');

                $converted = [
                    'current_price' => round((float)$this->currency->convert((float)$summary['current_price'], $currency, 'CAD'), 2),
                    'original_price' => null,
                    'condition' => (string)($summary['condition'] ?? ''),
                    'bid_count' => isset($summary['bid_count']) ? (int)$summary['bid_count'] : 0,
                ];

                if (!empty($summary['original_price'])) {
                    $converted['original_price'] = round((float)$this->currency->convert((float)$summary['original_price'], $currency, 'CAD'), 2);
                }

                return $converted;
            };

            $ownListingSummary = [];
            $ownAuction = [];
            $ownBuyNow = [];

            if ($excludeItemId !== '') {
                $ownListingSummary = $this->model_shopmanager_ebay->getOwnListingPriceSummary($excludeItemId, [
                    'site_id' => $siteId,
                    'marketplace_account_id' => 1,
                ]);

                $ownAuction = $convertPriceSummaryToCad((array)($ownListingSummary['auction'] ?? []));
                $ownBuyNow = $convertPriceSummaryToCad((array)($ownListingSummary['buy_now'] ?? []));
            }

            // Single request: 100 lowest prices (all listing types, all conditions), sorted price_asc.
            // We then classify the results into 4 buckets client-side.
            $searchOptions = [
                'sort'           => 'price_asc',
                'limit'          => 100,
                'page'           => 1,
                'site_id'        => $siteId,
                'condition_type' => 'all',
                'category_id'    => '261328',
                'auto_correct'   => 'KEYWORD',
            ];

            $this->log->write('[getMarketPrices][request] card_id=' . $card_id . ' keyword=' . $keyword . ' options=' . json_encode($searchOptions));

            $keywordsTried = [];
            $keywordsTried[] = $keyword;
            $marketData = $this->model_shopmanager_ebay->searchAndClassifyActiveItems($keyword, $searchOptions, 1, $excludeItemId);

            $this->log->write("[getMarketPrices][ebay_output]\n" . print_r($marketData, true));
            $warnings = ob_get_clean();

            if (!empty($excludeItemId)) {
                $this->log->write('[getMarketPrices][exclude_own_item] card_id=' . $card_id . ' excluding ebay_item_id=' . $excludeItemId);
            }

            $auctionRaw = $marketData['buckets']['auction_raw'] ?? null;
            $auctionGraded = $marketData['buckets']['auction_graded'] ?? null;
            $buyNowRaw = $marketData['buckets']['buy_now_raw'] ?? null;
            $buyNowGraded = $marketData['buckets']['buy_now_graded'] ?? null;

            if ($auctionRaw !== null) {
                $auctionRaw['price'] = round((float)$this->currency->convert((float)$auctionRaw['price'], (string)($auctionRaw['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($auctionGraded !== null) {
                $auctionGraded['price'] = round((float)$this->currency->convert((float)$auctionGraded['price'], (string)($auctionGraded['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($buyNowRaw !== null) {
                $buyNowRaw['price'] = round((float)$this->currency->convert((float)$buyNowRaw['price'], (string)($buyNowRaw['currency'] ?? 'USD'), 'CAD'), 2);
            }
            if ($buyNowGraded !== null) {
                $buyNowGraded['price'] = round((float)$this->currency->convert((float)$buyNowGraded['price'], (string)($buyNowGraded['currency'] ?? 'USD'), 'CAD'), 2);
            }

            $this->model_shopmanager_card_card->updateCardMarketPrices($card_id, $auctionRaw['price'] ?? null, $buyNowRaw['price'] ?? null);

            $json['success'] = true;
            $json['card_id'] = $card_id;
            $json['keyword'] = $keyword;
            $json['auto_correct'] = $autoCorrect;
            $json['keywords_tried'] = $keywordsTried;

            // Auction – ungraded (raw)
            $json['price_sold']      = $auctionRaw !== null ? number_format($auctionRaw['price'], 2, '.', '') : null;
            $json['price_sold_url']  = $auctionRaw['url'] ?? '';
            $json['price_sold_bids'] = $auctionRaw['bids'] ?? 0;

            // Auction – graded
            $json['price_sold_graded']       = $auctionGraded !== null ? number_format($auctionGraded['price'], 2, '.', '') : null;
            $json['price_sold_graded_url']   = $auctionGraded['url']   ?? '';
            $json['price_sold_graded_bids']  = $auctionGraded['bids']  ?? 0;
            $json['price_sold_graded_grade'] = $auctionGraded['grade'] ?? '';

            // Buy-now – ungraded (raw), own listing excluded
            $json['price_list']     = $buyNowRaw !== null ? number_format($buyNowRaw['price'], 2, '.', '') : null;
            $json['price_list_url'] = $buyNowRaw['url'] ?? '';

            // Buy-now – graded
            $json['price_list_graded']       = $buyNowGraded !== null ? number_format($buyNowGraded['price'], 2, '.', '') : null;
            $json['price_list_graded_url']   = $buyNowGraded['url']   ?? '';
            $json['price_list_graded_grade'] = $buyNowGraded['grade'] ?? '';

            $json['own_auction'] = [
                'price'          => isset($ownAuction['current_price']) ? number_format((float)$ownAuction['current_price'], 2, '.', '') : null,
                'original_price' => isset($ownAuction['original_price']) && $ownAuction['original_price'] !== null ? number_format((float)$ownAuction['original_price'], 2, '.', '') : null,
                'condition'      => (string)($ownAuction['condition'] ?? ''),
                'bid_count'      => isset($ownAuction['bid_count']) ? (int)$ownAuction['bid_count'] : 0,
                'url'            => (string)($ownListingSummary['url'] ?? ''),
                'is_variant'     => !empty($ownListingSummary['is_variant']),
            ];
            $json['own_buy_now'] = [
                'price'          => isset($ownBuyNow['current_price']) ? number_format((float)$ownBuyNow['current_price'], 2, '.', '') : null,
                'original_price' => isset($ownBuyNow['original_price']) && $ownBuyNow['original_price'] !== null ? number_format((float)$ownBuyNow['original_price'], 2, '.', '') : null,
                'condition'      => (string)($ownBuyNow['condition'] ?? ''),
                'bid_count'      => isset($ownBuyNow['bid_count']) ? (int)$ownBuyNow['bid_count'] : 0,
                'url'            => (string)($ownListingSummary['url'] ?? ''),
                'is_variant'     => !empty($ownListingSummary['is_variant']),
            ];

            $json['api_error'] = (string)($marketData['error'] ?? '');
            $json['api_auto_corrections'] = $marketData['auto_corrections'] ?? [];
            $json['api_warnings'] = $marketData['warnings'] ?? [];
            $json['rate_limited'] = $this->isApiRateLimitedMessage($json['api_error']);
            $json['manual_urls'] = $this->model_shopmanager_ebay->buildManualEbayUrls($keyword);
            if ($warnings) {
                $json['php_warning'] = trim($warnings);
            }

            $this->log->write('[getMarketPrices][output] card_id=' . $card_id . ' keyword=' . $keyword . ' auto_correct=' . ($autoCorrect ? '1' : '0') . ' tried=' . implode(' | ', $keywordsTried) . ' total=' . (int)($marketData['total'] ?? 0) . ' items=' . count($marketData['items'] ?? []) . ' auction_raw=' . ($auctionRaw !== null ? number_format($auctionRaw['price'], 2, '.', '') : 'NULL') . ' auction_graded=' . ($auctionGraded !== null ? number_format($auctionGraded['price'], 2, '.', '') . ' ' . ($auctionGraded['grade'] ?? '') : 'NULL') . ' buy_now_raw=' . ($buyNowRaw !== null ? number_format($buyNowRaw['price'], 2, '.', '') : 'NULL') . ' buy_now_graded=' . ($buyNowGraded !== null ? number_format($buyNowGraded['price'], 2, '.', '') . ' ' . ($buyNowGraded['grade'] ?? '') : 'NULL') . ' api_error=' . $json['api_error']);
        } catch (\Throwable $e) {
            ob_end_clean();
            $json['error'] = 'Exception: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine();
            $json['rate_limited'] = $this->isApiRateLimitedMessage($json['error']);
            if ($keyword !== '') {
                $json['manual_urls'] = $this->model_shopmanager_ebay->buildManualEbayUrls($keyword);
            }
            $this->log->write('[getMarketPrices][exception] card_id=' . $card_id . ' msg=' . $e->getMessage());
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function normalizeMarketKeyword(string $keyword): string {
        $keyword = trim($keyword);
        $keyword = preg_replace('/\s+/u', ' ', $keyword) ?? $keyword;

        return trim($keyword);
    }

    private function buildMarketKeywordCandidates(array $card, string $baseKeyword): array {
        $candidates = [];

        $baseKeyword = $this->normalizeMarketKeyword($baseKeyword);
        if ($baseKeyword !== '') {
            $candidates[] = str_replace('#', ' #', $baseKeyword);
            $candidates[] = str_replace('#', ' ', $baseKeyword);
        }

        if (!empty($card['set_name']) || !empty($card['player_name']) || !empty($card['card_number'])) {
            $parts = [];
            if (!empty($card['set_name'])) {
                $parts[] = trim((string)$card['set_name']);
            }
            if (!empty($card['player_name'])) {
                $parts[] = trim((string)$card['player_name']);
            }
            if (!empty($card['card_number'])) {
                $parts[] = '#' . trim((string)$card['card_number']);
            }

            $candidates[] = implode(' ', array_filter($parts));
        }

        if (!empty($card['player_name']) && !empty($card['card_number'])) {
            $candidates[] = trim((string)$card['player_name']) . ' #' . trim((string)$card['card_number']);
        }

        if (!empty($card['set_name']) && !empty($card['card_number'])) {
            $candidates[] = trim((string)$card['set_name']) . ' #' . trim((string)$card['card_number']);
        }

        $unique = [];
        $seen = [];

        foreach ($candidates as $candidate) {
            $candidate = $this->normalizeMarketKeyword((string)$candidate);
            if ($candidate === '') {
                continue;
            }

            $key = strtolower($candidate);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $unique[] = $candidate;
        }

        return $unique;
    }

    private function isApiRateLimitedMessage(string $message): bool {
        if ($message === '') {
            return false;
        }

        $text = strtolower($message);
        $patterns = [
            'call limit',
            'rate limit',
            'quota',
            'too many requests',
            'http 429',
            'error 429',
            'request limit'
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($text, $pattern)) {
                return true;
            }
        }

        return false;
    }

    private function validateMarketplace($marketplace_account_id) {
        return $this->config->get('ebay_bypass_enabled') && (int)$marketplace_account_id === 1;
    }

}

