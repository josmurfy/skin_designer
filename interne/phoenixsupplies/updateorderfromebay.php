<? 
//echo "allo";
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
//$db = mysqli_connect("127.0.0.1","n7f9655_store","Vivi1FX2Pixel$","n7f9655_storeliquidation"); 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");

/* check connection */
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
$j=0;
//echo $_GET['imp'];
if(is_array($_POST['vendu'])){
		foreach($_POST['vendu'] as $vendu)  
			{	
				//$itemvendu=explode(",", $vendu);
				$itemvendu=explode(",", $vendu);
				$sql2 = 'UPDATE `oc_product` SET quantity=quantity-'.$itemvendu[1].',ebay_last_check="2020-09-01" where product_id='.$itemvendu[0]; 
			//	echo $sql2.'<br><br>';
				//$req2 = mysqli_query($db,$sql2);  
				$quantitetotale=$itemvendu[1];
				if($itemvendu[2]-$itemvendu[1]<0){
					$quantitemagasin=$itemvendu[2]-$itemvendu[1];
					$itemvendu[1]=$itemvendu[2];
					$sql2 = 'UPDATE `oc_product` SET unallocated_quantity=unallocated_quantity+'.$quantitemagasin.' where product_id='.$itemvendu[0]; 
					//echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2);
				}

				$sql2 = 'UPDATE `oc_product` SET quantity=quantity-'.$quantitetotale.',ebay_last_check="2020-09-01" where product_id='.$itemvendu[0]; 
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
				$sql2 = 'UPDATE `oc_etsy_products_list` SET `update_flag` = "1" where id_product='.$itemvendu[0]; 

				$req2 = mysqli_query($db,$sql2); 

				$sql2 = 'SELECT ebay_id,p.unallocated_quantity,P.quantity as quantite_entrepot FROM  oc_product p  where p.product_id='.$itemvendu[0];//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
		//echo $sql2.'<br><br>';
				$data2 = mysqli_fetch_assoc($req2);
				$quantite_inventaire=$data2['unallocated_quantity']+$data2['quantite_entrepot'];
				//echo $quantite_inventaire;
				update_to_ebay($connectionapi,0,$quantite_inventaire,$data2['marketplace_item_id'],$_POST['product_id']);
			}
		$z=0;
//mise a jour prix en haut de 5000$
				$sql2 = 'SELECT * FROM `oc_product_special` where price>3000 order by product_id';//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				//echo $sql2.'<br><br>';
				while($data2 = mysqli_fetch_assoc($req2)){
					$sql3 = 'UPDATE `oc_product` SET `status`=0,ebay_last_check="2020-09-01" where product_id='.$data2['product_id']; 
					//echo $sql3.'<br><br>';
					$req3 = mysqli_query($db,$sql3);  
					$z++;
				}
				echo "OVER 3000$:".$z;
		//	include 'connection.php';include 'tools/miseajour_category_actif.php';
				//$sql2 = 'UPDATE `oc_product` SET location="" where quantity=0';
				//echo $sql2.'<br><br>';
				//$req2 = mysqli_query($db,$sql2);
}	
			
			 
			 //echo "allo";
			
			//$new = simplexml_load_string($response); 
			//echo json_encode($new);
			date_default_timezone_set('America/New_York');
				//$dateheure=$dateformat[2]."-".$dateformat[1]."-".$dateformat[0]." ".$ebayinputnameline[1];
			$dateformat=explode (" ",gmdate('Y-m-d H:i:s',strtotime("-7 days")));
			//echo strtotime (date('Y-m-d',$dateformat[0].' - 1 days'));
			$date_transaction=$dateformat[0]."T00:00:01.000Z";//.$dateformat[1].
			//echo $date_transaction;
			//$date_transaction='2021-12-18T17:44:45.510Z';
			//                2021-01-18T17:44:45.510Z
			//				  2021-1-18T9:47:00.000Z
			$today = getdate();
			
			//echo gmdate('Y-m-d H:i:s',time()-15);
			$dateformat2=explode (" ",gmdate('Y-m-d H:i:s',time()-15));
			$date_today=$dateformat2[0]."T".$dateformat2[1].".000Z";
			//echo $date_today;
			$date1_ts = strtotime($dateformat[0]);
			$date2_ts = strtotime($dateformat2[0]);
			$diff = round(($date2_ts - $date1_ts) / 86400);
			
			//echo $diff;
/* 			if($diff>10){
				$from='<NumberOfDays>5</NumberOfDays>'; 
				echo $diff;
			}else{ */
/* 			$from='<CreateTimeFrom>2021-12-20T18:34:44.000Z</CreateTimeFrom>
				<CreateTimeTo>2021-06-21T12:19:22.000Z</CreateTimeTo>';  */
	
		$from='<CreateTimeFrom>'.$date_transaction.'</CreateTimeFrom>
						<CreateTimeTo>'.$date_today.'</CreateTimeTo>';  
						
				$post = '<?xml version="1.0" encoding="utf-8"?>
			<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$connectionapi['EBAYTOKEN'].'</eBayAuthToken>
				</RequesterCredentials>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				'.$from.'
				<Pagination>
					<EntriesPerPage>100</EntriesPerPage>
					<PageNumber>1</PageNumber>
				</Pagination>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>Completed</OrderStatus>
				<SortingOrder>Descending</SortingOrder>
			</GetOrdersRequest>';/*<OrderStatus>Completed</OrderStatus>
			*/
			$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 1149",
					"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
					"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
					"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-CALL-NAME: GetOrders",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			 
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL,  $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($connection);
			$err = curl_error($connection);
			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
			$xml=new SimpleXMLElement($response);
				$jo=0;
				$i=0;
//print_r($response); 
			
			$new = simplexml_load_string($response); 
			$result = json_encode($new); 
			$textoutput=str_replace("}","<br><==<br>",$result);
			$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
			//	echo $textoutput."\nallo"."<br>";
			}
			$pages= $xml->PaginationResult->TotalNumberOfPages;
			//echo $pages;
			foreach($xml->OrderArray->Order as $order){


				$transactionArr = $order->TransactionArray;

				foreach($transactionArr->Transaction as $transaction){ 
					// Loop through each item in the order
					$item = $transaction->Item;
					if(strpos($item->SKU,'COM_',0)!==false){
						$item->SKU=str_replace('COM_','',$item->SKU);
						$ebayoutputnametab[$i]['ShipmentTrackingNumber'] = $transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber;
						$ebayoutputnametab[$i]['ShippingCarrierUsed'] = $transaction->ShippingDetails->ShipmentTrackingDetails->ShippingCarrierUsed;
						//echo $ebayoutputnametab[$i]['ShipmentTrackingNumber']."...<br><br>";
						//$json = json_decode($response, true);
						//print_r($json["OrderArray"]["Order"])."<br>"<br>";
						//print_r($transaction->ShippingDetails);
						if($ebayoutputnametab[$i]['ShipmentTrackingNumber']==""){
							$ebayoutputnametab[$i]['SalesRecordNumber'] = $order->ShippingDetails->SellingManagerSalesRecordNumber;
							$ebayoutputnametab[$i]['EbayId'] = $order->OrderID;

							$cart = array();
							$shipping_address = $order->ShippingAddress;

							$ebayoutputnametab[$i]['BuyerFullname'] = $shipping_address->Name;
							$ebayoutputnametab[$i]['BuyerAddress1'] = $shipping_address->Street1;
							$ebayoutputnametab[$i]['BuyerAddress2'] = $shipping_address->Street2;
							$ebayoutputnametab[$i]['BuyerCity'] = $shipping_address->CityName;
							$ebayoutputnametab[$i]['BuyerState'] = $shipping_address->StateOrProvince;
							$ebayoutputnametab[$i]['BuyerZip'] = $shipping_address->PostalCode;
							$ebayoutputnametab[$i]['Country'] = $shipping_address->Country;
							$ebayoutputnametab[$i]['Phone'] = $shipping_address->Phone;

							

							$ebayoutputnametab[$i]['shippingname'] = $order->ShippingServiceSelected->ShippingService;
							$ebayoutputnametab[$i]['shipping_cost'] = $order->ShippingServiceSelected->ShippingServiceCost;

							$ebayoutputnametab[$i]['products_total'] = $order->Subtotal;
							$ebayoutputnametab[$i]['total'] = $order->Total;
							$ebayoutputnametab[$i]['email'] = $transaction->Buyer->Email;

							

							$ebayoutputnametab[$i]['CustomLabel'] = (string)$item->SKU;
							$ebayoutputnametab[$i]['Quantity'] = (int)$transaction->QuantityPurchased;
							if($variation = $transaction->Variation){
								// If the purchase was of a product variation, the stock code/SKU is here
								$ebayoutputnametab[$i]['CustomLabel'] = (string)$variation->SKU;
							}
							$ebayoutputnametab[$i]['ItemTitle'] = (string)$item->Title;
							$ebayoutputnametab[$i]['SalePrice'] = (float)$transaction->TransactionPrice;
								$sql = 'select * from oc_product,oc_product_description where oc_product.product_id=oc_product_description.product_id and language_id=1 and (oc_product.product_id="'.$ebayoutputnametab[$i]['CustomLabel'].'")'; 
								//echo $sql."<br>";
								//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
								$req = mysqli_query($db,$sql);
								$data = mysqli_fetch_assoc($req);
								$sql3 = 'UPDATE `oc_product` SET ebay_last_check="2020-09-01" where product_id='.$data['product_id']; 
						//echo $sql3.'<br><br>';
								$req3 = mysqli_query($db,$sql3);  
									$ebayoutputnametab[$i]['locationanc']=str_replace('-','',$data['location']);
									//echo $data['location'];
									$ebayoutputnametab[$i]['quantiteanc']=$data['quantity'];
									//$ebayoutputnametab[$i]['ItemTitle']=substr($data['name'],0,45);
									$ebayoutputnametab[$i]['sku']=$data['sku'];
									$Weight=$data['weight']*$ebayoutputnametab[$i]['Quantity'];
									$WeightTot=array(); 
									$Weight=floatval($Weight);
									$WeightTot=explode('.', $Weight);
									$WeightOZ=intval(($Weight-$WeightTot[0])*16);
									$ebayoutputnametab[$i]['poids']=$WeightTot[0]." lb ".$WeightOZ." oz";
									$ebayoutputnametab[$i]['dimension']=number_format($data['length'], 1, '.', '')."x".number_format($data['width'], 1, '.', '')."x".number_format($data['height'], 1, '.', '');
									if($data['image']!="") 	$ebayoutputnametab[$i]['image']='<img height="50" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';
									//$value=(str_replace("$","",$ebayinputnameline[16])*$ebayinputnameline[15]);
									//echo $ebayoutputnametab[$i]['image'];
									$length=number_format($data['length'], 1, '.', '');
									$width=number_format($data['width'], 1, '.', '');
									$height=number_format($data['height'], 1, '.', '');
									$order_number="PL".$ebayinputnameline[0];
									$tracking_code="";
									$description=str_replace(","," ",$ebayinputnameline[14]);
									//print_r($ebayoutputnametab);
									//$i=($j+1);
								$sql = 'select * from oc_product where product_id="'.$ebayoutputnametab[$i]['CustomLabel'].'"'; 
								//echo $sql."<br>";
								//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
								$req = mysqli_query($db,$sql);
								$data = mysqli_fetch_assoc($req);
								$ebayoutputnametab[$i]['locationentrepot']=str_replace('-','',$data['location']);
								$ebayoutputnametab[$i]['quantiteentrepot']=$data['quantity'];

								$ebayoutputnametab[$i]['locationmagasin']='';
								$ebayoutputnametab[$i]['quantitemagasin']=$data['unallocated_quantity'];
							$i++;
						}
					}
				//$i++;
				}
				//echo $stockcode."<br>";
			}
			
if($pages>1){
	$post = '<?xml version="1.0" encoding="utf-8"?>
			<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$connectionapi['EBAYTOKEN'].'</eBayAuthToken>
				</RequesterCredentials>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				'.$from.'
				<Pagination>
					<EntriesPerPage>100</EntriesPerPage>
					<PageNumber>2</PageNumber>
				</Pagination>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>Completed</OrderStatus>
				<SortingOrder>Descending</SortingOrder>
			</GetOrdersRequest>';/*<OrderStatus>Completed</OrderStatus>
			*/
			$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 867",
					"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
					"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
					"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-CALL-NAME: GetOrders",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			 
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL,  $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($connection);
			
			$xml=new SimpleXMLElement($response);

			$new = simplexml_load_string($response); 
			$result = json_encode($new); 
			$textoutput=str_replace("}","<br><==<br>",$result);
			$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
			//	echo $textoutput."\nallo"."<br>";
				
			//$pages= $xml->PaginationResult->TotalNumberOfPages;
			//echo $pages;
			foreach($xml->OrderArray->Order as $order){


				$transactionArr = $order->TransactionArray;

				foreach($transactionArr->Transaction as $transaction){
					// Loop through each item in the order
					
				
					$ebayoutputnametab[$i]['ShipmentTrackingNumber'] = $transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber;
					$ebayoutputnametab[$i]['ShippingCarrierUsed'] = $transaction->ShippingDetails->ShipmentTrackingDetails->ShippingCarrierUsed;
					//echo $ebayoutputnametab[$i]['ShipmentTrackingNumber']."...<br><br>";
					//$json = json_decode($response, true);
					//print_r($json["OrderArray"]["Order"])."<br>"<br>";
					//print_r($transaction->ShippingDetails);
					if($ebayoutputnametab[$i]['ShipmentTrackingNumber']==""){
						$ebayoutputnametab[$i]['SalesRecordNumber'] = $order->ShippingDetails->SellingManagerSalesRecordNumber;
						$ebayoutputnametab[$i]['EbayId'] = $order->OrderID;

						$cart = array();
						$shipping_address = $order->ShippingAddress;

						$ebayoutputnametab[$i]['BuyerFullname'] = $shipping_address->Name;
						$ebayoutputnametab[$i]['BuyerAddress1'] = $shipping_address->Street1;
						$ebayoutputnametab[$i]['BuyerAddress2'] = $shipping_address->Street2;
						$ebayoutputnametab[$i]['BuyerCity'] = $shipping_address->CityName;
						$ebayoutputnametab[$i]['BuyerState'] = $shipping_address->StateOrProvince;
						$ebayoutputnametab[$i]['BuyerZip'] = $shipping_address->PostalCode;
						$ebayoutputnametab[$i]['Country'] = $shipping_address->Country;
						$ebayoutputnametab[$i]['Phone'] = $shipping_address->Phone;

						

						$ebayoutputnametab[$i]['shippingname'] = $order->ShippingServiceSelected->ShippingService;
						$ebayoutputnametab[$i]['shipping_cost'] = $order->ShippingServiceSelected->ShippingServiceCost;

						$ebayoutputnametab[$i]['products_total'] = $order->Subtotal;
						$ebayoutputnametab[$i]['total'] = $order->Total;
						$ebayoutputnametab[$i]['email'] = $transaction->Buyer->Email;

						$item = $transaction->Item;

						$ebayoutputnametab[$i]['CustomLabel'] = (string)$item->SKU;
						$ebayoutputnametab[$i]['Quantity'] = (int)$transaction->QuantityPurchased;
						if($variation = $transaction->Variation){
							// If the purchase was of a product variation, the stock code/SKU is here
							$ebayoutputnametab[$i]['CustomLabel'] = (string)$variation->SKU;
						}
						$ebayoutputnametab[$i]['ItemTitle'] = (string)$item->Title;
						$ebayoutputnametab[$i]['SalePrice'] = (float)$transaction->TransactionPrice;
						/*echo $ebayoutputnametab[$i]['CustomLabel'];
						if(strpos($ebayoutputnametab[$i]['CustomLabel'],"COM")===false){
							mysqli_close($db);
							echo "oui";
							$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_phoenixliquidation');
						}else{
							$ebayoutputnametab[$i]['CustomLabel']=str_replace("COM_","",$ebayoutputnametab[$i]['CustomLabel']);
							mysqli_close($db);
							$db = mysqli_connect("localhost","n7f9655_n7f9655","jnthngrvs01$$","n7f9655_phoenixsupplies"); 
						}*/
							$sql = 'select * from oc_product,oc_product_description where oc_product.product_id=oc_product_description.product_id and language_id=1 and (oc_product.product_id="'.$ebayoutputnametab[$i]['CustomLabel'].'")'; 
						//	echo $sql."<br>";
							//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
							$req = mysqli_query($db,$sql);
							$data = mysqli_fetch_assoc($req);
							$sql3 = 'UPDATE `oc_product` SET ebay_last_check="2020-09-01" where product_id='.$data['product_id']; 
					//echo $sql3.'<br><br>';
							$req3 = mysqli_query($db,$sql3); 
								$ebayoutputnametab[$i]['locationanc']=$data['location'];
								//echo $data['location'];
								$ebayoutputnametab[$i]['quantiteanc']=$data['quantity'];
								$ebayoutputnametab[$i]['ItemTitle']=($data['name']);
								$ebayoutputnametab[$i]['sku']=$data['sku'];
								$Weight=$data['weight']*$ebayoutputnametab[$i]['Quantity'];
								$WeightTot=array(); 
								$Weight=floatval($Weight);
								$WeightTot=explode('.', $Weight);
								$WeightOZ=intval(($Weight-$WeightTot[0])*16);
								$ebayoutputnametab[$i]['poids']=$WeightTot[0]." lb ".$WeightOZ." oz";
								$ebayoutputnametab[$i]['dimension']=number_format($data['length'], 1, '.', '')."x".number_format($data['width'], 1, '.', '')."x".number_format($data['height'], 1, '.', '');
								if($data['image']!="") 	$ebayoutputnametab[$i]['image']='<img height="50" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';
								//$value=(str_replace("$","",$ebayinputnameline[16])*$ebayinputnameline[15]);
								$length=number_format($data['length'], 1, '.', '');
								$width=number_format($data['width'], 1, '.', '');
								$height=number_format($data['height'], 1, '.', '');
								$order_number="PL".$ebayinputnameline[0];
								$tracking_code="";
								$description=str_replace(","," ",$ebayinputnameline[14]);
								//print_r($ebayoutputnametab);
								//$i=($j+1);
							$sql = 'select * from oc_product where product_id="'.$ebayoutputnametab[$i]['CustomLabel'].'"'; 
							//echo $sql."<br>";
							//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
							$req = mysqli_query($db,$sql);
							$data = mysqli_fetch_assoc($req);
							$ebayoutputnametab[$i]['locationentrepot']=$data['location'];
							$ebayoutputnametab[$i]['quantiteentrepot']=$data['quantity'];
							
							$ebayoutputnametab[$i]['locationmagasin']='';
							$ebayoutputnametab[$i]['quantitemagasin']=$data['unallocated_quantity'];
						$i++;
					}
				
				//$i++;
				}
				//echo $stockcode."<br>";
			}
}
if($pages==3){
	$post = '<?xml version="1.0" encoding="utf-8"?>
			<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$connectionapi['EBAYTOKEN'].'</eBayAuthToken>
				</RequesterCredentials>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				<NumberOfDays>3</NumberOfDays>
				<Pagination>
					<EntriesPerPage>100</EntriesPerPage>
					<PageNumber>3</PageNumber>
				</Pagination>
				<OrderRole>Seller</OrderRole>
				<OrderStatus>Completed</OrderStatus>
				<SortingOrder>Descending</SortingOrder>
			</GetOrdersRequest>';/*<OrderStatus>Completed</OrderStatus>
			*/
			$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 867",
					"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
					"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
					"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-CALL-NAME: GetOrders",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			 
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL,  $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec($connection);
			
			$xml=new SimpleXMLElement($response);

			$new = simplexml_load_string($response); 
			$result = json_encode($new); 
			$textoutput=str_replace("}","<br><==<br>",$result);
			$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
			//	echo $textoutput."\nallo"."<br>";
				
			//$pages= $xml->PaginationResult->TotalNumberOfPages;
			//echo $pages;
			foreach($xml->OrderArray->Order as $order){


				$transactionArr = $order->TransactionArray;

				foreach($transactionArr->Transaction as $transaction){
					// Loop through each item in the order
					
				
					$ebayoutputnametab[$i]['ShipmentTrackingNumber'] = $transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber;
					$ebayoutputnametab[$i]['ShippingCarrierUsed'] = $transaction->ShippingDetails->ShipmentTrackingDetails->ShippingCarrierUsed;
					//echo $ebayoutputnametab[$i]['ShipmentTrackingNumber']."...<br><br>";
					//$json = json_decode($response, true);
					//print_r($json["OrderArray"]["Order"])."<br>"<br>";
					//print_r($transaction->ShippingDetails);
					if($ebayoutputnametab[$i]['ShipmentTrackingNumber']==""){
						$ebayoutputnametab[$i]['SalesRecordNumber'] = $order->ShippingDetails->SellingManagerSalesRecordNumber;
						$ebayoutputnametab[$i]['EbayId'] = $order->OrderID;

						$cart = array();
						$shipping_address = $order->ShippingAddress;

						$ebayoutputnametab[$i]['BuyerFullname'] = $shipping_address->Name;
						$ebayoutputnametab[$i]['BuyerAddress1'] = $shipping_address->Street1;
						$ebayoutputnametab[$i]['BuyerAddress2'] = $shipping_address->Street2;
						$ebayoutputnametab[$i]['BuyerCity'] = $shipping_address->CityName;
						$ebayoutputnametab[$i]['BuyerState'] = $shipping_address->StateOrProvince;
						$ebayoutputnametab[$i]['BuyerZip'] = $shipping_address->PostalCode;
						$ebayoutputnametab[$i]['Country'] = $shipping_address->Country;
						$ebayoutputnametab[$i]['Phone'] = $shipping_address->Phone;

						

						$ebayoutputnametab[$i]['shippingname'] = $order->ShippingServiceSelected->ShippingService;
						$ebayoutputnametab[$i]['shipping_cost'] = $order->ShippingServiceSelected->ShippingServiceCost;

						$ebayoutputnametab[$i]['products_total'] = $order->Subtotal;
						$ebayoutputnametab[$i]['total'] = $order->Total;
						$ebayoutputnametab[$i]['email'] = $transaction->Buyer->Email;

						$item = $transaction->Item;

						$ebayoutputnametab[$i]['CustomLabel'] = (string)$item->SKU;
						$ebayoutputnametab[$i]['Quantity'] = (int)$transaction->QuantityPurchased;
						if($variation = $transaction->Variation){
							// If the purchase was of a product variation, the stock code/SKU is here
							$ebayoutputnametab[$i]['CustomLabel'] = (string)$variation->SKU;
						}
						$ebayoutputnametab[$i]['ItemTitle'] = (string)$item->Title;
						$ebayoutputnametab[$i]['SalePrice'] = (float)$transaction->TransactionPrice;
							$sql = 'select * from oc_product,oc_product_description where oc_product.product_id=oc_product_description.product_id and language_id=1 and (oc_product.product_id="'.$ebayoutputnametab[$i]['CustomLabel'].'")'; 
							//echo $sql."<br>"; 
							//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
							$req = mysqli_query($db,$sql);
							$data = mysqli_fetch_assoc($req);
							$sql3 = 'UPDATE `oc_product` SET ebay_last_check="2020-09-01" where product_id='.$data['product_id']; 
					//echo $sql3.'<br><br>';
							$req3 = mysqli_query($db,$sql3); 
								$ebayoutputnametab[$i]['locationanc']=$data['location'];
								//echo $data['location'];
								$ebayoutputnametab[$i]['quantiteanc']=$data['quantity'];
								$ebayoutputnametab[$i]['ItemTitle']=$data['name'];
								$ebayoutputnametab[$i]['sku']=$data['sku'];
								$Weight=$data['weight']*$ebayoutputnametab[$i]['Quantity'];
								$WeightTot=array(); 
								$Weight=floatval($Weight);
								$WeightTot=explode('.', $Weight);
								$WeightOZ=intval(($Weight-$WeightTot[0])*16);
								$ebayoutputnametab[$i]['poids']=$WeightTot[0]." lb ".$WeightOZ." oz";
								$ebayoutputnametab[$i]['dimension']=number_format($data['length'], 1, '.', '')."x".number_format($data['width'], 1, '.', '')."x".number_format($data['height'], 1, '.', '');
								if($data['image']!="") 	$ebayoutputnametab[$i]['image']='<img height="50" src="'.$GLOBALS['WEBSITE'].'image/'.$data['image'].'"/>';
								//$value=(str_replace("$","",$ebayinputnameline[16])*$ebayinputnameline[15]);
								$length=number_format($data['length'], 1, '.', '');
								$width=number_format($data['width'], 1, '.', '');
								$height=number_format($data['height'], 1, '.', '');
								$order_number="PL".$ebayinputnameline[0];
								$tracking_code="";
								$description=str_replace(","," ",$ebayinputnameline[14]);
								//print_r($ebayoutputnametab);
								//$i=($j+1);
							$sql = 'select * from oc_product where product_id="'.$ebayoutputnametab[$i]['CustomLabel'].'"'; 
							//echo $sql."<br>";
							//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
							$req = mysqli_query($db,$sql);
							$data = mysqli_fetch_assoc($req);
							$ebayoutputnametab[$i]['locationentrepot']=$data['location'];
							$ebayoutputnametab[$i]['quantiteentrepot']=$data['quantity'];
						
							$ebayoutputnametab[$i]['locationmagasin']='';
							$ebayoutputnametab[$i]['quantitemagasin']=$data['unallocated_quantity'];
						$i++;
					}
				
				//$i++;
				}
				//echo $stockcode."<br>";
			}
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<script type="text/javascript">
	$(function() {
		  $(document).ready(function () {
			
		   var todaysDate = new Date(); // Gets today's date
			
			// Max date attribute is in "YYYY-MM-DD".  Need to format today's date accordingly
			
			var year = todaysDate.getFullYear(); 						// YYYY
			var month = ("0" + (todaysDate.getMonth() + 1)).slice(-2);	// MM
			var day = ("0" + todaysDate.getDate()).slice(-2);			// DD

			var minDate = (year +"-"+ month +"-"+ day); // Results in "YYYY-MM-DD" for today's date 
			
			// Now to set the max date value for the calendar to be today's date
			$('.departDate input').attr('min',minDate);
		 
			  });
	});
    function selectAll() {
        var items = document.getElementsByName('vendu[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }

    function UnSelectAll() {
        var items = document.getElementsByName('vendu[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }			
</script>
<link href="stylesheet.css" rel="stylesheet">




</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="updateorderfromebay.php" method="post" enctype="multipart/form-data">
<div class="form_description">
<?if(!isset($dir_name)){
	date_default_timezone_set("America/New_York");
	$pagenu=1;$itemcount=0;?>
<h3><label class="description" for="categorie">ORDERS: <?if ($_GET['imp']=="oui"){ echo "(Page: ".$pagenu.")"; $pagenu++;}?></label></h3>

<?if ($_GET['imp']==""){?>
<?/*<label class="description" for="categorie">Date de Livraison:</label>



<SELECT name="datedepart" size="1">
<? for($z=0;$z<=14;$z++){
	if(date("N",strtotime(' +'.$z.' day'))==2 || date("N",strtotime(' +'.$z.' day'))==5){?>
			<option value="<?echo date("yy-m-d",strtotime(' +'.$z.' day'))?>"><?echo date("d M Y",strtotime(' +'.$z.' day'))?></option>
	 <?}?>
	<?}?>
</SELECT>
*/?>
<a href="updateorderfromebay.php?imp=oui"><strong>Imprimer le package slip</strong></a> 
<?}?>

<table border="1" width="100%">
	<tr>
	<th bgcolor="ff6251">
	
	<input type="button" onclick='selectAll()' value="Select All"/><br>
	<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
	</th>
	<th bgcolor="ff6251">
		
		</th>
		<th bgcolor="ff6251">
		SKU
		</th>
	
			<th bgcolor="ff6251">
		CLIENT
		</th>
			<th bgcolor="ff6251">
		Titre
		</th>
		<th bgcolor="ff6251">
		Prix
		</th>
		<th bgcolor="ff6251">
		QTE
		</th>
		
	</tr>

<?
		$sortCriteria =
		  array('SalesRecordNumber' => array(SORT_ASC, SORT_NUMERIC)
		  );
		$ebayoutputnametab = MultiSort($ebayoutputnametab, $sortCriteria, true);
		//$ebayoutputnametab = array_multisort (array_column($ebayoutputnametab, 'SalesRecordNumber'), SORT_DESC, $ebayoutputnametab);
		$i=0;
			foreach($ebayoutputnametab as $ebayoutputname) 
			{	 
			if ($itemcount>16 && $_GET['imp']=="oui"){
					$itemcount=0;
					
					echo '</table><p class="single_record"> </p><h3><label class="description" for="categorie">ORDERS: (Page: '.$pagenu.')</label></h3><table border="1" width="100%">
					<tr>
					<th bgcolor="ff6251">
					
					<input type="button" onclick=\'selectAll()\' value="Select All"/><br>
					<input type="button" onclick=\'UnSelectAll()\' value="Unselect All"/>
					</th>
					<th bgcolor="ff6251">
		
					</th>
					<th bgcolor="ff6251">
					SKU
					</th>
					
						<th bgcolor="ff6251">
					CLIENT
					</th>
						<th bgcolor="ff6251">
					Titre
					</th>
					<th bgcolor="ff6251">
					Prix
					</th>
					
					<th bgcolor="ff6251">
					QTE
					</th>
					</tr>';
					$pagenu++;
				}
			if($ebayoutputname['SalesRecordNumber']!=""){
			$j++;
			//echo "allo";
				if ($bgcolor=="ffffff"){
					$bgcolor="c0c0c0";
				}else{
					$bgcolor="ffffff";
				}
?>
					<tr>
					<td bgcolor="<?echo $bgcolor;?>">
					<input type="checkbox" name="vendu[]" value="<?echo $ebayoutputname['CustomLabel'].','.$ebayoutputname['Quantity'].','.$ebayoutputname['quantiteentrepot'];?>"/>
					<?echo $ebayoutputname['SalesRecordNumber'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['image'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">

						<?echo $ebayoutputname['sku'];?>
					</td>
					
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['BuyerFullname'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">
					<?echo $ebayoutputname['ItemTitle'];?>
					</td>
					<td bgcolor="<?echo $bgcolor;?>">$<?echo $ebayoutputname['Quantity']*$ebayoutputname['SalePrice'];?>
					</td>
					
					<td bgcolor="<?if($ebayoutputname['Quantity']>1){echo "red";}else{echo $bgcolor;}?>">
					<?if($ebayoutputname['Quantity']>1){echo '<p style="color:white">';}?><?echo $ebayoutputname['Quantity'];?><?if($ebayoutputname['Quantity']>1){echo '</p>';}?>
					</td>

					</tr>
		<?
		
	//$j++;
	//echo $j;
	$i++;
	$itemcount++;
			}
	}
		?>
</table>
<?
		if($pagenu&1){
			echo '<p class="single_record"> </p>';
			//echo "odd";
		}else{
			echo '<p class="single_record"> </p><div class="pagebreak"> </div><p class="single_record"> </p>';
			//echo "even";
		}
		$pagenu=1;$itemcount=0;?>

<h3><label class="description" for="categorie">INVENTAIRE: <?if ($_GET['imp']=="oui"){ echo "(Page: ".$pagenu.")"; $pagenu++;}?></label></h3>
	
<table border="1" width="100%">
	<tr>
	<th bgcolor="ff6251">

	</th>
	<th bgcolor="ff6251">
	SKU
	</th>
		<th bgcolor="ff6251">
	Titre
	</th>

	<th bgcolor="ff6251">
	ENTREPOT
	</th>
	<th bgcolor="ff6251">
	STOCK
	</th>
	<th bgcolor="ff6251">
	A PLACER 
	</th>
	<th bgcolor="ff6251">
	STOCK
	</th>
	<th bgcolor="ff6251">
	VENDU
	</th>
	
	</tr>

<?
// merge quantity

		$sortCriteria =
		  array('CustomLabel' => array(SORT_ASC, SORT_NUMERIC)
		  );
		$ebayoutputnametab = MultiSort($ebayoutputnametab, $sortCriteria, true);
		//$ebayoutputnametab = array_multisort (array_column($ebayoutputnametab, 'SalesRecordNumber'), SORT_DESC, $ebayoutputnametab);
		$i=0;
			$temp_array = array();
			foreach($ebayoutputnametab as $ebayoutputname) 
			{	 
			
				if ($temp_array[$i-1]['CustomLabel']==$ebayoutputname['CustomLabel'])
				{
					$temp_array[$i-1]['Quantity']=$temp_array[$i-1]['Quantity']+$ebayoutputname['Quantity'];
				}else{
					$temp_array[$i] = $ebayoutputname;
					$i++;
				}
			}
		$ebayoutputnametab=$temp_array;
				$sortCriteria =
		  array('locationentrepot' => array(SORT_ASC, SORT_STRING)
		  );
		$ebayoutputnametab = MultiSort($ebayoutputnametab, $sortCriteria, true);
		$i=0;
			foreach($ebayoutputnametab as $ebayoutputname) 
			{	 
				if ($itemcount>16 && $_GET['imp']=="oui"){
						$itemcount=0;
						
						echo '</table><p class="single_record"> </p><h3><label class="description" for="categorie">INVENTAIRE: (Page: '.$pagenu.')</label></h3>
						<table border="1" width="100%">
							<tr>
							<th bgcolor="ff6251">

							</th>
								<th bgcolor="ff6251">
							SKU
							</th>
								<th bgcolor="ff6251">
							Titre
							</th>
								<th bgcolor="ff6251">
							ENTREPOT
							</th>
							<th bgcolor="ff6251">
							STOCK
							</th>
							<th bgcolor="ff6251">
							NON PLACER 
							</th>
							<th bgcolor="ff6251">
							STOCK
							</th>
							<th bgcolor="ff6251">
							VENDU
							</th>';
						$pagenu++;
				}
				if($ebayoutputname['SalesRecordNumber']!=""){
				$j++;
				//echo "allo";
					if ($bgcolor=="ffffff"){
						$bgcolor="c0c0c0";
					}else{
						$bgcolor="ffffff";
					}
	?>
						<tr>
						<td bgcolor="<?echo $bgcolor;?>">
								<?echo $ebayoutputname['image'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">

							<?echo $ebayoutputname['sku'];?>
						</td>


						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $ebayoutputname['ItemTitle'];?>
						</td>
		
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $ebayoutputname['locationentrepot'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $ebayoutputname['quantiteentrepot'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $ebayoutputname['locationmagasin'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $ebayoutputname['quantitemagasin'];?>
						</td>
						<td bgcolor="<?echo $bgcolor;?>">
						<?echo $ebayoutputname['Quantity'];?>
						</td>
						</tr>
			<?		
		//$j++;
		//echo $j;
		$i++;
		$itemcount++;
				}
		}
		?>
</table>
<?

}
?>
		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="ebayinputarbonum" value="<?echo $ebayinputarbonum;?>" />
<?if ($_GET['imp']==""){?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h3><a href="interneusa.php" >Retour au MENU</a></h3>
		<h3><a href="<?echo $GLOBALS['WEBSITE'];?>

/interne/updateorderfromebay.php" >Orders Business</a></h3>
		<h3><a href="<?echo $GLOBALS['WEBSITE'];?>interne/updateordersite.php" >Orders sur SITE</a></h3> 
<?}?>

</form>
</body>
</html>
<?if ($_GET['imp']=="oui"){?>
 <script type="text/javascript">
		 window.//print();
		window.onafterprint = function(event) {
			window.location.href = 'updateorderfromebay.php'
		};
</script>
<?} // on ferme la connexion � mysql 
function MultiSort($data, $sortCriteria, $caseInSensitive = true)
{
  if( !is_array($data) || !is_array($sortCriteria))
    return false;      
  $args = array();
  $i = 0;
  foreach($sortCriteria as $sortColumn => $sortAttributes) 
  {
    $colList = array();
    foreach ($data as $key => $row)
    {
      $convertToLower = $caseInSensitive && (in_array(SORT_STRING, $sortAttributes) || in_array(SORT_REGULAR, $sortAttributes));
      $rowData = $convertToLower ? strtolower($row[$sortColumn]) : $row[$sortColumn];
      $colLists[$sortColumn][$key] = $rowData;
    }
    $args[] = &$colLists[$sortColumn];
     
    foreach($sortAttributes as $sortAttribute)
    {     
      $tmp[$i] = $sortAttribute;
      $args[] = &$tmp[$i];
      $i++;     
     }
  }
  $args[] = &$data;
  call_user_func_array('array_multisort', $args);
  return end($args);
}
mysqli_close($db); ?>