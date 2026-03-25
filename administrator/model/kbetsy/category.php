<?php

class ModelKbetsyCategory extends Model
{

    public function getEtsyCategories($parent_id = 0)
    {
        if ($parent_id != 0) {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_categories WHERE category_code = " . $parent_id;
            $query = $this->db->query($sql);
            if ($query->num_rows > 0) {
                $parent_id = $query->row["id_etsy_categories"];
            }
        }
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_categories WHERE parent_id = " . $parent_id;
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

}

?>