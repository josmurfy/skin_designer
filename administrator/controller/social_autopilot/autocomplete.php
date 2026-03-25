<?php
class ControllerSocialAutoPilotAutocomplete extends Controller {
	public function index() {
		$json = array();

		if (isset($this->request->get['sap_item_type']) || isset($this->request->get['sap_search'])) {
			$item_type = $this->request->get['sap_item_type'];

			$this->load->model('social_autopilot/' . $item_type);

			if (isset($this->request->get['sap_search'])) {
				$filter_search = $this->request->get['sap_search'];
			} else {
				$filter_search = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_search' => $filter_search,
				'start'         => 0,
				'limit'         => $limit
			);

			$results = $this->{'model_social_autopilot_' . $item_type}->getAutocomplete($filter_data);

			foreach ($results as $result) {
				$json[] = array(
					'id'   => $result['id'],
					'name' => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
