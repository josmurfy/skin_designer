<?php 
class ControllerExtensionPaymentVirtualMerchant extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('extension/payment/virtualmerchant');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('virtualmerchant', $this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_extension'] = $this->language->get('text_extension');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_test'] = $this->language->get('text_test');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_authorization'] = $this->language->get('text_authorization');
		$data['text_capture'] = $this->language->get('text_capture');		
		$data['text_account'] = $this->language->get('text_account');		
		$data['text_settings'] = $this->language->get('text_settings');		
		$data['text_recurring'] = $this->language->get('text_recurring');		
		$data['text_first_fifteen'] = $this->language->get('text_first_fifteen');		
		$data['text_fifteen_last'] = $this->language->get('text_fifteen_last');		

		$data['entry_login'] = $this->language->get('entry_login');
		$data['entry_user'] = $this->language->get('entry_user');
		$data['entry_pin'] = $this->language->get('entry_pin');
		$data['entry_currency'] = $this->language->get('entry_currency');
		$data['entry_server'] = $this->language->get('entry_server');
		$data['entry_mode'] = $this->language->get('entry_mode');
		$data['entry_method'] = $this->language->get('entry_method');
		$data['entry_debug'] = $this->language->get('entry_debug');
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_order_status'] = $this->language->get('entry_order_status');		
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_bill_on_half'] = $this->language->get('entry_bill_on_half');
		$data['entry_end_of_month'] = $this->language->get('entry_end_of_month');
		$data['entry_skip_cycle'] = $this->language->get('entry_skip_cycle');

		$data['help_user'] = $this->language->get('help_user');
		$data['help_total'] = $this->language->get('help_total');
		$data['help_end_of_month'] = $this->language->get('help_end_of_month');
		$data['help_skip_cycle'] = $this->language->get('help_skip_cycle');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

 		if (isset($this->error['login'])) {
			$data['error_login'] = $this->error['login'];
		} else {
			$data['error_login'] = '';
		}

 		if (isset($this->error['user'])) {
			$data['error_user'] = $this->error['user'];
		} else {
			$data['error_user'] = '';
		}

 		if (isset($this->error['pin'])) {
			$data['error_pin'] = $this->error['pin'];
		} else {
			$data['error_pin'] = '';
		}
		
 		if (isset($this->error['currency'])) {
			$data['error_currency'] = $this->error['currency'];
		} else {
			$data['error_currency'] = '';
		}
		
  		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true)
		);

   		$data['breadcrumbs'][] = array(
       		'text'	=> $this->language->get('heading_title'),
			'href'	=> $this->url->link('extension/payment/virtualmerchant', 'token=' . $this->session->data['token'], true),
   		);
				
		$data['action'] = $this->url->link('extension/payment/virtualmerchant', 'token=' . $this->session->data['token'], true);
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);
				
		if (isset($this->request->post['virtualmerchant_login'])) {
			$data['virtualmerchant_login'] = $this->request->post['virtualmerchant_login'];
		} else {
			$data['virtualmerchant_login'] = $this->config->get('virtualmerchant_login');
		}
	
		if (isset($this->request->post['virtualmerchant_user'])) {
			$data['virtualmerchant_user'] = $this->request->post['virtualmerchant_user'];
		} else {
			$data['virtualmerchant_user'] = $this->config->get('virtualmerchant_user');
		}
				
		if (isset($this->request->post['virtualmerchant_pin'])) {
			$data['virtualmerchant_pin'] = $this->request->post['virtualmerchant_pin'];
		} else {
			$data['virtualmerchant_pin'] = $this->config->get('virtualmerchant_pin');
		}
				
		if (isset($this->request->post['virtualmerchant_currency'])) {
			$data['virtualmerchant_currency'] = $this->request->post['virtualmerchant_currency'];
		} else {
			$data['virtualmerchant_currency'] = $this->config->get('virtualmerchant_currency');
		}
				
		if (isset($this->request->post['virtualmerchant_server'])) {
			$data['virtualmerchant_server'] = $this->request->post['virtualmerchant_server'];
		} else {
			$data['virtualmerchant_server'] = $this->config->get('virtualmerchant_server');
		}
		
		if (isset($this->request->post['virtualmerchant_debug'])) {
			$data['virtualmerchant_debug'] = $this->request->post['virtualmerchant_debug'];
		} else {
			$data['virtualmerchant_debug'] = $this->config->get('virtualmerchant_debug');
		}
		
		if (isset($this->request->post['virtualmerchant_mode'])) {
			$data['virtualmerchant_mode'] = $this->request->post['virtualmerchant_mode'];
		} else {
			$data['virtualmerchant_mode'] = $this->config->get('virtualmerchant_mode');
		}
		
		if (isset($this->request->post['virtualmerchant_method'])) {
			$data['virtualmerchant_method'] = $this->request->post['virtualmerchant_method'];
		} else {
			$data['virtualmerchant_method'] = $this->config->get('virtualmerchant_method');
		}
		
		if (isset($this->request->post['virtualmerchant_bill_on_half'])) {
			$data['virtualmerchant_bill_on_half'] = $this->request->post['virtualmerchant_bill_on_half'];
		} else {
			$data['virtualmerchant_bill_on_half'] = $this->config->get('virtualmerchant_bill_on_half');
		}

		if (isset($this->request->post['virtualmerchant_end_of_month'])) {
			$data['virtualmerchant_end_of_month'] = $this->request->post['virtualmerchant_end_of_month'];
		} else {
			$data['virtualmerchant_end_of_month'] = $this->config->get('virtualmerchant_end_of_month');
		}

		if (isset($this->request->post['virtualmerchant_skip_cycle'])) {
			$data['virtualmerchant_skip_cycle'] = $this->request->post['virtualmerchant_skip_cycle'];
		} else {
			$data['virtualmerchant_skip_cycle'] = $this->config->get('virtualmerchant_skip_cycle');
		}

		if (isset($this->request->post['virtualmerchant_total'])) {
			$data['virtualmerchant_total'] = $this->request->post['virtualmerchant_total'];
		} else {
			$data['virtualmerchant_total'] = $this->config->get('virtualmerchant_total');
		}
		
		if (isset($this->request->post['virtualmerchant_order_status_id'])) {
			$data['virtualmerchant_order_status_id'] = $this->request->post['virtualmerchant_order_status_id'];
		} else {
			$data['virtualmerchant_order_status_id'] = $this->config->get('virtualmerchant_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['virtualmerchant_geo_zone_id'])) {
			$data['virtualmerchant_geo_zone_id'] = $this->request->post['virtualmerchant_geo_zone_id'];
		} else {
			$data['virtualmerchant_geo_zone_id'] = $this->config->get('virtualmerchant_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['virtualmerchant_status'])) {
			$data['virtualmerchant_status'] = $this->request->post['virtualmerchant_status'];
		} else {
			$data['virtualmerchant_status'] = $this->config->get('virtualmerchant_status');
		}
		
		if (isset($this->request->post['virtualmerchant_sort_order'])) {
			$data['virtualmerchant_sort_order'] = $this->request->post['virtualmerchant_sort_order'];
		} else {
			$data['virtualmerchant_sort_order'] = $this->config->get('virtualmerchant_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/virtualmerchant.tpl', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/virtualmerchant')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['virtualmerchant_login']) {
			$this->error['login'] = $this->language->get('error_login');
		}

		if (!$this->request->post['virtualmerchant_user']) {
			$this->error['user'] = $this->language->get('error_user');
		}
		
		if (!$this->request->post['virtualmerchant_pin']) {
			$this->error['pin'] = $this->language->get('error_pin');
		}
		
		if (!$this->request->post['virtualmerchant_currency']) {
			$this->error['currency'] = $this->language->get('error_currency');
		}
		return !$this->error;
	}
	
	public function install() {
		$this->load->model('setting/setting');
		$this->model_setting_setting->editSetting('virtualmerchant', array(
			'virtualmerchant_currency'	=> 'USD',
		));
	}
}