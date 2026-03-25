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

	public function getTask($task_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_task WHERE task_id = '" . (int)$task_id . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function getTasks($data = array()) {
		$sql = "SELECT st.*, sc.name as channel_name, sc.code as channel_code FROM " . DB_PREFIX . "sap_task st LEFT JOIN " . DB_PREFIX . "sap_channel sc ON (st.channel_id = sc.channel_id) WHERE 1";

		$implode = array();

		if (isset($data['filter_channel_id']) && !is_null($data['filter_channel_id'])) {
			$implode[] = "st.channel_id = '" . (int)$data['filter_channel_id'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			if ($data['filter_status'] == 'in-progress') {
				$implode[] = "st.processed = '0'";
			}

			if ($data['filter_status'] == 'partial-success') {
				$implode[] = "(st.processed = '1' AND (st.success_rate > 0 AND st.success_rate < 100))";
			}

			if ($data['filter_status'] == 'success') {
				$implode[] = "(st.processed = '1' AND st.success_rate = 100)";
			}

			if ($data['filter_status'] == 'failed') {
				$implode[] = "(st.processed = '1' AND st.success_rate = 0)";
			}
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(st.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(st.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'st.task_id',
			'st.processed',
			'st.date_added',
			'st.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY st.task_id";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalTasks($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sap_task st WHERE 1";

		$implode = array();

		if (isset($data['filter_channel_id']) && !is_null($data['filter_channel_id'])) {
			$implode[] = "st.channel_id = '" . (int)$data['filter_channel_id'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			if ($data['filter_status'] == 'in-progress') {
				$implode[] = "st.processed = '0'";
			}

			if ($data['filter_status'] == 'partial-success') {
				$implode[] = "(st.processed = '1' AND (st.success_rate > 0 AND st.success_rate < 100))";
			}

			if ($data['filter_status'] == 'success') {
				$implode[] = "(st.processed = '1' AND st.success_rate = 100)";
			}

			if ($data['filter_status'] == 'failed') {
				$implode[] = "(st.processed = '1' AND st.success_rate = 0)";
			}
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(st.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(st.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTaskChannelPages($task_id) {
		$query = $this->db->query("SELECT scp.page_id, scp.name AS page_name, scp.type AS page_type, scp.extra, sc.code AS channel_code, sc.name AS channel_name, sc.link as channel_link FROM " . DB_PREFIX . "sap_task_to_channel_permission st2cp LEFT JOIN " . DB_PREFIX . "sap_channel_permission scp ON (st2cp.page_id = scp.page_id) LEFT JOIN " . DB_PREFIX . "sap_channel sc ON (scp.channel_id = sc.channel_id) WHERE st2cp.task_id = '" . (int)$task_id . "' ORDER BY scp.name ASC");

		return $query->rows;
	}

	public function deleteTask($task_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_task WHERE task_id = '" . (int)$task_id . "'");
	}
}
