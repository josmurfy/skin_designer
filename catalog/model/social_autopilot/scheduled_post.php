<?php
class ModelSocialAutoPilotScheduledPost extends Model {
	public function convertScheduledPostInTasks($data) {
		$tasks = array();

		$request_uid = uniqid();

		$channels = $this->getScheduledPostChannels($data['scheduled_post_id']);

		if ($channels) {
			foreach ($channels as $channel) {
				$channel_pages = $this->getScheduledPostChannelPages($data['scheduled_post_id'], $channel['channel_id']);

				$tasks[$channel['channel_id']] = array(
					'request_uid'       => $request_uid,
					'channel_id'        => $channel['channel_id'],
					'pages_data'        => $channel_pages,
					'message'           => $data['message'],
					'link'              => html_entity_decode($data['link'], ENT_QUOTES, 'UTF-8'),
					'image'             => html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8')
				);
			}
		}

		if ($tasks) {
			$this->load->model('social_autopilot/task');

			foreach ($tasks as $task) {
				$this->model_social_autopilot_task->addTask($task);
			}
		}

		return $request_uid;
	}

	public function getScheduledPosts() {
		$query = $this->db->query("SELECT sp.* FROM " . DB_PREFIX . "sap_scheduled_post sp WHERE sp.status = '1' AND sp.date_schedule <= TIMESTAMPADD(MINUTE, " . (int)$this->config->get('social_autopilot_timezone_difference') . ", NOW());");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	public function getScheduledPost($scheduled_post_id) {
		//
	}

	public function getScheduledPostChannels($scheduled_post_id) {
		$query = $this->db->query("SELECT DISTINCT sc.* FROM " . DB_PREFIX . "sap_scheduled_post_to_channel_permission ssp2cp LEFT JOIN " . DB_PREFIX . "sap_channel_permission scp ON (ssp2cp.page_id = scp.page_id) LEFT JOIN " . DB_PREFIX . "sap_channel sc ON (scp.channel_id = sc.channel_id) WHERE ssp2cp.scheduled_post_id = '" . (int)$scheduled_post_id . "'");

		if ($query->num_rows) {
			return $query->rows;
		} else {
			return false;
		}
	}

	public function getScheduledPostChannelPages($scheduled_post_id, $channel_id) {
		$pages_data = array();

		$query = $this->db->query("SELECT DISTINCT scp.* FROM " . DB_PREFIX . "sap_scheduled_post_to_channel_permission ssp2cp LEFT JOIN " . DB_PREFIX . "sap_channel_permission scp ON (ssp2cp.page_id = scp.page_id) WHERE ssp2cp.scheduled_post_id = '" . (int)$scheduled_post_id . "' AND scp.channel_id = '" . (int)$channel_id . "'");

		if ($query->num_rows) {
			foreach ($query->rows as $page_info) {
				$pages_data[] = array(
					'id'   => $page_info['page_id'],
					'name' => $page_info['name'],
				);
			}
		}

		return $pages_data;
	}

	public function setDisabled($scheduled_post_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "sap_scheduled_post SET status = '0' WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");
	}

	public function deleteScheduledPost($scheduled_post_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_scheduled_post WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "sap_scheduled_post_to_channel_permission WHERE scheduled_post_id = '" . (int)$scheduled_post_id . "'");
	}
}
