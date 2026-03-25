<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 


		$sql = 'SELECT * FROM oc_google_base_category';
		//echo $sql."<br>";
		$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){ 
			$nbcategory= substr_count($data['name'],">");
			$sql2 = 'UPDATE `oc_google_base_category` SET `nbcategory` = '.$nbcategory.' where google_base_category_id='.$data['google_base_category_id'];
			echo $sql2.'<br><br>';
			$req2 = mysqli_query($db,$sql2);
			}

/**/$req = mysqli_query($db,$sql);mysqli_close($db); 

?>