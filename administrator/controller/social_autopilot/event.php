<?php
class ControllerSocialAutoPilotEvent extends Controller {
	public function index() {
	}

	public function eventAddProduct($route, $data) {
		$this->eventAddSAPItem('product', $data);
	}

	public function eventEditProduct($route, $data) {
		$this->eventEditSAPItem('product', $data);
	}

	public function eventAddReview($route, $data) {
		$this->eventAddSAPItem('review', $data);
	}

	public function eventEditReview($route, $data) {
		$this->eventEditSAPItem('review', $data);
	}

	public function eventAddCategory($route, $data) {
		$this->eventAddSAPItem('category', $data);
	}

	public function eventEditCategory($route, $data) {
		$this->eventEditSAPItem('category', $data);
	}

	public function eventAddInformation($route, $data) {
		$this->eventAddSAPItem('information', $data);
	}

	public function eventEditInformation($route, $data) {
		$this->eventEditSAPItem('information', $data);
	}

	private function eventEditSAPItem($item_type, $data) {
		$item_id = $data[0];
		$item_info = $data[1];

		$status = in_array($this->config->get('social_autopilot_autopost'), array('ask', 'auto'));

		if ($status) {
			if (!isset($item_info['status']) || !$item_info['status']) {
				$status = false;
			}

			// add here other custom checks

			if ($status) {
				$this->session->data['sap_item_type'] = $item_type;
				$this->session->data['sap_item_id'] = $item_id;
			}
		}
	}

	public function eventAddSAPItem($item_type, $data) {
		$item_info = $data[0];

		$status = in_array($this->config->get('social_autopilot_autopost'), array('ask', 'auto'));

		if ($status) {
			if (!isset($item_info['status']) && !$item_info['status']) {
				$status = false;
			}

			// add here other custom checks

			if ($status) {
				$this->load->model('social_autopilot/' . $item_type);

				$item_id = $this->{'model_social_autopilot_' . $item_type}->getLastId();

				if ($item_id) {
					$this->session->data['sap_item_type'] = $item_type;
					$this->session->data['sap_item_id'] = $item_id;
				}
			}
		}
	}
}
