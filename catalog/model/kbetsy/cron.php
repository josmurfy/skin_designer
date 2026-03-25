<?php

class ModelKbetsyCron extends Model {

    public function auditLogEntry($auditLogEntry = '', $auditMethodName = '') {
        $auditLogTime = date("Y-m-d H:i:s");
        $auditLogUser = 'CRON';
        $auditLogSQL = "INSERT INTO " . DB_PREFIX . "etsy_audit_log VALUES (NULL, '" . $this->db->escape($auditLogEntry) . "', '" . $this->db->escape($auditLogUser) . "', '" . $this->db->escape($auditMethodName) . "', '" . $this->db->escape($auditLogTime) . "');";
        $this->db->query($auditLogSQL);
    }

    public function createEtsyObject() {
        $settings = $this->config->get('etsy_general_settings');
        $access_token = $this->config->get('etsy_access_token');
        $access_token_secret = $this->config->get('etsy_access_token_secret');
        $consumer_key = $settings['etsy_api_key'];
        $consumer_secret = $settings['etsy_api_secret'];
        $etsyMain = new \Etsy\EtsyMain($consumer_key, $consumer_secret, $access_token, $access_token_secret);
        return $etsyMain;
    }

    public function getShippingTemplatesToAdd() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates WHERE shipping_template_id IS NULL");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function getShippingTemplatesToRenew() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates WHERE shipping_template_id IS NOT NULL AND shipping_template_id != '' AND shipping_template_id != 0 AND renew_flag = '1' AND delete_flag = '0'");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function getShippingTemplatesToDelete() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates WHERE shipping_template_id IS NOT NULL AND shipping_template_id != '' AND shipping_template_id != 0 AND delete_flag = '1'");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function updateShippingTemplates($id, $data) {
        $this->db->query("UPDATE " . DB_PREFIX . "etsy_shipping_templates SET renew_flag = '0', shipping_template_id = '" . $data[0]['shipping_template_id'] . "' WHERE id_etsy_shipping_templates = '" . (int) $id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_templates_entries WHERE id_etsy_shipping_templates = '" . $id . "'");

        if (!empty($data[0]['Entries'])) {
            foreach ($data[0]['Entries'] as $entry) {

                $destination_country_name = "";
                $destination_country_id = $entry['destination_country_id'];
                if ($entry['destination_country_id'] != "") {
                    $destination_country_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_countries WHERE country_id = " . (int) $entry['destination_country_id']);
                    if ($destination_country_query->num_rows > 0) {
                        $destination_country_name = $destination_country_query->row['country_name'];
                    }
                }

                $destination_region_name = "";
                $destination_region_id = $entry['destination_region_id'];
                if ($entry['destination_region_id'] != "") {
                    $destination_region_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_regions WHERE region_id = " . (int) $entry['destination_region_id']);
                    if ($destination_region_query->num_rows > 0) {
                        $destination_region_name = $destination_region_query->row['region_name'];
                    }
                }

                $insert_entry = true;
                /* If template region entry exist, then do add the region entry again */
                if ($destination_region_id != "") {
                    $template_entry_region_exist = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates_entries WHERE shipping_entry_destination_region_id = " . $destination_region_id);
                    if ($template_entry_region_exist->num_rows > 0) {
                        $insert_entry = false;
                    }
                }
                if ($insert_entry == true) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_shipping_templates_entries("
                            . "id_etsy_shipping_templates,"
                            . "shipping_template_entry_id,"
                            . "shipping_entry_destination_country_id,"
                            . "shipping_entry_destination_country,"
                            . "shipping_entry_primary_cost,"
                            . "shipping_entry_secondary_cost,"
                            . "shipping_entry_destination_region_id,"
                            . "shipping_entry_destination_region,"
                            . "shipping_entry_date_added,"
                            . "shipping_entry_date_update"
                            . ") VALUES("
                            . "'" . $id . "',"
                            . "'" . $entry['shipping_template_entry_id'] . "',"
                            . "'" . $destination_country_id . "',"
                            . "'" . $destination_country_name . "',"
                            . "'" . $entry['primary_cost'] . "',"
                            . "'" . $entry['secondary_cost'] . "',"
                            . "'" . $destination_region_id . "',"
                            . "'" . $destination_region_name . "',"
                            . "'" . date("Y-m-d H:i:s") . "',"
                            . "'" . date("Y-m-d H:i:s") . "'"
                            . ")");
                }
            }
        }
    }

    public function deleteShippingTemplate($id, $shipping_template_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_templates WHERE shipping_template_id = '" . $shipping_template_id . "' AND id_etsy_shipping_templates = '" . (int) $id . "'");
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_templates_entries WHERE id_etsy_shipping_templates = '" . $id . "'");
    }

    public function getAllShippingTemplates() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates WHERE shipping_template_id IS NOT NULL AND shipping_template_id != '' AND shipping_template_id != 0");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function getShippingTemplateEntriesToAdd($id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates_entries WHERE id_etsy_shipping_templates = '" . (int) $id . "' AND shipping_template_entry_id IS NULL");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function getShippingTemplateEntriesToUpdate() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates_entries WHERE shipping_template_entry_id IS NOT NULL AND shipping_template_entry_id != '' AND shipping_template_entry_id != 0 AND renew_flag = '1' AND delete_flag = '0'");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function getShippingTemplateEntriesToDelete() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_shipping_templates_entries WHERE shipping_template_entry_id IS NOT NULL AND shipping_template_entry_id != '' AND shipping_template_entry_id != 0 AND delete_flag = '1'");

        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function updateShippingTemplateEntryStatus($shippingTemplateEntryID, $local_entry_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "etsy_shipping_templates_entries SET renew_flag = '0', shipping_template_entry_id = '" . $shippingTemplateEntryID . "' WHERE id_etsy_shipping_templates_entries = '" . (int) $local_entry_id . "'");
    }

    public function deleteShippingTemplateEntry($id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_shipping_templates_entries WHERE id_etsy_shipping_templates_entries = '" . (int) $id . "'");
    }

    public function insertCountries($countries) {
        $empty = $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "etsy_countries");
        if ($empty) {
            foreach ($countries as $country) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_countries VALUES (NULL, '" . (int) $country['country_id'] . "', '" . $this->db->escape($country['name']) . "', '" . $this->db->escape($country['iso_country_code']) . "')");
            }
        }
        return true;
    }

    public function geyEtsyCountry($country_id) {
        $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_countries WHERE country_id = '" . $country_id . "'");
        if ($result->num_rows > 0) {
            return $result->row;
        } else {
            return "";
        }
    }

    public function insertRegions($regions) {
        $empty = $this->db->query("TRUNCATE TABLE " . DB_PREFIX . "etsy_regions");
        if ($empty) {
            foreach ($regions as $region) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_regions VALUES (NULL, '" . (int) $region['region_id'] . "', '" . $this->db->escape($region['region_name']) . "')");
            }
        }
        return true;
    }

    public function geyEtsyRegion($region_id) {
        $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_regions WHERE region_id = '" . $region_id . "'");
        if ($result->num_rows > 0) {
            return $result->row;
        } else {
            return "";
        }
    }

    public function getEtsyProductCombinations($id_product) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_product_option epo LEFT JOIN " . DB_PREFIX . "etsy_products_list epl on epl.id_product_attribute = epo.etsy_product_option_id WHERE epo.id_product = '" . (int) $id_product . "' order by epo.color_option_value_id ASC");
        if ($query->num_rows) {
            return $query->rows;
        } else {
            return 0;
        }
    }

    public function checkVariationExists($id_product, $productVariation) {
        $query = $this->db->query("SELECT etsy_product_option_id FROM " . DB_PREFIX . "etsy_product_option WHERE id_product = '" . (int) $id_product . "' AND ((color_option_value_id = '" . (int) $productVariation['option_value_id'] . "' AND size_option_value_id = '" . $productVariation[0]['option_value_id'] . "') OR (color_option_value_id = '" . (int) $productVariation[0]['option_value_id'] . "' AND size_option_value_id = '" . $productVariation['option_value_id'] . "'))");
        if ($query->num_rows) {
            $query1 = $this->db->query("SELECT id_etsy_products_list FROM " . DB_PREFIX . "etsy_products_list WHERE id_product_attribute = '" . (int) $query->rows[0]['etsy_product_option_id'] . "'");

            return $query1->rows;
        } else {
            return 0;
        }
    }

    public function insertVariation($id_etsy_profiles, $productVariation) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_attribute_mapping WHERE id_etsy_profiles = '" . (int) $id_etsy_profiles . "'");
        foreach ($query->rows as $value) {
            if ($value['property_title'] == 'Color') {
                $color_option_id = $value['id_attribute_group'];
            }
        }
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "option_value_description WHERE option_value_id = '" . (int) $productVariation['option_value_id'] . "'");

        if ($query->row['option_id'] == $color_option_id) {
            $color_option_value_id = $productVariation['option_value_id'];
            $size_option_value_id = $productVariation[0]['option_value_id'];
        } else {
            $color_option_value_id = $productVariation[0]['option_value_id'];
            $size_option_value_id = $productVariation['option_value_id'];
        }
        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_product_option SET id_product = '" . (int) $productVariation['id_product'] . "', color_option_value_id = '" . (int) $productVariation['option_value_id'] . "', size_option_value_id = '" . $productVariation[0]['option_value_id'] . "'");
        $id = $this->db->getLastId();
        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_products_list SET id_etsy_products_list = '', id_etsy_profiles = '" . (int) $id_etsy_profiles . "', id_product = '" . (int) $productVariation['id_product'] . "', reference = '" . $this->db->escape($productVariation['reference']) . "', id_product_attribute = '" . (int) $id . "', date_added = NOW()");

        return $this->db->getLastId();
    }

    public function updateVariation($productVariation) {
        $query = $this->db->query("SELECT etsy_product_option_id FROM " . DB_PREFIX . "etsy_product_option WHERE id_product = '" . (int) $productVariation['id_product'] . "' AND ((color_option_value_id = '" . (int) $productVariation['option_value_id'] . "' AND size_option_value_id = '" . $productVariation[0]['option_value_id'] . "') OR (color_option_value_id = '" . (int) $productVariation[0]['option_value_id'] . "' AND size_option_value_id = '" . $productVariation['option_value_id'] . "'))");
        if ($query->num_rows) {
            $query = $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET reference = '" . $this->db->escape($productVariation['reference']) . "', renew_flag = '1' WHERE id_product = '" . (int) $productVariation['id_product'] . "' AND id_product_attribute = '" . (int) $query->rows[0]['etsy_product_option_id'] . "'");
        }
        return true;
    }

    public function getAllVariationsByProductId($product_id) {
        $query = $this->db->query("SELECT id_etsy_products_list FROM " . DB_PREFIX . "etsy_products_list WHERE id_product = '" . (int) $product_id . "' AND id_product_attribute != '0'");
        if ($query->num_rows) {
            return $query->rows;
        }
        return false;
    }

    public function insertProduct($id_etsy_profiles, $categoryProduct) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_products_list SET id_etsy_products_list = '', id_etsy_profiles = '" . (int) $id_etsy_profiles . "', id_product = '" . (int) $categoryProduct['product_id'] . "', reference = '" . $this->db->escape($categoryProduct['model']) . "', date_added = NOW()");
        return $this->db->getLastId();
    }

    public function updateProduct($id_etsy_profiles, $categoryProduct) {
        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET id_etsy_profiles = '" . (int) $id_etsy_profiles . "', reference = '" . $this->db->escape($categoryProduct['model']) . "' WHERE id_product = '" . (int) $categoryProduct['product_id'] . "' AND id_product_attribute = 0");
        return true;
    }

    public function getProductsToListOnEtsy() {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NULL AND id_product_attribute = 0 AND renew_flag = '0' AND delete_flag = '0'";
        $getProductsToListOnEtsy = $this->db->query($sql);
        if ($getProductsToListOnEtsy->num_rows > 0) {
            return $getProductsToListOnEtsy->rows;
        } else {
            return false;
        }
    }

    public function getProductsToRenewOnEtsy() {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL AND listing_id != '' AND listing_id != 0 AND id_product_attribute = 0 AND renew_flag = '1' AND delete_flag = '0'";
        $getProductsToListOnEtsy = $this->db->query($sql);
        if ($getProductsToListOnEtsy->num_rows > 0) {
            return $getProductsToListOnEtsy->rows;
        } else {
            return false;
        }
    }

    public function getProductsToDeleteOnEtsy() {
        $sql = "SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL AND listing_id != '' AND listing_id != 0 AND id_product_attribute = 0 AND renew_flag = '0' AND delete_flag = '1'";
        $getProductsToListOnEtsy = $this->db->query($sql);
        if ($getProductsToListOnEtsy->num_rows > 0) {
            return $getProductsToListOnEtsy->rows;
        } else {
            return false;
        }
    }

    public function getProductsListedOnEtsy($flag = false) {
        if ($flag) {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL AND listing_id != '' AND listing_id != 0 AND id_product_attribute = 0 AND renew_flag = '0'";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL AND listing_id != '' AND listing_id != 0 AND id_product_attribute = 0 AND renew_flag = '0' AND delete_flag = '0'";
        }
        $getProductsToListOnEtsy = $this->db->query($sql);
        if ($getProductsToListOnEtsy->num_rows > 0) {
            return $getProductsToListOnEtsy->rows;
        } else {
            return false;
        }
    }

    public function getProductsToUploadImageOnEtsy($newListings = false) {
        if ($newListings) {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL AND listing_id != '' AND listing_id != 0 AND listing_status = 'Listed' AND delete_flag = '0' AND date_listed > DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL AND listing_id != '' AND listing_id != 0 AND listing_status = 'Listed' AND delete_flag = '0' AND date_last_renewed > DATE_SUB(CURDATE(), INTERVAL 1 DAY)";
        }
        $getProductsToUploadImageOnEtsy = $this->db->query($sql);
        if ($getProductsToUploadImageOnEtsy->num_rows > 0) {
            return $getProductsToUploadImageOnEtsy->rows;
        } else {
            return false;
        }
    }

    public function updateVariationListingAddStatus($id) {

        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = 'Listed', date_listed = NOW(), listing_error = '' WHERE id_product = '" . (int) $id . "' AND delete_flag ='0'");
    }

    public function insertTranslation($id_product, $listing_id, $etsy_lang) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_translation SET id_product = '" . $id_product . "', listing_id = '" . $listing_id . "', status = 'Pending', lang_code = '" . $etsy_lang . "', date_added = NOW(), date_updated = NOW()");
    }

    public function updateVariationListingAddErrorStatus($id_product, $message) {

        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_error = '" . $this->db->escape($message) . "' WHERE id_product = '" . $id_product . "' AND id_product_attribute = '0'");
    }

    public function updateListingAddImageStatus($listing_img_id, $listing_id) {
        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_image_id = '" . $this->db->escape($listing_img_id) . "' WHERE listing_id = '" . (int) $listing_id . "'");
    }

    public function getProductToListVariationsOnEtsy($id_product) {
        $sql = "SELECT p1.`id_etsy_profiles`, p1.`id_product`, p1.`id_product_attribute`, p2.`listing_id`, p1.delete_flag FROM " . DB_PREFIX . "etsy_products_list p1 INNER JOIN " . DB_PREFIX . "etsy_products_list p2 ON p1.id_product = p2.id_product AND p2.listing_id IS NOT NULL AND p1.id_product_attribute != '0' AND p1.id_product = " . (int) $id_product . "";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function getProductToUpdateVariationsOnEtsy($id_product) {
        $sql = "SELECT p1.`id_etsy_profiles`, p1.`id_product`, p1.`id_product_attribute`, p2.`listing_id`, p1.delete_flag FROM " . DB_PREFIX . "etsy_products_list p1 INNER JOIN " . DB_PREFIX . "etsy_products_list p2  ON p1.id_product = p2.id_product AND p2.listing_id IS NOT NULL AND p1.id_product_attribute != '0' AND p1.id_product = " . (int) $id_product . "";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function getEtsyAttributeMapping($id_etsy_profiles, $option_id = '') {
        if ($option_id != '') {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_attribute_mapping WHERE id_etsy_profiles = '" . $id_etsy_profiles . "' AND id_attribute_group = '" . $option_id . "'";
        } else {
            $sql = "SELECT * FROM " . DB_PREFIX . "etsy_attribute_mapping WHERE id_etsy_profiles = '" . $id_etsy_profiles . "'";
        }
        $query = $this->db->query($sql);
        if ($query->num_rows == 1) {
            return $query->row;
        } elseif ($query->num_rows > 1) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function getCountryByISOCode($iso_code) {
        $sql = "SELECT * FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $iso_code . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getCountryByName($name) {
        $sql = "SELECT * FROM " . DB_PREFIX . "country WHERE name = '" . $name . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getStateByName($name) {
        $sql = "SELECT * FROM " . DB_PREFIX . "zone WHERE code = '" . $name . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getOptionId($id_attribute) {
        $sql = "SELECT * FROM " . DB_PREFIX . "product_option WHERE product_option_id = '" . $id_attribute . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->row;
        } else {
            return false;
        }
    }

    public function getProductOptions($product_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "product_option_value po left join " . DB_PREFIX . "product p on p.product_id = po.product_id WHERE po.product_id = '" . $product_id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows > 0) {
            return $query->rows;
        } else {
            return false;
        }
    }

    public function getProductOptionID($product_id, $product_option_id, $option_id = 0) {
        $product_option_value_data = array();


        $sql = "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int) $product_id . "' AND pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'";
        if ($option_id != '') {
            $sql .= " AND pov.option_id ='" . $option_id . "' ";
        }
        $sql .= " ORDER BY ov.sort_order";
        echo $sql;
        $product_option_value_query = $this->db->query($sql);

        foreach ($product_option_value_query->rows as $product_option_value) {
            $product_option_value_data[] = array(
                'product_option_value_id' => $product_option_value['product_option_value_id'],
                'option_value_id' => $product_option_value['option_value_id'],
                'product_option_id' => $product_option_value['product_option_id'],
                'name' => $product_option_value['name'],
                'image' => $product_option_value['image'],
                'quantity' => $product_option_value['quantity'],
                'subtract' => $product_option_value['subtract'],
                'price' => $product_option_value['price'],
                'price_prefix' => $product_option_value['price_prefix'],
                'weight' => $product_option_value['weight'],
                'weight_prefix' => $product_option_value['weight_prefix']
            );
        }

        if (count($product_option_value_data) > 0) {
            return $product_option_value_data;
        } else {
            return false;
        }
    }

    public function getProductOptionDetails($product_id, $option_value_id = 0) {
        $product_option_value_data = array();


        $sql = "SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int) $product_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "'";
        if ($option_value_id != '') {
            $sql .= " AND pov.option_value_id  ='" . $option_value_id . "' ";
        }
        $sql .= " ORDER BY ov.sort_order";

        $product_option_value_query = $this->db->query($sql);

        foreach ($product_option_value_query->rows as $product_option_value) {
            $product_option_value_data = array(
                'product_option_value_id' => $product_option_value['product_option_value_id'],
                'option_value_id' => $product_option_value['option_value_id'],
                'product_option_id' => $product_option_value['product_option_id'],
                'option_id' => $product_option_value['option_id'],
                'name' => $product_option_value['name'],
                'image' => $product_option_value['image'],
                'quantity' => $product_option_value['quantity'],
                'subtract' => $product_option_value['subtract'],
                'price' => $product_option_value['price'],
                'price_prefix' => $product_option_value['price_prefix'],
                'weight' => $product_option_value['weight'],
                'weight_prefix' => $product_option_value['weight_prefix']
            );
        }

        if (count($product_option_value_data) > 0) {
            return $product_option_value_data;
        } else {
            return false;
        }
    }

    public function getAttributeCombinationsById($product_option_id) {
        $product_option_value_data = array();

        $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

        foreach ($product_option_value_query->rows as $product_option_value) {
            $product_option_value_data[] = array(
                'product_option_value_id' => $product_option_value['product_option_value_id'],
                'option_value_id' => $product_option_value['option_value_id'],
                'product_option_id' => $product_option_value['product_option_id'],
                'name' => $product_option_value['name'],
                'image' => $product_option_value['image'],
                'quantity' => $product_option_value['quantity'],
                'subtract' => $product_option_value['subtract'],
                'price' => $product_option_value['price'],
                'price_prefix' => $product_option_value['price_prefix'],
                'weight' => $product_option_value['weight'],
                'weight_prefix' => $product_option_value['weight_prefix']
            );
        }

        if (count($product_option_value_data) > 0) {
            return $product_option_value_data;
        } else {
            return false;
        }
    }

    public function getAttributeInventory($product_option_id, $product_id) {
        $product_option_value_data = array();

        $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int) $product_option_id . "' AND pov.product_id = '" . (int) $product_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

        foreach ($product_option_value_query->rows as $product_option_value) {
            $product_option_value_data = array(
                'product_option_value_id' => $product_option_value['product_option_value_id'],
                'option_value_id' => $product_option_value['option_value_id'],
                'product_option_id' => $product_option_value['product_option_id'],
                'name' => $product_option_value['name'],
                'image' => $product_option_value['image'],
                'quantity' => $product_option_value['quantity'],
                'subtract' => $product_option_value['subtract'],
                'price' => $product_option_value['price'],
                'price_prefix' => $product_option_value['price_prefix'],
                'weight' => $product_option_value['weight'],
                'weight_prefix' => $product_option_value['weight_prefix']
            );
        }

        if (count($product_option_value_data) > 0) {
            return $product_option_value_data;
        } else {
            return false;
        }
    }

    public function getOptionInventory($product_option_id) {
        $product_option_value_data = array();

        $product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_id = '" . (int) $product_option_id . "' AND ovd.language_id = '" . (int) $this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

        foreach ($product_option_value_query->rows as $product_option_value) {
            $product_option_value_data[] = array(
                'product_option_value_id' => $product_option_value['product_option_value_id'],
                'option_value_id' => $product_option_value['option_value_id'],
                'product_option_id' => $product_option_value['product_option_id'],
                'name' => $product_option_value['name'],
                'image' => $product_option_value['image'],
                'quantity' => $product_option_value['quantity'],
                'subtract' => $product_option_value['subtract'],
                'price' => $product_option_value['price'],
                'price_prefix' => $product_option_value['price_prefix'],
                'weight' => $product_option_value['weight'],
                'weight_prefix' => $product_option_value['weight_prefix']
            );
        }

        if (count($product_option_value_data) > 0) {
            return $product_option_value_data;
        } else {
            return false;
        }
    }

    public function getProductOptionIDByName($name) {
        $order_query = $this->db->query("SELECT option_id FROM `" . DB_PREFIX . "option_description` WHERE name = '" . $name . "'");
        return $order_query->row['option_id'];
    }

   // public function getEtsyVariationByType($option_id, $etsy_product_option_id) {
    public function getEtsyVariationByType($option_name, $name) {
        $order_query = $this->db->query("SELECT option_value_id FROM `" . DB_PREFIX . "option_value_description` ovd LEFT JOIN " . DB_PREFIX . "option_description od on od.option_id = ovd.option_id WHERE od.name = '" . $option_name . "' and ovd.name = '" . $name . "'");
        return $order_query->row['option_value_id'];
    }

    public function get_option_values_id($option_name, $name) {
        //echo "SELECT option_value_id FROM `" . DB_PREFIX . "option_value_description` ovd LEFT JOIN " . DB_PREFIX . "option_description od on od.option_id = ovd.option_id WHERE od.name = '" . $option_name . "' and ovd.name = '".$name."'";
        $order_query = $this->db->query("SELECT option_value_id FROM `" . DB_PREFIX . "option_value_description` ovd LEFT JOIN " . DB_PREFIX . "option_description od on od.option_id = ovd.option_id WHERE od.name = '" . $option_name . "' and ovd.name = '" . $name . "'");
        return $order_query->row['option_value_id'];
    }

    public function get_product_option_value_id($product_id, $option_value_id) {
        $order_query = $this->db->query("SELECT product_option_value_id FROM `" . DB_PREFIX . "product_option_value` WHERE product_id = '" . $product_id . "' and option_value_id = '" . $option_value_id . "'");
        return $order_query->row['product_option_value_id'];
    }

    public function getCustomerByEmail($email) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

        return $query->row;
    }

    public function getLanguageByCode($code) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "language WHERE code = '" . $code . "'");
        return $query->row;
    }

    public function getCurrencyByCode($currency) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "currency WHERE code = '" . $this->db->escape($currency) . "'");
        return $query->row;
    }

    public function getProduct($product_id) {
        $result = $this->db->query("SELECT 
            p.product_id, 
            p.sku, 
            p.quantity,
            p.price, 
            p.model 
            FROM " . DB_PREFIX . "product as p where p.product_id = '" . $product_id . "'");
        return $result->row;
    }

}
