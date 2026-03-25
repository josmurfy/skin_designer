
<?
include '../connection.php';include '../functionload.php';

			//$sql2 = 'UPDATE `oc_product` SET shipping_ebay="0"';
			//$req2 = mysqli_query($db,$sql2,MYSQLI_USE_RESULT);

			$sql = 'SELECT * FROM `oc_product` AS P  where P.ebay_id>1 and (weight BETWEEN .501 and 2.01) and shipping_ebay=0 order by P.product_id desc';
			//echo $sql;
			//echo mysqli_num_rows($req);
			$i=0;
			while($data = mysqli_fetch_assoc($req)){

			
			// 192581196019 pour les 8 oz et moins
			// 192582228019 2 lbs et moins
			 revise_ebay_shipping($connectionapi,$data['marketplace_item_id'],'192582228019');
				$sql2 = 'UPDATE `oc_product` SET shipping_ebay="2" WHERE `product_id` ="'.$data['product_id'].'"';
				$req2 = mysqli_query($db,$sql2,MYSQLI_USE_RESULT);
			 $i++;
			}
			echo "32oz et moins:".$i."<br><br>";
			$i=0;
			
			$sql = 'SELECT * FROM `oc_product` AS P  where P.ebay_id>1 and (weight <.501) and shipping_ebay=0 order by P.product_id desc';			$req = mysqli_query($db,$sql);
			//echo $sql;
			//echo mysqli_num_rows($req);
			$i=0;
			while($data = mysqli_fetch_assoc($req)){

			
			// 192581196019 pour les 8 oz et moins
			// 192582228019 2 lbs et moins
			 revise_ebay_shipping($connectionapi,$data['marketplace_item_id'],'192581196019');
				$sql2 = 'UPDATE `oc_product` SET shipping_ebay="1" WHERE `product_id` ="'.$data['product_id'].'"';
				$req2 = mysqli_query($db,$sql2,MYSQLI_USE_RESULT);
			 $i++;
			}
			echo "8oz et moins:".$i."<br><br>";
			$i=0;
?>
<? // on ferme la connexion à mysql 
function revise_ebay_shipping($connectionapi,$ebay_id,$shipping) {


					$post = '<?xml version="1.0" encoding="utf-8"?>
							<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
							  <RequesterCredentials>
								<eBayAuthToken>'.$connectionapi['EBAYTOKEN'].'</eBayAuthToken>
							</RequesterCredentials>
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
							<Item> 
								<ItemID>'.$ebay_id.'</ItemID>
								 <SellerProfiles>
									<SellerShippingProfile>
										<ShippingProfileID>'.$shipping.'</ShippingProfileID>
									</SellerShippingProfile>
								 </SellerProfiles>
							</Item>
							</ReviseItemRequest>';
//echo $post;
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-CALL-NAME: ReviseItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			 
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($connection);
			$err = curl_error($connection);
			 
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#8482;&amp;lt;/sup&amp;gt;', '', $result);
			$result = str_replace('&amp;lt;sup&amp;gt;&amp;#174;&amp;lt;/sup&amp;gt;', '', $result);

			$result = str_replace('**', '', $result);
			$result = str_replace("\r\n", '', $result);
			$result = str_replace('\"', '"', $result);
			if ($err) {
				//echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object 
				//echo $result."\nallo";
				$new = simplexml_load_string($result);  
				// Convert into json 
				$result = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
				//echo $textoutput."\nallo"."<br>"; 
				$json = json_decode($result, true); 
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
				$ebay_quantity=$json["Item"]["Quantity"];
				$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
				$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
				//echo $ebay_quantity."---".$Quantity_sold;
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			return $result;
	}
mysqli_close($db); ?>