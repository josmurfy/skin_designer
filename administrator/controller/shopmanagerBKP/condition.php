<?php

class ControllerShopmanagerCondition extends Model {
    
    public function getConditionDetails() {
    $this->load->language('shopmanager/condition');
    $json = array();

        if (isset($this->request->get['category_id'])) {
            $category_id = (int)$this->request->get['category_id'];

            $this->load->model('shopmanager/condition');
            $this->load->model('localisation/language');

            //   $current_language = $this->config->get('config_language'); 
           //print("<pre>".print_r ($current_language,true )."</pre>");
       
               $languages = $this->model_localisation_language->getLanguageByCode('en');
               $language_id = $languages['language_id'];
            // Récupérer les conditions pour la catégorie donnée
            //print("<pre>" . print_r(value: '21:CONDITION.php') . "</pre>");
            $conditions = $this->model_shopmanager_condition->getConditionDetails($category_id);

            if (isset($conditions[$language_id])) {
                $json['conditions'] = array();
           //print("<pre>".print_r ($conditions[$language_id],true )."</pre>");
                foreach ($conditions[$language_id] as $condition) {
                    $json['conditions'][] = array(
                        'condition_id' => $condition['condition_id'],
                        'condition_marketplace_item_id' => $condition['condition_marketplace_item_id'],
                        'condition_name' => $condition['condition_name']
                    );
                }
 
            } else {
                $json['erreur'] = $this->language->get('error_no_conditions');
            }
        } else {
            $json['erreur'] = $this->language->get('error_no_category');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

}
