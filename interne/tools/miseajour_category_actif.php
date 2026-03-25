<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL  
include '../connection.php';include '../functionload.php';
$sql = 'SELECT p.product_id FROM `oc_product` p where p.`status` = 1 AND (p.quantity<=0 or (P.unallocated_quantity=0 and P.quantity=0 and p.quantity>0)) order by product_id ';
$req = mysqli_query($db,$sql);
//echo $sql.'<br><br>';

		$i=0;
			while($data = mysqli_fetch_assoc($req)){ 
		$sql2 = 'UPDATE `oc_product` SET `status` = 0,stock_status_id=5 where product_id='.$data['product_id'];

		$req2 = mysqli_query($db,$sql2);
}
		//flush category et manufacturier
		echo "Nb de produits OUT of STOCK désactivé: ".mysqli_num_rows($req)."<br>";  
		$sql2 = 'UPDATE `oc_product_to_category` SET `status` = 0 where status=1'; 
		//echo $sql2.'<br><br>';
		
		$req2 = mysqli_query($db,$sql2);
		echo "Nb de category active désactivé: ".mysqli_num_rows($req)."<br>";
/* 		$sql2 = 'UPDATE `oc_manufacturer_to_store` SET `store_id` = 9 where `store_id`=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */
/* 		$sql2 = 'delete from `oc_banner_image` where `banner_id` = 8';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */
$sql = 'SELECT p.product_id FROM `oc_product` p  where (P.unallocated_quantity>0 or P.quantity>0) order by product_id ';
$req = mysqli_query($db,$sql);
echo $sql.'<br><br>';
		echo "Nb de produits actifs: ".mysqli_num_rows($req)."<br>";
		$i=0;
			while($data = mysqli_fetch_assoc($req)){ 
			$sql2 = 'UPDATE `oc_product_to_category` SET `status` = 1 where product_id='.$data['product_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
			$i++;
 			$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=7 where product_id='.$data['product_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);	 
			}	
		
		//mise a jour des manufacturier
/* 		$sql = 'SELECT * FROM `oc_manufacturer_to_store`,oc_manufacturer where oc_manufacturer_to_store.manufacturer_id=oc_manufacturer.manufacturer_id and store_id=0 and image is not null order by name ';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){
				$sql2 = "INSERT INTO `oc_banner_image` (`banner_id`, `language_id`, `title`, `link`, `image`, `sort_order`) VALUES ('8','1', 'Quality products at the low price ".$data['name']."', 'https://phoenixliquidation.ca/".str_replace(" ","-",strtolower($data['name']))."', '".$data['image']."','0')";
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				$sql2 = "INSERT INTO `oc_banner_image` (`banner_id`, `language_id`, `title`, `link`, `image`, `sort_order`) VALUES ('8','2', 'Produits de qualités à petit prix ".$data['name']."', 'https://phoenixliquidation.ca/".str_replace(" ","-",strtolower($data['name']))."', '".$data['image']."','0')";
				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
			} */
		
		$sql2 = 'UPDATE `oc_category` SET `status` = 0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
 			$sql = 'SELECT category_id FROM `oc_product_to_category` where status=1 group by category_id ';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$calcitem=0;
			echo "Nb de category Activé: ".mysqli_num_rows($req)."<br>";
		while($data = mysqli_fetch_assoc($req)){ 
		
/*  			$sql3 = 'SELECT ebayyes FROM `oc_category_description` where language_id=1 and category_id='.$data['category_id'];
			//echo $sql."<br>";
			$req3 = mysqli_query($db,$sql3);	
			$data3 = mysqli_fetch_assoc($req3);	 */		
			$sql2 = 'UPDATE `oc_category` SET `status` = 1 where category_id='.$data['category_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
			/* $sql3 = 'SELECT * FROM `oc_category_description` WHERE `category_id` = "'.$data['category_id'].'" and language_id=1';
			
			$req3 = mysqli_query($db,$sql3);
			//echo $sql3.'<br><br>';
			$data3 = mysqli_fetch_assoc($req3);
			$sql2 ="UPDATE `oc_category_description` SET `description` = 'Find great deal on those ".$data3['name']." products on sale!', `meta_title` = 'Find great deal on those ".$data3['name']." products on sale!', `meta_description` = 'Find great deal on those ".$data3['name']." products on sale!' 
			WHERE '".$data['category_id']."' AND `oc_category_description`.`language_id` = 1";
			$req2 = mysqli_query($db,$sql2);
			//echo $sql2."<br>";
			$sql3 = 'SELECT * FROM `oc_category_description` WHERE `category_id` ="'.$data['category_id'].'" and language_id=2';
			//echo $sql."<br>";
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			$sql2 ="UPDATE `oc_category_description` SET `description` = 'Trouvez de bonnes affaires sur ces produits ".$data3['name']." en solde!', `meta_title` = 'Trouvez de bonnes affaires sur ces produits ".$data3['name']." en solde!', `meta_description` = 'Trouvez de bonnes affaires sur ces produits ".$data3['name']." en solde!' 
			WHERE '".$data['category_id']."' AND `oc_category_description`.`language_id` = 2";
			$req2 = mysqli_query($db,$sql2); */
			//$sql2."<br>";
			
		}
		//flush related
		$sql2 = 'DELETE FROM `oc_product_related`';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		
		$sql = 'SELECT product_id,manufacturer_id FROM `oc_product` where status=1 order by product_id ';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
			
		while($data = mysqli_fetch_assoc($req)){ 
			$sql2 = 'SELECT product_id FROM oc_product where quantity>0 and manufacturer_id='.$data['manufacturer_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
			while($data2 = mysqli_fetch_assoc($req2)){
				if($data['product_id']<>$data2['product_id']){
					$sql3 = "INSERT INTO `oc_product_related` (`product_id`, `related_id`) VALUES ('".$data['product_id']."', '".$data2['product_id']."')";
					//echo $sql2.'<br><br>';
					$req3 = mysqli_query($db,$sql3);
				}
			}
		} 


mysqli_close($db); 

?>