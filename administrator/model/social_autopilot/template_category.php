<?php
class ModelSocialAutoPilotTemplateCategory extends Model {
	public function addTemplateCategory($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "sap_template_category SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
	}

	public function editTemplateCategory($template_category_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_template_category SET code = '" . $this->db->escape($data['code']) . "', name = '" . $this->db->escape($data['name']) . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE template_category_id = '" . (int)$template_category_id . "'");
	}

	public function deleteTemplateCategory($template_category_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "template_category_id WHERE template_category_id = '" . (int)$template_category_id . "'");
	}

	public function getTemplateCategory($template_category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "template_category WHERE template_category_id = '" . (int)$template_category_id . "'");

		return $query->row;
	}

	public function getTemplateCategories($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "sap_template_category";

		$sort_data = array(
			'template_category_id',
			'code',
			'date_added',
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY template_category_id";
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
}	
