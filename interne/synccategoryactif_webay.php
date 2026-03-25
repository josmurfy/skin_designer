<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 

/* echo $_POST['google_base_category_id']."-";
echo $_POST['category_id']; */


		$sql = 'SELECT `ebay_category_id`,`ebay_category_name` FROM `oc_category`,`oc_wk_ebay_categories` where `oc_wk_ebay_categories`.`ebay_category_id`=`oc_category`.category_id order by oc_category.category_id';
		echo $sql.'<br><br>'; 
		$req = mysqli_query($db,$sql); 

		while($data = mysqli_fetch_assoc($req)){
		//echo $sql2.'<br><br>';	  
			$sql2 = "INSERT INTO `oc_wk_ebaysync_categories` ( `opencart_category_id`, `ebay_category_id`, `ebay_category_name`, `pro_condition_attr`, `variations_enabled`, `added_date`, `account_id`)
					 VALUES ('".$data['ebay_category_id']."', '".$data['ebay_category_id']."', '".$data['ebay_category_name']."', '0', '1', '2020-04-22 12:46:05', '1')";
			
			echo $sql2."<br><br>";
			$req2 = mysqli_query($db,$sql2);
		
		}

		

?>


<? // on ferme la connexion à mysql 
mysqli_close($db);
?>