<?php
namespace Opencart\Admin\Controller\Shopmanager;

class Ai extends \Opencart\System\Engine\Controller {
    public function prompt_ai(): void {
        $lang = $this->load->language('shopmanager/ai');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/ai');

        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/ai')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->respondJson($json);
            return;
        }

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $json['error'] = 'Méthode de requête non autorisée.';
            $this->respondJson($json);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (!isset($data['prompt'], $data['system_prompt'])) {
            $json['error'] = 'Paramètres invalides.';
            $this->respondJson($json);
            return;
        }

        $user_prompt = str_replace('\\', '', (string)$data['prompt']);
        $system_prompt = str_replace('\\', '', (string)$data['system_prompt']);
        $temperature = isset($data['temperature']) ? (float)$data['temperature'] : 1.0;

        // Compute user_token budget (let model handle internal caps)
        $max_tokens = $this->model_shopmanager_ai->countTokensOpenAI($system_prompt)
                    + $this->model_shopmanager_ai->countTokensOpenAI($user_prompt);

        try {
            $ai_response = $this->model_shopmanager_ai->prompt_ai($user_prompt, $system_prompt, $max_tokens, $temperature);

            if ($ai_response) {
                $json['success'] = $ai_response;
            } else {
                $json['error'] = '45:Erreur lors de la communication avec l\'API OpenAI.';
            }
        } catch (\Exception $e) {
            $this->logSafe('AI API Error: ' . $e->getMessage());
            $json['error'] = '49:Erreur lors de la communication avec l\'API OpenAI: ' . $e->getMessage();
        }

        $this->logSafe('AI prompt_ai response: ' . json_encode($json));
        $this->respondJson($json);
    }

    public function prompt_ai_image(): void {
        $lang = $this->load->language('shopmanager/ai');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/ai');

        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/ai')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->respondJson($json);
            return;
        }

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $json['error'] = 'Méthode de requête non autorisée.';
            $this->respondJson($json);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        if (!isset($data['prompt'])) {
            $json['error'] = 'Paramètres invalides.';
            $this->respondJson($json);
            return;
        }

        $prompt = (string)$data['prompt'];
        $ai_response = $this->model_shopmanager_ai->prompt_ai_image($prompt);

        if ($ai_response) {
            $json['success'] = $ai_response;
        } else {
            $json['error'] = '88:Erreur lors de la communication avec l\'API OpenAI.';
        }

        $this->respondJson($json);
    }

    public function getProductSpecific(): void {
        $lang = $this->load->language('shopmanager/ai');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/ai');
        $this->load->model('shopmanager/catalog/category');
        $this->load->model('shopmanager/catalog/product');

        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/ai')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->respondJson($json);
            return;
        }

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $json['error'] = 'Méthode de requête non autorisée.';
            $this->respondJson($json);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->logSafe('getProductSpecific Data received: ' . print_r($data, true));

        if (!isset($data['product_id'], $data['aspectName'])) {
            $json['error'] = 'Paramètres invalides.';
            $this->respondJson($json);
            return;
        }

        $product_id = (int)$data['product_id'];
        $aspectName = (string)$data['aspectName'];

        $product_info = $this->model_shopmanager_catalog_product->getProduct($product_id);

        // Resolve leaf category specifics
        $categories = $this->model_shopmanager_catalog_product->getCategories($product_id);
        $category_specifics = [];
        foreach ($categories as $category_id) {
            $category_info = $this->model_shopmanager_catalog_category->getCategory($category_id);
            if ($category_info && (string)$category_info['leaf'] === '1') {
                $category_specifics = json_decode((string)$category_info['specifics'], true) ?? [];
                break;
            }
        }

        $aspectConstraint = $category_specifics[$aspectName]['aspectConstraint'] ?? '';
        $aspectValues = $category_specifics[$aspectName]['aspectValues'] ?? '';

        $ai_response = $this->model_shopmanager_ai->getProductSpecific($product_info, $aspectName, $aspectValues, $aspectConstraint);

        if ($ai_response) {
            // Ne pas utiliser ucwords() car il manque les caractères Unicode comme Ș, ă, etc.
            // La capitalisation est déjà gérée côté JavaScript avec la fonction ucwords() améliorée
            $json['success'] = $ai_response;
        } else {
            $json['error'] = '148:Erreur lors de la communication avec le modèle AI.';
        }

        $this->logSafe('getProductSpecific Response: ' . json_encode($json));
        $this->respondJson($json);
    }

      public function translate(): void {
        $lang = $this->load->language('shopmanager/ai');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/ai');

        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/ai')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->respondJson($json);
            return;
        }

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $json['error'] = ($lang['error_method'] ?? '');
            $this->respondJson($json);
            return;
        }

        if (!$this->user->isLogged()) {
            $json['error'] = ($lang['error_login'] ?? '');
            $this->respondJson($json);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];

        $text_field = $data['text_field'] ?? trim($this->request->post['text_field'] ?? '');
        $targetLanguage = $data['targetLanguage'] ?? trim($this->request->post['targetLanguage'] ?? '');

        if (empty($text_field)) {
            $json['error'] = ($lang['error_text_required'] ?? '');
            $this->respondJson($json);
            return;
        }

        if (empty($targetLanguage)) {
            $json['error'] = ($lang['error_target_required'] ?? '');
            $this->respondJson($json);
            return;
        }

        try {
            $ai_response = $this->model_shopmanager_ai->translate(json_encode($text_field)  , $targetLanguage);

            if ($ai_response) {
                $json['success'] = $ai_response;
            } else {
                $json['error'] = ($lang['error_translate'] ?? '');
            }
        } catch (\Exception $e) {
            $json['error'] = ($lang['error_translate'] ?? '') . ' ' . $e->getMessage();
            $this->logSafe('Translate error: ' . $e->getMessage());
        }

        $this->logSafe('Translate response: ' . json_encode($json));
        $this->respondJson($json);
    }

    public function getMadeInCountry(): void {
        $lang = $this->load->language('shopmanager/ai');
        $data = $data ?? [];
        $data += $lang;
        $this->load->model('shopmanager/ai');
        $this->load->model('shopmanager/catalog/product');

        $json = [];

        if (!$this->user->hasPermission('modify', 'shopmanager/ai')) {
            $json['error'] = ($lang['error_permission'] ?? '');
            $this->respondJson($json);
            return;
        }

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $json['error'] = 'Méthode de requête non autorisée.';
            $this->respondJson($json);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? [];
        $this->logSafe('getMadeInCountry Data received: ' . print_r($data, true));

        if (!isset($data['product_id'])) {
            $json['error'] = 'Paramètres invalides.';
            $this->respondJson($json);
            return;
        }

        $product_id = (int)$data['product_id'];
        $product_info = $this->model_shopmanager_catalog_product->getProduct($product_id);

        if (!$product_info) {
            $json['error'] = 'Produit introuvable.';
            $this->respondJson($json);
            return;
        }

        try {
            $this->logSafe('getMadeInCountry: About to call AI model for product_id: ' . $product_id);
            $ai_response = $this->model_shopmanager_ai->getMadeInCountry($product_info);
            $this->logSafe('getMadeInCountry: AI response: ' . json_encode($ai_response));

            if ($ai_response) {
                $json['success'] = $ai_response;
            } else {
                $json['error'] = 'Erreur lors de la détermination du pays de fabrication.';
            }
        } catch (\Exception $e) {
            $json['error'] = 'Erreur: ' . $e->getMessage();
            $this->logSafe('getMadeInCountry error: ' . $e->getMessage());
        }

        $this->logSafe('getMadeInCountry Response: ' . json_encode($json));
        $this->respondJson($json);
    }

    private function respondJson(array $json): void {
        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    private function logSafe(string $message): void {
        try {
            if (property_exists($this, 'log') && $this->log) {
                //$this->log->write($message);
            } else {
                $this->registry->get('log')->write($message);
            }
        } catch (\Throwable $e) {
            // ignore logging failures
        }
    }
}

