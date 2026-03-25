<?php
class ControllerShopManagerShipping extends Controller {


    public function create_usps_acceptance() {
        $this->load->model('shopmanager/shipping');
        
        $apiUrl = 'https://api.usps.com/scan-forms/v3/scan-form';

       
            $trackingNumbers = ['9400108105464382564745'];

        $data = $this->model_shopmanager_shipping->prepareScanFormData($trackingNumbers);
        $response = $this->model_shopmanager_shipping->sendUSPSRequest($apiUrl, $data);

       /* if ($response['status'] == 201) {
            $responseBody = json_decode($response['body'], true);
            echo 'SCAN Form créé avec succès.';
            $this->model_shopmanager_shipping->savePDF($responseBody['SCANFormImage']);
        } else {
            echo 'Erreur: ' . $response['body'];
        }*/
    }

    public function get_shipping(){
        $this->load->model('shopmanager/shipping');
        $this->load->model('shopmanager/product');
        $this->load->model('shopmanager/catalog/category');
        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            $data = json_decode(file_get_contents('php://input'), true);
      //      $data =json_decode('{"product_id":"25917","length":"5","width":"4","height":"3","weight":"0.25"}',true);
            // Log des données reçues
            $this->log->write('Data received: ' . print_r($data, true));
       //     $data['weight']=.25;
      //   $data['length']=10;
      //  $data['width']=4;
      //     $data['height']=1;
       //    $data['product_id']=13647;
            // Vérification des paramètres
            if (isset($data['product_id']) && isset($data['weight']) &&
                isset($data['length'])  && isset($data['width']) && isset($data['height']) ) {
                $product_info = $this->model_shopmanager_product->getProduct($data['product_id']);
                $product_info['weight']=$data['weight'];
                $product_info['length']=$data['length'];
                $product_info['width']=$data['width'];
                $product_info['height']=$data['height'];
                // Categories
		  
                $categories = $this->model_shopmanager_product->getProductCategories($data['product_id']);

                foreach ($categories as $category_id) {
                    $category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
                    //print("<pre>".print_r ($category_info,true )."</pre>");
                    if ($category_info) {

                        if( $category_info['leaf']=='1'){
                            $product_info['category_id']=$category_info['category_id'];
                            break;
                        }
                    }
                }
            }elseif (isset($data['category_id']) && isset($data['weight']) &&
            isset($data['length'])  && isset($data['width']) && isset($data['height']) ){
                $product_info['weight']=$data['weight'];
                $product_info['length']=$data['length'];
                $product_info['width']=$data['width'];
                $product_info['height']=$data['height'];
                $product_info['category_id']=$data['category_id'];

            } else {
                $json['success'] = false;
                $json['message'] = 'Méthode de requête non autorisée.';
            }
                        // Calcul des tarifs d'expédition
                    $shippingRates = $this->model_shopmanager_shipping->calculateShippingRates($product_info);
                    $product_info['shipping_cost'] =  $shippingRates['shipping_cost'];
                    $product_info['shipping_carrier'] = $shippingRates['shipping_carrier'];
                    // Mise à jour des tarifs dans la base de données
            //     $this->model_shopmanager_product->editProduct($data['product_id'],$product_info);

                    if ($shippingRates) {
                        $json['success'] = true;
                        $json['message'] = $shippingRates;
                    } else {
                        $json['success'] = false;
                        $json['message'] = '89:Erreur lors de la communication avec le modèle AI.';
                    }
                } else {
                    $json['success'] = false;
                    $json['message'] = 'Paramètres invalides.';
                }
        
      //print("<pre>".print_r ($json,true )."</pre>");
        // Log de la réponse finale
        $this->log->write('Response: ' . json_encode($json));

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    
}
?>
