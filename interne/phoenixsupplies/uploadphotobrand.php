<?
include 'connection.php'; header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s�lectionne la base 
//mysqli_select_db('phoenkv5_store',$db);
if ($_GET['manufacturer_id']!=""){
	$_POST['manufacturer_id']=$_GET['manufacturer_id'];
	//echo 'allo';
}
//echo $GLOBALS['SITE_ROOT'].$uploads_dir."/108_".$key.".".$path[1]; 
	//echo  $_POST['piclink'];

if($_GET['exclure']=="oui"){
							 
	$sql2="UPDATE `oc_manufacturer_to_store` SET store_id ='1' where manufacturer_id=".$_POST['manufacturer_id'];
	$req2=mysqli_query($db,$sql2);
	$_POST['manufacturer_id']="";
	//echo "oui";

}


if (empty($_POST['manufacturer_id']) && $_POST['manufacturer_id']==""){
			
			//echo $_POST['manufacturer_id'];
			$sql = "SELECT * FROM `oc_manufacturer`,`oc_manufacturer_to_store` where oc_manufacturer.manufacturer_id=oc_manufacturer_to_store.manufacturer_id and oc_manufacturer_to_store.store_id=0 and (image is NULL or image ='') ";
	//echo $sql;
			$req = mysqli_query($db,$sql);
			echo mysqli_num_rows($req);
			$sql = "SELECT * FROM `oc_manufacturer`,`oc_manufacturer_to_store` where oc_manufacturer.manufacturer_id=oc_manufacturer_to_store.manufacturer_id and oc_manufacturer_to_store.store_id=0 and (image is NULL or image ='') limit 1";
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			$_POST['manufacturer_id']=$data['manufacturer_id'];
			$_POST['new']=1;
			$_POST['image']=$data['image'];
			
			$new=1;
}else{
			$sql = 'SELECT * FROM `oc_manufacturer`,`oc_manufacturer_to_store` where oc_manufacturer.manufacturer_id=oc_manufacturer_to_store.manufacturer_id and oc_manufacturer_to_store.manufacturer_id="'.$_POST['manufacturer_id'].'"';
	//echo $sql;
			$req = mysqli_query($db,$sql);
			$data = mysqli_fetch_assoc($req);

			$_POST['name_product']=$data['name'];
			$_POST['manufacturer_id']=$data['manufacturer_id'];
			$_POST['new']=1;
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
		$imagepath='/home/phoenkv5/public_html/phoenixliquidation/image/catalog/'.$_POST['manufacturer_id'].".".$path[$ext];
		//echo "<br>".$imagepath;
		//$imagepath=str_replace(array("\r","\n"),"",$imagepath);
		//$image = file_get_contents('https://images-na.ssl-images-amazon.com/images/I/31vgJTuXe1L.jpg');
		//echo urlencode($imagepath);
		file_put_contents($imagepath, $image); //Where to save the image on your server
		//file_put_contents('/home/phoenkv5/public_html/image/categorytmp/zzzz2.jpg', $image); //Where to save the image on your server
		$sql2="UPDATE `oc_manufacturer` SET image ='catalog/".$_POST['manufacturer_id'].".".$path[$ext]."' where manufacturer_id=".$_POST['manufacturer_id'];
		$req2=mysqli_query($db,$sql2);
		//echo $sql2;
		$_POST['image']="catalog/".$_POST['manufacturer_id'].".".$path[$ext];
	 }

if($_POST['upload']=="upload"){
		
	$GLOBALS['SITE_ROOT']=$_SERVER['DOCUMENT_ROOT'];
	if (!isset($_FILES['imageprincipale'])) {
		//echo "non";
		}else{
		//	echo "oui";
		$file=$_FILES['imageprincipale']['tmp_name'];
		//$image= addslashes(file_get_contents($_FILES['imageprincipale']['tmp_name']));
		$image_name= addslashes($_FILES['imageprincipale']['name']);
			//print_r($_FILES);
			$uploads_dir = 'image/catalog';
			$sqldir = 'catalog';
			//$uploads_dir = 'upload';
				if ($_FILES['imageprincipale']['error'] == 0) {
					//echo "oui";
					if (is_uploaded_file($_FILES['imageprincipale']['tmp_name']))
					{
						
						$tmp_name = $_FILES['imageprincipale']['tmp_name'];
						// basename() may prevent filesystem traversal attacks;
						// further validation/sanitation of the filename may be appropriate
						$name = basename($_FILES['imageprincipale']['name']);
						$path=explode(".",basename($_FILES['imageprincipale']['name']));
						$dir_name=$GLOBALS['SITE_ROOT']."/".$uploads_dir."/MAN".$_POST['manufacturer_id'].".".$path[1];
						move_uploaded_file($tmp_name, $dir_name);
						 
						$sql2="UPDATE `oc_manufacturer` SET image ='".$sqldir."/MAN".$_POST['manufacturer_id'].".".$path[1]."' where manufacturer_id=".$_POST['manufacturer_id'];
						$req2=mysqli_query($db,$sql2);
						echo $sql2;
						$_POST['image']=$sqldir."/MAN".$_POST['manufacturer_id'].".".$path[1];
					}
				}
		}		

}


?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title></title>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffbaba">
<h1>Ajout ou Modification Photos brand </h1>

<form id="form_67341" class="appnitro" action="uploadphotobrand.php?manufacturer_id=<? echo $_POST['manufacturer_id'];?>" method="post">
<div class="form_description">


</div>
<h3>Manufacturier <?if($new==1){?><a href="uploadphotobrand.php" class="button--style-red">Changer Manufacturier</a><?}?> <a href="uploadphotobrand.php?exclure=oui&manufacturer_id=<? echo $_POST['manufacturer_id'];?>" class="button--style-red">Exclure</a> <a href="listingusa.php?manufacturer_id=<?echo $_POST['manufacturer_id']?>" class="button--style-red">Menu Listing</a></h3>

<input id="manufacturer_id" type="text" name="manufacturer_id" value="<?echo $_POST['manufacturer_id'];?>" maxlength="255" <?if($new==1)echo "disabled";?> autofocus>
<input type="hidden" name="manufacturer_idcheck" value="<?echo $_POST['manufacturer_id'];?>" />
<input type="hidden" name="manufacturer_id" value="<?echo $_POST['manufacturer_id'];?>" />

<br>


</form>
<h3><label class="description" for="element_1">Titre :</label>
		<input id="name_product"  type="text" name="name_product" value="<?echo strtoupper ($_POST['name_product']);?>" maxlength="80" /></h3>
<br>
<form action="uploadphotobrand.php?insert=oui&manufacturer_id=<? echo $_POST['manufacturer_id'];?>" method="post" enctype="multipart/form-data" name="addroom">
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
 <input type="hidden" name="manufacturer_id" value="<?echo $_POST['manufacturer_id'];?>" />
 <input type="hidden" name="manufacturer_id" value="<?echo $_POST['manufacturer_id'];?>" />
 <input type="hidden" name="upload" value="upload" />

 	<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
 </form>
<br />

<br />
<br />
 <h1><a href="interneusa.php" class="button--style-red">Retour au MENU</a></h1>
 <script>
window.open("https://www.google.com/search?q=<?echo $_POST['name_product'];?> logo&sxsrf=ACYBGNSp0FVgigdgTlIKcFis22sXDCHMfg:1581608614303&source=lnms&tbm=isch&sa=X&ved=2ahUKEwj-3IOd787nAhWwg-AKHZ3-BGwQ_AUoAXoECAwQAw&biw=1310&bih=629","google");
</script>
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>