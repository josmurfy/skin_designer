<?php
class ModelExtensionShippingUps extends Model {
	function getQuote($address) {
		$this->load->language('extension/shipping/ups');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('ups_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if (!$this->config->get('ups_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$weight = $this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->config->get('ups_weight_class_id'));
			$weight_code = strtoupper($this->weight->getUnit($this->config->get('ups_weight_class_id')));

			if ($weight_code == 'KG') {
				$weight_code = 'LBS';
			} elseif ($weight_code == 'LB') {
				$weight_code = 'LBS';
			}

			$weight = ($weight < 0.1 ? 0.1 : $weight);
	 		$dimension=$this->cart->getDimension();
			$length = $dimension['length'];
			$width = $dimension['width'];
			$height = $dimension['height'];
			if($address['iso_code_2']=="US"){
				  $infoshipper['City'] = $this->config->get('ups_city');
				  $infoshipper['StateProvinceCode'] =  $this->config->get('ups_state') ;
				  $infoshipper['PostalCode'] = $this->config->get('ups_postcode');
				  $infoshipper['CountryCode'] = $this->config->get('ups_country');
			}else{
				  $infoshipper['City'] = 'Longueuil';
				  $infoshipper['StateProvinceCode'] =  'QC' ;
				  $infoshipper['PostalCode'] = 'J4G1R3';
				  $infoshipper['CountryCode'] = 'CA';
			}
			$length_code = strtoupper($this->length->getUnit($this->config->get('ups_length_class_id')));

			$service_code = array(
				// US Origin
				'US' => array(
					'01' => $this->language->get('text_us_origin_01'),
					'02' => $this->language->get('text_us_origin_02'),
					'03' => $this->language->get('text_us_origin_03'),
					'07' => $this->language->get('text_us_origin_07'),
					'08' => $this->language->get('text_us_origin_08'),
					'11' => $this->language->get('text_us_origin_11'),
					'12' => $this->language->get('text_us_origin_12'),
					'13' => $this->language->get('text_us_origin_13'),
					'14' => $this->language->get('text_us_origin_14'),
					'54' => $this->language->get('text_us_origin_54'),
					'59' => $this->language->get('text_us_origin_59'),
					'65' => $this->language->get('text_us_origin_65')
				),
				// Canada Origin
				'CA' => array(
					'01' => $this->language->get('text_ca_origin_01'),
					'02' => $this->language->get('text_ca_origin_02'),
					'07' => $this->language->get('text_ca_origin_07'),
					'08' => $this->language->get('text_ca_origin_08'),
					'11' => $this->language->get('text_ca_origin_11'),
					'12' => $this->language->get('text_ca_origin_12'),
					'13' => $this->language->get('text_ca_origin_13'),
					'14' => $this->language->get('text_ca_origin_14'),
					'54' => $this->language->get('text_ca_origin_54'),
					'65' => $this->language->get('text_ca_origin_65')
				),
				// European Union Origin
				'EU' => array(
					'07' => $this->language->get('text_eu_origin_07'),
					'08' => $this->language->get('text_eu_origin_08'),
					'11' => $this->language->get('text_eu_origin_11'),
					'54' => $this->language->get('text_eu_origin_54'),
					'65' => $this->language->get('text_eu_origin_65'),
					// next five services Poland domestic only
					'82' => $this->language->get('text_eu_origin_82'),
					'83' => $this->language->get('text_eu_origin_83'),
					'84' => $this->language->get('text_eu_origin_84'),
					'85' => $this->language->get('text_eu_origin_85'),
					'86' => $this->language->get('text_eu_origin_86')
				),
				// Puerto Rico Origin
				'PR' => array(
					'01' => $this->language->get('text_pr_origin_01'),
					'02' => $this->language->get('text_pr_origin_02'),
					'03' => $this->language->get('text_pr_origin_03'),
					'07' => $this->language->get('text_pr_origin_07'),
					'08' => $this->language->get('text_pr_origin_08'),
					'14' => $this->language->get('text_pr_origin_14'),
					'54' => $this->language->get('text_pr_origin_54'),
					'65' => $this->language->get('text_pr_origin_65')
				),
				// Mexico Origin
				'MX' => array(
					'07' => $this->language->get('text_mx_origin_07'),
					'08' => $this->language->get('text_mx_origin_08'),
					'54' => $this->language->get('text_mx_origin_54'),
					'65' => $this->language->get('text_mx_origin_65')
				),
				// All other origins
				'other' => array(
					// service code 7 seems to be gone after January 2, 2007
					'07' => $this->language->get('text_other_origin_07'),
					'08' => $this->language->get('text_other_origin_08'),
					'11' => $this->language->get('text_other_origin_11'),
					'54' => $this->language->get('text_other_origin_54'),
					'65' => $this->language->get('text_other_origin_65')
				)
			);

					  $access = $this->config->get('ups_key');
					  $userid = $this->config->get('ups_username');
					  $passwd = $this->config->get('ups_password');
					  $wsdl = "/home/phoenkv5/public_html/phoenixliquidation/admin/interne/RateWS.wsdl";
					  $operation = "ProcessRate";
					  $endpointurl = 'https://onlinetools.ups.com/webservices/Rate';
					 // $outputFileName = "XOLTResult.xml";
					 // $connectionapi['APIUPSURL']='https://www.ups.com/ups.app/xml/Rate'; 

								$weight = ($weight < 0.1 ? 0.1 : $weight);
								$pounds = floor($weight);
								$ounces = round(16 * ($weight - $pounds), 2);
				  $option['RequestOption'] = 'Shop';
				  $request['Request'] = $option;
			//echo $weight;
				  $pickuptype['Code'] = '01';
				  $pickuptype['Description'] = 'Daily Pickup';
				  $request['PickupType'] = $pickuptype;

				  $customerclassification['Code'] = $this->config->get('ups_pickup');
				  $customerclassification['Description'] = 'Classfication';
				  if ( $infoshipper['CountryCode'] == 'US' && $this->config->get('ups_pickup') == '11') {
					$request['CustomerClassification'] = $this->config->get('ups_classification');
				  }
				  //$shipper['Name'] = 'PhoenixLiquidation';
				  //$shipper['ShipperNumber'] = '222006';
		/* 		  $address['AddressLine'] = array
				  (
					  '100 Walnut ST'
				  ); */
				  $addressshipper['City'] = $infoshipper['City'];
				  $addressshipper['StateProvinceCode'] =  $infoshipper['StateProvinceCode'] ;
				  $addressshipper['PostalCode'] = $infoshipper['PostalCode'];
				  $addressshipper['CountryCode'] = $infoshipper['CountryCode'];
				  $shipper['Address'] = $addressshipper;
				  $shipment['Shipper'] = $shipper;

				 // $shipto['Name'] = 'PhoenixLiquidation';
				  //$addressTo['AddressLine'] = '1647 E 53rd St';
				  $addressTo['City'] = $address['city'];
				  $addressTo['StateProvinceCode'] = $address['zone_code'];
				  $addressTo['PostalCode'] = $address['postcode'];
				  $addressTo['CountryCode'] = $address['iso_code_2'];
				  //if ($this->config->get('ups_quote_type') == 'residential') {
					$addressTo['ResidentialAddressIndicator'] = '';
				 //}
				  $shipto['Address'] = $addressTo;
				  $shipment['ShipTo'] = $shipto;

		/* 		  $shipfrom['Name'] = 'PhoenixLiquidation';
				  $addressFrom['AddressLine'] = array
				  (
					  '100 Walnut ST'
				  ); */
				  $addressFrom['City'] = $infoshipper['City'];
				  $addressFrom['StateProvinceCode'] =  $infoshipper['StateProvinceCode'] ;
				  $addressFrom['PostalCode'] = $infoshipper['PostalCode'];
				  $addressFrom['CountryCode'] = $infoshipper['CountryCode'];
				  $shipfrom['Address'] = $addressFrom;
				  $shipment['ShipFrom'] = $shipfrom;

		/* 		  $service['Code'] = '03';
				  $service['Description'] = 'Service Code';
				  $shipment['Service'] = $service; */

				  $packaging1['Code'] = $this->config->get('ups_packaging');
				  $packaging1['Description'] = 'Rate';
				  $package1['PackagingType'] = $packaging1;
				  $dunit1['Code'] = $length_code;
				  $dunit1['Description'] = 'inches';
				  $dimensions1['Length'] = intval($length) ;
				  $dimensions1['Width'] = intval($width);
				  $dimensions1['Height'] = intval($height);
				  $dimensions1['UnitOfMeasurement'] = $dunit1;
				  $package1['Dimensions'] = $dimensions1;
				  $punit1['Code'] = $weight_code;
		/* 		  $punit1['Description'] = 'Pounds'; */
				  $packageweight1['Weight'] = $weight;
				  $packageweight1['UnitOfMeasurement'] = $punit1;
				  $package1['PackageWeight'] = $packageweight1;

				  $shipment['Package'] = array(	$package1 /* , $package2 */ );
				  $shipment['ShipmentServiceOptions'] = '';
				  $shipment['LargePackageIndicator'] = '';
				  $request['Shipment'] = $shipment;
				//echo "Request.......\n";
				  
				 // print_r($request);
				  //print("<pre>".print_r ($request,true )."</pre>");
				 // echo "\n\n";
				 // return $request;
			  

			  try
			  {

				$mode = array
				(
					 'soap_version' => 'SOAP_1_1',  // use soap 1.1 client
					 'trace' => 1
				);

				// initialize soap client
				$client = new SoapClient($wsdl , $mode);

				//set endpoint url
				$client->__setLocation($endpointurl);


				//create soap header
				$usernameToken['Username'] = $userid;
				$usernameToken['Password'] = $passwd;
				$serviceAccessLicense['AccessLicenseNumber'] = $access;
				$upss['UsernameToken'] = $usernameToken;
				$upss['ServiceAccessToken'] = $serviceAccessLicense;

				$header = new SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0','UPSSecurity',$upss);
				$client->__setSoapHeaders($header);


				//get response
				$resp = $client->__soapCall($operation ,array($request));
			  }
			  catch(Exception $ex)
			  {
				//print_r ($ex);
				//print("<pre>".print_r ($ex,true )."</pre>");
			  }

			$error = '';
 unlink('/home/phoenkv5/public_html/phoenixliquidation/testUPS.txt');
link('/home/phoenkv5/public_html/phoenixliquidation/testUPS.txt');
$fp = fopen('/home/phoenkv5/public_html/phoenixliquidation/testUPS.txt', 'w');
fwrite($fp, json_encode($resp));  
//$result=json_encode($resp);
			$quote_data = array();

			if ($resp) {
				//fwrite($fp, json_encode($resp)); 
				if ($this->config->get('ups_debug')) {
					$this->log->write("UPS DATA SENT: " . $request);
					$this->log->write("UPS DATA RECV: " . $resp);
				}
				//print("<pre>".print_r ($result,true )."</pre>";
				
// 				$previous_value = libxml_use_internal_errors(true);
				
//				$dom = new DOMDocument('1.0', 'UTF-8');
//				$dom->loadXml($result);

//				libxml_use_internal_errors($previous_value);
				
//				if (libxml_get_errors()) {
//					return false;
//				} 
				
				//$rating_service_selection_response = $result->RatingServiceSelectionResponse;

				//$response = $resp->Response;

				//$response_status_code = $resp->ResponseStatus->Code;
				//fwrite($fp, json_encode($resp))); 
				if ($resp->Response->ResponseStatus->Code != '1') {
					$error = $resp->Response->Error->ErrorCode . ': ' . $resp->Response->Error->ErrorDescription;
				} else {
					$rated_shipments = $resp->RatedShipment;

					foreach ($rated_shipments as $key =>$value) {
						//$service = $rated_shipment->Service;

						$code = $value->Service->Code;//$service->Code;

						//$total_charges = ;//$rated_shipment->TotalCharges;

						$cost = $value->TotalCharges->MonetaryValue;//$total_charges->MonetaryValue;

						$currency = $value->TotalCharges->CurrencyCode;//$total_charges->CurrencyCode;

						if (!($code && $cost)) {
							continue;
						}

						if ($this->config->get('ups_' . strtolower($this->config->get('ups_origin')) . '_' . $code)) {
							$quote_data[$code] = array(
								'code'         => 'ups.' . $code,
								'title'        => $service_code[$this->config->get('ups_origin')][$code],
								'cost'         => $this->currency->convert($cost, $currency, $this->config->get('config_currency')),
								'tax_class_id' => $this->config->get('ups_tax_class_id'),
								'text'         => $this->currency->format($this->tax->calculate($this->currency->convert($cost, $currency, $this->session->data['currency']), $this->config->get('ups_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency'], 1.0000000)
							);
						}
					}
				}
			} 

			$title = $this->language->get('text_title');

			if ($this->config->get('ups_display_weight')) {
				$title .= ' (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('ups_weight_class_id')) . ')';
			}

			if ($quote_data || $error) {
				$method_data = array(
					'code'       => 'ups',
					'title'      => $title,
					'quote'      => $quote_data,
					'sort_order' => $this->config->get('ups_sort_order'),
					'error'      => $error
				);
			}
		}

		return $method_data;
	}

}
