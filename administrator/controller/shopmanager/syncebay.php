<?php
namespace Opencart\Admin\Controller\Shopmanager;

class Syncebay extends \Opencart\System\Engine\Controller {
    public function index(): void {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->model('shopmanager/catalog/product'); 
        $this->load->model('shopmanager/ebay');
    
        $limit = 10; // Nombre de produits traités par exécution
        $offset = 0;//isset($this->request->get['offset']) ? (int)$this->request->get['offset'] : 0;
    
        // Récupérer un lot de produits à synchroniser
        $products = $this->model_shopmanager_catalog_product->getProductsToSyncEbay($limit, $offset);
    
        if (empty($products)) {
            echo "✅ Synchronisation terminée. Aucun produit restant.";
            return;
        }
    
        foreach ($products as $product) {
            $product_id = $product['product_id'];
            $marketplace_item_id = $product['marketplace_item_id'];
    
            // Mise à jour de la quantité sur eBay
       //     $ebayResponse = $this->model_ebay_ebay->updateQuantity($marketplace_item_id, $product['quantity'] + $product['unallocated_quantity']);
            $ebayResponse = $this->model_shopmanager_ebay->editQuantity($marketplace_item_id, $product['quantity'] + $product['unallocated_quantity'],0,$product_id);
    
            // Stocker la réponse eBay dans ebay_json
            $this->db->query("UPDATE " . DB_PREFIX . "product SET ebay_json = '" . $this->db->escape(json_encode($ebayResponse)) . "' WHERE product_id = " . (int)$product_id);
        }
    
        // Re-lancer le processus pour le lot suivant
        $nextOffset = $offset + $limit;
     //   echo "Traitement de $limit produits terminé. Prochain lot : $nextOffset...<br>";
    //print("<pre>".print_r ($ebayResponse,true )."</pre>");

     
       // echo '<script>setTimeout(() => { window.location.href = "' . $this->response->redirect('shopmanager/Syncebay', 'user_token=' . $this->session->data['user_token'].'&offset=' . $nextOffset, true) . '"; }, 5000);</script>';
       echo '<script>setTimeout(() => { window.location.href = "' . 
       html_entity_decode($this->url->link('shopmanager/syncebay', 'user_token=' . $this->session->data['user_token'] . '&offset=' . $nextOffset, true)) . 
       '"; }, 5000);</script>';
    
      
		//	$this->response->redirect($this->url->link('shopmanager/catalog/product/edit', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }
}
