<?php
class ControllerExtensionPaymentVirtualmerchant extends Controller {
	public function index() {
		$this->load->language('extension/payment/virtualmerchant');
		
		$data['text_credit_card'] = $this->language->get('text_credit_card');
		$data['text_wait'] = $this->language->get('text_wait');
		
		$data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
		$data['entry_cc_number'] = $this->language->get('entry_cc_number');
		$data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
		$data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
		
		$data['button_confirm'] = $this->language->get('button_confirm');
		
		$data['months'] = array();
		for ($i = 1; $i <= 12; $i++) {
			$data['months'][] = array(
				'text'  =>  sprintf('%02d', $i), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();
		
		$data['year_expire'] = array();
		
		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
		
		return $this->load->view('extension/payment/virtualmerchant.tpl', $data);
	}
	
	public function send() {		
		$this->load->language('extension/payment/virtualmerchant');
		
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$json = array();
		if ($order_info['total'] == 0) {
			if ($this->add_recurring($order_info)) {
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('config_order_status_id'));
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('virtualmerchant_order_status_id'), '', false);
				$json['redirect'] = $this->url->link('checkout/success', '', TRUE);
			} else {
				if ($this->error) {
					$this->log->write('VIRTUALMERCHANT ' . $this->error);
				}
				$this->load->language('extension/payment/virtualmerchant');
				$json['error'] = $this->language->get('error_subscription');
			}
		} else {
			$data = array();
			
			/* Settings */
			$data['ssl_show_form'] = 'FALSE';
			$data['ssl_result_format'] = 'xml';
			$data['ssl_transaction_type'] = ($this->config->get('virtualmerchant_method') == 'authorization') ? 'ccauthonly' : 'ccsale';
			$data['ssl_test_mode'] = ($this->config->get('virtualmerchant_mode') == 'test') ? 'TRUE' : 'FALSE';
			/* Payment */
			$data['ssl_card_number'] = substr(preg_replace('/[^0-9]/', '', $this->request->post['cc_number']), 0, 19);
			$data['ssl_exp_date'] = substr($this->request->post['cc_expire_date_month'], 0, 2) . substr($this->request->post['cc_expire_date_year'], -2);
			$data['ssl_cvv2cvc2'] = substr(preg_replace('/[^0-9]/', '', $this->request->post['cc_cvv2']), 0, 4);
			$data['ssl_cvv2cvc2_indicator'] = (empty($data['ssl_cvv2cvc2'])) ? 9 : 1;
			$data['ssl_amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], '', FALSE);
			$data['ssl_salestax'] = (isset($order_info['taxes'])) ? $this->currency->format($order_info['taxes'], $order_info['currency_code'], '', FALSE) : 0;
			$data['ssl_invoice_number'] = substr($this->session->data['order_id'], 0, 25);
			$data['ssl_description'] = substr(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), 0, 255);
			$data['ssl_customer_code'] = 1111;
			// Gateway Bug: Sending currency code for single-currency account triggers multi-currency error (even if currency code matches account currency)
/*  			if (strtoupper($order_info['currency_code']) != strtoupper($this->config->get('virtualmerchant_currency'))) {
				$data['ssl_transaction_currency'] = $order_info['currency_code'];
				$data['ssl_txn_currency_code'] = substr($order_info['currency_code'], 0, 3);
			}  */
			/* Billing */
			$data['ssl_first_name'] = substr(html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'), 0, 20);
			$data['ssl_last_name'] = substr(html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'), 0, 30);
			$data['ssl_company'] = substr(html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8'), 0, 50);
			$data['ssl_avs_address'] = substr(html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'), 0, 30);
			$data['ssl_address2'] = substr(html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8'), 0, 30);
			$data['ssl_city'] = substr(html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'), 0, 30);
			$data['ssl_state'] = substr(html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'), 0, 2);
			$data['ssl_avs_zip'] = substr(preg_replace('/[^a-z0-9]/i', '', html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8')), 0, 9);
			$data['ssl_country'] = substr(html_entity_decode($order_info['payment_iso_code_3'], ENT_QUOTES, 'UTF-8'), 0, 3);
			$data['ssl_phone'] = substr($order_info['telephone'], 0, 20);
			$data['ssl_email'] = substr($order_info['email'], 0, 100);
			$data['ssl_cardholder_ip'] = substr($this->request->server['REMOTE_ADDR'], 0, 40);
			/* Shipping */
			if ($order_info['shipping_method']) {
				$data['ssl_ship_to_first_name'] = substr(html_entity_decode($order_info['shipping_firstname'], ENT_QUOTES, 'UTF-8'), 0, 20);
				$data['ssl_ship_to_last_name'] = substr(html_entity_decode($order_info['shipping_lastname'], ENT_QUOTES, 'UTF-8'), 0, 30);
				$data['ssl_ship_to_company'] = substr(html_entity_decode($order_info['shipping_company'], ENT_QUOTES, 'UTF-8'), 0, 50);
				$data['ssl_ship_to_address1'] = substr(html_entity_decode($order_info['shipping_address_1'], ENT_QUOTES, 'UTF-8'), 0, 30);
				$data['ssl_ship_to_address2'] = substr(html_entity_decode($order_info['shipping_address_2'], ENT_QUOTES, 'UTF-8'), 0, 30);
				$data['ssl_ship_to_city'] = substr(html_entity_decode($order_info['shipping_city'], ENT_QUOTES, 'UTF-8'), 0, 30);
				$data['ssl_ship_to_state'] = substr(html_entity_decode($order_info['shipping_zone_code'], ENT_QUOTES, 'UTF-8'), 0, 2);
				$data['ssl_ship_to_zip'] = substr(preg_replace('/[^a-z0-9]/i', '', html_entity_decode($order_info['shipping_postcode'], ENT_QUOTES, 'UTF-8')), 0, 9);
				$data['ssl_ship_to_country'] = substr(html_entity_decode($order_info['shipping_iso_code_3'], ENT_QUOTES, 'UTF-8'), 0, 3);
			} else {
				$data['ssl_ship_to_first_name'] = substr(html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'), 0, 20);
				$data['ssl_ship_to_last_name'] = substr(html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'), 0, 30);
				$data['ssl_ship_to_company'] = substr(html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8'), 0, 50);
				$data['ssl_ship_to_address1'] = substr(html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'), 0, 30);
				$data['ssl_ship_to_address2'] = substr(html_entity_decode($order_info['payment_address_2'], ENT_QUOTES, 'UTF-8'), 0, 30);
				$data['ssl_ship_to_city'] = substr(html_entity_decode($order_info['payment_city'], ENT_QUOTES, 'UTF-8'), 0, 30);
				$data['ssl_ship_to_state'] = substr(html_entity_decode($order_info['payment_zone_code'], ENT_QUOTES, 'UTF-8'), 0, 2);
				$data['ssl_ship_to_zip'] = substr(preg_replace('/[^a-z0-9]/i', '', html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8')), 0, 9);
				$data['ssl_ship_to_country'] = substr(html_entity_decode($order_info['payment_iso_code_3'], ENT_QUOTES, 'UTF-8'), 0, 3);
			}
			
			$response = $this->call($data,$order_info['currency_code']);
			
			if ($this->error) {
				$json['error'] = $this->error;
				$this->log->write('VIRTUALMERCHANT ' . $json['error']);	
			} elseif ($response) {
				libxml_use_internal_errors(TRUE);
				$xml = @simplexml_load_string($response);
				if ($xml === FALSE) {
					$json['error'] = 'Invalid Gateway Response Format';
					$this->log->write('VIRTUALMERCHANT ERROR: ' . $json['error']);
				} else { //jomid			
					if (isset($xml->errorCode) && (int) $xml->errorCode > 0) {
						$json['error'] = sprintf("ERROR %s: %s\n\n%s", (string) $xml->errorCode, (string) $xml->errorName, (string) $xml->errorMessage);
					} elseif (isset($xml->ssl_result) && (int) @$xml->ssl_result == 0) {
						if ($this->add_recurring($order_info)) {
							$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('config_order_status_id'));
							$message = '';
							if (isset($xml->ssl_approval_code)) {
								$message .= "Approval Code: {$xml->ssl_approval_code}\n";
							}
							if (isset($xml->ssl_avs_response)) {
								$message .= "AVS Response: {$xml->ssl_avs_response}\n";
							}
							if (isset($xml->ssl_txn_id)) {
								$message .= "Transaction ID: {$xml->ssl_txn_id}\n";
							}
							if (isset($xml->ssl_cvv2_response)) {
								$message .= "Card Code Response: {$xml->ssl_cvv2_response}\n";
							}
							$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('virtualmerchant_order_status_id'), $message, false);
							$json['redirect'] = $this->url->link('checkout/success', '', TRUE);
						} else {
							$this->void($xml->ssl_txn_id);
							if ($this->error) {
								$this->log->write('VIRTUALMERCHANT ' . $this->error);
							}
							$json['error'] = $this->language->get('error_subscription');
						}
					} else {
						if (isset($xml->ssl_result_message)) {
							$json['error'] = (string) $xml->ssl_result_message;
							$this->log->write('VIRTUALMERCHANT ERROR: ' . print_r($xml, TRUE));
						} else {
							$json['error'] = 'Empty Gateway Result Message';
							$this->log->write('VIRTUALMERCHANT ERROR: ' . $json['error']);
						}
					}
				}
			} else {
				$json['error'] = 'Empty Gateway Response';
				$this->log->write('VIRTUALMERCHANT CURL ERROR: ' . $json['error']);
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	private function add_recurring($order_info) {
		if ($recurring_products = $this->cart->getRecurringProducts()) {
			$this->load->model('checkout/recurring');
			$rollback = FALSE;
			$recurring_ids = array();
			$cycles = array(
				'day'			=> 'DAILY',
				'week'			=> 'WEEKLY',
				'semi_month'	=> 'SEMIMONTHLY',
				'month'			=> 'MONTHLY',
				'year'			=> 'ANNUALLY',
			);
			foreach ($recurring_products as $item) {
				$data = array(
					'ssl_transaction_type'	=> 'ccaddrecurring',
					'ssl_show_form'			=> 'FALSE',
					'ssl_card_number'		=> substr(preg_replace('/[^0-9]/', '', $this->request->post['cc_number']), 0, 19),
					'ssl_exp_date'			=> substr($this->request->post['cc_expire_date_month'], 0, 2) . substr($this->request->post['cc_expire_date_year'], -2),
					'ssl_amount'			=> $this->currency->format($this->tax->calculate($item['recurring']['price'], $item['tax_class_id'], $this->config->get('config_tax')), $order_info['currency_code'], '', FALSE) * $item['quantity'],
					'ssl_first_name'		=> substr(html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8'), 0, 20),
					'ssl_last_name'			=> substr(html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8'), 0, 30),
					'ssl_avs_address'		=> substr(html_entity_decode($order_info['payment_address_1'], ENT_QUOTES, 'UTF-8'), 0, 30),
					'ssl_avs_zip'			=> substr(preg_replace('/[^a-z0-9]/i', '', html_entity_decode($order_info['payment_postcode'], ENT_QUOTES, 'UTF-8')), 0, 9),
					'ssl_description'		=> $item['name'],
					'ssl_description'		=> $item['name'],
					'ssl_email'				=> substr($order_info['email'], 0, 100), // Undocumented required field
				);
				$data['ssl_billing_cycle'] = $cycles[$item['recurring']['frequency']];
				switch ($item['recurring']['frequency']) {
					case 'month':
						if ($item['recurring']['trial_cycle'] == 2) {
							$data['ssl_billing_cycle'] = 'BIMONHTLY';
						} elseif ($item['recurring']['trial_cycle'] == 3) {
							$data['ssl_billing_cycle'] = 'QUARTERLY';
						} elseif ($item['recurring']['trial_cycle'] == 6) {
							$data['ssl_billing_cycle'] = 'SEMIANNUALLY';
						} elseif ($item['recurring']['trial_cycle'] == 12) {
							$data['ssl_billing_cycle'] = 'ANNUALLY';
						}
						break;
					case 'week':
						if ($item['recurring']['trial_cycle'] == 2) {
							$data['ssl_billing_cycle'] = 'BIWEEKLY';
						} elseif ($item['recurring']['trial_cycle'] == 4) {
							$data['ssl_billing_cycle'] = 'MONTHLY';
						}
						break;
				}
				if ($data['ssl_billing_cycle'] == 'SEMIMONTHLY') {
					$data['ssl_bill_on_half'] = ($this->config->get('virtualmerchant_bill_on_half') != 2) ? 1 : 2; // 1: 1st/15th, 2: 15th/Last
				}
				$month_based = array('MONTHLY', 'BIMONHTLY', 'QUARTERLY', 'SEMESTER', 'SEMIANNUALLY', 'ANNUALLY');
				if (in_array($data['ssl_billing_cycle'], $month_based) && date('j') < 31 && date('j', strtotime('+1 day')) == 1) { // Less than 31 days in month
					$data['ssl_end_of_month'] = $this->config->get('virtualmerchant_end_of_month') ? 'Y' : 'N'; // Default to N
				}

				if ($item['recurring']['trial'] && $item['recurring']['trial_duration'] > 0 && $item['recurring']['trial_cycle'] > 0) {
					$cycle = $item['recurring']['trial_cycle'];
					$frequency = $item['recurring']['trial_frequency'];
					if ($frequency == 'semi_month') { // TO-DO: Calculate actual date
						$frequency = 'week';
						$cycle *= 2;
					}
					$time = strtotime(sprintf('+%d %s', $cycle, $frequency));
				} else {
					$cycle = $item['recurring']['cycle'];
					$frequency = $item['recurring']['frequency'];
					if ($frequency == 'semi_month') { // TO-DO: Calculate actual date
						$frequency = 'week';
						$cycle *= 2;
					}
					if ($this->config->get('virtualmerchant_skip_cycle')) { // First cycle is included in product price
						$time = strtotime(sprintf('+%d %s', $cycle, $frequency));
					} else {
						$time = time(); // Start today
					}
				}
				$data['ssl_next_payment_date'] = date('m/d/Y', $time);

				// Bug: https://github.com/opencart/opencart/issues/2562
				$item['recurring_id'] = $item['recurring']['recurring_id'];
				$item['recurring_name'] = $item['recurring']['name'];
				$item['recurring_frequency'] = $item['recurring']['frequency'];
				$item['recurring_cycle'] = $item['recurring']['cycle'];
				$item['recurring_duration'] = $item['recurring']['duration'];
				$item['recurring_price'] = $item['recurring']['price'];
				$item['recurring_trial'] = $item['recurring']['trial'];
				$item['recurring_trial_frequency'] = $item['recurring']['trial_frequency'];
				$item['recurring_trial_cycle'] = $item['recurring']['trial_cycle'];
				$item['recurring_trial_duration'] = $item['recurring']['trial_duration'];
				$item['recurring_trial_price'] = $item['recurring']['trial_price'];
				
				$recurring_id = $this->model_checkout_recurring->create($item, $order_info['order_id'], $item['name']);
				$response = $this->call($data);
				$xml = @simplexml_load_string($response);
				if (isset($xml->ssl_result) && (int) $xml->ssl_result == 0) {
					$this->model_checkout_recurring->addReference($recurring_id, (string) $xml->ssl_recurring_id);
					$recurring_ids[$recurring_id] = (string) $xml->ssl_recurring_id;
				} else {
					$this->error = sprintf("ERROR %s: %s\n\n%s", (string) $xml->errorCode, (string) $xml->errorName, (string) $xml->errorMessage);
					$rollback = TRUE;
					break;
				}
			}
			if ($rollback) {
				foreach ($recurring_ids as $local => $remote) {
					$this->delete_recurring($remote);
				}
				return FALSE;
			}
		}
		return TRUE;
	}
	
	private function delete_recurring($recurring_id) {
		$this->call(array(
			'ssl_transaction_type'	=> 'ccdeleterecurring',
			'ssl_txn_id'			=> $recurring_id,
		));
	}

	private function void($trans_id) {
		$this->call(array(
			'ssl_transaction_type'	=> 'ccvoid',
			'ssl_txn_id'			=> $trans_id,
		));
	}
	
	private function call($arr,$currency) {
		if ($this->config->get('virtualmerchant_server') == 'live') {
    		//$url = 'https://www.myvirtualmerchant.com/VirtualMerchant/processxml.do';
			$url = 'https://api.convergepay.com/VirtualMerchant/processxml.do';
		} elseif ($this->config->get('virtualmerchant_server') == 'test') {
			//$url = 'https://demo.myvirtualmerchant.com/VirtualMerchantDemo/processxml.do';
			$url = 'https://api.demo.convergepay.com/VirtualMerchantDemo/processxml.do';
		}	

        $data = array();

		$data['ssl_merchant_id'] = substr($this->config->get('virtualmerchant_login'), 0, 15);
		$data['ssl_user_id'] = substr($this->config->get('virtualmerchant_user'), 0, 15);		$pin=explode(',', $this->config->get('virtualmerchant_pin'));		if (strtoupper($currency) != strtoupper($this->config->get('virtualmerchant_currency'))) {			$data['ssl_pin'] = substr($pin[1], 0, 64);					}else{
			$data['ssl_pin'] = substr($pin[0], 0, 64);		}
		
		$data = array_merge($data, $arr);
		
		$request = array('xmldata' => '<txn>');
		foreach ($data as $key => $value) {
			$request['xmldata'] .= sprintf('<%s>%s</%1$s>', $key, htmlspecialchars($value));
		}
		$request['xmldata'] .= '</txn>';

		if ($this->config->get('virtualmerchant_debug')) {
			$this->log->write('VIRTUALMERCHANT REQUEST: ' . print_r($request, TRUE));
		}

		$curl = curl_init($url);
		
		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request, '', '&'));
 
		$response = curl_exec($curl);

		if ($this->config->get('virtualmerchant_debug')) {
			$this->log->write('VIRTUALMERCHANT RESPONSE: ' . print_r($response, TRUE));
		}
				
		if (curl_error($curl)) {
			$this->error = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
			$this->log->write('VIRTUALMERCHANT ' . $this->error);	
		}
		
		curl_close($curl);

		return $response;

	}
}