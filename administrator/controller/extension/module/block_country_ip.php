<?php
class ControllerExtensionModuleBlockCountryIp extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/block_country_ip');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_block_country_ip', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['token'] . '&type=module', true));
		}
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_message'] = $this->language->get('entry_message');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_country'] = $this->language->get('entry_country');
		$data['entry_redirect'] = $this->language->get('entry_redirect');
		$data['entry_site'] = $this->language->get('entry_site');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		
		$data['help_message'] = $this->language->get('help_message');
		$data['help_country'] = $this->language->get('help_country');
		$data['help_redirect'] = $this->language->get('help_redirect');
		$data['help_site'] = $this->language->get('help_site');
		
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();
		

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['site'])) {
			$data['error_site'] = $this->error['site'];
		} else {
			$data['error_site'] = '';
		}
		if (isset($this->error['message'])) {
			$data['error_message'] = $this->error['message'];
		} else {
			$data['error_message'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/block_country_ip', 'user_token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/module/block_country_ip', 'user_token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['token'] . '&type=module', true);

		if (isset($this->request->post['module_block_country_ip_status'])) {
			$data['module_block_country_ip_status'] = $this->request->post['module_block_country_ip_status'];
		} else {
			$data['module_block_country_ip_status'] = $this->config->get('module_block_country_ip_status');
		}
		
		if (isset($this->request->post['module_block_country_ip_country'])) {
			$data['module_block_country_ip_country'] = $this->request->post['module_block_country_ip_country'];
		} elseif (null != ($this->config->get('module_block_country_ip_country'))) {
			$data['module_block_country_ip_country'] = $this->config->get('module_block_country_ip_country');
		} else {
			$data['module_block_country_ip_country'] = array();
		}
		
		if (isset($this->request->post['module_block_country_ip_redirect'])) {
			$data['module_block_country_ip_redirect'] = $this->request->post['module_block_country_ip_redirect'];
		} elseif (null != ($this->config->get('module_block_country_ip_redirect')))  {
			$data['module_block_country_ip_redirect'] = $this->config->get('module_block_country_ip_redirect');
		} else {
			$data['module_block_country_ip_redirect'] = '0';
		}
		
		if (isset($this->request->post['module_block_country_ip_message'])) {
			$data['module_block_country_ip_message'] = $this->request->post['module_block_country_ip_message'];
		} else {
			$data['module_block_country_ip_message'] = $this->config->get('module_block_country_ip_message');
		}
		
		if (isset($this->request->post['module_block_country_ip_site'])) {
			$data['module_block_country_ip_site'] = $this->request->post['module_block_country_ip_site'];
		} else {
			$data['module_block_country_ip_site'] = $this->config->get('module_block_country_ip_site');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/block_country_ip', $data));
	}

	public function install()
	{
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blocked_ips` (
				  `id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
				  `user_ip` varchar(50) NOT NULL,
				  `country_iso_code` varchar(3) DEFAULT NULL,
				  `country_name` varchar(50) DEFAULT NULL,
				  `subdivision_name` varchar(50) DEFAULT NULL,
				  `subdivision_iso_code` varchar(5) DEFAULT NULL,
				  `city_name` varchar(50) DEFAULT NULL,
				  `postal_code` varchar(20) DEFAULT NULL,
				  `latitude` varchar(20) DEFAULT NULL,
				  `longitude` varchar(20) DEFAULT NULL,
				  `access_page` varchar(200) NOT NULL,
				  `access_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
				  `error_msg` varchar(100) DEFAULT NULL
				) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
				
		$query = $this->db->query($sql);
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/block_country_ip')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['module_block_country_ip_redirect'])) {
			if($this->request->post['module_block_country_ip_redirect'] == 1)
			{
				if ((isset($this->request->post['module_block_country_ip_site']))&&(filter_var($this->request->post['module_block_country_ip_site'], FILTER_VALIDATE_URL))) {
					// Do Nothing
				}
				else
				{
					$this->error['site'] = $this->language->get('error_site');
				}
			}
			else
			{
				if ((isset($this->request->post['module_block_country_ip_message']))&&($this->request->post['module_block_country_ip_message'] != '')) {
					// Do Nothing
				}
				else
				{
					$this->error['message'] = $this->language->get('error_message');
				}
			}
		}
		

		return !$this->error;
	}
}
