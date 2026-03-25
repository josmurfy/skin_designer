<?php

class ModelKbetsyShopSection extends Model
{

    public function getShopSectionTotal($data)
    {
        $sql = "SELECT count(shop_section_id) as total FROM " . DB_PREFIX . "etsy_shop_sections WHERE shop_section_id > 0 ";

        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getShopSections($data, $shop_section_id = '')
    {
        if (!empty($shop_section_id)) {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE shop_section_id = '" . (int) $shop_section_id . "'";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shop_sections where shop_section_id > 0 ";
        }

        $sort_data = array(
            'title',
            'etsy_shop_section_id'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY title";
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

            $sql .= " LIMIT " . (int) $data['start'] . "," . (int) $data['limit'];
        }

        $query = $this->db->query($sql);

        if (!empty($shop_section_id)) {
            return $query->row;
        } else {
            return $query->rows;
        }
    }

    public function checkSectionExist($title, $shop_section_id = "")
    {
        if (!empty($shop_section_id)) {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE title = '" . $this->db->escape($title) . "' AND shop_section_id = '" . (int) $shop_section_id . "'";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE title = '" . $this->db->escape($title) . "'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function shopSectionUpdate($data)
    {
        if (isset($data['shop_section_id']) && $data['shop_section_id'] != "") {
            $this->db->query("UPDATE " . DB_PREFIX . "etsy_shop_sections SET "
                    . "title = '" . $this->db->escape($data['etsy']['shop_section']['title']) . "',"
                    . "shop_id = '" . $data['etsy']['shop_section']['shop_id'] . "',"
                    . "renew_flag = '1' "
                    . "WHERE shop_section_id = " . $data['shop_section_id']);
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_shop_sections SET "
                    . "title = '" . $this->db->escape($data['etsy']['shop_section']['title']) . "', "
                    . "shop_id = '" . $data['etsy']['shop_section']['shop_id'] . "',"
                    . "etsy_shop_section_id = NULL");
        }
    }

    public function deleteShopSection($shop_section_id)
    {
        $checkShopSectionSQL = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shop_sections WHERE shop_section_id = '" . (int) $shop_section_id . "'");
        if ($checkShopSectionSQL->num_rows > 0) {
            if ($checkShopSectionSQL->row['etsy_shop_section_id'] == NULL) {
                $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shop_sections WHERE shop_section_id = '" . (int) $shop_section_id . "' LIMIT 1");
            } else {
                $this->db->query("UPDATE " . DB_PREFIX . "etsy_shop_sections SET delete_flag = '1' WHERE shop_section_id = '" . (int) $shop_section_id . "'");
            }
        }
        return true;
    }

}
