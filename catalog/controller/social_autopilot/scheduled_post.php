<?php
class ControllerSocialAutoPilotScheduledPost extends Controller {
	public function index() {
		$log = array();

		$secret_key = (isset($this->request->get['secret_key'])) ? $this->request->get['secret_key'] : '';

		if ($secret_key == $this->config->get('social_autopilot_api_key')) {
			$this->load->model('social_autopilot/scheduled_post');
			$this->load->model('social_autopilot/sender');
			$this->load->model('social_autopilot/task');

			$scheduled_posts = $this->model_social_autopilot_scheduled_post->getScheduledPosts();

			if ($scheduled_posts) {
				foreach ($scheduled_posts as $scheduled_post) {
					$request_uid = $this->model_social_autopilot_scheduled_post->convertScheduledPostInTasks($scheduled_post);

					if ($request_uid) {
						// delete first scheduled post because was already moved in tasks
						$this->model_social_autopilot_scheduled_post->deleteScheduledPost($scheduled_post['scheduled_post_id']);

						$sap_response = $this->model_social_autopilot_sender->send($request_uid);

						if (isset($sap_response['error'])) {
							$this->model_social_autopilot_task->setTaskFailResponse($request_uid, $sap_response);

							$log[$request_uid] = $sap_response['error'];
						}

						if (isset($sap_response['response'])) {
							$sap_response_ok = $sap_response['response'];

							if (isset($sap_response_ok['success'])) {
								$log[$request_uid] = $sap_response_ok;
							}
						}
					}
				}
			}

			print '<pre>'; print_r($log); print '</pre>';
		}
	}
}
?>
