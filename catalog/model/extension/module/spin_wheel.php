<?php
class ModelExtensionModuleSpinWheel extends Model {
	public function getWheelOffers(){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "spin_wheel_offer` ORDER BY offer_id ASC");
		return $query->rows;
	}
	

	public function getWheelOffer($offer_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "spin_wheel_offer` WHERE `offer_id` = '" . (int)$offer_id . "'");
		return $query->row;
	}
	
	public function getWheelOfferByGravity(){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "spin_wheel_offer` WHERE `gravity` > 0 ");
		$wheel_offer_data = array();
		if($query->num_rows){
			
			foreach($query->rows as $row){
				$wheel_offer_data[$row['offer_id']]	 =  $row['label'];
			}
		}
		return $wheel_offer_data;	
	}
	
	public function getEmailExist($email){
		$query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "spin_wheel_form` WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) ."'");
		
		if($query->row['total'] > 0){
			return true;
		}else{
			return false;			
		}
	}	public function getIPSameDay($ip){		$query = $this->db->query("SELECT COUNT(*) as total FROM `" . DB_PREFIX . "spin_wheel_form` WHERE bip = '" . $ip ."'");				if($query->row['total'] > 0){			return true;		}else{			return false;					}	}
	
	public function addWheelForm($offer_id, $data){
		
		$config = $this->config->get('spin_wheel');
		$resultData = array();
		
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "spin_wheel_offer` WHERE `offer_id` = '" . (int)$offer_id . "'");
		if($query->num_rows > 0){
			$DiscountCode			= $this->generateUniqueCode();
			$TimeEnd				=  time() + 1 * 24 * 60 * 60;
			if($query->row['type'] == 1){
				$TYPE = 'F';
			}elseif($query->row['type'] == 2){
				$TYPE = 'P';				
			}else{
				$TYPE = 'F';
			}
			
			$CouponData	= array(
				'name'	=>	$query->row['label'],
				'code'	=> $DiscountCode,
				'discount'	=> $query->row['discount'],
				'type'	=> $TYPE,
				/* VERSION 1.0.1 */
				'total'	=> $query->row['total'],
				/* VERSION 1.0.1 */
				'logged'				=> '0',
				'shipping'				=> ($query->row['type'] == 3) ? 1 : 0,				
				'date_start'			=> date('Y-m-d', time()),
				'date_end'				=> date('Y-m-d', $TimeEnd),
				'uses_total'			=> '1',
				'uses_customer'			=> '1',
				'status'			=> '1',
			);
			$coupon_id = "";
			if($query->row['discount'] > 1){
				$coupon_id = $this->addCoupon($CouponData);
			}
			
			//if($coupon_id){
				
				$ip_address = $this->request->server['REMOTE_ADDR'];
				
				if (isset($this->request->server['HTTP_USER_AGENT'])) {
					$user_agent = $this->request->server['HTTP_USER_AGENT'];
				} else {
					$user_agent = '';
				}				
				
				if (!$this->customer->isLogged()) {
					$xml = simplexml_load_file("http://ip-api.com/xml/".$ip_address);
					$country = 	$xml->country;
					$customer_id = 0;
				}else{
					$customer_id = $this->customer->getId();
					$this->load->model('account/customer');
					$customer_info = $this->model_account_customer->getCustomer($customer_id);
					
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . $customer_info['country_id'] . "'");
					$country = $query->row['name'];
				}
				
	
				$this->db->query("INSERT INTO `" . DB_PREFIX . "spin_wheel_form` SET `coupon_id` = '" . (($coupon_id) ? $coupon_id : 0) . "', `customer_id` = '" . $customer_id . "', `firstname`= '" . $data['firstname'] ."', `lastname`= '" . $data['lastname'] ."', `email`= '" . $data['email'] ."', `country` = '" . $country . "', `ip` = '" . $ip_address . "', `user_agent` = '" . $user_agent . "', date_added = NOW()");				
					
			//}	
			return 	$CouponData;			
		}else{
			
		}

	}
	
	public function addCoupon($data){
		$this->db->query("INSERT INTO " . DB_PREFIX . "coupon SET name = '" . $this->db->escape($data['name']) . "', code = '" . $this->db->escape($data['code']) . "', discount = '" . (float)$data['discount'] . "', type = '" . $this->db->escape($data['type']) . "', total = '" . (float)$data['total'] . "', logged = '" . (int)$data['logged'] . "', shipping = '" . (int)$data['shipping'] . "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', uses_total = '" . (int)$data['uses_total'] . "', uses_customer = '" . (int)$data['uses_customer'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");	

		$coupon_id = $this->db->getLastId();
		
		return $coupon_id;
	}
	

	
	public function generateUniqueCode() {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$couponCode = '';
		for ($i = 0; $i < 10; $i++) {	
			$couponCode .= $characters[rand(0, strlen($characters) - 1)]; 
		}
		if($this->isUniqueCode($couponCode)) {	
			return $couponCode;
		} else {	
			return $this->generateUniqueCode();
		}
	}
	
	public function isUniqueCode($randomCode) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "coupon` WHERE code='".$this->db->escape($randomCode)."'");
		
		if($query->num_rows == 0) {
			return true;
		} else {
			return false;
		}	
	}	
}