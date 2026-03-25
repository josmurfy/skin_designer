<?


include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 

//doublon deja list/
			$sql2 = 'select * from oc_product where quantity>0 and (sku not like "%NO" and sku not like "%R" and sku not like "%U" and sku not like "%MR") and usa=1 order by sku';
			$req2= mysqli_query($db,$sql2); 
			while($data2 = mysqli_fetch_assoc($req2))
			{
			$sql = 'delete from oc_product where sku='.$data2[sku].' and quantity=0';
			$req= mysqli_query($db,$sql); 
			//$data = mysqli_fetch_assoc($req);
			$rowverif= mysqli_affected_rows($db);
			echo "<br>".$sql." ".$rowverif;
			}
//doublons non list/			
			
			
			$sql2 = 'select * from oc_product where quantity=0 and image is null and (sku not like "%NO" and sku not like "%R" and sku not like "%U" and sku not like "%MR") and usa=1 group by sku';
			$req2= mysqli_query($db,$sql2); 
			while($data2 = mysqli_fetch_assoc($req2))
			{
			$sql = 'delete from oc_product where sku='.$data2[sku].' and quantity=0 and product_id<>'.$data2[product_id];
			$req= mysqli_query($db,$sql); 
			//$data = mysqli_fetch_assoc($req);
			$rowverif= mysqli_affected_rows($db);
			echo "<br>".$sql." ".$rowverif;
			}

			
			
			
			
mysqli_close($db); 
?>