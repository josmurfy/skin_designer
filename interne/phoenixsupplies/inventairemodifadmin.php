<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
//echo $_POST['length'].'allo1';
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
 
// on cr&eacute;e la requ�te SQL verifier les ordres 
// savoir ledernier id 
//echo $_POST['sku'];
//echo 'check '.$_POST['skucheck'];
//echo $new;

//echo $_POST['newsku'];
//echo $_POST['name_product']."<br>".$_POST['price']."<br>".$_POST['model']."<br>".$_POST['manufacturer'];

//Estimation du prix 
//echo $_POST['andescription'];

if ($_POST['name_product']!="" && $_POST['model']!="" && $_POST['price']>0){
	$pricecad= $_POST['price']*1.3;
		$sql2 = 'UPDATE `oc_product_reception` SET close=0, title="'.$_POST['name_product'].'",model="'.$_POST['model'].'",`pricedetailusd`="'.$_POST['price'].'",`pricedetailcad`="'.$pricecad.'"';
		$sql2 .=' WHERE upc ='.$_POST['upc'];


		echo 'Modifier<br><br>';

		$req2 = mysqli_query($db,$sql2);
}

			
			//echo $_POST['sku'];
			$sql = 'SELECT * FROM `oc_product_reception` where upc!="" and (title="" or pricedetailusd=0) group by upc order by upc limit 1 '; 
			//echo $sql;
			
			//$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where NOT EXISTS
			//(SELECT * FROM oc_ebay_listing WHERE oc_ebay_listing.product_id=oc_product.product_id and oc_ebay_listing.status=1 ) and oc_product.product_id=oc_product_description.product_id 
			//and status = "1" and quantity>0 and description_mod=0 limit 1 ';
			//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['title'];


			$_POST['model']=$data['model'];

			$_POST['new']=1;
			$_POST['product_id']=$data['product_id'];
			//echo $_POST['product_id'];
			
			
			$_POST['upc']=$data['upc'];
			$_POST['sku']=$data['upc'];

			$_POST['price']=$data['pricedetailusd'];

			$_POST['invoice']=$data['invoice'];
			$_POST['pricedetailusd']=$data['pricedetailusd'];
			
			$new=1;
$differentiel=0;
  

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js"></script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="a8c6fe">
<form id="form_67341" class="appnitro" action="inventairemodifadmin.php" method="post">
<div class="form_description">
<h1>Modification Item ADMIN</h1>

</div>
<h3>UPC <?if($new==1){?><a href="inventairemodifadmin.php" class="button--style-red">Changer d'item</a><?}?> <a href="interne.php" class="button--style-red">Retour au MENU</a></h3>
<input id="upc"  type="text" name="upc" value="<?echo $_POST['upc'];?>" maxlength="255" />
<input type="hidden" name="skucheck" value="<?echo $_POST['upc'];?>" />

<table>

	  	<tr>
		<td><h3>Prix  : </h3><input id="price" type="text" name="price" value="<?if ($_POST['price']==0){echo $data9['ebayprice']*$differentiel;}else{echo $_POST['price']; $dejaprix=1;}?>" size="10" /> <strong>Cost Price: </strong><?$cost=(($data8['pricecost']*$data8['pricedetailusd']/$data8['pricedetailcad'])+$shipping)/.85; echo $cost;?>
		</td>

	</tr>
</table>

<h3><label class="description" for="element_1">Titre :</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<br>

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
	
		

		<p class="buttons">
		<input type="hidden" name="form_id" value="67341" />
		

		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="andescription" value="<?echo $_POST['andescription'];?>" />
		<input type="hidden" name="skuanc" value="<?echo $_POST['sku'];?>" />

		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		<h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>
	</td>
</table>
</form>
<script>
window.open("https://algopix.com/search?query=<?echo substr ($_POST['sku'],0,12);?>&markets=EBAY_US&from=US&shipping_method=carrier&costCurrencyCode=USD&itemConditions=New&costNew=1","algopix");
window.open("https://www.google.ca/search?q=<?echo substr ($_POST['sku'],0,12);?>&oq=<?echo substr ($_POST['sku'],0,12);?>","google");
</script>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>