<?php
// Original: warehouse/popup/wait.php

namespace Opencart\Admin\Controller\Warehouse\Popup;

class Wait extends \Opencart\System\Engine\Controller {
    public function index(): string {
        $this->load->language('warehouse/popup/wait');
        $data = [];
        

        $data['text_loading_please_wait'] = ($lang['text_loading_please_wait'] ?? '');
        $data['text_loading_specifics'] = ($lang['text_loading_specifics'] ?? '');
        $data['text_loading_message'] = ($lang['text_loading_message'] ?? '');
        $data['text_loading'] = ($lang['text_loading'] ?? '');

        return $this->load->view('warehouse/popup/wait', $data);
    }
}
