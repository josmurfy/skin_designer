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
		
		$sql = 'SELECT * FROM `oc_ebay_listing_sync`';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
		$calcitem=0;
		while($data = mysqli_fetch_assoc($req)){ 
			
			$sql3 = "INSERT `oc_wk_ebay_auction_product` (ebay_product_id,product_id,auction_status,buy_it_now_price,price_rule_status) VALUES ('".$data['ebay_item_id']."','".$data['product_id']."',1,'".$data['pricesite']."','disabled')";

			//$req3 = mysqli_query($db,$sql3) ;
			echo $sql3."<br>";
			$calcitem++;	
		}	

?>