<?php
namespace Opencart\Admin\Model\Shopmanager;

class Manufacturer extends \Opencart\System\Engine\Model {

	public function addManufacturer($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)($data['sort_order'] ?? 0) . "'");

		$manufacturer_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

if (isset($data['keyword']) && $data['keyword']) {
		$language_id = $this->config->get('config_language_id');
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "seo_url 
			SET store_id = 0,
			    language_id = '" . (int)$language_id . "',
			    `key` = 'manufacturer_id',
			    `value` = '" . (int)$manufacturer_id . "',
			    keyword = '" . $this->db->escape($data['keyword']) . "',
			    query = 'manufacturer_id=" . (int)$manufacturer_id . "',
			    sort_order = 0
		");
		}

		$this->cache->delete('manufacturer');

		return $manufacturer_id;
	}

	public function editManufacturer($manufacturer_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

$language_id = $this->config->get('config_language_id');
	$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `key` = 'manufacturer_id' AND `value` = '" . (int)$manufacturer_id . "' AND language_id = '" . (int)$language_id . "'");

	if ($data['keyword']) {
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "seo_url 
			SET store_id = 0,
			    language_id = '" . (int)$language_id . "',
			    `key` = 'manufacturer_id',
			    `value` = '" . (int)$manufacturer_id . "',
			    keyword = '" . $this->db->escape($data['keyword']) . "',
			    query = 'manufacturer_id=" . (int)$manufacturer_id . "',
			    sort_order = 0
		");
		}

		$this->cache->delete('manufacturer');
	}

	public function deleteManufacturer($manufacturer_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
	$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE `key` = 'manufacturer_id' AND `value` = '" . (int)$manufacturer_id . "'");
	}

	public function getManufacturer($manufacturer_id) {
		$language_id = $this->config->get('config_language_id');
	$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE `key` = 'manufacturer_id' AND `value` = '" . (int)$manufacturer_id . "' AND language_id = '" . (int)$language_id . "' LIMIT 1) AS keyword FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row;
	}

	public function getManufacturerByName($name) {
		// Effectuer la requête en échappant les caractères spéciaux dans le nom
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "manufacturer WHERE name = '" . $this->db->escape($name) . "'");
	
		// Si une ligne est trouvée, on la retourne, sinon on retourne null
		if ($query->num_rows > 0) {
			return $query->row;
		} else {
			return null;
		}
	}

	public function getManufacturerByID($name) {
		// Effectuer la requête en échappant les caractères spéciaux dans le nom
		$query = $this->db->query("SELECT DISTINCT manufacturer_id  FROM " . DB_PREFIX . "manufacturer WHERE name = '" . $this->db->escape($name) . "'");
	
		// Si une ligne est trouvée, on la retourne, sinon on retourne null
		if ($query->num_rows > 0) {
			return $query->row['manufacturer_id'];
		} else {
			return null;
		}
	}

	public function getManufacturers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
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

	public function getManufacturerStores($manufacturer_id) {
		$manufacturer_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_to_store WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		foreach ($query->rows as $result) {
			$manufacturer_store_data[] = $result['store_id'];
		}

		return $manufacturer_store_data;
	}

	public function getTotalManufacturers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manufacturer");

		return $query->row['total'];
	}
}
