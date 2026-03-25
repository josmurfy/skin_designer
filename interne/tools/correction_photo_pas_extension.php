<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte à MySQL  
include '../connection.php';include '../functionload.php';


 			$sql = 'SELECT * FROM `oc_product` where product_id=20250';
			//echo $sql."<br>";
			$req = mysqli_query($db,$sql);
			$calcitem=0;

		while($data = mysqli_fetch_assoc($req)){ 
			$test=explode(".",$data['image']);
			if ($test[1]==""){
				
				//echo changeext("/home/phoenkv5/public_html/phoenixliquidation/image/catalog/product/","", ".jpg", $verbose = false);
				$file="/home/phoenkv5/public_html/phoenixliquidation/image/".$data['image'];
				$newfile="/home/phoenkv5/public_html/phoenixliquidation/image/".$data['image'].".jpg";
				
				if (!copy ($file,$newfile )) {
					echo "La copie ".$file." du fichier a échoué...\n";
				}
				$sql2 = 'UPDATE `oc_product` SET `image` = "'.$data['image'].'.jpg" where product_id='.$data['product_id'];
				echo $sql2.'<br><br>';
				echo "/home/phoenkv5/public_html/phoenixliquidation/image/catalog/product/".$data['image'];
				$req2 = mysqli_query($db,$sql2);
			}
		}
//echo changeext("/home/phoenkv5/public_html/phoenixliquidation/image/catalog/product/","", ".jpgtest", $verbose = false);

mysqli_close($db); 

?>