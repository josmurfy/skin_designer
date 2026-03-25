<?php

include_once(DIR_SYSTEM . 'library/kbetsy/KbOAuth.php');
include_once(DIR_SYSTEM . 'library/kbetsy/EtsyApi.php');
include_once(DIR_SYSTEM . 'library/kbetsy/RequestValidator.php');
include_once(DIR_SYSTEM . 'library/kbetsy/EtsyMain.php');
include_once(DIR_SYSTEM . 'library/kbetsy/oauth_client.php');
include_once(DIR_SYSTEM . 'library/kbetsy/http.php');

class ControllerKbetsyOrder extends Controller {

    private $syncType = 'SyncOrders';
    private $syncMethod = '';
    private $syncError = false;

    public function index() {
        @ini_set('memory_limit', -1);
        @ini_set('max_execution_time', -1);
        @set_time_limit(0);

        if ($this->config->get('kbetsy_demo_flag') == 0) {
            $secure_key = $this->config->get('kbetsy_secure_key');
            if (!empty($this->request->get['secure_key']) && $secure_key == $this->request->get['secure_key']) {
                $this->load->model('kbetsy/cron');
                $this->model_kbetsy_cron->auditLogEntry("Order Sync Started", $this->syncType);

                $settings = $this->config->get('etsy_general_settings');
                if (isset($settings['enable']) && $settings['enable'] == 1) {
                    $etsy_access_token = $this->config->get('etsy_access_token');
                    $etsy_access_token_secret = $this->config->get('etsy_access_token_secret');
                    if ($etsy_access_token && $etsy_access_token_secret) {
                        $this->syncOrdersListing();
                    } else {
                        $logEntry = "Please connect you store to etsy from general settings page!";
                        $this->model_kbetsy_cron->auditLogEntry($logEntry, $this->syncType);
                        echo $logEntry;
                    }
                } else {
                    $logEntry = "Module is not enabled. Kindly go to general settings page to enable the module.";
                    $this->model_kbetsy_cron->auditLogEntry($logEntry, $this->syncType);
                    echo $logEntry;
                }
                $this->model_kbetsy_cron->auditLogEntry("Order Sync Completed", $this->syncType);
            } else {
                echo "Secure key not matched.";
            }
        } else {
            echo "Sorry!!! You are not allowed to perform this action the demo version.";
        }
        die();
    }
    public function updatequantitytoebay($product_id) {
		$this->load->model('catalog/product');
		$product_info = $this->model_catalog_product->getProduct($product_id);
		$ebay_id=$product_info['ebay_id'];
		$this->load->model('catalog/ebay');
		$ebay_account_info = $this->model_catalog_ebay->getAPI();
		$updquantity=$product_info['quantity']+$product_info['unallocated_quantity'];
		//echo $updquantity."allo".$ebay_id;
		//echo $ebay_account_info['ebay_connector_ebay_auth_token'];
		$post = '<?xml version="1.0" encoding="utf-8"?>
				<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					<RequesterCredentials>
						<eBayAuthToken>'.$ebay_account_info['ebay_connector_ebay_auth_token'].'</eBayAuthToken>
					</RequesterCredentials>
					<ErrorLanguage>en_US</ErrorLanguage>
					<WarningLevel>High</WarningLevel>
					<InventoryStatus>
					<ItemID>'.$ebay_id.'</ItemID>
					<Quantity>'.$updquantity.'</Quantity>
					</InventoryStatus>
				</ReviseInventoryStatusRequest>'; 

		$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 967",
					"X-EBAY-API-DEV-NAME: ".$ebay_account_info['ebay_connector_ebay_application_id'],
					"X-EBAY-API-APP-NAME: ".$ebay_account_info['ebay_connector_ebay_developer_id'],
					"X-EBAY-API-CERT-NAME: ".$ebay_account_info['ebay_connector_ebay_certification_id'],
					"X-EBAY-API-CALL-NAME: ReviseInventoryStatus",
					"X-EBAY-API-SITEID: 0" // 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_URL, "https://api.ebay.com/ws/api.dll");
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($connection);
		curl_close($connection);

		$xml = new SimpleXMLElement($response);
		//echo $post."allo"; 
	}
    private function syncOrdersListing() {
        $this->syncMethod = 'getOrders';
        $logEntry = 'Getting orders from Etsy.';
        $this->model_kbetsy_cron->auditLogEntry($logEntry, $this->syncMethod);

        $this->load->model('kbetsy/cron');
        $this->load->model('kbetsy/order');
        $this->load->model('kbetsy/product');
        $receiptsFetched = 0;

        //Get date to fetch orders from Etsy Marketplace
        $selectSQL = "SELECT MAX(date_added) as last_date FROM " . DB_PREFIX . "etsy_orders_list";
        $lastDateArray = $this->db->query($selectSQL);
        if ($lastDateArray->num_rows <= 0) {
            $lastDate = $lastDateArray->row['last_date'];
            $lastDate = date("Y-m-d H:i:s", strtotime(' -15 days'));
        } else {
            $lastDate = date("Y-m-d H:i:s", strtotime(' -5 days'));
        }
        $lastDate = strtotime($lastDate);
        $shop_id = $this->etsyGetShopDetails();
        if ($shop_id == false) {
            echo "Failed";
        } else {
            $etsyQueryString = array(
                'min_created' => $lastDate,
                'shop_id' => $shop_id
            );
            $etsyMain = $this->model_kbetsy_cron->createEtsyObject();
            $result = $etsyMain->sendRequest("findAllShopReceipts", array('params' => $etsyQueryString, 'data' => $etsyQueryString));
            if (isset($result['error'])) {
                $this->syncError = true;
                $this->model_kbetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                echo "Failed";
            } elseif (isset($result['results'])) {
                echo "result['results']";
                    //print("<pre>".print_r ($result['results'],true )."</pre>");
                if (!empty($result['results'])) {
                    $shopReceiptsList = $this->prepareReceiptFieldsList($result['results']);
                    echo "shopReceiptsList";
                    //print("<pre>".print_r ($shopReceiptsList,true )."</pre>");
                    foreach ($shopReceiptsList as $shopReceiptList) {
                        $orderResponse = $this->writeOrderIntoDb($shopReceiptList);
                        if ($orderResponse) {
                            if (!empty($orderResponse)) {
                                $receiptsFetched++;
                                $updateSQL = "UPDATE " . DB_PREFIX . "etsy_orders_list SET id_order = '" . $orderResponse . "' WHERE id_etsy_order = '" . $shopReceiptList['id_etsy_order'] . "'";
                                $this->db->query($updateSQL);
                            }
                        }
                    }
                }
            }
            if ($this->syncError == true) {
                echo "Success with some error(s). Refer to audit log for the details of the error.";
            } else {
                echo "Success";
            }
        }

        $logEntry = 'Total Orders Created: ' . $receiptsFetched;
        $this->model_kbetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
    }

    private function etsyGetUserDetails() {
        $this->syncMethod = 'SyncGetUserDetails';
        $etsyMain = $this->model_kbetsy_cron->createEtsyObject();
        $result = $etsyMain->sendRequest("getUser", array('params' => array('user_id' => '__SELF__')));
        if (isset($result['error'])) {
            $this->syncError = true;
            $this->model_kbetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
            return false;
        } elseif (isset($result['results'])) {
            return $result['results'][0]['user_id'];
        }
    }

    private function etsyGetShopDetails() {
        $this->syncMethod = 'SyncGetShopDetails';
        $etsyMain = $this->model_kbetsy_cron->createEtsyObject();
        $result = $etsyMain->sendRequest("findAllUserShops", array('params' => array('user_id' => '__SELF__')));
        if (isset($result['error'])) {
            $this->syncError = true;
            $this->model_kbetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
            return false;
        } elseif (isset($result['results'])) {
            return $result['results'][0]['shop_id'];
        }
    }

    private function prepareReceiptFieldsList($receiptDetails = array()) {
        $this->syncMethod = 'prepareReceiptFieldsList';
       
       //break;
        $orderDetails = array();
        if (!empty($receiptDetails) && count($receiptDetails) > 0) {
          //  echo "receiptDetails";
         //print("<pre>".print_r ($receiptDetails,true )."</pre>");
            foreach ($receiptDetails as $receiptDetail) {
                $dataExistenceResult = $this->db->query("SELECT count(*) as count FROM " . DB_PREFIX . "etsy_orders_list WHERE id_etsy_order = '" . $receiptDetail['receipt_id'] . "'");
                if ($dataExistenceResult->row['count'] == 0) {
                    //Get Transactions Details (Order Product Details)
                    $etsyMain = $this->model_kbetsy_cron->createEtsyObject();
                    $result = $etsyMain->sendRequest("findAllShop_Receipt2Transactions", array('params' => array('receipt_id' => $receiptDetail['receipt_id'])));
                    if (isset($result['error'])) {
                        $this->syncError = true;
                        $this->model_kbetsy_cron->auditLogEntry($result['error'], $this->syncMethod);
                    } elseif (isset($result['results'])) {

                        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_orders_list VALUES (NULL, 0, '" . $receiptDetail['receipt_id'] . "', '0', '" . $this->db->escape(date("Y-m-d H:i:s", $receiptDetail['creation_tsz'])) . "', NOW())");

                        $receiptTransactionsList = $result['results'];

                        //Get Country ID from Store Database
                        $orderCountry = $this->getStoreCountryID($receiptDetail['country_id']);

                        //Get State ID from Store Database
                        $orderState = $this->getStoreStateID($receiptDetail['state']);
                        //echo "orderState";
                       //print("<pre>".print_r ($orderState,true )."</pre>");
                        //Prepare Products Array for all ordered items
                        $productsArray = array();
                        foreach ($receiptTransactionsList as $receiptTransactionList) {
                            //Get Product ID from Etsy Product List Table

                            /* Set Update Flag to update on Etsy */
                            $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET update_flag = '1' WHERE listing_id = '" . $receiptTransactionList['listing_id'] . "' AND listing_status = 'Listed'");

                            $optionArray = array();
                            $productID = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id = '" . $receiptTransactionList['listing_id'] . "'");
                            if ($productID->num_rows > 0) {
                                $productDetails = $this->model_kbetsy_product->getProductByProductId($productID->row['id_product'], $this->config->get('config_language_id'));
                                $product_id = $productID->row['id_product'];
                                $product_model = $productDetails['model'];
                            } else {
                                $product_id = 0;
                                $product_model = "";
                            }

                            //Get Product Option ID
                            $variations = $receiptTransactionList['product_data']['property_values'];
                            if (!empty($variations)) {
                                $counter = 0;
                                foreach ($variations as $variation) {
                                    $product_option_id = 0;
                                    $product_option_value_id = 0;

                                    if ($product_id != 0) {
                                        $property_name = $variation['property_name'];

                                        /* Not added langauge_id condition because Etsy can return data in any langauge */
                                        $option_details = $this->db->query("SELECT pov.option_value_id, pov.option_id FROM " . DB_PREFIX . "etsy_attribute_mapping eam "
                                                . "INNER JOIN " . DB_PREFIX . "product_option_value pov "
                                                . "on pov.option_id = eam.option_id "
                                                . "INNER JOIN " . DB_PREFIX . "option_value_description ovd "
                                                . "on pov.option_value_id = ovd.option_value_id "
                                                . "WHERE property_title = '" . $property_name . "' "
                                                . "AND pov.product_id = '" . $product_id . "' "
                                                . "AND ovd.name = '" . $variation['values'][0] . "'");

                                        if ($option_details->num_rows > 0) {
                                            $product_option_id = $option_details->row['option_id'];
                                            $product_option_value_id = $option_details->row['option_value_id'];
                                        }
                                    }
                                    $counter++;
                                    $optionArray[] = array(
                                        'product_option_id' => $product_option_id,
                                        'product_option_value_id' => $product_option_value_id,
                                        'name' => $property_name,
                                        'value' => $variation['values'][0],
                                        'type' => ''
                                    );
                                }
                            }

                            $productsArray[] = array(
                                'product_id' => $product_id,
                                'name' => $receiptTransactionList['title'],
                                'quantity' => $receiptTransactionList['quantity'],
                                'model' => $product_model,
                                'price' => $receiptTransactionList['price'],
                                'total' => $receiptTransactionList['price'] * $receiptTransactionList['quantity'],
                                'tax' => 0.00,
                                'reward' => 0.00,
                                'option' => $optionArray
                            );
                        }

                        $customer = $this->model_kbetsy_cron->getCustomerByEmail($receiptDetail['buyer_email']);
                        if ($customer) {
                            $customer_id = $customer['customer_id'];
                            $customer_group_id = $customer['customer_group_id'];
                        } else {
                            $customer_id = 0;
                            $customer_group_id = $this->config->get('config_customer_group_id');
                        }

                        $etsy_settings = $this->config->get('etsy_order_settings');
                        $order_default_status = $etsy_settings['default_status'];
                        $order_paid_status = $etsy_settings['default_paid_status'];

                        if (!empty($receiptDetail['was_paid'])) {
                            $order_status = $order_paid_status;
                        } else {
                            $order_status = $order_default_status;
                        }

                        $currency = $this->model_kbetsy_cron->getCurrencyByCode($receiptDetail['currency_code']);

                        //Set Firstname and Lastname parameters
                        if (!empty($receiptDetail['name'])) {
                            $customerName = explode(' ', $receiptDetail['name'], 2);
                        }
                       
                        $orderDetails[] = array(
                            'store_id' => $this->config->get('store_id'),
                            'store_name' => "Etsy",
                            'store_url' => $this->config->get('config_url'),
                            'id_etsy_order' => $receiptDetail['receipt_id'],
                            'customer_id' => $customer_id,
                            'customer_group_id' => $customer_group_id,
                            'firstname' => !empty($customerName[0]) ? $customerName[0] : $receiptDetail['name'],
                            'lastname' => !empty($customerName[1]) ? $customerName[1] : '',
                            'email' => $receiptDetail['buyer_email'],
                            'telephone' => '', //Etsy does not provide phone/mobile number
                            'fax' => '', //Etsy does not provide phone/mobile number
                            'payment_firstname' => !empty($customerName[0]) ? $customerName[0] : $receiptDetail['name'],
                            'payment_lastname' => !empty($customerName[1]) ? $customerName[1] : '',
                            'payment_company' => '',
                            'payment_company_id' => '',
                            'payment_tax_id' => '',
                            'payment_address_1' => $receiptDetail['first_line'],
                            'payment_address_2' => $receiptDetail['second_line'],
                            'payment_postcode' => $receiptDetail['zip'],
                            'payment_city' => $receiptDetail['city'],
                            'payment_zone_id' => $orderState['zone_id'],
                            'payment_zone' => $orderState['name'],
                            'payment_country_id' => $orderCountry['country_id'],
                            'payment_country' => $orderCountry['name'],
                            'payment_address_format' => '',
                            'payment_method' => $receiptDetail['payment_method'],
                            'payment_code' => 'cod', //Hardcoded values to avoid error as payment_code is mandatory otherwise admin order details page will throw error
                            'shipping_firstname' => !empty($customerName[0]) ? $customerName[0] : $receiptDetail['name'],
                            'shipping_lastname' => !empty($customerName[1]) ? $customerName[1] : '',
                            'shipping_company' => '',
                            'shipping_address_1' => $receiptDetail['first_line'],
                            'shipping_address_2' => $receiptDetail['second_line'],
                            'shipping_postcode' => $receiptDetail['zip'],
                            'shipping_city' => $receiptDetail['city'],
                            'shipping_zone_id' => $orderState['zone_id'],
                            'shipping_zone' => $orderState['name'],
                            'shipping_country_id' => $orderCountry['country_id'],
                            'shipping_country' => $orderCountry['name'],
                            'shipping_address_format' => '',
                            'shipping_method' => $receiptDetail['shipping_details']['shipping_method'],
                            'shipping_code' => '',
                            'comment' => $receiptDetail['message_from_buyer'],
                            'total' => $receiptDetail['grandtotal'],
                            'reward' => '',
                            'order_status_id' => $order_status,
                            'affiliate_id' => 0,
                            'commission' => 0,
                            'language_id' => $this->config->get('config_language_id'),
                            'marketing_id' => '0',
                            'currency_id' => $currency['currency_id'],
                            'currency_code' => $currency['code'],
                            'currency_value' => 1, /* Currency value will alwasy be one as we are adding the actual currency data in the DB instead in default currency */
                            'ip' => '',
                            'forwarded_ip' => '',
                            'user_agent' => '',
                            'accept_language' => '',
                            'date_added' => date("Y-m-d H:i:s", $receiptDetail['creation_tsz']), //Commented and changed to store current date in date_added of order table
                            'date_modified' => date("Y-m-d H:i:s", $receiptDetail['creation_tsz']),
                            'total_shipping_cost' => $receiptDetail['total_shipping_cost'],
                            'total_tax' => $receiptDetail['total_tax_cost'] + $receiptDetail['total_vat_cost'],
                            'subtotal' => $receiptDetail['subtotal'],
                            'discount' => $receiptDetail['discount_amt'],
                            'products' => $productsArray
                        );
                    }
                }
            }
        }
        echo "orderDetails";
        //print("<pre>".print_r ($orderDetails,true )."</pre>");
        return $orderDetails;
    }

    private function getStoreCountryID($etsyCountryID = '') {
        $this->load->model('kbetsy/cron');
        $storeCountryID = 0;
        if (!empty($etsyCountryID)) {
            $countryDetail = $this->db->query("SELECT country_name, iso_code FROM " . DB_PREFIX . "etsy_countries WHERE country_id = '" . $etsyCountryID . "'");
            if ($countryDetail->num_rows > 0) {
                if (!empty($countryDetail->row['iso_code'])) {
                    $storeCountryID = $this->model_kbetsy_cron->getCountryByISOCode($countryDetail->row['iso_code']);
                } else {
                    if (!empty($countryDetail->row['country_name'])) {
                        $storeCountryID = $this->model_kbetsy_cron->getCountryByName($countryDetail->row['country_name']);
                    }
                }
            }
        }

        if (empty($storeCountryID)) {
            //Create New Country
        }
        return $storeCountryID;
    }

    //Function definition to get Store State ID
    private function getStoreStateID($etsyState = '') {
        $storeStateID = 0;
        if (!empty($etsyState)) {
            $getStateByName = $this->model_kbetsy_cron->getStateByName($etsyState);
            $storeStateID = $getStateByName;
        }
        return $storeStateID;
    }

    private function writeOrderIntoDb($order) {
        $order_id = $this->model_kbetsy_order->createOrder($order);
        if ($order_id) {
            foreach ($order['products'] as $product) {
                $product_id = $this->model_kbetsy_order->createOrderProducts($product, $order_id);
                if ($product_id) {
                    foreach ($product['option'] as $option) {
                        $this->model_kbetsy_order->createOrderOptions($option, $order_id, $product_id);
                    }
                }
            }
            $this->model_kbetsy_order->addHistory($order['order_status_id'], $order_id);
            $this->model_kbetsy_order->insertOrderTotal($order, $order_id);

            // Stock subtraction
            $order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int) $order_id . "'");
            foreach ($order_product_query->rows as $order_product) {
                $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int) $order_product['quantity'] . ") WHERE product_id = '" . (int) $order_product['product_id'] . "' AND subtract = '1'");
              
              
                $product_inventaire_query = $this->db->query("SELECT P.unallocated_quantity,P.quantity FROM `oc_product` AS P WHERE P.product_id = '" . (int)$order_product['product_id'] . "'");
                foreach ($product_inventaire_query->rows as $product_inventaire) {
                    $unallocated_quantity=$product_inventaire['unallocated_quantity'];
                    $quantity=$product_inventaire['quantity'];
                }
                $quantite_change_entrepot=$order_product['quantity'];
                $quantite_change_magasin=0;
                if($quantity-$order_product['quantity']<0){
                    $quantite_change_magasin=$order_product['quantity']-$quantity;
                    $quantite_change_entrepot=$quantity;
                    $this->db->query("UPDATE `" . DB_PREFIX . "product` SET unallocated_quantity = (unallocated_quantity - " . (int)$quantite_change_magasin . ") WHERE product_id = '" . (int)$order_product['product_id'] . "'");
                }	
                
             //   $this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity - " . (int)$quantite_change_entrepot . ") WHERE product_id = '" . (int)$order_product['product_id'] . "'");
                //insert quantite enlever de l inventaire des 2 emplacements dans order
              
                $this->updatequantitytoebay((int)$order_product['product_id']);

                $order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int) $order_id . "' AND order_product_id = '" . (int) $order_product['order_product_id'] . "'");

                foreach ($order_option_query->rows as $option) {
                    $this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int) $order_product['quantity'] . ") WHERE product_option_value_id = '" . (int) $option['product_option_value_id'] . "' AND subtract = '1'");
                }
            }
        }
        return $order_id;
    }

    public function syncOrderStatus() {
        @ini_set('memory_limit', -1);
        @ini_set('max_execution_time', -1);
        @set_time_limit(0);

        if ($this->config->get('kbetsy_demo_flag') == 0) {
            $secure_key = $this->config->get('kbetsy_secure_key');
            if (!empty($this->request->get['secure_key']) && $secure_key == $this->request->get['secure_key']) {
                $this->syncMethod = 'syncOrderStatus';

                $this->load->model('kbetsy/cron');
                $this->load->model('kbetsy/order');
                $this->model_kbetsy_cron->auditLogEntry("Order Status Sync Started", $this->syncMethod);

                $settings = $this->config->get('etsy_general_settings');
                if (isset($settings['enable']) && $settings['enable'] == 1) {
                    $etsy_access_token = $this->config->get('etsy_access_token');
                    $etsy_access_token_secret = $this->config->get('etsy_access_token_secret');
                    if ($etsy_access_token && $etsy_access_token_secret) {
                        $this->updateOrdersStatus();
                    } else {
                        $logEntry = "Please connect you store to etsy from general settings page!";
                        $this->model_kbetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
                        echo $logEntry;
                    }
                } else {
                    $logEntry = "Module is not enabled. Kindly go to general settings page to enable the module.";
                    $this->model_kbetsy_cron->auditLogEntry($logEntry, $this->syncMethod);
                    echo $logEntry;
                }
                $this->model_kbetsy_cron->auditLogEntry("Order Status Sync Completed", $this->syncMethod);
                if ($this->syncError == true) {
                    echo "Success with some error(s). Refer to audit log for the details of the error.";
                } else {
                    echo "Success";
                }
            } else {
                echo "Secure key not matched.";
            }
        } else {
            echo "Sorry!!! You are not allowed to perform this action the demo version.";
        }
        die();
    }

    //Function definition to update orders status on Etsy Marketplace
    private function updateOrdersStatus() {
        $this->syncMethod = 'syncOrderStatus';

        $reciptsUpdated = 0;

        //Get last status update date to send update orders status request on Etsy Marketplace
        $lastDate = $this->config->get('sync_orders_status_date');
        if (empty($lastDate)) {
            $lastDate = date("Y-m-d H:i:s", strtotime(' -2 days'));
        }

        $etsy_settings = $this->config->get('etsy_order_settings');
        $shippedStatus = $etsy_settings['shipped_status'];
        $paidStatus = $etsy_settings['paid_status'];

        if (!empty($lastDate) && !empty($paidStatus) && !empty($shippedStatus)) {
            //Get orders to update status on Etsy Marketplace
            $selectSQL = "SELECT eol.id_order, eol.id_etsy_order, o.order_status_id FROM " . DB_PREFIX . "etsy_orders_list eol, " . DB_PREFIX . "order o WHERE o.order_id = eol.id_order AND eol.is_status_updated = '1' AND (o.order_status_id IN (" . implode(",", $paidStatus) . ") OR o.order_status_id IN (" . implode(",", $shippedStatus) . ")) AND date_updated >= '" . $this->db->escape($lastDate) . "'";
            $receipts = $this->db->query($selectSQL);
            if ($receipts->num_rows) {

                foreach ($receipts->rows as $receipt) {
                    //Prepare parameters to send request
                    $etsyQueryString = array(
                        'receipt_id' => $receipt['id_etsy_order']
                    );

                    if (in_array($receipt['order_status_id'], $shippedStatus)) {
                        $etsyQueryString['was_shipped'] = '1';
                    }

                    if (in_array($receipt['order_status_id'], $paidStatus)) {
                        $etsyQueryString['was_paid'] = '1';
                    }

                    $etsyMain = $this->model_kbetsy_cron->createEtsyObject();
                    $result = $etsyMain->sendRequest("updateReceipt", array('params' => $etsyQueryString, 'data' => $etsyQueryString));
                    if (isset($result['error'])) {
                        $this->syncError = true;
                      //  $this->auditLogEntry($result['error'], $this->syncMethod);
                    } elseif (isset($result['results'])) {
                        $updateSQL = "UPDATE " . DB_PREFIX . "etsy_orders_list SET is_status_updated = '0' WHERE id_etsy_order = '" . $receipt['id_etsy_order'] . "'";
                        $this->db->query($updateSQL);
                    }
                    sleep(1); //Sleep job to avoid exceed limit rate
                    $reciptsUpdated++;
                    $this->model_kbetsy_cron->auditLogEntry("Order Status Update for Order ID:", $receipt['id_etsy_order']);
                }
                //Update date when orders status updated on Etsy Marketplaces
                $this->model_kbetsy_order->updateSyncStatusDate(date("Y-m-d H:i:s"));
            }
        }
        $this->model_kbetsy_cron->auditLogEntry("Total Order Status Sync:", $reciptsUpdated);
    }

}
