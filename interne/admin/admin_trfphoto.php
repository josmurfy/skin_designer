<?
//print_r($data['accesoires']);

// on se connecte à MySQL 
include 'connection.php';
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");


			$sql = 'SELECT * FROM `oc_product` ';//where product_id<20450
			//$sql = 'SELECT upc FROM `oc_product` as pr,`oc_product_description` as ds,oc_product_to_category as ca where pr.product_id=ds.product_id and pr.product_id=ca.product_id and pr.quantity>0   and pr.status=0 and pr.remarque_interne="" group by pr.upc,pr.product_id order by pr.price desc '; //and (pr.location like "%magasin%") and (pr.location like "%magasin%")
			$req = mysqli_query($db,$sql);
		//	echo $sql;
		while($data = mysqli_fetch_assoc($req)){
		if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/".$data['product_id']."/")) {
					move_photo($connectionapi,$data['product_id'],$data['marketplace_item_id'],$db);
					echo $data['product_id']."<br>";
				}
		
/* 		if (file_exists("/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/".$data['product_id']."/")) {
					unlink('/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/'.$data['product_id'].'*.*');
					
					//unlink('/home/n7f9655/public_html/canuship/interne/manifest/manifest'.date("Y-m-d").'___.csv');
					echo $data['product_id'].'*.jpg'."<br>";
				} */
		}

 

$req = mysqli_query($db,$sql);mysqli_close($db);

?>