<?php
// Original: shopmanager/fast_add.php
namespace Opencart\Admin\Model\Shopmanager;

class FastAdd extends \Opencart\System\Engine\Model {


	private $domain;

    public function __construct($registry) {
        parent::__construct($registry);
        if (defined('HTTPS_CATALOG')) {
            $this->domain = constant('HTTPS_CATALOG');
        } elseif (defined('HTTP_CATALOG')) {
            $this->domain = constant('HTTP_CATALOG');
        } else {
            $this->domain = '';
        }
    }
        
        // Ajouter un nouveau produit
        public function checkProductExists($upc, $condition_id) {
            // Vérifier si le produit existe déjà avec le même UPC et condition_id
            $query = $this->db->query("SELECT product_id, sku, quantity+unallocated_quantity AS total_quantity FROM " . DB_PREFIX . "product 
                                       WHERE upc = '" . $this->db->escape($upc) . "' 
                                       AND condition_id = '" . (int)$condition_id . "'");
            
            if ($query->num_rows > 0) {
                // Retourner le SKU si le produit existe
                return $query->row;
            } else {
                // Retourner null si aucun produit correspondant n'est trouvé
                return null;
            }
        }

        public function incrementQuantity($product_id, $unallocated_quantity = 0, $quantity = 0) {
            $this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = quantity+" . (int)$quantity . ", unallocated_quantity = unallocated_quantity+" . (int)$unallocated_quantity . ", status = 1 
            WHERE product_id = '" . (int)$product_id . "'");
        }
        public function populateDefaultValues($data) {
            // Définir les valeurs par défaut pour chaque champ attendu
            $default_values = array(
                
                'master_id' => 0,
                'variant' => '',
                'override' => '',
                'to_feed' => 1,
                'marketplace_item_id' => 0,
                'model' => 'n/a',
                'sku' => '',
                'upc' => '',
                'ean' => '',
                'jan' => '',
                'isbn' => '',
                'mpn' => '',
                'location' => '',
                'stores' => array(),
                'product_store' => array(0), // Ajouter le store par défaut avec ID 0
                'shipping' => 1, // Activer le shipping par défaut
                'price' => 0.0, // Mettre le prix à 0 par défaut
                'price_with_shipping' => 0.0,
                'shipping_cost' => 0.0,
                'shipping_carrier' => '',
                'recurrings' => array(),
                'product_recurrings' => array(),
                'date_available' => date('Y-m-d'),
                'date_added' => date('Y-m-d'),
                'date_modified' => date('Y-m-d'),
                'quantity' => 0,
                'unallocated_quantity' => 0,
                'condition_id' => 9,
                'minimum' => 1,
                'subtract' => 1,
                'sort_order' => 0,
                'stock_status_id' => 7,
                'tax_class_id' => 9,
                'status' => 1, // Activer le produit par défaut
                'weight' => 0.0,
                'weight_class_id' => 5,
                'length' => 0.0,
                'width' => 0.0,
                'height' => 0.0,
                'length_class_id' => 3,
                'manufacturer_id' => 0,
                'manufacturer' => 'Unbranded Generic',
                'ebay_info' => '',
                'product_categories' => array(),
                'product_filters' => array(),
                'product_attributes' => array(),
                'product_options' => array(),
                'option_values' => array(),
                'product_discounts' => array(),
                'product_specials' => array(),
                'image' => '',
                'thumb' =>  $this->domain. '/image/cache/no_image-100x100.png',
                'placeholder' =>  $this->domain. '/image/cache/no_image-100x100.png',
                'product_images' => array(),
                'product_downloads' => array(),
                'product_relateds' => array(),
                'points' => 0,
                'product_reward' => array(),
                'product_layout' => array(),
                'product_recurring' => array(),
                'product_description' => array(
                    1 => array( // Langue 1 (par exemple, anglais)
                        'name' => '',
                        'description' => '',
                        'tag' => '',
                        'meta_title' => '',
                        'meta_description' => '',
                        'meta_keyword' => '',
                        'color' => '',
                        'description_supp' => '',
                        'condition_supp' => '',
                        'included_accessories' => '',
                        'specifics' => null,
                        'keyword' => ''
                    ),
                    2 => array( // Langue 2 (par exemple, français)
                        'name' => '',
                        'description' => '',
                        'tag' => '',
                        'meta_title' => '',
                        'meta_description' => '',
                        'meta_keyword' => '',
                        'color' => '',
                        'description_supp' => '',
                        'condition_supp' => '',
                        'included_accessories' => '',
                        'specifics' => null,
                        'keyword' => ''
                    )
                )
            );
        
            // Parcourir les valeurs par défaut et les attribuer si elles sont absentes dans $data
            foreach ($default_values as $key => $default) {
                if (!isset($data[$key])) {
                    $data[$key] = $default;
                }
            }
        
            // Parcourir les descriptions de produits pour chaque langue et compléter les valeurs manquantes
            foreach ($default_values['product_description'] as $language_id => $default_description) {
                if (!isset($data['product_description'][$language_id])) {
                    $data['product_description'][$language_id] = $default_description;
                } else {
                    // Compléter les champs manquants dans chaque description de produit
                    foreach ($default_description as $desc_key => $desc_value) {
                        if (!isset($data['product_description'][$language_id][$desc_key])) {
                            $data['product_description'][$language_id][$desc_key] = $desc_value;
                        }
                    }
                }
            }
        
            return $data;
        }
        
    
        // Récupérer les informations d'un produit spécifique
        public function getProduct($product_id) {
		
            $query = $this->db->query("SELECT DISTINCT *,pd.name AS name_description,pd.color AS color_description , condition_id ,
            (SELECT name FROM " . DB_PREFIX . "manufacturer ma WHERE  p.manufacturer_id  = ma.manufacturer_id ) AS brand,
            (SELECT name FROM " . DB_PREFIX . "condition c WHERE p.condition_id = c.condition_id AND c.language_id = '" . (int)$this->config->get('config_language_id') . "' LIMIT 1) AS condition_name,
            (SELECT ca.category_id 
                FROM `" . DB_PREFIX . "product_to_category` pc 
                LEFT JOIN `" . DB_PREFIX . "category` ca ON (pc.category_id = ca.category_id)
                LEFT JOIN `" . DB_PREFIX . "category_description` cad ON (ca.category_id = cad.category_id AND cad.language_id = '" . (int)$this->config->get('config_language_id') . "' )
                WHERE pc.product_id = '" . (int)$product_id . " ' AND ca.leaf = 0
                 LIMIT 1) AS category_id,
            (SELECT cad.name
                FROM `" . DB_PREFIX . "product_to_category` pc 
                LEFT JOIN `" . DB_PREFIX . "category` ca ON (pc.category_id = ca.category_id)
                LEFT JOIN `" . DB_PREFIX . "category_description` cad ON (ca.category_id = cad.category_id AND cad.language_id = '" . (int)$this->config->get('config_language_id') . "'  )
                WHERE pc.product_id = '" . (int)$product_id . "' AND ca.leaf = 0
                 LIMIT 1) AS category_name
             FROM " . DB_PREFIX . "product p 
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) 
            
            WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");//, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword
        //	//print("<pre>" . print_r($query->row, true) . "</pre>");	
            return $query->row;
    
    
        }
    
        // Récupérer la liste des produits
        public function getProducts($data = array()) {
            $sql = "SELECT p.*, 
            ptc.category_id, 
            pd.name,
            IF(pd.specifics IS NOT NULL AND pd.specifics != '', '1', '0') AS has_specifics,
            IF(pis.upc IS NOT NULL, '1', '0') AS has_sources,  
            (SELECT c.name 
             FROM " . DB_PREFIX . "condition c 
             WHERE p.condition_id = c.condition_id 
               AND c.language_id = '" . (int)$this->config->get('config_language_id') . "' 
             LIMIT 1
            ) AS condition_name 
            FROM " . DB_PREFIX . "product p
            LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id)
            LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            LEFT JOIN " . DB_PREFIX . "product_info_sources pis ON (pis.upc = p.upc)  
            WHERE p.to_feed = 0 
            AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

    
    
            if (!empty($data['filter_sku'])) {
                $sql .= " AND (p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "%' OR p.upc LIKE '" . $this->db->escape($data['filter_sku']) . "%' OR p.product_id LIKE '" . $this->db->escape($data['filter_sku']) . "%') ";
            }
    
            if (!empty($data['filter_product_id'])) {
                $sql .= " AND p.product_id LIKE '" . $this->db->escape($data['filter_product_id']) . "%'";
            }
    
            if (!empty($data['filter_marketplace_account'])) {
                $sql .= " AND p.marketplace_item_id LIKE '" . $this->db->escape($data['filter_marketplace_account']) . "%'";
            }
            
            if (!empty($data['filter_name'])) {
                $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
            }
    
            if (!empty($data['filter_category_id'])) {
                $sql .= " AND ptc.category_id LIKE '" . $this->db->escape($data['filter_category_id']) . "%'";
            }
    
            if (!empty($data['filter_model'])) {
                $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
            }
    
            if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
                $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
            }
    
            if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
                $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
            }
            if (isset($data['filter_unallocated_quantity']) && !is_null($data['filter_unallocated_quantity'])) {
                $sql .= " AND p.unallocated_quantity = '" . (int)$data['filter_unallocated_quantity'] . "'";
            }
            if (isset($data['filter_location']) && !is_null($data['filter_location'])) {
                $sql .= " AND p.location = '" . (int)$data['filter_location'] . "'";
            }
    
            if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
                $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
            }
    
            if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
                if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
                    $sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
                } else {
                    $sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
                }
            }
    
            $sql .= " GROUP BY p.product_id";
    
            $sort_data = array(
                'p.condition_id',
                'p.marketplace_item_id',
                'pd.name',
                'cts.category_id',
                'p.price',
                'p.quantity',
                'p.unallocated_quantity',
                'p.location',
                'p.status',
                'p.sort_order',
                'has_specifics',
                'has_sources',
            );
    
            if (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2) {
                $sql .= " ORDER BY p.quantity";
            }elseif (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
                $sql .= " ORDER BY " . $data['sort'];
            } else {
                $sql .= " ORDER BY pd.name";
            }
    
            if (isset($data['order']) && ($data['order'] == 'DESC')) {
                $sql .= " DESC";
            } elseif (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2){
                $sql .= " ASC";
            }else {
                $sql .= " ASC";
            }
    
            if (isset($data['start']) || isset($data['limit'])) {
                if ($data['start'] < 0) {
                    $data['start'] = 0;
                }
    
                if ($data['limit'] < 1) {
                    $data['limit'] = 20;
                }
    
                $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
            }
            
       //print("<pre>".print_r ($sql,true )."</pre>");
            $query = $this->db->query($sql);
    
            if (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2){
                $rows=$this->removeProductsNoMissingFile($query->rows);
            }else{
                $rows=$query->rows;
            //	//print("<pre>".print_r ($rows,true )."</pre>");
            }
    
            return $rows;
        }
        public function getProductOptionValue($product_id, $product_option_value_id) {
            $query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
    
            return $query->row;
        }
    
        public function getProductImages($product_id) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");
    
            return $query->rows;
        }
    
        public function getProductDiscounts($product_id) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");
    
            return $query->rows;
        }
    
        public function getProductSpecials($product_id) {
            $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");
    
            return $query->rows;
        }
    	public function getTotalProducts($data = array()) {
            $sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
            $sql	.="LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id) ";
            $sql	.= " WHERE p. to_feed=1 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
    
            if (!empty($data['filter_sku'])) {
                $sql .= " AND (p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "' OR p.upc LIKE '" . $this->db->escape($data['filter_sku']) . "') ";
            }
    
            if (!empty($data['filter_product_id'])) {
                $sql .= " AND p.product_id = '" . $this->db->escape($data['filter_product_id']) . "'";
            }
    
            if (!empty($data['filter_marketplace_account'])) {
                $sql .= " AND p.marketplace_item_id = '" . $this->db->escape($data['filter_marketplace_account']) . "'";
            }
    
            if (!empty($data['filter_name'])) {
                $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
            }
    
            if (!empty($data['filter_category_id'])) {
                $sql .= " AND ptc.category_id LIKE '" . $this->db->escape($data['filter_category_id']) . "%'";
            }
    
            if (!empty($data['filter_model'])) {
                $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
            }
    
            if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
                $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
            }
    
            if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
                $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
            }
    
            if (isset($data['filter_unallocated_quantity']) && !is_null($data['filter_unallocated_quantity'])) {
                $sql .= " AND p.unallocated_quantity = '" . (int)$data['filter_unallocated_quantity'] . "'";
            }
    
            if (isset($data['filter_location']) && !is_null($data['filter_location'])) {
                $sql .= " AND p.location = '" . (int)$data['filter_location'] . "'";
            }
    
            if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
                $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
            }
    
            if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
                if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
                    $sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
                } else {
                    $sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
                }
            }
    
            $query = $this->db->query($sql);
    
            if (isset($data['filter_image']) && !is_null($data['filter_image']) && $data['filter_image'] == 2){
                $total=$query->row['total']-$this->removeTotalProductsNoMissingFile($data);
            }else{
                $total=$query->row['total'];
            }
            return $total;
        }
        // Supprimer un produit (si nécessaire)
        public function deleteProduct($product_id) {
            $this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
        }
        private function removeProductsNoMissingFile($results){

			$i=0;
			foreach ($results as $product_info) {
				if (is_file(DIR_IMAGE . $product_info['image'])) {
					unset($results[$i]);
				}
				$i++;
			}
			return $results;
	}
        public function removeTotalProductsNoMissingFile($data = array()) {
            $sql = "SELECT p.image FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";
            $sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (p.product_id = ptc.product_id) ";
            $sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
    
            if (!empty($data['filter_sku'])) {
                $sql .= " AND (p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "' OR p.upc LIKE '" . $this->db->escape($data['filter_sku']) . "') ";
            }
    
            if (!empty($data['filter_product_id'])) {
                $sql .= " AND p.product_id = '" . $this->db->escape($data['filter_product_id']) . "'";
            }
    
            if (!empty($data['filter_marketplace_account'])) {
                $sql .= " AND p.marketplace_item_id = '" . $this->db->escape($data['filter_marketplace_account']) . "'";
            }
    
            if (!empty($data['filter_name'])) {
                $sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
            }
    
            if (!empty($data['filter_category_id'])) {
                $sql .= " AND ptc.category_id LIKE '" . $this->db->escape($data['filter_category_id']) . "%'";
            }
    
            if (!empty($data['filter_model'])) {
                $sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
            }
    
            if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
                $sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
            }
    
            if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
                $sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
            }
    
            if (isset($data['filter_unallocated_quantity']) && !is_null($data['filter_unallocated_quantity'])) {
                $sql .= " AND p.unallocated_quantity = '" . (int)$data['filter_unallocated_quantity'] . "'";
            }
    
            if (isset($data['filter_location']) && !is_null($data['filter_location'])) {
                $sql .= " AND p.location = '" . (int)$data['filter_location'] . "'";
            }
    
            if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
                $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
            }
    
            if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
                if ($data['filter_image'] == 1 || $data['filter_image'] == 2) {
                    $sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
                } else {
                    $sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
                }
            }
    
            $query = $this->db->query($sql);
    
            $total=0;
            $rows=$query->rows;
            
    
                foreach ($rows as $row) {
                    if (is_file(DIR_IMAGE . $row['image'])) {					
                        $total++;
                    }
                }
            //	//print("<pre>".print_r ($total,true )."</pre>");
            return $total;
        }
    }
    
