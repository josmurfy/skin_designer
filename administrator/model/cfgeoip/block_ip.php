<?php
class ModelCfgeoipBlockIp extends Model {
	
	public function deleteBlockedIp($blocked_ip_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "blocked_ips WHERE id = '" . (int)$blocked_ip_id . "'");
	}
	
	public function getBlockedIp($blocked_ip_id) {
		$sql = "SELECT * FROM " . DB_PREFIX . "blocked_ips WHERE id = '" . (int)$blocked_ip_id . "'";
		
		$query = $this->db->query($sql);
		if ($query->num_rows) 
		{
			//echo '<pre>'; print_r($query->row); echo '</pre>';exit;
			return array(
			'user_ip'	=> $query->row['user_ip'],
			'country_iso_code'	=> $query->row['country_iso_code'],
			'country_name'	=> $query->row['country_name'],
			'subdivision_name'	=> $query->row['subdivision_name'],
			'subdivision_iso_code'	=> $query->row['subdivision_iso_code'],
			'city_name'	=> $query->row['city_name'],
			'postal_code'	=> $query->row['postal_code'],
			'latitude'	=> $query->row['latitude'],
			'longitude'	=> $query->row['longitude'],
			'access_page'	=> $query->row['access_page'],
			'error_msg'	=> $query->row['error_msg'],
			'access_date'	=> date($this->language->get('datetime_format'), strtotime($query->row['access_date'])),
			);
		}
		else
		{
			
		}
	}


	public function getBlockedIps($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "blocked_ips ";
		
		$implode = array();

		if (!empty($data['filter_user_ip'])) {
			$implode[] = "user_ip LIKE '%" . $this->db->escape($data['filter_user_ip']) . "%'";
		}

		if (!empty($data['filter_country_iso_code'])) {
			$implode[] = "country_iso_code LIKE '" . $this->db->escape($data['filter_country_iso_code']) . "'";
		}

		if (!empty($data['filter_country_name'])) {
			$implode[] = "country_name LIKE '%" . $this->db->escape($data['filter_country_name']) . "%'";
		}

		if (!empty($data['filter_subdivision_name'])) {
			$implode[] = "subdivision_name LIKE '%" . $this->db->escape($data['filter_subdivision_name']) . "%'";
		}

		if (!empty($data['filter_subdivision_iso_code'])) {
			$implode[] = "subdivision_iso_code LIKE '%" . $this->db->escape($data['filter_subdivision_iso_code']) . "%'";
		}

		if (!empty($data['filter_access_date'])) {
			$implode[] = "DATE(access_date) = DATE('" . $this->db->escape($data['filter_access_date']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$sort_data = array(
			'user_ip',
			'country_iso_code',
			'country_name',
			'subdivision_name',
			'subdivision_iso_code',
			'access_date'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY access_date";
		}

		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
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

	public function getTotalBlockedIp($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blocked_ips";

		$implode = array();

		if (!empty($data['filter_user_ip'])) {
			$implode[] = "user_ip LIKE '%" . $this->db->escape($data['filter_user_ip']) . "%'";
		}

		if (!empty($data['filter_country_iso_code'])) {
			$implode[] = "country_iso_code LIKE '" . $this->db->escape($data['filter_country_iso_code']) . "'";
		}

		if (!empty($data['filter_country_name'])) {
			$implode[] = "country_name LIKE '%" . $this->db->escape($data['filter_country_name']) . "%'";
		}

		if (!empty($data['filter_subdivision_name'])) {
			$implode[] = "subdivision_name LIKE '%" . $this->db->escape($data['filter_subdivision_name']) . "%'";
		}

		if (!empty($data['filter_subdivision_iso_code'])) {
			$implode[] = "subdivision_iso_code LIKE '%" . $this->db->escape($data['filter_subdivision_iso_code']) . "%'";
		}

		if (!empty($data['filter_access_date'])) {
			$implode[] = "DATE(access_date) = DATE('" . $this->db->escape($data['filter_access_date']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		
		$query = $this->db->query($sql);

		return $query->row['total'];
	}

}
