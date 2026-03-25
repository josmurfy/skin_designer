<?php
class ControllerExtensionShippingCanadaPostRate extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/canada_post_rate');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('canada_post_rate', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_extension'] = $this->language->get('text_extension');
		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_unselect_all'] = $this->language->get('text_unselect_all');

		$data['entry_origin_postcode'] = $this->language->get('entry_origin_postcode');
		$data['entry_username'] = $this->language->get('entry_username');
		$data['entry_password'] = $this->language->get('entry_password');
		$data['entry_customer_number'] = $this->language->get('entry_customer_number');
		$data['entry_cost'] = $this->language->get('entry_cost');
		$data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_services'] = $this->language->get('entry_services');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

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
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/shipping/canada_post_rate', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/shipping/canada_post_rate', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true);

		if (isset($this->request->post['canada_post_rate_username'])) {
			$data['canada_post_rate_username'] = $this->request->post['canada_post_rate_username'];
		} else {
			$data['canada_post_rate_username'] = $this->config->get('canada_post_rate_username');
		}
		
		if (isset($this->request->post['canada_post_rate_password'])) {
			$data['canada_post_rate_password'] = $this->request->post['canada_post_rate_password'];
		} else {
			$data['canada_post_rate_password'] = $this->config->get('canada_post_rate_password');
		}
		
		if (isset($this->request->post['canada_post_rate_customer_number'])) {
			$data['canada_post_rate_customer_number'] = $this->request->post['canada_post_rate_customer_number'];
		} else {
			$data['canada_post_rate_customer_number'] = $this->config->get('canada_post_rate_customer_number');
		}
		
		if (isset($this->request->post['canada_post_rate_origin_postcode'])) {
			$data['canada_post_rate_origin_postcode'] = $this->request->post['canada_post_rate_origin_postcode'];
		} else {
			$data['canada_post_rate_origin_postcode'] = $this->config->get('canada_post_rate_origin_postcode');
		}
		
		$array_service[] = array('value'=>'DOM.RP', 'label'=>'CA - Regular');
		$array_service[] = array('value'=>'DOM.EP', 'label'=>'CA - Expedited');	
		$array_service[] = array('value'=>'DOM.XP', 'label'=>'CA - Xpresspost');
		$array_service[] = array('value'=>'DOM.PC', 'label'=>'CA - Priority Courier');
		
		$array_service[] = array('value'=>'USA.TP', 'label'=>'US - Tracked Packet');
		$array_service[] = array('value'=>'USA.SP.AIR', 'label'=>'US - Small Packets Air');
		$array_service[] = array('value'=>'USA.EP', 'label'=>'US - Expedited US Business');
		$array_service[] = array('value'=>'USA.XP', 'label'=>'US - Xpresspost USA');
		$array_service[] = array('value'=>'USA.PW.PARCEL', 'label'=>'US - Priority Worldwide USA');
		
		$array_service[] = array('value'=>'INT.TP', 'label'=>'INT - Tracked Packet');
		$array_service[] = array('value'=>'INT.SP.SURF', 'label'=>'INT - Small Packets Surface');
		$array_service[] = array('value'=>'INT.IP.SURF', 'label'=>'INT - Parcel Surface');
		$array_service[] = array('value'=>'INT.SP.AIR', 'label'=>'INT - Small Packets Air');
		$array_service[] = array('value'=>'INT.XP', 'label'=>'INT - XPressPost International');
		$array_service[] = array('value'=>'INT.PW.PARCEL', 'label'=>'INT - Priority Worldwide INTL');
		
		$data['canada_post_rate_allowed_services'] = $array_service;
		
		if (isset($this->request->post['canada_post_rate_services'])) {
			$data['canada_post_rate_services'] = $this->request->post['canada_post_rate_services'];
		} elseif ($this->config->has('canada_post_rate_services')) {
			$data['canada_post_rate_services'] = $this->config->get('canada_post_rate_services');
		} else {
			$data['canada_post_rate_services'] = array();	
		}

		if (isset($this->request->post['canada_post_rate_tax_class_id'])) {
			$data['canada_post_rate_tax_class_id'] = $this->request->post['canada_post_rate_tax_class_id'];
		} else {
			$data['canada_post_rate_tax_class_id'] = $this->config->get('canada_post_rate_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		/*if (isset($this->request->post['canada_post_rate_geo_zone_id'])) {
			$data['canada_post_rate_geo_zone_id'] = $this->request->post['canada_post_rate_geo_zone_id'];
		} else {
			$data['canada_post_rate_geo_zone_id'] = $this->config->get('canada_post_rate_geo_zone_id');
		} 

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones(); */

		if (isset($this->request->post['canada_post_rate_status'])) {
			$data['canada_post_rate_status'] = $this->request->post['canada_post_rate_status'];
		} else {
			$data['canada_post_rate_status'] = $this->config->get('canada_post_rate_status');
		}

		if (isset($this->request->post['canada_post_rate_sort_order'])) {
			$data['canada_post_rate_sort_order'] = $this->request->post['canada_post_rate_sort_order'];
		} else {
			$data['canada_post_rate_sort_order'] = $this->config->get('canada_post_rate_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/canada_post_rate', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/canada_post_rate')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}