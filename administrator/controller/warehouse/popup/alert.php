<?php
// Original: warehouse/popup/alert.php
namespace Opencart\Admin\Controller\Warehouse\Popup;

class Alert extends \Opencart\System\Engine\Controller {
    public function index(): string {
        $this->load->language('warehouse/popup/alert');
        $data = [];
        

        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_message'] = ($lang['text_message'] ?? '');
        $data['text_close'] = ($lang['text_close'] ?? '');

        return $this->load->view('warehouse/popup/alert', $data);
    }
}