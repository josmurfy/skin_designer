<?php
class ModelSocialAutoPilotCronJob extends Model {
	public function addCronJob($data) {

	}

	public function editCronJob($cron_job_id, $data) {

	}

	public function deleteCronJob($cron_job_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_cron_job WHERE cron_job_id = '" . (int)$cron_job_id . "'");
	}

	public function getCronJob($cron_job_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_cron_job WHERE cron_job_id = '" . (int)$cron_job_id . "'");

		return $query->row;
	}

	public function getCronJobs($data = array()) {
		$sql = "SELECT cj.* FROM " . DB_PREFIX . "sap_cron_job cj WHERE 1";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "cj.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "cj.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(cj.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(cj.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'cj.name',
			'cj.status',
			'cj.date_added',
			'cj.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cron_job_id";
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

	public function getTotalCronJobs($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sap_cron_job cj WHERE 1";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "cj.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "cj.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(cj.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(cj.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
}
