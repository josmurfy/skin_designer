<?
//print_r($_POST['accesoires']);
$sku=(string)$_POST['sku'] ;
// on se connecte � MySQL 
include 'connection.php';header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");header("Cache-Control: post-check=0, pre-check=0", false);header("Pragma: no-cache");
// on s&eacute;lectionne la base 
//mysqli_select_db('n7f9655_storeliquidation',$db); 
// on cr&eacute;e la requ�te SQL verifier les ordres 
// savoir ledernier id 
//echo $_GET['sku'];
//echo 'check '.$_POST['skucheck'];
//echo $new;

if (empty ($_GET['parent_id'])){
	//echo "allo";
			$_GET['parent_id']=0;
			$_GET['parent_id_avant']=0;
}

if (isset($_GET['category_id'])){
	
	
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and language_id=1 and oc_category.category_id='.$_GET['category_id'];
	//echo $sql;
			$req = mysqli_query($db,$sql); 
			//echo $_GET['parent_id_avant'];
			//echo $categoryname;
			//echo $data['parent_id'];
			
		//if (isset($_POST['manufacturer_id'])==false){$_POST['manufacturer_id']=0;}

}		

if($_POST['catnameen']!="" && $_POST['catnamefr']!="" && $_POST['numcat']!=""){
	//echo "allo";
	if($_POST['ebayyes']<>1)$_POST['ebayyes']=0;

	$sql2 = "UPDATE `oc_category_description` SET `name`='".strtoupper(addslashes($_POST['catnameen']))."' WHERE language_id=1 and `category_id`='".$_POST['numcat']."'";
	echo $sql2."<br>";
	$req2 = mysqli_query($db,$sql2);
	$sql2 = "UPDATE `oc_category_description` SET `name`='".strtoupper(addslashes($_POST['catnamefr']))."' WHERE language_id=2 and `category_id`='".$_POST['numcat']."'";
	echo $sql2."<br>";
	$req2 = mysqli_query($db,$sql2);
}
	

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head> <?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
    <title></title><script src="https://cdn.tiny.cloud/1/7p6on3i68pu5r6qiracdsz4vybt7kh5oljvrcez8fmwhaya5/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script><script>    tinymce.init({      selector: '#mytextarea'    });  </script>
    <script src="//cdn.jsdelivr.net/jsbarcode/3.3.20/JsBarcode.all.min.js">
</script>
<link href="stylesheet.css" rel="stylesheet">
</head>
<body bgcolor="ffffff">
<h1>Modification Categorie</h1>
<form id="form_67341" class="appnitro" action="modifcategorie.php?category_id=<?echo $_GET['category_id'];?>&parent_id_avant=<?echo $_GET['parent_id_avant'];?>" method="post">

<a href="ajoutcategorie.php?parent_id=<?echo $_GET['parent_id_avant'];?>" >Retour en arriere</a>



<h3><label class="description" for="categorie">Categorie :</label></h3>
<?
$data = mysqli_fetch_assoc($req);
			$sql2 = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and language_id=2 and oc_category.category_id='.$data['category_id'];
	//echo $sql;
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);

?><br>
	Ajouter une Categorie : <b><span style="color: #ff0000;"><?echo $categoryname;?></b>	<br>
Nom Anglais <input id="catnameen"  type="text" name="catnameen" value="<?echo $data['name'];?>" maxlength="160" /> <br>
Nom Francais <input id="catnamefr"  type="text" name="catnamefr" value="<?echo $data2['name'];?>" maxlength="160" /> <br>
Numero Ebay <input id="numcat"  type="text" name="numcat" value="<?echo $data['category_id'];?>" maxlength="160" /> <br>



		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="etat" value="<?echo $_POST['etat'];?>" />
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="quantityinventaire" value="<?echo $_POST['quantityinventaire'];?>" />
		<input type="hidden" name="skuanc" value="<?echo (string)$_POST['sku'] ;?>" />
		
		<?if($new==1){?>
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		

	
</body>
</html>
<? // on ferme la connexion � mysql 
mysqli_close($db); ?>