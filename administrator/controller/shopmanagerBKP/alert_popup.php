<?php
class ControllerShopmanagerAlertPopup extends Controller {
    public function index() {
        $this->load->language('shopmanager/alert_popup');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_message'] = $this->language->get('text_message');
        $data['text_close'] = $this->language->get('text_close');

        return $this->load->view('shopmanager/alert_popup', $data);
    }
}
?>