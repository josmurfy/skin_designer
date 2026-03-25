<?php
class ControllerShopmanagerMarketplace extends Controller {
public function addToMarketplace(){
	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	$this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/product');
    $this->load->model('shopmanager/marketplace');
 
	$json = array();

	// Toujours vérifier la méthode de requête
	if (isset($json)) {
		if (isset($this->request->post)) {
		//	//print("<pre>".print_r ($this->request->post,true )."</pre>");
			$product_id = $this->request->post['product_id'] ;
		//	$quantity = $this->request->post['quantity'] + $this->request->post['unallocated_quantity'] ;
			$marketplace_account_id = $this->request->post['marketplace_account_id'];
			$marketplace_id = $this->request->post['marketplace_id']??9 ;
		   
		   //print("<pre>".print_r ($data,true )."</pre>");
			
			// Mise à jour des tarifs dans la base de données
			$result=$this->model_shopmanager_marketplace->addToMarketplace($product_id,$marketplace_account_id);
	  //print("<pre>".print_r ($result,true )."</pre>");
	//	  die();
		if (isset($result['ErrorLanguage'])) {
			$json['error'] = false;
			$json['message'] = json_encode($result);
		}elseif (isset($result['Ack']) && $result['Ack']!='Failure') {
			
				$data = array(
					'customer_id' => 10,
					'product_id' =>$product_id,
					'marketplace_id'=>$marketplace_id,
					'marketplace_account_id'=>$marketplace_account_id,
					'marketplace_item_id' => $result['ItemID'],
					'quantity_listed' => $result['quantity_listed'],
					'quantity_sold' => $result['quantity_sold'],
                    'currency' => '',
                    'price' => 0,
                    'category_id' => 0,
                    'specifics'=> '',
					'error' => '',
                    
				);

				$this->model_shopmanager_marketplace->addProductMarketplace($data);
                $this->model_shopmanager_marketplace->syncMarketplaceProduct($result['ItemID']);
				$json['success'] = true;
				$json['marketplace_item_id'] = $result['ItemID'];
				$json['message'] = $result; 
			}elseif (isset($result['error']) ){
				$json['error'] = true;
				$json['message'] = json_encode($result['error']);
			} else {
				$json['error'] = true;
				$json['message'] = json_encode($result);
			
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

public function editQuantityToMarketplace(){
	ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
	$this->load->model('shopmanager/ebay');
	$this->load->model('shopmanager/product');
    $this->load->model('shopmanager/marketplace');
 
	$json = array();

	// Toujours vérifier la méthode de requête
	if (isset($json)) {
		if (isset($this->request->post)) {
		//	//print("<pre>".print_r ($this->request->post,true )."</pre>");
			$product_id = $this->request->post['product_id'] ;
		//	$quantity = $this->request->post['quantity'] + $this->request->post['unallocated_quantity'] ;
			$marketplace_account_id = $this->request->post['marketplace_account_id'];
			
		   
		   //print("<pre>".print_r ($data,true )."</pre>");
			
			// Mise à jour des tarifs dans la base de données
			$result=$this->model_shopmanager_marketplace->editQuantityToMarketplace($product_id,$marketplace_account_id);
	  //print("<pre>".print_r ($result,true )."</pre>");
	//	  die();
		if (isset($result['ErrorLanguage'])) {
			$json['error'] = false;
			$json['message'] = json_encode($result);
		}elseif (isset($result['Ack']) && $result['Ack']!='Failure') {
			
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
public function editMarketplaceBulk(){

	ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
	$this->load->model('shopmanager/marketplace');

	$marketplace_accounts_id = $this->model_shopmanager_marketplace->getProducts();
	//print("<pre>".print_r ($marketplace_accounts_id,true )."</pre>");

	foreach($marketplace_accounts_id as $marketplace_account){
		if(isset($marketplace_account['marketplace_item_id'])){
			$this->model_shopmanager_marketplace->editToMarketplace($marketplace_account['product_id'], $marketplace_account['marketplace_account_id']);
	
			//print("<pre>".print_r ($marketplace_accounts_id,true )."</pre>");
		}
	}
}


}