<?
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include 'connection.php';

if (!isset($_POST['imp'])) {
    $_POST['imp'] = isset($_GET['imp']) ? $_GET['imp'] : '';
}
$j=0;
//echo $_GET['imp'];

//print("<pre>".print_r ($connectionapi,true )."</pre>");
 

if(isset($_POST['vendu']) && is_array($_POST['vendu'])){
		foreach($_POST['vendu'] as $vendu)  
			{	
				//$itemvendu=explode(",", $vendu);
				$itemvendu=explode(",", $vendu);
				$product_id_tmp=explode("_",$itemvendu[0]);
				$itemvendu[0]=$product_id_tmp[0];
				if (strpos($itemvendu[3], 'COM_') === 0) {
					// Se connecter à la première base de données
					$itemvendu[0]=str_replace("COM_", "", $itemvendu[0]);
					$website="https://phoenixsupplies.ca/";
					$db = mysqli_connect("localhost","n7f9655_n7f9655","jnthngrvs01$$","n7f9655_phoenixsupplies");
				} else {
					// Se connecter à la deuxième base de données
					$website="https://phoenixliquidation.ca/";
					$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_phoenixliquidation');
				}

				// Vérifier la connexion
				if (!$db) {
					die("Connection failed: " . mysqli_connect_error());
				}
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

				$sql2 = 'UPDATE `oc_product` SET quantity=quantity-'.$itemvendu[1].' where product_id='.$itemvendu[0]; 
			//	echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);  
			//	$sql2 = 'UPDATE `oc_product` SET quantity=quantity-'.$quantitetotale.',ebay_last_check="2020-09-01" where product_id='.$itemvendu[0]; 
		//		echo $sql2.'<br><br>';
			//	$req2 = mysqli_query($db,$sql2);  
				$sql2 = 'UPDATE `oc_etsy_products_list` SET `update_flag` = "1" where id_product='.$itemvendu[0]; 

				$req2 = mysqli_query($db,$sql2); 
			//	check_quantity($db, "oui");
				$sql2 = 'SELECT ebay_id,p.unallocated_quantity,p.quantity as quantite_entrepot FROM oc_product p  where product_id='.$itemvendu[0];//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
		//echo $sql2.'<br><br>';
				$data2 = mysqli_fetch_assoc($req2);
				$quantite_inventaire=$data2['unallocated_quantity']+$data2['quantite_entrepot'];
				//echo $quantite_inventaire;
				update_to_ebay($connectionapi,0,$quantite_inventaire,$data2['marketplace_item_id'],$itemvendu[0]);
			}
		$z=0;
//mise a jour prix en haut de 5000$

				mysqli_close($db);
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
			$dateformat=explode (" ",gmdate('Y-m-d H:i:s',strtotime("-10 days")));
			//echo strtotime (date('Y-m-d',$dateformat[0].' - 1 days'));
			$date_transaction=$dateformat[0]."T00:00:01.000Z";//.$dateformat[1].
			//echo $date_transaction;
			//$date_transaction='2021-12-18T17:44:45.510Z';
			//                2021-01-18T17:44:45.510Z
			//				  2021-1-18T9:47:00.000Z
		//	$today = getdate();
		
		
	//	echo "allo";
	//	//print("<pre>".print_r ($orders2,true )."</pre>");
	//	echo "allo";
			

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
<title>Orders</title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>

<link href="stylesheet.css" rel="stylesheet">
<?if(isset($_POST['imp']) && $_POST['imp']=='oui'){
	echo ' <style>

        @media print {
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            th, td {
                border: 1px solid #000;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #ff6251;
                color: #fff;
            }
            img {
                max-width: 100px;
                max-height: 100px;
            }
            .description {
                font-weight: bold;
                margin-bottom: 10px;
            }
			.pagebreak_inv {
                page-break-before: always;
            }
				    hjo {
        font-size: 21px;
        color: #124;
    }
        }
    </style>';
}

?>





</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="updateorderfromebay.php" method="post" enctype="multipart/form-data">

<?getEbayOrders($db,$connectionapi,$date_transaction);?>
<?

		?>

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="'. $new.'" />
		<input type="hidden" name="ebayinputarbonum" value="'. $ebayinputarbonum.'" />


</form>
</body>
</html>
<?
 function refreshAccessToken($refreshToken) {
	$client = new GuzzleHttp\Client();
	$url = 'https://api.ebay.com/identity/v1/oauth2/token';

	// Informations d'identification de l'application
	$clientId = 'CanUShip-CanUship-PRD-1d10eaf1b-9bf3ab28'; // Remplacez par votre client_id
	$clientSecret = 'PRD-93ff3ada979d-7fcf-4938-be46-ba89'; // Remplacez par votre client_secret

	// En-tête Authorization encodé en base64
	$encodedCredentials = base64_encode($clientId . ':' . $clientSecret);

	$headers = [
		'Content-Type' => 'application/x-www-form-urlencoded',
		'Authorization' => 'Basic ' . $encodedCredentials,
	];

	// Paramètres de la requête pour rafraîchir le jeton d'accès
	$body = [
		'grant_type' => 'refresh_token',
		'refresh_token' => $refreshToken,
		'scope' => 'https://api.ebay.com/oauth/api_scope https://api.ebay.com/oauth/api_scope/sell.marketing.readonly https://api.ebay.com/oauth/api_scope/sell.marketing https://api.ebay.com/oauth/api_scope/sell.inventory.readonly https://api.ebay.com/oauth/api_scope/sell.inventory https://api.ebay.com/oauth/api_scope/sell.account.readonly https://api.ebay.com/oauth/api_scope/sell.account https://api.ebay.com/oauth/api_scope/sell.fulfillment.readonly https://api.ebay.com/oauth/api_scope/sell.fulfillment https://api.ebay.com/oauth/api_scope/sell.analytics.readonly https://api.ebay.com/oauth/api_scope/sell.finances https://api.ebay.com/oauth/api_scope/sell.payment.dispute https://api.ebay.com/oauth/api_scope/commerce.identity.readonly https://api.ebay.com/oauth/api_scope/sell.reputation https://api.ebay.com/oauth/api_scope/sell.reputation.readonly https://api.ebay.com/oauth/api_scope/commerce.notification.subscription https://api.ebay.com/oauth/api_scope/commerce.notification.subscription.readonly https://api.ebay.com/oauth/api_scope/sell.stores https://api.ebay.com/oauth/api_scope/sell.stores.readonly'
	];

	try {
		// Effectuer la requête POST pour renouveler le jeton d'accès
		$response = $client->post($url, [
			'headers' => $headers,
			'form_params' => $body
		]);

		// Analyser la réponse
		$responseArray = json_decode($response->getBody()->getContents(), true);
//print("<pre>".print_r ('1441:ebay.php',true )."</pre>");
 //print("<pre>".print_r ($responseArray,true )."</pre>");
		// Vérifier si le nouveau jeton d'accès est obtenu
		if (isset($responseArray['access_token'])) {
			$responseArray['bearer_token']=$responseArray['access_token'];
			return $responseArray;
		} else {
			echo "Erreur lors de l'obtention du nouveau jeton d'accès.\n";
			print_r($responseArray);
			return null;
		}
	} catch (\Exception $e) {
		echo "Erreur pendant l'obtention du nouveau jeton d'accès : " . $e->getMessage();
		return null;
	}
}
function getEbayOrders ($db,$connectionapi,$date_transaction,$page=1, $orders=array()){

	
			
//echo gmdate('Y-m-d H:i:s',time()-15);
$dateformat2=explode (" ",gmdate('Y-m-d H:i:s',time()-15));
$date_today=$dateformat2[0]."T".$dateformat2[1].".000Z";
//echo $date_today;
//		$date1_ts = strtotime($dateformat[0]);
//		$date2_ts = strtotime($dateformat2[0]);
//		$diff = round(($date2_ts - $date1_ts) / 86400);

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

	<ErrorLanguage>en_US</ErrorLanguage>
	<WarningLevel>High</WarningLevel>
	'.$from.'
	<Pagination>
		<EntriesPerPage>100</EntriesPerPage>
		<PageNumber>'.$page.'</PageNumber>
	</Pagination>
	<OrderRole>Seller</OrderRole>
	<OrderStatus>Completed</OrderStatus>
	<SortingOrder>Descending</SortingOrder>
</GetOrdersRequest>';/*<OrderStatus>Completed</OrderStatus>
*/

/*	<RequesterCredentials> 
		<eBayAuthToken>'.$connectionapi['EBAYTOKEN'].'</eBayAuthToken>
	</RequesterCredentials>*/
$headers = array(
		"X-EBAY-API-COMPATIBILITY-LEVEL: 1149",
		"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
		"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
		"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
		"X-EBAY-API-IAF-TOKEN: Bearer ".$connectionapi['bearer_token'],
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
$idarray=count($orders);
$result=json_decode($result,true);
//echo "NB array:".$idarray;
 //print("<pre>".print_r ($result,true )."</pre>");

	foreach($result ['OrderArray']['Order'] as $order){
	//	echo "orderinital";
//	//print("<pre>".print_r ($order,true )."</pre>");
	//	$order_ebay= json_encode($order);
	//	//print("<pre>".print_r (json_decode($order_ebay,true),true )."</pre>");
		$orders[$idarray]=$order;
		$idarray++;
	
	}	
//	echo "orders";
//	//print("<pre>".print_r ($orders,true )."</pre>");
	$TotalNumberOfPages=$result['PaginationResult']['TotalNumberOfPages'];
	$page++;
	//echo "<BR>NB page=$TotalNumberOfPages";
	//echo "<BR>Page=$page";
	
	if($page<=$TotalNumberOfPages){
		getEbayOrders($db,$connectionapi,$date_transaction,$page,$orders);
	}else{
//$textoutput=str_replace("}","<br><==<br>",$orders);
//$textoutput=str_replace("{","<br>==><br>",$textoutput);
	$idarray=count($orders);
	//echo "<br>NB array:".$idarray;
	//	echo "order";
		//	//print("<pre>".print_r ($orders,true )."</pre>");
//	echo $extoutput."\nallo"."<br>";


	
		$i=0;
		foreach($orders as $order){
		//	echo "order";
		//	//print("<pre>".print_r ($order,true )."</pre>");
		if ($order['OrderStatus']!='Cancelled'){
			if(isset($order['TransactionArray']['Transaction'][0])){
				$transactionArr = $order['TransactionArray']['Transaction'];
			}else{
				$transactionArr = $order['TransactionArray'];
			}
	
		//print("<pre>".print_r ($ebayoutputnametab,true )."</pre>");	
		//	//print("<pre>".print_r ($transactionArr,true )."</pre>");

			foreach($transactionArr as $transaction){ 
				// Loop through each item in the order
			//	//print("<pre>".print_r ($transaction,true )."</pre>");
				$item = $transaction['Item'];
			//	if(strpos($item['SKU'],'COM_',0)===false ){
					if(!isset($transaction['ShippingDetails']['ShipmentTrackingDetails'][0]) && !isset($transaction['ShippingDetails']['ShipmentTrackingDetails'])){
		//	if( $order['ShippingDetails']['SellingManagerSalesRecordNumber']>=25010 && $order['ShippingDetails']['SellingManagerSalesRecordNumber']<=25021){
						$ebayoutputnametab[$i]['SalesRecordNumber'] = $order['ShippingDetails']['SellingManagerSalesRecordNumber'];
						$ebayoutputnametab[$i]['EbayId'] = $order['OrderID'];

				//		$cart = array();
						$shipping_address = $order['ShippingAddress'];

						$ebayoutputnametab[$i]['BuyerFullname'] = $shipping_address['Name'];
						$ebayoutputnametab[$i]['BuyerAddress1'] = $shipping_address['Street1'];
						$ebayoutputnametab[$i]['BuyerCity'] = $shipping_address['CityName'];
						$ebayoutputnametab[$i]['BuyerState'] = $shipping_address['StateOrProvince'];
						$ebayoutputnametab[$i]['BuyerZip'] = $shipping_address['PostalCode'];
						$ebayoutputnametab[$i]['Country'] = $shipping_address['Country'];
						$ebayoutputnametab[$i]['Phone'] = $shipping_address['Phone'];
						$ebayoutputnametab[$i]['products_total'] = $order['Subtotal'];
						$ebayoutputnametab[$i]['total'] = $order['Total'];

						//$item = $transaction']['Item;

						$ebayoutputnametab[$i]['CustomLabel'] = (string)$item['SKU'];
						$ebayoutputnametab[$i]['Quantity'] = (int)$transaction['QuantityPurchased'];
					//	if($variation == $transaction['Variation']){
							// If the purchase was of a product variation, the stock code/SKU is here
					//		$ebayoutputnametab[$i]['CustomLabel'] = (string)$variation['SKU'];
					//	}
						$ebayoutputnametab[$i]['ItemTitle'] = (string)$item['Title'];
						$ebayoutputnametab[$i]['SalePrice'] = (float)$transaction['TransactionPrice'];
						// Vérifier si le SKU commence par "COM_"
						if (strpos($ebayoutputnametab[$i]['CustomLabel'], 'COM_') === 0) {
							$ebayoutputnametab[$i]['CustomLabel']=str_replace("COM_", "",$ebayoutputnametab[$i]['CustomLabel']);
							$ebayoutputnametab[$i]['COM']="COM_";
							// Se connecter à la première base de données
							$db = mysqli_connect("localhost","n7f9655_n7f9655","jnthngrvs01$$","n7f9655_phoenixsupplies");
							$website="https://phoenixsupplies.ca/";
						} else {
							// Se connecter à la deuxième base de données
							$db = mysqli_connect('127.0.0.1','n7f9655_n7f9655','jnthngrvs01$$','n7f9655_phoenixliquidation');
							$website="https://phoenixliquidation.ca/";
							$ebayoutputnametab[$i]['COM']="RET_";
						}

						// Vérifier la connexion
						if (!$db) {
							die("Connection failed: " . mysqli_connect_error());
						}
							$sql = 'select * from oc_product,oc_product_description where oc_product.product_id=oc_product_description.product_id and language_id=1 and (oc_product.product_id="'.$ebayoutputnametab[$i]['CustomLabel'].'")'; 
							//echo $sql."<br>";

							//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
							$req = mysqli_query($db,$sql);
							$data = mysqli_fetch_assoc($req);
						//	//print("<pre>".print_r ($data,true )."</pre>");
						if(!empty($data)){
							$sql3 = 'UPDATE `oc_product` SET ebay_last_check="2020-09-01" where product_id='.$data['product_id']; 

					//echo $sql3.'<br><br>';
							mysqli_query($db,$sql3);  
						
								//$ebayoutputnametab[$i]['locationanc']=str_replace('-','',$data['location']);
								$ebayoutputnametab[$i]['locationanc']=$data['location'];
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
								if($data['image']!="") 	$ebayoutputnametab[$i]['image']='<img height="50" src="'.$website.'image/'.$data['image'].'"/>';
								//$value=(str_replace("$","",$ebayinputnameline[16])*$ebayinputnameline[15]);
								//echo $ebayoutputnametab[$i]['image'];
							//	$length=number_format($data['length'], 1, '.', '');
						//		$width=number_format($data['width'], 1, '.', '');
						//		$height=number_format($data['height'], 1, '.', '');
						//		$order_number="PL".$ebayinputnameline[0];
						//		$tracking_code="";
						//		$description=str_replace(","," ",$ebayinputnameline[14]);
								//print_r($ebayoutputnametab);
								//$i=($j+1);
							$sql = 'select * from oc_product where product_id="'.$ebayoutputnametab[$i]['CustomLabel'].'"'; 
							//echo $sql."<br>";
							//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
							$req = mysqli_query($db,$sql);
							$data = mysqli_fetch_assoc($req);
						//	$ebayoutputnametab[$i]['locationentrepot']=str_replace('-','',$data['location']);
							$ebayoutputnametab[$i]['locationentrepot']=$data['location'];
							$ebayoutputnametab[$i]['quantiteentrepot']=$data['quantity'];

							$ebayoutputnametab[$i]['locationmagasin']='';
							$ebayoutputnametab[$i]['quantitemagasin']=$data['unallocated_quantity'];
						}else {
							$ebayoutputnametab[$i]['locationanc'] = '';
							$ebayoutputnametab[$i]['quantiteanc'] = '';
							$ebayoutputnametab[$i]['sku'] = '';
							$ebayoutputnametab[$i]['poids'] = '';
							$ebayoutputnametab[$i]['dimension'] = '';
							$ebayoutputnametab[$i]['image'] = '';
							$ebayoutputnametab[$i]['locationentrepot'] = '';
							$ebayoutputnametab[$i]['quantiteentrepot'] = '';
							$ebayoutputnametab[$i]['locationmagasin'] = '';
							$ebayoutputnametab[$i]['quantitemagasin'] = '';
						}
						$i++;
				//		echo "ebayoutputnametab";
				//		//print("<pre>".print_r ($ebayoutputnametab,true )."</pre>");
						mysqli_close($db);
					}
		//		}
			
			//$idarray++;
			}
			//echo $stockcode."<br>";
		}
	}
	//print("<pre>".print_r ($ebayoutputnametab,true )."</pre>");	
	//echo "<br>NB order:".$i;
	//print("<pre>".print_r ($ebayoutputnametab,true )."</pre>");
	$bgcolor="ffffff";
	if ($_POST['imp']==""){
		$html='<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />';
		$html.='

		<a href="updateorderfromebay.php?imp=oui"><strong>Imprimer le package slip</strong></a> ';
	
	}else{
		$html="";
	}
	$html.='<div class="form_description">';
	
	if(!isset($dir_name)){
		date_default_timezone_set("America/New_York");
		$pagenu=1;$itemcount=0;

	$html.='<hjo><label class="description" for="categorie">ORDERS: </label></hjo>';
	
	
		
	
	
	$html.='<table border="1" width="100%">
		<tr>
		<th bgcolor="ff6251">
		
		<input type="checkbox" onclick="toggleSelectAll(this)" value="Select All"> 
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
	$sortCriteria =
	array('SalesRecordNumber' => array(SORT_ASC, SORT_NUMERIC)
	);
  $ebayoutputnametab = MultiSort($ebayoutputnametab, $sortCriteria, true);
  $i=0;
 
	  foreach($ebayoutputnametab as $ebayoutputname) 
	  {	 

		  
	  if(isset($ebayoutputname['SalesRecordNumber']) && $ebayoutputname['SalesRecordNumber']!=""){

		  if ($bgcolor=="ffffff"){
			  $bgcolor="c0c0c0";
		  }else{
			  $bgcolor="ffffff";
		  }

		  $html.=' <tr>
			  <td bgcolor="'. $bgcolor.'">
			  <input type="checkbox" name="vendu[]" value="'. $ebayoutputname['CustomLabel'].','.$ebayoutputname['Quantity'].','.$ebayoutputname['quantiteentrepot'].','.$ebayoutputnametab[$i]['COM'].'"/>
			  '. $ebayoutputname['SalesRecordNumber'].'
			  </td>
			  <td bgcolor="'. $bgcolor.'">
			  '. $ebayoutputname['image'].'
			  </td>
			  <td bgcolor="'. $bgcolor.'">

				  '. $ebayoutputname['sku'].'
			  </td>
			  
			  <td bgcolor="'. $bgcolor.'">
			  '. $ebayoutputname['BuyerFullname'].'
			  </td>
			  <td bgcolor="'. $bgcolor.'">
			  '. $ebayoutputname['ItemTitle'].'
			  </td>
			  <td bgcolor="'. $bgcolor.'">$'. $ebayoutputname['Quantity']*$ebayoutputname['SalePrice'].'
			  </td>
			  
			  <td bgcolor="';
			  if($ebayoutputname['Quantity']>1){
				$html.=' "red"';
				}else{
					$html.= $bgcolor;
					}
					$html.='">';
			  if($ebayoutputname['Quantity']>1){
				$html.='<p style="color:white">';
				}
				$html.=$ebayoutputname['Quantity'];

				if($ebayoutputname['Quantity']>1){
					$html.='</p>';}
					$html.='</td>
			  </tr>';
  
  

$i++;
$itemcount++;
	  }
}
$html.='
</table></table>';


		
		$pagenu=1;
		$itemcount=0;

			$html.='
			<hjo><label class="description" for="categorie">INVENTAIRE: </label></hjo>';


	$html.='
	
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
	VENDU
	</th>
	
	</tr>';



		$sortCriteria =
		  array('sku' => array(SORT_ASC, SORT_NUMERIC)
		  );
		$ebayoutputnametab = MultiSort($ebayoutputnametab, $sortCriteria, true);
		$i=0;
			$temp_array = array();
			foreach($ebayoutputnametab as $ebayoutputname) 
			{	 
			
				$skuExists = false;
				foreach ($temp_array as $key => $item) {
		
					if (isset($item['sku']) && $item['sku'] == $ebayoutputname['sku']) {
						$temp_array[$key]['Quantity'] += $ebayoutputname['Quantity'];
						$skuExists = true;
						break; // Exit the loop once the SKU is found and updated
					}
				}
				
				// Si le SKU n'existe pas déjà dans $temp_array, ajoutez le nouvel élément
				if (!$skuExists) {
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
		
				if($ebayoutputname['SalesRecordNumber']!=""){
		
					if ($bgcolor=="ffffff"){
						$bgcolor="c0c0c0";
					}else{
						$bgcolor="ffffff";
					}
					$html.='
						<tr>
						<td bgcolor="'. $bgcolor.'">
								'. $ebayoutputname['image'].'
						</td>
						<td bgcolor="'. $bgcolor.'">

							'. $ebayoutputname['sku'].'
						</td>


						<td bgcolor="'. $bgcolor.'">
						'. $ebayoutputname['ItemTitle'].'
						</td>
		
						<td bgcolor="'. $bgcolor.'">
						'. $ebayoutputname['locationentrepot'].'
						</td>
						<td bgcolor="'. $bgcolor.'">
						'. $ebayoutputname['quantiteentrepot'].'
						</td>
						
						<td bgcolor="'. $bgcolor.'">
						'. $ebayoutputname['Quantity'].'
						</td>
						</tr>';
				
		
		$i++;
		$itemcount++;
				}
			
		}
		$html.='
</table>';

}

	echo $html;

	}
}
}?>


<script type="text/javascript">
<?
if ($_POST['imp']=="oui"){?>

		 window.//print();
		window.onafterprint = function(event) {
			window.location.href = 'updateorderfromebay.php'
		};

<?}?> // on ferme la connexion � mysql 

$(function() {
	  $(document).ready(function () {
		
	   var todaysDate = new Date(); // Gets today's date
		
		
		var year = todaysDate.getFullYear(); 						// YYYY
		var month = ("0" + (todaysDate.getMonth() + 1)).slice(-2);	// MM
		var day = ("0" + todaysDate.getDate()).slice(-2);			// DD

		var minDate = (year +"-"+ month +"-"+ day); // Results in "YYYY-MM-DD" for today's date 
		
		// Now to set the max date value for the calendar to be today's date
		$('.departDate input').attr('min',minDate);
	 
		  });
});
function toggleSelectAll(mainCheckbox) {
		var items = document.getElementsByName('vendu[]');
		for (var i = 0; i < items.length; i++) {
			if (items[i].type == 'checkbox') {
				items[i].checked = mainCheckbox.checked;
			}
		}
	}


</script>
<?

ob_end_flush();
?>