<?php
// Original: shopmanager/country_conflict_popup.php
namespace Opencart\Admin\Controller\Shopmanager;

class CountryConflictPopup extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('shopmanager/country_conflict_popup');
        $data = [];
        

        $data['heading_title'] = ($lang['heading_title'] ?? '');
        $data['text_conflict_message'] = ($lang['text_conflict_message'] ?? '');
        $data['text_made_in_country'] = ($lang['text_made_in_country'] ?? '');
        $data['text_country_region_manufacture'] = ($lang['text_country_region_manufacture'] ?? '');
        $data['text_apply_all_languages'] = ($lang['text_apply_all_languages'] ?? '');
        $data['button_confirm'] = ($lang['button_confirm'] ?? '');

        // Récupérer les paramètres passés en GET
        $data['made_in_country'] = isset($this->request->get['made_in_country']) ? $this->request->get['made_in_country'] : '';
        $data['specifics_country'] = isset($this->request->get['specifics_country']) ? $this->request->get['specifics_country'] : '';

        // Envoyer le HTML au client
        $this->response->addHeader('Content-Type: text/html; charset=utf-8');
        $this->response->setOutput($this->load->view('shopmanager/country_conflict_popup', $data));
    }
}
