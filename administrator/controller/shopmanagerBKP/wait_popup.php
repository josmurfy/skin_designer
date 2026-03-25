<?php

class ControllerShopManagerWaitPopup extends Controller {

    public function index() {
		$this->load->language('shopmanager/wait_popup');

        	// Passer les variables de langue à la vue
		$data['text_loading_please_wait'] = $this->language->get('text_loading_please_wait');
		$data['text_loading_specifics'] = $this->language->get('text_loading_specifics');
		$data['text_loading_message'] = $this->language->get('text_loading_message');
		$data['text_loading'] = $this->language->get('text_loading');

		
		return $this->load->view('shopmanager/wait_popup', $data);
	}

}
