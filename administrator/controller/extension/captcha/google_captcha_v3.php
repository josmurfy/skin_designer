<?php
class ControllerExtensionCaptchaGoogleCaptchaV3 extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/captcha/google_captcha_v3');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('google_captcha_v3', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=captcha', true));
		}


        $text_strings = array(
            'heading_title',

            'text_version',
            'text_edit',
            'text_enabled',
            'text_disabled',
            'text_signup',

            'entry_register_site',
            'entry_key',
            'entry_secret',
            'entry_status',
            'entry_version',

            'button_save',
            'button_cancel',

            'tab_general',
            'tab_about',

        );

        foreach ($text_strings as $text) {
            $data[$text] = $this->language->get($text);
        }

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['key'])) {
			$data['error_key'] = $this->error['key'];
		} else {
			$data['error_key'] = '';
		}

		if (isset($this->error['secret'])) {
			$data['error_secret'] = $this->error['secret'];
		} else {
			$data['error_secret'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=captcha', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/captcha/google_captcha_v3', 'token=' . $this->session->data['token'], true)
		);

		$data['action'] = $this->url->link('extension/captcha/google_captcha_v3', 'token=' . $this->session->data['token'], true);

		$data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=captcha', true);

		if (isset($this->request->post['google_captcha_v3_key'])) {
			$data['google_captcha_v3_key'] = $this->request->post['google_captcha_v3_key'];
		} else {
			$data['google_captcha_v3_key'] = $this->config->get('google_captcha_v3_key');
		}

		if (isset($this->request->post['google_captcha_v3_secret'])) {
			$data['google_captcha_v3_secret'] = $this->request->post['google_captcha_v3_secret'];
		} else {
			$data['google_captcha_v3_secret'] = $this->config->get('google_captcha_v3_secret');
		}

		if (isset($this->request->post['google_captcha_v3_status'])) {
			$data['google_captcha_v3_status'] = $this->request->post['google_captcha_v3_status'];
		} else {
			$data['google_captcha_v3_status'] = $this->config->get('google_captcha_v3_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/captcha/google_captcha_v3', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/captcha/google_captcha_v3')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['google_captcha_v3_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}

		if (!$this->request->post['google_captcha_v3_secret']) {
			$this->error['secret'] = $this->language->get('error_secret');
		}

		return !$this->error;
	}
}
