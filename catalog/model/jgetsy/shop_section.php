<?php

class ModelJgetsyShopSection extends Model {

    public function getShopSectionById($id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE shop_section_id = '" . (int) $id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getAllShopSection() {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shop_sections";

        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function deleteShopSection($local_shop_section_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shop_sections WHERE shop_section_id = '" . $local_shop_section_id . "'");
    }

    public function getShopSectionToAdd() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE etsy_shop_section_id IS NULL");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function getShopSectionToRenew() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE etsy_shop_section_id IS NOT NULL AND etsy_shop_section_id != '' AND etsy_shop_section_id != 0 AND renew_flag = '1' AND delete_flag = '0'");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function getShopSectionToDelete() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE etsy_shop_section_id IS NOT NULL AND etsy_shop_section_id != '' AND etsy_shop_section_id != 0 AND delete_flag = '1'");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function updateShopSection($shop_section_id, $etsy_shop_section_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "etsy_shop_sections SET renew_flag = '0', etsy_shop_section_id = '" . $etsy_shop_section_id . "' WHERE shop_section_id = '" . (int) $shop_section_id . "'");
    }

}
