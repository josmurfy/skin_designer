<?php
// Original: warehouse/product/condition.php
namespace Opencart\Admin\Model\Warehouse\Product;

class Condition extends \Opencart\System\Engine\Model {
	

	public function getCondition($condition_id) {
		$query = $this->db->query("SELECT  * 
		FROM " . DB_PREFIX . "condition  
		WHERE condition_id = '" . (int)$condition_id . "'");
    //    AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		
        if ($query->num_rows > 0) {
            foreach ($query->rows as $row) {
                $result[$row['language_id']][$row['condition_id']] =  $row;
            }
        }
    
        return $result;
    }

    public function getConditionEbay($condition_marketplace_item_id) {
		$query = $this->db->query("SELECT  * 
		FROM " . DB_PREFIX . "condition_ebay  
		WHERE condition_marketplace_item_id = '" . (int)$condition_marketplace_item_id . "'");
		
        if ($query->num_rows > 0) {
            foreach ($query->rows as $row) {
                $conditions = json_decode($row['conditions'], true);
    
                if ($conditions && is_array($conditions)) {
                    foreach ($conditions as $cond_id => $cond_value) {
                        // Ajouter les informations au tableau de résultats
                        $result[$cond_id] = [               
                            'condition_id' => $cond_id,      
                            'condition_marketplace_item_id' => $cond_value['value']
                        ];
                    }
                }
            }
        }
    
        return $result;
	}

    public function getConditionEbayToCategory($category_id) {
		$query = $this->db->query("SELECT  * 
		FROM " . DB_PREFIX . "condition_ebay_to_category  
		WHERE category_id = '" . (int)$category_id . "'"); 
		
		return $query->row;
	}

    public function getConditionDetails($category_id = null, $condition_id = null, $condition_marketplace_item_id = null, $site_id = 0) {
        // Construction de la clause WHERE pour condition_id
        $this->load->model('warehouse/marketplace/ebay/api');

        if (is_array($category_id)) {
            $category_id = reset($category_id);
        }

        //$this->load->model('warehouse/tools/translate");
        //print("<pre>".print_r ($category_id,true )."</pre>");
            //print("<pre>".print_r ($condition_id,true )."</pre>");
            //print("<pre>".print_r ($condition_marketplace_item_id,true )."</pre>");
            //print("<pre>".print_r ($site_id,true )."</pre>");

           
       //print("<pre>".print_r ($site_id,true )."</pre>");
        $query = $this->db->query("SELECT ce.conditions, cec.conditions AS conditions_ebay, c.site_id
            FROM " . DB_PREFIX . "condition_ebay_to_category cec
            LEFT JOIN " . DB_PREFIX . "condition_ebay ce ON ce.condition_marketplace_item_id = cec.condition_marketplace_item_id
            LEFT JOIN " . DB_PREFIX . "category c ON c.category_id = cec.category_id
             WHERE cec.category_id = '" . (int)$category_id . "'");
    
        $result = [];
        $conditions_temp = []; // Initialize the variable here
    
        if ($query->num_rows > 0) {
            $row_condition = $query->row;
            // Décoder la colonne `conditions` (qui est un JSON)
            $conditions = $row_condition['conditions'] ? json_decode($row_condition['conditions'], true) : null;
            $conditions_ebay = $row_condition['conditions_ebay'] ? json_decode($row_condition['conditions_ebay'], true) : null;
            $site_id = $row_condition['site_id'];
          
            if (!isset($row_condition['conditions_ebay']) || !is_array($conditions_ebay)) {
                $conditions_ebay = $this->model_warehouse_marketplace_ebay_api->getConditionsByCategory($category_id, 1, $site_id);
                if ($conditions_ebay && is_array($conditions_ebay)) {
                    foreach ($conditions_ebay as $condition_ebay => $name) {
                        $query = $this->db->query("SELECT *
                        FROM " . DB_PREFIX . "condition  
                        WHERE name LIKE '%" . $name . "%'");

                        if ($query->num_rows > 0) {
                            foreach ($query->rows as $matching_key) {
                                //print("<pre>".print_r ($conditions_temp[$matching_key['condition_id']],true )."</pre>");
                                //print("<pre>".print_r ($conditions[$matching_key['condition_id']],true )."</pre>");
                                //print("<pre>".print_r ($matching_key['condition_id_index'],true )."</pre>");
                                $conditions_temp[$matching_key['condition_id']]['value'] = $conditions[$matching_key['condition_id']]['value'];
                                $conditions_temp[$matching_key['condition_id']]['condition_id_index'] = $matching_key['condition_id_index'];
                            }
                        } else {
                            $matching_keys = array();
                            foreach ($matching_keys as $matching_key) {
                                $query = $this->db->query("SELECT condition_id, name, prefix, sort_order
                                                           FROM " . DB_PREFIX . "condition c 
                                                           WHERE c.language_id = 1 
                                                           AND c.condition_id_index < 9  
                                                           AND c.condition_id = '" . (int)$matching_key . "'");
                                $rows_cond_name = $query->row;
                                if ($rows_cond_name) {
                                    if ($rows_cond_name['condition_id'] == 1) {
                                        $text_field = $name;
                                    } else {
                                        $text_field = '(' . $rows_cond_name['name'] . ') ' . $name;
                                    }
                                    $this->db->query("INSERT INTO " . DB_PREFIX . "condition 
                                                      SET condition_id = '" . (int)$matching_key . "', 
                                                          language_id = '1', 
                                                          sort_order = '" . $rows_cond_name['sort_order'] . "', 
                                                          prefix = '" . $this->db->escape($rows_cond_name['prefix']) . "', 
                                                          name = '" . $this->db->escape($text_field) . "'");
                                    $condition_id_index = $this->db->getLastId();
                                    $text_field_translated = $this->model_warehouse_tools_translate->translate($text_field, 'Fr');
                                    $this->db->query("INSERT INTO " . DB_PREFIX . "condition 
                                                      SET condition_id_index = '" . (int)$condition_id_index . "', 
                                                          condition_id = '" . (int)$matching_key . "', 
                                                          language_id = '2', 
                                                          sort_order = '" . $rows_cond_name['sort_order'] . "', 
                                                          prefix = '" . $this->db->escape($rows_cond_name['prefix']) . "', 
                                                          name = '" . $this->db->escape($text_field_translated) . "'");
                                    $conditions_temp[$matching_key]['value'] = $conditions[$matching_key]['value'];
                                    $conditions_temp[$matching_key]['condition_id_index'] = $condition_id_index;
                                }
                            }
                        }
                    }
                }
                $this->db->query("UPDATE `oc_condition_ebay_to_category` SET `conditions` = '" . json_encode($conditions_temp) . "' WHERE `oc_condition_ebay_to_category`.`category_id` ='" . (int)$category_id . "'");
                $row_condition['conditions_ebay'] = json_encode($conditions_temp);
                $conditions_ebay = json_decode($row_condition['conditions_ebay'], true);
            }
            if ($conditions_ebay && is_array($conditions_ebay)) {
                if(isset($condition_id)){
                    $query = $this->db->query("SELECT *
                    FROM " . DB_PREFIX . "condition c 
                    WHERE c.condition_id_index = '" . (int)$conditions_ebay[$condition_id]['condition_id_index'] . "'");

                    //print(json_encode($query));
                    $rows_cond = $query->rows;
                    //print("<pre>".print_r (139,true )."</pre>");
      //print("<pre>".print_r ($rows_cond,true )."</pre>");
       //print("<pre>".print_r (139,true )."</pre>");
                    $result = [];
                    foreach ($rows_cond as $row_cond) {
                         
                        $result[$row_cond['language_id']][$condition_id] = [
                            'condition_name' => isset($row_cond['name']) ? $row_cond['name'] : '',
                            'prefix' => isset($row_cond['prefix']) ? $row_cond['prefix'] : '',
                            'condition_id' => $condition_id,
                            'category_id' => $category_id,
                            'site_id' => $site_id,
                            'condition_marketplace_item_id' => $conditions_ebay[$condition_id]['value'],
                            'ConditionID' => $conditions_ebay[$condition_id]['value'],
                            'sort_order' => $row_cond['sort_order'], 
                        ];   
                        
                    }
                    
                        return $result;
                    
                }

                
                foreach ($conditions_ebay as $condition_id_key => $cond_value) {
                    $condition_id_key = ($condition_id !== null) ? $condition_id : $condition_id_key;
                    $query = $this->db->query("SELECT *
                    FROM " . DB_PREFIX . "condition c 
                    WHERE c.condition_id_index = '" . (int)$cond_value['condition_id_index'] . "'");

                    //print(json_encode($query));
                    $rows_cond = $query->rows;
                    foreach ($rows_cond as $row_cond) {
                        if (isset($condition_marketplace_item_id)) {
                            if ($cond_value['value'] == $condition_marketplace_item_id) {
                                $result[$row_cond['language_id']][$condition_id_key] = [
                                    'condition_name' => isset($row_cond['name']) ? $row_cond['name'] : '',
                                    'prefix' => isset($row_cond['prefix']) ? $row_cond['prefix'] : '',
                                    'condition_id' => $condition_id_key,
                                    'category_id' => $category_id,
                                    'site_id' => $site_id,
                                    'condition_marketplace_item_id' => $cond_value['value'],
                                    'sort_order' => $row_cond['sort_order'], 
                                ];
                                break;
                            }
                        } else {
                            $result[$row_cond['language_id']][$condition_id_key] = [
                                'condition_name' => isset($row_cond['name']) ? $row_cond['name'] : '',
                                'prefix' => isset($row_cond['prefix']) ? $row_cond['prefix'] : '',
                                'condition_id' => $condition_id_key,
                                'category_id' => $category_id,
                                'site_id' => $site_id,
                                'condition_marketplace_item_id' => $cond_value['value'],
                                'sort_order' => $row_cond['sort_order'], 
                            ];
                        }
                    }
                    if ($condition_id !== null) {
                        break;
                    }
                }
            }
        } else {
            //print("<pre>".print_r ($category_id,true )."</pre>");
            //print("<pre>".print_r ($condition_id,true )."</pre>");
            //print("<pre>".print_r ($condition_marketplace_item_id,true )."</pre>");
            //print("<pre>".print_r ($site_id,true )."</pre>");

            

            $query = $this->db->query("INSERT INTO `oc_condition_ebay_to_category` (`category_id`, `condition_marketplace_item_id`, `conditions`) VALUES ('" . $category_id . "', '99', NULL)");
            $this->getConditionDetails($category_id, $condition_id, $condition_marketplace_item_id, $site_id);
        }
        foreach ($result as &$conditions) {
            uasort($conditions, function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
        }
        return $result;
    }
    
    
	public function getConditions($data = array()) {

				/*CREATE INDEX idx_condition_id_cp ON oc_condition_path(condition_id);
CREATE INDEX idx_condition_id_c1 ON oc_condition(condition_id);
CREATE INDEX idx_path_id_cp ON oc_condition_path(path_id);
CREATE INDEX idx_condition_id_c2 ON oc_condition(condition_id);
 */
		$this->db->query("SET SQL_BIG_SELECTS=1");
		$sql = "SELECT *
				FROM " . DB_PREFIX . "condition WHERE 1=1 ";
			//	WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'"; 

		if (!empty($data['filter_name'])) {
			$sql .= " AND name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_condition_id'])) {
			$sql .= " AND condition_id = '" . $this->db->escape($data['filter_condition_id']) . "'";
		}

        if (!empty($data['filter_language_id'])) {
			$sql .= " AND language_id = '" . $this->db->escape($data['filter_language_id']) . "'";
		}


		//$sql .= " GROUP BY condition_id";

		$sort_data = array(
			'name',
			'condition_id',	
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
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

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
	//	//print("<pre>".print_r ($sql,true )."</pre>");
		$query = $this->db->query($sql);
        if (count($query->rows) > 0) {
          //print("<pre>".print_r ($query->row,true )."</pre>");
            if (!empty($data['filter_language_id'])) {
                return $query->rows;

            }else{
                foreach ($query->rows as $row) {
                    $result[$row['language_id']][$row['condition_id']] =  $row;
                }
           //print("<pre>".print_r (count($query->rows),true )."</pre>");
                return $result;
            }
           
        }
     //print("<pre>".print_r ($result,true )."</pre>");
       
	}
   
	

}
