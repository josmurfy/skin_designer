<?php
class ModelSocialAutoPilotSender extends Model {
	public function send($request_uid) {
		$this->load->model('social_autopilot/request');

		$curl_url = ($this->config->get('social_autopilot_localhost_debug') ? 'https://localhost/work/ocx3/' : 'https://www.oc-extensions.com/') . 'index.php?route=api/social_autopilot&api_key=' . $this->config->get('social_autopilot_api_key');
		$curl_info = $this->model_social_autopilot_request->getRequest($request_uid);

		$curl_status = $this->sendCURL($curl_url, $curl_info);

		//print '<pre>'; print_r($curl_info); print '</pre>';
		//print '<pre>'; print_r($curl_status); print '</pre>';

		return $curl_status;
	}

	private function sendCURL($url, $info) {
		$curl_status = array();

		$curl = curl_init();

		// Set SSL if required
		if (substr($url, 0, 5) == 'https') {
			curl_setopt($curl, CURLOPT_PORT, 443);
		}

		curl_setopt($curl, CURLOPT_TIMEOUT, 30);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, true);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_REFERER, HTTP_SERVER);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($info));

		$curl_response = curl_exec($curl);

		if (!$curl_response) {
			$curl_status['error'] = array('code' => 'error_curl', 'args' => array(curl_error($curl), curl_errno($curl)));
		} else {
			$curl_response = json_decode($curl_response, true);

			if (isset($curl_response['error'])) {
				$curl_status['error'] = $curl_response['error'];
			} elseif (!isset($curl_response['success']) || (isset($curl_response['success']) && $curl_response['success'] != true)) {
				$curl_status['error'] = array('code' => 'error_destination', 'args' => array($url));
			} else {
				$curl_status['response'] = $curl_response;
			}
		}

		curl_close($curl);

		return $curl_status;
	}
}
