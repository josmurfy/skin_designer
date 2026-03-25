<?php
if (version_compare(VERSION,'3.0.0.0','>=' )) {
	define('TEMPLATE_FOLDER', 'oc3');
	define('EXTENSION_BASE', 'marketplace/extension');
	define('TOKEN_NAME', 'user_token');
	define('TEMPLATE_EXTN', '');
	define('EXTN_ROUTE', 'extension/hbapps');
}else if (version_compare(VERSION,'2.2.0.0','<=' )) {
	define('TEMPLATE_FOLDER', 'oc2');
	define('EXTENSION_BASE', 'extension/hbapps');
	define('TOKEN_NAME', 'token');
	define('TEMPLATE_EXTN', '.tpl');
	define('EXTN_ROUTE', 'hbapps');
}else{
	define('TEMPLATE_FOLDER', 'oc2');
	define('EXTENSION_BASE', 'extension/extension');
	define('TOKEN_NAME', 'token');
	define('TEMPLATE_EXTN', '');
	define('EXTN_ROUTE', 'extension/hbapps');
}
define('EXTN_VERSION', '5.1.2');

class ControllerExtensionHbappsOrderShipment extends Controller {
	
	private $error = array(); 
	
	public function index() {   
		$data['extension_version'] =  EXTN_VERSION;
				
		if (isset($this->request->get['store_id'])){
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$this->load->language(EXTN_ROUTE.'/order_shipment');
		$this->load->model('extension/hbapps/order_shipment');
		$this->document->setTitle($this->language->get('heading_title_shipment'));

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();

		$this->load->model('setting/setting');
		
		$store_info = $this->model_setting_setting->getSetting('hb_shipment', $this->request->get['store_id']);
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('hb_shipment', $this->request->post, $this->request->get['store_id']);	
			
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link(EXTN_ROUTE.'/order_shipment', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME].'&store_id='.$data['store_id'], true));
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		
		$text_strings = array(
			'heading_title_shipment','button_save','button_cancel'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME], true)
   		);
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link(EXTENSION_BASE, TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME] . '&type=hbapps', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title_shipment'),
			'href'      => $this->url->link(EXTN_ROUTE.'/order_shipment', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME].'&store_id='.$data['store_id'], true)
   		);
				
		$data['action'] = $this->url->link(EXTN_ROUTE.'/order_shipment', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME].'&store_id='.$data['store_id'], true);
		
		$data['cancel'] = $this->url->link(EXTENSION_BASE, TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME] . '&type=hbapps', true);
		$data[TOKEN_NAME] = $this->session->data[TOKEN_NAME];
		$data['base_route'] = EXTN_ROUTE;
		
		$data['hb_shipment_template'] 		= isset($store_info['hb_shipment_template'])?$store_info['hb_shipment_template']:'';
		
		$data['hb_shipment_img_w'] 			= isset($store_info['hb_shipment_img_w'])?$store_info['hb_shipment_img_w']: '100';
		$data['hb_shipment_img_h'] 			= isset($store_info['hb_shipment_img_h'])?$store_info['hb_shipment_img_h']: '100';
		
		$data['hb_shipment_admin_email'] 	= isset($store_info['hb_shipment_admin_email'])?$store_info['hb_shipment_admin_email']: $this->config->get('config_email') ;
		$data['hb_shipment_sender'] 		= isset($store_info['hb_shipment_sender'])?$store_info['hb_shipment_sender']: $this->config->get('config_name') ;
		
		$data['hb_shipment_preview_enable'] 	= isset($store_info['hb_shipment_preview_enable'])?$store_info['hb_shipment_preview_enable']:'';
		$data['hb_shipment_quick_send'] 		= isset($store_info['hb_shipment_quick_send'])?$store_info['hb_shipment_quick_send']:'';
		
		$data['shipment_form_templates'] 		= array('order_shipment_form','order_shipment_form_compact_qty_asc','order_shipment_form_compact_qty_desc');
		$data['hb_shipment_form_template'] 		= isset($store_info['hb_shipment_form_template'])?$store_info['hb_shipment_form_template']:'order_shipment_form';
		
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		foreach ($data['languages'] as $language){
			$language_id = $language['language_id'];
			$data['hb_shipment_subject_single'][$language_id] = isset($store_info['hb_shipment_subject_single'.$language_id]) ? $store_info['hb_shipment_subject_single'.$language_id]: '{product_name} from your order has been shipped';
			$data['hb_shipment_subject_multiple'][$language_id] = isset($store_info['hb_shipment_subject_multiple'.$language_id]) ? $store_info['hb_shipment_subject_multiple'.$language_id]: 'Shipped : {product_name_limited} and {remaining} item(s)';
			
			$data['hb_shipment_tblock_1'][$language_id] = isset($store_info['hb_shipment_tblock_1'.$language_id]) ? $store_info['hb_shipment_tblock_1'.$language_id]: '';
			$data['hb_shipment_tblock_2'][$language_id] = isset($store_info['hb_shipment_tblock_2'.$language_id]) ? $store_info['hb_shipment_tblock_2'.$language_id]: '';
			$data['hb_shipment_tblock_3'][$language_id] = isset($store_info['hb_shipment_tblock_3'.$language_id]) ? $store_info['hb_shipment_tblock_3'.$language_id]: '';
			$data['hb_shipment_tblock_4'][$language_id] = isset($store_info['hb_shipment_tblock_4'.$language_id]) ? $store_info['hb_shipment_tblock_4'.$language_id]: '';
			
			$data['hb_shipment_sms'][$language_id] = isset($store_info['hb_shipment_sms'.$language_id]) ? $store_info['hb_shipment_sms'.$language_id]: '';
			$data['hb_shipment_shipped_comment'][$language_id] = isset($store_info['hb_shipment_shipped_comment'.$language_id]) ? $store_info['hb_shipment_shipped_comment'.$language_id]: 'Products in your order have been shipped. Tracking Link: {tracking_link} - Tracking ID: {tracking_id}';		
		}
		
		$data['template_lists'] = $this->email_templates();
		
		//settings
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['hb_shipment_eligible_status'] 		= isset($store_info['hb_shipment_eligible_status'])?$store_info['hb_shipment_eligible_status']: array();
		$data['hb_shipment_shipped_status'] 		= isset($store_info['hb_shipment_shipped_status'])?$store_info['hb_shipment_shipped_status']:'';
		
		$data['check_opencart_sms_installation']    = $this->model_extension_hbapps_order_shipment->check_opencart_sms_installation(); 
		$data['hb_shipment_send_sms'] 				= isset($store_info['hb_shipment_send_sms'])? $store_info['hb_shipment_send_sms']:'';
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/hbapps/'.TEMPLATE_FOLDER.'/order_shipment'.TEMPLATE_EXTN, $data));

	}
	
	private function email_templates(){
		$template_folder_path    = "view/template/extension/hbapps/".TEMPLATE_FOLDER."/order_shipment_templates";
		$files = array_diff(scandir($template_folder_path), array('.', '..'));
		$data['template_lists'] = array();
		foreach ($files as $file) {
			$file = str_replace('.tpl','',$file);
			$file = str_replace('.twig','',$file);
			$data['template_lists'][] = array(
				'label' => ucwords(str_replace('_',' ',$file)),
				'value'	=> $file
			);
		}
		return $data['template_lists'];
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', EXTN_ROUTE.'/order_shipment')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
	
	public function partner_list() {  
		$store_id = (int)$this->request->get['store_id'];		
		$this->load->model('extension/hbapps/order_shipment');
		$this->load->language(EXTN_ROUTE.'/order_shipment');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'store_id'	=> $store_id
		);

		$data[TOKEN_NAME] = $this->session->data[TOKEN_NAME];	
		
		$text_strings = array(
			'column_name','column_link', 'column_action','button_edit'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
		
		$reports_total = $this->model_extension_hbapps_order_shipment->getTotalPartners($data); 		
		$records = $this->model_extension_hbapps_order_shipment->getPartners($data);
		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' 				=> $record['id'],
				'name' 				=> $record['name'],
				'link'				=> $record['link'],
				'date_added' 		=> $record['date_added']
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link(EXTN_ROUTE.'/order_shipment/partner_list', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME] . '&store_id='.$store_id.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbapps/'.TEMPLATE_FOLDER.'/order_shipment_list'.TEMPLATE_EXTN, $data));
	}
	
	public function order_list(){
		$store_id = (int)$this->request->get['store_id'];		
		$this->load->model('extension/hbapps/order_shipment');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['search'])) {
			$search = $this->request->get['search'];
		} else {
			$search = '';
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->get['search'])) {
			$url .= '&search=' . $this->request->get['search'];
		}
		
		$data = array(
			'start' 	=> ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' 	=> $this->config->get('config_limit_admin'),
			'store_id'	=> $store_id,
			'search'	=> $search
		);

		$data[TOKEN_NAME] = $this->session->data[TOKEN_NAME];	
		
		$reports_total = $this->model_extension_hbapps_order_shipment->getTotalShipmentOrders($data); 		
		$records = $this->model_extension_hbapps_order_shipment->getShipmentOrders($data);
		$data['records'] = array();
		foreach ($records as $record) {
			$data['records'][] = array(
				'id' 			=> $record['id'],
				'order_id' 		=> $record['order_id'],
				'product' 		=> $record['product_name'],
				'model' 		=> $record['model'],
				'product_link'	=>	$this->url->link('catalog/product/edit', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME].'&product_id='.$record['product_id']),
				'order_link'	=>	$this->url->link('sale/order/info', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME].'&order_id='.$record['order_id']),
				'shipping_partner' 	=> $record['ship_name'],
				'shipped_qty' 	=> $record['shipped_qty'],
				'code' 			=> $record['code'],
				'tracking_link'	=> str_replace('{tracking_id}', $record['code'], $record['link']),
				'delivery_date'	=> ($record['delivery_date'] == NULL or $record['delivery_date'] == '0000-00-00') ? '' : date('d-m-Y',strtotime($record['delivery_date'])),
				'mail' 			=> ($record['mail'] == 1)? '<span style="color:green;"><i class="fa fa-check"></i> Sent</span>' : '<span style="color:orange;"><i class="fa fa-exclamation-circle"></i> Not Sent</span>',
				'date_added' 	=> date('d-M-Y H:i:s', strtotime($record['date_added'])),
				'date_modified' 	=> date('d-M-Y H:i:s', strtotime($record['date_modified']))
			);
		}
		
		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link(EXTN_ROUTE.'/order_shipment/order_list', TOKEN_NAME.'=' . $this->session->data[TOKEN_NAME] . '&store_id='.$store_id.'&search='.$search.'&page={page}', true);

		$data['pagination'] = $pagination->render();
		$limit = $this->config->get('config_limit_admin');

		$data['results'] = sprintf($this->language->get('text_pagination'), ($pagination->total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($pagination->total - $limit)) ? $pagination->total : ((($page - 1) * $limit) + $limit), $pagination->total, ceil($pagination->total / $limit));

		$this->response->setOutput($this->load->view('extension/hbapps/'.TEMPLATE_FOLDER.'/order_shipment_info_list'.TEMPLATE_EXTN, $data));
	}
	
	public function addpartner(){
		$this->load->model('extension/hbapps/order_shipment');
		$store_id = (int)$this->request->get['store_id'];	
		$name = trim($this->request->post['name']);
		$tracking_url = trim($this->request->post['tracking_url']);
		
		if (empty($name) or empty($tracking_url)){
			$json['warning'] = 'Please fill all fields!';
		}else{
			$this->model_extension_hbapps_order_shipment->addpartner($name, $tracking_url, $store_id);
			$json['success'] = 'Shipment Partner added Successfully';
		}
		$this->response->setOutput(json_encode($json));
	}
	
	public function editpartner(){
		$this->load->model('extension/hbapps/order_shipment');
		$store_id = (int)$this->request->get['store_id'];	
		$name = trim($this->request->post['name']);
		$id = trim($this->request->post['id']);
		$tracking_url = trim($this->request->post['tracking_url']);
		
		if (empty($name) or empty($tracking_url)){
			$json['warning'] = 'Please fill all fields!';
		}else{
			$this->model_extension_hbapps_order_shipment->editpartner($id, $name, $tracking_url);
			$json['success'] = 'Shipment Partner edited Successfully';
		}
		$this->response->setOutput(json_encode($json));
	}
	
	public function deletepartner(){
		$this->load->model('extension/hbapps/order_shipment');
		
		if (!isset($this->request->post['selected']) ){
			$json['warning'] = 'No Record Selected!';
		}else{
			$count = 0;
			$json['success'] = '';
			foreach ($this->request->post['selected'] as $id) {
				$this->model_extension_hbapps_order_shipment->deletepartner($id);
				$count = $count + 1;
			}
			$json['success'] .= $count.' item(s) deleted.';
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function shipment_form(){		
		$data['order_id'] = (int)$this->request->get['order_id'];
		
		if (isset($this->request->get['store_id'])) {
			$data['store_id'] = (int)$this->request->get['store_id'];
		}else{
			$data['store_id'] = 0;
		}
		
		$data[TOKEN_NAME] = $this->session->data[TOKEN_NAME];
		$data['base_route'] = EXTN_ROUTE;
		
		$this->load->language(EXTN_ROUTE.'/order_shipment');
		$this->load->model('extension/hbapps/order_shipment');
		$this->load->model('sale/order');
		
		$text_strings = array(
			'button_save','label_courier_product','label_courier','label_courier_track','label_courier_date','select_courier','select_courier_all','tab_courier'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
		
		$data['order_products'] = array();

		$products = $this->model_sale_order->getOrderProducts($data['order_id']);
		
		foreach ($products as $product) {
			$remaining_qty = $this->model_extension_hbapps_order_shipment->remaining_qty($data['order_id'], $product['order_product_id'], $product['quantity']);
			
			if ($remaining_qty > 0) {
			$data['order_products'][] = array(
				'order_product_id' => $product['order_product_id'],
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $this->model_sale_order->getOrderOptions($data['order_id'], $product['order_product_id']),
				'quantity'   => $product['quantity'],
				'remaining_qty'   => $remaining_qty,
				'price'      => $product['price'],
				'total'      => $product['total'],
				'reward'     => $product['reward']
			);
			}
		}
		
		$data['total_products'] =  count($data['order_products']);
		
		$data['shipment_partner_id'] = $data['shipped_product_id'] = $data['tracking_code'] = $data['courier_name'] =  $data['delivery_date']  = '';
				
		$last_saved_order_shipment_details = $this->db->query("SELECT * FROM `".DB_PREFIX."hb_shipment_order_info` a, `".DB_PREFIX."hb_shipping_company` b WHERE a.courier_id = b.id and a.order_id = '".(int)$data['order_id']."' ORDER BY date_modified DESC LIMIT 1 ");
		
		if ($last_saved_order_shipment_details->row) {
			$last_saved = $last_saved_order_shipment_details->row;
			$data['shipment_partner_id']    =   $last_saved['courier_id'];
			$data['shipped_product_id']    	=   $last_saved['product_id'];
			$data['tracking_code']   		=   $last_saved['code'];
			$data['courier_name']    		=   $last_saved['name'];
			$data['delivery_date']    		=   $last_saved['delivery_date'];
		}	
		
		$data['partners'] = $this->model_extension_hbapps_order_shipment->get_all_courier($data['store_id']);
		
		$this->load->model('setting/setting');
		$extn_info 	= $this->model_setting_setting->getSetting('hb_shipment', $data['store_id']);
		
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['hb_shipment_shipped_status'] 		= isset($extn_info['hb_shipment_shipped_status'])?$extn_info['hb_shipment_shipped_status']: 0;
		$data['hb_shipment_eligible_status'] 		= isset($extn_info['hb_shipment_eligible_status'])?$extn_info['hb_shipment_eligible_status']: array();
		$data['hb_shipment_quick_send'] 			= isset($extn_info['hb_shipment_quick_send'])? true : false;
		
		$hb_shipment_form_template 		= isset($extn_info['hb_shipment_form_template'])?$extn_info['hb_shipment_form_template']:'order_shipment_form';
				
		$this->response->setOutput($this->load->view('extension/hbapps/'.TEMPLATE_FOLDER.'/'.$hb_shipment_form_template.TEMPLATE_EXTN, $data));
	}
	
	public function savecourier(){
		$order_id			= $this->request->get['order_id'];
		$store_id			= $this->request->get['store_id'];
		$mail				= $this->request->get['mail'];

		$courier_id			= $this->request->post['courier_select'];
		$tracking_code 		= $this->request->post['courier_tracking_id'];
		$order_status_id 	= $this->request->post['shipped_order_status'];
		$delivery_date 		= $this->request->post['delivery_date'];
		
		$selected_order_product_id 		= $this->request->post['courier_select_product'];
		$qty 				= $this->request->post['qty'];
		
		$this->load->model('extension/hbapps/order_shipment');
		
		//$data			= $this->request->post;
		$inserted_id_array = array();
		foreach ($selected_order_product_id as $order_product_id) {
			$query_product_id = $this->db->query("SELECT product_id FROM `".DB_PREFIX."order_product` WHERE order_product_id = '".(int)$order_product_id."' LIMIT 1");
			$product_id = (int)$query_product_id->row['product_id'];
			
			$shipped_qty = $qty[$order_product_id];
			if ($shipped_qty > 0 ) {		
				$this->db->query("INSERT INTO `".DB_PREFIX."hb_shipment_order_info` (`order_id`,`courier_id`,`order_product_id`,`product_id`, `shipped_qty`,`code`,`delivery_date`,`store_id`,`date_modified`) VALUES ('".(int)$order_id."','".(int)$courier_id."','".(int)$order_product_id."','".(int)$product_id."','".(int)$shipped_qty."','".$this->db->escape($tracking_code)."','".$this->db->escape($delivery_date)."','".(int)$store_id."',now())");
				$inserted_id_array[] = $this->db->getLastId();
			}
		}
		
		if ($mail == 1) {
			//$string_order_product_id = implode(',',$selected_order_product_id);
			$string_inserted_id_array = implode(',',$inserted_id_array);
			$this->model_extension_hbapps_order_shipment->send_email($order_id, $string_inserted_id_array ,true,$order_status_id);
			$json['success'] = 'Shipment details for this order saved successfully and Email is sent!';
		}else{
			$json['success'] = 'Shipment details for this order saved successfully!';
		}
	
		$this->response->setOutput(json_encode($json));
		
	}
	
	public function order_shipment_info_table() {
		$data['store_id'] = (int)$this->request->get['store_id'];
		
		if (isset($this->request->get['order_id'])){
			$order_id = (int)$this->request->get['order_id'];
		}else{
			$order_id = 0;
		}
		
		$data['order_id'] = $order_id;
		
		$this->load->model('sale/order');
		$this->load->language('extension/hbapps/order_shipment');
		
		$data['text_no_results'] 		= $this->language->get('text_no_results');
		
		$data[TOKEN_NAME] = $this->session->data[TOKEN_NAME];	
		$data['base_route'] = EXTN_ROUTE;
		
		$data['order_shipment_data'] = array();
		
		$order_shipment_data = $this->db->query("SELECT *, a.id as shipment_id, c.name as pname, b.name as cname FROM " . DB_PREFIX . "hb_shipment_order_info a , `" . DB_PREFIX . "hb_shipping_company` b, " . DB_PREFIX . "order_product c where a.courier_id = b.id and a.order_product_id = c.order_product_id and a.order_id = c.order_id and a.order_id = '".(int)$order_id."' ORDER BY a.date_modified");
	
		if ($order_shipment_data->rows) {			
			foreach ($order_shipment_data->rows as $row) {
				$data['order_shipment_data'][] = array(
					'shipment_id'	=> $row['shipment_id'],
					'order_product_id'	=> $row['order_product_id'],
					'product_id'	=> $row['product_id'],
					'model'			=> $row['model'],
					'shipped_qty'	=> $row['shipped_qty'],
					'product_name'	=> $row['pname'],
					'option'     	=> $this->model_sale_order->getOrderOptions($row['order_id'], $row['order_product_id']),
					'partner_name'	=> $row['cname'],
					'code'			=> $row['code'],
					'tracking_link'	=> str_replace('{tracking_id}',$row['code'],$row['link']),
					'delivery_date'	=> ($row['delivery_date'] == NULL or $row['delivery_date'] == '0000-00-00') ? '' : date('d-m-Y',strtotime($row['delivery_date'])),
					'mail'			=> $row['mail']
				);
			}
		}
		
		$this->load->model('setting/setting');
		$extn_info 	= $this->model_setting_setting->getSetting('hb_shipment', $this->request->get['store_id']);
		
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		//$data['hb_shipment_eligible_status'] 		= isset($extn_info['hb_shipment_eligible_status'])?$extn_info['hb_shipment_eligible_status']: array();
		$data['hb_shipment_shipped_status'] 		= isset($extn_info['hb_shipment_shipped_status'])?$extn_info['hb_shipment_shipped_status']: 0;
		$data['hb_shipment_preview_enable'] 		= isset($extn_info['hb_shipment_preview_enable'])? true : false;

		$this->response->setOutput($this->load->view('extension/hbapps/'.TEMPLATE_FOLDER.'/order_shipment_info_table'.TEMPLATE_EXTN, $data));
	}
	
	public function editform(){		
		$id = (int)$this->request->get['id'];
		$data[TOKEN_NAME] = $this->session->data[TOKEN_NAME];
		$data['base_route'] = EXTN_ROUTE;
		
		$this->load->model('extension/hbapps/order_shipment');
		
		$item = $this->db->query("SELECT *, c.name as pname, b.name as cname FROM `".DB_PREFIX."hb_shipment_order_info` a,  `" . DB_PREFIX . "hb_shipping_company` b, " . DB_PREFIX . "order_product c WHERE a.courier_id = b.id and a.product_id = c.product_id AND a.`id` = '".(int)$id."'");
		
		$data['id'] 			= $id;
		$data['store_id'] 		= $item->row['store_id'];
		$data['product_name'] 	= $item->row['pname'];	
		$data['courier_name'] 	= $item->row['cname'];
		$data['courier_id'] 	= $item->row['courier_id'];
		$data['tracking_id']  	= $item->row['code'];
		$data['order_id']  		= $item->row['order_id'];	
		$data['delivery_date']  = ($item->row['delivery_date'] == NULL or $item->row['delivery_date'] == '0000-00-00') ? '' : date('d-m-Y',strtotime($item->row['delivery_date']));	
		$data['shipped_qty'] 	= $item->row['shipped_qty'];
		
		$data['partners'] = $this->model_extension_hbapps_order_shipment->get_all_courier($data['store_id']);
		
		$this->response->setOutput($this->load->view('extension/hbapps/'.TEMPLATE_FOLDER.'/order_shipment_info_table_edit'.TEMPLATE_EXTN, $data));
	}
	
	public function updateitem(){		
		$data = $this->request->post;
		
		$id 			= (int)$data['id'];
		$courier_id 	= (int)$data['courier_id'];
		$tracking_id	= trim($data['tracking_id']);
		$delivery_date 	= trim($data['delivery_date']);
		$shipped_qty 	= (int)$data['shipped_qty'];
		
		if ($this->validate()) {
			if (empty($tracking_id)){
				$json['error'] = 'Please enter tracking ID!!!';
			}else{
				$this->db->query("UPDATE `".DB_PREFIX."hb_shipment_order_info` SET `shipped_qty` = '".(int)$shipped_qty."', `courier_id` = '".(int)$courier_id."', `code` = '".$this->db->escape($tracking_id)."', `delivery_date` = '".$this->db->escape($delivery_date)."', `date_modified` = now() WHERE id = '".(int)$id."'");
				$json['success'] = 'Row updated successfully!';
			}
		}else{
			$json['error'] = 'You do not have permission!';
		}

		$this->response->setOutput(json_encode($json));
	}
	
	public function deleteitem(){
		$id = (int)$this->request->post['id'];
		
		if ($this->validate()) {
			$this->db->query("DELETE FROM `".DB_PREFIX."hb_shipment_order_info` WHERE `id` = '".(int)$id."'");
			$json['success'] = 'Item deleted successfully';
		}else{
			$json['error'] = 'You do not have permission!';
		}
 
		$this->response->setOutput(json_encode($json));
	}
	
	public function validate_products(){
		$order_id = (int)$this->request->get['order_id'];
		
		if (isset($this->request->get['shipment_id'])) {
			$shipment_id = $this->request->get['shipment_id'];
		}else{
			$shipment_id = 0;
		}
				
		if ($shipment_id == 0) {
			//get all shipment rows of the product 
			$product_ids = array();
			$query_shipments = $this->db->query("SELECT id FROM `" . DB_PREFIX . "hb_shipment_order_info` WHERE order_id = '".(int)$order_id."'");
			if ($query_shipments->rows) {
				foreach ($query_shipments->rows as $row) {
					$shipment_id_array[] = $row['id'];
				}
			}
		}else{
			$shipment_id_array = explode(',',$shipment_id);
		}
		
		$total_products = count($shipment_id_array);
		
		if ($total_products > 1) {
			$query = $this->db->query("SELECT count(*) as count FROM `" . DB_PREFIX . "hb_shipment_order_info` WHERE order_id = '".(int)$order_id."' AND id IN (".implode(',',$shipment_id_array).") AND `code` = (SELECT code FROM " . DB_PREFIX . "hb_shipment_order_info WHERE order_id = '".(int)$order_id."' AND id IN (".implode(',',$shipment_id_array).") LIMIT 1)");
			$same_tracking_count = $query->row['count'];

			if ($total_products != $same_tracking_count) {
				$json['error'] = 'Tracking information are not same for the items selected! You can send only one tracking code in an email, but the selected items do not have same tracking information.';
			}
		}
		
		$json['success'] = true;

		$this->response->setOutput(json_encode($json));
	}
	
	public function notify_customer(){		
		$order_id = (int)$this->request->get['order_id'];
		
		if (isset($this->request->get['shipment_id'])) {
			$shipment_id = $this->request->get['shipment_id'];

		}else{
			$shipment_id = 0;
		}
		
		if (isset($this->request->get['shipped_order_status'])) {
			$shipped_order_status = (int)$this->request->get['shipped_order_status'];
		}else{
			$shipped_order_status = 0;
		}
		
		if (isset($this->request->get['preview'])) {
			$preview = true;
		}else{
			$preview = false;
		}
		
		$data[TOKEN_NAME] = $this->session->data[TOKEN_NAME];
		$data['base_route'] = EXTN_ROUTE;
		
		$this->load->model('extension/hbapps/order_shipment');
				
		if ($preview === true){
			$mail_preview = $this->model_extension_hbapps_order_shipment->send_email($order_id,$shipment_id,false,0);	
			echo '<div style="text-align:center;background-color: #777;padding: 10px;color: #fff;" title="Email Subject">'.$mail_preview['subject'].'</div>';
			echo $mail_preview['content'];
		}else{
			$this->model_extension_hbapps_order_shipment->send_email($order_id,$shipment_id,true,$shipped_order_status);
			$json['success'] = 'Email sent successfully';
			$this->response->setOutput(json_encode($json));
		}
	}
	
	public function loadSampleBlock(){
		$selected_template = $this->request->get['template'];
		$this->load->model('setting/setting');
		$config = $this->model_setting_setting->getSetting('config', $this->request->get['store_id']);
		$logo = isset($config['config_logo'])? HTTPS_CATALOG.'image/'.$config['config_logo'] : HTTPS_CATALOG.'image/catalog/logo.png';
		for ($x = 1; $x <= 4; $x++) {
			$file = 'view/template/extension/hbapps/'.TEMPLATE_FOLDER.'/order_shipment_sample_blocks/'.$selected_template.'_block'.$x.'.txt';
			if (file_exists($file)) {
				$json['block'.$x] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
				$json['block'.$x] = str_replace('{store_url}',HTTPS_CATALOG,$json['block'.$x]);
				$json['block'.$x] = str_replace('{logo}',$logo,$json['block'.$x]);
			}else{
				$json['block'.$x] = ' ';
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function install(){
		$this->load->model('extension/hbapps/order_shipment');
		$this->model_extension_hbapps_order_shipment->install();
		$data['success'] = 'This extension has been installed successfully';
	}
	
	public function upgrade(){
		$this->load->model('extension/hbapps/order_shipment');
		$this->model_extension_hbapps_order_shipment->upgrade();
		$data['success'] = 'This extension has been updated successfully';
	}
	
	public function uninstall(){
		$this->load->model('extension/hbapps/order_shipment');
		$this->model_extension_hbapps_order_shipment->uninstall();
		$data['success'] = 'This extension is uninstalled successfully';
	}
	
}
?>