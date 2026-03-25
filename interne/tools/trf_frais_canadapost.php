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


		$sql = 'SELECT * FROM `oc_product` where quantity>0 and weight>0 and USPS is null order by upc';
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
				if ($weight > 70) {
					$status = false;
				}
			//echo $status;
				$method_data = array();

				if ($status) {

					$quote_data = array(); 
					if($weight >= 1) {
						$service='		<Service>Priority Commercial</Service>';
					}else{
						$service='		<Service>First Class Commercial</Service>
										<FirstClassMailType>PACKAGE SERVICE RETAIL</FirstClassMailType>';
					}
					$weight = ($weight < 0.1 ? 0.1 : $weight);
					$pounds = floor($weight);
					$ounces = round(16 * ($weight - $pounds), 2); // max 5 digits
					echo $weight."<br>";
					$postcode = str_replace(' ', '', 12919);

					
						$xml  = '<RateV4Request USERID="'.$connectionapi['APIUSPSUSERID'].'">';
						$xml .= '	<Package ID="1">';
						//$xml .=	'		<Service>First-Class Package Service - Retail</Service>';
						$xml .=	$service;
						$xml .=	'		<ZipOrigination>12919</ZipOrigination>';
						$xml .=	'		<ZipDestination>12919</ZipDestination>';
						$xml .=	'		<Pounds>' . $pounds . '</Pounds>';
						$xml .=	'		<Ounces>' . $ounces . '</Ounces>';
						$xml .=	'		<Container>VARIABLE</Container>';
						$xml .=	'		<Size>Regular</Size>';
						$xml .= '		<Width>' . $data['width'] . '</Width>';
						$xml .= '		<Length>' . $data['length'] . '</Length>';
						$xml .= '		<Height>' . $data['height'] . '</Height>';

						// Calculate girth based on usps calculation
						$xml .= '		<Girth>' . (round(((float)$data['width'] + (float)$data['length'] * 2 + (float)$data['height'] * 2), 1)) . '</Girth>';
						$xml .=	'		<Machinable>false</Machinable>';
						$xml .=	'	</Package>';
						$xml .= '</RateV4Request>';
						//echo $xml."<br><br>";
						$request = 'API=RateV4&XML=' . urlencode($xml);
							
					if ($status) {
						$curl = curl_init();

						curl_setopt($curl, CURLOPT_URL, $connectionapi['APIUSPSURL'] . $request);
						curl_setopt($curl, CURLOPT_HEADER, 0);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

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
							
							echo $result."\nallo"."<br>";
							$json = json_decode($result, true);
							print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
							$Postage=$json["Package"]["Postage"]["Rate"];
							$Postagecom=$json["Package"]["Postage"]["CommercialRate"];
							//$encodedSesssionIDString =rawurlencode ($sessionId);
							//echo $encodedSesssionIDString;
							$sql3 = "UPDATE `oc_product` SET `USPS` = '".$Postage."',`USPS_com` = '".$Postagecom."'  WHERE `upc` LIKE '".(string)$data['upc']."'";
							echo $sql3."<br>"."<br>";
							$req3 = mysqli_query($db,$sql3) ;
						}
					}
				}
		//	}

			
			
			$upc=(string)$data['upc'];
			
			
			
			//$sql2 = 'INSERT INTO `oc_product_special` (`product_id`,customer_group_id,priority,price) VALUES ("'.$data['product_id'].'",1,1,'.$data['price'].')';
			//echo $sql2;
			//$req2 = mysqli_query($db,$sql2);
		}	

		echo $calcitem;  

?>