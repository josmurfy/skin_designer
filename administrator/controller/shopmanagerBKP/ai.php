<?php

class ControllerShopManagerAi extends Controller {
    public function prompt_ai() {
        $this->load->model('shopmanager/ai');

        $json = array();

        // Toujours vérifier la méthode de requête
        if (isset($json)) {
            $data = json_decode(file_get_contents('php://input'), true);
                 
            // Log des données reçues
          //  $this->log->write('Data received: ' . print_r($data, true));

            // Vérification des paramètres
            if (isset($data['prompt']) && isset($data['system_prompt'])) {
                $user_prompt = str_replace('\\','',$data['prompt']);
                $system_prompt = str_replace('\\','',$data['system_prompt']);
                $max_tokens = isset($data['max_tokens']) ? (int)$data['max_tokens'] : 100;
                $temperature = isset($data['temperature']) ? (float)$data['temperature'] : 0.7;
  // Appel à l'IA
                $temperature = 1;
  //$max_tokens = 16385 - 100;
                $max_tokens = $this->model_shopmanager_ai->countTokensOpenAI($system_prompt) + $this->model_shopmanager_ai->countTokensOpenAI($user_prompt);  // Adjust token limit as needed
                $ai_response = $this->model_shopmanager_ai->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature);

                // Log de la réponse AI
           //     $this->log->write('AI response: ' . print_r($ai_response, true));

                if ($ai_response) {
                    $json['success'] = true;
                    $json['message'] = $ai_response;
                } else {
                    $json['success'] = false;
                    $json['message'] = '';
                }
            } else {
                $json['success'] = false;
                $json['message'] = 'Paramètres invalides.';
            }
        } else {
            $json['success'] = false;
            $json['message'] = 'Méthode de requête non autorisée.';
        }

        // Log de la réponse finale
        $this->log->write('Response: ' . json_encode($json));

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function prompt_ai_image(){
        $this->load->model('shopmanager/ai');
    
        $json = array();
    
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
    
            if (isset($data['prompt'])) {
                $prompt = $data['prompt'];
                $ai_response = $this->model_shopmanager_ai->prompt_ai_image($prompt);
    
                if ($ai_response) {
                    $json['success'] = true;
                    $json['message'] = $ai_response;
                } else {
                    $json['success'] = false;
                    $json['message'] = '71:Erreur lors de la communication avec l\'API OpenAI.';
                }
            } else {
                $json['success'] = false;
                $json['message'] = 'Paramètres invalides.';
            }
        } else {
            $json['success'] = false;
            $json['message'] = 'Méthode de requête non autorisée.';
        }
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    

    public function getProductSpecific() {
        $this->load->model('shopmanager/ai');
        $this->load->model('shopmanager/product');
        $this->load->model('shopmanager/catalog/category');
   // echo "allo";
        $json = array();
    
        // Toujours vérifier la méthode de requête
      
            $data = json_decode(file_get_contents('php://input'), true);
            $this->log->write('Data received: ' . print_r($data, true));
        /*    $data = array(
                'product_id' => 25044,
                'aspectName' => 'Theme'
            );*/

            if (isset($data['product_id']) && isset($data['aspectName'])) {
            //    echo "allo";
            //print("<pre>".print_r ($data,true )."</pre>");
                $product_info = $this->model_shopmanager_product->getProduct($data['product_id']);
                $categories = $this->model_shopmanager_product->getProductCategories($data['product_id']);
                $category_specifics = array();
                foreach ($categories as $category_id) {
                    $category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
                    if ($category_info && $category_info['leaf'] == '1') {
                        $category_specifics = json_decode($category_info['specifics'], true);
                        $category_id = $category_info['category_id'];
                        break;
                    }
                }
                $aspectName = $data['aspectName'];
                $aspectConstraint = $category_specifics[$aspectName]['aspectConstraint'] ?? '';
                $aspectValues = $category_specifics[$aspectName]['aspectValues'] ?? '';
    
                $ai_response = $this->model_shopmanager_ai->getProductSpecific($product_info, $aspectName, $aspectValues, $aspectConstraint);
    
                if ($ai_response) {
                    $json['success'] = true;
                    $json['message'] = ucwords($ai_response);
                } else {
                    $json['success'] = true;
                    $json['message'] = '128:Erreur lors de la communication avec le modèle AI.';
                }
            } else {
                $json['success'] = true;
                $json['message'] = 'Paramètres invalides.';
            }
      
    
        $this->log->write('Response: ' . json_encode($json));
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
}

