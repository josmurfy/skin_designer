<?php

class ModelKbetsyOrder extends Model {

    public function updateSyncStatusDate($data, $store_id = 0) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE store_id = '" . (int) $store_id . "' AND `code` = 'sync_orders_status_date'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int) $store_id . "', `code` = 'sync_orders_status_date', `key` = 'sync_orders_status_date', `value` = '" . $this->db->escape($data) . "'");
    }

    public function createOrder($data) {
        $etsy_country_query = $this->db->query("SELECT iso_code FROM `" . DB_PREFIX . "etsy_countries` WHERE `country_id` = '" . $data['payment_country_id'] . "' LIMIT 1");
        if ($etsy_country_query->num_rows > 0) {
            $country_query = $this->db->query("SELECT country_id FROM `" . DB_PREFIX . "country` WHERE `iso_code_2` = '" . $etsy_country_query->row['iso_code'] . "' OR `iso_code_3` = '" . $etsy_country_query->row['iso_code'] . "' OR `name` = '" . $etsy_country_query->row['iso_code'] . "' LIMIT 1");
            if ($country_query->num_rows > 0) {
                $data['payment_country_id'] = $country_query->row['country_id'];
                $data['shipping_country_id'] = $country_query->row['country_id'];
                $state_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE `country_id` = '" . $data['payment_country_id'] . "' AND (`name` = '" . $data['state'] . "' OR`code` LIKE '%" . $data['state'] . "%') LIMIT 1");
                if ($state_query->num_rows > 0) {
                    $data['payment_zone_id'] = $state_query->row['zone_id'];
                    $data['shipping_zone_id'] = $state_query->row['zone_id'];
                }
            }
        }
       // $data['payment_zone'] = $data['state'];
       // $data['shipping_zone'] = $data['state'];

        $sql = "INSERT INTO `" . DB_PREFIX . "order` SET "
                . "invoice_prefix = '0', "
                . "store_id = '" . $data['store_id'] . "', "
                . "store_name = '" . $data['store_name'] . "', "
                . "store_url = '" . $data['store_url'] . "', "
                . "firstname = '" . $this->db->escape($data['firstname']) . "', "
                . "lastname = '" . $this->db->escape($data['lastname']) . "', "
                . "email = '" . $this->db->escape($data['email']) . "', "
                . "telephone = '" . $this->db->escape($data['telephone']) . "', "
                . "fax = '" . $data['fax'] . "', "
                . "payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', "
                . "payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', "
                . "payment_company = '" . $data['payment_company'] . "', "
                . "payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', "
                . "payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', "
                . "payment_city = '" . $this->db->escape($data['payment_city']) . "', "
                . "payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', "
                . "payment_country = '" . $this->db->escape($data['payment_country']) . "', "
                . "payment_country_id = '" . (int) $data['payment_country_id'] . "', "
                . "payment_zone = '" . $data['payment_zone'] . "', "
                . "payment_zone_id = '" . $data['payment_zone_id'] . "', "
                . "payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', "
                . "payment_method = '" . $this->db->escape($data['payment_method']) . "', "
                . "payment_code = '" . $data['payment_code'] . "', "
                . "shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', "
                . "shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', "
                . "shipping_company = '" . $data['shipping_company'] . "', "
                . "shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', "
                . "shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', "
                . "shipping_city = '" . $this->db->escape($data['shipping_city']) . "', "
                . "shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', "
                . "shipping_country = '" . $this->db->escape($data['shipping_country']) . "', "
                . "shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', "
                . "shipping_zone_id = '" . $data['shipping_zone_id'] . "', "
                . "shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', "
                . "shipping_method = '" . $this->db->escape($data['shipping_method']) . "', "
                . "shipping_code = '" . $this->db->escape($data['shipping_code']) . "', "
                . "comment = '" . $this->db->escape($data['comment']) . "', "
                . "total = '" . $data['total'] . "', "
                . "order_status_id = '" . $data['order_status_id'] . "', "
                . "affiliate_id  = '" . $data['affiliate_id'] . "', "
                . "language_id = '" . (int) $data['language_id'] . "', "
                . "currency_id = '" . (int) $data['currency_id'] . "', "
                . "currency_code = '" . $this->db->escape($data['currency_code']) . "', "
                . "currency_value = '" . (float) $data['currency_value'] . "', "
                . "date_added = '" . $data['date_added'] . "', "
                . "date_modified = '" . $data['date_modified'] . "'";
               // echo $sql;
        $this->db->query($sql);
        $inserted_id = $this->db->getLastId();
        if ($inserted_id > 0) {
            return $inserted_id;
        } else {
            return false;
        }
    }

    public function createOrderProducts($order_product, $order_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET  order_id = '" . (int) $order_id . "', product_id = '" . (int) $order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (int) $order_product['quantity'] . "', price = '" . (float) $order_product['price'] . "', total = '" . (float) $order_product['total'] . "', tax = '" . (float) $order_product['tax'] . "', reward = '" . (int) $order_product['reward'] . "'");
        $order_product_id = $this->db->getLastId();
        if ($order_product_id) {
            return $order_product_id;
        }
        return false;
    }

    public function createOrderOptions($data, $order_id, $order_product_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int) $order_id . "', order_product_id = '" . (int) $order_product_id . "', product_option_id = '" . (int) $data['product_option_id'] . "', product_option_value_id = '" . (int) $data['product_option_value_id'] . "', name = '" . $data['name'] . "', `value` = '" . $this->db->escape($data['value']) . "', `type` = 'select'");
        //$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int) $data->QuantityPurchased . "), ebay_quantity_sync = '".true."'  WHERE product_option_value_id = '" . (int) $order_product_option_value_id . "' AND subtract = '1'");
        return true;
    }

    public function addHistory($order_status_id, $order_id) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int) $order_id . "', order_status_id = '" . (int) $order_status_id . "', notify = '', comment = '', date_added = '" . date("Y-m-d H:i:s") . "'");
        return true;
    }

    public function insertOrderTotal($data, $order_id) {
        $total = 0;
        $total_data = array();
        $total_data[0] = array(
            'code' => 'shipping',
            'title' => $data['shipping_method'],
            'text' => $this->currency->format((string) $data['total_shipping_cost'], $data['currency_code'], 1),
            'value' => (string) $data['total_shipping_cost'],
            'sort_order' => '3');
        $sub_total = (string) $data['subtotal'];
        $total_data[1] = array(
            'code' => 'sub_total',
            'title' => 'Sub-Total',
            'text' => $this->currency->format($sub_total, $data['currency_code'], 1),
            'value' => $sub_total,
            'sort_order' => '1'
        );

        if ((float) $data['total_tax'] > (float) 0.0) {
            $tax = (float) $data['total_tax'];
            $total_data[3] = array(
                'code' => 'tax',
                'title' => 'Tax',
                'text' => $this->currency->format($tax, $data['currency_code'], 1),
                'value' => $tax,
                'sort_order' => '4'
            );
        }

        $total = (string) $data['total'];
        $total_data[2] = array(
            'code' => 'total',
            'title' => 'Total',
            'text' => $this->currency->format(max(0, $total), $data['currency_code'], 1),
            'value' => max(0, $total),
            'sort_order' => '9'
        );

        if (isset($total_data)) {
            foreach ($total_data as $order_total) {
                $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int) $order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', `value` = '" . (float) $order_total['value'] . "', sort_order = '" . (int) $order_total['sort_order'] . "'");
            }
        }
    }

}
