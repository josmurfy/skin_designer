<?php
/**
 * @version [2.0.0.0] [Supported opencart version 2.3.x.x]
 * @category Webkul
 * @package Opencart-ebay Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */
class ModelEbayMapEbayMapOrder extends Model {
	public function getEbayOrderMap($data = array()) {
		$sql = "SELECT * FROM ".DB_PREFIX."wk_ebay_order_map om LEFT JOIN ".DB_PREFIX."order o ON(om.oc_order_id = o.order_id) WHERE 1 ";

		if(!empty($data['filter_account_id'])){
			$sql .= " AND om.account_id = '".(int)$data['filter_account_id']."' ";
		}

		if(!empty($data['filter_oc_order_id'])){
			$sql .= " AND om.oc_order_id ='".(int)$data['filter_oc_order_id']."' ";
		}

		if(!empty($data['filter_ebay_order_id'])){
			$sql .= " AND om.ebay_order_id ='".$data['filter_ebay_order_id']."' ";
		}

		if (isset($data['filter_order_total']) && !is_null($data['filter_order_total'])) {
			$sql .= " AND o.total LIKE '" . $this->db->escape($data['filter_order_total']) . "%'";
		}

		if(!empty($data['filter_date_added'])){
			$sql .= " AND om.sync_date LIKE '".$this->db->escape($data['filter_date_added'])."%' ";
		}

		if(!empty($data['filter_order_status'])){
			$sql .= " AND LCASE(om.ebay_order_status) LIKE '".$this->db->escape(strtolower($data['filter_order_status']))."%' ";
		}

		$sql .= " ORDER BY om.oc_order_id DESC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'] ;
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalEbayOrderMap($data = array()) {
		$sql = "SELECT COUNT(DISTINCT om.id) as total FROM ".DB_PREFIX."wk_ebay_order_map om LEFT JOIN ".DB_PREFIX."order o ON(om.oc_order_id = o.order_id) WHERE 1 ";

		if(!empty($data['filter_account_id'])){
			$sql .= " AND om.account_id = '".(int)$data['filter_account_id']."' ";
		}

		if(!empty($data['filter_oc_order_id'])){
			$sql .= " AND om.oc_order_id ='".(int)$data['filter_oc_order_id']."' ";
		}

		if(!empty($data['filter_ebay_order_id'])){
			$sql .= " AND om.ebay_order_id ='".$data['filter_ebay_order_id']."' ";
		}

		if (isset($data['filter_order_total']) && !is_null($data['filter_order_total'])) {
			$sql .= " AND o.total LIKE '" . $this->db->escape($data['filter_order_total']) . "%'";
		}

		if(!empty($data['filter_date_added'])){
			$sql .= " AND om.sync_date LIKE '".$this->db->escape($data['filter_date_added'])."%' ";
		}

		if(!empty($data['filter_order_status'])){
			$sql .= " AND LCASE(om.ebay_order_status) LIKE '".$this->db->escape(strtolower($data['filter_order_status']))."%' ";
		}

		$result = $this->db->query($sql)->row;
		return $result['total'];
	}

	public function __mapEbayOrder($eBayOrder = array(), $account_id = false){
        $item_shipping 	= 0;
        $shipMethod = '';
        $sync_result = $order_data = array();

        if (isset($eBayOrder['ShippingServiceSelected']['ShippingService'])) {
            $shipMethod .= $eBayOrder['ShippingServiceSelected']['ShippingService'];
        }

        $transactionList = $eBayOrder['TransactionArray']['Transaction'];

        if (!isset($transactionList[0])) {
            $transactionList = [0 => $transactionList];
        }
        $this->load->model('ebay_map/ebay_map_product');
        foreach ($transactionList as $transaction) {
        	//get item shipping cost
            if (isset($transaction['ActualShippingCost']['_'])) {
                $item_shipping = $item_shipping + floatval($transaction['ActualShippingCost']['_']);
            }

            //get order buyer name
            if (isset($transaction['Buyer']['UserFirstName'])) {
                $firstname 	= $transaction['Buyer']['UserFirstName'];
                $lastname 	= $transaction['Buyer']['UserLastName'];
            }else{
            		$firstname 	= 'Guest';
            		$lastname 	= 'User';
            }

            //check ordered item entry in opencart(item is synced or not)
            $getItemSyncEntry = $this->model_ebay_map_ebay_map_product->getProducts(array('filter_ebay_product_id' => $transaction['Item']['ItemID']));

	    			$product_data = array();
            if (isset($getItemSyncEntry[0]['oc_product_id']) && $getItemSyncEntry[0]['oc_product_id']) {
            	$product_data = $getItemSyncEntry[0];
                if(isset($transaction['Variation']['VariationSpecifics']['NameValueList'])){
                	$ebayItemvariations = $transaction['Variation']['VariationSpecifics']['NameValueList'];
                	if (!isset($ebayItemvariations[0])) {
	                    $VariationList = [0 => $ebayItemvariations];
	                }
                }
                $orderItems[] = [
						        			'product_id' => $product_data['oc_product_id'],
													'name'       => $product_data['product_name'],
													'model'      => $product_data['model'],
													'option'     => '',
													'quantity'   => $transaction['QuantityPurchased'],
													'price'      => $this->currency->convert($transaction['TransactionPrice']['_'], $transaction['TransactionPrice']['currencyID'], $this->config->get('config_currency')),
													'total'      => $this->currency->convert($transaction['TransactionPrice']['_'], $transaction['TransactionPrice']['currencyID'], $this->config->get('config_currency')) * $transaction['QuantityPurchased'],
													'tax'        => 0,
													'reward'     => 0
                	];
            } else {
            	$sync_result['error'] = array(
								'error_status'  => 1,
								'error_message' => 'eBay order id : <b> '.$eBayOrder['OrderID']." </b> not sync because Product <b> '" .$transaction['Item']['Title'].' '.$transaction['Item']['ItemID']."' </b> not Synced on Opencart. <br />",
								);
            	return $sync_result;
            }
	    }

		$getZoneId = array();
		$getCountryId['country_id'] = 0;
		if(isset($eBayOrder['ShippingAddress']['Country']) && $eBayOrder['ShippingAddress']['Country']){
			$getCountryId = $this->db->query("SELECT country_id,address_format FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $this->db->escape($eBayOrder['ShippingAddress']['Country']) . "' ")->row;
		}

		if(isset($eBayOrder['ShippingAddress']['StateOrProvince']) && $eBayOrder['ShippingAddress']['StateOrProvince']){
			$getZoneId = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE code = '" . $this->db->escape($eBayOrder['ShippingAddress']['StateOrProvince']) . "' ")->row;
			if(!isset($getZoneId['zone_id'])){
					$getZoneId['zone_id'] = 0;
			}
		}
		$getCurrencyId = array();
		if(isset($eBayOrder['AdjustmentAmount']['currencyID']) && $eBayOrder['AdjustmentAmount']['currencyID']){
			$getCurrencyId = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->db->escape($eBayOrder['AdjustmentAmount']['currencyID']) . "'")->row;
		}
		if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$forwarded_ip = $this->request->server['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$forwarded_ip = $this->request->server['HTTP_CLIENT_IP'];
		} else {
			$forwarded_ip = '';
		}

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$user_agent = $this->request->server['HTTP_USER_AGENT'];
		} else {
			$user_agent = '';
		}
		if($this->config->get('pp_standard_status') && isset($eBayOrder['CheckoutStatus']['PaymentMethod']) && $eBayOrder['CheckoutStatus']['PaymentMethod'] == 'PayPal'){
			$payment_method = $eBayOrder['CheckoutStatus']['PaymentMethod'];
            $payment_code   = 'pp_standard';
		}else{
			$payment_method = $eBayOrder['CheckoutStatus']['PaymentMethod'];
            $payment_code   = 'cod';
		}

		if( isset($eBayOrder['TransactionArray']['Transaction']['Buyer']['Email']) && trim($eBayOrder['TransactionArray']['Transaction']['Buyer']['Email']) == "Invalid Request") {
				$customer_email = $eBayOrder['BuyerUserID'];
		}else {
				$customer_email = $eBayOrder['TransactionArray']['Transaction']['Buyer']['Email'];
		}

    	$order_data = [
            'ebay_order_id' => $eBayOrder['OrderID'],
            'invoice_no'	=> 0,
            'invoice_prefix'=> $this->config->get('config_invoice_prefix'),
            'store_id'		=> $this->config->get('ebay_connector_ordersync_store'),
            'store_name'	=> $this->config->get('config_name'),
            'store_url'		=> $this->config->get('config_store_id') ? $this->config->get('config_url') : ($this->request->server['HTTPS'] ? HTTPS_SERVER : HTTP_SERVER),
            'order_status_id'=> $this->config->get('ebay_connector_order_status'),
            'currency_id' 	=> $getCurrencyId['currency_id'],
            'currency_code' => $getCurrencyId['code'],
            'currency_value'=> $getCurrencyId['value'],
            'firstname' 	=> $firstname,
            'lastname' 		=> $lastname,
            'email' 		=> $customer_email,
            'language_id' 	=> $this->config->get('config_language_id'),
            'shipping_address' => [
                    'shipping_firstname' 	=> $firstname,
                    'shipping_lastname' 	=> $lastname,
                    'shipping_address_1' 	=> $eBayOrder['ShippingAddress']['Street1'],
                    'shipping_address_2' 	=> $eBayOrder['ShippingAddress']['Street2'],
                    'shipping_city' 			=> $eBayOrder['ShippingAddress']['CityName'],
                    'shipping_postcode'		=> $eBayOrder['ShippingAddress']['PostalCode'],
                    'shipping_zone'				=> $eBayOrder['ShippingAddress']['StateOrProvince'],
                    'shipping_zone_id'		=> isset($getZoneId['zone_id']) ? $getZoneId['zone_id'] : 0,
                    'shipping_country'		=> $eBayOrder['ShippingAddress']['CountryName'],
                    'shipping_country_id'	=> $getCountryId['country_id'],
                    'shipping_address_format'=> $getCountryId['address_format'],
                    ],
            'payment_method' => $payment_method,
            'payment_code' 	 => $payment_code,
            'customer_group_id'	=> $this->config->get('config_customer_group_id'),
            'telephone' 	=> $eBayOrder['ShippingAddress']['Phone'],
            'fax' 			=> $eBayOrder['ShippingAddress']['Phone'],
            'items' 		=> $orderItems,
            'shipping_method'=> $shipMethod,
            'shipping_cost' => $item_shipping,
            'affiliate_id' 	=> 0,
						'commission' 	=> 0,
						'marketing_id' 	=> 0,
						'tracking' 		=> '',
            'user_agent'  	=> $user_agent,
            'forwarded_ip'  => $forwarded_ip,
            'ip' 			=> $this->request->server['REMOTE_ADDR'],
            'account_id' 	=> $account_id,
    	];

	    $getOrderId = $this->saveOrder($order_data);
	    $this->addOrderHistory($getOrderId, $this->config->get('ebay_connector_order_status'));

	    if($getOrderId){
	    	$this->db->query("INSERT INTO ".DB_PREFIX."wk_ebay_order_map SET `oc_order_id` = '".(int)$getOrderId."', `ebay_order_id` = '".$this->db->escape($order_data['ebay_order_id'])."', `ebay_order_status` = '".$this->db->escape($eBayOrder['OrderStatus'])."', `sync_date` = NOW(), `account_id` = '".(int)$account_id."' ");
	    	$map_id = $this->db->getLastId();

	    	if($map_id){
	    		$sync_result['success'] = array(
						'success_status'  => 1,
						'success_message' => 'eBay order id : <b> '.$eBayOrder['OrderID']." </b> has been synchronized with opencart's order id : <b> '" .$getOrderId. "' </b>. <br />",
								);
	    	}
	    }
	    return $sync_result;
	}

	public function saveOrder($data = array()){
		$this->load->model('customer/customer');
		if(isset($data['email'])){
			$checkCustomerEntry = $this->model_customer_customer->getCustomerByEmail($data['email']);
			if(!empty($checkCustomerEntry) && $checkCustomerEntry['customer_id']){
				$customer_id = $checkCustomerEntry['customer_id'];
			}else{
				$address 			= array();
				$address[0] 		= array(
										'firstname' => $data['firstname'],
										'lastname' 	=> $data['lastname'],
										'company' 	=> '',
										'address_1' => $data['shipping_address']['shipping_address_1'],
										'address_2' => $data['shipping_address']['shipping_address_2'],
										'city' 		=> $data['shipping_address']['shipping_city'],
										'postcode' 	=> $data['shipping_address']['shipping_postcode'],
										'country_id' => $data['shipping_address']['shipping_country_id'],
										'zone_id' 	=> $data['shipping_address']['shipping_zone_id'],
										'default'	=> true,
										);

				$data['password'] 	= $data['shipping_address']['shipping_lastname'].'_'.$data['shipping_address']['shipping_firstname'];
				$data['status'] 	= $data['approved'] = 1;
				$data['safe'] 		= $data['newsletter'] = 0;
				$data['custom_field'] = '';
				$data['address'] 	= $address;
				$customer_id 		= $this->model_customer_customer->addCustomer($data);
			}
		}else{
			$customer_id = 0;
		}
		$order_total = $order_sub_total = 0;
		foreach ($data['items'] as $key => $product) {
			$order_sub_total 	+= $product['total'];
			// $order_total 		+= $product['total'];
		}
		$order_total = $order_sub_total + $data['shipping_cost'];

		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$customer_id . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['firstname']) . "', payment_lastname = '" . $this->db->escape($data['lastname']) . "', payment_company = '', payment_address_1 = '" . $this->db->escape($data['shipping_address']['shipping_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['shipping_address']['shipping_address_2']) . "', payment_city = '" . $this->db->escape($data['shipping_address']['shipping_city']) . "', payment_postcode = '" . $this->db->escape($data['shipping_address']['shipping_postcode']) . "', payment_country = '" . $this->db->escape($data['shipping_address']['shipping_country']) . "', payment_country_id = '" . (int)$data['shipping_address']['shipping_country_id'] . "', payment_zone = '" . $this->db->escape($data['shipping_address']['shipping_zone']) . "', payment_zone_id = '" . (int)$data['shipping_address']['shipping_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['shipping_address']['shipping_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_address']['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_address']['shipping_lastname']) . "', shipping_company = '', shipping_address_1 = '" . $this->db->escape($data['shipping_address']['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address']['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_address']['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_address']['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_address']['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_address']['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_address']['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_address']['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address']['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_method']) . "', comment = '', total = '" . (float)$order_total . "', `order_status_id` = '".(int)$data['order_status_id']."', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', date_added = NOW(), date_modified = NOW()");

		$order_id = $this->db->getLastId();

		// Products
		if (isset($data['items'])) {
			foreach ($data['items'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

				$order_product_id = $this->db->getLastId();

				// foreach ($product['option'] as $option) {
				// 	$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				// }
			}
		}
		$totals 	=	[array(
							'code'       => 'sub_total',
							'title'      => 'Sub-Total',
							'value'      => (float)$order_sub_total,
							'sort_order' => $this->config->get('sub_total_sort_order')
						),
						array(
							'code'       => 'shipping',
							'title'      => $data['shipping_method'],
							'value'      => (float)$data['shipping_cost'],
							'sort_order' => $this->config->get('shipping_sort_order')
						),
						array(
							'code'       => 'total',
							'title'      => 'Total',
							'value'      => (float)$order_total,
							'sort_order' => $this->config->get('total_sort_order')
						)];
		// Totals
		if (isset($totals)) {
			foreach ($totals as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}
		return $order_id;
	}

	public function deleteMapOrder($oc_order_id = false){
		$result = false;
		if($oc_order_id){
			$this->db->query("DELETE FROM ".DB_PREFIX."order WHERE order_id = '".(int)$oc_order_id."' ");

			$this->db->query("DELETE FROM ".DB_PREFIX."order_product WHERE order_id = '".(int)$oc_order_id."' ");

			$this->db->query("DELETE FROM ".DB_PREFIX."order_total WHERE order_id = '".(int)$oc_order_id."' ");
			$this->db->query("DELETE FROM ".DB_PREFIX."order_history WHERE order_id = '".(int)$oc_order_id."' ");

			$this->db->query("DELETE FROM ".DB_PREFIX."wk_ebay_order_map WHERE oc_order_id = '".(int)$oc_order_id."' ");

			$result = true;
		}
		return $result;
	}

	public function addOrderHistory($order_id, $order_status_id, $comment = '', $notify = false){

		// Stock subtraction
		$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_product_query->rows as $order_product) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

			$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
			}
		}

		// Update the DB with the new statuses
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}

}
