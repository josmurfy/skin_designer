<?php
class ControllerExtensionModuleFeedify extends Controller{
	private $error = array();

	function editLayout($layout_id){
		$query = $this->db->query("SELECT `layout_module_id` FROM `".DB_PREFIX."layout_module` WHERE `code` = 'feedify' AND `layout_id` = '$layout_id'");		
		if(!$query->num_rows){
			$this->db->query("INSERT INTO `".DB_PREFIX."layout_module` SET `code` = 'feedify', `layout_id` = '$layout_id', `position` = 'content_bottom'");
		}
	}

	public function index(){
		$this->language->load('extension/module/feedify');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		$this->load->model('design/layout');
		$layout_total = $this->model_design_layout->getTotalLayouts();
		$filter_data = array(
			'start' => 0,
			'limit' => $layout_total
		);
		$results = $this->model_design_layout->getLayouts($filter_data);
		foreach ($results as $layout) {
			$this->editLayout($layout['layout_id']);
		}

		$this->document->addStyle('./view/stylesheet/feedify_main.css');

		if (!isset($_GET['page']) || strlen(trim($_GET['page'])) <= 0) {
			$this->handleMainPage();
		}else{
			switch (trim($_GET['page'])){
				case 'main':
					$this->handleMainPage();
					break;

				case 'callback':
					$this->handleCallbackPage();
					break;
			}
		}
	}

	private function getUrl($queryParams){
		$url = $this->url->link('extension/module/feedify', 'token=' . $this->session->data['token'] . '&' . $queryParams, '');
		return str_replace('&amp;', '&', $url);
	}

	private function handleMainPage(){
		$this->feedifyProcessFeedifyOptions();

		$feedify_settings = $this->model_setting_setting->getSetting('feedify');

		$feedify_host_name = 'https://feedify.net';
		$m_license_code = isset($feedify_settings['feedify_license_code']) ? $feedify_settings['feedify_license_code'] : '';		
		$m_status = isset($feedify_settings['feedify_status']) ? $feedify_settings['feedify_status'] : '';

		$data['m_license_code_old'] = $m_license_code;
		$data['m_widget_status'] = $m_status;

		$data['message'] = urldecode(isset($_GET['message']) ? htmlspecialchars($_GET['message'], ENT_COMPAT, 'UTF-8') : '');
		$data['error_message'] = urldecode(isset($_GET['error-message']) ? htmlspecialchars($_GET['error-message'], ENT_COMPAT, 'UTF-8') : '');

		$data['email'] = $email = $this->config->get('config_email');
		$data['user_full_name'] = '';

		$data['domain_name'] = '';
		if (isset($_SERVER['HTTP_HOST']))
			$data['domain_name'] = $domain_name = $_SERVER['HTTP_HOST'];
		else
			$data['domain_name'] = $domain_name = $_SERVER['SERVER_NAME'];

		$data['feedify_host_name'] = $feedify_host_name;
		$data['module_route'] = 'extension/module/feedify';
		$data['token'] = $token = $this->session->data['token'];
		$data['main_url'] = $main_url = $this->url->link('extension/module/feedify/success', 'token=' . $this->session->data['token']);

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$data['redirect_url'] = $redirect_url = HTTPS_SERVER.'index.php?route=extension/module/feedify/success&token='.$token;
		} else {
			$data['redirect_url'] = $redirect_url = HTTP_SERVER.'index.php?route=extension/module/feedify/success&token='.$token;
		}

		$data['src_url'] = $feedify_host_name."/thirdparty/pricing?domain=" . urlencode($domain_name) . "&email=$email" . "&channel=opencart&redirect_url=".urlencode($redirect_url);

		$this->renderPage($this->language->get('heading_title_main'), 'feedify_main.tpl', $data);
	}

	public function success(){
		$this->load->model('setting/setting');
		$feedify_settings = $this->model_setting_setting->getSetting('feedify');
		$data['feedify_license_code'] = 1;
		$data['feedify_status'] = 1;			
		$this->model_setting_setting->editSetting('feedify', $data);
		$this->response->redirect($this->url->link('extension/module/feedify', 'token=' . $this->session->data['token'], 'SSL'));
	}

	private function handleCallbackPage(){
		$data['main_url'] = $this->getUrl('page=main');		
		$data['vm'] = urldecode(isset($_REQUEST['verification_message']) ? htmlspecialchars($_REQUEST['verification_message'], ENT_COMPAT, 'UTF-8') : '');
		$data['wwa'] = urldecode(isset($_REQUEST['feedify_widget_status']) ? htmlspecialchars($_REQUEST['feedify_widget_status'], ENT_COMPAT, 'UTF-8') : '');
		$data['option'] = urldecode(isset($_REQUEST['option']) ? $_REQUEST['option'] : null);
		$this->renderPage('', 'feedify_callback.tpl');
	}

	private function feedifyProcessFeedifyOptions(){
		$redirect_url = "";		
		if (isset($_REQUEST['weAction']))
		{
			if ($_REQUEST['weAction'] == 'wp-save')
			{
				$message = $this->feedifyUpdateFeedifyOptions();
				$redirect_url = $this->getUrl('page=main&'.$message[0].'='.urlencode($message[1]));
			}
			elseif ($_REQUEST['weAction'] == 'reset')
			{
				$message = $this->feedifyResetFeedifyOptions();
				$redirect_url = $this->getUrl('page=main&'.$message[0].'='.urlencode($message[1]));
			}
			elseif ($_REQUEST['weAction'] == 'activate')
			{
				$message = $this->feedifyActivateWeWidget();
				$redirect_url = $this->getUrl('page=main&'.$message[0].'='.urlencode($message[1]));
			}
			elseif ($_REQUEST['weAction'] == 'discardMessage')
			{
				$this->feedifyDiscardStatusMessage();
				$redirect_url = $this->getUrl('page=main');
			}

			if (strlen($redirect_url) > 0) {				
				$this->response->redirect($redirect_url);
			}
		}
	}

	/**
	* Discard message processor.
	*/
	private function feedifyDiscardStatusMessage(){
		$feedify_settings = $this->model_setting_setting->getSetting('feedify');
		$data['license_code'] = isset($feedify_settings['license_code']) ? $feedify_settings['license_code'] : '';		
		$data['status'] = '';
		$data['feedify_module'] = isset($feedify_settings['feedify_module']) ? $feedify_settings['feedify_module'] : '';
		$data['feedify_status'] = '';
		$this->model_setting_setting->editSetting('feedify', $data);
	}

	/**
	* Resetting processor.
	*/
	private function feedifyResetFeedifyOptions(){
		$feedify_settings = $this->model_setting_setting->getSetting('feedify');
		$data['license_code'] = '';
		$data['status'] = '';
		$data['feedify_module'] = isset($feedify_settings['feedify_module']) ? $feedify_settings['feedify_module'] : '';
		$this->model_setting_setting->editSetting('feedify', $data);		
		return array('message', 'Your Feedify options are deleted. You can signup for a new account.');
	}

	/**
	* Update processor.
	*/
	private function feedifyUpdateFeedifyOptions(){
		$wlc = isset($_REQUEST['feedify_license_code']) ? htmlspecialchars($_REQUEST['feedify_license_code'], ENT_COMPAT, 'UTF-8') : '';		
		$vm = isset($_REQUEST['verification_message']) ? htmlspecialchars($_REQUEST['verification_message'], ENT_COMPAT, 'UTF-8') : '';
		$wws = isset($_REQUEST['feedify_widget_status']) ? htmlspecialchars($_REQUEST['feedify_widget_status'], ENT_COMPAT, 'UTF-8') : 'ACTIVE';

		if (!empty($wlc)){
			$feedify_settings = $this->model_setting_setting->getSetting('feedify');
			$data['feedify_license_code'] = trim($wlc);
			$data['feedify_status'] = 1;			
			$this->model_setting_setting->editSetting('feedify', $data);
		}else return array('error-message', 'Please add a license code.');
	}

	/**
	* Activate processor.
	*/
	private function feedifyActivateWeWidget(){
		$feedify_settings = $this->model_setting_setting->getSetting('feedify');
		$wlc = isset($_REQUEST['feedify_license_code']) ? htmlspecialchars($_REQUEST['feedify_license_code'], ENT_COMPAT, 'UTF-8') : '';
		$old_value = isset($feedify_settings['license_code']) ? $feedify_settings['license_code'] : '';		
		$wws = isset($_REQUEST['feedify_widget_status']) ? htmlspecialchars($_REQUEST['feedify_widget_status'], ENT_COMPAT, 'UTF-8') : 'ACTIVE';
		if ($wlc === $old_value && $wdc == $old_value_d){
			$feedify_settings = $this->model_setting_setting->getSetting('feedify');
			$data['license_code'] = $wlc;
			$data['status'] = $wws;
			$data['feedify_module'] = isset($feedify_settings['feedify_module']) ? $feedify_settings['feedify_module'] : '';
			$this->model_setting_setting->editSetting('feedify', $data);
			$msg = 'Your plugin installation is complete. You can do further customizations from your Feedify dashboard.';
			return array('message', $msg);
		}	else{
			$msg = 'Unauthorized plugin activation request';
			return array('error-message', $msg);
		}
	}

	private function renderPage($headingTitle, $templateFile, $data) {
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		}
		else {
			$data['error_warning'] = '';
		}


		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home') ,
			'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL') ,
			'separator' => false
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module') ,
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'].'&type=module', 'SSL') ,
			'separator' => ' :: '
		);
		$data['breadcrumbs'][] = array(
			'text' => $headingTitle ,
			'href' => $this->url->link('extension/module/feedify', 'token=' . $this->session->data['token'], 'SSL') ,
			'separator' => ' :: '
		);
		$data['heading_title'] = $headingTitle;
		$data['modules'] = array();
		$this->load->model('design/layout');
		$data['layouts'] = $this->model_design_layout->getLayouts();
		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$this->template = 'module/'.$templateFile;
		


		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/'.$templateFile, $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/feedify')) {
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
