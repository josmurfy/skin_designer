<?php

class ModelJgetsyProfile extends Model {

    public function getActiveEtsyProfiles() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_profiles WHERE active = '1'");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function getEtsyProfileDetails($id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_profiles WHERE id_etsy_profiles = '" . (int) $id . "'");

        if ($query->num_rows) {
            return $query->row;
        } else {
            return 0;
        }
    }

    public function getEtsyProfileByProduct($product_id) {
        $query = $this->db->query("SELECT ep.* FROM " . DB_PREFIX . "etsy_products_list pl "
                . "INNER JOIN " . DB_PREFIX . "etsy_profiles ep on pl.id_etsy_profiles = ep.id_etsy_profiles "
                . "WHERE id_product = '" . (int) $product_id . "'");

        if ($query->num_rows) {
            return $query->row;
        } else {
            return 0;
        }
    }

}
