<?php
class ControllerShopmanagerGoogle extends Controller {
    public function index() {
        ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
        $this->load->language('shopmanager/google');
        $this->load->model('shopmanager/google');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['entry_query'] = $this->language->get('entry_query');
        $data['button_search'] = $this->language->get('button_search');
        $data['text_no_results'] = $this->language->get('text_no_results');

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
            $data['error'] = $this->language->get('text_no_results');
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
        $data['action'] = $this->url->link('shopmanager/google', 'token=' . $this->session->data['token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('shopmanager/google_image_search', $data));
    }
}
