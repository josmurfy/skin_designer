<?php
/***********************
// @category  : OpenCart
// @module    : Spin Wheel Popup
// @author    : OpencartMarketplace <support@opencartmarketplace.com>
***********************/

class ControllerExtensionModuleSpinWheel extends Controller {
	static $__setting = array();
	public function index($setting) {
		$this->load->language('extension/module/spin_wheel');

		//$this->document->addScript('catalog/view/javascript/jquery/ocmp_spin_wheel/clipboard.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/ocmp_spin_wheel/spin_wheel.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/ocmp_spin_wheel/jquery.fireworks.js');
		$this->document->addStyle('catalog/view/javascript/jquery/ocmp_spin_wheel/spin_wheel.min.css');
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$this->session->data['sw__setting'] = $setting;
		
		
		$this->load->model('extension/module/spin_wheel');
		$data['wheel_offers'] = $this->model_extension_module_spin_wheel->getWheelOffers();
		
		$data['language_id'] = $this->config->get('config_language_id');

		$data['spin_wheel'] = ($setting['spin_wheel']) ? $setting['spin_wheel'] : array();

		$data['bg_image'] = isset($data['spin_wheel']['bg_image']) ? HTTP_SERVER .'image/' .$data['spin_wheel']['bg_image'] : '';
		
		$notes = isset($data['spin_wheel'][$data['language_id']]) ? $data['spin_wheel'][$data['language_id']]['note'] : '';
		
		$data['notes'] = explode("\n", preg_replace('~\r?\n~', "\n", $notes));
		
		
		$data['button_continue'] = $this->language->get('button_continue');
		
		$data['send_mail'] = $this->language->get('text_send_mail');

		//Errors
		$error = array();
		$error['firstname'] = $this->language->get('error_fistname');
		$error['lastname'] = $this->language->get('error_lastname');
		$error['email_invalid'] = $this->language->get('error_email_invalid');
		$error['email_empty'] = $this->language->get('error_email_empty');
		$error['email_exist'] = $this->language->get('error_exist');
		
		$data['errors'] = json_encode($error);
		
		$status = true;
		if(isset($data['spin_wheel']['fix_time']) && $data['spin_wheel']['fix_time'] == 1){
			$status = false;
			$Todaydate = strtotime(date('m/d/Y'));
			
			$startDate = (isset($data['spin_wheel']['active_date']) && $data['spin_wheel']['active_date'] != '') ? strtotime($data['spin_wheel']['active_date']) : '';
			$endDate = (isset($data['spin_wheel']['expire_date']) && $data['spin_wheel']['expire_date'] != '') ? strtotime($data['spin_wheel']['expire_date']) : '';
			
			
			if($startDate != '' && $endDate != ''){ 
				if($startDate < $Todaydate && $endDate > $Todaydate){
					$status = true;	
				}
			}	
		}
		
		//Check Customer Group
		if(isset($data['spin_wheel']['customer_group']) && in_array($this->config->get('config_customer_group_id'), $data['spin_wheel']['customer_group'])){
			$status = true;	
		}else{
			$status = false;	
		}

		//Check Store
		if(isset($data['spin_wheel']['stores']) && in_array($this->config->get('config_store_id'), $data['spin_wheel']['stores'])){
			$status = true;	
		}else{
			$status = false;	
		}

		
		if(VERSION <= '2.2.0.0'){
			$data['emailCheckUrl'] = 'index.php?route=module/spin_wheel/emailExist';
			$data['emailSend'] = 'index.php?route=module/spin_wheel/sendMail';
			$data['onRotate'] = 'index.php?route=module/spin_wheel/save';
		}else{
			$data['emailCheckUrl'] = 'index.php?route=extension/module/spin_wheel/emailExist';			
			$data['emailSend'] = 'index.php?route=extension/module/spin_wheel/sendMail';
			$data['onRotate'] = 'index.php?route=extension/module/spin_wheel/save';
		}

		if($status){
			return $this->load->view('extension/module/spin_wheel', $data);
		}	
	}
	
	public function save(){
		
		$json = array();
		
		$this->load->language('extension/module/spin_wheel');
		
		$this->load->model('extension/module/spin_wheel');
		
		if(isset($this->request->post['email'])){
			$email = $this->request->post['email'];
		}else{
			$email = '';
		}		
		
		
		$setting = isset($this->session->data['sw__setting']) ? $this->session->data['sw__setting'] : array();
		$spin_wheel = isset($setting['spin_wheel']) ? $setting['spin_wheel'] : array();
		
		if(isset($spin_wheel['email_recheck']) && ($spin_wheel['email_recheck'])){
			$validateEmail = $this->model_extension_module_spin_wheel->getEmailExist($email);
			
			if($validateEmail){
				$json['error_email_exist'] = true;
			}		
		}			$validateIPDate = $this->model_extension_module_spin_wheel->getIPSameDay($this->request->server['REMOTE_ADDR']);
		
		if(!$json){
			$WheelOffers = $this->model_extension_module_spin_wheel->getWheelOfferByGravity();		

			$offer_no = array_rand($WheelOffers, 1); 
			
			if($offer_no){
				$CouponData = $this->model_extension_module_spin_wheel->addWheelForm($offer_no, $this->request->post);
			}
			
			if($CouponData['code'] && $offer_no){
				$offer_info = $this->model_extension_module_spin_wheel->getWheelOffer($offer_no);
				
				if($offer_info['type'] == 3){
					$discount = $offer_info['label'];
				}elseif($offer_info['type'] == 2){
					$discount = (int)$offer_info['discount'] . '%';				
				}else{
					$discount = $this->currency->format($offer_info['discount'], $this->session->data['currency']);	
				}				
				
				if($offer_info['type'] == 3){
					$json['success'] = true;
				
					$json['result'] = 'win';
					$json['result_title'] = $this->language->get('text_win_shipping_title');
					$json['result_description'] = $this->language->get('text_win_shipping_desc');
					$json['label'] = $offer_info['label'];								
					$json['offer_no'] = $offer_info['offer_id'];
					$json['discount'] = $discount;								
					$json['type'] = $offer_info['type'];	

					$json['code'] = $CouponData['code'];		
				}elseif(($offer_info['type'] == 1 || $offer_info['type'] == 2) && ($offer_info['discount'] >= 1)){
					$json['success'] = true;
					
					$json['result'] = 'win';
					
					$json['result_title'] = sprintf($this->language->get('text_win_title'), $discount);
					$json['result_description'] = $this->language->get('text_win_description');
					$json['offer_no'] = $offer_info['offer_id'];
					$json['label'] = $offer_info['label'];								
					$json['type'] = $offer_info['type'];	
					$json['discount'] = $discount;		
					$json['code'] = $CouponData['code'];								
				}else{
					$json['success'] = true;
					
					$json['result'] = 'loose';
					$json['result_title'] = $this->language->get('text_loose_title');
					$json['result_description'] = $this->language->get('text_loose_desc');
					$json['label'] = $offer_info['label'];								
					$json['offer_no'] = $offer_info['offer_id'];
					$json['discount'] = 0;								
					$json['type'] = "";			
					$json['code'] = "";		
				}	
			}else{
				$json['success'] = false;
				
				$json['result'] = false; 
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));				
	}
	
	private function array_random_assoc($arr, $num = 1) {
		$keys = array_keys($arr);
		shuffle($keys);
		
		$r = array();
		for ($i = 0; $i < $num; $i++) {
			//$r[$keys[$i]] = $arr[$keys[$i]];
			$r[$keys[$i]] = $arr[$keys[$i]];
		}
		return $r;
	}

	public function emailExist(){
		$json = array();
		
		if(isset($this->request->post['email'])){
			$email = $this->request->post['email'];
		}else{
			$email = '';
		}
		
		$this->load->model('extension/module/spin_wheel');
		
		$validateEmail = $this->model_extension_module_spin_wheel->getEmailExist($email);
		
		if($validateEmail){
			$json['error'] = true;
		}else{
			$json['error'] = false;
		}
		
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}

	public function sendMail(){
		$setting = isset($this->session->data['sw__setting']) ? $this->session->data['sw__setting'] : array();
		$spin_wheel = isset($setting['spin_wheel']) ? $setting['spin_wheel'] : array(); 
		
		if(isset($this->request->post['firstname'])){
			$firstname =  $this->request->post['firstname'];
		}else{
			$firstname =  '';
		}
		
		if(isset($this->request->post['lastname'])){
			$lastname =  $this->request->post['lastname'];
		}else{
			$lastname =  '';
		}

		if(isset($this->request->post['email'])){
			$email =  $this->request->post['email'];
		}else{
			$email =  '';
		}

		if(isset($this->request->post['code'])){
			$code =  $this->request->post['code'];
		}else{
			$code =  '';
		}		
		
		if(isset($this->request->post['offer_no'])){
			$offer_no =  $this->request->post['offer_no'];
		}else{
			$offer_no =  '';
		}		
		
		if($offer_no){
			$this->load->model('extension/module/spin_wheel');
			
			$offer_info = $this->model_extension_module_spin_wheel->getWheelOffer($offer_no);
			

			$type = $offer_info['type'];
			
			if($type == 3){
				$discount = $offer_info['label'];
			}elseif($type == 2){
				$discount = (int)$offer_info['discount'] . '%';				
			}else{
				$discount = (VERSION <= '2.2.0.0') ?  $this->currency->format($offer_info['discount']) : $this->currency->format($offer_info['discount'], $this->session->data['currency']);	
			}
			
			$total = $offer_info['total'];
			
			if (VERSION < '2.0.2.0' || VERSION =='2.0.3.0') {
				$mailToUser = new Mail($this->config->get('config_mail'));
			} else {
				$mailToUser = new Mail();
				$mailToUser->protocol = $this->config->get('config_mail_protocol');
				$mailToUser->parameter = $this->config->get('config_mail_parameter');
				$mailToUser->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mailToUser->smtp_username = $this->config->get('config_mail_smtp_username');
				$mailToUser->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mailToUser->smtp_port = $this->config->get('config_mail_smtp_port');
				$mailToUser->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			}
			
			$subject = $spin_wheel[$this->config->get('config_language_id')]['email_subject'];
			
			$email_content = $spin_wheel[$this->config->get('config_language_id')]['email_content'];
			$find = array('{firstname}', '{lastname}', '{discount}', '{discount_code}', '{total}');
			
			$replace = array(
			'firstname' => $firstname, 
			'lastname' => $lastname, 
			'discount' => $discount, 
			'discount_code' => $code,
			'total' => $total
			);
			
			$email_message = str_replace($find, $replace, $email_content);
			
			$mailToUser->setTo($email);
			$mailToUser->setFrom($this->config->get('config_email'));
			$mailToUser->setSender($this->config->get('config_name'));
			$mailToUser->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mailToUser->setHtml(html_entity_decode($email_message, ENT_QUOTES, 'UTF-8'));
			$mailToUser->send(); 
			
			if ($mailToUser) 
			return true;
				else
			return false;		
		}
	}
	
	private function pre($arg){
		echo "<pre>";
		print_r($arg);
		echo "</pre>";
	}
}