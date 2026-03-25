<?php



class ModelJgetsyProduct extends Model {



    /** Deactivate Product for Inactive Profiles */

    public function updateProductStatusByProfileStatus() {

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_profiles WHERE active = '0'");

        if ($query->num_rows > 0) {

            foreach ($query->rows as $getEtsyProfile) {

                $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_status = 'Inactive', delete_flag = '1', renew_flag = '0' WHERE id_etsy_profiles = '" . (int) $getEtsyProfile['id_etsy_profiles'] . "'");

            }

        }

        return true;

    }



    public function syncProductsToModule() { 

        $profileQuery = 'SELECT * FROM ' . DB_PREFIX . 'etsy_profiles WHERE active = "1"';

        $profile_data = $this->db->query($profileQuery);

        $listProducts = array();

        $variationProducts = array();



        if ($profile_data->num_rows > 0) {

            $profiles = $profile_data->rows;

            foreach ($profiles as $profile) {



                /* Add Inactive & Disabled Product Condition */

                $query = $this->db->query("SELECT p.product_id, p.model FROM " . DB_PREFIX . "product p, " . DB_PREFIX . "product_to_category p2c WHERE p.product_id = p2c.product_id and p2c.category_id in(" . $profile['store_categories'] . ") AND p.status = 1");

                foreach ($query->rows as $products) {



                    $variations = $this->getVariations($products['product_id']);

                    $variation_flag = false;

                    if (!empty($variations)) {

                        $variation_flag = true;

                    }



                    /* Check in the profile produc table. If exist, Insert only new variations else all variations */

                    $query_check = $this->db->query("SELECT id_product, reference FROM " . DB_PREFIX . "etsy_products_list epp INNER JOIN " . DB_PREFIX . "etsy_profiles ep ON epp.id_etsy_profiles = ep.id_etsy_profiles WHERE id_product = " . $products['product_id']);

                    $attr_array = array();

                    foreach ($query_check->rows as $profile_product) {

                        $attr_array[] = $profile_product['reference'];

                    }

                    $productOptions = array();



                    /* To Generate the Correct OPtion Combination, Key is required instead of index. So added option_ as text in the key. Later Removed at the time of DB INSERT */

                    if (!empty($variations)) {

                        foreach ($variations as $variation) {

                            $productOptions['option_' . $variation['option_id']][] = $variation['option_value_id'];

                        }

                    }



                    /* Generate All Combinations of the Variations. Option_ID & Option Values. 

                      [1option] => 43

                      [5option] => 42

                      [2option] => 45

                     */



                    /* Insert Main Product */

                    $product_reference = $products['model'];



                    if (!$this->checkProductExists($products['product_id'])) {

                        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_products_list SET id_etsy_profiles = '" . (int) $profile['id_etsy_profiles'] . "', id_product = '" . (int) $products['product_id'] . "', id_product_attribute = 0, reference = '" . $this->db->escape($product_reference) . "', listing_status = 'Pending', date_added = now()");

                    }



                    $listProducts[] = $products['product_id'];

                    $variations_combinations = $this->get_combinations($productOptions);



                    /* If product variation exists insert only non added items else all items */

                    if ($query_check->num_rows != 0) {



                        if ($variation_flag) {

                            foreach ($variations_combinations as $variation) {



                                $product_reference = $products['product_id'];

                                foreach ($variation as $key_option_id => $key_option_value) {

                                    $option_id = str_replace("option_", "", $key_option_id);

                                    $product_reference = $product_reference . "_" . $option_id . ":" . $key_option_value;

                                }

                                if (in_array($product_reference, $attr_array)) {

                                    //do nothing

                                } else {

                                    //$this->db->query("INSERT INTO " . DB_PREFIX . "etsy_products_list SET id_etsy_profiles = '" . (int) $profile['id_etsy_profiles'] . "', id_product = '" . (int) $products['product_id'] . "', id_product_attribute = 1, reference = '" . $this->db->escape($product_reference) . "', listing_status = 'New', date_added = now()");

                                }

                                $variationProducts[] = $product_reference;

                            }

                        }

                    } else {

                        if ($variation_flag) {

                            foreach ($variations_combinations as $option_id => $variation) {

                                $product_reference = $products['product_id'];

                                foreach ($variation as $key_option_id => $key_option_value) {

                                    $option_id = str_replace("option_", "", $key_option_id);

                                    $product_reference = $product_reference . "_" . $option_id . ":" . $key_option_value;

                                }

                                $variationProducts[] = $product_reference;

                                //$this->db->query("INSERT INTO " . DB_PREFIX . "etsy_products_list SET id_etsy_profiles = '" . (int) $profile['id_etsy_profiles'] . "', id_product = '" . (int) $products['product_id'] . "', id_product_attribute = 1, reference = '" . $this->db->escape($product_reference) . "', listing_status = 'New', date_added = now()");

                            }

                        }

                    }

                }

            }

        }



        /* Delete those products which are not mapped with any profile */

        if (!empty($listProducts)) {

            $deleteSQL = "UPDATE " . DB_PREFIX . "etsy_products_list SET delete_flag = '1', renew_flag = '0', listing_status = 'Inactive' WHERE id_product NOT IN (" . implode(",", $listProducts) . ") and listing_id != 'NULL'";

            $this->db->query($deleteSQL);



            $deleteSQL = "DELETE FROM " . DB_PREFIX . "etsy_products_list WHERE id_product NOT IN (" . $this->db->escape(implode(",", $listProducts)) . ") and listing_id = NULL";

            $this->db->query($deleteSQL);

        }

        if (!empty($variationProducts)) {

            //$deleteSQL = "UPDATE " . DB_PREFIX . "etsy_products_list SET delete_flag = '1', renew_flag = '0', listing_status = 'Inactive' WHERE reference NOT IN ('" . implode("','", $variationProducts) . "') and listing_id != 'NULL'";

            //$this->db->query($deleteSQL);

            //$deleteSQL = "DELETE FROM " . DB_PREFIX . "etsy_products_list WHERE reference NOT IN ('" . implode("','", $variationProducts) . "') and listing_id = NULL";

            //$this->db->query($deleteSQL);

        }

    }



    function get_combinations($arrays) {

        $result = array(array());

        foreach ($arrays as $property => $property_values) {

            $tmp = array();

            foreach ($result as $result_item) {

                foreach ($property_values as $property_value) {

                    $tmp[] = array_merge($result_item, array($property => $property_value));

                }

            }

            $result = $tmp;

        }

        return $result;

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



    public function checkProductExists($product_id) {

        $query = $this->db->query("SELECT id_etsy_products_list FROM " . DB_PREFIX . "etsy_products_list WHERE id_product = '" . (int) $product_id . "'");



        if ($query->num_rows) {

            return $query->row;

        } else {

            return false;

        }

    }



    public function getProductsToListOnEtsy($product_id = "", $limit = 20) {

        if ($product_id == "") {
/*echo "SELECT epl.id_etsy_products_list,epl.id_etsy_profiles,epl.id_product,
epl.reference,epl.id_product_attribute,epl.listing_status,epl.listing_id,
epl.listing_image_id,epl.update_flag,epl.renew_flag,epl.delete_flag,epl.date_added,
epl.date_listed,epl.date_last_renewed,epl.listing_error,epl.error_flag,
epl.is_disabled,epl.sold_flag,epl.expiry_date,epl.updatedby,p.quantity
FROM " . DB_PREFIX . "etsy_products_list epl
LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id=epl.id_product) 
WHERE epl.listing_id IS NULL AND epl.renew_flag = '0' AND 
epl.delete_flag = '0' AND epl.is_disabled = 0 AND 
epl.error_flag = 0 AND p.quantity>0
ORDER BY epl.id_etsy_products_list ASC LIMIT " . $limit;*/
            $productsToListOnEtsy = $this->db->query(
                "SELECT epl.id_etsy_products_list,epl.id_etsy_profiles,epl.id_product,
                 epl.reference,epl.id_product_attribute,epl.listing_status,epl.listing_id,
                 epl.listing_image_id,epl.update_flag,epl.renew_flag,epl.delete_flag,epl.date_added,
                 epl.date_listed,epl.date_last_renewed,epl.listing_error,epl.error_flag,
                 epl.is_disabled,epl.sold_flag,epl.expiry_date,epl.updatedby,p.quantity
                 FROM " . DB_PREFIX . "etsy_products_list epl
                 LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id=epl.id_product) 
                 WHERE epl.listing_id IS NULL AND epl.renew_flag = '0' AND 
                 epl.delete_flag = '0' AND epl.is_disabled = 0  AND p.quantity>0
                 ORDER BY epl.id_etsy_products_list ASC LIMIT " . $limit);/*AND 
                 epl.error_flag = 0*/

        } else {

            $productsToListOnEtsy = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NULL AND renew_flag = '0' AND delete_flag = '0'  AND is_disabled = 0 AND id_product = '" . $product_id . "'");

        }

        if ($productsToListOnEtsy->num_rows > 0) {

            return $productsToListOnEtsy->rows;

        } else {

            return false;

        }

    }



    public function getProductsToUpdateOnEtsy($product_id = "", $limit = 20) {

        if ($product_id == "") {

            $productsToUpdate = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL AND (listing_status = 'Listed' OR listing_status = 'Inactive') AND (update_flag = '1' OR delete_flag = '1' OR renew_flag = '1') AND is_disabled = 0 AND error_flag = 0 ORDER BY id_etsy_products_list ASC LIMIT " . $limit);

        } else {

            $productsToUpdate = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL AND (listing_status = 'Listed' OR listing_status = 'Inactive') AND (update_flag = '1' OR delete_flag = '1' OR renew_flag = '1') AND is_disabled = 0 AND id_product = '" . $product_id . "'");

        }
        echo"***getProductsToUpdateOnEtsy***<br>";//print("<pre>".print_r ($productsToUpdate->rows,true )."</pre>");
        if ($productsToUpdate->num_rows > 0) { 

            return $productsToUpdate->rows;

        } else {

            return false;

        }

    }



    public function getProductByProductId($product_id, $language_id = 1) {

        $query = $this->db->query("SELECT "

                . "DISTINCT *, "

                . "pd.name AS name, "

                . "p.image, "

                . "m.name AS manufacturer, "

                . "(SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int) $language_id . "') AS weight_class,"

                . "(SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int) $language_id . "') AS length_class,"

                . " p.sort_order "

                . "FROM " . DB_PREFIX . "product p "

                . "LEFT JOIN " . DB_PREFIX . "product_description pd "

                . "ON (p.product_id = pd.product_id) "

                . "LEFT JOIN " . DB_PREFIX . "manufacturer m "

                . "ON (p.manufacturer_id = m.manufacturer_id) "

                . "WHERE p.product_id = '" . (int) $product_id . "' "

                . "AND pd.language_id = '" . (int) $language_id . "' "

                . "AND p.status = '1' "

                . "AND p.date_available <= NOW()");



        if ($query->num_rows > 0) {

            return $query->row;

        } else {

            return false;

        }

    }



    public function specialPrice($product_id) {

        $query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special ps WHERE product_id = " . $product_id . " AND (ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1");

        if ($query->num_rows > 0) {

            return $query->row["price"];

        } else {

            return "";

        }

    }



    public function checkVariations($product_id) {

        $query = $this->db->query("SELECT count(*) as count FROM `" . DB_PREFIX . "product_option` po INNER JOIN `" . DB_PREFIX . "option` o ON o.option_id = po.`option_id` WHERE o.type IN ('select', 'radio') AND product_id = '" . $product_id . "'");

        if ($query->row['count'] > 0) {

            return true;

        } else {

            return false;

        }

    }



    public function getVariations($product_id) {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option_value` pov "

                . "INNER JOIN `" . DB_PREFIX . "option` o "

                . "ON o.option_id = pov.`option_id` "

                . "WHERE o.type IN ('select', 'radio') AND pov.product_id = '" . $product_id . "' ORDER BY o.option_id ASC");

        if ($query->num_rows > 0) {

            return $query->rows;

        } else {

            return false;

        }

    }



    public function updateListingAddErrorStatus($id_product, $message) {

        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_error = '" . $this->db->escape($message) . "', error_flag = '1' WHERE id_product = '" . $id_product . "'");

    }



    public function updateListingAddStatus($data, $id) {

        $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET listing_id = '" . $data['listing_id'] . "', listing_status = 'Listed', renew_flag = '0', delete_flag = '0', date_listed = NOW(), listing_error = '', error_flag = '0' WHERE id_product = '" . (int) $id . "' AND id_product_attribute = '0'");

    }



    public function getAttributeMapping($product_id, $option_id, $option_value_id, $language_id) {

        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "etsy_attribute_mapping` eam "

                . "INNER JOIN `" . DB_PREFIX . "product_option_value` poc "

                . "ON eam.option_id = poc.`option_id` "

                . "INNER JOIN `" . DB_PREFIX . "option_value_description` ovc "

                . "ON poc.option_value_id = ovc.`option_value_id` "

                . "WHERE poc.option_id = '" . $option_id . "' "

                . "AND poc.product_id = '" . $product_id . "' "

                . "AND poc.option_value_id = '" . $option_value_id . "' "

                . "AND language_id = '" . $language_id . "'");

        if ($query->num_rows > 0) {

            return $query->row;

        } else {

            return false;

        }

    }



    public function getProductsListedOnEtsy() {

        $listedProduct = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_products_list WHERE listing_id IS NOT NULL");//

        if ($listedProduct->num_rows > 0) {

            return $listedProduct->rows;

        } else {

            return false;

        }

    }



    public function getImagesByProductId($product_id) {

        $images = array();

        $existing_images = array();



        $query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = " . (int) $product_id);

        if ($query->num_rows > 0) {

            $images[] = array("main_image" => 1, "image" => $query->row["image"]);

            $additional_images = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int) $product_id . "' GROUP BY image ORDER BY sort_order ASC");

            if ($additional_images->num_rows > 0) {

                $additional_images_array = $additional_images->rows;

                foreach ($additional_images_array as $additional_image) {

                    $images[] = array("main_image" => 0, "image" => $additional_image["image"]);

                }

            }



            /* Comparision of products images with DB ETSY Images. Then either Delete/Update/Create the images */

            $images_to_delete = array();

            $images_to_update = array();



            $query_existing_images = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_images WHERE product_id = '" . (int) $product_id . "' ORDER BY main_image DESC, image_id ASC");

            if ($query_existing_images->num_rows > 0) {

                $existing_images = $query_existing_images->rows;

                foreach ($existing_images as $existing_image) {



                    /* Check if DB image exists in final image list. Set delete flag to false. IF false image not to be deleted. */

                    $delete_flag = true;

                    foreach ($images as $image) {

                        if ($image['image'] == $existing_image['image_url']) {

                            $delete_flag = false;

                            break;

                        }

                    }



                    /* Delete images from DB if etsy_image_id is NULL. If etsy_image_id is not null then need to delete the from etsy as well */

                    if ($delete_flag == true) {

                        if ($existing_image['etsy_image_id'] == NULL) {

                            $this->db->query("DELETE FROM " . DB_PREFIX . "etsy_images WHERE image_id = '" . (int) $existing_image['image_id'] . "'");

                        } else {

                            $images_to_delete[] = array("etsy_image_id" => $existing_image['etsy_image_id'], "image_id" => $existing_image['image_id'], 'main_image' => $existing_image['main_image']);

                        }

                    } else if ($existing_image['etsy_image_id'] == NULL) {

                        /* If etsy image id not null & image already avaliable in etsy_image_table then include the images_to_add array list */

                        $images_to_update[] = array('image_id' => $existing_image['image_id'], 'image' => $existing_image['image_url'], 'main_image' => $existing_image['main_image'], 'update' => 0);

                    } else if ($existing_image['update_flag'] == 1) {

                        /* If image not to be deleted & update flag is equal to 1 then update the images on the etsy. Update_flag =1 is to force update the image */

                        $images_to_update[] = array('etsy_image_id' => $existing_image['etsy_image_id'], 'image' => $existing_image['image_url'], "image_id" => $existing_image['image_id'], 'main_image' => $existing_image['main_image'], 'update' => 1);

                    } else {

                        /* update flag = 0 means add, update flag = 1 means update image with content, update_flag =2 means update image rank only */

                        $images_to_update[] = array('etsy_image_id' => $existing_image['etsy_image_id'], 'image' => $existing_image['image_url'], "image_id" => $existing_image['image_id'], 'main_image' => $existing_image['main_image'], 'update' => 2);

                    }

                }

            }



            /* Search DB Images in images[] array to find the updated & delete image

             * For example DB Images 5,6,7 & images[] = 5,6,8. To find out the 7, above loop will work. 

             * 8 is the new addition. TO find out the 8, need to iterate the images[] array & search in DB array

             */

            if (!empty($images)) {

                foreach ($images as $image) {



                    $new_flag = true;

                    foreach ($existing_images as $existing_image) {

                        if ($image["image"] == $existing_image['image_url']) {

                            $new_flag = false;

                            break;

                        }

                    }



                    /* Delete images from DB if etsy_image_id is NULL. If etsy_image_id is not null then need to delete the from etsy as well */

                    if ($new_flag == true) {

                        $this->db->query("INSERT INTO " . DB_PREFIX . "etsy_images "

                                . "SET product_id = '" . $product_id . "',"

                                . "image_url = '" . $this->db->escape($image["image"]) . "',"

                                . "main_image = '" . $image["main_image"] . "'");

                        $image_id = $this->db->getLastId();

                        /* Update flag = 0 means image needs to be added else needs to be updated. */

                        $images_to_update[] = array("image_id" => $image_id, "image" => $image["image"], 'main_image' => $image['main_image'], 'update' => 0);

                    }

                }

            }

        }

        $final_images = array("delete" => $images_to_delete, "update" => $images_to_update);

        return $final_images;

    }



    public function getDefaultCurrency() {

        $result = $this->db->query("SELECT * FROM " . DB_PREFIX . "currency WHERE value = 1");

        if ($result->num_rows > 0) {

            return $result->row;

        } else {

            return false;

        }

    }



}

