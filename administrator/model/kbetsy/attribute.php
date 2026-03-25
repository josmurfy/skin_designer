<?php

class ModelKbetsyAttribute extends Model
{

    public function getEtsyOCOptionsMapping()
    {
        $query = $this->db->query("SELECT DISTINCT(po.option_id) FROM `" . DB_PREFIX . "product_option` po INNER JOIN `" . DB_PREFIX . "option` o ON o.option_id = po.`option_id` WHERE o.type IN ('select', 'radio') ORDER BY po.`option_id` ASC");
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function getOptionsName($data)
    {
        $query = $this->db->query("SELECT *, od.option_id as oc_option_id  FROM `" . DB_PREFIX . "option_description` od LEFT JOIN `" . DB_PREFIX . "etsy_attribute_mapping` eam ON od.option_id = eam.`option_id` WHERE od.option_id IN (" . implode(",", $data) . ") AND language_id = '" . (int) $this->config->get('config_language_id') . "'");
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function getEtsyOptions()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "etsy_attributes`");
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function getEtsyOptionDetails($etsy_option_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "etsy_attributes` WHERE etsy_property_id = '" . $etsy_option_id . "'");
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function saveMapping($etsy_option_id, $oc_option_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "etsy_attribute_mapping` WHERE option_id = '" . $oc_option_id . "'");
        $etsy_option_name = "";
        $etsyOptionDetails = $this->getEtsyOptionDetails($etsy_option_id);
        if ($etsyOptionDetails) {
            $etsy_option_name = $etsyOptionDetails['etsy_property_title'];
        }
        if ($query->num_rows > 0) {
            $this->db->query("UPDATE `" . DB_PREFIX . "etsy_attribute_mapping` SET "
                    . "property_id = '" . $etsy_option_id . "',"
                    . "property_title = '" . $etsy_option_name . "',"
                    . "option_id = '" . $oc_option_id . "',"
                    . "id_etsy_profiles = '',"
                    . "id_attribute_group = '',"
                    . "date_updated = '" . date("Y-m-d H:i:s") . "'"
                    . "WHERE option_id = '" . $oc_option_id . "'");
        } else {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "etsy_attribute_mapping` SET "
                    . "property_id = '" . $etsy_option_id . "',"
                    . "property_title = '" . $etsy_option_name . "',"
                    . "option_id = '" . $oc_option_id . "',"
                    . "id_etsy_profiles = '',"
                    . "id_attribute_group = '',"
                    . "date_added = '" . date("Y-m-d H:i:s") . "',"
                    . "date_updated = '" . date("Y-m-d H:i:s") . "'");
        }
    }

}
