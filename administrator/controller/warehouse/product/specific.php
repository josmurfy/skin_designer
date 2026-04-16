<?php
// Original: warehouse/product/product_specific.php
namespace Opencart\Admin\Controller\Warehouse\Product;

class Specific extends \Opencart\System\Engine\Controller {
    public function editSpecificKey(): void {
        $json = array();
        
        // Vérifier si les données nécessaires sont présentes
        if (isset($this->request->get['specific_key']) && isset($this->request->get['category_id']) && isset($this->request->get['replacement_term'])) {
            $specific_key = $this->request->get['specific_key'];
            $category_id = (int)$this->request->get['category_id'];
            $replacement_term = $this->request->get['replacement_term'];

            // Charger le modèle
            $this->load->model('warehouse/product/product_specific');

            // Modifier la clé spécifique
            $this->model_warehouse_product_specific->editSpecificKey($specific_key, $category_id, $replacement_term);

            $json['success'] = 'Specific key updated successfully';
        } else {
            $json['error'] = 'Missing data: specific_key, category_id or replacement_term';
        }

        // Retourner une réponse JSON
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    // Fonction pour supprimer une clé spécifique
    public function deleteSpecificKey(): void {
        $json = array();

        // Vérifier si les données nécessaires sont présentes
        if (isset($this->request->get['specific_key']) && isset($this->request->get['category_id'])) {
            $specific_key = $this->request->get['specific_key'];
            $category_id = (int)$this->request->get['category_id'];

            // Charger le modèle
            $this->load->model('warehouse/product/product_specific');

            // Supprimer la clé spécifique
            $this->model_warehouse_product_specific->deleteSpecificKey($specific_key, $category_id);

            $json['success'] = 'Specific key deleted successfully';
        } else {
            $json['error'] = 'Missing data: specific_key or category_id' . json_encode($this->request->get);
        }

        // Retourner une réponse JSON
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    // Fonction pour ajouter une nouvelle clé spécifique
    public function addSpecificKey(): void {
        $json = array();

        // Vérifier si les données nécessaires sont présentes
        if (isset($this->request->get['specific_key']) && isset($this->request->get['category_id']) && isset($this->request->get['replacement_term'])) {
            $specific_key = $this->request->get['specific_key'];
            $category_id = (int)$this->request->get['category_id'];
            $replacement_term = $this->request->get['replacement_term'];

            // Charger le modèle
            $this->load->model('warehouse/product/product_specific');

            // Ajouter la nouvelle clé spécifique
            $specific_id = $this->model_warehouse_product_specific->addSpecificKey($specific_key, $category_id, $replacement_term);

            $json['success'] = 'Specific key added successfully';
            $json['specific_id'] = $specific_id;
        } else {
            $json['error'] = 'Missing data: specific_key, category_id or replacement_term';
        }

        // Retourner une réponse JSON
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    // Fonction pour récupérer une clé spécifique (vérifier si elle existe)
    public function getSpecificKey(): void {
        $json = array();

        // Vérifier si les données nécessaires sont présentes
        if (isset($this->request->get['specific_key']) && isset($this->request->get['category_id'])) {
            $specific_key = $this->request->get['specific_key'];
            $category_id = (int)$this->request->get['category_id'];

            // Charger le modèle
            $this->load->model('warehouse/product/product_specific');

            // Récupérer la clé spécifique
            $replacement_term = $this->model_warehouse_product_specific->getSpecificKey($specific_key, $category_id);

            if ($replacement_term != 'not_set') {
                $json['exists'] = true;
                $json['replacement_term'] = $replacement_term;
            } else {
                $json['exists'] = false;
            }
        } else {
            $json['error'] = 'Missing data: specific_key or category_id';
        }

        // Retourner une réponse JSON
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
