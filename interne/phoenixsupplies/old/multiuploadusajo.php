<?
$db = mysql_connect('localhost', 'phoenkv5', 'Caro1FX2Skimo$$'); 
// on s�lectionne la base 
mysql_select_db('phoenkv5_store',$db);

			
			//echo $data['sku'];
			$sql= 'SELECT imageurl,upc FROM `oc_product_index` where imageurl !="" group by upc';
			
	//echo $sql;
			$req = mysql_query($sql);
			while($data = mysql_fetch_assoc($req)){
					$sql9 = 'SELECT product_id,image FROM `oc_product` where upc='.$data['upc'].' and image = ""';
					
					
					//echo $sql9;
					$req9 = mysql_query($sql9);
					$data9 = mysql_fetch_assoc($req9);
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
					$imagepath='/home/phoenkv5/public_html/image/catalog/product/'.$data9['product_id'].".".$path[$ext];
					echo "<br>".$imagepath;

					file_put_contents($imagepath, $image); //Where to save the image on your server
					$sql2="UPDATE `oc_product` SET image ='catalog/product/".$data9['product_id'].".".$path[$ext]."' where product_id=".$data9['product_id'];
					$req2=mysql_query($sql2);
					//echo $sql2;
					echo '<br><a href="https://www.phoenixsupplies.ca/image/catalog/product/'.$data9['product_id'].".".$path[$ext].'" target="_blank"><img src="'.$picexterne.'" alt="" width="100"/></a><br>';
				 }
			}

// on ferme la connexion � mysql 
mysql_close(); ?>