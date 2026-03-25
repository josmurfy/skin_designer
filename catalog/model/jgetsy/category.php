<?php

class ModelJgetsyCategory extends Model {

    public function updateCategories($etsy_category_id, $tag, $category_name = '', $parent_id = '0') {
        if ($etsy_category_id != "") {
            $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_categories WHERE category_code = '" . $etsy_category_id . "'");
            if ($result->num_rows <= 0) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_categories(category_code, tag, category_name, parent_id) VALUES('" . $this->db->escape($etsy_category_id) . "','" . $this->db->escape($tag) . "','" . $this->db->escape($category_name) . "','" . $this->db->escape($parent_id) . "')");
                return $this->db->getLastId();
            } else {
                return $this->db->row['id_etsy_categories'];
            }
        } else {
            return false;
        }
    }

}
