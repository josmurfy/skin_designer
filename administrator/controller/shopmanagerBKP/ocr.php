<?php
class ControllerShopmanagerOcr extends Controller {
    public function index() {
    
		
      

        $this->load->language('shopmanager/ocr');
        
        $this->document->setTitle($this->language->get('heading_title'));
   
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_form'] = !isset($this->request->get['product_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

        $data['text_image_upload'] = $this->language->get('text_image_upload');
        $data['text_recognized_text'] = $this->language->get('text_recognized_text');
        $data['text_drag_drop'] = $this->language->get('text_drag_drop');
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_description_supp'] = $this->language->get('entry_description_supp');


        $data['button_submit'] = $this->language->get('button_submit');
        $data['button_return'] = $this->language->get('button_return');
        $data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_ai_description_supp'] = $this->language->get('button_ai_description_supp');
		$data['button_ai_suggest_entry_name'] = $this->language->get('button_ai_suggest_entry_name');
       
		$data['token'] = $this->session->data['token'];

      
      // echo 'allo31ocr'; 

      return  $this->load->view('shopmanager/ocr', $data);
     
    }

    public function upload() {
        $this->load->language('shopmanager/ocr');
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
                $json['error'] = $this->language->get('error_ocr');
            }
        } else {
            $json['error'] = $this->language->get('error_no_file');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
