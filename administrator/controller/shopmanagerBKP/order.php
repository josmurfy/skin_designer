<?php
class ControllerShopmanagerOrder extends Controller {
    public function index() {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        $this->load->language('shopmanager/order');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('shopmanager/order');
        $this->load->model('shopmanager/tools');
        $this->getList();
    }

    protected function getList() {

        $data['breadcrumbs'] = array();

        $url='';

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('shopmanager/order', 'token=' . $this->session->data['token'] . $url, true)
		);

        $data['print_report'] = $this->url->link('shopmanager/order', 'type_report=order&token=' . $this->session->data['token'] . $url, true);
		$data['process'] = $this->url->link('shopmanager/order/updateQuantity', 'token=' . $this->session->data['token'] . $url, true);

        $data['orders'] = array();

        $orders = $this->model_shopmanager_order->getOrders();
      

        foreach ($orders as $order) {
        //print("<pre>".print_r ($order,true )."</pre>"); 
           // die();
            $data['orders'][] = array(
                'sales_record_number' => $order['sales_record_number'],
                'image'             => $order['image'],
                'sku'               => $order['sku'],
                'customlabel'       => $order['customlabel'],
                'customer'     => $order['customer'],
                'adress'     => $order['adress'].' '. $order['city'],
                'adress2'     => $order['zipcode'].' '. $order['state'].' '. $order['country'],
                'name'         => $order['name'],
                'needed_quantity'          => $order['needed_quantity'],
                'price'         => $order['price'],
                'product_id'        => $order['product_id'],
                'quantity'          => $order['quantity'],
                'location'          => $order['location'],
                'unallocated_quantity' => $order['unallocated_quantity'],
                'total'             => $order['total'],
                'order_id'          => $order['order_id'],
                'country'          => $order['country'],
                'platform'          => $order['platform'],
                'transaction_site_id'          => $order['transaction_site_id'],
                'com'          => $order['com'],
                
             //   'date_added'        => $order['date_added'],
             //   'date_modified'     => $order['date_modified'],
            //    'view'              => $this->url->link('shopmanager/order/info', 'order_id=' . $order['order_id'], true),
             //   'edit'              => $this->url->link('shopmanager/order/edit', 'order_id=' . $order['order_id'], true)
            );
        }

        $sortCriteria =
        array('sales_record_number' => array(SORT_ASC, SORT_NUMERIC)
        );

        $data['orders'] = $this->model_shopmanager_tools->MultiSort($data['orders'], $sortCriteria, TRUE);

        $sortCriteria =
        array('sku' => array(SORT_ASC, SORT_NUMERIC)
        );

        $orders_sorted = $this->model_shopmanager_tools->MultiSort($data['orders'], $sortCriteria, TRUE);
        $orders_sorted = $this->model_shopmanager_tools->MultiSort($data['orders'], $sortCriteria, TRUE);

        $temp_array = array();
        
        foreach ($orders_sorted as $order_sorted) {
            $sku = $order_sorted['sku'] ?? null;
        
            if (!$sku) {
                continue; // Ignore les entrées sans SKU
            }
        
            if (!isset($temp_array[$sku])) {
                // Première occurrence du SKU : on stocke l'entrée complète
                $temp_array[$sku] = $order_sorted;
            } else {
                // Si le SKU existe déjà, on additionne les quantités
                $temp_array[$sku]['needed_quantity'] += $order_sorted['needed_quantity'];
            }
        }
        
        // Convertir en tableau indexé pour la suite du traitement
        $orders_sorted = array_values($temp_array);
        
        $sortCriteria = array('location' => array(SORT_ASC, SORT_STRING));
        $data['orders_sorted'] = $this->model_shopmanager_tools->MultiSort($orders_sorted, $sortCriteria, TRUE);
        


        
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_orders'] = $this->language->get('text_orders');
        $data['text_inventaire'] = $this->language->get('text_inventaire');
        $data['text_no_orders'] = $this->language->get('text_no_orders');
        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_image'] = $this->language->get('column_image');
        $data['column_marketplace'] = $this->language->get('column_marketplace');
        $data['column_sku'] = $this->language->get('column_sku');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_product_name'] = $this->language->get('column_product_name');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_location'] = $this->language->get('column_location');
        $data['column_unallocated_quantity'] = $this->language->get('column_unallocated_quantity');
        $data['column_needed_quantity'] = $this->language->get('column_needed_quantity');
        $data['column_action'] = $this->language->get('column_action');
        $data['button_process'] = $this->language->get('button_process');
        $data['button_edit'] = $this->language->get('button_edit');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
		$data['wait_popup'] = $this->load->controller('shopmanager/wait_popup');
		$data['marketplace_error_popup'] = $this->load->controller('shopmanager/marketplace_error_popup');
		$data['alert_popup'] = $this->load->controller('shopmanager/marketplace_popup');

      
        if (isset($this->request->get['type_report'])) {
            $this->response->setOutput($this->load->view('shopmanager/order_print_report', $data));
        }else{
            $this->response->setOutput($this->load->view('shopmanager/order_list', $data));
        }
       
    }

    public function updateQuantity() {
        // Charger le modèle qui gère les produits
        $this->load->model('shopmanager/product');
        $this->load->model('shopmanager/order');
        // Vérifier si les données nécessaires sont envoyées via POST
        if (isset($this->request->post['vendu']) && is_array($this->request->post['vendu'])) {
            //print("<pre>".print_r ($this->request->post,true )."</pre>");
            // Mettre à jour la quantité du produit dans la base de données
            $this->model_shopmanager_order->updateQuantity($this->request->post);
    
            // Si marketplace_item_id n'est pas nul ou 0, mettre à jour la quantité sur eBay
            $this->response->redirect($this->url->link('shopmanager/order', 'token=' . $this->session->data['token'], true));

        } else {
            // En cas de données manquantes, renvoyer une erreur
        //    $json['success'] = false;
         //   $json['message'] = 'Des données sont manquantes pour effectuer la mise à jour.';
        }
    
        // Retourner la réponse au format JSON
       // $this->response->addHeader('Content-Type: application/json');
        //$this->response->setOutput(json_encode($json));
    }
}
?>
