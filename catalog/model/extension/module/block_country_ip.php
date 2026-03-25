<?php

class ModelExtensionModuleBlockCountryIp extends Model {

	public function addBlockIP($data)
	{
		//echo '<br/>data<pre>'; print_r($data); echo '</pre>';
		$sql ="INSERT INTO " . DB_PREFIX . "blocked_ips SET user_ip = '" . $this->db->escape($data['user_ip']) . "', country_iso_code = '" . $this->db->escape($data['country_iso_code']) . "', country_name = '" . $this->db->escape($data['country_name']) . "', subdivision_name = '" . $this->db->escape($data['subdivision_name']) . "', subdivision_iso_code = '" . $this->db->escape($data['subdivision_iso_code']) . "', city_name = '" . $this->db->escape($data['city_name']) . "', postal_code = '" . $this->db->escape($data['postal_code']) . "', latitude = '" . $this->db->escape($data['latitude']) . "', longitude = '" . $this->db->escape($data['longitude']) . "', access_page = '" . $this->db->escape($data['access_page']) . "', error_msg = '" . $this->db->escape($data['error_msg']) . "'";
		
		//echo '<br/>sql -->'.$sql;
		
		$this->db->query($sql);

		return $this->db->getLastId();
	}
	
	public function isIpBlock($blocked_ip)
	{
		$sql = "SELECT user_ip FROM " . DB_PREFIX . "blocked_ips WHERE user_ip = '" . $this->db->escape($blocked_ip) . "'";
		$query = $this->db->query($sql);
		
		if($query->num_rows)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}
