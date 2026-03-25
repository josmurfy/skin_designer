<?
include 'connection.php';

 			$sql3 = 'SELECT * FROM `oc_category_description` where ebayyes=1';
			//echo $sql3."<br>";
			//echo $cat[5]."<br><br>";
			$req3 = mysqli_query($db,$sql3);	
			while($data3 = mysqli_fetch_assoc($req3)){
				
					$sql2 = "INSERT INTO `oc_uksb_google_merchant_categories` (`category_id`, `google_category_gb`, `google_category_us`, `google_category_au`, `google_category_fr`, `google_category_de`, `google_category_it`, `google_category_nl`, `google_category_es`, `google_category_pt`, `google_category_cz`, `google_category_jp`, `google_category_dk`, `google_category_no`, `google_category_pl`, `google_category_ru`, `google_category_sv`, `google_category_tr`) 
					VALUES ('".$data3['category_id']."', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')";
					echo $sql2."<br>";;
					$req2 = mysqli_query($db,$sql2);
			}
			
			$sql3 = 'SELECT * FROM `oc_google_base_category_to_category`';
			//echo $sql3."<br>";
			//echo $cat[5]."<br><br>";
			$req3 = mysqli_query($db,$sql3);	
			while($data3 = mysqli_fetch_assoc($req3)){
				
					$sql2 = "UPDATE `oc_uksb_google_merchant_categories` SET `google_category_us` = '".$data3['google_base_category_id']."' WHERE `oc_uksb_google_merchant_categories`.`category_id` = '".$data3['category_id']."'";
					echo $sql2."<br>";;
					$req2 = mysqli_query($db,$sql2);
			}

mysqli_close($db);
?>