<?php

class ModelKbetsyShippingProfile extends Model {

    public function getShippingProfileById($id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE id_etsy_shipping_profiles = '" . (int) $id . "'";

        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getAllShippingProfiles() {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_profiles";

        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function deleteShippingProfile($local_profile_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_profiles_entries WHERE id_etsy_shipping_profiles = '" . $local_profile_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_profiles WHERE id_etsy_shipping_profiles = '" . $local_profile_id . "'");
    }

}
