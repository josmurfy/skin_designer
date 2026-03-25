<?php
class ModelSocialAutoPilotChannelPermission extends Model {
	public function addPermission($data) {
	}

	public function editPermission($permission_id, $data) {
	}

	public function deletePermission($permission_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_channel_permission WHERE permission_id = '" . (int)$permission_id . "'");
	}

	public function getPermission($permission_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_channel_permission WHERE permission_id = '" . (int)$permission_id . "'");

		return $query->row;
	}

	public function updateStatus($permission_id, $status) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_channel_permission SET status = '" . (int)$status . "' WHERE permission_id = '" . (int)$permission_id . "'");
	}

	public function getPageIdByPermissionId($permission_id) {
		$query = $this->db->query("SELECT page_id FROM " . DB_PREFIX . "sap_channel_permission WHERE permission_id = '" . (int)$permission_id . "'");

		if ($query->num_rows) {
			return $query->row['page_id'];
		} else {
			return false;
		}
	}

	public function getPageIdsByPermissionsIds($permissions_ids) {
		$pages_ids = array();

		$query = $this->db->query("SELECT page_id FROM " . DB_PREFIX . "sap_channel_permission WHERE permission_id IN (" . implode(",", $permissions_ids) . ")");

		if ($query->num_rows) {
			foreach ($query->rows as $result) {
				$pages_ids[] = $result['page_id'];
			}
		}

		return $pages_ids;
	}

	public function getPermissions($data = array()) {
		$sql = "SELECT cp.*, c.name AS channel_name, c.code as channel_code FROM " . DB_PREFIX . "sap_channel_permission cp LEFT JOIN " . DB_PREFIX . "sap_channel c ON (cp.channel_id = c.channel_id) WHERE 1 ";

		$implode = array();

		if (isset($data['filter_channel_id']) && !empty($data['filter_channel_id'])) {
			$implode[] = "cp.channel_id = '" . $this->db->escape($data['filter_channel_id']) . "'";
		}

		if (isset($data['filter_name']) && !empty($data['filter_name'])) {
			$implode[] = "cp.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "cp.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_date_added']) &&  !empty($data['filter_date_added'])) {
			$implode[] = "DATE(cp.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (isset($data['filter_permission_list']) && !empty($data['filter_permission_list'])) {
			$implode[] = " cp.permission_id IN (" . $this->db->escape($data['filter_permission_list']) . ")";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'channel_id',
			'name',
			'status',
			'date_added'
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

	public function getTotalPermissions($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sap_channel_permission cp WHERE 1";

		$implode = array();

		if (!empty($data['filter_channel_id'])) {
			$implode[] = "cp.channel_id = '" . $this->db->escape($data['filter_channel_id']) . "'";
		}

		if (!empty($data['filter_name'])) {
			$implode[] = "cp.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "cp.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(cp.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
