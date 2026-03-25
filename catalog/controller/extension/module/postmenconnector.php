<?php
class ControllerExtensionModulePostmenconnector extends Controller {
	private $error = array();
	
	
	public function on_order_created($route, $output, $comment = '', $notify = false, $override = false){
		$this->load->model('checkout/order');
		$this->load->model('account/order');
		$this->load->model('catalog/product');		 
		
		
		$postmenconnector_status = $this->config->get('postmenconnector_status');
	    $postmenconnector_apikey = $this->config->get('postmenconnector_apikey');
		$url = $this->config->get('postmenconnector_endurl');
		if($postmenconnector_status==0 or $postmenconnector_apikey=='')
		return false;
		$order_info = $this->model_checkout_order->getOrder($output[0]);
		$var=array($route,$output,$comment,$notify,$override);
		
		if(empty($order_info))
		return false;
		//get product details by order id
		$products = $this->model_account_order->getOrderProducts($output[0]);			
			
		$order_item=array();
		$order_item_temp=array();
				$order_item_temp['box_type']="custom";
				$order_item_temp['dimension']['width']=1;
				$order_item_temp['dimension']['height']=1;
				$order_item_temp['dimension']['depth']=1;
				$order_item_temp['dimension']['unit']='in';
				$parsel_weight=0;
				
			   foreach($products as $product2){				
				$product_details = $this->model_catalog_product->getProduct($product2['product_id']);
				$wegith_unit=$this->getWeightClassDescriptionsPostment($product_details['weight_class_id']);
				
				$wegith_unit=reset($wegith_unit);
				$wegith_unit=$wegith_unit['title'];
				$item_weight=round($this->converttokg($wegith_unit,$product_details['weight']),2);
				$parsel_weight=$parsel_weight+$item_weight;
				$order_item_temp['items'][]=array(
													"description"=> $product2['name'],
													"quantity"=>$product2['quantity'],
													"sku"=>$product2['model']." ",
													"item_id"=>" ".$product2['product_id']." ",
													"price"=> array(
																	"amount"=>$product2['price'],
																	"currency"=>$order_info['currency_code']
																),
													"weight"=>array(
														"value"=> $item_weight,
														"unit"=> "kg"
													)
													
												);
			   }
			   $order_item_temp['weight']['value']=$parsel_weight;
				$order_item_temp['weight']['unit']="kg";
			$order_item[]=$order_item_temp;

		$order_status=$this->getPaymentStatus($order_info['order_status']);
		
	    
	     $headers = array(
	        "content-type: application/json",
	        "postmen-api-key:$postmenconnector_apikey"
	    );
		$parseurl=parse_url($order_info['store_url']);
		//$storeulr=$parseurl['scheme'].'://'.$parseurl['host'];
		
		if (preg_match("/www/i", $parseurl['host'])) {
			$storeulr = $parseurl['host'];
			$storeulr.=rtrim($parseurl['path'],"/");
		}else{
			$storeulr = 'www.' . $parseurl['host'];
			$storeulr.=rtrim($parseurl['path'],"/");
		}
		$note=$this->clean($order_info['comment']);
				
		$order_number=(isset($order_info['order_id']))?$order_info['invoice_prefix'].$order_info['order_id']:'';
		$slug='opencart';
		$store_id=$storeulr.'#opencart';
		
		$description=($order_info['shipping_method']!='')?$order_info['shipping_method']:'unknown';
		$country=($order_info['shipping_iso_code_3']!='')?$order_info['payment_iso_code_3']:$order_info['payment_iso_code_3'];
		$company_name=($order_info['shipping_company']!='')?$order_info['shipping_company']:$order_info['payment_company'];
		$contact_name=($order_info['shipping_firstname']!='')?$order_info['shipping_firstname']:$order_info['payment_firstname'];
		$contact_name.=' ';
		$contact_name.=($order_info['shipping_lastname']!='')?$order_info['shipping_lastname']:$order_info['payment_lastname'];
		$street1=($order_info['shipping_address_1']!='')?$order_info['shipping_address_1']:$order_info['payment_address_1'];
		$street2=($order_info['shipping_address_2']!='')?$order_info['shipping_address_2']:$order_info['payment_address_2'];
		$city=($order_info['shipping_city']!='')?$order_info['shipping_city']:$order_info['payment_city'];
		$state=($order_info['shipping_zone']!='')?$order_info['shipping_zone']:$order_info['payment_zone'];
		$postal_code=($order_info['shipping_postcode']!='')?$order_info['shipping_postcode']:$order_info['payment_postcode'];
		
	  $body = '{
	"order_number": "'.$order_number.'",
	"archived": false,
	"app_connection": {		
		"slug": "'.$slug.'",
		"store_id": "'.$store_id.'",
		"url":"'.$storeulr.'"
	},
	"shipping_method": {
		"ref": " ",
		"price": {
			"amount": '.$order_info['total'].',
			"currency": "'.$order_info['currency_code'].'"
		},
		"description": "'.$this->clean(substr($description, 0, 254)).'"
	},
	"note":" '.$note.' ",
	"order_status": "'.strtolower($order_status['order_status']).'",
	"payment": {
		"status": "'.strtolower($order_status['payment_status']).'"
	},
	"fulfilment": {
		"status": "'.strtolower($order_status['fulfilment_status']).'"
	},
	"shipment": {
		"ship_to": {
			"country": "'.$country.'",
			"company_name": " '.$company_name.'",			
			"contact_name": "'.$contact_name.'",			
			"phone": "'.$order_info['telephone'].' ",
			"fax": " '.$order_info['fax'].'",
			"email": "'.$order_info['email'].'",
			"street1": "'.$street1.'",			
			"street2": " '.$street2.'",			
			"city": "'.$city.'",			
			"state": "'.$state.'",			
			"postal_code": " '.$postal_code.'",					
			"type": "business"
		},
		"parcels":'.json_encode($order_item,JSON_NUMERIC_CHECK).'
	},
	
	"order_created_at": "'.date("Y-m-d",strtotime($order_info['date_added'])).'T'.date("H:i:s",strtotime($order_info['date_added'])).'Z"
}';


		$sql = "SELECT * FROM `".DB_PREFIX."postmenconnector` where order_id=".$order_info['order_id'];		
				$query = $this->db->query($sql);
				$result=$query->rows;
				if(!empty($result) and $result[0]['status']==1){
					$method = 'PUT';
				}else{
					//new order created
					$method = 'POST';
				}
	
	    $curl = curl_init();
		$request_datetime=date("Y-m-d H:i:s");
	    curl_setopt_array($curl, array(
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_URL => $url,
	        CURLOPT_CUSTOMREQUEST => $method,
	        CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => $body
	    ));
	
	    $response = curl_exec($curl);
	    $err = curl_error($curl);
		$info = curl_getinfo($curl);
		$responsetime=0;
		if(!empty($info)){
			$responsetime=$info['total_time'];//seconds
		}
		
		$response_datetime=date("Y-m-d H:i:s");		
	    curl_close($curl);
	
	   
	$status=0;
	$responseObj=json_decode($response,true);
	if($responseObj['meta']['code']==200){
		$status=1;
	}
	
	//cehck if urder already exist
	$sql = "SELECT * FROM `".DB_PREFIX."postmenconnector` where order_id=".$order_info['order_id'];		
		$query = $this->db->query($sql);
		$result=$query->rows;
		if(!empty($result)){
			$customer=
			$query = $this->db->query("update ".DB_PREFIX."postmenconnector  set  customer='".$this->db->escape($contact_name)."',total='".$order_info['total']."',date_added='".date("Y-m-d H:i:s",strtotime($order_info['date_added']))."',request='".$this->db->escape($body)."',response='".$this->db->escape($response)."',status=".$status.",order_status='".$order_info['order_status']."',request_datetime='".$request_datetime."',response_datetime='".$response_datetime."',response_time='".$responsetime."' where order_id=".$order_info['order_id']);	
		}else{
			//insert data into table 
			$query = $this->db->query("INSERT INTO ".DB_PREFIX."postmenconnector (order_id, customer, total,date_added,request,response,status,order_status,request_datetime,response_datetime,response_time)
VALUES (".$order_info['order_id'].", '".$this->db->escape($contact_name)."', '".$order_info['total']."','".date("Y-m-d H:i:s",strtotime($order_info['date_added']))."','".$this->db->escape($body)."','".$this->db->escape($response)."',".$status.",'".$order_info['order_status']."','".$request_datetime."','".$response_datetime."','".$responsetime."')");	
		}
		return true;

	}
	
	public function getWeightClassDescriptionsPostment($weight_class_id) {
		$weight_class_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		foreach ($query->rows as $result) {
			$weight_class_data[$result['language_id']] = array(
				'title' => $result['title'],
				'unit'  => $result['unit']
			);
		}

		return $weight_class_data;
	}
	
	function converttokg($unit,$number){
		if($unit=='Gram'){
			return ($number/1000);
		}
		if($unit=='Pound'){
			return ($number*0.45359237);
		}
		if($unit=='Ounce'){
			return ($number* 0.02834952);
		}
		
		return $number;
	}
	
	function getPaymentStatus($opnecartPaymentstatus){
		if(strtolower($opnecartPaymentstatus)=='missing orders'){
			return array('fulfilment_status'=>'unfulfilled','order_status'=>'Open','payment_status'=>'pending');
		}
		if(strtolower($opnecartPaymentstatus)=='canceled'){
			return array('fulfilment_status'=>'unfulfilled','order_status'=>'Cancelled','payment_status'=>'voided');
		}
		if(strtolower($opnecartPaymentstatus)=='chargeback'){
			return array('order_status'=>'Open','fulfilment_status'=>'unfulfilled','payment_status'=>'pending');
		}		
		if(strtolower($opnecartPaymentstatus)=='complete'){
			return array('order_status'=>'Open','fulfilment_status'=>'fulfilled','payment_status'=>'Paid');
		}
		if(strtolower($opnecartPaymentstatus)=='failed'){
			return array('order_status'=>'Open','fulfilment_status'=>'unfulfilled','payment_status'=>'pending');
		}
		if(strtolower($opnecartPaymentstatus)=='pending'){
			return array('order_status'=>'Open','fulfilment_status'=>'unfulfilled','payment_status'=>'pending');
		}
		if(strtolower($opnecartPaymentstatus)=='processed'){
			return array('order_status'=>'Open','fulfilment_status'=>'unfulfilled','payment_status'=>'Paid');
		}
		if(strtolower($opnecartPaymentstatus)=='processing'){
			return array('order_status'=>'Open','fulfilment_status'=>'unfulfilled','payment_status'=>'pending');
		}
		if(strtolower($opnecartPaymentstatus)=='refunded'){
			return array('order_status'=>'Open','fulfilment_status'=>'unfulfilled','payment_status'=>'refunded');
		}
		if(strtolower($opnecartPaymentstatus)=='reversed'){
			return array('order_status'=>'cancelled','fulfilment_status'=>'unfulfilled','payment_status'=>'Pending');
		}
		if(strtolower($opnecartPaymentstatus)=='shipped'){
			return array('order_status'=>'Open','fulfilment_status'=>'fulfilled','payment_status'=>'Paid');
		}
		if(strtolower($opnecartPaymentstatus)=='voided'){
			return array('order_status'=>'Cancelled','fulfilment_status'=>'unfulfilled','payment_status'=>'Voided');
		}
		
		return array('order_status'=>'open','fulfilment_status'=>'unfulfilled','payment_status'=>'pending');
	}
	
	function clean($string) {
	   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	
	   return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
	}
}


