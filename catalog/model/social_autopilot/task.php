<?php
class ModelSocialAutoPilotTask extends Model {
	public function addTask($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "sap_task SET request_uid = '" . $this->db->escape($data['request_uid']) . "', channel_id = '" . (int)$data['channel_id'] . "', message = '" . $this->db->escape($data['message']) . "', link = '" . $this->db->escape(rawurldecode($data['link'])) . "', image = '" . $this->db->escape(rawurldecode($data['image'])) . "', processed = '0', date_added = NOW()");

		$task_id = $this->db->getLastId();

		if ($data['pages_data']) {
			foreach ($data['pages_data'] as $page_info) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "sap_task_to_channel_permission SET task_id = '" . (int)$task_id . "', page_id = '" . $this->db->escape($page_info['id']) . "'");
			}
		}

		return $task_id;
	}

	public function setTaskResponse($task_id, $response) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_task SET processed = '1', response = '" . $this->db->escape(serialize($response)) . "', success_rate = '" . (float)$response['success_rate'] . "' WHERE task_id = '" . (int)$task_id ."'");
	}

	public function setTaskFailResponse($request_uid, $response) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_task SET processed = '1', response = '" . $this->db->escape(serialize($response)) . "' WHERE request_uid = '" . $this->db->escape($request_uid) ."'");
	}
}
