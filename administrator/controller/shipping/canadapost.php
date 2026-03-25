<?php
/* 
 * OpenCart Canada Post Shipping Module
 * Version: 1.6
 * Author: Olivier Labbé
 * Email: olivier.labbe@votreespace.net
 * Web: http://www.votreespace.net
 * Description: Connects with Canada Post sellonline server to provide a
 *              shipping estimate.
*/

class ControllerShippingCanadaPost extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('shipping/canadapost');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('canadapost', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->response->redirect($this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_eng'] = $this->language->get('text_eng');
		$data['text_french'] = $this->language->get('text_french');
    $data['text_none'] = $this->language->get('text_none');
    $data['text_yes'] = $this->language->get('text_yes');
    $data['text_no'] = $this->language->get('text_no');
    $data['text_all_zones'] = $this->language->get('text_all_zones');
		
    $data['entry_sell_online_server'] = $this->language->get('entry_sell_online_server');  
    $data['entry_server'] = $this->language->get('entry_server');
		$data['entry_port'] = $this->language->get('entry_port');
		$data['entry_port_default'] = $this->language->get('entry_port_default');
		$data['entry_language'] = $this->language->get('entry_language');
    $data['entry_postcode'] = $this->language->get('entry_postcode');  
    $data['entry_merchantId'] = $this->language->get('entry_merchantId');
		$data['entry_merchantId_Sample'] = $this->language->get('entry_merchantId_Sample');
    $data['entry_origin'] = $this->language->get('entry_origin');
		$data['entry_handling'] = $this->language->get('entry_handling');
		$data['entry_handling_fee'] = $this->language->get('entry_handling_fee');
    $data['entry_turnAround'] = $this->language->get('entry_turnAround');
		$data['entry_turnAround_time'] = $this->language->get('entry_turnAround_time');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_sort_order_order'] = $this->language->get('entry_sort_order_order');
    $data['entry_originalPackaging'] = $this->language->get('entry_originalPackaging');
    $data['entry_tax_class'] = $this->language->get('entry_tax_class');
    $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_version_status_link'] = $this->language->get('entry_version_status_link');
		$data['entry_version_status_author'] = $this->language->get('entry_version_status_author');
		$data['entry_version_status_company'] = $this->language->get('entry_version_status_company');
		$data['entry_author'] = $this->language->get('entry_author');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['entry_link_sellonline'] = $this->language->get('entry_link_sellonline');
		$data['entry_sellonline'] = $this->language->get('entry_sellonline');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['postcode'])) {
			$data['error_postcode'] = $this->error['postcode'];
		} else {
			$data['error_postcode'] = '';
		}
		
  	$data['breadcrumbs'] = array();
		
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_shipping'),
			'href' => $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL')
		);
		
   	$data['breadcrumbs'][] = array(
       		'href' => $this->url->link('shipping/canadapost', 'token=' . $this->session->data['token'], 'SSL'),
       		'text' => $this->language->get('heading_title'),
     );
		
		$data['action'] = $this->url->link('shipping/canadapost', 'token=' . $this->session->data['token'], 'SSL');

		$data['cancel'] = $this->url->link('extension/shipping', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['canadapost_server'])) {
			$data['canadapost_server'] = $this->request->post['canadapost_server'];
		} else {
			$data['canadapost_server'] = $this->config->get('canadapost_server');
		}
      if (isset($this->request->post['canadapost_port'])) {
			$data['canadapost_port'] = $this->request->post['canadapost_port'];
		} else {
			$data['canadapost_port'] = $this->config->get('canadapost_port');
		}
      if (isset($this->request->post['canadapost_language'])) {
			$data['canadapost_language'] = $this->request->post['canadapost_language'];
		} else {
			$data['canadapost_language'] = $this->config->get('canadapost_language');
		}
      if (isset($this->request->post['canadapost_merchantId'])) {
			$data['canadapost_merchantId'] = $this->request->post['canadapost_merchantId'];
		} else {
			$data['canadapost_merchantId'] = $this->config->get('canadapost_merchantId');
		}
      
      if (isset($this->request->post['canadapost_origin'])) {
			$data['canadapost_origin'] = $this->request->post['canadapost_origin'];
		} else {
			$data['canadapost_origin'] = $this->config->get('canadapost_origin');
		}
		
		if (isset($this->request->post['canadapost_handling'])) {
			$data['canadapost_handling'] = $this->request->post['canadapost_handling'];
		} else {
			$data['canadapost_handling'] = $this->config->get('canadapost_handling');
		}

		if (isset($this->request->post['canadapost_turnAround'])) {
			$data['canadapost_turnAround'] = $this->request->post['canadapost_turnAround'];
		} else {
			$data['canadapost_turnAround'] = $this->config->get('canadapost_turnAround');
		}
      
      if (isset($this->request->post['canadapost_originalPackaging'])) {
			$data['canadapost_originalPackaging'] = $this->request->post['canadapost_originalPackaging'];
		} else {
			$data['canadapost_originalPackaging'] = $this->config->get('canadapost_originalPackaging');
		}

		if (isset($this->request->post['canadapost_status'])) {
			$data['canadapost_status'] = $this->request->post['canadapost_status'];
		} else {
			$data['canadapost_status'] = $this->config->get('canadapost_status');
		}
		
		if (isset($this->request->post['canadapost_sort_order'])) {
			$data['canadapost_sort_order'] = $this->request->post['canadapost_sort_order'];
		} else {
			$data['canadapost_sort_order'] = $this->config->get('canadapost_sort_order');
		}				
      if (isset($this->request->post['canadapost_tax_class_id'])) {
			$data['canadapost_tax_class_id'] = $this->request->post['canadapost_tax_class_id'];
		} else {
			$data['canadapost_tax_class_id'] = $this->config->get('canadapost_tax_class_id');
		}
      if (isset($this->request->post['canadapost_geo_zone_id'])) {
			$data['canadapost_geo_zone_id'] = $this->request->post['canadapost_geo_zone_id'];
		} else {
			$data['canadapost_geo_zone_id'] = $this->config->get('canadapost_geo_zone_id');
		}		

		$this->load->model('localisation/tax_class');
		
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/geo_zone');
		
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('shipping/canadapost.tpl', $data));
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'shipping/canadapost')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
      
         //Set a default handling fee immediately after install
			if($this->request->post['canadapost_handling'] == "") {
			 $this->request->post['canadapost_handling'] = "0.00";
			}
         //Set a default turnAroundTime fee immediately after install
			if($this->request->post['canadapost_turnAround'] == "0") {
			 $this->request->post['canadapost_turnAround'] = "0";
			}
         //Set a default server immediately after install
			if($this->request->post['canadapost_server'] == "") {
			 $this->request->post['canadapost_server'] = "sellonline.canadapost.ca";
			}
         //Set a default port immediately after install
			if($this->request->post['canadapost_port'] == "") {
			 $this->request->post['canadapost_port'] = "30000";
			}
         //Set a default port immediately after install
			if($this->request->post['canadapost_merchantId'] == "") {
			 $this->request->post['canadapost_merchantId'] = "CPC_DEMO_XML";
			}
         //Set a default language immediately after install
		 
			if(!isset($this->request->post['canadapost_language']) || $this->request->post['canadapost_language'] == "") {
			 $this->request->post['canadapost_language'] = "en";
			}
         
		//Only check values when the status is enabled, we don't want to bother then if they are just trying to disable it 
		if ($this->request->post['canadapost_status'] == 1) {
         
         //Make sure merchantId is not blank if enabling this module
			if($this->request->post['canadapost_merchantId'] == "") {
			 $this->error['warning'] = "You must have a Canada Post Sell Online Merchant Id to use this module";
			}
         
			//Validate origin postcode
			if(!preg_match('/[ABCEGHJKLMNPRSTVXYabceghjklmnprstvxy][0-9][A-Za-z] *[0-9][A-Za-z][0-9]/', $this->request->post['canadapost_origin'])){
				$this->error['warning'] = "Postal Code is invalid.  Must be a valid Canadian postal code.";
			}

			//Validate handling cost

			if(!preg_match('/^[0-9]{1,2}(\.[0-9]{1,2})?$/',$this->request->post['canadapost_handling'])){
				$this->error['warning'] = "Additional Handling Cost must be a decimal value eg. (2.00). Maximum value (99.99)";
			} else {
				//Clean it up if the user has put in single decimal (which is valid) eg 2.5., or whole number eg. 2
				//This will be done silently, user will see it when they return to the shipping module
				$this->request->post['canadapost_handling'] = sprintf("%.2f",$this->request->post['canadapost_handling']);
			}

		}


		return !$this->error;
	}
}
?>
