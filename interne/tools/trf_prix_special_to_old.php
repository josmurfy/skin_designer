<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';
// on s&eacute;lectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db); 

//temp trf prix special avant covid
/* 		$sql = 'SELECT * FROM `oc_product_special`';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
		$calcitem=0;
		while($data = mysqli_fetch_assoc($req)){ 	
			$sql2 = "SELECT * FROM `oc_product` where `product_id`='".$data['product_id']."'";
		echo $sql2."<br>";
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$sql3 = "UPDATE `oc_product_special` SET `priceretail` = '".number_format($data2['price'], 2, '.', '')."' where product_id=".$data['product_id'];
			echo $sql3."<br>"."<br>";
			$req3 = mysqli_query($db,$sql3) ;
			$calcitem++;	
		}	
		echo $calcitem." on ete traite dans priceold<br><br>";  */
		
//ajoute un prix retail quand le prix et plus bas que prix magasin
/*  		$sql = 'SELECT * FROM `oc_product_special` WHERE `priceebay` < 0 ORDER BY `priceretail` ASC';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
		$calcitem=0;
		while($data = mysqli_fetch_assoc($req)){ 	
			$sql3 = "UPDATE `oc_product` SET `price` = '".number_format($data['priceold']/.65, 2, '.', '')."' where product_id=".$data['product_id'];
			echo $sql3."<br>"."<br>";
			$req3 = mysqli_query($db,$sql3) ;
			$sql3 = "UPDATE `oc_product_special` SET `priceretail` = '".number_format($data['priceold']/.65, 2, '.', '')."' where product_id=".$data['product_id'];
			echo $sql3."<br>"."<br>";
			$req3 = mysqli_query($db,$sql3) ;
			$calcitem++;	
		}	
		echo $calcitem." on ete traite dans priceold<br><br>";    */
		
//supprime les prix speciaux quand quantity =0 et priceretail=0 .. mais met un 5 dans priority 
/* $sql = 'SELECT * FROM `oc_product` WHERE `price`=0 and quantity<1';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
		$calcitem=0;
		while($data = mysqli_fetch_assoc($req)){ 	
			$sql2 = "SELECT * FROM `oc_product_special` where `product_id`='".$data['product_id']."'";
		echo $sql2."<br>";
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			if($data2['product_id']==""){
				$priority=9;
			}else{
				$priority=5;
			}
			$sql3 = "UPDATE `oc_product_special` SET `priority` = '".$priority."' where product_id=".$data2['product_id'];
			echo $sql3."<br>"."<br>";
			//$req3 = mysqli_query($db,$sql3) ;
			$calcitem++;	
		}	
		echo $calcitem." on ete traite dans priceold<br><br>"; */

//		
		
		$sql = 'SELECT * FROM `oc_product`';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
		$calcitem=0;
		while($data = mysqli_fetch_assoc($req)){ 
			if($data['USPS']>0 && $data['USPS']< $data['USPS_com'] && ($data['USPS']< $data['UPS'] || $data['USPS']< $data['UPS_com'])){
				$frais_shipping=$data['USPS'];
			}elseif($data['USPS_com']>0 && ($data['USPS_com']< $data['UPS_com'] && $data['UPS_com']>0)){
				$frais_shipping=$data['USPS_com'];
			}elseif($data['UPS_com']>0 && $data['UPS_com']< $data['USPS_com']){
				$frais_shipping=$data['UPS_com'];
			}elseif($data['USPS_com']>0 && $data['USPS_com']< $data['USPS']){
				$frais_shipping=$data['USPS_com'];
			}elseif($data['UPS']>0 && $data['UPS']< $data['UPS_com']){
				$frais_shipping=$data['UPS'];
			}elseif($data['UPS_com']>0 && $data['UPS_com']< $data['UPS']){
				$frais_shipping=$data['UPS_com'];  
			}else{
				$frais_shipping=9999;
			}
			if($data['price_with_shipping']>0){
				$priceaprendre=$data['price_with_shipping'];
			}else{
				$priceaprendre=$data['price'];
			}
			
			//echo $priceebay."<br> ";
			$priceebay=($priceaprendre*.90)-$frais_shipping;
			echo $priceebay."<br> ";
			$priceebay= number_format($priceebay, 2, '.', '');
			echo $priceebay."<br> ";
			$price_replace=explode('.',$priceebay);
			$priceebay=$price_replace[0]+0.95;
			$profit=number_format($priceebay*.85-($data['price']*.20), 2, '.', '');
			
			$sql3 = "UPDATE `oc_product_special` SET `profit` = '".$profit."',`quantityleft` = '".$data['quantity']."',`shippingcost` = '".$frais_shipping."',`priceebay` = '".$priceebay."' where `product_id`=".$data['product_id'];
			echo $sql3."<br> ".$frais_shipping."<br>";
			echo "USPS:".$data['USPS']." USPS_com:".$data['USPS_com']." UPS:".$data['UPS']." UPS_com:".$data['UPS_com']."<br><br>";
			$req3 = mysqli_query($db,$sql3) ;
			$calcitem++;	
		}	
		echo $calcitem." item ajuster au prix ebay<br><br>";
		
		$sql3 = "UPDATE `oc_product_special` SET `price` = priceebay WHERE `priceebay` > 0 AND `quantityleft` > 0 ORDER BY `priceebay` ASC";
		$req3 = mysqli_query($db,$sql3) ;
?>