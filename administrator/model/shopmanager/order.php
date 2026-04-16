<?php
namespace Opencart\Admin\Model\Shopmanager;


class Order extends \Opencart\System\Engine\Model {

/**
 * Get a mysqli connection to the sister site's database.
 * Uses config constants — no hardcoded credentials.
 */
private function getSisterDbConnection(): \mysqli|false {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $is_phoenixsupplies = strpos($host, 'phoenixsupplies') !== false;

    // If on phoenixsupplies, sister = phoenixliquidation and vice versa
    $sister_db = $is_phoenixsupplies ? DB_DATABASE : DB_SISTER_DATABASE;

    $db = @mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, $sister_db, (int)DB_PORT);

    if (!$db) {
        $this->log->write('Order cross-DB connection failed: ' . mysqli_connect_error());
        return false;
    }

    mysqli_set_charset($db, 'utf8mb4');
    return $db;
}

/**
 * Query the sister DB for a product by ID (read-only).
 */
private function getSisterProduct(int $product_id): array|false {
    $db = $this->getSisterDbConnection();
    if (!$db) {
        return false;
    }

    $sql = "SELECT p.product_id, p.quantity, p.unallocated_quantity, p.sku, p.image, p.location
            FROM " . DB_PREFIX . "product p
            JOIN " . DB_PREFIX . "product_description pd ON p.product_id = pd.product_id
            WHERE pd.language_id = 1 AND p.product_id = " . (int)$product_id;

    $req = mysqli_query($db, $sql);
    $data = $req ? mysqli_fetch_assoc($req) : false;
    mysqli_close($db);

    return $data ?: false;
}

/**
 * Update quantity on the sister DB for a product.
 */
private function updateSisterQuantity(int $product_id, int $quantity, int $unallocated_quantity): bool {
    $db = $this->getSisterDbConnection();
    if (!$db) {
        return false;
    }

    $sql = "UPDATE " . DB_PREFIX . "product SET quantity = " . (int)$quantity
         . ", unallocated_quantity = " . (int)$unallocated_quantity
         . " WHERE product_id = " . (int)$product_id;

    $result = mysqli_query($db, $sql);
    mysqli_close($db);

    return (bool)$result;
}

/**
 * Detect which site we're running on.
 */
private function detectSite(): array {
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return [
        'is_phoenixsupplies'   => strpos($host, 'phoenixsupplies') !== false,
        'is_phoenixliquidation' => strpos($host, 'phoenixliquidation') !== false,
    ];
}

public function getOrder($order_id = null){

    $this->load->model('shopmanager/ebay');

    $order = $this->model_shopmanager_ebay->getOrder($order_id);

    return $order;
}

public function getOrders(array $data = []): array {

    $this->load->model('shopmanager/ebay');
    $this->load->model('shopmanager/catalog/product');
    $this->load->model('shopmanager/card/card');

    // Handle date filters
    if (!empty($data['filter_date_start']) && !empty($data['filter_date_end'])) {
        // Both dates: use start date for query
        $date_transaction = gmdate('Y-m-d\TH:i:s.000\Z', strtotime($data['filter_date_start']));
    } elseif (!empty($data['filter_date_start'])) {
        // Only start date: get orders from this date onwards
        $date_transaction = gmdate('Y-m-d\TH:i:s.000\Z', strtotime($data['filter_date_start']));
    } elseif (!empty($data['filter_date_end'])) {
        // Only end date: go back 90 days from end date to capture range
        $date_transaction = gmdate('Y-m-d\TH:i:s.000\Z', strtotime($data['filter_date_end'] . ' -90 days'));
    } elseif (!empty($data['filter_order_id_start']) || !empty($data['filter_order_id_end'])) {
        // Order ID filters: go back 90 days to capture orders
        $date_transaction = gmdate('Y-m-d\TH:i:s.000\Z', strtotime("-90 days"));
    } else {
        // No filters: default to last 10 days
        $date_transaction = gmdate('Y-m-d\TH:i:s.000\Z', strtotime("-10 days"));
    }

    $orders = $this->model_shopmanager_ebay->getOrders($date_transaction);

    $orders_output = [];
    $i = 0;
    
    // Prepare date range filters
    $filter_start_timestamp = !empty($data['filter_date_start']) ? strtotime($data['filter_date_start'] . ' 00:00:00') : null;
    $filter_end_timestamp = !empty($data['filter_date_end']) ? strtotime($data['filter_date_end'] . ' 23:59:59') : null;
    
    // Prepare order_id range filters
    $filter_order_id_start = !empty($data['filter_order_id_start']) ? $data['filter_order_id_start'] : null;
    $filter_order_id_end = !empty($data['filter_order_id_end']) ? $data['filter_order_id_end'] : null;
    
    // Determine if we should filter only non-shipped orders
    // Only show non-shipped if NO dates AND NO order_id filters are set at all
    // If any date or order_id filter is set, show all orders (shipped and non-shipped), except cancelled
    $filter_non_shipped_only = empty($data['filter_date_start']) && empty($data['filter_date_end']) 
                                && empty($data['filter_order_id_start']) && empty($data['filter_order_id_end']);
    

    foreach($orders as $order){
		if ($order['OrderStatus']!='Cancelled'){
               //print("<pre>".print_r ($order,true )."</pre>");
            // Check date filter on CreatedTime
            if (isset($order['CreatedTime'])) {
                $order_timestamp = strtotime($order['CreatedTime']);
                
                // Apply date filters
                if ($filter_start_timestamp && $order_timestamp < $filter_start_timestamp) {
                    continue; // Skip orders before start date
                }
                if ($filter_end_timestamp && $order_timestamp > $filter_end_timestamp) {
                    continue; // Skip orders after end date
                }
            }
            
			if(isset($order['TransactionArray']['Transaction'][0])){
				$transactionArr = $order['TransactionArray']['Transaction'];
			}else{
				$transactionArr = $order['TransactionArray'];
			}
 
			foreach($transactionArr as $transaction){ 
				$item = $transaction['Item'];
				//print("<pre>".print_r ($item,true )."</pre>");

				// Detect card SKU early
				$raw_sku_check = isset($transaction['Variation']['SKU']) 
				    ? (string)$transaction['Variation']['SKU'] 
				    : (string)($item['SKU'] ?? '');
				$is_card_sku = (bool)preg_match('/^[A-Z]{2}_CARD_/', $raw_sku_check);

				// Check if order is shipped (has tracking)
				$has_tracking = isset($transaction['ShippingDetails']['ShipmentTrackingDetails'][0]) || isset($transaction['ShippingDetails']['ShipmentTrackingDetails']);

				// For cards: also consider shipped if eBay set ShippedTime (regular mail, no tracking)
				$is_card_shipped_no_tracking = $is_card_sku && !$has_tracking && !empty($order['ShippedTime']);
				
				// Skip shipped orders ONLY if we're filtering for non-shipped only
				if ($filter_non_shipped_only && ($has_tracking || $is_card_shipped_no_tracking)) {
					continue;
				}
				
				// Get sales_record_number for filtering
				$sales_record_number = $order['ShippingDetails']['SellingManagerSalesRecordNumber'];
				
				// Apply order_id (sales_record_number) range filters
				if ($filter_order_id_start && (int)$sales_record_number < (int)$filter_order_id_start) {
					continue; // Skip orders before start sales_record_number
				}
				if ($filter_order_id_end && (int)$sales_record_number > (int)$filter_order_id_end) {
					continue; // Skip orders after end sales_record_number
				}
				
				// If we reach here, include the order
				$orders_output[$i]['sales_record_number'] = $sales_record_number;
				$orders_output[$i]['order_id'] = $order['OrderID'];

				$shipping_address = $order['ShippingAddress'];

				$orders_output[$i]['customer'] = $shipping_address['Name'];
				$orders_output[$i]['adress'] = $shipping_address['Street1'];
				$orders_output[$i]['city'] = $shipping_address['CityName'];
				$orders_output[$i]['state'] = $shipping_address['StateOrProvince'];
				$orders_output[$i]['zipcode'] = $shipping_address['PostalCode'];
						$orders_output[$i]['country'] = $shipping_address['Country']; 
						$orders_output[$i]['Phone'] = $shipping_address['Phone'];
						$orders_output[$i]['products_total'] = $order['Subtotal'];
						$orders_output[$i]['total'] = $order['Total'];
                        $orders_output[$i]['platform'] = $transaction['Platform'];
                        $orders_output[$i]['transaction_site_id'] = $transaction['TransactionSiteID'];
				// Use variation SKU if this is a multi-variation listing
				$raw_sku = isset($transaction['Variation']['SKU']) 
				    ? (string)$transaction['Variation']['SKU'] 
				    : (string)$item['SKU'];
				$orders_output[$i]['customlabel'] = $raw_sku;
				$orders_output[$i]['needed_quantity'] = (int)$transaction['QuantityPurchased'];
				$orders_output[$i]['name'] = isset($transaction['Variation']['VariationTitle']) 
				    ? (string)$transaction['Variation']['VariationTitle'] 
				    : (string)$item['Title'];
				$orders_output[$i]['price'] = (float)$transaction['TransactionPrice'];
//echo "<pre>".print_r ($orders_output[$i]['customlabel'],true )."</pre>";
                        if (preg_match('/^[A-Z]{2}_CARD_/', $raw_sku)) {
                            // Card variation SKU: e.g. CA_CARD_5_73 → card_id = 73
                            $sku_parts = explode('_', $raw_sku);
                            $card_id = (int)end($sku_parts);
                            $orders_output[$i]['customlabel'] = $card_id;
                            $orders_output[$i]['com'] = "CARD_";
                            $website = "https://phoenixliquidation.ca/";
                        } elseif (strpos($orders_output[$i]['customlabel'], 'COM_') === 0) {
                            $orders_output[$i]['customlabel'] = str_replace("COM_", "", $orders_output[$i]['customlabel']);
                            $orders_output[$i]['com'] = "COM_";
                            $website = "https://phoenixsupplies.ca/";
                        } else {
                            $orders_output[$i]['com'] = "RET_";
                            $website = "https://phoenixliquidation.ca/";
                        }

                        // Determine the host and connect to the appropriate database
                        $site = $this->detectSite();


                        if ($orders_output[$i]['com'] == "CARD_") {
                            
                            $data = $this->model_shopmanager_card_card->getCard((int)$orders_output[$i]['customlabel']);
                        } elseif ($orders_output[$i]['com'] == "COM_" && $site['is_phoenixsupplies']) {
                            $data = $this->model_shopmanager_catalog_product->getProduct((int)$orders_output[$i]['customlabel']);
                        } elseif ($orders_output[$i]['com'] == "COM_" && $site['is_phoenixliquidation']) {

                            $data = $this->getSisterProduct((int)$orders_output[$i]['customlabel']);
                        } elseif ($orders_output[$i]['com'] == "RET_" && $site['is_phoenixliquidation']) {
                            $data = $this->model_shopmanager_catalog_product->getProduct((int)$orders_output[$i]['customlabel']);
                        } elseif ($orders_output[$i]['com'] == "RET_" && $site['is_phoenixsupplies']) {

                            $data = $this->getSisterProduct((int)$orders_output[$i]['customlabel']);
                        }
                      
                     //print("<pre>".print_r ($data,true )."</pre>");
                                $orders_output[$i]['image'] = '';
                                $orders_output[$i]['listing_id'] = '';

						if(!empty($data)){
                                if ($orders_output[$i]['com'] == "CARD_") {
                                    $orders_output[$i]['quantity'] = $data['quantity'];
                                    $orders_output[$i]['unallocated_quantity'] = 0;
                                    $orders_output[$i]['location'] = $data['location'] ?? '';
                                    $orders_output[$i]['sku'] = $data['sku'];
                                    $orders_output[$i]['product_id'] = $data['card_id'];
                                    $orders_output[$i]['listing_id'] = $data['listing_id'] ?? '';
                                    $orders_output[$i]['image'] = !empty($data['image_url']) ? '<img height="50" src="' . $data['image_url'] . '"/>' : '';
                                } else {
							$orders_output[$i]['quantity']=$data['quantity'];
                                $orders_output[$i]['location']=$data['location'];
							$orders_output[$i]['unallocated_quantity']=$data['unallocated_quantity'];
							$orders_output[$i]['sku']=$data['sku'];
                                $orders_output[$i]['product_id']=$data['product_id'];

                                $orders_output[$i]['image'] = ($data['image'] != '') ? '<img height="50" src="'.$website.'image/'.$data['image'].'"/>' : '';
                                }
	
					}
					$i++;
						
			}
        }
    }
    return $orders_output;
}

    public function updateQuantity($post = []) {
        // Charger le modèle qui gère les produits
        $this->load->model('shopmanager/catalog/product');
        $this->load->model('shopmanager/card/card');
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
       
        // Group orders by product_id to cumulate quantities
        $products_to_update = [];
        
        foreach($post['vendu'] as $vendu) {
            $itemvendu = explode(",", $vendu);
            $product_id_tmp = explode("_", $itemvendu[0]);
            $product_id = $product_id_tmp[0];
            $needed_quantity = (int)$itemvendu[1];
            $quantity = (int)$itemvendu[2];
            $unallocated_quantity = (int)$itemvendu[3];
            $com = $itemvendu[4];
            
            // Create unique key for each product
            $key = $com . $product_id;
            
            if (!isset($products_to_update[$key])) {
                // First occurrence of this product
                $products_to_update[$key] = [
                    'product_id' => $product_id,
                    'com' => $com,
                    'quantity' => $quantity,
                    'unallocated_quantity' => $unallocated_quantity,
                    'needed_quantity_total' => $needed_quantity
                ];
            } else {
                // Product already exists, accumulate needed_quantity
                $products_to_update[$key]['needed_quantity_total'] += $needed_quantity;
            }
        }
        
        // Now process each unique product once with total quantities
        foreach($products_to_update as $product_data) {
            $product_id = $product_data['product_id'];
            $needed_quantity = $product_data['needed_quantity_total'];
            $quantity = $product_data['quantity'];
            $unallocated_quantity = $product_data['unallocated_quantity'];
            $com = $product_data['com'];
			
            // Determine the host and connect to the appropriate database
            $site = $this->detectSite();

            if (strpos($com, 'CARD_') === 0) {
                // Card listing: update oc_card.quantity directly
                $quantity_final = $quantity - $needed_quantity;
                if ($quantity_final < 0) $quantity_final = 0;
                
                $this->model_shopmanager_card_card->updateCardQuantity((int)$product_id, $quantity_final);
                continue; // Skip product/eBay update for cards
            } elseif (strpos($com, 'COM_') === 0) {
                // Se connecter à la première base de données
                $product_id = str_replace("COM_", "", $product_id);
                $website = "https://phoenixsupplies.ca/";
            } else {
                // Se connecter à la deuxième base de données
                $website = "https://phoenixliquidation.ca/";
            }

            //print("<pre>".print_r ($needed_quantity,true )."</pre>");
            $quantity_final = $needed_quantity - $quantity;
            //print("<pre>".print_r ($quantity_final,true )."</pre>");
                $unallocated_quantity_final=($quantity_final>0)?$unallocated_quantity-$quantity_final:$unallocated_quantity;
                $quantity_final=($quantity_final<0)?$quantity-$needed_quantity:0;
                $quantity_total_final=$quantity+$unallocated_quantity-$needed_quantity;
                //print("<pre>".print_r ($quantity_total_final,true )."</pre>");

                if ($com == "COM_" && $site['is_phoenixsupplies']) {
                  
                    $this->model_shopmanager_catalog_product->updateQuantity((int)$product_id, $quantity_final);
                    $this->model_shopmanager_catalog_product->updateUnallocatedQuantity((int)$product_id, $unallocated_quantity_final);
                    
                } elseif ($com == "COM_" && $site['is_phoenixliquidation']) {

                    $this->updateSisterQuantity((int)$product_id, $quantity_final, $unallocated_quantity_final);
                   
                } elseif ($com == "RET_" && $site['is_phoenixliquidation']) {
                    $this->model_shopmanager_catalog_product->updateQuantity((int)$product_id, $quantity_final);
                    $this->model_shopmanager_catalog_product->updateUnallocatedQuantity((int)$product_id, $unallocated_quantity_final);
                    //print("<pre>".print_r (189,true )."</pre>");
                } elseif ($com == "RET_" && $site['is_phoenixsupplies']) {

                    $this->updateSisterQuantity((int)$product_id, $quantity_final, $unallocated_quantity_final);
                }

                // Only update eBay listing if product is LOCAL to this site
                // COM_ on phoenixliquidation = sister product → skip (product_id collides with local)
                // RET_ on phoenixsupplies  = sister product → skip
                $is_local_product = ($com == "COM_" && $site['is_phoenixsupplies'])
                                 || ($com == "RET_" && $site['is_phoenixliquidation'])
                                 || ($com != "COM_" && $com != "RET_");

                if ($is_local_product) {
                    $this->load->model('shopmanager/marketplace');
                    $marketplace_accounts_id = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id]);

                    foreach($marketplace_accounts_id as $marketplace_account_id=> $marketplace_account){
                        if(isset($marketplace_account['marketplace_item_id'])){
                            //print("<pre>".print_r ($marketplace_account,true )."</pre>");
                          $result = $this->model_shopmanager_marketplace->editQuantity($product_id, $marketplace_account_id);
                          //print("<pre>".print_r ($result,true )."</pre>");
                          //$result=[]; 
                        /*  if(isset($result['Errors']) && $result['Ack']=='Failure'){
    
                                $this->error['error']="eBay ERROR: <br>";
                                if(isset($result['Errors'][0])){
                                    foreach($result['Errors'] as $error){
                                        
    
                                        $this->error['error'] .= '************ '.$error['LongMessage'].'<br>';
    
                                    }
                                }else{
                                    $this->error['error'] .= '************ '.$result['Errors']['LongMessage'].'<br>';
    
                                }
                                $this->session->data['error'] = $this->error['error'];
    
                            }else{
                                unset($result['Errors']);
                            }*/
                        }
                    }
                } // End of is_local_product
                        
                    
        }  // End of foreach products_to_update
		
    }
    
    public function undoProductQuantity($post = []) {
        $this->load->model('shopmanager/catalog/product');
        $this->load->model('shopmanager/card/card');
        $this->load->model('shopmanager/ebay');
        $this->load->model('shopmanager/marketplace');
       
        $products_to_update = [];
        
        foreach($post['vendu'] as $vendu) {
            $itemvendu = explode(",", $vendu);
            $product_id_tmp = explode("_", $itemvendu[0]);
            $product_id = $product_id_tmp[0];
            $needed_quantity = (int)$itemvendu[1];
            $quantity = (int)$itemvendu[2];
            $unallocated_quantity = (int)$itemvendu[3];
            $com = $itemvendu[4];
            $location = isset($itemvendu[5]) ? trim($itemvendu[5]) : '';
            
            $key = $com . $product_id;
            
            if (!isset($products_to_update[$key])) {
                $products_to_update[$key] = [
                    'product_id' => $product_id,
                    'com' => $com,
                    'quantity' => $quantity,
                    'unallocated_quantity' => $unallocated_quantity,
                    'needed_quantity_total' => $needed_quantity,
                    'location' => $location
                ];
            } else {
                $products_to_update[$key]['needed_quantity_total'] += $needed_quantity;
            }
        }
        
        foreach($products_to_update as $product_data) {
            $product_id = $product_data['product_id'];
            $needed_quantity = $product_data['needed_quantity_total'];
            $quantity = $product_data['quantity'];
            $unallocated_quantity = $product_data['unallocated_quantity'];
            $com = $product_data['com'];
            $location = $product_data['location'];

            $site = $this->detectSite();

            if (strpos($com, 'CARD_') === 0) {
                // Card listing: restore oc_card.quantity directly
                $quantity_final = $quantity + $needed_quantity;
               
                $this->model_shopmanager_card_card->updateCardQuantity((int)$product_id, $quantity_final);
                continue; // Skip product/eBay update for cards
            } elseif (strpos($com, 'COM_') === 0) {
                $product_id = str_replace("COM_", "", $product_id);
            }

            // UNDO LOGIC: If location exists, add to quantity (allocated), otherwise add to unallocated
            if (!empty($location)) {
                // Product has a location, restore to allocated quantity
                $quantity_final = $quantity + $needed_quantity;
                $unallocated_quantity_final = $unallocated_quantity; // No change to unallocated
            } else {
                // No location, restore to unallocated quantity only
                $quantity_final = $quantity; // No change to allocated
                $unallocated_quantity_final = $unallocated_quantity + $needed_quantity;
            }
            
            $quantity_total_final = $quantity_final + $unallocated_quantity_final;

            if ($com == "COM_" && $site['is_phoenixsupplies']) {
                $this->model_shopmanager_catalog_product->updateQuantity((int)$product_id, $quantity_final);
                $this->model_shopmanager_catalog_product->updateUnallocatedQuantity((int)$product_id, $unallocated_quantity_final);
            } elseif ($com == "COM_" && $site['is_phoenixliquidation']) {
                $this->updateSisterQuantity((int)$product_id, $quantity_final, $unallocated_quantity_final);
            } elseif ($com == "RET_" && $site['is_phoenixliquidation']) {
                $this->model_shopmanager_catalog_product->updateQuantity((int)$product_id, $quantity_final);
                $this->model_shopmanager_catalog_product->updateUnallocatedQuantity((int)$product_id, $unallocated_quantity_final);
            } elseif ($com == "RET_" && $site['is_phoenixsupplies']) {
                $this->updateSisterQuantity((int)$product_id, $quantity_final, $unallocated_quantity_final);
            }

            // Only update eBay listing if product is LOCAL to this site
            $is_local_product = ($com == "COM_" && $site['is_phoenixsupplies'])
                             || ($com == "RET_" && $site['is_phoenixliquidation'])
                             || ($com != "COM_" && $com != "RET_");

            if ($is_local_product) {
                $this->load->model('shopmanager/marketplace');
                $marketplace_accounts_id = $this->model_shopmanager_marketplace->getMarketplace(['product_id' => $product_id]);

                foreach($marketplace_accounts_id as $marketplace_account_id=> $marketplace_account){
                    if(isset($marketplace_account['marketplace_item_id'])){
                        $result = $this->model_shopmanager_marketplace->editQuantity($product_id, $marketplace_account_id);
                    }
                }
            }
        }
    }
}
