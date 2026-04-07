<?php
/* This module is copywrite to ozxmod
 * Author: ozxmod(ozxmod@gmail.com)
 * It is illegal to remove this comment without prior notice to oxzmod(ozxmod@gmail.com)
*/
class ControllerExtensionModuleAwesomeSocialLoginOzxmod extends Controller {
	private $error = array();

	public function index() {

		$oc_version = (int)VERSION.'.'.str_replace('.',"",substr(VERSION,2));

		if($oc_version >= 2.302) {
			$this->load->language('extension/module/awesome_social_login_ozxmod');
		}else {
			$this->load->language('module/awesome_social_login_ozxmod');
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('awesome_social_login_ozxmod', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');

			if($oc_version >= 2.302) {
				$this->response->redirect($this->url->link('extension/extension', 'user_token=' . $this->session->data['token']. '&type=module', 'SSL'));
			}else{
				$this->response->redirect($this->url->link('extension/module', 'user_token=' . $this->session->data['token'], 'SSL'));
			}
		}

		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();
		$data['languages'] = $languages;

		$data['text_edit'] = $this->language->get('text_edit');

		$data['text_modulesetting'] = $this->language->get('text_modulesetting');
		$data['entry_display_at_login'] = $this->language->get('entry_display_at_login');
		$data['entry_display_at_checkout'] = $this->language->get('entry_display_at_checkout');
		$data['entry_display'] = $this->language->get('entry_display');
		$data['entry_fb'] = $this->language->get('entry_fb');
		$data['entry_google'] = $this->language->get('entry_google');
		$data['entry_twitter'] = $this->language->get('entry_twitter');
		$data['entry_linkedin'] = $this->language->get('entry_linkedin');
		$data['entry_enable'] = $this->language->get('entry_enable');
		$data['entry_disable'] = $this->language->get('entry_disable');
		$data['text_apisetting'] = $this->language->get('text_apisetting');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_social_status'] = $this->language->get('entry_social_status');
		$data['text_social_status'] = $this->language->get('text_social_status');
		$data['text_status'] = $this->language->get('text_status');
		$data['entry_yes'] = $this->language->get('entry_yes');
		$data['entry_no'] = $this->language->get('entry_no');
		$data['text_signup_setting'] = $this->language->get('text_signup_setting');
		$data['entry_name_signup'] = $this->language->get('entry_name_signup');
		$data['entry_telephone_signup'] = $this->language->get('entry_telephone_signup');
		$data['text_name_signup'] = $this->language->get('text_name_signup');
		$data['text_telephone_signup'] = $this->language->get('text_telephone_signup');
		$data['text_template_setting'] = $this->language->get('text_template_setting');
		$data['entry_theme'] = $this->language->get('entry_theme');
		$data['text_enable'] = $this->language->get('text_enable');
		$data['text_disable'] = $this->language->get('text_disable');

		$data['heading_title'] = $this->language->get('heading_title');
		$data['entry_apikey'] = $this->language->get('entry_apikey');
		$data['entry_apisecret'] = $this->language->get('entry_apisecret');
		$data['entry_googleapikey'] = $this->language->get('entry_googleapikey');
		$data['entry_googleapisecret'] = $this->language->get('entry_googleapisecret');
		$data['entry_twitterapikey'] = $this->language->get('entry_twitterapikey');
		$data['entry_twitterapisecret'] = $this->language->get('entry_twitterapisecret');
		$data['entry_linkedinapikey'] = $this->language->get('entry_linkedinapikey');
		$data['entry_linkedinapisecret'] = $this->language->get('entry_linkedinapisecret');

		$data['text_newfbapp'] = $this->language->get('text_newfbapp');
		$data['text_googlenote'] = $this->language->get('text_googlenote');
		$data['text_newgoogleapp'] = $this->language->get('text_newgoogleapp');
		$data['text_newtwitterapp'] = $this->language->get('text_newtwitterapp');
		$data['text_newlinkedinapp'] = $this->language->get('text_newlinkedinapp');
		$data['text_support'] = $this->language->get('text_support');
		$data['text_mail'] = $this->language->get('text_mail');
		$data['text_visit'] = $this->language->get('text_visit');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_popup'] = $this->language->get('tab_popup');
		$data['tab_api'] = $this->language->get('tab_api');
		$data['tab_support'] = $this->language->get('tab_support');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');


 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['code'])) {
			$data['error_code'] = $this->error['code'];
		} else {
			$data['error_code'] = '';
		}

  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'user_token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/awesome_social_login_ozxmod', 'user_token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		if($oc_version >= 2.302) {
			$data['action'] = $this->url->link('extension/module/awesome_social_login_ozxmod', 'user_token=' . $this->session->data['token']. '&type=module', 'SSL');
			$data['cancel'] = $this->url->link('extension/extension', 'user_token=' . $this->session->data['token']. '&type=module', 'SSL');
		}else{
			$data['action'] = $this->url->link('module/awesome_social_login_ozxmod', 'user_token=' . $this->session->data['token'], 'SSL');
			$data['cancel'] = $this->url->link('extension/module', 'user_token=' . $this->session->data['token'], 'SSL');
		}

		$data['modules'] = array();

		foreach ($languages as $language) {
			if (isset($this->request->post['awesome_social_login_ozxmod_button_' . $language['language_id']])) {
				$data['awesome_social_login_ozxmod_button_' . $language['language_id']] = $this->request->post['awesome_social_login_ozxmod_button_' . $language['language_id']];
			} else {
				$data['awesome_social_login_ozxmod_button_' . $language['language_id']] = $this->config->get('awesome_social_login_ozxmod_button_' . $language['language_id']);
			}
		}

		if (isset($this->request->post['awesome_social_login_ozxmod_display_at_login'])) {
			$data['awesome_social_login_ozxmod_display_at_login'] = $this->request->post['awesome_social_login_ozxmod_display_at_login'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_display_at_login')) {
			$data['awesome_social_login_ozxmod_display_at_login'] = $this->config->get('awesome_social_login_ozxmod_display_at_login');
		} else $data['awesome_social_login_ozxmod_display_at_login'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_display_at_checkout'])) {
			$data['awesome_social_login_ozxmod_display_at_checkout'] = $this->request->post['awesome_social_login_ozxmod_display_at_checkout'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_display_at_checkout')) {
			$data['awesome_social_login_ozxmod_display_at_checkout'] = $this->config->get('awesome_social_login_ozxmod_display_at_checkout');
		} else $data['awesome_social_login_ozxmod_display_at_checkout'] = '';


		if (isset($this->request->post['awesome_social_login_ozxmod_display_fb'])) {
			$data['awesome_social_login_ozxmod_display_fb'] = $this->request->post['awesome_social_login_ozxmod_display_fb'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_display_fb')) {
			$data['awesome_social_login_ozxmod_display_fb'] = $this->config->get('awesome_social_login_ozxmod_display_fb');
		} else $data['awesome_social_login_ozxmod_display_fb'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_display_google'])) {
			$data['awesome_social_login_ozxmod_display_google'] = $this->request->post['awesome_social_login_ozxmod_display_google'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_display_google')) {
			$data['awesome_social_login_ozxmod_display_google'] = $this->config->get('awesome_social_login_ozxmod_display_google');
		} else $data['awesome_social_login_ozxmod_display_google'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_display_twitter'])) {
			$data['awesome_social_login_ozxmod_display_twitter'] = $this->request->post['awesome_social_login_ozxmod_display_twitter'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_display_twitter')) {
			$data['awesome_social_login_ozxmod_display_twitter'] = $this->config->get('awesome_social_login_ozxmod_display_twitter');
		} else $data['awesome_social_login_ozxmod_display_twitter'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_display_linkedin'])) {
			$data['awesome_social_login_ozxmod_display_linkedin'] = $this->request->post['awesome_social_login_ozxmod_display_linkedin'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_display_linkedin')) {
			$data['awesome_social_login_ozxmod_display_linkedin'] = $this->config->get('awesome_social_login_ozxmod_display_linkedin');
		} else $data['awesome_social_login_ozxmod_display_linkedin'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_status'])) {
			$data['awesome_social_login_ozxmod_status'] = $this->request->post['awesome_social_login_ozxmod_status'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_status')) {
			$data['awesome_social_login_ozxmod_status'] = $this->config->get('awesome_social_login_ozxmod_status');
		} else $data['awesome_social_login_ozxmod_status'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_social_status'])) {
			$data['awesome_social_login_ozxmod_social_status'] = $this->request->post['awesome_social_login_ozxmod_social_status'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_social_status')) {
			$data['awesome_social_login_ozxmod_social_status'] = $this->config->get('awesome_social_login_ozxmod_social_status');
		} else $data['awesome_social_login_ozxmod_social_status'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_name_signup'])) {
			$data['awesome_social_login_ozxmod_name_signup'] = $this->request->post['awesome_social_login_ozxmod_name_signup'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_name_signup')) {
			$data['awesome_social_login_ozxmod_name_signup'] = $this->config->get('awesome_social_login_ozxmod_name_signup');
		} else $data['awesome_social_login_ozxmod_name_signup'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_telephone_signup'])) {
			$data['awesome_social_login_ozxmod_telephone_signup'] = $this->request->post['awesome_social_login_ozxmod_telephone_signup'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_telephone_signup')) {
			$data['awesome_social_login_ozxmod_telephone_signup'] = $this->config->get('awesome_social_login_ozxmod_telephone_signup');
		} else $data['awesome_social_login_ozxmod_telephone_signup'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_apikey'])) {
			$data['awesome_social_login_ozxmod_apikey'] = $this->request->post['awesome_social_login_ozxmod_apikey'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_apikey')) {
			$data['awesome_social_login_ozxmod_apikey'] = $this->config->get('awesome_social_login_ozxmod_apikey');
		} else $data['awesome_social_login_ozxmod_apikey'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_apisecret'])) {
			$data['awesome_social_login_ozxmod_apisecret'] = $this->request->post['awesome_social_login_ozxmod_apisecret'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_apisecret')) {
			$data['awesome_social_login_ozxmod_apisecret'] = $this->config->get('awesome_social_login_ozxmod_apisecret');
		} else $data['awesome_social_login_ozxmod_apisecret'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_googleapikey'])) {
			$data['awesome_social_login_ozxmod_googleapikey'] = $this->request->post['awesome_social_login_ozxmod_googleapikey'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_googleapikey')) {
			$data['awesome_social_login_ozxmod_googleapikey'] = $this->config->get('awesome_social_login_ozxmod_googleapikey');
		} else $data['awesome_social_login_ozxmod_googleapikey'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_googleapisecret'])) {
			$data['awesome_social_login_ozxmod_googleapisecret'] = $this->request->post['awesome_social_login_ozxmod_googleapisecret'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_googleapisecret')) {
			$data['awesome_social_login_ozxmod_googleapisecret'] = $this->config->get('awesome_social_login_ozxmod_googleapisecret');
		} else $data['awesome_social_login_ozxmod_googleapisecret'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_twitterapikey'])) {
			$data['awesome_social_login_ozxmod_twitterapikey'] = $this->request->post['awesome_social_login_ozxmod_twitterapikey'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_twitterapikey')) {
			$data['awesome_social_login_ozxmod_twitterapikey'] = $this->config->get('awesome_social_login_ozxmod_twitterapikey');
		} else $data['awesome_social_login_ozxmod_twitterapikey'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_twitterapisecret'])) {
			$data['awesome_social_login_ozxmod_twitterapisecret'] = $this->request->post['awesome_social_login_ozxmod_twitterapisecret'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_twitterapisecret')) {
			$data['awesome_social_login_ozxmod_twitterapisecret'] = $this->config->get('awesome_social_login_ozxmod_twitterapisecret');
		} else $data['awesome_social_login_ozxmod_twitterapisecret'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_linkedinapikey'])) {
			$data['awesome_social_login_ozxmod_linkedinapikey'] = $this->request->post['awesome_social_login_ozxmod_linkedinapikey'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_linkedinapikey')) {
			$data['awesome_social_login_ozxmod_linkedinapikey'] = $this->config->get('awesome_social_login_ozxmod_linkedinapikey');
		} else $data['awesome_social_login_ozxmod_linkedinapikey'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_linkedinapisecret'])) {
			$data['awesome_social_login_ozxmod_linkedinapisecret'] = $this->request->post['awesome_social_login_ozxmod_linkedinapisecret'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_linkedinapisecret')) {
			$data['awesome_social_login_ozxmod_linkedinapisecret'] = $this->config->get('awesome_social_login_ozxmod_linkedinapisecret');
		} else $data['awesome_social_login_ozxmod_linkedinapisecret'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod_template_id'])) {
			$data['awesome_social_login_ozxmod_template_id'] = $this->request->post['awesome_social_login_ozxmod_template_id'];
		} elseif ($this->config->get('awesome_social_login_ozxmod_template_id')) {
			$data['awesome_social_login_ozxmod_template_id'] = $this->config->get('awesome_social_login_ozxmod_template_id');
		} else $data['awesome_social_login_ozxmod_template_id'] = '';

		if (isset($this->request->post['awesome_social_login_ozxmod'])) {
			$data['modules'] = $this->request->post['awesome_social_login_ozxmod'];
		} elseif ($this->config->get('awesome_social_login_ozxmod')) {
			$data['modules'] = $this->config->get('awesome_social_login_ozxmod');
		}

		// Theme template
		if ($this->request->server['HTTPS']) {
			$server = HTTPS_CATALOG;
		} else {
			$server = HTTP_CATALOG;
		}

		$data['text_google_redirect'] = sprintf($this->language->get('text_google_redirect'), $server."index.php?route=extension/module/awesome_social_login_ozxmod/glogin");
		$data['text_linkedin_redirect'] = sprintf($this->language->get('text_linkedin_redirect'), $server."index.php?route=extension/module/awesome_social_login_ozxmod/linkedinlogin");
		$data['text_twitter_redirect'] = sprintf($this->language->get('text_twitter_redirect'), $server."index.php?route=extension/module/awesome_social_login_ozxmod/twitter");


		$data['themes'] = array(
			array(
				'template_id' => 1,
				'text'		=> $this->language->get('text_template_1'),
				'image'		=> $server."catalog/view/theme/default/stylesheet/ozxmod/assets/img/template_1.png",
			),
			array(
				'template_id' => 2,
				'text'		=> $this->language->get('text_template_2'),
				'image'		=> $server."catalog/view/theme/default/stylesheet/ozxmod/assets/img/template_2.png",
			),
			array(
				'template_id' => 3,
				'text'		=> $this->language->get('text_template_3'),
				'image'		=> $server."catalog/view/theme/default/stylesheet/ozxmod/assets/img/template_3.png",
			)
		);

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		if($oc_version >= 2.302) {
			$this->response->setOutput($this->load->view('extension/module/awesome_social_login_ozxmod.tpl', $data));
		}else{
			$this->response->setOutput($this->load->view('module/awesome_social_login_ozxmod.tpl', $data));
		}

	}

	private function validate() {
		$oc_version = (int)VERSION.'.'.str_replace('.',"",substr(VERSION,2));

		if($oc_version >= 2.302) {
			if (!$this->user->hasPermission('modify', 'extension/module/awesome_social_login_ozxmod')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
		}else{
			if (!$this->user->hasPermission('modify', 'module/awesome_social_login_ozxmod')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
		}

		$warning = array();

		if (isset($this->request->post['awesome_social_login_ozxmod_display_fb'])) {
			if (!$this->request->post['awesome_social_login_ozxmod_apikey'] || !$this->request->post['awesome_social_login_ozxmod_apisecret']) {
				$warning[] = $this->language->get('error_facebook');
			}
		}

		if (isset($this->request->post['awesome_social_login_ozxmod_display_google'])) {
			if (!$this->request->post['awesome_social_login_ozxmod_googleapikey'] || !$this->request->post['awesome_social_login_ozxmod_googleapisecret']) {
				$warning[] = $this->language->get('error_google');
			}
		}

		if (isset($this->request->post['awesome_social_login_ozxmod_display_linkedin'])) {
			if (!$this->request->post['awesome_social_login_ozxmod_linkedinapikey'] || !$this->request->post['awesome_social_login_ozxmod_linkedinapisecret']) {
				$warning[] = $this->language->get('error_linkedin');
			}
		}

		if (isset($this->request->post['awesome_social_login_ozxmod_display_twitter'])) {
			if (!$this->request->post['awesome_social_login_ozxmod_twitterapikey'] || !$this->request->post['awesome_social_login_ozxmod_twitterapisecret']) {
				$warning[] = $this->language->get('error_twitter');
			}
		}

		if(!empty($warning))
			$this->error['warning'] = implode("<br/>", $warning);


		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}

/* This module is copywrite to ozxmod
 * Author: ozxmod(ozxmod@gmail.com)
 * It is illegal to remove this comment without prior notice to oxzmod(ozxmod@gmail.com)
*/
?>
