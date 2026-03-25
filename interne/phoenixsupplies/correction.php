<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
 
// on cr&eacute;e la requ�te SQL verifier les ordres 
// savoir ledernier id 
//echo $_GET['sku'];
//echo 'check '.$_POST['skucheck'];
//echo $new;
/* 			$sql = 'SELECT * FROM `oc_product` where usa=1';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			

		while($data = mysqli_fetch_assoc($req))
		{
			$sql2 = 'UPDATE `oc_product` SET `upc`="'.$data['sku'].'" WHERE `oc_product`.`product_id` ='.$data['product_id'];
			
			echo $sql2.'<br><br>';
			//UPDATE `oc_product` SET `REMARQUE_CORRECTION` = '1' WHERE `oc_product`.`product_id` = 309;
			$req2 = mysqli_query($db,$sql2);

				//echo $_POST['sku']; 
		} */

mysqli_close($db); ?>