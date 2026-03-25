<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
//echo $_POST['length'].'allo1';
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr&eacute;e la requête SQL verifier les ordres 
// savoir ledernier id 
//echo (string)$_POST['sku'] ;
//echo 'check '.$_POST['skucheck'];
//echo $new;

//echo $_POST['newsku'];
//echo $_POST['name_product']."<br>".$_POST['price']."<br>".$_POST['model']."<br>".$_POST['manufacturer'];

//Estimation du prix 
//echo $_POST['andescription'];

if ($_POST['name_product']!="" && $_POST['model']!="" && $_POST['manufacturer']!="" && $_POST['new']==1){
		$sql2 = 'UPDATE `oc_product` SET ebay_last_check="2020-09-01",`inventaire`=1, `status`="'.$_POST['status'].'", price="'.$_POST['price'].'",`remarque_interne`="'.$_POST['remarque'];
		$sql2 .='" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];


		//echo $sql2.'<br><br>';
		//UPDATE `oc_product` SET `REMARQUE_CORRECTION` = '1' WHERE `oc_product`.`product_id` = 309;
		$req2 = mysqli_query($db,$sql2);
		

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
					
					$req2 = mysqli_query($db,$sql2);
					$conditiondetail= mysqli_insert_id($db);
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
					
					$req2 = mysqli_query($db,$sql2);
					$accessorysupp= mysqli_insert_id($db);
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
					
					$req2 = mysqli_query($db,$sql2);
					$testsupp= mysqli_insert_id($db);
				$_POST['test'].=$testsupp.",";
				$_POST['testssuppch'][$i]=0;
				$_POST['testssupp'][$i]="";
				}elseif ($testsupp!=""){
					$_POST['test'].=strtoupper($testsupp).",";
				}
				
				$i++;
			}
			$sql2 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$_POST['manufacturer'];

			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			//echo $_POST['test'];
			// LA description Formater
			//echo $_POST['length'].`allo`;
			//echo $_POST['andescription'];
			$description.='<h2>Description :</h2>';
			if($_POST['andescription']!='')$description.=$_POST['andescription'].'<br><br>';
			$description.='<strong>Title : </strong>'.strtoupper($_POST['name_product']).'<br><strong>Model : </strong>'.strtoupper($_POST['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data2['name']).'<br>';
			$description.='<strong>Dimension : </strong>'.number_format($_POST['length'], 1, '.', '').'x'.number_format($_POST['width'], 1, '.', '').'x'.number_format($_POST['height'], 1, '.', '').' Inch<br>';
			$description.='<strong>Weight : </strong>'.number_format($_POST['weight'], 1, '.', '').' Lbs<br>';
			$sql2 = 'SELECT * FROM `oc_condition` where condition_id='.$_POST['etat'];
			
//echo $description;
//echo $sql2;
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$description.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data2['name']).'</strong><br>';
$conditionsupp=explode(',', $_POST['condition']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}elseif(is_numeric($conditioncheck)==true){
			$sql2 = 'SELECT * FROM `oc_condition_variant` where condition_variant_id='.$conditioncheck;
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
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
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
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
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$description.='- '.$data2['name'].'<br>';
	}
}	
			$sql = 'SELECT * FROM `oc_product`where product_id ="'.$_POST['product_id'].'"';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);	


			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixliquidation.ca/image/'.$data['image'].'\" width=\"500\"</td></tr>';
							
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
			$req2= mysqli_query($db,$sql2); 
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
					$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixliquidation.ca/image/'.$data2['image'].'\" width=\"500\"</td></tr>';
				$i++;
				}
			}
			$description.='</tbody></table><br>';
			//echo $description;
			
if ($_POST['changedescription']=="yes"){
	//echo $_POST['changedescription'];
				$sql2 = 'UPDATE `oc_product_description` SET description_mod=1,name="'.strtoupper($_POST['name_product']).'", description="'.$description.'",`condition`="'.$_POST['condition'].'",`accessory`="'.$_POST['accessory'].'",`test`="'.$_POST['test'].'" WHERE `product_id` ='.$_POST['product_id'];
//echo $sql2;
}else{
				$sql2 = 'UPDATE `oc_product_description` SET name="'.strtoupper($_POST['name_product']).'",`condition`="'.$_POST['condition'].'",`accessory`="'.$_POST['accessory'].'",`test`="'.$_POST['test'].'" WHERE `product_id` ='.$_POST['product_id'];

}	
			$req2 = mysqli_query($db,$sql2);
			if($_POST['status']==1 && $_POST['price']!=0){
				$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183628'";
				$req = mysqli_query($db,$sql);	
				//echo $sql;				
			}
		$new=1;
		$_POST['new']=0;
}

			
			//echo (string)$_POST['sku'] ;
			$sql = 'SELECT ds.name,pr.sku,pr.condition_id,pr.model,pr.manufacturer_id,pr.product_id,pr.upc,
					pr.priceebaysold,pr.priceterasold,pr.priceebaynow,pr.quantity,pr.price,pr.weight,pr.length,pr.width,pr.height,
					pr.location,ds.accessory,ds.condition,ds.test,pr.status,ds.description,pr.invoice
					FROM `oc_product` as pr,`oc_product_description` as ds,oc_product_to_category as ca where pr.product_id=ds.product_id and 
			pr.product_id=ca.product_id and pr.quantity>0 and pr.usa=0 and ca.category_id=183628 and inventaire=1 order by pr.product_id limit 1 '; //and oc_product_to_category.category_id="183628" limit 1 ';
			//$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where NOT EXISTS
			//(SELECT * FROM oc_ebay_listing WHERE oc_ebay_listing.product_id=oc_product.product_id and oc_ebay_listing.status=1 ) and oc_product.product_id=oc_product_description.product_id 
			//and status = "1" and quantity>0 and description_mod=0 limit 1 ';
			//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			(string)$_POST['sku'] =$data['sku'];
			$_POST['etat']=$data['condition_id'];
			//echo $_POST['etat'];
			$_POST['model']=$data['model'];
			$_POST['manufacturer']=$data['manufacturer_id'];
			$_POST['new']=1;
			$_POST['product_id']=$data['product_id'];
			//echo $_POST['product_id'];
			
			
			(string)$_POST['upc']=(string)$data['upc'];
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
			$_POST['category_id'] ="";
			$_POST['categoryarbonum']="";
			$_POST['accessory']=$data['accessory'];
			$_POST['condition']=$data['condition'];
			$_POST['test']=$data['test'];
			$_POST['status']=$data['status'];
			$_POST['description']=$data['description'];
			$_POST['invoice']=$data['invoice'];
			$_POST['pricedetailusd']=$data['pricedetailusd'];
			$sql = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$_POST['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			$name=$data['name'];
			$_POST['category_id']=$data['category_id'];
			
			$new=1;
$differentiel=0;
if($_POST['etat']=="9")$differentiel=1;
if($_POST['etat']=="99")$differentiel=.85;
if($_POST['etat']=="2")$differentiel=.70;
if($_POST['etat']=="22")$differentiel=.70;
if($_POST['etat']=="8")$differentiel=.6;

	 
	  		$sql9 = 'SELECT * FROM `oc_product_analyse` where upc="'.substr ((string)$_POST['upc'],0,12).'" group by upc';
			//echo $sql9;
			$req9 = mysqli_query($db,$sql9);
			$data9 = mysqli_fetch_assoc($req9);
			$sql8 = 'SELECT * FROM `oc_product_reception` where upc="'.substr ((string)$_POST['upc'],0,12).'" group by upc';
			//echo $sql8;
			$req8 = mysqli_query($db,$sql8);
			$data8 = mysqli_fetch_assoc($req8);
	  
	 $_POST['andescription']=$data9['description'];
	 if($data9['shipping']==0)
	 {
		 $shipping=3.18;
	 }else{
		 $shipping =$data9['shipping'];
	 }
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<form id="form_67341" class="appnitro" action="modificationitemadmintest.php" method="post">
<div class="form_description">
<h1>Modification Item ADMIN</h1>

</div>
<h3>SKU <?if($new==1){?><a href="modificationitemadmin.php" >Changer d'item</a><?}?> <a href="interne.php" >Retour au MENU</a></h3>
<input id="sku"  type="text" name="sku" value="<?echo (string)$_POST['sku'] ;?>" maxlength="255" <?if($new==1)echo "disabled";?>/>
<input type="hidden" name="skucheck" value="<?echo (string)$_POST['sku'] ;?>" />

<br>
<?if ($new>=1){?>
<svg class="barcode"
	jsbarcode-value="<?echo (string)$_POST['sku'] ;?>"
	jsbarcode-textmargin="0"
	jsbarcode-height="24"
	jsbarcode-fontoptions="bold"
	jsbarcode-fontsize="12">
</svg>
<script>
JsBarcode(".barcode").init();
</script>

<h3><label class="description" for="categorie">Categorie : <span style="color: #ff0000;"><?echo $name;?></span></label></h3>
		<?if($new==1){?>Changer la description cliquez ici :<input type="checkbox" name="changedescription" value="yes" <?if($_POST['changedescription']=='yes')echo 'checked';?>/>
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
<table>
	  <tr>
		<td><h3>$ eBay Now : </h3>
		</td>
		<td><input id="priceebaynow" type="text" name="priceebaynow" value="<?echo $_POST['priceebaynow'];?>" size="10" /></td>

		<td><h3>$ eBay Sold : </h3>
		</td>
		<td><input id="priceebaysold" type="text" name="priceebaysold" value="<?echo $_POST['priceebaysold'];?>" size="10"  /></td>

		<td><h3>$ Tera Sold : </h3>
		</td>
		<td><input id="priceterasold" type="text" name="priceterasold" value="<?echo $_POST['priceterasold'];?>" size="10"  /></td>

	  </tr>
</table>

<table>
<tr>
		<td> <strong>Prix Detail: </strong>$<?echo $data8['pricedetailusd'];?><br>
		<strong>eBay: (%<?echo substr ($data9['ebayprice']/$data8['pricedetailusd']*100,0,5);?>) </strong>$<?echo $data9['ebayprice'];if($dejaprix==1){echo "<br><strong>*** PRIX DEJA RENTRER (".$data9['ebayprice']*$differentiel.")</strong>";}?> <br><strong>ETAT: </strong><?echo $data9['ebayprice']*$differentiel;?> <strong>Demande: </strong><?echo $data9['ebaydemand'];?>
<br>

		<strong>Amazon: (%<?echo substr ($data9['amazonprice']/$data8['pricedetailusd']*100,0,5);?>) </strong>$<?echo $data9['amazonprice'];?> <br><strong>ETAT:</strong> <?echo $data9['amazonprice']*$differentiel;?><strong> Demande:</strong> <?echo $data9['amazondemand'];?>
		</td>

</tr>
</table>
	<table width="100%">

	  <tr>
		<td><h3>Quantit&eacute; : </h3></td>
		<td><input id="quantity"  type="text" name="quantity" value="<?echo $_POST['quantity'];?>" size="10" /></td>
		<td><h3>Location : </h3></td>
		<td><input id="location"  type="text" name="location" value="<?echo $_POST['location'];?>" size="10" /></td>
		<td><h3><a href="createbarcode.php?product_id=<?echo $_POST['product_id'];?>" target="_blank" style="color:#ff0000"><strong>Creation LABEL</strong></a></h3></td>
	  </tr>

	</table>

<table>

	  	<tr>
		<td><h3>Prix  : </h3><input id="price" type="text" name="price" value="<?if ($_POST['price']==0){echo $data9['ebayprice']*$differentiel;}else{echo $_POST['price']; $dejaprix=1;}?>" size="10" /> <strong>Cost Price: </strong><?$cost=(($data8['pricecost']*$data8['pricedetailusd']/$data8['pricedetailcad'])+$shipping)/.85; echo $cost;?>
		</td>

	</tr>
<td>
<?echo $_POST['description'];?>
</td>
</table>

<h3><label class="description" for="element_1">Titre :</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<br>
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

// on envoie la requête
$req = mysqli_query($db,$sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysqli_fetch_assoc($req))
    {
		$test1=strtolower ($_POST['name_product']);
		$test2=strtolower ($data['name']);
?>
			<option value="<?echo $data['manufacturer_id'];?>" <?if ($_POST['manufacturer']==$data['manufacturer_id']) {echo 'selected';$manufsearch=$data['name'];}?>><?echo $data['name'];?></option>
<?}?>
		</select>
		</td>
		  </tr>
		 <tr>
		<td><h3>Model :</h3></td>
		<td colspan="2"><input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="80" /></td>
	  </tr>
	  	  <tr>
		<td><h3>Facture d'achat :</h3></td>
		<td colspan="2"><input id="invoice" type="text" name="invoice" value="<?echo $_POST['invoice'];?>" maxlength="80" /></td>
	  </tr>
		</table>
		
		
		<h3>Condition :</h3>
		 
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
				<label class="choice" for="etat_4">Used - Like New</label> </td>
			<td><input id="etat_6" class="element radio" type="radio" name="etat" value="7" <?if ($_POST['etat']==7){?>checked<?}?>/> 
				<label class="choice" for="etat_5">Used - Very Good</label><br> </td>
		  </tr>
		  <tr>
			<td><input id="etat_7" class="element radio" type="radio" name="etat" value="6" <?if ($_POST['etat']==6){?>checked<?}?>/> 
				<label class="choice" for="etat_6">Used - Good</label> </td>
			<td><input id="etat_8" class="element radio" type="radio" name="etat" value="5" <?if ($_POST['etat']==5){?>checked<?}?>/> 
				<label class="choice" for="etat_7">Used - Poor</label><br> </td>
		  </tr>
		  <tr>
			<td><input id="etat_9" class="element radio" type="radio" name="etat" value="1" <?if ($_POST['etat']==1){?>checked<?}?>/> 
				<label class="choice" for="etat_8">For Parts Or For Repair</label> </td>
			<td></td>
		  </tr>
		</table>
	
		<tr>
			<td>
		<h3>Conditions Detail:</h3>
		<input type="hidden" name="condition" value="<?echo $_POST['condition'];?>" />
<?
$sql = 'SELECT * FROM `oc_condition_variant` where type=2 and condition_id like "%,'.$_POST['etat'].',%"order by name';

// on envoie la requête
$req = mysqli_query($db,$sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysqli_fetch_assoc($req))
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

// on envoie la requête
$req = mysqli_query($db,$sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysqli_fetch_assoc($req))
    {

		//echo $_POST['condition'].'*****'.$data['condition_variant_id'];
		//print_r($accarray[nameacc]);
		echo $accarray['nameacc'];
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

// on envoie la requête
$req = mysqli_query($db,$sql);
// on fait une boucle qui va faire un tour pour chaque enregistrement
while($data = mysqli_fetch_assoc($req))
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

	<tr>
	<td width="66%" valign="top">

		<h3><label class="description" for="description">REMARQUE INTERNE : </label>
		<input id="remarque"  type="text" name="remarque" value="<?echo $_POST['remarque'];?>" maxlength="255" />
	<table width="100%">
	  <tr>
		<td><h3>Dimension :</h3></td>
		<td><input id="length" class="" type="text" name="length" value="<?echo $_POST['length'];?>" maxlength="10" /></td>
		<td><b>L</b></td>
		<td><input id="width" class="" type="text" name="width" value="<?echo $_POST['width'];?>" maxlength="10" /></td>
		<td><b>P</b></td>		
		<td><input id="height" class="" type="text" name="height" value="<?echo $_POST['height'];?>" maxlength="10" /></td>
		<td><b>H</b></td>	
		<td> </td>
		<td> </td>		
	  </tr>
	  <tr>
		<td><h3>Poids : </h3></td>		
		<td><input id="weight" class="" type="text" name="weight" value="<?echo $_POST['weight'];?>" maxlength="10" /> </td>
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
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="andescription" value="<?echo $_POST['andescription'];?>" />
		<input type="hidden" name="skuanc" value="<?echo (string)$_POST['sku'] ;?>" />

		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" >Retour au MENU</a></h1>
	</td>
</table>
</form>

  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ((string)$_POST['sku'] ,0,12);?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ((string)$_POST['sku'] ,0,12);?>&rt=nc&LH_PrefLoc=1","ebaynew");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo $manufsearch." ".$_POST['model'];?>&rt=nc&LH_PrefLoc=1","ebaynew2");
</script>
</body>
</html>
<? // on ferme la connexion à mysql 
mysqli_close($db); ?>