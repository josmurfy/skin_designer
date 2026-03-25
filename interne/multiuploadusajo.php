<?
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db);

			
			//echo $data['sku'];
			$sql= 'SELECT imageurl,upc FROM `oc_product_index` where imageurl !="" group by upc';
			
	//echo $sql;
			$req = mysqli_query($db,$sql);
			while($data = mysqli_fetch_assoc($req)){
					$sql9 = 'SELECT product_id,image FROM `oc_product` where upc='.(string)$data['upc'].' and image = ""';
					
					
					//echo $sql9;
					$req9 = mysqli_query($db,$sql9);
					$data9 = mysqli_fetch_assoc($req9);
					echo "<br>".$data9['product_id'];
					
					 $picexterne=$data['imageurl'];
					 $picexterne2=$data['imageurl']; 
				// echo $picexterne2;
					 $picexterne2=str_replace(array("\r","\n", " "),"",$picexterne2);
					 $path=explode(".",basename($picexterne2));
					
				//	echo count($path);
					$ext=count($path)-1;

				 if($ext>=1 ){
			//echo "allo";
					// echo $picexterne2;
					 echo $picexterne2;
					$image = file_get_contents($picexterne2);
					$imagepath='/home/n7f9655/public_html/image/catalog/product/'.$data9['product_id'].".".$path[$ext];
					echo "<br>".$imagepath;

					file_put_contents($imagepath, $image); //Where to save the image on your server
					$sql2="UPDATE `oc_product` SET image ='catalog/product/".$data9['product_id'].".".$path[$ext]."' where product_id=".$data9['product_id'];
					$req2=mysqli_query($sql2);
					//echo $sql2;
					echo '<br><a href="https://www.phoenixliquidation.ca/image/catalog/product/'.$data9['product_id'].".".$path[$ext].'" target="_blank"><img src="'.$picexterne.'" alt="" width="100"/></a><br>';
				 }
			}

// on ferme la connexion � mysql 
mysqli_close($db); ?>