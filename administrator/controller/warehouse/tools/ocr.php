<?php
// Original: shopmanager/ocr.php
namespace Opencart\Admin\Controller\Shopmanager;

class Ocr extends \Opencart\System\Engine\Controller {
    public function index(): void {
    
		
      

        $this->load->language('shopmanager/ocr');
        $data = [];
        
        
        $this->document->setTitle(($lang['heading_title'] ?? ''));
   
        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_form'] = !isset($this->request->get['product_id']) ? ($lang['text_add'] ?? '') : ($lang['text_edit'] ?? '');

        $data['text_image_upload'] = ($lang['text_image_upload'] ?? '');
        $data['text_recognized_text'] = ($lang['text_recognized_text'] ?? '');
        $data['text_drag_drop'] = ($lang['text_drag_drop'] ?? '');
        $data['entry_name'] = ($lang['entry_name'] ?? '');
        $data['entry_description_supp'] = ($lang['entry_description_supp'] ?? '');


        $data['button_submit'] = ($lang['button_submit'] ?? '');
        $data['button_return'] = ($lang['button_return'] ?? '');
        $data['button_cancel'] = ($lang['button_cancel'] ?? '');
		$data['button_ai_description_supp'] = ($lang['button_ai_description_supp'] ?? '');
		$data['button_ai_suggest_entry_name'] = ($lang['button_ai_suggest_entry_name'] ?? '');
       
		$data['user_token'] = $this->session->data['user_token'];

      
      // echo 'allo31ocr'; 

      $this->response->setOutput($this->load->view('shopmanager/ocr', $data));
     
    }

    public function upload(): void {
        $this->load->language('shopmanager/ocr');
        $data = [];
        
        $json = [];
      //  echo 'allo';
   //   echo getenv('GOOGLE_APPLICATION_CREDENTIALS'); 
 //  phpinfo();
   //print("<pre>" . print_r('43', true) . "</pre>");
  //print("<pre>" . print_r($this->request->files['image'], true) . "</pre>");
               
        if (isset($this->request->files['image']) && is_file($this->request->files['image']['tmp_name'])) {
            $this->load->model('shopmanager/ocr');
          
            $text = $this->model_shopmanager_ocr->recognizeText($this->request->files['image']['tmp_name']);
       //$text='';
            if ($text) {
                $json['success'] = true;
                $json['text'] = $text;
            } else {
                $json['error'] = ($lang['error_ocr'] ?? '');
            }
        } else {
            $json['error'] = ($lang['error_no_file'] ?? '');
        }

        $this->response->addHeader('Content-Type: application/json; charset=utf-8');
        $this->response->setOutput(json_encode($json, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
