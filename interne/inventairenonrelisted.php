<? 
//echo (string)$_POST['sku'] ;
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';
$sql = "SELECT * FROM `oc_product` WHERE `quantity` >= 1 AND `ebay_id` = '' AND `ebay_id_old` != '' limit 1";//AND (error_ebay='' OR error_ebay is null)
//echo $sql.'<br><br>';
		// on envoie la requête
		$req = mysqli_query($db,$sql);
	
		while($data = mysqli_fetch_assoc($req)){
	
					relist_to_ebay($connectionapi,$data['product_id'],$data['ebay_id_old'],$db);
				echo $data['product_id']."<br>";
		}
	
				


mysqli_close($db);	?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head> </head>
<body bgcolor="ffffff">

</body>
</html>
<?php //header('Content-Type: text/html; charset=iso-8859-1');
 ?>
