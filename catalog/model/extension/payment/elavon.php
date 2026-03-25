<?php
/*  This module was written by Kagonesti for Virtual Merchant Processing for Elavon for opencart
 *  This is not free software - Once purchased, you may use it on any website you own (up to five websites, unless you contact me first for approval)
 *  email:  pdressler@telus.net
 *  Thank you for your purchase.  Please do not distribute
 *  For Opencart 2.0 - Jan 2015
 */ 
class ModelExtensionPaymentElavon extends Model {
	public function getMethod($address, $total) {
		$this->language->load('extension/payment/elavon');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('elavon_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('elavon_total') > 0 && $this->config->get('elavon_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('elavon_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}	

		$method_data = array();

		if ($status) {  
			$method_data = array(
				'code'       => 'elavon',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('elavon_sort_order')
			);
		}

		return $method_data;
	}
}