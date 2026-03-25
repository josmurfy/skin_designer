<?php
class ModelSocialAutoPilotChannelSetting extends Model {
	public function getSetting($channel_id) {
		$setting_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "sap_channel_setting WHERE channel_id = '" . (int)$channel_id . "'");

		foreach ($query->rows as $result) {
			$setting_data[$result['key']] = $result['value'];
		}

		return $setting_data;
	}

	public function editSetting($channel_id, $data) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "sap_channel_setting` WHERE channel_id = '" . (int)$channel_id . "'");

		foreach ($data as $key => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "sap_channel_setting SET channel_id = '" . (int)$channel_id . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
		}
	}

	public function deleteSetting($channel_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_channel_setting WHERE channel_id = '" . (int)$channel_id . "'");
	}

	public function getSettingValue($channel_id, $key) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "sap_channel_setting WHERE channel_id = '" . (int)$channel_id . "' AND `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;
		}
	}

	public function editSettingValue($channel_id, $key = '', $value = '') {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_channel_setting SET `value` = '" . $this->db->escape($value) . "' WHERE channel_id = '" . (int)$channel_id . "' AND `key` = '" . $this->db->escape($key) . "'");
	}
}
