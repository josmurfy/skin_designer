<?php
class ControllerExtensionModulePostmenconnector extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/postmenconnector');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$data['postment_masking_api_key']=$this->config->get('postmenconnector_maskingapikey');
		if ((@$this->request->post['action'] == 'configration') && $this->validate()) {
			 //$this->request->post['postmenconnector_endurl']='https://production-api-1001.postmen.io/v3/orders';
			 $this->request->post['postmenconnector_endurl']='https://production-api.postmen.com/v3/orders';
			 $postmenconnector_apikey=$this->request->post['postmenconnector_apikey'];
			 if (strpos($postmenconnector_apikey, '***') !== false) {
				$postmenconnector_apikey=$this->config->get('postmenconnector_apikey');
				$this->request->post['postmenconnector_apikey']=$postmenconnector_apikey;
			}			
			 $this->request->post['postmenconnector_maskingapikey']=substr($postmenconnector_apikey,0,3).'***'.substr($postmenconnector_apikey,-3,3);			
			 $response=$this->checkapikey();
			 
			 if(isset($response['meta']['code']) and $response['meta']['code']==4105){
				 $this->error['warning'] = "Invalid  API key.";
			 }else{
			
			
			  //set postmen order ulr			
			$this->model_setting_setting->editSetting('postmenconnector', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$data['success']=$this->language->get('text_success');
			$data['postment_masking_api_key']=$this->request->post['postmenconnector_maskingapikey'];
			$data['exportorder']=true;
			//$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
			 }
		}
		
		//export data to postmen		
		if (isset($this->request->post['action']) and $this->request->post['action'] == 'exportorders') {	
				ini_set('max_execution_time', 0);		
			   $orderimported=$this->synchOrders();			  
			   $data['success']=$orderimported." Orders  export to Postmen";			  
		}
		
		//resend order from opencart to postment
		
		if (isset($this->request->post['action']) and $this->request->post['action'] == 'order_resend') {
			$respns=$this->resendOrder($this->request->post['orderid']);
			if($respns==1){
				$data['success']="Order Successfully Resend To Postmen";
			}else{
				$this->error['warning'] = $respns;
			}
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_no_results'] = "No result found";
		$data['entry_apikey'] = $this->language->get('entry_apikey');		
		$data['entry_status'] = $this->language->get('entry_status');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		//get table data	
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
		}
		$data['sort'] = $sort;
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		
		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order_id'] = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'] . '&sort=order_id' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, true);
		$data['sort_customer'] = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, true);
		$data['sort_status'] = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'] . '&sort=status' . $url, true);
		$data['sort_total'] = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'] . '&sort=total' . $url, true);
		$data['sort_order_status'] = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'] . '&sort=order_status' . $url, true);

		
		
		$url = '';		
		
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}		

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		//get totla record
		$order_total = $this->getTotal();
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		//set url for resend
		$data['order_resend_url'] = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'] . '&action=order_resend' . $url, true);
		
		
		//reult from table
		$filter_data = array(
		    'sort'  => $sort,
			'order' => $order,			
			'start'             => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'             => $this->config->get('config_limit_admin')
		);
		$results = $this->getOrders($filter_data);
		
		foreach ($results as $result) {
			$data['Orders'][] = array(
				'customer'   => $result['customer'],
				'date_added'   =>date("d/m/Y",strtotime($result['date_added'])),
				'order_id' =>$result['order_id'],
				'id' =>$result['id'],
				'request' => $this->indent($result['request']),
				'response' => $this->indent($result['response']),
				'response_time' => $result['response_time'],
				'request_datetime' => $result['request_datetime'],
				'response_datetime' => $result['response_datetime'],
				'status' => $result['status'],
				'order_status'=>$result['order_status'],
				'total'  => $this->currency->format($result['total'], $this->config->get('config_currency')),
				
			);
		}
		
		

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
		$data['sort'] = $sort;
		$data['order'] = $order;
	    
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/module/postmenconnector', 'token=' . $this->session->data['token'].'&type=module', true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->post['postmenconnector_apikey'])) {
			$data['postmenconnector_apikey'] = $this->request->post['postmenconnector_apikey'];
		} else {
			$data['postmenconnector_apikey'] = $this->config->get('postmenconnector_apikey');
		}
		
		

		if (isset($this->request->post['postmenconnector_status'])) {
			$data['postmenconnector_status'] = $this->request->post['postmenconnector_status'];
		} else {
			$data['postmenconnector_status'] = $this->config->get('postmenconnector_status');
		}

		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/postmenconnector', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/postmenconnector')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	
	public function install() {
		$this->load->model('extension/event');		
		$this->model_extension_event->addEvent('postmenconnector', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/postmenconnector/on_order_created');
		
		//create table
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "postmenconnector` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL,
		  `customer` varchar(255),
		  `total` varchar(255),
		  `order_status` varchar(255),
		  `response_time` varchar(255),
		  `date_added` datetime NOT NULL,
		  `request_datetime` datetime NOT NULL,
		  `response_datetime` datetime NOT NULL,
		  `request` text COLLATE utf8_bin NOT NULL,
		  `response` text COLLATE utf8_bin NOT NULL,
		  `status` tinyint(1) NOT NULL,
		  PRIMARY KEY (`id`)
		)");
	}
	
	public function uninstall() {
		$this->load->model('extension/event');
		$this->model_extension_event->deleteEvent('postmenconnector');
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "postmenconnector`");
	}
	
	
	public function checkapikey(){
		$postmenconnector_apikey=$this->request->post['postmenconnector_apikey'];	
		$url=$this->request->post['postmenconnector_endurl'];	
	     $method = 'POST';
	     $headers = array(
	        "content-type: application/json",
	        "postmen-api-key:$postmenconnector_apikey"
	    );
	  $body = '';
	
	    $curl = curl_init();
	
	    curl_setopt_array($curl, array(
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_URL => $url,
	        CURLOPT_CUSTOMREQUEST => $method,
	        CURLOPT_HTTPHEADER => $headers,
			CURLOPT_POSTFIELDS => $body
	    ));
	
	    $response = curl_exec($curl);
	    $err = curl_error($curl);
	
	    curl_close($curl);
	
	    if ($err) {
	    	echo "cURL Error #:" . $err;
	    } else {
	    	return json_decode($response,true);
	    }
		
		
	
	}
	
	public function getTotal($data = array()) {
		$sql = "SELECT COUNT(DISTINCT id) AS total FROM `" . DB_PREFIX . "postmenconnector`";
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getOrders($data = array()) {
		$sql = "SELECT * FROM `".DB_PREFIX."postmenconnector`  ";		


		if (isset($data['sort'])) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY date_added";
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

		$query = $this->db->query($sql);

		return $query->rows;
	}
	
	public function resendOrder($orderid){
		
		$this->load->model('sale/order');
		$this->load->model('localisation/weight_class');
		$this->load->model('catalog/product');
		
		$postmenconnector_status = $this->config->get('postmenconnector_status');
	    $postmenconnector_apikey = $this->config->get('postmenconnector_apikey');
		$url = $this->config->get('postmenconnector_endurl');
		if($postmenconnector_status==0 or $postmenconnector_apikey=='')
		return "Api key NOt correct or status is disabled";
		
			
			$order_info = $this->model_sale_order->getOrder($orderid);						
			if(empty($order_info))
			return "Ordoer not found with id";			
			$products = $this->model_sale_order->getOrderProducts($orderid);
			
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
				$wegith_unit=$this->model_localisation_weight_class->getWeightClassDescriptions($product_details['weight_class_id']);
				
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
				$parseurl = parse_url($order_info['store_url']);
				if (preg_match("/www/i", $parseurl['host'])) {
					$storeulr = $parseurl['host'];
					$storeulr.=rtrim($parseurl['path'],"/");
				}else{
					$storeulr = 'www.' . $parseurl['host'];
					$storeulr.=rtrim($parseurl['path'],"/");
				}
				$note=$this->clean($order_info['comment']);
				$description=($order_info['shipping_method']!='')?$order_info['shipping_method']:$order_info['payment_method'];
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
			"order_number": "'.$order_info['invoice_prefix'].$order_info['order_id'].'", 
			"archived":false,
			"app_connection": {		
				"slug": "opencart",
				"store_id": "'.$storeulr.'#opencart",
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
					"phone": "'.$order_info['telephone'].'",
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
				$request_datetime=date("Y-m-d H:i:s");
				$curl = curl_init();
			
			$sql = "SELECT * FROM `".DB_PREFIX."postmenconnector` where order_id=".$orderid;		
				$query = $this->db->query($sql);
				$result=$query->rows;
				if(!empty($result) and $result[0]['status']==1){
					$method = 'PUT';
				}else{
					//new order created
					$method = 'POST';
				}
			
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
		$returnmsg=0;
		$responseObj=json_decode($response,true);
		//print_r($response);
		if($responseObj['meta']['code']==200){
			$status=1;
			$returnmsg=1;
		}else{
			$returnmsg=$responseObj['meta']['message'];
		}				
		
		//cehck if urder already exist
			$sql = "SELECT * FROM `".DB_PREFIX."postmenconnector` where order_id=".$orderid;		
				$query = $this->db->query($sql);
				$result=$query->rows;
				if(!empty($result)){
				
					$query = $this->db->query("update ".DB_PREFIX."postmenconnector  set  customer='".$this->db->escape($contact_name)."',total='".$order_info['total']."',date_added='".date("Y-m-d H:i:s",strtotime($order_info['date_added']))."',request='".$this->db->escape($body)."',response='".$this->db->escape($response)."',status=".$status.",order_status='".$order_info['order_status']."',request_datetime='".$request_datetime."',response_datetime='".$response_datetime."',response_time='".$responsetime."' where order_id=".$orderid);
				}else{
					//insert data into table 
					
					$query = $this->db->query("INSERT INTO ".DB_PREFIX."postmenconnector (order_id, customer, total,date_added,request,response,status,order_status,request_datetime,response_datetime,response_time)
		VALUES (".$order_info['order_id'].", '".$this->db->escape($contact_name)."', '".$order_info['total']."','".date("Y-m-d H:i:s",strtotime($order_info['date_added']))."','".$this->db->escape($body)."','".$this->db->escape($response)."',".$status.",'".$order_info['order_status']."','".$request_datetime."','".$response_datetime."','".$responsetime."')");	
				}
		
		
		
		return $returnmsg;
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
	function clean($string) {
	   //$string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
	
	   return preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
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
	
	public function indent($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

        // If this character is the end of an element,
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}

	function synchOrders(){
		ini_set('max_execution_time', 0);
		$sql = "SELECT O.order_id FROM `" . DB_PREFIX . "order` O left outer join `" . DB_PREFIX . "postmenconnector` P on `O`.order_id=`P`.order_id where P.order_id is NULL and O.date_added>=(CURDATE()-60) and (O.order_status_id<>3 and O.order_status_id<>5) ORDER BY O.date_added DESC limit 0,50";
		$query = $this->db->query($sql);
		$rows=$query->rows;
		$orderPushed=0;		
		foreach($rows as $rw){
		  $this->resendOrder($rw['order_id']);
		  $orderPushed++;
		}
		return $orderPushed;
	}

	
}