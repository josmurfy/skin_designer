<?
//print_r($_POST['accesoires']);
$sku=$_GET['sku'];
// on se connecte � MySQL 
//$db = mysqli_connect('localhost', 'phoenkv5_store', 'Vivi1FX2Pixel$$');  
// on s&eacute;lectionne la base 
//mysqli_select_db('phoenkv5_store',$db); 
//$db = mysqli_connect("127.0.0.1","phoenkv5_store","Vivi1FX2Pixel$$","phoenkv5_store");
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on cr&eacute;e la requ�te SQL verifier les ordres 
// savoir ledernier id 
//echo $_GET['sku'];
//echo 'check '.$_POST['skucheck'];

if($_GET['product_id']!="")header("location: miseajouritem.php?product_id=".$_GET['product_id']);

//echo $_GET['product_id'];

if ($_POST['status']==1 && $_POST['price']>0){
	
				$Date = date('Y-m-d H:i:s');
				$sql2 = 'UPDATE `oc_product` SET tax_class_id=9,ebay_last_check="2020-09-01",date_price_upd="'.$Date.'", stock_status_id=7,status=1,`price` = '.$_POST['price'].' WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

				//echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2); 
		
				//$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183628'";
				//$req = mysqli_query($db,$sql);	
				//echo "allo".$_GET['action'];
	
				//header("location: https://phoenixliquidation.ca/admin/interne/listingusaADMIN.php?sku=".$_GET['sku']);  
				$_POST['status']="";
}


if (empty($_POST['status'])){
			$sql3 = 'SELECT * FROM `oc_product`,oc_product_description where oc_product.product_id=oc_product_description.product_id and quantity>0 and status=1 and date_price_upd is null order by price desc ';
	//echo $sql;
			$req3 = mysqli_query($db,$sql3);
			echo "A faire: ".mysqli_num_rows($req3)."<br>";
			$sql3 = 'SELECT * FROM `oc_product`,oc_product_description where oc_product.product_id=oc_product_description.product_id and quantity>0 and status=1 and date_price_upd is null order by price desc limit 1';
	//echo $sql;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);
			$_POST['price']=$data3['price'];
			$_POST['name']=$data3['name'];
			$_POST['model']=$data3['model'];
			$_POST['product_id']=$data3['product_id'];
			$_POST['sku']=$data3['sku'];
			$_POST['description']=$data3['description'];
			$sql4 = 'SELECT * FROM oc_manufacturer where manufacturer_id='.$data3['manufacturer_id'];
	//echo $sql4;
			$req4 = mysqli_query($db,$sql4);
			$data4 = mysqli_fetch_assoc($req4);
			$_POST['manufacturer']=$data4['name'];

}

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffbaba">
<h1>Pret a lister l'item COMMERCIAL </h1>
<h3>
<?echo '<h1><font color="red">'.$erreurvide.'</font></h1>';?>
<form id="form_67341" class="appnitro" action="updateprice.php?action=<?echo $_GET['action'] ?>" method="post">
		Titre sur EBAY: <?echo $_POST['name'];?><br>
		Brand: <?echo $_POST['manufacturer'];?><br>
		Product ID: <?echo $_POST['product_id'];?><br>
		Sku ID: <?echo $_POST['sku'];?><br>

			
		<td>Price EBAY (finir par .95): <input id="price_with_shipping"  type="text" name="price" value="<?echo $_POST['price'];?>" size="10" /></td>

		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="condition_id" value="<?echo $_POST['condition_id'];?>" />
		<input type="hidden" name="sku" value="<?echo $_POST['sku'];?>" />
		Pret pour lister :<input type="checkbox" name="status" value="1" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		
</form>
<table bgcolor="ffffff" align="center">
<td>
<a href="multiuploadmodif.php?product_id=<?echo $_POST['product_id'];?>" class="button--style-red">Changer PHOTO</a><br>
<?echo html_entity_decode($_POST['description']);?> 
</td>
</table>
  <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo $_POST['manufacturer']." ".$_POST['model'];?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo $_POST['manufacturer']." ".$_POST['model'];?>&rt=nc&LH_PrefLoc=1","ebaynew");
window.open("https://sell.terapeak.com/product-research?buyerCountryCodes&categoryId&endDate=1581667199999&fromPrice&isAnyCriteria=false&keywords=<?echo $_POST['manufacturer']." ".$_POST['model'];?>&listingConditions=&listingTypes=&productId=&sellerCountryCodes&sellerName=&site=ALL&startDate=1550044800000&toPrice&transactionSite","google");
</script>
 <h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>

</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>