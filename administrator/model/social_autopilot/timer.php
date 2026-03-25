<?php
class ModelSocialAutoPilotTimer extends Model {
	public function getNow() {
		$query = $this->db->query("SELECT NOW() AS current_datetime");

		return $query->row['current_datetime'];
	}

	public function getRemainingMinutes($schedule_datetime) {
		$query = $this->db->query("SELECT TIMESTAMPDIFF(MINUTE, TIMESTAMPADD(MINUTE, " . (int)$this->config->get('social_autopilot_timezone_difference') . ", NOW()), '" . $this->db->escape($schedule_datetime) . "') AS remaining_minutes");

		return $query->row['remaining_minutes'];
	}
}
