<?php
class ControllerExtensionModuleWebpush extends Controller {

    private $error = array();

    public function index() {

        $this->language->load('extension/module/webpush');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting('module_webpush', $this->request->post);
            $appId = $this->request->post['module_webpush_appId'];
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=module', true));
        }

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_info'] = $this->language->get('text_info');
        $data['text_bottom_right'] = $this->language->get('text_bottom_right');
        $data['text_bottom_left'] = $this->language->get('text_bottom_left');
        $data['text_small'] = $this->language->get('text_small');
        $data['text_medium'] = $this->language->get('text_medium');
        $data['text_large'] = $this->language->get('text_large');

        $data['entry_admin'] = $this->language->get('entry_admin');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_appId'] = $this->language->get('entry_appId');
        $data['entry_bellStatus'] = $this->language->get('entry_bellStatus');
        $data['entry_position'] = $this->language->get('entry_position');
        $data['entry_size'] = $this->language->get('entry_size');
        $data['entry_autoRegister'] = $this->language->get('entry_autoRegister');

        $data['help_appId'] = $this->language->get('help_appId');
        $data['help_bellStatus'] = $this->language->get('help_bellStatus');
        $data['help_position'] = $this->language->get('help_position');
        $data['help_size'] = $this->language->get('help_size');
        $data['help_autoRegister'] = $this->language->get('help_autoRegister');
        $data['help_onesignalStatus'] = $this->language->get('help_onesignalStatus');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['appId'])) {
            $data['error_appId'] = $this->error['appId'];
        } else {
            $data['error_appId'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('extension/module/webpush', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $data['action'] = $this->url->link('extension/module/webpush', 'token=' . $this->session->data['token'], true);
        $data['cancel'] = $this->url->link('marketplace/extension', 'token=' . $this->session->data['token'] . '&type=module', true);

        if (isset($this->request->post['module_webpush_appId'])) {
            $data['module_webpush_appId'] = $this->request->post['module_webpush_appId'];
        } else {
            $data['module_webpush_appId'] = $this->model_setting_setting->getSettingValue('module_webpush_appId');
        }

        if (isset($this->request->post['module_webpush_bellStatus'])) {
            $data['module_webpush_bellStatus'] = $this->request->post['module_webpush_bellStatus'];
        } else {
            $data['module_webpush_bellStatus'] = $this->model_setting_setting->getSettingValue('module_webpush_bellStatus');
        }

        if (isset($this->request->post['module_webpush_position'])) {
            $data['module_webpush_position'] = $this->request->post['module_webpush_position'];
        } else {
            $data['module_webpush_position'] = $this->model_setting_setting->getSettingValue('module_webpush_position');
        }

        if (isset($this->request->post['module_webpush_size'])) {
            $data['module_webpush_size'] = $this->request->post['module_webpush_size'];
        } else {
            $data['module_webpush_size'] = $this->model_setting_setting->getSettingValue('module_webpush_size');
        }

        if (isset($this->request->post['module_webpush_autoRegister'])) {
            $data['module_webpush_autoRegister'] = $this->request->post['module_webpush_autoRegister'];
        } else {
            $data['module_webpush_autoRegister'] = $this->model_setting_setting->getSettingValue('module_webpush_autoRegister');
        }

        if (isset($this->request->post['module_webpush_status'])) {
            $data['module_webpush_status'] = $this->request->post['module_webpush_status'];
        } else {
            $data['module_webpush_status'] = $this->model_setting_setting->getSettingValue('module_webpush_status');
        }


        $this->load->model('design/layout');

        $data['layouts'] = $this->model_design_layout->getLayouts();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('extension/module/webpush', $data));
    }

    public function injectMenu($route, &$data){
        $data['menus'][] = [
            'id' => 'menu-webpush',
            'icon' => 'fa fa-bell',
            'name' => 'OneSignal',
            'href' => $this->url->link('extension/module/webpush','token='.$this->session->data['token'],true),
            'children' => array()
        ];
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'extension/module/webpush')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['module_webpush_appId']) {
            $this->error['appId'] = $this->language->get('error_appId');
        }

        return !$this->error;
    }

    public function install(){
        // Moving SDK files to the root directory
        rename(DIR_APPLICATION . 'view/javascript/OneSignalSDKWorker.js', substr(DIR_SYSTEM, 0, -7).'OneSignalSDKWorker.js');
        rename(DIR_APPLICATION . 'view/javascript/OneSignalSDKUpdaterWorker.js', substr(DIR_SYSTEM, 0, -7).'OneSignalSDKUpdaterWorker.js');
        $this->load->model("extension/event");
        $this->load->model('setting/setting');

        $this->model_extension_event->addEvent("webpush","catalog/view/common/header/before","extension/module/webpush/inject_one_script");
        $this->model_extension_event->addEvent("webpush_menu", "admin/view/common/column_left/before", "extension/module/webpush/injectMenu");

        $settings = [
            'module_webpush_size'           => 'medium',
            'module_webpush_autoRegister'   => 'false',
        ];
        $this->model_setting_setting->editSetting('module_webpush', $settings);
    }

    public function uninstall() {
        $this->load->model('setting/setting');
        $this->load->model('extension/event');

        $store_id = $this->config->get('config_store_id');
        $this->model_setting_setting->deleteSetting('module_webpush', $store_id);

        $this->model_extension_event->deleteEvent('webpush');
        $this->model_extension_event->deleteEvent('webpush_menu');
    }

}