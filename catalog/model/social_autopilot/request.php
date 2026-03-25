<?php
class ModelSocialAutoPilotRequest extends Model {
	// Build SAP request for OCX based on saved tasks
	public function getRequest($request_uid) {
		$sap_request = array();

		$query = $this->db->query("SELECT st.task_id, st.request_uid, st.channel_id, st.message, st.link, st.image, sc.code AS channel_code, sc.name AS channel_name, scp.page_id, scp.name as page_name, scp.access_token, scp.access_token_secret, scp.type as page_type, scp.extra FROM " . DB_PREFIX . "sap_task st LEFT JOIN " . DB_PREFIX . "sap_task_to_channel_permission st2cp ON (st.task_id = st2cp.task_id) LEFT JOIN " . DB_PREFIX . "sap_channel sc ON (st.channel_id = sc.channel_id) LEFT JOIN " . DB_PREFIX . "sap_channel_permission scp ON (st2cp.page_id = scp.page_id) WHERE st.request_uid = '" . $this->db->escape($request_uid) . "'");

		if ($query->num_rows) {
			foreach($query->rows as $request) {
				$callback_url = HTTPS_SERVER . 'index.php?route=social_autopilot/task&secret_key=' . $this->config->get('social_autopilot_api_key') . '&task_id=' . $request['task_id'];

				$sap_request['channels'][$request['channel_code']][] = array(
					'request_uid'   => $request['request_uid'],
					'channel_id'    => $request['channel_id'],
					'channel_code'  => $request['channel_code'],
					'channel_name'  => $request['channel_name'],
					'page_id'       => $request['page_id'],
					'page_name'     => $request['page_name'],
					'access_token'  => $request['access_token'],
					'access_token_secret' => $request['access_token_secret'],
					'page_type'     => $request['page_type'],
					'extra'         => ($request['extra']) ? unserialize($request['extra']) : '',
					'message'       => $request['message'],
					'link'          => html_entity_decode($request['link'], ENT_QUOTES, 'UTF-8'),
					'image'         => html_entity_decode($request['image'], ENT_QUOTES, 'UTF-8'),
					'callback_url'  => $callback_url
				);
			}
		}

		return $sap_request;
	}
}
