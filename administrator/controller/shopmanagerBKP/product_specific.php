<?
class ControllerShopmanagerProductSpecific extends Controller {



    // Fonction pour modifier une clé spécifique
    public function editSpecificKey() {
        $json = array();
        
        // Vérifier si les données nécessaires sont présentes
        if (isset($this->request->get['specific_key']) && isset($this->request->get['category_id']) && isset($this->request->get['replacement_term'])) {
            $specific_key = $this->request->get['specific_key'];
            $category_id = (int)$this->request->get['category_id'];
            $replacement_term = $this->request->get['replacement_term'];

            // Charger le modèle
            $this->load->model('shopmanager/product_specific');

            // Modifier la clé spécifique
            $this->model_shopmanager_product_specific->editSpecificKey($specific_key, $category_id, $replacement_term);

            $json['success'] = 'Specific key updated successfully';
        } else {
            $json['error'] = 'Missing data: specific_key, category_id or replacement_term';
        }

        // Retourner une réponse JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // Fonction pour supprimer une clé spécifique
    public function deleteSpecificKey() {
        $json = array();

        // Vérifier si les données nécessaires sont présentes
        if (isset($this->request->get['specific_key']) && isset($this->request->get['category_id'])) {
            $specific_key = $this->request->get['specific_key'];
            $category_id = (int)$this->request->get['category_id'];

            // Charger le modèle
            $this->load->model('shopmanager/product_specific');

            // Supprimer la clé spécifique
            $this->model_shopmanager_product_specific->deleteSpecificKey($specific_key, $category_id);

            $json['success'] = 'Specific key deleted successfully';
        } else {
            $json['error'] = 'Missing data: specific_key or category_id' . json_encode($this->request->get);
        }

        // Retourner une réponse JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // Fonction pour ajouter une nouvelle clé spécifique
    public function addSpecificKey() {
        $json = array();

        // Vérifier si les données nécessaires sont présentes
        if (isset($this->request->get['specific_key']) && isset($this->request->get['category_id']) && isset($this->request->get['replacement_term'])) {
            $specific_key = $this->request->get['specific_key'];
            $category_id = (int)$this->request->get['category_id'];
            $replacement_term = $this->request->get['replacement_term'];

            // Charger le modèle
            $this->load->model('shopmanager/product_specific');

            // Ajouter la nouvelle clé spécifique
            $specific_id = $this->model_shopmanager_product_specific->addSpecificKey($specific_key, $category_id, $replacement_term);

            $json['success'] = 'Specific key added successfully';
            $json['specific_id'] = $specific_id;
        } else {
            $json['error'] = 'Missing data: specific_key, category_id or replacement_term';
        }

        // Retourner une réponse JSON
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // Fonction pour récupérer une clé spécifique (vérifier si elle existe)
    public function getSpecificKey() {
        $json = array();

        // Vérifier si les données nécessaires sont présentes
        if (isset($this->request->get['specific_key']) && isset($this->request->get['category_id'])) {
            $specific_key = $this->request->get['specific_key'];
            $category_id = (int)$this->request->get['category_id'];

            // Charger le modèle
            $this->load->model('shopmanager/product_specific');

            // Récupérer la clé spécifique
            $replacement_term = $this->model_shopmanager_product_specific->getSpecificKey($specific_key, $category_id);

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
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
