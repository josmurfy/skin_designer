<?
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 
// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 

//echo "allo";
		$sql3 = 'SELECT * FROM `oc_product_reception` group by invoice';
//echo $sql;
		// on envoie la requ�te
		$req3 = mysqli_query($db,$sql3);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$quantity=0;
		$upcancien=0;
		$ok=0;
		while ($data3 = mysqli_fetch_assoc($req3))
				{
				$sql = 'SELECT * FROM `oc_product_reception` where invoice='.$data3['invoice'].' order by upc,product_id desc';
		//echo $sql.'<br><br>';
				// on envoie la requ�te
				$req = mysqli_query($db,$sql);
				// on fait une boucle qui va faire un tour pour chaque enregistrement
				$quantity=0;
				$upcancien=0;
				$ok=0;
				while ($data = mysqli_fetch_assoc($req))
				{
					$ok=1;
					

						$quantity=$quantity+$data['quantityrecu'];
						$product_id=$data['product_id'];
						$upcancien=$data['upc'];
						//echo "allo";

				}
						$sql2 = 'UPDATE `oc_product_reception` SET quantityrecu="'.$quantity.'" where product_id='.$product_id.' and upc='.$upcancien.' and invoice = "'.$data3['invoice'].'"';
						//echo $sql2.'<br><br>';
						//$req2 = mysqli_query($db,$sql2);
						
						$product_id=$data['product_id'];

						$sql2 = 'delete from `oc_product_reception` where upc='.$upcancien.' and quantityrecu!='.$quantity.' and invoice = "'.$data3['invoice'].'"';
						//echo $sql2.'<br><br>';
						//$req2 = mysqli_query($db,$sql2);
						$quantity=0;
						$upcancien=$data[upc];
				$upcancien=0;
			}
	$new=0;
		$sql3 = 'SELECT * FROM `oc_product_reception` where transfert=1 group by upc';
//echo $sql;
		// on envoie la requ�te
		$req3 = mysqli_query($db,$sql3);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		$quantity=0;
		$upcancien=0;
		$ok=0;
		while ($data3 = mysqli_fetch_assoc($req3))
		{
			$sql = 'SELECT * FROM `oc_product` where upc='.$data3['upc'].' group by upc';
//echo $sql;
		// on envoie la requ�te
			$req = mysqli_query($db,$sql);
			if (mysqli_affected_rows($db)==0){
				echo "non";
						$sql2 = 'UPDATE `oc_product_reception` SET transfert=0 where upc='.$data3['upc'];
						echo $sql2.'<br><br>';
						$req2 = mysqli_query($db,$sql2);
			}
		}









echo $ValeurTotal;

?>


<? echo 'FINI'; // on ferme la connexion � 
mysqli_close($db); ?>