<?php 
/***********************
// @category  : OpenCart
// @module    : Spin Wheel Popup
// @author    : OpencartMarketplace <support@opencartmarketplace.com>
***********************/

class ModelExtensionModuleSpinWheel extends Model {
  	public function install() {

		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "spin_wheel_offer` (
			  `offer_id` int(11) NOT NULL AUTO_INCREMENT,
			  `label` varchar(32) NOT NULL,
			  `type` tinyint(1) NOT NULL,
			  `discount` decimal(15,4) NOT NULL,
			  `gravity` int(11) NOT NULL,
			  `date_added` date NOT NULL,
			  `date_modified` date NOT NULL,
			  PRIMARY KEY (`offer_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
			 
		//Version 1.0.1
		$this->db->query("ALTER TABLE `" . DB_PREFIX . "spin_wheel_offer` ADD `total` FLOAT(15,4) NOT NULL AFTER `discount`;");
			 
		$this->db->query("INSERT INTO `" . DB_PREFIX . "spin_wheel_offer` (`offer_id`, `label`, `type`, `discount`, `total`, `gravity`, `date_added`, `date_modified`) VALUES
			(1, '10% Discount', 2, '10.0000', 0.1, 1, '0000-00-00', '2018-09-13'),
			(2, 'Sorry! No luck', 1, '0.0000', 0.1, 1, '0000-00-00', '2018-09-13'),
			(3, 'Free Shipping', 3, '0.0000', 0.1, 5, '0000-00-00', '2018-09-13'),
			(4, '35% OFF', 2, '35.0000', 0.1, 0, '0000-00-00', '2018-09-13'),
			(5, 'Opps! no luck today', 1, 0.0000, 0.1, 1, '0000-00-00', '2018-09-13'),
			(6, '$10 OFF', 1, '10.0000', 0.1, 1, '0000-00-00', '2018-09-13'),
			(7, '100% Cashback', 2, '100.0000', 0.1, 0, '0000-00-00', '2018-09-13'),
			(8, 'Next time', 1, '0.0000', 0.1, 7, '0000-00-00', '2018-09-13'),
			(9, '80% OFF', 2, '80.0000', 0.1, 0, '0000-00-00', '2018-09-13'),
			(10, 'Free Shipping', 3, '12.0000', 0.1, 5, '0000-00-00', '2018-09-13'),
			(11, 'No Luck Today', 1, '0.0000', 0.1, 2, '0000-00-00', '2018-09-13'),
			(12, '15% OFF', 2, '15.0000', 0.1, 2, '0000-00-00', '2018-09-13');
		");
		
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "spin_wheel_form` (
			  `spin_form_id` int(11) NOT NULL AUTO_INCREMENT,
			  `coupon_id` int(11) NOT NULL,
			  `customer_id` int(11) NOT NULL,
			  `firstname` varchar(32) NOT NULL,
			  `lastname` varchar(32) NOT NULL,
			  `email` varchar(64) NOT NULL,
			  `country` varchar(130) NOT NULL,
			  `ip` varchar(40) NOT NULL,
			  `user_agent` varchar(255) NOT NULL,
			  `date_added` date NOT NULL,
			  PRIMARY KEY (`spin_form_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");			 
		
			$language_id = $this->config->get('config_language_id');

	} 
  	public function uninstall() {

		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "spin_wheel_offer`");
		$this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "spin_wheel_form`");
		
		$this->load->model('setting/setting');
		$this->model_setting_setting->deleteSetting('spin_wheel');
  	} 
	
	public function addWheelCopuon($data){ 

		$query =  $this->db->query("INSERT INTO `" . DB_PREFIX . "redirect_url` SET old_url = '" . $this->db->escape($data['redirect']['old_url']) ."', new_url = '" . $this->db->escape($data['redirect']['new_url']) ."', type= '". $data['redirect']['type'] ."', store_id = '". $store_id ."', date_added = NOW()");
	}
	
	public function editWheelCopuon($offer_id, $data){
		
		$query =  $this->db->query("UPDATE `" . DB_PREFIX . "spin_wheel_offer` SET `label` = '" . $data['label'] ."', `type` = '". $data['type']."', `discount` = '". $data['discount']."', `total` = '" . $data['total'] . "',`gravity` = '". $data['gravity']."', `date_modified` = NOW() WHERE offer_id = '" . (int)$offer_id. "'");
	}	
	

	public function detaleByID($url_id){
		$query =  $this->db->query("DELETE FROM `" . DB_PREFIX . "redirect_url` WHERE url_id = '" . (int)$url_id . "'");
	}		
	
	public function getWheelCopuon($offer_id){
		$query =  $this->db->query("SELECT * from `" . DB_PREFIX . "spin_wheel_offer` WHERE offer_id = '" . $offer_id .  "'");
		return $query->row;
	}

	public function getWheelCopuons(){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "spin_wheel_offer` ORDER BY offer_id ASC LIMIT 12");
		return $query->rows;
	}
	public function getWheelForms(){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "spin_wheel_form` ORDER BY spin_form_id DESC");
		return $query->rows;
	}	
	
}
?>