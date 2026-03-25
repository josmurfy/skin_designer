<?php 
/*  This module was written by Kagonesti for Virtual Merchant Processing for Elavon for opencart
 *  This is not free software - Once purchased, you may use it on any website you own (up to five websites, unless you contact me first for approval)
 *  email:  pdressler@telus.net
 *  Thank you for your purchase.  Please do not distribute
 *  For Opencart 2.2 March 2016
 */
class ControllerExtensionPaymentElavon extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('extension/payment/elavon');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('elavon', $this->request->post);				

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_test'] = $this->language->get('text_test');
		$data['text_live'] = $this->language->get('text_live');
		$data['text_ccsale'] = $this->language->get('text_ccsale');
		$data['text_ccauthonly'] = $this->language->get('text_ccauthonly');
		$data['text_true'] = $this->language->get('text_true');
		$data['text_false'] = $this->language->get('text_false');
				

		$data['entry_vmid'] = $this->language->get('entry_vmid');
		$data['entry_userid'] = $this->language->get('entry_userid');
		$data['entry_pin'] = $this->language->get('entry_pin');
		$data['entry_cardsaccepted'] = $this->language->get('entry_cardsaccepted');
		$data['entry_refererurl'] = $this->language->get('entry_refererurl');
		
	       	$data['entry_store_number'] = $this->language->get('entry_store_number');		
		$data['entry_compatability'] = $this->language->get('entry_compatability');		
		$data['entry_mode'] = $this->language->get('entry_mode');
		$data['entry_method'] = $this->language->get('entry_method');
		$data['entry_total'] = $this->language->get('entry_total');	
		$data['entry_order_status'] = $this->language->get('entry_order_status');		
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$data['help_total'] = $this->language->get('help_total');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}


$data['entry_vmid'] = $this->language->get('entry_vmid');
		$data['entry_userid'] = $this->language->get('entry_userid');
		$data['entry_pin'] = $this->language->get('entry_pin');
		$data['entry_cardsaccepted'] = $this->language->get('entry_cardsaccepted');
               	$data['entry_refererurl'] = $this->language->get('entry_refererurl');	
		
		if (isset($this->error['vmid'])) {
			$data['error_vmid'] = $this->error['vmid'];
		} else {
			$data['error_vmid'] = '';
		}

		if (isset($this->error['userid'])) {
			$data['error_userid'] = $this->error['userid'];
		} else {
			$data['error_userid'] = '';
		}
		
		if (isset($this->error['pin'])) {
			$data['error_pin'] = $this->error['pin'];
		} else {
			$data['error_pin'] = '';
		}

		if (isset($this->error['cardsaccepted'])) {
			$data['error_cardsaccepted'] = $this->error['cardsaccepted'];
		} else {
			$data['error_cardsaccepted'] = '';
		}
		if (isset($this->error['refererurl'])) {
			$data['error_refererurl'] = $this->error['refererurl'];
		} else {
			$data['error_refererurl'] = '';
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
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/elavon', 'token=' . $this->session->data['token'], true)
		);
		
		$data['action'] = $this->url->link('extension/payment/elavon', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=payment', true);

		if (isset($this->request->post['elavon_vmid'])) {
			$data['elavon_vmid'] = $this->request->post['elavon_vmid'];
		} else {
			$data['elavon_vmid'] = $this->config->get('elavon_vmid');
		}

		if (isset($this->request->post['elavon_userid'])) {
			$data['elavon_userid'] = $this->request->post['elavon_userid'];
		} else {
			$data['elavon_userid'] = $this->config->get('elavon_userid');
		}

		if (isset($this->request->post['elavon_pin'])) {
			$data['elavon_pin'] = $this->request->post['elavon_pin'];
		} else {
			$data['elavon_pin'] = $this->config->get('elavon_pin');
		}

		if (isset($this->request->post['elavon_cardsaccepted'])) {
			$data['elavon_cardsaccepted'] = $this->request->post['elavon_cardsaccepted'];
		} else {
			$data['elavon_cardsaccepted'] = $this->config->get('elavon_cardsaccepted');
		}
		if (isset($this->request->post['elavon_refererurl'])) {
			$data['elavon_refererurl'] = $this->request->post['elavon_refererurl'];
		} else {
			$data['elavon_refererurl'] = $this->config->get('elavon_refererurl');
		}

		if (isset($this->request->post['elavon_mode'])) {
			$data['elavon_mode'] = $this->request->post['elavon_mode'];
		} else {
			$data['elavon_mode'] = $this->config->get('elavon_mode');
		}

		if (isset($this->request->post['elavon_method'])) {
			$data['elavon_method'] = $this->request->post['elavon_method'];
		} else {
			$data['elavon_method'] = $this->config->get('elavon_method');
		}
		
		if (isset($this->request->post['elavon_store_number'])) {
			$data['elavon_store_number'] = $this->request->post['elavon_store_number'];
		} else {
			$data['elavon_store_number'] = $this->config->get('elavon_store_number'); 
		} 
		
		if (isset($this->request->post['elavon_compatability'])) {
			$data['elavon_compatability'] = $this->request->post['elavon_compatability'];
		} else {
			$data['elavon_compatability'] = $this->config->get('elavon_compatability'); 
		} 

		if (isset($this->request->post['elavon_total'])) {
			$data['elavon_total'] = $this->request->post['elavon_total'];
		} else {
			$data['elavon_total'] = $this->config->get('elavon_total'); 
		} 

		if (isset($this->request->post['elavon_order_status_id'])) {
			$data['elavon_order_status_id'] = $this->request->post['elavon_order_status_id'];
		} else {
			$data['elavon_order_status_id'] = $this->config->get('elavon_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['elavon_geo_zone_id'])) {
			$data['elavon_geo_zone_id'] = $this->request->post['elavon_geo_zone_id'];
		} else {
			$data['elavon_geo_zone_id'] = $this->config->get('elavon_geo_zone_id'); 
		} 

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['elavon_status'])) {
			$data['elavon_status'] = $this->request->post['elavon_status'];
		} else {
			$data['elavon_status'] = $this->config->get('elavon_status');
		}

		if (isset($this->request->post['elavon_sort_order'])) {
			$data['elavon_sort_order'] = $this->request->post['elavon_sort_order'];
		} else {
			$data['elavon_sort_order'] = $this->config->get('elavon_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/elavon', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/elavon')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['elavon_vmid']) {
			$this->error['vmid'] = $this->language->get('error_vmid');
		}

		if (!$this->request->post['elavon_userid']) {
			$this->error['userid'] = $this->language->get('error_userid');
		}
		if (!$this->request->post['elavon_pin']) {
			$this->error['pin'] = $this->language->get('error_pin');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}