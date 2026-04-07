<?php
/***********************
// @category  : OpenCart
// @module    : Spin Wheel Popup
// @author    : OpencartMarketplace <support@opencartmarketplace.com>
***********************/

class ControllerExtensionModuleSpinWheel extends Controller {
	private $error = array();
    private $name;
    private $author;
    private $version;
    private $email;

	public function index() {
		$this->name = 'spin_wheel';
		$this->author = 'OpencartMarketplace';
		$this->version = '1.0.0';
		
		$this->load->language('extension/module/spin_wheel');

		$this->document->setTitle($this->language->get('heading_title2'));

		$this->load->model('setting/setting');
		$this->load->model('extension/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$this->model_setting_setting->editSetting($this->name, $this->request->post);
			
			if (!isset($this->request->get['module_id'])) {
				$this->model_extension_module->addModule($this->name, $this->request->post);
			} else {
				$this->model_extension_module->editModule($this->request->get['module_id'], $this->request->post);
			}
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			if(isset($this->request->post['save_stay']) && $this->request->post['save_stay'] == 1){
				if(isset($this->request->get['module_id'])){
					$this->response->redirect($this->url->link('extension/module/spin_wheel', 'user_token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'] . '&type=module', true));
				}
			}else{

				$this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['token'] . '&type=module', true));
			}	
		}


		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		$data['module_id'] = (isset($this->request->get['module_id']) && $this->request->get['module_id']) ? $this->request->get['module_id'] : '';
		
		
		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_extension_module->getModule($this->request->get['module_id']);
		}
		
		
		$this->document->addScript('view/javascript/jquery/ocmp_spin_wheel/js/spectrum.js');
		$this->document->addScript('view/javascript/jquery/ocmp_spin_wheel/js/script.min.js');
		$this->document->addScript('view/javascript/jquery/ocmp_spin_wheel/js/datatables.min.js');
		$this->document->addStyle('view/javascript/jquery/ocmp_spin_wheel/css/spectrum.css');
		$this->document->addStyle('view/javascript/jquery/ocmp_spin_wheel/css/datatables.min.css');
		$this->document->addStyle('view/javascript/jquery/ocmp_spin_wheel/css/style.min.css');
		
		
		$data['heading_title'] = $this->language->get('heading_title2');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_only_wheel'] = $this->language->get('text_only_wheel');
		$data['text_email_only'] = $this->language->get('text_email_only');
		$data['text_wheel_email'] = $this->language->get('text_wheel_email');

		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_close'] = $this->language->get('entry_close');
		$data['entry_email_recheck'] = $this->language->get('entry_email_recheck');
		$data['entry_sound'] = $this->language->get('entry_sound');
		$data['entry_display_itrvl'] = $this->language->get('entry_display_itrvl');		
		$data['entry_left_icon'] = $this->language->get('entry_left_icon');
		$data['entry_firework'] = $this->language->get('entry_firework');
		$data['entry_css'] = $this->language->get('entry_css');
		$data['entry_js'] = $this->language->get('entry_js');	
		$data['entry_mx_dis_freq'] = $this->language->get('entry_mx_dis_freq');	
		$data['entry_hide_after'] = $this->language->get('entry_hide_after');	
		$data['entry_fix_time'] = $this->language->get('entry_fix_time');	
		$data['entry_active_date'] = $this->language->get('entry_active_date');	
		$data['entry_expire_date'] = $this->language->get('entry_expire_date');	
		$data['entry_display'] = $this->language->get('entry_display');	
		$data['entry_theme'] = $this->language->get('entry_theme');	
		$data['entry_theme_pview'] = $this->language->get('entry_theme_pview');	
		$data['entry_wheel_pview'] = $this->language->get('entry_wheel_pview');	
		$data['entry_wheel_color'] = $this->language->get('entry_wheel_color');	
		$data['entry_wheel_font_color'] = $this->language->get('entry_wheel_font_color');	
		$data['entry_background_color'] = $this->language->get('entry_background_color');	
		$data['entry_text_color'] = $this->language->get('entry_text_color');	
		$data['entry_btn_bg_color'] = $this->language->get('entry_btn_bg_color');	
		$data['entry_btn_text_color'] = $this->language->get('entry_btn_text_color');	
		$data['entry_noluck_color'] = $this->language->get('entry_noluck_color');	
		$data['entry_title'] = $this->language->get('entry_title');	
		$data['entry_sub_title'] = $this->language->get('entry_sub_title');	
		$data['entry_note'] = $this->language->get('entry_note');	
		$data['entry_btn_label'] = $this->language->get('entry_btn_label');	
		$data['entry_text_no_luck'] = $this->language->get('entry_text_no_luck');	
		$data['entry_btn_text_color'] = $this->language->get('entry_btn_text_color');	
		$data['entry_display_coupon'] = $this->language->get('entry_display_coupon');			
		$data['entry_email_subject'] = $this->language->get('entry_email_subject');	
		$data['entry_email_content'] = $this->language->get('entry_email_content');	
		 
		
		$data['help_close'] = $this->language->get('help_close');	
		$data['help_email_recheck'] = $this->language->get('help_email_recheck');	
		$data['help_sound'] = $this->language->get('help_sound');	
		$data['help_dis_intervel'] = $this->language->get('help_dis_intervel');	
		$data['help_firework'] = $this->language->get('help_firework');	
		$data['help_css'] = $this->language->get('help_css');	
		$data['help_js'] = $this->language->get('help_js');	
		$data['help_hide_after'] = $this->language->get('help_hide_after');	
		$data['help_fix_time'] = $this->language->get('help_fix_time');	
		$data['help_active_date'] = $this->language->get('help_active_date');	
		$data['help_expire_date'] = $this->language->get('help_expire_date');	
		$data['help_display'] = $this->language->get('help_display');	
		$data['help_close'] = $this->language->get('help_close');	
		$data['help_close'] = $this->language->get('help_close');	
		
		
		$data['button_save_stay'] = $this->language->get('button_save_stay');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['prefix'] = $this->name;
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}		

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title2'),
			'href' => $this->url->link('extension/module/spin_wheel', 'user_token=' . $this->session->data['token'], true)
		);
		
		if(isset($this->request->get['module_id']) && $this->request->get['module_id']){
			$data['action'] = $this->url->link('extension/module/spin_wheel', 'user_token=' . $this->session->data['token'] . '&module_id=' . $this->request->get['module_id'], true);				
		}else{
			$data['action'] = $this->url->link('extension/module/spin_wheel', 'user_token=' . $this->session->data['token'], true);				
		}
		
		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['token'] . '&type=module', true);
			
		$data['token'] = $this->session->data['token'];
	
		$this->load->model('tool/image');
		$this->load->model('localisation/language');
		
		$languages =  $this->model_localisation_language->getLanguages();

		$data['languages'] = array();
		
		foreach($languages as $language){
			  if (version_compare(VERSION, '2.2', '>=')) {
				$tpl_lng = 'language/'.$language['code'].'/'.$language['code'].'.png';
			  } else {
				$tpl_lng = 'view/image/flags/'. $language['image'];
			  }
			  
			$data['languages'][] = array(
				'language_id' => $language['language_id'],
				'name' => $language['name'],
				'code' => $language['code'],
				'language_id' => $language['language_id'],
				'image' => $tpl_lng
			);
			
		}		
		
		
		//Stores
		$this->load->model('setting/store');
		$data['stores'] = array();
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$action = array();

			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}	
		
		//Customer Group
		
		if (version_compare(VERSION, '2.1', '>=')) {
		  $this->load->model('customer/customer_group');
		  $data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();

		} else {
		  $this->load->model('sale/customer_group');
		  $data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		}


		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}	
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {	
			$data['status'] = 0;
		}
		
		//$this->pre($module_info);
		$fileds_name = array();
		
		if (isset($this->request->post[$this->name])) {
			$data[$this->name] = $this->request->post[$this->name];
		} elseif (!empty($module_info)) {
			$data[$this->name] = $this->config->get($this->name);
		} else{
			$data[$this->name] = 0;			
		}

		/*echo "<pre>";
			print_r($data[$this->name]['stores']);
		echo "</pre>"; */

		if(!isset($this->request->get['module_id'])){
			$data['spin_wheel'] = $this->getDefaultValues();
		}		

		
		if (isset($this->request->post[$this->name]['bg_image']) && $this->request->post[$this->name]['bg_image']!= '') {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post[$this->name]['bg_image'], 100, 100);
		} elseif(isset($data[$this->name]['bg_image']) && $data[$this->name]['bg_image']!= '') {
			$data['thumb'] = $this->model_tool_image->resize($this->config->get($this->name)['bg_image'], 100, 100);
		}else{
			$data['thumb'] = $this->model_tool_image->resize('placeholder.png', 100, 100);
		}	

		
		$data['bg_url'] = HTTP_CATALOG .'image/';
					

		
		$data['max_display_frq'] = array(1 => 'Every Visit', 2 => 'One Visit per hour', 3 => 'One visit per day', 4 => 'One visit per week', 5 => 'One visit per month');
		$data['hide_after'] = array(0 => 'Always display', 1 => '10 Seconds', 2 => '20 Seconds', 3 => '30 Seconds', 6 => '60 Seconds');	
		$data['when_to_display'] = array(1 => 'Immediately', 2 => 'After Time(seconds)', 3 => 'When scroll down(%)');			
		$data['themes'] = array(0 => 'Classic', 'green' => 'Green', 'pink' => 'Pink', 'yellow' => 'Yellow', 'dark _blue' => 'Dark Blue', 'orange' => 'Orange', 'light_gray' => 'Light Gray', 'light_yellow' => 'Light Yellow', 'red' => 'Red');
		$data['display_coupon'] = array(1 => 'Only on wheel', 2 => 'Email Only', 3 => 'Email & Wheel');
		
		$this->load->model('extension/module/spin_wheel');
		
		$results = $this->model_extension_module_spin_wheel->getWheelCopuons();
		
		foreach($results as $result){
			if($result['type'] == 1) {
				$type = 'Fixed';
			}elseif($result['type'] == 2){ 
				$type = 'Percentage';
			}else{
				$type = 'Free Shipping';
			}
			
			$data['WheelCoupons'][] = array(
				'label' => $result['label'],
				'offer_id' => $result['offer_id'],
				'type' => $type,
				'discount' => $result['discount'],
				'gravity' => $result['gravity'],
				'href' => $this->url->link('extension/module/spin_wheel/edit', 'offer_id=' . $result['offer_id'] .'&token=' . $this->session->data['token'], true),
			);
		}
		
		$formDetails = $this->model_extension_module_spin_wheel->getWheelForms();	
		$data['statistics'] = array();
		$this->load->model('marketing/coupon');
		foreach($formDetails as $formDetail){
			if($formDetail['coupon_id']){
				$coupon_info = $this->model_marketing_coupon->getCoupon($formDetail['coupon_id']);
				$data['statistics'][] = array(
					'spin_form_id' => $formDetail['spin_form_id'],
					'code' => (isset($coupon_info['code'])) ? $coupon_info['code'] : '',
					'customer_id' => $formDetail['customer_id'],
					'firstname' => $formDetail['firstname'],
					'lastname' => $formDetail['lastname'],
					'email' => $formDetail['email'],
					'country' => $formDetail['country'],
					'ip' => $formDetail['ip'],
					'date_added' => $formDetail['date_added'],
				);
			}	
		}		

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/module/spin_wheel', $data));
	}
	
	public function edit(){
		
		$this->load->language('extension/module/spin_wheel');
		$this->load->model('extension/module/spin_wheel');

		$data['entry_label'] = $this->language->get('entry_label');		
		$data['entry_type'] = $this->language->get('entry_type');		
		$data['entry_discount'] = $this->language->get('entry_discount');		
		$data['entry_total'] = $this->language->get('entry_total');		
		$data['entry_gravity'] = $this->language->get('entry_gravity');		

		$data['text_fixed'] = $this->language->get('text_fixed');		
		$data['text_precentage'] = $this->language->get('text_precentage');		
		$data['text_shipping'] = $this->language->get('text_shipping');		
				
		if(isset($this->request->get['offer_id'])){
			$offer_id  = $this->request->get['offer_id'];
		}else{
			$offer_id  = 0;
		}
		
		$discount_info = $this->model_extension_module_spin_wheel->getWheelCopuon($offer_id);
		
		if($discount_info){
			
			$data['offer_id'] = $discount_info['offer_id'];
			$data['label'] = $discount_info['label'];
			$data['type'] = $discount_info['type'];
			$data['discount'] = $discount_info['discount'];		
			$data['total'] = $discount_info['total'];		
			$data['gravity'] = $discount_info['gravity'];		
		}
		
		$this->response->setOutput($this->load->view('extension/module/spin_wheel_discount', $data));
	}
	
	public function save(){
		$json = array();
		$this->load->language('extension/module/spin_wheel');
		$this->load->model('extension/module/spin_wheel');
		
		if(isset($this->request->post['offer_id'])){
			$offer_id = $this->request->post['offer_id'];		
		}else{
			$offer_id = 0;					
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDiscount()) {
			$this->model_extension_module_spin_wheel->editWheelCopuon($offer_id, $this->request->post);
			
			$json['success'] = $this->language->get('text_dc_success');
		}else{
			$json['error'] = $this->error;
		}	
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	protected function validateDiscount(){
		if (!$this->user->hasPermission('modify', 'extension/module/spin_wheel')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((utf8_strlen($this->request->post['label']) < 3) || (utf8_strlen($this->request->post['label']) > 32)){
			$this->error['label'] = $this->language->get('error_label');
		}


		if ((utf8_strlen($this->request->post['discount']) < 1)){
			$this->error['discount'] = $this->language->get('error_discount');
		}

		return !$this->error;		
	}
	
	private function getDefaultValues(){
		$language_id = $this->config->get('config_language_id');
		
		$spin_wheel_arr =  array(
			'close' => 1,'display_interval' => 0, 'email_recheck' => 1, 'sound' => 1, 'firework' => 1, 'css' => '', 'js' => '', 'hide_after' => 0, 'fix_time' => 0, 'active_date' => '', 'expire_date' => '',
			'when_to_display' => 1, 'spin_popup_time' => 5, 'spin_scroll_time' => '', 'display' => 'full', 'bg_type' => 2, 'bg_image' => '', 'wheel_color' => '#ff0000', 'wheel_font_color' => '#ffffff', 'background_color' => '#ff2b2b', 'text_color' => '#ffffff', 'button_bg_color' => '#690000', 'btn_text_color' => '#ffffff', 'text_noluck_color' => '#e5ff00', 'display_coupon' => 3, $language_id => array(
			'title' => 'Big Discount Offer', 'sub_title' => 'You have a chance to win a nice big discount. Are you ready?', 'note' => "You can spin the wheel only once. If you win,\nYou can claim your coupon for 1 day only!", 'btn_label' => 'PLEASE TRY YOUR LUCK', 'text_no_luck' => 'No, I do not feel lucky X', 'email_subject' => 'Congratulation! Redeem Your Coupon on First Purchase', 'email_content' => '<div style="background:#ffffff;width:700px;border:1px solid #dedede;">
			   <div style="text-align: center; background:#27ab2c;padding:15px;">
				 <img src="../image/catalog/spin_wheel/gift-icon.svg" style="width: 144.094px; height: 144.094px;">
				 <span style="display:block;margin:5px 0;color:#ffffff;font-size:18px;">You have won {discount}, you use coupon to checkout.</span>
			   </div>
			   <div style="padding:25px;">
					 <b>Hi {firstname}</b><br>
					<p>Conrgratulation, Thank you for registration with us, You can use below coupon code for next purchase!</p><br><br>
					<div style="text-align:center; border:1px solid #dedede;padding:25px;">
						<b>Coupon Code: </b><br>
						<div style="font-size:23px;">{discount_code}</div>
					</div><br><br>
				   <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas porttitor accumsan orci in vestibulum. Phasellus a mauris velit. Ut eget sapien non nisi venenatis mollis. Suspendisse sit amet fringilla eros, ac tincidunt risus.</p><br><br><br>
			   <p><b>
			Kind Regards</b>,<br><b>
			YourStore</b><br><b>
			http://www.example.com</b><br>
			</p>
			   </div>
			</div>'				
			));		
		return $spin_wheel_arr;
	}

	private function pre($arg){
		echo "<pre>";
		print_r($arg);
		echo "</pre>";
	}
	
	public function install(){
		$this->load->model('extension/module/spin_wheel');
		$this->model_extension_module_spin_wheel->install();
	}

	public function uninstall(){
		$this->load->model('extension/module/spin_wheel');
		$this->model_extension_module_spin_wheel->uninstall();		
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/spin_wheel')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		/*if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}*/
		
		return !$this->error;
	}
}