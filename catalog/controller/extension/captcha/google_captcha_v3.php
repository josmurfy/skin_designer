<?php
class ControllerExtensionCaptchaGoogleCaptchaV3 extends Controller {
    public function index($error = array()) {
        $this->load->language('extension/captcha/google_captcha_v3');

		$data['text_captcha'] = $this->language->get('text_captcha');

		$data['entry_captcha'] = $this->language->get('entry_captcha');

        if (isset($error['captcha'])) {
			$data['error_captcha'] = $error['captcha'];
		} else {
			$data['error_captcha'] = '';
		}

		$data['site_key'] = $this->config->get('google_captcha_v3_key');

        $data['route'] = $this->request->get['route'];

		return $this->load->view('extension/captcha/google_captcha_v3', $data);
    }

    public function validate() {
		if (empty($this->session->data['gcapcha'])) {
			$this->load->language('extension/captcha/google_captcha_v3');

			$recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($this->config->get('google_captcha_v3_secret')) . '&response=' . $this->request->post['g-recaptcha-response'] . '&remoteip=' . $this->request->server['REMOTE_ADDR']);

			$recaptcha = json_decode($recaptcha, true);

			if ($recaptcha['success']) {
				$this->session->data['gcapcha']	= true;
			} else {
                foreach($recaptcha['error-codes'] as $errors)
                {
                    return $this->language->get($errors);
                }
			}
		}
    }
}
