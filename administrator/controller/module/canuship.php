<?php

class ControllerModuleCanuship extends Controller {

    private $error = array();

    /**
     * Function to Set the Canuship module setting and actions
     * 
     * @return boolean
     */
    public function index() {

        $this->load->language('module/canuship');
        //Set the document title
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('setting/setting');

        //Module update success
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $this->model_setting_setting->editSetting('canuship', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
        }

        //Fetch the data from the language and assign to the module.
        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_error'] = $this->language->get('heading_error');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_confirm'] = $this->language->get('text_confirm');

        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_config_key'] = $this->language->get('entry_config_key');
        $data['entry_config_ver_key'] = $this->language->get('entry_config_ver_key');

        $data['button_keygen'] = $this->language->get('button_keygen');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_clear'] = $this->language->get('button_clear');
        $data['button_error_log'] = $this->language->get('button_error_log');

        //Check the warnings
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['config_key'])) {
            $data['error_config_key'] = $this->error['config_key'];
        } else {
            $data['error_config_key'] = '';
        }

        if (isset($this->error['verify_key'])) {
            $data['error_verify_key'] = $this->error['verify_key'];
        } else {
            $data['error_verify_key'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('module/canuship', 'token=' . $this->session->data['token'], 'SSL')
        );

        $data['action'] = $this->url->link('module/canuship', 'token=' . $this->session->data['token'], 'SSL');
        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL');
        $data['keygen'] = $this->url->link('module/canuship/keygen', 'token=' . $this->session->data['token'], 'SSL');
        $data['clear'] = $this->url->link('module/canuship/clear', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['canuship_status'])) {
            $data['canuship_status'] = $this->request->post['canuship_status'];
        } else {
            $data['canuship_status'] = $this->config->get('canuship_status');
        }
        
        //Set the canuship config and verification keys to data
        if (isset($this->request->post['canuship_config_key'])) {
            $data['canuship_config_key'] = $this->request->post['canuship_config_key'];
        } elseif ($this->config->get('canuship_config_key')) {
            $data['canuship_config_key'] = $this->config->get('canuship_config_key');
        } else {
            $data['canuship_config_key'] = '';
        }

        if (isset($this->request->post['canuship_verify_key'])) {
            $data['canuship_verify_key'] = $this->request->post['canuship_verify_key'];
        } elseif ($this->config->get('canuship_verify_key')) {
            $data['canuship_verify_key'] = $this->config->get('canuship_verify_key');
        } else {
            $data['canuship_verify_key'] = '';
        }
        $file = DIR_LOGS . 'canuship/' . $this->config->get('config_error_filename');
        if (file_exists($file)) {
            $data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
        } else {
            $data['log'] = '';
        }
        //Render the canuship template
       //print("<pre>".print_r ($data,true )."</pre>");
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('module/canuship.tpl', $data));
    }

    /**
     * Function to validate the permission and canuship keys
     * 
     * @return boolean
     */
    protected function validate() {

        if (!$this->user->hasPermission('modify', 'module/canuship')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['canuship_config_key']) {
            $this->error['config_key'] = $this->language->get('error_config_key');
        }

        if (!$this->request->post['canuship_verify_key']) {
            $this->error['verify_key'] = $this->language->get('error_verify_key');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Function to genrate the canuship keys
     * 
     * @return boolean
     */
    public function keygen() {

        $this->load->model('setting/setting');

        $config_key = sha1('canuship' . time() . HTTP_CATALOG);
        $verify_key = md5($config_key . DIR_APPLICATION);

        if (isset($this->request->get['status'])) {
            $canuship_status = $this->request->get['status'];
        } else {
            $canuship_status = $this->config->get('canuship_status');
        }

        //Update the canuship config and the verification key
        $data = array(
            'canuship_status' => $canuship_status,
            'canuship_config_key' => $config_key,
            'canuship_verify_key' => $verify_key
        );

        $this->model_setting_setting->editSetting('canuship', $data);

        //Redirect to the canuship module page settings
        $this->response->redirect($this->url->link('module/canuship', 'token=' . $this->session->data['token'], 'SSL'));
    }

    /**
     * Function to clear the canuship log
     * 
     * @return boolean
     */
    public function clear() {

        $this->load->language('module/canuship');

        $file = DIR_LOGS . 'canuship/' . $this->config->get('config_error_filename');

        $handle = fopen($file, 'w+');
        fclose($handle);

        $this->session->data['success'] = $this->language->get('text_cleared');
        $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'], 'SSL'));
    }

    /**
     * Function to add data to config file after module install
     * 
     * @return boolean
     */
    public function install() {

        if (!$this->user->hasPermission('modify', 'module/canuship')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!file_exists(DIR_LOGS . 'canuship')) {
            mkdir(DIR_LOGS . 'canuship');
        }

        $base_dir = str_replace('\'', '/', realpath(DIR_APPLICATION . '../')) . '/';

        $output = '<?php' . "\n";
        $output .= '// Generated during install (' . date('F j, Y, g:i a') . ')' . "\n\n";

        $output .= '// HTTP' . "\n";
        $output .= 'define(\'HTTP_SERVER\', \'' . HTTP_SERVER . '\');' . "\n";
        $output .= 'define(\'HTTP_CATALOG\', \'' . HTTP_CATALOG . '\');' . "\n";
        $output .= 'define(\'HTTP_IMAGE\', \'' . str_replace('admin', 'image', HTTP_SERVER) . '\');' . "\n\n";

        $output .= '// HTTPS' . "\n";
        $output .= 'define(\'HTTPS_SERVER\', \'' . HTTPS_SERVER . '\');' . "\n";
        $output .= 'define(\'HTTPS_CATALOG\', \'' . HTTPS_CATALOG . '\');' . "\n";
        $output .= 'define(\'HTTPS_IMAGE\', \'' . str_replace('admin', 'image', HTTPS_SERVER) . '\');' . "\n\n";

        $output .= '// DIR' . "\n";
        $output .= 'define(\'BASE_DIR\', \'' . $base_dir . '\');' . "\n\n";
        $output .= 'define(\'DIR_APPLICATION\', \'' . $base_dir . 'canuship/' . '\');' . "\n";
        $output .= 'define(\'DIR_SYSTEM\', \'' . DIR_SYSTEM . '\');' . "\n";
        $output .= 'define(\'DIR_LANGUAGE\', \'' . DIR_LANGUAGE . '\');' . "\n";
        $output .= 'define(\'DIR_CONFIG\', \'' . DIR_CONFIG . '\');' . "\n";
        $output .= 'define(\'DIR_IMAGE\', \'' . DIR_IMAGE . '\');' . "\n";
        $output .= 'define(\'DIR_CACHE\', \'' . DIR_CACHE . '\');' . "\n";
        $output .= 'define(\'DIR_MODIFICATION\', \'' . DIR_MODIFICATION . '\');' . "\n";
        $output .= 'define(\'DIR_LOGS\', \'' . DIR_LOGS . 'canuship/' . '\');' . "\n\n";

        $output .= '// DB' . "\n";
        $output .= 'define(\'DB_DRIVER\', \'' . DB_DRIVER . '\');' . "\n";
        $output .= 'define(\'DB_HOSTNAME\', \'' . DB_HOSTNAME . '\');' . "\n";
        $output .= 'define(\'DB_USERNAME\', \'' . DB_USERNAME . '\');' . "\n";
        $output .= 'define(\'DB_PASSWORD\', \'' . DB_PASSWORD . '\');' . "\n";
        $output .= 'define(\'DB_DATABASE\', \'' . DB_DATABASE . '\');' . "\n";
        $output .= 'define(\'DB_PREFIX\', \'' . DB_PREFIX . '\');' . "\n";
        $output .= '?>';

        $file = fopen('../canuship/config.php', 'w');
        fwrite($file, $output);
        fclose($file);
    }

    /**
     * Function to Clear the data from config file after module uninstall
     * 
     * @return boolean
     */
    public function uninstall() {

        if (!$this->user->hasPermission('modify', 'module/canuship')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        //Delete the data from config file
        $file = fopen('../canuship/config.php', 'w');
        fwrite($file, '');
        fclose($file);
    }

}