<?php
/* 
 * OpenCart Canada Post Shipping Module
 
 * Version: 1.5
 * Author: Olivier Labbé
 * Email: olivier.labbe@votreespace.net
 * Web: http://www.olivierlabbe.com/opencart/  
 
 * Version: 1.0
 * Author: Jason Mitchell
 * Email: jason@attemptone.com
 * Web: http://www.attemptone.com  
 * Description: Connects with Canada Post sellonline server to provide a
 *              shipping estimate.

 * Required Input:
 *   language (opt)
 *   merchantID
 *   fromPostalCode (opt)
 *   turnAroundTime (opt)
 *   itemsPrice (opt)
 *   list of item (quantity, weight, length, width, height, decription)
 *   city (opt)
 *   provOrState (opt)
 *   country
 *   postalCode
 * 
 * Required Output
 *   statusCode
 *   statusMessage
 *   requestID
 *   handling
 *   list of product(id, name, shippingDate, deliveryDate, nextDayAM, packing)
 *   list of emptySpace (opt)
 *   shippingOptions (insurance, deliveryConfirmation, signature)
*/

class ModelShippingCanadaPost extends Model {
	public function getQuote($address) {
		$this->load->language('shipping/canadapost');
	  
	  /*if ($this->config->get('canadapost_status')) {
         $address = $this->customer->getAddress($this->session->data['shipping_address_id']);
         $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('canadapost_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
         if (!$this->config->get('canadapost_geo_zone_id')) {
        		$status = TRUE;
         } elseif ($query->num_rows) {
        		$status = TRUE;
         } else {
        		$status = FALSE;
         }
		} else {
			$status = FALSE;
		}*/
		if ($this->config->get('canadapost_status')) {
      		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('canadapost_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");
		
      		if (!$this->config->get('canadapost_geo_zone_id')) {
        		$status = TRUE;
      		} elseif ($query->num_rows) {
        		$status = TRUE;
      		} else {
        		$status = FALSE;
      		}
		} else {
			$status = FALSE;
		}
		
		$weight = intval($this->weight->convert($this->cart->getWeight(), $this->config->get('config_weight_class_id'), 2));
		
		$method_data = array();
		#print_r($address);
		
			
			if ($status) {
      
      
      //If Canada Post has been enabled
      
         $quote_data = array();
         $return_error = FALSE;
         
		 $cp_title = $this->language->get('text_title');
		 $cp_estdelevery = $this->language->get('text_estdelevery');
         $cp_server = $this->config->get('canadapost_server'); // Default: sellonline.canadapost.ca
         $cp_port = $this->config->get('canadapost_port'); // Default: 30000
		 
         if($this->language->get('languagefile') == 'fr') {
         	$cp_language = 'fr'; // Default: en OR fr
		 } else {
			$cp_language = 'en';
		 }
		 
         $cp_merchantID = $this->config->get('canadapost_merchantId');
         $cp_fromPostalCode = $this->config->get('canadapost_origin');
         $cp_turnAroundTime = $this->config->get('canadapost_turnAround');
         $cp_itemsPrice = $this->cart->getSubTotal();
         $cp_readyToShip = $this->config->get('canadapost_originalPackaging');
         $cp_destCity = $address['city'];
         $cp_destProvince = $address['zone'];
         $cp_destCountry = $address['country'];
         $cp_destPostalCode = str_replace(' ', '', $address['postcode']);
                  
         //Prepare xml format
         $strXML = "<?xml version=\"1.0\" ?>";
         $strXML .= "<eparcel>\n";
         $strXML .= "  <language>" . $cp_language . "</language>\n";
         $strXML .= "  <ratesAndServicesRequest>\n";
         $strXML .= "    <merchantCPCID>" . $cp_merchantID . "</merchantCPCID>\n";
         $strXML .= "    <fromPostalCode>" . $cp_fromPostalCode . "</fromPostalCode>\n";
         $strXML .= "    <turnAroundTime>" . $cp_turnAroundTime . "</turnAroundTime>\n";
         $strXML .= "    <itemsPrice>" . $cp_itemsPrice . "</itemsPrice>\n";
         $strXML .= "    <lineItems>\n";
         foreach ($this->cart->getProducts() as $result) {
		 $modified_weight = $result['weight'] / $result['quantity'];
		 //$modified_weight = str_replace(".",",",$modified_weight);
            $strXML .= "      <item>\n";
            $strXML .= "        <quantity>" . $result['quantity'] . "</quantity>\n";
            $strXML .= "        <weight>" . $modified_weight . "</weight>\n";
            $strXML .= "        <length>" . $result['length'] . "</length>\n";
            $strXML .= "        <width>" . $result['width'] . "</width>\n";
            $strXML .= "        <height>" . $result['height'] . "</height>\n";
            $strXML .= "        <description>" . $result['name'] . ' [' . $result['model'] . ']' . "</description>\n";
            if($cp_readyToShip == 1)  $strXML .= "        <readyToShip/>\n";
            $strXML .= "      </item>\n";
         }
         $strXML .= "    </lineItems>\n";
         $strXML .= "    <city>" . html_entity_decode($cp_destCity) . "</city>\n";
         $strXML .= "    <provOrState>" . html_entity_decode($cp_destProvince) . "</provOrState>\n";
         $strXML .= "    <country>" . html_entity_decode($cp_destCountry) . "</country>\n";
         $strXML .= "    <postalCode>" . $cp_destPostalCode . "</postalCode>\n";
         $strXML .= "  </ratesAndServicesRequest>\n";
         $strXML .= "</eparcel>\n";
        # echo $strXML;
        
         //Make connection
         if ($resultXML = $this->sendToCanadaPost($cp_server, $cp_port, 'POST', '', $strXML)) {
             //parse results
             //echo $resultXML;
             $j=0;
			 #print_r ($resultXML);
             if($this->parseResults($resultXML)){
             	foreach ($this->parseResults($resultXML) as $myResult) {
               //Need to figure out still
              	 $quote_data['canadapost_'.$j] = $myResult;
               	$j++;
            	}
         	} else{
            	$return_error = $this->language->get('text_returnerror');
         	}

         } else {
             $return_error = $this->language->get('text_returnerror').' 0x01';
         }

       

      }
	  
     $method_data = array(
            'id'         => 'canadapost',
            'title'      => $this->language->get('text_title'),
            'quote'      => $quote_data,
            'sort_order' => $this->config->get('canadapost_sort_order'),
            'error'      => $return_error 
         );
      return $method_data;
   }

   
   /* POST message to Canada Post server */
    function sendToCanadaPost($host,$port,$method,$path,$data,$useragent=0) {
   // Supply a default method of GET if the one passed was empty
   if (empty($method))
       $method = 'GET';
   $method = strtoupper($method);
   if ($method == 'GET')
       $path .= '?' . $data;
   $buf = "";
   // try to connect to Canada Post server, for 2 second
   $fp = @fsockopen($host, $port, $errno, $errstr, 2);
   if ( $fp ) {
     fputs($fp, "$method $path HTTP/1.1\n");
     fputs($fp, "Host: $host\n");
     fputs($fp, "Content-type: application/x-www-form-urlencoded\n");
     fputs($fp, "Content-length: " . strlen($data) . "\n");
     if ($useragent)
      fputs($fp, "User-Agent: PHP / OpenCart\n");
     fputs($fp, "Connection: close\n\n");
     if ($method == 'POST')
      fputs($fp, $data);
     while (!feof($fp))
      $buf .= fgets($fp,128);
     fclose($fp);
   } else {
     $buf = '<?xml version="1.0" ?><eparcel><error><statusMessage>Cannot reach Canada Post Server. You may refresh this page (Press F5) to try again.</statusMessage></error></eparcel>'; 
   }

   return $buf;
    }
	
    /*
      Get Canada Post shipping products that are available for current parcels
      This function will return an array include all available products. e.g:
        Array ( 
          [0] => Array ( 
            [name] => Priority Courier 
            [rate] => 25.35 
            [shippingDate] => 2002-08-26 
            [deliveryDate] => 2002-08-27 
            [deliveryDayOfWeek] => 3 
            [nextDayAM] => true 
            [packingID] => P_0 
          ) 
          [1] => Array ( 
            [name] => Xpresspost 
            [rate] => 14.36 
            [shippingDate] => 2002-08-26 
            [deliveryDate] => 2002-08-27 
            [deliveryDayOfWeek] => 3 
            [nextDayAM] => false 
            [packingID] => P_0 
          ) 
          [2] => Array ( 
            [name] => Regular 
            [rate] => 12.36 
            [shippingDate] => 2002-08-26 
            [deliveryDate] => 2002-08-28 
            [deliveryDayOfWeek] => 4 
            [nextDayAM] => false 
            [packingID] => P_0 
          ) 
        )
      If the parcels can't be shipped or other error, this function will return 
      error message. e.g: "The parcel is too large to delivery."
    */ 


    /*
      Parser XML message returned by canada post server.
    */
    function parseResults($resultXML) {
     $statusMessage = substr(substr($resultXML, strpos($resultXML, "<statusMessage>")+strlen("<statusMessage>"), strpos($resultXML, "</statusMessage>")-strlen("<statusMessage>")-strpos($resultXML, "<statusMessage>")),0,2);
	  $cp_title = $this->language->get('text_title');
		 $cp_estdelevery = $this->language->get('text_estdelevery');
      //print "message = $statusMessage";
      if ($statusMessage == 'OK') {
         $strProduct = substr($resultXML, strpos($resultXML, "<product id=")+strlen("<product id=>"), strpos($resultXML, "</product>")-strlen("<product id=>")-strpos($resultXML, "<product id="));
         $index = 0;
         $aryProducts = false;
         while (strpos($resultXML, "</product>")) {
            $name = substr($resultXML, strpos($resultXML, "<name>")+strlen("<name>"), strpos($resultXML, "</name>")-strlen("<name>")-strpos($resultXML, "<name>"));
            $rate = substr($resultXML, strpos($resultXML, "<rate>")+strlen("<rate>"), strpos($resultXML, "</rate>")-strlen("<rate>")-strpos($resultXML, "<rate>"));
            $shippingDate = substr($resultXML, strpos($resultXML, "<shippingDate>")+strlen("<shippingDate>"), strpos($resultXML, "</shippingDate>")-strlen("<shippingDate>")-strpos($resultXML, "<shippingDate>"));
            $deliveryDate = substr($resultXML, strpos($resultXML, "<deliveryDate>")+strlen("<deliveryDate>"), strpos($resultXML, "</deliveryDate>")-strlen("<deliveryDate>")-strpos($resultXML, "<deliveryDate>"));
            $deliveryDayOfWeek = substr($resultXML, strpos($resultXML, "<deliveryDayOfWeek>")+strlen("<deliveryDayOfWeek>"), strpos($resultXML, "</deliveryDayOfWeek>")-strlen("<deliveryDayOfWeek>")-strpos($resultXML, "<deliveryDayOfWeek>"));
            $nextDayAM = substr($resultXML, strpos($resultXML, "<nextDayAM>")+strlen("<nextDayAM>"), strpos($resultXML, "</nextDayAM>")-strlen("<nextDayAM>")-strpos($resultXML, "<nextDayAM>"));
            $packingID = substr($resultXML, strpos($resultXML, "<packingID>")+strlen("<packingID>"), strpos($resultXML, "</packingID>")-strlen("<packingID>")-strpos($resultXML, "<packingID>"));
			$cp_handling = $this->config->get('canadapost_handling');
			$rate = $rate +$cp_handling;
			
            //$aryProducts[$index] = array($name . ', ' . $deliveryDate => $rate);
            $aryProducts[$index] = array(
               'id'           => 'canadapost.canadapost_'.$index,
			   'code'         => 'canadapost.canadapost_'.$index,
               'title'        => '<strong>'.$name.'</strong> | '.$cp_title.' &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ['.$cp_estdelevery.': '.$deliveryDate.']',
               'cost'         => $rate,
               'tax_class_id' => $this->config->get('canadapost_tax_class_id'),
               'text' => '$' . number_format($rate, 2, '.',',')
            );
            $index++;
            $resultXML = substr($resultXML, strpos($resultXML, "</product>") + strlen("</product>"));
         }
         return $aryProducts;
      } else {
         //if (strpos($resultXML, "<error>")) return $statusMessage;
         //else 
         return false;
      }
    }
}
?>