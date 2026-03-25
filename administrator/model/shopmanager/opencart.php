<?php
namespace Opencart\Admin\Model\Shopmanager;

/**
 * @version [3.0.0.0] [Supported opencart version 3.x.x.x]
 * @category Webkul
 * @package Marketplace Opencart Connector
 * @author [Webkul] <[<http://webkul.com/>]>
 * @copyright Copyright (c) 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html 
 */

class Opencart extends \Opencart\System\Engine\Model {


	/**
	 * [getOpencartAccount to get Opencart Account list or particular account details]
	 * @param  array  $data [filter data array]
	 * @return [type]       [list of Opencart accounts]
	 */
	public function getAccount($data = array(), $type = false) { 
		$sql = "SELECT * FROM `" . DB_PREFIX . "seller_connect_accounts` WHERE marketplace_id=8 AND `seller_id` = " . (int)$this->customer->getId() . "";

		if (!empty($data['filter_id'])) {
			$sql .= " AND `id` = " . (int)$data['filter_id'] . "";
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND `connector_store_name` LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}else if (!empty($data['filter_store_name']) && $type) {
			$sql .= " AND `connector_store_name` = '" . $this->db->escape($data['filter_store_name']) . "'";
		}else if (empty($data['filter_store_name'])) {
			$sql .= " AND `connector_store_name` = ''";
		}

		if (!empty($data['filter_user_id'])) {
			$sql .= " AND connector_user_id LIKE '%" . $this->db->escape($data['filter_user_id']) . "%'";
		}

		$sort_data = array(
			'id',
			'connector_store_name',
			'connector_user_id',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
//echo $sql;
		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * [getTotalAccount to get the total number of  account]
	 * @param  array  $data [filter data array]
	 * @return [type]       [total number of  account records]
	 */
	public function getTotalAccount($data = array()) {  
		$sql = "SELECT COUNT(DISTINCT id) AS total FROM " . DB_PREFIX . "seller_connect_accounts WHERE marketplace_id=8 AND `seller_id` = " . (int)$this->customer->getId() . "";

		if (!empty($data['filter_id'])) {
			$sql .= " AND `id` = " . (int)$data['filter_id'] . ""; 
		}

		if (!empty($data['filter_store_name'])) {
			$sql .= " AND `connector_store_name` LIKE '%" . $this->db->escape($data['filter_store_name']) . "%'";
		}

		if (!empty($data['filter_user_id'])) {
			$sql .= " AND `connector_user_id` LIKE '%" . $this->db->escape($data['filter_user_id']) . "%'";
		}

		$query = $this->db->query($sql);

		return $query->row['total']; 
	}

	/**
	 * [addAccount to add/update the  account details]
	 * @param  array  $data [details of  account]
	 * @return [type]       [description]
	 */
	public function editAccount($data = array()) {
		$data['value']=json_encode($data['value']);
		$this->db->query("UPDATE `" . DB_PREFIX . "seller_connect_accounts` SET `marketplace_id` = 8,
		`connector_store_name` = '" . $this->db->escape($data['connector_store_name']) . "',
		`connector_user_id` = '" . $this->db->escape($data['connector_user_id']) . "', 
		`connector_auth_token` = '" . $this->db->escape($data['connector_auth_token']) . "', 
		`value` = '" . $this->db->escape($data['value']) . "', date_modified=now() 
		WHERE `id` = '" . (int)$data['id'] . "' AND `seller_id` = " . (int)$this->customer->getId() . "");
	}
	public function addAccount($data = array()) {
		$data['value']=json_encode($data['value']);

			$this->db->query("INSERT INTO `" . DB_PREFIX . "seller_connect_accounts` SET `marketplace_id` = 8,
			`seller_id` = " . (int)$this->customer->getId() . ",
			`connector_store_name` = '" . $this->db->escape($data['connector_store_name']) . "', 
			`connector_user_id` = '" . $this->db->escape($data['connector_user_id']) . "',
			`connector_auth_token` = '" . $this->db->escape($data['connector_auth_token']) . "', 
			`value` = '" . $this->db->escape($data['value']) . "', date_added=now() ");
			$id = $this->db->getLastId();
		
		return $id;
	}

	
	/**
	 * [deleteAccount to delete the  account]
	 * @param  boolean $id [ account id]
	 * @return [type]              [description]
	 */
	public function deleteAccount($id = false) {
		if ($id) {
			$this->db->query("DELETE FROM ".DB_PREFIX."seller_connect_accounts WHERE id = '".(int)$id."' "); 
		}
	}

	
	public function getNewOrders($filter_order_id = null,$date_transaction = null,$account= array(),$page= null,$data= array())
	{
		$today = getdate();
		$args=array();
		//echo $account['date_transaction'];
		//print("<pre>".print_r ($account,true )."</pre>");
		if(isset($date_transaction)){
			//echo "allo";
			date_default_timezone_set('America/New_York');
				//$dateheure=$dateformat[2]."-".$dateformat[1]."-".$dateformat[0]." ".$opencartinputnameline[1];
			$dateformat=explode (" ",$date_transaction);
			//echo strtotime (date('Y-m-d',$dateformat[0].' - 1 days'));
			$date_transaction=$dateformat[0]." 00:00:01";//.$dateformat[1].
			//$date_transaction='2021-12-18T17:44:45.510Z';
			//                2021-01-18T17:44:45.510Z
			//				  2021-1-18T9:47:00.000Z
			
			
			//echo gmdate('Y-m-d H:i:s',time()-15);
			$dateformat2=explode (" ",gmdate('Y-m-d H:i:s',time()-15));
			$date_today=$dateformat2[0]."T".$dateformat2[1].".000Z";
			//echo $date_today;
			$date1_ts = strtotime($dateformat[0]);
			$date2_ts = strtotime($dateformat2[0]);
			$diff = round(($date2_ts - $date1_ts) / 86400);
	
		$args['createTimeFrom']=$date_transaction;
		$args['createTimeTo']=gmdate('Y-m-d',time()-15);  

		
		/*$from='<OrderIDArray> 
		<OrderID>08-08517-21586</OrderID>
		</OrderIDArray>';*/
		//2022-03-16
		
		}else{
			//$from='<NumberOfDays>15</NumberOfDays>';
			$args['createTimeFrom']=date('Y-m-d', strtotime('-90 days'));
			//echo $args['createTimeFrom'];
			$args['createTimeTo']=gmdate('Y-m-d',time()-15); 
		}
		$value=json_decode($account['value']);
		//print("<pre>".print_r ($value,true )."</pre>");

		$url=$value->url.'/canuship/index.php';
		 
		$query_array = array (
			'action' => 		'export',
			'start_date' => $args['createTimeFrom'],
			'end_date' => $args['createTimeTo'],
			'SS-UserName' => 	$account['connector_user_id'],
			'SS-Password' =>	$account['connector_auth_token'],
			'format' 			=> 'json'
		);
		$query = http_build_query($query_array);
		$result = file_get_contents($url . '?' . $query);
	   // header('Content-Type: text/xml');
	   // echo $result;
		$xml = new SimpleXMLElement($result);
		$serializer= new SimpleXMLArraySerializer($xml);
	
				$data=$serializer->arraySerialize();
				return $data;


}
public function getExistingOrders($orders,$account,$page,$typerequest,$data = array())
	{
		//echo $orders_query['date_transaction'];
		//print("<pre>".print_r ($orders,true )."</pre>");
		$value=json_decode($account['value']);
		$url=$value->url.'/canuship/index.php';
		$order_query=array();
		 foreach($orders as $order){
			$order_query[]=$order['record_number'];
		 }
		$query_array = array (
			'action' => 		'export',
			'orders_id' => 		implode(":", $order_query),
			'SS-UserName' => 	$account['connector_user_id'],
			'SS-Password' =>	$account['connector_auth_token'],
			'format' 			=> 'json'
		);
		$query = http_build_query($query_array);
		$result = file_get_contents($url . '?' . $query);
	   // header('Content-Type: text/xml');
	   // echo $result;
		$xml = new SimpleXMLElement($result);
		$serializer= new SimpleXMLArraySerializer($xml);
		//print_r($serializer->arraySerialize());
		//$result= json_encode($xml);
		//$result= json_decode($result,true);
		//print("<pre>".print_r ($serializer->arraySerialize(),true )."</pre>");
			
			/*	foreach($response['getOrdersResponse']['orderArray'] as $order){
//print("<pre>".print_r ($order,true )."</pre>");
					
					$data[$idarray]=$this->formatDataforDB($order['order'],$account,'new');
					$idarray++;
				
				}	*/

				//print("<pre>".print_r ($data,true )."</pre>");
				/*$TotalNumberOfEntries=$response['getOrdersResponse']['paginationResult']['totalNumberOfEntries'];

				$TotalNumberOfPages=$response['getOrdersResponse']['paginationResult']['totalNumberOfPages'];
				//print("<pre>".print_r ($response['getOrdersResponse']['paginationResult'],true )."</pre>");
				
				$page++;
				if($page<=$TotalNumberOfPages){
					$this->getExistingOrders($orders,$account,$page,$typerequest,$data);
				}else{
					return $data;	
				}*/
				$data=$serializer->arraySerialize();
				return $data;
}
public function formatDataforDB($order,$account,$typerequest){
	
	
		//	echo "oui<br>";
			$marketplace_order_status=0;
			//verifier si order existante
			$data = array();
			$countries = $this->model_localisation_country->getCountries();
			
			
					if($order['orderStatus']=="Completed"){
						$marketplace_order_status=1;
					}elseif($order['orderStatus']=="Shipped"){
						$marketplace_order_status=1;
					}elseif($order['orderStatus']=="InProcess"){
						$marketplace_order_status=3;
					}elseif($order['orderStatus']=="Inactive"){
						$marketplace_order_status=5;
					}elseif($order['orderStatus']=="CancelPending"){
						$marketplace_order_status=6;
					}elseif($order['orderStatus']=="Cancelled"){
						$marketplace_order_status=7;
					/*}elseif(isset($order['MonetaryDetails']['Refunds'])){
						$marketplace_order_status=4;*/
					}else{
						$marketplace_order_status=0;
					} 

			
				//print("<pre>".print_r ($order,true )."</pre>");
					$splitordernum=1;
					$createdTime = $order['createdTime'];
					$dateformat=explode ("T",$createdTime);
					//2001-01-01 00:00:01
					//$dateheure=$dateformat[2]."-".$dateformat[1]."-".$dateformat[0]." ".$opencartinputnameline[1];
					$dateformat2=explode (".",$dateformat[1]);
					$datetransaction=$dateformat[0]." ".$dateformat2[0];
					$shipping_address = $order['shippingAddress'];
					
					if($marketplace_order_status==1){
						if($shipping_address['country']=="PR")$shipping_address['country']="US";
						
						$consignee_country_id=0;
						foreach($countries as $country){
							if(strtolower($shipping_address['country'])==strtolower($country['iso_code_2'])){
								$consignee_country_id=$country['country_id'];
							}
						}
						
						if(strtolower($shipping_address['stateOrProvince']=="Puerto Rico"))$shipping_address['stateOrProvince']="PR";
						$zones=$this->model_localisation_zone->getZonesByCountryId($consignee_country_id);
						$consignee_zone_id=0;
						foreach($zones as $zone){
							if(strtolower($shipping_address['stateOrProvince'])==strtolower($zone['code']) || strtolower($shipping_address['stateOrProvince'])==strtolower($zone['name'])){
								$consignee_zone_id=$zone['zone_id'];
								$consignee_zone=$zone['code'];
							}
						}
						$consignee_firstname=addslashes($shipping_address['name']);
						$consignee_address_1=addslashes($shipping_address['street1']);
						$consignee_address_2=addslashes($shipping_address['street2']);
						$consignee_city=addslashes($shipping_address['cityName']);
						$consignee_postcode=$shipping_address['postalCode'];
						$consignee_country=addslashes($shipping_address['country']);
						
					}else{
						$consignee_firstname="";
						$consignee_address_1="";
						$consignee_address_2="";
						$consignee_city="";
						$consignee_telephone="";
						$consignee_postcode="";
						$consignee_country="";
						$consignee_zone="";
						$consignee_zone_id=0;
						$order['ExtendedOrderID']="";
						$consignee_country_id=0;
					}
						//$consignee_firstname = addslashes($transaction->Buyer->UserFirstName);
						//$consignee_lastname = addslashes($transaction->Buyer->UserLastName);	
					$weight=0;
					//print("<pre>".print_r ($shipping_address,true )."</pre>");
					$data = array(
					'marketplace'    		=> "",
					'consignee_firstname'	=> $consignee_firstname, 
					'consignee_lastname'   	=> "",
					'consignee_company'   	=> "",
					'consignee_email' 		=> "",
					'consignee_telephone' 	=> "",
					'consignee_message' 	=> "",
					'consignee_address_1'	=> $consignee_address_1,
					'consignee_address_2'	=> $consignee_address_2,
					'consignee_city'		=> $consignee_city,
					'consignee_postcode'	=> $consignee_postcode,
					'consignee_country'		=> $consignee_country,
					'consignee_zone'		=> $consignee_zone,
					'consignee_zone_id'		=> $consignee_zone_id,
					'consignee_total'	  	=> $order['subtotal'],
					'consignee_quantity'	=> 0,
					'record_number'			=> $order['orderID'],
					'shipping_comment' 	 	=> "",
					'shipping_status' 		=> "",
					'shipping_carrier_id'	=> 0,
					'shipping_carrier_name'	=> "",
					'seller_order_id'		=> "",
					'order_id'				=> $order['orderID'],
					'extended_order_id'		=> $order['orderID'],
					'order_status_id'		=> 1,
					'currency_id'			=> 5,
					'currency_code'			=> $order['currencyCode'],
					'consignee_country_id' 	=> $consignee_country_id,
					'date_transaction'		=> $datetransaction,
					'marketplace_account_id'=>$account['id'],
					'marketplace_id' 		=> $account['marketplace_id'] ,
					'marketplace_order_status'				=>$marketplace_order_status 
						);
					//	//print("<pre>".print_r ($data,true )."</pre>");
//update 
					//$xml2=$this->model_shopmanager_opencart->getExistingOrderDetail($account,$order['OrderID'],1);
				//	//print("<pre>".print_r ($xml2,true )."</pre>");
					//$shipments_order_opencart= json_encode($xml2);
				//	$shipments_order_opencart= json_decode($shipments_order_opencart,true);
					$shipments[0]=array(
						'seller_shipment_id' => '',
						'seller_manifest_id' => '',
						'shipment_status_id' => 1,
						'shipping_tracking' => '',
						'shipping_carrier_id' => 0,
						'shipping_status_id' => 0,
						'shipping_status' => '',
						'shipping_comment' => '',
						'shipping_depth' => 0,
						'shipping_length' => 0,
						'shipping_width' => 0,
						'shipping_weight' => 0,
						'shipping_weight_confirmed' => 0,
						'shipping_fee' => 0,
						'shipping_fee_confirmed' => 0,
						'date_added' => '',
						'date_transaction' => '',
						'shipping_carrier_name' => '',
						'shipping_url' =>'',
						'shipping_logo' =>'',
						'consignee_total' => 0,
						'consignee_quantity' =>0,
						'consignee_country_id' 	=> $consignee_country_id,
						'currency_code' =>'',
						'currency_id'	=>0
					);
					//print("<pre>".print_r ($xml2,true )."</pre>");
					
					//print("<pre>".print_r ($transactionsArr,true )."</pre>");
					$i=0;	
					$j=0;
					$row=0;
					$notracking='non';
					
						foreach($order['shippingDetails']['shipmentTrackingNumber'] as $shipmentTrackingNumber){
						//	//print("<pre>".print_r ($shipmentTrackingNumber,true )."</pre>");
							if(isset($shipmentTrackingNumber[0]))	{	
								$shipmentTrackingNumbertmp=$shipmentTrackingNumber[0];
								unset($order['shippingDetails']['shipmentTrackingNumber']);
								$order['shippingDetails']['shipmentTrackingNumber']=$shipmentTrackingNumbertmp;
							}else{
								unset($order['shippingDetails']['shipmentTrackingNumber']);
							}
						}
						
					
						if(isset($order['shippingDetails']['shipmentTrackingNumber']) && $notracking=='non'){
							$idarray=$this->searchForIdInArray($order['shippingDetails']['shipmentTrackingNumber'],$shipments,'shipping_tracking');
							if(!is_null($idarray)){
								//	echo "ouinull";
									$row=$idarray;
									$j++;
							}else{
									if(is_numeric($order['shippingDetails']['shipmentTrackingNumber'])){
										if(strlen($order['shippingDetails']['shipmentTrackingNumber'])==22){
											$order['shippingDetails']['ShippingCarrierUsed']="USPS";
										}else{
											$order['shippingDetails']['ShippingCarrierUsed']="FEDEX";
										}

									}else{
										$order['shippingDetails']['ShippingCarrierUsed']="UPS";
									}
									$row=$i;
									$carrierinfoquery=$this->db->query("SELECT * FROM `".DB_PREFIX ."shipping_company`
										   WHERE name='".$order['shippingDetails']['ShippingCarrierUsed']."'");
									$carrier=$carrierinfoquery->row;
									if(!isset($carrier['id']))
										$carrier['id']=0;

									$shipments[$row]=array(
										'seller_shipment_id' => '',
										'seller_manifest_id' => '',
										'shipment_status_id' => 1,
										'shipping_tracking' => $order['shippingDetails']['shipmentTrackingNumber'],
										'shipping_carrier_id' => $carrier['id'],
										'shipping_status_id' => 0,
										'shipping_status' => '',
										'shipping_comment' => '',
										'shipping_depth' => 0,
										'shipping_length' => 0,
										'shipping_width' => 0,
										'shipping_weight' => 0,
										'shipping_weight_confirmed' => 0,
										'shipping_fee' => 0,
										'shipping_fee_confirmed' => 0, 
										'date_added' => '',
										'date_transaction' => $datetransaction,
										'shipping_carrier_name' => $order['shippingDetails']['ShippingCarrierUsed'],
										'shipping_url' =>$carrier['link'],
										'shipping_logo' =>$carrier['logo'],
										'consignee_total' => 0,  
										'consignee_quantity' =>0,
										'consignee_country_id' 	=> $consignee_country_id,
										'currency_code' =>'',
										'currency_id'	=>0
									);
									$i++;
									$j=0;
							}
						}else{
							$notracking='oui';
							$row=0;
							$shipments[0]=array(
								'seller_shipment_id' => '',
								'seller_manifest_id' => '',
								'shipment_status_id' => 1,
								'shipping_tracking' => '', 
								'shipping_carrier_id' => 0,
								'shipping_status_id' => 0,
								'shipping_status' => '',
								'shipping_comment' => '',
								'shipping_depth' => 0,
								'shipping_length' => 0,
								'shipping_width' => 0,
								'shipping_weight' => 0,
								'shipping_weight_confirmed' => 0,
								'shipping_fee' => 0,
								'shipping_fee_confirmed' => 0,
								'date_added' => '',
								'date_transaction' => $datetransaction,
								'shipping_carrier_name' => '',
								'shipping_url' =>'',
								'shipping_logo' =>'',
								'consignee_total' => 0,
								'consignee_quantity' =>0,
								'consignee_country_id' 	=> 0,
								'currency_code' =>'',
								'currency_id'	=>0
							);
						}
						
						//$marketplace = $order['Platform;
						//$consignee_email = "";
						//print("<pre>".print_r ($shipments,true )."</pre>");
					if($typerequest=="new"){
						foreach($order['itemArray'] as $itemdetail)				
					
						
							
//print("<pre>".print_r ($itemdetail,true )."</pre>");
							
								$shipments[$row]['items'][$j]['weight'] =0;
								$shipments[$row]['items'][$j]['depth'] =0;
								$shipments[$row]['items'][$j]['length'] =0;
								$shipments[$row]['items'][$j]['width'] =0;
							

								$image=$itemdetail['item']['thumbnailURL'];

						//		
						//	}
							$shipments[$row]['items'][$j]['image']=$image;
							$shipments[$row]['items'][$j]['item_id']=$itemdetail['item']['itemID'];
							$shipments[$row]['items'][$j]['quantity']=(int)$itemdetail['item']['quantity'];
							$shipments[$row]['consignee_quantity']+=(int)$itemdetail['item']['quantity'];
							$shipments[$row]['items'][$j]['price']=$itemdetail['item']['price'];
							$shipments[$row]['consignee_total']+=((int)$itemdetail['item']['quantity']*$itemdetail['item']['price']);

							$shipments[$row]['items'][$j]['currency_code']=$order['currencyCode'];
							$shipments[$row]['currency_code']=$order['currencyCode'];
							$shipments[$row]['items'][$j]['item_name'] = ($itemdetail['item']['title']);
							$shipments[$row]['items'][$j]['made_in_country_id'] = 0;
						//	echo "allo";
									
						
						$currency_id= array();

						$currency_id=$this->model_localisation_currency->getCurrencyByCode($order['currencyCode']);
	
						if(isset($currency_id['currency_id'])){
							$shipments[$row]['items'][$j]['currency_id']=$currency_id['currency_id'];
							$shipments[$row]['currency_id']=$currency_id['currency_id'];
						}else{
							$shipments[$row]['items'][$j]['currency_id']=0;
							$shipments[$row]['currency_id']=0;
						}
					

					
				//	$data['weight']=ceil($weight);
				}
				$data['shipments']=$shipments;
				

					

	//echo "data"	;		
//print("<pre>".print_r ($data,true )."</pre>");


	return $data;

}

public function getOrder ($customer,$seller_order_id)
	{
		 $order_query = $this->db->query("SELECT connector_auth_token FROM `" . DB_PREFIX . "seller_connect_accounts` ea  WHERE marketplace_id=4 AND ea.seller_id = '".(int)$customer."'");
			//LEFT JOIN " . DB_PREFIX . "customerpartner_to_syncorder so ON (ea.seller_id = so.seller_id)
//jo
//echo "allo";
//echo $order_query->row['connector_auth_token'];
			$post = '<?xml version="1.0" encoding="utf-8"?>
			<GetOrdersRequest   xmlns="urn:Opencart:apis:eBLBaseComponents">
				<RequesterCredentials>
					<OpencartAuthToken>'.$order_query->row['connector_auth_token'].'</OpencartAuthToken>
				</RequesterCredentials>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				<OrderIDArray>
					<OrderID>'.$seller_order_id.'</OrderID>
				</OrderIDArray>
				
			</GetOrdersRequest  >';//<DetailLevel>ReturnAll</DetailLevel>
			$headers = array(
						"X-Opencart-API-COMPATIBILITY-LEVEL: 899",
						"X-BONANZLE-API-DEV-NAME: 52m6yrvxldCfJ1H",
						"X-Opencart-API-APP-NAME: 52m6yrvxldCfJ1H",
						"X-BONANZLE-API-CERT-NAME: bN4OtdU0BRCDG6L",
						"X-Opencart-API-CALL-NAME: GetOrders",
						"X-Opencart-API-SITEID: 0" // 3 for UK
			);
			 
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_URL, "https://api.Opencart.com/api_requests/secure_request");
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($connection);
			 
			 //echo "allo";
			$xml = new SimpleXMLElement($response);
			$new = simplexml_load_string($response); 
			//echo json_encode($new);
			return $xml;	
	}
	public function getStatus($data) {
		$url=$data['url']."/canuship";
        //print("<pre>".print_r ($data,true )."</pre>");
        $query_array = array (
            'action' => 'connection',
            'SS-UserName' => $data['SS-UserName'],
			'version'	=> $data['version'],
            'SS-Password' =>$data['SS-Password'],
            'format' => 'json' 
        );
        $query = http_build_query($query_array);
	//	>$account_info_db['config_name']? $account_info_db['config_name']:$account_info_db['config_owner']
        //$result = file_get_contents($url . '?' . $query);
       // header('Content-Type: text/xml');
       // echo $result;
	   $file_headers = @get_headers($url);
		if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') {
			$exists = false;
		}
		else {
			$exists = true;
		}
	   if($exists ===true){
			$result = file_get_contents($url . '?' . $query);
			$xml = new SimpleXMLElement($result);
			$serializer= new SimpleXMLArraySerializer($xml);
			//print_r($serializer->arraySerialize());
			//$result= json_encode($xml);
			//$result= json_decode($result,true);
			
			
			$response=$serializer->arraySerialize();

				/*if ($err) {
					echo "cURL Error #:" . $err;
					
				} else {*/

					// Convert xml string into an object 
					//
					//print("<pre>".print_r ($json,true )."</pre>");
					if(isset($response['success'])){
						$response['status_image']='<i class="fas fa-check-circle fa-2x" style="color:green"></i>';
						//	//print("<pre>".print_r ($response,true )."</pre>");
					}else{
						$response['status_image']='<i class="fas fa-times-circle fa-2x" style="color:red"></i>';
					}
					//echo "allo";	
				//}
	   }else{
			$response['error']="Website";
			$response['status_image']='<i class="fas fa-times-circle fa-2x" style="color:red"></i>';
	   }
	return $response;
}

    public function searchForIdInArray($id, $array,$valname) {
	 
			foreach ($array as $key => $val) {
				 //echo $val[$valname];
				if ($val[$valname] == $id) {
					return $key;
				}
			}
			return null;
    }
	public function get_url_contents(){
		// echo "allo";
		 $url='https://phoenixliquidation.ca/canuship/index.php';
		 
		 $query_array = array (
			 'action' => 'export',
			 'start_date' => '2022-01-04 00:02:03',
			 'end_date' => '2022-04-22 23:59:00',
			 'SS-UserName' => '2d0e49bdbcc70fe6f4d2838a9fa302176642d581',
			 'SS-Password' =>'08a195bbaec53dbc9c5a7509269d9d96',
			 'format' => 'json'
		 );
		 $query = http_build_query($query_array);
		 $result = file_get_contents($url . '?' . $query);
		// header('Content-Type: text/xml');
		// echo $result;
		 $xml = new SimpleXMLElement($result);
		 $serializer= new SimpleXMLArraySerializer($xml);
		 //print_r($serializer->arraySerialize());
		 //$result= json_encode($xml);
		 //$result= json_decode($result,true);
		 //print("<pre>".print_r ($serializer->arraySerialize(),true )."</pre>");
		 
	 }

	
}
interface ArraySerializer
{
    public function arraySerialize();
}

class SimpleXMLArraySerializer implements ArraySerializer
{
    /**
     * @var SimpleXMLElement
     */
    private $subject;

    /**
     * @var SimpleXMLArraySerializeStrategy
     */
    private $strategy;

    public function __construct(SimpleXMLElement $element, SimpleXMLArraySerializeStrategy $strategy = NULL) {
        $this->subject  = $element;
        $this->strategy = $strategy ?: new DefaultSimpleXMLArraySerializeStrategy();
    }

    public function arraySerialize() {
        $strategy = $this->getStrategy();
        return $strategy->serialize($this->subject);
    }

    /**
     * @return SimpleXMLArraySerializeStrategy
     */
    public function getStrategy() {
        return $this->strategy;
    }
}
abstract class SimpleXMLArraySerializeStrategy
{
    abstract public function serialize(SimpleXMLElement $element);
}

class DefaultSimpleXMLArraySerializeStrategy extends SimpleXMLArraySerializeStrategy
{
    public function serialize(SimpleXMLElement $element) {
        $array = array();

        // create array of child elements if any. group on duplicate names as an array.		
        foreach ($element as $name => $child) { 
           
            if (isset($array[$name])) {
                
                if (!is_array($array[$name])) {
                    $array[$name] = [$array[$name]];					
                  //  echo "ALLO";
                }	
                   $array[$name][]  = $this->serialize($child);				
            } else { 
                $array[$name]= $this->serialize($child);				
				//echo "3";
            }
        }

        // handle SimpleXMLElement text values.
        if (!$array) {
            $array = (string)$element;
        }

        // return empty elements as NULL (self-closing or empty tags)
        if (!$array) {
            $array = NULL;
        }

        return $array;
    }
    
}
