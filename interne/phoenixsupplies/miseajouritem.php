<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db); 

/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5,price=price_with_shipping where quantity=0 and price=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */

		$sql2 = 'UPDATE `oc_product` SET `status` = 0,stock_status_id=5,ebay_last_check="2020-09-01" where quantity<1 or location=""';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 0,stock_status_id=7 where price=0 and price_with_shipping>0 and quantity>0';
		$req2 = mysqli_query($db,$sql2);
		$sql2 = 'UPDATE `oc_product` SET `status` = 0,stock_status_id=5 where price_with_shipping=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */


/* 		//flush category
		$sql2 = 'UPDATE `oc_product_to_category` SET `status` = 0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$sql = 'SELECT product_id FROM `oc_product` where status=1 order by product_id ';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){ 
			$sql2 = 'UPDATE `oc_product_to_category` SET `status` = 1 where product_id='.$data['product_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
			}	
		
		
		$sql2 = 'UPDATE `oc_category` SET `status` = 0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
 			$sql = 'SELECT category_id FROM `oc_product_to_category` where status=1 group by category_id';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$calcitem=0;
		while($data = mysqli_fetch_assoc($req)){ 
			$sql2 = 'UPDATE `oc_category` SET `status` = 1 where category_id='.$data['category_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
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
		}  */



			if($_GET['product_id']!="")$_GET['product_id']= ' and `oc_product`.product_id="'.$_GET['product_id'].'"';
 			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and status=1 '.$_GET['product_id'];
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$calcitem=0;
	while($data = mysqli_fetch_assoc($req)){ 
			$name=$data['name'];
			$calcitem=$calcitem+1;;
			//echo $_POST['categoryarbonum'];
			// entree les category_id s

			// a verifer $categorynametab=explode('>', $_POST['categoryarbonum']);
			
			//description
			$sql5 = 'SELECT * FROM `oc_product_index` where upc='.substr($data['upc'],0,12);

			// on envoie la requête
			//echo $sql5;
			$req5 = mysqli_query($db,$sql5);
			$data5 = mysqli_fetch_assoc($req5);
			$sql2 = 'UPDATE `oc_product` SET upc="'.substr($data['upc'],0,12).'",ebay_last_check="2020-09-01" WHERE `product_id` ='.$data['product_id'];
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			$keywordindex=$data5['googlesearchphrase'];
			$sql9 = 'SELECT * FROM `oc_product_special` where product_id='.$data['product_id'];

			// on envoie la requête
			//echo $sql5;
			$req9 = mysqli_query($db,$sql9);
			$data9 = mysqli_fetch_assoc($req9);
			$prixcad=$data9['price']*1.34;

			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data['manufacturer_id'];

			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$sql6 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where language_id=1 and oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$data['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req6 = mysqli_query($db,$sql6);
			$data6 = mysqli_fetch_assoc($req6);
			$categoryname=$data6['name'];
			$sql6 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where language_id=2 and oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$data['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req6 = mysqli_query($db,$sql6);
			$data6 = mysqli_fetch_assoc($req6);
			$categorynamef=$data6['name'];
			$brand=$data2['name'];
			//Anglais
			$description='<h2>Description :</h2>';
			$descriptionf='<h2>Description :</h2>';
			$modele="Mod&egravele";
			// averifer
			//if($data['andescription']!='')$description.=$data['andescription'].'<br><br>';
			$description.='<strong>Title : </strong>'.strtoupper($name).'<br><strong>Model : </strong>'.strtoupper($data['model']);
			$descriptionf.='<strong>Titre : </strong>'.strtoupper($name).'<br><strong>'.strtoupper($modele).' : </strong>'.strtoupper($data['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data2['name']).'<br>';
			$descriptionf.='<br><strong>Marque : </strong>'.strtoupper($data2['name']).'<br>';
			if($data['color']=="")$data['color']="N/A";
			$description.='<strong>Color : </strong>'.strtoupper($data['color']).'<br>';
			$descriptionf.='<strong>Couleur : </strong>'.strtoupper($data['color']).'<br>';
			$description.='<strong>Package Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$descriptionf.='<strong>Dimension Boite : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$descriptionf.='<strong>Poids : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$sql2 = 'SELECT * FROM `oc_condition` where language_id=1 and condition_id='.$data['condition_id'];
//echo $sql2;
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$description.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data2['name']).'</strong><br>';
			$description.=addslashes(strtoupper($data2['condition'])."<br>");
			$conditionname=strtoupper($data2['name']);

$conditionsupp=explode(',', $data8['condition']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='<font color="red"><strong>- '.addslashes($conditioncheck).'</strong></font><br>';
		//echo $i;		
	}
}			
			
			if($data8['accessory']!="" && $data8['accessory']!=",")$description.='</p><h2>Accessories Included :</h2><p>';
$conditionsupp=explode(',', $data8['accessory']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.addslashes($conditioncheck).'<br>';
		//echo $i;		
	}
}		
//echo $_POST['test'];		
		if($data8['test']!="" && $data8['test']!=",")$description.='</p><h2>Tests - Repairs Done :</h2><p>';
$conditionsupp=explode(',', $data8['test']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.addslashes($conditioncheck).'<br>';
		//echo $i;		
	}
}	
//francais
			$sql3 = 'SELECT * FROM `oc_condition` where language_id=2 and condition_id='.$data['condition_id']; 
//echo $sql3;
			// on envoie la requête
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			$descriptionf.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data3['name']).'</strong><br>';
			$descriptionf.=addslashes(strtoupper($data3['condition'])."<br>");
			$conditionnamef=strtoupper($data3['name']);

			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			$descriptionf.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
			$descriptionf.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
							
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$data['product_id'];
			$req2= mysqli_query($db,$sql2); 
			//echo $sql2."<br>";
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
					$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data2['image'].'\" width=\"450\"</td></tr>';
					$descriptionf.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data2['image'].'\" width=\"450\"</td></tr>';
				$i++;
				}
			}
			$description.='</tbody></table><br>';
			$descriptionf.='</tbody></table><br>';
			//echo $description;
			// a verifier   $_POST['name_product']=htmlentities($_POST['name_product'], ENT_QUOTES);
			//$description=addslashes($description);
			//$descriptionf=addslashes($descriptionf);
			
			//
		// modification toutjours de description
/* 			$sql2 = "UPDATE `oc_product` SET image ='catalog/product/".str_replace('catalog/product/', '',$data['image'])."' where product_id=".$data['product_id']."";
			echo $sql2;
			$req2 = mysqli_query($db,$sql2); */
			$sql2 = 'UPDATE `oc_product_description` SET description_mod=1,tag="'.$conditionname.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'",meta_keyword="'.$conditionname.','.strtoupper($keywordindex).','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'", meta_title="'.$conditionname." ".addslashes(strtoupper($name)).'",name="'.addslashes(strtoupper($name)).'", description="'.$description.'", meta_description="Liquidation '.addslashes(strtoupper($categoryname.' '.strtoupper($keywordindex).' '.$name)).' '.$conditionname.' at the lowest price of $'.doubleval ($prixcad).' CAD Model: '.$data['model'].' UPC:'.$data['upc'].'" WHERE language_id=1 and `product_id` ='.$data['product_id'];
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
			
			$sql8 = "SELECT product_id FROM oc_product_description where language_id=2 and product_id=".$data['product_id'];
			$req8= mysqli_query($db,$sql8); 
			//echo $sql2."<br>";
			$data8 = mysqli_fetch_assoc($req8);
			$product_id_check= $data8['product_id'];
			if($product_id_check==""){
				$sql7 = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`,`description`) VALUES ('".$data['product_id']."', '', '', '2','')";
				$req7 = mysqli_query($db,$sql7);
				//echo $sql7."<br>";				
			}
			$sql2 = 'UPDATE `oc_product_description` SET description_mod=1,tag="'.$conditionnamef.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'",meta_keyword="'.$conditionnamef.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'", meta_title="'.$conditionnamef." ".addslashes(strtoupper($name)).'",name="'.addslashes(strtoupper($name)).'", description="'.$descriptionf.'", meta_description="Liquidation '.addslashes(strtoupper($categorynamef.' '.$name)).' '.$conditionnamef.' au meilleur prix de $'.doubleval ($prixcad).' CAD Modele: '.$data['model'].' UPC:'.$data['upc'].'"  WHERE language_id=2 and `product_id` ='.$data['product_id'];
			
			$req2 = mysqli_query($db,$sql2,$resultmode);
			//echo $resultmode;



	}
			//echo $calcitem;  
			if($_GET['product_id']!="")header("location: updateprice.php");
			
mysqli_close($db); 
?>