<?php
    require_once '/home/n7f9655/public_html/phoenixliquidation/vendor/autoload.php';
    use DTS\eBaySDK\Sdk;
    use DTS\eBaySDK\Trading\Services\TradingService;
    use DTS\eBaySDK\Constants\SiteIds;


class ModelCustomUpdateOrder extends Model {



    public function updateOrder($data) {
        if (is_array($data['vendu'])) {
            foreach ($data['vendu'] as $vendu) {
                $itemvendu = explode(",", $vendu);
                $product_id_tmp = explode("_", $itemvendu[0]);
                $itemvendu[0] = $product_id_tmp[0];

                $sql = "UPDATE " . DB_PREFIX . "product SET quantity = quantity - '" . (int)$itemvendu[1] . "', ebay_last_check = NOW() WHERE product_id = '" . (int)$itemvendu[0] . "'";
                $this->db->query($sql);

                if ($itemvendu[2] - $itemvendu[1] < 0) {
                    $quantitemagasin = $itemvendu[2] - $itemvendu[1];
                    $itemvendu[1] = $itemvendu[2];
                    $sql = "UPDATE " . DB_PREFIX . "product SET unallocated_quantity = unallocated_quantity + '" . (int)$quantitemagasin . "' WHERE product_id = '" . (int)$itemvendu[0] . "'";
                    $this->db->query($sql);
                }

                $sql = "UPDATE " . DB_PREFIX . "etsy_products_list SET update_flag = '1' WHERE id_product = '" . (int)$itemvendu[0] . "'";
                $this->db->query($sql);

                $sql = "SELECT ebay_id, p.unallocated_quantity, p.quantity as quantite_entrepot FROM " . DB_PREFIX . "product p WHERE pm.product_id = '" . (int)$itemvendu[0] . "'";
                $query = $this->db->query($sql);

                $data2 = $query->row;
                $quantite_inventaire = $data2['unallocated_quantity'] + $data2['quantite_entrepot'];

                // Appel à une fonction personnalisée pour mettre à jour eBay
                $this->updateToEbay($quantite_inventaire, $data2['ebay_id'], $itemvendu[0]);
            }
        }
    }
    public function getEbayOrders($date_transaction, $page = 1, &$orders = []) {

        $connectionapi = [
            'EBAYTOKEN' => 'v^1.1#i^1#p^3#I^3#f^0#r^1#t^Ul4xMF8yOkUxMEU4MjEyNkE1Q0ExQUVDREU5MzkyRjRCQkIzMDlDXzBfMSNFXjI2MA==',
            'EBAYAPIDEVNAME' => '73b8492a-f471-4170-86b8-ce9e6e2d6796',
            'EBAYAPIAPPNAME' => '73b8492a-f471-4170-86b8-ce9e6e2d6796',
            'APICERTNAME' => 'PRD-f78dd8ce63e4-212d-4ac1-8aa3-d2ad',
            'APIEBAYURL' => 'https://api.ebay.com/ws/api.dll',
            'APIUPSURL' => 'https://www.ups.com/ups.app/xml/Rate',
            'APIUPSTOKEN' => 'DD9F9AE20FFC7DD5',
            'APIUPSUSERID' => 'jonathangervais',
            'APIUPSPASSWORD' => 'jnthngrvs01$$',
            'APIUSPSURL' => 'https://secure.shippingapis.com/ShippingApi.dll?',
            'APIUSPSUSERID' => '209PHOEN3821',
        ];
   
          // Configuration du SDK eBay
    $config = [
        'production' => [
            'credentials' => [
                'appId'      => $connectionapi['EBAYAPIAPPNAME'],
                'certId'     => $connectionapi['APICERTNAME'],
                'devId'      => $connectionapi['EBAYAPIDEVNAME'],
              
            ],
            
        ],
        'authToken'  => $connectionapi['EBAYTOKEN'],
        'sandbox' => false
    ];

    // Initialisation du SDK eBay
   // Initialisation du SDK eBay
$sdk = new Sdk($config);

// Création du service Trading
$service = new TradingService([
    'credentials' => $config['production']['credentials'],
    'sandbox'     => $config['sandbox'],
    'siteId'      => SiteIds::US, // Spécifiez le site eBay approprié
    'authToken'   => $config['authToken'],
]);
   //print("<pre>".print_r ($service,true )."</pre>");
    // Configuration de la requête pour obtenir les commandes
    $request = new \DTS\eBaySDK\Trading\Types\GetOrdersRequestType();
    $request->OrderRole = \DTS\eBaySDK\Trading\Enums\TradingRoleCodeType::C_SELLER;
    $request->OrderStatus = \DTS\eBaySDK\Trading\Enums\OrderStatusCodeType::C_COMPLETED;
    $request->CreateTimeFrom = new \DateTime($date_transaction);
    $request->CreateTimeTo = new \DateTime();
    $request->Pagination = new \DTS\eBaySDK\Trading\Types\PaginationType();
    $request->Pagination->EntriesPerPage = 1;
    $request->Pagination->PageNumber = $page;
    $request->SortingOrder = \DTS\eBaySDK\Trading\Enums\SortOrderCodeType::C_DESCENDING;
  //print("<pre>".print_r ($request,true )."</pre>"); 
    try {
        // Exécution de la requête
        $response = $service->getOrders($request);
       
        if (isset($response->OrderArray)) {
       //print("<pre>".print_r ($response,true )."</pre>");
            $orders = $response->OrderArray->Order;
            
     //       $json = json_encode($response); // Convertit l'objet en JSON
     //       $array = json_decode($json, true); // Convertit le JSON en tableau associatif
     //print("<pre>".print_r ( $array,true )."</pre>");

            foreach ($orders as $order) {
             
       // Exécution de la requête

    //print("<pre>".print_r ( $order->TransactionArray->Transaction,true )."</pre>");
    //   echo $order->Item;
 //print("<pre>".print_r ($order['Item'],true )."</pre>");

               if ($order->OrderStatus != 'Cancelled') {
                  $transactionArr = [$order->TransactionArray->Transaction];//is_array($order->TransactionArray->Transaction) ? $order->TransactionArray->Transaction : [$order->TransactionArray->Transaction];
                //print("<pre>".print_r ( $transactionArr,true )."</pre>");
                   foreach ($transactionArr as $transaction) {
                 
                    if (isset($transaction[0])) {
                        $transaction = $transaction[0];
                   } else {
                        $transaction = $transaction; // Sinon, utiliser directement $transactionArr
                    }
                   
                    $item = $transaction->Item ;
                  //print("<pre>".print_r ($transaction,true )."</pre>");
                    //print("<pre>".print_r ($transaction,true )."</pre>");  
                    // Vérifier si SKU est présent et n'est pas un tableau
                        // Vérifier le SKU pour le traitement spécifique
                        if (strpos($item->SKU, 'COM_', 0) === false) {
                            // Accéder aux détails d'expédition
                            $shipmentTrackingDetails = is_array($transaction->ShippingDetails->ShipmentTrackingDetails) ? $transaction->ShippingDetails->ShipmentTrackingDetails[0] : $transaction->ShippingDetails->ShipmentTrackingDetails;
                            $shipmentTrackingNumber = isset($shipmentTrackingDetails->ShipmentTrackingNumber) ? $shipmentTrackingDetails->ShipmentTrackingNumber : '';
                            
                            // Vérifier si le numéro de suivi est vide
                            if (empty($shipmentTrackingNumber)) {
                                // Construire le tableau d'ordre pour OpenCart
                                $ebayOrder = [
                                    'SalesRecordNumber' => isset($order->ShippingDetails->SellingManagerSalesRecordNumber) ? $order->ShippingDetails->SellingManagerSalesRecordNumber : '',
                                    'EbayId' => isset($order->OrderID) ? $order->OrderID : '',
                                    'BuyerFullname' => isset($order->ShippingAddress->Name) ? $order->ShippingAddress->Name : '',
                                    'BuyerAddress1' => isset($order->ShippingAddress->Street1) ? $order->ShippingAddress->Street1 : '',
                                    'BuyerCity' => isset($order->ShippingAddress->CityName) ? $order->ShippingAddress->CityName : '',
                                    'BuyerState' => isset($order->ShippingAddress->StateOrProvince) ? $order->ShippingAddress->StateOrProvince : '',
                                    'BuyerZip' => isset($order->ShippingAddress->PostalCode) ? $order->ShippingAddress->PostalCode : '',
                                    'Country' => isset($order->ShippingAddress->Country) ? $order->ShippingAddress->Country : '',
                                    'Phone' => isset($order->ShippingAddress->Phone) ? $order->ShippingAddress->Phone : '',
                                    'products_total' => isset($order->Subtotal->value) ? $order->Subtotal->value : '',
                                    'total' => isset($order->Total->value) ? $order->Total->value : '',
                                    'CustomLabel' => isset($item->SKU) ? $item->SKU : '',
                                    'Quantity' => isset($transaction->QuantityPurchased) ? $transaction->QuantityPurchased : '',
                                    'ItemTitle' => isset($item->Title) ? $item->Title : '',
                                    'SalePrice' => isset($transaction->TransactionPrice->value) ? $transaction->TransactionPrice->value : '', 
                                ];
   
                                // Requête SQL pour OpenCart
                                $productId = $ebayOrder['CustomLabel'];
                            
                                $sql = "SELECT * FROM " . DB_PREFIX . "product p 
                                        JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id 
                                        WHERE pd.language_id = 1 AND p.product_id = '" . (int)$productId . "'";
                                $result = $this->db->query($sql);
                                $data = $result->row;
                                $ebayOrder['locationanc'] = isset($data['location']) ? $data['location'] : '';
                                $ebayOrder['quantiteanc'] = isset($data['quantity']) ? $data['quantity'] : 0;
   
                                // Ajouter l'ordre à la liste des commandes
                                $orders[] = $ebayOrder;
                            }
                        }
                  
                   }
               }
           





            }

            // Pagination si nécessaire
          //print("<pre>".print_r ($response,true )."</pre>");  
            $totalPages = $response->PaginationResult->TotalNumberOfPages;
            $currentPage = $page;

            if ($currentPage < $totalPages) {
                $this->getEbayOrders($date_transaction, $page + 1, $orders);
            }
        }
  
    } catch (\Exception $e) {
        // Gestion des autres exceptions
        echo 'Erreur générale : ' . $e->getMessage();
    }       
     
        

        
    return $orders;
 }
        
    

    protected function updateToEbay($quantity, $ebay_id, $product_id) {
        // Implémentez ici la logique pour mettre à jour eBay
    }
}
?>
