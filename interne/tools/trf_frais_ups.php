<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';
// on s&eacute;lectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db); 

/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5,price=price_with_shipping where quantity=0 and price=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */

/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5 where quantity=0 and price>0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$sql2 = 'UPDATE `oc_product` SET `status` = 0,stock_status_id=5 where price=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */


		$sql = 'SELECT * FROM `oc_product` where price_with_shipping>0 and weight>0  and status=1 order by upc limit 500';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
		$calcitem=0;
		while($data = mysqli_fetch_assoc($req)){ 
			//if($upc!=(string)$data['upc']){
				$calcitem++;
				$weight = $data['weight'];
				//$weight = .75;
				$status = true;
			//echo $weight;
				// 70 pound limit
			//echo $status;
				$method_data = array();

				if ($status) {

					$quote_data = array();

					$weight = ($weight < 0.1 ? 0.1 : $weight);
					$pounds = floor($weight);
					$ounces = round(16 * ($weight - $pounds), 2); // max 5 digits
					echo $weight."<br>";
					$postcode = str_replace(' ', '', 12919);

					
							$xml  = '<?xml version="1.0"?>';
							$xml .= '<AccessRequest xml:lang="en-US">';
							$xml .= '	<AccessLicenseNumber>'.$connectionapi['APIUPSTOKEN'].'</AccessLicenseNumber>';
							$xml .= '	<UserId>'.$connectionapi['APIUPSUSERID'].'</UserId>';
							$xml .= '	<Password>'.$connectionapi['APIUPSPASSWORD'].'</Password>';
							$xml .= '</AccessRequest>';
							$xml .= '<?xml version="1.0"?>';
							$xml .= '<RatingServiceSelectionRequest xml:lang="en-US">';
							$xml .= '	<Request>';
							$xml .= '		<TransactionReference>';
							$xml .= '			<CustomerContext>Bare Bones Rate Request</CustomerContext>';
							$xml .= '			<XpciVersion>1.0001</XpciVersion>';
							$xml .= '		</TransactionReference>';
							$xml .= '		<RequestAction>Rate</RequestAction>';
							$xml .= '		<RequestOption>shop</RequestOption>';
							$xml .= '	</Request>';
							$xml .= '   <PickupType>';
							$xml .= '       <Code>01</Code>';
							$xml .= '   </PickupType>';


								$xml .= '   <CustomerClassification>';
								$xml .= '       <Code>01</Code>';
								$xml .= '   </CustomerClassification>';


							$xml .= '	<Shipment>';
							$xml .= '		<Shipper>';
							$xml .= '			<Address>';
							$xml .= '				<City>Champlain</City>';
							$xml .= '				<StateProvinceCode>NY</StateProvinceCode>';
							$xml .= '				<CountryCode>US</CountryCode>';
							$xml .= '				<PostalCode>12919</PostalCode>';
							$xml .= '			</Address>';
							$xml .= '		</Shipper>';
							$xml .= '		<ShipTo>';
							$xml .= '			<Address>';
							$xml .= ' 				<City>Champlain</City>';
							$xml .= '				<StateProvinceCode>NY</StateProvinceCode>';
							$xml .= '				<CountryCode>US</CountryCode>';
							$xml .= '				<PostalCode>12919</PostalCode>';

//$xml .= '				<ResidentialAddressIndicator />';


							$xml .= '			</Address>';
							$xml .= '		</ShipTo>';
							$xml .= '		<ShipFrom>';
							$xml .= '			<Address>';
							$xml .= '				<City>Champlain</City>';
							$xml .= '				<StateProvinceCode>NY</StateProvinceCode>';
							$xml .= '				<CountryCode>US</CountryCode>';
							$xml .= '				<PostalCode>12919</PostalCode>';
							$xml .= '			</Address>';
							$xml .= '		</ShipFrom>';
							$xml .= '		<Service>';
							$xml .= '			<Code>03</Code>';
							$xml .= '		</Service>';
							$xml .= '		<Package>';
							$xml .= '			<PackagingType>';
							$xml .= '				<Code>02</Code>';
							$xml .= '			</PackagingType>';

							$xml .= '		    <Dimensions>';
							$xml .= '				<UnitOfMeasurement>';
							$xml .= '					<Code>IN</Code>';
							$xml .= '				</UnitOfMeasurement>';
							$xml .= '				<Length>' . intval($data['length']) . '</Length>';
							$xml .= '				<Width>' . intval($data['width']) . '</Width>';
							$xml .= '				<Height>' . intval($data['height']) . '</Height>';
							$xml .= '			</Dimensions>';

							$xml .= '			<PackageWeight>';
							$xml .= '				<UnitOfMeasurement>';
							$xml .= '					<Code>LBS</Code>';
							$xml .= '				</UnitOfMeasurement>';
							$xml .= '				<Weight>' . $weight . '</Weight>';
							$xml .= '			</PackageWeight>';

							$xml .= '		</Package>';

							$xml .= '	</Shipment>';
							$xml .= '</RatingServiceSelectionRequest>';
//echo $xml."<br><br>";
							$url = $connectionapi['APIUPSURL'];


							$curl = curl_init($url);

							curl_setopt($curl, CURLOPT_HEADER, 0);
							curl_setopt($curl, CURLOPT_POST, 1);
							curl_setopt($curl, CURLOPT_TIMEOUT, 60);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
							curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
							curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);

							$result = curl_exec($curl);
						$err = curl_error($curl);
						curl_close($curl);

						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);

						$result = str_replace('**', '', $result);
						$result = str_replace("\r\n", '', $result);
						$result = str_replace('\"', '"', $result);
						if ($err) {
							echo "cURL Error #:" . $err;
						} else {
							// Convert xml string into an object 
							//echo $result."\nallo";
							$new = simplexml_load_string($result);  
							// Convert into json 
							$result = json_encode($new); 
							
							//echo $result."\nallo"."<br>";
							$json = json_decode($result, true);
							//print_r($json["RatedShipment"][0]["TransportationCharges"]["MonetaryValue"])."<br>";
							//$Postage=$json["Package"]["Postage"]["Rate"];
							$Postagecom=$json["RatedShipment"][0]["TransportationCharges"]["MonetaryValue"];
							//$encodedSesssionIDString =rawurlencode ($sessionId);
							//echo $encodedSesssionIDString;
							//$sql3 = "UPDATE `oc_product` SET `UPS_com` = '".$Postagecom."'  WHERE `upc` LIKE '".(string)$data['upc']."'";
							//echo $sql3."<br>"."<br>";
							//$req3 = mysqli_query($db,$sql3) ;
						}
					}
											$xml  = '<?xml version="1.0"?>';
							$xml .= '<AccessRequest xml:lang="en-US">';
							$xml .= '	<AccessLicenseNumber>'.$connectionapi['APIUPSTOKEN'].'</AccessLicenseNumber>';
							$xml .= '	<UserId>'.$connectionapi['APIUPSUSERID'].'</UserId>';
							$xml .= '	<Password>'.$connectionapi['APIUPSPASSWORD'].'</Password>';
							$xml .= '</AccessRequest>';
							$xml .= '<?xml version="1.0"?>';
							$xml .= '<RatingServiceSelectionRequest xml:lang="en-US">';
							$xml .= '	<Request>';
							$xml .= '		<TransactionReference>';
							$xml .= '			<CustomerContext>Bare Bones Rate Request</CustomerContext>';
							$xml .= '			<XpciVersion>1.0001</XpciVersion>';
							$xml .= '		</TransactionReference>';
							$xml .= '		<RequestAction>Rate</RequestAction>';
							$xml .= '		<RequestOption>shop</RequestOption>';
							$xml .= '	</Request>';
							$xml .= '   <PickupType>';
							$xml .= '       <Code>01</Code>';
							$xml .= '   </PickupType>';


								$xml .= '   <CustomerClassification>';
								$xml .= '       <Code>01</Code>';
								$xml .= '   </CustomerClassification>';


							$xml .= '	<Shipment>';
							$xml .= '		<Shipper>';
							$xml .= '			<Address>';
							$xml .= '				<City>Champlain</City>';
							$xml .= '				<StateProvinceCode>NY</StateProvinceCode>';
							$xml .= '				<CountryCode>US</CountryCode>';
							$xml .= '				<PostalCode>12919</PostalCode>';
							$xml .= '			</Address>';
							$xml .= '		</Shipper>';
							$xml .= '		<ShipTo>';
							$xml .= '			<Address>';
							$xml .= ' 				<City>Champlain</City>';
							$xml .= '				<StateProvinceCode>NY</StateProvinceCode>';
							$xml .= '				<CountryCode>US</CountryCode>';
							$xml .= '				<PostalCode>12919</PostalCode>';

							$xml .= '				<ResidentialAddressIndicator />';


							$xml .= '			</Address>';
							$xml .= '		</ShipTo>';
							$xml .= '		<ShipFrom>';
							$xml .= '			<Address>';
							$xml .= '				<City>Champlain</City>';
							$xml .= '				<StateProvinceCode>NY</StateProvinceCode>';
							$xml .= '				<CountryCode>US</CountryCode>';
							$xml .= '				<PostalCode>12919</PostalCode>';
							$xml .= '			</Address>';
							$xml .= '		</ShipFrom>';
							$xml .= '		<Service>';
							$xml .= '			<Code>03</Code>';
							$xml .= '		</Service>';
							$xml .= '		<Package>';
							$xml .= '			<PackagingType>';
							$xml .= '				<Code>02</Code>';
							$xml .= '			</PackagingType>';

							$xml .= '		    <Dimensions>';
							$xml .= '				<UnitOfMeasurement>';
							$xml .= '					<Code>IN</Code>';
							$xml .= '				</UnitOfMeasurement>';
							$xml .= '				<Length>' . intval($data['length']) . '</Length>';
							$xml .= '				<Width>' . intval($data['width']) . '</Width>';
							$xml .= '				<Height>' . intval($data['height']) . '</Height>';
							$xml .= '			</Dimensions>';

							$xml .= '			<PackageWeight>';
							$xml .= '				<UnitOfMeasurement>';
							$xml .= '					<Code>LBS</Code>';
							$xml .= '				</UnitOfMeasurement>';
							$xml .= '				<Weight>' . $weight . '</Weight>';
							$xml .= '			</PackageWeight>';

							$xml .= '		</Package>';

							$xml .= '	</Shipment>';
							$xml .= '</RatingServiceSelectionRequest>';
//echo $xml."<br><br>";
							$url = $connectionapi['APIUPSURL'];


							$curl = curl_init($url);

							curl_setopt($curl, CURLOPT_HEADER, 0);
							curl_setopt($curl, CURLOPT_POST, 1);
							curl_setopt($curl, CURLOPT_TIMEOUT, 60);
							curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
							curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
							curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);

							$result = curl_exec($curl);
						$err = curl_error($curl);
						curl_close($curl);

						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
						$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);

						$result = str_replace('**', '', $result);
						$result = str_replace("\r\n", '', $result);
						$result = str_replace('\"', '"', $result);
						if ($err) {
							echo "cURL Error #:" . $err;
						} else {
							// Convert xml string into an object 
							//echo $result."\nallo";
							$new = simplexml_load_string($result);  
							// Convert into json 
							$result = json_encode($new); 
							
							//echo $result."\nallo"."<br>";
							$json = json_decode($result, true);
							//print_r($json["RatedShipment"][0]["TransportationCharges"]["MonetaryValue"])."<br>";
							//$Postage=$json["Package"]["Postage"]["Rate"];
							$Postage=$json["RatedShipment"][0]["TransportationCharges"]["MonetaryValue"];
							//$encodedSesssionIDString =rawurlencode ($sessionId);
							//echo $encodedSesssionIDString;
							$sql3 = "UPDATE `oc_product` SET `UPS` = '".$Postage."',`UPS_com` = '".$Postagecom."'  WHERE `upc` LIKE '".(string)$data['upc']."'";
							echo $sql3."<br>"."<br>";
							$req3 = mysqli_query($db,$sql3) ;
						}
					
		//	}

			
			
			$upc=(string)$data['upc'];
			
			
			
			//$sql2 = 'INSERT INTO `oc_product_special` (`product_id`,customer_group_id,priority,price) VALUES ("'.$data['product_id'].'",1,1,'.$data['price'].')';
			//echo $sql2;
			//$req2 = mysqli_query($db,$sql2);
		}	

		echo $calcitem;  

?>