<?php
class ModelSocialAutoPilotScheduledPost extends Model {
	public function addScheduledPost($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "sap_scheduled_post SET message = '" . $this->db->escape($data['message']) . "', link = '" . $this->db->escape(rawurldecode($data['link'])) . "', image = '" . $this->db->escape(rawurldecode($data['image'])) . "', item_type = '" . $this->db->escape($data['item_type']) . "', item_id = '" . (int)$data['item_id'] . "', status = '1', date_schedule = '" . $this->db->escape($data['schedule_datetime']) . "', date_added = NOW()");

		$scheduled_post_id = $this->db->getLastId();

		if ($data['permission']) {
			$this->load->model('social_autopilot/channel_permission');

			$pages_ids = $this->model_social_autopilot_channel_permission->getPageIdsByPermissionsIds($data['permission']);

			if ($pages_ids) {
				foreach ($pages_ids as $page_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "sap_scheduled_post_to_channel_permission SET scheduled_post_id = '" . (int)$scheduled_post_id . "', page_id = '" . $this->db->escape($page_id) . "'");
				}
			}
		}
	}

	public function editScheduledPost($scheduled_post_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_scheduled_post SET message = '" . $this->db->escape($data['message']) . "', link = '" . $this->db->escape(rawurldecode($data['link'])) . "', image = '" . $this->db->escape(rawurldecode($data['image'])) . "', item_type = '" . $this->db->escape($data['item_type']) . "', item_id = '" . (int)$data['item_id'] . "', status = '1', date_schedule = '" . $this->db->escape($data['schedule_datetime']) . "', date_modified = NOW() WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_scheduled_post_to_channel_permission WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");

		if ($data['permission']) {
			$this->load->model('social_autopilot/channel_permission');

			$pages_ids = $this->model_social_autopilot_channel_permission->getPageIdsByPermissionsIds($data['permission']);

			if ($pages_ids) {
				foreach ($pages_ids as $page_id) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "sap_scheduled_post_to_channel_permission SET scheduled_post_id = '" . (int)$scheduled_post_id . "', page_id = '" . $this->db->escape($page_id) . "'");
				}
			}
		}
	}

	public function deleteScheduledPost($scheduled_post_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_scheduled_post WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_scheduled_post_to_channel_permission WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");
	}

	public function getScheduledPost($scheduled_post_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_scheduled_post WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function getScheduledPostPages($scheduled_post_id) {
		$pages_data = array();

		$query = $this->db->query("SELECT page_id FROM " . DB_PREFIX . "sap_scheduled_post_to_channel_permission WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");

		if ($query->num_rows) {
			foreach ($query->rows as $page_info) {
				$pages_data[] = $page_info['page_id'];
			}
		}

		return $pages_data;
	}

	public function getScheduledPosts($data = array()) {
		$sql = "SELECT sp.* FROM " . DB_PREFIX . "sap_scheduled_post sp WHERE 1";

		$implode = array();

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "sp.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_schedule'])) {
			$implode[] = "DATE(sp.date_schedule) = DATE('" . $this->db->escape($data['filter_date_schedule']) . "')";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(t.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(t.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'sp.status',
			'sp.date_schedule',
			'sp.date_added',
			'sp.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sp.scheduled_post_id";
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

	public function getTotalScheduledPosts($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sap_scheduled_post sp WHERE 1";

		$implode = array();

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "sp.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_schedule'])) {
			$implode[] = "DATE(sp.date_schedule) = DATE('" . $this->db->escape($data['filter_date_schedule']) . "')";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(t.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(t.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function setStatus($scheduled_post_id, $status) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_scheduled_post SET status = '" . (int)$status . "' WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");
	}
}
