<?
$db = mysqli_connect("localhost","phoenkv5_store","Vivi1FX2Pixel$$","phoenkv5_store"); 
$connectionapi=array();
$connectionapi['EBAYTOKEN']='AgAAAA**AQAAAA**aAAAAA**KFaVYA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6wJk4GpDZSBoASdj6x9nY+seQ**Cz4GAA**AAMAAA**mMFDodKZkxSXmg2febmc1Ybt6sbInAYgoF299xPZN72w4FUHzYmSgZpu2PQC2/d1igEYyW/Ed8WoblRrbCZ9TzCiEfqcZrWFL8F4FlpvwJrVOrc4mPn0wQOMXbihNXvhcsBdkEgXYRbDdR9qqPMNPvbNjJt8Qdqyy1p5YPgCBAtXYYf6TT0hfhulb1QNO2lGAMFBG+eT24zBFsqV8KfdGZlhNsy7gQFgVoTTDLAxi1H0kb6uf0O7D1XyKibf/zgaws0INjiOb3y9gCr4VmgaqR+JvuQXrCv8TQOWdG4b03zKyIh85hG5bh2dZK9j4qoXw0QLjdO2Aaqi8yaNi7zACwWbLZFii8IqK0kI6U5Q1o9koX2pD9Ij0rpQ/aFg02GVH9BGk4J7T0E+fyyHpSDhF0oXp+ft/3i4FBZ6TPErMDhNLNN4vwBSsFxAGonHEpwsTy+wXlXQZ3WZ6UXQZIcvcRpSghKS0psspxrXpKMlYbU/aUGFLM8rbTLnwGDGUZFYsWg/E1ni+pM8mQ+Zg36puNZC+tiHag/nH3DPSXIcagYdNXN3QCwtm+tJDFofIf6wYGPiJ6LVlBYEt4yBUjFbimKtTBtWrXjMruHyolxDCigg35hLqf7dfnwxgvOxIRv9B8e5t5/EIeyFhGgSFyAR/iHGUL0DolKRYzOkw05Ef2WZBodHrXKHHGp+PU4z3ygXmskm6G6Qm9tlKPpBgmgIE8I1/J8rOeoRbk0Ab+hzBTWXU6hsqeoPYOSKIMiTiC5+';
$connectionapi['EBAYTOKENNEW']='AgAAAA**AQAAAA**aAAAAA**X2quYA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AHkIKmAJWAowSdj6x9nY+seQ**Cz4GAA**AAMAAA**djnbCtzpxegQw91AW0Z88mjXID6FJGInAbgAo43hm3OkU+PYTd1PcUB12eSLOX10ABFkgKC7dBGXjuBwGrfovRyGcSNg1xePTSQKHMKIFSddar1M91r7hoTaXeVXqUZcFKwLj5gJRw7SCeE3UnD/dJPJXtf3fcXd+w5ygiTlXpw3yA7PrOlZBFkvUAZ8by7gxRoJEpt4RRgm0UJ1zE7APE6xPruXWNTUP3k6Eh7j0rOXQYH+PEtAnXVj6k6TznGCSDI4LJXeTCGiNAxUlqRk9AkJNwYwS2r7YgX8Zi8rXiIjUb9zdo9u0e5WPF9P+NPWrjky3LIadsx/GDh8VbQj2nbFShZpSAfyl7/PXbhsNmyL4601wsUFC3oV1+L87ougZYnsfQVrRUZp2PO+HehonOGsDMoe6S+sA3iyjaYDiPA9GU7JK2mnd7KH7tXRwlu66lMnot2l3gTl7aWyIrLJCnWdZlx0EX3IMZb4F+Bsf3DFHCRpwDzgoovVY+fwbKjchvAscg0lkGFZVWhtMw588ckJcol9EHDd2ErHNaFS1ux7bElbgxTw9u9Hp6TEqdejfa2x+O1rR26Je5dp0Rr/cSOG2iVfBYGv9NYVCFc16xh/47hiV/QE9zJCAq5IhQzA/sjnV5BJ1hYgifB+2ANNPPUwyEOIzazC01cH6ML5WXlhc9CUMM9JwIpvPBzVEsVeOvOqWmRqHw3eMx5CIm6cfvB44O2dbjiDJmcI4Jdtxpd6F39J7e90DDxTAc8eRAnR';
$connectionapi['EBAYAPIDEVNAME']='73b8492a-f471-4170-86b8-ce9e6e2d6796';
$connectionapi['EBAYAPIAPPNAME']='73b8492a-f471-4170-86b8-ce9e6e2d6796';
$connectionapi['APICERTNAME']='PRD-d10eaf1ba793-d52a-46e3-919b-b4ec';
$connectionapi['APIEBAYURL']='https://api.ebay.com/ws/api.dll';

	$sql = "SELECT * FROM `oc_product` WHERE `ebay_id_old` NOT LIKE '0' AND `ebay_id_old` NOT LIKE 'ERREUR' AND usa<5 order by product_id DESC LIMIT 500 ";
	//$sql = 'SELECT *,P.product_id AS product_id, PD.name AS name_en, PD.color AS color_en, PD.description_supp AS description_supp_en,PD.condition_supp AS condition_supplementaire,P.price AS priceretail,PS.price AS price_magasin, P.image AS image_product,P.quantity, C.name AS condition_name,M.name AS brand FROM `oc_product` P LEFT JOIN `oc_product_description` PD ON (P.product_id=PD.product_id) LEFT JOIN `oc_product_special` PS ON (P.product_id=PS.product_id) LEFT JOIN `oc_manufacturer` M ON (M.manufacturer_id=P.manufacturer_id) LEFT JOIN `oc_condition` C ON (P.condition_id=C.condition_id AND C.language_id=1) where P.ebay_id_old=0 AND P.quantity>1 AND `ebay_id` NOT LIKE "" AND PD.language_id=1 ORDER BY `P`.`price_with_shipping` DESC limit 500';
	$req = mysqli_query($db,$sql);
			
			//echo $sql;
			while($data_import = mysqli_fetch_assoc($req))
			{
				$ebayresult=get_ebay_product($connectionapi,$data_import['marketplace_item_id']);
				//$json = json_decode($ebayresult, true);
				//print("<pre>".print_r ($json,true )."</pre>");
				$json=end_to_ebay($connectionapi,$data_import['ebay_id_old']);
				if(isset($json['Ack'])&& $json['Ack']=="Success"){
					$new_ebay_id=$json['ItemID'];
					$sql2 = 'UPDATE `oc_product`SET usa="5",ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$data_import['product_id'];
					echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2);
					echo $data_import['product_id']."<br>";
				}else{
					$sql2 = 'UPDATE `oc_product`SET usa="9",ebay_last_check="2020-09-01" WHERE `oc_product`.`product_id` ='.$data_import['product_id'];
					$req2 = mysqli_query($db,$sql2);
					echo $data_import['product_id']."---non<br>";
				}
			unset($json);
		//revise_ebay_product($connectionapi,'294401135207',101,2,$db,"non");
			}


function end_to_ebay($connectionapi,$ebay_id) {
		
		//echo $updquantity."allo".$ebay_id;
		$post = '<?xml version="1.0" encoding="utf-8"?>
				<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
					<RequesterCredentials>
						<eBayAuthToken>'.$connectionapi['EBAYTOKEN'].'</eBayAuthToken>
					</RequesterCredentials>
					<ErrorLanguage>en_US</ErrorLanguage>
					<WarningLevel>High</WarningLevel>
					<EndingReason>Incorrect</EndingReason>
					<ItemID>'.$ebay_id.'</ItemID>
				</EndItemRequest>'; 

		$headers = array(
					"X-EBAY-API-COMPATIBILITY-LEVEL: 1077",
					"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
					"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
					"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
					"X-EBAY-API-CALL-NAME: EndItem",
					"X-EBAY-API-SITEID: 0" // 3 for UK
		);
		$connection = curl_init();
		curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
		curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
		curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($connection, CURLOPT_POST, 1);
		curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
		curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
		$result2 = curl_exec($connection);
			$err = curl_error($connection);
			curl_close($connection);

			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object  
				//echo $result."\nallo";
				$new = simplexml_load_string($result2);  
				// Convert into json 
				$result2 = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result2);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
				//echo $textoutput."\nallo"."<br>";
				$json = json_decode($result2, true);
			//print("<pre>".print_r ($json,true )."</pre>");
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
			/* 	$ebay_quantity=$json["Item"]["Quantity"];
				$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
				$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"]; */
				//echo $ebay_quantity."---".$Quantity_sold; 
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			if($json["Ack"]=="Failure"){
				return $json;//array_merge($json,json_decode($result, true));
			}else{
				return $json;
			} 
	}
function delete_photo($product_id,$product_image_id,$db){
		
		//echo"Images suprimés<br>"; 
		if($product_image_id=="" || $product_image_id=="principal"){
	 		$sql = 'SELECT * FROM `oc_product` where product_id = "'.$product_id.'" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req); 
			//echo $data['image']."<br>";
			unlink('/home/phoenkv5/public_html/cart/image/' . $data['image']);
			if($product_image_id==""){
				$sql = 'SELECT * FROM `oc_product_image` where product_id = "'.$product_id.'" ';
				//echo $sql."<br>";
				$req = mysqli_query($db,$sql);
				while($data = mysqli_fetch_assoc($req)){
					//echo $data['image']."<br>";
					unlink('/home/phoenkv5/public_html/cart/image/' . $data['image']);
				}	
				$sql = 'DELETE FROM `oc_product_image` where product_id = "'.$product_id.'" ';	
				//echo $sql."<br>";
				$req = mysqli_query($db,$sql);
			}
		}elseif($product_image_id!="" && $product_image_id!="principal"){
			$sql = 'SELECT * FROM `oc_product_image` where product_image_id = "'.$product_image_id.'" ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){
				//echo $data['image']."<br>";
				unlink('/home/phoenkv5/public_html/cart/image/' . $data['image']);
			}	
			$sql = 'DELETE FROM `oc_product_image` where product_image_id = "'.$product_image_id.'" ';	
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			
		}
}
function import_ebay($connectionapi,$ebay_id_a_cloner,$product_id,$db){
					//			$homepage = file_get_contents('https://www.ebay.com/itm/144120759446');
/* unlink('/home/phoenkv5/public_html/cart/admin/interne/testimage.txt');
link('/home/phoenkv5/public_html/cart/admin/interne/testimage.txt');
$fp = fopen('/home/phoenkv5/public_html/cart/admin/interne/testimage.txt', 'w'); */



				
				//echo count($tmp);
/* 				$result=get_ebay_product($connectionapi,$ebay_id_a_cloner);
				$json = json_decode($result, true);
				//print("<pre>".print_r ($json['Item']['ListingDetails'],true )."</pre>");
				echo  
				echo $json['Item']['ListingDetails']['ViewItemURL']; */
				//echo 'https://www.ebay.com/itm/'.$ebay_id_a_cloner;
				$homepage=file_get_contents('https://www.ebay.com/itm/'.$ebay_id_a_cloner);
				//echo $homepage;
/* 				fwrite($fp, $homepage); 
				unlink('/home/phoenkv5/public_html/cart/admin/interne/testimage2.txt');
link('/home/phoenkv5/public_html/cart/admin/interne/testimage2.txt');
$fp2 = fopen('/home/phoenkv5/public_html/cart/admin/interne/testimage2.txt', 'w'); */
				$tmp=explode('maxImageUrl":"',$homepage);
/* 				echo '<br>'.count($tmp); */
				//$j=(count($tmp)-1);
				$j=((count($tmp)-1)/3)+1;
					$tmp2=explode('","maxImageHeight"',$tmp[1]);
					$tmp3=str_replace('\u002F','/',$tmp2[0]); 
/* 					fwrite($fp2, $tmp3);
					fputs($fp2, "\n");
					
					echo filesize($tmp3); */
					//echo $tmp3."<br>".$j;
				upload_from_ebay($product_id,$tmp3,1,$db);
				//echo "ALLO<br>";		
				for($i=2;$i<$j;$i++){
					
					$tmp2=explode('","maxImageHeight"',$tmp[$i]);
					$tmp3=str_replace('\u002F','/',$tmp2[0]); 
					upload_from_ebay($product_id,$tmp3,0,$db);
/* 					fwrite($fp2, $tmp3);
					fputs($fp2, "\n");	 */	
					//echo $tmp3."ALLO<br>";					
				}	//echo " ebay<br>";

}
function upload_from_ebay($product_id,$piclink,$principal,$db){
	if(strpos($piclink,"jpeg")>0)$pos=".jpeg";
	if(strpos($piclink,"JPEG")>0)$pos=".jpeg";
	if(strpos($piclink,"png")>0)$pos=".png";
	if(strpos($piclink,"PNG")>0)$pos=".png";
	if(strpos($piclink,"jpg")>0)$pos=".jpg";
	if(strpos($piclink,"JPG")>0)$pos=".jpg";
	if(strpos($piclink,"gif")>0)$pos=".gif";
	if(strpos($piclink,"GIF")>0)$pos=".gif";
	//echo "upload_from_ebay".$pos."<br>";
	$uploads_dir = 'image/catalog/product';
	$sqldir = 'catalog/product';
	if ($piclink!="")$picexterne2=$piclink;	 
		// echo $picexterne2;
		 $picexterne2=str_replace(array("\r","\n", " "),"",$picexterne2);
		 $filterimage=explode("?",basename($picexterne2));
		 
		 $path=explode($pos,basename($filterimage[0]));
		 
		$ext=count($path)-1;

	 if(($piclink!="") ){ //|| $piclink!=""

		$image = file_get_contents($picexterne2);

						
						
						if($principal==1) {
							$rdproduct_id="pri".mt_rand ( 1 , 99 );
							$dir_name=$GLOBALS['SITE_ROOT']."/".$uploads_dir."/".$product_id.$rdproduct_id.$pos;
							$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$product_id.$rdproduct_id.$pos."' where product_id=".$product_id;
							
						}else{
							$rdproduct_id="sec".mt_rand ( 1 , 99 );
							$dir_name=$GLOBALS['SITE_ROOT']."/".$uploads_dir."/".$product_id.$rdproduct_id.$pos;
							$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$sqldir."/".$product_id.$rdproduct_id.$pos."')";
							
						}
		//echo $sql2."<br>";
		$req2=mysqli_query($db,$sql2);
		$imagepath='/home/phoenkv5/public_html/cart/image/catalog/product/'.$product_id.$rdproduct_id.$pos;

		//file_put_contents($imagepath, $image); //Where to save the image on your server
		 save_image($piclink, $imagepath);
		//echo '<br>'.$sql2;
	 }
	 return "catalog/product/".$product_id.$rdproduct_id.".".$path[$ext];
}
function save_image($inPath, $outPath) {
  $in  = fopen($inPath,  "rb");
  $out = fopen($outPath, "wb");

  while (!feof($in)) {
    $read = fread($in, 8192);
    fwrite($out, $read);
  }

  fclose($in);
  fclose($out);
}
function revise_ebay_product($connectionapi,$ebay_id,$product_id,$updquantity,$db,$export_photo_to_ebay) {
			$result2= product_description_ebay($connectionapi,$product_id,$db,$export_photo_to_ebay);
			//echo $result2;
					if (is_numeric($updquantity))
					{
						//echo "oui";
						$quantity='<Quantity>'.$updquantity.'</Quantity>';
						}
					$post = '<?xml version="1.0" encoding="utf-8"?>
							<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
							  <RequesterCredentials>
								<eBayAuthToken>'.$connectionapi['EBAYTOKENNEW'].'</eBayAuthToken>
							</RequesterCredentials>
								<ErrorLanguage>en_US</ErrorLanguage>
								<WarningLevel>High</WarningLevel>
							  <Item> 
								<ItemID>'.$ebay_id.'</ItemID>
								'.$result2.$quantity.'
								</Item>
							</ReviseItemRequest>';
$testpost= json_decode($post, true); 
//print("<pre>".print_r ($testpost,true )."</pre>");
//print_r($testpost);
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1149",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-CALL-NAME: ReviseItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			 
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			
			//curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			//curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, FALSE);
			$result = curl_exec($connection);
			$err = curl_error($connection);
			 curl_close($connection);
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
				//print_r($response);
				$new = simplexml_load_string($result);  
				// Convert into json 
				$result = json_encode($new); 
				
				$textoutput=str_replace("}","<br><==<br>",$result);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
				//echo $textoutput."\nallo"."<br>";  
				$json = json_decode($result, true); 
				//print("<pre>".print_r ($json,true )."</pre>");
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";

				//echo $ebay_quantity."---".$Quantity_sold;
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			return $result;
	}
function get_ebay_product($connectionapi,$ebay_id) {
			//print_r($connectionapi);
					$post = '<?xml version="1.0" encoding="utf-8"?>
			<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$connectionapi['EBAYTOKEN'].'</eBayAuthToken>
				</RequesterCredentials>
				 <IncludeItemCompatibilityList>true</IncludeItemCompatibilityList>
				<IncludeItemSpecifics>true</IncludeItemSpecifics>
				 <DetailLevel>ReturnAll</DetailLevel>
				
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
				<ItemID>'.$ebay_id.'</ItemID>
			</GetItemRequest>';
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-CALL-NAME: GetItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			//$result = json_encode($post); 
			 //print("<pre>".print_r ($post,true )."</pre>");
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$result = curl_exec($connection);
			$err = curl_error($connection);
			 curl_close($connection);
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
				$textoutput=str_replace("}","<br><==<br>",$result);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
				//echo $textoutput."\nallo"."<br>";
				$json = json_decode($result, true);
				//print("<pre>".print_r ($json,true )."</pre>");
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
function add_ebay_item($connectionapi,$result,$post,$db){
	//print("<pre>".print_r ($post,true )."</pre>");
	//$result=get_from_upctemp($post['upc']);
	//$result_upctemp=json_decode($result, true);

/* 	if($post['image_principal']!=""){
		$image_principal=$post['image_principal'];
	}elseif($post['lien_a_cloner']!=""){
		$image_principal=$post['lien_a_cloner'];
	}else{ */
		$image_principal="https://phoenixliquidation.ca/image/data/tempo.jpg";
/* 	} */
			$imagexml='<PictureDetails> 
						<GalleryType>Gallery</GalleryType>
						<GalleryURL><![CDATA['.$image_principal.']]></GalleryURL>';
					
						$imagexml.='<PictureURL><![CDATA['.$image_principal.']]></PictureURL>';			
					
					$imagexml.='</PictureDetails>';
		//echo $imagexml;
/* 		 if(isset($post['lien_a_cloner'])&&$post['lien_a_cloner']!=""){
			//echo $post['ebay_id_a_cloner'];
			//echo "link_to_download";
			$imagexml=get_image_link_for_ebay($connectionapi,$post['lien_a_cloner'],"",$db);
			
		} */

	
	$json = json_decode($result, true);
	//print("<pre>".print_r ($json,true )."</pre>"); 
			unset ($json['Timestamp'])	;
			unset ($json['Ack'])	;	
			unset ($json['Version'])	;
			unset ($json['Build'])	;	
			unset ($json['Item']['GiftIcon'])	;
			unset ($json['Item']['AutoPay'])	;
			unset ($json['Item']['BuyerProtection'])	;
			unset ($json['Item']['ApplicationData'])	;
			unset ($json['Item']['SubTitle'])	;
			unset ($json['Item']['ShipToLocations']);
			unset ($json['Item']['HideFromSearch']);
			unset ($json['Item']['ReasonHideFromSearch']);
			unset ($json['Item']['PaymentAllowedSite']);
			
			
			unset ($json['Item']['HitCounter'])	;
			unset ($json['Item']['ItemID'])	;
			unset ($json['Item']['BuyItNowPrice']);
			 unset ($json['Item']['ListingDesigner'])	;
			 /*
			unset ($json['Item']['ListingDetails']);	
				
			unset ($json['Item']['ConditionDescription']);
			unset ($json['Item']['ProductListingDetails']['BrandMPN']['MPN']);
			
			$json['Item']['ListingDuration']="GTC";	
			$json['Item']['ListingType']="FixedPriceItem";	
			$json['Item']['Location']="Champlain, New York";	
			unset ($json['Item']['PrimaryCategory']['CategoryName']); */
			unset ($json['Item']['PrivateListing']);
			$json['Item']['ProductListingDetails']['UPC']='DoesNotApply';
			$json['Item']['ProductListingDetails']['IncludeeBayProductDetails']="true";
			unset ($json['Item']['UUID']);
			//unset($json['Item']['ProductListingDetails']['BrandMPN']);
			$json['Item']['BuyerResponsibleForShipping']="false";

				//	$json['Item']['ProductListingDetails']['BrandMPN']['Brand']=$post['brand'];
				//	$json['Item']['ProductListingDetails']['BrandMPN']['MPN']="DoesNotApply";

			
			unset($json['Item']['PaymentMethods']);
			unset($json['Item']['PayPalEmailAddress']);
			/* $json['Item']['PaymentMethods']="PayPal";	
			$json['Item']['PayPalEmailAddress']="gervais.jonathan@phoenixsupplies.ca";	 */
			unset ($json['Item']['PrivateListing']);
			/* $json['Item']['Quantity'] = 1; */
			$json['Item']['Country']="US";
			$json['Item']['Currency']="USD";
			unset ($json['Item']['ReviseStatus']);
			unset ($json['Item']['ReservePrice']);
			unset ($json['Item']['Seller']) ;
			unset ($json['Item']['SellingStatus']) ; 
			unset ($json['Item']['ShippingDetails']) ;
			//$json['Item']['ShippingDetails']=;
			$json['Item']['Site'] = "US";
            $json['Item']['StartPrice'] = $post['price_with_shipping'];
			unset ($json['Item']['Storefront']) ;
			unset ($json['Item']['OutOfStockControl']);
		/* 	unset ($json['Item']['Title']); */
            $json['Item']['Storefront'] = Array
                (
                    'StoreCategoryID' => 1,
                    'StoreCategory2ID' => 0,
                    'StoreURL' => "https://stores.ebay.ca/phoenixliquidationcenter"
                );
				
			unset ($json['Item']['TimeLeft'])	;
			unset ($json['Item']['HitCount'])	;
			unset ($json['Item']['BestOfferDetails'])	;
			$json['Item']['BestOfferDetails']= Array
                (
                   // ['BestOfferCount'] => 0,
                    'BestOfferEnabled' => 'true'
                   // ['NewBestOffer'] => false
                );
			
			//unset ($json['Item']['SKU']);
			$json['Item']['SKU']="COM".$json['Item']['SKU'];
			$json['Item']['PostalCode']="12919";
			unset($json['Item']['DispatchTimeMax']);
			unset($json['Item']['ProxyItem']);
			unset($json['Item']['BuyerGuaranteePrice']);
			unset($json['Item']['BuyerRequirementDetails']);
			//$json['Item']['Title']=addslashes(substr($post['name'],0,80));
			unset($json['Item']['IntangibleItem']);
			unset ($json['Item']['ReturnPolicy']);
			//unset ($json['Item']['ItemSpecifics']);
			$json['Item']['SellerProfiles'] = Array
                (
                    'SellerShippingProfile' => Array
                        (
                            'ShippingProfileID' => '66521950019',
                            'ShippingProfileName' => 'Shipping'
                        ),

                    'SellerReturnProfile' => Array
                        (
                            'ReturnProfileID' => '66521947019',
                            'ReturnProfileName' => 'Return'
                        ) ,

                    'SellerPaymentProfile' => Array
                        (
                            'PaymentProfileID' => '135483622019',
                            'PaymentProfileName' => 'PayPal'
                        ) 

                )
			;
			
			unset ($json['Item']['TopRatedListing'])	;
			unset ($json['Item']['LocationDefaulted'])	;
			unset ($json['Item']['GetItFast'])	;
			unset ($json['Item']['eBayPlus'])	;
			unset ($json['Item']['eBayPlusEligible'])	;
			unset ($json['Item']['IsSecureDescription'])	;
			unset ($json['Item']['ProxyItem'])	;
			unset ($json['Item']['BuyerGuaranteePrice'])	;
			unset ($json['Item']['IntangibleItem'])	;
			unset ($json['Item']['RestrictionPerBuyer'])	;
			unset ($json['Item']['ShippingServiceCostOverrideList'])	;
			unset ($json['Item']['DiscountPriceInfo'])	;
			
			
			unset ($json['Item']['ConditionDisplayName'])	;
			unset ($json['Item']['QuantityAvailableHint'])	;
			unset ($json['Item']['QuantityThreshold'])	;
			unset ($json['Item']['PostCheckoutExperienceEnabled'])	;
			unset ($json['Item']['ShippingPackageDetails'])	;
			unset ($json['Item']['HideFromSearch'])	;
			unset ($json['Item']['ListingDetails'])	;
			unset ($json['Item']['ReservePrice'])	;
			unset ($json['Item']['Charity'])	;
			unset ($json['Item']['PictureDetails']);
			//unset ($json['Item']['PictureDetails']['PictureSource']);
			//$json['Item']['PictureDetails']['GalleryURL'] =$result['items'][0]['images'][0];
//echo $result['items'][0]['images'][0];
			if($post['product_id']>0){
				
				$imagexml.=product_description_ebay($connectionapi,$post['product_id'],$db,"non");
				//echo"allo".$imagexml;
			}else{
				$json['Item']['Description']="Not finish yet to be list DO NOT buy it!!!"	;
				/* $sql2 = 'SELECT conditions FROM `oc_conditions_to_category` CC LEFT JOIN `oc_conditions` AS C ON CC.conditions_id=C.conditions_id where CC.category_id= '.$post['category_id'];
			
				//echo "<br>".$sql2;
				$req2 = mysqli_query($db,$sql2);
				$data2 = mysqli_fetch_assoc($req2); */
				//echo $post['conditions'];
/* 				$conditions = json_decode($data2['conditions'], true);
			
				$etat=explode(",",$post['etat']); */
			//echo "ALLO".$etat[0];
				//$json['Item']['ConditionID']=$post['conditions_id'];

					

			}
			//$json['Item']['PictureDetails']['GalleryURL'] ="https://www.phoenixliquidation.ca/image/data/tempo.jpg";
			//$json['Item']['PictureDetails']['PictureURL'] ="https://www.phoenixliquidation.ca/image/data/tempo.jpg";
			unset ($json['Item']['BuyerRequirementDetails'])	;
			unset ($json['Item']['Variations'])	;

			$json['Item']['ItemSpecifics']['NameValueList'][]=  Array 
								(
									'Name' => 'Model',
									'Value' => $post['model']
								);

			if($post['color']!=""){
				$json['Item']['ItemSpecifics']['NameValueList'][]=  Array 
								(
									'Name' => 'Color',
									'Value' => $post['color']
								);
			}
			$json['Item']['ItemSpecifics']['NameValueList'][]=  Array 
								(
									'Name' => 'MPN',
									'Value' => "DoesNotApply"
								);
/* 				$json['Item']['ItemSpecifics']['NameValueList'][]=  Array 
								(
									'Name' => 'test',
									'Value' => $post['model']
								); */
								
				/* $json['Item']['ItemSpecifics']['NameValueList'][]=  Array 
								(
									'Name' => 'MPN',
									'Value' => $post['upc']
								); */
			
			


			//print("<pre>".print_r ($json,true )."</pre>");
			//print("<pre>".print_r ($json,true )."</pre>");
			$xml=array2xml($json['Item'], false);
			/**/
			//print_r($xml);

			//print("<pre>".print_r ($xml,true )."</pre>");
			$post = '<?xml version="1.0" encoding="utf-8"?>
			<AddItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">
				<RequesterCredentials>
					<eBayAuthToken>'.$connectionapi['EBAYTOKENNEW'].'</eBayAuthToken>
				</RequesterCredentials>
				 ';
			$xml=str_replace('<?xml version="1.0"?>',$post,$xml);
			$xml=str_replace('<StartPrice','<StartPrice currencyID="USD"',$xml);
			$xml=str_replace('</Item>',"",$xml);
				 $post =$xml.$imagexml.'</Item>
				<ErrorLanguage>en_US</ErrorLanguage>
				<WarningLevel>High</WarningLevel>
			</AddItemRequest >';
 unlink('/home/phoenkv5/public_html/phoenixliquidation/admin/interne/test/test.txt');
link('/home/phoenkv5/public_html/phoenixliquidation/admin/interne/test/test.txt');
$fp = fopen('/home/phoenkv5/public_html/phoenixliquidation/admin/interne/test/test.txt', 'w');
fwrite($fp, $post); 
			$headers = array(
						"X-EBAY-API-COMPATIBILITY-LEVEL: 1157",
						"X-EBAY-API-DEV-NAME: ".$connectionapi['EBAYAPIDEVNAME'],
						"X-EBAY-API-APP-NAME: ".$connectionapi['EBAYAPIAPPNAME'],
						"X-EBAY-API-CERT-NAME: ".$connectionapi['APICERTNAME'],
						"X-EBAY-API-CALL-NAME: AddItem",
						"X-EBAY-API-SITEID: 0" // 3 for UK
			);
			//$result = json_encode($post); 
			//print("<pre>".print_r ($post,true )."</pre>");
			$connection = curl_init();
			curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($connection, CURLOPT_URL, $connectionapi['APIEBAYURL']);
			curl_setopt($connection, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($connection, CURLOPT_POST, 1);
			curl_setopt($connection, CURLOPT_POSTFIELDS, $post);
			curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);
			$result2 = curl_exec($connection);
			$err = curl_error($connection);
			curl_close($connection);

			if ($err) {
				echo "cURL Error #:" . $err;
			} else {
				// Convert xml string into an object  
				//echo $result."\nallo";
				$new = simplexml_load_string($result2);  
				// Convert into json 
				$result2 = json_encode($new); 
				$textoutput=str_replace("}","<br><==<br>",$result2);
				$textoutput=str_replace("{","<br>==><br>",$textoutput);
				
				//echo $textoutput."\nallo"."<br>";
				$json = json_decode($result2, true);
			//print("<pre>".print_r ($json,true )."</pre>");
				//print_r($json["Package"]["Postage"]["CommercialRate"])."<br>";
				$ebay_quantity=$json["Item"]["Quantity"];
				$Quantity_sold=$json["Item"]["SellingStatus"]["QuantitySold"];
				$ebay_price=$json["Item"]["SellingStatus"]["CurrentPrice"];
				//echo $ebay_quantity."---".$Quantity_sold; 
				//$encodedSesssionIDString =rawurlencode ($sessionId);
				//echo $encodedSesssionIDString;
			}
			if($json["Ack"]=="Failure"){
				return $json;//array_merge($json,json_decode($result, true));
			}else{
				return $json;
			} 
} 
	
function array2xml($array, $xml = false){

    if($xml === false){
        $xml = new SimpleXMLElement('<Item/>');
    }

    foreach($array as $key => $value){
        if(is_array($value)){
         //   array2xml($value, $xml->addChild($key));
		 
			if($key=="NameValueList"){
				//array2xml($value, $xml->addChild($key['NameValueList']));
				//echo $key."<br>";
				foreach($value as $key=>$value1){
					unset($value1['Source']);
					//echo $value1['Name'];
					/* if($value1['Name']!="MPN"){ */
						if(is_array($value1['Value'])){
							foreach($value1['Value'] as $key=>$value2){
								
								
									$value3['Name']=$value1['Name'];
									$value3['Value']=$value2;
									array2xml($value3, $xml->addChild("NameValueList"));
								
							}
						}else{
							array2xml($value1, $xml->addChild("NameValueList"));
						}
					/* } */
				}
			}else{
				array2xml($value, $xml->addChild($key));
			} 
        } else {
/* 			if($key =="ItemSpecifics"){
				foreach($array2 as $key => $value){
			} */
				
            $xml->addChild($key, htmlspecialchars($value));
        }
    }

    return $xml->asXML();
}
function product_description_ebay($connectionapi,$product_id,$db,$inventaire){
		//pour alimenter la description du listing ebay
					$desc1= '<p>When the fiscal year ends, most governmental agencies buy new equipment, leaving used but still functional equipment for surplus. </p>
<p><b>PhoenixSupplies </b>is a business based in Canada and in USA that resells equipment acquired from government surplus, mainly to American and Canadian buyers. </p>
<p><b>Our goal: </b>Reduce the ecological footprint by giving a second chance to functional products, sold at the best price. Unless indicated otherwise, all of our products are tested and fully functional.</p>
<p><br><b>PhoenixSupplies INC. 
(CANADA):</b> <br>659 Boulevard Jean-Paul Vincent<br>Quebec, Canada, J4G 1R3
<br><b>PhoenixSupplies LLC 
(USA):</b> <br>100 Walnut ST, <br>Champlain, New York, USA, 12919</p><p>
</p>';

$desc2= '<p><b>US CUSTOMERS :</b><br>
					We use USPS in most cases or UPS when the item is heavier than 1 to 2 pounds.<br> 
					We ONLY ship from our Champlain, NY, USA location.
					<p><b>CANADIAN CUSTOMERS :</b> <br>
					We use different carriers with tracking (CanPar, Dicom, UPS, Purolator or FEDEX).<br>
					We know that shipping costs are expensive in Canada. But we\'ve cut our shipping costs a lot by paying the difference to provide good products to all Canadians.<br>
					<br>
					Please allow us 1-3 business days before see the status of the tracking number, because we normally take 1 business day to process your order.<br>
					<p><b>HOURS:</b>
					<br>Monday to Friday : 9am to 5pm EST (Eastern Standard Time)<br>
					Saturday to Sunday: Closed<br><br>
					<b>Please note that we are closed on Weekends.</b> <br>All messages sent during the weekend will be answered by the next business day. </p>
					<p><b>PhoenixSupplies LLC 
					(USA):</b> <br>100 Walnut ST, <br>Champlain, New York, USA, 12919
					<br><b>PhoenixSupplies INC. 
					(CANADA):</b> <br>659 Boulevard Jean-Paul Vincent<br>Quebec, Canada, J4G 1R3</p>
					
					';
$desc3= '<p><b>USA &amp; CANADIAN CUSTOMERS</b>- We offer a 30 days of the eBay Money Back Guarantee. If you have any problem regarding the item, please CONTACT US DIRECTLY. We will explain to you how to return it or find the best solution for you. 
					</p><p><strong>INTERNATIONAL CUSTOMERS</strong>- We love our international customers, but due to the high costs of international shipping and because of the long distance items have to travel, ALL ITEMS SHIPPED OUTSIDE of the USA and Canada ARE SOLD AS IS WITH NO GUARANTEES. All sales are FINAL. No refunds or returns for international customers.
					</p>';
$desc4= '<p>We are surplus equipment dealers and do not have all abilities or knowledge to test all equipment for functionality beyond plugging it in and describing what we observe as best as we can.</p>
 <p>All statements regarding products and their configurations are made to the best of our ability and with the assumption that the buyer is knowledgeable and proficient in the use of this type of equipment.</p>
 <p>We do not know everything about the history of the equipment. </p>
 <p>Due to the nature of used and surplus equipment, no guarantees or warranties are offered, unless specified in the description.</p>
 <p>If you receive a non-functional product and is not declared inside the description as a NON WORKING unit or sold for PARTS ; this product will be replaced or refunded at our expense if you contact us within 30 days of the eBay Money Back Guarantee. It is <b>GUARANTEE!</b></p>
 <p>REFUNDS OR RETURNS will be allowed unless specified in the description.</p>
 <p>No manuals, standard power cords, accessories, software or else are included unless pictured and specified in the description.</p>
 <p> It is the buyerss responsibility to understand the terms of the sale and the nature of the equipment offered.</p>';

$desc5= '<p>Paypal, CreditCard and DebitCard.
					</p><p><b>CANADIAN CUSTOMERS:</b>- We ship from our Canadian Warehouse, Canadians Taxes may apply.
					<br><b>US CUSTOMERS:</b> We do not charge taxes because eBay does. If you have any issue please contact eBay.
					</p>';

$list1= '<style type="text/css">
#SuperWrapper {
width: 800px;
margin-left: auto;
margin-right: auto;
font-family: arial, Helvetica, sans-serif;
font-size: 12px;
}
#SuperWrapper p {
margin: 0px;
padding: 0px 0px 15px 0px;
line-height: 20px;
}
#SuperWrapper h1 {
padding: 5px 0px 15px 0px;
margin: 0px;
font-size: 26px;
font-weight: bold;
letter-spacing: -1px;
color: #000000;
}
#SuperWrapper a {
font-weight: bold;
text-decoration: underline;
color: #990000;
}
#SuperWrapper a:hover {
text-decoration:none;
}
#SuperHeader {
width:800px;
height: 240px;
background-image:url(https://www.phoenixsupplies.ca/ebay/Header.jpg);
}
#SuperHeaderLogo {
padding: 60px 0px 59px 50px;
font-family: arial, Helvetica, sans-serif;
font-size: 50px;
letter-spacing: -3px;
font-weight: bold;
margin: 0px;
color: #FFFFFF;
text-shadow: 1px 1px 1px #000;
}
#SuperHeaderMenu {
margin: 0px;
}
#SuperHeaderMenu ul.navi{
padding: 0px;
margin: 0px 0px 0px 0px;
width: 800px;
text-align: center;
position: relative;
}
#SuperHeaderMenu ul.navi li{
height: 22px;
padding: 0 10px 0 10px;
margin: 0px;
display: inline;
}
#SuperHeaderMenu ul.navi li a{
padding: 0px 8px 0px 8px;
font: 18px arial, Helvetica, sans-serif;
color: #FFFFFF;
text-decoration: none;
text-indent: 0px;
font-weight: bold;
margin: 0;
width: inherit;
letter-spacing: -1px;
line-height: 30px;
}
#SuperHeaderMenu ul.navi li a:hover{
color: #DEE4ED;
}
#SuperContentsWrapper {
width: 800px;
background-image: url(https://www.phoenixsupplies.ca/ebay/Contents.jpg);
}
#SuperContents {
width: 800px;
background-image: url(https://www.phoenixsupplies.ca/ebay/ContentsTop.jpg);
background-repeat: no-repeat;
}
#SuperContentsSub {
padding: 30px 40px 0px 40px;
}
#SuperFooter {
width: 800px;
height: 44px;
background-image:url(https://www.phoenixsupplies.ca/ebay/Footer.jpg);
}
#SuperFooterLink {
width: 800px;
background-image:url(https://www.phoenixsupplies.ca/ebay/BG.jpg);
height: 100px;
}
#SuperBoxContents {
padding: 0px 80px 0px 80px;
margin: 0px;
}
#SuperBoxContents p {
padding: 0px 0px 10px 0px;
margin: 0px;
line-height: 20px;
}
#SuperBoxContents ul {
padding: 0px 0px 0px 28px;
margin: 0px;
list-style-type: disc;
}
#SuperBoxContents li {
line-height: 20px;
}
#SuperPayment {
width: 800px;
}
#SuperPaymentTop {
width: 800px;
height: 83px;
background-image:url(https://www.phoenixsupplies.ca/ebay/PaymentPolicyTop.jpg);
}
#SuperPaymentContents {
width: 800px;
background-image:url(https://www.phoenixsupplies.ca/ebay/PaymentPolicyContents.jpg);
}
#SuperPaymentBottom {
width: 800px;
height: 53px;
background-image:url(https://www.phoenixsupplies.ca/ebay/PaymentPolicyBottom.jpg);
}
#SuperShipping {
width: 800px;
}
#SuperShippingTop {
width: 800px;
height: 83px;
background-image: url(https://www.phoenixsupplies.ca/ebay/ShippingPolicyTop.jpg);
}
#SuperAboutTop {
width: 800px;
height: 83px;
background-image: url(https://www.phoenixsupplies.ca/ebay/AboutPolicyTop.jpg);
}
#SuperTermTop {
width: 800px;
height: 83px;
background-image: url(https://www.phoenixsupplies.ca/ebay/TermPolicyTop.jpg);
}
#SuperShippingContents {
width: 800px;
background-image:url(https://www.phoenixsupplies.ca/ebay/ShippingPolicyContents.jpg);
}
#SuperShippingBottom {
width: 800px;
height: 53px;
background-image:url(https://www.phoenixsupplies.ca/ebay/ShippingPolicyBottom.jpg);
}
#SuperContacts {
width: 800px;
}
#SuperContactsTop {
width: 800px;
height: 83px;
background-image:url(https://www.phoenixsupplies.ca/ebay/ContactsTop.jpg);
}
#SuperContactsContents {
width: 800px;
background-image:url(https://www.phoenixsupplies.ca/ebay/ContactsContents.jpg);
}
#SuperContactsBottom {
width: 800px;
height: 53px;
background-image:url(https://www.phoenixsupplies.ca/ebay/ContactsBottom.jpg);
}
#SuperReturns {
width: 800px;
}
#SuperReturnsTop {
width: 800px;
height: 83px;
background-image:url(https://www.phoenixsupplies.ca/ebay/ReturnsTop.jpg);
}
#SuperReturnsContents {
width: 800px;
background-image:url(https://www.phoenixsupplies.ca/ebay/ReturnsContents.jpg);
}
#SuperReturnsBottom {
width: 800px;
height: 53px;
background-image:url(https://www.phoenixsupplies.ca/ebay/ReturnsBottom.jpg);
}
/* HTML5 ELEMENTS */
/* sub images > thumbnail list */
ul#SuperThumbs, ul#SuperThumbs li {
margin: 0;
padding: 0;
list-style: none;
}
ul#SuperThumbs li {
float: left;
background: #ffffff;
border: 1px solid #cccccc;
margin: 0px 0px 10px 10px;
padding: 8px;
-moz-border-radius: 10px;
border-radius: 10px;
}
ul#SuperThumbs a {
float: left;
display: block;
width: 150px;
height: 150px;
line-height: 100px;
overflow: hidden;
position: relative;
z-index: 1;
}
ul#SuperThumbs a img {
float: left;
width: 100%;
height: 100%;
border: 0px;
}
/* sub images > mouse over */
ul#SuperThumbs a:hover {
overflow: visible;
z-index: 1000;
border: none;
}
ul#SuperThumbs a:hover img {
background: #ffffff;
border: 1px solid #cccccc;
padding: 10px;
-moz-border-radius: 10px;
border-radius: 10px;
position: absolute;
top:-20px;
left:-50px;
width: auto;
height: auto;
}
/* sub images > clearing floats */
ul#SuperThumbs:after, li#SuperThumbs:after {
content: ".";
display: block;
height: 0;
clear: both;
visibility: hidden;
}
ul#SuperThumbs, li#SuperThumbs {
display: block;
}
ul#SuperThumbs, li#SuperThumbs {
min-height: 1%;
}
* html ul#SuperThumbs, * html li#SuperThumbs {
height: 1%;
}
</style>
<div id="SuperWrapper">
<div id="SuperHeader">
<div id="SuperHeaderLogo"><br></div>
<div id="SuperHeaderMenu">

<ul class="navi">
					<li><a href="https://stores.ebay.com/phoenixliquidationcenter">Other Items</a></li>
					<li><a href="https://feedback.ebay.com/ws/eBayISAPI.dll?ViewFeedback2&userid=phoenixliquidationcenter&ftab=AllFeedback&myworld=true&rt=nc&_trksid=p2545226.m2531.l4585">Feedbacks</a></li>
					<li><a href="https://members.ebay.com/ws/eBayISAPI.dll?ViewUserPage&amp;userid=phoenixliquidationcenter">About Us</a></li>
					<li><a href="https://contact.ebay.com/ws/eBayISAPI.dll?FindAnswers&frm=284&requested=phoenixliquidationcenter&iid=-1">Contact Us</a></li>
					<li><a href="https://my.ebay.com/ws/eBayISAPI.dll?AcceptSavedSeller&amp;ru=http%3A//cgi.ebay.com/ws/eBayISAPI.dll?ViewItemNext&amp;item=330478824623&amp;mode=0&amp;ssPageName=STRK:MEFS:ADDVI&amp;SellerId=phoenixliquidationcenter&amp;preference=0&amp;selectedMailingList_4487562=false">Add To Favorites</a></li>
</ul>

</div>
</div>
<div id="SuperContentsWrapper">
<div id="SuperContents">
<div id="SuperContentsSub">';
$list2= '</div>
<div id="SuperShipping">
<div id="SuperAboutTop"></div>
<div id="SuperShippingContents">
<div id="SuperBoxContents">';
$list3= '
</div>
</div>
<div id="SuperShipping">
<div id="SuperShippingTop"></div>
<div id="SuperShippingContents">
<div id="SuperBoxContents">';
$list4= '</div>
</div>
<div id="SuperShippingBottom"></div>
</div>
<div id="SuperContacts">
<div id="SuperContactsTop"></div>
<div id="SuperContactsContents">
<div id="SuperBoxContents">';
$list5= '</div>
</div>
<div id="SuperShippingBottom"></div>
</div>
<div id="SuperContacts">
<div id="SuperTermTop"></div>
<div id="SuperContactsContents">
<div id="SuperBoxContents">';
$list6= '</div>
</div>
<div id="SuperContactsBottom"></div>
</div>

<div id="SuperReturns">
<div id="SuperReturnsTop"></div>
<div id="SuperReturnsContents">
<div id="SuperBoxContents">';
$list7= '</div>
</div>
<div id="SuperReturnsBottom"></div>
</div>

</div>
</div>
<div id="SuperFooter"></div>
<div id="SuperFooterLink">
<p align="center">
</p>
</div>
</div>';  


	
// on crée la requête SQL
 $sql = 'SELECT *,P.image as image_princ,P.sku,P.price AS priceretail  FROM `oc_product` AS P LEFT JOIN `oc_product_description`AS PD ON P.product_id=PD.product_id where language_id=1 and P.product_id="'.$product_id.'"';// limit 2';// and product_id=1312';// ';
//echo $sql;
// on envoie la requête
$req = mysqli_query($db,$sql);

$data = mysqli_fetch_assoc($req);


				$System_Model=$data['model'];						
				$Part_Mfg=$data['mpn'];		
				$Upc='DoesNotApply';		
				$Color=$data['color'];				
				$Part_Number='';	
				$Ean=$data['ean'];
				$Action='*Action(SiteID=US|Country=US|Currency=USD|Version=745|CC=UTF-8)';
				$Listing_Type='equipment';	
				$Request_Type='ADD';	
				//echo $data['product_id'];
				$Price=($data['price_with_shipping']/.75);// 
				$PriceSite=($data['price_with_shipping']);
				if($data['status']==1 && $inventaire=="")$price_ok='<StartPrice currencyID="USD">'.$PriceSite.'</StartPrice>';
				$D2D_Price=$data['price']*.85;	
				$VTS_Friend_Price=$data['price']*.85;	
				$Currency='US Dollars';	
				$Quantity=$data['quantity'];	
				$Paypal_Activate='y';	
				$Paypal_Shipping='';	
				$Make_an_Offer='n';	
				$Weight=$data['weight'];	
				$Weight_Units='lb';	
				$Height=$data['height'];	
				$Width=$data['width'];	
				$Depth=$data['length'];	
				$Item_ID=$data['mpn'];	
				$Item_ID_Type='MPN';	
				$CustomLabel="COM_".$data['product_id'];	
				$Location=$data['location'];
				$Name=$data['name'];
/* 		if($data['quantity_total']<($data['quantity']+$data['unallocated_quantity'])){
			$updquantity=$data['unallocated_quantity'];
		}else{
			$updquantity=$data['quantity']+$data['unallocated_quantity'];
		} */
				
				$Image_1='https://www.phoenixsupplies.ca/image/'.$data['image_princ'];
				
				$line=$data['description'];

				$listing_description=$list1.$line.$list2.$desc1.$list3.$desc2.$list4.$desc3.$list5.$desc4.$list6.$desc5.$list7;
				$listing_description=str_replace(array("\r","\n"),"",$listing_description);
				$listing_description=html_entity_decode($listing_description, ENT_QUOTES);
				$listing_description=urldecode($listing_description);
				$listing_description="<![CDATA[" .$listing_description. "]]>";
				//
				//

								$Images='';	
								$Price=number_format($Price,2); // augmenter le prix
								$sql2 = "SELECT * FROM oc_product_image where product_id='".$product_id."'";
								$req2= mysqli_query($db,$sql2); 
								$i=1;
				$WeightTot=array();
				$Weight=floatval($Weight);
				$WeightTot=explode('.', $Weight);
				$WeightOZ=intval(($Weight-$WeightTot[0])*16);
				$result="";
				//if($inventaire==""){
					$result.="<Title>".htmlspecialchars(addslashes($Name))."</Title>";
					$result.='<PictureDetails>
					<GalleryType>Gallery</GalleryType>
					<GalleryURL>'.addslashes($Image_1).'</GalleryURL>
					 <PhotoDisplay>PicturePack</PhotoDisplay>
									  <PictureURL>'.addslashes($Image_1).'</PictureURL>';
									while($data2 = mysqli_fetch_assoc($req2))
									{
										if($i<13){
											$result.=addslashes('<PictureURL>https://www.phoenixsupplies.ca/image/'.$data2['image'].'</PictureURL>');//Image_'.$j.'>';
											
										$i++;
										}
									}
					$result.='
									</PictureDetails>
									 <Description>'.$listing_description.'</Description>
									<SKU>COM_'.$CustomLabel.'</SKU>'.$price_ok;;
				//}
							
				$result.='				<ShippingPackageDetails> 
								  <MeasurementUnit>English</MeasurementUnit>
								  <PackageDepth>'.$Depth.'</PackageDepth>
								  <PackageLength>'.$Height.'</PackageLength>
								  <PackageWidth>'.$Width.'</PackageWidth>
								  <WeightMajor>'.$WeightTot[0].'</WeightMajor>
								  <WeightMinor>'.$WeightOZ.'</WeightMinor>
								</ShippingPackageDetails>';
								
								//
									/* <ShippingIrregular> boolean </ShippingIrregular>
								  <ShippingPackage> ShippingPackageCodeType </ShippingPackage> */
		
		return $result;
		
	}	
?>
<html>
<head><meta content="text/html; charset=ISO-8859-1" http-equiv="content-type">
<meta http-equiv="refresh" content="30">
    <title></title>


</head>
<body bgcolor="ffffff">
</body>
</html>

<? 

// on ferme la connexion &agrave; mysql 
mysqli_close($db); ?>