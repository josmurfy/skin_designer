<?php

class ModelModuleHelloWorld extends Model {
    public function getvalue($limit) {
        $this->load->model('catalog/product');
        $product_data = $this->cache->get('product.recent.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit);
 
        if (!$product_data) {
            $query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);
 
            foreach ($query->rows as $result) {
                $product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
            }
 
            $this->cache->set('product.recent.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $this->config->get('config_customer_group_id') . '.' . (int)$limit, $product_data);
        }
 
        return $product_data;
    }
}
