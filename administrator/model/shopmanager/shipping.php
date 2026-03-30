<?php
namespace Opencart\Admin\Model\Shopmanager;

class Shipping extends \Opencart\System\Engine\Model {

    public function getAccessToken() {
        $clientId = 'LwjvhOcOCn4XHoV3H30EkiLgpqWzVxoZ';
        $clientSecret = 'AKJmQZ348nYhznru';
        $tokenUrl = 'https://api.usps.com/oauth2/v3/token';

        $scopes = 'subscriptions payments pickup tracking labels scan-forms companies service-delivery-standards locations international-labelsa';

        $postData = [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        //   'scope' => $scopes
        ];

        $ch = curl_init($tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));

        $response = curl_exec($ch);
        

        if ($response === false) {
            die('Erreur lors de la demande de jeton d\'accès.');
        }

        $tokenData = json_decode($response, true);
    //print("<pre>".print_r ($tokenData,true )."</pre>");
        if (isset($tokenData['access_token'])) {
            return $tokenData['access_token'];
        } else {
            die('Erreur d\'authentification: ' . $response);
        }
    }

    public function prepareScanFormData($trackingNumbers) {

        $data = [
            'form' => '5630',
            'imageType' => 'PDF',
            'labelType' => '8.5x11LABEL',
            'mailingDate' => date('Y-m-d'),
            'entryFacilityZIPCode' => '12919',
            'destinationEntryFacilityType' => 'NONE',
            'overwriteMailingDate' => false,
            'shipment' => [
                'trackingNumbers' => $trackingNumbers
            ],
            'fromAddress' => [
                    'firstName' => 'Jonathan',
                    'lastName' => 'Gervais',
                    'firm' => 'CanUship',
                    'streetAddress' => '100 Walnut St',
                    'city' => 'Champlain',
                    'state' => 'NY',
                    'ZIPCode' => '12919',
                    'ZIPPlus4' => '5335',
                    'country' => 'United States',
                    'countryISOCode' => '840'
                ]
            ];
       //print("<pre>".print_r ($data,true )."</pre>");
        return $data;
    }

    public function sendUSPSRequest($url, $data) {
        // Affichage des données pour débogage
        //print("<pre>" . print_r($data, true) . "</pre>");
        
        // Obtention du jeton d'accès OAuth2
        $accessToken = $this->getAccessToken();
        //print("<pre>" . print_r($accessToken, true) . "</pre>");
        $headers = [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'Accept: application/json'
        ];
    
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
        // Gestion de la réponse de l'API USPS
        if ($httpcode == 201) {
            $responseBody = json_decode($response, true);
            echo 'SCAN Form créé avec succès.';
            
            // Vérifier et traiter la réponse pour le fichier SCAN Form
            if (isset($responseBody['SCANFormImage'])) {
                $pdfContent = base64_decode($responseBody['SCANFormImage']);
                file_put_contents('SCANForm.pdf', $pdfContent);
            } else {
                echo 'Erreur: SCANFormImage non trouvé dans la réponse.';
            }
        } else {
            echo 'Erreur: ' . $response;
            // Affichage des détails de l'erreur pour débogage
            //print("<pre>" . print_r(json_decode($response, true), true) . "</pre>");
        }
    
        
    }
    

    public function savePDF($pdfContentBase64) {
        $pdfContent = base64_decode($pdfContentBase64);
        file_put_contents('SCANForm.pdf', $pdfContent);
    }

    public function calculateShippingRates($product_info) {
        $shipping_info=array();
        // Logique pour calculer les tarifs d'expédition
    //print("<pre>".print_r ($product_info['UPS_com'],true )."</pre>");
    //print("<pre>".print_r ($product_info['USPS'],true )."</pre>");
    //print("<pre>".print_r ($product_info['USPS_com'],true )."</pre>");
    //print("<pre>".print_r ($product_info['category_id'],true )."</pre>");
        if ($product_info['category_id']=='617' || $product_info['category_id']=='51071' || $product_info['category_id']=='176984'
						|| $product_info['category_id']=='261186' || $product_info['category_id']=='280' || $product_info['category_id']=='80135'
						|| $product_info['category_id']=='73329' 
						|| $product_info['category_id']=='149960'  || $product_info['category_id']=='14962' || $product_info['category_id']=='149959'
						|| $product_info['category_id']=='175718') {


				 $product_info['service'] = ['mailClass' => 'MEDIA_MAIL', 'priceType' => 'RETAIL', 'processingCategory' => 'NON_MACHINABLE', 'rateIndicator' => 'SP'];
				 $product_info['carrier'] = 'USPS Media Mail';
			} else {
				 $product_info['service'] = ['mailClass' => 'PRIORITY_MAIL', 'priceType' => 'COMMERCIAL', 'processingCategory' => 'MACHINABLE', 'rateIndicator' => 'DR'];
				 $product_info['carrier'] = 'USPS Priority Commercial';
			}
            $shipping_info['UPS_com'] = is_numeric($this->get_ups_rate($product_info))?$this->get_ups_rate($product_info):9999;
            $shipping_info['USPS'] = is_numeric($this->get_usps_rate($product_info))?$this->get_usps_rate($product_info):9999;
           $product_info['service'] = ['mailClass' => 'USPS_GROUND_ADVANTAGE', 'priceType' => 'COMMERCIAL', 'processingCategory' => 'MACHINABLE', 'rateIndicator' => 'DR'];
            $product_info['carrier'] = 'USPS GROUND ADVANTAGE';
          
            $shipping_info['USPS_com'] = is_numeric($this->get_usps_rate($product_info))? $this->get_usps_rate($product_info):9999;
          
        $rates = $this->determine_best_rate($shipping_info);
        return $rates;
    }

    private function determine_best_rate($shipping_info) {

        $Postagecom = $shipping_info['UPS_com'];
        $PostageUSPS = $shipping_info['USPS'];
        $PostagecomUSPS = $shipping_info['USPS_com'];
     //print("<pre>".print_r ($shipping_info,true )."</pre>");

        // Logique pour déterminer le meilleur tarif d'expédition
        $shipping_cost = 9999;
        $shipping_carrier = '';
      

        if ($PostageUSPS > 0 && $PostageUSPS < $PostagecomUSPS && $PostageUSPS < $Postagecom) {
            $shipping_cost = $PostageUSPS;
            $shipping_carrier = 'USPS Ground ADV';
          
        } elseif ($PostagecomUSPS > 0 && $PostagecomUSPS < $PostageUSPS && $PostagecomUSPS < $Postagecom) {
            $shipping_cost = $PostagecomUSPS;
            $shipping_carrier = 'USPS Priority Commercial';
          
        } elseif ($Postagecom < 9999) {
            $shipping_cost = $Postagecom;
            $shipping_carrier = 'UPS';
          
        }

        return [
            'shipping_cost' => $shipping_cost,
            'shipping_carrier' => $shipping_carrier,
         
        ];
    }

    private function get_ups_rate($product_info, $zipdestination = 12919) {
        //print("<pre>".print_r ($product_info,true )."</pre>");
            //print("<pre>".print_r ($product_info['length'],true )."</pre>");
        //    echo "allo";
        $product_info['weight']=$product_info['weight']??0.1;
        $product_info['height']=$product_info['height']??1;
        $product_info['length']=$product_info['length']??1;
        $product_info['width']=$product_info['width']??1;

            $access = 'DD9F9AE20FFC7DD5';
            $userid = 'jonathangervais';
            $passwd = 'jnthngrvs01$$';
            $wsdl = DIR_APPLICATION . "model/shopmanager/RateWS.wsdl";
            $operation = "ProcessRate";
            $endpointurl = 'https://onlinetools.ups.com/webservices/Rate';
            // $outputFileName = "XOLTResult.xml";
            // $connectionapi['APIUPSURL']='https://www.ups.com/ups.app/xml/Rate'; 
                    $weight = ($product_info['weight'] < 0.1 ? 0.1 : $product_info['weight']);
                    $pounds = floor($weight);
                    $ounces = round(16 * ($weight - $pounds), 2);
        $option['RequestOption'] = 'Shop';
        $request['Request'] = $option;
        //echo $weight;
        $pickuptype['Code'] = '01';
        $pickuptype['Description'] = 'Daily Pickup';
        $request['PickupType'] = $pickuptype;
        $customerclassification['Code'] = '01';
        $customerclassification['Description'] = 'Classfication';
        $request['CustomerClassification'] = $customerclassification;
        $shopmanager['Name'] = 'PhoenixLiquidation';
        //$shopmanager['ShopmanagerNumber'] = '222006';
        $address['AddressLine'] = array
        (
            '100 Walnut ST'
        );
        $address['City'] = 'Champlain';
        $address['StateProvinceCode'] = 'NY';
        $address['PostalCode'] = '12919';
        $address['CountryCode'] = 'US';
        $shopmanager['Address'] = $address;
        $shipment['Shopmanager'] = $shopmanager;
        $shipto['Name'] = 'PhoenixLiquidation';
        /*       $addressTo['AddressLine'] = '1647 E 53rd St';
        $addressTo['City'] = 'Los Angeles';
        $addressTo['StateProvinceCode'] = 'CA'; */
        // $addressTo['PostalCode'] = '90011';
        $addressTo['PostalCode'] = $zipdestination;
        $addressTo['CountryCode'] = 'US';
        $addressTo['ResidentialAddressIndicator'] = '';
        $shipto['Address'] = $addressTo;
        $shipment['ShipTo'] = $shipto;
        $shipfrom['Name'] = 'PhoenixLiquidation';
        $addressFrom['AddressLine'] = array
        (
            '100 Walnut ST'
        );
        $addressFrom['City'] = 'Champlain';
        $addressFrom['StateProvinceCode'] = 'NY';
        $addressFrom['PostalCode'] = '12919';
        $addressFrom['CountryCode'] = 'US';
        $shipfrom['Address'] = $addressFrom;
        $shipment['ShipFrom'] = $shipfrom;
        $service['Code'] = '03';
        $service['Description'] = 'Service Code';
        $shipment['Service'] = $service;
        $packaging1['Code'] = '02';
        $packaging1['Description'] = 'Rate';
        $package1['PackagingType'] = $packaging1;
        $dunit1['Code'] = 'IN';
        $dunit1['Description'] = 'inches';
        $dimensions1['Length'] = intval($product_info['length']) ;
        $dimensions1['Width'] = intval($product_info['width']);
        $dimensions1['Height'] = intval($product_info['height']);
        $dimensions1['UnitOfMeasurement'] = $dunit1;
        $package1['Dimensions'] = $dimensions1;
        $punit1['Code'] = 'LBS';
        $punit1['Description'] = 'Pounds';
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
        //echo "\n\n";
        // return $request;
try {
            $mode = array(
                'soap_version' => 'SOAP_1_1',
                'trace' => 1
            );
            $client = new \SoapClient($wsdl, $mode);
            $client->__setLocation($endpointurl);
            
            $usernameToken = [
                'Username' => $userid,
                'Password' => $passwd
            ];
            $serviceAccessLicense = [
                'AccessLicenseNumber' => $access
            ];
            $upss = [
                'UsernameToken' => $usernameToken,
                'ServiceAccessToken' => $serviceAccessLicense
            ];
            
            $header = new \SoapHeader('http://www.ups.com/XMLSchema/XOLTWS/UPSS/v1.0', 'UPSSecurity', $upss);
            $client->__setSoapHeaders($header);
            
            $resp = $client->__soapCall($operation, array($request));
            $rated_shipments = $resp->RatedShipment;
            
            foreach ($rated_shipments as $shipment) {
                if ($shipment->Service->Code == '03') {
                    return number_format($shipment->TransportationCharges->MonetaryValue * 0.73, 2, '.', '');
                }
            }
        } catch (\SoapFault $ex) {
            error_log('UPS SOAP Error: ' . $ex->getMessage());
            error_log('UPS Request: ' . $client->__getLastRequest());
            error_log('UPS Response: ' . $client->__getLastResponse());
            return 9999;
        } catch (\Exception $ex) {
            error_log('UPS Error: ' . $ex->getMessage());
            return 9999;
        }
    }
    
    

    // -------------------------------------------------------------------------
    // NOUVELLE FONCTION - USPS REST API v3 (OAuth2) - migration du 2026-01-25
    // L'ancienne Web Tools API (RateV4/XML) a été retirée par USPS.
    // -------------------------------------------------------------------------
    private function get_usps_rate($product_info, $zipdestination = 12919) {

        $product_info['weight'] = $product_info['weight'] ?? 0.1;
        $product_info['height'] = $product_info['height'] ?? 1;
        $product_info['length'] = $product_info['length'] ?? 1;
        $product_info['width']  = $product_info['width']  ?? 1;

        $weight    = max(0.1, (float)$product_info['weight']);
        $mailClass          = $product_info['service']['mailClass']          ?? 'USPS_GROUND_ADVANTAGE';
        $priceType          = $product_info['service']['priceType']          ?? 'RETAIL';
        $processingCategory = $product_info['service']['processingCategory'] ?? 'MACHINABLE';
        $rateIndicator      = $product_info['service']['rateIndicator']      ?? 'SP';

        $accessToken = $this->getAccessToken();

        $url = 'https://api.usps.com/prices/v3/base-rates/search';

        $payload = [
            'originZIPCode'                => '12919',
            'destinationZIPCode'           => (string)$zipdestination,
            'weight'                       => $weight,
            'length'                       => max(1, (int)$product_info['length']),
            'width'                        => max(1, (int)$product_info['width']),
            'height'                       => max(1, (int)$product_info['height']),
            'mailClass'                    => $mailClass,
            'processingCategory'           => $processingCategory,
            'destinationEntryFacilityType' => 'NONE',
            'rateIndicator'                => $rateIndicator,
            'priceType'                    => $priceType,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json',
            'Accept: application/json',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $json = json_decode($response, true);

        if ($httpCode !== 200) {
            $this->log->write('USPS ' . $mailClass . ' Error (HTTP ' . $httpCode . '): ' . $response);
            return 9999;
        }

        //$this->log->write('USPS ' . $mailClass . ' Response: ' . print_r($json, true));

        // L'API retourne un tableau rates[] avec le prix dans rates[n]['price']
        if (!empty($json['rates'])) {
            foreach ($json['rates'] as $rate) {
                if (!empty($rate['price'])) {
                    return (float)$rate['price'];
                }
            }
        }

        $this->log->write('USPS ' . $mailClass . ': aucun tarif trouvé dans la réponse.');
        return 9999;
    }

    // -------------------------------------------------------------------------
    // ANCIENNE FONCTION - USPS Web Tools API (RateV4/XML) - RETIRÉE 2026-01-25
    // Conservée comme référence. NE PAS UTILISER.
    // -------------------------------------------------------------------------
    private function get_usps_rate_OLD($product_info, $zipdestination = 12919) {

        $product_info['weight']=$product_info['weight']??0.1;
        $product_info['height']=$product_info['height']??1;
        $product_info['length']=$product_info['length']??1;
        $product_info['width']=$product_info['width']??1;

        $weight = ($product_info['weight'] < 0.1 ? 0.1 : $product_info['weight']);
        $pounds = floor($weight);
        $ounces = round(16 * ($weight - $pounds), 2);
        $userId = '209PHOEN3821';
        $url='https://secure.shippingapis.com/ShippingApi.dll?';

        $xml = '<RateV4Request USERID="' . $userId . '">';
        $xml .= '    <Package ID="1">';
        $xml .= $product_info['service'];
        $xml .= '        <ZipOrigination>12919</ZipOrigination>';
        $xml .= '        <ZipDestination>' . $zipdestination . '</ZipDestination>';
        $xml .= '        <Pounds>' . $pounds . '</Pounds>';
        $xml .= '        <Ounces>' . $ounces . '</Ounces>';
        $xml .= '        <Container>VARIABLE</Container>';
        $xml .= '        <Size>Regular</Size>';
        $xml .= '        <Width>' . $product_info['width'] . '</Width>';
        $xml .= '        <Length>' . $product_info['length'] . '</Length>';
        $xml .= '        <Height>' . $product_info['height'] . '</Height>';
        $xml .= '        <Machinable>false</Machinable>';
        $xml .= '    </Package>';
        $xml .= '</RateV4Request>';

        $request = 'API=RateV4&XML=' . urlencode($xml);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url . $request);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($curl);

        $response = simplexml_load_string($result);
        $result = json_encode($response);
        $json = json_decode($result, true);

        if ($product_info['service'] == '<Service>Media Mail</Service>') {
            //$this->log->write('USPS Media Mail Response: ' . print_r($json, true));
            return $json["Package"]["Postage"]["Rate"];
        } else {
            return (isset($json["Package"]["Postage"]["CommercialRate"])) ? $json["Package"]["Postage"]["CommercialRate"] : null;
        }
    }
    
    

    private function sendPostRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        
        return $response;
    }
}
?>
