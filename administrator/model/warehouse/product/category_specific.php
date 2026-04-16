<?php
// Original: warehouse/product/category_specific.php
namespace Opencart\Admin\Model\Warehouse\Product;

class CategorySpecific extends \Opencart\System\Engine\Model {

    // Récupère la liste des spécificités de catégories avec filtres et pagination
    public function getCategorySpecifics($data = array()) {
        $sql = "SELECT * FROM " . DB_PREFIX . "category_specifics WHERE 1";

        // Filtrage par nom spécifique
        if (!empty($data['filter_specific_name'])) {
            $sql .= " AND specific_name LIKE '%" . $this->db->escape($data['filter_specific_name']) . "%'";
        }

        // Ajout du tri et de l'ordre
        $sort_data = array(
            'specific_name'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY specific_name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        // Pagination
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
		//	//print("<pre>".print_r($sql, true)."</pre>");

        return $query->rows;
    }

    // Calcul du total des spécificités de catégories
    public function getTotalCategorySpecifics($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category_specifics WHERE 1";

        // Filtrage par nom spécifique
        if (!empty($data['filter_specific_name'])) {
            $sql .= " AND specific_name LIKE '%" . $this->db->escape($data['filter_specific_name']) . "%'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    // Ajout ou modification d'une traduction pour une spécificité de catégorie
    public function editCategorySpecificTranslation($specific_name, $translations) {
        // Requête pour vérifier si la spécificité existe déjà
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_specifics WHERE specific_name = '" . $this->db->escape($specific_name) . "'");

        if ($query->num_rows) {
            // Mise à jour des traductions existantes
            $this->db->query("UPDATE " . DB_PREFIX . "category_specifics SET translations = '" . $this->db->escape(json_encode($translations)) . "', updated_at = NOW() WHERE specific_name = '" . $this->db->escape($specific_name) . "'");
        } else {
            // Insertion d'une nouvelle spécificité avec ses traductions
            $this->db->query("INSERT INTO " . DB_PREFIX . "category_specifics SET specific_name = '" . $this->db->escape($specific_name) . "', translations = '" . $this->db->escape(json_encode($translations)) . "', created_at = NOW(), updated_at = NOW()");
        }
    }
    public function excludeSpecific($specific_name) {
        // Exécute la requête pour mettre à jour la colonne "exclude" à 1
        $this->db->query(
            "UPDATE " . DB_PREFIX . "category_specifics 
             SET exclude = 0, updated_at = NOW() 
             WHERE specific_name = '" . $this->db->escape($specific_name) . "'"
        );
        //print("<pre>".print_r('exclure', true)."</pre>");
        // Retourne true si la requête a modifié des lignes, sinon false
        return $this->db->countAffected() > 0;
    }
    // Suppression d'une spécificité de catégorie
    public function deleteCategorySpecific($category_specifics_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "category_specifics WHERE category_specifics_id = '" . (int)$category_specifics_id . "'");
    }

    public function getCategoriesWithoutSpecifics($language_id = 1) {
        if ($language_id === null) {
            $language_id = (int)$this->config->get('config_language_id');
        }
    
        $sql = "SELECT DISTINCT cd.category_id, cd.name, c.site_id
                FROM " . DB_PREFIX . "category_description cd 
                LEFT JOIN " . DB_PREFIX . "category c ON (cd.category_id = c.category_id)
                LEFT JOIN " . DB_PREFIX . "product_to_category ptc ON (cd.category_id = ptc.category_id)
                LEFT JOIN " . DB_PREFIX . "product p ON (ptc.product_id = p.product_id)
                WHERE c.leaf=1 AND (cd.specifics IS NULL OR cd.specifics = '' OR cd.specifics = '[]') 
                AND (p.quantity + p.unallocated_quantity) > 0
                AND cd.language_id = '" . (int)$language_id . "' ";//LIMIT 1
    
        $query = $this->db->query($sql);
    //print("<pre>".print_r($sql, true)."</pre>");
      //print("<pre>".print_r($query, true)."</pre>");
        $this->load->model('warehouse/marketplace/ebay/api'); 
        foreach($query->rows as $row){
            $this->model_warehouse_marketplace_ebay_api->getCategorySpecifics($row['category_id'],$row['site_id'], 1);
        }
      //  return $query->rows;
    }
    
}

