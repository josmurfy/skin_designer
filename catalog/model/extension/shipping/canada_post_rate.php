<?php
class ModelExtensionShippingCanadaPostRate extends Model {
	function getQuote($address) {
		if($address['country_id'] == 38) {
		$this->load->language('extension/shipping/canada_post_rate');

		/* $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('canada_post_rate_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('canada_post_rate_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		} */
		
		$status = true;
		$method_data = array();
		$quote_data = array();
		$i = 0;
		$allowedServices = $this->config->get('canada_post_rate_services');
		
		$username 		 = $this->config->get('canada_post_rate_username'); 
		$password 		 = $this->config->get('canada_post_rate_password');
		$customer_number = $this->config->get('canada_post_rate_customer_number');
		$origin_postcode = $this->config->get('canada_post_rate_origin_postcode');
		$destination_postcode = strtoupper (str_replace (" ","",$address['postcode']));
		$weight = $this->cart->getWeight();
		$weight = number_format($weight/0.453592, 2, '.', '');
		 if($this->session->data['language'] == 'fr') {
         	$cp_language = 'fr-CA'; // Default: en OR fr
		 } else {
			$cp_language = 'en-CA';
		 }
		//$weight = 1.5;
		//Development URL
		//$service_url = 'https://ct.soa-gw.canadapost.ca/rs/ship/price';
		
		//Production URL
		$service_url = 'https://soa-gw.canadapost.ca/rs/ship/price';

		
		//xml for Canada
			//<language>{$cp_language}</language>
		if($address['country_id'] == 38) {
			$xmldata = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v3">
	<customer-number>{$customer_number}</customer-number>
	<parcel-characteristics>
		<weight>{$weight}</weight>
	</parcel-characteristics>
	<origin-postal-code>{$origin_postcode}</origin-postal-code>
	<destination>	
		<domestic>
			<postal-code>{$destination_postcode}</postal-code>
		</domestic>
	</destination>
</mailing-scenario>
XML;
		}
		
		//xml for USA
		if($address['country_id'] == 223) {
			$xmldata = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v3">
	<customer-number>{$customer_number}</customer-number>
	<parcel-characteristics>
		<weight>{$weight}</weight>
	</parcel-characteristics>
	<origin-postal-code>{$origin_postcode}</origin-postal-code>
	<destination>	
		<united-states>
			<zip-code>{$address['postcode']}</zip-code>
		</united-states>
	</destination>
</mailing-scenario>
XML;
		}
		
		//xml for International - other than Canada or USA
		if($address['country_id'] != 223 && $address['country_id'] != 38) {
			$xmldata = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate-v3">
	<customer-number>{$customer_number}</customer-number>
	<parcel-characteristics>
		<weight>{$weight}</weight>
	</parcel-characteristics>
	<origin-postal-code>{$origin_postcode}</origin-postal-code>
	<destination>	
		<international>
			<country-code>{$address['iso_code_2']}</country-code>
		</international>
	</destination>
</mailing-scenario>
XML;
		}



		$curl = curl_init($service_url); // Create REST Request
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_CAINFO, DIR_SYSTEM . '/canadapost_third_party/cert/cacert.pem');
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $xmldata);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($curl, CURLOPT_USERPWD, $username . ':' . $password);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/vnd.cpc.ship.rate-v3+xml', 'Accept: application/vnd.cpc.ship.rate-v3+xml','Accept-language: '.$cp_language));
		//curl_setopt($curl, CURLOPT_HEADER, 0);
		$curl_response = curl_exec($curl); // Execute REST Request
		if(curl_errno($curl)){
			echo 'Curl error: ' . curl_error($curl) . "\n";
		}

		/* echo 'HTTP Response Status: ' . curl_getinfo($curl,CURLINFO_HTTP_CODE) . "\n"; */

		curl_close($curl);

		libxml_use_internal_errors(false);
		$xml = simplexml_load_string('<root>' . preg_replace('/<\?xml.*\?>/','',$curl_response) . '</root>');
		
		if (!$xml) {
			echo 'Failed loading XML' . "\n";
			echo $curl_response . "\n";
			foreach(libxml_get_errors() as $error) {
				echo "\t" . $error->message;
			}
		} else {
			if ($xml->{'price-quotes'} ) {
				$priceQuotes = $xml->{'price-quotes'}->children('http://www.canadapost.ca/ws/ship/rate-v3');
				//print_r ($priceQuotes);
				if ( $priceQuotes->{'price-quote'} ) {
					
					
					foreach ( $priceQuotes as $priceQuote ) { 
						
						if (!in_array($priceQuote->{'service-code'}, $allowedServices)) { continue; }
						
						$text_cost = $this->tax->calculate($this->currency->convert((float)$priceQuote->{'price-details'}->{'due'}/1.34, $this->config->get('config_currency'), $this->session->data['currency']), $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax'));
					
						$quote_data['canada_post_rate'. '_'.$i] = array(
							'code'         	=> 'canada_post_rate.canada_post_rate'. '_'. $i,
							'title'      	=> (string)$priceQuote->{'service-name'},
							'cost'  		=> (float)$priceQuote->{'price-details'}->{'due'}/1.34,
							'tax_class_id'  => $this->config->get('canada_post_rate_tax_class_id'),
							'text'          => $this->currency->format($text_cost, $this->session->data['currency'], '1.000', true)
							//'text'          => $this->currency->format($this->tax->calculate((float)$priceQuote->{'price-details'}->{'due'}, $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
							//'text'          => $this->tax->calculate($this->currency->convert((float)$priceQuote->{'price-details'}->{'due'}, $this->config->get('config_currency'), $this->session->data['currency']), $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax'))
							//'text'          => $this->currency->format((float)$priceQuote->{'price-details'}->{'due'}, $this->session->data['currency'], '1.000', true)
						);
						$i++;
					}
					
					
				}
			}else{
/* 					$quote_data['canada_post_rate'. '_'.0] = array(
							'code'         	=> 'canada_post_rate.canada_post_rate'. '_'. 0,
							'title'      	=> "Flat",
							'cost'  		=> "50",
							'tax_class_id'  => $this->config->get('canada_post_rate_tax_class_id'),
							'text'          => $this->currency->format(50, $this->session->data['currency'], '1.000', true)
							//'text'          => $this->currency->format($this->tax->calculate((float)$priceQuote->{'price-details'}->{'due'}, $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
							//'text'          => $this->tax->calculate($this->currency->convert((float)$priceQuote->{'price-details'}->{'due'}, $this->config->get('config_currency'), $this->session->data['currency']), $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax'))
							//'text'          => $this->currency->format((float)$priceQuote->{'price-details'}->{'due'}, $this->session->data['currency'], '1.000', true)
						); */
						$text_cost = $this->tax->calculate($this->currency->convert(50/1.34, $this->config->get('config_currency'), $this->session->data['currency']), $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax'));
						$quote_data['canada_post_rate'. '_'.$i] = array(
							'code'         	=> 'canada_post_rate.canada_post_rate'. '_'. $i,
							'title'      	=> 'Flat',
							'cost'  		=> 50/1.34,
							'tax_class_id'  => $this->config->get('canada_post_rate_tax_class_id'),
							'text'          => $this->currency->format($text_cost, $this->session->data['currency'], '1.000', true)
							//'text'          => $this->currency->format($this->tax->calculate((float)$priceQuote->{'price-details'}->{'due'}, $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'])
							//'text'          => $this->tax->calculate($this->currency->convert((float)$priceQuote->{'price-details'}->{'due'}, $this->config->get('config_currency'), $this->session->data['currency']), $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax'))
							//'text'          => $this->currency->format((float)$priceQuote->{'price-details'}->{'due'}, $this->session->data['currency'], '1.000', true)
						);
			}
			if ($xml->{'messages'} ) {					
			/* 	$messages = $xml->{'messages'}->children('http://www.canadapost.ca/ws/messages');		
				foreach ( $messages as $message ) {
					echo "<div class='alert alert-danger'>". $message->code ." - ". $message->description."</div>";
				} */

			
			}
				
		}
		
		if ($status) {
			//$quote_data = array();

			/* if ($xml->{'price-quotes'} ) {
				$priceQuotes = $xml->{'price-quotes'}->children('http://www.canadapost.ca/ws/ship/rate-v3');
				if ( $priceQuotes->{'price-quote'} ) {
					foreach ( $priceQuotes as $priceQuote ) {  
						$quote_data['canada_post_rate'] = array(
							'code'         	=> 'canada_post_rate.canada_post_rate',
							'title'      	=> (string)$priceQuote->{'service-name'},
							'text'          => $this->currency->format($this->tax->calculate((float)$priceQuote->{'price-details'}->{'due'}, $this->config->get('canada_post_rate_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']),
							'service_name'  => (string)$priceQuote->{'service-name'},
							'price'         => (float)$priceQuote->{'price-details'}->{'due'}
						);
					}
					
					
				}
			} */
			
			$sort_order = array();
			foreach ($quote_data as $key => $row) {
				$sort_order[$key]  = $row['cost'];
			}
			array_multisort($sort_order, SORT_ASC, $quote_data);
			
			if(!empty($quote_data)) {
				$method_data = array(
					'code'       => 'canada_post_rate',
					'title'      => $this->language->get('text_title'),
					'quote'      => $quote_data,
					'sort_order' => $this->config->get('canada_post_rate_sort_order'),
					'error'      => false
				);
			}
			
			
		}

		return $method_data;
		}
	}
}