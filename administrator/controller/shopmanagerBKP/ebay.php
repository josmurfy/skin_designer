<?php
class ControllerShopmanagerEbay extends Controller {
    private $error = array();

    public function index() {
        $this->load->language('shopmanager/ebay');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
           //print("<pre>".print_r ($this->request->post,true )."</pre>");
            $this->model_setting_setting->editSetting('ebay', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('shopmanager/ebay', 'token=' . $this->session->data['token'], true));
        }

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_success'] = $this->language->get('text_success');

        // Load entries
        $data['entry_seller_info'] = $this->language->get('entry_seller_info');
        $data['entry_shipping_info'] = $this->language->get('entry_shipping_info');
        $data['entry_return_policy'] = $this->language->get('entry_return_policy');
        $data['entry_product_disclaimer'] = $this->language->get('entry_product_disclaimer');
        $data['entry_payment_info'] = $this->language->get('entry_payment_info');
        $data['entry_template_style'] = $this->language->get('entry_template_style');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

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
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('shopmanager/ebay', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('shopmanager/ebay', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true);

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
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function delete(){

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/product');
     
        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
          

        //    $product_id=21768;
         //   $marketplace_item_id='296605947039';

            // Vérification des paramètres
            if (isset($this->request->post)) {
               
                $product_id = $this->request->post['product_id'] ;
                $marketplace_item_id = $this->request->post['marketplace_item_id'] ;
               //print("<pre>".print_r ($data,true )."</pre>");
                
                // Mise à jour des tarifs dans la base de données
                $result=$this->model_shopmanager_ebay->delete($marketplace_item_id);
               
        //print("<pre>".print_r ($result,true )."</pre>");
           

           //     if ($result['Ack']=='Success') {
                  
                    $json['success'] = true;
                    $json['message'] = '';
               /* } else {
                //print("<pre>".print_r ($result,true )."</pre>");
                    $json['error'] = false;
                    $json['message'] = $result['Errors']['LongMessage'];
                }*/

                $this->model_shopmanager_product->editProductMarketplaceItemId($product_id,0);
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
        $this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function relist(){

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/product');
     
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
                   
                    $this->model_shopmanager_product->editProductMarketplaceItemId($product_id,$result['ItemID']);
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
        $this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function add(){

        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/product');
     
        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            if (isset($this->request->post)) {
               
                $product_id = $this->request->post['product_id'] ;
                $quantity = $this->request->post['quantity'] + $this->request->post['unallocated_quantity'] ;
                $site_id = $this->request->post['site_id'] ;
               
               //print("<pre>".print_r ($data,true )."</pre>");
                
                // Mise à jour des tarifs dans la base de données
                $result=$this->model_shopmanager_ebay->add($product_id,$quantity,$site_id);
           //print("<pre>".print_r ($result,true )."</pre>");
              

                if ($result['Ack']!='Failure') {
                    //$this->model_shopmanager_product->editProductMarketplaceItemId($product_id,'');
           //         $this->model_shopmanager_product->editProductMarketplaceItemId($product_id,$result['ItemID']);
                    $data = array(
                        'customer_id' => 10,
                        'product_id' => $product_id,
                      //  'marketplace_id' => 1,
                      //  'marketplace_account_id' => $result['marketplace_account_id'],
                        'marketplace_item_id' => $result['ItemID'],
                        'quantity_listed' => $quantity,
                        'quantity_sold' => 0
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
        $this->log->write('Response: ' . json_encode($json));
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
                $result=$this->model_shopmanager_ebay->edit($product_id);
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
        $this->log->write('Response: ' . json_encode($json));
      //print("<pre>".print_r ($json,true )."</pre>");
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function editPrice(){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/product');
     
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
                            $result = $this->model_shopmanager_ebay->delete($marketplace_item_id);
                      //print("<pre>".print_r ('ebay:331',true )."</pre>");
                      //print("<pre>".print_r ($result,true )."</pre>");
                     
                            $existingEntry = $this->model_shopmanager_product->getMarketplace([
                                'marketplace_item_id' => $marketplace_item_id
                            ]);
                            $existing_data= json_decode($existingEntry[$marketplace_item_id],true);
                            $this->model_shopmanager_product->deleteProductMarketplaceItemId($marketplace_item_id);
                            // Remettre en liste l'élément eBay
                            $result = $this->model_shopmanager_ebay->relist($marketplace_item_id);
                            $existing_data['marketplace_item_id']=$result['ItemID'];
                           
                            if ($result['Ack']!='Failure') {
                                //$this->model_shopmanager_product->editProductMarketplaceItemId($product_id,'');\
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
        $this->log->write('Response: ' . json_encode($json));
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
        $this->log->write('Response: ' . json_encode($json));
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
        $this->load->language('shopmanager/ebay');
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/tools');
        $json = [];
      //print("<pre>".print_r (value: '446:ebay' )."</pre>");
        $data = json_decode(file_get_contents('php://input'), true);
     //   $data['keyword']=$data['keyword']??'';
      

        if (!isset($data['keyword']) || empty($data['keyword'])) {
            $json['error'] = $this->language->get('error_keyword_missing');
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
                $data_result['column_name'] = $this->language->get('column_name');
                $data_result['column_condition'] = $this->language->get('column_condition');
                $data_result['column_category_id'] = $this->language->get('column_category_id');
                $data_result['column_picture'] = $this->language->get('column_picture');
                $data_result['column_category_name'] = $this->language->get('column_category_name');
                $data_result['column_product_id'] = $this->language->get('column_product_id');
           //     $data_result['keyword'] = $this->language->get('keyword');
                $data_result['keyword'] = $data['keyword']??'';
                 
                $json['success'] = true;
    
                // Charge la vue en tant que HTML
                $json['html'] = $this->load->view('shopmanager/ebay_search_results', $data_result);
            } else {
                $json['error'] = $this->language->get('error_no_items_found');
            }
        }
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
   
    public function getProduct() {
        $this->load->language('shopmanager/ebay'); // Charger le fichier de langue
        $json = [];

      //  if ($this->request->server['REQUEST_METHOD'] === 'POST') {
            // Récupérer les paramètres de la requête
            $marketplace_item_id = $this->request->post['marketplace_item_id'] ?? null;
          
            $marketplace_item_id="166640725049";

            // Vérifier les paramètres
            if (!$marketplace_item_id) {
                $json['success'] = false;
                $json['error'] = $this->language->get('error_missing_parameters');
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
                  //  $json['error'] = $this->language->get('error_no_values');
                    $json['success'] = true;
                    $json['error'] = $marketplace_item_id ;
                }
            }
     //   } else {
            //$json['success'] = false;
            //$json['error'] = $this->language->get('error_invalid_request');
      //      $json['success'] = true;
     //       $json['error'] = $this->request->post['marketplace_item_id'] ;
    //    }

        // Retourner la réponse JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function validateMarketplace($marketplace_account_id) {
        return $this->config->get('ebay_bypass_enabled') && (int)$marketplace_account_id === 1;
    }

}

