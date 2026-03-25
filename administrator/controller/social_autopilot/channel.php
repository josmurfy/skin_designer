<?php
class ControllerSocialAutoPilotChannel extends Controller {
	private $error = array();

	public function getChannels() {
		$this->load->language('social_autopilot/channel');

		$this->load->model('social_autopilot/channel');

		$json = array();

		// Text
		$data['text_add_channel_permission'] = $this->language->get('text_add_channel_permission');

		// Help
		$data['help_add_channel_permission'] = $this->language->get('help_add_channel_permission');

		// Buttons
		$data['button_close'] = $this->language->get('button_close');

		// ------------------------------------------------------

		$api_key = $this->config->get('social_autopilot_api_key');

		$callback_url = rawurlencode(HTTPS_CATALOG . 'index.php?route=social_autopilot/permission&secret_key=' . $api_key);

		// Used for TWITTER after callback => success  to comeback in store admin
		$return_url = rawurlencode(HTTPS_SERVER . 'index.php?route=social_autopilot/channel_permission&token=' . $this->session->data['token']);

		$data['channels'] = array();

		$channels = $this->model_social_autopilot_channel->getChannels();

		if ($channels) {
			foreach ($channels as $channel) {
				$channel_url = ($this->config->get('social_autopilot_localhost_debug') ? 'https://localhost/work/ocx3/' : 'https://www.oc-extensions.com/') . 'index.php?route=social_autopilot/' . trim(utf8_strtolower($channel['code'])) . '&api_key=' . $api_key . '&callback_url=' . $callback_url . '&return_url=' . $return_url;

				$data['channels'][] = array(
					'channel_id' => $channel['channel_id'],
					'code'       => $channel['code'],
					'name'       => $channel['name'],
					'href'       => $channel_url
				);
			}
		}

		$json['output'] = $this->load->view('social_autopilot/channel_modal', $data);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
