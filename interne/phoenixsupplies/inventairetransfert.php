<?
//echo $_POST['upc'];
$upc=$_POST['upc'];
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on sélectionne la base 
 
// on crée la requête SQL verifier les ordres 
// savoir ledernier id 
 
 
// met a jour les item deja lister
/* 		$sql = 'SELECT * FROM `oc_product` where invoice = "3749335" order by sku';
//echo $sql;
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		$skuanc="";
		$quantityrecu=0;
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		while($data = mysqli_fetch_assoc($req)){
			//echo $data[sku];
				//echo $quantityrecu."<br>".$data[quantity]."<br>";

				$sql2 = 'UPDATE `oc_product_reception` SET quantityrecu=quantityrecu+'.$data[quantity].' where upc ="'.substr($data[sku],0,12).'"';
				echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				//$data2 = mysqli_fetch_assoc($req2); 

		}  */

if (isset($_POST['invoice'])){
	
/* 	// met a jour les item deja lister
		$sql = 'SELECT * FROM `oc_product` where invoice = "'.$_POST['invoice'].'" order by price';
//echo $sql;
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		while($data = mysqli_fetch_assoc($req)){
			$sql2 = 'UPDATE `oc_product_reception` SET transfert=1,priceebay="'.$data[price].'" where upc ="'.$data['sku'].'"';
			//echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2); 
		} */
	
 		$sql = 'SELECT * FROM `oc_product_reception` where quantityrecu>0 and transfert=0 and close=0 and recycle=0';
//echo $sql;
		// on envoie la requête
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		while($data = mysqli_fetch_assoc($req)){
				$data5['manufacturer_id']=0;
				if($data['title']!=""){
						if($data['brand']!=""){
							$sql5 = 'SELECT * FROM `oc_manufacturer` where name = "'.$data['brand'].'"';
							//echo $sql5.'<br><br>';

							// on envoie la requête
							$req5= mysqli_query($db,$sql5);
							// on fait une boucle qui va faire un tour pour chaque enregistrement
							$data5 = mysqli_fetch_assoc($req5);
							if(mysqli_affected_rows ()==0){
								
									$sql2 = 'INSERT INTO `oc_manufacturer` (`name`) VALUES ("'.addslashes(strtoupper($data['brand'])).'"';
									//echo $sql2;
									$req2 = mysqli_query($db,$sql2);
									$data5['manufacturer_id']= mysqli_insert_id($db);
									//echo $sql2;
									$sql2 = 'INSERT INTO `oc_manufacturer_to_store` (`manufacturer_id`) VALUES ("'.$data5['manufacturer_id'].'")';
									//echo $sql2;
									$req2 = mysqli_query($db,$sql2);
									//echo $_POST['manufacturer'];
							}
						}		
						$sql3 = 'SELECT * FROM `oc_product_analyse` where upc = "'.$data['upc'].'" ';
				//echo $sql;
						// on envoie la requête
						$req3 = mysqli_query($db,$sql3);
						// on fait une boucle qui va faire un tour pour chaque enregistrement
						$data3 = mysqli_fetch_assoc($req3);
				
						$sql2 = 'INSERT INTO `oc_product` (`weight_class_id`,`length_class_id`,`usa`,`model`, `sku`, `upc`, `mpn`,`quantity`,`manufacturer_id`, ';
						$sql2 .='`weight`, `height`, `length`,`width`,`color`,`ean`,`asin`,`tax_class_id`, `status`, `condition_id`,`invoice`)';
						$sql2 .=' VALUES ("7","3","1","'.addslashes(strtoupper($data['model'])).'", "'.$data['upc'].'", "'.$data['upc'].'", "'.addslashes(strtoupper($data['model'])).'","0","'.$data5['manufacturer_id'].'",';
						$sql2 .='"0", "0", "0", "0", "'.$data3['color'].'", "'.$data3['ean'].'","'.$data3['asin'].'","9", "0", "9","'.$_POST['invoice'].'");';
						//echo $sql2.'<br><br>';
						
						$req2 = mysqli_query($db,$sql2);
						$product_id= mysqli_insert_id($db);
						//echo $_POST['categoryarbonum'];
						// entree les category_id
						$categorynametab=explode('>', $_POST['categoryarbonum']);
						$sql7 = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$product_id."', '183630')";
						$req7 = mysqli_query($db,$sql7);
						$sql7 = "INSERT INTO `oc_product_description` (`product_id`, `name`, `meta_title` ,`language_id`) VALUES ('".$product_id."', '".addslashes(strtoupper($data['title']))."', '".addslashes(strtoupper($data['title']))."', '1')";
						$req7 = mysqli_query($db,$sql7);	
						echo $sql7."<br>";
						$sql7 = "INSERT INTO `oc_product_to_store` (`product_id`) VALUES ('".$product_id."')";
						$req7 = mysqli_query($db,$sql7);	
						//VIDAGE CHAMPS
						$_POST['category_id'] ="";
						$_POST['name_product']="";
						//$_POST['sku']="";
						$_POST['etat']="";
						$_POST['model']="";
						$_POST['name_product']="";
						$_POST['manufacturer']="";
						$_POST['new']=0;
						$_POST['categoryarbonum']="";
						$_POST['upc']="";
						$_POST['priceebaysold']="";
						$_POST['priceterasold']="";
						$_POST['priceebaynow']="";
						$_POST['quantity']=0;
					
						
						
						
						$sql2 = 'UPDATE `oc_product_reception` SET transfert=1 where upc ="'.$data['upc'].'"';
						//echo $sql2.'<br><br>';
						$req2 = mysqli_query($db,$sql2);
						$data2 = mysqli_fetch_assoc($req2); 
				}
		}
if ($_POST['closeinvoice']=="yes"){
		$sql2 = 'UPDATE `oc_product_reception` SET close=1 where invoice="'.$_POST[invoice].'"';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$data2 = mysqli_fetch_assoc($req2); 
}
if ($_POST['openinvoice']=="yes"){
		$sql2 = 'UPDATE `oc_product_reception` SET close=0 where invoice="'.$_POST[invoice].'"';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$data2 = mysqli_fetch_assoc($req2); 
}
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
<link href="stylesheet.css" rel="stylesheet">

</head>
<body bgcolor="<?echo $bgcolor;?>">
<form id="form_67341" class="appnitro" action="inventairetransfert.php" method="post">
<div class="form_description">
<h1>Inventaire Reception Item</h1>
</div>
<br><br>
<h1><?echo $data['title'];?></h1>
<h1><?echo $data['upc'];?></h1>
<h3>INVOICE <input id="invoice" class="element text medium" style="font-size: 13px; font-weight: normal;" type="text" name="invoice" value="<?echo $_POST['invoice'];?>" maxlength="255" /></h3>

<p class="buttons">
<input type="hidden" name="product_id" value="<?echo $data['product_id'];?>" />
<input type="hidden" name="new" value="<?echo $new;?>" />
<input type="hidden" name="status" value="1" />
<input type="checkbox" name="closeinvoice" value="yes"/>
		Fermer la facture:
<input type="checkbox" name="openinvoice" value="yes"/>
Reouvrir la facture:
<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>
</form>
<p id="footer"> 
</body>
</html>
<? echo 'FINI'; // on ferme la connexion à 
mysqli_close($db); ?>