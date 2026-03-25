<?php
class ControllerExtensionShippingZones extends Controller {
	private $error = array(); 
	
	public function index() {   
		
		if($_POST)
		{
			$this->load->model('localisation/zones');
			$data=$_POST;
			$result=$this->model_localisation_zones->saveGeoAllZonesPrice($data);
		}
		$this->load->language('extension/shipping/zones');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('zones', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		
		$data['entry_total'] = $this->language->get('entry_total');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_shipping'),
			'href'      => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/shipping/zones', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data['action'] = $this->url->link('extension/shipping/zones', 'token=' . $this->session->data['token'], 'SSL');
		
		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');
	
						
		
		if (isset($this->request->post['zones_status'])) {
			$data['zones_status'] = $this->request->post['zones_status'];
		} else {
			$data['zones_status'] = $this->config->get('zones_status');
		}
		$this->load->model('localisation/geo_zone');
		$this->load->model('localisation/zones');
		
		$data['geo_zones'] = $this->model_localisation_zones->getGeoAllZones();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/shipping/zones.tpl', $data));
								
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/zones')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>