<?php
// Original: shopmanager/google.php
namespace Opencart\Admin\Controller\Shopmanager;

class Google extends \Opencart\System\Engine\Controller {
    public function index(): void {
        ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
        $this->load->language('shopmanager/google');
        $data = [];
        
        $this->load->model('shopmanager/google');

        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['entry_query'] = ($lang['entry_query'] ?? '');
        $data['button_search'] = ($lang['button_search'] ?? '');
        $data['text_no_results'] = ($lang['text_no_results'] ?? '');

        $data['images'] = [];

        if (
            ($this->request->server['REQUEST_METHOD'] === 'POST' && !empty($this->request->post['query'])) ||
            ($this->request->server['REQUEST_METHOD'] === 'GET' && !empty($this->request->get['query']))
        ) {
            $query = !empty($this->request->post['query']) ? $this->request->post['query'] : $this->request->get['query'];
            $data['images'] = $this->model_shopmanager_google->get($query);
            $data['query'] = $query;
        }else{
            $data['query'] = '';

        }
        
//print("<pre>" . print_r($data['images'], true) . "</pre>");
        if (isset($data['images']['error'])) {
            $data['error'] = $data['images']['error'];
        } else {
            $data['error'] = '';
        }
        if (empty($data['images'])) {
            $data['error'] = ($lang['text_no_results'] ?? '');
        }else{
         

// Trier chaque groupe de site par résolution décroissante
                    // Aplatir toutes les images dans un seul tableau
            $all_images = [];
            foreach ($data['images'] as $site_images) {
                foreach ($site_images as $img) {
                    $all_images[] = $img;
                }
            }

            // Trier par résolution décroissante
            usort($all_images, function ($a, $b) {
                $resA = (isset($a['width']) ? $a['width'] : 0) * (isset($a['height']) ? $a['height'] : 0);
                $resB = (isset($b['width']) ? $b['width'] : 0) * (isset($b['height']) ? $b['height'] : 0);
                return $resB - $resA;
            });

            $data['images'] = $all_images;


        }
        $data['action'] = $this->url->link('shopmanager/google', 'user_token=' . $this->session->data['user_token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/google_image_search', $data));
    }

     public function translate(): void {
        $this->load->language('shopmanager/google');
        $data = [];
        

        $json = [];
        $result = null;

        if ($this->request->server['REQUEST_METHOD'] !== 'POST') {
            $json['error'] = ($lang['error_method'] ?? '');
        } elseif (!$this->user->isLogged()) {
            $json['error'] = ($lang['error_login'] ?? '');
        } elseif (!$this->user->hasPermission('modify', 'shopmanager/google')) {
            $json['error'] = ($lang['error_permission'] ?? '');
        } else {
            $text_field = $this->request->post['text_field'] ?? '';
            $targetLanguage = $this->request->post['targetLanguage'] ?? '';

            if (trim($text_field) === '') {
                $json['error'] = ($lang['error_text_required'] ?? '');
            } elseif (trim($targetLanguage) === '') {
                $json['error'] = ($lang['error_target_required'] ?? '');
            } else {
                try {
                    putenv('GOOGLE_APPLICATION_CREDENTIALS=' . __DIR__ . '/translate.json');
                    $translate = new \Google\Cloud\Translate\V2\TranslateClient();
                    $result = $translate->translate($text_field, ['target' => $targetLanguage]);
                    $json['success'] = addslashes($result['text']);
                } catch (\Exception $e) {
                    $json['error'] = ($lang['error_translate'] ?? '') . ' ' . $e->getMessage();
                    $this->log->write('Translate error: ' . $e->getMessage());
                }
            }
        }

        //$this->log->write('Translate response: ' . print_r($result, true));

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
