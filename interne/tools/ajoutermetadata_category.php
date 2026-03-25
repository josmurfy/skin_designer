<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include '../connection.php';include '../functionload.php';




		

			$sql3 = 'SELECT name,c.category_id FROM `oc_category` c left join `oc_category_description` cd on c.category_id=cd.category_id WHERE `status` = 1 and cd.language_id = 1 ';
			
			$req3 = mysqli_query($db,$sql3);
			echo $sql3.'<br><br>';
			while($data3 = mysqli_fetch_assoc($req3)){
				$sql2 ="UPDATE `oc_category_description` SET `description` = 'Find great deal on those ".$data3['name']." products on sale!', `meta_title` = 'Find great deal on those ".$data3['name']." products on sale!', `meta_description` = 'Find great deal on those ".$data3['name']." products on sale!' 
				WHERE category_id='".$data3['category_id']."' AND `oc_category_description`.`language_id` = 1";
				$req2 = mysqli_query($db,$sql2);
				echo $sql2.'<br><br>';
			}
			//echo $sql2."<br>";
			$sql3 = 'SELECT name,c.category_id FROM `oc_category` c left join `oc_category_description` cd on c.category_id=cd.category_id WHERE `status` = 1 and cd.language_id = 2 ';
			
			$req3 = mysqli_query($db,$sql3);
			echo $sql3.'<br><br>';
			while($data3 = mysqli_fetch_assoc($req3)){
				$sql2 ="UPDATE `oc_category_description` SET `description` = 'Trouvez de bonnes affaires sur ces produits ".$data3['name']." en solde!', `meta_title` = 'Trouvez de bonnes affaires sur ces produits ".$data3['name']." en solde!', `meta_description` = 'Trouvez de bonnes affaires sur ces produits ".$data3['name']." en solde!' 
				WHERE category_id='".$data3['category_id']."' AND `oc_category_description`.`language_id` = 2";
				$req2 = mysqli_query($db,$sql2);
				echo $sql2.'<br><br>';
			}
			
		
		//flush related
	

mysqli_close($db); 

?>