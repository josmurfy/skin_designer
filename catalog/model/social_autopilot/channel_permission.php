<?php
class ModelSocialAutoPilotChannelPermission extends Model {
	public function addPermission($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "sap_channel_permission SET channel_id = '" . (int)$data['channel_id'] . "', name = '" . $this->db->escape($data['name']) . "', page_id = '" . $this->db->escape($data['page_id']) . "', access_token = '" . $this->db->escape($data['access_token']) . "', access_token_secret = '" . $this->db->escape($data['access_token_secret']) . "', date_expire = '" . $this->db->escape($data['date_expire']) . "', type = '" . $this->db->escape($data['type']) . "', status = '1', extra = '" . $this->db->escape(serialize($data['extra'])) . "', date_added = NOW()");
	}

	public function editPermission($permission_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_channel_permission SET name = '" . $this->db->escape($data['name']) . "', access_token = '" . $this->db->escape($data['access_token']) . "', access_token_secret = '" . $this->db->escape($data['access_token_secret']) . "', extra = '" . $this->db->escape(serialize($data['extra'])) . "', date_expire = '" . $this->db->escape($data['date_expire']) . "', date_modified = NOW() WHERE permission_id = '" . (int)$permission_id . "'");
	}

	public function deletePermission($permission_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_channel_permission WHERE permission_id = '" . (int)$permission_id . "'");
	}

	public function getPermission($permission_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_channel_permission WHERE permission_id = '" . (int)$permission_id . "'");

		return $query->row;
	}

	public function getPermissionByChannelIdAndPageId($channel_id, $page_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_channel_permission WHERE channel_id = '" . (int)$channel_id . "' AND page_id = '" . $this->db->escape($page_id) . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function getPermissions($data = array()) {
		$sql = "SELECT cp.*, c.name AS channel_name, c.code as channel_code FROM " . DB_PREFIX . "sap_channel_permission cp LEFT JOIN " . DB_PREFIX . "sap_channel c ON (cp.channel_id = c.channel_id) WHERE 1 ";

		$implode = array();

		if (isset($data['filter_channel_id']) && !empty($data['filter_channel_id'])) {
			$implode[] = "cp.channel_id = '" . $this->db->escape($data['filter_channel_id']) . "'";
		}

		if (isset($data['filter_permission_list']) && !empty($data['filter_permission_list'])) {
			$implode[] = " cp.permission_id IN (" . $this->db->escape($data['filter_permission_list']) . ")";
		}

		if (isset($data['filter_pages_list']) && !empty($data['filter_pages_list'])) {
			$implode[] = " cp.page_id IN (" . $this->db->escape($data['filter_pages_list']) . ")";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getPermissionsIdsByScheduledPostId($scheduled_post_id) {
		$permissions_ids = array();

		$query = $this->db->query("SELECT DISTINCT permission_id FROM " . DB_PREFIX . "sap_channel_permission cp LEFT JOIN " . DB_PREFIX . "sap_scheduled_post_to_channel_permission sp2cp ON (cp.page_id = sp2cp.page_id) WHERE sp2cp.scheduled_post_id = '" . (int)$scheduled_post_id . "'");

		if ($query->num_rows) {
			foreach ($query->rows as $permission) {
				$permissions_ids[] = $permission['permission_id'];
			}
		}

		return $permissions_ids;
	}
}
