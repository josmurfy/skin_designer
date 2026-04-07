<?php

	/********************************************************************
	Version 1.0
		RapidCart Connector for OpenCart
		Copyright 2014 Purple Turtle Productions. All rights reserved.
		license	GNU General Public License version 3 or later; see GPLv3.txt
	By: Keneto 2014-11
	********************************************************************/

class ControllerModuleCartproductfeed extends Controller {
	
	private $error = array(); 
	
	public function index() {
		//Load the language file for this module
		$this->load->language('module/cartproductfeed');

		//Set the title from the language file $_['heading_title'] string
		$this->document->setTitle($this->language->get('heading_title'));
		
		//Load the settings model. You can also add any other models you want to load here.
		$this->load->model('setting/setting');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('cartproductfeed', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');

			//Is there a setting for token already in the DB?
			$id = $this->db->query('SELECT setting_id FROM ' . DB_PREFIX . 'setting a WHERE a.key = \'cart_product_feed_token\';');
			$strRapidCartToken = $this->request->post['edtRapidCartToken'];
			if (strlen($strRapidCartToken) == 0)
				$strRapidCartToken = rand(1000, 9999) . '-' . date('U') . '-' . rand(1000, 9999);
			if ($id->num_rows > 0)
				$this->db->query('UPDATE ' . DB_PREFIX . "setting SET value='$strRapidCartToken' WHERE setting_id = " . $id->row['setting_id']);
			else
				$this->db->query('INSERT ' . DB_PREFIX . "setting (`key`, `value`) VALUES ('cart_product_feed_token', '$strRapidCartToken');");

			$this->redirect($this->url->link('extension/module', 'user_token=' . $this->session->data['token'], 'SSL'));
		}

		//This is how the language gets pulled through from the language file.
		//
		// If you want to use any extra language items - ie extra text on your admin page for any reason,
		// then just add an extra line to the $text_strings array with the name you want to call the extra text,
		// then add the same named item to the $_[] array in the language file.
		//
		// 'my_module_example' is added here as an example of how to add - see admin/language/english/module/my_module.php for the
		// other required part.
		
		$text_strings = array(
				'heading_title',
				'text_enabled',
				'text_disabled',
				'text_content_top',
				'text_content_bottom',
				'text_column_left',
				'text_column_right',
				'entry_layout',
				'entry_position',
				'entry_status',
				'entry_sort_order',
				'button_save',
				'button_cancel',
				'button_add_module',
				'button_remove',
				'entry_example', //this is an example extra field added
				'rapid_cart_token'
		);
		
		foreach ($text_strings as $text) {
			$this->data[$text] = $this->language->get($text);
		}
		
		//END LANGUAGE

		//Load the token
		$data = $this->db->query('SELECT value FROM ' . DB_PREFIX . 'setting a WHERE a.key = \'cart_product_feed_token\';');
		if ($data->num_rows > 0)
			$this->data['strRapidCartToken'] = $data->row['value'];
		else
			$this->data['strRapidCartToken'] = '';
		
		//The following code pulls in the required data from either config files or user
		//submitted data (when the user presses save in admin). Add any extra config data
		// you want to store.
		//
		// NOTE: These must have the same names as the form data in your my_module.tpl file
		//
		$config_data = array(
				'my_module_example' //this becomes available in our view by the foreach loop just below.
		);
		
		foreach ($config_data as $conf) {
			if (isset($this->request->post[$conf])) {
				$this->data[$conf] = $this->request->post[$conf];
			} else {
				$this->data[$conf] = $this->config->get($conf);
			}
		}
	
		//This creates an error message. The error['warning'] variable is set by the call to function validate() in this controller (below)
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		//SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'user_token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/cartproductfeed', 'user_token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/cartproductfeed', 'user_token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['token'], 'SSL');

	
		//This code handles the situation where you have multiple instances of this module, for different layouts.
		if (isset($this->request->post['my_module_module'])) {
			$modules = explode(',', $this->request->post['my_module_module']);
		} elseif ($this->config->get('my_module_module') != '') {
			$modules = explode(',', $this->config->get('my_module_module'));
		} else {
			$modules = array();
		}			
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
				
		foreach ($modules as $module) {
			if (isset($this->request->post['my_module_' . $module . '_layout_id'])) {
				$this->data['my_module_' . $module . '_layout_id'] = $this->request->post['my_module_' . $module . '_layout_id'];
			} else {
				$this->data['my_module_' . $module . '_layout_id'] = $this->config->get('my_module_' . $module . '_layout_id');
			}	
			
			if (isset($this->request->post['my_module_' . $module . '_position'])) {
				$this->data['my_module_' . $module . '_position'] = $this->request->post['my_module_' . $module . '_position'];
			} else {
				$this->data['my_module_' . $module . '_position'] = $this->config->get('my_module_' . $module . '_position');
			}	
			
			if (isset($this->request->post['my_module_' . $module . '_status'])) {
				$this->data['my_module_' . $module . '_status'] = $this->request->post['my_module_' . $module . '_status'];
			} else {
				$this->data['my_module_' . $module . '_status'] = $this->config->get('my_module_' . $module . '_status');
			}	
						
			if (isset($this->request->post['my_module_' . $module . '_sort_order'])) {
				$this->data['my_module_' . $module . '_sort_order'] = $this->request->post['my_module_' . $module . '_sort_order'];
			} else {
				$this->data['my_module_' . $module . '_sort_order'] = $this->config->get('my_module_' . $module . '_sort_order');
			}				
		}
		
		$this->data['modules'] = $modules;
		
		if (isset($this->request->post['my_module_module'])) {
			$this->data['my_module_module'] = $this->request->post['my_module_module'];
		} else {
			$this->data['my_module_module'] = $this->config->get('my_module_module');
		}

		//Choose which template file will be used to display this request.
		$this->template = 'module/cartproductfeed.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		//Send the output.
		$this->response->setOutput($this->render());
	}

	//Validate user settings
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/cartproductfeed')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}

	public function install() {

	}


}
?>