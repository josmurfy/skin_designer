<?
//print_r($data3['accesoires']);
$sku=$data3['sku'];
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 

/* 		$sql3 = "select * from `oc_product_toner` where manufacturer_id=0";
		$req3 = mysqli_query($db,$sql3);
while($data3 = mysqli_fetch_assoc($req3))
    {
		$sql = "select * from `oc_manufacturer` where name like '%".strtoupper(strtolower($data3['marque']))."%' limit 1";
		$req = mysqli_query($db,$sql);
		$data = mysqli_fetch_assoc($req);
		$sql2 = 'UPDATE `oc_product_toner` SET `manufacturer_id`="'.$data['manufacturer_id'].'" where product_toner_id="'.$data3['product_toner_id'].'"';
		
		echo $sql2.'<br><br>';
		
		$req2 = mysqli_query($db,$sql2);
	} */
		$sql3 = "select * from `oc_product_toner` where product_id=0";
		$req3 = mysqli_query($db,$sql3);
while($data3 = mysqli_fetch_assoc($req3))
    {
		$sql2 = 'INSERT INTO `oc_product` (`image`,`price`,`model`, `sku`,`color`, `compatible`, `mpn`,`quantity`,`manufacturer_name`, `manufacturer_id`,';
		$sql2 .='`priceebaysold`, `priceterasold`, `priceebaynow`,`tax_class_id`, `status`, `condition_id`,`invoice`)';
		$sql2 .=' VALUES ("catalog/product/'.$data3['colorcode'].'toner.jpg","'.$data3['price'].'","'.strtoupper($data3['model']).'", "'.strtoupper($data3['model']).'", "'.strtoupper($data3['color']).'", "'.strtoupper($data3['compatible']).'", "'.strtoupper($data3['model']).'","0'.$data3['quantity'].'","'.strtoupper($data3['marque']).'","'.strtoupper($data3['manufacturer_id']).'",';
		$sql2 .='"", "", "", "9", "0", "9","");';
		echo $sql2.'<br><br>';
		
		$req2 = mysqli_query($db,$sql2);
		$product_id= mysqli_insert_id($db);
		//echo $data3['categoryarbonum'];
		// entree les category_id
		
		$categorynametab=explode('>','58058>171961>11195>16204' );
		foreach($categorynametab as $categoryname) 
		{
			//echo $categoryname;
			if ($categoryname !=""){
			$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$product_id."', '".$categoryname."')";
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			}
			
		}
		$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','catalog/product/toner2.jpg'),('".$product_id."','catalog/product/toner3.jpg'),('".$product_id."','catalog/product/toner4.jpg')";
		$req2=mysqli_query($db,$sql2);
		$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183628')";
		$req = mysqli_query($db,$sql); 
		$description='<h2>Description :</h2><strong>Title : </strong>'.strtoupper($data3['name'])." ".strtoupper($data3['color'])." for ".strtoupper($data3['marque'])." ".strtoupper($data3['model']).'<br><strong>Model : </strong>'.strtoupper($data3['model']);
			$description.='<br><strong>Brand : </strong> Compatible Cartridge For '.strtoupper($data3['marque']).'<br>';
			$description.='<strong>Compatible with : </strong>'.strtoupper($data3['compatible']).'<br>';
			$description.='<strong>Color : </strong>'.$data3['color'].'<br>';
			$description.='<h2>Conditions :</h2><p><strong>- NEW, <br>- Sealed Unused<br>- ISO9001, ISO4001 Quality<br>- STMC Compliant Company<br>- Made In China</strong><br>';
			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/catalog/product/'.$data3['colorcode'].'toner.jpg\" width=\"450\"</td></tr>';
							
		
			$sql5 = "SELECT * FROM oc_product_image where product_id=".$product_id;
			$req5= mysqli_query($db,$sql5); 
			while($data5 = mysqli_fetch_assoc($req5))
			{
					$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data5['image'].'\" width=\"450\"</td></tr>';

			}
		
		$sql = "INSERT INTO `oc_product_description` (`description`,`product_id`, `name`, `meta_title` ,`language_id`) VALUES ('".$description."','".$product_id."', '".strtoupper($data3['name'])." ".strtoupper($data3['color'])." for ".strtoupper($data3['marque'])." ".strtoupper($data3['model'])." ".strtoupper($data3['compatible'])."', '".strtoupper($data3['name'])." ".strtoupper($data3['color'])." for ".strtoupper($data3['marque'])." ".strtoupper($data3['model'])." ".strtoupper($data3['compatible'])."', '1')";
		$req = mysqli_query($db,$sql);	
		//echo $sql."<br>";
		$sql = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
		$req = mysqli_query($db,$sql);	
		$sql2 = 'UPDATE `oc_product_toner` SET `product_id`="'.$product_id.'" where product_toner_id="'.$data3['product_toner_id'].'"';	
		$req2 = mysqli_query($db,$sql2);
	}
mysqli_close($db); ?>