<?php
class ModelSocialAutoPilotChannel extends Model {
	public function addChannel($data) {
	}

	public function editChannel($channel_id, $data) {
	}

	public function deleteChannel($channel_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_channel WHERE channel_id = '" . (int)$channel_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_channel_permission WHERE channel_id = '" . (int)$channel_id . "'");
	}

	public function getChannel($channel_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_channel WHERE channel_id = '" . (int)$channel_id . "'");

		return $query->row;
	}

	public function getChannelByCode($code) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_channel WHERE LCASE(code) = '" . $this->db->escape(utf8_strtolower($code)) . "'");

		return $query->row;
	}

	public function getChannelsByPermissionsIds($permissions_ids = array()) {
		$channels_data = array();

		if ($permissions_ids) {
			$query = $this->db->query("SELECT DISTINCT c.* FROM " . DB_PREFIX . "sap_channel c LEFT JOIN " . DB_PREFIX . "sap_channel_permission cp ON (c.channel_id = cp.channel_id) WHERE cp.permission_id IN (" . implode(",", $permissions_ids) . ") ORDER BY c.channel_id");

			if ($query->num_rows) {
				$this->load->model('social_autopilot/channel_setting');

				foreach ($query->rows as $channel_info) {
					$channels_data[] = array(
						'channel_id' => $channel_info['channel_id'],
						'code' 		 => $channel_info['code'],
						'name' 	    => $channel_info['name'],
						'setting'    => $this->model_social_autopilot_channel_setting->getSetting($channel_info['channel_id'])
					);
				}
			}
		}

		return $channels_data;
	}

	public function getChannels($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "sap_channel WHERE 1 ";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['code'])) {
			$implode[] = "code LIKE '" . $this->db->escape($data['filter_code']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'status'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY channel_id";
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

	public function getTotalChannels($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sap_channel WHERE 1";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_code'])) {
			$implode[] = "code LIKE '" . $this->db->escape($data['filter_code']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
