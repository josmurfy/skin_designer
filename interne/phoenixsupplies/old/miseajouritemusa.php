<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s&eacute;lectionne la base 
mysql_select_db('phoenkv5_store',$db); 

/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5,price=price_with_shipping where quantity=0 and price=0';
		//echo $sql2.'<br><br>';
		$req2 = mysql_query($sql2); */

		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5 where quantity=0 and price_with_shipping>0';
		//echo $sql2.'<br><br>';
		$req2 = mysql_query($sql2);
		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=7 where price=0 and price_with_shipping>0 and quantity>0';
		$req2 = mysql_query($sql2);
		$sql2 = 'UPDATE `oc_product` SET `status` = 0,stock_status_id=5 where price_with_shipping=0';
		//echo $sql2.'<br><br>';
		$req2 = mysql_query($sql2);


		//flush category
		$sql2 = 'UPDATE `oc_category` SET `status` = 0';
		//echo $sql2.'<br><br>';
		$req2 = mysql_query($sql2);
 			$sql = 'SELECT * FROM `oc_product_to_category`';
			//echo $sql."<br>";
			$req = mysql_query($sql);
			$calcitem=0;
		while($data = mysql_fetch_assoc($req)){ 
			$sql2 = 'UPDATE `oc_category` SET `status` = 1 where category_id='.$data['category_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysql_query($sql2);
		}
		//flush related
		$sql2 = 'DELETE FROM `oc_product_related`';
		//echo $sql2.'<br><br>';
		$req2 = mysql_query($sql2);
		
		$sql = 'SELECT product_id,manufacturer_id FROM `oc_product` where status=1 order by product_id ';
		//echo $sql."<br>";
		$req = mysql_query($sql);
			
		while($data = mysql_fetch_assoc($req)){ 
			$sql2 = 'SELECT product_id FROM oc_product where quantity>0 and manufacturer_id='.$data['manufacturer_id'];
			//echo $sql2.'<br><br>';
			$req2 = mysql_query($sql2);
			while($data2 = mysql_fetch_assoc($req2)){
				if($data['product_id']<>$data2['product_id']){
					$sql3 = "INSERT INTO `oc_product_related` (`product_id`, `related_id`) VALUES ('".$data['product_id']."', '".$data2['product_id']."')";
					//echo $sql2.'<br><br>';
					$req3 = mysql_query($sql3);
				}
			}
		} 



		
 			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and usa=1 and description_mod<3';
			//echo $sql."<br>";
			$req = mysql_query($sql);
			$calcitem=0;
	while($data = mysql_fetch_assoc($req)){ 
			$name=$data['name'];
			$calcitem=$calcitem+1;;
			//echo $_POST['categoryarbonum'];
			// entree les category_id s

			// a verifer $categorynametab=explode('>', $_POST['categoryarbonum']);
			
			//description
			$sql5 = 'SELECT * FROM `oc_product_index` where upc='.substr($data['upc'],0,12);

			// on envoie la requête
			//echo $sql5;
			$req5 = mysql_query($sql5);
			$data5 = mysql_fetch_assoc($req5);
			$sql2 = 'UPDATE `oc_product` SET upc="'.substr($data['upc'],0,12).'" WHERE `product_id` ='.$data['product_id'];
			//echo $sql2;
			$req2 = mysql_query($sql2);
			$keywordindex=$data5['googlesearchphrase'];



			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data['manufacturer_id'];

			// on envoie la requête
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$sql6 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where language_id=1 and oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$data['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req6 = mysql_query($sql6);
			$data6 = mysql_fetch_assoc($req6);
			$categoryname=$data6['name'];
			$sql6 = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where language_id=2 and oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$data['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req6 = mysql_query($sql6);
			$data6 = mysql_fetch_assoc($req6);
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
			$description.='<strong>UPC : </strong>'.substr($data['upc'],0,12).'<br>';
			$descriptionf.='<strong>UPC : </strong>'.substr($data['upc'],0,12).'<br>';
			$description.='<strong>Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$descriptionf.='<strong>Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$descriptionf.='<strong>Poids : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$sql2 = 'SELECT * FROM `oc_condition` where language_id=1 and condition_id='.$data['condition_id'];
//echo $sql2;
			// on envoie la requête
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
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
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);
			$descriptionf.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data3['name']).'</strong><br>';
			$descriptionf.=addslashes(strtoupper($data3['condition'])."<br>");
			$conditionnamef=strtoupper($data3['name']);

			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			$descriptionf.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
			$descriptionf.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
							
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$$data['product_id'];
			$req2= mysql_query($sql2); 
			//echo $sql2."<br>";
			while($data2 = mysql_fetch_assoc($req2))
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
			$sql2 = 'UPDATE `oc_product_description` SET description_mod=2,tag="'.$conditionname.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'",meta_keyword="'.$conditionname.','.strtoupper($keywordindex).','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'", meta_title="'.$conditionname." ".addslashes(strtoupper($name)).'",name="'.addslashes(strtoupper($name)).'", description="'.$description.'", meta_description="Liquidation '.addslashes(strtoupper($categoryname.' '.strtoupper($keywordindex).' '.$name)).' '.$conditionname.' at the lowest price of $'.doubleval ($data['price']).' USD Model: '.$data['model'].' UPC:'.$data['upc'].'" WHERE language_id=1 and `product_id` ='.$data['product_id'];
			//echo $sql2;
			$req2 = mysql_query($sql2);
			$sql2 = 'UPDATE `oc_product_description` SET description_mod=2,tag="'.$conditionnamef.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'",meta_keyword="'.$conditionnamef.','. str_replace(' ', ',', addslashes(strtoupper($name))).','.$data['upc'].'", meta_title="'.$conditionnamef." ".addslashes(strtoupper($name)).'",name="'.addslashes(strtoupper($name)).'", description="'.$descriptionf.'", meta_description="Liquidation '.addslashes(strtoupper($categorynamef.' '.$name)).' '.$conditionnamef.' au meilleur prix de $'.doubleval ($data['price']).' USD Modele: '.$data['model'].' UPC:'.$data['upc'].'"  WHERE language_id=2 and `product_id` ='.$data['product_id'];
			//echo $sql2;
			$req2 = mysql_query($sql2);



	}
			echo $calcitem;  

?>