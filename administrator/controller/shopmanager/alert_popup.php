<?php
namespace Opencart\Admin\Controller\Shopmanager;

class AlertPopup extends \Opencart\System\Engine\Controller {
    public function index(): string {
        $this->load->language('shopmanager/alert_popup');
        $data = [];
        

        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_message'] = ($lang['text_message'] ?? '');
        $data['text_close'] = ($lang['text_close'] ?? '');

        return $this->load->view('shopmanager/alert_popup', $data);
    }
}