<?php
// Original: shopmanager/catalog/product_specific.php
namespace Opencart\Admin\Model\Shopmanager\Catalog;

class ProductSpecific extends \Opencart\System\Engine\Model {
    
    // 1. Récupérer une clé spécifique par clé et category_id
    public function getSpecificKey($specific_key, $category_id, $language_id =1) {
        $query = $this->db->query("SELECT sm.replacement_term 
                                   FROM " . DB_PREFIX . "product_specific_mappings sm
                                   JOIN " . DB_PREFIX . "product_specifics ps ON sm.specific_id = ps.id
                                   WHERE sm.language_id='".$language_id."' AND ps.specific_key = '" . $this->db->escape($specific_key) . "' 
                                   AND ps.category_id = '" . (int)$category_id . "'");

        if ($query->num_rows) {
          //print("<pre>" . print_r($query->rows, true) . "</pre>");
            return $query->row['replacement_term'];
        }

        return 'not_set'; // Si aucune correspondance n'est trouvée
    }

    // 2. Ajouter une nouvelle clé spécifique avec un terme de remplacement
    public function addSpecificKey($specific_key, $category_id, $replacement_term, $language_id =1) {
        // Ajouter la clé spécifique dans la table 'product_specifics'
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_specifics (category_id, specific_key) 
                          VALUES ('" . (int)$category_id . "', '" . $this->db->escape($specific_key) . "')");

        $specific_id = $this->db->getLastId(); // Récupérer l'ID de la clé spécifique insérée

        // Ajouter le terme de remplacement dans la table 'product_specific_mappings'
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_specific_mappings (specific_id, category_id, replacement_term,language_id ) 
                          VALUES ('" . (int)$specific_id . "', '" . (int)$category_id . "', '" . $this->db->escape($replacement_term) . "','".$language_id."')");

        return $specific_id; // Retourner l'ID de la clé spécifique ajoutée
    }

    // 3. Modifier une clé spécifique existante
    public function editSpecificKey($specific_key, $category_id, $replacement_term, $language_id =1) {
        // Modifier le terme de remplacement pour la clé spécifique donnée
        $this->db->query("UPDATE " . DB_PREFIX . "product_specific_mappings sm
                          JOIN " . DB_PREFIX . "product_specifics ps ON sm.specific_id = ps.id
                          SET sm.replacement_term = '" . $this->db->escape($replacement_term) . "' 
                          WHERE sm.language_id='".$language_id."' AND ps.specific_key = '" . $this->db->escape($specific_key) . "' 
                          AND ps.category_id = '" . (int)$category_id . "'");
    }

    // 4. Supprimer une clé spécifique
    public function deleteSpecificKey($specific_key, $category_id) {
        // Récupérer l'ID de la clé spécifique à partir de la table 'product_specifics'
        $query = $this->db->query("SELECT id FROM " . DB_PREFIX . "product_specifics 
                                   WHERE specific_key = '" . $this->db->escape($specific_key) . "' 
                                   AND category_id = '" . (int)$category_id . "'");
    
        if ($query->num_rows) {
            $specific_id = $query->row['id'];
    
            // Supprimer la clé spécifique et les mappings associés
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_specific_mappings WHERE specific_id = '" . (int)$specific_id . "'");
            $this->db->query("DELETE FROM " . DB_PREFIX . "product_specifics WHERE id = '" . (int)$specific_id . "'");
        }
    }

    // 5. Récupérer toutes les clés spécifiques pour une catégorie
    public function getAllSpecificKeysByCategory($category_id, $language_id =1) {
        $query = $this->db->query("SELECT ps.specific_key, sm.replacement_term 
                                   FROM " . DB_PREFIX . "product_specifics ps
                                   JOIN " . DB_PREFIX . "product_specific_mappings sm ON sm.specific_id = ps.id
                                   WHERE sm.language_id='".$language_id."' AND ps.category_id = '" . (int)$category_id . "'");

        return $query->rows; // Retourner toutes les clés spécifiques et leurs termes
    }
    public function findtranslated_term($replacement_term,$language_id=1){
        $checkQuery = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_specific_mappings` 
        WHERE `replacement_term` = '" . $this->db->escape($replacement_term) . "' 
        AND language_id = ". $language_id."  GROUP BY `replacement_term` ");
    //print("<pre>" . print_r($checkQuery->rows, true) . "</pre>");
        if ($checkQuery->num_rows > 0) {
            $updateQuery = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_specific_mappings` 
            WHERE `specific_id` = '" . $checkQuery->row['specific_id'] . "' 
            AND language_id = 2  GROUP BY `replacement_term` ");
     //print("<pre>" . print_r($updateQuery->rows, true) . "</pre>");
            if ($updateQuery->num_rows > 0) {
                return $updateQuery->row['replacement_term'];
            }else{
                return null;
            }

        }else{
            return null;
        }
    }
    public function translateMissingTerms() {
        // Charger le modèle pour les langues et la traduction
        $this->load->model('localisation/language');
        //$this->load->model('shopmanager/translate");
      
    
        // Récupérer toutes les langues disponibles
        $languages = $this->model_localisation_language->getLanguages();
     //print("<pre>" . print_r($languages, true) . "</pre>");
        $languages_info = $this->model_localisation_language->getLanguageByCode('en');
        $language_id = $languages_info['language_id'];
        // Requête pour sélectionner les termes avec language_id=1 et grouper par replacement_term
        $query = $this->db->query("SELECT * 
                                    FROM `" . DB_PREFIX . "product_specific_mappings` 
                                    WHERE  language_id = ".$language_id." 
                                    GROUP BY `replacement_term` 
                                    ORDER BY `replacement_term` ");
    
        // Boucle sur chaque terme retourné
        unset($languages[$language_id]);
      //print("<pre>" . print_r($query->rows, true) . "</pre>");
        foreach ($query->rows as $row) {
           
            $replacement_term = $row['replacement_term'];
            foreach ($languages as $language) {
                if ($language['code'] != 'en') {
            // Vérifier si le terme a déjà une traduction pour language_id=2
                $checkQuery = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_specific_mappings` 
                                                WHERE `replacement_term` = '" . $this->db->escape($replacement_term) . "' 
                                                AND language_id = ". $language['language_id']."  GROUP BY `replacement_term` ");
            //print("<pre>" . print_r($checkQuery->rows, true) . "</pre>");
            // Si aucune traduction n'est trouvée pour language_id=2, faire la traduction
                    if ($checkQuery->num_rows == 0) {
                        $updateQuery = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_specific_mappings` 
                        WHERE `replacement_term` = '" . $this->db->escape($replacement_term) . "' 
                        AND language_id = ". $language_id);


                       
                        foreach ($updateQuery->rows as $updaterow) {

                            $specific_id = $updaterow['specific_id'];
                            $category_id = $updaterow['category_id'];
                // Boucler à travers chaque langue pour traduire le terme
                            if($replacement_term!=''){
                                // Utiliser le modèle de traduction pour traduire le replacement_term dans la langue courante
                                $translated_term = $this->model_shopmanager_translate->translate($replacement_term, $language['code']);
                                $translated_term = str_replace('&#39;', "\'", $translated_term);
                                // Insérer la traduction dans la table " . DB_PREFIX . "product_specific_mappings pour cette langue
                                $this->db->query("INSERT INTO `" . DB_PREFIX . "product_specific_mappings` 
                                                (specific_id,category_id, language_id, replacement_term) 
                                                VALUES ('" . $this->db->escape($specific_id) . "','" . $this->db->escape($category_id) . "', '" . (int)$language['language_id'] . "', '" . $this->db->escape($translated_term) . "')");
                            }else{
                                $this->db->query("INSERT INTO `" . DB_PREFIX . "product_specific_mappings` 
                                                (specific_id,category_id, language_id, replacement_term) 
                                                VALUES ('" . $this->db->escape($specific_id) . "','" . $this->db->escape($category_id) . "', '" . (int)$language['language_id'] . "', '" . $this->db->escape('') . "')");
                            }
                        }
                    }
                }
            }
        }
    }
    
   
}


