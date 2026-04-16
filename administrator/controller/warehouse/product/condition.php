<?php
// Original: shopmanager/condition.php
namespace Opencart\Admin\Controller\Shopmanager;

class Condition extends \Opencart\System\Engine\Controller {
    public function getConditionDetails(): void {
        $this->load->language('shopmanager/condition');
        $data = [];
        
        $json = array();

        if (isset($this->request->get['category_id'])) {
            $category_id = (int)$this->request->get['category_id'];

            $this->load->model('shopmanager/condition');
            $this->load->model('localisation/language');

               $languages = $this->model_localisation_language->getLanguageByCode('en-gb');
               $language_id = $languages['language_id'] ?? 1;
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
                $json['erreur'] = ($lang['error_no_conditions'] ?? '');
            }
        } else {
            $json['erreur'] = ($lang['error_no_category'] ?? '');
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
