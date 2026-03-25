<?php
class ControllerExtensionModulegooglelookup extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/google_lookup');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('google_lookup', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_apikey'] = $this->language->get('entry_apikey');
		$data['entry_showmap'] = $this->language->get('entry_showmap');
		$data['entry_default'] = $this->language->get('entry_default');
		$data['entry_lat'] = $this->language->get('entry_lat');
		$data['entry_lng'] = $this->language->get('entry_lng');
		$data['entry_zoom'] = $this->language->get('entry_zoom');
		$data['entry_showcregister'] = $this->language->get('entry_showcregister');
		$data['entry_showcguest'] = $this->language->get('entry_showcguest');
		$data['entry_showeditaddress'] = $this->language->get('entry_showeditaddress');
		$data['entry_showpayment_add'] = $this->language->get('entry_showpayment_add');
		$data['entry_showshipping_add'] = $this->language->get('entry_showshipping_add');
		$data['entry_showaregister'] = $this->language->get('entry_showaregister');
		$data['entry_showaffiliateregister'] = $this->language->get('entry_showaffiliateregister');
		$data['text_success'] = $this->language->get('text_success');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_extension'] = $this->language->get('text_extension');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/google_lookup', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/module/google_lookup', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->post['google_lookup_status'])) {
			$data['google_lookup_status'] = $this->request->post['google_lookup_status'];
		} else {
			$data['google_lookup_status'] = $this->config->get('google_lookup_status');
		}
		
		if (isset($this->request->post['google_lookup_apikey'])) {
			$data['google_lookup_apikey'] = $this->request->post['google_lookup_apikey'];
		} else {
			$data['google_lookup_apikey'] = $this->config->get('google_lookup_apikey');
		}
		
		if (isset($this->request->post['google_lookup_map'])) {
			$data['google_lookup_map'] = $this->request->post['google_lookup_map'];
		} else {
			$data['google_lookup_map'] = $this->config->get('google_lookup_map');
		}
		
		if (isset($this->request->post['google_lookup_showcregister'])) {
			$data['google_lookup_showcregister'] = $this->request->post['google_lookup_showcregister'];
		} else {
			$data['google_lookup_showcregister'] = $this->config->get('google_lookup_showcregister');
		}
		
		if (isset($this->request->post['google_lookup_showaffiliateregister'])) {
			$data['google_lookup_showaffiliateregister'] = $this->request->post['google_lookup_showaffiliateregister'];
		} else {
			$data['google_lookup_showaffiliateregister'] = $this->config->get('google_lookup_showaffiliateregister');
		}
		
		if (isset($this->request->post['google_lookup_showcguest'])) {
			$data['google_lookup_showcguest'] = $this->request->post['google_lookup_showcguest'];
		} else {
			$data['google_lookup_showcguest'] = $this->config->get('google_lookup_showcguest');
		}
		
		if (isset($this->request->post['google_lookup_showaregister'])) {
			$data['google_lookup_showaregister'] = $this->request->post['google_lookup_showaregister'];
		} else {
			$data['google_lookup_showaregister'] = $this->config->get('google_lookup_showaregister');
		}
		
		if (isset($this->request->post['google_lookup_editaddress'])) {
			$data['google_lookup_editaddress'] = $this->request->post['google_lookup_editaddress'];
		} else {
			$data['google_lookup_editaddress'] = $this->config->get('google_lookup_editaddress');
		}
		
		if (isset($this->request->post['google_lookup_shipping_add'])) {
			$data['google_lookup_shipping_add'] = $this->request->post['google_lookup_shipping_add'];
		} else {
			$data['google_lookup_shipping_add'] = $this->config->get('google_lookup_shipping_add');
		}
		
		if (isset($this->request->post['google_lookup_payment_add'])) {
			$data['google_lookup_payment_add'] = $this->request->post['google_lookup_payment_add'];
		} else {
			$data['google_lookup_payment_add'] = $this->config->get('google_lookup_payment_add');
		}
		
		if (isset($this->request->post['google_lookup_lat'])) {
			$data['google_lookup_lat'] = $this->request->post['google_lookup_lat'];
		} else {
			$data['google_lookup_lat'] = $this->config->get('google_lookup_lat');
		}
		
		if (isset($this->request->post['google_lookup_lng'])) {
			$data['google_lookup_lng'] = $this->request->post['google_lookup_lng'];
		} else {
			$data['google_lookup_lng'] = $this->config->get('google_lookup_lng');
		}
		
		if (isset($this->request->post['google_lookup_zoom'])) {
			$data['google_lookup_zoom'] = $this->request->post['google_lookup_zoom'];
		} else {
			$data['google_lookup_zoom'] = $this->config->get('google_lookup_zoom');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/google_lookup', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/google_lookup')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}