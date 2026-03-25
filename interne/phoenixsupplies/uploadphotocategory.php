<?
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('phoenkv5_store',$db);
if ($_GET['category_id']!=""){
	$_POST['category_id']=$_GET['category_id'];
	//echo 'allo';
}
//echo $GLOBALS['SITE_ROOT'].$uploads_dir."/108_".$key.".".$path[1]; 
	//echo  $_POST['piclink'];
if ($_POST['submit']=="Submit" and $_GET['insert']=="oui"){
header("location: modificationitemusa.php?category_id=".$_GET['category_id']);  
exit();		}	


	
if ($_POST['category_id']=="" && $_POST['category_idcheck']==""){
	$new=0;
	//echo 'allo';
}
if($_POST['category_idcheck']!="")$_POST['category_id']=$_POST['category_idcheck'];

if (isset($_POST['category_id']) && $_POST['category_id']!=""){
			
			//echo $_POST['category_id'];
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and oc_category.category_id = "'.$_POST['category_id'].'"';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			$_POST['category_id']=$data['category_id'];
			$_POST['new']=1;
			$_POST['category_id']=$data['category_id'];
			$_POST['image']=$data['image'];
			
			$new=1;
}
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
		$imagepath='/home/phoenkv5/public_html/phoenixliquidation/image/catalog/'.$_POST['category_id'].".".$path[$ext];
		//echo "<br>".$imagepath;
		//$imagepath=str_replace(array("\r","\n"),"",$imagepath);
		//$image = file_get_contents('https://images-na.ssl-images-amazon.com/images/I/31vgJTuXe1L.jpg');
		//echo urlencode($imagepath);
		file_put_contents($imagepath, $image); //Where to save the image on your server
		//file_put_contents('/home/phoenkv5/public_html/image/categorytmp/zzzz2.jpg', $image); //Where to save the image on your server
		$sql2="UPDATE `oc_category` SET image ='catalog/".$_POST['category_id'].".".$path[$ext]."' where category_id=".$_POST['category_id'];
		$req2=mysqli_query($db,$sql2);
		echo $sql2;
		$_POST['image']="catalog/".$_POST['category_id'].".".$path[$ext];
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
			$uploads_dir = 'image/category';
			$sqldir = 'category';
			//$uploads_dir = 'upload';
				if ($_FILES['imageprincipale']['error'] == 0) {
					if (is_uploaded_file($_FILES['imageprincipale']['tmp_name']))
					{
						
						$tmp_name = $_FILES['imageprincipale']['tmp_name'];
						// basename() may prevent filesystem traversal attacks;
						// further validation/sanitation of the filename may be appropriate
						$name = basename($_FILES['imageprincipale']['name']);
						$path=explode(".",basename($_FILES['imageprincipale']['name']));
						$dir_name=$GLOBALS['SITE_ROOT']."/".$uploads_dir."/CAT".$_POST['category_id'].".".$path[1];
						move_uploaded_file($tmp_name, $dir_name);
						 
						$sql2="UPDATE `oc_category` SET image ='".$sqldir."/CAT".$_POST['category_id'].".".$path[1]."' where category_id=".$_POST['category_id'];
						$req2=mysqli_query($db,$sql2);
						//echo $sql2;
						$_POST['image']=$sqldir."/CAT".$_POST['category_id'].".".$path[1];
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
<h1>Ajout ou Modification Photos Category </h1>

<form id="form_67341" class="appnitro" action="uploadphotocategory.php?category_id=<? echo $_POST['category_id'];?>" method="post">
<div class="form_description">


</div>
<h3>category_id <?if($new==1){?><a href="uploadphotocategory.php" class="button--style-red">Changer d'item</a><?}?> <a href="interneusa.php" class="button--style-red">Retour au MENU</a> <a href="listingusa.php?category_id=<?echo $_POST['category_id']?>" class="button--style-red">Menu Listing</a></h3>
<a href="ajoutcategorie.php?parent_id=<?echo $_GET['parent_id_avant'];?>" class="button--style-red">Retour en arriere</a>
<input id="category_id" type="text" name="category_id" value="<?echo $_POST['category_id'];?>" maxlength="255" <?if($new==1)echo "disabled";?> autofocus>
<input type="hidden" name="category_idcheck" value="<?echo $_POST['category_id'];?>" />
<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />

<br>


</form>
<h3><label class="description" for="element_1">Titre :</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<br>
<form action="uploadphotocategory.php?insert=oui&category_id=<? echo $_POST['category_id'];?>" method="post" enctype="multipart/form-data" name="addroom">
 <h3>Image Principale: </h3>
	<table>
		<tr><td style="text-align: center;" align="center" valign="middle">
			<img src="https://www.phoenixliquidation.ca/image/<? echo $_POST['image'];?>" width="200" 
		</td></tr>
	</table>
	<input type="file" name="imageprincipale" class="ed"><br />
	 <h3>Ajout d'une photo via site</h3> <input id="piclink" type="text" name="piclink" value="" maxlength="255">

	<br>
 
 <input type="file" name="image[]" multiple class="ed"><br /><br>
 <input type="submit" name="Submit" value="Upload" id="button1" />
 <input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
 <input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
 <input type="hidden" name="upload" value="upload" />

 	<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
 </form>
<br />

<br />
<br />
 <h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>
 <script>
JsBarcode(".barcode").init();
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['category_id'],0,12);?>&LH_PrefLoc=1&rt=nc&LH_Sold=1&LH_Complete=1","ebaysold");
window.open("https://www.ebay.com/sch/i.html?_nkw=<?echo substr ($_POST['category_id'],0,12);?>&rt=nc&LH_PrefLoc=1","ebaynew");
window.open("https://www.google.com/search?q=<?echo substr ($_POST['category_id'],0,12);?>&rt=nc&LH_PrefLoc=1","google");
</script>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>