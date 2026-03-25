<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s&eacute;lectionne la base 
mysql_select_db('phoenkv5_store',$db); 
// on cr&eacute;e la requête SQL verifier les ordres 
// savoir ledernier id 

			$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$_POST['product_id'].'"';
	//echo $sql;
			$req3 = mysql_query($sql3);
			$data3 = mysql_fetch_assoc($req3);


if ($_POST['carac']!=""){
	//echo "allo";
	$test=cloneritem ("OK", $typeetat,$data3['condition_id'],$data3['sku'].$_POST['carac'],$_POST['product_id']);
	$sku=$data3['sku'].$_POST['carac'];
	//echo $sku."-".$_POST['carac']."-".$_POST['product_id'];
	header("location: ".$_POST['action'].".php?sku=".$sku); 
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
<body bgcolor="ffbaba">
<h1>Item a cloner </h1>
<h3>Sku a cloner: (<?echo $_GET['sku'];?>)</h3>
<form id="form_67341" class="appnitro" action="clonerusa.php?action=<?echo $_GET['action'] ?>" method="post">

		
		
		<td>Max 5 caracteres : <input id="carac"  type="text" name="carac" value="" size="5" /></td>
		<input type="hidden" name="product_id" value="<?echo $_GET['product_id'];?>" />
		<input type="hidden" name="action" value="<?echo $_GET['action'];?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</form>

 <h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

</body>
</html>
<? // on ferme la connexion à mysql /* z */
function cloneritem ($rowverif, $typeetat,$numetat,$skuachanger,$default_product_id) {
	
 			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and `oc_product`.product_id = "'.$default_product_id.'"';
			//echo $sql."<br>";
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req); 
	
			if($data['length']==0 || $data['height']==0 || $data['width']==0 || $data['weight']==0)$erreurvide="***Champs vide***";
		if(($rowverif==0 && $erreurvide=="" )|| $rowverif=="OK" ){
			//echo "ALLO";
/* 			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and `oc_product`.sku = "'.$_POST['sku'].'"';
			echo $sql."<br>";
			$req = mysql_query($sql);
			$data = mysql_fetch_assoc($req); */
			$sql6 = 'INSERT INTO `oc_product` (price,`weight_class_id`,`length_class_id`,`usa`,`model`, `sku`, `upc`, `mpn`,`quantity`,`manufacturer_id`, ';
			$sql6 .='`weight`, `height`, `length`,`width`,`color`,`ean`,`asin`,`tax_class_id`, `status`, `condition_id`,`invoice`,`image`)';
			$sql6 .=' VALUES ("'.$data['price'].'","7","3","1","'.strtoupper($data['model']).'", "'.$skuachanger.'", "'.substr($data['sku'],0,12).'", "'.strtoupper($data['model']).'","0","'.$data['manufacturer_id'].'",';
			$sql6 .='"'.$data['weight'].'", "'.$data['height'].'", "'.$data['length'].'", "'.$data['width'].'", "'.$data['color'].'", "'.$data['ean'].'","'.$data['asin'].'","9", "0", "'.$numetat.'","'.$data['invoice'].'","'.$data['image'].'");';
			//echo $sql6.'<br><br>';
			
			$req6 = mysql_query($sql6);
			$product_id= mysql_insert_id();
			//echo $_POST['categoryarbonum'];
			// entree les category_id s

			$categorynametab=explode('>', $_POST['categoryarbonum']);
			
			//description
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data['manufacturer_id'];
			$description='';
			// on envoie la requête
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='<h2>Description :</h2>';
			if($_POST['andescription']!='')$description.=$_POST['andescription'].'<br><br>';
			$description.='<strong>Title : </strong>'.strtoupper($data['name']).'<br><strong>Model : </strong>'.strtoupper($data['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data2['name']).'<br>';
			if($data['color']=="")$data['color']="N/A";
			$description.='<strong>Color : </strong>'.strtoupper($data['color']).'<br>';
			$description.='<strong>Dimension : </strong>'.doubleval ($data['length']).'x'.doubleval ($data['width']).'x'.doubleval ($data['height']).' Inch<br>';
			$description.='<strong>Weight : </strong>'.doubleval ($data['weight']).' Lbs<br>';
			$sql2 = 'SELECT * FROM `oc_condition` where condition_id='.$numetat;
//echo $sql2;
			// on envoie la requête
			$req2 = mysql_query($sql2);
			$data2 = mysql_fetch_assoc($req2);
			$description.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data2['name']).'</strong><br>';
			$description.=strtoupper($data2['condition'])."<br>";
	


			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"450\"</td></tr>';
							
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$data['product_id'];
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
			$data['name']=htmlentities($data['name'], ENT_QUOTES);
			$description=htmlentities($description, ENT_QUOTES);
			
			//
			$sql7 = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`,`description`) VALUES ('".$product_id."', '".strtoupper($data['name'])."', '".strtoupper($data['name'])."', '1','".$description."')";
			$req7 = mysql_query($sql7);	
			//echo $sql7."<br>";
			$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183630')";
			$req7 = mysql_query($sql7);
			$sql3 = 'SELECT * FROM `oc_product_image` where product_id = "'.$data['product_id'].'"';
			//echo $sql3."<br>";
			$req3 = mysql_query($sql3);
			while($data3 = mysql_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_image` (`product_id`, `image`) VALUES ('".$product_id."', '".$data3[image]."')";
				$req7 = mysql_query($sql7);
				//echo $sql7."<br>";
			}
			$sql3 = 'SELECT * FROM `oc_product_to_category` where product_id = "'.$data['product_id'].'"';
			//echo $sql3."<br>";
			$req3 = mysql_query($sql3);
			while($data3 = mysql_fetch_assoc($req3)){
				$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '".$data3[category_id]."')";
				$req7 = mysql_query($sql7);
				//echo $sql7."<br>";
			}

			$sql7 = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
			$req7 = mysql_query($sql7);	
			//echo $sql7."<br>";
			
			// mettre a jour les dim et poids 
			

		}
}
mysql_close(); ?>