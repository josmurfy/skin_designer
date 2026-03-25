<?php

/*
 * This file is used of Add products in the new Etsy tables
 * This file is having all the function which is used in submitting products on Etsy
 * File is added by Nikhil on 03-07-2017 
 */
include_once(DIR_SYSTEM . 'library/kbetsy/KbOAuth.php');

class ControllerExtensionModuleKbetsy extends Controller
{

    private $error = array();
    private $session_token_key = 'token';
    private $session_token = '';
    private $module_path = '';
    private $demo_flag = '0';

    public function __construct($registry)
    {
        parent::__construct($registry);
        if (VERSION >= 3.0) {
            $this->session_token_key = 'user_token';
            $this->session_token = $this->session->data['user_token'];
        } else {
            $this->session_token_key = 'token';
            $this->session_token = $this->session->data['token'];
        }
        if (VERSION <= '2.2.0') {
            $this->module_path = 'module/kbetsy';
        } else {
            $this->module_path = 'extension/module/kbetsy';
        }
    }

    public function index()
    {
        $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, true));
    }

    public function generalSettings()
    {
        //Load settings for Etsy plugin from database or from default settings
        $this->load->language($this->module_path);
        $this->document->setTitle($this->language->get('heading_title_main'));
        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('localisation/currency');

        if (!extension_loaded('OAuth')) {
            //$this->response->redirect($this->url->link($this->module_path . '/showError', $this->session_token_key . '=' . $this->session_token, true));
        }

        //Order Settings
        $data['text_status_order_default'] = $this->language->get('text_status_order_default');
        $data['text_status_order_paid'] = $this->language->get('text_status_order_paid');
        $data['text_status_order_paid_etsy'] = $this->language->get('text_status_order_paid_etsy');
        $data['text_status_order_shipped'] = $this->language->get('text_status_order_shipped');
        $data['text_status_order_paid_hint'] = $this->language->get('text_status_order_paid_hint');
        $data['text_status_order_shipped_hint'] = $this->language->get('text_status_order_shipped_hint');
        $data['text_status_order_default_hint'] = $this->language->get('text_status_order_default_hint');
        $data['text_status_order_paid_oc_hint'] = $this->language->get('text_status_order_paid_oc_hint');

        /* Save General Settings */
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateGeneralSettings()) {
            if($this->demo_flag == 0) {
                $this->request->post['etsy_general_settings'] = $this->request->post['etsy']['general'];
                $this->request->post['etsy_order_settings'] = $this->request->post['etsy']['order'];
                if ($this->request->post['etsy']['general']['enable'] == 0) {
                    $enable_status['module_kbetsy_status'] = '0';
                } else {
                    $enable_status['module_kbetsy_status'] = '1';
                }
                $this->model_setting_setting->editSetting('module_kbetsy', $enable_status);                
                unset($this->request->post['etsy']);
                $this->model_kbetsy_kbetsy->editSetting('etsy_general_settings', $this->request->post);
                $this->model_kbetsy_kbetsy->editSetting('etsy_order_settings', $this->request->post);
                $this->session->data['success'] = $this->language->get('text_save_connect_setting_error');
            } else {
                $this->session->data['error'] = $this->language->get('text_demo_mode_action');
            }
            $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        //links
        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['action'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['route'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path . '', $this->session_token_key . '=' . $this->session_token, 'SSL');

        $data['etsy'] = array();

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');

        // General Settings tab & info
        $data['text_general_enable'] = $this->language->get('text_general_enable');
        $data['text_general_store_name'] = $this->language->get('text_general_store_name');
        $data['text_general_etsy_quantity'] = $this->language->get('text_general_etsy_quantity');
        $data['text_general_guestenable'] = $this->language->get('text_general_guestenable');
        $data['text_etsy_api_detail'] = $this->language->get('text_etsy_api_detail');
        $data['text_etsy_api_detail_title'] = $this->language->get('text_etsy_api_detail_title');
        $data['text_etsy_store'] = $this->language->get('text_etsy_store');
        $data['text_etsy_merchant_id'] = $this->language->get('text_etsy_merchant_id');
        $data['text_etsy_market_place_id'] = $this->language->get('text_etsy_market_place_id');
        $data['text_etsy_api_key'] = $this->language->get('text_etsy_api_key');
        $data['text_etsy_api_secret'] = $this->language->get('text_etsy_api_secret');
        $data['text_etsy_api_host'] = $this->language->get('text_etsy_api_host');
        $data['text_etsy_api_version'] = $this->language->get('text_etsy_api_version');
        $data['text_etsy_default_lang'] = $this->language->get('text_etsy_default_lang');
        $data['text_etsy_lang_sync'] = $this->language->get('text_etsy_lang_sync');
        $data['text_product_listing'] = $this->language->get('text_product_listing');
        $data['text_product_update'] = $this->language->get('text_product_update');
        $data['text_product_id'] = $this->language->get('text_product_id');
        $data['text_product_name'] = $this->language->get('text_product_name');
        $data['text_product_sku'] = $this->language->get('text_product_sku');
        $data['text_product_price'] = $this->language->get('text_product_price');
        $data['text_product_quantity'] = $this->language->get('text_product_quantity');
        $data['text_status'] = $this->language->get('text_status');
        $data['text_action'] = $this->language->get('text_action');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['entry_select_currency'] = $this->language->get('entry_select_currency');
        $data['text_etsy_currency'] = $this->language->get('text_etsy_currency');
        $data['error_currency'] = $this->language->get('error_currency');
        $data['text_etsy_currency_hint'] = $this->language->get('text_etsy_currency_hint');
        $data['text_etsy_api_hint'] = $this->language->get('text_etsy_api_hint');
        $data['text_supported_text_language_hint'] = $this->language->get('text_supported_text_language_hint');

        //Tooltips
        $data['image_size_tooltip'] = $this->language->get('image_size_tooltip');
        $data['general_enable_tooltip'] = $this->language->get('general_enable_tooltip');
        $data['text_etsy_store_tooltip'] = $this->language->get('text_etsy_store_tooltip');
        $data['text_etsy_merchant_id_tooltip'] = $this->language->get('text_etsy_merchant_id_tooltip');
        $data['text_etsy_market_place_id_tooltip'] = $this->language->get('text_etsy_market_place_id_tooltip');
        $data['text_etsy_api_key_tooltip'] = $this->language->get('text_etsy_api_key_tooltip');
        $data['text_etsy_api_secret_tooltip'] = $this->language->get('text_etsy_api_secret_tooltip');
        $data['text_etsy_api_host_type_tooltip'] = $this->language->get('text_etsy_api_host_type_tooltip');
        $data['text_edit_general'] = $this->language->get('text_edit_general');
        $data['text_edit_order'] = $this->language->get('text_edit_order');
        $data['button_disconnect'] = $this->language->get('button_disconnect');

        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_connect'] = $this->language->get('button_connect');
        $data['connect'] = '';

        /* Set Default Order Status from the OC Complete Status */
        if ($this->config->get('config_order_status_id') != "") {
            $data['etsy_order_default_status_id'] = $this->config->get('config_order_status_id');
        }
        
        if ($this->config->get('config_order_status_id') != "") {
            $data['order_paid_status_id'] = $this->config->get('config_order_status_id');
        }
        
        if ($this->config->get('config_processing_status') != "") {
            /* Status can be multiple */
            if (is_array($this->config->get('config_processing_status'))) {
                $config_processing_status = $this->config->get('config_processing_status');
                $data['etsy_order_paid_status_id'] = $config_processing_status;
            } else {
                $data['etsy_order_paid_status_id'] = $this->config->get('config_processing_status');
            }
        }

        if ($this->config->get('config_complete_status') != "") {
            if (is_array($this->config->get('config_complete_status'))) {
                $config_processing_status = $this->config->get('config_complete_status');
                $data['etsy_order_shipped_status_id'] = $config_processing_status;
            } else {
                $data['etsy_order_shipped_status_id'] = $this->config->get('config_complete_status');
            }
        }

        $data['allcurrencies'] = array();
        $results = $this->model_localisation_currency->getCurrencies();
        $allowed_currencies = array();
        foreach ($results as $result) {
            if ($result['status']) {
                $data['allcurrencies'][] = array(
                    'currency_id' => $result['currency_id'],
                    'title' => $result['title'],
                    'code' => $result['code'],
                    'symbol_left' => $result['symbol_left'],
                    'symbol_right' => $result['symbol_right']
                );
            }
        }

        if ($this->model_kbetsy_kbetsy->getSetting('etsy_general_settings')) {
            $settings = $this->model_kbetsy_kbetsy->getSetting('etsy_general_settings');
            $order_settings = $this->model_kbetsy_kbetsy->getSetting('etsy_order_settings');
            if (isset($this->request->get['selectedCentral'])) {
                $data['etsy']['general'] = $settings['etsy_general_settings'];
                $data['etsy']['order'] = $settings['etsy_general_settings'];
            } else {
                if($this->demo_flag == 0) {
                    $data['etsy']['general'] = $settings['etsy_general_settings'];
                } else {
                    $data['etsy']['general'] = array(
                        'enable' => 1,
                        'etsy_api_key' => 'e4343ds423dkl2aln',
                        'etsy_api_secret' => 'f6dd2dpsn32xs3a1',
                        'currency' => 'USD',
                        'etsy_default_language' => '1',
                        'etsy_languages_to_sync' => array(1, 2)
                    );
                }
                $data['etsy']['order'] = $settings['etsy_general_settings'];
            }
            
            if(!empty($order_settings['etsy_order_settings']['default_status'])) {
                $data['etsy_order_default_status_id'] = $order_settings['etsy_order_settings']['default_status'];
            }
            
            if(!empty($order_settings['etsy_order_settings']['default_paid_status'])) {
                $data['order_paid_status_id'] = $order_settings['etsy_order_settings']['default_paid_status'];
            }
            
            if(!empty($order_settings['etsy_order_settings']['paid_status'])) {
                $data['etsy_order_paid_status_id'] = $order_settings['etsy_order_settings']['paid_status'];
            } else {
                $data['etsy_order_paid_status_id'] = array();
            }
            
            if(!empty($order_settings['etsy_order_settings']['shipped_status'])) {
                $data['etsy_order_shipped_status_id'] = $order_settings['etsy_order_settings']['shipped_status'];
            } else {
                $data['etsy_order_shipped_status_id'] = array();
            }
            
            if (!empty($data['etsy']['general']['etsy_api_key']) && !empty($data['etsy']['general']['etsy_api_secret'])) {
                $data['connect'] = $this->url->link($this->module_path . '/generalSettings', 'action=connect&' . $this->session_token_key . '=' . $this->session_token, 'SSL');
            }
        } else if (!empty($this->request->post)) {
            $data['etsy'] = $this->request->post['etsy'];
        } else {
            $data['etsy']['general']['currency'] = '';
            $data['etsy']['general']['etsy_default_language'] = '';
        }

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
        if (isset($this->request->get['oauth_verifier'])) {
            try {
                $ac = new \Etsy\KbOAuth($data['etsy']['general']['etsy_api_key'], $data['etsy']['general']['etsy_api_secret'], $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
                $response = $ac->getRequestURL();
                if (empty($etsyRequestData)) {
                    $this->session->data['error'] = $this->language->get('etsy_connection_error');
                } else {
                    $this->model_kbetsy_kbetsy->editSetting('etsy_access_token', array('etsy_access_token' => $response->access_token));
                    $this->model_kbetsy_kbetsy->editSetting('etsy_access_token_secret', array('etsy_access_token_secret' => $response->access_token_secret));
                    $this->session->data['success'] = $this->language->get('etsy_connected_success');
                    $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
                }
            } catch (Exception $e) {
                $this->session->data['error'] = $this->language->get('etsy_connection_error');
                $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        }
        if (isset($this->request->get['action']) && $this->request->get['action'] == 'connect') {
            try {
                $ac = new \Etsy\KbOAuth($data['etsy']['general']['etsy_api_key'], $data['etsy']['general']['etsy_api_secret'], str_replace('&amp;', '&', $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL')));
                $response = $ac->getRequestURL();
                
                if(!empty($response->access_token)) {
                    $this->model_kbetsy_kbetsy->editSetting('etsy_access_token', array('etsy_access_token' => $response->access_token));
                    $this->model_kbetsy_kbetsy->editSetting('etsy_access_token_secret', array('etsy_access_token_secret' => $response->access_token_secret));
                    $this->session->data['success'] = $this->language->get('etsy_connected_success');
                    $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
                } else {
                    die();
                }
            } catch (Exception $e) {
                $this->session->data['error'] = $this->language->get('etsy_connection_error');
                $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        }
        if (isset($this->request->get['action']) && $this->request->get['action'] == 'disconnect') {
            $this->model_kbetsy_kbetsy->deleteSetting('etsy_access_token');
            $this->model_kbetsy_kbetsy->deleteSetting('etsy_access_token_secret');
            $this->session->data['success'] = $this->language->get('etsy_disconnected_success');
            $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }
        if (!empty($this->request->post['etsy']['general']['etsy_api_key'])) {
            $data['etsy']['general']['etsy_api_key'] = $this->request->post['etsy']['general']['etsy_api_key'];
        }
        if (!empty($this->request->post['etsy']['general']['etsy_api_secret'])) {
            $data['etsy']['general']['etsy_api_secret'] = $this->request->post['etsy']['general']['etsy_api_secret'];
        }
        if (!empty($this->request->post['etsy']['general']['etsy_api_host'])) {
            $data['etsy']['general']['etsy_api_host'] = $this->request->post['etsy']['general']['etsy_api_host'];
        }
        if (!empty($this->request->post['etsy']['general']['etsy_api_version'])) {
            $data['etsy']['general']['etsy_api_version'] = $this->request->post['etsy']['general']['etsy_api_version'];
        }
        if (!empty($this->request->post['etsy']['general']['etsy_default_language'])) {
            $data['etsy']['general']['etsy_default_language'] = $this->request->post['etsy']['general']['etsy_default_language'];
        }
        if (!empty($this->request->post['etsy']['general']['etsy_languages_to_sync'])) {
            $data['etsy']['general']['etsy_languages_to_sync'] = $this->request->post['etsy']['general']['etsy_languages_to_sync'];
        }
        $data['etsy']['general']['etsy_api_host'] = 'https://openapi.etsy.com/';
        $data['etsy']['general']['etsy_api_version'] = 'v2';
        $this->load->model('localisation/language');

        $all_languages = $this->model_localisation_language->getLanguages();

        /* Check if langauge code is in  */
        $allowed_langauges = array('de', 'en', 'es', 'fr', 'it', 'ja', 'nl', 'pl', 'pt', 'ru');
        $sync_langauge = array();
        foreach ($all_languages as $etsy_language) {
            $lang_data = $this->model_localisation_language->getLanguage($etsy_language['language_id']);
            $lang_data_array = explode("-", $lang_data['code']);
            if (in_array($lang_data_array[0], $allowed_langauges)) {
                $sync_langauge[] = $etsy_language;
            }
        }
        $data['languages'] = $sync_langauge;

        $data['etsy_languages_to_sync'] = array();
        $etsy_access_token = $this->config->get('etsy_access_token');
        $etsy_access_token_secret = $this->config->get('etsy_access_token_secret');
        if (empty($etsy_access_token) || empty($etsy_access_token_secret)) {
            $data['connect_status'] = 'no';
        } else {
            $data['connect_status'] = 'yes';
            $data['disconnect'] = $this->url->link($this->module_path . '/generalSettings', 'action=disconnect&' . $this->session_token_key . '=' . $this->session_token, 'SSL');
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else if (isset($this->error['error'])) {
            $data['error'] = $this->error['error'];
        } else {
            $data['error'] = '';
        }

        if (isset($this->error['etsy_api_key'])) {
            $data['error_etsy_api_key'] = $this->error['etsy_api_key'];
        } else {
            $data['error_etsy_api_key'] = '';
        }

        if (isset($this->error['etsy_api_secret'])) {
            $data['error_etsy_api_secret'] = $this->error['etsy_api_secret'];
        } else {
            $data['error_etsy_api_secret'] = '';
        }
        if (isset($this->error['etsy_api_host'])) {
            $data['error_etsy_api_host'] = $this->error['etsy_api_host'];
        } else {
            $data['error_etsy_api_host'] = '';
        }

        if (isset($this->error['etsy_api_version'])) {
            $data['error_etsy_api_version'] = $this->error['etsy_api_version'];
        } else {
            $data['error_etsy_api_version'] = '';
        }

        if (isset($this->error['etsy_currency'])) {
            $data['error_currency'] = $this->error['etsy_currency'];
        } else {
            $data['error_currency'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 1;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (empty($data['languages'])) {
            $data["error"] = $this->language->get('text_supported_text_language_error');
            if (VERSION >= '2.2.0.0') {
                $this->response->setOutput($this->load->view($this->module_path . '/error', $data));
            } else {
                $this->response->setOutput($this->load->view($this->module_path . '/error.tpl', $data));
            }
        } else if (empty($data['allcurrencies'])) {
            $data["error"] = $this->language->get('text_supported_text_currency_error');
            if (VERSION >= '2.2.0.0') {
                $this->response->setOutput($this->load->view($this->module_path . '/error', $data));
            } else {
                $this->response->setOutput($this->load->view($this->module_path . '/error.tpl', $data));
            }
        } else {
            if (VERSION >= '2.2.0.0') {
                $this->response->setOutput($this->load->view($this->module_path . '/general_settings', $data));
            } else {
                $this->response->setOutput($this->load->view($this->module_path . '/general_settings.tpl', $data));
            }
        }
    }

    public function profileManagement()
    {
        $this->checkSettings();
        $this->load->language($this->module_path);
        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('kbetsy/attribute');

        $this->document->setTitle($this->language->get('heading_title_main'));

        /** Check if all the attributes are mapped */
        $all_options_mapped = true;
        $options = $this->model_kbetsy_attribute->getEtsyOCOptionsMapping();
        $optionIdArray = array();
        if (!empty($options)) {
            foreach ($options as $option) {
                $optionIdArray[] = $option["option_id"];
            }
        }

        if (!empty($optionIdArray)) {
            $optionMappings = $this->model_kbetsy_attribute->getOptionsName($optionIdArray);
            if (!empty($optionMappings)) {
                foreach ($optionMappings as $optionMapping) {
                    if ($optionMapping["option_id"] == "") {
                        $all_options_mapped = false;
                        break;
                    }
                }
            }
        }

        if ($all_options_mapped == false) {
            $this->session->data['error'] = $this->language->get('attribute_not_mapped');
            $this->response->redirect($this->url->link($this->module_path . '/attributeMapping', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'p.id_etsy_profiles';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';
        $sort_url = '';
        $filter_url = '';

        if (isset($this->request->get['filter_profile_name'])) {
            $url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_shipping_name'])) {
            $url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['page'])) {
            $filter_url .= '&page=' . $this->request->get['page'];
            $sort_url .= '&page=' . $this->request->get['page'];
        }
        
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
            $filter_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
            $filter_url .= '&order=' . $this->request->get['order'];
        }

        if ($order == 'ASC') {
            $sort_url .= '&order=DESC';
        } else {
            $sort_url .= '&order=ASC';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token. $filter_url, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token. $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token. $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_profile'),
            'href' => $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->request->get['filter_profile_name'])) {
            $filter_profile_name = $this->request->get['filter_profile_name'];
        } else {
            $filter_profile_name = null;
        }
        if (isset($this->request->get['filter_shipping_name'])) {
            $filter_shipping_name = $this->request->get['filter_shipping_name'];
        } else {
            $filter_shipping_name = null;
        }

        $filter_data = array(
            'filter_profile_name' => $filter_profile_name,
            'filter_shipping_name' => $filter_shipping_name,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $data['filter_profile_name'] = $filter_profile_name;
        $data['filter_shipping_name'] = $filter_shipping_name;

        $data['sort_id_etsy_profiles'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . '&sort=p.id_etsy_profiles' . $sort_url, true);
        $data['sort_profile_title'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . '&sort=p.profile_title' . $sort_url, true);
        $data['sort_etsy_category'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . '&sort=p.etsy_category_code' . $sort_url, true);
        $data['sort_shipping_origin_country'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_origin_country' . $sort_url, true);
        $data['sort_shipping_template_title'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . '&sort=st.shipping_template_title' . $sort_url, true);
        $data['sort_active'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . '&sort=p.active' . $sort_url, true);
        $data['sort_date_added'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . '&sort=p.date_added' . $sort_url, true);
        $data['sort_date_updated'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . '&sort=p.date_updated' . $sort_url, true);

        $profile_total = $this->model_kbetsy_kbetsy->getProfileTotal($filter_data);
        $profile_result = $this->model_kbetsy_kbetsy->getProfileDetails($filter_data);

        $data['profiles'] = array();
        foreach ($profile_result as $result) {
            $category_details = $this->model_kbetsy_kbetsy->getEtsyCategoryByCode($result['etsy_category_code']);

            $data['profiles'][] = array(
                'id_etsy_profiles' => $result['id_etsy_profiles'],
                'profile_title' => $result['profile_title'],
                'category_name' => isset($category_details['category_name']) ? $category_details['category_name'] : '',
                'shipping_template_title' => $result['shipping_template_title'],
                'active' => ($result['active']) ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
                'date_added' => $result['date_added'],
                'date_updated' => $result['date_updated'],
                'edit' => $this->url->link($this->module_path . '/profileUpdate', $this->session_token_key . '=' . $this->session_token . "&id_etsy_profiles=" . $result['id_etsy_profiles'] . $url, true),
                'delete' => $this->url->link($this->module_path . '/deleteProfile', $this->session_token_key . '=' . $this->session_token . "&id_etsy_profiles=" . $result['id_etsy_profiles'] . $url, true)
            );
        }
        $pagination = new Pagination();
        $pagination->total = $profile_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($profile_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($profile_total - $this->config->get('config_limit_admin'))) ? $profile_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $profile_total, ceil($profile_total / $this->config->get('config_limit_admin')));

        //links
        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['action'] = $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['route'] = $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['add'] = $this->url->link($this->module_path . '/profileUpdate', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['delete'] = $this->url->link($this->module_path . '/deleteProfiles', $this->session_token_key . '=' . $this->session_token . $url, 'SSL');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['text_edit_profile'] = $this->language->get('text_edit_profile');

        // Filter info
        $data['text_filter_profile_name'] = $this->language->get('text_filter_profile_name');
        $data['text_filter_shipping_name'] = $this->language->get('text_filter_shipping_name');
        $data['column_profile_id'] = $this->language->get('column_profile_id');
        $data['column_profile_title'] = $this->language->get('column_profile_title');
        $data['column_profile_category'] = $this->language->get('column_profile_category');
        $data['column_profile_shipping'] = $this->language->get('column_profile_shipping');
        $data['column_profile_status'] = $this->language->get('column_profile_status');
        $data['column_profile_added'] = $this->language->get('column_profile_added');
        $data['column_profile_updated'] = $this->language->get('column_profile_updated');
        $data['column_action'] = $this->language->get('column_action');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_profile_confirm_delete_etsy'] = $this->language->get('text_profile_confirm_delete_etsy');

        //Tooltips
        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_reset'] = $this->language->get('button_reset');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else if (isset($this->error['error'])) {
            $data['error'] = $this->error['error'];
        } else {
            $data['error'] = '';
        }

        if (isset($this->error['etsy_api_key'])) {
            $data['error_etsy_api_key'] = $this->error['etsy_api_key'];
        } else {
            $data['error_etsy_api_key'] = '';
        }
        $data['sort'] = $sort;
        $data['order'] = strtolower($order);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 2;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/profile', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/profile.tpl', $data));
        }
    }

    public function profileUpdate()
    {
        $this->load->language($this->module_path);

        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('catalog/category');
        $this->load->model('localisation/currency');
        $this->load->model('kbetsy/category');
        $this->load->model('kbetsy/profile');
        $this->load->model('kbetsy/shop_section');

        $this->document->setTitle($this->language->get('heading_title_main'));

        $data['store_categories'] = array();
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            if (isset($this->request->post['etsy']['profile']['id_etsy_category_final'])) {
                $data['id_etsy_category_final'] = $this->request->post['etsy']['profile']['id_etsy_category_final'];
            }

            if (isset($this->request->post['etsy']['profile']['etsy_category_text'])) {
                $data['etsy_category_text'] = $this->request->post['etsy']['profile']['etsy_category_text'];
            }

            if (isset($this->request->post['etsy']['profile']['profile_title'])) {
                $data['profile_title'] = $this->request->post['etsy']['profile']['profile_title'];
            }

            if (isset($this->request->post['etsy']['profile']['etsy_category'])) {
                $data['etsy_category'] = $this->request->post['etsy']['profile']['etsy_category'];
            }

            if (isset($this->request->post['product_category'])) {
                foreach ($this->request->post['product_category'] as $category_id) {
                    $category_info = $this->model_catalog_category->getCategory($category_id);
                    if ($category_info) {
                        $data['store_categories'][] = array(
                            'category_id' => $category_info['category_id'],
                            'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
                        );
                    }
                }
            }

            if (isset($this->request->post['etsy']['profile']['etsy_templates'])) {
                $data['etsy_templates'] = $this->request->post['etsy']['profile']['etsy_templates'];
            }
            if (isset($this->request->post['etsy']['profile']['who_made'])) {
                $data['who_made'] = $this->request->post['etsy']['profile']['who_made'];
            }
            if (isset($this->request->post['etsy']['profile']['when_made'])) {
                $data['when_made'] = $this->request->post['etsy']['profile']['when_made'];
            }
            if (isset($this->request->post['etsy']['profile']['is_customizable'])) {
                $data['is_customizable'] = $this->request->post['etsy']['profile']['is_customizable'];
            }
            
            if (isset($this->request->post['etsy']['profile']['price_type'])) {
                $data['price_type'] = $this->request->post['etsy']['profile']['price_type'];
            }
            if (isset($this->request->post['etsy']['profile']['price_management'])) {
                $data['price_management'] = $this->request->post['etsy']['profile']['price_management'];
            }
            if (isset($this->request->post['etsy']['profile']['increase_decrease'])) {
                $data['increase_decrease'] = $this->request->post['etsy']['profile']['increase_decrease'];
            }
            if (isset($this->request->post['etsy']['profile']['product_price'])) {
                $data['product_price'] = $this->request->post['etsy']['profile']['product_price'];
            }
            if (isset($this->request->post['etsy']['profile']['percentage_fixed'])) {
                $data['percentage_fixed'] = $this->request->post['etsy']['profile']['percentage_fixed'];
            }
            
            if (isset($this->request->post['etsy']['profile']['auto_renew'])) {
                $data['auto_renew'] = $this->request->post['etsy']['profile']['auto_renew'];
            }
            if (isset($this->request->post['etsy']['profile']['is_supply'])) {
                $data['is_supply'] = $this->request->post['etsy']['profile']['is_supply'];
            }
            if (isset($this->request->post['etsy']['profile']['etsy_recipient'])) {
                $data['etsy_recipient'] = $this->request->post['etsy']['profile']['etsy_recipient'];
            }
            if (isset($this->request->post['etsy']['profile']['etsy_occasion'])) {
                $data['etsy_occasion'] = $this->request->post['etsy']['profile']['etsy_occasion'];
            }
            if (isset($this->request->post['etsy']['profile']['shop_section_id'])) {
                $data['shop_section_id'] = $this->request->post['etsy']['profile']['shop_section_id'];
            }
        } else if (isset($this->request->get['id_etsy_profiles'])) {
            $filter_data = array();
            $profile_details = $this->model_kbetsy_kbetsy->getProfileDetails($filter_data, $this->request->get['id_etsy_profiles']);
            $data['etsy'] = array();
            $data['profile_title'] = $profile_details[0]['profile_title'];
            $data['etsy_category'] = $profile_details[0]['etsy_category_code'];
            $data['etsy_templates'] = $profile_details[0]['id_etsy_shipping_templates'];
            $data['who_made'] = $profile_details[0]['who_made'];
            $data['when_made'] = $profile_details[0]['when_made'];
            $data['is_customizable'] = $profile_details[0]['is_customizable'];
            $data['price_type'] = $profile_details[0]['price_type'];
            $data['price_management'] = $profile_details[0]['price_management'];
            $data['increase_decrease'] = $profile_details[0]['increase_decrease'];
            $data['product_price'] = $profile_details[0]['product_price'];
            $data['percentage_fixed'] = $profile_details[0]['percentage_fixed'];
            
            $data['auto_renew'] = $profile_details[0]['auto_renew'];
            $data['is_supply'] = $profile_details[0]['is_supply'];
            $data['etsy_recipient'] = $profile_details[0]['recipient'];
            $data['etsy_occasion'] = $profile_details[0]['occassion'];

            $data['id_etsy_category_final'] = $profile_details[0]['etsy_category_code'];
            $data['etsy_category_text'] = $profile_details[0]['etsy_category_text'];
            $data['shop_section_id'] = $profile_details[0]['shop_section_id'];

            $data['id_etsy_profiles'] = $this->request->get['id_etsy_profiles'];
            $categories = explode(",", $profile_details[0]['store_categories']);
            foreach ($categories as $category_id) {
                $category_info = $this->model_catalog_category->getCategory($category_id);
                if ($category_info) {
                    $data['store_categories'][] = array(
                        'category_id' => $category_info['category_id'],
                        'name' => ($category_info['path']) ? $category_info['path'] . ' &gt; ' . $category_info['name'] : $category_info['name']
                    );
                }
            }
        } else {
            $data['id_etsy_category_final'] = "";
            $data['etsy_category_text'] = "";
            $data['profile_title'] = "";
            $data['etsy_category'] = "";
            $data['etsy_templates'] = "";
            $data['who_made'] = "";
            $data['when_made'] = "";
            $data['is_customizable'] = "";
            $data['price_type'] = "";
            $data['price_management'] = "";
            $data['increase_decrease'] = "";
            $data['product_price'] = "";
            $data['percentage_fixed'] = "";
            
            $data['shop_section_id'] = "";
            $data['auto_renew'] = "";
            $data['is_supply'] = "";
            $data['etsy_recipient'] = "";
            $data['etsy_occasion'] = "";
            $data['category'] = array();
        }

        /* To Reset the Category Droppdown if category is already choosed to avoid preselect to category in dropdown. SO that dropdown change event can be triggered. */
        if ($data['id_etsy_category_final'] != "") {
            $data['etsy_category'] = "";
        }
        if (isset($this->request->post['id_etsy_profiles'])) {
            $data['id_etsy_profiles'] = $this->request->post['id_etsy_profiles'];
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (isset($this->request->post['id_etsy_profiles'])) {
                $this->model_kbetsy_profile->editProfile($this->request->post);
                $this->session->data['success'] = $this->language->get('etsy_profile_update_success');
            } else {
                $this->session->data['success'] = $this->language->get('etsy_profile_update_success');
                $this->model_kbetsy_profile->addProfile($this->request->post);
            }
            $this->response->redirect($this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else if (isset($this->error['error'])) {
            $data['error'] = $this->error['error'];
        } else {
            $data['error'] = '';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_profile'),
            'href' => $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->request->post['id_etsy_profiles']) || isset($this->request->get['id_etsy_profiles'])) {
            $data['heading_title_main'] = $this->language->get('heading_title_profile_edit');
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_profile_edit'),
                'href' => $this->url->link($this->module_path . '/profileUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL'),
                'separator' => ' :: '
            );
        } else {
            $data['heading_title_main'] = $this->language->get('heading_title_profile_add');
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_profile_add'),
                'href' => $this->url->link($this->module_path . '/profileUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL'),
                'separator' => ' :: '
            );
        }

        if (isset($this->request->get['id_etsy_profiles'])) {
            $data['action'] = $this->url->link($this->module_path . '/profileUpdate', 'id_etsy_profiles=' . $this->request->get['id_etsy_profiles'] . '&' . $this->session_token_key . '=' . $this->session_token, 'SSL');
        } else {
            $data['action'] = $this->url->link($this->module_path . '/profileUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL');
        }

        $data['route'] = $this->url->link($this->module_path . '/profileUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token, 'SSL');

        // General Settings tab & info
        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_profile_title'] = $this->language->get('text_profile_title');
        $data['text_etsy_category'] = $this->language->get('text_etsy_category');
        $data['text_attribute_mapping'] = $this->language->get('text_attribute_mapping');
        $data['text_attribute_mapping_size'] = $this->language->get('text_attribute_mapping_size');
        $data['text_attribute_mapping_color'] = $this->language->get('text_attribute_mapping_color');
        $data['text_store_category'] = $this->language->get('text_store_category');
        $data['text_shipping_template'] = $this->language->get('text_shipping_template');
        $data['text_etsy_currency'] = $this->language->get('text_etsy_currency');
        $data['text_who_made'] = $this->language->get('text_who_made');
        $data['text_when_made'] = $this->language->get('text_when_made');
        $data['text_recipient'] = $this->language->get('text_recipient');
        $data['text_occasion'] = $this->language->get('text_occasion');
        $data['text_general_store_name'] = $this->language->get('text_general_store_name');
        $data['text_general_etsy_quantity'] = $this->language->get('text_general_etsy_quantity');
        $data['text_general_guestenable'] = $this->language->get('text_general_guestenable');
        $data['text_etsy_api_detail'] = $this->language->get('text_etsy_api_detail');
        $data['text_etsy_api_detail_title'] = $this->language->get('text_etsy_api_detail_title');
        $data['text_etsy_store'] = $this->language->get('text_etsy_store');
        $data['text_etsy_merchant_id'] = $this->language->get('text_etsy_merchant_id');
        $data['text_etsy_market_place_id'] = $this->language->get('text_etsy_market_place_id');
        $data['text_etsy_api_key'] = $this->language->get('text_etsy_api_key');
        $data['text_etsy_api_secret'] = $this->language->get('text_etsy_api_secret');
        $data['text_etsy_api_host'] = $this->language->get('text_etsy_api_host');
        $data['text_product_listing'] = $this->language->get('text_product_listing');
        $data['text_product_update'] = $this->language->get('text_product_update');
        $data['text_product_id'] = $this->language->get('text_product_id');
        $data['text_product_name'] = $this->language->get('text_product_name');
        $data['text_product_sku'] = $this->language->get('text_product_sku');
        $data['text_product_price'] = $this->language->get('text_product_price');
        $data['text_product_quantity'] = $this->language->get('text_product_quantity');
        $data['text_status'] = $this->language->get('text_status');
        $data['text_action'] = $this->language->get('text_action');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_price_management'] = $this->language->get('text_price_management');
        $data['text_increase_decrese_price'] = $this->language->get('text_increase_decrese_price');
        $data['text_price_value'] = $this->language->get('text_price_value');
        $data['text_price_percentage_fixed'] = $this->language->get('text_price_percentage_fixed');
        $data['text_duration_hint'] = $this->language->get('text_duration_hint');
        $data['text_increase_price'] = $this->language->get('text_increase_price');
        $data['text_decrease_price'] = $this->language->get('text_decrease_price');
        $data['text_price_fixed'] = $this->language->get('text_price_fixed');
        $data['text_price_percentage'] = $this->language->get('text_price_percentage');
        $data['text_actual_price'] = $this->language->get('text_actual_price');
        $data['text_special_price'] = $this->language->get('text_special_price');
        $data['text_price_type'] = $this->language->get('text_price_type');
        $data['text_price_type_hint'] = $this->language->get('text_price_type_hint');
        $data['text_shop_section_select'] = $this->language->get('text_shop_section_select');
        $data['text_shop_section_select_option'] = $this->language->get('text_shop_section_select_option');
 
        
        //Tooltips
        $data['image_size_tooltip'] = $this->language->get('image_size_tooltip');
        $data['general_enable_tooltip'] = $this->language->get('general_enable_tooltip');
        $data['text_etsy_store_tooltip'] = $this->language->get('text_etsy_store_tooltip');
        $data['text_etsy_merchant_id_tooltip'] = $this->language->get('text_etsy_merchant_id_tooltip');
        $data['text_etsy_market_place_id_tooltip'] = $this->language->get('text_etsy_market_place_id_tooltip');
        $data['text_etsy_api_key_tooltip'] = $this->language->get('text_etsy_api_key_tooltip');
        $data['text_etsy_api_secret_tooltip'] = $this->language->get('text_etsy_api_secret_tooltip');
        $data['text_etsy_api_host_type_tooltip'] = $this->language->get('text_etsy_api_host_type_tooltip');
        $data['text_edit_profile_add'] = $this->language->get('text_edit_profile_add');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['entry_select_etsy_category'] = $this->language->get('entry_select_etsy_category');
        $data['entry_select_size'] = $this->language->get('entry_select_size');
        $data['entry_select_color'] = $this->language->get('entry_select_color');
        $data['entry_select_template'] = $this->language->get('entry_select_template');
        $data['entry_select_currency'] = $this->language->get('entry_select_currency');
        $data['entry_is_customize'] = $this->language->get('entry_is_customize');
        $data['entry_auto_renew'] = $this->language->get('entry_auto_renew');
        $data['entry_select_who_made'] = $this->language->get('entry_select_who_made');
        $data['entry_select_when_made'] = $this->language->get('entry_select_when_made');
        $data['entry_is_supply'] = $this->language->get('entry_is_supply');
        $data['entry_recepient'] = $this->language->get('entry_recepient');
        $data['entry_select_recepient'] = $this->language->get('entry_select_recepient');
        $data['entry_occasion'] = $this->language->get('entry_occasion');
        $data['entry_select_occasion'] = $this->language->get('entry_select_occasion');
        $data['a_finised_product'] = $this->language->get('a_finised_product');
        $data['tool_to_make_things'] = $this->language->get('tool_to_make_things');
        $data['select_leaf_category'] = $this->language->get('select_leaf_category');
        $data['text_confirm_category'] = $this->language->get('text_confirm_category');
        $data['text_change_category'] = $this->language->get('text_change_category');

        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');

        $data['allcurrencies'] = array();
        $results = $this->model_localisation_currency->getCurrencies();
        foreach ($results as $result) {
            if ($result['status']) {
                $data['allcurrencies'][] = array(
                    'currency_id' => $result['currency_id'],
                    'title' => $result['title'],
                    'code' => $result['code'],
                    'symbol_left' => $result['symbol_left'],
                    'symbol_right' => $result['symbol_right']
                );
            }
        }
        $data['etsy_categories'] = $this->model_kbetsy_category->getEtsyCategories();
        $data['etsy_ship_templates'] = $this->model_kbetsy_kbetsy->getShipTemplates();
        $data['etsy_shop_sections'] = $this->model_kbetsy_shop_section->getShopSections(array());
        
        $whoMadeOptions = array(
            'i_did' => $this->language->get('text_i_did'),
            'collective' => $this->language->get('text_collective'),
            'someone_else' => $this->language->get('text_someone_else')
        );

        if ($whoMadeOptions) {
            $whoMadeList = array();
            foreach ($whoMadeOptions as $key => $value) {
                $whoMadeList[] = array(
                    'id_option' => $key,
                    'name' => $value
                );
            }
        }
        $data['who_made_list'] = $whoMadeList;
        //Prepare array of When Made
        $whenMadeOptions = array(
            'made_to_order' => $this->language->get('text_made_to_order'),
            '2020_'.date("Y") => '2020 - '.date("Y"),
            '2010_2019' => '2010 - 2019',
            '2001_2009' => '2001 - 2009',
            'before_2001' => $this->language->get('text_before') . ' 2001',
            '2000_2000' => '2000',
            '1990s' => '1990s',
            '1980s' => '1980s',
            '1970s' => '1970s',
            '1960s' => '1960s',
            '1950s' => '1950s',
            '1940s' => '1940s',
            '1930s' => '1930s',
            '1920s' => '1920s',
            '1910s' => '1910s',
            '1900s' => '1900s',
            '1800s' => '1800s',
            '1700s' => '1700s',
            'before_1700' => $this->language->get('text_before') . ' 1700'
        );
        $data['occasions_list'] = array('' => $this->language->get('entry_select_occasion'),
            'anniversary' => 'Anniversary',
            'baptism' => 'Baptism',
            'bar_or_bat_mitzvah' => 'Bar or Bat Mitzvah',
            'birthday' => 'Birthday',
            'canada_day' => 'Canada Day',
            'chinese_new_year' => 'Chinese New Year',
            'cinco_de_mayo' => 'Cinco de Mayo',
            'confirmation' => 'Confirmation',
            'christmas' => 'Christmas',
            'day_of_the_dead' => 'Day of the Dead',
            'easter' => 'Easter',
            'eid' => 'Eid',
            'engagement' => 'Engagement',
            'fathers_day' => 'Fathers Day',
            'get_well' => 'Get Well',
            'graduation' => 'Graduation',
            'halloween' => 'Halloween',
            'hanukkah' => 'Hanukkah',
            'housewarming' => 'House Warming',
            'kwanzaa' => 'Kwanzaa',
            'prom' => 'Prom',
            'july_4th' => '4th July',
            'mothers_day' => 'Mothers Day',
            'new_baby' => 'New Baby',
            'new_years' => 'New Year',
            'quinceanera' => 'Quinceanera',
            'retirement' => 'Retirement',
            'st_patricks_day' => 'St. Patricks Day',
            'sweet_16' => 'Sweet 16',
            'sympathy' => 'Sympathy',
            'thanksgiving' => 'Thanks Giving',
            'valentines' => 'Valentines',
            'wedding' => 'Wedding'
        );
        $data['recipients_list'] = array(
            '' => $this->language->get('entry_select_recepient'),
            'men' => 'Men',
            'women' => 'Women',
            'unisex_adults' => 'Unisex Adults',
            'teen_boys' => 'Teen Boys',
            'teen_girls' => 'Teen Girls',
            'teens' => 'Teens',
            'boys' => 'Boys',
            'girls' => 'Girls',
            'children' => 'Children',
            'baby_boys' => 'Baby Boys',
            'baby_girls' => 'Baby Girls',
            'babies' => 'Babies',
            'birds' => 'Birds',
            'cats' => 'Cats',
            'dogs' => 'Dogs',
            'pets' => 'Pets',
            'not_specified' => 'Not Specified'
        );

        if ($whenMadeOptions) {
            $whenMadeList = array();
            foreach ($whenMadeOptions as $key => $value) {
                $whenMadeList[] = array(
                    'id_option' => $key,
                    'name' => $value
                );
            }
        }
        $data['when_made_list'] = $whenMadeList;
        $data['store_attributes'] = $this->model_kbetsy_kbetsy->getAttributes();

        if (isset($this->error['etsy_profile_title'])) {
            $data['error_etsy_profile_title'] = $this->error['etsy_profile_title'];
        } else {
            $data['error_etsy_profile_title'] = '';
        }
        if (isset($this->error['etsy_category'])) {
            $data['error_etsy_category'] = $this->error['etsy_category'];
        } else {
            $data['error_etsy_category'] = '';
        }

        if (isset($this->error['etsy_templates'])) {
            $data['error_etsy_templates'] = $this->error['etsy_templates'];
        } else {
            $data['error_etsy_templates'] = '';
        }

        if (isset($this->error['etsy_who_made'])) {
            $data['error_who_made'] = $this->error['etsy_who_made'];
        } else {
            $data['error_who_made'] = '';
        }
        if (isset($this->error['etsy_when_made'])) {
            $data['error_when_made'] = $this->error['etsy_when_made'];
        } else {
            $data['error_when_made'] = '';
        }
        if (isset($this->error['etsy_store_category'])) {
            $data['error_category'] = $this->error['etsy_store_category'];
        } else {
            $data['error_category'] = '';
        }
        if (isset($this->error['etsy_is_customizable'])) {
            $data['error_is_customizable'] = $this->error['etsy_is_customizable'];
        } else {
            $data['error_is_customizable'] = '';
        }
        
        if (isset($this->error['etsy_product_price'])) {
            $data['error_etsy_product_price'] = $this->error['etsy_product_price'];
        } else {
            $data['error_etsy_product_price'] = '';
        }
        

        if (isset($this->error['etsy_recipient'])) {
            $data['error_etsy_recipient'] = $this->error['etsy_recipient'];
        } else {
            $data['error_etsy_recipient'] = '';
        }
        if (isset($this->error['etsy_occasion'])) {
            $data['error_etsy_occasion'] = $this->error['etsy_occasion'];
        } else {
            $data['error_etsy_occasion'] = '';
        }

        $shippingTemplate = $this->model_kbetsy_kbetsy->getShipTemplates();
        if (!empty($shippingTemplate)) {
            $data['shipping_template'] = true;
        } else {
            $data['shipping_template'] = false;
        }
        $data['shipping_template_not_avaliable_error'] = sprintf($this->language->get('shipping_template_not_avaliable_error'), $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        $data['category_not_avaliable_error'] = sprintf($this->language->get('category_not_avaliable_error'), HTTPS_CATALOG . 'index.php?route=kbetsy/category');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 2;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/profile_update', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/profile_update.tpl', $data));
        }
    }

    
    public function ajaxGetPropertiesList()
    {
        $propertiesListHTML = '';
        if(isset($this->request->get['category_id'])){
            $propertSetJson = file_get_contents("https://www.etsy.com/api/v3/ajax/public/taxonomy/" . $this->request->get['category_id'] . "/properties");
            $propertySet = json_decode($propertSetJson, TRUE);
            if (!empty($propertySet)) {
                $propertiesListHTML = $this->displayPropertiesList($propertySet);
                
            }
        }
        echo $propertiesListHTML;
        die();
    }
    /** Display Attribute Dropdown */
   public function displayPropertiesList($properties = array())
   {
        $fields = array();
        if (!empty($properties)) {
            //Get Mapped Attribute
            $selected_attribute = array();
            if (isset($this->request->get['id_etsy_profiles'])) {
                $mappedAttribute = $this->db->query("SELECT * FROM " . DB_PREFIX . "etsy_attribute_option_mapping WHERE id_etsy_profiles = '" . (int) $this->request->get['id_etsy_profiles'] . "'");
                if ($mappedAttribute->num_rows) {
                    foreach ($mappedAttribute->rows as $mapped) {
                        $selected_attribute[] = array("id" => $mapped['property_id'], "value" => $mapped['id_attribute_group']);
                    }
                }
            }

           foreach ($properties as $propery) {
               if ($propery["attributeId"] != null && $propery["attributeId"] != 3) {
                   $selected_values = array();
                   $flag = false;

                    /* Popuplate Selecte Values from the DB Values */
                    foreach ($selected_attribute as $attribute) {
                        if ($attribute["id"] == $propery["propertyId"]) {
                            $selected_value = $attribute["value"];
                            $selected_values = explode(",", $selected_value);
                            $flag = true;
                        }
                    }

                    /* In case no DB selected value, Pick the default selected value from the Etsy */
                    if ($flag == false) {
                        if (isset($propery['selectedValues'])) {
                            foreach ($propery['selectedValues'] as $selectedValues) {
                                $selected_values[] = $selectedValues['id'];
                            }
                        }
                    }

                    $fields[] = array(
                        'type' => 'checkbox',
                        'name' => $propery['name'],
                        'multi' => $propery['isMultiValued'],
                        'id' => $propery['propertyId'],
                        'required' => $propery['isRequired'],
                        'values' => $propery['possibleValues'],
                        'selected' => $selected_values
                    );
               }
           }

           $data['property_list'] = $fields;
           

            if (VERSION >= '2.2.0.0') {
                return  ($this->load->view($this->module_path . '/properties_list', $data));
            } else {
                return  ($this->load->view($this->module_path . '/properties_list.tpl', $data));
            }
        } else {
            return "";
        }
   }
    
    public function getSubcategories()
    {
        $this->load->model('kbetsy/category');
        if (isset($this->request->get['category_id'])) {
            $category_id = $this->request->get['category_id'];
            $data = $this->model_kbetsy_category->getEtsyCategories($category_id);
            if($data == false) {
                $name = 'category/getTaxonomyNodeProperties&taxonomy_id=' . $category_id;
                //$scales = $this->executeCRON($name);
                //echo $scales;
                echo json_encode(array("scales" => ""));
            } else {
                echo json_encode($data);
            }
        }
        die();
    }

    public function shopSection()
    {
        $this->load->language($this->module_path);
        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('kbetsy/shop_section');

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'title';
        }
        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        $url = '';
        $sort_url = '';
        $filter_url = '';

        if (isset($this->request->get['page'])) {
            $filter_url .= '&page=' . $this->request->get['page'];
            $sort_url .= '&page=' . $this->request->get['page'];
        }
        
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
            $filter_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
            $filter_url .= '&order=' . $this->request->get['order'];
        }

        if ($order == 'ASC') {
            $sort_url .= '&order=DESC';
        } else {
            $sort_url .= '&order=ASC';
        }
        
        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token. $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_shop_section'),
            'href' => $this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $filter_data = array(
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $data['sort'] = $sort;
        $data['order'] = strtolower($order);

        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['sort_etsy_shop_section_id'] = $this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token . '&sort=etsy_shop_section_id' . $sort_url, true);
        $data['sort_title'] = $this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token . '&sort=title' . $sort_url, true);

        $shop_section_total = $this->model_kbetsy_shop_section->getShopSectionTotal($filter_data);
        $shop_sections = $this->model_kbetsy_shop_section->getShopSections($filter_data);

        $data['shop_sections'] = array();
        foreach ($shop_sections as $result) {
            $data['shop_sections'][] = array(
                'etsy_shop_section_id' => $result['etsy_shop_section_id'],
                'title' => $result['title'],
                'edit' => $this->url->link($this->module_path . '/shopSectionUpdate', $this->session_token_key . '=' . $this->session_token . "&shop_section_id=" . $result['shop_section_id'] . $url, true),
                'delete' => $this->url->link($this->module_path . '/deleteShopSection', $this->session_token_key . '=' . $this->session_token . "&shop_section_id=" . $result['shop_section_id'] . $url, true)
            );
        }

        $pagination = new Pagination();
        $pagination->total = $shop_section_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($shop_section_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($shop_section_total - $this->config->get('config_limit_admin'))) ? $shop_section_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $shop_section_total, ceil($shop_section_total / $this->config->get('config_limit_admin')));

        //Links
        $data['add'] = $this->url->link($this->module_path . '/shopSectionUpdate', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['sync_shop_section_url'] = HTTPS_CATALOG . 'index.php?route=kbetsy/shop_section';

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['text_shop_section'] = $this->language->get('text_shop_section');
        $data['text_sync_shop_section'] = $this->language->get('text_sync_shop_section');
        $data['text_no_shop_section_error'] = sprintf($this->language->get('text_no_shop_section_error'), $data['sync_shop_section_url']);
        $data['text_confirm_delete_etsy'] = $this->language->get('text_confirm_delete_etsy');

        $data['column_etsy_shop_section_id'] = $this->language->get('column_etsy_shop_section_id');
        $data['column_etsy_shop_section_title'] = $this->language->get('column_etsy_shop_section_title');
        $data['column_action'] = $this->language->get('column_action');

        $data['button_add'] = $this->language->get('button_add');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_edit'] = $this->language->get('button_edit');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 9;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/shopsection', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/shopsection.tpl', $data));
        }
    }
    
    public function shopSectionUpdate()
    {
        $this->load->language($this->module_path);
        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('kbetsy/shop_section');

        $this->document->setTitle($this->language->get('heading_title_main'));

        if (isset($this->request->get['shop_section_id'])) {
            $data['shop_section_id'] = $this->request->get['shop_section_id'];

            $filter_data = array();

            $shop_section_details = $this->model_kbetsy_shop_section->getShopSections($filter_data, $this->request->get['shop_section_id']);
            if (!empty($shop_section_details)) {
                $data['shop_section_title'] = $shop_section_details["title"];
                $data['etsy_shop_section_id'] = $shop_section_details["etsy_shop_section_id"];
                $data['shop_id'] = $shop_section_details["shop_id"];
            } else {
                $this->response->redirect($this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        } else {
            $data['shop_section_title'] = "";
            $data['etsy_shop_section_id'] = "";
            $data['shop_id'] = "";
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (isset($this->request->post['shop_section_id']) && $this->request->post['shop_section_id'] != "") {
                $this->model_kbetsy_shop_section->shopSectionUpdate($this->request->post);
                $this->session->data['success'] = $this->language->get('shop_section_update_success');
                $this->executeCRON("shop_section/renewShopSectionRequest");
            } else {
                $this->session->data['success'] = $this->language->get('shop_section_add_success');
                $this->model_kbetsy_shop_section->shopSectionUpdate($this->request->post);
                $this->executeCRON("shop_section/createShopSectionRequest");
            }
            $this->response->redirect($this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_shop_section'),
            'href' => $this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->request->get['shop_section_id'])) {
            $data['heading_title_main'] = $this->language->get('heading_title_shop_section_edit');
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shop_section_edit'),
                'href' => $this->url->link($this->module_path . '/shopSectionUpdate?shop_section_id='.$this->request->get['shop_section_id'], $this->session_token_key . '=' . $this->session_token, 'SSL'),
                'separator' => ' :: '
            );
        } else {
            $data['heading_title_main'] = $this->language->get('heading_title_shop_section_add');
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shop_section_add'),
                'href' => $this->url->link($this->module_path . '/shopSectionUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL'),
                'separator' => ' :: '
            );
        }

        //links
        if (isset($this->request->get['shop_section_id'])) {
            $data['action'] = $this->url->link($this->module_path . '/shopSectionUpdate&shop_section_id=' . $this->request->get['shop_section_id'], $this->session_token_key . '=' . $this->session_token, 'SSL');
        } else {
            $data['action'] = $this->url->link($this->module_path . '/shopSectionUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL');
        }
        $data['cancel'] = $this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token, 'SSL');

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_select_shop'] = $this->language->get('text_select_shop');
        $data['text_shop_section_title'] = $this->language->get('text_shop_section_title');

        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        if (isset($this->error['shop_section_title'])) {
            $data['error_shop_section_title'] = $this->error['shop_section_title'];
        } else {
            $data['error_shop_section_title'] = '';
        }

        if (isset($this->request->post['etsy']['shop_section']['title'])) {
            $data['shop_section_title'] = $this->request->post['etsy']['shop_section']['title'];
        }
        if (isset($this->request->post['etsy']['shop_section']['shop_id'])) {
            $data['shop_id'] = $this->request->post['etsy']['shop_section']['shop_id'];
        }
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 9;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);
        
        /* Fetch Etsy Shops */
        $etsy_shops = array();
        $etsy_shop_data = $this->executeCRON("shop_section/getEtsyShop");
        $etsy_shop_data_array = json_decode($etsy_shop_data, TRUE);
        
        /* If error in getting the shop details, Show error page */
        if($etsy_shop_data_array['type'] == 'error') {
            
            /* Error Page Heading Title */
            if (isset($this->request->get['shop_section_id'])) {
                $data['text_edit_general'] = $this->language->get('heading_title_shop_section_edit');
            } else {
                $data['text_edit_general'] = $this->language->get('heading_title_shop_section_add');
            }
            $data['general_settings'] = $this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token, 'SSL');
            
            $data['error'] = $this->language->get('error_fetch_shop_details')."<br/>". $etsy_shop_data_array['message'];
            if (VERSION >= '2.2.0.0') {
                $this->response->setOutput($this->load->view($this->module_path . '/error', $data));
            } else {
                $this->response->setOutput($this->load->view($this->module_path . '/error.tpl', $data));
            }
        } else {
            foreach($etsy_shop_data_array['data'] as $etsy_shop) {
                $etsy_shops[] = array("shop_id" => $etsy_shop['shop_id'], "title" => $etsy_shop['shop_name']);
            }
            $data['etsy_shops'] = $etsy_shops;
            
            if (VERSION >= '2.2.0.0') {
                $this->response->setOutput($this->load->view($this->module_path . '/shop_section_form', $data));
            } else {
                $this->response->setOutput($this->load->view($this->module_path . '/shop_section_form.tpl', $data));
            }
        }
    }
    
    public function deleteShopSection()
    {
        $this->load->language($this->module_path);
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('kbetsy/shop_section');

        if (!empty($this->request->get['shop_section_id'])) {
            if($this->demo_flag == 0) {            
                $this->model_kbetsy_shop_section->deleteShopSection($this->request->get['shop_section_id']);
                $this->executeCRON("shop_section/deleteShopSectionRequest");
                $this->session->data['success'] = $this->language->get('shop_section_delete_success');
            } else {
                $this->session->data['error'] = $this->language->get('text_demo_mode_action');
            }
            $this->response->redirect($this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        } else {
            $this->response->redirect($this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }
    }

    public function shippingTemplates()
    {
        $this->load->language($this->module_path);
        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');

        if (isset($this->request->get['filter_profile_name'])) {
            $filter_profile_name = $this->request->get['filter_profile_name'];
        } else {
            $filter_profile_name = null;
        }
        if (isset($this->request->get['filter_shipping_name'])) {
            $filter_shipping_name = $this->request->get['filter_shipping_name'];
        } else {
            $filter_shipping_name = null;
        }
        if (isset($this->request->get['filter_shipping_country'])) {
            $filter_shipping_country = $this->request->get['filter_shipping_country'];
        } else {
            $filter_shipping_country = null;
        }
        if (isset($this->request->get['filter_min_proc_days'])) {
            $filter_min_proc_days = $this->request->get['filter_min_proc_days'];
        } else {
            $filter_min_proc_days = null;
        }
        if (isset($this->request->get['filter_max_proc_days'])) {
            $filter_max_proc_days = $this->request->get['filter_max_proc_days'];
        } else {
            $filter_max_proc_days = null;
        }
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'id_etsy_shipping_templates';
        }
        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';
        $sort_url = '';
        $filter_url = '';
        
        if (isset($this->request->get['filter_profile_name'])) {
            $url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_shipping_name'])) {
            $url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
        }
        
        if (isset($this->request->get['filter_shipping_country'])) {
            $url .= '&filter_shipping_country=' . urlencode(html_entity_decode($this->request->get['filter_shipping_country'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_shipping_country=' . urlencode(html_entity_decode($this->request->get['filter_shipping_country'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_shipping_country=' . urlencode(html_entity_decode($this->request->get['filter_shipping_country'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_min_proc_days'])) {
            $url .= '&filter_min_proc_days=' . urlencode(html_entity_decode($this->request->get['filter_min_proc_days'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_min_proc_days=' . urlencode(html_entity_decode($this->request->get['filter_min_proc_days'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_min_proc_days=' . urlencode(html_entity_decode($this->request->get['filter_min_proc_days'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_max_proc_days'])) {
            $url .= '&filter_max_proc_days=' . urlencode(html_entity_decode($this->request->get['filter_max_proc_days'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_max_proc_days=' . urlencode(html_entity_decode($this->request->get['filter_max_proc_days'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_max_proc_days=' . urlencode(html_entity_decode($this->request->get['filter_max_proc_days'], ENT_QUOTES, 'UTF-8'));
        }


        if (isset($this->request->get['page'])) {
            $filter_url .= '&page=' . $this->request->get['page'];
            $sort_url .= '&page=' . $this->request->get['page'];
        }
        
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
            $filter_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
            $filter_url .= '&order=' . $this->request->get['order'];
        }

        if ($order == 'ASC') {
            $sort_url .= '&order=DESC';
        } else {
            $sort_url .= '&order=ASC';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token. $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_shipping'),
            'href' => $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $filter_data = array(
            'filter_profile_name' => $filter_profile_name,
            'filter_shipping_name' => $filter_shipping_name,
            'filter_shipping_country' => $filter_shipping_country,
            'filter_min_proc_days' => $filter_min_proc_days,
            'filter_max_proc_days' => $filter_max_proc_days,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $data['sort'] = $sort;
        $data['order'] = strtolower($order);

        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['sort_id_etsy_shipping_templates'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token . '&sort=id_etsy_shipping_templates' . $sort_url, true);
        $data['sort_shipping_template_title'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_template_title' . $sort_url, true);
        $data['sort_shipping_origin_country'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_origin_country' . $sort_url, true);
        $data['sort_shipping_min_process_days'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_min_process_days' . $sort_url, true);
        $data['sort_shipping_max_process_days'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_max_process_days' . $sort_url, true);
        
        $shipping_total = $this->model_kbetsy_kbetsy->getShippingTemplateTotal($filter_data);
        $shipping_templates_details = $this->model_kbetsy_kbetsy->getShippingTemplateDetails($filter_data);
        
        $data['shipping_templates'] = array();
        foreach ($shipping_templates_details as $result) {
            $data['shipping_templates'][] = array(
                'id_etsy_shipping_templates' => $result['id_etsy_shipping_templates'],
                'shipping_template_title' => $result['shipping_template_title'],
                'shipping_origin_country' => $result['shipping_origin_country'],
                'shipping_template_title' => $result['shipping_template_title'],
                'shipping_min_process_days' => $result['shipping_min_process_days'],
                'shipping_max_process_days' => $result['shipping_max_process_days'],
                'add' => $this->url->link($this->module_path . '/shippingTemplateEntryUpdate', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates=" . $result['id_etsy_shipping_templates'] . $url, true),
                'view' => $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates=" . $result['id_etsy_shipping_templates'] . $url, true),
                'edit' => $this->url->link($this->module_path . '/shippingTemplateUpdate', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates=" . $result['id_etsy_shipping_templates'] . $url, true),
                'delete' => $this->url->link($this->module_path . '/deleteShippingTemplates', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates=" . $result['id_etsy_shipping_templates'] . $url, true)
            );
        }

        $pagination = new Pagination();
        $pagination->total = $shipping_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($shipping_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($shipping_total - $this->config->get('config_limit_admin'))) ? $shipping_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $shipping_total, ceil($shipping_total / $this->config->get('config_limit_admin')));

        //Links
        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['action'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['route'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['add'] = $this->url->link($this->module_path . '/shippingTemplateUpdate', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['delete'] = $this->url->link($this->module_path . '/deleteShippingTemplates', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['sync_template_url'] = HTTPS_CATALOG . 'index.php?route=kbetsy/template';

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['text_edit_shipping'] = $this->language->get('text_edit_shipping');

        // Filter info
        $data['text_filter_profile_name'] = $this->language->get('text_filter_profile_name');
        $data['text_filter_shipping_name'] = $this->language->get('text_filter_shipping_name');
        $data['text_filter_shipping_country'] = $this->language->get('text_filter_shipping_country');
        $data['text_filter_min_proc_days'] = $this->language->get('text_filter_min_proc_days');
        $data['text_filter_max_proc_days'] = $this->language->get('text_filter_max_proc_days');
        $data['column_template_id'] = $this->language->get('column_template_id');
        $data['column_shipping_title'] = $this->language->get('column_shipping_title');
        $data['column_min_processing'] = $this->language->get('column_min_processing');
        $data['column_max_processing'] = $this->language->get('column_max_processing');
        $data['column_shipping_origin'] = $this->language->get('column_shipping_origin');
        $data['column_action'] = $this->language->get('column_action');
        $data['text_sync_shipping_template'] = $this->language->get('text_sync_shipping_template');
        $data['text_no_shipping_template_error'] = sprintf($this->language->get('text_no_shipping_template_error'), $data['sync_template_url']);
        $data['button_add'] = $this->language->get('button_add');

        //Buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['text_confirm_delete_etsy'] = $this->language->get('text_confirm_delete_etsy');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_view_entries'] = $this->language->get('button_view_entries');
        $data['button_add_entry'] = $this->language->get('button_add_entry');
        $data['button_reset'] = $this->language->get('button_reset');
        $data['country_missing_error'] = $this->language->get('etsy_country_missing_error');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        if (isset($this->error['etsy_api_key'])) {
            $data['error_etsy_api_key'] = $this->error['etsy_api_key'];
        } else {
            $data['error_etsy_api_key'] = '';
        }
        $data['etsycountires'] = $this->model_kbetsy_kbetsy->getEtsyCountriesCount();
        $data['text_sync_now'] = $this->language->get('text_sync_now');
        $data['etsy_country_sync_url'] = HTTPS_CATALOG . 'index.php?route=kbetsy/country';

        $data['sort'] = $sort;
        $data['filter_profile_name'] = $filter_profile_name;
        $data['filter_shipping_name'] = $filter_shipping_name;
        $data['filter_shipping_country'] = $filter_shipping_country;
        $data['filter_min_proc_days'] = $filter_min_proc_days;
        $data['filter_max_proc_days'] = $filter_max_proc_days;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 3;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/shipping', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/shipping.tpl', $data));
        }
    }

    public function shippingTemplateUpdate()
    {
        $this->load->language($this->module_path);
        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');

        $this->document->setTitle($this->language->get('heading_title_main'));

        if (isset($this->request->get['id_etsy_shipping_templates'])) {
            $filter_data = array();
            $data['etsy'] = array();

            $shipping_templates_details = $this->model_kbetsy_kbetsy->getShippingTemplateDetails($filter_data, $this->request->get['id_etsy_shipping_templates']);
            if (!empty($shipping_templates_details)) {
                $data['etsy'] = array(
                    'template' => array(
                        'template_title' => $shipping_templates_details[0]['shipping_template_title'],
                        'primary_cost' => $shipping_templates_details[0]['shipping_primary_cost'],
                        'secondary_cost' => $shipping_templates_details[0]['shipping_secondary_cost'],
                        'min_process_days' => $shipping_templates_details[0]['shipping_min_process_days'],
                        'max_process_days' => $shipping_templates_details[0]['shipping_max_process_days'],
                    )
                );
                $data['etsy_select_country'] = $shipping_templates_details[0]['shipping_origin_country_id'];
                $data['id_etsy_shipping_templates'] = $shipping_templates_details[0]['id_etsy_shipping_templates'];
            }
        } else {
            $data['etsy_select_country'] = 0;
            $data['id_etsy_shipping_templates'] = "";
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (isset($this->request->post['id_etsy_shipping_templates']) && $this->request->post['id_etsy_shipping_templates'] != "") {
                $this->model_kbetsy_kbetsy->shippingTemplateUpdate($this->request->post);
                $this->session->data['success'] = $this->language->get('shipping_profile_update_success');
                $this->executeCRON("template/renewShippingTemplateRequest");
            } else {
                $this->session->data['success'] = $this->language->get('shipping_profile_update_success');
                $this->model_kbetsy_kbetsy->shippingTemplateUpdate($this->request->post);
                $this->executeCRON("template/createShippingTemplateRequest");
            }
            $this->response->redirect($this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_shipping'),
            'href' => $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->request->get['id_etsy_shipping_templates'])) {
            $data['heading_title_main'] = $this->language->get('heading_title_shipping_edit');
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shipping_edit'),
                'href' => $this->url->link($this->module_path . '/shippingTemplateUpdate&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'], $this->session_token_key . '=' . $this->session_token, 'SSL'),
                'separator' => ' :: '
            );
        } else {
            $data['heading_title_main'] = $this->language->get('heading_title_shipping_add');
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shipping_add'),
                'href' => $this->url->link($this->module_path . '/shippingTemplateUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL'),
                'separator' => ' :: '
            );
        }

        //links
        if (isset($this->request->get['id_etsy_shipping_templates'])) {
            $data['action'] = $this->url->link($this->module_path . '/shippingTemplateUpdate', 'id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . '&' . $this->session_token_key . '=' . $this->session_token, 'SSL');
        } else {
            $data['action'] = $this->url->link($this->module_path . '/shippingTemplateUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL');
        }

        $data['route'] = $this->url->link($this->module_path . '/shippingTemplateUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');

        $data['heading_title'] = $this->language->get('heading_title');

        // General Settings tab & info
        $data['text_template_title'] = $this->language->get('text_template_title');
        $data['text_origin_country'] = $this->language->get('text_origin_country');
        $data['text_primary_cost'] = $this->language->get('text_primary_cost');
        $data['text_secondary_cost'] = $this->language->get('text_secondary_cost');
        $data['text_min_process_days'] = $this->language->get('text_min_process_days');
        $data['text_max_process_days'] = $this->language->get('text_max_process_days');
        $data['text_currency_info'] = $this->language->get('text_currency_info');
        //Tooltips
        $data['image_size_tooltip'] = $this->language->get('image_size_tooltip');
        $data['general_enable_tooltip'] = $this->language->get('general_enable_tooltip');
        $data['text_etsy_store_tooltip'] = $this->language->get('text_etsy_store_tooltip');
        $data['text_etsy_merchant_id_tooltip'] = $this->language->get('text_etsy_merchant_id_tooltip');
        $data['text_etsy_market_place_id_tooltip'] = $this->language->get('text_etsy_market_place_id_tooltip');
        $data['text_etsy_api_key_tooltip'] = $this->language->get('text_etsy_api_key_tooltip');
        $data['text_etsy_api_secret_tooltip'] = $this->language->get('text_etsy_api_secret_tooltip');
        $data['text_etsy_api_host_type_tooltip'] = $this->language->get('text_etsy_api_host_type_tooltip');
        $data['text_edit_shipping_template_add'] = $this->language->get('text_edit_shipping_template_add');

        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');

        $data['etsy_countries'] = $this->model_kbetsy_kbetsy->getEtsyCountries();

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        if (isset($this->error['etsy_template_title'])) {
            $data['error_etsy_template_title'] = $this->error['etsy_template_title'];
        } else {
            $data['error_etsy_template_title'] = '';
        }
        if (isset($this->error['etsy_primary_cost'])) {
            $data['error_etsy_primary_cost'] = $this->error['etsy_primary_cost'];
        } else {
            $data['error_etsy_primary_cost'] = '';
        }
        if (isset($this->error['etsy_secondary_cost'])) {
            $data['error_etsy_secondary_cost'] = $this->error['etsy_secondary_cost'];
        } else {
            $data['error_etsy_secondary_cost'] = '';
        }
        if (isset($this->error['etsy_min_process_days'])) {
            $data['error_etsy_min_process_days'] = $this->error['etsy_min_process_days'];
        } else {
            $data['error_etsy_min_process_days'] = '';
        }
        if (isset($this->error['etsy_max_process_days'])) {
            $data['error_etsy_max_process_days'] = $this->error['etsy_max_process_days'];
        } else {
            $data['error_etsy_max_process_days'] = '';
        }

        if (isset($this->request->post['etsy']['template']['template_title'])) {
            $data['etsy']['template']['template_title'] = $this->request->post['etsy']['template']['template_title'];
        }
        if (isset($this->request->post['etsy']['template']['primary_cost'])) {
            $data['etsy']['template']['primary_cost'] = $this->request->post['etsy']['template']['primary_cost'];
        }
        if (isset($this->request->post['etsy']['template']['secondary_cost'])) {
            $data['etsy']['template']['secondary_cost'] = $this->request->post['etsy']['template']['secondary_cost'];
        }
        if (isset($this->request->post['etsy']['template']['min_process_days'])) {
            $data['etsy']['template']['min_process_days'] = $this->request->post['etsy']['template']['min_process_days'];
        }
        if (isset($this->request->post['etsy']['template']['max_process_days'])) {
            $data['etsy']['template']['max_process_days'] = $this->request->post['etsy']['template']['max_process_days'];
        }
        if (isset($this->request->post['etsy']['template']['origin_country'])) {
            $data['etsy_select_country'] = $this->request->post['etsy']['template']['origin_country'];
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 3;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/shipping_template_form', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/shipping_template_form.tpl', $data));
        }
    }
    
    public function deleteShippingTemplates()
    {
        $this->load->language($this->module_path);
        if (!empty($this->request->get['id_etsy_shipping_templates'])) {
            if($this->demo_flag == 0) {
                $this->load->model('kbetsy/kbetsy');
                $id_etsy_shipping_templates = $this->request->get['id_etsy_shipping_templates'];
                if ($this->model_kbetsy_kbetsy->checkProfileMapping($id_etsy_shipping_templates)) {
                    $this->model_kbetsy_kbetsy->deleteShippingTemplate($id_etsy_shipping_templates);
                    $this->executeCRON("template/deleteShippingTemplateRequest");
                    $this->session->data['success'] = $this->language->get('error_shipping_delete_success');
                } else {
                    $this->session->data['error'] = $this->language->get('error_shipping_template_already_mapped');
                }
            } else {
                $this->session->data['error'] = $this->language->get('text_demo_mode_action');
            }
            $this->response->redirect($this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        } else {
            $this->response->redirect($this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }
    }

    public function shippingTemplateEntries()
    {
        if (isset($this->request->get['filter_profile_name'])) {
            $filter_profile_name = $this->request->get['filter_profile_name'];
        } else {
            $filter_profile_name = null;
        }
        if (isset($this->request->get['filter_shipping_name'])) {
            $filter_shipping_name = $this->request->get['filter_shipping_name'];
        } else {
            $filter_shipping_name = null;
        }
        if (isset($this->request->get['filter_shipping_country'])) {
            $filter_shipping_country = $this->request->get['filter_shipping_country'];
        } else {
            $filter_shipping_country = null;
        }
        if (isset($this->request->get['filter_destination_country'])) {
            $filter_destination_country = $this->request->get['filter_destination_country'];
        } else {
            $filter_destination_country = null;
        }
        if (isset($this->request->get['filter_destination_region'])) {
            $filter_destination_region = $this->request->get['filter_destination_region'];
        } else {
            $filter_destination_region = null;
        }
        if (isset($this->request->get['filter_destination_region'])) {
            $filter_destination_region = $this->request->get['filter_destination_region'];
        } else {
            $filter_destination_region = null;
        }
        if (isset($this->request->get['filter_destination_region'])) {
            $filter_destination_region = $this->request->get['filter_destination_region'];
        } else {
            $filter_destination_region = null;
        }
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $this->load->language($this->module_path);

        $this->document->setTitle($this->language->get('heading_title_main'));

        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');

        if (isset($this->request->get['id_etsy_shipping_templates'])) {
            $id_etsy_shipping_templates = $this->request->get['id_etsy_shipping_templates'];
            $shippingTemplateData = $this->model_kbetsy_kbetsy->getShippingTemplateById($id_etsy_shipping_templates);
            if ($shippingTemplateData == false) {
                $this->response->redirect($this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        } else {
            /* Redirect to Etsy Shippin Template page if id_etsy_shipping_templates is not avaliable on URL */
            $this->response->redirect($this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }
        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'id_etsy_shipping_templates';
        }
        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array) $this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';
        if (isset($this->request->get['filter_profile_name'])) {
            $url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_shipping_name'])) {
            $url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_shipping_country'])) {
            $url .= '&filter_shipping_country=' . urlencode(html_entity_decode($this->request->get['filter_shipping_country'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_destination_country'])) {
            $url .= '&filter_destination_country=' . urlencode(html_entity_decode($this->request->get['filter_destination_country'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_destination_region'])) {
            $url .= '&filter_destination_region=' . urlencode(html_entity_decode($this->request->get['filter_destination_region'], ENT_QUOTES, 'UTF-8'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_shipping'),
            'href' => $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_shipping_entries'),
            'href' => $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . $url, 'SSL'),
            'separator' => ' :: '
        );

        $filter_data = array(
            'filter_profile_name' => $filter_profile_name,
            'filter_shipping_name' => $filter_shipping_name,
            'filter_shipping_country' => $filter_shipping_country,
            'filter_destination_country' => $filter_destination_country,
            'filter_destination_region' => $filter_destination_region,
            'id_etsy_shipping_templates' => $id_etsy_shipping_templates,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        $data['sort_id_etsy_shipping_templates'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&sort=id_etsy_shipping_templates&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . $url, true);
        $data['sort_shipping_template_title'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_template_title&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . $url, true);
        $data['sort_shipping_origin_country'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_origin_country&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . $url, true);
        $data['sort_shipping_entry_destination_country'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_entry_destination_country&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . $url, true);
        $data['sort_shipping_entry_destination_region'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_entry_destination_region&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . $url, true);
        $data['sort_shipping_entry_primary_cost'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_entry_primary_cost&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . $url, true);
        $data['sort_shipping_entry_secondary_cost'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&sort=shipping_entry_secondary_cost&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . $url, true);

        $shipping_total = $this->model_kbetsy_kbetsy->getTotalShippingTemplateEntries($filter_data);
        $shipping_templates_details = $this->model_kbetsy_kbetsy->getShippingTemplateEntries($filter_data);

        $data['shipping_template_entries'] = array();
        foreach ($shipping_templates_details as $result) {
            $data['shipping_template_entries'][] = array(
                'id_etsy_shipping_templates_entries' => $result['id_etsy_shipping_templates_entries'],
                'shipping_template_title' => $result['shipping_template_title'],
                'shipping_origin_country' => $result['shipping_origin_country'],
                'shipping_template_title' => $result['shipping_template_title'],
                'shipping_entry_destination_country' => $result['shipping_entry_destination_country'],
                'shipping_entry_destination_region' => $result['shipping_entry_destination_region'],
                'shipping_entry_primary_cost' => $result['shipping_entry_primary_cost'],
                'shipping_entry_secondary_cost' => $result['shipping_entry_secondary_cost'],
                'edit' => $this->url->link($this->module_path . '/shippingTemplateEntryUpdate', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates_entries=" . $result['id_etsy_shipping_templates_entries'] . $url, true),
                'delete' => $this->url->link($this->module_path . '/deleteShippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates=" . $result['id_etsy_shipping_templates'] . "&id_etsy_shipping_templates_entries=" . $result['id_etsy_shipping_templates_entries'] . $url, true)
            );
        }
        $pagination = new Pagination();
        $pagination->total = $shipping_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($shipping_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($shipping_total - $this->config->get('config_limit_admin'))) ? $shipping_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $shipping_total, ceil($shipping_total / $this->config->get('config_limit_admin')));
        //links
        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['action'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['route'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['add'] = $this->url->link($this->module_path . '/shippingTemplateEntryUpdate', $this->session_token_key . '=' . $this->session_token . $url . "&id_etsy_shipping_templates=" . $id_etsy_shipping_templates, true);
        $data['delete'] = $this->url->link($this->module_path . '/deleteShippingTemplatesEntries', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['etsy'] = array();

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['heading_title_shipping_entries'] = $this->language->get('heading_title_shipping_entries') . " (" . $shippingTemplateData['shipping_template_title'] . ")";

        // Filter info
        $data['text_filter_profile_name'] = $this->language->get('text_filter_profile_name');
        $data['text_filter_shipping_name'] = $this->language->get('text_filter_shipping_name');
        $data['text_filter_shipping_country'] = $this->language->get('text_filter_shipping_country');
        $data['text_filter_min_proc_days'] = $this->language->get('text_filter_min_proc_days');
        $data['text_filter_max_proc_days'] = $this->language->get('text_filter_max_proc_days');
        $data['text_everywhere_else'] = $this->language->get('text_everywhere_else');
        
        $data['column_template_id'] = $this->language->get('column_template_id');
        $data['column_shipping_title'] = $this->language->get('column_shipping_title');
        $data['column_destination_country'] = $this->language->get('column_destination_country');
        $data['column_destination_region'] = $this->language->get('column_destination_region');
        $data['column_max_processing'] = $this->language->get('column_max_processing');
        $data['column_primary_cost'] = $this->language->get('column_primary_cost');
        $data['column_secondary_cost'] = $this->language->get('column_secondary_cost');
        $data['column_shipping_origin'] = $this->language->get('column_shipping_origin');
        $data['column_action'] = $this->language->get('column_action');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['button_add_template_entry'] = $this->language->get('button_add_template_entry');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_reset'] = $this->language->get('button_reset');

        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['text_confirm_delete_etsy'] = $this->language->get('text_confirm_delete_etsy');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_add_template_entry'] = $this->language->get('shipping_entry_add');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        if (isset($this->error['etsy_api_key'])) {
            $data['error_etsy_api_key'] = $this->error['etsy_api_key'];
        } else {
            $data['error_etsy_api_key'] = '';
        }
        $data['sort'] = $sort;
        $data['order'] = strtolower($order);
        $data['filter_profile_name'] = $filter_profile_name;
        $data['filter_shipping_name'] = $filter_shipping_name;
        $data['filter_shipping_country'] = $filter_shipping_country;
        $data['filter_destination_country'] = $filter_destination_country;
        $data['filter_destination_region'] = $filter_destination_region;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 3;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/shipping_entries', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/shipping_entries.tpl', $data));
        }
    }

    public function shippingTemplateEntryUpdate()
    {
        // load settings for Etsy plugin from database or from default settings
        $this->load->language($this->module_path);

        $this->document->setTitle($this->language->get('heading_title_main'));

        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');

        if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            $shipping_templates_details = $this->model_kbetsy_kbetsy->getShippingTemplateEntries(array('id_etsy_shipping_templates_entries' => $this->request->get['id_etsy_shipping_templates_entries']));
            if (count($shipping_templates_details) > 0) {
                $data['etsy'] = array();
                $data['etsy'] = array(
                    'template' => array(
                        'template_title' => $shipping_templates_details[0]['shipping_template_title'],
                        'primary_cost' => $shipping_templates_details[0]['shipping_entry_primary_cost'],
                        'secondary_cost' => $shipping_templates_details[0]['shipping_entry_secondary_cost'],
                        'shipping_entry_destination_country_id' => $shipping_templates_details[0]['shipping_entry_destination_country_id'],
                        'shipping_entry_destination_region_id' => $shipping_templates_details[0]['shipping_entry_destination_region_id'],
                    )
                );
                $data['destination_type'] = $shipping_templates_details[0]['shipping_entry_destination_country_id'] ? 'country' : 'region';
                $data['etsy_select_country'] = $shipping_templates_details[0]['shipping_origin_country_id'];
                $data['id_etsy_shipping_templates_entries'] = $shipping_templates_details[0]['id_etsy_shipping_templates_entries'];
                $data['id_etsy_shipping_templates'] = $shipping_templates_details[0]['id_etsy_shipping_templates'];
            } else {
                $this->session->data['error'] = $this->language->get('wrong_url_reached');
                $this->response->redirect($this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        } else if (isset($this->request->get['id_etsy_shipping_templates'])) {
            $shipping_templates_details = $this->model_kbetsy_kbetsy->getShippingTemplateDetails(array(), $this->request->get['id_etsy_shipping_templates']);
            if (count($shipping_templates_details) > 0) {
                $data['etsy'] = array();
                $data['etsy'] = array(
                    'template' => array(
                        'template_title' => $shipping_templates_details[0]['shipping_template_title']
                    )
                );
                $data['etsy_select_country'] = $shipping_templates_details[0]['shipping_origin_country_id'];
                $data['id_etsy_shipping_templates'] = $shipping_templates_details[0]['id_etsy_shipping_templates'];
            } else {
                $this->session->data['error'] = $this->language->get('wrong_url_reached');
                $this->response->redirect($this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        } else {
            $this->session->data['error'] = $this->language->get('wrong_url_reached');
            $this->response->redirect($this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            if (isset($this->request->post['id_etsy_shipping_templates_entries'])) {
                $this->model_kbetsy_kbetsy->shippingTemplateEntryUpdate($this->request->post);
                $this->session->data['success'] = $this->language->get('shipping_template_location_updated');
                $this->executeCRON("template/updateShippingTemplateEntryRequest");
            } elseif (isset($this->request->post['id_etsy_shipping_templates'])) {
                $this->session->data['success'] = $this->language->get('shipping_template_location_updated');
                $this->model_kbetsy_kbetsy->shippingTemplateEntryUpdate($this->request->post);
                $this->executeCRON("template/createShippingTemplateEntryRequest");
            }
            if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
                $this->response->redirect($this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&id_etsy_shipping_templates=' . $data['id_etsy_shipping_templates'], 'SSL'));
            } else {
                $this->response->redirect($this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&id_etsy_shipping_templates=' . $this->request->post['id_etsy_shipping_templates'], 'SSL'));
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_shipping'),
            'href' => $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shipping_entry'),
                'href' => $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates=" . $data['id_etsy_shipping_templates'], 'SSL'),
                'separator' => ' :: '
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shipping_entry_edit'),
                'href' => $this->url->link($this->module_path . '/shippingTemplateEntryUpdate', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates_entries=" . $this->request->get['id_etsy_shipping_templates_entries'], 'SSL'),
                'separator' => ' :: '
            );
        } else {

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shipping_entry'),
                'href' => $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates=" . $this->request->get['id_etsy_shipping_templates'], 'SSL'),
                'separator' => ' :: '
            );

            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title_shipping_entry_add'),
                'href' => $this->url->link($this->module_path . '/shippingTemplateEntryUpdate', $this->session_token_key . '=' . $this->session_token, 'SSL'),
                'separator' => ' :: '
            );
        }

        //links
        if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            $data['action'] = $this->url->link($this->module_path . '/shippingTemplateEntryUpdate', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates_entries=" . $this->request->get['id_etsy_shipping_templates_entries'], 'SSL');
        } else if (isset($this->request->get['id_etsy_shipping_templates'])) {
            $data['action'] = $this->url->link($this->module_path . '/shippingTemplateEntryUpdate', 'id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'] . '&' . $this->session_token_key . '=' . $this->session_token, 'SSL');
        }

        if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            $data['cancel'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&id_etsy_shipping_templates=' . $data['id_etsy_shipping_templates'], 'SSL');
        } else {
            $data['cancel'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . '&id_etsy_shipping_templates=' . $this->request->get['id_etsy_shipping_templates'], 'SSL');
        }

        $data['heading_title'] = $this->language->get('heading_title');
        if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            $data['heading_title_main'] = $this->language->get('heading_title_shipping_entry_edit');
        } else {
            $data['heading_title_main'] = $this->language->get('heading_title_shipping_entry_add');
        }

        // General Settings tab & info
        $data['text_template_title'] = $this->language->get('text_template_title');
        $data['text_origin_country'] = $this->language->get('text_origin_country');
        $data['text_primary_cost'] = $this->language->get('text_primary_cost');
        $data['text_secondary_cost'] = $this->language->get('text_secondary_cost');
        $data['text_min_process_days'] = $this->language->get('text_min_process_days');
        $data['text_max_process_days'] = $this->language->get('text_max_process_days');
        $data['text_destination_type'] = $this->language->get('text_destination_type');
        $data['text_country'] = $this->language->get('text_country');
        $data['text_region'] = $this->language->get('text_region');
        $data['entry_destination_country'] = $this->language->get('entry_destination_country');
        $data['entry_destination_region'] = $this->language->get('entry_destination_region');
        $data['entry_select_country'] = $this->language->get('entry_select_country');
        $data['entry_select_region'] = $this->language->get('entry_select_region');
        $data['text_currency_info'] = $this->language->get('text_currency_info');
        $data['text_everywhere_else'] = $this->language->get('text_everywhere_else');
        $data['text_edit_shipping_template_add'] = $this->language->get('text_edit_shipping_template_add');

        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');


        $data['etsy_countries'] = $this->model_kbetsy_kbetsy->getEtsyCountries();
        $data['etsy_regions'] = $this->model_kbetsy_kbetsy->getEtsyRegions();

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        if (isset($this->error['etsy_primary_cost'])) {
            $data['error_etsy_primary_cost'] = $this->error['etsy_primary_cost'];
        } else {
            $data['error_etsy_primary_cost'] = '';
        }
        if (isset($this->error['etsy_secondary_cost'])) {
            $data['error_etsy_secondary_cost'] = $this->error['etsy_secondary_cost'];
        } else {
            $data['error_etsy_secondary_cost'] = '';
        }
        if (isset($this->error['shipping_entry_destination_country_id'])) {
            $data['error_shipping_entry_destination_country_id'] = $this->error['shipping_entry_destination_country_id'];
        } else {
            $data['error_shipping_entry_destination_country_id'] = '';
        }
        if (isset($this->error['shipping_entry_destination_region_id'])) {
            $data['error_shipping_entry_destination_region_id'] = $this->error['shipping_entry_destination_region_id'];
        } else {
            $data['error_shipping_entry_destination_region_id'] = '';
        }

        if (isset($this->request->post['etsy']['template']['primary_cost'])) {
            $data['etsy']['template']['primary_cost'] = $this->request->post['etsy']['template']['primary_cost'];
        } else if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            /* Data is already set on the top of this condition */
        } else {
            $data['etsy']['template']['primary_cost'] = "";
        }

        if (isset($this->request->post['etsy']['template']['secondary_cost'])) {
            $data['etsy']['template']['secondary_cost'] = $this->request->post['etsy']['template']['secondary_cost'];
        } else if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            
        } else {
            $data['etsy']['template']['secondary_cost'] = "";
        }

        if (isset($this->request->post['etsy']['template']['shipping_entry_destination_country_id'])) {
            $data['etsy']['template']['shipping_entry_destination_country_id'] = $this->request->post['etsy']['template']['shipping_entry_destination_country_id'];
        } else if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            
        } else {
            $data['etsy']['template']['shipping_entry_destination_country_id'] = "";
        }

        if (isset($this->request->post['etsy']['template']['shipping_entry_destination_region_id'])) {
            $data['etsy']['template']['shipping_entry_destination_region_id'] = $this->request->post['etsy']['template']['shipping_entry_destination_region_id'];
        } else if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            
        } else {
            $data['etsy']['template']['shipping_entry_destination_region_id'] = "";
        }

        if (isset($this->request->post['etsy']['template']['destination_type'])) {
            $data['destination_type'] = $this->request->post['etsy']['template']['destination_type'];
        } else if (isset($this->request->get['id_etsy_shipping_templates_entries'])) {
            
        } else {
            $data['destination_type'] = 'country';
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 3;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/shipping_template_entry_form', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/shipping_template_entry_form.tpl', $data));
        }
    }

    public function deleteShippingTemplateEntries()
    {
        $this->load->language($this->module_path);
        if (!empty($this->request->get['id_etsy_shipping_templates'])) {
            if($this->demo_flag == 0) {
                $this->load->model('kbetsy/kbetsy');
                $id_etsy_shipping_templates_entries = $this->request->get['id_etsy_shipping_templates_entries'];
                $this->model_kbetsy_kbetsy->deleteShippingTemplateEntries($id_etsy_shipping_templates_entries);
                $this->session->data['success'] = $this->language->get('text_shipping_entry_delete_success');
                $this->executeCRON("template/deleteShippingTemplateEntryRequest");
                if (!empty($this->request->get['id_etsy_shipping_templates'])) {
                    $this->response->redirect($this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token . "&id_etsy_shipping_templates=" . $this->request->get['id_etsy_shipping_templates'], 'SSL'));
                } else {
                    $this->response->redirect($this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token, 'SSL'));
                }
            } else {
                $this->session->data['error'] = $this->language->get('text_demo_mode_action');
                $this->response->redirect($this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        } else {
            $this->response->redirect($this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }
    }

    public function orderListing()
    {
        // load settings for Etsy plugin from database or from default settings
        $this->load->language($this->module_path);

        $this->document->setTitle($this->language->get('heading_title_main'));

        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');

        if (isset($this->request->get['filter_order_id'])) {
            $filter_order_id = $this->request->get['filter_order_id'];
        } else {
            $filter_order_id = null;
        }

        if (isset($this->request->get['filter_customer'])) {
            $filter_customer = $this->request->get['filter_customer'];
        } else {
            $filter_customer = null;
        }

        if (isset($this->request->get['filter_order_status'])) {
            $filter_order_status = $this->request->get['filter_order_status'];
        } else {
            $filter_order_status = null;
        }

        if (isset($this->request->get['filter_total'])) {
            $filter_total = $this->request->get['filter_total'];
        } else {
            $filter_total = null;
        }

        if (isset($this->request->get['filter_date_added'])) {
            $filter_date_added = $this->request->get['filter_date_added'];
        } else {
            $filter_date_added = null;
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $filter_date_modified = $this->request->get['filter_date_modified'];
        } else {
            $filter_date_modified = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'o.date_added';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';
        $sort_url = '';
        $filter_url = '';

        if (isset($this->request->get['filter_order_id'])) {
            $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            $sort_url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            $filter_url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
        }

        if (isset($this->request->get['filter_customer'])) {
            $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
            $sort_url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
            $filter_url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }

        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
            $sort_url .= '&filter_total=' . $this->request->get['filter_total'];
            $filter_url .= '&filter_total=' . $this->request->get['filter_total'];
        }

        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            $sort_url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            $filter_url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }

        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            $sort_url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            $filter_url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
        }
        
        if (isset($this->request->get['page'])) {
            $filter_url .= '&page=' . $this->request->get['page'];
            $sort_url .= '&page=' . $this->request->get['page'];
        }
        
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
            $filter_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
            $filter_url .= '&order=' . $this->request->get['order'];
        }

        if ($order == 'ASC') {
            $sort_url .= '&order=DESC';
        } else {
            $sort_url .= '&order=ASC';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_order_listing'),
            'href' => $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $filter_data = array(
            'filter_order_id' => $filter_order_id,
            'filter_customer' => $filter_customer,
            'filter_order_status' => $filter_order_status,
            'filter_total' => $filter_total,
            'filter_date_added' => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );
        
        $orders_total = $this->model_kbetsy_kbetsy->getTotalOrders($filter_data);
        $results = $this->model_kbetsy_kbetsy->getOrders($filter_data);
        $data['etsy_orders'] = array();
        foreach ($results as $result) {
            $data['etsy_orders'][] = array(
                'order_id' => $result['order_id'],
                'id_etsy_order' => $result['id_etsy_order'],
                'customer' => $result['customer'],
                'total' => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
                'status' => $result['status'],
                'date_added' => $result['date_added'],
                'view' => $this->url->link('sale/order/info', $this->session_token_key . '=' . $this->session_token . '&order_id=' . $result['order_id'] . $url, 'SSL')
            );
        }
        $pagination = new Pagination();
        $pagination->total = $orders_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($orders_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($orders_total - $this->config->get('config_limit_admin'))) ? $orders_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $orders_total, ceil($orders_total / $this->config->get('config_limit_admin')));
        //links
        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['action'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['route'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['add'] = $this->url->link($this->module_path . '/shippingTemplateUpdate', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['delete'] = $this->url->link($this->module_path . '/deleteShippingTemplates', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['token'] = $this->session_token;
        $data['etsy'] = array();

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['text_edit_shipping'] = $this->language->get('text_edit_shipping');

        // Filter info
        $data['text_order_listing'] = $this->language->get('text_order_listing');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_missing'] = $this->language->get('text_missing');

        $data['column_order_id'] = $this->language->get('column_order_id');
        $data['column_customer'] = $this->language->get('column_customer');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_total'] = $this->language->get('column_total');
        $data['column_date_added'] = $this->language->get('column_date_added');
        $data['column_id_etsy_order'] = $this->language->get('column_id_etsy_order');
        $data['column_action'] = $this->language->get('column_action');

        $data['entry_return_id'] = $this->language->get('entry_return_id');
        $data['entry_order_id'] = $this->language->get('entry_order_id');
        $data['entry_customer'] = $this->language->get('entry_customer');
        $data['entry_order_status'] = $this->language->get('entry_order_status');
        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_date_added'] = $this->language->get('entry_date_added');
        $data['entry_date_modified'] = $this->language->get('entry_date_modified');

        $data['button_invoice_print'] = $this->language->get('button_invoice_print');
        $data['button_shipping_print'] = $this->language->get('button_shipping_print');
        $data['button_insert'] = $this->language->get('button_insert');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view'] = $this->language->get('button_view');
        $data['button_reset'] = $this->language->get('button_reset');
        //Tooltips
        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['button_filter'] = $this->language->get('button_filter');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        $data['sort_order'] = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . '&sort=o.order_id' . $sort_url, 'SSL');
        $data['sort_etsy_order'] = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . '&sort=eol.id_etsy_order' . $sort_url, 'SSL');
        $data['sort_customer'] = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . '&sort=customer' . $sort_url, 'SSL');
        $data['sort_status'] = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . '&sort=status' . $sort_url, 'SSL');
        $data['sort_total'] = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . '&sort=o.total' . $sort_url, 'SSL');
        $data['sort_date_added'] = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . '&sort=o.date_added' . $sort_url, 'SSL');
        $data['sort_date_modified'] = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token . '&sort=o.date_modified' . $sort_url, 'SSL');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }
        if (isset($this->error['etsy_api_key'])) {
            $data['error_etsy_api_key'] = $this->error['etsy_api_key'];
        } else {
            $data['error_etsy_api_key'] = '';
        }
        $data['filter_order_id'] = $filter_order_id;
        $data['filter_customer'] = $filter_customer;
        $data['filter_order_status'] = $filter_order_status;
        $data['filter_total'] = $filter_total;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_date_modified'] = $filter_date_modified;

        $this->load->model('localisation/order_status');

        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $data['sort'] = $sort;
        $data['order'] = strtolower($order);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 6;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/order_listing', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/order_listing.tpl', $data));
        }
    }

    public function productListing()
    {
        $this->load->language($this->module_path);

        $this->document->setTitle($this->language->get('heading_title_main'));

        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('kbetsy/profile');

        if (isset($this->request->get['filter_name'])) {
            $filter_name = $this->request->get['filter_name'];
        } else {
            $filter_name = null;
        }

        if (isset($this->request->get['filter_model'])) {
            $filter_model = $this->request->get['filter_model'];
        } else {
            $filter_model = null;
        }

        if (isset($this->request->get['filter_listing_status'])) {
            $filter_listing_status = $this->request->get['filter_listing_status'];
        } else {
            $filter_listing_status = null;
        }

        if (isset($this->request->get['filter_listed_on'])) {
            $filter_listed_on = $this->request->get['filter_listed_on'];
        } else {
            $filter_listed_on = null;
        }

        if (isset($this->request->get['filter_listing_id'])) {
            $filter_listing_id = $this->request->get['filter_listing_id'];
        } else {
            $filter_listing_id = null;
        }

        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = null;
        }
        
        if (isset($this->request->get['filter_id_etsy_profiles'])) {
            $filter_id_etsy_profiles = $this->request->get['filter_id_etsy_profiles'];
        } else {
            $filter_id_etsy_profiles = null;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'id_etsy_products_list';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'ASC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';
        $sort_url = '';
        $filter_url = '';

        if (isset($this->request->get['filter_name'])) {
            $url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_model'])) {
            $url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_listing_status'])) {
            $url .= '&filter_listing_status=' . $this->request->get['filter_listing_status'];
            $sort_url .= '&filter_listing_status=' . $this->request->get['filter_listing_status'];
            $filter_url .= '&filter_listing_status=' . $this->request->get['filter_listing_status'];
        }

        if (isset($this->request->get['filter_quantity'])) {
            $url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            $sort_url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
            $filter_url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
        }

        if (isset($this->request->get['filter_listing_id'])) {
            $url .= '&filter_listing_id=' . $this->request->get['filter_listing_id'];
            $sort_url .= '&filter_listing_id=' . $this->request->get['filter_listing_id'];
            $filter_url .= '&filter_listing_id=' . $this->request->get['filter_listing_id'];
        }

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . $this->request->get['filter_status'];
            $sort_url .= '&filter_status=' . $this->request->get['filter_status'];
            $filter_url .= '&filter_status=' . $this->request->get['filter_status'];
        }
        
        if (isset($this->request->get['filter_id_etsy_profiles'])) {
            $url .= '&filter_id_etsy_profiles=' . $this->request->get['filter_id_etsy_profiles'];
            $sort_url .= '&filter_id_etsy_profiles=' . $this->request->get['filter_id_etsy_profiles'];
            $filter_url .= '&filter_id_etsy_profiles=' . $this->request->get['filter_id_etsy_profiles'];
        }
        
        if (isset($this->request->get['page'])) {
            $filter_url .= '&page=' . $this->request->get['page'];
            $sort_url .= '&page=' . $this->request->get['page'];
        }
        
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
            $filter_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
            $filter_url .= '&order=' . $this->request->get['order'];
        }

        if ($order == 'ASC') {
            $sort_url .= '&order=DESC';
        } else {
            $sort_url .= '&order=ASC';
        }


        if (isset($this->request->get['action_type']) && $this->request->get['id_etsy_products_list']) {
            $this->listingAction($this->request->get['action_type'], $this->request->get['id_etsy_products_list'], $url);
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_product_listing'),
            'href' => $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $filter_data = array(
            'filter_name' => $filter_name,
            'filter_model' => $filter_model,
            'filter_listing_status' => $filter_listing_status,
            'filter_listed_on' => $filter_listed_on,
            'filter_listing_id' => $filter_listing_id,
            'filter_status' => $filter_status,
            'filter_id_etsy_profiles' => $filter_id_etsy_profiles,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $data['etsy_profiles'] = $this->model_kbetsy_profile->getAllProfiles();

        $orders_total = $this->model_kbetsy_kbetsy->getTotalProducts($filter_data);
        $results = $this->model_kbetsy_kbetsy->getProducts($filter_data);

        $data['etsy_products'] = array();

        $this->load->model('tool/image');

        $listing_status_array = array('Listed' => $this->language->get('text_listed'),
            'Pending' => $this->language->get('text_pending'),
            'Updated' => $this->language->get('text_updated'),
            'Inactive' => $this->language->get('text_inactive'),
            'Expired' => $this->language->get('text_expired'),
            'Draft' => $this->language->get('text_draft'),
            'Disabled' => $this->language->get('text_disabled'),
        );

        foreach ($results as $result) {
            if (is_file(DIR_IMAGE . $result['image'])) {
                $image = $this->model_tool_image->resize($result['image'], 40, 40);
            } else {
                $image = $this->model_tool_image->resize('no_image.png', 40, 40);
            }
            $data['etsy_products'][] = array(
                'product_id' => $result['product_id'],
                'listing_id' => $result['listing_id'],
                'image' => $image,
                'name' => $result['name'],
                'model' => $result['model'],
                'profile_title' => $result['profile_title'],
                'quantity' => $result['quantity'],
                'listing_status' => $result['listing_status'],
                'listing_status_text' => $listing_status_array[$result['listing_status']],
                'update_status' => $result['update_flag'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
                'update_flag' => $result['update_flag'],
                'renew_flag' => $result['renew_flag'],
                'delete_flag' => $result['delete_flag'],
                'listed_on' => $result['date_listed'],
                'message' => $result['listing_error'],
                'is_disabled' => $result['is_disabled'],
                'admin_edit_link' => $this->url->link('catalog/product/edit', $this->session_token_key . '=' . $this->session_token . "&product_id=" . $result['product_id'], 'SSL'),
                'catalog_link' => HTTPS_CATALOG ."index.php?route=product/product&product_id=" . $result['product_id'],
                'delete' => $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&action_type=delete&id_etsy_products_list=' . $result['id_etsy_products_list'] . $url, 'SSL'),
                'revise' => $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&action_type=revise&id_etsy_products_list=' . $result['id_etsy_products_list'] . $url, 'SSL'),
                'activate' => $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&action_type=activate&id_etsy_products_list=' . $result['id_etsy_products_list'] . $url, 'SSL'),
                'disable' => $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&action_type=disable&id_etsy_products_list=' . $result['id_etsy_products_list'] . $url, 'SSL'),
                'renew' => $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&action_type=renew&id_etsy_products_list=' . $result['id_etsy_products_list'] . $url, 'SSL'),
                'halt' => $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&action_type=halt&id_etsy_products_list=' . $result['id_etsy_products_list'] . $url, 'SSL')
            );
        }

        $pagination = new Pagination();
        $pagination->total = $orders_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($orders_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($orders_total - $this->config->get('config_limit_admin'))) ? $orders_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $orders_total, ceil($orders_total / $this->config->get('config_limit_admin')));
        
        //links
        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['action'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['route'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['add'] = $this->url->link($this->module_path . '/shippingTemplateUpdate', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['delete'] = $this->url->link($this->module_path . '/deleteShippingTemplates', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['token'] = $this->session_token;

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['text_edit_shipping'] = $this->language->get('text_edit_shipping');

        // Filter info
        $data['text_product_listing'] = $this->language->get('text_product_listing');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_missing'] = $this->language->get('text_missing');

        $data['column_image'] = $this->language->get('column_image');
        $data['column_name'] = $this->language->get('column_name');
        $data['column_product_id'] = $this->language->get('column_product_id');
        $data['column_quantity'] = $this->language->get('column_quantity');
        
        $data['column_model'] = $this->language->get('column_model');
        $data['column_profile'] = $this->language->get('column_profile');
        $data['column_price'] = $this->language->get('column_price');
        $data['column_quantity'] = $this->language->get('column_quantity');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_action'] = $this->language->get('column_action');
        $data['column_listing_status'] = $this->language->get('column_listing_status');
        $data['column_relisting_status'] = $this->language->get('column_relisting_status');
        $data['column_listing_id'] = $this->language->get('column_listing_id');
        $data['column_listed_on'] = $this->language->get('column_listed_on');
        $data['column_variation'] = $this->language->get('column_variation');
        $data['update_flag_text'] = $this->language->get('update_flag_text');
        $data['delete_flag_text'] = $this->language->get('delete_flag_text');
        $data['renew_flag_text'] = $this->language->get('renew_flag_text');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_disabled_hint'] = $this->language->get('text_disabled_hint');
        
        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_model'] = $this->language->get('entry_model');
        $data['entry_price'] = $this->language->get('entry_price');
        $data['entry_quantity'] = $this->language->get('entry_quantity');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_error_message'] = $this->language->get('entry_error_message');
        $data['entry_no_error'] = $this->language->get('entry_no_error');

        $data['button_error'] = $this->language->get('button_error');
        $data['button_renew'] = $this->language->get('button_renew');
        $data['button_revise'] = $this->language->get('button_revise');
        $data['button_halt'] = $this->language->get('button_halt');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['button_view'] = $this->language->get('button_view');
        $data['button_relist'] = $this->language->get('button_relist');
        $data['button_reactivate'] = $this->language->get('button_reactivate');
        $data['button_halt_activation'] = $this->language->get('button_halt_activation');
        $data['button_halt_deletion'] = $this->language->get('button_halt_deletion');
        
        
        $data['button_reset'] = $this->language->get('button_reset');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['button_filter'] = $this->language->get('button_filter');
        $data['sync_product_to_etsy'] = $this->language->get('sync_product_to_etsy');
        

        $this->load->model('setting/setting');
        $secure_key = $this->model_setting_setting->getSetting('kbetsy_secure_key');
        $data['secure_key'] = $secure_key['kbetsy_secure_key'];

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        $data['sort_id'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=p.product_id' . $sort_url, 'SSL');
        $data['sort_name'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=pd.name' . $sort_url, 'SSL');
        $data['sort_model'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=p.model' . $sort_url, 'SSL');
        $data['sort_quantity'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=p.quantity' . $sort_url, 'SSL');
        $data['sort_profile'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=ep.id_etsy_profiles' . $sort_url, 'SSL');
        $data['sort_listing_status'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=epl.listing_status' . $sort_url, 'SSL');
        $data['sort_listing_id'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=epl.listing_id' . $sort_url, 'SSL');
        $data['sort_update_status'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=epl.update_flag' . $sort_url, 'SSL');
        $data['sort_listed_on'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . '&sort=epl.date_listed' . $sort_url, 'SSL');

        $data['filter_name'] = $filter_name;
        $data['filter_model'] = $filter_model;
        $data['filter_listing_status'] = $filter_listing_status;
        $data['filter_listed_on'] = $filter_listed_on;
        $data['filter_status'] = $filter_status;
        $data['filter_listing_id'] = $filter_listing_id;
        $data['filter_id_etsy_profiles'] = $filter_id_etsy_profiles;
        $data['listing_statuses'] = array(
            'Pending' => $this->language->get('text_pending'),
            'Listed' => $this->language->get('text_listed'),
            'Disabled' => $this->language->get('text_disabled'),
            'Inactive' => $this->language->get('text_inactive'),
            'Expired' => $this->language->get('text_expired'),
            'Sold Out' => $this->language->get('text_soldout'),
            'Draft' => $this->language->get('text_draft')
        );
        $data['sort'] = $sort;
        $data['order'] = strtolower($order);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 4;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/product_listing', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/product_listing.tpl', $data));
        }
    }

    public function auditLog()
    {
        $this->load->language($this->module_path);

        $this->document->setTitle($this->language->get('heading_title_main'));

        $this->load->model('setting/setting');
        $this->load->model('kbetsy/kbetsy');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        if (isset($this->request->get['filter_id_etsy_audit_log'])) {
            $filter_id_etsy_audit_log = $this->request->get['filter_id_etsy_audit_log'];
        } else {
            $filter_id_etsy_audit_log = null;
        }
        if (isset($this->request->get['filter_log_entry'])) {
            $filter_log_entry = $this->request->get['filter_log_entry'];
        } else {
            $filter_log_entry = null;
        }
        if (isset($this->request->get['filter_log_class_method'])) {
            $filter_log_class_method = $this->request->get['filter_log_class_method'];
        } else {
            $filter_log_class_method = null;
        }
        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'id_etsy_audit_log';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        $url = '';
        $sort_url = '';
        $filter_url = '';

        if (isset($this->request->get['filter_id_etsy_audit_log'])) {
            $url .= '&filter_id_etsy_audit_log=' . urlencode(html_entity_decode($this->request->get['filter_id_etsy_audit_log'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_id_etsy_audit_log=' . urlencode(html_entity_decode($this->request->get['filter_id_etsy_audit_log'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_id_etsy_audit_log=' . urlencode(html_entity_decode($this->request->get['filter_id_etsy_audit_log'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_log_entry'])) {
            $url .= '&filter_log_entry=' . urlencode(html_entity_decode($this->request->get['filter_log_entry'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_log_entry=' . urlencode(html_entity_decode($this->request->get['filter_log_entry'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_log_entry=' . urlencode(html_entity_decode($this->request->get['filter_log_entry'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_log_class_method'])) {
            $url .= '&filter_log_class_method=' . urlencode(html_entity_decode($this->request->get['filter_log_class_method'], ENT_QUOTES, 'UTF-8'));
            $sort_url .= '&filter_log_class_method=' . urlencode(html_entity_decode($this->request->get['filter_log_class_method'], ENT_QUOTES, 'UTF-8'));
            $filter_url .= '&filter_log_class_method=' . urlencode(html_entity_decode($this->request->get['filter_log_class_method'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['page'])) {
            $filter_url .= '&page=' . $this->request->get['page'];
            $sort_url .= '&page=' . $this->request->get['page'];
        }
        
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
            $filter_url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
            $filter_url .= '&order=' . $this->request->get['order'];
        }

        if ($order == 'ASC') {
            $sort_url .= '&order=DESC';
        } else {
            $sort_url .= '&order=ASC';
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_audit'),
            'href' => $this->url->link($this->module_path . '/auditLog', $this->session_token_key . '=' . $this->session_token . $filter_url, 'SSL'),
            'separator' => ' :: '
        );

        $filter_data = array(
            'filter_id_etsy_audit_log' => $filter_id_etsy_audit_log,
            'filter_log_entry' => $filter_log_entry,
            'filter_log_class_method' => $filter_log_class_method,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $data['sort_id_etsy_audit_log'] = $this->url->link($this->module_path . '/auditLog', $this->session_token_key . '=' . $this->session_token . '&sort=id_etsy_audit_log' . $sort_url, true);
        $data['sort_log_entry'] = $this->url->link($this->module_path . '/auditLog', $this->session_token_key . '=' . $this->session_token . '&sort=log_entry' . $sort_url, true);
        $data['sort_log_user'] = $this->url->link($this->module_path . '/auditLog', $this->session_token_key . '=' . $this->session_token . '&sort=log_user' . $sort_url, true);
        $data['sort_log_class_method'] = $this->url->link($this->module_path . '/auditLog', $this->session_token_key . '=' . $this->session_token . '&sort=log_class_method' . $sort_url, true);
        $data['sort_log_time'] = $this->url->link($this->module_path . '/auditLog', $this->session_token_key . '=' . $this->session_token . '&sort=log_time' . $sort_url, true);

        $audit_total = $this->model_kbetsy_kbetsy->getAuditLogTotal($filter_data);
        $audit_details = $this->model_kbetsy_kbetsy->getAuditLog($filter_data);

        $data['audit_log'] = array();
        foreach ($audit_details as $result) {
            $data['audit_log'][] = array(
                'id_etsy_audit_log' => $result['id_etsy_audit_log'],
                'log_entry' => $result['log_entry'],
                'log_user' => $result['log_user'],
                'log_class_method' => $result['log_class_method'],
                'log_time' => $result['log_time'],
            );
        }
        $pagination = new Pagination();
        $pagination->total = $audit_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link($this->module_path . '/auditLog', $this->session_token_key . '=' . $this->session_token . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();
        $data['results'] = sprintf($this->language->get('text_pagination'), ($audit_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($audit_total - $this->config->get('config_limit_admin'))) ? $audit_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $audit_total, ceil($audit_total / $this->config->get('config_limit_admin')));
        //links
        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['action'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['route'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['add'] = $this->url->link($this->module_path . '/shippingTemplateUpdate', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['delete'] = $this->url->link($this->module_path . '/deleteShippingTemplates', $this->session_token_key . '=' . $this->session_token . $url, true);
        $data['token'] = $this->session_token;
        $data['etsy'] = array();

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['text_edit_audit'] = $this->language->get('text_edit_audit');

        // Filter info
        $data['text_filter_log_entry'] = $this->language->get('text_filter_log_entry');
        $data['text_filter_log_class_method'] = $this->language->get('text_filter_log_class_method');
        $data['text_filter_min_proc_days'] = $this->language->get('text_filter_min_proc_days');
        $data['text_filter_max_proc_days'] = $this->language->get('text_filter_max_proc_days');
        $data['column_log_id'] = $this->language->get('column_log_id');
        $data['column_log_description'] = $this->language->get('column_log_description');
        $data['column_action_user'] = $this->language->get('column_action_user');
        $data['column_action_called'] = $this->language->get('column_action_called');
        $data['column_time_action'] = $this->language->get('column_time_action');
        $data['column_action'] = $this->language->get('column_action');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['button_add'] = $this->language->get('button_add');
        $data['button_reset'] = $this->language->get('button_reset');

        //Tooltips
        //buttons
        $data['button_save'] = $this->language->get('button_save');
        $data['button_save_and_stay'] = $this->language->get('button_save_and_stay');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_add_module'] = $this->language->get('button_add_module');
        $data['button_remove'] = $this->language->get('button_remove');
        $data['button_delete'] = $this->language->get('button_delete');
        $data['text_confirm'] = $this->language->get('text_confirm');
        $data['button_filter'] = $this->language->get('button_filter');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        $data['sort'] = $sort;
        $data['order'] = strtolower($order);
        $data['filter_log_entry'] = $filter_log_entry;
        $data['filter_log_class_method'] = $filter_log_class_method;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 8;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/audit', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/audit.tpl', $data));
        }
    }

    private function validate()
    {
        $this->load->model('kbetsy/profile');

        $this->error = array();
        if (!$this->user->hasPermission('modify', $this->module_path)) {
            $this->error['error'] = $this->language->get('error_permission');
        }

        if (isset($this->request->post['etsy']['general'])) {
            if (!$this->request->post['etsy']['general']['api_key']) {
                $this->error['etsy_api_key'] = $this->language->get('error_etsy_api_key');
            }
            if (!$this->request->post['etsy']['general']['api_secret']) {
                $this->error['etsy_api_secret'] = $this->language->get('error_etsy_api_secret');
            }
            if (!$this->request->post['etsy']['general']['api_host']) {
                $this->error['etsy_api_host'] = $this->language->get('error_etsy_api_host');
            }
        }

        if (isset($this->request->post['etsy']['template']) && isset($this->request->post['shipping_templates'])) {
            if (isset($this->request->post['etsy']['template']['template_title']) && empty($this->request->post['etsy']['template']['template_title'])) {
                $this->error['etsy_template_title'] = $this->language->get('error_etsy_template_title');
            } else if (isset($this->request->post['etsy']['template']['template_title']) && !isset($this->request->post['id_etsy_shipping_templates'])) {
                if ($this->model_kbetsy_kbetsy->checkTemplateExists(trim($this->request->post['etsy']['template']['template_title']))) {
                    $this->error['etsy_template_title'] = $this->language->get('error_etsy_template_title_duplicate');
                }
            }
            if (($this->request->post['etsy']['template']['primary_cost'] == '') || !$this->validatePriceFormat($this->request->post['etsy']['template']['primary_cost'])) {
                $this->error['etsy_primary_cost'] = $this->language->get('error_etsy_primary_cost');
            }

            if (($this->request->post['etsy']['template']['secondary_cost'] == '') || !$this->validatePriceFormat($this->request->post['etsy']['template']['secondary_cost'])) {
                $this->error['etsy_secondary_cost'] = $this->language->get('error_etsy_secondary_cost');
            } else if ($this->request->post['etsy']['template']['secondary_cost'] > $this->request->post['etsy']['template']['primary_cost']) {
                $this->error['etsy_secondary_cost'] = $this->language->get('error_etsy_secondary_cost_more');
            }

            if (empty($this->request->post['etsy']['template']['min_process_days']) || !$this->checkPositiveNumber($this->request->post['etsy']['template']['min_process_days'])) {
                $this->error['etsy_min_process_days'] = $this->language->get('error_etsy_min_process_days');
            } else if (empty($this->request->post['etsy']['template']['max_process_days']) || !$this->checkPositiveNumber($this->request->post['etsy']['template']['max_process_days'])) {
                $this->error['etsy_max_process_days'] = $this->language->get('error_etsy_max_process_days');
            } elseif ($this->request->post['etsy']['template']['max_process_days'] < $this->request->post['etsy']['template']['min_process_days']) {
                $this->error['etsy_max_process_days'] = $this->language->get('max_processing_day_error');
            }
        } else if (isset($this->request->post['etsy']['template']) && isset($this->request->post['shipping_templates_entries'])) {
            if (($this->request->post['etsy']['template']['primary_cost'] == '') || !$this->validatePriceFormat($this->request->post['etsy']['template']['primary_cost'])) {
                $this->error['etsy_primary_cost'] = $this->language->get('error_etsy_primary_cost');
            }
            if (($this->request->post['etsy']['template']['secondary_cost'] == '') || !$this->validatePriceFormat($this->request->post['etsy']['template']['secondary_cost'])) {
                $this->error['etsy_secondary_cost'] = $this->language->get('error_etsy_secondary_cost');
            } else if ($this->request->post['etsy']['template']['secondary_cost'] > $this->request->post['etsy']['template']['primary_cost']) {
                $this->error['etsy_secondary_cost'] = $this->language->get('error_etsy_secondary_cost_more');
            }

            if ($this->request->post['etsy']['template']['destination_type'] == 'country') {
                if ($this->request->post['etsy']['template']['shipping_entry_destination_country_id'] == "") {
                    $this->error['shipping_entry_destination_country_id'] = $this->language->get('error_shipping_entry_destination_country_id');
                } else {
                    if (isset($this->request->post['id_etsy_shipping_templates_entries'])) {
                        $status = $this->model_kbetsy_kbetsy->checkDestinationCountryExists($this->request->post['etsy']['template']['shipping_entry_destination_country_id'], $this->request->post['id_etsy_shipping_templates'], $this->request->post['id_etsy_shipping_templates_entries']);
                    } else {
                        $status = $this->model_kbetsy_kbetsy->checkDestinationCountryExists($this->request->post['etsy']['template']['shipping_entry_destination_country_id'], $this->request->post['id_etsy_shipping_templates']);
                    }
                    if ($status) {
                        $this->error['shipping_entry_destination_country_id'] = $this->language->get('error_shipping_entry_destination_already_mapped');
                    }
                }
            } else {
                if (empty($this->request->post['etsy']['template']['shipping_entry_destination_region_id'])) {
                    $this->error['shipping_entry_destination_region_id'] = $this->language->get('error_shipping_entry_destination_region_id');
                } else {
                    if (isset($this->request->post['id_etsy_shipping_templates_entries'])) {
                        $status = $this->model_kbetsy_kbetsy->checkDestinationRegionExists($this->request->post['etsy']['template']['shipping_entry_destination_region_id'], $this->request->post['id_etsy_shipping_templates'], $this->request->post['id_etsy_shipping_templates_entries']);
                    } else {
                        $status = $this->model_kbetsy_kbetsy->checkDestinationRegionExists($this->request->post['etsy']['template']['shipping_entry_destination_region_id'], $this->request->post['id_etsy_shipping_templates']);
                    }
                    if ($status) {
                        $this->error['shipping_entry_destination_region_id'] = $this->language->get('error_shipping_entry_destination_already_mapped');
                    }
                }
            }
        }
        
        if (isset($this->request->post['etsy']['shop_section'])) {
            $this->load->model('kbetsy/shop_section');
            
            /* Shop Section Blank Title Validation */
            if (!$this->request->post['etsy']['shop_section']['title']) {
                $this->error['shop_section_title'] = $this->language->get('error_shop_section_title_blank');
            } else {
                /* Shop Section Blank Title Dulicacy */
                if (isset($this->request->post['shop_section_id'])) {
                    $status = $this->model_kbetsy_shop_section->checkSectionExist($this->request->post['etsy']['shop_section']['title'], $this->request->post['shop_section']);
                } else {
                    $status = $this->model_kbetsy_shop_section->checkSectionExist($this->request->post['etsy']['shop_section']['title']);
                }
                if($status) {
                    $this->error['shop_section_title'] = $this->language->get('error_shop_section_title_duplicate');
                }
            }
        }

        if (isset($this->request->post['etsy']['profile'])) {
            if (!$this->request->post['etsy']['profile']['profile_title']) {
                $this->error['etsy_profile_title'] = $this->language->get('error_etsy_profile_title');
            }
            if (!$this->request->post['etsy']['profile']['id_etsy_category_final']) {
                $this->error['etsy_category'] = $this->language->get('error_etsy_category');
            }
            if (!isset($this->request->post['product_category'])) {
                $this->error['etsy_store_category'] = $this->language->get('error_store_category');
            } else {
                if (isset($this->request->post['id_etsy_profiles'])) {
                    $status = $this->model_kbetsy_profile->checkProfileCategory($this->request->post['product_category'], $this->request->post['id_etsy_profiles']);
                } else {
                    $status = $this->model_kbetsy_profile->checkProfileCategory($this->request->post['product_category']);
                }
                if (!empty($status)) {
                    $this->error['etsy_store_category'] = $this->language->get('error_category_already_mapped') . $status[0]['profile_title'];
                }
            }

            if (!$this->request->post['etsy']['profile']['etsy_templates']) {
                $this->error['etsy_templates'] = $this->language->get('error_etsy_templates');
            }
            
            if ($this->request->post['etsy']['profile']['product_price'] != "" && !ctype_digit($this->request->post['etsy']['profile']['product_price'])) {
                $this->error['etsy_product_price'] = $this->language->get('error_etsy_product_price_int');
            } else if ($this->request->post['etsy']['profile']['product_price'] != "" && $this->request->post['etsy']['profile']['product_price'] < 0) {
                $this->error['etsy_product_price'] = $this->language->get('error_etsy_product_price_negative');
            }

            if (!$this->request->post['etsy']['profile']['who_made']) {
                $this->error['etsy_who_made'] = $this->language->get('error_who_made');
            }
            if (!$this->request->post['etsy']['profile']['when_made']) {
                $this->error['etsy_when_made'] = $this->language->get('error_when_made');
            }
        }
        return !$this->error;
    }

    private function validateGeneralSettings()
    {
        if (!$this->user->hasPermission('modify', $this->module_path)) {
            $this->error['error'] = $this->language->get('error_permission');
        }
        if (isset($this->request->post['etsy']['general'])) {
            if (empty($this->request->post['etsy']['general']['etsy_api_key'])) {
                $this->error['etsy_api_key'] = $this->language->get('error_etsy_api_key');
            }
            if (empty($this->request->post['etsy']['general']['etsy_api_secret'])) {
                $this->error['etsy_api_secret'] = $this->language->get('error_etsy_api_secret');
            }

            if (empty($this->request->post['etsy']['general']['currency'])) {
                $this->error['etsy_currency'] = $this->language->get('error_currency');
            }
        }
        return !$this->error;
    }

    public function install()
    {
        if (VERSION >= '2.0.0.0' && VERSION < '2.0.1.0') {
            $this->load->model('tool/event');
            $this->model_tool_event->addEvent('kbetsy', 'post.order.history.add', 'ebay_feed/cron/on_order_history_add');
            $this->model_tool_event->addEvent('kbetsy', 'post.admin.product.edit', 'module/kbebay/onProductUpdate');
        } elseif (VERSION >= '2.0.1.0' && VERSION <= '2.1.0.2') {
            $this->load->model('extension/event');
            $this->model_extension_event->addEvent('kbetsy', 'post.order.history.add', 'kbetsy/cron/on_order_history_add');
            $this->model_extension_event->addEvent('kbetsy', 'post.admin.product.edit', 'module/kbetsy/onProductUpdate');
        } elseif (VERSION >= '2.2.0.0' && VERSION < '3.0.0.0') {
            $this->load->model('extension/event');
            $this->model_extension_event->addEvent('kbetsy', 'catalog/model/checkout/order/addOrderHistory/after', 'kbetsy/cron/on_order_history_add');
            $this->model_extension_event->addEvent('kbetsy', 'admin/model/catalog/product/editProduct/after', 'extension/module/kbetsy/onProductUpdate');
        } elseif (VERSION >= '3.0.0.0') {
            $this->load->model('setting/event');
            $this->model_setting_event->addEvent('kbetsy', 'catalog/model/checkout/order/addOrderHistory/after', 'kbetsy/cron/on_order_history_add');
            $this->model_setting_event->addEvent('kbetsy', 'admin/model/catalog/product/editProduct/after', 'extension/module/kbetsy/onProductUpdate');
        }
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('setting/setting');

        $demo_flag['kbetsy_demo_flag'] = $this->demo_flag;
        $this->model_setting_setting->editSetting('kbetsy_demo_flag', $demo_flag);

        $this->model_kbetsy_kbetsy->install();
    }

    public function uninstall()
    {
        if (VERSION >= '2.0.0.0' && VERSION < '2.0.1.0') {
            $this->load->model('tool/event');
            $this->model_tool_event->deleteEvent('kbetsy');
        } elseif (VERSION >= '2.0.1.0' && VERSION < '3.0.0.0') {
            $this->load->model('extension/event');
            $this->model_extension_event->deleteEvent('kbetsy');
        } elseif (VERSION >= '3.0.0.0') {
            $this->load->model('setting/event');
            $this->model_setting_event->deleteEvent('kbetsy');
        }
    }

    private function listingAction($action, $id_etsy_products_list, $url)
    {
        if (!empty($id_etsy_products_list) && !empty($action)) {

            //Get Product Name
            $getProductListingDetails = $this->db->query("SELECT pl.name FROM " . DB_PREFIX . "etsy_products_list epl, " . DB_PREFIX . "product_description pl WHERE epl.id_etsy_products_list = '" . (int) $id_etsy_products_list . "' AND epl.id_product = pl.product_id AND pl.language_id = '" . (int) $this->config->get('config_language_id') . "'");

            if ($action == 'renew') {
                $checkDeleteFlag = $this->db->query("SELECT count(*) as count FROM " . DB_PREFIX . "etsy_products_list WHERE id_etsy_products_list = '" . (int) $id_etsy_products_list . "' AND (delete_flag = '1' OR delete_flag = '2')");
                if ($checkDeleteFlag->row['count'] == 0) {
                    $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET renew_flag = '1', delete_flag = '0', update_flag = '0', error_flag = '0' WHERE id_etsy_products_list = '" . (int) $id_etsy_products_list . "'");
                    $this->session->data['success'] = sprintf($this->language->get('text_renewal_product_success'), $getProductListingDetails->rows[0]['name']);
                } else {
                    $this->session->data['error'] = sprintf($this->language->get('text_renewal_product_failure'), $getProductListingDetails->rows[0]['name']);
                }
            } else if ($action == 'activate') {
                $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET is_disabled = '0', error_flag = '0' WHERE id_etsy_products_list = '" . (int) $id_etsy_products_list . "'");
                $this->session->data['success'] = sprintf($this->language->get('text_product_enable_success'), $getProductListingDetails->rows[0]['name']);
            } else if ($action == 'disable') {
                $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET is_disabled = '1' WHERE id_etsy_products_list = '" . (int) $id_etsy_products_list . "'");
                $this->session->data['success'] = sprintf($this->language->get('text_product_disabled_success'), $getProductListingDetails->rows[0]['name']);
            } else if ($action == 'halt') {
                $this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET renew_flag = '0', delete_flag = '0', update_flag = '0' WHERE id_etsy_products_list = '" . (int) $id_etsy_products_list . "'");
                $this->session->data['success'] = sprintf($this->language->get('text_halt_renewal_product_success'), $getProductListingDetails->rows[0]['name']);
            } else if ($action == 'delete') {
                $deleteProductListingSQL = "UPDATE " . DB_PREFIX . "etsy_products_list SET delete_flag = '1', renew_flag = '0', update_flag = '0' WHERE id_etsy_products_list = '" . (int) $id_etsy_products_list . "'";
                if ($this->db->query($deleteProductListingSQL)) {
                    $this->session->data['success'] = sprintf($this->language->get('text_delete_success'), $getProductListingDetails->rows[0]['name']);
                } else {
                    $this->session->data['error'] = sprintf($this->language->get('text_delete_failed'), $getProductListingDetails->rows[0]['name']);
                }
            } else if ($action == 'revise') {
                if ($this->db->query("UPDATE " . DB_PREFIX . "etsy_products_list SET delete_flag = '0', renew_flag = '0', update_flag = '1', error_flag = '0' WHERE id_etsy_products_list = '" . (int) $id_etsy_products_list . "'")) {
                    $this->session->data['success'] = sprintf($this->language->get('text_renewal_resume'), $getProductListingDetails->rows[0]['name']);
                }
            }
        }
        $this->response->redirect($this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
    }

    public function synchronization()
    {
        $this->load->language($this->module_path);
        $this->document->setTitle($this->language->get('heading_title_main'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_synchronization'),
            'href' => $this->url->link($this->module_path . '/synchronization', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        $data['base_url'] = HTTPS_CATALOG . 'index.php?route=etsy/cron/jobs&action=';
        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_synchronization'] = $this->language->get('heading_title_synchronization');
        $data['text_country_syncronization'] = $this->language->get('text_country_syncronization');
        $data['text_shipping_template_syncronization'] = $this->language->get('text_shipping_template_syncronization');
        $data['text_product_syncronization'] = $this->language->get('text_product_syncronization');
        $data['text_product_local_syncronization'] = $this->language->get('text_product_local_syncronization');
        $data['text_update_product_syncronization'] = $this->language->get('text_update_product_syncronization');
        $data['text_status_sync_syncronization'] = $this->language->get('text_status_sync_syncronization');
        
        
        $data['text_variation_syncronization'] = $this->language->get('text_variation_syncronization');
        $data['text_order_syncronization'] = $this->language->get('text_order_syncronization');
        $data['text_status_syncronization'] = $this->language->get('text_status_syncronization');
        $data['text_language_syncronization'] = $this->language->get('text_language_syncronization');
        $data['text_sync_now'] = $this->language->get('text_sync_now');
        $data['text_cron_config'] = $this->language->get('text_cron_config');
        $data['text_cron_config_help'] = $this->language->get('text_cron_config_help');
        $data['text_cron_via_cp'] = $this->language->get('text_cron_via_cp');
        $data['text_cron_via_ssh'] = $this->language->get('text_cron_via_ssh');
        $data['cron_frequency'] = $this->language->get('cron_frequency');
        $data['cron_two_frequency'] = $this->language->get('cron_two_frequency');
        $data['cron_day_frequency'] = $this->language->get('cron_day_frequency');
        
        $data['local_sync_cron_hint'] = $this->language->get('local_sync_cron_hint');
        $data['product_sync_cron_hint'] = $this->language->get('product_sync_cron_hint');
        $data['product_update_sync_cron_hint'] = $this->language->get('product_update_sync_cron_hint');
        $data['product_status_sync_cron_hint'] = $this->language->get('product_status_sync_cron_hint');
        $data['order_sync_cron_hint'] = $this->language->get('order_sync_cron_hint');
        $data['order_status_sync_cron_hint'] = $this->language->get('order_status_sync_cron_hint');
        
        
        $this->load->model('setting/setting');
         $secure_key = $this->model_setting_setting->getSetting('kbetsy_secure_key');
         $data['secure_key'] = $secure_key['kbetsy_secure_key'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 7;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/synchronization', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/synchronization.tpl', $data));
        }
    }

    public function deleteProfile()
    {
        $this->load->language($this->module_path);
        if($this->demo_flag == 0) {
            if (!empty($this->request->get['id_etsy_profiles'])) {
                $this->load->model('kbetsy/kbetsy');
                $this->load->model('kbetsy/profile');
                $id_etsy_profiles = $this->request->get['id_etsy_profiles'];
                if ($this->model_kbetsy_profile->deleteProfile($id_etsy_profiles)) {
                    $this->session->data['success'] = $this->language->get('text_profile_delete_success');
                } else {
                    $this->session->data['error'] = $this->language->get('text_profile_delete_failure');
                }
                $this->response->redirect($this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            } else {
                $this->response->redirect($this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        } else {
            $this->session->data['error'] = $this->language->get('text_demo_mode_action');
            $this->response->redirect($this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        }
    }

    private function checkPositiveNumber($value)
    {
        $filter_options = array(
            'options' => array('min_range' => 0)
        );
        if (filter_var($value, FILTER_VALIDATE_INT, $filter_options) !== FALSE && $value >= 0) {
            return true;
        }
    }

    private function validatePriceFormat($value)
    {
        $filter_options = array(
            'options' => array('min_range' => 0)
        );
        if (filter_var($value, FILTER_VALIDATE_FLOAT, $filter_options) !== FALSE && $value >= 0) {
            return true;
        }
    }

    public function deleteProfiles()
    {
        $this->load->model('kbetsy/kbetsy');
        $this->load->language($this->module_path);

        if (!empty($this->request->post['selected'])) {
            if (isset($this->request->post['selected']) && $this->validateDelete()) {
                foreach ($this->request->post['selected'] as $id_etsy_profiles) {
                    if ($this->model_kbetsy_kbetsy->deleteProfile($id_etsy_profiles)) {
                        $this->session->data['success'] = $this->language->get('text_profile_delete_success');
                    } else {
                        $this->session->data['error'] = $this->language->get('text_profile_delete_failure');
                    }
                }
            }
        } else {
            $this->session->data['error'] = $this->language->get('non_selected_error');
        }

        $url = '';
        if (isset($this->request->get['filter_profile_name'])) {
            $url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_shipping_name'])) {
            $url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $this->response->redirect($this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
    }

    public function enableProfile()
    {
        $this->load->model('kbetsy/kbetsy');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $id_etsy_profiles) {
                if ($this->model_kbetsy_kbetsy->enableProfile($id_etsy_profiles)) {
                    $this->session->data['success'] = "Profile enabled successfully.";
                } else {
                    $this->session->data['error'] = "Profile enable failed.";
                }
            }
        }
        $url = '';
        if (isset($this->request->get['filter_profile_name'])) {
            $url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_shipping_name'])) {
            $url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $this->response->redirect($this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
    }

    public function disableProfile()
    {
        $this->load->model('kbetsy/kbetsy');

        if (isset($this->request->post['selected']) && $this->validateDelete()) {
            foreach ($this->request->post['selected'] as $id_etsy_profiles) {
                if ($this->model_kbetsy_kbetsy->disableProfile($id_etsy_profiles)) {
                    $this->session->data['success'] = "Profile disabled successfully.";
                } else {
                    $this->session->data['error'] = "Profile disable failed.";
                }
            }
        }
        $url = '';
        if (isset($this->request->get['filter_profile_name'])) {
            $url .= '&filter_profile_name=' . urlencode(html_entity_decode($this->request->get['filter_profile_name'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_shipping_name'])) {
            $url .= '&filter_shipping_name=' . urlencode(html_entity_decode($this->request->get['filter_shipping_name'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }
        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }
        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $this->response->redirect($this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token . $url, 'SSL'));
    }

    protected function validateDelete()
    {
        if (!$this->user->hasPermission('modify', 'module/etsy')) {
            $this->error['error'] = $this->language->get('error_permission');
        }
        return !$this->error;
    }

    public function onProductUpdate($path, $data = array())
    {
        $product_id = 0;
        if($path == "catalog/product/editProduct") {
            $product_id = $data[0];
        } else {
            $product_id = $path;
        }
        if (!empty($product_id)) {
            $updateSQL = "UPDATE " . DB_PREFIX . "etsy_products_list SET update_flag = '1' WHERE id_product = '" . (int) $product_id . "' AND listing_status = 'Listed'";
            $this->db->query($updateSQL);
        }
    }

    /* Show oAuth Extension Error. Called from General Settings */
    public function showError()
    {
        $this->load->language($this->module_path);
        $data['error'] = $this->language->get('oAuth_error');

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_general'),
            'href' => $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        //links
        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['action'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['route'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['cancel'] = $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['text_edit_general'] = $this->language->get('text_edit_general');
        $data['token'] = $this->session_token;

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['button_cancel'] = $this->language->get('button_cancel');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 1;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/error', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/error.tpl', $data));
        }
    }

    public function attributeMapping()
    {
        $this->load->language($this->module_path);
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('kbetsy/attribute');
        $this->document->setTitle($this->language->get('heading_title_main'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('attribute_mapping_heading_title'),
            'href' => $this->url->link($this->module_path . '/attributeMapping', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['text_attribute_mapping'] = $this->language->get('text_attribute_mapping');
        $data['mapping_not_required'] = $this->language->get('mapping_not_required');
        $data['option_name'] = $this->language->get('option_name');
        $data['etsy_option'] = $this->language->get('etsy_option');
        $data['column_action'] = $this->language->get('column_action');
        $data['text_no_results'] = $this->language->get('text_no_results');
        $data['select_etsy_option'] = $this->language->get('select_etsy_option');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_add_custom_attribute'] = $this->language->get('button_add_custom_attribute');

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }
        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        } else {
            $data['error'] = '';
        }

        $options = $this->model_kbetsy_attribute->getEtsyOCOptionsMapping();
        if ($options == false) {
            $data['options'] = false;
        } else {
            $optionId = array();
            foreach ($options as $value) {
                $optionId[] = $value['option_id'];
            }
            $data['options'] = true;
            $data['optionMappings'] = $this->model_kbetsy_attribute->getOptionsName($optionId);
        }

        $data['etsy_options'][] = array("id" => "", "name" => $this->language->get('select_etsy_option'));
        $etsy_options = $this->model_kbetsy_attribute->getEtsyOptions();
        if (!empty($etsy_options)) {
            foreach ($etsy_options as $etsy_option) {
                $data['etsy_options'][] = array("id" => $etsy_option["etsy_property_id"], "name" => $etsy_option["etsy_property_title"]);
            }
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $data['token'] = $this->session_token;
        $data['session_token_key'] = $this->session_token_key;
        $data['module_path'] = $this->module_path;
        $data['active_tab'] = 5;
        $data['tabs'] = $this->load->controller($this->module_path . '/tabs', $data);

        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/attribute_mapping', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/attribute_mapping.tpl', $data));
        }
    }

    public function saveAttributeMapping()
    {
        $response = array();
        $this->load->model('kbetsy/kbetsy');
        $this->load->model('kbetsy/attribute');
        $this->load->language($this->module_path);

        if (isset($this->request->post['etsy_option_id']) && isset($this->request->post['opencart_option_id'])) {
            if ($this->request->post['etsy_option_id'] == "" || $this->request->post['opencart_option_id'] == "") {
                $response = array("type" => "error", "message" => $this->language->get('save_attribute_empty_field'));
            } else {
                $this->model_kbetsy_attribute->saveMapping($this->request->post['etsy_option_id'], $this->request->post['opencart_option_id']);
                $response = array("type" => "success");
                $this->session->data['success'] = $this->language->get('save_attribute_success');
            }
        } else {
            $response = array("type" => "error", "message" => $this->language->get('save_attribute_error'));
        }
        echo json_encode($response);
        die();
    }

    public function tabs($data)
    {
        $this->load->language($this->module_path);

        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_gs'] = $this->language->get('text_gs');
        $data['text_am'] = $this->language->get('text_am');
        $data['text_pm'] = $this->language->get('text_pm');
        $data['text_st'] = $this->language->get('text_st');
        $data['text_ste'] = $this->language->get('text_ste');
        $data['text_pl'] = $this->language->get('text_pl');
        $data['text_os'] = $this->language->get('text_os');
        $data['text_ol'] = $this->language->get('text_ol');
        $data['text_sy'] = $this->language->get('text_sy');
        $data['text_al'] = $this->language->get('text_al');
        $data['text_ss'] = $this->language->get('text_ss');
        $data['text_support'] = $this->language->get('text_support');

        $data['general_settings'] = $this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['profile_management'] = $this->url->link($this->module_path . '/profileManagement', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['product_listing'] = $this->url->link($this->module_path . '/productListing', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['order_listing'] = $this->url->link($this->module_path . '/orderListing', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['shipping_templates'] = $this->url->link($this->module_path . '/shippingTemplates', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['shop_section'] = $this->url->link($this->module_path . '/shopSection', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['shipping_template_entries'] = $this->url->link($this->module_path . '/shippingTemplateEntries', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['synchronization'] = $this->url->link($this->module_path . '/synchronization', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['audit_log'] = $this->url->link($this->module_path . '/auditLog', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['attribute_mapping'] = $this->url->link($this->module_path . '/attributeMapping', $this->session_token_key . '=' . $this->session_token, 'SSL');
        $data['support'] = $this->url->link($this->module_path . '/support', $this->session_token_key . '=' . $this->session_token, 'SSL');

        if (VERSION >= '2.2.0.0') {
            return $this->load->view($this->module_path . '/tabs', $data);
        } else {
            return $this->load->view($this->module_path . '/tabs.tpl', $data);
        }
    }

    public function support()
    {
        $this->load->language($this->module_path);
        $this->document->setTitle($this->language->get('heading_title_main'));

        if (isset($this->request->get['store_id'])) {
            $store_id = $this->request->get['store_id'];
        } else {
            $store_id = 0;
        }

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => false
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link($this->extension_path, $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->url->link($this->module_path . '/kbebay', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_support'),
            'href' => $this->url->link($this->module_path . '/kbebay/support', $this->session_token_key . '=' . $this->session_token, 'SSL'),
            'separator' => ' :: '
        );
        $data['heading_title'] = $this->language->get('heading_title');
        $data['heading_title_main'] = $this->language->get('heading_title_main');
        $data['text_support'] = $this->language->get('text_support');

        $data['text_click_here'] = $this->language->get('text_click_here');        
        $data['text_user_manual'] = $this->language->get('text_user_manual');
        $data['text_support_other'] = $this->language->get('text_support_other');
        $data['text_support_marketplace'] = $this->language->get('text_support_marketplace');
        $data['text_support_marketplace_descp'] = $this->language->get('text_support_marketplace_descp');
        $data['text_support_gs'] = $this->language->get('text_support_gs');
        $data['text_support_gs_descp'] = $this->language->get('text_support_gs_descp');
        $data['text_support_ebay'] = $this->language->get('text_support_ebay');
        $data['text_support_ebay_descp'] = $this->language->get('text_support_ebay_descp');
        $data['text_support_mab'] = $this->language->get('text_support_mab');
        $data['text_support_mab_descp'] = $this->language->get('text_support_mab_descp');
        $data['text_support_view_more'] = $this->language->get('text_support_view_more');
        $data['text_support_ticket1'] = $this->language->get('text_support_ticket1');
        $data['text_support_ticket2'] = $this->language->get('text_support_ticket2');
        $data['text_support_ticket3'] = $this->language->get('text_support_ticket3');

        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $data['active_tab'] = 10;
        $data['tab_common'] = $this->load->controller($this->module_path . '/tabs', $data);
        if (VERSION >= '2.2.0.0') {
            $this->response->setOutput($this->load->view($this->module_path . '/support', $data));
        } else {
            $this->response->setOutput($this->load->view($this->module_path . '/support.tpl', $data));
        }
    }
    
    private function checkSettings()
    {
        $this->load->language($this->module_path);
        $this->load->model('kbetsy/kbetsy');
        $config = $this->model_kbetsy_kbetsy->getSetting('etsy_general_settings');
        if (empty($config)) {
            $this->session->data['error'] = $this->language->get('text_save_general_setting');
            $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
        } else {
            $etsy_access_token = $this->model_kbetsy_kbetsy->getSetting('etsy_access_token');
            $etsy_access_token_secret = $this->model_kbetsy_kbetsy->getSetting('etsy_access_token_secret');
            if (empty($etsy_access_token) || empty($etsy_access_token_secret)) {
                $this->session->data['error'] = $this->language->get('text_save_connect_setting_error');
                $this->response->redirect($this->url->link($this->module_path . '/generalSettings', $this->session_token_key . '=' . $this->session_token, 'SSL'));
            }
        }
    }

    private function executeCRON($name)
    {
        $url = HTTPS_CATALOG . "index.php?route=kbetsy/" . $name;
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, TRUE);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

}

?>