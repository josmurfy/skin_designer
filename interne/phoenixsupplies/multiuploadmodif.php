<?
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db);
if ($_GET['product_id']!=""){
	$_POST['product_id']=$_GET['product_id'];
	//echo 'allo';
}
//echo $GLOBALS['SITE_ROOT'].$uploads_dir."/108_".$key.".".$path[1];  
	//echo  $_POST['piclink'];
if ($_POST['submit']=="Submit" and $_GET['insert']=="oui"){
header("location: updateprice.php?product_id=".$_POST['product_id']); 
exit();		}	

if ($_GET['product_image_id']!="" && $_GET['delete']=="oui" )	{
			$sql = 'delete from `oc_product_image` where product_image_id='.$_GET['product_image_id'];
	echo $sql;
			$req = mysqli_query($db,$sql);
}else{
		
	if ($_POST['product_id']=="" && $_POST['product_idcheck']==""){
		$new=0;
		//echo 'allo';
	}
	if($_POST['product_idcheck']!="")$_POST['product_id']=$_POST['product_idcheck'];
	if ($_GET['flusher']=="oui"){
		unlink('/home/phoenkv5/public_html/cart/image/'.$_GET['image'].'');	
		echo '/home/phoenkv5/public_html/cart/image/'.$_GET['image'].'';
	}

	if (isset($_POST['product_id']) && $_POST['product_id']!=""){
				
				//echo $_POST['product_id'];
				$sql = 'SELECT * FROM `oc_product`,`oc_product_description`,oc_ebay_listing where oc_product.product_id=oc_ebay_listing.product_id and oc_product.product_id=oc_product_description.product_id and  oc_product.product_id="'.$_POST['product_id'].'" limit 1';
		//echo $sql;
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);
				$_POST['ebay_item_id']=$data['ebay_item_id'];
				$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and  oc_product.product_id="'.$_POST['product_id'].'" limit 1';
		//echo $sql;
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);

				$_POST['name_product']=$data['name'];
				$_POST['product_id']=$data['product_id'];
				$_POST['new']=1;
				$_POST['image']=$data['image'];
				
				$new=1;
				


		 $_POST['andescription']=$data9['description'];
		
		 $picexterne=$data9['imageurl'];
		 $picexterne2=$data9['imageurl'];
	if ($_POST['piclink']!="")
			$picexterne2=$_POST['piclink'];	 
			// echo $picexterne2;
			 $picexterne2=str_replace(array("\r","\n", " "),"",$picexterne2);
			 $path=explode(".",basename($picexterne2));
			 
		//	echo count($path); 
			$ext=count($path)-1;

		 if(($_POST['image']=="" && $ext>=1 || $_POST['piclink']!="") ){ //|| $_POST['piclink']!=""
			$image = file_get_contents($picexterne2);
			$imagepath='/home/phoenkv5/public_html/cart/image/catalog/product/'.$_POST['product_id'].".".$path[$ext];
			file_put_contents($imagepath, $image); //Where to save the image on your server
			//file_put_contents('/home/phoenkv5/public_html/image/catalog/producttmp/zzzz2.jpg', $image); //Where to save the image on your server
			$sql2="UPDATE `oc_product` SET image ='catalog/product/".$_POST['product_id'].".".$path[$ext]."' where product_id=".$_POST['product_id'];
			$req2=mysqli_query($db,$sql2);
			echo $sql2;
			$_POST['image']="catalog/product/".$_POST['product_id'].".".$path[$ext];
		 }
	}
	//$piclinkadd= array();
	$piclinkadd=$_POST['piclinkadd'];
	//print_r($piclinkadd);
	//echo count ($piclinkadd, COUNT_RECURSIVE);
	if (count ($piclinkadd)>0 && $piclinkadd[0]!=""){
		//echo "allo";
			$picexterne=$piclinkadd;
			for ($i = 0; $i < count ($piclinkadd); $i++) {
					if($piclinkadd[$i]!=""){
						$rdproduct_id=mt_rand ( 1 , 9999 );
						$picexterne2=$picexterne[$i];	 
						//echo "<br>".$picexterne2."<br>";
						//echo "allo2<br>";
						$picexterne2=str_replace(array("\r","\n", " "),"",$picexterne2);
						$path=explode(".",basename($picexterne2));
						 
					//	echo count($path); 
						$ext=count($path)-1;
						$piclinkaddimage=$_POST['piclinkaddimage'];
						 if(($_POST['image']=="" && $ext>=1 || $_POST['piclinkadd']!="") ){ //|| $_POST['piclink']!=""
						//echo "allo<br>";
							$image = file_get_contents($picexterne2);
							$imagepath='/home/phoenkv5/public_html/cart/image/catalog/product/'.$_POST['product_id']."_".$rdproduct_id.".".$path[$ext];

							file_put_contents($imagepath, $image); //Where to save the image on your server
							$sql = 'delete from `oc_product_image` where product_image_id='.$piclinkaddimage[$i];
							//echo $sql."<br>";
							$req = mysqli_query($db,$sql);
							$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$_POST['product_id']."','catalog/product/".$_POST['product_id']."_".$rdproduct_id.".".$path[$ext]."')";
							$req2=mysqli_query($db,$sql2);
							//echo $sql2."<br>";
							$_POST['image']="catalog/product/".$_POST['product_id'].".".$path[$ext];
					 }
				 } 
			}
	}
}
	if (isset($_POST['product_id']) && $_POST['product_id']!=""){
				
				//echo $_POST['product_id'];
				$sql = 'SELECT * FROM `oc_product`,`oc_product_description`,oc_ebay_listing where oc_product.product_id=oc_ebay_listing.product_id and oc_product.product_id=oc_product_description.product_id and  oc_product.product_id="'.$_POST['product_id'].'" limit 1';
		//echo $sql;
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);
				$_POST['ebay_item_id']=$data['ebay_item_id'];
				$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and  oc_product.product_id="'.$_POST['product_id'].'" limit 1';
		//echo $sql;
				$req = mysqli_query($db,$sql);
				$data = mysqli_fetch_assoc($req);

				$_POST['name_product']=$data['name'];
				$_POST['product_id']=$data['product_id'];
				$_POST['new']=1;
				$_POST['image']=$data['image'];
				
				$new=1;
				


		 $_POST['andescription']=$data9['description'];
	}
if($_POST['upload']=="upload"){
	
	$GLOBALS['SITE_ROOT']=$_SERVER['DOCUMENT_ROOT'];
	if (!isset($_FILES['imageprincipale'])) {
		echo "";
		}else{
		$file=$_FILES['imageprincipale']['tmp_name'];
		//$image= addslashes(file_get_contents($_FILES['imageprincipale']['tmp_name']));
		$image_name= addslashes($_FILES['imageprincipale']['name']);
			//print_r($_FILES);
			$uploads_dir = 'image/catalog/product';
			$sqldir = 'catalog/product';
			
			//$uploads_dir = 'upload';
				if ($_FILES['imageprincipale']['error'] == 0) {
					if (is_uploaded_file($_FILES['imageprincipale']['tmp_name']))
					{
						$tmp_name = $_FILES['imageprincipale']['tmp_name'];
						// basename() may prevent filesystem traversal attacks;
						// further validation/sanitation of the filename may be appropriate
						$name = basename($_FILES['imageprincipale']['name']);
						$path=explode(".",basename($_FILES['imageprincipale']['name']));
						$dir_name=$GLOBALS['SITE_ROOT']."/".$uploads_dir."/".$_POST['product_id'].".".$path[1];
						move_uploaded_file($tmp_name, $dir_name);
						 
						$sql2="UPDATE `oc_product` SET image ='".$sqldir."/".$_POST['product_id'].".".$path[1]."' where product_id=".$_POST['product_id'];
						$req2=mysqli_query($db,$sql2);
						//echo $sql2;
						$_POST['image']=$sqldir."/".$_POST['product_id'].".".$path[1];
					}
				}
		}		

}?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffbaba">
<h1>Ajout ou Modification Photos Item </h1>

<form id="form_67341" class="appnitro" action="multiuploadmodif.php?product_id=<? echo $_POST['product_id'];?>" method="post">
<div class="form_description">


</div>
<h3>ID Produit <?if($new==1){?><?}?> <a href="updateprice.php" class="button--style-red">Retour au MENU</a> <a href="listingusa.php?product_id=<?echo $_POST['product_id'];?>" class="button--style-red">Menu Listing</a> <a href="multiuploadmodif.php?flusher=oui&image=<? echo $_POST['image'];?>&product_id=<? echo $_POST['product_id'];?>" class="button--style-red">Flusher Photo</a></h3>
<input id="product_id" type="text" name="product_id" value="<?echo $_POST['product_id'];?>" maxlength="255" <?if($new==1)echo "disabled";?> autofocus>
<input type="hidden" name="product_idcheck" value="<?echo $_POST['product_id'];?>" />
<input type="hidden" name="ebay_item_id" value="<?echo $_POST['ebay_item_id'];?>" />

<br>


</form>
<h3><label class="description" for="element_1">Titre :</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<br>
<form action="multiuploadmodif.php?insert=oui&product_id=<? echo $_POST['product_id'];?>" method="post" enctype="multipart/form-data" name="addroom">
 <h3>Image Principale: </h3>
	<table>
		<tr><td style="text-align: center;" align="center" valign="middle">
			<img src="https://www.phoenixsupplies.ca/image/<? echo $_POST['image'];?>" width="200" 
		</td></tr>
	</table>
	<input type="file" name="imageprincipale" class="ed"><br />
	 <h3>Ajout d'une photo via site</h3> <input id="piclink" type="text" name="piclink" value="" maxlength="255">

	<br>
 <h3>Autres Images: </h3>

<table bgcolor="ffffff"> <tbody><tr>
<?			$i=0;
			$sql2 = "SELECT * FROM oc_product_image where product_id=".$_POST['product_id'];
			$req2= mysqli_query($db,$sql2); 
			while($data2 = mysqli_fetch_assoc($req2))
			{
				if($i<13){
				if($i==5 ||$i==10)echo "</tr><tr>";?>
				<td style="text-align: center;" align="center" valign="middle"><img src="https://www.phoenixsupplies.ca/image/
				<?
				echo $data2['image'];
				?>
				" width="200"><br>
				<a href="multiuploadmodif.php?delete=oui&product_image_id=<?
				echo $data2['product_image_id'];
				?>&product_id=<?echo $_POST['product_id'];?>" class="button--style-red">Supprimer</a><br>
				<h3>Ajout d'une photo via site</h3> <input id="piclinkadd[<?echo $i?>]" type="text" name="piclinkadd[<?echo $i?>]" value="" maxlength="255">
				<input type="hidden" name="piclinkaddimage[<?echo $i?>]" value="<?echo $data2['product_image_id'];?>" />
				</td>
				
				<?
				
				$i++;
				}
			}
			$description.='</tbody></table><br>';
?>
</tr></tbody></table><br>
 <input type="file" name="image[]" multiple class="ed"><br /><br>
 <input type="submit" name="Submit" value="Upload" id="button1" />
 <input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
 <input type="hidden" name="ebay_item_id" value="<?echo $_POST['ebay_item_id'];?>" />
 <input type="hidden" name="upload" value="upload" />

 	<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
 </form>
<br />

<br />
<br />

 <script>
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['ebay_item_id'],0,12);?>&rt=nc&LH_PrefLoc=1","ebaynewmodif");
</script>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>