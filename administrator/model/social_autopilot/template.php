<?php
class ModelSocialAutoPilotTemplate extends Model {
	public function addTemplate($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "sap_template SET template_category_id = '" . (int)$data['template_category_id'] . "', name = '" . $this->db->escape($data['name']) . "', message = '" . $data['message'] . "', status = '" . (int)$data['status'] . "', `default` = '" . (int)$data['default'] . "', date_added = NOW()");

		$template_id = $this->db->getLastId();

		// id default is set to YES then make this template default for category and remove default option for old template
		if ($data['default']) {
			$this->db->query("UPDATE " . DB_PREFIX . "sap_template SET `default` = '0' WHERE template_category_id = '" . (int)$data['template_category_id'] . "' AND template_id != '" . (int)$template_id . "'");
		}
	}

	public function editTemplate($template_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_template SET template_category_id = '" . (int)$data['template_category_id'] . "', name = '" . $this->db->escape($data['name']) . "', message = '" . $data['message'] . "', status = '" . (int)$data['status'] . "', `default` = '" . (int)$data['default'] . "', date_modified = NOW() WHERE template_id = '" . (int)$template_id . "'");

		if ($data['default']) {
			$this->db->query("UPDATE " . DB_PREFIX . "sap_template SET `default` = '0' WHERE template_category_id = '" . (int)$data['template_category_id'] . "' AND template_id != '" . (int)$template_id . "'");
		}
	}

	public function deleteTemplate($template_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_template WHERE template_id = '" . (int)$template_id . "'");
	}

	public function getTemplate($template_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_template WHERE template_id = '" . (int)$template_id . "'");

		return $query->row;
	}

	public function getDefaultTemplateByCategoryCode($category_code) {
		$query = $this->db->query("SELECT t.* FROM " . DB_PREFIX . "sap_template t LEFT JOIN " . DB_PREFIX . "sap_template_category tc ON (t.template_category_id = tc.template_category_id) WHERE t.status = 1 AND t.default = '1' AND tc.code = '" . $this->db->escape($category_code) . "'");

		if ($query->num_rows) {
			return $query->row;
		} else {
			return false;
		}
	}

	public function getDefaultTemplateIdByCategoryId($category_id) {
		$query = $this->db->query("SELECT template_id FROM " . DB_PREFIX . "sap_template WHERE status = 1 AND `default` = '1' AND template_category_id = '" . (int)$category_id . "'");

		if ($query->num_rows) {
			return $query->row['template_id'];
		} else {
			return 0;
		}
	}

	public function isDefault($template_id) {
		$query = $this->db->query("SELECT `default` FROM " . DB_PREFIX . "sap_template WHERE template_id = '" . (int)$template_id . "'");

		if ($query->num_rows) {
			return $query->row['default'];
		} else {
			return false;
		}
	}

	public function getTemplates($data = array()) {
		$sql = "SELECT t.*, tc.name as category_name, tc.code as category_code FROM " . DB_PREFIX . "sap_template t LEFT JOIN " . DB_PREFIX . "sap_template_category tc ON (t.template_category_id = tc.template_category_id)  WHERE 1";

		$implode = array();

		if (!empty($data['filter_template_category_id'])) {
			$implode[] = "t.template_category_id = '" . (int)$data['filter_template_category_id'] . "'";
		}

		if (!empty($data['filter_name'])) {
			$implode[] = "t.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "t.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_default']) && !is_null($data['filter_default'])) {
			$implode[] = "t.default = '" . (int)$data['filter_default'] . "'";
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
			't.name',
			't.template_category_id',
			't.status',
			't.default',
			't.date_added',
			't.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY template_id";
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

	public function getTotalTemplates($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sap_template t LEFT JOIN " . DB_PREFIX . "sap_template_category tc ON (t.template_category_id = tc.template_category_id) WHERE 1";

		$implode = array();

		if (!empty($data['filter_template_category_id'])) {
			$implode[] = "t.template_category_id = '" . (int)$data['filter_template_category_id'] . "'";
		}

		if (!empty($data['filter_name'])) {
			$implode[] = "t.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "t.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_default']) && !is_null($data['filter_default'])) {
			$implode[] = "t.default = '" . (int)$data['filter_default'] . "'";
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

	public function getTotalTemplatesByCategoryId($category_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "sap_template WHERE template_category_id = '" . (int)$category_id . "'");

		return $query->row['total'];
	}
}
