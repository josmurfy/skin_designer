<?php

namespace Opencart\Admin\Controller\Shopmanager;

class WaitPopup extends \Opencart\System\Engine\Controller {
    public function index(): string {
        $this->load->language('shopmanager/wait_popup');
        $data = [];
        

        $data['text_loading_please_wait'] = ($lang['text_loading_please_wait'] ?? '');
        $data['text_loading_specifics'] = ($lang['text_loading_specifics'] ?? '');
        $data['text_loading_message'] = ($lang['text_loading_message'] ?? '');
        $data['text_loading'] = ($lang['text_loading'] ?? '');

        return $this->load->view('shopmanager/wait_popup', $data);
    }
}
