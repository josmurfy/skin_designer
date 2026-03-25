<?php

namespace vendor\isenselabs\Squareup\Compatibility\Model;

use \vendor\isenselabs\Squareup\Compatibility\Model as SquareModel;

class Payment extends SquareModel {
    protected $imodule_route_payment;
    protected $imodule_extension_route;
    protected $imodule_event_model;
    protected $imodule_event_route;

    public function __construct($registry) {
        // Construct the OC model
        parent::__construct($registry);

        // Load config
        $this->load->config('vendor/isenselabs/squareup/compatibility');

        // Set some config values
        $this->imodule_route_payment = $this->config->get('squareup_imodule_route_payment');
        $this->imodule_extension_route = $this->config->get('squareup_imodule_extension_route');
        $this->imodule_event_model = $this->config->get('squareup_imodule_event_model');
        $this->imodule_event_route = $this->config->get('squareup_imodule_event_route');
    }

    public function getOrderProducts($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
        
        return $query->rows;
    }
    
    public function getOrderOptions($order_id, $order_product_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product_id . "'");
        
        return $query->rows;
    }
    
    public function getOrderVouchers($order_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
    
        return $query->rows;
    }
    
    public function getOrderTotals($order_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
        
        return $query->rows;
    }
}