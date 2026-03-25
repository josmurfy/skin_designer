<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s&eacute;lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr&eacute;e la requ�te SQL verifier les ordres 
// savoir ledernier id 
//echo $_GET['sku'];
//echo 'check '.$_POST['skucheck'];
//echo $new;
if ($_GET['sku']!=""){
	$_POST['sku']=$_GET['sku'];
	//echo 'allo';
}

if ($_POST['sku']=="" && $_POST['skucheck']==""){
	$new=0;
	//echo 'allo';
}
if($_POST['skucheck']!="")$_POST['sku']=$_POST['skucheck'];
//echo $_POST['newsku'];
//echo $_POST['new'];


if ($_POST['name_product']!="" && $_POST['sku']!="" 
	&& $_POST['model']!="" && $_POST['manufacturer']!="" && $_POST['new']==1){
		
	if($_POST['etat']!=$_POST['etatavant']){
		$_POST['skuold']=$_POST['sku'];
		if($_POST['etat']=="9")$_POST['sku']=substr($_POST['sku'],0,12);
		if($_POST['etat']=="99")$_POST['sku']=substr($_POST['sku'],0,12)."NO";
		if($_POST['etat']=="2")$_POST['sku']=substr($_POST['sku'],0,12)."MR";
		if($_POST['etat']=="22")$_POST['sku']=substr($_POST['sku'],0,12)."R";
		if($_POST['etat']=="8")$_POST['sku']=substr($_POST['sku'],0,12)."U";
		//verifier si existant
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.$_POST['sku'].'"';
	//echo $sql;
			$req = mysql_query($sql);
			$rowverif= mysql_affected_rows();
			$data =mysql_fetch_assoc($req);
			if($_POST['length']==0 || $_POST['height']==0 || $_POST['width']==0 || $_POST['weight']==0)$erreurvide="***Champs vide***";
		if($rowverif==0 && $erreurvide==""){
			//echo "ALLO";
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and `oc_product`.sku = "'.$_POST['skuold'].'"';
			//echo $sql."<br>";
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$sql6 = 'INSERT INTO `oc_product` (`weight_class_id`,`length_class_id`,`usa`,`model`, `sku`, `upc`, `mpn`,`quantity`,`manufacturer_id`, ';
			$sql6 .='`weight`, `height`, `length`,`width`,`color`,`ean`,`asin`,`tax_class_id`, `status`, `condition_id`,`invoice`,`image`)';
			$sql6 .=' VALUES ("7","3","1","'.strtoupper($data['model']).'", "'.$_POST['sku'].'", "'.$_POST['sku'].'", "'.strtoupper($data['model']).'","0","'.$data['manufacturer_id'].'",';
			$sql6 .='"'.$data['weight'].'", "'.$data['height'].'", "'.$data['length'].'", "'.$data['width'].'", "'.$data['color'].'", "'.$data['ean'].'","'.$data['asin'].'","9", "0", "'.$_POST['etat'].'","'.$_POST['invoice'].'","'.$data['image'].'");';
			//echo $sql6.'<br><br>';
			
			$req6 = mysql_query($sql6);
			$product_id= mysql_insert_id();
			//echo $_POST['categoryarbonum'];
			// entree les category_id s

			$categorynametab=explode('>', $_POST['categoryarbonum']);


			$sql7 = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`) VALUES ('".$product_id."', '".strtoupper($data['name'])."', '".strtoupper($data['name'])."', '1')";
			$req7 = mysql_query($sql7);	
			//echo $sql7."<br>";
			$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183630')";
			$req7 = mysql_query($sql7);
			$sql3 = 'SELECT * FROM `oc_product_image` where product_id = "'.$data['product_id'].'"';
			//echo $sql3;
			$req3 = mysql_query($sql3);
			while($data3 = mysql_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_image` (`product_id`, `image`) VALUES ('".$product_id."', '".$data3[image]."')";
				$req7 = mysql_query($sql7);
				//echo $sql7;
			}
			$sql3 = 'SELECT * FROM `oc_product_to_category` where product_id = "'.$data['product_id'].'"';
			//echo $sql3;
			$req3 = mysql_query($sql3);
			while($data3 = mysql_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '".$data3[category_id]."')";
				$req7 = mysql_query($sql7);
				//echo $sql7;
			}

			$sql7 = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
			$req7 = mysql_query($sql7);	
			//echo $sql7."<br>";
			
			// mettre a jour les dim et poids
			

		}elseif($rowverif==0){
			$_POST['sku']=$_POST['skuold'];
		}
	}
	
		$sql2 = 'UPDATE `oc_product` SET `inventaire`=1, `invoice`="'.$_POST['invoice'].'",`model`="'.strtoupper($_POST['model']).'", `upc`="'.$_POST['sku'].'"';
		$sql2 .=', `mpn` = "'.strtoupper($_POST['model']).'",`quantity` = "0'.$_POST['quantity'].'",`manufacturer_id` = "'.$_POST['manufacturer'].'"';
		$sql2 .=', `price` ="'.$_POST['price'].'", `priceterasold` = "'.$_POST['priceterasold'].'", `priceebaynow` ="'.$_POST['priceebaynow'].'"';
		$sql2 .=', `location`="'.$_POST['location'].'" ,`weight`="'.$_POST['weight'].'",`height`="'.$_POST['height'].'"';
		$sql2 .=', `width`="'.$_POST['width'].'",`length`="'.$_POST['length'].'",`remarque_interne`="'.$_POST['remarque'];
		$sql2 .='" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

		//echo $sql2.'<br><br>';
		//UPDATE `oc_product` SET `REMARQUE_CORRECTION` = '1' WHERE `oc_product`.`product_id` = 309;
		$req2 = mysql_query($sql2); 
		$sql2 = 'UPDATE `oc_product` SET `weight`="'.$_POST['weight'].'",`height`="'.$_POST['height'].'"';
		$sql2 .=', `width`="'.$_POST['width'].'",`length`="'.$_POST['length'];
		$sql2 .='" WHERE `oc_product`.`usa`=1 and`oc_product`.`upc` ='.substr($_POST['sku'],0,12);

		//echo $sql2.'<br><br>';	  
		$req2 = mysql_query($sql2); 
if ($_POST['manufacturer']!="" && $_POST['manufacturersupp']=="")$_POST['manufacturer_id']=$_POST['manufacturer'];

if (isset($_POST['manufacturersupp'])&& $_POST['manufacturer_id']==0 && $_POST['manufacturersupp']!=""){
	$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.strtoupper($_POST['manufacturersupp']).'")';
	//echo $sql2;
	$req2 = mysql_query($sql2);
	$_POST['manufacturer_id']= mysql_insert_id();
	$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$_POST['manufacturer_id'].'")';
	//echo $sql2;
	$req2 = mysql_query($sql2);
	//echo $_POST['manufacturer'];
	
}
if (isset($_POST['category_id']) && $_POST['category_id']!=""){
	
	
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$_POST['category_id'];
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$name=$data['name'];
			//echo $data[parent_id];
			while($data[parent_id]>0){
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$_POST['product_id']."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysql_query($sql2);
				$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id='.$data[parent_id];
	//echo $sql;
				$req = mysql_query($sql);
				$data = mysql_fetch_assoc($req);

			}
				$sql2 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$_POST['product_id']."', '".$data['category_id']."')";
				//echo $sql2."<br>";
				$req2 = mysql_query($sql2);
	
/* 		$_POST['category_id']=str_replace(array("\r","\n"),"",$_POST['category_id']);
		$categorynametab=explode('>', $_POST['category_id']);
		$category_id=0;
		$categoryarbonum="";
		//if($_POST['newsku']==1)$_POST['sku']=mt_rand ( 100000000000 , 999999999999 );
		foreach($categorynametab as $categoryname) 
		{
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and parent_id='.$category_id.' and name like "'.$categoryname.'"';
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$category_id=$data['category_id'];
			$name=$data['name'];
			$categoryarbonum.=$data['category_id'].">";
			$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id` ) VALUES ('".$_POST['product_id']."', '".$data['category_id']."')";
			//echo $sql."<br>";
			$req = mysql_query($sql);
		}
		$categorynametab=explode('>', $_POST['categoryarbonum']);
		foreach($categorynametab as $categoryname) 
		{
			//echo $categoryname;
			if ($categoryname !=""){

			}
			
		} */
		//$new=1;
		//if (isset($_POST['etat'])){$new2=1;}
		//if (isset($_POST['manufacturer'])==false){$_POST['manufacturer']=0;}

}		

			$_POST['condition']=",";
			$i=0;
			foreach($_POST['conditiondetail'] as $conditiondetail) 
			{
				$_POST['condition'].=$conditiondetail.",";
			}
			foreach($_POST['conditionsdetailsupp'] as $conditiondetailsupp) 
			{

				if($conditiondetailsupp!="" && $_POST['conditionsdetailsuppch'][$i]>0){
					$sql2 = 'INSERT INTO `oc_condition_variant` (`name`, `status`, `type`, `condition_id`)';
					$sql2 .=' VALUES ("'.strtoupper($conditiondetailsupp).'", "1", "2", ",'.$_POST['etat'].',");';
					//echo $sql2.'<br><br>';
					
					$req2 = mysql_query($sql2);
					$conditiondetail= mysql_insert_id();
				$_POST['condition'].=$conditiondetail.",";
				$_POST['conditionsdetailsupp'][$i]="";
				$_POST['conditionsdetailsuppch'][$i]=0;
				}elseif ($conditiondetailsupp!=""){
					$_POST['condition'].=strtoupper($conditiondetailsupp).",";
				}
				
				$i++;
			}
			
			//echo $_POST['condition'];
			$_POST['accessory']=",";
			$i=0;
			foreach($_POST['accessorysupp'] as $accessorysupp) 
			{
				$_POST['accessory'].=$accessorysupp.",";
			}
			foreach($_POST['accesoiressupp'] as $accessorysupp) 
			{

				if($accessorysupp!="" && $_POST['accesoiressuppch'][$i]>0){
					$sql2 = 'INSERT INTO `oc_condition_variant` (`name`, `status`, `type`, `condition_id`)';
					$sql2 .=' VALUES ("'.strtoupper($accessorysupp).'", "1", "1", ",'.$_POST['etat'].',");';
					//echo $sql2.'<br><br>';
					
					$req2 = mysql_query($sql2);
					$accessorysupp= mysql_insert_id();
				$_POST['accessory'].=$accessorysupp.",";
				$_POST['accesoiressupp'][$i]="";
				$_POST['accesoiressuppch'][$i]=0;
				}elseif ($accessorysupp!=""){
					$_POST['accessory'].=strtoupper($accessorysupp).",";
				}
				
				$i++;
			}
			//echo $_POST['accessory'];
			
			$_POST['test']=",";
			$i=0;
			foreach($_POST['testsupp'] as $testsupp) 
			{
				$_POST['test'].=$testsupp.",";
			}
			foreach($_POST['testssupp'] as $testsupp) 
			{
				//echo $_POST[testssuppch][$i];
				if($testsupp!="" && $_POST['testssuppch'][$i]>0){
					$sql2 = 'INSERT INTO `oc_condition_variant` (`name`, `status`, `type`, `condition_id`)';
					$sql2 .=' VALUES ("'.strtoupper($testsupp).'", "1", "3", ",'.$_POST['etat'].',");';
					//echo $sql2.'<br><br>';
					
					$req2 = mysql_query($sql2);
					$testsupp= mysql_insert_id();
				$_POST['test'].=$testsupp.",";
				$_POST['testssuppch'][$i]=0;
				$_POST['testssupp'][$i]="";
				}elseif ($testsupp!=""){
					$_POST['test'].=strtoupper($testsupp).",";
				}
				
				$i++;
			}
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer'];

			// on envoie la requ�te
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			//echo $_POST['test'];
			// LA description Formater
			$description.='<h2>Description :</h2>';
			if($_POST['andescription']!='')$description.=$_POST['andescription'].'<br><br>';
			$description.='<strong>Title : </strong>'.strtoupper($_POST['name_product']).'<br><strong>Model : </strong>'.strtoupper($_POST['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data2['name']).'<br>';
			$description.='<strong>Dimension : </strong>'.doubleval ($_POST['length']).'x'.doubleval ($_POST['width']).'x'.doubleval ($_POST['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($_POST['weight']).' Lbs<br>';
			$sql2 = 'SELECT * FROM `oc_condition` where condition_id='.$_POST['etat'];
//echo $sql2;
			// on envoie la requ�te
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data2['name']).'</strong><br>';

$conditionsupp=explode(',', $_POST['condition']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}elseif(is_numeric($conditioncheck)==true){
			$sql2 = 'SELECT * FROM `oc_condition_variant` where condition_variant_id='.$conditioncheck;
			// on envoie la requ�te
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='- '.$data2['name'].'<br>';
	}
}			
			
			if($_POST['accessory']!="" && $_POST['accessory']!=",")$description.='</p><h2>Accessories Included :</h2><p>';
$conditionsupp=explode(',', $_POST['accessory']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}elseif(is_numeric($conditioncheck)==true){
			$sql2 = 'SELECT * FROM `oc_condition_variant` where condition_variant_id='.$conditioncheck;
			// on envoie la requ�te
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='- '.$data2['name'].'<br>';
	}
}		
//echo $_POST['test'];		
		if($_POST['test']!="" && $_POST['test']!=",")$description.='</p><h2>Tests - Repairs Done :</h2><p>';
$conditionsupp=explode(',', $_POST['test']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}elseif(is_numeric($conditioncheck)==true){
			$sql2 = 'SELECT * FROM `oc_condition_variant` where condition_variant_id='.$conditioncheck;
			// on envoie la requ�te
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='- '.$data2['name'].'<br>';
	}
}	
			$sql = 'SELECT * FROM `oc_product`where product_id ="'.$_POST['product_id'].'"';
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);	


			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
							
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
			$req2= mysql_query($sql2); 
			while($data2 = mysql_fetch_assoc($req2))
			{
				if($i<13){
					$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data2['image'].'\" width=\"450\"</td></tr>';
				$i++;
				}
			}
			$description.='</tbody></table><br>';
			//echo $description;
			$_POST['name_product']=htmlentities($_POST['name_product'], ENT_QUOTES);
			$description=htmlentities($description, ENT_QUOTES);
			
if ($_POST['changedescription']=="yes"){
				$sql2 = 'UPDATE `oc_product_description` SET description_mod=1,name="'.strtoupper($_POST['name_product']).'", description="'.$description.'",`condition`="'.$_POST['condition'].'",`accessory`="'.$_POST['accessory'].'",`test`="'.$_POST['test'].'" WHERE `product_id` ='.$_POST['product_id'];

}else{
				$sql2 = 'UPDATE `oc_product_description` SET name="'.strtoupper($_POST['name_product']).'",`condition`="'.$_POST['condition'].'",`accessory`="'.$_POST['accessory'].'",`test`="'.$_POST['test'].'" WHERE `product_id` ='.$_POST['product_id'];

}	
			//echo $sql2;
			$req2 = mysql_query($sql2);
			if($_POST['status']==1){
				$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$_POST['product_id']."', '183628')";
				$req = mysql_query($sql);
				//echo $sql;
				$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183630'";
				$req = mysql_query($sql);	
				//echo $sql;				
			}
		$new=1;
}
if (isset($_POST['sku']) && $_POST['sku']!=""){
			
			//echo $_POST['sku'];
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.$_POST['sku'].'"';
	//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			$_POST['sku']=$data['sku'];
			$_POST['etat']=$data['condition_id'];
			//echo $_POST['etat'];
			$_POST['model']=$data['model'];
			$_POST['manufacturer']=$data['manufacturer_id'];
			$_POST['new']=1;
			$_POST['product_id']=$data['product_id'];
			//echo $_POST['product_id'];
			$_POST['upc']=$data['upc'];
			$_POST['price']=$data['price'];
			$_POST['priceebaysold']=$data['priceebaysold'];
			$_POST['priceterasold']=$data['priceterasold'];
			$_POST['priceebaynow']=$data['priceebaynow'];
			$_POST['quantity']=$data['quantity'];
			$_POST['price']=$data['price'];
			$_POST['weight']=$data['weight'];
			$_POST['length']=$data['length'];
			$_POST['width']=$data['width'];
			$_POST['height']=$data['height'];
			$_POST['location']=$data['location'];
			$_POST['invoice']=$data['invoice'];
			//$_POST['category_id'] ="";
			//$_POST['categoryarbonum']="";
			$_POST['accessory']=$data['accessory'];
			$_POST['condition']=$data['condition'];
			$_POST['test']=$data['test'];
			$_POST['description']=$data['description'];
			$_POST['image']=$data['image'];
			$sql = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$_POST['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req);
			$name=$data['name'];
			$_POST['category_id']=$data['category_id'];
			


			$new=1;
	  		$sql9 = 'SELECT * FROM `oc_product_analyse` where upc="'.substr ($_POST['sku'],0,12).'" group by upc';
			//echo $sql9;
			$req9 = mysql_query($sql9);
			$data9 = mysql_fetch_assoc($req9);
			$_POST['andescription']=$data9['description'];
			$sql8 = 'SELECT * FROM `oc_product_reception` where upc="'.substr ($_POST['sku'],0,12).'" group by upc';
			//echo $sql8;
			$req8 = mysql_query($sql8);
			$data8 = mysql_fetch_assoc($req8);
			
if($_POST['etat']=="9")$algoetat="New&costNew=".$data8[pricecost];
if($_POST['etat']=="99")$algoetat="New&costNew=".$data8[pricecost];
if($_POST['etat']=="2")$algoetat=="Refurbished&costRefurbished=".$data8[pricecost]."&costNew=".$data8[pricecost];
if($_POST['etat']=="22")$algoetat="Refurbished&costRefurbished=".$data8[pricecost]."&costNew=".$data8[pricecost];
if($_POST['etat']=="8")$algoetat="Used&costUsed=".$data8[pricecost]."&costNew=".$data8[pricecost];	  

	 $_POST['andescription']=$data9['description'];
	
	 $picexterne=$data9['imageurl'];
	 if($data9['shipping']==0)
	 {
		 $shipping=3.18;
	 }else{
		 $shipping =$data9['shipping'];
	 }

}?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<style> 
input[type=text] {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
textarea  {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}

select {
    width: 100%;
    padding: 8px 10px;
    margin: 8px 0;
    box-sizing: border-box;
    border: 2px solid #ccc;
    -webkit-transition: 0.5s;
    transition: 0.5s;
    outline: none;
}
select:focus {
    border: 3px solid #555;
}

input[type=text]:focus {
    border: 3px solid #555;
}
textarea:focus {
    border: 3px solid #555;
}
</style>
</head>
<body bgcolor="ffffff">
<h1>Modification Item </h1>
<?echo '<h1><font color="red">'.$erreurvide.'</font></h1>';?>
<form id="form_67341" class="appnitro" action="modificationitemusatest.php" method="post">
<div class="form_description">


</div>
<h3>SKU <?if($new==1){?><a href="modificationitemusa.php" class="button--style-red">Changer d'item</a><?}?> <a href="interneusa.php" class="button--style-red">Retour au MENU</a> <a href="multiuploadusa.php" class="button--style-red">Retour Photo</a></h3>
<input id="sku"  type="text" name="sku"  value="<?echo $_POST['sku'];?>" maxlength="255" <?if($new==1)echo "disabled";?> autofocus>
<input type="hidden" name="skucheck" value="<?echo $_POST['sku'];?>" />

<br>
<?if ($new>=1){?>
<svg class="barcode"
	jsbarcode-value="<?echo $_POST['sku'];?>"
	jsbarcode-textmargin="0"
	jsbarcode-height="24"
	jsbarcode-fontoptions="bold"
	jsbarcode-fontsize="12">
</svg>

<script>
JsBarcode(".barcode").init();

</script>
<?
if($picexterne!="")
{
	echo '<br><a href="'.$picexterne.'" target="_blank"><img src="'.$picexterne.'" alt="" width="100"/></a><br>';
}

?><h3>Quantite recu (<?echo $data8[quantityrecu];?>)</h3>
		<h3>Condition :</h3>
		<span> 
		<table>
		  <tr>
			<td><input id="etat_1" class="element radio" type="radio" name="etat" value="9" <?if ($_POST['etat']==9){?>checked<?}?>/> 
				<label class="choice" for="etat_1">New</label></td>
			<td><input id="etat_2" class="element radio" type="radio" name="etat" value="99" <?if ($_POST['etat']==99){?>checked<?}?>/> 
				<label class="choice" for="etat_2">New (Other)</label> <br></td>
		  </tr>
		  <tr>
			<td><input id="etat_3" class="element radio" type="radio" name="etat" value="2" <?if ($_POST['etat']==2){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Manufacturer Refurbished</label></td>
			<td><input id="etat_4" class="element radio" type="radio" name="etat" value="22" <?if ($_POST['etat']==22){?>checked<?}?>/> 
				<label class="choice" for="etat_3">Seller Refurbished</label> <br> </td>
		  </tr>
		  <tr>
			<td><input id="etat_5" class="element radio" type="radio" name="etat" value="8" <?if ($_POST['etat']==8){?>checked<?}?>/> 
				<label class="choice" for="etat_4">Used</label> </td>
			
		  </tr>
		</table>

<h3><label class="description" for="categorie">Categorie :</label></h3>
		<?if($new==1){?>Changer la description cliquez ici :<input type="checkbox" name="changedescription" value="yes" <?if($_POST['changedescription']=='yes')echo 'checked';?>/>
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<input id="category_id"  type="text" name="category_id" value="<?echo $_POST['category_id'];?>" maxlength="80" /> <br>
La categorie trouvee est : <b><span style="color: #ff0000;"><?echo $name;?></span></b>

<h3><label class="description" for="element_1">Titre:</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<br />

<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
<table width="100%" >
	<td width="34%" valign="top">
		<table width="100%">
		  <tr>
			<td valign="top"><h3>Brand :</h3></td>
			<td><select name="manufacturer">
			<option value="" selected></option>
<?
$sql = 'SELECT * FROM `oc_manufacturer` order by name';

// on envoie la requ�te
$req = mysql_query($sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysql_fetch_assoc($req))
    {
		$selected="";
		if (isset($_POST['manufacturer_id'])){
			$test2=strtolower ($_POST['manufacturer_id']);
			$test1=strtolower ($data['manufacturer_id']);
			if ($test1==$test2) {
				$selected="selected";
			}
		}else{
			$test2=strtolower ($data['name']);
			$test1=strtolower ($_POST['name_product']);
			if (strpos($test1, $test2) !== false) {
				$selected="selected";
				//echo 'allo';
			}
		}
	
		
		
?>
			<option value="<?echo $data['manufacturer_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
<?}?>
		</select><br>
		<input type="hidden" name="manufacturer_id" value="<?echo $_POST['manufacturer'];?>" />
		
		<input id="manufacturersupp"  type="text" name="manufacturersupp" value="<?echo $_POST['manufacturersupp'];?>" maxlength="80" <?if (isset($_POST['manufacturersupp']))echo "disabled";?>/>
		</td>
		  </tr>

<table>
<td>
<?echo html_entity_decode($_POST['description']);?> 
</td>
</table>


<br>
<input type="hidden" name="category_id" value="<?echo $category_id;?>" />
<table width="100%" >

		 <tr>
		<td><h3>Model :</h3></td>
		<td colspan="2"><input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="80" /></td>
	  </tr>
	   <tr>
		<td><h3>Facture d'achat :</h3></td>
		<td colspan="2"><input id="invoice" type="text" name="invoice" value="<?echo $_POST['invoice'];?>" maxlength="80" /></td>
	  </tr>
		</table>
	<tr>
		<td width= "20%"><h3>Prix  : </h3><input id="price" type="text" name="price" value="<?echo $_POST['price'];?>" size="10" disabled />
		</td>
	</tr>
	<tr>
	<td width="66%" valign="top">
	<table width="100%">
	  <tr>
		<td><h3>$ Price : </h3>
		</td>
		<td><input id="price" type="text" name="price" value="<?echo $_POST['price'];?>" size="10" /></td>

		<td>
		</td>
		<td></td>

		<td>
		</td>
		<td></td>

	  </tr>
	  <tr>
		<td><h3>Quantit&eacute; : </h3></td>
		<td><input id="quantity"  type="text" name="quantity" value="<?echo $_POST['quantity'];?>" size="10" /></td>
		<td><h3>Location : </h3></td>
		<td><input id="location"  type="text" name="location" value="<?echo $_POST['location'];?>" size="10" /></td>
		<td><h3><a href="createsmallbarcode.php?product_id=<?echo $_POST['product_id'];?>" target="_blank" style="color:#ff0000"><strong>SMALL LABEL</strong></a> <a href="createbarcodeusa.php?product_id=<?echo $_POST['product_id'];?>" target="_blank" style="color:#ff0000"><strong>Creation LABEL</strong></a></h3></td>
	  </tr>

	</table>

		<h3><label class="description" for="description">REMARQUE INTERNE : </label>
		<input id="remarque"  type="text" name="remarque" value="<?echo $_POST['remarque'];?>" maxlength="255" />
	<table width="100%">
	  <tr>
		<td><h3>Dimension :</h3></td>
		<td><input id="length" class="" type="text" name="length" value="<?echo $_POST['length'];?>" maxlength="5" /></td>
		<td><b>L</b></td>
		<td><input id="width" class="" type="text" name="width" value="<?echo $_POST['width'];?>" maxlength="5" /></td>
		<td><b>P</b></td>		
		<td><input id="height" class="" type="text" name="height" value="<?echo $_POST['height'];?>" maxlength="5" /></td>
		<td><b>H</b></td>	
		<td> </td>
		<td> </td>		
	  </tr>
	  <tr>
		<td><h3>Poids : </h3></td>		
		<td><input id="weight" class="" type="text" name="weight" value="<?echo $_POST['weight'];?>" maxlength="5" /> </td>
		<td><b>Lbs</b></td>
		<td> </td>
		<td> </td>
		<td> </td>
		<td> </td>
		<td> </td>
		<td> </td>
	  </tr>
	</table>
		

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		
		<?}?>
		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="etatavant" value="<?echo $_POST['etat'];?>" />
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="skuanc" value="<?echo $_POST['sku'];?>" />
		<input type="hidden" name="andescription" value="<?echo $_POST['andescription'];?>" />
		<?if($new==1){?>Changer la description cliquez ici :<input type="checkbox" name="changedescription" value="yes" <?if($_POST['changedescription']=='yes')echo 'checked';?>/>
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		
	</td>
</table>	
		
<?if($new==1){?>	
		<tr>
			<td>
		<h3>Conditions Detail:</h3>
		<input type="hidden" name="condition" value="<?echo $_POST['condition'];?>" />
<?
$sql = 'SELECT * FROM `oc_condition_variant` where type=2 and condition_id like "%,'.$_POST['etat'].',%"order by name';

// on envoie la requ�te
$req = mysql_query($sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysql_fetch_assoc($req))
    {

		
		//print_r($accarray[nameacc]);
		//echo $_POST['condition'].'*****'.$data['condition_variant_id'];
		$test=$data['condition_variant_id'].',';
?>
		<input type="checkbox" name="conditiondetail['<?echo $data['condition_variant_id'];?>']" value="<?echo $data['condition_variant_id'];?>" 
		<?if (strpos($_POST['condition'], $data['condition_variant_id'].',') == TRUE){echo 'checked';}?>> <?echo $data['name'];?><br>
<?}
//ajoute les comment suppl&eacute
//echo $_POST['condition'];
$conditionsupp=explode(',', $_POST['condition']);
$i=0;
$_POST['conditionsdetailsupp'][0]="";
$_POST['conditionsdetailsupp'][1]="";
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$_POST['conditionsdetailsupp'][$i]=$conditioncheck;
		//echo $i;
		$i++;
		
	}
}

?>
		<h3>Conditions suppl&eacute;mentaires :</h3>
		<table>
		  <tr>
			<td width="95%"><input type="text" name="conditionsdetailsupp[]" value="<?echo $_POST['conditionsdetailsupp'][0];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="conditionsdetailsuppch[]" <?if($_POST['conditionsdetailsuppch'][0]==1){echo 'checked';}?> value="1">Save</td>
		  </tr>
		  <tr>
			<td><input type="text" name="conditionsdetailsupp[]" value="<?echo $_POST['conditionsdetailsupp'][1];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="conditionsdetailsuppch[]" <?if($_POST['conditionsdetailsuppch'][1]==2){echo 'checked';}?> value="2">Save</td>
		  </tr>
		  
		</table>
	</td>
	</tr>
	<tr>
	<td>
		<h3>Accesoires Inclus ou Non Inclus:</h3>
		<input type="hidden" name="accessory" value="<?echo $_POST['accessory'];?>" />
<?
$sql = 'SELECT * FROM `oc_condition_variant` where type=1 and condition_id like "%,'.$_POST['etat'].',%"order by name';

// on envoie la requ�te
$req = mysql_query($sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysql_fetch_assoc($req))
    {

		//echo $_POST['condition'].'*****'.$data['condition_variant_id'];
		//print_r($accarray[nameacc]);
		echo $accarray[nameacc];
?>
		<input type="checkbox" name="accessorysupp['<?echo $data['condition_variant_id'];?>']" value="<?echo $data['condition_variant_id'];?>" 
		<?if (strpos($_POST['accessory'], $data['condition_variant_id'].',') == TRUE){echo 'checked';}?>> <?echo $data['name'];?><br>
<?}
//ajoute les comment suppl&eacute
//echo $_POST['condition'];
$accessorysupp=explode(',', $_POST['accessory']);
$i=0;
$_POST['accesoiressupp'][0]="";
$_POST['accesoiressupp'][1]="";
foreach($accessorysupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$_POST['accesoiressupp'][$i]=$conditioncheck;
		//echo $i;
		$i++;
		
	}
}?>
		<h3>Accesoires suppl&eacute;mentaires :</h3>
		<table>
		  <tr>
			<td width="95%"><input type="text" name="accesoiressupp[]" value="<?echo $_POST['accesoiressupp'][0];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="accesoiressuppch[]" <?if($_POST['accesoiressuppch'][0]==1){echo 'checked';}?> value="1">Save</td>
		  </tr>
		  <tr>
			<td><input type="text" name="accesoiressupp[]" value="<?echo $_POST['accesoiressupp'][1];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="accesoiressuppch[]" <?if($_POST['accesoiressuppch'][1]==2){echo 'checked';}?> value="2">Save</td>
		  </tr> 
		</table>
	</td>
	</tr>
		<tr>
	<td>
		<h3>Test effectu&eacute;s :</h3>
		<input type="hidden" name="test" value="<?echo $_POST['test'];?>" />
<?
$sql = 'SELECT * FROM `oc_condition_variant` where type=3 and condition_id like "%,'.$_POST['etat'].',%"order by name';

// on envoie la requ�te
$req = mysql_query($sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysql_fetch_assoc($req))
    {

		
		//print_r($accarray[nameacc]);
		//echo $testarray[nameacc];
?>
		<input type="checkbox" name="testsupp['<?echo $data['condition_variant_id'];?>']" value="<?echo $data['condition_variant_id'];?>" 
		<?if (strpos($_POST['test'], $data['condition_variant_id'].',') == TRUE){echo 'checked';}?>> <?echo $data['name'];?><br>
<?}//ajoute les comment suppl&eacute
//echo $_POST['condition'];
$testsupp=explode(',', $_POST['test']);
$i=0;
$_POST['testssupp'][0]="";
$_POST['testssupp'][1]="";
foreach($testsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$_POST['testssupp'][$i]=$conditioncheck;
		//echo $i;
		$i++;
		
	}
}?>
		<h3>Tests suppl&eacute;mentaires :</h3>
		<table>
		  <tr>
			<td width="95%"><input type="text" name="testssupp[]" value="<?echo $_POST['testssupp'][0];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="testssuppch[]" <?if($_POST['testssuppch'][0]=="1"){echo 'checked';}?> value="1">Save</td>
		  </tr>
		  <tr>
			<td><input type="text" name="testssupp[]" value="<?echo $_POST['testssupp'][1];?>" maxlength="80" /></td>
			<td><input type="checkbox" name="testssuppch[]" <?if($_POST['testssuppch'][1]=="2"){echo 'checked';}?> value="2">Save</td>
		  </tr> 
		</table>
	</td>
	</tr>
	</table>
<?}?>
</form>

 <h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>
  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['sku'],0,12);?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['sku'],0,12);?>&rt=nc&LH_PrefLoc=1","ebaynew");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo $manufsearch." ".$_POST['model'];?>&rt=nc&LH_PrefLoc=1","ebaynew2");
</script>
</body>
</html>
<? // on ferme la connexion � mysql 
mysql_close(); ?>