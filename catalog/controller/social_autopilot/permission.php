<?php   
class ControllerSocialAutoPilotPermission extends Controller {
	public function index() {
		$json = array();
		
		if (!$json) {
			if (!isset($this->request->get['secret_key'])) {
				$json['error'] = array('code' => 'error_no_secret_key', 'args' => array());
			}
		}
		
		if (!$json) {
			if (isset($this->request->get['secret_key']) && $this->request->get['secret_key'] != $this->config->get('social_autopilot_api_key')) {
				$json['error'] = array('code' => 'error_secret_key_mismatch', 'args' => array());
			}
		}
		
		// permissions info are sent via POST
		if (!$json) {
			if (!isset($this->request->post['permissions'])) {
				$json['error'] = array('code' => 'error_no_permissions', 'args' => array());
			}
		}
		
		if (!$json) {			
			$this->load->model('social_autopilot/channel_permission');	
				
			foreach ($this->request->post['permissions'] as $permission_info) {
				$old_permission = $this->model_social_autopilot_channel_permission->getPermissionByChannelIdAndPageId($permission_info['channel_id'], $permission_info['page_id']);
				
				if ($old_permission) {
					$this->model_social_autopilot_channel_permission->editPermission($old_permission['permission_id'], $permission_info);
				} else {
					$this->model_social_autopilot_channel_permission->addPermission($permission_info);
				}	
			}
			
			$json['success'] = true;		
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
?>