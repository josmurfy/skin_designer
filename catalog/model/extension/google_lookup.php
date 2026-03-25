<?php
class ModelExtensionGoogleLookup extends Model {
    public function getcountryy($name) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "country WHERE name = '" . $name . "'");
		return $query->row;
	}
	
	public function getzones($name) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone WHERE name = '" . $name . "'");
	
		return $query->row;
	}
}
