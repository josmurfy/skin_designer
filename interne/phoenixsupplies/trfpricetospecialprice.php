<?
//print_r($_POST['accesoires']);
$sku=$_POST['sku'];
// on se connecte à MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
 

/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5,price=price_with_shipping where quantity=0 and price=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */

/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5 where quantity=0 and price>0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$sql2 = 'UPDATE `oc_product` SET `status` = 0,stock_status_id=5 where price=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */


		$sql = 'SELECT * FROM `oc_product` where usa=1';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
		$calcitem=0;
		while($data = mysqli_fetch_assoc($req)){ 
			$calcitem++;
			$sql2 = 'INSERT INTO `oc_product_special` (`product_id`,customer_group_id,priority,price) VALUES ("'.$data['product_id'].'",1,1,'.$data['price'].')';
			//echo $sql2;
			$req2 = mysqli_query($db,$sql2);
		}	

		echo $calcitem;  

?>