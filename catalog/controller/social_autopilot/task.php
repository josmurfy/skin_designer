<?php
class ControllerSocialAutoPilotTask extends Controller {
	public function index() {
		$json = array();

		if (!$json) {
			if (!isset($this->request->get['secret_key'])) {
				$json['error'] = array('code' => 'error_no_secret_key', 'args' => array());
			}
		}

		if (!$json) {
			if (isset($this->request->get['secret_key']) && $this->request->get['secret_key'] != $this->config->get('social_autopilot_api_key')) {
				$json['error'] = array('code' => 'error_secret_key_mismatch', 'args' => array());
			}
		}

		if (!$json) {
			if (!isset($this->request->get['task_id'])) {
				$json['error'] = array('code' => 'error_no_task_id', 'args' => array());
			}
		}

		if (!$json) {
			$task_id = isset($this->request->get['task_id']) ? $this->request->get['task_id'] : 0;

			if ($task_id) {
				$task_response = $this->request->post;

				if (isset($task_response['success_rate'])) {
					$this->load->model('social_autopilot/task');

					$this->model_social_autopilot_task->setTaskResponse($task_id, $task_response);

					$json['success'] = true;
				}
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>
