<?php
class ControllerSocialAutoPilotShare extends Controller {
	public function index() {
		$this->load->language('social_autopilot/share');

		$this->load->model('social_autopilot/channel');
		$this->load->model('social_autopilot/sender');
		$this->load->model('social_autopilot/scheduled_post');
		$this->load->model('social_autopilot/request');
		$this->load->model('social_autopilot/task');

		$json = array();

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', 'social_autopilot/share')) {
				$json['error'] = $this->language->get('error_permission');
			}

			if (!$json) {
				// need to know channels for validation (can be different from channel to channel)
				$selected_channels = array();

				if (isset($this->request->post['permission'])) {
					$selected_channels = $this->model_social_autopilot_channel->getChannelsByPermissionsIds($this->request->post['permission']);
				}

				// start validation
				if (!$selected_channels) {
					$json['error'] = $this->language->get('error_no_channels');
				}

				if (!$json) {
					foreach ($selected_channels as $channel) {
						foreach ($channel['setting'] as $key => $value) {
							if ($key == 'message_min_length' && utf8_strlen($this->request->post['message']) < $value) {
								$json['error'] = sprintf($this->language->get('error_message_min_length'), $channel['name'], $value);

								break;
							}

							if ($key == 'message_max_length' && utf8_strlen($this->request->post['message']) > $value) {
								$json['error'] = sprintf($this->language->get('error_message_max_length'), $channel['name'], $value);

								break;
							}

							if ($key == 'image_required' && utf8_strlen($this->request->post['image']) < 1) {
								$json['error'] = sprintf($this->language->get('error_image'), $channel['name'], $channel['name']);

								break;
							}
						}
					}
				}
			}

			if (!$json) {
				if (utf8_strlen($this->request->post['link']) < 1) {
					$json['error'] = $this->language->get('error_link');
				}
			}

			if (!$json) {
				if ($this->request->post['scheduled_post']) {
					if (utf8_strlen($this->request->post['schedule_datetime']) != 19) {
						$json['error'] = $this->language->get('error_schedule_time_format');
					}

					if (!$json) {
						$this->load->model('social_autopilot/timer');

						$remaining_minutes = $this->model_social_autopilot_timer->getRemainingMinutes($this->request->post['schedule_datetime']);

						if ($remaining_minutes < 20) {
							$json['error'] = $this->language->get('error_schedule_time_remaining');
						}
					}
				}
			}

			// validation is ok - build & send to OC-Extensions
			if (!$json) {
				// CASE SCHEDULED POST
				if ($this->request->post['scheduled_post']) {
					if ($this->request->post['scheduled_post_id']) {
						$this->model_social_autopilot_scheduled_post->editScheduledPost($this->request->post['scheduled_post_id'], $this->request->post);
					} else {
						$this->model_social_autopilot_scheduled_post->addScheduledPost($this->request->post);
					}

					$json['success'] = sprintf($this->language->get('text_success_scheduled_post'), $this->request->post['schedule_datetime']);

				} else {
					// CASE DIRECT POST
					// if scheduled post and was posted manual - delete it from scheduled list (=> task)
					if ($this->request->post['scheduled_post_id']) {
						$this->model_social_autopilot_scheduled_post->deleteScheduledPost($this->request->post['scheduled_post_id']);
					}

					// create task per channels - store later response from OCX
					$request_uid = $this->model_social_autopilot_request->convertRequestInTasks($this->request->post);

					$sap_response = $this->model_social_autopilot_sender->send($request_uid);

					if (isset($sap_response['error'])) {
						$json['error'] = vsprintf($this->language->get($sap_response['error']['code']), $sap_response['error']['args']);
					}

					if (isset($sap_response['response'])) {
						$sap_response_ok = $sap_response['response'];

						if (isset($sap_response_ok['success'])) {
							$json['success'] =	$this->language->get('text_success_task');
						}
					}
				}
			}

		} else {

			// Text
			$data['text_publish_title'] = $this->language->get('text_publish_title');
			$data['text_post_preview'] = $this->language->get('text_post_preview');
			$data['text_post_where'] = $this->language->get('text_post_where');

			$data['text_custom_link'] = $this->language->get('text_custom_link');

			// Entry
			$data['entry_message'] = $this->language->get('entry_message');
			$data['entry_schedule_date'] = $this->language->get('entry_schedule_date');

			// Help
			$data['help_scheduled_date'] = $this->language->get('help_scheduled_date');
			$data['help_autocomplete'] = $this->language->get('help_autocomplete');
			$data['help_custom_link'] = $this->language->get('help_custom_link');

			// Buttons
			$data['button_close'] = $this->language->get('button_close');
			$data['button_post'] = $this->language->get('button_post');
			$data['button_post_schedule'] = $this->language->get('button_post_schedule');
			$data['button_update_post_schedule'] = $this->language->get('button_update_post_schedule');

			// need to know selected pages when scheduled post is loaded
			$selected_pages = array();

			if (isset($this->request->get['scheduled_post_id'])) {
				$selected_pages = $this->model_social_autopilot_scheduled_post->getScheduledPostPages($this->request->get['scheduled_post_id']);
			}

			$data['channel_permissions'] = array();

			$this->load->model('social_autopilot/channel_permission');

			$filter_data = array(
				'filter_status' => 1
			);

			$channel_permissions = $this->model_social_autopilot_channel_permission->getPermissions($filter_data);

			if ($channel_permissions) {
				foreach ($channel_permissions as $channel_permission) {
					$data['channel_permissions'][] = array(
						'permission_id' => $channel_permission['permission_id'],
						'name' 			 => (utf8_strlen($channel_permission['name']) > 12) ? utf8_substr($channel_permission['name'], 0, 12) . '...' : $channel_permission['name'],
						'page_id'	    => $channel_permission['page_id'],
						'channel_code'  => $channel_permission['channel_code'],
						'selected'      => (!$selected_pages || in_array($channel_permission['page_id'], $selected_pages)) ? true : false
					);
				}
			}

			// custom post autocomplete
			$ignore_template_categories = array('review');

			$data['template_categories'] = array();

			$this->load->model('social_autopilot/template_category');

			$template_categories = $this->model_social_autopilot_template_category->getTemplateCategories();

			if ($template_categories) {
				foreach ($template_categories as $template_category) {
					if (!in_array($template_category['code'], $ignore_template_categories)) {
						$data['template_categories'][] = array(
							'template_category_id' => $template_category['template_category_id'],
							'code' 					  => $template_category['code'],
							'name' 					  => $template_category['name']
						);
					}
				}
			}

			$json['output'] = $this->load->view('social_autopilot/share_modal', $data);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function preview() {
		$json = array();

		$item_type = isset($this->request->post['item_type']) ? $this->request->post['item_type'] : false;
		$item_id = isset($this->request->post['item_id']) ? $this->request->post['item_id'] : false;
		$scheduled_post_id = isset($this->request->post['scheduled_post_id']) ? $this->request->post['scheduled_post_id'] : false;

		$share_info = array();

		if ($item_type && $item_id) {
			$this->load->model('social_autopilot/' . $item_type);

			$share_info = $this->{'model_social_autopilot_' . $item_type}->getShareInfo($item_id);
		}

		if ($scheduled_post_id) {
			$this->load->model('social_autopilot/scheduled_post');

			$scheduled_post_info = $this->model_social_autopilot_scheduled_post->getScheduledPost($scheduled_post_id);

			if ($scheduled_post_info) {
				$share_info['message'] = html_entity_decode($scheduled_post_info['message'], ENT_QUOTES, 'UTF-8');
				$share_info['schedule_datetime'] = $scheduled_post_info['date_schedule'];

				// Load saved link only for custom post
				if (!$item_type && !$item_id) {
					$share_info['link'] = rawurlencode(html_entity_decode($scheduled_post_info['link'], ENT_QUOTES, 'UTF-8'));
				}
			}
		}

		if ($share_info) {
			$json['share_info'] = $share_info;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	// Check if is required to post something - from events
	public function check() {
		$json = array();

		if (isset($this->session->data['sap_item_type']) && isset($this->session->data['sap_item_id'])) {
			$json['sap_item_type'] = $this->session->data['sap_item_type'];
			$json['sap_item_id'] = $this->session->data['sap_item_id'];
			$json['sap_auto_post'] = ($this->config->get('social_autopilot_autopost') == 'auto') ? true : false;

			unset($this->session->data['sap_item_type']);
			unset($this->session->data['sap_item_id']);

			$json['success'] = true;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
