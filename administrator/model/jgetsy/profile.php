<?php

class ModelJgetsyProfile extends Model
{

    public function addProfile($data)
    {
        $data['product_category'] = implode(",", $data['product_category']);
        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_profiles("
                . "profile_title,"
                . "etsy_category_code,"
                . "etsy_category_text,"
                . "store_categories,"
                . "id_etsy_shipping_profiles,"
                . "is_customizable,"
                . "who_made,"
                . "when_made,"
                . "is_supply,"
                . "recipient,"
                . "occassion,"
                . "auto_renew,"
                . "price_type,"
                . "price_management,"
                . "increase_decrease,"
                . "product_price,"
                . "percentage_fixed,"
                . "shop_section_id,"
                . "active,"
                . "date_added,"
                . "date_updated"
                . ")VALUES("
                . "'" . $this->db->escape($data['etsy']['profile']['profile_title']) . "',"
                . "'" . (int) $data['etsy']['profile']['id_etsy_category_final'] . "', "
                . "'" . $this->db->escape($data['etsy']['profile']['etsy_category_text']) . "', "
                . "'" . $this->db->escape($this->db->escape($data['product_category'])) . "', "
                . "'" . (int) $data['etsy']['profile']['etsy_templates'] . "',"
                . "'" . (int) ($data['etsy']['profile']['is_customizable']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['who_made']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['when_made']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['is_supply']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['etsy_recipient']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['etsy_occasion']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['auto_renew']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['price_type']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['price_management']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['increase_decrease']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['product_price']) . "',"
                . "'" . $this->db->escape($data['etsy']['profile']['percentage_fixed']) . "',"                
                . "'" . $this->db->escape($data['etsy']['profile']['shop_section_id']) . "',"                
                . "'1', "
                . "NOW(), "
                . "NOW()"
                . ")");
        $id = $this->db->getLastId();
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_attribute_option_mapping where id_etsy_profiles = '".$id."' ");
        
        foreach($data['property_attr'] as $key=>$property_attr) {
            
            if(!empty($property_attr)) {
            
                $id_attribute_group = implode(",",$property_attr);
                
                $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_attribute_option_mapping("
                    . "property_id,"
                    . "id_etsy_profiles,"
                    . "id_attribute_group"
                    . ")VALUES("
                    . "'" . (int)$key . "',"
                    . "'" . (int)$id . "',"
                    . "'" . $id_attribute_group . "');"
                        );
            }
        }
        
    }

    public function editProfile($data)
    {
        $data['product_category'] = implode(",", $data['product_category']);
        $this->db->query("UPDATE " . DB_PREFIX . "etsy_profiles SET "
                . "profile_title = '" . $this->db->escape($data['etsy']['profile']['profile_title']) . "', "
                . "etsy_category_code = '" . (int) $data['etsy']['profile']['id_etsy_category_final'] . "', "
                . "store_categories = '" . $this->db->escape($this->db->escape($data['product_category'])) . "', "
                . "etsy_category_text = '" . $this->db->escape($this->db->escape($data['etsy']['profile']['etsy_category_text'])) . "', "
                . "id_etsy_shipping_profiles = '" . (int) $data['etsy']['profile']['etsy_templates'] . "',"
                . "who_made = '" . $this->db->escape($data['etsy']['profile']['who_made']) . "',"
                . "when_made = '" . $this->db->escape($data['etsy']['profile']['when_made']) . "',"
                . "auto_renew = '" . $this->db->escape($data['etsy']['profile']['auto_renew']) . "',"
                . "price_type = '" . $this->db->escape($data['etsy']['profile']['price_type']) . "',"
                . "price_management = '" . $this->db->escape($data['etsy']['profile']['price_management']) . "',"
                . "increase_decrease = '" . $this->db->escape($data['etsy']['profile']['increase_decrease']) . "',"
                . "product_price = '" . $this->db->escape($data['etsy']['profile']['product_price']) . "',"
                . "percentage_fixed = '" . $this->db->escape($data['etsy']['profile']['percentage_fixed']) . "',"
                . "active = '1', "
                . "date_updated = NOW(), "
                . "is_customizable = '" . $this->db->escape($data['etsy']['profile']['is_customizable']) . "',"
                . "is_supply = '" . $this->db->escape($data['etsy']['profile']['is_supply']) . "',"
                . "recipient = '" . $this->db->escape($data['etsy']['profile']['etsy_recipient']) . "',"
                . "occassion = '" . $this->db->escape($data['etsy']['profile']['etsy_occasion']) . "',"
                . "shop_section_id = '" . $this->db->escape($data['etsy']['profile']['shop_section_id']) . "'"
                . "WHERE id_etsy_profiles = " . $data['id_etsy_profiles']);
        
        /* Set Update Flag to the Products to 1 */
        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET update_flag = '1', error_flag = '0' WHERE id_etsy_profiles = '".$data['id_etsy_profiles']."' AND listing_status = 'Listed' AND delete_flag = '0' AND renew_flag = '0'");
        
        $id=$data['id_etsy_profiles'];
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_attribute_option_mapping where id_etsy_profiles = '".$id."' ");
        
        foreach($data['property_attr'] as $key=>$property_attr) {
            $id_attribute_group = "";
            if(is_array($property_attr) && !empty($property_attr)) {
                $id_attribute_group = implode(",",$property_attr);
            } else if($property_attr!="") {
                $id_attribute_group = $property_attr;
            }
            if($id_attribute_group !="") {  
                $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_attribute_option_mapping("
                    . "property_id,"
                    . "id_etsy_profiles,"
                    . "id_attribute_group"
                    . ")VALUES("
                    . "'" . $this->db->escape($key) . "',"
                    . "'" . (int)$id . "',"
                    . "'" . $this->db->escape($id_attribute_group) . "');"
                );
            }
        }
    }

    public function deleteProfile($id_etsy_profiles)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_profiles WHERE id_etsy_profiles = '" . (int) $id_etsy_profiles . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_products_list WHERE id_etsy_profiles = '" . (int) $id_etsy_profiles . "'");
        return true;
    }

    public function saveAttribute($data,$id_etsy_profiles=0)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_profiles WHERE id_etsy_profiles = '" . (int) $id_etsy_profiles . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_products_list WHERE id_etsy_profiles = '" . (int) $id_etsy_profiles . "'");
        return true;
    }
    

    
    public function getAllProfiles() {
        $sql = "SELECT id_etsy_profiles, profile_title FROM " . DB_PREFIX . "etsy_profiles ORDER BY profile_title ASC";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function checkProfileCategory($data, $id = '')
    {
        if (isset($id) && $id != '') {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_profiles WHERE id_etsy_profiles != " . (int) $id . " AND (";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_profiles WHERE (";
        }
        if (!empty($data)) {
            $count = count($data);
            $i = 1;
            foreach ($data as $category) {
                if ($count > $i) {
                    $sql .= " FIND_IN_SET(".$category.", store_categories) OR ";
                } else {
                    $sql .= " FIND_IN_SET(".$category.", store_categories)";
                }
                $i++;
            }
        }
        $sql .= ")";
        $query = $this->db->query($sql);
        return $query->rows;
    }

}

?>