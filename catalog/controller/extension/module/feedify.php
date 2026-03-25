<?php  
class ControllerExtensionmodulefeedify extends Controller {
	public function index() {
		$data['license_code'] = $this->config->get('license_code');
		if(isset($GLOBALS['order_id'])){
			$data['order_id'] = $GLOBALS['order_id'];
			$query = $this->db->query("SELECT * FROM `".DB_PREFIX."order` WHERE `order_id` = '".$data['order_id']."'");
			$data['firstname'] = isset($query->row['payment_firstname']) ? $query->row['payment_firstname'] : '';
			$data['lastname'] = isset($query->row['payment_lastname']) ? $query->row['payment_lastname'] : '';
			$data['email'] = isset($query->row['email']) ? $query->row['email'] : '';
			unset($GLOBALS['guest']);
			unset($GLOBALS['order_id']);
		}	
		return $this->load->view('extension/module/feedify', $data);
	}

	function sendFeedbackMail($order_status_id = '', $api_key){
		if($order_status_id){
			$sql = "SELECT `order_id` FROM `" . DB_PREFIX . "order` WHERE `order_status_id` = $order_status_id";
			$query = $this->db->query($sql);
			if ($query->num_rows) {
				$this->load->library('feedify');
				$this->feedify = new Feedify($this->registry);
				foreach ($query->rows as $row) {
					$order_id = $row['order_id'];
					$this->feedify->sendFeedbackMail($order_id,  $api_key, FALSE);
				}
			}
		}
	}
}
?>
