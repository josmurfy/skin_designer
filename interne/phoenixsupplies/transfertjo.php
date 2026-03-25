<?
//echo $_POST['sku'];
$sku=$_POST['sku'];
// on se connecte � MySQL 
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
 

// mettre ajour les orphelins non declar�

//echo "allo";
		$sql = 'SELECT * FROM `oc_product_reception` where title="" group by upc';
//echo $sql;
		// on envoie la requ�te
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
		while ($data = mysqli_fetch_assoc($req))
		{
				$walmart=0;
				$close=0;
				$sql3 = 'SELECT * FROM `oc_product_analyse` where upc='.$data[upc].' group by upc'; 
				$req3 = mysqli_query($db,$sql3);
				$data3 = mysqli_fetch_assoc($req3);
				if($data3[walmartprice]>0){
					$walmart=9999;
					$pricedetailusd=$data3[walmartprice];					
				}elseif($data3[amazonprice]>$data3[ebayprice]){
					$pricedetailusd=$data3[amazonprice];
				}else{
					$pricedetailusd=$data3[ebayprice];
				}
				if($pricedetailusd==0)$close=3;
				$data3[weight]=$data3[weight]/16;
				$sql2 = 'UPDATE `oc_product_reception` SET quantity='.$data[quantityrecu].',model="'.addslashes($data3[model]).'",title="'.addslashes($data3[title]).'",brand="'.addslashes($data3[brand]).'",priceretailnew='.$walmart.',close='.$close.',  pricedetailusd='.$pricedetailusd.' where  title="" and upc='.$data[upc];
				/*quantityrecu=0,quantity='.$data[quantityrecu].',*/
				echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				
                
		}




// on cr�e la requ�te SQL verifier les ordres 
// savoir ledernier id 
		$sql = 'SELECT * FROM `oc_product_reception` where ok=0  order by upc';
//echo $sql;
		// on envoie la requ�te
		$req = mysqli_query($db,$sql);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
				$quantity=0;
				$upcancien=0;
				$quantityrecu=0;
				$quantitydetruit=0;
				$quantityinterne=0;
				$quantityrevente=0;
		$ok=0;
		while ($data = mysqli_fetch_assoc($req))
		{
			$ok=1;
			if ($data[upc]==$upcancien || $upcancien==0)
			{
				$product_id=$data['product_id'];
				$upcancien=$data[upc];
				$quantity=$quantity+$data['quantity'];
				$quantityrecu=$quantityrecu+$data['quantityrecu'];
				$quantitydetruit=$quantitydetruit+$data['quantitydetruit'];
				$quantityinterne=$quantityinterne+$data['quantityinterne'];
				if($data['recycle']==1)$quantityrevente=$quantityrevente+$data['quantityrecu'];
				//echo $quantity."<BR>";
				//echo $quantityrecu."<BR><BR>";
			}else{
				$sql4 = 'SELECT * FROM `oc_product_reception` where upc='.$upcancien.' group by upc';
//echo $sql4;
		// on envoie la requ�te
				$req4 = mysqli_query($db,$sql4);
				$data4 = mysqli_fetch_assoc($req4);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
				$sql2 = "INSERT INTO `oc_product_index` ( `priceretailnew`, `title`, `model`, `upc`, `mpn`,`quantity`, `brand`, `priceebay`, `pricesuggest`, `pricedetailusd`, `pricedetailcad`, `pricecost`, `pricecostper`, `pricepotentiel`, `profit`, `quantityrecu`, `quantitydetruit`, `quantityinterne`, `quantityrevente`, `transfert`) VALUES
														('".addslashes($data4['priceretailnew'])."', '".addslashes($data4['title'])."', '".addslashes($data4['model'])."', '".addslashes($data4['upc'])."', '".addslashes($data4['mpn'])."', '".$quantity."', '".addslashes($data4['brand'])."', '".addslashes($data4['priceebay'])."', '".addslashes($data4['pricesuggest'])."', '".addslashes($data4['pricedetailusd'])."', '".addslashes($data4['pricedetailcad'])."', '".addslashes($data4['pricecost'])."', '".addslashes($data4['pricecostper'])."', '".addslashes($data4['pricepotentiel'])."', '".addslashes($data4['profit'])."', '".addslashes($quantityrecu)."', '".addslashes($quantitydetruit)."', '".addslashes($quantityinterne)."', '".addslashes($quantityrevente)."', '".addslashes($data4['transfert'])."')";
				echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				$sql2 = 'UPDATE `oc_product_reception` SET ok=1 where upc='.$upcancien;
				echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				$product_id=$data['product_id'];
				
				$upcancien=$data[upc];
				$quantity=$data['quantity'];
				$quantityrecu=$data['quantityrecu'];
				$quantitydetruit=$data['quantitydetruit'];
				$quantityinterne=$data['quantityinterne'];
				if($data['recycle']==1){
					$quantityrevente=$data['quantityrecu'];
				}else{
					$quantityrevente=0;
				}
			}
		}
		if ($upcancien!=""){
				$sql4 = 'SELECT * FROM `oc_product_reception` where upc='.$upcancien.' group by upc';
//echo $sql4;
		// on envoie la requ�te
				$req4 = mysqli_query($db,$sql4);
				$data4 = mysqli_fetch_assoc($req4);
		// on fait une boucle qui va faire un tour pour chaque enregistrement
				$sql2 = "INSERT INTO `oc_product_index` ( `priceretailnew`, `title`, `model`, `upc`, `mpn`,`quantity`, `brand`, `priceebay`, `pricesuggest`, `pricedetailusd`, `pricedetailcad`, `pricecost`, `pricecostper`, `pricepotentiel`, `profit`, `quantityrecu`, `quantitydetruit`, `quantityinterne`, `quantityrevente`, `transfert`) VALUES
														('".addslashes($data4['priceretailnew'])."', '".addslashes($data4['title'])."', '".addslashes($data4['model'])."', '".addslashes($data4['upc'])."', '".addslashes($data4['mpn'])."', '".$quantity."', '".addslashes($data4['brand'])."', '".addslashes($data4['priceebay'])."', '".addslashes($data4['pricesuggest'])."', '".addslashes($data4['pricedetailusd'])."', '".addslashes($data4['pricedetailcad'])."', '".addslashes($data4['pricecost'])."', '".addslashes($data4['pricecostper'])."', '".addslashes($data4['pricepotentiel'])."', '".addslashes($data4['profit'])."', '".addslashes($quantityrecu)."', '".addslashes($quantitydetruit)."', '".addslashes($quantityinterne)."', '".addslashes($quantityrevente)."', '".addslashes($data4['transfert'])."')";
				echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				$sql2 = 'UPDATE `oc_product_reception` SET ok=1 where upc='.$upcancien;
				echo $sql2.'<br><br>';		
				$req2 = mysqli_query($db,$sql2);
		}

		
		$sql = 'SELECT * FROM `oc_product_analyse` where ok=0 and title!=""  group by upc';
//echo $sql;
		// on envoie la requ�te
		$req = mysqli_query($db,$sql);
		$ok=0;
		while ($data = mysqli_fetch_assoc($req))
		{
			$ok=1;

		// on fait une boucle qui va faire un tour pour chaque enregistrement
				if($data['title']==""){
					$algopix=3;
				}else{
					$algopix=1;
				}
				$sql2 = "update `oc_product_index` set   description= '".$data['description']."',
						  color= '".$data['color']."',
						  imageurl= '".$data['imageurl']."',
						  ean= '".$data['ean']."',
						  asin= '".$data['asin']."',
						  weight= '".$data['weight']."',
						  width= '".$data['width']."',
						  height= '".$data['height']."',
						  length= '".$data['length']."',
						  shippingcost= '".$data['shippingcost']."',
						  walmartprice= '".$data['walmartprice']."',
						  amazonprice= '".$data['amazonprice']."',
						  ebayprice= '".$data['ebayprice']."',
						  googlesearchphrase= '".$data['googlesearchphrase']."',
						  algopix='".$algopix."'
						  where upc=".$data['upc'];
				echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);
				$sql2 = 'UPDATE `oc_product_analyse` SET ok=1 where upc='.$data['upc'];
				echo $sql2.'<br><br>';
				$req2 = mysqli_query($db,$sql2);

		}

 // on ferme la connexion � mysql 
mysqli_close($db); ?>