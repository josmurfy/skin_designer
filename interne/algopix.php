<?
include 'connection.php';
ob_start();
$i=0;
$itemcount =0;
$bgcolor="#FFFFFF";
include $GLOBALS['SITE_ROOT'].'interne/translatenew.php';
require_once '/home/n7f9655/public_html/canuship/vendor/autoload.php';
//print("<pre>".print_r ($_POST,true )."</pre>");
//echo "oui".$_POST['majname'][26664];
//echo $_POST['name']['24415'];
//print("<pre>".print_r ($_POST['name']['24415'],true )."</pre>");
$testnb=0;
$totalsale=0;
if(isset($_FILES['importfile']['name']) && $_FILES['importfile']['name']!="")
{
	//echo "allo14";
	$filename = $_FILES['importfile']['name'];

//print("<pre>".print_r ($_POST,true )."</pre>");
	/* Choose where to save the uploaded file */
	$location = "/home/n7f9655/public_html/phoenixliquidation/interne/upload/".$filename;
	/* Save the uploaded file to the local filesystem */
	if (move_uploaded_file($_FILES['importfile']['tmp_name'], $location) ) { 
		$json['success']="success" ;
		//echo $filename;
		readfilexls($location,$db);
		//readfilexls("/home/n7f9655/public_html/phoenixliquidation/interne/upload/Classeur6.xlsx"
		unlink($location);
	} else { 
		$json['success']= 'Failure'; 
	}
echo $json['success'];
	//$json['success']=$results;
//	$this->response->addHeader('Content-Type: application/json');
//	$this->response->setOutput(json_encode($json));	
	//echo "uploaded";
}elseif(isset($_POST['remove'])){
	echo "allo36";
	foreach($_POST['product_id'] as $product){
        $data=explode(',',$product); 
		$product_id=$data[0];
        $sql2 = "UPDATE `oc_algopix` SET `verif_fait`='1'";
		$sql2 .=" WHERE product_id='".$product_id."'";
		echo $sql2."<br>";
		$req2 = mysqli_query($db,$sql2);
       
	}
}elseif(isset($_POST['product_id']) && isset($_POST['feed']) && (!isset($_POST['sku']) || $_POST['sku']=='' )){//&& !isset($_POST['majname'])
	//echo "allo54";
//	//print("<pre>".print_r ($product,true )."</pre>"); 
	foreach($_POST['product_id'] as $product){
		$testnb++;
		$data=explode(',',$product); 
		$upc=$data[1];
		$product_id=$data[0];
		if(!isset($_POST['brand'])){
			$_POST['brand']=array();
			//print("<pre>" . print_r($_POST, true) . "</pre>");
		}

		if(!isset($_POST['brand'][$product_id])){
			$_POST['brand'][$product_id]=' ';
			//print("<pre>" . print_r($_POST, true) . "</pre>");
		}

		//	//print("<pre>".print_r ($_POST,true )."</pre>"); 
		//echo $_POST['manufacturersupp'][$product_id];
		if(isset($_POST['manufacturersupp'][$product_id])&& $_POST['manufacturersupp'][$product_id]!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.addslashes(strtoupper($_POST['manufacturersupp'][$product_id])).'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			$_POST['manufacturer_id'][$product_id]= mysqli_insert_id($db);
			$_POST['brand'][$product_id]=$_POST['manufacturersupp'][$product_id];
		}elseif(isset($_POST['manufacturer_id'][$product_id]) && $_POST['manufacturer_id'][$product_id]!=""){
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'][$product_id];
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$_POST['brand'][$product_id]=$data2['name'];
		}elseif(isset($_POST['manufacturer_recom'][$product_id]) ){
		    $_POST['manufacturer_id'][$product_id] = $_POST['manufacturer_recom'][$product_id];
			$sql2 = 'SELECT * FROM `oc_manufacturer` WHERE manufacturer_id=' . $_POST['manufacturer_id'][$product_id];
			// on envoie la requête
			$req2 = mysqli_query($db, $sql2);
			$data2 = mysqli_fetch_assoc($req2);
		//	//print("<pre>" . print_r($_POST, true) . "</pre>");
			//echo $data2['name'];
			$_POST['brand'][$product_id] = isset($data2['name']) ? $data2['name'] : '';
			
		}
		if($_POST['marketplace_item_id'][$product_id]!=""){
			$ebay_id=" `ebay_id` = '".$_POST['marketplace_item_id'][$product_id]."' ,";
		}else{
			$ebay_id="";
		}
		$options = isset($_POST['options'][$product_id]) ? $_POST['options'][$product_id] : [];
		$options_string = implode(', ', $options);
		
		$_POST['name'][$product_id]=$_POST['name'][$product_id]." ".$options_string ;

		if(isset($_POST['majname'][$product_id])){


			$_POST['name'][$product_id]=$_POST['majname'][$product_id];
		}
		$sql2 = "UPDATE `oc_algopix` SET 
		`model` = '".format_string(isset($_POST['model'][$product_id]) ? $_POST['model'][$product_id] : "")."',
		`color` = '".strtoupper(isset($_POST['color'][$product_id]) ? $_POST['color'][$product_id] : "")."',
		`name` = '".addslashes(format_string(isset($_POST['name'][$product_id]) ? $_POST['name'][$product_id] : ""))."',
		`manufacturer_id` = '".(isset($_POST['manufacturer_id'][$product_id]) ? '0'.$_POST['manufacturer_id'][$product_id] : "")."',
		`image` = '".(isset($_POST['image'][$product_id]) ? $_POST['image'][$product_id] : "")."',
		`brand` = '".(isset($_POST['brand'][$product_id]) ? $_POST['brand'][$product_id] : "")."',
		`upc` = '".$upc."',
		`weight` = '.25',
		".$ebay_id."
		`height` = '".(isset($_POST['height'][$product_id]) ? $_POST['height'][$product_id] : "")."',
		`price` = price + ".(isset($_POST['price'][$product_id]) ? $_POST['price'][$product_id] : 0).",
		`width` = '".(isset($_POST['width'][$product_id]) ? $_POST['width'][$product_id] : "")."',
		`length` = '".(isset($_POST['length'][$product_id]) ? $_POST['length'][$product_id] : "")."',
		date_update = now()
		WHERE product_id = '".$product_id."' OR upc = '".$upc."'";
	
	//	echo $sql2."<br>";
		$req2 = mysqli_query($db,$sql2);
	}
}elseif(isset($_POST['product_id']) && isset($_POST['FindTitle'])){
	echo "allo96";
	foreach($_POST['product_id'] as $product){
		$testnb++;
		$data=explode(',',$product); 
		$upc=$data[1];
		$product_id=$data[0];
		if(!isset($_POST['brand'][$product_id]))
			$_POST['brand'][$product_id]='';
		if($_POST['ebay_id_hidden'][$product_id]>0){
			$result=get_ebay_product($connectionapi,$_POST['ebay_id_hidden'][$product_id]);
			$json = json_decode($result, true);
		//	echo $_POST['condition_id'][$product_id];
			$result_ebay=find_bestprice_ebay($connectionapi,$_POST['name'][$product_id],$upc,1,1,'');
//echo $connectionapi,$_POST['name'][$product_id];
//echo $upc;
		//	//print("<pre>".print_r ($result_ebay,true )."</pre>"); 
		//	$pricevariant=json_encode($result_ebay['pricevariant']);
			$pricevariant=(json_encode($result_ebay['pricevariant']))!==null?json_encode($result_ebay['pricevariant']):0;
			if($_POST['brand_hidden'][$product_id]==""){
				foreach($json['Item']['ItemSpecifics']['NameValueList'] as $itemspecific){
					if(isset($itemspecific['Name']) && $itemspecific['Name']=='Studio'){
						$_POST['brand'][$product_id]= $itemspecific['Value'];
						$_POST['brand_hidden'][$product_id]=$itemspecific['Value'];
					}
				}
			}
			if(isset($_POST['brand'][$product_id]) && $_POST['brand'][$product_id]==""){
				$_POST['brand'][$product_id]=$result_ebay['brand'];
			}
			if(!isset($json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']))
			$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']=$json['Item']['ShippingDetails']['ShippingServiceOptions'][0]['ShippingServiceCost'];
			//echo "SurEBAY".($json['Item']['StartPrice']+$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']);
			//	echo "Algopix".$_POST['algopix_price_hidden'][$product_id];
			if(($json['Item']['ListingDetails']['ConvertedStartPrice']
			+$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost'])
			>$_POST['algopix_price_hidden'][$product_id]){
				echo "SurEBAY".($json['Item']['ListingDetails']['ConvertedStartPrice']+$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']);
				echo "Algopix".$_POST['algopix_price_hidden'][$product_id];
				$_POST['price'][$product_id]=$json['Item']['ListingDetails']['ConvertedStartPrice']+$json['Item']['ShippingDetails']['ShippingServiceOptions']['ShippingServiceCost']-$_POST['algopix_price_hidden'][$product_id];
			}
		
			if(isset($_POST['majname'][$product_id])){
				//echo "oui".$_POST['majname'][$product_id];
				$_POST['name'][$product_id]=$_POST['majname'][$product_id];
			}
			$_POST['ebayname'][$product_id]=$result_ebay['name'];
		}
		//echo $_POST['manufacturersupp'][$product_id];
		if(isset($_POST['manufacturersupp'][$product_id])&& $_POST['manufacturersupp'][$product_id]!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.addslashes(strtoupper($_POST['manufacturersupp'][$product_id])).'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			$_POST['manufacturer_id'][$product_id]= mysqli_insert_id($db);
			$_POST['brand'][$product_id]=$_POST['manufacturersupp'][$product_id];
		}elseif(isset($_POST['manufacturer_id'][$product_id]) && $_POST['manufacturer_id'][$product_id]!=""){
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'][$product_id];
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$_POST['brand'][$product_id]=$data2['name'];
		}elseif(isset($_POST['manufacturer_recom'][$product_id]) ){
			$_POST['manufacturer_id'][$product_id]=$_POST['manufacturer_recom'][$product_id];
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'][$product_id];
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$_POST['brand'][$product_id]=$data2['name'];
		}
		if($_POST['marketplace_item_id'][$product_id]!=""){
			$ebay_id=", `ebay_id` = '".$_POST['marketplace_item_id'][$product_id]."' ";
		}else{
			$ebay_id="";
		}
		$pricevariant=0;
		$sql2 = "UPDATE `oc_algopix` SET `model`='".format_string($_POST['model'][$product_id])."',`color`='".strtoupper($_POST['color'][$product_id])."'";
		$sql2 .=", `name` = '".addslashes(format_string($_POST['name'][$product_id]))."',`manufacturer_id` = '0".$_POST['manufacturer_id'][$product_id]."',
		`image`='".$_POST['image'][$product_id]."',`brand` = '".$_POST['brand'][$product_id]."'";
		$sql2 .=$ebay_id.",`upc`='".$upc."', `weight`='".$_POST['weight'][$product_id]."',`height`='".$_POST['height'][$product_id]."',`price`=price+".$_POST['price'][$product_id]."";
		$sql2 .=",pricevariant='".$pricevariant."', `width`='".$_POST['width'][$product_id]."',`length`='".$_POST['length'][$product_id];
		$sql2 .="',date_update=now() WHERE product_id='".$product_id."' or upc='".$upc."'";
		//echo $sql2."<br>";
		$req2 = mysqli_query($db,$sql2);
	}
}elseif(isset($_POST['majname'])){
	echo "allo177";
	foreach($_POST['majname'] as $product_id => $name){
		$name = addslashes(format_string($name)); // Préparer le nom du produit
		$sql2 = "UPDATE `oc_algopix` SET `name` = '$name' WHERE product_id = '$product_id' OR upc = '$upc'";
		$req2 = mysqli_query($db, $sql2);
	
		if ($req2) {
			echo "Mise à jour réussie pour le produit avec l'ID : $product_id\n";
		} else {
			echo "Échec de la mise à jour pour le produit avec l'ID : $product_id\n";
		}
	}
}elseif(isset($_POST['product_id']) && isset($_POST['upctmp'])){
	echo "allo196";
	foreach($_POST['product_id'] as $product){
		$testnb++;
		$data=explode(',',$product); 
		$upc=$data[1];
		//echo $upc;
		$product_id=$data[0];
				//$_POST['manufacturer_id'][$product_id]=0;
				$_POST['upctemp'][$product_id]=get_from_upctemp($upc);
				$result_upctemp=json_decode($_POST['upctemp'][$product_id],true);
				//print("<pre>".print_r ($result_upctemp,true )."</pre>");
			if($_POST['name'][$product_id]=="" || !isset($_POST['name'][$product_id])){
				$_POST['name'][$product_id]=addslashes($result_upctemp['items'][0]['title']);
				$_POST['name'][$product_id]=str_replace('Bilingual','Canadian Release',$_POST['name'][$product_id]);
		//echo "upc";
			}	
			if($_POST['brand_hidden'][$product_id]=="" && $_POST['manufacturer_id'][$product_id]<1){
				$_POST['brand'][$product_id]=$result_upctemp['items'][0]['brand'];
				$_POST['brand_hidden'][$product_id]=$result_upctemp['items'][0]['brand'];
			}
			if($_POST['brand'][$product_id]==""){
				$_POST['brand'][$product_id]=$result_ebay['brand'];
			}
				if($result_upctemp['items'][0]['model']=="" || !isset($result_upctemp['items'][0]['model'])){
					$_POST['model'][$product_id]="None";
				}else{
					$_POST['model'][$product_id]=$result_upctemp['items'][0]['model'];
				}
	//$_POST['sku']=substr((string)$_GET['upc'] ,0,12);
				//$search = array('lb', 'lbs');
				//$_POST['weight']=str_replace($search,"",$result_upctemp['items'][0]['weight']);
				$result_ebay=find_bestprice_ebay($connectionapi,$_POST['name'][$product_id],$upc,1,1,'');
				$pricevariant=(json_encode($result_ebay['pricevariant']))!==null?json_encode($result_ebay['pricevariant']):0;
						   //find_bestprice_ebay($connectionapi,$q,string $gtin,$sort,$limit,$ebay_id)
				//print("<pre>".print_r ($result_ebay,true )."</pre>");
				if($result_ebay['ebay_id_a_cloner']>0 && $_POST['marketplace_item_id'][$product_id]<1){
					$_POST['marketplace_item_id'][$product_id]=$result_ebay['ebay_id_a_cloner'];
					$_POST['price'][$product_id]=$result_ebay['price_with_shipping'];//$result_ebay['price_with_shipping']-.05;
					$_POST['category_id'][$product_id]=$result_ebay['category_id'];
					$_POST['categoryname'][$product_id]=$result_ebay['categoryname'];
					if ($_POST['name'][$product_id]==""){
						$_POST['name'][$product_id]=addslashes($result_ebay['name']);
						$_POST['name'][$product_id]=str_replace('Sealed','',$_POST['name'][$product_id]);
						$_POST['name'][$product_id]=str_replace('sealed','',$_POST['name'][$product_id]);
					}
					if (isset($_POST['algopix_image'][$product_id]) && $_POST['algopix_image'][$product_id]==""){
						$_POST['algopix_image'][$product_id]=addslashes($result_ebay['image']);
					}
					/*else{
						$_POST['weight']=$result_ebay['weight'];
						$_POST['weight2']=$result_ebay['weight2'];
						$_POST['length']=$result_ebay['length'];
						$_POST['width']=$result_ebay['width'];
						$_POST['height']=$result_ebay['height'];
					}*/
					if($result_ebay['model']!="")
						$_POST['model'][$product_id]=$result_ebay['model'];
					if($result_ebay['brand']!="" && $_POST['brand_hidden'][$product_id]=="" 
					&& $_POST['manufacturer_id'][$product_id]<1){
						$_POST['brand'][$product_id]=$result_ebay['brand'];
						$_POST['brand_hidden'][$product_id]=$result_ebay['brand'];
					}
					if($result_ebay['color']!="")
						$_POST['color'][$product_id]=$result_ebay['color'];
					//echo "browse fait";
					//$_POST['browse_ebay'][$product_id]=$result_ebay['html'];
					if(strlen($result_ebay['name']) > strlen($_POST['name'][$product_id])&& isset($_POST['majname'][$product_id])){
						$_POST['name'][$product_id]=$result_ebay['name'];
					}
				}
		}
		//echo $_POST['manufacturersupp'][$product_id];
		if(isset($_POST['manufacturersupp'][$product_id])&& $_POST['manufacturersupp'][$product_id]!=""){
			$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.addslashes(strtoupper($_POST['manufacturersupp'][$product_id])).'")';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			$_POST['manufacturer_id'][$product_id]= mysqli_insert_id($db);
			$_POST['brand'][$product_id]=$_POST['manufacturersupp'][$product_id];
		}elseif(isset($_POST['manufacturer_id'][$product_id]) && $_POST['manufacturer_id'][$product_id]!=""){
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'][$product_id];
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$_POST['brand'][$product_id]=$data2['name'];
		}elseif(isset($_POST['manufacturer_recom'][$product_id]) ){
			$_POST['manufacturer_id'][$product_id]=$_POST['manufacturer_recom'][$product_id];
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer_id'][$product_id];
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$_POST['brand'][$product_id]=$data2['name'];
		}
		if($_POST['marketplace_item_id'][$product_id]!=""){
			$ebay_id=", `ebay_id` = '".$_POST['marketplace_item_id'][$product_id]."' ";
		}else{
			$ebay_id="";
		}
		$sql2 = "UPDATE `oc_algopix` SET `model`='".addslashes(format_string($_POST['model'][$product_id]))."',`color`='".strtoupper($_POST['color'][$product_id])."'";
		$sql2 .=", `name` = '".addslashes(format_string($_POST['name'][$product_id]))."',`manufacturer_id` = '0".$_POST['manufacturer_id'][$product_id]."',
		`image`='".$_POST['image'][$product_id]."',`brand` = '".$_POST['brand'][$product_id]."'";
		$sql2 .=$ebay_id.",`upc`='".$upc."', `weight`='".$_POST['weight'][$product_id]."',`height`='".$_POST['height'][$product_id]."',`price`=price+".$_POST['price'][$product_id]."";
		$sql2 .=",pricevariant='".$pricevariant."', `width`='".$_POST['width'][$product_id]."',`length`='".$_POST['length'][$product_id];
		$sql2 .="',date_update=now() WHERE product_id='".$product_id."' or upc='".$upc."'";
		//echo $sql2."<br>";
		$req2 = mysqli_query($db,$sql2);
		//sleep(5);
}
//echo "oui".$_POST['majname'][26664];
if(isset($_POST['sku']) && ($_POST['sku']!="")){
	echo "allo305";
	$sql4 = 'select upc,product_id
	from oc_product   
	where  sku="'.$_POST['sku'].'"'; 
		$req4 = mysqli_query($db,$sql4);
		$product = mysqli_fetch_assoc($req4);
	$sql2 = "UPDATE `oc_algopix` SET `verif_fait`='5',`product_id`='".$product['product_id']."' where `upc`='".$product['upc']."'";
//echo $sql2;
	$req2 = mysqli_query($db,$sql2);
}
//echo "nb:".$testnb;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
function change_key( $array, $old_key, $new_key ) {
    if( ! array_key_exists( $old_key, $array ) )
        return $array;
    $keys = array_keys( $array );
    $keys[ array_search( $old_key, $keys ) ] = $new_key;
    return array_combine( $keys, $array );
}
function readfilexls($location,$db){ 
	// echo getcwd();
	 //require __DIR__ . '/../Header.php';
	// echo $location;
	 $inputFileName = $location;
	// $helper->log('Loading file ' . pathinfo($inputFileName, PATHINFO_BASENAME) . ' using IOFactory to identify the format');
	 $spreadsheet = IOFactory::load($inputFileName);
	 $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
	// var_dump($sheetData);
	 //unset($sheetData[1]);
	 $i=1;
	 $j=0;
	$headings = $sheetData[1];
	unset($sheetData[1]);
	$nb=count($sheetData)+1;
	foreach($headings as $key=>$value){
		//echo ($heading) ;
		// Notez notre utilisation de ===.  == ne fonctionnerait pas comme attendu
		// car la position de 'a' est la 0-ième (premier) caractère.
			for($i=2;$i<$nb;$i++){
				$sheetData[$i]=change_key($sheetData[$i], $key, $value );
			}
	}
	/*$j=0;
	$i=2;
	foreach($sheetData[2] as $key=>$value){
		echo $key."<br>";
		$pos = strpos($key, 'Enhanced Data');
		// Notez notre utilisation de ===.  == ne fonctionnerait pas comme attendu
		// car la position de 'a' est la 0-ième (premier) caractère.
		if ($pos === false) {
			$j=0;
		}else{
			//echo "<br>allo2".$value ;
			$valuenew=str_replace('Enhanced Data: ','',$key);
				$sheetData[2][$j]=change_key($sheetData[2], $value, $valuenew );
			$j++;
			echo "La chaine a été trouvée dans la chaîne";
			echo " et débute à la position $valuenew <br>";
		}
		$i++;
	}*/
	//print("<pre>".print_r ($sheetData,true )."</pre>");
	//print("<pre>".print_r ($headings,true )."</pre>");
//	//print("<pre>".print_r ($sheetData,true )."</pre>");
	foreach($sheetData as $key=>$value){
		$pos = strpos($key, 'Enhanced Data');
		if(isset($value['Original Search Term'])){
			$sql = 'select upc,product_id from oc_product where upc='.$value['Original Search Term']; 
			//echo $sql."<br>";
	//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
			$req = mysqli_query($db,$sql);
			$product = mysqli_fetch_assoc($req);
		}
		if(isset($product)){
			//echo $product['upc'];
			$sql2 = "
			INSERT INTO `oc_algopix` 
			(`algopix_id`, `product_id`, `model`, `color`, `upc`, `asin`, `image`, `name`, `brand`, `price`, `weight`, `length`, `width`, `height`, `shipping`, `ebay_id`, `description`, `verif_fait`) 
		VALUES 
			(NULL, 
			'".$product['product_id']."', 
			'".(isset($value['Model']) ? $value['Model'] : '')."', 
			'".(isset($value['Color']) ? $value['Color'] : '')."', 
			'".$product['upc']."', 
			'', 
			'".(isset($value['Image URL']) ? $value['Image URL'] : '')."', 
			'".(isset($value['Product Title']) ? addslashes($value['Product Title']) : '')."', 
			'".(isset($value['Brand Name']) ? $value['Brand Name'] : '')."', 
			'".(isset($value['eBay US Price (USD)']) ? $value['eBay US Price (USD)'] : 0)."', 
			'.25', 
			'0', 
			'0', 
			'0', 
			'0', 
			'".(isset($value['eBay US eBay Item ID']) ? $value['eBay US eBay Item ID'] : 0)."', 
			'', 
			'1');";
			//echo $sql2.'<br>';
			$req2 = mysqli_query($db,$sql2);
			$algopix_id=mysqli_insert_id($db);
			if($algopix_id>0){
				foreach($value as $key2=>$value2){
					$pos = strpos($key2, 'Enhanced Data');
					if ($pos !== false && ($value2!="Data could not be retrieved" &&  $value2!="")) {
						//echo "La chaîne '$findme' ne se trouve pas dans la chaîne '$mystring'";
						$keyname=str_replace('Enhanced Data: ','',$key2);
						$sql3 = "INSERT INTO `oc_algopix_enhanced` (`algopix_id`, `name`, `value`) 
						VALUES ('".$algopix_id."', '".addslashes($keyname)."', '".$value2."');";
						//echo $sql3.'<br>';
						$req3 = mysqli_query($db,$sql3);
					}
				}
			}
			$algopix_id=0;
		}else{
			echo "non";
		}
	}
 // echo  date("Y-m-d");
 }
 function array_to_xml( $data, &$xml_data ) {
	 foreach( $data as $key => $value ) {
		 if( is_array($value) ) {
			 if( is_numeric($key) ){
				 $key = 'item'.$key; //dealing with <0/>..<n/> issues
			 }
			 $subnode = $xml_data->addChild($key);
			 /*$this->*/array_to_xml($value, $subnode);
		 } else {
			 $xml_data->addChild("$key",htmlspecialchars("$value"));
		 }
	  }
 }    
 //echo $_FILES['importfile']['name'];
if(isset($_POST['list']) && isset($_POST['product_id'])){
	foreach($_POST['product_id'] as $product){
        $data=explode(',',$product); 
		$product_id=$data[0];
        $sql2 = "UPDATE `oc_algopix` SET verif_fait =9";
		$sql2 .=" WHERE product_id='".$product_id."'";
		$req2 = mysqli_query($db,$sql2);
		//echo $sql2."<br>";
	}
	header("location: bulklisting.php?action=list");  
	exit();  
}
?>
<html> 
<head>
<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title>
			<script type="text/javascript" src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/jquery-2.1.1.min.js"></script>
	<script type="text/javascript" src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/bootstrap/js/bootstrap.min.js"></script>
	<link href="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/font-awesome/css/font-awesome.min.css" type="text/css" rel="stylesheet" />
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/moment.js" type="text/javascript"></script>
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
	<link href="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/jquery/datetimepicker/bootstrap-datetimepicker.min.css" type="text/css" rel="stylesheet" media="screen" />
	<script src="<?echo $GLOBALS['WEBSITE'];?>admin/view/javascript/common.js" type="text/javascript"></script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
 <link href="stylesheet.css" rel="stylesheet">
  <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
</head>
<body bgcolor="<?if($resultebay){?>red<?}else{?>ffffff<?}?>">
<form action="algopix.php" method="post"  enctype="multipart/form-data">
  <table style="text-align: left; width: 1000px; margin-left: auto; margin-right: auto;" border="1" cellpadding="2" cellspacing="2">
    <tbody>
      <tr align="center">
        <th colspan="3" rowspan="1" style="vertical-align:  middle; ">
			<img style="width: 488px; height: 145px;" alt="" src="<?echo $GLOBALS['WEBSITE'];?>/image/catalog/cie/entetelow.jpg">
		</th>
      </tr>
      <tr>
        <th style="vertical-align: middle; background-color: #e4bc03;  text-align: center;width: 200px">
		<a href="listing.php" >Annuler</a> 		
        </th>
        <th colspan="2" style="vertical-align: middle;height: 50; background-color: #030282; color: white;text-align: center;">
		<h1>Bulk Algopix feed</h1>
		</th>
     </tr>
	 	  <tr>
	    <th colspan="3" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
		<br> <input id="saveForm5" class="button_text" type="submit" name="feed" value="Feed" onclick="selectAll()" />
		 	
		<input id="saveForm6" class="button_text" type="submit" name="FindTitle" value="FindTitle" />
       <input id="saveForm7" class="button_text" type="submit" name="upctmp" value="UPCtmp" />
    <input id="saveForm8" class="button_text" type="submit" name="list" value="List" />
       <input id="saveForm9" class="button_text" type="submit" name="remove" value="Remove" />
	   <br>SKU : <input type="text" name="sku" value="" size="80" autofocus />
		</th>
	  </tr>
		<tr>
        <th colspan="3" style="vertical-align: middle;height: 50; background-color: #030282; color: white;text-align: center;">
	<input type="button" onclick='selectAll()' value="Select All"/> 
	<input type="button" onclick='UnSelectAll()' value="Unselect All"/>
	</th>
		</tr>
        <?
$sql4 = 'select al.manufacturer_id,p.upc,p.product_id,p.quantity,al.brand,p.sku,p.weight,p.length,p.width,p.height,
		al.weight algopix_weight,al.length algopix_length,al.width algopix_width,
		al.height algopix_height,p.condition_id,pd.condition_supp,p.remarque_interne,pc.category_id,
		al.color,al.model ,al.image algopix_image,al.name algopix_name,al.price algopix_price,al.ebay_id algopix_to_clone,ale.value brand2
		from oc_product_description pd  
		left join oc_product p on (p.product_id=pd.product_id) 
		left join oc_algopix al on (al.product_id=p.product_id)
		left join oc_algopix_enhanced ale on (al.algopix_id=ale.algopix_id && ale.name="publisher")  
		left join oc_product_to_category pc on (pc.product_id=p.product_id and (pc.category_id=139973 or pc.category_id=617))
		where  pd.language_id=1 and al.verif_fait =5 and p.quantity>0 order by p.product_id '; 
//echo $sql4."<br>";al.image!="" and pd.name="" and 
//echo $ebayoutputnametab[$i]['CustomLabel']."cl<br>";
$req4 = mysqli_query($db,$sql4);
while($product = mysqli_fetch_assoc($req4)){
	//print("<pre>".print_r ($product,true )."</pre>");
	$sql2 = 'SELECT conditions FROM `oc_conditions_to_category` CC LEFT JOIN `oc_conditions` AS C ON CC.conditions_id=C.conditions_id where CC.category_id= '.$product['category_id'];
	//echo "<br>".$sql2;
	$req2 = mysqli_query($db,$sql2);
	$data2 = mysqli_fetch_assoc($req2);
	$conditions = json_decode($data2['conditions'], true);
	//print("<pre>".print_r ($conditions,true )."</pre>");
	$product['condition']=$conditions[$product['condition_id']]['value'];
	//echo $product['condition'];
	if($product['condition_id']==9){
		$variant=1;
		$product['etat']="9,";
	}elseif($product['condition_id']==99){
		$variant=.9;
		$product['etat']="99,NO";
	}elseif($product['condition_id']==8){
		$variant=.9;
		$product['etat']="8,LN";
	}elseif($product['condition_id']==7){
		$variant=.8;
		$product['etat']="7,VG";
	}elseif($product['condition_id']==6){
		$variant=.75;
		$product['etat']="6,G";
	}elseif($product['condition_id']==5){
		$variant=.65;
		$product['etat']="5,P";
	}elseif($product['condition_id']==1){
		$variant=.5;
		$product['etat']="1,FP";
	}elseif($product['condition_id']==2){
		$variant=.85;
		$product['etat']="2,SR";
	}elseif($product['condition_id']==22){
		$variant=.85;
		$product['etat']="22,R";
	}
	$product['price_with_shipping']=($product['algopix_price']*$variant)-.05;
        if ($bgcolor=="ffffff"){
					$bgcolor="c0c0c0";
				}else{
					$bgcolor="ffffff";
				}
?>
<th id="champ1<?echo $product['product_id'];?>" style="vertical-align:  middle; height: 50px; background-color:grey; color: white; width: 200px;" >
<input id="<?echo $product['product_id'];?>" type="checkbox" name="product_id[]" value="<?echo $product['product_id'].','.$product['upc'];?>" onclick="document.getElementById('champ1<?echo $product['product_id'];?>').style.backgroundColor='green';document.getElementsByName('product[<?echo $product['product_id'];?>]').checked = true;"/>
					<?echo $product['product_id'];?>

</th>

        <th style="vertical-align:  middle; height: 50px; background-color:grey; color: white; width: 200px;">
			<label>Title: (<?echo strlen($product['algopix_name'])?>)</label>
		</th>
		<th style="vertical-align:  middle; height: 50px; background-color:grey; color: white; width: 200px;">
<label>Image:</label>
</th>
	
			</tr>
					<tr>
						<?
						$isPoor = ImageisPoorResolution($product['algopix_image']);
       					 $bgColor = $isPoor ? 'red' : 'white';
		?>
					<td colspan="1" rowspan="5" style="vertical-align:  middle; text-align: center; height: 192px; width: 33%; background-color: <?echo $bgColor;?>">';
			<?echo $isPoor ? '<img src="'.$product['algopix_image'].'"/>':'<img height="400" src="'.$product['algopix_image'].'"/>';?>
			</td>		
			<td bgcolor="<?if($product['algopix_name']!=""){echo $bgcolor;}else{echo "red";}?>" id="champ4<?echo $product['product_id'];?>">
					
					<input type="text" name="name[<?echo $product['product_id'];?>]" value="<?echo $product['algopix_name'];?>" maxlength="80" onclick="document.getElementById('champ4<?echo $product['product_id'];?>').style.backgroundColor='green';document.getElementById('product[<?echo $product['product_id'];?>]').checked = true;"/>
					<br>
				 <!-- Options Check Box -->
				 <div>
        <label>
            <input type="checkbox" name="options[<?php echo $product['product_id']; ?>][]" value="Widescreen" /> Widescreen
        </label>
        <label>
            <input type="checkbox" name="options[<?php echo $product['product_id']; ?>][]" value="Fullscreen" /> Fullscreen
        </label>
    </div>

    <!-- Liste déroulante de 1 à 6 disques-set -->
    <div>
        <label for="disk_set_<?php echo $product['product_id']; ?>">Disk Set:</label>
        <select name="options[<?php echo $product['product_id']; ?>][]" id="disk_set_<?php echo $product['product_id']; ?>">
		<option value="">1 disc</option>
            <?php for ($i = 2; $i <= 6; $i++): ?>
                <option value="<?php echo $i; ?>-Disk Set"><?php echo $i; ?>-Disk Set</option>
            <?php endfor; ?>
        </select>
    </div>

    <!-- Check Boxes pour les formats -->
    <div>
        <label>
            <input type="checkbox" name="options[<?php echo $product['product_id']; ?>][]" value="DVD" /> DVD
        </label>
        <label>
            <input type="checkbox" name="options[<?php echo $product['product_id']; ?>][]" value="Blu-ray" /> Blu-ray
        </label>
        <label>
            <input type="checkbox" name="options[<?php echo $product['product_id']; ?>][]" value="4K Blu-ray" /> 4K Blu-ray
        </label>
        <label>
            <input type="checkbox" name="options[<?php echo $product['product_id']; ?>][]" value="Combo Disk" /> Combo Disk
        </label>
        <label>
            <input type="checkbox" name="options[<?php echo $product['product_id']; ?>][]" value="Canadian Cover Edition" /> Canadian Cover Edition
        </label>
    </div>
					<?if(isset($_POST['FindTitle'])){

				

			

// pour Chat GPT
					$keywords[$product['product_id']] = array(
					//	'Product Condition: '.$_POST['condition_name'],
						'product name:'.$product['algopix_name'],
					//	'brand name:'.$_POST['brand'],
						'UPC:'.$product['upc'],
					//	'Ebay Category: '.$_POST['categoryname'],
					//	'Additional product info: '.$_POST['condition_suppen'],
					//	'Additional product description: '.$_POST['description_suppen'],
						
						

						// Ajoutez d'autres mots-clés si nécessaire en fonction des données disponibles
					);
					$ChatGPT_Title[$product['product_id']]=generateOptimizedTitle_CHATGPT_UPC_movie($product['upc'],$product['algopix_name']);
					echo '<input class="element radio" type="radio" name="majname['.$product['product_id'].']" value="'.$product['algopix_name'].'"  onclick="document.getElementById(\'champ1'.$product['product_id'].'\').style.backgroundColor=\'green\';document.getElementById(\''.$product['product_id'].'\').checked = true;"/> 
					<label class="choice" >';
					echo ' ('.strlen($product['algopix_name']).') '.$product['algopix_name'];
					echo '</label><br>';
					
					// Supposons que $ChatGPT_Title et $product soient définis correctement.
					$product_id = $product['product_id'];
				
						// Supprimer les guillemets simples et doubles
						$title = str_replace(['"', "'"], '', $ChatGPT_Title[$product_id]);
						// Supprimer les sauts de ligne et les espaces blancs supplémentaires
						$title = trim(preg_replace('/\s+/', ' ', $title));
						// Supprimer les sauts de ligne
						$title = str_replace(["\r", "\n"], '', $title);

						// Échapper les caractères spéciaux restants
						$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
					
					$title_length = strlen($title);
			//		echo $title;
					// Génère l'élément de formulaire avec des informations de produit.
					echo '
					<input class="element radio" type="radio" name="majname['.$product_id.']" value="'.$title.'" onclick="document.getElementById(\'champ1'.$product_id.'\').style.backgroundColor=\'green\';document.getElementById(\''.$product['product_id'].'\').checked = true;"/> 
					<label class="choice" >';
					echo 'GPT: ('.$title_length.') '.$title;
					echo '</label><br>';
					if(isset($_POST['ebayname'][$product['product_id']])){
						// Supprimer les guillemets simples et doubles
						$title = str_replace(['"', "'"], '', $_POST['ebayname'][$product['product_id']]);
						// Supprimer les sauts de ligne et les espaces blancs supplémentaires
						$title = trim(preg_replace('/\s+/', ' ', $title));
						// Supprimer les sauts de ligne
						$title = str_replace(["\r", "\n"], '', $title);

						// Échapper les caractères spéciaux restants
						$title = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
					
					$title_length = strlen($title);
					
						
						echo '
						<input class="element radio" type="radio" name="majname['.$product_id.']" value="'.$title.'" onclick="document.getElementById(\'champ1'.$product_id.'\').style.backgroundColor=\'green\';document.getElementById(\''.$product['product_id'].'\').checked = true;"/> 
						<label class="choice" >';
						echo 'eBay: ('.$title_length.') '.$title;
						echo '</label><br>';

					}
					
				//	//print("<pre>".print_r ($json,true )."</pre>");
					}?>	
				
				</td>		
				
					<td bgcolor="<?if($product['algopix_image']!=""){echo $bgcolor;}else{echo "red";}?>" id="champ3<?echo $product['product_id'];?>">
					
				
					<input type="text" name="image[<?echo $product['product_id'];?>]" value="<?echo $product['algopix_image'];?>" size="10" onclick="document.getElementById('champ3<?echo $product['product_id'];?>').style.backgroundColor='green';document.getElementById(\'<? echo $product['product_id'];?>').checked = true;"/>
					
					</td>
         
				
<?
$totalsale=$totalsale+(($product['quantity']*(($product['price_with_shipping']*.85)-4))*1.25);?>

			</tr>
			<tr>
		
				
<th style="vertical-align:  middle; height: 0px; background-color:grey; color: white; width: 200px;">
Price Found
</th>
<th style="vertical-align:  middle; height: 0px; background-color:grey; color: white; width: 200px;">
Ebay clone: 
</th>

		</tr>
			<tr>

					<td bgcolor="<?if($product['algopix_price']>0){echo $bgcolor;}else{echo "red";}?>" id="champ6<?echo $product['product_id'];?>">
					<input type="hidden" name="algopix_price_hidden[<?echo $product['product_id'];?>]" value="<?echo $product['algopix_price'];?>" />
					Price Found: <?echo number_format($product['algopix_price'], 2,'.', '');?> +
					<input id="price<?echo $product['product_id'];?>"  type="text" name="price[<?echo $product['product_id'];?>]" value="0" size="10" onclick="document.getElementById('champ6<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
					Price EBAY: 
					<input id="price_with_shipping<?echo $product['product_id'];?>"  type="text" name="price_with_shipping[<?echo $product['product_id'];?>]" value="<?echo number_format($product['price_with_shipping'], 2,'.', '');?>" size="10" onclick="document.getElementById('champ7<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
					</td>
					<td bgcolor="<?if($product['algopix_to_clone']>0){echo $bgcolor;}else{echo "red";}?>" id="champ8<?echo $product['product_id'];?>">
					<a href="https://www.ebay.com/itm/<?echo $product['algopix_to_clone'];?>" target="ebayactive"><?echo $product['algopix_to_clone'];?></a> 	
					<input type="hidden" name="ebay_id_hidden[<?echo $product['product_id'];?>]" value="<?echo $product['algopix_to_clone'];?>" />
					<input type="hidden" name="condition_id[<?echo $product['product_id'];?>]" value="<?echo $product['condition_id'];?>" />
					<br><input type="text" name="ebay_id[<?echo $product['product_id'];?>]" value="" size="10" onclick="document.getElementById('champ8<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
				</td>
					
                   
				
					</tr>
					<tr>

<th style="vertical-align:  middle; background-color:grey; color: white; width: 200px;">
	
<label>Brand: <?echo $product['brand'];?></label>         
</th>
<th style="vertical-align:  middle;  background-color:grey; color: white; width: 200px;">	
<label>Info:</label>		 </th>
				</tr><tr>
		
				<?if($product['model']=="")
						$product['model']="none";
					?>

					<?if($product['brand']=="")
						$product['brand']=$product['brand2'];
					?>
                    <td bgcolor="<?if($product['manufacturer_id']>0){echo $bgcolor;}else{echo "red";}?>" id="champ11<?echo $product['product_id'];?>">
					<input type="hidden" name="model[<?echo $product['product_id'];?>]" value="<?echo $product['model'];?>" size="10" onclick="document.getElementById('champ10<?echo $product['product_id'];?>').style.backgroundColor='green';"/>
  
				   <?$brandtmp=explode(" ",$product['brand']);
						$brandtmp=$brandtmp[0];
						?>
					<input type="hidden" name="brand[<?echo $product['product_id'];?>]" value="<?echo $product['brand'];?>" />
					<input type="hidden" name="brand_hidden[<?echo $product['product_id'];?>]" value="<?echo $product['brand'];?>" />
					<select name="manufacturer_id[<?echo $product['product_id'];?>]" onclick="document.getElementById('champ11<?echo $product['product_id'];?>').style.backgroundColor='green';">
			<option value="" selected></option>
<?
			$sql = 'SELECT * FROM `oc_manufacturer` order by name';
			// on envoie la requête
			$req = mysqli_query($db,$sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			$brandrecom="";
			while($data = mysqli_fetch_assoc($req))
				{
					$selected="";
					if (isset($product['manufacturer_id']) && $product['manufacturer_id']!=0){
						$test2=strtolower ($data['manufacturer_id']);
						$test1=strtolower ($product['manufacturer_id']);
						$test3=strtolower( $data['brand']);
						$test4=strtolower( $product['brand']);
						if ($test1==$test2 || $test3=$test4 && ($test1==0 || $test1== null || $test1=="") ) {
							$selected="selected";
						}
						//echo "allo";
					}else{
						$test2=strtolower ($data['name']);
						$test1=strtolower ($brandtmp);
						//echo "allo2";
						if (strpos($test2, $test1) !== false && ($test2!="" && $test1!="") ) {
							//$selected="selected";
							//echo 'allo3';
							//$brandrecom[$i]
							$brandrecom=$brandrecom.",".$data['name']."@".$data['manufacturer_id'];
						}
					}
					?>
								<option value="<?echo $data['manufacturer_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
					<?}?>
							</select><br>
							<input type="hidden" name="manufacturer_id_old[<?echo $product['product_id'];?>]" value="<?echo $product['manufacturer_id'];?>" />
					<?	
					//echo $brandrecom;
					$brandrecomtab=explode(',', $brandrecom);
					foreach($brandrecomtab as $brandrecomtab2){
						if($brandrecomtab2!=null ){
							//echo $brandrecomtab2;
							$brandrecomtab3=explode('@', $brandrecomtab2);
							echo '<input id="manufacturer_recom'.$product['product_id'].'" class="element radio" type="radio" name="manufacturer_recom['.$product['product_id'].']" value="'.$brandrecomtab3[1].'"/> 
									<label class="choice">'.$brandrecomtab3[0].'</label><br>';
						}
					}	 
?>		
		<label>Add if not in the list:</label> <br><input id="manufacturersupp<?echo $product['product_id'];?>"  type="text" name="manufacturersupp[<?echo $product['product_id'];?>]" value="" maxlength="80" />
		</div>
					</td>
                  
					<input type="hidden" name="color[<?echo $product['product_id'];?>]" value="<?echo $product['color'];?>" size="10" onclick="document.getElementById('champ12<?echo $product['product_id'];?>').style.backgroundColor='green';" />
					<input id="length<?echo $product['product_id'];?>"  type="hidden" name="length[<?echo $product['product_id'];?>]" value="<?echo (int)($product['length']);?>" size="3" />
				<input id="width<?echo $product['product_id'];?>"  type="hidden" name="width[<?echo $product['product_id'];?>]" value="<?echo (int)($product['width']);?>" size="3" />
				<input id="height<?echo $product['product_id'];?>"  type="hidden" name="height[<?echo $product['product_id'];?>]" value="<?echo (int)($product['height']);?>" size="3" />
				
					<input id="weight<?echo $product['product_id'];?>"  type="hidden" name="weight[<?echo $product['product_id'];?>]" value="<?echo number_format($product['weight'], 2,'.', '');?>" size="5" />
		
					<?
					if($product['algopix_length']>0)
						$product['length']=$product['algopix_length'];
					if($product['algopix_width']>0)
						$product['width']=$product['algopix_width'];
					if($product['algopix_height']>0)
						$product['height']=$product['algopix_height'];
					if($product['algopix_weight']>0)
						$product['weight']=$product['algopix_weight'];
					?>
				
		
				
                    <td bgcolor="<?echo $bgcolor;?>">
					UPC: <a href="https://www.google.com/search?q=<?echo $product['upc'];?>&sxsrf=ALiCzsb0RjYCtzJ9TN3fsTutZ_vxHdToZQ:1660065327909&source=lnms&tbm=isch&sa=X&ved=2ahUKEwi93LGhobr5AhV8GVkFHYM8C3kQ_AUoAnoECAEQBA&biw=1024&bih=685&dpr=1.25" target="google"><?echo $product['upc'];?></a> 	
					<br>SKU:  <?echo $product['sku'];?><br><br><strong>Extra Info:</strong><br>
       				 <?if($product['condition_id']!=9 && $product['condition_supp']=="")
					 		$product['condition_supp']='Comes from a former rental store, could have a RFID sticker in the middle of the disk, no Digital Code included';	?>
					<input id="condition_supp<?echo $product['product_id'];?>"  type="text" name="condition_supp[<?echo $product['product_id'];?>]" value="<?echo $product['condition_supp'];?>" size="10" />
					</td>
					
			</tr>
				
		<?
	//$j++;
	//echo $j;
	$i++;
	$itemcount++;
            }
		?>
		<tr>
	    <th colspan="4" style="vertical-align:  middle; height: 50px; background-color: #e4bc03; width: 200px;text-align:right"> 	
        <input type="file" name="importfile" >
		<br> <input id="saveForm" class="button_text" type="submit" name="feed" value="Feed" onclick="selectAll()" />
		<br> 	
       <input id="saveForm2" class="button_text" type="submit" name="upctmp" value="UPCtmp" />
<br>    <input id="saveForm3" class="button_text" type="submit" name="list" value="List" />
       <input id="saveForm4" class="button_text" type="submit" name="remove" value="Remove" />
		</th>
		</tr>
</table>
<?echo "Total Sale:".number_format($totalsale, 2,'.', '');?>
<input type="hidden" name="start" value="1" />
</form>
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
        var items = document.getElementsByName('product_id[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = true;
        }
    }
    function UnSelectAll() {
        var items = document.getElementsByName('product_id[]');
        for (var i = 0; i < items.length; i++) {
            if (items[i].type == 'checkbox')
                items[i].checked = false;
        }
    }			
</script>
</body>
</html>
<?  
// on ferme la connexion à mysql 
mysqli_close($db);
ob_end_flush(); ?>