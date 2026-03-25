<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
//echo $_POST['length'].'allo1';
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
 


if ($_POST['name_product']!="" && $_POST['model']!="" && $_POST['manufacturer']!="" && $_POST['new']==1){
		$sql2 = 'UPDATE `oc_product` SET stock_status_id=7,model="'.addslashes(strtoupper($_POST['model'])).'",status="'.$_POST['status'].'",manufacturer_id="'.$_POST['manufacturer'].'",price="'.$_POST['price'].'",ebay_last_check="2020-09-01",`remarque_interne`="'.$_POST['remarque'];
		$sql2 .='" WHERE `oc_product`.`product_id` ='.$_POST['product_id'];


		//echo $sql2.'<br><br>';
		//UPDATE `oc_product` SET `REMARQUE_CORRECTION` = '1' WHERE `oc_product`.`product_id` = 309;
		$req2 = mysqli_query($db,$sql2);
		
/* 			$sql2 = 'SELECT * FROM `oc_product`,oc_product_description WHERE oc_product.product_id=oc_product_description.product_id and oc_product.product_id ='.$_POST['product_id'];
//echo $sql2;
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);


			$sql3 = 'SELECT * FROM `oc_manufacturer` where manufacturer_id='.$data2['manufacturer_id'];

			// on envoie la requête
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			//echo $_POST['test'];
			// LA description Formater
			//echo $_POST['length'].`allo`;
			//echo $_POST['andescription'];
			$description.='<h2>Description :</h2>';
			if($_POST['andescription']!='')$description.=$_POST['andescription'].'<br><br>';
			$description.='<strong>Title : </strong>'.strtoupper($data2['name']).'<br><strong>Model : </strong>'.strtoupper($data2['model']);
			$description.='<br><strong>Brand : </strong>'.strtoupper($data3['name']).'<br>';
			if($data2['color']=="")$data2['color']="N/A";
			$description.='<strong>Color : </strong>'.strtoupper($data2['color']).'<br>';
			$description.='<strong>Dimension : </strong>'.number_format($data2['length'], 1, '.', '').'x'.number_format($data2['width'], 1, '.', '').'x'.number_format($data2['height'], 1, '.', '').' Inch<br>';
			$description.='<strong>Weight : </strong>'.number_format($data2['weight'], 1, '.', '').' Lbs<br>';
			$sql2 = 'SELECT * FROM `oc_condition` where condition_id='.$data2['condition_id'];
//echo $sql2;
			// on envoie la requête
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
			$description.='<h2>Conditions :</h2><p>- <strong>'.strtoupper($data2['name']).'</strong><br>';
			$description.=strtoupper($data2['condition'])."<br>";
$conditionsupp=explode(',', $_POST['condition']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}
}			
			
			if($_POST['accessory']!="" && $_POST['accessory']!=",")$description.='</p><h2>Accessories Included :</h2><p>';
$conditionsupp=explode(',', $_POST['accessory']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}
}		
//echo $_POST['test'];		
		if($_POST['test']!="" && $_POST['test']!=",")$description.='</p><h2>Tests - Repairs Done :</h2><p>';
$conditionsupp=explode(',', $_POST['test']);
foreach($conditionsupp as $conditioncheck){
	if(is_numeric($conditioncheck)==false && $conditioncheck!=""){
		$description.='- '.$conditioncheck.'<br>';
		//echo $i;		
	}
}	
	
			$sql = 'SELECT * FROM `oc_product`where product_id ="'.$_POST['product_id'].'"';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);	


			$description.='<h2>Photos :</h2><table bgcolor=\"FFFFFF\"style=\"width: 500px;\" border=\"1\" cellspacing=\"1\" cellpadding=\"5\" align=\"center\"><tbody>';
			
			$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data['image'].'\" width=\"500\"</td></tr>';
							
		
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
			$req2= mysqli_query($db,$sql2); 
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
					$description.='<tr><td style=\"text-align: center;\" align=\"center\" valign=\"middle\"><img src=\"https://www.phoenixsupplies.ca/image/'.$data2['image'].'\" width=\"500\"</td></tr>';
				$i++;
				}
			}
			$description.='</tbody></table><br>';
			//echo $description;
			$_POST['name_product']=htmlentities($_POST['name_product'], ENT_QUOTES);
			$description=htmlentities($description, ENT_QUOTES);

	//echo $_POST['changedescription'];
	*/
				$sql2 = 'UPDATE `oc_product_description` SET name="'.addslashes(strtoupper($_POST['name_product'])).'" WHERE `product_id` ='.$_POST['product_id'];
//echo $sql2;
	
			$req2 = mysqli_query($db,$sql2); 
			if($_POST['status']==1 && $_POST['price']!=0){
				$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183628'";
				$req = mysqli_query($db,$sql);	
				//echo $sql;				
			}
		$new=1;
		$_POST['new']=0;
		if($_POST['Correction']=="yes" && $_POST['remarque']!=""){
				$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183628'";
				$req = mysqli_query($db,$sql);	
				$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$_POST['product_id']."', '183631')";
				$req = mysqli_query($db,$sql);			
		}
}

			
			//echo $_POST['sku'];
			$sql = 'SELECT ds.name,pr.sku,pr.condition_id,pr.model,pr.manufacturer_id,pr.product_id,pr.upc,
					pr.priceebaysold,pr.priceterasold,pr.priceebaynow,pr.quantity,pr.price,pr.weight,pr.length,pr.width,pr.height,
					pr.location,ds.accessory,ds.condition,ds.test,pr.status,ds.description,pr.invoice
					FROM `oc_product` as pr,`oc_product_description` as ds,oc_product_to_category as ca where pr.product_id=ds.product_id and 
			pr.product_id=ca.product_id and pr.quantity>0 and pr.usa=0  and  pr.status=0 and not(pr.location="") and pr.remarque_interne="" order by pr.product_id limit 1 '; //and ca.category_id="183628" limit 1 ';
			//$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where NOT EXISTS
			//(SELECT * FROM oc_ebay_listing WHERE oc_ebay_listing.product_id=oc_product.product_id and oc_ebay_listing.status=1 ) and oc_product.product_id=oc_product_description.product_id 
			//and status = "1" and quantity>0 and description_mod=0 limit 1 ';
			//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

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
			$_POST['remarque']=$data['remarque_interne'];
			$sql = 'SELECT * FROM `oc_product_to_category`,`oc_category_description` where oc_product_to_category.category_id=oc_category_description.category_id and product_id="'.$_POST['product_id'].'" and ebayyes=1';
			//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
			$name=$data['name'];
			$_POST['category_id']=$data['category_id'];
			
			$new=1;
$differentiel=0;
/* if($_POST['etat']=="9")$differentiel=1;
if($_POST['etat']=="99")$differentiel=.85;
if($_POST['etat']=="2")$differentiel=.70;
if($_POST['etat']=="22")$differentiel=.70;
if($_POST['etat']=="8")$differentiel=.6;

	 
	  		$sql9 = 'SELECT * FROM `oc_product_index` where upc="'.substr ($_POST['upc'],0,12).'" group by upc';
			//echo $sql9;
			$req9 = mysqli_query($db,$sql9);
			$data9 = mysqli_fetch_assoc($req9);
			$sql8 = 'SELECT * FROM `oc_product_reception` where upc="'.substr ($_POST['upc'],0,12).'" group by upc';
			//echo $sql8;
			$req8 = mysqli_query($db,$sql8);
			$data8 = mysqli_fetch_assoc($req8);
	  
	 $_POST['andescription']=$data9['description'];
	 if($data9['shipping']==0)
	 {
		 $shipping=3.18;
	 }else{
		 $shipping =$data9['shipping'];
	 } */
	 

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="modificationitemadmin.php" method="post">
<div class="form_description">
<h1>Modification Item ADMIN COMMERCIAL</h1>

</div>
<h3>SKU <?if($new==1){?><a href="modificationitemadmin.php" class="button--style-red">Changer d'item</a><?}?> <a href="interne.php" class="button--style-red">Retour au MENU</a></h3>
<input id="sku"  type="text" name="sku" value="<?echo $_POST['sku'];?>" maxlength="255" <?if($new==1)echo "disabled";?>/>
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

<h3><label class="description" for="categorie">Categorie : <span style="color: #ff0000;"><?echo $name;?></span></b></label></h3>
		<?if($new==1){?>Correction a faire :<input type="checkbox" name="Correction" value="yes"/>
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />

	<table width="100%">
	  <tr>
		<td><h3>Location: <?echo $_POST['location'];?>
		 </h3></td>
		</tr> 
	  <tr>
		<td><h3>Quantit&eacute; : </h3></td>
		<td><input id="quantity"  type="text" name="quantity" value="<?echo $_POST['quantity'];?>" size="10" /></td>

	  </tr>

	</table>


<table>

	  	<tr>
		<td><h3>Prix  : </h3><input id="price" type="text" name="price" value="<?if ($_POST['price']==0){echo $data9['ebayprice']*$differentiel;}else{echo $_POST['price']; $dejaprix=1;}?>" size="10" /> <strong>Cost Price: </strong><?$cost=(($data8['pricecost']*$data8['pricedetailusd']/$data8['pricedetailcad'])+$shipping)/.85; echo $cost;?>
		</td>

	</tr>

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
			<option value="<?echo $data['manufacturer_id'];?>" <?if ($_POST['manufacturer']==$data['manufacturer_id']) {echo 'selected';$manufac_name=$data['name'];}?>><?echo $data['name'];?></option>
<?}?>
		</select>
		</td>
		  </tr>
		 <tr>
		<td><h3>Model :</h3></td>
		<td colspan="2"><input id="model" type="text" name="model" value="<?echo $_POST['model'];?>" maxlength="80" /></td>
	  </tr>
		</table>
		
		


	<tr>
	<td width="66%" valign="top">


		<h3><label class="description" for="description">REMARQUE INTERNE : </label>
		<input id="remarque"  type="text" name="remarque" value="<?echo $_POST['remarque'];?>" maxlength="255" />

		

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		
		<?}?>
		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="andescription" value="<?echo $_POST['andescription'];?>" />
		<input type="hidden" name="skuanc" value="<?echo $_POST['sku'];?>" />

		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>
	</td>

</table>
<table bgcolor="ffffff" width="100%" align="center">
<?echo html_entity_decode($_POST['description']);?>
</table>
<input type="hidden" name="etat" value="<?echo $_POST['etat'];?>" />
</form>
  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo $_POST['model'].' '.$manufac_name;?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo $_POST['model'].' '.$manufac_name;?>&rt=nc&LH_PrefLoc=1","ebaynew");
window.open("https://sell.terapeak.com/product-research?buyerCountryCodes&categoryId&endDate=1551761999999&fromPrice&isAnyCriteria=false&keywords=<?echo $manufac_name.'+'.$_POST['model'];?>&listingConditions=&listingTypes=&productId=&sellerCountryCodes&sellerName=&site=ALL&startDate=1520139600000&toPrice&transactionSite","ebaysold2");
</script>
</body>
</html>
<? // on ferme la connexion à mysql 
mysqli_close($db); ?>