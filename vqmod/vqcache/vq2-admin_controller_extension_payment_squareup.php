<?php

use vendor\isenselabs\Squareup\Compatibility\Controller\Payment as Controller;

class ControllerExtensionPaymentSquareup extends Controller {
    const CRON_ENDED_FLAG_COMPLETE = 1;
    const CRON_ENDED_FLAG_ERROR = 2;
    const CRON_ENDED_FLAG_TIMEOUT = 3;

    private $error = array();

    public function index() {
        // $this->load->language($this->imodule_route_payment);

        $this->load->model($this->imodule_route_payment);
        $this->load->model('setting/setting');

        $this->loadLibrary('vendor/isenselabs/squareup');

        // Ensures missing tables would be created
        $this->imodule_model_payment->createTables();

        // Ensures that all necessary alterations would be performed after an update
        $this->imodule_model_payment->alterTables();
        $this->imodule_model_payment->createIndexes();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->user->hasPermission('modify', $this->imodule_route_payment) && version_compare(VERSION, '2.0.0.0', '>=')) {
            // Ensures that the necessary events are hooked
            $this->imodule_model_payment->dropEvents();
            $this->imodule_model_payment->createEvents();
        }

        //Check for old columns
        $this->imodule_model_payment->updateDatabase();

        $missing_geo_zones = $this->imodule_model_payment->missingPreliminaryGeoZones();
        $skip_geo_zones = (bool)$this->config->get('squareup_skip_geo_zones');
        $from_geo_zone_link = (bool)isset($this->request->get['show_geo_zone']);

        // Deprecated - should be used when we implement a Catalog sync in the direction Square > OpenCart
        $can_modify_geo_zones = false; //$this->user->hasPermission('modify', 'localisation/geo_zone');

        if ($can_modify_geo_zones && ($from_geo_zone_link || ($missing_geo_zones && !$skip_geo_zones))) {
            $this->showGeoZones();
        } else {
            $this->showForm();
        }
    }

    public function connect() {
        // $this->load->language($this->imodule_route_payment);

        $this->loadLibrary('vendor/isenselabs/squareup');

        $json = array();

        if (!$this->user->hasPermission('modify', $this->imodule_route_payment)) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['squareup_client_id']) || strlen($this->request->post['squareup_client_id']) > 32) {
            $json['error'] = $this->language->get('error_client_id');
        }

        if (empty($this->request->post['squareup_client_secret']) || strlen($this->request->post['squareup_client_secret']) > 50) {
            $json['error'] = $this->language->get('error_client_secret');
        }

        if (empty($json['error'])) {
            $this->session->data['squareup_connect']['squareup_client_id'] = $this->request->post['squareup_client_id'];
            $this->session->data['squareup_connect']['squareup_client_secret'] = $this->request->post['squareup_client_secret'];
            $this->session->data['squareup_connect']['squareup_webhook_signature'] = $this->request->post['squareup_webhook_signature'];

            $json['redirect'] = $this->squareup_api->authLink($this->request->post['squareup_client_id']);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function transaction_info() {
        // $this->load->language($this->imodule_route_payment);

        $this->load->model($this->imodule_route_payment);

        $this->loadLibrary('vendor/isenselabs/squareup');

        if (isset($this->request->get['squareup_transaction_id'])) {
            $squareup_transaction_id = $this->request->get['squareup_transaction_id'];
        } else {
            $squareup_transaction_id = 0;
        }

        $transaction_info = $this->imodule_model_payment->getTransaction($squareup_transaction_id);

        if (empty($transaction_info)) {
            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }

        $transaction_status = $this->imodule_model_payment->getTransactionStatus($transaction_info);

        $this->document->setTitle(sprintf($this->language->get('heading_title_transaction'), $transaction_info['transaction_id']));

        $data = $this->imodule_language_payment;

        $data['alerts'] = $this->pullAlerts();

        $this->clearAlerts();

        $data['text_edit'] = sprintf($this->language->get('heading_title_transaction'), $transaction_info['transaction_id']);

        $amount = $this->currency->format($transaction_info['transaction_amount'], $transaction_info['transaction_currency']);

        $data['confirm_capture'] = sprintf($this->language->get('text_confirm_capture'), $amount);
        $data['confirm_void'] = sprintf($this->language->get('text_confirm_void'), $amount);
        $data['confirm_refund'] = $this->language->get('text_confirm_refund');
        $data['insert_amount'] = sprintf($this->language->get('text_insert_amount'), $amount, $transaction_info['transaction_currency']);
        $data['text_loading'] = $this->language->get('text_loading_short');

        $data['billing_address_company'] = $transaction_info['billing_address_company'];
        $data['billing_address_street'] = $transaction_info['billing_address_street_1'] . ' ' . $transaction_info['billing_address_street_2'];
        $data['billing_address_city'] = $transaction_info['billing_address_city'];
        $data['billing_address_postcode'] = $transaction_info['billing_address_postcode'];
        $data['billing_address_province'] = $transaction_info['billing_address_province'];
        $data['billing_address_country'] = $transaction_info['billing_address_country'];

        $data['transaction_id'] = $transaction_info['transaction_id'];
        $data['is_fully_refunded'] = $transaction_status['is_fully_refunded'];
        $data['merchant'] = $transaction_info['merchant_id'];
        $data['order_id'] = $transaction_info['order_id'];
        $data['store_id'] = $this->imodule_model_payment->getOrderStoreId($transaction_info['order_id']);
        $data['status'] = $transaction_status['text'];
        $data['order_history_data'] = json_encode($transaction_status['order_history_data']);
        $data['amount'] = $amount;
        $data['currency'] = $transaction_info['transaction_currency'];
        $data['browser'] = $transaction_info['device_browser'];
        $data['ip'] = $transaction_info['device_ip'];
        $data['date_created'] = date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($transaction_info['created_at']));

        $data['is_merchant_transaction'] = $transaction_status['is_merchant_transaction'];

        if (!$data['is_merchant_transaction']) {
            $data['alerts'][] = array(
                'type' => 'warning',
                'icon' => 'warning',
                'text' => sprintf($this->language->get('text_different_merchant'), $transaction_info['merchant_id'], $this->config->get('squareup_merchant_id'))
            );
        }

        $data['cancel'] = $this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'] . '&tab=tab-transaction', $this->getUrlSsl());

        $data['url_order'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $transaction_info['order_id'], $this->getUrlSsl());
        $data['url_void'] = $this->url->link($this->imodule_route_payment . '/void', 'token=' . $this->session->data['token'] . '&preserve_alert=true&squareup_transaction_id=' . $transaction_info['squareup_transaction_id'], $this->getUrlSsl());
        $data['url_capture'] = $this->url->link($this->imodule_route_payment . '/capture', 'token=' . $this->session->data['token'] . '&preserve_alert=true&squareup_transaction_id=' . $transaction_info['squareup_transaction_id'], $this->getUrlSsl());
        $data['url_refund'] = $this->url->link($this->imodule_route_payment . '/refund', 'token=' . $this->session->data['token'] . '&preserve_alert=true&squareup_transaction_id=' . $transaction_info['squareup_transaction_id'], $this->getUrlSsl());
        $data['url_refund_modal'] = $this->url->link($this->imodule_route_payment . '/refund_modal', 'token=' . $this->session->data['token'] . '&preserve_alert=true&squareup_transaction_id=' . $transaction_info['squareup_transaction_id'], $this->getUrlSsl());
        $data['url_transaction'] = sprintf(
            vendor\isenselabs\Squareup::VIEW_TRANSACTION_URL,
            $transaction_info['transaction_id'],
            $transaction_info['location_id']
        );

        $data['is_authorized'] = in_array($transaction_info['transaction_type'], array('AUTHORIZED'));
        $data['is_captured'] = in_array($transaction_info['transaction_type'], array('CAPTURED'));

        $data['has_refunds'] = count($transaction_status['refunds']) > 0;

        if ($data['has_refunds']) {
            $data['refunds'] = array();

            $data['text_refunds'] = sprintf($this->language->get('text_refunds'), count($transaction_status['refunds']));

            foreach ($transaction_status['refunds'] as $refund) {
                $amount = $this->currency->format(
                    $this->squareup_api->standardDenomination(
                        $refund['amount_money']['amount'],
                        $refund['amount_money']['currency']
                    ),
                    $refund['amount_money']['currency']
                );

                if (isset($refund['processing_fee_money'])) {
                    $fee = $this->currency->format(
                        $this->squareup_api->standardDenomination(
                            $refund['processing_fee_money']['amount'],
                            $refund['processing_fee_money']['currency']
                        ),
                        $refund['processing_fee_money']['currency']
                    );
                } else {
                    $fee = $this->language->get('text_na');
                }

                $data['refunds'][] = array(
                    'date_created' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($refund['created_at'])),
                    'reason' => $refund['reason'],
                    'status' => $refund['status'],
                    'amount' => $amount,
                    'fee' => $fee
                );
            }
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link($this->imodule_extension_route, 'token=' . $this->session->data['token'] . '&type=payment', $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => sprintf($this->language->get('heading_title_transaction'), $transaction_info['squareup_transaction_id']),
            'href' => $this->url->link($this->imodule_route_payment . '/transaction_info', 'token=' . $this->session->data['token'] . '&squareup_transaction_id=' . $squareup_transaction_id, $this->getUrlSsl())
        );

        // API login
        $data['button_ip_add'] = $this->language->get('button_ip_add');
        $data['error_warning'] = '';
        $data['catalog'] = $this->isSSL() ? HTTPS_CATALOG : HTTP_CATALOG;
        $data['token'] = $this->session->data['token'];

        $this->loadApi($data);

        $this->loadAdminChildren($data);

        $this->response->setOutput($this->loadView($this->imodule_route_payment . '_transaction_info', $data));
    }

    public function transactions() {
        // $this->load->language($this->imodule_route_payment);

        $this->load->model($this->imodule_route_payment);

        if (isset($this->request->get['page'])) {
            $page = (int)$this->request->get['page'];
        } else {
            $page = 1;
        }

        $order_histories = array();

        $result = array(
            'transactions' => array(),
            'pagination' => ''
        );

        $filter_data = array(
            'start' => ($page - 1) * (int)10,
            'limit' => 10
        );

        if (isset($this->request->get['order_id'])) {
            // We want to get all possible transactions, regardless of the selected page
            $filter_data = array(
                'order_id' => $this->request->get['order_id']
            );
        }

        $transactions_total = $this->imodule_model_payment->getTotalTransactions($filter_data);
        $transactions = $this->imodule_model_payment->getTransactions($filter_data);

        $this->load->model('sale/order');

        foreach ($transactions as $transaction) {
            $amount = $this->currency->format($transaction['transaction_amount'], $transaction['transaction_currency']);

            $order_info = $this->model_sale_order->getOrder($transaction['order_id']);

            $transaction_status = $this->imodule_model_payment->getTransactionStatus($transaction);

            if ($transaction_status['order_history_data']) {
                $order_histories[] = $transaction_status['order_history_data'];
            }

            $result['transactions'][] = array(
                'squareup_transaction_id' => $transaction['squareup_transaction_id'],
                'transaction_id' => $transaction['transaction_id'],
                'url_order' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $transaction['order_id'], $this->getUrlSsl()),
                'url_void' => $this->url->link($this->imodule_route_payment . '/void', 'token=' . $this->session->data['token'] . '&squareup_transaction_id=' . $transaction['squareup_transaction_id'], $this->getUrlSsl()),
                'url_capture' => $this->url->link($this->imodule_route_payment . '/capture', 'token=' . $this->session->data['token'] . '&squareup_transaction_id=' . $transaction['squareup_transaction_id'], $this->getUrlSsl()),
                'url_refund' => $this->url->link($this->imodule_route_payment . '/refund', 'token=' . $this->session->data['token'] . '&squareup_transaction_id=' . $transaction['squareup_transaction_id'], $this->getUrlSsl()),
                'url_refund_modal' => $this->url->link($this->imodule_route_payment . '/refund_modal', 'token=' . $this->session->data['token'] . '&squareup_transaction_id=' . $transaction['squareup_transaction_id'], $this->getUrlSsl()),
                'confirm_capture' => sprintf($this->language->get('text_confirm_capture'), $amount),
                'confirm_void' => sprintf($this->language->get('text_confirm_void'), $amount),
                'order_id' => $transaction['order_id'],
                'store_id' => $this->imodule_model_payment->getOrderStoreId($transaction['order_id']),
                'type' => $transaction_status['type'],
                'status' => $transaction_status['text'],
                'amount_refunded' => $transaction_status['amount_refunded'],
                'is_fully_refunded' => $transaction_status['is_fully_refunded'],
                'order_history_data' => $transaction_status['order_history_data'],
                'is_merchant_transaction' => $transaction_status['is_merchant_transaction'],
                'text_different_merchant' => sprintf($this->language->get('text_different_merchant'), $transaction['merchant_id'], $this->config->get('squareup_merchant_id')),
                'amount' => $amount,
                'customer' => $order_info['firstname'] . ' ' . $order_info['lastname'],
                'ip' => $transaction['device_ip'],
                'date_created' => date($this->language->get('date_format_short') . ' ' . $this->language->get('time_format'), strtotime($transaction['created_at'])),
                'url_info' => $this->url->link($this->imodule_route_payment . '/transaction_info', 'token=' . $this->session->data['token'] . '&squareup_transaction_id=' . $transaction['squareup_transaction_id'], $this->getUrlSsl())
            );
        }

        $pagination = new Pagination();
        $pagination->total = $transactions_total;
        $pagination->page = $page;
        $pagination->limit = 10;
        $pagination->url = '{page}';

        $result['pagination'] = $pagination->render();

        if (isset($this->request->get['order_id'])) {
            $result['order_histories'] = $order_histories;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($result));
    }

    public function refresh_token() {
        // $this->load->language($this->imodule_route_payment);

        if (!$this->user->hasPermission('modify', $this->imodule_route_payment)) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('error_permission')
            ));

            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }

        $this->load->model('setting/setting');

        $this->loadLibrary('vendor/isenselabs/squareup');

        try {
            $response = $this->squareup_api->refreshToken();

            if (!isset($response['access_token']) || !isset($response['token_type']) || !isset($response['expires_at']) || !isset($response['merchant_id']) ||
                $response['merchant_id'] != $this->config->get('squareup_merchant_id')) {
                $this->pushAlert(array(
                    'type' => 'danger',
                    'type_oc15' => 'warning',
                    'icon' => 'exclamation-circle',
                    'text' => $this->language->get('error_refresh_access_token')
                ));
            } else {
                $settings = $this->model_setting_setting->getSetting('squareup');

                $settings['squareup_access_token'] = $response['access_token'];
                $settings['squareup_access_token_expires'] = $response['expires_at'];

                $this->model_setting_setting->editSetting('squareup', $settings);

                $this->pushAlert(array(
                    'type' => 'success',
                    'icon' => 'exclamation-circle',
                    'text' => $this->language->get('text_refresh_access_token_success')
                ));
            }
        } catch (vendor\isenselabs\Squareup\Exception\Api $e) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => sprintf($this->language->get('error_token'), $e->getMessage())
            ));
        }

        $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
    }

    public function oauth_callback() {
        // $this->load->language($this->imodule_route_payment);

        if (!$this->user->hasPermission('modify', $this->imodule_route_payment)) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('error_permission')
            ));

            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }

        $this->loadLibrary('vendor/isenselabs/squareup');

        if (isset($this->request->get['error']) || isset($this->request->get['error_description'])) {
            // auth error
            if ($this->request->get['error'] == 'access_denied' && $this->request->get['error_description'] == 'user_denied') {
                // user rejected giving auth permissions to his store
                $this->pushAlert(array(
                    'type' => 'warning',
                    'icon' => 'exclamation-circle',
                    'text' => $this->language->get('error_user_rejected_connect_attempt')
                ));
            }

            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }

        // verify parameters for the redirect from Square (against random url crawling)
        if (!isset($this->request->get['state']) || !isset($this->request->get['code'])) {
            // missing or wrong info
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('error_possible_xss')
            ));

            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }

        // verify the state (against cross site requests)
        if (!isset($this->session->data['squareup_oauth_state']) || $this->session->data['squareup_oauth_state'] != $this->request->get['state']) {
            // state mismatch
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('error_possible_xss')
            ));

            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }

        try {
            $token = $this->squareup_api->exchangeCodeForAccessToken($this->request->get['code']);

            $this->session->data['squareup_token'] = $token;

            if ($this->config->has('squareup_merchant_id') && $this->config->get('squareup_merchant_id') != $token['merchant_id']) {
                $this->responseRedirect($this->url->link($this->imodule_route_payment . '/confirm_merchant', 'token=' . $this->session->data['token'], $this->getUrlSsl()));
            } else {
                $this->acceptNewMerchant();
                $this->clearConnectSession();
                $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
            }
        } catch (vendor\isenselabs\Squareup\Exception\Api $e) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => sprintf($this->language->get('error_token'), $e->getMessage())
            ));

            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }
    }

    public function confirm_merchant() {
        if (empty($this->session->data['squareup_token'])) {
            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }

        $has_catalog_sync = 'none' != $this->config->get('squareup_sync_source');

        if (isset($this->request->get['action'])) {
            if ($this->request->get['action'] == 'confirm') {
                $this->load->model($this->imodule_route_payment);

                $this->acceptNewMerchant($has_catalog_sync);
                $this->imodule_model_payment->truncateMerchantSpecificTables();
            }

            $this->clearConnectSession();
            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
        }

        // $this->load->language($this->imodule_route_payment);

        $this->document->setTitle($this->language->get('heading_title_confirm_merchant'));

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link($this->imodule_extension_route, 'token=' . $this->session->data['token'] . '&type=payment', $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_confirm_merchant'),
            'href' => $this->url->link($this->imodule_route_payment . '/confirm_merchant', 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        $data['has_catalog_sync'] = $has_catalog_sync;

        $data['confirm'] = $this->url->link($this->imodule_route_payment . '/confirm_merchant', 'token=' . $this->session->data['token'] . '&action=confirm', $this->getUrlSsl());
        $data['reject'] = $this->url->link($this->imodule_route_payment . '/confirm_merchant', 'token=' . $this->session->data['token'] . '&action=reject', $this->getUrlSsl());

        $this->loadAdminChildren($data);

        $this->response->setOutput($this->loadView($this->imodule_route_payment . '_confirm_merchant', $data));
    }

    protected function acceptNewMerchant($force_on_demand_sync = false) {
        // $this->load->language($this->imodule_route_payment);

        $this->loadLibrary('vendor/isenselabs/squareup');

        $this->load->model('setting/setting');

        $token = $this->session->data['squareup_token'];

        $previous_setting = $this->model_setting_setting->getSetting('squareup');

        $previous_setting['squareup_locations'] = $this->squareup_api->fetchLocations($token['access_token'], $first_location_id);

        if (
            !isset($previous_setting['squareup_location_id']) ||
            (isset($previous_setting['squareup_location_id']) && !in_array(
                $previous_setting['squareup_location_id'],
                array_map(
                    function($location) {
                        return $location['id'];
                    },
                    $previous_setting['squareup_locations']
                )
            ))
        ) {
            $previous_setting['squareup_location_id'] = $first_location_id;
        }

        unset($previous_setting['squareup_sandbox_locations']);
        unset($previous_setting['squareup_sandbox_location_id']);

        $previous_setting['squareup_client_id'] = $this->session->data['squareup_connect']['squareup_client_id'];
        $previous_setting['squareup_client_secret'] = $this->session->data['squareup_connect']['squareup_client_secret'];
        $previous_setting['squareup_webhook_signature'] = $this->session->data['squareup_connect']['squareup_webhook_signature'];
        $previous_setting['squareup_merchant_id'] = $token['merchant_id'];
        $previous_setting['squareup_merchant_name'] = '';
        $previous_setting['squareup_access_token'] = $token['access_token'];
        $previous_setting['squareup_access_token_expires'] = $token['expires_at'];

        if ($force_on_demand_sync) {
            $previous_setting['squareup_cron_is_on_demand'] = '1';
        }

        $this->model_setting_setting->editSetting('squareup', $previous_setting);

        $this->pushAlert(array(
            'type' => 'success',
            'icon' => 'exclamation-circle',
            'text' => $this->language->get('text_refresh_access_token_success')
        ));
    }

    protected function clearConnectSession() {
        unset($this->session->data['squareup_connect']);
        unset($this->session->data['squareup_oauth_state']);
        unset($this->session->data['squareup_oauth_redirect']);
        unset($this->session->data['squareup_token']);
    }

    public function capture() {
        $this->transactionAction(function($transaction_info, &$json) {
            $this->squareup_api->captureTransaction($transaction_info['location_id'], $transaction_info['transaction_id']);

            $status = 'CAPTURED';

            $this->imodule_model_payment->updateTransaction($transaction_info['squareup_transaction_id'], $status);

            $json['order_history_data'] = array(
                'notify' => 1,
                'override' => 0,
                'squareup_is_capture' => true,
                'order_id' => $transaction_info['order_id'],
                'store_id' => $this->imodule_model_payment->getOrderStoreId($transaction_info['order_id']),
                'order_status_id' => $this->imodule_model_payment->getOrderStatusId($transaction_info['order_id'], $status),
                'comment' => $this->language->get('squareup_status_comment_' . strtolower($status)),
            );

            $json['success'] = $this->language->get('text_success_capture');
        });
    }

    public function void() {
        $this->transactionAction(function($transaction_info, &$json) {
            $this->squareup_api->voidTransaction($transaction_info['location_id'], $transaction_info['transaction_id']);

            $status = 'VOIDED';

            $this->imodule_model_payment->updateTransaction($transaction_info['squareup_transaction_id'], $status);

            $json['order_history_data'] = array(
                'notify' => 1,
                'override' => 0,
                'order_id' => $transaction_info['order_id'],
                'store_id' => $this->imodule_model_payment->getOrderStoreId($transaction_info['order_id']),
                'order_status_id' => $this->imodule_model_payment->getOrderStatusId($transaction_info['order_id'], $status),
                'comment' => $this->language->get('squareup_status_comment_' . strtolower($status)),
            );

            $json['success'] = $this->language->get('text_success_void');
        });
    }

    public function refund_modal() {
        // $this->load->language($this->imodule_route_payment);

        $this->load->model($this->imodule_route_payment);

        $this->loadLibrary('vendor/isenselabs/squareup');

        $json = array();

        if (!$this->user->hasPermission('modify', $this->imodule_route_payment)) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $data = $this->imodule_language_payment;

            $transaction_info = $this->imodule_model_payment->getTransaction($this->request->get['squareup_transaction_id']);

            $max_allowed_amount = $this->squareup_api->lowestDenomination($transaction_info['transaction_amount'], $transaction_info['transaction_currency']);

            if (!empty($transaction_info['refunds'])) {
                foreach (json_decode($transaction_info['refunds'], true) as $refund) {
                    $max_allowed_amount -= $refund['amount_money']['amount'];
                }
            }

            $max_allowed_amount_standard = $this->squareup_api->standardDenomination($max_allowed_amount, $transaction_info['transaction_currency']);

            $max_allowed = $this->currency->format($max_allowed_amount_standard, $transaction_info['transaction_currency']);

            $data['price_prefix'] = $this->currency->getSymbolLeft($transaction_info['transaction_currency']);
            $data['price_suffix'] = $this->currency->getSymbolRight($transaction_info['transaction_currency']);

            $data['max_allowed'] = $max_allowed_amount_standard;

            $data['text_itemized_refund_intro'] = sprintf($this->language->get('text_itemized_refund_intro'), $max_allowed, $transaction_info['transaction_currency']);

            $data['products'] = array();

            $this->load->model('sale/order');

            $products = $this->model_sale_order->getOrderProducts($transaction_info['order_id']);

            foreach ($products as $product) {
                $is_ad_hoc_item = $this->imodule_model_payment->isAdHocItem($product['order_product_id']);
                $allowed_restock_quantity = $this->imodule_model_payment->getAllowedRestockQuantity($product['order_product_id']);
                $allowed_refund_quantity = $this->imodule_model_payment->getAllowedRefundQuantity($product['order_product_id']);

                $max_refund_quantity = min($allowed_refund_quantity, (int)$product['quantity']);
                $max_restock_quantity = min($max_refund_quantity, $allowed_restock_quantity, (int)$product['quantity']);

                $price = $product['price'] + $product['tax'];
                $total = $product['total'] + (int)$product['quantity'] * $product['tax'];

                $data['products'][] = array(
                    'product_id'                        => $product['product_id'],
                    'order_product_id'                  => $product['order_product_id'],
                    'name'                              => $product['name'],
                    'model'                             => $product['model'],
                    'options'                           => $this->model_sale_order->getOrderOptions($transaction_info['order_id'], $product['order_product_id']),
                    'quantity'                          => $product['quantity'],
                    'max_restock_quantity'              => $max_restock_quantity,
                    'max_refund_quantity'               => $max_refund_quantity,
                    'is_ad_hoc_item'                    => $is_ad_hoc_item,
                    'price_raw'                         => $price,
                    'price'                             => $this->currency->format($price, $transaction_info['transaction_currency']),
                    'price_total_raw'                   => $total,
                    'price_total'                       => $this->currency->format($total, $transaction_info['transaction_currency'])
                );
            }

            $data['text_insert_amount'] = sprintf($this->language->get('text_insert_amount'), $max_allowed, $transaction_info['transaction_currency']);

            $json['html'] = $this->loadView($this->imodule_route_payment . '_refund_modal', $data);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function refund() {
        $this->transactionAction(function($transaction_info, &$json) {
            if (!empty($this->request->post['reason'])) {
                $reason = $this->request->post['reason'];
            } else {
                $reason = $this->language->get('text_no_reason_provided');
            }

            if (!empty($this->request->post['amount'])) {
                $amount = preg_replace('~[^0-9\.\,]~', '', $this->request->post['amount']);

                if (strpos($amount, ',') !== FALSE && strpos($amount, '.') !== FALSE) {
                    $amount = (float)str_replace(',', '', $amount);
                } else if (strpos($amount, ',') !== FALSE && strpos($amount, '.') === FALSE) {
                    $amount = (float)str_replace(',', '.', $amount);
                } else {
                    $amount = (float)$amount;
                }
            } else {
                $amount = 0;
            }

            $currency = $transaction_info['transaction_currency'];
            $tenders = @json_decode($transaction_info['tenders'], true);

            $updated_transaction = $this->squareup_api->refundTransaction($transaction_info['location_id'], $transaction_info['transaction_id'], $reason, $amount, $currency, $tenders[0]['id']);

            $status = $updated_transaction['tenders'][0]['card_details']['status'];

            $refunds = array();

            if (!empty($updated_transaction['refunds'])) {
                $refunds = $updated_transaction['refunds'];
            }

            $this->imodule_model_payment->updateTransaction($transaction_info['squareup_transaction_id'], $status, $refunds);

            $total_refunded_amount = 0;
            $has_pending = false;
            foreach ($refunds as $refund) {
                if ($refund['status'] == 'REJECTED' || $refund['status'] == 'FAILED') {
                    continue;
                }

                if ($refund['status'] == 'PENDING') {
                    $has_pending = true;
                }

                $total_refunded_amount = $refund['amount_money']['amount'];
            }

            $refund_status = null;
            if (!$has_pending) {
                if ($total_refunded_amount == $this->squareup_api->lowestDenomination($transaction_info['transaction_amount'], $transaction_info['transaction_currency'])) {
                    $refund_status = 'fully_refunded';
                } else {
                    $refund_status = 'partially_refunded';
                }
            }

            $last_refund = array_pop($refunds);

            if ($last_refund) {
                $refunded_amount = $this->currency->format(
                    $this->squareup_api->standardDenomination(
                        $last_refund['amount_money']['amount'],
                        $last_refund['amount_money']['currency']
                    ),
                    $last_refund['amount_money']['currency']
                );

                $comment = sprintf($this->language->get('text_refunded_amount'), $refunded_amount, $last_refund['status'], $last_refund['reason']);

                $order_history_data = array(
                    'notify' => 1,
                    'override' => 0,
                    'order_id' => $transaction_info['order_id'],
                    'store_id' => $this->imodule_model_payment->getOrderStoreId($transaction_info['order_id']),
                    'order_status_id' => $this->imodule_model_payment->getOrderStatusId($transaction_info['order_id'], $refund_status),
                    'comment' => $comment
                );

                if (isset($this->request->post['restock']) || isset($this->request->post['refund'])) {
                    $order_history = new vendor\isenselabs\Squareup\OrderHistory($this->registry);

                    if (isset($this->request->post['restock'])) {
                        $restock = array();

                        foreach ($this->request->post['restock'] as $order_product_id => $quantity) {
                            $catalog_object_id = $order_history->getSquareItemObjectIdByOrderProductId($order_product_id);
                            $product_id = $order_history->getProductIdByOrderProductId($order_product_id);

                            $restock[] = array(
                                'catalog_object_id' => false !== $catalog_object_id ? $catalog_object_id : null,
                                'quantity' => $quantity,
                                'order_product_id' => $order_product_id,
                                'product_id' => $product_id
                            );
                        }

                        if (!empty($restock)) {
                            $order_history_data['square_restock'] = $restock;
                        }
                    }

                    if (isset($this->request->post['refund'])) {
                        $refund = array();

                        foreach ($this->request->post['refund'] as $order_product_id => $quantity) {
                            $catalog_object_id = $order_history->getSquareItemObjectIdByOrderProductId($order_product_id);
                            $product_id = $order_history->getProductIdByOrderProductId($order_product_id);

                            $refund[] = array(
                                'catalog_object_id' => false !== $catalog_object_id ? $catalog_object_id : null,
                                'quantity' => $quantity,
                                'order_product_id' => $order_product_id,
                                'product_id' => $product_id
                            );
                        }

                        if (!empty($refund)) {
                            $order_history_data['square_refund'] = $refund;
                        }
                    }
                }

                $json['order_history_data'] = $order_history_data;

                $json['success'] = $this->language->get('text_success_refund');
            } else {
                $json['error'] = $this->language->get('error_no_refund');
            }
        });
    }

    public function order() {
        // $this->load->language($this->imodule_route_payment);

        $data = $this->imodule_language_payment;

        $data['url_list_transactions'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/transactions', 'token=' . $this->session->data['token'] . '&order_id=' . $this->request->get['order_id'] . '&page={PAGE}', $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['token'] = $this->session->data['token'];
        $data['order_id'] = $this->request->get['order_id'];

        // API login
        $data['button_ip_add'] = $this->language->get('button_ip_add');
        $data['error_warning'] = '';
        $data['catalog'] = $this->isSSL() ? HTTPS_CATALOG : HTTP_CATALOG;
        $data['token'] = $this->session->data['token'];

        $this->loadApi($data);

        return $this->loadView($this->imodule_route_payment . '_order', $data);
    }

    public function install() {
        $this->load->model($this->imodule_route_payment);

        $this->imodule_model_payment->createTables();
    }

    public function uninstall() {
        $this->load->model($this->imodule_route_payment);

        $this->imodule_model_payment->dropTables();
    }

    public function recurringButtons() {
        if (!$this->user->hasPermission('modify', 'sale/recurring')) {
            return;
        }

        $this->load->model($this->imodule_route_payment);

        // $this->load->language($this->imodule_route_payment);

        $data = $this->imodule_language_payment;

        if (isset($this->request->get['order_recurring_id'])) {
            $order_recurring_id = $this->request->get['order_recurring_id'];
        } else {
            $order_recurring_id = 0;
        }

        $recurring_info = $this->model_sale_recurring->{$this->config->get('squareup_imodule_recurring_get_method_name')}($order_recurring_id);

        $data['button_text'] = $this->language->get('button_cancel_recurring');

        if ($recurring_info[$this->config->get('squareup_imodule_recurring_info_status_key')] == ModelExtensionPaymentSquareup::RECURRING_ACTIVE) {
            $data['order_recurring_id'] = $order_recurring_id;
        } else {
            $data['order_recurring_id'] = '';
        }

        $this->load->model('sale/order');

        $order_info = $this->model_sale_order->getOrder($recurring_info['order_id']);

        $data['order_id'] = $recurring_info['order_id'];
        $data['store_id'] = $order_info['store_id'];
        $data['order_status_id'] = $order_info['order_status_id'];
        $data['comment'] = $this->language->get('text_order_history_cancel');
        $data['notify'] = 1;

        // API login
        $data['button_ip_add'] = $this->language->get('button_ip_add');
        $data['error_warning'] = '';
        $data['catalog'] = $this->isSSL() ? HTTPS_CATALOG : HTTP_CATALOG;
        $data['token'] = $this->session->data['token'];

        $this->loadApi($data);

        $data['cancel'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/recurringCancel', 'order_recurring_id=' . $order_recurring_id . '&token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");

        return $this->loadView($this->imodule_route_payment . '_recurring_buttons', $data);
    }

    public function recurringCancel() {
        // $this->load->language($this->imodule_route_payment);

        $json = array();

        if (!$this->user->hasPermission('modify', 'sale/recurring')) {
            $json['error'] = $this->language->get('error_permission_recurring');
        } else {
            $this->load->model('sale/recurring');

            if (isset($this->request->get['order_recurring_id'])) {
                $order_recurring_id = $this->request->get['order_recurring_id'];
            } else {
                $order_recurring_id = 0;
            }

            $recurring_info = $this->model_sale_recurring->{$this->config->get('squareup_imodule_recurring_get_method_name')}($order_recurring_id);

            if ($recurring_info) {
                $this->load->model($this->imodule_route_payment);

                $this->imodule_model_payment->editOrderRecurringStatus($order_recurring_id, ModelExtensionPaymentSquareup::RECURRING_CANCELLED);

                if (version_compare(VERSION, '2.0.0.0', '<')) {
                    $this->load->model('sale/order');

                    $order_info = $this->model_sale_order->getOrder($recurring_info['order_id']);

                    $this->model_sale_order->addOrderHistory($recurring_info['order_id'], array(
                        'order_status_id' => $order_info['order_status_id'],
                        'comment' => $this->language->get('text_order_history_cancel'),
                        'notify' => true
                    ));
                }

                $json['success'] = $this->language->get('text_canceled_success');
            } else {
                $json['error'] = $this->language->get('error_not_found');
            }
        }

        if (version_compare(VERSION, '2.0.0.0', '<')) {
            if (!empty($json['success'])) {
                $this->session->data['success'] = $json['success'];
            }

            $this->responseRedirect($this->url->link('sale/recurring/info', 'order_recurring_id=' . $this->request->get['order_recurring_id'] . '&token=' . $this->request->get['token'], $this->getUrlSsl()));
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function setAdminLink(&$route, &$data, &$template) {
        if (!$this->config->has('squareup_status')) {
            return;
        }

        if (!$this->user->hasPermission('access', $this->config->get('squareup_imodule_route_payment'))) {
            return;
        }

        foreach ($data['menus'] as &$menu) {
            if ($menu['id'] == 'menu-extension') {
                $menu['children'][] = array(
                    'name' => 'Square',
                    'children' => array(),
                    'href' => $this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl())
                );

                return;
            }
        }
    }

    public function setAccessTokenAlert(&$route, &$dashboard_data, &$template = null) {
        $dashboard_data['footer'] = $this->access_token_alert_event() . $dashboard_data['footer'];
    }

    public function access_token_alert_event() {
        if (!$this->config->has('squareup_status')) {
            return "";
        }

        if (!$this->user->hasPermission('access', $this->imodule_route_payment)) {
            return "";
        }

        $data['squareup_url'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/access_token_alert', 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, 'UTF-8');

        return $this->loadView($this->imodule_route_payment . '_access_token_alert_event', $data);
    }

    public function access_token_alert() {
        if ($this->config->get('squareup_access_token') && $this->config->get('squareup_access_token_expires')) {
            $this->setAccessTokenAlerts($this->config->get('squareup_access_token_expires'));

            $data['alerts'] = $this->pullAlerts();

            $this->clearAlerts();

            $this->response->setOutput($this->loadView($this->imodule_route_payment . '_access_token_alert', $data));
        }
    }

    protected function setAccessTokenAlerts($expires) {
        $expiration_time = date_create_from_format('Y-m-d\TH:i:s\Z', $expires);
        $now = date_create();

        $delta = $expiration_time->getTimestamp() - $now->getTimestamp();
        $expiration_date_formatted = $expiration_time->format('l, F jS, Y h:i:s A, e');

        if ($delta < 0) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => sprintf($this->language->get('text_token_expired'), $this->url->link($this->imodule_route_payment . '/refresh_token', 'token=' . $this->session->data['token'], $this->getUrlSsl()))
            ));
        } else if ($delta < (5 * 24 * 60 * 60)) { // token is valid, just about to expire
            $this->pushAlert(array(
                'type' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => sprintf($this->language->get('text_token_expiry_warning'), $expiration_date_formatted, $this->url->link($this->imodule_route_payment . '/refresh_token', 'token=' . $this->session->data['token'], $this->getUrlSsl()))
            ));
        }

        return $expiration_date_formatted;
    }

    protected function inLocations($locations, $location_id) {
        foreach ($locations as $location) {
            if ($location['id'] == $location_id) {
                return true;
            }
        }

        return false;
    }

    public function setProductWarning(&$route, &$product_form_data, &$output) {
        $product_form_data['footer'] = $this->product_form_warning() . $product_form_data['footer'];
    }

    public function product_form_warning() {
        if (!$this->config->has('squareup_status')) {
            return "";
        }

        if (!$this->config->has('squareup_inventory_sync') || $this->config->get('squareup_inventory_sync') == 'none') {
            return "";
        }

        if (empty($this->request->get['product_id'])) {
            return "";
        }

        $this->loadLibrary('vendor/isenselabs/squareup');

        if (null === $item_id = $this->imodule_model_payment->getItemId($this->request->get['product_id'])) {
            return "";
        }

        $url_extension = html_entity_decode($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, 'UTF-8');
        $url_dashboard = $this->squareup_api->itemLink($item_id);

        $data['text'] = sprintf($this->language->get('text_product_warning'), $url_extension, $url_dashboard);

        return $this->loadView($this->imodule_route_payment . '_product_form_warning', $data);
    }

    /*
     * This is an event handler triggered once per admin panel request because admin directory name may get modified while a CRON job is registered. This method sets squareup_admin_url_transaction and squareup_admin_url_settings required by the Square catalog method ControllerExtensionPaymentSquareup::info()
     */

    public function setAdminURL() {
        // In case user is not yet defined, do nothing
        if (!$this->registry->has('user')) {
            return;
        }

        // We need this to run only once per request
        if ($this->registry->has('event')) {
            $this->event->unregister('controller/*/after', $this->imodule_route_payment . '/setAdminURL');
        }

        // No need to run it for non-logged-in users
        if (!$this->user->isLogged()) {
            return;
        }

        $this->load->model('setting/setting');
        $this->load->model($this->imodule_route_payment);

        $this->model_setting_setting->editSettingValue('squareup', 'squareup_admin_url_transaction', $this->imodule_model_payment->getAdminURLTransaction());
        $this->model_setting_setting->editSettingValue('squareup', 'squareup_admin_url_settings', $this->imodule_model_payment->getAdminURLSettings());
    }

    // A deprecated (public set to private) method which should be used when we implement a Catalog sync in the direction Square > OpenCart
    //public function geoZone() {
    private function geoZone() {
        // $this->load->language($this->imodule_route_payment);

        $this->load->model($this->imodule_route_payment);
        $this->load->model('setting/setting');

        $action = isset($this->request->get['action']) ? $this->request->get['action'] : 'skip';

        switch ($action) {
            case 'confirm' : {
                // Confirm
                if ($this->user->hasPermission('modify', 'localisation/geo_zone')) {
                    $this->imodule_model_payment->setupGeoZones();

                    $this->session->data['success'] = $this->language->get('text_success_geo_zone');
                } else {
                    $this->pushAlert($this->language->get('error_permission_geo_zone'));
                }
            } break;
            default : {
                // Skip
                if ($this->user->hasPermission('modify', $this->imodule_route_payment)) {
                    $previous_setting = $this->model_setting_setting->getSetting('squareup');

                    $skip_geo_zones_setting = array(
                        'squareup_skip_geo_zones' => 1
                    );

                    $this->model_setting_setting->editSetting('squareup', array_merge($previous_setting, $skip_geo_zones_setting));
                } else {
                    $this->pushAlert($this->language->get('error_permission'));
                }
            } break;
        }

        $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
    }

    public function taxRate() {
        // $this->load->language($this->imodule_route_payment);

        $json = array();

        if (!$this->user->hasPermission('modify', 'localisation/tax_rate')) {
            $json['error'] = $this->language->get('error_permission_tax_rate');
        } else {
            $this->load->model('localisation/tax_rate');
            $this->load->model($this->imodule_route_payment);

            foreach ($this->request->post['tax_rate'] as $tax_rate_id => $geo_zone_id) {
                if (empty($geo_zone_id)) {
                    $json['error_tax_rate_id'][] = $tax_rate_id;
                } else {
                    $this->imodule_model_payment->updateTaxRateGeoZone($tax_rate_id, $geo_zone_id);
                }
            }

            if (!empty($json['error_tax_rate_id'])) {
                $json['error'] = $this->language->get('error_tax_rate');
            } else {
                $json['success'] = true;
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    // A deprecated (public set to private) method which should be used when we implement a Catalog sync in the direction Square > OpenCart
    // public function syncModal() {
    private function syncModal() {
        $json = array();

        // $this->load->language($this->imodule_route_payment);

        $this->load->model($this->imodule_route_payment);

        $this->loadLibrary('vendor/isenselabs/squareup');

        $data = $this->imodule_language_payment;

        $oc_product_count = $this->imodule_model_payment->countOpenCartProducts();
        $square_product_count = $this->squareup_api->countSquareItems();

        $location_name = '';

        foreach ($this->config->get('squareup_locations') as $location) {
            if ($location['id'] == $this->config->get('squareup_location_id')) {
                $location_name = $location['name'];
            }
        }

        $square_delta = $square_product_count - $oc_product_count;
        $oc_delta = $oc_product_count - $square_product_count;

        $data['text_sync_configure_intro'] = sprintf($this->language->get('text_sync_configure_intro'), $location_name, $square_product_count, $oc_product_count);

        if ($square_delta >= 0) {
            $data['text_sync_configure_option_1'] = sprintf($this->language->get('text_sync_configure_option_1_unassign'), $square_delta);
        } else {
            $data['text_sync_configure_option_1'] = sprintf($this->language->get('text_sync_configure_option_1_assign'), abs($square_delta));
        }

        $data['text_sync_configure_option_2'] = sprintf($this->language->get('text_sync_configure_option_2'), $square_product_count);

        if ($oc_delta >= 0) {
            $data['text_sync_configure_option_3'] = sprintf($this->language->get('text_sync_configure_option_3_unassign'), $oc_delta);
        } else {
            $data['text_sync_configure_option_3'] = sprintf($this->language->get('text_sync_configure_option_3_assign'), abs($oc_delta));
        }

        $data['text_sync_configure_option_4'] = sprintf($this->language->get('text_sync_configure_option_4'), $oc_product_count);

        $direction = $this->getPostValue('squareup_sync_source');

        if ($direction == 'opencart') {
            $data['selected'] = '2';
        } else {
            $data['selected'] = '4';
        }

        $json['html'] = $this->loadView($this->imodule_route_payment . '_sync_modal', $data);
        $json['already_synced'] = (bool)$this->config->get('squareup_initial_sync');

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function download_sync_log() {
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Description: File Transfer');
        header('Content-Type: plain/text');
        header('Content-Disposition: attachment; filename="squareup_sync_log_' . date('Y-m-d_H-i-s', time()) . '.txt"');
        header('Content-Transfer-Encoding: binary');

        $diff_id = $this->config->get('squareup_last_sync_diff_id');

        if ($diff_id) {
            $num_rows = 0;
            $step = 1000;
            $page = 0;

            do {
                $sql = "SELECT * FROM `" . DB_PREFIX . "squareup_diff` WHERE diff_id='" . $this->db->escape($diff_id) . "' ORDER BY squareup_diff_id ASC LIMIT " . ($page * $step) . "," . $step;

                $result = $this->db->query($sql);

                $num_rows = $result->num_rows;

                if ($num_rows > 0) {
                    foreach ($result->rows as $row) {
                        var_export($row);
                        echo PHP_EOL;
                    }
                }

                $page++;
            } while ($num_rows > 0);
        }

        exit;
    }

    public function url_check_cron_status() {
        $json = array();

        $this->load->config('vendor/isenselabs/squareup/cron');

        $this->load->model('setting/setting');

        // $this->load->language($this->imodule_route_payment);

        $setting = $this->model_setting_setting->getSetting('squareup');

        $config = new Config();

        foreach ($setting as $key => $value) {
            $config->set($key, $value);
        }

        $json['on_demand_status'] = (bool)$config->get('squareup_cron_is_on_demand') || (bool)$config->get('squareup_cron_is_running');

        $cron_status_text = $this->language->get('text_na');

        if ((bool)$config->get('squareup_cron_is_running')) {
            $time = date('l, F jS, Y h:i:s A, e', $config->get('squareup_cron_started_at'));

            $cron_status_text = sprintf($this->language->get('text_cron_status_text_running'), $time);
        } else {
            if ($config->has('squareup_cron_ended_type')) {
                if ((bool)$config->get('squareup_cron_is_on_demand')) {
                    $time = date('l, F jS, Y h:i:s A, e', time());

                    $cron_status_text = sprintf($this->language->get('text_cron_status_text_queued'), $time);
                } else {
                    $time = date('l, F jS, Y h:i:s A, e', $config->get('squareup_cron_ended_at'));

                    switch ($config->get('squareup_cron_ended_type')) {
                        case self::CRON_ENDED_FLAG_COMPLETE : $cron_status_text = sprintf($this->language->get('text_cron_status_text_completed'), $time);
                            break;
                        case self::CRON_ENDED_FLAG_TIMEOUT : $cron_status_text = sprintf($this->language->get('text_cron_status_text_timed_out'), $time);
                            break;
                        case self::CRON_ENDED_FLAG_ERROR : $cron_status_text = sprintf($this->language->get('text_cron_status_text_failed'), $time);
                            break;
                    }
                }
            }
        }

        $json['cron_status_text'] = $cron_status_text;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function on_demand_cron() {
        // $this->load->language($this->imodule_route_payment);

        if (!$this->user->hasPermission('modify', $this->imodule_route_payment)) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('error_permission')
            ));
        } else if ($this->config->get('squareup_cron_is_running')) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('error_task_running')
            ));
        } else {
            $this->load->model('setting/setting');

            $setting = $this->model_setting_setting->getSetting('squareup');

            $setting['squareup_cron_is_on_demand'] = '1';

            $this->model_setting_setting->editSetting('squareup', $setting);
        }

        $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()));
    }

    public function order_info_link() {
        $transaction_info = $this->imodule_model_payment->getTransactionByOrderId($this->request->get['order_id']);

        if (empty($transaction_info)) {
            $this->response->setOutput('');
        } else {
            $data['text_manage'] = $this->language->get('text_manage');
            $data['text_manage_tooltip'] = $this->language->get('text_manage_tooltip');
            $data['transaction_url'] = $this->url->link($this->imodule_route_payment . '/transaction_info', 'token=' . $this->session->data['token'] . '&squareup_transaction_id=' . $transaction_info['squareup_transaction_id'], $this->getUrlSsl());

            $this->response->setOutput($this->loadView($this->imodule_route_payment . '_order_info_link', $data));
        }
    }

    protected function getPostValue($key) {
        if (isset($this->request->post[$key])) {
            return $this->request->post[$key];
        }

        return null;
    }

    protected function showGeoZones() {
        $this->document->setTitle($this->language->get('heading_title') . ' - ' . $this->language->get('text_configure_geo_zone'));

        $data = $this->imodule_language_payment;

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link($this->imodule_extension_route, 'token=' . $this->session->data['token'] . '&type=payment', $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        if (isset($this->request->get['show_geo_zone'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('text_configure_geo_zone'),
                'href' => $this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'] . '&show_geo_zone=1', $this->getUrlSsl())
            );

            $data['cancel'] = html_entity_decode($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        } else {
            $data['cancel'] = html_entity_decode($this->url->link($this->imodule_extension_route, 'token=' . $this->session->data['token'] . '&type=payment', $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        }

        $data['confirm'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/geoZone', 'token=' . $this->session->data['token'] . '&action=confirm', $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['skip'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/geoZone', 'token=' . $this->session->data['token'] . '&action=skip', $this->getUrlSsl()), ENT_QUOTES, "UTF-8");

        $this->load->model('localisation/country');

        $predefined_countries = $this->imodule_model_payment->getPredefinedCountries();

        $country_info = $this->model_localisation_country->getCountry($this->config->get('config_country_id'));

        $data['store_country'] = !in_array($country_info['iso_code_3'], $predefined_countries);

        $data['text_zone_store_country'] = sprintf($this->language->get('text_zone_store_country'), $country_info['name']);

        $this->loadAdminChildren($data);

        $this->response->setOutput($this->loadView($this->imodule_route_payment . '_geo_zone', $data));
    }

    protected function showForm() {
        $this->load->config('vendor/isenselabs/squareup/cron');

        $data = $this->imodule_language_payment;

        if ($this->isSSL()) {
            $server = HTTPS_SERVER;
        } else {
            $server = HTTP_SERVER;
        }

        $previous_setting = $this->model_setting_setting->getSetting('squareup');

        try {
            unset($previous_setting['squareup_sandbox_locations']);
            unset($previous_setting['squareup_sandbox_location_id']);

            if ($this->config->get('squareup_access_token')) {
                $first_location_id = null;

                if (false === $locations = $this->squareup_api->verifyToken($this->config->get('squareup_access_token'), $first_location_id)) {
                    unset($previous_setting['squareup_status']);
                    unset($previous_setting['squareup_merchant_id']);
                    unset($previous_setting['squareup_initial_sync']);
                    unset($previous_setting['squareup_merchant_name']);
                    unset($previous_setting['squareup_access_token']);
                    unset($previous_setting['squareup_access_token_expires']);
                    unset($previous_setting['squareup_locations']);
                    unset($previous_setting['squareup_location_id']);

                    $this->config->set('squareup_merchant_id', null);
                    $this->config->set('squareup_initial_sync', null);
                    $this->config->set('squareup_status', 0);
                    $this->config->set('squareup_apple_pay_registered', 0);
                } else {
                    $previous_setting['squareup_locations'] = $locations;

                    if (empty($previous_setting['squareup_location_id']) || (!empty($first_location_id) && !$this->inLocations($locations, $previous_setting['squareup_location_id']))) {
                        $previous_setting['squareup_location_id'] = $first_location_id;
                    }

                    if (!$this->config->get('squareup_apple_pay_registered')) {
                        if (null !== $domain = $this->imodule_model_payment->setupApplePayDomainVerificationFile()) {
                            $result = $this->squareup_api->registerApplePayDomain($domain);

                            if ($result == 'VERIFIED') {
                                $previous_setting['squareup_apple_pay_registered'] = 1;
                            }
                        }
                    }

                    $previous_setting['squareup_merchant_name'] = $this->squareup_api->getMerchantName();
                }
            }

            $this->model_setting_setting->editSetting('squareup', $previous_setting);
        } catch (vendor\isenselabs\Squareup\Exception\Api $e) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => sprintf($this->language->get('text_location_error'), $e->getMessage())
            ));
        }

        $previous_config = new Config();

        foreach ($previous_setting as $key => $value) {
            $previous_config->set($key, $value);
        }

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
            if (isset($this->request->post['squareup_initial_sync_type'])) {
                $type = $this->request->post['squareup_initial_sync_type'];

                if (in_array($type, array('1', '2'))) {
                    $this->request->post['squareup_sync_source'] = 'opencart';
                } else {
                    $this->request->post['squareup_sync_source'] = 'square';
                }
            }

            $previous_currency = $this->imodule_model_payment->getLocationCurrency($previous_config->get('squareup_locations'), $previous_config->get('squareup_location_id'));

            if ($previous_currency != $this->config->get('config_currency') && $this->request->post['squareup_sync_source'] != 'none') {
                $this->pushAlert(array(
                    'type' => 'danger',
                    'type_oc15' => 'warning',
                    'icon' => 'exclamation-circle',
                    'text' => sprintf($this->language->get('text_currency_different'), $this->config->get('config_currency'), $previous_currency)
                ));

                $this->request->post['squareup_sync_source'] = 'none';
            }

            $new_settings = array_merge($previous_setting, $this->request->post);

            if (isset($new_settings['squareup_location_id'])) {
                try {
                    $this->squareup_api->updateWebhookPermissions($new_settings['squareup_location_id'], array(
                        'INVENTORY_UPDATED'
                    ));
                } catch (vendor\isenselabs\Squareup\Exception\Api $e) {
                    $this->pushAlert(array(
                        'type' => 'danger',
                        'type_oc15' => 'warning',
                        'icon' => 'exclamation-circle',
                        'text' => sprintf($this->language->get('text_webhook_error'), $e->getMessage())
                    ));
                }
            }

            $this->model_setting_setting->editSetting('squareup', $new_settings);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->responseRedirect($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'] . '&type=payment', $this->getUrlSsl()));
        } else {
            if (!$previous_config->get('squareup_cron_acknowledge')) {
                $this->pushAlert(array(
                    'type' => 'warning',
                    'icon' => 'exclamation-circle',
                    'text' => $this->language->get('text_warning_cron')
                ));
            }
        }

        $this->document->setTitle($this->language->get('heading_title'));

        $data['error_status']                       = $this->getValidationError('status');
        $data['error_display_name']                 = $this->getValidationError('display_name');
        $data['error_client_id']                    = $this->getValidationError('client_id');
        $data['error_client_secret']                = $this->getValidationError('client_secret');
        $data['error_delay_capture']                = $this->getValidationError('delay_capture');
        $data['error_location']                     = $this->getValidationError('location');
        $data['error_cron_email']                   = $this->getValidationError('cron_email');
        $data['error_cron_acknowledge']             = $this->getValidationError('cron_acknowledge');
        $data['error_status_authorized']            = $this->getValidationError('status_authorized');
        $data['error_status_captured']              = $this->getValidationError('status_captured');
        $data['error_status_voided']                = $this->getValidationError('status_voided');
        $data['error_status_failed']                = $this->getValidationError('status_failed');
        $data['error_status_partially_refunded']    = $this->getValidationError('status_partially_refunded');
        $data['error_status_fully_refunded']        = $this->getValidationError('status_fully_refunded');
        $data['error_cron_standard_period']         = $this->getValidationError('cron_standard_period');

        $data['order_status_settings_hidden'] =
            (bool)$this->getSettingValue('squareup_status_authorized') &&
            (bool)$this->getSettingValue('squareup_status_captured') &&
            (bool)$this->getSettingValue('squareup_status_voided') &&
            (bool)$this->getSettingValue('squareup_status_failed') &&
            (bool)$this->getSettingValue('squareup_status_partially_refunded') &&
            (bool)$this->getSettingValue('squareup_status_fully_refunded');

        $data['squareup_status']                    = $this->getSettingValue('squareup_status');
        $data['squareup_status_authorized']         = $this->getSettingValue('squareup_status_authorized', $this->imodule_model_payment->inferOrderStatusId('processing'));
        $data['squareup_status_captured']           = $this->getSettingValue('squareup_status_captured', $this->imodule_model_payment->inferOrderStatusId('processed'));
        $data['squareup_status_voided']             = $this->getSettingValue('squareup_status_voided', $this->imodule_model_payment->inferOrderStatusId('void'));
        $data['squareup_status_failed']             = $this->getSettingValue('squareup_status_failed', $this->imodule_model_payment->inferOrderStatusId('fail'));
        $data['squareup_status_partially_refunded'] = $this->getSettingValue('squareup_status_partially_refunded', $this->imodule_model_payment->inferOrderStatusId('refund'));
        $data['squareup_status_fully_refunded']     = $this->getSettingValue('squareup_status_fully_refunded', $this->imodule_model_payment->inferOrderStatusId('refund'));

        $data['squareup_display_name']              = $this->getSettingValue('squareup_display_name');
        $data['squareup_client_id']                 = $this->getSettingValue('squareup_client_id');
        $data['squareup_client_secret']             = $this->getSettingValue('squareup_client_secret');
        $data['squareup_webhook_signature']         = $this->getSettingValue('squareup_webhook_signature');
        $data['squareup_debug']                     = $this->getSettingValue('squareup_debug');
        $data['squareup_guest']                     = $this->getSettingValue('squareup_guest');
        $data['squareup_sort_order']                = $this->getSettingValue('squareup_sort_order');
        $data['squareup_total']                     = $this->getSettingValue('squareup_total', '1.00');
        $data['squareup_geo_zone_id']               = $this->getSettingValue('squareup_geo_zone_id');
        $data['squareup_locations']                 = $this->getSettingValue('squareup_locations', $previous_config->get('squareup_locations'));
        $data['squareup_location_id']               = $this->getSettingValue('squareup_location_id');
        $data['squareup_delay_capture']             = $this->getSettingValue('squareup_delay_capture');
        $data['squareup_recurring_status']          = $this->getSettingValue('squareup_recurring_status');
        $data['squareup_cron_email_status']         = $this->getSettingValue('squareup_cron_email_status');
        $data['squareup_cron_email']                = $this->getSettingValue('squareup_cron_email', $this->config->get('config_email'));
        $data['squareup_cron_token']                = $this->getSettingValue('squareup_cron_token');
        $data['squareup_cron_acknowledge']          = $this->getSettingValue('squareup_cron_acknowledge', null, true);
        $data['squareup_notify_recurring_success']  = $this->getSettingValue('squareup_notify_recurring_success');
        $data['squareup_notify_recurring_fail']     = $this->getSettingValue('squareup_notify_recurring_fail');
        $data['squareup_merchant_id']               = $this->getSettingValue('squareup_merchant_id', $previous_config->get('squareup_merchant_id'));
        $data['squareup_merchant_name']             = $this->getSettingValue('squareup_merchant_name', $previous_config->get('squareup_merchant_name'));
        $data['squareup_admin_url_transaction']     = $this->getSettingValue('squareup_admin_url_transaction', $this->imodule_model_payment->getAdminURLTransaction());
        $data['squareup_admin_url_settings']        = $this->getSettingValue('squareup_admin_url_settings', $this->imodule_model_payment->getAdminURLSettings());
        $data['squareup_sync_source']               = $this->getSettingValue('squareup_sync_source');
        $data['squareup_icon_status']               = $this->getSettingValue('squareup_icon_status', '1');
        $data['squareup_accepted_cards_status']     = $this->getSettingValue('squareup_accepted_cards_status');
        $data['squareup_inventory_sync']            = $this->getSettingValue('squareup_inventory_sync');
        $data['squareup_cron_standard_period']      = $this->getSettingValue('squareup_cron_standard_period', ((int)$this->config->get('squareup_cron_standard_period_default') / 60));
        $data['squareup_ad_hoc_sync']               = $this->getSettingValue('squareup_ad_hoc_sync', '1');

        $data['max_standard_period'] = (int)$this->config->get('squareup_cron_standard_period_default') / 60;

        // Deprecated because we do not have the sync direction from Square to OpenCart.
        $data['initial_sync_not_performed']                 = false; //!$this->config->get('squareup_initial_sync');

        $data['access_token_expires_time'] = $this->language->get('text_na');

        if ($previous_config->get('squareup_access_token') && $previous_config->get('squareup_access_token_expires')) {
            $data['access_token_expires_time'] = $this->setAccessTokenAlerts($previous_config->get('squareup_access_token_expires'));
        } else if ($previous_config->get('squareup_client_id')) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('text_token_revoked')
            ));
        }

        $data['squareup_redirect_uri'] = str_replace('&amp;', '&', $this->url->link($this->imodule_route_payment . '/oauth_callback', '', $this->getUrlSsl()));
        $data['squareup_refresh_link'] = $this->url->link($this->imodule_route_payment . '/refresh_token', 'token=' . $this->session->data['token'], $this->getUrlSsl());

        if (!$this->config->get('squareup_status')) {
            $this->pushAlert(array(
                'type' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('text_extension_disabled'),
                'non_dismissable' => true
            ));
        }

        if (isset($this->error['warning'])) {
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->error['warning']
            ));
        }

        // Insert success message from the session
        if (isset($this->session->data['success'])) {
            $this->pushAlert(array(
                'type' => 'success',
                'icon' => 'exclamation-circle',
                'text' => $this->session->data['success']
            ));

            unset($this->session->data['success']);
        }

        if ($this->isSSL()) {
            // Push the SSL reminder alert
            $this->pushAlert(array(
                'type' => 'info',
                'icon' => 'lock',
                'text' => $this->language->get('text_notification_ssl')
            ));
        } else {
            // Push the SSL reminder alert
            $this->pushAlert(array(
                'type' => 'danger',
                'type_oc15' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('error_no_ssl')
            ));
        }

        if ($this->config->get('squareup_access_token')) {
            $this->pushAlert(array(
                'type' => 'info',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('text_enable_payment')
            ));
        }

        if ($this->config->get('squareup_delay_capture')) {
            $this->pushAlert(array(
                'type' => 'warning',
                'icon' => 'exclamation-circle',
                'text' => $this->language->get('text_auth_voided_6_days')
            ));
        }

        if ($this->config->get('squareup_status') && !$this->squareup_api->getLocationCurrency(false)) {
            $current_location_currency = $this->imodule_model_payment->getLocationCurrency($previous_config->get('squareup_locations'), $previous_config->get('squareup_location_id'));
            $url_edit_currencies = $this->url->link('localisation/currency', 'token=' . $this->session->data['token'], $this->getUrlSsl());

            $this->pushAlert(array(
                'type' => 'danger',
                'icon' => 'exclamation-circle',
                'text' => sprintf($this->language->get('error_currency_unavailable'), $current_location_currency, $url_edit_currencies)
            ));
        }

        $tabs = array(
            'tab-transaction',
            'tab-setting',
            'tab-recurring',
            'tab-cron'
        );

        if (isset($this->request->get['tab']) && in_array($this->request->get['tab'], $tabs)) {
            $data['tab'] = $this->request->get['tab'];
        } else if ($this->error) {
            $data['tab'] = 'tab-setting';
        } else {
            $data['tab'] = $tabs[1];
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link($this->imodule_extension_route, 'token=' . $this->session->data['token'] . '&type=payment', $this->getUrlSsl())
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl())
        );

        $data['heading_title'] = $this->language->get('heading_title') . ' ' . $this->config->get('squareup_imodule_version');

        $data['action'] = html_entity_decode($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['cancel'] = html_entity_decode($this->url->link($this->imodule_extension_route, 'token=' . $this->session->data['token'] . '&type=payment', $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['connect'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/connect', 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");

        // Deprecated - should be used when we implement a Catalog sync in the direction Square > OpenCart
        $data['setup_geo_zones'] = html_entity_decode($this->url->link($this->imodule_route_payment, 'token=' . $this->session->data['token'] . '&show_geo_zone=1', $this->getUrlSsl()), ENT_QUOTES, "UTF-8");

        $data['on_demand_cron'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/on_demand_cron', 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['url_list_transactions'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/transactions', 'token=' . $this->session->data['token'] . '&page={PAGE}', $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['url_tax_rate'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/taxRate', 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['url_sync_modal_options'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/syncModal', 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['url_check_cron_status'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/url_check_cron_status', 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");
        $data['url_download_sync_log'] = html_entity_decode($this->url->link($this->imodule_route_payment . '/download_sync_log', 'token=' . $this->session->data['token'], $this->getUrlSsl()), ENT_QUOTES, "UTF-8");

        $data['help'] = 'http://docs.isenselabs.com/square';
        $data['url_video_help'] = 'https://www.youtube.com/watch?v=4sSSKwA3KrM';
        $data['url_integration_settings_help'] = 'http://docs.isenselabs.com/square/integration_settings';

        $this->load->model('localisation/language');
        $data['languages'] = array();
        foreach ($this->model_localisation_language->getLanguages() as $language) {
            if (version_compare(VERSION, '2.2.0.0', '>=')) {
                $image = 'language/' . $language['code'] . '/'. $language['code'] . '.png';
            } else {
                $image = preg_replace('~^https?:~', '' , HTTP_CATALOG) . 'image/flags/' . $language['image'];
            }

            $data['languages'][] = array(
                'language_id' => $language['language_id'],
                'name' => $language['name'] . ($language['code'] == $this->config->get('config_language') ? $this->language->get('text_default') : ''),
                'image' => $image
            );
        }

        $this->load->model('localisation/order_status');
        $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->load->model('localisation/geo_zone');
        $data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

        // Deprecated - should be used when we implement a Catalog sync in the direction Square > OpenCart
        $data['can_modify_geo_zones'] = false; // $this->user->hasPermission('modify', 'localisation/geo_zone');

        $data['squareup_cron_command'] = 'export CUSTOM_SERVER_NAME=' . parse_url($server, PHP_URL_HOST) . '; export CUSTOM_SERVER_PORT=443; export SQUARE_CRON=1; export SQUARE_ROUTE=' . $this->config->get('squareup_imodule_route_payment') . '/cron; ' . PHP_BINDIR . '/php -d memory_limit=512M -d session.save_path=' . session_save_path() . ' ' . DIR_SYSTEM . 'library/vendor/isenselabs/squareup/cron.php > /dev/null 2> /dev/null';

        if (!$this->config->get('squareup_cron_token')) {
            $data['squareup_cron_token'] = md5(mt_rand());
        }

        $data['squareup_cron_url'] = 'https://' . parse_url($server, PHP_URL_HOST) . dirname(parse_url($server, PHP_URL_PATH)) . '/index.php?route=' . $this->imodule_route_payment . '/cron&cron_token={CRON_TOKEN}';

        $data['squareup_webhook_url'] = 'https://' . parse_url($server, PHP_URL_HOST) . dirname(parse_url($server, PHP_URL_PATH)) . '/index.php?route=' . $this->imodule_route_payment . '/webhook';

        // API login
        $data['button_ip_add'] = $this->language->get('button_ip_add');
        $data['error_warning'] = '';
        $data['catalog'] = $this->isSSL() ? HTTPS_CATALOG : HTTP_CATALOG;
        $data['token'] = $this->session->data['token'];

        $this->loadApi($data);

        // Tax rate popup
        $new_tax_rates = $this->imodule_model_payment->getNewTaxRates();
        $data['new_tax_rates'] = $new_tax_rates;
        $data['has_new_tax_rates'] = count($new_tax_rates) > 0;

        $this->loadAdminChildren($data);

        $data['alerts'] = $this->pullAlerts();

        $this->clearAlerts();

        $this->response->setOutput($this->loadView($this->imodule_route_payment, $data));
    }

    protected function validate() {
        if (!$this->user->hasPermission('modify', $this->imodule_route_payment)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['squareup_status'])) {
            return true;
        }

        if ($this->config->get('squareup_merchant_id') && !$this->config->get('squareup_locations')) {
            $this->error['warning'] = $this->language->get('text_no_appropriate_locations_warning');
        }

        if ($this->config->get('squareup_locations') && isset($this->request->post['squareup_location_id']) && !in_array($this->request->post['squareup_location_id'], array_map(function($location) {
            return $location['id'];
        }, $this->config->get('squareup_locations')))) {
            $this->error['location'] = $this->language->get('error_no_location_selected');
        }

        if (!empty($this->request->post['squareup_cron_email_status'])) {
            if (!filter_var($this->request->post['squareup_cron_email'], FILTER_VALIDATE_EMAIL)) {
                $this->error['cron_email'] = $this->language->get('error_invalid_email');
            }
        }

        if (empty($this->request->post['squareup_cron_acknowledge'])) {
            $this->error['cron_acknowledge'] = $this->language->get('error_cron_acknowledge');
        }

        if (empty($this->request->post['squareup_status_authorized'])) {
            $this->error['status_authorized'] = $this->language->get('error_status_not_set');
        }

        if (empty($this->request->post['squareup_status_captured'])) {
            $this->error['status_captured'] = $this->language->get('error_status_not_set');
        }

        if (empty($this->request->post['squareup_status_voided'])) {
            $this->error['status_voided'] = $this->language->get('error_status_not_set');
        }

        if (empty($this->request->post['squareup_status_failed'])) {
            $this->error['status_failed'] = $this->language->get('error_status_not_set');
        }

        if (empty($this->request->post['squareup_status_partially_refunded'])) {
            $this->error['status_partially_refunded'] = $this->language->get('error_status_not_set');
        }

        if (empty($this->request->post['squareup_status_fully_refunded'])) {
            $this->error['status_fully_refunded'] = $this->language->get('error_status_not_set');
        }

        $this->load->config('vendor/isenselabs/squareup/cron');

        $max_period = $this->config->get('squareup_cron_standard_period_default') / 60;

        if (empty($this->request->post['squareup_cron_standard_period']) || (int)$this->request->post['squareup_cron_standard_period'] < 180 || (int)$this->request->post['squareup_cron_standard_period'] > $max_period) {
            $this->error['cron_standard_period'] = sprintf($this->language->get('error_cron_standard_period'), 180, $max_period);
        }

        if ($this->error && empty($this->error['warning'])) {
            $this->error['warning'] = $this->language->get('error_form');
        }

        return !$this->error;
    }

    protected function transactionAction($callback) {
        // $this->load->language($this->imodule_route_payment);

        $this->load->model($this->imodule_route_payment);

        $this->loadLibrary('vendor/isenselabs/squareup');

        $json = array();

        if (!$this->user->hasPermission('modify', $this->imodule_route_payment)) {
            $json['error'] = $this->language->get('error_permission');
        }

        if (isset($this->request->get['squareup_transaction_id'])) {
            $squareup_transaction_id = $this->request->get['squareup_transaction_id'];
        } else {
            $squareup_transaction_id = 0;
        }

        $transaction_info = $this->imodule_model_payment->getTransaction($squareup_transaction_id);

        if (empty($transaction_info)) {
            $json['error'] = $this->language->get('error_transaction_missing');
        } else if (empty($json['error'])) {
            try {
                $callback($transaction_info, $json);
            } catch (vendor\isenselabs\Squareup\Exception\Api $e) {
                $json['error'] = $e->getMessage();
            }
        }

        if (isset($this->request->get['preserve_alert'])) {
            if (!empty($json['error'])) {
                $this->pushAlert(array(
                    'type' => 'danger',
                    'type_oc15' => 'warning',
                    'icon' => 'exclamation-circle',
                    'text' => $json['error']
                ));
            }

            if (!empty($json['success'])) {
                $this->pushAlert(array(
                    'type' => 'success',
                    'icon' => 'exclamation-circle',
                    'text' => $json['success']
                ));
            }
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    protected function pushAlert($alert) {
        $this->session->data['squareup_alerts'][] = $alert;
    }

    protected function pullAlerts() {
        if (isset($this->session->data['squareup_alerts'])) {
            return $this->session->data['squareup_alerts'];
        } else {
            return array();
        }
    }

    protected function clearAlerts() {
        unset($this->session->data['squareup_alerts']);
    }

    protected function getSettingValue($key, $default = null, $checkbox = false) {
        if ($checkbox) {
            if ($this->request->server['REQUEST_METHOD'] == 'POST' && !isset($this->request->post[$key])) {
                return $default;
            } else {
                return $this->config->get($key);
            }
        }

        if (isset($this->request->post[$key])) {
            return $this->request->post[$key];
        } else if ($this->config->has($key)) {
            return $this->config->get($key);
        } else {
            return $default;
        }
    }

    protected function getValidationError($key) {
        if (isset($this->error[$key])) {
            return $this->error[$key];
        } else {
            return '';
        }
    }

    protected function loadApi(&$data) {
        $data['old_api'] = true;
        $data['is_oc15'] = version_compare(VERSION, '2.0.0.0', '<');

        if (version_compare(VERSION, '2.1.0.0', '>=')) {
            $this->load->model('user/api');

            $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

            if ($api_info) {
                $data['api_id'] = $api_info['api_id'];
                $data['api_key'] = $api_info['key'];
                $data['api_ip'] = $this->request->server['REMOTE_ADDR'];
            } else {
                $data['api_id'] = '';
                $data['api_key'] = '';
                $data['api_ip'] = '';
            }

            $data['old_api'] = false;
        } else if (version_compare(VERSION, '2.0.0.0', '>=')) {
            // Unset any past sessions this page date_added for the api to work.
            unset($this->session->data['cookie']);

            // Set up the API session
            if ($this->user->hasPermission('modify', $this->imodule_route_payment)) {
                $this->load->model('user/api');

                $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));

                if ($api_info) {
                    $curl = curl_init();

                    // Set SSL if required
                    if (substr(HTTPS_CATALOG, 0, 5) == 'https') {
                        curl_setopt($curl, CURLOPT_PORT, 443);
                    }

                    curl_setopt($curl, CURLOPT_HEADER, false);
                    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
                    curl_setopt($curl, CURLOPT_USERAGENT, $this->request->server['HTTP_USER_AGENT']);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_FORBID_REUSE, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_URL, HTTPS_CATALOG . 'index.php?route=api/login');
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($api_info));

                    $json = curl_exec($curl);

                    if (!$json) {
                        $data['error_warning'] = sprintf($this->language->get('error_curl'), curl_error($curl), curl_errno($curl));
                    } else {
                        $response = json_decode($json, true);
                    }

                    if (isset($response['cookie'])) {
                        $this->session->data['cookie'] = $response['cookie'];
                    }
                }
            }

            if (isset($response['cookie'])) {
                $this->session->data['cookie'] = $response['cookie'];
            } else {
                $data['error_warning'] = $this->language->get('error_permission');
            }
        }
    }
}
