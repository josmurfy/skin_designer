<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php'; 
// on s&eacute;lectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db); 

/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5,price=price_with_shipping where quantity=0 and price=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */

/* 		$sql2 = 'UPDATE `oc_product` SET `status` = 1,stock_status_id=5 where quantity=0 and price>0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2);
		$sql2 = 'UPDATE `oc_product` SET `status` = 0,stock_status_id=5 where price=0';
		//echo $sql2.'<br><br>';
		$req2 = mysqli_query($db,$sql2); */
// desactive au cas 

/* 		$sql = 'SELECT * FROM `oc_product` where usa=1'; 
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
 */
?>