<?
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('phoenkv5_storeliquidation',$db);
if ($_GET['sku']!=""){
	$_POST['sku']=$_GET['sku'];
	//echo 'allo';
}
//echo $GLOBALS['SITE_ROOT'].$uploads_dir."/108_".$key.".".$path[1];  
	//echo  $_POST['piclink'];
if ($_POST['submit']=="Submit" and $_GET['insert']=="oui"){
header("location: modificationitem.php?sku=".$_GET['sku']); 
exit();		}	

if ($_GET['product_image_id']!="" && $_GET['delete']=="oui" )	{
			$sql = 'delete from `oc_product_image` where product_image_id='.$_GET['product_image_id']; 
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);
}
	
if ($_POST['sku']=="" && $_POST['skucheck']==""){
	$new=0;
	//echo 'allo';
}
if($_POST['skucheck']!="")$_POST['sku']=$_POST['skucheck'];
if ($_GET['flusher']=="oui"){
	unlink('/home/phoenkv5/public_html/cart/image/'.$_GET['image'].'');	
	echo '/home/phoenkv5/public_html/cart/image/'.$_GET['image'].'';
}

if (isset($_POST['sku']) && $_POST['sku']!=""){
			
			//echo $_POST['sku'];
			$sql = 'SELECT * FROM `oc_product`,`oc_product_description` where oc_product.product_id=oc_product_description.product_id and sku = "'.$_POST['sku'].'"';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			$_POST['sku']=$data['sku'];
			$_POST['new']=1;
			$_POST['product_id']=$data['product_id'];
			$_POST['image']=$data['image'];
			
			$new=1;
	  		$sql9 = 'SELECT * FROM `oc_product_index` where upc="'.substr ($_POST['sku'],0,12).'"';
			//echo $sql9;
			$req9 = mysqli_query($db,$sql9);
			$data9 = mysqli_fetch_assoc($req9);
			$_POST['andescription']=$data9['description'];
			$sql8 = 'SELECT * FROM `oc_product_index` where upc="'.substr ($_POST['sku'],0,12).'"';
			//echo $sql8;
			$req8 = mysqli_query($db,$sql8);
			$data8 = mysqli_fetch_assoc($req8);
			
if($_POST['etat']=="9")$algoetat="New&costNew=".$data8[pricecost];
if($_POST['etat']=="99")$algoetat="New&costNew=".$data8[pricecost];
if($_POST['etat']=="2")$algoetat=="Refurbished&costRefurbished=".$data8[pricecost]."&costNew=".$data8[pricecost];
if($_POST['etat']=="22")$algoetat="Refurbished&costRefurbished=".$data8[pricecost]."&costNew=".$data8[pricecost];
if($_POST['etat']=="8")$algoetat="Used&costUsed=".$data8[pricecost]."&costNew=".$data8[pricecost];	   

	 $_POST['andescription']=$data9['description'];
	
	 $picexterne=$data9['imageurl'];
	 $picexterne2=$data9['imageurl'];
if ($_POST['piclink']!="")$picexterne2=$_POST['piclink'];	 
		// echo $picexterne2;
		 $picexterne2=str_replace(array("\r","\n", " "),"",$picexterne2);
		 $path=explode(".",basename($picexterne2));
		 
	//	echo count($path); 
		$ext=count($path)-1;

	 if(($_POST['image']=="" && $ext>=1 || $_POST['piclink']!="") ){ //|| $_POST['piclink']!=""
//echo "allo";
		// echo $picexterne2;
		// echo $picexterne2;
		$image = file_get_contents($picexterne2);
		$imagepath='/home/phoenkv5/public_html/cart/image/catalog/product/'.$_POST['product_id'].".".$path[$ext];
		//echo "<br>".$imagepath;
		//$imagepath=str_replace(array("\r","\n"),"",$imagepath);
		//$image = file_get_contents('https://images-na.ssl-images-amazon.com/images/I/31vgJTuXe1L.jpg');
		//echo urlencode($imagepath);
		file_put_contents($imagepath, $image); //Where to save the image on your server
		//file_put_contents('/home/phoenkv5/public_html/image/catalog/producttmp/zzzz2.jpg', $image); //Where to save the image on your server
		$sql2="UPDATE `oc_product` SET image ='catalog/product/".$_POST['product_id'].".".$path[$ext]."' where product_id=".$_POST['product_id'];
		$req2=mysqli_query($db,$sql2);
		echo $sql2;
		$_POST['image']="catalog/product/".$_POST['product_id'].".".$path[$ext];
	 }
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
		
	if (!isset($_FILES['image']['tmp_name'])) {
		echo "";
		}else{
		$file=$_FILES['image']['tmp_name'];
		//$image= addslashes(file_get_contents($_FILES['image']['tmp_name']));
		$image_name= addslashes($_FILES['image']['name']);
			//print_r($_FILES);
			$uploads_dir = 'image/catalog/product';
			$sqldir = 'catalog/product';
			//$uploads_dir = 'upload';
			foreach ($_FILES['image']['error'] as $key => $error) {
				if ($error == 0) {
					if (is_uploaded_file($_FILES['image']['tmp_name'][$key]))
					{
						$rdsku=mt_rand ( 1 , 9999 );
						$tmp_name = $_FILES['image']['tmp_name'][$key];
						// basename() may prevent filesystem traversal attacks;
						// further validation/sanitation of the filename may be appropriate
						$name = basename($_FILES['image']['name'][$key]);
						$path=explode(".",basename($_FILES['image']['name'][$key]));
						
						$dir_name=$GLOBALS['SITE_ROOT']."/".$uploads_dir."/".$_POST['product_id']."_".$rdsku.".".$path[1];
						move_uploaded_file($tmp_name, $dir_name);
						//echo $GLOBALS['SITE_ROOT'].$uploads_dir."/108_".$key.".".$path[1];  

						$sql2="INSERT INTO oc_product_image (product_id, image) VALUES ('".$_POST['product_id']."','".$sqldir."/".$_POST['product_id']."_".$rdsku.".".$path[1]."')";
						$req2=mysqli_query($db,$sql2);
						//echo $sql2;
					}
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

<form id="form_67341" class="appnitro" action="multiupload.php?sku=<? echo $_POST['sku'];?>" method="post">
<div class="form_description">


</div>
<h3>SKU <?if($new==1){?><a href="multiupload.php" class="button--style-red">Changer d'item</a><?}?> <a href="interne.php" class="button--style-red">Retour au MENU</a> <a href="listing.php?sku=<?echo $_POST['sku'];?>" class="button--style-red">Menu Listing</a> <a href="multiupload.php?flusher=oui&image=<? echo $_POST['image'];?>&sku=<? echo $_POST['sku'];?>" class="button--style-red">Flusher Photo</a></h3>
<input id="sku" type="text" name="sku" value="<?echo $_POST['sku'];?>" maxlength="255" <?if($new==1)echo "disabled";?> autofocus>
<input type="hidden" name="skucheck" value="<?echo $_POST['sku'];?>" />
<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />

<br>
<?if ($new>=1){?>
<svg class="barcode"
	jsbarcode-value="<?echo $_POST['sku'];?>"
	jsbarcode-textmargin="0"
	jsbarcode-height="24"
	jsbarcode-fontoptions="bold"
	jsbarcode-fontsize="12">
</svg>
<script>
JsBarcode(".barcode").init();
</script>
<?
if($picexterne!="")
{
	echo '<br><a href="'.$picexterne.'" target="_blank"><img src="'.$picexterne.'" alt="" width="100"/></a><br>';
}
?>

</form>
<h3><label class="description" for="element_1">Titre :</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<br>
<form action="multiupload.php?insert=oui&sku=<? echo $_POST['sku'];?>" method="post" enctype="multipart/form-data" name="addroom">
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
				<a href="multiupload.php?delete=oui&product_image_id=<?
				echo $data2['product_image_id'];
				?>&sku=<?echo $_POST['sku'];?>" class="button--style-red">Supprimer</a>
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
 <input type="hidden" name="sku" value="<?echo $_POST['sku'];?>" />
 <input type="hidden" name="upload" value="upload" />
 <?}?>
 	<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
 </form>
<br />

<br />
<br />
 <h1><a href="interne.php" class="button--style-red">Retour au MENU</a></h1>
 <script>
JsBarcode(".barcode").init();
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['sku'],0,12);?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['sku'],0,12);?>&rt=nc&LH_PrefLoc=1","ebaynew");
window.open("https://www.google.com/search?q=<?echo substr ($_POST['sku'],0,12);?>&rt=nc&LH_PrefLoc=1","google");
</script>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>