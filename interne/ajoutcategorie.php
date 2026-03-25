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
//print("<pre>".print_r ($_POST,true )."</pre>"); 
if (empty ($_GET['parent_id']) && ($_POST['parent_id'] =="")){
	echo "allo<br>";
			$_GET['parent_id']=0;
			$_GET['parent_id_avant']=0;
}elseif($_POST['parent_id'] !=""){
		$_GET['parent_id']=$_POST['parent_id'];
		$_GET['parent_id_avant']=0;
}
if(isset ($_GET['product_id'])){
$_POST['primary_cat']=$_GET['primary_cat'];

}
if(isset ($_GET['product_id'])){
	$_POST['product_id']=$_GET['product_id'];
	$_POST['numcat']=$_GET['numcat'];
}

if (isset($_GET['parent_id'])){
	
	
			$sql = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and language_id=1 and oc_category.parent_id='.$_GET['parent_id'].' order by name'; 
	echo $sql;
			$req = mysqli_query($db,$sql); 
			//echo $_GET['parent_id_avant'];
			$categoryname=$data['name'];
			if($_GET['parent_id_avant']==""){
				//echo "allo";
				$_GET['parent_id_avant']=$data['parent_id'];
			}
			echo "<br>Categorie Name".$categoryname."<br>";
			//echo $data['parent_id'];
			
		//if (isset($_POST['manufacturer_id'])==false){$_POST['manufacturer_id']=0;}

}		

if($_POST['catnameen']!="" && $_POST['catnamefr']!="" && $_POST['numcat']!="" && $_POST['parent_id']!="" ){
	//echo "allo";
	if($_POST['ebayyes']<>1)$_POST['ebayyes']=0;
	$sql = "INSERT INTO `oc_category` (`category_id_index`, `category_id`, `image`, `parent_id`, `top`, `column`, `sort_order`, `status`, `date_added`, `date_modified`,   `verif`) VALUES (NULL, '".$_POST['numcat']."', NULL, '".$_POST['parent_id']."', NULL, NULL, '0', '0', NULL, NULL,   '1')";
	echo $sql."<br>";
	$req = mysqli_query($db,$sql);
	$sql = "INSERT INTO `oc_category_description` (`category_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `old`, `ebayyes`) VALUES ('".$_POST['numcat']."', '1', '".strtoupper(addslashes($_POST['catnameen']))."', '', '', '', '', NULL, '".$_POST['ebayyes']."')";
	echo $sql."<br>";
	$req = mysqli_query($db,$sql);
	$sql = "INSERT INTO `oc_category_description` (`category_id`, `language_id`, `name`, `description`, `meta_title`, `meta_description`, `meta_keyword`, `old`, `ebayyes`) VALUES ('".$_POST['numcat']."', '2', '".strtoupper(addslashes($_POST['catnamefr']))."', '', '', '', '', NULL, '".$_POST['ebayyes']."')";
	echo $sql."<br>";
	$req = mysqli_query($db,$sql);
	$sql = "INSERT INTO `oc_category_to_store` (`category_id`, `store_id`) VALUES ('".$_POST['numcat']."', '0')";
	echo $sql."<br>";
	$req = mysqli_query($db,$sql);
	if($_POST['conditions_id']!=""){
		$sql = "INSERT INTO `oc_conditions_to_category` (`category_id`, `conditions_id`) VALUES ('".$_POST['numcat']."', '".$_POST['conditions_id']."')";
		echo $sql."<br>";
		$req = mysqli_query($db,$sql);
	}
	if($_POST['primary_cat']!="")
		$_POST['numcat']=$_POST['primary_cat'];
	if($_POST['product_id']){
		header("location: modificationitem.php?product_id=".$_POST['product_id']."&category_id=".$_POST['numcat']);  
		exit();
	}else{
		 echo "<script>window.close();</script>";
	}
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
<h1>Ajout Categorie</h1>
<form id="form_67341" class="appnitro" action="ajoutcategorie.php?parent_id=<?echo $_GET['parent_id'];?>&parent_id_avant=<?echo $_GET['parent_id_avant'];?>" method="post">

<a href="interneusa.php" >Retour au MENU</a> <a href="ajoutcategorie.php?parent_id=<?echo $_GET['parent_id_avant'];?>" >Retour en arriere</a> 



<h3><label class="description" for="categorie">Categorie :</label></h3>
<?
while($data = mysqli_fetch_assoc($req)){
			$sql2 = 'SELECT * FROM `oc_category`,`oc_category_description` where oc_category.category_id=oc_category_description.category_id and language_id=2 and oc_category.category_id='.$data['category_id'];
	//echo $sql;
			$req2 = mysqli_query($db,$sql2);
			$data2 = mysqli_fetch_assoc($req2);
	echo '<a href="ajoutcategorie.php?parent_id='.$data['category_id'].'&parent_id_avant='.$_GET['parent_id'].'" >'.$data['name'].'</a> ('.$data2['name'].') ('.$data['category_id'].')';
	echo ' <a href="modifcategorie.php?category_id='.$data['category_id'].'&parent_id_avant='.$_GET['parent_id'].'" >Modifier</a>';
	echo ' <a href="uploadphotocategory.php?category_id='.$data['category_id'].'&parent_id_avant='.$_GET['parent_id'].'" >Ajouter Photo</a><br>';

}
?><br>
	Ajouter une Categorie : <b><span style="color: #ff0000;"><?echo $categoryname;?></span></b>	<br>
Nom Anglais <input id="catnameen"  type="text" name="catnameen" value="<?echo $_POST['catnameen'];?>" maxlength="160" /> <br>
Nom Francais <input id="catnamefr"  type="text" name="catnamefr" value="<?echo $_POST['catnamefr'];?>" maxlength="160" /> <br>
Numero Ebay <input type="checkbox" name="ebayyes" value="1" checked />Categorie sur EBAY<input id="numcat"  type="text" name="numcat" value="<?echo $_POST['numcat'];?>" maxlength="160" /> <br>
Parent Id<input id="parent_id"  type="text" name="parent_id" value="<?echo $_POST['parent_id'];?>" maxlength="160" /> <br>
<select name="conditions_id">
			<option value="" selected></option>
			<?
$sql = 'SELECT * FROM `oc_conditions` order by name';

			// on envoie la requ�te
			$req = mysqli_query($db,$sql);
			// on fait une boucle qui va faire un tour pour chaque enregistrement
			
			while($data = mysqli_fetch_assoc($req))
				{
					$selected="";

						

							
					?>
								<option value="<?echo $data['conditions_id'];?>" <?echo $selected;?>><?echo $data['name'];?></option>
					<?}?>
							</select><br>
							<input type="hidden" name="conditions_id_old" value="<?echo $_POST['conditions_id'];?>" />
					<?	
					//echo $brandrecom;
					
?>


		<input type="hidden" name="new" value="<?echo $new;?>" />
		<input type="hidden" name="product_id" value="<?echo $_POST['product_id'];?>" />
		<input type="hidden" name="etat" value="<?echo $_POST['etat'];?>" />
		<input type="hidden" name="category_id" value="<?echo $_POST['category_id'];?>" />
		<input type="hidden" name="quantityinventaire" value="<?echo $_POST['quantityinventaire'];?>" />
		<input type="hidden" name="skuanc" value="<?echo (string)$_POST['sku'] ;?>" />
		<input type="hidden" name="primary_cat" value="<?echo $_POST['primary_cat'];?>" />
		
		<?if($new==1){?>
		Pret pour lister :<input type="checkbox" name="status" value="1" <?if($_POST['status']=='1')echo 'checked';?>/>
		<?}?>
		<input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
		

	
</body>
</html>
<? // on ferme la connexion � mysql 

/**/mysqli_close($db);

?>