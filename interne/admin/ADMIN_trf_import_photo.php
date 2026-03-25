<?
include 'connection.php';

	$sql = "SELECT * FROM `oc_product` WHERE usa=1 and quantity>0 and ebay_id>0 
	order by product_id desc limit 100";//  usa=1 and ebay_id>0 ...... and product_id=21963
	//$sql = 'SELECT *,P.product_id AS product_id, PD.name AS name_en, PD.color AS color_en, PD.description_supp AS description_supp_en,PD.condition_supp AS condition_supplementaire,P.price AS priceretail,PS.price AS price_magasin, P.image AS image_product,P.quantity, C.name AS condition_name,M.name AS brand FROM `oc_product` P LEFT JOIN `oc_product_description` PD ON (P.product_id=PD.product_id) LEFT JOIN `oc_product_special` PS ON (P.product_id=PS.product_id) LEFT JOIN `oc_manufacturer` M ON (M.manufacturer_id=P.manufacturer_id) LEFT JOIN `oc_condition` C ON (P.condition_id=C.condition_id AND C.language_id=1) where P.ebay_id_old=0 AND P.quantity>1 AND `ebay_id` NOT LIKE "" AND PD.language_id=1 ORDER BY `P`.`price_with_shipping` DESC limit 500';
	$req = mysqli_query($db,$sql);
	$i=0;
	$j=0;		
	$k=0;
	/* 		echo $sql;
			while($data_import = mysqli_fetch_assoc($req))
			{

				//delete_photo($data_import['product_id'],"",$db);
				if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/".$data_import['product_id']."")) {
						//echo	mkdir("/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/".$data_import['product_id']."", 0755, true);
						echo "Dossier PRIMAIRE NON EXISTANT<br>".$data_import['product_id']." IMAGE: ".$data_import['image'];
				}else{
					echo "<br><br>Dossier EXISTANT:<br>".$data_import['product_id']." IMAGE: ".$data_import['image'];
					if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/".$data_import['image']."")) {
						$j++;
						echo "<br>****image NON EXISTANTE:<br>****".$data_import['product_id']." IMAGE: ".$data_import['image'];
						$temp=explode("/",$data_import['image']);
						$temp2=explode("pri",$temp[3]);
						if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/".$temp2[0]."")) {
							$temp2=explode(".",$temp[3]);
							if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/".$temp2[0]."")) {
								echo "<br>********DOSSIER ORIGINE NON EXISTANT:<br>********".$temp2[0];
								$i++;
								//a faire manuel
							}else{
								echo "<br>------->DOSSIER ORIGINE EXISTANT:<br>------->".$temp2[0];
								if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/".$temp[0]."/".$temp[1]."/".$temp2[0]."/".$temp[3])) {
									echo "<br>************image origine NON EXISTANTE:<br>************".$temp2[0]." IMAGE: ".$temp[0]."/".$temp[1]."/".$temp2[0]."/".$temp[3];
									$sql2 = "SELECT * FROM `oc_product` WHERE product_id=".$temp2[0];//  usa=1 and ebay_id>0
									$req2 = mysqli_query($db,$sql2);
									$data_import2 = mysqli_fetch_assoc($req2);
									
										if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/".$data_import2['image'])) {
											echo "<br>************A REFAIRE MANUELLEMENT:".$data_import['product_id'];
											$i++;
										}else{
											echo "<br>************NOUVEAU NOM D'IMAGE:".$data_import2['image'];
											$k++;
									
											$piclink="https://phoenixliquidation/image/catalog/product/".$data_import2['image'];
											echo "<br>IMAGES PRINCIPAL".$piclink;
											// juste a copier a partir du dossier origine avec nouveau nom
											//upload_from_link_website($data_import['product_id'],$piclink,1,$db);
											$sql2 = "SELECT * FROM `oc_product_image` WHERE product_id=".$temp2[0];//  usa=1 and ebay_id>0
											echo $sql2;
											$req2 = mysqli_query($db,$sql2);
											while($data_import2 = mysqli_fetch_assoc($req2)){
												$piclink="https://phoenixliquidation/image/catalog/product/".$data_import2['image'];
											//	upload_from_link_website($data_import['product_id'],$piclink,0,$db);
												echo "<br>IMAGES SECONDAIRE".$piclink;
											}
										}
									
									
								}else{
									echo "<br>---------->image EXISTANTE:<br>---------->".$data_import['product_id']." IMAGE: ".$data_import['image'];
									// juste a copier a partir du dossier origine
								}
							}
							
														
						}else{
							echo "<br>------->DOSSIER ORIGINE EXISTANT:<br>------->".$temp2[0];
							if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/".$temp[0]."/".$temp[1]."/".$temp2[0]."/".$temp[3])) {
								echo "<br>************image origine NON EXISTANTE:<br>************".$temp2[0]." IMAGE: ".$temp[0]."/".$temp[1]."/".$temp2[0]."/".$temp[3];
								$sql2 = "SELECT * FROM `oc_product` WHERE product_id=".$temp2[0];//  usa=1 and ebay_id>0
								$req2 = mysqli_query($db,$sql2);
								$data_import2 = mysqli_fetch_assoc($req2);
								echo "<br>********NOUVEAU NOM D'IMAGE:".$data_import2['image'];
								$k++;
								// juste a copier a partir du dossier origine avec nouveau nom
								
							}else{
								echo "<br>---------->image EXISTANTE:<br>---------->".$data_import['product_id']." IMAGE: ".$data_import['image'];
								//juste a copier a partir du dossier origine
								
							}
						}
					}else{
						echo "<br>--->image EXISTANTE:<br>--->".$data_import['product_id']." IMAGE: ".$data_import['image'];
						//mettre a jour a partir de ebay
						if($data_import['marketplace_item_id']>0){
							delete_photo($data_import['product_id'],"principal",$db);
							delete_photo($data_import['product_id'],"",$db);
							link_to_download($connectionapi,$data_import['product_id'],$data_import['marketplace_item_id'],"",$db);
							mise_en_page_description($connectionapi,$data_import['product_id'],$db);
							revise_ebay_product($connectionapi,$data_import['marketplace_item_id'],$data_import['product_id'],"",$db,"oui");
							echo "<br>****REVISER***"; 
							$sql3 = 'UPDATE `oc_product`SET usa="8" WHERE `oc_product`.`product_id` ='.$data_import['product_id'];
							$req3 = mysqli_query($db,$sql3);
						}else{
							$sql3 = 'UPDATE `oc_product`SET usa="2" WHERE `oc_product`.`product_id` ='.$data_import['product_id'];
							$req3 = mysqli_query($db,$sql3);
							echo "<br>****NO REVISER pas sur ebay***"; 
						}
					}
					
				} */
				//$json=import_ebay($connectionapi,$data_import['marketplace_item_id'],$data_import['product_id'],$db);
				//mise_en_page_description($connectionapi,$data_import['product_id'],$db);
				//$json=revise_ebay_product($connectionapi,$data_import['marketplace_item_id'],$data_import['product_id'],$data_import['quantity'],$db,"non");
/* 				if(isset($json['Ack'])&& $json['Ack']=="Warning"){
					$new_ebay_id=$json['ItemID'];
					$sql2 = 'UPDATE `oc_product`SET usa="5" WHERE `oc_product`.`product_id` ='.$data_import['product_id']; 
					echo $sql2.'<br><br>';
					$req2 = mysqli_query($db,$sql2);
					echo $data_import['product_id']."<br>";
				}else{
					$sql2 = 'UPDATE `oc_product`SET usa="9" WHERE `oc_product`.`product_id` ='.$data_import['product_id'];
					$req2 = mysqli_query($db,$sql2);
					echo $data_import['product_id']."---non<br>";
				}

			unset($json); */
	//		}

echo "<br>Produit problematique:".$j;
echo "<br>Image d'origine a ete renommee:".$k;
echo "<br>Produit a faire manuel:".$i;

function upload_from_phoenix($product_id,$piclink,$principal,$db){
	if(strpos($piclink,"jpeg")>0)$pos=".jpeg";
	if(strpos($piclink,"JPEG")>0)$pos=".jpeg";
	if(strpos($piclink,"png")>0)$pos=".png";
	if(strpos($piclink,"PNG")>0)$pos=".png";
	if(strpos($piclink,"jpg")>0)$pos=".jpg";
	if(strpos($piclink,"JPG")>0)$pos=".jpg";
	if(strpos($piclink,"gif")>0)$pos=".gif";
	if(strpos($piclink,"GIF")>0)$pos=".gif";
	//echo "upload_from_ebay".$pos."<br>";
	$uploads_dir = 'image/catalog/product';
	$sqldir = 'catalog/product';
	if ($piclink!="")$picexterne2=$piclink;	 
		// echo $picexterne2;
		 $picexterne2=str_replace(array("\r","\n", " "),"",$picexterne2);
		 $filterimage=explode("?",basename($picexterne2));
		 
		 $path=explode($pos,basename($filterimage[0]));
		 
		$ext=count($path)-1;

	 if(($piclink!="") ){ //|| $piclink!=""

		$image = file_get_contents($picexterne2);
//echo '/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/'.$product_id."/";
						if (!file_exists("/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/".$product_id."/")) {
							mkdir("/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/".$product_id."/", 0755, true);
						}
		

		//file_put_contents($imagepath, $image); //Where to save the image on your server
		
						
						if($principal==1) {
							
							$rdproduct_id="pri".mt_rand ( 1 , 99 );
			//				$dir_name=$SITE_ROOT."/".$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.$pos;
							$imagepath='/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/'.$product_id."/".$product_id.$rdproduct_id.$pos;
							$error=save_image($piclink, $imagepath);
							if($error==""){
								delete_photo($product_id,"principal",$db);
								$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.$pos."' where product_id=".$product_id;
								$req2=mysqli_query($db,$sql2);
							}
						}else{
							$rdproduct_id="sec".mt_rand ( 1 , 99 );
			//				$dir_name=$SITE_ROOT."/".$uploads_dir."/".$product_id."/".$product_id.$rdproduct_id.$pos;
							$imagepath='/home/n7f9655/public_html/phoenixliquidation/image/catalog/product/'.$product_id."/".$product_id.$rdproduct_id.$pos;
							$error=save_image($piclink, $imagepath);
							if($error==""){
								$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$product_id."','".$sqldir."/".$product_id."/".$product_id.$rdproduct_id.$pos."')";
								$req2=mysqli_query($db,$sql2);
							}
						}
						
		//echo $sql2."<br>";
		
	
		//echo '<br>'.$sql2;
	 }
	 return "catalog/product/".$product_id.$rdproduct_id.".".$path[$ext];
}
?>
<html>
<head><?php header('Content-Type: text/html; charset=iso-8859-1'); ?>

    <title></title>


</head>
<body bgcolor="ffffff">
</body>
</html>

<? 

// on ferme la connexion &agrave; mysql 
mysqli_close($db); ?>