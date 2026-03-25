<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
 
// on cr&eacute;e la requête SQL verifier les ordres 
// savoir ledernier id 
//echo $_GET['sku'];
//echo 'check '.$_POST['skucheck'];
//echo $new;
			$sql3 = 'SELECT * FROM `oc_product` where product_id = "'.$_GET['product_id'].'"';
	//echo $sql;
			$req3 = mysqli_query($db,$sql3);
			$data3 = mysqli_fetch_assoc($req3);


if ($_POST['quantity']<>0){
	
	
				$sql = "INSERT INTO `oc_product_to_category` (`product_id`, `category_id`) VALUES ('".$_POST['product_id']."', '183628')";
				$req = mysqli_query($db,$sql);
				//echo $sql;
				$sql = "delete from `oc_product_to_category` where product_id='".$_POST['product_id']."' and category_id='183630'";
				$req = mysqli_query($db,$sql);	
				//echo $sql;
				$sql2 = 'UPDATE `oc_product` SET `quantity` = quantity+0'.$_POST['quantity'].' WHERE `oc_product`.`product_id` ='.$_POST['product_id'];

		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); 
				header("location: ".$_GET['action'].".php"); 
}?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="a8c6fe">
<h1>Etiquette prix</h1>

<form id="form_67341" class="appnitro" action="createbarcodemagasinun.php?action=<?echo $_GET['action'] ?>" method="post">
		
		<td>sku: <input id="sku"  type="text" name="sku" value="" size="20" autofocus /></td>
		<input type="hidden" name="product_id" value="<?echo $_GET['product_id'];?>" />
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
</form>

 <h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>

</body>
</html>
<? // on ferme la connexion à mysql 
mysqli_close($db); ?>