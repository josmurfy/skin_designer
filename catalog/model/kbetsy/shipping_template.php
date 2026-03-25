<?php

class ModelKbetsyShippingTemplate extends Model {

    public function getShippingTemplateById($id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates WHERE id_etsy_shipping_templates = '" . (int) $id . "'";

        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getAllShippingTemplates() {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates";

        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function deleteShippingTemplate($local_template_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_templates_entries WHERE id_etsy_shipping_templates = '" . $local_template_id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_templates WHERE id_etsy_shipping_templates = '" . $local_template_id . "'");
    }

}
